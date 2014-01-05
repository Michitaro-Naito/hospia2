<?php

class FavoriteHospital extends AppModel {
	public $actsAs = array('Containable');
	 
	public $name = 'FavoriteHospital';
	public $useTable = 'favorite_hospitals';
	
	var $hasAndBelongsToMany = array(
  		'Hospital' => array(
    		'className' => 'Hospital',
    		'joinTable' => 'favorite_hospitals_hospital',
            'foreignKey' => 'favorite_hospital_id',
            'associationForeignKey' => 'hospital_id',
    		'unique' => false,
  		)
	);
	
	public $validate = array(
        'user_id' => array(
            'rule'    => array('limitGroups', 10),
            'message' => 'You are only allowed 10 groups.'
        )
    );
	
	public function limitGroups($check, $limit) {
        // $check will have value: array('promotion_code' => 'some-value')
        // $limit will have value: 10
        $existing_groups_count = $this->find('count', array(
            'conditions' => $check
        ));
        return $existing_groups_count < $limit;
    }
    
    public function addHospital($gid, $hid) { 
    	$db = $this->getDataSource();
    	//$fh_count = $this->query("SELECT count(favorite_hospital_id) FROM favorite_hospitals_hospital WHERE favorite_hospital_id = ".$gid.";");
    	$fh_count = $db->fetchAll(
    			'SELECT count(favorite_hospital_id) from favorite_hospitals_hospital where favorite_hospital_id = ?',
    			array($gid)
		);
    	if($fh_count[0][0]['count(favorite_hospital_id)'] < 10){	
    		//$existing_association = $this->query("SELECT count(*) FROM favorite_hospitals_hospital WHERE favorite_hospital_id = ".$gid." AND hospital_id = ".$hid.";");
    		$existing_association = $db->fetchAll(
    						'SELECT count(*) from favorite_hospitals_hospital where favorite_hospital_id = ? and hospital_id = ?',
    						array($gid,$hid)
			);
    		if($existing_association[0][0]['count(*)'] < 1){					
    			$this->data['Hospital']['id'] = $hid;
				$this->data['FavoriteHospital']['id'] = $gid;
				if($this->save($this->data)){return true;}
			} else {
				throw new Exception(__('Association already exists in that group'));
			}
		} else {
			throw new Exception(__('Already 10 Hospitals in that group'));
		}
		return false;
    }
    
    public function deleteHospital($gid, $hid) {
    	$db = $this->getDataSource();
    	$existing_association = $db->fetchAll(
    						'SELECT count(*) from favorite_hospitals_hospital where favorite_hospital_id = ? and hospital_id = ?',
    						array($gid,$hid)
			);
    	if($existing_association[0][0]['count(*)'] > 0){	
    		$db->fetchAll(
    						'DELETE from favorite_hospitals_hospital where favorite_hospital_id = ? and hospital_id = ?',
    						array($gid,$hid)
			);
    		return true;
		} else {
			return false;
		} 
 	
    }
}

?>