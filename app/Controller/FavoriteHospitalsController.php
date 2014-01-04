<?php
class FavoriteHospitalsController extends AppController {
	public $components = array('Auth');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'edit');
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->FavoriteHospital->create();
            if ($this->Auth->loggedIn()) {
             	$this->request->data['FavoriteHospital']['user_id'] = $this->Auth->user('id');
            	if ($this->FavoriteHospital->save($this->request->data)) {
                	$this->Session->setFlash(__('<strong>Group Added!</strong>'));
                	$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
            	} else {
                	$this->Session->setFlash(__('Something went wrong with adding your group. Please, try again.'));
                	$this->set('errors', $this->FavoriteHospital->validationErrors);
            	}
            } else {
                $this->Session->setFlash(__('You are no longer logged in.'));
            }
        }
    }
    
    public function edit($id = null) { 
    	$favhos = $this->FavoriteHospital->find('first',array('conditions'=>array('FavoriteHospital.id'=> $id)));

        if ($this->request->is('post') || $this->request->is('put'))
        {
            $this->FavoriteHospital->id = $favhos['FavoriteHospital']['id'];
            if ($this->FavoriteHospital->save($this->request->data))
            {
                $this->Session->setFlash('Your group has been updated');
                $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
            }
            else
            {
                $this->Session->setFlash('Server broke!');
            }
        }
        else 
        {
            if($favhos['FavoriteHospital']['user_id'] != $this->Auth->user('id'))
            {
                $this->Session->setFlash('Not yours!');
                $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
            }
            else
            {               
                $this->request->data = $this->FavoriteHospital->read(null, $favhos['FavoriteHospital']['id']);
            }
        }
    }
    
    public function view($id = null) {
        $this->FavoriteHospital->id = $id;
        if (!$this->FavoriteHospital->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('fh', $this->FavoriteHospital->read(null, $id));
    }
    
    public function addHospital($gid = null, $hid = null) {
    	//$hid = 1010111085;
    	//$gid = 3;
    	/*$this->data['FavoriteHospital']['id'] = $gid;
		$this->data['Hospital']['id'] = $hid;

		$this->FavoriteHospital->save($this->data);*/
    	/*$conditions = array("Post.title" => "This is a post", "Post.author_id" => 1);
		// Example usage with a model:
		$this->Post->find('first', array('conditions' => $conditions));*/
    	
    	$favhos = $this->FavoriteHospital->find('first',array('conditions'=>array('FavoriteHospital.id'=> $gid)));
    	if($favhos['FavoriteHospital']['user_id'] != $this->Auth->user('id'))
    	{
            $this->Session->setFlash($favhos['FavoriteHospital']['user_id'].'Not your hospital Group.'.$this->Auth->user('id'));
            $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        }
        else {
        	$this->FavoriteHospital->addHospital($gid, $hid);
        	$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        }
    }
    
    public function deleteHospital($gid = null, $hid = null) {
		$favhos = $this->FavoriteHospital->find('first',array('conditions'=>array('FavoriteHospital.id'=> $gid)));
		
    	if($favhos['FavoriteHospital']['user_id'] != $this->Auth->user('id'))
    	{
            $this->Session->setFlash('Not your hospital Group.');
            $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        }
        else {
        	$this->FavoriteHospital->deleteHospital($gid, $hid);
        	$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        }
    }
		
}