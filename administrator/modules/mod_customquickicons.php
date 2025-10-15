<?php
/**
 * @version $Id: mod_quickicon.php 1004 2005-11-13 17:18:18Z stingrey $
 * @package Joomla
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

if (!defined('_JOS_QUICKICON_MODULE')) {
    /** ensure that functions are declared only once */
    define('_JOS_QUICKICON_MODULE', 1);

    $components_images = array();
    global $components_images;

    function quickiconButton($link, $image, $text, $pre, $post, $newWindow) {
        global $components_images, $mosConfig_live_site;
        $components_images[] = $mosConfig_live_site . '/administrator/images/new_navi_icon/' . $image;
        ?>
        <div style="float:left;">
            <div class="icon">
                <a href="<?php echo $link; ?>"<?php echo $newWindow; ?>>
                    <img src="<?php echo $mosConfig_live_site . '/administrator/images/new_navi_icon/' . $image; ?>" />
                    <span><?php echo $pre . $text . $post; ?></span>
                </a>
            </div>
        </div>
        <?php
    }
    ?>

    <script>


        function check_new_messages_count() {
            var url = '/administrator/components/com_sms_conversation/send_get_sms.php';

            var post_data = {
                action: 'check_new_messages_count'
            }
            jQuery.post(url, post_data, function (data) {

                if (data > 0) {
                    var src = jQuery('a[href="index2.php?option=com_sms_conversation"]').find('img').attr('src');
                    src = src.replace('.png', '_new.png');
                    jQuery('a[href="index2.php?option=com_sms_conversation"]').find('img').attr('src', src)
                }

            });

        }
        check_new_messages_count()

    </script>
    <div id="cpanel">
        <?php
        global $database, $my, $orcaUrl;
        $aIconLinks = array();
        $aIconLinks[0] = "Manage Orders[--1--]manage_orders.png[--1--]index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart";
        $aIconLinks[1] = "Manage Products[--1--]manage_products.png[--1--]index2.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart";
        $aIconLinks[2] = "Manage Coupons[--1--]manage_coupons.png[--1--]index2.php?pshop_mode=admin&page=coupon.coupon_list&option=com_virtuemart";

        $aIconLinks[3] = "Manage Content[--1--]manage_content.png[--1--]index2.php?option=com_content";
        $aIconLinks[4] = "Manage Static Content[--1--]manage_static_content.png[--1--]index2.php?option=com_typedcontent";
        $aIconLinks[5] = "Manage Content Sections[--1--]manage_section.png[--1--]index2.php?option=com_sections&scope=content";
        $aIconLinks[6] = "Manage Content Categories[--1--]manage_category.png[--1--]index2.php?option=com_categories&section=content";

        $aIconLinks[7] = "View Reports[--1--]view_reports.png[--1--]index2.php?option=com_brightreporter";
        $aIconLinks[8] = "Manage Joomfish[--1--]manage_joomfish.png[--1--]index2.php?option=com_joomfish";
        $aIconLinks[9] = "Manage Users[--1--]manage_user.png[--1--]index2.php?option=com_users";
        $aIconLinks[10] = "Manage Frontend Users[--1--]manage_user.png[--1--]index2.php?pshop_mode=admin&page=admin.user_list&option=com_virtuemart";
        $aIconLinks[11] = "Manage Deliveries[--1--]manage_deliveries.png[--1--]index2.php?option=com_deliver";
        $aIconLinks[12] = "Postal Code Manager[--1--]postal_code.png[--1--]index2.php?option=com_assignorder";
        //$aIconLinks[13] = "Proflowers Order Manager[--1--]proflowers_order_manager.png[--1--]index2.php?option=com_proflower";
        $aIconLinks[14] = "Phone Order Manager[--1--]phone_order_manager.png[--1--]index2.php?option=com_phoneorder";
        $aIconLinks[15] = "User Group Manager[--1--]user_group.png[--1--]index2.php?option=com_newusergroup";
        $aIconLinks[16] = "Change Password[--1--]private_profile.png[--1--]index2.php?option=com_privateprofile";
        $aIconLinks[17] = "XML Order Manager[--1--]xml_icon.gif[--1--]index2.php?option=com_xmlorder";
        $aIconLinks[18] = "Produce Order[--1--]produce_order.png[--1--]index2.php?option=com_ajaxorder&task=searchOrderForm";
        $aIconLinks[19] = "Package Order[--1--]package_order.png[--1--]index2.php?option=com_ajaxorder&task=packageOrder";
        $aIconLinks[20] = "Ship Order[--1--]ship_order.png[--1--]index2.php?option=com_ajaxorder&task=shipOrder";
        //     $aIconLinks[58] = "Ship Order With Map[--1--]com_shiporder.png[--1--]index2.php?option=com_shiporder";
        $aIconLinks[21] = "Packaging Delivery[--1--]packaging_delivery.png[--1--]index2.php?option=com_ajaxorder&task=packagingDelivery";
