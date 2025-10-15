<?php
global $database;

$query = "SELECT `options` 
FROM `tbl_options` 
WHERE `type`='slider'";

$database->setQuery($query);
$slider_result = $database->loadResult();

$query = "SELECT `s`.* 
FROM `jos_vm_slider` AS `s`
WHERE `s`.`public`=1 and slider_type=2 and  
(now() >= date_start or date_start is null or date_start='0000-00-00') and 
(now()<= date_end or date_end is null or date_end='0000-00-00')";
if ($slider_result == 'random') {
    $query .= " ORDER BY RAND()";
} else {
    $query .= " ORDER BY `s`.`queue` ASC";
}
$query .= "  limit 3";
$database->setQuery($query);
$banners = $database->loadObjectList();

if ($banners) {
    foreach ($banners as $banner) {

        echo ' <div class="col-sm-4 col-md-4 col-lg-4 hidden-xs sale_banner_full">
               <div class="slider_image_loader"></div>
                            <a href="' . $banner->src . '">
                                <img alt="' . $banner->alt . '" style="display: none;" class="slider_image_real" src="/images/header_images/' . $banner->image . '" />
                            </a>
                        </div>';
    }
} ?>

