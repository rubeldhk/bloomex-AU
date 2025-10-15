<?php
/**
* @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
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

global $mosConfig_absolute_path;

require_once( $mainframe->getPath( 'front_html' ) );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/virtuemart.cfg.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/language.class.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/english.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/Log/Log.php" );

$cid 	= josGetArrayInts( 'cid' );
$step	= 0;
$stack	= array();

/*switch ($task) {				
	case 'save_order_xml':
		saveXMLOrder( $option );
		break;

	default:
		makeXMLOrder( $option );				
		break;
}*/


function getMonthDays($Month, $Year) {
	if( is_callable("cal_days_in_month")) { 
		return cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
	}else{
		return date("d",mktime(0,0,0,$Month+1,0,$Year));
	}
}
					
					
function lisDay( $list_name, $selected_item="", $extra="") {
	$sString	= "";
	$list 		= array("DAY","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
	
	$sString	= "<select name='{$list_name}' {$extra}>";
	foreach ($list as $value) {
		$sString	.= "<option value='{$value}'>{$value}</option>";
	}
	
	$sString	.= "</select>";
	return $sString;	
}


function listMonth($list_name, $selected_item="", $extra="") {
	$sString	= "";
	$list = array("" => "Month",
	"01" => "January",
	"02" => "February",
	"03" => "March",
	"04" => "April",
	"05" => "May",
	"06" => "June",
	"07" => "July",
	"08" => "August",
	"09" => "September",
	"10" => "October",
	"11" => "November",
	"12" => "December");
	
	$sString	= "<select name='{$list_name}' {$extra}>";
	foreach ($list as $key => $value) {
		$sString	.= "<option value='{$key}'>{$value}</option>";
	}
	
	$sString	.= "</select>";
	return $sString;	
}


function listYear($list_name, $selected_item="", $extra="", $max = 7, $from = 2009, $direct = "up" ) {
	$sString	= "";
		
	$sString	= "<select name='{$list_name}' {$extra}>";
	for ( $i = 0; $i < $max; $i++ ) {
		$value		= $from + $i;
		$text		= $from + $i;
		if( $selected_item == $value ) {
			$sString	.= "<option selected value='".$value."'>".$text."</option>";
		}else{
			$sString	.= "<option value='".$value."'>".$text."</option>";
		}
	}
	
	$sString	.= "</select>";
	return $sString;	
}


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
	$xml_parser 	= xml_parser_create("utf-8");
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



function makeXMLOrder(  $option ) {
	global $database, $my, $mosConfig_absolute_path, $act, $mosConfig_offset;
	
	if( !$my->id ) mosRedirect( "index.php?option=com_login" );
	
	$aArrayInformation	= array();
	
	$query 	= "SELECT name, options FROM tbl_options WHERE type='deliver_option' ORDER BY name DESC ";
	$database->setQuery($query);
	$aInfomation["unavailable"]	= $database->loadObjectList();
		
	
	$query 	= "SELECT shipping_rate_name FROM #__vm_shipping_rate ORDER BY shipping_rate_name ASC";
	$database->setQuery($query);
	$aInfomation["shipping"]	= $database->loadObjectList();
	
	HTML_PhoneOrder::makeXMLOrder( $option, $aInfomation );
}


function saveXMLOrder(  $option ) {
	global $database, $my, $mosConfig_absolute_path, $act, $mosConfig_offset;
	$timestamp = time() + ($mosConfig_offset*60*60);
	$PaymentVar	= array();
	
	
	if( $_FILES["xml_file"]["name"] ) {
		//$sXmlFile	= do_upload( $_FILES["xml_file"], $mosConfig_absolute_path."/media/" );
	}else {
		$msg	= "This upload process is wrong! Please try again!";
	}
	
	if( $sXmlFile ) {
		$aData			= xml_to_array( $mosConfig_absolute_path."/media/".$sXmlFile );
	
		$user_id					= 0;
		$account_email				= trim($aData[0]["children"][0]["cdata"]);
		$user_name					= trim($aData[0]["children"][1]["cdata"]);
		
		/*$bill_company_name			= $aData[0]["children"][1]["children"][0]["cdata"];
		$bill_first_name			= $aData[0]["children"][1]["children"][1]["cdata"];
		$bill_last_name				= $aData[0]["children"][1]["children"][2]["cdata"];
		$bill_middle_name			= $aData[0]["children"][1]["children"][3]["cdata"];
		$bill_address_1				= $aData[0]["children"][1]["children"][4]["cdata"];
		$bill_address_2				= $aData[0]["children"][1]["children"][5]["cdata"];
		$bill_city					= $aData[0]["children"][1]["children"][6]["cdata"];
		$bill_zip_code				= $aData[0]["children"][1]["children"][7]["cdata"];
		$bill_country				= $aData[0]["children"][1]["children"][8]["cdata"];
		$bill_state					= $aData[0]["children"][1]["children"][9]["cdata"];
		$bill_phone					= $aData[0]["children"][1]["children"][10]["cdata"];
		$bill_evening_phone			= $aData[0]["children"][1]["children"][11]["cdata"];
		$bill_fax					= $aData[0]["children"][1]["children"][12]["cdata"];*/
		
		//print_r($aData[0]["children"][2]["children"]);
		$address_user_name			= trim($aData[0]["children"][2]["children"][0]["cdata"]);
		$deliver_first_name			= trim($aData[0]["children"][2]["children"][1]["cdata"]);
		$deliver_last_name			= trim($aData[0]["children"][2]["children"][2]["cdata"]);
		$deliver_middle_name		= trim($aData[0]["children"][2]["children"][3]["cdata"]);
		$deliver_address_1			= trim($aData[0]["children"][2]["children"][4]["cdata"]);
		$deliver_address_2			= trim($aData[0]["children"][2]["children"][5]["cdata"]);
		$deliver_city				= trim($aData[0]["children"][2]["children"][6]["cdata"]);
		$deliver_zip_code			= trim($aData[0]["children"][2]["children"][7]["cdata"]);
		$deliver_country			= trim($aData[0]["children"][2]["children"][8]["cdata"]);
		$deliver_state				= trim($aData[0]["children"][2]["children"][9]["cdata"]);
		$deliver_phone				= trim($aData[0]["children"][2]["children"][10]["cdata"]);
		$deliver_cell_phone			= trim($aData[0]["children"][2]["children"][11]["cdata"]);
		$deliver_evening_phone		= trim($aData[0]["children"][2]["children"][12]["cdata"]);	
		$deliver_fax				= trim($aData[0]["children"][2]["children"][13]["cdata"]);
		$deliver_recipient_email	= trim($aData[0]["children"][2]["children"][14]["cdata"]);		
		$shipping_method			= trim($aData[0]["children"][2]["children"][15]["cdata"]);
		$deliver_day				= intval($aData[0]["children"][2]["children"][16]["cdata"]);
		$deliver_month				= intval($aData[0]["children"][2]["children"][17]["cdata"]);
		$card_msg					= trim($aData[0]["children"][2]["children"][18]["cdata"]);
		$signature					= trim($aData[0]["children"][2]["children"][19]["cdata"]);
		$card_comment				= trim($aData[0]["children"][2]["children"][20]["cdata"]);
				
		
		$payment_method				= trim($aData[0]["children"][3]["children"][0]["cdata"]);
		$name_on_card				= trim($aData[0]["children"][3]["children"][1]["cdata"]);
		$credit_card_number			= trim($aData[0]["children"][3]["children"][2]["cdata"]);
		$credit_card_security_code	= trim($aData[0]["children"][3]["children"][3]["cdata"]);
		$expire_month				= trim($aData[0]["children"][3]["children"][4]["cdata"]);
		$expire_year				= trim($aData[0]["children"][3]["children"][5]["cdata"]);
		
		$aProduct	= array();
		$aSKU		= array();
		$i			= 0;
		foreach ($aData[0]["children"][4]["children"] as $value) {	
			$sSKUCode		= trim($aData[0]["children"][4]["children"][$i]["cdata"]);
			$nQuantityItem	= intval(trim($aData[0]["children"][4]["children"][$i]["attrs"]["QUANTITY"][0]));
			
			if( $nQuantityItem <= 0 ) $nQuantityItem = 1;
			
			if( $sSKUCode ) {
				$aProduct[$sSKUCode] 	= $nQuantityItem;
				$aSKU[$i]				= "'".trim($sSKUCode)."'";
				$i++;
			}
		}
		$sSKU	= implode( ",", $aSKU );
		
		//======================================================================================================================================		
		/*Select user account*/
		
		$query 		= "SELECT * FROM #__vm_user_info AS VUI, #__users AS U WHERE VUI.user_id = U.id AND U.username ='{$user_name}'  AND U.email = '{$account_email}' AND VUI.address_type = 'BT'";
		$database->setQuery($query);
		$oUserInfo	= $database->loadObjectList();
		$oUser		= $oUserInfo[0];	
//		echo "<br/>1.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
   			

		if( $oUser ) {
			$user_id			= $oUser->user_id;
			$bill_company_name 	= $oUser->company; 
   			$bill_last_name 	= $oUser->last_name; 
   			$bill_first_name 	= $oUser->first_name; 
   			$bill_middle_name 	= $oUser->middle_name; 
   			$bill_phone 		= $oUser->phone_1; 
   			$bill_fax 			= $oUser->fax; 
   			$bill_address_1 	= $oUser->address_1; 
   			$bill_address_2 	= $oUser->address_2; 
   			$bill_city 			= $oUser->city; 
   			$bill_state 		= $oUser->state; 
   			$bill_country 		= $oUser->country; 
   			$bill_zip_code 		= $oUser->zip; 
   			$bill_email 		= $oUser->user_email; 
   			
			
			/*Insert the User Billto & Shipto Info to User Information Manager Table*/
			$user_info_id	= md5($user_id.time());	
			
			
			$query	= "	SELECT user_info_id FROM #__vm_user_info 
						WHERE address_type = 'ST' 
						AND address_type_name = '{$address_user_name}' 
						AND ( address_1 = '{$deliver_address_1}' || address_2 = '{$deliver_address_2}' ) 
						AND city = '{$deliver_city}' 
						AND state = '{$deliver_state}' 
						AND country = '{$deliver_country}' 
						AND zip = '{$deliver_zip_code}'";
			$database->setQuery($query);
			$oUserInfo	= $database->loadObjectList();
			$oUser		= $oUserInfo[0];
//			echo "<br/>2.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

			
			if( !$oUser ) {
				$query 	= "INSERT INTO #__vm_user_info( user_info_id, 
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
					   	   VALUES(  '".md5($user_info_id)."', 
					   	   			{$user_id}, 
					   	   			'ST', 
					   	   			'{$address_user_name}', 
					   	   			'', 
					   	   			'{$deliver_last_name}', 
					   	   			'{$deliver_first_name}', 
					   	   			'{$deliver_middle_name}', 
					   	   			'{$deliver_phone}', 
					   	   			'{$deliver_cell_phone}', 
					   	   			'{$deliver_fax}', 
					   	   			'{$deliver_address_1}', 
					   	   			'{$deliver_address_2}', 
					   	   			'{$deliver_city}', 
					   	   			'{$deliver_state}', 
					   	   			'{$deliver_country}', 
					   	   			'{$deliver_zip_code}', 
					   	   			'{$deliver_recipient_email}' )";
				$database->setQuery($query);
				$database->query();	
//				echo "<br/>3.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			}else{				
				/*$address_user_name		= $oUser->address_type_name;
	   			$deliver_last_name		= $oUser->last_name;
	   			$deliver_first_name		= $oUser->first_name;
	   			$deliver_middle_name	= $oUser->middle_name;
	   			$deliver_phone			= $oUser->phone_1;
	   			$deliver_cell_phone		= $oUser->phone_2;
	   			$deliver_fax			= $oUser->fax;	
	   			$deliver_address_1		= $oUser->address_1;
	   			$deliver_address_2		= $oUser->address_2;
	   			$deliver_city			= $oUser->city;
	   			$deliver_state			= $oUser->state;
	   			$deliver_country		= $oUser->country;
	   			$deliver_zip_code		= $oUser->zip;
	   			$deliver_recipient_email= $oUser->user_email;*/
			}
			
			
			$query 				= "SELECT * FROM #__vm_vendor WHERE vendor_country = '{$bill_country}'";
			$database->setQuery( $query );
			$rows 				= $database->loadObjectList();	
			$aVendor			= $rows[0];
			$vendor_id			= $aVendor->vendor_id;
			if( !$vendor_id ) $vendor_id = 1;
			$PaymentVar["vendor_id"]		= $vendor_id;
			$vendor_currency				= $aVendor->vendor_currency;	
			$PaymentVar["vendor_currency"]	= $vendor_currency;

			
			$query 				= " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id 
									FROM #__vm_shipping_rate AS VSR
									INNER JOIN #__vm_shipping_carrier AS VSC
									ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id  
									WHERE VSR.shipping_rate_name  = '{$shipping_method}'";
			$database->setQuery( $query );
			$aShippingMethod	= $database->loadRow();
			$sShippingMethod	= "standard_shipping|". implode( "|", $aShippingMethod );
//			echo "<br/>5.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

			
			$query				= " SELECT VMP.product_price, VM.product_sku, VTR.tax_rate 
									FROM #__vm_product AS VM 
									LEFT JOIN #__vm_product_price AS VMP 
									ON VM.product_id = VMP.product_id 
									LEFT JOIN  #__vm_tax_rate AS VTR 
									ON VM.product_tax_id = VTR.tax_rate_id 
									WHERE VM.product_sku IN ({$sSKU})";
			$database->setQuery($query);
			$rows				= $database->loadObjectList();
//			echo "<br/>6.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

			
			/*==========================Canculate Tax and Total Order==========================*/
			$nTotalPrice		= 0;			
			$nSubTotalPrice		= 0;			
			$nTotalTax			= 0;			
			$order_tax_details = array();
			
			/*print_r($rows);
			echo "<br/><br/>";
			print_r($aProduct);
			echo "<br/><br/>";			
			echo $aProduct['CO92']."aaaaaaaaaaa";*/
			
			if( count($rows) ) {
				foreach ($rows as $value) {
					if( !isset($order_tax_details[$value->tax_rate]) ) {
						$order_tax_details[$value->tax_rate] = doubleval($value->tax_rate) * doubleval($value->product_price);
					}else {
						$order_tax_details[$value->tax_rate] = $order_tax_details[$value->tax_rate] + (doubleval($value->tax_rate) * doubleval($value->product_price));
					}		
					
					$nItemTax		 = doubleval($value->tax_rate) * doubleval($value->product_price);
					$nTotalTax		+= $nItemTax * $aProduct[$value->product_sku];
					$nSubTotalPrice	+=  ($nItemTax + doubleval($value->product_price)) * $aProduct[$value->product_sku];
					//echo $nItemTax."===".$nSubTotalPrice."===".$value->tax_rate."===".$value->product_price."===".$aProduct[$value->product_sku]."<br/><br/>";
				}
			}
			
			/*==========================Canculate Deliver Fee==========================*/
			$nDeliverSameDayFee		= 0;
			$nSpecialDeliver		= 0;
			$nDeliverPostalCodeFee	= 0;
			$nDeliverMethodFee		= 0;
			$nDeliverFee			= 0;
			$nDeliverFeeTax			= 0;
			
			
			$query 							= "SELECT options FROM tbl_options WHERE type='cut_off_time'";
			$database->setQuery($query);
			$sOptionParam					= $database->loadResult();
			$aOptionParam					= explode( "[--1--]", $sOptionParam );
			$time_limit						= $aOptionParam[0]*60 + $aOptionParam[1];
//			echo "<br/>6.1.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			
						
			if( intval($aOptionParam[0]) >= 12 ) {               			
				$aInfomation['time'] 		= ( intval($aOptionParam[0]) - 12 ).":".$aOptionParam[1]." PM";
			}else {
				$aInfomation['time'] 		= intval( $aOptionParam[0] ).":".$aOptionParam[1]." AM";
			}
			
			
			$day_now		= intval(date('j',time()));
			$month_now		= intval(date('m',time()));
			$year_now		= intval(date('Y',time()));
			$hour_now		= intval(date('H',time()));
			$minute_now		= intval(date('i',time()));
			$time_now		= $hour_now*60 + $minute_now;
			
			//die( $month_now . "========" . $deliver_month  . "========" . $day_now  . "========" . $deliver_day );
			
			if( ( $month_now > $deliver_month && $month_now != 12 ) || 	( $day_now > $deliver_day && $month_now == $deliver_month )	 ) {
				mosRedirect( "index2.php?option=$option&act=$act", "Your deliver date is incorrect! Please choose other deliver date again!" );
			}
			
			$query 	= "SELECT id FROM tbl_options WHERE type='deliver_option' AND name ='{$deliver_month}/{$deliver_day}' ";
			$database->setQuery($query);
			$bUnvailableDate	= intval($database->loadResult());
			
			if( $bUnvailableDate ) {
				mosRedirect( "index2.php?option=$option&act=$act", "Our service is not available with this deliver date! Please choose other deliver date again!" );
			}
			
			
			//echo $nTimeNow."===".$nHourNow."===".$nMinuteNow."===".$nTimeLimit;
			if( $time_now >= $time_limit ) {
				$cut_off_time = 1;
			}else {
				$cut_off_time 		= 0;
				if( $day_now == $deliver_day && $month_now == $deliver_month ) {
					$nDeliverSameDayFee	= floatval($aOptionParam[2]);
				}
			}
			
			
			$query 	= "SELECT options FROM tbl_options WHERE type='special_deliver' AND name ='{$deliver_month}/{$deliver_day}' ";
			$database->setQuery($query);
			$nSpecialDeliver 	= floatval($database->loadResult());
//			echo "<br/>7.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			

			        
		    $query 			= "SELECT options FROM tbl_options WHERE type='postal_code' AND name ='{$deliver_zip_code}' ";
			$database->setQuery($query);
			$sPostalCode 	= $database->loadResult();
			$aPostalCode 	= explode( "[--1--]", $sPostalCode );
			$nDeliverPostalCodeFee	= floatval( $aPostalCode[2] );			
//			echo "<br/>8.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

			
			$query 				= " SELECT shipping_rate_id, shipping_rate_value, tax_rate, shipping_rate_id, shipping_rate_name 
									FROM #__vm_shipping_rate 
									INNER JOIN #__vm_tax_rate 
									ON shipping_rate_vat_id = tax_rate_id 
									WHERE shipping_rate_name  = '{$shipping_method}'";
			$database->setQuery($query);
			$oShippingRateInfo	= $database->loadObjectList();	
			$aShippingRateInfo	= $oShippingRateInfo[0];
			$nDeliverMethodFee	= floatval($aShippingRateInfo->shipping_rate_value);
//			echo "<br/>9.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

			
			$nDeliverFee		= $nDeliverSameDayFee + $nSpecialDeliver + $nDeliverPostalCodeFee + $nDeliverMethodFee;
			$nDeliverFeeTax		= ( $nDeliverFee * $aShippingRateInfo->tax_rate );
			$nDeliverFee		= $nDeliverFeeTax + $nDeliverFee;
			
			$nTotalPrice		= $nSubTotalPrice + $nDeliverFee;
		
			
			/*die( "Total Price:".$nTotalPrice . "====Sub Total Price: " . $nSubTotalPrice . "====Total Tax: " . $nTotalTax  . "====Deliver Fee: " . $nDeliverFee   . "====Deliver Fee Tax: " . $nDeliverFeeTax  
			. "===Deliver Same Day Fee: " . $nDeliverSameDayFee. "===Special Deliver: " . $nSpecialDeliver. "===Deliver Postal Code Fee: " . $nDeliverPostalCodeFee. "===Deliver Method Fee:" . $nDeliverMethodFee );*/
			
			/* Insert the main order information */
			$order_number	=  md5( "order".$user_id.time() );
			
			//================================== PAYMENT =========================================
			$VM_LANG 	=new  vmLanguage();
			$PaymentVar["user_id"]				= $user_id;	
			$PaymentVar["bill_company_name"]	= $bill_company_name;
			$PaymentVar["bill_last_name"]		= $bill_last_name;
			$PaymentVar["bill_first_name"]		= $bill_first_name;
			$PaymentVar["bill_middle_name"]		= $bill_middle_name;
			$PaymentVar["bill_phone"]			= $bill_phone;
			$PaymentVar["bill_fax"]				= $bill_fax;
			$PaymentVar["bill_address_1"]		= $bill_address_1;
			$PaymentVar["bill_address_2"]		= $bill_address_2;
			$PaymentVar["bill_city"]			= $bill_city;
			$PaymentVar["bill_state"]			= $bill_state;
			$PaymentVar["bill_country"]			= $bill_country;
			$PaymentVar["bill_zip_code"]		= $bill_zip_code;
			$PaymentVar["bill_email"]			= $bill_email;
			$PaymentVar["expire_month"]			= $expire_month;
			$PaymentVar["expire_year"]			= $expire_year;
			$PaymentVar["order_payment_number"]	= $credit_card_number;
			$PaymentVar["credit_card_code"]		= $credit_card_security_code;
			
			$aResult	= array();
			if ( !process_payment( $order_number, $total_price , $PaymentVar, $aResult ) ) {
				$aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
			}
			
			if( $aResult["approved"] ) {
				$order_status	= "A";
				$payment_msg	= " and Payment Approved";
			}else {
				$order_status	= "P";
				$payment_msg	= " and Payment Failed";
			}
			
			//====================================================================================
			
			
			$query 			= "INSERT INTO #__vm_orders( user_id, 
														 vendor_id, 
														 order_number, 
														 user_info_id, 
														 order_total, 
														 order_subtotal, 
														 order_tax, 
														 order_tax_details, 
														 order_shipping, 
														 order_shipping_tax, 
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
														 username ) 
						   	   VALUES( 	{$user_id}, 
						   	   			{$vendor_id}, 
						   	   			'{$order_number}', 
						   	   			'{$user_info_id}', 
						   	   			{$nTotalPrice}, 
						   	   			{$nSubTotalPrice}, 
						   	   			{$nTotalTax}, 
						   	   			'".serialize($order_tax_details)."', 
						   	   			{$nDeliverFee}, 
						   	   		   	{$nDeliverFeeTax}, 
						   	   		   	'{$vendor_currency}', 
						   	   		   	'$order_status', 
						   	   		   	".$timestamp.", 
						   	   		   	".$timestamp.", 
						   	   		   	'".($deliver_day."-".$deliver_month."-".date("Y", time()))."', 
						   	   		   	'".$sShippingMethod."', 
								   	   	'".htmlspecialchars(strip_tags($card_msg))."', 
								   	   	'".htmlspecialchars(strip_tags($signature))."', 
								   	   	'00000', 
								   	   	'".htmlspecialchars(strip_tags($card_comment))."', 
								   	   	'".htmlspecialchars(strip_tags($user_name))."' )";
			$database->setQuery($query);
			$database->query();	
			$order_id	= $database->insertid();
//			echo "<br/>10.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

			
			/*Insert the initial Order History.*/
                        date_default_timezone_set('Australia/Sydney');
			$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
				
			$query = "INSERT INTO #__vm_order_history(	order_id,
														order_status_code,
														date_added,
														customer_notified,
														comments) 
						VALUES ('$order_id', 
								'P', 
								'" . $mysqlDatetime . "', 
								1, 
								'')";
			$database->setQuery($query);
			$database->query();	
//			echo "<br/>11.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			
			
			/*Insert the Order payment info */
			$payment_number = preg_replace("/ |-/", "", $credit_card_number);
		
			
			// Payment number is encrypted using mySQL ENCODE function.
			$query 	= "INSERT INTO #__vm_order_payment(	order_id, 
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
								'',
								'{$name_on_card}',			
								'')";
								//$query .= "'" . $vmInputFilter->safeSQL( @$d["order_payment_trans_id"] ). "'";
			$database->setQuery($query);
			$database->query();	
//			echo "<br/>12.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			
			/*Insert the User Billto & Shipto Info to Order Information Manager Table*/
			$query 	= "INSERT INTO #__vm_order_user_info (  order_id, 
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
						   	   			'{$bill_company_name}', 
						   	   			'{$bill_last_name}', 
						   	   			'{$bill_first_name}', 
						   	   			'{$bill_middle_name}', 
						   	   			'{$bill_phone}', 
						   	   			'{$bill_fax}', 
						   	   			'{$bill_address_1}', 
						   	   			'{$bill_address_2}', 
						   	   			'{$bill_city}', 
						   	   			'{$bill_state}', 
						   	   			'{$bill_country}', 
						   	   			'{$bill_zip_code}', 
						   	   			'{$bill_email}' )";
			$database->setQuery($query);
			$database->query();	
