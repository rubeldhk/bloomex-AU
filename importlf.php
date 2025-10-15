<?php

include 'lfconfig.php';

global $mosConfig_offset, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_smtphost, $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol;
include 'configuration.php';

//bloomex live data
$receiver_host = $mosConfig_host;
$receiver_user = $mosConfig_user;
$receiver_password = $mosConfig_password;
$receiver_db = $mosConfig_db;


require_once 'cron/MAIL5.php';
echo '<a href="JavaScript:window.close()"><b>Close Window</b></a><hr/>';
error_reporting(E_ALL);
ini_set('display_errors', 1);

//connect bloomex
$bloomex = new mysqli($receiver_host, $receiver_user, $receiver_password, $receiver_db);
if ($bloomex->connect_error) {
    echo '<br/>Bloomex Connect Error (' . $bloomex->connect_errno . ') ' . $bloomex->connect_error;
} else {
    echo "Bloomex connected<br/>";
}

$qwerty = "UPDATE `tbl_last_importlfp` set block=1 where block=0";
$result = $bloomex->query($qwerty);

if ($bloomex->errno) {
    die('block  Error (' . $bloomex->errno . ') ' . $bloomex->error);
}

$num_rows = $bloomex->affected_rows;

// sucellfully blocked table

if ($num_rows > 0) {
    //connect lf
    $lf = new mysqli($feeder_host, $feeder_user, $feeder_password, $feeder_db);
    if ($lf->connect_error) {
        echo '<br/> Connect Error (' . $lf->connect_errno . ') ' . $lf->connect_error;
    } else {
        echo "{$feeder} connected<br/>";
    }
    //counting orders
    $query = "SELECT o.order_id FROM jos_vm_orders o "
            . "LEFT JOIN tbl_feed_out f ON f.order_id =o.order_id WHERE f.order_id is NULL and o.cdate> $feedstart order by o.order_id";
    echo '<div style="display:none">' . $query . '</div><br/>';
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the select query [' . $lf->error . ']');
    }
    if ($result->num_rows == 0) {
        $qwerty = "UPDATE `tbl_last_importlfp` set `block`=0, `last_date_timest`= UNIX_TIMESTAMP( NOW( )) where block=1";
        $result = $bloomex->query($qwerty);
        die('No new orders');
    }
    echo "$result->num_rows  new orders [Limited to 3 a run], start processing <br/>";
    // actuall check
    $query = "SELECT o.order_id FROM jos_vm_orders o "
            . "LEFT JOIN tbl_feed_out f ON f.order_id =o.order_id WHERE f.order_id is NULL and o.cdate> $feedstart order by o.order_id LIMIT 0,3";
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the select query [' . $lf->error . ']');
    }
    if ($result->num_rows == 0) {
        $qwerty = "UPDATE `tbl_last_importlfp` set `block`=0, `last_date_timest`= UNIX_TIMESTAMP( NOW( )) where block=1";
        $result = $bloomex->query($qwerty);
        die('No new orders');
    }

    $orders = array();
    $bloomex->set_charset("utf8");
    while ($row = $result->fetch_object()) {
        confirmOrder($row->order_id, $bloomex, $lf, $feeder);
    }


    $qwerty = "UPDATE `tbl_last_importlfp` set `block`=0, `last_date_timest`= UNIX_TIMESTAMP( NOW( )) where block=1";
    $result = $bloomex->query($qwerty);

    $bloomex->close();
    $lf->close();
    echo '<hr/><a href="JavaScript:window.close()"><b>Close Window</b></a>';
} else {
    $qwerty = "UPDATE `tbl_last_importlfp` SET `block`= IF((`last_date_timest`+100 < UNIX_TIMESTAMP( NOW( ))),0,1) where block=1";
    $upd_cnt = $bloomex->query($qwerty);
    if ($bloomex->errno) {
        die('clear block Error (' . $bloomex->errno . ') ' . $bloomex->error);
    }
    echo 'Sorry! Service is temporary busy, try again.';

    $bloomex->close();
}

