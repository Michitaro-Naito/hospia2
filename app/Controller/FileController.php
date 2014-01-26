<?php
/**
 * Manages Files uploaded by Admin.
 * Files will be saved to /app/webroot/files
 * Admin only.
 */
class FileController extends AppController{
	
	public function Index(){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		$this->paginate = array(
			'File'=>array(
				'order'=>array('File.id'=>'desc'),
				'limit'=>50,
			)
		);
		$files = $this->paginate('File');
		$this->set('files', $files);
	}
	
	public function Upload(){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		if(!empty($_FILES) && !empty($_FILES['upfile'])){
			$upfile = $_FILES['upfile'];
			if($upfile['error']===0 && $upfile['size']>0){
				// Successfully received a file.
				$name = $upfile['name'];
				
				// Modify name if there is a same name already.
				$count = $this->File->find('count', array(
					'conditions'=>array(
						'File.name'=>$name
					)
				));
				if($count > 0){
					// Modify name
					$name = uniqid() . $name;
				}
				
				// Move to the permanent directory.
				$path = realpath('./files') . '/' . $name;
				move_uploaded_file($upfile['tmp_name'], $path);
				$this->File->create(array('File'=>array(
					'name'=>$name
				)));
				if($this->File->save()){
					$this->redirect(array('controller'=>'File', 'action'=>'Index'));
				}
			}
		}
	}
}
