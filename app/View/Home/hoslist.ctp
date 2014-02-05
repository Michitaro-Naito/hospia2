<?php $this->assign('title', '病院検索'); ?>
<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var getZonesUrl = '<?php echo Router::url('/ajax/getZones.json'); ?>';
var getHospitalsUrl = '<?php echo Router::url('/ajax/getHospitals.json'); ?>';
var detailUrl = '<?php echo Router::url('/hosdetail'); ?>';
var displayTypesOriginal = JSON.parse('<?php echo json_encode($displayTypes); ?>');
var isMobile = <?php echo $this->request->is('mobile')? 'true': 'false' ?>;

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
		var t = root.currentDisplayType().id;
		var v = '';
		if(typeof t !== 'undefined'){
			switch(t){
				case 'bed':
				case 'general':
				case 'doctor':
				case 'nurse':
					v = s.Hospital[t];
					break;
				default:
					if(typeof s.Dpc !== 'undefined')
						v = Number(s.Dpc.ave_month).toFixed(0);
					break;
			}
		}
		if(v===null)
			return '';
		if(v==0)
			return '';
		return v;
	});
	s.fmDpcCondition = ko.computed(function(){
		//Hospital.dpc_ct
		var str = s.Hospital.dpc_ct;
		//平成24年度新規DPC準備病院
		str = str.replace(/平成([0-9]*)年度DPC参加病院/, '$1年<br>参加');
		str = str.replace(/平成([0-9]*)年度新規DPC準備病院/, '$1年<br>準備');
		return str;
	});
	s.GetStyle = ko.computed(function(){
		if(!root.barInitialized()) return 'width: 0%';
		var rate = 100 * parseFloat(s.fmValue()) / root.MaxValue();
		return 'width: ' + rate + '%';
	});
	s.DetailUrl = ko.computed(function(){
		return detailUrl + '/' + s.Hospital.wam_id;
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
	s.selectedDisplayType = ko.observable(s.displayTypes()[1]);		// 選択された表示項目
	s.currentDisplayType = ko.observable();			// 現在の表示項目
	
	s.initialized = ko.observable(false);				// 初回の病院一覧の取得が完了しているか
	s.barInitialized = ko.observable(false);
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
				// GETパラメータでzoneIdが指定されている場合は、初期値として選択する。（選択中の都道府県に該当する医療圏が存在する場合のみ）
				// また、病院一覧が未取得の場合は取得する。
				if(!s.initialized()){
					for(var n=0; n<s.zones().length; n++){
						var z = s.zones()[n];
						if(z.id == s.default.zoneId){
							s.selectedZone(z);
							break;
						}
					}
					s.getHospitals();
				}
			});
		}
	});
	
	// 選択された表示切り替えに合わせて項目を変更
	s.displayTypeGroup.subscribe(function(newValue){
		s.displayTypes(s.displayTypesOriginal[newValue]);
	});
	
	s.MaxValue = ko.computed(function(){
		var max = 0;
		for(var n=0; n<s.hospitals().length; n++){
			var h = s.hospitals()[n];
			var value = parseFloat(h.fmValue());
			if(value > max)
				max = value;
		}
		return max;
	});
	
	// 病院を検索取得
	s.getHospitals = function(){
		s.initialized(true);
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
			s.currentDisplayType(s.selectedDisplayType());
			for(var n=0; n<data.hospitals.length; n++){
				s.hospitals.push(new Hospital(s, data.hospitals[n]));
			}
			s.hospitalCount(data.count);
			if(isMobile){
				s.barInitialized(true);
			}else{
				s.barInitialized(false);
				setTimeout(function(){
					s.barInitialized(true);
				}, 1000);
			}
		});
		
		s.nextPage++;
	}
	
	// GETパラメータに応じて初期値を設定する
	var uri = new Uri(location.href);
	s.default = {};
	s.default.prefectureId = uri.getQueryParamValue('prefectureId');
	s.default.zoneId = uri.getQueryParamValue('zoneId');
	s.default.hospitalName = uri.getQueryParamValue('hospitalName');
	for(var n=0; n<s.prefectures.length; n++){
		var p = s.prefectures[n];
		if(p.id == s.default.prefectureId){
			s.selectedPrefecture(p);
			break;
		}
	}
	if(s.default.hospitalName) s.hospitalName(decodeURI(s.default.hospitalName));
	
}
var model = new AppModel();
ko.applyBindings(model);

