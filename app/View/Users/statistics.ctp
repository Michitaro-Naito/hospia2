<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		var url = '<?php echo $this->Html->url('/Users/StatisticsGetUsers.json'); ?>';
		var page = 0;
		var sum = {
			total: 0,
			normal: 0,
			premium: 0
		};
		
		function showMessage(message){
			$('#note').prepend($('<div>').text(message));
		}
		
		function process(){
			showMessage(page+'ページ目...');
			$.ajax({
				cache: false,
				type: 'POST',
				url: url,
				data: {
					page: page
				}
			}).done(function(data){
				console.info(data);
				sum.total += data.users.length;
				for(var n=0; n<data.users.length; n++){
					var u = data.users[n];
					if(u.Subscription.length > 0)
						sum.premium++;
					else
						sum.normal++;
				}
				
				showMessage('OK');
				page++;
				if(data.users.length == 0){
					showMessage('集計が完了しました。');
					showMessage('総会員数：' + sum.total + '名');
					showMessage('通常会員数：' + sum.normal + '名');
					showMessage('プレミアム会員数：' + sum.premium + '名');
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
<h1>ユーザー集計ツール</h1>
<p>
	開始を押すと通常会員とプレミアム会員を集計し、それぞれの総数を画面に表示します。<br/>
	完了するまでページを開いたまましばらくお待ちください。<br/>
	計算中にページを閉じたり移動すると計算は中断されますが、データが破損する心配はありません。
</p>
<button type="button" id="start">開始</button>
<div id="note">
</div>
