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

    $query = "SELECT 
        `dr`.`id_rate`,
        IFNULL(`dr`.`name`, 'Default') AS 'name', 
        orderby
    FROM `jos_driver_rates` AS `dr` 
    WHERE 
        `dr`.`warehouse_id` = " . $warehouse_id . "
     ORDER BY 
                `dr`.`orderby` asc";
    $database->setQuery($query);
    $rates_obj = $database->loadObjectList();

    $rows = array();
    $rates = array();

    foreach ($rates_obj as $rate_obj) {
        $rates[$rate_obj->id_rate] = array(
            'name' => $rate_obj->name,
            'orderby' => $rate_obj->orderby,
            'dates' => array()
        );
    }

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

        while ($zip_symbols > 0) {
            $query = "SELECT 
                `r`.`id_rate`,
                `r`.`rate`,
                `r`.`orderby`,
                `r`.`name`
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

            $defArr = [
                'count_a_r' => 0,
                'count_a_m' => 0,
                'count_c_r' => 0,
                'count_c_m' => 0,
            ];
            $shipping_method_names = array(
                24 => "Regular: 9:00am - 6:00pm",
                25 => "Morning: before 12:00pm",
                26 => "Evening: 6:00pm - 9:00pm"
            );
            $details = explode("|", $order_obj->ship_method_id);
            $shipping_id = (isset($details[4])) ? (int) $details[4] : '';
            if (!array_key_exists($shipping_id, $shipping_method_names)) {
                $shipping_id = (isset($details[5])) ? (int) $details[5] : '';
            }
            if ($rate_obj) {

                if (array_key_exists($rate_obj->id_rate, $rates)) {
                    if (array_key_exists($order_obj->ddate, $rates[$rate_obj->id_rate]['dates'])) {
                        if ($order_obj->order_status == 'A') {
                            if ($shipping_id == 25) {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_a_m']++;
                            } else {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_a_r']++;
                            }
                        } else {
                            if ($shipping_id == 25) {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_c_m']++;
                            } else {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_c_r']++;
                            }
                        }
                    } else {
                        $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate] = $defArr;
                        if ($order_obj->order_status == 'A') {
                            if ($shipping_id == 25) {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_a_m'] = 1;
                            } else {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_a_r'] = 1;
                            }
                        } else {
                            if ($shipping_id == 25) {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_c_m'] = 1;
                            } else {
                                $rates[$rate_obj->id_rate]['dates'][$order_obj->ddate]['count_c_r'] = 1;
                            }
                        }
                    }
                }

                $zip_symbols = 0;
            }

            $zip_symbols--;
        }
    }

    $i = 0;
    $total_paid_regular = 0;
    $total_paid_morning = 0;
    $total_confirmed_regular = 0;
    $total_confirmed_morning = 0;
    $total_orders_to_delivery = 0;
    foreach ($rates as $k => $v) {
        if (empty($v['dates'])) {
            unset($rates[$k]);
        }
    }
    //$rates = sortArr($rates);
    foreach ($rates as $rate) {
        foreach ($rate['dates'] as $k => $v) {
            $total_paid_regular += $v['count_a_r'];
            $total_paid_morning += $v['count_a_m'];
            $total_confirmed_regular += $v['count_c_r'];
            $total_confirmed_morning += $v['count_c_m'];
            $total_orders_to_delivery += array_sum($v);
            $rows[$i] = array(
                'Date' => $k,
                'Rate' => $rate['name'],
                'Total orders to delivery' => array_sum($v),
                'Confirmed orders Morning' => $v['count_c_m'],
                'Paid orders Regular' => $v['count_a_r'],
                'Paid orders Morning' => $v['count_a_m'],
                'Confirmed orders Regular' => $v['count_c_r'],
            );
            $i++;
        }
    }
    $rows[] = array(
        'Rate' => 'TOTAL',
        'Date' => 'ALL',
        'Total orders to delivery' => $total_orders_to_delivery,
        'Confirmed orders Morning' => $total_confirmed_morning,
        'Paid orders Regular' => $total_paid_regular,
        'Paid orders Morning' => $total_paid_morning,
        'Confirmed orders Regular' => $total_confirmed_regular,
    );
}

function sortArr($arr) {
    //collect array by delivery date
    $res = [];
    foreach ($arr as $a) {
        foreach ($a['dates'] as $k => $v) {
            $res[$k][] = $v;
        }
    }

    //sort arrays by orderby value
    foreach ($res as $k => $r) {
        usort($r, 'sortByOrder');
        $res[$k] = $r;
    }

    //concat arrays into main array
    $return = [];
    foreach ($res as $k => $r) {
        foreach ($r as $m) {
            $return[] = $m;
        }
    }

    return $return;
}

function sortByOrder($a, $b) {
    return $a['orderby'] - $b['orderby'];
}
?>