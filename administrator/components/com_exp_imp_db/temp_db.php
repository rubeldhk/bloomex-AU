<?
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
require_once( "virtuemart/virtuemart.cfg.php" );
require_once( "virtuemart/english.php" );
//require_once( "virtuemart/Log.php" );
require_once( "mailer.php" );
require ('../configuration.php');
require ( "conf.php" );

//----------- Setup Email ------------------
        $VM_LANG =  new vmAbstractLanguage_1(); 
        $aEmail = array();
        $sendmail->set(html, true);

global $database, $database_1;
$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
$database_1 =new database($mosConfig_host_1,$mosConfig_user_1,$mosConfig_password_1,$mosConfig_db_1,$mosConfig_dbprefix_1);

 $database->setQuery("SELECT last_time_export, name_partner_export FROM #__vm_order_export_time WHERE id_partner_export = '".$nPartnerOrderID."'");
 $tmp_partner = $database->loadObjectList();
 if (isset($tmp_partner[0]->name_partner_export))  { 
 /*$start_date = $tmp_partner[0]->last_time_export; //date("d-m-Y",strtotime($tmp_partner[0]->last_time_export));    
 if ($start_date > (date("Y-m-d G:i:s", $timestamp)) || $start_date == '0000-00-00 00:00:00' || $start_date == '')
 { $start_date = date("Y-m-d G:i:s", $timestamp); };
  */
 if (isset ($_GET['end_date'])) {$end_date = $_GET['end_date'];};
 
 if (isset($end_date) && (strtotime($end_date)!=false)) {
    print 'interval export '.$start_date.'    -      '.$end_date;
 $s_date = strtotime($start_date);
 $e_date = strtotime($end_date);
 
 $sPartnerName = $tmp_partner[0]->name_partner_export;

$database_1->setQuery("SELECT * FROM #__vm_orders
RIGHT JOIN #__vm_order_payment ON #__vm_order_payment.order_id = #__vm_orders.order_id
WHERE #__vm_orders.cdate BETWEEN '".$s_date."' AND '".$e_date."' "); //#__vm_orders.order_id = '1139687'
$vm_orders_id = $database_1->loadObjectList();

foreach($vm_orders_id as $orders_id) 
{  
    $order_number = md5("order" . $orders_id->user_id . time());    
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    //print $order_id = $orders_id->order_id.'<br>';
    
$database->setQuery("INSERT INTO #__vm_orders
( 
user_id, 
vendor_id, 
order_number, 
user_info_id, 
order_total, 
order_subtotal, 
order_tax, 
order_tax_details, 
order_shipping, 
order_shipping_tax, 
coupon_discount, 
order_currency, 
order_status, 
cdate, 
mdate, 
ddate,
ship_method_id, 
customer_note, 
customer_signature, 
customer_occasion, 
customer_comments, 
find_us,
username 
)VALUES(                     
'".$orders_id->user_id."', 
'".$orders_id->vendor_id."', 
'".$order_number."', 
'".$orders_id->user_info_id."', 
'".$orders_id->order_total."', 
'".$orders_id->order_subtotal."', 
'".$orders_id->order_tax."', 
'".$orders_id->order_tax_details."', 
'".$orders_id->order_shipping."', 
'".$orders_id->order_shipping_tax."', 
'".$orders_id->coupon_discount."', 
'".$orders_id->order_currency."', 
'".$orders_id->order_status."', 
'".time()."', 
'".time()."', 
'".$orders_id->ddate."',
'".$orders_id->ship_method_id."', 
'".$orders_id->customer_note."', 
'".$orders_id->customer_signature."', 
'".$orders_id->customer_occasion."', 
'".$orders_id->customer_comments."', 
'".$orders_id->find_us."',
'".$orders_id->username."');
    ");
$database->query();
$order_id_now = $database->insertid();

$database->setQuery("INSERT INTO #__vm_order_payment(
order_id, 
order_payment_code, 
payment_method_id, 
order_payment_number, 
order_payment_expire, 
order_payment_log, 
order_payment_name, 
order_payment_trans_id
) VALUES (
'".$order_id_now."', 
'".$orders_id->order_payment_code."',
'3', 
'".$orders_id->order_payment_number."', 
'".$orders_id->order_payment_expire."', 
'".$orders_id->order_payment_log."', 
'".$orders_id->order_payment_name."',	
'".$orders_id->order_payment_trans_id."');
");
$database->query();

$database->setQuery("INSERT INTO #__vm_order_history (
order_id,
order_status_code,
date_added,
customer_notified,
comments
) VALUES (
'".$order_id_now."', 
'P', 
'".$mysqlDatetime."', 
'1', 
'');
");
$database->query();

$database->setQuery("INSERT INTO tbl_xmlorder
  ( partner_order_id, 
    order_id, 
    partner_name, 
    created_date 
    ) VALUES (
    '".$nPartnerOrderID."', 
    '".$order_id_now."',
    '".$sPartnerName."',
    '".date("Y-m-d H:i:s")."');
   ");
$database->query();

$database_1->setQuery("SELECT * FROM #__vm_user_info 
WHERE user_id = '".$orders_id->user_id."'"); 
$vm_orders_user = $database_1->loadObjectList();
   
foreach($vm_orders_user as $orders_user) 
        {  
$database->setQuery("INSERT INTO #__vm_order_user_info (  
order_id, 
user_id, 
address_type, 
address_type_name, 
company, 
last_name, 
first_name, 
middle_name, 
phone_1, 
phone_2,
fax, 
address_1, 
address_2, 
city, 
state, 
country, 
zip,
user_email 
)VALUES(
'".$order_id_now."', 
'".$orders_user->user_id."',
'".$orders_user->address_type."',
'".$orders_user->address_type_name."',
'".$orders_user->company."',
'".$orders_user->last_name."',
'".$orders_user->first_name."',
'".$orders_user->middle_name."',
'".$orders_user->phone_1."',
'".$orders_user->phone_2."',
'".$orders_user->fax."',
'".$orders_user->address_1."',
'".$orders_user->address_2."',
'".$orders_user->city."',
'".$orders_user->state."',
'".$orders_user->country."',
'".$orders_user->zip."',
'".$orders_user->user_email."');
");
$database->query();
        };
 
    $database_1->setQuery("SELECT * FROM #__vm_order_item 
RIGHT JOIN #__vm_product ON #__vm_product.product_id = #__vm_order_item.product_id
RIGHT JOIN  #__vm_product_category_xref ON #__vm_product_category_xref.product_id = #__vm_order_item.product_id
WHERE #__vm_order_item.order_id = '".$orders_id->order_id."' GROUP BY #__vm_order_item.product_id"); 
$vm_orders = $database_1->loadObjectList();

$k = 0;
$$html_ord_items = array();
    foreach($vm_orders as $orders) 
        {  
$database->setQuery("INSERT INTO #__vm_order_item (   
order_id, 
user_info_id, 
vendor_id, 
product_id, 
order_item_sku, 
order_item_name, 
product_quantity, 
product_item_price, 
product_final_price, 
order_item_currency, 
order_status, 
product_attribute, 
cdate, 
mdate 
)VALUES( 
'".$order_id_now."',  
'".$orders_user->user_info_id."', 
'".$orders->vendor_id."', 
'".$orders->product_id."', 
'".$orders->order_item_sku."', 
'".$orders->order_item_name."', 
'".$orders->product_quantity."', 
'".$orders->product_item_price."', 
'".$orders->product_final_price."', 
'".$orders->order_item_currency."', 
'P', 
'".$orders->product_attribute."', 
'".$orders->cdate."', 
'".$orders->mdate."');
");
$database->query();

$database->setQuery("INSERT INTO #__vm_order_product_type( 
order_id, 
product_id, 
product_type_name, 
quantity, 
price
)VALUES ( 
'".$order_id_now."',
'".$orders->product_id."', 
'".$orders->product_type_name."', 
'".$orders->quantity."', 
'".$orders->price."');
");
$database->query();

$$html_ord_items[$k] = $orders->order_item_name.'  '.$orders->order_item_sku. '  '. $orders->product_quantity.'  '.$orders->product_final_price ;
      $k++;  
        };
        
        $deliver_zip_code = $zip_cod_e; // 	then delete
        $query =   "SELECT WH.warehouse_email, WH.warehouse_code FROM jos_vm_warehouse AS WH, 
                   jos_postcode_warehouse AS PWH 
                   WHERE WH.warehouse_id = PWH.warehouse_id 
                   AND 
                   PWH.postal_code = '".$deliver_zip_code."'"; 
        $oWarehouse = $database->loadObjectList($database->setQuery($query), 'MYSQL_ASSOC');  
        $warehouse_code = $oWarehouse[0]->warehouse_code;
        $warehouse_email = $oWarehouse[0]->warehouse_email;
    
        $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id_now . "'";
        $database->query($query);
        
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY;
if ($warehouse_code) {
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id_now;
            $mail_Content = str_replace('{order_id}', $order_id_now, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
            $aEmail['to'] = $warehouse_email;
            $aEmail['from'] = $mosConfig_mailfrom;
            $aEmail['subject'] = $mail_Subject;
            $aEmail['body'] = $mail_Content;
            $sendmail->getParams($aEmail);
            $sendmail->setHeaders();
            $sendmail->send();
                    }

        $query = "SELECT * FROM jos_vm_creditcard WHERE creditcard_code = '".$orders_id->order_payment_code."'";
        $payment_info_details = $database->loadObjectList($database->setQuery($query), 'MYSQL_ASSOC');
        $payment_info_details .= '<br />Name On Card: ' . $payment_info_details->creditcard_name . '<br />'
                . 'Credit Card Number: ' . $credit_card_number . '<br />'
                . 'Expiration Date: ' . $expire_month . ' / ' . $expire_year . '<br />';

        $shopper_header = 'Thank you for shopping with us.  Your order information follows.';
        $shopper_order_link = $mosConfig_live_site . "/index.php?page=account.order_details&order_id=$order_id_now";
        $shopper_footer_html = "<br /><br />Thank you for your patronage.<br />"
                . "<br /><a title=\"View the order by following the link below.\" href=\"$shopper_order_link\">View the order by following the link below.</a>"
                . "<br /><br />Questions? Problems?<br />"
                . "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom . "\">" . $mosConfig_mailfrom . "</a>";

        $vendor_header = "The following order was received.";
        $vendor_order_link = $mosConfig_live_site . "/index.php?page=order.order_print&order_id=$order_id_now&pshop_mode=admin";
        $vendor_footer_html = "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";

        $database->setQuery("SELECT * FROM #__vm_vendor WHERE vendor_id = '".$orders_id->vendor_id."'");
        $vendor_details = $database->loadObjectList();
   
        $vendor_image = "<img src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/vendor/" . $vendor_details[0]->vendor_full_image . "\" alt=\"vendor_image\" border=\"0\" />";
        $html = str_replace('{phpShopVendorName}', $vendor_details[0]->vendor_name, $html);
        $html = str_replace('{phpShopVendorStreet1}', $vendor_details[0]->vendor_phone, $html);
        $html = str_replace('{phpShopVendorStreet2}', $vendor_details[0]->vendor_address_1, $html);
        $html = str_replace('{phpShopVendorZip}', $vendor_details[0]->vendor_zip, $html);
        $html = str_replace('{phpShopVendorCity}', $vendor_details[0]->vendor_city, $html);
        $html = str_replace('{phpShopVendorState}', $vendor_details[0]->vendor_state, $html);
        $html = str_replace('{phpShopVendorImage}', $vendor_image, $html);
        $html = str_replace('{phpShopOrderNumber}', $order_id_now, $html);
        $html = str_replace('{phpShopOrderDate}', date("M d, Y", $timestamp), $html);
        $html = str_replace('{phpShopDeliveryDate}', $orders_id->ddate, $html);
        $html = str_replace('{phpShopOrderStatus}', $orders_id->order_payment_log, $html);
        
        $html = str_replace('{phpShopBTCompany}', $vm_orders_user[0]->company, $html);
        $html = str_replace('{phpShopBTName}', $vm_orders_user[0]->first_name . " " . $vm_orders_user[1]->middle_name . " " . $vm_orders_user[1]->last_name, $html);
        $html = str_replace('{phpShopBTStreet1}', $vm_orders_user[0]->address_1, $html);
        $html = str_replace('{phpShopBTStreet2}', $vm_orders_user[0]->address_2, $html);
        $html = str_replace('{phpShopBTCity}', $vm_orders_user[0]->city, $html);
        $html = str_replace('{phpShopBTState}', $vm_orders_user[0]->state, $html);
        $html = str_replace('{phpShopBTZip}', $vm_orders_user[0]->zip, $html);
        $html = str_replace('{phpShopBTCountry}', $vm_orders_user[0]->country, $html);
        $html = str_replace('{phpShopBTPhone}', $vm_orders_user[0]->phone_1, $html);
        $html = str_replace('{phpShopBTFax}', $vm_orders_user[0]->fax, $html);
        $html = str_replace('{phpShopBTEmail}', $vm_orders_user[0]->user_email, $html);
        
        $html = str_replace('{phpShopSTCompany}', $vm_orders_user[1]->company, $html);
        $html = str_replace('{phpShopSTName}', $vm_orders_user[1]->first_name . " " . $vm_orders_user[1]->middle_name . " " . $vm_orders_user[1]->last_name, $html);
        $html = str_replace('{phpShopSTStreet1}', $vm_orders_user[1]->address_1, $html);
        $html = str_replace('{phpShopSTStreet2}', $vm_orders_user[1]->address_2, $html);
        $html = str_replace('{phpShopSTCity}', $vm_orders_user[1]->city, $html);
        $html = str_replace('{phpShopSTState}', $vm_orders_user[1]->state, $html);
        $html = str_replace('{phpShopSTZip}', $vm_orders_user[1]->zip, $html);
        $html = str_replace('{phpShopSTCountry}', $vm_orders_user[1]->country, $html);
        $html = str_replace('{phpShopSTPhone}', $vm_orders_user[1]->phone_1, $html);
        $html = str_replace('{phpShopSTFax}', $vm_orders_user[1]->fax, $html);
        $html = str_replace('{phpShopSTEmail}', "", $html);
        
        /*
        foreach($html_ord_items as $ord_items) 
        {  
        $html_items .= $ord_items.'<br>';
        };*/
        $html = str_replace('{phpShopOrderItems}', $html_items, $html);
       
        $html = str_replace('{phpShopOrderSubtotal}', "$" . number_format($orders_id->order_subtotal, 2, '.', ' '), $html);
        $html = str_replace('{phpShopOrderShipping}', "$" . number_format($orders_id->order_shipping, 2, '.', ' '), $html);
        $html = str_replace('{phpShopOrderTax}', "$" . number_format($orders_id->order_tax, 2, '.', ' '), $html);
        $html = str_replace('{phpShopOrderTotal}', "$" . number_format($orders_id->order_total, 2, '.', ' '), $html);

        $html = str_replace('{phpShopOrderDisc1}', (isset($orders_id->coupon_discount) ? $orders_id->coupon_discount : ""), $html);
        $html = str_replace('{phpShopOrderDisc2}', (isset($orders_id->order_discount) ? $orders_id->order_discount : ""), $html);
        $html = str_replace('{phpShopCustomerNote}', htmlspecialchars(strip_tags($orders_id->customer_note)), $html);
        $html = str_replace('{phpShopCustomerSignature}', htmlspecialchars(strip_tags($orders_id->customer_signature)), $html);
        $html = str_replace('{phpShopCustomerInstructions}', htmlspecialchars(strip_tags($orders_id->customer_comments)), $html);
        $html = str_replace('{PAYMENT_INFO_LBL}', "Payment Information", $html);
        $html = str_replace('{PAYMENT_INFO_DETAILS}', $orders_id->order_payment_log, $html);
        $html = str_replace('{SHIPPING_INFO_LBL}', "Delivery Information", $html);
        
        $rows_pay = str_replace('_', ' ', $orders_id->order_payment_log);
        $order_payment = explode('|',$rows_pay);
        $html = str_replace('{SHIPPING_INFO_DETAILS}', $order_payment[0] . " (" . $order_payment[2] . ")", $html);
        
        $shopper_html = str_replace('{phpShopOrderHeaderMsg}', $shopper_header, $html);
        $shopper_html = str_replace('{phpShopOrderClosingMsg}', $shopper_footer_html, $shopper_html);
        $shopper_subject = $aVendor->vendor_name . " Purchase Order - " . $order_id_now;
        
        $aEmail['to'] = $vm_orders_user[0]->user_email;
        $aEmail['from'] = $mosConfig_mailfrom;
        $aEmail['subject'] = $shopper_subject;
        $aEmail['body'] = $shopper_html;
        $sendmail->getParams($aEmail);
        $sendmail->setHeaders();
        $sendmail->send();
};
 
} else {print "Selected not correct end date of the period.";};

$last_time_export = date('Y-m-d G:i:s', $timestamp);
$database->setQuery("UPDATE #__vm_order_export_time 
SET last_time_export = '".$last_time_export."'
WHERE id_partner_export = '".$nPartnerOrderID."'
");
$database->query();

} else {print "Such a partner in the database has not.";}
?>
