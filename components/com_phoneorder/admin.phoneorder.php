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
defined('_VALID_MOS') or die('Restricted access');
/* if (!$acl->acl_check( 'manager', 'manage', 'users', $my->usertype, 'components', 'com_users' )) {
  mosRedirect( 'index2.php', _NOT_AUTH );
  } */

global $mosConfig_absolute_path;

require_once( $mainframe->getPath('admin_html') );
require_once( $mainframe->getPath('class') );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/language.class.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/languages/english.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/Log/Log.php" );


$cid = josGetArrayInts('cid');
$act = mosGetParam($_REQUEST, "act", "");
$step = 0;
$stack = array();


switch ($act) {
    case "xml_order":
        switch ($task) {
            case 'save_order_xml':
                saveXMLOrder($option);
                break;

            default:
                makeXMLOrder($option);
                break;
        }
        break;

    default:
        switch ($task) {
            case 'check_account_info':
                checkAccountInfo($option);
                break;

            case 'check_counpon_code':
                checkCounponCode($option);
                break;

            case 'getsate':
                getState($option);
                break;

            case 'save':
                savePhoneOrder($option);
                break;

            case 'save_order_success':
                HTML_PhoneOrder::savePhoneOrderSuccess($option);
                break;

            default:
                makePhoneOrder($option);
                break;
        }
        break;
}

function getState($option) {
    global $database;
    $selector_id = trim(mosGetParam($_POST, "selector_id", ""));
    $country_id = trim(mosGetParam($_POST, "country_id", ""));


    $query = " SELECT S.state_2_code, S.state_name 
				FROM #__vm_state S INNER JOIN #__vm_country AS C 
				ON C.country_id	= S.country_id 
				WHERE C.country_3_code = '$country_id'
				ORDER BY S.state_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if (count($rows)) {
        echo mosHTML::selectList($rows, "$selector_id", "size='1'", "state_2_code", "state_name");
    } else {
        echo "error";
    }

    exit(0);
}

function makePhoneOrder($option) {
    global $database, $my, $mosConfig_absolute_path;
    $aInfomation = array();

    $query = " SELECT CONCAT_WS( ' - ', CONCAT_WS( '', '[SKU: ', VM.product_sku,']'), VM.product_name, CONCAT_WS( '', '$', ROUND(VMP.product_price,2))) AS name, VM.product_id 
									FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP ON VM.product_id = VMP.product_id
									WHERE VM.product_publish = 'Y' 
									ORDER BY VM.product_sku";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    /* $aProduct 					= array();
      $aProductItem 				= new stdClass;
      $aProductItem->name			= "------------------------------------------------ Select Product ------------------------------------------------";
      $aProductItem->id			= 0;
      $aProduct[] 				= $aProductItem;
      $aProduct  					= array_merge( $aProduct, $rows ); */
//	$aInfomation['cboProduct']	= mosHTML::selectList( $aProduct, "select_product_id", "class='cbo-product' size='1'", "product_id", "name", "0" );
    foreach ($rows as $item) {
        $aInfomation['cboProduct'] .= '{ value_id: "' . $item->product_id . '", text: "' . $item->name . '" },';
    }
    $aInfomation['cboProduct'] = substr($aInfomation['cboProduct'], 0, strlen($aInfomation['cboProduct']) - 1);


    $query = " SELECT VM.product_id, VM.product_sku, VM.product_name, VMP.product_price, VTR.tax_rate 
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP ON VM.product_id = VMP.product_id
								 LEFT JOIN  #__vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if (count($rows)) {
        foreach ($rows as $value) {
            $sString .= $value->product_id . "[--1--]" . $value->product_sku . "[--1--]" . $value->product_name . "[--1--]" . round(doubleval($value->product_price), 2) . "[--1--]" . round(floatval($value->tax_rate), 2) . "[--2--]";
        }
    }
    $aInfomation['Product'] = $sString;


    $query = "SELECT country_3_code, country_name FROM #__vm_country ORDER BY country_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['bill_country'] = mosHTML::selectList($rows, "bill_country", "size='1' id='bill_country'", "country_3_code", "country_name", "CAN");
    $aInfomation['deliver_country'] = mosHTML::selectList($rows, "deliver_country", "size='1' id='deliver_country'", "country_3_code", "country_name", "CAN");


    $query = " SELECT S.state_2_code, S.state_name 
				FROM #__vm_state S INNER JOIN #__vm_country AS C 
				ON C.country_id	= S.country_id 
				WHERE C.country_3_code = 'CAN'
				ORDER BY S.state_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['bill_state'] = mosHTML::selectList($rows, "bill_state", "size='1'", "state_2_code", "state_name");
    $aInfomation['deliver_state'] = mosHTML::selectList($rows, "deliver_state", "size='1'", "state_2_code", "state_name");


    $query = "SELECT order_occasion_code, order_occasion_name FROM #__vm_order_occasion ORDER BY list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['occasion'] = mosHTML::selectList($rows, "occasion", "size='1'", "order_occasion_code", "order_occasion_name");


    $query = "SELECT shipping_rate_id, CONCAT( '$', shipping_rate_value, ' - ', shipping_rate_name) AS shipping_rate FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['shipping_method'] = mosHTML::selectList($rows, "shipping_method", "size='1'", "shipping_rate_id", "shipping_rate");


    $query = "SELECT shipping_rate_id, shipping_rate_value, tax_rate FROM #__vm_shipping_rate INNER JOIN #__vm_tax_rate ON shipping_rate_vat_id = tax_rate_id ORDER BY shipping_rate_list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    /* echo $query;
      print_r($rows); */
    foreach ($rows as $item) {
        $aInfomation['shipping_method_list_fee'] .= $item->shipping_rate_id . "[--1--]" . floatval($item->shipping_rate_value) . "[--1--]" . floatval($item->tax_rate) . "[--2--]";
    }


    $query = "SELECT creditcard_code,creditcard_name FROM #__vm_creditcard";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['payment_method'] = mosHTML::selectList($rows, "payment_method", "size='1'", "creditcard_code", "creditcard_name");


    $aInfomation['delivery_day'] = lisDay("deliver_day", date('j', time()), "");
    $aInfomation['delivery_month'] = listMonth("deliver_month", date('m', time()), "onchange='changeUnAvailableDate( this.value );'");
    $aInfomation['expire_month'] = listMonth("expire_month", null, " size='1' ");
    $aInfomation['expire_year'] = listYear("expire_year", date("Y"), " size='1' ", 7, date("Y"));


    $query = "SELECT name,options FROM tbl_options WHERE type='special_deliver' ";
    $database->setQuery($query);
    $aSpecialDeliver = $database->loadObjectList();

    foreach ($aSpecialDeliver as $item) {
        $aInfomation['special_deliver'] .= $item->name . "/" . $item->options . "[--1--]";
    }

    $query = "SELECT name,options FROM tbl_options WHERE type='postal_code' ";
    $database->setQuery($query);
    $aPostalCode = $database->loadObjectList();
    /* echo $query;
      print_r($aPostalCode); */
    foreach ($aPostalCode as $item) {
        $aInfomation['postal_code_deliver'] .= $item->name . "[--1--]" . $item->options . "[--2--]";
    }


    $query = "SELECT name FROM tbl_options WHERE type='deliver_option' ORDER BY name";
    $database->setQuery($query);
    $aUnAvailableDate = $database->loadObjectList();

    foreach ($aUnAvailableDate as $item) {
        $aInfomation['unavailable_date'] .= $item->name . "[--1--]";
    }


    $query = "SELECT options FROM tbl_options WHERE type='cut_off_time'";
    $database->setQuery($query);
    $sOptionParam = $database->loadResult();
    $aInfomation['option_param'] = explode("[--1--]", $sOptionParam);
    $aInfomation['time_limit'] = $aInfomation['option_param'][0] * 60 + $aInfomation['option_param'][1];

    if (intval($aInfomation['option_param'][0]) >= 12) {
        $aInfomation['time'] = ( intval($aInfomation['option_param'][0]) - 12 ) . ":" . $aInfomation['option_param'][1] . " PM";
    } else {
        $aInfomation['time'] = intval($aInfomation['option_param'][0]) . ":" . $aInfomation['option_param'][1] . " AM";
    }


    $aInfomation['day_now'] = intval(date('j', time()));
    $aInfomation['month_now'] = intval(date('m', time()));
    $aInfomation['year_now'] = intval(date('Y', time()));
    $aInfomation['hour_now'] = intval(date('H', time()));
    $aInfomation['minute_now'] = intval(date('i', time()));
    $aInfomation['time_now'] = $aInfomation['hour_now'] * 60 + $aInfomation['minute_now'];


    //echo $nTimeNow."===".$nHourNow."===".$nMinuteNow."===".$nTimeLimit;
    if ($aInfomation['time_now'] >= $aInfomation['time_limit']) {
        $aInfomation['cut_off_time'] = 1;
    } else {
        $aInfomation['cut_off_time'] = 0;
    }
    $aInfomation['days_of_month_now'] = getMonthDays($aInfomation['month_now'], $aInfomation['year_now']);


    $aInfomation['DELIVERY_DATE'] = "<span>Attention! Same day orders cut off time %s local time.</span> Bloomex Time is: %s";


    HTML_PhoneOrder::makePhoneOrder($option, &$aInfomation);
}

