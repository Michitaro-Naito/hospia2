<?php $this->assign('title', '患者数ランキング'); ?>
<?php $this->start('script'); ?>
<script>

// 一覧を設定する
var mdcs = JSON.parse('<?php echo json_encode($mdcs); ?>');					// 診断分類一覧
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');	// 都道府県一覧
var cmplst = JSON.parse('<?php echo json_encode($cmplst); ?>');				// 比較指数一覧
var years = JSON.parse('<?php echo json_encode($years); ?>');	// 会計年度一覧
var hospitals = JSON.parse('<?php echo json_encode($hospitals); ?>');		// 医療機関一覧
var detailUrl = '<?php echo Router::url('/hosdetail'); ?>';

// コンボボックスの初期値を設定する
var defMdc = '<?php echo $defMdc; ?>';										// 診断分類
var defPrefecture = '<?php echo $defPrefecture; ?>';						// 都道府県
var defCmp = '<?php echo $defCmp; ?>';										// 比較指数
var defYear = '<?php echo $defYear; ?>';

// Represents a Hospital
function Hospital(root, data){
	var s = this;
	s.root = root;
	s.Area = data.Area;
	s.Dpc = data.Dpc;
	s.Hospital = data.Hospital;
	
	// 選択中の値を返す
	s.Value = ko.computed(function(){
		var cmp = s.root.selectedCmp();
		if(typeof cmp === 'undefined')
			return 0;
		switch(cmp.id){
			case 'ave_month@dpc':
				return Number(s.Dpc.ave_month);
			case 'zone_share@dpc':
				return Number(s.Dpc.zone_share);
			case 'ave_in@dpc':
				return Number(s.Dpc.ave_in);
			case 'complex@dpc':
				return Number(s.Dpc.complex);
			case 'efficiency@dpc':
				return Number(s.Dpc.efficiency);
		}
		return 0;
	});
	
	// フォーマットされた選択中の値を返す
	s.fmValue = ko.computed(function(){
		var value = s.Value();
		var cmp = s.root.selectedCmp();
		if(typeof cmp === 'undefined')
			return value;
		switch(cmp.id){
			case 'ave_month@dpc':
			case 'ave_in@dpc':
				return value.toFixed(1);
			case 'zone_share@dpc':
				return (value * 100).toFixed(1) + '%';
			case 'complex@dpc':
			case 'efficiency@dpc':
				return value.toFixed(2);
		}
		return value;
	});
	s.GetStyle = ko.computed(function(){
		if(!root.barInitialized()) return 'width: 0%';
		var rate = 100 * s.Value() / root.MaxValue();
		return 'width: ' + rate + '%';
	});
	s.DetailUrl = ko.computed(function(){
		return detailUrl + '/' + s.Hospital.wam_id;
	});
}

// バインド対象のデータを設定する
function AppModel(){
	var self = this;

	self.mdcs = mdcs;											// 診断分類一覧
	self.prefectures = prefectures;								// 都道府県一覧
	self.cmplst = cmplst;										// 比較指数一覧
	self.years = years;
	//self.selectedMdc = ko.observable(defMdc);					// 選択された診断分類
	self.selectedMdc = ko.observable(self.mdcs[defMdc]);
	self.selectedPrefecture = ko.observable(defPrefecture);		// 選択された都道府県
	self.selectedCmp = ko.observable();					// 選択された比較指数
	self.selectedYear = ko.observable(defYear);
	self.barInitialized = ko.observable(false);
	var hs = [];
	for(var n=0; n<hospitals.length; n++){
		hs.push(new Hospital(self, hospitals[n]));
	}
	self.hospitals = hs;			// 医療機関一覧
	
	self.MaxValue = ko.computed(function(){
		var max = 0;
		for(var n=0; n<self.hospitals.length; n++){
			var h = self.hospitals[n];
			var value = h.Value();
			if(value > max)
				max = value;
		}
		return max;
	});
	
	// 初期値を設定する
	self.setDefaultValues = function(){
		// selectedCmp
		for(var n=0; n<model.cmplst.length; n++){
			var cmp = self.cmplst[n];
			if(cmp.id === defCmp){
				self.selectedCmp(cmp);
				break;
			}
		}
		self.barInitialized(false);
		setTimeout(function(){
			self.barInitialized(true);
		}, 1000);
	}
}

// 医療機関一覧を検索する
function getHospitals(){
	$("#menuForm").submit();
}

// バインドする
var model = new AppModel();
ko.applyBindings(model);
model.setDefaultValues();

</script>
<?php $this->end(); ?>



<!-- メニュー -->
<div class="box">
	<form action="<?php echo $this->webroot.'toplst'; ?>" method="POST" id="menuForm">
		<h2>
			<span data-bind="text: selectedMdc().name"></span>
			患者数ランキング
		</h2>
		<div class="content">
			<input type="hidden" name="valueMdc" data-bind="value: selectedMdc().id"/>
			<input type="hidden" name="valuePrefecture" data-bind="value: selectedPrefecture"/>
			<input type="hidden" name="valueCmp" data-bind="value: selectedCmp"/>
			<input type="hidden" name="valueYear" data-bind="value: selectedYear"/>
			<table>
				<tr>
					<td>
						　診断分類　
						<select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc, event: {change: getHospitals}"></select>
					</td>
					<td style="padding-left: 20px;">
						都道府県<?php echo $this->My->tip('患者数ランキング-都道府県'); ?>
						<select data-bind="options: prefectures, optionsValue: 'id', optionsText: 'name', value: selectedPrefecture, event: {change: getHospitals}"></select>
					</td>
					<td>
						<span class="premium">会計年度</span>
						<select data-bind="options: years, optionsValue: 'id', optionsText: 'name', value: selectedYear, event: {change: getHospitals}"></select>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<!-- Head -->
<div class="row thead">
	<div class="col-sm-6">
		<table>
			<tr>
				<th class="name">病院名<br /><?php echo $this->My->tip('病院名'); ?></th>
				<th class="address">都道府県</th>
				<th class="ave_month">月平均患者数</th>
			</tr>
		</table>
	</div>
	<div class="col-sm-6">
		<table>
			<tr>
				<th>
					グラフ表示の変更
					<select data-bind="options: cmplst, optionsText: 'name', value: selectedCmp"></select>
				</th>
			</tr>
		</table>
	</div>
	<div style="clear: both;"> </div>
</div>

<!-- Data -->
<ul data-bind="foreach: hospitals" class="items toplst">
	<li class="row">
		
		<div class="col-sm-6 left">
			<table>
				<tr>
					<td class="name"><a data-bind="text: Hospital.name, attr: { href: DetailUrl }"></a></td>
					<td class="address"><span data-bind="text: Area.addr1"></span></td>
					<td class="ave_month" style="text-align: right;"><span data-bind="text: addFigure((Number(Dpc.ave_month)).toFixed(1))" class="ar"></span></td>
				</tr>
			</table>
		</div>
		
		<div class="col-sm-6 right">
			<table>
				<tr>
					<td data-bind="text: addFigure(fmValue())" class="value" style="text-align: right;"></td>
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
		<div style="clear: both;"> </div>
	</li>
</ul>