function confirmOrder($feed_order_id, $bloomex, $lf, $feeder) {
    global $mosConfig_offset, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_smtphost, $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol;
    $timestamp = date("Y-m-d G:i:s", time() + ($mosConfig_offset * 60 * 60));
    $query = "SELECT * FROM jos_vm_orders where order_id=$feed_order_id";

    if (!$order = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the order select query [' . $lf->error . ']');
    }
    $query = "SELECT id FROM jos_users where name like '$feeder'";
    if (!$result = $bloomex->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running feeder select query[' . $bloomex->error . ']');
    }
    $user_id = $result->fetch_object();
    $user_id = $user_id->id;
    $order = $order->fetch_object();
    $feed_user_id = $order->user_id;
    $order->customer_note = $bloomex->real_escape_string($order->customer_note);
    $order->customer_signature = $bloomex->real_escape_string($order->customer_signature);
    $order->customer_occasion = $bloomex->real_escape_string($order->customer_occasion);
    $order->customer_comments = $bloomex->real_escape_string($order->customer_comments);
    $time = time() + 3600 * 14;
    $query = "INSERT INTO jos_vm_orders( user_id,
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
                                            username )
                    VALUES( 	'$user_id',
                                    '$order->vendor_id',
                                    '$order->order_number',
                                    '$order->user_info_id',
                                    '$order->order_total',
                                    '$order->order_subtotal',
                                    '$order->order_tax',
                                    '$order->order_tax_details',
                                    '$order->order_shipping',
                                    '$order->order_shipping_tax',
                                    '$order->coupon_discount',
                                    '$order->order_currency',
                                    '$order->order_status',
                                    '$order->cdate',
                                    '$time',
                                    '$order->ddate',
                                    '$order->ship_method_id',
                                    '$order->customer_note',
                                    '$order->customer_signature',
                                    '$order->customer_occasion',
                                    '$order->customer_comments',
                                    '$order->find_us',
                                    '$order->ip_address',
                                    '$feeder' )";
    if (!$result = $bloomex->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert query [' . $bloomex->error . ']');
    }
    $order_id = $bloomex->insert_id;
    
    //immidiatelly insert to feed out in order to preven duplicates
    $query = "INSERT INTO tbl_feed_out (order_id,outer_order_id,user_id,feed_receiver) "
            . "VALUES ('$feed_order_id','$order_id','$feed_user_id','Bloomex.com.au' )";
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the lf feed query [' . $lf->error . ']');
    }
    
    //ORDER HISTORY//
    $query = "SELECT * FROM `jos_vm_order_history` WHERE `order_id`=" . $order->order_id . "";
    if (!$lf_history = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the lf order history [' . $lf->error . ']');
    }

    $history_inserts = array();

    while ($row = $lf_history->fetch_object()) {

        $history_inserts[] = "(" . $order_id . ", '" . $row->order_status_code . "', '" . $row->warehouse . "', '" . $row->priority . "', '" . $row->date_added . "', '" . $row->customer_notified . "', '" . $row->warehouse_notified . "', '" . $row->comments . "', '" . $feeder . "')";
    }
    
    date_default_timezone_set('Australia/Sydney');
    $timestamp = time(); 
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

    $history_inserts[] = "(" . $order_id . ", '" . $order->order_status . "', '', '', '" . $mysqlDatetime . "', '0', '0', 'Pulled from " . $feeder . " [ order id: " . $order->order_id . "]',  '" . $feeder . "')";

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
            `user_name`
        )
        VALUES
            " . implode(',', $history_inserts) . "";

    if (!$result = $bloomex->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the history insert query [' . $bloomex->error . ']');
    }

    $query = "INSERT INTO `jos_vm_order_history`
        (
            `order_id`,
            `order_status_code`,
            `date_added`,
            `comments`
        )
        VALUES
        (
            '" . $order->order_id . "',
            '" . $order->order_status . "',
            '" . $mysqlDatetime . "',
            'Pushed to bloomex.com.au [BLCOMA order id: " . $order_id . "]'
        )";

    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the lf history insert query [' . $bloomex->error . ']');
    }
    //END ORDER HISTORY

    $query = "select * from  jos_vm_order_payment WHERE order_id=$feed_order_id";
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the order_payment query [' . $lf->error . ']');
    }
    $row = $result->fetch_object();

    $query = "INSERT INTO jos_vm_order_payment(	order_id,
                                                    order_payment_code,
                                                    payment_method_id,
                                                    order_payment_number,
                                                    order_payment_expire,
                                                    order_payment_log,
                                                    order_payment_name,
                                                    order_payment_trans_id)
                                            VALUES ('$order_id',
                                                    '{$bloomex->real_escape_string($row->order_payment_code)}',
                                                    '{$bloomex->real_escape_string($row->payment_method_id)}',
                                                    '{$bloomex->real_escape_string($row->order_payment_number)}',
                                                    '{$bloomex->real_escape_string($row->order_payment_expire)}',
                                                    '{$bloomex->real_escape_string($row->order_payment_log)}',
                                                    '{$bloomex->real_escape_string($row->order_payment_name)}',
                                                    '{$bloomex->real_escape_string($row->order_payment_code)}')";
    if (!$result = $bloomex->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the order payment [' . $bloomex->error . ']');
    }

    $query = "SELECT * from jos_vm_order_user_info where address_type='ST' AND order_id=$feed_order_id ORDER BY address_type_name,order_info_id ASC LIMIT 0,1";
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the select user info query [' . $lf->error . ']');
    }
    $st = $result->fetch_object(); // should be bt here

    $query = "SELECT * from jos_vm_order_user_info where address_type='BT' AND order_id=$feed_order_id ORDER BY address_type_name,order_info_id ASC LIMIT 0,1";
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the select user info query [' . $lf->error . ']');
    }
    $bt = $result->fetch_object(); // should be st here

    $bt_street_number = '';
    $bt_street_name = '';
    $bt_suite = '';
    $bt_address = $bt->address_1;
    $bt_address_arr = explode(" ", $bt_address);
    if (is_array($bt_address_arr)) {
        foreach ($bt_address_arr as $k => $v) {
            if ($k == 0) {
                $bt_street_number = $v;
            } else {
                if ($k == count($bt_address_arr) - 1) {
                    if (intval($v) == $v) {
                        $bt_suite = $v;
                    } else {
                        $bt_street_name = $bt_street_name . " " . $v;
                    }
                } else {
                    $bt_street_name = $bt_street_name . " " . $v;
                }
            }
        }
    }


    $st_street_number = '';
    $st_street_name = '';
    $st_suite = '';
    $st_address = $st->address_1;
    $st_address_arr = explode(" ", $st_address);
    if (is_array($st_address_arr)) {
        foreach ($st_address_arr as $k => $v) {
            if ($k == 0) {
                $st_street_number = $v;
            } else {
                if ($k == count($st_address_arr) - 1) {
                    if (intval($v) == $v) {
                        $st_suite = $v;
                    } else {
                        $st_street_name = $st_street_name . " " . $v;
                    }
                } else {
                    $st_street_name = $st_street_name . " " . $v;
                }
            }
        }
    }

    /* Insert the User Billto & Shipto Info to Order Information Manager Table */
    $query = "INSERT INTO jos_vm_order_user_info (  order_id,
                                                    user_id,
                                                    address_type,
                                                    address_type_name,
                                                    company,
                                                    last_name,
                                                    first_name,
                                                    middle_name,
                                                    phone_1,
                                                    fax,
                                                    suite,
                                                    street_number,
                                                    street_name,
                                                    address_1,
                                                    address_2,
                                                    city,
                                                    state,
                                                    country,
                                                    zip,
                                                    user_email )
                                               VALUES(  '$order_id',
                                                            '$user_id',
                                                            'BT',
                                                            '-default-',
                                                            '$bt->company',
                                                            '$bt->last_name',
                                                            '$bt->first_name',
                                                            '$bt->middle_name',
                                                            '$bt->phone_1',
                                                            '$bt->fax',
                                                            '$bt_suite',
                                                            '$bt_street_number',
                                                            '$bt_street_name',
                                                            '$bt->address_1',
                                                            '$bt->address_2',
                                                            '$bt->city',
                                                            '$bt->state',
                                                            '$bt->country',
                                                            '$bt->zip',
                                                            '$bt->user_email'
                                                             )";
    if (!( $bloomex->query($query))) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert BT query [' . $bloomex->error . ']');
    }
    /* Insert the User Billto & Shipto Info to Order Information Manager Table */
    $query = "INSERT INTO jos_vm_order_user_info (  order_id,
                                                    user_id,
                                                    address_type,
                                                    address_type_name,
                                                    company,
                                                    last_name,
                                                    first_name,
                                                    middle_name,
                                                    phone_1,
                                                    fax,
                                                    suite,
                                                    street_number,
                                                    street_name,
                                                    address_1,
                                                    address_2,
                                                    city,
                                                    state,
                                                    country,
                                                    zip,
                                                    user_email )
                                               VALUES(  '$order_id',
                                                            '$user_id',
                                                            'ST',
                                                            'address_type_name',
                                                            '$st->company',
                                                            '$st->last_name',
                                                            '$st->first_name',
                                                            '$st->middle_name',
                                                            '$st->phone_1',
                                                            '$st->fax',
                                                            '$st_suite',
                                                            '$st_street_number',
                                                            '$st_street_name',
                                                            '$st->address_1',
                                                            '$st->address_2',
                                                            '$st->city',
                                                            '$st->state',
                                                            '$st->country',
                                                            '$st->zip',
                                                            '$st->user_email'
                                                             )";
    if (!( $bloomex->query($query))) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert BT query [' . $bloomex->error . ']');
    }
    $user_info_id = $bloomex->insert_id;
    $query = "SELECT * from jos_vm_order_item where order_id=$feed_order_id";
    if (!$result = $lf->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the select items query [' . $lf->error . ']');
    }
    while ($product = $result->fetch_object()) {
        $query = "SELECT product_id from jos_vm_product where product_sku like '" . $product->order_item_sku . "'";
        if (!$product_id = $bloomex->query($query)) {
            die(__FILE__ . ' ' . __LINE__ . 'There was an error running the check product id query [' . $bloomex->error . ']');
        }

        $product_id = $product_id->fetch_object();
        if ($product_id == NULL) {

            //CREATE NEW PRODUCT
            $query = "SELECT * from `jos_vm_product` where `product_id`=" . $product->product_id . "";

            if (!$product_lf = $lf->query($query)) {
                die(__FILE__ . ' ' . __LINE__ . 'There was an error running the check product id query [' . $lf->error . ']');
            }

            $product_lf = $product_lf->fetch_object();

            if ($product_lf->product_discount_id > 0) {
                $query = "SELECT * from `jos_vm_product_discount` where `discount_id`=" . $product_lf->product_discount_id . "";

                if (!$product_d_lf = $lf->query($query)) {
                    die(__FILE__ . ' ' . __LINE__ . 'There was an error running the check product discount id query [' . $lf->error . ']');
                }

                $product_d_lf = $product_d_lf->fetch_object();

                $query = "INSERT INTO `jos_vm_product_discount`
                (
                    `amount`, 
                    `is_percent`, 
                    `start_date`, 
                    `end_date`, 
                    `discount_type`, 
                    `coupon_code`
                )
                VALUES
                (
                    '" . $product_d_lf->amount . "',
                    '" . $product_d_lf->is_percent . "',
                    '" . $product_d_lf->start_date . "',
                    '" . $product_d_lf->end_date . "',
                    '" . $product_d_lf->discount_type . "',
                    '" . $product_d_lf->coupon_code . "'
                )";

                if (!($bloomex->query($query))) {
                    die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert product discount query [' . $bloomex->error . ']');
                }

                $product_d_bl = $bloomex->insert_id;
            } else {
                $product_d_bl = 0;
            }

            $query = "INSERT INTO `jos_vm_product`
            (
                `vendor_id`,
                `product_sku`,
                `product_s_desc`,
                `product_desc`,
                `product_thumb_image`,
                `product_full_image`,
                `product_publish`,
                `product_related`,
                `product_weight`,
                `product_weight_uom`,
                `product_length`,
                `product_width`,
                `product_height`,
                `product_lwh_uom`,
                `product_url`,
                `product_in_stock`,
                `product_available_date`,
                `product_availability`,
                `product_special`,
                `product_discount_id`,
                `ship_code_id`,
                `cdate`,
                `mdate`,
                `product_name`,
                `product_sales`,
                `attribute`,
                `custom_attribute`,
                `product_tax_id`,
                `product_unit`,
                `product_packaging`,
                `ingredient_list`,
                `product_coupon_discount`,
                `meta_info`,
                `meta_info_fr`
            )
            VALUES
            (
                '" . $product_lf->vendor_id . "',
                '" . $bloomex->real_escape_string($product_lf->product_sku) . "',
                '" . $bloomex->real_escape_string($product_lf->product_s_desc) . "',
                '" . $bloomex->real_escape_string($product_lf->product_desc) . "',
                '" . $product_lf->product_thumb_image . "',
                '" . $product_lf->product_full_image . "',
                'N',
                '" . $product_lf->product_related . "',
                '" . $product_lf->product_weight . "',
                '" . $product_lf->product_weight_uom . "',
                '" . $product_lf->product_length . "',
                '" . $product_lf->product_width . "',
                '" . $product_lf->product_height . "',
                '" . $product_lf->product_lwh_uom . "',
                '" . $bloomex->real_escape_string($product_lf->product_url ). "',
                '" . $product_lf->product_in_stock . "',
                '" . $product_lf->product_available_date . "',
                '" . $product_lf->product_availability . "',
                '" . $product_lf->product_special . "',
                " . $product_d_bl . ",
                '" . $product_lf->ship_code_id . "',
                '" . $product_lf->cdate . "',
                '" . $product_lf->mdate . "',
                '" . $bloomex->real_escape_string($product_lf->product_name) . "',
                '" . $product_lf->product_sales . "',
                '" . $product_lf->attribute . "',
                '" .$bloomex->real_escape_string( $product_lf->custom_attribute) . "',
                '" . $product_lf->product_tax_id . "',
                '" . $product_lf->product_unit . "',
                '" . $product_lf->product_packaging . "',
                '" . $bloomex->real_escape_string($product_lf->ingredient_list) . "',
                '" . $product_lf->product_coupon_discount . "',
                '" .$bloomex->real_escape_string( $product_lf->meta_info) . "',
                '" .$bloomex->real_escape_string( $product_lf->meta_info_fr) . "'
            )";

            if (!($bloomex->query($query))) {
                echo "query: " . $query . "</br>";
                die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert product query [' . $bloomex->error . ']');
            }

            $product_id_bl = $bloomex->insert_id;

            $query = "INSERT INTO `jos_vm_product_options` (`product_id`) VALUES (" . $product_id_bl . ")";

            if (!($bloomex->query($query))) {
                echo "query: " . $query . "</br>";
                die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert product options query [' . $bloomex->error . ']');
            }

            $query = "SELECT * from `jos_vm_product_price` where `product_id`=" . $product->product_id . "";

            if (!$product_pr_lf = $lf->query($query)) {
                echo "query: " . $query . "</br>";
                die(__FILE__ . ' ' . __LINE__ . 'There was an error running the check product price query [' . $lf->error . ']');
            }

            $product_pr_lf = $product_pr_lf->fetch_object();

            $query = "INSERT INTO `jos_vm_product_price` 
            (
                `product_id`, 
                `product_price`, 
                `product_currency`, 
                `product_price_vdate`, 
                `product_price_edate`, 
                `cdate`, 
                `mdate`, 
                `shopper_group_id`, 
                `price_quantity_start`, 
                `price_quantity_end`, 
                `saving_price`, 
                `compare_at`
            )
            VALUES
            (
                " . $product_id_bl . ",
                '" . $product_pr_lf->product_price . "', 
                '" . $product_pr_lf->product_currency . "',  
                '" . $product_pr_lf->product_price_vdate . "',  
                '" . $product_pr_lf->product_price_edate . "',  
                '" . $product_pr_lf->cdate . "',  
                '" . $product_pr_lf->mdate . "',  
                '" . $product_pr_lf->shopper_group_id . "',  
                '" . $product_pr_lf->price_quantity_start . "',  
                '" . $product_pr_lf->price_quantity_end . "',  
                '" . (isset($product_pr_lf->saving_price) ? $product_pr_lf->saving_price : '') . "',  
                '" . (isset($product_pr_lf->compare_at) ? $product_pr_lf->compare_at : '') . "'
            )";

            if (!($bloomex->query($query))) {
                die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert product price query [' . $bloomex->error . ']');
            }

            $product_id = $product_id_bl;
            //END CREATE NEW PRODUCT


            /* echo "[ORDER#$order_id]WARNING: no bloomex product with sku " . $product->order_item_sku . " was found, please edit order manually";
              $product_id = 1;
             */
        } else {
            $product_id = $product_id->product_id;
        }

        $query = "INSERT INTO jos_vm_order_item (   order_id,
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
                                                           VALUES(     $order_id,
                                                                        '$user_info_id',
                                                                        '1',
                                                                        '$product_id',
                                                                        '" . $bloomex->real_escape_string($product->order_item_sku) . "',
                                                                        '" . $bloomex->real_escape_string($product->order_item_name) . "',
                                                                        '$product->product_quantity',
                                                                        '$product->product_item_price',
                                                                        '$product->product_final_price',
                                                                        '$product->order_item_currency',
                                                                        '$product->order_status',
                                                                        '" . $bloomex->real_escape_string($product->product_attribute) . "',
                                                                        '$product->cdate',
                                                                        '$product->mdate')";
        if (!($bloomex->query($query))) {
            die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert product query [' . $bloomex->error . ']');
        }
    }
    /* ===================================== Assign Order To The WareHouse ===================================== */
    $query = "SELECT WH.warehouse_email, WH.warehouse_code FROM jos_vm_warehouse AS WH, jos_postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code LIKE '" . substr($st->zip, 0, 3) . "%'";
    if (!$result = $bloomex->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the insert BT query [' . $bloomex->error . ']');
    }
    $oWarehouse = array();
    while ($row = $result->fetch_object()) {
        $oWarehouse[] = $row;
    }

    $query = "INSERT INTO tbl_feed_in (order_id	, feed_order_id, feed_user_id, feed_supplier)"
            . "VALUES ( '$order_id','$feed_order_id','$feed_user_id','$feeder' )";
    if (!$result = $bloomex->query($query)) {
        die(__FILE__ . ' ' . __LINE__ . 'There was an error running the bloomex feed insert  query [' . $bloomex->error . ']');
    }



    echo "<br/>$feeder order #" . $feed_order_id . " was sucessfully moved to bloomex [order id#$order_id]";
    if (count($oWarehouse)) {
        $oWarehouse = $oWarehouse[0];
        $warehouse_code = $oWarehouse->warehouse_code;
        $warehouse_email = $oWarehouse->warehouse_email;
        $query = "UPDATE jos_vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        if (!$result = $bloomex->query($query)) {
            die(__FILE__ . ' ' . __LINE__ . 'There was an error running the updating order with wh query [' . $bloomex->error . ']');
        }
        if ($warehouse_code) {
            $mail_Subject = "New order imported from  $feeder , Bloomex  Order ID #" . $order_id . " importer's Order Id was #" . $feed_order_id;
            $mail_Content = "New order imported from  $feeder ,<br/> Bloomex  Order ID #" . $order_id . " importer's Order Id was #" . $feed_order_id . "<br/> Please check it asap. Thanks!";
            $m = new MAIL5;
            $m->From($mosConfig_mailfrom);
            $m->AddTo($warehouse_email);
            $m->Subject($mail_Subject);
            $m->Html($mail_Content);
            $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
            if (!$c) {
                die(print_r($m->Result));
            }
            if ($m->Send($c)) {
                
            } else {
                '<br /><pre>';
                print_r($m->History);
                list($tm1, $ar1) = each($m->History[0]);
                list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
            }
            $m->Disconnect();
        }
    } else {
        $query = "UPDATE jos_vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
        if (!$result = $bloomex->query($query)) {
            die(__FILE__ . ' ' . __LINE__ . 'There was an error running the updating order with wh query [' . $bloomex->error . ']');
        }
    }
}
