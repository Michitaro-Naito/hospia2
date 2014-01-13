<?php $this->start('script'); ?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script>
(function(){
// Gets Data from Server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

// knockout.js
function AppModel(){
	var s = this;
	s.hospital = dat.hospital;
	s.hospitalsNearby = dat.hospitalsNearby;
}

var model = new AppModel();
ko.applyBindings(model, document.getElementById('hosinfo'));

// Google Maps v3
var map;
google.maps.event.addDomListener(window, 'load', function(){
	// Creates a map
	var options = {
		zoom: 8,
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
	google.maps.event.addListener(marker, 'click', function(){
		map.setZoom(8);
		map.setCenter(marker.getPosition());
	});
});

})();
</script>
<?php $this->end(); ?>



<?php echo $this->element('additional_information'); ?>

<!-- Data -->
<div id="hosinfo">
	<div data-bind="visible: hospital !== null, with: hospital">
		<div data-bind="text: Hospital.name"></div>
		<div data-bind="text: Hospital.wam_id"></div>
		<div data-bind="text: Hospital.addr3"></div>
		<div data-bind="text: Hospital.tel"></div>
		<div data-bind="text: Hospital.url"></div>
		<div data-bind="text: Jcqhc.url"></div>
		<div data-bind="text: Hospital.bed"></div>
		<div data-bind="text: Hospital.general"></div>
		<div data-bind="text: Hospital.doctor"></div>
		<div data-bind="text: Hospital.nurse"></div>
		<div data-bind="text: Hospital.patient"></div>
		<div data-bind="text: Hospital.outpatient"></div>
		<div data-bind="text: Jcqhc.first_rd"></div>
		<div data-bind="text: Jcqhc.latest_rd"></div>
	</div>
	
	<ul data-bind="foreach: hospitalsNearby">
		<li>
			<span data-bind="text: Hospital.name"></span>
		</li>
	</ul>
	
	<div id="map-canvas" style="width: 590px; height: 590px; border: 1px solid black;">Loading...</div>
</div>

<?php echo $this->element('favorite', array('wamId'=>$dat['wamId'])); ?>

<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url(null, true)));
?>
