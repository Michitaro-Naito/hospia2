<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

App::uses('CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	// Define Sitewide Components all controllers... 
	public $components = array( 'Session' );
	public $isPremiumUser = false;
	
	//Function to check if current user is Admin, useful later.
	public function isAuthorized($user) {
    	// Admin can access every action
    	if (isset($user['role']) && $user['role'] === 'admin') {
    	    return true;
    	}
    	// Default deny
    	return false;
	}
	
	/**
	 * Returns true if current User is Admin.
	 * Otherwise, returns false.
	 */
	public function IsAdmin(){
		if(!$this->Auth->loggedIn())
			return false;
		return $this->Auth->user('role') === 'admin';
	}
	
	/**
	 * Returns true if current User is Premium.
	 * Otherwise, returns false.
	 * Caches result using Session.
	 * @param bool $clearCache Clears cache if true.
	 */
	public function IsPremiumUser($clearCache = false){
		$result = $this->_IsPremiumUser($clearCache);
		$this->isPremiumUser = $result;
		$this->set('isPremiumUser', $this->isPremiumUser);
	}
	private function _IsPremiumUser($clearCache = false){
		// Logged in?
		$this->Auth = $this->Components->load('Auth');
		$this->Auth->initialize($this);
		if(!$this->Auth->loggedIn())
			return false;
		
		if(!$clearCache){
			// Uses cached value if available
			$sessionValue = $this->Session->read('Auth.User.IsPremium');
			if($sessionValue !== null && $sessionValue['expires'] > time())
				return $sessionValue['value'];
		}
		
		$result = null;
		
		// Premium if Admin
		if($this->IsAdmin())
			$result = true;
		
		if($result == null){
			// Desides from database
			$this->User = ClassRegistry::init('User');
			$this->User->bindModel(array(
				'hasMany'=>array(
					'Subscription'=>array(),
					'SubscriptionCloudPayment'=>array()
				)
			));
			$this->User->id = $this->Auth->user('id');
			$user = $this->User->read();
			$result = false;
			
			// true if payed.
			foreach($user['Subscription'] as $s){
				if($s['product_id'] === Configure::read('ProductId_PremiumSubscription')){
					$result = true;
					break;
				}
			}
			foreach($user['SubscriptionCloudPayment'] as $s){
				if($s['product_id'] === Configure::read('ProductId_PremiumSubscription')){
					$result = true;
					break;
				}
			}
			
			// true if free trial (insentive) or SpecialUser
			$until = new DateTime($user['User']['insentive_until']);
			$now = new DateTime();
			if($until > $now || !empty($user['User']['special']))
				$result = true;
		}
		
		// Caches to Session
		$this->Session->write('Auth.User.IsPremium', array(
			'value' => $result,
			'expires' => time() + 300
		));
		
		return $result;
	}
	
	public function beforeFilter(){
		// ブラウザキャッシュを無効にする
		$this->disableCache();
		$this->IsPremiumUser();
		$this->Auth->allow();
		
		// Detectors
		$this->request->addDetector('mobile', array(
			'env' => 'HTTP_USER_AGENT', 'pattern' => '/iPhone|Android|Windows Phone/i'
		));
		
		$this->set('loggedIn', $this->Auth->loggedIn());
		$this->set('username', $this->Auth->user('username'));
	}
}
