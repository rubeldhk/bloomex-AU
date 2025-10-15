<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT 
  `o`.`order_id`,
  `o`.`order_total`,
  `o`.`order_tax`,
  `o`.`order_shipping`
FROM 
  `jos_vm_partners_orders` AS `p`
INNER JOIN `jos_vm_orders` AS `o`
    ON
    `o`.`order_id`=`p`.`order_id`
    AND
    `o`.`order_total`=`o`.`order_subtotal`
";

$result = $mysqli->query($query);
    
if ($result->num_rows > 0) {
    while ($obj = $result->fetch_object()) {
        $new_subtotal = $obj->order_total - $obj->order_tax - $obj->order_shipping;
        
        $query = "UPDATE `jos_vm_orders` 
        SET
            `order_subtotal`='" . $new_subtotal . "'
        WHERE
            `order_id`=" . $obj->order_id . "
        ";
        
        echo '<pre>';
        print_r($query);
        echo '</pre>';
        
        $mysqli->query($query);
    }
}
$result->close();

$mysqli->close();


