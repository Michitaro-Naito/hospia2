<?php if(!$isPremiumUser && empty($noAds)): ?>
	<?php if($this->request->is('mobile')): ?>
		<div class="row" style="text-align: center; margin-top: 10px;">
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- hospia_sp1 -->
			<ins class="adsbygoogle"
			     style="display:inline-block;width:300px;height:250px"
			     data-ad-client="ca-pub-4974907436165676"
			     data-ad-slot="7433520957"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
	<?php else: ?>
<div class="" style="margin: 10px 0">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- hospia_rectangle_large -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px;margin-left:10px;margin-right:20px;"
     data-ad-client="ca-pub-4974907436165676"
     data-ad-slot="4229596559"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- hospia_rectangle_large -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px"
     data-ad-client="ca-pub-4974907436165676"
     data-ad-slot="4229596559"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>
	<?php endif; ?>
<?php else: ?>
	<!-- 広告がない場合の下部のマージン -->
	<div style="height: 20px;"> </div>
<?php endif; ?>