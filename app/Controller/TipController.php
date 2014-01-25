<?php

/**
 * Lets Admin manage tips (short descriptions for Users).
 */
class TipController extends AppController{
	
	public function Index(){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		$this->request->data['VM'] = $this->request->query;
		$cond = array();
		if(!empty($this->request->data['VM']['name']))
			$cond['Tip.name like'] = "%{$this->request->data['VM']['name']}%";
		$this->paginate = array(
			'paramType'=>'querystring',
			'order'=>array('Tip.id'=>'desc'),
			'limit'=>50,
		);
		$tips = $this->paginate('Tip', $cond);
		$this->set('tips', $tips);
	}
	
	public function Edit($id = null){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		if(empty($this->data)){
			$this->data = $this->Tip->findById($id);
		}else{
			if($this->Tip->save($this->data)){
				$this->Session->setFlash('Saved!');
				return $this->redirect(array('action'=>'Index'));
			}
		}
	}
	
	public function Delete($id = null){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		if($this->request->isPost()){
			$this->Tip->delete($id, false);
			return $this->redirect(array('action'=>'Index'));
		}
	}
	
	/**
	 * TODO: Cache
	 */
	public function View($name = null){
		$tip = $this->Tip->findByName($name);
		$content = '';
		if($tip !== false)
			$content = $tip['Tip']['value'];
		$this->autoRender = false;
		$this->response->body($content);
	}
	
}
