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
	 * 医療機関一覧を検索する。（比較リスト）
	 */
	public function GetComparableHospitals(){
		$hospitals = $this->Data->GetComparableHospitals($this->data['wamId'], $this->data['ctgry'], $this->data['mdcId'], $this->data['clst']);
		$this->set('hospitals', $hospitals);
		$this->set('_serialize', array('hospitals'));
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
		$dpcs = $this->Data->GetDpcs($this->data['mdcId'], true);
		$this->set('dpcs', $dpcs);
		$this->set('_serialize', array('dpcs'));
	}
	
	public function GetDpcsByIdsAndMdc(){
		$dpcs = $this->Data->GetDpcsByIdsAndMdc($this->data['ids'], $this->data['mdcId']);
		$this->set('dpcs', $dpcs);
		$this->set('_serialize', array('dpcs'));
	}
	
	public function GetDpcsByWamIdAndYear(){
		$dpcs = $this->Data->GetDpcsByWamIdAndYear($this->data['wamId'], $this->data['year']);
		$this->set('dpcs', $dpcs);
		$this->set('_serialize', array('dpcs'));
	}
	
	/**
	 * Dpcテーブルから、医療機関IDと会計年度を指定して診療実績を取得する。
	 */
	public function GetDpcsByHospitalIdAndFiscalYear(){
		$dpcs = $this->Data->GetDpcsByHospitalIdAndFiscalYear($this->data['wamId'], $this->data['fiscalYear']);
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
	
	/**
	 * インセンティブの取得を試みる。
	 */
	public function GetInsentive(){
		$success = false;
		$hours = 0;
		$nextUntil = '';
		if($this->Auth->loggedIn()){
			
			// Gets User
			$this->loadModel('User');
			$this->User->bindModel(array(
				'hasMany'=>array(
					'Subscription'
				)
			));
			$user = $this->User->find('first', array(
				'conditions'=>array(
					'User.username'=>$this->Auth->user('username')
				)
			));
			
			// Gets Settings
			$this->loadModel('Settings');
			$settings = $this->Settings->find('first');
			
			if(
				!empty($user)																	// User exits
				&& !empty($settings)													// Settings exists
				&& $settings['Settings']['insentive_active']	// Insentive is active
				&& empty($user['Subscription'])								// User is not paid
				&& $user['User']['insentive_count'] < $settings['Settings']['insentive_max_count']	// Limit not exceeded
			){
				// Extends User.insentive_until
				$now = new DateTime();
				$until = new DateTime($user['User']['insentive_until']);
				$hours = $settings['Settings']['insentive_hours'];
				if($until > $now)
					$nextUntil = $until->modify("+{$hours} hours")->format('Y-m-d H:i:s');
				else
					$nextUntil = $now->modify("+{$hours} hours")->format('Y-m-d H:i:s');
				$user['User']['insentive_until'] = $nextUntil;
				$user['User']['insentive_count']++;
				if($this->User->save($user))
					$success = true;
			}
		}
		
		// Checks again that this User is Premium
		$this->IsPremiumUser(true);
		
		// Returns Results
		$dt = new DateTime($user['User']['insentive_until']);
		$this->set('result', array(
			'success'=>$success,
			'hours'=>$hours,
			'until'=>$dt->format('Y年m月d日 H:i'),
			'count'=>$user['User']['insentive_count'],
			'max'=>$settings['Settings']['insentive_max_count']
		));
		$this->set('_serialize', array('result'));
	}
}