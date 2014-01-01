<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Please enter your username and password'); ?></legend>
        <?php echo $this->Form->input('username');
        echo $this->Form->input('password');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
<?php 
	echo __('No Account? '); 
	echo $this->Html->link(
         	'Register <span class="glyphicon glyphicon-pencil"></span>', 
         	array('controller' => 'users', 'action' => 'add'),
         	array('escape' => false)
    );
    // ^^ Escape ->false allows you to add a span glyphicon to a link without it being escaped
?>
</div>