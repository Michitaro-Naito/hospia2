<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function Dpc(root, data){
	var s = this;
	s.root = root;
	s.Dpc = data.Dpc;
	s.fmMdcName = ko.computed(function(){
		for(var n=0; n<root.mdcs.length; n++){
			var mdc = root.mdcs[n];
			if(mdc.id == s.Dpc.mdc_cd)
				return mdc.name;
		}
	});
	s.GetStyle = ko.computed(function(){
		if(!root.barInitialized())
			return 'width: 0%';
		var id = root.selectedDisplayTypeForDpc().id;
		return 'width: ' + 100 * s.Dpc[id] / s.root.MaxValue() + '%';
	});
}

function AppModel(){
	var s = this;
	
	s.fiscalYears = dat.fiscalYears;						// 会計年度一覧
	s.displayTypesForDpc = dat.displayTypesForDpc;// 表示方法一覧
	s.mdcs = dat.mdcs;
	s.dpcs = ko.observableArray();									// 検索取得したDpc一覧
	
	s.selectedFiscalYear = ko.observable();					// 選択された会計年度
	s.selectedDisplayTypeForDpc = ko.observable();	// 選択された表示方法
	s.barInitialized = ko.observable(false);
	
	s.MaxValue = ko.computed(function(){
		var display = s.selectedDisplayTypeForDpc();
		if(typeof display == 'undefined')
			return 0;
		var key = display.id;
		var max = 0;
		$.each(s.dpcs(), function(index, dpc){
			var value = Number(dpc.Dpc[key]);
			if(value > max)
				max = value;
		});
		return max;
	});
	
	s.selectedDisplayTypeForDpc.subscribe(function(newValue){
		s.sort();
	});
	
	s.sort = function(key){
		var key = s.selectedDisplayTypeForDpc().id;
		var dpcs = s.dpcs();
		dpcs.sort(function(a, b){
			return Number(b.Dpc[key]) - Number(a.Dpc[key]);
		});
		s.dpcs(dpcs);
		s.barInitialized(false);
		setTimeout(function(){
			s.barInitialized(true);
		}, 1000);
	}
	
	s.search = function(){
		console.info('search');
		$.postJSON({
			url: dat.getDpcsUrl,
			data: {
				wamId: dat.wamId,
				fiscalYear: s.selectedFiscalYear().id
			}
		}).done(function(data){
			console.info(data);
			var dpcs = [];
			for(var n=0; n<data.dpcs.length; n++){
				var dpc = data.dpcs[n];
				dpcs.push(new Dpc(s, dpc));
			}
			s.dpcs(dpcs);
			s.sort();
		});
	}
}

var model = new AppModel();
ko.applyBindings(model, document.getElementById('hosdetail'));
model.search();
</script>
<?php $this->end(); ?>



<div id="hosdetail">
	<?php echo $this->element('hosdetail_menu'); ?>
	<?php echo $this->element('additional_information'); ?>
	
	<!-- Menu -->
	<div class="box">
		<h2>診療実績</h2>
		<div class="content">
			年度<select data-bind="options: fiscalYears, optionsText: 'name', value: selectedFiscalYear"></select>
			<button data-bind="click: search">検索</button>
		</div>
	</div>
	
	<!-- Head -->
	<div class="row">
		<div class="col-sm-6">
			<table class="hosdetail-head">
				<tr>
					<th class="">診断分類<?php echo $this->My->tip('診療実績-診断分類'); ?></th>
					<th class="ave_month">月平均患者数<?php echo $this->My->tip('診療実績-月平均患者数'); ?></th>
					<th class="zone_share">医療圏シェア<?php echo $this->My->tip('診療実績-医療圏シェア'); ?></th>
					<th class="ave_in">平均在院日数<?php echo $this->My->tip('診療実績-平均在院日数'); ?></th>
					<th class="complex">患者構成指標<?php echo $this->My->tip('診療実績-患者構成指標'); ?></th>
					<th class="efficiency">在院日数指標<?php echo $this->My->tip('診療実績-在院日数指標'); ?></th>
				</tr>
			</table>
		</div>
		<div class="col-sm-6">
			<table>
				<tr>
					<th>
						<select data-bind="options: displayTypesForDpc, optionsText: 'name', value: selectedDisplayTypeForDpc"></select>
						<?php echo $this->My->tip('グラフ表示'); ?>
					</th>
				</tr>
			</table>
		</div>
	</div>
	
	<!-- Data -->
	<ul data-bind="foreach: dpcs" class="items hosdetail">
		<li class="row">
			<div class="col-sm-6 left">
				<table>
					<tr>
						<td data-bind="text: fmMdcName" class="mdc-name"></td>
						<td data-bind="text: Number(Dpc.ave_month).toFixed(1)" class="ave_month"></td>
						<td data-bind="text: Number(Dpc.zone_share).toFixed(1) + '%'" class="zone_share"></td>
						<td data-bind="text: Number(Dpc.ave_in).toFixed(1)" class="ave_in"></td>
						<td data-bind="text: Number(Dpc.complex).toFixed(2)" class="complex"></td>
						<td data-bind="text: Number(Dpc.efficiency).toFixed(2)" class="efficiency"></td>
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
