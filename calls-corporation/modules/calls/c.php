<?php

$class_folder = end(explode('/', __DIR__));

include_once MY_ROOT.'/modules/'.$class_folder.'/m.php';
include_once MY_ROOT.'/modules/'.$class_folder.'/v.php';

class calls_controller {

    private $mysqli = null;
    private $class_model = null;
    private $class_view = null;
    private $default_class = null;

    public function __construct() {
        global $mysqli, $default_class, $class_folder;

        $this->mysqli = $mysqli;
        $class_model = $class_folder.'_model';
        $class_view = $class_folder.'_view';
        $this->class_model = new $class_model;
        $this->class_view = new $class_view;
        $this->default_class = $default_class;
    }

    public function __destruct() {
        unset($this->class_model, $this->class_view);
    }

    public function router() {
        Switch (isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
            case 'setCount':
                $this->setCount();
            break;
        
            case 'reQueue':
                $this->reQueue();
                break;

            case 'sendEmail':
                $this->sendEmail();
                break;

            case 'noCorp':
                $this->noCorp();
                break;

            case 'stakeHolder':
                $this->saveCompany(true);
                break;

            case 'saveCompany':
                $this->saveCompany();
                break;

            case 'callAttempt':
                $this->callAttempt();
                break;

            case 'logout':
                $this->setLogout();
                break;

            case 'login':
                $this->setLogin();
                break;

            default:
                $this->setDefault();
        }
    }

    private function setDefault() {
        $json = $this->class_model->getDefault();
        $obj = json_decode($json);

        if ($obj->result == true) {
            $page_title = 'Corporate calls';
            $page_description = 'Corporate calls';

            $this->default_class->setHeader($page_title, $page_description);
            if (isset($_COOKIE['extension'])) {
                $this->class_view->setDefault($obj, $this->default_class);
            }
            else {
                $this->class_view->setLoginForm($obj, $this->default_class);
            }
            $this->default_class->setFooter();
        }
    }

    private function setLogout() {
        $json = $this->class_model->getLogout();
        $obj = json_decode($json);

        header('Location: '.MY_PATH);
    }

    private function setLogin() {
        $json = $this->class_model->getLogin($_POST['extension']);
        $obj = json_decode($json);

        header('Location: '.MY_PATH);
    }

    private function callAttempt() {
        $json = $this->class_model->callAttempt();

        echo $json;
    }

    private function saveCompany($type = false) {
        $json = $this->class_model->saveCompany();

        if ($type == true) {
            $this->stakeHolder();
        }

        echo $json;
    }

    private function stakeHolder() {
        $json = $this->class_model->stakeHolder();

        return $json;
    }

    private function noCorp() {
        $json = $this->class_model->noCorp();

        echo $json;
    }

    private function sendEmail() {
        $json = $this->class_model->sendEmail();

        echo $json;
    }

    private function reQueue() {
        $json = $this->class_model->reQueue();

        echo $json;
    }
    
    private function setCount() {
        $json = $this->class_model->getCount();
        echo $json;
    }

}

$class_name = $class_folder.'_controller';
${$class_name} = new $class_name;

${$class_name}->router();

unset(${$class_name});
?>