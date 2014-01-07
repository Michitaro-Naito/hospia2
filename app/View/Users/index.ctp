<?php $this->start('script'); ?>
<script>
function Disable(id){
	alert(id);
}
</script>
<?php $this->end(); ?>



<!-- Simple user index for testing. -->
<div class="page-header">
    <h1>Users</h1>
</div>
<ul>
	<?php foreach($users as $u): ?>
		<li>
			<?php echo h($u['User']['id']); ?>
			<?php echo h($u['User']['username']); ?>
			<?php echo $this->Html->link('編集', array('controller'=>'Users', 'action'=>'Edit', $u['User']['id'])); ?>
			<button type="button" onclick="Disable(<?php echo h($u['User']['id']); ?>);">無効化</button>
		</li>
	<?php endforeach; ?>
</ul>

<!-- DEBUG -->
<ul class="media-list">
<?php foreach ($users as $user): ?>
	<li>
		<ul>
		<li class="media">
			<?php debug($user); ?>
		</li>
    	<li class="media">
    		<?php echo $user['User']['id']; ?>
    	</li>
    	<li class="media">
    		<?php echo $user['User']['username']; ?>
    	</li>
    	<li class="media">
    	</li>
    	</ul>
    </li>
<?php endforeach; ?>
<?php unset($user); ?>
</ul>