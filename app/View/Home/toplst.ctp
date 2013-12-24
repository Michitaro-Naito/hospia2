<!-- 改修前のコード -->

<!-- Head -->
<!--
<div class="col-sm-6">空白　病院名　月平均患者数</div>
<div class="col-sm-6">グラフ表示</div>
-->

<!-- Data -->
<!--
<div class="row">
	<div class="col-sm-6">空白　○○病院　123</div>
	<div class="col-sm-6">123.4　バー</div>
</div>
<div class="row">
	<div class="col-sm-6">空白　○○病院　123</div>
	<div class="col-sm-6">123.4　バー</div>
</div>
<div class="row">
	<div class="col-sm-6">空白　○○病院　123</div>
	<div class="col-sm-6">123.4　バー</div>
</div>
-->



<!-- JavaScript----------------------------------------------------------- -->
<?php $this->start('script'); ?>
<script>

// 一覧を設定する
var mdcs = JSON.parse('<?php echo json_encode($mdcs); ?>');					// 診断分類一覧
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');	// 都道府県一覧
var cmplst = JSON.parse('<?php echo json_encode($cmplst); ?>');				// 比較指数一覧
var hospitals = JSON.parse('<?php echo json_encode($hospitals); ?>');		// 医療機関一覧

// コンボボックスの初期値を設定する
var defMdc = '<?php echo $defMdc; ?>';										// 診断分類
var defPrefecture = '<?php echo $defPrefecture; ?>';						// 都道府県
var defCmp = '<?php echo $defCmp; ?>';										// 比較指数

// バインド対象のデータを設定する
function AppModel(){

	var self = this;

	self.mdcs = mdcs;											// 診断分類一覧
	self.prefectures = prefectures;								// 都道府県一覧
	self.cmplst = cmplst;										// 比較指数一覧
	self.hospitals = hospitals;									// 医療機関一覧
	self.selectedMdc = ko.observable(defMdc);					// 選択された診断分類
	self.selectedPrefecture = ko.observable(defPrefecture);		// 選択された都道府県
	self.selectedCmp = ko.observable(defCmp);					// 選択された比較指数

	// 比較指数
	self.cmpDisp = {
		ave_month : ko.observable(false),
		zone_share : ko.observable(false),
		ave_in : ko.observable(false),
		complex : ko.observable(false),
		efficiency : ko.observable(false)
	};

	// 比較指数が変更されたとき
	self.selectedCmp.subscribe(function(newValue){
		changeCmpVisible(newValue);
	});
}

// 医療機関一覧を検索する
function getHospitals(){
	$("#menuForm").submit();
}

// 比較指数の表示非表示を切り替える
function changeCmpVisible(cmp){
	model.cmpDisp.ave_month(false);
	model.cmpDisp.zone_share(false);
	model.cmpDisp.ave_in(false);
	model.cmpDisp.complex(false);
	model.cmpDisp.efficiency(false);
	switch (cmp) {
	case 'ave_month@dpc':
		model.cmpDisp.ave_month(true);
		break;
	case 'zone_share@dpc':
		model.cmpDisp.zone_share(true);
		break;
	case 'ave_in@dpc':
		model.cmpDisp.ave_in(true);
		break;
	case 'complex@dpc':
		model.cmpDisp.complex(true);
		break;
	case 'efficiency@dpc':
		model.cmpDisp.efficiency(true);
		break;
	default:
		break;
	}
}

// バインドする
var model = new AppModel();
ko.applyBindings(model);

// 比較指数の表示・非表示に初期値を設定する
changeCmpVisible(defCmp);

</script>
<?php $this->end(); ?>
<!-- --------------------------------------------------------------------- -->

<!-- メニュー -->
<div class="row">
	<form action="<?php echo $this->webroot.'toplst'; ?>" method="POST" id="menuForm">
		<div class="col-sm-12">神経系患者数ランキング</div>
		<input type="hidden" name="valueMdc" data-bind="value: selectedMdc"/>
		<input type="hidden" name="valuePrefecture" data-bind="value: selectedPrefecture"/>
		<input type="hidden" name="valueCmp" data-bind="value: selectedCmp"/>
		<table>
			<tr>
				<td>
					　診断分類　
					<select data-bind="options: mdcs, optionsValue: 'id', optionsText: 'name', value: selectedMdc, event: {change: getHospitals}"></select>
				</td>
				<td>
					<span id="h01">　　都道府県&nbsp;<img src="http://hospia.jp/img/helplink2.jpg" alt="" />&nbsp;</span>
					<select data-bind="options: prefectures, optionsValue: 'id', optionsText: 'name', value: selectedPrefecture, event: {change: getHospitals}"></select>
				</td>
			</tr>
		</table>
	</form>
</div>

<!-- 医療機関一覧 -->
<div class="row">
	<div id="ht_toplst_main">
		<table class="hosdetail_table" border="1">
			<!-- ヘッダ -->
			<thead>
				<tr>
					<th></th>
					<th><span id="h03">病院名<img src="http://hospia.jp/img/helplink.jpg" alt="" /></span></th>
					<th>都道府県</th>
					<th width = "80"><span id="h04" >月平均<br>患者数<br><img src="http://hospia.jp/img/helplink.jpg" alt="" /></span></th>
					<th colspan="2" width="300">
						<span id="h02">
							グラフ表示
							<select data-bind="options: cmplst, optionsValue: 'id', optionsText: 'name', value: selectedCmp"></select>
							<img src="http://hospia.jp/img/helplink.jpg" alt="" />
						</span>
					</th>
				</tr>
			</thead>
			<!-- 医療機関情報 -->
			<tbody data-bind="foreach: hospitals">
				<tr bgcolor="#f6f6f6">
					<!-- 医療機関詳細表示用のアイコン -->
					<td style="width:20px;" class="centertd">
						<!-- <img src="http://hospia.jp/img/URLgray.jpg" style="cursor:pointer;" alt="" onClick="TODO" /> -->
						<span data-bind="text: Hospital.wam_id"></span>
					</td>
					<!-- 医療機関名のリンク(医療機関詳細ページに遷移) -->
					<td style="width:220px;" >
						<a data-bind="attr: { href: 'http://hospia.jp/hosdetail/?wam_id=' + Hospital.wam_id}">
							<span data-bind="text: Hospital.name"></span>
						</a>
					</td>
					<!-- 医療機関が在る都道府県の名前 -->
					<td style="width:100px;" >
						<span data-bind="text: Area.addr1"></span>
					</td>
					<!-- 月平均患者数 -->
					<td style="width:50px;" align="center">
						<span data-bind="text: (Number(Dpc.ave_month)).toFixed(1)"></span>
					</td>
					<!-- グラフ表示 -->
					<td style="width:60px;" align="right">
						<span data-bind="text: (Number(Dpc.ave_month)).toFixed(1), visible: model.cmpDisp.ave_month"></span>
						<span data-bind="text: (Number(Dpc.zone_share) * 100).toFixed(1) + '%', visible: model.cmpDisp.zone_share"></span>
						<span data-bind="text: (Number(Dpc.ave_in)).toFixed(1), visible: model.cmpDisp.ave_in"></span>
						<span data-bind="text: (Number(Dpc.complex)).toFixed(2), visible: model.cmpDisp.complex"></span>
						<span data-bind="text: (Number(Dpc.efficiency)).toFixed(2), visible: model.cmpDisp.efficiency"></span>
					</td>
					<td>
						<input type="hidden" id="id_barval1" value="213.167" />
						<img id="id_bar1" src="http://hospia.jp/img/bar.jpg" width="213.167" height="20" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>