<?php

/**
 * @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Category
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');
date_default_timezone_set('Australia/Sydney');
global $mosConfig_absolute_path;


require_once( $mosConfig_absolute_path . "/scripts/deliveries/ShipmentFactory.php" );

require_once( $mainframe->getPath('admin_html') );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/language.class.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/languages/english.php" );
switch ($task) {

    case 'getDeliveries':
        getDeliveries();
        break;
    case 'markOrder':
        markOrder();
        break;
    case 'markRemove':
        markRemove();
        break;
    case 'confirmCustomerSentEmail':
        confirmCustomerSentEmail();
        break;
    case 'checkCustomerSentEmailsCount':
        checkCustomerSentEmailsCount();
        break;
    case 'SendMailAgain':
        SendMailAgain();
        break;
    case 'SendPODToPartner':
        SendPODToPartner();
        break;
    case 'SendShippingForm':
        SendShippingForm();
        break;
    case 'getOrderAssignedDeliveryService':
        getOrderAssignedDeliveryService();
        break;
    case 'updateColor':
        updateColor();
        break;

    case 'updateDDate':
        updateDDate();
        break;
    case 'SetOrderCondition':
        SetOrderCondition();
        break;
    case 'addProductItem':
        addProductItem();
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

    case 'saveRefoundReason':
        saveRefoundReason();
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

    case 'removeCCInfo':
        removeCCInfo();
        break;

    case 'RateHistoryRefresh':
        RateHistoryRefresh();
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
        exportAddressBookXML();
        break;

    case 'exportUPSConnect':
        exportUPSConnect();
        break;

    case 'shipOrder':
        shipOrder();
        break;

    case 'shipOrderAddress':
        shipOrderAddress();
        break;

    case 'searchOrderForm':
        searchOrderForm();
        break;

    case 'packageOrder':
        packageOrder();
        break;

    case 'packagingDelivery':
        packagingDelivery();
        break;


    case 'sendordertoiris':
        sendOrderToIris();
        break;

    case 'changeRate':
        //updateRating();
        updateRate();
        break;
    case 'send_substitution_text':
        send_substitution_text();
        break;
    case 'CheckOldDeliveries':
        CheckOldDeliveries();
        break;

    case 'checkOrderHasActiveDelivery':
        checkOrderHasActiveDelivery();
        break;

    case 'getCancelLink':
        getCancelLink();
        break;

    case 'printShipmentLabel':
        printShipmentLabel();
        break;

    case 'sendOrderToCarrier':
        sendOrderToCarrier();
        break;

    case 'getProductSizePrice':
        getProductSizePrice();
        break;

    default:
        loadAjaxOrder();
        break;
}


function saveRefoundReason() {
    global $database, $my;

    $orderId = (int)mosGetParam($_REQUEST, "order_id", 0);
    $reason = mosGetParam($_REQUEST, "reason");
    $description = mosGetParam($_REQUEST, "description");

    $q = sprintf("INSERT INTO jos_vm_order_refunded_reason (
          order_id, 
          user_id, 
          reason, 
          description, 
          created_at
          ) VALUES ('%s', '%s', '%s', '%s', '%s')",
        $orderId,
        $my->id,
        $reason,
        $description,
        date('Y-m-d H:i:s')
    );

    $database->setQuery($q);
    $database->query();

    echo 'success';
    exit();
}

function getProductSizePrice()
{
    global $database;
    $productSku = mosGetParam($_POST, 'product_sku');

    $sql = "SELECT * FROM jos_vm_product AS P
         LEFT JOIN jos_vm_product_options AS PO ON PO.product_id = P.product_id
         WHERE P.product_sku LIKE '$productSku%' LIMIT 1";
    $database->setQuery($sql);
    $product = false;
    $database->loadObject($product);

    $result = [
        'product_id' => $product->product_id,
        'deluxe' => $product->deluxe == '' ? 0 : $product->deluxe,
        'supersize' => $product->supersize == '' ? 0 : $product->supersize,
        'product_size' => (bool)$product->deluxe  || (bool)$product->supersize
    ];

    echo json_encode($result);
    require_once '../end_access_log.php';
    exit(0);
}

function markOrder() {
    global $database, $option, $my;
    $orderID = mosGetParam($_POST, "id", 0);
    $mark_desc = $database->getEscaped(mosGetParam($_POST, "desc", ''));
    $selected_mark = mosGetParam($_POST, "selected_mark", '');
    $selected_mark_name = mosGetParam($_POST, "selected_mark_name", '');

    if (!$orderID) {
        echo "This Order is not exist.";
        exit(0);
    }
    $mysqlDatetime = date("Y-m-d G:i:s");
    $query = "INSERT INTO jos_vm_order_mark_history(order_id,
                                                                date_added,
                                                                order_mark_code,
                                                                description, user_name)
				VALUES ('$orderID',
						'" . $mysqlDatetime . "','" . $selected_mark . "',
						'" . $mark_desc . "', '" . $my->username . "')";
    $database->setQuery($query);
    $database->query();
    $new_mark_id = $database->insertid();

    $query = "Select order_status_code from jos_vm_order_history where order_id = '$orderID' order by order_status_history_id desc limit 1";
    $database->setQuery($query);
    $res = $database->loadResult();
    $comment = 'mark : ' . $selected_mark_name;
    if ($mark_desc) {
        $comment .= " <br>description : " . $mark_desc;
    }

    $query = "INSERT INTO jos_vm_order_history(	order_id,
                                                                date_added,
                                                                order_status_code,
                                                                comments, user_name)
				VALUES ('$orderID',
						'" . $mysqlDatetime . "','" . $res . "','$comment', '" . $my->username . "')";
    $database->setQuery($query);
    $database->query();
    die("$new_mark_id");
}

function markRemove() {
    global $database, $option, $my;
    $orderID = $database->getEscaped(mosGetParam($_POST, "order_id", 0));
    $mark_desc = $database->getEscaped(mosGetParam($_POST, "desc", ''));
    $mark_id = $database->getEscaped(mosGetParam($_POST, "mark_id", 0));
    $published = $database->getEscaped(mosGetParam($_POST, "published", ''));

    if (!$orderID) {
        echo "This Order is not exist.";
        exit(0);
    }
    $mysqlDatetime = date("Y-m-d G:i:s");
    $query = "UPDATE jos_vm_order_mark_history SET published='" . $published . "' WHERE id='" . $mark_id . "'";
    $database->setQuery($query);
    $database->query();


    $query = "Select order_status_code from jos_vm_order_history where order_id = '$orderID' order by order_status_history_id desc limit 1";
    $database->setQuery($query);
    $res = $database->loadResult();

    $comment = 'mark status change : ' . (($published == 'Y') ? 'Added' : 'Removed');
    if ($mark_desc) {
        $comment .= " <br>description : " . $mark_desc;
    }
    $query = "INSERT INTO jos_vm_order_history(	order_id,
                                                                date_added,
                                                                order_status_code,
                                                                comments, user_name)
				VALUES ('$orderID',
						'" . $mysqlDatetime . "','" . $res . "','$comment', '" . $my->username . "')";
    $database->setQuery($query);
    $database->query();
    die("success");
}

function CheckOldDeliveries() {
    global $database, $my;
    $mysqlDatetime = date("Y-m-d G:i:s");
    $order_id = $_POST['order_id'];
    $return = array();
    $return['result'] = false;
    $active_shipment_less_one_day = false;

    $query = "SELECT id,dateadd
    FROM `jos_vm_orders_deliveries` 
    WHERE `order_id` ='" . $order_id . "' and ( active = 1 )";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    foreach ($rows as $k => $r) {
        $date_now = new DateTime(date('Y-m-d H:i'));
        $date_shipment = new DateTime(date('Y-m-d', strtotime($r->dateadd)));
        if ($date_now->diff($date_shipment)->days < 1) {
            $active_shipment_less_one_day = true;
        }
    }

    if ($active_shipment_less_one_day) {
        $return['result'] = true;
    } else {
        $query = "UPDATE `jos_vm_orders_deliveries` SET `active`='0'
    WHERE `order_id`=" . $order_id . "";
        $database->setQuery($query);
        $database->query();

        $query = "SELECT
                `warehouse`
        FROM `jos_vm_orders`
        WHERE `order_id`=" . $order_id . "";
        $order_obj = false;
        $database->setQuery($query);
        $database->loadObject($order_obj);

        $warehouse = $order_obj->warehouse;


        $query = "INSERT INTO `jos_vm_order_history`
    (
        `order_id`,
        `order_status_code`,
        `warehouse`,
        `priority`,
        `date_added`,
        `customer_notified`,
        `warehouse_notified`,
        `comments`,
        `iris_notified`,
        `user_name`
    )
    VALUES (
        " . $order_id . ",
        'A',
        '" . $database->getEscaped($warehouse) . "',
        '',
        '" . $mysqlDatetime . "',
        '0',
        '0',
        'resend delivery system and cancel old deliveries',
        '0',
        '" . $database->getEscaped($my->username) . "'
    )";
        $database->setQuery($query);
        $database->query();
    }

    echo json_encode($return);
    exit(0);
}

function RateHistoryRefresh() {
    global $database, $option, $mosConfig_calculate_rate_url, $mosConfig_payment_centralization_auth;
    $email = $database->getEscaped(mosGetParam($_POST, "email", ''));

    if (!$email) {
        echo "This customer email is empty.";
        exit(0);
    }
    $data = array('email_address' => $email, 'project' => 'bloomex.com.au');
    $curl = curl_init($mosConfig_calculate_rate_url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_payment_centralization_auth);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    $curl_response = curl_exec($curl);

    $query = "SELECT h.*
    FROM `jos_rate_history_api` as h 
    left join jos_vm_order_user_info as i on i.user_id=h.user_id and i.address_type LIKE 'BT' 
    WHERE i.user_email='" . $email . "' group by h.id ORDER BY h.`date` desc";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    HTML_AjaxOrder::RateHistoryRefresh($option, $rows);
}

function getDeliveries() {
    global $database, $my, $mosConfig_offset;

    $warehouse_code = $_POST['warehouse_code'];
    $order_id = $_POST['order_id'];

    $return = array();
    $return['result'] = false;

    $query = "SELECT `d`.*
    FROM `jos_vm_deliveries` AS `d`
    INNER JOIN `jos_vm_orders` as `o` ON `o`.`order_id`=" . (int) $order_id . "
    INNER JOIN `jos_vm_warehouse` as `w` ON `w`.`warehouse_code`=`o`.`warehouse`
    LEFT JOIN `jos_vm_warehouses_deliveries` AS `w_d` ON `w_d`.`warehouse_id`=`w`.`warehouse_id` AND `w_d`.`delivery_id`=`d`.`id`
    WHERE `w_d`.`id` IS NOT NULL";
    $database->setQuery($query);

    $rows = $database->loadObjectList();

    if (sizeof($rows) > 0) {
        $return['result'] = true;
        $return['rows'] = $rows;
    }

    echo json_encode($return);
    exit(0);
}
function SendPODToPartner() {
    global $database, $my,  $mosConfig_mailfrom, $mosConfig_fromname;

    $order_id = (int) $_POST['order_id'];
    $return['status'] = 'error';
    //NEW CONFIRMATION

    $user_info_obj = false;
    $query = "SELECT 
               `o`.`order_id`,
                `ui`.`user_email`,
                `o`.`order_status`,
                `o`.`warehouse`
     FROM `jos_vm_order_user_info` AS `ui`
    INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ui`.`order_id`
    WHERE `ui`.`order_id`=" . $order_id . " AND `ui`.`address_type`='BT'";

    $database->setQuery($query);
    $database->loadObject($user_info_obj);


    require_once CLASSPATH . 'ps_comemails.php';

    $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='15' AND `recipient_type`='4' ";
    $confirmation_obj = false;
    $database->setQuery($query);
    $database->loadObject($confirmation_obj);

    $q = "SELECT p.partner_name,p.partner_email  FROM tbl_local_parthners_orders as o  "
        . "JOIN tbl_local_parthners  as p on o.partner_id = p.partner_id ";
    $q .= "WHERE o.order_id = '" . $order_id . "' order by o.id DESC";
    $partner = false;
    $database->setQuery($q);
    $database->loadObject($partner);

    if ($confirmation_obj && $partner) {
        $query = "INSERT INTO `jos_vm_order_history`
        (
            `order_id`,
            `order_status_code`,
            `warehouse`,
            `date_added`,
            `customer_notified`,
            `warehouse_notified`,
            `comments`,
            `user_name`
        )
        VALUES (
            " . $user_info_obj->order_id . ",
            '" . $user_info_obj->order_status . "',
            '" . $user_info_obj->warehouse . "',
            '" . date('Y-m-d H:i:s') . "',
            '0',
            '0',
            'Send Proof Of Delivery Notification To Partner: " . $database->getEscaped($partner->partner_name) . "',
            '" . $database->getEscaped($my->username) . "'
        )";

        $database->setQuery($query);
        $database->query();

        $ps_comemails = new ps_comemails;

        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $partner->partner_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $confirmation_obj->email_html), 1);

        $return['status'] = 'success';
        $return['email'] = $partner->partner_email;
    }

    echo json_encode($return);
    require_once '../end_access_log.php';
    exit(0);
    //!NEW CONFIRMATION
}
function SendMailAgain() {
    global $database, $my, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $mos_debug_email;

    $order_id = (int) $_POST['order_id'];

    //NEW CONFIRMATION

    $user_info_obj = false;
    $query = "SELECT 
               `o`.`order_id`,
                `ui`.`user_email`,
                `o`.`order_status`,
                `o`.`warehouse`
     FROM `jos_vm_order_user_info` AS `ui`
    INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ui`.`order_id`
    WHERE `ui`.`order_id`=" . $order_id . " AND `ui`.`address_type`='BT'";

    $database->setQuery($query);
    $database->loadObject($user_info_obj);

    $queryPartner= "SELECT id from jos_vm_api2_orders  WHERE order_id = ". $order_id;
    $database->setQuery($queryPartner);
    $checkOrderFromPartner	= $database->loadResult();

    $addIntoWhere='';
    if($checkOrderFromPartner) {
        $addIntoWhere = ' AND for_foreign_orders=1 ';
    }
        require_once CLASSPATH . 'ps_comemails.php';

        $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='1' $addIntoWhere";
        $confirmation_obj = false;
        $database->setQuery($query);
        $database->loadObject($confirmation_obj);
        if ($confirmation_obj) {
            $query = "INSERT INTO `jos_vm_order_history`
        (
            `order_id`,
            `order_status_code`,
            `warehouse`,
            `date_added`,
            `customer_notified`,
            `warehouse_notified`,
            `comments`,
            `user_name`
        )
        VALUES (
            " . $user_info_obj->order_id . ",
            '" . $user_info_obj->order_status . "',
            '" . $user_info_obj->warehouse . "',
            '" . date('Y-m-d H:i:s') . "',
            '1',
            '0',
            'Resend Confirmation Email',
            '" . $database->getEscaped($my->username) . "'
        )";

            $database->setQuery($query);
            $database->query();

            $ps_comemails = new ps_comemails;

            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $user_info_obj->user_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $confirmation_obj->email_html), 1);
        }
        $return['status'] = 'success';


    $return['email'] = $user_info_obj->user_email;
    echo json_encode($return);
    require_once '../end_access_log.php';
    exit(0);
    //!NEW CONFIRMATION
}

function SendShippingForm() {
    global $database, $my,$mosConfig_fast_checkout_salt, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $mos_debug_email;

    $order_id = (int) $_POST['order_id'];
    $user_id = (int) $_POST['user_id'];
    $bill_user_email = $database->getEscaped($_POST['bill_user_email']);
    $bill_user_first_name = $database->getEscaped($_POST['bill_user_first_name']);
    $zip = $database->getEscaped($_POST['zip']);

    if($user_id) {
        $user_info_obj = false;
        $query = "SELECT 
        i1.`user_info_id`
    FROM `jos_vm_user_info` as i1 
    WHERE i1.`user_id`=" . $user_id . " AND i1.zip='" . $zip . "' AND i1.`address_type`='ST'";
        $database->setQuery($query);
        $database->loadObject($user_info_obj);
    }


    //NEW CONFIRMATION

    if($user_info_obj->user_info_id) {
        $cache = str_rot13($order_id . ';' . ($user_info_obj->user_info_id??''));
        $link_href = 'checkout/2/' . $cache;
    }else{
        $from_string = array("{order_id}", "{user_id}");
        $to_string   = array($order_id, $user_id);
        $cache = str_replace($from_string, $to_string, $mosConfig_fast_checkout_salt);
        $link_href = 'fast-checkout-shipping-form/' . $cache;
    }

    require_once CLASSPATH . 'ps_comemails.php';

    $query = "SELECT
                `email_subject`, 
                `email_html` 
                FROM `jos_vm_emails` 
                WHERE 
                    `email_type`='12' 
                    AND 
                    `recipient_type`='1'
                ";
    $confirmation_obj = false;
    $database->setQuery($query);
    $database->loadObject($confirmation_obj);

    $ps_comemails = new ps_comemails;

    $confirmation_obj->email_html = str_replace('{ShippingFormLink}', 'https://bloomex.com.au/' . $link_href, $confirmation_obj->email_html);
    $confirmation_obj->email_html = str_replace('{phpShopBTName}', $bill_user_first_name, $confirmation_obj->email_html);
    $confirmation_obj->email_subject = str_replace('{phpShopBTName}', $bill_user_first_name, $confirmation_obj->email_subject);

    mosMail($mosConfig_mailfrom, $mosConfig_fromname, $bill_user_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $confirmation_obj->email_html), 1);

    $return['status'] = 'success';
    $return['email'] = $bill_user_email;

    echo json_encode($return);
    require_once '../end_access_log.php';
    exit(0);
    //!NEW CONFIRMATION
}

function getOrderAssignedDeliveryService() {
    global $database, $my,$mosConfig_adm_link,$mosConfig_shipstation_api_key,$mosConfig_adm_auth, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $mos_debug_email;


    $orderID = (int) str_replace('bloom-', '', $_POST['order_id']);

    $query = "select w.warehouse_id ,d.pin as tracking_number,
       s.tracking_url as shipstation_tracking_url,s.tracking_number as shipstation_tracking,
       d.shipment_id as shipment_id,m.name,concat(p.driver_option_type,' - ',p.service_name) as driver_option_type,
       d.delivery_type
FROM jos_vm_orders as o 
LEFT JOIN jos_vm_warehouse as w on w.warehouse_code=o.warehouse
LEFT JOIN jos_vm_orders_deliveries AS d on d.order_id=o.order_id AND d.active = 1
LEFT JOIN tbl_shipstation_orders_couriers as s on s.order_id = o.order_id
LEFT JOIN jos_vm_deliveries as m ON m.id=d.delivery_type 
LEFT JOIN tbl_driver_option as p ON p.service_name=m.name AND p.warehouse_id = w.warehouse_id
WHERE o.order_id=" . $database->getEscaped($orderID)."  order by d.dateadd desc";

    $res = false;
    $database->setQuery($query);
    $database->loadObject($res);
    $return['result'] = false;
    if ($res) {

        $return['result'] = true;
        $return['warehouse_id'] = $res->warehouse_id;
        $return['driver_option_type'] = $res->driver_option_type;
        $return['tracking_number'] = $res->tracking_number;

        if($res->delivery_type == '12') {
            if($res->shipstation_tracking != '') {
                $return['tracking_number'] = $res->shipstation_tracking;
                $return['description'] = $res->shipstation_tracking_url;
            } else {
                $bearer = base64_encode($mosConfig_shipstation_api_key);
                $curl = curl_init();
                $curlOptions = [
                    CURLOPT_URL => 'https://ssapi.shipstation.com/shipments?orderId='.$res->shipment_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST =>  "GET",
                    CURLOPT_HTTPHEADER => [
                        "Host: ssapi.shipstation.com",
                        "Authorization:  Basic $bearer",
                        "Content-Type: application/json"
                    ],
                ];
                curl_setopt_array($curl, $curlOptions);
                $response = curl_exec($curl);
                if ($response) {
                    $responseData = json_decode($response);
                    if($responseData && isset($responseData->shipments) && !empty($responseData->shipments))
                    {
                        $serviceCodes = [
                             'australia_post' =>	'https://auspost.com.au/parcels-mail/track.html#/track?id=',
                             'star_track'	=> 'https://startrack.com.au/track/details/',
                             'fastway_au' =>	'https://www.aramex.com.au/tools/track/?l='
                        ];

                        $return['description'] = isset($serviceCodes[$responseData->shipments[0]->carrierCode])?$serviceCodes[$responseData->shipments[0]->carrierCode]. $responseData->shipments[0]->trackingNumber:'';
                        $return['tracking_number'] = $responseData->shipments[0]->trackingNumber;
                    }
                }
            }
        }

    }

    echo json_encode($return);
    require_once '../end_access_log.php';
    exit(0);
}

function send_substitution_text() {
    global $database, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_limit_sms_sender_AccountKey;

    $order_id = mosGetParam($_REQUEST, "id");
    $user_name = mosGetParam($_REQUEST, "user_name");

    $query = "SELECT user_email,phone_1 FROM #__vm_order_user_info AS UI,#__vm_orders AS O WHERE O.order_id = $order_id AND O.order_id = UI.order_id AND UI.address_type = 'BT' limit 1 ";
    $database->setQuery($query);
    $user_det = $database->loadObjectList();

    $number = formatMobileNumber($user_det[0]->phone_1);
    if ($number) {

        $mail_Body = "A minor substitution is required on Bloomex Order# " . $order_id . ". To accept and receive FREE UPGRADE reply YES. Reply within 30 mins or will proceed as per policy.";

        $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
        $parameters = new stdClass;
        $parameters->CellNumber = $number;
        $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
        $parameters->MessageBody = $mail_Body;

        $Result_id = $client->SendMessageExtended($parameters);
        if ($Result_id->SendMessageExtendedResult->QueuedSuccessfully == 1) {
            $query = "Select order_status_code from jos_vm_order_history where order_id = '$order_id' order by order_status_history_id desc limit 1";
            $database->setQuery($query);
            $res = $database->loadResult();
            $mysqlDatetime = date("Y-m-d G:i:s");
            $MessageID = $Result_id->SendMessageExtendedResult->MessageID;
            $query = "INSERT INTO jos_vm_order_history(	order_id,
                                                                date_added,
                                                                order_status_code,
                                                                comments, user_name)
				VALUES ('$order_id',
						'" . $mysqlDatetime . "','" . $res . "',
						'Sent Substitution Text <br>MessageID:" . $MessageID . "', '" . $user_name . "')";
            $database->setQuery($query);
            $database->query();


            $query = "INSERT INTO `jos_sms_history`
                (
                    `messageID`,
                    `phone`,
                    `datetime`,
                    `text`,
                    `direction`,
                    `operator`,
                    `status`
                ) 
                VALUES  (
                    '" . $database->getEscaped($MessageID) . "',
                    '" . $database->getEscaped($number) . "',
                    '" . $mysqlDatetime . "',
                    '" . $database->getEscaped($mail_Body) . "',
                    'outgoing',
                    '" . $database->getEscaped($user_name) . "',
                    'pending'
                )";
            $database->setQuery($query);
            $database->query();

            $query = "INSERT INTO `sms_conversation`
                (
                    `title`,
                    `text`,
                    `number`,
                    `last_modified`
                ) 
                VALUES  (
                    'Substitution Text',
                    '" . $database->getEscaped($mail_Body) . "',
                    '" . $database->getEscaped($number) . "',
                    " . time() . "
                )";
            $database->setQuery($query);
            $database->query();

            echo "success";
            require_once '../end_access_log.php';
            exit(0);
        } else {
            echo $Result_id->SendMessageExtendedResult->ErrorMessage;
            require_once '../end_access_log.php';
            exit(0);
        }
    } else {
        echo "incorrect number format";
        require_once '../end_access_log.php';
        exit(0);
    }
}

function updateColor() {
    global $database, $my;

    $orderID = intval(mosGetParam($_POST, "order_id", ""));
    $color = addslashes(mosGetParam($_POST, "color", ""));

    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
    }

    $query = "UPDATE #__vm_orders SET `color` ='" . $color . "'WHERE order_id = " . $orderID;
    $database->setQuery($query);
    $database->query();

    $query = "Select order_status_code from jos_vm_order_history where order_id = '$orderID' order by order_status_history_id desc limit 1";
    $database->setQuery($query);
    $res = $database->loadResult();
    $mysqlDatetime = date("Y-m-d G:i:s");

    $query = "INSERT INTO jos_vm_order_history(	order_id,
                                                                date_added,
                                                                order_status_code,
                                                                comments, user_name)
				VALUES ('$orderID',
						'" . $mysqlDatetime . "','" . $res . "',
						'change color to " . $color . "', '" . $my->username . "')";
    $database->setQuery($query);
    $database->query();

    require_once '../end_access_log.php';
    exit(0);
}

function updateDDate() {
    global $database, $my, $mosConfig_live_site, $mosConfig_absolute_path, $sess,
    $VM_LANG, $mosConfig_smtpauth, $mosConfig_mailer, $vmLogger,$mosConfig_mailfrom,
    $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtphost;

    require_once(CLASSPATH . 'ps_main.php');

    $orderID = intval(mosGetParam($_POST, "order_id", ""));
    $ddate = addslashes(mosGetParam($_POST, "ddate", ""));

    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
    }

    $query = "SELECT `ddate`, `order_status` FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();

    $query = "UPDATE #__vm_orders SET ddate ='" . $ddate . "'WHERE order_id = " . $orderID;
    $database->setQuery($query);
    $database->query();

    $mysqlDatetime = date("Y-m-d G:i:s");
    $query = "INSERT INTO `jos_vm_order_history` (`order_id`, `order_status_code`, `customer_notified`, `warehouse_notified`, `date_added`, `comments`, `user_name`)
        VALUES
        (" . $orderID . ", '" . $oOrderInfo[0]->order_status . "', '0', '0', '" . $mysqlDatetime . "', 'Changed delivery date from " . $oOrderInfo[0]->ddate . " to " . $ddate . ".', '" . $my->username . "')";
    $database->setQuery($query);
    $database->query();
//    $_POST['notify_warehouse'] = 0;
//    $_POST['notify_customer'] = 0;

    if ((int) $_POST['notify_warehouse'] == 1) {
        $q = "SELECT vendor_name,contact_email FROM #__vm_vendor ";
        $q .= "WHERE vendor_id='" . $_SESSION['ps_vendor_id'] . "'";

        $database->setQuery($q);
        $dbv = $database->loadObjectList();
        $dbv = $dbv[0];

        $q = "SELECT warehouse_name,warehouse_email,order_status_name FROM #__vm_warehouse,#__vm_orders,#__vm_order_status ";
        $q .= "WHERE #__vm_orders.order_id = '" . $orderID . "' ";
        $q .= "AND #__vm_orders.warehouse = #__vm_warehouse.warehouse_code ";
        $q .= "AND #__vm_orders.order_status = #__vm_order_status.order_status_code ";

        $database->setQuery($q);
        $db = $database->loadObjectList();
        $db = $db[0];

        /* MAIL BODY */
        $message = _HI . " WAREHOUSE " . $db->warehouse_name . ",\n\n";
        $message .= 'Order id: ' . $orderID . ' delivery date has been changed from ' . $oOrderInfo[0]->ddate . ' to ' . $ddate . '.';

        $mail_Body = html_entity_decode($message);

        $mail_Subject = str_replace("{order_id}", $orderID, html_entity_decode($VM_LANG->_PHPSHOP_ORDER_STATUS_CHANGE_SEND_SUBJ));

        $result = vmMail($mosConfig_mailfrom, $dbv->vendor_name, $db->warehouse_email, $mail_Subject, $mail_Body, '');
    }

    if ((int) $_POST['notify_customer'] == 1) {
        $q = "SELECT vendor_name,contact_email FROM #__vm_vendor ";
        $q .= "WHERE vendor_id='" . $_SESSION['ps_vendor_id'] . "'";

        $database->setQuery($q);
        $dbv = $database->loadObjectList();
        $dbv = $dbv[0];

        $q = "SELECT first_name,last_name,user_email,order_status_name FROM #__vm_order_user_info,#__vm_orders,#__vm_order_status ";
        $q .= "WHERE #__vm_orders.order_id = '" . $orderID . "' ";
        $q .= "AND #__vm_orders.user_id = #__vm_order_user_info.user_id ";
        $q .= "AND #__vm_orders.order_id = #__vm_order_user_info.order_id ";
        $q .= "AND order_status = order_status_code ";

        $database->setQuery($q);
        $db = $database->loadObjectList();
        $db = $db[0];

        $message = _HI . $db->first_name . " " . $db->last_name . ",\n\n";
        $message .= 'Your order id: ' . $orderID . ' delivery date has been changed from ' . $oOrderInfo[0]->ddate . ' to ' . $ddate . '.';

        $mail_Body = html_entity_decode($message);
        $mail_Subject = str_replace("{order_id}", $orderID, html_entity_decode($VM_LANG->_PHPSHOP_ORDER_STATUS_CHANGE_SEND_SUBJ));

        $result = vmMail($mosConfig_mailfrom, $dbv->vendor_name, $db->user_email, $mail_Subject, $mail_Body, '');


        unset($db, $dbv);
    }
    if ((int) $_POST['notify_recipient'] == 1) {
        $q = "SELECT vendor_name,contact_email FROM #__vm_vendor ";
        $q .= "WHERE vendor_id='" . $_SESSION['ps_vendor_id'] . "'";

        $database->setQuery($q);
        $dbv = $database->loadObjectList();
        $dbv = $dbv[0];

        $q = "SELECT first_name,last_name,user_email,order_status_name FROM #__vm_order_user_info,#__vm_orders,#__vm_order_status ";
        $q .= "WHERE #__vm_orders.order_id = '" . $orderID . "' ";
        $q .= "AND #__vm_order_user_info.address_type = 'ST' ";
        $q .= "AND #__vm_orders.user_id = #__vm_order_user_info.user_id ";
        $q .= "AND #__vm_orders.order_id = #__vm_order_user_info.order_id ";
        $q .= "AND order_status = order_status_code ";

        $database->setQuery($q);
        $db = $database->loadObjectList();
        $db = $db[0];

        $message = _HI . $db->first_name . " " . $db->last_name . ",\n\n";
        $message .= 'Your order id: ' . $orderID . ' delivery date has been changed from ' . $oOrderInfo[0]->ddate . ' to ' . $ddate . '.';

        $mail_Body = html_entity_decode($message);
        $mail_Subject = str_replace("{order_id}", $orderID, html_entity_decode($VM_LANG->_PHPSHOP_ORDER_STATUS_CHANGE_SEND_SUBJ));

        $result = vmMail($mosConfig_mailfrom, $dbv->vendor_name, $db->user_email, $mail_Subject, $mail_Body, '');


        unset($db, $dbv);
    }

    echo date("d-M-Y", strtotime($ddate));
    require_once '../end_access_log.php';
    exit(0);
}

