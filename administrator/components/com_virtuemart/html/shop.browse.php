<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 * This is the Main Product Listing File!
 *
 * @version $Id: shop.browse.php,v 1.10.2.10 2006/04/23 19:40:07 soeren_nb Exp $
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

global $manufacturer_id, $keyword1, $keyword2, $search_category, $DescOrderBy, $search_limiter;
global $search_op, $orderby, $product_type_id, $default, $vmInputFilter, $VM_BROWSE_ORDERBY_FIELDS, $mm_action_url, $my;

//echo "<h3>".$VM_LANG->_PHPSHOP_BROWSE_LBL."</h3>\n";
global $mm_action_url, $mosConfig_absolute_path, $my, $VM_LANG, $mosConfig_lang, $mosConfig_live_site, $mosConfig_404_category,$mosConfig_enable_fast_checkout;

global $database, $sef,$city_obj;


    $iso_client_lang = 'en';

$categories_array = array('179','328','329','330');

?>

<script type="text/javascript">
    sVM_EDIT = "<?php echo $VM_LANG->_VM_EDIT; ?>";
    sVM_DELETE = "<?php echo $VM_LANG->_VM_DELETE; ?>";
    sVM_DELETING = "<?php echo $VM_LANG->_VM_DELETING; ?>";
    sVM_UPDATING = "<?php echo $VM_LANG->_VM_UPDATING; ?>";
    sVM_ADD_ADDRESS = "<?php echo $VM_LANG->_VM_ADD_ADDRESS; ?>";
    sVM_UPDATE_ADDRESS = "<?php echo $VM_LANG->_VM_UPDATE_ADDRESS; ?>";

    sVM_ADD_PRODUCT_SUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_PRODUCT_SUCCESSFUL; ?>";
    sVM_ADD_PRODUCT_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_PRODUCT_UNSUCCESSFUL; ?>";
    sVM_CONFIRM_DELETE = "<?php echo $VM_LANG->_VM_CONFIRM_DELETE; ?>";
    sVM_DELETE_SUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_SUCCESSFUL; ?>";
    sVM_DELETE_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_UNSUCCESSFUL; ?>";
    sVM_CONFIRM_QUANTITY = "<?php echo $VM_LANG->_VM_CONFIRM_QUANTITY; ?>";
    sVM_UPDATE_CART_ITEM_SUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_CART_ITEM_SUCCESSFUL; ?>";
    sVM_UPDATE_CART_ITEM_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_CART_ITEM_UNSUCCESSFUL; ?>";

    sVM_CONFIRM_FIRST_NAME = "<?php echo $VM_LANG->_VM_CONFIRM_FIRST_NAME; ?>";
    sVM_CONFIRM_LAST_NAME = "<?php echo $VM_LANG->_VM_CONFIRM_LAST_NAME; ?>";
    sVM_CONFIRM_ADDRESS = "<?php echo $VM_LANG->_VM_CONFIRM_ADDRESS; ?>";
    sVM_CONFIRM_CITY = "<?php echo $VM_LANG->_VM_CONFIRM_CITY; ?>";
    sVM_CONFIRM_ZIP_CODE = "<?php echo $VM_LANG->_VM_CONFIRM_ZIP_CODE; ?>";
    sVM_CONFIRM_VALID_ZIP_CODE = "<?php echo $VM_LANG->_VM_CONFIRM_VALID_ZIP_CODE; ?>";
    sVM_CONFIRM_COUNTRY = "<?php echo $VM_LANG->_VM_CONFIRM_COUNTRY; ?>";
    sVM_CONFIRM_STATE = "<?php echo $VM_LANG->_VM_CONFIRM_STATE; ?>";
    sVM_CONFIRM_PHONE_NUMBER = "<?php echo $VM_LANG->_VM_CONFIRM_PHONE_NUMBER; ?>";
    sVM_CONFIRM_EMAIL = "<?php echo $VM_LANG->_VM_CONFIRM_EMAIL; ?>";
    sVM_CONFIRM_ADD_NICKNAME = "<?php echo $VM_LANG->_VM_CONFIRM_ADD_NICKNAME; ?>";

    sVM_DELETING_DELIVER_INFO = "<?php echo $VM_LANG->_VM_DELETING_DELIVER_INFO; ?>";
    sVM_DELETE_DELIVER_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_DELIVER_INFO_SUCCESSFUL; ?>";
    sVM_DELETE_DELIVER_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_DELIVER_INFO_UNSUCCESSFUL; ?>";
    sVM_UPDATING_DELIVER_INFO = "<?php echo $VM_LANG->_VM_UPDATING_DELIVER_INFO; ?>";
    sVM_UPDATE_DELIVER_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_DELIVER_INFO_SUCCESSFUL; ?>";
    sVM_UPDATE_DELIVER_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_DELIVER_INFO_UNSUCCESSFUL; ?>";
    sVM_ADD_DELIVER_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_DELIVER_INFO_SUCCESSFUL; ?>";
    sVM_ADD_DELIVER_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_DELIVER_INFO_UNSUCCESSFUL; ?>";
    sVM_LOAD_DELIVER_INFO_FORM_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_LOAD_DELIVER_INFO_FORM_UNSUCCESSFUL; ?>";

    sVM_UPDATING_BILLING_INFO = "<?php echo $VM_LANG->_VM_UPDATING_BILLING_INFO; ?>";
    sVM_UPDATE_BILLING_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_BILLING_INFO_SUCCESSFUL; ?>";
    sVM_UPDATE_BILLING_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_BILLING_INFO_UNSUCCESSFUL; ?>";