//			echo "<br/>13.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			
			
			$query 	= "INSERT INTO #__vm_order_user_info (  order_id, 
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
						   	   			'{$address_user_name}', 
						   	   			'', 
						   	   			'{$deliver_last_name}', 
						   	   			'{$deliver_first_name}', 
						   	   			'{$deliver_middle_name}', 
						   	   			'{$deliver_phone}', 
						   	   			'{$deliver_cell_phone}', 
						   	   			'{$deliver_fax}', 
						   	   			'{$deliver_address_1}', 
						   	   			'{$deliver_address_2}', 
						   	   			'{$deliver_city}', 
						   	   			'{$deliver_state}', 
						   	   			'{$deliver_country}', 
						   	   			'{$deliver_zip_code}', 
						   	   			'{$deliver_recipient_email}' )";
			$database->setQuery($query);
			$database->query();	
//			echo "<br/>14.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			
			/*Insert all Products from the Cart into order line items*/
			$query				= " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate 
									FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
									ON VM.product_id = VMP.product_id 
									LEFT JOIN  #__vm_tax_rate AS VTR 
									ON VM.product_tax_id = VTR.tax_rate_id 
									WHERE  VM.product_sku IN ({$sSKU})";
			$database->setQuery($query);
			$rows				= $database->loadObjectList();	
