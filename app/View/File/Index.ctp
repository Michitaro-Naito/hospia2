<h2>アップロード済みのファイル</h2>
<ul>
	<?php foreach($files as $f): ?>
		<li>
			<?php echo $this->Html->link($f['File']['name'], '/files/'.$f['File']['name'], array('target'=>'_blank')); ?>
			<?php echo h($f['File']['created']); ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php debug($files); ?>
