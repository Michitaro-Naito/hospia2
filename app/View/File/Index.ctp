<h2>アップロード済みのファイル</h2>
<ul>
	<?php foreach($files as $f): ?>
		<li>
			<?php echo $this->Html->link($f['File']['name'], '/files/'.$f['File']['name'], array('target'=>'_blank')); ?>
			<button type="button" onclick="window.opener.InsertLink('<?php echo h($f['File']['name']); ?>');"></button>
			<?php echo h($f['File']['created']); ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php debug($files); ?>
