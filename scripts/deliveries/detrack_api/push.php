<?php
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

require_once $mosConfig_absolute_path . '/includes/joomla.php';
require_once $mosConfig_absolute_path . '/includes/MAIL5/MAIL5.php';
require_once $mosConfig_absolute_path . '/administrator/components/com_virtuemart/classes/ps_comemails.php';

session_name(md5($mosConfig_live_site));
session_start();

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$json = isset($_POST['json']) ? $_POST['json'] : '';
$input = json_decode($json, TRUE);

//test notification
$m = new MAIL5;
$m->From($mosConfig_mailfrom);
$addto = $m->AddTo('errors@bloomex.com.au');


$m->Subject('Incoming detrack push notification');
$m->Html($json);
$c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
if ($c) {
    if (!$m->Send($c)) {
        $output .= "<pre>";
        var_dump($m->History);
        print_r($m->History);
        list($tm1, $ar1) = each($m->History[0]);
        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
    }
    $m->Disconnect();
}

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
$confirmation_obj = $confirmation_result->fetch_object();

//set variables
$order_id = $mysqli->real_escape_string(substr($input['do'], 2));
$order_status = $mysqli->real_escape_string($input['status']);
$delivery_reason = $mysqli->real_escape_string($input['reason']);
$order_note = $mysqli->real_escape_string($input['note']);

$warehouse = "";
$priority = "";

$order_sql = $mysqli->query("SELECT o.*,i.user_email
        FROM `jos_vm_orders` as o 
        join jos_vm_order_user_info as i on i.order_id=o.order_id and i.address_type='BT'
        WHERE o.`order_id`=" . $order_id . "");

if ($order_sql->num_rows > 0) {
    $order_obj = $order_sql->fetch_object();
    $user_email = $order_obj->user_email;
    if ($order_obj->warehouse == "NOWAREHOUSEASSIGNED") {
        $warehouse = "NOWAR";
    } else {
        $warehouse = $order_obj->warehouse;
    }
    $priority = $order_obj->priority;
} else {
    echo $order_sql->num_rows;
}
if ($input['status'] == 'Delivered') {
    $order_status_code = "D";
    $message = "Delivery confirmation by Detrack" . ($order_note ? " Drivers note: " . $order_note : "");

    if ($user_email) {

        $ps_comemails = new ps_comemails;
        $confirmation_obj->email_html = str_replace('{UpdateStatusComment}', '', $confirmation_obj->email_html);

        $m = new MAIL5;
        $m->From($mosConfig_mailfrom);
        $addto = $m->AddTo($user_email);

        if ($addto) {
            $m->Subject($ps_comemails->setVariables((int) $order_id, $confirmation_obj->email_subject));
            $m->Html($ps_comemails->setVariables((int) $order_id, $confirmation_obj->email_html));
            $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
            if ($c) {
                if (!$m->Send($c)) {
                    $output .= "<pre>";
                    var_dump($m->History);
                    print_r($m->History);
                    list($tm1, $ar1) = each($m->History[0]);
                    list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                } else {
                    $i++;
                    $output .= 'Mail sent to  ' . $user_email . '<br>';
                }
                $m->Disconnect();
            }
        }
    }
} else {
    $order_status_code = "i";
    $message = "Reason: " . $delivery_reason . ". Note: " . $order_note;
}
$mysqli->query("UPDATE `jos_vm_orders`
                SET `order_status`='" . $order_status_code . "'
                WHERE `order_id`=" . $order_id . "
                ");

date_default_timezone_set('Australia/Sydney');
$date_now = date("Y-m-d G:i:s", time());

$mysqli->query("INSERT INTO `jos_vm_order_history`
                (
                    `order_id`, 
                    `customer_notified`, 
                    `order_status_code`, 
                    `priority`,
                    `warehouse`,
                    `date_added`, 
                    `comments`,
                    `user_name` 
                )
                VALUES
                (
                    '" . $mysqli->real_escape_string($order_id) . "',
                    '1',
                    '" . $mysqli->real_escape_string($order_status_code) . "', 
                    '" . $mysqli->real_escape_string($priority) . "',
                    '" . $mysqli->real_escape_string($warehouse) . "',
                    '" . $mysqli->real_escape_string($date_now) . "',
                    '" . $mysqli->real_escape_string($message) . "',
                    'Detrack bot')
                ");
if ($input) {
    $mysqli->query("INSERT INTO `jos_order_detrack_json`
                    (
                        `order_id`, 
                        `json_body`,
                        `date_added`
                    )
                    VALUES
                    (
                        " . $mysqli->real_escape_string($order_id) . ",
                        '" . $mysqli->real_escape_string($json) . "', 
                        NOW()
                    )
                   ");
}
