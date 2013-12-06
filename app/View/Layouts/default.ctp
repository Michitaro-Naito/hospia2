<?php echo $this->Html->docType(); ?>
<html>
  <head>
  	<?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Starter Template for Bootstrap</title>

    <?php
			echo $this->Html->meta('icon');
    	echo $this->Html->css('bootstrap.min');			// Bootstrap core CSS
			echo $this->Html->css('starter-template');
			echo $this->fetch('meta');
			echo $this->fetch('css');
    ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <?php echo $this->Html->link('病院情報局', '/', array('class'=>'navbar-brand')); ?>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
          	<?php
          		$items = array(
          			//'ホーム'=>'/',
          			'病院検索'=>'/hoslist',
          			'DPC全国統計'=>'/dpc',
          			'患者数ランキング'=>'/toplst',
          			'病院ニュース'=>'/wp/archives/category/news/',
          			'情報活用の視点'=>'/wp/archives/category/topics/',
          			'特集'=>'/wp/archives/category/month/',
          			'お知らせ'=>'/wp/archives/category/info/'
							);
							foreach($items as $key => $value):
          	?>
          		<li class="<?php if(FALSE) echo 'active'; ?>"><?php echo $this->Html->link($key, $value); ?></li>
          	<?php endforeach; ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    
    
    <div id="wrap">
    	<div id="body" class="container">
  			<?php if(!empty($is_top)): ?>
    			<?php
    				echo $this->element('ad_top');
    				echo $this->fetch('content');
    				echo $this->element('ad_bottom');
    			?>
    			
  			<?php else: ?>
    			<div class="row">
	    			<div class="col-sm-8">
		    			<?php
		    				echo $this->element('ad_top');
		    				echo $this->fetch('content');
		    				echo $this->element('ad_bottom');
		    			?>
	    			</div>
	    			<div class="col-sm-4">
	    				<?php
	    					echo $this->element('sidebar');
								echo $this->element('ad_sidebar');
	    				?>
	    			</div>
    			</div>
    		<?php endif; ?>
    	</div>
    </div><!-- /.container -->
    
    
    <div id="footer">
      <div class="container">
      	
	<a href="http://hospia.jp/">ＴＯＰ</a>　|　
<a href="http://hospia.jp/wp/gu/">ご利用ガイド</a>　|　
<a href="http://hospia.jp/wp/policy/">サイトポリシー</a>　|　
<a href="http://hospia.jp/wp/company/">運営会社</a>　|　
<a href="http://hospia.jp/wp/sitemap/">サイトマップ</a>　|　
<a href="http://hospia.jp/malady/aboutwidget.php">ウィジェット</a>　|　
<a href="http://hospia.jp/wp/ad/">広告掲載</a>　|　
<a href="http://hospia.jp/wp/inquiry/">お問い合わせ </a> 
<p class="text-muted credit">Copyright(C) Care Review, Inc., All rights reserved. </p>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php
    	echo $this->Html->script('jquery-1.10.2.min');
    	echo $this->Html->script('bootstrap.min.js');
			echo $this->Html->script('knockout-3.0.0');
			echo $this->Html->script('utility');
			echo $this->fetch('script');
    ?>
  </body>
</html>