<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: product.product_form.php,v 1.14.2.4 2006/03/10 15:55:15 soeren_nb Exp $
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

require_once( CLASSPATH . 'ps_product_discount.php' );

$product_id = mosGetParam($_REQUEST, 'product_id');
if (is_array($product_id))
    $product_id = (int) $product_id[0];

$product_parent_id = mosGetParam($_REQUEST, 'product_parent_id');
$next_page = mosGetParam($_REQUEST, 'next_page', "product.product_display");
$option = empty($option) ? mosgetparam($_REQUEST, 'option', 'com_virtuemart') : $option;
$clone_product = mosGetParam($_REQUEST, 'clone_product', "0");

$dl_checked = "";
$curr_filename = "";
$list = Array();
$my_categories = array();
$selected_categories = array();
$related_products = Array();

if ($product_parent_id) {
    if ($product_id) {
        $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_UPDATE_ITEM_LBL;
    } else {
        $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_NEW_ITEM_LBL;
    }
    $info_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_INFO_LBL;
    $status_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_STATUS_LBL;
    $dim_weight_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_DIM_WEIGHT_LBL;
    $images_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_IMAGES_LBL;
    $delete_message = $VM_LANG->_PHPSHOP_PRODUCT_FORM_DELETE_ITEM_MSG;
} else {
    $product_parent_id = '';
    if ($product_id = @$vars["product_id"]) {
        if ($clone_product == '1') {
            $action = $VM_LANG->_PHPSHOP_PRODUCT_CLONE;
        } else {
            $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_UPDATE_ITEM_LBL;
        }
    } else {
        $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_NEW_PRODUCT_LBL;
    }
    $info_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_INFO_LBL;
    $status_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_STATUS_LBL;
    $dim_weight_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_DIM_WEIGHT_LBL;
    $images_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_IMAGES_LBL;
    $delete_message = $VM_LANG->_PHPSHOP_PRODUCT_FORM_DELETE_PRODUCT_MSG;
}

if (!empty($product_id)) {
    $price = $ps_product->get_retail_price($product_id);
}

if (!empty($product_id)) {
    // get the Database object we're filling the product form with
    $db = $ps_product->sql($product_id);
    $db->next_record();

    // Get category IDs
    $db2 = new ps_DB;
    $q = "SELECT 
        `c`.`category_id`,
        `c`.`category_name`,
        `c`.`category_publish`
    FROM #__{vm}_product_category_xref AS `pc_x`
    INNER JOIN `jos_vm_category` AS `c`
        ON
            `c`.`category_id`=`pc_x`.`category_id`
    WHERE `pc_x`.`product_id`='$product_id'";
    $db2->query($q);
    while ($db2->next_record()) {
        $my_categories[$db2->f("category_id")] = "1";
        $selected_categories[$db2->f("category_id")] = $db2->f("category_name").''.(($db2->f("category_publish") == 'N') ? '&nbsp;&nbsp;&nbsp;| U' : '').'';
    }

    // Get the Manufacturer ID
    $db2->query("SELECT manufacturer_id FROM #__{vm}_product_mf_xref WHERE product_id='$product_id'");
    $db2->next_record();
    $manufacturer_id = $db2->f("manufacturer_id");

    // Get the Related Products
    $db2->query("SELECT related_products FROM #__{vm}_product_relations WHERE product_id='$product_id'");
    if ($db2->next_record()) {
        $related_products = explode("|", $db2->f("related_products"));
    }

    // Look if the Product is downloadable
    $q_dl = "SELECT attribute_name,attribute_value AS filename FROM #__{vm}_product_attribute WHERE ";
    $q_dl .= "product_id='$product_id' AND attribute_name='download'";
    $db2->query($q_dl);
    if ($db2->next_record()) {
        $dl_checked = "checked=\"checked\"";
    }
    $curr_filename = $db2->f("filename");
} elseif (empty($vars["error"])) {
    $default["product_publish"] = "Y";
    $default["product_weight_uom"] = $VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT_UOM_DEFAULT;
    $default["product_lwh_uom"] = $VM_LANG->_PHPSHOP_PRODUCT_FORM_DIMENSION_UOM_DEFAULT;
    $default["product_unit"] = $VM_LANG->_PHPSHOP_PRODUCT_FORM_UNIT_DEFAULT;
    $default["product_available_date"] = time();
}
// get the default shopper group
$shopper_db = new ps_DB;
$q = "SELECT shopper_group_id,shopper_group_name FROM #__{vm}_shopper_group WHERE `default`= '1' AND vendor_id='" . $db->f("vendor_id") . "'";
$shopper_db->query($q);
if ($shopper_db->num_rows() < 1) {  // when there is no "default", take the first in the table 
    $q = "SELECT shopper_group_id,shopper_group_name from #__{vm}_shopper_group WHERE vendor_id='$ps_vendor_id'";
    $shopper_db->query($q);
}
$shopper_db->next_record();
$my_shopper_group_id = $shopper_db->f("shopper_group_id");

// For cloning a product, we just need to empty the variable product_id
if ($clone_product == "1") {
    $product_id = "";
}
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site ?>/includes/js/calendar/calendar-mos.css" title="green" />
<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/includes/js/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/includes/js/calendar/lang/calendar-en.js"></script>
<br />
<?php
$title = '<img src="' . IMAGEURL . 'ps_image/product_code.png" border="0" align="center" alt="Product Form" />&nbsp;&nbsp;';
$title .= $action;

if (!empty($product_id)) {
    $title .= " :: " . $db->f("product_name");
    $flypage = $ps_product->get_flypage($product_id);
    ?>
    <a href="<?php echo $mosConfig_live_site . "/index2.php?option=com_virtuemart&page=shop.product_details&flypage=$flypage&product_id=$product_id" ?>" target="_blank">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SHOW_FLYPAGE ?>
    </a>
    <?php
}
//First create the object and let it print a form heading
$formObj = new formFactory($title);
//Then Start the form
$formObj->startForm('adminForm', 'enctype="multipart/form-data"');

$tabs = new mShopTabs(0, 1, "_main");
$tabs->startPane("content-pane");
$tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/edit.png\" align=\"center\" width=\"16\" height=\"16\" border=\"0\" />&nbsp;$info_label", "info-page");

global $database,$mosConfig_aws_s3_bucket_public_url;
$sql = "SELECT * FROM #__vm_product_options WHERE product_id = $product_id";
$database->setQuery($sql);
$rows = $database->loadObjectList();

mosCommonHTML::loadCKeditor();

$query_ingredient_price = "SELECT  ROUND(SUM(igl_quantity*o.landing_price),2) as normal_price,
                        ROUND(SUM(igl_quantity_deluxe*o.landing_price),2) as deluxe_price,
                        ROUND(SUM(igl_quantity_petite*o.landing_price),2) as petite_price,
                        ROUND(SUM(igl_quantity_supersize*o.landing_price),2) as supersize_price
                        FROM `product_ingredients_lists` as l
                        left join product_ingredient_options as o on o.igo_id=l.igo_id where l.product_id=$product_id
                        group by l.product_id";
$database->setQuery($query_ingredient_price);
$rows_ingredient_price = $database->loadObjectList();

$petite_price = "";
$normal_price = "";
$deluxe_price = "";
$supersize_price = "";
if (!empty($rows_ingredient_price[0]->normal_price)) {
    $petite_price = $rows_ingredient_price[0]->petite_price;
    $normal_price = $rows_ingredient_price[0]->normal_price;
    $deluxe_price = $rows_ingredient_price[0]->deluxe_price;
    $supersize_price = $rows_ingredient_price[0]->supersize_price;
}

?>