</script>
<?php
$db_browse = new ps_DB;
$dbp = new ps_DB;
$db = new ps_DB;

/* load important class files */
require_once CLASSPATH . 'ps_product.php';
$ps_product = new ps_product;
require_once CLASSPATH . 'ps_product_category.php';
$ps_product_category = new ps_product_category;
require_once CLASSPATH . 'ps_reviews.php';
?>

<script type="text/javascript">
    sSecurityUrl = "<?php echo ( SECUREURL != "" ? SECUREURL : $mm_action_url ); ?>";
    bMember = <?php echo $my->id; ?>;
</script>

<?php
$Itemid = mosgetparam($_REQUEST, "Itemid", null);

$keyword_product = $vmInputFilter->safeSQL(urldecode(mosGetParam($_REQUEST, 'keyword_product', null)));
$keyword1 = $vmInputFilter->safeSQL(urldecode(mosGetParam($_REQUEST, 'keyword1', null)));
$keyword2 = $vmInputFilter->safeSQL(urldecode(mosGetParam($_REQUEST, 'keyword2', null)));
// possible values: [ASC|DESC]
$DescOrderBy = $vmInputFilter->safeSQL(mosGetParam($_REQUEST, 'DescOrderBy', "ASC"));
$search_limiter = $vmInputFilter->safeSQL(mosGetParam($_REQUEST, 'search_limiter', null));
$search_op = $vmInputFilter->safeSQL(mosGetParam($_REQUEST, 'search_op', null));
// possible values:
// product_name, product_price, product_sku, product_cdate (=latest additions)
// $orderby = $vmInputFilter->safeSQL( mosGetParam( $_REQUEST, 'orderby', VM_BROWSE_ORDERBY_FIELD ));
$orderby = $vmInputFilter->safeSQL(mosGetParam($_REQUEST, 'orderby', 'product_price'));

$default['category_flypage'] = FLYPAGE;

