<?php
include(str_replace('/scripts', '', __DIR__).'/configuration.php');
$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$timestamp = time() + ($mosConfig_offset * 60 * 60);
$cron_username = 'Cron Bot';
$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);



$query = "SELECT o.order_id,o.priority,o.warehouse
FROM `jos_vm_orders` as o
WHERE o.`order_status` LIKE 'Z' AND
  STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') < DATE_SUB(NOW() , INTERVAL 3 DAY) OR STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') IS NULL ";

$result = $mysqli->query($query);
if (!$result) {
    printf("Errormessage: %s\n", $mysqli->error);
    printf("Query: %s\n", $query);
    die();
}


while ($row = $result->fetch_object()) {
    $q = "UPDATE jos_vm_orders SET";
    $q .= " order_status='D' ";
    $q .= ", mdate='" . $timestamp . "' ";
    $q .= "WHERE order_id='" . $row->order_id . "'";
    $result_upd = $mysqli->query($q);

    if (!$result_upd) {
        printf("Errormessage: %s\n", $mysqli->error);
        printf("Query: %s\n", $q);
        die();
    }

    $q = "INSERT INTO jos_vm_order_history ";
    $q .= "(order_id,order_status_code,warehouse,priority,date_added,customer_notified,warehouse_notified,comments,user_name) VALUES (";
    $q .= "'" . $row->order_id . "', 'D', '" . $row->warehouse . "','" . $row->priority . "', '$mysqlDatetime', 'Y', 'N',  'Cron Updated Statuses from in transit to delivered', '" . $cron_username . "')";
    $result_his = $mysqli->query($q);

    if (!$result_his) {
        printf("Errormessage: %s\n", $mysqli->error);
        printf("Query: %s\n", $q);
        die();
    }
}

$result->close();
$mysqli->close();

?>