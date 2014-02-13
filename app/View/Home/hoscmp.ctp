<?php
	$title = '診療実績';
	if(!empty($dat['hospital']['Hospital']['name']))
		$title = $dat['hospital']['Hospital']['name'];
	$this->assign('title', $title);
?>
<?php $this->start('script'); ?>
<script>
(function(){

var dat = JSON.parse('<?php echo json_encode($dat); ?>');

function Hospital(root, data){
	var s = this;
	s.root = root;
	s.Hospital = data.Hospital;
	s.Area = data.Area;
	s.Dpc = data.Dpc;
	s.valueForSelection = ko.computed(function(){
		var value = 0;
		if(s.root.currentComparisonCategory().id == 'basic')
			value = s.Hospital[s.root.selectedDisplayTypeForBasic().id];
		if(s.root.currentComparisonCategory().id == 'dpc')
			value = s.Dpc[s.root.selectedDisplayTypeForDpc().id];
		return Number(value);
	}, this);
	s.fmValueForSelection = ko.computed(function(){
		var str = addFigure(s.valueForSelection().toFixed(1));
		if(s.root.currentComparisonCategory().id == 'dpc' 
			&& s.root.selectedDisplayTypeForDpc().id == 'zone_share')
			str += '%';
		return str;
	});
	s.fmDistance = ko.computed(function(){
		if(!s.Hospital.distance)
			return '0.0';
		return addFigure(Number(s.Hospital.distance).toFixed(1));
	});
	s.GetStyle = ko.computed(function(){
		if(!root.barInitialized())
			return 'width: 0%';
		return 'width: ' + 100 * s.valueForSelection() / root.MaxValueForSelection() + '%';
	});
	s.DetailUrl = ko.computed(function(){
		return dat.detailUrl + '/' + s.Hospital.wam_id;
	});
}

function AppModel(){
	var s = this;
	
	s.wamId = dat.wamId;																	// 医療機関ID
	s.comparisonCategories = dat.comparisonCategories;		// 比較カテゴリ（DPC or 基本情報)
	s.mdcs = dat.mdcs;																		// MDC一覧
	s.displayTypesForHoscmp = dat.displayTypesForHoscmp;	// 比較リスト生成方法（距離が近い病院 or 患者数が多い病院）
	s.displayTypesForBasic = dat.displayTypesForBasic;		// 取得した病院一覧の表示方法一覧（基本情報で検索時）
	s.displayTypesForDpc = dat.displayTypesForDpc;				// 取得した病院一覧の表示方法一覧（MDCで検索時）
	
	s.selectedComparisonCategory = ko.observable();
	s.currentComparisonCategory = ko.observable(s.comparisonCategories[0]);				// 現在の表示方法：比較カテゴリ
	s.selectedMdc = ko.observable();
	s.selectedDisplayTypeForHoscmp = ko.observable();
	s.selectedDisplayTypeForBasic = ko.observable();
	s.selectedDisplayTypeForDpc = ko.observable();
	s.barInitialized = ko.observable(false);
	
	s.hospitals = ko.observableArray();										// 検索取得された病院一覧
	
	s.NameForSelection = ko.computed(function(){
		var id = s.currentComparisonCategory().id;
		if(id == 'basic')
			var sel = s.selectedDisplayTypeForBasic();
		else
			var sel = s.selectedDisplayTypeForDpc();
		if(typeof sel == 'undefined')
			return '';
		return sel.name;
	});
	
	s.MaxValueForSelection = ko.computed(function(){
		var max = 0;
		$.each(s.hospitals(), function(index, h){
			var value = h.valueForSelection();
			if(value > max)
				max = value;
		});
		return max;
	});
	
	s.Average = ko.computed(function(){
		if(s.hospitals().length == 0)
			return [];
		var data = {
			Dpc: {
				
			},
			Hospital:{
				wam_id: 'Average'
			},
			Area:{
				
			}
		};
		$.each(s.displayTypesForDpc, function(n, t){
			data.Dpc[t.id] = 0.0;
		});
		$.each(s.hospitals(), function(n, h){
			$.each(s.displayTypesForDpc, function(m, t){
				data.Dpc[t.id] += Number(h.Dpc[t.id]);
			});
		});
		$.each(s.displayTypesForDpc, function(n, t){
			data.Dpc[t.id] /= s.hospitals().length;
		});
		return new Hospital(s, data);
	});
	
	s.search = function(){
		var sendData = {
			wamId: s.wamId,
			ctgry: s.selectedComparisonCategory().id,
			mdcId: s.selectedMdc().id,
			clst: s.selectedDisplayTypeForHoscmp().id
		};
		$.postJSON({
			url: dat.searchUrl,
			data: sendData
		}).done(function(data){
			s.currentComparisonCategory(s.selectedComparisonCategory());
			//s.hospitals(data.hospitals);
			s.hospitals([]);
			for(var n=0; n<data.hospitals.length; n++){
				s.hospitals.push(new Hospital(s, data.hospitals[n]));
			}
			s.barInitialized(false);
			setTimeout(function(){
				s.barInitialized(true);
			}, 1000);
		});
	}
}

var model = new AppModel();
ko.applyBindings(model, document.getElementById('hoscmp'));
model.search();

})();
</script>
<?php $this->end(); ?>



