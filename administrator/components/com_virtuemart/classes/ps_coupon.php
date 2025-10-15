<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: ps_coupon.php,v 1.5.2.1 2005/12/01 20:00:32 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage classes
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

/**
 * Class Code for coupon codes
 * The author would like to thank Digitally Imported (www.di.fm) for good music to code to
 *
 *
 * CHANGELOG:
 *
 * v 1.0: Initial Release (28-NOV-2004) - Erich
 */
class ps_coupon {

    function validate_add(&$d) {
        global $VM_LANG, $vmLogger;
        /* init the database */
        $coupon_db = new ps_DB;
        $valid = true;

        /* make sure the coupon_code does not exist */
        $q = "SELECT coupon_code FROM #__{vm}_coupons WHERE coupon_code = '" . $d['coupon_code'] . "' ";
        $coupon_db->query($q);
        if ($coupon_db->next_record()) {
            $vmLogger->err($VM_LANG->_PHPSHOP_COUPON_CODE_EXISTS);
            $valid = false;
        }
        if (empty($d['coupon_value']) || empty($d['coupon_code'])) {
            $vmLogger->warning($VM_LANG->_PHPSHOP_COUPON_COMPLETE_ALL_FIELDS);
            $valid = false;
        }
        if (!is_numeric($d['coupon_value'])) {
            $vmLogger->err($VM_LANG->_PHPSHOP_COUPON_VALUE_NOT_NUMBER);
            $valid = false;
        }
        return $valid;
    }

    function validate_update(&$d) {
        global $VM_LANG;
        /* init the database */
        $coupon_db = new ps_DB;
        $valid = true;

        /* make sure the coupon_code does not exist */
        $q = "SELECT coupon_code FROM #__{vm}_coupons WHERE coupon_code = '" . $d['coupon_code'] . "' AND coupon_id <> '" . $d['coupon_id'] . "'";
        $coupon_db->query($q);
        if ($coupon_db->next_record()) {
            $d["error"] = $VM_LANG->_PHPSHOP_COUPON_CODE_EXISTS;
            $valid = false;
        }
        if (empty($d['coupon_value']) || empty($d['coupon_code'])) {
            $d['error'] .= $VM_LANG->_PHPSHOP_COUPON_COMPLETE_ALL_FIELDS;
            $valid = false;
        }
        if (!is_numeric($d['coupon_value'])) {
            $d['error'] .= $VM_LANG->_PHPSHOP_COUPON_VALUE_NOT_NUMBER;
            $valid = false;
        }
        return $valid;
    }

    /* function to add a coupon coupon_code to the database */

    function add_coupon_code(&$d) {
        global $my;

        $coupon_db = new ps_DB;

        if (!$this->validate_add($d)) {
            return false;
        }

        $q = "INSERT INTO #__{vm}_coupons ( coupon_code, percent_or_total, coupon_type, coupon_value, create_date, edit_date, username,expiry_date  ) ";
        $q .= "VALUES ( '" . $d['coupon_code'] . "', '" . $d['percent_or_total'] . "', '" . $d['coupon_type'] . "', '" . $d['coupon_value'] . "', NOW(), NOW(), '".$my->username."', '" . $d['expiry_date'] . "' ) ";
        $coupon_db->query($q);
        return true;
    }

    /* $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ */

    /* function to update a coupon */

    function update_coupon(&$d) {
        global $my;
        if (!$this->validate_update($d)) {
            return false;
        }
        /* init the database */
        $coupon_db = new ps_DB;

        $q = "UPDATE #__{vm}_coupons SET ";
        $q .= "coupon_code = '" . $d["coupon_code"] . "', ";
        $q .= "percent_or_total = '" . $d["percent_or_total"] . "', ";
        $q .= "coupon_type = '" . $d["coupon_type"] . "', ";
        $q .= "coupon_value = '" . $d["coupon_value"] . "', ";
        $q .= "expiry_date = '" . $d["expiry_date"] . "', ";
        $q .= "edit_date = NOW(),";
        $q .= "username = '".$my->username."'";
        $q .= "WHERE coupon_id = '" . $d['coupon_id'] . "'";
        $coupon_db->query($q);

        return true;
    }

