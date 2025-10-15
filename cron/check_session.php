<?php


include_once '../configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT
    * 
FROM `jos_user_session_info`
";

$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    echo " user_id ".$obj->user_id."<br>";
    echo " order_id ".$obj->order_id."<br>";
    echo " item_id ".$obj->item_id."<br>";
    echo " action_date ".$obj->action_date."<br>";
    echo " check ".$obj->check."<br>"; 
    echo " checkout step ".$obj->checkout_step."<br>"; 
    echo " request ".$obj->request."<br>"; 
                 
}

$result->close();
$mysqli->close();
       
?>
