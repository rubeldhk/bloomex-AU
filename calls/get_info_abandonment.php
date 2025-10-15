<?php

include_once './config.php';
$type = 'abandonment';
$project = $_COOKIE['project']??'AUS';
include_once 'curl_number.php';
$date = date('Y-m-d');

function check_never_call($number) {
    global $mysqli;

    $query = "SELECT 
        `id`  
    FROM `tbl_cart_abandonment` 
    WHERE 
        `number`='" . $number . "'
        AND  
        `call_customer`='NEVER'
    ";

    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $return = false;
    } else {
        $return = true;
    }

    $result->close();

    if ($return) {

        $query = "SELECT
        `id` 
    FROM `tbl_not_receive` 
    WHERE 
        `number`='" . $number . "'
    ";

        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $return = false;
        } else {
            $return = true;
        }

        $result->close();
    }
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
        `id_info` = '" . $id_info . "'
        AND 
        `number`='" . $number . "' 
        AND 
        `answer_type` LIKE 'DEFAULT'
        AND 
        `end_datetime` BETWEEN '" . $datetime_from . "' AND '" . $datetime_to . "'
    ";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $return = false;
    } else {
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
        `number`='" . $number . "' 
        AND 
        DATE_FORMAT(`datetime`,'%Y-%m-%d')='" . date('Y-m-d') . "'
    ";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $return = false;
    } else {
        $return = true;
    }

    $result->close();

    return $return;
}

$return = array();
$return['type'] = $type;

//$datetime_from = strtotime("-15 minutes", time());
//$datetime_to = strtotime("-30 minutes", time());

$datetime_from = date('Y-m-d H:i:s', strtotime('-60 minutes', strtotime(date('Y-m-d G:i:s'))));
$datetime_to = date('Y-m-d H:i:s', strtotime('-30 minutes', strtotime(date('Y-m-d G:i:s'))));

$query_numbers = "SELECT 
    `a`.`id`, 
    `a`.`number`, 
    `a`.`user_id`, 
    `a`.`link`, 
    `a`.`first_name`, 
    `a`.`status`  
FROM  `tbl_cart_abandonment` as `a`
LEFT JOIN `jos_vm_user_info` AS `u` ON `u`.`user_id`=`a`.`user_id`
WHERE  
(
    ( `a`.`status`  = 'abandonment' ) 
    OR 
    ( `a`.`status`  = 'sent' )
) 
AND  
(
    (`a`.`call_customer` = 'DEFAULT')
    OR
    (`a`.`call_customer` = 'CALLBACK')  
)
AND  
(`a`.`number`  != '' )";

switch ($project){
    case "NZL";
        $query_numbers.=" AND a.project is not null ";
    break;
    case "AUS";
        $query_numbers.=" AND a.project is null ";
        break;
}

$query_numbers .= " AND  (`a`.`datetime_dt` BETWEEN '" . $datetime_from . "' AND '" . $datetime_to . "')
group by `a`.`user_id`
ORDER BY `a`.`id`";
$result = $mysqli->query($query_numbers);
$count = $result->num_rows;

if ($count > 0) {
    $checknumber = false;

    while ($obj = $result->fetch_object()) {
        $only_d = preg_replace("/\D/", '', $obj->number);

        if (check_never_call($obj->number) AND checkcalllater($only_d, $obj->id) AND checktodaycall($only_d)) {
            $query = "UPDATE `tbl_cart_abandonment`
            SET 
                `call_customer`='BUSY'  
            WHERE `id`=" . $obj->id . "
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
            $return['country'] = $obj->user_id?'AUS':'NZL';
            $return['number'] = $only_d;

            $data = curl_number($_POST['ext_prj'], $return);

            //$data = 1;

            if ($data != 1) {
                $return['error'] = 'no permission to call';
                $return['id_info'] = 0;

                $query = "UPDATE `tbl_cart_abandonment`
                SET 
                    `call_customer`='DEFAULT'  
                WHERE `id`=" . $obj->id . "
                ";

                $mysqli->query($query);
            } else {
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
                    " . $return['id_info'] . ",
                    '" . $only_d . "',
                    '" . $return['type'] . "',
                    '" . (int) $_POST['ext_prj'] . "',
                    '" . date('Y-m-d G:i:s') . "'
                )";

                $mysqli->query($query);

                $return['number_id'] = $mysqli->insert_id;
            }
        } else {
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
                " . $obj->id . ",
                '" . $only_d . "',
                '" . $return['type'] . "',
                '" . (int) $_POST['ext_prj'] . "',
                'wrong number',
                '" . date('Y-m-d G:i:s') . "'
            )";

            $mysqli->query($query);

            $return['number_id'] = $mysqli->insert_id;

            $query = "UPDATE `tbl_cart_abandonment`
            SET 
                `call_customer`='NEVER'  
            WHERE `id`=" . $obj->id . "
            ";

            $mysqli->query($query);
        }
    } else {
        $return['error'] = 'no check number';
        $return['id_info'] = 0;
    }
} else {
    $return['error'] = 'no need abandonment';
    $return['id_info'] = 0;
}

$return['datestart'] = $datetime_from;
$return['datetime_to'] = $datetime_to;
$return['now'] = date('c');

$result->close();
$mysqli->close();

echo json_encode($return);
?>