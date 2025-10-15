<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* PayPal IPN Result Checker
*
* @version $Id: checkout.result.php,v 1.5.2.1 2006/03/10 15:55:15 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

if( !isset( $_REQUEST["order_id"] ) || empty( $_REQUEST["order_id"] )) {
	echo "Order ID is not set or emtpy!";
}
else {
	include( CLASSPATH. "payment/ps_paypal.cfg.php" );
	$order_id = intval( mosgetparam( $_REQUEST, "order_id" ));

	$q = "SELECT order_status FROM #__{vm}_orders WHERE ";
	$q .= "#__{vm}_orders.user_id= " . $auth["user_id"] . " ";
	$q .= "AND #__{vm}_orders.order_id= $order_id ";
	$db->query($q);
	if ($db->next_record()) {
		$order_status = $db->f("order_status");
		if($order_status == PAYPAL_VERIFIED_STATUS
      || $order_status == PAYPAL_PENDING_STATUS) {  ?> 
        <img src="<?php echo IMAGEURL ?>ps_image/button_ok.png" align="center" alt="Success" border="0" />
        <h2><?php echo $VM_LANG->_PHPSHOP_PAYPAL_THANKYOU ?></h2>
    
    <?php
      }
      else { ?>
        <img src="<?php echo IMAGEURL ?>ps_image/button_cancel.png" align="center" alt="Failure" border="0" />
        <span class="message"><?php echo $VM_LANG->_PHPSHOP_PAYPAL_ERROR ?></span>
    
    <?php
    } ?>
    <br />
     <p><a href="index.php?option=com_virtuemart&page=account.order_details&order_id=<?php echo $order_id ?>">
     <?php echo $VM_LANG->_PHPSHOP_ORDER_LINK ?></a>
     </p>


		
		$dbbt = new ps_DB;
		$dbod = new ps_DB;
		$dbx = new ps_DB;
		$dby = new ps_DB;
		$dbz = new ps_DB;
		$item_string ='';
		$tran_string ='';

//Get customer and order details based on order number

		$dbbt ->query("SELECT city, state, country, zip FROM #__{vm}_user_info WHERE user_id= '".$auth["user_id"]."'");
		$dbod ->query("SELECT order_total, order_tax, order_shipping FROM #__{vm}_orders WHERE order_id = $order_id ");

// Build Goole Analytics Transaction line

$tran_string = 'UTM:T|' .$order_id .  '|MRE Test|' . $dbod->f("order_total")  . '|' . $dbod->f("order_tax") . '|' . $dbod->f("order_shipping") . '|' . $dbbt->f("city"). '||' .$dbbt->f("country");

//Get Google Analytics Item line details

$db->query("SELECT order_item_sku,order_item_name, product_final_price, product_quantity FROM #__{vm}_order_item WHERE order_id='$order_id'");

	while ($db->next_record()) {

//Check if product has a parent id - otherwise category is blank

$dbx->query("SELECT product_id, product_parent_id FROM #__{vm}_product WHERE product_sku = '".$db->f("order_item_sku")."'"); 

$product_id = $dbx->f("product_parent_id"); 

IF ($dbx->f("product_parent_id") == "0") {
	 	$product_id = $dbx->f("product_id");
	 }

//Get product category info

$dby->query("SELECT category_id FROM #__{vm}_product_category_xref WHERE product_id = $product_id");
$dbz->query("SELECT category_name FROM #__{vm}_category WHERE category_id = '".$dby->f("category_id")."'"); 

//Build Google Analytics Item line 
$item_string .= ' UTM:I|' .$order_id. '|' . $db->f("order_item_sku"). '|' .$db->f("order_item_name") .'|'. $dbz->f("category_name")  .'|'. $db->f("product_final_price") .'|'. $db->f("product_quantity");

		}
//Add both google lines together
$analytic = $tran_string .' '. $item_string;

echo   '<!-- Google Code for Purchase Conversion Page -->
			<script language="JavaScript" type="text/javascript">
			<!--
			var google_conversion_id = "UA-232639";
			var google_conversion_language = "en_GB";
			var google_conversion_format = "1";
			var google_conversion_color = "666666";
			if ('.$order_total.') {
				var google_conversion_value = "'.$order_total.'";
			}
			var google_conversion_label = "Purchase";
			//-->
			</script>

			<script language="JavaScript" src="https://www.googleadservices.com/pagead/conversion.js"></script>

			<noscript>
				<img height=1 width=1 border=0 src="https://www.googleadservices.com/pagead/conversion/UA-232639/?value='.$order_total.'&label=Purchase&script=0">
			</noscript>';

//Post info to analytics
echo '<body onLoad="javascript:__utmSetTrans();">
      <form style="display:none;" name="utmform">
		<textarea id="utmtrans">'.$analytic.'</textarea></form>';

 <?php
	}
	else {
		echo "Order not found!";
	}
}


?>

