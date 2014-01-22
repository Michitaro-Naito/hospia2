<?php $this->start('script'); ?>
<script>
(function(){

var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function Hospital(root, data){
	var s = this;
	s.Hospital = data.Hospital;
	s.Area = data.Area;
	s.Dpc = data.Dpc;
	s.valueForSelection = ko.computed(function(){
		var value = 0;
		if(model.currentComparisonCategory().id == 'basic')
			value = s.Hospital[model.selectedDisplayTypeForBasic().id];
		if(model.currentComparisonCategory().id == 'dpc')
			value = s.Dpc[model.selectedDisplayTypeForDpc().id];
		return Number(value);
	}, this);
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
	
	s.search = function(){
		var sendData = {
			wamId: s.wamId,
			ctgry: s.selectedComparisonCategory().id,
			mdcId: s.selectedMdc().id,
			clst: s.selectedDisplayTypeForHoscmp().id
		};
		console.info(sendData);
		$.postJSON({
			url: dat.searchUrl,
			data: sendData
		}).done(function(data){
			console.info(data);
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



<?php echo $this->element('additional_information'); ?>

<div id="hoscmp">
	<?php echo $this->element('hosdetail_menu'); ?>
	
	<!-- Menu -->
	<div class="box">
		<h2 data-bind="">他病院比較</h2>
		<div class="content">
			<select data-bind="options: comparisonCategories, optionsText: 'name', value: selectedComparisonCategory"></select>
			<select data-bind="visible: selectedComparisonCategory().id=='dpc', options: mdcs, optionsText: 'name', value: selectedMdc"></select>
			<select data-bind="options: displayTypesForHoscmp, optionsText: 'name', value: selectedDisplayTypeForHoscmp"></select>
			<button data-bind="click: search">検索</button>
		</div>
	</div>
	
	<!-- Head -->
	<div class="row">
		<div class="col-sm-6">
			<table class="hoscmp-head">
				<tr>
					<th class="name">病院名</th>
					<th class="address">所在地</th>
					<th data-bind="text: NameForSelection" class="value"></th>
				</tr>
			</table>
		</div>
		<div class="col-sm-6">
			<table class="hoscmp-head">
				<tr>
					<th class="display">
						グラフ表示
						<select data-bind="visible: currentComparisonCategory().id == 'basic', options: displayTypesForBasic, optionsText: 'name', value: selectedDisplayTypeForBasic"></select>
						<select data-bind="visible: currentComparisonCategory().id == 'dpc', options: displayTypesForDpc, optionsText: 'name', value: selectedDisplayTypeForDpc"></select>
					</th>
					<th class="distance">距離(km)</th>
				</tr>
			</table>
		</div>
	</div>
	
	<!-- Data -->
	<ul data-bind="foreach: hospitals" class="items">
		<li class="row">
			<div class="col-sm-6 left">
				<table>
					<tr>
						<td class="name">
							<a data-bind="text: Hospital.name, attr: { href: DetailUrl }"></a>
						</td>
						<td data-bind="text: Area.addr1 + Area.addr2" class="address"></td>
						<td data-bind="text: valueForSelection().toFixed(1)" class="value"></td>
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
						<td data-bind="text: Number(Hospital.distance).toFixed(1)" class="distance"></td>
					</tr>
				</table>
			</div>
		</li>
	</ul>
</div>

<?php echo $this->element('favorite', array('wamId'=>$dat['wamId'])); ?>

<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url(null, true)));
?>
