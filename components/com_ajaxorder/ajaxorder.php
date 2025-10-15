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
session_name('virtuemart');
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 0,
    'cookie_secure' => true,
    'cookie_httponly' => true,
]);
// no direct
defined('_VALID_MOS') or die('Restricted access');
global $mosConfig_absolute_path;
const FIFTEEN_SECONDS = 15 * 60;
require_once($mainframe->getPath('front_html'));
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/language.class.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/languages/english.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/Log/Log.php");

//this is wrap up on exit(), want to call logging first
function endit($case)
{
    global $mosConfig_absolute_path;
    require_once($mosConfig_absolute_path . "/end_access_log.php");
    exit($case);
}

switch ($task) {

    case 'RemoveAddress':
        RemoveAddress();
        break;

    case 'UpdateAddress':
        UpdateAddress();
        break;
    case 'getOrSetShippingInfoId':
        getOrSetShippingInfoId();
        break;
    case 'GetAjaxProducts':
        GetAjaxProducts();
        break;
    case 'getValidCalendarDates':
        getValidCalendarDates();
        break;
    case 'setCardMsgAndOccasion':
        setCardMsgAndOccasion();
        break;
    case 'updateUserAddress':
        updateUserAddress();
        break;
    case 'deleteUserAddress':
        deleteUserAddress();
        break;
    case 'CartAction':
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and !empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $CartAction = new CartAction;

            switch ($_POST['action']) {
                case 'SetQuantityCart':
                    echo $CartAction->SetQuantityCart();
                    break;
                case 'GetCart':
                    echo $CartAction->GetCart();
                    break;
                case 'RemoveFromCart':
                    echo $CartAction->RemoveFromCart();
                    break;
                case 'AddToCart':
                    echo $CartAction->AddToCart();
                    break;
                default:
                    break;
            }
        } else {
            echo 'Bye.';
        }
        exit(0);
        break;

    case 'CheckoutAjax':

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and !empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $CheckoutAjax = new CheckoutAjax;

            switch ($_POST['action']) {
                case 'GetShippingPrice':
                    echo $CheckoutAjax->GetShippingPrice($_POST['delivery_date'], $_POST['user_info_id']);
                    endit(0);
                    break;

                case 'GetTotal';
                    echo $CheckoutAjax->GetTotal($_POST['user_info_id'], $_POST['donate']);
                    endit(0);
                    break;

                case 'SetShippingMethod':
                    echo $CheckoutAjax->SetShippingMethod($_POST['shipping_method']);
                    endit(0);
                    break;

                case 'SetCouponCode';
                    echo $CheckoutAjax->SetCouponCode($_POST['coupon_code']);
                    endit(0);
                    break;

                case 'SetShippingAddress';
                    echo $CheckoutAjax->SetShippingAddress($_POST['checkout_user_info_id']);
                    endit(0);
                    break;

                default:
                    break;
            }
        } else {
            echo 'Bye.';
            endit(0);
        }
        break;

    case 'setOrder':
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and !empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            setOrder();
        } else {
            echo 'Bye.';
            endit(0);
        }
        break;

    case 'validCart':
        validCart();
        break;

    case 'confirmOrder':
        //confirmOrder();
        die('please contact system administrator');
        break;

    case 'getUserAddress':
        getUserAddress();
        break;

    case 'copyUserBillingAddress':
        copyUserBillingAddress();
        break;

    case 'getUserBillingAddress':
        getUserBillingAddress();
        break;

    case 'deleteOrderItem':
        deleteOrderItem();
        break;

    case 'updateQuantity':
        updateQuantity();
        break;

    case 'updateStandardShipping':
        updateStandardShipping();
        break;

    case 'updateDiscount':
        updateDiscount();
        break;

    case 'updateCouponDiscount':
        updateCouponDiscount();
        break;

    case 'updateSpecialInstructions':
        updateSpecialInstructions();
        break;

    case 'updateCardMessage':
        updateCardMessage();
        break;

    case 'loadOrderHistory':
        loadOrderHistory();
        break;

    case 'updateBilling':
        updateBillingInfo();
        break;

    case 'updateDeliver':
        updateDeliverInfo();
        break;

    case 'loadOrderItemDetail':
        loadOrderItemDetail("order");
        break;

    case 'loadOrderCart':
        loadOrderItemDetail("cart");
        break;

    case 'exportAddressBookXML':
        exportAddressBookXML("cart");
        break;

    case 'selectDeliveryOption':
        selectDeliveryOption("cart");
        break;
    case 'check_ccard':
        check_ccard();
        break;
    case 'get_google_place':
        get_google_place();
        break;
    case 'get_google_geocode':
        get_google_geocode();
        break;
    case 'get_billing_country':
        get_billing_country();
        break;
    case 'get_donation_price':
        get_donation_price();
        break;
    case 'checkCardProduct':
        checkCardProduct();
        break;
    case 'sendGoogleAnalytics':
        sendGoogleAnalytics();
        break;
    case 'savePhoneNumberForUpdates':
        savePhoneNumberForUpdates();
        break;
    case 'setExitPopupClick':
        setExitPopupClick();
        break;
    default:
        loadAjaxOrder();
        break;
}

function GetAjaxProducts() {
    global $sef;
    $start = mosGetParam($_REQUEST, 'start');
    $products = mosGetParam($_REQUEST, 'products', '[]');
    require_once(CLASSPATH . 'vmAbstractObject.class.php');
    require_once(CLASSPATH . 'ps_database.php');
    require_once(CLASSPATH . 'ps_product.php');
    $ps_product = new ps_product;
   die($ps_product->show_product_list($products, ($sef->landing_type > 0 ? true : false),$start,true));

}
function updateUserAddress()
{
    global $database;
    $data = json_decode(file_get_contents('php://input'), true);
    $user_info_id = mosGetParam($data, 'id', '');
    $return = array();
    $return['result'] = false;

    $email = $_SESSION['auth']['username'] ?? 'unknown';
    $mdate = time();
    $suite = mosGetParam($data, "suite", "");
    $firstName = mosGetParam($data, "firstName", "");
    $lastName = mosGetParam($data, "lastName", "");
    $streetNumber = mosGetParam($data, "streetNumber", "");
    $streetName = mosGetParam($data, "streetName", "");
    $city = mosGetParam($data, "city", "");
    $zip = mosGetParam($data, "zip", "");
    $state = mosGetParam($data, "stateShort", "");
    $phone = mosGetParam($data, "phone", "");
    $email = mosGetParam($data, "email", "");

    if ($user_info_id) {
        $query = "UPDATE jos_vm_user_info SET  
            first_name='{$firstName}',
            last_name='{$lastName}',
            phone_1='{$phone}',
            user_email='{$email}',
            city='{$city}',
            state='{$state}',
            zip='{$zip}',
            suite='{$suite}',
            street_number='{$streetNumber}',
            street_name='{$streetName}',  
            mdate = '{$mdate}', 
            extra_field_3 = 'modif by: {$email}'  
       WHERE user_info_id ='{$user_info_id}'
       ";

        $database->setQuery($query);
        $res = $database->query();

        if ($res) {
            $return['result'] = true;
        }
    }

    echo json_encode($return);
    exit;
}

function deleteUserAddress()
{
    global $database;
    $data = json_decode(file_get_contents('php://input'), true);
    $user_info_id = mosGetParam($data, 'id', '');
    $return = array();
    $return['result'] = false;

    $userId = (int) $_SESSION['auth']['user_id'] ?? 0;;

    if ($user_info_id || $userId) {

        $queryCheck = "SELECT COUNT(*) FROM jos_vm_user_info WHERE user_id =$userId AND address_type='ST'";
        $database->setQuery($queryCheck);
        $addressCount = (int) $database->loadResult();

        if ($addressCount <= 1) {
            $return['error'] = "You can't delete the last address";
            echo json_encode($return);
            exit;
        }

        // Удаляем адрес
        $queryDelete = "DELETE FROM jos_vm_user_info WHERE user_info_id = '{$user_info_id}'";
        $database->setQuery($queryDelete);
        $res = $database->query();

        if ($res) {
            $return['result'] = true;
        }
    }

    echo json_encode($return);
    exit;
}

function savePhoneNumberForUpdates()
{
    global $database, $mosConfig_offset;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    $return = array();
    //$user_id = mosGetParam($_REQUEST, 'user_id');
    $order_id = mosGetParam($_REQUEST, 'order_id');

    $phonenumber = mosGetParam($_REQUEST, 'phoneNumber');
    $PhoneNumber = preg_replace('/[^0-9]/', '', $phonenumber);


    $q = "SELECT id FROM `tbl_track_order_status` order_id='" . $order_id . "'";
    $database->setQuery($q);
    $check = $database->loadResult();
    $return['msg'] = 'already_exist';
    if (!$check) {
        $sql = "INSERT INTO `tbl_track_order_status` ( `order_id`,`phone_number`,`date_added`) VALUES ( " . $order_id . ",'" . $database->getEscaped($PhoneNumber) . "','" . $mysqlDatetime . "')";
        $database->setQuery($sql);
        $database->query();
        $return['msg'] = 'success';
        $return['phone'] = mosGetParam($_REQUEST, 'phoneNumber');
    }
    echo json_encode($return);
    exit;
}
function setExitPopupClick()
{
    global $mosConfig_offset;

    $_SESSION['guest']['free_product'] = true;
    $_SESSION['free_product']['time'] = time() + ($mosConfig_offset * 60 * 60);

    exit('success');
}
function sendGoogleAnalytics()
{

    $measurement_id = 'G-0PR9LY6TG2';
    $api_secret = 'aWQ9R1RNLTVRWFY1NFhEJmVudj0xJmF1dGg9eUhUVmtaV1Fpc29UUjlRQS1ETGQyZw==';
    $url = "https://server-side-tagging-fzuvocxoca-uc.a.run.app?measurement_id=" . $measurement_id . "&api_secret=" . $api_secret;

    $data = array(
        'client_id' => 'gtm-5qxv54xd-nwnly',
        'events' => array(
            array(
                'name' => 'search',
                'params' => array(
                    'search' => 'rose',
                )
            )
        )
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json",
            'method'  => 'POST',
            'content' => json_encode($data)
        )
    );
    $context  = stream_context_create($options);
    $resp = file_get_contents($url, false, $context);
    $return['result'] = 'success';
    $return['resp'] = $resp;

    exit(json_encode($return, JSON_FORCE_OBJECT));
}
function checkCardProduct()
{

    global $mosConfig_offset;

    if (isset($_SESSION['cart']) && count($_SESSION['cart']) == 1) {
        if (isset($_SESSION['free_product'])) {
            $couponTime = $_SESSION['free_product']['time'];
            $timeNow = time() + ($mosConfig_offset * 60 * 60);
            $couponLiveTime = $timeNow - $couponTime;
            if ($couponLiveTime < FIFTEEN_SECONDS) {
                exit('error');
            }
        }
        exit('success');
    }

    if (isset($_SESSION['guest'])) {
        $couponTime = $_SESSION['free_product']['time'];
        $timeNow = time() + ($mosConfig_offset * 60 * 60);
        $couponLiveTime = $timeNow - $couponTime;
        if ($couponLiveTime < FIFTEEN_SECONDS) {
            exit('error');
        }
    }

    exit('error');
}

function get_donation_price()
{
    global $database;
    $deliver_info_id = mosGetParam($_REQUEST, 'deliver_info_id');
    $res = '';

    $q = "SELECT zip FROM `jos_vm_user_info` WHERE `user_info_id`='" . $deliver_info_id . "'";
    $database->setQuery($q);
    $deliver_zip_code = $database->loadResult();


    $query = "SELECT WH.warehouse_id FROM #__vm_warehouse AS WH, #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.published=1 AND PWH.postal_code LIKE '" . substr($deliver_zip_code, 0, 3) . "%'";
    $database->setQuery($query);
    $oWarehouse = $database->loadResult();
    $warehouse_id = 0;
    if ($oWarehouse) {
        $warehouse_id = $oWarehouse;
    }

    $query_don = "SELECT `name`,`price`,`text`,`id` FROM `tbl_donation_vars` where ( published=1 and warehouse_id='" . $warehouse_id . "') OR ( published=1 and warehouse_id=0)";
    $database->setQuery($query_don);
    $res_don = $database->loadRow();
    if ($res_don && $res_don[1]) {
        $res_don[1] = number_format($res_don[1], 2, ".", "");
        $res = '<tr class="donate">
                <td  width=35% valign="top"><b>I would like to donate <span style="color:red">$' . $res_don[1] . '</span> to <span style="color:red">' . $res_don[0] . '</span></b>
                    <div class="tooltip">
                        <img src="/images/M_images/con_info.png" align="middle" border="0">
                        <span class="tooltiptext">
                            <div class="popup_text">
                                 ' . $res_don[2] . '
                            </div>
                        </span>
                    </div>


                </td>
                <td>
                    <input type="checkbox" name="donate" id="donate" donation_name="' . $res_don[0] . ' " donation_id="' . $res_don[3] . '" value="0" onclick="if(this.checked){this.value = 1} else {this.value = 0}">
                </td>
            </tr>';
    }
    echo $res;
    exit(0);
}

function check_ccard()
{
    global $mosConfig_absolute_path;

    include_once $mosConfig_absolute_path . '/CreditCard.php';
    $return = array();
    $credit_card_number = $_POST['ccard_number'];
    $result = CreditCard::validCreditCard($credit_card_number);
    if ($result && $result['valid']) {
        $return['result'] = true;
        $return['type'] = $result['type'];
    } else {
        $return['result'] = false;
        $return['error'] = "Please enter a valid credit card number";
    }

    echo json_encode($return);

    exit(0);
}

function selectDeliveryOption()
{
    global $database, $iso_client_lang, $mosConfig_live_site, $my;

    $CurrentDeliveryState = trim(mosGetParam($_REQUEST, 'delivery_state'));
    $CurrentDeliveryDate = trim(mosGetParam($_REQUEST, 'delivery_date'));
    $aInfomation['delivery_default_date'] = trim(mosGetParam($_REQUEST, 'delivery_default_date'));

    if ($CurrentDeliveryDate) {
        $aCurrentDeliveryDate = explode("/", $CurrentDeliveryDate);

        $sDateWhere = " AND name LIKE '" . intval($aCurrentDeliveryDate[0]) . "/%'";
    }
    //old_delivery
    $shipping_method = intval(mosGetParam($_REQUEST, 'shipping_method'));
    $query = "SELECT shipping_rate_value FROM #__vm_shipping_rate WHERE shipping_rate_id = $shipping_method";
    $database->setQuery($query);
    $nRate = $database->loadResult();

    $aInfomation['ShippingMethod']['rate'] = floatval($nRate);
//	$aInfomation['ShippingMethod']['label']	= $oShippingMethod[0]->shipping_rate_name;
//	$aInfomation['ShippingMethod']['text']	=  LangNumberFormat::number_format($oShippingMethod[0]->shipping_rate_value, 2, ".", "");

    $query = "SELECT shipping_rate_id, shipping_rate_name FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC LIMIT 3";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $aInfomation['ShippingMethod']['text'] = "";
    $j = 0;
    foreach ($rows as $item) {

        if ($shipping_method == $item->shipping_rate_id || (!$shipping_method && $j == 0)) {
            $sChecked = "checked='checked'";
        } else {
            $sChecked = "";
        }

        if ($iso_client_lang != "en") {
            $query = "SELECT `value` FROM #__jf_content AS JC, #__languages AS L WHERE JC.language_id = L.id AND L.iso = '$iso_client_lang' AND JC.reference_field = 'shipping_rate_name' AND JC.reference_id = $item->shipping_rate_id ";
            $database->setQuery($query);
            $item->shipping_rate_name = $database->loadResult();

            $sExpressClass = "express_image_fr";
        } else {
            $sExpressClass = "express_image";
        }

        $shipping_rate_name = str_replace("Express Image", "<span class='$sExpressClass'>&nbsp;</span>", utf8_encode(htmlspecialchars($item->shipping_rate_name, ENT_QUOTES)));

        //$aInfomation['ShippingMethod']['text'] .= '<div class="txt-1"><input onclick="chooseShippingMethod( \'' . $CurrentDeliveryDate . '\' , ' . $item->shipping_rate_id . ' )" name="shipping_method_radio" id="shipping_method_radio' . $item->shipping_rate_id . '" value="' . $item->shipping_rate_id . '" ' . $sChecked . '  type="radio"><label for="shipping_method' . $item->shipping_rate_id . '">' . $shipping_rate_name . '</label></div>';
        $aInfomation['ShippingMethod']['text'] .= '<div class="txt-1">' . $shipping_rate_name . '</div>';
        $j++;
    }
    //end old delivery
    $user_info_id = trim(mosGetParam($_REQUEST, 'user_info_id'));
    //$aInfomation['Calendar'] = draw_calendar($aCurrentDeliveryDate[1], $aCurrentDeliveryDate[0], $aCurrentDeliveryDate[2], $aUnAvailableDate, $aSpecialDeliver, $aInfomation['ShippingMethod']['rate'], $nCutOffTime, $aDeliveryPostalCode[2], $aInfomation['delivery_default_date'], $aFreeShipping, $aShippingSurcharge, $realPostCode);
    $subtotalprice = $_REQUEST['subtotalprice'] ? $_REQUEST['subtotalprice'] : 0;
    $aInfomation['Calendar'] = draw_calendar($CurrentDeliveryState, $CurrentDeliveryDate, $user_info_id, $subtotalprice);

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


    HTML_AjaxOrder::selectDeliveryOption($aInfomation);
}

/* draws a calendar */

//function draw_calendar($day, $month, $year, $aUnAvailableDate, $aSpecialDeliver, $nShippingMethod, $nCutOffTime, $nDeliveryPostalCode, $delivery_default_date, $aFreeShipping, $aShippingSurcharge, $realPostCode) {

function draw_calendar($CurrentDeliveryState, $delivery_date, $user_info_id, $subtotalprice)
{
    global $mosConfig_lang, $database;
    $discount_shipping = 0;
    date_default_timezone_set('Australia/Sydney');

    $a_delivery_date = explode('/', $delivery_date);

    $month = $a_delivery_date[0];
    $day = $a_delivery_date[1];
    $year = $a_delivery_date[2];

    $query = "SELECT `shipping_rate_value` FROM `jos_vm_shipping_rate` WHERE `shipping_rate_id`=31 LIMIT 1";
    $database->setQuery($query);
    $shipping_price = $database->loadResult();


    /* get total price from cart */
    $total_price = 0;
    $products_id = array();

    foreach ($_SESSION['cart'] as $item) {
        if ($item['product_id'] > 0) {
            $products_id[] = $item['product_id'];
        }
    }

    $sql = "SELECT * FROM #__vm_product_options WHERE product_id IN (" . implode(',', $products_id) . ")";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    $sql_platin = "SELECT count(product_id) as count FROM #__vm_product  WHERE product_sku LIKE 'PC-01' AND product_id IN (" . implode(',', $products_id) . ")";
    $database->setQuery($sql_platin);
    $row_platin = $database->loadObjectList();

    $nextDay = 0;
    $freeShipping = 1;
    $tuefri = 0;

    foreach ($rows as $item) {
        if ($item->no_delivery == 0)
            $freeShipping = 0;
        if ($item->next_day_delivery == 1)
            $nextDay = 1;
        if ($item->tuefri_delivery == 1)
            $tuefri = 1;
    }
    /*
      $sql = "DELETE  FROM tbl_platinum_club WHERE cdate  <= UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 1 YEAR)) ";
      $database->setQuery($sql);
      $database->query();
     */
    $sql_platin = "SELECT count(id) as count FROM tbl_platinum_club  WHERE user_id=" . $_SESSION['auth']['user_id'] . " AND `end_datetime` IS NULL";
    $database->setQuery($sql_platin);
    $row_platin_old = $database->loadObjectList();

    if ($row_platin[0]->count == '1' || $row_platin_old[0]->count > 0) {
        $_SESSION['platinum_cart'] = 1;
        $discount_shipping = 1;
    }

    $sql = "SELECT * FROM jos_freeshipping_price WHERE public=1";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    if (count($rows) > 0) {
        if ($rows[0]->price <= floatval($subtotalprice)) {
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
            }
            $k++;
        }
    }
    /**/

    /* get coupon */


    if (!empty($_SESSION['coupon_code'])) {
        if (strpos($_SESSION['coupon_code'], "PC-") !== false) {
            $discount_shipping = 1;

            //$_SESSION['coupon_value'])
        }
    }

    /**/

    /* get postalcodes */
    $query = "SELECT `zip`,`state` FROM `jos_vm_user_info` WHERE `user_info_id`='" . $user_info_id . "' LIMIT 1";
    $database->setQuery($query);
    $result = $database->loadObjectList();

    $real_post_code = $result[0]->zip;
    $real_state = $result[0]->state;

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

    if ($postcode_active == false) {
        $date_other = explode('/', $first_date_other);
        $postcode_reason = $first_postcode_reason;
    }

    /* get cut off time */

    //if (preg_match('/^6/si', $real_post_code))
    if (substr($real_post_code, 0, 1) == '6') {
        date_default_timezone_set('Australia/Perth');
    }

    $query_limittime = "SELECT options FROM tbl_options WHERE type='cut_off_time' ";
    $database->setQuery($query_limittime);
    $sOptionParam = $database->loadResult();
    $aOptionParam = explode("[--1--]", $sOptionParam);
    $nTimeLimit = $aOptionParam[0] * 60 + $aOptionParam[1];
    $nTimeLimitNextDay = 16 * 60;
    $nHourNow = intval(date('H', time()));
    $nMinuteNow = intval(date('i', time()));
    $nTimeNow = $nHourNow * 60 + $nMinuteNow;
    if ($nTimeLimit > $nTimeNow) {
        $nCutOffTime = 0;
    } else {
        $nCutOffTime = 1;
    }


    $nCutOffTime_price = 5; //$aOptionParam[2];
    $nNextCutOffTime_price = 0; //$aOptionParam[2];

    if ($nTimeLimitNextDay > $nTimeNow) {
        $nNextCutOffTime = 0;
    } else {
        $nNextCutOffTime = 1;
    }


    /**/


    $sql_un_del = "SELECT `day`, `description`, `states` FROM `tbl_unavailable_delivery`  WHERE `month`='" . $month . "'";
    $database->setQuery($sql_un_del);
    $row_un_del = $database->loadObjectList();

    $un_del_array = array();
    //$states_array = array('NW', 'VI', 'AT', 'QL', 'WA', 'SA', 'NT');

    foreach ($row_un_del as $row) {
        $states = explode(';', $row->states);

        if (in_array($CurrentDeliveryState, $states)) {
            $un_del_array[$row->day] = $row->description;
        }
    }

    $undeliverdayscount = 0;
    $undeliverpostalcode = 0;
    $query = "SELECT postal_code,days_in_route,deliverable FROM `jos_postcode_warehouse` WHERE `postal_code`='" . $real_post_code . "' and published=1 LIMIT 1";
    $database->setQuery($query);
    $database->loadObject($postalcodeoption);
    if ($postalcodeoption) {
        if ($postalcodeoption->deliverable == 0) {
            $undeliverpostalcode = 1;
        } else {
            $undeliverdayscount = $postalcodeoption->days_in_route;
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
        if (($list_day < (date('j') + $undeliverdayscount) and $month == date("m")) or (is_array($date_other) and mktime(0, 0, 0, intval($date_other[0]), intval($date_other[1]), $date_other[2]) > mktime(0, 0, 0, intval($month), $list_day, intval($year))) or $unAvailable_status == 1 or ($tuefri == 1 and in_array(date('N', strtotime($list_day . '-' . intval($month) . '-' . intval($year))), array(1, 6, 7)))) {
            $day_class = 'calendar-day-np';
        } else {
            $day_class = 'calendar-day';
        }

        if ($list_day == $day and $month == date('m')) {
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

        /*
          $needle = $year.'-'.$month.'-'.$list_day;

          $needle = $year.'-'.$month.'-'.$list_day;
          $result = array_filter($ShippingSurcharge, function($innerArray){
          global $needle;

          return ($innerArray[1] == $needle);

          });


          echo '<pre>'.print_r($result, true).'</pre>';
         */

        $shipping_total_price = 0;
        $shipping_div_price = '';
        if ($nNextCutOffTime == 1 and date('j') == date('t', strtotime('today')) and $list_day == 1) {
            if ($nNextCutOffTime == 1) {
                $NextCutOffTime = 1;
            }
        }
        /*
          if (isset($NextCutOffTime)) {
          $shipping_total_price += $nNextCutOffTime_price;
          unset($NextCutOffTime);
          }
         */
        if ($list_day == date('j') and $month == date("m") and $nNextCutOffTime == 1 or $list_day == date('j') and $month == date("m") and $nCutOffTime == 1) {
            if ($nNextCutOffTime == 1)
                $NextCutOffTime = 1;

            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'Cut off time.\');" ';
        } elseif (array_key_exists($list_day, $un_del_array)) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $un_del_array[$list_day] . '\');" ';
        } elseif ($list_day < date('j') and $month == date("m")) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'This day has already passed\');" ';
        } elseif ($list_day < (date('j') + $undeliverdayscount) and $month == date("m")) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'This day has blocked for this postal code\');" ';
        } elseif ($list_day == date('j') and $month == date("m") and $nextDay == 1) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'Products that you have ordered, the ability to deliver the next day.\');" ';
        } elseif ($tuefri == 1 and in_array(date('N', strtotime($list_day . '-' . intval($month) . '-' . intval($year))), array(1, 6, 7))) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'Your order has the products that are delivered only from Tuesday to Friday\');" ';
        } elseif (is_array($date_other) and mktime(0, 0, 0, intval($date_other[0]), intval($date_other[1]), $date_other[2]) > mktime(0, 0, 0, intval($month), $list_day, intval($year))) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'<b>The reason that you can not make an order for this day</b>:<br/>' . $postcode_reason . '\');" ';
        } elseif ($unAvailable_status == 1) {
            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $unAvailable_text . '\');" ';
        } else {
            $message = '';
            $day_type = 'Other';
            if ($freeShipping == 1) {
                $message = '<b>Free shipping</b>';
            } elseif ($freeshipping_status == 1) {
                $message = '<b>' . $freeshipping_text . '</b>';
            } else {


                /*
                  if ($list_day == date('j') AND $month == date("m") AND $nCutOffTime == 0) {
                  //$shipping_total_price += ($nCutOffTime_price + 5);
                  $shipping_total_price += ($nCutOffTime_price);
                  //                    $message = _DELIVERY_SAME_DAY . '<br/>$' . ($nCutOffTime_price+5);
                  $day_type = 'Same_day';
                  if ($shipping_surcharge_price != 0) {
                  $message .= '<br/>' . _DELIVERY_EXTRA_SURCHARGE . '<br/>$' . $shipping_surcharge_price;
                  }
                  } elseif ($list_day == (date('j') + 1) AND $month == date("m") AND $nCutOffTime == 1) {
                  $day_type = 'Same_day';
                  $shipping_total_price += $nNextCutOffTime_price;
                  } elseif ($list_day == (date('j') + 2) AND $month == date("m") AND $nCutOffTime == 1) {
                  $day_type = 'Next_day';
                  $shipping_total_price += $nCutOffTime_price;
                  } elseif ($list_day == (date('j') + 1) AND $month == date("m")) {
                  $shipping_total_price += $nCutOffTime_price;
                  //                    $message = 'Next Day Delivery<br/>$' . ($nCutOffTime_price);
                  $day_type = 'Next_day';
                  if ($shipping_surcharge_price != 0) {
                  $message .= '<br/>' . _DELIVERY_EXTRA_SURCHARGE . '<br/>$' . $shipping_surcharge_price;
                  }
                  } elseif ($shipping_surcharge_price != 0) {
                  $message = _DELIVERY_EXTRA_SURCHARGE . '<br/>$' . $shipping_surcharge_price;
                  }
                 */


                $shipping_total_price += $shipping_price + $shipping_surcharge_price;


                if ($list_day == date('j') and $month == date("m")) {
                    if ($nCutOffTime == 0) {
                        $shipping_total_price += $nCutOffTime_price;
                    }
                } elseif ($list_day == (date('j') + 1) and $month == date("m")) {
                    if ($nNextCutOffTime == 0) {
                        $shipping_total_price += $nNextCutOffTime_price;
                    } elseif ($nNextCutOffTime == 1) {
                        $shipping_total_price += $nCutOffTime_price;
                    }
                } elseif ($list_day == (date('j') + 2) and $month == date("m")) {
                    if ($nNextCutOffTime == 1) {
                        $shipping_total_price += $nNextCutOffTime_price;
                    }
                }
                if ($shipping_surcharge_price != 0) {
                    $message .= _DELIVERY_EXTRA_SURCHARGE . '<br/>$' . $shipping_surcharge_price;
                }

                if ($discount_shipping == 1) {
                    $message .= '<br/><b>Discount shipping</b>';
                }
                if ($discount_shipping == 1) {

                    $shipping_total_price -= 14.95;
                    /*
                      if ($list_day == (date('j') + 1) AND $month == date("m")){
                      $shipping_total_price  = '5.00';
                      }else{
                      $shipping_total_price = '0.00';
                      }
                     * 
                     */
                }
                //$message = $nNextCutOffTime.'|'.$nCutOffTime.'|'.$nNextCutOffTime_price.'|'.$nCutOffTime_price;
            }

            if ($freeShipping == 1 or $freeshipping_status == 1 or $shipping_total_price < 0 or $shipping_total_price == 0) {
                $shipping_total_price = '0.00';
            }

            $shipping_total_price = number_format($shipping_total_price, 2);


            $day_function = ' onmouseover="changeDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $message . '\');" onclick="chooseDay(\'' . $month . '/' . $list_day . '/' . $year . '\', \'' . $shipping_total_price . '\', \'' . $day_type . '\');" ';

            $shipping_div_price = '<div class="special-deliver">$' . $shipping_total_price . '</div>';
        }
        /**/

        $calendar .= '<td class="' . $day_class . '" ' . $day_function . '>';
        $calendar .= '<div class="day-number ' . $day_class_2 . '">' . $list_day . '</div>' . $shipping_div_price;

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

    /* old calendar */
    /**/
    return $calendar;
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

