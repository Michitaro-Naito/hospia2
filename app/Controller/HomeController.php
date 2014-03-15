<?php
class HomeController extends AppController {
	public $components = array('CookieData', 'Data');
	public $helpers = array('Js' => array('Jquery'));
	
	/**
	 * トップページ
	 */
	public function Index() {
		$this->set('recentPosts', $this->Data->GetRecentPosts());
		$this->set('dat', array(
			'prefectures'=>$this->Data->GetPrefectures(),
			'getZonesUrl'=>Router::url('/Ajax/GetZones.json'),
			'hoslistUrl'=>Router::url('/hoslist'),
			'rememberedHospitals'=>$this->CookieData->GetRememberedHospitals(),
			'hospitalsMostViewed'=>$this->Data->GetHospitalsMostViewed(),
			'maladyCategories'=>$this->Data->GetMaladyCategories(),
			'mdcs'=>$this->Data->GetMdcs()
		));
	}

	/**
	 * 病院検索
	 */
	public function Hoslist(){
		$this->set('prefectures', $this->Data->GetPrefectures());
		$this->set('displayTypes', $this->Data->GetDisplayTypes());
	}

	/**
	 * DPC全国統計
	 */
	public function Dpc(){
		$this->set('mdcs', $this->Data->GetMdcs(true));
		$this->set('prefectures', $this->Data->GetPrefectures());
	}

	/**
	 * 患者数ランキング
	 */
	public function Toplst(){
		// 検索項目の初期値を定義する
		define("MDC_DEFAULT", "0");
		define("PREFECTURE_DEFAULT", "00");
		define("CMP_DEFAULT", "ave_month@dpc");
		// メニューの一覧に表示する項目を取得する
		$prefectures = $this->Data->GetPrefectures();
		$prefectures[0]['id'] = PREFECTURE_DEFAULT;
		$prefectures[0]['name'] = '全国';
		$this->set('prefectures', $prefectures);
		$this->set('mdcs', $this->Data->GetMdcs());
		$this->set('cmplst', $this->Data->GetCmplst());
		// 検索項目の値を初期化する
		$selectedMdc = MDC_DEFAULT;
		$selectedPrefecture = PREFECTURE_DEFAULT;
		$selectedCmp = CMP_DEFAULT;
		if ($this->request->data) {
			// フォーム送信されてきた場合は選択された値を取得する
			$selectedMdc = $this->request->data("valueMdc");
			$selectedPrefecture = $this->request->data("valuePrefecture");
			$selectedCmp = $this->request->data("valueCmp");
		}else{
			// GETパラメーターからMDCの初期値を取得する
			if(!empty($_GET['id'])) $selectedMdc = $_GET['id'];
		}
		// 医療機関検索のための検索条件を設定する
		$condMdcValue = $selectedMdc;
		$condPrefectureValue = $selectedPrefecture;
		if ($condPrefectureValue === PREFECTURE_DEFAULT) $condPrefectureValue = null;
		$hospitals = $this->Data->GetHospitalsByMdcAndPrefecture($condMdcValue, $condPrefectureValue);
		// 選択された検索項目の値をビューに反映させる
		$this->set("defMdc", $selectedMdc);
		$this->set("defPrefecture", $selectedPrefecture);
		$this->set("defCmp", $selectedCmp);
		$this->set("hospitals", $hospitals);
	}

	/**
	 * 疾患別患者数ランキング
	 */
	public function Maladylist(){
		$mdata = '';
		if(isset($_GET['mdata'])) $mdata = $_GET['mdata'];
		$this->set('maladyCategories', $this->Data->GetMaladyCategories());
		$this->set('prefectures', $this->Data->GetPrefectures());
		$this->set('defaultMaladyCategory', $mdata);
	}

	/**
	 * 病院詳細
	 * 医療機関IDと会計年度から診療実績を検索表示
	 */
	public function Hosdetail(){
		$wamId = $this->_RedirectIfOldUrl('/hosdetail/');
		$this->CookieData->RememberHospitalId($wamId);
		$this->Data->IncrementViewCount($wamId);
		$this->_GetAdditionalInformation($wamId);
		
		$this->set('dat', array(
			'wamId'=>$wamId,
			'hospital'=>$this->Data->GetHospital($wamId),
			'fiscalYears'=>$this->Data->GetFiscalYears(),
			'displayTypesForDpc'=>$this->Data->GetDisplayTypesForDpc(),
			'mdcs'=>$this->Data->GetMdcs(),
			'getDpcsUrl'=>Router::url('/ajax/getDpcsByHospitalIdAndFiscalYear.json')
		));
	}

