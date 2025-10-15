<?php
$SurveyTimehousrs1= 16;
$SurveyTimehousrs2= 15;

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$query = "SELECT 
    `ui`.`first_name`,
    `ui`.`order_id`,
    `ui`.`user_id`, 
    `ui`.`last_name`, 
    `ui`.`user_email` 
FROM `jos_vm_order_history` AS `h`
LEFT JOIN `jos_vm_order_user_info` AS `ui` 
    ON `ui`.`order_id`=`h`.`order_id` 
        AND
        `ui`.`address_type`='BT'
WHERE 
    `h`.`date_added` BETWEEN NOW() - INTERVAL ".$SurveyTimehousrs1." HOUR AND NOW() - INTERVAL ".$SurveyTimehousrs2." HOUR
    AND 
    `h`.`order_status_code`='D'  
GROUP BY `h`.`order_id`";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('No result');
}

while ($obj = $result->fetch_object()) {
    $nothing = 0;
    $subject = 'Bloomex would like to get your feedback';
    $html = "<div>
	<span style=\"font-size:14px;\">Dear {user_name},</span></div><br>
<div>
	<span style=\"font-size:14px;\">We hope you enjoyed your experience with Bloomex!&nbsp;</span></div><br>
<div>
	<span style=\"font-size:14px;\">In order to continue providing excellent service to our customers, we would appreciate if you would take a few minutes to complete this very brief <a href=\"https://bloomex.com.au/index.php?option=com_survey&user_id={user_id}&order_id={order_id}\"  target=\"_self\">survey </a>.</span></div><br>
<div>
	<span style=\"font-size:14px;\">Best Regards,</span></div><br><br>
	<img style='width: 200px' src=\"https://bloomex.com.au/templates/bloomex7/images/bloomexlogo.png\" >";


    $html = str_replace('{user_name}', $obj->first_name . " " . $obj->last_name, $html);
    $html = str_replace('{user_id}', $obj->user_id, $html);

    $html = str_replace('{order_id}', $obj->order_id, $html);

    $m = new MAIL5;
    $m->From($mosConfig_mailfrom);
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

?>