function removeCCInfo() {
    global $database;
    $orderID = intval(mosGetParam($_POST, "order_id", ""));

    if ($orderID > 0) {
        $query = "UPDATE #__vm_order_payment SET order_payment_code = '', order_payment_number = 'NOT SAVED' WHERE order_id = $orderID";
        $database->setQuery($query);
        $database->query();

        echo "success";
    } else {
        echo "error";
    }
    require_once '../end_access_log.php';
    exit(0);
}

function checkOrderID($orderID) {
    $VM_LANG = new vmLanguage();

    if ($orderID) {
        $aOrderID = explode("-", $orderID);
        if (trim($aOrderID[0]) == $VM_LANG->_VM_BARCODE_PREFIX) {
            return trim($aOrderID[1]);
        } else {
            return "Wrong ID";
        }
    } else {
        return "Wrong ID";
    }
}

function sendOrderToIris() {
    global $database, $my, $mosConfig_absolute_path, $option;
    $orderID = intval(mosGetParam($_POST, "order_id", ""));


    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    if (!empty($oOrderInfo[0]->order_id)) {
        $aInfomation["OrderInfo"] = $oOrderInfo[0];
    }

    $query = "SELECT warehouse FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $warehouse = $database->loadResult();

    $query = "SELECT COUNT(*)  FROM #__vm_order_history  WHERE order_status_code = 'O' AND  order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $bExistStatus = $database->loadResult();

    $query = "SELECT * FROM #__vm_order_user_info  WHERE address_type = 'ST' AND  order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oDelvieryAdd = $database->loadObjectList();

    $aAvailableCity = array("testcity", "victoria", "prince george", "nanaimo", "kelowna", "regina", "saskatoon", "saint catherine's", "kingston", "barrie", "windsor", "london", "red deer", "quebec city", "saint john", "sydney");
    $aAvailableState = array("ON", "BC", "BC", "BC", "BC", "SK", "SK", "ON", "ON", "ON", "ON", "ON", "AB", "QC", "NS", "NS");


    if ((isset($aInfomation["OrderInfo"]->order_id) && intval($aInfomation["OrderInfo"]->order_id) <= 0) && $orderID != "") {
        $msg = "error[--1--]This order was not found.";
        /* }elseif( empty( $oDelvieryAdd[0]->city ) || empty( $oDelvieryAdd[0]->state ) || !( in_array( trim($oDelvieryAdd[0]->state), $aAvailableState )  && in_array( strtolower(trim($oDelvieryAdd[0]->city)), $aAvailableCity ) ) ) {
          $msg	= "error[--1--]The delivery address is not appropriate with service of IRIS";
         */
    } elseif (empty($oDelvieryAdd[0]->city) || empty($oDelvieryAdd[0]->state)) {
        $msg = "error[--1--]The city or state are empty. Please fill them again.";
    } elseif ($warehouse == 'IRIS' && $bExistStatus) {
        $msg = "gone[--1--]";
    } else {
        $msg = "success[--1--]Order successfully sent to IRIS.";
    }
    echo $msg;
    require_once '../end_access_log.php';
    exit(0);
}

