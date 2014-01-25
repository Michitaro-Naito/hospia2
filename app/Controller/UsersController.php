<?php
// app/Controller/UsersController.php
class UsersController extends AppController {
	public $components = array('Auth');
	
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
            	$email->subject('病院情報局登録確認メール');
            	$email->send("病院情報局へご登録いただきありがとうございます。\n以下のリンクをクリックして登録を完了して下さい。\n" . $link . "\n\nこのメールにお心当たりがない場合は、どなたかが誤って貴方のメールアドレスを誤って入力してしまったと思われますので廃棄していただけますようよろしくお願いいたします。また、このメールは自動で送信されておりますので、ご返信いただいてもお返事申し上げる事ができません。何卒ご了承ください。 - 病院情報局 http://hospia.jp/");
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
			$jwt = $this->JWTData->GeneratePremiumSubscriptionJWT(intval($user['User']['id']), $user['User']['username'], $user['User']['displayname'], $user['User']['email']);
			
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
}