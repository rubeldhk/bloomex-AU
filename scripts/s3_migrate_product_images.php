<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('_VALID_MOS', true);
define('_JEXEC', true);

$product_id = $_REQUEST['product_id'] ?? '';


include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';
$ResizeAndSave = new ResizeImageAndSaveToS3();

global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);

$sql = "SELECT 
           p.product_id,
           p.product_name,
           p.product_sku,
           p.product_thumb_image,
           p.product_full_image,
           s.full_image_link_webp,
           s.medium_image_link_webp,
           s.small_image_link_webp,
           s.full_image_link_jpeg,
           s.medium_image_link_jpeg,
           s.small_image_link_jpeg
        FROM  `jos_vm_product` as p
        left join jos_vm_product_s3_images as s on s.product_id = p.product_id
        where p.product_publish ='Y' and (p.product_full_image !='' or p.product_thumb_image !='') and s.full_image_link_webp is null limit 50";
if($product_id){
    $sql.=" and p.product_id = {$product_id}";
}

$database->setQuery( $sql );
if(!$database->query()) {
    echo $database->stderr( true );
    return;
}

if ($database->getNumRows() == 0) {
    die('no products');
}
$j = 0;

$products = $database->loadObjectList();

foreach ($products as $row) {

    set_time_limit(60);
// файл
    $path = $_SERVER['DOCUMENT_ROOT'] . '/components/com_virtuemart/shop_image/product/';

    $imagePath = is_file($path . $row->product_full_image) ? $path . $row->product_full_image : $path . $row->product_thumb_image;
    $ResizeAndSave->resizeProductImageAndSave($row,$imagePath,$database);

    echo "<pre>";print_r($row);echo "</pre>";

}



