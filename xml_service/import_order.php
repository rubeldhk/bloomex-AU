<?php
define( '_VALID_MOS', 1 );
define('MYSQL_TYPES_NUMERIC', 'int real ');
define('MYSQL_TYPES_DATE', 'datetime timestamp year date time ');
define('MYSQL_TYPES_STRING', 'string blob ');


require_once('../configuration.php');
require_once('db.class.php');
require_once("ftp.class.php");

$db = new db_class;
if (!$db->connect( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, true)) $db->print_last_error(false);


$rootPath			=  "$mosConfig_absolute_path/xml_service/xml_processed/";
$rootPartnerPath	=  "$mosConfig_absolute_path/media/partner_service/";
//======================================================================================================================================
function data_filter ( $aKw, $name ) {
	foreach( $aKw as $value ) {
		$pos  = strpos( $name, $value );
		if ( $pos == true ) return true;
	}
	
	return false;
}


function startTag( $parser, $name, $attrs ) {
	global $stack;
	$tag	= array( "name"=>$name, "attrs"=>$attrs ); 
	array_push( $stack, $tag );
}


function cdata( $parser, $cdata ) {
	global $stack; 
	if( trim( $cdata ) ) {
		$stack[ count( $stack )-1 ]['cdata']	= $cdata;
	}
}


function endTag( $parser, $name ) {
	global $stack; 
	$stack[ count( $stack )-2 ]['children'][]	= $stack[ count( $stack )-1 ];
	array_pop( $stack );
}


