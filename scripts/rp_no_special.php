<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$query = "SELECT 
    `product_id`, 
    `product_name`, 
    `product_sku`
FROM `jos_vm_product`
WHERE `product_sku` LIKE '%RP%'";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $product_ids = array();
    while($result_obj = $result->fetch_object()) {
        echo $result_obj->product_sku.'<br/>';
        
        $product_ids[] = $result_obj->product_id;
    }
    
    $query = "UPDATE `jos_vm_product_options` SET `no_special`='1' WHERE `product_id` IN (".implode(',', $product_ids).")";
    $mysqli->query($query);
}