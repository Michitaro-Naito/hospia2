<?php $this->start('script'); ?>
<script src="<?php echo Configure::read('GoogleWallet_ScriptUrl'); ?>"></script>
<script>
//Success handler
var successHandler = function(purchaseAction){
	console.info('Success!');
	location.reload();
}

//Failure handler
var failureHandler = function(purchaseActionError){
	console.info('Failed!');
}

function purchase(){
	var generatedJwt = '<?php echo $dat['jwt']; ?>';
  google.payments.inapp.buy({
    'jwt'     : generatedJwt,
    'success' : successHandler,
    'failure' : failureHandler
  });
}
</script>
<?php $this->end(); ?>



<?php echo $this->Session->flash(); ?>

<div class="box">
	<h2>会員情報管理</h2>
	<div class="content">
		<?php echo $this->Html->link('会員情報修正', array('controller'=>'Users', 'action'=>'EditMe')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo $this->Html->link('メールアドレス変更', array('controller'=>'Users', 'action'=>'EditEmail')); ?>
	</div>
</div>

<div class="box">
	<h2>プレミアム会員権管理</h2>
	<div class="content">
		<?php if(empty($dat['user']['Subscription']) && empty($dat['user']['SubscriptionCloudPayment']) && empty($dat['user']['User']['special'])): ?>
			<div class="alert">現在は通常会員です。</div>
			<!--
			<p>
				毎月の会費をお支払いいただくと、プレミアム機能をご利用いただけます。<br/>
				以下のボタンからGoogleウォレットへアクセスし、購入手続きが完了すると、直ちにプレミアム機能をご利用いただけます。<br/>
				会費のお支払いはいつでも停止できますので、是非お気軽にご利用ください。
			</p>
			<ul class="basic">
			<li class="li2"><?php echo $this->Html->link('プレミアム機能のご案内', '/wp/archives/225'); ?></li>
			<li class="li2"><?php echo $this->Html->link('プレミアム会員利用規約', '/wp/archives/233'); ?></li>
			<li class="li2"><?php echo $this->Html->link('病院情報局利用規約', '/wp/archives/217'); ?></li>
			</ul>
			<button class="buy-button btn btn-default"
			  id="buybutton1" name="buy" type="button"
			  onClick="purchase()">
			  利用規約に同意してプレミアム会員になる
			</button>
			-->
			
			<p>
				毎月1,000円（税別）の会費をお支払いいただくと、プレミアム機能をご利用いただけます。<br/>
				<a href="http://hospia.jp/wp/archives/225" target="_blank">・プレミアム機能のご案内</a><br/>
				<a href="http://hospia.jp/wp/archives/233" target="_blank">・プレミアム会員利用規約</a><br/>
				以下のボタンから購入画面へアクセスし、購入手続きが完了すると、直ちにプレミアム機能をご利用いただけます。<br/>
				プレミアム機能はいつでも解約できますので、是非お気軽にご利用ください。
			</p>
			<FORM ACTION="https://credit.j-payment.co.jp/gateway/payform.aspx"METHOD="POST">
				<INPUT TYPE="HIDDEN" NAME="aid" VALUE="108313"/>
				<INPUT TYPE="HIDDEN" NAME="pt" VALUE="1"/>
				<INPUT TYPE="HIDDEN" NAME="iid" VALUE="jp_hospia_premium_subscription"/>
				<input type="hidden" name="cod" value="<?php echo h($dat['user']['User']['id']); ?>"/>
				<input type="hidden" name="product_id" value="jp.hospia.premium_subscription"/>
				<INPUT TYPE="submit" NAME="submit" VALUE="利用規約に同意してプレミアム会員になる" class="btn btn-default"/>
			</FORM>
			
		<?php else: ?>
			<?php if(!empty($dat['user']['Subscription']) || !empty($dat['user']['SubscriptionCloudPayment'])): ?>
				<div class="alert alert-success">現在はプレミアム会員です。</div>
			<?php else: ?>
				<div class="alert alert-success">現在は特別会員です。</div>
			<?php endif; ?>
			<!--
			<p>
				プレミアム機能をご利用いただき誠にありがとうございます。<br/>
				会費の支払いを停止される場合は、以下のリンクからGoogleウォレットへアクセスし、「もっと見る」→「定期購入」から、「病院情報局 - プレミアム会費」をご解約ください。<br/>
				いつでも解約手続きができますが、解約すると直ちにプレミアム機能をご利用いただけなくなりますので、あらかじめご了承ください。
			</p>
			<ul class="basic">
			<li class="li2"><?php echo $this->Html->link('プレミアム機能のご案内', '/wp/archives/225'); ?></li>
			<li class="li2"><?php echo $this->Html->link('プレミアム会員利用規約', '/wp/archives/233'); ?></li>
			<li class="li2"><?php echo $this->Html->link('病院情報局利用規約', '/wp/archives/217'); ?></li>
			<li class="li2"><?php echo $this->Html->link('プレミアム会費の支払いを停止する', 'https://wallet.google.com', array('target'=>'_blank')); ?></li></ul>
			
			<?php if(count($dat['user']['Subscription']) > 1): ?>
				<div class="alert alert-danger">
					複数のプレミアム会員契約があります。誤って何度もお支払いになってしまいましたか？
					プレミアム会員権はひとつあれば全ての機能をご利用いただけますので、
					<?php echo $this->Html->link('こちらのページから不要なものをご解約なさってください。', 'https://wallet.google.com', array('target'=>'_blank', 'class'=>'alert-link')); ?>
				</div>
			<?php endif; ?>
			-->
			
			<!-- Unsubscribe (Cloud Payment) -->
			<?php if(!empty($dat['user']['SubscriptionCloudPayment'])): ?>
				<p>
					プレミアム機能をご利用いただき誠にありがとうございます。<br/>
					プレミアム会費は毎月ご登録いただいたクレジットカードに課金させていただきます。<br/>
					会費の支払いを停止される場合は、以下のボタンから退会フォームへアクセスし、表示されている自動課金番号を入力してご解約ください。<br/>
					いつでも退会手続きができますが、退会処理が完了するとすぐにプレミアム機能をご利用いただけなくなりますので、あらかじめご了承ください。
				</p>
				<?php foreach($dat['user']['SubscriptionCloudPayment'] as &$s): ?>
					<div>
						自動課金番号：<?php echo h($s['subscription_id']); ?>
					</div>
				<?php endforeach; ?>
				<div class="row">
					<div class="col-md-6">
						<form action="https://credit.j-payment.co.jp/gateway/cardinfo.aspx" method="POST">
							<input type="hidden" name="aid" value="108313"/>
							<input type="hidden" name="tid" value="<?php echo $s['order_id'] ?>"/>
							<input type="submit" value="カード情報を変更する" class="btn btn-default btn-block"/>
						</form>
					</div>
					<div class="col-md-6">
						<form action="https://credit.j-payment.co.jp/gateway/acstop.aspx" method="POST">
							<input type="hidden" name="aid" value="108313"/>
							<input type="submit" value="プレミアム会員を退会する" class="btn btn-default btn-block"/>
						</form>
					</div>
				</div>
			<?php endif; ?>
			
		<?php endif; ?>
	</div>
</div>

<div class="box">
	<h2>プレミアム機能一覧</h2>
	<div class="content">
		<table class="table">
			<thead>
				<tr>
					<th>区分</th>
					<th>機能名</th>
					<th>詳細</th>
					<th>お試しページ</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td rowspan="2">病院内比較</td>
					<td>時系列分析</td>
					<td>ひとつの病院について、過去からの実績推移を折れ線グラフで比較できます。</td>
					<td><?php echo $this->Html->link('東大病院', '/Home/CompareMdcsByYear/1138814790', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td>ポジション分析</td>
					<td>ひとつの病院について、診療実績の特徴をバブルチャートで比較できます。</td>
					<td><?php echo $this->Html->link('東大病院', '/Position/1138814790', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td rowspan="2">グループ内比較</td>
					<td>時系列分析</td>
					<td>お気に入りグループに登録された最大15病院について、過去からの実績推移を折れ線グラフで比較できます。</td>
					<td><?php echo $this->Html->link('東京都中央区周辺', '/LineChart/1', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td>ポジション分析</td>
					<td>お気に入りグループに登録された最大15病院について、診療実績の特徴をバブルチャートで比較できます。</td>
					<td><?php echo $this->Html->link('東京都中央区周辺', '/BubbleChart/1', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td colspan="2">過去年度データ表示</td>
					<td colspan="2">以下の各ページの過去年度データを表示することができます。病院別診療実績ページ、MDC別患者数ランキング、DPC全国統計・手術情報別病院ランキング、主な疾患別患者数ランキング</td>
				</tr>
				<tr>
					<td colspan="2">広告非表示</td>
					<td colspan="2">サイトの閲覧時に広告が表示されなくなります。</td>
				</tr>
			</tbody>
		</table>
		<p class="muted">Androidをお使いの場合はバージョン4以上をご利用ください。</p>
	</div>
</div>

<?php echo $this->element('favorite'); ?>
