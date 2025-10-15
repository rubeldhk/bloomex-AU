<?php
global $database, $mosConfig_home_page_categories,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url;

$query = "SELECT
                `c`.`category_id`,
                `c`.`category_full_image`,
                `c`.`category_name`,
                s.full_image_link_webp,
                s.full_image_link_jpeg,
                `c`.`alias`
            FROM  `jos_vm_category` AS `c` 
             left join  jos_vm_category_s3_images as s on s.category_id = c.category_id
            where `c`.`category_publish`='Y' and `c`.`category_full_image` != ''  and `c`.`category_id` in (".implode(',', $mosConfig_home_page_categories).")
            ORDER BY `c`.`list_order`";

$database->setQuery($query);

$categories = $database->loadObjectList();

if ($categories) {
    foreach ($categories as $category) {

        $link = getCategoryCanonical($category->alias);

        echo ' <div class="d-none d-md-block categories_banner_list padding0" style="
padding-top: 0px;
padding-right: 1px;
padding-bottom: 0px;
padding-left: 1px;">
                           
                            <a href="' . $link . '">
                             <div class="slider_image_loader"></div>
                                <img alt="' . $category->category_name . '" style="display: none;"  class="slider_image_real" src="'. $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $category->full_image_link_jpeg : $category->full_image_link_webp) .'" />
                            </a>
                            <h4 class="text-center">' . $category->category_name . '</h4>
                        </div>';
    }
}
?>

