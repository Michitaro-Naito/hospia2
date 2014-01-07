<div id="wrapper">

<div id="content">
<div id="innerbox">
<div class="pagebox">

<h2 class="posttitle">Post/Index</h2>
<?php echo $this->Html->link('Add', '/Post/Edit/'); ?>
<?php foreach($posts as $p): ?>
	<li>
		<?php echo h($p['Post']['id']); ?>
		<?php echo h($p['Post']['title']); ?>
		<?php echo $this->Html->link('編集', array('controller'=>'Post', 'action'=>'Edit', $p['Post']['id'])); ?>
	</li>
<?php endforeach; ?>
<?php
    echo $this->Paginator->prev('« Previous ', null, null, array('class' => 'disabled'));
    echo $this->Paginator->next(' Next »', null, null, array('class' => 'disabled'));
?>

</div><!-- END div.post -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->