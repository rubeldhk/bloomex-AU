<?php

/**
 * @version $Id: contact.php 4730 2006-08-24 21:25:37Z stingrey $
 * @package Joomla
 * @subpackage Contact
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

global $mainframe;

switch ($task) {

    default:
        view();
        break;
}

function view() {
    global $database, $mosConfig_live_site, $mosConfig_lang, $mm_action_url, $sess,$VM_LANG,$mosConfig_404_category, $mosConfig_absolute_path, $mainframe;
    
    $query = "SELECT 
    `p`.`product_id`
    FROM 
        `jos_vm_product` AS `p`,  
        `jos_vm_product_category_xref` AS `cx`
    WHERE 
        `cx`.`category_id`=".$mosConfig_404_category." 
        AND
        `p`.`product_id`=`cx`.`product_id`
        AND `p`.`product_publish`='Y' 
    ORDER BY RAND() LIMIT 0, 16";

    $database->setQuery($query);
    $products_obj = $database->loadObjectList();
    shuffle($products_obj);
    $products = array();

    foreach ($products_obj as $product_obj) {
        $products[] = $product_obj->product_id;
    }
    
    require_once( $mosConfig_absolute_path . '/components/com_virtuemart/virtuemart_parser.php' );
    require_once(CLASSPATH . 'vmAbstractObject.class.php' );
    require_once(CLASSPATH . 'ps_database.php' );
    require_once(CLASSPATH . 'ps_product.php' );
    $ps_product = new ps_product;

    $mainframe->setPageTitle2(trim('404 Error Page - SORRY PRODUCT NOT CURRENTLY AVAILABLE'));
    $mainframe->appendMetaTag('description', 'Send 404 Error Page Online - Same Day 404 Error Page Delivery. Order 404 Error Page Online and Discount 404 Error Page Bloomex');
    $mainframe->appendMetaTag('keywords', '404 Error Page,delivery,send');
    
    include_once './modules/breadcrumbs.php';
    ?>
    <script type="text/javascript">
        window.onload = function(e){
            document.title="404 Error Page - SORRY PRODUCT NOT CURRENTLY AVAILABLE";
        }
    </script>
    <div class="container bottom_category">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 title">
                    <div class="flower">
                        <img width="32px" height="32px"  alt="page not found" src="/templates/bloomex_adaptive/images/Flower.svg">
                    </div>
                    <h1>404 Error Page</h1>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description">
                    Sorry the product you searching for is currently unavailable. Feel free to navigate using our categories or select something from one of our "Best Sellers" listed below...
                </div>
            </div>
        </div>
    <?php
    $ps_product->show_product_list($products, true);
}

?>