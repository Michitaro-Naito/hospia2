<?php $this->start('script'); ?>
<script>
var disableUrl = '<?php echo h(Router::url('/Users/Disable')); ?>';
function Disable(id){
	if(!window.confirm('ユーザーを無効化します。よろしいですか？'))
		return;
	$('#form0').attr('action', disableUrl+'/'+id).submit();
}
</script>
<?php $this->end(); ?>



<!-- Simple user index for testing. -->
<div class="page-header">
    <h1>Users</h1>
</div>
<?php echo $this->Html->link('Add', '/Users/Edit/'); ?>

<!-- Form to Search -->
<?php
	echo $this->Form->create('VM', array('type' => 'get'));
	echo $this->Form->text('username');
	echo $this->Form->end('Search');
?>

<!-- Data -->
<ul>
	<?php foreach($users as $u): ?>
		<li>
			<?php echo h($u['User']['id']); ?>
			<?php echo h($u['User']['username']); ?>
			<?php echo $this->Html->link('編集', array('controller'=>'Users', 'action'=>'Edit', $u['User']['id'])); ?>
			<button type="button" onclick="Disable(<?php echo h($u['User']['id']); ?>);">無効化</button>
		</li>
	<?php endforeach; ?>
	<?php
	    echo $this->Paginator->prev('« Previous ', null, null, array('class' => 'disabled'));
	    echo $this->Paginator->next(' Next »', null, null, array('class' => 'disabled'));
	?>
</ul>

<form method="post" action="" id="form0"></form>