	/**
	 * 病院比較
	 * ある医療機関と、近隣の医療機関もしくは患者数の多い医療機関を比較表示する。
	 */
	public function Hoscmp(){
		$wamId = $this->_RedirectIfOldUrl('/hoscmp/');
		$this->CookieData->RememberHospitalId($wamId);
		$this->Data->IncrementViewCount($wamId);
		$this->_GetAdditionalInformation($wamId);
		
		$this->set('dat', array(
			'wamId'=>$wamId,
			'hospital'=>$this->Data->GetHospital($wamId),
			'comparisonCategories'=>$this->Data->GetComparisonCategories(),
			'mdcs'=>$this->Data->GetMdcs(),
			'displayTypesForHoscmp'=>$this->Data->GetDisplayTypesForHoscmp(),
			'displayTypesForDpc'=>$this->Data->GetDisplayTypesForDpc(),
			'displayTypesForBasic'=>$this->Data->GetDisplayTypesForBasic(),
			'searchUrl'=>Router::url('/ajax/getComparableHospitals.json'),
			'detailUrl'=>Router::url('/hosdetail'),
		));
	}

	/**
	 * 病院基本情報
	 * ある病院の基本情報を表示する。
	 */
	public function Hosinfo(){
		$wamId = $this->_RedirectIfOldUrl('/hosinfo/');
		$this->CookieData->RememberHospitalId($wamId);
		$this->Data->IncrementViewCount($wamId);
		$this->_GetAdditionalInformation($wamId);

		$hospital = $this->Data->GetHospital($wamId);
		$this->set('dat', array(
			'wamId'=>$wamId,
			'hospital'=>$hospital,
			'hospitalsNearby'=>$this->Data->GetHospitalsNearby($hospital)
		));
	}
	
	/**
	 * 過年度比較機能
	 * Compares hospital's MDCs by Fiscal Year.
	 * Displayed as a chart.
	 * Premium User only.
	 */
	public function CompareMdcsByYear($wamId){
		if(!$this->isPremiumUser && $wamId != '1138814790')
			throw new Exception('プレミアム会員になるとこの機能にアクセスできます。');
		
		$displayTypesForDpc = $this->Data->GetDisplayTypesForDpc();
		$hospital = $this->Data->GetHospitalWithDpcs($wamId);
		$chartData = array();
		for($year = $hospital['MinFiscalYear']; $year<=$hospital['MaxFiscalYear']; $year++){
			array_push($chartData, array(
				'year'=>$year
			));
		}
		foreach($hospital['Dpc'] as $d){
			$row = &$chartData[intval($d['fiscal_year'])-$hospital['MinFiscalYear']];
			foreach($displayTypesForDpc as $type){
				$row[$d['mdc_cd'].'.'.$type['id']] = $d[$type['id']];
			}
		}
		
		//$this->set('bareLayout', true);
		$this->set('dat', array(
			'wamId'=>$wamId,
			'chartData'=>$chartData,
			'hospital'=>$hospital,
			'mdcs'=>$this->Data->GetMdcs(),
			'displayTypesForDpc'=>$displayTypesForDpc
		));
	}
	
	/**
	 * プレミアム機能(お気に入りグループ比較-折れ線グラフ)
	 * Compares hospitals in a favorite group.
	 * Displayed as a line chart.
	 * Premium User only.
	 */
	public function CompareInFavoriteGroupByYear($id){
		if(!$this->isPremiumUser && $id != '1')
			throw new Exception('プレミアム会員になるとこの機能にアクセスできます。');
		
		$displayTypesForDpc = $this->Data->GetDisplayTypesForDpc();
		$this->FavoriteHospital = ClassRegistry::init('FavoriteHospital');
		$group = $this->FavoriteHospital->read(null, $id);
		$ids = array();
		foreach($group['Hospital'] as $h){
			array_push($ids, $h['wam_id']);
		}
		$this->set('bareLayout', true);
		$this->set('dat', array(
			'id'=>$id,
			'group'=>$group,
			'ids'=>$ids,
			'mdcs'=>$this->Data->GetMdcs(),
			'displayTypesForDpc'=>$displayTypesForDpc,
			'getDpcsUrl'=>Router::url('/Ajax/GetDpcsByIdsAndMdc.json'),
		));
	}
	