<table class="adminform">
    <tr> 
        <td valign="top">
            <table width="100%" border="0">
                <tr> 
                    <td align="left" colspan="2"><?php echo "<h2 >$info_label</h2>"; ?></td>
                </tr>
                <tr>
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            Must be Combined:</div>
                    </td>
                    <td width="79%" >
                        <?php
                        $must_be_combined = '';
                        if (isset($rows[0]->must_be_combined) AND !empty($rows[0]->must_be_combined)) {
                            $must_be_combined = 'checked="checked"';
                        }
                        ?>
                        <input type="checkbox" name="must_be_combined" value="1" <?php echo $must_be_combined; ?> />
                    </td>
                </tr>
                <tr>
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            Show Sale Overlay:</div>
                    </td>
                    <td width="79%" >
                        <?php
                        $show_sale_overlay = '';
                        if (isset($rows[0]->show_sale_overlay) AND !empty($rows[0]->show_sale_overlay)) {
                            $show_sale_overlay = 'checked="checked"';
                        }
                        ?>
                        <input type="checkbox" name="show_sale_overlay" value="1" <?php echo $show_sale_overlay; ?> />
                    </td>
                </tr>
                <tr>
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            Sold Out:</div>
                    </td>
                    <td width="79%" >
                        <?php
                        $product_sold_out = '';
                        if (isset($rows[0]->product_sold_out) AND !empty($rows[0]->product_sold_out)) {
                            $product_sold_out = 'checked="checked"';
                        }
                        ?>
                        <input type="checkbox" name="product_sold_out" value="1" <?php echo $product_sold_out; ?> />
                    </td>
                </tr>
                <tr>
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            Out Of Season:</div>
                    </td>
                    <td width="79%" >
                        <?php
                        $product_out_of_season = '';
                        if (isset($rows[0]->product_out_of_season) AND !empty($rows[0]->product_out_of_season)) {
                            $product_out_of_season = 'checked="checked"';
                        }
                        ?>
                        <input type="checkbox" name="product_out_of_season" value="1" <?php echo $product_out_of_season; ?> />
                    </td>
                </tr>
                <tr> 
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            Disable Coupons:</div>
                    </td>
                    <td width="79%" > 
                        <?php
                        if ($db->sf("not_apply_discount") == 1) {
                            $sNAD = "checked=\"checked\"";
                        } else {
                            $sNAD = "";
                        }
                        ?> 
                        <input type="checkbox" name="not_apply_discount" value="1" <?php echo $sNAD; ?> />
                    </td>
                </tr>
                <tr> 
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PUBLISH ?>:</div>
                    </td>
                    <td width="79%" > <?php
                        if ($db->sf("product_publish") == "Y") {
                            echo "<input type=\"checkbox\" name=\"product_publish\" value=\"Y\" checked=\"checked\" />";
                        } else {
                            echo "<input type=\"checkbox\" name=\"product_publish\" value=\"Y\" />";
                        }
                        ?> </td>
                </tr>
                <!--MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM-->
                <tr> 
                    <td  width="21%" ><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_RP ?>:</div>
                    </td>
                    <td width="79%" > <?php
                        if ($db->sf("product_related") == "Y") {
                            echo "<input type=\"checkbox\" name=\"product_related\" value=\"Y\" checked=\"checked\" />";
                        } else {
                            echo "<input type=\"checkbox\" name=\"product_related\" value=\"Y\" />";
                        }
                        ?> </td>
                </tr>
                <tr> 
                    <td width="21%" ><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SKU ?>:</div>
                    </td>
                    <td width="79%" height="2"> 
                        <input type="text" class="inputbox"  name="product_sku" value="<?php $db->sp("product_sku"); ?>" size="32" maxlength="64" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" height="18"><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_NAME ?>:</div>
                    </td>
                    <td width="79%" height="18" > 
                        <input type="text" class="inputbox seo_name"  name="product_name" value="<?php echo shopMakeHtmlSafe($db->sf("product_name")); ?>" size="32" maxlength="255" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" height="18">
                        <div style="text-align:right;font-weight:bold;">Alias:</div>
                    </td>
                    <td width="79%" height="18" > 
                        <input type="text" class="inputbox seo_alias"  name="alias" value="<?php echo shopMakeHtmlSafe($db->sf("alias")); ?>" size="32" maxlength="255" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%"><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_URL ?>:</div>
                    </td>
                    <td width="79%"> 
                        <input type="text" class="inputbox"  name="product_url" value="<?php $db->sp("product_url"); ?>" size="32" maxlength="255" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%"><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_VENDOR ?>:</div>
                    </td>
                    <td width="79%" ><?php $ps_product->list_vendor($db->sf("vendor_id")); ?></td>
                </tr>
                <tr> 
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_MANUFACTURER ?>:</div>
                    </td>
                    <td width="79%" ><?php $ps_product->list_manufacturer(@$manufacturer_id); ?></td>
                </tr>
                <?php if (!$product_parent_id) { ?>
                    <tr> 
                        <td width="29%" valign="top"><div style="text-align:right;font-weight:bold;">
                                <?php echo $VM_LANG->_PHPSHOP_CATEGORIES ?>:<br/><br/>
                                <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_MULTISELECT) ?></div>
                        </td>
                        <td width="71%" ><?php $ps_product_category->list_all("product_categories[]", "", $my_categories, 10, false, true); ?></td>
                    </tr>
                    <?php
                }
                ?>
                    <tr> 
                        <td width="29%" valign="top">
                            <div style="text-align:right;font-weight:bold;">
                                Canonical category:
                            </div>
                        </td>
                        <td width="71%">
                            <select name="canonical_category_id">
                                <?php
                                foreach ($selected_categories as $selected_category_key => $selected_category_name) {
                                    ?>
                                    <option value="<?php echo $selected_category_key; ?>" <?php echo ((isset($rows[0]->canonical_category_id) AND $rows[0]->canonical_category_id == $selected_category_key) ? 'selected' : ''); ?>>
                                        <?php echo $selected_category_name; ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
            </table>
        </td>
        <td>
            <table>
                <tr> 
                    <td width="29%" ><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRICE_NET ?>:</div>
                    </td>
                    <td width="71%" >
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <input type="text" value="<?php echo (isset($price["product_price"]) ? round(@$price["product_price"], 2) : '') ?>" class="inputbox" id="product_price" name="product_price" onchange="updateGross();, updateSPS();" size="10" maxlength="10" />
                                    <input type="hidden" name="product_price_id" value="<?php echo @$price["product_price_id"] ?>" />
                                </td>
                                <td><?php
                                    if (empty($price["product_currency"]))
                                        $price["product_currency"] = $vendor_currency;
                                    $ps_html->list_currency("product_currency", $price["product_currency"])
                                    ?>
                                </td>
                                <td>&nbsp;<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRICE_FORM_GROUP . ": " . $shopper_db->f("shopper_group_name")); ?>               
                                    <input type="hidden" name="shopper_group_id" value="<?php echo $my_shopper_group_id ?>" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!--
                <tr> 
                    <td width="29%" ><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRICE_GROSS ?>:</div>
                    </td>
                    <td width="71%" ><input type="text" class="inputbox" onkeyup="updateNet();" id="product_price_incl_tax" name="product_price_incl_tax" size="10" /></td>
                </tr>
                <tr>
                    <td width="29%" ><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_RATE_FORM_VAT_ID ?>:</div></td>
                    <td width="71%" >
                        <?php
                        require_once(CLASSPATH . 'ps_tax.php');
                        $tax_rates = ps_tax::list_tax_value("product_tax_id", $db->sf("product_tax_id"), "updateGross();")
                        ?>
                    </td>
                </tr>
                <script>/*
                 <tr> 
                 <td width="21%" ><div style="text-align:right;font-weight:bold;">Coupon Code</div></td>
                 <td width="79%" ><?php //echo ps_product_discount::coupon_discount_list($db->sf("product_coupon_discount"));         ?>
                 </td>
                 </tr>*/
                </script>-->
                <tr> 
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Discount / Savings</div></td>
                    <td width="79%" >
                        <input type="hidden" class="inputbox" name="product_discount_id"  id="product_discount_id" value="0" />
                        <!--<input type="text" class="inputbox" name="saving_price"  id="saving_price" size="15" value="<?php echo (!empty($price["saving_price"]) ? number_format($price["saving_price"], 2, '.', '') : ""); ?>" />-->
                        <input type="text" class="inputbox" onchange="updateSPS();" name="saving_price"  id="saving_price" size="15" value="<?php echo (!empty($price["saving_price"]) ? number_format($price["saving_price"], 2, '.', '') : ""); ?>" />
                        <script type="text/javascript">
                            function updateSPS() {
                                var product_price = parseFloat(document.getElementById("product_price").value);
                                var saving_price = parseFloat(document.getElementById("saving_price").value);
                                if(product_price > 0 && saving_price >= 0 && product_price >= saving_price)
                                        {
                                             document.getElementById("discounted_price").value = formatCurrency(product_price - saving_price);
                                         } else {
                                            alert('resulting price below zero, please check discount amount');
                                            document.getElementById("discounted_price").value = 0;
                                                }
                            }

                            function formatCurrency(num) {
                                num = isNaN(num) || num === '' || num === null ? 0.00 : num;
                                return parseFloat(num).toFixed(2);
                            }
                        </script>
                    </td>
                </tr>
                <tr> 
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Discounted Price</div></td>
                    <td width="79%" >
                        <!--<input type="text" class="inputbox" name="discounted_price" id="discounted_price" size="15" value="<?php echo (isset($price["saving_price"]) ? number_format($price["product_price"] - $price["saving_price"], 2, '.', '') : ""); ?>" />-->
                        <input type="text" class="inputbox" name="discounted_price" id="discounted_price" size="15" value="" />
                        <span> ingredient price <b class="price_by_ingredient">$<?php echo $normal_price; ?></b></span>

                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Special Discount For Specific Customer (percent)</div></td>
                    <td width="79%" >
                        <input type="text" class="inputbox" name="discount_for_customer" id="discount_for_customer" size="15" value="<?php echo $price["discount_for_customer"]; ?>" />
                    </td>
                </tr>

                <?php


                $sno_delivery = "";
                if (isset($rows[0]->no_delivery) AND !empty($rows[0]->no_delivery)) {
                    $sno_delivery = 'checked="checked"';
                }
                
                $sno_delivery_order = "";
                if (isset($rows[0]->no_delivery_order) AND (int)($rows[0]->no_delivery_order) == 1) {
                    $sno_delivery_order = 'checked="checked"';
                }
                $contain_alcohol = "";
                if (isset($rows[0]->contain_alcohol) AND (int)($rows[0]->contain_alcohol) == 1) {
                    $contain_alcohol = 'checked="checked"';
                }
                $is_bestseller = "";
                if (isset($rows[0]->is_bestseller) AND (int)($rows[0]->is_bestseller) == 1) {
                    $is_bestseller = 'checked="checked"';
                }
                $never_bestseller = "";
                if (isset($rows[0]->never_bestseller) AND (int)($rows[0]->never_bestseller) == 1) {
                    $never_bestseller = 'checked="checked"';
                }
                $promo = "";
                if (!empty($rows[0]->promo)) {
                    $promo = 'checked="checked"';
                }
                $sno_special = "";
                if (isset($rows[0]->no_special) AND (int)$rows[0]->no_special == 1) {
                    $sno_special = 'checked="checked"';
                }

                $snext_day_delivery = "";
                if (isset($rows[0]->next_day_delivery) AND !empty($rows[0]->next_day_delivery)) {
                    $snext_day_delivery = 'checked="checked"';
                }

                $surprise_publish = "";
                if (!empty($rows[0]->surprise_publish) AND $rows[0]->surprise_publish == 1) {
                    $surprise_publish = 'checked="checked"';
                }

                $stuefri_delivery = "";
                if (isset($rows[0]->tuefri_delivery) AND $rows[0]->tuefri_delivery == '1') {
                    $stuefri_delivery = 'checked="checked"';
                }

                $sno_tax = "";
                if (isset($rows[0]->no_tax) AND !empty($rows[0]->no_tax)) {
                    $sno_tax = 'checked="checked"';
                }

                
                
                $sfhid = "";
                if (isset($rows[0]->fhid) AND !empty($rows[0]->fhid)) {
                    $sfhid = $rows[0]->fhid;
                }

                global $mosConfig_extra_touches_cat;
                $nExtraTouchesCategory = implode(',', $mosConfig_extra_touches_cat);
                $sql = "SELECT * FROM #__vm_category WHERE category_id IN ( $nExtraTouchesCategory )";
                $database->setQuery($sql);
                $oCategories = $database->loadObjectList();
                ?>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Free Product:</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="no_delivery" <?php echo $sno_delivery; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Promo Product:</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="promo" <?php echo $promo; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Free Shipping:</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="no_delivery_order" <?php echo $sno_delivery_order; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Contain Alcohol:</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="contain_alcohol" <?php echo $contain_alcohol; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Is Best Seller:</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="is_bestseller" <?php echo $is_bestseller; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Never Best Seller:</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="never_bestseller" <?php echo $never_bestseller; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Petite</div>
                        <span style="float:right"> ingredient price <b class="price_by_ingredient">$<?php echo $petite_price; ?></b></span>
                    </td>
                    <td width="79%" >
                        <div class="add_supersize_delux">
                            <input type="text" value="<?php echo (isset($rows[0]->petite) ? $rows[0]->petite : ''); ?>" name="petite" />
                            <input type="button" value="-5">
                            <input type="button" value="-10">
                            <input type="button" value="-15">
                            <input type="button" value="-20">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Deluxe</div>
                        <span style="float:right"> ingredient price <b class="price_by_ingredient">$<?php echo $deluxe_price; ?></b></span>
                    </td>
                    <td width="79%" >
                        <div class="add_supersize_delux">
                            <input type="text" value="<?php echo (isset($rows[0]->deluxe) ? $rows[0]->deluxe : ''); ?>" name="deluxe" />
                            <input type="button" value="5">
                            <input type="button" value="10">
                            <input type="button" value="15">
                            <input type="button" value="20">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Supersize</div>
                        <span style="float:right"> ingredient price <b class="price_by_ingredient">$<?php echo $supersize_price; ?></b></span>
                    </td>
                    <td width="79%" >
                        <div class="add_supersize_delux">
                            <input type="text" value="<?php echo (isset($rows[0]->supersize) ? $rows[0]->supersize : ''); ?>" name="supersize" />
                            <input type="button" value="5">
                            <input type="button" value="10">
                            <input type="button" value="15">
                            <input type="button" value="20">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="21%" style="text-align:right;font-weight:bold;">
                        Product Type
                        </td>
                    <td width="79%" >
                        <div style="font-weight:bold;">Flowers <input name="product_type_option" type="radio" value="1" <?php echo (isset($rows[0]->product_type) AND $rows[0]->product_type == '1') ? 'checked' : ''; ?>></div>
                        <div style="font-weight:bold;">Gift Basket <input name="product_type_option" type="radio" value="2" <?php echo (isset($rows[0]->product_type) AND $rows[0]->product_type == '2') ? 'checked' : ''; ?>></div>
                        <div style="font-weight:bold;">Other <input name="product_type_option" type="radio" value="3" <?php echo (isset($rows[0]->product_type) AND $rows[0]->product_type == '3') ? 'checked' : ''; ?>></div>
                    </td>
                </tr>
                <!--#6474 YI Integrating "Subscription"-->
                 <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Subscription</div>
                    </td>
                    <td width="79%" >
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        3 month
                                    </td>
                                    <td>
                                        <input type="text" value="<?php echo $rows[0]->sub_3; ?>" name="sub_3" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        6 month
                                    </td>
                                    <td>
                                        <input type="text" value="<?php echo $rows[0]->sub_6; ?>" name="sub_6" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        12 month
                                    </td>
                                    <td>
                                        <input type="text" value="<?php echo $rows[0]->sub_12; ?>" name="sub_12" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <!--/Integrating "Subscription"-->
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Next Day Delivery</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="next_day_delivery" <?php echo $snext_day_delivery; ?>  />
                    </td>                
                </tr>
                <tr>
                    <td width="21%">
                        <div style="text-align:right;font-weight:bold;">Surprise publish</div>
                    </td>
                    <td width="79%">
                        <input type="checkbox" value="1" name="surprise_publish" <?php echo $surprise_publish; ?>  />
                    </td>
                </tr>
                <tr>
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Tue-Fri Delivery</div>
                    </td>
                    <td width="79%" >
                        <input type="checkbox" value="1" name="tuefri_delivery" <?php echo $stuefri_delivery; ?>  />
                    </td>                
                </tr>
                <tr> 
                    <td width="21%"><div style="text-align:right;font-weight:bold;" title="Special offers page will be skipped if this product is in the cart.">No special page:</div>
                    </td>
                    <td width="79%">
                        <input type="checkbox" value="1" name="no_special" <?php echo $sno_special; ?>  />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Extra Touches Menu:</div></td>
                    <td width="79%" >
                        <select name="extra_touches_menu" id="extra_touches_menu" size="1" style="font-weight:bold;">
                            <option value="">0 (-none-)</option>
                            <?php
                            if (count($oCategories)) {
                                $p = 1;
                                foreach ($oCategories as $category) {
                                    $sSelected = "";
                                    if (isset($rows[0]->extra_touches_menu) AND !empty($rows[0]->extra_touches_menu) && $rows[0]->extra_touches_menu == $category->category_id) {
                                        $sSelected = ' selected="selected" ';
                                    } else {
                                        $sSelected = ' ';
                                    }
                                    ?>
                                    <option value="<?php echo $category->category_id; ?>" <?php echo $sSelected; ?>><?php echo $p . " " . $category->category_name; ?></option>
                                    <?php
                                    $p++;
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr> 
                    <td width="21%" ><div style="text-align:right;font-weight:bold;">Compare at:</div>
                    </td>
                    <td width="79%" >
                        <input type="text" name="compare_at" value="<?php echo round(@$price["compare_at"], 2); ?>" size="15" />
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr> 
                    <td width="29%" valign="top"><div style="text-align:right;font-weight:bold;">
                            <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_S_DESC ?>:<p>Max length 255 characters</p></div>
                    </td>
                    <td width="71%"  valign="top">
                        <textarea class="inputbox" name="product_s_desc" maxlength="255"  id="short_desc" cols="35" rows="6" ><?php echo $db->sf("product_s_desc"); ?></textarea>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="adminform">
    <tr>
        <td valign="top" width="15%"><div style="font-weight:bold;">
                <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DESCRIPTION ?>:</div>
        </td>
        <td width="85%">
            <textarea id="product_desc" name="product_desc" cols="60" rows="4"><?php echo $db->sf("product_desc"); ?></textarea>
        </td>
    </tr>
    <tr>
        <td valign="top" width="15%"><div style="font-weight:bold;">
                <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DESCRIPTION ?>  For City {city_name} {city_url} {state_name} {state_short}:</div>
        </td>
        <td width="85%">
            <textarea id="product_desc_city" name="product_desc_city" cols="60" rows="4"><?php echo $db->sf("product_desc_city"); ?></textarea>
        </td>
    </tr>
</table>
<script type="text/javascript">
    CKEDITOR.replace('product_desc');
    CKEDITOR.replace('product_desc_city');
</script>

<?php
$tabs->endTab();
$tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/options.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;$status_label", "status-page");
?>

<table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform">
    <tr> 
        <td align="left" colspan="2"><?php echo "<h2>$status_label</h2>"; ?></td>
    </tr>
    <tr> 
        <td width="21%" height="2" ><div style="text-align:right;font-weight:bold;">
                <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IN_STOCK ?>:</div>
        </td>
        <td width="79%" height="2" > 
            <input type="text" class="inputbox"  name="product_in_stock" value="<?php $db->sp("product_in_stock"); ?>" size="10" />
        </td>
    </tr>
    <tr> 
        <td width="21%" ><div style="text-align:right;font-weight:bold;">
                <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_AVAILABLE_DATE ?>:</div>
        </td>
        <td width="79%" >
            <input class="inputbox" type="text" name="product_available_date" id="product_available_date" size="20" maxlength="19" value="<?php echo date('Y-m-d', $db->sf("product_available_date")); ?>" />
            <input name="reset" type="reset" class="button" onClick="return showCalendar('product_available_date', 'y-mm-dd');" value="..." />
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td valign="top" width="21%" ><div style="text-align:right;font-weight:bold;">
                <?php echo $VM_LANG->_PHPSHOP_AVAILABILITY ?>:</div>
        </td>
        <td width="79%" >
            <input type="text" class="inputbox" name="product_availability" value="<?php $db->sp("product_availability"); ?>" />
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_AVAILABILITY_TOOLTIP1); ?>
            <br /><br />
            <select class="inputbox" name="image" onchange="javascript:if (document.adminForm.image.options[selectedIndex].value != '') {
                        document.imagelib.src = '<?php echo IMAGEURL ?>availability/' + document.adminForm.image.options[selectedIndex].value;
                        document.adminForm.product_availability.value = document.adminForm.image.options[selectedIndex].value;
                    } else {
                        document.imagelib.src = '<?php echo $mosConfig_live_site ?>/images/stories/noimage.png'
                    }">
                <option value="">Select Image</option><?php
                $path = IMAGEPATH . "availability";
                $files = mosReadDirectory("$path", ".", true, true);
                foreach ($files as $file) {
                    $file_info = pathinfo($file);
                    $filename = $file_info['basename'];
                    if ($filename != "index.html") {
                        ?>
                        <option <?php echo ($db->f("product_availability") == $filename) ? "selected=\"selected\"" : "" ?> value="<?php echo $filename ?>">
                            <?php echo $filename ?>
                        </option><?php
                    }
                }
                ?>
            </select>&nbsp;
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_AVAILABILITY_TOOLTIP2); ?>
            &nbsp;&nbsp;&nbsp;
            <img src="<?php echo $db->f("product_availability") ? IMAGEURL . "availability/" . $db->sf("product_availability") : $mosConfig_live_site . "/images/stories/noimage.png"; ?>" name="imagelib" border="0" alt="Preview" />
        </td>
    <tr> 
        <td width="21%" ><div style="text-align:right;font-weight:bold;">
                <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SPECIAL ?>:</div>
        </td>
        <td width="79%" ><?php if ($db->sf("product_special") == "Y") { ?>
                <input type="checkbox" name="product_special" value="Y" checked="checked" />
            <?php } else {
                ?>
                <input type="checkbox" name="product_special" value="Y" />
            <?php }
            ?> </td>
    </tr>
    <!-- Added for the avanced attribute modification -->
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td align="right" width="21%" valign="top"><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ATTRIBUTE_LIST ?>:</div></td> 
        <td width="79%" >
            <input class="inputbox" type="text" name="product_advanced_attribute" value="<?php $db->sp("attribute"); ?>" size="64" />
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ATTRIBUTE_LIST_EXAMPLES ?></td></tr>
    <!-- END added for the advanced attribute modification --> 

    <!-- Added for the custom attribute modification -->
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td align="right" width="21%" valign="top"><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_CUSTOM_ATTRIBUTE_LIST ?>:</div></td> 
        <td width="79%" >
            <input class="inputbox" type="text" name="product_custom_attribute" value="<?php $db->sp("custom_attribute"); ?>" size="64" />
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_CUSTOM_ATTRIBUTE_LIST_EXAMPLES ?></td>
    </tr>
    <tr>
        <td align="right" style="text-align:right;vertical-align:top;">
            <b>Ingredient List: </b>
        </td>
        <td >
            <textarea name="ingredient_list" rows="10" cols="70"><?php $db->sp("ingredient_list"); ?></textarea>
        </td>
    </tr>
    <!-- END added for the custom attribute modification -->