function try_again($error = '')
{
    //if (!$error) {
    $error = 'FAILURE IN PROCESSING PAYMENT: PLEASE CONFIRM CREDIT CARD INFORMATION IS ACCURATE AND RE-SUBMIT ORDER or CALL 1-888-912-5666 FOR IMMEDIATE ASSISTANCE';
    //}
    if ($_SESSION['try_again']) {
        if ($_SESSION['try_again'] < 5) {
            $_SESSION['try_again']++;
        } else {
            session_destroy();
            echo "redirect[--1--]" . $error . "";
            endit(0);
        }
    } else {
        $_SESSION['try_again'] = 1;
    }
    return "error[--1--]" . $error . "";
}

function confirmOrder()
{
    global $database, $my, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $mos_debug_email, $vendor_currency;
    date_default_timezone_set('Australia/Sydney');
    $timestamp = time();
    $PaymentVar = array();
    $file = '';

    $user_id = intval(mosGetParam($_REQUEST, "user_id"));
    if ($user_id == '0') {
        die('wrong user');
    }
    $user_info_id = mosGetParam($_REQUEST, "user_info_id", "");
    $user_name = mosGetParam($_REQUEST, "user_name", "");
    $account_email = mosGetParam($_REQUEST, "account_email", "");

    $occasion = mosGetParam($_REQUEST, "occasion", "CONGR");
    $shipping_method = mosGetParam($_REQUEST, "shipping_method", "");
    $card_msg = mosGetParam($_REQUEST, "card_msg", "");
    $signature = mosGetParam($_REQUEST, "signature", "");
    $card_comment = mosGetParam($_REQUEST, "card_comment", "");
    $deliver_date = mosGetParam($_REQUEST, "deliver_date", "");
    $vendor_currency = mosGetParam($_REQUEST, "vendor_currency_string", "");
    $balloon_value = mosGetParam($_REQUEST, "balloon_value", "");

    $payment_method_state = mosGetParam($_REQUEST, "payment_method_state", "");
    $payment_method = mosGetParam($_REQUEST, "payment_method", "");
    $name_on_card = mosGetParam($_REQUEST, "name_on_card", "");
    $credit_card_number = mosGetParam($_REQUEST, "credit_card_number", "");
    $credit_card_security_code = mosGetParam($_REQUEST, "credit_card_security_code", "");
    $expire_month = intval(mosGetParam($_REQUEST, "expire_month", ""));
    $expire_year = intval(mosGetParam($_REQUEST, "expire_year", ""));
    $find_us = intval(mosGetParam($_REQUEST, "find_us", 0));
    $company_invoice = intval(mosGetParam($_REQUEST, "company_invoice", 0));

    $sProductId = mosGetParam($_REQUEST, "product_id_string", "");
    $aProductId = explode(",", $sProductId);
    $sQuantity = mosGetParam($_REQUEST, "quantity_string", "");
    $aQuantityTemp = explode(",", $sQuantity);
    $sProductCoupon = mosGetParam($_REQUEST, "product_coupon_string", "");
    $aProductCouponTemp = explode(",", $sProductCoupon);
    $sSelectBouquet = mosGetParam($_REQUEST, "select_bouquet", "");
    $aSelectBouquet = explode(",", $sSelectBouquet);

    //$hostname = "ftp://".$user . ":" . $pass . "@" . $host . "/log/" . $file;
    $hostname = $mosConfig_absolute_path . "/qazwsx/edcrfv/" . $file;
    //$hostname = $file;
    $arr = array();
    $sProductId = "";
    $aQuantity = array();
    for ($h = 0; $h < count($aProductId); $h++) {
        if ($aProductId[$h]) {
            $sProductId .= $aProductId[$h] . ",";
            $aQuantity[] = $aProductId[$h] . "[--1--]" . $aQuantityTemp[$h];
            $arr[$aProductId[$h]] = $aSelectBouquet[$h];
            if (empty($aProductCouponTemp[$h]))
                $aProductCouponTemp[$h] = "";
            $aProductCoupon[$h] = $aProductId[$h] . "[--1--]" . $aProductCouponTemp[$h];
        }
    }
    $sProductId = substr($sProductId, 0, strlen($sProductId) - 1);

    $total_price = doubleval(mosGetParam($_REQUEST, "total_price", ""));
    $sub_total_price = doubleval(mosGetParam($_REQUEST, "sub_total_price", ""));
    $total_tax = doubleval(mosGetParam($_REQUEST, "total_tax", ""));
    $nStateTax = doubleval(mosGetParam($_REQUEST, "state_tax", ""));
    $deliver_fee = doubleval(mosGetParam($_REQUEST, "deliver_fee", ""));
    $deliver_fee_type = mosGetParam($_REQUEST, "deliver_fee_type", "Other");
    $total_deliver_tax_fee = doubleval(mosGetParam($_REQUEST, "total_deliver_tax_fee", ""));
    $coupon_discount = doubleval(mosGetParam($_REQUEST, "coupon_discount", ""));
    $coupon_code_string = trim(mosGetParam($_REQUEST, "coupon_code_string", ""));
    $coupon_type_string = trim(mosGetParam($_REQUEST, "coupon_type_string", ""));
    $coupon_code = mosGetParam($_REQUEST, "coupon_code", "");
    $coupon_code_type = mosGetParam($_REQUEST, "coupon_code_type", "");
    $coupon_value = mosGetParam($_REQUEST, "coupon_value", "");
    $free_shipping = intval(mosGetParam($_REQUEST, "free_shipping", ""));
    //Update rate use total_price
    $query = "SELECT rate "
        . "FROM #__users  WHERE id = '" . $user_id . "' ";
    $database->setQuery($query);
    $rows = $database->loadResult();
    $oRatePrev = $rows;
    $oRateNext = $oRatePrev;
    if ($oRatePrev < 10) {
        if (($total_price >= 60) && ($total_price < 100)) {
            $oRateNext += 1;
        }
        if ($total_price >= 100) {
            $oRateNext += 2;
        }
        if ($oRateNext > 10) {
            $oRateNext = 10;
        }

        $query = "UPDATE #__users SET rate={$oRateNext} WHERE id = '{$user_id}' and rate<10";
        $database->setQuery($query);
        $database->query();


        if ($oRateNext != $oRatePrev) {
            $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

            $query = " INSERT INTO #__rate_history (id,user_name,date,coment,prev_rate) VALUES({$user_id}, 'Bloomex System','{$mysqlDatetime}','Customer\'s Purchase','{$oRatePrev}' ) ";
            //echo $query;
            $database->setQuery($query);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "error";
                endit(0);
            }
        }
    }


    if ($user_info_id) {


        $query = "SELECT * FROM #__vm_user_info AS VUI, #__users AS U WHERE VUI.user_id = U.id AND VUI.user_info_id ='{$user_info_id}'  AND VUI.user_id = '{$user_id}'";
        $database->setQuery($query);
        $oInfo = $database->loadObjectList();
        $oUser = $oInfo[0];


        if (count($oUser)) {
            $address_user_name = $oUser->address_type_name;
            $deliver_company_name = $oUser->company;
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
            $address_type2 = $oUser->address_type2;
            $deliver_suite = $oUser->suite;
            $deliver_street_number = $oUser->street_number;
            $deliver_street_name = $oUser->street_name;


            if ($deliver_street_number == '') {
                die('error[--1--]Please Enter Shipping Street Number');
            }
            if ($deliver_street_name == '') {
                die('error[--1--]Please Enter Shipping Street Name');
            }
            if ($deliver_city == '') {
                die('error[--1--]Please Enter Shipping City');
            }
            if ($deliver_zip_code == '') {
                die('error[--1--]Please Enter Shipping Zip Code');
            }
            if ($deliver_phone == '') {
                die('error[--1--]Please Enter Shipping Phone');
            }
        }
    }


    //get legacy
    if (isset($_SESSION['legacy_id'])) {
        $bill_phone = mysql_real_escape_string($_REQUEST['legacy_phone_1']);
        $bill_company_name = mysql_real_escape_string($_REQUEST['legacy_company']);
        $bill_password = mysql_real_escape_string(md5($_REQUEST['legacy_password']));
        $bill_first_name = mysql_real_escape_string($_REQUEST['legacy_first_name']);
        $bill_last_name = mysql_real_escape_string($_REQUEST['legacy_last_name']);
        $bill_city = mysql_real_escape_string($_REQUEST['legacy_city']);
        $bill_state = mysql_real_escape_string($_REQUEST['legacy_state']);
        $bill_country = mysql_real_escape_string($_REQUEST['legacy_country']);
        if ($bill_country == "USA") {
            $vendor_currency = "USD";
        }
        $bill_zip_code = mysql_real_escape_string($_REQUEST['legacy_zip']);
        $bill_suite = mysql_real_escape_string($_REQUEST['legacy_address_suite']);
        $bill_street_number = mysql_real_escape_string($_REQUEST['legacy_address_street_number']);
        $bill_street_name = mysql_real_escape_string($_REQUEST['legacy_address_street_name']);
        $account_email = $bill_email = mysql_real_escape_string($_REQUEST['legacy_email']);

        $addr = $bill_suite . ' ' . $bill_street_number . ' ' . $bill_street_name;

        if ($bill_email == '') {
            die('error[--1--]Please Enter Billing  Email');
        }
        if ($bill_phone == '') {
            die('error[--1--]Please Enter Billing  Phone');
        }
        if ($bill_first_name == '') {
            die('error[--1--]Please Enter Billing  First Name');
        }
        if ($bill_last_name == '') {
            die('error[--1--]Please Enter Billing  Last Name');
        }
        if ($bill_password == '') {
            die('error[--1--]Please Enter Billing  Password');
        }

        if ($bill_street_number == '') {
            die('error[--1--]Please Enter Billing  Street Number ');
        }
        if ($bill_street_name == '') {
            die('error[--1--]Please Enter Billing  Street Name ');
        }
        if ($bill_city == '') {
            die('error[--1--]Please Enter Billing  City ');
        }
        if ($bill_zip_code == '') {
            die('error[--1--]Please Enter Billing  Postal Code ');
        }


        $query = " UPDATE #__vm_user_info
                        SET address_type_name	= '-default-',
                                company				= '{$bill_company_name}',
                                last_name			= '{$bill_last_name}',
                                first_name			= '{$bill_first_name}',
                                phone_1				= '{$bill_phone}',
                                address_1			= '{$addr}',
                                address_2			= ' ',
                                city				= '{$bill_city}',
                                state				= '{$bill_state}',
                                country				= '{$bill_country}',
                                zip				= '{$bill_zip_code}',
                                suite				= '{$bill_suite}',
                                street_number			= '{$bill_street_number}',
                                street_name			= '{$bill_street_name}'

					   	   WHERE user_id = '{$user_id}' AND address_type = 'BT'";
        //die($query);
        $database->setQuery($query);
        $database->query();

        $query = " UPDATE #__users SET password= '{$bill_password}' WHERE id = '{$user_id}'";

        $database->setQuery($query);
        $database->query();

        unset($_SESSION['legacy_id']);
        unset($_SESSION['legacy_email']);
    } else {


        //====================================================================================================================================
        $query = "SELECT * FROM #__vm_user_info WHERE user_id = '{$user_id}' AND address_type = 'BT'";
        $database->setQuery($query);
        $oInfo = $database->loadObjectList();
        $oUser = $oInfo[0];

        $database->query();
        if (count($oUser)) {
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
            if ($bill_country == "USA") {
                $vendor_currency = "USD";
            }
            $bill_zip_code = $oUser->zip;
            $bill_email = $oUser->user_email;
            $bill_suite = $oUser->suite;
            $bill_street_number = $oUser->street_number;
            $bill_street_name = $oUser->street_name;
        }
    }

    $query = "SELECT * FROM #__vm_vendor WHERE vendor_country = '{$bill_country}'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $sDataLog = " end salect from __vm_vendor 693\n";

    if (count($rows)) {
        $aVendor = $rows[0];
        $vendor_id = $aVendor->vendor_id;
        $vendor_full_image = $aVendor->vendor_full_image;
        $vendor_name = $aVendor->vendor_name;
        $vendor_phone = $aVendor->vendor_phone;
        $vendor_address_1 = $aVendor->vendor_address_1;
        $vendor_zip = $aVendor->vendor_zip;
        $vendor_city = $aVendor->vendor_city;
        $vendor_state = $aVendor->vendor_state;
    } else {
        $vendor_id = 1;
        $vendor_full_image = "";
        $vendor_name = "";
        $vendor_phone = "";
        $vendor_address_1 = "";
        $vendor_zip = "";
        $vendor_city = "";
        $vendor_state = "";
    }


    $query = " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id
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


    $query = " SELECT VMP.product_id, VMP.product_price, VTR.tax_rate
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
            $value_product_price = getProductPrice($value->product_id, $value->product_price);

            //echo $value->product_id . "===" . $value->product_price  . "===" . $value_product_price . "<br/><br/>";
            if (!isset($order_tax_details["$nStateTax"])) {
                $order_tax_details["$nStateTax"] = doubleval($nStateTax) * doubleval($value_product_price);
            } else {
                $order_tax_details["$nStateTax"] = $order_tax_details["$nStateTax"] + (doubleval($nStateTax) * doubleval($value_product_price));
            }
        }
    } else {
        foreach ($rows as $value) {
            $value_product_price = getProductPrice($value->product_id, $value->product_price);
            //echo $value->product_id . "===" . $value->product_price  . "===" . $value_product_price . "<br/><br/>";
            if (!isset($order_tax_details["$value->tax_rate"])) {
                $order_tax_details["$value->tax_rate"] = doubleval($value->tax_rate) * doubleval($value_product_price);
            } else {
                $order_tax_details["$value->tax_rate"] = $order_tax_details["$value->tax_rate"] + (doubleval($value->tax_rate) * doubleval($value_product_price));
            }
        }
    }


    /* Insert the main order information */
    $order_number = $user_id . "_" . date("YmdHis");

    //================================== PAYMENT =========================================
    $VM_LANG = new vmLanguage();
    if ($credit_card_number == "4111111111111111") {
        $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
        $order_status = "X";
        $payment_msg = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
    } elseif ($company_invoice) {
        $aResult["order_payment_log"] = 'Company Invoice';
        $order_status = "3";
        $payment_msg = 'Company Invoice';
    } else {
        require_once($mosConfig_absolute_path . '/components/com_ajaxorder/nab/payment.php');

        $aData = array();
        $aData[0] = $order_number;
        $aData[1] = date("YdmHiu") . "000" + 600;
        $aData[2] = number_format($total_price, 2, '', '');
        $aData[3] = $order_number;
        $aData[4] = $credit_card_number;
        $aData[5] = sprintf("%02d", $expire_month) . "/" . substr($expire_year, -2, 2);




        $aResult = array();
        $aResult = processNABpayment($aData);


        if (!empty($aResult["Status"]["statusCode"])) {
            if ($aResult["Status"]["statusCode"] == "000") {
                if (!empty($aResult['MessageInfo']["messageID"]) && $aResult['MessageInfo']["messageID"] == $aData[0]) {
                    if (($aResult['Payment']["TxnList"]["Txn"]["responseCode"] == "00" || $aResult['Payment']["TxnList"]["Txn"]["responseCode"] == "08") && trim($aResult['Payment']["TxnList"]["Txn"]["responseText"]) == "Approved") {
                        $order_status = "A";
                        $aResult["approved"] = 1;
                        $payment_msg = " Payment has been approved";
                        if (in_array($credit_card_number, $mosConfig_test_card_numbers)) {
                            $order_status = "X";
                        }

                        $aResult["order_payment_trans_id"] = $aResult['MessageInfo']["messageID"];
                        $aResult["order_payment_log"] = $aResult['Payment']["TxnList"]["Txn"]["responseText"];
                    } else {
                        if ($aResult['Payment']["TxnList"]["Txn"]["responseCode"] == '05')
                            $aResult['Payment']["TxnList"]["Txn"]["responseText"] = 'Credit card declined, please resubmit your credit card.';

                        $Error = "Error #" . $aResult['Payment']["TxnList"]["Txn"]["responseCode"] . ": " . $aResult['Payment']["TxnList"]["Txn"]["responseText"];
                        echo try_again($Error);
                        endit(0);
                    }
                } else {
                    $Error = "Error: The messageID is incorrect";
                    echo "error[--1--]<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $Error . "</b>";
                    endit(0);
                }
            } else {
                $Error = "Error #" . $aResult["Status"]["statusCode"] . ": " . $aResult["Status"]["statusDescription"];
                echo try_again($Error);
                endit(0);
            }
        } else {
            echo try_again();
            endit(0);
        }
    }

    $phpShopDeliveryDate = date("M d, Y", strtotime($deliver_date) + ($mosConfig_offset * 60 * 60));
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
												 ip_address,
												 coupon_code,
												 coupon_type,
												 coupon_value,
												 username )
				   	   VALUES( 	$user_id,
				   	   			$vendor_id,
				   	   			'$order_number',
				   	   			'$user_info_id',
				   	   			$total_price,
				   	   			$sub_total_price,
				   	   			$total_tax,
				   	   			'" . serialize($order_tax_details) . "',
				   	   			$deliver_fee,
				   	   		   	$total_deliver_tax_fee,
				   	   		   	$coupon_discount,
				   	   		   	'$vendor_currency',
				   	   		   	'$order_status',
				   	   		   	" . $timestamp . ",
				   	   		   	" . $timestamp . ",
				   	   		   	'" . date("d-m-Y", strtotime($deliver_date)) . "',
				   	   		   	'" . $sShippingMethod . "',
						   	   	'" . mysql_real_escape_string($card_msg) . "',
						   	   	'" . mysql_real_escape_string($signature) . "',
						   	   	'" . mysql_real_escape_string($occasion) . "',
						   	   	'" . mysql_real_escape_string($card_comment) . "',
						   	   	$find_us,
						   	   	'" . htmlspecialchars(strip_tags($ip_address)) . "',
								'$coupon_code',
						   	   	'$coupon_code_type',
						   	   	'$coupon_value',
						   	   	'" . htmlspecialchars(strip_tags($user_name)) . "' )";
    $database->setQuery($query);
    //echo "1. $query <br/>".$database->getErrorMsg()."<br/><br/>";

    $database->query();
    $order_id = $database->insertid();
    $test_q = $query;

    $query = "INSERT INTO #__vm_order_delivery_type(	order_id,
												delivery_type,
												date_added)
				VALUES ('$order_id',
						'" . $deliver_fee_type . "',
						'" . $timestamp . "')";
    $database->setQuery($query);
    $database->query();

    if (isset($_COOKIE['utm_source']) & isset($_COOKIE['utm_campaign']) & isset($_COOKIE['utm_medium'])) {
        $utm_date = date('Y-m-d');
        $query = "INSERT INTO tbl_track_legacy_url (`url`,`date`,`order_id`) VALUES ('" . $_COOKIE['utm_legacy_url'] . "','" . $utm_date . "','" . $order_id . "')";
        $database->setQuery($query);
        $database->query();

        setcookie("utm_medium", "", time() - 36000, '/; SameSite=Strict',"",true,true);
        setcookie("utm_source", "", time() - 36000, '/; SameSite=Strict',"",true,true);
        setcookie("utm_campaign", "", time() - 36000, '/; SameSite=Strict',"",true,true);
        setcookie("utm_legacy_url", "", time() - 36000, '/; SameSite=Strict',"",true,true);
    }

    if ($coupon_discount > 0 && strpos($coupon_code_string, "PC-") !== false) {
        $phpShopShippingDiscount = "-" . LangNumberFormat::number_format($coupon_discount, 2, '.', ' ');
        $phpShopCouponDiscount = "-/-";
    } elseif ($coupon_discount > 0) {
        $phpShopCouponDiscount = "-" . LangNumberFormat::number_format($coupon_discount, 2, '.', ' ');
        $phpShopShippingDiscount = "-/-";
    }


    //===================== DELETE GIF COUNPON AFTER USED =================================
    if ($coupon_code_string && $coupon_type_string == "gift") {
        $sql = "DELETE FROM #__vm_coupons WHERE coupon_code = '$coupon_code_string' AND coupon_type = 'gift'";
        $database->setQuery($sql);
        $database->query();
    }

    //echo "2. $query <br/>".$database->getErrorMsg()."<br/><br/>";

    /* Insert the initial Order History. */
    date_default_timezone_set('Australia/Sydney');
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

    $query = "INSERT INTO #__vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments, user_name)
				VALUES ('$order_id',
						'$order_status',
						'" . $mysqlDatetime . "',
						1,
						'From front-end.','" . $my->username . "')";
    $database->setQuery($query);
    $database->query();
    //echo "3. $query <br/>".$database->getErrorMsg()."<br/><br/>";


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
    } elseif ($credit_card_number == "4111111111111111") {
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
							'{$aResult["order_payment_log"]}[--1--]$payment_method',
							'{$name_on_card}',
							'{$aResult["order_payment_trans_id"]}')";
        $database->setQuery($query);
        $database->query();
    }
    //echo "4. $query <br/>".$database->getErrorMsg()."<br/><br/>";

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
													user_email, suite, street_number,street_name )
				   	   VALUES(  '" . $order_id . "',
				   	   			{$user_id},
				   	   			'BT',
				   	   			'-default-',
				   	   			'" . htmlentities($bill_company_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_last_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_first_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_middle_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_phone, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_fax, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_address_1, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_address_2, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_city, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_state, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_country, ENT_QUOTES) . "',
				   	   			'" . htmlentities($bill_zip_code, ENT_QUOTES) . "',
				   	   			'" . htmlentities($account_email, ENT_QUOTES) . "',
                                                                '" . htmlentities($bill_suite, ENT_QUOTES) . "',
                                                                '" . htmlentities($bill_street_number, ENT_QUOTES) . "',
                                                                '" . htmlentities($bill_street_name, ENT_QUOTES) . "' )";
    $database->setQuery($query);

    if (!$database->query()) {
        //die("error[--1--]$sDeliveryError");
        $sMySubject = "The wrong of Billing Address for order #$order_id";
        $sErrorMsg = $database->getErrorMsg();
        $sMyContent = "$query <br/><br/><br/>$sErrorMsg<br/><br/><br/>";
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $mos_debug_email, $sMySubject, $sMyContent, 1);
    }


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
													city,
													state,
													country,
													zip,
													user_email, suite,street_number, street_name )
				   	   VALUES(  '" . $order_id . "',
				   	   			{$user_id},
				   	   			'ST',
				   	   			'" . htmlentities($address_type2, ENT_QUOTES) . "',
				   	   			'" . htmlentities($address_user_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_company_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_last_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_first_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_middle_name, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_phone, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_cell_phone, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_fax, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_address_1, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_address_2, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_city, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_state, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_country, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_zip_code, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_recipient_email, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_suite, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_street_number, ENT_QUOTES) . "',
				   	   			'" . htmlentities($deliver_street_name, ENT_QUOTES) . "' )";
    $database->setQuery($query);
    if (!$database->query()) {
        //die("error[--1--]$sDeliveryError");
        $sMySubject = "The wrong of Delivery Address for order #$order_id";
        $sErrorMsg = $database->getErrorMsg();
        $sMyContent = "$query <br/><br/><br/>$sErrorMsg<br/><br/><br/>";
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $mos_debug_email, $sMySubject, $sMyContent, 1);
    }
    //echo "6. $query <br/>".$database->getErrorMsg()."<br/><br/>";

    /* Insert all Products from the Cart into order line items */

    $query = " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc,VMP.saving_price, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate
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
							<td width="10%">Product Price (Gross)</td>
							<td width="5%">Quantity</td>
							<td width="10%">Total</td>
						</tr>';


    $query = " SELECT SG.shopper_group_discount
					FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id
					WHERE  SVX.user_id = {$user_id} LIMIT 1";
    $database->setQuery($query);
    $ShopperGroupDiscount = $database->loadResult();
    if ($ShopperGroupDiscount) {
        $ShopperGroupDiscount = $ShopperGroupDiscount / 100;
    }


    $phpShopOrderSubtotal = 0;
    $phpShopOrderTax = 0;
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

