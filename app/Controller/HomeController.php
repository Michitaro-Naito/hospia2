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
}