</table>

<?php
$db_items = $ps_product->items_sql($product_id);
if (!$product_parent_id and $product_id and $db_items->num_rows() > 0) {
    ?> 
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr> 
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr> 
            <td colspan="4"><div style="text-align:right;font-weight:bold;">
                    <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_ITEMS_LBL ?></div>
            </td>
        </tr>
        <tr nowrap> 
            <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_NAME ?></td>
            <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SKU ?></td>
            <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRICE ?></td>
            <?php
            $db_heading = $ps_product->attribute_sql("", $product_id);
            while ($db_heading->next_record()) {
                ?> 
                <td><?php echo $db_heading->sf("attribute_name"); ?></td>
                <?php
            }
            ?> </tr>
        <tr> 
            <td colspan="<?php echo $db_heading->num_rows() + 3 ?>"> 
                <hr size="1" />
            </td>
        </tr>
        <?php
        while ($db_items->next_record()) {
            ?> 
            <tr nowrap> 
                <td> <?php
                    $url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&product_id=" . $db_items->f("product_id") . "&product_parent_id=$product_id";
                    echo "<a href=\"" . $sess->url($url) . "\">";
                    echo $db_items->f("product_name");
                    echo "</a>";
                    ?> </td>
                <td><?php $db_items->sp("product_sku"); ?> </td>
                <td> <?php
                    $price = $ps_product->get_price($db_items->f("product_id"));
                    $url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_price_list&product_id=" . $db_items->f("product_id") . "&product_parent_id=$product_parent_id";
                    $url .= "&return_args=" . urlencode("page=$page&product_id=$product_id");
                    echo "<a href=\"" . $sess->url($url) . "\">";
                    if ($price) {
                        if (!empty($price["item"])) {
                            echo $price["product_price"];
                        } else {
                            echo "none";
                        }
                    } else {
                        echo "none";
                    }
                    echo "</a>";
                    ?> </td>
                <?php
                $db_detail = $ps_product->attribute_sql($db_items->f("product_id"), $product_id);
                while ($db_detail->next_record()) {
                    ?> 
                    <td><?php $db_detail->p("attribute_value"); ?></td>
                    <?php
                }
                ?> </tr>
            <?php
        }
        ?> 
    </table>
    <?php
} elseif ($product_parent_id) {
    ?> 
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr> 
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr> 
            <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_ATTRIBUTES_LBL ?></strong></td>
        </tr>
        <?php
        if (!empty($_REQUEST['product_id'])) {
            $db_attribute = $ps_product->attribute_sql($product_id, $product_parent_id);
        } else {
            $db_attribute = $ps_product->attribute_sql("", $product_parent_id);
        }
        $num = 0;
        while ($db_attribute->next_record()) {
            $num++;
            ?> 
            <tr nowrap> 
                <td width="21%" height="22" > 
                    <div style="text-align:right;font-weight:bold;"><?php
                        echo $db_attribute->sf("attribute_name") . ":";
                        $field_name = "attribute_$num";
                        ?></div>
                </td>
                <td width="79%" > 
                    <input type="text" class="inputbox"  name="<?php echo $field_name; ?>" size="32" maxlength="255" value="<?php $db_attribute->sp("attribute_value"); ?>" />
                </td>
            </tr>
        <?php }
        ?> 
    </table>
    <?php
}

