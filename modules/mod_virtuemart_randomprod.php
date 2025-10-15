<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/* Random Products Module
*
* @version $Id: mod_virtuemart_randomprod.php,v 1.4 2005/11/24 19:18:49 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
* @copyright (C) Mr PHP
// W: www.mrphp.com.au
// E: info@mrphp.com.au
// P: +61 418 436 690
* Conversion to Mambo and the rest:
* 	@copyright (C) 2004-2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/
global $sef;
require_once CLASSPATH.'ps_product.php';

$ps_product = new ps_product;

if (empty($category_id)) {
    $category_id = $params->get( 'category_id', null );
}

if ($category_id > 0) {
    $query = "SELECT 
        `p`.`product_id`
    FROM `jos_vm_product_category_xref` AS `pc_x`
    INNER JOIN `jos_vm_product` AS `p`
        ON
        `p`.`product_id`=`pc_x`.`product_id`
        AND
        `p`.`product_publish`='Y' 

    WHERE
        `pc_x`.`category_id`=".$category_id." 
    GROUP BY `p`.`product_id`
    ORDER BY RAND() LIMIT 0, 16";
}
else {
    $query = "SELECT 
    `p`.`product_id`
    FROM (
        `jos_vm_product` AS `p`
    ) 
    WHERE 
        `p`.`product_publish`='Y' 
    order by RAND() LIMIT 0, 16";
}
date_default_timezone_set('Australia/Sydney');
if (isset($_REQUEST['option']) &&  $_REQUEST['option'] == 'com_best_seller') {
 $query="SELECT  product_id FROM jos_vm_product_options WHERE is_bestseller = '1'";
}

$database->setQuery($query);
$products_obj = $database->loadObjectList();

$products = array();

foreach ($products_obj as $product_obj) {
    $products[] = $product_obj->product_id;
}
$product_ordering_a = array(
    1 => array(
        'title' => 'sort by rating', 
        'type' => 'desc'
    ),
    2 => array(
        'title' => 'sort by price', 
        'type' => 'desc'
    ),
    3 => array(
        'title' => 'sort by price', 
        'type' => 'asc'
    ),
);

$product_ordering = isset($_COOKIE['product_ordering']) ? $_COOKIE['product_ordering'] : '';
$sorting_class = '';
if($product_ordering==='desc'){
    $sorting_class ='glyphicon-sort-by-attributes-alt';
}elseif($product_ordering==='asc'){
    $sorting_class ='glyphicon-sort-by-attributes';
}

include_once './modules/breadcrumbs.php';

if(isset($_REQUEST['option']) &&  $_REQUEST['option'] == 'com_page_not_found'){
    ?>
    <script type="text/javascript">
        window.onload = function(e){
            document.title="404 Error Page - SORRY PRODUCT NOT CURRENTLY AVAILABLE";
        }
    </script><br>
    <div class="container bottom_category">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 title t6">
                <div class="flower">
                    <img width="32px" height="32px" alt="page not found" src="/templates/bloomex_adaptive/images/Flower.svg">
                </div>
                <h1>404 Error Page</h1>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description">
                Sorry the product you searching for is currently unavailable. Feel free to navigate using our categories or select something from one of our "Best Sellers" listed below...
            </div>
        </div>
    </div>
    <?php

} elseif ($sef->homepage || (isset($_REQUEST['option']) &&  $_REQUEST['option'] == 'com_best_seller')) {
    ?>
    <div class="container bottom_category">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 title mt-3">
                <div class="flower">
                    <img width="32px" height="32px"  alt="page not found" src="/templates/bloomex_adaptive/images/Flower.svg">
                </div>
                <h1 class="landing_title"><?php echo $sef->h1; ?></h1>
                <?php if($_REQUEST['option'] != 'com_best_seller'){?>
                    <p class="sort_by_select">Sort by price<span class="glyphicon <?php echo $sorting_class; ?>"></span></p>
                <?php }?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description">
                <?php
                $landing_content = [];

                if (strstr($sef->description_text, '{readmore}') !== false) {
                    $landing_content = explode('{readmore}', $sef->description_text);
                    $desc = $landing_content[0];
                }
                else {
                    $desc = $sef->description_text;
                }

                if (isset($landing_content[1]) AND !empty($landing_content[1])) {
                    $desc .= '<span class="landing_content_more_btn"> more...</span><span class="landing_content_more">'.$landing_content[1].'</span>';
                }
                echo  strip_tags( $desc, '<a><span><strong><p>'); ?>
            </div>
        </div>
    </div>
<style>
    @media(max-width:767px){
        .bottom_category .title h1, .landing_title {
            font-size: 11px;
        }
    }
</style>

    <?php
}
echo $ps_product->show_product_list($products, true);


?>
