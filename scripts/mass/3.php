<?php
session_start();

$id_script = 3;

$return = (object)array(
    'result' => false
);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

Switch (isset($_POST['task']) ? $_POST['task'] : '') {
    case 'getCount':
        $query = "SELECT 
            COUNT(`oi`.`order_id`) AS `count`
        FROM `jos_vm_order_item` AS `oi`
        LEFT JOIN `tbl_mass_scripts_iteration` AS `ms_i`
            ON
            `ms_i`.`id_script_inner`=`oi`.`order_id`
            AND
            `ms_i`.`id_script`=".$id_script."
        INNER JOIN `jos_vm_orders` AS `o`
            ON
                STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') BETWEEN '2018-12-01' AND '2018-12-31'
        WHERE `ms_i`.`id_iteration` IS NULL
        ";

        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $return->result = true;
            
            $obj = $result->fetch_object();
            
            $return->count = $obj->count;
        }
        $result->close();
        
        break;
        
    default:
        $query = "SELECT 
            `oi`.`order_id`,
            `oi`.`order_item_id`,
            `oi`.`product_id`,
            `oi`.`product_quantity`,
            `oi`.`order_item_name`
        FROM `jos_vm_order_item` AS `oi`
        LEFT JOIN `tbl_mass_scripts_iteration` AS `ms_i`
            ON
            `ms_i`.`id_script_inner`=`oi`.`order_id`
            AND
            `ms_i`.`id_script`=".$id_script."
        INNER JOIN `jos_vm_orders` AS `o`
            ON
                STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') BETWEEN '2018-12-01' AND '2018-12-31'
        WHERE  
            `ms_i`.`id_iteration` IS NULL
        GROUP BY `oi`.`order_item_id`
        ORDER BY `oi`.`order_id` ASC LIMIT 1000
        ";

        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $return->result = true;
            
            $iteration_inserts = array();
            $order_item_ids = array();
            $orders_ids = array();
            $products_ids = array();
            
            while ($obj = $result->fetch_object()) {
                if (!in_array($obj->order_id, $orders_ids)) {
                    $orders_ids[] = $obj->order_id;
                    $iteration_inserts[] = "(".$id_script.", ".$obj->order_id.")";
                }

                $order_items[] = $obj;
                
                if (!in_array($obj->product_id, $products_ids)) {
                    $products_ids[] = $obj->product_id;
                }
                
                $order_items_ids[] = $obj->order_item_id;
            }

            if (count($iteration_inserts) > 0) {
                $query = "INSERT INTO `tbl_mass_scripts_iteration`
                (
                    `id_script`,
                    `id_script_inner`
                )
                VALUES ".implode(',', $iteration_inserts)."
                ";

                $mysqli->query($query);
                
                unset($orders_ids, $iteration_inserts);

                $query = "SELECT 
                    `pil`.`product_id`,
                    `pio`.`igo_product_name`,
                    `pil`.`igl_quantity`,
                    `pil`.`igl_quantity_supersize`,
                    `pil`.`igl_quantity_deluxe`
                FROM `product_ingredients_lists` AS `pil`
                INNER JOIN `product_ingredient_options` AS `pio`
                    ON
                        `pio`.`igo_id`=`pil`.`igo_id`
                WHERE 
                    `pil`.`product_id` IN (".implode(',', $products_ids).")
                ";

                $products_result = $mysqli->query($query);

                $products = array();

                if ($products_result->num_rows > 0) {
                    while ($product_obj = $products_result->fetch_object()) {
                        $products[$product_obj->product_id][] = $product_obj; 
                    }
                }
                $products_result->close();

                $inserts = array();

                foreach ($order_items as $order_item) {
                    if (array_key_exists($order_item->product_id, $products)) {    
                        if (strpos($order_item->order_item_name, '(supersize)')) {
                            $prod_type = 'igl_quantity_supersize';
                        } elseif (strpos($order_item->order_item_name, '(deluxe)')) {
                            $prod_type = 'igl_quantity_deluxe';
                        } else {
                            $prod_type = 'igl_quantity';
                        }

                        foreach ($products[$order_item->product_id] as $one_product) {
                            $inserts[] = "(
                                ".$order_item->order_id.",
                                ".$order_item->order_item_id.",
                                '".$mysqli->real_escape_string($one_product->igo_product_name)."',
                                ".($one_product->$prod_type * $order_item->product_quantity)."
                            )";
                        }
                    }
                }
                
                unset($products);

                $query = "DELETE 
                FROM `jos_vm_order_item_ingredient`
                WHERE 
                    `order_item_id` IN (".implode(',', $order_items_ids).")
                ";
                $mysqli->query($query);

                unset($order_items_ids, $order_items);

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
                unset($inserts);
            }
        }
        $result->close();

        break;
}

$mysqli->close();

echo json_encode($return);

?>