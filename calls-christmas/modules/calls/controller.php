<?php

$class_folder = 'calls';

include_once MY_ROOT . '/modules/' . $class_folder . '/model.php';
include_once MY_ROOT . '/modules/' . $class_folder . '/view.php';

class calls_controller {

    private $mysqli = null;
    private $class_model = null;
    private $class_view = null;
    private $default_class = null;

    public function __construct() {
        global $mysqli, $default_class, $class_folder;

        $this->mysqli = $mysqli;
        $class_model = $class_folder . '_model';
        $class_view = $class_folder . '_view';
        $this->class_model = new $class_model;
        $this->class_view = new $class_view;
        $this->default_class = $default_class;
    }

    public function __destruct() {
        unset($this->class_model, $this->class_view);
    }

    public function router() {
        if (isset($_SESSION['extension'])) {
            switch (isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
                case 'sendEmails':
                    $this->sendEmails();
                    break;

                case 'setCount':
                    $this->setCount();
                    break;

                case 'getHistories':
                    $this->getHistories();
                    break;

                case 'setVoicemail':
                    $this->setStatus(4);
                    break;

                case 'setHot':
                    $this->setStatus(2);
                    break;

                case 'setNot':
                    $this->setStatus(3);
                    break;

                case 'setBuyer':
                    $this->setStatus(1);
                    break;

                case 'reQueue':
                    $this->reQueue();
                    break;

                case 'reSendEmail':
                    $this->reSendEmail();
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

                case 'startCall':
                    $this->startCall();
                    break;

                case 'getNextPrevCorpUser':
                    $this->getNextPrevCorpUser();
                    break;

                default:
                    $this->setDefault();
            }
        }
        elseif (isset($_REQUEST['task']) AND $_REQUEST['task'] == 'login') {
            $this->setLogin();
        }
        else {
            $this->setDefault();
        }
    }

    private function startCall()
    {
        $json = $this->class_model->startCall();
        return json_decode($json);
    }

    private function getNextPrevCorpUser()
    {
        $json = $this->class_model->getNextPrevCorpUser();
        return json_decode($json);
    }

    private function setDefault() {
        $json = $this->class_model->getDefault();
        $obj = json_decode($json);

        if ($obj->result == true) {
            $page_title = 'Corporate calls';
            $page_description = 'Corporate calls';

            $this->default_class->setHeader($page_title, $page_description);
            if (isset($_SESSION['extension'])) {
                $this->class_view->setDefault($obj, $this->default_class);
            } else {
                $this->class_view->setLoginForm($obj, $this->default_class);
            }
            $this->default_class->setFooter();
        }
    }

    private function setLogout() {
        $json = $this->class_model->getLogout();
        header("HTTP/1.1 205 Reset Content");
        header('Location: ' . MY_PATH);
    }

    private function setLogin() {
        $json = $this->class_model->getLogin($_POST['extension'], $_POST['project']);
        header("HTTP/1.1 205 Reset Content");
        header('Location: ' . MY_PATH);
    }

    private function callAttempt() {
        $json = $this->class_model->callAttempt();

        echo $json;
    }

    private function reSendEmail() {
        $json = $this->class_model->reSendEmail();

        echo $json;
    }

    private function reQueue() {
        $json = $this->class_model->reQueue();

        echo $json;
    }

    private function setStatus($status) {
        $json = $this->class_model->setStatus($status);

        echo $json;
    }

    private function getHistories() {
        $json = $this->class_model->getHistories((int) $_POST['id']);

        echo $json;
    }

    private function setCount() {
        $json = $this->class_model->getCount();

        echo $json;
    }
    
    private function sendEmails() {
        $json = $this->class_model->sendEmails();

        echo $json;
    }

}

$class_name = $class_folder . '_controller';
${$class_name} = new $class_name;

${$class_name}->router();

unset(${$class_name});