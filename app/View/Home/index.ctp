<?php //echo $this->element('debug'); ?>



<?php
	$this->set('is_top', TRUE);
?>
<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function AppModel(){
	var s = this;
	
	s.prefectures = dat.prefectures;
	s.zones = ko.observableArray();
	s.hospitalName = ko.observable();
	
	s.selectedPrefecture = ko.observable();
	s.selectedZone = ko.observable();
	
	// 選択された都道府県に合わせて医療圏を再読み込み
	s.selectedPrefecture.subscribe(function(newValue){
		if(newValue.id !== null){
			$.postJSON({
				url: dat.getZonesUrl,
				data: {
					prefectureId: newValue.id
				}
			}).done(function(data){
				s.zones(data.zones);
			});
		}
	});
	
	// hoslstへページ移動する。その際、選択された都道府県・医療圏・病院名を渡す。
	s.GotoHoslst = function(){
		var uri = new Uri(dat.hoslistUrl);
		uri.replaceQueryParam('prefectureId', s.selectedPrefecture().id);
		if(s.selectedZone()) uri.replaceQueryParam('zoneId', s.selectedZone().id);
		uri.replaceQueryParam('hospitalName', s.hospitalName());
		window.location.href = uri.toString();
	}
}

var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<?php echo $this->element('fb_root'); ?>

<div class="row">
	
	<!-- 検索 -->
	<div class="col-sm-6">
		<div class="box">
			<h2>病院検索</h2>
			<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
			医療圏<?php echo $this->My->tip('医療圏'); ?>：
			<select data-bind="options: zones, optionsText: 'name', value: selectedZone"></select>
			<input type="text" data-bind="value: hospitalName"/>
			<button type="button" data-bind="click: GotoHoslst">検索</button>
		</div>
	</div>
	
	<!-- ご利用ガイド -->
	<div class="col-sm-6">ご利用ガイド</div>
	
</div>

<div class="row">
	<div class="col-sm-9">
		
		<!-- 新着情報 -->
		<div class="box">
			<h2>新着情報</h2>
			<div class="content">
				<?php foreach($recentPosts as $p): ?>
					<div><?php echo $this->Html->link($p['Post']['title'], "/wp/archives/{$p['Post']['post_id']}"); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		
		<!-- ソーシャル連携 -->
		<div class="">
			<?php
				echo $this->element('twitter_follow');
				echo $this->element('fb_follow');
			?>
		</div>
		
		<!-- 最近チェックした病院　と　閲覧数の多い病院 -->
		<div class="row">
  		<div class="col-sm-6">
  			<div class="box">
	  			<h2>最近チェックした病院</h2>
	  			<ul>
		  			<?php foreach($dat['rememberedHospitals'] as $h): ?>
		  				<li><?php echo h($h['Hospital']['name']); ?></li>
		  			<?php endforeach; ?>
	  			</ul>
  			</div>
  		</div>
  		
  		<div class="col-sm-6">
  			<div class="box">
	  			<h2>閲覧数の多い病院</h2>
	  			<ul>
	  				<?php foreach($dat['hospitalsMostViewed'] as $h): ?>
	  					<li><?php echo h($h['Hospital']['name'] . $h[0]['sum']); ?></li>
	  				<?php endforeach; ?>
	  			</ul>
  			</div>
  		</div>
		</div>
		
		<!-- お気に入りグループ一覧 -->
		<div class="box">
			<h2>お気に入りグループ一覧</h2>
		</div>
		
	</div>
	
	<!-- サイドバー -->
	<div class="col-sm-3 bs-sidebar">
		
		<!-- 疾患別メニュー -->
		<div class="box">
			<h2>主な疾患別患者数ランキング</h2>
			<ul>
				<?php foreach($dat['maladyCategories'] as $key => $c): ?>
					<li><?php echo $this->Html->link($c['name'], array('action'=>'Maladylist', 'controller'=>'Home', '?'=>array('mdata'=>$c['id']))); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		
		<!-- 診断分類別メニュー -->
		<div class="box">
			<h2>診断分類別患者数ランキング</h2>
			<ul>
				<?php foreach($dat['mdcs'] as $key => $m): ?>
					<li><?php echo $this->Html->link('MDC'.str_pad($key, 2, '0', STR_PAD_LEFT).' '.$m['name'], array('action'=>'Toplst', 'controller'=>'Home', '?'=>array('id'=>$m['id']))); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		
		<!-- 広告ユニット(未使用) -->
		<?php echo $this->element('ad_sidebar'); ?>
		
	</div>
	<!-- /サイドバー -->
</div>
