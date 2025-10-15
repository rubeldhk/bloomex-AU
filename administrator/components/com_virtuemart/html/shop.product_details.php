<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: shop.product_details.php,v 1.12.2.6 2006/04/05 18:16:54 soeren_nb Exp $
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
global $mm_action_url,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $mosConfig_live_site,$mosConfig_enable_fast_checkout, $mosConfig_absolute_path, $my, $VM_LANG, $mosConfig_lang, $iso_client_lang, $cur_template, $sef;

require_once(CLASSPATH . 'ps_product_files.php' );
require_once(CLASSPATH . 'ps_product.php' );
$ps_product = new ps_product;

require_once(CLASSPATH . 'ps_product_category.php' );
$ps_product_category = new ps_product_category;

require_once(CLASSPATH . 'ps_product_attribute.php' );
$ps_product_attribute = new ps_product_attribute;

require_once(CLASSPATH . 'ps_product_type.php' );
$ps_product_type = new ps_product_type;
require_once(CLASSPATH . 'ps_reviews.php' );

/* Flypage Parameter has old page syntax: shop.flypage
 * so let's get the second part - flypage */
$flypage = mosGetParam($_REQUEST, "flypage", FLYPAGE);

$flypage = str_replace('shop.', '', $flypage);

$product_id = intval(mosgetparam($_REQUEST, "product_id", null));
$product_sku = $db->getEscaped(mosgetparam($_REQUEST, "sku", ''));
$category_id = mosgetparam($_REQUEST, "category_id", null);
$manufacturer_id = mosgetparam($_REQUEST, "manufacturer_id", null);
$Itemid = mosgetparam($_REQUEST, "Itemid", null);
$db_product = new ps_DB;

// Get the product info from the database
$q = "SELECT p.*,s.full_image_link_webp,s.full_image_link_jpeg FROM `#__{vm}_product` as p 
         left join jos_vm_product_s3_images as s on s.product_id = p.product_id 
         WHERE ";
if (!empty($product_id)) {
    $q .= " p.`product_id`=$product_id";
} elseif (!empty($product_sku)) {
    $q .= " p.`product_sku`='$product_sku'";
} else {
    mosRedirect($mosConfig_live_site . "/404-Error-Page.html");
}

if (!$perm->check("admin,storeadmin")) {
    $q .= " AND `product_publish`='Y'";
    if (CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
        $q .= " AND `product_in_stock` > 0 ";
    }
}
$db_product->query($q);

// Redirect back to Product Browse Page on Error
if (!$db_product->next_record()) {
    mosRedirect($mosConfig_live_site . "/404-Error-Page.html");
}


/** GET THE PRODUCT NAME * */
$product_name = shopMakeHtmlSafe($db_product->f("product_name"));
if ($db_product->f("product_publish") == "N")
    mosRedirect($mosConfig_live_site . "/404-Error-Page.html");

$product_description = str_replace('h1', 'h2', $db_product->f("product_desc"));
$product_description = str_replace(['half price','1/2 Price','50% off'], [''], $product_description);



if ($sef->city && $sef->city->city && $sef->city->state) {
    $healthy = [
        "{city_name}",
        "{state_name}",
        "{state_short}",
        "{city_url}"
    ];
    $yummy   = [
        $sef->city->city,
        $sef->city->state,
        $sef->city->state_short,
        $sef->city->url
    ];
    $product_desc_dirty = ($db_product->f("product_desc_city")!='') ? $db_product->f("product_desc_city") : $db_product->f("product_desc");
    $product_description = str_replace($healthy,$yummy,$product_desc_dirty);
}

/** Get the CATEGORY NAVIGATION * */
$navigation_pathway = "";
$navigation_childlist = "";
$pathway_appended = false;
if (empty($category_id)) {
    $q = "SELECT category_id FROM #__{vm}_product_category_xref WHERE product_id = '$product_id' LIMIT 0,1";
    $db->query($q);
    $db->next_record();
    if (!$db->f("category_id")) {
        // The Product Has no category entry and must be a Child Product
        // So let's get the Parent Product
        $q = "SELECT product_id FROM #__{vm}_product WHERE product_id = '" . $db_product->f("product_parent_id") . "' LIMIT 0,1";
        $db->query($q);
        $db->next_record();

        $q = "SELECT category_id FROM #__{vm}_product_category_xref WHERE product_id = '" . $db->f("product_id") . "' LIMIT 0,1";
        $db->query($q);
        $db->next_record();
    }
    $_GET['category_id'] = $category_id = $db->f("category_id");
}
//$navigation_pathway .= $ps_product_category->get_navigation_list($category_id);
//$navigation_pathway .= " " . $ps_product_category->pathway_separator() . " " . $product_name;