    /* $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ */

    /* function to remove coupon coupon_code from the database */

    function remove_coupon_code(&$d) {

        /* remove the coupon coupon_code */
        /* init the database */
        $coupon_db = new ps_DB;
        if (is_array($d['coupon_id'])) {
            foreach ($d['coupon_id'] as $coupon) {
                $q = "DELETE FROM #__{vm}_coupons WHERE coupon_id = '$coupon' ";
                $coupon_db->query($q);
            }
        } else {
            $q = "DELETE FROM #__{vm}_coupons WHERE coupon_id = '" . $d['coupon_id'] . "' ";
            $coupon_db->query($q);
        }
        $_SESSION['coupon_discount'] = 0;
        $_SESSION['coupon_redeemed'] = false;

        return true;
    }

    function process_coupon_code($d) {
        global $VM_LANG, $database;
        /* init the database */
        $coupon_db = new ps_DB;

        /* we need some functions from the checkout module */
        require_once( CLASSPATH . "ps_checkout.php" );
        $checkout = new ps_checkout();
        $d['coupon_code'] = trim(mosGetParam($_REQUEST, 'coupon_code'));
        $coupon_id = mosGetParam($_SESSION, 'coupon_id', null);
        $product_coupon_discount = mosGetParam($_SESSION, 'product_coupon_discount', null);
        $outdated_coupon = false;


        $bProductCoupon = false;
        $nSubtotalProductEffectDiscount = 0;
        $nProductCouponDiscount = 0;
        if (!empty($d['coupon_code'])) {
            if (substr_count($d['coupon_code'], 'PC-')) {
                $query = "SELECT coupon_code FROM #__vm_orders WHERE coupon_code='{$d['coupon_code']}' AND (UNIX_TIMESTAMP() - 31536000) > cdate";
                $database->setQuery($query);
                $result = $database->loadObjectList();
                if ($result && isset($result[0])) {
                    $query = "DELETE FROM #__vm_coupons WHERE coupon_code='{$d['coupon_code']}'";
                    $database->setQuery($query);
                    $database->query();
                    $outdated_coupon = true;
                }
            }
            $query = " SELECT * FROM #__vm_product_discount WHERE discount_type = 'coupon' AND coupon_code = '" . $d['coupon_code'] . "' LIMIT 1";
            $database->setQuery($query);
            $coupon_code = $database->loadObjectList();

            //        	die("11111111111111111111111111111");
            if (count($coupon_code)) {
                if (!empty($coupon_code[0]->discount_id)) {
                    if (($coupon_code[0]->start_date <= 0 && $coupon_code[0]->end_date <= 0) || ( time() > $coupon_code[0]->start_date && time() < $coupon_code[0]->end_date)) {
                        $query = " SELECT product_id FROM #__vm_product WHERE product_coupon_discount = '" . $d['coupon_code'] . "'";
                        $database->setQuery($query);
                        $product_coupon = $database->loadObjectList();
                        //echo ($query);
                        //print_r($product_coupon);

                        if (count($product_coupon) && count($_SESSION['cart'])) {
                            foreach ($product_coupon as $product_item) {
                                for ($i = 0; $i < $_SESSION['cart']["idx"]; $i++) {
                                    if (($_SESSION['cart'][$i]["product_id"] == $product_item->product_id)) {
                                        $_SESSION['cart'][$i]["product_coupon_discount"] = $d['coupon_code'];
                                        $bProductCoupon = true;
                                        $nSubtotalProductEffectDiscount += $_SESSION['cart'][$i]["price"];
                                    }
                                }
                            }
                        }

                        if ($bProductCoupon) {
                            $_SESSION['coupon_redeemed'] = true;
                            $_SESSION['coupon_id'] = "";
                            $_SESSION['coupon_type'] = "product_coupon_discount";

                            $_SESSION['coupon_code'] = $d['coupon_code'];
                            $_SESSION['coupon_value'] = $coupon_code[0]->amount;

                            if ($coupon_code[0]->is_percent) {
                                $_SESSION['coupon_code_type'] = "percent";
                                $nProductCouponDiscount = ($nSubtotalProductEffectDiscount * $_SESSION['coupon_value'] ) / 100;
                            } else {
                                $_SESSION['coupon_code_type'] = "total";
                                $nProductCouponDiscount = $_SESSION['coupon_value'];
                            }

                            if ($nProductCouponDiscount >= $nSubtotalProductEffectDiscount) {
                                $_SESSION['product_coupon_discount_value'] = $nSubtotalProductEffectDiscount;
                            } else {
                                $_SESSION['product_coupon_discount_value'] = $nProductCouponDiscount;
                            }
                        } else {
                            $bProductCoupon = false;
                        }
                    }
                } else {
                    $bProductCoupon = false;
                }
            } else {
                $bProductCoupon = false;
            }
        }

        $bOrderCoupon = false;
        if (!$bProductCoupon) {
            /* print_r($d);
              die(doubleval(preg_replace("/[^0-9,\.]/", '', $d["cart_total"]))); */
            if (!empty($coupon_id)) {
                /* the query to select the coupon coupon_code */
                $q = "SELECT coupon_id, percent_or_total, coupon_value, coupon_type FROM #__{vm}_coupons WHERE coupon_id = '" . $coupon_id . "' ";
            } else {
                /* the query to select the coupon coupon_code */
                $q = "SELECT coupon_id, percent_or_total, coupon_value, coupon_type FROM #__{vm}_coupons WHERE coupon_code = '" . $d['coupon_code'] . "' ";
            }

            /* make the query */
            $coupon_db->query($q);

            /* see if we have any fields returned */
            if ($coupon_db->num_rows() > 0) {
                /* we have a record */
                /* see if we are calculating percent or dollar discount */
                if ($coupon_db->f("percent_or_total") == "percent") {
                    /* percent */
                    //$subtotal = $checkout->calc_order_subtotal( $d );

                    /* take the subtotal for calculation of the discount */
                    //$_SESSION['coupon_discount'] = round( ($subtotal * $coupon_db->f("coupon_value") / 100), 2);
                    $coupon_discount = round((doubleval($d["total"]) * $coupon_db->f("coupon_value") / 100), 2);
                } else {
                    /* dollar */
                    $coupon_discount = ($coupon_db->f("coupon_value"));
                }

                if ($coupon_discount >= floatval($d["total"])) {
                    $_SESSION['coupon_discount'] = floatval($d["total"]);
                } else {
                    $_SESSION['coupon_discount'] = $coupon_discount;
                }

                /* mark this order as having used a coupon so people cant go and use coupons over and over */
                $_SESSION['coupon_redeemed'] = true;
                $_SESSION['coupon_id'] = $coupon_db->f("coupon_id");
                $_SESSION['coupon_type'] = $coupon_db->f("coupon_type");

                $_SESSION['coupon_code'] = $d['coupon_code'];
                $_SESSION['coupon_code_type'] = $coupon_db->f("percent_or_total");
                $_SESSION['coupon_value'] = $coupon_db->f("coupon_value");

                $bOrderCoupon = true;
            } else {
                $bProductCoupon = false;
            }
        }

        if (!$bProductCoupon && !$bProductCoupon) {
            /* no record, so coupon_code entered was not valid */
            if ($outdated_coupon) {
                $_REQUEST['coupon_error'] = 'Sorry, your one year Platinum Club membership has already expired. You can buy another one <a href="/index.php?page=shop.product_details&flypage=shop.flypage&product_id=869&category_id=82&option=com_virtuemart">here</a>.';
            } else {
                $_REQUEST['coupon_error'] = $VM_LANG->_PHPSHOP_COUPON_CODE_INVALID;
            }
            return false;
        }

        return true;
    }

}

?>