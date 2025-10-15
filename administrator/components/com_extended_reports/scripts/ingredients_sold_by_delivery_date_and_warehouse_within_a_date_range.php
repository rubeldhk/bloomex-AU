<?php
global $database;

if (isset($_REQUEST['task'])
    && $_REQUEST['task'] == 'PrepReport'
    && isset($_REQUEST['option'])
    && $_REQUEST['option'] == 'com_extended_reports') {

    ?>
    <table class="adminlist">
        <tbody>
        <tr>
            <td>From</td>
            <td>
                <input class="text_area" type="text" name="StartDate" id="StartDate" size="10" value="">
                <input name="reset" type="reset" class="button" onclick="return showCalendar('StartDate', 'y-mm-dd');"
                       value="...">
            </td>
        </tr>
        <tr>
            <td>To</td>
            <td>
                <input class="text_area" type="text" name="EndDate" id="EndDate" size="10" value="">
                <input name="reset" type="reset" class="button" onclick="return showCalendar('EndDate', 'y-mm-dd');"
                       value="...">
            </td>
        </tr>
        <tr>
            <td>type</td>
            <td>
                <select name="type" id="type">
                    <option value="0" selected="selected">ALL</option>
                    <option value="FLOWER/PLANT">FLOWER/PLANT</option>
                    <option value="GOURMET">GOURMET</option>
                    <option value="HARDGOOD">HARDGOOD</option>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
    <?php

} else {
    $StartDate = mosGetParam($_REQUEST, 'StartDate', false);
    $EndDate = mosGetParam($_REQUEST, 'EndDate', false);
    $type = mosGetParam($_REQUEST, 'type', false);

    $query = " SELECT
        `wh`.`warehouse_name`,
        o.order_id,
        `oi`.`ingredient_name` AS 'ingredient_name',
        `io`.`type`,
        SUM(`oi`.`ingredient_quantity`) AS 'ingredient_quantity'
    FROM `jos_vm_orders` AS `o`
   
    INNER JOIN `jos_vm_order_item_ingredient` AS `oi`
        ON
            `oi`.`order_id`=`o`.`order_id`
    INNER JOIN `jos_vm_warehouse` AS `wh`
        ON
            `wh`.`warehouse_code`=`o`.`warehouse` 
    LEFT JOIN `product_ingredient_options` as `io` on `io`.`igo_product_name`= `oi`.`ingredient_name`
    WHERE
        
          (
    (
      date_format(
        str_to_date(`o`.`ddate`, '%d-%m-%Y'), 
        '%Y-%m-%d 00:00:01'
      ) BETWEEN '" . $StartDate . "' 
      AND '" . $EndDate . " 23:59:59'
    ) 
    OR (
      `o`.`ddate` BETWEEN '" . $StartDate . "' 
      AND '" . $EndDate . " 23:59:59'
    )
  ) 
        
        
    AND
    `o`.`order_status` NOT IN ('X', '6', 'P')";
    if ($type) {
        $query .= " AND `io`.`type` = '" . $type . "' ";
    }
    $query .= " GROUP BY `o`.`warehouse`,oi.ingredient_name";

    $database->setQuery($query);
    $products = $database->loadObjectList();



    $rows = array();

    $warehouses = array();

    foreach ($products as $product) {
        if (!in_array($product->warehouse_name, $warehouses)) {
            $warehouses[] = $product->warehouse_name;
        }
    }

    $i = 0;
    $ingredients = array();

    foreach ($products as $product) {
        if (!in_array($product->ingredient_name, $ingredients)) {
            $ingredients[] = $product->ingredient_name;

            $rows[$i] = array(
                'ingredient_name' => $product->ingredient_name,
                'type' => $product->type,
                'total' => 0,
                'orders_count' => 0,
                'orders' => []
            );

            foreach ($warehouses as $warehouse) {
                $rows[$i][$warehouse] = 0;
                foreach ($products as $product2) {
                    if ($warehouse == $product2->warehouse_name
                        and
                        $product2->ingredient_name == $product->ingredient_name
                    ) {
                        $rows[$i]['total'] += $product2->ingredient_quantity;
                        $rows[$i][$warehouse] = $product2->ingredient_quantity;
                        in_array($product2->order_id, $rows[$i]['orders']) ?: array_push($rows[$i]['orders'],$product2->order_id); $rows[$i]['orders_count'] ++;
                    }
                }
            }

            unset($rows[$i]['orders']);
            $i++;
        }
    }
    unset($ingredients, $warehouses, $products);

}
?>
