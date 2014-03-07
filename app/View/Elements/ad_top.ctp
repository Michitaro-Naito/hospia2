<?php if(!$isPremiumUser && empty($noAds)): ?>
	<?php if($this->request->is('mobile')): ?>
		<div class="row" style="text-align: center;">
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- hospia_mobile -->
		<ins class="adsbygoogle"
		     style="display:inline-block;width:320px;height:50px"
		     data-ad-client="ca-pub-4974907436165676"
		     data-ad-slot="5956787750"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
		</div>
	<?php else: ?>
<div class="row" style="margin-top: 5px; margin-bottom: 10px; text-align: center;">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-4974907436165676";
/* hospia_top_t */
google_ad_slot = "2874601085";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
	<?php endif; ?>
<?php endif; ?>