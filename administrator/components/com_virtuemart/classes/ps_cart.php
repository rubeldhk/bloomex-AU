<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: ps_cart.php,v 1.12.2.4 2006/05/06 10:05:26 soeren_nb Exp $
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
 * CLASS DESCRIPTION
 *                   
 * ps_cart
 *
 * The cart class is used to store products and carry them through the user's
 * session in the store.
 * properties:  
 * 	item() - an array of items
 *       idx - the current count of items in the cart
 *       error - the error message returned by validation if any
 * methods:
 *       add()
 *       update()
 *       delete()
 * *********************************************************************** */
class ps_cart {

    var $classname = "ps_cart";

    /**
     * Calls the constructor
     *
     * @return array An empty cart
     */
    static function initCart() {
        global $my, $cart, $sess;
        // Register the cart
        if (empty($_SESSION['cart'])) {
            $cart = array();
            $cart['idx'] = 0;
            $_SESSION['cart'] = $cart;
            return $cart;
        } else {
            if (( @$_SESSION['auth']['user_id'] != $my->id ) && empty($my->id) && @$_GET['cartReset'] != 'N') {
                // If the user ID has changed (after logging out)
                // empty the cart!
                $sess->emptySession();
                ps_cart::reset();
            }
        }
        return $_SESSION['cart'];
    }

    /**
     * adds an item to the shopping cart
     * @author pablo
     * @param array $d
     */
    function add2(&$d) {
        global $sess, $VM_LANG, $cart, $option, $vmLogger, $database, $my;
        $addToCart = TRUE;
        foreach ($cart as $cartKey => $cartProduct) {
            if ($cartKey != 'idx') {
                foreach ($cartProduct as $key => $val) {
                    if ($key == 'product_id' && $val == $d['product_id']) {
                        $addToCart = FALSE;
                    }
                }
            }
        }
        if ($addToCart == TRUE) {
            $this->add($d);
        }
    }

