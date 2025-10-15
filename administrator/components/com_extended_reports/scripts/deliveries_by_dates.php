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
        </tbody>
    </table>
    <?php
} else {
    $StartDate = ((isset($_POST['StartDate'])) ? $database->getEscaped($_POST['StartDate']) : '');
    $EndDate = ((isset($_POST['EndDate'])) ? $database->getEscaped($_POST['EndDate']) : '');
    $warehouse_id = ((isset($_POST['warehouse_id'])) ? (int) $_POST['warehouse_id'] : 0);




    $rates = array();


    $query = "SELECT 
        `o`.`order_id`,
        `o`.`order_status`,
        `o`.`ship_method_id`,
        `ui`.`zip`,
        `o`.`ship_method_id`,
        STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') AS 'ddate'
    FROM `jos_vm_orders` AS `o`
    INNER JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_code`=`o`.`warehouse` 
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
        AND
        `o`.`order_status` IN ('A', 'C')
        AND `w`.`warehouse_id` = " . $warehouse_id . "
    ORDER BY 
        STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y')
    ";
    $database->setQuery($query);
    $orders = $database->loadObjectList();

    $rows = array();
    $rates = array();
    $p=0;
    foreach ($orders as $order_obj) {
        if (strrpos($order_obj->ship_method_id, 'Morning')) {
            $order_obj->ship_method = 'Morning';
        } elseif (strrpos($order_obj->ship_method_id, 'Evening')) {
            $order_obj->ship_method = 'Evening';
        } else {
            $order_obj->ship_method = 'Regular';
        }
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

            $shipping_method_names = array(
                24 => "Regular: 9:00am - 6:00pm",
                25 => "Morning: before 12:00pm",
                26 => "Evening: 6:00pm - 9:00pm"
            );
            $details = explode("|", $order_obj->ship_method_id);
            $shipping_id = (isset($details[4]))?(int) $details[4]:'';
            if (!array_key_exists($shipping_id, $shipping_method_names)) {
                $shipping_id = (isset($details[5]))?(int) $details[5]:'';
            }
            if ($rate_obj) {


                    $rates[$p]['ddate'] = $order_obj->ddate;
                    $rates[$p]['name'] = $rate_obj->name;
                    $rates[$p]['orderby'] = $rate_obj->orderby;
                        if ($order_obj->order_status == 'A') {
                            if ($shipping_id == 25) {
                                $rates[$p]['paid_morning_order'] = $order_obj->order_id;
                            } else {
                                $rates[$p]['paid_regular_order'] = $order_obj->order_id;
                            }
                        }
                        else
                        {
                            if ($shipping_id == 25) {
                                    $rates[$p]['confirmed_morning_order'] = $order_obj->order_id;
                            } else {
                                    $rates[$p]['confirmed_regular_order'] = $order_obj->order_id;
                            }

                        }



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
                'Confirmed Morning Order Id' => ($v['confirmed_morning_order'])??'',
                'Confirmed Regular Order Id' => ($v['confirmed_regular_order'])??'',
                'Paid Morning Order Id' => ($v['paid_morning_order'])??'',
                'Paid Regular Order Id' => ($v['paid_regular_order'])??'',
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