<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<div class="box">
		<h2>ユーザー登録</h2>
		<div class="content">
			<?php echo $this->Session->flash(); ?>
			<table>
				<tr>
					<td>ユーザー名<small>(半角英数4文字以上)</small></td>
					<td><?php echo $this->Form->input('username', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>表示名<small>(半角英数4文字以上)</small></td>
					<td><?php echo $this->Form->input('displayname', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>メールアドレス</td>
					<td><?php echo $this->Form->input('email', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>パスワード<small>(半角英数6文字以上)</small></td>
					<td><?php echo $this->Form->input('password', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo $this->Form->submit('登録'); ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php echo $this->Form->end(); ?>
</div>