    function add(&$d) {
        global $sess, $VM_LANG, $cart, $option, $vmLogger, $database, $my;

        include_class("product");

        $Itemid = intval(mosgetparam($_REQUEST, "Itemid", null));
        $product_id = $d["product_id"];
        $quantity = isset($d["quantity"]) ? $d["quantity"] : 1;
        $_SESSION['last_page'] = "shop.product_details";
        $db = new ps_DB;

        // Check for negative quantity
        if ($quantity < 0) {
            $vmLogger->warning($VM_LANG->_PHPSHOP_CART_ERROR_NO_NEGATIVE);
            return False;
        }

        if (!preg_match("/^[0-9]*$/", $quantity)) {
            $vmLogger->warning($VM_LANG->_PHPSHOP_CART_ERROR_NO_VALID_QUANTITY);
            return False;
        }


        // Check to see if checking stock quantity
        if (CHECK_STOCK) {
            $q = "SELECT product_in_stock ";
            $q .= "FROM #__{vm}_product where product_id='$product_id'";
            $db->query($q);
            $db->next_record();
            $product_in_stock = $db->f("product_in_stock");
            if (empty($product_in_stock)) {
                $product_in_stock = 0;
            }
            if ($quantity > $product_in_stock) {
                $msg = $VM_LANG->_PHPSHOP_CART_STOCK_1;
                eval("\$msg .= \"" . $VM_LANG->_PHPSHOP_CART_STOCK_2 . "\";");

                $vmLogger->tip($msg);
                $GLOBALS['page'] = 'shop.waiting_list';
                return true;
            }
        }

        // Quick add of item
        $q = "SELECT product_id FROM #__{vm}_product WHERE ";
        $q .= "product_parent_id = '" . $d['product_id'] . "'";
        $db->query($q);

        if ($db->num_rows()) {
            $vmLogger->tip($VM_LANG->_PHPSHOP_CART_SELECT_ITEM);
            return false;
        }

        // If no quantity sent them assume 1
        if ($quantity == "")
            $quantity = 1;


        // Check to see if we already have it
        $updated = 0;

        $result = ps_product_attribute::cartGetAttributes($d);

        if (($result["attribute_given"] == false && !empty($result["advanced_attribute_list"])) || ($result["custom_attribute_given"] == false && !empty($result["custom_attribute_list"]))) {
            $_REQUEST['flypage'] = ps_product::get_flypage($product_id);
            $GLOBALS['page'] = 'shop.product_details';
            $vmLogger->tip($VM_LANG->_PHPSHOP_CART_SELECT_ITEM);
            return true;
        }

        // Check for duplicate and do not add to current quantity
        for ($i = 0; $i < $_SESSION["cart"]["idx"]; $i++) {
            // modified for advanced attributes
            if ($_SESSION['cart'][$i]["product_id"] == $product_id &&
                    $_SESSION['cart'][$i]["description"] == $d["description"]
            ) {
                $updated = 1;
            }
        }

        //CALCULATE SHOPPER GROUP DISCOUNT	
        $ShopperGroupDiscount = 0;
        $query = " SELECT SG.shopper_group_discount 
						FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id  	
						WHERE  SVX.user_id = " . $my->id . " LIMIT 1";
        $database->setQuery($query);
        $ShopperGroupDiscount = $database->loadResult();

        $nShopperGroupDiscount = 0;
        if (!empty($ShopperGroupDiscount)) {
            if (!empty($ShopperGroupDiscount) && $ShopperGroupDiscount > 0) {
                $nShopperGroupDiscount = floatval($ShopperGroupDiscount) / 100;
            }
        }
        //print_r($ShopperGroupDiscount);
        //echo $query."<br/><br/>";
        // add extra touch products start
        if ($d['extra_touches']) {

            $product_ides_arr = explode('&', $d['extra_touches']);
            foreach ($product_ides_arr as $p) {
                $product_id_extra_arr = explode('=', $p);
                $product_id_extra = $product_id_extra_arr[1];

                $q = "SELECT  not_apply_discount  FROM #__vm_product WHERE product_id = '" . $product_id_extra . "'";
                $database->setQuery($q);
                $not_apply_discount = $database->loadResult();
                $product_price = 0;
                //IMPLEMENT #5055
                $aPrice = ps_product::get_retail_price($product_id_extra);
                $my_taxrate = ps_product::get_product_taxrate($product_id_extra);
                if (!empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0) {
                    $product_price = (($aPrice["product_price"] - $aPrice["saving_price"]) + ($aPrice["product_price"]) * $my_taxrate);
                } else {
                    $product_price = $aPrice["product_price"] + $aPrice["product_price"] * $my_taxrate;
                }

                //$product_price 	= floatval($product_price)  - ( floatval($product_price) * floatval($nShopperGroupDiscount) );  
                $product_price = floatval($product_price);

                $updated_extra = 0;
                for ($i = 0; $i < $_SESSION["cart"]["idx"]; $i++) {
                    // modified for advanced attributes
                    if ($_SESSION['cart'][$i]["product_id"] == $product_id_extra) {
                        $_SESSION['cart'][$i]["quantity"]++;
                        $updated_extra = 1;
                    } else {
                        $updated_extra = 0;
                    }
                }
                if (!$updated_extra) {

                    $k = $_SESSION['cart']["idx"];
                    $_SESSION['cart'][$k]["price"] = $product_price;
                    $_SESSION['cart'][$k]["quantity"] = 1;
                    $_SESSION['cart'][$k]["product_id"] = $product_id_extra;
                    $_SESSION['cart'][$k]["description"] = '';
                    $_SESSION['cart'][$k]["not_apply_discount"] = intval($not_apply_discount);
                    $_SESSION['cart'][$k]["apply_group_discount"] = floatval($nShopperGroupDiscount);
                    $_SESSION['cart'][$k]["pick_up"] = 0;
                    $_SESSION['cart'][$k]["select_bouquet"] = 'standard';
                    $_SESSION['cart']["idx"]++;
                }
            }
        }
        // add extra touch products end
        // If we did not update then add the item
        if (!$updated) {
            $q = "SELECT  not_apply_discount  FROM #__vm_product WHERE product_id = '" . $product_id . "'";
            $database->setQuery($q);
            $not_apply_discount = $database->loadResult();

            $product_price = 0;
            //IMPLEMENT #5055
            $aPrice = ps_product::get_retail_price($product_id);
            $my_taxrate = ps_product::get_product_taxrate($product_id);
            $bloomex_reg_price = ($aPrice['product_price'] - $aPrice['saving_price']);
            $product_price = number_format($bloomex_reg_price, 2, '.', '');

            $product_price = floatval($product_price);


            $select_bouquet = isset($d['select_bouquet']) ? $d['select_bouquet'] : 'standard';
            $sql = "SELECT " . $database->getEscaped($select_bouquet) . " FROM #__vm_product_options WHERE product_id = $product_id LIMIT 1";
            $database->setQuery($sql);
            $bouquet_add_price = $database->loadResult();
            $product_price_standard = $product_price;
            $product_price += $bouquet_add_price;



            $k = $_SESSION['cart']["idx"];
            $_SESSION['cart'][$k]["price"] = $product_price;
            $_SESSION['cart'][$k]["quantity"] = $quantity;
            $_SESSION['cart'][$k]["product_id"] = $product_id;
            $_SESSION['cart'][$k]["select_bouquet"] = $select_bouquet;
            $_SESSION['cart'][$k]["price_standard"] = $product_price_standard;
            // added for the advanced attribute modification
            $_SESSION['cart'][$k]["description"] = $d["description"];
            $_SESSION['cart'][$k]["not_apply_discount"] = intval($not_apply_discount);
            $_SESSION['cart'][$k]["apply_group_discount"] = floatval($nShopperGroupDiscount);
            $_SESSION['cart']["idx"]++;
        } else {
            $this->update($d);
        }



        /* next 3 lines added by Erich for coupon code */
        /* if the cart was updated we gotta update any coupon discounts to avoid ppl getting free stuff */
        if (!empty($_SESSION['coupon_discount'])) {
            // Update the Coupon Discount !!
            $_POST['do_coupon'] = 'yes';
        }

        $cart = $_SESSION['cart'];

        @setcookie("just_change", "1", time() + 3600);

        if (mosGetParam($_REQUEST, "action", "") == "ajax") {
            if (count($cart)) {
                $nTotalItem = 0;
                $nTotalPrice = 0;
                $nSubTotalPrice = 0;

                foreach ($cart as $item) {
                    if (intval($item["product_id"])) {
                        $query = " SELECT VTR.tax_rate 
										FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
										ON VM.product_id = VMP.product_id 
										LEFT JOIN  #__vm_tax_rate AS VTR 
										ON VM.product_tax_id = VTR.tax_rate_id 
										WHERE VM.product_id = " . intval($item["product_id"]);
                        $database->setQuery($query);
                        $tax_value = $database->loadResult();

                        $nTotalPrice += ( $item['price'] * $item['quantity'] * $tax_value ) + $item['price'] * $item['quantity'];
                        $nSubTotalPrice += $item['price'] * $item['quantity'];
                        $nTotalItem += $item['quantity'];
                    }
                }
            }

            if ($nTotalItem) {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCTS_LBL;
            } else {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCT_LBL;
            }
            $nTotalPrice = LangNumberFormat::number_format(round($nTotalPrice, 2), 2, ",", "");

            if ($nTotalItem) {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCTS_LBL;
            } else {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCT_LBL;
            }


//			if( isset($_SESSION["coupon_id"]) ) {
//				if( intval($_SESSION["coupon_id"]) ) {
//					$query		= " SELECT percent_or_total, coupon_value FROM #__vm_coupons WHERE coupon_id = ".intval($_SESSION["coupon_id"]);
//					$database->setQuery($query);
//					$rows		= $database->loadObjectList();
//					$coupon	= $rows[0];
//					if( $coupon->percent_or_total == "percent" ) {
//						$nCoupon						= $nSubTotalPrice * ( floatval($coupon->coupon_value) / 100 );
//						$_SESSION["coupon_discount"]	= $nCoupon;
//					}elseif( $coupon->percent_or_total == "total" ) {
//						$_SESSION["coupon_discount"]	= floatval($coupon->coupon_value);
//					}
//				}
//			}
            if ($nTotalItem == 1) {
                $query_must_be_combined = " SELECT must_be_combined
							FROM  #__vm_product_options WHERE product_id = " . intval($product_id);
                $database->setQuery($query_must_be_combined);
                $must_be_combined = $database->loadResult();


                if ($must_be_combined == 1) {
                    $notice = 'The product must be combined. Add one more product.';
                }
            }
            echo "success[--1--]{$nTotalItem} $sResult,<br/>{$nTotalPrice}[--2--]{$notice}";
            require_once 'end_access_log.php';
            die();
        }

        return True;
    }

