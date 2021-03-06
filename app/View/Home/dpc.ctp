<?php $this->assign('title', 'DPC全国統計'); ?>
<?php $this->start('script'); ?>
<script>
// Get initial values from server
var mdcs = JSON.parse('<?php echo json_encode($mdcs); ?>');
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var years = JSON.parse('<?php echo json_encode($years); ?>');
var getDpcsUrl = '<?php echo Router::url('/ajax/getDpcs.json'); ?>';
var getWoundsUrl = '<?php echo Router::url('/ajax/getWounds.json'); ?>';
var detailUrl = '<?php echo Router::url('/hosdetail'); ?>';
var premiumContentUrl = '<?php echo Router::url('/users/premium_content'); ?>';

// Knockout

function Dpc(data){
	var s = this;
	s.id = data.MdcDpc.dpc_cd;
	s.name = s.id + ' ' + data.MdcDpc.dpc;
}

function Wound(root, data){
	var s = this;
	s.root = root;
	s.Wound = data.Wound;
	s.Details_Count = data.Details_Count;
	s.Details_Days = data.Details_Days;
	s.isTotal = data.isTotal;
	s.Rate = ko.computed(function(){
		if(typeof root.Total == 'undefined')
			return 0;
		var total = root.Total();
		return s.Wound.count / total.Wound.count;
	});
	s.RateDays = ko.computed(function(){
		if(typeof root.Total == 'undefined')
			return 0;
		var total = root.Total();
		return s.Wound.days / root.MaxDays();
	});
	s.fmRate = ko.computed(function(){
		return addFigure(Number(100*s.Rate()).toFixed(1)) + '%';
	});
	s.GetStyle = ko.computed(function(){
		return 'width: ' + 100*s.Rate() + '%';
	});
	s.GetStyleDays = ko.computed(function(){
		return 'width: ' + 100*s.RateDays() + '%';
	});
}

function AppModel(){
	var s = this;
	
	s.mdcs = mdcs;
	s.dpcs = ko.observableArray();
	s.prefectures = ko.observableArray(prefectures);
	s.years = ko.observableArray(years);
	s.wounds = ko.observableArray();									// 検索結果
	s.selectedMdc = ko.observable();
	s.selectedDpc = ko.observable();
	s.currentDpc = ko.observable();
	s.selectedPrefecture = ko.observable();
	s.selectedYear = ko.observable();
	s.currentYear = ko.observable();
	s.firstLoad = ko.observable(false);
	s.detailUrl = detailUrl;
	
	// MDC選択時にDPC一覧を読み込む
	s.selectedMdc.subscribe(function(newValue){
		$.ajax({
			cache: false,
			type: 'POST',
			dataType: 'JSON',
			url: getDpcsUrl,
			data: {
				mdcId: newValue.id
			}
		}).done(function(data){
			s.dpcs([]);
			for(var n=0; n<data.dpcs.length; n++){
				s.dpcs.push(new Dpc(data.dpcs[n]));
			}
			if(!s.firstLoad()){
				s.firstLoad(true);
				s.search();
			}
		});
	});
	
	s.selectedPrefecture.subscribe(function(newValue){
		if(s.firstLoad())
			s.search();
	});
	
	// 合計を計算する
	s.Total = ko.computed(function(){
		var total = {
			Wound: {
				operation: '計',
				count: 0,
				days: 0
			}
		};
		for(var n=0; n<s.wounds().length; n++){
			var w = s.wounds()[n];
			total.Wound.count += parseInt(w.Wound.count);
			//total.Wound.days += parseFloat(w.Wound.days);
			total.Wound.days += parseFloat(w.Wound.count) * parseFloat(w.Wound.days);
		}
		total.Wound.days /= total.Wound.count;
		total.isTotal = true;
		return new Wound(s, total);
	});
	s.MaxDays = ko.computed(function(){
		var max = 0.0;
		for(var n=0; n<s.wounds().length; n++){
			var w = s.wounds()[n];
			var days = new Number(w.Wound.days);
			if(days > max)
				max = days;
		}
		return max;
	});
	
	// 検索
	s.search = function(){
		<?php if(empty($isPremiumUser)): ?>
		// Redirects non premium user
		if(s.selectedYear().id != s.years()[0].id)
			location.href = premiumContentUrl;
		<?php endif; ?>
		
		s.currentYear(s.selectedYear());
		s.wounds([]);
		
		$.postJSON({
			url: getWoundsUrl,
			data: {
				mdcId: s.selectedMdc().id,
				dpcId: s.selectedDpc().id,
				prefectureId: s.selectedPrefecture().id,
				year: s.selectedYear().id
			}
		}).done(function(data){
			s.currentDpc(s.selectedDpc());
			s.wounds([]);
			for(var n=0; n<data.wounds.length; n++){
				s.wounds.push(new Wound(s, data.wounds[n]));
			}
		});
	}
	
}

