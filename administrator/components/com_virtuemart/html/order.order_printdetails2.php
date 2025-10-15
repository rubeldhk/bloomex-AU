<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: order.order_printdetails.php,v 1.5.2.2 2006/03/10 15:55:15 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
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
mm_showMyFileName(__FILE__);

require_once(CLASSPATH . 'ps_checkout.php');
require_once(CLASSPATH . 'ps_product.php');
$ps_product = new ps_product;

$orders= mosgetparam($_REQUEST, 'order_id', null);
$dbc = new ps_DB;
$dbs = new ps_DB;

$orders = explode(",", $orders);
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
$lastElement = end($orders);
foreach($orders as $order_id){
if (!is_numeric($order_id))
    die('Please provide a valid Order ID!');

$q = "SELECT * FROM #__{vm}_orders AS O, #__{vm}_order_occasion AS OC WHERE O.order_id='$order_id' AND O.customer_occasion = OC.order_occasion_code";

$db->query($q);
if (!$db->next_record())
{
    $q = "SELECT * FROM #__{vm}_orders AS O WHERE O.order_id='$order_id'";
    $db->query($q);
    $db->next_record();
}

if($my->prevs->warehouse_only &&( $my->prevs->warehouse_only!=$db->f("warehouse"))) {
     die('Order is not assigned to your warehouse');
}

    $queryPartner = "SELECT * FROM `jos_vm_api2_orders` ao "
        . "where ao.order_id = $order_id";
    $order = false;
    $db->setQuery($queryPartner);
    $partner = $db->loadResult();
?>

<!--Start formatting for envelope-->


<table border="0" cellspacing="0" cellpadding="2" width="100%">
    <!-- begin customer information --> 
    <tr class="sectiontableheader"> 
        <th align="left"><?php echo $partner ? 'GST 137-361-566' : 'ABN: 27 147 609 443' ; ?> <br/><br/><?php echo $VM_LANG->_PHPSHOP_ACC_ORDER_INFO ?></th>
        <th align="left"><img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo $VM_LANG->_VM_BARCODE_PREFIX . "-" . sprintf("%08d", trim($db->f("order_id"))); ?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" /><br/><br/></th>
    </tr>
    <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER ?>:</td>
        <td><?php printf("%08d", $db->f("order_id")); ?></td>
    </tr>
     <tr> 
        <td>Tax Invoice:</td>
        <td><?php printf("%08d", $db->f("order_id")); ?></td>
    </tr>
    <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_DATE ?>:</td>
        <td><?php echo date("d-M-Y H:i", $db->f("cdate")); ?></td>
    </tr>
    <tr> 
        <td><b>Delivery Date:</b></td>
        <td><b><?php echo date($db->f("ddate")); ?></B></td>
    </tr>

    <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_STATUS ?>:</td>
        <td><?php
$q = "SELECT order_status_name FROM #__{vm}_order_status WHERE order_status_code = '" . $db->f("order_status") . "'";
$dbos = new ps_DB;
$dbos->query($q);
$dbos->next_record();
echo $dbos->f("order_status_name");
?>

        </td>
    </tr>
    <!-- End Customer Information --> 
    <!-- Begin 2 column bill-ship to --> 
    <tr class="sectiontableheader"> 
        <th align="left" colspan="2"><br/><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_INFO_LBL ?></th>
    </tr>
    <tr valign="top"> 
        <td width="50%"> 
            <!-- Begin BillTo -->
            <?php
            // Get bill_to information
            $dbbt = new ps_DB;
            $q = "SELECT * FROM #__{vm}_order_user_info WHERE  order_id='$order_id' ORDER BY address_type, order_info_id ASC";
            $dbbt->query($q);
            $dbbt->next_record();
            ?> 
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr> 
                    <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_BILL_TO_LBL ?></strong></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> :</td>
                    <td><?php $dbbt->p("company"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> :</td>
                    <td><?php
            $dbbt->p("first_name");
            echo " ";
            $dbbt->p("middle_name");
            echo " ";
            $dbbt->p("last_name");
            ?></td>
                </tr>
                <?php if ($dbbt->f("suite")) { ?>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_SUITE ?> :</td>
                        <td><?php
                    $dbbt->p("suite");
                    ?></td>
                    </tr>
                <?php } ?>
                <?php if ($dbbt->f("street_number")) { ?>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_STREET_NUMBER ?> :</td>
                        <td><?php
                $dbbt->p("street_number");
                    ?></td>
                    </tr>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_STREET_NAME ?> :</td>
                        <td><?php
                        $dbbt->p("street_name");
                    ?></td>
                    </tr>         
                <?php } else { ?>
                    <tr valign="top"> 
                        <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</strong></td>
                        <td><?php
                $dbbt->p("address_1");
                echo "<br />";
                $dbbt->p("address_2");
                    ?></td>
                    </tr>
                <?php } ?>

                <tr> 
                    <td>District :</td>
                    <td><?php $dbbt->p("district"); ?></td>
                </tr>
                <tr>
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
                    <td><?php $dbbt->p("city"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?> :</td>
                    <td><?php
                $dbs->query("SELECT state_name FROM #__{vm}_state WHERE state_2_code = '" . $dbbt->f("state") . "'");
                $dbs->next_record();
                echo $dbs->f("state_name");
                ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?> :</td>
                    <td><?php $dbbt->p("zip"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?> :</td>
                    <td><?php
                        $country = $dbbt->f("country");
                        $dbc->query("SELECT country_name FROM #__{vm}_country WHERE country_3_code = '$country'");
                        $dbc->next_record();
                        $country_name = $dbc->f("country_name");
                        echo $country_name;
                ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?> :</td>
                    <td><?php $dbbt->p("phone_1"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?> :</td>
                    <td><?php $dbbt->p("fax"); ?></td>
                </tr>
                <?php if($my->prevs->warehouse_only == false){ ?>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?> :</td>
                    <td><?php $dbbt->p("user_email"); ?></td>
                </tr>     
                <?php } ?>
            </table>
            <!-- End BillTo --> </td>
        <td width="50%"> <!-- Begin ShipTo --> <?php
                        // Get ship_to information
                        $dbbt->next_record();
                        $dbst = & $dbbt;
                ?> 
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr> 
                    <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIP_TO_LBL ?></strong></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> :</td>
                    <td><?php $dbst->p("company"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> :</td>
                    <td><?php
            $dbst->p("first_name");
            echo " ";
            $dbst->p("middle_name");
            echo " ";
            $dbst->p("last_name");
                ?></td>
                </tr>
                <?php if ($dbbt->f("suite")) { ?>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_SUITE ?> :</td>
                        <td><?php
                $dbbt->p("suite");
                    ?></td>
                    </tr>
                <?php } ?>
                <?php if ($dbbt->f("street_number")) { ?>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_STREET_NUMBER ?> :</td>
                        <td><?php
                $dbbt->p("street_number");
                    ?></td>
                    </tr>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_STREET_NAME ?> :</td>
                        <td><?php
                        $dbbt->p("street_name");
                    ?></td>
                    </tr>         
                <?php } else { ?>
                    <tr valign="top"> 
                        <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</strong></td>
                        <td><?php
                $dbbt->p("address_1");
                echo "<br />";
                $dbbt->p("address_2");
                    ?></td>
                    </tr>
                <?php } ?>       
                <tr> 
                    <td>District :</td>
                    <td><?php $dbst->p("district"); ?></td>
                </tr>
                <tr>
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
                    <td><?php $dbst->p("city"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?> :</td>
                    <td><?php
                $dbs->query("SELECT state_name FROM #__{vm}_state WHERE state_2_code = '" . $dbbt->f("state") . "' AND `country_id`=13");
                $dbs->next_record();
                echo $dbs->f("state_name");
                ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?> :</td>
                    <td><?php $dbst->p("zip"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?> :</td>
                    <td><?php
                        if ($country != $dbst->f("country")) {
                            $country = $dbst->f("country");
                            $dbc->query("SELECT country_name FROM #__{vm}_country WHERE country_3_code = '$country'");
                            $dbc->next_record();
                            $country_name = $dbc->f("country_name");
                        }
                        echo $country_name;
                ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?> :</td>
                    <td><?php $dbst->p("phone_1"); ?></td>
                </tr>
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE2 ?> :</td>
                    <td><?php $dbst->p("phone_2"); ?></td>
                </tr> 
                <tr> 
                    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?> :</td>
                    <td><?php $dbst->p("fax"); ?></td>
                </tr>          
            </table>
            <!-- End ShipTo --> 
            <!-- End Customer Information --> 
        </td>
    </tr>
    <tr> 
        <td colspan="2">&nbsp;</td>
    </tr>
    <?php if ($PSHOP_SHIPPING_MODULES[0] != "no_shipping" && $db->f("ship_method_id")) { ?> 
        <tr> 
            <td colspan="2"> 
                <table width="100%" border="0" cellspacing="0" cellpadding="1">

                    <tr class="sectiontableheader"> 
                        <th align="left"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL ?></th>
                    </tr>
                    <tr> 
                        <td> 
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr> 
                                    <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_CARRIER_LBL ?></strong></td>
                                    <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_MODE_LBL ?></strong></td>
                                    <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE ?>&nbsp;</strong></td>
                                </tr>
                                <tr> 
                                    <td><?php
    $details = explode("|", $db->f("ship_method_id"));
    echo $details[1];
        ?>&nbsp;
                                    </td>
                                    <td><?php
                                    echo $details[2];
        ?>
                                    </td>
                                    <td><?php
                                    echo $CURRENCY_DISPLAY->getFullValue($details[3]);
        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr><?php
                                }
    ?> 
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <!-- Begin Order Items Information --> 
    <tr class="sectiontableheader"> 
        <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_ITEM ?></th>
    </tr>
    <tr> 
        <td colspan="2"> 
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr align="left"> 
                    <th><b><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_QTY ?><b></th>
                                <th> <b> <?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_NAME ?></b></th>
                                <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SKU ?></th>
                                <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE ?></th>
                                <th align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL ?>&nbsp;&nbsp;&nbsp;</th>
                                </tr>
                                <?php
                                $dbcart = new ps_DB;
                                $q = "SELECT * FROM #__{vm}_order_item ";
                                $q .= "WHERE #__{vm}_order_item.order_id='$order_id' ";
                                $dbcart->query($q);
                                $subtotal = 0;
                                while ($dbcart->next_record()) {
                                    ?> 
                                    <tr align="left" valign="top"> 
                                        <td width="10%"><b><u> <?php $dbcart->p("product_quantity"); ?></u></b></td>
                                        <td width="45%"><b><u> <?php
                                $dbcart->p("order_item_name");
                                echo "</u></b>";

                                //echo " <font size=\"-2\">" . $dbcart->f("product_attribute") . "</font>";
                                    ?></td>
                                                    <td width="10%"><?php $dbcart->p("order_item_sku"); ?>
                                                    </td>
                                                    <td width="10%"><?php
                                                /*
                                                  $price = $ps_product->get_price($dbcart->f("product_id"));
                                                  $item_price = $price["product_price"]; */
                                                $item_price = $dbcart->f("product_item_price");
                                                echo $CURRENCY_DISPLAY->getFullValue($item_price);
                                    ?></td>
                                                    <td align="right" width="10%"><?php
                                                    $total = $dbcart->f("product_quantity") * $item_price;
                                                    $subtotal += $total;
                                                    echo $CURRENCY_DISPLAY->getFullValue($total);
                                    ?>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr><?php
                                                }
                                ?> 
                                                <tr> 
                                                    <td colspan="4" align="right">&nbsp;&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr> 
                                                    <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SUBTOTAL ?> :</td>
                                                    <td align="right"><?php echo $CURRENCY_DISPLAY->getFullValue($subtotal) ?>&nbsp;&nbsp;&nbsp;</td>
                                                </tr>
                                                <?php
                                                /* COUPON DISCOUNT */
                                                $coupon_discount = $db->f("coupon_discount");

                                                if ($coupon_discount > 0) {
                                                    $subtotal -= $coupon_discount;
                                                    ?>
                                                    <tr>
                                                        <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:
                                                        </td> 
                                                        <td align="right"><?php echo "- " . $CURRENCY_DISPLAY->getFullValue($coupon_discount); ?>&nbsp;&nbsp;&nbsp;
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }

                                                $dbcd = new ps_DB;
                                                $q  = "SELECT shopper_discount_value from jos_vm_orders_extra WHERE  order_id ='$order_id'";
                                                $dbcd->query($q);
                                                $dbcd->next_record();
                                                if($dbcd->f("shopper_discount_value")){
                                                    ?>
                                                    <tr>
                                                        <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_CART_CORPORATE_DISCOUNT ?> </td>
                                                        <td align="right">-<?php echo $CURRENCY_DISPLAY->getFullValue($dbcd->f("shopper_discount_value")); ?>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                <?php }
                                                ?>

                                                <tr> 
                                                    <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING ?> :</td>
                                                    <td align="right"><?php
                                                $shipping_total = $db->f("order_shipping");
                                                echo $CURRENCY_DISPLAY->getFullValue($shipping_total);
                                                ?>&nbsp;&nbsp;&nbsp;</td>
                                                </tr>
                                                <!--tr>
                                                    <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX ?> (included in total) :</td>
                                                    <td align="right"><?php
                                                        $tax_total = $db->f("order_tax") + $db->f("order_shipping_tax");
                                                        $tax_total = round($db->f("order_total")-($db->f("order_total")/1.1), 2);
                                                        echo $CURRENCY_DISPLAY->getFullValue($tax_total);
                                                ?>&nbsp;&nbsp;&nbsp;</td>
                                                </tr-->
                <?php
                // Get bill_to information
                $donation = new ps_DB;
                $q  = "SELECT donation_price FROM `tbl_used_donation` WHERE order_id='" . $db->f("order_id") . "'";
                $donation->query($q);
                $donation->next_record();
                $donation_price = $donation->f("donation_price");
                if ($donation_price) {
                    ?>
                    <tr>

                        <td colspan="4" align="right">Donation (not included into total) :</td>
                        <td align="right"><?php
                            echo $CURRENCY_DISPLAY->getFullValue($donation_price);
                            ?>&nbsp;&nbsp;
                        </td>
                    </tr>
                    <?php
                }

                $dbucks = new ps_DB;
                $q  = "SELECT * FROM `tbl_bucks_history` WHERE order_id='" . $db->f("order_id") . "'  and used_bucks!=''";
                $dbucks->query($q);
                $dbucks->next_record();
                $bucks = $dbucks->f("used_bucks");
                if ($bucks) {
                    ?>
                    <tr>

                        <td colspan="4" align="right">Used Bucks :</td>
                        <td align="right"><?php
                            echo '-'.$CURRENCY_DISPLAY->getFullValue($bucks);
                            ?>&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <?php
                }
                $dbcredit= new ps_DB;
                $q  = "SELECT credits FROM `jos_vm_users_credits_uses` WHERE order_id='" . $db->f("order_id") . "'  and credits!=''";
                $dbcredit->query($q);
                $dbcredit->next_record();
                $credits = $dbcredit->f("credits");
                if ($credits) {
                    ?>
                    <tr>

                        <td colspan="4" align="right">Used Credits :</td>
                        <td align="right"><?php
                            echo '-'.$CURRENCY_DISPLAY->getFullValue($credits);
                            ?>&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <?php
                }
                ?>
                                                <tr> 
                                                    <td colspan="4" align="right">
                                                        <?php if (PAYMENT_DISCOUNT_BEFORE == '1') { ?><strong><?php
                                                    }

                                                    echo $VM_LANG->_PHPSHOP_CART_TOTAL . ":";
                                                    if (PAYMENT_DISCOUNT_BEFORE != '1') {
                                                            ?></strong><?php } ?></td>

                                                    <td align="right"><?php if (PAYMENT_DISCOUNT_BEFORE == '1') { ?><strong><?php
                                                                $total = $db->f("order_total");
                                                                echo $CURRENCY_DISPLAY->getFullValue($total);
                                                            } else {
                                                                $total = $db->f("order_subtotal") + $db->f("order_tax") + $db->f("order_shipping");
                                                                echo $CURRENCY_DISPLAY->getFullValue($total);
                                                            }
                                                            if (PAYMENT_DISCOUNT_BEFORE == '1') {
                                                            ?></strong><?php } ?>&nbsp;&nbsp;&nbsp;</td>
                                                </tr>
                                                <?php if ($db->f("order_discount") != 0.00 && PAYMENT_DISCOUNT_BEFORE != '1') { ?>
                                                    <tr>
                                                        <td colspan="4" align="right"><?php
                                                if ($db->f("order_discount") > 0)
                                                    echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT;
                                                else
                                                    echo $VM_LANG->_PHPSHOP_FEE;
                                                    ?>:
                                                        </td> 
                                                        <td align="right"><?php
                                                        if ($db->f("order_discount") > 0)
                                                            echo "- " . $CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount")));
                                                        elseif ($db->f("order_discount") < 0)
                                                            echo "+ " . $CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount")));
                                                    ?>&nbsp;&nbsp;&nbsp;
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" align="right"><strong><?php echo $VM_LANG->_PHPSHOP_CART_TOTAL ?>: </strong></td>
                                                        <td align="right"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($db->f("order_total")); ?>
                                                            </strong>&nbsp;&nbsp;&nbsp;
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <td colspan="4" align="right">&nbsp;</td>
                                                    <td align="right"><strong><?php echo ps_checkout::show_tax_details($db->f('order_tax_details')); ?>
                                                        </strong>&nbsp;&nbsp;&nbsp;
                                                    </td>
                                                </tr>            
                                                </table>
                                                </td>
                                                </tr>
                                                <!-- End Order Items Information --> 

                                                <br />

                                                <table width="100%">
                                                    <tr>
                                                        <td colspan="2">&nbsp;</td>
                                                    </tr>
                                                    <tr class="sectiontableheader">
                                                        <th align="left"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_NOTE ?>:</th>
                                                        <th align="left"><?php echo nl2br($db->f("customer_note")) ; ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td> <?php echo $PHPSHOP_LANG->_PHPSHOP_USER_FORM_OCCASION ?> :</td>
                                                        <td><?php echo nl2br($db->f("order_occasion_name")) . "<br />"; ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td ><?php echo $PHPSHOP_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS ?> :</td>
                                                        <td><?php echo nl2br($db->f("customer_comments")) . "<br />"; ?>
                                                        </td>
                                                    </tr>



                                                </table>
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
// } /* End of security check */
                                                ?>
