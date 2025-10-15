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

if ($_SERVER['SERVER_PORT'] != 443 || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "on")) {
    $url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    header("Location: $url");
}

global $mosConfig_absolute_path;

require_once($mainframe->getPath('admin_html'));
require_once($mainframe->getPath('class'));
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/language.class.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/languages/english.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/Log/Log.php");
require_once $mosConfig_absolute_path.'/includes/stripe/init.php';


$cid = josGetArrayInts('cid');
$act = mosGetParam($_REQUEST, "act", "");
$task = mosGetParam($_REQUEST, "task", "");
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
            case 'selectDeliveryOption':
                selectDeliveryOption();
                break;
            case 'check_ccard':
                check_ccard();
                break;
            case 'check_account_info':
                checkAccountInfo($option);
                break;
            case 'check_email_address_exist':
                checkEmailAddressExist();
                break;
            case 'check_corporate_user':
                checkCorporateUser($option);
                break;
            case 'check_user_bt_info':
                CheckUserBtInfo($option);
                break;
            case 'get_donation':
                get_donation();
                break;
            case 'updateBillingInfo':
                updateBillingInfo();
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
                savePhoneOrderSuccess($option);

                break;
            case 'edit_delivery_address':
                edit_delivery_address();
                break;

            default:
                makePhoneOrder($option);
                break;
        }
        break;
}
function savePhoneOrderSuccess($option){
    global $database;
    $order_id = mosGetParam($_REQUEST, "order_id", "");
    $stripePaymentLinkUrl = $_SESSION['stripe_payment_url'] ?? null;
    if(isset($_SESSION['order_id_for_stripe_payment']) && $_SESSION['order_id_for_stripe_payment'] == $order_id) {

        unset($_SESSION['stripe_payment_url']);
        unset($_SESSION['order_id_for_stripe_payment']);

    }


    $query = "SELECT order_total,order_currency,coupon_code, order_tax, order_shipping 
                        FROM jos_vm_orders 
                        WHERE order_id = $order_id ";
    $database->setQuery($query);
    $orderObj = false;
    $database->loadObject($orderObj);

    $query = "SELECT order_item_sku,order_item_name, product_final_price,c.category_name, product_quantity 
            FROM jos_vm_order_item as i
            left join jos_vm_product as p on p.product_sku = order_item_sku 
            left join jos_vm_product_category_xref x on x.product_id = p.product_id 
            left join jos_vm_category c on c.category_id = x.category_id 
            WHERE order_id=	'$order_id' group by order_item_id";
    $database->setQuery($query);
    $productsObjList = $database->loadObjectList();

    $orderProducts = [];
    foreach ($productsObjList as $productObj){
        $orderProducts[] =[
            'item_name' => $productObj->order_item_name,
            'item_id' => $productObj->order_item_sku,
            'price' => $productObj->product_final_price,
            'item_category' => $productObj->category_name,
            'item_variant' => "standard",
            'quantity' => $productObj->product_quantity
        ];
    }

    $orderProductsJson = json_encode($orderProducts);
    HTML_PhoneOrder::savePhoneOrderSuccess($option,$orderProductsJson,$orderObj,$stripePaymentLinkUrl);

}

function CheckUserBtInfo()
{
    global $database;
    $sEmail = mosGetParam($_REQUEST, "email", "");
    $result['result'] = false;
    $query = "SELECT u.id FROM jos_users as u
 inner join jos_vm_user_info as i on i.user_id=u.id and i.address_type='BT'
 WHERE u.email = '{$sEmail}'";
    $database->setQuery($query);
    $oRow = $database->loadObjectList();
    $oUser = $oRow[0];
    $user_id = intval($oUser->id);
    if ($user_id) {
        $result['result'] = true;
    }
    die(json_encode($result));
}

function edit_delivery_address()
{
    global $database;
    $delivery_address_id = $_POST['delivery_info_id'];

    $query = "SELECT * FROM #__vm_user_info AS VUI WHERE  VUI.user_info_id ='{$delivery_address_id}'";
    $database->setQuery($query);
    $oUserInfo = $database->loadObjectList();
    $oUser = json_encode($oUserInfo[0]);

    exit($oUser);
}

function get_donation()
{
    global $database;
    $zip = $_POST['zip'];
    $query = "SELECT WH.warehouse_id FROM #__vm_warehouse AS WH, #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.published=1 AND PWH.postal_code LIKE '" . substr($zip, 0, 3) . "%'";
    $database->setQuery($query);
    $oWarehouse = $database->loadResult();
    $warehouse_id = 0;
    if ($oWarehouse) {
        $warehouse_id = $oWarehouse;
    }
    $query_don = "SELECT `name`,`price`,`text`,`id` FROM `tbl_donation_vars` where ( published=1 and warehouse_id='" . $warehouse_id . "') OR ( published=1 and warehouse_id=0)";
    $database->setQuery($query_don);
    $res_don = $database->loadRow();
    $res = json_encode($res_don);
    exit($res);
}

function updateBillingInfo()
{
    global $database, $my;

    $user_id = intval(mosGetParam($_REQUEST, "user_id", 0));

    $bill_company_name = mosGetParam($_REQUEST, "bill_company_name", "");
    $bill_first_name = mosGetParam($_REQUEST, "bill_first_name", "");
    $bill_last_name = mosGetParam($_REQUEST, "bill_last_name", "");
    $bill_middle_name = mosGetParam($_REQUEST, "bill_middle_name", "");
    $bill_suite = mosGetParam($_REQUEST, "bill_suite", "");
    $bill_street_number = mosGetParam($_REQUEST, "bill_street_number", "");
    $bill_street_name = mosGetParam($_REQUEST, "bill_street_name", "");
    $bill_city = mosGetParam($_REQUEST, "bill_city", "");
    $bill_district = mosGetParam($_REQUEST, "bill_district", "");
    $bill_zip_code = mosGetParam($_REQUEST, "bill_zip_code", "");
    $bill_country = mosGetParam($_REQUEST, "bill_country", "");
    $bill_state = mosGetParam($_REQUEST, "bill_state", "");
    $bill_phone = mosGetParam($_REQUEST, "bill_phone", "");
    $bill_evening_phone = mosGetParam($_REQUEST, "bill_evening_phone", "");
    $addr = $bill_suite . ' ' . $bill_street_number . ' ' . $bill_street_name;
    if ($user_id) {
        $query_update = "UPDATE #__vm_user_info SET  
                                        company= '{$bill_company_name}', 
                                        last_name='{$bill_last_name}',
                                        first_name='{$bill_first_name}',
                                        phone_1='{$bill_phone}',
                                        city='{$bill_city}',
                                        district='{$bill_district}',
                                        state='{$bill_state}',
                                        middle_name	= '{$bill_middle_name}', 
                                        address_1='{$addr}',
                                        country='{$bill_country}',
                                        zip='{$bill_zip_code}',
                                        extra_field_1='{$bill_evening_phone}',
                                        suite='{$bill_suite}',
                                        street_number='{$bill_street_number}',
                                        street_name='{$bill_street_name}'  WHERE user_id = $user_id AND address_type='BT' ";
        $database->setQuery($query_update);
        $database->query();
        $result = "success";
    } else {
        $result = "error";
    }
    $res = json_encode($result);
    require_once '../end_access_log.php';
    exit($res);
}

function updatecredits($user_id, $order_id, $used_credits, $username)
{
    global $database, $mosConfig_offset;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    $used_credits = number_format($used_credits, 2, ".", "");
    $query = "SELECT `credits` FROM `jos_vm_users_credits` WHERE `user_id`=" . $user_id . "";
    $database->setQuery($query);
    $res = $database->loadResult();
    $current_value = $res - $used_credits;
    $current_value = $current_value;

    if ($used_credits) {

        $sql = "UPDATE `jos_vm_users_credits` SET `credits`=" . $current_value . " WHERE `user_id`=" . $user_id . "";
        $database->setQuery($sql);
        $database->query();

        $query = "INSERT INTO 
        `jos_vm_users_credits_uses` 
        (
            `user_id`,
            `order_id`,
            `credits`,
            `comments`,
            `username`,
            `datetime`
        )
        VALUES (
            " . (int)$user_id . ",
            " . (int)$order_id . ",
            '" . $database->getEscaped($used_credits) . "',
            '" . $database->getEscaped('Redeem $' . $used_credits . ' credits.') . "',
            '" . $database->getEscaped($username) . "',
            '" . $mysqlDatetime . "'
        )
        ";

        $database->setQuery($query);
        $database->query();
    }
}

function updatebucks($user_id, $order_id, $new_bucks, $used_bucks)
{
    global $database, $mosConfig_offset;
    if ($new_bucks <= 0)
        $new_bucks = 0;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    $new_bucks = number_format($new_bucks, 2, ".", "");
    $used_bucks = number_format($used_bucks, 2, ".", "");
    $query = "SELECT `bucks` FROM `tbl_bucks` WHERE `user_id`=" . $user_id . "";
    $database->setQuery($query);
    $res = $database->loadResult();
    $current_value = $res - $used_bucks;
    $current_value += $new_bucks;
    $current_value = number_format($current_value, 2, ".", "");
    if ($res) {
        $sql = "UPDATE `tbl_bucks` SET `bucks`=" . $current_value . " WHERE `user_id`=" . $user_id . "";
        $database->setQuery($sql);
        $database->query();
    } else {
        $sql = "INSERT INTO `tbl_bucks` (`bucks`, `user_id`) VALUES (" . $current_value . ", " . $user_id . ")";
        $database->setQuery($sql);
        $database->query();
    }

    if ($used_bucks) {
        $comment = "Used $$used_bucks Bucks Into $order_id order";
        $sql = "INSERT INTO `tbl_bucks_history` (`used_bucks`, `user_id`,`order_id`,`comment`,`date_added`) VALUES (" . $used_bucks . ", " . $user_id . ", " . $order_id . ", '" . $comment . "','" . $mysqlDatetime . "')";
        $database->setQuery($sql);
        $database->query();
    }
    $comment = "Added New Bucks $$new_bucks. Current Bucks is  $$current_value ";
    $sql = "INSERT INTO `tbl_bucks_history` (`user_id`,`order_id`,`comment`,`date_added`) VALUES ( " . $user_id . ", " . $order_id . ", '" . $comment . "','" . $mysqlDatetime . "')";
    $database->setQuery($sql);
    $database->query();
}

function adddonte($order_id, $donated_price, $used_donate_id)
{
    global $database;
    $sql = "INSERT INTO `tbl_used_donation` (`donation_id`,`donation_price`, `order_id`) VALUES (" . $used_donate_id . ", " . $donated_price . ", " . $order_id . ")";
    $database->setQuery($sql);
    $database->query();
}

