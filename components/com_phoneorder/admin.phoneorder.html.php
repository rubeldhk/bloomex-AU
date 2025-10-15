<?php
/**
* @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
global  $mosConfig_absolute_path;
/*require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_database.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_html.php" );*/
/**
* @package Joomla
* @subpackage Category
*/
class HTML_PhoneOrder {
	
	//============================================= PHONE ORDER ===============================================
	function savePhoneOrderSuccess( $option ) {
?>	
	<style type="text/css">		
		a.place-link:link, a.place-link:visited {
			font:bold 12px Tahoma, Verdana; 
			text-transform:uppercase;
			text-decoration:none;
			color:blue; 
		}
		
		a.place-link:hover {
			text-decoration:underline;
			font-style:italic;
		}
	</style>
	<p>&nbsp;</p>
	<a href="index2.php?option=<?php echo $option?>" class="place-link">Place New Order</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
	<a href="index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart" class="place-link">Check Order list</a>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
<?php
	}	
	
	
	
	function makePhoneOrder( $option, $aInfomation ) {
		global $mosConfig_live_site;		
		
	?>
		<script src="<?php echo $mosConfig_live_site."/administrator/components/com_phoneorder/jquery.js"?>" > </script>
		<script src="<?php echo $mosConfig_live_site."/administrator/components/com_phoneorder/jquery.autocomplete.js"?>" > </script>
		<style type="text/css">		
			table.product-list tr.header td {
				font:bold 12px Tahoma, Verdana;
				color:#993333;
			}
			
			table.product-list tr td  table.product-item{
				border:1px solid #CCC;
				margin-top:5px;
			}
			
			table.product-list tr td  table.product-item td{
				font:normal 12px Tahoma, Verdana;
			}
			
			table.product-list tr td  table.product-item td a{
				font:normal 11px Tahoma, Verdana;
			}
			
			table.product-list tr td  table.product-item td.price {
				color:#FF0000;
				font-weight:bold;
			}
			
			table.product-list tr td  table.product-item td.product-name {
				color:#0000FF;
				font-weight:bold;
			}
			
			table.product-list td.price2, div.extra-fee, td.calculate-price {
				font:bold 12px Tahoma, Verdana;
				color:#FF0000;
				text-align:left;
			}
			
			div.extra-fee span {
				font:normal 12px Tahoma, Verdana;
				line-height:160%;
			}
						
			table.product-list td.title2 {
				font:bold 12px Tahoma, Verdana;
				text-align:right;
				color:#0000FF;				
			}
						
			input.btn {
				font:bold 12px Tahoma, Verdana;
				cursor:pointer;
				margin-left:10px;
				padding:3px;
			}	
			
			input.btn2 {
				font:bold 12px Tahoma, Verdana;
				cursor:pointer;
				padding:2px;
			}	
			
			select {
				font:normal 12px Tahoma, Verdana;
				padding:3px;
			}
			
			select.cbo-product {
				margin-left:10px;
			}
			
			table.billing-info {
				border:1px solid #CCC;
				margin-top:10px;
			}
			
			table.billing-info td.header {
				font:bold 13px Tahoma, Verdana;
				border-bottom: 1px solid #993333;;
				text-transform:uppercase;				
				text-align:center;
				color:#993333;
				padding:7px;
			}
			
			table.billing-info td.header2 {
				font:bold 12px Tahoma, Verdana;
				color:#993333;
				padding:7px;
			}
			
			table.billing-info td.title{
				font:bold 11px Tahoma, Verdana;
				padding:5px 5px 5px 15px;
				vertical-align:top;
			}
			
			div.error-msg {
				font:bold 11px Tahoma, Verdana;
				color:#FF0000;
				padding:5px;
			}
			
			td.notice {
				font:bold 12px Tahoma, Verdana;
				text-indent:15px;
				color:#FF6600;
			}
			
			td.cut-off-time {
				font:normal 12px Tahoma, Verdana;				
				padding:5px;
			}
			
			td.cut-off-time span{
				color:#FF0000;
				padding-left:10px;
			}
			
			div#deliver-address-default {
				font:normal 11px Tahoma, Verdana;				
				padding:0px 10px 0px 20px;
				line-height:20px;
			}			
			
			div.msgReport {
				font:bold 11px Tahoma, Verdana;
				margin:20px 0px 0px 0px;
				text-align:left;
				display:block;
				color:#3366FF;
				
			}
			
			div.before-check-account {
				font:bold 12px Tahoma, Verdana;
				margin:20px 0px 40px 0px;
				text-align:center;
				display:block;
				color:#3366FF;
				
			}
			
			div.after-check-account {
				display:none;
			}
			
			.ac_results {
				padding: 0px;
				border: 1px solid black;
				background-color: white;
				overflow: hidden;
				z-index: 99999;
			}
			
			.ac_results ul {
				width: 100%;
				list-style-position: outside;
				list-style: none;
				padding: 0;
				margin: 0;
			}
			
			.ac_results li {
				margin: 0px;
				padding: 2px 5px;
				cursor: default;
				display: block;
				/* 
				if width will be 100% horizontal scrollbar will apear 
				when scroll mode will be used
				*/
				/*width: 100%;*/
				font: menu;
				font-size: 12px;
				/* 
				it is very important, if line-height not setted or setted 
				in relative units scroll will be broken in firefox
				*/
				line-height: 16px;
				overflow: hidden;
			}
			
			.ac_results li strong{
				color:red;
			}
						
			.ac_loading {
				background: white url('indicator.gif') right center no-repeat;
			}
			
			.ac_odd {
				background-color: #eee;
			}
			
			.ac_over {
				background-color: #0A246A;
				color: white;
			}
			
