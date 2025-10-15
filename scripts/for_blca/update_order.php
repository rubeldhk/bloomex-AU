<?php

include_once $_SERVER['DOCUMENT_ROOT'] . 'configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$mysqli->set_charset('utf8');
$result = array('result' => 'false');
$query = 'UPDATE jos_vm_orders SET 
            customer_note="' . $mysqli->real_escape_string($_POST['card_msg']) . '",
            customer_signature="' . $mysqli->real_escape_string($_POST['signature']) . '",
            customer_occasion="' . $mysqli->real_escape_string($_POST['customer_occasion']) . '",
            customer_comments="' . $mysqli->real_escape_string($_POST['card_comment']) . '",
            order_status="' . $_POST['orderStatus'] . '",
            ddate="' . $_POST['ddate'] . '" 
            WHERE order_id=' . $_POST['order_id'];

$result = $mysqli->query($query);
if ($result) {
    $result['result'] = true;
} else {
    $result['error'] = $mysqli->error;
    $result['errorno'] = $mysqli->errno;
}

echo json_encode($result);
