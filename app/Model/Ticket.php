<?php 
class Ticket extends AppModel {
    var $name = 'Ticket';   
    
    var $validate = array(
        'dummy' => array(
            'rule' => 'notEmpty',
            'message' => 'Please use setTicket instead!'
        )
    );
 
    function beforeValidate($options = null) {
        if (!isset($this->data[$this->alias]['dummy']) ) {
            $this->data[$this->alias]['dummy'] = '';
        }
        return true;
    }
 
    
    function setTicket($url = null, $data = null, $deadline = null, $usecount = null) {
        $this->_garbage();
        if ( $url && $data ) {
            if ( empty($deadline) ) {
                $deadline = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
            }
            $record[$this->alias]['hash'] = Security::hash(time().$url, 'md5');
            $record[$this->alias]['caller'] = $url;
            $record[$this->alias]['data'] = $data;
            $record[$this->alias]['deadline'] = $deadline;
            $record[$this->alias]['usecount'] = $usecount;
            $record[$this->alias]['dummy'] = 'dummy';
            
            if ( $this->save($record) ) {
                return $record[$this->alias]['hash'];
            }
        }
        return false;
    }
    
    function getTicket($url = null, $hash = null) {
        $this->_garbage();
        if ( $url && $hash ) {
            $result = $this->findByHash($hash);
            if ( is_array($result) && is_array($result[$this->alias]) && ($result[$this->alias]['caller']==$url) ) {
                return $result[$this->alias]['data'];
            }
        }
        return false;
    }
 
    function del($hash = null) {
        $this->_garbage();
        if ($hash) 
        { 
            $ticketObj = new Ticket(); 
            $data = $ticketObj->findByHash($hash); 
            if ( is_array($data) && is_array($data['Ticket']) ) 
            { 
                return $data =  $this->delete($data['Ticket']['id']);
            } 
        } 
        return false; 
    }
 
 
    function _garbage() {
        $now = date('Y-m-d H:i:s', time());
        $result = $this->deleteAll( array(
            'OR' => array(
                $this->alias.'.deadline <' => $now,
                $this->alias.'.deadline' => null
            )
        ));
    }
}
?>