//if ($ps_product_category->has_childs($category_id)) {
//    $navigation_childlist .= $ps_product_category->get_child_list($category_id);
//}

/* Set Dynamic Pathway */
//$mainframe->appendPathWay($navigation_pathway);

/* Set Dynamic Page Title */
$mainframe->setPageTitle(html_entity_decode(substr($product_name, 0, 60)));

/* Prepend Product Short Description Meta Tag "description" */
$mainframe->prependMetaTag("description", strip_tags($db_product->f("product_s_desc")));


/** Show an "Edit PRODUCT"-Link ** */
if ($perm->check("admin,storeadmin")) {
    $edit_link = "<a href=\"" . sefRelToAbs($mosConfig_live_site . "/index.php?page=product.product_form&next_page=shop.product_details&product_id=$product_id&option=com_virtuemart&Itemid=$Itemid") . "\">
      <img src=\"images/M_images/edit.png\" width=\"16\" height=\"16\" alt=\"" . $VM_LANG->_PHPSHOP_PRODUCT_FORM_EDIT_PRODUCT . "\" border=\"0\" /></a>";
} else {
    $edit_link = "";
}



/** PRODUCT IMAGE * */
$product_image = "";
$full_image = $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $db_product->f("full_image_link_jpeg") : $db_product->f("full_image_link_webp"));


$product_image = '<img fetchpriority="high" src="' . $full_image . '" alt="' . $product_name . '" />';

//IMPLEMENT #5055
$aPrice = $ps_product->get_retail_price($product_id);

$aPrice["real_product_price"] = $aPrice["product_price"];
if (!empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0) {
    $aPrice["real_product_price"] = $aPrice["product_price"] - $aPrice["saving_price"];
}
//echo "<pre>";print_r($aPrice);die;
if($aPrice['promotion_discount']) {
    $aPrice["real_product_price"] = round($aPrice["real_product_price"] - $aPrice["real_product_price"]*$aPrice['promotion_discount']/100,2);


    if(date("Y-m-d") == $aPrice['end_promotion'] || $aPrice['end_promotion'] == '0000-00-00') {
        $product_image .= '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
    } else {
        $product_image .= '<div class="new promotion_product"><span>Sale Ends In: </span> <span class="promotion_countdown promotion_product_'.$product_id.'" product_id="'.$product_id.'" date_end="' . date("m/d/Y", strtotime($aPrice['end_promotion'])) . '"></span></div>';
    }

}

if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
    $aPrice["real_product_price"] = round($aPrice["real_product_price"] - $aPrice["real_product_price"]*$aPrice['discount_for_customer']/100,2);
}

$template = read_file(PAGEPATH . "templates/product_details/$flypage.php", PAGEPATH . "templates/product_details/flypage.php");

global $mainframe;
$mainframe->addMetaTag("og:image", $full_image);

global $mosConfig_lang, $database, $mosConfig_live_site,$mosConfig_show_compare_at_price;

$sql = "SELECT 
    `petite`, 
    `deluxe`, 
    `supersize`,
    `sub_3`, 
    `sub_6`, 
    `sub_12`,
    `extra_touches_menu`,
    `surprise_publish`,
    `product_type`,
    `promo`,
    `product_sold_out`,
    `product_out_of_season`,
    `no_delivery_order`,
    `is_bestseller`,
    `show_sale_overlay`,
    `no_delivery`
