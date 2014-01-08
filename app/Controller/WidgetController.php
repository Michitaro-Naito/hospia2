<?php
class WidgetController extends AppController{
	
	/**
	 * Introduces Widget to User
	 */
	public function About(){
		
	}
	
	/**
	 * Returns JavaScript for Widget
	 */
	public function Script(){
		$this->layout = null;
		$scode = '';
		if(isset($_GET['scode']))
			$scode = $_GET['scode'];
		$this->set('scode', $scode);
		$this->response->type('js');
	}
}