function getMonthDays($Month, $Year) {
    if (is_callable("cal_days_in_month")) {
        return cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
    } else {
        return date("d", mktime(0, 0, 0, $Month + 1, 0, $Year));
    }
}

function lisDay($list_name, $selected_item = "", $extra = "") {
    $sString = "";
    $list = array("DAY", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");

    $sString = "<select name='{$list_name}' {$extra}>";
    foreach ($list as $value) {
        $sString .= "<option value='{$value}'>{$value}</option>";
    }

    $sString .= "</select>";
    return $sString;
}

function listMonth($list_name, $selected_item = "", $extra = "") {
    $sString = "";
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

    $sString = "<select name='{$list_name}' {$extra}>";
    foreach ($list as $key => $value) {
        $sString .= "<option value='{$key}'>{$value}</option>";
    }

    $sString .= "</select>";
    return $sString;
}

function listYear($list_name, $selected_item = "", $extra = "", $max = 7, $from = 2009, $direct = "up") {
    $sString = "";

    $sString = "<select name='{$list_name}' {$extra}>";
    for ($i = 0; $i < $max; $i++) {
        $value = $from + $i;
        $text = $from + $i;
        if ($selected_item == $value) {
            $sString .= "<option selected value='" . $value . "'>" . $text . "</option>";
        } else {
            $sString .= "<option value='" . $value . "'>" . $text . "</option>";
        }
    }

    $sString .= "</select>";
    return $sString;
}

function checkAccountInfo($option) {
    global $database, $mainframe, $mosConfig_list_limit;
    $sEmail = mosGetParam($_REQUEST, "email", "");

    $query = "SELECT id, username FROM #__users WHERE email = '{$sEmail}'";
    $database->setQuery($query);
    $oRow = $database->loadObjectList();
    $oUser = $oRow[0];
    $user_id = intval($oUser->id);

    if ($user_id) {
        $query = "	SELECT U.username, VUI.* 
					FROM #__users AS U 
					LEFT JOIN #__vm_user_info AS VUI
					ON U.id = VUI.user_id 
					WHERE U.email='{$sEmail}' 
					ORDER BY VUI.address_type";
        $database->setQuery($query);
        $oRow = $database->loadObjectList();
        //print_r($oRow);
        //echo $query;

        $bExistBilling = false;
        $bExistShipping = false;
        foreach ($oRow as $value) {
            if ($value->address_type == 'BT') {
                $sAccountBillingInfo = $value->username . "[--1--]"
                        . $value->user_info_id . "[--1--]"
                        . $value->user_id . "[--1--]"
                        . $value->address_type . "[--1--]"
                        . $value->address_type_name . "[--1--]"
                        . $value->company . "[--1--]"
                        . $value->title . "[--1--]"
                        . $value->last_name . "[--1--]"
                        . $value->first_name . "[--1--]"
                        . $value->middle_name . "[--1--]"
                        . $value->phone_1 . "[--1--]"
                        . $value->phone_2 . "[--1--]"
                        . $value->fax . "[--1--]"
                        . $value->address_1 . "[--1--]"
                        . $value->address_2 . "[--1--]"
                        . $value->city . "[--1--]"
                        . $value->state . "[--1--]"
                        . $value->country . "[--1--]"
                        . $value->zip . "[--1--]"
                        . $value->user_email;

                $bExistBilling = true;
            } else {
                $sAccountShippingInfo .= $value->user_info_id . "[--1--]"
                        . $value->user_id . "[--1--]"
                        . $value->zip . "[--1--]"
                        . "<b>" . $value->address_type_name . "</b> "
                        . ". Address: " . $value->address_1 . ", " . $value->city . ", " . $value->state . " " . $value->zip . ", " . $value->country
                        . ". Phone: " . $value->phone_1 . "(" . $value->phone_2 . ")"
                        . ". Fax: " . $value->fax . "[--2--]";

                if ($value->user_info_id) {
                    $bExistShipping = true;
                }
            }
        }

        if ($bExistBilling && $bExistShipping) {
            $sMsg = "<font color='blue'>System found some info with this email!</font>";
        } else {
            $sMsg = "<font color='blue'>System found only joomla account info with this email!</font>";

            if (!$bExistShipping && !$bExistBilling) {
                $sMsg .= "[--3--]System didn't find any Virtuemart Account Info with this email!";
                $sAccountBillingInfo = $oUser->id . "[--1--]" . $oUser->username . "[--1--]0";
            } else {
                if (!$bExistBilling) {
                    $sMsg .= "[--3--]System didn't find any Virtuemart Billing Info with this email!";
                    $sAccountBillingInfo = $oUser->id . "[--1--]" . $oUser->username . "[--1--]0";
                }

                if (!$bExistShipping) {
                    $sMsg .= "[--3--]System didn't find any Virtuemart Deliver Info with this email!";
                }
            }
        }

        $sAccountInfo = $sAccountBillingInfo . "[--3--]" . $sAccountShippingInfo . "[--3--]" . $sMsg;
        echo $sAccountInfo;
    } else {
        echo "error";
    }
    exit(0);
}

function checkCounponCode($option) {
    global $database;

    $coupon_discount_code = trim(mosGetParam($_REQUEST, 'coupon_discount_code'));

    $query = " SELECT percent_or_total, coupon_value FROM #__vm_coupons WHERE coupon_code = '{$coupon_discount_code}'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $coupon = $rows[0];

    if ($coupon->percent_or_total) {
        if ($coupon->percent_or_total == "percent") {
            $nCouponValue = round(floatval($coupon->coupon_value), 2);
            echo "success[--1--]<font color='blue'>This coupon code will reduce {$nCouponValue}% of order total price. Please click to \"Calculate Order Price\" to get right price.</font>[--1--]{$nCouponValue}[--1--]{$coupon->percent_or_total}";
        } elseif ($coupon->percent_or_total == "total") {
            $nCouponValue = round(floatval($coupon->coupon_value), 2);
            echo "success[--1--]<font color='blue'>This coupon code will reduce \${$nCouponValue} of order total price. Please click to \"Calculate Order Price\" to get right price.</font>[--1--]{$nCouponValue}[--1--]{$coupon->percent_or_total}";
        }
    } else {
        echo "error[--1--]<font color='red'>This coupon code is not exist. Please try again!</font>";
    }
    exit(0);
}

function savePhoneOrder($option) {
    global $database, $my, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $PaymentVar = array();


    $bCheckCCPayment = intval(mosGetParam($_REQUEST, "check_CC_payment", 0));
    $user_id = intval(mosGetParam($_REQUEST, "user_id", 0));
    $user_name = mosGetParam($_REQUEST, "user_name", "");
    $account_email = mosGetParam($_REQUEST, "account_email", "");

    $bill_company_name = mosGetParam($_REQUEST, "bill_company_name", "");
    $bill_first_name = mosGetParam($_REQUEST, "bill_first_name", "");
    $bill_last_name = mosGetParam($_REQUEST, "bill_last_name", "");
    $bill_middle_name = mosGetParam($_REQUEST, "bill_middle_name", "");
    $bill_address_1 = mosGetParam($_REQUEST, "bill_address_1", "");
    $bill_address_2 = mosGetParam($_REQUEST, "bill_address_2", "");
    $bill_city = mosGetParam($_REQUEST, "bill_city", "");
    $bill_zip_code = mosGetParam($_REQUEST, "bill_zip_code", "");
    $bill_country = mosGetParam($_REQUEST, "bill_country", "");
    $bill_state = mosGetParam($_REQUEST, "bill_state", "");
    $bill_phone = mosGetParam($_REQUEST, "bill_phone", "");
    $bill_evening_phone = mosGetParam($_REQUEST, "bill_evening_phone", "");
    $bill_fax = mosGetParam($_REQUEST, "bill_fax", "");


    $address_user_name = mosGetParam($_REQUEST, "address_user_name", "");
    $deliver_first_name = mosGetParam($_REQUEST, "deliver_first_name", "");
    $deliver_last_name = mosGetParam($_REQUEST, "deliver_last_name", "");
    $deliver_middle_name = mosGetParam($_REQUEST, "deliver_middle_name", "");
    $deliver_address_1 = mosGetParam($_REQUEST, "deliver_address_1", "");
    $deliver_address_2 = mosGetParam($_REQUEST, "deliver_address_2", "");
    $deliver_city = mosGetParam($_REQUEST, "deliver_city", "");
    $deliver_zip_code = mosGetParam($_REQUEST, "deliver_zip_code", "");
    $deliver_country = mosGetParam($_REQUEST, "deliver_country", "");
    $deliver_state = mosGetParam($_REQUEST, "deliver_state", "");
    $deliver_phone = mosGetParam($_REQUEST, "deliver_phone", "");
    $deliver_evening_phone = mosGetParam($_REQUEST, "deliver_evening_phone", "");
    $deliver_cell_phone = mosGetParam($_REQUEST, "deliver_cell_phone", "");
    $deliver_fax = mosGetParam($_REQUEST, "deliver_fax", "");
    $deliver_recipient_email = mosGetParam($_REQUEST, "deliver_recipient_email", "");
    $occasion = mosGetParam($_REQUEST, "occasion", "");
    $shipping_method = mosGetParam($_REQUEST, "shipping_method", "");
    $card_msg = mosGetParam($_REQUEST, "card_msg", "");
    $signature = mosGetParam($_REQUEST, "signature", "");
    $card_comment = mosGetParam($_REQUEST, "card_comment", "");
    $deliver_day = intval(mosGetParam($_REQUEST, "deliver_day", ""));
    $deliver_month = intval(mosGetParam($_REQUEST, "deliver_month", ""));


    $payment_method_state = mosGetParam($_REQUEST, "payment_method_state", "");
    $payment_method = mosGetParam($_REQUEST, "payment_method", "");
    $name_on_card = mosGetParam($_REQUEST, "name_on_card", "");
    $credit_card_number = mosGetParam($_REQUEST, "credit_card_number", "");
    $credit_card_security_code = mosGetParam($_REQUEST, "credit_card_security_code", "");
    $expire_month = intval(mosGetParam($_REQUEST, "expire_month", ""));
    $expire_year = intval(mosGetParam($_REQUEST, "expire_year", ""));


    $exist_address_deliver = intval(mosGetParam($_REQUEST, "exist_address_deliver", 0));
    $deliver_address_item = mosGetParam($_REQUEST, "deliver_address_item", "");


    $sProductId = mosGetParam($_REQUEST, "product_id", "");
    if ($sProductId) {
        $aProductId = explode(",", $sProductId);
    }

    $sQuantity = mosGetParam($_REQUEST, "quantity", "");
    if ($sQuantity) {
        $aQuantity = explode(",", $sQuantity);
    }
    for ($h = 0; $h < count($aProductId); $h++) {
        $aQuantity[$h] = $aProductId[$h] . "[--1--]" . $aQuantity[$h];
    }


    $coupon_discount_price = doubleval(mosGetParam($_REQUEST, "coupon_discount_price", ""));
    $total_price = doubleval(mosGetParam($_REQUEST, "total_price", ""));
    $deliver_fee = doubleval(mosGetParam($_REQUEST, "deliver_fee", ""));
    $sub_total_price = doubleval(mosGetParam($_REQUEST, "sub_total_price", ""));
    $total_tax = doubleval(mosGetParam($_REQUEST, "total_tax", ""));
    $total_deliver_tax_fee = doubleval(mosGetParam($_REQUEST, "total_deliver_tax_fee", ""));


    /* Insert the main order information */
    $order_number = md5("order" . $user_id . time());
    $VM_LANG = new vmLanguage();
    //================================== PAYMENT =========================================
    if ($payment_method_state == "offline") {
        $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
        $order_status = "P";
        $payment_msg = " and " . $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
        ;
    } else {
        $PaymentVar["user_id"] = $user_id;
        $PaymentVar["bill_company_name"] = $bill_company_name;
        $PaymentVar["bill_last_name"] = $bill_last_name;
        $PaymentVar["bill_first_name"] = $bill_first_name;
        $PaymentVar["bill_middle_name"] = $bill_middle_name;
        $PaymentVar["bill_phone"] = $bill_phone;
        $PaymentVar["bill_fax"] = $bill_fax;
        $PaymentVar["bill_address_1"] = $bill_address_1;
        $PaymentVar["bill_address_2"] = $bill_address_2;
        $PaymentVar["bill_city"] = $bill_city;
        $PaymentVar["bill_state"] = $bill_state;
        $PaymentVar["bill_country"] = $bill_country;
        $PaymentVar["bill_zip_code"] = $bill_zip_code;
        $PaymentVar["bill_email"] = $bill_email;
        $PaymentVar["expire_month"] = $expire_month;
        $PaymentVar["expire_year"] = $expire_year;
        $PaymentVar["order_payment_number"] = $credit_card_number;
        $PaymentVar["credit_card_code"] = $credit_card_security_code;

        $aResult = array();
        $bPayment = process_payment($order_number, $total_price, $PaymentVar, $aResult);

        if ($aResult["approved"] == 1) {
            $order_status = "A";
            $payment_msg = " and Payment Approved";
        } else {
            echo "error[--1--]<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $aResult["order_payment_log"] . "</b>";
            exit(0);
        }
    }


    //====================================================================================================================================
    $user_info_id = md5($user_id . time());

    $query = "SELECT user_id FROM #__vm_user_info WHERE user_email = '{$account_email}' AND address_type = 'BT'";
    $database->setQuery($query);

    if (intval($database->loadResult())) {
        /* $user_id			= $oUser->user_id;
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
          $bill_email 		= $oUser->user_email; */

        $query = " UPDATE #__vm_user_info 
							SET address_type_name	= '-default-', 
								company				= '{$bill_company_name}', 
								last_name			= '{$bill_last_name}', 
								first_name			= '{$bill_first_name}', 
								middle_name			= '{$bill_middle_name}', 
								phone_1				= '{$bill_phone}',
								fax					= '{$bill_fax}', 
								address_1			= '{$bill_address_1}', 
								address_2			= '{$bill_address_2}', 
								city				= '{$bill_city}', 
								state				= '{$bill_state}', 
								country				= '{$bill_country}', 
								zip					= '{$bill_zip_code}'													
					   	   WHERE user_email = '{$account_email}' AND address_type = 'BT'";
        //die($query);
        $database->setQuery($query);
        $database->query();

        $query = "SELECT username FROM #__users WHERE email = '{$account_email}'";
        $database->setQuery($query);
        $user_name = $database->loadResult();
    } else {
//			print_r($_POST);
//			die("aaaaaaaaaaaaaaaaaaaaaaa");
        /* Insert new user account */
        $query = "SELECT id FROM #__users WHERE email = '{$account_email}'";
        $database->setQuery($query);
        $user_id = intval($database->loadResult());
        if (!$user_id) {
            $query = "INSERT INTO #__users( name, username, email, usertype, block, gid ) VALUES( '{$user_name}', '{$user_name}', '{$account_email}' , 'Registered' , 0, 18 )";
            $database->setQuery($query);
            $database->query();
            $user_id = $database->insertid();

            $query = "INSERT INTO #__core_acl_aro( section_value, value, order_value, name, hidden ) VALUES( 'users', {$user_id}, 0, '{$user_name}', 0 )";
            $database->setQuery($query);
            $database->query();
            $aro_id = $database->insertid();

            $query = "INSERT INTO #__core_acl_groups_aro_map( group_id, section_value, aro_id ) VALUES( 18, '', {$aro_id} )";
            $database->setQuery($query);
            $database->query();
        }

        /* Insert the New User Billto & Shipto Info to User Information Manager Table */
        $query = "INSERT INTO #__vm_user_info( user_info_id, 
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
															country, zip,
															user_email ) 
						   	   VALUES(  '" . $user_info_id . "', 
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
						   	   			'{$account_email}' )";
        $database->setQuery($query);
        $database->query();
        //echo "<br/>2. <br/>".$database->getErrorMsg()."<br/>";
    }


    if ($exist_address_deliver && $address_user_name) {
        $query = "INSERT INTO #__vm_user_info( user_info_id, 
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
					   	   VALUES(  '" . md5($user_info_id) . "', 
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
    } else {
        $aDeliverAddressItem = explode("[--1--]", $deliver_address_item);

        $query = "SELECT * FROM #__vm_user_info AS VUI, #__users AS U WHERE VUI.user_id = U.id AND VUI.user_info_id ='{$aDeliverAddressItem[0]}'  AND VUI.user_id = '{$aDeliverAddressItem[1]}'";
        $database->setQuery($query);
        $oUserInfo = $database->loadObjectList();
        $oUser = $oUserInfo[0];

        if ($oUser) {
            $address_user_name = $oUser->address_type_name;
            $deliver_last_name = $oUser->last_name;
            $deliver_first_name = $oUser->first_name;
            $deliver_middle_name = $oUser->middle_name;
            $deliver_phone = $oUser->phone_1;
            $deliver_cell_phone = $oUser->phone_2;
            $deliver_fax = $oUser->fax;
            $deliver_address_1 = $oUser->address_1;
            $deliver_address_2 = $oUser->address_2;
            $deliver_city = $oUser->city;
            $deliver_state = $oUser->state;
            $deliver_country = $oUser->country;
            $deliver_zip_code = $oUser->zip;
            $deliver_recipient_email = $oUser->user_email;
        }
    }
//	die($query);
//	echo "<br/>3. <br/>".$database->getErrorMsg()."<br/>";
//	echo "=======================".$deliver_address_item."=======================";

    $query = "SELECT * FROM #__vm_vendor WHERE vendor_country = '{$bill_country}'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aVendor = $rows[0];
    $vendor_id = $aVendor->vendor_id;
    if (!$vendor_id)
        $vendor_id = 1;
    $PaymentVar["vendor_id"] = $vendor_id;
    $vendor_currency = $aVendor->vendor_currency;
    $PaymentVar["vendor_currency"] = $vendor_currency;

    $query = " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id 
							FROM #__vm_shipping_rate AS VSR
							INNER JOIN #__vm_shipping_carrier AS VSC
							ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id  
							WHERE VSR.shipping_rate_id = {$shipping_method}";
    $database->setQuery($query);
    $aShippingMethod = $database->loadRow();
    $sShippingMethod = "standard_shipping|" . implode("|", $aShippingMethod);


    $query = " SELECT VMP.product_price, VTR.tax_rate 
							FROM #__vm_product AS VM 
							LEFT JOIN #__vm_product_price AS VMP 
							ON VM.product_id = VMP.product_id 
							LEFT JOIN  #__vm_tax_rate AS VTR 
							ON VM.product_tax_id = VTR.tax_rate_id 
							WHERE VM.product_id IN ({$sProductId})";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $order_tax_details = array();
    if (count($rows)) {
        foreach ($rows as $value) {
            if (!isset($order_tax_details[$value->tax_rate])) {
                $order_tax_details[$value->tax_rate] = doubleval($value->tax_rate) * doubleval($value->product_price);
            } else {
                $order_tax_details[$value->tax_rate] = $order_tax_details[$value->tax_rate] + (doubleval($value->tax_rate) * doubleval($value->product_price));
            }
        }
    }

    //====================================================================================
    $query = "INSERT INTO #__vm_orders( user_id, 
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
												 username ) 
				   	   VALUES( 	{$user_id}, 
				   	   			{$vendor_id}, 
				   	   			'{$order_number}', 
				   	   			'{$user_info_id}', 
				   	   			{$total_price}, 
				   	   			{$sub_total_price}, 
				   	   			{$total_tax}, 
				   	   			'" . serialize($order_tax_details) . "', 
				   	   			{$deliver_fee}, 
				   	   		   	{$total_deliver_tax_fee}, 
				   	   		   	{$coupon_discount_price}, 
				   	   		   	'{$vendor_currency}', 
				   	   		   	'$order_status', 
				   	   		   	" . $timestamp . ", 
				   	   		   	" . $timestamp . ", 
				   	   		   	'" . ($deliver_day . "-" . $deliver_month . "-" . date("Y", time())) . "', 
				   	   		   	'" . $sShippingMethod . "', 
						   	   	'" . htmlspecialchars(strip_tags($card_msg)) . "', 
						   	   	'" . htmlspecialchars(strip_tags($signature)) . "', 
						   	   	'" . htmlspecialchars(strip_tags($occasion)) . "', 
						   	   	'" . htmlspecialchars(strip_tags($card_comment)) . "', 
						   	   	'" . htmlspecialchars(strip_tags($user_name)) . "' )";
    $database->setQuery($query);
    $database->query();
    $order_id = $database->insertid();
    /* echo $query;
      echo "<br/>4. <br/>".$database->getErrorMsg()."<br/>";
      die(); */

    /* Insert the initial Order History. */
    
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
//	echo "<br/>5. <br/>".$database->getErrorMsg()."<br/>";	


    /* Insert the Order payment info */
    $payment_number = preg_replace("/ |-/", "", $credit_card_number);


    // Payment number is encrypted using mySQL ENCODE function.
    $query = "INSERT INTO #__vm_order_payment(	order_id, 
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
						'" . strtotime("{$expire_month}/01/{$expire_year}") . "',
						'{$aResult["order_payment_log"]}',
						'{$name_on_card}',			
						'{$aResult["order_payment_trans_id"]}')";
    //$query .= "'" . $vmInputFilter->safeSQL( @$d["order_payment_trans_id"] ). "'";
    $database->setQuery($query);
    $database->query();
    /* echo "<br/>6. <br/>".$database->getErrorMsg()."<br/>"; */


    /* Insert the User Billto & Shipto Info to Order Information Manager Table */
    $query = "INSERT INTO #__vm_order_user_info (  order_id, 
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
				   	   VALUES(  '" . $order_id . "', 
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
				   	   			'{$account_email}' )";
    $database->setQuery($query);
    $database->query();
