<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<div class="box" style="margin: 20px;">
		<h2>病院情報局　会員ログイン</h2>
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
					<td colspan="2" style="padding: 20px;"><?php echo $this->Form->submit('ログイン'); ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php echo $this->Form->end(); ?>
<ul class="basic">
<li class="li2"><?php echo $this->Html->link('会員機能のご案内', '/wp/archives/225'); ?>
<?php echo $this->Form->end(); ?></li>
<li class="li2"><span>会員登録がまだですか？</span>
<?php echo $this->Html->link('こちらから登録できます。<span class="glyphicon glyphicon-pencil"></span>', array('controller' => 'users', 'action' => 'add'), array('escape'=>false)); ?></li>
<li class="li2"><span>パスワードをお忘れですか？</span>
<?php echo $this->Html->link('こちらから再設定できます。', array('controller'=>'Users', 'action'=>'ResetPassword')); ?></li>
</ul>
</div>