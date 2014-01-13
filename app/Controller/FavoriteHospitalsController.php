<?php
class FavoriteHospitalsController extends AppController {
	public $components = array('RequestHandler', 'Data');
	
	/**
	 * お気に入りグループの一覧を取得する。
	 */
	public function GetFavoriteGroups(){
		$loggedIn = $this->Auth->loggedIn();
		$groups = array();
		if($loggedIn){
			$userId = $this->Auth->user('id');
			$this->loadModel('FavoriteHospital');
			$groups = $this->FavoriteHospital->find('all', array(
				'conditions'=>array(
					'FavoriteHospital.user_id'=>$userId
				),
				//'recursive'=>-1
			));
		}
		
		$this->set('dat', array(
			'loggedIn'=>$loggedIn,
			'favoriteGroups'=>$groups
		));
		$this->set('_serialize', array('dat'));
	}
	
	public function AddFavoriteGroup(){
		$result = false;
		$groupId = null;
		
		if($this->Auth->loggedIn()){
			$userId = $this->Auth->user('id');
			$newName = $this->request->data['newName'];
			
			$this->loadModel('FavoriteHospital');
			$this->FavoriteHospital->data['FavoriteHospital']['user_id'] = $userId;
			$this->FavoriteHospital->data['FavoriteHospital']['name'] = $newName;
			if($this->FavoriteHospital->save()){
				$result = true;
				$groupId = $this->FavoriteHospital->id;
			}
		}
		
		$this->set('dat', array(
			'result'=>$result,
			'groupId'=>$groupId
		));
		$this->set('_serialize', array('dat'));
	}
	
	public function RenameFavoriteGroup(){
		$result = false;
		
		if($this->Auth->loggedIn()){
			$userId = $this->Auth->user('id');
			$groupId = $this->request->data['groupId'];
			$newName = $this->request->data['newName'];
			
			$this->loadModel('FavoriteHospital');
			$this->FavoriteHospital->id = $groupId;
			$this->FavoriteHospital->read();
			if($this->FavoriteHospital->data['FavoriteHospital']['user_id'] == $userId){
				$this->FavoriteHospital->data['FavoriteHospital']['name'] = $newName;
				if($this->FavoriteHospital->save())
					$result = true;
			}
		}
		
		$this->set('dat', array('result'=>$result));
		$this->set('_serialize', array('dat'));
	}
	
	/**
	 * 
	 */
	public function AddHospitalToFavoriteGroup(){
		$result = false;
		
		if($this->Auth->loggedIn()){
			$userId = $this->Auth->user('id');
			$groupId = $this->request->data['groupId'];
			$wamId = $this->request->data['wamId'];
			
			$this->loadModel('FavoriteHospital');
			$group = $this->FavoriteHospital->find('first', array(
				'conditions'=>array(
					'FavoriteHospital.id'=>$groupId,
					'FavoriteHospital.user_id'=>$userId
				)
			));
			if(!empty($group)){
				if($this->FavoriteHospital->addHospital($groupId, $wamId))
					$result = true;
			}
		}

		$this->set('dat', array('result'=>$result));
		$this->set('_serialize', array('dat'));
	}
	
	/**
	 * 
	 */
	public function RemoveHospitalFromFavoriteGroup(){
		$result = false;
		
		if($this->Auth->loggedIn()){
			$userId = $this->Auth->user('id');
			$groupId = $this->request->data['groupId'];
			$wamId = $this->request->data['wamId'];
			
			$this->loadModel('FavoriteHospital');
			$group = $this->FavoriteHospital->find('first', array(
				'conditions'=>array(
					'FavoriteHospital.id'=>$groupId,
					'FavoriteHospital.user_id'=>$userId
				)
			));
			if(!empty($group)){
				if($this->FavoriteHospital->deleteHospital($groupId, $wamId))
					$result = true;
			}
		}
		
		$this->set('dat', array('result'=>$result));
		$this->set('_serialize', array('dat'));
	}
	
	/*public $components = array('Auth');
	
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
    
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException(); //Disable this to test delete.
        }
        $this->FavoriteHospital->id = $id;
        if (!$this->FavoriteHospital->exists()) {
            throw new NotFoundException(__('Invalid Group'));
        }
        if ($this->FavoriteHospital->delete()) {
            $this->Session->setFlash(__('Group deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Group was not deleted'));
        $this->redirect(array('action' => 'index'));
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
			if($this->FavoriteHospital->deleteHospital($gid, $hid)){
        		$this->Session->setFlash('Hospital Removed From Group');
        		$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        	} else {
        		$this->Session->setFlash('Removing Hospital Failed');
        		$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        	}	
        }
    }*/
		
}