//	echo "<br/>7. <br/>".$database->getErrorMsg()."<br/>";	


    $query = "INSERT INTO #__vm_order_user_info (  order_id, 
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
				   	   VALUES(  '" . $order_id . "', 
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
//	echo "<br/>8. <br/>".$database->getErrorMsg()."<br/>";

    /* Insert all Products from the Cart into order line items */
    $query = " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate 
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
							ON VM.product_id = VMP.product_id 
							LEFT JOIN  #__vm_tax_rate AS VTR 
							ON VM.product_tax_id = VTR.tax_rate_id 
							WHERE VM.product_id IN ({$sProductId})";
    $database->setQuery($query);
    $rows = $database->loadObjectList();


    $order_items = '<table width="100%">';
    $order_items .= '<tr>
							<td width="5%">No</td>
							<td width="5%">SKU Code</td>
							<td width="50%">Product Name</td>
							<td width="10%">Product Price (Net)</td>
							<td width="5%">Tax</td>
							<td width="10%">Product Price (Gross)</td>
							<td width="5%">Quantity</td>
							<td width="10%">SubTotal</td>
						</tr>';
    if (count($rows)) {
        $j = 0;
        foreach ($rows as $value) {
            $nQuantityTemp = 0;
            for ($h = 0; $h < count($aQuantity); $h++) {
                $aQuantityTemp = explode("[--1--]", $aQuantity[$h]);

                if (intval($value->product_id) == intval($aQuantityTemp[0])) {
                    $nQuantityTemp = $aQuantityTemp[1];
                    break;
                }
            }

            $query = "INSERT INTO #__vm_order_item (   order_id, 
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
				   	   			" . $value->product_id . ", 
				   	   			'" . addslashes($value->product_sku) . "', 
				   	   			'" . addslashes($value->product_name) . "', 	
				   	   			" . intval($nQuantityTemp) . ", 
				   	   			" . $value->product_price . ", 	
				   	   			" . ( ($value->product_price * $value->tax_rate ) + $value->product_price ) . ", 	
				   	   			'" . $value->product_currency . "', 
				   	   			'P', 
				   	   			'" . addslashes($value->product_desc) . "', 
				   	   			'{$timestamp}', 
				   	   			'{$timestamp}'
				   	   			 )";
            $database->setQuery($query);
            $database->query();
            
            

//			echo "<br/>9-1. <br/>".$database->getErrorMsg()."<br/>";


            $order_items .= '<tr>
								<td>' . ($j + 1) . '. </td>
								<td>' . addslashes($value->product_sku) . '</td>
								<td>' . stripslashes($value->product_name) . '</td>
								<td>' . $value->product_price . '</td>
								<td>' . $value->tax_rate . '</td>
								<td>' . round((($value->product_price * $value->tax_rate ) + $value->product_price), 4) . '</td>
								<td>' . intval($nQuantityTemp) . '</td>
								<td>' . round((($value->product_price * $value->tax_rate ) + $value->product_price) * intval($nQuantityTemp), 4) . '</td>
							</tr>';

            /* Insert ORDER_PRODUCT_TYPE */
            $query = "SELECT * FROM #__vm_product_product_type_xref, #__vm_product_type 
					  WHERE #__vm_product_product_type_xref.product_id = '" . $value->product_id . "' 
					  AND #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id";
            $database->setQuery($query);
            $rows2 = $database->loadObjectList();

            foreach ($rows2 as $item) {
                $product_type_id = $item->product_type_id;

                $query = "  SELECT * 
							FROM #__vm_product_type_$product_type_id 
							WHERE product_id='" . $value->product_id . "' ";
                $database->setQuery($query);
                $rows3 = $database->loadObjectList();
                $item2 = $rows3[0];

                $query = "INSERT INTO #__vm_order_product_type( order_id, 
															product_id, 
															product_type_name, 
															quantity, price) 															
							VALUES ( {$order_id}, 
									 " . $value->product_id . "', 
									 '" . addslashes($item2->product_type_name) . "', 
									 " . $item2->quantity . ", 
									 " . $item2->price . ")";
                $database->setQuery($query);
                $database->query();
//				echo "<br/>9-2. <br/>".$database->getErrorMsg()."<br/>";
            }


            /* Update Stock Level and Product Sales */
            if ($value->product_in_stock) {
                $query = "	UPDATE #__vm_product 
							SET product_in_stock = product_in_stock - " . intval($aQuantity[$j]) . " 
							WHERE product_id = '" . $value->product_id . "'";
                $database->setQuery($query);
                $database->query($query);
            }

            $query = "	UPDATE #__vm_product 
						SET product_sales= product_sales + " . intval($aQuantity[$j]) . "  
						WHERE product_id='" . $value->product_id . "'";
            $database->query($query);
//			echo "<br/>9-3. <br/>".$database->getErrorMsg()."<br/>";


            $j++;
        }
    }
    $order_items .= '</table>';

    $query = "SELECT creditcard_name FROM #__vm_creditcard WHERE creditcard_code = '$payment_method'";
    $database->setQuery($query);
    $payment_info_details = $database->loadResult();
    $payment_info_details .= '<br />Name On Card: ' . $name_on_card . '<br />'
            . 'Credit Card Number: ' . $credit_card_number . '<br />'
            . 'Expiration Date: ' . $expire_month . ' / ' . $expire_year . '<br />';

    $shopper_header = 'Thank you for shopping with us.  Your order information follows.';
    $shopper_order_link = $mosConfig_live_site . "/index.php?page=account.order_details&order_id=$order_id";
    $shopper_footer_html = "<br /><br />Thank you for your patronage.<br />"
            . "<br /><a title=\"View the order by following the link below.\" href=\"$shopper_order_link\">View the order by following the link below.</a>"
            . "<br /><br />Questions? Problems?<br />"
            . "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom . "\">" . $mosConfig_mailfrom . "</a><br/><b> Please Note: Orders placed for deliveries outside of Sydney, Melbourne, Brisbane and Perth may be delayed. We will contact you via email if there is an issue.</b>";

    $vendor_header = "The following order was received.";
    $vendor_order_link = $mosConfig_live_site . "/index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin";
    $vendor_footer_html = "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";

    $vendor_image = "<img src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/vendor/" . $aVendor->vendor_full_image . "\" alt=\"vendor_image\" border=\"0\" />";


    /* ===================================== Assign Email Content ===================================== */
    $myFile = $mosConfig_absolute_path . "/administrator/components/com_virtuemart/html/templates/order_emails/email_english.html";
    $fh = fopen($myFile, 'r');
    $html = fread($fh, filesize($myFile));
    fclose($fh);

    $html = str_replace('{phpShopVendorName}', $aVendor->vendor_name, $html);
    $html = str_replace('{phpShopVendorStreet1}', $aVendor->vendor_address_1, $html);
    $html = str_replace('{phpShopVendorStreet2}', $aVendor->vendor_address_2, $html);
    $html = str_replace('{phpShopVendorZip}', $aVendor->vendor_zip, $html);
    $html = str_replace('{phpShopVendorCity}', $aVendor->vendor_city, $html);
    $html = str_replace('{phpShopVendorState}', $aVendor->vendor_state, $html);
    $html = str_replace('{phpShopVendorImage}', $vendor_image, $html);
    $html = str_replace('{phpShopOrderHeader}', "Purchase Order", $html);
    $html = str_replace('{phpShopOrderNumber}', $order_number, $html);
    $html = str_replace('{phpShopOrderDate}', date("F j, Y", $timestamp), $html);
    $html = str_replace('{phpShopOrderStatus}', "Pendding", $html);

    $html = str_replace('{phpShopBTCompany}', $bill_company_name, $html);
    $html = str_replace('{phpShopBTName}', $bill_first_name . " " . $bill_middle_name . " " . $bill_last_name, $html);
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
    $html = str_replace('{phpShopSTName}', $deliver_first_name . " " . $deliver_middle_name . " " . $deliver_last_name, $html);
    $html = str_replace('{phpShopSTStreet1}', $deliver_address_1, $html);
    $html = str_replace('{phpShopSTStreet2}', $deliver_address_2, $html);
    $html = str_replace('{phpShopSTCity}', $deliver_city, $html);
    $html = str_replace('{phpShopSTState}', $deliver_state, $html);
    $html = str_replace('{phpShopSTZip}', $deliver_zip_code, $html);
    $html = str_replace('{phpShopSTCountry}', $deliver_country, $html);
    $html = str_replace('{phpShopSTPhone}', $deliver_phone, $html);
    $html = str_replace('{phpShopSTFax}', $deliver_fax, $html);

    $html = str_replace('{phpShopOrderItems}', $order_items, $html);

    $html = str_replace('{phpShopOrderSubtotal}', "$" . number_format($sub_total_price, 2, '.', ' '), $html);
    $html = str_replace('{phpShopOrderShipping}', "$" . number_format($deliver_fee, 2, '.', ' '), $html);
    $html = str_replace('{phpShopOrderTax}', "$" . number_format($total_tax, 2, '.', ' '), $html);

    $html = str_replace('{phpShopOrderTotal}', "$" . number_format($total_price, 2, '.', ' '), $html);

    $html = str_replace('{phpShopOrderDisc1}', $order_disc1, $html);
    $html = str_replace('{phpShopOrderDisc2}', $order_disc2, $html);
    $html = str_replace('{phpShopOrderDisc3}', $order_disc3, $html);
    $html = str_replace('{phpShopCustomerNote}', htmlspecialchars(strip_tags($card_msg)), $html);

    $html = str_replace('{PAYMENT_INFO_LBL}', "Payment Information", $html);

    $html = str_replace('{PAYMENT_INFO_DETAILS}', $payment_info_details, $html);

    $html = str_replace('{SHIPPING_INFO_LBL}', "Delivery Information", $html);

    $html = str_replace('{SHIPPING_INFO_DETAILS}', $aShippingMethod[0] . " (" . $aShippingMethod[1] . ")", $html);
    /* if( $this->_SHIPPING ) {

      }
      else {
      $html = str_replace('{SHIPPING_INFO_DETAILS}', " ./. ", $html);
      } */

    $shopper_html = str_replace('{phpShopOrderHeaderMsg}', $shopper_header, $html);
    $shopper_html = str_replace('{phpShopOrderClosingMsg}', $shopper_footer_html, $shopper_html);

    /* $vendor_html = str_replace('{phpShopOrderHeaderMsg}',$vendor_header, $html);
      $vendor_html = str_replace('{phpShopOrderClosingMsg}',$vendor_footer_html,$vendor_html); */

    $shopper_subject = $aVendor->vendor_name . " Purchase Order - " . $order_id;

    mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);



    /* ===================================== Assign Order To The WareHouse ===================================== */
    
    $while = 4;
    $need_zip_code = $deliver_zip_code;
    
    while ($while > 0)
    {
        $query = "SELECT WH.warehouse_email, WH.warehouse_code FROM #__vm_warehouse AS WH, #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code LIKE '" . $need_zip_code . "%'";
        $database->setQuery($query);
        $oWarehouse = $database->loadObjectList();
        
        if (count($oWarehouse))
        {
            $oWarehouse = $oWarehouse[0];
            $warehouse_code = $oWarehouse->warehouse_code;
            $warehouse_email = $oWarehouse->warehouse_email;
            
            $while = 0;
        }
        else
        {
            $while--;
            
            $need_zip_code = substr($need_zip_code, 0, $while);
            
            if ($while == 0) $while = -1;
        }   
    }
    
    if ($while == 0)
    {
        $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        $database->setQuery($query);
        $database->query();

        if ($warehouse_code) 
        {
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
            $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
            
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
        }
    }
    else
    {
        $query = "UPDATE #__vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        $database->setQuery($query);
        $database->query();
    }
    /*
    $query = "SELECT WH.warehouse_email, WH.warehouse_code FROM #__vm_warehouse AS WH, #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code = '" . substr($deliver_zip_code, 0, 3) . "'";
    $database->setQuery($query);
    $oWarehouse = $database->loadObjectList();
    $oWarehouse = $oWarehouse[0];
    $warehouse_code = $oWarehouse->warehouse_code;
    $warehouse_email = $oWarehouse->warehouse_email;


    $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
    $database->setQuery($query);
    $database->query();

    if ($warehouse_code) {
        $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
        $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
//		echo $mail_Content."<br/><br/>".$mail_Subject."<br/><br/>".$mosConfig_mailfrom."<br/><br/>".$mosConfig_fromname."<br/><br/>".$warehouse_email."<br/><br/>";
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
    }
    */
    //echo $mosConfig_mailfrom."<br>".$mosConfig_fromname."<br>".$account_email."<br>".$shopper_subject."<br>".$shopper_html."<br><br>================<br><br>".$html;
    echo "save_order_success[--1--]" . str_replace(" ", "+", "Save Phone Order Successfully {$payment_msg}");
    exit(0);
}

