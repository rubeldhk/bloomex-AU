<?php
global $mosConfig_absolute_path;
require_once $mosConfig_absolute_path.'/components/administrator/LoadImageWithReferenceTemplate.php';


$database_table = 'jos_vm_edit_banner_href';
$languages_array = array( 'Top Banner English', 'Top Banner French' );
$names_array = array( 'en', 'fr' );
$folder_for_images = "/images_upload/com_edit_banner/";
$header = "Edit Banner";

$_load_image_with_reference_template = new LoadImageWithReferenceTemplate( $database_table, $folder_for_images, $names_array, $languages_array, $header );
?>