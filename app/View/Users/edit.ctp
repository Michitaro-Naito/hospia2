<?php
	echo $this->Form->create('User', array(
		'inputDefaults'=>array(
			'class'=>'form-control'
		)
	));
	echo $this->Form->hidden('id');
?>
<?php
	echo $this->Form->inputs(array(
		'id',
		'username',
		'new_password'=>array('type'=>'text'),
		'role',
		'email',
		//'displayname',
		'sei', 'mei', 'sei_kana', 'mei_kana',
		'job' => array('options'=>Configure::read('jobs'), 'empty'=>'選択して下さい'),
		'active'
	));
	if(!empty($this->data['User']['created']))
		echo $this->Form->input('created', array('type'=>'text', 'disabled'=>'disabled'));
	if(!empty($this->data['User']['modified']))
		echo $this->Form->input('modified', array('type'=>'text', 'disabled'=>'disabled'));
?>
<?php
	echo $this->Form->submit('保存する', array('class'=>'btn btn-default'));
	echo $this->Form->end();
?>

<?php echo $this->Html->link('一覧に戻る', array('controller'=>'Users', 'action'=>'Index')); ?>