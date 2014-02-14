<?php
$this->Post = ClassRegistry::init('Post');
if($this->request->is('mobile')){
	$cond = array(
		'Post.post_id' => 215
	);
}else{
	$cond = array(
		'Post.post_id' => 2
	);
}
$post = $this->Post->find('first', array(
	'conditions'=>$cond
));
echo $post['Post']['content'];