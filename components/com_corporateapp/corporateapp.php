<?php

$url = $database->getEscaped($_GET['url']);
$row = false;

$query = "SELECT * FROM `jos_corporateapp` WHERE `url`='".$url."'";

$database->setQuery($query);
$database->loadObject($row);

if ($row) {
    mosLoadModules('user9', -1);
}
else {
    $sef->set404();
}

?>

