<?php
App::import('Vendor', 'JWT');

App::uses('Component', 'Controller');

/**
 * Handles JWT (JSON Web Token).
 * Calls JWT.php internally.
 * 
 * User receives this JWT from Server.
 * User sends it to Google when he is going to buy it.
 * Google notifies Server if bought.
 */
class JWTDataComponent extends Component{
	public $sellerIdentifier = '';
	public $sellerSecret = '';
	
	public function __construct(){
		$this->sellerIdentifier = Configure::read('GoogleWallet_SellerId');
		$this->sellerSecret = Configure::read('GoogleWallet_SellerSecret');
	}
	
	/**
	 * Generates JWT which represents Premium-Subscription.
	 * @param integer $userId
	 * @param string $username
	 * @param string $displayName
	 * @param string $email
	 * @return string Encoded JWT
	 */
	public function GeneratePremiumSubscriptionJWT($userId, $username, $displayName, $email){
		//
		if(!is_int($userId))
			throw new Exception('$userId must be integer.');
		if(!is_string($username) || empty($username))
			throw new Exception('$username must be string and not empty.');
		if(!is_string($displayName) || empty($displayName))
			throw new Exception('$displayName must be string and not empty.');
		if(!is_string($email) || empty($email))
		  throw new Exception('$email must be string and not empty.');
		
		// Stores data about Buyer and Product. (This helps to identify who bought what later.)
		$sellerData = json_encode(array(
			// Buyer
			'user_id' => $userId,						// Buyer's UserId (User.id)
			'username' => $username,				// Buyer's UserName
			'display_name' => $displayName,	// Buyer's DisplayName
			'email' => $email,							// Buyer's Email Address
			
			// Product
			'product_id' => Configure::read('ProductId_PremiumSubscription'),
			'fetched' => time(),
		));
		
		$payload = array(
		  "iss" => $this->sellerIdentifier,
		  "aud" => "Google",
		  "typ" => "google/payments/inapp/subscription/v1",
		  "exp" => time() + 3600,
		  "iat" => time(),
		  "request" => array (
		    "name" => "病院情報局 - プレミアム会費",
		    "description" => "毎月の会費をお支払いいただくと、プレミアム機能をお使いいただけます。",
		    "sellerData" => $sellerData,
		    'initialPayment' => array(
		    	'price' => '1000',
		    	'currencyCode' => 'JPY',
		    	'paymentType' => 'prorated',
				),
		    "recurrence" => array(
		    	"price" => '1000',
		    	'currencyCode' => 'JPY',
		    	'startTime' => time() + 2600000,
		    	'frequency' => 'monthly',
		    	//'numRecurrences' => '2'	// Inifinite
				)
		  )
		);
		$jwt = JWT::encode($payload, $this->sellerSecret);
		
		return $jwt;
	}
	
	/**
	 * Decodes JWT
	 * @param string $jwt Encoded JWT
	 * @return mixed Payload
	 */
	public function Decode($jwt){
		$payload = JWT::decode($jwt, $this->sellerSecret);
		return $payload;
	}
}
