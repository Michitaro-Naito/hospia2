<?php
App::uses('Component', 'Controller');

/**
 * クッキーに記憶するデータを扱うコンポーネント。
 */
class CookieDataComponent extends Component {
	public $components = array('Cookie', 'Data');
	
	/**
	 * 閲覧した病院のデータを取得する(最新10件)
	 */
	public function GetRememberedHospitals(){
		$ids = $this->GetRememberedHospitalIds();
		return $this->Data->GetHospitalsByIds($ids);
	}
	
	/**
	 * 閲覧した病院のIDを取得する(最新10件)
	 */
	public function GetRememberedHospitalIds(){
		$recentDisplayedHospitals = $this->Cookie->read('recentDisplayedHospitals');
		if(empty($recentDisplayedHospitals)) $recentDisplayedHospitals = array();
		return $recentDisplayedHospitals;
	}
	
	/**
	 * 閲覧した病院のIDを記憶する(最新10件)
	 */
	public function RememberHospitalId($wamId){
		if(empty($wamId)) return;
		
		$recentDisplayedHospitals = $this->GetRememberedHospitalIds();
		foreach($recentDisplayedHospitals as $key => $h){
			if($h == $wamId) unset($recentDisplayedHospitals[$key]);
		}
		array_unshift($recentDisplayedHospitals, $wamId);
		while(count($recentDisplayedHospitals) > 10) array_pop($recentDisplayedHospitals);
		$this->Cookie->write('recentDisplayedHospitals', $recentDisplayedHospitals);
	}
}
