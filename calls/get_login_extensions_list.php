<?php

include_once './config.php';

$time = time() - 11;

$datetime_to = date('Y-m-d G:i:s', strtotime('-11 seconds', strtotime(date('Y-m-d G:i:s'))));
$query = "SELECT
    `extension`  
FROM `track_login_extensions`   
WHERE 
    `date_end_dt`>='".$datetime_to."'
";

$result = $mysqli->query($query);

$arr = array();

if ($result->num_rows > 0) {
    while ($obj = $result->fetch_object()) {
        $arr[] = $obj->extension;
    }
}

#$result->close();
$mysqli->close();

echo json_encode($arr);

?>
