<?php $this->start('script'); ?>
<script>
var deleteUrl = '<?php echo h(Router::url('/Tip/Delete')); ?>';
function Delete(id){
	if(!window.confirm('説明文を削除します。よろしいですか？'))
		return;
	$('#form0').attr('action', deleteUrl+'/'+id).submit();
}
</script>
<?php $this->end(); ?>



<div id="wrapper">

<div id="content">
<div id="innerbox">
<div class="pagebox">

<h2 class="posttitle">Tip/Index</h2>
<?php echo $this->Html->link('Add', '/Tip/Edit/'); ?>

<!-- Form to Search -->
<?php
	echo $this->Form->create('VM', array('type' => 'get'));
	echo $this->Form->text('name');
	echo $this->Form->end('Search');
?>

<!-- Data -->
<?php foreach($tips as $t): ?>
	<li>
		<?php echo h($t['Tip']['id']); ?>
		<?php echo h($t['Tip']['name']); ?>
		<?php echo $this->Html->link('編集', array('controller'=>'Tip', 'action'=>'Edit', $t['Tip']['id'])); ?>
		<button type="button" onclick="Delete(<?php echo h($t['Tip']['id']); ?>);">削除</button>
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