	/**
	 * プレミアム機能(お気に入りグループ比較-バブルチャート)
	 * Compares hospitals in a favorite group.
	 * Displayed as a bubble chart.
	 * Premium User only.
	 */
	public function CompareInFavoriteGroupByBubbles($id){
		if(!$this->isPremiumUser && $id != '1')
			throw new Exception('プレミアム会員になるとこの機能にアクセスできます。');
		
		$displayTypesForDpc = $this->Data->GetDisplayTypesForDpc();
		$this->FavoriteHospital = ClassRegistry::init('FavoriteHospital');
		$group = $this->FavoriteHospital->read(null, $id);
		$ids = array();
		foreach($group['Hospital'] as $h){
			array_push($ids, $h['wam_id']);
		}
		$years = array();
		$max = $this->Data->GetFiscalYear();
		$min = $max - 6;
		for($year=$max; $year>=$min; $year--){
			array_push($years, array('id'=>$year, 'name'=>'平成'.($year-1988).'年度'));
		}
		$this->set('bareLayout', true);
		$this->set('dat', array(
			'id'=>$id,
			'group'=>$group,
			'ids'=>$ids,
			'mdcs'=>$this->Data->GetMdcs(),
			'years'=>$years,
			'displayTypesForDpc'=>$displayTypesForDpc,
			'getDpcsUrl'=>Router::url('/Ajax/GetDpcsByIdsAndMdc.json'),
		));
	}
	
	public function CompareMdcsByBubbles($wamId = null){
		if(!$this->isPremiumUser && $wamId != '1138814790')
			throw new Exception('プレミアム会員になるとこの機能にアクセスできます。');
		
		$years = array();
		$max = $this->Data->GetFiscalYear();
		$min = $max - 6;
		for($year=$max; $year>=$min; $year--){
			array_push($years, array('id'=>$year, 'name'=>'平成'.($year-1988).'年度'));
		}
		
		$this->set('dat', array(
			'wamId'=>$wamId,
			'hospital'=>$this->Data->GetHospitalWithDpcs($wamId),
			'mdcs'=>$this->Data->GetMdcs(),
			'years'=>$years,
			'displayTypesForDpc'=>$this->Data->GetDisplayTypesForDpc(),
			'getDpcsUrl'=>Router::url('/Ajax/GetDpcsByWamIdAndYear.json')
		));
	}
	
	public function Sitemap($page = null){
		if($page===null)
			$page = 1;
		
		// Black List
		$blacklist = array(
			'病院ニュース',
			'サイドナビ',
			'ご利用ガイド（目次）',
			'運営会社',
			'サイトマップ',
			'広告の募集について',
			'お問い合わせ',
			'投稿の募集について',
			'サイドナビゲーションスペース(ワードプレスページ内)'
		);
		
		// Gets Posts
		$this->loadModel('Post');
		$this->paginate = array(
			'fields'=>array(
				'id', 'created', 'post_id', 'status', 'category', 'title'
			),
			'conditions'=>array(
				'status' => 'publish',
				'category !=' => 'news'
			),
			'order'=>array(
				'category' => 'asc',
				//'created' => 'desc',
				//'post_id' => 'desc'
			),
			'limit' => 100,
			'page' => $page
		);
		$posts = $this->paginate('Post');
		
		// Groups Posts
		$categories = array(
			'info' => 'お知らせ',
			//'poll' => 'クイックアンケート',
			'ranking' => '各種ランキング',
			'topics' => '情報活用の視点',
			'month' => '特集',
			'news' => '病院ニュース',
			'list' => '病院リスト'
		);
		$groups = array();
		foreach($categories as $ckey => $cval){
			$groups[$ckey] = array();
			foreach($posts as $p){
				if($p['Post']['category']===$ckey)
					array_push($groups[$ckey], $p);
			}
		}
		$this->set('groups', $groups);
		$this->set('posts', $posts);
		$this->set('maladyCategories', $this->Data->GetMaladyCategories());
	}
	
	/**
	 * Gets additional information for registered User.
	 */
	private function _GetAdditionalInformation($wamId){
		if($this->Auth->loggedIn()){
			$this->set('additionalInformation', array(
				'viewCount'=>$this->Data->GetViewCount($wamId),
			));
		}
	}
	
	/**
	 * Redirects if old url specified.
	 * Old: /foo?wam_id=123
	 * New: /foo/123
	 * @param path to redirect like /foo/
	 * @return wam_id
	 */
	private function _RedirectIfOldUrl($path){
		$wamId = $this->request->params['wam_id'];
		if(empty($wamId) && !empty($_REQUEST['wam_id'])){
			$wamId = $_REQUEST['wam_id'];
			return $this->redirect($path . $wamId);
		}
		if(!empty($wamId) && preg_match('/W[0-9]{7}/', $wamId)){
			$this->loadModel('WamToNew');
			$row = $this->WamToNew->find('first', array(
				'conditions'=>array(
					'WamToNew.wam_id'=>$wamId
				)
			));
			if(!empty($row)){
				return $this->redirect($path . $row['WamToNew']['new_id']);
			}
		}
		return $wamId;
	}
}