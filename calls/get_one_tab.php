<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND !empty($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    session_start();
    
    include_once './config.php';
    
    $return = array();

    if (isset($_SESSION['session_hash'])) {
        if ($_SESSION['session_hash'] == $_POST['session_hash']) {
            $_SESSION['session_expired'] = time();
            
            $return['result'] = true;
        }
        else {
            if (time() > $_SESSION['session_expired']+$session_expired*60) {
                $_SESSION['session_hash'] = $_POST['session_hash'];
                $_SESSION['session_expired'] = time();

                $return['result'] = true;
            }
            else {
                $return['result'] = false;
            }
        }
    }
    else {
        $_SESSION['session_hash'] = $_POST['session_hash'];
        $_SESSION['session_expired'] = time();
        
        $return['result'] = true;
    }
    
    echo json_encode($return);
}

?>
