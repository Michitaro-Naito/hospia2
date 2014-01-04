<?php

class FavoriteHospital extends AppModel {
	
	public $name = 'FavoriteHospital';
	public $useTable = 'favorite_hospitals';
	
	public $belongsTo = 'User';
	
	public $hasAndBelongsToMany = 'Hospital';
	
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
    	$this->data['Hospital']['id'] = $hid;
		$this->data['FavoriteHospital']['id'] = $gid;
		$this->FavoriteHospital->save($this->data);
    }
}

?>