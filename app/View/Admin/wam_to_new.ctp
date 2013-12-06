<h1>WamIdToNewId</h1>
<?php //debug($rows); ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		var url = '<?php echo $this->Html->url('/Admin/WamToNewProcess.json'); ?>';
		var page = 0;
		
		function showMessage(message){
			$('#note').prepend($('<div>').text(message));
		}
		
		function process(){
			showMessage(page+'...');
			$.ajax({
				cache: false,
				type: 'POST',
				url: url,
				data: {
					page: page
				}
			}).done(function(data){
				showMessage('done');
				console.info(data);
				page++;
				//if(count >= 10) return;
				if(data.count == 0) return;
				if(page >= 1000) return;
				process();
			}).fail(function(){
				showMessage('fail');
			});
		}
		
		$('#start').click(function(){
			process();
		});
		
		$('#stop').click(function(){
			
		});
	});
</script>
<button type="button" id="start">Start</button>
<!--<button type="button" id="stop">Stop</button>-->
<div id="note"></div>
