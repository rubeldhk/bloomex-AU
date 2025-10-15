<?php
global $database, $my;

if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'PrepReport' && isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_extended_reports') {
    $where_a = array();
    if ($my->gid != 25) {
        if (isset($my->routes_warehouses)) {
            $where_a[] = " `warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ")";
        }
    }
    $where = '';
    if (count($where_a) > 0) {
        $where .= " WHERE ";
        $where .= implode(" AND ", $where_a);
    }

    $query = "SELECT warehouse_id,warehouse_name from jos_vm_warehouse " . $where;

    $database->setQuery($query);
    $drivers = $database->loadObjectList();
    f($my, $query, $drivers);
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
                    <option value="0">All</option>
                    <?php
                    foreach ($drivers as $driver_obj) {
                        ?>
                        <option value="<?php echo $driver_obj->warehouse_id; ?>">
                            <?php echo $driver_obj->warehouse_name; ?>
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

    $query = sprintf("SELECT 
            DISTINCT `d`.`id` 
        FROM 
            `tbl_driver_option` AS `d` 
        INNER JOIN 
                `jos_vm_routes` AS `r` 
        ON 
            `r`.`driver_id` = `d`.`id` 
        AND `r`.`datetime` BETWEEN '%s 00:00:00' AND '%s 23:59:59'",
        $StartDate,
        $EndDate
    );

    if ($warehouse_id) {
        $query .= sprintf("WHERE `d`.`warehouse_id` = '%s'", $warehouse_id);
    }

    $query .="GROUP BY `d`.id";

    $database->setQuery($query);
    $drivers = $database->loadAssocList();
    f($drivers, $query);
    $rows = [];
    $data = [];
    foreach ($drivers as $v) {
        $query = sprintf("SELECT 
                `jvw`.`warehouse_name` AS 'warehouseName', 
                COUNT(`ro`.`order_id`) AS 'ordersTotal'
            FROM `tbl_driver_option` AS `d` 
            INNER JOIN `jos_vm_routes` AS `r` 
                ON `r`.`driver_id` = `d`.`id` 
                AND `r`.`datetime` 
                BETWEEN '%s 00:00:00' AND '%s 23:59:59' 
            LEFT JOIN `jos_vm_warehouse` AS `jvw`
    	        ON `jvw`.`warehouse_id` = `d`.`warehouse_id`
            INNER JOIN `jos_vm_routes_orders` AS `ro` 
                ON `ro`.`route_id` = `r`.`id` 
            LEFT JOIN `jos_driver_rates` AS `dr` 
                ON `dr`.`id_rate` = `ro`.`id_rate` 
            LEFT JOIN `jos_driver_rate_xref` AS `rx` 
                ON `rx`.`id_rate` = `ro`.`id_rate` AND `rx`.`id_driver`=`d`.`id`
            WHERE 
                `r`.`publish`='1' AND
                `ro`.`billable` = '1' AND
                `d`.`id` = %s
            ORDER BY 
                `jvw`.`warehouse_name`",
            $StartDate,
            $EndDate,
            $v['id']
        );
        $database->setQuery($query);
        $results = $database->loadAssocList();
        $warehouseNameOrdersTotal = reset($results);
        if(isset($data[$warehouseNameOrdersTotal['warehouseName']]) && $data[$warehouseNameOrdersTotal['warehouseName']]) {
            $data[$warehouseNameOrdersTotal['warehouseName']]['ordersTotal'] += $warehouseNameOrdersTotal['ordersTotal'];
        } else {
            $data[$warehouseNameOrdersTotal['warehouseName']] = $warehouseNameOrdersTotal;
        }
    }
    $rows = array_values($data);
    $rows[] = [
        'warehouseName' => 'ALL',
        'ordersTotal' => array_sum(array_column($data, 'ordersTotal'))
    ];
}
