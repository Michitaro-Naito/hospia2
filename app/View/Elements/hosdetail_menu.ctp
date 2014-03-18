<!-- Menu -->
<div id="hosdetail-menu">
	<div class="box">
		<h2><?php echo h($dat['hospital']['Hospital']['name']); ?></h2>
		<div class="content">
			<?php echo $this->Html->link('診療実績', '/hosdetail/' . $dat['wamId']); ?>
			<?php echo $this->Html->link('他病院比較', '/hoscmp/' . $dat['wamId']); ?>
			<?php echo $this->Html->link('病院基本情報', '/hosinfo/' . $dat['wamId']); ?>
			<?php echo $this->Html->link('時系列分析', '/Compare/' . $dat['wamId'], array('class'=>'premium')); ?>
			<?php echo $this->Html->link('ポジション分析', '/Position/' . $dat['wamId'], array('class'=>'premium')); ?>
		</div>
	</div>
</div>
