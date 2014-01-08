<?php $this->start('script'); ?>
<script>
(function(){

var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function AppModel(){
	var s = this;
	s.hospital = dat.hospital;
	s.hospitalsNearby = dat.hospitalsNearby;
}

var model = new AppModel();
ko.applyBindings(model);

})();
</script>
<?php $this->end(); ?>



<?php echo $this->element('additional_information'); ?>

<!-- Data -->
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
<!-- End Data -->
<!-- Start Ajax Add To Group -->
<div id="addToGroup">
	<h4> Add this Hospital to a group! </h4>
	<select id="groupDropdown">
	<?php
	foreach ($groups as $group) {
		?><option value='<?php echo $group["FavoriteHospital"]["id"]?>'><?php echo $group["FavoriteHospital"]["name"];?></option><?php
	}
	?>
	</select>
	<button id="submitToGroup">Add To Group</button>
	<p class="groupResult"></p>
</div>
<?php echo $this->Html->script('jquery-1.10.2.min'); ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {	
	$("#submitToGroup").click(function (event) {
		/*$.ajax({
			async:true, 
			data:$("#CarSeriesId").closest("form").serialize(), 
			dataType:"html", 
			evalScripts:true, 
			success:function (data, textStatus) {
				$("#CarBadgeId").html(data);
				$("#CarBadgeId").prepend('<option value="0"> - </option>');
				$("select#CarBadgeId").val("0");
				$("#CarBadgeId").show();
				$("label[for='CarBadgeId']").show();
			}, 
			url:"\/jdm_market\/series\/get_badges"
		});*/
		$("#addToGroup").slideUp();
	return false;});});
//]]>
</script>




<!-- Comments -->
<?php
	echo $this->element('fb_root');
	echo $this->element('fb_comments', array('commentUrl'=>Router::url(null, true)));
?>