function data_filter($aKw, $name) {
    foreach ($aKw as $value) {
        $pos = strpos($name, $value);
        if ($pos == true)
            return true;
    }

    return false;
}

function startTag($parser, $name, $attrs) {
    global $stack;
    $tag = array("name" => $name, "attrs" => $attrs);
    array_push($stack, $tag);
}

function cdata($parser, $cdata) {
    global $stack;
    if (trim($cdata)) {
        $stack[count($stack) - 1]['cdata'] = $cdata;
    }
}

function endTag($parser, $name) {
    global $stack;
    $stack[count($stack) - 2]['children'][] = $stack[count($stack) - 1];
    array_pop($stack);
}

function xml_to_array($file = '') {
    global $stack;
    $stack = array();
    $xml_parser = xml_parser_create("utf-8");
    xml_set_element_handler($xml_parser, "startTag", "endTag");
    xml_set_character_data_handler($xml_parser, "cdata");

    $data = xml_parse($xml_parser, (@file_get_contents($file)));

    if (!$data) {
        die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
    }
    xml_parser_free($xml_parser);
    return $stack;
}

function makeXMLOrder($option) {
    global $database, $my, $mosConfig_absolute_path, $act, $mosConfig_offset;

    $aArrayInformation = array();

    $query = "SELECT name, options FROM tbl_options WHERE type='deliver_option' ORDER BY name DESC ";
    $database->setQuery($query);
    $aInfomation["unavailable"] = $database->loadObjectList();


    $query = "SELECT shipping_rate_name FROM #__vm_shipping_rate ORDER BY shipping_rate_name ASC";
    $database->setQuery($query);
    $aInfomation["shipping"] = $database->loadObjectList();

    HTML_PhoneOrder::makeXMLOrder($option, $aInfomation);
}

