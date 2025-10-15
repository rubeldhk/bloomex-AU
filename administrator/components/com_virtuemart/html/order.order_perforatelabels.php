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
require_once($_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/phpqrcode.php');
$orders = mosgetparam($_REQUEST, 'order_id', null);
$dbc = new ps_DB;
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
    window.onload = function () {
        document.getElementById('loader').style.display = "none";
    };
</script>
<?php
$lastElement = end($orders);
foreach ($orders as $order_id) {
    if (!is_numeric($order_id))
        die('Please provide a valid Order ID!');

    $q = "SELECT O.*,e.shopper_group_name, h.comments FROM #__{vm}_orders O"
            . " LEFT JOIN jos_vm_orders_extra AS e ON e.order_id = O.order_id "
            . " AND e.shopper_discount_value > 0"
            . " LEFT JOIN jos_vm_order_history h on h.order_id = O.order_id "
            . " WHERE O.order_id='$order_id' LIMIT 1";

    $db->query($q);

    if ($db->next_record()) {
        $date = $date_str = date("d-M-Y", strtotime($db->f("ddate")));

        if (strstr($db->f('comments'), 'delivery delay')) {
            $date_str .= " Delay Accepted";
        }

        if ($my->prevs->warehouse_only && ( $my->prevs->warehouse_only != $db->f("warehouse"))) {
            die('Order is not assigned to your warehouse');
        }
// Get ship_to information

        $shipping_info = new ps_DB;
        $query = "SELECT * from jos_vm_order_user_info AS ui 
    LEFT JOIN #__{vm}_state AS S ON S.state_2_code = `ui`.`state` AND  S.`country_id`=13
    LEFT JOIN #__{vm}_country  AS C  ON C.country_3_code=`ui`.`country`
    WHERE `ui`.`order_id` = {$order_id} AND `ui`.`address_type`='ST'";
        $shipping_info->query($query);
        $shipping_info->next_record();

        $billing_info = new ps_DB;
        $query = "SELECT * from jos_vm_order_user_info AS ui 
    LEFT JOIN #__{vm}_state AS S ON S.state_2_code = `ui`.`state` AND  S.`country_id`=13
    LEFT JOIN #__{vm}_country  AS C  ON C.country_3_code=`ui`.`country`
    WHERE `ui`.`order_id` = {$order_id} AND `ui`.`address_type`='BT'";
        $billing_info->query($query);
        $billing_info->next_record();

        /* rate */
        $query = "SELECT 
            `q`.`id`,
            `q`.`token`
        FROM `jos_vm_orders_qr` AS `q`
        WHERE 
            `q`.`order_id`=" . $order_id . "
        ";
        $qr_obj = null;
        $database->setQuery($query);
        $database->loadObject($qr_obj);

        if ($qr_obj === null) {
            $qr_token = md5('scanit' . $order_id);

            $query = "INSERT INTO `jos_vm_orders_qr`
            (
                `order_id`,
                `token`
            )
            VALUES (
                " . $order_id . ",
                '" . $qr_token . "'
            )
            ";
            $database->setQuery($query);
            $database->query();
        } else {
            $qr_token = $qr_obj->token;
        }

        $rate_name = 'Default';
        $zip_symbols = 4;
        $zip = strtoupper(str_replace(array(' ', '-'), '', trim($shipping_info->f('zip'))));

        while ($zip_symbols > 1) {
            $query = "SELECT 
                `r`.`id_rate`,
                `r`.`rate`,
                `r`.`name`
            FROM `jos_driver_rates` AS `r`
            INNER JOIN `jos_driver_rates_postalcodes` AS `rp`
                ON
                `rp`.`postalcode`='" . $database->getEscaped(strtoupper(mb_substr($zip, 0, $zip_symbols))) . "'
                AND
                `rp`.`id_rate`=`r`.`id_rate`
            ";
            $rate_obj = null;
            $database->setQuery($query);
            $database->loadObject($rate_obj);

            if ($rate_obj !== null) {
                $rate_name = $rate_obj->name;
                break;
            }
            $zip_symbols--;
        }
        /* !rate */
        $query = "SELECT *  FROM #__vm_order_item WHERE #__vm_order_item.order_id = '" . $order_id . "' ORDER BY order_item_id ASC";
        $database->setQuery($query);
        $product = $database->loadObjectList();
        if (count($product)) {
            $k = 0;
            $products_list_table = "<table style='border-collapse:separate;border-spacing: 3px 5px;margin: 0 auto;'><tr><td colspan='2'><hr></td></td></tr>";
            foreach ($product as $item) {
                $item->ingredient_list = "";
                $products_list = "";
                $ingredient_bold_list = "";


                $qInput = "SELECT 
                    `oi_i`.`ingredient_name`,
                    `oi_i`.`ingredient_quantity`,
                    `i`.`bold`
                FROM `jos_vm_order_item_ingredient` AS `oi_i`
                LEFT JOIN `product_ingredient_options` AS `i`
                    ON
                    `i`.`igo_product_name`=`oi_i`.`ingredient_name`
                WHERE 
                    `oi_i`.`order_item_id`={$item->order_item_id}";

                $database->setQuery($qInput);
                $ing = $database->loadObjectList();

                if ($ing) {
                    $item->ingredient_list .= "<table style='border-collapse:separate;border-spacing: 3px 5px;'>";
                    foreach ($ing as $v) {
                        if ($v->bold == '1') {
                            $ingredient_bold_list .= "<tr style='font-weight: bold;'><td>" . ($v->ingredient_quantity / $item->product_quantity ) . "</td><td>" . $v->ingredient_name . "</td></tr>";
                        } else {
                            $item->ingredient_list .= "<tr><td>" . ($v->ingredient_quantity / $item->product_quantity ) . "</td><td>" . $v->ingredient_name . "</td></tr>";
                        }
                    }
                    if ($ingredient_bold_list) {
                        $item->ingredient_list .= "<tr><td colspan='2'><hr></td></td></tr>" . $ingredient_bold_list . "</table>";
                    } else {
                        $item->ingredient_list .= "</table>";
                    }
                }


                if ($item->order_item_name) {
                    $products_list .= "<table style='border-collapse:separate;border-spacing: 3px 5px;'>";
                    $products_list .= "<tr><td><b>Product:</b></td><td>" . str_replace("\n", "<br/>", $item->order_item_name) . "</td></tr>";
                    $products_list .= "<tr><td><b>SKU:</b></td><td>" . $item->order_item_sku . "</td></tr>";
                    $products_list .= '<tr><td style="font: 22px arial;"><b>QTY:</b></td><td style="font: 22px arial;"><span style="font-weight: bold; text-decoration: underline;">' . $item->product_quantity . '</span></td></tr>';
                    $products_list .= '</table>';
                    $k++;
                }
                $products_list_table .= "<tr><td>$products_list</td><td>$item->ingredient_list</td></tr><tr><td colspan='2'><hr></td></td></tr>";
            }
            $products_list_table .= "</table>";
        }
        //===================== #6373 YI, ship date on NEW FORM============
        $shipday = date("w", strtotime($db->f("ddate")));

        $nowdate = date("d-M-Y");
        $nowday = date("w");
        $shipdate = ''; // expected ship date
        $intransit = ''; // days in transit

        if ($date == $nowdate) {
            $shipdate = $nowdate;
        } else {
            $shipdate = $date;
        }
        // check if shipping address out of town
        $zip = strtoupper(str_replace(array(' ', '-'), '', trim($shipping_info->f('zip'))));
        $zip_symbols = 4;
        $intransit ="UKNOWN POSTCODE";
        $postcode_obj = false;
        while ($zip_symbols>0) {
            $query = "SELECT 
                `city`,
                `days_in_route`,
                `out_of_town`
            FROM `jos_postcode_warehouse`
            WHERE
                `postal_code`= '" . $database->getEscaped(strtoupper(mb_substr($zip, 0, $zip_symbols))) . "'";
            $database->setQuery($query);
            $database->loadObject($postcode_obj);

            if ($postcode_obj) {
                $city_name = $postcode_obj->city;
                $days_in_route = $postcode_obj->days_in_route;
                if ($days_in_route > 0) { //check out of town
                    $d_date = date_create($db->f("ddate"));
                    $temp_date = date_sub($d_date, date_interval_create_from_date_string("$days_in_route days"));
                    $shipdate = date_format($temp_date, "d-M-Y");  //temporal ship date
                    $intransit = 'Days in transit: ' . $days_in_route;

                }
                break;
            }

            $zip_symbols--;
        }
        ?>
        <br/>
        <style type="text/css">
            body { width: 100%; margin: 0; float: none; vertical-align:top; }
        </style>
        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" valign="top">    
            <tr>
                <td width="50%" valign="top" align="left" style="text-align:left;">
                    <table width="90%" cellspacing="0" cellpadding="5" border="0" align="center" valign="top">
                        <tr> 
                            <td>&nbsp;</td>
                            <td style="text-align:left;">	          	 
                                <img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo $VM_LANG->_VM_BARCODE_PREFIX . "-" . sprintf("%08d", trim($db->f("order_id"))); ?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" />
                                <br/>
                                <br/>
                            </td>
                        </tr> 
                        <?php if ($db->f("shopper_group_name")) { ?>
                            <tr>
                                <td colspan="2" style ="border: 1px solid black;text-align: center;"><?php echo "Corporate"; ?></td></tr>
                            <tr>
                            <?php } ?>
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
                            <td class="content"> <b> <?php echo $date_str; ?></b><span style="color: red"><?php echo ($shipdate) ? "|Ship: <b> " . $shipdate . " </b>|" : "" ?></span></td>
                        </tr>
                        <tr> 
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_TYPE_LABEL; ?>:</strong></td>
                            <td style="text-align:left;">
                                <?php
                                echo $shipping_info->f("address_type2");
                                ?>
                            </td>
                        </tr>
                        <?php if ($shipping_info->f("company") != '') { ?>
                            <tr> 
                                <td width="40%" style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?>:</strong></td>
                                <td style="text-align:left;" width="60%"><?php $shipping_info->p("company"); ?></td>
                            </tr>
                        <?php } ?>
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
                            <?php
                        }

                        $color_array = array(
                            "black" => "BLACK",
                            "orange" => "ORANGE",
                            "lime" => "LIME",
                            "red" => "RED",
                            "brown" => "Brown",
                            "not_valid" => "NOT VALID COLOUR"
                        );
                        $color = $color_array[$db->f("color")];
                        ?>

                        <?php if ($shipping_info->f("district") != '') { ?>
                            <tr> 
                                <td style="text-align:right;"><strong>District:</strong></td>
                                <td style="text-align:left;"><?php $shipping_info->p("district"); ?></td>
                            </tr>
                        <?php } ?>

                        <?php if ($shipping_info->f("city") != '') { ?>
                            <tr>
                                <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?>:</strong></td>
                                <td style="text-align:left;"><?php $shipping_info->p("city"); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($shipping_info->f("state_name") != '') { ?>
                            <tr> 
                                <td style="text-align:right;white-space:nowrap;"><strong>Province:</strong></td>
                                <td style="text-align:left;"><?php $shipping_info->p("state_name"); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($shipping_info->f("zip") != '') { ?>
                            <tr> 
                                <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?>:</strong></td>
                                <td style="text-align:left;"><?php $shipping_info->p("zip"); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($shipping_info->f("country_name") != '') { ?>
                            <tr> 
                                <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?>:</strong></td>
                                <td style="text-align:left;"><?php $shipping_info->p("country_name"); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($shipping_info->f("phone_1") != '') { ?>
                            <tr> 
                                <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>:</strong></td>
                                <td style="text-align:left;"><?php $shipping_info->p("phone_1"); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </td>
                <td width="50%" valign="top" align="right" style="text-align:right;">
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
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="50%" valign="top" align="left" style="text-align:left;">
                    <table width="90%" cellspacing="0" cellpadding="5" border="0" align="center" valign="top">
                        <tr>
                            <td colspan="2"><?php echo $products_list_table; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;vertical-align:top;"><strong><?php echo "Colour"; ?>:</strong></td>
                            <td style="text-align:left;"><?php echo $color; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;vertical-align:top;"><strong><?php echo "Special Instructions"; ?>:</strong></td>
                            <td style="text-align:left;"><?php $db->p("customer_comments"); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="50%" valign="top" align="right" style="text-align:right;">
                    <table width="90%" cellspacing="0" cellpadding="5" border="0" align="center" valign="top">
                        <tr>
                            <td class="caption">

                                <span class ="create-route-link" style="display:none"><?php echo $mosConfig_driverApp_link . '/create/' . $qr_token; ?></span>
                                <?php
                                QRcode::png($mosConfig_driverApp_link . '/create/' . $qr_token, "images/qrCodes/qr_{$db->f('order_id')}.png", QR_ECLEVEL_L, 4, 1);
                                echo '<img src="images/qrCodes/qr_' . $db->f("order_id") . '.png" />';
                                ?>

                            <td class="content" style="font: 30px arial;    vertical-align: top;text-align: left">
                                <?php
                                if ($shipping_id == '25') {
                                    echo '<b style="float: right;font-weight: bold;">AM</b><br>';
                                }
                                ?>
                                <b><?php echo $rate_name; ?></b></td>
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
                                echo $shipping_info->f("address_type2");
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
                        <?php if ($shipping_info->f("street_number") OR $shipping_info->f("street_name") OR $shipping_info->f("suite")) { ?>
                            <tr>
                                <td class="caption">Address :</td>
                                <td  class="content" style="text-align:left;"><?php echo $shipping_info->f("street_number") . " " . $shipping_info->f("street_name"); ?></td>
                            </tr>
                        <?php } else { ?>
                            <tr valign="top">
                                <td class="caption"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</td>
                                <td  class="content" style="text-align:left;"><?php echo $shipping_info->f("address_1") . " <br /> " . $shipping_info->p("address_2"); ?></td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td style="text-align:right;"><strong>District:</strong></td>
                            <td style="text-align:left;"><?php $shipping_info->p("district"); ?></td>
                        </tr>
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
                            <td style="text-align:right;"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>:</strong></td>
                            <td style="text-align:left;"><?php $shipping_info->p("phone_1"); ?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td style="text-align:left;">
                                <img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo $VM_LANG->_VM_BARCODE_PREFIX . "-" . sprintf("%08d", trim($db->f("order_id"))); ?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
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
   