    /**
     * updates the quantity of a product_id in the cart
     * @author pablo
     * @param array $d
     * @return boolean result of the update
     */
    function update(&$d) {
        global $sess, $VM_LANG, $func, $vmLogger, $database, $CURRENCY_DISPLAY;
        $product_coupon_discount = isset($d['product_coupon_discount']) ? $d['product_coupon_discount'] : "";
        $action_discount = isset($d['action_discount']) ? $d['action_discount'] : "";
        $product_id = isset($d['product_id']) ? $d['product_id'] : "";


        include_class("product");

        $db = new ps_DB;
        $product_id = $d["product_id"];
        $quantity = !empty($d["quantity"]) ? $d["quantity"] : 1;
        $_SESSION['last_page'] = "shop.cart";

        // Check for negative quantity
        if ($quantity < 0) {
            $vmLogger->warning($VM_LANG->_PHPSHOP_CART_ERROR_NO_NEGATIVE);
            return False;
        }

        if (!preg_match("/^[0-9]*$/", $quantity)) {
            $vmLogger->warning($VM_LANG->_PHPSHOP_CART_ERROR_NO_VALID_QUANTITY);
            return False;
        }

        if (!$product_id) {
            return false;
        }

        if ($quantity == 0) {
            $this->delete($d);
        } else {
            $d["description"] = isset($d["description"]) ? $d["description"] : "";
            for ($i = 0; $i < $_SESSION['cart']["idx"]; $i++) {
                // modified for the advanced attribute modification
                if (($_SESSION['cart'][$i]["product_id"] == $product_id ) && ($_SESSION['cart'][$i]["description"] == stripslashes($d["description"]) )) {
                    if (strtolower($func) == 'cartadd') {
                        $quantity += $_SESSION['cart'][$i]["quantity"];
                    }
                    if ((isset($_REQUEST['cart_products']) AND!empty($_REQUEST['cart_products']))) {
                        $quantity += $_SESSION['cart'][$i]["quantity"];
                    }
                    // Check to see if checking stock quantity
                    if (CHECK_STOCK) {
                        $q = "SELECT product_in_stock ";
                        $q .= "FROM #__{vm}_product where product_id=";
                        $q .= $product_id;
                        $db->query($q);
                        $db->next_record();
                        $product_in_stock = $db->f("product_in_stock");
                        if (empty($product_in_stock))
                            $product_in_stock = 0;
                        if (($quantity) > $product_in_stock) {
                            $msg = $VM_LANG->_PHPSHOP_CART_STOCK_1;
                            eval("\$msg .= \"" . $VM_LANG->_PHPSHOP_CART_STOCK_2 . "\";");

                            $vmLogger->tip($msg);

                            if (mosGetParam($_REQUEST, "action", "") == "ajax") {
                                echo "error[--1--]{$VM_LANG->_PHPSHOP_CART_STOCK_2}";
                                require_once 'end_access_log.php';
                                die();
                            }

                            $page = 'shop.waiting_list';
                            return true;
                        }
                    }
                    $_SESSION['cart'][$i]["quantity"] = $quantity;

                    if ($d['select_bouquet']) {
                        $select_bouquet = $d['select_bouquet'] ? $d['select_bouquet'] : 'standard';
                        $_SESSION['cart'][$i]["select_bouquet"] = $select_bouquet;
                        global ${$select_bouquet};
                        $product_price = ${$select_bouquet} + $_SESSION['cart'][$i]["price_standard"];
                        $_SESSION['cart'][$i]["price"] = $product_price;
                    }
                }
            }
        }


        if (mosGetParam($_REQUEST, "action", "") == "ajax") {
            if (count($_SESSION['cart'])) {
                global $database;
                $nTotalItem = 0;
                $nTotalTax = 0;
                $nSubTotalPrice = 0;
                $nProductCouponDiscount = 0;
                $nTotalNotApplyDiscount = 0;

                foreach ($_SESSION['cart'] as $item) {
                    $nSubTotalOneProductItem = 0;
                    $nCouponDiscountOneProductItem = 0;
                    if (intval($item["product_id"])) {
                        $query = " SELECT VTR.tax_rate 
										FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
										ON VM.product_id = VMP.product_id 
										LEFT JOIN  #__vm_tax_rate AS VTR 
										ON VM.product_tax_id = VTR.tax_rate_id 
										WHERE VM.product_id = " . intval($item["product_id"]);
                        $database->setQuery($query);
                        $tax_value = $database->loadResult();


                        if (!empty($item["product_coupon_discount"])) {
                            $query = " SELECT * FROM #__vm_product_discount WHERE  discount_type = 'coupon' AND coupon_code = '" . $item["product_coupon_discount"] . "'";
                            $database->setQuery($query);
                            $coupon_code = $database->loadObjectList();

                            if ($coupon_code[0]->is_percent) {
                                $nCouponDiscountOneProductItem = ( floatval($item['price']) * ($coupon_code[0]->amount / 100) ) * $item['quantity'];
                            } else {
                                $nCouponDiscountOneProductItem = floatval($coupon_code[0]->amount) * $item['quantity'];
                            }
                        }


                        $nSubTotalOneProductItem = floatval($item['price'] * $item['quantity']);

                        if ($nCouponDiscountOneProductItem >= $nSubTotalOneProductItem) {
                            $nCouponDiscountOneProductItem = $nSubTotalOneProductItem;
                        }

                        if (!empty($item['not_apply_discount'])) {
                            $nTotalNotApplyDiscount += $nSubTotalOneProductItem;
                        }

                        $nProductCouponDiscount += $nCouponDiscountOneProductItem;
                        $nTotalTax += $nSubTotalOneProductItem * $tax_value;
                        $nSubTotalPrice += $nSubTotalOneProductItem;
                        $nTotalItem += $item['quantity'];
                    }
                }
            }

            if ($nTotalItem) {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCTS_LBL;
            } else {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCT_LBL;
            }

            $sTotalTax = LangNumberFormat::number_format(round($nTotalTax, 2), 2, ",", "");
            $sSubTotalPrice = LangNumberFormat::number_format(round($nSubTotalPrice, 2), 2, ",", "");
            $nSubTotalPriceBFDiscount = round($nSubTotalPrice, 2);

            $_SESSION["coupon_id"] = isset($_SESSION["coupon_id"]) ? $_SESSION["coupon_id"] : "";
            $_SESSION["coupon_type"] = isset($_SESSION["coupon_type"]) ? $_SESSION["coupon_type"] : "";
            $sCoupon = "";
            $nCoupon = 0;

            if (intval($_SESSION["coupon_id"]) && $_SESSION["coupon_type"] != "product_coupon_discount") {
                $query = " SELECT percent_or_total, coupon_value FROM #__vm_coupons WHERE coupon_id = " . intval($_SESSION["coupon_id"]);
                $database->setQuery($query);
                $rows = $database->loadObjectList();
                $coupon = $rows[0];

                if (trim($coupon->percent_or_total) == "percent") {
                    $nCoupon = ($nSubTotalPrice - $nTotalNotApplyDiscount) * ( floatval($coupon->coupon_value) / 100 );
                } elseif (trim($coupon->percent_or_total) == "total") {
                    $nCoupon = floatval($coupon->coupon_value);
                }

                if ($nCoupon >= ($nSubTotalPrice - $nTotalNotApplyDiscount)) {
                    $nCoupon = ($nSubTotalPrice - $nTotalNotApplyDiscount);
                }
                $sCoupon = LangNumberFormat::number_format(round($nCoupon, 2), 2, ",", "");

                $_SESSION["coupon_discount"] = $nCoupon;
                $nSubTotalPrice = $nSubTotalPrice - $nCoupon;
            } else {
                if ($nProductCouponDiscount) {
                    $nCoupon = $nProductCouponDiscount;
                    $sCoupon = LangNumberFormat::number_format(round($nProductCouponDiscount, 2), 2, ",", "");

                    $nSubTotalPrice = $nSubTotalPrice - $nCoupon;
                    $_SESSION['product_coupon_discount_value'] = $nProductCouponDiscount;
                }
            }

            //$nTotalPrice	= $nSubTotalPrice + $nTotalTax;	
            $sTotalPrice = LangNumberFormat::number_format(round($nSubTotalPrice, 2), 2, ",", "");

            //echo "success[--1--]{$nTotalItem} $sResult,<br/>{$sTotalPrice}[--1--]{$sTotalPrice}[--1--]{$nTotalPrice}[--1--]{$sSubTotalPrice}[--1--]{$nTotalPrice}[--1--]{$sTotalTax}[--1--]{$nTotalTax}[--1--]{$sCoupon}[--1--]{$nCoupon}";				
            echo "success[--1--]{$nTotalItem} $sResult,<br/>{$sTotalPrice}[--1--]{$sTotalPrice}[--1--]{$nSubTotalPrice}[--1--]{$sSubTotalPrice}[--1--]{$nSubTotalPrice}[--1--]{$sTotalTax}[--1--]{$nTotalTax}[--1--]{$sCoupon}[--1--]{$nCoupon}[--1--]{$nSubTotalPriceBFDiscount}";
            require_once 'end_access_log.php';
            die();
        }
    }