<?php echo $this->element('hosdetail_menu'); ?>
<?php echo $this->element('additional_information'); ?>
<?php echo $this->element('favorite', array('wamId'=>$dat['wamId'], 'compact'=>true)); ?>

<div id="hoscmp">
	<!-- Menu -->
	<div class="box">
		<h2 data-bind="">他病院比較</h2>
		<div class="content">
			<ul class="elements clearfix">
				<li>
					比較区分<?php echo $this->My->tip('他病院比較-比較区分'); ?>：
					<select data-bind="options: comparisonCategories, optionsText: 'name', value: selectedComparisonCategory"></select>
				</li>
				<li>
					診断分類<?php echo $this->My->tip('他病院比較-診断分類'); ?>：
					<select data-bind="visible: selectedComparisonCategory().id=='dpc', options: mdcs, optionsText: 'name', value: selectedMdc"></select>
				</li>
				<li>
					比較リスト<?php echo $this->My->tip('他病院比較-比較リスト'); ?>：
					<select data-bind="options: displayTypesForHoscmp, optionsText: 'name', value: selectedDisplayTypeForHoscmp"></select>
				</li>
				<li><button data-bind="click: search">検索</button></li>
			</ul>
		</div>
	</div>
	
	<!-- Head -->
	<div class="row thead">
		<div class="col-sm-6">
			<table class="hoscmp-head">
				<tr>
					<th class="name">病院名<?php echo $this->My->tip('病院名'); ?></th>
					<th class="address">所在地</th>
					<th data-bind="text: NameForSelection" class="value"></th>
				</tr>
			</table>
		</div>
		<div class="col-sm-6">
			<table class="hoscmp-head">
				<tr>
					<th class="display">
						グラフ表示<?php echo $this->My->tip('グラフ表示'); ?>
						<select data-bind="visible: currentComparisonCategory().id == 'basic', options: displayTypesForBasic, optionsText: 'name', value: selectedDisplayTypeForBasic"></select>
						<select data-bind="visible: currentComparisonCategory().id == 'dpc', options: displayTypesForDpc, optionsText: 'name', value: selectedDisplayTypeForDpc"></select>
					</th>
					<th class="distance">距離(km)<?php echo $this->My->tip('距離'); ?></th>
				</tr>
			</table>
		</div>
	</div>
	
	<!-- Data -->
	<ul data-bind="foreach: hospitals().concat(Average())" class="items">
		<li class="row">
			<div class="col-sm-6 left">
				<table>
					<tr>
						<td data-bind="visible: Hospital.wam_id == 'Average'">上記リスト平均</td>
						<td class="name">
							<a data-bind="visible: Hospital.wam_id != $root.wamId, text: Hospital.alias, attr: { href: DetailUrl }"></a>
							<span data-bind="visible: Hospital.wam_id == $root.wamId, text: Hospital.alias" class="muted"></span>
						</td>
						<td class="address">
							<span data-bind="visible: Hospital.wam_id != 'Average', text: Area.addr1 + Area.addr2"></span>
						</td>
						<td data-bind="text: fmValueForSelection()" class="value ar"></td>
					</tr>
				</table>
			</div>
			
			<div class="col-sm-6 right">
				<table>
					<tr>
						<td>
							<div class="progress">
							  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;" data-bind="attr: {style:GetStyle}">
							    <span class="sr-only">60% Complete</span>
							  </div>
							</div>
						</td>
						<td class="distance ar">
							<span data-bind="visible: Hospital.wam_id != 'Average', text: fmDistance"></span>
						</td>
					</tr>
				</table>
			</div>
		</li>
	</ul>
</div>

<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url('/hosdetail/' . $dat['hospital']['Hospital']['wam_id'], true)));
?>
