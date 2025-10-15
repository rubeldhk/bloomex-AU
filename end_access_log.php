<?php

/*
global $my, $database, $log_current_id;
$access_username = $my->username . "(" . $my->id . ")";
$session = $database->getEscaped(json_encode($_SESSION));

if ($log_current_id) {
    $sql_log = "UPDATE tbl_access_log SET endtime=NOW(),phpexectime=(NOW()-starttime),username='" . $access_username . "',session='" . $session . "' WHERE id='" . $log_current_id . "'";
    $database->setQuery($sql_log);
    $database->query();
}*/
