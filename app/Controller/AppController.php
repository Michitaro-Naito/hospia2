<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public function beforeFilter(){
		$this->disableCache();
	}
	
	public function getFiscalYear(){
		$this->loadModel('Dpc');
		$dpc = $this->Dpc->find('first', array(
			'fields'=>array('max(Dpc.fiscal_year) as max')
		));
		return $dpc[0]['max'];
	}
	
	/**
	 * Gets prefectures from area table.
	 */
	public function _getPrefectures(){
		$this->loadModel('Area');
		$rows = $this->Area->find('all', array(
			'fields'=>array('Area.addr1_cd', 'Area.addr1'),
			'order'=>'Area.addr1_cd',
			'group'=>'Area.addr1_cd'
		));
		$prefs = array();
		array_push($prefs, array('id'=>null, 'name'=>null));
		foreach($rows as $row){
			array_push($prefs, array('id'=>$row['Area']['addr1_cd'], 'name'=>$row['Area']['addr1']));
		}
		return $prefs;
	}
	
	public function _getZones($prefectureId){
		$this->loadModel('Area');
		$rows = $this->Area->find('all', array(
			'conditions'=>array(
				'Area.addr1_cd'=>$prefectureId
			),
			'fields'=>array('Area.zone_cd', 'Area.zone2nd'),
			'order'=>'Area.zone_cd',
			'group'=>'Area.zone_cd'
		));
		$zones = array();
		array_push($zones, array('id'=>null, 'name'=>null));
		foreach($rows as $row){
			array_push($zones, array('id'=>$row['Area']['zone_cd'], 'name'=>$row['Area']['zone2nd']));
		}
		return $zones;
	}
	
	public function getMdcs(){
		$mdcs = array();
		foreach (Configure::read('mdc') as $key => $value){
			array_push($mdcs, array('id'=>$key, 'name'=>$value));
		}
		return $mdcs;
	}
	
	public function _getDpcs($mdcId){
		$this->loadModel('MdcDpc');
		$dpcs = $this->MdcDpc->find('all', array(
			'conditions'=>array(
				'MdcDpc.mdc_cd'=>$mdcId
			)
		));
		return $dpcs;
	}
	
	public function _getDisplayTypes(){
		$types = array(
			0 => array(),
			1 => $this->getMdcs()
		);
		foreach (Configure::read('basic') as $key => $value) {
			array_push($types[0], array('id'=>$key, 'name'=>$value));
		}
		return $types;
	}
	
	// 病院を検索して結果を返す
	public function _getHospitals($prefectureId, $zoneId, $hospitalName, $orderBy, $page){
		$this->loadModel('Hospital');
		
		$cond = array();
		if(!empty($prefectureId)) $cond['Hospital.addr1_cd'] = $prefectureId;
		if(!empty($zoneId)) $cond['Hospital.zone_cd'] = $zoneId;
		if(!empty($hospitalName)) $cond['Hospital.name like'] = '%'.$hospitalName.'%';
		
		$order = array();
		switch($orderBy){
			// 一般
			case 'bed':
				$order = array('Hospital.bed'=>'desc');
				break;
			case 'general':
				$order = array('Hospital.general'=>'desc');
				break;
			case 'doctor':
				$order = array('Hospital.doctor'=>'desc');
				break;
			case 'nurse':
				$order = array('Hospital.nurse'=>'desc');
				break;
				
			// 診断分類別
			default:
				$mdc_cd = intval($orderBy);
				$order = array('Dpc.ave_month'=>'desc');
				break;
		}
		
		$count = $this->Hospital->find('count', array(
			'conditions'=>$cond
		));
		
		if(isset($mdc_cd)){
			$this->Hospital->bindModel(array(
				'hasOne'=>array(
					'Dpc'=>array(
						'className'=>'Dpc',
						'foreignKey'=>'wam_id',
						'conditions'=>array(
							'Dpc.mdc_cd'=>$mdc_cd,
							'Dpc.fiscal_year'=>$this->getFiscalYear()//2012
						)
					)
				)
			));
		}
		
		// 病院を取得
		$hospitals = $this->Hospital->find('all', array(
			'conditions'=>$cond,
			'order'=>$order,
			'limit'=>20,
			'offset'=> 20 * max(0, intval($page)-1)
		));
		
		// 後から病院のAreaをjoin（パフォーマンスのため）
		$this->loadModel('Area');
		$codes = array();
		foreach($hospitals as $hospital){
			array_push($codes, $hospital['Hospital']['addr2_cd']);
		}
		$areas = $this->Area->find('all', array(
			'conditions'=>array(
				'Area.addr2_cd'=>$codes
			)
		));
		// hospitalsへ結合
		foreach($hospitals as &$hospital){
			foreach($areas as $area){
				if($area['Area']['addr2_cd'] == $hospital['Hospital']['addr2_cd']){
					$hospital['Area'] = $area['Area'];
					break;
				}
			}
		}
		
		// 後から病院のJcqhcをjoin（パフォーマンスのため）
		$this->loadModel('Jcqhc');
		$wamIds = array();
		foreach($hospitals as $h){
			array_push($wamIds, $h['Hospital']['wam_id']);
		}
		$jcqhcs = $this->Jcqhc->find('all', array(
			'conditions'=>array(
				'Jcqhc.wam_id'=>$wamIds
			)
		));
		foreach($hospitals as &$h){
			foreach($jcqhcs as $j){
				if($j['Jcqhc']['wam_id'] == $h['Hospital']['wam_id']){
					$h['Jcqhc'] = $j['Jcqhc'];
					break;
				}
			}
		}
		
		return array(
			'areas'=>$areas,
			'count'=>$count,
			'hospitals'=>$hospitals
		);
	}
}
