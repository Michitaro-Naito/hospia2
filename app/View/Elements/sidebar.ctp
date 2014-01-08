<?php
$this->Post = ClassRegistry::init('Post');
$post = $this->Post->find('first', array(
	'conditions'=>array(
		'Post.post_id'=>2
	)
));
echo $post['Post']['content'];