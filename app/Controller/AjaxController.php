<?php
/**
 * AJAXを処理するためのコントローラ。主に各ページから検索のために呼ばれる。
 */
class AjaxController extends AppController {
	public $components = array('RequestHandler', 'Data');
	
	/**
	 * 医療圏一覧を検索する。
	 */
	public function GetZones(){
		$zones = $this->Data->GetZones($_REQUEST['prefectureId']);
		$this->set('zones', $zones);
		$this->set('_serialize', array('zones'));
	}
	
	/**
	 * 医療機関一覧を検索する。
	 */
	public function GetHospitals(){
		$hospitals = $this->Data->GetHospitals($this->data['prefectureId'], $this->data['zoneId'], $this->data['hospitalName'], $this->data['displayType'], $this->data['page']);
		$this->set('count', $hospitals['count']);
		$this->set('hospitals', $hospitals['hospitals']);
		$this->set('_serialize', array('count', 'hospitals'));
	}
	
	/**
	 * 疾患カテゴリと都道府県から、その疾患で患者数が多いトップ100の医療機関一覧を検索する。
	 */
	public function GetHospitalsByMalady(){
		$hospitals = $this->Data->GetHospitalsByMalady($this->data['maladyId'], $this->data['prefectureId']);
		$this->set('hospitals', $hospitals);
		$this->set('_serialize', array('hospitals'));
	}
	
	/**
	 * 診療実績一覧を検索する。
	 */
	public function GetDpcs(){
		$dpcs = $this->Data->GetDpcs($this->data['mdcId']);
		$this->set('dpcs', $dpcs);
		$this->set('_serialize', array('dpcs'));
	}
	
	/**
	 * 手術情報を検索する。
	 * TODO: DBのworkloadが大きいため、要キャッシュ。
	 */
	public function GetWounds(){
		$wounds = $this->Data->GetWounds($this->data['dpcId'], $this->data['prefectureId']);
		$this->set('wounds', $wounds);
		$this->set('_serialize', array('wounds'));
	}
	
}