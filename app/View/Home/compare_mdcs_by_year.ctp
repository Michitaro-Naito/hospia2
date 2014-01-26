<?php $this->start('script'); ?>
<?php echo $this->Html->script('amcharts/amcharts'); ?>
<?php echo $this->Html->script('amcharts/serial'); ?>
<script>
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function AppModel(){
	var s = this;
	
	s.displayTypes = dat.displayTypesForDpc;
	s.selectedDisplayType = ko.observable();
	
	s.selectedDisplayType.subscribe(function(){
		s.DrawChart();
	}, this);
	
	s.DrawChart = function(){
	  // SERIAL CHART
	  var chart = new AmCharts.AmSerialChart();
	  chart.dataProvider = dat.chartData;	//chartData;
	  chart.categoryField = "year";
	  chart.startDuration = 0.5;
	  chart.balloon.color = "#000000";
	  chart.sequencedAnimation = false;
	  
	  // AXES
	  // category
	  var categoryAxis = chart.categoryAxis;
	  categoryAxis.fillAlpha = 1;
	  categoryAxis.fillColor = "#FAFAFA";
	  categoryAxis.gridAlpha = 0;
	  categoryAxis.axisAlpha = 0;
	  categoryAxis.gridPosition = "start";
	  categoryAxis.position = "top";
	  
	  // value
	  var valueAxis = new AmCharts.ValueAxis();
	  valueAxis.title = s.selectedDisplayType().name;//"Place taken";
	  valueAxis.dashLength = 5;
	  valueAxis.axisAlpha = 0;
	  //valueAxis.minimum = 1;
	  //valueAxis.maximum = 6;
	  valueAxis.integersOnly = true;
	  valueAxis.gridCount = 10;
	  valueAxis.reversed = false; // this line makes the value axis reversed
	  chart.addValueAxis(valueAxis);
	  
	  // GRAPHS
	  for(var n=0; n<dat.mdcs.length; n++){
	  	var mdc = dat.mdcs[n];
	  	var graph = new AmCharts.AmGraph();
		  graph.title = mdc.name;
		  graph.valueField = n + '.' + s.selectedDisplayType().id;	//'.ave_in';
		  //graph.hidden = true; // this line makes the graph initially hidden           
		  graph.balloonText = dat.mdcs[n].name + " [[category]]: [[value]]";
		  graph.lineAlpha = 1;
		  graph.bullet = "round";
		  chart.addGraph(graph);
	  }
	  
	  // CURSOR
	  var chartCursor = new AmCharts.ChartCursor();
	  chartCursor.cursorPosition = "mouse";
	  chartCursor.zoomable = false;
	  chartCursor.cursorAlpha = 0;
	  chart.addChartCursor(chartCursor);                
	  
	  // LEGEND
	  var legend = new AmCharts.AmLegend();
	  legend.useGraphSettings = true;
	  chart.addLegend(legend);
	  
	  // WRITE
	  chart.write("chartdiv");
	}
}

var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<div id="chartdiv"></div>
<div class="box">
	<h2>表示切替</h2>
	<div class="content">
		<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>
	</div>
</div>
