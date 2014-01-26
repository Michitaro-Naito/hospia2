<?php
class Post extends AppModel{
	public $primaryKey = 'ID';
	public $name = 'Post';
	public $useTable = 'post';
	public $validate = array(
		'title'=>array(
			'between'=>array(
				'rule'=>array('between', 1, 20),
				'message' => 'Between 5 to 20 characters',
			)
		)
	);
	public $actsAs = array('CakeSoftDelete.SoftDeletable');
}
?>