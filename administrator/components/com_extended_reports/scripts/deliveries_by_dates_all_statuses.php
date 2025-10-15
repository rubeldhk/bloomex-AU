<?php
global $database, $my;

if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'PrepReport' && isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_extended_reports') {
    $where_a = array();
    if ($my->gid != 25) {
        if (isset($my->routes_warehouses)) {
            $where_a[] = "`wh`.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ")";
        }
    }
    $where = '';
    if (count($where_a) > 0) {
        $where .= " WHERE ";
        $where .= implode(" AND ", $where_a);
    }

    $query = "SELECT 
        `wh`.`warehouse_id`, 
        `wh`.`warehouse_name` 
    FROM `jos_vm_warehouse` AS `wh` 
    " . $where . "
    ORDER BY    
        `wh`.`warehouse_name`
    ";

    $database->setQuery($query);
    $warehouses = $database->loadObjectList();
    ?>
    <table class="adminlist">
        <tbody>
            <tr>
                <td>From</td>
                <td>
                    <input class="text_area" type="text" name="StartDate" id="StartDate" size="10" value="">
                    <input name="reset" type="reset" class="button" onclick="return showCalendar('StartDate', 'y-mm-dd');" value="...">
                </td>
            </tr>
            <tr>
                <td>To</td>
                <td>
                    <input class="text_area" type="text" name="EndDate" id="EndDate" size="10" value="">
                    <input name="reset" type="reset" class="button" onclick="return showCalendar('EndDate', 'y-mm-dd');" value="...">
                </td>
            </tr>
            <tr>
                <td>Warehouse</td>
                <td>
                    <select name="warehouse_id">
                        <?php
                        foreach ($warehouses as $wh_obj) {
                            ?>
                            <option value="<?php echo $wh_obj->warehouse_id; ?>">
                                <?php echo $wh_obj->warehouse_name; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Delivery Type</td>
                <td>
                    <select name="delivery_type">
                        <option value="0">Regular</option>
                        <option value="1">Morning</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
} else {
    $StartDate = ((isset($_POST['StartDate'])) ? $database->getEscaped($_POST['StartDate']) : '');
    $EndDate = ((isset($_POST['EndDate'])) ? $database->getEscaped($_POST['EndDate']) : '');
    $warehouse_id = ((isset($_POST['warehouse_id'])) ? (int) $_POST['warehouse_id'] : 0);
    $delivery_type = ((isset($_POST['delivery_type'])) ? (int) $_POST['delivery_type'] : 0);

    $rates = array();
    $query = "SELECT 
        `o`.`order_id`,
        `s`.`order_status_name`,
        `o`.`ship_method_id`,
        `ui`.`zip`,
        STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') AS 'ddate'
    FROM `jos_vm_orders` AS `o`
    INNER JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_code`=`o`.`warehouse` 
    INNER JOIN `jos_vm_order_status` s  ON `s`.`order_status_code`=`o`.`order_status` 
    INNER JOIN `jos_vm_order_user_info` AS `ui`
        ON
        `ui`.`order_id`=`o`.`order_id`
        AND
        `ui`.`address_type`='ST'
    WHERE
        STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') BETWEEN 
        '" . $StartDate . " 00:00:00' 
        AND 
        '" . $EndDate . " 23:59:59'
        AND `w`.`warehouse_id` = " . $warehouse_id . " ";
    if($delivery_type){
        $query .= " AND POSITION('Morning' IN `o`.`ship_method_id`) > 0";
    }else{
        $query .= " AND POSITION('Morning' IN `o`.`ship_method_id`) = 0";
    }
    $query .= " ORDER BY 
        STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y')
    ";
    $database->setQuery($query);
    $orders = $database->loadObjectList();

    $rows = array();
    $rates = array();
    $p=0;
    foreach ($orders as $order_obj) {
        $zip_symbols = 6;
        $zip = strtoupper(str_replace(array(' ', '-'), '', trim($order_obj->zip)));

        while ($zip_symbols > 2) {
            $query = "SELECT 
                `r`.`id_rate`,
                `r`.`rate`,
                `r`.`orderby`,
                IFNULL(`r`.`name`, 'Default') AS 'name'
            FROM `jos_driver_rates_postalcodes` AS `rp`
            INNER JOIN `jos_driver_rates` AS `r`
                ON
                `r`.`id_rate`=`rp`.`id_rate`
            WHERE
                `rp`.`postalcode`='" . $database->getEscaped(mb_substr($zip, 0, $zip_symbols)) . "'
            ";
            $rate_obj = false;
            $database->setQuery($query);
            $database->loadObject($rate_obj);

            if ($rate_obj) {

                    $rates[$p]['ddate'] = $order_obj->ddate;
                    $rates[$p]['name'] = $rate_obj->name;
                    $rates[$p]['orderby'] = $rate_obj->orderby;
                    $rates[$p]['order_id'] = $order_obj->order_id;
                    $rates[$p]['order_status_name'] = $order_obj->order_status_name;
                $zip_symbols = 0;
            }

            $zip_symbols--;
        }
        set_time_limit(60);
        $p++;
    }

    $i = 0;
    $rates = sortArr($rates);
        foreach ($rates as $k => $v) {
            $rows[$i] = array (
                'Rate' => $v['name'],
                'Order Id' => ($v['order_id'])??'',
                'Order status' => ($v['order_status_name'])??'',
                'Date' => $v['ddate'],
            );
            $i++;
        }

}

function sortArr($arr){
    //collect array by delivery date
    $res = [];
    foreach($arr as $a){
            $res[$a['ddate']][]=$a;
    }

    //sort arrays by orderby value
    foreach($res as $k=>$r){
         usort($r, 'sortByOrder');
        $res[$k] = $r;
    }

    //concat arrays into main array
    $return=[];
    foreach($res as $k=>$r){
        foreach($r as $m){
            $return[]=$m;
        }
    }

    return $return;
}
function sortByOrder($a, $b) {
    return $a['orderby'] - $b['orderby'];
}

?>