<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$sql = "SELECT `u`.`user_email`, `p`.`id` 
FROM `tbl_platinum_club` as `p`
LEFT JOIN `jos_vm_user_info` as `u` on `u`.`user_id`=`p`.`user_id`
WHERE `p`.`start_datetime` <= DATE_SUB(NOW(),INTERVAL 1 YEAR)  AND `end_datetime` IS NULL AND `u`.`address_type`='BT'";

$result = $mysqli->query($sql);

while ($obj = $result->fetch_object()) {
    $shopper_subject = 'Your Bloomex Platinum Club Membership.';
    $shopper_html = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/html/templates/user_emails/end_of_platinum_email.html');

    $m = new MAIL5;
    $m->from($mosConfig_mailfrom);
    $m->AddTo($obj->user_email);
    $m->Subject($shopper_subject);
    $m->Html($shopper_html);
    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);// or die(print_r($m->Result));

    $m->Send($c);
    
    $sql = "UPDATE `tbl_platinum_club` SET `end_datetime`=NOW() WHERE `id`=".$obj->id."";
    $mysqli->query($sql);
}
$result->close();
$mysqli->close();
?>