FROM `jos_vm_product_options` WHERE `product_id`=" . $product_id . " LIMIT 1";
$database->setQuery($sql);
$product_options = false;
$database->loadObject($product_options);
$product_out_of_season = 0;
$product_no_delivery_order = 0;
$product_show_sale_overlay = 0;
$product_is_bestseller = 0;
$product_sold_out = 0;
if ($product_options) {
    $extra_touches_menu = $product_options->extra_touches_menu;
    $petite = $product_options->petite;
    $deluxe = $product_options->deluxe;
    $supersize = $product_options->supersize;
    $free_product = (int) $product_options->no_delivery;
    $promo = $product_options->promo;
    $surprise_publish = (int) $product_options->surprise_publish;
    $product_sold_out = (int) $product_options->product_sold_out;
    $product_out_of_season = (int) $product_options->product_out_of_season;
    $product_no_delivery_order = (int) $product_options->no_delivery_order;
    $product_show_sale_overlay = (int) $product_options->show_sale_overlay;
    $product_is_bestseller = (int) $product_options->is_bestseller;
    /*$sub_3 = false;
    $sub_9 = false;
    $sub_12 = false;*/
  
      $sub_3 = round($product_options->sub_3, 2);
      $sub_6 = round($product_options->sub_6, 2);
      $sub_12 = round($product_options->sub_12, 2);
     
    $product_type = $product_options->product_type;
}

$extra_products = '';
$petite_html = '';
$regular_html = '';
$deluxe_html = '';
$supersize_html = '';

if ($extra_touches_menu) {
    $sqlCategory = "SELECT `jvc`.`category_id` FROM `jos_vm_category_xref` AS `jvcx` 
        LEFT JOIN `jos_vm_category` AS `jvc` ON `jvcx`.`category_child_id` = `jvc`.`category_id` 
        WHERE `jvcx`.`category_parent_id` = '$extra_touches_menu'";
    $database->setQuery($sqlCategory);
    $categoriesId = $database->loadObjectList();

    $categoryId = [];
    foreach($categoriesId as $category) {
        $categoryId[] = $category->category_id;
    }
    $categoryId[] = $extra_touches_menu;

    $sql = "SELECT 
        DISTINCT `P`.`product_sku`,  
        `P`.`product_name`, 
        `P`.`product_id`, 
        `P`.`product_desc`, 
        `P`.`product_thumb_image`, 
        `P`.`product_full_image`, 
        `VMP`.`product_price`,
        `VMP`.`saving_price`,
        `C`.`category_id`,
        `C`.`category_name`
    FROM `jos_vm_product` AS `P` 
    INNER JOIN `jos_vm_product_category_xref` AS `PCX` ON `P`.`product_id`=`PCX`.`product_id` 
    INNER JOIN `jos_vm_category` AS `C` ON `C`.`category_id`=`PCX`.`category_id` 
    INNER JOIN `jos_vm_product_price` AS `VMP` ON `P`.`product_id`=`VMP`.`product_id` 
    WHERE `C`.`category_id` in (" . implode(',', $categoryId) . ") AND `P`.`product_publish`='Y' 
    ORDER BY RAND()";

    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    if (count($rows)) {
        shuffle($rows);
        $products = array();

        foreach ($rows as $product_obj) {
            $products[] = $product_obj->product_id;
        }

        $extra_products = $ps_product->showExtraProductListNew($products, $categoryId);
    }
}

if (!empty($aPrice['saving_price']) AND $aPrice['saving_price'] > 0 AND $aPrice['product_price'] >= 0) {
    $product_price='';
    if ($mosConfig_show_compare_at_price){
        $product_price = '
                    <div class="compareat">
                        Compare at: 
                        <div class="old">
                            <span style="font-size: 15px"> $' . number_format($aPrice['product_price'], 2, '.', '') . '</span>
                        </div> 
                    </div> 
                    ';
    }

    $product_price .= '
                  <div>
                        <div style="font-size: 21px;font-weight: bold;color: #A40001;">
                            <span >Bloomex Price:</span> 
                            $<span  class="product_price_span">' . number_format(($aPrice['real_product_price']), 2, '.', '') . '</span>
                        </div>
                       
                    </div> 
                    
                    ';
} elseif (!empty($aPrice['product_price'])) {
    $product_price = '<div class="new" style="font-size: 21px;font-weight: bold;color: #A40001;"><span >Bloomex Price:</span> $<span  class="product_price_span">' . number_format(($aPrice['real_product_price']), 2, '.', '') . '</span></div>';
} else {
    $product_price = '<div class="new" style="font-size: 21px;font-weight: bold;color: #A40001;"><span >Bloomex Price:</span> $<span  class="product_price_span">0.00</span></div>';
}