function get_current_donation_id_price($donate)
{
    global $database;
    $query = "SELECT price FROM `tbl_donation_vars` WHERE `id`=" . $donate . "";
    $database->setQuery($query);
    $res = $database->loadResult();
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

function check_ccard()
{
    global $mosConfig_absolute_path, $database;

    include_once $mosConfig_absolute_path . '/CreditCard.php';
    $return = array();
    $credit_card_number = $_POST['ccard_number'];
    $result = CreditCard::validCreditCard($credit_card_number);
    if ($result && $result['valid']) {

        $mask = substr($credit_card_number, 0, 6) . 'x' . substr($credit_card_number, -4);

        $query = "SELECT `id`, `block` FROM `jos_vm_user_ccards` WHERE `block`=1 AND `mask`='" . $database->getEscaped($mask) . "'";
        $oCcard_block = false;
        $database->setQuery($query);
        $database->loadObject($oCcard_block);

        if ($oCcard_block) {
            $return['result'] = false;
            $return['error'] = 'ATTENTION! This card has been blocked.';
        } else {
            $return['result'] = true;
            $return['type'] = $result['type'];
        }
    } else {
        $return['result'] = false;
        $return['error'] = "Please enter a valid credit card number";
    }


    echo json_encode($return);

    require_once '../end_access_log.php';

    exit(0);
}

function selectDeliveryOption()
{
    global $database, $iso_client_lang, $mosConfig_live_site;

    $CurrentDeliveryDate = trim(mosGetParam($_REQUEST, 'delivery_date'));
    $select_sub = isset($_POST['select_sub']) ? $_POST['select_sub'] : '';
    if ($CurrentDeliveryDate) {
        $aCurrentDeliveryDate = explode("/", $CurrentDeliveryDate);

        $sDateWhere = " AND name LIKE '" . intval($aCurrentDeliveryDate[0]) . "/%'";
    }
    $deliveryOption_new = trim(mosGetParam($_REQUEST, 'delivery_option_new'));
    $adeliveryOption = explode('[--1--]', $deliveryOption_new);
    $real_post_code = $adeliveryOption[1];
    $real_state = $adeliveryOption[0];
    $unzip_obj = false;
    $oot = false;
    $zip_symbols = 4;
    $additionalDeliveryFee = 0;
    $zip = str_replace(' ', '', trim($database->getEscaped($real_post_code)));
    while (($unzip_obj == false) and ($zip_symbols > 0)) {
        $query = "SELECT 
                    `postal_code`,
                    `days_in_route`,
                    `deliverable`,
                    `additional_delivery_fee`,
                    `out_of_town`
                FROM `jos_postcode_warehouse`
                WHERE 
                    `postal_code` LIKE '" . substr($database->getEscaped($zip), 0, $zip_symbols) . "'
                AND 
                    `published`='1' LIMIT 1
                ";
        $unzip_obj = false;
        $database->setQuery($query);
        $database->loadObject($unzip_obj);
        $zip_symbols--;
    }
    if ($unzip_obj) {
        $additionalDeliveryFee = $unzip_obj->additional_delivery_fee;
        if ($unzip_obj->deliverable != 0) {
            $oot = $unzip_obj->out_of_town;
        }
    }


    $aProducts = trim(mosGetParam($_REQUEST, 'product_id'));
    $coupon_discount_code = trim(mosGetParam($_REQUEST, 'coupon_discount_code'));

    $query_unavailable = "SELECT name, options FROM tbl_options WHERE type='deliver_option' $sDateWhere";
    $database->setQuery($query_unavailable);
    $oUnAvailableDate = $database->loadObjectList();

    $aUnAvailableDate = array();
    if (count($oUnAvailableDate)) {
        foreach ($oUnAvailableDate as $item) {
            $aTemp = explode("/", $item->name);
            $aUnAvailableDate[] = intval($aTemp[1]);
            $aInfomation['UnAvailableDate'][] = $item->name . " - " . $item->options;
        }
    }


    $query_special_deliver = "SELECT name,options FROM tbl_options WHERE type='special_deliver' $sDateWhere";
    $database->setQuery($query_special_deliver);
    $oSpecialDeliver = $database->loadObjectList();

    $aSpecialDeliver = array();
    if (count($oSpecialDeliver)) {
        foreach ($oSpecialDeliver as $item) {
            $aTemp = explode("/", $item->name);
            $aSpecialDeliver[] = intval($aTemp[1]) . '-' . $item->options;
        }
    }


    $shipping_method = intval(mosGetParam($_REQUEST, 'shipping_method'));
    $query = "SELECT * FROM #__vm_shipping_rate WHERE shipping_rate_id = $shipping_method";
    $database->setQuery($query);
    $oShippingMethod = $database->loadObjectList();
    //print_r($oShippingMethod);
    //echo $bLocalFloristOnly."----------------";
    //#4379: Add DELIVERY OPTIONS to operator console
    $bLocalFloristOnly = false;
    if (trim($oShippingMethod[0]->shipping_rate_name) == "LOCAL FLORIST DELIVERY (ONLY)" && $oShippingMethod[0]->shipping_rate_value == 6.99) {
        $bLocalFloristOnly = true;
    }

    $aInfomation['ShippingMethod']['rate'] = floatval($oShippingMethod[0]->shipping_rate_value);
    $aInfomation['ShippingMethod']['label'] = $oShippingMethod[0]->shipping_rate_name;

    $query = "SELECT shipping_rate_id, shipping_rate_name FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();


    $aInfomation['ShippingMethod']['text'] = "";
    $j = 0;
    foreach ($rows as $item) {

        if ($shipping_method == $item->shipping_rate_id || (!$shipping_method && $j == 0)) {
            $sChecked = true;
        } else {
            $sChecked = false;
        }
        $shipping_rate_name = str_replace("Express Image", "<span class='express_image'>&nbsp;</span>", htmlspecialchars($item->shipping_rate_name, ENT_QUOTES));
        if ($additionalDeliveryFee) {
            $healthy = array("14.99", "18.98");
            $yummy = array(14.99 + $additionalDeliveryFee, 18.98 + $additionalDeliveryFee);
            $shipping_rate_name = str_replace($healthy, $yummy, $shipping_rate_name);
        }

        if ($select_sub == 'true') {
            $shipping_rate_name = '$14.99 Per month';
        }
        $aInfomation['ShippingMethod']['text'] .= '<div class="txt-1">'.($sChecked ? '<img src="images/tick.png" width="12" height="12" border="0" alt="">':'').'<label for="shipping_method' . $item->shipping_rate_id . '">' . $shipping_rate_name . '</label></div>';
        $j++;
        if ($select_sub == 'true') {
            break;
        }
    }
    $neededDay = $lastNeededDay = $nameOfHoliday = null;

    $query = "SELECT options
                            FROM `tbl_options`
                            WHERE `type` = 'holidays_type' and published = 1";
    $database->setQuery($query);
    $holidayOptions = $database->loadResult();

    if ($holidayOptions !== null) {
        $activeHoliday = [];
        foreach (json_decode($holidayOptions, true) as $key => $value) {
            $startDate = $value['start_date'];
            $amountDays = $value['amount_days'];
            $isActive = $value['isActive'];
            if ($startDate === null || $amountDays === null || !$isActive) {
                continue;
            }

            $activeHoliday = $value;
        }
        if ($activeHoliday !== null) {
            $neededDay =  (int) date('j', strtotime($activeHoliday['start_date']));
            $nameOfHoliday = $activeHoliday['name'];
        }
    }

    if (isset($activeHoliday) && $aCurrentDeliveryDate[0] == date("m")) {
        $monthName = date('F', strtotime($CurrentDeliveryDate));
        $lastDay = '';
        if ($activeHoliday['amount_days'] > 2) {
            $lastDay = strtr(' - {day}th', [
                '{day}' => $neededDay + $activeHoliday['amount_days'] - 1
            ]);
        }

        $aInfomation['holiday'] = strtr('If delivery is {monthName} {firstDay}, there may be a delay until {secondDay}th{lastDay}', [
            '{monthName}' => $monthName,
            '{firstDay}' => $neededDay,
            '{secondDay}' => $neededDay + 1,
            '{lastDay}' => $lastDay
        ]);
        $aInfomation['Blended'] = $nameOfHoliday. ': '.$monthName .' '. $neededDay ;
    }



    $query_limittime = "SELECT options FROM tbl_options WHERE type='cut_off_time' ";
    $database->setQuery($query_limittime);
    $sOptionParam = $database->loadResult();
    $aOptionParam = explode("[--1--]", $sOptionParam);
    $nTimeLimit = $aOptionParam[0] * 60 + $aOptionParam[1];
    $nHourNow = intval(date('H', time()));
    $nMinuteNow = intval(date('i', time()));
    $nTimeNow = $nHourNow * 60 + $nMinuteNow;

    if ($nTimeNow >= $nTimeLimit || $bLocalFloristOnly == true) {
        $aInfomation['CutOffTime'] = 0;
    } else {
        $aInfomation['CutOffTime'] = "$" . number_format($aOptionParam[2], 2, ".", "");
    }

    $deliveryPostalCode = trim(mosGetParam($_REQUEST, 'delivery_postalcode'));
    $deliveryState = trim(mosGetParam($_REQUEST, 'delivery_state'));


    $query = "SELECT * FROM tbl_freeshipping ORDER BY id";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $aFreeShipping = array();
    if (count($rows)) {
        foreach ($rows as $row) {
            $aFreeShipping[] = date("Y-n-j", intval($row->freedate));
        }
    }

    $query = "SELECT * FROM tbl_shipping_surcharge ORDER BY date";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $aShippingSurcharge = array();
    if (count($rows)) {
        $k = 0;
        foreach ($rows as $row) {
            $aShippingSurcharge[$k]["date"] = date("Y-n-j", strtotime($row->date));
            $aShippingSurcharge[$k]["price"] = $row->amount;
            $k++;
        }
    }


    $subtotalprice = isset($_REQUEST['subtotalprice']) ? $_REQUEST['subtotalprice'] : 0;

    $aInfomation['Calendar'] = draw_calendar($deliveryState, $CurrentDeliveryDate, $deliveryPostalCode, $aProducts, $coupon_discount_code, $select_sub, $subtotalprice, $deliveryOption_new, $oot,$additionalDeliveryFee);
    $aInfomation['CurrentDeliveryDate'] = $CurrentDeliveryDate;
    $aInfomation['PreDeliveryDate'] = "";
    $aInfomation['NextDeliveryDate'] = "";

    if ($aCurrentDeliveryDate[0] <= date("m") && $aCurrentDeliveryDate[2] <= date("Y")) {
        $aInfomation['PreDeliveryDate'] = "";
    } else {
        if (intval($aCurrentDeliveryDate[0]) == 1) {
            $aInfomation['PreDeliveryDate'] .= "12/" . $aCurrentDeliveryDate[1] . "/" . (intval($aCurrentDeliveryDate[2]) - 1);
        } else {
            $aInfomation['PreDeliveryDate'] .= (intval($aCurrentDeliveryDate[0]) - 1) . "/" . $aCurrentDeliveryDate[1] . "/" . $aCurrentDeliveryDate[2];
        }
    }

    if (intval($aCurrentDeliveryDate[0]) == 12) {
        $aInfomation['NextDeliveryDate'] .= "1/" . $aCurrentDeliveryDate[1] . "/" . (intval($aCurrentDeliveryDate[2]) + 1);
    } else {
        $aInfomation['NextDeliveryDate'] .= (intval($aCurrentDeliveryDate[0]) + 1) . "/" . $aCurrentDeliveryDate[1] . "/" . $aCurrentDeliveryDate[2];
    }


    HTML_PhoneOrder::selectDeliveryOption($aInfomation);
}

function isAllowedBlendByPostalByWarehouse($warehouseId,$oot)

{
    global $database;

    $query = "SELECT allow_local_blend,allow_out_of_town_blend
                            FROM `jos_vm_warehouse`
                            WHERE `warehouse_id` = ".$warehouseId;
    $warehouseBlendConfig = false;
    $database->setQuery($query);
    $database->loadObject($warehouseBlendConfig);

    if ($warehouseBlendConfig->allow_local_blend && $warehouseBlendConfig->allow_out_of_town_blend) {
        return true;
    }
    if ($warehouseBlendConfig->allow_local_blend && !$oot) {
        return true;
    }

    if ($warehouseBlendConfig->allow_out_of_town_blend && $oot) {
        return true;
    }
    return false;

}

/* draws a calendar */

//function draw_calendar($day, $month, $year, $aUnAvailableDate, $aSpecialDeliver, $oShippingMethod, $nCutOffTime, $nDeliveryPostalCode, $aFreeShipping, $bLocalFloristOnly = false, $aShippingSurcharge) {
function draw_calendar($deliveryState, $delivery_date, $deliveryPostalCode, $aProducts, $coupon_discount_code = '', $select_sub, $subtotalprice, $deliveryOption_new, $oot,$additionalDeliveryFee)
{
    global $mosConfig_lang, $database;

    date_default_timezone_set('Australia/Sydney');
    $discount_shipping = 0;
    /*
      if (substr($real_post_code, 0, 1) == '6') {
      date_default_timezone_set('Australia/Perth');

      $calendar .= '<br/>' . date('d.m H:i');
      } */
    if ($select_sub == 'true') {
        $subscription_month = 1;
    }

    $params = array();
    parse_str($aProducts, $params);

    $aProductsId = array();

    foreach ($params['product_id'] as $ids) {
        if ($ids > 0)
            $aProductsId[] = $ids;
    }


    $a_delivery_date = explode('/', $delivery_date);

    $month = $a_delivery_date[0];
    $day = $a_delivery_date[1];
    $year = $a_delivery_date[2];

    $shipping_method = intval(mosGetParam($_REQUEST, 'shipping_method','31'));
    $query = "SELECT shipping_rate_value FROM jos_vm_shipping_rate WHERE shipping_rate_id = $shipping_method";
    $database->setQuery($query);
    $shipping_price = $database->loadResult();

    if ($additionalDeliveryFee) {
        $shipping_price += $additionalDeliveryFee;
    }

    $sql = "SELECT * FROM #__vm_product_options WHERE product_id IN (" . implode(',', $aProductsId) . ")";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    $sql_platin = "SELECT count(product_id) as count FROM #__vm_product  WHERE product_sku LIKE 'PC-01' AND product_id IN (" . implode(',', $aProductsId) . ")";
    $database->setQuery($sql_platin);
    $row_platin = $database->loadObjectList();
    if (isset($row_platin[0]) && $row_platin[0]->count == '1') {
        $discount_shipping = 1;
        $_SESSION['platinum_cart'] = 1;
    }

    $adeliveryOption = explode('[--1--]', $deliveryOption_new);

    $real_post_code = $adeliveryOption[1];
    $real_city_name = $adeliveryOption[2];
    $real_state = $adeliveryOption[0];
    $real_country = $adeliveryOption[3];

    $sql_un_del = "SELECT  
                available_from_date,EXTRACT( DAY FROM `available_from_date` ) as 'available_from_day' , EXTRACT( MONTH FROM `available_from_date` ) as 'available_from_month',
                available_until_date,EXTRACT( DAY FROM `available_until_date` ) as 'available_until_day' , EXTRACT( MONTH FROM `available_until_date` ) as 'available_until_month',
                json_data, `description` FROM `tbl_unavailable_delivery` 
    WHERE '" . $year . (strlen($month) == 1 ? '0' . $month : $month) . "' BETWEEN EXTRACT(YEAR_MONTH FROM available_from_date) AND  EXTRACT(YEAR_MONTH FROM available_until_date) 
    ";

    $database->setQuery($sql_un_del);
    $row_un_del = $database->loadObjectList();

    $un_del_array = array();

    foreach ($row_un_del as $row) {

        $unavailable_states = $unavailable_cities = $unavailable_postalCodes = [];
        $jsonData = json_decode(html_entity_decode($row->json_data));
        if ($jsonData) {
            if ($jsonData->states) {
                $unavailable_states = $jsonData->states;
            }

            if ($jsonData->cities) {
                $unavailable_cities = $jsonData->cities;
            }

            if ($jsonData->postalCodes) {
                $unavailable_postalCodes = $jsonData->postalCodes;
            }
        }
        $available_from_date = new DateTime($row->available_from_date);
        $available_until_date = new DateTime($row->available_until_date);
        $available_until_date->modify('+1 day');
        $period = new DatePeriod(
            $available_from_date,
            new DateInterval('P1D'),
            $available_until_date
        );


        foreach ($period as $key => $value) {
            if ($value->format('Y') == $year and intval($value->format('m')) == $month) {
                if (
                    in_array($real_state, $unavailable_states) ||
                    in_array($real_post_code, $unavailable_postalCodes) ||
                    in_array(strtolower($real_city_name), array_map('strtolower', $unavailable_cities))

                ) {

                    $un_del_array[$value->format('j')] = $row->description;
                }
            }
        }
    }

    $nextDay = 0;
    $tuefri = 0;

    $freeShipping = 1;
    $freeShipping_order = false;

    foreach ($rows as $product_obj) {
        if ((int)$product_obj->no_delivery == 0) {
            $freeShipping = 0;
        }
        if ((int)$product_obj->no_delivery_order == 1) {
            $freeShipping_order = true;
        }
    }


    if ($freeShipping_order == true) {
        $freeShipping = 1;
    }

    if ($_REQUEST['user_id'] > 0) {
        $sql_platin = "SELECT count(id) as count FROM tbl_platinum_club  WHERE user_id=" . $_REQUEST['user_id'] . " AND `end_datetime` IS NULL";
        $database->setQuery($sql_platin);
        $row_platin_old = $database->loadObjectList();
        /**/
        if ($row_platin_old[0]->count > 0) {
            $discount_shipping = 1;
        }
    } else {

        /**/
        if (isset($row_platin[0])) {

            if ($row_platin[0]->count == '1') {

                $_SESSION['platinum_cart'] = 1;
                $discount_shipping = 1;
            }
        }
    }
    $_SESSION['free_shipping_by_price'] = false;
    $sql = "SELECT * FROM jos_freeshipping_price WHERE public=1";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    if (count($rows) > 0) {
        if ($rows[0]->price <= floatval($subtotalprice)) {
            $_SESSION['free_shipping_by_price'] = true;
            $freeShipping = 1;
        }
    }
    /* get shipping surchage */
    $query = "SELECT * FROM tbl_delivery_options ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $ShippingSurcharge = array();
    $unAvailable = array();
    $freeshipping = array();

    if (count($rows)) {
        $k = 0;
        foreach ($rows as $row) {
            if ($row->type == 'surcharge') {
                $ShippingSurcharge[$k]["date"] = date("Y-n-j", strtotime($row->calendar_day));
                $ShippingSurcharge[$k]["price"] = number_format($row->price, 2, '.', '');
            } elseif ($row->type == 'free') {
                $freeshipping[$k]['calendar_day'] = date("Y-n-j", strtotime($row->calendar_day));
                $freeshipping[$k]['name'] = $row->name;
            } elseif ($row->type == 'unavaliable') {
                $unAvailable[$k]['calendar_day'] = date("Y-n-j", strtotime($row->calendar_day));
                $unAvailable[$k]['name'] = $row->name;
            } elseif ($row->type == 'ootsurcharge') {
                if ($oot) { //go like undeliver
                    $unAvailable[$k]['calendar_day'] = date("Y-n-j", strtotime($row->calendar_day));
                    $unAvailable[$k]['name'] = $row->name;
                } else { // jsut usrcharge
                    $ShippingSurcharge[$k]["date"] = date("Y-n-j", strtotime($row->calendar_day));
                    $ShippingSurcharge[$k]["price"] = number_format($row->price, 2, '.', '');
                }

            } elseif ($row->type == 'oot') {
                if ($oot) {
                    $unAvailable[$k]['calendar_day'] = date("Y-n-j", strtotime($row->calendar_day));
                    $unAvailable[$k]['name'] = $row->name;
                }
            }
            $k++;
        }
    }
    /**/

    /* get coupon */


    if (!empty($coupon_discount_code)) {
        if (strpos($coupon_discount_code, "PC-") !== false) {
            $discount_shipping = 1;

            //$_SESSION['coupon_value'])
        }
    }

    /**/

    /* get postalcodes */


    $timezones = array(
        'AT' => 'Australia/Sydney',
        'NW' => 'Australia/Sydney',
        'NT' => 'Australia/Darwin',
        'QL' => 'Pacific/Guam',
        'SA' => 'Australia/Adelaide',
        'TA' => 'Australia/Hobart',
        'VI' => 'Australia/Melbourne',
        'WA' => 'Australia/Perth',
        'AU' => 'Pacific/Auckland',
        'BP' => 'Pacific/Auckland',
        'CA' => 'Pacific/Auckland',
        'GS' => 'Pacific/Auckland',
        'HB' => 'Pacific/Auckland',
        'MW' => 'Pacific/Auckland',
        'MB' => 'Pacific/Auckland',
        'NS' => 'Pacific/Auckland',
        'NL' => 'Pacific/Auckland',
        'OT' => 'Pacific/Auckland',
        'SL' => 'Pacific/Auckland',
        'TK' => 'Pacific/Auckland',
        'WK' => 'Pacific/Auckland',
        'WG' => 'Pacific/Auckland',
        'WC' => 'Pacific/Auckland',
    );
    if ($timezones[$real_state]) {
        date_default_timezone_set($timezones[$real_state]);
    } else {
        date_default_timezone_set('Australia/Sydney');
    }


    $query = "SELECT * FROM `jos_vm_postcode` WHERE `active`='1' ORDER BY `id` DESC";

    $database->setQuery($query);
    $result = $database->loadObjectList();

    $postcode_active = false;
    $postcode_reason = null;
    $date_other = null;

    foreach ($result as $item) {
        $postcodes = explode(';', $item->postcodes);

        foreach ($postcodes as $pc) {
            if (strripos($pc, '-') != false) {
                $pce = explode('-', $pc);
                if (in_array($real_post_code, range($pce[0], $pce[1])))
                    $postcode_active = true;
            } else {
                if ($real_post_code == $pc)
                    $postcode_active = true;
            }
        }

        if ($postcode_active == true)
            break 1;
        else {
            $first_date_other = $item->date_others;
            $first_postcode_reason = $item->reason;
        }
    }

    if ($postcode_active == false and isset($first_date_other) and isset($first_postcode_reason)) {
        $date_other = explode('/', $first_date_other);
        $postcode_reason = $first_postcode_reason;
    }

    $same_day_hour = 13;
    $next_day_hour = 16;
    $same_next_day_surcharge = 5;

    $same_day_limit = $same_day_hour * 60;
    $next_day_limit = $next_day_hour * 60;
    $hour_now = intval(date('H'));
    $minute_now = intval(date('i'));

    $time_now = $hour_now * 60 + $minute_now;

     $same_day = $next_day = false;

    if ($same_day_limit > $time_now) {
        $same_day = true;
    }
    if ($next_day_limit < $time_now) {
        $next_day = true;
    }


    $undeliverdayscount = 0;
    $undeliverpostalcode = 0;
    $postalcodeoption = false;
    $zip_symbols = 4;
    $supposedWarehouseId = '';
    if($real_country!='NZL') {
        while (($postalcodeoption == false) and ($zip_symbols > 0)) {
            $query = "SELECT 
                `postal_code`,
                `days_in_route`,
                `warehouse_id`,
                `deliverable` 
            FROM `jos_postcode_warehouse`
            WHERE 
                `postal_code` LIKE '" . substr($database->getEscaped($real_post_code), 0, $zip_symbols) . "'
            AND 
                `published`='1' LIMIT 1
            ";
            $postalcodeoption = false;
            $database->setQuery($query);
            $database->loadObject($postalcodeoption);
            $zip_symbols--;
        }
        if ($postalcodeoption) {
            $supposedWarehouseId = $postalcodeoption->warehouse_id;
            if ($postalcodeoption->deliverable == 0) {
                $undeliverpostalcode = 1;
            } else {
                $undeliverdayscount = $postalcodeoption->days_in_route;
            }
        }
    }


    $neededMonth = null;
    $blendedDayPrice = 19.99;

    $deliveryDateTimestamp = strtotime($delivery_date);

    $days_in_month = date('t', $deliveryDateTimestamp);

    $neededDay = null;

    $neededDays = [];


    $query = "SELECT options
                            FROM `tbl_options`
                            WHERE `type` = 'holidays_type' and published = 1";
    $database->setQuery($query);
    $holidayOptions = $database->loadResult();

    if ($holidayOptions !== null && isAllowedBlendByPostalByWarehouse($supposedWarehouseId,$oot)) {
        $activeHoliday = [];
        foreach (json_decode($holidayOptions, true) as $key => $value) {
            $startDate = $value['start_date'];
            $amountDays = $value['amount_days'];
            $isActive = $value['isActive'];
            if ($startDate === null || $amountDays === null || !$isActive) {
                continue;
            }

            $activeHoliday = $value;
        }
        if ($activeHoliday !== null) {
            $neededDay = (int) date('j', strtotime($activeHoliday['start_date']));
            $neededMonth = (int) date('m', strtotime($activeHoliday['start_date']));
            $amountDays = $activeHoliday['amount_days'] - 1;
            $lastNeededDay = $neededDay + $amountDays;
            if ($lastNeededDay > $days_in_month) {
                $lastNeededDay = $days_in_month;
            }
            $neededDays = range($neededDay, $lastNeededDay);
        }
    }





    $calendar = '<table cellpadding="0" cellspacing="0" class="calendar"><br/>';

    switch ($mosConfig_lang) {
        case 'french':
            $headings = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
            break;

        case 'english':

        default:
            $headings = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
            break;
    }

    $calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

    $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
    $days_in_this_week = 1;
    $day_counter = 0;
    $dates_array = array();

    $calendar .= '<tr class="calendar-row">';

    for ($x = 0; $x < $running_day; $x++) {
        $calendar .= '<td class="calendar-day-np">&nbsp;</td>';
        $days_in_this_week++;
    }

    for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
        $unAvailable_status = 0;
        $unAvailable_text = null;

        foreach ($unAvailable as $item) {
            if ($item['calendar_day'] == $year . '-' . $month . '-' . $list_day) {
                $unAvailable_status = 1;
                $unAvailable_text = $item['name'];
            }
        }
        if ($undeliverpostalcode) {
            $unAvailable_status = 1;
            $unAvailable_text = 'Undeliverable Postcode';
        }

        $freeshipping_status = 0;
        $freeshipping_text = null;

        foreach ($freeshipping as $item) {
            if ($item['calendar_day'] == $year . '-' . $month . '-' . $list_day) {
                $freeshipping_status = 1;
                $freeshipping_text = $item['name'];
            }
        }


        if ($list_day < (date('j') + $undeliverdayscount) and $month == date("m")
            or (is_array($date_other) and mktime(0, 0, 0, intval($date_other[0]), intval($date_other[1]), $date_other[2]) > mktime(0, 0, 0, intval($month), $list_day, intval($year)))
            or (isset($subscription_month) and $subscription_month > 0 and $list_day == date('j') and $month == date('n'))
            or $unAvailable_status == 1
            or ($tuefri == 1 and in_array(date('N', strtotime($list_day . '-' . intval($month) . '-' . intval($year))), array(1, 6, 7)))
        ) {
            $day_class = 'calendar-day-np';
        } else {
            $day_class = 'calendar-day';
        }

        if ($list_day == $day and $month == date('m') and (!isset($subscription_month) or $subscription_month == 0)) {
            $day_class_2 = 'calendar-today';
        } elseif ($list_day == date('j') and $month == date('m')) {
            $day_class_2 = 'calendar-deliver';
        } else {
            $day_class_2 = '';
        }

        /* organization shipping price and js function */

        $shipping_surcharge_price = 0;


        foreach ($ShippingSurcharge as $i) {
            if ($i['date'] == $year . '-' . $month . '-' . $list_day) {
                $shipping_surcharge_price = $i['price'];
            }
        }


        $shipping_total_price = '';
        $shipping_div_price = '';

        $dayDeliveryDate = date('d-m-Y', strtotime($month . '/' . $list_day . '/' . $year));
        if ($supposedWarehouseId) {
            $query = "SElECT (l.orders_count - count(order_id)) as posible_order_count
                        FROM jos_vm_orders as o 
                        join jos_vm_warehouse as w on w.warehouse_code=o.warehouse
                        join jos_vm_warehouse_order_limit as l on l.warehouse_id=w.warehouse_id
                        WHERE o.ddate = '$dayDeliveryDate' AND `o`.`order_status` NOT IN ('X','O') and w.warehouse_id=$supposedWarehouseId group by o.warehouse ";
            $possibleOrderCount = false;
            $database->setQuery($query);
            $database->loadObject($possibleOrderCount);
        }
        if ($list_day == date('j') and $month == date("m") AND $same_day == false) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'Cut off time.\');" ';
            //$day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'Cut off time.\');" ';
        } elseif ($possibleOrderCount && $possibleOrderCount->posible_order_count <= 0) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'Warehouse orders limit has already passed for this day\');" ';
        } elseif (array_key_exists($list_day, $un_del_array)) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $un_del_array[$list_day] . '\');" ';
        } elseif ($list_day < date('j') and $month == date("m")) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'This day has already passed\');" ';
        } elseif ($list_day < (date('j') + $undeliverdayscount) and $month == date("m")) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'This day has blocked for this postal code\');" ';
        } elseif ($list_day == date('j') and $month == date("m") and $nextDay == 1) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'Products that you have ordered, the ability to deliver the next day.\');" ';
        } elseif ($tuefri == 1 and in_array(date('N', strtotime($list_day . '-' . intval($month) . '-' . intval($year))), array(1, 6, 7))) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'Your order has the products that are delivered only from Tuesday to Friday\');" ';
        } elseif (is_array($date_other) and mktime(0, 0, 0, intval($date_other[0]), intval($date_other[1]), $date_other[2]) > mktime(0, 0, 0, intval($month), $list_day, intval($year))) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'<b>The reason that you can not make an order for this day</b>:<br/>' . $postcode_reason . '\');" ';
        } elseif ($unAvailable_status == 1) {
            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $unAvailable_text . '\');" ';
        }elseif (in_array($list_day, $neededDays) && ($neededMonth == $month)) {
            $day_function = ' onmouseover="changeDay(' . 1 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'<span>This is blanded day</span>\');" onclick="chooseDay(' . 1 .',\'' . $month . '/' . $neededDay . '/' . $year . '\', \'' . $blendedDayPrice . '\');" ';
        }
        else {
            $message = '';
            $day_type = 'Other';

            if (isset($subscription_month) and $subscription_month > 0) {
                $shipping_total_price = $shipping_price;
            } else {
                if ($freeShipping == 1) {
                    $message = '<b>Free shipping</b>';
                } elseif ($freeshipping_status == 1) {
                    $message = '<b>' . $freeshipping_text . '</b>';
                } else {


                    if ($list_day == date('j') and $month == date("m") AND $same_day == true) {
                         $shipping_total_price += $same_next_day_surcharge;

                    } elseif ($list_day == (date('j') + 1) and $month == date("m") AND $next_day == true) {
                        $shipping_total_price += $same_next_day_surcharge;

                    }
                    if ($shipping_surcharge_price != 0) {
                        $message .= _DELIVERY_EXTRA_SURCHARGE . '<br/>$' . $shipping_surcharge_price;
                    }

                    if ($discount_shipping == 1) {
                        $message .= '<br/><b>Discount shipping</b>';
                        $shipping_total_price = $shipping_total_price - 14.95;
                    }
                }


                $shipping_total_price += $shipping_price + $shipping_surcharge_price;
            }
            if ($freeShipping == 1 or $freeshipping_status == 1 or $shipping_total_price < 0 or $shipping_total_price == 0)
                $shipping_total_price = '0.00';

            $day_function = ' onmouseover="changeDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $message . '\');" onclick="chooseDay(' . 0 .',\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $shipping_total_price . '\', \'' . $day_type . '\');" ';

            $shipping_div_price = '<div class="special-deliver">$' . $shipping_total_price . '</div>';
        }
        /**/

        if (in_array($list_day, $neededDays) && ($neededMonth == $month)) {
            //holiday dates
            $doubleDays = 'calendar-day-holiday';
            $specialPriceDiv = '<div class="special-deliver-holiday">$' . $blendedDayPrice . '</div>';
            $calendar .= '<td class="' . $doubleDays . '" ' . $day_function . '>';
            $calendar .= '<div class="day-number-holiday ' . $day_class_2 . '">' . $list_day . '</div>' . $specialPriceDiv;
        } else {
            $calendar .= '<td class="' . $day_class . '" ' . $day_function . '>';
            $calendar .= '<div class="day-number ' . $day_class_2 . '">' . $list_day . '</div>' . $shipping_div_price;
        }


        $calendar .= '</td>';

        if ($running_day == 6) {
            $calendar .= '</tr>';

            if (($day_counter + 1) != $days_in_month) {
                $calendar .= '<tr class="calendar-row">';
            }

            $running_day = -1;
            $days_in_this_week = 0;
        }

        $days_in_this_week++;
        $running_day++;
        $day_counter++;
    }

    if ($days_in_this_week < 8) {
        for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
            $calendar .= '<td class="calendar-day-np">&nbsp;</td>';
        }
    }

    $calendar .= '</tr></table>';

    $additionalDeliveryFeeText = '';
    if ($additionalDeliveryFee) {
        $additionalDeliveryFeeText = '<div  style="margin-top: 20px;font-size:15px;text-align:center;color:#FF6600 !important" role="alert">' . (($mosConfig_lang == 'french') ? 'Frais de livraison suppl?mentaires pour les r?gions ?loign?es: ' . number_format($additionalDeliveryFee, 2, '.', '') . '$' : 'Additional delivery fee for remote area: ' . number_format($additionalDeliveryFee, 2, '.', '') . '$') . '</div>';
    }
    return $additionalDeliveryFeeText . $calendar;
}

