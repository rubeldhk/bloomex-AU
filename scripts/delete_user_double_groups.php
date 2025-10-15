<?php
$limit=500;

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

function delete_user_group($user_id, $count) {
    global $mysqli;
    
    $sql = "DELETE FROM `jos_vm_shopper_vendor_xref`
    WHERE `user_id`=".$user_id." LIMIT ".$count."
    ";
    
    if (!$mysqli->query($query)) {
        die('Delete Error: '.$mysqli->error);
    }
}

$query = "SELECT 
    `user_id`, 
    COUNT(*) AS `c`
FROM `jos_vm_shopper_vendor_xref`
GROUP BY `user_id`
HAVING `c`>1 LIMIT ".$limit."
";

$result = $mysqli->query($query);
$users=array();
$j=0;

while ($row = $result->fetch_object()) {
    delete_user_group($row->user_id, $row->c-1);
    $j++;
}

$result->close();
$mysqli->close();

echo "we deleted ".$j." items";

?>