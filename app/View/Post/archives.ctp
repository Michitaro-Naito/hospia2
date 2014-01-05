<?php
	$rootURL = $this->webroot;
	$firstPost = $posts[0]['Post'];
	$categoryDisp = Configure::read("categoryDisp");
?>

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
			$strHref = $rootURL."wp/archives/category/".$strCategory."/";
			echo " ";
			echo "<a href=\"".$strHref."\" title=\"".$categoryDisp[$strCategory]." の投稿をすべて表示\" rel=\"category tag\">";
			echo $categoryDisp[$strCategory];
			echo "</a>";
			if ($index < $size - 1) {
				echo ",";
			}
		}
		echo " &gt;";
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

</div><!-- END div.post -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->