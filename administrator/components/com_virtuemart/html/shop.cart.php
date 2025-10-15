<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.cart.php,v 1.3.2.4 2006/04/21 17:05:17 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivat ive of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

$manufacturer_id = mosGetParam( $_REQUEST, 'manufacturer_id');

//$mainframe->setPageTitle( $VM_LANG->_PHPSHOP_CART_TITLE );
$mainframe->setPageTitle2('SHOPPING CART SUMMARY');
$mainframe->addMetaTag('description', 'SHOPPING CART SUMMARY');

global $VM_LANG, $iso_client_lang, $database,$mosConfig_checkout_specials,$mosConfig_enable_fast_checkout,$mosConfig_extra_touches_cat;

$_SESSION['url_coupon_code'] = (isset($_GET['coupon_code']) AND !empty($_GET['coupon_code'])) ? $_GET['coupon_code'] : '';

$fhid	= !empty($_REQUEST["fhid"]) ? intval($_REQUEST["fhid"]) : 0;
$cobrand	= !empty($_REQUEST["cobrand"]) ? trim($_REQUEST["cobrand"]) : "";
$pid		= !empty($_REQUEST["pid"]) ? intval($_REQUEST["pid"]) : 0;
if( $fhid ) {
    $_SESSION['funeral']['FHID'] = $fhid;	
    $_SESSION['funeral']['PID'] = $pid;	
    $_SESSION['funeral']['COBRAND'] = $cobrand;	
}

$continue_link = '';
if( !empty( $category_id)) {
    $continue_link = $sess->url( $_SERVER['PHP_SELF'].'?page=shop.browse&amp;category_id='.$category_id );
}
elseif( empty( $category_id) && !empty($product_id)) {
    $db->query( 'SELECT `category_id` FROM `#__{vm}_product_category_xref` WHERE `product_id`='.intval($product_id) );
    $db->next_record();
    $category_id = $db->f('category_id');
    $continue_link = $sess->url( $_SERVER['PHP_SELF'].'?page=shop.browse&amp;category_id='.$category_id );
}
elseif( !empty( $manufacturer_id )) {
    $continue_link = $sess->url( $_SERVER['PHP_SELF'].'?page=shop.browse&amp;manufacturer_id='.$manufacturer_id );
}

$show_basket = true;
?>
<?php if(isset($_REQUEST['msg'])){ ?>
    <div class="container">
        <div class="row alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_REQUEST['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php } ?>
<!-- Cart Begins here s-->
<div class="container">
    <div class="row">
        <div class="col-12 <?php if ($cart['idx'] > 0) {
            echo 'col-lg-12';
        } ?>">
            <div class="cart_wrapper">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 padding0">
                        <h3>SHOPPING CART SUMMARY</h3>
                        <?php

                        include_once PAGEPATH . 'basket.php'; ?>
                    </div>
                </div>
                <?php
                if ($cart['idx'] > 0) {

                    ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 buttons basket_buttons">
                            <a class="continue" href="/"><?php echo $VM_LANG->_PHPSHOP_CONTINUE_SHOPPING; ?></a>
                            <a class="proceed" href="/checkout/">
                                <?php echo $VM_LANG->_PHPSHOP_PROCEED_CHECKOUT; ?>
                            </a>
                            <?php
                            if ($mosConfig_enable_fast_checkout) {
                                echo ' <a class="proceed_fast" href="/fast-checkout/">' . $VM_LANG->_PHPSHOP_CLICK_TO_PAY . ' </a>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <!-- End Cart -->
        <?php
        if ($cart['idx']) {

            $LIMIT = 12;

            $nExtraTouchesCategory = implode(',', $mosConfig_extra_touches_cat);

            $sql = "SELECT 
        DISTINCT `P`.`product_sku`,  
        `P`.`product_name`, 
        `P`.`product_id`, 
        `P`.`product_desc`, 
        `P`.`product_thumb_image`, 
        `P`.`product_full_image`, 
        `VMP`.`product_price`,
        `VMP`.`saving_price`
    FROM `jos_vm_product` AS `P` 
    LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`P`.`product_id`
    INNER JOIN `jos_vm_product_category_xref` AS `PCX` ON `P`.`product_id`=`PCX`.`product_id` 
    INNER JOIN `jos_vm_category` AS `C` ON `C`.`category_id`=`PCX`.`category_id` 
    INNER JOIN `jos_vm_product_price` AS `VMP` ON `P`.`product_id`=`VMP`.`product_id` 
    WHERE `C`.category_id IN ( $nExtraTouchesCategory ) AND `P`.`product_publish`='Y' and `po`.`product_sold_out`!=1 and `po`.`product_out_of_season`!=1
    ORDER BY RAND() LIMIT $LIMIT";
            $database->setQuery($sql);
            $related_products = $database->loadObjectList();

            shuffle($related_products);
            $products = array();

            foreach ($related_products as $product_obj) {
                $products[] = $product_obj->product_id;
            }
            ?>
            <div class="col-12 col-lg-12">
                <div class="add-ons">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h3>Enhance Your Gift - Special Add-Ons</h3>
                            <h5>Make your gesture even more special with these delightful extras.</h5>
                            <?php
                            echo $ps_product->show_product_list_mini($products);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="quantity_string" value=""/>
            <?php
        }
        ?>
    </div>
</div>