<?php
class FavoriteHospitalController extends AppController {
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
    
    public function addHospital($id = null) {
    	$hid = 1010111085;
    	$gid = 3;
    }
		
}