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
	 * Returns true if current User is Premium.
	 * Otherwise, returns false.
	 * TODO: Cache
	 */
	private function _IsPremiumUser(){
		$this->Auth = $this->Components->load('Auth');
		if(!$this->Auth->loggedIn())
			return false;
		
		$this->User = ClassRegistry::init('User');
		$this->User->bindModel(array(
			'hasMany'=>array(
				'Subscription'=>array()
			)
		));
		$this->User->id = $this->Auth->user('id');
		$user = $this->User->read();
		$result = false;
		foreach($user['Subscription'] as $s){
			if($s['product_id'] === Configure::read('ProductId_PremiumSubscription')){
				$result = true;
				break;
			}
		}
		return $result;
	}
	
	public function beforeFilter(){
		// ブラウザキャッシュを無効にする
		$this->disableCache();
		$this->isPremiumUser = $this->_IsPremiumUser();
		$this->set('isPremiumUser', $this->isPremiumUser);
	}
}
