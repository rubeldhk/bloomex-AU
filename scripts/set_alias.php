<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

/*
$query = "SELECT 
    `p`.`product_id`
FROM `jos_vm_product` AS `p`
LEFT JOIN `jos_vm_product_options` AS `po`
    ON
        `po`.`product_id`=`p`.`product_id`
WHERE `po`.`product_id` IS NULL";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = [];
    
    while ($obj = $result->fetch_object()) {
        $inserts[] = "(".$obj->product_id.")";
    }
    
    $query = "INSERT `jos_vm_product_options` 
    (
        `product_id`
    )
    VALUES ".implode(',', $inserts)."
    ";
    
    echo $query;
        
    $mysqli->query($query);
}
die;
*/

$query = "SELECT
    `lp`.`id`,
    `lp`.`url`
FROM `tbl_landing_pages` AS `lp`
ORDER BY `lp`.`id` ASC
LIMIT ".$_GET['start'].", 500
";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = [];
    
    while ($obj = $result->fetch_object()) {
        //$inserts[] = "('gift-hamper-basket/".$obj->url."', '".$obj->url."/gift-baskets', '1')";
        //$inserts[] = "('florist/".$obj->url."', '".$obj->url."/flowers', '1')";
        $inserts[] = "('sympathy-flowers/".$obj->url."/', '".$obj->url."/occasions/sympathy-funeral-flowers/', '1')";
    }
    
    $query = "INSERT `jos_aliases` 
    (
        `from`,
        `to`,
        `status`
    )
    VALUES ".implode(',', $inserts)."
    ";
    
    echo $query;
        
    $mysqli->query($query);
}
    

die;


/*
$query = "SELECT 
    `pp`.`product_id`,
    `pp`.`product_price`
FROM `jos_vm_product_price` AS `pp`
ORDER BY `pp`.`product_id`";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    while ($obj = $result->fetch_object()) {
        
        
        $new_price = number_format($obj->product_price*1.1, 2);
        
        $new_price = preg_replace('/96$/u', '95', $new_price);
        
        echo $obj->product_id.' '.$obj->product_price.'|'.$new_price.'</br>';
        
        $query_new_price = "UPDATE `jos_vm_product_price` 
        SET
            `product_price`='".$new_price."'
        WHERE `product_id`=".$obj->product_id."";
        
        $mysqli->query($query_new_price);
    }
}

die;


$query = "SELECT
    `pc_x`.`category_id`,
    `pc_x`.`product_id`
FROM `jos_vm_product_category_xref` AS `pc_x`
INNER JOIN `jos_vm_category` AS `c`
    ON
    `c`.`category_id`=`pc_x`.`category_id`
    AND
    `c`.`category_publish`='Y'
GROUP BY `pc_x`.`product_id`";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    while ($obj = $result->fetch_object()) {
        $query = "UPDATE `jos_vm_product_options` SET `canonical_category_id`='".$obj->category_id."'
        WHERE `product_id`=".$obj->product_id."";
        
        echo $query.'<br/>';
        $mysqli->query($query);
    }
}
    

die;


$query = "SELECT
    `m`.`id`,
    `c`.`alias`
FROM `jos_menu` AS `m`
INNER JOIN `jos_vm_category` as `c`
    ON
        `c`.`category_id`=`m`.`link`
WHERE `m`.`new_type`='vm_category'";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    while ($obj = $result->fetch_object()) {
        $query = "UPDATE `jos_menu` SET `alias`='".$obj->alias."'
        WHERE `id`=".$obj->id."";
        
        $mysqli->query($query);
    }
}
    
die;

//CATEGORY

$query = "SELECT 
    `c`.`category_id`,
    `c`.`category_name`
FROM `jos_vm_category` AS `c`
ORDER BY `c`.`category_id`";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        $alias = preg_replace('/\-{2,}/siu', '-', strtolower(str_replace(array('  ', ' '), '-', preg_replace('/[^a-z0-9\-\s]/siu', '', $obj->category_name))));
        
        echo $obj->category_name.' || '.$alias.'<br/>';
        
        $query = "UPDATE `jos_vm_category`
        SET `alias`='".$alias."'
        WHERE `category_id`=".$obj->category_id."
        ";
            
        $mysqli->query($query);
    }
}

$result->close();


//PRODUCT

$query = "SELECT 
    `p`.`product_id`,
    `p`.`product_name`
FROM `jos_vm_product` AS `p`
ORDER BY `p`.`product_id`";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        $alias = preg_replace('/\-{2,}/siu', '-', strtolower(str_replace(array('  ', ' '), '-', preg_replace('/[^a-z0-9\-\s]/siu', '', $obj->product_name))));

        echo $obj->product_name.' || '.$alias.'<br/>';
        
        $query = "UPDATE `jos_vm_product`
        SET `alias`='".$alias."'
        WHERE `product_id`=".$obj->product_id."
        ";
            
        $mysqli->query($query);
    }
}

$result->close();

//MENU

$query = "SELECT 
    `m`.`id`,
    `m`.`name`
FROM `jos_menu` AS `m`
ORDER BY `m`.`id`";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        $alias = preg_replace('/\-{2,}/siu', '-', strtolower(str_replace(array('  ', ' '), '-', preg_replace('/[^a-z0-9\-\s]/siu', '', $obj->name))));
        
        echo $obj->name.' || '.$alias.'<br/>';
        
        $query = "UPDATE `jos_menu`
        SET `alias`='".$alias."'
        WHERE `id`=".$obj->id."
        ";
            
        $mysqli->query($query);
    }
}

$result->close();

$mysqli->close();
*/
?>