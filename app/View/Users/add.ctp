<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<div class="box" style="margin: 20px;">
		<h2>病院情報局　会員登録</h2>
		<div class="content">
			<p>以下の利用規約をよくお読みいただき、ご同意の上で会員登録をお願いいたします。</p>
			<ul class="basic"><li class="li2"><?php echo $this->Html->link('［病院情報局利用規約］', '/wp/archives/217'); ?></li></ul>
			<?php echo $this->Session->flash(); ?>
			<table>
				<tr>
					<td>ユーザー名<small>(半角英数4文字以上)</small></td>
					<td><?php echo $this->Form->input('username', array('label'=>false)); ?></td>
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
					<td>姓<small>(6文字以内)</small></td>
					<td><?php echo $this->Form->input('sei', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>名<small>(6文字以内)</small></td>
					<td><?php echo $this->Form->input('mei', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>せい<small>(ひらがな12文字以内)</small></td>
					<td><?php echo $this->Form->input('sei_kana', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>めい<small>(ひらがな12文字以内)</small></td>
					<td><?php echo $this->Form->input('mei_kana', array('label'=>false)); ?></td>
				</tr>
				<tr>
					<td>ご職業</td>
					<td>
						<?php
							echo $this->Form->input('job', array(
							    'options' => Configure::read('jobs'), //array(1=>'aaa', 2, 3, 4, 5),
							    'empty' => '選択して下さい',
							    'label' => false
							));
						?>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="padding: 20px;">
						<?php echo $this->Form->submit('利用規約に同意して登録する'); ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
<ul class="basic">
<li class="li2"><?php echo $this->Html->link('会員機能のご案内', '/wp/archives/225'); ?></li>
</ul>
<?php echo $this->Form->end(); ?>
</div>