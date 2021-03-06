<?php echo $this->Html->docType(); ?>
<html>
  <head>
  	<?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <meta name="description" content="全国の急性期病院の診療実績（患者数、平均在院平均など）を比較">
    <meta name="author" content="">
    <meta property="og:image" content="<?php echo Router::url('/img/logo_fb.png', true); ?>" />
    <meta property="og:image:type" content="image/png">
		<meta property="og:image:width" content="270" />
		<meta property="og:image:height" content="270" />
		<meta property="og:description" content="全国の急性期病院の診療実績（患者数、平均在院平均など）を比較" />

    <title>
    	<?php
    		$viewTitle = $this->fetch('title');
    		if(!empty($viewTitle))
    			$title = $viewTitle;
    		if(!empty($title))
    			echo h($title . ' - 病院情報局');
				else
					echo h('病院情報局');
    	?>
    </title>

    <?php
			echo $this->Html->meta('icon');
    	echo $this->Html->css('bootstrap.min');			// Bootstrap core CSS
			echo $this->Html->css('site');
			echo $this->Html->css('site2');
			echo $this->fetch('meta');
			echo $this->fetch('css');
    ?>
    
    <!--[if lt IE 8]>
    	<?php echo $this->Html->css('ie7'); ?>
    <![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <?php echo $this->element('googletag'); ?>
  </head>

  <body>
  	
    <div class="navbar navbar-inverse" role="navigation">
      <div class="container">
      	<?php if(!empty($loggedIn)): ?>
      		<div class="welcome">
      			ようこそ、<?php echo h($username); ?>さん！
		      	<?php
		      		$suffix = '（会員情報・お気に入り管理画面へ）';
		      		if($this->request->is('mobile'))
								$suffix = '';
		      		if(!empty($isPremiumUser))
								echo $this->Html->link('プレミアム会員'.$suffix, array('controller'=>'Users', 'action'=>'Subscribe'));
							else
								echo $this->Html->link('通常会員'.$suffix, array('controller'=>'Users', 'action'=>'Subscribe'));
		      	?>
      		</div>
      	<?php endif; ?>
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <?php //echo $this->Html->link('病院情報局', '/', array('class'=>'navbar-brand')); ?>
          <div class="navbar-brand">
          	<?php echo $this->Html->link($this->Html->image('logo_original.png'), '/', array('escape'=>false)); ?>
          </div>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
          	<?php
							$items = array(
								array('label'=>'病院検索', 'small'=>'Hospital Search', 'url'=>'/hoslist'),
								array('label'=>'患者数ランキング', 'small'=>'Top Hospitals', 'url'=>'/toplst'),
								array('label'=>'DPC全国統計', 'small'=>'DPC Statistics', 'url'=>'/dpc'),
								array('label'=>'病院ニュース', 'small'=>'Hospital News', 'url'=>'/wp/archives/category/news/'),
								array('label'=>'情報活用', 'small'=>'Point of View', 'url'=>'/wp/archives/category/topics/'),
								array('label'=>'特集', 'small'=>'Special', 'url'=>'/wp/archives/category/month/'),
								array('label'=>'お知らせ', 'small'=>'Information', 'url'=>'/wp/archives/category/info/'),
							);
							// ログイン
							if($this->Session->read('Auth.User'))
								array_push($items, array('label'=>'ログアウト', 'small'=>'Logout', 'url'=>array('controller'=>'Users', 'action'=>'Logout')));
							else
								array_push($items, array('label'=>'ログイン', 'small'=>'Login', 'url'=>array('controller'=>'Users', 'action'=>'Login')));
							foreach($items as $key => $value):
          	?>
          		<?php if($key != 0): ?>
          			<li class="divider"></li>
          		<?php endif; ?>
          		<li>
          			<?php echo $this->Html->link($value['label'], $value['url']); ?>
          			<small><?php echo h($value['small']); ?></small>
          		</li>
          	<?php endforeach; ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    
    
    <div id="wrap">
    	<div id="body" class="container">
  			<?php if(!empty($is_top) || !empty($bareLayout)): ?>
    			<?php
    				echo $this->element('ad_top');
    				echo $this->fetch('content');
    				//echo $this->element('ad_bottom');
    			?>
    			
  			<?php else: ?>
    			<div class="row">
	    			<div class="col-sm-9">
		    			<?php
		    				echo $this->element('ad_top');
		    				echo $this->fetch('content');
		    				echo $this->element('ad_bottom');
		    			?>
	    			</div>
	    			<div class="col-sm-3">
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
      	<div class="nav">
	      	<?php
		      	echo $this->Html->link('ＴＯＰ', '/'); 
		      	echo $this->Html->link('病院情報局ナビ', '/wp/archives/216');
		      	echo $this->Html->link('ご利用ガイド', '/wp/gu/');
		      	echo $this->Html->link('サイトポリシー', '/wp/policy/'); 
		      	echo $this->Html->link('運営会社', '/wp/company/'); 
		      	echo $this->Html->link('サイトマップ', '/Sitemap'); 
		      	echo $this->Html->link('ウィジェット', '/malady/aboutwidget.php'); 
		      	//echo $this->Html->link('広告掲載', '/wp/ad/'); 
		      	echo $this->Html->link('お問い合わせ', '/wp/inquiry/');
	      	?>
      	</div>
				<p class="credit">Copyright(C) Care Review, Inc., All rights reserved. </p>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php
    	echo $this->Html->script('json2');
    	echo $this->Html->script('jquery-1.10.2.min');
    	echo $this->Html->script('bootstrap.min.js');
			echo $this->Html->script('knockout-3.0.0');
			echo $this->Html->script('jsuri-1.1.1.min');
			echo $this->Html->script('jquery.balloon.min.js');
			echo $this->Html->script('utility');
			echo $this->fetch('script');
    ?>
    <script>
    	try{
    		$.initBalloons('<?php echo h(Router::url('/Tip/View')); ?>');
    	}catch(e){
    		alert(JSON.stringify(e));
    	}
    </script>

<!-- Yahooアクセス解析 -->
<script type="text/javascript">
  (function () {
    var tagjs = document.createElement("script");
    var s = document.getElementsByTagName("script")[0];
    tagjs.async = true;
    tagjs.src = "//s.yjtag.jp/tag.js#site=LZaFZD6";
    s.parentNode.insertBefore(tagjs, s);
  }());
</script>
<noscript>
  <iframe src="//b.yjtag.jp/iframe?c=LZaFZD6" width="1" height="1" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</noscript>
<!-- /Yahooアクセス解析 -->

  </body>
</html>