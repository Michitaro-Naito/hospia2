<?php $this->start('script'); ?>
<script>
(function(){

var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function Hospital(data){
	var s = this;
	s.Hospital = data.Hospital;
	s.Area = data.Area;
	s.Dpc = data.Dpc;
	s.patients = ko.computed(function(){
		return s.Dpc.ave_month;
		//if(model.currentComparisonCategory().id == 'basic') return s.Hospital.patient;
		//if(model.currentComparisonCategory().id == 'dpc') return s.Dpc.ave_month;
	}, this);
	s.valueForSelection = ko.computed(function(){
		if(model.currentComparisonCategory().id == 'basic') return s.Hospital[model.selectedDisplayTypeForBasic().id];
		if(model.currentComparisonCategory().id == 'dpc') return s.Dpc[model.selectedDisplayTypeForDpc().id];
	}, this);
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
	
	s.hospitals = ko.observableArray();										// 検索取得された病院一覧
	
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
				s.hospitals.push(new Hospital(data.hospitals[n]));
			}
		});
	}
}

var model = new AppModel();
ko.applyBindings(model);

})();
</script>
<?php $this->end(); ?>



<!-- Menu -->
<div data-bind="text: wamId"></div>
<select data-bind="options: comparisonCategories, optionsText: 'name', value: selectedComparisonCategory"></select>
<select data-bind="visible: selectedComparisonCategory().id=='dpc', options: mdcs, optionsText: 'name', value: selectedMdc"></select>
<select data-bind="options: displayTypesForHoscmp, optionsText: 'name', value: selectedDisplayTypeForHoscmp"></select>
<button data-bind="click: search">検索</button>

<select data-bind="visible: currentComparisonCategory().id == 'basic', options: displayTypesForBasic, optionsText: 'name', value: selectedDisplayTypeForBasic"></select>
<select data-bind="visible: currentComparisonCategory().id == 'dpc', options: displayTypesForDpc, optionsText: 'name', value: selectedDisplayTypeForDpc"></select>

<!-- Data -->
<div data-bind="text: currentComparisonCategory().name"></div>
<ul data-bind="foreach: hospitals">
	<li>
		<span data-bind="text: Hospital.name"></span>
		<span data-bind="text: patients"></span>
		<span data-bind="text: valueForSelection"></span>
		<span data-bind="text: Hospital.distance"></span>
	</li>
</ul>

<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url(null, true)));
?>
