<?php $this->start('script'); ?>
<?php echo $this->Html->script('tinymce/tinymce.min.js'); ?>
<script type="text/javascript">
tinymce.init({
	language: 'ja',
	selector: "textarea",
	plugins: [
		"advlist autolink lists link image charmap print preview anchor",
		"searchreplace visualblocks code fullscreen",
		"insertdatetime media table contextmenu paste"
	],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	autosave_ask_before_unload: false
});

function InsertLink(link){
	tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="<?php echo Router::url('/files'); ?>/'+link+'">'+link+'</a>');
	alert('リンクが挿入されました。');
}

$(document).ready(function(){
	$('#uploader').click(function(){
		window.open('<?php echo Router::url('/File') ?>', 'UploaderWindow');
	});
});
</script>
<?php $this->end(); ?>

<?php echo $this->Form->create('Post'); ?>

<?php
	echo $this->Form->inputs(array(
		'post_id'=>array('type'=>'text'),
		'title'=>array('type'=>'text'),
		'content',
		'status',
	));
	if(!empty($this->data['Post']['created']))
		echo $this->Form->input('created', array('type'=>'text', 'disabled'=>'disabled'));
	if(!empty($this->data['Post']['modified']))
		echo $this->Form->input('modified', array('type'=>'text', 'disabled'=>'disabled'));
?>
<button type="button" id="uploader">ファイルアップロード</button>
<?php echo $this->Form->end('Save'); ?>

<?php echo $this->Html->link('一覧', array('controller'=>'Post', 'action'=>'Index')); ?>