function getState($option)
{
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

function makePhoneOrder($option)
{
    global $database, $my, $mosConfig_absolute_path;
    $aInfomation = array();
    $query = " SELECT JVPO.deluxe,JVPO.supersize,JVPO.petite,JVPO.sub_3,JVPO.sub_6,JVPO.sub_12,JVPO.must_be_combined,VM.product_id, VM.product_sku, VM.product_name, VMP.product_price, VTR.tax_rate, VMP.saving_price
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP ON VM.product_id = VMP.product_id
								 LEFT JOIN  #__vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id
                         LEFT JOIN #__vm_product_options AS JVPO ON VM.product_id = JVPO.product_id ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    require_once $mosConfig_absolute_path . '/administrator/components/com_virtuemart/html/templates/product_details/BalloonProperty.php';
    $sAttrubute = BalloonProperty::instance()->get_admin();

    $sString = "";
    if (count($rows)) {
        foreach ($rows as $value) {

            $q = "SELECT 
            `l`.`igl_quantity` as `quantity_normal`, 
            `l`.`igl_quantity_deluxe` as `quantity_deluxe`, 
            `l`.`igl_quantity_supersize` as `quantity_supersize`, 
            `l`.`igl_quantity_petite` as `quantity_petite`, 
            `o`.`igo_product_name` as `name`
            FROM `product_ingredients_lists` as `l`
            LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
            WHERE `l`.`product_id`=" . $value->product_id . "";

            $database->setQuery($q);
            $result = $database->loadObjectList();

            $ingredient_list = '';
            $ingredient_list_normal = "<div class='ing_{noItem} ing_standard_{noItem}'>";
            $ingredient_list_deluxe = "<div style='display:none' class='ing_{noItem} ing_".$value->deluxe."_{noItem}'>";
            $ingredient_list_supersize = "<div style='display:none' class='ing_{noItem} ing_".$value->supersize."_{noItem}'>";
            $ingredient_list_petite = "<div style='display:none' class='ing_{noItem} ing_".$value->petite."_{noItem}'>";

           foreach ($result as $ing){
                $ingredient_list_normal .= $ing->quantity_normal . " x " . $ing->name . "<br/>";
                $ingredient_list_deluxe .= $ing->quantity_deluxe . " x " . $ing->name . "<br/>";
                $ingredient_list_supersize .= $ing->quantity_supersize . " x " . $ing->name . "<br/>";
                $ingredient_list_petite .= $ing->quantity_petite . " x " . $ing->name . "<br/>";
            }
            $ingredient_list_normal .= "</div>";
            $ingredient_list_deluxe .= "</div>";
            $ingredient_list_supersize .= "</div>";
            $ingredient_list_petite .= "</div>";
            $ingredient_list = $ingredient_list_normal.$ingredient_list_deluxe.$ingredient_list_supersize.$ingredient_list_petite;

            require_once(CLASSPATH . 'vmAbstractObject.class.php');
            require_once(CLASSPATH . 'ps_database.php');
            require_once(CLASSPATH . 'ps_product.php');
            $ps_product = new ps_product;
            $aPrice = $ps_product->get_retail_price($value->product_id);
            $my_taxrate = $ps_product->get_product_taxrate($value->product_id);
            $bloomex_reg_price = ($aPrice['product_price'] - $aPrice['saving_price'])-(($aPrice['promotion_discount'])?(($aPrice['product_price'] - $aPrice['saving_price'])*$aPrice['promotion_discount']/100):0);
            $value_product_price = number_format($bloomex_reg_price, 2, '.', '');
            $value_product_price = $value->sub_3 ? $value->sub_3 : floatval($value_product_price);
            $add_select = ($value->product_sku == 'RP02') ? $sAttrubute : '';
            $sString .= utf8_encode($value->product_id . "[--1--]" . addslashes($value->product_sku) . "[--1--]" . addslashes($value->product_name) . ' ' . $add_select . "[--1--]" . round(doubleval($value_product_price), 2) . "[--1--]" . round(floatval($value->tax_rate), 2) . "[--1--]" . $value->deluxe . "[--1--]" . $value->supersize . '' . $add_select . "[--1--]" . $value->sub_3 . "[--1--]" . $value->sub_6 . "[--1--]" . $value->sub_12 . "[--1--]" . $value->must_be_combined . "[--1--]" . $ingredient_list . "[--1--]" . $value->petite . "[--2--]");
        }
    }

    $aInfomation['Product'] = $sString;
    $query = "SELECT country_3_code, country_name FROM #__vm_country ORDER BY country_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $delivery_country = array();
    foreach ($rows as $r) {
        if ($r->country_3_code == 'AUS' || $r->country_3_code == 'NZL') {
            $delivery_country[] = $r;
            /*break;*/
        }
    }
    $aInfomation['bill_country'] = mosHTML::selectList($rows, "bill_country", "size='1' id='bill_country'", "country_3_code", "country_name", "AUS");
    $aInfomation['deliver_country'] = mosHTML::selectList($delivery_country, "deliver_country", "size='1' id='deliver_country'", "country_3_code", "country_name", "AUS");


    $query = " SELECT S.state_2_code, S.state_name
				FROM #__vm_state S INNER JOIN #__vm_country AS C
				ON C.country_id	= S.country_id
				WHERE C.country_3_code = 'AUS'
				ORDER BY S.state_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['bill_state'] = mosHTML::selectList($rows, "bill_state", "size='1'", "state_2_code", "state_name");
    $aInfomation['deliver_state'] = mosHTML::selectList($rows, "deliver_state", "size='1'", "state_2_code", "state_name");


    $query = "SELECT order_occasion_code, order_occasion_name FROM #__vm_order_occasion WHERE published='1' ORDER BY list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();


    $aInfomation['occasion'] = mosHTML::selectList($rows, "occasion", "size='1'", "order_occasion_code", "order_occasion_name", 'CHR');

    // *add sales line option
    $query = "SELECT id, sales_line FROM #__vm_phone_sales_lines";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['sales_line'] = "<select name='sales_line' id='sales_line'>";
    if (count($rows)) {
        foreach ($rows as $item) {
            if ($item->sales_line == 'Bloomex AU Sales') {
                $aInfomation['sales_line'] .= "<option value=" . $item->id . " selected='selected'>" . $item->sales_line . "</option>";
            } else {
                $aInfomation['sales_line'] .= "<option value=" . $item->id . ">" . $item->sales_line . "</option>";
            }
        }
    }

    $query = "SELECT shipping_rate_id, REPLACE( shipping_rate_name, 'Express Image', '') AS shipping_rate FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['shipping_method'] = mosHTML::selectList($rows, "shipping_method", "size='1'", "shipping_rate_id", "shipping_rate");


//	$query 	= "SELECT shipping_rate_id, shipping_rate_value, tax_rate FROM #__vm_shipping_rate INNER JOIN #__vm_tax_rate ON shipping_rate_vat_id = tax_rate_id ORDER BY shipping_rate_list_order ASC";
    $query = "SELECT shipping_rate_id, shipping_rate_value FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    /* echo $query;
      print_r($rows); */

    $aInfomation['shipping_method_list_fee'] = "";
    if (count($rows)) {
        foreach ($rows as $item) {
            $aInfomation['shipping_method_list_fee'] .= $item->shipping_rate_id . "[--1--]" . floatval($item->shipping_rate_value) . "[--1--]0[--2--]";
        }
    }


    $query = "SELECT creditcard_code,creditcard_name FROM #__vm_creditcard";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aInfomation['payment_method'] = mosHTML::selectList($rows, "payment_method", "size='1'", "creditcard_code", "creditcard_name");


    $aInfomation['delivery_day'] = lisDay("deliver_day", date('j', time()), "onchange='changeDeliver( );'");
    $aInfomation['delivery_month'] = listMonth("deliver_month", date('m', time()), "onchange='changeUnAvailableDate( this.value );'");
    $aInfomation['delivery_year'] = listYear("deliver_year", date("Y"), " size='1' onchange='changeDeliver( );'", 7, date("Y"));
    $aInfomation['expire_month'] = listMonth("expire_month", null, " size='1' ");
    $aInfomation['expire_year'] = listYear("expire_year", date("Y"), " size='1' ", 30, date("Y"));


    $query = "SELECT name,options FROM tbl_options WHERE type='special_deliver' ";
    $database->setQuery($query);
    $aSpecialDeliver = $database->loadObjectList();
    $aInfomation['special_deliver'] = "";
    if (count($aSpecialDeliver)) {
        foreach ($aSpecialDeliver as $item) {
            $aInfomation['special_deliver'] .= $item->name . "/" . $item->options . "[--1--]";
        }
    }


    $query = "SELECT name FROM tbl_options WHERE type='deliver_option' ORDER BY name";
    $database->setQuery($query);
    $aUnAvailableDate = $database->loadObjectList();

    $aInfomation['unavailable_date'] = "";
    if (count($aUnAvailableDate)) {
        foreach ($aUnAvailableDate as $item) {
            $aInfomation['unavailable_date'] .= $item->name . "[--1--]";
        }
    }


    $query = "SELECT options FROM tbl_options WHERE type='cut_off_time'";
    $database->setQuery($query);
    $sOptionParam = $database->loadResult();
    $aInfomation['option_param'] = explode("[--1--]", $sOptionParam);
    $aInfomation['time_limit'] = $aInfomation['option_param'][0] * 60 + $aInfomation['option_param'][1];

    if (intval($aInfomation['option_param'][0]) >= 12) {
        $aInfomation['time'] = (intval($aInfomation['option_param'][0]) - 12) . ":" . $aInfomation['option_param'][1] . " PM";
    } else {
        $aInfomation['time'] = intval($aInfomation['option_param'][0]) . ":" . $aInfomation['option_param'][1] . " AM";
    }

    ini_set('date.timezone', 'Australia/Sydney');
    $aInfomation['day_now'] = intval(date('j'));
    $aInfomation['month_now'] = intval(date('m', time()));
    $aInfomation['year_now'] = intval(date('Y', time()));
    $aInfomation['hour_now'] = intval(date('H', time()));
    $aInfomation['minute_now'] = intval(date('i', time()));
    $aInfomation['time_now'] = $aInfomation['hour_now'] * 60 + $aInfomation['minute_now'];


//	echo $aInfomation['time_now']."===".$aInfomation['hour_now']."===".$aInfomation['minute_now']."===".$aInfomation['time_limit'];
    if ($aInfomation['time_now'] >= $aInfomation['time_limit']) {
        $aInfomation['cut_off_time'] = 1;
    } else {
        $aInfomation['cut_off_time'] = 0;
    }
    $aInfomation['days_of_month_now'] = getMonthDays($aInfomation['month_now'], $aInfomation['year_now']);


    $aInfomation['DELIVERY_DATE'] = "<span>Attention! Same day orders cut off time %s local time.</span> Bloomex Time is: %s";

    $query = "SELECT * FROM jos_vm_tax_rate";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $sStateTax = "";
    if (count($rows)) {
        foreach ($rows as $item) {
            $sStateTax .= $item->tax_country . "[--1--]" . $item->tax_state . "[--1--]" . floatval($item->tax_rate) . "[--2--]";
        }
    }
    $aInfomation['state_tax'] = $sStateTax;


    $query = "SELECT * FROM tbl_freeshipping ORDER BY id";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    //print_r($rows);

    $aInfomation['FreeShipping'] = "";
    if (count($rows)) {
        foreach ($rows as $row) {
            $aInfomation['FreeShipping'] .= date("n/j/Y", intval($row->freedate)) . ",";
        }
    }
    $aInfomation['FreeShipping'] = !empty($aInfomation['FreeShipping']) ? substr($aInfomation['FreeShipping'], 0, strlen($aInfomation['FreeShipping']) - 1) : "";
    //echo "<br/><br/>".$aInfomation['FreeShipping'];
    $query = "SELECT `country_2_code`,`country_3_code` FROM `jos_vm_country`";
    $database->setQuery($query);
    $res = $database->loadObjectList();
    foreach ($res as $r) {
        $aInfomation['countries'][$r->country_2_code] = $r->country_3_code;
    }
    $orderCallType = $_GET['order_call_type'] ?? null;
    HTML_PhoneOrder::makePhoneOrder($option, $aInfomation, $orderCallType);
}

function getMonthDays($Month, $Year)
{
    if (is_callable("cal_days_in_month")) {
        return cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
    } else {
        return date("d", mktime(0, 0, 0, $Month + 1, 0, $Year));
    }
}

function lisDay($list_name, $selected_item = "", $extra = "")
{
    $sString = "";
    $list = array("DAY", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");

    $sString = "<select name='{$list_name}' {$extra}>";
    foreach ($list as $value) {
        $sString .= "<option value='{$value}'>{$value}</option>";
    }

    $sString .= "</select>";
    return $sString;
}

function listMonth($list_name, $selected_item = "", $extra = "")
{
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

function listYear($list_name, $selected_item = "", $extra = "", $max = 7, $from = 2009, $direct = "up")
{
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

function checkCorporateUser()
{
    global $database;
    $result = array();
    $sEmail = mosGetParam($_REQUEST, "email", "");
    $shgid_check_id = mosGetuserShoperGroupId($sEmail);
    $result['result'] = false;
    if ($shgid_check_id) {
        $result['result'] = true;
        $query = "SELECT shopper_group_discount FROM jos_vm_shopper_group WHERE shopper_group_id = '{$shgid_check_id}'";
        $database->setQuery($query);
        $oRow = $database->loadObjectList();
        if ($oRow) {
            $result['corporate_discount'] = $oRow[0]->shopper_group_discount;
        }
    }
    die(json_encode($result));
}

function checkEmailAddressExist(){
    $return = [];
    $return['result'] = false;
    global $database;
    $user = false;
    $sEmail = mosGetParam($_REQUEST, "email", "");
    $query = "SELECT id, username, block FROM #__users WHERE email = '{$sEmail}'";
    $database->setQuery($query);
    $database->loadObject($user);
    if($user){
        $return['result'] = true;
        if($user->block){
            $return['blocked'] = true;
        }else{
            $return['accountInfo'] = $user;
        }
    };
    die(json_encode($return));
}
function checkAccountInfo($option)
{
    global $database, $mainframe, $mosConfig_list_limit;
    $sEmail = mosGetParam($_REQUEST, "email", "");

    $query = "SELECT id, username, block FROM #__users WHERE email = '{$sEmail}'";
    $database->setQuery($query);
    $oRow = $database->loadObjectList();
    $oUser = $oRow[0];
    $user_id = intval($oUser->id);
    $block = intval($oUser->block);
    if ($block) {
        echo "blocked";
        exit(0);
    }
    if ($user_id) {
        $query = "	SELECT U.username, VUI.*
					FROM #__users AS U
					LEFT JOIN #__vm_user_info AS VUI
					ON U.id = VUI.user_id
					WHERE U.email='{$sEmail}'
					ORDER BY VUI.address_type";
        $database->setQuery($query);
        $oRow = $database->loadObjectList();

        $query = "SELECT `rate` FROM `jos_vm_users_rating` WHERE `user_id`=" . $user_id . "";
        $database->setQuery($query);
        $rate = $database->loadResult();

        if ($rate == 1) {
            $query = "UPDATE `jos_vm_user_ccards` SET `block`=1 WHERE `user_id`=" . $user_id . "";
            $database->setQuery($query);

            $database->query();
        }
        //print_r($oRow);
        //echo $query;

        $bExistBilling = false;
        $bExistShipping = false;
        $exist_st = false;
        $sAccountShippingInfo = "";
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
                    . $value->user_email . "[--1--]"
                    . $value->suite . "[--1--]"
                    . $value->street_number . "[--1--]"
                    . $value->street_name . "[--1--]"
                    . $value->district . "[--1--]";


                $bExistBilling = true;
            } else {
                if (!$exist_st) { // first billing information
                    $sAccountBillingInfo .= $value->phone_1 . "[--1--]" . $value->extra_field_1 . "[--1--]";
                    $exist_st = true;
                }
                $postalCodeQuery = "SELECT id FROM jos_postcode_warehouse WHERE deliverable='0' AND published=1 AND postal_code = '" . trim($value->zip) . "'";
                $database->setQuery($postalCodeQuery);
                $postal_code_check = $database->loadResult();

                if ($postal_code_check) {
                    $sUnDeliver = "[--1--]undeliver";
                } else {
                    $sUnDeliver = "[--1--]deliver";
                }
                $addr = "";
                if ($value->street_name) {

                    if ($value->suite) {
                        $addr = $value->suite . ", ";
                    }
                    $addr .= $value->street_number . ", " . $value->street_name;
                } else {
                    $addr = $value->address_1 . " " . $value->address_2;
                }
                $sAccountShippingInfo .= $value->user_info_id . "[--1--]"
                    . $value->user_id . "[--1--]"
                    . $value->zip . "[--1--]"
                    . "<b>" . $value->address_type_name . "</b> "
                    . ". Address: " . $addr . ", " . $value->city . ", " . $value->state . " " . $value->zip . ", " . $value->country
                    . ". Phone: " . $value->phone_1 . "(" . $value->phone_2 . ")"
                    . ". Fax: " . $value->fax . "[--1--]" . $value->country . "_" . $value->state . $sUnDeliver . "[--2--]";

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

        $query = " SELECT SG.shopper_group_discount
						FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id
						WHERE  SVX.user_id = {$user_id} LIMIT 1";
        $database->setQuery($query);
        $ShopperGroupDiscount = $database->loadResult();
        if ($ShopperGroupDiscount) {
            $ShopperGroupDiscount = $ShopperGroupDiscount / 100;
        }
        $query = "SELECT `credits` FROM `jos_vm_users_credits` WHERE `user_id`=" . $user_id . "";
        $database->setQuery($query);
        $credits = $database->loadResult();
        if ($credits)
            $credits = number_format($credits, 2, ".", "");

        $query = "SELECT `bucks` FROM `tbl_bucks` WHERE `user_id`=" . $user_id . "";
        $database->setQuery($query);
        $bucks = $database->loadResult();
        if ($bucks)
            $bucks = number_format($bucks, 2, ".", "");
        $sAccountInfo = $sAccountBillingInfo . "[--3--]" . $sAccountShippingInfo . "[--3--]" . floatval($ShopperGroupDiscount) . "[--3--]" . $bucks . "[--3--]" . $credits . "[--3--]" . $sMsg . "";
        echo $sAccountInfo;
    } else {
        echo "error";
    }
    exit(0);
}

function checkCounponCode($option)
{
    global $database;

    $coupon_discount_code = trim(mosGetParam($_REQUEST, 'coupon_discount_code'));
    $product_id_string = trim(mosGetParam($_REQUEST, 'product_id_string'));
    $product_qty_string = trim(mosGetParam($_REQUEST, 'product_qty_string'));
    $nShopperGroupDiscount = doubleval(mosGetParam($_REQUEST, "shopper_group_discount", ""));
    $nSubTotalWithOutTax = doubleval(mosGetParam($_REQUEST, "nSubTotalWithOutTax", ""));

    $aProductId = array();
    if ($product_id_string) {
        $aProductId = explode(",", $product_id_string);
    }

    $aQuantity = array();
    if ($product_qty_string) {
        $aQuantity = explode(",", $product_qty_string);
    }
    for ($h = 0; $h < count($aProductId); $h++) {
        $aQuantity[$h] = $aProductId[$h] . "[--1--]" . $aQuantity[$h];
    }

    $coupon_type = "";
    $coupon_price = 0;
    $coupon_value = 0;
    $percent_or_total = "";
    $not_apply_coupon_products = "";
    $product_aplly_coupon = "";

    //Check Coupon For Order Total
    $query = " SELECT coupon_id, percent_or_total, coupon_value,CASE 
                WHEN `expiry_date`>'" . date('Y-m-d') . "' THEN 0 
                WHEN `expiry_date`='0000-00-00' THEN 0 
            ELSE 1 END as `expired` FROM #__vm_coupons WHERE coupon_code = '{$coupon_discount_code}'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $coupon = $rows[0];

    //Check Coupon For Product
    $query = " SELECT product_id FROM #__vm_product WHERE product_coupon_discount = '" . $coupon_discount_code . "' AND product_id IN ($product_id_string)";
    $database->setQuery($query);
    $product_apply_coupon = $database->loadObjectList();

    $query = " SELECT * FROM #__vm_product_discount WHERE discount_type = 'coupon' AND coupon_code = '" . $coupon_discount_code . "' LIMIT 1";
    $database->setQuery($query);
    $product_coupon = $database->loadObjectList();

    $aProductApplyCoupon = array();
    if (count($product_apply_coupon) > 0 && count($product_coupon)) {
        foreach ($product_apply_coupon as $item) {
            $aProductApplyCoupon[] = $item->product_id;
        }
    }

    //List Product
    $query = " SELECT VM.product_id, VM.not_apply_discount, VM.product_name, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP
							ON VM.product_id = VMP.product_id
							LEFT JOIN  #__vm_tax_rate AS VTR
							ON VM.product_tax_id = VTR.tax_rate_id
							WHERE VM.product_id IN ({$product_id_string})";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if (count($rows)) {
        foreach ($rows as $value) {
            $nQuantityTemp = 0;
            for ($h = 0; $h < count($aQuantity); $h++) {
                $aQuantityTemp = explode("[--1--]", $aQuantity[$h]);

                if (intval($value->product_id) == intval($aQuantityTemp[0])) {
                    $nQuantityTemp = $aQuantityTemp[1];
                    break;
                }
            }

            if ($nShopperGroupDiscount > 0) {
                $product_item_price = $value->product_price - ($value->product_price * doubleval($nShopperGroupDiscount));
            } else {
                $product_item_price = $value->product_price;
            }

            if (!empty($value->not_apply_discount)) {
                $not_apply_coupon_products .= $value->product_name . ",";
            } elseif (in_array($value->product_id, $aProductApplyCoupon) &&
                (
                    ((!empty($product_coupon->start_date) && strtotime($product_coupon->start_date) < time()) && (!empty($product_coupon->end_date) && strtotime($product_coupon->end_date) > time())) || ($product_coupon->start_date == 0 || $product_coupon->end_date == 0)
                )
            ) {

                if ($product_coupon[0]->is_percent == 1) {
                    $nSubCouponFOP = ($product_item_price * ($product_coupon[0]->amount / 100));

                    if ($nSubCouponFOP > $product_item_price) {
                        $nSubCouponFOP = $product_item_price;
                    }
                } else {
                    if ($product_coupon[0]->amount > $product_item_price) {
                        $nSubCouponFOP = $product_item_price;
                    } else {
                        $nSubCouponFOP = $product_coupon[0]->amount;
                    }
                }

                $coupon_price += floatval($nSubCouponFOP * $nQuantityTemp);
            }
            $product_aplly_coupon .= $value->product_sku . ",";
        }
    }


    if (!empty($coupon->coupon_id)) {
        if ($coupon->percent_or_total == "percent") {
            $percent_or_total = "percent";
            $nCouponValue = ($nSubTotalWithOutTax) * ($coupon->coupon_value / 100);
        } elseif ($coupon->percent_or_total == "total") {
            $percent_or_total = "total";
            if (($nSubTotalWithOutTax) > $coupon->coupon_value) {
                $nCouponValue = $coupon->coupon_value;
            } else {
                $nCouponValue = ($nSubTotalWithOutTax);
            }
        }

        $coupon_type = "order_coupon";
        $coupon_type_msg = "order total price";
        $coupon_value = $coupon->coupon_value;
        $coupon_price = round(floatval($nCouponValue), 2);
    } elseif (count($product_coupon) > 0) {
        $coupon_type = "product_coupon";
        $coupon_price = round(floatval($coupon_price), 2);
        $coupon_value = !empty($product_coupon[0]->amount) ? floatval(($product_coupon[0]->amount)) : "";
        if ($product_coupon[0]->is_percent == 1) {
            $percent_or_total = "percent";
        } else {
            $percent_or_total = "total";
        }

    }

    if ($percent_or_total == "percent") {
        $coupon_msg = "$coupon_value%";
    } elseif ($percent_or_total == "total") {
        $coupon_msg = "$$coupon_value";
    }

    if ($not_apply_coupon_products) {
        $not_apply_coupon_products = rtrim($not_apply_coupon_products, ',');
        echo "error[--1--]Sorry, coupons cannot be used with  <font color='red'>{$not_apply_coupon_products}</font>";
    } elseif ($coupon_value > 0) {
        if ($coupon->expired == 1) {
            echo "error[--1--]<font color='red'>Coupon has been expired.</font>";
        } else {
            echo "success[--1--]<font color='blue'>This coupon code will reduce $coupon_msg of $coupon_type_msg. Please click to \"Calculate Order Price\" to get right price.</font>[--1--]{$coupon_price}[--1--]{$coupon_type}[--1--]{$coupon_value}[--1--]{$percent_or_total}[--1--]{$product_aplly_coupon}[--1--]{$coupon_discount_code}";
        }
    } else {
        echo "error[--1--]<font color='red'>This coupon code is not exist. Please try again!</font>";
    }

    exit(0);
}

function getProductPrice($product_id, $current_price)
{
    global $mosConfig_lifetime, $database;

    $discount_info = array();
    // We use the Session now to store the discount info for
    // each product. But this info can change regularly,
    // so we check if the session time has expired
    if (empty($_SESSION['product_sess'][$product_id]['discount_info']) || (time() - $_SESSION['product_sess'][$product_id]['discount_info']['create_time']) > $mosConfig_lifetime) {
        $starttime = time();
        $year = date('Y');
        $month = date('n');
        $day = date('j');
        // get the beginning time of today
        $endofday = mktime(0, 0, 0, $month, $day, $year) - 1440;

        // Get the DISCOUNT AMOUNT
        $q = "SELECT amount,is_percent FROM #__vm_product,#__vm_product_discount ";
        $q .= "WHERE product_id='$product_id' AND (start_date<='$starttime' OR start_date=0) AND (end_date>='$endofday' OR end_date=0) ";
        $q .= "AND product_discount_id=discount_id";
        $database->setQuery($q);
        $oRows = $database->loadObjectList();
        $oRow = (isset($oRows[0])) ? $oRows[0] : null;

        if (!empty($oRow->amount)) {
            $discount_info["amount"] = $oRow->amount;
            $discount_info["is_percent"] = $oRow->is_percent;
        } else {
            $discount_info["amount"] = 0;
            $discount_info["is_percent"] = 0;
        }
        $discount_info['create_time'] = time();
        $_SESSION['product_sess'][$product_id]['discount_info'] = $discount_info;
    } else {
        $discount_info = $_SESSION['product_sess'][$product_id]['discount_info'];
    }

    if (!empty($discount_info["amount"])) {
        switch ($discount_info["is_percent"]) {
            case 0:
                $current_price = (($current_price) - $discount_info["amount"]);
                break;
            case 1:
                $current_price = ($current_price - ($discount_info["amount"] / 100) * $current_price);
                break;
        }
    }

    return $current_price;
}

function savePhoneOrder($option)
{
    global $database, $my, $mosConfig_offset, $mosConfig_absolute_path,
           $mosConfig_live_site, $mosConfig_fromname, $deluxe, $supersize,$petite,
           $mosConfig_test_card_numbers, $mosConfig_payment_centralization,
           $mosConfig_nz_stripe_secret_key, $mosConfig_mailfrom,
           $mosConfig_au_stripe_secret_key,$mosConfig_mailwizz_url,
           $mosConfig_mailwizz_api_key,$mosConfig_mailwizz_list_id;

    date_default_timezone_set('Australia/Sydney');
    $timestamp = time();
    $PaymentVar = array();


    $bCheckCCPayment = intval(mosGetParam($_REQUEST, "check_CC_payment", 0));
    $user_id = intval(mosGetParam($_REQUEST, "user_id", 0));
    $user_name = mosGetParam($_REQUEST, "user_name", "");
    $account_email = mosGetParam($_REQUEST, "account_email", "");

    $bill_company_name = mosGetParam($_REQUEST, "bill_company_name", "");
    $bill_first_name = mosGetParam($_REQUEST, "bill_first_name", "");
    $bill_last_name = mosGetParam($_REQUEST, "bill_last_name", "");
    $bill_middle_name = mosGetParam($_REQUEST, "bill_middle_name", "");
    $bill_suite = mosGetParam($_REQUEST, "bill_suite", "");
    $bill_street_number = mosGetParam($_REQUEST, "bill_street_number", "");
    $bill_street_name = mosGetParam($_REQUEST, "bill_street_name", "");
    $bill_address_1 = $bill_suite . ' ' . $bill_street_number . ' ' . $bill_street_name;
    $bill_address_2 = '';
    $bill_city = mosGetParam($_REQUEST, "bill_city", "");
    $bill_district = mosGetParam($_REQUEST, "bill_district", "");
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
    $deliver_company_name = mosGetParam($_REQUEST, "deliver_company_name", "");
    $deliver_suite = mosGetParam($_REQUEST, "deliver_suite", "");
    $deliver_street_number = mosGetParam($_REQUEST, "deliver_street_number", "");
    $deliver_street_name = mosGetParam($_REQUEST, "deliver_street_name", "");

    $deliver_address_1 = $deliver_suite . ' ' . $deliver_street_number . ' ' . $deliver_street_name;
    $deliver_address_2 = ' ';
    $delivery_address_type2 = mosGetParam($_REQUEST, "delivery_address_type2", "");
    $deliver_city = mosGetParam($_REQUEST, "deliver_city", "");
    $deliver_district = mosGetParam($_REQUEST, "deliver_district", "");
    $deliver_zip_code = mosGetParam($_REQUEST, "deliver_zip_code", "");
    $deliver_country = mosGetParam($_REQUEST, "deliver_country", "");
    $deliver_state = mosGetParam($_REQUEST, "deliver_state", "");
    $deliver_phone = mosGetParam($_REQUEST, "deliver_phone", "");
    $deliver_evening_phone = mosGetParam($_REQUEST, "deliver_evening_phone", "");
    $deliver_cell_phone = mosGetParam($_REQUEST, "deliver_cell_phone", "");
    $deliver_fax = mosGetParam($_REQUEST, "deliver_fax", "");
    $deliver_recipient_email = mosGetParam($_REQUEST, "deliver_recipient_email", "");
    $occasion = mosGetParam($_REQUEST, "occasion", "");
    $order_create_type = mosGetParam($_REQUEST, "order_create_type", "");
    $orderCallType = mosGetParam($_REQUEST, 'order_call_type', null);
    $sales_line = mosGetParam($_REQUEST, "sales_line", "");
    $shipping_method = mosGetParam($_REQUEST, "shipping_method", "");
    $card_msg = mosGetParam($_REQUEST, "card_msg", "");
    $signature = mosGetParam($_REQUEST, "signature", "");
    $card_comment = mosGetParam($_REQUEST, "card_comment", "");
    $delivery_date = trim(mosGetParam($_REQUEST, "delivery_date", ""));
    $aDeliveryDate = explode("/", $delivery_date);
    $deliver_day = (strlen($aDeliveryDate[1]) == 1) ? '0' . $aDeliveryDate[1] : $aDeliveryDate[1];
    $deliver_month = (strlen($aDeliveryDate[0]) == 1) ? '0' . $aDeliveryDate[0] : $aDeliveryDate[0];
    $deliver_year = $aDeliveryDate[2];
    $delivery_date = date("d-m-Y", strtotime($deliver_day . "-" . $deliver_month . "-" . $deliver_year));


    $payment_method_state = mosGetParam($_REQUEST, "payment_method_state", "");
    $payment_method = mosGetParam($_REQUEST, "payment_method", "");
    $name_on_card = mosGetParam($_REQUEST, "name_on_card", "");
    $credit_card_number = mosGetParam($_REQUEST, "credit_card_number", "");
    $credit_card_security_code = mosGetParam($_REQUEST, "credit_card_security_code", "");
    $expire_month = intval(mosGetParam($_REQUEST, "expire_month", ""));
    $expire_year = intval(mosGetParam($_REQUEST, "expire_year", ""));
    $nStateTax = doubleval(mosGetParam($_REQUEST, "state_tax", ""));
    $nShopperGroupDiscount = doubleval(mosGetParam($_REQUEST, "shopper_group_discount", ""));

    $exist_address_deliver = intval(mosGetParam($_REQUEST, "exist_address_deliver", 0));
    $deliver_address_item = mosGetParam($_REQUEST, "deliver_address_item", "");
    $free_shipping = intval(mosGetParam($_REQUEST, "free_shipping", 0));
    $is_blended_day = $_REQUEST['is_blended_day'] ?? false;

    $sProductId = mosGetParam($_REQUEST, "product_id", "");
    if ($sProductId) {
        $aProductId = explode(",", $sProductId);
    }

    $sQuantity = mosGetParam($_REQUEST, "quantity", "");
    if ($sQuantity) {
        $aQuantity = explode(",", $sQuantity);
    }
    $sdeluxe_supersize = mosGetParam($_REQUEST, "deluxe_supersize", "");
    if ($sdeluxe_supersize) {
        $adeluxe_supersize = explode(",", $sdeluxe_supersize);
    }
    $select_sub = mosGetParam($_REQUEST, "select_sub", "");
    if ($select_sub) {
        $aselect_sub = explode(",", $select_sub);
    }
    $arr = array();
    for ($h = 0; $h < count($aProductId); $h++) {
        $aQuantity[$h] = $aProductId[$h] . "[--1--]" . $aQuantity[$h];
        if (stripos($adeluxe_supersize[$h], 'supersize') !== false) {
            $adeluxe_supersize[$h] = 'supersize';
        } elseif (stripos($adeluxe_supersize[$h], 'deluxe') !== false) {
            $adeluxe_supersize[$h] = 'deluxe';
        } elseif (stripos($adeluxe_supersize[$h], 'petite') !== false) {
            $adeluxe_supersize[$h] = 'petite';
        }
        $arr[$aProductId[$h]] = $adeluxe_supersize[$h];
        $aSub[$h] = $aselect_sub[$h];
    }

    $coupon_discount_code = trim(mosGetParam($_REQUEST, "coupon_discount_code", ""));
    $coupon_discount_price = doubleval(mosGetParam($_REQUEST, "coupon_discount_price", ""));
    $coupon_discount_type = trim(mosGetParam($_REQUEST, "coupon_discount_type", ""));
    $coupon_discount_value = doubleval(mosGetParam($_REQUEST, "coupon_discount_value", ""));
    $coupon_discount_percent_or_total = trim(mosGetParam($_REQUEST, "coupon_discount_percent_or_total", ""));
    $coupon_discount_product_aplly_coupon = trim(mosGetParam($_REQUEST, "coupon_discount_product_aplly_coupon", ""));

    $used_donate_id = intval(mosGetParam($_REQUEST, "donate", 0));
    $donated_price = 0;
    if ($used_donate_id) {
        $donated_price = get_current_donation_id_price($used_donate_id);
    }
    $used_bucks = round(doubleval(mosGetParam($_REQUEST, "used_bucks_price", "")), 2);
    $used_credits = round(doubleval(mosGetParam($_REQUEST, "used_credits_price", "")), 2);
    $total_price = doubleval(mosGetParam($_REQUEST, "total_price", ""));
    $deliver_fee = (!empty($select_sub)) ? round((mosGetParam($_REQUEST, "sub_delivery", "")), 2) : doubleval(mosGetParam($_REQUEST, "deliver_fee", ""));
    $deliver_fee_type = mosGetParam($_REQUEST, "deliver_fee_type", "Other");
    $sub_total_price = doubleval(mosGetParam($_REQUEST, "sub_total_price", ""));
    $total_tax = doubleval(mosGetParam($_REQUEST, "total_tax", ""));
    $total_tax = round($total_price - ($total_price / 1.1), 2);
    $total_deliver_tax_fee = doubleval(mosGetParam($_REQUEST, "total_deliver_tax_fee", ""));

    $sproduct_balloon = mosGetParam($_REQUEST, "product_balloon", "");
    if ($sproduct_balloon) {
        $aproduct_balloon = explode(",", $sproduct_balloon);
    }


    $aud = array('AUS', 'CAN', 'NZL', '');
    $currency = ($deliver_country == 'NZL')?'NZD':(in_array($bill_country, $aud) ? 'AUD' : 'USD');
    $stripeSecretKey = ($deliver_country == 'NZL')?$mosConfig_nz_stripe_secret_key:$mosConfig_au_stripe_secret_key;


      /* Insert the main order information */
    //$order_number = $user_id . "_" . date("YmdHis");
    $order_number = 'blau_' . date('YmdHi') . '_' . mt_rand(10000000, 99999999);

    $VM_LANG = new vmLanguage();

    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    $mask = substr($credit_card_number, 0, 6) . 'x' . substr($credit_card_number, -4);



    $gateway = (isset($bill_currency) and $bill_currency == 'USD') ? "BS US" : "BS CA";

    $query = "SELECT * FROM #__vm_vendor WHERE vendor_country = '{$bill_country}'";
    $database->setQuery($query);
    $aVendor = false;
    if ($database->loadObject($aVendor)) {
        $vendor_id = $aVendor->vendor_id;
        $vendor_currency = $aVendor->vendor_currency;
    } else {
        $vendor_id = 1;
        $vendor_currency = '';
    }


    //================================== PAYMENT =========================================
    if ($total_price == '0.00') {
        $order_status = ($_SESSION['virtual'] == true) ? "D" : "A";
        $payment_msg = "";
    } elseif ($payment_method_state == "offline") {
        $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
        $order_status = "P";
        $payment_msg = " and " . $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
    }elseif ($payment_method_state == "stripe") {
        $order_status = "26";
        $payment_msg = " and " . $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG_STRIPE;
    } else {
        $query = "SELECT `id`, `block` FROM `jos_vm_user_ccards` WHERE `user_id`=" . (int)$user_id . " AND `mask`='" . $database->getEscaped($mask) . "'";
        $oCcard = false;
        $database->setQuery($query);
        $database->loadObject($oCcard);

        if (!$oCcard) {
            $query = "INSERT INTO `jos_vm_user_ccards`
            (
                `user_id`,
                `mask`
            )
            VALUES (
                " . (int)$user_id . ",
                '" . $database->getEscaped($mask) . "'
            )";

            $database->setQuery($query);
            $database->query();
        }

        $query = "SELECT `id`, `block` FROM `jos_vm_user_ccards` WHERE `block`=1 AND `mask`='" . $database->getEscaped($mask) . "'";
        $oCcard_block = false;
        $database->setQuery($query);
        $database->loadObject($oCcard_block);

        if ($oCcard_block) {
            echo "error[--1--]This credit card has been blocked.";
            exit(0);
        }

        require_once($mosConfig_absolute_path . '/components/com_ajaxorder/nab/payment.php');
        global $mosConfig_test_card_numbers;
        if (in_array($credit_card_number, $mosConfig_test_card_numbers)) {
            $total_price = 0.05;
        }
        $aData = array();
        $aData[0] = $order_number;
        $aData[1] = date("YdmHiu") . "000" + 600;
        $aData[2] = number_format($total_price + $donated_price, 2, '', '');
        $aData[3] = $order_number;
        $aData[4] = $credit_card_number;
        $aData[5] = sprintf("%02d", $expire_month) . "/" . substr($expire_year, -2, 2);
        $aData[6] = $credit_card_security_code; //cvv

        $aResult = array();

        if ($mosConfig_payment_centralization == true) {
            $PaymentVarCentralization = array(
                'project' => 'bloomex.com.au',
                'order_number' => $order_number,
                'amount' => $total_price + $donated_price,
                'cardholder_name' => $name_on_card,
                'card_number' => $credit_card_number,
                'exp_month' => sprintf('%02d', $expire_month),
                'exp_year' => substr($expire_year, -2),
                'cvv' => $credit_card_security_code,
                'currency' => $vendor_currency,
                'first_name' => $bill_first_name,
                'last_name' => $bill_last_name,
                'billing_address_line_1' => (!empty($bill_suite) ? $bill_suite . '#, ' : '') . $bill_street_number . ' ' . $bill_street_name,
                'billing_address_line_2' => '',
                'billing_city' => $bill_city,
                'billing_state' => $bill_state,
                'billing_country' => $bill_country,
                'billing_zip' => $bill_zip_code,
                'billing_phone' => $bill_phone,
                'billing_email' => $account_email,
                'billing_ip' => $_SERVER['REMOTE_ADDR']
            );

            $aResult = process_payment_centralization($PaymentVarCentralization);

            $aResult['MessageInfo']['messageID'] = $aResult['order_payment_trans_id'];
            $aResult['Payment']['TxnList']['Txn']['responseText'] = $aResult['order_payment_log'];
            $aResult[0] = $aResult['order_payment_log'];

            if ($aResult['approved'] == 1) {
                $aResult['Status']['statusCode'] = '000';
            }
        } else {
            $aResult = processNABpayment($aData);
        }


        if (!empty($aResult["Status"]["statusCode"])) {
            if ($aResult["Status"]["statusCode"] == "000") {
                if (!empty($aResult['MessageInfo']["messageID"]) && $aResult['MessageInfo']["messageID"] == $aData[0]) {
                    if ((isset($aResult['Payment']["TxnList"]["Txn"]["responseCode"]) and ($aResult['Payment']["TxnList"]["Txn"]["responseCode"] == "00" || $aResult['Payment']["TxnList"]["Txn"]["responseCode"] == "08") && trim($aResult['Payment']["TxnList"]["Txn"]["responseText"]) == "Approved") or $aResult['approved'] == 1) {
                        $order_status = "A";
                        if (in_array($credit_card_number, $mosConfig_test_card_numbers)) {
                            $order_status = "X";
                        }
                        $payment_msg = " Payment has been approved";
                        $aResult["approved"] = 1;
                        $aResult["order_payment_trans_id"] = $aResult['MessageInfo']["messageID"];
                        $aResult["order_payment_log"] = $aResult['Payment']["TxnList"]["Txn"]["responseText"];
                    } else {
                        $Error = "Error #" . $aResult['Payment']["TxnList"]["Txn"]["responseCode"] . ": " . $aResult['Payment']["TxnList"]["Txn"]["responseText"];
                        echo "error[--1--]<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $Error . "</b>";
                        exit(0);
                    }
                } else {
                    $Error = "Error: The messageID is incorrect";
                    echo "error[--1--]<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $Error . "</b>";
                    exit(0);
                }
            } else {
                $Error = "Error #" . $aResult["Status"]["statusCode"] . ": " . $aResult["Status"]["statusDescription"];
                echo "error[--1--]<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $Error . "</b>";
                exit(0);
            }
        } else {
            echo "error[--1--]<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $aResult[0] . "</b>";
            exit(0);
        }

        /*print_r($aData);
          print_r($aResult);
          die();*/
    }


    //====================================================================================================================================
    $user_info_id = md5($user_id . time());

    $query = "SELECT user_id FROM #__vm_user_info WHERE user_email = '{$account_email}' AND address_type = 'BT'";
    $database->setQuery($query);

    if (intval($database->loadResult())) {
        $addr = $bill_suite . ' ' . $bill_street_number . ' ' . $bill_street_name;
        $query = " UPDATE #__vm_user_info
							SET address_type_name	= '-default-',
								company				= '{$bill_company_name}',
								last_name			= '{$bill_last_name}',
								first_name			= '{$bill_first_name}',
								middle_name			= '{$bill_middle_name}',
								phone_1				= '{$bill_phone}',
                                phone_2				= '{$bill_evening_phone}',
								fax				    = '{$bill_fax}',
								address_1			= '{$addr}',
								address_2			= ' ',
								bill_district		= '{$bill_district}',
								city				= '{$bill_city}',
								state				= '{$bill_state}',
								country				= '{$bill_country}',
								zip				    = '{$bill_zip_code}',
								suite				= '{$bill_suite}',
								street_number	    = '{$bill_street_number}',
								street_name			= '{$bill_street_name}'

					   	   WHERE user_email = '{$account_email}' AND address_type = 'BT'";
        $database->setQuery($query);
        $database->query();

        $query = "SELECT username FROM #__users WHERE email = '{$account_email}'";
        $database->setQuery($query);
        $user_name = $database->loadResult();
    } else {
        $query = "SELECT id FROM #__users WHERE email = '{$account_email}'";
        $database->setQuery($query);
        $user_id = intval($database->loadResult());
        if (!$user_id) {
            $query = "INSERT INTO #__users( name, username, email, usertype, block, gid,registerDate ) VALUES( '{$user_name}', '{$user_name}', '{$account_email}' , 'Registered' , 0, 18,'{$mysqlDatetime}' )";
            $database->setQuery($query);
            $database->query();
            $user_id = $database->insertid();

            $shgid_check = 5;
            $shgid_check_id = mosGetuserShoperGroupId($account_email);
            if ($shgid_check_id) {
                $shgid_check = $shgid_check_id;
            }
            $q = "INSERT INTO jos_vm_shopper_vendor_xref ";
            $q .= "(user_id,vendor_id,shopper_group_id) ";
            $q .= "VALUES ('$user_id','1','" . $shgid_check . "')";

            $database->setQuery($q);
            $database->query();

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
                                                                                                                        phone_2,
															fax,
															address_1,
															address_2,
															district,
															city,
															state,
															country, zip,
															user_email,
                                                                                                                        extra_field_1,
															perms, suite, street_number, street_name )
						   	   VALUES(  '" . $user_info_id . "',
						   	   			{$user_id},
						   	   			'BT',
						   	   			'-default-',
						   	   			'{$bill_company_name}',
						   	   			'{$bill_last_name}',
						   	   			'{$bill_first_name}',
						   	   			'{$bill_middle_name}',
						   	   			'{$bill_phone}',
                                                                                '{$bill_evening_phone}',
						   	   			'{$bill_fax}',
						   	   			'{$addr}',
						   	   			' ',
						   	   			'{$bill_district}',
						   	   			'{$bill_city}',
						   	   			'{$bill_state}',
						   	   			'{$bill_country}',
						   	   			'{$bill_zip_code}',
						   	   			'{$account_email}',
                                                                                '{$deliver_evening_phone}',
										'shopper',
                                                                                '{$bill_suite}', '{$bill_street_number}','{$bill_street_name}')";
        $database->setQuery($query);
        $database->query();
    }

    $addr2 = $deliver_suite . ' ' . $deliver_street_number . ' ' . $deliver_street_name;
    if ($exist_address_deliver && $address_user_name) {
        $query = "INSERT INTO #__vm_user_info( user_info_id,
														user_id,
														address_type,
														address_type2,
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
														district,
														city,
														state,
														country,
														zip,
														user_email, extra_field_1, suite, street_number, street_name )
					   	   VALUES(  '" . md5($user_info_id) . "',
					   	   			{$user_id},
					   	   			'ST',
					   	   			'{$delivery_address_type2}',
					   	   			'{$address_user_name}',
					   	   			'{$deliver_company_name}',
					   	   			'{$deliver_last_name}',
					   	   			'{$deliver_first_name}',
					   	   			'{$deliver_middle_name}',
					   	   			'{$deliver_phone}',
					   	   			'{$deliver_cell_phone}',
					   	   			'{$deliver_fax}',
					   	   			'{$addr2}',
					   	   			' ',
					   	   			'{$deliver_district}',
					   	   			'{$deliver_city}',
					   	   			'{$deliver_state}',
					   	   			'{$deliver_country}',
					   	   			'{$deliver_zip_code}',
					   	   			'{$deliver_recipient_email}', '{$deliver_evening_phone}',
                                                                        '{$deliver_suite}',  '{$deliver_street_number}', '{$deliver_street_name}' )";
        $database->setQuery($query);
        $database->query();
    } else {
        $aDeliverAddressItem = explode("[--1--]", $deliver_address_item);

        $query_update = "UPDATE #__vm_user_info SET  
                                                                    address_type_name='{$address_user_name}',
                                                                    address_type2='{$delivery_address_type2}',
                                                                    last_name='{$deliver_last_name}',
                                                                    first_name='{$deliver_first_name}',
                                                                    phone_1='{$deliver_phone}',
                                                                    phone_2='{$deliver_cell_phone}',
                                                                    address_1='{$addr2}',
                                                                    city='{$deliver_city}',
                                                                    district='{$deliver_district}',
                                                                    state='{$deliver_state}',
                                                                    country='{$deliver_country}',
                                                                    zip='{$deliver_zip_code}',
                                                                    user_email='{$deliver_recipient_email}',
                                                                    extra_field_1='{$deliver_evening_phone}',
                                                                    suite='{$deliver_suite}',
                                                                    street_number='{$deliver_street_number}',
                                                                    street_name='{$deliver_street_name}'  WHERE user_info_id ='{$aDeliverAddressItem[0]}'";

        $database->setQuery($query_update);
        $database->query();
    }


    $PaymentVar["vendor_id"] = $vendor_id;
    $PaymentVar["vendor_currency"] = $vendor_currency;

    $query = " SELECT VSC.shipping_carrier_name,
                          REPLACE(VSR.shipping_rate_name,'{fee}','".$deliver_fee."') as shipping_rate_name, 
                        '".$deliver_fee."', 
   VSR.shipping_rate_id
							FROM #__vm_shipping_rate AS VSR
							INNER JOIN #__vm_shipping_carrier AS VSC
							ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id
							WHERE VSR.shipping_rate_id = {$shipping_method}";
    $database->setQuery($query);
    $aShippingMethod = $database->loadRow();

    if ($free_shipping >= 1) {
        $sFreeShipping = "Free";
    } else {
        $sFreeShipping = "Paid";
    }

    if (is_array($aShippingMethod) && count($aShippingMethod)) {
        $sShippingMethod = "standard_shipping|" . implode("|", $aShippingMethod) . "|$sFreeShipping";
    } else {
        $sShippingMethod = "standard_shipping|$sFreeShipping";
    }

    $query = " SELECT (VMP.product_price-VMP.saving_price) as product_price, VTR.tax_rate
							FROM #__vm_product AS VM
							LEFT JOIN #__vm_product_price AS VMP
							ON VM.product_id = VMP.product_id
							LEFT JOIN  #__vm_tax_rate AS VTR
							ON VM.product_tax_id = VTR.tax_rate_id
							WHERE VM.product_id IN ({$sProductId})";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $order_tax_details = array();
    if ($nStateTax) {

        foreach ($rows as $value) {
            if (!isset($order_tax_details["$nStateTax"])) {
                $order_tax_details["$nStateTax"] = doubleval($nStateTax) * doubleval($value->product_price);
            } else {
                $order_tax_details["$nStateTax"] += doubleval($nStateTax) * doubleval($value->product_price);
            }
        }
    } else {
        foreach ($rows as $value) {
            if (!isset($order_tax_details["$value->tax_rate"])) {
                $order_tax_details["$value->tax_rate"] = doubleval($value->tax_rate) * doubleval($value->product_price);
            } else {
                $order_tax_details["$value->tax_rate"] += doubleval($value->tax_rate) * doubleval($value->product_price);
            }
        }
    }


    //====================================================================================
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip_address = "unknown";
    }

    $query = "INSERT INTO #__vm_orders( user_id,
												 vendor_id,
												 order_number,
												 user_info_id,
												 order_total,
												 order_subtotal,
												 order_tax,
												 order_tax_details,
												 order_shipping,
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
												 ip_address,
												 coupon_code,
												 coupon_type,
												 coupon_value,
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
				   	   		   	{$coupon_discount_price},
				   	   		   	'{$vendor_currency}',
				   	   		   	'$order_status',
				   	   		   	" . $timestamp . ",
				   	   		   	" . $timestamp . ",
				   	   		   	'" . ($deliver_day . "-" . $deliver_month . "-" . $deliver_year) . "',
				   	   		   	'" . $sShippingMethod . "',
						   	   	'" . $database->getEscaped(htmlspecialchars(strip_tags($card_msg))) . "',
						   	   	'" . $database->getEscaped(htmlspecialchars(strip_tags($signature))) . "',
						   	   	'" . $database->getEscaped(htmlspecialchars(strip_tags($occasion))) . "',
						   	   	'" . $database->getEscaped(htmlspecialchars(strip_tags($card_comment))) . "',
						   	   	'" . $database->getEscaped(htmlspecialchars(strip_tags($ip_address))) . "',
								'$coupon_discount_code',
						   	   	'$coupon_discount_percent_or_total',
						   	   	'$coupon_discount_value',
						   	   	'" . $database->getEscaped(htmlspecialchars(strip_tags($user_name))) . "' )";
    $database->setQuery($query);
    $database->query();
    $order_id = $database->insertid();
    if ($database->_errorNum > 0) {
        $text_error = 'Error: ' . $database->_errorMsg;
        $text_error .= '<br><br>Query: ' . $query;
        moslogerrors('Phoneorder insert order', $text_error);
    }

    if($_SESSION['free_shipping_by_price'] && $deliver_fee == '0.00'){
        $query = "INSERT INTO `tbl_free_shipping_by_price_orders`
                        (	
                            `order_id`,
                            `user_id`,
                            `date_added`
                        ) 
                        VALUES (
                            " . $order_id . ",
                            " . $user_id . ",
                           '" . $mysqlDatetime . "'
                        )";
        $database->setQuery($query);
        $database->query();
    }


    $query = "INSERT INTO #__vm_order_delivery_type(	order_id,
												delivery_type,
												date_added)
				VALUES ('$order_id',
						'" . $deliver_fee_type . "',
						'" . $timestamp . "')";
    $database->setQuery($query);
    $database->query();

    $new_bucks = $sub_total_price * 0.025;
    updatebucks($user_id, $order_id, $new_bucks, $used_bucks);
    updatecredits($user_id, $order_id, $used_credits, $account_email);

    $query = "INSERT INTO tbl_order_operator (	order_id,
            operator_code,cdate,sales_line, order_call_type,order_create_type)
        VALUES ('$order_id',
        '$my->id',NOW(),'$sales_line', ". (empty($orderCallType) ?  "null" : "'" . $orderCallType . "'") . ",'".$order_create_type."')";
    $database->setQuery($query);
    $database->query();

    if ($coupon_discount_price > 0 && strpos($coupon_discount_code, "PC-") !== false) {
        $phpShopCouponDiscount = "-" . LangNumberFormat::number_format($coupon_discount_price, 2, '.', ' ');
    } elseif ($coupon_discount_price > 0) {
        $phpShopCouponDiscount = "-" . LangNumberFormat::number_format($coupon_discount_price, 2, '.', ' ');
    }

    //===================== DELETE GIF COUNPON AFTER USED =================================
    if ($coupon_discount_code) {
        $sql = "DELETE FROM #__vm_coupons WHERE coupon_code = '$coupon_discount_code' AND coupon_type = 'gift'";
        $database->setQuery($sql);
        $database->query();
    }

    /* Insert the initial Order History. */
    date_default_timezone_set('Australia/Sydney');
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

    $history_comment = 'From phone-order.';

    if (!empty($aResult["order_payment_trans_id"])) {
        $history_comment .= ' Transaction Id: ' . $aResult["order_payment_trans_id"] . '. Amount: ' . $aData[2];
    }
    $accept_info = $is_blended_day ? " | Last Minute Order - Holiday delivery delay accepted" : "";

    $query = "INSERT INTO #__vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments, user_name)
				VALUES ('$order_id',
						'" . $order_status . "',
						'" . $mysqlDatetime . "',
						1,
						'" . $database->getEscaped($history_comment. $accept_info) . "', '" . $my->username . "')";
    $database->setQuery($query);
    $database->query();
