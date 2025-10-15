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
        $addr = $_POST['suite'] . ' ' . $_POST['street_number'] . ' ' . $_POST['street_name'];
        $query = "UPDATE `jos_vm_user_info` 
        SET
            `company`='".$mysqli->real_escape_string($_POST['company'])."',
            `title`='".$mysqli->real_escape_string($_POST['title'])."',
            `first_name`='".$mysqli->real_escape_string($_POST['first_name'])."',
            `last_name`='".$mysqli->real_escape_string($_POST['last_name'])."',
            `suite`='".$mysqli->real_escape_string($_POST['suite'])."',
            `street_number`='".$mysqli->real_escape_string($_POST['street_number'])."',
            `street_name`='".$mysqli->real_escape_string($_POST['street_name'])."',
            address_1	= '".$addr."',
            `city`='".$mysqli->real_escape_string($_POST['city'])."',
            `address_type2`='".$mysqli->real_escape_string($_POST['address_type2'])."',
            `zip`='".$mysqli->real_escape_string($_POST['zip'])."',
            `country`='".$mysqli->real_escape_string($_POST['country'])."',
            `state`='".$mysqli->real_escape_string($_POST['state'])."',
            `phone_1`='".$mysqli->real_escape_string($_POST['phone_1'])."',
            `phone_2`='".$mysqli->real_escape_string($_POST['phone_2'])."',
            `user_email`='".$mysqli->real_escape_string($_POST['email'])."'
        WHERE `user_info_id`='".$mysqli->real_escape_string($obj->user_info_id)."'";    
        
        $mysqli->query($query);
        
        $return['result'] = true;
    }
}

echo json_encode($return);


$mysqli->close();

?>
    
