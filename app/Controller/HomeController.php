<?php
class HomeController extends AppController {
	
	
	public function index() {
		/*debug('abc');
		debug('def');
		$this->loadModel('Dpc');
		$dat = $this->Dpc->find('all', array('limit'=>5));
		debug($dat);*/
	}
	
	public function hoslist(){
		$this->set('prefectures', $this->_getPrefectures());
		$this->set('displayTypes', $this->_getDisplayTypes());
	}
	
	public function dpc(){
		$this->set('mdcs', $this->getMdcs());
		$this->set('prefectures', $this->_getPrefectures());
	}
	
	public function toplst(){
		
	}
	
}