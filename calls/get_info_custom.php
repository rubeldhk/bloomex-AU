<?php

include_once './config.php';
$type = 'ocassion';
include_once 'curl_number.php';
include_once 'timezones.php';
$date = date('Y-m-d');

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
    FROM `tbl_not_receive` 
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

$return = array();
$return['type'] = $type;
$need_states = get_need_states();

if (sizeof($need_states) > 0) {
    $query_numbers = "SELECT 
        `o`.`order_id` AS 'id',
        `ou`.`phone_1` AS 'number'
    FROM `jos_vm_orders` as `o`
    LEFT JOIN `jos_vm_order_user_info` AS `ou` 
        ON
            `ou`.`order_id`=`o`.`order_id`
    WHERE 
        (
            `o`.`customer_occasion` LIKE 'BIRTH' 
            OR 
            `o`.`customer_occasion` LIKE  'ANNIV'
        )  
        AND 
        `ou`.`call_customer`='DEFAULT' 
        AND  
        `ou`.`address_type`='BT' 
        AND 
        `country`='CAN' 
        AND 
        `ou`.`phone_1`!=''
        AND 
        `ou`.`state` IN ('".implode("','", $need_states)."')
        AND  
        DATE_FORMAT(FROM_UNIXTIME(`o`.`cdate`), '%e-%b') LIKE DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 3 DAY), '%e-%b')
        GROUP BY `o`.`cdate` DESC";

    $result = $mysqli->query($query_numbers);
    $count = $result->num_rows;
    
    if ($count > 0) {
        $checknumber = false;
        
        while ($obj = $result->fetch_object()) {
        // this actually checks if we CAN call a number, reason is to find if any callables here
            if (check_never_call($obj->number)) {
                $checknumber = true;
                break;
            }
        }
        if ($checknumber) {
            $only_d = preg_replace("/\D/", '', $obj->number);

            if (mb_strlen($only_d) == 10 OR mb_strlen($only_d) == 11) {
                $return['id_info'] = $obj->id;
                $return['number'] = (mb_strlen($only_d) == 10) ? '1' . $only_d : $only_d;

                $data = curl_number($_POST['ext_prj'], $return);
                // $data=1;
                if ($data != 1) {
                    $return['id_info'] = 0;
                } 
                else {
                    $query = "UPDATE `jos_vm_order_user_info`
                    SET 
                        `call_customer`='BUSY'  
                    WHERE `order_id`=".$return['id_info']."
                    ";
                    
                    $mysqli->query($query);
                    
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
                        '".$return['number']."',
                        '".$return['type']."',
                        '".(int)$_POST['ext_prj']."',    
                        NOW()
                    )";
                    
                    $mysqli->query($query);
                }
            } 
            else {
                $return['id_info'] = 0;
                
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
                    ".$obj->id.",
                    '".$only_d."',
                    '".$return['type']."',
                    '".(int)$_POST['ext_prj']."',    
                    'wrong number',
                    NOW()
                )";

                $mysqli->query($query);

                $query = "UPDATE `jos_vm_order_user_info`
                SET 
                    `call_customer`='NEVER'  
                WHERE `order_id`=".$obj->id."
                ";
                
                $mysqli->query($query);
            }
        }
    } 
    else {
        $return['id_info'] = 0;
        $return['status'] = 'cant find good ocasion';
        $return['query'] = $query_numbers;
        $return['statehours'] = show_need_states();
        $return['current hour'] = (int) date('H');
    }
} 
else {
    $return['id_info'] = 0;
    $return['status'] = 'no good states';
    $return['statehours'] = $need_states;
    $return['current hour'] = (int) date('H');
}

$result->close();
$mysqli->close();

echo json_encode($return);

?>
