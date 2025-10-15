
<?php
/**
 * @version $Id: contact.html.php 4157 2006-07-02 17:58:51Z stingrey $
 * @package Joomla
 * @subpackage Contact
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Contact
 */
class HTML_page_not_found {



    function view($products_obj)
    {
        global $VM_LANG,$mosConfig_live_site,$mosConfig_absolute_path,$sess;
        require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
        ?>
<script>
        window.onload = function(e){
        document.title="404 Error Page - SORRY PRODUCT NOT CURRENTLY AVAILABLE";
        jQuery('meta[name=description]').remove();
        jQuery('meta[name=keywords]').remove();
        jQuery('head').append( '<meta name="description" content="Send 404 Error Page Online - Same Day 404 Error Page Delivery. Order 404 Error Page Online and Discount 404 Error Page Bloomex">' );
        jQuery('head').append( '<meta name="keywords" content="404 Error Page,delivery,send">' );
        }
</script>
<div id="product-list" class="product-list-items">
            <?php
            if ($products_obj){
                foreach ($products_obj as $product_obj) {

                    $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
                    $product_rating = round($product_obj->rating, 1);

                            $sBtnImage = "button.png";

                    $url = '?option=com_virtuemart&page=shop.product_details&category_id='.$product_obj->category_id.'&flypage=shop.flypage&product_id='.$product_obj->product_id;
                    $sess = new ps_session;

                    if (isset($sess) && strpos($url, 'com_virtuemart')) {
                        $url = $sess->url($url);
                    } else {
                        $url = sefRelToAbs($url);
                    }


                    ?>
                    <div class="product-list" price_ordering="<?php echo $product_real_price; ?>" rating_ordering="<?php echo $product_rating; ?>">
                        <a class="product-title" href="<?php echo $url; ?>"><?php echo $product_obj->product_name; ?></a>
                        <span class="sku-code"><?php echo $product_obj->product_sku; ?></span>
                        <span class="price">$<?php echo $product_real_price; ?></span>
                        <div class="product-image">
                            <a href="<?php echo $url; ?>">
                                <div class="product_image_loader"></div>
                                <img style="display: none;" class="product_<?php echo $product_obj->product_id;?> product_image_real" src="<?php echo $mosConfig_live_site; ?>/components/com_virtuemart/shop_image/product/<?php echo $product_obj->product_thumb_image; ?>" height="262" border="0" alt="<?php echo $product_obj->product_name; ?>">
                            </a>
                        </div>
                        <div class="form-add-cart" id="div_<?php echo $product_obj->product_id; ?>">
                            <form action="<?php echo $mosConfig_live_site; ?>/index.php" method="post" name="addtocart" id="formAddToCart_<?php echo $product_obj->product_id; ?>">
                                <input name="quantity_<?php echo $product_obj->product_id; ?>" class="inputbox" type="hidden" size="3" value="1">

                                <?php
                                if($product_obj->product_real_price ) {
                                    ?>
                                    <img alt="Add to Cart" style="cursor:pointer;" align="absbottom" class="add-to-cart" name="<?php echo $product_obj->product_id; ?>" src="components/com_virtuemart/shop_image/ps_image/<?php echo $sBtnImage; ?>" width="100" height="33" border="0">
                                    <?php
                                }else{
                                    ?>
                                    <a href='tel:1-888-912-5666'><p class='call_for_pricing'><?php echo $VM_LANG->_PHPSHOP_PRODUCT_CALL;?></p></a>
                                    <?php
                                }
                                ?>
                                <input type="hidden" name="category_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_id; ?>">
                                <input type="hidden" name="product_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_id; ?>">
                                <input type="hidden" name="price_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_real_price; ?>">
                            </form>
                        </div>
                        <?php
                        for ($i = 1; $i <= 4; $i++) {
                            ?>
                            <img src="/templates/bloomex7/images/star.png" alt="star" />
                            <?php
                        }
                        if ($product_rating <= 4.5) {
                            ?>
                            <img src="/templates/bloomex7/images/star_half.png" alt="star-half" />
                            <?php
                        }
                        else {
                            ?>
                            <img src="/templates/bloomex7/images/star.png" alt="star" />
                            <?php
                        }
                        ?>
                        <span style="font-weight: bold;"><?php echo $product_rating; ?></span>/5<br>based on<span style="font-weight: bold;"><?php echo $product_obj->review_count; ?></span>Customer Reviews
                    </div>
                    <?php
                }
            }
            ?>
        </div>
  <?php  } } ?>
