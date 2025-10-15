<?php
/*
error_reporting(E_ALL &~ E_NOTICE);
ini_set("display_errors", 1);
*/
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$return = array();
$return['result'] = false;

if ($_POST['key'] == md5($_POST['my_id'].$_POST['user_info_id'].'blca') AND $_POST['my_id'] == $_POST['user_id']) {
    $query = "SELECT `user_info_id` FROM `jos_vm_user_info`
    WHERE 
    `user_id`=".(int)$_POST['user_id']."
    AND 
    `user_info_id`='".$mysqli->real_escape_string($_POST['user_info_id'])."'";

    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $obj = $result->fetch_object();

        $query = "DELETE FROM `jos_vm_user_info`
	WHERE 
            `user_info_id`='".$mysqli->real_escape_string($obj->user_info_id)."'";  
        
        $mysqli->query($query);
        
        $return['result'] = true;
    }
}

echo json_encode($return);

$mysqli->close();

?>
    
