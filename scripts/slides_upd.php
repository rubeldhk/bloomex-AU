<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$file = file('./slides.csv');

foreach ($file as $line) {
    $line_a = explode(',', $line);

    $query = "UPDATE `jos_vm_slider`
    SET
        `src`='".$mysqli->real_escape_string($line_a[2])."',
        `alt`='".$mysqli->real_escape_string($line_a[3])."'
    WHERE 
        `id`=".(int)$line_a[0]."
    ";
    
    $mysqli->query($query);
}

$mysqli->close();

