<table class="social-container">
	<tr>
		<td>
			<!-- Facebook Like -->
			<div class="fb-follow-container-post">
				<div class="fb-like"
					data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
			</div>
		</td>
		<td>
			<button type="button" id="fb-feed">シェア</button>
			<!-- モーダル(新しいグループ) -->
			<div class="modal fade" id="FbFeedModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        <h4 class="modal-title" id="myModalLabel">ありがとうございます</h4>
			      </div>
			      <div class="modal-body">
			      	<p>
			      		無料でプレミアム機能をご利用いただける権利を、<span class="hours"></span>時間設定(延長)させていただきました。<br/>
			      		<span class="until"></span>ごろまでプレミアム機能をご利用いただけます。<br/>
			      		現在までの獲得回数：<span class="count"></span>回<br/>
			      		最大獲得回数：<span class="max"></span>回
			      	</p>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-primary" data-dismiss="modal">了解</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</td>
		<td>
			<!-- Twitter Tweet -->
			<div class="twitter-tweet-container-post">
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-via="hospia_jp" data-text="<?php echo $twitterText; ?>">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
		</td>
	</tr>
</table>

<?php if(!empty($commentUrl)): ?>
	<div class="fbcomment">この病院に関するコメント</div>
	<div class="fb-comments"
		<?php if(!$this->request->is('mobile')): ?>
			data-width="718"
		<?php endif; ?>
		data-href="<?php echo $commentUrl; ?>" data-numposts="5" data-colorscheme="light"></div>
<?php endif; ?>