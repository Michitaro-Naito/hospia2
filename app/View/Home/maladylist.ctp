<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var maladyCategories = JSON.parse('<?php echo json_encode($maladyCategories); ?>');
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var getHospitalsByMaladyUrl = '<?php echo Router::url('/ajax/getHospitalsByMalady.json'); ?>';
var defaultMaladyCategory = JSON.parse('<?php echo json_encode($defaultMaladyCategory); ?>');

function MaladyCategory(data){
	var s = this;
	
	s.id = data.id;
	s.name = data.name;
	/* 
	 * 一覧表示の際は以下の形式で表示する。
	 * 
	 * がん合計
	 * ├食道がん
	 * ├...
	 * └その他がん
	 */
	s.fmName = ko.computed(function(){
		var name = s.name;
		if(s.id > 'm100' && s.id < 'm120') name = '├' + name;
		if(s.id == 'm120') name = '└' + name;
		return name;
	}, this);
}

function AppModel(){
	var s = this;
	
	s.maladyCategories = [];											// 疾患カテゴリ
	for(var n=0; n<maladyCategories.length; n++){
		s.maladyCategories.push(new MaladyCategory(maladyCategories[n]));
	}
	s.prefectures = prefectures;									// 都道府県一覧
	s.hospitals = ko.observableArray();						// 検索取得された病院一覧(トップ100)
	
	s.selectedMaladyCategory = ko.observable();		// 選択された疾患カテゴリ
	s.selectedPrefecture = ko.observable();				// 選択された都道府県
	
	// 選択された疾患カテゴリと都道府県から、病院一覧を検索する。
	s.search = function(){
		$.postJSON({
			url: getHospitalsByMaladyUrl,
			data: {
				maladyId: s.selectedMaladyCategory().id,
				prefectureId: s.selectedPrefecture().id
			}
		}).done(function(data){
			s.hospitals(data.hospitals);
		});
	}
	
	// 既定の疾患カテゴリを選択する
	for(var n=0; n<s.maladyCategories.length; n++){
		var c = s.maladyCategories[n];
		if(c.id == defaultMaladyCategory){
			s.selectedMaladyCategory(c);
			return;
		}
	}
}

var model = new AppModel();
ko.applyBindings(model);
model.search();
</script>
<?php $this->end(); ?>



<!-- Menu -->
<select data-bind="options: maladyCategories, optionsText: 'fmName', value: selectedMaladyCategory"></select>
<div data-bind="text: selectedMaladyCategory().name"></div>
<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
<div data-bind="text: selectedPrefecture().name"></div>
<button data-bind="click: search">他の疾患・地域に変更する</button>

<!-- Head -->

<!-- Data -->
<div data-bind="foreach: hospitals">
	<div>
		<span data-bind="text: $index()+1"></span>
		<span data-bind="text: Hospital.name"></span>
		<span data-bind="text: Area.addr1"></span>
		<span data-bind="text: Area.addr2"></span>
		<span data-bind="text: MaladyData.mcounts"></span>
		<span data-bind="text: MaladyData.mdays"></span>
	</div>
</div>