function saveXMLOrder($option) {
    global $database, $my, $mosConfig_absolute_path, $act, $mosConfig_offset;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $PaymentVar = array();

    if ($_FILES["xml_file"]["name"]) {
        $sXmlFile = do_upload($_FILES["xml_file"], $mosConfig_absolute_path . "/media/");
    } else {
        $msg = "This upload process is wrong! Please try again!";
    }

    if ($sXmlFile) {
        $aData = xml_to_array($mosConfig_absolute_path . "/media/" . $sXmlFile);

        $user_id = 0;
        $account_email = trim($aData[0]["children"][0]["cdata"]);
        $user_name = trim($aData[0]["children"][1]["cdata"]);

        /* $bill_company_name			= $aData[0]["children"][1]["children"][0]["cdata"];
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
          $bill_fax					= $aData[0]["children"][1]["children"][12]["cdata"]; */

        //print_r($aData[0]["children"][2]["children"]);
        $address_user_name = trim($aData[0]["children"][2]["children"][0]["cdata"]);
        $deliver_first_name = trim($aData[0]["children"][2]["children"][1]["cdata"]);
        $deliver_last_name = trim($aData[0]["children"][2]["children"][2]["cdata"]);
        $deliver_middle_name = trim($aData[0]["children"][2]["children"][3]["cdata"]);
        $deliver_address_1 = trim($aData[0]["children"][2]["children"][4]["cdata"]);
        $deliver_address_2 = trim($aData[0]["children"][2]["children"][5]["cdata"]);
        $deliver_city = trim($aData[0]["children"][2]["children"][6]["cdata"]);
        $deliver_zip_code = trim($aData[0]["children"][2]["children"][7]["cdata"]);
        $deliver_country = trim($aData[0]["children"][2]["children"][8]["cdata"]);
        $deliver_state = trim($aData[0]["children"][2]["children"][9]["cdata"]);
        $deliver_phone = trim($aData[0]["children"][2]["children"][10]["cdata"]);
        $deliver_cell_phone = trim($aData[0]["children"][2]["children"][11]["cdata"]);
        $deliver_evening_phone = trim($aData[0]["children"][2]["children"][12]["cdata"]);
        $deliver_fax = trim($aData[0]["children"][2]["children"][13]["cdata"]);
        $deliver_recipient_email = trim($aData[0]["children"][2]["children"][14]["cdata"]);
        $shipping_method = trim($aData[0]["children"][2]["children"][15]["cdata"]);
        $deliver_day = intval($aData[0]["children"][2]["children"][16]["cdata"]);
        $deliver_month = intval($aData[0]["children"][2]["children"][17]["cdata"]);
        $card_msg = trim($aData[0]["children"][2]["children"][18]["cdata"]);
        $signature = trim($aData[0]["children"][2]["children"][19]["cdata"]);
        $card_comment = trim($aData[0]["children"][2]["children"][20]["cdata"]);


        $payment_method = trim($aData[0]["children"][3]["children"][0]["cdata"]);
        $name_on_card = trim($aData[0]["children"][3]["children"][1]["cdata"]);
        $credit_card_number = trim($aData[0]["children"][3]["children"][2]["cdata"]);
        $credit_card_security_code = trim($aData[0]["children"][3]["children"][3]["cdata"]);
        $expire_month = trim($aData[0]["children"][3]["children"][4]["cdata"]);
        $expire_year = trim($aData[0]["children"][3]["children"][5]["cdata"]);

        $aProduct = array();
        $aSKU = array();
        $i = 0;
        foreach ($aData[0]["children"][4]["children"] as $value) {
            $sSKUCode = trim($aData[0]["children"][4]["children"][$i]["cdata"]);
            $nQuantityItem = intval(trim($aData[0]["children"][4]["children"][$i]["attrs"]["QUANTITY"][0]));

            if ($nQuantityItem <= 0)
                $nQuantityItem = 1;

            if ($sSKUCode) {
                $aProduct[$sSKUCode] = $nQuantityItem;
                $aSKU[$i] = "'" . trim($sSKUCode) . "'";
                $i++;
            }
        }
        $sSKU = implode(",", $aSKU);

        //======================================================================================================================================		
        /* Select user account */

        $query = "SELECT * FROM #__vm_user_info AS VUI, #__users AS U WHERE VUI.user_id = U.id AND U.username ='{$user_name}'  AND U.email = '{$account_email}' AND VUI.address_type = 'BT'";
        $database->setQuery($query);
        $oUserInfo = $database->loadObjectList();
        $oUser = $oUserInfo[0];
//		echo "<br/>1.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


        if ($oUser) {
            $user_id = $oUser->user_id;
            $bill_company_name = $oUser->company;
            $bill_last_name = $oUser->last_name;
            $bill_first_name = $oUser->first_name;
            $bill_middle_name = $oUser->middle_name;
            $bill_phone = $oUser->phone_1;
            $bill_fax = $oUser->fax;
            $bill_address_1 = $oUser->address_1;
            $bill_address_2 = $oUser->address_2;
            $bill_city = $oUser->city;
            $bill_state = $oUser->state;
            $bill_country = $oUser->country;
            $bill_zip_code = $oUser->zip;
            $bill_email = $oUser->user_email;


            /* Insert the User Billto & Shipto Info to User Information Manager Table */
            $user_info_id = md5($user_id . time());


            $query = "	SELECT user_info_id FROM #__vm_user_info 
						WHERE address_type = 'ST' 
						AND address_type_name = '{$address_user_name}' 
						AND ( address_1 = '{$deliver_address_1}' || address_2 = '{$deliver_address_2}' ) 
						AND city = '{$deliver_city}' 
						AND state = '{$deliver_state}' 
						AND country = '{$deliver_country}' 
						AND zip = '{$deliver_zip_code}'";
            $database->setQuery($query);
            $oUserInfo = $database->loadObjectList();
            $oUser = $oUserInfo[0];
//			echo "<br/>2.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            if (!$oUser) {
                $query = "INSERT INTO #__vm_user_info( user_info_id, 
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
					   	   VALUES(  '" . md5($user_info_id) . "', 
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
            } else {
                /* $address_user_name		= $oUser->address_type_name;
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
                  $deliver_recipient_email= $oUser->user_email; */
            }


            $query = "SELECT * FROM #__vm_vendor WHERE vendor_country = '{$bill_country}'";
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            $aVendor = $rows[0];
            $vendor_id = $aVendor->vendor_id;
            if (!$vendor_id)
                $vendor_id = 1;
            $PaymentVar["vendor_id"] = $vendor_id;
            $vendor_currency = $aVendor->vendor_currency;
            $PaymentVar["vendor_currency"] = $vendor_currency;


            $query = " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id 
									FROM #__vm_shipping_rate AS VSR
									INNER JOIN #__vm_shipping_carrier AS VSC
									ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id  
									WHERE VSR.shipping_rate_name  = '{$shipping_method}'";
            $database->setQuery($query);
            $aShippingMethod = $database->loadRow();
            $sShippingMethod = "standard_shipping|" . implode("|", $aShippingMethod);
//			echo "<br/>5.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            $query = " SELECT VMP.product_price, VM.product_sku, VTR.tax_rate 
									FROM #__vm_product AS VM 
									LEFT JOIN #__vm_product_price AS VMP 
									ON VM.product_id = VMP.product_id 
									LEFT JOIN  #__vm_tax_rate AS VTR 
									ON VM.product_tax_id = VTR.tax_rate_id 
									WHERE VM.product_sku IN ({$sSKU})";
            $database->setQuery($query);
            $rows = $database->loadObjectList();
//			echo "<br/>6.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            /* ==========================Canculate Tax and Total Order========================== */
            $nTotalPrice = 0;
            $nSubTotalPrice = 0;
            $nTotalTax = 0;
            $order_tax_details = array();

            /* print_r($rows);
              echo "<br/><br/>";
              print_r($aProduct);
              echo "<br/><br/>";
              echo $aProduct['CO92']."aaaaaaaaaaa"; */

            if (count($rows)) {
                foreach ($rows as $value) {
                    if (!isset($order_tax_details[$value->tax_rate])) {
                        $order_tax_details[$value->tax_rate] = doubleval($value->tax_rate) * doubleval($value->product_price);
                    } else {
                        $order_tax_details[$value->tax_rate] = $order_tax_details[$value->tax_rate] + (doubleval($value->tax_rate) * doubleval($value->product_price));
                    }

                    $nItemTax = doubleval($value->tax_rate) * doubleval($value->product_price);
                    $nTotalTax += $nItemTax * $aProduct[$value->product_sku];
                    $nSubTotalPrice += ($nItemTax + doubleval($value->product_price)) * $aProduct[$value->product_sku];
                    //echo $nItemTax."===".$nSubTotalPrice."===".$value->tax_rate."===".$value->product_price."===".$aProduct[$value->product_sku]."<br/><br/>";
                }
            }

            /* ==========================Canculate Deliver Fee========================== */
            $nDeliverSameDayFee = 0;
            $nSpecialDeliver = 0;
            $nDeliverPostalCodeFee = 0;
            $nDeliverMethodFee = 0;
            $nDeliverFee = 0;
            $nDeliverFeeTax = 0;


            $query = "SELECT options FROM tbl_options WHERE type='cut_off_time'";
            $database->setQuery($query);
            $sOptionParam = $database->loadResult();
            $aOptionParam = explode("[--1--]", $sOptionParam);
            $time_limit = $aOptionParam[0] * 60 + $aOptionParam[1];
//			echo "<br/>6.1.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            if (intval($aOptionParam[0]) >= 12) {
                $aInfomation['time'] = ( intval($aOptionParam[0]) - 12 ) . ":" . $aOptionParam[1] . " PM";
            } else {
                $aInfomation['time'] = intval($aOptionParam[0]) . ":" . $aOptionParam[1] . " AM";
            }


            $day_now = intval(date('j', time()));
            $month_now = intval(date('m', time()));
            $year_now = intval(date('Y', time()));
            $hour_now = intval(date('H', time()));
            $minute_now = intval(date('i', time()));
            $time_now = $hour_now * 60 + $minute_now;

            //die( $month_now . "========" . $deliver_month  . "========" . $day_now  . "========" . $deliver_day );

            if (( $month_now > $deliver_month && $month_now != 12 ) || ( $day_now > $deliver_day && $month_now == $deliver_month )) {
                mosRedirect("index2.php?option=$option&act=$act", "Your deliver date is incorrect! Please choose other deliver date again!");
            }

            $query = "SELECT id FROM tbl_options WHERE type='deliver_option' AND name ='{$deliver_month}/{$deliver_day}' ";
            $database->setQuery($query);
            $bUnvailableDate = intval($database->loadResult());

            if ($bUnvailableDate) {
                mosRedirect("index2.php?option=$option&act=$act", "Our service is not available with this deliver date! Please choose other deliver date again!");
            }


            //echo $nTimeNow."===".$nHourNow."===".$nMinuteNow."===".$nTimeLimit;
            if ($time_now >= $time_limit) {
                $cut_off_time = 1;
            } else {
                $cut_off_time = 0;
                if ($day_now == $deliver_day && $month_now == $deliver_month) {
                    $nDeliverSameDayFee = floatval($aOptionParam[2]);
                }
            }


            $query = "SELECT options FROM tbl_options WHERE type='special_deliver' AND name ='{$deliver_month}/{$deliver_day}' ";
            $database->setQuery($query);
            $nSpecialDeliver = floatval($database->loadResult());
//			echo "<br/>7.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";



            $query = "SELECT options FROM tbl_options WHERE type='postal_code' AND name ='{$deliver_zip_code}' ";
            $database->setQuery($query);
            $sPostalCode = $database->loadResult();
            $aPostalCode = explode("[--1--]", $sPostalCode);
            $nDeliverPostalCodeFee = floatval($aPostalCode[2]);
//			echo "<br/>8.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            $query = " SELECT shipping_rate_id, shipping_rate_value, tax_rate, shipping_rate_id, shipping_rate_name 
									FROM #__vm_shipping_rate 
									INNER JOIN #__vm_tax_rate 
									ON shipping_rate_vat_id = tax_rate_id 
									WHERE shipping_rate_name  = '{$shipping_method}'";
            $database->setQuery($query);
            $oShippingRateInfo = $database->loadObjectList();
            $aShippingRateInfo = $oShippingRateInfo[0];
            $nDeliverMethodFee = floatval($aShippingRateInfo->shipping_rate_value);
//			echo "<br/>9.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            $nDeliverFee = $nDeliverSameDayFee + $nSpecialDeliver + $nDeliverPostalCodeFee + $nDeliverMethodFee;
            $nDeliverFeeTax = ( $nDeliverFee * $aShippingRateInfo->tax_rate );
            $nDeliverFee = $nDeliverFeeTax + $nDeliverFee;

            $nTotalPrice = $nSubTotalPrice + $nDeliverFee;


            /* die( "Total Price:".$nTotalPrice . "====Sub Total Price: " . $nSubTotalPrice . "====Total Tax: " . $nTotalTax  . "====Deliver Fee: " . $nDeliverFee   . "====Deliver Fee Tax: " . $nDeliverFeeTax  
              . "===Deliver Same Day Fee: " . $nDeliverSameDayFee. "===Special Deliver: " . $nSpecialDeliver. "===Deliver Postal Code Fee: " . $nDeliverPostalCodeFee. "===Deliver Method Fee:" . $nDeliverMethodFee ); */

            /* Insert the main order information */
            $order_number = md5("order" . $user_id . time());


            //================================== PAYMENT =========================================
            $VM_LANG = new vmLanguage();
            $PaymentVar["user_id"] = $user_id;
            $PaymentVar["bill_company_name"] = $bill_company_name;
            $PaymentVar["bill_last_name"] = $bill_last_name;
            $PaymentVar["bill_first_name"] = $bill_first_name;
            $PaymentVar["bill_middle_name"] = $bill_middle_name;
            $PaymentVar["bill_phone"] = $bill_phone;
            $PaymentVar["bill_fax"] = $bill_fax;
            $PaymentVar["bill_address_1"] = $bill_address_1;
            $PaymentVar["bill_address_2"] = $bill_address_2;
            $PaymentVar["bill_city"] = $bill_city;
            $PaymentVar["bill_state"] = $bill_state;
            $PaymentVar["bill_country"] = $bill_country;
            $PaymentVar["bill_zip_code"] = $bill_zip_code;
            $PaymentVar["bill_email"] = $bill_email;
            $PaymentVar["expire_month"] = $expire_month;
            $PaymentVar["expire_year"] = $expire_year;
            $PaymentVar["order_payment_number"] = $credit_card_number;
            $PaymentVar["credit_card_code"] = $credit_card_security_code;

            $aResult = array();
            if (!process_payment($order_number, $total_price, $PaymentVar, $aResult)) {
                $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
            }

            if ($aResult["approved"]) {
                $order_status = "A";
                $payment_msg = " and Payment Approved";
            } else {
                $order_status = "P";
                $payment_msg = " and Payment Failed";
            }
            //====================================================================================


            $query = "INSERT INTO #__vm_orders( user_id, 
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
						   	   			'" . serialize($order_tax_details) . "', 
						   	   			{$nDeliverFee}, 
						   	   		   	{$nDeliverFeeTax}, 
						   	   		   	'{$vendor_currency}', 
						   	   		   	'$order_status', 
						   	   		   	" . $timestamp . ", 
						   	   		   	" . $timestamp . ", 
						   	   		   	'" . ($deliver_day . "-" . $deliver_month . "-" . date("Y", time())) . "', 
						   	   		   	'" . $sShippingMethod . "', 
								   	   	'" . htmlspecialchars(strip_tags($card_msg)) . "', 
								   	   	'" . htmlspecialchars(strip_tags($signature)) . "', 
								   	   	'00000', 
								   	   	'" . htmlspecialchars(strip_tags($card_comment)) . "', 
								   	   	'" . htmlspecialchars(strip_tags($user_name)) . "' )";
            $database->setQuery($query);
            $database->query();
            $order_id = $database->insertid();
//			echo "<br/>10.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


            /* Insert the initial Order History. */
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


            /* Insert the Order payment info */
            $payment_number = preg_replace("/ |-/", "", $credit_card_number);


            // Payment number is encrypted using mySQL ENCODE function.
            $query = "INSERT INTO #__vm_order_payment(	order_id, 
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
								'($payment_number}', 
								'" . strtotime("{$expire_month}/01/{$expire_year}") . "',
								'',
								'{$name_on_card}',			
								'')";
            //$query .= "'" . $vmInputFilter->safeSQL( @$d["order_payment_trans_id"] ). "'";
            $database->setQuery($query);
            $database->query();
