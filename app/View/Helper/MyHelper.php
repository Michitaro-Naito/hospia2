<?php
class MyHelper extends AppHelper{
	public $helpers = array('Html');
	
	public function Tip($key){
		//<div class="tip" data-tipkey="医療圏">tip</div>
		return $this->Html->tag('span', '?', array('class'=>'tip', 'data-tipkey'=>$key));
	}
}
