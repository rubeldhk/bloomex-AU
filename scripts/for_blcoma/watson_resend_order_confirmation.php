<?php
define('_VALID_MOS', true);
define('_JEXEC', true);
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';

global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);

if ($_POST['key'] == md5($_POST['order_id'].'watson')) {
    echo json_encode(SendMailAgain($_POST));
}


function SendMailAgain()
{
    global $database, $mosConfig_mailfrom, $mosConfig_fromname;

    $return = array();
    $order_id = (int)$_POST['order_id'];
    $user_info_obj = false;
    $query = "SELECT 
        `user_email` 
    FROM `jos_vm_order_user_info`
    WHERE `order_id`=".$order_id." AND `address_type`='BT'";

    $database->setQuery($query);
    $database->loadObject($user_info_obj);

    require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/classes/ps_comemails.php';

    $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='1'";
    $confirmation_obj = false;
    $database->setQuery($query);
    $database->loadObject($confirmation_obj);

    $ps_comemails = new ps_comemails;

    $a = mosMail($mosConfig_mailfrom, $mosConfig_fromname, $user_info_obj->user_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $confirmation_obj->email_html), 1);

    if($a){
        $return['result'] = true;
        $return['email'] = $user_info_obj->user_email;
    }else{
        $return['result'] = false;
        $return['email'] = $user_info_obj->user_email;
    }
        return $return;
}
?>

