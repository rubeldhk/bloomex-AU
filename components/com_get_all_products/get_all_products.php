<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

global $my;
require_once $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php';
require_once CLASSPATH.'ps_product.php';

$query = "SELECT GROUP_CONCAT(`category_id` SEPARATOR ',') AS `categories_ids`
    FROM `jos_vm_category_unsearchable`
    ";

$database->setQuery($query);
$unsearchable = false;
$database->loadObject($unsearchable);

$ps_product = new ps_product;
    $products = [];
    $query = "SELECT 
   distinct `p`.`product_id`
FROM 
    `jos_vm_product_category_xref` AS `cx`
INNER JOIN `jos_vm_product` AS `p`
    ON `p`.`product_id`=`cx`.`product_id` 
    AND
    `p`.`product_publish`='Y' 
     INNER JOIN `jos_vm_product_category_xref` AS `pc_xref` ON `pc_xref`.`product_id`=`p`.`product_id` AND `pc_xref`.`category_id` NOT IN (".$unsearchable->categories_ids.")
        
        INNER JOIN `jos_vm_product_options` AS `po`
            ON `po`.`product_id`=`p`.`product_id`
INNER JOIN `jos_vm_category` AS `c`
            ON `c`.`category_id`=`po`.`canonical_category_id`
            AND
            `c`.`category_publish`='Y'
    ";
    $database->setQuery($query);
    $products_obj = $database->loadObjectList();
    if (count($products_obj) > 0) {
        foreach ($products_obj as $product_obj) {
                    $products[] = $product_obj->product_id;
        }
    }
echo $ps_product->show_product_list($products);



?>