    /**
     * deletes a given product_id from the cart
     *
     * @param array $d
     * @return boolan Result of the deletion
     */
    function delete($d) {
        global $VM_LANG;
        $temp = array();
        $product_id = isset($d["product_id"]) ? $d["product_id"] : 0;
        $product_desc = isset($d["description"]) ? $d["description"] : "";


        if (!$product_id) {
            $_SESSION['last_page'] = "shop.cart";
            return False;
        }


        $j = 0;
        for ($i = 0; $i < $_SESSION['cart']["idx"]; $i++) {
            // modified for the advanced attribute modification			
            if (is_array($_SESSION['cart'])) {
                $cart_product_id = isset($_SESSION['cart'][$i]["product_id"]) ? $_SESSION['cart'][$i]["product_id"] : 0;
                $cart_description = isset($_SESSION['cart'][$i]["description"]) ? $_SESSION['cart'][$i]["description"] : "";

                if ($cart_product_id != $product_id || $cart_description != stripslashes($product_desc)) {
                    $temp[$j++] = $_SESSION['cart'][$i];
                }

                //REMOVE PRODUCT COUPON IF PRODUCT HAD COUPON REMOVED
                if ($cart_product_id == $product_id && !empty($_SESSION['cart'][$i]["product_coupon_discount"])) {
                    $aM = array('coupon_id', 'coupon_type', 'coupon_discount', 'coupon_code', 'coupon_value', 'coupon_code_type', 'coupon_redeemed', 'product_coupon_discount_value');
                    foreach ($aM as $am_t) {
                        unset($_SESSION[$am_t]);
                    }
                }
            }
        }
        $temp["idx"] = $j;
        $_SESSION['cart'] = $temp;


        if (mosGetParam($_REQUEST, "action", "") == "ajax") {
            if (count($_SESSION['cart'])) {
                global $database;
                $nTotalItem = 0;
                $nTotalTax = 0;
                $nSubTotalPrice = 0;
                $nProductCouponDiscount = 0;

                foreach ($_SESSION['cart'] as $item) {
                    $nSubTotalOneProductItem = 0;
                    $nCouponDiscountOneProductItem = 0;

                    if (intval($item["product_id"])) {
                        $query = " SELECT VTR.tax_rate 
										FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP 
										ON VM.product_id = VMP.product_id 
										LEFT JOIN  #__vm_tax_rate AS VTR 
										ON VM.product_tax_id = VTR.tax_rate_id 
										WHERE VM.product_id = " . intval($item["product_id"]);
                        $database->setQuery($query);
                        $tax_value = $database->loadResult();

                        if (!empty($item["product_coupon_discount"])) {
                            $query = " SELECT * FROM #__vm_product_discount WHERE  discount_type = 'coupon' AND coupon_code = '" . $item["product_coupon_discount"] . "'";
                            $database->setQuery($query);
                            $coupon_code = $database->loadObjectList();

                            if ($coupon_code[0]->is_percent) {
                                $nCouponDiscountOneProductItem = ( floatval($item['price']) * ($coupon_code[0]->amount / 100) ) * $item['quantity'];
                            } else {
                                $nCouponDiscountOneProductItem = floatval($coupon_code[0]->amount) * $item['quantity'];
                            }
                        }


                        $nSubTotalOneProductItem = floatval($item['price'] * $item['quantity']);

                        if ($nCouponDiscountOneProductItem >= $nSubTotalOneProductItem) {
                            $nCouponDiscountOneProductItem = $nSubTotalOneProductItem;
                        }

                        if (!empty($item['not_apply_discount'])) {
                            $nTotalNotApplyDiscount += $nSubTotalOneProductItem;
                        }

                        $nProductCouponDiscount += $nCouponDiscountOneProductItem;
                        $nTotalTax += $nSubTotalOneProductItem * $tax_value;
                        $nSubTotalPrice += $nSubTotalOneProductItem;
                        $nTotalItem += $item['quantity'];
                    }
                }
            }

            if ($nTotalItem) {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCTS_LBL;
            } else {
                $sResult = $VM_LANG->_PHPSHOP_PRODUCT_LBL;
            }


            $sTotalTax = LangNumberFormat::number_format(round($nTotalTax, 2), 2, ",", "");
            $sSubTotalPrice = LangNumberFormat::number_format(round($nSubTotalPrice, 2), 2, ",", "");
            $nSubTotalPriceBFDiscount = round($nSubTotalPrice, 2);

            $sCoupon = "";
            $nCoupon = 0;
            if (intval($_SESSION["coupon_id"]) && $_SESSION["coupon_type"] != "product_coupon_discount") {
                $query = " SELECT percent_or_total, coupon_value FROM #__vm_coupons WHERE coupon_id = " . intval($_SESSION["coupon_id"]);
                $database->setQuery($query);
                $rows = $database->loadObjectList();
                $coupon = $rows[0];

                if (trim($coupon->percent_or_total) == "percent") {
                    $nCoupon = ($nSubTotalPrice - $nTotalNotApplyDiscount) * ( floatval($coupon->coupon_value) / 100 );
                } elseif (trim($coupon->percent_or_total) == "total") {
                    $nCoupon = floatval($coupon->coupon_value);
                }

                if ($nCoupon >= ($nSubTotalPrice - $nTotalNotApplyDiscount)) {
                    $nCoupon = ($nSubTotalPrice - $nTotalNotApplyDiscount);
                }
                $sCoupon = LangNumberFormat::number_format(round($nCoupon, 2), 2, ",", "");

                $_SESSION["coupon_discount"] = $nCoupon;
                $nSubTotalPrice = $nSubTotalPrice - $nCoupon;
            } else {
                if ($nProductCouponDiscount) {
                    $nCoupon = $nProductCouponDiscount;
                    $sCoupon = LangNumberFormat::number_format(round($nProductCouponDiscount, 2), 2, ",", "");

                    $nSubTotalPrice = $nSubTotalPrice - $nCoupon;
                    $_SESSION['product_coupon_discount_value'] = $nProductCouponDiscount;
                }
            }

            //$nTotalPrice	= $nSubTotalPrice + $nTotalTax;	
            $sTotalPrice = LangNumberFormat::number_format(round($nSubTotalPrice, 2), 2, ",", "");

            echo "success[--1--]{$nTotalItem} $sResult,<br/>{$sTotalPrice}[--1--]{$sTotalPrice}[--1--]{$nSubTotalPrice}[--1--]{$sSubTotalPrice}[--1--]{$nSubTotalPrice}[--1--]{$sTotalTax}[--1--]{$nTotalTax}[--1--]{$sCoupon}[--1--]{$nCoupon}[--1--]{$nSubTotalPriceBFDiscount}";
            //echo "success[--1--]{$nTotalItem} $sResult,<br/>{$sTotalPrice}[--1--]{$sTotalPrice}[--1--]{$nTotalPrice}[--1--]{$sSubTotalPrice}[--1--]{$nTotalPrice}[--1--]{$sTotalTax}[--1--]{$nTotalTax}[--1--]{$sCoupon}[--1--]{$nCoupon}";
            require_once 'end_access_log.php';
            die();
        }

        return True;
    }

    /**
     * Empties the cart
     * @author pablo
     * @return boolean true
     */
    static function reset() {
        global $cart;
        $_SESSION['cart'] = array();
        $_SESSION['cart']["idx"] = 0;
        $cart = $_SESSION['cart'];
        return True;
    }

}

?>