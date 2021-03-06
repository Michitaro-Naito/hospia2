<div class="pagebox">
	<h2 class="posttitle">サイトマップ</h2>
	
	<!-- TOP?お問い合わせ -->
	<?php
		$links = array(
			'TOP' => '/',
			'病院検索' => '/hoslist',
			'DPC全国統計' => '/dpc',
			'患者数ランキング' => '/toplst',
			'病院情報局ナビ' => '/wp/archives/216',
			'ご利用ガイド' => '/wp/gu',
			'サイトポリシー' => '/wp/policy',
			'利用規約' => '/wp/archives/217',
			'運営会社' => '/wp/company',
			'お問い合わせ' => '/wp/inquiry',
			'サイトマップ' => '/Sitemap',
			//'投稿募集' => '/wp/post',
			//'広告掲載' => '/wp/ad'
		);
	?>
	<ul class="basic">
		<!-- 固定リンク -->
		<?php foreach($links as $key => $value): ?>
			<li><?php echo $this->Html->link($key, $value); ?></li>
		<?php endforeach; ?>
		
		<!-- 疾患別ランキング -->
		<?php foreach($maladyCategories as $key => $c): ?>
			<li class="<?php if($c['id']>='m101'&&$c['id']<='m120') echo 'li2'; ?>"><?php echo $this->Html->link($c['name'], array('action'=>'Maladylist', 'controller'=>'Home', '?'=>array('mdata'=>$c['id']))); ?></li>
		<?php endforeach; ?>
		
		<!-- 記事 -->
		<?php
			$categories = array(
				'info' => 'お知らせ',
				'list' => '病院リスト',
				'news' => '病院ニュース',
				'month' => '特集',
				//'poll' => 'クイックアンケート',
				'ranking' => '各種ランキング',
				'topics' => '情報活用の視点'
			);
			foreach($groups as $gkey => $g):
		?>
			<?php if(!empty($g)): ?>
				<li><?php echo $this->Html->link($categories[$gkey], '/wp/category/'.$gkey); ?></li>
				<?php foreach($g as $p): ?>
					<li class="li2"><?php echo $this->Html->link($p['Post']['title'], '/wp/archives/'.$p['Post']['post_id']); ?></li>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
	<?php
    echo $this->Paginator->prev('≪ Previous ', null, null, array('class' => 'disabled'));
    echo $this->Paginator->next(' Next ≫', null, null, array('class' => 'disabled'));
	?>
</div>
