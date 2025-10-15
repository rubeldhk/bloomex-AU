<?php
//disabled for now
/* 

$ip = $database->getEscaped($_SERVER['REMOTE_ADDR']);
$area = (isset($adminpanel)) ? 'admin' : 'frontend';
$get = $post = $option = $page = '';
if (isset($_REQUEST['option']))
    $option = $database->getEscaped($_REQUEST['option']);
if (isset($_REQUEST['page']))
    $page = $database->getEscaped($_REQUEST['page']);

if (isset($log_file_path)) {
    $log_file_path = $database->getEscaped($log_file_path);
}
if ($_GET) {
    $get = $database->getEscaped(json_encode($_GET));
}


if ($_POST) {
    $post = $database->getEscaped(json_encode($_POST));
}
$file = $database->getEscaped($_SERVER['PHP_SELF']);


$query_log = "INSERT INTO `tbl_access_log` 
            (`area`, `file`, `option`, `page`, `ip`, `get`, `post`, `starttime`) 
            VALUES 
            ('" . $area . "',
                '" . $file . "',
                    '" . $option . "',
             '" . $page . "',
               '" . $ip . "',
                '" . $get . "',
                 '" . $post . "',
                  NOW())";
$database->setQuery($query_log);
$database->query();
global $log_current_id;
$log_current_id=$database->insertid();
*/