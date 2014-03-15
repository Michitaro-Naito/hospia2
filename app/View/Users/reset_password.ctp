<div class="box" style="margin: 20px;">
	<h2>パスワード再設定</h2>
	<div class="content">
		<p>パスワードの再設定に必要な情報を、ご登録のメールアドレスに送信いたします。</p>
		<?php
			echo $this->Form->create('ResetPasswordVM', array('inputDefaults'=>array('label'=>false)));
			echo $this->Session->flash();
		?>
		<table>
			<tr>
				<th>メールアドレス</th>
				<td><?php echo $this->Form->input('email'); ?></td>
			</tr>
			<tr>
				<th>姓</th>
				<td><?php echo $this->Form->input('sei'); ?></td>
			</tr>
			<tr>
				<th>名</th>
				<td><?php echo $this->Form->input('mei'); ?></td>
			</tr>
		</table>
		<?php echo $this->Form->end('送信する'); ?>
	</div>
</div>