$subscribes_html = '';
$select_bouquet = '';
if ($sub_3 > 0 OR $sub_6 > 0 OR $sub_12 > 0) {
    $VM_LANG->_PHPSHOP_PD_STEP1_TITLE = $VM_LANG->_PHPSHOP_PD_STEP1_TITLE2;
    $sub_price = 0;

    if ($sub_3 > 0) {
        $subscribes_html .= '<div class="wrapper">
            <div class="inner">
                <div class="image">
                    <img src="/templates/bloomex_adaptive/images/sub-3.png" alt="3 month" />
                </div>
                <label class="container">
                    <input type="radio" name="select_sub" id="select_sub" value="sub_3">
                    <span class="checkmark"></span>
                </label>
                <span class="title">
                    3 month
                    $' . number_format($sub_3, 2, '.', '') . '
                </span>
            </div>
        </div>';

        if ($sub_price == 0) {
            $sub_price = number_format($sub_3, 2, '.', '');
        }
    }
    if ($sub_6 > 0) {
        $subscribes_html .= '<div class="wrapper">
            <div class="inner">
                <div class="image">
                    <img src="/templates/bloomex_adaptive/images/sub-6.png" alt="6 month" />
                </div>
                <label class="container">
                    <input type="radio" name="select_sub" id="select_sub" value="sub_6">
                    <span class="checkmark"></span>
                </label>
                <span class="title">
                    6 month
                    $' . number_format($sub_6, 2, '.', '') . '
                </span>
            </div>
        </div>';

        if ($sub_price == 0) {
            $sub_price = number_format($sub_6, 2, '.', '');
        }
    }
    if ($sub_12 > 0) {
        $subscribes_html .= '<div class="wrapper">
            <div class="inner">
                <div class="image">
                    <img src="/templates/bloomex_adaptive/images/sub-12.png" alt="12 month" />
                </div>
                <label class="container">
                    <input type="radio" name="select_sub" id="select_sub" value="sub_12">
                    <span class="checkmark"></span>
                </label>
                <span class="title">
                    12 month
                    $' . number_format($sub_12, 2, '.', '') . '
                </span>
            </div>
        </div>';

        if ($sub_price == 0) {
            $sub_price = number_format($sub_12, 2, '.', '');
        }
    }

    $product_price = '<div class="new">from ' . $en_price_symbol . '<span class="product_price_span">$' . $sub_price . '</span>' . $fr_price_symbol;
} else {
    $bouquet_info_alert = '';
    if ($petite) {
        $petite_tooltip = ($product_type == '1' ?
            'Petite Bouquets are smaller while equally vibrant & thoughtful' :
            'Petite Gift Baskets are smaller while equally delicious & thoughtful');
        $bouquet_info_alert .= $petite_tooltip;
        $petite_html = '
<div class="wrapper tooltripHoverBox variant-option">
    <input type="radio" name="select_bouquet" id="petite_' . $product_id . '" value="petite"
        onclick="setBouquet(`'. $product_name.'`, `petite`,`'. $product_id.'`,`'. $petite.'`);">

    <label for="petite_' . $product_id . '">
        <div class="inner">
            <div class="image">
                <img src="/templates/bloomex_adaptive/images/' . ($product_type == '1' ? 'size-0' : 'size-basket-0') . '.png" alt="petite" />
            </div>
            <span class="title">
                ' . $VM_LANG->_PHPSHOP_PD_PETITE . ' -$' . str_replace('-','',$petite) . '
            </span>
        </div>
    </label>

    <div class="tooltripDiv variant-option__tooltripDiv">
        <span class="closeTooltrip">X</span>
        <span class="product_size_type_tooltrip">' . $petite_tooltip . '</span>
    </div>
</div>
';
    }

    if (isset($deluxe) OR isset($supersize) OR isset($petite)) {
        $regular_html = '
<div class="wrapper variant-option selected">
    <input type="radio" checked="checked" name="select_bouquet" id="standart_' . $product_id . '" value="standart"
        onclick="setBouquet(`'. $product_name.'`, `standart`,`'. $product_id.'`,`0`);">

    <label for="standart_' . $product_id . '">
        <div class="inner">
            <div class="image">
                <img src="/templates/bloomex_adaptive/images/' . ($product_type == '1' ? 'size-1' : 'size-basket-1') . '.png" alt="standart" />
            </div>
            <span class="title">' . $VM_LANG->_PHPSHOP_PD_REGULARSIZE . '</span>
        </div>
    </label>
</div>';
    }

    if ($deluxe) {
        $deluxe_tooltip = ($product_type == '1' ?
                'Deluxe Bouquets are more impressive and contain more blooms than the Standard Bouquet' :
                'Deluxe Gift Baskets are more impressive and contain more gourmet items then the standard Gift Basket');
        $bouquet_info_alert .= $deluxe_tooltip;

        $deluxe_html = '
<div class="wrapper tooltripHoverBox variant-option">
    <input type="radio" name="select_bouquet" id="deluxe_' . $product_id . '" value="deluxe"
        onclick="setBouquet(`'. $product_name.'`, `deluxe`,`'. $product_id.'`,`'. $deluxe.'`);">

    <label for="deluxe_' . $product_id . '">
        <div class="inner">
            <div class="image">
                <img src="/templates/bloomex_adaptive/images/' . ($product_type == '1' ? 'size-2' : 'size-basket-2') . '.png" alt="deluxe" />
            </div>
            <span class="title">' . $VM_LANG->_PHPSHOP_PD_DELUXESIZE . ' +$' . $deluxe . '</span>
        </div>
    </label>

    <div class="tooltripDiv variant-option__tooltripDiv">
        <span class="closeTooltrip">X</span>
        <span class="product_size_type_tooltrip">' . $deluxe_tooltip . '</span>
    </div>
</div>';
    }
    if ($supersize) {
        $supersize_tooltip = ($product_type == '1' ?
                'SUPERSIZE Bouquets are significantly more impressive and contain more blooms than the Deluxe Bouquet' :
                'SUPERSIZE Gift Baskets are significantly more impressive and contain more gourmet items then the Deluxe Gift Basket');
        $bouquet_info_alert .= '<br><br>' . $supersize_tooltip;
        $supersize_html = '
<div class="wrapper tooltripHoverBox variant-option">
    <input type="radio" name="select_bouquet" id="supersize_' . $product_id . '" value="supersize"
        onclick="setBouquet(`'. $product_name.'`, `supersize`,`'. $product_id.'`,`'. $supersize.'`);">

    <label for="supersize_' . $product_id . '">
        <div class="inner">
            <div class="image">
                <img src="/templates/bloomex_adaptive/images/' . ($product_type == '1' ? 'size-3' : 'size-basket-3') . '.png" alt="supersize" />
            </div>
            <span class="title" style="text-align: center">' . $VM_LANG->_PHPSHOP_PD_SUPERSIZESIZE . ' +$' . $supersize . '</span>
        </div>
    </label>

    <div class="tooltripDiv variant-option__tooltripDiv">
        <span class="closeTooltrip">X</span>
        <span class="product_size_type_tooltrip">' . $supersize_tooltip . '</span>
    </div>
</div>';
    }
}


