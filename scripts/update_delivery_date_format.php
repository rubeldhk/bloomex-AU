<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "UPDATE `jos_vm_orders` 
SET 
`ddate`=IF(Date_Format(`ddate`, '%d-%m-%Y') IS NULL, `ddate`, Date_Format(`ddate`, '%d-%m-%Y'))
WHERE `ddate` IS NOT NULL";

if (!$mysqli->query($query)) {
    die('error: '.$mysqli->error);
}

$mysqli->close();

?>