//			echo "<br/>12.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

            /* Insert the User Billto & Shipto Info to Order Information Manager Table */
            $query = "INSERT INTO #__vm_order_user_info (  order_id, 
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
						   	   VALUES(  '" . $order_id . "', 
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


            $query = "INSERT INTO #__vm_order_user_info (  order_id, 
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
						   	   VALUES(  '" . $order_id . "', 
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

            /* Insert all Products from the Cart into order line items */
            $query = " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate 
									FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
									ON VM.product_id = VMP.product_id 
									LEFT JOIN  #__vm_tax_rate AS VTR 
									ON VM.product_tax_id = VTR.tax_rate_id 
									WHERE  VM.product_sku IN ({$sSKU})";
            $database->setQuery($query);
            $rows = $database->loadObjectList();
//			echo "<br/>15.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";

            if (count($rows)) {
                foreach ($rows as $value) {
                    $nQuantityTemp = $aProduct[$value->product_sku];

                    if (!$nQuantityTemp)
                        $nQuantityTemp = 1;

                    $query = "INSERT INTO #__vm_order_item (   order_id, 
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
						   	   			" . $value->product_id . ", 
						   	   			'" . addslashes($value->product_sku) . "', 
						   	   			'" . addslashes($value->product_name) . "', 	
						   	   			" . intval($nQuantityTemp) . ", 
						   	   			" . $value->product_price . ", 	
						   	   			" . ( ($value->product_price * $value->tax_rate ) + $value->product_price ) . ", 	
						   	   			'" . $value->product_currency . "', 
						   	   			'P', 
						   	   			'" . addslashes($value->product_desc) . "', 
						   	   			'{$timestamp}', 
						   	   			'{$timestamp}'
						   	   			 )";
                    $database->setQuery($query);
                    $database->query();
//					echo "<br/>16.<br/>" . $query . "<br/>".$database->getErrorMsg()."<br/><br/>";


                    /* Insert ORDER_PRODUCT_TYPE */
                    $query = "SELECT * FROM #__vm_product_product_type_xref, #__vm_product_type 
							  WHERE #__vm_product_product_type_xref.product_id = '" . $value->product_id . "' 
							  AND #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id";
                    $database->setQuery($query);
                    $rows2 = $database->loadObjectList();

                    foreach ($rows2 as $item) {
                        $product_type_id = $item->product_type_id;

                        $query = "  SELECT * 
									FROM #__vm_product_type_$product_type_id 
									WHERE product_id='" . $value->product_id . "' ";
                        $database->setQuery($query);
                        $rows3 = $database->loadObjectList();
                        $item2 = $rows3[0];

                        $query = "INSERT INTO #__vm_order_product_type( order_id, 
																	product_id, 
																	product_type_name, 
																	quantity, price) 															
									VALUES ( {$order_id}, 
											 " . $value->product_id . "', 
											 '" . addslashes($item2->product_type_name) . "', 
											 " . $item2->quantity . ", 
											 " . $item2->price . ")";
                        $database->setQuery($query);
                        $database->query();
                    }


                    /* Update Stock Level and Product Sales */
                    if ($value->product_in_stock) {
                        $query = "	UPDATE #__vm_product 
									SET product_in_stock = product_in_stock - " . intval($nQuantityTemp) . " 
									WHERE product_id = '" . $value->product_id . "'";
                        $database->setQuery($query);
                        $database->query($query);
                    }

                    $query = "	UPDATE #__vm_product 
								SET product_sales= product_sales + " . intval($nQuantityTemp) . "  
								WHERE product_id='" . $value->product_id . "'";
                    $database->query($query);
                }
            }

            $msg = "Save Order Successfully {$payment_msg}";
        } else {
            $msg = "This user info is not exist in our system! Please try again!";
        }
    } else {
        $msg = "This upload process is wrong! Please try again!";
    }

    //echo $msg;
    mosRedirect("index2.php?option=$option&act=$act", $msg);
}

