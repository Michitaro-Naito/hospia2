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
    	//tested with hid = 1010111085 and hid = 1010112489
		//http://localhost:8888/hospia2/favoritehospitals/addHospital/3/1010111085
		
    	$favhos = $this->FavoriteHospital->find('first',array('conditions'=>array('FavoriteHospital.id'=> $gid)));

    	if($favhos['FavoriteHospital']['user_id'] != $this->Auth->user('id'))
    	{
            $this->Session->setFlash('Not your hospital Group.');
            $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        }
        else {
        	$this->loadModel('Hospital');
    		$hos = $this->Hospital->find('count',array('conditions'=>array('Hospital.wam_id'=> $hid)));
    		if($hos){
        		if($this->FavoriteHospital->addHospital($gid, $hid)){
        			$this->Session->setFlash('Hospital Added To Group');
        			$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        		} else {
        			$this->Session->setFlash('Adding Hospital To Group Failed');
        			$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        		}
        	} else {
        		$this->Session->setFlash('No Such Hospital Exists');
        		$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        	}
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