//
//            $query5 = "SELECT product_price , saving_price FROM #__vm_product_price WHERE product_id='" . $value->product_id . "' LIMIT 1";
//            $database->setQuery($query5);
//            $database->loadObject($oPrice5);
//
//
//            if (!empty($oPrice5->saving_price) && $oPrice5->saving_price > 0 && $oPrice5->product_price >= 0) {
//                $value_product_price = (($oPrice5->product_price - $oPrice5->saving_price)+($oPrice5->product_price - $oPrice5->saving_price)*$my_taxrate);
//            } else {
//                $value_product_price = getProductPrice($value->product_id, $value->product_price);
//                    }
//                               if (!empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0) {
//                $product_price =  (($aPrice['product_price'] - $aPrice['saving_price']) + ($aPrice['product_price'] - $aPrice['saving_price']) * $my_taxrate);
//                //$sale = "<img src='/images/sale.png' class='sale' />";
//            } else {
//                $product_price = $aPrice["product_price"] + $aPrice["product_price"] * $my_taxrate;
//            } 
            require_once(CLASSPATH . 'vmAbstractObject.class.php');
            require_once(CLASSPATH . 'ps_database.php');
            require_once(CLASSPATH . 'ps_product.php');
            $ps_product = new ps_product;
            $aPrice = $ps_product->get_retail_price($value->product_id);
            $my_taxrate = $ps_product->get_product_taxrate($value->product_id);
            $bloomex_reg_price = (($aPrice['product_price'] - $aPrice['saving_price']) + ($aPrice['product_price']) * $my_taxrate);
            $product_item_price = number_format($bloomex_reg_price, 2, '.', '');
            $product_item_price = floatval($product_item_price);

            if ($ShopperGroupDiscount) {
                $product_item_price = $product_item_price - ($product_item_price * doubleval($ShopperGroupDiscount));
            } else {
                $product_item_price = $product_item_price;
            }

            $product_item_price = $product_item_price + ${$arr[$value->product_id]};
            if ($arr[$value->product_id] == 'deluxe' || $arr[$value->product_id] == 'supersize')
                $value->product_name = $value->product_name . "  ( " . strtoupper($arr[$value->product_id]) . " )";

            $product_final_price = ($product_item_price * floatval($nStateTax)) + $product_item_price;
            $sProductCouponItem = "";
            for ($h = 0; $h < count($aProductCoupon); $h++) {
                $aProductCouponTemp = explode("[--1--]", $aProductCoupon[$h]);
                if (intval($value->product_id) == intval($aProductCouponTemp[0])) {
                    if ($aProductCouponTemp[1]) {
                        $query = " SELECT * FROM #__vm_product_discount WHERE coupon_code = '" . $aProductCouponTemp[1] . "'";
                        $database->setQuery($query);
                        $coupon_code = $database->loadObjectList();
                        $product_coupon_value = 0;
                        if (!empty($coupon_code[0]->discount_id)) {
                            $sProductCouponItem = $coupon_code[0]->coupon_code . "|" . $coupon_code[0]->is_percent . "|" . $coupon_code[0]->amount;
                            if ($coupon_code[0]->is_percent) {
                                $product_coupon_value = (floatval($product_final_price) * ($coupon_code[0]->amount / 100));
                            } else {
                                $product_coupon_value = floatval($coupon_code[0]->amount);
                            }

                            if ($product_coupon_value >= floatval($product_final_price)) {
                                $product_final_price = 0;
                            } else {
                                $product_final_price = floatval($product_final_price) - $product_coupon_value;
                            }
                        }
                    }
                    break;
                }
            }

            if (htmlentities($value->product_sku, ENT_QUOTES) == 'RP02')
                $value->product_name = $value->product_name . ' ( ' . $balloon_value . ' ) ';
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
															product_coupon,
															cdate,
															mdate )
					   	   VALUES(     $order_id,
					   	   			'$user_info_id',
					   	   			$vendor_id,
					   	   			" . $value->product_id . ",
					   	   			'" . htmlentities($value->product_sku, ENT_QUOTES) . "',
					   	   			'" . htmlentities($value->product_name, ENT_QUOTES) . "',
					   	   			" . intval($nQuantityTemp) . ",
					   	   			" . $product_item_price . ",
					   	   			" . $product_final_price . ",
					   	   			'" . $value->product_currency . "',
					   	   			'P',
					   	   			'" . htmlentities(strip_tags($value->product_desc), ENT_QUOTES) . "',
									'" . $sProductCouponItem . "',
					   	   			'$timestamp',
					   	   			'$timestamp'
					   	   			 )";
                $database->setQuery($query);
                $database->query();

                $order_items .= '<tr>
									<td>' . ($j + 1) . '. </td>
									<td>' . addslashes($value->product_sku) . '</td>
									<td>' . stripslashes($value->product_name) . '<br/></td>
									<td>' . LangNumberFormat::number_format($product_final_price, 2, ".", " ") . '</td>
									<td>' . intval($nQuantityTemp) . '</td>
									<td>' . LangNumberFormat::number_format($product_final_price * intval($nQuantityTemp), 2, ".", " ") . '</td>
								</tr>';
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
															product_coupon,
															cdate,
															mdate )
					   	   VALUES(  $order_id,
					   	   			'$user_info_id',
					   	   			$vendor_id,
					   	   			" . $value->product_id . ",
					   	   			'" . htmlentities($value->product_sku, ENT_QUOTES) . "',
					   	   			'" . htmlentities($value->product_name, ENT_QUOTES) . "',
					   	   			" . intval($nQuantityTemp) . ",
					   	   			" . $product_item_price . ",
					   	   			" . $product_final_price . ",
					   	   			'" . $value->product_currency . "',
					   	   			'P',
					   	   			'" . htmlentities(strip_tags($value->product_desc), ENT_QUOTES) . "',
									'" . $sProductCouponItem . "',
					   	   			'$timestamp',
					   	   			'$timestamp'
					   	   			 )";
                $database->setQuery($query);
                $database->query();


                /*
                 * <td>' . LangNumberFormat::number_format((($product_item_price * $value->tax_rate ) + $product_item_price), 2, ".", " ") . '</td>
                 * <td>' . LangNumberFormat::number_format((($product_item_price * $value->tax_rate ) + $product_item_price) * intval($nQuantityTemp), 2, ".", " ") . '</td>
                 */
                $order_items .= '<tr>
									<td>' . ($j + 1) . '. </td>
									<td>' . addslashes($value->product_sku) . '</td>
									<td>' . stripslashes($value->product_name) . '<br/></td>
									<td>' . LangNumberFormat::number_format($product_final_price, 2, ".", " ") . '</td>
									<td>' . intval($nQuantityTemp) . '</td>
									<td>' . LangNumberFormat::number_format($product_final_price * intval($nQuantityTemp), 2, ".", " ") . '</td>
								</tr>';
                //$phpShopOrderTax		+=  ($value->product_price * $value->tax_rate )*intval($nQuantityTemp);
            }

            $order_item_id = $database->insertid();

            //ORDER ITEM INGREDIENTS

            $q = "SELECT `l`.`igl_quantity` as `quantity`, `o`.`igo_product_name` as `name`
                FROM `product_ingredients_lists` as `l`
                LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
                WHERE `l`.`product_id`=" . $value->product_id . "";
            $database->setQuery($q);
            $order_item_ingredients_rows = $database->loadObjectList();

            $order_item_ingredients_array = array();

            foreach ($order_item_ingredients_rows as $row) {
                $order_item_ingredients_array[] = "(" . $order_id . ", " . $order_item_id . ", '" . $database->getEscaped($row->name) . "', '" . ($row->quantity * intval($nQuantityTemp)) . "')";
            }

            $query = "INSERT INTO #__vm_order_item_ingredient (order_id, order_item_id, ingredient_name, ingredient_quantity)
                VALUES " . implode(',', $order_item_ingredients_array) . "";

            $database->setQuery($query);
            $database->query();

            unset($order_item_ingredients_array);


            //END


            $phpShopOrderSubtotal += round($product_final_price * intval($nQuantityTemp), 2);

            /* Insert ORDER_PRODUCT_TYPE */
            $query = "SELECT * FROM #__vm_product_product_type_xref, #__vm_product_type
					  WHERE #__vm_product_product_type_xref.product_id = '" . $value->product_id . "'
					  AND #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id";
            $database->setQuery($query);
            $rows2 = $database->loadObjectList();
            if (count($rows2)) {
                foreach ($rows2 as $item) {
                    $product_type_id = $item->product_type_id;

                    $query = "  SELECT *
								FROM #__vm_product_type_$product_type_id
								WHERE product_id='" . $value->product_id . "' ";
                    $database->setQuery($query);
                    $rows3 = $database->loadObjectList();
                    $item2 = $rows3[0];


                    $product_type_name = isset($item2->product_type_name) ? $item2->product_type_name : "";
                    $product_type_quantity = isset($item2->quantity) ? $item2->quantity : 0;
                    $product_type_price = isset($item2->price) ? $item2->price : "";

                    $query = "INSERT INTO #__vm_order_product_type( order_id,
																product_id,
																product_type_name,
																quantity, price)
								VALUES ( $order_id,
										 " . $value->product_id . "',
										 '" . addslashes($product_type_name) . "',
										 " . $product_type_quantity . ",
										 " . $product_type_price . ")";
                    $database->setQuery($query);
                    $database->query();
                    //echo "8. $query <br/>".$database->getErrorMsg()."<br/><br/>";
                }
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
            //echo "9. $query <br/>".$database->getErrorMsg()."<br/><br/>";


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
    $shopper_order_link = $mosConfig_live_site . "/order-details/?order_id=$order_id";
    $shopper_footer_html = "<br /><br />Thank you for your patronage.<br />"
        . "<br /><a title=\"View the order by following the link below.\" href=\"$shopper_order_link\">View the order by following the link below.</a>"
        . "<br /><br />Questions? Problems?<br />"
        . "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom . "\">" . $mosConfig_mailfrom . "</a><br/><b> Please Note: Orders placed for deliveries outside of Sydney, Melbourne, Brisbane and Perth may be delayed. We will contact you via email if there is an issue.</b>";

    $vendor_header = "The following order was received.";
    $vendor_order_link = $mosConfig_live_site . "/index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin";
    $vendor_footer_html = "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";

    $vendor_image = "<img src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/vendor/" . $vendor_full_image . "\" alt=\"vendor_image\" border=\"0\" />";

    /* ===================================== Assign Email Content ===================================== */
    $myFile = $mosConfig_absolute_path . "/administrator/components/com_virtuemart/html/templates/order_emails/email_english.html";
    $fh = fopen($myFile, 'r');
    $html = fread($fh, filesize($myFile));
    fclose($fh);
    //echo "10. Read Email File Errors<br/><br/>";
    $vendor_city = (isset($aVendor->vendor_city)) ? $aVendor->vendor_city : '';
    $vendor_state = (isset($aVendor->vendor_state)) ? $aVendor->vendor_state : '';
    $html = str_replace('{phpShopVendorName}', $vendor_name, $html);
    $html = str_replace('{phpShopVendorStreet1}', $vendor_phone, $html);
    $html = str_replace('{phpShopVendorStreet2}', $vendor_address_1, $html);
    $html = str_replace('{phpShopVendorZip}', $vendor_zip, $html);
    $html = str_replace('{phpShopVendorCity}', $vendor_city, $html);
    $html = str_replace('{phpShopVendorState}', $vendor_state, $html);
    $html = str_replace('{phpShopVendorImage}', $vendor_image, $html);
    $html = str_replace('{phpShopOrderHeader}', "Purchase Order", $html);
    $html = str_replace('{phpShopOrderNumber}', $order_id, $html);
    $html = str_replace('{phpShopOrderDate}', date("M d, Y", $timestamp), $html);
    $html = str_replace('{phpShopDeliveryDate}', $phpShopDeliveryDate, $html);
    $html = str_replace('{phpShopOrderStatus}', $aResult["order_payment_log"], $html);

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

    $html = str_replace('{phpShopSTCompany}', $deliver_company_name, $html);
    $html = str_replace('{phpShopSTName}', $deliver_first_name . " " . $deliver_middle_name . " " . $deliver_last_name, $html);
    $html = str_replace('{phpShopSTStreet1}', $deliver_address_1, $html);
    $html = str_replace('{phpShopSTStreet2}', $deliver_address_2, $html);
    $html = str_replace('{phpShopSTCity}', $deliver_city, $html);
    $html = str_replace('{phpShopSTState}', $deliver_state, $html);
    $html = str_replace('{phpShopSTZip}', $deliver_zip_code, $html);
    $html = str_replace('{phpShopSTCountry}', $deliver_country, $html);
    $html = str_replace('{phpShopSTPhone}', $deliver_phone, $html);
    $html = str_replace('{phpShopSTFax}', $deliver_fax, $html);
    $html = str_replace('{phpShopSTEmail}', "", $html);
    $html = str_replace('{phpShopOrderItems}', $order_items, $html);
    $html = str_replace('{phpShopLiveSite}', $mosConfig_live_site, $html);
    $query = "SELECT * FROM jos_vm_edit_email_banner";
    $database->setQuery($query);
    $ress = $database->loadObjectList();
    if ($ress) {
        $html = str_replace('{as1}', $ress[0]->href, $html);
        $html = str_replace('{as2}', $ress[1]->href, $html);
        $html = str_replace('{as3}', $ress[2]->href, $html);
    }
    $phpShopCouponDiscount = (isset($phpShopCouponDiscount)) ? $phpShopCouponDiscount : '';
    $phpShopShippingDiscount = (isset($phpShopShippingDiscount)) ? $phpShopShippingDiscount : '';
    $html = str_replace('{phpShopOrderSubtotal}', "" . LangNumberFormat::number_format($phpShopOrderSubtotal, 2, '.', ' '), $html);
    $html = str_replace('{phpShopOrderShipping}', "" . LangNumberFormat::number_format(($deliver_fee - $total_deliver_tax_fee), 2, '.', ' '), $html);
    // $html = str_replace('{phpShopOrderTax}', "" . LangNumberFormat::number_format($total_tax, 2, '.', ' '), $html);
    $html = str_replace('{phpShopOrderTotal}', "" . LangNumberFormat::number_format($total_price, 2, '.', ' '), $html);

    $html = str_replace('{phpShopCouponDiscount}', $phpShopCouponDiscount, $html);
    $html = str_replace('{phpShopShippingDiscount}', $phpShopShippingDiscount, $html);

    $html = str_replace('{phpShopOrderDisc1}', (isset($order_disc1) ? $order_disc1 : ""), $html);
    $html = str_replace('{phpShopOrderDisc2}', (isset($order_disc1) ? $order_disc2 : ""), $html);
    $html = str_replace('{phpShopOrderDisc3}', (isset($order_disc1) ? $order_disc3 : ""), $html);
    $html = str_replace('{phpShopCustomerNote}', htmlspecialchars(strip_tags($card_msg)), $html);
    $html = str_replace('{phpShopCustomerSignature}', htmlspecialchars(strip_tags($signature)), $html);
    $html = str_replace('{phpShopCustomerInstructions}', htmlspecialchars(strip_tags($card_comment)), $html);

    $html = str_replace('{PAYMENT_INFO_LBL}', "Payment Information", $html);
    $html = str_replace('{PAYMENT_INFO_DETAILS}', $payment_info_details, $html);
    $html = str_replace('{SHIPPING_INFO_LBL}', "Delivery Information", $html);
    $aShippingMethod[1] = "$" . number_format(($deliver_fee - $total_deliver_tax_fee), 2, '.', ' ');
    $html = str_replace('{SHIPPING_INFO_DETAILS}', $aShippingMethod[0] . " (" . $aShippingMethod[1] . ")", $html);

    $shopper_html = str_replace('{phpShopOrderHeaderMsg}', $shopper_header, $html);
    $shopper_html = str_replace('{phpShopOrderClosingMsg}', $shopper_footer_html, $shopper_html);

    $shopper_subject = $vendor_name . " Purchase Order - " . $order_id;

    mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);
    //echo $mosConfig_mailfrom."<br>".$mosConfig_fromname."<br><br><br><br><br><br>=============================".$account_email."<br><br><br><br><br>=============================".$shopper_subject."<br>".$shopper_html."<br><br>================<br><br>".$html;
    ///echo("=============================".$isSend);

    /* ===================================== Assign Order To The WareHouse ===================================== */

    $while = 4;
    $need_zip_code = $deliver_zip_code;

    while ($while > 0) {
        $query = "SELECT WH.warehouse_email,"
            . " WH.warehouse_code FROM #__vm_warehouse AS WH,"
            . " #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.published=1 AND PWH.postal_code LIKE '" . $need_zip_code . "'";
        $database->setQuery($query);
        $oWarehouse = $database->loadObjectList();
        if (count($oWarehouse)) {
            $oWarehouse = $oWarehouse[0];
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
            $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "',color='black', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        } else {

            $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        }
        $database->setQuery($query);
        $database->query();

        if ($warehouse_code) {
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
            $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);

            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
        }
    } else {
        $query = "UPDATE #__vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        $database->setQuery($query);
        $database->query();
    }


    /*
      $query = "SELECT WH.warehouse_email, WH.warehouse_code FROM #__vm_warehouse AS WH, #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code LIKE '" . $deliver_zip_code . "'";
      $database->setQuery($query);
      $oWarehouse = $database->loadObjectList();

      if (count($oWarehouse)) {
      $oWarehouse = $oWarehouse[0];
      $warehouse_code = $oWarehouse->warehouse_code;
      $warehouse_email = $oWarehouse->warehouse_email;

      $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
      $database->setQuery($query);
      $database->query();

      if ($warehouse_code) {
      $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
      $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
      //echo "<br/><br/>".$mail_Content."<br/><br/>".$mail_Subject."<br/><br/>".$mosConfig_mailfrom."<br/><br/>".$mosConfig_fromname."<br/><br/>".$warehouse_email."<br/><br/>";
      mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
      }
      } else {
      $query = "UPDATE #__vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
      $database->setQuery($query);
      $database->query();
      }
     */
    $sql = "SELECT COUNT(*) FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'VC-01'";
    $database->setQuery($sql);
    $vc_rows = $database->loadResult();
    $sql = "SELECT COUNT(*) FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'PC-01'";
    $database->setQuery($sql);
    $pc_rows = $database->loadResult();

    $sVCCouponCode = "";
    if ($vc_rows) {
        $sVCCouponCode = createCouponName("VC-"); //"VC-" . strtoupper(genRandomString(8));
    }

    $sPCCouponCode = "";
    if ($pc_rows) {
        $sPCCouponCode = createCouponName("PC-"); //"PC-" . strtoupper(genRandomString(8));
    }

    if (strlen($sVCCouponCode) > 0 || strlen($sPCCouponCode) > 0) {
        $userPCVC = (strlen($sVCCouponCode) > 0) ? $sVCCouponCode : $sPCCouponCode;
        $sql = "INSERT INTO #__vm_coupons_user(user_id,coupon_code)  VALUES('$user_id','$userPCVC')";
        $database->setQuery($sql);
        $database->query();
    }

    if ($sVCCouponCode != "") {
        $sql = "INSERT INTO #__vm_coupons(coupon_code, percent_or_total , coupon_type, coupon_value, create_date, edit_date, username )
						VALUES('$sVCCouponCode', 'total', 'gift', '20.00', NOW(), NOW(), 'auto')";
        $database->setQuery($sql);
        $database->query();

        $shopper_subject = "Your Bloomex $20.00 voucher code";
        $shopper_html = "Dear $bill_first_name,<br/><br/>
							Thank you for your purchase.  Your Bloomex $20.00 voucher code is <b>$sVCCouponCode</b><br/><br/>
							Call or order online at your convenience.<br/><br/>
							Best Regards,<br/><br/>
							Jessica<br/>
							Bloomex Inc<br/>
							866 912 5666<br/><br/>
							<img src='$mosConfig_live_site/templates/bloomex7/images/coupon_logo.png' />";
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);
    }

    if ($sPCCouponCode != "") {
        $sql = "INSERT INTO #__vm_coupons(coupon_code, percent_or_total , coupon_type, coupon_value, create_date, edit_date, username )
						VALUES('$sPCCouponCode', 'total', 'permanent', '14.99', NOW(), NOW(), 'auto')";
        $database->setQuery($sql);
        $database->query();

        $shopper_subject = "Your Bloomex Platinum Club Membership";
        $shopper_html = "Dear $bill_first_name,<br/><br/>
							Congratulations on becoming a Bloomex Platinum Club Member. You will now receive Free Regular Shipping on all your orders by logging in under this email address.
                            Call or order online at your convenience.<br/><br/>
							Best Regards,<br/>
							Bloomex Australia<br/>
							1800 451 637<br/><br/>
							<img src='http://media.bloomex.ca/coupon_logo.png' />";
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);
    }

    if ($deliver_state == "WA") {

        $query_upt = "UPDATE #__vm_orders set `warehouse`='p01' WHERE order_id=" . $order_id;
        $database->setQuery($query_upt);
        $database->query();
    }

    die("success[--1--]{$order_id}[--1--]{$payment_msg}");
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

/*
 * name: process_payment()
 * created by: durian
 * description: process transaction with Pay Flow Pro
 * parameters:
 * 	$order_number, the number of the order we're processing here
 * $order_total, the total $ of the order
 * returns:
 */

function process_payment($order_number, $order_total, $PaymentVar, &$aResult)
{
    global $vendor_mail, $vendor_currency, $database;
    $VM_LANG = new vmLanguage();
    $vmLogger = new vmLog();
    $order_total = round($order_total, 2);

    $ps_vendor_id = $PaymentVar["vendor_id"];
    $auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : "";

    $query = "SELECT * FROM #__shopper_group WHERE vendor_id = {$ps_vendor_id}";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $shopper_group_id = 0;
    if (count($rows)) {
        $aShopperGroup = $rows[0];
        $shopper_group_id = $aShopperGroup->shopper_group_id;
    }

    /*     * * Get the Configuration File for Pay Flow Pro ** */
    if ($vendor_currency == "USD") {
        require_once(CLASSPATH . "payment/ps_pfp2usa.cfg.php");
    } else {
        require_once(CLASSPATH . "payment/ps_pfp2.cfg.php");
    }

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
        $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_INTERNAL_ERROR . ': ' . $response['RESPMSG'];
        /* record transaction ID */
        $aResult["order_payment_trans_id"] = $response['PNREF'];

        $aResult["approved"] = 0;

        return False;
    }
}

function exportAddressBookXML()
{
    global $database, $mosConfig_absolute_path;
    $orderID = mosGetParam($_POST, "order_id", "");


    //======================================================================================
    $query = "SELECT * from #__vm_order_user_info WHERE order_id IN ({$orderID}) AND address_type = 'ST'";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    /* echo $query;
      print_r($oOrderInfo); */

    $sHeader = "CustID,Name,Address1,Address2,Address3,City,Prov,PostalCode,Attention,PhoneNumber,EMail,Reference,CostCentre\r\n";
    $sData = "";
    if (count($oOrderInfo)) {
        foreach ($oOrderInfo as $value) {
            $CustID = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->order_id, ENT_COMPAT, "UTF-8"))));
            $Name = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->first_name . " " . $value->last_name . " " . $value->middle_name, ENT_COMPAT, "UTF-8"))));
            $Address1 = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->address_1, ENT_COMPAT, "UTF-8"))));
            $Address2 = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->address_2, ENT_COMPAT, "UTF-8"))));
            $Address3 = "";
            $City = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->city, ENT_COMPAT, "UTF-8"))));
            $Prov = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->state, ENT_COMPAT, "UTF-8"))));
            $PostalCode = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->zip, ENT_COMPAT, "UTF-8"))));
            $Attention = "";
            $PhoneNumber = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->phone_1, ENT_COMPAT, "UTF-8"))));
            $EMail = str_replace("\015\012", ' ', str_replace(',', ' ', rtrim(htmlentities($value->user_email, ENT_COMPAT, "UTF-8"))));
            $Reference = "";
            $CostCentre = "";

            $sData .= "{$CustID},{$Name},{$Address1},{$Address2},{$Address3},{$City},{$Prov},{$PostalCode},{$Attention},{$PhoneNumber},{$EMail},{$Reference},{$CostCentre}\r\n";
        }
    }

    $sPath = "{$mosConfig_absolute_path}/media/addressbook/addressbook_" . date("m_d_Y_H_i_s") . ".csv";
    $sPath2 = "{$mosConfig_live_site}/media/addressbook/addressbook_" . date("m_d_Y_H_i_s") . ".csv";

    $fp = fopen($sPath, 'w');
    fwrite($fp, $sHeader . $sData);
    fclose($fp);

    if (is_file($sPath)) {
        echo $sPath2;
    } else {
        echo "error";
    }
    endit(0);
}

function loadOrderItemDetail($action)
{
    global $database;
    $orderID = intval(mosGetParam($_POST, "order_id", 0));
    $aInfomation = array();

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }

    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    $aInfomation["OrderInfo"] = $oOrderInfo[0];

    //======================================================================================
    $query = " SELECT * FROM #__vm_order_item WHERE order_id = {$orderID}";
    $database->setQuery($query);
    $aInfomation["OrderItem"] = $database->loadObjectList();

    if ($action == "order") {
        HTML_AjaxOrder::loadOrderItemDetail($option, $aInfomation);
    } elseif ($action == "cart") {
        HTML_AjaxOrder::loadOrderCart($option, $aInfomation);
    }
}

function validCart()
{
    global $database;
    $product_number_items = intval(mosGetParam($_REQUEST, "product_number_items", 0));
    $just_change = mosGetParam($_COOKIE, 'just_change', '');

    if (!empty($just_change) && $just_change > 0) {
        echo "reload";
    } else {
        echo "";
    }

    @setcookie("just_change", "", time() - 3600, '/; SameSite=Strict',"",true);
    endit(0);
}

function copyUserBillingAddress()
{
    global $database, $my;

    $return = array();
    $return['result'] = false;

    if ((int)$my->id > 0) {
        $query = "SELECT 
            `user_id`, 
            `address_type_name`, 
            `address_type2`, 
            `first_name`, 
            `last_name`, 
            `middle_name`, 
            `company`, 
            `address_1`, 
            `address_2`, 
            `city`, 
            `zip`, 
            `country`, 
            `state`, 
            `phone_1`, 
            `phone_2`, 
            `fax`, `title`, 
            `user_email`, 
            `suite`, 
            `street_number`,
            `street_name` 
        FROM `jos_vm_user_info` 
        WHERE 
            `user_id`=" . (int)$my->id . "
        AND 
            `address_type`='BT'";

        $database->setQuery($query);
        $billingObj = false;
        $database->loadObject($billingObj);

        if ($billingObj) {
            $return['result'] = true;
            $return['billingObj'] = $billingObj;
        }
    }

    echo json_encode($return);
    exit;
}

function getUserAddress()
{
    global $database, $my;

    $user_info_id = mosGetParam($_POST, 'user_info_id', '');

    $return = array();
    $return['result'] = false;

    if ((int)$my->id > 0) {
        $query = "SELECT 
            `user_id`, 
            `user_info_id`, 
            `address_type_name`, 
            `address_type2`, 
            `first_name`, 
            `last_name`, 
            `middle_name`, 
            `company`, 
            `address_1`, 
            `address_2`, 
            `city`, 
            `zip`, 
            `country`, 
            `state`, 
            `phone_1`, 
            `phone_2`, 
            `fax`, `title`, 
            `user_email`, 
            `suite`, 
            `street_number`,
            `street_name` 
        FROM `jos_vm_user_info` 
        WHERE 
            `user_id`=" . (int)$my->id . "
        AND 
            `user_info_id`='" . $database->getEscaped($user_info_id) . "'
        AND 
            `address_type`='ST'";

        $database->setQuery($query);
        $shipping_obj = false;
        $database->loadObject($shipping_obj);

        if ($shipping_obj) {
            $return['result'] = true;
            $return['shipping_obj'] = $shipping_obj;
        }
    }

    echo json_encode($return);
    exit;
}

function checkDeliveryDateAvailability($ddate)
{
    global $database;
    $user_info_id = isset($_SESSION['checkout_ajax']['user_info_id']) ? $database->getEscaped($_SESSION['checkout_ajax']['user_info_id']) : '';

    $query = "SELECT `zip`,`state`
            FROM `jos_vm_user_info` AS `ui` 
            WHERE `ui`.`user_info_id`='" . $user_info_id . "' AND `ui`.`address_type`='ST'";

    $zip_obj = false;
    $database->setQuery($query);
    $database->loadObject($zip_obj);

    $timezones = array(
        'AT' => 'Australia/Sydney',
        'NW' => 'Australia/Sydney',
        'NT' => 'Australia/Darwin',
        'QL' => 'Pacific/Guam',
        'SA' => 'Australia/Adelaide',
        'TA' => 'Australia/Hobart',
        'VI' => 'Australia/Melbourne',
        'WA' => 'Australia/Perth'
    );
    if (array_key_exists($zip_obj->state, $timezones)) {
        date_default_timezone_set($timezones[$zip_obj->state]);
    } else {
        date_default_timezone_set('Australia/Sydney');
    }

    $date = date("Y-m-d", strtotime($ddate));
    $cutoffhour = 13;
    //we still allow 10 minutes to finsh the order
    if (($date < date("Y-m-d")) || (($date == date("Y-m-d")) && ((date('G') * 60 + date('i')) > $cutoffhour * 60 + 10))) {
        //cut off
        return checkDeliveryDateAvailability(date('d-m-Y', strtotime('+1 day', strtotime($date))));
    } else {
        $query = "SELECT calendar_day FROM `tbl_delivery_options` WHERE `calendar_day`='$date' and type='unavaliable'";
        $database->setQuery($query);
        $unavailable = false;
        $database->loadObject($unavailable);
        if ($unavailable) {
            return checkDeliveryDateAvailability(date('d-m-Y', strtotime('+1 day', strtotime($date))));
        }
    }
    date_default_timezone_set('Australia/Sydney'); // get date back
    return $ddate;
}