function packagingDelivery() {
    global $database, $my, $mosConfig_absolute_path, $option;
    $aInfomation = array();
    $aList = array();
    $orderID = checkOrderID(mosGetParam($_POST, "order_id", ""));
    if (!$orderID)
        $orderID = intval(mosGetParam($_POST, "order_id_research", ""));

    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    if (count($oOrderInfo)) {
        $aInfomation["OrderInfo"] = $oOrderInfo[0];
    }

    if ((isset($aInfomation["OrderInfo"]->order_id) && intval($aInfomation["OrderInfo"]->order_id) <= 0) && $orderID != "") {
        $msg = "This order not found.";
    } else {
        $msg = "";
        $query = "UPDATE #__vm_orders SET  order_status = 'D' WHERE order_id = " . intval($orderID);
        $database->setQuery($query);
        $database->query();

        //======================================================================================
        $query = "INSERT INTO #__vm_order_history (order_id, order_status_code, date_added, customer_notified, warehouse_notified, comments,user_name)  
			  	   VALUES ($orderID, 'D', '" . date("Y-m-d H:i:s", time()) . "', 0, 0, '','" . $my->username . "')";
        $database->setQuery($query);
        $database->query();

        //======================================================================================
        $query = "SELECT OS.order_status_name FROM #__vm_order_status AS OS, #__vm_orders AS O WHERE O.order_id = " . intval($orderID) . " AND O.order_status = OS.order_status_code";
        $database->setQuery($query);
        $aInfomation["OrderStatus"] = $database->loadResult();

        if (intval($orderID))
            $msg = "Order status of #$orderID changed to \"" . $aInfomation["OrderStatus"] . "\"";
    }

    HTML_AjaxOrder::packagingDelivery($option, $msg);
}

function packageOrder() {
    global $database, $my, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom_noreply, $mosConfig_mailfrom, $option;
    $aInfomation = array();
    $aList = array();
    $change_order_status = trim(mosGetParam($_POST, "change_order_status", ""));
    if (!$change_order_status) {
        $orderID = checkOrderID(mosGetParam($_POST, "order_id", ""));
    } else {
        $orderID = mosGetParam($_POST, "order_id", "");
    }
    $ingredient_list = mosGetParam($_POST, "ingredient_list", "");
    if (!$orderID)
        $orderID = intval(mosGetParam($_POST, "order_id_research", ""));
    $aOrderStatus = array("Q", "C", "G", "D", "Z");

    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    if (count($oOrderInfo)) {
        $aInfomation["OrderInfo"] = $oOrderInfo[0];
    }

    if ((isset($aInfomation["OrderInfo"]->order_id) && intval($aInfomation["OrderInfo"]->order_id) <= 0) && $orderID != "") {
        $msg = "This order not found.";
    } else {
        $msg = "";

        //======================================================================================
        $query = "SELECT order_status FROM #__vm_orders WHERE order_id = " . intval($orderID);
        $database->setQuery($query);
        $OrderStatus = $database->loadResult();

        $queryUpdate = array();
        if (!$change_order_status && !in_array($OrderStatus, $aOrderStatus)) {
            $queryUpdate[] = " order_status = 'G' ";
        } elseif ($change_order_status) {
            $queryUpdate[] = "order_status = '$change_order_status' ";
        }

        if (count($queryUpdate)) {
            $sQueryUpdate = implode(",", $queryUpdate);
            $query = "UPDATE #__vm_orders SET $sQueryUpdate WHERE order_id = " . intval($orderID);
            $database->setQuery($query);
            $database->query();

            //======================================================================================
            $order_status_code = $change_order_status ? $change_order_status : 'G';

            if ($order_status_code == 'Q') {
                $query = "SELECT user_email FROM #__vm_order_user_info AS UI,#__vm_orders AS O WHERE O.order_id = $orderID AND O.order_id = UI.order_id AND UI.address_type = 'BT' ";
                $database->setQuery($query);
                $user_email = $database->loadResult();

                $sql = "SELECT U.username FROM #__users AS U, #__vm_orders AS O  WHERE U.id = O.user_id AND O.order_id=" . $orderID;
                $database->setQuery($sql);
                $sProflowersName = $database->loadResult();
                if ($sProflowersName == "redenvelope.com_proflower") {
                    $sql = "SELECT proflower_id FROM #__vm_order_proflower WHERE order_id=" . $orderID;
                    $database->setQuery($sql);
                    $sProflowersID = $database->loadResult();

                    $message_mail_Content = "</br></br>Proflowers ID: $sProflowersID";
                } else {
                    $message_mail_Content = "";
                }

//				$mail_Content	= "The Order #$orderID has been produced and awaiting to be delivered";
                $mail_Content = "Your order has been produced and is waiting to be delivered $message_mail_Content";
                $mail_Subject = "Order Status Change: Your Order #$orderID";

                //die($mosConfig_mailfrom."====". $mosConfig_fromname."====". $user_email."====". $mail_Subject."====". $mail_Content."====". $query);
                mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $user_email, $mail_Subject, $mail_Content, 1);

                $comments = "Order has been produced and is waiting to be delivered";
                $customer_notified = 1;
            } else {
                $comments = "";
                $customer_notified = 0;
            }


            $query = "INSERT INTO #__vm_order_history (order_id, order_status_code, date_added, customer_notified, warehouse_notified, comments,user_name)  
				  	   VALUES ($orderID, '$order_status_code', '" . date("Y-m-d H:i:s", time()) . "', $customer_notified, 0, '$comments','" . $my->username . "')";
            $database->setQuery($query);
            $database->query();
        }

        //======================================================================================
        $query = "SELECT OS.order_status_name FROM #__vm_order_status AS OS, #__vm_orders AS O WHERE O.order_id = " . intval($orderID) . " AND O.order_status = OS.order_status_code";
        $database->setQuery($query);
        $aInfomation["OrderStatus"] = $database->loadResult();

        if ($change_order_status) {
            $msg = "Order status of #$orderID changed to <span style='font-size:24px'>\"" . $aInfomation["OrderStatus"] . "\"</span>";
        }


        //======================================================================================
        $query = "SELECT * FROM #__vm_order_history WHERE order_id = " . intval($orderID) . " AND order_status_code = 'M'";
        $database->setQuery($query);
        $oMsg = $database->loadObjectList();

        $aInfomation["SubsituationMessage"] = "";
        if (count($oMsg)) {
            foreach ($oMsg as $item) {
                $aInfomation["SubsituationMessage"] .= str_replace("\n", "<br/>", $item->comments) . "<br/><br/>";
            }
        } else {
            $aInfomation["SubsituationMessage"] .= "None";
        }


        //======================================================================================
        $query = " SELECT OI.*, P.product_full_image, P.ingredient_list FROM #__vm_order_item AS OI, #__vm_product AS P WHERE OI.product_id = P.product_id AND OI.order_id = " . intval($orderID);
        $database->setQuery($query);
        $aInfomation["OrderItem"] = $database->loadObjectList();
    }


    HTML_AjaxOrder::packageOrder($option, $aInfomation, $orderID, $msg);
}

function searchOrderForm() {
    global $database, $my, $mosConfig_absolute_path, $option;
    $aInfomation = array();
    $aList = array();
    $change_order_status = trim(mosGetParam($_POST, "change_order_status", ""));
    if (!$change_order_status) {
        $orderID = checkOrderID(mosGetParam($_POST, "order_id", ""));
    } else {
        $orderID = mosGetParam($_POST, "order_id", "");
    }
    $comments = mosGetParam($_POST, "comment", "");
    if (!$orderID)
        $orderID = intval(mosGetParam($_POST, "order_id_research", ""));
    $aOrderStatus = array("Q", "C", "G", "N", "W", "E", "D", "M", "i", "Z");

    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();

    $aInfomation["OrderInfo"] = (object) [];

    if (count($oOrderInfo)) {
        $aInfomation["OrderInfo"] = $oOrderInfo[0];
    }


    if ((isset($aInfomation["OrderInfo"]->order_id) && intval($aInfomation["OrderInfo"]->order_id) <= 0) && $orderID != "") {
        $msg = "This order not found.";
    } else {
        $msg = "";

        //======================================================================================
        $query = "SELECT order_status FROM #__vm_orders WHERE order_id = " . intval($orderID);
        $database->setQuery($query);
        $OrderStatus = $database->loadResult();

        $queryUpdate = array();
        if (!$change_order_status && !in_array($OrderStatus, $aOrderStatus)) {
            $queryUpdate[] = " order_status = 'N' ";
        } elseif ($change_order_status) {
            $queryUpdate[] = "order_status = '$change_order_status' ";
        }

        /* if( $change_order_status )	{
          $queryUpdate[]	= "ingredient_list = '". htmlentities($ingredient_list) ."'";
          } */

        if (count($queryUpdate)) {
            $sQueryUpdate = implode(",", $queryUpdate);
            $query = "UPDATE #__vm_orders SET $sQueryUpdate WHERE order_id = " . intval($orderID);
            $database->setQuery($query);
            $database->query();

            //======================================================================================
            $order_status_code = $change_order_status ? $change_order_status : 'N';
            if (!in_array($order_status_code, array("M", "i"))) {
                $comments = "";
            }

            $query = "INSERT INTO #__vm_order_history (order_id, order_status_code, date_added, customer_notified, warehouse_notified, comments, user_name)  
				  	   VALUES ($orderID, '$order_status_code', '" . date("Y-m-d H:i:s", time()) . "', 0, 0, '$comments','" . $my->username . "')";
            $database->setQuery($query);
            $database->query();
        }

        //======================================================================================
        $query = "SELECT OS.order_status_name FROM #__vm_order_status AS OS, #__vm_orders AS O WHERE O.order_id = " . intval($orderID) . " AND O.order_status = OS.order_status_code";
        $database->setQuery($query);
        $aInfomation["OrderStatus"] = $database->loadResult();

        if ($change_order_status) {
            $msg = "Order status of #$orderID changed to <span style='font-size:24px'>\"" . $aInfomation["OrderStatus"] . "\"</span>";
        }

        //======================================================================================
        $query = " SELECT OI.*, P.product_full_image, P.ingredient_list FROM #__vm_order_item AS OI, #__vm_product AS P WHERE OI.product_id = P.product_id AND OI.order_id = " . intval($orderID);
        $database->setQuery($query);
        $aInfomation["OrderItem"] = $database->loadObjectList();

        $query = "SELECT ingredient_list FROM #__vm_orders WHERE order_id = " . intval($orderID);
        $database->setQuery($query);

        $aInfomation["OrderInfo"]->ingredient_list = $database->loadResult();
    }

    HTML_AjaxOrder::searchOrderForm($option, $aInfomation, $orderID, $msg);
}

function shipOrderAddress() {
    global $database, $my, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_fromname, $option;
    $orderID = mosGetParam($_POST, "order_id", "");
    $address = '';
    $orderID = (int) str_replace('bloom-', '', $orderID);

    $query = "SELECT order_id,user_id,ddate FROM jos_vm_orders WHERE order_id='" . $orderID . "'";
    $database->setQuery($query);
    $result = $database->loadObjectList();
    if ($result[0]) {
        $q = "SELECT * from jos_vm_order_user_info WHERE order_id='" . $orderID . "' ORDER BY address_type DESC LIMIT 2";
        $database->setQuery($q);
        $result2 = $database->loadObjectList();
        $address = $result2[0]->city . ', ' . $result2[0]->address_1 . ', ' . $result2[0]->zip . ', ' . 'Australia';
    }

    echo '<div id=\'address\'>' . $address . '</div>';
}

