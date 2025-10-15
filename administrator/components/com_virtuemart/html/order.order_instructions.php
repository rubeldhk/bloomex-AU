<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 * @version $Id: order.order_printdetails.php,v 1.7 2005/05/10 18:45:04 soeren_nb Exp $
 * @package mambo-phpShop
 * @subpackage HTML
 * Contains code from PHPShop(tm):
 * 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
 * 	Community: www.phpshop.org, forums.phpshop.org
 * Conversion to Mambo and the rest:
 * 	@copyright (C) 2004-2005 Soeren Eberhardt
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * mambo-phpShop is Free Software.
 * mambo-phpShop comes with absolute no warranty.
 *
 * www.mambo-phpshop.net
 */
mm_showMyFileName(__FILE__);
global $database;

require_once(CLASSPATH . 'ps_checkout.php');
require_once(CLASSPATH . 'ps_product.php');
$ps_product = new ps_product;
?>
<style>
    #loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url(/images/103.gif) center no-repeat #fff;
    }
</style>
<div id="loader"></div>

<script type="text/javascript">
    window.onload = function() {
                document.getElementById('loader').style.display = "none";
            };
</script>
<?php
$orders = mosgetparam($_REQUEST, 'order_id', null);
$dbc = new ps_DB;
$orders = explode(",", $orders);
$lastElement = end($orders);
foreach($orders as $order_id){
if (!is_numeric($order_id))
    die('Please provide a valid Order ID!');

$q = "SELECT * FROM #__{vm}_orders WHERE order_id='$order_id'";
$db->query($q);

if ($db->next_record()) {
    if($my->prevs->warehouse_only &&( $my->prevs->warehouse_only!=$db->f("warehouse"))) {
     die('Order is not assigned to your warehouse');
}
// Get ship_to information
    
    $shipping_info = new ps_DB;
    $query = "SELECT * from jos_vm_order_user_info WHERE order_id = {$order_id} ORDER BY address_type, order_info_id ASC LIMIT 1,1";
    $shipping_info->query($query);
    $shipping_info->next_record();

    $billing_info = new ps_DB;
    $query = "SELECT * from jos_vm_order_user_info WHERE order_id = {$order_id} ORDER BY address_type, order_info_id ASC LIMIT 0,1";
    $billing_info->query($query);
    $billing_info->next_record();

    $query = "SELECT #__vm_product.*, #__vm_order_item.product_quantity AS product_quantity FROM #__vm_product, #__vm_order_item WHERE #__vm_product.product_id = #__vm_order_item.product_id AND #__vm_order_item.order_id = '" . $order_id . "' ORDER BY order_item_id ASC";
    $database->setQuery($query);
    $product = $database->loadObjectList();
    $sIngredientList = "";
    if (count($product)) {
        $k = 0;
        foreach ($product as $item) {
            if ($item->product_name) {
                $sIngredientList .= str_replace("\n", "<br/>", "<b>" . $item->product_name . "</b>") . "<br/>";
                $sIngredientList .= "<b>SKU:</b> " . $item->product_sku . "<br/><br/>";
                $sIngredientList .= "<b>QTY:</b> " . $item->product_quantity . "<br/><br/>";
                if ($k == count($product) - 1) {
                    $sIngredientList .= str_replace("\n", "<br/>", $item->ingredient_list);
                } else {
                    $sIngredientList .= str_replace("\n", "<br/>", $item->ingredient_list) . "<br/><br/>";
                }
                $k++;
            }
        }
    }
    ?>
    <br/>
    <style type="text/css">
        body { width: 100%; margin: 0; float: none; vertical-align:top; }
    </style>
    
                        <h3 style="text-align:center;vertical-align:top;"><strong><?php echo "Special Instructions"; ?></strong></h3><br>
                        <p style="text-align:left;"><?php $db->p("customer_comments"); ?></p>
    
    
<!--    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" valign="top">    
        <tr>
            <td width="50%" valign="top" align="left" style="text-align:left;padding:0px 74px 0px 0px;">	         -->
                <!--<table width="90%" cellspacing="0" cellpadding="5" border="0" align="center" valign="top">-->
<!--                    <tr> 
                        <td>&nbsp;</td>
                        <td style="text-align:left;">	          	 
                            <img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo $VM_LANG->_VM_BARCODE_PREFIX . "-" . sprintf("%08d", trim($db->f("order_id"))); ?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" />
                            <br/>
                            <br/>
                            <br/>
                        </td>
                    </tr> 
                    <tr>
                        <td style="text-align:right;vertical-align:top;"><strong>Delivery Time: </strong></td>
                        <td><b><?php
    $details = explode("|", $db->f("ship_method_id"));
    echo $details[2];
    ?></b>              
                        </td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_USER_FORM_DELIVERYDATE; ?>:</strong></td>
                        <td style="text-align:left;"><b><?php echo $db->f("ddate"); ?></b></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_TYPE_LABEL; ?>:</strong></td>
                        <td style="text-align:left;">
                            <?php
                            if ($shipping_info->f("address_type2") == "B") {
                                echo "Business";
                            } elseif ($shipping_info->f("address_type2") == "R") {
                                echo "Residential";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr> 
                        <td width="40%" style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?>:</strong></td>
                        <td style="text-align:left;" width="60%"><?php $shipping_info->p("company"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?>:</strong></td>
                        <td style="text-align:left;">
                            <?php
                            $shipping_info->p("first_name");
                            echo " ";
                            $shipping_info->p("middle_name");
                            echo " ";
                            $shipping_info->p("last_name");
                            ?>
                        </td>
                    </tr>
                                        <?php if ($shipping_info->f("suite")) { ?>
                        <tr > 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_SUITE ?> :</strong></td>
                            <td style="text-align:left;"><?php
                $shipping_info->p("suite");
                        ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($shipping_info->f("street_number")) { ?>
                        <tr > 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_STREET_NUMBER ?> :</strong></td>
                            <td style="text-align:left;"><?php
                $shipping_info->p("street_number");
                        ?></td>
                        </tr>
                        <tr > 
                            <td style="text-align:right;"><strong>&nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_STREET_NAME ?> :</strong></td>
                            <td style="text-align:left;"><?php
                        $shipping_info->p("street_name");
                        ?></td>
                        </tr>         
                    <?php } else { ?>
                        <tr valign="top"> 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</strong></td>
                            <td style="text-align:left;"><?php
                $shipping_info->p("address_1");
                echo "<br />";
                $shipping_info->p("address_2");
                        ?></td>
                        </tr> 
                    <?php } ?>


                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("city"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;white-space:nowrap;"><strong>Province:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("state_name"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("zip"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("country_name"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("phone_1"); ?></td>
                    </tr>	       
                    <tr> 
                        <td style="text-align:right;vertical-align:top;"><strong>Product list:</strong></td>
                        <td style="text-align:left;"><?php echo $sIngredientList; ?></td>
                    </tr>-->
<!--                    <tr> 
                        <td style="text-align:right;vertical-align:top;"><strong><?php echo "Special Instructions"; ?>:</strong></td>
                        <td style="text-align:left;"><?php $db->p("customer_comments"); ?></td>
                    </tr>
                </table>-->
<!--            </td>
            <td width="50%" valign="top" align="right" style="text-align:right;padding:162px 0px 0px 74px;">	  

                <table width="90%" cellspacing="0" cellpadding="5" border="0" align="center" valign="top">
                    <tr>
                        <td  align="center" colspan="2">
                            <div style="text-align:center;display:block;width:100%;">
                                <b><?php
                if ($db->f("customer_note")) {
                    echo str_replace("\\", "", nl2br(htmlspecialchars_decode($db->f("customer_note"))));
                } else {
                    echo " ";
                }
                    ?></b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" colspan="2">
                            <div style="text-align:center;display:block;width:100%;">
                                <b><?php
                                if ($db->f("customer_signature")) {
                                    echo str_replace("\\", "", nl2br(htmlspecialchars_decode($db->f("customer_signature"))));
                                } else {
                                    echo " ";
                                }
                    ?></b>
                            </div>
                            <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        </td>
                    </tr>
                    <tr> 
                        <td width="40%" style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_USER_FORM_DELIVERYDATE ?>:</strong></td>
                        <td width="60%" style="text-align:left;"><b><?php echo $db->f("ddate"); ?></b></td>
                    </tr>            
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER ?>:</strong></td>
                        <td style="text-align:left;"><b><?php printf("%08d", $db->f("order_id")); ?></b></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_TYPE_LABEL; ?>:</strong></td>
                        <td style="text-align:left;">
                            <?php
                            if ($shipping_info->f("address_type2") == "B") {
                                echo "Business";
                            } elseif ($shipping_info->f("address_type2") == "R") {
                                echo "Residential";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("company"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?>:</strong></td>
                        <td style="text-align:left;"><?php
                        $shipping_info->p("first_name");
                        echo " ";
                        $shipping_info->p("middle_name");
                        echo " ";
                        $shipping_info->p("last_name");
                            ?></td>
                    </tr>
                    <?php if ($shipping_info->f("suite")) { ?>
                        <tr > 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_SUITE ?> :</strong></td>
                            <td style="text-align:left;"><?php
                $shipping_info->p("suite");
                        ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($shipping_info->f("street_number")) { ?>
                        <tr > 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_STREET_NUMBER ?> :</strong></td>
                            <td style="text-align:left;"><?php
                $shipping_info->p("street_number");
                        ?></td>
                        </tr>
                        <tr > 
                            <td style="text-align:right;"><strong>&nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_STREET_NAME ?> :</strong></td>
                            <td style="text-align:left;"><?php
                        $shipping_info->p("street_name");
                        ?></td>
                        </tr>         
                    <?php } else { ?>
                        <tr valign="top"> 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</strong></td>
                            <td style="text-align:left;"><?php
                $shipping_info->p("address_1");
                echo "<br />";
                $shipping_info->p("address_2");
                        ?></td>
                        </tr> 
                    <?php } ?>

                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("city"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;white-space:nowrap;"><strong>Province:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("state_name"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("zip"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("country_name"); ?></td>
                    </tr>
                    <tr> 
                        <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>:</strong></td>
                        <td style="text-align:left;"><?php $shipping_info->p("phone_1"); ?></td>
                    </tr>
                    <tr> 
                        <td>&nbsp;</td>
                        <td style="text-align:left;">
                            <br/>
                            <br/>
                            <img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo $VM_LANG->_VM_BARCODE_PREFIX . "-" . sprintf("%08d", trim($db->f("order_id"))); ?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" />
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                            <br/>			          
                        </td>
                    </tr> 
                    <tr>
                        <td colspan="2"><div style="text-align:center;display:block;width:100%;"><b><?php $db->p("customer_comments"); ?></b></div></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>-->
    <?php
}
?>


    <?php if($order_id != $lastElement) { ?>
        <div class="hr_order"></div>
    <?php } ?>
<style>
    .hr_order {
        width: 100%;
        background-color: black;
        height: 1px;
    }
    @media print {
    .hr_order {page-break-after: always;}
}
</style>
        <?php

                    }
?>
   