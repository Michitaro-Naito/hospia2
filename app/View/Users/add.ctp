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
					<td colspan="2"><?php echo $this->Form->submit('登録'); ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php echo $this->Form->end(); ?>
</div>