function setCardMsgAndOccasion()
{
    global $database, $my, $mosConfig_mailfrom, $mosConfig_fromname;
    date_default_timezone_set('Australia/Sydney');
    $customer_occasion = mosGetParam($_REQUEST, 'customer_occasion', "");
    $card_msg = mosGetParam($_REQUEST, 'card_msg', "");
    $signature = mosGetParam($_REQUEST, 'signature', "");
    $card_comment = mosGetParam($_REQUEST, 'card_comment', "");

    $deliver_type = mosGetParam($_REQUEST, "address_type", "");
    $deliver_company_name = mosGetParam($_REQUEST, "shipping_info_company", "");
    $deliver_first_name = mosGetParam($_REQUEST, "shipping_info_first_name", "");
    $deliver_last_name = mosGetParam($_REQUEST, "shipping_info_last_name", "");
    $deliver_phone_1 = mosGetParam($_REQUEST, "shipping_info_phone_1", "");
    $deliver_suite = mosGetParam($_REQUEST, "shipping_info_suite", "");
    $deliver_street_number = mosGetParam($_REQUEST, "shipping_info_street_number", "");
    $deliver_street_name = mosGetParam($_REQUEST, "shipping_info_street_name", "");
    $deliver_city = mosGetParam($_REQUEST, "shipping_info_city", "");
    $deliver_email = mosGetParam($_REQUEST, "shipping_info_user_email", "");
    $deliver_zip = mosGetParam($_REQUEST, "shipping_info_zip", "");
    $deliver_state = mosGetParam($_REQUEST, "shipping_info_state", "");
    $delivery_date = mosGetParam($_REQUEST, "shipping_info_delivery_date", "");
    $address_type2  = ($deliver_company_name != '') ? 'Business' : '';

    $query = "SELECT o.ddate,o.order_status,i.user_email ,o.warehouse,po.id as order_set_pending_status
            FROM `jos_vm_orders` as o 
            left join jos_vm_order_user_info as i on i.order_id=o.order_id and i.address_type='BT'
            left join jos_vm_cards_for_pending_orders as po on po.order_id=o.order_id 
            WHERE o.`order_id`=" . $_SESSION['checkout_ajax']['thankyou_order_id']." ";

    $database->setQuery($query);
    $order_obj = false;
    $database->loadObject($order_obj);

    $orderStatus = 'A';
    $comment = 'Pending Delivery Address -> Paid';
    if($order_obj->order_set_pending_status) {
        $orderStatus = "P";
        $comment = 'Pending Delivery Address -> Pending';
    }
    $warehouse_code = $order_obj->warehouse;
    if (isset($order_obj) && isset($order_obj->order_status) && in_array($order_obj->order_status,['X','P'])) {
        $orderStatus = $order_obj->order_status;
        $comment = '';
    }

    $wh_obj = false;
    $zip_symbols = 4;
    while (($wh_obj == false) AND ($zip_symbols > 0)) {
        $query = "SELECT 
                            `wh`.`warehouse_email`,
                            `wh`.`warehouse_code`,
                            `pwh`.`out_of_town`
                        FROM `jos_postcode_warehouse` AS `pwh` 
                        LEFT JOIN `jos_vm_warehouse` AS `wh` ON `wh`.`warehouse_id`=`pwh`.`warehouse_id` 
                        WHERE `pwh`.country = 'AUS' and 
                            `pwh`.`postal_code` LIKE '" . substr($deliver_zip, 0, $zip_symbols) . "'
                        ";

        $database->setQuery($query);
        $wh_obj = false;
        $database->loadObject($wh_obj);
        if ($wh_obj) {
            $warehouse_code = $wh_obj->warehouse_code;
            break;
        }
        $zip_symbols--;
    }


    $ddate = checkDeliveryDateAvailability($delivery_date ? $delivery_date : $order_obj->ddate);

    $query = 'UPDATE jos_vm_orders SET 
            customer_note="' . $database->getEscaped($card_msg) . '",
            customer_signature="' . $database->getEscaped($signature) . '",
            customer_occasion="' . $database->getEscaped($customer_occasion) . '",
            customer_comments="' . $database->getEscaped($card_comment) . '",
            order_status="' . $orderStatus . '",
            warehouse="' . $warehouse_code . '",
            ddate="' . $ddate . '" 
            WHERE order_id=' . $_SESSION['checkout_ajax']['thankyou_order_id'];
    $database->setQuery($query);
    $database->query();
    
    $mysqlDatetime = date('Y-m-d G:i:s', time());
    $query = "INSERT INTO `jos_vm_order_history`
                    (	
                        `order_id`,
                        `order_status_code`,
                        `date_added`,
                        `comments`, 
                        `user_name`,
                        `warehouse`
                    ) 
                    VALUES (
                        '" . $_SESSION['checkout_ajax']['thankyou_order_id'] . "', 
                        '" . $orderStatus . "', 
                        '" . $mysqlDatetime . "', 
                        '" . $database->getEscaped($comment) . "', 
                        '" . $database->getEscaped($my->username) . "',
                        '" . $warehouse_code . "'
                    )";
    $database->setQuery($query);
    $database->query();
    if ($ddate != $order_obj->ddate && $order_obj->ddate != '') {
        $query = "INSERT INTO `jos_vm_order_history`
                    (	
                        `order_id`,
                        `order_status_code`,
                        `date_added`,
                        `customer_notified`,
                        `comments`, 
                        `user_name`,
                        `warehouse`
                    ) 
                    VALUES (
                        '" . $_SESSION['checkout_ajax']['thankyou_order_id'] . "', 
                        '" . $orderStatus . "', 
                        '" . $mysqlDatetime . "', 
                        '1', 
                        'Same day delivery cutoff passed, delivery date changed from " . $order_obj->ddate . " to " . $ddate . "',
                        'System',
                        '" . $warehouse_code . "'
                    )";
        $database->setQuery($query);
        $database->query();
        $notifySubject = 'Same day delivery cutoff passed';
        $notifyText = "Same day delivery cutoff passed, delivery date changed from " . $order_obj->ddate . " to " . $ddate;
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $order_obj->user_email, $notifySubject, $notifyText, 1);
    }
    $query = " UPDATE jos_vm_order_user_info
                    SET company		= '{$database->getEscaped($deliver_company_name)}',
                        title		= '{$database->getEscaped($deliver_type)}',
                        last_name	= '{$database->getEscaped($deliver_last_name)}',
                        first_name	= '{$database->getEscaped($deliver_first_name)}',
                        phone_1		= '{$database->getEscaped($deliver_phone_1)}',
                        city		= '{$database->getEscaped($deliver_city)}',
                        user_email	= '{$database->getEscaped($deliver_email)}',
                        address_type2	= '{$database->getEscaped($address_type2)}',
                        suite	= '{$database->getEscaped($deliver_suite)}',
                        street_number	= '{$database->getEscaped($deliver_street_number)}',
                        street_name	= '{$database->getEscaped($deliver_street_name)}',
                        state	= '{$database->getEscaped($deliver_state)}',
                        zip	= '{$database->getEscaped($deliver_zip)}'

                    WHERE order_id = '" . $_SESSION['checkout_ajax']['thankyou_order_id'] . "' and address_type='ST' ";
    $database->setQuery($query);
    $database->query();

    if($_SESSION['checkout_ajax']['user_info_id']){
        $query_update = "UPDATE #__vm_user_info SET  
                    address_type_name='-default-',
                    address_type2='',
                    last_name='{$deliver_last_name}',
                    first_name='{$deliver_first_name}',
                    phone_1='{$deliver_phone_1}',
                    city='{$deliver_city}',
                    state='{$deliver_state}',
                    zip='{$deliver_zip}',
                    user_email='{$deliver_email}',
                    suite='{$deliver_suite}',
                    street_number='{$deliver_street_number}',
                    street_name='{$deliver_street_name}'  WHERE user_info_id ='{$_SESSION['checkout_ajax']['user_info_id']}'";

        $database->setQuery($query_update);
        $database->query();
    }



    exit(json_encode(['result' => true]));
}

function getOrSetShippingInfoId()
{
    global $database, $my;

    $state = mosGetParam($_REQUEST, 'state', false);


    $return['result'] = false;

    $query = "SELECT user_info_id
            FROM `jos_vm_user_info`
            WHERE `user_id`='" . (int)$my->id . "'
             AND state='" . $database->getEscaped($state) . "' 
             AND `address_type`='ST' 
             ";
    $database->setQuery($query);
    $user_info_obj = false;
    $database->loadObject($user_info_obj);

    if ($user_info_obj) {
        $return['result'] = true;
        $_SESSION['checkout_ajax']['user_info_id'] = $user_info_obj->user_info_id;
    } else {

        $user_info_id = md5($my->id . time());
        $query = "INSERT INTO
                `jos_vm_user_info` (
                    `user_id`,
                    `user_info_id`,
                    `address_type`,
                    `zip`,
                    `country`,
                    `state`,
                    `cdate`,
                    `mdate`
                )
                VALUES (
                    '" . $my->id . "',
                    '" . $user_info_id . "',
                    'ST',
                    '',
                    'AUS',
                    '" . $database->getEscaped($state) . "',
                    " . time() . ",
                    " . time() . "
                )";
        $database->setQuery($query);
        if ($database->query()) {
            $return['result'] = true;
            $_SESSION['checkout_ajax']['user_info_id'] = $user_info_id;
        } else {
            $return['error'] = 'insert error';
        }
    }

    exit(json_encode($return));
}

function getValidCalendarDates()
{
    global $database,$my;

    $state = mosGetParam($_REQUEST, 'state', false);
    $zip = mosGetParam($_REQUEST, 'zip', false);

    $return['result'] = false;

    $query = "SELECT user_info_id
            FROM `jos_vm_user_info`
            WHERE `user_id`='" . (int)$my->id . "'
             AND state='" . $database->getEscaped($state) . "' 
             AND zip='" . $database->getEscaped($zip) . "' 
             AND `address_type`='ST' 
             ";
    $database->setQuery($query);
    $user_info_obj = false;
    $database->loadObject($user_info_obj);

    if ($user_info_obj) {
        $_SESSION['checkout_ajax']['user_info_id'] = $user_info_obj->user_info_id;
    } elseif($zip && $my->id && $state) {
        $user_info_id = md5($my->id . time());
        $query = "INSERT INTO
                `jos_vm_user_info` (
                    `user_id`,
                    `user_info_id`,
                    `address_type`,
                    `zip`,
                    `country`,
                    `state`,
                    `cdate`,
                    `mdate`
                )
                VALUES (
                    '" . $my->id . "',
                    '" . $user_info_id . "',
                    'ST',
                    '" . $database->getEscaped($zip) . "',
                    'AUS',
                    '" . $database->getEscaped($state) . "',
                    " . time() . ",
                    " . time() . "
                )";
        $database->setQuery($query);
        if ($database->query()) {
            $_SESSION['checkout_ajax']['user_info_id'] = $user_info_id;
        }
    }
    require_once CLASSPATH.'ps_for_checkout.php';
    $ps_for_checkout = new ps_for_checkout;
    $return =  $ps_for_checkout->getDeliveryCalendar();
    exit(json_encode($return));
}

function UpdateAddress()
{
    global $database, $my;

    $return = array();
    $return['result'] = false;

    $address_a = array();
    $address_a['address_type'] = '';
    $address_a['address_type2'] = '';
    $address_a['company'] = '';
    $address_a['title'] = '';
    $address_a['first_name'] = '';
    $address_a['last_name'] = '';
    $address_a['middle_name'] = '';
    $address_a['suite'] = '';
    $address_a['street_number'] = '';
    $address_a['street_name'] = '';
    $address_a['city'] = '';
    $address_a['zip'] = '';
    $address_a['country'] = '';
    $address_a['state'] = '';
    $address_a['phone_1'] = '';
    $address_a['phone_2'] = '';
    $address_a['fax'] = '';
    $address_a['user_email'] = '';

    foreach ($_POST as $k => $v) {
        $k = str_replace(array('billing_info_', 'shipping_info_'), '', $k);

        $address_a[$k] = $v;
    }

    $user_id = (int)$my->id;

    if ($user_id > 0) {
        if (empty($address_a['user_info_id'])) {
            $user_info_id = md5($user_id . time());

            $query = "INSERT INTO 
                `jos_vm_user_info` (
                    `user_id`,
                    `user_info_id`,
                    `address_type`,
                    `address_type2`,
                    `company`,
                    `title`,
                    `first_name`,
                    `last_name`,
                    `middle_name`,
                    `suite`,
                    `street_number`,
                    `street_name`,
                    `city`,
                    `zip`,
                    `country`,
                    `state`,
                    `phone_1`,
                    `phone_2`,
                    `fax`,
                    `user_email`,
                    `cdate`,
                    `mdate`
                )
                VALUES (
                    " . $user_id . ",
                    '" . $user_info_id . "',
                    '" . $database->getEscaped($address_a['address_type']) . "',
                    '" . $database->getEscaped($address_a['address_type2']) . "',
                    '" . $database->getEscaped($address_a['company']) . "',
                    '" . $database->getEscaped($address_a['title']) . "',
                    '" . $database->getEscaped($address_a['first_name']) . "',
                    '" . $database->getEscaped($address_a['last_name']) . "',
                    '" . $database->getEscaped($address_a['middle_name']) . "',
                    '" . $database->getEscaped($address_a['suite']) . "',
                    '" . $database->getEscaped($address_a['street_number']) . "',
                    '" . $database->getEscaped($address_a['street_name']) . "',
                    '" . $database->getEscaped($address_a['city']) . "',
                    '" . $database->getEscaped($address_a['zip']) . "',
                    '" . $database->getEscaped($address_a['country']) . "',
                    '" . $database->getEscaped($address_a['state']) . "',
                    '" . $database->getEscaped($address_a['phone_1']) . "',
                    '" . $database->getEscaped($address_a['phone_2']) . "',
                    '" . $database->getEscaped($address_a['fax']) . "',
                    '" . $database->getEscaped($address_a['user_email']) . "',
                    '" . time() . "',
                    '" . time() . "'
                )
                ";

            $database->setQuery($query);
            if ($database->query()) {

                $query = "SELECT 
                        `company`,
                        CONCAT(`first_name`, ' ', `middle_name`, ' ', `last_name`) as 'full_name',
                        CONCAT(`suite`, ' ', `street_number`, ' ', `street_name`, ', ', `zip`, ', ', `state`, ', ', `country`) as 'address',
                        `phone_1`,
                        `fax`,
                        `user_email`
                    FROM `jos_vm_user_info`
                    WHERE 
                        `user_id`=" . $user_id . "
                    AND
                        `user_info_id`='" . $user_info_id . "'
                    AND 
                        `address_type`='" . $database->getEscaped($address_a['address_type']) . "'
                    ";

                $database->setQuery($query);
                $user_info_obj = false;
                $database->loadObject($user_info_obj);

                $return['result'] = true;
                $return['user_info_obj'] = $user_info_obj;
            } else {
                $return['error'] = 'DB error.';
            }
        } else {
            $query = "SELECT 
                `user_id`,
                `user_info_id`,
                `address_type`
            FROM `jos_vm_user_info`
            WHERE 
                `user_id`=" . $user_id . "
            AND
                `user_info_id`='" . $database->getEscaped($address_a['user_info_id']) . "'
            AND 
                `address_type`='" . $database->getEscaped($address_a['address_type']) . "'
            ";

            $database->setQuery($query);
            $user_info_obj = false;
            $database->loadObject($user_info_obj);

            if ($user_info_obj) {
                $query = "UPDATE 
                `jos_vm_user_info` SET
                    `company`='" . $database->getEscaped($address_a['company']) . "',
                    `title`='" . $database->getEscaped($address_a['title']) . "',
                    `first_name`='" . $database->getEscaped($address_a['first_name']) . "',
                    `last_name`='" . $database->getEscaped($address_a['last_name']) . "',
                    `middle_name`='" . $database->getEscaped($address_a['middle_name']) . "',
                    `address_type2`='" . $database->getEscaped($address_a['address_type2']) . "',
                    `suite`='" . $database->getEscaped($address_a['suite']) . "',
                    `street_number`='" . $database->getEscaped($address_a['street_number']) . "',
                    `street_name`='" . $database->getEscaped($address_a['street_name']) . "',
                    `country`='" . $database->getEscaped($address_a['country']) . "',
                    `state`='" . $database->getEscaped($address_a['state']) . "',
                    `phone_1`='" . $database->getEscaped($address_a['phone_1']) . "',
                    `phone_2`='" . $database->getEscaped($address_a['phone_2']) . "',
                    `fax`='" . $database->getEscaped($address_a['fax']) . "',
                    `user_email`='" . $database->getEscaped($address_a['user_email']) . "',
                    `mdate`='" . time() . "'
                WHERE 
                    `user_id`=" . (int)$user_info_obj->user_id . "
                AND
                    `user_info_id`='" . $database->getEscaped($user_info_obj->user_info_id) . "'
                AND 
                    `address_type`='" . $database->getEscaped($user_info_obj->address_type) . "'
                ";

                $database->setQuery($query);
                if ($database->query()) {
                    $query = "SELECT 
                        `company`,
                        CONCAT(`first_name`, ' ', `middle_name`, ' ', `last_name`) as 'full_name',
                        CONCAT(`suite`, ' ', `street_number`, ' ', `street_name`, ', ', `city`, ', ', `state`, ', ', `country`) as 'address',
                        `phone_1`,
                        `fax`,
                        `user_email`
                    FROM `jos_vm_user_info`
                    WHERE 
                        `user_id`=" . (int)$user_info_obj->user_id . "
                    AND
                        `user_info_id`='" . $database->getEscaped($user_info_obj->user_info_id) . "'
                    AND 
                        `address_type`='" . $database->getEscaped($user_info_obj->address_type) . "'
                    ";

                    $database->setQuery($query);
                    $user_info_obj = false;
                    $database->loadObject($user_info_obj);

                    $return['result'] = true;
                    $return['user_info_obj'] = $user_info_obj;
                } else {
                    $return['error'] = 'DB error.';
                }
            } else {
                $return['error'] = 'Hack.';
            }
        }
    } else {
        $return['error'] = 'User not found.';
        if ($_POST['checkoutStep'] && $_POST['order_id'] && $_POST['shipping_info_user_info_id']) {
            $cache = str_rot13($_POST['order_id'] . ';' . $_POST['shipping_info_user_info_id']);
            $return['redirect'] = '/checkout/2/' . $cache;
        }
    }

    echo json_encode($return);

    exit;
}

function RemoveAddress()
{
    global $database, $my;

    $return = array();
    $return['result'] = false;

    $user_id = (int)$my->id;

    if ($user_id > 0) {
        $query = "SELECT 
            `user_id`,
            `user_info_id`,
            `address_type`
        FROM `jos_vm_user_info`
        WHERE 
            `user_id`=" . $user_id . "
        AND
            `user_info_id`='" . $database->getEscaped($_POST['user_info_id']) . "'
        AND 
            `address_type`='ST'
        ";

        $database->setQuery($query);
        $user_info_obj = false;
        $database->loadObject($user_info_obj);

        if ($user_info_obj) {
            $query = "DELETE FROM 
            `jos_vm_user_info` 
            WHERE 
                `user_id`=" . (int)$user_info_obj->user_id . "
            AND
                `user_info_id`='" . $database->getEscaped($user_info_obj->user_info_id) . "'
            AND 
                `address_type`='" . $database->getEscaped($user_info_obj->address_type) . "'
            ";

            $database->setQuery($query);
            if ($database->query()) {
                $query = "SELECT 
                    `company`,
                    CONCAT(`first_name`, ' ', `middle_name`, ' ', `last_name`) as 'full_name',
                    CONCAT(`suite`, ' ', `street_number`, ' ', `street_name`, ', ', `zip`, ', ', `state`, ', ', `country`) as 'address',
                    `phone_1`,
                    `fax`,
                    `user_email`
                FROM `jos_vm_user_info`
                WHERE 
                    `user_id`=" . (int)$user_info_obj->user_id . "
                AND
                    `user_info_id`='" . $database->getEscaped($user_info_obj->user_info_id) . "'
                AND 
                    `address_type`='" . $database->getEscaped($user_info_obj->address_type) . "'
                ";

                $database->setQuery($query);
                $user_info_obj = false;
                $database->loadObject($user_info_obj);

                $return['result'] = true;
                $return['user_info_obj'] = $user_info_obj;
            } else {
                $return['error'] = 'DB error.';
            }
        } else {
            $return['error'] = 'Hack.';
        }
    } else {
        $return['error'] = 'User not found.';
    }

    echo json_encode($return);

    exit;
}

function getUserBillingAddress()
{
    global $database;
    $user_id = intval(mosGetParam($_REQUEST, "user_id", 0));
    $user_info_id = mosGetParam($_POST, "user_info_id", "");

    $query = "	SELECT user_id, user_info_id, address_type_name, first_name, last_name, middle_name, company, address_1, address_2, city, zip, country, state, phone_1, phone_2, fax, title, user_email, suite, street_number,street_name
				FROM #__vm_user_info WHERE user_id = " . $user_id . " AND user_info_id = '" . $user_info_id . "' AND address_type = 'BT' ";
    $database->setQuery($query);
    $row = $database->loadRow();

    if (count($row)) {
        $sData = implode("[--2--]", $row);
    }

    echo "success[--1--]" . $sData;
    endit(0);
}

function deleteOrderItem()
{
    global $database, $mosConfig_offset;
    $ItemID = mosGetParam($_POST, "item_id", "");
    $aItemID = explode("[----]", $ItemID);
    $orderItemID = intval($aItemID[0]);
    $orderID = intval($aItemID[1]);
    $order_item_quantity = 0;


    $query = "SELECT product_id, product_quantity, product_final_price, product_item_price, product_final_price - product_item_price AS item_tax "
        . "FROM #__vm_order_item WHERE order_id = '" . $orderID . "' "
        . "AND order_item_id = '" . addslashes($orderItemID) . "'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderItemInfo = $rows[0];

    $product_id = $oOrderItemInfo->product_id;
    $diff = $order_item_quantity - $oOrderItemInfo->product_quantity;
    $price_change = $diff * $oOrderItemInfo->product_final_price;
    $tax_change = $diff * $oOrderItemInfo->item_tax;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    //update rate
    $query = "SELECT order_total "
        . "FROM #__vm_orders WHERE order_id = '" . $orderID . "' ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderTotal = $rows[0];
    if ($oOrderTotal >= 100) {
        if ((($oOrderTotal + $price_change) >= 60) && (($oOrderTotal + $price_change) < 100)) {
            $query = "UPDATE #__users SET rate=rate-1 WHERE id = '{$user_id}' and rate<10";
            $database->setQuery($query);
            $database->query();
        }
        if (($oOrderTotal + $price_change) < 60) {
            $query = "UPDATE #__users SET rate=rate-2 WHERE id = '{$user_id}' and rate<9";
            $database->setQuery($query);
            $database->query();
        }
    }
    if ($oOrderTotal >= 60) {
        if (($oOrderTotal + $price_change) >= 100) {
            $query = "UPDATE #__users SET rate=rate+1 WHERE id = '{$user_id}' and rate<10";
            $database->setQuery($query);
            $database->query();
        }
        if (($oOrderTotal + $price_change) < 60) {
            $query = "UPDATE #__users SET rate=rate-1 WHERE id = '{$user_id}' and rate<10";
            $database->setQuery($query);
            $database->query();
        }
    }

    // Update order
    $query = "UPDATE #__vm_orders "
        . "SET order_tax = (order_tax + " . $tax_change . " ), "
        . "order_total = (order_total + " . $price_change . " ), "
        . "order_subtotal = (order_subtotal + " . $price_change . ") "
        . "WHERE order_id = '" . $orderID . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }


    $query = "DELETE FROM #__vm_order_item "
        . "WHERE order_item_id = '" . addslashes($orderItemID) . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }


    $query = "DELETE FROM #__vm_order_product_type  "
        . "WHERE order_id = '" . $orderID . "' "
        . " AND product_id = '" . $product_id . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }


    /* Update Stock Level and Product Sales */
    $query = "UPDATE #__vm_product "
        . "SET product_in_stock = product_in_stock - " . $diff
        . ", product_sales= product_sales + " . $diff
        . " WHERE product_id = '" . $product_id . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }

    echo "success";
    endit(0);
}

function updateQuantity()
{
    global $database, $mosConfig_offset;
    $ItemID = mosGetParam($_POST, "item_id", "");
    $aItemID = explode("[----]", $ItemID);
    $orderItemID = intval($aItemID[0]);
    $orderID = intval($aItemID[1]);
    $order_item_quantity = intval(mosGetParam($_REQUEST, "order_item_quantity", 0));

    if (!$order_item_quantity || $order_item_quantity < 0) {
        echo "error";
        endit(0);
    }


    $query = "SELECT product_id, product_quantity, product_final_price, product_item_price, product_final_price - product_item_price AS item_tax "
        . "FROM #__vm_order_item WHERE order_id = '" . $orderID . "' "
        . "AND order_item_id = '" . addslashes($orderItemID) . "'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderItemInfo = $rows[0];

    $product_id = $oOrderItemInfo->product_id;
    $diff = $order_item_quantity - $oOrderItemInfo->product_quantity;
    $tax_change = $diff * $oOrderItemInfo->item_tax;
    $price_change = $diff * $oOrderItemInfo->product_final_price;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);

    //update rate
    $query = "SELECT order_total "
        . "FROM #__vm_orders WHERE order_id = '" . $orderID . "' ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderTotal = $rows[0];
    if ($oOrderTotal >= 100) {
        if ((($oOrderTotal + $price_change) >= 60) && (($oOrderTotal + $price_change) < 100)) {
            $query = "UPDATE #__users SET rate=rate-1 WHERE id = '{$user_id}' and rate<10";
            $database->setQuery($query);
            $database->query();
        }
        if (($oOrderTotal + $price_change) < 60) {
            $query = "UPDATE #__users SET rate=rate-2 WHERE id = '{$user_id}' and rate<9";
            $database->setQuery($query);
            $database->query();
        }
    }
    if ($oOrderTotal >= 60) {
        if (($oOrderTotal + $price_change) >= 100) {
            $query = "UPDATE #__users SET rate=rate+1 WHERE id = '{$user_id}' and rate<10";
            $database->setQuery($query);
            $database->query();
        }
        if (($oOrderTotal + $price_change) < 60) {
            $query = "UPDATE #__users SET rate=rate-1 WHERE id = '{$user_id}' and rate<10";
            $database->setQuery($query);
            $database->query();
        }
    }

    // Update order
    $query = "UPDATE #__vm_orders "
        . "SET order_tax = (order_tax + " . $tax_change . " ), "
        . "order_total = (order_total + " . $price_change . " ), "
        . "order_subtotal = (order_subtotal + " . $price_change . ") "
        . "WHERE order_id = '" . $orderID . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }


    $query = "UPDATE #__vm_order_item "
        . "SET product_quantity = " . $order_item_quantity . ", "
        . "mdate = " . $timestamp . " "
        . "WHERE order_item_id = '" . addslashes($orderItemID) . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }


    /* Update Stock Level and Product Sales */
    $query = "UPDATE #__vm_product "
        . "SET product_in_stock = product_in_stock - " . $diff
        . ", product_sales= product_sales + " . $diff
        . " WHERE product_id = '" . $product_id . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        endit(0);
    }

    echo "success";
    endit(0);
}

