<div class="box">
	<h2>メールアドレス変更</h2>
	<div class="content">
		<?php if(empty($user)): ?>
			ログインして下さい。
		<?php else: ?>
			<?php
				echo $this->Form->create('EditEmailVM');
				echo $this->Session->flash();
			?>
			現在のメールアドレス：<?php echo h($user['User']['email']); ?><br/>
			新しいメールアドレス：<?php echo $this->Form->input('new_email', array('label'=>false, 'div'=>false)); ?>
			<?php echo $this->Form->end('送信'); ?>
		<?php endif; ?>
	</div>
</div>