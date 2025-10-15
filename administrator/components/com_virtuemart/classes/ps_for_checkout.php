<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class ps_for_checkout {

    public function setOrderItemIngredients($order_id, $order_item_id, $product_id, $quantity, $select_bouquet) {
        global $database;

        $return = (object) array('result' => false);

        Switch ($select_bouquet) {
            case 'petite':
                $quantity_field = 'igl_quantity_petite';
                break;
            case 'deluxe':
                $quantity_field = 'igl_quantity_deluxe';
                break;
            case 'supersize':
                $quantity_field = 'igl_quantity_supersize';
                break;
            default:
                $quantity_field = 'igl_quantity';
                break;
        }

        $query = "SELECT 
            `l`.`" . $quantity_field . "` AS `quantity`, 
            `o`.`igo_product_name` as `name`
        FROM `product_ingredients_lists` as `l`
        LEFT JOIN `product_ingredient_options` as `o` 
            ON `o`.`igo_id`=`l`.`igo_id`
        WHERE `l`.`product_id`=" . $product_id . "
        ";

        $database->setQuery($query);
        $order_item_ingredients_rows = $database->loadObjectList();

        $order_item_ingredients_array = array();

        foreach ($order_item_ingredients_rows as $row) {
            $order_item_ingredients_array[] = "(
                " . $order_id . ",
                " . $order_item_id . ",
                '" . $database->getEscaped($row->name) . "',
                '" . ($row->quantity * $quantity) . "'
            )";
        }

        if (sizeof($order_item_ingredients_array) > 0) {
            $query = "INSERT INTO `jos_vm_order_item_ingredient` 
            (
                `order_id`, 
                `order_item_id`, 
                `ingredient_name`, 
                `ingredient_quantity`
            ) 
            VALUES 
                " . implode(',', $order_item_ingredients_array) . "
            ";

            $database->setQuery($query);

            if ($database->query()) {
                $return->result = true;
            }
        }

        return $return;
    }


    public function MakeOrder() {
        global $database, $mosConfig_au_stripe_secret_key,$mosConfig_live_site,$mosConfig_absolute_path,$my;
        $return = array();
        $return['result'] = false;
        if($mosConfig_live_site == 'https://bloomex.com.au'){
            $data = array(
                'secret' => "6LcfVQYaAAAAAFq_1NskgYNo8E8RDj59fIDSWxye",
                'response' => isset($_POST['gcapcha']) ? $_POST['gcapcha'] : ''
            );

            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            if ($response) {
                $result = json_decode($response);
                if (!$result->success) {
                    $return['error'] = 'User verification error, please try again';
                    echo (json_encode($return));
                    die();
                }
            }
        }

        $existProducts = [];
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] AS $cart_product) {
                if (isset($cart_product['product_id'])) {
                    $existProducts[] = $cart_product['product_id'];
                }
            }
        }
        $sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (" . implode(',', $existProducts) . ") and promo='1'";
        $database->setQuery($sql);
        if ($database->loadResult() && count($existProducts) == 1) {
            $return['error'] = "Please add another product other than the gift to your cart!";
            echo json_encode($return);
            die();
        }


        $_SESSION['checkout_ajax'] = array_merge($_SESSION['checkout_ajax'],$_POST);


        $_POST['delivery_date'] = $_SESSION['checkout_ajax']['delivery_date'];
        $prices = (object) $this->GetTotal($_SESSION['checkout_ajax']['redeem_bucks'], $_SESSION['checkout_ajax']['donation_id'], $_SESSION['checkout_ajax']['redeem_credits']);

        if ($_SESSION['checkout_ajax']['payment_method_state'] == "stripe" && (($prices->total_price + $prices->donated_price) >= 0.5)) {


            $query = "UPDATE 
                `jos_vm_user_info` SET
                    `first_name`='" . $database->getEscaped($_POST['billing_info_first_name']) . "',
                    `last_name`='" . $database->getEscaped($_POST['billing_info_last_name']) . "',
                    `country`='" . $database->getEscaped($_POST['billing_info_country']) . "',
                    `phone_1`='" . $database->getEscaped($_POST['billing_info_phone_1']) . "',
                    `user_email`='" . $database->getEscaped($_POST['billing_info_user_email']) . "',
                    `mdate`='" . time() . "'
                WHERE 
                    `user_id`=" . (int)$my->id . "
                AND 
                    `address_type`='BT'
                ";
            $database->setQuery($query);
            $database->query();

            $query = "SELECT 
                    * 
                FROM `jos_vm_user_info` 
                WHERE 
                    `user_id`=" . (int) $my->id . "
                AND 
                    `address_type`='BT'
                ";
            $database->setQuery($query);
            $bt_obj = false;
            $database->loadObject($bt_obj);

            date_default_timezone_set('Australia/Sydney');
            $mysqlDatetime = date('Y-m-d G:i:s', time());
            $query_search = "INSERT INTO tbl_stripe_orders_logs ( user_id,user_name,user_email,order_total,order_status,date_added) VALUES ( 
                                '".$database->getEscaped((int) $my->id)."',
                                '".$database->getEscaped($bt_obj->first_name.' '.$bt_obj->last_name)."',
                                '".$database->getEscaped($bt_obj->user_email)."',
                                '".$database->getEscaped($prices->total_price + $prices->donated_price)."',
                                'pending_stripe',
                                '" . $mysqlDatetime . "'
                                )";
            $database->setQuery($query_search);
            $database->query();
            $stripeOrderLogId = $database->insertid()??'';


            $aud = array('AUS', 'CAN', 'NZL', '');
            $currency = in_array($bt_obj->country, $aud) ? 'AUD' : 'USD';

            require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
            $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);

            $success_url = "$mosConfig_live_site/checkout/2/?session_id={CHECKOUT_SESSION_ID}&mosmsgsuccess=true&stripe_order_log_id=$stripeOrderLogId&mosmsg=Payment executed by Stripe Successfully";
            $cancel_url = "$mosConfig_live_site/checkout/1/?mosmsg=Payment Failed";
            $orderItems = [];


            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] AS $cart_product) {
                    if (isset($cart_product['product_id'])) {

                        $product_final_price = number_format($cart_product['price'] * $_SESSION['checkout_ajax']['products_tax_rate'] + $cart_product['price'], 2, '.', '');

                        $query = "SELECT 
                            *
                        FROM `jos_vm_product`
                        WHERE `product_id`=" . (int) $cart_product['product_id'] . "
                        ";
                        $database->setQuery($query);
                        $product_obj = false;
                        $database->loadObject($product_obj);

                        if (
                            $cart_product['select_bouquet'] == 'petite' OR
                            $cart_product['select_bouquet'] == 'deluxe' OR
                            $cart_product['select_bouquet'] == 'supersize'
                        ) {
                            $product_obj->product_name .= ' (' . htmlspecialchars($cart_product['select_bouquet']) . ')';
                        }

                        $orderItems[] =  [
                            'price_data' => [
                                'currency' => $currency,
                                'unit_amount' => $product_final_price*100,
                                'product_data' => [
                                    'name' => htmlentities($product_obj->product_name, ENT_QUOTES),
                                    'images' => [
                                        $mosConfig_live_site.'/components/com_virtuemart/shop_image/product/'.$product_obj->product_thumb_image
                                    ]
                                ],
                            ],
                            'quantity' => (int) $cart_product['quantity']
                        ];
                    }
                }
            }

            $orderItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $prices->shipping_price * 100,
                    'product_data' => [
                        'name' => 'Delivery'
                    ],
                ],
                'quantity' => '1'
            ];

            if ($prices->donated_price) {
                $orderItems[] = [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $prices->donated_price * 100,
                        'product_data' => [
                            'name' => 'Donation'
                        ],
                    ],
                    'quantity' => '1'
                ];
            }
            $customer = $stripe->customers->create([
                'email' => $bt_obj->user_email,
                'name' => $bt_obj->first_name.' '.$bt_obj->last_name,
            ]);


            $stripeSessionParams = [
                'payment_intent_data'=>['description' => 'Payer '.$bt_obj->first_name.' '.$bt_obj->last_name],
                'success_url' => $success_url,
                'customer' => $customer->id,
                'custom_text' => [
                    'submit' => ['message' => '⚠️ After completing your payment, you will be automatically redirected to enter your shipping address and choose your delivery date.'],
                ],
                'cancel_url' => $cancel_url,
                'line_items' => $orderItems,
                'mode' => 'payment',
            ];

            $orderDiscountsStripe = number_format(abs($prices->total_price - $prices->products_price - $prices->shipping_price), 2, ".", "") * 100;
            $orderDiscounts = [
                'amount_off' => $orderDiscountsStripe,
                'name' => 'Discount Price',
                'duration' => 'once',
                'currency' => $currency,
            ];

            if ($orderDiscountsStripe > 0) {
                $stripeCoupon = $stripe->coupons->create($orderDiscounts);
                $stripeSessionParams['discounts'][] = ['coupon' => $stripeCoupon->id];
            }


            $stripeSession = $stripe->checkout->sessions->create($stripeSessionParams);
            $return['stripePaymentUrl'] = $stripeSession->url;

            $_SESSION['checkout_ajax']['stripeSessionId'] = $stripeSession->id;

            $query = "UPDATE `tbl_stripe_orders_logs`  SET `order_data`='".$database->getEscaped(serialize($_SESSION))."'
                                        WHERE `id`='" . (int)$stripeOrderLogId . "' ";
            $database->setQuery($query);
            $database->query();

            $return['result'] = true;
            echo json_encode($return);
            exit;

        } else {
            $this->SetOrder();
        }

    }
    public function SetOrder($stripeResponse = [],$ajax=true,$stripeOrderLogId='',$newSessionData=[]) {
        global $database, $my,$mosConfig_fraud_notification_list,$mainframe, $mosConfig_offset,$mosConfig_set_order_status_pending_anyway, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $mosConfig_test_card_numbers, $mosConfig_payment_centralization,$mosConfig_au_stripe_secret_key;

        if($newSessionData) {
            $my = new stdClass();
            $_SESSION = $newSessionData;
            $my->id = $_SESSION['checkout_ajax']['user_id'];
            $my->username = $_SESSION['checkout_ajax']['user_name'];
        }


        date_default_timezone_set('Australia/Sydney');

        $return = array();
        $return['result'] = false;

        if (!isset($_SESSION['checkout_ajax']) || empty($_SESSION['checkout_ajax'])) {
            $return['error'] = 'Session data is empty, please try again';
            if(!$ajax) {
                mosRedirect('/checkout/1/?msg='.$return['error']);
            }
            echo (json_encode($return));
            die();
        }


        $return['try_again'] = true;

        $timestamp = time();
        $mysqlDatetime = date('Y-m-d G:i:s', $timestamp);
        $PaymentVar = array();
        $file = '';
        $vendor_currency = '';

        $confirm_obj = new stdClass();
        $confirm_obj->user_id = (int) $my->id;

        if ($confirm_obj->user_id > 0) {
            //have to check if we have actual cart first - had issues
            $cart_products = array();
            $existProducts = [];
            $checkExistPromotionDiscountProduct = false;
            $checkExistSpecialDiscountProduct = false;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] AS $cart_product) {
                    if (isset($cart_product['product_id'])) {
                        if($cart_product['promotion_discount']) {
                            $checkExistPromotionDiscountProduct = true;
                        }
                        if($cart_product['hasSpecialDiscount']) {
                            $checkExistSpecialDiscountProduct = true;
                        }
                        $existProducts[] = $cart_product['product_id'];
                        $cart_products[] = array(
                            'product_id' => (int) $cart_product['product_id'],
                            'quantity' => (int) $cart_product['quantity'],
                            'price' => number_format($cart_product['price'], 2, '.', ''),
                            'select_bouquet' => $cart_product['select_bouquet'],
                            'select_sub' => $cart_product['select_sub']
                        );
                    }
                }
            }
            $sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (" . implode(',', $existProducts) . ") and promo='1'";
            $database->setQuery($sql);
            if ($database->loadResult() && count($existProducts) == 1) {
                $return['error'] = "Please add another product other than the gift to your cart!";
                if(!$ajax) {
                    mosRedirect('/checkout/1/?msg='.$return['error']);
                }
                echo json_encode($return);
                exit;
            }

            /* check if virtual product only in cart */
            $sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (" . implode(',', $existProducts) . ") and no_delivery='1'";
            $database->setQuery($sql);
            if ($database->loadResult() && count($existProducts) == 1) {
                $_SESSION['checkout_ajax']['virtual'] = true;
            }

            if (sizeof($cart_products) > 0) {
                $confirm_obj->user_name = $my->username;
                $confirm_obj->user_info_id = $_SESSION['checkout_ajax']['user_info_id'];
                $confirm_obj->user_email = $_SESSION['checkout_ajax']['account_email'];
                $confirm_obj->occasion = $_SESSION['checkout_ajax']['customer_occasion']??'';
                $confirm_obj->shipping_method = (int) $_SESSION['checkout_ajax']['shipping_method'];
                $confirm_obj->card_msg = $_SESSION['checkout_ajax']['card_msg']??'';
                $confirm_obj->signature = $_SESSION['checkout_ajax']['signature']??'';
                $confirm_obj->card_comment = $_SESSION['checkout_ajax']['card_comment']??'';
                $confirm_obj->delivery_date = $_SESSION['checkout_ajax']['delivery_date'];
                $confirm_obj->vendor_currency = $_SESSION['checkout_ajax']['vendor_currency_string'];
                $confirm_obj->name_on_card = $_SESSION['checkout_ajax']['name_on_card']??'';
                $confirm_obj->card_number = $_SESSION['checkout_ajax']['card_number']??'';
                $confirm_obj->card_cvv = $_SESSION['checkout_ajax']['card_cvv']??'';
                $confirm_obj->expire_month =$_SESSION['checkout_ajax']['expire_month']??'';
                $confirm_obj->expire_year = $_SESSION['checkout_ajax']['expire_year']??'';
                $confirm_obj->find_us = $_SESSION['checkout_ajax']['find_us']??'';
                $confirm_obj->redeem_bucks = $_SESSION['checkout_ajax']['redeem_bucks']??'';
                $confirm_obj->donation_id = $_SESSION['checkout_ajax']['donation_id']??'';
                $confirm_obj->redeem_credits = $_SESSION['checkout_ajax']['redeem_credits']??'';
                $confirm_obj->payment_method_state = $_SESSION['checkout_ajax']['payment_method_state']?? 'card';
                $confirm_obj->ip_address = $_SERVER['REMOTE_ADDR']??'';
                $confirm_obj->products_tax_rate = $_SESSION['checkout_ajax']['products_tax_rate'];
                $confirm_obj->free_shipping = $_SESSION['checkout_ajax']['free_shipping'];


                $_POST['delivery_date'] = $_SESSION['checkout_ajax']['delivery_date'];
                $confirm_obj->prices = (object) $this->GetTotal($confirm_obj->redeem_bucks, $confirm_obj->donation_id, $confirm_obj->redeem_credits);

                $confirm_obj->coupon_code = $confirm_obj->coupon_value = $confirm_obj->coupon_type = $confirm_obj->percent_or_total = '';

                if (isset($_SESSION['checkout_ajax']['coupon_code'])) {
                    $query = "SELECT `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($_SESSION['checkout_ajax']['coupon_code']) . "'";
                    $coupon_obj = false;
                    $database->setQuery($query);
                    $database->loadObject($coupon_obj);

                    if ($coupon_obj) {
                        $confirm_obj->coupon_code = $coupon_obj->coupon_code;
                        $confirm_obj->coupon_value = number_format($coupon_obj->coupon_value, 2);
                        $confirm_obj->percent_or_total = $coupon_obj->percent_or_total;
                        $confirm_obj->coupon_type = $coupon_obj->coupon_type;
                    }
                }

                $query = "SELECT 
                    * 
                FROM `jos_vm_user_info` 
                WHERE 
                    `user_id`=" . $confirm_obj->user_id . "
                AND 
                    `address_type`='BT'
                ";
                $database->setQuery($query);
                $bt_obj = false;
                $database->loadObject($bt_obj);

                $aud = array('AUS', 'CAN', 'NZL', '');
                $currency = in_array($bt_obj->country, $aud) ? 'AUD' : 'USD';


                $query = "SELECT 
                    * 
                FROM `jos_vm_user_info` 
                WHERE 
                    `user_id`=" . $confirm_obj->user_id . "
                AND
                    `user_info_id`='" . $confirm_obj->user_info_id . "'
                AND 
                    `address_type`='ST'
                ";
                $database->setQuery($query);
                $st_obj = false;
                $database->loadObject($st_obj);

                //$order_number = md5('order'.$confirm_obj->user_id.time());
                $order_number = 'blau_' . date('YmdHi') . '_' . mt_rand(10000000, 99999999);

                $PaymentVar['user_id'] = $confirm_obj->user_id;
                $PaymentVar['bill_company_name'] = $bt_obj->company;
                $PaymentVar['bill_last_name'] = $bt_obj->last_name;
                $PaymentVar['bill_first_name'] = $bt_obj->first_name;
                $PaymentVar['bill_middle_name'] = $bt_obj->middle_name;
                $PaymentVar['bill_phone'] = $bt_obj->phone_1;
                $PaymentVar['bill_fax'] = $bt_obj->fax;
                $PaymentVar['bill_address_1'] = $bt_obj->street_number . ' ' . $bt_obj->street_name;
                $PaymentVar['bill_address_2'] = '';
                $PaymentVar['bill_city'] = $bt_obj->city;
                $PaymentVar['bill_state'] = $bt_obj->state;
                $PaymentVar['bill_country'] = $bt_obj->country;
                $PaymentVar['bill_zip_code'] = $bt_obj->zip;
                $PaymentVar['bill_email'] = $bt_obj->user_email;
                $PaymentVar['expire_month'] = $confirm_obj->expire_month;
                $PaymentVar['expire_year'] = $confirm_obj->expire_year;
                $PaymentVar['order_payment_number'] = $confirm_obj->card_number;
                $PaymentVar['credit_card_code'] = $confirm_obj->card_cvv;
                $PaymentVar['bill_suite'] = $bt_obj->suite;
                $PaymentVar['bill_street_number'] = $bt_obj->street_number;
                $PaymentVar['bill_street_name'] = $bt_obj->street_name;
                $PaymentVar['name_on_card'] = $confirm_obj->name_on_card;

                $aResult = array();
                $stripePaymentProcess = false;
                if (($confirm_obj->prices->total_price + $confirm_obj->prices->donated_price) == 0) {
                    $aResult['approved'] = 1;
                } elseif  ($confirm_obj->payment_method_state == "stripe") {
                    $stripePaymentProcess = true;
                } elseif ($mosConfig_payment_centralization == true) {
                    $PaymentVarCentralization = array(
                        'project' => 'bloomex.com.au',
                        'order_number' => $order_number,
                        'amount' => $confirm_obj->prices->total_price + $confirm_obj->prices->donated_price,
                        'cardholder_name' => $confirm_obj->name_on_card,
                        'card_number' => $confirm_obj->card_number,
                        'exp_month' => sprintf('%02d', $confirm_obj->expire_month),
                        'exp_year' => substr($confirm_obj->expire_year, -2),
                        'cvv' => $confirm_obj->card_cvv,
                        'currency' => $currency,
                        'first_name' => $bt_obj->first_name,
                        'last_name' => $bt_obj->last_name,
                        'billing_address_line_1' => (!empty($bt_obj->suite) ? $bt_obj->suite . '#, ' : '') . $bt_obj->street_number . ' ' . $bt_obj->street_name,
                        'billing_address_line_2' => '',
                        'billing_city' => $bt_obj->city,
                        'billing_state' => $bt_obj->state,
                        'billing_country' => $bt_obj->country,
                        'billing_zip' => $bt_obj->zip,
                        'billing_phone' => $bt_obj->phone_1,
                        'billing_email' => $bt_obj->user_email,
                        'billing_ip' => $_SERVER['REMOTE_ADDR']
                    );

                    $aResult = $this->process_payment_centralization($PaymentVarCentralization);
                } else {
                    //$this->process_payment_beanstream($order_number, $confirm_obj->prices->total_price+$confirm_obj->prices->donated_price, $PaymentVar, $aResult);
                }
                $sProductId = '';
                foreach ($_SESSION['cart'] as $key => $product) {
                    if ($product['product_id']) {
                        $aProductId[] = (int) $product['product_id'];
                        $sProductId .= (int) $product['product_id'] . ",";
                        $aQuantity[$product['product_id']] = $product['quantity'];
                        $aPrice[$product['product_id']] = $product['price'];
                        $aBouquet[$product['product_id']] = $product['select_bouquet'];
                    }
                }
                $sProductId = substr($sProductId, 0, strlen($sProductId) - 1);
                $cart_product_ids = array();

                foreach ($_SESSION['cart'] AS $cart_product) {
                    if (isset($cart_product['product_id'])) {
                        $cart_product_ids[] = (int) $product['product_id'];
                    }
                }


                $order_tax_details = array();

                foreach ($_SESSION['cart'] AS $product_key => $cart_product) {
                    if (isset($cart_product['product_id'])) {

                        if (!isset($order_tax_details['' . $confirm_obj->products_tax_rate . ''])) {
                            $order_tax_details['' . $confirm_obj->products_tax_rate . ''] = doubleval($_SESSION['cart'][$product_key]['price']);
                        } else {
                            $order_tax_details['' . $confirm_obj->products_tax_rate . ''] += doubleval($_SESSION['cart'][$product_key]['price']);
                        }
                    }
                }

                if (
                    (isset($aResult['approved']) && $aResult['approved'] == 1)
                    || $stripePaymentProcess
                    || $mosConfig_set_order_status_pending_anyway
                ) {
                    if ($_SESSION['checkout_ajax']['checkoutStepOrder']) {

                        /* if only virtual product in cart, status delivered */
                        if (isset($_SESSION['checkout_ajax']['virtual'])) {
                            $order_status = "D";
                            $return['result'] = true;
                            $return['order_id'] = $order_id??'';
                            $_SESSION['checkout_ajax']['thankyou'] = md5('thankyou' . $order_id);
                            $_SESSION['checkout_ajax']['thankyou_order_id'] = $order_id;
                        }  else {
                            $order_status = "PD";
                        }

                    } else {
                        $order_status = "A";
                    }
                    $payment_msg = ' Payment has been approved';
                    if (in_array($PaymentVar["order_payment_number"], $mosConfig_test_card_numbers)) {
                        $order_status = "X";
                    }

                    $query = "SELECT 
                        VSC.shipping_carrier_name, 
                        REPLACE(VSR.shipping_rate_name,'{fee}','".$confirm_obj->prices->shipping_price."') as shipping_rate_name, 
                        '".$confirm_obj->prices->shipping_price."', 
                        VSR.shipping_rate_id
                    FROM #__vm_shipping_rate AS VSR
                    INNER JOIN #__vm_shipping_carrier AS VSC
                    ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id
                    WHERE VSR.shipping_rate_id =" . $confirm_obj->shipping_method . "";
                    $database->setQuery($query);
                    $aShippingMethod = $database->loadRow();

                    if ($confirm_obj->free_shipping == 1) {
                        $sFreeShipping = "Free";
                    } else {
                        $sFreeShipping = "Paid";
                    }

                    if (is_array($aShippingMethod) && count($aShippingMethod)) {
                        $sShippingMethod = "standard_shipping|" . implode("|", $aShippingMethod) . "|$sFreeShipping";
                    } else {
                        $sShippingMethod = "standard_shipping|$sFreeShipping";
                    }

                    $wh_obj = false;
                    $zip_symbols = 4;

                    $warehouse = getWarehouseFromStateRelation($st_obj->state);

                    while (($wh_obj == false) AND ($zip_symbols > 0)) {
                        $query = "SELECT 
                            `wh`.`warehouse_email`,
                            `wh`.`warehouse_code`,
                            `pwh`.`out_of_town`
                        FROM `jos_postcode_warehouse` AS `pwh` 
                        LEFT JOIN `jos_vm_warehouse` AS `wh` ON `wh`.`warehouse_id`=`pwh`.`warehouse_id` 
                        WHERE `pwh`.country = 'AUS' and 
                            `pwh`.`postal_code` LIKE '" . substr($st_obj->zip, 0, $zip_symbols) . "'
                        ";

                        $database->setQuery($query);
                        $wh_obj = false;
                        $database->loadObject($wh_obj);
                        if ($wh_obj) {
                            $oot = $wh_obj->out_of_town;
                            break;
                        }
                        $zip_symbols--;
                    }

                    $blendedDate = $_SESSION['blendedDate'] ?? null;
                    $isLastMinuteOrder = false;
                    if ($blendedDate !== null) {
                        $isLastMinuteOrder = (date('d-m-Y', strtotime($confirm_obj->delivery_date)) == $blendedDate)?1:0;
                    }
                    unset($_SESSION['isLastMinuteOrder']);
                    if ($isLastMinuteOrder) {
                        $_SESSION['isLastMinuteOrder'] = true;
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
                        `username`,
                        `warehouse`
                    ) 
                    VALUES ( 	
                        " . $confirm_obj->user_id . ", 
                        '1', 
                        '" . $order_number . "', 
                        '" . $database->getEscaped($confirm_obj->user_info_id) . "', 
                        '" . $confirm_obj->prices->total_price . "', 
                        '" . $confirm_obj->prices->products_price . "', 
                        '" . $confirm_obj->prices->taxes_price . "', 
                        '" . serialize($order_tax_details) . "', 
                        '" . $confirm_obj->prices->shipping_price . "',  
                        '" . $confirm_obj->prices->coupon_discount . "',  
                        '" . $currency . "', 
                        '" . $order_status . "', 
                        '" . $timestamp . "', 
                        '" . $timestamp . "', 
                        '" . date('d-m-Y', strtotime($confirm_obj->delivery_date)) . "',
                        '" . $database->getEscaped($sShippingMethod) . "',
                        '" . $database->getEscaped($confirm_obj->card_msg) . "', 
                        '" . $database->getEscaped($confirm_obj->signature) . "', 
                        '" . $database->getEscaped($confirm_obj->occasion) . "', 
                        '" . $database->getEscaped($confirm_obj->card_comment) . "', 
                        '" . (int) $confirm_obj->find_us . "', 
                        '" . $database->getEscaped($confirm_obj->ip_address) . "', 	
                        '" . $database->getEscaped($confirm_obj->coupon_code) . "', 	 
                        '" . $database->getEscaped($confirm_obj->percent_or_total) . "', 	
                        '" . $database->getEscaped($confirm_obj->coupon_value) . "', 	
                        '" . $database->getEscaped($confirm_obj->user_name) . "' ,
                        '" . $database->getEscaped($warehouse) . "' 
                    )";
                    $database->setQuery($query);
                    $database->query();
                    $order_id = $database->insertid();
                    if ($database->_errorNum > 0) {
                        $text_error = 'Error: ' . $database->_errorMsg;
                        $text_error .= '<br><br>Query: ' . $query;
                        moslogerrors('front insert order', $text_error);
                    }

                    $query = "UPDATE `jos_users`
                        SET
                            `Marketing_Opt_in`='" . (int) $confirm_obj->find_us . "'
                         WHERE id=" . $confirm_obj->user_id;
                    $database->setQuery($query);
                    $database->query();

                    //===================== DELETE GIF COUNPON AFTER USED =================================
                    if ($confirm_obj->coupon_code && $confirm_obj->coupon_type == "gift") {
                        $sql = "DELETE FROM #__vm_coupons WHERE coupon_code = '$confirm_obj->coupon_code' AND coupon_type = 'gift'";
                        $database->setQuery($sql);
                        $database->query();
                    }


                    if($_SESSION['checkout_ajax']['free_shipping_by_price'] && $confirm_obj->prices->shipping_price = '0.00'){
                        $query = "INSERT INTO `tbl_free_shipping_by_price_orders`
                        (	
                            `order_id`,
                            `user_id`,
                            `date_added`
                        ) 
                        VALUES (
                            " . $order_id . ",
                            " . $confirm_obj->user_id . ",
                           '" . $mysqlDatetime . "'
                        )";
                        $database->setQuery($query);
                        $database->query();
                    }

                    $aResult["order_payment_trans_id"] = ($aResult["order_payment_trans_id"]) ?? ($stripeResponse[0]??'');
                    $aResult["order_payment_log"] = ($aResult["order_payment_log"]) ?? ($stripeResponse[1]??'');
                    $query = "INSERT INTO `jos_vm_order_payment`
                        (	
                            `order_id`, 
                            `order_payment_code`, 
                            `payment_method_id`, 
                            `order_payment_number`, 
                            `order_payment_expire`, 
                            `order_payment_log`, 
                            `order_payment_name`, 
                            `order_payment_trans_id`
                        ) 
                        VALUES (
                            " . $order_id . ", 
                            '', 
                            3, 
                           'NOT SAVED', 
                            '',
                            '{$aResult["order_payment_log"]}[--1--]',
                            '',			
                            '{$aResult["order_payment_trans_id"]}'
                        )";

                    if(isset($stripeResponse[0]) && $stripeResponse[0]!='') {
                        require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
                        $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);
                        $stripe->paymentIntents->update(
                            $stripeResponse[0],
                            [
                                'metadata' => ['order_id' => $order_id,'order place method' => 'front end'],
                                'description' => 'Order# '.$order_id
                            ]
                        );
                    }

                    $database->setQuery($query);
                    $database->query();

                    $ua=getBrowser();
                    $deviceDetails = ', Device: '.(isMobileDevice()?'Mobile':'Web') .
                        ', Browser: ' . $ua['name'] . ' ' . $ua['version'] . ' on ' .$ua['platform'] . ', UserAgent: ' . $ua['userAgent'];

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
                        " . $order_id . ", 
                        '" . $order_status . "', 
                        '" . $mysqlDatetime . "', 
                        1, 
                        'Front End " . $deviceDetails
                        . (($isLastMinuteOrder) ? " | Last Minute Order - Holiday delivery delay accepted" : '')
                        . (($checkExistPromotionDiscountProduct) ? " | There is a promotion product in order" : '')
                        . (($checkExistSpecialDiscountProduct) ? " | There is a special discounted product in order" : '') .
                        "',
                        '" . $database->getEscaped($confirm_obj->user_name) . "',
                        '" . $database->getEscaped($warehouse) . "'
                    )";
                    $database->setQuery($query);
                    $database->query();


                    if((isset($aResult['approved']) && $aResult['approved'] != 1)) {
                        $query = "INSERT INTO `jos_vm_cards_for_pending_orders`
                            (
                                `order_id`,
                                `card_number`,
                                `name_on_card`,
                                `month`,
                                `year`,
                                `cvv`,
                                `datetime`
                            )
                            VALUES (
                                " . (int)$order_id . ",
                                '" . $database->getEscaped($confirm_obj->card_number) . "',
                                '" . $database->getEscaped($confirm_obj->name_on_card) . "',
                                '" . $database->getEscaped(sprintf('%02d', $confirm_obj->expire_month)) . "',
                                '" . $database->getEscaped(substr($confirm_obj->expire_year, -2)) . "',
                                '" . $confirm_obj->card_cvv . "',
                                '" . $mysqlDatetime . "'
                            )";

                        $database->setQuery($query);
                        $database->query();
                    }


                    $addresses = array($bt_obj, $st_obj);

                    foreach ($addresses as $address_obj) {
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
                            `middle_name`, 
                            `phone_1`, 
                            `fax`, 
                            `address_1`, 
                            `address_2`, 
                            `city`, 
                            `state`, 
                            `country`, 
                            `zip`,
                            `user_email`, 
                            `suite`, 
                            `street_number`,
                            `street_name` 
                        ) 
                        VALUES(  
                            " . $order_id . ", 
                            " . $confirm_obj->user_id . ", 
                            '" . $database->getEscaped($address_obj->address_type) . "', 
                            '" . $database->getEscaped($address_obj->address_type2) . "', 
                            '-default-', 
                            '" . $database->getEscaped($address_obj->company) . "',
                            '" . $database->getEscaped($address_obj->last_name) . "',
                            '" . $database->getEscaped($address_obj->first_name) . "',
                            '" . $database->getEscaped($address_obj->middle_name) . "',
                            '" . $database->getEscaped($address_obj->phone_1) . "',
                            '" . $database->getEscaped($address_obj->fax) . "',
                            '" . $database->getEscaped($address_obj->address_1) . "',
                            '" . $database->getEscaped($address_obj->address_2) . "',
                            '" . $database->getEscaped($address_obj->city) . "',
                            '" . $database->getEscaped($address_obj->state) . "',
                            '" . $database->getEscaped($address_obj->country) . "',
                            '" . $database->getEscaped($address_obj->zip) . "',
                            '" . $database->getEscaped($address_obj->user_email) . "',
                            '" . $database->getEscaped($address_obj->suite) . "',
                            '" . $database->getEscaped($address_obj->street_number) . "',
                            '" . $database->getEscaped($address_obj->street_name) . "'
                        )";
                        $database->setQuery($query);
                        $database->query();
                    }

                    $sub_orders = array();
                    $cart_products_objects = [];
                    $product_i = 1;
                    foreach ($cart_products as $product_key => $cart_product) {

                        $query = "SELECT 
                            *
                        FROM `jos_vm_product`
                        WHERE `product_id`=" . (int) $cart_products[$product_key]['product_id'] . "
                        ";
                        $database->setQuery($query);
                        $product_obj = false;
                        $database->loadObject($product_obj);

                        if (
                            $cart_products[$product_key]['select_bouquet'] == 'petite' OR
                            $cart_products[$product_key]['select_bouquet'] == 'deluxe' OR
                            $cart_products[$product_key]['select_bouquet'] == 'supersize'
                        ) {
                            $product_obj->product_name .= ' (' . htmlspecialchars($cart_products[$product_key]['select_bouquet']) . ')';
                        }
                        $cart_products_objects[$product_obj->product_id] = $product_obj;
                        if (!empty($cart_products[$product_key]['select_sub'])) {
                            $select_sub = '';

                            if ($cart_products[$product_key]['select_sub'] == 'sub_3') {
                                $select_sub = 'Subscription 3 months';

                                $sub_months = 3;
                            } elseif ($cart_products[$product_key]['select_sub'] == 'sub_6') {
                                $select_sub = 'Subscription 6 months';

                                $sub_months = 6;
                            } elseif ($cart_products[$product_key]['select_sub'] == 'sub_12') {
                                $select_sub = 'Subscription 12 months';

                                $sub_months = 12;
                            }

                            $product_obj->product_name .= ' (' . htmlspecialchars($select_sub) . ')';

                            if ($sub_months) {
                                if (sizeof($sub_orders) == 0) {
                                    $sub_orders[0] = $order_id;
                                    $this->SetSubOrderXref($order_id, $order_id);
                                }

                                for ($i_sub = 1; $i_sub < $sub_months; $i_sub++) {
                                    if (array_key_exists($i_sub, $sub_orders)) {
                                        $sub_order_id = $sub_orders[$i_sub];

                                        $sub_order_item_data = array();
                                        $sub_order_item_data['user_info_id'] = $confirm_obj->user_info_id;
                                        $sub_order_item_data['vendor_id'] = 1;
                                        $sub_order_item_data['product_id'] = $product_obj->product_id;
                                        $sub_order_item_data['product_sku'] = $product_obj->product_sku;
                                        $sub_order_item_data['product_name'] = $product_obj->product_name;
                                        $sub_order_item_data['nQuantityTemp'] = $cart_products[$product_key]['quantity'];
                                        $sub_order_item_data['product_currency'] = $product_obj->product_currency;
                                        $sub_order_item_data['order_status'] = $order_status;
                                        $sub_order_item_data['product_desc'] = $product_obj->product_desc;
                                        $sub_order_item_data['timestamp'] = $timestamp;

                                        $this->SetSubOrderItem($sub_order_id, $sub_order_item_data);
                                    } else {
                                        $sub_order_data = array();
                                        $sub_order_data['user_id'] = $confirm_obj->user_id;
                                        $sub_order_data['vendor_id'] = 1;
                                        $sub_order_data['user_info_id'] = $confirm_obj->user_info_id;
                                        $sub_order_data['vendor_currency'] = $vendor_currency;
                                        $sub_order_data['order_status'] = $order_status;
                                        $sub_order_data['timestamp'] = $timestamp;
                                        $sub_order_data['sShippingMethod'] = $sShippingMethod;
                                        $sub_order_data['card_msg'] = $confirm_obj->card_msg;
                                        $sub_order_data['signature'] = $confirm_obj->signature;
                                        $sub_order_data['card_comment'] = $confirm_obj->card_comment;
                                        $sub_order_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
                                        $sub_order_data['user_name'] = $confirm_obj->user_name;
                                        $sub_order_data['ddate_time'] = strtotime($confirm_obj->delivery_date);

                                        $sub_order_data['bill_company_name'] = $bt_obj->company;
                                        $sub_order_data['bill_last_name'] = $bt_obj->last_name;
                                        $sub_order_data['bill_first_name'] = $bt_obj->first_name;
                                        $sub_order_data['bill_phone'] = $bt_obj->phone_1;
                                        $sub_order_data['bill_phone_2'] = $bt_obj->phone_2;
                                        $sub_order_data['bill_address_1'] = $bt_obj->address_1;
                                        $sub_order_data['bill_address_2'] = $bt_obj->address_1;
                                        $sub_order_data['bill_city'] = $bt_obj->city;
                                        $sub_order_data['bill_state'] = $bt_obj->state;
                                        $sub_order_data['bill_country'] = $bt_obj->country;
                                        $sub_order_data['bill_zip_code'] = $bt_obj->zip;
                                        $sub_order_data['account_email'] = $confirm_obj->user_email;
                                        $sub_order_data['bill_suite'] = $bt_obj->suite;
                                        $sub_order_data['bill_street_number'] = $bt_obj->street_number;
                                        $sub_order_data['bill_street_name'] = $bt_obj->street_name;

                                        $sub_order_data['deliver_company_name'] = $st_obj->company;
                                        $sub_order_data['deliver_last_name'] = $st_obj->last_name;
                                        $sub_order_data['deliver_first_name'] = $st_obj->first_name;
                                        $sub_order_data['deliver_phone'] = $st_obj->phone_1;
                                        $sub_order_data['deliver_cell_phone'] = $st_obj->phone_2;
                                        $sub_order_data['deliver_address_1'] = $st_obj->address_1;
                                        $sub_order_data['deliver_address_2'] = $st_obj->address_2;
                                        $sub_order_data['deliver_city'] = $st_obj->city;
                                        $sub_order_data['deliver_state'] = $st_obj->state;
                                        $sub_order_data['deliver_country'] = $st_obj->country;
                                        $sub_order_data['deliver_zip_code'] = $st_obj->zip;
                                        $sub_order_data['deliver_recipient_email'] = $st_obj->user_email;
                                        $sub_order_data['deliver_suite'] = $st_obj->suite;
                                        $sub_order_data['deliver_street_number'] = $st_obj->street_number;
                                        $sub_order_data['deliver_street_name'] = $st_obj->street_name;

                                        $sub_order_id = $this->SetSubOrder($i_sub, $sub_order_data);
                                        $sub_orders[$i_sub] = $sub_order_id;

                                        $sub_order_item_data = array();
                                        $sub_order_item_data['user_info_id'] = $confirm_obj->user_info_id;
                                        $sub_order_item_data['vendor_id'] = 1;
                                        $sub_order_item_data['product_id'] = $product_obj->product_id;
                                        $sub_order_item_data['product_sku'] = $product_obj->product_sku;
                                        $sub_order_item_data['product_name'] = $product_obj->product_name;
                                        $sub_order_item_data['nQuantityTemp'] = $cart_products[$product_key]['quantity'];
                                        $sub_order_item_data['product_currency'] = $product_obj->product_currency;
                                        $sub_order_item_data['order_status'] = $order_status;
                                        $sub_order_item_data['product_desc'] = $product_obj->product_desc;
                                        $sub_order_item_data['timestamp'] = $timestamp;

                                        $this->SetSubOrderItem($sub_order_id, $sub_order_item_data);

                                        $this->SetSubOrderXref($order_id, $sub_order_id);
                                    }
                                }
                            }
                        }

                        $product_final_price = number_format($cart_products[$product_key]['price'] * $confirm_obj->products_tax_rate + $cart_products[$product_key]['price'], 2, '.', '');
                        $product_obj->product_currency = '';

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
                            `order_item_currency`, 
                            `order_status`, 
                            `product_attribute`, 
                            `product_coupon`, 
                            `cdate`, 
                            `mdate` 
                        ) 
                        VALUES (
                            " . $order_id . ", 
                            '" . $database->getEscaped($confirm_obj->user_info_id) . "',
                            '', 
                            " . $product_obj->product_id . ", 
                            '" . $database->getEscaped($product_obj->product_sku) . "', 
                            '" . $database->getEscaped($product_obj->product_name) . "', 
                            " . $cart_products[$product_key]['quantity'] . ", 
                            '" . $cart_products[$product_key]['price'] . "',	
                            '" . $product_final_price . "', 	
                            '" . $database->getEscaped($product_obj->product_currency) . "', 
                            '" . $order_status . "', 
                            '" . $database->getEscaped(strip_tags($product_obj->product_desc)) . "', 
                            '', 
                            '" . $timestamp . "', 
                            '" . $timestamp . "'
                        )";

                        $database->setQuery($query);
                        if (!($database->query())) {
                            $debug = array(
                                'query' => $query,
                                'session' => $_SESSION,
                                'product' => $product_obj,
                                'confirm_obj' => $confirm_obj
                            );
                            mosMail($mosConfig_mailfrom, $mosConfig_fromname, 'test@bloomex.ca', 'cant add order item ', json_encode($debug), 1);
                        }



                        $order_item_id = $database->insertid();

                        //ORDER ITEM INGREDIENTS
                        $this->setOrderItemIngredients($order_id, $order_item_id, (int) $cart_products[$product_key]['product_id'], (int) $cart_products[$product_key]['quantity'], $cart_products[$product_key]['select_bouquet']);
                        //

                        $product_i++;
                    }



                    $klaviyoData = [
                        "OrderId" => $order_id,
                        "ItemNames" => [],
                        "Items" => [],
                        "BillingAddress" => [
                            "FirstName" => $bt_obj->first_name,
                            "LastName" => $bt_obj->last_name,
                            "Address1" => $bt_obj->address_1,
                            "City" => $bt_obj->city,
                            "CountryCode" => $bt_obj->country,
                            "Zip" => $bt_obj->zip,
                            "Phone" => $bt_obj->phone_1
                        ],
                        "ShippingAddress" => [
                            "Address1" => $bt_obj->address_1
                        ]
                    ];

                    global $mosConfig_absolute_path;
                    include_once $mosConfig_absolute_path.'/administrator/components/com_virtuemart/classes/KlaviyoTracker.php';
                    $KlaviyoTracker = new KlaviyoTracker;

                    foreach ($cart_products as $cartProduct) {
                        $klaviyoData['Items'][] = [
                            "ProductID" => $cartProduct['product_id'],
                            "SKU" => $cart_products_objects[$cartProduct['product_id']]->product_sku,
                            "ProductName" => $cart_products_objects[$cartProduct['product_id']]->product_name,
                            "Quantity" => $cartProduct['quantity'],
                            "ItemPrice" => $cartProduct['price'],
                            "RowTotal" => $cartProduct['price'] * $cartProduct['quantity'],
                            'ImageURL' => IMAGEURL . 'product/' . $cart_products_objects[$cartProduct['product_id']]->product_thumb_image,
                        ];

                        $orderedProduct = [
                            "OrderId" => $order_id,
                            "ProductID" => $cartProduct['product_id'],
                            "SKU" => $cart_products_objects[$cartProduct['product_id']]->product_sku,
                            "ProductName" => $cart_products_objects[$cartProduct['product_id']]->product_name,
                            "ImageUrl" =>  IMAGEURL . "product/" . $cart_products_objects[$cartProduct['product_id']]->product_thumb_image,
                            "Quantity" => $cartProduct['quantity'],
                        ];
                        try {
                            $KlaviyoTracker->getInstance()->sendOrderedProduct($my->email, $orderedProduct, $cartProduct['price'] * $cartProduct['quantity'] );
                        }catch (Exception $e) {}
                        $klaviyoData['ItemNames'][] = $cart_products_objects[$cartProduct['product_id']]->product_name;
                    }
                    try {
                        $KlaviyoTracker->getInstance()->sendPlaceOrder($my->email, $klaviyoData, $confirm_obj->prices->total_price + $confirm_obj->prices->donated_price);
                    }catch (Exception $e) {}



                    $new_bucks = $confirm_obj->prices->products_price * 0.025;

                        $this->updatebucks($confirm_obj->user_id, $order_id, $new_bucks, $confirm_obj->prices->used_bucks);



                    if ($confirm_obj->prices->used_donate_id > 0) {
                        $this->adddonate($order_id, $confirm_obj->prices->donated_price, $confirm_obj->prices->used_donate_id);
                    }

                    //!NEW CONFIRMATION
                    $query = "SELECT 
                        `g`.`shopper_group_discount`,
                        `g`.`shopper_group_name`,
                        `g`.`shopper_group_id`
                        FROM `jos_vm_shopper_vendor_xref` AS `x`
                        INNER JOIN `jos_vm_shopper_group` AS `g` ON `g`.`shopper_group_id`=`x`.`shopper_group_id`
                        WHERE `x`.`user_id`=" . $confirm_obj->user_id . "";

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
                                '" . number_format($confirm_obj->prices->products_price * floatval($shopper_group_obj->shopper_group_discount) / 100, 2, '.', '') . "',
                                '" . $shopper_group_obj->shopper_group_id . "',
                                '" . $shopper_group_obj->shopper_group_name . "'
                            )";

                        $database->setQuery($query);
                        $database->query();
                    }
                    if ($confirm_obj->redeem_credits > 0) {
                        $this->setCreditsLog($order_id, $confirm_obj->prices->used_credits);
                    }
                    //!NEW CONFIRMATION

                    $this->check_and_crete_vc_coupon($sProductId, $aQuantity, $bt_obj->first_name, $confirm_obj->user_email);
                    $_SESSION['checkout_ajax']['thankyou'] = md5('thankyou' . $order_id);
                    $_SESSION['checkout_ajax']['thankyou_order_id'] = $order_id;

                    $return['result'] = true;
                    $return['order_id'] = $order_id;

                } else {
                    $try_again = true;
                    if (isset($_SESSION['try_again'])) {
                        if ($_SESSION['try_again'] < 4) {
                            $_SESSION['try_again']++;
                            $_SESSION['try_again_data'][] = $PaymentVar;
                        } else {
                            $return['try_again'] = false;

                            $query = "UPDATE `jos_users`
                            SET
                                `block`='1'
                            WHERE
                                `id`='" . (int)$confirm_obj->user_id . "'
                            ";

                            $database->setQuery($query);
                            $database->query();


                            $fraud_subject = 'Bloomex.com.au automatic users blocking';
                            $fraud_html = 'User ID: ' . $confirm_obj->user_id . '<br/>User email: ' . $bt_obj->user_email . '<br/>Data:<br/>' . json_encode($_SESSION['try_again_data']);
                            if($mosConfig_fraud_notification_list) {
                                foreach ($mosConfig_fraud_notification_list as $fraud_email) {
                                    mosMail($mosConfig_mailfrom, $mosConfig_fromname, $fraud_email, $fraud_subject, $fraud_html, 1);
                                }
                            }
                            $query = "INSERT INTO tbl_users_block_history( user_id, username,block,reason_type,reason,datetime ) VALUES( '$confirm_obj->user_id','checkout','1','4th desclined payment attempt','Last payment response:".json_encode($PaymentVar)."',now() )";
                            $database->setQuery($query);
                            $database->query();



                            $try_again = false;
                            $mainframe->logout();
                        }
                    } else {
                        $_SESSION['try_again'] = 1;
                        $_SESSION['try_again_data'] = array($PaymentVar);
                    }
                    if ($try_again === true) {
                        $return['error'] = 'Please confirm Credit Card details and try again.';
                    } else {
                        $return['error'] = 'Account has been blocked (cause suspicious activity)';
                    }

                }
            } else {
                $return['error'] = 'Cart is empty.';
            }
        } else {
            $return['error'] = 'Your are not authorize.';
        }
        if(!$ajax) {

            if($stripeOrderLogId && $stripeResponse && isset($order_id)){
                $query = "UPDATE `tbl_stripe_orders_logs`  SET `order_status`='paid',`order_id`='".$order_id."',`transaction_details`='".$database->getEscaped(serialize($stripeResponse))."'
                                        WHERE `id`='" . (int)$stripeOrderLogId . "' ";
                $database->setQuery($query);
                $database->query();


//                update abandonment status to change shipping form email
                $sql = "SELECT id FROM tbl_cart_abandonment WHERE user_id=" . (int)$confirm_obj->user_id  . " AND (status ='abandonment' OR status='sent')   AND `datetime_dt`>'" . date('Y-m-d H:i:s', strtotime('-4 hours')) . "'";
                $database->setQuery($sql);
                $rows = $database->loadObjectList();

                if ($rows) {
                    $sql = "UPDATE  tbl_cart_abandonment SET status='wait_delivery_address',order_id='" . $order_id . "',user_info_id='" . $database->getEscaped($confirm_obj->user_info_id) . "',datetime_dt='" . date('Y-m-d H:i:s') . "'  WHERE  id = " . $rows[0]->id;
                    $database->setQuery($sql);
                    $database->query();
                }
            }

            mosRedirect('/checkout/2/'.(isset($return['error'])?'?msg='.$return['error']:''));
        }

        echo json_encode($return);
        exit;
    }

    public function createFastOrder($stripeResponse = [],$stripeOrderLogId='',$newSessionData=[]) {
        global $database, $my,$mosConfig_sitename,$VM_LANG, $mosConfig_absolute_path,
               $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname,
               $mosConfig_au_stripe_secret_key;

        $default_user_name = 'Fast Checkout Default User';
        date_default_timezone_set('Australia/Sydney');
        $LoginedUser = false;
        $timestamp = time();
        $mysqlDatetime = date('Y-m-d G:i:s', $timestamp);

        $file = '';
        $vendor_currency = '';

        $confirm_obj = new stdClass();

        if($newSessionData) {
            $my = new stdClass();
            $_SESSION = $newSessionData;
            $my->id = $_SESSION['checkout_ajax']['user_id'];
            $my->username = $_SESSION['checkout_ajax']['user_name'];
        }

        if($my->id){
            $LoginedUser = true;
        }

        $query = "SELECT id FROM #__users WHERE email = '{$database->getEscaped($stripeResponse[2])}'";
        $database->setQuery($query);
        $user_id = intval($database->loadResult());

        if (!$user_id && !$my->id) {
            $pwd = mosMakePassword();
            $pwdCache = md5($pwd);

            $query = "INSERT INTO #__users( name, username, email, usertype, block, gid,registerDate,password ) VALUES( '{$database->getEscaped($stripeResponse[3])}', '{$database->getEscaped($stripeResponse[2])}', '{$database->getEscaped($stripeResponse[2])}' , 'Registered' , 0, 18,'{$mysqlDatetime}','{$pwdCache}' )";
            $database->setQuery($query);
            $database->query();

            $my->id = $user_id = $_SESSION['checkout_ajax']['user_id'] = $database->insertid();
            $my->username = $stripeResponse[2];

            $user_info_id = md5($user_id . time());


            $query = "INSERT INTO `jos_vm_user_info` 
                    (  
                        `user_info_id`, 
                        `user_id`, 
                        `address_type`, 
                        `address_type2`, 
                        `address_type_name`, 
                        `suite`,
                        `street_number`, 
                        `street_name`, 
                        `first_name`, 
                        `phone_1`, 
                        `country`, 
                        `zip`,
                        `user_email`
                    ) 
                    VALUES(  
                        '" . $user_info_id . "', 
                        " . $user_id . ", 
                        'BT', 
                        '', 
                        '-default-', 
                        '', 
                        '', 
                        '', 
                        '" . $database->getEscaped($stripeResponse[3]) . "',
                        '" . $database->getEscaped($stripeResponse[4]) . "',
                        '" . $database->getEscaped($stripeResponse[5]) . "',
                        '" . $database->getEscaped($stripeResponse[6]) . "',
                        '" . $database->getEscaped($stripeResponse[2]) . "'
                    ),(  
                        '" .  md5($user_info_id) . "', 
                        " . $user_id . ", 
                        'ST', 
                        '', 
                        '-default-', 
                        '', 
                        '', 
                        '', 
                        '',
                        '',
                        'AUS',
                        '',
                        ''
                    )";
            $database->setQuery($query);
            $database->query();

            $shgid_check = 5;
            $shgid_check_id = mosGetuserShoperGroupId($database->getEscaped($stripeResponse[2]));
            if ($shgid_check_id) {
                $shgid_check = $shgid_check_id;
            }
            $q = "INSERT INTO jos_vm_shopper_vendor_xref ";
            $q .= "(user_id,vendor_id,shopper_group_id) ";
            $q .= "VALUES ('$user_id','1','" . $shgid_check . "')";

            $database->setQuery($q);
            $database->query();

            $query = "INSERT INTO #__core_acl_aro( section_value, value, order_value, name, hidden ) VALUES( 'users', {$user_id}, 0, '{$database->getEscaped($stripeResponse[3])}', 0 )";
            $database->setQuery($query);
            $database->query();
            $aro_id = $database->insertid();

            $query = "INSERT INTO #__core_acl_groups_aro_map( group_id, section_value, aro_id ) VALUES( 18, '', {$aro_id} )";
            $database->setQuery($query);
            $database->query();

            $subject = sprintf(_SEND_SUB, $stripeResponse[3], $mosConfig_sitename);
            $message = sprintf($VM_LANG->_PHPSHOP_USER_SEND_REGISTRATION_DETAILS, $stripeResponse[3], $mosConfig_sitename, $mosConfig_live_site,$stripeResponse[2],$pwd);
            mosMail($mosConfig_mailfrom, $mosConfig_fromname, $stripeResponse[2], $subject, $message);

        } elseif(!$my->id && $user_id) {
            $my->id = $_SESSION['checkout_ajax']['user_id'] = $user_id;
            $my->username = $stripeResponse[2];
        }


            //have to check if we have actual cart first - had issues
            $cart_products = array();
            $existProducts = [];
            $checkExistPromotionDiscountProduct = false;
            $checkExistSpecialDiscountProduct = false;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] AS $cart_product) {
                    if (isset($cart_product['product_id'])) {
                        if($cart_product['promotion_discount']) {
                            $checkExistPromotionDiscountProduct = true;
                        }
                        if($cart_product['hasSpecialDiscount']) {
                            $checkExistSpecialDiscountProduct = true;
                        }
                        $existProducts[] = $cart_product['product_id'];
                        $cart_products[] = array(
                            'product_id' => (int) $cart_product['product_id'],
                            'quantity' => (int) $cart_product['quantity'],
                            'price' => number_format($cart_product['price'], 2, '.', ''),
                            'select_bouquet' => $cart_product['select_bouquet'],
                            'select_sub' => $cart_product['select_sub']
                        );
                    }
                }
            }

            /* check if virtual product only in cart */
            $sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (" . implode(',', $existProducts) . ") and no_delivery='1'";
            $database->setQuery($sql);
            if ($database->loadResult() && count($existProducts) == 1) {
                $_SESSION['checkout_ajax']['virtual'] = true;
            }


            $query = "SELECT 
                    `ui`.*
                FROM `jos_vm_user_info` AS `ui`
                WHERE 
                    `ui`.`user_id`=" . (int) $my->id . "
                AND 
                    `ui`.`address_type`='ST'
                ORDER BY `ui`.`mdate` DESC
                ";

            $database->setQuery($query);
            $rows = $database->loadObjectList();

            if (sizeof($rows) > 0) {
                    $_SESSION['checkout_ajax']['user_info_id'] = $rows[0]->user_info_id;
            }

            if (sizeof($cart_products) > 0) {

                $confirm_obj->user_id = $my->id;
                $confirm_obj->user_name = $my->username;
                $confirm_obj->user_info_id = $_SESSION['checkout_ajax']['user_info_id']??'';
                $confirm_obj->user_email = $_SESSION['checkout_ajax']['account_email']??'';
                $confirm_obj->occasion = $_SESSION['checkout_ajax']['customer_occasion']??'';
                $confirm_obj->shipping_method = (int) $_SESSION['checkout_ajax']['shipping_method']??'';
                $confirm_obj->card_msg = $_SESSION['checkout_ajax']['card_msg']??'';
                $confirm_obj->signature = $_SESSION['checkout_ajax']['signature']??'';
                $confirm_obj->card_comment = $_SESSION['checkout_ajax']['card_comment']??'';
                $confirm_obj->delivery_date = $_SESSION['checkout_ajax']['delivery_date']??'';
                $confirm_obj->vendor_currency = $_SESSION['checkout_ajax']['vendor_currency_string']??'';
                $confirm_obj->name_on_card = $_SESSION['checkout_ajax']['name_on_card']??'';
                $confirm_obj->card_number = $_SESSION['checkout_ajax']['card_number']??'';
                $confirm_obj->card_cvv = $_SESSION['checkout_ajax']['card_cvv']??'';
                $confirm_obj->expire_month =$_SESSION['checkout_ajax']['expire_month']??'';
                $confirm_obj->expire_year = $_SESSION['checkout_ajax']['expire_year']??'';
                $confirm_obj->find_us = $_SESSION['checkout_ajax']['find_us']??'';
                $confirm_obj->redeem_bucks = $_SESSION['checkout_ajax']['redeem_bucks']??'';
                $confirm_obj->donation_id = $_SESSION['checkout_ajax']['donation_id']??'';
                $confirm_obj->redeem_credits = $_SESSION['checkout_ajax']['redeem_credits']??'';
                $confirm_obj->payment_method_state = $_SESSION['checkout_ajax']['payment_method_state']?? 'card';
                $confirm_obj->ip_address = $_SERVER['REMOTE_ADDR']??'';
                $confirm_obj->products_tax_rate = $_SESSION['checkout_ajax']['products_tax_rate'];
                $confirm_obj->free_shipping = $_SESSION['checkout_ajax']['free_shipping'];
                $confirm_obj->total_price = $_SESSION['checkout_ajax']['total_price'];
                $confirm_obj->products_price = $_SESSION['checkout_ajax']['products_price'];
                $confirm_obj->shipping_price = $_SESSION['checkout_ajax']['shipping_price'];

                if (!isset($_SESSION['checkout_ajax']['shipping_method'])) {
                    $_SESSION['checkout_ajax']['shipping_method'] = 31;
                }

                $query = "SELECT 
                    * 
                FROM `jos_vm_user_info` 
                WHERE 
                    `user_id`=" . $confirm_obj->user_id . "
                AND 
                    `address_type`='BT'
                ";
                $database->setQuery($query);
                $bt_obj = false;
                $database->loadObject($bt_obj);

                $aud = array('AUS', 'CAN', 'NZL', '');
                $currency = in_array(($bt_obj->country ?? 'AUS'), $aud) ? 'AUD' : 'USD';


                $order_number = 'blau_' . date('YmdHi') . '_' . mt_rand(10000000, 99999999);

                $sProductId = '';
                foreach ($_SESSION['cart'] as $key => $product) {
                    if ($product['product_id']) {
                        $aProductId[] = (int) $product['product_id'];
                        $sProductId .= (int) $product['product_id'] . ",";
                        $aQuantity[$product['product_id']] = $product['quantity'];
                        $aPrice[$product['product_id']] = $product['price'];
                        $aBouquet[$product['product_id']] = $product['select_bouquet'];
                    }
                }
                $sProductId = substr($sProductId, 0, strlen($sProductId) - 1);


                $order_tax_details = array();

                foreach ($_SESSION['cart'] AS $product_key => $cart_product) {
                    if (isset($cart_product['product_id'])) {

                        if (!isset($order_tax_details['' . $confirm_obj->products_tax_rate . ''])) {
                            $order_tax_details['' . $confirm_obj->products_tax_rate . ''] = doubleval($_SESSION['cart'][$product_key]['price']);
                        } else {
                            $order_tax_details['' . $confirm_obj->products_tax_rate . ''] += doubleval($_SESSION['cart'][$product_key]['price']);
                        }
                    }
                }


                        /* if only virtual product in cart, status delivered */
                        if (isset($_SESSION['checkout_ajax']['virtual'])) {
                            $order_status = "D";
                        }  else {
                            $order_status = "PD";
                        }


                    $warehouse = 'NOWAREHOUSEASSIGNED';

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
                        `username`,
                        `warehouse`
                    ) 
                    VALUES ( 	
                        " . $confirm_obj->user_id . ", 
                        '1', 
                        '" . $order_number . "', 
                        '" . $database->getEscaped($confirm_obj->user_info_id) . "', 
                        '" . $confirm_obj->total_price . "', 
                        '" . $confirm_obj->products_price . "', 
                        '0', 
                        '" . serialize($order_tax_details) . "', 
                        '" . $confirm_obj->shipping_price . "', 
                        '',  
                        '" . $currency . "', 
                        '" . $order_status . "', 
                        '" . $timestamp . "', 
                        '" . $timestamp . "', 
                        '',
                        '',
                        '" . $database->getEscaped($confirm_obj->card_msg) . "', 
                        '" . $database->getEscaped($confirm_obj->signature) . "', 
                        '" . $database->getEscaped($confirm_obj->occasion) . "', 
                        '" . $database->getEscaped($confirm_obj->card_comment) . "', 
                        '" . (int) $confirm_obj->find_us . "', 
                        '" . $database->getEscaped($confirm_obj->ip_address) . "', 	
                        '" . $database->getEscaped($confirm_obj->user_name) . "' ,
                        '" . $database->getEscaped($warehouse) . "' 
                    )";
                    $database->setQuery($query);
                    $database->query();
                    $order_id = $database->insertid();
                    if ($database->_errorNum > 0) {
                        $text_error = 'Error: ' . $database->_errorMsg;
                        $text_error .= '<br><br>Query: ' . $query;
                        moslogerrors('front insert order', $text_error);
                    }




                    $order_payment_trans_id = $stripeResponse[0]??'';
                    $order_payment_log = $stripeResponse[1]??'';
                    $query = "INSERT INTO `jos_vm_order_payment`
                        (	
                            `order_id`, 
                            `order_payment_code`, 
                            `payment_method_id`, 
                            `order_payment_number`, 
                            `order_payment_expire`, 
                            `order_payment_log`, 
                            `order_payment_name`, 
                            `order_payment_trans_id`
                        ) 
                        VALUES (
                            " . $order_id . ", 
                            '', 
                            3, 
                           'NOT SAVED', 
                            '',
                            '{$order_payment_log}[--1--]',
                            '',			
                            '{$order_payment_trans_id}'
                        )";

                    if(isset($stripeResponse[0]) && $stripeResponse[0]!='') {
                        require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
                        $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);
                        $stripe->paymentIntents->update(
                            $stripeResponse[0],
                            [
                                'metadata' => ['order_id' => $order_id,'order place method' => 'front end fast checkout'],
                                'description' => 'Order# '.$order_id
                            ]
                        );
                    }

                    $database->setQuery($query);
                    $database->query();

                    $ua=getBrowser();
                    $deviceDetails = ', Device: '.(isMobileDevice()?'Mobile':'Web') .
                        ', Browser: ' . $ua['name'] . ' ' . $ua['version'] . ' on ' .$ua['platform'] . ', UserAgent: ' . $ua['userAgent'];

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
                        " . $order_id . ", 
                        '" . $order_status . "', 
                        '" . $mysqlDatetime . "', 
                        1, 
                        'Front End , Fast Checkout " . $deviceDetails
                        . (($checkExistPromotionDiscountProduct) ? " | There is a promotion product in order" : '')
                        . (($checkExistSpecialDiscountProduct) ? " | There is a special discounted product in order" : '')
                        . ((!$LoginedUser) ? " | The customer was not logged in before the payment" : '') .
                        "',
                        '" . $database->getEscaped($confirm_obj->user_name) . "',
                        '" . $database->getEscaped($warehouse) . "'
                    )";
                    $database->setQuery($query);
                    $database->query();

                    $query = "INSERT INTO `jos_vm_order_user_info` 
                    (  
                        `order_id`, 
                        `user_id`, 
                        `address_type`, 
                        `address_type2`, 
                        `address_type_name`, 
                        `first_name`, 
                        `phone_1`, 
                        `country`, 
                        `zip`,
                        `user_email`
                    ) 
                    VALUES(  
                        " . $order_id . ", 
                        " . $confirm_obj->user_id . ", 
                        'BT', 
                        '', 
                        '-default-', 
                        '" . $database->getEscaped($stripeResponse[3]) . "',
                        '" . $database->getEscaped($stripeResponse[4]) . "',
                        '" . $database->getEscaped($stripeResponse[5]) . "',
                        '" . $database->getEscaped($stripeResponse[6]) . "',
                        '" . $database->getEscaped($stripeResponse[2]) . "'
                    ),
                    (  
                        " . $order_id . ", 
                        " . $confirm_obj->user_id . ", 
                        'ST', 
                        '', 
                        '-default-', 
                        '',
                        '',
                        'AUS',
                        '',
                        ''
                    )";
                    $database->setQuery($query);
                    $database->query();




                    $sub_orders = array();

                    $product_i = 1;
                    foreach ($cart_products as $product_key => $cart_product) {

                        $query = "SELECT 
                            *
                        FROM `jos_vm_product`
                        WHERE `product_id`=" . (int) $cart_products[$product_key]['product_id'] . "
                        ";
                        $database->setQuery($query);
                        $product_obj = false;
                        $database->loadObject($product_obj);

                        if (
                            $cart_products[$product_key]['select_bouquet'] == 'petite' OR
                            $cart_products[$product_key]['select_bouquet'] == 'deluxe' OR
                            $cart_products[$product_key]['select_bouquet'] == 'supersize'
                        ) {
                            $product_obj->product_name .= ' (' . htmlspecialchars($cart_products[$product_key]['select_bouquet']) . ')';
                        }

                        if (!empty($cart_products[$product_key]['select_sub'])) {
                            $select_sub = '';

                            if ($cart_products[$product_key]['select_sub'] == 'sub_3') {
                                $select_sub = 'Subscription 3 months';

                                $sub_months = 3;
                            } elseif ($cart_products[$product_key]['select_sub'] == 'sub_6') {
                                $select_sub = 'Subscription 6 months';

                                $sub_months = 6;
                            } elseif ($cart_products[$product_key]['select_sub'] == 'sub_12') {
                                $select_sub = 'Subscription 12 months';

                                $sub_months = 12;
                            }

                            $product_obj->product_name .= ' (' . htmlspecialchars($select_sub) . ')';

                            if ($sub_months) {
                                if (sizeof($sub_orders) == 0) {
                                    $sub_orders[0] = $order_id;
                                    $this->SetSubOrderXref($order_id, $order_id);
                                }

                                for ($i_sub = 1; $i_sub < $sub_months; $i_sub++) {
                                    if (array_key_exists($i_sub, $sub_orders)) {
                                        $sub_order_id = $sub_orders[$i_sub];

                                        $sub_order_item_data = array();
                                        $sub_order_item_data['user_info_id'] = $confirm_obj->user_info_id;
                                        $sub_order_item_data['vendor_id'] = 1;
                                        $sub_order_item_data['product_id'] = $product_obj->product_id;
                                        $sub_order_item_data['product_sku'] = $product_obj->product_sku;
                                        $sub_order_item_data['product_name'] = $product_obj->product_name;
                                        $sub_order_item_data['nQuantityTemp'] = $cart_products[$product_key]['quantity'];
                                        $sub_order_item_data['product_currency'] = $product_obj->product_currency;
                                        $sub_order_item_data['order_status'] = $order_status;
                                        $sub_order_item_data['product_desc'] = $product_obj->product_desc;
                                        $sub_order_item_data['timestamp'] = $timestamp;

                                        $this->SetSubOrderItem($sub_order_id, $sub_order_item_data);
                                    } else {
                                        $sub_order_data = array();
                                        $sub_order_data['user_id'] = $confirm_obj->user_id;
                                        $sub_order_data['vendor_id'] = 1;
                                        $sub_order_data['user_info_id'] = $confirm_obj->user_info_id;
                                        $sub_order_data['vendor_currency'] = $vendor_currency;
                                        $sub_order_data['order_status'] = $order_status;
                                        $sub_order_data['timestamp'] = $timestamp;
                                        $sub_order_data['sShippingMethod'] = $sShippingMethod;
                                        $sub_order_data['card_msg'] = $confirm_obj->card_msg;
                                        $sub_order_data['signature'] = $confirm_obj->signature;
                                        $sub_order_data['card_comment'] = $confirm_obj->card_comment;
                                        $sub_order_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
                                        $sub_order_data['user_name'] = $confirm_obj->user_name;
                                        $sub_order_data['ddate_time'] = strtotime($confirm_obj->delivery_date);

                                        $sub_order_data['bill_company_name'] = $bt_obj->company;
                                        $sub_order_data['bill_last_name'] = $bt_obj->last_name;
                                        $sub_order_data['bill_first_name'] = $bt_obj->first_name;
                                        $sub_order_data['bill_phone'] = $bt_obj->phone_1;
                                        $sub_order_data['bill_phone_2'] = $bt_obj->phone_2;
                                        $sub_order_data['bill_address_1'] = $bt_obj->address_1;
                                        $sub_order_data['bill_address_2'] = $bt_obj->address_1;
                                        $sub_order_data['bill_city'] = $bt_obj->city;
                                        $sub_order_data['bill_state'] = $bt_obj->state;
                                        $sub_order_data['bill_country'] = $bt_obj->country;
                                        $sub_order_data['bill_zip_code'] = $bt_obj->zip;
                                        $sub_order_data['account_email'] = $confirm_obj->user_email;
                                        $sub_order_data['bill_suite'] = $bt_obj->suite;
                                        $sub_order_data['bill_street_number'] = $bt_obj->street_number;
                                        $sub_order_data['bill_street_name'] = $bt_obj->street_name;

                                        $sub_order_data['deliver_company_name'] = $st_obj->company;
                                        $sub_order_data['deliver_last_name'] = $st_obj->last_name;
                                        $sub_order_data['deliver_first_name'] = $st_obj->first_name;
                                        $sub_order_data['deliver_phone'] = $st_obj->phone_1;
                                        $sub_order_data['deliver_cell_phone'] = $st_obj->phone_2;
                                        $sub_order_data['deliver_address_1'] = $st_obj->address_1;
                                        $sub_order_data['deliver_address_2'] = $st_obj->address_2;
                                        $sub_order_data['deliver_city'] = $st_obj->city;
                                        $sub_order_data['deliver_state'] = $st_obj->state;
                                        $sub_order_data['deliver_country'] = $st_obj->country;
                                        $sub_order_data['deliver_zip_code'] = $st_obj->zip;
                                        $sub_order_data['deliver_recipient_email'] = $st_obj->user_email;
                                        $sub_order_data['deliver_suite'] = $st_obj->suite;
                                        $sub_order_data['deliver_street_number'] = $st_obj->street_number;
                                        $sub_order_data['deliver_street_name'] = $st_obj->street_name;

                                        $sub_order_id = $this->SetSubOrder($i_sub, $sub_order_data);
                                        $sub_orders[$i_sub] = $sub_order_id;

                                        $sub_order_item_data = array();
                                        $sub_order_item_data['user_info_id'] = $confirm_obj->user_info_id;
                                        $sub_order_item_data['vendor_id'] = 1;
                                        $sub_order_item_data['product_id'] = $product_obj->product_id;
                                        $sub_order_item_data['product_sku'] = $product_obj->product_sku;
                                        $sub_order_item_data['product_name'] = $product_obj->product_name;
                                        $sub_order_item_data['nQuantityTemp'] = $cart_products[$product_key]['quantity'];
                                        $sub_order_item_data['product_currency'] = $product_obj->product_currency;
                                        $sub_order_item_data['order_status'] = $order_status;
                                        $sub_order_item_data['product_desc'] = $product_obj->product_desc;
                                        $sub_order_item_data['timestamp'] = $timestamp;

                                        $this->SetSubOrderItem($sub_order_id, $sub_order_item_data);

                                        $this->SetSubOrderXref($order_id, $sub_order_id);
                                    }
                                }
                            }
                        }

                        $product_final_price = number_format($cart_products[$product_key]['price'] * $confirm_obj->products_tax_rate + $cart_products[$product_key]['price'], 2, '.', '');
                        $product_obj->product_currency = '';

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
                            `order_item_currency`, 
                            `order_status`, 
                            `product_attribute`, 
                            `product_coupon`, 
                            `cdate`, 
                            `mdate` 
                        ) 
                        VALUES (
                            " . $order_id . ", 
                            '" . $database->getEscaped($confirm_obj->user_info_id) . "',
                            '', 
                            " . $product_obj->product_id . ", 
                            '" . $database->getEscaped($product_obj->product_sku) . "', 
                            '" . $database->getEscaped($product_obj->product_name) . "', 
                            " . $cart_products[$product_key]['quantity'] . ", 
                            '" . $cart_products[$product_key]['price'] . "',	
                            '" . $product_final_price . "', 	
                            '" . $database->getEscaped($product_obj->product_currency) . "', 
                            '" . $order_status . "', 
                            '" . $database->getEscaped(strip_tags($product_obj->product_desc)) . "', 
                            '', 
                            '" . $timestamp . "', 
                            '" . $timestamp . "'
                        )";

                        $database->setQuery($query);
                        if (!($database->query())) {
                            $debug = array(
                                'query' => $query,
                                'session' => $_SESSION,
                                'product' => $product_obj,
                                'confirm_obj' => $confirm_obj
                            );
                            mosMail($mosConfig_mailfrom, $mosConfig_fromname, 'test@bloomex.ca', 'cant add order item ', json_encode($debug), 1);
                        }



                        $order_item_id = $database->insertid();

                        //ORDER ITEM INGREDIENTS
                        $this->setOrderItemIngredients($order_id, $order_item_id, (int) $cart_products[$product_key]['product_id'], (int) $cart_products[$product_key]['quantity'], $cart_products[$product_key]['select_bouquet']);
                        //

                        $product_i++;
                    }

                    $new_bucks = $confirm_obj->products_price * 0.025;

                    $this->updatebucks($confirm_obj->user_id, $order_id, $new_bucks, '');


                    //!NEW CONFIRMATION
                    $query = "SELECT 
                        `g`.`shopper_group_discount`,
                        `g`.`shopper_group_name`,
                        `g`.`shopper_group_id`
                        FROM `jos_vm_shopper_vendor_xref` AS `x`
                        INNER JOIN `jos_vm_shopper_group` AS `g` ON `g`.`shopper_group_id`=`x`.`shopper_group_id`
                        WHERE `x`.`user_id`=" . $confirm_obj->user_id;

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
                                '" . number_format($confirm_obj->products_price * floatval($shopper_group_obj->shopper_group_discount) / 100, 2, '.', '') . "',
                                '" . $shopper_group_obj->shopper_group_id . "',
                                '" . $shopper_group_obj->shopper_group_name . "'
                            )";

                        $database->setQuery($query);
                        $database->query();
                    }

                    //!NEW CONFIRMATION

                    $this->check_and_crete_vc_coupon($sProductId, $aQuantity, $stripeResponse[3], $stripeResponse[2]);
                    $_SESSION['checkout_ajax']['thankyou'] = md5('thankyou' . $order_id);
                    $_SESSION['checkout_ajax']['thankyou_order_id'] = $order_id;

            } else {
                mosRedirect('/cart/?mosmsg=Cart is empty, please reach out to our Customer Service Team');
            }

            if($stripeOrderLogId && $stripeResponse && isset($order_id)){
                $query = "UPDATE `tbl_stripe_orders_logs`  SET 
                                     `user_name` = '".$database->getEscaped($stripeResponse[3])."',
                                     `user_email` = '".$database->getEscaped($stripeResponse[2])."',
                                     `order_total` = '".$database->getEscaped($confirm_obj->total_price)."',
                                     `order_status`='paid',
                                     `order_id`='".$order_id."',
                                     `transaction_details`='".$database->getEscaped(serialize($stripeResponse))."'
                                        WHERE `id`='" . (int)$stripeOrderLogId . "' ";
                $database->setQuery($query);
                $database->query();

            }

            mosRedirect('/fast-checkout-shipping-form/'.(isset($return['error'])?'?msg='.$return['error']:''));

    }

    public function setCreditsLog($order_id, $used_credits) {
        global $database, $my;

        date_default_timezone_set('Australia/Sydney');

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
            " . (int) $my->id . ",
            " . (int) $order_id . ",
            '" . $database->getEscaped($used_credits) . "',
            '" . $database->getEscaped('Redeem $' . $used_credits . ' credits.') . "',
            '" . $database->getEscaped($my->username) . "',
            '" . date('Y-m-d H:i:s') . "'
        )
        ";

        $database->setQuery($query);

        if ($database->query()) {
            $query = "UPDATE 
            `jos_vm_users_credits` 
            SET 
                `credits`=`credits` - '" . $used_credits . "'
            WHERE `user_id`=" . (int) $my->id . "";

            $database->setQuery($query);

            if ($database->query()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getCredits() {
        global $database, $my;

        $query = "SELECT 
            `uc`.`credits`
        FROM `jos_vm_users_credits` AS `uc`
        WHERE `uc`.`user_id`=" . (int) $my->id . "";

        $user_credits = false;

        $database->setQuery($query);
        $user_credits_result = $database->loadObject($user_credits);

        if ($user_credits_result === true) {
            return $user_credits->credits;
        } else {
            return 0;
        }
    }

    function check_and_crete_vc_coupon($sProductId, $aQuantity, $first_name, $user_email) {
        global $database, $mosConfig_live_site, $mosConfig_fromname, $mosConfig_mailfrom;

        //check VC product
        $sql = "SELECT product_id FROM #__vm_product WHERE product_id IN ($sProductId) AND product_sku = 'VC-01'";
        $database->setQuery($sql);
        $vc_rows = $database->loadResult();
        $sVCCouponCode = "";
        if ($vc_rows) {
            $vc_quantity = $aQuantity[$vc_rows];
            for ($s = 0; $s < $vc_quantity; $s++) {

                $sVCCouponCode = $this->createCouponName("VC-"); //"VC-" . strtoupper(genRandomString(8));
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

    function genRandomString($length = 10) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $string = "";
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }
        return $string;
    }

    function createCouponName($prefix) {
        $count = 0;
        while (true) {
            $sVCCouponCode = $prefix . strtoupper($this->genRandomString(8));
            if (!( $this->checkCouponName($sVCCouponCode) ))
                return $sVCCouponCode;
            if ($count > 5)
                return $sVCCouponCode . strtoupper($this->genRandomString(8)); // limit time
        }
        return "ERROR";
    }

    function checkCouponName($name) {
        global $database;
        $query = "SELECT coupon_id FROM #__vm_coupons WHERE coupon_code='$name'";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if ($result && isset($result[0]))
            return true;
        return false;
    }

    function process_payment_beanstream($order_number, $order_total, $PaymentVar, &$aResult) {
        global $mosConfig_test_card_numbers, $mosConfig_beanstream_mechant_id_usd;
        if (in_array($PaymentVar["order_payment_number"], $mosConfig_test_card_numbers)) {
            $order_total = 0.01;
        }
        $VM_LANG = new vmLanguage();
        $vmLogger = new vmLog();
        $name = $PaymentVar["bill_first_name"] . ' ' . $PaymentVar["bill_last_name"];
        $url = 'https://www.beanstream.com/scripts/process_transaction.asp';
        $fields = array(
            'merchant_id' => $mosConfig_beanstream_mechant_id_usd,
            'requestType' => 'BACKEND',
            'trnType' => 'P',
            'trnOrderNumber' => $order_number,
            'trnAmount' => urlencode($order_total),
            'trnCardOwner' => urlencode($PaymentVar["name_on_card"]),
            'trnCardNumber' => urlencode($PaymentVar["order_payment_number"]),
            'trnExpMonth' => urlencode(sprintf("%02d", $PaymentVar["expire_month"])),
            'trnExpYear' => urlencode(substr($PaymentVar["expire_year"], -2)),
            'trnCardCvd' => urlencode($PaymentVar["credit_card_code"]),
            'ordName' => urlencode($name),
            'ordAddress1' => urlencode($PaymentVar["bill_address_1"]),
            'ordAddress2' => urlencode($PaymentVar["bill_address_2"]),
            'ordCity' => urlencode($PaymentVar["bill_city"]),
            'ordProvince' => urlencode($PaymentVar["bill_state"]),
            'ordCountry' => urlencode($PaymentVar["bill_country"]),
            'ordPostalCode' => urlencode(strtoupper(str_replace('-', '', $PaymentVar["bill_zip_code"]))),
            'ordPhoneNumber' => urlencode($PaymentVar["bill_phone"])
        );

        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        $hash_salt = '6DF0DCFE-B857-40BE-9BA0-7D4C7387';
        $hash = sha1($fields_string . $hash_salt);
        $fields_string = $fields_string . "&hashValue=" . $hash;

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'curl_err ';
            // this would be your first hint that something went wrong
            $aResult["approved"] = 0;
            $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_INTERNAL_ERROR;
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus == 200) {
                // everything went better than expected
                parse_str($result, $response);

                if ($response['trnApproved'] == '1') {
                    /* We're approved (or captured)! */
                    $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS . ': ' . $response['messageText'];
                    /* record transaction ID */
                    $aResult["order_payment_trans_id"] = $response['trnId'];
                    $aResult["approved"] = 1;
                    return True;
                } else {

                    // Transaction Error
                    $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_ERROR . ': ' . $response['messageText'];
                    /* record transaction ID */
                    $aResult["order_payment_trans_id"] = $response['trnId'];
                    $aResult["approved"] = 0;

                    return False;
                }
            } else {
                // the request did not complete as expected. common errors are 4xx
                // (not found, bad request, etc.) and 5xx (usually concerning
                // errors/exceptions in the remote script execution)

                $aResult["approved"] = 0;
                $aResult["order_payment_log"] = $VM_LANG->_PHPSHOP_INTERNAL_ERROR;
                return False;
            }
        }
        curl_close($ch);
    }

    public function setDeliveryAddress() {
        global $database, $my;

        $return = array();
        $return['result'] = false;
        $user_id = $_SESSION['checkout_ajax']['user_id']??$my->id;
        $user_info_id = $database->getEscaped($_POST['user_info_id']);

        $query = "SELECT 
           *
        FROM `jos_vm_user_info` AS `ui`
        WHERE 
            `ui`.`user_info_id`='" . $user_info_id . "'
        AND
            `ui`.`user_id`=" . (int) $user_id . "
        AND 
            `ui`.`address_type`='ST'
        ";

        $database->setQuery($query);
        $st_obj = false;
        $database->loadObject($st_obj);

        if ($st_obj) {
            $_SESSION['checkout_ajax']['user_info_id'] = $st_obj->user_info_id;
            $return['result'] = true;
            $return['data'] = $st_obj;
        }

        echo json_encode($return);
        exit;
    }

    public function getDeliveryCalendarOptions() {

        $return = array();
        $return['result'] = false;

        $calendar = $this->getDeliveryCalendar();
        $options = $this->getDeliveryOptions($calendar['additionalDeliveryFee']??0);

        if ($calendar['result'] AND $options['result']) {
            $return['result'] = true;
            $return['calendar'] = $calendar['calendar'];
            $return['options'] = $options['options'];
            $return['error'] = $calendar['error'];
            $return['suggested_products'] = $calendar['suggested_products'];
        }

        echo json_encode($return);

        exit;
    }

    function getDeliveryOptionsHTML() {
        echo json_encode($this->getDeliveryCalendar());
        exit;
    }

    function getDeliveryOptions($additionalDeliveryFee = 0) {
        global $database;

        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            *
        FROM `jos_vm_shipping_rate`
        ORDER BY `shipping_rate_list_order` ASC LIMIT 3
        ";

        $subscription_month = 0;
        $sub_array = array(
            'sub_3' => 3,
            'sub_6' => 6,
            'sub_12' => 12
        );

        foreach ($_SESSION['cart'] AS $cart_product) {
            if (isset($cart_product['product_id'])) {
                if (array_key_exists(trim($cart_product['select_sub']), $sub_array)) {
                    if ($subscription_month < $sub_array[trim($cart_product['select_sub'])]) {
                        $subscription_month = $sub_array[trim($cart_product['select_sub'])];
                    }
                }
            }
        }

        $database->setQuery($query);
        $delivery_methods_obj = $database->loadObjectList();

        $return['options'] = '<div class="delivery_options">';
        $return['options'] .= '<div class="step"><span class="number">Step 1:</span> Select your delivery option:</div>';
        $return['options'] .= '<div class="truck"></div>';
        $return['options'] .= '<div class="methods">';
        foreach ($delivery_methods_obj as $delivery_method_obj) {
            if ($additionalDeliveryFee) {

                $healthy = array("14.99", "18.98");
                $yummy = array(14.99 + $additionalDeliveryFee, 18.98 + $additionalDeliveryFee);
                $delivery_method_obj->shipping_rate_name = str_replace($healthy, $yummy, $delivery_method_obj->shipping_rate_name);
            }
            $return['options'] .= '<div class="method">';
            $return['options'] .= '<label class="radio_container">';
            if ($delivery_method_obj->shipping_rate_id != 24) {
                $return['options'] .= '<span class="delivery_express"></span> ';
            }
            if ($subscription_month > 0) {
                $delivery_method_obj->shipping_rate_name .= '(Per month)';
            }
            $return['options'] .= '$' . number_format($delivery_method_obj->shipping_rate_value, 2, '.', '') . ' - ' . $delivery_method_obj->shipping_rate_name;
            $return['options'] .= '<input type="radio" name="delivery_option_id" id="delivery_option_id" value="' . $delivery_method_obj->shipping_rate_id . '" ' . ((int) $_SESSION['checkout_ajax']['shipping_method'] == $delivery_method_obj->shipping_rate_id ? 'checked' : '') . '><span class="checkmark"></span>';
            $return['options'] .= '</label>';
            $return['options'] .= '</div>';

            if ($subscription_month > 0) {
                break;
            }
        }
        $return['options'] .= '</div>';
        $return['options'] .= '<div class="step"><span class="number">Step 2:</span> Select your delivery date:</div>';
        $return['options'] .= '<div class="delivery_date" id="delivery_date"></div>';
        $return['options'] .= '</div>';

        $return['result'] = true;

        return $return;
    }

    function getDeliveryCalendarHTML() {
        echo json_encode($this->getDeliveryCalendar());
        exit;
    }

    private function setCalendarDay($day_class = '', $day = '', $day_delivery_date = '', $day_delivery_beaty_date = '', $need_price = false, $delivery_price = 0, $wrapper_class = '',$mergeAmountDays = null) {

        if ($mergeAmountDays === null) {
            $mergeAmountDays = 1;
        }

        if ($mergeAmountDays > 7) {
            $mergeAmountDays = 7;
        }

        $mergeAmountDaysClass = "day-wrap-$mergeAmountDays";
        $dayWrapperClass = 'day_wrapper';
        $dayWrapperClass .= ' ' . trim($wrapper_class);
        $dayWrapperClass .= ' ' . $mergeAmountDaysClass;

        $return = '<div class="' . $dayWrapperClass . '">';

        $return .= '<div class="' . $day_class . '" delivery_date="' . $day_delivery_date . '" delivery_beaty_date="' . $day_delivery_beaty_date . '">';
        $return .= '<span class="day_number">' . $day . '</span>';
        if ($need_price) {
            $return .= '<span class="day_price">$' . number_format($delivery_price, 2, '.', '') . '</span>';
        }
        $return .= '</div>';
        $return .= '</div>';

        return $return;
    }

    public function getDaysOfWeekInMonth($date, $neededDayOfWeek)
    {
        $timestamp = strtotime($date);
        list($year, $month, $amountDaysInMonth) = explode('/', date('Y/m/t', $timestamp));
        $days = range(1, $amountDaysInMonth);

        $result = [];
        foreach ($days as $day) {
            $tempTimestamp = strtotime("$month/$day/$year");
            $dayOfWeek = (int) date('N', $tempTimestamp);
            if ($dayOfWeek === $neededDayOfWeek) {
                $result[] = $day;
            }
        }

        return $result;
    }

    private function isAllowedBlendByPostalByWarehouse($unzip_obj)

    {
        global $database;
        if (empty($unzip_obj)) {
            return false;
        }

        $warehouseId = (int) $unzip_obj->warehouse_id;

        $query = "SELECT allow_local_blend,allow_out_of_town_blend
                            FROM `jos_vm_warehouse`
                            WHERE `warehouse_id` = ".$warehouseId;
        $warehouseBlendConfig = false;
        $database->setQuery($query);
        $database->loadObject($warehouseBlendConfig);
        if(!$warehouseBlendConfig){
            return false;
        }
        if ($warehouseBlendConfig->allow_local_blend && $warehouseBlendConfig->allow_out_of_town_blend) {
            return true;
        }
        if ($warehouseBlendConfig->allow_local_blend && !$unzip_obj->out_of_town) {
            return true;
        }

        if ($warehouseBlendConfig->allow_out_of_town_blend && $unzip_obj->out_of_town) {
            return true;
        }
        return false;

    }


    public function getDeliveryCalendar() {
        $this->GetPlatinumOptions();
        global $database, $my, $VM_LANG;

        $return = array();
        $return['result'] = false;
        $return['error'] = '';
        $return['suggested_products'] = '';

        if (!isset($_SESSION['checkout_ajax']['shipping_method'])) {
            $_SESSION['checkout_ajax']['shipping_method'] = 31;
        }
        if (isset($_POST['delivery_option_id']) AND!empty($_POST['delivery_option_id'])) {
            $_SESSION['checkout_ajax']['shipping_method'] = (int) $_POST['delivery_option_id'];
        }

        $query = "SELECT 
            *
        FROM `jos_vm_shipping_rate`
        WHERE 
            `shipping_rate_id`=" . (int) $_SESSION['checkout_ajax']['shipping_method'] . "
        ";

        $database->setQuery($query);
        $delivery_method_obj = false;
        $database->loadObject($delivery_method_obj);

        if ($delivery_method_obj) {

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

            $date_now = date('m/d/Y');

            $subscription_month = 0;
            $sub_array = array(
                'sub_3' => 3,
                'sub_6' => 6,
                'sub_12' => 12
            );

            $products_id = array();
            $issetFlowersInCart = false;
            foreach ($_SESSION['cart'] AS $cart_product) {
                if (isset($cart_product['product_id'])) {
                    if($cart_product['product_type'] == "1") {
                        $issetFlowersInCart = true;
                    }
                    $products_id[] = $cart_product['product_id'];
                    if (array_key_exists(trim($cart_product['select_sub']), $sub_array)) {
                        if ($subscription_month < $sub_array[trim($cart_product['select_sub'])]) {
                            $subscription_month = $sub_array[trim($cart_product['select_sub'])];
                        }
                    }
                }
            }
            if ($subscription_month > 0) {
                $date_now = date('m/d/Y', strtotime('+1 day'));
            }

            $delivery_date = (isset($_POST['delivery_date']) AND!empty($_POST['delivery_date']) AND preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/siu', $_POST['delivery_date'])) ? $_POST['delivery_date'] : $date_now;

            $delivery_date_a = explode('/', $delivery_date);
            $delivery_year = $delivery_date_a[2];
            $delivery_day = $delivery_date_a[1];
            $delivery_month = $delivery_date_a[0];

            $day_of_weeks = array('Sun','Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

            $delivery_day_of_week = date('N', strtotime($delivery_month . '/1/' . $delivery_year));

            $days_in_month = date('t', strtotime($delivery_date));
            $delivery_month_name = date('F', strtotime($delivery_date));
            $delivery_month_number = date('m', strtotime($delivery_date));


            $undeliverdayscount = 0;
            $undeliverpostalcode = 0;
            $additionalDeliveryFee = 0;
            $unzip_obj = false;
            $zip_symbols = 4;
            $oot = false;
            $supposedWarehouseId = '';
            while (($unzip_obj == false) AND ($zip_symbols > 0)) {
                $query = "SELECT 
                `postal_code`,
                `days_in_route`,
                `warehouse_id`,
                `additional_delivery_fee`,
                `deliverable`,
                `out_of_town`
            FROM `jos_postcode_warehouse`
            WHERE country = 'AUS' and 
                `postal_code` LIKE '" . substr($database->getEscaped($zip_obj->zip), 0, $zip_symbols) . "'
            AND 
                `published`='1' LIMIT 1
            ";
                $unzip_obj = false;
                $database->setQuery($query);
                $database->loadObject($unzip_obj);
                $zip_symbols--;
            }
            if ($unzip_obj) {
                $supposedWarehouseId = $unzip_obj->warehouse_id;
                if ($unzip_obj->deliverable == 0 && $issetFlowersInCart) {
                    //we don't have postal code checking process anymore
//                    $undeliverpostalcode = 1;
                } else {
                    $undeliverdayscount = $unzip_obj->days_in_route;
                    $oot = $unzip_obj->out_of_town;
                    $return['additionalDeliveryFee'] = $additionalDeliveryFee = $unzip_obj->additional_delivery_fee;
                }

            }


            $query = "SELECT 
                * 
            FROM `tbl_delivery_options`
            ";
            $database->setQuery($query);
            $delivery_options_obj = $database->loadObjectList();

            $freeshipping_option_a = [];
            $unavailable_option_a = [];
            $freeshipping_option_a = [];
            $surcharge_option_a = [];

            foreach ($delivery_options_obj as $delivery_option_obj) {
                if ($delivery_option_obj->type == 'surcharge') {
                    $surcharge_option_a[$delivery_option_obj->calendar_day] = array('title' => $delivery_option_obj->name, 'price' => $delivery_option_obj->price);
                } elseif ($delivery_option_obj->type == 'unavaliable') {
                    $unavailable_option_a[$delivery_option_obj->calendar_day] = $delivery_option_obj->name;
                } elseif ($delivery_option_obj->type == 'ootsurcharge') {
                    if ($oot) { //go like undeliver
                        $unavailable_option_a[$delivery_option_obj->calendar_day] = $delivery_option_obj->name;
                    } else { // jsut usrcharge
                        $surcharge_option_a[$delivery_option_obj->calendar_day] = array('title' => $delivery_option_obj->name, 'price' => $delivery_option_obj->price);
                    }
                } elseif ($delivery_option_obj->type == 'oot') {
                    if ($oot) {
                        $unavailable_option_a[$delivery_option_obj->calendar_day] = $delivery_option_obj->name;
                    }
                } elseif ($delivery_option_obj->type == 'free') {
                    $freeshipping_option_a[$delivery_option_obj->calendar_day] = $delivery_option_obj->name;
                }
            }



            $sql_un_del = "SELECT  
                available_from_date,EXTRACT( DAY FROM `available_from_date` ) as 'available_from_day' , EXTRACT( MONTH FROM `available_from_date` ) as 'available_from_month',
                available_until_date,EXTRACT( DAY FROM `available_until_date` ) as 'available_until_day' , EXTRACT( MONTH FROM `available_until_date` ) as 'available_until_month',
                json_data, `description` FROM `tbl_unavailable_delivery` 
            WHERE '" . $delivery_year . (strlen($delivery_month) == 1 ? '0' . $delivery_month : $delivery_month) . "' BETWEEN EXTRACT(YEAR_MONTH FROM available_from_date) AND  EXTRACT(YEAR_MONTH FROM available_until_date) 
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

//get city name by postal code
                $query = "SELECT city
                            FROM `jos_postcode_warehouse`
                            WHERE `country` = 'AUS' and `postal_code` = '" . $database->getEscaped($zip_obj->zip) . "'";
                $database->setQuery($query);
                $cityObj = $database->loadResult();

                foreach ($period as $key => $value) {
                    if ($value->format('Y') == $delivery_year and intval($value->format('m')) == $delivery_month) {
                        if (
                            in_array($zip_obj->state, $unavailable_states) ||
                            in_array($zip_obj->zip, $unavailable_postalCodes) ||
                            ($cityObj && in_array(strtolower($cityObj), array_map('strtolower', $unavailable_cities)))
                        ) {
                            $un_del_array[] = $value->format('j');
                        }
                    }
                }
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

            $sql = "SELECT * FROM `jos_vm_product_options` WHERE `product_id` IN (" . implode(',', $products_id) . ")";
            $database->setQuery($sql);
            $products_obj = $database->loadObjectList();

            $freeShipping = 1;
            foreach ($products_obj as $product_obj) {
                if ((int) $product_obj->no_delivery == 0) {
                    $freeShipping = 0;
                }
            }

            $sql = "SELECT product_id FROM #__vm_product_options WHERE  product_id in (" . implode(',', $products_id) . ") and no_delivery_order='0'";
            $database->setQuery($sql);
            if (!$database->loadResult()) {
                $freeShipping = 1;
            }

            $this->GetCartPrice();
            $this->GetFreeShippingOptions();
            if ($this->free_shipping) {
               $freeShipping = 1;
            }

            $mergedAmounts = $neededDay = $lastNeededDay = null;
            $mergedDays = [];

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


                if (!empty($activeHoliday)) {


                    $neededDay =  (int) date('j', strtotime($activeHoliday['start_date']));

                    $amountDays = $activeHoliday['amount_days'] - 1;

                    $lastNeededDay = $neededDay + $amountDays;

                    if ($lastNeededDay > $days_in_month) {

                        $lastNeededDay = $days_in_month;

                    }

                    $neededDays = range($neededDay, $lastNeededDay);

                    $saturdaysInMonth = $this->getDaysOfWeekInMonth($delivery_date,6);

                    $groupIndex = reset($neededDays);

                    foreach ($neededDays as $d) {
                        if ($d > (int)$days_in_month) {
                            break;
                        }
                        $mergedDays[$groupIndex][] = $d;
                        if (in_array($d, $saturdaysInMonth)) {
                            $groupIndex = $d + 1;
                        }

                    }

                }

            }

            $return['calendar'] = '';
            $return['disabled_dates'] = [];

            $isAllowBlendForWarehouse = $this->isAllowedBlendByPostalByWarehouse($unzip_obj);

            if (!empty($activeHoliday) && (int) $delivery_month === (int) date('n',strtotime($activeHoliday['start_date'])) && $isAllowBlendForWarehouse) {

                $return['calendar'] .= '<div class="alert alert-warning" role="alert">';

                $lastDay = '';
                if ($activeHoliday['amount_days'] > 2) {
                    $lastDay = strtr($VM_LANG->_MERGED_DELIVERY_DAYS_INFO_ALERT_LAST_DAY, [
                        '{day}' => $neededDay + $activeHoliday['amount_days'] - 1
                    ]);
                }

                $return['calendar'] .= strtr($VM_LANG->_MERGED_DELIVERY_DAYS_INFO_ALERT, [
                    '{monthName}' => $delivery_month_name,
                    '{firstDay}' => $neededDay,
                    '{secondDay}' => $neededDay + 1,
                    '{lastDay}' => $lastDay,
                ]);
                $return['calendar'] .= '</div>';

            }


            //date_default_timezone_set($default_timezone);

            $return['calendar'] .= '<div class="title">Delivery calendar</div>';
            if ($additionalDeliveryFee) {
                $return['calendar'] .= '<div class="alert alert-info" style="margin-top: 20px;" role="alert">' .  'Additional delivery fee for remote area: ' . number_format($additionalDeliveryFee, 2, '.', '') . '$</div>';
            }
            $return['calendar'] .= '<div class="truck"></div>';
            $return['calendar'] .= '<div class="title">Select a delivery date below</div>';
            $return['calendar'] .= '<div class="text">Select your Delivery Date by clicking on the date you wish to have your order delivered...</div>';
            $return['calendar'] .= '<div class="unavailable_date_message">This date is not available for delivery. Please select a date shown in white.</div>';
            $return['calendar'] .= '<div class="delivery_calendar">';
            $return['calendar'] .= '<div class="title">';
            $return['calendar'] .= '<div class="pre_month" delivery_date="' . date('m/d/Y', strtotime($delivery_date . ' -1 month')) . '"></div>';
            $return['calendar'] .= '<div class="now_month">' . $delivery_month_name . ' ' . htmlspecialchars($delivery_year) . '</div>';
            $next_month = $delivery_month + 1;
            $next_month_day = 1;
            $next_month_year = $delivery_year;

            if ($next_month > 12) {
                $next_month = 1;
                $next_month_year += 1;
            }

            $return['calendar'] .= '<div class="next_month" delivery_date="' . date('m/d/Y', strtotime($next_month . '/' . $next_month_day . '/' . $next_month_year . '')) . '"></div>';
            $return['calendar'] .= '</div>';

            foreach ($day_of_weeks as $day_of_week) {
                $return['calendar'] .= '<div class="day_of_week">' . $day_of_week . '</div>';
            }

            for ($pre_day = 0; $pre_day < $delivery_day_of_week; $pre_day++) {
                $return['calendar'] .= $this->setCalendarDay('day_inner past');
            }


            if (isset($_SESSION['blendedDate'])) {
                unset($_SESSION['blendedDate']);
            }

            if (isset($_SESSION['isLastMinuteOrder'])) {
                unset($_SESSION['isLastMinuteOrder']);
            }


            for ($day = 1; $day <= $days_in_month; $day++) {
                $day_wrapper_class = '';
                $delivery_price = $delivery_method_obj->shipping_rate_value;
                if ($additionalDeliveryFee) {
                    $delivery_price += $additionalDeliveryFee;
                }
                $need_price = true;
                $day_class = 'day_inner ready';

                $day_date = date('Y-m-d', strtotime($delivery_month . '/' . $day . '/' . $delivery_year));
                $day_delivery_date = date('d-m-Y', strtotime($delivery_month . '/' . $day . '/' . $delivery_year));
                //change date format;
                $day_delivery_beaty_date = date('l, M d Y', strtotime($delivery_month . '/' . $day . '/' . $delivery_year));

                if (strtotime($date_now . ' +' . $undeliverdayscount . ' day') > strtotime($delivery_month . '/' . $day . '/' . $delivery_year)) {
                    $need_price = false;
                } elseif (in_array($day, $un_del_array)) {
                    $need_price = false;
                } elseif ($undeliverpostalcode == 1) {
                    $need_price = false;
                } elseif ((date('Y-m-d') == $day_date AND $same_day == false)) { //OR (date('Y-m-d', strtotime('+1 day')) == $day_date AND $next_day == false)
                    $need_price = false;
                } elseif (array_key_exists($day_date, $unavailable_option_a)) {
                    $need_price = false;
                } elseif (array_key_exists($day_date, $freeshipping_option_a) OR $freeShipping == 1) {
                    $delivery_price = 0;
                }

                if ($supposedWarehouseId) {
                    $query = "SElECT (l.orders_count - count(order_id)) as posible_order_count
                        FROM jos_vm_orders as o 
                        join jos_vm_warehouse as w on w.warehouse_code=o.warehouse
                        join jos_vm_warehouse_order_limit as l on l.warehouse_id=w.warehouse_id
                        WHERE o.ddate = '$day_delivery_date' AND `o`.`order_status` NOT IN ('X','O') and w.warehouse_id=$supposedWarehouseId group by o.warehouse ";
                    $possibleOrderCount = false;
                    $database->setQuery($query);
                    $database->loadObject($possibleOrderCount);
                    if ($possibleOrderCount && $possibleOrderCount->posible_order_count <= 0) {
                        $need_price = false;
                    }
                }

                if ($need_price == true) {
                    if (array_key_exists($day_date, $surcharge_option_a)) {
                        $delivery_price += $surcharge_option_a[$day_date]['price'];
                    }

                    if ($this->shipping_discount) {
                        $delivery_price -= $this->shipping_discount;
                    }
                }

                if ($date_now == $day_delivery_date OR $delivery_date == $day_delivery_date) {
                    $day_class .= ' now';
                }


                if ($subscription_month == 0) {
                    if ((date('Y-m-d') == $day_date AND $same_day == true) && $delivery_price > 0) {
                        $delivery_price += $same_next_day_surcharge;
                    }
                    elseif((date('Y-m-d', strtotime('+1 day')) == $day_date AND $next_day == true) && $delivery_price > 0) {
                        $delivery_price += $same_next_day_surcharge;
                    }
                }


                $isNeedToBlendDates = false;

                if (!empty($activeHoliday)) {
                    $isNeededMonth = (int) $delivery_month === (int) date('n',strtotime($activeHoliday['start_date']));
                    $isNeedToBlendDates = $isNeededMonth && array_key_exists($day, $mergedDays) && $isAllowBlendForWarehouse;
                }
                if ($isNeedToBlendDates) {
                    $need_price = true;
                }

                if ($need_price == false) {
                    $day_class = 'day_inner past';
                }

                $day_delivery_beaty_date = date('l, M d Y', strtotime($delivery_month . '/' . $day . '/' . $delivery_year));

                if ($isNeedToBlendDates) {
                    $mergedAmounts = count($mergedDays[$day] ?? []) ?? 1;
                    $day = $day + (count($mergedDays[$day] ?? []) - 1);

                    if ($neededDay === null || $lastNeededDay === null) {
                        $day_inner = $day;
                    } else {
                        $day_inner = "$neededDay-$lastNeededDay";
                    }
                    $delivery_price = 19.99;
                    if (array_key_exists($day_date, $freeshipping_option_a) OR $freeShipping == 1) {
                        $delivery_price = 0;
                    }
                    $day_wrapper_class = "day_wrapper_days";
                    $day_class .= " day_wrapper_merge";
                    $day_delivery_beaty_date = strtr($VM_LANG->_MERGED_DELIVERY_DAYS_INFO_MESSAGE, [
                        '{monthName}' => $delivery_month_name,
                        '{fromDay}' => $neededDay,
                        '{toDay}' => $lastNeededDay,
                        '{year}' => (int) date('Y',strtotime($activeHoliday['start_date']))
                    ]);

                    $day_delivery_date = date('d-m-Y', strtotime($activeHoliday['start_date']));
                    $_SESSION['blendedDate'] = $day_delivery_date;

                    if(date('Y-m-d', strtotime($delivery_month . '/' . $day . '/' . $delivery_year)) < date('Y-m-d') ){
                        $day_class = 'day_inner past';
                        $need_price = false;
                    }

                } else {
                    $day_inner = $day;
                }
                if(!$need_price){
                    $return['disabled_dates'][] = $day_delivery_date;
                }

                $return['calendar'] .= $this->setCalendarDay($day_class, $day_inner, $day_delivery_date, $day_delivery_beaty_date, $need_price, ($delivery_price>=0)?$delivery_price:0, $day_wrapper_class, $mergedAmounts);
            }

            $return['calendar'] .= '</div>';
            $return['result'] = true;
            if ($undeliverpostalcode == 1) {
                $return['error'] = 'Sorry we cannot deliver flowers to this location due to "Days in Transit". We would like to offer you a Gourmet Gift Hamper with a 20% Discount. Click <span class="bold_clickabel" onclick="document.getElementById(\'suggested_products\').style.display = \'block\';">Here</span> if that is of interest to you.';
                $_SESSION['enableSpecialDiscountInProductsForCustomer'] = true;
                $_SESSION['customerCantBuyFlowers'] = true;
                $return['suggested_products'] = $this->get_discounted_products_list();
            }
        } else {
            $return['error'] = 'Delivery option isn\'t exist.';
        }

        return $return;
    }

    function get_discounted_products_list() {
        global $database, $sef,$mosConfig_show_compare_at_price;


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
                and `pp`.`discount_for_customer` > 0 and `p`.`product_publish`='Y'
            GROUP BY `p`.`product_sku` ORDER BY  RAND() limit 4";

        $database->setQuery($query);
        $products_obj = $database->loadObjectList();


        $related_products = '<div id="suggested_products" class="container-fluid products related_products "><div class="row">';

        if ($products_obj) {
            foreach ($products_obj as $product_obj) {
                $product_old_price = number_format(round($product_obj->product_price, 2), 2, '.', '');
                $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
                $product_real_price_dicount = '';
                if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                    $product_real_price_dicount = round($product_real_price*$product_obj->discount_for_customer/100,2);;
                    $product_real_price = round($product_real_price - $product_real_price*$product_obj->discount_for_customer/100,2);
                }
                $product_rating = round($product_obj->rating, 1);
                $savingPrice = $product_old_price - $product_real_price;
                $link = $sef->getCanonicalProduct($product_obj->alias, true);


                $related_products .= '<div class="col-6 col-sm-3 col-md-3 col-lg-3 wrapper" price_ordering="' . $product_real_price . '" rating_ordering="' . $product_rating . '">
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
                    $related_products .= '<div style="font-size: 14px;color: #A40001;font-weight: bold;">Bloomex Price: <span class="price">$' . $product_real_price . '</span></div>';
                    $related_products .= '<div class="form-add-cart" id="div_' . $product_obj->product_id . '">
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
    private function get_data1($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function addFuneralDeliveryAddress($fhid = 0, $cobrand = '', $pid = 0) {
        global $database, $my;

        if ($fhid) {
            if (empty($cobrand) && empty($pid)) {
                $url = "http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?fhid=$fhid";
            } else {
                $url = "http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?fhid=$fhid&cobrand=$cobrand&pid=$pid";
            }

            $aData = json_decode($this->get_data1($url));

            $sObituaryFHPhone = !empty($aData->FuneralHome->FHPhone) ? htmlentities(trim($aData->FuneralHome->FHPhone), ENT_QUOTES) : "";
            $sObituaryFHKnownBy1 = !empty($aData->FuneralHome->FHKnownBy1) ? htmlentities(trim($aData->FuneralHome->FHKnownBy1), ENT_QUOTES) : "";

            if (!empty($aData->Obituary->FullName)) {
                $aObituaryFullName = explode(" ", $aData->Obituary->FullName, 2);
                $sObituaryFN = htmlentities(trim($aObituaryFullName[0]), ENT_QUOTES);
                $sObituaryLN = htmlentities(trim($aObituaryFullName[1]), ENT_QUOTES);
            } else {
                $sObituaryFN = $sObituaryFHKnownBy1; // Neu $cobrand = "", $pid = 0 set First Name = FuneralHome Address
                $sObituaryLN = "";
            }

            $sObituaryFHAddress1 = !empty($aData->FuneralHome->FHAddress1) ? htmlentities(trim($aData->FuneralHome->FHAddress1), ENT_QUOTES) : "";
            if ($sObituaryFHAddress1 != "") {
                $sObituaryFHAddress1 = "$sObituaryFHKnownBy1, $sObituaryFHAddress1";
            }
            $sObituaryFHAddress2 = !empty($aData->FuneralHome->FHAddress2) ? htmlentities(trim($aData->FuneralHome->FHAddress2), ENT_QUOTES) : "";
            if ($sObituaryFHAddress2 != "") {
                $sObituaryFHAddress2 = "$sObituaryFHKnownBy1, $sObituaryFHAddress2";
            }
            $sObituaryFHCity = !empty($aData->FuneralHome->FHCity) ? htmlentities(trim($aData->FuneralHome->FHCity), ENT_QUOTES) : "";
            $sObituaryFHState = !empty($aData->FuneralHome->FHState) ? htmlentities(trim($aData->FuneralHome->FHState), ENT_QUOTES) : "";
            $sObituaryFHZip = !empty($aData->FuneralHome->FHZip) ? htmlentities(trim($aData->FuneralHome->FHZip), ENT_QUOTES) : "";

            $hash_secret = "VirtueMartIsCool";

            $sObituaryFHAddress = explode(' ', trim($aData->FuneralHome->FHAddress1));
            $sObituaryFHStreetNumber = trim($sObituaryFHAddress[0]);
            $sObituaryFHStreetName = trim(preg_replace('/^' . $sObituaryFHAddress[0] . '/siu', '', trim($aData->FuneralHome->FHAddress1)));

            $sql = "SELECT 
                COUNT(*)
            FROM `jos_vm_user_info`
            WHERE 
                `address_type`='ST' 
            AND 
                `user_id`=" . (int) $my->id . "
            AND 
                `last_name`='" . $database->getEscaped($sObituaryLN) . "'
            AND 
                `first_name`='" . $database->getEscaped($sObituaryFN) . "'
            AND
                ( 
                    `address_1`='" . $database->getEscaped($sObituaryFHAddress1) . "' 
                    OR 
                    `address_2`='" . $database->getEscaped($sObituaryFHAddress2) . "'
                ) 
            AND
                `street_name`='" . $database->getEscaped($sObituaryFHStreetNumber) . "'
            AND
                `street_number`='" . $database->getEscaped($sObituaryFHStreetName) . "',
            AND
                `city`='" . $database->getEscaped($sObituaryFHCity) . "'
            AND 
                `state`='" . $database->getEscaped($sObituaryFHState) . "' 
            AND
                `zip`='" . $database->getEscaped($sObituaryFHZip) . "' 
            ";
            $database->setQuery($sql);
            $bExist = $database->loadResult();

            if (!$bExist) {
                $sql = "INSERT INTO `jos_vm_user_info`
                (
                    `user_info_id`, 
                    `user_id`,
                    `address_type`, 
                    `last_name`,
                    `first_name`, 
                    `phone_1`,
                    `address_1`, 
                    `address_2`,
                    `city`,
                    `state`,
                    `country`,
                    `zip`, 
                    `extra_field_3`,
                    `address_type2`,
                    `street_number`,
                    `street_name`,
                    `cdate`,
                    `mdate`
                )
                VALUES (
                    '" . md5(uniqid($hash_secret)) . "',
                    " . (int) $my->id . ", 
                    'ST', 
                    '" . $database->getEscaped($sObituaryLN) . "',
                    '" . $database->getEscaped($sObituaryFN) . "',
                    '" . $database->getEscaped($sObituaryFHPhone) . "',
                    '" . $database->getEscaped($sObituaryFHAddress1) . "',  
                    '" . $database->getEscaped($sObituaryFHAddress2) . "', 
                    '" . $database->getEscaped($sObituaryFHCity) . "', 
                    '" . $database->getEscaped($sObituaryFHState) . "',
                    'AUS', 
                    '" . $database->getEscaped($sObituaryFHZip) . "',
                    '" . $database->getEscaped('FuneralInfo|' . $fhid . '|' . $cobrand . '|' . $pid) . "',
                    'R', 
                    '" . $database->getEscaped($sObituaryFHStreetNumber) . "',
                    '" . $database->getEscaped($sObituaryFHStreetName) . "',
                    '" . time() . "',
                    '" . time() . "'
                )";

                $database->setQuery($sql);
                $database->query();
            }
        }
    }

    public function getSTAddressesRadio($user_id) {
        global $database, $my;

        $return = array();
        $return['result'] = false;

        if ((int) $my->id AND $my->id == $user_id) {

            if (!empty($_COOKIE['funeral_FHID'])) {
                $fhid = $_COOKIE['funeral_FHID'];
                $pid = $_COOKIE['funeral_PID'];
                $cobrand = $_COOKIE['funeral_COBRAND'];

                $this->addFuneralDeliveryAddress($fhid, $cobrand, $pid);

                setcookie('funeral_FHID', '', time() - 36000);
                setcookie('funeral_PID', '', time() - 36000);
                setcookie('funeral_COBRAND', '', time() - 36000);
            }

            $query = "SELECT 
                `ui`.*
            FROM `jos_vm_user_info` AS `ui`
            WHERE 
                `ui`.`user_id`=" . (int) $user_id . "
            AND 
                `ui`.`address_type`='ST'
            ORDER BY `ui`.`mdate` DESC
            ";

            $database->setQuery($query);
            $rows = $database->loadObjectList();

            if (sizeof($rows) > 0) {
                $return['result'] = true;
                $return['shipping_addresses'] = array();

                $i = 1;

                foreach ($rows as $row) {
                    if ($i == 1) {
                        $_SESSION['checkout_ajax']['user_info_id'] = $row->user_info_id;
                    }

                    $return['shipping_addresses'][] = '<tr user_info_id="' . $row->user_info_id . '">
                        <td>
                            <label class="radio_container"><input type="radio" name="user_info_id" value="' . $row->user_info_id . '" ' . ($i == 1 ? 'checked' : '') . '><span class="checkmark"></span></label>
                        </td>
                        <td>
                            <div class="data">
                                <span>Name:</span> ' . $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name . '
                                <br/>
                                <span>Address:</span> ' . $row->suite . ' ' . $row->street_number . ' ' . $row->street_name . ', ' . $row->city . ', ' . $row->zip . ', ' . $row->state . ', ' . $row->country . '
                            </div>
                            <div class="actions">
                                <button type="submit" class="btn btn-default edit_shipping_address">edit</button>
                                <button type="submit" class="btn btn-default remove_shipping_address">delete</button>
                            </div>
                        </td>
                    </tr>';

                    $i++;
                }
            }
        }

        return json_encode($return);
    }

    public function getSTAddresses() {
        global $my;

        echo $this->getSTAddressesRadio($my->id);
        exit;
    }

    public $checkout_errors = array();

    public function adddonate($order_id, $donated_price, $used_donate_id) {
        global $database;

        $sql = "INSERT INTO 
        `tbl_used_donation` 
        (
            `donation_id`,
            `donation_price`, 
            `order_id`
        ) 
        VALUES (
            " . (int) $used_donate_id . ",
            '" . $donated_price . "',
            " . (int) $order_id . "
        )";
        $database->setQuery($sql);
        $database->query();
    }

    public function updatebucks($user_id, $order_id, $new_bucks, $used_bucks) {
        global $database, $mosConfig_offset;
        date_default_timezone_set('Australia/Sydney');
        $timestamp = time();
        $mysqlDatetime = date('Y-m-d G:i:s', $timestamp);
        $new_bucks = number_format($new_bucks, 2, '.', '');
        $used_bucks = number_format($used_bucks, 2, '.', '');

        $query = "SELECT `bucks` FROM `tbl_bucks` WHERE `user_id`=" . (int) $user_id . "";
        $database->setQuery($query);
        $res = $database->loadResult();
        $current_value = $res - $used_bucks;
        $current_value += $new_bucks;
        $current_value = number_format($current_value, 2, '.', '');
        if ($res) {
            $sql = "UPDATE `tbl_bucks` SET `bucks`='" . $current_value . "' WHERE `user_id`=" . (int) $user_id . "";
            $database->setQuery($sql);
            $database->query();
        } else {
            $sql = "INSERT INTO `tbl_bucks` (`bucks`, `user_id`) VALUES ('" . $current_value . "', " . (int) $user_id . ")";
            $database->setQuery($sql);
            $database->query();
        }

        if ($used_bucks) {
            $comment = "Used $$used_bucks Bucks Into $order_id order";
            $sql = "INSERT INTO `tbl_bucks_history`
            (
                `used_bucks`, 
                `user_id`,
                `order_id`,
                `comment`,
                `date_added`
            ) 
            VALUES (
                '" . $used_bucks . "', 
                " . (int) $user_id . ", 
                " . (int) $order_id . ", 
                '" . $database->getEscaped($comment) . "',
                '" . $mysqlDatetime . "'
            )";
            $database->setQuery($sql);
            $database->query();
        }

        $comment = "Added New Bucks $$new_bucks. Current Bucks is  $$current_value ";
        $sql = "INSERT INTO `tbl_bucks_history`
        (
            `user_id`,
            `order_id`,
            `comment`,
            `date_added`
        ) 
        VALUES (
            " . (int) $user_id . ",
            " . (int) $order_id . ", 
            '" . $database->getEscaped($comment) . "', 
            '" . $mysqlDatetime . "'
        )";
        $database->setQuery($sql);
        $database->query();
    }

    public function get_current_bucks() {
        global $database, $my;
        $query = "SELECT `bucks` FROM `tbl_bucks` WHERE `user_id`=" . $my->id . "";
        $database->setQuery($query);
        $res = $database->loadResult();
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public function get_current_donation_id_price($donate) {
        global $database;

        $query = "SELECT price FROM `tbl_donation_vars` WHERE `id`=" . (int) $donate . "";
        $database->setQuery($query);
        $res = $database->loadResult();
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public function GetTotalAjax() {
        $redeem_bucks = mosGetParam($_POST, 'redeem_bucks', 0);
        $donation_id = mosGetParam($_POST, 'donation_id', 0);
        $redeem_credits = mosGetParam($_POST, 'redeem_credits', 0);
        $proof_drinking_age = mosGetParam($_POST, 'proof_drinking_age', 0);

        if(checkShoppingCartContainAlcohol() && $proof_drinking_age != '1') {
            $return = array();
            $return['result'] = false;
            $return['error'][0] = 'Please proof that you are of legal drinking age';
            die(json_encode($return));
        }

        echo json_encode($this->GetTotal($redeem_bucks, $donation_id, $redeem_credits));
        exit;
    }

    public function GetTotal($redeem_bucks, $donation_id, $redeem_credits) {
        global $my;

        $return = array();
        $delivery_date = $_POST['delivery_date']??($_SESSION['checkout_ajax']['delivery_date']??'');
        $json = $this->GetShippingPrice($delivery_date, ($_SESSION['checkout_ajax']['user_info_id']) ?? '');

        $this->GetCouponDiscount();

        $from_json = json_decode($json);

        $total_price = 0;
        $shipping_price = 0;
        $products_price = 0;
        $taxes_price = 0;
        $used_bucks = 0;
        $donated_price = 0;
        $used_donate_id = 0;

        if (sizeof($from_json->error) == 0) {

            $rate_json = json_decode($this->GetUserRating());

            if (sizeof($rate_json->error) > 0) {
                $return['result'] = false;
                $return['error'] = $rate_json->error;
            } else {
                if ($from_json->free_shipping == 0) {
                    $shipping_price = $from_json->shipping_price + $from_json->same_next_surcharge + $from_json->shipping_surcharge;
                }

                if ($from_json->shipping_discount > 0) {
                    if ($shipping_price > $from_json->shipping_discount) {
                        $shipping_price -= $from_json->shipping_discount;
                    } else {
                        $shipping_price = 0;
                    }
                }
                //we don't want shipping price to be less than zero
                 if ($shipping_price < 0) {
                     $shipping_price = 0;
                  }
                $products_price = $this->cart_total;
                $products_saved_price = $this->cart_saved_total;

                $corporate_discount = $products_price * $from_json->corporate_discount / 100;

                $total_price_without_tax_delivery = $products_price - $this->coupon_discount - $corporate_discount;

                if ($redeem_bucks) {
                    $bucks = $this->get_current_bucks();
                    if ($bucks) {
                        if ($total_price_without_tax_delivery > $bucks) {
                            $used_bucks = $bucks;
                        } else {
                            $used_bucks = $total_price_without_tax_delivery;
                        }
                    }
                }

                if ($donation_id) {
                    $donated_price = $this->get_current_donation_id_price($donation_id);
                    $used_donate_id = $donation_id;
                }

                $shipping_tax = 0; //$shipping_price * $from_json->shipping_tax_rate;
                $products_tax = 0; //($products_price - $this->coupon_discount - $corporate_discount) * $from_json->products_tax_rate;
                $products_tax = ($products_price - $this->coupon_discount - $corporate_discount) * $from_json->products_tax_rate;

                if ($this->no_tax == 1) {
                    $taxes_price = 0;
                } else {
                    $taxes_price = $shipping_tax + $products_tax;
                }

                //$total_price = $products_price + $shipping_price + $taxes_price - $this->coupon_discount - $corporate_discount - $used_bucks + $donated_price;
                $total_price = $products_price + $shipping_price - $this->coupon_discount - $corporate_discount - $used_bucks;

                $used_credits = 0;
                if ($redeem_credits > 0) {
                    $user_credits = $this->getCredits();
                    if ($total_price > $user_credits) {
                        $used_credits = $user_credits;
                    } else {
                        $used_credits = $total_price;
                    }
                }
                $total_price -= $used_credits;

                $return['result'] = true;
                $return['products_price'] = number_format($products_price, 2, '.', '');
                $return['products_saved_price'] = number_format(($products_saved_price + $corporate_discount + $this->coupon_discount), 2, '.', '');
                $return['shipping_tax'] = number_format($shipping_tax, 2, '.', '');
                $return['products_tax'] = number_format($products_tax, 2, '.', '');
                $return['taxes_price'] = number_format($taxes_price, 2, '.', '');
                $return['shipping_price'] = number_format($shipping_price, 2, '.', '');
                $return['corporate_discount'] = number_format($corporate_discount, 2, '.', '');
                $return['coupon_discount'] = number_format($this->coupon_discount, 2, '.', '');
                $return['total_price'] = number_format($total_price, 2, '.', '');
                $return['used_bucks'] = number_format($used_bucks, 2, '.', '');
                $return['donated_price'] = number_format($donated_price, 2, '.', '');
                $return['used_donate_id'] = (int) $used_donate_id;
                $return['total_price'] = number_format($total_price, 2, '.', '');
                $return['shipping_tax_rate'] = $return['products_tax_rate'] = 0;
                $return['used_credits'] = number_format($used_credits, 2, '.', '');


                $_SESSION['checkout_ajax']['used_bucks'] = $return['used_bucks'];
                $_SESSION['checkout_ajax']['donated_price'] = $return['donated_price'];
                $_SESSION['checkout_ajax']['used_donate_id'] = $used_donate_id;
                $_SESSION['checkout_ajax']['used_credits'] = $return['used_credits'];

                $_SESSION['checkout_ajax']['products_price'] = $return['products_price'];
                $_SESSION['checkout_ajax']['products_saved_price'] = $return['products_saved_price'];
                $_SESSION['checkout_ajax']['taxes_price'] = $return['taxes_price'];
                $_SESSION['checkout_ajax']['shipping_price'] = $return['shipping_price'];
                $_SESSION['checkout_ajax']['corporate_discount'] = $return['corporate_discount'];
                $_SESSION['checkout_ajax']['coupon_discount'] = $return['coupon_discount'];

                $_SESSION['checkout_ajax']['total_price'] = $return['total_price'];

                $_SESSION['checkout_ajax']['shipping_tax_rate'] = $return['shipping_tax_rate'];
                $_SESSION['checkout_ajax']['products_tax_rate'] = $return['products_tax_rate'];

                $_SESSION['checkout_ajax']['shipping_tax'] = $return['shipping_tax'];
                $_SESSION['checkout_ajax']['products_tax'] = $return['products_tax'];

                $_SESSION['checkout_ajax']['user_id'] = $my->id;
                $_SESSION['checkout_ajax']['user_name'] = $my->username;
                $_SESSION['checkout_ajax']['account_email'] = $my->email??'';

                $_SESSION['checkout_ajax']['free_shipping'] = $from_json->free_shipping;

                $_SESSION['checkout_ajax']['vendor_currency_string'] = $_SESSION['vendor_currency'];

            }
        } else {
            $return['result'] = false;
            $return['error'] = $from_json->error;
        }

        return $return;
    }

    private function GetCartPrice() {
        $cart_price = 0;
        $cart_saved_price = 0;

        if (is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product) {
                $cart_price += $product['quantity'] * $product['price'];
                $cart_saved_price += $product['quantity'] * $product['saved_price'];
            }
        }
        $this->cart_saved_total = round($cart_saved_price, 2);
        return $this->cart_total = round($cart_price, 2);
    }

    private function GetUserRating() {
        global $database, $my;

        $return = array();
        $return['error'] = array();

        $query = "SELECT `rate` FROM `jos_vm_users_rating` WHERE `user_id`=" . (int) $my->id . "";
        $database->setQuery($query);
        $rate = $database->loadResult();

        if ($rate == 1) {
            $query = "UPDATE `jos_vm_user_ccards` SET `block`=1 WHERE `user_id`=" . (int) $my->id . "";
            $database->setQuery($query);

            $database->query();

            $return['result'] = false;
            $return['error'][] = 'Unfortunately we unable to process your order. Try to place order in another company.';
        } else {
            $return['result'] = true;
        }

        return json_encode($return);
    }

    private function GetCouponDiscount() {
        global $database;

        $coupon_discount = 0;

        foreach ($_SESSION['cart'] as $v) {
            if (isset($v["not_apply_discount"]) && ( $v["not_apply_discount"] > 0)) {
                $not_apply_discount = $v["product_id"];
            }
        }
        if (isset($not_apply_discount) && $not_apply_discount > 0 && $_SESSION['checkout_ajax']['coupon_code']) {
            unset($_SESSION['checkout_ajax']['coupon_code']);
        }

        if (isset($_SESSION['checkout_ajax']['coupon_code']) AND!empty($_SESSION['checkout_ajax']['coupon_code'])) {
            $return = array();

            $query = "SELECT `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $database->getEscaped($_SESSION['checkout_ajax']['coupon_code']) . "'";

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

        return $this->coupon_discount = round($coupon_discount, 2);
    }

    private function GetProductOptions() {
        global $database;

        if (is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product) {
                if ($product['product_id'] > 0) {
                    $products_id[] = (int) $product['product_id'];
                }
            }

            $query = "SELECT `no_tax`, `no_delivery`, `no_delivery_order`, `next_day_delivery`,`must_be_combined` FROM `jos_vm_product_options` WHERE `product_id` IN (" . implode(',', $products_id) . ")";

            $database->setQuery($query);
            $product_options_result = $database->loadObjectList();

            $this->free_shipping = 1;
            $this->next_day = 0;
            $this->no_tax = 1;
            $freeShipping_order = false;
            $this->must_be_combined = 0;
            $j = 0;
            foreach ($product_options_result as $product_option) {
                if ($product_option->next_day_delivery == 1) {
                    $this->next_day = 1;
                }

                if ($product_option->no_tax == 0) {
                    $this->no_tax = 0;
                }
                if ($product_option->must_be_combined == 1 && $j == 0) {
                    $this->must_be_combined = 1;
                } else {
                    $this->must_be_combined = 0;
                    $j++;
                }
                if ((int) $product_option->no_delivery == 0) {
                    $this->free_shipping = 0;
                }
                if ((int) $product_option->no_delivery_order == 1) {
                    $freeShipping_order = true;
                }
            }

            if ($freeShipping_order == true) {
                $this->free_shipping = 1;
            }

            return true;
        }
    }

    private function GetPlatinumOptions() {
        global $database, $my;

        if (is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product) {
                if ($product['product_id'] > 0) {
                    $products_id[] = (int) $product['product_id'];
                }
            }

            $query = "SELECT `product_id` FROM `jos_vm_product`  WHERE (`product_sku`='PC-01' OR `product_sku`='PC-01SP') AND `product_id` IN (" . implode(',', $products_id) . ")";

            $new_platinum_result = false;
            $database->setQuery($query);
            $database->loadObject($new_platinum_result);

            $query = "SELECT `id` FROM `tbl_platinum_club` WHERE `user_id`=" . $my->id . " AND `end_datetime` IS NULL";

            $old_platinum_result = false;
            $database->setQuery($query);
            $database->loadObject($old_platinum_result);

            $this->shipping_discount = 0;

            if ($new_platinum_result OR $old_platinum_result) {
                $_SESSION['platinum_cart'] = 1;
                //$this->free_shipping = 1;
                $this->shipping_discount = 14.95;
            }

            return true;
        }
    }

    private function GetFreeShippingOptions() {
        global $database;
        $_SESSION['checkout_ajax']['free_shipping_by_price'] = false;
        $query = "SELECT `price` FROM `jos_freeshipping_price` WHERE `public`=1";

        $free_shipping_result = false;
        $database->setQuery($query);
        $database->loadObject($free_shipping_result);

        if ($free_shipping_result) {
            if ($free_shipping_result->price <= $this->cart_total) {
                $_SESSION['checkout_ajax']['free_shipping_by_price'] = true;
                $this->free_shipping = 1;
            }
        }

        return true;
    }

    private function GetDeliveryOptionsC($delivery_date,$zip) {
        global $database;

        $unzip_obj = false;
        $zip_symbols = 4;
        $oot = false;
        $additionalDeliveryFee = 0;
        while (($unzip_obj == false) AND ($zip_symbols > 0)) {
            $query = "SELECT 
                `postal_code`,
                `days_in_route`,
                `warehouse_id`,
                `additional_delivery_fee`,
                `deliverable`,
                `out_of_town`
            FROM `jos_postcode_warehouse`
            WHERE country = 'AUS' and 
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
            if ($unzip_obj->deliverable != 0) {
                $oot = $unzip_obj->out_of_town;
                $additionalDeliveryFee = $unzip_obj->additional_delivery_fee;
            }
        }
        $this->shipping_surcharge = $additionalDeliveryFee;
        $this->shipping_unavaliable = 0;

        $query = "SELECT `type`, `calendar_day`, `price` FROM `tbl_delivery_options`";

        $database->setQuery($query);
        $delivery_options_result = $database->loadObjectList();

        $ShippingSurcharge = array();
        $unAvailable = array();
        $freeshipping = array();

        if ($delivery_options_result) {
            foreach ($delivery_options_result AS $delivery_option) {
                if (date('Y-m-d', strtotime($delivery_option->calendar_day)) == $delivery_date) {
                    if ($delivery_option->type == 'surcharge') {
                        $this->shipping_surcharge += round($delivery_option->price, 2);
                    } elseif ($delivery_option->type == 'ootsurcharge' && !$oot) {
                        $this->shipping_surcharge += round($delivery_option->price, 2);
                    } elseif ($delivery_option->type == 'free') {
                        $this->free_shipping = 1;
                    } elseif ($delivery_option->type == 'unavaliable') {
                        $this->shipping_unavailable = 1;
                        $this->checkout_errors[] = 'On this date delivery is not available.';
                    }
                }
            }
        }

        return true;
    }


    private function GetBadPostcode($post_code) {
        global $database;

        $query = "SELECT `id` FROM `tbl_options` WHERE `name`='" . $post_code . "' AND `type`='postal_code' AND `published`=1 LIMIT 1";

        $bad_postcode_result = false;
        $database->setQuery($query);
        $database->loadObject($bad_postcode_result);

        if ($bad_postcode_result) {
            $this->shipping_unavailable = 1;
            $this->checkout_errors[] = 'Bad postcode.';
        }

        return true;
    }

    private function GetSameNextDay($delivery_date, $state) {

        $this->same_next_surcharge = 0;

        $default_timezone = date_default_timezone_get();

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
        if (array_key_exists($state, $timezones)) {
            date_default_timezone_set($timezones[$state]);
        } else {
            date_default_timezone_set('Australia/Sydney');
        }

        $same_day_hour = 13;
        $next_day_hour = 16;

        $same_day_limit = $same_day_hour * 60;
        $next_day_limit = $next_day_hour * 60;

        $hour_now = intval(date('H'));
        $minute_now = intval(date('i'));

        $time_now = $hour_now * 60 + $minute_now;

        if (date('Y-m-d') == $delivery_date) {
            if ($same_day_limit > $time_now) {
                $this->same_next_surcharge = 5;
            }
        } elseif (date('Y-m-d', strtotime('+1 day')) == $delivery_date) {
            if ($next_day_limit < $time_now) {
                $this->same_next_surcharge = 5;
            }
        }

        date_default_timezone_set($default_timezone);

        return true;
    }

    private function GetCorporateDiscount() {
        global $database, $my;

        $this->corporate_discount = 0;

        $query = "SELECT `SG`.`shopper_group_discount` FROM `jos_vm_shopper_vendor_xref` AS `SVX` INNER JOIN `jos_vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id` WHERE `SVX`.`user_id`=" . $my->id . " LIMIT 1";

        $corporate_discount_result = false;
        $database->setQuery($query);
        $database->loadObject($corporate_discount_result);

        if ($corporate_discount_result) {
            $this->corporate_discount = $corporate_discount_result->shopper_group_discount;
        }

        return true;
    }

    private function GetSubscriptionProduct() {
        $this->subscription_month = 0;

        foreach ($_SESSION['cart'] AS $key => $product) {
            if ($product['product_id']) {

                $sub_array = array(
                    'sub_3' => 3,
                    'sub_6' => 6,
                    'sub_12' => 12
                );

                if (array_key_exists(trim($product['select_sub']), $sub_array)) {
                    if ($this->subscription_month < $sub_array[$product['select_sub']]) {
                        $this->subscription_month = $sub_array[$product['select_sub']];
                    }
                }
            }
        }

        return true;
    }

    public function GetShippingPrice($delivery_date = '', $ship_to_info_id = '') {
        global $database, $my;

        $return = array();

        if (!empty($delivery_date)) {
            $a_delivery_date = explode('-', $delivery_date);

            $_SESSION['checkout_ajax']['delivery_date'] = $delivery_date;

            $month = $a_delivery_date[1];
            $day = $a_delivery_date[0];
            $year = $a_delivery_date[2];

            $query = "SELECT `shipping_rate_value` FROM `jos_vm_shipping_rate` WHERE `shipping_rate_id`=" . (int) $_SESSION['checkout_ajax']['shipping_method'] . " LIMIT 1";

            $shipping_method_result = false;
            $database->setQuery($query);
            $database->loadObject($shipping_method_result);

            if ($shipping_method_result) {
                $this->shipping_price = $shipping_method_result->shipping_rate_value;

                $blendedDate = $_SESSION['blendedDate'] ?? null;
                if ($blendedDate !== null && !empty($delivery_date)) {
                    $currentDeliveryDate = new DateTime($delivery_date);
                    $blendedDate = new DateTime($blendedDate);
                    if ($currentDeliveryDate->diff($blendedDate)->days === 0) {
                        $this->shipping_price += 5.04;
                        $this->is_blended_date = true;
                    }
                }

                $this->shipping_unavailable = 0;

                $query = "SELECT `user_info_id`, `state`, `country`, `zip` FROM `jos_vm_user_info` WHERE `user_info_id`='" . $database->getEscaped($ship_to_info_id) . "' AND `user_id`=" . $my->id . " AND `address_type`='ST'";

                $user_info_result = false;
                $database->setQuery($query);
                $database->loadObject($user_info_result);

                if ($user_info_result) {
                    $query = "SELECT `tax_rate` FROM `jos_vm_tax_rate` WHERE `tax_state`='" . $database->getEscaped($user_info_result->state) . "' AND `tax_country`='" . $database->getEscaped($user_info_result->country) . "'";

                    $tax_delivery_result = false;
                    $database->setQuery($query);
                    $database->loadObject($tax_delivery_result);

                    if ($tax_delivery_result) {

                        $this->shipping_tax_rate = $tax_delivery_result->tax_rate; //$tax_delivery_result->tax_delivery_rate;
                        $this->products_tax_rate = $tax_delivery_result->tax_rate;

                        $this->GetCartPrice();
                        $this->GetProductOptions();
                        $this->GetPlatinumOptions();
                        $this->GetFreeShippingOptions();
                        $this->GetDeliveryOptionsC($year . '-' . $month . '-' . $day,$user_info_result->zip);
                        $this->GetBadPostcode($user_info_result->zip);
                        $this->GetSameNextDay($year . '-' . $month . '-' . $day, $user_info_result->state);
                        $this->GetCorporateDiscount();
                        $this->GetSubscriptionProduct();

                        if ($this->subscription_month > 0) {
                            $this->shipping_surcharge = 0;
                            $this->same_next_surcharge = 0;
                            $this->shipping_price *= $this->subscription_month;
                        }

                        if ($this->free_shipping) {
                            $this->shipping_price = 0;
                        }
                        if ($this->must_be_combined == 0) {
                            if ($this->shipping_unavailable == 1) {
                                $return['result'] = false;
                                $return['error'] = $this->checkout_errors;
                            } else {
                                $return['result'] = true;

                                $return['cart_total'] = $this->cart_total;
                                $return['free_shipping'] = $this->free_shipping;
                                $return['shipping_surcharge'] = $this->shipping_surcharge;
                                $return['shipping_discount'] = $this->shipping_discount;
                                $return['same_next_surcharge'] = $this->same_next_surcharge;
                                $return['shipping_price'] = $this->shipping_price;
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
                        $return['error'][] = 'Choose delivery state.';
                    }
                } else {
                    $return['result'] = false;
                    $return['error'][] = 'Choose a shipping address.';
                }
            } else {
                $return['result'] = false;
                $return['error'][] = 'Choose a delivery option.';
            }
        } else {
            $return['result'] = false;
            $return['error'][] = 'Choose a delivery date.';
        }
        if (isset($this->is_blended_date) && $this->is_blended_date === true) {
            $return['same_next_surcharge'] = 0;
        }
        return json_encode($return);
    }

    public function SetCouponCode() {
        global $database, $my;

        date_default_timezone_set('Australia/Sydney');

        $coupon_code = $_POST['coupon_code'];

        $return = array();
        $return['result'] = false;

        foreach ($_SESSION['cart'] as $v) {
            if (isset($v["not_apply_discount"]) && ( $v["not_apply_discount"] > 0)) {
                $not_apply_discount = $v["product_id"];
            }
        }

        if (isset($not_apply_discount) && $not_apply_discount > 0) {
            $query = "SELECT product_name FROM jos_vm_product WHERE product_id='{$not_apply_discount}'";
            $database->setQuery($query);
            $result = $database->loadResult();
            $return['error'] = "Sorry, coupons cannot be used with {$result}";
            unset($_SESSION['url_coupon_code']);
        } else {


            $query = "SELECT 
            `c`.`coupon_code`, 
            `c`.`percent_or_total`, 
            `c`.`coupon_value`,
            CASE 
                WHEN `c`.`expiry_date`>'" . date('Y-m-d') . "' THEN 0 
                WHEN `c`.`expiry_date`='0000-00-00' THEN 0 
            ELSE 1 END as `expired`
        FROM `jos_vm_coupons` AS `c`
        WHERE `c`.`coupon_code`='" . $database->getEscaped($coupon_code) . "'";

            $coupon_result = false;
            $database->setQuery($query);
            $database->loadObject($coupon_result);

            if ($coupon_result) {
                if ($coupon_result->expired == 1) {
                    $return['error'] = 'Coupon has been expired.';
                } else {
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
                        $_SESSION['checkout_ajax']['coupon_code'] = $coupon_result->coupon_code;

                        $return['result'] = true;

                        if ($coupon_result->percent_or_total == 'percent') {
                            $coupon_discount = $coupon_result->coupon_value . '%';
                        } elseif ($coupon_result->percent_or_total == 'total') {
                            $coupon_discount = '$' . $coupon_result->coupon_value;
                        }

                        $return['info'] = 'Your coupon is applied. Discount is ' . $coupon_discount . '.';
                    }
                }
            } else {
                $return['error'] = 'Coupon does not exist or already been used.';
            }
        }

        echo json_encode($return);
        exit;
    }

    function process_payment_centralization($PaymentVarCentralization) {
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
            moslogerrors('Front payment', $text_error);
        }
        return $json;
    }

    function getDonation() {
        global $database, $PHPSHOP_LANG;

        $return = array();
        $return['result'] = false;





        $query = "SELECT
                `name`,
                `price`,
                `text`,
                `id`
            FROM `tbl_donation_vars` 
            WHERE 
                    `published`=1           
            ";
        $donate_obj = false;
        $database->setQuery($query);
        $database->loadObject($donate_obj);
        f($query, $donate_obj);
        if ($donate_obj) {
            $return['result'] = true;
            /*
              if ($mosConfig_lang == 'fr') {
              $donate_obj->name = $donate_obj->name_fr;
              $donate_obj->text = $donate_obj->text_fr;
              }
             */
            $donate_obj->label = str_replace(array('{amount}', '{name}'), array('<span class="amount">$' . $donate_obj->price . '</span>', '<span class="name">' . $donate_obj->name . '</span>'), $PHPSHOP_LANG->_VM_DONATION_LABEL);

            $return['donate'] = $donate_obj;
        }


        return json_encode($return);
    }

    function getDonationData() {
        if (isset($_SESSION['checkout_ajax']['user_info_id']) AND!empty($_SESSION['checkout_ajax']['user_info_id'])) {
            echo $this->getDonation($_SESSION['checkout_ajax']['user_info_id']);
        }

        exit;
    }

    function SetSubOrder($i_sub, $sub_order_data) {
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

    function SetSubOrderItem($sub_order_id, $sub_order_item_data) {
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
            '" . $database->getEscaped($sub_order_item_data['product_sku']) . "',
            '" . $database->getEscaped($sub_order_item_data['product_name']) . "',
            " . $sub_order_item_data['nQuantityTemp'] . ",
            '0.00',
            '0.00',
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

        $query = "SELECT 
            `l`.`igl_quantity` as `quantity`, 
            `o`.`igo_product_name` as `name`
        FROM `product_ingredients_lists` as `l`
            LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
        WHERE `l`.`product_id`=" . $sub_order_item_data['product_id'] . "";
        $database->setQuery($query);
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

    function SetSubOrderXref($order_id, $sub_order_id) {
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

}
