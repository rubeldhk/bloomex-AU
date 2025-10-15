<?php

$order_id = $_REQUEST['order_id'] ? $_REQUEST['order_id'] : '';
$start = $_REQUEST['start'] ? $_REQUEST['start'] : '';
if ($order_id) {
    $where = '`o`.`order_id` = "' . $order_id . '"';
} elseif ($start) {
    $where = '`o`.`order_id` > "' . $start . '"';
} else {
    die("no start");
}
include_once '../configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$order_id = $mysqli->escape_string($order_id);

$query = "SELECT 
    `o`.`order_id`,
    `oi`.`order_item_id`,
    `oi`.`product_id`,
    `oi`.`product_quantity`,
`oi`.`order_item_name`
FROM `jos_vm_orders` AS `o`
INNER JOIN `jos_vm_order_item` AS `oi`
    ON
        `oi`.`order_id`=`o`.`order_id`
WHERE {$where}
";
var_dump($query);
$result = $mysqli->query($query);

if ($result->num_rows > 0) {


    while ($obj = $result->fetch_object()) {
        $inserts = array();
        $query = "DELETE from jos_vm_order_item_ingredient 
        WHERE `order_item_id`='" . $obj->order_item_id . "'
        ";
        $mysqli->query($query);

        if (strpos($obj->order_item_name, '(supersize)')) {
            $prod_type = " `pil`.`igl_quantity_supersize`  as igl_quantity ";
        } elseif (strpos($obj->order_item_name, '(deluxe)')) {
            $prod_type = " `pil`.`igl_quantity_deluxe` as igl_quantity";
        } else {
            $prod_type = " `pil`.`igl_quantity`  as igl_quantity";
        }

        $query = "SELECT 
            `pio`.`igo_product_name`,
            $prod_type
        FROM `product_ingredients_lists` AS `pil`
        INNER JOIN `product_ingredient_options` AS `pio`
            ON
                `pio`.`igo_id`=`pil`.`igo_id`
        WHERE `pil`.`product_id`=" . $obj->product_id . "
        ";

        $result_order_item = $mysqli->query($query);

        if ($result_order_item->num_rows > 0) {
            while ($obj_order_item = $result_order_item->fetch_object()) {
                $inserts[] = "(
                    " . $obj->order_id . ",
                    " . $obj->order_item_id . ",
                    '" . $mysqli->real_escape_string($obj_order_item->igo_product_name) . "',
                    " . ($obj_order_item->igl_quantity * $obj->product_quantity) . "
                )";
            }
        }
        if (count($inserts) > 0) {
            $query = "INSERT INTO `jos_vm_order_item_ingredient`
        (
            `order_id`,
            `order_item_id`,
            `ingredient_name`,
            `ingredient_quantity`
        )
        VALUES " . implode(',', $inserts) . "
        ";
            $mysqli->query($query);
        }
        echo "order_id " . $obj->order_id . " order_item_name " . $obj->order_item_name . " updated /n/r<br/>";
    }
}

$result->close();

$mysqli->close();

