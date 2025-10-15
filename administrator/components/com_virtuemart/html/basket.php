<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

mm_showMyFileName( __FILE__ );

define ('_MIN_POV_REACHED', '1');

require_once(CLASSPATH. 'ps_product.php' );
$ps_product = new ps_product;
require_once(CLASSPATH. 'ps_checkout.php' ); 
$ps_checkout = new ps_checkout;
require_once(CLASSPATH . 'ps_shipping_method.php' );

global $weight_total,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $total, $tax_total,$my, $cart, $order_tax_details, $discount_factor,$database, $iso_client_lang, $mm_action_url,$sef;
?>
    <script type="text/javascript">
        total_price = 0;
    </script>
<?php
if ((isset($_REQUEST['cart_id']) AND!empty($_REQUEST['cart_id']))) {
    $q = "Select * from jos_vm_carts where hash like '" . $database->getEscaped($_REQUEST['cart_id']) . "'";

    $saved_cart = false;
    $database->setQuery($q);
    $database->loadObject($saved_cart);
    require_once(CLASSPATH . 'ps_cart.php' );
    $ps_cart = new ps_cart;
    if ($saved_cart) {
        $products = json_decode($saved_cart->products);

        unset($_SESSION['cart']);

        $_SESSION['cart']['idx'] = 0;

        foreach ($products as $product) {

            if ((isset($product->product_id) AND isset($product->amount))) {
                $d = array();
                $d['product_id'] = (int) $product->product_id;
                $d['quantity'] = (int) $product->amount;
                $d['description'] = '';
                $d['extra_touches'] = '';


                $ps_cart->add($d);
            }
        }

        $cart = $_SESSION['cart'];
        $q = "select *,
            CASE 
                WHEN `expiry_date`>'" . date('Y-m-d') . "' THEN 0 
                WHEN `expiry_date`='0000-00-00' THEN 0 
            ELSE 1 END as `expired`
             from jos_vm_coupons where coupon_code = '" . $saved_cart->coupon . "' ";
        $coupon = false;
        $database->setQuery($q);
        $database->loadObject($coupon);
        $_SESSION['coupon_expired'] = false;
        if ($coupon) {
            if($coupon->expired){
                joomAlert::add_alert('Your 20% Coupon has been expired.', "danger");
                $_SESSION['coupon_expired'] = true;
            }else{
                joomAlert::add_alert('Your 20% discount will be applied on checkout', "success");
                $_SESSION['url_coupon_code'] = $coupon->coupon_code;
            }
        }
        // f($q, $coupon);
    }
}

if ((isset($_REQUEST['cart_products']) AND ! empty($_REQUEST['cart_products']))) {
    require_once(CLASSPATH . 'ps_cart.php' );
    $ps_cart = new ps_cart;

    $products = explode(';', $_REQUEST['cart_products']);

    unset($_SESSION['cart']);

    $_SESSION['cart']['idx'] = 0;

    foreach ($products as $product) {
        $product = explode(',', $product);

        if ((!empty($product[0]) AND ! empty($product[1]))) {
            $d = array();
            $d['product_id'] = (int) $product[0];
            $d['quantity'] = (int) $product[1];
            $d['description'] = '';
            $d['extra_touches'] = '';


            $ps_cart->add($d);
        }
    }

    $cart = $_SESSION['cart'];
}

$isShopCartPage = (isset($_GET['page']) AND $_GET['page'] == 'shop.cart');

if ($isShopCartPage) {
    if(isset($_SESSION['url_coupon_code']) AND !empty($_SESSION['url_coupon_code'])){
        echo '<h4 class=" text-success text-center">Your 20% discount will be applied on checkout</h4>';
    }elseif(isset($_SESSION['coupon_expired'])){
        echo '<h4 class=" text-danger text-center">Your 20% Coupon has been expired.</h4>';
    }
}


