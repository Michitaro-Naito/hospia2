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

<h2 class="posttitle">バルーンチップ管理</h2>
<?php echo $this->Html->link('新しいバルーンチップを作成する', '/Tip/Edit/'); ?>

<!-- Form to Search -->
<?php
	echo $this->Form->create('VM', array('type' => 'get'));
	echo $this->Form->text('name', array('class'=>'form-control'));
	echo $this->Form->submit('バルーンチップを検索する', array('class'=>'btn btn-default'));
	echo $this->Form->end();
?>

<!-- Data -->
<table class="table table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Key</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($tips as $t): ?>
			<tr>
				<td><?php echo h($t['Tip']['id']); ?></td>
				<td><?php echo h($t['Tip']['name']); ?></td>
				<td>
					<?php echo $this->Html->link('編集する', array('controller'=>'Tip', 'action'=>'Edit', $t['Tip']['id'])); ?>
					<button type="button" onclick="Delete(<?php echo h($t['Tip']['id']); ?>);" class="btn btn-default">削除する</button>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
    echo $this->Paginator->prev('« 前へ ', null, null, array('class' => 'disabled'));
    echo $this->Paginator->next(' 次へ »', null, null, array('class' => 'disabled'));
?>

<form method="post" action="" id="form0">
	<input type="hidden" name="id" />
</form>

</div><!-- END div.post -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->