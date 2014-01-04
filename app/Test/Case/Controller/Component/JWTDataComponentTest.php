<?php
App::import('Vendor', 'JWT');

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('JWTDataComponent', 'Controller/Component');

class TestJWTDataController extends Controller{
	
}

class JWTDataComponentTest extends CakeTestCase{
  public $JWTDataComponent = null;
  public $Controller = null;
	
	public function setUp(){
		parent::setUp();
		
		// Sets up a controller
		$Collection = new ComponentCollection();
    $this->JWTData = new JWTDataComponent($Collection);
    $CakeRequest = new CakeRequest();
    $CakeResponse = new CakeResponse();
    $this->Controller = new TestJWTDataController($CakeRequest, $CakeResponse);
    $this->JWTData->startup($this->Controller);
		
		// Sets up data
		$this->sellerIdentifier = '04806629248295947480';
		$this->sellerSecret = 'xzFzun3WgEG6nAc1x0rtOQ';
		$this->testData = array(
			// google/payments/inapp/item/v1/postback/buy
			'eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJHb29nbGUiLCJyZXF1ZXN0Ijp7Im5hbWUiOiJQaWVjZSBvZiBDYWtlIiwiZGVzY3JpcHRpb24iOiJWaXJ0dWFsIGNob2NvbGF0ZSBjYWtlIHRvIGZpbGwgeW91ciB2aXJ0dWFsIHR1bW15IiwicHJpY2UiOiIxMC41MCIsImN1cnJlbmN5Q29kZSI6IlVTRCIsInNlbGxlckRhdGEiOiJ1c2VyX2lkOjEyMjQyNDUsb2ZmZXJfY29kZTozMDk4NTc2OTg3LGFmZmlsaWF0ZTpha3NkZmJvdnU5aiJ9LCJyZXNwb25zZSI6eyJvcmRlcklkIjoiR1dER19TLjE0NDJkODhmLWJiZTktNGI1OS05MGQzLTdlOGIzMWRlYzEzYSJ9LCJ0eXAiOiJnb29nbGUvcGF5bWVudHMvaW5hcHAvaXRlbS92MS9wb3N0YmFjay9idXkiLCJhdWQiOiIwNDgwNjYyOTI0ODI5NTk0NzQ4MCIsImlhdCI6MTM4ODYzNjk3NCwiZXhwIjoxMzg4NjM2OTk0fQ.M8wMLXSISRQyWC9fVSqSr70_1MOFyOXMLG53TuS9BUY',
			
			// google/payments/inapp/subscription/v1/postback/buy
			'eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJHb29nbGUiLCJyZXF1ZXN0Ijp7Im5hbWUiOiLjg5fjg6zjg5_jgqLjg6DmqZ_og70iLCJkZXNjcmlwdGlvbiI6IueXhemZouaDheWgseWxgOODl-ODrOODn-OCouODoOapn-iDvSIsInNlbGxlckRhdGEiOiJ1c2VyX2lkOjEyMjQyNDUsb2ZmZXJfY29kZTozMDk4NTc2OTg3LGFmZmlsaWF0ZTpha3NkZmJvdnU5aiIsImluaXRpYWxQYXltZW50Ijp7InByaWNlIjoiMjAwIiwiY3VycmVuY3lDb2RlIjoiSlBZIiwicGF5bWVudFR5cGUiOiJwcm9yYXRlZCJ9LCJyZWN1cnJlbmNlIjp7InByaWNlIjoiMTAwIiwiY3VycmVuY3lDb2RlIjoiSlBZIiwic3RhcnRUaW1lIjoxMzg4NjQ4OTI4LCJmcmVxdWVuY3kiOiJtb250aGx5IiwibnVtUmVjdXJyZW5jZXMiOiIyIn19LCJyZXNwb25zZSI6eyJvcmRlcklkIjoiR1dER19TLjk4ZjJhYjgyLTU5MDMtNGE1Mi1iNmVkLTI1ZjU4M2ViMGYyMiJ9LCJ0eXAiOiJnb29nbGUvcGF5bWVudHMvaW5hcHAvc3Vic2NyaXB0aW9uL3YxL3Bvc3RiYWNrL2J1eSIsImF1ZCI6IjA0ODA2NjI5MjQ4Mjk1OTQ3NDgwIiwiaWF0IjoxMzg4NjQ1Mzc2LCJleHAiOjEzODg2NDUzOTZ9.adcppZ_q35U-TaNjZsuMqbFIwX8fpbUPB7RcVRxQFwE',
			
			// google/payments/inapp/subscription/v1/canceled
			'eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJHb29nbGUiLCJyZXNwb25zZSI6eyJvcmRlcklkIjoiR1dER19TLjI2ZDU5YTA4LTgzMTctNDhlMy1iM2VjLTA2NDRkZmY4OWNhMCIsInN0YXR1c0NvZGUiOiJTVUJTQ1JJUFRJT05fQ0FOQ0VMRUQifSwidHlwIjoiZ29vZ2xlL3BheW1lbnRzL2luYXBwL3N1YnNjcmlwdGlvbi92MS9jYW5jZWxlZCIsImF1ZCI6IjA0ODA2NjI5MjQ4Mjk1OTQ3NDgwIiwiaWF0IjoxMzg4NjQzNDgwLCJleHAiOjEzODg2NDM1MDB9.8IGBj7cGQ4e6CJiXSoZMUe8PDbQkG-NgNBnEP8zn4Hk',
		);
	}
	
	/**
	 * Tests to decode sandbox JWT.
	 */
	public function testDecodingUsingJWTPHP(){
		foreach($this->testData as $jwt){
			// Decodes a JWT.
			$payload = JWT::decode($jwt, $this->sellerSecret);
			
			// Outputs decoded payload
			debug($payload);
		}
	}
	
	/**
	 * Tests to encode payload and decode.
	 */
	public function testEncodingUsingJWTPHP(){
		// Encodes a payload
		$payload = array(
		  "iss" => $this->sellerIdentifier,
		  "aud" => "Google",
		  "typ" => "google/payments/inapp/subscription/v1",
		  "exp" => time() + 3600,
		  "iat" => time(),
		  "request" => array (
		    "name" => "プレミアム機能",
		    "description" => "病院情報局プレミアム機能",
		    "sellerData" => "user_id:1224245,offer_code:3098576987,affiliate:aksdfbovu9j",
		    'initialPayment' => array(
		    	'price' => '200',
		    	'currencyCode' => 'JPY',
		    	'paymentType' => 'prorated'
				),
		    "recurrence" => array(
		    	"price" => '100',
		    	'currencyCode' => 'JPY',
		    	'startTime' => time() + 3600,
		    	'frequency' => 'monthly',
		    	'numRecurrences' => '2'
				)
		  )
		);
		$jwt = JWT::encode($payload, $this->sellerSecret);
		
		// Outputs encoded JWT
		debug($jwt);
		
		// Decodes a JWT
		$payloadDecoded = JWT::decode($jwt, $this->sellerSecret);
		
		// Outputs decoded payload
		debug($payloadDecoded);
	}
	
	/**
	 * Tests to generate a sandbox JWT which represents a product.
	 */
	public function testGenerationgJWT(){
		$jwt = $this->JWTData->GeneratePremiumSubscriptionJWT(111, 'User0', 'The User 0', 'm-naito@amlitek.com');
		debug($jwt);
		$payload = JWT::decode($jwt, $this->sellerSecret);
		debug($payload);
		debug(json_decode($payload->request->sellerData));
	}
}
