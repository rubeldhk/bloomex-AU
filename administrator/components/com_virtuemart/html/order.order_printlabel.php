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
global $mosConfig_live_site;

$ps_product = new ps_product;

$orders = mosgetparam($_REQUEST, 'order_id', null);
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
foreach ($orders as $order_id) {
    if (!is_numeric($order_id))
        die('Please provide a valid Order ID!');

    $q = "SELECT * FROM #__{vm}_orders AS O, #__{vm}_order_occasion AS OC WHERE O.order_id='$order_id' AND O.customer_occasion = OC.order_occasion_code";
    $db->query($q);

    if ($db->next_record()) {
        if ($my->prevs->warehouse_only && ( $my->prevs->warehouse_only != $db->f("warehouse"))) {
            die('Order is not assigned to your warehouse');
        }
        ?>

        <?php
        // Get bill_to information
        $dbbt = new ps_DB;
        $q = "SELECT * FROM #__{vm}_order_user_info WHERE  order_id='$order_id' AND address_type = 'ST' ORDER BY address_type, order_info_id ASC";
        $dbbt->query($q);
        $dbbt->next_record();
        $dbst = & $dbbt;

        if ($PSHOP_SHIPPING_MODULES[0] != "no_shipping" && $db->f("ship_method_id")) {
            ?>
            <br/><br/>
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tr>
                    <td  align="center">
                        <div style="width:300px;">
                            <b><?php
                                if ($db->f("customer_note")) {
                                    echo str_replace("\\", "", nl2br(html_entity_decode($db->f("customer_note"))));
                                } else {
                                    echo " ./. ";
                                }
                                ?></b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td  align="center">
                        <div style="width:300px;">
                            <b><?php
                                if ($db->f("customer_signature")) {
                                    echo str_replace("\\", "", nl2br(html_entity_decode($db->f("customer_signature"))));
                                } else {
                                    echo " ./. ";
                                }
                                ?></b>
                        </div>
                        <br/><br/><br/>
                    </td>
                </tr>

                <tr>
                    <td align="center">
                        <br/>
                        <br/>
                        <img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo $VM_LANG->_VM_BARCODE_PREFIX . "-" . sprintf("%08d", trim($db->f("order_id"))); ?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" />
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>

                        <table width="55%" border="0" align="center" cellpadding="5"  cellspacing="0">
                            <tr>
                                <td width="30%"><strong>Delivery Date:</strong></td>
                                <td width="70%"><b><?php echo date($db->f("ddate")); ?></b></td>
                            </tr>
                            <tr>
                                <td><strong>Delivery Time : </strong></td>
                                <td><b><?php
                                        $details = explode("|", $db->f("ship_method_id"));
                                        echo $details[2];
                                        ?></b>              </td>
                            </tr>
                            <tr>
                                <td><b><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER ?> </b>: </td>
                                <td><b><?php printf("%08d", $db->f("order_id")); ?></b></td>
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
                            <?php if ($dbst->f("suite")) { ?>
                                <tr valign="top"> 
                                    <td><?php echo $VM_LANG->_PHPSHOP_SUITE ?> :</td>
                                    <td><?php
                                        $dbst->p("suite");
                                        ?></td>
                                </tr>
                            <?php } ?>
                            <?php if ($dbst->f("street_number")) { ?>
                                <tr valign="top"> 
                                    <td><?php echo $VM_LANG->_PHPSHOP_STREET_NUMBER ?> :</td>
                                    <td><?php
                                        $dbst->p("street_number");
                                        ?></td>
                                </tr>
                                <tr valign="top"> 
                                    <td><?php echo $VM_LANG->_PHPSHOP_STREET_NAME ?> :</td>
                                    <td><?php
                                        $dbst->p("street_name");
                                        ?></td>
                                </tr>         
                            <?php } else { ?>
                                <tr valign="top"> 
                                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</strong></td>
                                    <td><?php
                                        $dbst->p("address_1");
                                        echo "<br />";
                                        $dbst->p("address_2");
                                        ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
                                <td><?php $dbst->p("city"); ?></td>
                            </tr>
                            <tr>
                                <td>District :</td>
                                <td><?php $dbst->p("district"); ?></td>
                            </tr>
                            <tr>
                                <td>Province :</td>
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
                                    if (!isset($country) || $country != $dbst->f("country")) {
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
                        </table></td>
                </tr>
            </table>
            <?php
        }
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
