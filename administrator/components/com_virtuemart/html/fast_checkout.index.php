<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: checkout.index.php,v 1.5.2.3 2006/04/27 19:35:52 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU Gen eral Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

//print_r($_SESSION['coupon_discount']);
mm_showMyFileName(__FILE__);

global $database, $mm_action_url, $my, $cur_template, $mosConfig_show_compare_at_price,
       $mosConfig_offset, $mosConfig_stripe_enable, $mosConfig_au_stripe_secret_key,
       $mosConfig_absolute_path, $mosConfig_live_site;

$shipping_price = 19.95;


global $VM_LANG;
date_default_timezone_set('Australia/Sydney');
if (!isset($_SESSION['checkout_ajax'])) {
    $_SESSION['checkout_ajax'] = array();
}


include_once CLASSPATH . 'ps_for_checkout.php';
$ps_for_checkout = new ps_for_checkout;
$stripeSessionId = mosGetParam($_REQUEST, 'session_id', '');
$stripeOrderLogId = mosGetParam($_REQUEST, 'stripe_order_log_id', '');
$mosmsg = strip_tags(mosGetParam($_REQUEST, 'mosmsg', ''));

require_once $mosConfig_absolute_path . '/includes/stripe/init.php';
$stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);

if (!$stripeSessionId && !$stripeOrderLogId) {
    ?>
    <div id="ajaxloader"></div>
    <script type="text/javascript">
        document.getElementById("ajaxloader").style.display = "block";
        items = [];
        total_price = 0;
    </script>
    <?php
    $_SESSION['checkout_ajax']['user_id'] = $my->id;
    $_SESSION['checkout_ajax']['user_name'] = $my->username;

    if (!isset($_SESSION['checkout_ajax']['shipping_method'])) {
        $_SESSION['checkout_ajax']['shipping_method'] = 31;
    }
    date_default_timezone_set('Australia/Sydney');
    $mysqlDatetime = date('Y-m-d G:i:s', time());
    $query = "INSERT INTO tbl_stripe_orders_logs ( order_status,date_added) VALUES ( 
                                    'pending_stripe',
                                    '" . $mysqlDatetime . "'
                                    )";
    $database->setQuery($query);
    $database->query();
    $stripeOrderLogId = $database->insertid() ?? '';


    $aud = array('AU', 'CA', 'NZ', '');
    $currency = in_array(getCountryByIp(), $aud) ? 'AUD' : 'USD';

    $success_url = "$mosConfig_live_site/fast-checkout/?session_id={CHECKOUT_SESSION_ID}&mosmsgsuccess=true&stripe_order_log_id=$stripeOrderLogId&mosmsg=Payment executed by Stripe Successfully";
    $cancel_url = "$mosConfig_live_site/cart/?mosmsg=Payment Failed";
    $orderItems = [];
    $existProducts = [];
    $products_price = 0;
    $total_price = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $cart_product) {
            if (isset($cart_product['product_id'])) {

                $product_final_price = number_format($cart_product['price'], 2, '.', '');

                $query = "SELECT 
                                p.*,`c`.`category_name`,
                                  `pp`.`saving_price`
                            FROM `jos_vm_product` as p 
                            LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
                            LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
                            LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                            WHERE p.`product_id`=" . (int)$cart_product['product_id'] . "  group by `p`.`product_id`
                            ";
                $database->setQuery($query);
                $product_obj = false;
                $database->loadObject($product_obj);

                if (
                    $cart_product['select_bouquet'] == 'petite' or
                    $cart_product['select_bouquet'] == 'deluxe' or
                    $cart_product['select_bouquet'] == 'supersize'
                ) {
                    $product_obj->product_name .= ' (' . htmlspecialchars($cart_product['select_bouquet']) . ')';
                }
                $products_price += $product_final_price * $cart_product['quantity'];
                $existProducts[] = $cart_product['product_id'];
                $orderItems[] = [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $product_final_price * 100,
                        'product_data' => [
                            'name' => $product_obj->product_name,
                            'images' => [
                                $mosConfig_live_site . '/components/com_virtuemart/shop_image/product/' . $product_obj->product_thumb_image
                            ]
                        ],
                    ],
                    'quantity' => (int)$cart_product['quantity']
                ];
                $total_price += $product_final_price * $cart_product['quantity'];
                ?>
                <script type="text/javascript">
                    // remove_from_cart ga4
                    items.push({
                                    item_name: '<?= $product_obj->product_name; ?>',
                                    item_id: '<?= $product_obj->product_sku; ?>',
                                    price: parseFloat("<?= $product_final_price; ?>").toFixed(2) * parseInt("<?= (int)$cart_product['quantity'] ?>"),
                                    discount: parseFloat("<?= $product_obj->saving_price; ?>").toFixed(2),
                                    item_category: "<?= $product_obj->category_name ?>",
                                    item_variant: "<?= $cart_product['select_bouquet'] ?? 'standard' ?>",
                                    quantity: "<?= (int)$cart_product['quantity'] ?>"
                                })
                                total_price += parseFloat("<?= $product_final_price; ?>").toFixed(2) * parseInt("<?= (int)$cart_product['quantity'] ?>");
                            </script>
                <?php
            }
        }
    }

    $sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (" . implode(',', $existProducts) . ") and promo='1'";
    $database->setQuery($sql);
    if ($database->loadResult() && count($existProducts) == 1) {
        $return['error'] = "Please add another product other than the gift to your cart!";
        mosRedirect('/cart/?mosmsg=' . $return['error']);
    }


    $_SESSION['checkout_ajax']['free_shipping_by_price'] = false;
    $query = "SELECT `price` FROM `jos_freeshipping_price` WHERE `public`=1";
    $free_shipping_result = false;
    $database->setQuery($query);
    $database->loadObject($free_shipping_result);

    if ($free_shipping_result) {
        if ($free_shipping_result->price <= $total_price) {
            $_SESSION['checkout_ajax']['free_shipping_by_price'] = true;
            $shipping_price = 0;
        }
    }

    $sql = "SELECT product_id FROM #__vm_product_options WHERE  product_id in (" . implode(',', $existProducts) . ") and no_delivery_order='0'";
    $database->setQuery($sql);
    if ($database->loadResult() && $shipping_price) {
        $orderItems[] = [
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => $shipping_price * 100,
                'product_data' => [
                    'name' => 'Delivery'
                ],
            ],
            'quantity' => '1'
        ];
    }

    $stripeSessionParams = [
        'payment_intent_data' => ['description' => 'Fast Checkout'],
        'success_url' => $success_url,
        'custom_text' => [
            'submit' => ['message' => '⚠️ After completing your payment, you will be automatically redirected to enter your shipping address and choose your delivery date.'],
        ],
        'cancel_url' => $cancel_url,
        'line_items' => $orderItems,
        'mode' => 'payment',
        'phone_number_collection' => ['enabled' => true],
    ];

    $corporate_discount = 0;
    if (isset($my->id)) {
        $query = "SELECT `SG`.`shopper_group_discount` FROM `jos_vm_shopper_vendor_xref` AS `SVX` INNER JOIN `jos_vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id` WHERE `SVX`.`user_id`=" . $my->id . " LIMIT 1";
        $corporate_discount_result = false;
        $database->setQuery($query);
        $database->loadObject($corporate_discount_result);

        if ($corporate_discount_result) {

            $corporate_discount = $products_price * $corporate_discount_result->shopper_group_discount / 100;
            $orderDiscountsStripe = number_format(abs($corporate_discount), 2, ".", "") * 100;

            if ($orderDiscountsStripe) {
                $orderDiscounts = [
                    'amount_off' => $orderDiscountsStripe,
                    'name' => 'Discount Price',
                    'duration' => 'once',
                    'currency' => $currency,
                ];
                $stripeCoupon = $stripe->coupons->create($orderDiscounts);
                $stripeSessionParams['discounts'][] = ['coupon' => $stripeCoupon->id];
            }

        }

    }

    $total_price += $shipping_price - $corporate_discount;
    $_SESSION['checkout_ajax']['total_price'] = $shipping_price + $products_price - $corporate_discount;
    $_SESSION['checkout_ajax']['products_price'] = $products_price;
    $_SESSION['checkout_ajax']['shipping_price'] = $shipping_price;
    if ($total_price< 0.5) {
        mosRedirect('/cart/?mosmsg=Total price is less than 0.5 and stripe can\'t create payment link, please reach out to our Customer Service Team');
    }

    $stripeSession = $stripe->checkout->sessions->create($stripeSessionParams);
    $return['stripePaymentUrl'] = $stripeSession->url;

    $_SESSION['checkout_ajax']['stripeSessionId'] = $stripeSession->id;

    $bt_obj = false;
    if($my->id >0){
        $query = "SELECT 
                        * 
                    FROM `jos_vm_user_info` 
                    WHERE 
                        `user_id`=" . (int) $my->id . "
                    AND 
                        `address_type`='BT'
                    ";
        $database->setQuery($query);

        $database->loadObject($bt_obj);
    }

    $query = "UPDATE `tbl_stripe_orders_logs`  SET 
                                     `order_data`='" . $database->getEscaped(serialize($_SESSION)) . "',
                                     `user_id`='".$database->getEscaped((int) $my->id)."',
                                     user_name = '".$database->getEscaped(($bt_obj->first_name ?? '').' '.($bt_obj->last_name??''))."',
                                user_email = '".$database->getEscaped(($bt_obj->user_email??''))."',
                                    order_total='".$database->getEscaped(number_format($total_price, 2, ".", ""))."'
                                            WHERE `id`='" . (int)$stripeOrderLogId . "' ";
    $database->setQuery($query);
    $database->query();


    $query_track = "INSERT INTO tbl_track_buy_now_clicks ( stripe_payment_url,user_id,stripe_log_id,date_added) VALUES ( 
                                    '" .  $stripeSession->url . "',
                                    '" .  $my->id . "',
                                    '" . (int)$stripeOrderLogId . "',
                                    '" . $mysqlDatetime . "'
                                    )";
    $database->setQuery($query_track);
    $database->query();

    ?>
    <script type='text/javascript'>

        pushGoogleAnalytics(
            'buy_now_click',
            items,
            total_price.toFixed(2)
        );

        setTimeout(function(){location.href = "<?php echo $stripeSession->url; ?>"} , 3000);
    </script>
    <?php


} else {

    if (!$stripeSessionId || !isset($_SESSION['checkout_ajax']) || empty($_SESSION['checkout_ajax'])) {
        mosRedirect('/cart/?mosmsg=session data parameter is empty, please reach out to our Customer Service Team');
    }

    $stripeSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
    $stripe_transaction_id = ($stripeSession->payment_intent ?? '');
    $stripe_payment_status = ($stripeSession->payment_status ?? '');

    if ($stripe_transaction_id && $stripe_payment_status == 'paid') {
        $stripeResponse = [
            $stripe_transaction_id,
            $mosmsg,
            $stripeSession->customer_details->email,
            $stripeSession->customer_details->name,
            $stripeSession->customer_details->phone,
            $stripeSession->customer_details->address->country,
            $stripeSession->customer_details->address->postal_code
        ];

        $ps_for_checkout->createFastOrder($stripeResponse, $stripeOrderLogId);
    } else {

        $query = "UPDATE `tbl_stripe_orders_logs`
                SET 
                    `order_status`='canceled'
                WHERE  
                    id = " . $database->getEscaped($stripeOrderLogId);
        $database->setQuery($query);
        $database->query();
    }
}
?>