function updateStandardShipping()
{
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $standard_shipping = intval(mosGetParam($_REQUEST, "standard_shipping", 0));

    $query = " SELECT * FROM #__vm_orders WHERE order_id = {$orderID}";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderInfo = $rows[0];
    $aCurrentShipping = explode("|", $oOrderInfo->ship_method_id);
    $nDeliverFee = $oOrderInfo->order_shipping - $oOrderInfo->order_shipping_tax - floatval($aCurrentShipping[3]);

    $query = " SELECT shipping_rate_name, shipping_carrier_name, shipping_rate_value, tax_rate
				FROM #__vm_shipping_rate, #__vm_tax_rate, #__vm_shipping_carrier
				WHERE shipping_carrier_id = shipping_rate_carrier_id
				AND tax_rate_id = shipping_rate_vat_id
				AND shipping_rate_id = '" . addslashes($standard_shipping) . "'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oStandingShipping = $rows[0];

    if (!$oStandingShipping->shipping_rate_value) {
        echo "error";
        endit(0);
    }

    $shipping_carrier = $oStandingShipping->shipping_carrier_name;
    $shipping_name = $oStandingShipping->shipping_rate_name;
    $shipping_tax = ($oStandingShipping->shipping_rate_value + $nDeliverFee) * $oStandingShipping->tax_rate;
    $shipping_rate = $oStandingShipping->shipping_rate_value + $nDeliverFee + $shipping_tax;
    $shipping_method = "standard_shipping|$shipping_carrier|$shipping_name|" . round($oStandingShipping->shipping_rate_value, 2) . "|$standard_shipping";


    // Update order
    $query = "UPDATE #__vm_orders "
        . "SET order_total = order_total - order_shipping + " . $shipping_rate . ", "
        . "order_shipping = " . $shipping_rate . ", "
        . "order_shipping_tax =  " . $shipping_tax . ", "
        . "ship_method_id = '" . addslashes($shipping_method) . "'"
        . " WHERE order_id = '" . $orderID . "'";
    $database->setQuery($query);

    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }

    endit(0);
}

function updateDiscount()
{
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $order_discount = floatval(mosGetParam($_REQUEST, "order_discount", 0));

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }

    //======================================================================================
    $query = "SELECT SUM(product_quantity*product_final_price) - SUM(product_quantity*product_item_price) AS item_tax, "
        . "SUM(product_quantity*product_final_price) as final_price "
        . "FROM #__vm_order_item WHERE order_id = '" . $orderID . "'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $row = $rows[0];

    // Update order
    $query = "UPDATE #__vm_orders "
        . "SET order_tax = (order_total - order_shipping - order_shipping_tax + order_discount - " . $order_discount . " ) * (" . $row->item_tax . " / " . $row->final_price . " ), "
        . "order_total = order_total + order_discount - " . $order_discount . ", "
        . "order_discount =  " . $order_discount . " "
        . "WHERE order_id = " . $orderID;
    $database->setQuery($query);

    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }

    endit(0);
}

function updateCouponDiscount()
{
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $order_coupon_discount = floatval(mosGetParam($_REQUEST, "order_coupon_discount", 0));

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }

    //======================================================================================
    $query = "SELECT SUM(product_quantity*product_final_price) - SUM(product_quantity*product_item_price) AS item_tax, "
        . "SUM(product_quantity*product_final_price) as final_price "
        . "FROM #__vm_order_item WHERE order_id = '" . $orderID . "'";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $row = $rows[0];

    // Update order
    $query = "UPDATE #__vm_orders "
        . "SET order_tax = (order_total - order_shipping - order_shipping_tax + coupon_discount - " . $order_coupon_discount . " ) * (" . $row->item_tax . " / " . $row->final_price . " ), "
        . "order_total = order_total + coupon_discount - " . $order_coupon_discount . ", "
        . "coupon_discount =  " . $order_coupon_discount . " "
        . "WHERE order_id = " . $orderID;
    $database->setQuery($query);


    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }
    //echo $query.$database->getErrorMsg();
    endit(0);
}

function updateSpecialInstructions()
{
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $customer_comments = htmlspecialchars(strip_tags(addslashes(mosGetParam($_REQUEST, "customer_comments", ""))));

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }

    //======================================================================================
    $query = "UPDATE #__vm_orders
			   SET customer_comments='{$customer_comments}'
			   WHERE order_id = $orderID";
    $database->setQuery($query);
    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }

    endit(0);
}

function updateCardMessage()
{
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $customer_note = htmlspecialchars(strip_tags(mosGetParam($_REQUEST, "customer_note", "")));
    $customer_signature = htmlspecialchars(strip_tags(addslashes(mosGetParam($_REQUEST, "customer_signature", ""))));

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }

    //======================================================================================
    $query = "UPDATE #__vm_orders
			   SET customer_note='{$customer_note}',
			   customer_signature='{$customer_signature}'
			   WHERE order_id = $orderID";
    $database->setQuery($query);
    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }

    endit(0);
}

function loadOrderHistory()
{
    global $database;
    $orderID = mosGetParam($_POST, "id", 0);

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }

    //======================================================================================
    $query = "SELECT * FROM #__vm_order_history WHERE order_id = $orderID ORDER BY order_status_history_id";
    $database->setQuery($query);
    $rows = $database->loadObjectList();


    HTML_AjaxOrder::loadOrderHistory($option, $rows);
}

function loadAjaxOrder()
{
    global $database, $my, $mosConfig_absolute_path;
    $aInfomation = array();
    $aList = array();
    $orderID = mosGetParam($_POST, "id", 0);

    if (!$orderID) {
        echo "This Order is not exist.";
        endit(0);
    }
    $aInfomation["UserName"] = $my->username;
    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    $aInfomation["OrderInfo"] = $oOrderInfo[0];

    //======================================================================================
    $query = "SELECT rate from #__users WHERE id = {$aInfomation["OrderInfo"]->user_id} ";
    $database->setQuery($query);
    $oUserRate = $database->loadObjectList();
    $aInfomation["rate"] = $oUserRate[0];

    $query = "SELECT * from #__rate_history WHERE id = {$aInfomation["OrderInfo"]->user_id} order by date desc ";
    $database->setQuery($query);
    $oUserRate = $database->loadObjectList();
    $aInfomation["RateHistory"] = $oUserRate;

    //======================================================================================
    $query = "SELECT * from #__vm_order_user_info WHERE user_id = {$aInfomation["OrderInfo"]->user_id} AND order_id = {$orderID} ORDER BY address_type, order_info_id ASC LIMIT 2";
    $database->setQuery($query);
    $oUserOrderInfo = $database->loadObjectList();
    $aInfomation["BillingInfo"] = $oUserOrderInfo[0];
    $aInfomation["ShippingInfo"] = $oUserOrderInfo[1];


    $types = array();
    $types[] = mosHTML::makeOption("", " - None - ");
    $types[] = mosHTML::makeOption("Mr.", "Mr.");
    $types[] = mosHTML::makeOption("Mrs.", "Mrs.");
    $types[] = mosHTML::makeOption("Dr.", "Dr.");
    $types[] = mosHTML::makeOption("Prof.", "Prof.");
    $aList['BillingInfoType'] = mosHTML::selectList($types, 'bill_type', 'class="inputbox" size="1"', 'value', 'text', $aInfomation["BillingInfo"]->title);
    $aList['ShippingInfoType'] = mosHTML::selectList($types, 'deliver_type', 'class="inputbox" size="1"', 'value', 'text', $aInfomation["ShippingInfo"]->title);


    //======================================================================================
    $query = "SELECT country_3_code, country_name FROM #__vm_country ORDER BY country_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oCountry = new stdClass;
    $oCountry->country_name = " ------------------ Country ------------------ ";
    $oCountry->country_3_code = "";
    $aCountry = array();
    $aCountry[] = $oCountry;
    $rows = array_merge($aCountry, $rows);
    $sSelected = $aInfomation["BillingInfo"]->country ? $aInfomation["BillingInfo"]->country : "CAN";
    $aList['BillingInfoCountry'] = mosHTML::selectList($rows, "bill_country", "size='1'", "country_3_code", "country_name", $sSelected);
    $sSelected = $aInfomation["ShippingInfo"]->country ? $aInfomation["ShippingInfo"]->country : "CAN";
    $aList['ShippingInfoCountry'] = mosHTML::selectList($rows, "deliver_country", "size='1'", "country_3_code", "country_name", $sSelected);


    //======================================================================================
    $query = "SELECT state_2_code, state_name FROM #__vm_state ORDER BY state_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oState = new stdClass;
    $oState->state_name = " ------------------ State ------------------ ";
    $oState->state_2_code = "";
    $aState = array();
    $aState[] = $oState;
    $rows = array_merge($aState, $rows);
    $aInfomation['BillingInfoState'] = mosHTML::selectList($rows, "bill_state", "size='1'", "state_2_code", "state_name", $aInfomation["BillingInfo"]->state);
    $aInfomation['ShippingInfoState'] = mosHTML::selectList($rows, "deliver_state", "size='1'", "state_2_code", "state_name", $aInfomation["ShippingInfo"]->state);
    ///echo  $aInfomation["ShippingInfo"]->state;
    //======================================================================================
    $query = " SELECT * FROM #__vm_order_item WHERE order_id = {$orderID}";
    $database->setQuery($query);
    $aInfomation["OrderItem"] = $database->loadObjectList();

    $query = " SELECT #__vm_payment_method.*, #__vm_order_payment.*, #__vm_order_payment.order_payment_number AS account_number
				FROM #__vm_payment_method, #__vm_order_payment
				WHERE #__vm_payment_method.payment_method_id = #__vm_order_payment.payment_method_id
				AND #__vm_order_payment.order_id = {$orderID}";
    $database->setQuery($query);
    $oPaymentInfo = $database->loadObjectList();
    $aInfomation["PaymentInfo"] = $oPaymentInfo[0];


    //======================================================================================
    $query = "SELECT order_status_code, order_status_name FROM #__vm_order_status ORDER BY order_status_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderStatus = new stdClass;
    $oOrderStatus->order_status_name = " - Order Status - ";
    $oOrderStatus->order_status_code = "";
    $aOrderStatus = array();
    $aOrderStatus[] = $oOrderStatus;
    $rows = array_merge($aOrderStatus, $rows);
    $aList['OrderStatus'] = mosHTML::selectList($rows, "order_status_inside", "size='1'", "order_status_code", "order_status_name", $aInfomation["OrderInfo"]->order_status);


    //======================================================================================
    $query = "SELECT warehouse_code, warehouse_name FROM #__vm_warehouse ORDER BY warehouse_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oWareHouse = new stdClass;
    $oWareHouse->warehouse_name = " - Warehouse - ";
    $oWareHouse->warehouse_code = "";
    $aWareHouse = array();
    $aWareHouse[] = $oWareHouse;
    $rows = array_merge($aWareHouse, $rows);
    $aList['OrderWareHouse'] = mosHTML::selectList($rows, "warehouse_inside", "size='1'", "warehouse_code", "warehouse_name", $aInfomation["OrderInfo"]->warehouse);


    //======================================================================================
    if (!$aInfomation["OrderInfo"]->priority) {
        $aInfomation["OrderInfo"]->priority = "PR01";
    }

    $query = "SELECT priority_code, priority_name FROM #__vm_priority ORDER BY priority_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oPriority = new stdClass;
    $oPriority->priority_name = " - Priority - ";
    $oPriority->priority_code = "";
    $aPriority = array();
    $aPriority[] = $oPriority;
    $rows = array_merge($aPriority, $rows);
    $aList['OrderPriority'] = mosHTML::selectList($rows, "priority_inside", "size='1'", "priority_code", "priority_name", $aInfomation["OrderInfo"]->priority);


    //======================================================================================
    $query = "SELECT * FROM #__vm_order_history WHERE order_id = $orderID ORDER BY order_status_history_id";
    $database->setQuery($query);
    $aInfomation["OrderHistoryInfo"] = $database->loadObjectList();


    //======================================================================================
    $aTemp = explode("|", $aInfomation["OrderInfo"]->ship_method_id);
    $query = "SELECT shipping_rate_id, shipping_rate_name FROM #__vm_shipping_rate ORDER BY shipping_rate_id ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aList['OrderStandingShpping'] = mosHTML::selectList($rows, "standard_shipping", "size='1'", "shipping_rate_id", "shipping_rate_name", $aTemp[4]);


    //======================================================================================
    $query = "SELECT CONCAT_WS( ' - ', CONCAT_WS( '', '[SKU: ', product_sku,']'), product_name ) AS name, product_id FROM #__vm_product ORDER BY product_sku";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aProduct = array();
    $aProductItem = new stdClass;
    $aProductItem->name = "--------------------------------------------- Select Product ---------------------------------------------";
    $aProductItem->id = 0;
    $aProduct[] = $aProductItem;
    $aProduct = array_merge($aProduct, $rows);
    $aList['cboProduct'] = mosHTML::selectList($aProduct, "add_product_id", "class='cbo-product' size='1'", "product_id", "name", "0");

//	print_r($aInfomation["OrderInfo"]);
    HTML_AjaxOrder::loadAjaxOrder($option, $aInfomation, $aList);
}

function updateBillingInfo()
{
    global $database;

    $ID = intval(mosGetParam($_REQUEST, "id", 0));
    $bill_type = mosGetParam($_REQUEST, "bill_type", "");
    $bill_company_name = mosGetParam($_REQUEST, "bill_company_name", "");
    $bill_first_name = mosGetParam($_REQUEST, "bill_first_name", "");
    $bill_last_name = mosGetParam($_REQUEST, "bill_last_name", "");
    $bill_middle_name = mosGetParam($_REQUEST, "bill_middle_name", "");
    //$bill_address_1 = mosGetParam($_REQUEST, "bill_address_1", "");
    // $bill_address_2 = mosGetParam($_REQUEST, "bill_address_2", "");
    $bill_suite = mosGetParam($_REQUEST, "bill_suite", "");
    $bill_street_number = mosGetParam($_REQUEST, "bill_street_number", "");
    $bill_street_name = mosGetParam($_REQUEST, "bill_street_name", "");
    $bill_city = mosGetParam($_REQUEST, "bill_city", "");
    $bill_zip_code = mosGetParam($_REQUEST, "bill_zip_code", "");
    $bill_country = mosGetParam($_REQUEST, "bill_country", "");
    $bill_state = mosGetParam($_REQUEST, "bill_state", "");
    $bill_phone = mosGetParam($_REQUEST, "bill_phone", "");
    $bill_evening_phone = mosGetParam($_REQUEST, "bill_evening_phone", "");
    $bill_fax = mosGetParam($_REQUEST, "bill_fax", "");
    $bill_email = mosGetParam($_REQUEST, "bill_email", "");
    $addr = $bill_suite . ' ' . $bill_street_number . ' ' . $bill_street_name;
    $query = " UPDATE #__vm_order_user_info
				SET company		= '{$bill_company_name}',
					title		= '{$bill_type}',
					last_name	= '{$bill_last_name}',
					first_name	= '{$bill_first_name}',
					middle_name	= '{$bill_middle_name}',
					phone_1		= '{$bill_phone}',
					phone_2		= '{$bill_evening_phone}',
					fax			= '{$bill_fax}',
					address_1	= '{$addr}',
					address_2	= ' ',
					city		= '{$bill_city}',
					state		= '{$bill_state}',
					country		= '{$bill_country}',
					zip			= '{$bill_zip_code}',
					user_email	= '{$bill_email}',
                                        suite           = '{$bill_suite}',
                                        street_number   = '{$bill_street_number}',
                                        street_name     = '{$bill_street_name}'

				WHERE order_info_id = $ID";
    $database->setQuery($query);
    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }

    endit(0);
}

function updateDeliverInfo()
{
    global $database;

    $ID = intval(mosGetParam($_REQUEST, "id", 0));
    $deliver_type = mosGetParam($_REQUEST, "deliver_type", "");
    $deliver_company_name = mosGetParam($_REQUEST, "deliver_company_name", "");
    $deliver_first_name = mosGetParam($_REQUEST, "deliver_first_name", "");
    $deliver_last_name = mosGetParam($_REQUEST, "deliver_last_name", "");
    $deliver_middle_name = mosGetParam($_REQUEST, "deliver_middle_name", "");
    //$deliver_address_1 = mosGetParam($_REQUEST, "deliver_address_1", "");
    // $deliver_address_2 = mosGetParam($_REQUEST, "deliver_address_2", "");
    $deliver_suite = mosGetParam($_REQUEST, "deliver_suite", "");
    $deliver_street_number = mosGetParam($_REQUEST, "deliver_street_number", "");
    $deliver_street_name = mosGetParam($_REQUEST, "deliver_street_name", "");
    $deliver_city = mosGetParam($_REQUEST, "deliver_city", "");
    $deliver_zip_code = mosGetParam($_REQUEST, "deliver_zip_code", "");
    $deliver_country = mosGetParam($_REQUEST, "deliver_country", "");
    $deliver_state = mosGetParam($_REQUEST, "deliver_state", "");
    $deliver_phone = mosGetParam($_REQUEST, "deliver_phone", "");
    $deliver_evening_phone = mosGetParam($_REQUEST, "deliver_evening_phone", "");
    $deliver_fax = mosGetParam($_REQUEST, "deliver_fax", "");
    $deliver_email = mosGetParam($_REQUEST, "deliver_email", "");
    $addr = $deliver_suite . ' ' . $deliver_street_number . ' ' . $deliver_street_name;
    $query = " UPDATE #__vm_order_user_info
				SET company		= '{$deliver_company_name}',
					title		= '{$delivering_type}',
					last_name	= '{$deliver_last_name}',
					first_name	= '{$deliver_first_name}',
					middle_name	= '{$deliver_middle_name}',
					phone_1		= '{$deliver_phone}',
					phone_2		= '{$deliver_evening_phone}',
					fax			= '{$deliver_fax}',
					address_1	= '{$addr}',
					address_2	= ' ',
					city		= '{$deliver_city}',
					state		= '{$deliver_state}',
					country		= '{$deliver_country}',
					zip			= '{$deliver_zip_code}',
					user_email	= '{$deliver_email}',
					suite           = '{$deliver_suite}',
					street_number	= '{$deliver_street_number}',
					street_name	= '{$deliver_street_name}'

				WHERE order_info_id = $ID";
    $database->setQuery($query);
    if ($database->query()) {
        echo "success";
    } else {
        echo "error";
    }

    endit(0);
}