if (!empty($category_id)) {
    /**
     * CATEGORY DESCRIPTION
     */

    $desc = $sef->meta_source->data['category']->category_description;
    if ($city_obj && $city_obj->city && $city_obj->state) {
        $desc = str_replace('{state_name}',$city_obj->state,str_replace('{city_name}',$city_obj->city,$sef->meta_source->data['category']->category_description_city));
    }


    $desc = (trim(str_replace('<br />', '', $desc)) != '') ? $desc : null;
    $Obituary = null;
    if (isset($_COOKIE['funeral_FHID']) & isset($_COOKIE['funeral_PID']) & isset($_COOKIE['funeral_COBRAND'])) {
        $pid = $_COOKIE['funeral_PID'];
        $fhid = $_COOKIE['funeral_FHID'];
        $cobrand = $_COOKIE['funeral_COBRAND'];
        $url = 'http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?';
        $url .= "fhid=" . $fhid;
        $url .= "&cobrand=" . $cobrand;
        $url .= "&pid=" . $pid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        $funeral = json_decode($json);
        $Obituary = ( isset($funeral->Obituary) ) ? $funeral->Obituary : null;
        $desc = str_replace('[PID]', $Obituary->FullName, $desc);
    }

    if ($Obituary) {
        ?>
        <div class="container bottom_category obituary">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 title t2">
                    <div class="flower">
                        <img width="32px" height="32px"  alt="Flower" src="/templates/bloomex_adaptive/images/Flower.svg">
                    </div>
                    Send Flowers in Memory of <h1><?php echo $Obituary->FullName; ?></h1>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description">
                </div>
            </div>
        </div>
        <?php
    }
    else {
        include_once './modules/breadcrumbs.php';
        ?>
        <div class="container bottom_category">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 title t3">
                    <div class="flower">
                        <img width="32px" height="32px"  alt="Flower" src="/templates/bloomex_adaptive/images/Flower.svg">
                    </div>
                    <?php
                    $checkCategoryName = $sef->h1;
                    if ($sef->landing_type > 0) {
                        ?>
                        <span class="landing_title"><?php echo $checkCategoryName; ?></span>
                        <?php
                    } 
                    else {
                        ?>
                        <h1 class="landing_title"><?php echo $checkCategoryName; ?></h1>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description">
                    <?php 
                    if ($sef->landing_type > 0) {
                        Switch ($sef->landing_type) {
                            case 1:
                                $landing_type = 'landing';
                            break;
                        
                            case 2:
                                $landing_type = 'basket';
                            break;
                        
                            case 3:
                                $landing_type = 'sympathy';
                            break;

                            case 4:
                                $landing_type = 'flower-delivery';
                            break;
                        }

                        $query = "SELECT 
                            `category`,
                            `en_left_pop`,
                            `en_center_pop`,
                            `en_right_pop`,
                            `right_pop_publish`
                        FROM `tbl_landing_pages_info` AS `lp`
                        WHERE 
                            `lp`.`landing_url` LIKE 'default'
                            AND 
                            `lp`.`type` LIKE '".$landing_type."'
                        ";
                        
                        $landing_obj = false;
                        $database->setQuery($query);

                        if ($database->loadObject($landing_obj)) {
                            $from = array('{city_name}', '{state_name}', '{city_url}');
                            $to = array($GLOBALS['city_obj']->city, $GLOBALS['city_obj']->state, $GLOBALS['city_obj']->url);
                            $Page = [
                                'province' => $GLOBALS['city_obj']->province,
                                'left_pop' => str_replace($from, $to, $landing_obj->en_left_pop),
                                'center_pop' => str_replace($from, $to, $landing_obj->en_center_pop),
                                'right_pop' => str_replace($from, $to, $landing_obj->en_right_pop),
                                'right_pop_publish' => $landing_obj->right_pop_publish,
                            ];
                            

                            $category_id = unserialize($landing_obj->category)[0];
                            
                            $landing_content = [];
                            
                            if (strstr($GLOBALS['city_obj']->description, '{readmore}') !== false) {
                                $landing_content = explode('{readmore}', $GLOBALS['city_obj']->description);
                                $desc = $landing_content[0];
                            }
                            else {
                                $desc = $GLOBALS['city_obj']->description;
                            }

                            if (isset($landing_content[1]) AND !empty($landing_content[1])) {
                                $desc .= '<span class="landing_content_more_btn"> more...</span><span class="landing_content_more">'.$landing_content[1].'</span>';
                            }
                        }
                    }
                    elseif (isset($GLOBALS['city_obj'])) {
                        $landing_content = [];
                        if (strstr($GLOBALS['city_obj']->description, '{readmore}') !== false) {
                            $landing_content = explode('{readmore}', $GLOBALS['city_obj']->description);
                            $desc = $landing_content[0];
                        }
                        else {
                            $desc = $GLOBALS['city_obj']->description;
                        }

                        if (isset($landing_content[1]) AND !empty($landing_content[1])) {
                            $desc .= '<span class="landing_content_more_btn"> more...</span><span class="landing_content_more">'.$landing_content[1].'</span>';
                        }
                    }
                    elseif (!empty($sef->description_text)) {
                        $desc = $sef->description_text;
                    }
                    
                    echo strip_tags($desc, '<a><span><strong><p>'); 
                    ?>
                </div>
            </div>

        </div>
        <?php
    }
}

$query = "SELECT 
    `p`.`product_id`,
    (`pp`.`product_price`-`pp`.`saving_price`) AS `product_real_price`
FROM 
    `jos_vm_product_category_xref` AS `cx`
INNER JOIN `jos_vm_product` AS `p`
    ON `p`.`product_id`=`cx`.`product_id` 
    AND
    `p`.`product_parent_id`='0' 
    AND 
    `p`.`product_publish`='Y' 
LEFT JOIN `jos_vm_product_price` AS `pp` 
    ON `pp`.`product_id`=`p`.`product_id`
WHERE 
    `cx`.`category_id`=" . $category_id . "       
ORDER BY RAND(p.`product_id`)";
if ($sef->landing_type > 0) {
    $query .= " LIMIT 24";
}
$database->setQuery($query);
$products_obj = $database->loadObjectList();

$products = array();

$lowPrice = 0;
$highPrice = 0;

$i = 1;
foreach ($products_obj as $product_obj) {
    if ($i == 1) {
        $lowPrice = $product_obj->product_real_price;
        $highPrice = $product_obj->product_real_price;
    }
    else {
        if ($lowPrice > $product_obj->product_real_price) {
            $lowPrice = $product_obj->product_real_price;
        }
        if ($highPrice < $product_obj->product_real_price) {
            $highPrice = $product_obj->product_real_price;
        }
    }
    $products[] = $product_obj->product_id;
    $i++;
}
?>

<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Product",
    "name": "<?php echo $checkCategoryName; ?>",
    "offers": {
        "@type": "AggregateOffer",
        "lowPrice": "<?php echo number_format((float) $lowPrice, 2, '.', ''); ?>",
        "highPrice ": "<?php echo number_format((float) $highPrice, 2, '.', ''); ?>",
        "priceCurrency": "AUD",
        "availability": "In stock"
    }
}
</script>
<?php