//        $aIconLinks[22] = "Postal Code Manager[--1--]postal_code.png[--1--]index2.php?option=com_deliver&act=postal_code";
        $aIconLinks[23] = "Driver Option Manager[--1--]driver_option.png[--1--]index2.php?option=com_deliver&act=driver_option";
        $aIconLinks[24] = "Manage Virtuemart Tax[--1--]tax.png[--1--]index2.php?pshop_mode=admin&page=tax.tax_list&option=com_virtuemart";
        //$aIconLinks[25] = "Meta Tag Configuration[--1--]metatag_cfg.png[--1--]index2.php?option=com_phpmagicmetatag";
        //$aIconLinks[26] = "SEO Links Manager[--1--]seo_link.png[--1--]index2.php?option=com_sef";
        $aIconLinks[27] = "Location Manager[--1--]landding_page.png[--1--]index2.php?option=com_landingpages";
        $aIconLinks[61] = "Location Manager Default[--1--]landding_page.png[--1--]index2.php?option=com_landingpages_default";
        $aIconLinks[28] = "Search Log Manager[--1--]search.png[--1--]index2.php?option=com_searchlog";
        $aIconLinks[29] = "Free Shipping Manager[--1--]freeshipping.png[--1--]index2.php?option=com_deliver&act=free_shipping";
        $aIconLinks[30] = "Testimonial Manager[--1--]testimonial.png[--1--]index2.php?option=com_testimonial";
        $aIconLinks[31] = "Shipping Surcharge Manager[--1--]components.png[--1--]index2.php?option=com_deliver&act=shipping_surcharge";
        $aIconLinks[32] = "Edit Email Banner[--1--]com_edit_email_banner.png[--1--]index2.php?option=com_edit_email_banner";
        //  $aIconLinks[33] = "Edit Landing Page Products[--1--]com_edit_landing_pages.png[--1--]index2.php?option=com_edit_landing_pages";
        $aIconLinks[34] = "Edit Thin Banner[--1--]com_edit_banner.png[--1--]index2.php?option=com_edit_banner";
        //  $aIconLinks[35] = "Edit Featured Product[--1--]com_featured_product.png[--1--]index2.php?option=com_featured_product";
        //  $aIconLinks[36] = "Edit Corners[--1--]com_edit_corners.png[--1--]index2.php?option=com_edit_corners";
        //$aIconLinks[37] = "Landing Products[--1--]com_landing_products.png[--1--]index2.php?option=com_landing_products";
        //  $aIconLinks[37] = "Order feed[--1--]com_servermanager.png[--1--]index2.php?option=com_servermanager";
//        $aIconLinks[38] = "Survey[--1--]emailsurvey.png[--1--]index2.php?option=com_survey";
        $aIconLinks[39] = "Survey After Delivery[--1--]emailsurvey.png[--1--]index2.php?option=com_survey_after_delivery";
        $aIconLinks[40] = "Survey After Order[--1--]emailsurvey.png[--1--]index2.php?option=com_survey_after_order";
        //  $aIconLinks[40] 	= "Operators Codes [--1--]operatorscodes.png[--1--]index2.php?option=com_operatorscodes";
        //$aIconLinks[39] = "Edit Landing Gift Basket Categories[--1--]com_edit_landding_gift_banners.png[--1--]index2.php?option=com_gift_landding";
        //$aIconLinks[40]		= "Legacy[--1--]com_legacy.png[--1--]index2.php?option=com_legacy";
