<?php $this->start('script'); ?>
<script>
// Get initial values from server
var mdcs = JSON.parse('<?php echo json_encode($mdcs); ?>');
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var getDpcsUrl = '<?php echo Router::url('/ajax/getDpcs.json'); ?>';
var getWoundsUrl = '<?php echo Router::url('/ajax/getWounds.json'); ?>';

// Knockout

function Dpc(data){
	var s = this;
	s.id = data.MdcDpc.dpc_cd;
	s.name = s.id + ' ' + data.MdcDpc.dpc;
}

function Wound(data){
	var s = this;
	s.Wound = data.Wound;
	s.Details_Count = data.Details_Count;
	s.Details_Days = data.Details_Days;
}

function AppModel(){
	var s = this;
	
	s.mdcs = mdcs;
	s.dpcs = ko.observableArray();
	s.prefectures = ko.observableArray(prefectures);
	s.wounds = ko.observableArray();									// 検索結果
	s.selectedMdc = ko.observable();
	s.selectedDpc = ko.observable();
	s.selectedPrefecture = ko.observable();
	s.firstLoad = ko.observable(false);
	
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
			console.info(data);
			s.wounds([]);
			for(var n=0; n<data.wounds.length; n++){
				s.wounds.push(new Wound(data.wounds[n]));
			}
		});
	}
	
}

var model = new AppModel();
ko.applyBindings(model);

</script>
<?php $this->end(); ?>



<!-- Menu -->
<div class="row">
	<div class="col-sm-12">傷病別統計データ</div>
	診断分類：<select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc"></select>
	傷病名：<select data-bind="options: dpcs, optionsText: 'name', value: selectedDpc"></select>
	<button data-bind="click: search">検索</button>
</div>

<!-- Data -->
<ul data-bind="foreach: wounds">
	<li>
		<span data-bind="text: Wound.operation"></span>
		<span data-bind="text: Wound.count"></span>
		<span data-bind="text: Wound.days"></span>
	</li>
</ul>

<!-- Menu 2 -->
<div class="row">
	<div class="col-sm-12">手術情報別病院ランキング</div>
	都道府県：<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
</div>

<!-- Data 2 -->
<div data-bind="foreach: wounds">
	<div class="row">
		<div class="col-sm-12">
			<span data-bind="text: Wound.operation"></span>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			患者数の多い病院
			<ul data-bind="foreach: Details_Count">
				<li>
					<span data-bind="text: Hospital.name"></span>
					<span data-bind="text: Detail.count"></span>
					<span data-bind="text: Detail.days"></span>
				</li>
			</ul>
		</div>
		<div class="col-sm-6">
			在院日数の短い病院
			<ul data-bind="foreach: Details_Days">
				<li>
					<span data-bind="text: Hospital.name"></span>
					<span data-bind="text: Detail.count"></span>
					<span data-bind="text: Detail.days"></span>
				</li>
			</ul>
		</div>
	</div>
</div>
