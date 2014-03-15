<!-- Additional Information -->
<?php if(isset($additionalInformation)): ?>
	過去30日間のアクセス件数:<?php echo h($additionalInformation['viewCount']['sum']); ?>｜
	お気に入り登録件数:<?php echo h($additionalInformation['viewCount']['favorite_sum']); ?>
<?php else: ?>
	<p><?php echo $this->Html->link('ログインすると、アクセス件数の閲覧や、お気に入りグループ登録などの機能をご利用いただけます。', array('controller'=>'Users', 'action'=>'Login')); ?></p>
<?php endif; ?>
