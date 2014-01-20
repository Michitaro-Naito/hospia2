<?php $this->start('script'); ?>
<script>

// 一覧を設定する
var mdcs = JSON.parse('<?php echo json_encode($mdcs); ?>');					// 診断分類一覧
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');	// 都道府県一覧
var cmplst = JSON.parse('<?php echo json_encode($cmplst); ?>');				// 比較指数一覧
var hospitals = JSON.parse('<?php echo json_encode($hospitals); ?>');		// 医療機関一覧
var detailUrl = '<?php echo Router::url('/hosdetail'); ?>';

// コンボボックスの初期値を設定する
var defMdc = '<?php echo $defMdc; ?>';										// 診断分類
var defPrefecture = '<?php echo $defPrefecture; ?>';						// 都道府県
var defCmp = '<?php echo $defCmp; ?>';										// 比較指数

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
	self.selectedMdc = ko.observable(defMdc);					// 選択された診断分類
	self.selectedPrefecture = ko.observable(defPrefecture);		// 選択された都道府県
	self.selectedCmp = ko.observable();					// 選択された比較指数
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
		<h2>神経系患者数ランキング</h2>
		<div class="content">
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
		</div>
	</form>
</div>

<!-- Head -->
<div class="row">
	<div class="col-sm-6">病院名　都道府県</div>
	<div class="col-sm-6">
		グラフ表示
		<select data-bind="options: cmplst, optionsText: 'name', value: selectedCmp"></select>
	</div>
</div>

<!-- Data -->
<ul data-bind="foreach: hospitals" class="items toplst">
	<li class="row">
		
		<div class="col-sm-6 left">
			<table>
				<tr>
					<td class="name"><a data-bind="text: Hospital.name, attr: { href: DetailUrl }"></a></td>
					<td class="address"><span data-bind="text: Area.addr1"></span></td>
					<td class="ave_month"><span data-bind="text: (Number(Dpc.ave_month)).toFixed(1)"></span></td>
				</tr>
			</table>
		</div>
		
		<div class="col-sm-6 right">
			<table>
				<tr>
					<td data-bind="text: fmValue" class="value"></td>
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
