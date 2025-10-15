<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$id_a = array();

$query = "SELECT  
    `user_id` 
FROM `tbl_platinum_club` 
WHERE `end_datetime` IS NULL
";

$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    $id_a[] = $obj->user_id;
}

$result->close();

if (sizeof($id_a) > 0) {
    $query = "UPDATE `jos_vm_shopper_vendor_xref`
    SET 
    `shopper_group_id`='5'
    WHERE `user_id` 
    IN (".implode(',', $id_a).")";
    
    if ($mysqli->query($query)) {
        echo $query;
        die('Update Error: '.$mysqli->error);
    }
}

$mysqli->close();

?>