function xml_to_array( $file = '' ) {
	global $stack;
	$stack			= array();
	$xml_parser 	= xml_parser_create("UTF-8");
	xml_set_element_handler( $xml_parser, "startTag", "endTag" );
	xml_set_character_data_handler( $xml_parser, "cdata" );	
	
	$data 			= xml_parse( $xml_parser, (@file_get_contents( $file )) );
	
	if( !$data ) {
		die(sprintf("XML error: %s at line %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser)));
	}
	xml_parser_free($xml_parser);
	return $stack;
}
//======================================================================================================================================


echo "Import Order  XML file:<br/><br/>";

$users 		= $db->select("SELECT * FROM tbl_partners WHERE published = 1");
while ( $row = $db->get_row($users, 'MYSQL_ASSOC') ) {	
	//print_r($row);
	$ftp = new ClsFTP( $row['ftp_username'], $row['ftp_password'], $row['domain_name'] );
			
	if(  $ftp->file_size( "/orders.xml" ) >= 0 && $ftp->get( $rootPath . "orders.xml", "/orders.xml" ) ) {	
	//echo $rootPath . "orders.xml";
		if( is_file($rootPath . "orders.xml") ) {		
			$aXmlData		= xml_to_array( $rootPath . "orders.xml" );
			print_r($aXmlData);
			
			$sError	= "";
			$i		= 1;
			if( count($aXmlData[0]["children"]) ) {
				$sOrderResult	='<?xml version="1.0" encoding="utf-8"?>
										<orders version="1.0.0">';
				
				foreach ($aXmlData[0]["children"] as $item) {				
					$aOrderItem 							= array();
					$aOrderItem['Mode']						= intval($item['children'][0]['cdata']);
					$aOrderItem['PartnerOrderID']			= trim(htmlentities($item['children'][1]['cdata'], ENT_QUOTES));
					$aOrderItem['PartnerName']				= trim(htmlentities($item['children'][2]['cdata'], ENT_QUOTES));
					$aOrderItem['DeliveryDate']				= trim(htmlentities($item['children'][3]['cdata'], ENT_QUOTES));
					$aOrderItem['Occasion']					= trim(htmlentities($item['children'][4]['cdata'], ENT_QUOTES));
					$aOrderItem['CouponCode']				= trim(htmlentities($item['children'][5]['cdata'], ENT_QUOTES));
					$aOrderItem['RecipientCardMessage']		= trim(htmlentities($item['children'][6]['cdata'], ENT_QUOTES));	
					$aOrderItem['RecipientSignature']		= trim(htmlentities($item['children'][7]['cdata'] , ENT_QUOTES));				
					
					$aOrderItem['RecipientFullName']		= trim(htmlentities($item['children'][8]['children'][0]['cdata'] . " " . $item['children'][8]['children'][2]['cdata'] . " " . $item['children'][8]['children'][1]['cdata'], ENT_QUOTES));
					$aOrderItem['RecipientPhoneNumber']		= trim(htmlentities($item['children'][8]['children'][10]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientAddress']			= trim(htmlentities($item['children'][8]['children'][8]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientAddress2']		= trim(htmlentities($item['children'][8]['children'][9]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientCity']			= trim(htmlentities($item['children'][8]['children'][7]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientState']			= trim(htmlentities($item['children'][8]['children'][5]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientZipCode']			= trim(htmlentities($item['children'][8]['children'][6]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientCountry']			= trim(htmlentities($item['children'][8]['children'][4]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientFax']				= trim(htmlentities($item['children'][8]['children'][11]['cdata'] , ENT_QUOTES));
					$aOrderItem['RecipientEmail']			= trim(htmlentities($item['children'][8]['children'][12]['cdata'] , ENT_QUOTES));
					
					
					$aOrderItem['ProductSKU']		= "";
					$bCart							= false;				
					$aProductItem					= array();	
					if( count( $item['children'][9]['children'] ) ) {
						foreach ($item['children'][9]['children'] as $product_item) {
							$aProductItem[]				 = trim(htmlentities($product_item['children'][0]['cdata'] , ENT_QUOTES));
							$sProductItem			 	.= trim(htmlentities($product_item['children'][0]['cdata'] , ENT_QUOTES)) . ",";
							$aOrderItem['ProductSKU']	.= trim(htmlentities($product_item['children'][0]['cdata'] , ENT_QUOTES)) . '[--1--]' . trim(htmlentities($product_item['children'][1]['cdata'] , ENT_QUOTES)) . '[--2--]';
						}
						$bCart	= true;
					}
					
								
					//=========================== CHECK ==============================
					$bCheck		= true;
					if( !$aOrderItem['PartnerOrderID'] ){
						$bCheck	= false;
						$sError	.= '<partner_order_id>The order must have Partner Order ID.</partner_order_id>';
					}else {
						$bOrderId	= $db->select_one("SELECT id FROM tbl_xmlorder WHERE partner_order_id = '" . $aOrderItem['PartnerOrderID'] . "'");				
						if( $bOrderId ){
							$bCheck	= false;
							$sError	.= '<partner_order_id>This Order Id is exist.</partner_order_id>';
						}
					}
					
					$bUser	= $db->select_one("SELECT id FROM jos_users WHERE username = '" . $aOrderItem['PartnerName'] . "'");				
					if( !$bUser ){
						$bCheck	= false;
						$sError	.= '<partner_name>This user is not exist.</partner_name>';
					}
								
//					echo $aOrderItem['DeliveryDate']."<br/>"			;
//					if( !validateDate( $aOrderItem['DeliveryDate'], 'MM/DD/YYYY' ) ){
//						$bCheck	= false;
//						$sError	.= '<delivery_date>Delivery date is incorrect or empty.</delivery_date>';
//					}
					
					$bCoupon	= $db->select_one("SELECT coupon_id FROM jos_vm_coupons WHERE coupon_code = '" . $aOrderItem['CouponCode'] . "'");	
					if( !$bCoupon ){
						$bCheck	= false;
						$sError	.= '<coupon_code>Coupon Code is incorrect.</coupon_code>';
					}
									
					if( !$aOrderItem['RecipientFullName'] ){
						$bCheck	= false;
						$sError	.= '<recipient_name>Please enter first name, last name, middle name.</recipient_name>';
					}
					
					if( !$aOrderItem['RecipientState'] || !$aOrderItem['RecipientCountry'] || !$aOrderItem['RecipientZipCode'] ){
						$bCheck	= false;
						$sError	.= '<shipping_address>Please enter Country, State, Zip Code for shipping info.</shipping_address>';
					}
					
					if( !$bCart ){
						$bCheck	= false;
						$sError	.= '<products>There are not any products in your order.</products>';
					}
					
					if( count($aProductItem) ) {
						foreach ($aProductItem as $product) {
							$bProduct	= $db->select_one(" SELECT product_id FROM jos_vm_product WHERE product_sku = '$product' ");
							
							if( !$bProduct ){
								$bCheck	= false;
								$sError	.= '<product sku="'.$product.'">This product is not exist.</product>';
							}
						}	
					}
					
					if( $bCheck ) {
						if(  $aOrderItem['Mode'] ) {
							$sResultProcess		= confirmOrder($aOrderItem);
							
							if( $sResultProcess ) {
								$aResultProcess		= explode( "[--1--]", $sResultProcess );
								
								$sOrderResult	.= "<order>
														<mode>".$aOrderItem['Mode']."</mode>
														<partner_order_id>".$aOrderItem['PartnerOrderID']."</partner_order_id>
														<partner_name>".$aOrderItem['PartnerName']."</partner_name> 
														<order_id>".$aResultProcess[0]."</order_id>
														<sub_total>".$aResultProcess[1]."</sub_total>
														<tax>".$aResultProcess[2]."</tax>		
														<discount>".$aResultProcess[3]."</discount>
														<total>".$aResultProcess[4]."</total>
														<curency>".$aResultProcess[5]."</curency>
														<order_status>".$aResultProcess[6]."</order_status>
														<message>Order was saved successful</message>
													</order>";
							}else {
								$sOrderResult	.= "<order>
													<mode>".$aOrderItem['Mode']."</mode>
													<partner_order_id>".$aOrderItem['PartnerOrderID']."</partner_order_id>
													<partner_name>".$aOrderItem['PartnerName']."</partner_name> 
													<message>Order have some wrong in processing. Please check again.</message>
												</order>";
							}
						}else {
							$sOrderResult	.= "<order>
													<mode>".$aOrderItem['Mode']."</mode>
													<partner_order_id>".$aOrderItem['PartnerOrderID']."</partner_order_id>
													<partner_name>".$aOrderItem['PartnerName']."</partner_name> 
													<message>Order was saved successful</message>
												</order>";
						}					
					}else {					
						$sOrderResult	.= "<order>
												<mode>".$aOrderItem['Mode']."</mode>
												<partner_order_id>".$aOrderItem['PartnerOrderID']."</partner_order_id>
												<partner_name>".$aOrderItem['PartnerName']."</partner_name> 
												<message>
													$sError 
												</message>
											</order>";
					}	
				}
				
				$sOrderResult	.= '</orders>';				
				
			}		
			
			if( $ftp->renamefile( "/httpdocs/orders.xml", "/httpdocs/order_processed/order_".date("Y_m_d_h_i").".xml" ) ) {
				
				if( $ftp->put_string( "/httpdocs/order_result_" . date("Y_m_d_h_i") . ".xml",  $sOrderResult ) ) {	
					echo "$i. <b>" . $row['partner_name'] . "</b>: finished.<br/>";					
				}else {
					echo "$i. <b>" . $row['partner_name'] . "</b>: unsuccessful.<br/>";
				}				
				$i++;
				
				$query = "	UPDATE tbl_partners SET import_updated_time = '".date( "Y-m-d H:i:s" , time())."' WHERE partner_name = '" . $row['partner_name'] . "'";
				$db->update_sql($query);
			}
		}
	}
	
	$ftp->close();
} 


function confirmOrder( $Info ) {	
	global $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $db;
	
	require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/virtuemart.cfg.php" );
	require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/language.class.php" );
	require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/english.php" );
	require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/Log/Log.php" );
	$timestamp 			= time() + ($mosConfig_offset*60*60);
	$PaymentVar			= array();
	$isResult			= true;
	$sResultProcess		= "";

	$nPartnerOrderID			= $Info['PartnerOrderID'];
	$sPartnerName				= $Info['PartnerName'];
	$sDeliveryDate				= $Info['DeliveryDate'];
		
	$sProductSKU				= $Info['ProductSKU']; 
	$aProductItem				= explode( '[--2--]', $sProductSKU );
	$aQuantity					= array();
	$sProductSKUItem			= "";
	
	if( count($aProductItem) ) {
		foreach ($aProductItem as $Item) {
			if( $Item ) {
				$aItem	= explode( '[--1--]', $Item );
				$sProductSKUItem		.= "'".$aItem[0] . "',";
				$aQuantity[$aItem[0]]	 = intval($aItem[1]);
			}
		}
	}	
	$sProductSKUItem			= substr( $sProductSKUItem, 0, strlen($sProductSKUItem) - 1 );
	
	$sOccasion					= $Info['Occasion'];		
	$sCouponCode				= $Info['CouponCode'];	
	$sRecipientCardMessage		= $Info['RecipientCardMessage'];
	$sRecipientSignature		= $Info['RecipientSignature'];		

	
	$sRecipientFullName			= $Info['RecipientFullName'];
	$sRecipientPhoneNumber		= $Info['RecipientPhoneNumber'];
	$sRecipientAddress			= $Info['RecipientAddress'];
	$sRecipientAddress2			= $Info['RecipientAddress2'];
	$sRecipientCity				= $Info['RecipientCity'];
	$sRecipientState			= $Info['RecipientState'];
	$sRecipientZipCode			= $Info['RecipientZipCode'];
	$sRecipientCountry			= $Info['RecipientCountry'];
	$sRecipientFax				= $Info['RecipientFax'];
	$sRecipientEmail			= $Info['RecipientEmail'];

	
	$query 	= "SELECT * FROM jos_users AS U, jos_vm_user_info AS UI WHERE U.id = UI.user_id AND U.username = '{$sPartnerName}' LIMIT 1";
	$row	= $db->select($query);
	$oUser	= $db->get_row( $row, 'MYSQL_ASSOC');
		
	/*print_r($oUser);
	die("aaaaaaaaaaaaa");*/
	if( $oUser ) {		
		$user_info_id		= $oUser['user_info_id'];		
		$user_id			= $oUser['user_id'];
		$bill_company_name 	= $oUser['company']; 
		$bill_last_name 	= $oUser['last_name']; 
		$bill_first_name 	= $oUser['first_name']; 
		$bill_middle_name 	= $oUser['middle_name']; 
		$bill_phone 		= $oUser['phone_1']; 
		$bill_fax 			= $oUser['fax']; 
		$bill_address_1 	= $oUser['address_1']; 
		$bill_address_2 	= $oUser['address_2']; 
		$bill_city 			= $oUser['city']; 
		$bill_state 		= $oUser['state']; 
		$bill_country 		= $oUser['country']; 
		$bill_zip_code 		= $oUser['zip']; 
		$bill_email 		= $oUser['user_email']; 
		$account_email 		= $oUser['user_email']; 		
	}else {
		$user_info_id		= md5($user_id.time());	

		$query 	= "INSERT INTO jos_users( name, 
										 username, 
										 usertype, 
										 block, 
										 gid, 
										 registerDate ) 
			   	   VALUES(  '{$sPartnerName}', 
			   	   			'{$user_name}', 
			   	   			'Registered', 
			   	   			0, 
			   	   			18, 
			   	   			'".date( "Y-m-d H:i:s", time() )."')";
		$user_id	= $db->insert_sql($query);
		
			
		$aPartnerName	= explode( " ", trim($sPartnerName) );
		$query 	= "INSERT INTO jos_vm_user_info( user_info_id, 
												user_id, 
												address_type, 
												address_type_name, 
												company, 
												last_name, 
												first_name, 
												middle_name ) 
			   	   VALUES(  '".md5($user_info_id)."', 
			   	   			{$user_id}, 
			   	   			'BT', 
			   	   			'-default-', 
			   	   			'Pro Flower', 
			   	   			'{$aPartnerName[1]}', 
			   	   			'{$aPartnerName[0]}', 
			   	   			'')";
//		echo (($query));
		$result	= $db->insert_sql($query);
		
		$bill_first_name	= $aPartnerName[0];
		$bill_last_name		= $aPartnerName[1];
//		die($result);
	}
	//die($bill_first_name."=======".$bill_last_name);
	
	
	$occasion					= "CONGR";
	$card_msg					= htmlspecialchars($sRecipientCardMessage, ENT_QUOTES);
	$signature					= $sRecipientSignature;
	$card_comment				= "";
	
	
	$aDeliveryDate				= explode( "-", $sDeliveryDate );
	$deliver_day				= intval(trim($aDeliveryDate[1]));
	$deliver_month				= intval(trim($aDeliveryDate[0]));
	$deliver_year				= intval(trim($aDeliveryDate[2]));
	
	$address_user_name			= "";
	$deliver_company_name 		= ""; 
	$deliver_first_name			= trim(substr( $sRecipientFullName, 0, strpos( trim($sRecipientFullName), " " ) + 1 ));
	$deliver_last_name			= trim(substr( $sRecipientFullName, strpos( trim($sRecipientFullName), " " ) + 1 , strlen(trim($sRecipientFullName)) - strpos( trim($sRecipientFullName), " " ) ));
	$deliver_middle_name		= "";

	$deliver_phone				= $sRecipientPhoneNumber;
	$deliver_cell_phone			= $sRecipientPhoneNumber;
	$deliver_fax				= $sRecipientFax;	
	$deliver_address_1			= $sRecipientAddress;
	$deliver_address_2			= $sRecipientAddress2;
	$deliver_city				= $sRecipientCity;
	$deliver_state				= $sRecipientState;
	$deliver_country			= $sRecipientCountry;	
	$deliver_zip_code			= $sRecipientZipCode;
	$deliver_recipient_email	= $sRecipientEmail;
	
   			
	$payment_method_state		= "";
	$payment_method				= "";
	$name_on_card				= "";
	$credit_card_number			= "";
	$credit_card_security_code	= "";
	$expire_month				= "";
	$expire_year				= "";
	$find_us					= 1;
	
	
	$query 		= "SELECT * FROM jos_vm_tax_rate WHERE tax_country = '{$deliver_country}' AND tax_state = '{$deliver_state}' LIMIT 1";
	$row		= $db->select($query);
	$oStateTax	= $db->get_row( $row, 'MYSQL_ASSOC');	
	$nStateTax	= $oStateTax['tax_rate'];
	
		
	$query				= " SELECT VM.product_id, VM.product_sku, VM.product_name, VMP.product_price, VTR.tax_rate 
							FROM jos_vm_product AS VM INNER JOIN jos_vm_product_price AS VMP ON VM.product_id = VMP.product_id
							INNER JOIN  jos_vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id AND VM.product_sku IN ({$sProductSKUItem})";
	$rows				= $db->select($query);
	while ( $row = $db->get_row($rows, 'MYSQL_ASSOC') ) {
		$sProductId			.=  $row['product_id'] . ",";
		
		$sub_total_price	+= 	round( doubleval($row['product_price']), 2 ) * $aQuantity[$row['product_sku']];
		if( $nStateTax ) {
			$total_tax		+=	round( $nStateTax, 2 ) * ( round( doubleval($row['product_price']), 2 ) * $aQuantity[$row['product_sku']] );	
		}else {
			$total_tax		+=	round( floatval($row['tax_rate']), 2 ) * ( round( doubleval($row['product_price']), 2 ) * $aQuantity[$row['product_sku']] );	
		}
	}
	
	
	$row				= $db->select("SELECT * FROM jos_vm_coupons WHERE coupon_code = '" . $sCouponCode . "'");
	$oCoupon			= $db->get_row( $row, 'MYSQL_ASSOC');	
	$nDiscountValue		= 0;
	if( $oCoupon['coupon_id'] ){
		if( $oCoupon['percent_or_total'] == 'percent' ){
			$sub_total_price	= floatval($sub_total_price) - ( $sub_total_price * (floatval($oCoupon['coupon_value']) / 100) ) ; 
			$nDiscountValue		= $sub_total_price * (floatval($oCoupon['coupon_value']) / 100);
		}else {
			$sub_total_price	= floatval($sub_total_price) - floatval($oCoupon['coupon_value']);
			$nDiscountValue		= floatval($oCoupon['coupon_value']);
		}
	}
	
	$total_price	= doubleval( $sub_total_price + $total_tax );
	$sProductId		= substr( $sProductId, 0, strlen($sProductId) - 1 );
		
	
	$query 	= "SELECT shipping_rate_id, shipping_rate_value, tax_rate FROM jos_vm_shipping_rate INNER JOIN jos_vm_tax_rate ON shipping_rate_vat_id = tax_rate_id ORDER BY shipping_rate_list_order ASC LIMIT 1";
	$row 	= $db->get_row($db->select($query), 'MYSQL_ASSOC');	
	if( $nStateTax ) {
		$total_deliver_tax_fee		= doubleval( $row['shipping_rate_value'] * $nStateTax );
	}else {
		$total_deliver_tax_fee		= doubleval( $row['shipping_rate_value'] * $row['tax_rate'] );	
	}	
	
	$deliver_fee				= doubleval( $row['shipping_rate_value'] + $total_deliver_tax_fee );
	$shipping_method			= $row['shipping_rate_id'];
	
	//Canculate Total Price
	$total_price	= doubleval( $deliver_fee + $total_price );
	
	
	//echo "=======================".$deliver_address_item."=======================";	
	$query 				= "SELECT * FROM jos_vm_vendor WHERE vendor_country = '{$bill_country}'";
	$row 				= $db->get_row($db->select($query), 'MYSQL_ASSOC');	
	$vendor_id			= $row['vendor_id'];
	if( !$vendor_id ) $vendor_id = 1;
	$PaymentVar["vendor_id"]		= $vendor_id;	
	$vendor_currency				= $row['vendor_currency'];
	$PaymentVar["vendor_currency"]	= $vendor_currency;
	//echo "<br/>33. <br/>".$query."<br/>";
	
	
	$query 				= " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id 
							FROM jos_vm_shipping_rate AS VSR
							INNER JOIN jos_vm_shipping_carrier AS VSC
							ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id  
							WHERE VSR.shipping_rate_id = {$shipping_method}";
	$row 				= $db->get_row($db->select($query), 'MYSQL_ASSOC');	
	$sShippingMethod	= "standard_shipping|". implode( "|", $row );
	
	
	$order_tax_details 	= array();
	$query				= " SELECT VMP.product_price, VTR.tax_rate 
							FROM jos_vm_product AS VM 
							LEFT JOIN jos_vm_product_price AS VMP 
							ON VM.product_id = VMP.product_id 
							LEFT JOIN  jos_vm_tax_rate AS VTR 
							ON VM.product_tax_id = VTR.tax_rate_id 
							WHERE VM.product_id IN ({$sProductId})";
	$rows				= $db->select($query);
	while ( $row = $db->get_row($rows, 'MYSQL_ASSOC') ) {
		if( $nStateTax ) {
			if( !isset($order_tax_details[$nStateTax]) ) {
				$order_tax_details[$nStateTax] = doubleval($nStateTax) * doubleval($row['product_price']);
			}else {
				$order_tax_details[$nStateTax] = $order_tax_details[$nStateTax] + (doubleval($nStateTax) * doubleval($row['product_price']));
			}	
		}else {
			if( !isset($order_tax_details[$row['tax_rate']]) ) {
				$order_tax_details[$row['tax_rate']] = doubleval($row['tax_rate']) * doubleval($row['product_price']);
			}else {
				$order_tax_details[$row['tax_rate']] = $order_tax_details[$row['tax_rate']] + (doubleval($row['tax_rate']) * doubleval($row['product_price']));
			}	
		}
	}
			
	
	/* Insert the main order information */
	$order_number	= md5( "order".$user_id.time() );
	
	//================================== PAYMENT =========================================
	$VM_LANG 	=new  vmLanguage();
	
	
	$aResult["order_payment_log"] 	= $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
	$order_status					= "P";
	$payment_msg					= " and ".$VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
	
	//====================================================================================
	$phpShopDeliveryDate	=  date( "M d, Y" ,strtotime($deliver_day."-".$deliver_month."-".$deliver_year) + ($mosConfig_offset*60*60));
	$query 			= "INSERT INTO jos_vm_orders( user_id, 
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
												 username ) 
				   	   VALUES( 	$user_id, 
				   	   			$vendor_id, 
				   	   			'$order_number', 
				   	   			'$user_info_id', 
				   	   			$total_price, 
				   	   			$sub_total_price, 
				   	   			$total_tax, 
				   	   			'".serialize($order_tax_details)."', 
				   	   			$deliver_fee, 
				   	   		   	$total_deliver_tax_fee, 
				   	   		   	$nDiscountValue, 
				   	   		   	'$vendor_currency', 
				   	   		   	'$order_status', 
				   	   		   	".$timestamp.", 
				   	   		   	".$timestamp.", 
				   	   		   	'".($deliver_day."-".$deliver_month."-".date("Y", time()))."', 
				   	   		   	'".$sShippingMethod."', 
						   	   	'".htmlspecialchars(strip_tags($card_msg))."', 
						   	   	'".htmlspecialchars(strip_tags($signature))."', 
						   	   	'".htmlspecialchars(strip_tags($occasion))."', 
						   	   	'".htmlspecialchars(strip_tags($card_comment))."', 
						   	   	'$find_us', 
						   	   	'".htmlspecialchars(strip_tags($user_name))."' )";	
	$order_id	= $db->insert_sql($query);	
	
	if( !$order_id ) {
		return  false;
	}
	
	$sOrderStatus	= $db->select_one("SELECT order_status_name FROM jos_vm_order_status WHERE order_status_code = '" . $order_status . "'");				
	if( $sOrderStatus ){
		$order_status	= $sOrderStatus;
	}	
	$sResultProcess	= $order_id . "[--1--]" . $sub_total_price . "[--1--]" . $total_tax . "[--1--]" . $nDiscountValue . "[--1--]" . $total_price . "[--1--]" . strtoupper($vendor_currency) . "[--1--]" . htmlentities( htmlspecialchars($order_status, ENT_QUOTES), ENT_QUOTES, 'UTF-8' );
	
	
	/*echo $query;
	echo "<br/>4. <br/>".$database->getErrorMsg()."<br/>";
	die($sResultProcess . "===========" .$query);*/
	
	//Mix Info
	$query = "INSERT INTO tbl_xmlorder( partner_order_id, order_id, partner_name, created_date ) VALUES ('$nPartnerOrderID', $order_id, '" . $sPartnerName . "', '" . date("Y-m-d H:i:s") . "' )";
	$isResult	= $db->insert_sql($query);
	if( !$isResult ) {
		return  false;
	}	
	
	/*Insert the initial Order History.*/
	$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
		
	$query = "INSERT INTO jos_vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments) 
				VALUES ('$order_id', 
						'P', 
						'" . $mysqlDatetime . "', 
						1, 
						'')";
	$db->insert_sql($query);
	//echo "<br/>5. <br/>".$database->getErrorMsg()."<br/>";	
	
	
	/*Insert the Order payment info */
	$payment_number = preg_replace("/ |-/", "", $credit_card_number);	

	
	// Payment number is encrypted using mySQL ENCODE function.
	$query 	= "INSERT INTO jos_vm_order_payment(	order_id, 
												order_payment_code, 
												payment_method_id, 
												order_payment_number, 
												order_payment_expire, 
												order_payment_log, 
												order_payment_name, 
												order_payment_trans_id) 
				VALUES ({$order_id}, 
						'{$credit_card_security_code}', 
						3, 
						'{$payment_number}', 
						'".strtotime("{$expire_month}/01/{$expire_year}")."',
						'{$aResult["order_payment_log"]}',
						'{$name_on_card}',			
						'{$aResult["order_payment_trans_id"]}')";
						//$query .= "'" . $vmInputFilter->safeSQL( @$d["order_payment_trans_id"] ). "'";
	$db->insert_sql($query);
	//echo "<br/>6. <br/>".$database->getErrorMsg()."<br/>";
	
	
	/*Insert the User Billto & Shipto Info to Order Information Manager Table*/
	$query 	= "INSERT INTO jos_vm_order_user_info (  order_id, 
													user_id, 
													address_type, 
													address_type_name, 
													company, 
													last_name, 
													first_name, 
													middle_name, 
													phone_1, 
													fax, 
													address_1, 
													address_2, 
													city, 
													state, 
													country, 
													zip,
													user_email ) 
				   	   VALUES(  '".$order_id."', 
				   	   			{$user_id}, 
				   	   			'BT', 
				   	   			'-default-', 
				   	   			'". htmlentities($bill_company_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_last_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_first_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_middle_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_phone, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_fax, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_address_1, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_address_2, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_city, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_state, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_country, ENT_QUOTES)."', 
				   	   			'". htmlentities($bill_zip_code, ENT_QUOTES)."', 
				   	   			'". htmlentities($account_email, ENT_QUOTES)."' )";
	$isResult	= $db->insert_sql($query);
	if( !$isResult ) {
		return  false;
	}	
	//echo "<br/>7. <br/>".$database->getErrorMsg()."<br/>";	
		
	$query 	= "INSERT INTO jos_vm_order_user_info (  order_id, 
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
													user_email ) 
				   	   VALUES(  '".$order_id."', 
				   	   			{$user_id}, 
				   	   			'ST', 
				   	   			'". htmlentities($address_user_name, ENT_QUOTES)."', 
				   	   			'$deliver_company_name', 
				   	   			'". htmlentities($deliver_last_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_first_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_middle_name, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_phone, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_cell_phone, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_fax, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_address_1, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_address_2, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_city, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_state, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_country, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_zip_code, ENT_QUOTES)."', 
				   	   			'". htmlentities($deliver_recipient_email, ENT_QUOTES)."' )";
	$isResult	= $db->insert_sql($query);
	if( !$isResult ) {
		return  false;
	}
	
//	die($query);
	//echo "<br/>8. <br/>".$database->getErrorMsg()."<br/>";
	
	/*Insert all Products from the Cart into order line items*/
	$order_items	= '<table width="100%">';
	$order_items   .= '<tr>
							<td width="5%">No</td>
							<td width="5%">SKU Code</td>
							<td width="50%">Product Name</td>
							<td width="10%">Product Price (Net)</td>
							<td width="5%">Tax</td>
							<td width="10%">Product Price (Gross)</td>
							<td width="5%">Quantity</td>
							<td width="10%">SubTotal</td>
						</tr>';
	
	$phpShopOrderSubtotal	= 0;
	$phpShopOrderTax		= 0;
	$query					= " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate 
								FROM jos_vm_product AS VM LEFT JOIN jos_vm_product_price AS VMP 
								ON VM.product_id = VMP.product_id 
								LEFT JOIN  jos_vm_tax_rate AS VTR 
								ON VM.product_tax_id = VTR.tax_rate_id 
								WHERE VM.product_id IN ({$sProductId})";
	$rows					= $db->select($query);
	
	$j 	= 0;
	while ( $row = $db->get_row($rows, 'MYSQL_ASSOC') ) {	
						
		if( $nStateTax ) {
			$nTaxTemp	= $nStateTax;			
		}else {
			$nTaxTemp	= $row['tax_rate'];
		}
		
		$query 	= "INSERT INTO jos_vm_order_item (   order_id, 
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
													mdate ) 
			   	   VALUES(  {$order_id}, 
			   	   			'{$user_info_id}', 
			   	   			{$vendor_id}, 
			   	   			".$row['product_id'].", 
			   	   			'".addslashes( $row['product_sku'] )."', 
			   	   			'".addslashes( $row['product_name'] )."', 	
			   	   			".intval($aQuantity[$row['product_sku']]).", 
			   	   			".$row['product_price'].", 	
			   	   			".( ($row['product_price'] * $nTaxTemp ) + $row['product_price'] ).", 	
			   	   			'".$row['product_currency']."', 
			   	   			'P', 
			   	   			'".addslashes( $row['product_desc'] )."', 
			   	   			'{$timestamp}', 
			   	   			'{$timestamp}'
			   	   			 )";
		$db->insert_sql($query);
		//echo "<br/>9-1. <br/>".$database->getErrorMsg()."<br/>";
		

		$order_items .= '<tr>
							<td>'.($j+1).'. </td>
							<td>'.addslashes( $row['product_sku'] ).'</td>
							<td>'.addslashes( $row['product_name'] ).'<br/>'.addslashes( $row['product_desc'] ).'</td>
							<td>$'.number_format( $row['product_price'], 2, ".", " " ).'</td>
							<td>'.number_format( $nTaxTemp, 2, ".", " " ).'</td>
							<td>$'.number_format( (($row['product_price'] * $nTaxTemp ) + $row['product_price']), 2, ".", " " ).'</td>
							<td>'.intval($aQuantity[$row['product_sku']]).'</td>
							<td>$'.number_format( (($row['product_price'] * $nTaxTemp) + $row['product_price'])*intval($aQuantity[$row['product_sku']]), 2, ".", " " ).'</td>
						</tr>';
					
		$phpShopOrderSubtotal	+=  $row['product_price'] * intval($aQuantity[$row['product_sku']]);
		//$phpShopOrderTax		+=  ($row['product_price'] * $nTaxTemp )*intval($nQuantityTemp);
		
		
		/* Insert ORDER_PRODUCT_TYPE */
		$query = "SELECT * FROM jos_vm_product_product_type_xref, jos_vm_product_type 
				  WHERE jos_vm_product_product_type_xref.product_id = '".$row['product_id']."' 
				  AND jos_vm_product_product_type_xref.product_type_id = jos_vm_product_type.product_type_id";
		$rows2	= $db->select($query);
		
		while ( $row2 = $db->get_row($rows2, 'MYSQL_ASSOC') ) {	
			$product_type_id = $row2['product_type_id'];
			
			$query = "  SELECT * 
						FROM jos_vm_product_type_$product_type_id 
						WHERE product_id='".$row['product_id']."' ";			
			$item2 = $db->get_row($db->select($query), 'MYSQL_ASSOC');
        		
			$query = "INSERT INTO jos_vm_order_product_type( order_id, 
														product_id, 
														product_type_name, 
														quantity, price) 															
						VALUES ( {$order_id}, 
								 ".$row['product_id']."', 
								 '" . addslashes($item2['product_type_name']) . "', 
								 " . $item2['quantity'] . ", 
								 ".$item2['price']. ")";
           	$db->insert_sql($query);
			//echo "<br/>9-2. <br/>".$database->getErrorMsg()."<br/>";
		}
		
		
		/* Update Stock Level and Product Sales */
		if ( $row['product_in_stock'] ) {
			$query = "	UPDATE jos_vm_product 
						SET product_in_stock = product_in_stock - ".intval($aQuantity[$row['product_sku']])." 
						WHERE product_id = '" . $row['product_id'] . "'";
			$db->update_sql($query);
		}

		$query = "	UPDATE jos_vm_product 
					SET product_sales= product_sales + ".intval($aQuantity[$row['product_sku']])."  
					WHERE product_id='".$row['product_id'] ."'";
		$db->update_sql($query);
		//echo "<br/>9-3. <br/>".$database->getErrorMsg()."<br/>";
		
		
		$j++;
	}
	$order_items	.= '</table>';

	
	/*===================================== Assign Order To The WareHouse =====================================*/
	$query		= "SELECT WH.warehouse_email, WH.warehouse_code FROM jos_vm_warehouse AS WH, jos_postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code = '".substr( $deliver_zip_code, 0, 3 )."'";
	$oWarehouse	= $db->get_row($db->select($query), 'MYSQL_ASSOC');
	$warehouse_code		= $oWarehouse['warehouse_code'];
	$warehouse_email	= $oWarehouse['warehouse_email'];
	
	$query = "UPDATE jos_vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
	$db->update_sql($query);
		
	if ( $warehouse_code ) {
		$mail_Subject 	= $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #".$order_id;
		$mail_Content 	= str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
		
		/*$m = new email ( $mail_Subject ,        // subject
	                	 $mail_Content,           // message body
	                	 $mosConfig_fromname,    // sender's name
	                	 $mosConfig_mailfrom,   // sender's email
	                	 array($warehouse_email)// To: recipients
	                	 //"paul@whereever.com"      // Cc: recipient
	                   );*/
//		$m->send();
	}	
	
		
	$query 	= "SELECT creditcard_name FROM jos_vm_creditcard WHERE creditcard_code = '$payment_method'";
	$payment_info_details	= $db->get_row($db->select($query), 'MYSQL_ASSOC');
	$payment_info_details  .= '<br />Name On Card: '.$name_on_card.'<br />'
							. 'Credit Card Number: '.$credit_card_number.'<br />'
							. 'Expiration Date: '.$expire_month.' / '.$expire_year.'<br />';
	
	$shopper_header 		= 'Thank you for shopping with us.  Your order information follows.';
	$shopper_order_link 	= $mosConfig_live_site."/index.php?page=account.order_details&order_id=$order_id";					
	$shopper_footer_html 	= "<br /><br />Thank you for your patronage.<br />"
						 	. "<br /><a title=\"View the order by following the link below.\" href=\"$shopper_order_link\">View the order by following the link below.</a>"
							. "<br /><br />Questions? Problems?<br />"
							. "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom."\">".$mosConfig_mailfrom."</a>";
							
	$vendor_header			= "The following order was received.";
	$vendor_order_link 		= $mosConfig_live_site."/index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin" ;
	$vendor_footer_html 	= "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";
	
	$vendor_image = "<img src=\"".$mosConfig_live_site."/components/com_virtuemart/shop_image/vendor/".$aVendor->vendor_full_image."\" alt=\"vendor_image\" border=\"0\" />";
	
	/*===================================== Assign Email Content =====================================*/
	$myFile 	= $mosConfig_absolute_path."/administrator/components/com_virtuemart/html/templates/order_emails/email_english.html";
	$fh 		= fopen($myFile, 'r');
	$html 		= fread($fh, filesize($myFile));
	fclose($fh);
	
	$html = str_replace('{phpShopVendorName}', $aVendor->vendor_name ,$html);
	$html = str_replace('{phpShopVendorStreet1}', $aVendor->vendor_phone,$html);
	$html = str_replace('{phpShopVendorStreet2}', $aVendor->vendor_address_1,$html);
	$html = str_replace('{phpShopVendorZip}', $aVendor->vendor_zip, $html);
	$html = str_replace('{phpShopVendorCity}', $aVendor->vendor_city,$html);
	$html = str_replace('{phpShopVendorState}', $aVendor->vendor_state,$html);
	$html = str_replace('{phpShopVendorImage}',$vendor_image,$html);
	$html = str_replace('{phpShopOrderHeader}',"Purchase Order",$html);
	$html = str_replace('{phpShopOrderNumber}',$order_id ,$html);
	$html = str_replace('{phpShopOrderDate}', date( "M d, Y" , $timestamp),$html);
	$html = str_replace('{phpShopDeliveryDate}', $phpShopDeliveryDate, $html);
	$html = str_replace('{phpShopOrderStatus}', $aResult["order_payment_log"], $html);

	$html = str_replace('{phpShopBTCompany}', $bill_company_name, $html);
	$html = str_replace('{phpShopBTName}', $bill_first_name." ".$bill_middle_name." ".$bill_last_name, $html);
	$html = str_replace('{phpShopBTStreet1}', $bill_address_1, $html);
	$html = str_replace('{phpShopBTStreet2}', $bill_address_2, $html);
	$html = str_replace('{phpShopBTCity}', $bill_city, $html);
	$html = str_replace('{phpShopBTState}', $bill_state, $html);
	$html = str_replace('{phpShopBTZip}', $bill_zip_code, $html);
	$html = str_replace('{phpShopBTCountry}', $bill_country, $html);
	$html = str_replace('{phpShopBTPhone}', $bill_phone, $html);
	$html = str_replace('{phpShopBTFax}', $bill_fax, $html);
	$html = str_replace('{phpShopBTEmail}', $account_email, $html);

	$html = str_replace('{phpShopSTCompany}', $address_user_name, $html);
	$html = str_replace('{phpShopSTName}', $deliver_first_name." ".$deliver_middle_name." ".$deliver_last_name,$html);
	$html = str_replace('{phpShopSTStreet1}', $deliver_address_1, $html);
	$html = str_replace('{phpShopSTStreet2}', $deliver_address_2, $html);
	$html = str_replace('{phpShopSTCity}', $deliver_city, $html);
	$html = str_replace('{phpShopSTState}', $deliver_state, $html);
	$html = str_replace('{phpShopSTZip}', $deliver_zip_code, $html);
	$html = str_replace('{phpShopSTCountry}', $deliver_country, $html);
	$html = str_replace('{phpShopSTPhone}', $deliver_phone, $html);
	$html = str_replace('{phpShopSTFax}', $deliver_fax, $html);
	$html = str_replace('{phpShopSTEmail}', "", $html);
	
	$html = str_replace('{phpShopOrderItems}',$order_items,$html);	
	$html = str_replace('{phpShopOrderSubtotal}',"$".number_format($phpShopOrderSubtotal, 2, '.', ' '),$html);	
	$html = str_replace('{phpShopOrderShipping}',"$".number_format(($deliver_fee - $total_deliver_tax_fee), 2, '.', ' '),$html);
	$html = str_replace('{phpShopOrderTax}', "$".number_format($total_tax + $total_deliver_tax_fee, 2, '.', ' ') , $html );
	$html = str_replace('{phpShopOrderTotal}',"$".number_format($total_price, 2, '.', ' ') ,$html);

	$html = str_replace('{phpShopOrderDisc1}',(isset($order_disc1) ? $order_disc1 : ""), $html);
	$html = str_replace('{phpShopOrderDisc2}',(isset($order_disc1) ? $order_disc2 : ""), $html);
	$html = str_replace('{phpShopOrderDisc3}',(isset($order_disc1) ? $order_disc3 : ""), $html);
	$html = str_replace('{phpShopCustomerNote}',htmlspecialchars(strip_tags($card_msg)), $html);
	$html = str_replace('{phpShopCustomerSignature}',htmlspecialchars(strip_tags($signature)), $html);
	$html = str_replace('{phpShopCustomerInstructions}',htmlspecialchars(strip_tags($card_comment)), $html);
	$html = str_replace('{PAYMENT_INFO_LBL}', "Payment Information", $html);
	$html = str_replace('{PAYMENT_INFO_DETAILS}', $payment_info_details, $html);
	$html = str_replace('{SHIPPING_INFO_LBL}', "Delivery Information", $html);	
	$html = str_replace('{SHIPPING_INFO_DETAILS}', $aShippingMethod[0]." (".$aShippingMethod[1].")", $html);

	$shopper_html 		= str_replace('{phpShopOrderHeaderMsg}', $shopper_header, $html);
	$shopper_html 		= str_replace('{phpShopOrderClosingMsg}',$shopper_footer_html, $shopper_html);	
	$shopper_subject 	= $aVendor->vendor_name . " Purchase Order - " . $order_id;
	
	
	
   /*$m = new email ( $shopper_subject ,        // subject
                	 $shopper_html,           // message body
                	 $mosConfig_fromname,    // sender's name
                	 $mosConfig_mailfrom,   // sender's email
                	 array($account_email)// To: recipients
               	   );*/
