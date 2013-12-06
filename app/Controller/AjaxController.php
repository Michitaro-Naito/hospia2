<?php
class AjaxController extends AppController {
	public $components = array('RequestHandler');
	
	public function getZones(){
		$zones = $this->_getZones($_REQUEST['prefectureId']);
		$this->set('zones', $zones);
		$this->set('_serialize', array('zones'));
	}
	
	public function getHospitals(){
		$hospitals = $this->_getHospitals($this->data['prefectureId'], $this->data['zoneId'], $this->data['hospitalName'], $this->data['displayType'], $this->data['page']);
		$this->set('data', $this->data);
		$this->set('count', $hospitals['count']);
		$this->set('hospitals', $hospitals['hospitals']);
		$this->set('areas', $hospitals['areas']);
		$this->set('_serialize', array('data', 'count', 'hospitals', 'areas'));
	}
	
	public function getDpcs(){
		$dpcs = $this->_getDpcs($this->data['mdcId']);
		$this->set('dpcs', $dpcs);
		$this->set('_serialize', array('dpcs'));
	}
	
	// DPC全国統計で、診断分類・傷病名・都道府県から手術情報を得る。
	// DBのworkloadが大きいため、要キャッシュ。
	public function getWounds(){
		$fiscalYear = $this->getFiscalYear();
		
		// 手術情報を取得
		$this->loadModel('Wound');
		$wounds = $this->Wound->find('all', array(
			'conditions'=>array(
				'Wound.fiscal_year'=>$fiscalYear,
				'Wound.dpc_cd'=>$this->data['dpcId']
			)
		));
		
		// 手術の詳細情報(患者数トップ20と在院日数トップ20)を取得
		$this->loadModel('Detail');
		foreach($wounds as &$w){
			$vs = array(
				array('name'=>'Details_Count', 'order'=>array('Detail.count'=>'desc')),	// 患者数トップ20
				array('name'=>'Details_Days', 'order'=>array('Detail.days'=>'asc'))			// 在院日数トップ20
			);
			foreach($vs as $v){
				$this->Detail->bindModel(array(
					'belongsTo'=>array(
						'Hospital'=>array(
							'className'=>'Hospital',
							'foreignKey'=>'wam_id'
						)
					)
				));
				$cond = array(
					'Detail.fiscal_year'=>$w['Wound']['fiscal_year'],
					'Detail.dpc_cd'=>$w['Wound']['dpc_cd'],
					'Detail.jutu_cd'=>$w['Wound']['jutu_cd']
				);
				if(!empty($this->data['prefectureId'])) $cond['Hospital.addr1_cd'] = $this->data['prefectureId'];
				$details = $this->Detail->find('all', array(
					'conditions'=>$cond,
					'order'=>$v['order'],
					'limit'=>20
				));
				$w[$v['name']] = $details;
			}
		}
		
		$this->set('wounds', $wounds);
		$this->set('_serialize', array('wounds'));
	}
	
}