<div id="fb-root"></div>
<?php $this->append('script'); ?>
<script>
	function ShowFeedDialog(){
		// FB not initialized yet.
	}
	
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '371990586279310',
      status     : true,
      xfbml      : true
    });
    ShowFeedDialog = function(){
			FB.ui(
	   {
	     method: 'feed',
	     name: '<?php echo h($this->fetch('title')); ?>'+' - 病院情報局',
	     link: '<?php echo h(Router::url(null, true)); ?>',	//'http://hospia.jp/',
	     picture: '<?php echo h(Router::url('/img/logo_fb.png', true)); ?>',	//'http://hospia.jp/img/logo_fb.png',
	     description: '全国の急性期病院の診療実績（患者数、平均在院平均など）を比較'
	   },
	   function(response) {
	     if (response && response.post_id) {
	       // Tries to get insentive...
	       $.ajax({
	       	cache: false,
	       	type: 'POST',
	       	dataType: 'JSON',
	       	url: '<?php echo h(Router::url('/Ajax/GetInsentive.json')); ?>'
	       }).done(function(data){
	       	if(data.result.success){
	       		// Succeeded. Shows a dialog...
	       		$('#FbFeedModal .hours').text(data.result.hours);
	       		$('#FbFeedModal .until').text(data.result.until);
	       		$('#FbFeedModal .count').text(data.result.count);
	       		$('#FbFeedModal .max').text(data.result.max);
    				$('#FbFeedModal').modal('show');
	       	}
	       });
	     }
	   }
	   );
    }
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/ja_JP/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
	
	$(document).ready(function(){
		$('#fb-feed').click(function(){
			ShowFeedDialog();
		});
		$('#FbFeedModal').on('hidden.bs.modal', function () {
		    // do something…
		    location.reload();
		});
	});
</script>
<?php $this->end(); ?>
<script>
</script>