<?php

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', '1');

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'administrator/components/com_virtuemart/classes/ps_comemails.php';

global $mysqli;

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$return = array();
$return['result'] = false;
$post = array();

if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $post = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $post = $_POST;
}
Switch ($task = isset($post['task']) ? $post['task'] : '') {
    case "in_transit":

        if (isset($post['route_id'])) {
            $return = in_transit(intval($post['route_id']));
        } else {
            $return['error'] = 'No route_id set';
        }

        break;

    case 'update_status_delivered':
        if (!isset($post['leg_id'])) {
            $return['error'] = 'No leg_id';
            break;
        }
        if (!isset($post['driver_id'])) {
            $return['error'] = 'No driver_id';
            break;
        }

        $return = update_status_delivered((int) $post['driver_id'], (int) $post['leg_id']);
        break;

    case 'update_status':
        if (!isset($post['leg_id'])) {
            $return['error'] = 'No leg_id';
            break;
        }
        if (!isset($post['driver_id'])) {
            $return['error'] = 'No driver_id';
            break;
        }
        if (!isset($post['order_status'])) {
            $return['error'] = 'No order_status';
            break;
        }
        $return = update_status((int) $post['driver_id'], (int) $post['leg_id'], $post['order_status']);
        break;
    case 'send_status':
        if (!isset($post['leg_id'])) {
            $return['error'] = 'No leg_id';
            break;
        }
        if (!isset($post['driver_id'])) {
            $return['error'] = 'No driver_id';
            break;
        }
        if (!isset($post['order_status'])) {
            $return['error'] = 'No order_status';
            break;
        }
        $return = sendStatus((int)$post['driver_id'], (int)$post['leg_id'], $post['order_status']);
        break;
    default:
        $return['error'] = 'no task';
        break;
}


echo json_encode($return);

$mysqli->close();