//        $aIconLinks[41] = "Undeliver Postal Code Manager[--1--]postal_code.png[--1--]index2.php?option=com_postdeliver&act=postal_code";
        //$aIconLinks[42]		= "Footer Feature[--1--]com_footer_feature.png[--1--]index2.php?option=com_footer_feature";
        $aIconLinks[43] = "Slider[--1--]com_slider.png[--1--]index2.php?option=com_slider";
        $aIconLinks[44] = "Partners[--1--]com_partners.png[--1--]index2.php?option=com_partners";
        //$aIconLinks[45] = "Exit Page Pop Up[--1--]com_servermanager.png[--1--]index2.php?option=com_exit_page_pop_up";
        $aIconLinks[46] = "Delivery date intelligence by Postcode[--1--]postal_code.png[--1--]index2.php?option=com_postcode";
        $aIconLinks[47] = "Email Sender Occoasion[--1--]email_sender_occasion.png[--1--]index2.php?option=com_email_sender_occasion";
        $aIconLinks[48] = "Manage Warehouse Orders[--1--]manage_orders.png[--1--]index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart";
        $aIconLinks[49] = "Free Shipping[--1--]com_freeshipping.png[--1--]index2.php?option=com_freeshipping";
        $aIconLinks[50] = "Unavailable Delivery by State Manager[--1--]unavailable_delivery.png[--1--]index2.php?option=com_deliver&act=unavailable_delivery";
        $aIconLinks[51] = "Thank You Page Links[--1--]postal_code.png[--1--]index2.php?option=com_thankyou_review_links";
        //$aIconLinks[52] = "Mobile Products Queue[--1--]queue.png[--1--]index2.php?option=com_virtuemart&page=product.mobilequeue";
        $aIconLinks[53] = "SMS Sender Manager[--1--]sms_sender.png[--1--]index2.php?option=com_sms_sender";
        $aIconLinks[54] = "Sms Conversation[--1--]csms_conversation.png[--1--]index2.php?option=com_sms_conversation";
        $aIconLinks[55] = "Manage ingredients[--1--]com_ingredients.png[--1--]index2.php?option=com_ingredients";
        $aIconLinks[56] = "Platinum Club Manager[--1--]platinum_cart.png[--1--]index2.php?option=com_platinum_cart";
        $aIconLinks[57] = "Company Shopper Group[--1--]company_groups.png[--1--]index2.php?option=com_company_groups";

        $aIconLinks[59] = "Extensions[--1--]com_extensions.png[--1--]index2.php?option=com_extensions";
        $aIconLinks[60] = "Warehouse orders limits[--1--]list.png[--1--]index2.php?option=com_warehouse_order_limit";
        $aIconLinks[61] = "Location Manager Default[--1--]landding_page.png[--1--]index2.php?option=com_landingpages_default";
        $aIconLinks[62] = "Credit Cards Manager[--1--]com_ccards.png[--1--]index2.php?option=com_ccards";
        $aIconLinks[63] = "Donation Vars[--1--]donation_vars.png[--1--]index2.php?option=com_donation_vars";
        $aIconLinks[64] = "Corporate Landing Pages[--1--]com_corporateapp.png[--1--]index2.php?option=com_corporateapp";
        $aIconLinks[65] = "Emails Manager[--1--]com_emails.png[--1--]index2.php?option=com_emails";
        $aIconLinks[66] = "Bulk Corporate Orders[--1--]parse_company_orders.png[--1--]index2.php?option=com_parse_company_orders";

        $aIconLinks[67] = "Create Route[--1--]com_shiporder_directions.png[--1--]index2.php?option=com_shiporder_directions";
        $aIconLinks[68] = "Route Manager[--1--]com_directions.png[--1--]index2.php?option=com_directions";
        $aIconLinks[69] = "Driver SMS Manager[--1--]com_directionsms.png[--1--]index2.php?option=com_directionsms";
        $aIconLinks[70] = "Install Manager[--1--]com_obituary_update.png[--1--]index2.php?option=com_install";
        $aIconLinks[71] = "Extended Reports[--1--]view_reports.png[--1--]index2.php?option=com_extended_reports";
        $aIconLinks[72] = "Aliases Manager[--1--]com_aliases.png[--1--]index2.php?option=com_aliases";
        $aIconLinks[73] = "MetaTags Manager[--1--]com_meta_tags.png[--1--]index2.php?option=com_metatags";
        $aIconLinks[74] = "Blocked emails[--1--]com_bad_emails.png[--1--]index2.php?option=com_bad_emails";
        $aIconLinks[75] = "Footer Links Manager[--1--]com_footer_links.png[--1--]index2.php?option=com_footer_links";
        $aIconLinks[76] = "Manage Categories[--1--]manage_products.png[--1--]index2.php?pshop_mode=admin&page=product.product_category_list&option=com_virtuemart";
        $aIconLinks[77] = "SMS Template Manager[--1--]com_emails.png[--1--]index2.php?option=com_sms_templates";
        $aIconLinks[78] = "Free Email domains[--1--]Gmail-icon.png[--1--]index2.php?option=com_free_email_domains";
        $aIconLinks[79] = "Mass Order Email and Update[--1--]email_sender.png[--1--]index2.php?option=com_email_sender";
        $aIconLinks[80] = "Driver rates[--1--]com_driver_rates.png[--1--]index2.php?option=com_driver_rates";
        $aIconLinks[81] = "SMM Manager[--1--]smm_tools.png[--1--]index2.php?option=com_smm_tools";
        $aIconLinks[82] = "Exit Popup[--1--]exit_popup_manager.png[--1--]index2.php?option=com_exit_popup";
        $aIconLinks[83] = "Promotion Products[--1--]promotion_products.png[--1--]index2.php?option=com_products_promotion";
        $aIconLinks[84] = "Stripe Orders Logs[--1--]stripe.png[--1--]index2.php?option=com_stripe_orders_logs";
        $aIconLinks[85] = "Resource Manager[--1--]resource_manager.png[--1--]index2.php?option=com_resource_manager";
        $aIconLinks[86] = "Blog Post[--1--]blog_post.webp[--1--]index2.php?option=com_blog_post";
        $aIconLinks[87] = "Disable Indexing[--1--]com_ingredients.png[--1--]index2.php?option=com_disable_indexing";

        $aIconLinks[100] = "Employees[--1--]manage_user.png[--1--]{$orcaUrl}/erp/authorize/{$my->username}";

        $aAccessAreas = array();

        $aAccessAreas['full_menus'] = "0;18;19;20;21;1;2;76;3;4;5;6;7;8;15;9;10;11;12;23;24;14;17;28;29;30;31;39;40;43;44;46;47;49;50;51;53;54;55;56;57;59;60;61;62;63;64;65;66;67;68;69;70;71;27;72;73;74;75;77;78;79;80;81;82;83;84;85;86;87";
        $aAccessAreas['manage_orders'] = "0";
        $aAccessAreas['manage_products'] = "1;76";
        $aAccessAreas['manage_coupons'] = "2";
        $aAccessAreas['manage_content'] = "3;4;5;6";
        $aAccessAreas['view_reports'] = "7";
        $aAccessAreas['view_reports_2'] = "7";
        $aAccessAreas['view_reports_3'] = "7";
        $aAccessAreas['view_reports_4'] = "7";
        $aAccessAreas['view_reports_5'] = "7";
        $aAccessAreas['manage_joomfish'] = "8";
        $aAccessAreas['add_user'] = "9";
        $aAccessAreas['user_list'] = "10";
        $aAccessAreas['manage_deliveries'] = "11";
        $aAccessAreas['postal_code_warehouse_manager'] = "12";
        $aAccessAreas['proflowers_order_manager'] = "13";
        $aAccessAreas['phone_order_manager'] = "14";
        $aAccessAreas['produce_order'] = "18";
        $aAccessAreas['package_order'] = "19";
        $aAccessAreas['ship_order'] = "20";
        $aAccessAreas['packaging_delivery'] = "21";
