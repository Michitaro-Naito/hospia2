<?php
App::uses('Component', 'Controller');

/**
 * データを扱うコンポーネント。データベースや設定ファイルからデータを取得し、指定の形式で返す処理はこちらへ集約する。
 * @example $prefectures = $this->Data->GetPrefectures();
 */
class DataComponent extends Component {
	
	/**
	 * MdcDpcテーブルから診療実績ID一覧を検索取得する。
	 */
	public function GetDpcs($mdcId){
		$this->MdcDpc = ClassRegistry::init('MdcDpc');
		$dpcs = $this->MdcDpc->find('all', array(
			'conditions'=>array(
				'MdcDpc.mdc_cd'=>$mdcId
			)
		));
		return $dpcs;
	}
	
	/**
	 * 設定ファイルから表示区分一覧を取得する。
	 */
	public function GetDisplayTypes(){
		$types = array(
			0 => array(),
			1 => $this->GetMdcs()
		);
		foreach (Configure::read('basic') as $key => $value) {
			array_push($types[0], array('id'=>$key, 'name'=>$value));
		}
		return $types;
	}
	
	/**
	 * Dpcテーブルから会計年度を取得する。
	 */
	public function GetFiscalYear(){
		$this->Dpc = ClassRegistry::init('Dpc');
		$dpc = $this->Dpc->find('first', array(
			'fields'=>array('max(Dpc.fiscal_year) as max')
		));
		return $dpc[0]['max'];
	}
	
	/**
	 * Hospital, Dpc, Jcqhc, Areaテーブルから医療機関一覧を検索取得する。
	 */
	public function GetHospitals($prefectureId, $zoneId, $hospitalName, $orderBy, $page){
		$this->Hospital = ClassRegistry::init('Hospital');
		
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
		$this->Area = ClassRegistry::init('Area');
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
		$this->Jcqhc = ClassRegistry::init('Jcqhc');
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

	/**
	 * 疾患カテゴリと都道府県から、その疾患で患者数が多いトップ100の医療機関一覧を検索する。
	 */
	public function GetHospitalsByMalady($maladyId, $prefectureId){
		$fiscalYear = $this->GetFiscalYear();
		
		// トップ100のMaladyDataを取得
		$this->MaladyData = ClassRegistry::init('MaladyData');
		$cond = array(
			'MaladyData.upyear'=>$fiscalYear,
			'MaladyData.mcatid'=>$maladyId
		);
		if(!empty($prefectureId))
			$cond['addr1_cd'] = $prefectureId;
		$rows = $this->MaladyData->find('all', array(
			'conditions'=>$cond,
			'order'=>array('MaladyData.mcounts'=>'desc'),
			'limit'=>100
		));
		
		// 後からHospitalとAreaを結合（パフォーマンスのため）
		$ids = array();
		foreach($rows as $row){
			array_push($ids, $row['MaladyData']['wam_id']);
		}
		$this->Hospital = ClassRegistry::init('Hospital');
		$this->Hospital->bindModel(array(
			'belongsTo'=>array(
				'Area' => array(
					'className'=>'Area',
					'foreignKey'=>'addr2_cd'
				)
			)
		));
		$hospitals = $this->Hospital->find('all', array(
			'conditions'=>array(
				'Hospital.wam_id'=>$ids
			)
		));
		foreach($rows as &$row){
			foreach($hospitals as $h){
				if($h['Hospital']['wam_id'] == $row['MaladyData']['wam_id']){
					$row['Hospital'] = $h['Hospital'];
					$row['Area'] = $h['Area'];
					break;
				}
			}
		}
		
		return $rows;
	}

	/**
	 * MaladyCateテーブルから疾患カテゴリ一覧を取得する。
	 */
	public function GetMaladyCategories(){
		$this->MaladyCat = ClassRegistry::init('MaladyCat');
		$rows = $this->MaladyCat->find('all', array(
			'order'=>array('MaladyCat.mcatid'=>'asc')
		));
		$maladyCategories = array();
		foreach($rows as $row){
			$id = $row['MaladyCat']['mcatid'];
			$name = $row['MaladyCat']['mname'];
			array_push($maladyCategories, array('id'=>$id, 'name'=>$name));
		}
		return $maladyCategories;
	}
	
	/**
	 * 設定ファイルから診断分類一覧を取得する。
	 */
	public function GetMdcs(){
		$mdcs = array();
		foreach (Configure::read('mdc') as $key => $value){
			array_push($mdcs, array('id'=>$key, 'name'=>$value));
		}
		return $mdcs;
	}
	
	/**
	 * Areaテーブルから都道府県一覧を取得する。
	 */
	public function GetPrefectures(){
		$this->Area = ClassRegistry::init('Area');
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
	
	/**
	 * Wound, Detail, Hospitalテーブルから詳細な手術情報を検索取得する。
	 */
	public function GetWounds($dpcId, $prefectureId){
		$fiscalYear = $this->GetFiscalYear();
		
		// 手術情報を取得
		$this->Wound = ClassRegistry::init('Wound');
		$wounds = $this->Wound->find('all', array(
			'conditions'=>array(
				'Wound.fiscal_year'=>$fiscalYear,
				'Wound.dpc_cd'=>$dpcId
			)
		));
		
		// 手術の詳細情報(患者数トップ20と在院日数トップ20)を取得
		$this->Detail = ClassRegistry::init('Detail');
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
				if(!empty($prefectureId)) $cond['Hospital.addr1_cd'] = $prefectureId;
				$details = $this->Detail->find('all', array(
					'conditions'=>$cond,
					'order'=>$v['order'],
					'limit'=>20
				));
				$w[$v['name']] = $details;
			}
		}
		return $wounds;
	}
	
	/**
	 * Areaテーブルから医療圏一覧を検索取得する。
	 */
	public function GetZones($prefectureId){
		$this->Area = ClassRegistry::init('Area');
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
}