$tabs->endTab();
$tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/info.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" alt=\"info\" />&nbsp;$dim_weight_label", "about-page");
?>

<table class="adminform">
    <tr>
        <td width="50%"><?php
            echo "<h2>$dim_weight_label</h2>";
            ?><table>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_LENGTH ?>:</div>
                    </td>
                    <td width="79%" > 
                        <input type="text" class="inputbox"  name="product_length" value="<?php $db->sp("product_length"); ?>" size="15" maxlength="15" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_WIDTH ?>:</div>
                    </td>
                    <td width="79%" > 
                        <input type="text" class="inputbox"  name="product_width" value="<?php $db->sp("product_width"); ?>" size="15" maxlength="15" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_HEIGHT ?>:</div>
                    </td>
                    <td width="79%" > 
                        <input type="text" class="inputbox"  name="product_height" value="<?php $db->sp("product_height"); ?>" size="15" maxlength="15" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DIMENSION_UOM ?>:</div>
                    </td>
                    <td width="79%" > 
                        <input type="text" class="inputbox"  name="product_lwh_uom" value="<?php $db->sp("product_lwh_uom"); ?>" size="8" maxlength="32" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" >&nbsp;</td>
                    <td width="79%" >&nbsp;</td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT ?>:</div>
                    </td>
                    <td width="79%" > 
                        <input type="text" class="inputbox"  name="product_weight" size="15" maxlength="15" value="<?php $db->sp("product_weight"); ?>" />
                    </td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT_UOM ?>:</div>
                    </td>
                    <td width="79%" > 
                        <input type="text" class="inputbox"  name="product_weight_uom" value="<?php $db->sp("product_weight_uom"); ?>" size="8" maxlength="32" />
                    </td>
                </tr>
                <!-- Changed Packaging - Begin -->
                <tr> 
                    <td width="21%" valign="top" >&nbsp;</td>
                    <td width="21%" >&nbsp;</td>
                </tr>
                <tr> 
                    <td width="21%" valign="top" > 
                        <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_UNIT ?>:</strong></div>
                    </td>
                    <td width="21%" > 
                        <input type="text" class="inputbox"  name="product_unit" size="15" maxlength="15" value="<?php $db->sp("product_unit"); ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="21%" valign="top" > 
                        <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PACKAGING ?>:</strong></div>
                    </td>
                    <td width="21%" > 
                        <input type="text" class="inputbox"  name="product_packaging" value="<?php echo $db->f("product_packaging") & 0xFFFF; ?>" size="8" maxlength="32" />&nbsp;<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_PACKAGING_DESCRIPTION); ?>
                    </td>
                </tr>
                <tr>
                    <td width="21%" valign="top" > 
                        <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_BOX ?>:</strong></div>
                    </td>
                    <td width="21%" > 
                        <input type="text" class="inputbox"  name="product_box" value="<?php echo ($db->f("product_packaging") >> 16) & 0xFFFF; ?>" size="8" maxlength="32" />&nbsp;<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_BOX_DESCRIPTION); ?>
                    </td>
                </tr>
                <!-- Changed Packaging - End -->
            </table>
        </td>
        <td width="50%" valign="top">
            <h2><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOADABLEGOODS ?></h2>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr> 
                    <td width="31%"><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DOWNLOADABLE ?></div></td>
                    <td align="left" width="69%">
                        <input class="inputbox" <?php echo $dl_checked ?> type="checkbox" name="downloadable" onchange="javascript: if (document.adminForm.downloadable.checked == true)
                                    document.adminForm.filename.disabled = false;
                                else {
                                    document.adminForm.filename.disabled = true;
                                }" value="Y" /></td>
                </tr>
                <tr> 
                    <td width="31%"><div align="right"><?php if ($curr_filename) echo $VM_LANG->_PHPSHOP_FILES_FORM_CURRENT_FILE . ":"; ?></div></td>
                    <td valign="top" align="left" width="69%"><?php echo $curr_filename; ?>
                    </td>
                </tr>
                <tr> 
                    <td width="31%"><div style="text-align:right;font-weight:bold;"><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_FILENAME_TOOLTIP); ?>
                            &nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_FILENAME; ?>:</div></td>
                    <td valign="top" align="left" width="69%">
                        <input type="text" name="filename" class="inputbox" value="<?php echo $curr_filename; ?>" size="32" />
                    </td>
                </tr>
                <tr> 
                    <td width="31%"><div style="text-align:right;font-weight:bold;"><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_UPLOAD_TOOLTIP); ?>
                            &nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_UPLOAD ?>:</div></td>
                    <td valign="top" align="left" width="69%">
                        <input type="file" name="file_upload" class="inputbox" size="32" />
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
$tabs->endTab();
$tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/image.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;$images_label", "images-page");

