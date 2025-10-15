<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

if ($mysqli->connect_error) {
    echo '<br/>Bloomex Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
} 
else {
    echo "Bloomex connected<br/>";
}

function delete_order($order_id) {
    global $mysqli;
    
    $query = "SELECT
        `order_id` 
    FROM `jos_vm_orders` 
    WHERE order_id=".$order_id."
    ";
    
    $result = $mysqli->query($query);

    if ($result->num_rows == 0) {
        echo "<br/>no order with order_id=" . $order_id;
    } 
    else {
        $sql_arr = array();
        $sql_arr[] = "DELETE  FROM jos_vm_orders WHERE order_id=" . $order_id;
        $sql_arr[] = "DELETE  FROM jos_vm_order_history WHERE order_id=" . $order_id;
        $sql_arr[] = "DELETE  FROM jos_vm_order_payment WHERE order_id=" . $order_id;
        $sql_arr[] = "DELETE  FROM jos_vm_order_user_info  WHERE order_id=" . $order_id;
        $sql_arr[] = "DELETE  FROM jos_vm_order_item  WHERE order_id=" . $order_id;
        $sql_arr[] = "DELETE  FROM jos_vm_order_product_type  WHERE order_id=" . $order_id;
        
        foreach ($sql_arr as $sql) {
            if (!$mysqli->query($sql)) {
                $result->close();
                die('Delete Error: '.$mysqli->error);
            }
        }
        echo "Order $order_id was deleted successfully<br/>";
    }
    
    $result->close();
}

$query = "SELECT
    `o`.`order_id` 
FROM `jos_vm_orders` AS `o` 
LEFT JOIN `jos_vm_order_history` AS `h` 
    ON `h`.`order_id`=`o`.`order_id` 
WHERE `comments` LIKE '%Pushed to bloomex.com.au [BLCOMA order id: 1273527]%'";

$result = $mysqli->query($query);

while ($row = $result->fetch_object()) {
    delete_order($row->order_id);
}

$result->close();
$mysqli->close();

?>