function shipOrder() {
    global $database, $my, $mosConfig_absolute_path, $mosConfig_mailfrom_noreply, $mosConfig_fromname, $option;
    $aInfomation = array();
    $aInfomation["OrderItem"] = array();
    $aList = array();
    $aDriversOptions = array();
    $bExist = false;
    $bNotFound = false;
    $bClear = false;
    $msg = "";
    $confirm = mosGetParam($_POST, "confirm", "");
    $removeID = mosGetParam($_POST, "removeID", "");
    if (!$confirm && !$removeID) {
        $orderID = checkOrderID(mosGetParam($_POST, "order_id", ""));
    } else {
        $orderID = mosGetParam($_POST, "order_id", "");
    }
    $sOrderListID = trim(mosGetParam($_POST, "order_id_research", ""));
    $aOrderListID = explode(",", $sOrderListID);

    $sEmailText = "";
    $sExtraOptions = "";
    $message_mail_Content = "";
    $drivers_name_and_telephone_number = trim(mosGetParam($_POST, "drivers_name_and_telephone_number", ""));
    $telephone_or_email = trim(mosGetParam($_POST, "telephone_or_email", ""));
    $telephone_or_email2 = trim(mosGetParam($_POST, "telephone_or_email2", ""));
    $tracking_number = trim(mosGetParam($_POST, "tracking_number", ""));
    $driver_option_type = mosGetParam($_POST, "driver_option_type", "");
    $aDriverOptionType = explode(" - ", $driver_option_type);
    $warehouse_id = mosGetParam($_POST, "warehouse_id", "");


    $query = "SELECT warehouse_name FROM jos_vm_warehouse WHERE warehouse_id = $warehouse_id";
    $database->setQuery($query);
    $warehouse_name = $database->loadResult();
    $go = '';
    if ($warehouse_name) {
        $sExtraOptions = "warehouse[--1--]$warehouse_name" . "[--2--]";
    }


    if ($drivers_name_and_telephone_number) {
        $sExtraOptions .= "drivers_name_and_telephone_number[--1--]$drivers_name_and_telephone_number";
        $aDriversOptions["drivers_name_and_telephone_number"] = $drivers_name_and_telephone_number;
    }


    if ($telephone_or_email) {
        $sExtraOptions .= "telephone_or_email[--1--]$telephone_or_email";
        $aDriversOptions["telephone_or_email"] = $telephone_or_email;
    }

    if ($telephone_or_email2) {
        $sExtraOptions .= "telephone_or_email[--1--]$telephone_or_email2";
        $aDriversOptions["telephone_or_email"] = $telephone_or_email2;

        $sExtraOptions .= "[--2--]tracking_number[--1--]$tracking_number";
        $aDriversOptions["tracking_number"] = $tracking_number;
    }


    $sOrderListIDTemp = "";
    /* if( count($aOrderListID) && $removeID ) {
      for ( $i = 0; $i < count($aOrderListID); $i++ ) {
      if( intval($removeID) == intval($aOrderListID[$i]) && intval($aOrderListID[$i]) > 0 )  continue;

      $sOrderListIDTemp	.= $aOrderListID[$i].",";
      }
      $sOrderListID		= substr( $sOrderListIDTemp, 0, strlen($sOrderListIDTemp)-1 );
      $aOrderListID		= explode( ",", $sOrderListID );

      $msg	= "This order #$removeID was removed in current list.";
      }
     * 
     */


    if (count($aOrderListID)) {
        foreach ($aOrderListID as $value) {
            if (intval($orderID) == intval($value) && intval($value) > 0) {
                $bExist = true;
                break;
            }
        }
    }

    //======================================================================================
    if (isset($orderID) && intval($orderID)) {
        $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
        $database->setQuery($query);
        $oOrderInfo = $database->loadObjectList();
        if (count($oOrderInfo)) {
            $aInfomation["OrderInfo"] = $oOrderInfo[0];
        }
    }


    if ((isset($aInfomation["OrderInfo"]->order_id) && intval($aInfomation["OrderInfo"]->order_id) <= 0) && $orderID != "") {
        $msg = "This order not found.";
        $bNotFound = true;
    } elseif ($bExist) {
        $msg = "This order #$orderID was scaned in current list.";
    }


    if (!$confirm) {
        if ((isset($aInfomation["OrderInfo"]->order_id))) {
            $query = "SELECT id FROM tbl_driver_option_order WHERE order_id = " . intval($orderID);
            $database->setQuery($query);
            $nDriverOptionIdSelected = $database->loadResult();

            if ($nDriverOptionIdSelected) {
                $query = "UPDATE tbl_driver_option_order SET  driver_option_type = '$driver_option_type', description = '" . htmlentities($sExtraOptions, ENT_QUOTES) . "' WHERE order_id = " . intval($orderID);
                $database->setQuery($query);
                $database->query();
            } else {
                $query = "INSERT INTO tbl_driver_option_order( driver_option_type, order_id, description,date_added,in_transit) VALUES( '$driver_option_type', " . $orderID . ", '" . htmlentities($sExtraOptions, ENT_QUOTES) . "' , NOW(), '0')";
                $database->setQuery($query);
                $database->query();
            }
        }
    }
    $sOrderSuccessful_data = '';
    if ($confirm) {
        $aOrderListID = array_unique(explode(",", $sOrderListID));

        $sOrderSuccessful = "";
        for ($i = 0; $i < count($aOrderListID); $i++) {

            $query = "SELECT user_email FROM #__vm_order_user_info AS UI,#__vm_orders AS O WHERE O.order_id = " . intval($aOrderListID[$i]) . " AND O.order_id = UI.order_id AND UI.address_type = 'BT' ";
            $database->setQuery($query);
            $user_email = $database->loadResult();

            $query = "UPDATE tbl_driver_option_order SET  in_transit = '1' WHERE order_id = " . intval($aOrderListID[$i]);
            $database->setQuery($query);
            $database->query();

            $query = "SELECT * FROM tbl_driver_option_order WHERE order_id = " . intval($aOrderListID[$i]);
            $database->setQuery($query);
            $oDriversOption = $database->loadObjectList();


            if ($oDriversOption[0]->description) {
                $sDriversName = "";
                $aDriversOptions = explode("[--2--]", $oDriversOption[0]->description);
                for ($j = 0; $j < count($aDriversOptions); $j++) {
                    if (trim($aDriversOptions[$j])) {
                        $aDriversOptionItem = explode("[--1--]", $aDriversOptions[$j]);

                        $sDriversName .= str_replace("warehouse", "Warehouse", "<b>" . $aDriversOptionItem[0]) . ":</b> " . $aDriversOptionItem[1] . "<br/>";
                    }
                }
                $sDriversName = str_replace("telephone_or_email", "Telephone or Email", $sDriversName);
                $sDriversName = str_replace("tracking_number", "Tracking Number", $sDriversName);
                $sDriversName = str_replace("drivers_name_and_telephone_number", "Drivers name and Telephone Number", $sDriversName);
            }


            //================================== UPDATE ORDER ====================================		
            $query = "UPDATE #__vm_orders SET order_status = 'Z' WHERE order_id = " . intval($aOrderListID[$i]);
            $database->setQuery($query);
            $database->query();

            $query = "INSERT INTO #__vm_order_history (order_id, order_status_code, date_added, customer_notified, warehouse_notified, comments, user_name)  
				  	   VALUES (" . $aOrderListID[$i] . ", 'Z', '" . date("Y-m-d H:i:s", time()) . "', 1, 0, '$sDriversName','" . $my->username . "')";
            $database->setQuery($query);
            $database->query();

            $sOrderSuccessful .= "#" . $aOrderListID[$i] . ", ";

            //================================== EMAIL ====================================	
            $sql = "SELECT U.username FROM #__users AS U, #__vm_orders AS O  WHERE U.id = O.user_id AND O.order_id=" . intval($aOrderListID[$i]);
            $database->setQuery($sql);
            $sProflowersName = $database->loadResult();
            if ($sProflowersName == "redenvelope.com_proflower") {
                $sql = "SELECT proflower_id FROM #__vm_order_proflower WHERE order_id=" . intval($aOrderListID[$i]);
                $database->setQuery($sql);
                $sProflowersID = $database->loadResult();

                $message_mail_Content = "</br></br>Proflowers ID: $sProflowersID";
            } else {
                $message_mail_Content = "";
            }

            $mail_Content = "Your Order is in transit.</br></br>Please see tracking information below:$message_mail_Content</br></br>$sDriversName";
            $mail_Subject = "Your Order #" . intval($aOrderListID[$i]) . " is in transit";
            mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $user_email, $mail_Subject, $mail_Content, 1);

            $query = "DELETE FROM jos_vm_last_map_data WHERE user_id='" . $my->id . "' && order_id='" . intval($aOrderListID[$i]) . "'";
            $database->setQuery($query);
            $database->query();

            $sOrderSuccessful_data .= ( $sOrderSuccessful_data != '' ) ? ',' : '';
            $sOrderSuccessful_data .= intval($aOrderListID[$i]);
        }



        $bClear = true;

        if ($sOrderSuccessful)
            $sOrderSuccessful = substr($sOrderSuccessful, 0, strlen($sOrderSuccessful) - 2);
        $sOrderSuccessful_data = "The Driver Option of $sOrderSuccessful_data is 'in transit'";
    }


    if (!$bExist && !$bNotFound) {
        if ($sOrderListID && intval($orderID) > 0) {
            $sOrderListID .= ",$orderID";
        } elseif (!$sOrderListID && intval($orderID) > 0) {
            $sOrderListID = $orderID;
        }
    }

    if (!$bClear) {
        $query = " SELECT O.*, OS.order_status_name FROM #__vm_orders AS O, #__vm_order_status AS OS WHERE O.order_status = OS.order_status_code AND O.order_id IN ($sOrderListID)";
        $database->setQuery($query);
        $aInfomation["OrderItem"] = $database->loadObjectList();
    } else {
        unset($aInfomation["OrderItem"]);
        $sOrderListID = "";
        $drivers_name = "";
        $driver_option_id = 0;
    }

    //======================================================================================
    /* $types		= array();
      $types[] 	= mosHTML::makeOption( "", "------ Select ------" );
      $types[] 	= mosHTML::makeOption( "Bloomex Driver", "Bloomex Driver" );
      $types[] 	= mosHTML::makeOption( "Local Driver", "Local Driver" );
      $types[] 	= mosHTML::makeOption( "Courier", "Courier" );
      $aList['driver_option_type'] 	= mosHTML::selectList( $types, 'driver_option_type', 'class="inputbox" size="1" onchange="buildSelectBox(this.value);"', 'value', 'text', $driver_option_type ); */


    $query = "SELECT DO.*, VMW.warehouse_name FROM tbl_driver_option AS DO, jos_vm_warehouse AS VMW WHERE DO.warehouse_id = VMW.warehouse_id ORDER BY VMW.warehouse_name ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $aList['service_name'] = "";
    if (count($rows)) {
        $sDriverOptionType = "";
        $sDriverOptionInfo = "";
        foreach ($rows as $value) {
            $aList['service_name'] .= $value->warehouse_id . "[--1--]" . trim($value->driver_option_type) . " - " . trim($value->service_name) . "[--1--]" . trim($value->description) . "[--2--]";
        }
    }


    $types = array();
    $types[] = mosHTML::makeOption("", "------ Select Warehouse ------");
    $query = "SELECT * FROM jos_vm_warehouse ORDER BY warehouse_name ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    if (count($rows)) {
        foreach ($rows as $warehouse) {
            $types[] = mosHTML::makeOption($warehouse->warehouse_id, $warehouse->warehouse_name);
        }
    }
    $aList['warehouse_id'] = mosHTML::selectList($types, 'warehouse_id', 'class="inputbox" size="1" onchange="buildSelectBox(this.value);"', 'value', 'text', $warehouse_id);
    $aList['warehouse_id_selected'] = $warehouse_id;
    $aList['driver_option_type'] = $driver_option_type;

    require_once 'ConstructBodyMapData.php';
    $construct_body_map_data = new ConstructBodyMapData();
    $html3 = '';
    $say_operation = '';
    if (isset($_POST['ajax_post_search']) AND $_POST['ajax_post_search'] == 'true') {
        if ($removeID == '') {


            $construct_body_map_data->work($sOrderListID, isset($aInfomation["OrderItem"]) ? $aInfomation["OrderItem"] : '');
            $new_map_data = $construct_body_map_data->get();

            $addr_line = array();
            $addr_line = $new_map_data['addr_line'];
            $address_print = $new_map_data['address_print'];

            $show_map = $new_map_data['show_map'];

            $insert_data = $new_map_data['insert_data'];

            $query = "SELECT * FROM #__vm_orders WHERE order_id = '" . (int) $orderID . "' LIMIT 1";
            $database->setQuery($query);
            $rrr = $database->loadObjectList();
            if (count($rrr) < 1)
                $say_operation = '<p style=\'color:#FF0000;\'>bad bloom-0' . (int) $orderID . '</p>';

            $ddddd = '';
            if ($say_operation == '') {
                $ddddd = $construct_body_map_data->set($_POST['warehouse_id'], $_POST['driver_option_type'], $insert_data);
                if (!$ddddd)
                    $say_operation = 'The Driver Option of bloom-0' . $orderID . ' was added earlier';
                if ($say_operation == '')
                    $say_operation = '<p style=\'"color:#0000FF;\'>The Driver Option of bloom-0' . $orderID . ' was saved successful</p>';
            }


            $html5 = '<div id=\'insert_number\' style=\'display:none;\'>' . $ddddd . '</div>';
        }
        /* $to = "ilyamaestro@yandex.ru";
          $subject = "Test mail";
          $message = "Hello! This is a simple email messageaasdasdasdasa222333. = ".$ddddd;
          $from = "ilyamaestro@yandex.ru";
          $headers = "From:" . $from;
          mail($to,$subject,$message,$headers);
         * 
         */
        $del = '';
        $html3 = '';
        if ($removeID != '') {
            $del = $removeID;
            $query = "DELETE FROM jos_vm_last_map_data WHERE user_id='" . $my->id . "' && order_id='" . $removeID . "'";
            $database->setQuery($query);
            if ($database->query()) {
                $html3 .= '<div id=\'delete_number\' style=\'display:none;\'>' . $del . '</div>';
                $say_operation = '<p style=\'color:#FF0000;\'>This order bloom-0' . $del . ' was removed in current list.</p>';
            } else {
                $say_operation = '<p style=\'color:#FF0000;\'>Do not remove bloom-0' . $del . '.</p>';
            }
        }







        $query = "SELECT * FROM jos_vm_last_map_data WHERE user_id='" . $my->id . "' ORDER BY id ASC";
        $database->setQuery($query);
        $datamap3 = $database->loadObjectList();



        $html3 .= '<table width=\'100%\' class=\'adminform\' border=\'1\'><tr> <th width=\'30\'>#</th><th width=\'120\' style=\'text-align:left;\'>Order ID</th><th width=\'250\' style=\'text-align:left;\'>Order Status</th><th style=\'text-align:left;\'>Driver Option</th><th style=\'text-align:left;\'>Driver Addresses</th><th style=\'text-align:center;\'>Remove In List</th></tr>'; //.= $last_lap_data3->table_start();  
        $addr_line3 = array();
        $addr_line3[0] = ''; // map show address
        $address_print3 = ''; // print show address
        $line_i3 = 1;
        $i3 = 0;
        $html7 = '';
        if ($datamap3) {
            while ($datamap3[$i3]) {
                //if( $say_operation == '' && $orderID == $row3[1] )
                //{
                //    $say_operation = 'This order bloom-0'.$orderID.' was scaned in current list.';
                //}
                $row3 = explode('[--1--]', $datamap3[$i3]->address);
                $addr_line3[$line_i3] = $row3[4];
                $row3[1] = (int) $row3[1];
                //$html4 .= '[--1--]'.$row3[1];
                $html7 .= ( $html7 != '' ) ? ',' : '';
                $html7 .= $row3[1];
                $address_print3 .= ($i3 + 1) . '&nbsp;&nbsp;&nbsp;[' . $row3[1] . ']&nbsp;&nbsp;&nbsp;' . $row3[4] . '<br>';
                $html4 .= '[--2--]' . $row3[4];
                $html3 .= '<tr><td style=\'text-align:center;vertical-align:top;\'>' . ($i3 + 1) . '</td><td style=\'text-align:left;vertical-align:top;\'><strong>' . $row3[1] . '</strong></td><td style=\'text-align:left;vertical-align:top;\'>' . $row3[2] . '</td><td style=\'text-align:left;padding-right:10px;vertical-align:top;font-size:11px;\'><span style=>' . $row3[3] . '</span></td><td style=\'text-align:left;padding-right:10px;vertical-align:top;font-size:11px;\'>' . $row3[4] . '</td><td style=\'text-align:center;padding-right:10px;vertical-align:top;font-size:11px;\' width=\'100\'><input type=\'button\' name=\'remove\' value=\'Remove\' onclick=\'removeShipOrder(' . $row3[1] . ');\' /></td></tr>';
                $line_i3++;
                $i3++;
            }
        }


        $html3 .= '</table>';
        $html3 .= '<div id=\'say_operation22\' style=\'display:none;\'>' . $say_operation . '</div>';
        $html3 .= '<div id=\'address_print22\' style=\'display:none;\'>' . $address_print3 . '</div>';
        $html3 .= '<div id=\'full_markers\' style=\'display:none;\'>' . $html7 . '</div>';


        $query3 = "SELECT warehouse_id, warehouse_name FROM jos_vm_warehouse WHERE warehouse_id='" . $_POST['warehouse_id'] . "' LIMIT 1";
        $database->setQuery($query3);
        $result3 = $database->loadObjectList();
        //$sender_options = new SenderOptions( $my->username );
        $name = '';
        if ($result3[0]) {
            $name = strtolower($result3[0]->warehouse_name);
        }

        require_once 'bloomexorder.php';
        $sender_options = new SenderOptions($name);
        $warehouse22 = $sender_options->City . ', ' . $sender_options->StreetName . ' ' . $sender_options->StreetNumber . ', ' . $sender_options->PostalCode . ', ' . 'Australia';


        $html3 .= '<div id=\'warehouse22\' style=\'display:none;\'>' . $warehouse22 . '</div>';

        $html3 .= $html5;

        $msg = '';
    }
    //else
    //{if( $sOrderSuccessful_data != '' ) $say_operation = $sOrderSuccessful_data;
    HTML_AjaxOrder::shipOrder($option, $aInfomation, $sOrderListID, $aList, $sOrderSuccessful_data, $aDriversOptions, $construct_body_map_data, $html3);
    //}
}

function exportUPSConnect() {
    global $database, $mosConfig_absolute_path, $mosConfig_fromname;
    $orderID = mosGetParam($_POST, "order_id", "");
    $aInfomation = array();

    //======================================================================================
    $query = "SELECT * FROM #__vm_orders AS O 
							INNER JOIN #__vm_order_user_info AS OUI ON O.order_id = OUI.order_id
							INNER JOIN #__vm_country AS C ON OUI.country = C.country_3_code
							WHERE O.order_id IN ({$orderID}) AND OUI.address_type ='ST' ORDER BY O.order_id";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    /* 	
      $aInfomation["OrderInfo"]	= $oOrderInfo[0];


      //======================================================================================
      $query 		= "SELECT * from  WHERE user_id = {$aInfomation["OrderInfo"]->user_id} AND order_id = {$orderID} ORDER BY address_type, order_info_id ASC LIMIT 2";
      $database->setQuery($query);
      $oUserOrderInfo	= $database->loadObjectList();
      $aInfomation["BillingInfo"]		= $oUserOrderInfo[0];
      $aInfomation["ShippingInfo"]	= $oUserOrderInfo[1];

      //======================================================================================
      $query 		= "SELECT * from #__vm_order_user_info WHERE order_id IN ({$orderID}) AND address_type = 'ST'";
      $database->setQuery($query);
      $oOrderInfo	= $database->loadObjectList(); */
    /* echo $query;
      print_r($oOrderInfo); */

    $sData = "";
    if (count($oOrderInfo)) {
        foreach ($oOrderInfo as $value) { //6 items in one line
            $sData .= "H,4,{$value->order_id},{$value->user_id},{$mosConfig_fromname},{$value->first_name} {$value->middle_name} {$value->last_name},{$value->phone_1},{$value->address_1},{$value->address_2},,{$value->country_2_code},{$value->city}," . strtoupper($value->zip) . ",{$value->state},20,P,1,1,,,,,PRE,,,,,,,,,,,,,,,,,,,,1,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,1,0\n";
        }
    }

    $sPath = "{$mosConfig_absolute_path}/media/addressbook/order.txt";
    if (is_file($sPath)) {
        @chmod($sPath, 0777);
        @unlink($sPath);
    }

    $fp = fopen($sPath, 'w');
    fwrite($fp, $sData);
    fclose($fp);

    if (is_file($sPath)) {
        echo"{$mosConfig_live_site}/media/addressbook/order.txt";
    } else {
        echo "error";
    }
    require_once '../end_access_log.php';
    exit(0);
}

function exportAddressBookXML() {
    global $database, $mosConfig_absolute_path, $mosConfig_live_site;
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
    require_once '../end_access_log.php';
    exit(0);
}

function loadOrderItemDetail($action) {
    global $database, $option;
    $orderID = intval(mosGetParam($_POST, "order_id", 0));
    $aInfomation = array();

    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
    }

    //======================================================================================
    $query = "SELECT * FROM #__vm_orders WHERE order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    if (count($oOrderInfo)) {
        $aInfomation["OrderInfo"] = $oOrderInfo[0];
    }

    $query = " SELECT shopper_discount_value from jos_vm_orders_extra 	
					WHERE  order_id = {$orderID} LIMIT 1";
    $database->setQuery($query);
    $aInfomation["ShopperGroupDiscount"] = $database->loadResult();

    if (isset($oOrderInfo[0]->order_tax_details)) {
        $aOrderTaxDetails = unserialize($oOrderInfo[0]->order_tax_details);
        if (isset($aOrderTaxDetails[0])) {
            $nStateTax = $aOrderTaxDetails[0];
        }
    }

    $query = "SELECT * FROM #__vm_order_item WHERE order_id = $orderID";
    $database->setQuery($query);
    $aInfomation["OrderItem"] = $database->loadObjectList();


    if ($action == "order") {
        HTML_AjaxOrder::loadOrderItemDetail($option, $aInfomation);
    } elseif ($action == "cart") {
        HTML_AjaxOrder::loadOrderCart($option, $aInfomation);
    }
}