//	echo "<br/>5. <br/>".$database->getErrorMsg()."<br/>";


    /* Insert the Order payment info */
    $payment_number = preg_replace("/ |-/", "", $credit_card_number);


    if (!isset($aResult["order_payment_trans_id"]))
        $aResult["order_payment_trans_id"] = "";
    // Payment number is encrypted using mySQL ENCODE function.

    if (!empty($aResult["approved"]) && $aResult["approved"] == 1) {
        $query = "INSERT INTO #__vm_order_payment(	order_id,
													order_payment_code,
													payment_method_id,
													order_payment_number,
													order_payment_expire,
													order_payment_log,
													order_payment_name,
													order_payment_trans_id)
					VALUES ({$order_id},
							'',
							3,
							'NOT SAVED',
							'',
							'{$aResult["order_payment_log"]}[--1--]$payment_method',
							'',
							'{$aResult["order_payment_trans_id"]}')";
        $database->setQuery($query);
        $database->query();
    } elseif ($payment_method_state == "offline") {
        $query = "INSERT INTO #__vm_order_payment(	order_id,
													order_payment_code,
													payment_method_id,
													order_payment_number,
													order_payment_expire,
													order_payment_log,
													order_payment_name,
													order_payment_trans_id)
					VALUES ({$order_id},
							'',
							3,
							'{$payment_number}',
							'" . strtotime("{$expire_month}/01/{$expire_year}") . "',
							'{$aResult["order_payment_log"]}',
							'{$name_on_card}',
							'{$aResult["order_payment_trans_id"]}')";
        $database->setQuery($query);
        $database->query();
    }



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
                                                                                                        phone_2,
													fax,
													address_1,
													address_2,
													district,
													city,
													state,
													country,
													zip,
													user_email, suite,street_number, street_name )
				   	   VALUES(  '" . $order_id . "',
				   	   			{$user_id},
				   	   			'BT',
				   	   			'-default-',
				   	   			'" . htmlentities($bill_company_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_last_name), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_first_name), ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_middle_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_phone, ENT_QUOTES) . "',
                                                                '" . htmlentities($bill_evening_phone, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_fax, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_address_1, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_address_2, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_district), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_city), ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_state, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_country, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_zip_code), ENT_QUOTES) . "',
				   	   			'" . htmlentities($account_email, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_suite), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_street_number), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($bill_street_name), ENT_QUOTES) . "')";
    $database->setQuery($query);
    $database->query();

    try {
        $config = new \EmsApi\Config([
            'apiUrl'    => $mosConfig_mailwizz_url,
            'apiKey'    => $mosConfig_mailwizz_api_key,
        ]);

        \EmsApi\Base::setConfig($config);

        $endpoint = new EmsApi\Endpoint\ListSubscribers();
        $response = $endpoint->emailSearch($mosConfig_mailwizz_list_id, $account_email);
        if($response->body['status'] != 'success') {
            $response = $endpoint->create($mosConfig_mailwizz_list_id, [
                'EMAIL'    => $account_email,
                'FNAME'    => $bill_first_name,
                'LNAME'    => $bill_last_name,
                'PHONE'    => $bill_phone
            ]);
        }
    } catch (Exception $e) {}

    $query = "INSERT INTO #__vm_order_user_info (  order_id,
													user_id,
													address_type,
													address_type2,
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
													district,
													city,
													state,
													country,
													zip,
													user_email, extra_field_1, suite,street_number, street_name )
				   	   VALUES(  '" . $order_id . "',
				   	   			{$user_id},
				   	   			'ST',
			   	   				'" . htmlentities($delivery_address_type2, ENT_QUOTES, 'UTF-8') . "',
			   	   				'" . htmlentities($address_user_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_company_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_last_name), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_first_name), ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_middle_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_phone), ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_cell_phone, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_fax, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_address_1, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_address_2, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_district), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_city), ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_state, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_country, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_zip_code), ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_recipient_email, ENT_QUOTES) . "',
                                                                '" . htmlentities($deliver_evening_phone, ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_suite), ENT_QUOTES) . "',
				   	   			'" . htmlentities(trim($deliver_street_number), ENT_QUOTES) . "',
                                                                '" . htmlentities(trim($deliver_street_name), ENT_QUOTES) . "'
				   	   			 )";

    $database->setQuery($query);
    $database->query();

    if($deliver_country == 'NZL') {

        $query = "INSERT INTO `jos_vm_api2_orders` 
                            (
                                `partner`,
                                `api_order_id`,
                                `request`,
                                `order_id`,
                                `datetime_add`,
                                `order_type`
                            )
                            VALUES (
                                'bloomex.co.nz',
                                '0',
                                '',
                                '" . $order_id . "',
                                '" . $mysqlDatetime . "',
                                'phoneorder'
                            )
                            ";
        $database->setQuery($query);
        $database->query();

    }

    /* Insert all Products from the Cart into order line items */
    $query = " SELECT VM.product_id, VM.product_name,VM.product_thumb_image, VM.product_sku, VM.product_desc,VMP.saving_price, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate, OP.sub_3, OP.sub_6, OP.sub_12
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP
							ON VM.product_id = VMP.product_id
							LEFT JOIN  #__vm_tax_rate AS VTR
							ON VM.product_tax_id = VTR.tax_rate_id
                                                        LEFT JOIN #__vm_product_options AS OP
							ON OP.product_id = VM.product_id
							WHERE VM.product_id IN ({$sProductId})";
    $database->setQuery($query);
    $rows = $database->loadObjectList();


    require_once CLASSPATH . 'ps_for_checkout.php';
    $ps_for_checkout = new ps_for_checkout;

    $phpShopOrderSubtotal = 0;
    $phpShopOrderTax = 0;
    $aProductSkuApplyCoupon = !empty($coupon_discount_product_aplly_coupon) ? explode($coupon_discount_product_aplly_coupon, ",") : array();

    $sub_months_max = 0;
    $orderItems = [];

    if (count($rows)) {
        $j = 0;
        $sub_orders = array();
        $checkExistPromotionDiscountProduct = false;
        foreach ($rows as $value) {
            $nQuantityTemp = 0;
            for ($h = 0; $h < count($aQuantity); $h++) {
                $aQuantityTemp = explode("[--1--]", $aQuantity[$h]);

                if (intval($value->product_id) == intval($aQuantityTemp[0])) {
                    $nQuantityTemp = $aQuantityTemp[1];
                    break;
                }
            }


            require_once(CLASSPATH . 'vmAbstractObject.class.php');
            require_once(CLASSPATH . 'ps_database.php');
            require_once(CLASSPATH . 'ps_product.php');
            $ps_product = new ps_product;
            $aPrice = $ps_product->get_retail_price($value->product_id);
            $my_taxrate = $ps_product->get_product_taxrate($value->product_id);
            $bloomex_reg_price = ($aPrice['product_price'] - $aPrice['saving_price'])-(($aPrice['promotion_discount'])?(($aPrice['product_price'] - $aPrice['saving_price'])*$aPrice['promotion_discount']/100):0);
            $product_item_price = number_format($bloomex_reg_price, 2, '.', '');
            $product_item_price = floatval($product_item_price);


            if($aPrice['promotion_discount']){
                $checkExistPromotionDiscountProduct = true;
            }


            $nor_del_super = '';

            $sql = "SELECT deluxe,supersize,petite FROM #__vm_product_options WHERE product_id = $value->product_id LIMIT 1";
            $database->setQuery($sql);
            $res = $database->loadObjectList();

            if ($res) {
                $deluxe = $res[0]->deluxe;
                $supersize = $res[0]->supersize;
                $petite = $res[0]->petite;
            }

            if ($arr[$value->product_id] && $arr[$value->product_id] == 'deluxe') {
                $value_product_price = $product_item_price + $deluxe;
                $nor_del_super = ' (deluxe) ';

                $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity_deluxe,') ')) as ingredient_list
                                FROM product_ingredients_lists as l
                                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                                WHERE l.product_id = " . $value->product_id . " group by l.product_id";
            } elseif ($arr[$value->product_id] && $arr[$value->product_id] == 'supersize') {
                $value_product_price = $product_item_price + $supersize;
                $nor_del_super = ' (supersize) ';
                $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity_supersize,') ')) as ingredient_list
                                FROM product_ingredients_lists as l
                                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                                WHERE l.product_id = " . $value->product_id . " group by l.product_id";
            } elseif ($arr[$value->product_id] && $arr[$value->product_id] == 'petite') {
                $value_product_price = $product_item_price + $petite;
                $nor_del_super = ' (petite) ';
                $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity_petite,') ')) as ingredient_list
                                FROM product_ingredients_lists as l
                                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                                WHERE l.product_id = " . $value->product_id . " group by l.product_id";
            } else {
                $value_product_price = $product_item_price;
                $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity,') ')) as ingredient_list
                                FROM product_ingredients_lists as l
                                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                                WHERE l.product_id = " . $value->product_id . " group by l.product_id";
            }
            $database->setQuery($q_ing);
            $ing = $database->loadRow();
            if ($ing) {
                $value->ingredient_list = $ing[0];
            }
            $value->product_name = $value->product_name . $nor_del_super;
            $product_item_price = $value_product_price;
            $product_final_price = $product_item_price;

            /*===============#6474 YI Subcsription Option==============*/
            $nSub = $aSub[$h];

            $Sub_array = array('sub_3', 'sub_6', 'sub_12');

            if (in_array($nSub, $Sub_array)) {
                $select_sub = '';

                if ($nSub == 'sub_3') {
                    $select_sub = 'Subscription 3 months';

                    $sub_months = 3;

                    if ($sub_months_max < $sub_months) {
                        $sub_months_max = $sub_months;
                    }

                    $product_item_price = $value->sub_3;
                } elseif ($nSub == 'sub_6') {
                    $select_sub = 'Subscription 6 months';

                    $sub_months = 6;

                    if ($sub_months_max < $sub_months) {
                        $sub_months_max = $sub_months;
                    }

                    $product_item_price = $value->sub_6;
                } elseif ($nSub == 'sub_12') {
                    $select_sub = 'Subscription 12 months';

                    $sub_months = 12;

                    if ($sub_months_max < $sub_months) {
                        $sub_months_max = $sub_months;
                    }

                    $product_item_price = $value->sub_12;
                }

                $product_final_price = $product_item_price;

                $value->product_name = $value->product_name . ' (' . $select_sub . ')';

                if ($sub_months) {
                    if (sizeof($sub_orders) == 0) {
                        $sub_orders[0] = $order_id;
                        SetSubOrderXref($order_id, $order_id);
                    }

                    for ($i_sub = 1; $i_sub < $sub_months; $i_sub++) {
                        if (array_key_exists($i_sub, $sub_orders)) {
                            $sub_order_id = $sub_orders[$i_sub];

                            $sub_order_item_data = array();
                            $sub_order_item_data['user_info_id'] = $user_info_id;
                            $sub_order_item_data['vendor_id'] = $vendor_id;
                            $sub_order_item_data['product_id'] = $value->product_id;
                            $sub_order_item_data['product_sku'] = $value->product_sku;
                            $sub_order_item_data['product_name'] = $value->product_name;
                            $sub_order_item_data['nQuantityTemp'] = intval($nQuantityTemp);
                            $sub_order_item_data['product_currency'] = $value->product_currency;
                            $sub_order_item_data['order_status'] = $order_status;
                            $sub_order_item_data['product_desc'] = $value->product_desc;
                            $sub_order_item_data['timestamp'] = $timestamp;
                            $sub_order_item_data['ingredient_list'] = $value->ingredient_list;
                            SetSubOrderItem($sub_order_id, $sub_order_item_data);
                        } else {
                            $sub_order_data = array();
                            $sub_order_data['user_id'] = $user_id;
                            $sub_order_data['vendor_id'] = $vendor_id;
                            $sub_order_data['user_info_id'] = $user_info_id;
                            $sub_order_data['vendor_currency'] = $bill_currency;
                            $sub_order_data['order_status'] = $order_status;
                            $sub_order_data['timestamp'] = $timestamp;
                            $sub_order_data['sShippingMethod'] = $sShippingMethod;
                            $sub_order_data['card_msg'] = $card_msg;
                            $sub_order_data['signature'] = $signature;
                            $sub_order_data['card_comment'] = $card_comment;
                            $sub_order_data['ip_address'] = $ip_address;
                            $sub_order_data['user_name'] = $user_name;
                            $sub_order_data['ddate_time'] = strtotime($delivery_date);

                            $sub_order_data['bill_company_name'] = $bill_company_name;
                            $sub_order_data['bill_last_name'] = $bill_last_name;
                            $sub_order_data['bill_first_name'] = $bill_first_name;
                            $sub_order_data['bill_phone'] = $bill_phone;
                            $sub_order_data['bill_phone_2'] = $bill_phone_2;
                            $sub_order_data['bill_address_1'] = $bill_address_1;
                            $sub_order_data['bill_address_2'] = $bill_address_2;
                            $sub_order_data['bill_city'] = $bill_city;
                            $sub_order_data['bill_district'] = $bill_district;
                            $sub_order_data['bill_state'] = $bill_state;
                            $sub_order_data['bill_country'] = $bill_country;
                            $sub_order_data['bill_zip_code'] = $bill_zip_code;
                            $sub_order_data['account_email'] = $account_email;
                            $sub_order_data['bill_suite'] = $bill_suite;
                            $sub_order_data['bill_street_number'] = $bill_street_number;
                            $sub_order_data['bill_street_name'] = $bill_street_name;

                            $sub_order_data['address_type2'] = $delivery_address_type2;
                            $sub_order_data['address_user_name'] = $address_user_name;
                            $sub_order_data['deliver_company_name'] = $deliver_company_name;
                            $sub_order_data['deliver_last_name'] = $deliver_last_name;
                            $sub_order_data['deliver_first_name'] = $deliver_first_name;
                            $sub_order_data['deliver_phone'] = $deliver_phone;
                            $sub_order_data['deliver_cell_phone'] = $deliver_cell_phone;
                            $sub_order_data['deliver_address_1'] = $deliver_address_1;
                            $sub_order_data['deliver_address_2'] = $deliver_address_2;
                            $sub_order_data['deliver_city'] = $deliver_city;
                            $sub_order_data['deliver_district'] = $deliver_district;
                            $sub_order_data['deliver_state'] = $deliver_state;
                            $sub_order_data['deliver_country'] = $deliver_country;
                            $sub_order_data['deliver_zip_code'] = $deliver_zip_code;
                            $sub_order_data['deliver_recipient_email'] = $deliver_recipient_email;
                            $sub_order_data['deliver_suite'] = $deliver_suite;
                            $sub_order_data['deliver_street_number'] = $deliver_street_number;
                            $sub_order_data['deliver_street_name'] = $deliver_street_name;

                            $sub_order_id = SetSubOrder($i_sub, $sub_order_data);
                            $sub_orders[$i_sub] = $sub_order_id;

                            $sub_order_item_data = array();
                            $sub_order_item_data['user_info_id'] = $user_info_id;
                            $sub_order_item_data['vendor_id'] = $vendor_id;
                            $sub_order_item_data['product_id'] = $value->product_id;
                            $sub_order_item_data['product_sku'] = $value->product_sku;
                            $sub_order_item_data['product_name'] = $value->product_name;
                            $sub_order_item_data['nQuantityTemp'] = intval($nQuantityTemp);
                            $sub_order_item_data['product_currency'] = $value->product_currency;
                            $sub_order_item_data['order_status'] = $order_status;
                            $sub_order_item_data['product_desc'] = $value->product_desc;
                            $sub_order_item_data['timestamp'] = $timestamp;
                            $sub_order_item_data['ingredient_list'] = $value->ingredient_list;
                            SetSubOrderItem($sub_order_id, $sub_order_item_data);

                            SetSubOrderXref($order_id, $sub_order_id);
                        }
                    }
                }
            }
            /*===============/Subcsription Option==============*/

            if ($coupon_discount_type == "product_coupon") {
                if (in_array($value->product_sku, $aProductSkuApplyCoupon)) {
                    $sProductCouponItem = $coupon_discount_code . "|" . $coupon_discount_percent_or_total . "|" . $coupon_discount_value;
                    if ($coupon_discount_percent_or_total == "percent") {
                        $product_coupon_value = (floatval($product_final_price) * ($coupon_discount_value / 100));
                    } else {
                        $product_coupon_value = floatval($coupon_discount_value);
                    }

                    if ($product_coupon_value >= floatval($product_final_price)) {
                        $product_final_price = 0;
                    } else {
                        $product_final_price = floatval($product_final_price) - $product_coupon_value;
                    }
                }
            }
            if ($value->product_sku == 'RP02') {
                $value->product_name .= ' ( ' . $aproduct_balloon[$j] . ' ) ';
            }
            if ($nStateTax) {
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
									'" . htmlentities($value->product_sku, ENT_QUOTES) . "',
					   	   			'" . htmlentities($value->product_name, ENT_QUOTES) . "',
					   	   			" . intval($nQuantityTemp) . ",
					   	   			" . $product_item_price . ",
					   	   			" . $product_final_price . ",
					   	   			'" . $value->product_currency . "',
					   	   			'P',
					   	   			'" . htmlentities(strip_tags($value->product_desc), ENT_QUOTES) . "',
					   	   			'{$timestamp}',
					   	   			'{$timestamp}'
					   	   			 )";

                $database->setQuery($query);
                $database->query();

            } else {
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
					   	   			'" . htmlentities($value->product_sku, ENT_QUOTES) . "',
					   	   			'" . htmlentities($value->product_name, ENT_QUOTES) . "',
					   	   			" . intval($nQuantityTemp) . ",
					   	   			" . $product_item_price . ",
					   	   			" . $product_final_price . ",
					   	   			'" . $value->product_currency . "',
					   	   			'P',
					   	   			'" . htmlentities(strip_tags($value->product_desc), ENT_QUOTES) . "',
					   	   			'{$timestamp}',
					   	   			'{$timestamp}'
					   	   			 )";
                $database->setQuery($query);
                $database->query();

            }

            $orderItems[] =  [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $product_final_price*100,
                        'product_data' => [
                            'name' => htmlentities($value->product_name, ENT_QUOTES),
                            'images' => [
                                $mosConfig_live_site.'/components/com_virtuemart/shop_image/product/'.$value->product_thumb_image
                            ]
                        ],
                    ],
                    'quantity' => intval($nQuantityTemp)
            ];

            $order_item_id = $database->insertid();


            //ORDER ITEM INGREDIENTS
            $ps_for_checkout->setOrderItemIngredients($order_id, $order_item_id, $value->product_id, intval($nQuantityTemp), $arr[$value->product_id]);
            //


            $phpShopOrderSubtotal += $product_item_price * intval($nQuantityTemp);

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

    $query = "SELECT `SG`.`shopper_group_discount`
    FROM `#__vm_shopper_vendor_xref` AS `SVX` 
        INNER JOIN `#__vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id`
    WHERE `SVX`.`user_id`=" . $user_id . " LIMIT 1";

    $database->setQuery($query);
    $ShopperGroupDiscount = $database->loadResult();

    $sDiscount = 0;
    $nDiscount = 0;
    $nDiscount = number_format($phpShopOrderSubtotal * ($ShopperGroupDiscount / 100), 2);

    if ($nDiscount > 0) {
        $query = "UPDATE `#__vm_orders` SET `order_discount`='" . $nDiscount . "' WHERE `order_id`=" . $order_id . "";

        $database->setQuery($query);
        $database->query();

        $sDiscount = '-' . LangNumberFormat::number_format($nDiscount, 2, '.', ' ');
    }



    $query = "SELECT creditcard_name FROM #__vm_creditcard WHERE creditcard_code = '$payment_method'";
    $database->setQuery($query);
    $payment_info_details = $database->loadResult();
    $payment_info_details .= '<br />Name On Card: ' . $name_on_card . '<br />'
        . 'Credit Card Number: ' . $credit_card_number . '<br />'
        . 'Expiration Date: ' . $expire_month . ' / ' . $expire_year . '<br />';

    $shopper_header = 'Thank you for shopping with us.  Your order information follows.';
    $shopper_order_link = $mosConfig_live_site . "/order-details/?order_id=$order_id";
    $shopper_footer_html = "<br /><br />Thank you for your patronage.<br />"
        . "<br /><a title=\"View the order by following the link below.\" href=\"$shopper_order_link\">View the order by following the link below.</a>"
        . "<br /><br />Questions? Problems?<br />"
        . "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom . "\">" . $mosConfig_mailfrom . "</a>"
        . "<br/> Please Note: Orders placed for deliveries outside of Sydney, Melbourne, Brisbane and Perth may be delayed. We will contact you via email if there is an issue.</b>";

    $vendor_header = "The following order was received.";
    $vendor_order_link = $mosConfig_live_site . "/index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin";
    $vendor_footer_html = "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";

    $vendor_image = "<img src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/vendor/" . (isset($aVendor->vendor_full_image) ? $aVendor->vendor_full_image : '') . "\" alt=\"vendor_image\" border=\"0\" />";


    //NEW CONFIRMATION
    $shopper_group_obj = false;

    $query = "SELECT 
        `g`.`shopper_group_discount`,
        `g`.`shopper_group_name`,
        `g`.`shopper_group_id`
    FROM `jos_vm_shopper_vendor_xref` AS `x`
    INNER JOIN `jos_vm_shopper_group` AS `g` ON `g`.`shopper_group_id`=`x`.`shopper_group_id`
    WHERE `x`.`user_id`=" . $user_id . "";

    $database->setQuery($query);
    $database->loadObject($shopper_group_obj);

    if ($shopper_group_obj) {
        $query = "INSERT INTO `jos_vm_orders_extra`
        (
            `order_id`,
            `shopper_discount_value`,
            `shopper_group_id`,
            `shopper_group_name`
        )
        VALUES (
            " . $order_id . ",
            '" . number_format($phpShopOrderSubtotal * floatval($shopper_group_obj->shopper_group_discount) / 100, 2, '.', '') . "',
            '" . $shopper_group_obj->shopper_group_id . "',
            '" . $shopper_group_obj->shopper_group_name . "'
        )";

        $database->setQuery($query);
        $database->query();
    }
    if ($used_donate_id) {
        adddonte($order_id, $donated_price, $used_donate_id);
    }