//surprise
$surprise = '';
if ($surprise_publish == 1) {
    $surprise = '<div class="hidden-xs surprise">
        <img src="/templates/bloomex_adaptive/images/surprise.jpg" alt="surprise" />
    </div>';
}

if ((isset($_COOKIE['funeral_FHID']) AND isset($_COOKIE['funeral_PID']) AND isset($_COOKIE['funeral_COBRAND'])) OR in_array($category_id, array(259, 16))) {
    $surprise = '';
}

//flower below images
$flowerBelowImages = '';
$ProductIsFlower = $ps_product_category->checkProductIsFlowerType($db_product->f("product_id"));
if($ProductIsFlower)
{
    $flowerBelowImages = '
<div class="row flowerBelowImages">
        <div class="col-sm-4 col-md-4 col-lg-4 d-none d-sm-block ">
            <img alt="terms and conditions" style="" src="/images/benefits_images/FLower1.png">
            <p>Read substitution policy <a target="_blank" href="https://bloomex.com.au/terms-and-conditions/"><b>here</b></a></p>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4 d-none d-sm-block">
            <img alt="vase" style="" src="/images/benefits_images/FLower2.png">
            <p>Flowers do NOT come with vase included unless otherwise specified</p>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4 d-none d-sm-block">
            <img alt="delivery policy" style="" src="/images/benefits_images/FLower3.png">
            <p>For remote delivery location details read <a target="_blank" href="https://bloomex.com.au/delivery-policy/"><b>here</b></a></p>
        </div>
    </div>';
}

