<?php

include_once '../configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

if($_REQUEST['order_id']) {
    $order_id = $_REQUEST['order_id'];
    delete_order($order_id);
} 
else {
    die('please write order id');
}

function delete_order($order_id) {
    global $mysqli;
    
    $query = "SELECT
        `order_id` 
    FROM `jos_vm_orders` 
    WHERE 
        `order_id`=".$order_id."
    ";
    
    $result = $mysqli->query($query);

    if (!$result || $result->num_rows == 0) {
           echo "no order with order_id=".$order_id;
    } 
    else {
        $sql_arr = array();
        $sql_arr[] = "DELETE FROM jos_vm_orders WHERE order_id=".$order_id;
        $sql_arr[] = "DELETE FROM jos_vm_order_history WHERE order_id=".$order_id;
        $sql_arr[] = "DELETE FROM jos_vm_order_payment WHERE order_id=".$order_id;
        $sql_arr[] = "DELETE FROM jos_vm_order_user_info  WHERE order_id=".$order_id;
        $sql_arr[] = "DELETE FROM jos_vm_order_item  WHERE order_id=".$order_id;
        $sql_arr[] = "DELETE FROM jos_vm_order_product_type  WHERE order_id=".$order_id;

        foreach ($sql_arr as $sql) {
            $result = $mysqli->query($sql);
            
            if (!$result) {
                $result->close();
                $mysqli->close();
                die('Delete Error: '.$mysqli->error);
            }
            
            $result->close();
        }
        echo "Order $order_id was deleted successfully";
    }
    $result->close();
    $mysqli->close();
}

?>