<?php

include_once MY_ROOT . '/modules/' . 'default' . '/m.php';
include_once MY_ROOT . '/modules/' . 'default' . '/v.php';

class default_controller {

    private $mysqli = null;
    private $class_model = null;
    private $class_view = null;

    public function __construct() {
        global $mysqli;

        $this->mysqli = $mysqli;
        $this->class_model = new default_model();
        $this->class_view = new default_view();
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