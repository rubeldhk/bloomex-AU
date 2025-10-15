<?php


$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$query = "SELECT  
    `a`.`order_id`,
    `w`.`warehouse_name`,
    `w`.`warehouse_email`
FROM `jos_vm_api2_orders` AS `a`
JOIN `jos_vm_orders` AS `o` ON o.order_id = a.order_id
JOIN `jos_vm_warehouse` AS `w` ON w.warehouse_code = o.warehouse
WHERE `a`.`warehouse_notified`='0'";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('No result');
}

while ($obj = $result->fetch_object()) {

    $subject = "Notify Production of Order ID #" . $obj->order_id;
    $html = "You get a new Order(ID $obj->order_id). Please check it asap. Thanks!";

    $m = new MAIL5;
    $m->From($mosConfig_mailfrom);
    $m->AddTo($obj->warehouse_email);
    $m->Subject($subject);
    $m->Html($html);

    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);

    if ($m->Send($c)) {
        echo '<br/>[' . $obj->order_id . '] Notification mail sent to warehouse ' . $obj->warehouse_name;

        $query = "UPDATE `jos_vm_api2_orders` SET `warehouse_notified`='1' WHERE `order_id`=".$obj->order_id;
        $mysqli->query($query);

    } else {
        '<br /><pre>';
        print_r($m->History);
        list($tm1, $ar1) = each($m->History[0]);
        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
        echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
    }
    $m->Disconnect();
}
$result->close();
$mysqli->close();


?>