var model = new AppModel();
ko.applyBindings(model);

</script>
<?php $this->end(); ?>



<!-- Menu -->
<div class="box">
	<h2>傷病別全国統計</h2>
	<div class="content">
		<ul class="elements">
			<li>診断分類<?php echo $this->My->tip('DPC-診断分類'); ?>：<select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc"></select>　</li>
			<li>傷病名<?php echo $this->My->tip('DPC-傷病名'); ?>：<select data-bind="options: dpcs, optionsText: 'name', value: selectedDpc" style="max-width: 200px;"></select>　</li>
			<li><span class="premium">表示年度：</span><select data-bind="options: years, optionsText: 'name', value: selectedYear"></select></li>
			<li><button data-bind="click: search">変更する</button></li>
		</ul>
	</div>
</div>

<!-- Data -->
<h2 data-bind="if: currentDpc()" class="dpc">
	<span data-bind="text: currentDpc().name"></span>
	&nbsp;&nbsp;&nbsp;&nbsp;<span data-bind="if: currentYear() != null">表示年度：<span data-bind="text: currentYear().name"></span></span>
</h2>
<table class="dpc" border="1" bordercolor="#CCC">
	<thead>
		<tr>
			<th class="text-center">手術情報<?php echo $this->My->tip('DPC-手術情報'); ?></th>
			<th class="text-center" colspan="3">患者数および割合<?php echo $this->My->tip('DPC-患者数および割合'); ?></th>
			<th class="text-center" colspan="2">平均在院日数<?php echo $this->My->tip('DPC-平均在院日数'); ?></th>
		</tr>
	</thead>
	<tbody data-bind="foreach: wounds().concat(Total())">
		<tr>
			<td data-bind="text: Wound.operation" class="operation"></td>
			<td data-bind="text: addFigure(Wound.count)" class="count ar"></td>
			<td data-bind="text: fmRate" class="rate ar"></td>
			<td class="bar">
				<div data-bind="visible: !isTotal" class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;" data-bind="attr: {style:GetStyle}">
				    <span class="sr-only">60% Complete</span>
				  </div>
				</div>
			</td>
			<td data-bind="text: addFigure(Number(Wound.days).toFixed(1))" class="days ar"></td>
			<td class="bar">
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;" data-bind="attr: {style:GetStyleDays}">
				    <span class="sr-only">60% Complete</span>
				  </div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<!-- Menu 2 -->
<div class="box">
	<h2>手術情報別病院ランキング<?php echo $this->My->tip('DPC-手術情報別病院ランキング', array('image'=>true)); ?></h2>
	<div class="content">
		都道府県<?php echo $this->My->tip('DPC-都道府県'); ?>：<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
	</div>
</div>

<!-- Data 2 -->
<div data-bind="foreach: wounds">
	<h2 data-bind="text: Wound.operation" class="dpc"></h2>
	<div class="row">
		<div class="col-sm-6">
			<h3 class="dpc">患者数が多い病院ランキング</h3>
			<table class="dpc-half" border="1" bordercolor="#CCC">
				<thead>
					<tr><th>病院名</th><th>患者数</th><th>日数</th></tr>
				</thead>
				<tbody data-bind="foreach: Details_Count">
					<tr>
						<td>
							<a data-bind="text: Hospital.name, attr: { href: detailUrl + '/' + Hospital.wam_id }"></a>
						</td>
						<td data-bind="text: addFigure(Detail.count)" class="ar"></td>
						<td data-bind="text: addFigure(Number(Detail.days).toFixed(1))" class="ar"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-6">
			<h3 class="dpc">在院日数が短い病院ランキング</h3>
			<table class="dpc-half" border="1" bordercolor="#CCC">
				<tr><th>病院名</th><th>患者数</th><th>日数</th></tr>
				<tbody data-bind="foreach: Details_Days">
					<tr>
						<td>
							<a data-bind="text: Hospital.name, attr: { href: detailUrl + '/' + Hospital.wam_id }"></a>
						</td>
						<td data-bind="text: addFigure(Detail.count)" class="ar"></td>
						<td data-bind="text: addFigure(Number(Detail.days).toFixed(1))" class="ar"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="clear: both;"> </div>
	</div>
</div>