/* make sure this is the checkout screen */
if ($cart["idx"] == 0) {
    if(!isset($checkoutStep)){
        echo '<center>' . $VM_LANG->_PHPSHOP_EMPTY_CART . '</center>';
    }
        $checkout = False;
}
else {
    $product_rows 			= Array();
    $checkout				= true;

    // Added for the zone shipping module
    $vars["zone_qty"] 		= 0;
    $total 					= 0;	
    $weight_total 			= 0;
    $weight_subtotal 			= 0;
    $tax_total 				= 0;
    $shipping_total 			= 0;
    $shipping_tax 			= 0;
    $order_total 				= 0;
    $order_total_wtax 		= 0;
    $deliver_extra_price 		= 0;
    $shipping					= false;
    $discount_before			= false;
    $discount_after			= false;
    $total2 					= 0;	
    $total_undiscounted2		= 0;	
    $nProductCouponDiscount	= 0;
    $nTotalNotApplyDiscount	= 0;

    $cart = $_SESSION['cart'];

    //CALCULATE SHOPPER GROUP DISCOUNT	
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
    //print_r($ShopperGroupDiscount);
    //echo $query."<br/><br/>";
    $existProducts=[];
    for ($i=0;$i<$cart["idx"];$i++) {
        $existProducts[]=$cart[$i]['product_id'];
            if( !empty($nShopperGroupDiscount) && $cart[$i]["apply_group_discount"] <= 0 ) {
                     $cart[$i]["price"] 	= floatval( $cart[$i]["price"])  - ( floatval( $cart[$i]["price"]) * floatval($nShopperGroupDiscount) );
            }
    }
    $sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (".implode(',',$existProducts).") and promo='1'";
    $database->setQuery($sql);
    if($database->loadResult() && count($existProducts)==1){
        joomAlert::add_alert("Please add another product other than the gift to your cart!", 'danger');
        joomAlert::print_alerts();

    }
    //NEW CART

        ?>


        <table class="table cart">
            <thead>
            <tr>
                <th>
                    <?php echo $VM_LANG->_cart_product; ?>
                </th>
                <th class="d-none d-xl-table-cell">
                    <?php echo $VM_LANG->_cart_price; ?>
                </th>
                <th>
                    <?php echo $VM_LANG->_cart_quantity; ?>
                </th>
                <th>
                    <?php echo $VM_LANG->_cart_total; ?>
                </th>
                <th>
                    <?php echo $VM_LANG->_cart_remove; ?>
                </th>
            </tr>
            </thead>
            <tbody>
                <?php
                $cart_subtotal = 0;
                $items = [];
                $cartProducts = [];
                for ($i = 0; $i < $cart['idx']; $i++) {
                    if (isset($cart[$i]['product_id'])) {
                        $cartProducts[] = $cart[$i]['product_id'];
                    }
                }


                $query = "SELECT 
                                `p`.`product_id`,
                                `p`.`product_sku`,
                                `s`.`small_image_link_jpeg`,
                                `s`.`small_image_link_webp`,
                                `p`.`product_name`,
                                `p`.`alias`,
                                `po`.`promo`,
                                `pp`.`product_price`,
                                `pp`.`saving_price`,
                                `pp`.`discount_for_customer`,
                                 `pm`.`discount` as promotion_discount,
                                CASE 
                                    WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                                    ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                                END AS `product_real_price`,
                                `c`.`category_flypage`, 
                                `c`.`category_name`,
                                `c`.`category_id`,
                                `c`.`alias` AS 'category_alias',
                                `po`.`no_special`
                            FROM `jos_vm_product` AS `p`
                            INNER JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
                            LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                            LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
                            LEFT JOIN (SELECT 
                                            CASE 
                                                WHEN pmp.category_id > 0  THEN x.product_id
                                                ELSE pmp.product_id
                                            END AS `product_id`,pmp.discount,pmp.end_promotion
                                            FROM `jos_vm_products_promotion` as pmp 
                            left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
                            WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
                            LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
                            LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
                            LEFT JOIN `jos_vm_tax_rate` AS `tr` ON `tr`.`tax_rate_id`=`p`.`product_tax_id`
                            WHERE `p`.`product_id` in (".implode(',',$cartProducts).") group by `p`.`product_id` ";
                $database->setQuery($query);
                $cartProductsObj = $database->loadObjectList();

                $cartProductsObj = array_column($cartProductsObj, null, 'product_id');

                for ($i = 0; $i < $cart['idx']; $i++) {
                        if (isset($cart[$i])) {
                        $product_id = (int)$cart[$i]['product_id'];

                        if ($product_id > 0) {
                            $product_info = $cartProductsObj[$product_id];

                            $quantity = (int)$cart[$i]['quantity'];


                            if ((int)$product_info->no_special == 1) {
                                $no_special = true;
                            }

                            if ($cart[$i]['select_bouquet'] == 'petite') {
                                $product_info->product_name .= ' (petite)';
                            }
                            if ($cart[$i]['select_bouquet'] == 'deluxe') {
                                $product_info->product_name .= ' (deluxe)';
                            }
                            elseif ($cart[$i]['select_bouquet'] == 'supersize') {
                                $product_info->product_name .= ' (supersize)';
                            }
                            elseif (isset($cart[$i]['select_sub']) && $cart[$i]['select_sub'] == 'sub_3') {
                                $product_info->product_name .= ' (Subscription 3 months)';
                            }
                            elseif (isset($cart[$i]['select_sub']) && $cart[$i]['select_sub'] == 'sub_6') {
                                $product_info->product_name .= ' (Subscription 6 months)';
                            }
                            elseif (isset($cart[$i]['select_sub']) && $cart[$i]['select_sub'] == 'sub_12') {
                                $product_info->product_name .= ' (Subscription 12 months)';
                            }

                            $bouquet_add_price = 0;
                            if(isset($cart[$i]['select_bouquet']) && $cart[$i]['select_bouquet'] != 'standart') {
                                $sql = "SELECT " . $database->getEscaped($cart[$i]['select_bouquet']) . " FROM `jos_vm_product_options` WHERE product_id = $product_id LIMIT 1";
                                $database->setQuery($sql);
                                $bouquet_add_price = $database->loadResult();
                            }

                            $product_info->product_real_price = $bouquet_add_price + $product_info->product_real_price;

                            if (!empty($cart[$i]['select_sub'])) {
                                $sql = "SELECT ".$database->getEscaped($cart[$i]['select_sub'])." FROM `jos_vm_product_options` WHERE product_id = $product_id LIMIT 1";
                                $database->setQuery($sql);
                                $select_sub_price = $database->loadResult();

                                $product_info->product_real_price = $select_sub_price;
                            }

                            $product_real_price = number_format($product_info->product_real_price, 2, '.', '');
                            if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                                $product_real_price = round($product_real_price - $product_real_price*$product_info->discount_for_customer/100,2);
                            }
                            $product_subtotal = number_format($quantity*$product_real_price, 2, '.', '');
                            $link = $sef->getCanonicalProduct($product_info->alias, true);

                            ?>
                            <tr cart_product_id="<?php echo htmlspecialchars($product_info->product_id); ?>" i="<?php echo $i; ?>" class="cart_product_tr cart_product_id_<?php echo htmlspecialchars($product_info->product_id); ?>">
                                <td style="position: relative">
                                    <div class="image <?= (!!$isShopCartPage ? "shopping_cart_table_image" : "") ?>">
                                        <img alt="<?php echo htmlspecialchars($product_info->product_name); ?>" src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product_info->small_image_link_jpeg : $product_info->small_image_link_webp); ?>" />
                                    </div>
                                    <div class="info <?= (!!$isShopCartPage ? "shopping_cart_table_info" : "") ?>">
                                        <a href="<?php echo $link; ?>">
                                            <p><?php echo htmlspecialchars($product_info->product_name); ?></p>
                                        </a>
                                        <p class="d-none d-xl-block">SKU: <span><?php echo htmlspecialchars($product_info->product_sku); ?></span></p>
                                    </div>
                                </td>
                                <td data-th="price" class="d-none d-xl-table-cell">
                                    $<?php echo $product_real_price; ?>
                                </td>
                                <td data-th="quantity">
                                    <?php if($product_info->promo){; ?>
                                         <div class="text-center">1</div>
                                    <?php }else{; ?>
                                        <div class="quantity">
                                            <span class="minus">-</span>
                                            <input type="text" class="quantity_input" value="<?php echo $quantity; ?>">
                                            <span class="plus">+</span>
                                        </div>
                                    <?php }; ?>
                                </td>
                                <td class="cart_product_subtotal_<?php echo htmlspecialchars($product_info->product_id); ?>" data-th="total">
                                    $<?php echo $product_subtotal; ?>
                                </td>
                                <td data-th="remove">
                                    <div class="remove" product_id="<?php echo htmlspecialchars($product_info->product_id); ?>"></div>
                                </td>
                                <input type="hidden" name="price_<?= $product_info->product_id; ?>" value="<?= $product_real_price; ?>">
                                <input type="hidden" name="sku_<?= $product_info->product_id; ?>" value="<?= $product_info->product_sku; ?>">
                                <input type="hidden" name="name_<?= $product_info->product_id; ?>" value="<?= $product_info->product_name; ?>">
                                <input type="hidden" name="discount_<?= $product_info->product_id; ?>" value="<?= $product_info->saving_price; ?>">
                                <input type="hidden" name="category_<?= $product_info->product_id; ?>" value="<?= $product_info->category_name ?>">
                                <input type="hidden" name="quantity_<?= $product_info->product_id; ?>" value="<?= $quantity ?>">

                            </tr>

                            <?php

                            $items[] = [
                                'data-item_name' => $product_info->product_name,
                                'data-item_id' => $product_info->product_sku,
                                'data-price' =>  number_format(round($product_real_price, 2), 2, '.', ''),
                                'data-discount' =>  number_format(round($product_info->saving_price, 2), 2, '.', ''),
                                'data-item_category' => $product_info->category_name,
                                'data-item_variant' => 'standard',
                                'data-quantity' => $quantity
                            ];

                            $cart_subtotal += $product_subtotal;

                        }
                    }
                }
                ?>
                <tr class="subtotal">
                    <td data-th="subtotal" colspan="5">
                        <span class="title">Subtotal:</span> <span class="price cart_subtotal">$<?php echo number_format($cart_subtotal, 2, '.', ''); ?></span>
                    </td>
                </tr>
                <tr class="subtotal basket_coupon" style="display: none">
                    <td data-th="Coupon discount" colspan="5">
                        <span class="title">Coupon discount:</span> <span class="price basket_coupon_discount"></span>
                    </td>
                </tr>

                <?php
                if ($nShopperGroupDiscount > 0) {
                    $corporate_discount = number_format($nShopperGroupDiscount*$cart_subtotal, 2, '.', '');
                    ?>
                    <tr class="subtotal">
                        <td data-th="corporate discount" colspan="5"><span class="title">corporate discount:</span> <span class="price corporate_discount">$<?php echo $corporate_discount; ?></span></td>
                    </tr>
                    <tr class="subtotal">
                        <td data-th="total" colspan="5"><span class="title">total:</span> <span class="price cart_total">$<?php echo number_format($cart_subtotal-$corporate_discount, 2, '.', ''); ?></span></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php

        if ($my->id && PSHOP_COUPONS_ENABLE =='1' && !@$_SESSION['coupon_redeemed'] && 
            (
                $page == "shop.cart"
                || @$checkout_this_step == CHECK_OUT_GET_PAYMENT_METHOD
                || @$checkout_this_step == CHECK_OUT_GET_SHIPPING_ADDR && CHECKOUT_STYLE != 3 
                || @$checkout_this_step == CHECK_OUT_GET_SHIPPING_METHOD && CHECKOUT_STYLE == 3 
            )
        ) {  
            if ($nShopperGroupDiscount == 0) {
                include (PAGEPATH."coupon.coupon_field.php");   
            }
        }

    //!NEW CART
}

if (isset($checkoutStep) && $checkoutStep == '1'){ ?>
    <script type='text/javascript'>

        pushGoogleAnalytics(
            'begin_checkout',
            <?= json_encode($items); ?>,
            <?= number_format($cart_subtotal, 2, '.', ''); ?>
        );

        function addShippingInfo() {

            pushGoogleAnalytics(
                'add_shipping_info',
                <?= json_encode($items); ?>,
                <?= number_format($cart_subtotal, 2, '.', ''); ?>,
                'regular'
            );
        }
    </script>
<?php } else if (!isset($checkoutStep)) { ?>
    <script type='text/javascript'>
        pushGoogleAnalytics(
            'view_cart',
            <?= json_encode($items); ?>,
            <?= number_format($cart_subtotal, 2, '.', ''); ?>
        );

    </script>
<?php } ?>