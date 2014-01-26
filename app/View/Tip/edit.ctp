<?php $this->start('script'); ?>
<?php echo $this->Html->script('tinymce/tinymce.min.js'); ?>
<script type="text/javascript">
tinymce.init({
	language: 'ja',
	selector: "textarea",
	plugins: [
    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
    "searchreplace wordcount visualblocks visualchars code fullscreen",
    "insertdatetime media nonbreaking save table contextmenu directionality",
    "emoticons template paste textcolor"
	],
  toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
  toolbar2: "print preview media | forecolor backcolor emoticons",
	autosave_ask_before_unload: false
});

function InsertLink(link){
	tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="<?php echo Router::url('/files'); ?>/'+link+'">'+link+'</a>');
	alert('リンクが挿入されました。');
}

function InsertImage(link){
	tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<img src="<?php echo Router::url('/files'); ?>/'+link+'"/>');
	alert('画像が挿入されました。');
}

$(document).ready(function(){
	$('#uploader').click(function(){
		window.open('<?php echo Router::url('/File') ?>', 'UploaderWindow');
	});
});
</script>
<?php $this->end(); ?>

<?php
	echo $this->Form->create('Tip', array('inputDefaults'=>array('class'=>'form-control')));
	echo $this->Form->hidden('id');
?>

<?php
	echo $this->Form->inputs(array(
		'name',
		'value'
	));
?>
<button type="button" id="uploader">ファイルアップロード</button>
<?php
	echo $this->Form->submit('保存する', array('class'=>'btn btn-default'));
	echo $this->Form->end();
?>

<?php echo $this->Html->link('一覧に戻る', array('controller'=>'Tip', 'action'=>'Index')); ?>
