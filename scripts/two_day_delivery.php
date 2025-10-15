<?php

//define holiday
$holiday = 'valentine';

include_once '../configuration.php';
$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');


if (!isset($_REQUEST['on'])) {
    Echo "Please add ?on=1 or ?on=0 to url to turn two day delivery on/off";
} else {
    $sql_data = ($_REQUEST['on'] == 0) ? 0 : 1;
    $query = "UPDATE tbl_options SET published = $sql_data where type = 'two_day_delivery' and name = '$holiday'";
    $result_insert = $mysqli->query($query);
    if ($result_insert) {
        echo "updated sucessfully";
    } else {
        echo $mysqli->error;
    }
}

$query = "SELECT published from tbl_options where type = 'two_day_delivery' and name = '$holiday'";

$result_select = $mysqli->query($query);
$published_obj = $result_select->fetch_object();
$published = $published_obj->published;

echo "<hr>Two day delivery for $holiday now is " . (($published) ? 'ON' : 'OFF');
$mysqli->close();
