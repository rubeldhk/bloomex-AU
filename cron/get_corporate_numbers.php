<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

define('USERID', '1000000');


function checkNeverCall($number) {
    global $mysqli;

    $query = "SELECT 
        `order_id`  
    FROM  `jos_vm_order_user_info` 
    WHERE  
        `phone_1`='".$number."'
        AND  
        `call_customer`='NEVER'";

    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $return = true;
    }
    else {
        $return = false;
    }

    $result->close();

    return $return;
}

$query = "
SELECT 
  `ou`.`order_id`, 
  `ou`.`first_name`, 
  `ou`.`last_name`, 
  `ou`.`company`, 
  `ou`.`phone_1`, 
  `ou2`.`phone_1` as phone2, 
  `ou`.`user_email`, 
  `ou`.`state`, 
  `ou`.`city` 
FROM 
  `jos_vm_orders` AS `o` 
  LEFT JOIN `jos_vm_order_user_info` AS `ou` ON `ou`.`address_type` = 'BT' 
  AND `ou`.`order_id` = `o`.`order_id` 
  LEFT JOIN `jos_vm_order_user_info` AS `ou2` ON `ou2`.`phone_1` = `ou`.`phone_1` 
  AND `ou2`.`address_type` = 'BT' 
  AND ou2.call_customer = 'NEVER' 
  LEFT JOIN `jos_vm_shopper_vendor_xref` AS `x` ON `x`.`user_id` = `ou`.`user_id` 
  LEFT JOIN `jos_vm_shopper_group` AS `g` ON `g`.`shopper_group_id` = `x`.`shopper_group_id` 
  LEFT JOIN `jos_free_email_domains` AS `d` ON `d`.`domain` = SUBSTRING_INDEX(`ou`.`user_email`, '@', -1) 
WHERE 
  `ou`.`user_email` IS NOT NULL 
  AND `ou2`.`order_info_id` IS NULL 
  AND `d`.`id` IS NULL 
  AND `g`.`shopper_group_discount` = '0.00' AND  
  ou.user_id > '" . USERID . "'
  AND ou.user_id not in (
    select 
      user_id 
    from 
      jos_new_corporate_calls cc 
      inner join jos_vm_orders o on o.order_id = cc.order_id
  ) 
GROUP BY 
  ou.user_id 
ORDER BY 
  `ou`.`order_id` ASC 
LIMIT 
  100
";
echo '<pre>';
print_r($query);
echo '</pre>';
$result = $mysqli->query($query);
if (!$result) {
    printf(__LINE__ . "query: %s\n", $query);
    printf(__LINE__ . "Errormessage: %s\n", $mysqli->error);
}
if ($result->num_rows > 0) {
    echo "$result->num_rows rows";
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        $obj->phone_1 = preg_replace('/\D/siu', '', $obj->phone_1);
        if($obj->phone_1 == '' || checkNeverCall($obj->phone_1)) {
            $query = "UPDATE `jos_vm_order_user_info`
                SET 
                    `call_customer`='NEVER'  
                WHERE `order_id`=".$obj->order_id."
                ";

            $mysqli->query($query);
            continue;
        }
        $obj->phone_1 = '61' . substr($obj->phone_1, -9);
        $inserts[] = "(
                '" . $mysqli->real_escape_string($obj->first_name) . "',
                '" . $mysqli->real_escape_string($obj->last_name) . "',
                " . (int) $obj->order_id . ",
                '" . $mysqli->real_escape_string($obj->company) . "',
                '" . $mysqli->real_escape_string($obj->user_email) . "',
                '" . $mysqli->real_escape_string($obj->phone_1) . "',
                '" . date('Y-m-d H:i:s') . "',
                '0'
            )";

    }

    if (sizeof($inserts) > 0) {
        $query = "INSERT INTO 
            `jos_new_corporate_calls`
            (
                `first_name`,
                `last_name`,
                `order_id`,
                `company_name`,
                `email`,
                `phone`,
                `datetime_add`,
                `status`
            )
            VALUES 
                " . implode(',', $inserts) . "
            ";

        echo $query;

        $result = $mysqli->query($query);
        if (!$result) {
            printf(__LINE__ . "query: %s\n", $query);
            printf(__LINE__ . "Errormessage: %s\n", $mysqli->error);
        }
    }
} else {
    echo "no rows";
}
$mysqli->close();

