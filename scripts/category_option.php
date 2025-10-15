<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT 
    `c`.`category_id`
FROM `jos_vm_category` AS `c`
";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();
    
    while($result_obj = $result->fetch_object()) {
        $inserts[] = "(".$result_obj->category_id.", '1')";
    }
    
    if (sizeof($inserts) > 0) {
        $query = "INSERT INTO `jos_vm_category_options` 
        (
            `category_id`,
            `category_type`
        )
        VALUES ".implode(',', $inserts)."
        ";
        
        echo $query;
        
        $mysqli->query($query);
    }
}

$result->close();
$mysqli->close();

?>
