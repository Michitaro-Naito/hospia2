<?php
class Settings extends AppModel{
	public $name = 'Settings';
	public $useTable = 'settings';
	public $validate = array(
		'insentive_hours'=>array(
      'required' => array(
          'rule' => array('notEmpty'),
          'message' => '入力して下さい。'
      ),
      'range'=>array(
      	'rule' => array('range', 0, 721),
      	'message' => '1から720で入力して下さい。'
			)
		),
		'insentive_max_count' => array(
      'required' => array(
          'rule' => array('notEmpty'),
          'message' => '入力して下さい。'
      ),
      'range'=>array(
      	'rule' => array('range', 0, 1000001),
      	'message' => '1から1000000で入力して下さい。'
			)
		)
	);
}
?>