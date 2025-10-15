<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

date_default_timezone_set('America/Toronto');

$file = file('./2all-aus.csv');

$inserts = array();

foreach ($file as $line) {
    $line_a = explode('|', $line);
    
    if (count($line_a) == 1) {
        continue;
    }
    
    $number = $line_a[4];
    
    $query = "SELECT 
    *
    FROM `tbl_calls_christmas2019` AS `c`
    WHERE
        `c`.`number`='".$mysqli->real_escape_string($number)."'
    ";
    
    $result = $mysqli->query($query);
    
    if ($result->num_rows > 0) {
        $obj = $result->fetch_object();

        $query = "UPDATE 
        `tbl_calls_christmas2019` AS `cc`
        SET 
            `cc`.`key`='',
            `cc`.`datetime_call`='0000-00-00 00:00:00',
            `cc`.`datetime_next`='0000-00-00 00:00:00',
            `cc`.`status`='0'
        WHERE 
            `cc`.`id`=".$obj->id."
        ";
        
        echo $query.'<br/>';
        
        $mysqli->query($query);
        
        $query = "INSERT INTO `tbl_calls_history`
        (
            `id_number`,
            `comment`,
            `datetime_add`
        )
        VALUES (
            ".$obj->id.",
            '".$mysqli->real_escape_string('Set status: active. Ticket #1109.')."',
            '".date('Y-m-d H:i:s')."'
        )";

        $mysqli->query($query);
        
        echo $query.'<br/>';
        
        echo 'yes<br/>'; 
    }
    else {
        echo 'no || '.$number.'<br/>'; 
    }
    $result->close();
}

$mysqli->close();