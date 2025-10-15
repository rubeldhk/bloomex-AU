<?php
date_default_timezone_set('Australia/Sydney');
$date_now = date("Y-m-d G:i:s", time());
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

session_name(md5($mosConfig_live_site));
session_start();

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);


$mysqli->query("INSERT INTO `tbl_pickfleet_push_notofications`
                (
                    `post_data`,
                    `post_date`
                )
                VALUES
                (
                    '" . $mysqli->real_escape_string(json_encode($_POST)) . "','".$date_now."'
                    )
                ");



$json = isset($_POST['json']) ? $_POST['json'] : '';
$input = json_decode($json, TRUE);

//set variables
$order_id = $mysqli->real_escape_string(substr($input['do'], 2));
$order_status = $mysqli->real_escape_string($input['status']);
$delivery_reason = $mysqli->real_escape_string($input['reason']);
$order_note = $mysqli->real_escape_string($input['note']);

$warehouse = "";
$priority = "";

$order_sql = $mysqli->query("SELECT *
        FROM `jos_vm_orders`
        WHERE `order_id`=" . $order_id . "");

if ($order_sql->num_rows > 0) {
    $order_obj = $order_sql->fetch_object();
    if ($order_obj->warehouse == "NOWAREHOUSEASSIGNED") {
        $warehouse = "NOWAR";
    } else {
        $warehouse = $order_obj->warehouse;
    }
    $priority = $order_obj->priority;
} else {
    echo $order_sql->num_rows;
}
if ($input['status'] == 'Delivered') {
    $order_status_code = "D";
    $message = "Delivery confirmation by PickFleet" . ($order_note ? " Drivers note: " . $order_note : "");
} else {
    $order_status_code = "i";
    $message = "Reason: " . $delivery_reason . ". Note: " . $order_note;
}
$mysqli->query("UPDATE `jos_vm_orders`
                SET `order_status`='" . $order_status_code . "'
                WHERE `order_id`=" . $order_id . "
                ");



$mysqli->query("INSERT INTO `jos_vm_order_history`
                (
                    `order_id`, 
                    `order_status_code`, 
                    `priority`,
                    `warehouse`,
                    `date_added`, 
                    `comments`,
                    `user_name` 
                )
                VALUES
                (
                    '" . $mysqli->real_escape_string($order_id) . "',
                    '" . $mysqli->real_escape_string($order_status_code) . "', 
                    '" . $mysqli->real_escape_string($priority) . "',
                    '" . $mysqli->real_escape_string($warehouse) . "',
                    '" . $mysqli->real_escape_string($date_now) . "',
                    '" . $mysqli->real_escape_string($message) . "',
                    'PickFleet bot')
                ");


