<?php

class Tip extends AppModel{
	public $useTable = 'tip';
	public $validate = array(
		'name' => array(
			'between'=>array(
				'rule'=>array('between', 1, 20),
				'message'=>'1〜20文字で入力して下さい。'
			),
			'isUnique'=>array(
				'rule'=>array('isUnique'),
				'message'=>'既に存在します。他の名前を付けて下さい。'
			)
		)
	);
}
