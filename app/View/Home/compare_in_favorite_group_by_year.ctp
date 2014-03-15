<?php $this->start('script'); ?>
<?php echo $this->Html->script('amcharts/amcharts'); ?>
<?php echo $this->Html->script('amcharts/serial'); ?>
<script>
var dat = JSON.parse('<?php echo json_encode($dat); ?>');

function AppModel(){
	var s = this;
	
	s.group = dat.group;			// お気に入りグループの情報
	s.ids = dat.ids;					// グループ内の医療機関ID一覧
	
	s.mdcs = dat.mdcs;												// 選択可能なMDC一覧
	s.displayTypes = dat.displayTypesForDpc;	// 選択可能な表示方法一覧
	
	s.selectedMdc = ko.observable(s.mdcs[0]);	// 選択されたMDC
	s.selectedDisplayType = ko.observable();	// 選択された表示方法
	
	s.chartData = ko.observableArray();
	s.chart = ko.observable();
	
	s.selectedMdc.subscribe(function(){
		// MDCが再選択されたため、データをAJAXでダウンロードする。
		s.GetData();
	});
	s.selectedDisplayType.subscribe(function(){
		// 表示方法が再選択されたため、グラフを描き直す。
		s.DrawChart();
	});
	
	s.GetData = function(){
		$.postJSON({
			url: dat.getDpcsUrl,
			data: {
				ids: s.ids,
				mdcId: s.selectedMdc().id
			}
		}).done(function(data){
			s.chartData(data.dpcs);
			s.DrawChart();
		});
	}
	
	s.DrawChart = function(){
		// Remember current settings
		var currentChart = s.chart();
		if(currentChart){
			for(var n=0; n<currentChart.graphs.length && n<s.group.Hospital.length; n++){
				var g = currentChart.graphs[n];
				var h = s.group.Hospital[n];
				h.hidden = g.hidden;
			}
		}
		
	  // SERIAL CHART
	  var chart = new AmCharts.AmSerialChart();
	  chart.dataProvider = s.chartData();	//dat.chartData;	//chartData;
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
		for(var n=0; n<s.group.Hospital.length; n++){
			var h = s.group.Hospital[n];
			var graph = new AmCharts.AmGraph();
		  graph.title = h.alias;//.name;
		  graph.valueField = h.wam_id + '.' + s.selectedDisplayType().id;
		  graph.hidden = h.hidden;
		  graph.balloonText = h.alias + " [[category]]: [[value]]";
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
}

var model = new AppModel();
ko.applyBindings(model);
model.GetData();
</script>
<?php $this->end(); ?>



<div class="box">
	<h2>
		グループ名：
		<span data-bind="text: group.FavoriteHospital.name"></span>
	</h2>
	<div class="content">
		診断分類：<span data-bind="text: selectedMdc().name"></span>
	</div>
</div>
<div id="chartdiv"></div>
<div class="box">
	<h2>表示切替</h2>
	<div class="content">
		<select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc"></select>
		<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>
		<p>グラフの病院名をクリックすると、表示の有無も切り替えられます。</p>
	</div>
</div>
