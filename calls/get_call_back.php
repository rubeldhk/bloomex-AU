<?php

include_once './config.php';
$type = 'call_back';
include_once 'curl_number.php';
$date = date('Y-m-d');

$return = array();
$return['type'] = $type;
$return['id_info'] = 0;

$datetime_from = date('Y-m-d G:i:s', strtotime('-1 day', strtotime(date('Y-m-d G:i:s'))));
$datetime_to = date('Y-m-d G:i:s', strtotime('-2 minutes', strtotime(date('Y-m-d G:i:s'))));
$query = "SELECT *
FROM `calls_callback_tmp`
WHERE `date` BETWEEN '".$datetime_from."' AND '".$datetime_to."'";

$result = $mysqli->query($query);

if (!$result) {
    echo $query;
    die('Select error: '.$mysqli->error);
}

if ($result->num_rows > 0) {
    $obj = $result->fetch_object();
    
    $number = $obj->tel;

    $only_d = preg_replace("/\D/", '', $number);
    if ($only_d[0] == "0") {
        $only_d = substr($only_d, 1);
    }
    if (mb_strlen($only_d) == 9) {
        $return['number'] = $only_d;
        $data = curl_number($_POST['ext_prj'], $return);

        if ($data != 1) {
            $return['error'] = 'no permission to call';
            $return['id_info'] = 0;
        } 
        else {
            $return['count'] = $count - 1;
            $return['id_info'] = $obj->id;
            
            $query = "INSERT INTO `tbl_numbers_to_give`
            (
                `id_info`, 
                `number`,
                `type`,
                `ext`,
                `datetime`
            )
            VALUES
            (
                ".$return['id_info'] . ",
                '".$only_d."',
                '".$return['type']."',
                '".(int)$_POST['ext_prj']."',
                '".date('Y-m-d G:i:s')."'
            )";
            
            $mysqli->query($query);
            
            $return['number_id'] =  $mysqli->insert_id;
            
            $query = "DELETE FROM `calls_callback_tmp`
            WHERE `id`=".$obj->id."";
            
            $mysqli->query($query);
        }
    } 
    else {
        $return['id_info'] = 0;
        $return['error'] = 'wrong number';
        
        $query = "INSERT INTO `tbl_numbers_to_give`
        (
            `id_info`, 
            `number`,
            `type`,
            `ext`,
            `note`,
            `datetime`
        )
        VALUES
        (
            ".$obj->id.",
            '".$only_d."',
            '".$return['type']."',
            '".(int)$_POST['ext_prj']."',
            'wrong number',
            '".date('Y-m-d G:i:s')."'
        )";
        
        $mysqli->query($query);
            
        $return['number_id'] =  $mysqli->insert_id;

        $query = "DELETE FROM `calls_callback_tmp`
        WHERE `id`=".$obj->id."";

        $mysqli->query($query);
    }
}

$result->close();
$mysqli->close();

echo json_encode($return);

?>