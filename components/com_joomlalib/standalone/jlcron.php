<?php
require('stubjambo.php');
require_once('../jlcoreapi.inc');
JLCoreApi::loadConfig();
JLCoreApi::import('jlcron');
JLCoreApi::import('jllog');

$jllog = new JLLog('jl');

$jlcron = new JLCron($jllog);
$jlcron->exec();
?>