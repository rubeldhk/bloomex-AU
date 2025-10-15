<?php

include ("../configuration.php");

if (!$mosConfig_host)
    die('no config');
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($mosConfig_db)) {
    die('Could not select database: ' . mysql_error());
}

$query = 'SELECT o.order_id,ui.zip 
    FROM jos_vm_orders o 
    LEFT JOIN jos_vm_order_history h on o.order_id=h.order_id 
    LEFT join jos_vm_order_user_info ui on o.order_id = ui.order_id 
    WHERE h.comments like "From frontend." AND ui.address_type = "ST" order by o.order_id desc"';


$sql = mysql_query($query);
$timestamp = time();
while ($out = mysql_fetch_array($sql)) {
    $while = 4;
    $need_zip_code = $out['zip'];
    $order_id = $out['order_id'];
    while ($while > 0) {
        $query = "SELECT WH.warehouse_email,"
                . " WH.warehouse_code FROM jos_vm_warehouse AS WH,"
                . " jos_postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code LIKE '" . $need_zip_code . "'";
        $sql = mysql_query($query);
        $oWarehouse = null;
        $oWarehouse = mysql_fetch_object($sql);
        if ($oWarehouse) {
            $warehouse_code = $oWarehouse->warehouse_code;
            $warehouse_email = $oWarehouse->warehouse_email;
            $while = 0;
            break;
        } else {
            $while--;
            $need_zip_code = substr($need_zip_code, 0, $while);
            if ($while == 0) {
                $while = -1;
            }
        }
    }

    if ($while == 0) {
        if ($warehouse_code == 'WH12') {
            $query = "UPDATE jos_vm_orders SET warehouse='" . $warehouse_code . "',color='black', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        } else {

            $query = "UPDATE jos_vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        }
        $database->setQuery($query);
        $database->query();

        if ($warehouse_code) {
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
            $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);

            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
        }
    } else {
        $query = "UPDATE jos_vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        $database->setQuery($query);
        $database->query();
    }
}
