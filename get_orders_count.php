<?php
$md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';
if ($_POST['key'] == md5($md5_salt.'ORDERS_COUNT'.$md5_salt)) {
    include_once 'configuration.php';
    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $sql = "SELECT COUNT(*) as orders_count FROM jos_vm_orders;";
    $res = $mysqli->query($sql);
    $order_count = $res->fetch_assoc();
    echo json_encode($order_count);
$mysqli->close();
}
