<?php
App::uses('Component', 'Controller');

/**
 * 2つの病院を比較する。(ソート用)
 */
function _CompareHospitalsByDistance($h1, $h2){
	$d1 = $h1['Hospital']['distance'];
	$d2 = $h2['Hospital']['distance'];
	if($d1 < $d2) return -1;
	if($d1 == $d2) return 0;
	return 1;
}
function _CompareHospitalsByGeneral($h1, $h2){
	// 一般病床数で比較(Hospital.general)
	$p1 = $h1['Hospital']['patient'];
	$p2 = $h2['Hospital']['patient'];
	if($p1 > $p2) return -1;
	if($p1 == $p2) return 0;
	return 1;
}
function _CompareHospitalsByDpcPatient($h1, $h2){
	// DPCの患者数で比較(Dpc.ave_month)
	$p1 = $h1['Dpc']['ave_month'];
	$p2 = $h2['Dpc']['ave_month'];
	if($p1 > $p2) return -1;
	if($p1 == $p2) return 0;
	return 1;
}

/**
 * データを扱うコンポーネント。データベースや設定ファイルからデータを取得し、指定の形式で返す処理はこちらへ集約する。
 * @example $prefectures = $this->Data->GetPrefectures();
 */
class DataComponent extends Component {
	
	/**
	 * 2つの病院を比較する。(ソート用)
	 */
	private $_ids = array();
	public function _CompareHospitalsByIds($h1, $h2){
		$a = array_search($h1['Hospital']['wam_id'], $this->_ids);
		$b = array_search($h2['Hospital']['wam_id'], $this->_ids);
		if($a !== FALSE && $b === FALSE) return -1;
		if($a === FALSE && $b !== FALSE) return 1;
		if($b === FALSE && $b === FALSE) return 0;
		
		if($a < $b) return -1;
		if($a > $b) return 1;
		return 0;
	}
	
	/**
	 * MdcDpcテーブルから診療実績ID一覧を検索取得する。
	 */
	public function GetDpcs($mdcId, $excludeFirst=false){
		$this->MdcDpc = ClassRegistry::init('MdcDpc');
		$dpcs = $this->MdcDpc->find('all', array(
			'conditions'=>array(
				'MdcDpc.mdc_cd'=>$mdcId
			)
		));
		if($excludeFirst && count($dpcs)>0)
			array_shift($dpcs);
		return $dpcs;
	}
	
	/**
	 * Dpcテーブルから指定された複数の病院の診療実績を取得する。
	 */
	public function GetDpcsByIdsAndMdc($ids, $mdcId){
		$maxFiscalYear = $this->GetFiscalYear();
		$minFiscalYear = $maxFiscalYear-6;
		$this->Dpc = ClassRegistry::init('Dpc');
		$dpcs = $this->Dpc->find('all', array(
			'conditions'=>array(
				'Dpc.wam_id'=>$ids,
				'Dpc.mdc_cd'=>$mdcId,
				'Dpc.fiscal_year >='=>$minFiscalYear,
				'Dpc.fiscal_year <='=>$maxFiscalYear,
			)
		));
		$data = array();
		$displayTypesForDpc = $this->GetDisplayTypesForDpc();
		for($year=$minFiscalYear; $year<=$maxFiscalYear; $year++){
			/*$data[$year] = array(
				'year'=>$year
			);*/
			array_push($data, array('year'=>$year));
		}
		foreach($dpcs as $d){
			foreach($displayTypesForDpc as $type){
				$data[intval($d['Dpc']['fiscal_year'])-$minFiscalYear][$d['Dpc']['wam_id'].'.'.$type['id']] = $d['Dpc'][$type['id']];
			}
		}
		return $data;
	}

