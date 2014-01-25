<!-- Additional Information -->
<?php if(isset($additionalInformation)): ?>
	閲覧数:<?php echo h($additionalInformation['viewCount']['sum']); ?>
	お気に入り:<?php echo h($additionalInformation['viewCount']['favorite_sum']); ?>
<?php else: ?>
	<p><?php echo $this->Html->link('ログインすると、この病院の閲覧数とお気に入り登録回数も見ることができます。', array('controller'=>'Users', 'action'=>'Login')); ?></p>
<?php endif; ?>
