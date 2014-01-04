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
				// Find User
				$this->User = ClassRegistry::init('User');
				$this->User->bindModel(array(
					'hasMany'=>array(
						'Subscription'=>array(
						)
					)
				), false);
				$this->User->id = $userId;
				$this->User->read();
				//debug($this->User->data);
				// Change state of Subscription
				array_push(
					$this->User->data['Subscription'],
					array(
						'order_id'=>$orderId,
						'product_id'=>$productId
					)
				);
				$this->User->saveAll();
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
}
