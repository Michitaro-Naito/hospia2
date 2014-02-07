<?php $this->start('script'); ?>
<?php echo $this->Html->script('amcharts/amcharts'); ?>
<?php echo $this->Html->script('amcharts/xy'); ?>
<script>
var dat = JSON.parse('<?php echo json_encode($dat); ?>');

function AppModel(){
	var s = this;
	
	s.group = dat.group;			// お気に入りグループの情報
	s.ids = dat.ids;					// グループ内の医療機関ID一覧
	
	s.mdcs = dat.mdcs;												// 選択可能なMDC一覧
	s.years = dat.years;											// 選択可能な会計年度一覧
	s.displayTypes = dat.displayTypesForDpc;	// 選択可能な表示方法一覧
	s.displayTypesForValue = [{id:'none', name:'指定なし'}].concat(dat.displayTypesForDpc);
	
	s.selectedMdc = ko.observable(s.mdcs[0]);					// 選択されたMDC
	s.selectedYear = ko.observable(s.years[0]);					// 選択された会計年度
	s.selectedDisplayTypeX = ko.observable(s.displayTypes[0]);	// 選択された表示方法(X軸)
	s.selectedDisplayTypeY = ko.observable(s.displayTypes[2]);	// 選択された表示方法(Y軸)
	s.selectedDisplayTypeValue = ko.observable(s.displayTypesForValue[0]);	// 選択された表示方法(大きさ)
	
	s.chartData = ko.observableArray();				// 7年分のデータ
	
	s.selectedMdc.subscribe(function(){
		// MDCが再選択されたため、データをAJAXでダウンロードする。
		s.GetData();
	});
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
		
		// AmChartsの形式に直す
		for(var n=0; n<s.ids.length; n++){
			var id = s.ids[n];
			var row = {};
			row.name = '';
			row.alias = '';
			for(var m=0; m<s.group.Hospital.length; m++){
				var h = s.group.Hospital[m];
				if(h.wam_id==id){
					row.name = h.name;
					row.alias = h.alias;
					break;
				}
			}
			row.x = dataOfYear[id + '.' + s.selectedDisplayTypeX().id];
			row.y = dataOfYear[id + '.' + s.selectedDisplayTypeY().id];
			if(s.selectedDisplayTypeValue().id == 'none')
				row.value = 1;
			else
				row.value = dataOfYear[id + '.' + s.selectedDisplayTypeValue().id];
			chartData.push(row);
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
		graph.balloonText = "[[name]]</br>x:<b>[[x]]</b> y:<b>[[y]]</b><br>value:<b>[[value]]</b>";
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



<div class="box">
	<h2>
		グループ名：
		<span data-bind="text: group.FavoriteHospital.name"></span>
	</h2>
	<div class="content">
		<span data-bind="text: selectedMdc().name"></span>
		・<span data-bind="text: selectedYear().name"></span>
		大きさ：<span data-bind="text: selectedDisplayTypeValue().name"></span>
	</div>
</div>
<div id="chartdiv" class="bubbles"></div>
<div class="box">
	<h2>表示切替</h2>
	<div class="content">
		<table class="table">
			<tbody>
				<tr>
					<th>診断分類</th>
					<td><select data-bind="options: mdcs, optionsText: 'name', value: selectedMdc"></select></td>
				</tr>
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
