<?php

defined('_VALID_MOS') or die('Restricted access');
require_once( $mainframe->getPath('front_html') );
$confirm = trim(mosGetParam($_REQUEST, "confirm"));

switch ($task) {
    case "decline":
        HTML_LocalPartner_Orders::viewDeclinePage();
        break;
    case "confirm":
        HTML_LocalPartner_Orders::viewConfirmPage();
        break;
    default:
        viewPage();
        break;
}

function viewPage() {
    global $mainframe, $database, $my, $Itemid, $mosConfig_live_site;
    $partner_id = trim(mosGetParam($_REQUEST, "partner_id"));
    $order_id = trim(mosGetParam($_REQUEST, "order_id"));
    $confirm = trim(mosGetParam($_REQUEST, "confirm"));
    $key = trim(mosGetParam($_REQUEST, "key"));
    $comment = mysql_real_escape_string(trim(mosGetParam($_REQUEST, "comment", 0)));
    if ($comment) {
        $comment = " Comment left: " . $comment;
    } else {
        $comment = 'No comments left';
    }

    $q = "SELECT partner_name,partner_price,confirm  FROM tbl_local_parthners_orders  "
            . "LEFT JOIN tbl_local_parthners  on tbl_local_parthners_orders.partner_id = tbl_local_parthners.partner_id ";
    $q .= "WHERE tbl_local_parthners_orders.order_id = '" . $order_id . "' ";
    $q .= "AND tbl_local_parthners_orders.partner_id = '" . $partner_id . "' ";
    $database->setQuery($q);
    $database->query();
    $database->loadObject($chk);
    $price = $chk->partner_price;
    $pass = md5($chk->partner_name);
 
    if ($pass == $key) {
        $query = "UPDATE `tbl_local_parthners_orders` set confirm=" . $confirm . ",mtime=null WHERE order_id = '$order_id' and partner_id = '$partner_id'";
        $database->setQuery($query);
        $database->query();

        $order_status = ($confirm > 0) ? 'F' : 'L';
        $q = "UPDATE jos_vm_orders SET";
        $q .= " order_status='" . $order_status . "' ";
        $q .= "WHERE order_id='" . $order_id . "'";
        $database->setQuery($q);
        $database->query();

        $q = "INSERT INTO tbl_local_parthners_orders_history (order_id, partner_id, status, price, comments, time)";
        $q .= " VALUES ("
                . $order_id . ", " . $partner_id . ", '" . $order_status . "',  '" . $price . "',"
                . "'" . $comment . "', NOW() ) ";
        $database->setQuery($q);
        $database->query();
        
        $old_tz = date_default_timezone_get();
        date_default_timezone_set('Australia/Sydney');
        $mysqlDatetime = date("Y-m-d G:i:s");
        date_default_timezone_set($old_tz);
        
        $q = "INSERT INTO jos_vm_order_history ";
        $q .= "(order_id,order_status_code,warehouse,priority,date_added,customer_notified,warehouse_notified,comments) VALUES (";
        $q .= "'" . $order_id . "', '" . $order_status . "', '','', '".$mysqlDatetime."', '', '',"
                . " 'Order " . (($confirm > 0) ? 'confirmed' : 'declined' ) . " by Parthner " . ($chk->partner_name)
                . $comment . "')";
        $database->setQuery($q);
        $database->query();
        HTML_LocalPartner_Orders::viewPageFinal($confirm);
    } else {
        HTML_LocalPartner_Orders::viewPageFinal(0);
    }
}

?>
