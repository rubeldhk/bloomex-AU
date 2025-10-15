<?php

function get_need_states() {
    global $mysqli, $min_hour, $max_hour;

    $now_hour = (int)date('H');
    
    $query_country = "SELECT
        `country_id` 
    FROM `jos_vm_country` 
    WHERE `country_3_code`='AUS'
    ";
    
    $result_country = $mysqli->query($query_country);
    $obj_country = $result_country->fetch_object();
    
    $result_country->close();
    
    $query_states = "SELECT
        `state_2_code`, 
        `timezone_offsets` 
    FROM `jos_vm_state` 
    WHERE 
        `country_id`=".$obj_country->country_id."
    ";
    
    $result_states = $mysqli->query($query_states);

    $need_states = array();

    while ($obj_state = $result_states->fetch_object()) {
        $state_hour = $now_hour + $obj_state->timezone_offsets; //system behind 4 hrs

        if ($min_hour <= $state_hour AND $state_hour <= $max_hour) {
            $need_states[] = $obj_state->state_2_code;
        }
    }
    
    $result_states->close();
    
    return $need_states;
}

function show_need_states($link) {
    global $mysqli, $min_hour, $max_hour;

    $now_hour = (int) date('H');

    $query_country = "SELECT
        `country_id` 
    FROM `jos_vm_country` 
    WHERE `country_3_code`='AUS'
    ";
        
    $result_country = $mysqli->query($query_country);
    $obj_country = $result_country->fetch_object();
    
    $result_country->close();

    $query_states = "SELECT
        `state_2_code`, 
        `timezone_offsets` 
    FROM `jos_vm_state` 
    WHERE `country_id`=".$obj_country->country_id."
    ";

    $result_states = $mysqli->query($query_states);

    $need_states = array();
    $state_hours = array();
    
    while ($obj_state = $result_states->fetch_object()) {
        $state_hour = $now_hour + $obj_state->timezone_offsets; //system behind 4 hrs
        $state_hours[$obj_state->state_2_code] = $state_hour;
        if ($min_hour <= $state_hour AND $state_hour <= $max_hour) {
            $need_states[] = $obj_state->state_2_code;
        }
    }
    
    $result_states->close();
    
    return $state_hours;
}

include_once './config.php';
include_once './config_occassion.php';
$type = 'ocassion';
$project = $_COOKIE['project']??'AUS';
include_once 'curl_number.php';
$date = date('Y-m-d');

function array_find_deep($array, $search, $keys = array()) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $sub = array_find_deep($value, $search, array_merge($keys, array($key)));
            if (count($sub)) {
                return $sub;
            }
        } 
        elseif ($value == $search) {
            return array_merge($keys, array($key));
        }
    }

    return array();
}

function check_number_state($number, $need_states) {
    $statees_dial = array(
        "AB" => array(403, 587, 780, 825),
        "BC" => array(250, 778, 236, 604),
        "MB" => array(204, 431),
        "NB" => array(506),
        "NL" => array(709),
        "NT" => array(867),
        "NS" => array(902, 782),
        "NU" => array(867),
        "ON" => array(365, 613, 807, 226, 289, 437, 416, 519, 647, 905, 249, 343, 548, 705),
        "PE" => array(782, 902),
        "QC" => array(579, 873, 514, 581, 819, 438, 418, 450),
        "SK" => array(639, 306),
        "YT" => array(867)
    );


    $dial = substr(ltrim(ltrim($number, "+"), "1"), 0, 3);
    if ($dial) {

        $search_val = array_find_deep($statees_dial, $dial);
        if ($search_val) {
            $checked_state = $search_val[0];
            if (!in_array($checked_state, $need_states)) {

                return false;
            }
        }

        return true;
    } else {
        return false;
    }
}

function check_never_call($number) {
    global $mysqli;
    
    $query = "SELECT 
        `order_id` 
    FROM  `jos_vm_order_user_info` 
    WHERE  
        `phone_1`='".$number."'
        AND  
        `call_customer`='NEVER'
    ";
    
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $return = false;
    } 
    else {
        $return = true;
    }
    
    $result->close();

    $query = "SELECT 
        `id` 
    FROM  `tbl_not_receive` 
    WHERE  
        `number`='".$number."'
    ";
    
    $result = $mysqli->query($query);
    
    if ($result->num_rows > 0) {
        $return = false;
    } 
    else {
        $return = true;
    }
    
    $result->close();

    return $return;
}

function checkcalllater($number, $id_info) {
    global $mysqli;
    
    $datetime_from = date('Y-m-d G:i:s', strtotime('-1 hour', strtotime(date('Y-m-d G:i:s'))));
    $datetime_to = date('Y-m-d G:i:s');
    
    $query = "SELECT 
        *
    FROM `tbl_numbers_to_give`
    WHERE 
        `id_info`='".$id_info."'
        AND 
        `number`='".$number."' 
        AND 
        `answer_type` LIKE 'DEFAULT'
        AND 
        `end_datetime` BETWEEN '".$datetime_from."' AND '".$datetime_to."'
    ";
        
    $result = $mysqli->query($query);
    
    if ($result->num_rows > 0) {
        $return = false;
    } 
    else {
        $return = true;
    }
    
    $result->close();

    return $return;
}

