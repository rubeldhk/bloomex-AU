<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* This is the default Basket Template. Modify as you like.
*
* @version $Id: basket_b2c.html.php,v 1.3.2.1 2006/02/27 19:41:42 soeren_nb Exp $
* @package VirtueMart
* @subpackage templates
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
?>
<form action="<?php echo $action_url ?>" method="post">
<div class="msgReport" id="msgReport" style="display:none;">&nbsp;</div>
<table width="96%" cellspacing="3" cellpadding="5" border="0">
  <tr align="left" class="sectiontableheader">
	<th width="20%"><?php echo $VM_LANG->_PHPSHOP_CART_NAME ?></th>
	<th width="20%">Image</th>
	<th width="10%"><?php echo $VM_LANG->_PHPSHOP_CART_SKU ?></th>
	<th width="10%" style="text-align:center;"><?php echo $VM_LANG->_PHPSHOP_CART_PRICE ?></th>
	<th width="10%" style="text-align:center;"><?php echo $VM_LANG->_PHPSHOP_CART_QUANTITY ?></th>
	<th width="10%" style="text-align:center;"><?php echo $VM_LANG->_PHPSHOP_CART_SUBTOTAL ?></th>
	<th width="10%" style="text-align:center;" colspan="2" align="center"><?php echo $VM_LANG->_PHPSHOP_CART_ACTION ?></th>
  </tr>
<?php foreach( $product_rows as $product ) { ?>
  <tr valign="top" class="<?php echo $product['row_color'] ?>" id="cartItem_<?php echo $product['product_id']?>">
    <td style="text-align:left;"><?php echo $product['product_name'] . $product['product_attributes'] ?></td>
    <td style="text-align:center;"><center><?php echo $product['product_thumb_pic'] ?></center></td>
    <td><?php echo $product['product_sku'] ?></td>
    <td style="text-align:center;"><?php echo $product['product_price'] ?></td>
    <td style="text-align:center;"><?php echo $product['quantity_box'] ?></td>
    <td style="text-align:center;"><?php echo $product['subtotal'] ?></td>
    <td style="text-align:center;" colspan="2">
	    <?php echo $product['update_form'] ?>
	    <?php echo $product['delete_form'] ?>
	    <div id="msgReportBlock_<?php echo $product['product_id']?>"  class="msgReportBlock"></div>
    </td>
  </tr>
<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
  <tr class="sectiontableentry2">
    <td colspan="5" align="right"><?php echo $VM_LANG->_PHPSHOP_CART_SUBTOTAL ?>:</td> 
    <td colspan="3"><span id="totalSubPrice"><?php echo $subtotal_display ?></span></td>
  </tr>
<?php if( $discount_before ) { ?>
  <tr class="sectiontableentry1">
    <td colspan="5" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:
    </td> 
    <td colspan="3"><span id="totalCouponDiscount"><?php echo $coupon_display ?></span></td>
  </tr>
<?php } 
if( $shipping ) { ?>
  <tr class="sectiontableentry1">
	<td colspan="5" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING ?>: </td> 
	<td colspan="3"><?php echo $shipping_display ?></td>
  </tr>
<?php } 
if($discount_after) { ?>
  <tr class="sectiontableentry1">
    <td colspan="5" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:
    </td> 
    <td colspan="3"><?php echo $coupon_display ?></td>
  </tr>
<?php } ?>
  <tr>
    <td colspan="4">&nbsp;</td>
    <td colspan="5"><hr /></td>
  </tr>
  <!--<?php if ( $show_tax ) { ?>
  <tr class="sectiontableentry2">
	<td colspan="5" align="right" valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX ?>: </td> 
	<td colspan="3"><span id="totalTax"><?php echo $tax_displayzz ?></span></td>
  </tr>
<?php } ?> -->

  <tr>
    <td colspan="5" align="right"><strong><?php echo $VM_LANG->_PHPSHOP_CART_TOTAL ?>:</strong></td>
    <td colspan="3"><strong><span id="totalPrice"><?php echo $order_total_display; ?></span></strong>
    </td>
  </tr>
  <tr>
    <td colspan="8"><hr /></td>
  </tr>
</table>

<input type="hidden" name="sub_total_price" value="<?php echo floatval(floatval(preg_replace( "/[^\d\.]/i", "", strtolower($total)))); ?>"/>
<input type="hidden" name="total_tax" value="<?php echo floatval(preg_replace( "/[^\d\.]/i", "", strtolower($tax_display))); ?>"/>
<input type="hidden" name="coupon_discount" value="<?php echo ( isset($_SESSION['coupon_discount']) ? doubleval($_SESSION['coupon_discount']) : 0); ?>"/>
</form>