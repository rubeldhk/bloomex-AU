<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$file = file('./AUS.csv');

$ids = array();

foreach ($file as $order_id) {
    $order_id = (int)$order_id;
    
    $query = "SELECT 
        `o`.`user_id`
    FROM `jos_vm_orders` AS `o`
    WHERE
        `o`.`order_id`=" . $order_id . "
    LIMIT 1
    ";
    
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $obj = $result->fetch_object();
        
        if (!in_array($obj->user_id, $ids)) {
            $ids[] = $obj->user_id;
        }
    }
    $result->close();
}

if (count($ids) > 0) {
    $query = "UPDATE 
    `jos_users` AS `u`
    SET
    `u`.`block`='1'
    WHERE
        `u`.`id` IN (" . implode(', ', $ids ). ")
    ";
    
    $mysqli->query($query);
    
    echo '<pre>';
    print_r($query);
    echo '</pre>';
}

$mysqli->close();