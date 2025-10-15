<?php

include_once(__DIR__ . "/../includes/kint.phar");
include_once(__DIR__ . "/../includes/kint_fleo.php");
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
$time = time();

require_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';


require_once $mosConfig_absolute_path . '/includes/joomla.php';
require_once $mosConfig_absolute_path . '/administrator/components/com_virtuemart/classes/ps_comemails.php';


$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

date_default_timezone_set("Australia/Sydney");
$mailer = new ps_comemails();
function markOrder($row, $order_mark, $mysqli) {
    $orderID = $row['order_id'];
    if (!$orderID) {
        echo "This Order is not exist.";
        exit(0);
    }
    $mark_desc = $mysqli->real_escape_string(strip_tags($row['mark_description']));
    $selected_mark = $order_mark['order_mark_code'];
    $selected_mark_name = $order_mark['order_mark_name'];
    $mysqlDatetime = date("Y-m-d G:i:s");
    $query = "INSERT INTO jos_vm_order_mark_history (
            order_id,
            date_added,
            order_mark_code,
            description, 
            user_name
        ) VALUES (
            '$orderID',
            '" . $mysqlDatetime . "',
            '" . $selected_mark . "',
            '" . $mark_desc . "', 
            'Mass_email sender'
        )";
    $mysqli->query($query);

    $query = "Select order_status_code from jos_vm_order_history where order_id = '$orderID' order by order_status_history_id desc limit 1";
    $result = $mysqli->query($query);
    $res = $result->fetch_assoc();
    $comment = 'mark : ' . $selected_mark_name;
    if ($mark_desc) {
        $comment .= " <br>description : " . $mark_desc;
    }

    $query = "INSERT INTO jos_vm_order_history (
                order_id,
                date_added,
                order_status_code,
                comments, user_name
        ) VALUES (
                  '$orderID',
                '" . $mysqlDatetime . "',
                '" . $res . "',
                '$comment', 
                'Mass_email sender'
        )";
    $mysqli->query($query);
}
$now = date("Y-m-d  H:i:s");
$query = "SELECT et.*,efs.*,ui.*,efs.order_id as 'order_id',et.id as 'id',efs.id as 'email_id' FROM `tbl_email_text` et "
        . " INNER JOIN `tbl_emails_for_sending` efs ON efs.text_id=et.id "
        . " INNER JOIN `jos_vm_order_user_info` ui on ui.order_id = efs.order_id AND ui.address_type= 'BT' "
        . " LEFT JOIN `tbl_not_receive` AS `nr` ON `nr`.`email`=`ui`.`user_email` "
        . " WHERE efs.sent_datetime = 0 AND `nr`.`id` IS NULL and et.publish=1 and et.date < NOW() LIMIT 0, $mosConfig_limit_email_sender";

$result = $mysqli->query($query);


if (!$result) {
    die('SELECT error: ' . $mysqli->error);
}
f($query, $result);
$i = 0;

while ($row = $result->fetch_assoc()) {
    f($row);
    if ($row['order_id'] < 1000000) { //jsut in case
        die("wtf order id " . $row['order_id']);
    }
    $user_email = $row['user_email'];

    $order_id = $row['order_id'];
    $html_send = str_replace('{user_name}', (isset($row['first_name']) ? $row['first_name'] : ""), $row['text']);
    $html_send = str_replace('{user_last_name}', (isset($row['last_name']) ? $row['last_name'] : ""), $html_send);
    $html_send = str_replace('{user_email}', (isset($row['user_email']) ? $row['user_email'] : ""), $html_send);
    $html_send = str_replace('{order_id}', $order_id, $html_send);

    $html_subject = str_replace('{user_name}', (isset($row['first_name']) ? $row['first_name'] : ""), $row['subject']);
    $html_subject = str_replace('{user_last_name}', (isset($row['last_name']) ? $row['last_name'] : ""), $html_subject);
    $html_subject = str_replace('{user_email}', (isset($row['user_email']) ? $row['user_email'] : ""), $html_subject);
    $html_subject = str_replace('{order_id}', $order_id, $html_subject);


    $confirmation_obj = $mailer->get_email_text($row['order_id'], $row['order_status_code'] ?? '');

    $email_html = str_replace('{UpdateStatusComment}', $html_send, $confirmation_obj->email_html);
    $email_html = $mailer->setVariables($row['order_id'], $email_html);

    $mail_result = $mailer->send($html_subject, $email_html, $row['order_id'], $user_email);


    if ($row['order_status_code']) {
        $qwerty = "UPDATE `jos_vm_orders` SET `order_status`='" . $row['order_status_code'] . "' WHERE order_id='" . $row['order_id'] . "'";
        $mysqli->query($qwerty);
    }
    if ($row['mark_description'] || $row['mark_status']) {
        $qwerty = "SELECT * FROM jos_vm_order_mark WHERE order_mark_code='" . $row['mark_status'] . "'";
        $resultQuery = $mysqli->query($qwerty);
        $orderMark = $resultQuery->fetch_assoc();
        if ($orderMark) {
            markOrder($row, $orderMark, $mysqli);
        }
    }
    $comments = " Text id: " . $row['id'] . "<br>"
            . " Email subject: " . $mysqli->real_escape_string(strip_tags($html_subject)) . " <br> "
            . " Email text: " . $mysqli->real_escape_string(strip_tags($html_send));

    $mysqlDatetime = date('Y-m-d H:i:s');

    $qwerty = "INSERT INTO `jos_vm_order_history` (order_id,order_status_code,user_name,customer_notified,comments, date_added) "
            . " VALUES ("
            . "'" . $row['order_id'] . "' , "
            . "'" . $row['order_status_code'] . "'" . ", "
            . "'Mass order email' ,"
            . " 1, "
            . "'" . $comments . "',   '" . $mysqlDatetime . "')";

    $result_history = $mysqli->query($qwerty);

    if (!$result_history) {
        echo "<hr>";
        echo $qerty;
        echo "<hr>";
        die($mysqli->error);
    }

    if ($mail_result) {
        $i++;
        $qwerty = "UPDATE `tbl_emails_for_sending` SET `sent_datetime`=NOW(),`send_status`='ok' where `id`='" . $row['email_id'] . "'";
        $mysqli->query($qwerty);
        echo '<br/>' . $row['order_id'] . '<strong> Mail sent to </strong>  ' . $row['user_email'];
    } else {
        echo '<br/>' . $row['order_id'] . '<strong> Mail failed to </strong>  ' . $row['user_email'];
        $qwerty = "UPDATE `tbl_emails_for_sending` SET `sent_datetime`=NOW() `send_status`='$mail_result' where `id`='" . $row['email_id'] . "'";
    }
}
echo "<br>$i emails total. Process took " . (time() - $time) . " seconds";
$mysqli->close();
