<?php

$class_folder = 'default';

include_once MY_ROOT.'/modules/'.$class_folder.'/model.php';
include_once MY_ROOT.'/modules/'.$class_folder.'/view.php';

class default_controller {
   
    private $mysqli = null;
    private $class_model = null;
    private $class_view = null;
    
    public function __construct() {
        global $mysqli, $default_class, $class_folder;
        
        $this->mysqli = $mysqli;
        $class_model = $class_folder.'_model';
        $class_view = $class_folder.'_view';
        $this->class_model = new $class_model;
        $this->class_view = new $class_view;
    }
    
    public function __destruct() {
       unset($this->class_model, $this->class_view);
    }
    
    public function setHeader($title = '', $description = '') {
        $json = $this->class_model->setHeader();
        $obj = json_decode($json);
        
        $this->class_view->setHeader($obj, $title, $description);
    }
    
    public function setFooter() {
        $this->class_view->setFooter();
    }
    
}