//			echo "<br/>15.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
			
			if( count($rows) ) {
				foreach ($rows as $value) {		
					$nQuantityTemp	= $aProduct[$value->product_sku];
					
					if( !$nQuantityTemp ) $nQuantityTemp = 1;
					
					$query 	= "INSERT INTO #__vm_order_item (   order_id, 
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
						   	   			".$value->product_id.", 
						   	   			'".addslashes( $value->product_sku )."', 
						   	   			'".addslashes( $value->product_name )."', 	
						   	   			".intval($nQuantityTemp).", 
						   	   			".$value->product_price.", 	
						   	   			".( ($value->product_price * $value->tax_rate ) + $value->product_price ).", 	
						   	   			'".$value->product_currency."', 
						   	   			'P', 
						   	   			'".addslashes( $value->product_desc )."', 
						   	   			'{$timestamp}', 
						   	   			'{$timestamp}'
						   	   			 )";
					$database->setQuery($query);
					$database->query();
//					echo "<br/>16.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";
					
					
					/* Insert ORDER_PRODUCT_TYPE */
					$query = "SELECT * FROM #__vm_product_product_type_xref, #__vm_product_type 
							  WHERE #__vm_product_product_type_xref.product_id = '".$value->product_id."' 
							  AND #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id";
					$database->setQuery($query);
					$rows2	= $database->loadObjectList();	
					
					foreach ($rows2 as $item) {
						$product_type_id = $item->product_type_id;
						
						$query = "  SELECT * 
									FROM #__vm_product_type_$product_type_id 
									WHERE product_id='".$value->product_id."' ";
						$database->setQuery($query);
						$rows3	= $database->loadObjectList();	
						$item2	= $rows3[0];	
			        		
						$query = "INSERT INTO #__vm_order_product_type( order_id, 
																	product_id, 
																	product_type_name, 
																	quantity, price) 															
									VALUES ( {$order_id}, 
											 ".$value->product_id."', 
											 '" . addslashes($item2->product_type_name) . "', 
											 " . $item2->quantity . ", 
											 ".$item2->price. ")";
			           	$database->setQuery($query);
						$database->query();						
					}
					
					
					/* Update Stock Level and Product Sales */
					if ( $value->product_in_stock ) {
						$query = "	UPDATE #__vm_product 
									SET product_in_stock = product_in_stock - ".intval($nQuantityTemp)." 
									WHERE product_id = '" . $value->product_id . "'";
						$database->setQuery($query);
						$database->query($query);
					}
		
					$query = "	UPDATE #__vm_product 
								SET product_sales= product_sales + ".intval($nQuantityTemp)."  
								WHERE product_id='".$value->product_id ."'";
					$database->query($query);
					
				}
			}
			
			$msg	= "Save Order Successfully $payment_msg";
		}else {
			$msg	= "This user info is not exist in our system! Please try again!";
		}
	}else {
		$msg	= "This upload process is wrong! Please try again!";
	}
	
	//echo $msg;
	mosRedirect( "index.php?option=$option", $msg );
}

//========================================================================================
function do_upload( $file, $dest_dir ) {
	global $clearUploads;
	
	
	if( $act ) {
		$act	= "&act=$act";
	}

	$format = substr( $file['name'], -3 );

	$allowable = array ( 'xml', 'XML' );

    $noMatch = 0;
	foreach( $allowable as $ext ) {
		if ( strcasecmp( $format, $ext ) == 0 ) {
			$noMatch = 1;
		}
	}
	
    if(!$noMatch){
		return false;
    }

    $sFileName	= strtolower(time().".$format");
	if ( !move_uploaded_file($file['tmp_name'], $dest_dir.$sFileName) ){
		return false;
	} 
	
	$clearUploads = true;
	return $sFileName;
}
?>