//assign order to wh



    $wh_obj = false;
    $zip_symbols = 4;

    while (($wh_obj == false) and ($zip_symbols > 0)) {
        $query = "SELECT 
        IFNULL(`wh`.`warehouse_email`, '') AS `warehouse_email`, 
        IFNULL(`wh`.`warehouse_code`, 'NOWAREHOUSEASSIGNED') AS `warehouse_code`
    FROM `jos_postcode_warehouse` AS `pwh` 
    LEFT JOIN `jos_vm_warehouse` AS `wh` ON `wh`.`warehouse_id`=`pwh`.`warehouse_id` 
    WHERE 
        `pwh`.`postal_code` LIKE '" . substr($deliver_zip_code, 0, $zip_symbols) . "' AND 
        `pwh`.`country` LIKE '" . $deliver_country . "'
    ";

        $database->setQuery($query);
        $wh_obj = false;
        $database->loadObject($wh_obj);

        $zip_symbols--;
    }


    if ($wh_obj) {
        $warehouse = $wh_obj->warehouse_code;
        $query = "UPDATE `jos_vm_orders` SET
        `warehouse`='" . $wh_obj->warehouse_code . "', 
        `mdate`='" . $timestamp . "' 
    WHERE `order_id`=" . $order_id . "
    ";
        $database->setQuery($query);
        $database->query();

        if (!empty($wh_obj->warehouse_email)) {
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
            $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $wh_obj->warehouse_email, $mail_Subject, $mail_Content, 1);
        }

    } else {
        $query = "UPDATE `jos_vm_orders` SET `warehouse`='NOWAREHOUSEASSIGNED' WHERE `order_id`=" . $order_id;
        $database->setQuery($query);
        $database->query();

    }

    $orderItems[] =  [
        'price_data' => [
            'currency' => $currency,
            'unit_amount' => $deliver_fee*100,
            'product_data' => [
                'name' => 'Delivery'
            ],
        ],
        'quantity' => '1'
    ];

    if($donated_price) {
        $orderItems[] =  [
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => $donated_price*100,
                'product_data' => [
                    'name' => 'Donation'
                ],
            ],
            'quantity' => '1'
        ];
    }


    $stripePaymentLinkUrl='';
    if ($payment_method_state == "stripe" && ($total_price + $donated_price) > 0) {
        try {
        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        $success_url_payment_link = "https://bloomex.com.au/account/?order_id=$order_id&deliver_country=$deliver_country&payment_place=paymentlink&session_id={CHECKOUT_SESSION_ID}&mosmsgsuccess=true&mosmsg=$VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG_STRIPE_SUCCESS";
        $cancel_url = "$mosConfig_live_site/administrator/index2.php?option=com_phoneorder&task=save_order_success&order_id=$order_id&mosmsg=$VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG_STRIPE_FAILED";

            $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
            $isValidEmail = preg_match($emailPattern, trim($account_email));
            if(!$isValidEmail){
                $accountEmail = 'no-email@bloomex.ca';
            } else {
                $accountEmail = $account_email;
            }

            $customer = $stripe->customers->create([
                'email' => $accountEmail,
                'name' => $bill_first_name,
            ]);


            $stripeSessionParams = [
            'success_url' => $success_url_payment_link,
            'cancel_url' => $cancel_url,
            'line_items' => $orderItems,
            'mode' => 'payment',
            'customer' => $customer->id,
            "custom_text" => ['submit' => ['message' => 'ATTENTION!!! After paying the system will redirect to the company site. Please don\'t close this page.']],
        ];

        $discountPrice = number_format(abs($total_price - $sub_total_price - $deliver_fee), 2, ".", "") * 100;
        $orderDiscounts = [
            'amount_off' =>  $discountPrice,
            'name' => 'Discount Price',
            'duration' => 'once',
            'currency' => $currency,
        ];

        if($discountPrice > 0){
            $stripeCoupon = $stripe->coupons->create($orderDiscounts);
            $stripeSessionParams['discounts'][] = ['coupon' => $stripeCoupon->id];
        }


        $customerStripeSession = $stripe->checkout->sessions->create($stripeSessionParams);
        $stripePaymentLinkUrl =  $customerStripeSession->url;
            $_SESSION['stripe_payment_url'] = $stripePaymentLinkUrl;
            $_SESSION['order_id_for_stripe_payment'] = $order_id;

            $sql_pc = "INSERT INTO tbl_stripe_orders_adm_logs 
                            (order_id, session_id, 
                            payment_url,order_status,deliver_country, date_added) 
                            VALUES ('{$order_id}',  '{$customerStripeSession->id}', '{$customerStripeSession->url}', 'pending_stripe', '{$deliver_country}','{$mysqlDatetime}')";
            $database->setQuery($sql_pc);
            $database->query();


        } catch (Exception $e) {
            $payment_msg = $e->getMessage();
        }
    }

    $updateComments = (($checkExistPromotionDiscountProduct) ? " | There is a promotion product in order" : '').
                      (($stripePaymentLinkUrl) ? " | The Stripe payment link is sent Automatically or you can Share it with the client $stripePaymentLinkUrl" : '');

    $query = "UPDATE `jos_vm_order_history` SET  `warehouse`='" . ($warehouse ?? '') . "',comments = CONCAT(comments, '".$updateComments."') WHERE `order_id`=" . $order_id;
    $database->setQuery($query);
    $database->query();
    UpdateSubOrderWarehouse($order_id, $warehouse);

