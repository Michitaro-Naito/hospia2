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

<h2 class="posttitle">記事管理</h2>
<?php echo $this->Html->link('新しい記事を作成する', '/Post/Edit/'); ?>

<!-- Form to Search -->
<?php
	echo $this->Form->create('VM', array('type' => 'get'));
	echo $this->Form->text('title', array('class'=>'form-control'));
	echo $this->Form->submit('記事を検索する', array('class'=>'btn btn-default'));
	echo $this->Form->end();
?>

<!-- Data -->
<table class="table table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>タイトル</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($posts as $p): ?>
			<tr>
				<td><?php echo h($p['Post']['id']); ?></td>
				<td><?php echo h($p['Post']['title']); ?></td>
				<td>
					<?php echo $this->Html->link('編集する', array('controller'=>'Post', 'action'=>'Edit', $p['Post']['id'])); ?>
					<button type="button" onclick="Disable(<?php echo h($p['Post']['id']); ?>);" class="btn btn-default">無効化する</button>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

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