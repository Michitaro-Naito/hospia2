<?php
	$rootURL = $this->webroot;
	$firstPost = $posts[0]['Post'];
	$categoryDisp = Configure::read("categoryDisp");
	$this->assign('title', $firstPost['title']);
?>

<?php echo $this->element('fb_root'); ?>

<div id="wrapper">

<div id="breadcrumb">
	<?php
		// ホームへのリンクの出力
		echo "　　<a href=\"".$rootURL."\">ホーム</a>";
		echo " &gt;";
		// カテゴリへのリンクの出力
		$strHref = "";
		$strCategory = "";
		for($index = 0, $size = count($posts); $index < $size; $index++){
			$strCategory = $posts[$index]['Post']['category'];
			if($strCategory == 'news')
				continue;
			$strHref = $rootURL."wp/archives/category/".$strCategory."/";
			echo " ";
			echo "<a href=\"".$strHref."\" title=\"".$categoryDisp[$strCategory]." の投稿をすべて表示\" rel=\"category tag\">";
			echo $categoryDisp[$strCategory];
			echo "</a>";
			if ($index < $size - 1) {
				echo ",";
			}
			echo " &gt;";
		}
		// パンくずの最後の記事のタイトルを出力
		echo " ";
		echo $firstPost['title'];
	?>
</div>

<div id="content">
<div id="innerbox">
<div class="pagebox">

<h2 class="posttitle">
	<a href="<?php echo $rootURL."wp/archives/".$firstPost['post_id']; ?>">
		<?php echo $firstPost['title']; ?>
	</a>
</h2>

<?php echo $firstPost['content']; ?>
<div>　</div>

<hr>

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
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-via="hospia_jp" data-text="<?php echo $firstPost['title']; ?>">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
		</td>
	</tr>
</table>

</div><!-- END div.post -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->