			#selectProductId {
				font:bold 12px Tahoma, Verdana;				
				padding:4px 2px 4px 5px;
			}

		</style>

		<div id="product-item-default" style="display:none">
			<div id="product-item-{noItem}">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="product-item">
					<tr>
						<td width="5%" style="text-align:center;"><strong>{noItem}.</strong></td>
						<td width="50%" class="product-name">{item-name}<input type="hidden" value="{item-id}" id="product-id-item-{noItem}" name="product_id[]"/></td>
						<td width="10%" class="price">{item-price}</td>
						<td width="10%" class="price">{item-tax}</td>
						<td width="10%"><input type="text" size="7" maxlength="3" name="quantity[]" id="quantity-item-{noItem}" value="1" onblur="checkNumberProduct(this.value, {real-price}, {real-tax}, {noItem});" onfocus="saveNumberProduct(this.value);"/></td>
						<td width="10%" class="price" id="item-subtotal-{noItem}">{item-subtotal}</td>
						<td width="5%" id="deleteActionLink"><a href="#" onclick="deleteItem( {noItem}, {real-price}, {real-tax} );return false;" id="deleteItem" >Delete</a></td>
					</tr>
				</table>
			</div>
		</div>
		
		<div id="deliver-address-item" style="display:none">
			<input type="radio" name="deliver_address_item" value="{value}" {status} />{text}<br/>
		</div>
		
		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			Phone Order Manager:
			<small>Add New</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Phone Order Information Detail</th>
			<tr>	
			
			<tr>
				<td colspan="2">
					<table width="99%" cellpadding="5" cellspacing="0" border="0" class="product-list" align="center">
						<tr class="header">
							<td width="5%" style="text-align:center;">No</td>
							<td width="50%">Product Name</td>
							<td width="10%">Price</td>
							<td width="10%">Tax</td>
							<td width="10%">Quantity</td>
							<td width="10%">Subtotal</td>
							<td width="5%">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="7" id="product-list-items" style="text-align:center;"></td>
						</tr>
						<tr>
							<td colspan="5" class="title2">Total:</td>
							<td colspan="2" class="price2" id="total-price">$0.00</td>							
						</tr>
					</table>
				</td>
			</tr>
			
			<!--===================================================================-->			
			
			<tr>
				<td colspan="2">
					<div class="error-msg" >Please add all product items of order before update quantity!</div>
					<?php ///echo $aInfomation['cboProduct']; ?>
					&nbsp;<input type="text" id="selectProductId" size="100" autocomplete="off" />					
					<input type="hidden" id="select_product_id" value="" />		
					<input type="button" value="Add Product" id="addProductItem" class="btn" />					
				</td>
			</tr>
			
			<tr>
				<td width="50%" valign="top">
					<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">						
						<tr>
							<td colspan="2" class="header" style="text-align:center;">Billing Information</td>
						</tr>
						<tr>
							<td colspan="2" class="header2">Account Information</td>
						</tr>
						<tr>
							<td width="30%" class="title">Username<font color="red">*</font>:</td>
							<td width="70%"><input type="text" name="user_name" value="" size="30" /></td>
						</tr>
						<!--<tr>
							<td class="title">Password<font color="red">*</font>:</td>
							<td><input type="text" name="pass" value="" size="30" /></td>
						</tr>
						<tr>
							<td class="title">Confirm Password<font color="red">*</font>:</td>
							<td><input type="text" name="pass_confirm" value="" size="30" /></td>
						</tr>-->
						<tr>
							<td class="title">E-mail<font color="red">*</font>:</td>
							<td><input type="text" name="account_email" value="" size="30" /></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td>
								<div class="error-msg" id="error-report">Please enter email to find exist info in your system first!<br/>If your account isn't exist, you must create an new account.</div>
								<input type="button" value="Check Account Info" id="checkAccInfo" class="btn2" />&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="button" value="Create New Account" id="createAccInfo" class="btn2" />&nbsp;&nbsp;&nbsp;&nbsp;
								<!--<input type="button" value="Copy to Deliver Info" id="copyInfo" class="btn2" />-->
							</td>
						</tr>
					</table>
					<div class="after-check-account">
						<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">	
							<tr>
								<td colspan="2" class="header2">Billing Information</td>
							</tr>
							<tr>
								<td width="30%" class="title">Company Name Title </td>
								<td width="70%"><input type="text" name="bill_company_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">First Name<font color="red">*</font>:</td>
								<td><input type="text" name="bill_first_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Last Name<font color="red">*</font>:</td>
								<td><input type="text" name="bill_last_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Middle Name:</td>
								<td><input type="text" name="bill_middle_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Address 1<font color="red">*</font>:</td>
								<td><input type="text" name="bill_address_1" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Address 2:</td>
								<td><input type="text" name="bill_address_2" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">City<font color="red">*</font>:</td>
								<td><input type="text" name="bill_city" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Zip Code / Postal Code<font color="red">*</font>:</td>
								<td>
									<input type="text" name="bill_zip_code" value="" size="30" maxlength="10" /><br>
									Example: <b>K2E 7X3 (LDL DLD . Where L= letter , D = number)</b>
								</td>
							</tr>
							<tr>
								<td class="title">Country<font color="red">*</font>:</td>
								<td><?php echo $aInfomation['bill_country']; ?></td>
							</tr>
							<tr>
								<td class="title">State/Province/Region<font color="red">*</font>:</td>
								<td><div id="bill_state_container"><?php echo $aInfomation['bill_state']; ?></div></td>
							</tr>
							<tr>
								<td class="title">Phone<font color="red">*</font>:</td>
								<td><input type="text" name="bill_phone" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Evening Phone:</td>
								<td><input type="text" name="bill_evening_phone" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Fax:</td>
								<td><input type="text" name="bill_fax" value="" size="30" /></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">						
							<tr>
								<td colspan="2" class="header">Coupon Discount Code</td>
							</tr>
							<tr>
								<td class="title" width="30%">Your code:</td>
								<td width="70%">
									<input type="text" name="coupon_discount_code" value="" size="32" />
									<input type="hidden" name="coupon_discount_code_value" value="" />
									<input type="hidden" name="coupon_discount_code_type" value="" />							
									<input type="hidden" name="coupon_discount_price" value="" />							
								</td>
							</tr>
							<tr>
								<td class="title">&nbsp;</td>
								<td>
									<div class="msgReport" id="couponCode" style="display:none;text-align:left;padding:0px 0px 6px 0px;margin:0px;">&nbsp;</div>
									<input type="button" value="Check Coupon Code" id="checkCouponCode" class="btn2" />
								</td>
							</tr>
						</table>	
						<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">						
							<tr>
								<td colspan="2" class="header">Payment Method</td>
							</tr>
							<td colspan="2" class="title">
								<input name="payment_method_state" id="Visa, Master Card, American Express" value="live" checked="checked" type="radio"><label for="Visa, Master Card, American Express">Visa, Master Card, American Express</label><br>
								<input name="payment_method_state" id="Select this option ONLY if your credit card was declined" value="offline" type="radio"><label for="Select this option ONLY if your credit card was declined">Select this option ONLY if your credit card was declined</label>
							</td>
							<tr>
								<td class="title" width="30%" >Credit Card Type<font color="red">*</font>:</td>
								<td width="70%" ><?php echo $aInfomation['payment_method'];?></td>
							</tr>
							<tr>
								<td class="title">Name On Card<font color="red">*</font>:</td>
								<td><input type="text" name="name_on_card" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Credit Card Number<font color="red">*</font>:</td>
								<td><input type="text" name="credit_card_number" value="" size="30" maxlength="16" /></td>
							</tr>
							<tr>
								<td class="title">Credit Card Security Code<font color="red">*</font>:</td>
								<td><input type="text" name="credit_card_security_code" value="" size="30" maxlength="3" /></td>
							</tr>
							<tr>
								<td class="title">Expiration Date<font color="red">*</font>:</td>
								<td><?php echo $aInfomation['expire_month']; ?>&nbsp;/&nbsp;<?php echo $aInfomation['expire_year']; ?></td>
							</tr>
						</table>	
					</div>				
				</td>
				<td width="50%" valign="top">
					<div class="after-check-account">
						<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">						
							<tr>
								<td colspan="2" class="header">Deliver Information</td>
							</tr>
							<tr>
								<td colspan="2" class="header2"><input type="radio" name="exist_address_deliver" value="0"/> Please choose any exist deliver information below:</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="deliver-address-default"><div class="error-msg">None</div></div>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="header2"><input type="radio" name="exist_address_deliver" value="1" checked/> Or add new deliver information below:</td>
							</tr>
							<tr>
								<td width="30%" class="title">Address Nickname<font color="red">*</font>:</td>
								<td width="70%"><input type="text" name="address_user_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">First Name<font color="red">*</font>:</td>
								<td><input type="text" name="deliver_first_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Last Name<font color="red">*</font>:</td>
								<td><input type="text" name="deliver_last_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Middle Name:</td>
								<td><input type="text" name="deliver_middle_name" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Address 1<font color="red">*</font>:</td>
								<td><input type="text" name="deliver_address_1" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Address 2:</td>
								<td><input type="text" name="deliver_address_2" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">City<font color="red">*</font>:</td>
								<td><input type="text" name="deliver_city" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Zip Code / Postal Code<font color="red">*</font>:</td>
								<td>
									<input type="text" name="deliver_zip_code" value="" size="30" maxlength="10" /><br>
									Example: <b>K2E 7X3 (LDL DLD . Where L= letter , D = number)</b>									
								</td>
							</tr>
							<tr>
								<td class="title">Country<font color="red">*</font>:</td>
								<td><?php echo $aInfomation['deliver_country']; ?></td>
							</tr>
							<tr>
								<td class="title">State/Province/Region<font color="red">*</font>:</td>
								<td><div id="deliver_state_container"><?php echo $aInfomation['deliver_state']; ?></div></td>
							</tr>
							<tr>
								<td class="title">Phone<font color="red">*</font>:</td>
								<td><input type="text" name="deliver_phone" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Evening Phone:</td>
								<td><input type="text" name="deliver_evening_phone" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Cell Phone:</td>
								<td><input type="text" name="deliver_cell_phone" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Fax:</td>
								<td><input type="text" name="deliver_fax" value="" size="30" /></td>
							</tr>
							<tr>
								<td class="title">Recipient's Email Address:</td>
								<td><input type="text" name="deliver_recipient_email" value="" size="30" /></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">						
							<tr>
								<td colspan="2" class="header">Delivery Options</td>
							</tr>
							<tr>
								<td class="title" width="30%" >Occasion:</td>
								<td width="70%" ><?php echo $aInfomation['occasion'];?></td>
							</tr>
							<tr>
								<td class="title">Delivery Method:</td>
								<td><?php echo $aInfomation['shipping_method'];?></td>
							</tr>
							<tr>
								<td colspan="2" class="cut-off-time">
								<?php 
					               	printf( $aInfomation['DELIVERY_DATE'], $aInfomation['time'], date("h:i A") ); 
				                ?>
								</td>
							</tr>
							<tr>
								<td class="title">Deliver Date:</td>
								<td>
									<?php echo $aInfomation['delivery_day']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$aInfomation['delivery_month'];?>
								</td>
							</tr>
							<tr>
								<td class="title">Deliver Extra Fee:</td>
								<td>							
									<div id='deliver_extra' class="extra-fee">$0.00</div>
								</td>
							</tr>
							<tr>
								<td class="title">Card Message:</td>
								<td><textarea name="card_msg" cols="45" rows="3"></textarea></td>
							</tr>
							<tr>
								<td class="title">Signature:</td>
								<td><textarea name="signature" cols="45" rows="3"></textarea></td>
							</tr>
							<tr>
								<td class="title">Instructions and comments:</td>
								<td><textarea name="card_comment" cols="45" rows="3"></textarea></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="before-check-account">
						Please "Check Account Info" or "Create New Account" to continue
					</div>
					<div class="after-check-account">
						<table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">						
							<tr>
								<td colspan="2" class="header">Order Price Detail</td>
							</tr>
							<tr>
								<td colspan="2" class="notice">Please choose products, fill all Billing Info and Shipping Info, Coupon Discount before Calculate Order Detail</td>
							</tr>
							<tr>
								<td class="title" width="20%" >Total Items Price: </td>
								<td width="80%" class="calculate-price" id="calcualte-total-items-price">N/A</td>
							</tr>
							<tr>
								<td class="title">Deliver Fee:</td>
								<td class="calculate-price" id="calcualte-deliver-fee">N/A</td>
							</tr>
							<tr>
								<td class="title">Deliver Extra Fee:</td>
								<td class="calculate-price" id="calcualte-extra-deliver-fee">N/A</td>
							</tr>
							<tr>
								<td class="title">Total Deliver Fee:</td>
								<td class="calculate-price" id="calcualte-total-deliver-fee">N/A</td>
							</tr>
							<tr>
								<td class="title">Discount Price:</td>
								<td class="calculate-price" id="calcualte-discount-price">N/A</td>
							</tr>
							<tr>
								<td class="title">Total Price: </td>
								<td class="calculate-price" id="calcualte-total-price">N/A</td>
							</tr>
							<tr>
								<td class="title"></td>
								<td>
									<div class="msgReport" id="msgCheckoutReport" style="text-align:left;">&nbsp;</div><br/>
									<input type="button" value="Calculate Order Price" id="calculateOrderPrice" class="btn2" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="button" value="Save Order" id="saveOrder" class="btn2" />
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>	
		</table>
		
		<script language="javascript" type="text/javascript">
			<!--
            //============================================= DELIVER FUNCTIONS =============================================	
            oForm							= document.adminForm;
            var nDeliverExtraDay			= 0;
            var nDeliverExtraFee			= 0;
            var nDeliverFee					= 0;
            var nSpecialDeliverExtraFee		= 0;
            var nPostalCodeDeliverExtraFee	= 0;
            var nZipCheckedValue			= 0; 
       		var nExtraDayMonth 				= 0;   
   			var nDaysValid					= 0;        			
   			var nMonthsValid				= 0;   			
   			var bDeliverZipCode				= 0;
   			var bExistDeliverAddress		= 1;
   			var	sZipCode					= "";
   			
   			
   			var aOption 					= new Array();     
   			var aUnAvailableDate			= new Array();     
   			var aUnAvailableItem			= new Array();  
   			
   			
   			var nMinuteNow					= <?php echo $aInfomation['minute_now']; ?>;
   			var nHourNow					= <?php echo $aInfomation['hour_now']; ?>;
   			var nDayNow						= <?php echo $aInfomation['day_now']; ?>;
            var nDaysOfMonthNow				= <?php echo $aInfomation['days_of_month_now']; ?>;
            var nMonthNow					= <?php echo $aInfomation['month_now']; ?>;
            var bCutOffTime					= <?php echo $aInfomation['cut_off_time']; ?>;   
       		var nIndex						= parseFloat(nDayNow);      
       		var sPostalCodeDeliver			= "<?php echo $aInfomation['postal_code_deliver']; ?>";
       		var aPostalCodeDeliver			= sPostalCodeDeliver.split("[--1--]");
       		var sUnAvailableDate			= "<?php echo $aInfomation['unavailable_date']; ?>";
   			var sSpecialDeliver				= "<?php echo $aInfomation['special_deliver']; ?>";
   			var nDeliverExtraFeeForSameDay	= "<?php echo $aInfomation['option_param'][2]; ?>";         		          			
   			var sDeliverMethodFee			= "<?php echo $aInfomation['shipping_method_list_fee']; ?>";         		          			
   			var sDeliverZipCode				= jQuery.trim($("input[name='deliver_zip_code']").val());   
   			      		          			
   			var sDeliverFeeExtraSameDay		= " <span>(Deliver extra fee for the same day)</span>"; 
   			var sSpecialDeliverExtraFee 	= " <span>(Deliver extra fee for the special day)</span>";
   			var sPostalCodeDeliverExtraFee	= " <span>(Deliver extra fee follow postal code)</span>";
   			var sDeliverFeeTax				= " (With deliver fee: ";
   			var sUnAvailableDateText		= " - UnAvailable Date  ";
   			var sUnAvailableDateMsg			= "Your deliver date is not incorrect!";   			
   			var products 					= [ <?php echo $aInfomation['cboProduct']; ?> ];
		
   			
   			function findValueCallback(event, data, formatted) {
   				$("#select_product_id").val(data.value_id);
			}
			
			
			$("#selectProductId").autocomplete(products, {
				matchContains: true,
				scrollHeight: 250,
				minChars: 0,
				max: 100,
				formatItem: function(row, i, max) {
					return row.text;
				},
				formatResult: function(row) {
					return row.text;
				}
			}).result(findValueCallback);
			
					
			
			$("#checkCouponCode").click(function() {	
				if( jQuery.trim($("input[name='coupon_discount_code']").val()) == "" ) {					
					$("input[name='coupon_discount_code_value']").val( "" );
					$("input[name='coupon_discount_code_type']").val( "" );
					$("#couponCode").css('display', 'block'); 
					$("#couponCode").html( '<font color="red">Please enter your coupon code!</font>' ); 
					//alert("Please enter your coupon code!");
					return;
				}
								
				$.post( "index2.php",
					{ 	option: 					"com_phoneorder", 
						task: 						"check_counpon_code",
						coupon_discount_code: 		$("input[name='coupon_discount_code']").val(), 
						ajaxSend: function(){
							$("#couponCode").css('display', 'block'); 
							$("#couponCode").html('<img src="components/com_virtuemart/html/jquery_ajax.gif" align="absmiddle"/>&nbsp;&nbsp;Coupon Code is checking...'); 
						}
					},			
					function(data){
						aData	= data.split("[--1--]");
						if( jQuery.trim( aData[0] ) == "success" ) {	
							$("#couponCode").html( aData[1] );						
							$("input[name='coupon_discount_code_value']").val(aData[2]);
							$("input[name='coupon_discount_code_type']").val(aData[3]);
							//alert(aData[2]+"==="+aData[3]);
						}else {
							$("#couponCode").css('display', 'block'); 
							$("#couponCode").html( aData[1] );
						}
					}
				);
//				alert($("input[name='coupon_discount_code_value']").val() + "===" + $("input[name='coupon_discount_code_type']").val());
			});
			
			
   			$("#calculateOrderPrice").click( function() {        			
       			if( nTotalPrice <= 0 ) {
       				alert("You haven't any products!");
       				return;
       			}     
       			
       			 
       			if( jQuery.trim($("input[name='coupon_discount_code_value']").val()) == "" && jQuery.trim($("input[name='coupon_discount_code_type']").val()) == ""  ) {
	       			if( jQuery.trim($("input[name='coupon_discount_code']").val()) != "" ) {
						alert('Please click to "Calculate Order Price" button to get right discount price first.');
						$("input[name='coupon_discount_code_value']").val( "" );
						$("input[name='coupon_discount_code_type']").val( "" );
						$("#couponCode").css('display', 'none'); 
						$("#couponCode").html( "" );
					}
       			}
       			
       			nCounponDiscountPrice		= 0;
       			nTotalPriceBeforeDiscount	= nTotalPrice;       			
       			nCounponDiscountValue		= parseFloat($("input[name='coupon_discount_code_value']").val());
       			nCounponDiscountType		= $("input[name='coupon_discount_code_type']").val();
       			
       				
       			if( nCounponDiscountValue && nCounponDiscountType && jQuery.trim($("input[name='coupon_discount_code']").val()) != "" ) {
       				if( nCounponDiscountType == "percent") {
       					nCounponDiscountPrice	= nSubTotalWithOutTax * ( nCounponDiscountValue / 100 );
       				}else if( nCounponDiscountType == "total" ) {
       					nCounponDiscountPrice	= nCounponDiscountValue;
       				}
       				$("#calcualte-discount-price").html("-$" + formatAsMoney(nCounponDiscountPrice));
       				nTotalPriceBeforeDiscount	= nTotalPriceBeforeDiscount - nCounponDiscountPrice;
       			}else {
       				$("#calcualte-discount-price").html("N/A");
       				$("#couponCode").css('display', 'none'); 
					$("#couponCode").html( "" );
       			}
       			
       			
       			$("input[name='coupon_discount_price']").val(nCounponDiscountPrice);
       			$("input[name='total_price']").val(nTotalPriceBeforeDiscount);
       			$("#calcualte-total-items-price").html("$"+formatAsMoney(nTotalPrice));
       			
       			
       			nDeliverFee	= 0;       			
       			nDeliverFee	= getDeliverMethodFee($("select[name='shipping_method']").val(), "fee");
       			$("#calcualte-deliver-fee").html("$"+formatAsMoney(nDeliverFee));
       			
       			//alert(nDeliverFee + "----" + nDeliverExtraFee + "----" + nSpecialDeliverExtraFee + "----" + nPostalCodeDeliverExtraFee );
       			nDeliverFee			= parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nSpecialDeliverExtraFee) + parseFloat(nPostalCodeDeliverExtraFee);
       			nDeliverTaxRate		= getDeliverMethodFee($("select[name='shipping_method']").val(), "tax");       			
       			nTotalDeliverTax	= ( nDeliverFee * parseFloat(nDeliverTaxRate) );
       			nDeliverFee			= nDeliverFee + nTotalDeliverTax;
       			//alert( "nDeliverFee:" + nDeliverFee + "--nDeliverExtraFee" + nDeliverExtraFee + "--nSpecialDeliverExtraFee" + nSpecialDeliverExtraFee + "--nPostalCodeDeliverExtraFee" + nPostalCodeDeliverExtraFee);
       			
       			$("#calcualte-extra-deliver-fee").html("$" + formatAsMoney( parseFloat(nDeliverExtraFee) + parseFloat(nSpecialDeliverExtraFee) + parseFloat(nPostalCodeDeliverExtraFee) ));       			
       			$("#calcualte-total-deliver-fee").html("$" + formatAsMoney( nDeliverFee )+ sDeliverFeeTax + (parseFloat(nDeliverTaxRate)*100) + "%)" );       			
       			$("input[name='total_deliver_tax_fee']").val(nTotalDeliverTax);
       			$("input[name='deliver_fee']").val(nDeliverFee);
       			       			
       			$("input[name='sub_total_price']").val(nSubTotalWithOutTax);
				$("input[name='total_tax']").val(nTotalTax);				
				
       			$("#calcualte-total-price").html("$"+formatAsMoney(parseFloat(nTotalPriceBeforeDiscount) + parseFloat(nDeliverFee)));
       			$("input[name='total_price']").val(parseFloat(nTotalPriceBeforeDiscount) + parseFloat(nDeliverFee)); 
       			//alert(nTotalTax);       			    			
       		});
       		
       		
    
       		$("#saveOrder").click( function() {   
       			$("#calculateOrderPrice").trigger('click');  
       			
       			if( nTotalPrice <= 0 ) {
       				return;
       			}
       			
       			if( bExistDeliverAddress == 1 ) {
	       			myArray = { "user_name" 				: "User Name", 
	   							"account_email" 			: "Account Email", 
	   							"bill_first_name" 			: "Billing First Name", 
	   							"bill_last_name" 			: "Billing Last Name", 
	   							//"bill_address_1" 			: "Billing Address 1", 
	   							"bill_city" 				: "Billing City", 
	   							"bill_zip_code" 			: "Billing Zip Code", 
	   							"bill_country" 				: "Billing Country", 
	   							"bill_state" 				: "Billing State", 
	   							"bill_phone" 				: "Billing Phone Number", 
	   							"address_user_name" 		: "Address Nickname", 
	   							"deliver_first_name" 		: "Deliver First Name", 
	   							"deliver_last_name" 		: "Deliver Last Name", 
	   							//"deliver_address_1" 		: "Deliver Address 1", 
	   							"deliver_city" 				: "Deliver City", 
	   							"deliver_zip_code" 			: "Deliver Zip Code", 
	   							"deliver_country" 			: "Deliver Country", 
	   							"deliver_state" 			: "Deliver State", 
	   							"deliver_phone" 			: "Deliver Phone Number", 
	   							"payment_method" 			: "Payment Method", 
	   							"name_on_card" 				: "Name On Card", 
	   							"credit_card_number" 		: "Credit Card Number", 
	   							"credit_card_security_code" : "Cedit Card Security Code", 
	   							"expire_month" 				: "Expire Month"
	   						  };
       			}else {
       				myArray = { "user_name" 				: "User Name", 
	   							"account_email" 			: "Account Email", 
	   							"bill_first_name" 			: "Billing First Name", 
	   							"bill_last_name" 			: "Billing Last Name", 
	   							//"bill_address_1" 			: "Billing Address 1", 
	   							"bill_city" 				: "Billing City", 
	   							"bill_zip_code" 			: "Billing Zip Code", 
	   							"bill_country" 				: "Billing Country", 
	   							"bill_state" 				: "Billing State", 
	   							"bill_phone" 				: "Billing Phone Number", 
	   							"payment_method" 			: "Payment Method", 
	   							"name_on_card" 				: "Name On Card", 
	   							"credit_card_number" 		: "Credit Card Number", 
	   							"credit_card_security_code" : "Cedit Card Security Code", 
	   							"expire_month" 				: "Expire Month"
	   						  };
       			}
       			
   				selectElementArray	= new Array("bill_country", "bill_state", "deliver_country", "deliver_state", "payment_method", "expire_month" );
   				
   				var objValid	= "";
				for ( key in myArray ) {
					if( jQuery.inArray( key, selectElementArray ) != -1 ) {
						objValid	= "select[name='" + key + "']";
					}else {
						objValid	= "input[name='" + key + "']";
					}
					
					if( key == "bill_zip_code" ) {
						if( !isValidZipCode( $("input[name='bill_zip_code']").val() ) ) {
							alert("Please enter your billing postcode again!");
							return;
						}
					}
					
					if( key == "deliver_zip_code" ) {
						if( !isValidZipCode( $("input[name='deliver_zip_code']").val() ) ) {
							alert("Please enter your deliver postcode again!");
							return;
						}
					}
					
					if( jQuery.trim($(objValid).val()) == "" ) {
						alert('Please enter your "' + myArray[key] + '" !');
						$(objValid).focus();
						return;
					}
				}
				
				
				
				if( ( nMonthNow > parseFloat($("select[name='deliver_month']").val()) && nMonthNow != 12 ) 
					|| 	( nDayNow > parseFloat($("select[name='deliver_day']").val()) && nMonthNow == parseFloat($("select[name='deliver_month']").val()) )	
				  ) {
					alert( sUnAvailableDateMsg );
					return;
				}
				
				
				if( isUnAvailableDate( parseFloat($("select[name='deliver_month']").val()), parseFloat($("select[name='deliver_day']").val())) ) {
					alert( sUnAvailableDateMsg );
					return;
				}
				
				sArrayOfProductID	= "";
				sArrayOfQuantityID	= "";
				
				if( oForm.elements['product_id[]'].length ) {
					for( k = 0; k < oForm.elements['product_id[]'].length; k++ ) {
						sArrayOfProductID	= sArrayOfProductID + oForm.elements['product_id[]'][k].value + ",";
						sArrayOfQuantityID	= sArrayOfQuantityID + oForm.elements['quantity[]'][k].value + ",";
					}
					
					sArrayOfProductID	= sArrayOfProductID.substring( 0, sArrayOfProductID.length - 1 );
					sArrayOfQuantityID	= sArrayOfQuantityID.substring( 0, sArrayOfQuantityID.length - 1 );
				}else {
					sArrayOfProductID	= $("#product-id-item-1").val();
					sArrayOfQuantityID	= $("#quantity-item-1").val();
				}
				
				/*alert(sArrayOfProductID + "===" + sArrayOfQuantityID);
				return;*/
				
				if ( confirm('Do you want to save this order?') == true ) {
					$.post( "index2.php",
						{ 	option: 					"com_phoneorder", 
							task: 						"save",
							//check_CC_payment: 			"1",
							user_id: 					$("input[name='user_id']").val(), 
							user_name: 					$("input[name='user_name']").val(), 
							account_email: 				$("input[name='account_email']").val(),							
							bill_company_name: 			$("input[name='bill_company_name']").val(), 
							bill_first_name: 			$("input[name='bill_first_name']").val(), 
							bill_last_name: 			$("input[name='bill_last_name']").val(), 
							bill_middle_name: 			$("input[name='bill_middle_name']").val(), 
							bill_address_1: 			$("input[name='bill_address_1']").val(), 
							bill_address_2: 			$("input[name='bill_address_2']").val(), 
							bill_city: 					$("input[name='bill_city']").val(), 
							bill_zip_code: 				$("input[name='bill_zip_code']").val(), 
							bill_country: 				$("select[name='bill_country']").val(), 
							bill_state: 				$("select[name='bill_state']").val(), 
							bill_phone: 				$("input[name='bill_phone']").val(), 
							bill_evening_phone: 		$("input[name='bill_evening_phone']").val(), 
							bill_fax: 					$("input[name='bill_fax']").val(),
							address_user_name: 			$("input[name='address_user_name']").val(), 
							deliver_first_name: 		$("input[name='deliver_first_name']").val(), 
							deliver_last_name: 			$("input[name='deliver_last_name']").val(), 
							deliver_middle_name: 		$("input[name='deliver_middle_name']").val(), 
							deliver_address_1: 			$("input[name='deliver_address_1']").val(), 
							deliver_address_2: 			$("input[name='deliver_address_2']").val(), 
							deliver_phone: 				$("input[name='deliver_phone']").val(), 
							deliver_evening_phone: 		$("input[name='deliver_evening_phone']").val(), 
							deliver_cell_phone: 		$("input[name='deliver_cell_phone']").val(), 
							deliver_city: 				$("input[name='deliver_city']").val(), 
							deliver_zip_code: 			$("input[name='deliver_zip_code']").val(), 
							deliver_country: 			$("select[name='deliver_country']").val(), 
							deliver_state: 				$("select[name='deliver_state']").val(), 
							deliver_evening_phone: 		$("input[name='deliver_evening_phone']").val(), 
							deliver_cell_phone: 		$("input[name='deliver_cell_phone']").val(), 
							deliver_fax: 				$("input[name='deliver_fax']").val(),
							deliver_recipient_email: 	$("input[name='deliver_recipient_email']").val(),							
							occasion: 					$("select[name='occasion']").val(),
							shipping_method: 			$("select[name='shipping_method']").val(),
							card_msg: 					$("textarea[name='card_msg']").val(),
							signature: 					$("textarea[name='signature']").val(),
							card_comment: 				$("textarea[name='card_comment']").val(),
							deliver_day: 				$("select[name='deliver_day']").val(),
							deliver_month: 				$("select[name='deliver_month']").val(),
							payment_method_state: 		$("input[name='payment_method_state']:checked").val(),
							payment_method: 			$("select[name='payment_method']").val(),
							name_on_card: 				$("input[name='name_on_card']").val(),
							credit_card_number: 		$("input[name='credit_card_number']").val(),
							credit_card_security_code: 	$("input[name='credit_card_security_code']").val(),
							expire_month: 				$("select[name='expire_month']").val(),
							expire_year: 				$("select[name='expire_year']").val(),
							total_price: 				$("input[name='total_price']").val(),
							coupon_discount_price: 		$("input[name='coupon_discount_price']").val(),
							deliver_fee: 				$("input[name='deliver_fee']").val(),
							sub_total_price: 			$("input[name='sub_total_price']").val(),
							total_tax: 					$("input[name='total_tax']").val(),
							total_deliver_tax_fee: 		$("input[name='total_deliver_tax_fee']").val(),
							exist_address_deliver: 		$("input[name='exist_address_deliver']:checked").val(),
							deliver_address_item: 		$("input[name='deliver_address_item']:checked").val(),
							product_id: 				sArrayOfProductID,
							quantity: 					sArrayOfQuantityID,
							ajaxSend: function(){
								$("#msgCheckoutReport").html('<img src="components/com_virtuemart/html/jquery_ajax.gif" align="absmiddle"/>&nbsp;&nbsp;Order checkout is processing...'); 
							}
						},			
						function(data){						
							aData	= data.split("[--1--]");
							if( jQuery.trim( aData[0] ) == "save_order_success" ) {
								location.href	= "index2.php?option=com_phoneorder&task=save_order_success&mosmsg=" + aData[1];								
							}else {
								$("#msgCheckoutReport").html( aData[1] );
							}
						}
					);
				}
       		});
       		
   			
   			if( sDeliverZipCode != "" ) {
   				bDeliverZipCode	= 1;
   			}
   				
   			   				
       		setDeliverDefault( );
       		
       		$("input[name='deliver_zip_code']").blur( function() { 
   				changeDeliver( );       		
       		});
       		
       		
       		function setDeliverDefault( ) {   
       			if( bCutOffTime ) {
       				if( nDayNow >= nDaysOfMonthNow ) {
       					nDaysValid = 1;
       					
       					if( parseFloat(nMonthNow) == 12 ) {
	       					nMonthsValid = 1;
	       				}else {
	       					nMonthsValid = parseFloat(nMonthNow) + 1;
	       				}	       				
       				}else {
       					nDaysValid = parseFloat(nDayNow) + 1;
       					nMonthsValid = parseFloat(nMonthNow);
       				}
       			}else {
       				nDaysValid = parseFloat(nDayNow);
  					nMonthsValid = parseFloat(nMonthNow); 
       			} 
       			
       								
  				changeUnAvailableDate( parseFloat(nMonthsValid) );
       			setDeliverDate( nDaysValid, nMonthsValid );
       			changeDeliver( );
       		}
       		
       		$("select[name='deliver_month'],select[name='deliver_day']").change( function() {        			
       			nDaysValid = parseFloat($("select[name='deliver_day']").val());
				nMonthsValid = parseFloat($("select[name='deliver_month']").val());
   				changeDeliver( );       		
       		});
       		
       		
       		function changeDeliver( ) {
       			if( bCutOffTime <= 0 && ( nDayNow == parseFloat($("select[name='deliver_day']").val()) && nMonthNow == parseFloat($("select[name='deliver_month']").val())  ) ) {
  					nDeliverExtraFee	= nDeliverExtraFeeForSameDay;
  					$("#deliver_extra").html("$" + nDeliverExtraFee + sDeliverFeeExtraSameDay);    					
       			}else {
       				nDeliverExtraFee	= 0;
       				$("#deliver_extra").html("");
       			}
       			
       			nSpecialDeliverExtraFee = isSpecialDate( nMonthsValid, nDaysValid );  
				if( nSpecialDeliverExtraFee ) {
					if( nDeliverExtraFee > 0 ) {
						$("#deliver_extra").html($("#deliver_extra").html() + "<br/>$" + nSpecialDeliverExtraFee + sSpecialDeliverExtraFee);  
					}else {
						$("#deliver_extra").html("$" + nSpecialDeliverExtraFee + sSpecialDeliverExtraFee);  
					}
				}	
				
				if( sZipCode == "" ) {
       				nPostalCodeDeliverExtraFee	= isPostalCodeFee(jQuery.trim($("input[name='deliver_zip_code']").val()));
       				//alert(jQuery.trim($("input[name='deliver_zip_code']").val()) + "===" + nPostalCodeDeliverExtraFee);
				}else {
					nPostalCodeDeliverExtraFee	= isPostalCodeFee(jQuery.trim(sZipCode));
				}
				//alert(nPostalCodeDeliverExtraFee);
				if( nPostalCodeDeliverExtraFee > 0 ) {
					if( nDeliverExtraFee > 0 || nSpecialDeliverExtraFee > 0 ) {
						$("#deliver_extra").html($("#deliver_extra").html() + "<br/>$" + nPostalCodeDeliverExtraFee + sPostalCodeDeliverExtraFee);  
					}else {
						$("#deliver_extra").html("$" + nPostalCodeDeliverExtraFee + sPostalCodeDeliverExtraFee);  
					}
				}
       		}
       		
       		
       		function setDeliverDate( day, month  ) {
       			oForm.deliver_day.options[day].selected = true;
       			oForm.deliver_month.options[month].selected = true; 
       		}    		
       		
       		
       		function changeUnAvailableDate( nCurrentMonth ) {       			
       			if( sUnAvailableDate ) {               				
   					aUnAvailableDate = sUnAvailableDate.split("[--1--]");
   					nCurrentMonth    = parseFloat(nCurrentMonth);			
   					
   					for( i = 0; i < 32; i ++ ) {
   						oForm.deliver_day.options[i].style.color 	= "black";
   						oForm.deliver_day.options[i].text			= oForm.deliver_day.options[i].value;
   					}
   					
   					
   					for( i = 0; i < aUnAvailableDate.length ; i++  ) {
   						if( aUnAvailableDate[i] != "" ) {
       						aUnAvailableItem = aUnAvailableDate[i].split("/");
       						if( nCurrentMonth == parseFloat(aUnAvailableItem[0]) ) {       							
           						oForm.deliver_day.options[aUnAvailableItem[1]].style.color 	= "red";
           						oForm.deliver_day.options[aUnAvailableItem[1]].text			= oForm.deliver_day.options[aUnAvailableItem[1]].text + sUnAvailableDateText;
       						}
   						}
   					}   				
   				}
       		}
       		
       		
       		function setSelector( objectElement, valueSelected ) {       			
       			for ( var i = 0; i < objectElement.options.length; i++ ){ //loop through all form elements
					if ( objectElement.options[i].value == valueSelected ){
						objectElement.options[i].selected = true;
					}
				}
       		}
       		
       		
       		function isUnAvailableDate( nCurrentMonth, nCurrentDay ) {
       			if( sUnAvailableDate ) {               				
   					aUnAvailableDate = sUnAvailableDate.split("[--1--]");           					
   					for( i = 0; i < aUnAvailableDate.length ; i++  ) {
   						if( aUnAvailableDate[i] ) {
       						aUnAvailableItem = aUnAvailableDate[i].split("/");
       						//alert(parseFloat(nCurrentMonth) + "==" + parseFloat(aSpecialDeliverItem[0]) + "&&" +  parseFloat(nCurrentDay) + "==" + parseFloat(aSpecialDeliverItem[1]));
       						//alert( nCurrentMonth + "==" + aUnAvailableItem[0] + "----" + nCurrentDay + "==" + aUnAvailableItem[1] );
       						if( nCurrentMonth == aUnAvailableItem[0] && nCurrentDay == aUnAvailableItem[1] ) {
       							return true;
       							break;
       						}
   						}
   					}   					
   					return false;
   				}
       		}
       		
       		
       		function isSpecialDate( nCurrentMonth, nCurrentDay ) {
       			if( sSpecialDeliver ) {               				
   					aSpecialDeliver = sSpecialDeliver.split("[--1--]");           					
   					for( i = 0; i < aSpecialDeliver.length ; i++  ) {
   						if( aSpecialDeliver[i] ) {
       						aSpecialDeliverItem = aSpecialDeliver[i].split("/");       						
       						if( parseFloat(nCurrentMonth) == parseFloat(aSpecialDeliverItem[0]) && parseFloat(nCurrentDay) == parseFloat(aSpecialDeliverItem[1]) ) {
       							return aSpecialDeliverItem[2];
       							break;
       						}
   						}
   					}   					
   					return 0;
   				}
       		}   
       		  
       		
			function isPostalCodeFee( sPostalCode ) {
       			if( sPostalCodeDeliver ) {               				
   					aPostalCodeDeliver = sPostalCodeDeliver.split("[--2--]");           					
   					for( i = 0; i < aPostalCodeDeliver.length ; i++  ) {
   						if( aPostalCodeDeliver[i] ) {
       						aPostalCodeDeliverItem = aPostalCodeDeliver[i].split("[--1--]");
       						if( sPostalCode == aPostalCodeDeliverItem[0] ) {
       							return aPostalCodeDeliverItem[3];
       							break;
       						}
   						}
   					}   					
   					return 0;
   				}
       		}     
       		
       		
			function getDeliverMethodFee( methodID, sType ) {
       			if( sDeliverMethodFee ) {               				
   					aDeliverMethodFee = sDeliverMethodFee.split("[--2--]");           					
   					for( i = 0; i < aDeliverMethodFee.length ; i++  ) {
   						if( aDeliverMethodFee[i] ) {
       						aDeliverMethodFeeItem = aDeliverMethodFee[i].split("[--1--]");
       						if( methodID == aDeliverMethodFeeItem[0] ) {
       							if( sType == "tax" ) {
       								return aDeliverMethodFeeItem[2];
       							}else {
       								return aDeliverMethodFeeItem[1];
       							}
       							break;
       						}
   						}
   					}   					
   					return 0;
   				}
       		}     
       		
			
			//============================================= PRODUCT FUNCTIONS =============================================			
			var nTotalPrice 		= 0;				
			var nSubTotalWithOutTax	= 0;				
			var nTotalTax			= 0;				
			var nTotalDeliverTax	= 0;				
			var nDeliverTaxRate		= 0;				
			var nTotalItem 			= 0;				
			var aAccountInfo 		= new Array();				
			var sProductInformation = "<?php echo $aInfomation['Product']; ?>";		
		
			function addProductItem() {				
				productID	= document.adminForm.select_product_id.value;
				
				if( productID > 0 ) {
					nTotalItem++;
					sContent			= $("#product-item-default").html().replace(/{noItem}/g, nTotalItem );									
					aProductInformation	= sProductInformation.split("[--2--]");
					var	nSubTotal 		= 0;
					var	nTotal 			= 0;
					
					for( i = 0; i < aProductInformation.length; i++ ) {
						aProductItem	= aProductInformation[i].split("[--1--]");
						if( aProductItem[0] == productID ) {
							sContent	= sContent.replace(/{item-id}/g, aProductItem[0] );
							sContent	= sContent.replace(/{item-name}/g, "[SKU: " + aProductItem[1] + "] - " + aProductItem[2] );
							sContent	= sContent.replace(/{item-price}/g, "$"+aProductItem[3] );
							sContent	= sContent.replace(/{item-tax}/g, (aProductItem[4]*100)+"%" );	
							sContent	= sContent.replace(/{real-price}/g, aProductItem[3] );	
							sContent	= sContent.replace(/{real-tax}/g, aProductItem[4] );
																			
							nSubTotal	= ( parseFloat(aProductItem[3]) * parseFloat(aProductItem[4]) )  + parseFloat(aProductItem[3]);	
							nTotalTax	= nTotalTax + ( parseFloat(aProductItem[3]) * parseFloat(aProductItem[4]) );
							nSubTotalWithOutTax	= nSubTotalWithOutTax + parseFloat(aProductItem[3]);
							nTotal		= nTotalPrice + nSubTotal;
							nTotalPrice	= nTotal;
							nSubTotal	= formatAsMoney(nSubTotal);
							nTotal		= formatAsMoney(nTotal);
							
							$("#quantity-item-"+nTotalItem).val(nSubTotal);
							$("#total-price").html("$"+nTotal);
							sContent	= sContent.replace(/{item-subtotal}/g, "$"+nSubTotal );
							break;
						}					
					}
					
					$("#product-list-items").html($("#product-list-items").html() + sContent);
					$("#select_product_id").val("");
					$("#selectProductId").val("");								
					
				}else {					
					alert('Please choose products for your order form!');
				}
			}
			

			function deleteItem( noItem, real_price, real_tax ) {
				if ( confirm('Do you want to delete this item?') == true ) {
					nQuantity	= parseFloat($("#quantity-item-"+noItem).val());
					nDownPrice	= nQuantity * real_price;
					nDownPrice	= ( nDownPrice * real_tax ) + nDownPrice;					
					nTotalPrice	= nTotalPrice - nDownPrice;
					nSubTotalWithOutTax	= nSubTotalWithOutTax - real_price;
					$("#total-price").html("$"+formatAsMoney(nTotalPrice));	
					$("#product-item-"+noItem).html("");
				}
			}
			
			
			function saveNumberProduct( number_items ) {
				$("input[name='product_temp']").val(number_items);
			}
			
			
			function checkNumberProduct( number_items, real_price, real_tax, noItem ) {
				if( number_items > 0 ) {
					nExtraNumber = parseInt(number_items) - parseInt($("input[name='product_temp']").val());
					if( nExtraNumber > 0 ) {
						nExtraPrice	= ( real_price * nExtraNumber );	
						nTotalTax	= nTotalTax + ( parseFloat(nExtraPrice) * parseFloat(real_tax) );
						nExtraPrice	= ( real_tax * nExtraPrice ) + nExtraPrice;		
						nSubTotalWithOutTax	= nSubTotalWithOutTax + nExtraPrice;										
						nTotal		= parseFloat(nTotalPrice) + nExtraPrice;
						nTotalPrice	= nTotal;
						
					}else {
						nExtraPrice	= ( real_price * nExtraNumber );
						//alert(nExtraNumber + "===" + nExtraPrice + "===" + nTotal );	
						nTotalTax	= nTotalTax + ( parseFloat(nExtraPrice) * parseFloat(real_tax) );					
						nExtraPrice	= ( real_tax * nExtraPrice ) + nExtraPrice;
						nSubTotalWithOutTax	= nSubTotalWithOutTax + nExtraPrice;
						nTotal		= parseFloat(nTotalPrice) + nExtraPrice;
						nTotalPrice	= nTotal;
					}
					nTotal	= formatAsMoney(nTotal);
					$("#total-price").html("$"+nTotal);							
						
					nExtraRealPrice	= ( real_price * number_items );
					nExtraRealPrice	= ( real_tax * nExtraRealPrice ) + nExtraRealPrice;
					nExtraRealPrice	= formatAsMoney(nExtraRealPrice);
					$("#item-subtotal-"+noItem).html("$"+nExtraRealPrice);
				}else {	
					//$("#quantity-item-"+noItem).focus();
					alert("Please enter a number for product quantity!");	
									
				}
				
			}
			
			
			$("#addProductItem").click(function() {	
				addProductItem();
			});
			
			
			$("#createAccInfo").click(function() {	
				if ( confirm('Do you want to clear all fields data in "Billing Information" and "Deliver Information"? \nWill we create a new account?') == true ) {
					$("input[name='user_name']").val("");
					$("input[name='user_id']").val("0");
					$("input[name='bill_company_name']").val("");
					$("input[name='bill_first_name']").val("");
					$("input[name='bill_last_name']").val("");
					$("input[name='bill_middle_name']").val("");
					$("input[name='bill_address_1']").val("");
					$("input[name='bill_address_2']").val("");
					$("input[name='bill_city']").val("");
					$("input[name='bill_zip_code']").val("");
					$("input[name='bill_phone']").val("");
					$("input[name='bill_evening_phone']").val("");
					$("input[name='bill_fax']").val("");
					$(".after-check-account").css("display","block");
					$(".before-check-account").css("display","none");
					$("input[name='exist_address_deliver'][value='1']").attr('checked', true);
					$("#deliver-address-default").html( '<div class="error-msg">None</div>' );
					$("#error-report").html("You are creating a new account information!");
					//$("#createAccInfo").css('display', 'none');
				}
			});
			
			
			$("#checkAccInfo").click(function() {	
				sEmail	= jQuery.trim($("input[name='account_email']").val());
				
				if( sEmail != "" ) {
					$.ajax({
				 		data: "email="+sEmail,	
				 		type: "POST",
				 		dataType: "html",
						url: "?option=<?php echo $option?>&task=check_account_info",
						success		: function(data, textStatus){
							data			= jQuery.trim(data);
							
							if( data == "error" ) {
								$("#error-report").html("This account information is not exist! Please try again or create a new account information!");
								/*$("input[name='user_name']").val("");
								$("input[name='user_id']").val("0");
								$("input[name='bill_company_name']").val("");
								$("input[name='bill_first_name']").val("");
								$("input[name='bill_last_name']").val("");
								$("input[name='bill_middle_name']").val("");
								$("input[name='bill_address_1']").val("");
								$("input[name='bill_address_2']").val("");
								$("input[name='bill_city']").val("");
								$("input[name='bill_zip_code']").val("");
								$("input[name='bill_phone']").val("");
								$("input[name='bill_evening_phone']").val("");
								$("input[name='bill_fax']").val("");*/
								$(".after-check-account").css("display","none");
								$(".before-check-account").css("display","block");
								$("#deliver-address-default").html( '<div class="error-msg">None</div>' );
							}else {
								aData			= data.split("[--3--]");
								aAccountInfo	= aData[0].split("[--1--]")	;
								aDeliver		= aData[1].split("[--2--]");							
								
								if( aData[3] ) {
									sMsg	= aData[2] + "<br/>" + aData[3];
								}else {
									sMsg	= aData[2];
								}
											
								$("#error-report").html( sMsg );
								//$("#createAccInfo").css('display', 'none');
								$("input[name='exist_address_deliver'][value='0']").attr('checked', true);
							
								if( aAccountInfo[2] != 0 ) {
									$("input[name='user_name']").val(aAccountInfo[0]);
									$("input[name='user_id']").val(aAccountInfo[2]);
									$("input[name='bill_company_name']").val(aAccountInfo[5]);
									$("input[name='bill_first_name']").val(aAccountInfo[8]);
									$("input[name='bill_last_name']").val(aAccountInfo[7]);
									$("input[name='bill_middle_name']").val(aAccountInfo[9]);
									$("input[name='bill_address_1']").val(aAccountInfo[13]);
									$("input[name='bill_address_2']").val(aAccountInfo[14]);
									$("input[name='bill_city']").val(aAccountInfo[15]);
									setSelector( oForm.bill_state, aAccountInfo[16] );
									setSelector( oForm.bill_country, aAccountInfo[17] );
									$("input[name='bill_zip_code']").val(aAccountInfo[18]);
									$("input[name='bill_phone']").val(aAccountInfo[10]);
									$("input[name='bill_evening_phone']").val(aAccountInfo[11]);
									$("input[name='bill_fax']").val(aAccountInfo[12]);
									$(".after-check-account").css("display","block");
									$(".before-check-account").css("display","none");
								}else {									
									$("input[name='user_name']").val(aAccountInfo[1]);
									$("input[name='user_id']").val(aAccountInfo[0]);
									$("input[name='bill_company_name']").val("");
									$("input[name='bill_first_name']").val("");
									$("input[name='bill_last_name']").val("");
									$("input[name='bill_middle_name']").val("");
									$("input[name='bill_address_1']").val("");
									$("input[name='bill_address_2']").val("");
									$("input[name='bill_city']").val("");
									$("input[name='bill_zip_code']").val("");
									$("input[name='bill_phone']").val("");
									$("input[name='bill_evening_phone']").val("");
									$("input[name='bill_fax']").val("");
									$(".after-check-account").css("display","none");
									$(".before-check-account").css("display","block");
								}
								
								
								if( aDeliver[0] ) {
									for( j = 0; j < aDeliver.length; j++ ) {
										aDeliverTemp	= aDeliver[j].split("[--1--]");
										if( aDeliverTemp[0] != "" ) {
											sContentTemp	= $("#deliver-address-item").html().replace(/{value}/g, aDeliverTemp[0] + "[--1--]" + aDeliverTemp[1] + "[--1--]" + aDeliverTemp[2] );	
											sContentTemp	= sContentTemp.replace(/{text}/g, aDeliverTemp[3] );
																						
											if( j == 0 ) {
												sContentTemp	= sContentTemp.replace(/{status}/g, "checked" );
												$("#deliver-address-default").html( sContentTemp );
											}else {
												sContentTemp	= sContentTemp.replace(/{text}/g, "" );
												$("#deliver-address-default").html( $("#deliver-address-default").html() + sContentTemp );
											}
										}
									}	
									
	
									$("input[name='deliver_address_item']").click( function() {
										$("input[name='exist_address_deliver'][value='0']").attr('checked', true);				
										$("input[name='exist_address_deliver'][value='1']").attr('checked', false);			
										bExistDeliverAddress = 0;
										
										aDeliverTemp	= $("input[name='deliver_address_item'][checked='true']").val().split("[--1--]");									
										sZipCode		= aDeliverTemp[2];
										changeDeliver( );
									});		
									
	
									$("input[name='exist_address_deliver'][value='1']").click( function() {
										$("input[name='deliver_address_item']").attr('checked', false);	
										bExistDeliverAddress 	= 1;
										sZipCode				= "";	
										changeDeliver( );
									});	
								}else {
									$("#deliver-address-default").html( '<div class="error-msg">None</div>' );
								}
							}								
						} 
					});
				}else {
					alert("Please enter account email to check infomation!");
				}
			});
			
			
			$("#bill_country").change(function () {			     	
		     	$("#bill_state_container").html('Loading...');
		     
		 		$.post( "index2.php",
				   	  	{ option: 				"com_phoneorder", 
				   	  	  task: 				"getsate", 
				   	  	  selector_id: 			"bill_state", 
				   	  	  country_id: 			$(this).val()
				   	  	},
				   	  	  
					   	function(data){
					     	if( data != "error" ) {			     		
					     		$("#bill_state_container").html(data);
					     	}else {
					     		$("#bill_state_container").html("There aren't any states of this country. Please chose other one!");
					     	}
					   	}
				);     		
		    });
		    
		    
			$("#deliver_country").change(function () {			     	
		     	$("#deliver_state_container").html('Loading...');
		     
		 		$.post( "index2.php",
				   	  	{ option: 				"com_phoneorder", 
				   	  	  task: 				"getsate", 
				   	  	  selector_id: 			"deliver_state", 
				   	  	  country_id: 			$(this).val()
				   	  	},
				   	  	  
					   	function(data){
					     	if( data != "error" ) {			     		
					     		$("#deliver_state_container").html(data);
					     	}else {
					     		$("#deliver_state_container").html("There aren't any states of this country. Please chose other one!");
					     	}
					   	}
				);     		
		    });
			
			
			$("#copyInfo").click(function() {	
				if( aAccountInfo[0] != "" ) {
					$("input[name='address_user_name']").val(aAccountInfo[5]);
					$("input[name='deliver_first_name']").val(aAccountInfo[8]);
					$("input[name='deliver_last_name']").val(aAccountInfo[7]);
					$("input[name='deliver_middle_name']").val(aAccountInfo[9]);
					$("input[name='deliver_address_1']").val(aAccountInfo[13]);
					$("input[name='deliver_address_2']").val(aAccountInfo[14]);
					$("input[name='deliver_city']").val(aAccountInfo[15]);
					setSelector( oForm.deliver_state, aAccountInfo[16] );
					setSelector( oForm.deliver_country, aAccountInfo[17] );
					$("input[name='deliver_zip_code']").val(aAccountInfo[18]);
					$("input[name='deliver_phone']").val(aAccountInfo[10]);
					$("input[name='deliver_evening_phone']").val(aAccountInfo[11]);
					$("input[name='deliver_fax']").val(aAccountInfo[12]);
				}
			});
						
			
			function formatAsMoney(mnt) {
			    mnt -= 0;
			    mnt = (Math.round(mnt*100))/100;
			    return (mnt == Math.floor(mnt)) ? mnt + '.00' : ( (mnt*10 == Math.floor(mnt*10)) ? mnt + '0' : mnt);
			}	
			
			function isValidZipCode( value ) {
			   var re = /^[A-Za-z0-9\s]{6,7}$/;
			   return (re.test(value));
			}
			
		//-->
		</script>
		<input type="hidden" name="deliver_fee" value="0" />
		<input type="hidden" name="total_deliver_tax_fee" value="0" />
        <input type="hidden" name="total_tax" value="0" />
        <input type="hidden" name="sub_total_price" value="0" />
        <input type="hidden" name="total_price" value="0" />
		<input type="hidden" name="product_temp" value="0" />
		<input type="hidden" name="user_id" value="0" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="save" />		
		</form>
		<?php
	}
	
	
	//============================================= XML ORDER ===============================================
	function makeXMLOrder( $option, $aInfomation ) {
		global $mosConfig_live_site;		
		
	?>	
		<style type="text/css">				
			td.title {
				font:bold 12px Tahoma, Verdana;				
				text-align:right;		
			}
			
			
			input.btn {
				font:bold 12px Tahoma, Verdana;
				cursor:pointer;
				padding:3px;
			}
		</style>
		<script type="text/javascript">
			function validXmlFile() {
				if( document.adminForm.xml_file.value == "" ) {
					alert("Please select your XML file before upload!");
					document.adminForm.xml_file.focus();
					return;
				}
				
				document.adminForm.submit();
			}
		
		</script>
		
		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			XML Order Manager:
			<small>Add New</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform" cellspacing="5" >			
			<tr>
				<td width="20%" align="right" class="title" style="text-align:right;padding-right:20px;"><strong>XML Order source file:</strong> </td>
				<td width="80%" align="left"><a href="<?php echo $mosConfig_live_site."/administrator/components/com_phoneorder/order.xml"?>"><strong>Order XML file</strong></a> (Right click and choose Save Link As...)</td>
			</tr>
			<tr>
				<td width="20%" align="right" class="title" style="text-align:right;vertical-align:top;padding-top:10px;padding-right:20px;"><strong>UnAvailable Date List:<br/>(Month/Day)</strong> </td>
				<td width="80%" align="left" style="font:normal 12px Tahoma, Verdana; line-height:25px;">
					<?php
						for ( $i = 0; $i < count($aInfomation["unavailable"]); $i++ ) {								
							echo "<strong>".$aInfomation["unavailable"][$i]->name."</strong> (".$aInfomation["unavailable"][$i]->options.")<br/>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td width="20%" align="right" class="title" style="text-align:right;vertical-align:top;padding-top:10px;padding-right:20px;"><strong>Payment Method:</strong> </td>
				<td width="80%" align="left" style="font:normal 12px Tahoma, Verdana; line-height:25px;">
					<?php
						for ( $i = 0; $i < count($aInfomation["shipping"]); $i++ ) {								
							echo "<strong>".$aInfomation["shipping"][$i]->shipping_rate_name."</strong><br/>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td width="20%" align="right" class="title" style="text-align:right;"><strong>XML Order file:</strong> </td>
				<td width="80%" align="left"><input type="file" name="xml_file" size="60"/></td>
			</tr>
			<tr>
				<td width="20%" align="right" class="title" style="text-align:right;"></td>
				<td width="80%" align="left"><input class="btn" type="button" name="submit2" value="Upload & Save Order" onclick="validXmlFile();"/></td>
			</tr>
		</table>
					
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="xml_order" />		
		<input type="hidden" name="task" value="save_order_xml" />		
		</form>
		<?php
	}
}
?>
