<?php
	
function _compareDistance($a, $b){
	$aval = floatval($a['Coordinate']['distance']);
	$bval = floatval($b['Coordinate']['distance']);
	if($aval == $bval) return 0;
	return $aval < $bval ? -1 : 1;
}

class AdminController extends AppController{
	public $components = array('RequestHandler', 'Auth');
	
	public function index(){
		
	}
	
	// Coordinates -> Distance
	public function distance(){
		
	}
	
	// JSON
	public function distanceProcess(){
		$page = intval($_REQUEST['page']) + 1;
		
		$this->loadModel('Coordinate');
		$rows = $this->Coordinate->find('all', array(
			'limit' => 1,
			'page' => $page
		));
		
		// Update Distance table
		$this->loadModel('Distance');
		foreach($rows as $key => &$row){
			$id = $row['Coordinate']['wam_id'];
			$srcLat = $row['Coordinate']['latitude'];
			$srcLong = $row['Coordinate']['longitude'];
			
			// Calculate distance
			$destinations = $this->Coordinate->find('all', array(
				'conditions' => array(
					'wam_id !=' => $id
				)
			));
			foreach($destinations as &$destination){
				//debug($row['Coordinate']['wam_id'].'_'.$destination['Coordinate']['wam_id']);
				$destLat = $destination['Coordinate']['latitude'];
				$destLong = $destination['Coordinate']['longitude'];
				$dist = $this->_distance($srcLat, $srcLong, $destLat, $destLong);
				$destination['Coordinate']['distance'] = $dist;
			}
			usort($destinations, "_compareDistance"/*function($a, $b){
				$aval = floatval($a['Coordinate']['distance']);
				$bval = floatval($b['Coordinate']['distance']);
				if($aval == $bval) return 0;
				return $aval < $bval ? -1 : 1;
			}*/);
			$destinations = array_slice($destinations, 0, 20);
			$ids = array();
			foreach($destinations as $destination){
				array_push($ids, $destination['Coordinate']['wam_id']);
			}
			
			// Save distance
			$this->Distance->create();
			$dat = array('Distance'=>array(
				'wam_id' => $id,
				'basic' => implode(',', $ids)
			));
			$this->Distance->save($dat);
		}
		
		$this->set('rows', $rows);
		$this->set('count', count($rows));
		//$this->set('destinations', $destinations);
		$this->set('_serialize', array('rows', 'count'));
	}
	
	public function wamToNew(){
	}
	
	// JSON
	public function wamToNewProcess(){
		$page = intval($_REQUEST['page']) + 1;//$this->request['page'];
		
		$this->loadModel('WamToNew');
		$rows = $this->WamToNew->find('all', array(
			'limit' => 10,
			'page' => $page
		));
		
		/*// Update DPC Table
		$this->loadModel('Dpc');
		foreach($rows as $key => &$row){
			if($row['WamToNew']['new_id']=='統合' || $row['WamToNew']['new_id']=='閉院'){
			}else{
				$this->Dpc->updateAll(															// update table dpc
					array('Dpc.wam_id' => $row['WamToNew']['new_id']),// set new id
					array('Dpc.wam_id' => $row['WamToNew']['wam_id'])	// which has old id
				);
			}
		}*/
		
		/*// Update Detail Table
		$this->loadModel('Detail');
		foreach($rows as $key => &$row){
			if($row['WamToNew']['new_id']=='統合' || $row['WamToNew']['new_id']=='閉院'){
			}else{
				$this->Detail->updateAll(															// update table dpc
					array('Detail.wam_id' => $row['WamToNew']['new_id']),// set new id
					array('Detail.wam_id' => $row['WamToNew']['wam_id'])	// which has old id
				);
			}
		}*/
		
		/*// Update MaladyData Table
		$this->loadModel('MaladyData');
		foreach($rows as $key => &$row){
			if($row['WamToNew']['new_id']=='統合' || $row['WamToNew']['new_id']=='閉院'){
			}else{
				$this->MaladyData->updateAll(															// update table dpc
					array('MaladyData.wam_id' => $row['WamToNew']['new_id']),// set new id
					array('MaladyData.wam_id' => $row['WamToNew']['wam_id'])	// which has old id
				);
			}
		}*/
		
		// Update ViewCount Table
		$this->loadModel('ViewCount');
		foreach($rows as $key => &$row){
			if($row['WamToNew']['new_id']=='統合' || $row['WamToNew']['new_id']=='閉院'){
			}else{
				$this->ViewCount->updateAll(															// update table dpc
					array('ViewCount.wam_id' => $row['WamToNew']['new_id']),// set new id
					array('ViewCount.wam_id' => $row['WamToNew']['wam_id'])	// which has old id
				);
			}
		}
		
		$this->set('page', $page);
		$this->set('rows', $rows);
		$this->set('count', count($rows));
		$this->set('_serialize', array('page', 'rows', 'count'));
	}
	
	/*// 距離計算プログラム（前の会社様が残されたコードの一部より、/etc/y.php）
	private function _distance($latitude0, $longitude0, $latitude, $longitude)
	{
		// ラジアンに変換
		$a_long = deg2rad(floatval($longitude0));
		$a_lati = deg2rad(floatval($latitude0));
		$b_long = deg2rad(floatval($longitude));
		$b_lati = deg2rad(floatval($latitude));
	
		$latave = ($a_lati + $b_lati) / 2;
		$latidiff = $a_lati - $b_lati;
		$longdiff = $a_long - $b_long;
	
		if (0)
		{	// 日本測地系
			// 子午線曲率半径
			$meridian	= 6334834 / sqrt(pow(1 - 0.006674 * sin($latave) * sin($latave), 3));
			// 卯酉線曲率半径
			$primevertical	= 6377397 / sqrt(1 - 0.006674 * sin($latave) * sin($latave));
		}
		else
		{	// 世界測地系
			// 子午線曲率半径
			$meridian	= 6335439 / sqrt(pow(1 - 0.006694 * sin($latave) * sin($latave), 3));
			// 卯酉線曲率半径
			$primevertical	= 6378137 / sqrt(1 - 0.006694 * sin($latave) * sin($latave));
		}
	
		// Hubenyの簡易式
		$x	= $meridian * $latidiff;
		$y	= $primevertical * cos($latave) * $longdiff;
	
		return	sqrt($x * $x + $y * $y) / 1000;
	}*/
	
	/**
	 * ２点間の直線距離を求める（Lambert-Andoyer）
	 *
	 * @param   float   $lat1       始点緯度(十進度)
	 * @param   float   $lon1       始点経度(十進度)
	 * @param   float   $lat2       終点緯度(十進度)
	 * @param   float   $lon2       終点経度(十進度)
	 * @return  float               距離（Km）
	 */
	function _distance($lat1, $lon1, $lat2, $lon2) {
		//debug($lat1.'_'.$lon1.'_'.$lat2.'_'.$lon2);
		
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
}
?>