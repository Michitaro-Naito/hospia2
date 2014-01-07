<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function AppModel(){
	var s = this;
	
	s.fiscalYears = dat.fiscalYears;						// 会計年度一覧
	s.displayTypesForDpc = dat.displayTypesForDpc;// 表示方法一覧
	s.dpcs = ko.observableArray();									// 検索取得したDpc一覧
	
	s.selectedFiscalYear = ko.observable();					// 選択された会計年度
	s.selectedDisplayTypeForDpc = ko.observable();	// 選択された表示方法
	
	s.search = function(){
		console.info('search');
		$.postJSON({
			url: dat.getDpcsUrl,
			data: {
				wamId: dat.wamId,
				fiscalYear: s.selectedFiscalYear().id
			}
		}).done(function(data){
			s.dpcs(data.dpcs);
		});
	}
}

var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<?php echo $this->element('additional_information'); ?>

<!-- Menu -->
<select data-bind="options: fiscalYears, optionsText: 'name', value: selectedFiscalYear"></select>
<select data-bind="options: displayTypesForDpc, optionsText: 'name', value: selectedDisplayTypeForDpc"></select>
<button data-bind="click: search">検索</button>

<!-- Data -->
<ul data-bind="foreach: dpcs">
	<li>
		<span data-bind="text: Dpc.ave_month"></span>
		<span data-bind="text: Dpc.zone_share"></span>
		<span data-bind="text: Dpc.ave_in"></span>
		<span data-bind="text: Dpc.complex"></span>
		<span data-bind="text: Dpc.efficiency"></span>
	</li>
</ul>

<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url(null, true)));
?>