function setOrder()
{
    global $database, $my, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $mos_debug_email, $vendor_currency, $mosConfig_test_card_numbers, $mosConfig_payment_centralization;

    $timestamp = time();
    $PaymentVar = array();
    $file = '';

    if (isset($_SESSION['cart'])) {
        if ($_SESSION['checkout_ajax']['user_id'] < 0) {
            die('wrong user');
        }

        $user_id = $_SESSION['checkout_ajax']['user_id'];
        $user_info_id = $_SESSION['checkout_ajax']['user_info_id'];
        $user_name = $_SESSION['checkout_ajax']['user_name'];
        $account_email = $_SESSION['checkout_ajax']['account_email'];

        $occasion = mosGetParam($_REQUEST, "occasion", "CONGR");
        $shipping_method = $_SESSION['checkout_ajax']['shipping_method'];
        $card_msg = mosGetParam($_REQUEST, "card_msg", "");
        $signature = mosGetParam($_REQUEST, "signature", "");
        $card_comment = mosGetParam($_REQUEST, "card_comment", "");
        $deliver_date = $_SESSION['checkout_ajax']['delivery_date'];
        $vendor_currency = $_SESSION['checkout_ajax']['vendor_currency_string'];

        $donated_price = ($_SESSION['checkout_ajax']['donated_price']) ? $_SESSION['checkout_ajax']['donated_price'] : 0;
        $used_donate_id = ($_SESSION['checkout_ajax']['used_donate_id']) ? $_SESSION['checkout_ajax']['used_donate_id'] : 0;

        $payment_method = mosGetParam($_REQUEST, "payment_method", "");
        $name_on_card = mosGetParam($_REQUEST, "name_on_card", "");
        $credit_card_number = mosGetParam($_REQUEST, "credit_card_number", "");
        $credit_card_security_code = mosGetParam($_REQUEST, "credit_card_security_code", "");
        $expire_month = intval(mosGetParam($_REQUEST, "expire_month", ""));
        $expire_year = intval(mosGetParam($_REQUEST, "expire_year", ""));
        $find_us = intval(mosGetParam($_REQUEST, "find_us", 0));
        $company_invoice = intval(mosGetParam($_REQUEST, "company_invoice", 0));
        //$pick_up_location = $_REQUEST['pick_up_location'];

        $hostname = $mosConfig_absolute_path . "/qazwsx/edcrfv/" . $file;

        $aProductId = array();
        $aQuantity = array();
        $aPrice = array();
        $aBouquet = array();


        foreach ($_SESSION['cart'] as $key => $product) {
            if ($product['product_id']) {
                $aProductId[] = (int)$product['product_id'];
                $aQuantity[$product['product_id']] = $product['quantity'];
                $aPrice[$product['product_id']] = $product['price'];
                $aBouquet[$product['product_id']] = $product['select_bouquet'];
            }
        }

        $sProductId = implode(',', $aProductId);

        $total_price = $_SESSION['checkout_ajax']['total_price'];
        $sub_total_price = $_SESSION['checkout_ajax']['products_price'];
        $total_tax = $_SESSION['checkout_ajax']['taxes_price'];
        $nStateTax = $_SESSION['checkout_ajax']['products_tax_rate'];
        $deliver_fee = $_SESSION['checkout_ajax']['shipping_price'];
        $total_deliver_tax_fee = $_SESSION['checkout_ajax']['shipping_tax'];

        $coupon_discount = $_SESSION['checkout_ajax']['coupon_discount'];
        $coupon_code_string = $_SESSION['checkout_ajax']['coupon_code'];


        $query = "SELECT `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($coupon_code_string) . "'";

        $coupon_result = false;
        $database->setQuery($query);
        $database->loadObject($coupon_result);


        if ($coupon_result) {
            $coupon_type_string = $coupon_result->coupon_type;
            $coupon_code = $coupon_result->coupon_code;
            $coupon_code_type = $coupon_result->percent_or_total;
            $coupon_value = $coupon_result->coupon_value;
        }

        $free_shipping = $_SESSION['checkout_ajax']['free_shipping'];


        $query = "SELECT * FROM #__vm_user_info WHERE user_id = '" . $database->getEscaped($user_id) . "' AND address_type = 'BT'";
        $oUser = false;
        $database->setQuery($query);
        $database->loadObject($oUser);

        if ($oUser) {
            $user_id = $oUser->user_id;
            $bill_company_name = $oUser->company;
            $bill_last_name = $oUser->last_name;
            $bill_first_name = $oUser->first_name;
            $bill_middle_name = $oUser->middle_name;
            $bill_phone = $oUser->phone_1;
            $bill_phone_2 = $oUser->phone_2;
            $bill_address_1 = $oUser->address_1;
            $bill_address_2 = $oUser->address_2;
            $bill_city = $oUser->city;
            $bill_state = $oUser->state;
            $bill_country = $oUser->country;
            if ($bill_country == '') {
                $bill_country == 'AUS';
            }
            $bill_zip_code = $oUser->zip;
            $bill_email = $oUser->user_email;
            $bill_suite = $oUser->suite;
            $bill_street_number = $oUser->street_number;
            $bill_street_name = $oUser->street_name;
        }


        $query = "SELECT * FROM #__vm_user_info AS VUI, #__users AS U WHERE VUI.user_id = U.id AND VUI.user_info_id ='" . $database->getEscaped($user_info_id) . "'";
        $oUser = false;
        $database->setQuery($query);
        $database->loadObject($oUser);

        if ($oUser) {
            $address_user_name = $oUser->address_type_name;
            $deliver_company_name = $oUser->company;
            $deliver_last_name = $oUser->last_name;
            $deliver_first_name = $oUser->first_name;
            $deliver_middle_name = $oUser->middle_name;
            $deliver_phone = $oUser->phone_1;
            $deliver_cell_phone = $oUser->phone_2;
            $deliver_address_1 = $oUser->address_1;
            $deliver_address_2 = $oUser->address_2;
            $deliver_city = $oUser->city;
            $deliver_state = $oUser->state;
            $deliver_country = $oUser->country;
            $deliver_zip_code = $oUser->zip;
            $deliver_recipient_email = $oUser->user_email;
            $address_type2 = $oUser->address_type2;
            $deliver_suite = $oUser->suite;
            $deliver_street_number = $oUser->street_number;
            $deliver_street_name = $oUser->street_name;
        }

        $query = " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id
            FROM #__vm_shipping_rate AS VSR
            INNER JOIN #__vm_shipping_carrier AS VSC
            ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id
            WHERE VSR.shipping_rate_id = '" . $database->getEscaped($shipping_method) . "'";
        $database->setQuery($query);
        $aShippingMethod = $database->loadRow();

        if ($free_shipping == 1) {
            $sFreeShipping = "Free";
        } else {
            $sFreeShipping = "Paid";
        }

        if (is_array($aShippingMethod) && count($aShippingMethod)) {
            $sShippingMethod = "standard_shipping|" . implode("|", $aShippingMethod) . "|$sFreeShipping";
        } else {
            $sShippingMethod = "standard_shipping|$sFreeShipping";
        }

        $query = " SELECT VMP.product_id, VMP.product_price, VTR.tax_rate
            FROM #__vm_product AS VM
            LEFT JOIN #__vm_product_price AS VMP
            ON VM.product_id = VMP.product_id
            LEFT JOIN  #__vm_tax_rate AS VTR
            ON VM.product_tax_id = VTR.tax_rate_id
            WHERE VM.product_id IN ({$sProductId})";
        $database->setQuery($query);

        if ($sProductId == '') {
            echo "error[--1--]Internal error processing your order, please refresh the page or call our toll-free line at ‎1-888-912-5666‎ to complete your order.";
            endit(0);
        }
        $rows = $database->loadObjectList();
        $order_tax_details = array();

        foreach ($rows as $value) {
            $value_product_price = getProductPrice($value->product_id, $value->product_price);

            if (!isset($order_tax_details["$nStateTax"])) {
                $order_tax_details["$nStateTax"] = doubleval($nStateTax) * doubleval($value_product_price);
            } else {
                $order_tax_details["$nStateTax"] = $order_tax_details["$nStateTax"] + (doubleval($nStateTax) * doubleval($value_product_price));
            }
        }


        /* Insert the main order information */
        //$order_number = md5("order" . $user_id . time());
        $order_number = 'blau_' . date('YmdHi') . '_' . mt_rand(10000000, 99999999);

        //================================== PAYMENT =========================================
        $VM_LANG = new vmLanguage();

        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
        $mask = substr($credit_card_number, 0, 6) . 'x' . substr($credit_card_number, -4);
        $gateway = ($vendor_currency == 'USD') ? "BS US" : "BS CA";


        if ($credit_card_number == "4111111111111111") {
            $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
            $order_status = "X";
            $payment_msg = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
        } elseif ($company_invoice) {
            $aResult["order_payment_log"] = 'Company Invoice';
            $order_status = "3";
            $payment_msg = 'Company Invoice';
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
                require_once 'end_access_log.php';
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
                        if (($aResult['Payment']["TxnList"]["Txn"]["responseCode"] == "00" || $aResult['Payment']["TxnList"]["Txn"]["responseCode"] == "08") && trim($aResult['Payment']["TxnList"]["Txn"]["responseText"]) == "Approved" or $aResult['approved'] == 1) {
                            $order_status = "A";
                            $aResult["approved"] = 1;
                            $payment_msg = " Payment has been approved";
                            if (in_array($credit_card_number, $mosConfig_test_card_numbers)) {
                                $order_status = "X";
                            }

                            $aResult["order_payment_trans_id"] = $aResult['MessageInfo']["messageID"];
                            $aResult["order_payment_log"] = $aResult['Payment']["TxnList"]["Txn"]["responseText"];
                        } else {
                            if ($aResult['Payment']["TxnList"]["Txn"]["responseCode"] == '05')
                                $aResult['Payment']["TxnList"]["Txn"]["responseText"] = 'Credit card declined, please resubmit your credit card.';

                            $Error = "Error #" . $aResult['Payment']["TxnList"]["Txn"]["responseCode"] . ": " . $aResult['Payment']["TxnList"]["Txn"]["responseText"];
                            echo try_again($Error);
                            endit(0);
                        }
                    } else {
                        $Error = "Error: The messageID is incorrect";
                        echo "error[--1--]" . $Error . "";
                        endit(0);
                    }
                } else {
                    $Error = "Error #" . $aResult["Status"]["statusCode"] . ": " . $aResult["Status"]["statusDescription"];
                    echo try_again($Error);
                    endit(0);
                }
            } else {
                echo try_again();
                endit(0);
            }
        }

        //====================================================================================
        $phpShopDeliveryDate = date("M d, Y", strtotime($deliver_date) + ($mosConfig_offset * 60 * 60));


        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip_address = "unknown";
        }

        $vendor_id = '1';

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
            find_us,
            ip_address,
            coupon_code,
            coupon_type,
            coupon_value,
            username )
        VALUES( 	
            '" . $database->getEscaped($user_id) . "',
            '" . $database->getEscaped($vendor_id) . "',
            '" . $database->getEscaped($order_number) . "',
            '" . $database->getEscaped($user_info_id) . "',
            '" . $database->getEscaped($total_price) . "',
            '" . $database->getEscaped($sub_total_price) . "',
            '" . $database->getEscaped($total_tax) . "',
            '" . serialize($order_tax_details) . "',
            '" . $database->getEscaped($deliver_fee) . "',
            '" . $database->getEscaped($total_deliver_tax_fee) . "',
            '" . $database->getEscaped($coupon_discount) . "',
            '" . $database->getEscaped($vendor_currency) . "',
            '" . $database->getEscaped($order_status) . "',
            " . $timestamp . ",
            " . $timestamp . ",
            '" . date("d-m-Y", strtotime($deliver_date)) . "',
            '" . $database->getEscaped($sShippingMethod) . "',
            '" . $database->getEscaped($card_msg) . "',
            '" . $database->getEscaped($signature) . "',
            '" . $database->getEscaped($occasion) . "',
            '" . $database->getEscaped($card_comment) . "',
            '" . $database->getEscaped($find_us) . "',
            '" . $database->getEscaped(strip_tags($ip_address)) . "',
            '" . $database->getEscaped($coupon_code) . "',
            '" . $database->getEscaped($coupon_code_type) . "',
            '" . $database->getEscaped($coupon_value) . "',
            '" . $database->getEscaped(strip_tags($user_name)) . "' )";

        $database->setQuery($query);

        if (!$database->query()) {
            echo $database->getErrorMsg();
            die;
        }


        $order_id = $database->insertid();
        $test_q = $query;



        $payment_number = preg_replace("/ |-/", "", $credit_card_number);

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
        } elseif ($credit_card_number == "4111111111111111") {
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
                '{$aResult["order_payment_log"]}[--1--]$payment_method',
                '{$name_on_card}',
                '{$aResult["order_payment_trans_id"]}')";
            $database->setQuery($query);
            $database->query();
        }

        if ($coupon_discount > 0 && strpos($coupon_code_string, "PC-") !== false) {
            $phpShopShippingDiscount = "-" . LangNumberFormat::number_format($coupon_discount, 2, '.', ' ');
            $phpShopCouponDiscount = "-/-";
        } elseif ($coupon_discount > 0) {
            $phpShopCouponDiscount = "-" . LangNumberFormat::number_format($coupon_discount, 2, '.', ' ');
            $phpShopShippingDiscount = "-/-";
        }

        //===================== DELETE GIFT COUPON AFTER USED =================================
        if ($coupon_code_string && $coupon_type_string == "gift") {
            $sql = "DELETE FROM #__vm_coupons WHERE coupon_code = '$coupon_code'";
            $database->setQuery($sql);
            $database->query();
        }


        date_default_timezone_set('Australia/Sydney');
        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

        $history_comment = 'From frontend.';

        if (!empty($aResult["order_payment_trans_id"])) {
            $history_comment .= ' Transaction Id: ' . $aResult["order_payment_trans_id"] . '. Amount: ' . $aData[2];
        }

        $query = "INSERT INTO #__vm_order_history(	order_id,
            order_status_code,
            date_added,
            customer_notified, comments, user_name)
            VALUES (
            '$order_id',
            '$order_status',
            '" . $mysqlDatetime . "',
            1, '" . $database->getEscaped($history_comment) . "', '" . htmlspecialchars(strip_tags($account_email)) . "')";

        $database->setQuery($query);
        $database->query();

        /* Insert the Order payment info */
        $payment_number = preg_replace("/ |-/", "", $credit_card_number);

        if (!isset($aResult["order_payment_trans_id"])) {
            $aResult["order_payment_trans_id"] = "";
        }
        // Payment number is encrypted using mySQL ENCODE function.
        if (!empty($aResult["approved"]) && $aResult["approved"] == 1) {
            $query = "INSERT INTO #__vm_order_payment(	order_id,
                order_payment_code,
                payment_method_id,
                order_payment_number,
                order_payment_expire,
                order_payment_log,
                order_payment_name,
                order_payment_trans_id,
                gateway)
                VALUES (
                {$order_id},
                '',
                3,
                'NOT SAVED',
                '',
                '{$aResult["order_payment_log"]}[--1--]$payment_method',
                '',
                '{$aResult["order_payment_trans_id"]}','{$gateway}')";

            $database->setQuery($query);
            $database->query();
        } elseif ($credit_card_number == "4111111111111111" /* && $credit_card_security_code=='1111' && !$expire_month && $expire_year=='2020' && $name_on_card=='Bloomex' */) {
            $query = "INSERT INTO #__vm_order_payment(	order_id,
                order_payment_code,
                payment_method_id,
                order_payment_number,
                order_payment_expire,
                order_payment_log,
                order_payment_name,
                order_payment_trans_id,
                gateway)
                VALUES (
                {$order_id},
                '',
                3,
                '{$payment_number}',
                '" . strtotime("{$expire_month}/01/{$expire_year}") . "',
                '{$aResult["order_payment_log"]}[--1--]$payment_method',
                '{$name_on_card}',
                '{$aResult["order_payment_trans_id"]}','{$gateway}')";

            $database->setQuery($query);
            $database->query();

            $aResult["order_payment_log"] = '<span style="display: block;background-color: yellow;color: red;text-transform: uppercase;padding: 4px;">' . $aResult["order_payment_log"] . '</span>';
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
            address_1,
            address_2,
            city,
            state,
            country,
            zip,
            user_email, suite, street_number,street_name )
            VALUES(  '" . $order_id . "',
            {$user_id},
            'BT',
            '-default-',
            '" . htmlentities($bill_company_name, ENT_QUOTES) . "',
            '" . htmlentities($bill_last_name, ENT_QUOTES) . "',
            '" . htmlentities($bill_first_name, ENT_QUOTES) . "',
            '" . htmlentities($bill_middle_name, ENT_QUOTES) . "',
            '" . htmlentities($bill_phone, ENT_QUOTES) . "',
            '" . htmlentities($bill_phone_2, ENT_QUOTES) . "',
            '" . htmlentities($bill_address_1, ENT_QUOTES) . "',
            '" . htmlentities($bill_address_2, ENT_QUOTES) . "',
            '" . htmlentities($bill_city, ENT_QUOTES) . "',
            '" . htmlentities($bill_state, ENT_QUOTES) . "',
            '" . htmlentities($bill_country, ENT_QUOTES) . "',
            '" . htmlentities($bill_zip_code, ENT_QUOTES) . "',
            '" . htmlentities($account_email, ENT_QUOTES) . "',
            '" . htmlentities($bill_suite, ENT_QUOTES) . "',
            '" . htmlentities($bill_street_number, ENT_QUOTES) . "',
            '" . htmlentities($bill_street_name, ENT_QUOTES) . "' )";

        $database->setQuery($query);

        if (!$database->query()) {
            $sMySubject = "The wrong of Billing Address for order #$order_id";
            $sErrorMsg = $database->getErrorMsg();
            $sMyContent = "$query <br/><br/><br/>$sErrorMsg<br/><br/><br/>";
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $mos_debug_email, $sMySubject, $sMyContent, 1);
        }

        if (!isset($_REQUEST['find_us'])) {
            $date = date("Y-m-d");
            $user_email = htmlentities($account_email, ENT_QUOTES);
            $query = "INSERT INTO tbl_unsubscribe_comments (user_id, comment, email,date) VALUES ('$user_id','', '$user_email','$date')";
            $database->setQuery($query);
            $database->query();
        }

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
            address_1,
            address_2,
            city,
            state,
            country,
            zip,
            user_email, suite,street_number, street_name )
            VALUES(  
            '" . $order_id . "',
            {$user_id},
            'ST',
            '" . htmlentities($address_type2, ENT_QUOTES) . "',
            '" . htmlentities($address_user_name, ENT_QUOTES) . "',
            '" . htmlentities($deliver_company_name, ENT_QUOTES) . "',
            '" . htmlentities($deliver_last_name, ENT_QUOTES) . "',
            '" . htmlentities($deliver_first_name, ENT_QUOTES) . "',
            '" . htmlentities($deliver_middle_name, ENT_QUOTES) . "',
            '" . htmlentities($deliver_phone, ENT_QUOTES) . "',
            '" . htmlentities($deliver_cell_phone, ENT_QUOTES) . "',
            '" . htmlentities($deliver_address_1, ENT_QUOTES) . "',
            '" . htmlentities($deliver_address_2, ENT_QUOTES) . "',
            '" . htmlentities($deliver_city, ENT_QUOTES) . "',
            '" . htmlentities($deliver_state, ENT_QUOTES) . "',
            '" . htmlentities($deliver_country, ENT_QUOTES) . "',
            '" . htmlentities($deliver_zip_code, ENT_QUOTES) . "',
            '" . htmlentities($deliver_recipient_email, ENT_QUOTES) . "',
            '" . htmlentities($deliver_suite, ENT_QUOTES) . "',
            '" . htmlentities($deliver_street_number, ENT_QUOTES) . "',
            '" . htmlentities($deliver_street_name, ENT_QUOTES) . "' )";

        $database->setQuery($query);

        if (!$database->query()) {
            $sMySubject = "The wrong of Delivery Address for order #$order_id";
            $sErrorMsg = $database->getErrorMsg();
            $sMyContent = "$query <br/><br/><br/>$sErrorMsg<br/><br/><br/>";
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $mos_debug_email, $sMySubject, $sMyContent, 1);
        }
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
        $order_items .= '<tr style="background-color: #E0E0E0;color: #000;font-weight: bold;">
                                                            <td width="5%" style="border-top: 5px solid red;border: 1px solid black;">#</td>
                                                            <td width="50%"  style="border-top: 5px solid red;border: 1px solid black;">Product Name</td>
                                                            <td width="15%"  style="border-top: 5px solid red;border: 1px solid black;">SKU</td>
                                                            <td width="5%"  style="border-top: 5px solid red;border: 1px solid black;">Quantity</td>
                                                            <td width="15%"  style="border-top: 5px solid red;border: 1px solid black;">Product Price</td>
                                                            <td width="15%"  style="border-top: 5px solid red;border: 1px solid black;">Total</td>
                                                    </tr>';


        $query = "SELECT `SG`.`shopper_group_discount`
        FROM `#__vm_shopper_vendor_xref` AS `SVX` 
            INNER JOIN `#__vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id`
        WHERE `SVX`.`user_id`=" . $user_id . " LIMIT 1";

        $database->setQuery($query);
        $ShopperGroupDiscount = $database->loadResult();

        if ($ShopperGroupDiscount) {
            $ShopperGroupDiscount = $ShopperGroupDiscount / 100;
        }

        $phpShopOrderSubtotal = 0;
        $phpShopOrderTax = 0;

        if (count($rows)) {
            $j = 0;
            foreach ($rows as $value) {
                $nQuantityTemp = $aQuantity[$value->product_id];
                $nBouquet = $aBouquet[$value->product_id];

                $product_item_price = $aPrice[$value->product_id];

                $product_final_price = ($product_item_price * floatval($nStateTax)) + $product_item_price;

                if ($nBouquet == 'deluxe' or $nBouquet == 'supersize') {
                    $value->product_name = $value->product_name . '  (' . $nBouquet . ')';
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
                    product_coupon,
                    cdate,
                    mdate )
                    VALUES(     $order_id,
                    '$user_info_id',
                    $vendor_id,
                    " . $value->product_id . ",
                    '" . htmlentities($value->product_sku, ENT_QUOTES) . "',
                    '" . htmlentities($value->product_name, ENT_QUOTES) . "',
                    " . intval($nQuantityTemp) . ",
                    " . $product_item_price . ",
                    " . $product_final_price . ",
                    '" . $value->product_currency . "',
                    'P',
                    '" . htmlentities(strip_tags($value->product_desc), ENT_QUOTES) . "',
                    '',
                    '$timestamp',
                    '$timestamp'
                     )";

                $database->setQuery($query);
                $database->query();

                $order_items .= '<tr style="background-color: #DFE7EF">
                    <td style="border: 1px solid #ccc;">' . ($j + 1) . '. </td>
                    <td style="border: 1px solid #ccc;">' . stripslashes($value->product_name) . '<br/></td>
                    <td style="border: 1px solid #ccc;">' . addslashes($value->product_sku) . '</td>
                    <td style="border: 1px solid #ccc;">' . intval($nQuantityTemp) . '</td>
                    <td style="border: 1px solid #ccc;">$' . number_format($product_item_price, 2, ".", " ") . '</td>
                    <td style="border: 1px solid #ccc;">$' . number_format(($product_item_price) * intval($nQuantityTemp), 2, ".", " ") . '</td>
                </tr>';


                $order_item_id = $database->insertid();


                //ORDER ITEM INGREDIENTS

                $q = "SELECT `l`.`igl_quantity` as `quantity`, `o`.`igo_product_name` as `name`
                FROM `product_ingredients_lists` as `l`
                LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
                WHERE `l`.`product_id`=" . $value->product_id . "";
                $database->setQuery($q);
                $order_item_ingredients_rows = $database->loadObjectList();

                $order_item_ingredients_array = array();

                foreach ($order_item_ingredients_rows as $row) {
                    $order_item_ingredients_array[] = "(" . $order_id . ", " . $order_item_id . ", '" . addslashes($row->name) . "', '" . ($row->quantity * intval($nQuantityTemp)) . "')";
                }

                $query = "INSERT INTO #__vm_order_item_ingredient (order_id, order_item_id, ingredient_name, ingredient_quantity)
                VALUES " . implode(',', $order_item_ingredients_array) . "";

                $database->setQuery($query);
                $database->query();

                unset($order_item_ingredients_array);

                //END
                //DELETE CUSTOM

                if (preg_match('/^Custom-/si', addslashes($value->product_sku))) {
                    $qInput = "DELETE FROM `#__vm_product` WHERE `product_id`='" . $value->product_id . "";
                    $database->setQuery($qInput);
                    $database->query();

                    $qInput = "DELETE FROM `#__vm_product_price` WHERE `product_id`='" . $value->product_id . "";
                    $database->setQuery($qInput);
                    $database->query();

                    $qInput = "DELETE FROM `#__vm_product_category_xref` WHERE `product_id`='" . $value->product_id . "";
                    $database->setQuery($qInput);
                    $database->query();

                    $qInput = "DELETE FROM `product_ingredients_lists` WHERE `product_id`='" . $value->product_id . "";
                    $database->setQuery($qInput);
                    $database->query();
                }

                //END

                $phpShopOrderSubtotal += $product_item_price * intval($nQuantityTemp);

                /* Insert ORDER_PRODUCT_TYPE */
                $query = "SELECT * FROM #__vm_product_product_type_xref, #__vm_product_type
                                              WHERE #__vm_product_product_type_xref.product_id = '" . $value->product_id . "'
                                              AND #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id";
                $database->setQuery($query);
                $rows2 = $database->loadObjectList();

                if (count($rows2)) {
                    foreach ($rows2 as $item) {
                        $product_type_id = $item->product_type_id;

                        $query = "  SELECT * FROM #__vm_product_type_$product_type_id
                            WHERE product_id='" . $value->product_id . "' ";
                        $database->setQuery($query);
                        $rows3 = $database->loadObjectList();
                        $item2 = $rows3[0];


                        $product_type_name = isset($item2->product_type_name) ? $item2->product_type_name : "";
                        $product_type_quantity = isset($item2->quantity) ? $item2->quantity : 0;
                        $product_type_price = isset($item2->price) ? $item2->price : "";

                        $query = "INSERT INTO #__vm_order_product_type( order_id,
                                                                                                                                    product_id,
                                                                                                                                    product_type_name,
                                                                                                                                    quantity, price)
                                                                    VALUES ( $order_id,
                                                                                     " . $value->product_id . "',
                                                                                     '" . addslashes($product_type_name) . "',
                                                                                     " . $product_type_quantity . ",
                                                                                     " . $product_type_price . ")";
                        $database->setQuery($query);
                        $database->query();
                    }
                }

                $j++;
            }
        }

        $nDiscount = 0;
        $sDiscount = 0;
        $nDiscount = number_format($phpShopOrderSubtotal * $ShopperGroupDiscount, 2);

        if ($nDiscount > 0) {
            $query = "UPDATE `#__vm_orders` SET `order_discount`='" . $nDiscount . "' WHERE `order_id`=" . $order_id . "";

            $database->setQuery($query);
            $database->query();

            $sDiscount = '-' . LangNumberFormat::number_format($nDiscount, 2, '.', ' ');
        }

        $order_items .= '</table>';

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
            . "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom . "\">" . $mosConfig_mailfrom . "</a><br/><b> Please Note: Orders placed for deliveries outside of Sydney, Melbourne, Brisbane and Perth may be delayed. We will contact you via email if there is an issue.</b>";

        $vendor_header = "The following order was received.";
        $vendor_order_link = $mosConfig_live_site . "/index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin";
        $vendor_footer_html = "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";

        $vendor_image = "<img src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/vendor/" . $vendor_full_image . "\" alt=\"vendor_image\" border=\"0\" />";

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

        require_once CLASSPATH . 'ps_comemails.php';

        $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='1'";
        $confirmation_obj = false;
        $database->setQuery($query);
        $database->loadObject($confirmation_obj);

        $ps_comemails = new ps_comemails;

        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $confirmation_obj->email_html), 1);
        //!NEW CONFIRMATION
        //FRAUD
        $admin_order_link = $mosConfig_live_site . '/administrator/index2.php?page=order.order_list&option=com_virtuemart&order_id_filter=' . $order_id;

        /*
          $ip_country_b = trim(file_get_contents('http://ipinfo.io/' . $_SERVER['REMOTE_ADDR'] . '/country'));
          $ip_country = strtolower($ip_country_b);
          $customer_ip_address = $ip_country_b;
         */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://ip-api.com/php/' . $_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $query = @unserialize(curl_exec($ch));

        //$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$_SERVER['REMOTE_ADDR']));

        if ($query and $query['status'] == 'success') {
            $ip_country = strtolower($query['countryCode']);
            $customer_ip_address = $query['countryCode'];
        } else {
            $ip_country = 'unk';
            $customer_ip_address = 'UNK';
        }

        $total_price_30_120 = false;
        $discount_price_25 = false;
        $cardholder_name_coincides = false;
        $ip_usa_can_au = false;
        $ip_coincides = false;

        $send_fraud = false;

        if ($coupon_discount < 25) {
            $discount_price_25 = true;
        }

        if (30 < $total_price and $total_price < 120) {
            $total_price_30_120 = true;
        }

        $bill_names = array($bill_first_name, $bill_last_name, $bill_middle_name);

        foreach ($bill_names as $bill_name) {
            if (empty($bill_name)) {
                continue;
            }

            $bill_name_a = explode(' ', $bill_name);

            foreach ($bill_name_a as $bill_name_one) {
                if (empty($bill_name_one)) {
                    continue;
                }

                if (strstr(strtolower($name_on_card), strtolower($bill_name_one))) {
                    $cardholder_name_coincides = true;
                }
            }
        }

        $good_country = array('us', 'ca', 'au');

        if (in_array($ip_country, $good_country)) {
            $ip_usa_can_au = true;
        }

        $query = "SELECT `country_2_code` FROM `jos_vm_country` WHERE `country_3_code`='" . $database->getEscaped($bill_country) . "'";
        $bill_country_2_code = false;
        $database->setQuery($query);
        $database->loadObject($bill_country_2_code);

        if ($ip_country == strtolower($bill_country_2_code->country_2_code)) {
            $ip_coincides = true;
        }

        if ($discount_price_25 == false) {
            $send_fraud = true;
        } else {
            if ($total_price_30_120 == true) {
                if ($cardholder_name_coincides == true) {
                    if ($ip_usa_can_au == false) {
                        if ($ip_coincides == false) {
                            $send_fraud = true;
                        }
                    }
                } else {
                    $send_fraud = true;
                }
            } else {
                $send_fraud = true;
            }
        }

        if ($discount_price_25 == false) {
            $send_fraud = true;
        }

        $filter_triggering = 'NO';
        $first_part = substr($credit_card_number, 0, 6);
        $last_part = substr($credit_card_number, -4);
        $cc_number = $first_part . 'x' . $last_part;
        $fraud_email = 'fraud@bloomex.ca';
        //$fraud_email = 'danielyanlevon89@mail.ru';
        $admin_order_link = '<a href="' . $admin_order_link . '">' . $order_id . '</a>';
        $total_price_30_120 = (($total_price_30_120 == true) ? '<span style="color: green;">yes</span>' : '<span style="color: red;">no</span>');
        $discount_price_25 = (($discount_price_25 == true) ? '<span style="color: green;">yes</span>' : '<span style="color: red;">no</span>');
        $cardholder_name_coincides = (($cardholder_name_coincides == true) ? '<span style="color: green;">yes</span>' : '<span style="color: red;">no</span>');
        $ip_usa_can_au = (($ip_usa_can_au == true) ? '<span style="color: green;">yes</span>' : '<span style="color: red;">no</span>');
        $ip_coincides = (($ip_coincides == true) ? '<span style="color: green;">yes</span>' : '<span style="color: red;">no</span>');
        if ($send_fraud == true) {
            $shopper_html_fraud = '<table>
                <tr>
                    <th colspan="2">
                        <b>Fraud info</b>
                    </th>
                </tr>
                <tr>
                    <td>
                        Card holder name
                    </td>
                    <td>
                        ' . $name_on_card . '
                    </td>
                </tr>
                <tr>
                    <td>
                        CC Number
                    </td>
                    <td>
                        ' . $cc_number . '
                    </td>
                </tr>
                <tr>
                    <td>
                        Customer IP-address 
                    </td>
                    <td>
                        ' . $customer_ip_address . '
                    </td>
                </tr>
                <tr>
                    <td>
                        Price value
                    </td>
                    <td>
                    ' . $total_price_30_120 . '
                     </td>
                </tr>
                <tr>
                    <td>
                        Discount price < $25
                    </td>
                    <td>
                      ' . $discount_price_25 . '
                    </td>
                 </tr>
                <tr>
                    <td>
                        Cardholder name coincides with billing
                    </td>
                    <td>
                    ' . $cardholder_name_coincides . '
                    </td>
                </tr>
                <tr>
                    <td>
                        Ip-address from USA, CAN, AU
                    </td>
                    <td>
                    ' . $ip_usa_can_au . '
                    </td>
                </tr>
                <tr>
                    <td>
                        Ip-address coincides with billing
                    </td>
                    <td>
                     ' . $ip_coincides . '
                    </td>
                </tr>
                <tr>
                    <td>
                        Open order in order-list
                    </td>
                    <td>
                       ' . $admin_order_link . '
                    </td>
               </tr>
                </table><br/>';

            $shopper_html_new = preg_replace('/<img([^>]+)>/si', '', $shopper_html);
            $filter_triggering = 'YES';
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $fraud_email, 'AU FCF of ' . $order_id, $shopper_html_fraud . $shopper_html_new, 1);
        }
        $admin_order_link = strip_tags($admin_order_link);
        $cardholder_name_coincides = strip_tags($cardholder_name_coincides);
        $ip_coincides = strip_tags($ip_coincides);
        $ip_usa_can_au = strip_tags($ip_usa_can_au);
        $total_price_30_120 = strip_tags($total_price_30_120);
        $discount_price_25 = strip_tags($discount_price_25);
        $sql_fraud = "INSERT INTO #__vm_fraud_track(
                                                        order_id, name_on_card , ip_address,
                                                        name_coincides_with_billing,
                                                        Ip_address_coincides_with_billing,Ip_address_from_usa_au_can,
                                                        total_price_30_120,discount_price_25,create_date,cc_number,filter_triggering
                                                         )
						VALUES('$admin_order_link', '$name_on_card',
						 '$customer_ip_address',
						  '$cardholder_name_coincides',
						   '$ip_coincides',
						    '$ip_usa_can_au',
						    '$total_price_30_120',
						    '$discount_price_25',
						    '$timestamp','$cc_number','$filter_triggering')";
        $database->setQuery($sql_fraud);
        $database->query();

        //!FRAUD

        /* ===================================== Assign Order To The WareHouse ===================================== */

        $while = 4;
        $need_zip_code = $deliver_zip_code;

        $current_time = date("G");
        if (date("Y-m-d", strtotime($deliver_date)) == date("Y-m-d") && $current_time <= 15) {
            $col = 'PWH.same_day_warehouse_id';
        } else {
            $col = 'PWH.warehouse_id';
        }

        while ($while > 0) {
            $query = "SELECT WH.warehouse_email,"
                . " WH.warehouse_code FROM #__vm_warehouse AS WH,"
                . " #__postcode_warehouse AS PWH WHERE WH.warehouse_id = {$col} AND PWH.published=1 AND PWH.postal_code LIKE '" . $need_zip_code . "'";
            $database->setQuery($query);
            $oWarehouse = $database->loadObjectList();
            if (count($oWarehouse)) {
                $oWarehouse = $oWarehouse[0];
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
                $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "',color='black', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
            } else {

                $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
            }
            $database->setQuery($query);
            $database->query();

            if ($warehouse_code) {
                $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
                $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);

                mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
            }
        } else {
            $query = "UPDATE #__vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
            $database->setQuery($query);
            $database->query();
        }


        $sql = "SELECT COUNT(*) FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'PC-01'";
        $database->setQuery($sql);
        $pc_rows = $database->loadResult();

        if ($pc_rows) {
            $sql_pc = "INSERT INTO tbl_platinum_club  (user_id,start_datetime) VALUES ('" . $user_id . "', NOW())";
            $database->setQuery($sql_pc);
            $database->query();

            $shopper_subject = "Your Bloomex Platinum Club Membership";
            $shopper_html = "Dear $bill_first_name,<br/><br/>
							Congratulations on becoming a Bloomex Platinum Club Member. You will now receive Free Regular Shipping on all your orders by logging in under this email address.
                            Call or order online at your convenience.<br/><br/>
							Best Regards,<br/>
							Bloomex Australia<br/>
							1800 451 637<br/><br/>
							<img src='http://media.bloomex.ca/coupon_logo.png' />";
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);
        }


        $sql = "SELECT product_id FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'VC-01'";
        $database->setQuery($sql);
        $vc_rows = $database->loadResult();
        $sVCCouponCode = "";


        if ($vc_rows) {
            $vc_quantity = $aQuantity[$vc_rows];
            for ($s = 0; $s < $vc_quantity; $s++) {

                $sVCCouponCode = createCouponName("VC-"); //"VC-" . strtoupper(genRandomString(8));
                if ($sVCCouponCode != "") {
                    $sql = "INSERT INTO #__vm_coupons(coupon_code, percent_or_total , coupon_type, coupon_value )
						VALUES('$sVCCouponCode', 'total', 'gift', '20.00')";
                    $database->setQuery($sql);
                    $database->query();

                    $shopper_subject = "Your Bloomex $20.00 voucher code";
                    $shopper_html = "Dear $bill_first_name,<br/><br/>
                Thank you for your purchase.  Your Bloomex $20.00 voucher code is <b>$sVCCouponCode</b><br/><br/>
                Call or order online at your convenience.<br/><br/>
                Best Regards,<br/><br/>
                Jessica<br/>
                Bloomex Inc<br/>
                866 912 5666<br/><br/>
                <img src='$mosConfig_live_site/templates/bloomex7/images/coupon_logo.png' />";

                    mosMail($mosConfig_mailfrom, $mosConfig_fromname, $account_email, $shopper_subject, $shopper_html, 1);
                }
            }
        }
    }

    global $mosConfig_adm_auth, $mosConfig_adm_link;
    $md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';
    if (!empty($_SESSION['platinum_cart'])) {
        $service_url = $mosConfig_adm_link . '/scripts/for_blcoma/update_platinum_club_uses_count.php';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_adm_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'user_id=' . $user_id . '&key=' . md5($md5_salt . $user_id . $md5_salt));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $curl_response = curl_exec($curl);
        if ($curl_response) {
            $response = json_decode($curl_response);
            curl_close($curl);
            if ($response->result && $response->result == 'finished_limit') {

            }
        }
    }
    $CheckoutAjax = new CheckoutAjax;
    if ($used_donate_id)
        $CheckoutAjax->adddonte($order_id, $donated_price, $used_donate_id);

    unset($_SESSION['checkout_shipping_method'], $_SESSION['checkout_coupon_code'], $_SESSION['checkout_user_info_id'], $_SESSION['platinum_cart'], $_SESSION['checkout_delivery_date'], $_SESSION['checkout_ajax']);

    die("success[--1--]{$order_id}[--1--]{$payment_msg}");
}

