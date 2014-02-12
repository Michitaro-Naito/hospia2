<?php
class Post extends AppModel{
	public $primaryKey = 'ID';
	public $name = 'Post';
	public $useTable = 'post';
	public $validate = array(
		'title'=>array(
			'between'=>array(
				'rule'=>array('between', 1, 100),
				'message' => '1から100文字で入力して下さい。',
			)
		)
	);
	public $actsAs = array('CakeSoftDelete.SoftDeletable');
}
?>