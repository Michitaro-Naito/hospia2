<?php $this->start('script'); ?>
<?php echo $this->Html->script('amcharts/amcharts'); ?>
<?php echo $this->Html->script('amcharts/serial'); ?>
<script>
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
//console.info(dat);

function AppModel(){
	var s = this;
	
	s.group = dat.group;			// お気に入りグループの情報
	s.ids = dat.ids;					// グループ内の医療機関ID一覧
	
	s.mdcs = dat.mdcs;												// 選択可能なMDC一覧
	s.displayTypes = dat.displayTypesForDpc;	// 選択可能な表示方法一覧
	
	s.selectedMdc = ko.observable();					// 選択されたMDC
	s.selectedDisplayType = ko.observable();	// 選択された表示方法
	
	s.chartData = ko.observableArray();
	
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
		  graph.title = h.name;
		  graph.valueField = h.wam_id + '.' + s.selectedDisplayType().id;
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



<div data-bind="text: group.FavoriteHospital.name"></div>
<div id="chartdiv" style="width: 100%; height: 362px;"></div>
<select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc"></select>
<select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayType"></select>