	/**
	 * Dpcテーブルから、医療機関IDと会計年度を指定して診療実績を取得する。
	 */
	public function GetDpcsByHospitalIdAndFiscalYear($wamId, $fiscalYear){
		$this->Dpc = ClassRegistry::init('Dpc');
		$dpcs = $this->Dpc->find('all', array(
			'conditions'=>array(
				'Dpc.wam_id'=>$wamId,
				'Dpc.fiscal_year'=>$fiscalYear
			),
			'order'=>array('Dpc.mdc_cd'=>'asc')
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

	public function GetDisplayTypesForBasic(){
		$types = array();
		foreach(Configure::read('basic') as $key => $value){
			array_push($types, array('id'=>$key, 'name'=>$value));
		}
		return $types;
	}

	/**
	 * 設定ファイルから、診療実績の並び替え方法一覧を取得する。
	 */
	public function GetDisplayTypesForDpc(){
		$types = array();
		foreach(Configure::read('dpc') as $key => $value){
			array_push($types, array('id'=>$key, 'name'=>$value));
		}
		return $types;
	}

	/**
	 * 設定ファイルから、表示切替：比較区分を取得する。
	 */
	public function GetComparisonCategories(){
		$types = array();
		foreach(Configure::read('ctgry') as $key => $value){
			array_push($types, array('id'=>$key, 'name'=>$value));
		}
		return $types;
	}

	/**
	 * 設定ファイルから、表示切替：比較リストを取得する。
	 */
	public function GetDisplayTypesForHoscmp(){
		$types = array();
		foreach(Configure::read('clst') as $key => $value){
			array_push($types, array('id'=>$key, 'name'=>$value));
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
	 * Dpcテーブルから、選択可能な会計年度の一覧を取得する。
	 */
	public function GetFiscalYears(){
		$this->Dpc = ClassRegistry::init('Dpc');
		$dpc = $this->Dpc->find('first', array(
			'fields'=>array(
				'min(Dpc.fiscal_year) as min',
				'max(Dpc.fiscal_year) as max'
			)
		));
		$min = $dpc[0]['min'];
		$max = $dpc[0]['max'];
		$fiscalYears = array();
		for($n=$max; $n>=$min; $n--){
			array_push($fiscalYears, array('id'=>$n, 'name'=>'平成'.($n-1988).'年'));
		}
		return $fiscalYears;
	}

	/**
	 * Hospital, Jcqhcテーブルから医療機関情報を1件取得する。
	 * @param int wamId 医療機関ID
	 * @return mixed 医療機関情報。見つからない場合はnull。
	 */
	public function GetHospital($wamId){
		$this->Hospital = ClassRegistry::init('Hospital');
		$this->Hospital->bindModel(array(
			'hasOne'=>array(
				'Coordinate'=>array(
					'foreignKey'=>'wam_id'
				),
				'Jcqhc'=>array(
					'foreignKey'=>'wam_id'
				)
			)
		));
		$hospital = $this->Hospital->find('first', array(
			'conditions'=>array(
				'Hospital.wam_id' => $wamId
			)
		));
		if(empty($hospital)) return null;
		return $hospital;
	}
	
	/**
	 * 過去7年間のDPCを全て含めて病院のデータを取得する。
	 * Hospital has many DPCs
	 */
	public function GetHospitalWithDpcs($wamId){
		$maxFiscalYear = $this->GetFiscalYear();
		$minFiscalYear = $maxFiscalYear - 6;
		$this->Hospital = ClassRegistry::init('Hospital');
		$this->Hospital->bindModel(array(
			'hasMany'=>array(
				'Dpc'=>array(
					'foreignKey'=>'wam_id',
					'conditions'=>array(
						'fiscal_year <='=>$maxFiscalYear,
						'fiscal_year >='=>$minFiscalYear
					)
				)
			)
		));
		$hospital = $this->Hospital->find('first', array(
			'conditions'=>array(
				'Hospital.wam_id'=>$wamId
			)
		));
		$hospital['MinFiscalYear'] = intval($minFiscalYear);
		$hospital['MaxFiscalYear'] = intval($maxFiscalYear);
		return $hospital;
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
	 * IDを指定して病院を取得する。
	 * @param ids wam_idの配列。
	 */
	public function GetHospitalsByIds($ids){
		$this->Hospital = ClassRegistry::init('Hospital');
		$hospitals = $this->Hospital->find('all', array(
			'conditions'=>array(
				'Hospital.wam_id'=>$ids
			)
		));
		// Sort hospitals by ids
		usort($hospitals, array($this, '_CompareHospitalsByIds'));
		return $hospitals;
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
	 * 全ての訪問者に最もよく閲覧される病院を取得する。(1ヶ月以内のトップ10)
	 */
	public function GetHospitalsMostViewed(){
		$this->ViewCount = ClassRegistry::init('ViewCount');
		$since = new DateTime();
		$since->modify('-1 month');
		$counts = $this->ViewCount->find('all', array(
			'conditions'=>array(
				'ViewCount.ymd >=' => $since->format('Y-m-d')
			),
			'fields'=>array(
				'ViewCount.wam_id',
				'sum(ViewCount.cnt) as sum'
			),
			'group'=>array('ViewCount.wam_id'),
			'order'=>array('sum'=>'desc'),
			'limit'=>10
		));
		$ids = array();
		foreach($counts as $c){
			array_push($ids, $c['ViewCount']['wam_id']);
		}
		$hospitals = $this->GetHospitalsByIds($ids);
		foreach($counts as &$c){
			foreach($hospitals as $h){
				if($c['ViewCount']['wam_id'] == $h['Hospital']['wam_id']){
					$c['Hospital'] = $h['Hospital'];
				}
			}
		}
		return $counts;
	}

	/**
	 * 付近の医療機関を20件取得する。（その際、正確な距離も計算して近い順にソートする。）
	 * @param src 医療機関。この周辺を検索する。
	 */
	public function GetHospitalsNearby($src){
		// 付近の医療機関のIDを取得
		$this->Distance = ClassRegistry::init('Distance');
		$distance = $this->Distance->find('first', array(
			'conditions'=>array(
				'Distance.wam_id'=>$src['Hospital']['wam_id']
			)
		));
		$ids = split(',', $distance['Distance']['basic']);

		// IDから医療機関情報を取得
		$this->Hospital = ClassRegistry::init('Hospital');
		$this->Hospital->bindModel(array(
			'hasOne'=>array(
				'Coordinate'=>array(
					'foreignKey'=>'wam_id'
				)
			)
		));
		$hospitals = $this->Hospital->find('all', array(
			'conditions'=>array(
				'Hospital.wam_id'=>$ids
			)
		));

		// 距離を計算して格納する
		foreach($hospitals as &$h){
			$h['Hospital']['distance'] = $this->GetDistance(
				$src['Coordinate']['latitude'], $src['Coordinate']['longitude'],
				$h['Coordinate']['latitude'], $h['Coordinate']['longitude']
			);
		}

		// 距離が短い順にソートする
		usort($hospitals, '_CompareHospitalsByDistance');

		return $hospitals;
	}

	/**
	 * 医療機関一覧を検索取得する。（比較リスト）
	 */
	public function GetComparableHospitals($wamId, $ctgry, $mdcId, $clst){
		$this->Hospital = ClassRegistry::init('Hospital');
		$fiscalYear = $this->GetFiscalYear();

		// 閲覧中の病院を取得
		$this->Hospital->bindModel(array(
			'hasOne'=>array(
				'Coordinate'=>array(
					'foreignKey'=>'wam_id'
				)
			)
		));
		$src = $this->Hospital->find('first', array(
			'conditions'=>array(
				'Hospital.wam_id'=>$wamId
			)
		));

		if($clst==='distance'){
			// 距離が近い病院を取得
			$this->Distance = ClassRegistry::init('Distance');
			$distance = $this->Distance->find('first', array(
				'conditions'=>array(
					'Distance.wam_id'=>$wamId
				)
			));
			$ids = split(',', $distance['Distance']['basic']);
			$this->Hospital->bindModel(array(
				'hasOne'=>array(
					'Coordinate'=>array(
						'foreignKey'=>'wam_id'
					),
					'Dpc'=>array(
						'foreignKey'=>'wam_id',
						'conditions'=>array(
							'Dpc.mdc_cd'=>$mdcId,
							'Dpc.fiscal_year'=>$fiscalYear
						)
					)
				)
			));
			$hospitals = $this->Hospital->find('all', array(
				'conditions'=>array(
					'Hospital.wam_id'=>$ids
				)
			));

			// 距離を計算して格納する
			foreach($hospitals as &$h){
				$h['Hospital']['distance'] = $this->GetDistance(
					$src['Coordinate']['latitude'], $src['Coordinate']['longitude'],
					$h['Coordinate']['latitude'], $h['Coordinate']['longitude']
				);
			}

			// 距離が短い順にソートする
			usort($hospitals, '_CompareHospitalsByDistance');

			// 比較区分が診療実績の場合は、診療実績を結合する。（パフォーマンスのため後から）

			return $hospitals;

		}else{
			// 患者数が多い病院（基本情報が選択されている場合はHospital.general(一般病床数)、DPCが選択されている場合はDpc.ave_month
			//$order = array('Hospital.general'=>'desc');
			//if($ctgry=='dpc') $order = array('Dpc.ave_month'=>'desc');
			$mdcId = 0;		// 基本情報が選択されている場合はmdcId=0に固定
			$order = array('Dpc.ave_month'=>'desc');
			$this->Hospital->bindModel(array(
				'hasOne'=>array(
					'Coordinate'=>array(
						'foreignKey'=>'wam_id'
					),
					'Dpc'=>array(
						'foreignKey'=>'wam_id',
						'conditions'=>array(
							'Dpc.mdc_cd'=>$mdcId,
							'Dpc.fiscal_year'=>$fiscalYear
						)
					)
				)
			));
			$hospitals = $this->Hospital->find('all', array(
				'conditions'=>array(
				),
				'order'=>$order,
				'limit'=>20
			));

			// 距離を計算して格納する
			foreach($hospitals as &$h){
				$h['Hospital']['distance'] = $this->GetDistance(
					$src['Coordinate']['latitude'], $src['Coordinate']['longitude'],
					$h['Coordinate']['latitude'], $h['Coordinate']['longitude']
				);
			}

			// 患者数が多い順にソートする
			// $ctgry == 'basic'の場合は既にソートされている。
			if($ctgry == 'dpc') usort($hospitals, '_CompareHospitalsByDpcPatient');

			return $hospitals;
		}
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
	 * @param bool excludeFirst 最初の要素を取り除くか。
	 */
	public function GetMdcs($excludeFirst=false){
		$mdcs = array();
		$first = true;
		foreach (Configure::read('mdc') as $key => $value){
			if($excludeFirst && $first){
				$first = false;
				continue;
			}
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

	/**
	 * ２点間の直線距離を求める（Lambert-Andoyer）
	 *
	 * @param   float   $lat1       始点緯度(十進度)
	 * @param   float   $lon1       始点経度(十進度)
	 * @param   float   $lat2       終点緯度(十進度)
	 * @param   float   $lon2       終点経度(十進度)
	 * @return  float               距離（Km）
	 */
	function GetDistance($lat1, $lon1, $lat2, $lon2) {
		if($lat1==$lat2 && $lon1==$lon2) return 0.0;
	    // WGS84
	    $A = 6378137.0;             // 赤道半径
	    $F = 1 / 298.257222101;     // 扁平率

	    // 扁平率 F = (A - B) / A
	    $B = $A * (1.0 - $F);       // 極半径

	    $lat1 = deg2rad($lat1);
	    $lon1 = deg2rad($lon1);
	    $lat2 = deg2rad($lat2);
	    $lon2 = deg2rad($lon2);

	    $P1 = atan($B/$A) * tan($lat1);
	    $P2 = atan($B/$A) * tan($lat2);

	    // Spherical Distance
	    $sd = acos(sin($P1)*sin($P2) + cos($P1)*cos($P2)*cos($lon1-$lon2));

	    // Lambert-Andoyer Correction
	    $cos_sd = cos($sd/2);
	    $sin_sd = sin($sd/2);
	    $c = (sin($sd) - $sd) * pow(sin($P1)+sin($P2),2) / $cos_sd / $cos_sd;
	    $s = (sin($sd) + $sd) * pow(sin($P1)-sin($P2),2) / $sin_sd / $sin_sd;
	    $delta = $F / 8.0 * ($c - $s);

	    // Geodetic Distance
	    $distance = $A * ($sd + $delta);

	    return $distance / 1000.0;
	}

	/**
	 * 設定ファイルから比較指数一覧を取得する。
	 */
	public function GetCmplst(){
		$cmplst = array();
		foreach (Configure::read('cmplst') as $key => $value){
			array_push($cmplst, array('id'=>$key, 'name'=>$value));
		}
		return $cmplst;
	}

	/**
	 * 診断分類と都道府県から医療機関一覧を検索取得する。
	 */
	public function GetHospitalsByMdcAndPrefecture($mdcId, $prefectureId){

		// 医療機関一覧用のModelを取得する
		$this->Hospital = ClassRegistry::init('Hospital');

		// 医療機関の検索条件を設定する
		$cond = array();
		if(!empty($prefectureId)) $cond['Hospital.addr1_cd'] = $prefectureId;

		// 診療実績の結合条件を設定する
		$condDpc = array();
		if(isset($mdcId)) $condDpc['Dpc.mdc_cd'] = $mdcId;
		$condDpc['Dpc.fiscal_year'] = $this->getFiscalYear(); //2012

		// 並び順を設定する
		$order = array('Dpc.ave_month'=>'desc');

		// 医療機関一覧と診療実績を結合する
		$this->Hospital->bindModel(array(
			'hasOne'=>array(
				'Dpc'=>array(
					'className'=>'Dpc',
					'foreignKey'=>'wam_id',
					'type'=>'inner',
					'conditions'=>$condDpc
				)
			)
		));

		// 病院を取得
		$hospitals = $this->Hospital->find('all', array(
			'conditions'=>$cond,
			'order'=>$order,
			'limit'=>100
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

		// 取得した医療機関一覧を呼び出し元に返す
		return $hospitals;
	}

	/**
	 * ViewCountを1増やす。
	 */
	public function IncrementViewCount($wamId){
		$this->ViewCount = ClassRegistry::init('ViewCount');
		$now = new DateTime();
		$nowString = $now->format('Y-m-d');
		$query = 'update viewcnt set cnt = cnt + 1 where wam_id = ' . intval($wamId) . ' and ymd = "' . $nowString . '"';
		$this->ViewCount->query($query);
		if($this->ViewCount->getAffectedRows() === 0){
			// No row is affected. It's a first visit of a day. Insert a row.
			try{
				$this->ViewCount->create(array(
					'wam_id' => $wamId,
					'ymd' => $nowString,
					'cnt' => 1
				));
				$this->ViewCount->save();
			}catch(PDOException $e){
				// Insert failed. Just try to update once more.
				$this->ViewCount->query($query);
			}
		}
	}

	/*
	 * 記事のIDを元に投稿記事のデータを検索取得する。
	 */
	public function GetPostsByPostId($postId){

		// 投稿記事用のModelを取得する
		$this->Post = ClassRegistry::init('Post');

		// 投稿記事の検索条件を設定する
		$cond = array();
		$cond['Post.status'] = 'publish';
		$cond['Post.category'] = array('topics', 'info', 'month', 'ranking');
		if(isset($postId)) $cond['Post.post_id'] = $postId;

		// 並び順を設定する
		$order = array('Post.ID'=>'asc');

		// 投稿記事を取得
		$posts = $this->Post->find('all', array(
			'conditions'=>$cond,
			'order'=>$order
		));

		// 取得した投稿記事一覧を呼び出し元に返す
		return $posts;
	}
	
	/**
	 * 最新15件の記事を取得する。(公開されているもののみ)
	 */
	public function GetRecentPosts(){
		$this->Post = ClassRegistry::init('Post');
		$posts = $this->Post->find('all', array(
			'conditions'=>array(
				'Post.status'=>'publish'
			),
			'order'=>array('Post.created'=>'desc'),
			'limit'=>15,
		));
		return $posts;
	}

	/**
	 * 記事のカテゴリを元に投稿記事のデータを検索取得する。
	 */
	public function GetPostsByCategory($category){

		// 投稿記事用のModelを取得する
		$this->Post = ClassRegistry::init('Post');

		// 投稿記事の検索条件を設定する
		$cond = array();
		$cond['Post.status'] = 'publish';
		if(isset($category)) $cond['Post.category'] = $category;

		// 並び順を設定する
		$order = array('Post.created'=>'desc');

		// 投稿記事を取得
		$posts = $this->Post->find('all', array(
			'conditions'=>$cond,
			'order'=>$order,
			'limit'=>30
		));

		// 取得した投稿記事一覧を呼び出し元に返す
		return $posts;
	}
	
	/**
	 * 指定した医療機関の最近1ヶ月間のページビュー数を取得する。
	 */
	public function GetViewCount($wamId){
		$this->ViewCount = ClassRegistry::init('ViewCount');
		$since = new DateTime();
		$since->modify('-1 month');
		$count = $this->ViewCount->find('first', array(
			'conditions'=>array(
				'ViewCount.wam_id'=>$wamId,
				'ViewCount.ymd >='=>$since->format('Y-m-d'),
			),
			'fields'=>array(
				'ViewCount.wam_id',
				'sum(ViewCount.cnt) as sum',
			),
			'group'=>array('ViewCount.wam_id'),
			'order'=>array('sum'=>'desc'),
		));
		if(empty($count))
			return 0;
		return $count[0]['sum'];
	}
}
