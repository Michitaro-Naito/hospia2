<?php $this->start('script'); ?>
<script src="https://sandbox.google.com/checkout/inapp/lib/buy.js"></script>
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
		<?php if(empty($dat['user']['Subscription'])): ?>
			<div class="alert">現在は通常会員です。</div>
			<?php echo $this->Html->link('・プレミアム機能のご案内', '/wp/archives/225'); ?><br/>
			<?php echo $this->Html->link('・プレミアム会員利用規約', '/wp/archives/233'); ?><br/>
			<?php echo $this->Html->link('・病院情報局利用規約', '/wp/archives/217'); ?>
			<p>
				毎月の会費をお支払いいただくと、プレミアム機能をご利用いただけます。<br/>
				以下のボタンからGoogleウォレットへアクセスし、購入手続きが完了すると、直ちにプレミアム機能をご利用いただけます。<br/>
				会費のお支払いはいつでも停止できますので、是非お気軽にご利用ください。
			</p>
			<button class="buy-button btn btn-default"
			  id="buybutton1" name="buy" type="button"
			  onClick="purchase()">
			  利用規約に同意してプレミアム会員になる
			</button>
			
		<?php else: ?>
			<div class="alert alert-success">現在はプレミアム会員です。</div>
			<?php echo $this->Html->link('・プレミアム機能のご案内', '/wp/archives/225'); ?><br/>
			<?php echo $this->Html->link('・プレミアム会員利用規約', '/wp/archives/233'); ?><br/>
			<?php echo $this->Html->link('・病院情報局利用規約', '/wp/archives/217'); ?>
			<p>
				プレミアム機能をご利用いただき誠にありがとうございます。<br/>
				会費の支払いを停止される場合は、以下のボタンからGoogleウォレットへアクセスし、「もっと見る」→「定期購入」から、「病院情報局 - プレミアム会費」をご解約ください。<br/>
				いつでも解約手続きができますが、解約すると直ちにプレミアム機能をご利用いただけなくなりますので、あらかじめご了承ください。
			</p>
			<?php echo $this->Html->link('プレミアム会費の支払いを停止する', 'https://wallet.google.com', array('target'=>'_blank')); ?>
			
			<?php if(count($dat['user']['Subscription']) > 1): ?>
				<div class="alert alert-danger">
					複数のプレミアム会員契約があります。誤って何度もお支払いになってしまいましたか？
					プレミアム会員権はひとつあれば全ての機能をご利用いただけますので、
					<?php echo $this->Html->link('こちらのページから不要なものをご解約なさってください。', 'https://wallet.google.com', array('target'=>'_blank', 'class'=>'alert-link')); ?>
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
					<th>機能名</th>
					<th>詳細</th>
					<th>お試しページ</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>過年度比較</td>
					<td>ひとつの医療機関について、その診療実績を7年度分まとめて折れ線グラフで比較できます。</td>
					<td><?php echo $this->Html->link('順天堂大学医学部附属　順天堂医院', '/Home/CompareMdcsByYear/1130514836', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td>お気に入りグループ内比較(折れ線グラフ)</td>
					<td>お気に入りグループに登録された最大15の医療機関を折れ線グラフで比較できます。</td>
					<td><?php echo $this->Html->link('東京都中央区周辺', '/LineChart/1', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td>お気に入りグループ内比較(バブルチャート)</td>
					<td>お気に入りグループに登録された最大15の医療機関をバブルチャートで比較できます。</td>
					<td><?php echo $this->Html->link('東京都中央区周辺', '/BubbleChart/1', array('target'=>'_blank')); ?></td>
				</tr>
				<tr>
					<td>広告非表示</td>
					<td>サイトの閲覧時に広告が表示されなくなります。</td>
				</tr>
			</tbody>
		</table>
		<p class="muted">Androidをお使いの場合はバージョン4以上をご利用ください。</p>
	</div>
</div>

<?php echo $this->element('favorite'); ?>
