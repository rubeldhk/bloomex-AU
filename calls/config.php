<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

date_default_timezone_set('Australia/Sydney');

$min_hour = 9;
$max_hour = 20;
$session_expired = 10; //minutes