if ($product_sold_out == 1 || $product_out_of_season == 1) {
    $product_out_of_season_or_sold_out_html = '<style>
        .product_details_wrapper .row .info>div:not(.info_desktop) {
            display: none;
        }
    </style>';
    if ($product_sold_out == 1) {
        $product_out_of_season_or_sold_out_html .= '<div class="product_sold_out"><img src="/templates/bloomex_adaptive/images/sold_out_en.webp" alt="sold out" /></div>';
    } elseif ($product_out_of_season == 1) {
        $product_out_of_season_or_sold_out_html .= '<div class="product_sold_out"><img src="/templates/bloomex_adaptive/images/out_of_season.webp" alt="out of season" /></div>';
    }
}
if ( $product_no_delivery_order) {
    $product_no_delivery_order_html = '<div class="product_no_delivery_order">' . $VM_LANG->_PHPSHOP_FREE_SHIPPING_PRODUCT . '</div>';
}
if ($product_show_sale_overlay){
    $product_show_sale_overlay_html = '<div class="product_show_sale_overlay">' . $VM_LANG->_PHPSHOP_SHOW_SALE_OVERAL . '</div>';
}
if ($product_is_bestseller){
    $product_is_bestseller_html = '<div class="product_is_bestseller">' . $VM_LANG->_PHPSHOP_BEST_SELLER . '</div>';
}
$sql_r = "SELECT category_name FROM `jos_vm_category` WHERE `category_id`=" . $category_id . "";
$database->setQuery($sql_r);
$category = false;
$database->loadObject($category);

$product_real_price = ($aPrice['real_product_price']) ? number_format(($aPrice['real_product_price']), 2, '.', '') : '0.00';
$add_to_cart_form = '<div class="form-add-cart sticky-buy-box" id="div_' . $db_product->f("product_id") . '">
                    <form action="' . $mosConfig_live_site . '/index.php" method="post" name="addtocart" id="formAddToCart_' . $db_product->f("product_id") . '">
                        <input name="quantity_' . $db_product->f("product_id") . '" class="inputbox" type="hidden" size="3" value="1">
                        <div class="add" product_id="' . $db_product->f("product_id") . '">
                            ' . $VM_LANG->_PHPSHOP_PD_ADDTOCART . '
                        </div>';
                if ($mosConfig_enable_fast_checkout) {
                    $add_to_cart_form .= '<div class="add add_and_buy proceed_fast" product_id="' . $db_product->f("product_id") . '">
                                            ' . $VM_LANG->_PHPSHOP_CLICK_TO_PAY . '
                                        </div>';
                 }
$add_to_cart_form .= '<input type="hidden" name="category_id_' . $db_product->f("product_id") . '" value="' . $category_id . '">
                        <input type="hidden" name="product_id_' . $db_product->f("product_id") . '" value="' . $db_product->f("product_id") . '">
                        <input type="hidden" name="price_' . $db_product->f("product_id") . '" value="' . $product_real_price . '">
                        <input type="hidden" name="sku_' . $db_product->f("product_id") . '" value="' . $db_product->f("product_sku") . '">
                        <input type="hidden" name="name_' . $db_product->f("product_id") . '" value="' . $product_name . '">
                        <input type="hidden" name="discount_' . $db_product->f("product_id") . '" value="' . number_format($aPrice['saving_price'], 2, '.', '') . '">
                        <input type="hidden" name="category_' . $db_product->f("product_id") . '" value="' . $category->category_name . '">

                    </form>
                    <div class="trust-bar-pdp">
                        <img src="/templates/bloomex_adaptive/images/visa.svg" alt="Visa" />
                        <img src="/templates/bloomex_adaptive/images/mastercard.svg" alt="Mastercard" />
                        <span>100% Secure Transaction</span>
                    </div>
                    <div class="trust-divider"></div>
                </div>';

