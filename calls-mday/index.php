<?php

session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?');
    exit;
}

header('Content-Type: text/html; charset=utf-8');
set_time_limit(60);
date_default_timezone_set('America/Toronto');
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__) . '/';
$folder = 'calls-mday';

define('MY_ROOT', $_SERVER['DOCUMENT_ROOT'].'/'.$folder);

$project = isset($_COOKIE['project']) ? (int) $_COOKIE['project'] : 1;

include_once MY_ROOT.'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

define('MY_PATH', $mosConfig_live_site.'/'.$folder);

include_once MY_ROOT.'/modules/default/controller.php';
$default_class = new default_controller;

include_once MY_ROOT.'/modules/calls/controller.php';
$calls_class = new calls_controller;

unset($default_class, $calls_class);
$mysqli->close();
