<table class="social-container">
	<tr>
		<td>
			<!-- Facebook Like -->
			<div class="fb-follow-container-post">
				<div class="fb-like"
					data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
			</div>
		</td>
		<td>
			<!-- Twitter Tweet -->
			<div class="twitter-tweet-container-post">
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-via="hospia_jp" data-text="<?php echo $hospitalName; ?>">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
		</td>
	</tr>
</table>

<div class="fbcomment">Facebookでこの病院の情報を共有する</div>
<div class="fb-comments"
	<?php if(!$this->request->is('mobile')): ?>
		data-width="718"
	<?php endif; ?>
	data-href="<?php echo $commentUrl; ?>" data-numposts="5" data-colorscheme="light"></div>