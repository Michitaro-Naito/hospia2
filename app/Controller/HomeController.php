<?php
class HomeController extends AppController {
	public $components = array('CookieData', 'Data');

	/**
	 * トップページ
	 */
	public function Index() {
		// デバッグ
		$this->loadModel('Hospital');
		$hospitals = $this->Hospital->find('all',array(
			'limit'=>5
		));
		$this->set('hospitals', $hospitals);
		$this->set('dat', array(
			'prefectures'=>$this->Data->GetPrefectures(),
			'getZonesUrl'=>Router::url('/Ajax/GetZones.json'),
			'hoslistUrl'=>Router::url('/hoslist'),
			'rememberedHospitals'=>$this->CookieData->GetRememberedHospitals(),
			'hospitalsMostViewed'=>$this->Data->GetHospitalsMostViewed()
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
		$this->set('mdcs', $this->Data->GetMdcs());
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
		// フォーム送信されてきた場合は選択された値を取得する
		if ($this->request->data) {
			$selectedMdc = $this->request->data("valueMdc");
			$selectedPrefecture = $this->request->data("valuePrefecture");
			$selectedCmp = $this->request->data("valueCmp");
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
		$this->set('maladyCategories', $this->Data->GetMaladyCategories());
		$this->set('prefectures', $this->Data->GetPrefectures());
	}

	/**
	 * 病院詳細
	 * 医療機関IDと会計年度から診療実績を検索表示
	 */
	public function Hosdetail(){
		$wamId = $_REQUEST['wam_id'];
		$this->CookieData->RememberHospitalId($wamId);
		$this->Data->IncrementViewCount($wamId);
		
		$this->set('dat', array(
			'wamId'=>$wamId,
			'fiscalYears'=>$this->Data->GetFiscalYears(),
			'displayTypesForDpc'=>$this->Data->GetDisplayTypesForDpc(),
			'getDpcsUrl'=>Router::url('/ajax/getDpcsByHospitalIdAndFiscalYear.json')
		));
	}

	/**
	 * 病院比較
	 * ある医療機関と、近隣の医療機関もしくは患者数の多い医療機関を比較表示する。
	 */
	public function Hoscmp(){
		$wamId = $_REQUEST['wam_id'];
		$this->CookieData->RememberHospitalId($wamId);
		$this->Data->IncrementViewCount($wamId);
		
		$this->set('dat', array(
			'wamId'=>$wamId,
			'comparisonCategories'=>$this->Data->GetComparisonCategories(),
			'mdcs'=>$this->Data->GetMdcs(),
			'displayTypesForHoscmp'=>$this->Data->GetDisplayTypesForHoscmp(),
			'displayTypesForDpc'=>$this->Data->GetDisplayTypesForDpc(),
			'displayTypesForBasic'=>$this->Data->GetDisplayTypesForBasic(),
			'searchUrl'=>Router::url('/ajax/getComparableHospitals.json')
		));
	}

	/**
	 * 病院基本情報
	 * ある病院の基本情報を表示する。
	 */
	public function Hosinfo(){
		$wamId = $_REQUEST['wam_id'];
		$this->CookieData->RememberHospitalId($wamId);
		$this->Data->IncrementViewCount($wamId);
		
		$hospital = $this->Data->GetHospital($wamId);
		$this->set('dat', array(
			'wamId'=>$wamId,
			'hospital'=>$hospital,
			'hospitalsNearby'=>$this->Data->GetHospitalsNearby($hospital)
		));
	}
}