//        $aAccessAreas['postal_code'] = "22";
        $aAccessAreas['driver_option'] = "23";
        $aAccessAreas['tax_manager'] = "24";
        //$aAccessAreas['metatag_cfg'] = "25";
        //$aAccessAreas['seo_link'] = "26";
        $aAccessAreas['location_manager'] = "27";
        $aAccessAreas['searchlog'] = "28";
        $aAccessAreas['free_shipping'] = "29";
        $aAccessAreas['com_xmlorder'] = "17";
        $aAccessAreas['com_testimonial'] = "30";
        $aAccessAreas['shipping_surcharge'] = "31";
        $aAccessAreas['com_edit_email_banner'] = "32";
        // $aAccessAreas['com_edit_title_category'] = "33";
        $aAccessAreas['com_edit_banner'] = "34";
        //  $aAccessAreas['com_featured_product'] = "35";
        //   $aAccessAreas['com_edit_corners'] = "36";
        //$aAccessAreas['com_landing_products'] = "37";
        //   $aAccessAreas['com_servermanager'] = "37";
//        $aAccessAreas['com_survey'] = "38";
        $aAccessAreas['com_survey_after_delivery'] = "39";
        $aAccessAreas['com_survey_after_order'] = "40";
        //$aAccessAreas['com_gift_landding'] = "39";
        // $aAccessAreas['com_operatorscodes']				= "40";
        //$aAccessAreas['com_legacy']                                     = "40";