function addProductItem() {
    global $database, $mosConfig_offset, $my;
    $orderID = intval(mosGetParam($_POST, "order_id", 0));
    $productID = intval(mosGetParam($_POST, "add_product_id", 0));
    $productQuantity = intval(mosGetParam($_POST, "add_product_quantity", 0));
    $selectBouquet = mosGetParam($_POST, "select_bouquet", 'standard');

    $query = " SELECT * FROM #__vm_orders WHERE order_id = {$orderID}";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderInfo = $rows[0];


    $query = " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc,
  VMP.product_price as real_product_price, VMP.product_price-VMP.saving_price as product_price,
   VMP.product_currency, VM.product_in_stock, VTR.tax_rate ,
  PO.deluxe, PO.supersize
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
							ON VM.product_id = VMP.product_id 
							LEFT JOIN  #__vm_tax_rate AS VTR 
							ON VM.product_tax_id = VTR.tax_rate_id 
							LEFT JOIN #__vm_product_options AS PO
							ON PO.product_id = VM.product_id
							WHERE VM.product_id = {$productID}";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $oProductInfo = $rows[0];

    $deluxe = $oProductInfo->deluxe;
    $supersize = $oProductInfo->supersize;

    if ($selectBouquet == 'deluxe' || $selectBouquet == 'supersize') {
        $oProductInfo->product_name .= ' (' . htmlspecialchars($selectBouquet) . ')';
    }
    $productPrice = 0;


    switch ($selectBouquet) {
        case 'deluxe':
            $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity_deluxe,') ')) as ingredient_list
                FROM product_ingredients_lists as l
                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                WHERE l.product_id = " . $oProductInfo->product_id . " group by l.product_id";
            $productPrice = $deluxe;
            $quantity_field = 'igl_quantity_deluxe';
            break;
        case 'supersize':
            $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity_supersize,') ')) as ingredient_list
                FROM product_ingredients_lists as l
                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                WHERE l.product_id = " . $oProductInfo->product_id . " group by l.product_id";
            $productPrice = $supersize;
            $quantity_field = 'igl_quantity_supersize';
            break;
        default:
            $q_ing = "SELECT group_concat(concat(p.igo_product_name,' (',l.igl_quantity,') ')) as ingredient_list
                FROM product_ingredients_lists as l
                LEFT JOIN product_ingredient_options as p on p.igo_id=l.igo_id
                WHERE l.product_id = " . $oProductInfo->product_id . " group by l.product_id";
            $quantity_field = 'igl_quantity';
            break;
    }

    $database->setQuery($q_ing);
    $ing = $database->loadRow();
    if ($ing) {
        $oProductInfo->ingredient_list = $ing[0];
    }


    $tax_value = $oProductInfo->real_product_price * $oProductInfo->tax_rate;
    $product_final_price = $oProductInfo->product_price + $productPrice; // + $tax_value;
    $tax_change = $productQuantity * $tax_value;
    $price_change = $productQuantity * $product_final_price;
    $timestamp = time() + ($mosConfig_offset * 60 * 60);

    $query = "INSERT INTO #__vm_order_item "
            . "(order_id, user_info_id, vendor_id, product_id, order_item_sku, order_item_name, "
            . "product_quantity, product_item_price, product_final_price, "
            . "order_item_currency, order_status, product_attribute, cdate, mdate) "
            . "VALUES ( $orderID,
						'{$oOrderInfo->user_info_id}',
						{$oOrderInfo->vendor_id},
						{$oProductInfo->product_id},
						'{$oProductInfo->product_sku}',
						'" . addslashes($oProductInfo->product_name) . "',						
						{$productQuantity},
						{$product_final_price},						
						{$product_final_price},
						'{$oProductInfo->product_currency}',
						'{$oOrderInfo->order_status}',
						'" . addslashes($oProductInfo->product_desc) . "',
						{$timestamp},
						{$timestamp})";
    $database->setQuery($query);
    //echo $query;
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        require_once '../end_access_log.php';
        exit(0);
    }

    $order_item_id = $database->insertid();

    //ORDER ITEM INGREDIENTS

    $q = "SELECT `l`.`" . $quantity_field . "` AS `quantity`, `o`.`igo_product_name` as `name`
    FROM `product_ingredients_lists` as `l`
    LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
    WHERE `l`.`product_id`=" . $oProductInfo->product_id . "";
    $database->setQuery($q);
    $order_item_ingredients_rows = $database->loadObjectList();

    $order_item_ingredients_array = array();

    foreach ($order_item_ingredients_rows as $row) {
        $order_item_ingredients_array[] = "(" . $orderID . ", " . $order_item_id . ", '" . addslashes($row->name) . "', '" . ($row->quantity * intval($productQuantity)) . "')";
    }

    $query = "INSERT INTO #__vm_order_item_ingredient (order_id, order_item_id, ingredient_name, ingredient_quantity)
    VALUES " . implode(',', $order_item_ingredients_array) . "";

    $database->setQuery($query);
    $database->query();

    unset($order_item_ingredients_array);

    //END

    /* Insert ORDER_PRODUCT_TYPE */
    $query = " SELECT * FROM #__vm_product_product_type_xref, #__vm_product_type 
				WHERE #__vm_product_product_type_xref.product_id = '" . $oProductInfo->product_id . "' 
				AND #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    foreach ($rows as $item) {
        $product_type_id = $item->product_type_id;

        $query = "SELECT * FROM #__vm_product_type_$product_type_id WHERE product_id = '" . $oProductInfo->product_id . "' ";
        $database->setQuery($query);
        $rows2 = $database->loadObjectList();
        $dbtype2 = $rows2[0];

        $query = " INSERT INTO #__vm_order_product_type(order_id, product_id, product_type_name, quantity, price) 
					VALUES ( $orderID, 
							{$oProductInfo->product_id}, 
							'" . addslashes($item->product_type_name) . "',
							{$dbtype2->quantity},
							{$dbtype2->price}) ";
        $database->setQuery($query);
        $database->query();
    }
    UpdateRateData($orderID, $price_change, 'Add product');
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
        require_once '../end_access_log.php';
        exit(0);
    }


    /* Update Stock Level and Product Sales */
    $query = "UPDATE #__vm_product "
            . "SET "/* product_in_stock = product_in_stock - " . $productQuantity */
            . " product_sales= product_sales + " . $productQuantity
            . " WHERE product_id = '" . $oProductInfo->product_id . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        require_once '../end_access_log.php';
        exit(0);
    }

    $comment = $oProductInfo->product_name . ' [SKU ' . $oProductInfo->product_sku . ' | quantity ' . $productQuantity . '] has been added.';
    $mysqlDatetime = date("Y-m-d G:i:s");
    $query = "INSERT INTO `#__vm_order_history`
    (
        `order_id`,
        `order_status_code`,
        `date_added`,
        `comments`,
        `user_name`
    )
    VALUES (
        " . $oOrderInfo->order_id . ",
        '" . $oOrderInfo->order_status . "',
       '" . $mysqlDatetime . "',
        '" . $comment . "',
        '" . $my->username . "'
    )";
    $database->setQuery($query);
    $database->query();

    echo "success";
    require_once '../end_access_log.php';
    exit(0);
}

function deleteOrderItem() {
    global $database, $mosConfig_offset, $my;
    $ItemID = mosGetParam($_POST, "item_id", "");
    $aItemID = explode("[----]", $ItemID);
    $orderItemID = (int) $aItemID[0];
    $orderID = (int) $aItemID[1];
    $order_item_quantity = 0;
    $user_name = mosGetParam($_REQUEST, "user_name");
    $order_status = mosGetParam($_REQUEST, "order_status");
    $warehouse = mosGetParam($_REQUEST, "warehouse");
    $priority = mosGetParam($_REQUEST, "priority");

    $query = "SELECT `order_id`, `order_status` FROM `#__vm_orders` WHERE `order_id`=" . $orderID . "";
    $database->setQuery($query);
    $order_obj = false;
    $database->loadObject($order_obj);

    if ($order_obj) {
        $query = "SELECT 
            `order_item_name`,
            `product_id`, 
            `product_quantity`,
            `order_item_sku`,
            `product_final_price`, 
            `product_item_price`, 
            `product_final_price` - `product_item_price` AS 'item_tax'
        FROM `#__vm_order_item` WHERE `order_id`=" . $order_obj->order_id . " AND `order_item_id`=" . $orderItemID . "";
        $database->setQuery($query);

        $oOrderItemInfo = false;
        $database->loadObject($oOrderItemInfo);

        if ($oOrderItemInfo) {

            $order_item_name = $oOrderItemInfo->order_item_name;
            $product_id = $oOrderItemInfo->product_id;
            $diff = $order_item_quantity - $oOrderItemInfo->product_quantity;
            $price_change = $diff * $oOrderItemInfo->product_final_price;
            $tax_change = $diff * $oOrderItemInfo->item_tax;
            $timestamp = time() + ($mosConfig_offset * 60 * 60);
            //update rate
            UpdateRateData($orderID, $price_change, 'Delete order item');
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
                require_once '../end_access_log.php';
                exit(0);
            }

            $timestamp = time() + ($mosConfig_offset * 60 * 60);
            $query = "DELETE FROM #__vm_order_item "
                    . "WHERE order_item_id = '" . addslashes($orderItemID) . "'";
            $database->setQuery($query);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "error";
                require_once '../end_access_log.php';
                exit(0);
            }

            $query = "DELETE FROM #__vm_order_product_type  "
                    . "WHERE order_id = '" . $orderID . "' "
                    . " AND product_id = '" . $product_id . "'";
            $database->setQuery($query);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "error";
                require_once '../end_access_log.php';
                exit(0);
            }


            /* Update Stock Level and Product Sales 
              $query = "UPDATE #__vm_product "
              . "SET product_in_stock = product_in_stock - " . $diff
              . ", product_sales= product_sales + " . $diff
              . " WHERE product_id = '" . $product_id . "'";
              $database->setQuery($query);
              if (!$database->query()) {
              echo $database->getErrorMsg();
              echo "error";
              require_once '../end_access_log.php';
              exit(0);
              }
             */

            $comment = $oOrderItemInfo->order_item_name . ' [SKU ' . $oOrderItemInfo->order_item_sku . ' | quantity ' . $oOrderItemInfo->product_quantity . '] has been deleted.';
            $mysqlDatetime = date("Y-m-d G:i:s");
            $query = "INSERT INTO `#__vm_order_history`
            (
                `order_id`,
                `order_status_code`,
                `date_added`,
                `comments`,
                `user_name`
            )
            VALUES (
                " . $order_obj->order_id . ",
                '" . $order_obj->order_status . "',
                '" . $mysqlDatetime . "',
                '" . $comment . "',
                '" . $my->username . "'
            )";
            $database->setQuery($query);
            $database->query();


            echo "success";
        } else {
            echo "error";
        }

        require_once '../end_access_log.php';
        exit(0);
    }
}

function updateQuantity() {
    global $database, $mosConfig_offset, $my;
    $ItemID = mosGetParam($_POST, "item_id", "");
    $aItemID = explode("[----]", $ItemID);
    $orderItemID = (int) $aItemID[0];
    $orderID = (int) $aItemID[1];
    $deliver_country = intval($aItemID[2]);
    $deliver_state = intval($aItemID[3]);
    $order_item_quantity = intval(mosGetParam($_REQUEST, "order_item_quantity", 0));
    $user_name = mosGetParam($_REQUEST, "user_name");
    $order_status = mosGetParam($_REQUEST, "order_status");
    $warehouse = mosGetParam($_REQUEST, "warehouse");
    $priority = mosGetParam($_REQUEST, "priority");

    if (!$order_item_quantity || $order_item_quantity < 0) {
        echo "error";
        require_once '../end_access_log.php';
        exit(0);
    }

    $query = "SELECT `order_id`, `order_status` FROM `#__vm_orders` WHERE `order_id`=" . $orderID . "";
    $database->setQuery($query);
    $order_obj = false;
    $database->loadObject($order_obj);

    if ($order_obj) {
        $query = "SELECT tax_rate FROM jos_vm_tax_rate WHERE tax_country = '$deliver_country' AND tax_state = '$deliver_state'";
        $database->setQuery($query);
        $nStateTax = $database->loadResult();


        $query = "SELECT 
            `order_item_name`, 
            `order_item_sku`, 
            `product_id`, 
            `product_quantity`, 
            `product_final_price`, 
            `product_item_price`, 
            `product_final_price` - `product_item_price` AS 'item_tax' 
        FROM `#__vm_order_item` WHERE `order_id`=" . $orderID . " AND `order_item_id`=" . $orderItemID . "";
        $database->setQuery($query);
        $oOrderItemInfo = false;
        $database->loadObject($oOrderItemInfo);

        $timestamp = time() + ($mosConfig_offset * 60 * 60);
        $product_id = $oOrderItemInfo->product_id;
        $diff = $order_item_quantity - $oOrderItemInfo->product_quantity;
        $order_item_name = $oOrderItemInfo->order_item_name;
        if ($nStateTax) {
            $tax_change = $diff * $nStateTax;
            $price_change = $diff * ($oOrderItemInfo->product_item_price * $nStateTax);
        } else {
            $tax_change = $diff * $oOrderItemInfo->item_tax;
            $price_change = $diff * $oOrderItemInfo->product_final_price;
        }

        //update rate
        UpdateRateData($orderID, $price_change, 'Update Quantity');

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
            require_once '../end_access_log.php';
            exit(0);
        }


        $query = "UPDATE #__vm_order_item "
                . "SET product_quantity = " . $order_item_quantity . ", "
                . "mdate = " . $timestamp . " "
                . "WHERE order_item_id = '" . addslashes($orderItemID) . "'";
        $database->setQuery($query);
        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "error";
            require_once '../end_access_log.php';
            exit(0);
        }

        $query = "SELECT * FROM #__vm_order_item_ingredient WHERE order_item_id=" . addslashes($orderItemID) . "";
        $database->setQuery($query);
        $ingredients = $database->loadObjectList();

        if ($ingredients) {
            $order_item_ingredients_array = array();

            foreach ($ingredients as $ingredient) {
                $order_item_ingredients_array[] = "(" . $orderID . ", " . $orderItemID . ", '" . addslashes($ingredient->ingredient_name) . "', '" . ($ingredient->ingredient_quantity / $oOrderItemInfo->product_quantity * intval($order_item_quantity)) . "')";
            }
            $query = "DELETE FROM #__vm_order_item_ingredient WHERE order_item_id=" . addslashes($orderItemID) . "";

            $database->setQuery($query);
            $database->query();

            $query = "INSERT INTO #__vm_order_item_ingredient (order_id, order_item_id, ingredient_name, ingredient_quantity)
            VALUES " . implode(',', $order_item_ingredients_array) . "";

            $database->setQuery($query);
            $database->query();

            unset($order_item_ingredients_array);
        }


        /* Update Stock Level and Product Sales
          $query = "UPDATE #__vm_product "
          . "SET product_in_stock = product_in_stock - " . $diff
          . ", product_sales= product_sales + " . $diff
          . " WHERE product_id = '" . $product_id . "'";
          $database->setQuery($query);
          if (!$database->query()) {
          echo $database->getErrorMsg();
          echo "error";
          require_once '../end_access_log.php';
          exit(0);
          }
         */

        $comment = $oOrderItemInfo->order_item_name . ' [SKU ' . $oOrderItemInfo->order_item_sku . '] change quantity from ' . $oOrderItemInfo->product_quantity . ' to ' . $order_item_quantity . '.';
        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO `#__vm_order_history`
        (
            `order_id`,
            `order_status_code`,
            `date_added`,
            `comments`,
            `user_name`
        )
        VALUES (
            " . $order_obj->order_id . ",
            '" . $order_obj->order_status . "',
            '" . $mysqlDatetime . "',
            '" . $comment . "',
            '" . $my->username . "'
        )";
        $database->setQuery($query);
        $database->query();


        echo "success";
    } else {
        echo "error";
    }

    require_once '../end_access_log.php';
    exit(0);
}

function updateStandardShipping() {
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $standard_shipping = intval(mosGetParam($_REQUEST, "standard_shipping", 0));
    $user_name = mosGetParam($_REQUEST, "user_name");
    $order_status = mosGetParam($_REQUEST, "order_status");
    $warehouse = mosGetParam($_REQUEST, "warehouse");
    $priority = mosGetParam($_REQUEST, "priority");
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
        require_once '../end_access_log.php';
        exit(0);
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
        $q = "INSERT INTO #__vm_order_history ";
        $q .= "(order_id,order_status_code,warehouse,priority,date_added,customer_notified,warehouse_notified,comments,user_name) VALUES (";
        $q .= "'" . $orderID . "', '$order_status', '$warehouse','$priority', convert_tz(now(),@@session.time_zone,'America/Toronto'), '0', '0',  'Changed shipping method to: $shipping_method', '$user_name')";
        $database->setQuery($q);
        $database->query();
    } else {
        echo "error";
    }
    require_once '../end_access_log.php';
    exit(0);
}