if ($product_sold_out == 1) {
    $add_to_cart_form= '<div class="add">Sold Out</div>';
 }
if ($aPrice['real_product_price'] == '0.00' && $free_product == 0 && $promo == '0') {
    $add_to_cart_form = '<a href="tel:1800905147"><div class="add"> Call for Pricing</div></a>';
}
$product_add_to_cart_radio_buttons = '';
$sql = "SELECT * FROM `tbl_smm_tools`";
$database->setQuery($sql);
$database->loadObject($smmTools);

$existProducts=[];
for ($i=0;$i<$_SESSION['cart']["idx"];$i++) {
    $existProducts[]=$_SESSION['cart'][$i]['product_id'];
}

$sql = "SELECT product_id FROM #__vm_product_options WHERE product_id in (".implode(',',$existProducts).") and promo='1'";
$database->setQuery($sql);
$promoProductsExist = $database->loadResult();

if($ProductIsFlower
    && $smmTools->show_free_gift_radio_buttons
    && !checkProductExistInShoppingCart($smmTools->free_gift_radio_first_product_id)
    && !checkProductExistInShoppingCart($smmTools->free_gift_radio_second_product_id)
    && !$promoProductsExist
){
    $product_add_to_cart_radio_buttons='                
                <div class="form-check">
                    <input class="form-check-input extra_product_radio" type="radio" name="extra_products" id="free_gift_'.$smmTools->free_gift_radio_first_product_id.'" value="'.$smmTools->free_gift_radio_first_product_id.'">
                    <label class="form-check-label extra_product_radio_label" for="free_gift_'.$smmTools->free_gift_radio_first_product_id.'">
                        '.$smmTools->free_gift_radio_first_product_name.'
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input extra_product_radio" type="radio" name="extra_products" id="free_gift_'.$smmTools->free_gift_radio_second_product_id.'" value="'.$smmTools->free_gift_radio_second_product_id.'">
                    <label class="form-check-label extra_product_radio_label" for="free_gift_'.$smmTools->free_gift_radio_second_product_id.'">
                        '.$smmTools->free_gift_radio_second_product_name.'
                    </label>
                </div>
                ';
}
?>

<style>
    .arrowRight {
        border: solid black;
        border-width: 0 3px 3px 0;
        display: inline-block;
        padding: 3px;
        transform: rotate(-45deg);
        -webkit-transform: rotate(-45deg);
    }

    .arrowDown {
        border: solid black;
        border-width: 0 3px 3px 0;
        display: inline-block;
        padding: 3px;
        transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
    }

</style>

<script type='text/javascript'>
    function toggleElement(title) {
        var element = document.getElementById(title);
        element.style.display = element.style.display === 'none' ? '' : 'none';

        var head = document.getElementById(title + ' head');

        head.classList.add(head.classList[0] === 'arrowDown' ? "arrowRight" : "arrowDown");
        head.classList.remove(head.classList[0] === 'arrowRight' ? "arrowRight" : "arrowDown");
    }

        let items = [];
        total_price = 0;
        items.push({
                    item_name: "<?= $product_name; ?>",
                    item_id: "<?= $db_product->f('product_sku'); ?>",
                    price: parseFloat("<?= $product_real_price; ?>").toFixed(2),
                    discount: parseFloat('<?= number_format($aPrice['saving_price'], 2, '.', ''); ?>').toFixed(2),
                    item_category: "<?= $category->category_name; ?>",
                    item_variant: 'standard',
                    quantity: '1'
                    })
        total_price += parseFloat("<?= $product_real_price; ?>");
        pushGoogleAnalytics(
            'view_item',
            items,
            total_price.toFixed(2)
        );

        (function() {
            setTimeout(function () {
                KlaviyoTracker.track('Viewed Product', {
                    "ProductName": "<?php echo $product_name; ?>",
                    "ProductID": <?php echo $product_id; ?>,
                    "SKU": "<?php echo $db_product->f("product_sku"); ?>",
                    "Categories": "<?php echo $category? $category->category_name: ''; ?>",
                    "ImageURL": "<?php echo $mosConfig_live_site."/components/com_virtuemart/shop_image/product/" . $full_image; ?>",
                    "URL": window.location.href,
                    "Price": <?php echo number_format(($aPrice['real_product_price']), 2, '.', ''); ?>,
                    "CompareAtPrice": <?php echo number_format($aPrice['saving_price'], 2, '.', ''); ?>
                })
            }, 1000);
        })();
    </script>

