<?php

class default_model {
    private $mysqli = null;
   
    public function __construct() {
        global $mysqli;
        
        $this->mysqli = $mysqli;
    }
    
    public function setHeader() {
        $return = array();
        $return['result'] = false;
    }

}
