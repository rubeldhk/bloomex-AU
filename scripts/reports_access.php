<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$inserts = array();

$query = "SELECT 
    `id`,
    `title`
FROM `jos_jeporter`
ORDER BY `id` ASC";

$result = $mysqli->query($query);
while ($obj = $result->fetch_object()) {
    $inserts[] = "(".$obj->id.", '1')";
}

$query = "INSERT INTO `jos_jeporter_level_xref` 
(
    `id_jeporter`,
    `level`
)
VALUES ".implode(',', $inserts)."";
$mysqli->query($query);

?>

