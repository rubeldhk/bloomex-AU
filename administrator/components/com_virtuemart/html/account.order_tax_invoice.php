<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: account.order_tax_invoice.php,v 1.8.2.4 2006/04/27 19:35:52 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2006 Soeren Eberhardt. All rights reserved.
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
global $vendor_currency, $my, $database;
$orderId = mosgetparam($_REQUEST, 'order_id', 0);
$userId = (int) $my->id;
date_default_timezone_set('Australia/Sydney');
if ($userId > 0) {
    $queryOrder = "SELECT *,o.cdate FROM `jos_vm_orders` AS `o`
    LEFT join jos_vm_order_user_info as i on i.order_id=o.order_id and i.address_type='BT'
    WHERE `o`.`user_id`=" . $userId . "    AND `o`.`order_id`=$orderId";

    $orderObj = false;
    $database->setQuery($queryOrder);
    $database->loadObject($orderObj);

    $queryItems = "SELECT * FROM `jos_vm_order_item` WHERE `order_id`=" . $orderId;

    $database->setQuery($queryItems);
    $orderItemsObj = $database->loadObjectList();


    if ($orderObj) {
        ?>
        <div class="container white taxInvoiceDetails">
            <div class="row ">
                <div class="col-12">
                    <input type="button" class="btn btn-warning  float-end clickToPrint" value="Click To Print" onclick="window.print();return false;" />
                    <img alt="Bloomex Australia" src="/templates/bloomex_adaptive/images/bloomexlogo.png">
                </div>
            </div>
            <div class="row ">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3  class="float-start"><?php echo $VM_LANG->_PHPSHOP_ORDER_TAX_INVOICE; ?></h3>
                    <h3 class="float-end">#INV-<?php echo $orderObj->order_id; ?></h3>
                </div>
            </div>
            <hr class="bottomBorder">

            <div class="row ">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <p><?php echo $VM_LANG->_PHPSHOP_BUSINESS_NAME; ?>: Bloomex Pty Ltd</p>
                    <p><?php echo $VM_LANG->_PHPSHOP_ORDER_ADDRESS; ?>: 9/12-18 Victoria Street East, Lidcombe, NSW, 2141</p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <table class="table  float-end borderless">
                        <tr><td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER; ?></td><td>INV-<?php echo $orderObj->order_id; ?></td></tr>
                        <tr><td><?php echo $VM_LANG->_PHPSHOP_ORDER_ISSUE_DATE; ?></td><td><?php echo date("F j, Y", $orderObj->cdate); ?></td></tr>
                    </table>
                </div>
            </div>
            <hr class="bottomBorder">

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <h3><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_BILL_TO_LBL; ?></h3>
                    <?php echo $orderObj->company; ?><br>
                    <?php echo $orderObj->user_email; ?><br>
                    <?php echo $orderObj->suite . ' ' . $orderObj->street_number . ' ' . $orderObj->street_name . ', <br>' . $orderObj->city . ', ' . $orderObj->state . ', ' . $orderObj->zip; ?><br>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?php
                    $subtotal = 0;
                    $GST = 0;
                    $total = 0;

                    if (!$orderItemsObj) {
                        echo $VM_LANG->_PHPSHOP_ORDER_EMPTY;
                    } else {
                        ?>
                        <table class="table float-end ">
                            <tr>
                                <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_QUANTITY; ?></th>
                                <th><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_LBL; ?></th>
                                <th><?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRICE; ?></th>
                                <th><?php echo $VM_LANG->_PHPSHOP_PRODUCT_AMOUNT; ?></th>
                            </tr>
                            <?php
                            foreach ($orderItemsObj as $item) {
                                $itemPrice = number_format($item->product_final_price, 2, '.', '');
                                $itemAmount = number_format(($itemPrice * $item->product_quantity), 2, '.', '');
                                echo "<tr>
                            <td>" . $item->product_quantity . "</td>
                            <td>" . $item->order_item_name . "</td>
                            <td>$" . $itemPrice . "</td>
                            <td>$" . $itemAmount . "</td>
                        </tr>";
                            }
                            ?>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table float-end borderless">
                        <tr><td>Subtotal:</td><td>$<?php echo $orderObj->order_subtotal; ?></td><td><i>(GST included:$<?php echo round($orderObj->order_subtotal / 11, 2); ?>)</i></td></tr>
                        <tr><td>Delivery fee:</td><td>$<?php echo $orderObj->order_shipping; ?></td><td><i>(GST included:$<?php echo round($orderObj->order_shipping / 11, 2); ?>)</i></td></tr>
                        <?php
                        $discount = ($orderObj->order_total - $orderObj->order_shipping - $orderObj->order_subtotal);
                        if ($discount) {
                            ?>
                            <tr><td>Discount:</td><td>$<?php echo $discount; ?></td><td><i>(GST excluded:$<?php echo round($discount / 11, 2); ?>)</i></td></tr>
                        <?php } ?>
                        <tr><th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL; ?></th><th>$<?php echo number_format(($orderObj->order_total), 2, '.', ''); ?></th><th><i>(GST included:$<?php echo round($orderObj->order_total / 11, 2); ?>)</i></th></tr>

                    </table>
                </div>
            </div>

            <p><b>Bloomex Pty Ltd</b> 	&nbsp;	&nbsp;	&nbsp; ABN: 27147609443 &nbsp;	&nbsp;	&nbsp; Phone 1 800-905-147 	&nbsp;	&nbsp;	&nbsp;  wecare@bloomex.com.au  <br> bloomex.com.au</p>

        </div>
        <?php
    } else {
        echo '<h4>' . _LOGIN_TEXT . '</h4><br/>';
        include(PAGEPATH . 'checkout.login_form.php');
        echo '<br/><br/>';
    }
} else {
    echo '<h4>' . _LOGIN_TEXT . '</h4><br/>';
    include(PAGEPATH . 'checkout.login_form.php');
    echo '<br/><br/>';
}
?>
<style>
    .bottomBorder{
        height: 2px;
        width: 100%;
        background: #A40001;
    }
    .borderless{
        width: auto !important;
    }
    .borderless td, .borderless th {
        border: none !important;
    }
    .table th{
        background: #A40001;
        color: #FFFFFF;
    }
    .pull-right,.pull-right th{
        text-align: right;
    }
    .taxInvoiceDetails .row{
        margin-bottom: 10px;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .taxInvoiceDetails, .taxInvoiceDetails * {
            visibility: visible;
        }
        .taxInvoiceDetails {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .clickToPrint{
            display: none;
        }
    }
</style>