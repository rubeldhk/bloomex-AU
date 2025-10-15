<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

date_default_timezone_set('Australia/Sydney');

// Подключаем те же конфиги, что и в других кронах
require_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

if ($mysqli->connect_error) {
    die('DB connection failed: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8');

// Чистим логи старше 3-х дней
$sql = "DELETE FROM tbl_cron_queries WHERE run_time < NOW() - INTERVAL 3 DAY";
$mysqli->query($sql);

echo "[".date('Y-m-d H:i:s')."] Cleanup done, affected rows: " . $mysqli->affected_rows . PHP_EOL;

$mysqli->close();

?>
