<?php
class TransactionController extends AppController{
	public $components = array('JWTData', 'Auth');
	
	public function BeforeFilter(){
		$this->Auth->allow();
	}
	
	/**
	 * Handles Transaction of Google Wallet.
	 * Called by Google when User subscribed / canceled.
	 */
	public function GoogleWalletPostback(){
		$jwt = $_REQUEST['jwt'];
		if(empty($jwt))
			throw new Exception('pass $_REQUEST["jwt"]');
		
		// Record JWT anyways (Losing this data must be avoided at any cost)
		$this->Transaction->create(array(
			'Transaction' => array(
				'jwt' => $jwt
			)
		));
		$this->Transaction->save();
		
		// Record Payload
		$payload = $this->JWTData->Decode($jwt);
		$this->Transaction->data['Transaction']['payload'] = print_r($payload, true);
		$typ = $payload->typ;
		$this->Transaction->data['Transaction']['typ'] = $typ;
		$this->Transaction->save();
		
		switch($typ){
			case 'google/payments/inapp/subscription/v1/postback/buy':
				// ----- User began to subscribe -----
				$payload->request->sellerData = json_decode($payload->request->sellerData);
				$orderId = $payload->response->orderId;
				$userId = $payload->request->sellerData->user_id;
				$productId = $payload->request->sellerData->product_id;
				if($productId !== Configure::read('ProductId_PremiumSubscription')){
					// Invalid Product ID
					return;
				}
				$this->Subscription = ClassRegistry::init('Subscription');
				$this->Subscription->create(array(
					'Subscription'=>array(
						'user_id'=>$userId,
						'order_id'=>$orderId,
						'product_id'=>$productId
					)
				));
				$this->Subscription->save();
				// Record detailed Transaction
				$this->Transaction->data['Transaction']['user_id'] = $userId;
				$this->Transaction->data['Transaction']['username'] = $payload->request->sellerData->username;
				$this->Transaction->data['Transaction']['display_name'] = $payload->request->sellerData->display_name;
				$this->Transaction->data['Transaction']['email'] = $payload->request->sellerData->email;
				$this->Transaction->data['Transaction']['order_id'] = $orderId;
				$this->Transaction->data['Transaction']['product_id'] = $productId;
				$this->Transaction->save();
				// Return orderId to tell Google that's OK.
				$this->autoRender = false;
				$this->response->body($orderId);
				return;
				
			case 'google/payments/inapp/subscription/v1/canceled':
				// ----- User canceled subscription -----
				$orderId = $payload->response->orderId;
				// Delete Active Subscription
				$this->Subscription = ClassRegistry::init('Subscription');
				$this->Subscription->data = $this->Subscription->find('first', array(
					'conditions'=>array(
						'Subscription.order_id' => $orderId
					)
				));
				$this->Subscription->id = $this->Subscription->data['Subscription']['id'];
				$this->Subscription->delete();
				// Return orderId to tell Google that's OK.
				$this->autoRender = false;
				$this->response->body($orderId);
				return;
				
			default:
				// Unknown typ?
				break;
		}
		
		// Something went wrong.
		$this->autoRender = false;
		$this->response->body('ERROR');
		return;
	}

	/**
	 * Cloud Payment notifies here when the first payment is done.
	 * Activates premium features.
	 */
	public function CloudPaymentKickback_FirstPayment(){
		
		// Records payload
		$this->TransactionCloudPayment = ClassRegistry::init('TransactionCloudPayment');
		$this->TransactionCloudPayment->create(array(
			'TransactionCloudPayment' => array(
				'query' => print_r($this->request->query, true)
			)
		));
		$this->TransactionCloudPayment->save();
		
		/*
Incoming data ($this->request->query):
Array (
   [gid] => 12027473
   [rst] => 1
   [ap] => TestMod
   [ec] => 
   [god] => 12027126
   [cod] => 
   [am] => 1000
   [tx] => 80
   [sf] => 0
   [ta] => 1080
   [pt] => 1
   [submit] => 購入
   [acid] => 1000179835
)
		 */
		if($this->request->query['rst'] == 1){	// Payed successfully?
			try{
				// Adds Subscription
				$this->SubscriptionCloudPayment = ClassRegistry::init('SubscriptionCloudPayment');
				$this->SubscriptionCloudPayment->create(array('SubscriptionCloudPayment'=>array(
					'user_id' => $this->request->query['cod'],						// UserId of this site
					'order_id' => $this->request->query['gid'],						// Unique order ID of Cloud Payment
					'subscription_id' => $this->request->query['acid'],		// Unique subscription ID of Cloud Payment
					'product_id' => $this->request->query['product_id']		// ProductId, jp.hospia.premium_subscription
				)));
				$this->SubscriptionCloudPayment->save();
				
			}catch (Exception $e){
				// Failed. Notifies Admin.
				$email = new CakeEmail('smtp');
		  	$email->to('m-naito@amlitek.com');
				$email->subject('キックバック通知(first payment)の処理に失敗しました');
				$body = $e;
				$email->send($body);
			}
		}
		
		// Returns OK
		$this->autoRender = false;
		$this->response->body('OK');
		return;
	}

	/**
	 * Cloud Payment notifies subscription status here.
	 * If succeeded, does nothing. (Continues to provide premium features.)
	 * If failed, disables premium features.
	 */
	public function CloudPaymentKickback_SubscriptionStatus(){
		
		// Records payload
		$this->TransactionCloudPayment = ClassRegistry::init('TransactionCloudPayment');
		$this->TransactionCloudPayment->create(array(
			'TransactionCloudPayment' => array(
				'query' => print_r($this->request->query, true)
			)
		));
		$this->TransactionCloudPayment->save();
		
		// Disables premium features if unsubscribed.
		// 2: Failed
		// 3: Retry failed (won't come)
		// 4: Unsubsribed by User
		// 5: Failed after demo (won't come)
		if(in_array($this->request->query['rst'], array(2, 3, 4, 5))){
			try{
				// Deletes active subscription
				$this->SubscriptionCloudPayment = ClassRegistry::init('SubscriptionCloudPayment');
				$this->SubscriptionCloudPayment->data = $this->SubscriptionCloudPayment->find('first', array(
					'conditions'=>array(
						'SubscriptionCloudPayment.subscription_id' => $this->request->query['acid']
					)
				));
				$this->SubscriptionCloudPayment->id = $this->SubscriptionCloudPayment->data['SubscriptionCloudPayment']['id'];
				$this->SubscriptionCloudPayment->delete();
			}catch(Exception $e){
				// Failed. Notifies Admin.
				$email = new CakeEmail('smtp');
		  	$email->to('m-naito@amlitek.com');
				$email->subject('キックバック通知(subscription)の処理に失敗しました');
				$body = $e;
				$email->send($body);
			}
		}
		
		// Returns OK
		$this->autoRender = false;
		$this->response->body('OK');
		return;
	}
}
