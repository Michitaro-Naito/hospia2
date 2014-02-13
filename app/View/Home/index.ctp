<?php //echo $this->element('debug'); ?>



<?php
	$this->set('is_top', TRUE);
?>
<?php $this->start('script'); ?>
<script>
(function(){
try{
// Get initial variables from server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');

function AppModel(){
	var s = this;
	
	s.prefectures = dat.prefectures;
	s.zones = ko.observableArray();
	s.hospitalName = ko.observable();
	
	s.selectedPrefecture = ko.observable();
	s.selectedZone = ko.observable();
	
	// 選択された都道府県に合わせて医療圏を再読み込み
	s.selectedPrefecture.subscribe(function(newValue){
		try{
			if(!newValue)
				return;
			if(!newValue.id)
				return;
			$.postJSON({
				url: dat.getZonesUrl,
				data: {
					prefectureId: newValue.id
				}
			}).done(function(data){
				s.zones(data.zones);
			});
		}catch(e){
			alert(e);
		}
	});
	
	// hoslstへページ移動する。その際、選択された都道府県・医療圏・病院名を渡す。
	s.gotoHoslst = function(){
		try{
		var uri = new Uri(dat.hoslistUrl);
		uri.replaceQueryParam('prefectureId', s.selectedPrefecture().id);
		if(s.selectedZone()) uri.replaceQueryParam('zoneId', s.selectedZone().id);
		uri.replaceQueryParam('hospitalName', s.hospitalName());
		window.location.href = uri.toString();
		}catch(e){
			alert(e);
		}
	}
}

var model = new AppModel();
ko.applyBindings(model, document.getElementById('IndexSearch'));
}catch(e){
	alert(e);
}
})();
</script>
<?php $this->end(); ?>



<?php echo $this->element('fb_root'); ?>

<div class="row">
	
	<!-- 検索 -->
	<div class="col-sm-5" id="IndexSearch">
		<div class="box">
			<h2>
				<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
				病院検索
			</h2>
			<div class="content">
				<table class="search">
					<tr>
						<td>都道府県</td>
						<td><select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select></td>
					</tr>
					<tr>
						<td>医療圏<?php echo $this->My->tip('医療圏'); ?></td>
						<td><select data-bind="options: zones, optionsText: 'name', value: selectedZone"></select></td>
					</tr>
					<tr>
						<td>病院名(一部でも可)</td>
						<td><input type="text" data-bind="value: hospitalName"/></td>
					</tr>
					<tr>
						<td colspan="2">
							<button type="button" class="search" data-bind="click: gotoHoslst">
								<?php echo $this->Html->image('icon/search.png', array('style'=>'padding-bottom:3px;')); ?>
								検索
							</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
	<!-- ご利用ガイド -->
	<div class="col-sm-7">
		<?php echo $this->Html->link($this->Html->image('main.png'), '/wp/gu/', array('escape'=>false, 'id'=>'main')); ?>
		<div class="row">
			<?php echo $this->Html->link($this->Html->image('sp_main.jpg'), '/wp/gu/', array('escape'=>false, 'id'=>'sp-main')); ?>
		</div>
	</div>
	
</div>

<div class="row" style="margin-top: 17px;">
	<div class="col-sm-9">
		
		<!-- 新着情報 -->
		<div class="box">
			<h2>
				<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
				新着情報
			</h2>
			<div class="content">
				<ul class="news">
					<?php foreach($recentPosts as $p): ?>
						<?php ob_start(); ?>
						<?php
							$utcDate = new DateTime($p['Post']['created'], new DateTimeZone('UTC'));
							$utcDate->setTimezone(new DateTimeZone('Asia/Tokyo'));
						?>
						<span><?php echo h($p['Post']['title']); ?></span>
						<?php $element = ob_get_clean(); ?>
						<li>
							<table>
								<tr>
									<td><time datetime="<?php echo h($utcDate->format('Y-m-d H:i:s')); ?>"><?php echo h($utcDate->format('Y.m.d')); ?></time></td>
									<td><?php echo $this->Html->link($element, "/wp/archives/{$p['Post']['post_id']}", array('escape'=>false)); ?></td>
								</tr>
							</table>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		
		<!-- ソーシャル連携 -->
		<div class="">
			<div><?php echo $this->element('twitter_follow'); ?></div>
			<div><?php echo $this->element('fb_follow'); ?></div>
		</div>
		
		<!-- 最近チェックした病院　と　閲覧数の多い病院 -->
		<div class="row">
  		<div class="col-sm-6">
  			<div class="box">
	  			<h2>
						<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
	  				最近チェックした病院<?php echo $this->My->tip('最近チェックした病院', array('image'=>true)); ?>
	  			</h2>
	  			<div class="content">
		  			<ul class="basic">
			  			<?php foreach($dat['rememberedHospitals'] as $h): ?>
			  				<li><?php echo $this->Html->link($h['Hospital']['name'], '/hosdetail/'.$h['Hospital']['wam_id']); ?></li>
			  			<?php endforeach; ?>
		  			</ul>
	  			</div>
  			</div>
  		</div>
  		
  		<div class="col-sm-6">
  			<div class="box">
	  			<h2>
						<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
	  				閲覧数の多い病院<?php echo $this->My->tip('閲覧数の多い病院', array('image'=>true)); ?>
	  			</h2>
	  			<div class="content">
		  			<ul class="basic">
		  				<?php foreach($dat['hospitalsMostViewed'] as $h): ?>
		  					<li><?php echo $this->Html->link($h['Hospital']['name'], '/hosdetail/'.$h['Hospital']['wam_id']); ?></li>
		  				<?php endforeach; ?>
		  			</ul>
	  			</div>
  			</div>
  		</div>
		</div>
		
		<!-- お気に入りグループ一覧 -->
		<?php echo $this->element('favorite'); ?>
		
		<!-- 広告ユニット(下部) -->
		<?php echo $this->element('ad_bottom'); ?>
		
	</div>
	
	<!-- サイドバー -->
	<div class="col-sm-3 bs-sidebar">
		
		<!-- 疾患別メニュー -->
		<div class="box">
			<h2 style="font-size:12px;">
				<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
				主な疾患別患者数ランキング<?php echo $this->My->tip('主な疾患別患者数ランキング', array('image'=>true)); ?>
			</h2>
			<div class="content">
				<ul class="basic">
					<?php foreach($dat['maladyCategories'] as $key => $c): ?>
						<li class="<?php if($c['id']>='m101'&&$c['id']<='m120') echo 'li2'; ?>"><?php echo $this->Html->link($c['name'], array('action'=>'Maladylist', 'controller'=>'Home', '?'=>array('mdata'=>$c['id']))); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		
		<!-- 診断分類別メニュー -->
		<div class="box">
			<h2 style="font-size:12px;">
				<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
				診断分類別患者数ランキング<?php echo $this->My->tip('診断分類別患者数ランキング', array('image'=>true)); ?>
			</h2>
			<div class="content">
				<ul class="basic">
					<?php foreach($dat['mdcs'] as $key => $m): ?>
						<li><?php echo $this->Html->link('MDC'.str_pad($key, 2, '0', STR_PAD_LEFT).' '.$m['name'], array('action'=>'Toplst', 'controller'=>'Home', '?'=>array('id'=>$m['id']))); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		
		<!-- 広告ユニット(未使用) -->
		<?php echo $this->element('ad_sidebar'); ?>
		
	</div>
	<!-- /サイドバー -->
</div>
