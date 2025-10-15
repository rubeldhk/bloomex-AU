<?php
/**
 * @version $Id: admin.banners.html.php 1596 2005-12-31 05:40:31Z stingrey $
 * @package Joomla
 * @subpackage Banners
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
 * Banner clients
 * @package Joomla
 */
class HTML_NewUserGroups {

    function showNewUserGroups(&$rows, &$pageNav, $option) {
        global $my;

        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>User Groups Manager</th>
                </tr>
            </table>

            <table class="adminlist">
                <tr>
                    <th width="20">
                        #
                    </th>
                    <th width="20">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
                    </th>
                    <th align="left" nowrap>
                        User Groups
                    </th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = &$rows[$i];
                    $link = 'index2.php?option=com_newusergroup&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosHTML::idBox($i, $row->id);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td align="center">
                            <?php echo $pageNav->rowNumber($i); ?>
                        </td>
                        <td>
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>" title="User Group Name"><?php echo $row->departments_name; ?></a>
                        </td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    function NewUserGroupForm(&$row, $option) {
        mosMakeHtmlSafe($row, ENT_QUOTES, 'extrainfo');
        ?>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }
                // do field validation
                if (form.departments_name.value == "") {
                    alert("Please fill in the User Group Name.");
                } else {
                    submitform(pressbutton);
                }
            }
            //-->
        </script>
        <table class="adminheading">
                <tr>
                    <th>
                        User Group:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <form action="index2.php" method="post" name="adminForm">
                <table class="adminform">
                    <tr>
                        <th colspan="2">
                            Details
                        </th>
                    </tr>
                    <tr>
                        <td width="10%">
                            User Group Name:
                        </td>
                        <td>
                            <input class="inputbox" type="text" name="departments_name" size="50" maxlength="255" valign="top" value="<?php echo $row->departments_name; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Area Name:
                        </td>
                        <td>
                            <?php
                            $aAreaName = explode("[--1--]", $row->area_name);
                            ?>
                            <input type=checkbox name=area_name[] value='full_menus' <?php if (in_array('full_menus', $aAreaName)) echo "checked"; ?> />Full Menus &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='manage_orders' <?php if (in_array('manage_orders', $aAreaName)) echo "checked"; ?> />Manage Orders &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='manage_warehouse_orders' <?php if (in_array('manage_warehouse_orders', $aAreaName)) echo "checked"; ?> />Manage Warehouse Only Orders &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='manage_products' <?php if (in_array('manage_products', $aAreaName)) echo "checked"; ?> />Manage Products &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_ingredients' <?php if (in_array('com_ingredients', $aAreaName)) echo "checked"; ?> />Manage Ingredients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='manage_coupons' <?php if (in_array('manage_coupons', $aAreaName)) echo "checked"; ?> />Manage Coupons &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='manage_content' <?php if (in_array('manage_content', $aAreaName)) echo "checked"; ?> />Manage Content &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='view_reports' <?php if (in_array('view_reports', $aAreaName)) echo "checked"; ?> />Director+ Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='view_reports_2' <?php if (in_array('view_reports_2', $aAreaName)) echo "checked"; ?> />Sales Managers Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='view_reports_3' <?php if (in_array('view_reports_3', $aAreaName)) echo "checked"; ?> />Customer Service Managers Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='view_reports_4' <?php if (in_array('view_reports_4', $aAreaName)) echo "checked"; ?> />Sales and Customer Service Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='view_reports_5' <?php if (in_array('view_reports_5', $aAreaName)) echo "checked"; ?> />Production Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='manage_joomfish' <?php if (in_array('manage_joomfish', $aAreaName)) echo "checked"; ?> />Manage Joomfish &nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='add_user' <?php if (in_array('add_user', $aAreaName)) echo "checked"; ?> />Manage Users  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='user_list' <?php if (in_array('user_list', $aAreaName)) echo "checked"; ?> />Manage Frontend Users &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='manage_deliveries' <?php if (in_array('manage_deliveries', $aAreaName)) echo "checked"; ?> />Manage Deliveries &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='produce_order' <?php if (in_array('produce_order', $aAreaName)) echo "checked"; ?> />Produce Order &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='package_order' <?php if (in_array('package_order', $aAreaName)) echo "checked"; ?> />Package Order  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='ship_order' <?php if (in_array('ship_order', $aAreaName)) echo "checked"; ?> />Ship Order &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='packaging_delivery' <?php if (in_array('packaging_delivery', $aAreaName)) echo "checked"; ?> />Packaging Delivery  &nbsp;
                            <input type=checkbox name=area_name[] value='show_account_number' <?php if (in_array('show_account_number', $aAreaName)) echo "checked"; ?> />Show Account Number &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>			
                            <input type=checkbox name=area_name[] value='postal_code_warehouse_manager' <?php if (in_array('postal_code_warehouse_manager', $aAreaName)) echo "checked"; ?> />Postal code Warehouse Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='proflowers_order_manager' <?php if (in_array('proflowers_order_manager', $aAreaName)) echo "checked"; ?> />Proflowers Order Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='phone_order_manager' <?php if (in_array('phone_order_manager', $aAreaName)) echo "checked"; ?> />Phone Order Manager &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='postal_code' <?php if (in_array('postal_code', $aAreaName)) echo "checked"; ?> />Postal Code Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='driver_option' <?php if (in_array('driver_option', $aAreaName)) echo "checked"; ?> />Driver Option Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='tax_manager' <?php if (in_array('tax_manager', $aAreaName)) echo "checked"; ?> />Tax Manager <br/><br/>
                            <!--<input type=checkbox name=area_name[] value='metatag_cfg' <?php if (in_array('metatag_cfg', $aAreaName)) echo "checked"; ?> />Meta Tag Configuration Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='seo_link' <?php if (in_array('seo_link', $aAreaName)) echo "checked"; ?> />SEO Links Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
                            <input type=checkbox name=area_name[] value='location_manager' <?php if (in_array('location_manager', $aAreaName)) echo "checked"; ?> />Location Manager<br/><br/>		
                            <input type=checkbox name=area_name[] value='searchlog' <?php if (in_array('searchlog', $aAreaName)) echo "checked"; ?> />Search Log Manager&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='free_shipping' <?php if (in_array('free_shipping', $aAreaName)) echo "checked"; ?> />Free Shipping Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_xmlorder' <?php if (in_array('com_xmlorder', $aAreaName)) echo "checked"; ?> />XML Order Manager <br/><br/>	
                            <input type=checkbox name=area_name[] value='com_testimonial' <?php if (in_array('com_testimonial', $aAreaName)) echo "checked"; ?> />Testimonial Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='shipping_surcharge' <?php if (in_array('shipping_surcharge', $aAreaName)) echo "checked"; ?> />Shipping Surcharge Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_edit_email_banner' <?php if (in_array('com_edit_email_banner', $aAreaName)) echo "checked"; ?> />Edit Email Banner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>	
                            <input type=checkbox name=area_name[] value='com_edit_title_category' <?php if (in_array('com_edit_title_category', $aAreaName)) echo "checked"; ?> />Edit Landing page title categories &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_edit_banner' <?php if (in_array('com_edit_banner', $aAreaName)) echo "checked"; ?> />Edit Top Banners &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_featured_product' <?php if (in_array('com_featured_product', $aAreaName)) echo "checked"; ?> />Edit Featured Product &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='com_edit_corners' <?php if (in_array('com_edit_corners', $aAreaName)) echo "checked"; ?> />Edit Corners &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_landing_products' <?php if (in_array('com_landing_products', $aAreaName)) echo "checked"; ?> />Landing Products &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_partners' <?php if (in_array('com_partners', $aAreaName)) echo "checked"; ?> />Edit Local Parthners<br/><br/>
                            <input type=checkbox name=area_name[] value='com_sms_conversation' <?php if (in_array('com_sms_conversation', $aAreaName)) echo "checked"; ?> />SMS Conversation &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_sms_sender' <?php if (in_array('com_sms_sender', $aAreaName)) echo "checked"; ?> />SMS Sender &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_slider' <?php if (in_array('com_slider', $aAreaName)) echo "checked"; ?> />Slider &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_donation_vars' <?php if (in_array('com_donation_vars', $aAreaName)) echo "checked"; ?> />Donation Vars &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_shiporder' <?php if (in_array('com_shiporder', $aAreaName)) echo "checked"; ?> />Ship Order With Map &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_extensions' <?php if (in_array('com_extensions', $aAreaName)) echo "checked"; ?> />Extensions &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_emails' <?php if (in_array('com_emails', $aAreaName)) echo "checked"; ?> />Emails Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_postcode' <?php if (in_array('com_postcode', $aAreaName)) echo "checked"; ?> />Postcodes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='com_company_groups' <?php if (in_array('com_company_groups', $aAreaName)) echo "checked"; ?> />Company Shopper Group
                            <input type=checkbox name=area_name[] value='com_shiporder' <?php if (in_array('com_shiporder', $aAreaName)) echo "checked"; ?> />Ship Order With Map &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_ccards' <?php if (in_array('com_ccards', $aAreaName)) echo "checked"; ?> />Credit Cards Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_corporateapp' <?php if (in_array('com_corporateapp', $aAreaName)) echo "checked"; ?> />Corporate Landing Pages &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_parse_company_orders' <?php if (in_array('com_parse_company_orders', $aAreaName)) echo "checked"; ?> />Bulk Corporate Orders &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_shiporder_directions' <?php if (in_array('com_shiporder_directions', $aAreaName)) echo "checked"; ?> />Create Route &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_directions' <?php if (in_array('com_directions', $aAreaName)) echo "checked"; ?> />Route Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_directionsms' <?php if (in_array('com_directionsms', $aAreaName)) echo "checked"; ?> />Driver SMS Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_extended_reports' <?php if (in_array('com_extended_reports', $aAreaName)) echo "checked"; ?> />Exteded Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_aliases' <?php if (in_array('com_aliases', $aAreaName)) echo "checked"; ?> />Aliases Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_metatags' <?php if (in_array('com_metatags', $aAreaName)) echo "checked"; ?> />MetaTags Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_bad_emails' <?php if (in_array('com_bad_emails', $aAreaName)) echo "checked"; ?> />Blocked emails &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_footer_links' <?php if (in_array('com_footer_links', $aAreaName)) echo "checked"; ?> />Footer Links Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_thankyou_review_links' <?php if (in_array('com_thankyou_review_links', $aAreaName)) echo "checked"; ?> />Thank You Review Links &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_sms_templates' <?php if (in_array('com_sms_templates', $aAreaName)) echo "checked"; ?> />SMS Template Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_warehouse_order_limit' <?php if (in_array('com_warehouse_order_limit', $aAreaName)) echo "checked"; ?> />Warehouse orders limits &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=checkbox name=area_name[] value='com_email_sender' <?php if (in_array('com_email_sender', $aAreaName)) echo "checked"; ?> />Mass Order Email and Update &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='com_driver_rates' <?php if (in_array('com_driver_rates', $aAreaName)) echo "checked"; ?> />Driver Rates &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='com_smm_tools' <?php if (in_array('com_smm_tools', $aAreaName)) echo "checked"; ?> />SMM Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='com_exit_popup' <?php if (in_array('com_exit_popup', $aAreaName)) echo "checked"; ?> />Exit Popup &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
                            <input type=checkbox name=area_name[] value='com_products_promotion' <?php if (in_array('com_products_promotion', $aAreaName)) echo "checked"; ?> />Promotion Products<br/><br/>
                            <input type=checkbox name=area_name[] value='com_resource_manager' <?php if (in_array('com_resource_manager', $aAreaName)) echo "checked"; ?> />Resource Manager<br/><br/>
                            <input type=checkbox name=area_name[] value='com_blog_post' <?php if (in_array('com_blog_post', $aAreaName)) echo "checked"; ?> />Blog Post<br/><br/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="option" value="<?php echo $option; ?>" />
                <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
                <input type="hidden" name="task" value="" />
            </form>
            <?php
        }

    }
    ?>