if (!stristr($db->f("product_thumb_image"), "http") && $clone_product != "1")
    echo "<input type=\"hidden\" name=\"product_thumb_image_curr\" value=\"" . $db->f("product_thumb_image") . "\" />";

if (!stristr($db->f("product_full_image"), "http") && $clone_product != "1")
    echo "<input type=\"hidden\" name=\"product_full_image_curr\" value=\"" . $db->f("product_full_image") . "\" />";

$ps_html->writableIndicator(array(IMAGEPATH . "product", IMAGEPATH . "product/resized"));
?>
<table class="adminform" >
    <tr>
        <td colspan="2" style="font-weight: bold; font-size: 12px; color: #FF0000; padding:15px;">
            Size Full image should be: 600 px / 700 px<br>
            Size Thumb image should be: 225 px / 262 px<br>
        </td>
    </tr>
    <tr> 
        <td valign="top" width="50%" style="border-right: 1px solid black;">
            <h2><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_FULL_IMAGE ?></h2>
            <table>
                <tr> 
                    <td colspan="2" ><?php
                        if ($product_id) {
                            echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL . "<br />
                    <strong>Image name:</strong>&nbsp;&nbsp;&nbsp; 
                    File Name    <input type='radio' name='checkProductNameFull' value='1' checked='checked' onclick=\"selectNameVariant( 'full', '1')\" />
                    Product Name <input type='radio' name='checkProductNameFull' value='2' onclick=\"selectNameVariant( 'full', '2')\" /> <br />";
                        }
                        ?> 
                        <input type="file" class="inputbox" name="product_full_image" id="product_full_image" onchange="upload_new_image('product_full_image', 'product_full_image_new_name', 'product_full_image_new_name_result');
                                document.adminForm.product_full_image_url.value = '';
                                document.adminForm.product_full_image_action[1].checked = true;" size="50" maxlength="255" />
                        <br> <input type="text" name="product_full_image_new_name" id="product_full_image_new_name" onKeyUp="isset_name('product_full_image_new_name', 'product_full_image_new_name_result', 'product_full_image');">
                        <div id="product_full_image_new_name_result"></div>
                        <input type="hidden" name="product_full_image_new_name_hidden" id="product_full_image_new_name_hidden" value="">
                        <input type="hidden" name="product_full_image_new_name_hidden_1" id="product_full_image_new_name_hidden_1" value="">
                        <input type="hidden" name="product_full_image_new_name_hidden_2" id="product_full_image_new_name_hidden_2" value="">
                    </td>
                </tr>
                <tr> 
                    <td colspan="2" ><div style="font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_IMAGE_ACTION ?>:</div><br/>
                        <input type="radio" class="inputbox" id="product_full_image_action0" name="product_full_image_action" checked="checked" value="none" onchange="toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true);
                                toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true);"/>
                        <label for="product_full_image_action0"><?php echo $VM_LANG->_PHPSHOP_NONE; ?></label><br/>
                        <?php
