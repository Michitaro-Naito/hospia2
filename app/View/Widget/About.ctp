<?php $this->start('script'); ?>
<script>
$('#GenerateButton').click(function(){
	var title = $('#Title').val();
	if(title.length == 0){
		alert('タイトルを入力して下さい。');
		return;
	}
	var str = 
	'<!-- Begin 病院情報局-->\n'
	+ '<script type="text/javascript"\n'
	+ '"http://hospia.jp/widget/maladywidget.php?scode='+encodeURI(title)+'&ttype=1"\n'
	+ 'charset="UTF-8"></' + 'script>\n'
	+ '<div style="text-align: right; font-size: 13px; margin: 5px;">\n'
	+ 'Powered by <a style="color: #FF6600;" href="http://hospia.jp/">病院情報局</a>\n'
	+ '</div>\n'
	+ '<!-- End 病院情報局-->';
	$('#Code').text(str);
});
</script>
<?php $this->end(); ?>



<div id="content">
<div id="innerbox">
<div class="pagebox">

	<h2 class="posttitle">病院情報局ウィジェット</h2>
<p>あなたのサイトやブログに、病院情報局のランキングを表示するウィジェットを簡単に設置することができます。<br />
サイトやブログの価値を高める情報提供ツールとして、ご活用いただけると幸いです。</p>
<div style="background:#FFE4C4; font-size: 13px; padding:10px 20px 10px 20px;">
<strong>ウィジェットのサンプル</strong><br />
［都道府県］と［診断分類（または疾患名）］を選択して検索ボタンを押すと、ランキングが一発表示されます！
<script type="text/javascript"
src="http://hospia.jp/widget/maladywidget.php?scode=hospia&ttype=1"
charset="UTF-8"></script>
<div style="text-align: right; font-size: 13px; margin: 5px;">
Powered by <a style="color: #FF6600;" href="http://hospia.jp/">病院情報局</a>
</div>
</div>

	<div class="hmargin"></div>

<h4>ウィジェットの設置方法</h4>
<ul>
<li>以下のフォームに貼り付け先のサイトやブログのタイトルを入力して、ボタンを押してください。</li>
<li>出力されたコードをコピーして、サイトやブログに貼り付ければ設置完了です。</li>
</ul>
	<div class="formbox" style="background:#f1f1f1; padding:10px 20px 10px 20px;">
		<div>
			<span class="formboxlabel">貼り付けるサイトやブログのタイトル</span>
			<input type="text" id="Title" maxlength="20">
			（20文字以内）<br>
			<button type="button" id="GenerateButton">貼り付け用のコードを出力する</button>
			<textarea class="" id="Code" style="width: 600px; height: 180px;" onclick="this.focus();this.select()" readonly></textarea>
		</div>
	</div>

	<div class="hmargin"></div>

<h4>設置にあたっての留意事項</h4>
<ul>
<li><font style="color: red; font-weight: bold;">ウィジェットを設置したことに伴うトラブルや損害に対して、当社は一切の責任を負いませんので、あらかじめご了承の上で設置してください。</font></li>
<li>設置にあたって当社への連絡は必要ありませんが、営利目的にはご利用いただけません。</li>
<li>設置する場所の横幅に応じて、ウィジェットの横幅は自動的に調整されます。</li>
<li>設置方法などの個別サポートは行っておりませんので、各サイト管理者やブログサービスにお問い合わせください。（一般的にはHTMLの編集画面に貼り付ければOKです）</li>
<li>Javascriptが設置できないサイトやブログには、設置できません。</li>
<li>サイトに合わせてデザインをカスタマイズしたい場合は、HTMLソースを提供しますので、個別にお問い合わせください。</li>

</ul>

	<div class="hmargin"></div>


	<div>　</div>
	<table width="200">
		<tr>
			<td align="center">
				<a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-lang="ja">ツイート</a>
				<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			</td>
			<td align="center">
				<a name="fb_share" type="button" href="http://www.facebook.com/sharer.php?u=http://hospia.jp/&amp;t=%e7%97%85%e9%99%a2%e6%83%85%e5%a0%b1%e5%b1%80" share_url="">share</a>
				<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
			</td>
			<td align="center">
				<g:plusone size="medium" count="false"></g:plusone>
			</td>
		</tr>
	</table>

</div><!-- END div.pagebox -->
</div><!-- END div#innerbox -->
</div><!-- END div#content -->
