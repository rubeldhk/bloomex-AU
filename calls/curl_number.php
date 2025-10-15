<?php

function curl_number($extension, $return) {
    global $mysqli;
    
    $ext_prj = (int) $extension;
    $user = 'test';
    $password = 'Ue7equo8';
    $country = $return['country']??'AUS';
    if ($return['type'] == 'abandonment') {
        $type_num = 1;
    }
    elseif ($return['type'] == 'ocassion') {
        $type_num = 2;
    }
    else {
        $type_num = 4;
    }

    $url = 'http://' . $user . ':' . $password . '@sip2.bloomex.ca:8080/paneltest/autorecall/call.php?ext=' . $ext_prj . '&tel=' . $return['number'] . '&code=' . $country . '&call=' . $type_num;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    
    $query = "INSERT INTO `tbl_call_curl`
    (
        `call_type`,
        `extension`,
        `number`,
        `request`,
        `response`,
        `http_code`,
        `error`,
        `datetime`
    )
    VALUES (
        '".$mysqli->real_escape_string($return['type'])."',
        ".$extension.",
        '".$mysqli->real_escape_string($return['number'])."',
        '".$mysqli->real_escape_string($url)."',
        '".$mysqli->real_escape_string($data)."',
        '".$mysqli->real_escape_string($info['http_code'])."',
        '".$mysqli->real_escape_string(curl_error($ch))."',
        '".date('Y-m-d G:i:s')."'
    )";
    
    $mysqli->query($query);

    curl_close($ch);

    return (int) $data;
}

?>