<?php
$template = str_replace('{petite_html}', $petite_html, $template);
$template = str_replace('{regular_html}', $regular_html, $template);
$template = str_replace('{deluxe_html}', $deluxe_html, $template);
$template = str_replace('{supersize_html}', $supersize_html, $template);
$template = str_replace('{subscribes_html}', $subscribes_html, $template);
$template = str_replace('{step_1_title}', $VM_LANG->_PHPSHOP_PD_STEP1_TITLE, $template);
$template = str_replace('{step_2_title}', $VM_LANG->_PHPSHOP_PD_STEP2_TITLE, $template);
$template = str_replace('{step_3_title}', $VM_LANG->_PHPSHOP_PD_STEP3_TITLE, $template);
$template = str_replace('{addtocart_title}', $VM_LANG->_PHPSHOP_PD_ADDTOCART, $template);
$template = str_replace('{product_name}', $product_name, $template);
$template = str_replace('{product_image}', $product_image, $template);
$template = str_replace('{full_image}', $full_image, $template); // to display the full image on flypage
$template = str_replace('{product_price}', $product_price, $template);
$template = str_replace('{select_bouquet}', $select_bouquet, $template);
$template = str_replace('{product_s_desc}', $db_product->f("product_s_desc"), $template);
$template = str_replace('{product_description}', $product_description, $template);
$template = str_replace('{product_sku}', $db_product->f("product_sku"), $template);
$template = str_replace('{product_id}', $db_product->f("product_id"), $template);
$template = str_replace('{extra_products}', $extra_products, $template);
$template = str_replace('{surprise}', $surprise, $template);
$template = str_replace('{flowerBelowImages}', $flowerBelowImages, $template);
$template = str_replace('{mosConfig_live_site}', $mosConfig_live_site, $template);
$template = str_replace('{h1}', $sef->h1, $template);
$template = str_replace('{product_out_of_season_or_sold_out_html}', ($product_out_of_season_or_sold_out_html ?? ""), $template);
$template = str_replace('{product_no_delivery_order_html}', ($product_no_delivery_order_html ?? ""), $template);
$template = str_replace('{product_is_bestseller_html}', ($product_is_bestseller_html ?? ""), $template);
$template = str_replace('{product_show_sale_overlay_html}', ($product_show_sale_overlay_html ?? ""), $template);
$template = str_replace('{add_to_cart_form}', $add_to_cart_form, $template);
$template = str_replace('{bouquet-info-alert}', $bouquet_info_alert, $template);
$template = str_replace('{product_add_to_cart_radio_buttons}', $product_add_to_cart_radio_buttons, $template);

$template = str_replace('{product_url}', $mosConfig_live_site.$_SERVER['REQUEST_URI'], $template);
$template = str_replace('{product_name_text}', htmlspecialchars($product_name, ENT_QUOTES), $template);
$template = str_replace('{product_image_url}', $mosConfig_live_site."/components/com_virtuemart/shop_image/product/" . $full_image, $template);
$template = str_replace('{product_description_text}',  htmlspecialchars(strip_tags(str_replace(array("\r", "\n"), '', $product_description)), ENT_QUOTES), $template);
$template = str_replace('{product_price_number}', number_format(($aPrice['real_product_price']), 2, '.', ''), $template);
$template = str_replace('{product_rating}', $review_obj->rating, $template);
$template = str_replace('{product_review_count}', $review_obj->review_count, $template);

echo $template;
?>