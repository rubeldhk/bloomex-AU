<?php
include_once './config.php';

$extension = (int) $_POST['ext_prj'];
$check_first = $_POST['check_first'];
$date_start = $_POST['date_start'];

if ($check_first == 'true') {
    $date_start = $date_end = date('Y-m-d G:i:s');

    $query = "INSERT INTO `track_login_extensions`
    (
        `extension`,
        `date_start_dt`,
        `date_end_dt`
    )  
    VALUES (
        '".$extension."',
        '".$date_start."',
        '".$date_end."'
    )";
    $mysqli->query($query);
} 
else {
    $date_end = date('Y-m-d G:i:s');
    $query = "UPDATE `track_login_extensions`
    SET 
        `date_end_dt`='".$date_end."'  
    WHERE 
        `extension`=".$extension." 
        AND 
        `date_start_dt`='".$date_start."'
    ";
    $mysqli->query($query);
}
$mysqli->close();

echo $date_start;

?>