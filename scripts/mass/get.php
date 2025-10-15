<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT 
    `order_id`
FROM `jos_vm_orders`
WHERE 
STR_TO_DATE( `ddate` , '%d-%m-%Y' ) BETWEEN '2018-12-01' AND '2018-12-31'
";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        $inserts[] = "(".$obj->order_id.")";
    }
}
$result->close();

if (count($inserts) > 0) {
    $query = "INSERT INTO `tbl_for_mass_1`
    (
        `order_id`
    )
    VALUES ".implode(',', $inserts)."
    ";
    
    echo $query;
    
    $mysqli->query($query);
}

$mysqli->close();