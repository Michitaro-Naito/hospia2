<?php
	echo $this->Form->create();
	echo $this->Form->hidden('id');
?>
<?php
	echo $this->Form->inputs(array(
		'id',
		'username',
		'new_password'=>array('type'=>'text'),
		'role',
		'email',
		'displayname',
		'active'
	));
	if(!empty($this->data['User']['created']))
		echo $this->Form->input('created', array('type'=>'text', 'disabled'=>'disabled'));
	if(!empty($this->data['User']['modified']))
		echo $this->Form->input('modified', array('type'=>'text', 'disabled'=>'disabled'));
?>
<?php echo $this->Form->end('Save'); ?>
