<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('_VALID_MOS', 'true');
include "configuration.php";

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

if (isset($_REQUEST['cache']) AND !empty($_REQUEST['cache'])) {
    $data = parse_str(base64_decode(trim($_REQUEST["cache"])), $output);
    $user_id = $output['user_id'];
    $order_id = $output['order_id'];
    $type = $output['type'];

    $q = "SELECT id from tbl_cron_survey_send 
 where order_id='" . $mysqli->escape_string($order_id) . "' and type='" . $mysqli->escape_string($type) . "'  and email_open_datetime is null ";
    $res = $mysqli->query($q);

    if (!$res->num_rows==0) {
        $q_u="UPDATE tbl_cron_survey_send SET  email_open_datetime = NOW() WHERE order_id=".$mysqli->escape_string($order_id);
        $mysqli->query($q_u);
    }

}