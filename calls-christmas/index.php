<?php

session_start();
error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=utf-8');
set_time_limit(60);
date_default_timezone_set('America/Toronto');

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?');
    exit;
}

$folder = 'calls-christmas';

define('MY_ROOT', $_SERVER['DOCUMENT_ROOT']. '/' . $folder);

$project = isset($_SESSION['project']) ? (int) $_SESSION['project'] : 1;

include_once MY_ROOT.'/configuration.php';
Kint::$enabled_mode = false;

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

define('MY_PATH', $mosConfig_live_site.'/'.$folder);

include_once MY_ROOT.'/modules/default/controller.php';
$default_class = new default_controller;

include_once MY_ROOT.'/modules/calls/controller.php';
$calls_class = new calls_controller;

unset($default_class, $calls_class);
$mysqli->close();