<?php $this->start('script'); ?>
<script>
	$(document).ready(function(){
		var progress = 0.0;
		window.setInterval(function(){
			progress += 100.0 / 60.0;
			$('.progress-bar').css('width', progress + '%');
			if(progress >= 100.0){
				// Redirects User to /User/Subscribe
				location.href = '<?php echo Router::url(array('controller'=>'Users', 'action'=>'Subscribe')); ?>';
			}
		}, 1000);
	});
</script>
<?php $this->end(); ?>



<h2>処理中</h2>
<p>
	処理中<br/>
	大切な処理を行っています。このままでしばらくお待ちください...<br/>
	（最長で60秒ほどかかります）
</p>
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
    <span class="sr-only">0% Complete</span>
  </div>
</div>
