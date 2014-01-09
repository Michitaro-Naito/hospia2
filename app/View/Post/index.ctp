<?php $this->start('script'); ?>
<script>
var disableUrl = '<?php echo h(Router::url('/Post/Disable')); ?>';
function Disable(id){
	if(!window.confirm('記事を無効化します。よろしいですか？'))
		return;
	$('#form0').attr('action', disableUrl+'/'+id).submit();
}
</script>
<?php $this->end(); ?>



<div id="wrapper">

<div id="content">
<div id="innerbox">
<div class="pagebox">

<h2 class="posttitle">Post/Index</h2>
<?php echo $this->Html->link('Add', '/Post/Edit/'); ?>

<!-- Form to Search -->
<?php
	echo $this->Form->create('VM', array('type' => 'get'));
	echo $this->Form->text('title');
	echo $this->Form->end('Search');
?>

<!-- Data -->
<?php foreach($posts as $p): ?>
	<li>
		<?php echo h($p['Post']['id']); ?>
		<?php echo h($p['Post']['title']); ?>
		<?php echo $this->Html->link('編集', array('controller'=>'Post', 'action'=>'Edit', $p['Post']['id'])); ?>
		<button type="button" onclick="Disable(<?php echo h($p['Post']['id']); ?>);">無効化</button>
	</li>
<?php endforeach; ?>
<?php
    echo $this->Paginator->prev('« Previous ', null, null, array('class' => 'disabled'));
    echo $this->Paginator->next(' Next »', null, null, array('class' => 'disabled'));
?>

<form method="post" action="" id="form0">
	<input type="hidden" name="id" />
</form>

</div><!-- END div.post -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->