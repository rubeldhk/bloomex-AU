<?php
define('_VALID_MOS', true);
define('_JEXEC', true);
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';

global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);

$return = array();


if ($_POST['key'] == md5($_POST['order_id'].'watson')) {
    if($_POST['notify_customer'] && $_POST['notify_customer']==1){
        $return['notify_customer']=notify_customer($_POST);
    }
    if($_POST['notify_warehouse'] && $_POST['notify_warehouse']==1){
        $return['notify_warehouse']=notify_warehouse($_POST);
    }
}

echo json_encode($return);



function notify_customer(&$d) {
    global $database, $mosConfig_mailfrom, $mosConfig_fromname;
    $user_info_obj = false;
    $query = "SELECT 
            `user_email` 
        FROM `jos_vm_order_user_info`
        WHERE `order_id`=" . (int) $d["order_id"] . " AND `address_type`='BT'";

    $database->setQuery($query);
    $database->loadObject($user_info_obj);
    require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/classes/ps_comemails.php';

    $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1' AND `order_status_code`='" . $database->getEscaped($d["order_status"]) . "'";
    $confirmation_obj = false;
    $database->setQuery($query);
    $database->loadObject($confirmation_obj);

    if (!$confirmation_obj) {
        $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1'";
        $confirmation_obj = false;
        $database->setQuery($query);
        $database->loadObject($confirmation_obj);
    }

    $ps_comemails = new ps_comemails;

    if (($d['include_comment']) && !empty($d['order_comment'])) {
        $confirmation_obj->email_html = str_replace('{UpdateStatusComment}', htmlspecialchars($d['order_comment']), $confirmation_obj->email_html);
    } else {
        $confirmation_obj->email_html = str_replace('{UpdateStatusComment}', '', $confirmation_obj->email_html);
    }

    $a = mosMail($mosConfig_mailfrom, $mosConfig_fromname, $user_info_obj->user_email, $ps_comemails->setVariables((int) $d["order_id"], $confirmation_obj->email_subject), $ps_comemails->setVariables((int) $d["order_id"], $confirmation_obj->email_html), 1);
    if($a){
        return true;
    }else{
        return false;
    }


}

function notify_warehouse(&$d) {
    global $database, $mosConfig_mailfrom, $mosConfig_fromname;

    $warehouse_obj = false;
    $query = "SELECT 
            w.`warehouse_name`, 
            w.`warehouse_email` ,
            s.order_status_name
        FROM `jos_vm_orders` as o 
        left join `jos_vm_warehouse` as w on w.warehouse_code=o.warehouse
        left join `jos_vm_order_status` as s on s.order_status_code=o.order_status
        WHERE o.`order_id`='" . $database->getEscaped($d['order_id']) . "'";
    $database->setQuery($query);
    $database->loadObject($warehouse_obj);

    if($warehouse_obj->warehouse_email){
        $subject = "Order Status Change: " . $d["order_id"] . " - " . $warehouse_obj->order_status_name;
        $body = "Order Status Change: " . $d["order_id"] . " - " . $warehouse_obj->order_status_name . $d['comments'];
        $a = mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_obj->warehouse_email, $subject, $body, 1);
        if($a){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
?>

