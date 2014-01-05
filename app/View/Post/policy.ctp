<?php
	$rootURL = $this->webroot;
	$firstPost = $posts[0]['Post'];
?>

<div id="wrapper">

<div id="breadcrumb">
	<?php
		// ホームへのリンクの出力
		echo "　　<a href=\"".$rootURL."\">ホーム</a>";
		echo " &gt; ";
		// サイトポリシーと出力
		echo "サイトポリシー";
	?>
</div>

<div id="content">
<div id="innerbox">
<div class="pagebox">

<h2>サイトポリシー</h2>

<?php echo $firstPost['content']; ?>

</div><!-- END div.post -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
</div><!-- END div#wrapper -->