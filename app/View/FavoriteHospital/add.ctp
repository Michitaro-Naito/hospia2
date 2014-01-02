<div class="form">
<?php echo $this->Session->flash(); ?>
<?php 
	foreach ($errors as $error):
		echo $error[0]; 
	endforeach;
	unset($error);
?>
<?php echo $this->Form->create('FavoriteHospital'); ?>
    <fieldset>
        <legend><?php echo __('Add Hospital Group'); ?></legend>
        <?php 
        echo $this->Form->input('name', array(
        						'label' => 'Group Name'
        						));
   		?>
    </fieldset>
<?php echo $this->Form->submit('Submit',array('formnovalidate' => true)); ?>
</div>