// Check if GD library is available
                        if (function_exists('imagecreatefromjpeg')) {
                            ?>
                            <input type="radio" class="inputbox" id="product_full_image_action1" name="product_full_image_action" value="auto_resize" onchange="toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true);
                                    toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true);"/>
                            <label for="product_full_image_action1"><?php
                                echo $VM_LANG->_PHPSHOP_FILES_FORM_AUTO_THUMBNAIL . "</label><br />";
                            }
                            if ($product_id and $db->f("product_full_image")) {
                                ?>
                                <input type="radio" class="inputbox" id="product_full_image_action2" name="product_full_image_action" value="delete" onchange="toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true);
                                        toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true);"/>
                                <label for="product_full_image_action2"><?php
                                    echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL . "</label><br />";
                                }
                                ?> 
                                </td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr> 
                                    <td width="21%" ><?php echo _URL . " (" . _CMN_OPTIONAL . "!)&nbsp;"; ?></td>
                                    <td width="79%" >
                                        <?php
                                        if (stristr($db->f("product_full_image"), "http"))
                                            $product_full_image_url = $db->f("product_full_image");
                                        else if (!empty($_REQUEST['product_full_image_url']))
                                            $product_full_image_url = $_REQUEST['product_full_image_url'];
                                        else
                                            $product_full_image_url = "";
                                        ?>
                                        <input type="text" class="inputbox" size="50" name="product_full_image_url" value="<?php echo $product_full_image_url ?>" onchange="if (this.value.length > 0)
                                                    document.adminForm.product_full_image_action[1].checked = false;
                                                else
                                                    document.adminForm.product_full_image_action[1].checked = true;
                                                toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true);
                                                toggleDisable(document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true);" />
                                    </td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr> 
                                    <td colspan="2" >
                                        <div style="overflow:auto;">
                                            <?php
                                            if ($clone_product != "1") {
                                                echo $ps_product->image_tag($mosConfig_aws_s3_bucket_public_url . $db->f("full_image_link_webp"), "", 0);
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                </table>
                                </td>

                                <td valign="top" width="50%">
                                    <h2><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_THUMB_IMAGE ?></h2>
                                    <table>
                                        <tr> 
                                            <td colspan="2" ><?php
                                                if ($product_id) {
                                                    echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL . "<br />
                    <strong>Image name:</strong>&nbsp;&nbsp;&nbsp; 
                    File Name    <input type='radio' name='checkProductNameThumb' value='1' checked='checked' onclick=\"selectNameVariant( 'thumb', '1') \"  />
                    Product Name <input type='radio' name='checkProductNameThumb' value='2' onclick=\"selectNameVariant( 'thumb', '2')\"  /> <br />";
                                                }
                                                ?> 
                                                <input type="file" class="inputbox" name="product_thumb_image" id="product_thumb_image" size="50" maxlength="255" onchange="upload_new_image('product_thumb_image', 'product_thumb_image_new_name', 'product_thumb_image_new_name_result');
                                                        if (document.adminForm.product_thumb_image.value != '')
                                                            document.adminForm.product_thumb_image_url.value = '';" />
                                                <br> <input type="text" name="product_thumb_image_new_name" id="product_thumb_image_new_name" onKeyUp="isset_name('product_thumb_image_new_name', 'product_thumb_image_new_name_result', 'product_thumb_image');">
                                                <div id="product_thumb_image_new_name_result"></div>
                                                <input type="hidden" name="product_thumb_image_new_name_hidden" id="product_thumb_image_new_name_hidden" value="">
                                                <input type="hidden" name="product_thumb_image_new_name_hidden_1" id="product_thumb_image_new_name_hidden_1" value="">
                                                <input type="hidden" name="product_thumb_image_new_name_hidden_2" id="product_thumb_image_new_name_hidden_2" value="">
                                            </td>
                                        </tr>
                                        <tr> 
                                            <td colspan="2" ><div style="font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_IMAGE_ACTION ?>:</div><br/>
                                                <input type="radio" class="inputbox" id="product_thumb_image_action0" name="product_thumb_image_action" checked="checked" value="none" onchange="toggleDisable(document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image, true);
                                                        toggleDisable(document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image_url, true);"/>
                                                <label for="product_thumb_image_action0"><?php echo $VM_LANG->_PHPSHOP_NONE ?></label><br/>
                                                <?php if ($product_id and $db->f("product_thumb_image")) { ?>
                                                    <input type="radio" class="inputbox" id="product_thumb_image_action1" name="product_thumb_image_action" value="delete" onchange="toggleDisable(document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image, true);
                                                            toggleDisable(document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image_url, true);"/>
                                                    <label for="product_thumb_image_action1"><?php
                                                        echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL . "</label><br />";
                                                    }
                                                    ?> 
                                            </td>
                                        </tr>
                                        <tr><td colspan="2">&nbsp;</td></tr>
                                        <tr> 
                                            <td width="21%" ><?php echo _URL . " (" . _CMN_OPTIONAL . ")&nbsp;"; ?></td>
                                            <td width="79%" >
                                                <?php
                                                if (stristr($db->f("product_thumb_image"), "http"))
                                                    $product_thumb_image_url = $db->f("product_thumb_image");
                                                else if (!empty($_REQUEST['product_thumb_image_url']))
                                                    $product_thumb_image_url = $_REQUEST['product_thumb_image_url'];
                                                else
                                                    $product_thumb_image_url = "";
                                                ?>
                                                <input type="text" class="inputbox" size="50" name="product_thumb_image_url" value="<?php echo $product_thumb_image_url ?>" />
                                            </td>
                                        </tr>
                                        <tr><td colspan="2">&nbsp;</td></tr>
                                        <tr>
                                            <td colspan="2" >
                                                <div style="overflow:auto;">
                                                    <?php
                                                    if ($clone_product != "1")
                                                        echo $ps_product->image_tag($mosConfig_aws_s3_bucket_public_url . $db->f("medium_image_link_webp"), "", 0)
                                                        ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                </tr>
                                </table>

                                <?php
                                $tabs->endTab();
                                /*
                                $tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/related.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;" . $VM_LANG->_PHPSHOP_RELATED_PRODUCTS, "related-page");
                                ?>
                                <table class="adminform">
                                    <tr>
                                        <td colspan="2"><h2><?php echo $VM_LANG->_PHPSHOP_RELATED_PRODUCTS ?></h2></td>
                                    </tr>
                                    <tr>
                                        <td width="21%" valign="top">
                                            <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_INFO_MSG_PLEASE_SELECT ?>:</div>
                                            <br/><br/>
                                            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_RELATED_PRODUCTS_TIP); ?>
                                        </td>
                                        <td width="79%"><?php
                                            echo $ps_html->list_products("related_products[]", $related_products, $product_id, false);
                                            ?></td>
                                    </tr>
                                </table>

                                <?php
                                $tabs->endTab();
*/

                                $tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/info.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;Meta Information", "images-page");

                                $aMetaInfo = explode("[--2010--]", trim($db->f("meta_info")));
                                $aMetaInfoFr = explode("[--2010--]", trim($db->f("meta_info_fr")));
                                ?>
                                <table class="adminform">
                                    <tr>
                                        <td colspan="2"><h2>Meta Information(English Version)</h2></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"><b>Page Title:</b></td>
                                        <td width="90%" align="left"><input type="text" name="page_title" value="<?php echo (isset($aMetaInfo[0]) ? $aMetaInfo[0] : ''); ?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Description:</b></td>
                                        <td align="left"><textarea type="text" name="meta_description" rows="5" cols="71"><?php echo (isset($aMetaInfo[1]) ? $aMetaInfo[1] : ''); ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Keywords:</b></td>
                                        <td align="left">	
                                            <textarea type="text" name="meta_keywords" rows="5" cols="71"><?php echo (isset($aMetaInfo[2]) ? $aMetaInfo[2] : ''); ?></textarea><br/>
                                            System will replace all space by commas
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><h2>Meta Information For City {city_name} {state_name}:</h2></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"><b>Page Title:</b></td>
                                        <td width="90%" align="left"><input type="text" name="page_title_fr" value="<?php echo (isset($aMetaInfoFr[0]) ? $aMetaInfoFr[0] : ''); ?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Description:</b></td>
                                        <td align="left"><textarea type="text" name="meta_description_fr" rows="5" cols="71"><?php echo (isset($aMetaInfoFr[1]) ? $aMetaInfoFr[1] : ''); ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Keywords:</b></td>
                                        <td align="left">	
                                            <textarea type="text" name="meta_keywords_fr" rows="5" cols="71"><?php echo (isset($aMetaInfoFr[2]) ? $aMetaInfoFr[2] : ''); ?></textarea><br/>
                                            System will replace all space by commas
                                        </td>
                                    </tr>
                                </table>
                                <!-- Changed Product Type - Begin -->
                                <?php
                                $tabs->endTab();
                                $tabs->startTab("History", "images-page");

                                $dbh = new ps_DB;
                                $sql = "(SELECT *,  'stage' AS `where` FROM  `jos_vm_product_history_stage` 
                                WHERE `product_id`=".$product_id." ORDER BY `date` DESC)
                                UNION
                                (SELECT *,  'live' AS `where` FROM `jos_vm_product_history_live` 
                                WHERE `product_id`=".$product_id." ORDER BY `date` DESC)
                                ORDER BY `date` DESC";

                                $dbh->query($sql);
                                $product_histories = $dbh->loadObjectList();

                                if (sizeof($product_histories) > 0)
                                {
                                    ?>
                                    <table class="adminform" border="1">
                                        <tr>
                                            <td><h2>Name</h2></td>
                                            <td><h2>Old value</h2></td>
                                            <td><h2>New value</h2></td>
                                            <td><h2>Username</h2></td>
                                            <td><h2>Date</h2></td>
                                            <td><h2>Where</h2></td>
                                        </tr>
                                        <?php
                                        foreach ($product_histories as $product_history)
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $product_history->name; ?></td>
                                                <td><?php echo $product_history->old; ?></td>
                                                <td><?php echo $product_history->new; ?></td>
                                                <td><?php echo $product_history->username; ?></td>
                                                <td><?php echo $product_history->date; ?></td>
                                                <td><?php echo $product_history->where; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                    </table>
                                    <?php
                                }

                                $tabs->endTab();
                                // Get Product Types
                                $dba = new ps_DB;
                                $q = "SELECT * FROM #__{vm}_product_product_type_xref,#__{vm}_product_type WHERE ";
                                $q .= "#__{vm}_product_product_type_xref.product_type_id=#__{vm}_product_type.product_type_id ";
                                $q .= "AND product_id='$product_id' ";
                                /*  if (!$product_parent_id) {
                                  $q .= "AND product_id='$product_id' ";
                                  }
                                  else {
                                  $q .= "AND product_id='$product_parent_id' ";
                                  } */
                                $q .= "ORDER BY product_type_list_order";
                                $dba->query($q);

                                $dbpt = new ps_DB;
                                $dbp = new ps_DB;

                                while ($dba->next_record()) {

                                    $product_type_id = $dba->f("product_type_id");

                                    $tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/info.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;" . $dba->f("product_type_name"), "parameter-page-$product_type_id");

                                    $q = "SELECT * FROM #__{vm}_product_type_parameter WHERE ";
                                    $q .= "product_type_id='$product_type_id' ";
                                    $q .= "ORDER BY parameter_list_order";
                                    $dbpt->query($q);

                                    $q = "SELECT * FROM #__{vm}_product_type_$product_type_id WHERE ";
                                    $q .= "product_id='$product_id'";
                                    $dbp->query($q);
                                    ?>

                                    <table width="100%" border="0" cellspacing="0" cellpadding="2">
                                        <tr> 
                                            <td colspan="2" height="2" >&nbsp;</td>
                                        </tr>

                                        <?php
                                        while ($dbpt->next_record()) {
                                            if ($dbpt->f("parameter_type") != "B") {
                                                echo "<tr>\n  <td width=\"21%\" height=\"2\" valign=\"top\"><div style=\"text-align:right;font-weight:bold;\">";
                                                echo $dbpt->f("parameter_label");

                                                if ($dbpt->f("parameter_description")) {
                                                    echo "&nbsp;";
                                                    echo mm_ToolTip($dbpt->f("parameter_description"));
                                                }
                                                echo "&nbsp;:</div>\n  </td>\n  <td width=\"79%\" height=\"2\" >";

                                                $parameter_values = $dbpt->f("parameter_values");
                                                if (!empty($parameter_values)) { // List of values
                                                    $fields = explode(";", $parameter_values);
                                                    echo "<select class=\"inputbox\" name=\"product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name");

                                                    if ($dbpt->f("parameter_type") == "V") { //  Type: Multiple Values
                                                        $size = min(count($fields), 6);
                                                        echo "[]\" multiple size=\"$size\">\n";
                                                        $selected_value = array();
                                                        $get_item_value = $dbp->f($dbpt->f("parameter_name"));
                                                        $get_item_value = explode(",", $get_item_value);
                                                        foreach ($get_item_value as $value) {
                                                            $selected_value[$value] = 1;
                                                        }
                                                        foreach ($fields as $field) {
                                                            echo "<option value=\"$field\"" . (($selected_value[$field] == 1) ? " selected>" : ">") . $field . "</option>\n";
                                                        }
                                                    } else {  // Other Parameter type
                                                        echo "\">\n";
                                                        foreach ($fields as $field) {
                                                            echo "<option value=\"$field\" ";
                                                            if ($dbp->f($dbpt->f("parameter_name")) == $field)
                                                                echo "selected=\"selected\"";
                                                            echo " >" . $field . "</option>\n";
                                                        }
                                                    }
                                                    echo "</select>\n";
                                                }
                                                else { // Input field
                                                    switch ($dbpt->f("parameter_type")) {
                                                        case "I": // Integer
                                                        case "F": // Float
                                                        case "D": // Date & Time
                                                        case "A": // Date
                                                        case "M": // Time
                                                            echo "    <input type=\"text\" class=\"inputbox\"  name=\"product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name") . "\" value=\"" . $dbp->f($dbpt->f("parameter_name")) . "\" size=\"20\" />";
                                                            break;
                                                        case "T": // Text
                                                        case "S": // Short Text
                                                            echo "<textarea class=\"inputbox\" name=\"product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name") . "\" cols=\"35\" rows=\"6\" >";
                                                            echo $dbp->sf($dbpt->f("parameter_name")) . "</textarea>";
                                                            break;
                                                        case "C": // Char
                                                            echo "    <input type=\"text\" class=\"inputbox\"  name=\"product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name") . "\" value=\"" . $dbp->f($dbpt->f("parameter_name")) . "\" size=\"5\" />";
                                                            break;
                                                        case "V": // Multiple Values
                                                            echo "    <input type=\"text\" class=\"inputbox\"  name=\"product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name") . "\" value=\"" . $dbp->f($dbpt->f("parameter_name")) . "\" size=\"20\" />";

