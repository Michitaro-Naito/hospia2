<?php $this->start('script'); ?>
<script src="https://sandbox.google.com/checkout/inapp/lib/buy.js"></script>
<script>
//Success handler
var successHandler = function(purchaseAction){
	console.info('Success!');
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



<h2>Active Subscriptions</h2>
<ul>
	<?php foreach($dat['user']['Subscription'] as $s): ?>
		<li>
			<span><?php echo h($s['order_id']); ?></span>
			<span><?php echo h($s['product_id']); ?></span>
		</li>
	<?php endforeach; ?>
</ul>

<h2>Subscribe</h2>
<button class="buy-button"
  id="buybutton1" name="buy" type="button"
  onClick="purchase()">
  Buy
</button>

<?php debug($dat); ?>
