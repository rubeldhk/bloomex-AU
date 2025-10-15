<?php
include_once './config.php';

$extension = (int)$_POST['ext_prj'];
$user = 'test';
$password = 'Ue7equo8';
$url = 'http://' . $user . ':' . $password . '@sip2.bloomex.ca:8080/paneltest/autorecall/status_time.php?ext=' . $extension;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$curl_data = curl_exec($ch);
$data = json_decode($curl_data);
$info = curl_getinfo($ch);

if ((int)$data->result == 0) {

    $talktime_a = explode(':', trim($data->talktime));

    $talktime_sec = 0;

    if (is_array($talktime_a)) {
        $talktime_sec = (int)$talktime_a[0]*3600+(int)$talktime_a[1]*60+(int)$talktime_a[2];
    }

    $number_id = (int)$_POST['number_id'];
    
    $query = "UPDATE `tbl_numbers_to_give`
    SET 
        `end_call_datetime`=DATE_ADD(`datetime`, INTERVAL ".$talktime_sec." SECOND) 
    WHERE `id`=".$number_id."";
    
    $mysqli->query($query);
}

$query = "INSERT INTO `tbl_call_curl`
(
    `extension`,
    `request`,
    `response`,
    `http_code`,
    `error`,
    `datetime`
)
VALUES (
    ".$extension.",
    '".$mysqli->real_escape_string($url)."',
    '".$mysqli->real_escape_string($curl_data)."',
    '".$mysqli->real_escape_string($info['http_code'])."',
    '".$mysqli->real_escape_string(curl_error($ch))."',
    '".date('Y-m-d G:i:s')."'
)";

$mysqli->query($query);

echo (int)$data->result;

curl_close($ch);
$mysqli->close();

?>


