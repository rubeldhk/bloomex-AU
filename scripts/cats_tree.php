<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

/*
function cat_childs($id_cat) {
    global $mysqli;
    
    $query = "SELECT * 
    FROM `jos_vm_category_xref` 
    WHERE `category_parent_id`=".$id_cat."";
    
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        while($result_obj = $result->fetch_object()) {
            echo $result_obj->category_child_id.',';
            cat_childs($result_obj->category_child_id);
        }
    }
}

cat_childs(183);*/

$query = "SELECT DISTINCT(`product_id`)
FROM `jos_vm_product_category_xref`
WHERE `category_id` IN (183,185,187,189,191,193,195,199,201,203,205,207,209,211,258,264,278,353) ";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $product_ids = array();
    while($result_obj = $result->fetch_object()) {
        $product_ids[] = $result_obj->product_id;
    }
    
    $query = "UPDATE `jos_vm_product_options` SET `product_type`='2' WHERE `product_id` IN (".implode(',', $product_ids).")";
    echo $query;
    //$mysqli->query($query);
}