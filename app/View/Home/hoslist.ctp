<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var getZonesUrl = '<?php echo Router::url('/ajax/getZones.json'); ?>';
var getHospitalsUrl = '<?php echo Router::url('/ajax/getHospitals.json'); ?>';
var displayTypesOriginal = JSON.parse('<?php echo json_encode($displayTypes); ?>');
//console.info(displayTypesOriginal);

function Item(id, name){
	this.id = id;
	this.name = name;
}

function Hospital(root, data){
	var s = this;
	s.root = root;
	
	s.Area = data['Area'];
	s.Dpc = data['Dpc'];
	s.Hospital = data['Hospital'];
	s.Jcqhc = data['Jcqhc'];
	
	s.fmValue = ko.computed(function(){
		//if(typeof root.currentDisplayType() == 'undefined') return '';
		//if(root.currentDisplayType())
		var t = root.currentDisplayType().id;
		if(typeof t == 'undefined') return '';
		switch(t){
			case 'bed':
			case 'general':
			case 'doctor':
			case 'nurse':
				return s.Hospital[t];
			default:
				if(s.Dpc == undefined) return '';
				return s.Dpc.ave_month;
		}
	});
}

function AppModel(){
	var s = this;
	
	s.prefectures = prefectures;																		// 都道府県一覧
	s.zones = ko.observableArray([new Item(null, null)]);						// 選択された都道府県の医療圏一覧
	s.displayTypesOriginal = displayTypesOriginal;									// 表示切替(基本+DPC)
	s.displayTypes = ko.observableArray(s.displayTypesOriginal[0]);	// 表示切替(画面に表示中のもの)
	s.hospitals = ko.observableArray();															// 検索取得された病院一覧
	
	s.selectedPrefecture = ko.observable();			// 選択された都道府県
	s.selectedZone = ko.observable();						// 選択された医療圏
	s.hospitalName = ko.observable('');					// 検索ボックス内の病院名
	s.displayTypeGroup = ko.observable('0');		// 基本とDPCどちらの表示切替を表示するか
	s.selectedDisplayType = ko.observable();		// 選択された表示項目
	s.currentDisplayType = ko.observable();			// 現在の表示項目
	
	s.hospitalCount = ko.observable(0);
	s.nextPage = 1;
	
	s.isReadMoreVisible = function(){
		return s.hospitals().length < s.hospitalCount();
	}
	
	// 選択された都道府県に合わせて医療圏を再読み込み
	s.selectedPrefecture.subscribe(function(newValue){
		if(newValue.id !== null){
			$.ajax({
				cache: false,
				type: 'POST',
				dataType: 'JSON',
				url: getZonesUrl,
				data: {
					prefectureId: newValue.id
				}
			}).done(function(data){
				s.zones(data.zones);
			});
		}
	});
	
	// 選択された表示切り替えに合わせて項目を変更
	s.displayTypeGroup.subscribe(function(newValue){
		s.displayTypes(s.displayTypesOriginal[newValue]);
	});
	
	// 病院を検索取得
	s.getHospitals = function(){
		s.nextPage = 1;
		s.hospitals([]);
		s.getHospitalsMore();
	}
	
	// 病院をさらに検索取得(次のページ)
	s.getHospitalsMore = function(){
		$.ajax({
			cache: false,
			type: 'POST',
			dataType: 'JSON',
			url: getHospitalsUrl,
			data: {
				prefectureId: s.selectedPrefecture().id,
				zoneId: s.selectedZone().id,
				hospitalName: s.hospitalName(),
				displayType: s.selectedDisplayType().id,
				page: s.nextPage
			}
		}).done(function(data){
			console.info(data);
			s.currentDisplayType(s.selectedDisplayType());
			//s.hospitals(data.hospitals);
			for(var n=0; n<data.hospitals.length; n++){
				//s.hospitals.push(data.hospitals[n]);
				s.hospitals.push(new Hospital(s, data.hospitals[n]));
			}
			s.hospitalCount(data.count);
		});
		
		s.nextPage++;
	}
	
}
var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<!-- Menu -->
<div class="row">
	<div class="col-sm-6">
		<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
		<select data-bind="options: zones, optionsText: 'name', value: selectedZone"></select>
		<input type="text" data-bind="value: hospitalName"/>
		<button type="button" data-bind="click: getHospitals">検索</button>
		
		<!--<div data-bind="visible: selectedPrefecture, with: selectedPrefecture">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>
		<div data-bind="visible: selectedZone, with: selectedZone">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>
		<div data-bind="text: hospitalName"></div>-->
	</div>
	
	<div class="col-sm-6">
		<input type="radio" name="displayTypeGroup" value="0" data-bind="checked: displayTypeGroup" />
		<input type="radio" name="displayTypeGroup" value="1" data-bind="checked: displayTypeGroup" />
		<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>
		
		<!--<div data-bind="text: displayTypeGroup"></div>
		<div data-bind="visible: selectedDisplayType, with: selectedDisplayType">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>
		<div data-bind="visible: currentDisplayType, with: currentDisplayType">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>-->
	</div>
</div>

<!-- Head -->
<div class="row">
	<div class="col-sm-6">空白　病院名　所在地　DPC　機能評価　臨床研修</div>
	<div class="col-sm-6" data-bind="visible: currentDisplayType, with: currentDisplayType">
		<span data-bind="text: name"></span>
	</div>
</div>

<!-- Data -->
<ul data-bind="visible: hospitals, foreach: hospitals">
	<li class="row">
		<div class="col-sm-6">
			空白
			<span data-bind="text: Hospital.name"></span>
			<span data-bind="text: Area.addr2"></span>
			<span data-bind="visible: Hospital.dpc_id != 0">*DPC*</span>
			<span data-bind="visible: typeof Jcqhc != 'undefined'">*機能評価*</span>
			<span data-bind="visible: Hospital.training != 0">*臨床検収*</span>
		</div>
		<div class="col-sm-6">
			<span data-bind="text: fmValue"></span>
			バー
		</div>
	</li>
</ul>

<!-- Read More -->
<div>
	<span data-bind="text: hospitals().length"></span>
	/<span data-bind="text: hospitalCount"></span>
</div>
<div data-bind="visible: isReadMoreVisible(), click: getHospitalsMore">さらに読み込む</div>
