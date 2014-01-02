<?php
class FavoriteHospitalController extends AppController {
	public $components = array('Auth');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
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
		
}