//check address verification in percent
    $select_sub_order = 'SELECT sub_order_id FROM jos_vm_sub_orders_xref where order_id=' . $order_id;
    $database->setQuery($select_sub_order);
    $sub_orders = $database->loadObjectList();
    if ($sub_orders) {
        foreach ($sub_orders as $order) {
            check_address_verification($order->sub_order_id);
        }
    } else {
        check_address_verification($order_id);
    };

    if (!empty($_SESSION['platinum_cart'])) {

        $sql = "SELECT COUNT(*) FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'PC-01'";
        $database->setQuery($sql);
        $pc_rows = $database->loadResult();

        if ($pc_rows) {
            $sql_pc = "INSERT INTO tbl_platinum_club  (user_id,start_datetime) VALUES ('" . $user_id . "', NOW())";
            $database->setQuery($sql_pc);
            $database->query();

            $sql = "INSERT INTO tbl_platinum_club  (user_id,start_datetime) VALUES ('" . $user_id . "',NOW())";
            $database->setQuery($sql);
            $database->query();

            $platinum_cart_subject = "Dear  " . $bill_first_name;
            $platinum_cart_html = "
            Dear " . $bill_first_name . " <br><br>

            Thank you your purchase of the Bloomex Platinum Club. Your Platinum Club entitles you to many exclusive member benefits for a period of one-year.  First and foremost you will receive Free Standard Shipping on all purchases. In addition you will receive an exclusive “Platinum Club” Deal of the Week.<br><br> 

            Your Platinum Club is now active – all you have to do to receive the benefits is login with your email of record and all discounts and incentives will be automatically applied to your purchase.<br><br>

            We appreciate your continued business.<br><br>

            Sincerely<br><br>

            Bloomex <br>
            Australia's Official Florist
            ";

            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $platinum_cart_subject, $platinum_cart_html, 1);
        }

        $sql_pc = "UPDATE `tbl_platinum_club` SET `uses`=`uses`+1 WHERE `user_id`=" . $user_id . " AND `end_datetime` IS NULL";
        $database->setQuery($sql_pc);
        $rows = $database->loadObjectList();

        $query = "SELECT `id`, `uses` FROM `tbl_platinum_club` WHERE `user_id`=" . $user_id . " AND `end_datetime` IS NULL";

        $platinum_result = false;
        $database->setQuery($query);
        $database->loadObject($platinum_result);

        if ($platinum_result->uses == 6) {
            $sql_pc = "UPDATE `tbl_platinum_club` SET `end_datetime`=NOW() WHERE `id`=" . $platinum_result->id . "";
            $database->setQuery($sql_pc);
            $rows = $database->loadObjectList();

            $shopper_subject = 'Your Bloomex Platinum Club Membership.';
            $shopper_html = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/administrator/components/com_virtuemart/html/templates/user_emails/end_of_platinum_email.html');

            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);
        }

        $sql_pc = "INSERT INTO `tbl_platinum_club_uses` (`platinum_club_id`, `order_id`) VALUES (" . $platinum_result->id . ", " . $order_id . ")";
        $database->setQuery($sql_pc);
        $rows = $database->loadObjectList();


        unset($_SESSION['platinum_cart']);
    }


    require_once CLASSPATH . 'ps_comemails.php';

    $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='1'";

    if($deliver_country == 'NZL') {
        $query .= ' AND for_foreign_orders=1 ';
    }

    $confirmation_obj = false;
    $database->setQuery($query);
    $database->loadObject($confirmation_obj);

    $ps_comemails = new ps_comemails;

    if ($is_blended_day) {
        $ps_comemails->isLastMinuteOrder = true;
        $ps_comemails->isLastMinuteOrderLabel = '<span style="font-style: italic;">Delay notified/accepted during checkout</span>';
    }

    $message = $confirmation_obj->email_html;
    if ($stripePaymentLinkUrl) {
        $message = str_replace("Your order information follows", "To complete your payment, visit this link <a href='$stripePaymentLinkUrl'>STRIPE PAYMENT.</a><br><b>Please note that the link is active for one hour.</b>" , $message);
    }

    mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $message), 1);
    //!NEW CONFIRMATION


    check_and_crete_vc_coupon($sProductId, $aQuantity, $bill_first_name, $account_email);
    echo "save_order_success[--1--]" . str_replace(" ", "+", "Save Phone Order Successfully {$payment_msg}") . "[--1--]" . $order_id;
    exit(0);
}