//	$m->send();
	
	return $sResultProcess;
}


 function validateDate( $date, $format='YYYY-MM-DD') {
    switch( $format ) {
        case 'YYYY/MM/DD':
        case 'YYYY-MM-DD':
        list( $y, $m, $d ) = preg_split( '/[-\.\/ ]/', $date );
        break;

        case 'YYYY/DD/MM':
        case 'YYYY-DD-MM':
        list( $y, $d, $m ) = preg_split( '/[-\.\/ ]/', $date );
        break;

        case 'DD-MM-YYYY':
        case 'DD/MM/YYYY':
        list( $d, $m, $y ) = preg_split( '/[-\.\/ ]/', $date );
        break;

        case 'MM-DD-YYYY':
        case 'MM/DD/YYYY':
        list( $m, $d, $y ) = preg_split( '/[-\.\/ ]/', $date );
        break;

        case 'YYYYMMDD':
        $y = substr( $date, 0, 4 );
        $m = substr( $date, 4, 2 );
        $d = substr( $date, 6, 2 );
        break;

        case 'YYYYDDMM':
        $y = substr( $date, 0, 4 );
        $d = substr( $date, 4, 2 );
        $m = substr( $date, 6, 2 );
        break;

        default:
        throw new Exception( "Invalid Date Format" );
    }
    return checkdate( $m, $d, $y );
}

?> 