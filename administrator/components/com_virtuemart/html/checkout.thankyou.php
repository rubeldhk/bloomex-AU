<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 * This file is called after the order has been placed by the customer
 *
 * @version $Id: checkout.thankyou.php,v 1.7 2005/10/24 18:13:07 soeren_nb Exp $
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
mm_showMyFileName(__FILE__);

require_once(CLASSPATH . 'ps_product.php');
$ps_product = new ps_product;
global $vendor_currency, $database, $mosConfig_live_site,$mosConfig_mailfrom,$mosConfig_mailfrom_noreply, $mosConfig_fromname,$VM_LANG,$prod,$mosConfig_ga4_gtm,$mosConfig_mailwizz_url,$mosConfig_mailwizz_api_key,$mosConfig_mailwizz_list_id;

$Itemid = mosGetParam($_REQUEST, "Itemid", null);
$order_id = isset($_SESSION['checkout_ajax']['thankyou_order_id'])?(int)$_SESSION['checkout_ajax']['thankyou_order_id']:'';

if(!$auth['user_id']) {
     $auth['user_id'] = $_SESSION['checkout_ajax']['user_id'];
}

if ((isset($_SESSION['checkout_ajax']['thankyou'])) AND ($_SESSION['checkout_ajax']['thankyou'] == md5('thankyou'.$order_id))) {




    unset($_SESSION['checkout_ajax']);
    unset($_SESSION['url_coupon_code']);
    unset($_SESSION['enableSpecialDiscountInProductsForCustomer']);
    $sql = "SELECT id FROM tbl_cart_abandonment WHERE user_id=".$_SESSION['auth']['user_id']." AND status !='finished' ";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    if ($rows) {
        date_default_timezone_set('Australia/Sydney');
        
        $sql = "UPDATE   tbl_cart_abandonment SET status='finished',datetime_dt='".date('Y-m-d H:i:s')."'  WHERE  user_id = ".$_SESSION['auth']['user_id'];
        $database->setQuery($sql);
         $database->query();
    }
    if (!empty($_SESSION['platinum_cart'])) {

        $sql = "SELECT id FROM tbl_platinum_club WHERE user_id=".$auth['user_id']."";
        $database->setQuery($sql);
        $rows = $database->loadObjectList();

        if(!$rows){
            $sql = "INSERT INTO tbl_platinum_club  (user_id,cdate) VALUES ('".$auth['user_id']."','".time()."')";
            $database->setQuery($sql);
            $database->query();
        }

        unset($_SESSION['platinum_cart']);
    }
    //========================== CLEAR CART ==========================
    unset($_SESSION['cart']);
    unset($_SESSION['coupon_redeemed']);
    unset($_SESSION['coupon_id']);
    unset($_SESSION['coupon_type']);
    unset($_SESSION['coupon_discount']);
    unset($_SESSION['coupon_code']);
    unset($_SESSION['coupon_value']);
    unset($_SESSION['coupon_code_type']);
    unset($_SESSION['url_coupon_code']);
    //================================================================

    if (isset($_REQUEST['msg']) AND trim($_REQUEST['msg']) == "error_order") {
        echo $VM_LANG->_VM_ORDER_ERROR_1;
    } else {
    ################## BEGIN GOOGLE CONVERSION CODE #####################################################
        ?>
    <style>
    @font-face {
    font-family: a_AvanteLt;
    src: url(/templates/bloomex7/fonts/a_AvanteLt.ttf);
    }
       .foot_review
       {
        font-family: a_AvanteLt;
        text-shadow: 1px 1px 2px black, 0 0 1em rgb(0, 0, 0);
        text-align: center;
        margin-top: -130px;
        left: 200px;
        color:#FFF;
        font-size: 30px;
        padding: 5px;
        line-height: 1.2;
        text-shadow: 1px 1px 2px black, 0 0 1em black;
       }

        .foot_review a
        {
           color: #FFF;
           text-decoration: underline;
        }

        .in_thankyou
        {
            padding-top: 20px;
            padding-left: 38px;
            padding-bottom: 20px;
            float: left;
        }

        .in_thankyou img
        {
            width: 360px;
            max-width: 360px;
        }

        .all_thankyou
        {
            display: none;
            margin: 0 auto;
            width: 900px;
        }
    .update-btn,
    .learn-more-btn {
        background-color: #B22222;
        border-color: #B22222;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: bold;
        margin-top: 10px;
    }
    .small-italic-text {
        font-size: 12px;
        font-style: italic;
        color: #666;
    }

    .thankyou-customer-name {
        display: inline-block;
        text-align: left;
        font-size: 35px;
        font-weight: bold;
        margin-left: 20px;
    }
    @media (max-width: 768px) {
        .update-btn,
        .learn-more-btn {
            width: 100%;
        }
        .thankyou-customer-name {
            font-size: 20px;
            margin-bottom: 10px;
        }
    }
    </style>

        <script type="text/javascript">
            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
            document.write(decodeURIComponent("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
        </script>

        <?php

        $dbod = new ps_DB;
        $dbx = new ps_DB;
        $dby = new ps_DB;
        $dbz = new ps_DB;
        $item_string = '';
        $tran_string = '';

    //Get customer and order details based on order number
        $user = false;
        $database->setQuery("SELECT `user_id`, `user_email`,city, state, country, zip,phone_1,last_name,first_name,street_name FROM `jos_vm_order_user_info` WHERE `order_id`=" . $order_id . " AND `address_type`='BT'");
        $database->loadObject($user);


        $dbod->query("SELECT order_total,w.warehouse_email,coupon_code,coupon_discount,order_subtotal, order_tax, order_shipping,warehouse,order_currency,po.id as order_set_pending_status 
                        FROM #__{vm}_orders 
                        left join #__{vm}_warehouse as w on w.warehouse_code=jos_vm_orders.warehouse  
                        left join #__{vm}_cards_for_pending_orders as po on po.order_id=jos_vm_orders.order_id  
                        WHERE jos_vm_orders.order_id = $order_id ");

        switch ($dbod->f("warehouse")){
            case 'WH12':
                $review_link ='https://search.google.com/local/writereview?placeid=ChIJq6qqqra8EmsRv1pciPfPzcE';
                break;
             case 'WH14':
                $review_link ='https://search.google.com/local/writereview?placeid=ChIJIRA300bjk2sRD_vFXPru7Q8';
                break;
             case 'WH15':
                $review_link ='https://search.google.com/local/writereview?placeid=ChIJPQlzFg5f1moRFkpoCEAG1FM';
                break;
             case 'p01':
                $review_link ='https://search.google.com/local/writereview?placeid=ChIJpTfKNkqxMioR4B8av9035Nk';
                break;
             default:
                $review_link ='https://search.google.com/local/writereview?placeid=ChIJq6qqqra8EmsRv1pciPfPzcE';
                break;
        }
        if($dbod->f("warehouse_email") && $prod){
            $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
            $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $dbod->f("warehouse_email"), $mail_Subject, $mail_Content, 1);
        }




    // Build Google Analytics Transaction line
    //==================== Ecommerce Tracking ====================
        $sEcommerceTracking = "_gaq.push(['_addTrans',
                                                                        '$order_id',           				// order ID - required
                                                                        'affiliate',  						// affiliation or store name
                                                                        '" . $dbod->f("order_total") . "',      // total - required
                                                                        '" . $dbod->f("order_tax") . "',        // tax
                                                                        '" . $dbod->f("order_shipping") . "',   // shipping
                                                                        '" . $user->city. "',       		// city
                                                                        '" . $user->state . "',     		// state or province
                                                                        '" . $user->country . "'           // country
                                                                      ]);";
    //=======z=====================================================

        $bucks_obj = $return['bucks_obj'] = false;

        $query = "SELECT `used_bucks` FROM `tbl_bucks_history`
        WHERE `order_id`=" . $order_id . "";
        $database->setQuery($query);
        $database->loadObject($bucks_obj);

        $query = "SELECT `donation_price` FROM `tbl_used_donation`
        WHERE `order_id`=" . $order_id . "";
        $database->setQuery($query);
        $database->loadObject($donate_obj);

        $query = "SELECT `shopper_discount_value` FROM `jos_vm_orders_extra`
        WHERE `order_id`=" . $order_id . "";
        $database->setQuery($query);
        $database->loadObject($shopper_discount_obj);

        $query = "SELECT `credits` FROM `jos_vm_users_credits_uses`
        WHERE `order_id`=" . $order_id . "";
        $database->setQuery($query);
        $database->loadObject($credits_obj);
        $additional_insights = [
            'redeem_bloomex_bucks'=> number_format($bucks_obj->used_bucks ?: 0, 2, '.', ''),
            'corporate_discount'=>  number_format($shopper_discount_obj->shopper_discount_value ?? 0, 2, '.', ''),
            'coupon_discount'=>  number_format($dbod->f("coupon_discount"), 2, '.', ''),
            'redeem_credit'=>  number_format($credits_obj->credits ?? 0, 2, '.', ''),
            'donation'=>  number_format($donate_obj->donation_price ?? 0, 2, '.', '')
        ];
        $discount = ($shopper_discount_obj->shopper_discount_value ?? 0) + $dbod->f("coupon_discount");

        $googleAnalyticsTransactionInfo = json_encode([
            'transaction_id' => $order_id,
            'shipping' =>  number_format($dbod->f("order_shipping"), 2, '.', ''),
            'discount' =>  number_format($discount, 2, '.', ''),
            'coupon' => $dbod->f("coupon_code"),
            'additional_insights'=> $additional_insights,
            'email'=> $user->user_email,
            'phone_number'=> $user->phone_1,
            'first_name'=> $user->first_name,
            'last_name'=> $user->last_name,
            'street'=> $user->street_name,
            'city'=> $user->city,
            'region'=> $user->state,
            'postal_code'=> $user->zip,
            'country'=> $user->country,
        ]);

        try {
            $config = new \EmsApi\Config([
                'apiUrl'    => $mosConfig_mailwizz_url,
                'apiKey'    => $mosConfig_mailwizz_api_key,
            ]);

            \EmsApi\Base::setConfig($config);

            $endpoint = new EmsApi\Endpoint\ListSubscribers();
            $response = $endpoint->emailSearch($mosConfig_mailwizz_list_id, $user->user_email);
            if($response->body['status'] != 'success') {
                $response = $endpoint->create($mosConfig_mailwizz_list_id, [
                    'EMAIL'    => $user->user_email,
                    'FNAME'    => $user->first_name,
                    'LNAME'    =>  $user->last_name,
                    'PHONE'    =>  $user->phone_1
                ]);
            }
        } catch (Exception $e) {}


        //Get Google Analytics Item line details

        $query = " SELECT i.order_item_sku,i.order_item_name, i.product_final_price, i.product_quantity, p.product_id, p.product_parent_id, c.category_name
                        FROM jos_vm_order_item as i
                        left join jos_vm_product as p on p.product_sku = i.order_item_sku 
                        left join jos_vm_product_category_xref as x on x.product_id = p.product_id
                        left join jos_vm_category as c on c.category_id = x.category_id
                        where i.order_id = '$order_id' GROUP by i.order_item_id";
        $googleAnalyticsItems = [];
        $itemIdes = [];
        $totalPrice = $dbod->f("order_total");
        $database->setQuery($query);
        $orderData = $database->loadObjectList();
        foreach ($orderData as $row) {
            //Check if product has a parent id - otherwise category is blank

            $product_id = $row->product_parent_id;

            if ($row->product_parent_id == "0") {
                $product_id = $row->product_id;
            }
            $itemIdes[] = $product_id;
//            $aPrice = $ps_product->get_retail_price($product_id);
            //Get product category info

            //==================== Ecommerce Tracking ====================
            $sEcommerceTracking .= " _gaq.push(['_addItem',
                                                                        '$order_id',           // order ID - required
                                                                        '" . $row->order_item_sku . "',         // SKU/code - required
                                                                        '" . $row->order_item_name . "',        // product name
                                                                        '" . $row->category_name . "',   		// category or variation
                                                                        '" . $row->product_final_price . "',    // unit price - required
                                                                        '" . $row->product_quantity . "'          // quantity - required
                                                                      ]);";
            //============================================================
            $itemType = strpos($row->order_item_name, 'deluxe') ?
                "deluxe" :
                (strpos($row->order_item_name, 'supersize') ?
                    'supersize' :
                    'standard'
                );


            $googleAnalyticsItems[] =[
                'item_name' => $row->order_item_name,
                'item_id' => $row->order_item_sku,
                'price' =>  number_format($row->product_final_price, 2, '.', ''),
//                'discount' => number_format($aPrice['saving_price'], 2, '.', ''),
                'item_category' => $row->category_name,
                'quantity' => $row->product_quantity,
                'item_variant' => $itemType,
            ];

            $orderProducts[] =[
                'item_name' => $row->order_item_name,
                'item_id' => $row->order_item_sku,
                'price' => $row->product_final_price,
                'item_category' => $row->category_name,
                'item_variant' => "standard",
                'quantity' => $row->product_quantity
            ];

        }
        $googleAnalyticsItems = json_encode($googleAnalyticsItems);
        $orderProductsJson = json_encode($orderProducts);


    ################## END GOOGLE CONVERSION CODE ######################################################
    ################## Ecommerce Tracking #################################
        echo "<script type='text/javascript'>
                    var _gaq = _gaq || [];
                    _gaq.push(['_setAccount', 'UA-50366851-1']);
                    _gaq.push(['_trackPageview']);

                    $sEcommerceTracking

                    _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

                    (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                    })();
                    
                    pushPurchaseGoogleAnalytics('purchase', $googleAnalyticsItems, $totalPrice, $googleAnalyticsTransactionInfo);
                    


    </script>";

        //facebook pixel tracking
        echo "<script type='text/javascript'>

         if (typeof fbq === 'undefined') {
                console.warn('Facebook Pixel might be blocked for tracking Purchase.');
        } else {
            setTimeout(function() {
                if (typeof fbq === 'function') {
                          fbq('track', 'Purchase', {
                                value: " . $totalPrice . ",
                                currency: '".$dbod->f("order_currency")."',
                                content_ids: '".$itemIdes."',
                                content_type: 'product'
                            });
                }
            }, 1000);
        }

    </script>";
    ###################################################################
        ?>

    <noscript>
    <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/968397865/?label=5FslCJ_N1QkQqajizQM&amp;guid=ON&amp;script=0"/>
    </div>
    </noscript>
        <center>
            <?php

            //send confirmation email
            require_once CLASSPATH . 'ps_comemails.php';
            $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='1'";
            $confirmation_obj = false;
            $database->setQuery($query);
            $database->loadObject($confirmation_obj);
            $ps_comemails = new ps_comemails;

            $isLastMinuteOrder = $_SESSION['isLastMinuteOrder'] ?? false;

            if ($isLastMinuteOrder) {
                $ps_comemails->isLastMinuteOrder = true;
                $ps_comemails->isLastMinuteOrderLabel = '<span style="font-style: italic;">Delay notified/accepted during checkout</span>';
            }
            unset($_SESSION['isLastMinuteOrder']);
            mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $user->user_email, $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject), $ps_comemails->setVariables($order_id, $confirmation_obj->email_html), 1);


            $db = new ps_DB;
            $q = "SELECT * FROM #__{vm}_payment_method, #__{vm}_order_payment, #__{vm}_orders ";
            $q .= "WHERE #__{vm}_order_payment.order_id='$order_id' ";
            $q .= "AND #__{vm}_payment_method.payment_method_id=#__{vm}_order_payment.payment_method_id ";
            $q .= "AND #__{vm}_orders.user_id='" . $auth["user_id"] . "' ";
            $q .= "AND #__{vm}_orders.order_id='$order_id' ";
            $db->query($q);

    //die("bbbbbbbbb$q bbbbbbbbbbbb");

            if ($db->next_record()) {
                ?>
                <div class="container thankyou_page_wrapper">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 inner"> 
                            <?php
                            $arr_log = explode("[--1--]", $db->f("order_payment_log"));
                            ?>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="image">
                                        <img src="/templates/bloomex_adaptive/images/finish.png" alt="Success" style="width: 60px; height: 60px;">
                                        <div class="thankyou-customer-name">
                                            <?php
                                            echo $VM_LANG->_PHPSHOP_THANKYOU_CUSTOMER_NAME
                                            ?>
                                        </div>
                                    </div >
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <?php
                                        echo '<div class="order-info" style="font-size: 16px; margin-bottom: 10px;text-align: left;"><b>Order No</b>: ' . $order_id . '</div>';
                                        echo '<div class="order-info" style="font-size: 16px; margin-bottom: 10px;text-align: left;"><b>Order Total</b>: ' . $dbod->f("order_total") . '</div>';
                                        echo '<div class="order-info" style="font-size: 16px; margin-bottom: 20px;text-align: left;"><b>Confirmation Email Sent To</b>: ' . $user->user_email . '</div>';
                                        echo '<a class="order-info" href="/account/" 
                                        class="btn btn-danger btn-lg" 
                                        style="background-color: #B22222; border-color: #B22222;
                                        color: #fff; padding: 10px 20px; float: left;
                                        border-radius: 20px; font-weight: bold; display: inline-block; margin-bottom: 10px;">'
                                            . $VM_LANG->_PHPSHOP_VIEW_ORDER_DETALES .
                                            '</a>';

                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12  mobile_attention_box">
                                    <!-- Левая колонка -->
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 float-start text-left">
                                        <h3 class="margin_1"> <?php echo $VM_LANG->_PHPSHOP_ORDER_UPDATES; ?></h3>
                                        <p class="order_updates_text margin_0" style="padding-bottom: 15px"><?php echo $VM_LANG->_PHPSHOP_YOU_WILL_RECEIVE_UPDATES_BY_EMAIL; ?></p>
                                        <button class="btn btn-danger update-btn" data-toggle="modal" data-target="#phoneNumberModal">
                                            <?php echo $VM_LANG->_PHPSHOP_GET_DELIVERY_UPDATES_BY_TEXT; ?>
                                        </button>
                                        <p class="small-italic-text"><?php echo $VM_LANG->_PHPSHOP_EMAIL_INFO; ?></p>
                                    </div>

                                    <!-- Правая колонка -->
                                    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6  float-end text-left">
                                        <h3 class="margin_1"><?php echo $VM_LANG->_PHPSHOP_CORPORATE_CLIENTS; ?></h3>
                                        <p class=" margin_0" style="padding-bottom: 15px"><?php echo $VM_LANG->_PHPSHOP_CORPORATE_DISCOUNT; ?></p>
                                        <a href="https://bloomex.ca/apply-for-20-corporate-discount/" target="_blank" class="btn btn-danger learn-more-btn">
                                            <?php echo $VM_LANG->_LEARN_MORE; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="image">


                                <?php
                                $database->setQuery("SELECT
                            `id`, 
                            `image`, 
                            `url`, 
                            `type`, 
                            `percent`
                        FROM `tbl_thankyou_review_links` 
                        WHERE `published`=1");
                                $review_obj = $database->loadObjectList();

                                if($review_obj){
                                    $rev_arr = array();
                                    $rev_arr_new = array();
                                    foreach ($review_obj as $rev){
                                        $rev_arr_new[$rev->id]=$rev;
                                        if($rev->type=='company'){
                                            if($rev->percent){
                                                for($j=0;$j<$rev->percent;$j++){
                                                    $rev_arr['company'][]=$rev->id;
                                                }
                                            }
                                        }
                                        if($rev->type=='google'){
                                            if($rev->percent){
                                                for($m=0;$m<$rev->percent;$m++){
                                                    $rev_arr['google'][]=$rev->id;
                                                }
                                            }
                                        }
                                    }
                                    $google_index = array_rand($rev_arr['google']);
                                    $google_review_link = $rev_arr_new[$rev_arr['google'][$google_index]]->url;
                                    $google_image_link = $rev_arr_new[$rev_arr['google'][$google_index]]->image;

                                    $company_index = array_rand($rev_arr['company']);
                                    $company_link = $rev_arr_new[$rev_arr['company'][$company_index]]->url;
                                    $company_image_link = $rev_arr_new[$rev_arr['company'][$company_index]]->image;

                                    ?>

                                    <div class="row" style="margin-top: 30px;">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <a href="<?php echo $google_review_link; ?>" target="_blank">
                                                <img style="width:100%" src="/images/thankyou_images/<?php echo $google_image_link; ?>">
                                            </a>
                                        </div>
                                    </div>

                                <?php } ?>

                            </div>

                            <?php
                            if ($db->f("order_status") == "P") {
                // Copy the db object to prevent it gets altered
                                $db_temp = ps_DB::_clone($db);
                                /** Start printing out HTML Form code (Payment Extra Info) * */
                                ?>
                                <br />
                                <table  style="display: none" width="100%">
                                    <tr>
                                        <td width="100%" align="center">
                                            <?php
                                            /* Try to get PayPal/PayMate/Worldpay/whatever Configuration File */
                                            @include( CLASSPATH . "payment/" . $db->f("payment_class") . ".cfg.php" );

                                            echo DEBUG ? vmCommonHTML::getInfoField('Beginning to parse the payment extra info code...') : '';

                                            // Here's the place where the Payment Extra Form Code is included
                                            // Thanks to Steve for this solution (why make it complicated...?)
                                            if (eval('?>' . $db->f("payment_extrainfo") . '<?php ') === false) {
                                                echo vmCommonHTML::getErrorField("Error: The code of the payment method " . $db->f('payment_method_name') . ' (' . $db->f('payment_method_code') . ') '
                                                        . 'contains a Parse Error!<br />Please correct that first');
                                            } else {
                                                echo DEBUG ? vmCommonHTML::getInfoField('Successfully parsed the payment extra info code.') : '';
                                            }
                                            /** END printing out HTML Form code (Payment Extra Info) * */
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <?php
                                $db = $db_temp;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            } /* End of security check */
            ?>

        </center>

        <?php
    }


    $query = "SELECT 
    `p`.`product_id`
    FROM (
        `jos_vm_product` AS `p`
    ) 
    WHERE 
        `p`.`product_publish`='Y'  and `p`.`product_thumb_image` != ''
    order by RAND() LIMIT 0, 4";

    $database->setQuery($query);
    $products_obj = $database->loadObjectList();
    $products = array();

    foreach ($products_obj as $product_obj) {
        $products[] = $product_obj->product_id;
    }
    $product_ordering_a = array(
        1 => array(
            'title' => 'sort by rating',
            'type' => 'desc'
        ),
        2 => array(
            'title' => 'sort by price',
            'type' => 'desc'
        ),
        3 => array(
            'title' => 'sort by price',
            'type' => 'asc'
        ),
    );
    $product_ordering = $_COOKIE['product_ordering'] ?? '';
    $sorting_class = 'glyphicon-sort';
    if ($product_ordering === 'desc') {
        $sorting_class = 'glyphicon-sort-by-attributes-alt';
    } elseif ($product_ordering === 'asc') {
        $sorting_class = 'glyphicon-sort-by-attributes';
    }
    $sortbypriceLabel = 'Sort by price';
    ?>
    <br><br>
    <div class="container bottom_category">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 title">
                <div class="flower">
                    <img alt="page not found" src="/templates/bloomex_adaptive/images/Flower.svg">
                </div>
                <h1>Australia’s #1 Flower Delivery - Trusted by Millions</h1>
                <p class="sort_by_select"><?php echo $sortbypriceLabel; ?><span
                            class="glyphicon <?php echo $sorting_class; ?> "></span></p>
            </div>
        </div>
    </div>
    <?php
    echo $ps_product->show_product_list($products);
    ?>

    <div class="modal fade" id="phoneNumberModal" tabindex="-1" role="dialog"aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body savePhoneNumberForm">
                    <h5 class="text-center"><?php echo $VM_LANG->_MOBILE_PHONE_NUMBER; ?></h5>
                    <input id="savePhoneNumberInput" type="text" class="form-control" placeholder="phone number">
                    <img id="savePhoneNumber" src="/templates/bloomex_adaptive/images/savePhoneNumber.png">
                    <p id="savePhoneNumberMsg"></p>
                </div>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(".close_popup").bind('click', function () {
            jQuery("#openModal").hide();
            jQuery("#popup_details").html('');
        });
        jQuery('#savePhoneNumber').click(function (e) {
            if ($('#savePhoneNumberInput').val() == '') {
                $('#savePhoneNumberMsg').text("<?php echo $VM_LANG->_PHPSHOP_PLEASE_SET_PHONE_NUMBER; ?>").addClass('text-danger')
                return false;
            }
            //trying to pre-validate nubmer on client side
            var Phone = $('#savePhoneNumberInput').val();
            Phone = Phone.replace(/\D/g, '');
            if (Phone.length === 10) {
                Phone = '1' + Phone;
            }
            if (Phone.length != 11) {
                $('#savePhoneNumberMsg').text("<?php echo $VM_LANG->_PHPSHOP_INVALID_AUSTRALIAN_CELL_PHONE; ?>").addClass('text-danger')
                return false;
            }

            jQuery.ajax({
                type: 'POST',
                url: '/index.php',
                async: false,
                data: ({
                    option: 'com_ajaxorder',
                    task: 'savePhoneNumberForUpdates',
                    phoneNumber: Phone,
                    user_id: '<?php echo $user->user_id; ?>',
                    order_id: '<?php echo $order_id; ?>'
                }),
                dataType: 'json',
                success: function (json) {
                    if (json.msg == 'success') {
                        $('#savePhoneNumberMsg').text("<?php echo $VM_LANG->_PHPSHOP_PHONE_NUMBER_UPDATES_SUCCESS; ?>").addClass('text-success')
                        $('.order_updates_text').text("<?php echo $VM_LANG->_PHPSHOP_PHONE_NUMBER_UPDATES_SUCCESS; ?>").addClass('text-success')
                        $('#phoneNumberModal').modal('hide')

                    } else {
                        $('#savePhoneNumberMsg').text("<?php echo $VM_LANG->_PHPSHOP_TRACK_ORDER_PHONE_NUMBER_SAVED; ?>").addClass('text-info')
                    }
                }
            })
        });
        var input = document.getElementById("savePhoneNumberInput");

        // Execute a function when the user releases a key on the keyboard
        input.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                // Cancel the default action, if needed
                event.preventDefault();
                // Trigger the button element with a click
                document.getElementById("savePhoneNumber").click();
            }
        });
    </script>
    <!-- Google Code for Bloomex AU Conversion Page -->
    <script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 968397865;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "5FslCJ_N1QkQqajizQM";
    var google_remarketing_only = false;
    /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
    <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/968397865/?label=5FslCJ_N1QkQqajizQM&amp;guid=ON&amp;script=0"/>
    </div>
    </noscript>

    <?php
//SERM #5927 Google Verified Reviews
    $google_review_query = "SELECT ui.`user_email`,o.warehouse,STR_TO_DATE(o.ddate,'%d-%m-%Y') as estimated_delivery_date FROM jos_vm_orders o
          LEFT JOIN jos_vm_order_user_info ui on ui.order_id= o.order_id and ui.address_type= 'BT'
          WHERE o.`order_id`='$order_id'";

    $gro = false;
    $database->setQuery($google_review_query);
    $database->loadObject($gro);
    if ($gro) {
        ?>
        <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
        <script>
            window.renderOptIn = function () {
                window.gapi.load('surveyoptin', function () {
                    window.gapi.surveyoptin.render({
                        // REQUIRED FIELDS
                        "merchant_id": 102019613,
                        "order_id": "<?php echo $order_id; ?>",
                        "email": "<?php echo $gro->user_email; ?>",
                        "delivery_country": "au",
                        "estimated_delivery_date": "<?php echo $gro->estimated_delivery_date; ?>",
                    });
                });
            }
        </script>
    <?php } ?>

    <?php
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
    }
}
else {
    ob_end_clean();
    header('Location: /account/');
    die;
}
?>
