<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

set_time_limit(600);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$query = "SELECT  `h1`. * 
FROM  `jos_vm_order_history` AS  `h1` 
LEFT JOIN  `jos_vm_order_history` AS  `h2` ON  `h2`.`order_id` =  `h1`.`order_id` 
AND  `h2`.`order_status_history_id` <  `h1`.`order_status_history_id` 
WHERE  `h2`.`order_status_history_id` IS NOT NULL 
AND  `h1`.`date_added` <  `h2`.`date_added` 
AND  `h1`.`order_status_code` =  'Z'
GROUP BY  `h1`.`order_id` LIMIT 1000";

$ids = array();

if ($result = $mysqli->query($query)) {
    $i = 0;
    while ($obj = $result->fetch_object()) {
        $ids[] = $obj->order_status_history_id;
            
        $i++;
    }
}

echo '<hr/>'.$i;

if (sizeof($ids) > 0) {
    $query = "UPDATE  `jos_vm_order_history` AS `h1` SET  `h1`.`date_added` = DATE_ADD(`h1`.`date_added` , INTERVAL 16 HOUR ) WHERE `order_status_history_id` IN (".implode(',', $ids).")";
    $mysqli->query($query);
}

$mysqli->close();
?>
