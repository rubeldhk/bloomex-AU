<?php

if (is_file($_SERVER['DOCUMENT_ROOT'].'/scripts/configuration.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/configuration.php';
}
else {
    include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';
}

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

date_default_timezone_set('Australia/Sydney');

$query = "SELECT 
        `o`.* 
FROM `jos_vm_partners_orders` AS `po` 
INNER JOIN `jos_vm_orders` AS `o` 
    ON `o`.`order_id`=`po`.`order_id` 
WHERE `po`.`order_id` >= 1432602";
    
$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    
    $item_query = "SELECT 
        `oi`.`order_item_id`, 
        `oi`.`product_quantity`, 
        `oi`.`product_item_price`, 
        (pp.product_price - pp.saving_price) AS 'new_price' 
    FROM `jos_vm_partners_orders` AS `po` 
    INNER JOIN `jos_vm_order_item` AS `oi` ON `oi`.`order_id` = `po`.`order_id` 
    INNER JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id` = `oi`.`product_id` 
    WHERE `po`.`order_id`=".$obj->order_id." 
    GROUP BY `oi`.`order_item_id`";

    $item_result = $mysqli->query($item_query);

    $shipping = 14.95;
    $order_subtotal = 0;
    
    while ($item_obj = $item_result->fetch_object()) {
        $order_subtotal += $item_obj->new_price*$item_obj->product_quantity;
        
        $update = "UPDATE `jos_vm_order_item`
        SET
            `product_item_price`='".number_format($item_obj->new_price, 2, '.', '')."',
            `product_final_price`='".number_format($item_obj->new_price, 2, '.', '')."'
        WHERE `order_item_id`=".$item_obj->order_item_id."
        ";
        
        echo $update.'<br/>';
        
        $mysqli->query($update);
        echo 'DONE'.'<br/>';
    }   
    $item_result->close();
    
    $order_subtotal = number_format($order_subtotal, 2, '.', '');
    $order_total = number_format($order_subtotal+$obj->order_shipping, 2, '.', '');
    
    $update = "UPDATE `jos_vm_orders`
    SET
        `order_total`='".$order_total."',
        `order_subtotal`='".$order_subtotal."',	
        `order_tax`='0',
        `order_shipping_tax`='0'
    WHERE `order_id`=".$obj->order_id."
    ";
        
    echo $update.'<br/>';
    $mysqli->query($update);
    echo 'DONE'.'<br/>';
}
$result->close();
$mysqli->close();