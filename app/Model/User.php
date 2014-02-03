<?php
// app/Model/User.php
class User extends AppModel {
	
	//Act as Soft Deletable...
	public $actsAs = array('CakeSoftDelete.SoftDeletable');
	
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '入力して下さい。'
            ),
            'unique' => array(
            	'rule' => 'isUnique',
            	'message' => '既に使用されています。他のユーザー名をお試しください。'
            )
        ),
        'displayname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '入力して下さい。'
            ),
            'unique' => array(
            	'rule' => 'isUnique',
            	'message' => '既に使用されています。他の表示名をお試しください。'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '入力して下さい。'
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '入力して下さい。'
            ),
            'email' => array(
            	'rule' => array('email', true),
            	'message' => 'aaa@bbb.ccc形式で正しいメールアドレスを入力して下さい。'
            ),
            'unique' => array(
            	'rule' => 'isUnique',
            	'message' => '既に使用されています。他のメールアドレスをご利用ください。'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'author', 'basic')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );
	
	public $hasMany = 'FavoriteHospital';
	
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
		    $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
}
?>