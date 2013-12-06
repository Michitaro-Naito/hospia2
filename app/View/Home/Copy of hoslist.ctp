<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var getZonesUrl = '<?php echo Router::url('/ajax/getZones.json'); ?>';
var displayTypesOriginal = JSON.parse('<?php echo json_encode($displayTypes); ?>');

function Item(id, name){
	this.id = id;
	this.name = name;
}
function AppModel(){
	var s = this;
	
	s.prefectures = prefectures;
	s.zones = ko.observableArray();
	s.displayTypesOriginal = displayTypesOriginal;
	s.displayTypes = ko.observableArray(s.displayTypesOriginal[0]);
	
	s.selectedPrefecture = ko.observable();
	s.selectedZone = ko.observable();
	s.hospitalName = ko.observable('');
	s.displayTypeGroup = ko.observable('0');
	s.selectedDisplayType = ko.observable();
	
	s.selectedPrefecture.subscribe(function(newValue){
		// 選択された都道府県に合わせて医療圏を再読み込み
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
	s.displayTypeGroup.subscribe(function(newValue){
		// 選択された表示切り替えに合わせて項目を変更
		s.displayTypes(s.displayTypesOriginal[newValue]);
	});
	
	s.search = function(){
		alert('searching');
	}
}
var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<?php
echo $this->Form->create('HoslistVM');
echo $this->Form->select('prefecture', $prefectures, array('data-bind'=>'foo'));
echo $this->Form->select('zone', array());
echo $this->Form->submit();
echo $this->Form->end();
debug($this->data);
?>



<!-- Menu -->
<div class="row">
	<div class="col-sm-6">
		<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
		<select data-bind="options: zones, optionsText: 'name', value: selectedZone"></select>
		<input type="text" data-bind="value: hospitalName"/>
		<button type="button" data-bind="click: search">検索</button>
		
		<div data-bind="visible: selectedPrefecture, with: selectedPrefecture">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>
		<div data-bind="visible: selectedZone, with: selectedZone">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>
		<div data-bind="text: hospitalName"></div>
	</div>
	
	<div class="col-sm-6">
		<input type="radio" name="displayTypeGroup" value="0" data-bind="checked: displayTypeGroup" />
		<input type="radio" name="displayTypeGroup" value="1" data-bind="checked: displayTypeGroup" />
		<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>
		
		<div data-bind="text: displayTypeGroup"></div>
		<div data-bind="visible: selectedDisplayType, with: selectedDisplayType">
			<span data-bind="text: id"></span>
			<span data-bind="text: name"></span>
		</div>
	</div>
</div>

<br/><br/><br/>

<!-- Head -->
<div class="row">
	<div class="col-sm-6">空白　病院名　所在地　DPC　機能評価　臨床研修</div>
	<div class="col-sm-6">筋骨格系　月平均退院患者数</div>
</div>

<!-- Data -->
<div class="row">
	<div class="col-sm-6">空白　○○病院　○○市　DPC　機能評価　臨床研修</div>
	<div class="col-sm-6">13　バー</div>
</div>
<div class="row">
	<div class="col-sm-6">空白　○○病院　○○市　DPC　機能評価　臨床研修</div>
	<div class="col-sm-6">13　バー</div>
</div>
<div class="row">
	<div class="col-sm-6">空白　○○病院　○○市　DPC　機能評価　臨床研修</div>
	<div class="col-sm-6">13　バー</div>
</div>