<?php $this->assign('title', '主な疾患別患者数ランキング'); ?>
<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var maladyCategories = JSON.parse('<?php echo json_encode($maladyCategories); ?>');
var prefectures = JSON.parse('<?php echo json_encode($prefectures); ?>');
var years = JSON.parse('<?php echo json_encode($years); ?>');
var getHospitalsByMaladyUrl = '<?php echo Router::url('/ajax/getHospitalsByMalady.json'); ?>';
var defaultMaladyCategory = JSON.parse('<?php echo json_encode($defaultMaladyCategory); ?>');
var detailUrl = '<?php echo Router::url('/hosdetail'); ?>';
var premiumContentUrl = '<?php echo Router::url('/users/premium_content'); ?>';

function Hospital(data){
	var s = this;
	s.Hospital = data.Hospital;
	s.Area = data.Area;
	s.MaladyData = data.MaladyData;
	s.DetailUrl = ko.computed(function(){
		return detailUrl + '/' + s.Hospital.wam_id;
	});
}

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
	s.years = years;
	s.hospitals = ko.observableArray();						// 検索取得された病院一覧(トップ100)
	
	s.selectedMaladyCategory = ko.observable();		// 選択された疾患カテゴリ
	s.selectedPrefecture = ko.observable();				// 選択された都道府県
	s.selectedYear = ko.observable();
	s.currentMaladyCategory = ko.observable();
	
	// 選択された疾患カテゴリと都道府県から、病院一覧を検索する。
	s.search = function(){
		<?php if(empty($isPremiumUser)): ?>
		// Redirects non premium user
		if(s.selectedYear().id != s.years[0].id)
			location.href = premiumContentUrl;
		<?php endif; ?>
		
		s.currentMaladyCategory(s.selectedMaladyCategory());
		$.postJSON({
			url: getHospitalsByMaladyUrl,
			data: {
				maladyId: s.selectedMaladyCategory().id,
				prefectureId: s.selectedPrefecture().id,
				year: s.selectedYear().id
			}
		}).done(function(data){
			//s.hospitals(data.hospitals);
			var hospitals = [];
			for(var n=0; n<data.hospitals.length; n++){
				var h = data.hospitals[n];
				hospitals.push(new Hospital(h));
			}
			s.hospitals(hospitals);
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
<div class="box">
	<h2 data-bind="if: currentMaladyCategory()"><span data-bind="text: currentMaladyCategory().name"></span>の病院ランキング</h2>
	<div class="content">
		<select data-bind="options: maladyCategories, optionsText: 'fmName', value: selectedMaladyCategory"></select>
		<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
		<span class="premium">会計年度：</span>
		<select data-bind="options: years, optionsText: 'name', value: selectedYear"></select>
		<button data-bind="click: search">他の疾患・地域に変更する</button>
	</div>
</div>

<!-- Head -->

<!-- Data -->
<table class="maladylist" border="1" bordercolor="#CCC">
	<thead>
		<tr><th>順位</th><th>病院名</th><th>都道府県</th><th>市区町村</th><th>退院患者数</th><th>平均在院日数</th></tr>
	</thead>
	<tbody data-bind="foreach: hospitals">
		<tr>
			<td data-bind="text: $index()+1" class="ar"></td>
			<td>
				<a data-bind="text: Hospital.name, attr: { href: DetailUrl }"></a>
			</td>
			<td data-bind="text: Area.addr1"></td>
			<td data-bind="text: Area.addr2"></td>
			<td data-bind="text: addFigure(MaladyData.mcounts)" class="ar"></td>
			<td data-bind="text: addFigure(Number(MaladyData.mdays).toFixed(1))" class="ar"></td>
		</tr>
	</tbody>
</table>

<!-- Note -->
<blockquote><p>
【集計方法】<br>
2013年9月20日開催の診療報酬調査専門組織・ＤＰＣ評価分科会において報告された、平成24年度「ＤＰＣ導入の影響評価に関する調査」資料より、該当するDPCコードの患者数および平均在院日数を当社で集計しました。<br>
・元データ⇒ <a href="http://www.mhlw.go.jp/stf/shingi/0000023522.html" target="_brank">平成25年度 第7回 診療報酬調査専門組織・ＤＰＣ評価分科会 資料</a> （厚生労働省のサイトが開きます）</p>
<p>【注意事項】<br>
・患者数および平均在院日数は、2012年4月～2013年3月の12ヶ月間の集計値です。<br>
・元データは手術方式や処置内容別に細分化され、各区分の患者数が10件未満の病院の情報が公開されていないため、各病院の実際の患者数とは異なる場合があります。
</p></blockquote>
