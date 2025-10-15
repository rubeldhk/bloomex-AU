<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

date_default_timezone_set('Australia/Sydney');
$datetime = date('Y-m-d G:i:s');

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$query = "SELECT
    `o`.`order_status`,
    `o`.`warehouse`,
    `ui`.`first_name`,
    `ui`.`order_id`,
    `ui`.`user_id`, 
    `ui`.`last_name`, 
    `ui`.`user_email` 
FROM `jos_vm_orders` AS `o`
LEFT JOIN `jos_vm_order_user_info` AS `ui` 
    ON `ui`.`order_id`=`o`.`order_id` 
        AND
        `ui`.`address_type`='BT'
          LEFT JOIN `tbl_cron_survey_send` AS `c` ON `c`.`order_id`=`o`.`order_id` AND `c`.type='order'
          LEFT JOIN `jos_vm_sub_orders_xref` AS `s` ON `s`.`sub_order_id`=`o`.`order_id`
          LEFT JOIN `jos_vm_api2_orders` AS `d` ON `d`.`order_id`=`o`.`order_id`
WHERE 
FROM_UNIXTIME( o.cdate , '%Y-%m-%d %H:%i:%s' ) BETWEEN NOW() - INTERVAL 1 DAY AND NOW()
  AND `c`.id IS NULL AND `d`.id IS NULL
  AND `o`.`order_status` NOT IN ('PD', 'X', 'R', 'L', 'O', '1', '2', 'P', '26')
  AND `s`.`sub_order_id` IS NULL";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('No result');
}

while ($obj = $result->fetch_object()) {
    $query = "INSERT INTO tbl_cron_survey_send (order_id,email,date,type) VALUES ( '{$obj->order_id}','{$obj->user_email}',NOW(),'order')";
    $result2 = $mysqli->query($query);
    
    $query = "INSERT INTO `jos_vm_order_history` (`order_id`,`order_status_code`,`comments`,`date_added`,`user_name`,`warehouse`) VALUES ('{$obj->order_id}','{$obj->order_status}','sent survey after order','" . $datetime . "','Cron','{$obj->warehouse_code}')";
    $mysqli->query($query);
    
    $cache = base64_encode('user_id=' . $obj->user_id . '&order_id=' . $obj->order_id. '&type=order');
    $nothing = 0;
    $subject = 'Your opinion matters most to us!';
    $html = "<div>
	<span style=\"font-size:14px;\">Dear " . $obj->first_name . " " . $obj->last_name . ",</span></div><br>
<div>
	<span style=\"font-size:14px;\">We hope you enjoyed your experience with Bloomex!&nbsp;</span></div><br>
<div>
        <span style=\"font-size:14px;\">In order to continue providing excellent service to our customers, we would appreciate if you would take a few minutes to complete this very brief <a href=\"" . $mosConfig_live_site . "/survey-after-order/?data={$cache}&utm_source=survey-after-order&utm_medium=email-survey&utm_campaign=order-id\"  target=\"_self\">survey </a>.</span></div><br>
<div>
	<span style=\"font-size:14px;\">Best Regards,</span></div><br><br>
	<img style='width: 200px' src=\"https://bloomex.com.au/templates/bloomex7/images/bloomexlogo.png\" >
	<img style='display:none' src=\"https://bloomex.com.au/track_survey_open.php?cache='.$cache.'\" >
	";

    $m = new MAIL5;
    $m->From($mosConfig_mailfrom_noreply);
    $m->AddTo($obj->user_email);
    $m->Subject($subject);
    $m->Html($html);

    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);

    if ($m->Send($c)) {
        echo '<br/>[' . $obj->order_id . '] Mail sent to ' . $obj->user_email;
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

if ($nothing) {
    echo "no surveys to send query:" . $query;
}