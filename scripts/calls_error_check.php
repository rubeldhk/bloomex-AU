<?php

if (is_file($_SERVER['DOCUMENT_ROOT'].'/scripts/configuration.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/configuration.php';
}
else {
    include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';
}

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

date_default_timezone_set('Australia/Sydney');

$query = "SELECT 
    `c`.`id`,
    `c`.`number`
FROM `tbl_calls_christmas2019` AS `c`
WHERE 
  `c`.`status`='5'
ORDER BY 
  `c`.`id`
";

$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    $only_d = preg_replace("/\D/", '', $obj->number);
    
    if (mb_strlen($only_d) == 9) {
        //$phone = '4'.$only_d;

        $query = "UPDATE `tbl_calls_christmas2019`
        SET
            `status`='0'
        WHERE 
            `id`=".$obj->id."
        ";
        
        echo $query.'<br/>';
        
        $mysqli->query($query);
        
        /*
        $comment = 'Add 4 to number.';
        
        $query = "INSERT INTO `tbl_calls_history`
        (
            `id_number`,
            `comment`,
            `datetime_add`
        )
        VALUES (
            ".$obj->id.",
            '".$mysqli->real_escape_string($comment)."',
            '".date('Y-m-d H:i:s')."'
        )";

        echo $query.'<br/>';
        
        $mysqli->query($query);*/
    }
}
$result->close();
$mysqli->close();

?>