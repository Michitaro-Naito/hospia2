<?php $this->start('script'); ?>
<script>
// Get initial values from server
var mdcs = JSON.parse('<?php echo json_encode($mdcs); ?>');
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var getDpcsUrl = '<?php echo Router::url('/ajax/getDpcs.json'); ?>';
var getWoundsUrl = '<?php echo Router::url('/ajax/getWounds.json'); ?>';
var detailUrl = '<?php echo Router::url('/hosdetail'); ?>';

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
		return Number(100*s.Rate()).toFixed(1) + '%';
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
	s.wounds = ko.observableArray();									// 検索結果
	s.selectedMdc = ko.observable();
	s.selectedDpc = ko.observable();
	s.currentDpc = ko.observable();
	s.selectedPrefecture = ko.observable();
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
			console.info(data);
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
			total.Wound.days += parseFloat(w.Wound.days);
		}
		return new Wound(s, total);
	});
	s.MaxDays = ko.computed(function(){
		var max = 0;
		for(var n=0; n<s.wounds().length; n++){
			var w = s.wounds()[n];
			if(w.Wound.days > max)
				max = w.Wound.days;
		}
		return max;
	});
	
	// 検索
	s.search = function(){
		$.postJSON({
			url: getWoundsUrl,
			data: {
				mdcId: s.selectedMdc().id,
				dpcId: s.selectedDpc().id,
				prefectureId: s.selectedPrefecture().id
			}
		}).done(function(data){
			//console.info(data);
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
	<h2>傷病別統計データ</h2>
	<div class="content">
		診断分類<?php echo $this->My->tip('DPC-診断分類'); ?>：<select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc"></select>
		傷病名<?php echo $this->My->tip('DPC-傷病名'); ?>：<select data-bind="options: dpcs, optionsText: 'name', value: selectedDpc"></select>
		<button data-bind="click: search">検索</button>
	</div>
</div>

<!-- Data -->
<h2 data-bind="if: currentDpc()" class="dpc">
	<span data-bind="text: currentDpc().name"></span>
</h2>
<table class="dpc">
	<thead>
		<tr>
			<th>手術情報<?php echo $this->My->tip('DPC-手術情報'); ?></th>
			<th colspan="3">患者数および割合<?php echo $this->My->tip('DPC-患者数および割合'); ?></th>
			<th colspan="2">平均在院日数<?php echo $this->My->tip('DPC-平均在院日数'); ?></th>
		</tr>
	</thead>
	<tbody data-bind="foreach: wounds().concat(Total())">
		<tr>
			<td data-bind="text: Wound.operation" class="operation"></td>
			<td data-bind="text: Wound.count" class="count"></td>
			<td data-bind="text: fmRate" class="rate"></td>
			<td class="bar">
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;" data-bind="attr: {style:GetStyle}">
				    <span class="sr-only">60% Complete</span>
				  </div>
				</div>
			</td>
			<td data-bind="text: Number(Wound.days).toFixed(2)" class="days"></td>
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
			<h3 class="dpc">患者数の多い病院</h3>
			<table class="dpc-half">
				<tr><th>病院名</th><th>患者数</th><th>日数</th></tr>
				<tbody data-bind="foreach: Details_Count">
					<tr>
						<td>
							<a data-bind="text: Hospital.name, attr: { href: detailUrl + '/' + Hospital.wam_id }"></a>
						</td>
						<td data-bind="text: Detail.count"></td>
						<td data-bind="text: Number(Detail.days).toFixed(2)"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-6">
			<h3 class="dpc">在院日数の短い病院</h3>
			<table class="dpc-half">
				<tr><th>病院名</th><th>患者数</th><th>日数</th></tr>
				<tbody data-bind="foreach: Details_Days">
					<tr>
						<td>
							<a data-bind="text: Hospital.name, attr: { href: detailUrl + '/' + Hospital.wam_id }"></a>
						</td>
						<td data-bind="text: Detail.count"></td>
						<td data-bind="text: Number(Detail.days).toFixed(2)"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
