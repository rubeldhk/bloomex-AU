<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT `product_id`, `product_name` FROM `jos_vm_product`
    WHERE 
    `product_name` LIKE '%basket%'
    ";

    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $products_id = array();
        
        while ($obj = $result->fetch_object()) {
            echo $obj->product_name.'<br/>';
            $products_id[] = $obj->product_id;
        }
        
        $query = "UPDATE `jos_vm_product_options` SET `product_type`='2' WHERE `product_id` IN (".implode(',', $products_id).")";
        
        $mysqli->query($query);
    }
?>