function updateDiscount() {
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $order_discount = floatval(mosGetParam($_REQUEST, "order_discount", 0));
    $user_name = mosGetParam($_REQUEST, "user_name");
    $order_status = mosGetParam($_REQUEST, "order_status");
    $warehouse = mosGetParam($_REQUEST, "warehouse");
    $priority = mosGetParam($_REQUEST, "priority");
    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
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

    $mysqlDatetime = date("Y-m-d G:i:s");
    if ($database->query()) {
        echo "success";
        $q = "INSERT INTO #__vm_order_history ";
        $q .= "(order_id,order_status_code,warehouse,priority,date_added,customer_notified,warehouse_notified,comments,user_name) VALUES (";
        $q .= "'" . $orderID . "', '$order_status', '$warehouse','$priority', '" . $mysqlDatetime . "', '0', '0', 'Changed discount to: $order_discount$', '$user_name')";
        $database->setQuery($q);
        $database->query();
    } else {
        echo "error";
    }
    require_once '../end_access_log.php';
    exit(0);
}

function updateCouponDiscount() {
    global $database;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $order_coupon_discount = floatval(mosGetParam($_REQUEST, "order_coupon_discount", 0));
    $user_name = mosGetParam($_REQUEST, "user_name");
    $order_status = mosGetParam($_REQUEST, "order_status");
    $warehouse = mosGetParam($_REQUEST, "warehouse");
    $priority = mosGetParam($_REQUEST, "priority");
    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
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
        $q = "INSERT INTO #__vm_order_history ";
        $q .= "(order_id,order_status_code,warehouse,priority,date_added,customer_notified,warehouse_notified,comments,user_name) VALUES (";
        $q .= "'" . $orderID . "', '$order_status', '$warehouse','$priority', convert_tz(now(),@@session.time_zone,'America/Toronto'), '0', '0', 'Changed coupon discount to: $order_coupon_discount$', '$user_name')";
        $database->setQuery($q);
        $database->query();
    } else {
        echo "error";
    }
    require_once '../end_access_log.php';
    exit(0);
}

function updateSpecialInstructions() {
    global $database, $my;

    $orderID = mosGetParam($_POST, "order_id", 0);
    $customer_comments = htmlspecialchars(strip_tags(addslashes(mosGetParam($_REQUEST, "customer_comments", ""))));

    $query = "SELECT `order_id`, `order_status`, `customer_comments` FROM `#__vm_orders` WHERE `order_id`=" . (int) $orderID . "";
    $database->setQuery($query);
    $order_obj = false;
    $database->loadObject($order_obj);

    if ($order_obj) {
        $query = "UPDATE `#__vm_orders` SET `customer_comments`='" . $customer_comments . "' WHERE `order_id`=" . $order_obj->order_id . "";
        $database->setQuery($query);

        if ($database->query()) {
            $comment = 'Special Instructions has been changed from ' . $order_obj->customer_comments . ' to ' . $customer_comments . '.';
            $mysqlDatetime = date("Y-m-d G:i:s");
            $query = "INSERT INTO `#__vm_order_history`
            (
                `order_id`,
                `order_status_code`,
                `date_added`,
                `comments`,
                `user_name`
            )
            VALUES (
                " . $order_obj->order_id . ",
                '" . $order_obj->order_status . "',
                '" . $mysqlDatetime . "',
                '" . $comment . "',
                '" . $my->username . "'
            )";
            $database->setQuery($query);
            $database->query();

            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "This Order is not exist.";
    }

    require_once '../end_access_log.php';
    exit(0);
}

function updateCardMessage() {
    global $database, $my;
    $orderID = mosGetParam($_POST, "order_id", 0);
    $customer_note = htmlspecialchars(strip_tags(mosGetParam($_REQUEST, "customer_note", "")));
    $customer_signature = htmlspecialchars(strip_tags(addslashes(mosGetParam($_REQUEST, "customer_signature", ""))));

    $query = "SELECT `order_id`, `order_status`, `customer_note`, `customer_signature` FROM `#__vm_orders` WHERE `order_id`=" . (int) $orderID . "";
    $database->setQuery($query);
    $order_obj = false;
    $database->loadObject($order_obj);
    $mysqlDatetime = date("Y-m-d G:i:s");
    if ($order_obj) {
        $query = "UPDATE `#__vm_orders` SET `customer_note`='" . $customer_note . "', `customer_signature`='" . $customer_signature . "' WHERE `order_id`=" . $order_obj->order_id . "";
        $database->setQuery($query);

        if ($database->query()) {
            $comment = 'Card Message has been changed from ' . $order_obj->customer_note . ' to ' . $customer_note . '.';
            $comment .= '<br/>Signature has been changed from ' . $order_obj->customer_signature . ' to ' . $customer_signature . '.';

            $query = "INSERT INTO `#__vm_order_history`
            (
                `order_id`,
                `order_status_code`,
                `date_added`,
                `comments`,
                `user_name`
            )
            VALUES (
                " . $order_obj->order_id . ",
                '" . $order_obj->order_status . "',
                '" . $mysqlDatetime . "',
                '" . $comment . "',
                '" . $my->username . "'
            )";
            $database->setQuery($query);
            $database->query();

            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "This Order is not exist.";
    }
    require_once '../end_access_log.php';

    exit(0);
}

function loadOrderHistory() {
    global $database, $option, $mosConfig_limit_sms_sender_AccountKey;
    $orderID = mosGetParam($_POST, "id", 0);

    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
    }

    //======================================================================================
    $query = "SELECT * FROM jos_vm_order_history AS OH LEFT JOIN jos_vm_order_status AS OS ON OH.order_status_code = BINARY OS.order_status_code WHERE OH.order_id = $orderID ORDER BY OH.order_status_history_id";
    //$query = "SELECT * FROM #__vm_order_history AS OH, #__vm_order_status AS OS  WHERE OH.order_status_code LIKE BINARY OS.order_status_code AND OH.order_id = $orderID ORDER BY OH.order_status_history_id";
    //$query 	= "SELECT * FROM #__vm_order_history WHERE order_id = $orderID ORDER BY order_status_history_id";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    foreach ($rows as $k => $r) {
        /* if ($r->comments != '') {
          $MessageID_arr = explode('MessageID:', $r->comments);
          if (count($MessageID_arr) > 1) {

          $client = new SoapClient('http://smsgateway.ca/sendsms.asmx?WSDL');
          $parameters = new stdClass;
          $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
          $parameters->MessageID = trim($MessageID_arr[1]);
          $Result_id = $client->GetRepliesToMessage($parameters);
          $res = $Result_id->GetRepliesToMessageResult->SMSIncomingMessage;
          $mesage_html = '';
          if ($res) {
          if (is_array($res)) {
          foreach ($res as $r1) {
          $mesage_html .= "<div class='customer_reply' ><div style='float:left;width: 60%'>" . $r1->Message . "</div><div style='float:right'>" . $r1->FormattedReceivedDate . "  " . $r1->FormattedReceivedTime . "</div></div>";
          }
          } else {
          $mesage_html .= "<div class='customer_reply' >" . $res->Message . "<div style='float:right'>" . $res->FormattedReceivedDate . "  " . $res->FormattedReceivedTime . "</div></div>";
          }
          }

          $r->comments .= '<br>Response: <span style="color:#e32717">' . $mesage_html."</span>";
          }
          $rows[$k]->comments = $r->comments;
          } */
        $query = "SELECT * FROM #__vm_order_history_images WHERE  history_id = $r->order_status_history_id";
        $database->setQuery($query);
        $row = $database->loadObjectList();
        $rows[$k]->images = $row;

        $query = "SELECT * FROM jos_vm_order_history_videos WHERE  history_id = $r->order_status_history_id";
        $database->setQuery($query);
        $videos = $database->loadObjectList();
        $rows[$k]->videos = $videos;
    }
    HTML_AjaxOrder::loadOrderHistory($option, $rows);
}
function confirmCustomerSentEmail() {
    global $my,$mosConfig_ca_adm_link,$mosConfig_ca_adm_auth,$mosConfig_live_site;
    $d['sender'] = mosGetParam($_POST, "sender", '');
    $d['employer'] = $my->username;
    $d['site'] = $mosConfig_live_site;
    $return['result'] = false;
    if($d['sender']){
        $service_url = 'https://mailbot.bloomex.ca/task/resolveSenderEmails/';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_ca_adm_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $d);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $curl_response = curl_exec($curl);
        if ($curl_response) {
            $response = json_decode($curl_response);
            curl_close($curl);
            if ($response->result) {
                $return['msg'] = $d['sender'].' email(s) have been marked as resolved';
                $return['result'] = true;
            }else{
                $return['error'] = $response->error;
            }
        }else{
            $return['error'] = curl_error($curl);
        }
    }else{
        $return['error'] = 'Email is empty';
    }
    echo json_encode($return);die;
}


function checkCustomerSentEmailsCount() {
    global $my, $mosConfig_ca_adm_link,$mosConfig_ca_adm_auth, $mosConfig_live_site;
    $d['sender'] = mosGetParam($_POST, "sender", '');
    $d['employer'] = $my->username;
    $d['site'] = $mosConfig_live_site;
    $return['result'] = false;
    if ($d['sender']) {
        $service_url = 'https://mailbot.bloomex.ca/task/getSenderEmails/';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_ca_adm_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $d);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $curl_response = curl_exec($curl);
        if ($curl_response) {
            curl_close($curl);
            $response = json_decode($curl_response);
            if ($response->result && $response->emailsCount > 0) {
                $return['result'] = true;
                $return['count'] = $response->emailsCount;
            } else {
                $return['error'] = 'No emails sent by this customer';
            }

        } else {
            $return['error'] = curl_error($curl);
        }
    } else {
        $return['error'] = 'Email is empty';
    }
    echo json_encode($return);
    die;
}

