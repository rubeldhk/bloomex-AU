<?php
global $mosConfig_absolute_path;

require_once 'components/templates/LoadImageWithMoreElements.php';

$database_table = 'jos_vm_edit_featured_product_href';
$languages_array = array( 'Featured English', 'Featured French' );
$names_array = array( 'en', 'fr' );
$folder_for_images = "/images_upload/com_featured_product/";
$header = "Edit Featured Product";
$image_name = 'featured.jpg';
$elements = array( 'title', 'sku', 'price', 'href' );

$_load_image_with_reference_template = new LoadImageWithMoreElements( $database_table, $folder_for_images, $names_array, $languages_array, $header, $elements, $image_name );
$_load_image_with_reference_template->create();
?>