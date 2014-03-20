<h2>各種設定</h2>
<?php
	echo $this->Form->create('Settings', array(
		'inputDefaults'=>array(
			'class'=>'form-control'
		)
	));
	echo $this->Form->hidden('id');
?>
<?php
	echo $this->Form->input('insentive_active', array('label'=>'インセンティブを提供する', 'type'=>'checkbox', 'class'=>false));
	echo $this->Form->input('insentive_hours', array('label'=>'1回のインセンティブで延長される時間'));
	echo $this->Form->input('insentive_max_count', array('label'=>'1会員がインセンティブを得られる最大回数'));
?>
<?php
	echo $this->Form->submit('保存する', array('class'=>'btn btn-default'));
	echo $this->Form->end();
?>

<?php echo $this->Html->link('一覧に戻る', array('controller'=>'Users', 'action'=>'Index')); ?>