<?php $this->start('script'); ?>
<?php echo $this->Html->script('amcharts/amcharts'); ?>
<?php echo $this->Html->script('amcharts/serial'); ?>
<script>
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function AppModel(){
	var s = this;
	
	s.hospital = dat.hospital;
	s.displayTypes = dat.displayTypesForDpc;
	s.selectedDisplayType = ko.observable();
	s.mdcs = ko.observableArray(dat.mdcs);
	s.chart = ko.observable();
	
	s.selectedDisplayType.subscribe(function(){
		s.DrawChart();
	}, this);
	
	s.DrawChart = function(){
		// Remember current settings
		var currentChart = s.chart();
		var hiddenFlags = []
		if(currentChart){
			for(var n=0; n<currentChart.graphs.length && n<s.mdcs().length; n++){
				var g = currentChart.graphs[n];
				var mdc = s.mdcs()[n];
				mdc.hidden = g.hidden;
			}
		}
		
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
	  for(var n=0; n<s.mdcs().length; n++){
	  	var mdc = dat.mdcs[n];
	  	var graph = new AmCharts.AmGraph();
		  graph.title = mdc.name;
		  graph.valueField = n + '.' + s.selectedDisplayType().id;	//'.ave_in';
		  graph.hidden = mdc.hidden;
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
	  
	  s.chart(chart);
	}
	
	// Default values
	for(var n=0; n<s.mdcs().length; n++){
		var mdc = s.mdcs()[n];
		mdc.hidden = true;
		if(n==0)
			mdc.hidden = false;
	}
}

var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<?php echo $this->element('hosdetail_menu'); ?>
<div id="chartdiv"></div>
<div class="box">
	<h2>表示切替</h2>
	<div class="content">
		<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>
		<p>グラフの診断分類名をクリックすると、表示の有無も切り替えられます。</p>
	</div>
</div>
