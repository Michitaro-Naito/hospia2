<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<div class="box">
		<h2>ログイン</h2>
		<div class="content">
			<?php echo $this->Session->flash(); ?>
			<table>
				<tr>
					<td>ユーザー名</td>
					<td><?php echo $this->Form->input('username', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>パスワード</td>
					<td><?php echo $this->Form->input('password', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo $this->Form->submit('ログイン'); ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php echo $this->Form->end(); ?>
<span>アカウントがありませんか？</span>
<?php echo $this->Html->link('こちらから登録できます。<span class="glyphicon glyphicon-pencil"></span>', array('controller' => 'users', 'action' => 'add'), array('escape'=>false)); ?><br/>
<span>パスワードをお忘れですか？</span>
<?php echo $this->Html->link('こちらから再設定できます。', array('controller'=>'Users', 'action'=>'ResetPassword')); ?>
</div>