<h2>アップロード済みのファイル</h2>
<?php echo $this->Html->link('新しいファイルをアップロードする', array('controller'=>'File', 'action'=>'Upload')); ?>
<?php if(empty($files)): ?>
	<p>アップロードされたファイルはありません。</p>
<?php else: ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>ファイル名</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($files as $f): ?>
				<tr>
					<td><?php echo $this->Html->link($f['File']['name'], '/files/'.$f['File']['name'], array('target'=>'_blank')); ?></td>
					<td>
						<button type="button" onclick="window.opener.InsertLink('<?php echo h($f['File']['name']); ?>');">リンクを挿入する</button>
						<button type="button" onclick="window.opener.InsertImage('<?php echo h($f['File']['name']); ?>');">画像として挿入する</button>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