//        $aAccessAreas['com_com_postdeliver'] = "41";
        //$aAccessAreas['com_footer_feature']                                     = "42";
        $aAccessAreas['com_slider'] = "43";
        $aAccessAreas['com_partners'] = "44";
        //$aAccessAreas['com_exit_page_pop_up'] = "45";
        $aAccessAreas['com_postcode'] = "46";
        $aAccessAreas['com_email_sender_occasion'] = "47";
        $aAccessAreas['manage_warehouse_orders'] = "48";
        $aAccessAreas['com_freeshipping'] = "49";
        $aAccessAreas['unavailable_delivery'] = "50";
        $aAccessAreas['com_thankyou_review_links'] = "51";
        //$aAccessAreas['product.mobilequeue'] = "52";
        $aAccessAreas['com_sms_sender'] = "53";
        $aAccessAreas['com_sms_conversation'] = "54";
        $aAccessAreas['com_ingredients'] = "55";
        $aAccessAreas['com_platinum_cart'] = "56";
        $aAccessAreas['com_company_groups'] = "57";
        //  $aAccessAreas['com_shiporder']                           = "58";
        $aAccessAreas['com_extensions'] = "59";
        $aAccessAreas['com_warehouse_order_limit'] = "60";
        $aAccessAreas['com_landingpages_default'] = "61";
        $aAccessAreas['com_ccards'] = "62";
        $aAccessAreas['com_donation_vars'] = "63";
        $aAccessAreas['com_corporateapp'] = "64";
        $aAccessAreas['com_emails'] = "65";
        $aAccessAreas['com_parse_company_orders'] = "66";

        $aAccessAreas['com_shiporder_directions'] = "67";
        $aAccessAreas['com_directions'] = "68";
        $aAccessAreas['com_directionsms'] = "69";
        $aAccessAreas['com_install'] = "70";
        $aAccessAreas['com_extended_reports'] = "71";
        $aAccessAreas['com_aliases'] = "72";
        $aAccessAreas['com_metatags'] = "73";
        $aAccessAreas['com_bad_emails'] = "74";
        $aAccessAreas['com_footer_links'] = "75";
        $aAccessAreas['com_sms_templates'] = "77";
        $aAccessAreas['com_free_email_domains'] = "78";
        $aAccessAreas['com_email_sender'] = "79";
        $aAccessAreas['com_driver_rates'] = "80";
        $aAccessAreas['com_smm_tools'] = "81";
        $aAccessAreas['com_exit_popup'] = "82";
        $aAccessAreas['com_products_promotion'] = "83";
        $aAccessAreas['com_stripe_orders_logs'] = "84";
        $aAccessAreas['com_resource_manager'] = "85";
        $aAccessAreas['com_blog_post'] = "86";
        $aAccessAreas['com_disable_indexing'] = "87";


        $aPulicAccess = array(100,16);



        $query = "SELECT area_name FROM tbl_new_user_group AS NUG, tbl_mix_user_group AS MUG WHERE NUG.id = MUG.user_group_id AND MUG.user_id = $my->id";
        $database->setQuery($query);
        $area_name = $database->loadResult();

        if ($area_name) {
            $aAreaName = explode('[--1--]', $area_name);

            if (count($aAreaName)) {
                $bBreak = false;
                foreach ($aAreaName as $itemArea) {

                    if ($itemArea == 'edit_user' && $bBreak)
                        continue;

                    if ($itemArea == 'add_user')
                        $bBreak = true;

                    $aTemp = explode(';', $aAccessAreas[$itemArea]);

                    if (count($aTemp)) {
                        foreach ($aTemp as $menuItem) {
                            $row = explode('[--1--]', $aIconLinks[$menuItem]);
                            quickiconButton($row[2], $row[1], $row[0], "", "", "");
                        }
                    }

                    if ($itemArea == 'full_menus')
                        break;
                }
            }
        }
        foreach ($aPulicAccess as $Item) {
            $row = explode('[--1--]', $aIconLinks[$Item]);
            quickiconButton($row[2], $row[1], $row[0], "", "", "");
        }
        ?>

    </div>
    <?php
}
?>