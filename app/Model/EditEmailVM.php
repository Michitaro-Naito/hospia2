<?php
class EditEmailVM extends AppModel{
	public $useTable = false;
	public $validate = array(
		'new_email'=>array(
			'between'=>array(
				'rule'=>array('email'),
				'message' => '正しいEメールアドレスを入力して下さい。',
			)
		)
	);
}
