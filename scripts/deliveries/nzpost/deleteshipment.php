<?php
require $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
if(session_id() == '') {
    session_name(md5($mosConfig_live_site));
    session_start();
}

require_once('connectstonzpostapi.php');
$nzpost = new NZPost($_REQUEST['order_id']);
$response_options = $nzpost->deleteshipment($_REQUEST);

if (isset($response_options['error'])) {
   echo $response_options['error'];
} else {
    echo $response_options['message'];
}