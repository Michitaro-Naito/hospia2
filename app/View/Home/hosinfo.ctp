<?php
	$title = '診療実績';
	if(!empty($dat['hospital']['Hospital']['name']))
		$title = $dat['hospital']['Hospital']['name'];
	$this->assign('title', $title);
?>
<?php $this->start('script'); ?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script>
(function(){
// Gets Data from Server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

// Google Maps v3
var map;
google.maps.event.addDomListener(window, 'load', function(){
	// Creates a map
	var options = {
		zoom: 12,
		center: new google.maps.LatLng(dat.hospital.Coordinate.latitude, dat.hospital.Coordinate.longitude)
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), options);
	
	// Adds markers (a marker represents a hospital)
	function CreateMarker(h, icon){
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(h.Coordinate.latitude, h.Coordinate.longitude),
			map: map,
			title: h.Hospital.name,
			icon: icon
		});
		return marker;
	}
	CreateMarker(dat.hospital, 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=・|FEEB00|000000');
	for(var n=0; n<dat.hospitalsNearby.length; n++){
		var h = dat.hospitalsNearby[n];
		var marker = CreateMarker(h, 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + (n+1) + '|FF766A|000000');
	}
	
	// Initializes a map
	/*google.maps.event.addListener(marker, 'click', function(){
		map.setZoom(12);
		map.setCenter(marker.getPosition());
	});*/
});

})();
</script>
<?php $this->end(); ?>




<?php echo $this->element('hosdetail_menu'); ?>
<?php echo $this->element('additional_information'); ?>
<?php echo $this->element('favorite', array('wamId'=>$dat['wamId'], 'compact'=>true)); ?>
<!-- Data -->
<div id="hosinfo">
	<?php
		$h = $dat['hospital'];
		if(!empty($h)):
	?>
		<div class="row">
			<div class="col-sm-6 left">
				<div class="address"><?php echo h($h['Area']['addr1'].$h['Area']['addr2'].$h['Hospital']['addr3']); ?></div>
				<div class="tel">TEL: <?php echo h($h['Hospital']['tel']); ?></div>
				<?php if(!empty($h['Hospital']['url'])): ?>
					<div class="url"><?php echo $this->Html->link('病院ホームページ', $h['Hospital']['url']); ?></div>
				<?php endif; ?>
				<?php if(!empty($h['Jcqhc']['url'])): ?>
					関連サイト<br/>
					都道府県 医療機能情報サイト<?php echo $this->My->tip('基本情報-都道府県医療機能情報サイト'); ?>
					<div class="jcqhc"><?php echo $this->Html->link('医療機能評価機構 審査結果', $h['Jcqhc']['url']); ?><?php echo $this->My->tip('基本情報-医療機能評価機構審査結果'); ?></div>
				<?php endif; ?>
			</div>
			
			<div class="col-sm-6 right">
				<table>
					<tr>
						<td rowspan="2">病床数</td>
						<td>総病床数<?php echo $this->My->tip('総病床数'); ?></td>
						<td><?php echo h($h['Hospital']['bed']); ?>床</td>
					</tr>
					<tr>
						<td>うち一般病床数<?php echo $this->My->tip('一般病床数'); ?></td>
						<td><?php echo h($h['Hospital']['general']); ?>床</td>
					</tr>
					<tr>
						<td rowspan="2">職員数</td>
						<td>医師数<?php echo $this->My->tip('医師数'); ?></td>
						<td><?php echo h($h['Hospital']['doctor']); ?>人</td>
					</tr>
					<tr>
						<td>看護師数<?php echo $this->My->tip('看護師数'); ?></td>
						<td><?php echo h($h['Hospital']['nurse']); ?>人</td>
					</tr>
					<tr>
						<td rowspan="2">1日平均患者数</td>
						<td>入院患者数(一般病床)<?php echo $this->My->tip('入院患者数(一般病床)'); ?></td>
						<td><?php echo h($h['Hospital']['patient']); ?>人</td>
					</tr>
					<tr>
						<td>外来患者数<?php echo $this->My->tip('外来患者数'); ?></td>
						<td><?php echo h($h['Hospital']['outpatient']); ?>人</td>
					</tr>
					<tr>
						<td rowspan="2">病院機能評価<?php echo $this->My->tip('基本情報-病院機能評価'); ?></td>
						<td>当初認定日</td>
						<td><?php
							if(!empty($h['Jcqhc']['first_rd'])){
								$dt = new DateTime($h['Jcqhc']['first_rd']);
								echo h($dt->format('Y-m-d'));
							}
						?></td>
					</tr>
					<tr>
						<td>最新認定日</td>
						<td><?php
							if(!empty($h['Jcqhc']['latest_rd'])){
								$dt = new DateTime($h['Jcqhc']['latest_rd']);
								echo h($dt->format('Y-m-d'));
							}
						?></td>
					</tr>
				</table>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="row">
		<div class="col-sm-3 left">
			<h3>周辺の急性期病院<?php echo $this->My->tip('基本情報-周辺の急性期病院'); ?></h3>
			<ul class="hospitals-nearby">
				<?php foreach($dat['hospitalsNearby'] as $key => $h): ?>
					<li><?php echo h($key+1 . ' '); echo $this->Html->link($h['Hospital']['alias'], '/hosdetail/' . $h['Hospital']['wam_id']); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		
		<div class="col-sm-9 right">
			<div id="map-canvas" style="width: 100%; height: 500px; border: 1px solid black;">Loading...</div>
		</div>
	</div>
</div>

<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url(null, true)));
?>
