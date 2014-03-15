<?php $this->start('script'); ?>
<?php echo $this->Html->script('amcharts/amcharts'); ?>
<?php echo $this->Html->script('amcharts/xy'); ?>
<script>
var dat = JSON.parse('<?php echo json_encode($dat); ?>');

function AppModel(){
	var s = this;
	
	s.wamId = dat.wamId;
	s.hospital = dat.hospital;
	s.mdcs = dat.mdcs;
	s.years = dat.years;											// 選択可能な会計年度一覧
	s.displayTypes = dat.displayTypesForDpc;	// 選択可能な表示方法一覧
	s.displayTypesForValue = [{id:'none', name:'指定なし'}].concat(dat.displayTypesForDpc);
	
	s.selectedYear = ko.observable(s.years[0]);					// 選択された会計年度
	s.selectedDisplayTypeX = ko.observable(s.displayTypes[1]);	// 選択された表示方法(X軸)
	s.selectedDisplayTypeY = ko.observable(s.displayTypes[2]);	// 選択された表示方法(Y軸)
	s.selectedDisplayTypeValue = ko.observable(s.displayTypesForValue[1]);	// 選択された表示方法(大きさ)
	
	s.chartData = ko.observableArray();				// 7年分のデータ
	
	// 表示方法が再選択されたため、グラフを描き直す。
	s.selectedYear.subscribe(function(){
		s.DrawChart();
	});
	s.selectedDisplayTypeX.subscribe(function(){
		s.DrawChart();
	});
	s.selectedDisplayTypeY.subscribe(function(){
		s.DrawChart();
	});
	s.selectedDisplayTypeValue.subscribe(function(){
		s.DrawChart();
	});
	
	s.GetData = function(){
		console.info('Get');
		$.postJSON({
			url: dat.getDpcsUrl,
			data: {
				wamId: s.wamId,
				year: s.selectedYear().id
			}
		}).done(function(data){
			console.info(data);
			s.chartData(data.dpcs);
			s.DrawChart();
		});
	}
	
	s.DrawChart = function(){
		// Format s.chartData() to display
		var chartData = [];
		
		// 7年分のデータから指定された1年分を取り出す
		var dataOfYear;
		for(var n=0; n<s.chartData().length; n++){
			var data = s.chartData()[n];
			if(data.year == s.selectedYear().id){
				dataOfYear = data;
				break;
			}
		}
		if(typeof dataOfYear === 'undefined'){
			return;
		}
		console.info(dataOfYear);
		
		// AmChartsの形式に直す
		for(var n=0; n<s.mdcs.length; n++){
			var mdc = s.mdcs[n];
			var row = {};
			row.name = mdc.name;
			row.alias = mdc.name;
			row.x = dataOfYear[mdc.id + '.' + s.selectedDisplayTypeX().id];
			row.y = dataOfYear[mdc.id + '.' + s.selectedDisplayTypeY().id];
			if(s.selectedDisplayTypeValue().id == 'none')
				row.realValue = 1;
			else
				row.realValue = dataOfYear[mdc.id + '.' + s.selectedDisplayTypeValue().id];
			chartData.push(row);
		}
		
		// 表示される大きさを正規化する。
		var maxValue = 1.0;
		for(var n=0; n<chartData.length; n++){
			var row = chartData[n];
			if(row.value > maxValue)
				maxValue = row.realValue;
		}
		for(var n=0; n<chartData.length; n++){
			chartData[n].value = chartData[n].realValue / maxValue;
		}
		
		// XY Chart
		chart = new AmCharts.AmXYChart();
		//chart.pathToImages = "../amcharts/images/";
		chart.dataProvider = chartData;
		chart.startDuration = 1.5;
		
		// AXES
		// X
		var xAxis = new AmCharts.ValueAxis();
		xAxis.title = s.selectedDisplayTypeX().name;
		xAxis.position = "bottom";
		xAxis.autoGridCount = true;
		chart.addValueAxis(xAxis);
		
		// Y
		var yAxis = new AmCharts.ValueAxis();
		yAxis.title = s.selectedDisplayTypeY().name;
		yAxis.position = "left";
		yAxis.autoGridCount = true;
		chart.addValueAxis(yAxis);
		
		// GRAPH
		var graph = new AmCharts.AmGraph();
		graph.valueField = "value"; // valueField responsible for the size of a bullet
		graph.xField = "x";
		graph.yField = "y";
		graph.lineAlpha = 0;
		graph.bullet = "bubble";
		graph.balloonText = "[[name]]</br>x:<b>[[x]]</b> y:<b>[[y]]</b><br>value:<b>[[realValue]]</b>";
		graph.labelText = "[[alias]]";
		chart.addGraph(graph);
		
		// WRITE                                
		chart.write("chartdiv");
	}
}

var model = new AppModel();
ko.applyBindings(model);
model.GetData();
</script>
<?php $this->end(); ?>



<?php echo $this->element('hosdetail_menu'); ?>
<span data-bind="text: selectedYear().name"></span>
大きさ：<span data-bind="text: selectedDisplayTypeValue().name"></span>
<div id="chartdiv" class="bubbles"></div>
<div class="box">
	<h2>表示切替</h2>
	<div class="content">
		<table class="table">
			<tbody>
				<tr>
					<th>年度</th>
					<td><select data-bind="options: years, optionsText: 'name', value: selectedYear"></select></td>
				</tr>
				<tr>
					<th>X軸</th>
					<td><select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayTypeX"></select></td>
				</tr>
				<tr>
					<th>Y軸</th>
					<td><select data-bind="options: displayTypes, optionsText: 'name', value: selectedDisplayTypeY"></select></td>
				</tr>
				<tr>
					<th>大きさ</th>
					<td><select data-bind="options: displayTypesForValue, optionsText: 'name', value: selectedDisplayTypeValue"></select></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
