<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Register'); ?></legend>
        <?php 
        echo $this->Form->input('username', array(
        						'label' => 'User Name'
        						));
        echo $this->Form->input('displayname', array(
        						'label' => 'Display Name'
        						));
        echo $this->Form->input('email', array(
        						'label' => 'Email Address'
        						));
        echo $this->Form->input('password', array(
        						'label' => 'Password'
        						));
   		?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>