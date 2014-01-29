<?php
class MyHelper extends AppHelper{
	public $helpers = array('Html');
	
	public function Tip($key, $options = null){
		//<div class="tip" data-tipkey="医療圏">tip</div>
		if(isset($options)){
			if(!is_array($options))
				throw new Exception('$options must be an array.');
			if(!empty($options['image']))
				return $this->Html->image('icon/question.png', array('style'=>array('padding:6px;float:right;'), 'class'=>'tip', 'data-tipkey'=>$key));
		}
		return $this->Html->image('icon/question.png', array('style'=>array('padding: 0 0 5px 0;'), 'class'=>'tip', 'data-tipkey'=>$key));
		//return $this->Html->tag('span', '?', array('class'=>'tip', 'data-tipkey'=>$key));
	}
}
