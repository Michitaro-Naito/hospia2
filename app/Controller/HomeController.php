<?php
class HomeController extends AppController {
	public $components = array('Data');
	
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
		$this->set('dat', array(
			'wamId'=>$_REQUEST['wam_id'],
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
		$this->set('dat', array(
			'wamId'=>$_REQUEST['wam_id'],
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
		$hospital = $this->Data->GetHospital($wamId);
		$this->set('dat', array(
			'wamId'=>$wamId,
			'hospital'=>$hospital,
			'hospitalsNearby'=>$this->Data->GetHospitalsNearby($hospital)
		));
	}
}