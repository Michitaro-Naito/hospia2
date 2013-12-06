<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		var url = '<?php echo $this->Html->url('/Admin/DistanceProcess.json'); ?>';
		var page = 0;
		
		function showMessage(message){
			$('#note').prepend($('<div>').text(message));
		}
		
		function process(){
			showMessage(page+'行目の病院について計算開始...');
			$.ajax({
				cache: false,
				type: 'POST',
				url: url,
				data: {
					page: page
				}
			}).done(function(data){
				showMessage('OK');
				console.info(data);
				page++;
				if(data.count == 0){
					showMessage('完了しました。');
					return;
				}
				if(page>=10000) return;
				//if(page >= 10000) return;
				process();
			}).fail(function(){
				showMessage('失敗しました。');
			});
		}
		
		$('#start').click(function(){
			process();
		});
	});
</script>

<?php echo $this->Html->link('戻る', '/admin'); ?>
<h1>距離再計算ツール</h1>
<p>
	開始を押すとCoordinatesテーブルから病院間の距離を再計算し、結果をDistanceテーブルへ格納します。<br/>
	完了するまでページを開いたまましばらくお待ちください。<br/>
	計算中にページを閉じたり移動すると計算は中断されますが、データが破損する心配はありません。
</p>
<button type="button" id="start">開始</button>
<div id="note"></div>
