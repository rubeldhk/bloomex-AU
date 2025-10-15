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
require_once(CLASSPATH . "ps_checkout.php");
global $database,$mainframe, $mm_action_url, $my, $cur_template,$mosConfig_show_compare_at_price,$mosConfig_offset,$mosConfig_stripe_enable;

global $VM_LANG,$mosConfig_live_site;
date_default_timezone_set('Australia/Sydney');
if (!isset($_SESSION['checkout_ajax'])) {
    $_SESSION['checkout_ajax'] = array();
}
$_SESSION['checkout_ajax']['checkoutStepOrder'] = true;

if ($perm->is_registered_customer($auth['user_id'])) {
    $checkoutStep = $_REQUEST['checkoutStep'] ?? 1;

    $query = "SELECT * FROM `jos_vm_user_info` AS `ui`
                WHERE `ui`.`user_id`=" . (int)$auth["user_id"] . "
                AND `ui`.`address_type`='BT'";
    $database->setQuery($query);
    $bt_obj = false;
    $database->loadObject($bt_obj);
} else {
    $checkoutStep = 0;
}

include_once CLASSPATH . 'ps_for_checkout.php';
$ps_for_checkout = new ps_for_checkout;
$stripeSessionId = mosGetParam($_REQUEST, 'session_id', '');
$stripeOrderLogId = mosGetParam($_REQUEST, 'stripe_order_log_id', '');
$mosmsg = strip_tags(mosGetParam($_REQUEST, 'mosmsg', ''));

?>


<script type="text/javascript">
    sSecurityUrl = "<?php echo(SECUREURL != "" ? SECUREURL : $mm_action_url); ?>";
    sCurrentZipChecked = "";
    bMember = <?php echo $my->id; ?>;
    current_page = "checkout.index";
</script>

<?php

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

    $sString = "<select class='form-control' id='{$list_name}' name='{$list_name}' {$extra}>";
    $i = 1;
    foreach ($list as $key => $value) {
        if ($i == 1) {
            $sString .= "<option value='{$key}'>{$value}</option>";
        } else {
            $sString .= "<option value='{$key}'>{$key}</option>";
        }

        $i++;
    }

    $sString .= "</select>";
    return $sString;
}

function listYear($list_name, $selected_item = "", $extra = "", $max = 7, $from = 2009, $direct = "up")
{
    $sString = "";

    $sString = "<select class='form-control' id='{$list_name}' name='{$list_name}' {$extra}>";
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

$orderId = ($_SESSION['checkout_ajax']['thankyou_order_id']) ?:
    mosGetParam($_REQUEST, 'order_id', "");

?>

<style>
    .steps-form {
        display: table;
        width: 100%;
        position: relative;
    }

    .steps-form .steps-row {
        display: table-row;
    }

    .steps-form .steps-row .steps-step {
        display: table-cell;
        text-align: center;
        position: relative;
        border-top: 4px solid #6c757d;
    }

    .steps-form .steps-row .steps-step.active_step {
        border-top: 4px solid #007bff;
    }

    .steps-form .steps-row .steps-step.active_step .btn-circle {
        border: 4px solid #007bff;

    }

    .steps-form .steps-row .steps-step p {
        margin-top: -20px
    }

    .steps-form .steps-row .steps-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
    }

    .steps-form .steps-row .steps-step .btn-circle {
        display: inline-block;
        width: 50px;
        height: 50px;
        text-align: center;
        padding: 6px 0;
        font-size: 22px;
        line-height: 1.428571429;
        border-radius: 50%;
        position: relative;
        top: -26px;
        border: 4px solid #6c757d;
        color: #000;
        background: #F5F7FA;
    }

    .autocomplete-form-group {
        clear: both;
        padding: 0 15px;
    }

    .address_type_hidden, .top_1, .top_3, .breadcrumbs_wrapper, .bottom_1, .bottom_2, .bottom_3,#calculate {
        display: none;
    }

    .clear-left {
        clear: left;
    }

    .billing_address_btn, .shipping_address_btn {
        padding-left: 15px !important;
        clear: both;
        float: left;
        margin-left: 15px !important;
    }

    .delivery_date_wrapper {
        max-width: 100% !important;
    }

    .cart_wrapper {
        padding-top: 30px !important;
    }

    div.update_shipping_info_wrapper, div.update_billing_info_wrapper {
        display: block;
    }

    .clear-left span.title {
        margin-bottom: 5px;
        display: block;
    }