function update_status_delivered($driver_id, $leg_id) {
    global $mysqli;

    $leg_sql = $mysqli->query("SELECT 
                `ro`.`id`,
                `r`.`id` AS 'route_id',
                `ro`.`order_id`,
                `u`.`user_email`
            FROM `jos_vm_routes_orders` AS `ro`
            INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id` AND `r`.`driver_id`=" . (int) $driver_id . "
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ro`.`order_id`
            INNER JOIN `jos_vm_order_user_info` AS `u` ON `u`.`order_id`=`ro`.`order_id` AND `u`.`address_type`='BT'
            WHERE `ro`.`id`=" . (int) $leg_id . "");

    if ($leg_sql->num_rows > 0) {
        $leg_obj = $leg_sql->fetch_object();

        $ps_comemails = new ps_comemails;
        $email_obj = $ps_comemails->get_email_text($leg_obj->order_id, 'D');

        $result_email = $ps_comemails->send($ps_comemails->setVariables($leg_obj->order_id, $email_obj->email_subject), $ps_comemails->setVariables($leg_obj->order_id, $email_obj->email_html), $leg_obj->order_id, $leg_obj->user_email);
        $return['result_email'] = $result_email;
        $return['result'] = true;
    } else {
        $return['error'] = 'No leg.';
    }
    return $return;
}

function update_status($driver_id, $leg_id, $order_status = 'i') {
    global $mysqli;
    date_default_timezone_set('Australia/Sydney'); //set default timezone
    $now = date("Y-m-d H:i:s");
    $comment = 'update from driver app';

    $leg_sql = $mysqli->query("SELECT 
                `ro`.`id`,
                `r`.`id` AS 'route_id',
                `ro`.`order_id`,
                `u`.`user_email`
            FROM `jos_vm_routes_orders` AS `ro`
            INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id` AND `r`.`driver_id`=" . $driver_id . "
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ro`.`order_id`
            INNER JOIN `jos_vm_order_user_info` AS `u` ON `u`.`order_id`=`ro`.`order_id` AND `u`.`address_type`='BT'
            WHERE `ro`.`id`=" . $leg_id . "");

    if ($leg_sql->num_rows > 0) {
        $leg_obj = $leg_sql->fetch_object();

        $query = "UPDATE `jos_vm_orders` SET
                `order_status`='" . $mysqli->real_escape_string($order_status) . "' 
                WHERE `order_id`=" . $leg_obj->order_id . "
                ";


        $mysqli->query($query);

        $return['status_query'] = $query;
        $return['status_result'] = $mysqli->query($query);

        $query = "INSERT INTO `jos_vm_order_history` 
                    (
                        `order_id`, 
                        `order_status_code`, 
                        `customer_notified`, 
                        `date_added`, 
                        `comments`, 
                        `user_name`
                    )
                    VALUES (
                        '" . $leg_obj->order_id . "',
                        '" . $mysqli->real_escape_string($order_status) . "', 
                        '1', 
                        '" . $now . "', 
                        '" . $mysqli->real_escape_string($comment) . "', 
                        'driverApp'
                    )";

        $return['history_query'] = $query;
        $return['history_result'] = $mysqli->query($query);

        $return['result'] = true;
    } else {
        $return['error'] = 'No leg.';
    }
    return $return;
}

function sendStatus($driverId, $legId, $orderStatus = 'i')
{
    global $mysqli;
    date_default_timezone_set('America/Toronto'); //set default timezone

    $sql = <<<SQL
SELECT 
    `ro`.`id`,
    `r`.`id` AS 'route_id',
    `ro`.`order_id`,
    `u`.`user_email`,
    `d`.`service_name` AS `driver_name`
FROM `jos_vm_routes_orders` AS `ro`
INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id` AND `r`.`driver_id` = $driverId
INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ro`.`order_id`
INNER JOIN `jos_vm_order_user_info` AS `u` ON `u`.`order_id`=`ro`.`order_id` AND `u`.`address_type`='BT'
INNER JOIN `tbl_driver_option` AS `d` ON `d`.`id`=`r`.`driver_id` 
WHERE `ro`.`id` = $legId
SQL;

    $legSql = $mysqli->query($sql);

    if ($legSql->num_rows > 0) {
        $legObj = $legSql->fetch_object();

        $psComemails = new ps_comemails('order_confirmations');
        $emailObj = $psComemails->get_email_text($legObj->order_id, $orderStatus);
        $emailComment = "Order returned by driver " . $legObj->driver_name;

        $emailHtml = str_replace('{UpdateStatusComment}', $emailComment, $emailObj->email_html);

        $resultEmail = $psComemails->send(
            $psComemails->setVariables($legObj->order_id, $emailObj->email_subject),
            $psComemails->setVariables($legObj->order_id, $emailHtml),
            $legObj->order_id,
            $legObj->user_email);

        $return['result_email'] = $resultEmail;

        $return['result'] = true;
    } else {
        $return['error'] = 'No leg.';
    }
    return $return;
}

function in_transit($route_id) {
    global $mysqli;
    date_default_timezone_set('Australia/Sydney'); //set default timezone
    $now = date("Y-m-d H:i:s");
    $query = "SELECT 
                `r`.`id` AS `route_id`,
                `r`.`driver_id`,
                `r`.`warehouse_id`,
                `ro`.`order_id`,
                `u`.`user_email`,
                CONCAT(`d`.`service_name`, ' ', `d`.`driver_option_type`) AS `driver_description`,
                `d`.`description` AS `driver_information`,
                `d`.`service_name` AS `driver_name`,
                `d`.`number` AS `driver_phone`                
            FROM `jos_vm_routes` AS `r`
            INNER JOIN `jos_vm_routes_orders` AS `ro` 
                ON 
                `ro`.`route_id`=`r`.`id` 
            INNER JOIN `jos_vm_order_user_info` AS `u` 
                ON 
                `u`.`order_id`=`ro`.`order_id` 
                AND 
                `u`.`address_type`='BT'
            INNER JOIN `tbl_driver_option` AS `d` 
                ON 
                `d`.`id`=`r`.`driver_id` 
            WHERE
                `r`.`id`=" . $route_id . "
            GROUP BY 
                `ro`.`order_id`
            ";
    $orders_result = $mysqli->query($query);
    $return['orders_result'] = $orders_result;
    $return['myqli_error'] = $mysqli->error;

    if ($orders_result->num_rows > 0) {
        $return['updated_orders'] = array();
        while ($order_obj = $orders_result->fetch_object()) {

            $ps_comemails = new ps_comemails;
            $email_obj = $ps_comemails->get_email_text($order_obj->order_id, 'Z');
            $result_inside = array();
            $email_comment = "Order picked up by driver " . $order_obj->driver_name;

            $email_html = str_replace('{UpdateStatusComment}', $email_comment, $email_obj->email_html);


            $result_email = $ps_comemails->send($ps_comemails->setVariables($order_obj->order_id, $email_obj->email_subject), $ps_comemails->setVariables($order_obj->order_id, $email_html), $order_obj->order_id, $order_obj->user_email);

            $result_inside['email'] = $result_email;
            $return['updated_orders'][$order_obj->order_id] = $result_inside;

            //cancel gofor
            $query = "SELECT shipment_id FROM jos_vm_orders_deliveries WHERE delivery_type=13 and active=1 and `order_id`=" . $order_obj->order_id;

            $res = $mysqli->query($query);
            $checkOrderPostalCodeRate = ($res) ? $res->fetch_object() : false;

            if ($checkOrderPostalCodeRate) {
                include_once $_SERVER['DOCUMENT_ROOT'] . 'scripts/deliveries/gofor/gofor.class.php';
                $gofor = new gofor($mysqli);
                if ($checkOrderPostalCodeRate->shipment_id) {
                    $res = $gofor->CancelanExistingDeliveryRequest($checkOrderPostalCodeRate->shipment_id);
                    $query = "update jos_vm_orders_deliveries SET active=0 where delivery_type=13 and active=1 and `order_id`=" . $order_obj->order_id;
                    $mysqli->query($query);

                    $comment = "Driver app cancel GoFor Shipment " . $checkOrderPostalCodeRate->shipment_id . ". Result: " . json_encode($res);
                    $mysqli->query("INSERT INTO jos_vm_order_history(order_id, date_added,comments,user_name) 
                                             VALUES ('$order_obj->order_id',  '" . date("Y-m-d H:i:s", time()) . "', '$comment','" . $my->username . "')");
                }
            }
        }
        $return['result'] = true;
    }
    return $return;
}
