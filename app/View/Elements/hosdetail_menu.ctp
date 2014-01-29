<!-- Menu -->
<div id="hosdetail-menu">
	<h2><?php echo h($dat['hospital']['Hospital']['name']); ?></h2>
	<div class="box">
		<div class="content">
			<?php echo $this->Html->link('診療実績', '/hosdetail/' . $dat['wamId']); ?>
			<?php echo $this->Html->link('他病院比較', '/hoscmp/' . $dat['wamId']); ?>
			<?php echo $this->Html->link('病院基本情報', '/hosinfo/' . $dat['wamId']); ?>
			<?php if($isPremiumUser): ?>
				<?php echo $this->Html->link('過年度比較', '/Compare/' . $dat['wamId'], array('target'=>'_blank')); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