</style>
<div class="checkout_wrapper">
    <div class="container cart_wrapper" >
        <div class="row">
            <div class="col-12 p-0">
                <h3 class="express_checkout_title"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_TITLE; ?></h3>
                <p class="checkout_form_subtitle text-center"><?php echo $checkoutStep == 1 ? $VM_LANG->_PHPSHOP_CHECKOUT_TITLE_STEP_1 : ($checkoutStep == 2 ? $VM_LANG->_PHPSHOP_CHECKOUT_TITLE_STEP_2 . $orderId: ''); ?></p>
            </div>
        </div>
    </div>
    <?php

    if (!isset($checkoutStep) && !isset($_REQUEST['shippingParams']))
        mosRedirect('/cart/');

    elseif ($checkoutStep == 0) {

        include(PAGEPATH . 'checkout.register_form_newstyle.php');
    }
    elseif ($checkoutStep == 1) {
    if(isset($_SERVER['HTTP_REFERER']) &&
       trim(parse_url(urldecode($_SERVER['HTTP_REFERER']), PHP_URL_PATH), '/') == 'checkout')
    {
        $mainframe->updateUserSessionCartData($_SESSION['cart'] ?? []);
    }


        $required_fields = array('first_name', 'last_name', 'country', 'phone_1', 'user_email');
        $shopper_fields = array();

        $shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
        $shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
        $shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
        $shopper_fields['user_email'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL;
        $shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;

        $addresses = [];
        $queryAddresses = "
        SELECT 
            CONCAT(first_name, ' ', last_name) as name, 
            city, zip, 
            ui.state as state, 
            CONCAT(street_number, ' ', street_name) AS street,
            phone_1 as phone,
            user_info_id
        FROM jos_vm_user_info as ui
            WHERE user_id = " . (int)$my->id . "
            AND address_type = 'ST' and country = 'AUS'
            AND last_name IS NOT NULL AND last_name <> ''
            AND first_name IS NOT NULL AND first_name <> ''
            AND city IS NOT NULL AND city <> ''
            AND zip IS NOT NULL AND zip <> ''
             AND street_number <> ''
             AND street_name <> ''
             AND phone_1 <> ''
            ORDER BY cdate DESC";
        $database->setQuery($queryAddresses);
        $addresses = $database->loadObjectList();
        if (!empty($addresses)) {
            $defaultAddress = (object) [
                'user_info_id' => null,
                'name' => $VM_LANG->_CHECKOUT_MY_ADDRESSES_DEFAULT,
                'street' => '',
                'city' => '',
                'state' => '',
                'zip' => ''
            ];
            array_unshift($addresses, $defaultAddress);
        }


        echo '<script src="/templates/'.$cur_template.'/js/googleaddress-checkout.js?ver=3"></script>';
    $show_basket = true;
        ?>
    <div class="container p-0 delivery_wrapper">
        <div class="row">
            <div class="col-12 col-lg-6" id="viewBillingInfoForm">
                <h3 class="text-center mt-4 mb-2 checkout_form_title"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL_STEP1; ?></h3>
                    <div class=" wrapper px-2">
                        <?php
                        foreach ($shopper_fields as $key => $value) {
                            ?>
                            <div class="form-group">
                                <label for="login"><?php echo $value; ?><?php echo in_array($key, $required_fields) ? '*' : ''; ?>
                                    :</label>
                                <?php

                                switch ($key) {

                                    case 'country':
                                        echo $ps_html->list_country('billing_info_country', $bt_obj->country?:'AUS', 'id="billing_info_country" class="checkout_form_input" autocomplete="new-password"', 'billing_info_country');
                                        break;

                                    case 'user_email':
                                        ?>
                                        <input type="email" autocomplete="new-password" class="checkout_form_input"
                                               autocomplete="new-password"
                                               id="billing_info_<?php echo $key; ?>"
                                               name="billing_info_<?php echo $key; ?>"
                                               title="Please enter a valid email address"
                                               value="<?php echo htmlspecialchars($bt_obj->$key); ?>" placeholder="e.g., yourname@example.com">
                                        <span class="help-block error">Please enter a valid email address. For example johndoe@domain.com</span>

                                        <?php
                                        break;
                                    case 'phone_1':
                                        ?>
                                        <input type="text" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 && /^\d{0,15}$/.test(this.value));"
                                               autocomplete="new-password"
                                               class="checkout_form_input"
                                               id="billing_info_<?php echo $key; ?>"
                                               name="billing_info_<?php echo $key; ?>"
                                               value="<?php echo htmlspecialchars($bt_obj->$key); ?>"
                                               placeholder="Enter your phone number">
                                        <?php
                                        break;
                                    default:
                                        ?>
                                        <input type="text"
                                               autocomplete="new-password"
                                               class="checkout_form_input"
                                               id="billing_info_<?php echo $key; ?>"
                                               name="billing_info_<?php echo $key; ?>"
                                               value="<?php echo htmlspecialchars($bt_obj->$key); ?>"
                                               placeholder="Enter your <?php echo str_replace('_',' ',$key); ?>">
                                        <?php
                                        break;
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
            </div>
            <div class="col-12  col-lg-6">
                <h3 class="text-center mt-4 mb-2 checkout_form_title">Shopping Cart Summary</h3>
                <?php include_once PAGEPATH.'basket.php'; ?>
            </div>
        </div>
    </div>

        <script>
            window.addEventListener(
                'load',
                function () {
                    loadScript('https://maps.googleapis.com/maps/api/js?key=AIzaSyDFRP59njojtx0eXlHmvYyGAtWZFwvRSLU&libraries=places&callback=initAutocomplete&language=en')
                },
            false);
            var billing_info__fields = <?php echo json_encode($shopper_fields); ?>;
            var billing_info_required_fields = <?php echo json_encode($required_fields); ?>;
            (function() {
                setTimeout(function () {
                    KlaviyoTracker.track('Started Checkout', shoppingCartData)
                }, 2000);
            })();
        </script>
        <div class="container delivery_wrapper">
            <div class="row">
                <div class="col-12">
                    <hr>
                    <h3 class="text-center mt-4 mb-2 checkout_form_title"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_PAYMENT_LBL_STEP2; ?></h3>
                </div>
            </div>
        </div>
        <?php
        $link = "?cart_products=";
        foreach ($_SESSION['cart'] as $key => $prod) {
            $lastElement = end($_SESSION['cart']);
            if (is_int($key)) {
                if ($prod == $lastElement)
                    $link .= $prod['product_id'] . "," . $prod['quantity'];
                else
                    $link .= $prod['product_id'] . "," . $prod['quantity'] . ";";
            }
        }


        $sql = "DELETE FROM tbl_cart_abandonment WHERE user_id=" . $auth['user_id'] . " AND status ='abandonment' ";
        $database->setQuery($sql);
        $database->query();

        $sql = "INSERT INTO tbl_cart_abandonment  (link, user_id,datetime_dt,status,first_name,number) VALUES ('" . $link . "','" . $auth['user_id'] . "','" . date('Y-m-d H:i:s') . "','abandonment','" . $auth['first_name'] . "','" . $bt_obj->phone_1 . "') ";
        $database->setQuery($sql);
        $database->query();
    ?>
        <div class="container delivery_wrapper">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 delivery_inner p-0">
                    <label class="container"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FIND_US_2; ?>
                        <input type="checkbox" name="find_us" id="find_us" checked value="1">
                        <span class="checkmark"></span>
                    </label>
                    <?php
                    $query = "SELECT `bucks` FROM `tbl_bucks` WHERE `user_id`=" . (int)$my->id . "";
                    $database->setQuery($query);
                    $redeem_bucks = $database->loadResult();
                    if ($redeem_bucks AND $redeem_bucks > 0) {
                        $redeem_bucks = number_format($redeem_bucks, 2, '.', '');
                        ?>
                        <label class="container"><?php echo $PHPSHOP_LANG->_VM_BLOOMEX_BUCKS; ?>
                            ($<?php echo $redeem_bucks; ?>)
                            <input type="checkbox" name="redeem_bucks" id="redeem_bucks" value="1">
                            <span class="checkmark"></span>
                        </label>
                        <?php
                    }

                            $query = "SELECT 
                            `uc`.`credits`
                        FROM `jos_vm_users_credits` AS `uc`
                        WHERE `uc`.`user_id`=" . (int)$my->id . "";

                            $user_credits = false;

                            $database->setQuery($query);
                            $user_credits_result = $database->loadObject($user_credits);
                            if ($user_credits_result AND $user_credits->credits > 0) {
                                $credits = number_format($user_credits->credits, 2, '.', '');
                                ?>
                                <label class="container">Redeem Credit ($<?php echo $credits; ?>)
                                    <input type="checkbox" name="redeem_credits" id="redeem_credits" value="1">
                                    <span class="checkmark"></span>
                                </label>
                                <?php
                            }
                            ?>
                            <?php

                            $donation_obj = json_decode($ps_for_checkout->getDonation());
                            if ($donation_obj AND isset($donation_obj->donate)) {
                                ?>
                                <label class="container donation" <?php echo(isset($donation_obj) ? '' : 'style="display: none;"'); ?>>
                            <span id="donation_label"
                                  title="<?php echo(isset($donation_obj) ? $donation_obj->donate->text : ''); ?>">
                                <?php echo(isset($donation_obj) ? $donation_obj->donate->label : ''); ?>
                            </span>
                                    <input type="checkbox" name="donation_id" id="donation_id"
                                           value="<?php echo(isset($donation_obj) ? $donation_obj->donate->id : ''); ?>">
                                    <span class="checkmark"></span>
                                </label>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <br/>
                    <input type="hidden" id="selected_user_info_id" name="user_info_id" value="">
                    <?php if (!empty($addresses) && count($addresses) > 0): ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 delivery_inner">
                                <h5><?= $VM_LANG->_CHECKOUT_MY_ADDRESSES;?></h5>
                                <div id="shipping_addresses" class="address-grid">
                                    <?php
                                    $count = 0;
                                    foreach ($addresses as $index => $address) {
                                        $checked = ($index === 0) ? 'checked' : '';
                                        $hiddenClass = ($index >= 3) ? 'hidden-address' : '';
                                        $radioId = "shipping_address_{$index}";
                                        echo "<label class='address-item {$hiddenClass}' for='{$radioId}' data-user-info-id='{$address->user_info_id}' style='display: ".($index < 3 ? "flex" : "none").";'>";
                                        echo "<input type='radio' id='{$radioId}' name='shipping_address' value='{$index}' {$checked} class='radio-hidden'>";
                                        echo "<span class='checkmark'></span>";
                                        echo "<div class='address-content'>";
                                        echo "<strong>{$address->name}</strong><br>";
                                        echo htmlspecialchars("{$address->street}") . "<br>";
                                        $addressParts = array_filter([$address->city, $address->state, $address->zip]);
                                        echo htmlspecialchars(implode(', ', $addressParts)) . "<br>";
                                        echo htmlspecialchars("{$address->phone}");
                                        echo "</div>";
                                        echo "</label>";

                                        $count++;
                                    }
                                    ?>
                                </div>

                                <?php if ($count > 3): ?>
                                    <button id="showMoreAddresses" class="btn"><?= $VM_LANG->_CHECKOUT_MY_ADDRESSES_SHOW_BTN; ?></button>
                                    <button id="hideMoreAddresses" class="btn" style="display: none;"><?= $VM_LANG->_CHECKOUT_MY_ADDRESSES_HIDE_BTN; ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 mt-4  delivery_inner mb-3">
                            <b for="googleaddressupdate_shipping">Delivery City/Town/Suburb</b>
                            <input id="googleaddressupdate_shipping" class="checkout_form_input" type="text"
                                   placeholder="Start typing delivery City/Town"/>
                        </div>

                    </div><br>
                    <div class="row justify-content-start">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">

                            <span class="title h"><b>Delivery State:<font color="red">*</font></b></span>
                            <div class="state_wrapper">
                                <?php echo $ps_html->dynamic_state_lists('shipping_info_country', 'shipping_info_state', 'AUS'); ?>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE2; ?>:</span>

                                <input type="text" class="checkout_form_input" name="delivery_date_2" onclick="selectDeliveryDateLight()" id="delivery_date_2"
                                       readonly="readonly" maxlength="10" placeholder="<?php echo $VM_LANG->_PHPSHOP_SELECT_DELIVERY_DATE; ?>"/>
                            <div class="ddate_tooltip">Select the delivery date again.</div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 delivery_inner">

                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 calendar_wrapper">
                                <div class="close_form"></div>

                                <div class="calendar">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="container payment_wrapper <?php if($mosConfig_stripe_enable) { echo 'hide_payment_wrapper';}?>" >
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYMENT_LBL ?></h3>
                    <hr/>
                </div>
                <?php if($mosConfig_stripe_enable) {?>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 payment_method_inner">
                        <input name="payment_method_state" id="stripe_payment_method" class="form-input" checked="checked" value="stripe" type="radio">
                        <img src="/templates/bloomex_adaptive/images/stripe.png" alt="Stripe"/>
                        <label for="stripe_payment_method">(Pay secure via Stripe. <a href="https://stripe.com/en-ca/resources/more/secure-payment-systems-explained" target="_blank">Read more about  Stripe Payment Processing.</a> )</label>
                    </div>
                <?php } else { ?>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 payment_method_inner">
                    <input name="payment_method_state" id="cart_payment_method" value="card" class="form-input"  checked="checked" type="radio">
                    <div class="payment_method unactive visa">
                        <img src="/templates/bloomex_adaptive/images/visa.svg" alt="Visa"/>
                    </div>
                    <div class="payment_method unactive mastercard">
                        <img src="/templates/bloomex_adaptive/images/mastercard.svg" alt="Master Card"/>
                    </div>
                    <div class="payment_method unactive amex">
                        <img src="/templates/bloomex_adaptive/images/amex.svg" alt="Amex"/>
                    </div>
                    <div class="payment_method unactive discover">
                        <img src="/templates/bloomex_adaptive/images/discover.svg" alt="Discover"/>
                    </div>

                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 payment_inner">
                    <div class="form-group">
                        <label for="name_on_card"><?php echo $VM_LANG->_VM_NAME_ON_CARD; ?><font
                                    color="red">*</font>:</label>
                        <input type="text" class="form-control" id="name_on_card" name="name_on_card" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="card_number"><?php echo $VM_LANG->_VM_CREDIT_CARD_NUMBER; ?><font
                                    color="red">*</font>:</label>
                        <input type="text" class="form-control" maxlength="16" id="card_number" name="card_number"
                               placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="card_cvv"><?php echo $VM_LANG->_VM_CREDIT_CARD_SECURITY_CODE; ?><font
                                    color="red">*</font>:</label>
                        <input type="text" class="form-control" maxlength="4" id="card_cvv" name="card_cvv"
                               placeholder="">
                    </div>
                    <div class="form-group">
                        <label id="expire_label" for="expire_date"><?php echo $VM_LANG->_VM_EXPIRY_MONTH; ?><font
                                    color="red">*</font>:</label>
                        <?php echo listMonth('expire_month', null, 'size="1" '); ?>
                        <?php echo listYear('expire_year', date('Y'), 'size="1" ', 30, date('Y')); ?>
                        <div class="attention">
                            <?php echo $VM_LANG->_VM_CREDIT_CARD_NOTE; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
                <div class="container price_wrapper">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h3><?php echo $VM_LANG->_VM_ORDER_PRICE_DETAIL ?></h3>
                            <hr/>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 price_inner">
                            <div class="total_item_price">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_TOTAL_ITEMS_PRICE; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>


                            <div class="corporate_discount">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_CORPORATE_DISCOUNT; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>

                            <div class="coupon_discount">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_COUPON_DISCOUNT; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>

                            <div class="total_delivery_price">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_DELIVERY_FEE; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>

                            <div class="total_bloomex_bucks">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_BLOOMEX_BUCKS; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>
                            <div class="total_credits">
                            <span class="title">
                                Redeem Credit
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>
                            <div class="total_donate">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_DONATION; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>

                            <div class="total_price">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_TOTAL_PRICE; ?>
                            </span>
                                <span class="price">
                                N/A
                            </span>
                            </div>
                            <?php if($mosConfig_show_compare_at_price){ ?>
                                <div class="total_item_saved_price">
                            <span class="title">
                                <?php echo $VM_LANG->_VM_TOTAL_ITEMS_SAVED_PRICE; ?>
                            </span>
                                    <span class="price">
                                N/A
                            </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">

            </div>
        </div>
    </div>


        <div class="container checkout_buttons_wrapper">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 checkout_buttons delivery_inner">
                    <?php if(checkShoppingCartContainAlcohol()) {?>
                        <label class="container drink_age_message"><?php echo $PHPSHOP_LANG->_PHPSHOP_ORDER_DRINK_AGE_REQUIRED; ?>
                            <input type="checkbox" name="proof_drinking_age" id="proof_drinking_age" value="1" checked>
                            <span class="checkmark"></span>
                        </label>
                    <?php } ?>
                    <button type="submit" class="btn"
                            id="calculate"><?php echo $VM_LANG->_VM_CALCULATE_BUTTON; ?></button>
                    <button type="submit" class="btn" <?= ($mosConfig_live_site == 'https://bloomex.com.au') ? 'id="confirm"' : ' onclick="submitorder()"'; ?> >PAY & GO TO DELIVERY</button>
                    <p style="margin-top: 10px;color: #808080;font-size: 12px;">By clicking "Pay & Go to Delivery" you accept our <a  style="color:#A40001" href="/terms-and-conditions/" target="_blank">Terms & Conditions</a></p>

                    <input type="hidden" id="nextcheckoutStep" value="2"/>

                    <button style="visibility: hidden;display: none" class="g-recaptcha capcha_validate"
                            data-sitekey="6LcfVQYaAAAAANTgSUbCcBi0xb9E4SzHVLK3JbyF"
                            data-callback="submitorder">
                    </button>
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                    <div class="result">Choose a delivery date.</div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 checkout_processing">
                    <span>Please wait, while Your order being processed...</span>
                </div>
            </div>
        </div>
        <div class="hidden_images">
            <img src="/templates/<?php echo $cur_template; ?>/images/truck.png"/>
            <img src="/templates/<?php echo $cur_template; ?>/images/truck_express.png"/>
        </div>
    <?php
    }elseif ($checkoutStep == 2) {

    //redirect on TYP if virtual product in cart
    if (isset($_SESSION['checkout_ajax']['virtual'])) {
        mosRedirect("/purchase-thankyou/");
        unset($_SESSION['checkout_ajax']['virtual']);
        exit;
    }


    if($stripeSessionId) {

        global $mosConfig_absolute_path,$mosConfig_au_stripe_secret_key;
        require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
        $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);
        $stripeSession = $stripe->checkout->sessions->retrieve($stripeSessionId);

        $stripe_payment_status = ($stripeSession->payment_status??'');
        $stripe_transaction_id = ($stripeSession->payment_intent??'');

        if($stripe_payment_status == 'paid'){
            $stripeResponse = [$stripe_transaction_id,$mosmsg];
            $ps_for_checkout->SetOrder($stripeResponse,false,$stripeOrderLogId);
        }

    }


    if (isset($_REQUEST['shippingParams'])) {
        $shippingParams = str_rot13($_REQUEST['shippingParams']);
        $shippingParamsArr = explode(";", $shippingParams);


        if ($shippingParamsArr[0]) {

            $sqlCheckUser = "SELECT order_status FROM jos_vm_orders WHERE order_id='" . $shippingParamsArr[0] . "' and user_id=" . $_SESSION['auth']['user_id'];
            $database->setQuery($sqlCheckUser);
            $orderStatusFillShipping = $database->loadResult();
            if (!in_array($orderStatusFillShipping,['PD','X'])) {
                mosRedirect('/cart/?msg=You already filled address details for order ' . $shippingParamsArr[0] . ' and we are currently working on it');
            }

            $_SESSION['checkout_ajax']['thankyou'] = md5('thankyou' . $shippingParamsArr[0]);
            $_SESSION['checkout_ajax']['thankyou_order_id'] = $shippingParamsArr[0];
            $_SESSION['checkout_ajax']['account_email'] = $my->email;
        }
        if (isset($shippingParamsArr[1])) {
            $_SESSION['checkout_ajax']['user_info_id'] = $shippingParamsArr[1];

            $sqlCheckUser = "SELECT user_id FROM jos_vm_user_info WHERE user_info_id='" . $shippingParamsArr[1] . "' and user_id=" . $_SESSION['auth']['user_id'];
            $database->setQuery($sqlCheckUser);
            $rows = $database->loadResult();
            if (!$rows) {
                unset($_SESSION['checkout_ajax']);
                mosRedirect('/cart/');
            }
        }
    } else {
        $sql = "SELECT id FROM tbl_cart_abandonment WHERE user_id=" . $_SESSION['auth']['user_id'] . " AND (status ='abandonment' OR status='sent')   AND `datetime_dt`>'" . date('Y-m-d H:i:s', strtotime('-4 hours')) . "'";
        $database->setQuery($sql);
        $rows = $database->loadObjectList();

        $userInfoId = $_SESSION['checkout_ajax']['user_info_id'] ?? '';
        $orderId = ($_SESSION['checkout_ajax']['thankyou_order_id']) ?
            $_SESSION['checkout_ajax']['thankyou_order_id'] :
            mosGetParam($_REQUEST, 'order_id', "");

        if ($rows) {
            $sql = "UPDATE  tbl_cart_abandonment SET status='wait_delivery_address',order_id='" . $orderId . "',user_info_id='" . $userInfoId . "',datetime_dt='" . date('Y-m-d H:i:s') . "'  WHERE  id = " . $rows[0]->id;
            $database->setQuery($sql);
            $database->query();
        }
    }

    ?>
        <div class="container shipping_wrapper">
            <div class="row">
                <div class="col-12">

                    <?php
                    $required_fields = array('first_name', 'last_name', 'street_name', 'street_number','city', 'zip',  'country', 'state', 'phone_1');
                    $shopper_fields = array();

                    $shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
                    $shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
                    $shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
                    $shopper_fields['user_email'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL;
                    $shopper_fields['company'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY;
                    $shopper_fields['suite'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_SUITE;
                    $shopper_fields['street_number'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NUMBER;
                    $shopper_fields['street_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NAME;
                    $shopper_fields['city'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY;
                    $shopper_fields['zip'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP;
                    $shopper_fields['state'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE;
                    $shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;

                    $query = "SELECT `ui`.*,s.state_name FROM `jos_vm_user_info` AS `ui`
                                  left join jos_vm_state as s on s.state_2_code=`ui`.state and s.country_id=13 
                                    WHERE `ui`.`user_info_id`='" . $_SESSION['checkout_ajax']['user_info_id'] . "'
                                    AND `ui`.`address_type`='ST'";

                    $database->setQuery($query);
                    $st_obj = false;
                    $database->loadObject($st_obj);

                    $checkSimilarAddressQuery = "SELECT * FROM `jos_vm_user_info` WHERE `address_type` = 'ST' AND user_id=" . (int) $my->id . "  AND `state` = '".$st_obj->state."' AND `zip` = '".$st_obj->zip."' ";
                    $database->setQuery($checkSimilarAddressQuery);
                    $checkSimilarAddress = $database->loadObjectList();

                    if ($checkSimilarAddress && count($checkSimilarAddress) > 1) {
                        echo '<div class="shipping_addresses_wrapper"><table class="table shipping_addresses">';
                        foreach ($checkSimilarAddress as $row) {
                            echo '<tr user_info_id="' . $row->user_info_id . '">
                            <td>
                                <label class="radio_container"><input type="radio"  name="radio_shipping_address" value="' . $row->user_info_id . '" ' . ($_SESSION['checkout_ajax']['user_info_id'] == $row->user_info_id ? 'checked' : '') . '><span class="checkmark"></span></label>
                            </td>
                            <td>
                                <div class="data">
                                    <span>Name:</span> ' . $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name . '
                                    <br/>
                                    <span>Address:</span> ' . $row->suite . ' ' . $row->street_number . ' ' . $row->street_name . ', ' . $row->city . ', ' . $row->zip . ', ' . $row->state . ', ' . $row->country . '
                                </div>
                            </td>
                        </tr>';
                        }
                        echo '</table></div>';
                    }


                    $query = "SELECT * FROM `jos_vm_orders` 
                                    WHERE `order_id`=" . $_SESSION['checkout_ajax']['thankyou_order_id'];

                    $database->setQuery($query);
                    $order_obj = false;
                    $database->loadObject($order_obj);

                    ?>

                    <?php echo (isset($bt_obj->country) && $bt_obj->country == 'AUS' && $bt_obj->zip == $st_obj->zip && $bt_obj->state == $st_obj->state) ? '<p> Click <b class="sameAsBillingBtn">here</b> to fill inputs same as billing</p>' : ''; ?>

                    <form role="form" action="index.php" method="post" id="update_shipping_info_form">
                        <div class="row">
                        <?php
                        foreach ($shopper_fields as $key => $value) {
                            ?>
                            <div class="form-group  col-12 col-lg-6">
                                <label for="shipping_info_<?php echo $key; ?>"><?php echo $value; ?><?php echo in_array($key, $required_fields) ? '*' : ''; ?>
                                    :</label>
                                <?php
                                Switch ($key) {
                                    case 'state':
                                        echo $ps_html->list_states("shipping_info_state", $st_obj->state, '13', "id='shipping_info_state' readonly class='form-control' style='display:none'", false);
                                        echo "<input type='text' class=\"checkout_form_input\" readonly value='" . $st_obj->state_name . "'>";
                                        break;
                                    case 'country':
                                        echo $ps_html->list_country('shipping_info_country', 'AUS', 'id="shipping_info_country" only="AUS" autocomplete="new-password"  style="display:none" readonly  onchange="changeStateList(\'shipping_info_state\', \'shipping_info_country\');"', 'shipping_info_country');
                                        echo "<input type='text' class=\"checkout_form_input\" readonly value='Australia'>";
                                        break;
                                    case 'user_email':
                                        ?>
                                        <input type="email" autocomplete="new-password" class="checkout_form_input"
                                               id="shipping_info_<?php echo $key; ?>"
                                               name="shipping_info_<?php echo $key; ?>"
                                               value="<?php echo htmlspecialchars($st_obj->$key); ?>" placeholder="">
                                        <?php
                                        break;

                                    case 'title':
                                        echo $ps_html->list_user_title('', 'name="shipping_info_title" autocomplete="new-password"  id="shipping_info_title"');
                                        break;

                                    case 'zip':
                                        ?>
                                        <input type="text" autocomplete="new-password" class="checkout_form_input"
                                               id="shipping_info_zip"  maxlength="4" name="shipping_info_zip"
                                               value="<?php echo htmlspecialchars($st_obj->$key); ?>" placeholder="">
                                        <?php
                                        break;
                                    default:
                                        ?>
                                        <input type="text" autocomplete="new-password" class="checkout_form_input"
                                               id="shipping_info_<?php echo $key; ?>"
                                               name="shipping_info_<?php echo $key; ?>"
                                               value="<?php echo htmlspecialchars($st_obj->$key); ?>" placeholder="">
                                        <?php
                                        break;
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <script type="text/javascript">
                            var shipping_info_fields = <?php echo json_encode($shopper_fields); ?>;
                            var shipping_info_required_fields = <?php echo json_encode($required_fields); ?>;
                        </script>
                        <div class="form-group col-12 col-lg-6">
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_OCCASION; ?>:</span>
                            <?php $ps_html->list_user_occasion('customer_occasion', 'id="customer_occasion"', ($order_obj->customer_occasion)??''); ?>
                        </div>
                            <div class="clearfix"></div>
                        <div class="form-group col-12 col-lg-6">
                            <span class="title h"
                                  id="card_msg_title"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?>:</span>
                            <textarea class="form-control mb-3"
                                      title="<?php echo strip_tags($VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE) ?>" rows="4"
                                      name="card_msg" id="card_msg"><?php echo ($order_obj->customer_note)??''; ?></textarea>
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_SIGNATURE ?>:</span>
                            <textarea class="form-control mb-3"
                                      title="<?php echo strip_tags($VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_SIGNATURE) ?>" rows="3"
                                      name="signature"
                                      id="signature"><?php echo ($order_obj->customer_signature)??''; ?></textarea>
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS ?>:</span>
                            <textarea class="form-control mb-3"
                                      title="<?php echo strip_tags($VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS) ?>" rows="3"
                                      name="card_comment"
                                      id="card_comment"><?php echo ($order_obj->customer_comments)??''; ?></textarea>
                        </div>
                        <div class="clearfix"></div>
                            <div class="col-12">
                                <button type="submit" class="btn shipping_address_btn"
                                        onclick="return checkUpdateShippingInfoFormExtended(event);">COMPLETE ORDER
                                </button>
                            </div>

                        <input type="hidden" id="checkoutStep" name="checkoutStep" value="2"/>
                        <input type="hidden" id="shipping_info_user_info_id" name="shipping_info_user_info_id"
                               value="<?php echo $_SESSION['checkout_ajax']['user_info_id']; ?>"/>
                        <input type="hidden" id="order_id" name="order_id"
                               value="<?php echo $_SESSION['checkout_ajax']['thankyou_order_id']; ?>"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php
    }
    

    ?>
    <script>
        function loadScript(url, callback) {
            var script = document.createElement('script');
            script.setAttribute('type', 'text/javascript');
            if (typeof callback === 'function') {
                script.addEventListener('load', callback, false);
            }
            script.setAttribute('src', url);
            document.body.appendChild(script);
        }


        $('#shipping_info_company').change(function () {
            if ($(this).val() != '') {
                $('#shipping_info_address_type2').val('Business').change()
            } else {
                $('#shipping_info_address_type2').val('Home/Residence').change()
            }
        })
    </script>

</div>