class CheckoutAjax
{

    public $checkout_errors = array();

    public function adddonte($order_id, $donated_price, $used_donate_id)
    {

        global $database;
        $sql = "INSERT INTO `tbl_used_donation` (`donation_id`,`donation_price`, `order_id`) VALUES (" . $used_donate_id . ", " . $donated_price . ", " . $order_id . ")";
        $database->setQuery($sql);
        $database->query();
    }

    public function get_current_donation_id_price($donate)
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

    public function GetTotal($ship_to_info_id, $donate)
    {
        global $my;

        $return = array();

        $json = $this->GetShippingPrice($_SESSION['checkout_delivery_date'], $ship_to_info_id);

        $this->GetCouponDiscount();

        $from_json = json_decode($json);

        $total_price = 0;
        $shipping_price = 0;
        $products_price = 0;
        $taxes_price = 0;

        if (sizeof($from_json->error) == 0) {

            $rate_json = json_decode($this->GetUserRating());

            if (sizeof($rate_json->error) > 0) {
                $return['result'] = false;
                $return['error'] = $rate_json->error;
            } else {

                if ($from_json->free_shipping == 0) {
                    if ($from_json->shipping_discount > 0) {
                        $from_json->shipping_price -= $from_json->shipping_discount;
                    }

                    $shipping_price = $from_json->shipping_price + $from_json->shipping_surcharge;

                    $shipping_price += ($from_json->same_next_surcharge);
                    if ($shipping_price < 0) {
                        $shipping_price = 0;
                    }
                }


                $products_price = $this->cart_total;

                $corporate_discount = $products_price * $from_json->corporate_discount / 100;
                if ($donate) {
                    $donated_price = $this->get_current_donation_id_price($donate);
                    $used_donate_id = $donate;
                }
                $shipping_tax = $shipping_price * $from_json->shipping_tax_rate;
                $products_tax = ($products_price - $this->coupon_discount - $corporate_discount) * $from_json->products_tax_rate;

                $taxes_price = $shipping_tax + $products_tax;

                $total_price = $products_price + $shipping_price + $taxes_price - $this->coupon_discount - $corporate_discount;

                $return['result'] = true;
                $return['products_price'] = number_format($products_price, 2, '.', '');
                $return['shipping_tax'] = number_format($shipping_tax, 2, '.', '');
                $return['products_tax'] = number_format($products_tax, 2, '.', '');
                $return['taxes_price'] = number_format($taxes_price, 2, '.', '');
                $return['donated_price'] = number_format($donated_price, 2, '.', '');
                $return['shipping_price'] = number_format($shipping_price, 2, '.', '');
                $return['corporate_discount'] = number_format($corporate_discount, 2, '.', '');
                $return['coupon_discount'] = number_format($this->coupon_discount, 2, '.', '');
                $return['total_price'] = number_format($total_price, 2, '.', '');

                $_SESSION['checkout_ajax'] = array();

                $_SESSION['checkout_ajax']['products_price'] = $return['products_price'];
                $_SESSION['checkout_ajax']['taxes_price'] = $return['taxes_price'];
                $_SESSION['checkout_ajax']['shipping_price'] = $return['shipping_price'];
                $_SESSION['checkout_ajax']['corporate_discount'] = $return['corporate_discount'];
                $_SESSION['checkout_ajax']['coupon_discount'] = $return['coupon_discount'];
                $_SESSION['checkout_ajax']['donated_price'] = $return['donated_price'];
                $_SESSION['checkout_ajax']['used_donate_id'] = $used_donate_id;

                $_SESSION['checkout_ajax']['total_price'] = $return['total_price'];

                $_SESSION['checkout_ajax']['shipping_tax_rate'] = $return['shipping_tax_rate'];
                $_SESSION['checkout_ajax']['products_tax_rate'] = $return['products_tax_rate'];

                $_SESSION['checkout_ajax']['shipping_tax'] = $return['shipping_tax'];
                $_SESSION['checkout_ajax']['products_tax'] = $return['products_tax'];

                $_SESSION['checkout_ajax']['user_id'] = $my->id;
                $_SESSION['checkout_ajax']['user_name'] = $my->username;
                $_SESSION['checkout_ajax']['account_email'] = $my->email;

                $_SESSION['checkout_ajax']['free_shipping'] = $json['free_shipping'];

                $_SESSION['checkout_ajax']['coupon_code'] = $_SESSION['checkout_coupon_code'];

                $_SESSION['checkout_ajax']['delivery_date'] = $_SESSION['checkout_delivery_date'];
                $_SESSION['checkout_ajax']['shipping_method'] = $_SESSION['checkout_shipping_method'];
                $_SESSION['checkout_ajax']['user_info_id'] = $_SESSION['checkout_ship_to_info_id'];
                $_SESSION['checkout_ajax']['vendor_currency_string'] = $_SESSION['vendor_currency'];

                /*
                  occasion: $j("select[name='customer_occasion']").val(),
                  card_msg: $j("textarea[name='card_msg']").val(),
                  signature: $j("textarea[name='signature']").val(),
                  card_comment: $j("textarea[name='card_comment']").val(),

                  payment_method: $j("select[name='payment_method']").val(),
                  name_on_card: $j("input[name='name_on_card']").val(),
                  credit_card_number: $j("input[name='credit_card_number']").val(),
                  credit_card_security_code: $j("input[name='credit_card_security_code']").val(),
                  expire_month: $j("select[name='expire_month']").val(),
                  expire_year: $j("select[name='expire_year']").val(),
                  find_us: $j("input[name='find_us']:checked").val(),
                  pick_up_location: $j('#pick_up_list option:selected').val() */
            }
        } else {
            $return['result'] = false;
            $return['error'] = $from_json->error;
        }

        return json_encode($return);
    }

    private function GetCartPrice()
    {
        /*
          global $database,$my;
          $cart_price = 0;
          $ShopperGroupDiscount	= 0;
          $query 		= " SELECT SG.shopper_group_discount
          FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id
          WHERE  SVX.user_id = ". $my->id ." LIMIT 1";
          $database->setQuery($query);
          $ShopperGroupDiscount	= $database->loadResult();

          $nShopperGroupDiscount	= 0;
          if( !empty($ShopperGroupDiscount) ) {
          if( !empty($ShopperGroupDiscount) && $ShopperGroupDiscount > 0 ) {
          $nShopperGroupDiscount	= floatval($ShopperGroupDiscount) / 100;
          }
          }


          $cart = $_SESSION['cart'];
          for ($i=0;$i<$cart["idx"];$i++) {
          if( !empty($nShopperGroupDiscount) && $cart[$i]["apply_group_discount"] <= 0 ) {
          $cart[$i]["price"] 	= floatval( $cart[$i]["price_standard"])  - ( floatval( $cart[$i]["price_standard"]) * floatval($nShopperGroupDiscount) );
          }
          }
          if (is_array($cart)) {
          foreach ($cart as $product) {

          $cart_price += $product['quantity'] * $product['price'];
          }
          }
          $_SESSION['cart']= $cart;
          return $this->cart_total = number_format($cart_price, 2);
         */
        $cart_price = 0;

        if (is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product) {
                $cart_price += $product['quantity'] * $product['price'];
            }
        }

        return $this->cart_total = number_format($cart_price, 2);
    }

    private function GetUserRating()
    {
        global $database, $my;

        $return = array();
        $return['error'] = array();

        $query = "SELECT `rate` FROM `jos_vm_users_rating` WHERE `user_id`=" . (int)$my->id . "";
        $database->setQuery($query);
        $rate = $database->loadResult();

        if ($rate == 1) {
            $query = "UPDATE `jos_vm_user_ccards` SET `block`=1 WHERE `user_id`=" . (int)$my->id . "";
            $database->setQuery($query);

            $database->query();

            $return['result'] = false;
            $return['error'][] = 'Unfortunately we unable to process your order. Try to place order in another company.';
        } else {
            $return['result'] = true;
        }

        return json_encode($return);
    }

    private function GetCouponDiscount()
    {
        global $database;

        $coupon_discount = 0;

        if (isset($_SESSION['checkout_coupon_code']) and !empty($_SESSION['checkout_coupon_code'])) {
            $return = array();

            $query = "SELECT `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($_SESSION['checkout_coupon_code']) . "'";

            $coupon_result = false;
            $database->setQuery($query);
            $database->loadObject($coupon_result);

            if ($coupon_result) {
                $coupon_value = number_format($coupon_result->coupon_value, 2);

                if ($coupon_result->percent_or_total == 'total') {
                    if ($this->cart_total >= $coupon_value) {
                        $coupon_discount = $coupon_value;
                    } else {
                        $coupon_discount = $this->cart_total;
                    }
                } elseif ($coupon_result->percent_or_total == 'percent') {
                    $coupon_discount = $this->cart_total / 100 * $coupon_value;
                }
            }
        }

        return $this->coupon_discount = number_format($coupon_discount, 2);
    }

    private function GetProductOptions()
    {
        global $database;
        $j = 0;
        if (is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product) {
                if ($product['product_id'] > 0) {
                    $j++;
                    $products_id[] = (int)$product['product_id'];
                }
            }

            $query = "SELECT `no_delivery`, `next_day_delivery`,`must_be_combined` FROM `jos_vm_product_options` WHERE `product_id` IN (" . implode(',', $products_id) . ")";

            $database->setQuery($query);
            $product_options_result = $database->loadObjectList();

            $this->free_shipping_product = 1;
            $this->free_shipping = 1;
            $this->next_day = 0;
            $this->must_be_combined = 0;

            foreach ($product_options_result as $product_option) {
                if ($product_option->next_day_delivery == 1) {
                    $this->next_day = 1;
                }
                if ($product_option->must_be_combined == 1) {
                    $this->must_be_combined = 1;
                }

                if ($product_option->no_delivery == 0) {
                    $this->free_shipping = 0;
                    $this->free_shipping_product = 0;
                }
            }

            if ($j != 1) {
                $this->must_be_combined = 0;
            }

            return true;
        }
    }

    private function GetPlatinumOptions()
    {
        global $database, $my;

        if (is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product) {
                if ($product['product_id'] > 0) {
                    $products_id[] = (int)$product['product_id'];
                }
            }

            $query = "SELECT `product_id` FROM `jos_vm_product`  WHERE (`product_sku` LIKE 'PC-01' OR `product_sku` LIKE 'PC-01SP') AND `product_id` IN (" . implode(',', $products_id) . ")";

            $new_platinum_result = false;
            $database->setQuery($query);
            $database->loadObject($new_platinum_result);


            /*
              $sql = "DELETE FROM `tbl_platinum_club` WHERE `cdate`<=UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 1 YEAR))";
              $sql = "SET `tbl_platinum_club` SET `end_datetime`=NOW() WHERE `start_datetime`<=DATE_SUB(NOW(),INTERVAL 1 YEAR)";
              $database->setQuery($sql);
              $database->query();
             */

            $query = "SELECT `id` FROM `tbl_platinum_club` WHERE `user_id`=" . $my->id . " AND `end_datetime` IS NULL";

            $old_platinum_result = false;
            $database->setQuery($query);
            $database->loadObject($old_platinum_result);

            $this->shipping_discount = 0;

            if ($new_platinum_result or $old_platinum_result) {
                $_SESSION['platinum_cart'] = 1;
                //$this->free_shipping = 1;
                $this->shipping_discount = 14.95;
            }

            return true;
        }
    }

    private function GetFreeShippingOptions()
    {
        global $database;

        $query = "SELECT `price` FROM `jos_freeshipping_price` WHERE `public`=1";

        $free_shipping_result = false;
        $database->setQuery($query);
        $database->loadObject($free_shipping_result);

        if ($free_shipping_result) {
            if ($free_shipping_result->price <= $this->cart_total) {
                $this->free_shipping = 1;
            }
        }

        return true;
    }

    private function GetDeliveryOptions($delivery_date)
    {
        global $database;

        $this->shipping_surcharge = 0;
        $this->shipping_unavaliable = 0;

        $query = "SELECT `type`, `calendar_day`, `price` FROM `tbl_delivery_options`";

        $database->setQuery($query);
        $delivery_options_result = $database->loadObjectList();

        $ShippingSurcharge = array();
        $unAvailable = array();
        $freeshipping = array();

        if ($delivery_options_result) {
            foreach ($delivery_options_result as $delivery_option) {
                if (date('Y-n-j', strtotime($delivery_option->calendar_day)) == $delivery_date) {
                    if ($delivery_option->type == 'surcharge') {
                        $this->shipping_surcharge = number_format($delivery_option->price, 2, '.', '');
                    } elseif ($delivery_option->type == 'free') {
                        $this->free_shipping = 1;
                    } elseif ($delivery_option->type == 'unavaliable') {
                        $this->shipping_unavailable = 1;
                        $this->checkout_errors[] = 'On this date delivery is not available.';
                    }
                }
            }
        }

        $query = "SELECT `date`, `amount` FROM `tbl_shipping_surcharge`";

        $database->setQuery($query);
        $delivery_options_result = $database->loadObjectList();

        if ($delivery_options_result) {
            foreach ($delivery_options_result as $delivery_option) {
                if (date('Y-n-j', strtotime($delivery_option->date)) == $delivery_date) {
                    $this->shipping_surcharge += number_format($delivery_option->amount, 2, '.', '');
                }
            }
        }

        return true;
    }

    private function GetBadPostcode($post_code)
    {
        global $database;

        $query = "SELECT `postal_code` FROM `jos_postcode_warehouse` WHERE `postal_code`='" . $post_code . "' AND published=1 AND  `deliverable`=0 LIMIT 1";
        $database->setQuery($query);
        $bad_postcode_result = $database->loadResult();

        if ($bad_postcode_result) {
            $this->shipping_unavailable = 1;
            $this->checkout_errors[] = 'Sorry we currently do not offer delivery to the postal code as input.';
        }

        return true;
    }

    private function GetSameNextDay($delivery_date, $zip)
    {
        global $database;

        date_default_timezone_set('Australia/Sydney');

        $this->same_next_surcharge = 0;

        $default_timezone = date_default_timezone_get();

        if (substr($zip, 0, 1) == '6') {
            date_default_timezone_set('Australia/Perth');
        }

        $same_day_surcharge = '5';
        $next_day_surcharge = '0';

        $same_day_hour = 13;
        $next_day_hour = 16;

        $same_day_limit = $same_day_hour * 60;
        $next_day_limit = $next_day_hour * 60;

        $hour_now = intval(date('H'));
        $minute_now = intval(date('i'));

        $time_now = $hour_now * 60 + $minute_now;

        if (date('Y-n-j') == $delivery_date) {
            if ($same_day_limit > $time_now) {
                $this->same_next_surcharge = $same_day_surcharge;
            }
        } elseif (date('Y-n-j', strtotime('+1 day')) == $delivery_date) {
            if ($next_day_limit > $time_now) {
                $this->same_next_surcharge = $next_day_surcharge;
            } elseif ($next_day_limit < $time_now) {
                $this->same_next_surcharge = $same_day_surcharge;
            }
        } elseif (date('Y-n-j', strtotime('+2 day')) == $delivery_date) {
            if ($next_day_limit < $time_now) {
                $this->same_next_surcharge = $next_day_surcharge;
            }
        }

        date_default_timezone_set($default_timezone);

        return true;
    }

    private function GetCorporateDiscount()
    {
        global $database, $my;

        $this->corporate_discount = 0;

        //$query = "SELECT `SG`.`discount_price` FROM `jos_vm_shopper_vendor_xref` AS `SVX` INNER JOIN `jos_vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id` WHERE `SVX`.`user_id`=" . $my->id . " LIMIT 1";
        $query = "SELECT `SG`.`shopper_group_discount` FROM `jos_vm_shopper_vendor_xref` AS `SVX` INNER JOIN `jos_vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id` WHERE `SVX`.`user_id`=" . $my->id . " LIMIT 1";

        $corporate_discount_result = false;
        $database->setQuery($query);
        $database->loadObject($corporate_discount_result);

        if ($corporate_discount_result) {
            //$this->corporate_discount = $corporate_discount_result->discount_price;
            $this->corporate_discount = $corporate_discount_result->shopper_group_discount;
        }

        return true;
    }

    public function GetShippingPrice($delivery_date = '', $ship_to_info_id = '')
    {
        global $database, $my;

        $return = array();

        if (!empty($delivery_date)) {
            $a_delivery_date = explode('/', $delivery_date);

            $_SESSION['checkout_delivery_date'] = $delivery_date;

            $month = $a_delivery_date[0];
            $day = $a_delivery_date[1];
            $year = $a_delivery_date[2];

            $query = "SELECT `shipping_rate_value` FROM `jos_vm_shipping_rate` WHERE `shipping_rate_id`=" . (int)$_SESSION['checkout_shipping_method'] . " LIMIT 1";

            $shipping_method_result = false;
            $database->setQuery($query);
            $database->loadObject($shipping_method_result);

            if ($shipping_method_result) {
                $this->shipping_price = $shipping_method_result->shipping_rate_value;
                $this->shipping_unavailable = 0;

                //$query = "SELECT `user_info_id`, `state`, `country`, `zip` FROM `jos_vm_user_info` WHERE `user_info_id`='" . $database->getEscaped($ship_to_info_id) . "' AND `user_id`=" . $my->id . " AND `address_type`='ST'";
                $query = "SELECT `user_info_id`, `state`, `country`, `zip` FROM `jos_vm_user_info` WHERE `user_info_id`='" . $database->getEscaped($ship_to_info_id) . "'  AND `address_type`='ST'";

                $user_info_result = false;
                $database->setQuery($query);
                $database->loadObject($user_info_result);

                if ($user_info_result) {
                    $_SESSION['checkout_ship_to_info_id'] = $ship_to_info_id;

                    $query = "SELECT `tax_rate` FROM `jos_vm_tax_rate` WHERE `tax_state`='" . $database->getEscaped($user_info_result->state) . "' AND `tax_country`='" . $database->getEscaped($user_info_result->country) . "'";

                    $tax_delivery_result = false;
                    $database->setQuery($query);
                    $database->loadObject($tax_delivery_result);

                    if ($tax_delivery_result) {

                        $this->shipping_tax_rate = 0; //$tax_delivery_result->tax_delivery_rate;
                        $this->products_tax_rate = 0; //$tax_delivery_result->tax_rate;

                        $this->GetCartPrice();
                        $this->GetProductOptions();
                        $this->GetPlatinumOptions();
                        //$this->GetFreeShippingOptions();
                        $this->GetDeliveryOptions($year . '-' . $month . '-' . $day);
                        $this->GetBadPostcode($user_info_result->zip);
                        $this->GetSameNextDay($year . '-' . $month . '-' . $day, $user_info_result->zip);
                        $this->GetCorporateDiscount();

                        if ($this->must_be_combined == 0) {
                            if ($this->shipping_unavailable == 1) {
                                $return['result'] = false;
                                $return['error'] = $this->checkout_errors;
                            } else {
                                $return['result'] = true;

                                $return['cart_total'] = $this->cart_total;
                                $return['free_shipping'] = $this->free_shipping;
                                $return['free_shipping_product'] = $this->free_shipping_product;
                                $return['shipping_surcharge'] = $this->shipping_surcharge;
                                $return['shipping_discount'] = $this->shipping_discount;
                                $return['same_next_surcharge'] = $this->same_next_surcharge;
                                $return['shipping_price'] = $this->free_shipping == 1 ? 0 : $this->shipping_price;
                                $return['shipping_tax_rate'] = $this->shipping_tax_rate;
                                $return['products_tax_rate'] = $this->products_tax_rate;
                                $return['corporate_discount'] = $this->corporate_discount;

                                $return['error'] = $this->checkout_errors;
                            }
                        } else {
                            $return['result'] = false;
                            $return['error'][] = 'The product must be combined. Add one more product.';
                        }
                    } else {
                        $return['result'] = false;
                        $return['error'][] = 'Error tax rate.';
                    }
                } else {
                    $return['result'] = false;
                    $return['error'][] = 'Error shipping address.';
                }
            } else {
                $return['result'] = false;
                $return['error'][] = 'Error delivery option.';
            }
        } else {
            $return['result'] = false;
            $return['error'][] = 'Error delivery date.';
        }

        return json_encode($return);
    }

    public function SetShippingMethod($shipping_method)
    {
        global $database;

        $return = array();

        $query = "SELECT `shipping_rate_id` FROM `jos_vm_shipping_rate` WHERE `shipping_rate_id`=" . (int)$shipping_method . "";

        $shipping_method_result = false;
        $database->setQuery($query);
        $database->loadObject($shipping_method_result);

        if ($shipping_method_result) {
            $_SESSION['checkout_shipping_method'] = $shipping_method_result->shipping_rate_id;

            $return['result'] = true;
        } else {
            $return['result'] = false;
            $return['error'] = 'Shipping method error.';
        }

        return json_encode($return);
    }

    public function SetCouponCode($coupon_code)
    {
        global $database, $my;

        $return = array();

        $query = "SELECT `coupon_code`, `percent_or_total`, `coupon_value` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($coupon_code) . "'";

        $coupon_result = false;
        $database->setQuery($query);
        $database->loadObject($coupon_result);

        if ($coupon_result) {
            if (strpos($coupon_result->coupon_code, 'PC-') !== false) {
                $query = "SELECT `id` FROM `tbl_platinum_club` WHERE `user_id`=" . $my->id . " AND `end_datetime` IS NULL";

                $platinum_result = false;
                $database->setQuery($query);
                $database->loadObject($platinum_result);

                if ($platinum_result) {
                    $return['result'] = true;
                    $return['info'] = 'You are already in the Platinum Club, you can use this coupon for another account.';
                } else {
                    $query = "INSERT INTO `tbl_platinum_club` (`user_id`, `start_datetime`) VALUES (" . $my->id . ", NOW())";

                    $database->setQuery($query);

                    if (!$database->query()) {
                        $return['result'] = false;
                        $return['error'] = 'Error.';
                    } else {
                        $query = "DELETE FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($coupon_result->coupon_code) . "'";
                        $database->setQuery($query);
                        $database->query();

                        $return['result'] = true;
                        $return['info'] = 'Congratulations, you are in the Platinum Club.';
                    }
                }
            } else {
                $_SESSION['checkout_coupon_code'] = $coupon_result->coupon_code;

                $return['result'] = true;

                if ($coupon_result->percent_or_total == 'percent') {
                    $coupon_discount = $coupon_result->coupon_value . '%';
                } elseif ($coupon_result->percent_or_total == 'total') {
                    $coupon_discount = '$' . $coupon_result->coupon_value;
                }

                $return['info'] = 'Your coupon is applied. Discount is ' . $coupon_discount . '.';
            }
        } else {
            $return['result'] = false;
            $return['error'] = 'Coupon does not exist or already been used.';
        }

        return json_encode($return);
    }

    public function SetShippingAddress($checkout_user_info_id)
    {
        global $database, $my;

        $return = array();

        $query = "SELECT `user_info_id` FROM `jos_vm_user_info` WHERE `user_info_id`='" . $database->getEscaped($checkout_user_info_id) . "' AND `user_id`=" . $my->id . " AND `address_type`='ST'";

        $user_info_result = false;
        $database->setQuery($query);
        $database->loadObject($user_info_result);

        if ($user_info_result) {
            $_SESSION['checkout_user_info_id'] = $user_info_result->user_info_id;

            $return['result'] = true;
        } else {
            $return['result'] = false;
        }

        return json_encode($return);
    }

}

class CartAction
{

    public function GetCart()
    {
        $return = array();

        $real_cart = $this->GetRealCart();

        $return['products'] = $real_cart['products'];
        $return['cart_price'] = $real_cart['cart_price'];
        $return['cart_items'] = $real_cart['cart_items'];
        $return['corporate_discount'] = $real_cart['corporate_discount'];
        $return['coupon_discount'] = $real_cart['coupon_discount'];
        $return['cart_total'] = $real_cart['cart_total'];

        return json_encode($return, JSON_FORCE_OBJECT);
    }

    private function GetRealCart()
    {
        global $database, $my,$mainframe;

        $return = array();

        $return['products'] = array();

        $corporate_discount = 0;
        $query = "SELECT 
        `sg`.`shopper_group_discount` 
        FROM `jos_vm_shopper_vendor_xref` AS `svx` 
        INNER JOIN `jos_vm_shopper_group` AS `sg` 
            ON `sg`.`shopper_group_id`=`svx`.`shopper_group_id`  	
        WHERE `svx`.`user_id`=" . (int)$my->id . " LIMIT 1";

        $database->setQuery($query);
        $corporate_discount = $database->loadResult();
        $corporate_discount = floatval($corporate_discount) / 100;

        $cart_price = 0;
        $cart_items = 0;
        if(isset($_SESSION['cart'])){

        for ($i = 0; $i < $_SESSION['cart']['idx']; $i++) {
            if (isset($_SESSION['cart'][$i]['product_id']) and (int)$_SESSION['cart'][$i]['product_id'] > 0) {
                $product_info = $this->GetProductInfo((int)$_SESSION['cart'][$i]['product_id'], $_SESSION['cart'][$i]['select_bouquet'], ($_SESSION['cart'][$i]['select_sub']??''));
                $product_info['quantity'] = (int)$_SESSION['cart'][$i]['quantity'];

                $return['products'][] = $product_info;
                $cart_price += $_SESSION['cart'][$i]['price'] * (int)$_SESSION['cart'][$i]['quantity'];

                $cart_items = $cart_items + $product_info['quantity'];
            }
        }

        }
        $coupon_discount = 0;

        if (isset($_SESSION['checkout_ajax']['coupon_code']) and !empty($_SESSION['checkout_ajax']['coupon_code'])) {
            $query = "SELECT `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($_SESSION['checkout_ajax']['coupon_code']) . "'";

            $coupon_result = false;
            $database->setQuery($query);
            $database->loadObject($coupon_result);

            if ($coupon_result) {
                $coupon_value = number_format($coupon_result->coupon_value, 2);

                if ($coupon_result->percent_or_total == 'total') {
                    if ($cart_price >= $coupon_value) {
                        $coupon_discount = $coupon_value;
                    } else {
                        $coupon_discount = $cart_price;
                    }
                } elseif ($coupon_result->percent_or_total == 'percent') {
                    $coupon_discount = $cart_price / 100 * $coupon_value;
                }
            }
        }

        $return['cart_price'] = number_format(round($cart_price, 2), 2, '.', '');
        $return['corporate_discount'] = number_format($corporate_discount * $return['cart_price'], 2, '.', '');
        $return['coupon_discount'] = number_format($coupon_discount, 2, '.', '');
        $return['cart_total'] = number_format(($return['cart_price'] - $return['corporate_discount'] - $return['coupon_discount']), 2, '.', '');
        $return['cart_items'] = $cart_items;

        return $return;
    }

