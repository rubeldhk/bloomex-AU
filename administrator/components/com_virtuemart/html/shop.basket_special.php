<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
*Special modification of shop.basket_short.php
*@author: SirArthur
*@author email: sirarthur@sirarthur.info
*
*/
mm_showMyFileName( __FILE__ );

require_once(CLASSPATH. 'ps_product.php' );
$ps_product =new  ps_product;
require_once(CLASSPATH. 'ps_shipping_method.php' );
require_once(CLASSPATH. 'ps_checkout.php' );
$ps_checkout =new  ps_checkout;

global $CURRENCY_DISPLAY, $VM_LANG, $vars;

$cart = $_SESSION['cart'];
$auth = $_SESSION['auth'];
  if ($cart["idx"] == 0) {
     echo $VM_LANG->_PHPSHOP_EMPTY_CART;
     echo " <img src=\"images/cart_empty.png\" border=\"0\" title=\"Your cart is empty\">";
     $checkout = false;
  }
  else {
    $checkout = True;
$scriptout = "";
    $total = $order_taxable = $order_tax = 0;
    $amount = 0;
    $weight_total = 0;
    for ($i=0;$i<$cart["idx"];$i++) {

      $price = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"],$cart[$i]["description"]);
      $amount += $cart[$i]["quantity"];
      $pname = $ps_product->get_field($cart[$i]["product_id"], "product_name");

      if (@$auth["show_price_including_tax"] == 1) {
        $my_taxrate = $ps_product->get_product_taxrate($cart[$i]["product_id"] );
        $price["product_price"] *= ($my_taxrate+1);
      }
      $subtotal = round( $price["product_price"], 2 ) * $cart[$i]["quantity"];
      $scriptout .= $cart[$i]["quantity"] . "x $pname " . number_format($subtotal,2,'.',',') . " CAD";
      if($i < $cart['idx'] - 1) $scriptout .= "<br>";
      $total += $subtotal;

      $weight_subtotal = ps_shipping_method::get_weight($cart[$i]["product_id"]) * $cart[$i]["quantity"];
      $weight_total += $weight_subtotal;
    }
    
    if( !empty($_SESSION['coupon_discount']) ) {
        $total -= $_SESSION['coupon_discount'];
    }
    
    if ($amount > 1) 
      echo $amount ." ". $VM_LANG->_PHPSHOP_PRODUCTS_LBL;
    else
      echo $amount ." ". $VM_LANG->_PHPSHOP_PRODUCT_LBL;
     echo "<br />";
    echo $CURRENCY_DISPLAY->getFullValue( $total );
    echo "<div id=\"bubble\"><a title=\"$scriptout\"><img src=\"images/cart_full.png\" border=\"0\"></a></div>";
  }
?>