function loadAjaxOrder() {
    global $database, $my, $mosConfig_absolute_path, $mosConfig_limit_sms_sender_AccountKey, $option,$mosConfig_ca_adm_link,$mosConfig_ca_adm_auth;

    $aInfomation = array();
    $aList = array();
    $orderID = intval(mosGetParam($_POST, "id", 0));

    if (!$orderID) {
        echo "This Order is not exist.";
        require_once '../end_access_log.php';
        exit(0);
    }
    $aInfomation["UserName"] = $my->username;
    //======================================================================================
    $query = "SELECT o.*,op.operator_code as operator,op.order_call_type FROM jos_vm_orders as o 
left join tbl_order_operator AS op on op.order_id=o.order_id
WHERE o.order_id = $orderID LIMIT 1";
    $database->setQuery($query);
    $oOrderInfo = $database->loadObjectList();
    $aInfomation["OrderInfo"] = $oOrderInfo[0];
    if ($my->prevs->warehouse_only && ( $my->prevs->warehouse_only != $aInfomation["OrderInfo"]->warehouse)) {
        echo "bye";
        require_once '../end_access_log.php';
        exit(0);
    }
    $query = "SELECT rate from #__users WHERE id = {$aInfomation["OrderInfo"]->user_id} ";
    $database->setQuery($query);
    $oUserRate = $database->loadObjectList();
    $aInfomation["rate"] = isset($oUserRate[0]) ? $oUserRate[0] : '';

    $query = "SELECT * from tbl_order_condition WHERE order_id = {$aInfomation["OrderInfo"]->order_id} ";
    $database->setQuery($query);
    $OrderCondition = $database->loadObjectList();
    $aInfomation["OrderInfo"]->soft_fraud = '';
    $aInfomation["OrderInfo"]->hard_fraud = '';
    $aInfomation["OrderInfo"]->inadequate_customer_behavior = '';
    $aInfomation["OrderInfo"]->fair_chargeback_suspecting = '';
    if ($OrderCondition) {
        $aInfomation["OrderInfo"]->soft_fraud = $OrderCondition[0]->soft_fraud;
        $aInfomation["OrderInfo"]->hard_fraud = $OrderCondition[0]->hard_fraud;
        $aInfomation["OrderInfo"]->inadequate_customer_behavior = $OrderCondition[0]->inadequate_customer_behavior;
        $aInfomation["OrderInfo"]->fair_chargeback_suspecting = $OrderCondition[0]->fair_chargeback_suspecting;
    }


    $query = "SELECT * from #__rate_history WHERE id = {$aInfomation["OrderInfo"]->user_id} order by date desc";
    $database->setQuery($query);
    $oUserRate = $database->loadObjectList();
    $aInfomation["RateHistory"] = $oUserRate;

    $query = "SELECT `rate` FROM `#__vm_users_rating` WHERE `user_id`=" . $aInfomation["OrderInfo"]->user_id . "";
    $database->setQuery($query);
    $oUserRate = $database->loadObjectList();
    $aInfomation["UserRate"] = (sizeof($oUserRate) > 0 ? $oUserRate[0] : (object) ['rate' => '0']);

    $query = "SELECT * FROM `#__vm_users_rating_history` WHERE `user_id`=" . $aInfomation["OrderInfo"]->user_id . " ORDER BY `set_date` ASC";

    $database->setQuery($query);
    $aInfomation["RatingHistory"] = $database->loadObjectList();

    //new rating system
    $query = "SELECT `rating` FROM `jos_vm_users_rating_api` WHERE `user_id`=" . $aInfomation["OrderInfo"]->user_id . "";
    $database->setQuery($query);
    $oUserRate_new = $database->loadObjectList();
    $aInfomation["UserRate_new"] = $oUserRate_new[0];


    $queryPartner= "SELECT id from jos_vm_api2_orders  WHERE order_id = ". $orderID;
    $database->setQuery($queryPartner);
    $checkOrderFromPartner	= $database->loadResult();

    if($checkOrderFromPartner) {
        $aInfomation["ForeignOrder"] = true;
    }

    // $query 		= "SELECT C.name from tbl_operators_codes as C , tbl_order_operator as O WHERE O.order_id = ". $orderID." AND O.operator_id=C.id"; 
    //$database->setQuery($query);
    //$isOperInfo	= $database->loadResult();
    // if($isOperInfo){
    //  $aInfomation["OperatorInfo"]=$isOperInfo;
    //  }
    //  else{
    //  $aInfomation["OperatorInfo"]='';
    //  }
    /*
      $query = "SELECT order_id from #__vm_order_user_info WHERE order_id = " . $orderID . " AND address_type = 'ST'";
      $database->setQuery($query);
      $isDeliveryInfo = $database->loadResult();
      if (empty($isDeliveryInfo->order_id)) {

      $query = "SELECT * from #__vm_user_info WHERE user_id = " . $aInfomation["OrderInfo"]->user_id . " AND address_type = 'ST'";
      $database->setQuery($query);
      $oBilling = $database->loadObjectList();
      /* echo $query."========".count($oBilling);
      print_r($oBilling);

      if (!empty($oBilling[0]->user_id) && count($oBilling) == 1) {
      $billing = $oBilling[0];
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
      zip )
      VALUES(  '" . $orderID . "',
      " . $aInfomation["OrderInfo"]->user_id . ",
      'ST',
      '" . htmlentities($billing->address_type_name, ENT_QUOTES) . "',
      '" . htmlentities($billing->company, ENT_QUOTES) . "',
      '" . htmlentities($billing->last_name, ENT_QUOTES) . "',
      '" . htmlentities($billing->first_name, ENT_QUOTES) . "',
      '" . htmlentities($billing->middle_name, ENT_QUOTES) . "',
      '" . htmlentities($billing->phone_1, ENT_QUOTES) . "',
      '" . htmlentities($billing->fax, ENT_QUOTES) . "',
      '" . htmlentities($billing->address_1, ENT_QUOTES) . "',
      '" . htmlentities($billing->address_2, ENT_QUOTES) . "',
      '" . htmlentities($billing->city, ENT_QUOTES) . "',
      '" . htmlentities($billing->state, ENT_QUOTES) . "',
      '" . htmlentities($billing->country, ENT_QUOTES) . "',
      '" . htmlentities($billing->zip, ENT_QUOTES) . "' )";
      $database->setQuery($query);
      $database->query();
      //			echo $query;
      }
      }

     */
    $query = " SELECT shopper_discount_value from jos_vm_orders_extra 	
					WHERE  order_id = {$orderID} LIMIT 1";
    $database->setQuery($query);
    $aInfomation["ShopperGroupDiscount"] = $database->loadResult();

    //======================================================================================
    $query = "SELECT * from #__vm_order_user_info WHERE order_id = {$orderID} ORDER BY address_type, order_info_id ASC LIMIT 2";
    $database->setQuery($query);
    $oUserOrderInfo = $database->loadObjectList();
    $aInfomation["BillingInfo"] = $oUserOrderInfo[0];
    $aInfomation["ShippingInfo"] = $oUserOrderInfo[1];
    /* echo $query;
      print_r($oUserOrderInfo); */

    $types = array();
    $types[] = mosHTML::makeOption("", " - None - ");
    $types[] = mosHTML::makeOption("Mr.", "Mr.");
    $types[] = mosHTML::makeOption("Mrs.", "Mrs.");
    $types[] = mosHTML::makeOption("Dr.", "Dr.");
    $types[] = mosHTML::makeOption("Prof.", "Prof.");


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
    $aList['BillingInfoCountry'] = mosHTML::selectList($rows, "bill_country", "size='1' onChange='changeBillingState(this.value);'", "country_3_code", "country_name", $sSelected);
    $sSelected = $aInfomation["ShippingInfo"]->country ? $aInfomation["ShippingInfo"]->country : "CAN";
    $aList['ShippingInfoCountry'] = mosHTML::selectList($rows, "deliver_country", "size='1' onChange='changeShippingState(this.value);'", "country_3_code", "country_name", $sSelected);


    //======================================================================================
    $query = "SELECT S.state_2_code, S.state_name FROM #__vm_state AS S, #__vm_country AS C WHERE S.country_id = C.country_id AND C.country_3_code ='{$aInfomation["BillingInfo"]->country}' ORDER BY S.state_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oState = new stdClass;
    $oState->state_name = " ------------------ State ------------------ ";
    $oState->state_2_code = "";
    $aState = array();
    $aState[] = $oState;
    $rows = array_merge($aState, $rows);
    $aInfomation['BillingInfoState'] = mosHTML::selectList($rows, "bill_state", "size='1'", "state_2_code", "state_name", $aInfomation["BillingInfo"]->state);

    $query = "SELECT S.state_2_code, S.state_name FROM #__vm_state AS S, #__vm_country AS C WHERE S.country_id = C.country_id AND C.country_3_code ='{$aInfomation["ShippingInfo"]->country}' ORDER BY S.state_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oState = new stdClass;
    $oState->state_name = " ------------------ State ------------------ ";
    $oState->state_2_code = "";
    $aState = array();
    $aState[] = $oState;
    $rows = array_merge($aState, $rows);
    $aInfomation['ShippingInfoState'] = mosHTML::selectList($rows, "deliver_state", "size='1'", "state_2_code", "state_name", $aInfomation["ShippingInfo"]->state);
//	echo  $aInfomation["ShippingInfo"]->state.'======'.$aInfomation["BillingInfo"]->state;
    //======================================================================================
    $query = "SELECT i.* FROM jos_vm_order_item as i
left join jos_vm_product as p on p.product_id=i.product_id WHERE i.order_id = {$orderID}";
    $database->setQuery($query);
    $aInfomation["OrderItem"] = $database->loadObjectList();

    $it = 0;
    foreach ($aInfomation["OrderItem"] as $item) {
        $qInput = "SELECT group_concat(ingredient_name,' (',ingredient_quantity,')' SEPARATOR '<br>') as ingredient_list
FROM `jos_vm_order_item_ingredient`
WHERE order_id={$orderID} and order_item_id={$item->order_item_id} group by order_item_id";
        $database->setQuery($qInput);
        $ing = $database->loadRow();

            $aInfomation["OrderItem"][$it]->ingredient_list = $ing[0]??'';

        $it++;
    }

    $query = " SELECT #__vm_payment_method.*, #__vm_order_payment.*, #__vm_order_payment.order_payment_number AS account_number 
				FROM #__vm_payment_method, #__vm_order_payment  
				WHERE #__vm_payment_method.payment_method_id = #__vm_order_payment.payment_method_id 
				AND #__vm_order_payment.order_id = {$orderID}";
    $database->setQuery($query);
    $oPaymentInfo = $database->loadObjectList();
    if (count($oPaymentInfo)) {
        $aInfomation["PaymentInfo"] = $oPaymentInfo[0];

        $aTemp2 = explode("[--1--]", $aInfomation["PaymentInfo"]->order_payment_log);

        if (is_array($aTemp2)) {
            if (isset($aTemp2[0]))
                $aInfomation["PaymentInfo"]->order_payment_log = $aTemp2[0];

            if (!isset($aTemp2[1]))
                $aTemp2[1] = "";

            $query = "SELECT creditcard_name FROM #__vm_creditcard WHERE creditcard_code = '{$aTemp2[1]}'";
            $database->setQuery($query);
            //	print_r($aInfomation["OrderInfo"]);
            if ($aInfomation["PaymentInfo"]->payment_method_id = 3 && $aInfomation["OrderInfo"]->order_status == "P") {
                $aInfomation["PaymentInfo"]->payment_method_name = "Card type: <b>{$database->loadResult()}</b><br />Payment information was captured for later processing. We may contact you over the phone to verify credit card information.";
            } else {
                $aInfomation["PaymentInfo"]->payment_method_name = "Card type: <b>{$database->loadResult()}</b>";
            }
        }
    }

    $query = "SELECT area_name FROM tbl_new_user_group AS NUG, tbl_mix_user_group AS MUG WHERE NUG.id = MUG.user_group_id AND MUG.user_id = $my->id";
    $database->setQuery($query);
    $area_name = $database->loadResult();
    //echo $area_name."<br/><br/><br/>";	

    if ($area_name) {
        $aAreaName = explode('[--1--]', $area_name);

        if (!in_array("show_account_number", $aAreaName)) {
            $aInfomation["PaymentInfo"]->account_number = "";
            $aInfomation["PaymentInfo"]->order_payment_trans_id = "";
        }
    }

    //======================================================================================
    $query = "SELECT order_status_code, order_status_name FROM #__vm_order_status WHERE publish='1' ORDER BY order_status_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderStatus = new stdClass;
    $oOrderStatus->order_status_name = " - Order Status - ";
    $oOrderStatus->order_status_code = "";
    $aOrderStatus = array();
    $aOrderStatus[] = $oOrderStatus;
    $rows = array_merge($aOrderStatus, $rows);
    $aList['OrderStatus'] = mosHTML::selectList($rows, "order_status_inside", "size='1' class=order_list_status_$orderID", "order_status_code", "order_status_name", $aInfomation["OrderInfo"]->order_status);


    require_once(CLASSPATH . 'ps_order_mark.php');
    $ps_order_mark = new ps_order_mark;
    $query = "Select h.*,m.order_mark_name from jos_vm_order_mark_history as h left join jos_vm_order_mark as m on m.order_mark_code=h.order_mark_code where h.order_id = '$orderID' order by h.id asc ";
    $database->setQuery($query);
    $order_mark_history = $database->loadObjectList();


    if ($order_mark_history) {
        $mark_order_last_value = end($order_mark_history);
        if (isset($mark_order_last_value->order_mark_code)) {
            $aInfomation['OrderMark'] = $ps_order_mark->getOrdermark($mark_order_last_value->order_mark_code, 'id="order_mark" name="order_mark"  class="form-control pull-left"');
        }
        $aInfomation['OrderMarkHistory'] = $order_mark_history;
    } else {
        $aInfomation['OrderMark'] = $ps_order_mark->getOrdermark('', 'id="order_mark" name="order_mark"  class="form-control pull-left"');
    }


    $query = "Select h.*,p.partner_name 
            from tbl_local_parthners_orders_history as h
            left join tbl_local_parthners as p on p.partner_id = h.partner_id
            where h.order_id = '$orderID' order by h.id asc ";
    $database->setQuery($query);
    $aInfomation['PartnerOrderHistory'] = $database->loadObjectList();

    //======================================================================================
    $query = "SELECT warehouse_code, warehouse_name FROM #__vm_warehouse where published=1 ORDER BY warehouse_name ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oWareHouse = new stdClass;
    $oWareHouse->warehouse_name = "NO WAREHOUSE ASSIGNED";
    $oWareHouse->warehouse_code = "NOWAREHOUSEASSIGNED";
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


    //======================================================================================	 order_status_code
    //$query = "SELECT * FROM #__vm_order_history AS OH, #__vm_order_status AS OS  WHERE OH.order_status_code LIKE BINARY OS.order_status_code AND OH.order_id = $orderID ORDER BY OH.order_status_history_id";
    $query = "SELECT * FROM jos_vm_order_history AS OH LEFT JOIN jos_vm_order_status AS OS ON OH.order_status_code = BINARY OS.order_status_code WHERE OH.order_id = $orderID ORDER BY OH.order_status_history_id";
    $database->setQuery($query);
    $history_list = $database->loadObjectList();
    foreach ($history_list as $k => $r) {

        /*  if ($r->comments != '') {
          $MessageID_arr = explode('MessageID:', $r->comments);
          if (count($MessageID_arr) > 1) {

          $client = new SoapClient('http://smsgateway.ca/sendsms.asmx?WSDL');
          $parameters = new stdClass;
          $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
          $parameters->MessageID = trim($MessageID_arr[1]);
          $Result_id = $client->GetRepliesToMessage($parameters);
          $res = $Result_id->GetRepliesToMessageResult->SMSIncomingMessage;
          $mesage_html = '';
          if ($res) {
          if (is_array($res)) {
          foreach ($res as $r1) {
          $mesage_html .= "<div class='customer_reply' ><div style='float:left;width: 60%'>" . $r1->Message . "</div><div style='float:right'>" . $r1->FormattedReceivedDate . "  " . $r1->FormattedReceivedTime . "</div></div>";
          }
          } else {
          $mesage_html .= "<div class='customer_reply' >" . $res->Message . "<div style='float:right'>" . $res->FormattedReceivedDate . "  " . $res->FormattedReceivedTime . "</div></div>";
          }
          }

          $r->comments .= '<br><Response:span style="color:#e32717"> ' . $mesage_html."</span>";
          }
          $history_list[$k]->comments = $r->comments;
          } */


        $query = "SELECT * FROM #__vm_order_history_images WHERE  history_id = $r->order_status_history_id";
        $database->setQuery($query);
        $row = $database->loadObjectList();
        $history_list[$k]->images = $row;

        $query = "SELECT * FROM jos_vm_order_history_videos WHERE  history_id = $r->order_status_history_id";
        $database->setQuery($query);
        $videos = $database->loadObjectList();
        $history_list[$k]->videos = $videos;
    }
    $aInfomation["OrderHistoryInfo"] = $history_list;


    //======================================================================================	 
    $aTemp = explode("|", $aInfomation["OrderInfo"]->ship_method_id);
    $query = "SELECT shipping_rate_id, shipping_rate_name FROM #__vm_shipping_rate ORDER BY shipping_rate_id ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aList['OrderStandingShpping'] = mosHTML::selectList($rows, "standard_shipping", "size='1'", "shipping_rate_id", "shipping_rate_name", $aTemp[4]);


    //======================================================================================
    $query = "SELECT CONCAT_WS( ' - ', CONCAT_WS( '', '[SKU: ', product_sku,']'), product_name ) AS name, product_id FROM #__vm_product where product_publish='Y' ORDER BY product_sku";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $aProduct = array();
    $aProductItem = new stdClass;
    $aProductItem->name = "--------------------------------------------- Select Product ---------------------------------------------";
    $aProductItem->product_id = 0;
    $aProduct[] = $aProductItem;
    $aProduct = array_merge($aProduct, $rows);
    $aList['cboProduct'] = mosHTML::selectList($aProduct, "add_product_id", "class='cbo-product' size='1' disabled", "product_id", "name", "0");
    //$aList['cboProduct'] = $aProduct;

    $query = "SELECT tax_rate FROM jos_vm_tax_rate WHERE tax_country = '{$aInfomation["ShippingInfo"]->country}' AND tax_state = '{$aInfomation["ShippingInfo"]->state}'";
    $database->setQuery($query);
    $nStateTax = $database->loadResult();
    $aInfomation["nStateTax"] = $nStateTax;

    $query = "SELECT donation_price FROM tbl_used_donation WHERE order_id = '{$orderID}' AND donation_price is not null";
    $database->setQuery($query);
    $donated_price = $database->loadResult();
    $aInfomation["donated_price"] = $donated_price;

    $aInfomation["used_bucks"] = 0;
    $query = "SELECT used_bucks FROM tbl_bucks_history WHERE order_id = '{$orderID}' AND used_bucks is not null";
    $database->setQuery($query);
    $used_bucks = $database->loadResult();
    $aInfomation["used_bucks"] = $used_bucks;

    $aInfomation["used_credits"] = 0;

    $query = "SELECT 
        `c`.`credits`
    FROM `jos_vm_users_credits_uses` AS `c`	
    WHERE  
        `c`.`order_id`=" . (int) $orderID . "
    ";

    $database->setQuery($query);
    $credits_obj = false;
    $database->loadObject($credits_obj);
    if ($credits_obj) {
        $aInfomation["used_credits"] = $credits_obj->credits;
    }

    $aInfomation["order_status_code"] = $aInfomation["OrderInfo"]->order_status;

    $query = "SELECT order_status_name FROM jos_vm_order_status  WHERE order_status_code = '{$aInfomation["OrderInfo"]->order_status}'";
    $database->setQuery($query);
    $aInfomation["OrderInfo"]->order_status = $database->loadResult();



//	print_r($aInfomation["OrderInfo"]);
    HTML_AjaxOrder::loadAjaxOrder($option, $aInfomation, $aList);
}

function updateBillingInfo() {
    global $database, $my;

    $ID = intval(mosGetParam($_REQUEST, "id", 0));

    $query = "SELECT order_id,first_name,last_name,middle_name,company,country,state,city,zip,suite,street_number,street_name,district,phone_1,phone_2,user_email,extra_field_1  FROM `#__vm_order_user_info` WHERE order_info_id = $ID";
    $database->setQuery($query);
    $res = $database->loadAssocList();
    $order_id = $res[0]['order_id'];
    $res_old = $res[0];


    $bill_type = mosGetParam($_REQUEST, "bill_type", "");
    $bill_company_name = mosGetParam($_REQUEST, "bill_company_name", "");
    $bill_first_name = mosGetParam($_REQUEST, "bill_first_name", "");
    $bill_last_name = mosGetParam($_REQUEST, "bill_last_name", "");
    $bill_middle_name = mosGetParam($_REQUEST, "bill_middle_name", "");
    //$bill_address_1				= mosGetParam( $_REQUEST, "bill_address_1"		, "" );
    //$bill_address_2				= mosGetParam( $_REQUEST, "bill_address_2"		, "" );
    $bill_suite = mosGetParam($_REQUEST, "bill_suite", "");
    $bill_street_number = mosGetParam($_REQUEST, "bill_street_number", "");
    $bill_street_name = mosGetParam($_REQUEST, "bill_street_name", "");
    $bill_district = mosGetParam($_REQUEST, "bill_district", "");
    $bill_city = mosGetParam($_REQUEST, "bill_city", "");
    $bill_zip_code = mosGetParam($_REQUEST, "bill_zip_code", "");
    $bill_country = mosGetParam($_REQUEST, "bill_country", "");
    $bill_state = mosGetParam($_REQUEST, "bill_state", "");
    $bill_phone = mosGetParam($_REQUEST, "bill_phone", "");
    $bill_evening_phone = mosGetParam($_REQUEST, "bill_evening_phone", "");
    $bill_email = mosGetParam($_REQUEST, "bill_email", "");
    $addr = $bill_suite . ' ' . $bill_street_number . ' ' . $bill_street_name;

    $user_info_id = get_billing_info_user($ID);
    if ($user_info_id) {
        $query_update = "UPDATE #__vm_user_info SET  
                                        last_name='{$bill_last_name}',
                                        first_name='{$bill_first_name}',
                                        phone_1='{$bill_phone}',
                                        district='{$bill_district}',
                                        city='{$bill_city}',
                                        state='{$bill_state}',
                                        address_1='{$addr}',
                                        country='{$bill_country}',
                                        zip='{$bill_zip_code}',
                                        extra_field_1='{$bill_evening_phone}',
                                        suite='{$bill_suite}',
                                        street_number='{$bill_street_number}',
                                        user_email='{$bill_email}',
                                        street_name='{$bill_street_name}'  WHERE user_info_id ='{$user_info_id}'";

        $database->setQuery($query_update);
        $database->query();
    }


    $query = " UPDATE #__vm_order_user_info
				SET company		= '{$bill_company_name}', 
					title		= '{$bill_type}', 
					last_name	= '{$bill_last_name}', 
					first_name	= '{$bill_first_name}', 
					middle_name	= '{$bill_middle_name}', 
					phone_1		= '{$bill_phone}', 
					extra_field_1		= '{$bill_evening_phone}', 
					district		= '{$bill_district}', 
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

        if (!empty($bill_street_number) AND!empty($bill_street_name)) {
            $query = " UPDATE #__vm_order_user_info
				SET address_1	= '{$addr}', 
                                    address_2	= ' '  
				WHERE order_info_id = $ID";

            $database->setQuery($query);
            $database->query();
        }


        $query = "SELECT order_id,first_name,last_name,middle_name,company,country,state,city,zip,suite,street_number,street_name,district,phone_1,phone_2,user_email,extra_field_1  FROM `#__vm_order_user_info` WHERE order_info_id = $ID";
        $database->setQuery($query);
        $res = $database->loadAssocList();
        $res_new = $res[0];
        $diff = array_diff_assoc($res_old, $res_new);
        if (count($diff) > 0) {

            $history = "Update Billing Info: " . json_encode($diff);

            $query = "Select order_status_code from jos_vm_order_history where order_id = '$order_id' order by order_status_history_id desc limit 1";
            $database->setQuery($query);
            $res = $database->loadResult();
            $mysqlDatetime = date("Y-m-d G:i:s");
            $query = "INSERT INTO jos_vm_order_history(	order_id,
                                                                date_added,
                                                                order_status_code,
                                                                comments, user_name)
				VALUES ('$order_id',
						'" . $mysqlDatetime . "','" . $res . "',
						'" . $history . "', '" . $my->username . "')";
            $database->setQuery($query);
            $database->query();
        }

        echo "success";
    } else {
        echo "error";
    }

    require_once '../end_access_log.php';
    exit(0);
}

