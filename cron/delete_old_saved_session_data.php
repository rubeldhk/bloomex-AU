<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';


$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
global $mysqli;
date_default_timezone_set('Australia/Sydney');
$datetime_from = date('Y-m-d G:i:s', strtotime('-60 minutes', strtotime(date('Y-m-d G:i:s'))));
$datetime_to = date('Y-m-d G:i:s', strtotime('-15 minutes', strtotime(date('Y-m-d G:i:s'))));

$query = "DELETE FROM `tbl_saved_session_data` WHERE date_modify < NOW() - INTERVAL 30 DAY";

$result = $mysqli->query($query);

if (!$result) {
    die('DELETE error: ' . $mysqli->error);
}

$mysqli->close();
