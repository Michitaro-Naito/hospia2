<?php
	$rootURL = $this->webroot;
	$categoryDisp = Configure::read("categoryDisp");
?>

<div id="wrapper">

<div id="breadcrumb">
	<?php
		// ホームへのリンクの出力
		echo "　　<a href=\"".$rootURL."\">ホーム</a>";
		echo " &gt; ";
		// カテゴリ名の出力
		echo "［".$categoryDisp[$category]."］一覧";
	?>
</div>

<div id="content">
<div id="innerbox">

<?php
	foreach($posts as $post):
	$postURL = $rootURL."wp/archives/".$post['Post']['post_id'];
?>
<div class="pagebox">

<h2 class="posttitle">
	<a href="<?php echo $postURL; ?>">
		<?php echo $post['Post']['title']; ?>
	</a>
</h2>

<?php
	$content = $post['Post']['content'];
	$more = '<!--more-->';
	if (stristr($content, $more)) {
		$parts = explode($more, $content);
		echo $parts[0];
		echo "<a href=\"".$postURL."\" class=\"more-link\">続きを読む &raquo;</a>";
	} else {
		echo $content;
	}
?>

</div><!-- END div.post -->
<?php endforeach; ?>

</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->