if(model.selectedPrefecture().id == null) model.getHospitals();
</script>
<?php $this->end(); ?>



<!-- Menu -->
<div class="row row-search">
	<div class="col-sm-6">
		<div class="box">
			<h2>
				<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
				病院検索
			</h2>
			<div class="content">
				<table class="search">
					<tr>
						<td>都道府県</td>
						<td><select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select></td>
					</tr>
					<tr>
						<td>医療圏</td>
						<td><select data-bind="options: zones, optionsText: 'name', value: selectedZone"></select></td>
					</tr>
					<tr>
						<td>病院名(一部でも可)</td>
						<td><input type="text" data-bind="value: hospitalName"/></td>
					</tr>
					<tr>
						<td colspan="2">
							<button type="button" class="search" data-bind="click: getHospitals">
								<?php echo $this->Html->image('icon/search.png', array('style'=>'padding-bottom:3px;')); ?>
								検索
							</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
	<div class="col-sm-6">
		<div class="box">
			<h2>
				<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
				表示切替<?php echo $this->My->tip('病院検索-表示切替', array('image'=>true)); ?>
			</h2>
			<div class="content">
				<div style="margin-top: 20px;">
					<label>
						<input type="radio" name="displayTypeGroup" value="0" data-bind="checked: displayTypeGroup" />
						基本項目<?php echo $this->My->tip('病院検索-基本項目'); ?>
					</label>
					<label>
						<input type="radio" name="displayTypeGroup" value="1" data-bind="checked: displayTypeGroup" />
						診断分類別患者数<?php echo $this->My->tip('病院検索-診断分類別患者数'); ?>
					</label>
				</div>
				<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>
				<button type="button" class="" data-bind="click: getHospitals">表示</button>
			</div>
		</div>
	</div>
</div>

<!-- Head -->
<div class="row thead">
	<div class="col-sm-6 left">
		<table>
			<thead>
				<tr>
					<th class="name">病院名<?php echo $this->My->tip('病院名'); ?></th>
					<th class="address">所在地</th>
					<th class="dpc">DPC<?php echo $this->My->tip('DPC'); ?></th>
					<th class="jcqhc">機能評価<?php echo $this->My->tip('機能評価'); ?></th>
					<th class="training">臨床研修<?php echo $this->My->tip('臨床研修'); ?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="col-sm-6 right" data-bind="visible: currentDisplayType, with: currentDisplayType">
		<table>
			<thead>
				<tr>
					<th data-bind="text: name"></th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<!-- Data -->
<ul class="hoslist" data-bind="visible: hospitals, foreach: hospitals">
	<li class="row">
		<div class="col-sm-6 left">
			<table>
				<tr>
					<td class="name">
						<a data-bind="text: Hospital.name, attr: {href: DetailUrl}, visible: Hospital.dpc_id != 0"></a>
						<span data-bind="text: Hospital.name, visible: Hospital.dpc_id == 0" class="muted"></span>
					</td>
					<td class="address" data-bind="text: Area.addr2"></td>
					<td class="dpc">
						<div data-bind="visible: Hospital.dpc_id != 0, html: fmDpcCondition" class="icon-like"></div>
					</td>
					<td class="jcqhc">
						<div data-bind="visible: typeof Jcqhc != 'undefined'" class="icon-like">機能<br/>評価</div>
					</td>
					<td class="training">
						<div data-bind="visible: Hospital.training != 0" class="icon-like">臨床<br/>研修</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-sm-6 right">
			<table>
				<tr>
					<td class="value"><span data-bind="text: addFigure(fmValue())"></span></td>
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

<!-- Read More -->
<div>
	<span data-bind="text: hospitals().length"></span>
	/<span data-bind="text: hospitalCount"></span>
</div>
<button class="btn btn-default" data-bind="visible: isReadMoreVisible(), click: getHospitalsMore">さらに読み込む</button>
