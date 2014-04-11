<?php
// app/Controller/UsersController.php
class UsersController extends AppController {
	public $components = array('RequestHandler', 'Auth');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add','activate');
    }
		
		/**
		 * List of Users. Admin only.
		 */
    public function Index() {
		 	if(!$this->IsAdmin())
				return $this->redirect('/');
			
				$this->request->data['VM'] = $this->request->query;
				$cond = array();
				if(!empty($this->request->data['VM']['username']))
					$cond['User.username like'] = "%{$this->request->data['VM']['username']}%";
        $this->paginate = array(
        	'User'=>array(
        		'paramType'=>'querystring',
        		'order'=>array('User.id'=>'desc'),
        		'limit'=>50,
					)
				);
				$users = $this->paginate('User', $cond);
				
				// User hasMany Subscriptions (Joins here because of performace reasons. I miss LINQ to SQL...)
				$ids = array();
				foreach($users as &$u){
					array_push($ids, $u['User']['id']);
					$u['Subscription'] = array();
				}
				$this->loadModel('Subscription');
				$subscriptions = $this->Subscription->find('all', array(
					'conditions'=>array(
						'Subscription.user_id'=>$ids
					)
				));
				foreach($subscriptions as $s){
					foreach($users as &$u){
						if($u['User']['id'] === $s['Subscription']['user_id']){
							array_push($u['Subscription'], $s['Subscription']);
						}
					}
				}
				
				$this->set('users', $users);
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
			$this->set('noAds', true);
        if ($this->request->is('post')) {
            $this->User->create();
            $this->request->data['User']['role'] = 'basic'; //Users role will always be basic for the moment.
            $this->request->data['User']['active'] = 0; //User will be inactive untill they confirm email.
            if ($this->User->save($this->request->data)) {
            	//**Ticket Logic**
            	$user_email = $this->request->data['User']['email'];
            	//$ticket = $this->Ticket->setTicket($this->params['controller'], $theUser['User']['email']); 
            	$this->loadModel('Ticket');
            	$ticket = $this->Ticket->setTicket($this->params['controller'], $user_email); 
            	$link = Router::url('/users/activate/'.$ticket, true); 
            	
            	//**Email Logic (This can be templated, just using simple solution for now)
            	$email = new CakeEmail('smtp');
            	$email->to($user_email);
							$email->subject('［病院情報局］ユーザー登録の確認');
							$body = 
"病院情報局へのユーザー登録を受け付けました。

以下のリンクをクリックして登録を完了して下さい。
{$link}

このメッセージに心当たりがない場合は、どなたかが貴方のメールアドレスを誤って入力したと思われますので、このメールを削除していただきますようお願いいたします。

******************************
病院情報局 http://hospia.jp/

※URLをクリックしてもうまくアクセスできない場合は、Webブラウザのアドレス欄に、URLが1行になるように貼り付けてアクセスして下さい。
※このメールは自動で送信されておりますので、このメールには返信しないようお願いいたします。";
							$body = str_replace("\n", "\r\n", $body);
							$email->send($body);
            	//$email->subject('病院情報局登録確認メール');
            	//$email->send("病院情報局へご登録いただきありがとうございます。\r\n以下のリンクをクリックして登録を完了して下さい。\r\n" . $link . "\r\n\r\nこのメールにお心当たりがない場合は、どなたかが誤って貴方のメールアドレスを誤って入力してしまったと思われますので廃棄していただけますようよろしくお願いいたします。また、このメールは自動で送信されておりますので、ご返信いただいてもお返事申し上げる事ができません。何卒ご了承ください。 - 病院情報局 http://hospia.jp/");
            	//**Email Logic** End
            	//**Ticket Logic** End
            	
                $this->Session->setFlash('ユーザー情報を登録しました。メールを確認してからログインして下さい。');
                $this->redirect(array('controller'=>'Users', 'action' => 'Login'));
            } else {
                $this->Session->setFlash('ユーザー情報の登録中にエラーが発生しました。内容を修正してから再度お試しください。');
            }
        }
        
        //All logged in users should not be able to access registration.
        //Later this can be changed to allow admins to add new users including new admins.
        if ($this->Auth->loggedIn()) { $this->redirect(array('action' => 'index')); }
    }
		
		/**
		 * Add or Edit an User.
		 * Admin only.
		 */
		 public function Edit($id = null) {
		 	if(!$this->IsAdmin())
				return $this->redirect('/');
			
			if(empty($this->request->data)){
				$this->request->data = $this->User->findById($id);
			}else{
				if ($this->request->is('post') || $this->request->is('put')){
					// Try to save
					if(!empty($this->request->data['User']['new_password'])){
						$this->request->data['User']['password'] = $this->request->data['User']['new_password'];
					}
					if($this->User->save($this->request->data)){
						$this->Session->setFlash('Saved!');
						return $this->redirect(array('action'=>'Index'));
					}
				}
			}
    }
		
		/**
		 * Disable (SoftDelete) an User.
		 * Admin only.
		 */
    public function Disable($id = null) {
		 	if(!$this->IsAdmin() || !($this->request->is('post') || $this->request->is('put')))
				return $this->redirect('/');
			
			if($this->User->delete($id, false))
      	$this->Session->setFlash(__('User deleted'));
			else
				$this->Session->setFlash(__('User not deleted'));
			
			return $this->redirect(array('action'=>'Index'));
    }
		
		public function Statistics(){
		 	if(!$this->IsAdmin())
				return $this->redirect('/');
		}
		
		public function StatisticsGetUsers(){
		 	if(!$this->IsAdmin())
				return $this->redirect('/');
			$page = intval($_REQUEST['page']) + 1;
			$users = $this->User->find('all', array(
				'conditions'=>array(
				),
				'limit'=>10,
				'page'=>$page
			));
			$ids = array();
			foreach($users as &$u){
				array_push($ids, $u['User']['id']);
				$u['Subscription'] = array();
			}
			$this->loadModel('Subscription');
			$subscriptions = $this->Subscription->find('all', array(
				'conditions'=>array(
					'Subscription.user_id'=>$ids
				)
			));
			foreach($subscriptions as $s){
				foreach($users as &$u){
					if($u['User']['id'] === $s['Subscription']['user_id']){
						array_push($u['Subscription'], $s['Subscription']);
					}
				}
			}
			$this->set('users', $users);
			$this->set('_serialize', array('users'));
		}
		
		public function login() {
			$this->set('noAds', true);
		    if ($this->request->is('post')) {
		        if ($this->Auth->login()) {
		        	if ($this->Auth->user('active')) {
		            	$this->redirect(array('controller'=>'Home', 'action' => 'Index'));
		            }
		            else {
		            	$this->Session->setFlash('ログインの前にメールをご確認ください。');
		            	$this->redirect($this->Auth->logout());
		            }
		        } else {
		            $this->Session->setFlash('ユーザー名かパスワードが間違っています。');
		        }
		    }
		}
		
		public function logout() {
		    $this->Auth->logout();
				$this->redirect('/');
		}
		
		public function EditMe(){
			$id = $this->Auth->user('id');
			if(empty($this->request->data)){
				$this->request->data = $this->User->findById($id);
			}else{
				if ($this->request->is('post') || $this->request->is('put')){
					// Try to save
					$prefix = '';
					if(!empty($this->request->data['User']['new_password'])){
						$this->request->data['User']['password'] = $this->request->data['User']['new_password'];
						$masked = str_pad($this->request->data['User']['new_password'][0], strlen($this->request->data['User']['new_password']), '*');
						$prefix = "(新しいパスワード：{$masked})";
					}
					$this->User->id = $id;
					if($this->User->save($this->request->data, true, array('password', 'new_password', 'sei', 'mei', 'sei_kana', 'mei_kana', 'job'))){
						return $this->flash('会員情報を更新しました。'.$prefix, array('controller'=>'Users', 'action'=>'Subscribe'));
						//return $this->redirect(array('controller'=>'Users', 'action'=>'Subscribe'));
					}
				}
			}
		}
		
		public function EditEmail(){
			$id = $this->Auth->user('id');
			$this->User->id = $id;
			$user = $this->User->read();
			if(!empty($this->request->data)){
				$this->loadModel('EditEmailVM');
				$this->EditEmailVM->create($this->request->data);
				if($this->EditEmailVM->validates()){
					$this->loadModel('EmailChange');
					// Valid email. Goes to Email Authentication.
					$dat = array('EmailChange'=>array(
						'user_id' => $user['User']['id'],
						'new_email' => $this->request->data['EditEmailVM']['new_email'],
						'hash' => Security::hash($user['User']['id'].'-'.$this->request->data['EditEmailVM']['new_email'])
					));
					if($this->EmailChange->save($dat)){
          	$email = new CakeEmail('smtp');
          	$email->to($this->request->data['EditEmailVM']['new_email']);
          	//$email->subject('病院情報局メールアドレス変更確認メール');
						$url = Router::url('/Users/EditEmailConfirm/'.$dat['EmailChange']['hash'], true);
						$email->subject('［病院情報局］メールアドレス変更の確認');
							$body = 
"病院情報局へのメールアドレス変更申請を受け付けました。

メールアドレスを {$user['User']['email']} から {$this->request->data['EditEmailVM']['new_email']} に変更するには、以下のURLをクリックして下さい。
{$url}

このメッセージに心当たりがない場合は、どなたかが貴方のメールアドレスを誤って入力したと思われますので、このメールを削除していただきますようお願いいたします。

******************************
病院情報局 http://hospia.jp/

※URLをクリックしてもうまくアクセスできない場合は、Webブラウザのアドレス欄に、URLが1行になるように貼り付けてアクセスして下さい。
※このメールは自動で送信されておりますので、このメールには返信しないようお願いいたします。";
							$body = str_replace("\n", "\r\n", $body);
							$email->send($body);
          	//$email->send("病院情報局でお使いのメールアドレスを {$user['User']['email']} から {$this->request->data['EditEmailVM']['new_email']} に変更するには以下のURLをクリックして下さい。\r\n{$url}");
						$this->flash($this->request->data['EditEmailVM']['new_email'].'に確認メールを送信しました。', '/Users/Subscribe');
					}else{
						$this->flash('エラーによりメール変更手続きを開始できませんでした。', '/Users/Subscribe');
					}
				}
			}
			$this->set('user', $user);
		}

		public function EditEmailConfirm($hash = null){
			// Is hash valid?
			$this->loadModel('EmailChange');
			$row = $this->EmailChange->find('first', array(
				'conditions'=>array(
					'EmailChange.hash' => $hash
				)
			));
			if(empty($row))
				// Invalid
				throw new Exception("無効な確認URLです。誤ったURLにアクセスしたか、確認済みである可能性があります。");
			
			// Valid. Change email and delete row.
			$this->User->id = $row['EmailChange']['user_id'];
			$this->User->read();
			$this->User->data['User']['email'] = $row['EmailChange']['new_email'];
			if(!$this->User->save())
				throw new Exception("不明なエラーにより会員情報の更新に失敗しました。");
			$this->EmailChange->delete($row['EmailChange']['id']);
			
			$this->flash('お使いのメールアドレスを変更しました。', '/Users/Subscribe');
		}
		
		/**
		 * パスワード再設定
		 */
		public function ResetPassword(){
			if(!empty($this->request->data)){
				if(
					empty($this->request->data['ResetPasswordVM']['email'])
					|| empty($this->request->data['ResetPasswordVM']['sei'])
					|| empty($this->request->data['ResetPasswordVM']['mei'])
				){
					$this->Session->setFlash('空欄にはできません。');
					return;
				}
				
				$user = $this->User->find('first', array(
					'conditions'=>array(
						'User.email' => $this->request->data['ResetPasswordVM']['email'],
						'User.sei' => $this->request->data['ResetPasswordVM']['sei'],
						'User.mei' => $this->request->data['ResetPasswordVM']['mei']
					)
				));
				if(empty($user)){
					$this->Session->setFlash('該当する会員情報がありません。');
					return;
				}
				
				// Valid user. Goes to Email Authentication...
				$this->loadModel('PasswordReset');
				$dat = array('PasswordReset'=>array(
					'user_id'=>$user['User']['id'],
					'hash'=>Security::hash($user['User']['id'] . '-' . time())
				));
				if($this->PasswordReset->save($dat)){
        	$email = new CakeEmail('smtp');
        	$email->to($user['User']['email']);
        	//$email->subject('病院情報局パスワード再設定確認メール');
					$url = Router::url('/Users/ResetPasswordConfirm/'.$dat['PasswordReset']['hash'], true);
        	//$email->send("病院情報局でお使いのパスワードを再設定するには以下のURLをクリックして下さい。\r\n{$url}");
        	
					$email->subject('［病院情報局］パスワード再設定の確認');
					$body = 
"病院情報局でお使いのパスワード再設定の申請を受け付けました。

パスワード再設定するには、以下のURLをクリックして下さい。
{$url}

このメッセージに心当たりがない場合は、どなたかが貴方のメールアドレスを誤って入力したと思われますので、このメールを削除していただきますようお願いいたします。

******************************
病院情報局 http://hospia.jp/

※URLをクリックしてもうまくアクセスできない場合は、Webブラウザのアドレス欄に、URLが1行になるように貼り付けてアクセスして下さい。
※このメールは自動で送信されておりますので、このメールには返信しないようお願いいたします。";
					$body = str_replace("\n", "\r\n", $body);
					$email->send($body);
					$this->flash('パスワード再設定用のURLを送信しました。メールボックスをご確認ください。', '/');
				}else{
					$this->flash('エラーによりパスワード変更手続きを開始できませんでした。', '/');
				}
			}
		}
		public function ResetPasswordConfirm($hash = null){
			// Is hash valid?
			$this->loadModel('PasswordReset');
			$row = $this->PasswordReset->find('first', array(
				'conditions'=>array(
					'PasswordReset.hash' => $hash
				)
			));
			if(empty($row))
				// Invalid
				throw new Exception("無効な確認URLです。誤ったURLにアクセスしたか、確認済みである可能性があります。");
			
			// Valid. Change password and delete row.
			$password = $this->randomPassword();
			$this->User->id = $row['PasswordReset']['user_id'];
			$user = $this->User->read();
			$this->User->data['User']['password'] = $password;
			if(!$this->User->save())
				throw new Exception("不明なエラーにより会員情報の更新に失敗しました。");
			
			// Notify by email
    	$email = new CakeEmail('smtp');
    	$email->to($user['User']['email']);
    	//$email->subject('病院情報局パスワード再設定のお知らせ');
    	//$email->send("病院情報局をご利用いただきありがとうございます。\r\n再設定されたアカウント情報を以下の通りお知らせいたします。\r\nID: {$user['User']['username']}\r\n再設定されたパスワード: {$password}");
    	$email->subject('［病院情報局］パスワードを変更しました');
			$body = 
"病院情報局のパスワードが変更されました。

再設定されたアカウント情報を以下の通りお知らせいたします。
ID: {$user['User']['username']}
再設定されたパスワード: {$password}

今後ともよろしくお願い申し上げます。

******************************
病院情報局 http://hospia.jp/

※このメールは自動で送信されておりますので、このメールには返信しないようお願いいたします。";
			$body = str_replace("\n", "\r\n", $body);
			$email->send($body);
			$this->PasswordReset->delete($row['PasswordReset']['id']);
			
			$this->flash('再設定されたパスワードをメールで送信しました。', '/');
		}
		
		/**
		 * User can begin to subscribe (pay monthly) to access advanced features.
		 * User also can see active subscriptions here.
		 */
		public function Subscribe(){
			$this->set('noAds', true);
			$this->JWTData = $this->Components->load('JWTData');
			
			// Makes sure that Cache is cleared to view this page.
			$this->IsPremiumUser(true);
			
			// Get active subscriptions
			$this->User->bindModel(array(
				'hasMany'=>array(
					'Subscription'=>array()
				)
			));
			$this->User->id = $this->Auth->user('id');
			$user = $this->User->read();
			
			// Get JWT (User uses it to begin to subscribe)
			$jwt = $this->JWTData->GeneratePremiumSubscriptionJWT(intval($user['User']['id']), $user['User']['username'], $this->User->getVirtualField('displayname'), $user['User']['email']);
			
			// Pass data to View
			$this->set('dat', array(
				'user' => $user,
				'jwt' => $jwt
			));
		}
		
		//Activation for email confirmation.
		function activate($hash = null) { 
			$this->Ticket = ClassRegistry::init('Ticket'); //Issue with $this->loadModel('Ticket');
    		$email = $this->Ticket->getTicket($this->params['controller'], $hash);
    		$authUser = $this->User->findByEmail($email); 
    		if (is_array($authUser)) { 
                $this->User->id = $authUser['User']['id'];
                $this->User->saveField('active', true);
                $this->Ticket->del($hash);
                $this->redirect(array('controller'=>'Users', 'action'=>'Login')); 
    		}
    		$this->Ticket->del($hash);	
		} 
		
		function randomPassword() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 12; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
}