function check_and_crete_vc_coupon($sProductId, $aQuantity, $first_name, $user_email)
{
    global $database, $mosConfig_live_site, $mosConfig_fromname, $mosConfig_mailfrom;

    //check VC product
    $sql = "SELECT product_id FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'VC-01'";
    $database->setQuery($sql);
    $vc_rows = $database->loadResult();

    $sVCCouponCode = "";
    if ($vc_rows) {
        for ($h = 0; $h < count($aQuantity); $h++) {
            $aQuantityTemp = explode("[--1--]", $aQuantity[$h]);

            if (intval($vc_rows) == intval($aQuantityTemp[0])) {
                $vc_quantity = $aQuantityTemp[1];
                break;
            }
        }
        for ($s = 0; $s < $vc_quantity; $s++) {

            $sVCCouponCode = createCouponName("VC-"); //"VC-" . strtoupper(genRandomString(8));
            if ($sVCCouponCode != "") {
                $sql = "INSERT INTO #__vm_coupons(coupon_code, percent_or_total , coupon_type, coupon_value )
						VALUES('$sVCCouponCode', 'total', 'gift', '20.00')";
                $database->setQuery($sql);
                $database->query();

                $shopper_subject = "Your Bloomex $20.00 voucher code";
                $shopper_html = "Dear $first_name,<br/><br/>
                Thank you for your purchase.  Your Bloomex $20.00 voucher code is <b>$sVCCouponCode</b><br/><br/>
                Call or order online at your convenience.<br/><br/>
                Best Regards,<br/><br/>
                Jessica<br/>
                Bloomex Inc<br/>
                866 912 5666<br/><br/>
                <img src='$mosConfig_live_site/templates/bloomex7/images/coupon_logo.png' />";

                mosMail($mosConfig_mailfrom, $mosConfig_fromname, $user_email, $shopper_subject, $shopper_html, 1);
            }
        }
    }
}