//========================================================================================
function do_upload($file, $dest_dir) {
    global $clearUploads;


    if ($act) {
        $act = "&act=$act";
    }

    $format = substr($file['name'], -3);

    $allowable = array('xml', 'XML');

    $noMatch = 0;
    foreach ($allowable as $ext) {
        if (strcasecmp($format, $ext) == 0) {
            $noMatch = 1;
        }
    }

    if (!$noMatch) {
        return false;
    }

    $sFileName = strtolower(time() . ".$format");
    if (!move_uploaded_file($file['tmp_name'], $dest_dir . $sFileName)) {
        return false;
    }

    $clearUploads = true;
    return $sFileName;
}

/*
 * name: process_payment()
 * created by: durian
 * description: process transaction with Pay Flow Pro
 * parameters:
 * 	$order_number, the number of the order we're processing here
 * 	$order_total, the total $ of the order
 * returns: 
 */

function process_payment($order_number, $order_total, $PaymentVar, &$aResult) {
    global $vendor_mail, $vendor_currency, $database;
    $VM_LANG = new vmLanguage();
    $vmLogger = new vmLog();
    $order_total = round($order_total, 2);

    $ps_vendor_id = $PaymentVar["vendor_id"];
    $auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : "";

    $query = "SELECT * FROM #__shopper_group WHERE vendor_id = {$ps_vendor_id}";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aShopperGroup = $rows[0];
    $shopper_group_id = $aShopperGroup->shopper_group_id;


    /*     * * Get the Configuration File for Pay Flow Pro ** */
    require_once(CLASSPATH . "payment/ps_pfp2.cfg.php");

    // Get the Transaction Key securely from the database
    $sql = " SELECT payment_passkey as passkey 
				FROM #__vm_payment_method 
				WHERE payment_class='ps_pfp2' AND shopper_group_id = $shopper_group_id";
    $database->setQuery($sql);
    $passkey = $database->loadResult();
    //speedhack
    $passkey = '1';
    //end speedhack

    if (empty($passkey)) {
        $vmLogger->err($VM_LANG->_PHPSHOP_PAYMENT_ERROR .
                "PFP account password is empty. You must " .
                "adjust your settings.");
        return false;
    }


    /* XXX If a test request, use "test-payflow.verisign.com" */
    if (PFP2_TEST_REQUEST == 'TRUE') {
        $host = 'https://pilot-payflowpro.verisign.com/transaction';
        $vmLogger->debug("Using test site: " . $host);
    } else {
        $host = 'https://payflow.verisign.com';
        $vmLogger->debug("Using real site: " . $host);
    }


    $name = $PaymentVar["bill_first_name"] . ' ' . $PaymentVar["bill_last_name"];
    $expmon = $PaymentVar["expire_month"];
    $expyear = $PaymentVar["expire_year"];
    $expmon = sprintf("%02d", $expmon % 100);
    $expyear = sprintf("%02d", $expyear % 100);
    /* Pay Flow Pro vars to send */
    $request = array(
        'USER' => PFP2_USER,
        'VENDOR' => PFP2_LOGIN,
        'PWD' => $passkey,
        'PARTNER' => PFP2_PARTNER,
        'TENDER' => 'C',
        /* This needs to be either [S]ale or [A]uthorize */
        'TRXTYPE' => PFP2_TYPE,
        'AMT' => $order_total,
        'NAME' => substr($name, 0, 30),
        'ACCT' => $PaymentVar["order_payment_number"],
        'CVV2' => $PaymentVar["credit_card_code"],
        'EXPDATE' => $expmon . $expyear,
        'STREET' => substr($PaymentVar["bill_address_1"], 0, 30),
        'ZIP' => substr($PaymentVar["bill_zip_code"], 0, 9),
        'COMMENT1' => substr('Email: ' . $PaymentVar["bill_email"] .
                ', Remote IP: ' . $_SERVER["REMOTE_ADDR"], 0, 128),
        'CUSTREF' => substr($order_number, 0, 12),
    );

    //speedhack

    require(CLASSPATH . "payment/pfpro.class.inc");

    $p = new PFPro($request, $host);
    /* print_r($p) ;
      die(); */

    $response = $p->trx_result;
    //	$response = pfpro_process($request, $host);
    //speedhack
//	print_r($response);

    if ($response['RESULT'] == '0') {
        /* We're approved (or captured)! */
        $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS . ': ' . $response['RESPMSG'];
        /* record transaction ID */
        $aResult["order_payment_trans_id"] = $response['PNREF'];

        $aResult["approved"] = 1;

        return True;
    } elseif ($response['RESULT'] == '12') {
        /* credit card declined - get out the scissors */
        $vmLogger->err($response['RESPMSG']);
        $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_ERROR . ': ' . $response['RESPMSG'];
        /* record transaction ID */
        $aResult["order_payment_trans_id"] = $response['PNREF'];

        $aResult["approved"] = 0;

        return False;
    } else {
        // Transaction Error
        $vmLogger->err($response['RESPMSG']);
        $aResult["order_payment_log"] = $response['RESPMSG'];
        /* record transaction ID */
        $aResult["order_payment_trans_id"] = $response['PNREF'];

        $aResult["approved"] = 0;

        return False;
    }
}

?>