$discpunt_group = false;
    
if ($my->id) {
    $query = "SELECT `SG`.`discount_price`
        FROM `#__vm_shopper_vendor_xref` AS `SVX` 
        INNER JOIN `#__vm_shopper_group` AS `SG` ON `SG`.`shopper_group_id`=`SVX`.`shopper_group_id`
    WHERE `SVX`.`user_id`=" . $my->id . " LIMIT 1";

    $database->setQuery($query);
    $Discount = $database->loadResult();
    if (count($Discount) > 0) {
        $discpunt_group = true;
    }
}

if ($discpunt_group == true) {
    global $mosConfig_lang;
    ?>
    <div class="container discount_group">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 title t4">
                <?php echo $mosConfig_lang == 'french' ? 'Votre rabais sera appliquer l\'ors de lachat' : 'Your Discount will be applied at Checkout'; ?>
            </div>
        </div>
    </div>
    <?php
}
?>
<div class="container subcategories">
    <div class="row">
        <?php
//        $nav_list = $ps_product_category->get_navigation_list($category_id);

        $child_list = $ps_product_category->get_child_list_new($category_id);
        if (!empty($child_list)) {
            echo $child_list;
        }
        ?>
    </div>
</div>
<?php

if (in_array($category_id, $categories_array)) {
    switch ($mosConfig_lang) {
        case 'french':
            $sImage = "checkout_special_fr.png";
            $pc_image = '/templates/bloomex7/images/half_price_shipping_french.jpg';
            $vc_image = '/templates/bloomex7/images/save__10_french.jpg';
            break;

        case 'english':
        default:
            $sImage = "checkout_special.png";
            $pc_image = '/templates/bloomex7/images/Half_Price_Shipping_banner_NEW.jpg';
            $vc_image = '/templates/bloomex7/images/Save__10_on_next_purchase_banner.jpg';
            break;
    }
    ?>
    <div class="container cart_wrapper">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 buttons">
                <a class="proceed" href="/checkout/">
                    <?php echo $VM_LANG->_PHPSHOP_PROCEED_CHECKOUT; ?>
                </a>
                <?php
                if($mosConfig_enable_fast_checkout) {
                    echo ' <a class="proceed_fast" href="/fast-checkout/">'.$VM_LANG->_PHPSHOP_CLICK_TO_PAY.' </a>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}


$limit = (isMobileDevice() ? 12 : 24);
$totalPages = ceil(count($products) / $limit);
$page = 1;
if (preg_match('/page=(\d+)/i', $_SERVER['QUERY_STRING'], $matches)) {
    $page = (int) $matches[1];
}
if($page > $totalPages) {
    $page = $totalPages;
}
echo $ps_product->show_product_list($products, ($sef->landing_type > 0 ? true : false),($page - 1) * $limit);

 if ($totalPages > 1):
    $urlPerPage =$sef->real_uri;
    ?>
    <div class="row mt-5">
        <div class="col text-center">
            <nav>
                <ul class="pagination justify-content-center <?= (isMobileDevice() ? 'pagination-sm' : 'pagination-lg') ?>">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="/<?= $urlPerPage ?>/<?= ($page > 2) ? '?page='.($page - 1) : '' ?>">« Prev</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="/<?= $urlPerPage ?>/<?= ($i!=1) ? '?page='.$i:'' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="/<?= $urlPerPage ?>/?page=<?= $page + 1 ?>">Next »</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
<?php endif;
if ( $sef->landing_type > 0) {
    mosLoadModules('botBodyFrm', -1);
}
echo "<div class='container'>
            <div class='row'> 
                <div class='col-12 col-sm-2 ms-auto' style='float: right'>
            
                <p class=\"sort_by_select\">Sort by price<span class=\"glyphicon ".$sorting_class." \"></span></p>

                </div>
            </div>
        </div>";


                
    if (!empty($sef->description_text_footer)) {
        ?>
        <div class="container description-footer">
            <div class="row">
                <div class="col-xs-12">
                    <div class="text">
                        <?php echo $sef->description_text_footer; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

?>
