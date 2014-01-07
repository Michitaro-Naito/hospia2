<?php
class Post extends AppModel{
	public $primaryKey = 'ID';
	public $name = 'Post';
	public $useTable = 'post';
	public $validate = array(
		'title'=>array(
			'between'=>array(
				'rule'=>array('between', 1, 10),
				'message' => 'Between 5 to 15 characters',
			)
		)
	);
	public $actsAs = array('CakeSoftDelete.SoftDeletable');
}
?>