// 						$fields=explode(";",$parameter_values);
// 						echo "<select class=\"inputbox\" name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name");
// 						if ($db->f("parameter_multiselect")=="Y") {
// 							$size = min(count($fields),6);
// 							echo "[]\" multiple size=\"$size\">\n";
// 							$selected_value = array();
// 							$get_item_value = explode(",",$dbp->sf($dbpt->f("parameter_name")));
// 							foreach($get_item_value as $value) {
// 								$selected_value[$value] = 1;
// 							}
// 							foreach($fields as $field) {
// 								echo "<option value=\"$field\"".(($selected_value[$field]==1) ? " selected>" : ">"). $field."</option>\n";
// 							}
// 						}
// 						else {
// 							echo "\">\n";
// 							$get_item_value = $dbp->sf($dbpt->f("parameter_name"));
// 							foreach($fields as $field) {
// 								echo "<option value=\"$field\"".(($get_item_value==$field) ? " selected>" : ">"). $field."</option>\n";
// 							}
// 						}
// 						echo "</select>";
                                                            break;
                                                        default: // Default type Short Text
                                                            echo "    <input type=\"text\" class=\"inputbox\" name=\"product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name") . "\" value=\"" . $dbp->f($dbpt->f("parameter_name")) . "\" size=\"20\" />";
                                                    }
                                                }
                                                echo " " . $dbpt->f("parameter_unit");
                                                if ($dbpt->f("parameter_default")) {
                                                    echo " (" . $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_DEFAULT . ": ";
                                                    echo $dbpt->f("parameter_default") . ")";
                                                }
                                                echo " [ " . $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE . ": ";
                                                switch ($dbpt->f("parameter_type")) {
                                                    case "I": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_INTEGER;
                                                        break; // Integer
                                                    case "T": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TEXT;
                                                        break;  // Text
                                                    case "S": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_SHORTTEXT;
                                                        break; // Short Text
                                                    case "F": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_FLOAT;
                                                        break;  // Float
                                                    case "C": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_CHAR;
                                                        break;  // Char
                                                    case "D": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATETIME . " "; // Date & Time
                                                        echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATE_FORMAT . " ";
                                                        echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TIME_FORMAT;
                                                        break;
                                                    case "A": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATE . " ";  // Date
                                                        echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATE_FORMAT;
                                                        break;
                                                    case "M": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TIME . " ";  // Time
                                                        echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TIME_FORMAT;
                                                        break;
                                                    case "V": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_MULTIVALUE;
                                                        break;  // Multiple Value
                                                }
                                                echo " ]";
                                            } else {
                                                echo "<tr>\n  <td colspan=\"2\" height=\"2\" ><hr/>";
                                            }
                                            echo "  </td>\n</tr>";
                                        }
                                        ?>
                                        <tr> 
                                            <td colspan="2" align="right">

                                                <?php
                                                echo '<h3>' . _E_REMOVE . ' =&gt; ' . $ps_html->deleteButton("product_type_id", $product_type_id, "productProductTypeDelete", $keyword, $limitstart, "&product_id=$product_id&product_parent_id=$product_parent_id&next_page=$next_page") . '</h3>';
                                                ?>
                                            </td>
                                        </tr>

                                    </table>

                                    <?php
                                    $tabs->endTab();
                                    //<!-- Changed Product Type - End -->
                                }
                                if ($clone_product == "1") {
                                    $tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/copy_f2.gif\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />Clone Product Otions", "clone-page");
                                    echo '<input type="hidden" name="clone_product" value="Y" />';
                                    echo '<input type="hidden" name="old_product_id" value="' . $_REQUEST['product_id'] . '" />';
                                    $db_att = new ps_DB;
                                    $db->query("SELECT product_id, product_name 
                FROM #__{vm}_product
                WHERE product_parent_id='" . $_REQUEST['product_id'] . "' ");
                                    if ($db->num_rows() > 0) {
                                        echo "<h3>Also clone these Child Items:</h3>";
                                    }
                                    while ($db->next_record()) {
                                        $db_att->query("SELECT attribute_name, attribute_value FROM #__{vm}_product_attribute 
                      WHERE product_id ='" . $db->f("product_id") . "'");
                                        echo '<input type="checkbox" checked="checked" name="child_items[]" value="' . $db->f("product_id") . '" id="child_' . $db->f("product_id") . '" />
    <label for="child_' . $db->f("product_id") . '">' . $db->f("product_name") . ' (';
                                        while ($db_att->next_record()) {
                                            echo $db_att->f("attribute_name") . ": " . $db_att->f("attribute_value") . "; ";
                                        }
                                        echo ')</label><br/>';
                                    }

                                    $tabs->endTab();
                                }

                                $tabs->endPane();

// Add necessary hidden fields
                                $formObj->hiddenField('product_id', $product_id);
                                $formObj->hiddenField('product_parent_id', $product_parent_id);

                                $funcname = !empty($product_id) ? "productUpdate" : "productAdd";

// finally close the form:
                                $formObj->finishForm($funcname, $next_page, $option);
                                ?>
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
                                <script type="text/javascript" src="https://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
                                <script src="https://malsup.github.com/jquery.form.js"></script>
                                <script type="text/javascript">

                                    jQuery('select[name="product_categories[]"]').change(function (e) {
                                        var canonical_category_id_options = '';
                                        let selected_canonical_category_id = jQuery('select[name="canonical_category_id"]').val();
                                        jQuery(this).find('option:selected').each(function (i, selected) {
                                            var option_text_a = jQuery(selected).text().split('|');
                                            canonical_category_id_options += '<option value="' + jQuery(selected).val() + '" '+((selected_canonical_category_id==jQuery(selected).val())?"selected":"")+'>' + option_text_a[2].trim() + '</option>';
                                        });

                                        jQuery('select[name="canonical_category_id"]').empty().append(canonical_category_id_options);
                                    });
                                    $(function() {
                                        jQuery('.add_supersize_delux input[type=button]').click(function(){
                                            jQuery(this).parent('.add_supersize_delux').find('input[type=text]').val(jQuery(this).val())

                                        })
                                    })
                                    
                                    // BEGIN Treatment names of pictures

                                    function get_image_name(name)
                                    {
                                        name = name.replace(/\\/g, '/');
                                        name = name.substr(name.lastIndexOf('/') + 1);
                                        return name.substr(0, name.lastIndexOf('.'));
                                    }

                                    function get_file_extension(name)
                                    {
                                        name = name.replace(/\\/g, '/');
                                        name = name.substr(name.lastIndexOf('/') + 1);
                                        return name.substr(name.lastIndexOf('.') + 1);
                                    }

                                    function upload_new_image(id_upload, id_isset_name, id_report)
                                    {
                                        var upload = document.getElementById(id_upload);
                                        var elem_isset_name = document.getElementById(id_isset_name);
                                        elem_isset_name.value = get_image_name(upload.value);
                                        isset_name(id_isset_name, id_report, id_upload);
                                    }

                                    function isset_name(id_image, id_report, id_upload)
                                    {

                                        var file_extension = get_file_extension(document.getElementById(id_upload).value);
                                        var elem = document.getElementById(id_image);
                                        var elem_hidden = document.getElementById(id_image + '_hidden');
                                        var report = document.getElementById(id_report);
                                        var $j = jQuery.noConflict();
                                        $j.post("/administrator/components/com_virtuemart/classes/phpInputFilter/image_name.php",
                                                {name: elem.value,
                                                    nameProduct: document.adminForm.product_name.value,
                                                    extension: file_extension,
                                                    folder: 'product',
                                                    createNewName: true},
                                        function (data) {
                                            if (data != '') {
                                                var newNamewArr = data.split('[--1--]');
                                                document.getElementById(id_image + '_hidden_1').value = newNamewArr[0];
                                                document.getElementById(id_image + '_hidden_2').value = newNamewArr[1];
                                                /*    var inputs = document.getElementsByName("checkProductName" + ((id_image.indexOf('Full')) ? 'Full' : 'Thumb'));
                                                 var selectedValue;
                                                 for (var i = 0; i < inputs.length; i++) {
                                                 if (inputs[i].checked)
                                                 {
                                                 selectedValue = inputs[i].value;
                                                 break;
                                                 }
                                                 }*/
                                                document.getElementById(id_image).value = document.getElementById(id_image + '_hidden').value = document.getElementById(id_image + '_hidden_1'/* + selectedValue*/).value;
                                            }
                                            else {
                                                report.innerHTML = style_text(data, false);
                                                elem_hidden.value = 'ERROR!';
                                            }
                                        });
                                    }

                                    function selectNameVariant(pos, num)
                                    {
                                        var id = 'product_' + pos + '_image_new_name'; // product_full_image_new_name
                                        document.getElementById(id).value = document.getElementById(id + '_hidden').value = document.getElementById(id + '_hidden_' + num).value;
                                    }

                                    function check_for_matching_names()
                                    {
                                        if (document.getElementById('product_full_image_new_name').value == document.getElementById('product_thumb_image_new_name').value)
                                            return true;
                                        return false;
                                    }

                                    function style_text(text, flag)
                                    {
                                        var color = (flag) ? '56BA15' : 'FF0000';
                                        return '<span style="color:#' + color + ';">' + text + '</span>';
                                    }
                                    
                                     function updateDiscountedPrice() {
                                                        if (document.adminForm.product_price.value != '') {
                                                            try {
                                                                var selected_discount = document.adminForm.product_discount_id.selectedIndex;
                                                                var discountCalc = document.adminForm.product_discount_id[selected_discount].id;
                                                                var origPrice = document.adminForm.product_price_incl_tax.value;

                                                                if (discountCalc) {
                                                                    eval('var discPrice = ' + origPrice + discountCalc);
                                                                    if (discPrice != origPrice) {
                                                                        document.adminForm.discounted_price_override.value = discPrice.toFixed(2);
                                                                    }
                                                                }
                                                            } catch (e) {
                                                            }
                                                        }
                                                    }
                                      updateDiscountedPrice();              

if (jQuery('#product_price').val()!=='')
                                     {
                                         updateSPS();
                                     };
                                    // END Treatment names of pictures
                                </script>
