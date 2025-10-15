<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
date_default_timezone_set('Australia/Sydney');
$timestamp = time();
$cron_username = 'Cron Bot';
$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
require_once $mosConfig_absolute_path . '/includes/joomla.php';
require_once $mosConfig_absolute_path . '/administrator/components/com_virtuemart/classes/ps_comemails.php';

include_once $mosConfig_absolute_path . '/cron/update_delivery_services_tracking.php';

//in case joomla set this wrong 
date_default_timezone_set('Australia/Sydney');

$query = "SELECT 
    `o`.`order_id`,
    `o`.`ddate`,
    `o`.`warehouse`
FROM `jos_vm_orders` AS `o`
WHERE 
    (
        ( 
            `o`.`order_status`='Z'
        ) 
        AND 
        (
            STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') < DATE_SUB(NOW(), INTERVAL 5 DAY)
        )
    )
ORDER BY 
    `o`.`order_id`
";

$orders_result = $mysqli->query($query);

if ($orders_result->num_rows > 0) {
    while ($order_obj = $orders_result->fetch_object()) {
        $query = "UPDATE `jos_vm_orders` 
        SET
            `order_status`='D', 
            `mdate`='" . $timestamp . "' 
        WHERE 
            `order_id`=" . $order_obj->order_id . "
        ";

        $update_result = $mysqli->query($query);

        if (!$update_result) {
            die('Update error: ' . $mysqli->error);
        }

        $query = "INSERT INTO `jos_vm_order_history`
        (
            `order_id`,
            `order_status_code`,
            `warehouse`,
            `date_added`,
            `customer_notified`,
            `warehouse_notified`,
            `comments`,
            `user_name`
        )
        VALUES (
            " . $order_obj->order_id . ",
            'D',
            '" . $order_obj->warehouse . "',          
            '" . $mysqlDatetime . "',
            '0',
            '0',
            'Cron Update Status - 5 days past delivery',
            '" . $cron_username . "'
        )
        ";
        $insert_result = $mysqli->query($query);

        if (!$insert_result) {
            die('Insert error: ' . $mysqli->error);
        }
    }
}
$orders_result->close();

$tz_sydney_wh_list = array('WH12', 'WH14', 'vic', 'WH15'); //sydney, brisbane, victoria, melbourne GMT+10
$tz_perth_wh_list = array('p01'); // GMT+8

$delivery_list = array(
    8 => $tz_perth_wh_list,
    10 => $tz_sydney_wh_list
);

$query = "SELECT
    `email_subject`, 
    `email_html` 
FROM `jos_vm_emails` 
WHERE 
    `email_type`='2' 
    AND 
    `recipient_type`='1' 
    AND `order_status_code`='D'
";

$confirmation_result = $mysqli->query($query);

if ($confirmation_result->num_rows > 0) {
    $confirmation_obj = $confirmation_result->fetch_object();
} else {
    $confirmation_result->close();

    $query_sel = "SELECT 
        `email_subject`, 
        `email_html` 
    FROM `jos_vm_emails` 
    WHERE 
        `email_type`='2' 
        AND 
        `recipient_type`='1'
    ";

    $confirmation_result = $mysqli->query($query);
    $confirmation_obj = $confirmation_result->fetch_object();
}

$i = 0;
$output = '';
foreach ($delivery_list as $time_shift => $wh_list) {
    $query = "SELECT 
        `o`.`order_id`,
        `i`.`user_email`,
        `i`.`first_name`,
        `o`.`priority`,
        `o`.`warehouse`,
        STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y') AS `ddate`
    FROM `jos_vm_orders` AS `o`
    LEFT JOIN `jos_vm_order_user_info` AS `i` 
        ON 
        `i`.`order_id`=`o`.`order_id`
    LEFT JOIN `jos_vm_orders_deliveries` as `d` 
        ON   
        `d`.`order_id`=`o`.`order_id`
    LEFT JOIN `jos_vm_deliveries` as `e` 
        ON   
        `e`.`id`=`d`.`delivery_type`
    WHERE 
        ( 
            (
                (
                    o.`order_status` LIKE 'Z' 
                    OR 
                    (
                        o.`order_status` LIKE 'C' 
                        AND 
                        o.warehouse LIKE '005'
                    ) 
                ) 
                AND
                (
                    (`o`.`ship_method_id` LIKE 'standard_shipping|Bloomex Australia|$14.95 - Regular%')
                    AND
                    DATE_ADD(STR_TO_DATE(`o`.`ddate`, '%d-%m-%Y'), INTERVAL 20 HOUR) <= DATE_ADD(NOW(), INTERVAL " . $time_shift . " HOUR)
                )
            )
        ) 
        AND 
        (
            `i`.`address_type`='BT' 
        )
        AND 
        (
           (`o`.`warehouse` IN ('" . implode('\',\'', $wh_list) . "'))
         
        )
        AND (
           d.id is null
        )
        ORDER BY 
            `o`.`order_id` ASC LIMIT 200
    ";
    
    $orders_result = $mysqli->query($query);
    if (!$orders_result) {
        printf("Error: %s\n", $mysqli->error);
    }

    if ($orders_result->num_rows > 0) {
        while ($order_obj = $orders_result->fetch_object()) {
            echo $order_obj->order_id . " to update , ddate" . $order_obj->order_id;
    
            $q = "UPDATE `jos_vm_orders` 
            SET
                `order_status`='D',
                `mdate`='" . $timestamp . "'
            WHERE
                `order_id`='" . $order_obj->order_id . "'";

            $mysqli->query($q);

            $q = "INSERT INTO jos_vm_order_history ";
            $q .= "(order_id,order_status_code,warehouse,priority,date_added,customer_notified,warehouse_notified,comments,user_name) VALUES (";
            $q .= "'" . $order_obj->order_id . "', 'D', '" . $order_obj->warehouse . "','" . $order_obj->priority . "', '$mysqlDatetime', 'Y', 'N',  'Cron Update Status', '" . $cron_username . "')";
            $result_his = $mysqli->query($q);
            if (!$result_his) {
                printf("Errormessage: %s\n", $mysqli->error);
                printf("Query: %s\n", $q);
                die();
            }

            $ps_comemails = new ps_comemails;
            $confirmation_obj->email_html = str_replace('{UpdateStatusComment}', '', $confirmation_obj->email_html);


            $m = new MAIL5;
            $m->From($mosConfig_mailfrom_noreply);
            $addto = $m->AddTo($order_obj->user_email);

            if ($addto) {
                $m->Subject($ps_comemails->setVariables((int)$order_obj->order_id, $confirmation_obj->email_subject));
                $m->Html($ps_comemails->setVariables((int)$order_obj->order_id, $confirmation_obj->email_html));
                $c = $m->Connect($mosConfig_smtphost, (int)$mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
                if ($c) {
                    if (!$m->Send($c)) {
                        $output .= "<pre>";
                        var_dump($m->History);
                        print_r($m->History);
                        list($tm1, $ar1) = each($m->History[0]);
                        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                    } else {
                        $i++;
                        $output .= 'Mail sent to  ' . $order_obj->user_email . '<br>';
                    }
                    $m->Disconnect();
                }
            } 
            else {
                $output .= 'Wrong email address ' . $order_obj->user_email;
            }
        }

        if ($i > 0) {
            $output .= PHP_EOL . PHP_EOL . "we sent email to " . $i . " recipients" . PHP_EOL;
        } else {
            $output .= 'there is no orders to update status';
        }
    }
    $orders_result->close();
}

echo "$output";
$mysqli->close();
unset($database);
