<?php
	$this->set('is_top', TRUE);
?>



<div class="row">
	<div class="col-sm-6">病院検索</div>
	<div class="col-sm-6">ご利用ガイド</div>
</div>

<div class="row">
	<div class="col-sm-9">
		<div class="row">
			<div class="col-sm-12">新着情報</div>
		</div>
		<div class="row">
			<div class="col-sm-12">Facebookボタン</div>
		</div>
		<div class="row">
  		<div class="col-sm-6">最近チェックした病院</div>
  		<div class="col-sm-6">閲覧数の多い病院</div>
		</div>
		<div class="row">
  		<div class="col-sm-12">お気に入りグループ一覧</div>
		</div>
	</div>
	<div class="col-sm-3 bs-sidebar">
		<div class="row">
  		<div class="col-sm-12">疾患別</div>
		</div>
		<div class="row">
  		<div class="col-sm-12">診断分類別</div>
		</div>
		<?php echo $this->element('ad_sidebar'); ?>
	</div>
</div>