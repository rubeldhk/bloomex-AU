<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT 
    `cd`.`domain`
FROM `jos_new_corporate_domains` AS `cd`";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $where = array();

    while ($obj = $result->fetch_object()) {
        $where[] = "`email` LIKE '%@".$mysqli->real_escape_string($obj->domain)."'";
    }
    
    $result->close();

    $query = "DELETE 
    FROM `jos_new_corporate_calls`
    WHERE ".implode(' OR ', $where)."
    ";
    
    echo '<pre>';
    echo $query;
    echo '</pre>';
    
    $mysqli->query($query);
}