function checktodaycall($number) {
    global $mysqli;
    
    $query = "SELECT 
        *
    FROM `tbl_numbers_to_give`
    WHERE 
        `number`='".$number."' 
        AND 
        DATE_FORMAT(datetime,'%Y-%m-%d')='".date('Y-m-d')."'
    ";
    
    $result = $mysqli->query($query);
    
    if ($result->num_rows > 0) {
        $return = false;
    } 
    else {
        $return = true;
    }
    
    $result->close();

    return $return;
}

$return = array();
$return['type'] = $type;

    $query_numbers = "SELECT 
        `o`.`order_id` AS 'id',
        `ou`.`phone_1` AS 'number',
        `ou`.`country`
    FROM `jos_vm_orders` AS `o`
    LEFT JOIN `jos_vm_order_user_info` AS `ou` 
        ON 
        `ou`.`order_id`=`o`.`order_id`
   LEFT JOIN `jos_vm_api2_orders` AS `ao` 
        ON 
        `ao`.`order_id`=`o`.`order_id`
    WHERE  
        `o`.`customer_occasion` RLIKE 'BIRTH|ANNIV|CONGR|WED|HOL|BABY|AUDAY|HAN|HAL|FD'
        AND 
        `ou`.`call_customer`='DEFAULT' 
        AND  
        `ou`.`address_type`='BT' 
        AND 
        `ou`.`country` in ('AUS','NZL') 
        AND 
        `ou`.`phone_1`!=''
        AND  
        DATE_FORMAT(FROM_UNIXTIME(`o`.`cdate`+ 10 * 60 * 60), '%e-%b') LIKE DATE_FORMAT(date_add(NOW(),interval +82 hour), '%e-%b')
        AND 
        DATE_FORMAT(FROM_UNIXTIME(`o`.`cdate`),'%Y') >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 YEAR),'%Y')";

    switch ($project){
        case "NZL";
            $query_numbers.=" AND ao.id is not null ";
        break;
        case "AUS";
            $query_numbers.=" AND ao.id is null ";
            break;
    }

    $query_numbers .= "GROUP BY `o`.`order_id`  ORDER BY `o`.`order_id` asc
    ";


    $result = $mysqli->query($query_numbers);
    $count = $result->num_rows;
    
    if ($count > 0) {
        $checknumber = false;
        while ($obj = $result->fetch_object()) {
            $only_d = preg_replace("/\D/", '', $obj->number);
            
            if (check_never_call($obj->number) && checkcalllater($only_d, $obj->id) && checktodaycall($only_d)) {
                
                $query = "UPDATE `jos_vm_order_user_info`
                SET 
                    `call_customer`='BUSY'  
                WHERE 
                    `order_id`='".$obj->id."'
                ";
                
                $mysqli->query($query);

                $checknumber = true;
                
                break;
            }
        }
        if ($checknumber) {
            $only_d = substr($only_d,-9);
            if (mb_strlen($only_d) == 9) {
                $return['id_info'] = $obj->id;
                $return['number'] = $only_d;
                $return['country'] = $obj->country;

                $data = curl_number($_POST['ext_prj'], $return);

                if ($data != 1) {
                    $return['error'] = 'no permission to call';
                    $return['id_info'] = 0;
                    
                    $query = "UPDATE `jos_vm_order_user_info`
                    SET 
                        `call_customer`='DEFAULT'  
                    WHERE 
                        `order_id`='".$obj->id."'
                    ";

                    $mysqli->query($query);
                } 
                else {
                    
                    $return['count'] = $count - 1;
                    
                    $query = "INSERT INTO `tbl_numbers_to_give`
                    (
                        `id_info`, 
                        `number`,
                        `type`,
                        `ext`,
                        `datetime`
                    )
                    VALUES (
                        ".$return['id_info'].",
                        '".$only_d."',
                        '".$return['type']."',
                        ".(int)$_POST['ext_prj'].",    
                        '".date('Y-m-d G:i:s')."'
                    )";
                    
                    $mysqli->query($query);

                    $return['number_id'] =  $mysqli->insert_id;
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
                VALUES (
                    ".$return['id_info'].",
                    '".$only_d."',
                    '".$return['type']."',
                    ".(int)$_POST['ext_prj'].",    
                    'wrong number',
                    '".date('Y-m-d G:i:s')."'
                )";

                $mysqli->query($query);

                $return['number_id'] =  $mysqli->insert_id;
                
                $query = "UPDATE `jos_vm_order_user_info`
                SET 
                    `call_customer`='NEVER'  
                WHERE `order_id`='".$obj->id."'
                ";
                
                $mysqli->query($query);
            }
        }
    } 
    else {
        $return['id_info'] = 0;
        $return['status'] = 'cant find good occassion';
        $return['query'] = $query_numbers;
        //$return['statehours'] = show_need_states($link);
        $return['current hour'] = (int) date('H');
    }
//}
    
$result->close();
$mysqli->close();

echo json_encode($return);

?>