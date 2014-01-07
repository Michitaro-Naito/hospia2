<h2>File/Upload</h2>

<form method="post" enctype="multipart/form-data">
	<input type="file" name="upfile" />
	<input type="submit" value="Upload" />
</form>
<?php echo realpath('./files'); ?>
<?php debug($_FILES); ?>
