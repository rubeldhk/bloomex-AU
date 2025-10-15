<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('_VALID_MOS', true);
define('_JEXEC', true);

$category_id = $_REQUEST['category_id'] ?? '';


include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';
$ResizeAndSave = new ResizeImageAndSaveToS3();

global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);

$sql = "SELECT 
           c.category_id,
           c.category_full_image,
           s.full_image_link_webp,
           s.full_image_link_jpeg
        FROM  `jos_vm_category` as c
        left join jos_vm_category_s3_images as s on s.category_id = c.category_id
        where c.category_publish ='Y' and c.category_full_image !='' and s.full_image_link_webp is null limit 50";
if($category_id){
    $sql.=" and c.category_id = {$category_id}";
}

$database->setQuery( $sql );
if(!$database->query()) {
    echo $database->stderr( true );
    return;
}

if ($database->getNumRows() == 0) {
    die('no categories');
}
$j = 0;

$categories = $database->loadObjectList();

foreach ($categories as $row) {

    set_time_limit(60);
// файл
    $path = $_SERVER['DOCUMENT_ROOT'] . '/components/com_virtuemart/shop_image/category/';

    $imagePath =  $path . $row->category_full_image;
    $ResizeAndSave->resizeCategoryImageAndSave($row,$imagePath,$database);

    echo "<pre>";print_r($row);echo "</pre>";

}



