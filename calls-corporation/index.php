<?php
if (substr($_SERVER['HTTP_HOST'], 0, 3) != "adm") {
    $actual_link = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('Location: ' . $actual_link);
}

if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    setcookie('extension', '');
    header('Location: ?');
    exit;
}
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Australia/Sydney');
include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/MAIL5/MAIL5.php';

define('MY_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/calls-corporation');

include_once MY_ROOT . '/system/configuration.php';

define('MY_PATH', $mosConfig_live_site . '/calls-corporation');

include_once MY_ROOT . '/modules/default/c.php';
$default_class = new default_controller;

include_once MY_ROOT . '/modules/calls/c.php';
$calls_class = new calls_controller;

unset($default_class, $calls_class);
$mysqli->close();
