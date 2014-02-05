<!-- Additional Information -->
<?php if(isset($additionalInformation)): ?>
	閲覧数:<?php echo h($additionalInformation['viewCount']['sum']); ?>
	お気に入り:<?php echo h($additionalInformation['viewCount']['favorite_sum']); ?>
<?php else: ?>
	<p><?php echo $this->Html->link('無料会員登録をしていただくと、お気に入りグループ登録などの機能をご利用いただけます。', array('controller'=>'Users', 'action'=>'Login')); ?></p>
<?php endif; ?>
