<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$id_a = array();

$query = "SELECT
    `product_id` 
FROM `jos_vm_product_options` 
WHERE `show_deluxe_supersize`=1
";
    
$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    $id_a[] = $obj->product_id;
}

$result->close();

$query ="UPDATE `jos_vm_product_options`
SET 
    `deluxe`='5',
    `supersize`='10' 
WHERE `product_id` IN (".implode(',', $id_a).")";

var_dump($query);
echo "<hr/>";

if (!$mysqli->query($query)) {
    die('Delete Error: '.$mysqli->error);
}

echo "<hr/>";

$mysqli->close();

?>
