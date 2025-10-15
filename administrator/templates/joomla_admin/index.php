<?php
/**
 * @version $Id: index.php 4801 2006-08-28 16:10:28Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or die('Restricted access');
global $mosConfig_live_site, $mosConfig_ajaxservices,$mosConfig_ga4_gtm,$my;




//============================ NEW USER GROUP ====================================
if ($my->gid != 25) {
    global $database;
    $aAccessAreas = array();
    $aAccessAreas['manage_orders'] = "option=com_virtuemart&page=order.";
    $aAccessAreas['manage_products'] = "option=com_virtuemart&page=product.";
    $aAccessAreas['manage_coupons'] = "option=com_virtuemart&page=coupon.";
    $aAccessAreas['manage_content'] = "option=com_content[--1--]option=com_typedcontent[--1--]option=com_sections&scope=content[--1--]option=com_categories&section=content";
    $aAccessAreas['view_reports'] = "option=com_brightreporter";
    $aAccessAreas['view_reports_2'] = "option=com_brightreporter";
    $aAccessAreas['view_reports_3'] = "option=com_brightreporter";
    $aAccessAreas['view_reports_4'] = "option=com_brightreporter";
    $aAccessAreas['view_reports_5'] = "option=com_brightreporter";
    $aAccessAreas['com_extended_reports'] = "option=com_extended_reports";
    $aAccessAreas['manage_joomfish'] = "option=com_joomfish";
    $aAccessAreas['add_user'] = "option=com_users&task=view[--1--]option=com_users&task=new[--1--]option=com_virtuemart&page=admin.user_list[--1--]option=com_virtuemart&page=admin.user_form&task=new[--1--]option=com_users&task=editA[--1--]option=com_users&task=edit[--1--]option=com_virtuemart&page=admin.user_form&task=edit";
    $aAccessAreas['user_list'] = "option=com_virtuemart&page=admin.";
    $aAccessAreas['manage_deliveries'] = "option=com_deliver";
    $aAccessAreas['postal_code_warehouse_manager'] = "option=com_assignorder";
    $aAccessAreas['proflowers_order_manager'] = "option=com_proflower";
    $aAccessAreas['phone_order_manager'] = "option=com_phoneorder";
    $aAccessAreas['private_profile'] = "option=com_privateprofile";
    $aAccessAreas['show_account_number'] = "";
    $aAccessAreas['metatag_cfg'] = "option=com_phpmagicmetatag";
    $aAccessAreas['seo_link'] = "option=com_sef";
    $aAccessAreas['produce_order'] = "option=com_ajaxorder&task=searchOrderForm";
    $aAccessAreas['package_order'] = "option=com_ajaxorder&task=packageOrder";
    $aAccessAreas['ship_order'] = "option=com_ajaxorder&task=shipOrder";
    $aAccessAreas['packaging_delivery'] = "option=com_ajaxorder&task=packagingDelivery";
    $aAccessAreas['postal_code'] = "option=com_deliver&act=postal_code[--1--]option=com_deliver&act=postal_code&task=editA";
    $aAccessAreas['driver_option'] = "option=com_deliver&act=driver_option[--1--]option=com_deliver&act=driver_option&task=editA";
    $aAccessAreas['tax_manager'] = "option=com_virtuemart&page=tax.";
    $aAccessAreas['location_manager'] = "option=com_landingpages";
    $aAccessAreas['com_llp'] = "option=com_llp";
    $aAccessAreas['searchlog'] = "option=com_searchlog";
    $aAccessAreas['free_shipping'] = "option=com_deliver&act=free_shipping[--1--]option=com_deliver&act=free_shipping&task=editA";
    $aAccessAreas['com_xmlorder'] = "option=com_xmlorder";
    $aAccessAreas['com_testimonial'] = "option=com_testimonial";
    $aAccessAreas['shipping_surcharge'] = "option=com_deliver&act=shipping_surcharge[--1--]option=com_deliver&act=shipping_surcharge&task=editA";
    $aAccessAreas['com_edit_email_banner'] = "option=com_edit_email_banner";
    $aAccessAreas['com_edit_title_category'] = "option=com_edit_title_category";
    $aAccessAreas['com_edit_banner'] = "option=com_edit_banner";
    $aAccessAreas['com_featured_product'] = "option=com_featured_product";
    $aAccessAreas['com_edit_corners'] = "option=com_edit_corners";
    $aAccessAreas['com_partners'] = "option=com_partners";
    $aAccessAreas['com_landing_products'] = "option=com_landing_products";
    $aAccessAreas['com_postcode'] = "option=com_postcode";
    $aAccessAreas['com_shiporder'] = "option=com_shiporder";
    $aAccessAreas['com_slider'] = "option=com_slider";
    $aAccessAreas['manage_warehouse_orders'] = "option=com_virtuemart&page=order.";
    $aAccessAreas['com_donation_vars'] = "option=com_donation_vars";
    $aAccessAreas['com_emails'] = "option=com_emails";
    $aAccessAreas['com_sms_conversation'] = "option=com_sms_conversation";
    $aAccessAreas['com_sms_sender'] = "option=com_sms_sender";
    $aAccessAreas['com_platinum_cart'] = "option=com_platinum_cart";
    $aAccessAreas['com_company_groups'] = "option=com_company_groups";
    $aAccessAreas['com_extensions'] = "option=com_extensions";
    $aAccessAreas['com_corporateapp'] = "option=com_corporateapp";
    $aAccessAreas['com_parse_company_orders'] = "option=com_parse_company_orders";
    $aAccessAreas['com_directions'] = "option=com_directions";
    $aAccessAreas['com_shiporder_directions'] = "option=com_shiporder_directions";
    $aAccessAreas['com_directionsms'] = "option=com_directionsms";
    $aAccessAreas['com_aliases'] = "option=com_aliases";
    $aAccessAreas['com_metatags'] = "option=com_metatags";
    $aAccessAreas['com_bad_emails'] = "option=com_bad_emails";
    $aAccessAreas['com_thankyou_review_links'] = "option=com_thankyou_review_links";
    $aAccessAreas['com_footer_links'] = "option=com_footer_links";
    $aAccessAreas['com_sms_templates'] = "option=com_sms_templates";
    $aAccessAreas['com_warehouse_order_limit'] = "option=com_warehouse_order_limit";
    $aAccessAreas['com_free_email_domains'] = "option=com_free_email_domains";
    $aAccessAreas['com_email_sender'] = "option=com_email_sender";
    $aAccessAreas['com_driver_rates'] = "option=com_driver_rates";
    $aAccessAreas['com_smm_tools'] = "option=com_smm_tools";
    $aAccessAreas['com_products_promotion'] = "option=com_products_promotion";
    $aAccessAreas['com_exit_popup'] = "option=com_exit_popup";
    $aAccessAreas['com_stripe_orders_logs'] = "option=com_stripe_orders_logs";
    $aAccessAreas['com_resource_manager'] = "option=com_resource_manager";
    $aAccessAreas['com_blog_post'] = "option=com_blog_post";
    $aAccessAreas['com_disable_indexing'] = "option=com_disable_indexing";

    $option = isset($_REQUEST['option']) ? trim($_REQUEST['option']) : '';
    $task = isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
    $act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : '';
    $scope = isset($_REQUEST['scope']) ? trim($_REQUEST['scope']) : '';
    $section = isset($_REQUEST['section']) ? trim($_REQUEST['section']) : '';
    $user_id = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : '';
    $sValidAreas = "";

    if ($option) {
        $sValidAreas = "option=$option";
    }

    if ($option == 'com_virtuemart') {
        $sValidAreas .= "&page=$page";

        if ($page == "admin.user_form" && !$user_id)
            $sValidAreas .= "&task=new";

        if ($page == "admin.user_form" && $user_id)
            $sValidAreas .= "&task=edit";
    } else {
        if ($option == 'com_sections' && $scope) {
            $sValidAreas .= "&scope=$scope";
        } elseif ($option == 'com_categories' && $section) {
            $sValidAreas .= "&section=$section";
        } elseif ($option != '' && $act) {
            $sValidAreas .= "&act=$act";
        } elseif ($option != '' && $task) {
            $sValidAreas .= "&task=$task";
        } elseif ($option == 'com_users' && !$task) {
            $sValidAreas .= "&task=view";
        }
    }

    $my->prevs->warehouse_only = false;
    if ($option != 'com_privateprofile') {
        $query = "SELECT area_name FROM tbl_new_user_group AS NUG, tbl_mix_user_group AS MUG WHERE NUG.id = MUG.user_group_id AND MUG.user_id = $my->id";
        $database->setQuery($query);
        $area_name = $database->loadResult();

        $bAccess = false;
        if ($area_name) {
            $aAreaName = explode('[--1--]', $area_name);

            foreach ($aAreaName as $areaNameItem) {

                if ($areaNameItem != 'full_menus') {
                    $aTemp = explode("[--1--]", $aAccessAreas[$areaNameItem]);
                    if (count($aTemp) && $aAccessAreas[$areaNameItem]) {
                        foreach ($aTemp as $findme) {
                            $pos = strpos($sValidAreas, $findme);

                            if ($pos === false) {
                                $bAccess = false;
                            } else {
                                $bAccess = true;
                                break;
                            }
                        }
                    }
                    if ($bAccess)
                        break;
                }
            }
        }

        if (!$bAccess && $sValidAreas) {
            echo mosRedirect('index2.php', _NOT_AUTH);
        }
    }
}
//=================================================================================


$tstart = mosProfiler::getmicrotime();
?>
<html>
    <head>
        <title><?php echo $mosConfig_sitename; ?> - Administration [Joomla]</title>
        <link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/administrator/templates/joomla_admin/css/template_css.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/administrator/templates/joomla_admin/css/theme.css" type="text/css" />
        <script language="JavaScript" src="<?php echo $mosConfig_live_site; ?>/includes/js/JSCookMenu_mini.js" type="text/javascript"></script>
        <script language="JavaScript" src="<?php echo $mosConfig_live_site; ?>/administrator/includes/js/ThemeOffice/theme.js" type="text/javascript"></script>
        <script language="JavaScript" src="<?php echo $mosConfig_live_site; ?>/includes/js/joomla.javascript.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/administrator/templates/<?php echo $cur_template; ?>/js/jquery.js"></script>

        <script type="text/javascript">
            jQuery(document).ready(function () {

                jQuery('.seo_name').keyup(function () {
                    var seo_alias = jQuery(this).val().replace(/\s/g, '-');
                    seo_alias = seo_alias.replace(/\$|'|"|<|>|\!|\||@|#|%|^|\^|\\|\/|&|\*|\(\)|\|\/|;|\+|,|\?|:|{|}|\[|\]/g, '').toLowerCase();

                    jQuery('.seo_alias').val(seo_alias);
                });

            });
        </script>
        <?php
        include_once( $mosConfig_absolute_path . '/editor/editor.php' );
        initEditor();
        ?>
        <meta http-equiv="Content-Type" content="text/html; UTF-8" />
        <meta name="Generator" content="Joomla! Content Management System" />
        <link rel="shortcut icon" href="<?php echo $mosConfig_live_site . '/images/favicon.ico'; ?>" />
        <?php if ($option == 'com_phoneorder' || $option == 'com_parse_company_orders') { ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer', '<?php echo $GLOBALS['mosConfig_ga4_gtm']; ?>');</script>
        <!-- End Google Tag Manager -->
        <?php } ?>
        <script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/google_analitics.js?renew=4"></script>
    </head>
    <?php
    $url = $mosConfig_live_site . '/administrator/';
    ?>

    <body onload="MM_preloadImages('<?php echo $url; ?>images/help_f2.png', '<?php echo $url; ?>images/archive_f2.png', '<?php echo $url; ?>images/back_f2.png', '<?php echo $url; ?>images/cancel_f2.png', '<?php echo $url; ?>images/delete_f2.png', '<?php echo $url; ?>images/edit_f2.png', '<?php echo $url; ?>images/new_f2.png', '<?php echo $url; ?>images/preview_f2.png', '<?php echo $url; ?>images/publish_f2.png', '<?php echo $url; ?>images/save_f2.png', '<?php echo $url; ?>images/unarchive_f2.png', '<?php echo $url; ?>images/unpublish_f2.png', '<?php echo $url; ?>images/upload_f2.png')">
    <?php if ($option == 'com_phoneorder' || $option == 'com_parse_company_orders') { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $mosConfig_ga4_gtm; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php } ?>
    <div id="wrapper">
            <div id="header">
                <div id="joomla">
                    <img src="<?php echo $mosConfig_live_site; ?>/administrator/templates/joomla_admin/images/header_text.png" alt="Joomla! Logo" />
                </div>
            </div>
        </div>
        <table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="menubackgr" style="padding-left:5px;">
                    <?php mosLoadAdminModule('fullmenu'); ?>
                </td>
                <td class="menubackgr" align="right">
                    <div id="wrapper1">
                        <?php mosLoadAdminModules('header', 2); ?>
                    </div>
                </td>
                <td class="menubackgr" align="right" style="padding-right:5px;">
                    <a href="index2.php?option=logout" style="color: #333333; font-weight: bold">
                        Logout</a>
                    <strong><?php echo $my->username; ?></strong>
                </td>
            </tr>
        </table>

        <table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="menudottedline" width="40%">
                    <?php mosLoadAdminModule('pathway'); ?>
                </td>
                <td class="menudottedline" align="right">
                    <?php mosLoadAdminModule('toolbar'); ?>
                </td>
            </tr>
        </table>

        <br />
        <?php mosLoadAdminModule('mosmsg'); ?>

        <div align="center" class="centermain">
            <div class="main">
                <?php mosMainBody_Admin(); ?>
            </div>
        </div>

        <div align="center" class="footer">
            <table width="99%" border="0">
                <tr>
                    <td align="center">
                        <div align="center">
                            <?php echo $_VERSION->URL; ?>
                        </div>
                        <div align="center" class="smallgrey">
                            <?php echo $version; ?>
                            <br />
                            <a href="http://bloomex.com.au/administrator/index2.php?option=com_brightreporter&act=reports" target="_blank">Check for latest Version</a>
                        </div>
                        <?php
                        if ($mosConfig_debug) {
                            echo '<div class="smallgrey">';
                            $tend = mosProfiler::getmicrotime();
                            $totaltime = ($tend - $tstart);
                            printf("Page was generated in %f seconds", $totaltime);
                            echo '</div>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php mosLoadAdminModules('debug'); ?>
    </body>
</html>
