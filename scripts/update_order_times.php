<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);



$query = "SELECT order_id,date_added from jos_vm_order_history "
        . "where comments LIKE 'Front End'";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $orders = array();
    while ($result_obj = $result->fetch_object()) {
        $timestamp = strtotime($result_obj->date_added) - 15 * 60 * 60;
        $order_id = $result_obj->order_id;
        $query = "UPDATE `jos_vm_orders` SET `cdate`='$timestamp', mdate='$timestamp' WHERE `order_id` like '$order_id';";
        echo $query;
        $mysqli->query($query);
    }
}
echo '$a40 ';
echo "$a40";