    private function GetProductInfo($product_id, $select_bouquet, $select_sub)
    {
        global $database, $mosConfig_live_site,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url;

        $query = "SELECT 
            `p`.`product_id`,
            `p`.`product_sku`,
            `s`.`small_image_link_jpeg`,
            `s`.`small_image_link_webp`,
            `p`.`product_name`,
            `po`.`product_type`,
            `po`.`promo`,
            `c`.`category_id`
        FROM `jos_vm_product` AS `p`
        LEFT JOIN jos_vm_product_options as po on po.product_id = p.product_id
        LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_product_category_xref` AS `x` ON `x`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`x`.`category_id`
        WHERE `p`.`product_id`=" . $product_id . "";

        $product_info = false;
        $database->setQuery($query);
        $database->loadObject($product_info);

        $product_price_result = $this->GetProductPrice($product_id);

        if ($product_price_result) {
            $price_info['product_price'] = $product_price_result->product_price;
            $price_info['saving_price'] = $product_price_result->saving_price;
            $price_info['product_currency'] = $product_price_result->product_currency;
        } else {
            $price_info['product_price'] = "";
            $price_info['saving_price'] = "";
            $price_info['product_currency'] = $_SESSION['vendor_currency'];
        }

        if (!empty($price_info['saving_price']) and $price_info['saving_price'] > 0 and $price_info['product_price'] >= 0) {
            $product_price = $price_info['product_price'] - $price_info['saving_price'];
        } else {
            $product_price = $price_info['product_price'];
        }

        if($product_price_result->promotion_discount) {
            $product_price = round($product_price - $product_price*$product_price_result->promotion_discount/100,2);
        }
        if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
            $product_price = round($product_price - $product_price * $product_price_result->discount_for_customer/100,2);
        }
        $sql = "SELECT " . $database->getEscaped($select_bouquet) . " FROM `jos_vm_product_options` WHERE `product_id`=" . $product_info->product_id . " LIMIT 1";
        $database->setQuery($sql);
        $bouquet_add_price = $database->loadResult();

        $product_price += $bouquet_add_price;

        if (!empty($select_sub)) {
            $sql = "SELECT " . $database->getEscaped($select_sub) . " FROM `jos_vm_product_options` WHERE `product_id`=" . $product_info->product_id . " LIMIT 1";
            $database->setQuery($sql);
            $select_sub_price = $database->loadResult();

            $product_price = $select_sub_price;
        }



        $product_info_array = array(
            'id' => $product_info->product_id,
            'sku' => $product_info->product_sku,
            'name' => $product_info->product_name,
            'promo' => $product_info->promo,
            'price' => number_format(floatval($product_price), 2, '.', ''),
            'image' => $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product_info->small_image_link_jpeg : $product_info->small_image_link_webp),
            'url' => '/index.php?option=com_virtuemart&page=shop.product_details&product_id=' . $product_info->product_id . '&category_id=' . $product_info->category_id,
            'select_bouquet' => $select_bouquet,
            'select_sub' => $select_sub,
            'promotion_discount' => $product_price_result->promotion_discount,
            'product_type' => $product_info->product_type
        );

        return $product_info_array;
    }

    private function GetShopperGroupDiscount($user_id)
    {
        global $database;

        $ShopperGroupDiscount = 0;

        $query = "SELECT 
            `SG`.`shopper_group_discount`
        FROM `jos_vm_shopper_vendor_xref` AS `SVX` 
            INNER JOIN `jos_vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id`  	
        WHERE  `SVX`.`user_id`=" . $user_id . " LIMIT 1";
        $database->setQuery($query);
        $ShopperGroupDiscount = $database->loadResult();

        $nShopperGroupDiscount = 0;

        if (!empty($ShopperGroupDiscount)) {
            if (!empty($ShopperGroupDiscount) and $ShopperGroupDiscount > 0) {
                $nShopperGroupDiscount = floatval($ShopperGroupDiscount) / 100;
            }
        }

        return floatval($nShopperGroupDiscount);
    }

    private function AddToSession($session_data)
    {
        $k = ($_SESSION['cart']['idx']) ? $_SESSION['cart']['idx'] : 0;

        $_SESSION['cart'][$k]['price'] = $session_data['price'];
        $_SESSION['cart'][$k]['promotion_discount'] = $session_data['promotion_discount'];
        $_SESSION['cart'][$k]['saved_price'] = $session_data['saved_price'];
        $_SESSION['cart'][$k]['price_standard'] = $session_data['price_standard'];
        $_SESSION['cart'][$k]['quantity'] = $session_data['quantity'];
        $_SESSION['cart'][$k]['product_id'] = $session_data['product_id'];
        $_SESSION['cart'][$k]['product_type'] = $session_data['product_type'];
        $_SESSION['cart'][$k]['hasSpecialDiscount'] = $session_data['hasSpecialDiscount'];
        $_SESSION['cart'][$k]['containAlcohol'] = $session_data['containAlcohol'];
        $_SESSION['cart'][$k]['description'] = $session_data['description'];
        $_SESSION['cart'][$k]['not_apply_discount'] = $session_data['not_apply_discount'];
        $_SESSION['cart'][$k]['apply_group_discount'] = $session_data['apply_group_discount'];
        $_SESSION['cart'][$k]['pick_up'] = $session_data['pick_up'];
        $_SESSION['cart'][$k]['select_bouquet'] = $session_data['select_bouquet'];
        $_SESSION['cart'][$k]['select_sub'] = $session_data['select_sub'];

        $_SESSION['cart']['idx']++;
    }

    private function GetApplyDiscount($product_id)
    {
        global $database;

        $query = "SELECT `not_apply_discount` FROM `jos_vm_product` WHERE `product_id`=" . $product_id . "";
        $database->setQuery($query);
        $not_apply_discount = $database->loadResult();

        return (int)$not_apply_discount;
    }

    private function GetProductPrice($product_id)
    {
        global $database;

        $query = "SELECT `vendor_id` FROM `jos_vm_product` WHERE `product_id`=" . $product_id . "";
        $vendor_result = false;
        $database->setQuery($query);
        $database->loadObject($vendor_result);
        $vendor_id = $vendor_result->vendor_id;

        $query = "SELECT `shopper_group_id` FROM `jos_vm_shopper_group` WHERE `vendor_id`=" . $vendor_id . " AND `default`='1'";
        $shopper_group_result = false;
        $database->setQuery($query);
        $database->loadObject($shopper_group_result);
        $default_shopper_group_id = $shopper_group_result->shopper_group_id;

        $query = "SELECT 
            `pp`.`product_price`,
            `pp`.`product_currency`, 
            `pp`.`saving_price`,
            `pp`.`discount_for_customer`,
            `pm`.`discount` as promotion_discount
        FROM `jos_vm_product` AS `p` 
        LEFT JOIN `jos_vm_product_price` AS `pp`  ON `pp`.`product_id`=`p`.`product_id`
        LEFT JOIN (SELECT 
                        CASE 
                            WHEN pmp.category_id > 0  THEN x.product_id
                            ELSE pmp.product_id
                        END AS `product_id`,pmp.discount,pmp.end_promotion
                        FROM `jos_vm_products_promotion` as pmp 
        left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
        WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
        WHERE `p`.`product_id`=" . $product_id . " AND `pp`.`shopper_group_id`=" . $default_shopper_group_id . "";

        $product_price_result = false;
        $database->setQuery($query);
        $database->loadObject($product_price_result);

        return $product_price_result;
    }

    private function getProductType($product_id)
    {
        global $database;

        $query = "SELECT `product_type` FROM `jos_vm_product_options` WHERE `product_id`=" . $product_id . "";
        $product_type_result = false;
        $database->setQuery($query);
        $database->loadObject($product_type_result);
        return $product_type_result->product_type;

    }

    private function checkProductContainAlcohol($product_id)
    {
        global $database;

        $query = "SELECT `contain_alcohol` FROM `jos_vm_product_options` WHERE `product_id`=" . $product_id . "";
        $product_type_result = false;
        $database->setQuery($query);
        $database->loadObject($product_type_result);
        return $product_type_result->contain_alcohol;

    }

    protected function checkAlreadyExistPromoProduct($products)
    {
        global $database;
        $existPromoProduct = false;
        if ($_SESSION['cart']['idx']) {
            $existProducts = [];
            for ($i = 0; $i < $_SESSION['cart']['idx']; $i++) {
                $existProducts[] = $_SESSION['cart'][$i]['product_id'];
            }
            $sql = "SELECT promo,product_id FROM #__vm_product_options WHERE product_id in (" . implode(',', $existProducts) . ") and promo='1'";
            $database->setQuery($sql);
            if ($database->loadResult()) {
                $existPromoProduct = true;
            }
        }
        $sql = "SELECT promo FROM #__vm_product_options WHERE product_id in (" . implode(',', $products) . ") and promo='1'";
        $database->setQuery($sql);
        $checkAddedProductPromo = $database->loadObjectList();
        if (($existPromoProduct && $checkAddedProductPromo) || count($checkAddedProductPromo) > 1) {
            return true;
        }
        return false;
    }

    public function AddToCart()
    {
        global $mosConfig_enable_fast_checkout;
    if($_SESSION['cart']['idx'] && isset($_SESSION['customerCantBuyFlowers'])){
        for ($i = 0; $i < $_SESSION['cart']['idx']; $i++) {
            if ($_SESSION['cart'][$i]['product_type'] == '1') {
              unset($_SESSION['cart'][$i]);
            }
        }
        unset($_SESSION['cart']['idx']);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $_SESSION['cart']['idx'] = count($_SESSION['cart']);
        unset($_SESSION['customerCantBuyFlowers']);
    }

        global $database, $my;
//        debug_backtrace()
//            echo "<pre>";print_r(debug_backtrace());die;
        $return = array();

        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'] > 0 ? (int)$_POST['quantity'] : 0;
        $pick_up = isset($_POST['pick_up']) ? (int)$_POST['pick_up'] : 0;

        $nShopperGroupDiscount = $this->GetShopperGroupDiscount($my->id);

        $updated = $update_idx = false;
        if ($product_id != 0) { // zero means we jsut want cart data
            $select_bouquet = isset($_POST['select_bouquet']) ? $_POST['select_bouquet'] : 'standard';
            $select_sub = isset($_POST['select_sub']) ? $_POST['select_sub'] : '';

            $extraPoducts = [];
            if ($_POST['extra_touches']) {
                $product_ides_arr = explode('&', $_POST['extra_touches']);
                foreach ($product_ides_arr as $p) {
                    $product_id_extra_arr = explode('=', $p);
                    $extraPoducts[] = $product_id_extra_arr[1];
                }
            }

            $return['alreadyExistPromoProduct'] = $this->checkAlreadyExistPromoProduct(array_merge($extraPoducts,[$product_id]));
            $return['enableFastCheckout'] = $mosConfig_enable_fast_checkout;
            if (!$return['alreadyExistPromoProduct']) {

            if (count($extraPoducts) > 0) {


                foreach ($extraPoducts as $product_id_extra) {
                    $select_bouquet_extra = 'standart';
                    $select_sub_extra = '';

                    $updated_extra = false;

                    for ($i = 0; $i < $_SESSION['cart']['idx']; $i++) {
                        if ($_SESSION['cart'][$i]['product_id'] == $product_id_extra) {
                            if ($_SESSION['cart'][$i]['product_id'] == $product_id_extra and $_SESSION['cart'][$i]['select_bouquet'] == $select_bouquet_extra and $_SESSION['cart'][$i]['select_sub'] == $select_sub_extra) {
                                $_SESSION['cart'][$i]['quantity'] += $quantity;

                                $updated_extra = true;
                            }
                        }
                    }

                    if ($updated_extra == false) {
                        $not_apply_discount = $this->GetApplyDiscount($product_id_extra);
                        $product_price = 0;

                        $product_extra_price_result = $this->GetProductPrice($product_id_extra);
                        $product_saved_price = $product_extra_price_result->saving_price;
                        if (!empty($product_extra_price_result->saving_price) and $product_extra_price_result->saving_price > 0 and $product_extra_price_result->product_price >= 0) {
                            $product_price = $product_extra_price_result->product_price - $product_extra_price_result->saving_price;
                        } else {
                            $product_price = $product_extra_price_result->product_price;
                        }

                        if (!empty($select_sub_extra)) {
                            $sql = "SELECT " . $database->getEscaped($select_sub_extra) . " FROM #__vm_product_options WHERE product_id = " . (int)$product_id_extra . " LIMIT 1";
                            $database->setQuery($sql);
                            $select_sub_price = $database->loadResult();

                            $product_price = $select_sub_price;
                        }

                        if($product_extra_price_result->promotion_discount) {
                            $product_price = round($product_price - $product_price*$product_extra_price_result->promotion_discount/100,2);
                        }
                        if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                            $product_price = round($product_price - $product_price*$product_extra_price_result->discount_for_customer/100,2);
                        }
                        $session_data = array(
                            'price' => floatval($product_price),
                            'promotion_discount' => $product_extra_price_result->promotion_discount,
                            'saved_price' => floatval($product_saved_price),
                            'price_standard' => '',
                            'quantity' => 1,
                            'product_id' => $product_id_extra,
                            'product_type' => $this->getProductType($product_id_extra),
                            'hasSpecialDiscount' => $_SESSION['enableSpecialDiscountInProductsForCustomer']??false,
                            'containAlcohol' => $this->checkProductContainAlcohol($product_id_extra),
                            'description' => '',
                            'not_apply_discount' => $not_apply_discount,
                            'apply_group_discount' => $nShopperGroupDiscount,
                            'pick_up' => 0,
                            'select_bouquet' => $select_bouquet_extra,
                            'select_sub' => $select_sub_extra
                        );

                        $this->AddToSession($session_data);
                    }
                }
            }

                $temp_idx = $_SESSION['cart']['idx'];

                for ($i = 0; $i < $_SESSION['cart']['idx']; $i++) {
                    if (isset($_SESSION['cart'][$i]['product_id']) and $_SESSION['cart'][$i]['product_id'] == $product_id) {
                        if ($_SESSION['cart'][$i]['select_bouquet'] == $select_bouquet and $_SESSION['cart'][$i]['select_sub'] == $select_sub) {
                            $_SESSION['cart'][$i]['quantity'] += $quantity;

                            $updated = true;
                        }
                        /*
                          else {
                          unset($_SESSION['cart'][$i]);

                          $update_idx = true;
                          } */
                    }
                }

                if ($update_idx == true) {
                    unset($_SESSION['cart'][$i], $_SESSION['cart']['idx']);

                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                    $_SESSION['cart']['idx'] = $temp_idx - 1;
                }

                if ($updated == false) {
                    $not_apply_discount = $this->GetApplyDiscount($product_id);

                    $product_price_result = $this->GetProductPrice($product_id);

                    if ($product_price_result) {
                        $price_info['product_price'] = $product_price_result->product_price;
                        $price_info['saving_price'] = $product_price_result->saving_price;
                        $price_info['product_currency'] = $product_price_result->product_currency;
                    } else {
                        $price_info['product_price'] = "";
                        $price_info['saving_price'] = "";
                        $price_info['product_currency'] = $_SESSION['vendor_currency'];
                    }

                    if (!empty($price_info['saving_price']) and $price_info['saving_price'] > 0 and $price_info['product_price'] >= 0) {
                        $product_price = $price_info['product_price'] - $price_info['saving_price'];
                    } else {
                        $product_price = $price_info['product_price'];
                    }

                    $product_price_standard = floatval($product_price);

                    if($product_price_result->promotion_discount) {
                        $product_price_standard = round($product_price_standard - $product_price_standard*$product_price_result->promotion_discount/100,2);
                    }
                    if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                        $product_price_standard = round($product_price_standard - $product_price_standard*$product_price_result->discount_for_customer/100,2);
                    }
                    $product_saved_price = floatval($price_info['saving_price']);
                    $sql = "SELECT " . $database->getEscaped($select_bouquet) . " FROM #__vm_product_options WHERE product_id = $product_id LIMIT 1";
                    $database->setQuery($sql);
                    $bouquet_add_price = $database->loadResult();

                    $product_price = $bouquet_add_price + $product_price_standard;

                    if (!empty($select_sub)) {
                        $sql = "SELECT " . $database->getEscaped($select_sub) . " FROM #__vm_product_options WHERE product_id = $product_id LIMIT 1";
                        $database->setQuery($sql);
                        $select_sub_price = $database->loadResult();

                        $product_price = $product_price_standard = $select_sub_price;
                    }

                    $session_data = array(
                        'price' => floatval($product_price),
                        'promotion_discount' => $product_price_result->promotion_discount,
                        'saved_price' => floatval($product_saved_price),
                        'price_standard' => floatval($product_price_standard),
                        'hasSpecialDiscount' => $_SESSION['enableSpecialDiscountInProductsForCustomer']??false,
                        'containAlcohol' => $this->checkProductContainAlcohol($product_id),
                        'quantity' => $quantity,
                        'product_id' => $product_id,
                        'product_type' => $this->getProductType($product_id),
                        'description' => isset($d['description']) ? $d['description'] : '',
                        'not_apply_discount' => $not_apply_discount,
                        'apply_group_discount' => $nShopperGroupDiscount,
                        'pick_up' => $pick_up,
                        'select_bouquet' => $select_bouquet,
                        'select_sub' => $select_sub
                    );

                    $this->AddToSession($session_data);
                }
            }
        }

        $real_cart = $this->GetRealCart();

        $return['products'] = $real_cart['products'];
        $return['cart_price'] = $real_cart['cart_price'];
        $return['cart_items'] = $real_cart['cart_items'];
        $return['coupon_discount'] = $real_cart['coupon_discount'];
        $return['corporate_discount'] = $real_cart['corporate_discount'];
        $return['cart_total'] = $real_cart['cart_total'];
//        $return['related_products'] = $this->show_related_product_list();

        return json_encode($return, JSON_FORCE_OBJECT);
    }
    function show_related_product_list() {
        global $database, $mosConfig_show_compare_at_price,$sef;


        $query = "SELECT 
                `p`.`product_id`, 
                `p`.`product_name`, 
                `p`.`product_sku`, 
                `p`.`product_thumb_image`, 
                `p`.`alias`, 
                `pp`.`product_price`,
                `pp`.`discount_for_customer`,
                `pm`.`discount` as promotion_discount,
                CASE 
                    WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                    ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                END AS `product_real_price`,
                `c`.`category_flypage`, 
                `c`.`category_id`, 
                `c`.`category_name`, 
                `c`.`alias` AS 'category_alias', 
                `fr`.`rating`, 
                `fr`.`review_count`,  
                `po`.`no_delivery`,  
                `po`.`promo`, 
                `pm`.`end_promotion`,  
                `po`.`product_out_of_season`
            FROM `jos_vm_product` AS `p`
                LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                LEFT JOIN (SELECT 
                                CASE 
                                    WHEN pmp.category_id > 0  THEN x.product_id
                                    ELSE pmp.product_id
                                END AS `product_id`,pmp.discount,pmp.end_promotion
                                FROM `jos_vm_products_promotion` as pmp 
                left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
                WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
                LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
                LEFT JOIN `tbl_product_fake_reviews` AS `fr` ON `fr`.`product_id`=`p`.`product_id` 
                WHERE `po`.`product_sold_out` = false and  `po`.`product_out_of_season` = false 
                and `p`.`product_related`='Y' and `p`.`product_publish`='Y'
            GROUP BY `p`.`product_sku` ORDER BY  RAND() limit 4";

        $database->setQuery($query);
        $products_obj = $database->loadObjectList();


        $related_products = '<div class="container-fluid products related_products"><div class="row">';

        if ($products_obj) {
            foreach ($products_obj as $product_obj) {
                $product_old_price = number_format(round($product_obj->product_price, 2), 2, '.', '');
                $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
                if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                    $product_real_price = round($product_real_price - $product_real_price*$product_obj->discount_for_customer/100,2);
                }
                $product_rating = round($product_obj->rating, 1);
                $savingPrice = $product_old_price - $product_real_price;
                $link = $sef->getCanonicalProduct($product_obj->alias, true);


                $related_products .= '<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 wrapper" price_ordering="' . $product_real_price . '" rating_ordering="' . $product_rating . '">
                                <div class="inner">
                                    <a class="product-title" href="' . $link . '">';

                if ($product_obj->promotion_discount) {

                    if(date("Y-m-d") == $product_obj->end_promotion || $product_obj->end_promotion == '0000-00-00') {
                        $related_products .= '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
                    } else {
                        $related_products .= '<div class="new promotion_product">
                                            <span>Sale Ends In: </span> 
                                            <span class="promotion_countdown promotion_product_' . $product_obj->product_id . '" product_id="' . $product_obj->product_id . '" date_end="' . date("m/d/Y", strtotime($product_obj->end_promotion)) . '"></span>
                                        </div>';
                    }
                };

                $related_products .= '<div class="product-image">
                                                <img class="product_image_real" src="/components/com_virtuemart/shop_image/product/' . $product_obj->product_thumb_image . '" alt="name: ' . $product_obj->product_name . '">
                                            </div>
                                        <span class="product-title">' . $product_obj->product_name . '</span>
                                    </a>';

                if ($product_old_price != $product_real_price && $mosConfig_show_compare_at_price) {
                    $related_products .= '<div style="font-size: 15px">Compare at: <span class="old_price"><s>$' . $product_old_price . '</s></span></div>';
                }

                if ($product_obj->product_real_price == '0.00' && $product_obj->no_delivery == 0 && $product_obj->promo == '0') {
                    $related_products .= '<a style="display: block;text-align: center;margin: 20px auto;" href="tel:1800905147"><div class="add">Call For Pricing</div></a>';
                } else {
                    $related_products .= '<div style="font-size: 14px;color: #A40001;font-weight: bold;">Bloomex Price: <span class="price">$' . $product_real_price . '</span></div>
                                        <div class="form-add-cart" id="div_' . $product_obj->product_id . '">
                                            <form action="/index.php" method="post" name="addtocart" id="formAddToCart_' . $product_obj->product_id . '">
                                                <input name="quantity_' . $product_obj->product_id . '" class="inputbox" type="hidden" size="3" value="1">


                                                <div class="add" product_id="' . $product_obj->product_id . '">Add to Cart</div>

                                                <input type="hidden" name="category_id_' . $product_obj->product_id . '" value="' . $product_obj->category_id . '">
                                                <input type="hidden" name="product_id_' . $product_obj->product_id . '" value="' . $product_obj->product_id . '">
                                                <input type="hidden" name="price_' . $product_obj->product_id . '" value="' . $product_real_price . '">
                                                <input type="hidden" name="sku_' . $product_obj->product_id . '" value="' . $product_obj->product_sku . '">
                                                <input type="hidden" name="name_' . $product_obj->product_id . '" value="' . $product_obj->product_name . '">
                                                <input type="hidden" name="discount_' . $product_obj->product_id . '" value="' . $savingPrice . '">
                                                <input type="hidden" name="category_' . $product_obj->product_id . '" value="' . $product_obj->category_name . '">

                                            </form>
                                        </div>';

                }
                $related_products .= '</div></div>';

            }
        }

        $related_products .= '</div></div>';
        return $related_products;

    }
    public function RemoveFromCart()
    {
        $return = array();

        $product_id = (int)$_POST['product_id'];
        $product_i = (int)$_POST['product_i'];

        $temp_idx = $_SESSION['cart']['idx'];
        $update_idx = false;

        foreach ($_SESSION['cart'] as $key => $value) {
            if ((int)trim($value['product_id']) == $product_id ) {
                unset($_SESSION['cart'][$key]);
                $update_idx = true;
            }
        }

        if ($update_idx) {
            unset($_SESSION['cart']['idx']);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $_SESSION['cart']['idx'] = $temp_idx - 1;
        }

        $real_cart = $this->GetRealCart();

        $return['products'] = $real_cart['products'];
        $return['cart_price'] = $real_cart['cart_price'];
        $return['cart_items'] = $real_cart['cart_items'];
        $return['coupon_discount'] = $real_cart['coupon_discount'];
        $return['corporate_discount'] = $real_cart['corporate_discount'];
        $return['cart_total'] = $real_cart['cart_total'];
        $return['alcohol_exist'] = checkShoppingCartContainAlcohol();

        return json_encode($return, JSON_FORCE_OBJECT);
    }

    public function SetQuantityCart()
    {
        global $database;
        $return = array();
        $new_subtotal_product = 0;
        $product_id = (int)$_POST['product_id'];
        $product_i = (int)$_POST['product_i'];
        $new_val = (int)$_POST['new_val'] > 0 ? (int)$_POST['new_val'] : 1;
        $sql = "SELECT promo FROM #__vm_product_options WHERE product_id = $product_id LIMIT 1";
        $database->setQuery($sql);
        if ($database->loadResult()) {
            $new_val = 1;
        }
        foreach ($_SESSION['cart'] as $key => $value) {
            if (((int)trim($value['product_id']) == $product_id)) {
                $_SESSION['cart'][$key]['quantity'] = $new_val;
                $new_subtotal_product = number_format($new_val * $_SESSION['cart'][$key]['price'], 2, '.', '');
            }
        }

        $real_cart = $this->GetRealCart();

        $return['products'] = $real_cart['products'];
        $return['cart_price'] = $real_cart['cart_price'];
        $return['cart_items'] = $real_cart['cart_items'];
        $return['corporate_discount'] = $real_cart['corporate_discount'];
        $return['coupon_discount'] = $real_cart['coupon_discount'];
        $return['cart_total'] = $real_cart['cart_total'];
        $return['new_subtotal_product'] = $new_subtotal_product;

        return json_encode($return, JSON_FORCE_OBJECT);
    }

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

    return $json;
}

function get_google_place()
{
    $return['msg'] = 'empty';
    $place = urlencode(strtolower($_REQUEST['place']));
    $query_string = urlencode(strtolower($_REQUEST['place'] . ' ' . $_REQUEST['query_string'] . ' Australia'));
    $service_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . $query_string . "&inputtype=textquery&fields=formatted_address,name,geometry&key=AIzaSyDFRP59njojtx0eXlHmvYyGAtWZFwvRSLU";
    $curl = curl_init($service_url); // Create REST Request
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.manifest-v8+xml'));
    $curl_response = curl_exec($curl); // Execute REST Request
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl) . "\n";
    }
    $result = json_decode($curl_response);
    if ($result->status == 'OK') {
        $return['msg'] = '';
        foreach ($result->results as $k => $r) {
            if ($k == 10) {
                break;
            }
            $return['msg'] .= '<div class="place_result_item" address="' . htmlentities($r->formatted_address) . '">' . $r->name . '</div>';
        }
    }
    curl_close($curl);

    echo json_encode($return);
    exit;
}



function get_billing_country()
{
    $ip = getRealIpAddr();

    $url = "http://ip-api.com/php/" . $ip;
    $return['msg'] = '';
    $curl = curl_init($url); // Create REST Request
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.manifest-v8+xml'));
    $curl_response = curl_exec($curl); // Execute REST Request
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl) . "\n";
    }
    curl_close($curl);

    if ($curl_response) {
        $res = unserialize($curl_response);
        $return['msg'] = $res['countryCode'];
    }
    echo json_encode($return);
    exit;
}

function get_google_geocode()
{
    $address = urlencode(strtolower($_REQUEST['address']));
    $return['msg'] = 'empty';
    $componentForm = array(
        'street_number' => 'short_name',
        'route' => 'long_name',
        'locality' => 'long_name',
        'administrative_area_level_1' => 'short_name',
        'country' => 'short_name',
        'postal_code' => 'short_name'
    );
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDFRP59njojtx0eXlHmvYyGAtWZFwvRSLU";
    $curl = curl_init($url); // Create REST Request
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.manifest-v8+xml'));
    $curl_response = curl_exec($curl); // Execute REST Request
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl) . "\n";
    }
    $resp = json_decode($curl_response, true);
    if ($resp['status'] == 'OK') {
        $return['msg'] = '';
        $address_components = isset($resp['results'][0]['address_components']) ? $resp['results'][0]['address_components'] : "";
        if ($address_components) {
            $address_details = array();
            foreach ($address_components as $element) {
                $key = str_replace(' political', '', implode(' ', $element['types']));
                $address_details[$key] = (isset($componentForm[$key]) and isset($element[$componentForm[$key]])) ? $element[$componentForm[$key]] : '';
            }
            $return['msg'] = $address_details;
        }
    }
    curl_close($curl);

    echo json_encode($return);
    exit;
}

?>

