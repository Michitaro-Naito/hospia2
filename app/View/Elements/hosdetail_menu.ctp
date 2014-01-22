<!-- Menu -->
<h2><?php echo h($dat['hospital']['Hospital']['name']); ?></h2>
<?php echo $this->Html->link('診療実績', '/hosdetail/' . $dat['wamId']); ?>
<?php echo $this->Html->link('他病院比較', '/hoscmp/' . $dat['wamId']); ?>
<?php echo $this->Html->link('病院基本情報', '/hosinfo/' . $dat['wamId']); ?>