function updateRate() {
    global $database, $my, $mosConfig_offset;

    $return = array();

    $user_id = intval(mosGetParam($_REQUEST, "id", 0));

    $set_rate = mosGetParam($_REQUEST, "rate", "");
    $comment = mosGetParam($_REQUEST, "comment", "");

    $query = "SELECT `rate` FROM `#__vm_users_rating` WHERE `user_id`=" . (int) $user_id . "";
    $database->setQuery($query);
    $pre_rate = $database->loadResult();

    $query = "UPDATE `#__vm_users_rating` SET `rate`=" . (int) $set_rate . " WHERE `user_id`=" . (int) $user_id . "";
    $database->setQuery($query);
    $database->query();

    if ((int) $pre_rate == 1 AND (int) $set_rate > 1) {
        $query = "SELECT `mask` FROM `jos_vm_user_ccards` WHERE `user_id`=" . (int) $user_id . "";
        $database->setQuery($query);

        $rows = $database->loadObjectList();

        foreach ($rows as $row) {
            $query = "UPDATE `jos_vm_user_ccards` SET `block`=0 WHERE `mask`=" . $row->mask . "";
        }
    }

    if ((int) $set_rate == 1) {
        $query = "SELECT `mask` FROM `jos_vm_user_ccards` WHERE `user_id`=" . (int) $user_id . "";
        $database->setQuery($query);

        $rows = $database->loadObjectList();

        foreach ($rows as $row) {
            $query = "UPDATE `jos_vm_user_ccards` SET `block`=1 WHERE `mask`=" . $row->mask . "";
        }
    }

    $query = "INSERT INTO `#__vm_users_rating_history` 
    (
        `user_id`, 
        `username`, 
        `set_date`, 
        `comment`, 
        `pre_rate`, 
        `set_rate`
    )
    VALUES (
        " . (int) $user_id . ",
        '" . $my->username . "',
        NOW(),
        '" . $database->getEscaped($comment) . "',
        '" . $pre_rate . "',
        '" . $set_rate . "'
    )";
    $database->setQuery($query);

    if ($database->query()) {
        $new_history = false;
        $query = "SELECT * FROM `#__vm_users_rating_history` WHERE `user_id`=" . (int) $user_id . " ORDER BY `set_date` DESC LIMIT 1";
        $database->setQuery($query);
        $database->loadObject($new_history);

        $return['result'] = true;

        if ($new_history) {
            $return['tr'] = '<tr>
                <td>' . $new_history->comment . '</td>
                <td>' . $new_history->set_rate . '</td>
                <td>' . $new_history->pre_rate . '</td>
                <td>' . $new_history->set_date . '</td>
                <td>' . $new_history->username . '</td>
            </tr>';

            $return['user_rate'] = $new_history->set_rate;
        }
    } else {
        $return['result'] = false;
    }

    echo json_encode($return);

    require_once '../end_access_log.php';
    exit(0);
}

function get_billing_info_user($ID) {
    global $database;
    $query = " SELECT user_id FROM  #__vm_order_user_info WHERE order_info_id = $ID";
    $database->setQuery($query);
    $user_id = $database->loadResult();

    if ($user_id) {
        $query_user = " SELECT user_info_id FROM  #__vm_user_info WHERE user_id = $user_id AND address_type='BT' ";
        $database->setQuery($query_user);
        $oList_user = $database->loadObjectList();
        if ($oList_user) {
            return $oList_user[0]->user_info_id;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_delivery_info_user($ID) {
    global $database;
    $query = " SELECT user_id,zip FROM  #__vm_order_user_info WHERE order_info_id = $ID";
    $database->setQuery($query);
    $oList = $database->loadObjectList();

    if ($oList) {
        $user_id = $oList[0]->user_id;
        $zip = $oList[0]->zip;
        $query_user = " SELECT user_info_id FROM  #__vm_user_info WHERE user_id = $user_id AND address_type='ST'  AND zip = '" . $zip . "' ";
        $database->setQuery($query_user);
        $oList_user = $database->loadObjectList();

        if ($oList_user) {

            return $oList_user[0]->user_info_id;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function updateDeliverInfo() {
    global $database, $my,$mosConfig_adm_link,$mosConfig_adm_auth;

    $ID = intval(mosGetParam($_REQUEST, "id", 0));


    $query = "SELECT order_id,first_name,last_name,middle_name,company,country,state,city,zip,suite,street_number,street_name,district,phone_1,phone_2,user_email,address_type2,extra_field_1 FROM `#__vm_order_user_info` WHERE order_info_id = $ID";
    $database->setQuery($query);
    $res = $database->loadAssocList();
    $order_id = $res[0]['order_id'];
    $res_old = $res[0];


    $deliver_type = mosGetParam($_REQUEST, "deliver_type", "");
    $deliver_company_name = mosGetParam($_REQUEST, "deliver_company_name", "");
    $deliver_first_name = mosGetParam($_REQUEST, "deliver_first_name", "");
    $deliver_last_name = mosGetParam($_REQUEST, "deliver_last_name", "");
    $deliver_middle_name = mosGetParam($_REQUEST, "deliver_middle_name", "");
    //$deliver_address_1			= mosGetParam( $_REQUEST, "deliver_address_1"		, "" );
    //$deliver_address_2			= mosGetParam( $_REQUEST, "deliver_address_2"		, "" );
    $deliver_suite = mosGetParam($_REQUEST, "deliver_suite", "");
    $deliver_street_number = mosGetParam($_REQUEST, "deliver_street_number", "");
    $deliver_street_name = mosGetParam($_REQUEST, "deliver_street_name", "");

    $deliver_district = mosGetParam($_REQUEST, "deliver_district", "");
    $deliver_city = mosGetParam($_REQUEST, "deliver_city", "");
    $deliver_zip_code = mosGetParam($_REQUEST, "deliver_zip_code", "");
    $deliver_country = mosGetParam($_REQUEST, "deliver_country", "");
    $deliver_state = mosGetParam($_REQUEST, "deliver_state", "");
    $deliver_phone = mosGetParam($_REQUEST, "deliver_phone", "");
    $deliver_evening_phone = mosGetParam($_REQUEST, "deliver_evening_phone", "");
    $deliver_email = mosGetParam($_REQUEST, "deliver_email", "");
    $address_type2 = mosGetParam($_REQUEST, "address_type2", "");

    $addr = $deliver_suite . ' ' . $deliver_street_number . ' ' . $deliver_street_name;
    $user_info_id = get_delivery_info_user($ID);
    if ($user_info_id) {
        $query_update = "UPDATE #__vm_user_info SET  
                                        last_name='{$deliver_last_name}',
                                        first_name='{$deliver_first_name}',
                                        middle_name='{$deliver_middle_name}',
                                        phone_1='{$deliver_phone}',
                                        district='{$deliver_district}',
                                        city='{$deliver_city}',
                                        state='{$deliver_state}',
                                        address_1='{$addr}',
                                        country='{$deliver_country}',
                                        zip='{$deliver_zip_code}',
                                        extra_field_1='{$deliver_evening_phone}',
                                        suite='{$deliver_suite}',
                                        street_number='{$deliver_street_number}',
                                        street_name='{$deliver_street_name}'  WHERE user_info_id ='{$user_info_id}'";

        $database->setQuery($query_update);
        $database->query();
    }



    $query = " UPDATE #__vm_order_user_info
				SET company		= '{$deliver_company_name}', 
					title		= '{$deliver_type}', 
					last_name	= '{$deliver_last_name}', 
					first_name	= '{$deliver_first_name}', 
					middle_name	= '{$deliver_middle_name}', 
					phone_1		= '{$deliver_phone}', 
					extra_field_1		= '{$deliver_evening_phone}', 
					district		= '{$deliver_district}', 
					city		= '{$deliver_city}', 
					state		= '{$deliver_state}', 
					country		= '{$deliver_country}', 
					zip			= '{$deliver_zip_code}', 
					user_email	= '{$deliver_email}',
					address_type2	= '{$address_type2}',
					suite	= '{$deliver_suite}',
					street_number	= '{$deliver_street_number}',
					street_name	= '{$deliver_street_name}'
                                            
				WHERE order_info_id = $ID";
    $database->setQuery($query);

    if ($database->query()) {

        if (!empty($deliver_street_number) AND!empty($deliver_street_name)) {
            $query = " UPDATE #__vm_order_user_info
                                    SET address_1	= '{$addr}', 
                                        address_2	= ' '  
                                    WHERE order_info_id = $ID";

            $database->setQuery($query);
            $database->query();
        }
        $query = "SELECT order_id,first_name,last_name,middle_name,company,country,state,city,zip,suite,street_number,street_name,district,phone_1,phone_2,user_email,address_type2,extra_field_1 FROM `#__vm_order_user_info` WHERE order_info_id = $ID";
        $database->setQuery($query);
        $res = $database->loadAssocList();
        $res_new = $res[0];
        $diff = array_diff_assoc($res_old, $res_new);
        if (count($diff) > 0) {

            $history = "Update Delivery Info: " . json_encode($diff);

            $query = "Select order_status_code from jos_vm_order_history where order_id = '$order_id' order by order_status_history_id desc limit 1";
            $database->setQuery($query);
            $res = $database->loadResult();
            $mysqlDatetime = date("Y-m-d G:i:s");
            $query = "INSERT INTO jos_vm_order_history(	order_id,
                                                                date_added,
                                                                order_status_code,
                                                                comments, user_name)
				VALUES ('$order_id',
						'" . $mysqlDatetime . "','" . $res . "',
						'" . $history . "', '" . $my->username . "')";
            $database->setQuery($query);
            $database->query();
        }

        $query = "SELECT order_id FROM `jos_vm_orders_deliveries` WHERE order_id = $order_id and delivery_type='12' and active='1'";
        $database->setQuery($query);
        if($database->loadResult()){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $mosConfig_adm_link."/scripts/deliveries/shipstation/index.php?delivery_id=12&task=create&addtag=3952&update_deliver_data=true&sender={$my->username}&order_id={$order_id}");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $mosConfig_adm_auth);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        echo "success";
    } else {
        echo "error";
    }

    require_once '../end_access_log.php';
    exit(0);
}

function UpdateRateData($orderID, $price_change, $str_comm) {
    global $database;
    //update rate
    $query = "SELECT order_total,user_id "
            . "FROM #__vm_orders WHERE order_id = '" . $orderID . "' ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $oOrderTotal = $rows[0];


    $query = "SELECT rate "
            . "FROM #__users  WHERE id = '" . $oOrderTotal->user_id . "' ";
    $database->setQuery($query);
    $rows = $database->loadResult();
    $oRatePrev = $rows;
    $oRateNext = $oRatePrev;
    if ($oRatePrev < 10) {
        if ($oOrderTotal->order_total >= 100) {
            if ((($oOrderTotal->order_total + $price_change) >= 60) && (($oOrderTotal->order_total + $price_change) < 100)) {
                $oRateNext--;
            }
            if (($oOrderTotal->order_total + $price_change) < 60) {
                $oRateNext -= 2;
            }
        }
        if ($oOrderTotal->order_total >= 60) {
            if (($oOrderTotal->order_total + $price_change) >= 100) {
                $oRateNext++;
            }
            if (($oOrderTotal->order_total + $price_change) < 60) {
                $oRateNext--;
            }
        }
        if ($oRateNext > 10) {
            $oRateNext = 10;
        }
        $query = "UPDATE #__users SET rate={$oRateNext}  WHERE id = '{$oOrderTotal->user_id}' ";
        $database->setQuery($query);
        $database->query();
        if ($oRateNext != $oRatePrev) {
            $timestamp = time() + ( (-1) * 60 * 60 );
            $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
            $query = " INSERT INTO #__rate_history (id,user_name,date,coment,prev_rate) VALUES({$oOrderTotal->user_id}, 'Bloomex System','{$mysqlDatetime}','{$str_comm}','{$oRatePrev}') ";
            $database->setQuery($query);
            $database->query();
        }
    }
}

function SetOrderCondition() {
    global $database;

    $return = array();
    $return['result'] = true;

    $order_id = (int) $database->getEscaped($_POST['order_id']);
    $soft_fraud = ($database->getEscaped($_POST['soft_fraud']) == 'true') ? 1 : 0;
    $hard_fraud = ($database->getEscaped($_POST['hard_fraud']) == 'true') ? 1 : 0;
    $inadequate_customer_behavior = ($database->getEscaped($_POST['inadequate_customer_behavior']) == 'true') ? 1 : 0;
    $fair_chargeback_suspecting = ($database->getEscaped($_POST['fair_chargeback_suspecting']) == 'true') ? 1 : 0;
    $res = false;
    $query = "SELECT *
    FROM `tbl_order_condition`
    WHERE `order_id`=" . $order_id . " ";

    $database->setQuery($query);
    $database->loadObject($res);
    if ($res) {
        $query = "UPDATE `tbl_order_condition` SET 
            `soft_fraud`='" . $soft_fraud . "',
            `hard_fraud`='" . $hard_fraud . "',
            `inadequate_customer_behavior`='" . $inadequate_customer_behavior . "',
            `fair_chargeback_suspecting`='" . $fair_chargeback_suspecting . "'
            WHERE `order_id`=" . $order_id;
        $database->setQuery($query);
        if (!$database->query()) {
            $return['result'] = false;
            $return['error'] = $database->_errorMsg;
        }
    } else {
        $query = "INSERT INTO `tbl_order_condition`
        (
            `order_id`,
            `soft_fraud`,
            `hard_fraud`,
            `inadequate_customer_behavior`,
            `fair_chargeback_suspecting`
        )
        VALUES (
            " . $order_id . ",
            '" . $soft_fraud . "',
            '" . $hard_fraud . "',
            '" . $inadequate_customer_behavior . "',
            '" . $fair_chargeback_suspecting . "'
        )";
        $database->setQuery($query);
        if (!$database->query()) {
            $return['result'] = false;
            $return['error'] = $database->_errorMsg;
        }
    }

    echo json_encode($return);
    die;
}


function getCancelLink()
{
    global $database;
    $carrier = $_POST['carrier'];
    $return = array();

    $query = "SELECT d.cancel_endpoint as cancelLink
            FROM jos_vm_deliveries as d
            WHERE d.name = '".$carrier."'";;

    $database->setQuery($query);
    $exist = $database->loadObject($delivery);
    if (!$exist) {
        $return['result'] = false;
    } elseif ($delivery->cancelLink === '') {
        $return['cancel_link'] = '/scripts/deliveries/cancel.php';
        $return['result'] = true;
    } else {
        $return['cancel_link'] = $delivery->cancelLink;
        $return['result'] = true;
    }

    echo json_encode($return);
    exit(0);
}

function checkOrderHasActiveDelivery() {
    global $database;
    $order_id = $_POST['order_id'];
    $return = array();

    $query = "SELECT od.shipment_id      as trackingNumber,
                     od.dateadd  as createdAt,
                     c.name      as deliveryCompany,
                     o.warehouse as warehouseCode,
                     w.timezone  as timezone
            FROM jos_vm_orders_deliveries as od
            INNER JOIN jos_vm_deliveries as c on c.id = od.delivery_type
            LEFT JOIN jos_vm_orders as o on o.order_id = od.order_id
            LEFT JOIN jos_vm_warehouse as w on w.warehouse_code = o.warehouse
            WHERE od.order_id = $order_id
            AND od.active = 1";

    $database->setQuery($query);
    $exist = $database->loadObject($delivery);

    if (!$exist) {
        $return['result'] = false;
    } else {
        $dateWarehouseNow = new DateTime($delivery->timezone);
        $dateWarehouseCreatedAt = new DateTime($delivery->createdAt);
        $diffDays = $dateWarehouseNow->diff($dateWarehouseCreatedAt)->days;

        $return['shipment_id'] = $delivery->trackingNumber;
        $return['carrier'] = $delivery->deliveryCompany;
        $return['created'] = $dateWarehouseCreatedAt->format('M d Y');
        $return['warehouse'] = $delivery->warehouseCode;
        $return['diffDays'] = $diffDays;
        $return['result'] = true;
    }

    echo json_encode($return);
    exit(0);
}

function sendOrderToCarrier()
{
    $order_id = $_POST['order_id'];
    $carrier = $_POST['carrier'];

    $shipment = ShipmentFactory::build($carrier, $order_id);

    $return = $shipment->make();
    if ($return['success']) {
        echo json_encode(array('success' => true, 'shipment_id' => $return['shipment_id']));
    } else {
        echo json_encode(array('success' => false, 'error' => $return['error']));
    }
    exit(0);
}

function printShipmentLabel()
{
    $order_id = $_POST['order_id'];
    $carrier = $_POST['carrier'];
    $shipment_id = $_POST['shipment_id'];
    $shipment = ShipmentFactory::build($carrier, $order_id);

    $shipment->printLabel($shipment_id);

}

?>
