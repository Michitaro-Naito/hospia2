<?php
// app/Controller/UsersController.php
class UsersController extends AppController {
	public $components = array('Auth');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add','activate');
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
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
            	$email->subject('Verification');
            	$email->send('Click the link to activate ' . $link);
            	//**Email Logic** End
            	//**Ticket Logic** End
            	
                $this->Session->setFlash(__('<strong>Registration Successful</strong> Please activate your email before you log in.'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Something went wrong with your Registration. Please, try again.'));
            }
        }
        
        //All logged in users should not be able to access registration.
        //Later this can be changed to allow admins to add new users including new admins.
        if ($this->Auth->loggedIn()) { $this->redirect(array('action' => 'index')); }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException(); //Disable this to test delete.
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        $this->redirect(array('action' => 'index'));
    }
		
		public function login() {
		    if ($this->request->is('post')) {
		        if ($this->Auth->login()) {
		        	if ($this->Auth->user('active')) {
		            	$this->redirect(array('action' => 'index'));
		            }
		            else {
		            	$this->Session->setFlash(__('Please confirm your email to log in.'));
		            	$this->redirect($this->Auth->logout());
		            }
		        } else {
		            $this->Session->setFlash(__('Invalid username or password, try again'));
		        }
		    }
		}
		
		public function logout() {
		    $this->redirect($this->Auth->logout());
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
                $this->redirect( '/' ); 
    		}
    		$this->Ticket->del($hash);	
		} 
}