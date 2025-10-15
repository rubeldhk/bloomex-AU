<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

$mainframe->setPageTitle( _SEARCH_TITLE );
global $my;
require_once $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php';
require_once CLASSPATH.'ps_product.php';
$ps_product = new ps_product;

$searchword = isset($_REQUEST['searchword']) ? trim($_REQUEST['searchword']) : '';

include_once $mosConfig_absolute_path.'/modules/breadcrumbs.php';

$searchword = $database->getEscaped($searchword);

if (strlen($searchword) >= 3) {
    
    $query = "SELECT GROUP_CONCAT(`category_id` SEPARATOR ',') AS `categories_ids`
    FROM `jos_vm_category_unsearchable`
    ";
    
    $database->setQuery($query);
    $unsearchable = false;
    $database->loadObject($unsearchable);
    
    $products = [];
    
    $max_results = 24;
    
    $query = "SELECT 
        `p`.`product_id`,
        (`pp`.`product_price`-`pp`.`saving_price`) AS `product_real_price`
    FROM `jos_vm_product` AS `p`
        LEFT JOIN `jos_vm_product_price` AS `pp` on `pp`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_product_category_xref` AS `pc_xref` ON `pc_xref`.`product_id`=`p`.`product_id` AND `pc_xref`.`category_id` NOT IN (".$unsearchable->categories_ids.")
        INNER JOIN `jos_vm_product_options` AS `po`
            ON `po`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_category` AS `c`
            ON `c`.`category_id`=`po`.`canonical_category_id`
            AND
            `c`.`category_publish`='Y'
    WHERE 
    (
        `p`.`product_sku` LIKE '%".urldecode($searchword)."%' OR  `p`.`product_name` LIKE '%".urldecode($searchword)."%'
    )
    AND `p`.`product_publish`='Y'
    GROUP BY `p`.`product_id`
    HAVING `product_real_price`>0.01
    ORDER BY `p`.`product_name` ASC LIMIT $max_results
    ";

    $database->setQuery($query);
    $products_obj = $database->loadObjectList();
    
    if (count($products_obj) > 0) {
        foreach ($products_obj as $product_obj) {
                    $products[] = $product_obj->product_id;
        }
    }

    $query_search = "INSERT INTO tbl_track_search_queries ( search_word) VALUES( '".urldecode($searchword)."')";
    $database->setQuery($query_search);
    $database->query();

    if (count($products) > 0) {

        echo $ps_product->show_product_list($products);
    }
    else {
        ?>
        <div class="container search_error">
            <div class="row">          
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 error"> 
                    Nothing found on your request.
                </div>
            </div>
        </div>
        <?php
    }
}
else {
    ?>
    <div class="container search_error">
        <div class="row">          
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 error"> 
                The length of the search string must be at least 3 characters.
            </div>
        </div>
    </div>
    <?php
}
?>
<script defer type="text/javascript">
    pushGoogleAnalytics("search", [{search_term: "<?= $searchword?>"}]);

</script>

