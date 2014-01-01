<?php
// app/Controller/UsersController.php
class UsersController extends AppController {
	public $components = array('Auth');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
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
            
            $this->request['role'] = 'basic'; //Users role will always be basic for the moment.
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Registration Successful'));
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
		            $this->redirect($this->Auth->redirect());
		        } else {
		            $this->Session->setFlash(__('Invalid username or password, try again'));
		        }
		    }
		}
		
		public function logout() {
		    $this->redirect($this->Auth->logout());
		}
}