function genRandomString($length = 10)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

function createCouponName($prefix)
{
    $count = 0;
    while (true) {
        $sVCCouponCode = $prefix . strtoupper(genRandomString(8));
        if (!(checkCouponName($sVCCouponCode)))
            return $sVCCouponCode;
        if ($count > 5)
            return $sVCCouponCode . strtoupper(genRandomString(8)); // limit time
    }
    return "ERROR";
}

function checkCouponName($name)
{
    global $database;
    $query = "SELECT coupon_id FROM #__vm_coupons WHERE coupon_code='$name'";
    $database->setQuery($query);
    $result = $database->loadObjectList();
    if ($result && isset($result[0]))
        return true;
    return false;
}

function data_filter($aKw, $name)
{
    foreach ($aKw as $value) {
        $pos = strpos($name, $value);
        if ($pos == true)
            return true;
    }

    return false;
}

function startTag($parser, $name, $attrs)
{
    global $stack;
    $tag = array("name" => $name, "attrs" => $attrs);
    array_push($stack, $tag);
}

function cdata($parser, $cdata)
{
    global $stack;
    if (trim($cdata)) {
        $stack[count($stack) - 1]['cdata'] = $cdata;
    }
}

function endTag($parser, $name)
{
    global $stack;
    $stack[count($stack) - 2]['children'][] = $stack[count($stack) - 1];
    array_pop($stack);
}

function xml_to_array($file = '')
{
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

function makeXMLOrder($option)
{
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

function saveXMLOrder($option)
{
    global $database, $my, $mosConfig_absolute_path, $act, $mosConfig_offset, $mosConfig_test_card_numbers;
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
            $bill_evening_phone = $oUser->phone_2;

            $bill_fax = $oUser->fax;
            $bill_address_1 = $oUser->address_1;
            $bill_address_2 = $oUser->address_2;
            $bill_city = $oUser->city;
            $bill_state = $oUser->state;
            $bill_country = $oUser->country;
            $bill_zip_code = $oUser->zip;
            $bill_email = $oUser->user_email;
            $deliver_evening_phone = $oUser->extra_field_1;


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
														user_email, extra_field_1 , suite, street_number, street_name)
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
					   	   			'{$deliver_recipient_email}', '{$deliver_evening_phone}' , '{$deliver_suite}', '{$deliver_street_number}', '{$deliver_street_name}'";
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
                $aInfomation['time'] = (intval($aOptionParam[0]) - 12) . ":" . $aOptionParam[1] . " PM";
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

            if (($month_now > $deliver_month && $month_now != 12) || ($day_now > $deliver_day && $month_now == $deliver_month)) {
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


            $query = " SELECT shipping_rate_id, shipping_rate_value, tax_rate, shipping_rate_id, shipping_rate_name
									FROM #__vm_shipping_rate
									INNER JOIN #__vm_tax_rate
									ON shipping_rate_vat_id = tax_rate_id
									WHERE shipping_rate_name  = '{$shipping_method}'";
            $database->setQuery($query);
            $oShippingRateInfo = $database->loadObjectList();
            $aShippingRateInfo = $oShippingRateInfo[0];
            $nDeliverMethodFee = floatval($aShippingRateInfo->shipping_rate_value);


            $nDeliverFee = $nDeliverSameDayFee + $nSpecialDeliver + $nDeliverMethodFee;
            $nDeliverFeeTax = ($nDeliverFee * $aShippingRateInfo->tax_rate);
            $nDeliverFee = $nDeliverFeeTax + $nDeliverFee;

            $nTotalPrice = $nSubTotalPrice + $nDeliverFee;

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
            $PaymentVar["bill_evening_phone"] = $bill_evening_phone;

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
            $PaymentVar["deliver_evening_phone"] = $deliver_evening_phone;


            $aResult = array();
            if (!process_payment($order_number, $total_price, $PaymentVar, $aResult)) {
                $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
            }

            if ($aResult["approved"]) {
                $order_status = "A";
                $payment_msg = " and Payment Approved";
                if (in_array($credit_card_number, $mosConfig_test_card_numbers)) {
                    $order_status = "X";
                }
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
            $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

            $query = "INSERT INTO #__vm_order_history(	order_id,
														order_status_code,
														date_added,
														customer_notified,
														comments, user_name)
						VALUES ('$order_id',
								'P',
								'" . $mysqlDatetime . "',
								1,
								'','" . $my->username . "')";
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
								'{$payment_number}',
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
						   	   			'BT',
						   	   			'-default-',
						   	   			'{$bill_company_name}',
						   	   			'{$bill_last_name}',
						   	   			'{$bill_first_name}',
						   	   			'{$bill_middle_name}',
						   	   			'{$bill_phone}',
                                                                                '{$bill_evening_phone}',
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
															user_email, extra_field_1 )
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
						   	   			'{$deliver_recipient_email}', '{$deliver_evening_phone}' )";
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
                $j2 = 0;
                foreach ($rows as $value) {
                    $nQuantityTemp = $aProduct[$value->product_sku];

                    if (!$nQuantityTemp)
                        $nQuantityTemp = 1;
                    //if( $value->product_sku == 'RP02' ) $value->product_name .= ' ( '.$aproduct_balloon[$j2++].' ) ';
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
						   	   			" . (($value->product_price * $value->tax_rate) + $value->product_price) . ",
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
function do_upload($file, $dest_dir)
{
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

function process_payment($order_number, $order_total, $PaymentVar, &$aResult)
{
    global $vendor_mail, $vendor_currency, $database;
    $VM_LANG = new vmLanguage();
    $vmLogger = new vmLog();
    $order_total = round($order_total, 2);

    $ps_vendor_id = isset($PaymentVar["vendor_id"]) ? $PaymentVar["vendor_id"] : 1;
    $auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : "";

    $query = "SELECT * FROM #__shopper_group WHERE vendor_id = {$ps_vendor_id}";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aShopperGroup = $rows[0];
    if (count($aShopperGroup)) {
        $shopper_group_id = $aShopperGroup->shopper_group_id;
    } else {
        $shopper_group_id = 0;
    }


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

function SetSubOrder($i_sub, $sub_order_data)
{
    global $database;

    $ddate_time_new = strtotime('+' . $i_sub . ' month', $sub_order_data['ddate_time']);

    $w = date('w', $ddate_time_new);

    if ($w == 0) {
        $ddate_new = date('d-m-Y', strtotime('next monday', $ddate_time_new));
    } else {
        $ddate_new = date('d-m-Y', $ddate_time_new);
    }

    $query = "INSERT INTO `jos_vm_orders`
    (
        `user_id`,
        `vendor_id`,
        `order_number`,
        `user_info_id`,
        `order_total`,
        `order_subtotal`,
        `order_tax`,
        `order_tax_details`,
        `order_shipping`,
        `order_shipping_tax`,
        `coupon_discount`,
        `order_currency`,
        `order_status`,
        `cdate`,
        `mdate`,
        `ddate`,
        `ship_method_id`,
        `customer_note`,
        `customer_signature`,
        `customer_occasion`,
        `customer_comments`,
        `find_us`,
        `ip_address`,
        `coupon_code`,
        `coupon_type`,
        `coupon_value`,
        `username` 
    )
    VALUES ( 	
        " . $sub_order_data['user_id'] . ",
        " . $sub_order_data['vendor_id'] . ",
        '" . $sub_order_data['order_number'] . "',
        '" . $sub_order_data['user_info_id'] . "',
        '0.00',
        '0.00',
        '0.00',
        '',
        '0.00',
        '0.00',
        '0.00',
        '" . $sub_order_data['vendor_currency'] . "',
        '" . $sub_order_data['order_status'] . "',
        '" . $sub_order_data['timestamp'] . "',
        '" . $sub_order_data['timestamp'] . "',
        '" . $ddate_new . "',
        '" . $sub_order_data['sShippingMethod'] . "',
        '" . $database->getEscaped($sub_order_data['card_msg']) . "',
        '" . $database->getEscaped($sub_order_data['signature']) . "',
        '" . $database->getEscaped($sub_order_data['occasion']) . "',
        '" . $database->getEscaped($sub_order_data['card_comment']) . "',
        '" . $sub_order_data['find_us'] . "',
        '" . $database->getEscaped($sub_order_data['ip_address']) . "',
        '',
        '',
        '',
        '" . $database->getEscaped($sub_order_data['user_name']) . "'
    )";

    $database->setQuery($query);

    if (!$database->query()) {
        echo $query;
        echo $database->getErrorMsg();
        die;
    }

    $sub_order_id = $database->insertid();

    $query = "INSERT INTO `jos_vm_order_user_info` 
    (  
        `order_id`,
        `user_id`,
        `address_type`,
        `address_type_name`,
        `company`,
        `last_name`,
        `first_name`,
        `phone_1`,
        `phone_2`,
        `address_1`,
        `address_2`,
        `district`,
        `city`,
        `state`,
        `country`,
        `zip`,
        `user_email`, 
        `suite`, 
        `street_number`,
        `street_name` 
    )
    VALUES (  
        " . $sub_order_id . ",
        " . $sub_order_data['user_id'] . ",
        'BT',
        '-default-',
        '" . $database->getEscaped($sub_order_data['bill_company_name']) . "',
        '" . $database->getEscaped($sub_order_data['bill_last_name']) . "',
        '" . $database->getEscaped($sub_order_data['bill_first_name']) . "',
        '" . $database->getEscaped($sub_order_data['bill_phone']) . "',
        '" . $database->getEscaped($sub_order_data['bill_phone_2']) . "',
        '" . $database->getEscaped($sub_order_data['bill_address_1']) . "',
        '" . $database->getEscaped($sub_order_data['bill_address_2']) . "',
        '" . $database->getEscaped($sub_order_data['bill_district']) . "',
        '" . $database->getEscaped($sub_order_data['bill_city']) . "',
        '" . $database->getEscaped($sub_order_data['bill_state']) . "',
        '" . $database->getEscaped($sub_order_data['bill_country']) . "',
        '" . $database->getEscaped($sub_order_data['bill_zip_code']) . "',
        '" . $database->getEscaped($sub_order_data['account_email']) . "',
        '" . $database->getEscaped($sub_order_data['bill_suite']) . "',
        '" . $database->getEscaped($sub_order_data['bill_street_number']) . "',
        '" . $database->getEscaped($sub_order_data['bill_street_name']) . "'
    )";

    $database->setQuery($query);

    if (!$database->query()) {
        echo $query;
        echo $database->getErrorMsg();
        die;
    }

    $query = "INSERT INTO `jos_vm_order_user_info` 
    (  
        `order_id`,
        `user_id`,
        `address_type`,
        `address_type2`,
        `address_type_name`,
        `company`,
        `last_name`,
        `first_name`,
        `phone_1`,
        `phone_2`,
        `address_1`,
        `address_2`,
        `district`,
        `city`,
        `state`,
        `country`,
        `zip`,
        `user_email`, 
        `suite`,
        `street_number`, 
        `street_name` 
        )
    VALUES (  
        " . $sub_order_id . ",
        " . $sub_order_data['user_id'] . ",
        'ST',
        '" . $database->getEscaped($sub_order_data['address_type2']) . "',
        '" . $database->getEscaped($sub_order_data['address_user_name']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_company_name']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_last_name']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_first_name']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_phone']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_cell_phone']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_address_1']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_address_2']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_district']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_city']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_state']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_country']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_zip_code']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_recipient_email']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_suite']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_street_number']) . "',
        '" . $database->getEscaped($sub_order_data['deliver_street_name']) . "'
    )";

    $database->setQuery($query);

    if (!$database->query()) {
        echo $query;
        echo $database->getErrorMsg();
        die;
    }

    return $sub_order_id;
}

function SetSubOrderItem($sub_order_id, $sub_order_item_data)
{
    global $database;

    $query = "INSERT INTO `jos_vm_order_item` 
    (   
        `order_id`,
        `user_info_id`,
        `vendor_id`,
        `product_id`,
        `order_item_sku`,
        `order_item_name`,
        `product_quantity`,
        `product_item_price`,
        `product_final_price`,
        `ingredient_list`,
        `product_desc`,
        `order_item_currency`,
        `order_status`,
        `product_attribute`,
        `product_coupon`,
        `cdate`,
        `mdate` 
    )
    VALUES (     
        " . $sub_order_id . ",
        '" . $sub_order_item_data['user_info_id'] . "',
        " . $sub_order_item_data['vendor_id'] . ",
        " . $sub_order_item_data['product_id'] . ",
        '" . $database->getEscaped($database->getEscaped($sub_order_item_data['product_sku'])) . "',
        '" . $database->getEscaped($database->getEscaped($sub_order_item_data['product_name'])) . "',
        " . $sub_order_item_data['nQuantityTemp'] . ",
        '0.00',
        '0.00',
        '" . $database->getEscaped($sub_order_item_data['ingredient_list']) . "',
        '" . $database->getEscaped($sub_order_item_data['product_desc']) . "',
        '" . $sub_order_item_data['product_currency'] . "',
        '" . $sub_order_item_data['order_status'] . "',
        '" . $database->getEscaped($sub_order_item_data['product_desc']) . "',
        '',
        '" . $sub_order_item_data['timestamp'] . "',
        '" . $sub_order_item_data['timestamp'] . "'
    )";

    $database->setQuery($query);

    if (!$database->query()) {
        echo $query;
        echo $database->getErrorMsg();
        die;
    }

    $sub_order_item_id = $database->insertid();


    if (strpos($sub_order_item_data['product_name'], '(deluxe)')) {
        $query = "SELECT 
        `l`.`igl_quantity_deluxe` as `quantity`, 
        `o`.`igo_product_name` as `name`
    FROM `product_ingredients_lists` as `l`
        LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
    WHERE `l`.`product_id`=" . $sub_order_item_data['product_id'] . "";
        $database->setQuery($query);
    } elseif (strpos($sub_order_item_data['product_name'], '(supersize)')) {
        $query = "SELECT 
        `l`.`igl_quantity_supersize` as `quantity`, 
        `o`.`igo_product_name` as `name`
    FROM `product_ingredients_lists` as `l`
        LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
    WHERE `l`.`product_id`=" . $sub_order_item_data['product_id'] . "";
        $database->setQuery($query);
    } elseif (strpos($sub_order_item_data['product_name'], '(petite)')) {
        $query = "SELECT 
        `l`.`igl_quantity_petite` as `quantity`, 
        `o`.`igo_product_name` as `name`
    FROM `product_ingredients_lists` as `l`
        LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
    WHERE `l`.`product_id`=" . $sub_order_item_data['product_id'] . "";
        $database->setQuery($query);
    } else {
        $query = "SELECT 
        `l`.`igl_quantity` as `quantity`, 
        `o`.`igo_product_name` as `name`
    FROM `product_ingredients_lists` as `l`
        LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
    WHERE `l`.`product_id`=" . $sub_order_item_data['product_id'] . "";
        $database->setQuery($query);
    }
    $order_item_ingredients_rows = $database->loadObjectList();

    $order_item_ingredients_array = array();

    foreach ($order_item_ingredients_rows as $row) {
        $order_item_ingredients_array[] = "(" . $sub_order_id . ", " . $sub_order_item_id . ", '" . $database->getEscaped($row->name) . "', '" . ($row->quantity * $sub_order_item_data['nQuantityTemp']) . "')";
    }

    if (sizeof($order_item_ingredients_array) > 0) {
        $query = "INSERT INTO `jos_vm_order_item_ingredient` 
        (
            `order_id`, 
            `order_item_id`, 
            `ingredient_name`, 
            `ingredient_quantity`
        ) VALUES 
            " . implode(',', $order_item_ingredients_array) . "";

        $database->setQuery($query);
        $database->query();
    }

    return true;
}

function UpdateSubOrderWarehouse($order_id, $warehouse)
{
    global $database, $mosConfig_offset;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $query = 'Select sub_order_id FROM jos_vm_sub_orders_xref WHERE order_id=' . $order_id;
    $database->setQuery($query);
    $sub_orders = $database->loadObjectList();
    if ($sub_orders) {
        foreach ($sub_orders as $sub_order) {

            $query = "UPDATE `jos_vm_orders` SET
            `warehouse`='" . $warehouse . "', 
            `mdate`='" . $timestamp . "' 
        WHERE `order_id`=" . $sub_order->sub_order_id . "
        ";

            $database->setQuery($query);
            $database->query();
        }
    }
}

function SetSubOrderXref($order_id, $sub_order_id)
{
    global $database;

    $query = "INSERT INTO `jos_vm_sub_orders_xref`
    (
        `order_id`,
        `sub_order_id`
    )
    VALUES (
        " . $order_id . ",
        " . $sub_order_id . "
    )";

    $database->setQuery($query);

    if (!$database->query()) {
        echo $query;
        echo $database->getErrorMsg();
        die;
    }

    return true;
}

function process_payment_centralization($PaymentVarCentralization)
{
    global $mosConfig_payment_centralization_url, $mosConfig_payment_centralization_auth;

    $curl = curl_init($mosConfig_payment_centralization_url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_payment_centralization_auth);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($PaymentVarCentralization));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    $curl_response = curl_exec($curl);
    $json = json_decode($curl_response, true);

    if ($curl_response === false) {
        $text_error = 'Curl error: ' . curl_error($curl);
        $text_error .= '<br><br>Request: ' . http_build_query($PaymentVarCentralization);
        moslogerrors('Phoneorder payment', $text_error);
    }


    return $json;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Untitled Document</title>
</head>

<body>
</body>
</html>
