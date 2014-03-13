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
    <h1>ユーザー管理</h1>
</div>
<?php echo $this->Html->link('新しいユーザーを作成する', '/Users/Edit/'); ?>

<!-- Form to Search -->
<?php
	echo $this->Form->create('VM', array('type' => 'get'));
	echo $this->Form->text('username', array('class'=>'form-control'));
	echo $this->Form->submit('登録されているユーザーを検索する', array('class'=>'btn btn-default'));
	echo $this->Form->end();
?>

<!-- Data -->
<table class="table table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>ユーザー名</th>
			<th>氏名</th>
			<th>区分</th>
			<th>職業</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($users as $u): ?>
			<tr>
				<td><?php echo h($u['User']['id']); ?></td>
				<td><?php echo h($u['User']['username']); ?></td>
				<td><?php echo h($u['User']['sei'] . $u['User']['mei']); ?></td>
				<td>
					<?php if(!empty($u['Subscription'])): ?>
						プレミアム
					<?php else: ?>
						通常
					<?php endif; ?>
				</td>
				<td><?php echo h($u['User']['job']); ?></td>
				<td>
					<?php echo $this->Html->link('編集する', array('controller'=>'Users', 'action'=>'Edit', $u['User']['id'])); ?>
					<button type="button" onclick="Disable(<?php echo h($u['User']['id']); ?>);" class="btn btn-default">無効化する</button>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
    echo $this->Paginator->prev('« 前へ ', null, null, array('class' => 'disabled'));
    echo $this->Paginator->next(' 次へ »', null, null, array('class' => 'disabled'));
?>

<form method="post" action="" id="form0"></form>
