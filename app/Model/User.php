<?php
// app/Model/User.php
class User extends AppModel {
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'You forgot a Username.'
            ),
            'unique' => array(
            	'rule' => 'isUnique',
            	'message' => 'Username already taken.'
            )
        ),
        'displayname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please choose a Display Name.'
            ),
            'unique' => array(
            	'rule' => 'isUnique',
            	'message' => 'Display Name already taken.'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'You forgot your password.'
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Email is mandatory for registration.'
            ),
            'email' => array(
            	'rule' => array('email', true),
            	'message' => 'Please enter a valid email address.'
            ),
            'unique' => array(
            	'rule' => 'isUnique',
            	'message' => 'Account with that email already exists.'
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
		
		public function beforeSave($options = array()) {
		    if (isset($this->data[$this->alias]['password'])) {
		        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		    }
		    return true;
		}
}
?>