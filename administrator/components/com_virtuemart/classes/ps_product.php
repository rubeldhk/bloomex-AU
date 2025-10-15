<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: ps_product.php,v 1.24.2.14 2006/04/21 17:05:17 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage classes
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

/**
 * The class is is used to manage product repository.
 * @package virtuemart
 * @author pablo, jep, gday, soeren
 * 
 */
class ps_product extends vmAbstractObject {
    const EXTRA_TOUCH_ALIAS = [
        'cart' => 'greeting-cards',
        'vase' => 'gift-vases',
        'teddy' => 'cute-teddy-bears',
        'wine' => 'select-wines--bubbly',
        'balloon' => 'celebration-balloons',
        'treat' => 'gourmet-chocolates--treats',
        'special' => 'popular-add-on-bundles',
        'diy_bulk' => 'finishing-touches--value-offers',
    ];
    const EXTRA_TOUCH_IMAGES = [
        'card' => 'Full-Size-Greeting-Cards.png',
        'vase' => 'Glass-Vases.png',
        'treat' => 'Gourmet-Chocolates.png',
        'wine' => 'wolf_blass_shiraz_cabernet.png',
        'teddy' => 'Teddy-Bears.png',
        'balloon' => 'Themed-Balloons.png',
        'special' => 'deluxe_gift_packaging.png',
        'diy_bulk' => 'gift-box-white.png',
    ];
    const EXTRA_TOUCH_PRODUCTS_COUNT = 12;
    var $classname = "ps_product";

    /**
     * Validates product fields and uploaded image files.
     *
     * @param array $d The input vars
     * @return boolean True when validation successful, false when not
     */
    function validate(&$d) {
        global $vmLogger, $database, $perm;
        $valid = true;
        $db = new ps_DB;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        if ($perm->check('admin')) {
            $vendor_id = $d['vendor_id'];
        } else {
            $vendor_id = $ps_vendor_id;
        }

        $q = "SELECT product_id,product_thumb_image,product_full_image FROM #__{vm}_product WHERE product_sku='";
        $q .= $d["product_sku"] . "'";
        $db->setQuery($q);
        $db->query();
        if ($db->next_record() && ($db->f("product_id") != $d["product_id"])) {
            $vmLogger->err("A Product with the SKU " . $d['product_sku'] . " already exists.");
            $valid = false;
        }
        if (!empty($d['product_discount_id'])) {
            if ($d['product_discount_id'] == "override") {

                $d['is_percent'] = "0";
                $d['amount'] = (float) $d['product_price_incl_tax'] - (float) $d['discounted_price_override'];

                require_once( CLASSPATH . 'ps_product_discount.php' );
                $ps_product_discount = new ps_product_discount;
                $ps_product_discount->add($d);
                $d['product_discount_id'] = $database->insertid();
            }
        }
        if (empty($d['manufacturer_id'])) {
            $d['manufacturer_id'] = "1";
        }
        if (empty($d["product_sku"])) {
            $vmLogger->err("A Product Sku must be entered.");
            $valid = false;
        }
        if (!$d["product_name"]) {
            $vmLogger->err("A product name must be entered.");
            $valid = false;
        }
        if (!$d["product_available_date"]) {
            $vmLogger->err("You must provide an availability date.");
            $valid = false;
        } else {
            $day = substr($d["product_available_date"], 8, 2);
            $month = substr($d["product_available_date"], 5, 2);
            $year = substr($d["product_available_date"], 0, 4);
            $d["product_available_date_timestamp"] = mktime(0, 0, 0, $month, $day, $year);
        }

        /** Validate Product Specific Fields * */
        if (!$d["product_parent_id"]) {
            if (sizeof(@$d["product_categories"]) < 1) {
                $vmLogger->err("A Category must be selected.");
                $valid = false;
            }
        }
        if (!empty($d['downloadable']) && (empty($_FILES['file_upload']['name']) && empty($d['filename']))) {
            $vmLogger->err("Please specify a Product File for Download!");
            $valid = false;
        }

        /** Image Upload Validation * */
        $maxSizeWidth = 600;
        $maxSizeHeight = 700;

        if (!empty($d['product_full_image_url'])) {
            // Image URL
            if (substr($d['product_full_image_url'], 0, 4) != "http") {
                $vmLogger->err("Image URL must begin with http.");
                return false;
            }
            // if we have an uploaded image file, prepare this one for deleting.
            if ($db->f("product_full_image") && substr($db->f("product_thumb_image"), 0, 4) != "http") {
                $_REQUEST["product_full_image_curr"] = $db->f("product_full_image");
                $d["product_full_image_action"] = "delete";
                if (!validate_image($d, "product_full_image", "product")) {
                    return false;
                }
            }
            $d["product_full_image"] = $d['product_full_image_url'];
        } else {
            // File Upload
            $validThumb = true;
            if ($_FILES["product_full_image"]["tmp_name"] != '') {
                $filename = $_FILES["product_full_image"]["tmp_name"];
                $imageNameWebp = 'tmp_'.basename($_FILES["product_full_image"]["name"]);
                if(!exif_imagetype($filename) && copy($filename, __DIR__.'/'.$imageNameWebp)){
                    $im = imagecreatefromwebp(__DIR__.'/'.$imageNameWebp);
                    $getimagesize[0] = imagesx($im);
                    $getimagesize[1] = imagesy($im);
                    unlink(__DIR__.'/'.$imageNameWebp);
                }else{
                    if (!$getimagesize = getimagesize($filename)) {
                        $getimagesize = getimagesize($filename);
                    }
                }

                if ($getimagesize[0] != $maxSizeWidth)
                    $validThumb = false; // width
                if ($validThumb && $getimagesize[1] != $maxSizeHeight)
                    $validThumb = false; // height
            }

            if ($validThumb) {
                if (!validate_image($d, "product_full_image", "product")) {
                    $valid = false;
                }
            } else {
                $valid = $validThumb;
            }
            if (!$validThumb) {
                $vmLogger->err("Size Full image should be: $maxSizeWidth px / $maxSizeHeight px<br>
                                              Size Thumb image should be: " . (PSHOP_IMG_WIDTH) . " px / " . (PSHOP_IMG_HEIGHT) . " px<br>");
            }
        }

        $new_alias = $d['alias'];
        $checkAlias = $this->checkAlias($d['alias'], $d['product_id']);
        if ($checkAlias->result === false) {
            $i_alias = 1;
            while($checkAlias->result === false) {
                $new_alias = $d['alias'] . '-' . $i_alias;
                
                $checkAlias = $this->checkAlias($new_alias, $d['product_id']);
                $i_alias++;
            }
        }
        $d['alias'] = $new_alias;
        /*
        if ($checkAlias->result == false) {
            $err = 'Alias is busy, check:<br/>';
            
            foreach ($checkAlias->products AS $product_obj) {
                $err .= '<a href="/administrator/index2.php?option=com_virtuemart&page=product.product_form&product_id='.$product_obj->product_id.'" target="_blank">'.$product_obj->product_name.'</a><br/>';
            }

            $vmLogger->err($err);
            $valid = false;
        }*/
        

        foreach ($d as $key => $value) {
            if (!is_array($value))
                $d[$key] = addslashes($value);
        }
        return $valid;
    }
    
    private function checkAlias($alias, $product_id) {
        global $database;
        
        $return = (object)[
            'result' => true
        ];
        
        $query = "SELECT
            `p`.`product_id`,
            `p`.`product_name`
        FROM `jos_vm_product` AS `p`
        WHERE 
            `p`.`alias`='" . $database->getEscaped($alias) . "'
            AND
            `p`.`product_id`!=" . (int)$product_id . "
        ";
        $database->setQuery($query);
        $products_obj = $database->loadObjectList();
        
        if ((!is_null($products_obj)) AND (count($products_obj) > 0)) {
            $return->result = false;
            $return->products = $products_obj;
        }
        
        return $return;
    }

    /**
     * Validates that a product can be deleted
     *
     * @param array $d The input vars
     * @return boolean Validation sucessful?
     */
    function validate_delete($product_id, &$d) {
        global $vmLogger;
        /* Check that ps_vendor_id and product_id match
          if (!$this->check_vendor($d)) {
          $d["error"] = "ERROR: Cannot delete product. Wrong product or vendor." ;
          return false;
          } */
        if (empty($product_id)) {
            $vmLogger->err("Please specify a Product to delete!");
            return false;
        }
        /* Get the image filenames from the database */
        $db = new ps_DB;
        $q = "SELECT product_thumb_image,product_full_image ";
        $q .= "FROM #__{vm}_product ";
        $q .= "WHERE product_id='$product_id'";
        $db->setQuery($q);
        $db->query();
        $db->next_record();

        /* Prepare product_thumb_image for Deleting */
        if (!stristr($db->f("product_thumb_image"), "http")) {
            $_REQUEST["product_thumb_image_curr"] = $db->f("product_thumb_image");
            $d["product_thumb_image_action"] = "delete";
            if (!validate_image($d, "product_thumb_image", "product")) {
                $vmLogger->err("Failed deleting Product Images!");
                return false;
            }
        }
        /* Prepare product_full_image for Deleting */
        if (!stristr($db->f("product_full_image"), "http")) {
            $_REQUEST["product_full_image_curr"] = $db->f("product_full_image");
            $d["product_full_image_action"] = "delete";
            if (!validate_image($d, "product_full_image", "product")) {
                return false;
            }
        }
        return true;
    }

    function saveorder(&$d) {
        global $perm, $vmLogger;
        $db = new ps_DB;
        $cb = mosGetParam($_POST, 'product_id', array(0));
        $product_list = mosGetParam($_POST, 'product_list', array(0));
        $category_reorder = mosGetParam($_POST, 'category_reorder', 0);
//		print_r($cb); echo "<br/>";
//		print_r($product_list);
//		die("aaaaaaaaaaaaaaa$category_reorder");

        if (count($cb) && !empty($category_reorder)) {
            $i = 0;
            foreach ($cb AS $item) {
                if (!empty($product_list[$i])) {
                    $q = "UPDATE #__{vm}_product_category_xref ";
                    $q .= "SET product_list='" . intval($product_list[$i]) . "' ";
                    $q .= "WHERE category_id='$category_reorder' AND product_id = " . $item;
                    $db->query($q);
                }
                $i++;
            }
        }

        $vmLogger->info("Product was reorder successfully.");

        return true;
    }

    /**
     * Function to add a new product into the product table
     *
     * @param array $d The input vars
     * @return boolean True, when the product was added, false when not
     */
    function add(&$d) {
        global $perm, $vmLogger, $my, $mosConfig_live_site;
        $database = new ps_DB();

        if (!$this->validate($d)) {
            return false;
        }

        if (!process_images($d)) {
            return false;
        }
        $d["product_sku"] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/i', '', $d["product_sku"]);
        $d["product_sku"] = strip_tags($d["product_sku"]);
        $d["product_sku"] = explode(' ', $d["product_sku"]);
        $d["product_sku"] = $d["product_sku"][0];
        $timestamp = time();
        $db = new ps_DB;

        if (empty($d["product_publish"])) {
            $d["product_publish"] = "N";
        }

        if (empty($d["clone_product"])) {
            $d["clone_product"] = "N";
        }

        $d["not_apply_discount"] = intval($d["not_apply_discount"]);

        // added for advanced attribute modification
        // strips the trailing semi-colon from an attribute
        if (';' == substr($d["product_advanced_attribute"], strlen($d["product_advanced_attribute"]) - 1, 1)) {
            $d["product_advanced_attribute"] = substr($d["product_advanced_attribute"], 0, strlen($d["product_advanced_attribute"]) - 1);
        }
        // added for custom attribute modification
        // strips the trailing semi-colon from an attribute
        if (';' == substr($d["product_custom_attribute"], strlen($d["product_custom_attribute"]) - 1, 1)) {
            $d["product_custom_attribute"] = substr($d["product_custom_attribute"], 0, strlen($d["product_custom_attribute"]) - 1);
        }
        $d["product_special"] = empty($d["product_special"]) ? "N" : "Y";
        if (@$d['product_full_image_action'] == 'auto_resize') {
            $product_thumb_image = $d['product_sku'] . "/" . $d["product_thumb_image"];
        } else {
            $product_thumb_image = $d["product_thumb_image"];
        }

        $q = "INSERT INTO #__{vm}_product (vendor_id,product_parent_id,product_sku,";
        $q .= "product_name,product_desc,product_desc_city,product_s_desc,";
        $q .= "product_thumb_image,product_full_image,";
        //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
        $q .= "product_publish,product_related,product_weight,product_weight_uom,";
        $q .= "product_length,product_width,product_height,product_lwh_uom,";
        $q .= "product_unit,product_packaging,"; // Changed Packaging - Added
        $q .= "product_url,product_in_stock,";
        $q .= "attribute,custom_attribute,";
        $q .= "product_available_date,product_availability,product_special,product_discount_id,";
        $q .= "cdate,mdate,product_tax_id, ingredient_list, meta_info, meta_info_fr, not_apply_discount, product_coupon_discount, alias) ";
        $q .= "VALUES ('";
        $q .= $d['vendor_id'] . "','" . $d["product_parent_id"] . "','";
        $q .= $d["product_sku"] . "','" . $d["product_name"] . "','";
        $q .= $d["product_desc"] . "','" . $d["product_desc_city"] . "','" . $d["product_s_desc"] . "','";
        $q .= $product_thumb_image . "','";
        $q .= $d["product_full_image"] . "','" . $d["product_publish"] . "','" . $d["product_related"] . "','";
        $q .= $d["product_weight"] . "','" . $d["product_weight_uom"] . "','";
        $q .= $d["product_length"] . "','" . $d["product_width"] . "','";
        $q .= $d["product_height"] . "','" . $d["product_lwh_uom"] . "','";
        $q .= $d["product_unit"] . "','" . (($d["product_box"] << 16) | ($d["product_packaging"] & 0xFFFF)) . "','"; // Changed Packaging - Added
        $q .= $d["product_url"] . "','" . $d["product_in_stock"] . "','";
        $q .= $d["product_advanced_attribute"] . "','";
        $q .= $d["product_custom_attribute"] . "','";
        $q .= $d["product_available_date_timestamp"] . "','";
        $q .= $d["product_availability"] . "','";
        $q .= $d["product_special"] . "','";
        $q .= $d["product_discount_id"] . "','$timestamp','$timestamp','" . $d["product_tax_id"] . "','" . $d["ingredient_list"] . "',";
        $q .= "'" . $d["page_title"] . "[--2010--]" . $d["meta_description"] . "[--2010--]" . $d["meta_keywords"] . "',";
        $q .= "'" . $d["page_title_fr"] . "[--2010--]" . $d["meta_description_fr"] . "[--2010--]" . $d["meta_keywords_fr"] . "',";
        $q .= $d["not_apply_discount"] . ", ";
        $q .= "'" . $d["product_coupon_discount"] . "',";
        $q .= "'" . $d["alias"] . "'";
        $q .= ")";
        $db->setQuery($q);
        $db->query();


        $d["product_id"] = $db->last_insert_id();

        include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
        $ResizeAndSave = new ResizeImageAndSaveToS3();
        $ResizeAndSave->resizeProductImageAndSave((object)$d,IMAGEPATH .'product/'. $d["product_full_image"],$db);


        if ($mosConfig_live_site == 'https://bloomex.com.au') {
            $product_change_table = "#__{vm}_product_history_live";
        } else {
            $product_change_table = "#__{vm}_product_history_stage";
        }

        $product_change_sql = "INSERT INTO `" . $product_change_table . "`
        (`product_id`, `name`, `username`, `date`) VALUES  
        (" . $d["product_id"] . ", 'Created', '" . $my->username . "', DATE_SUB(NOW(), INTERVAL 4 HOUR))";

        $db->setQuery($product_change_sql);
        $db->query();

        // If is Item, add attributes from parent //
        if ($d["product_parent_id"]) {
            $q = "SELECT attribute_name FROM #__{vm}_product_attribute_sku ";
            $q .= "WHERE product_id='" . $d["product_parent_id"] . "' ";
            $q .= "ORDER BY attribute_list,attribute_name";

            $db->setQuery($q);
            $db->query();

            $db2 = new ps_DB;
            $i = 0;
            while ($db->next_record()) {
                $i++;
                $q = "INSERT INTO #__{vm}_product_attribute VALUES ";
                $q .= "('" . $d["product_id"] . "', '" . $db->f("attribute_name") . "', '" . $d["attribute_$i"] . "')";
                $db2->query($q);
            }
        } else {
            /* If is Product, Insert category ids */
            foreach ($d["product_categories"] as $category_id) {
                $q = "INSERT INTO #__{vm}_product_category_xref ";
                $q .= "(category_id,product_id) ";
                $q .= "VALUES ('$category_id','" . $d["product_id"] . "')";
                $db->setQuery($q);
                $db->query();
            }
        }
        $q = "INSERT INTO #__{vm}_product_mf_xref VALUES (";
        $q .= "'" . $d['product_id'] . "', '" . $d['manufacturer_id'] . "')";
        $db->setQuery($q);
        $db->query();

        // Handle "Downloadable Product" Queries and File copying
        if (@$d['downloadable'] == "Y") {
            if (!empty($_FILES['file_upload']['name'])) {
                require_once( CLASSPATH . 'ps_product_files.php' );
                $ps_product_files = new ps_product_files();
                // Set file-add values
                $d["file_published"] = "1";
                $d["upload_dir"] = "DOWNLOADPATH";
                $d["file_title"] = $_FILES['file_upload']['name'];
                $d["file_url"] = "";
                if (!$ps_product_files->add($d)) {
                    $d['error'] = 'Error: Failed to upload the downloadable file.';
                }
            } else {
                $d["file_title"] = $d["file_name"];
            }
            // Insert an attribute called "download", attribute_value: filename
            $q2 = "INSERT INTO #__{vm}_product_attribute ";
            $q2 .= "(product_id,attribute_name,attribute_value) ";
            $q2 .= "VALUES ('" . $d["product_id"] . "','download','" . $d["file_title"] . "')";
            $db->setQuery($q2);
            $db->query();
        }
        if (!empty($d["related_products"])) {
            /* Insert Pipe separated Related Product IDs */
            $related_products = implode("|", $d["related_products"]);

            $q = "INSERT INTO #__{vm}_product_relations ";
            $q .= "(product_id, related_products) ";
            $q .= "VALUES ('" . $d["product_id"] . "','$related_products')";
            $db->setQuery($q);
            $db->query();
        }
        // ADD A PRICE, IF NOT EMPTY ADD 0
        if (!empty($d['product_price'])) {

            if (empty($d['product_currency']))
                $d['product_currency'] = $_SESSION['vendor_currency'];

            $d["price_quantity_start"] = 0;
            $d["price_quantity_end"] = "";
            require_once ( CLASSPATH . 'ps_product_price.php');
            $my_price = new ps_product_price;
            $my_price->add($d);
        }

        // CLONE PRODUCT additional code
        if ($d["clone_product"] == "Y") {

            // Clone Parent Product's Attributes
            $q = "INSERT INTO #__{vm}_product_attribute_sku
              SELECT '" . $d["product_id"] . "', attribute_name, attribute_list 
              FROM #__{vm}_product_attribute_sku WHERE product_id='" . $d["old_product_id"] . "' ";
            $db->setQuery($q);
            $db->query();
            if (!empty($d["child_items"])) {

                $database->query("SHOW COLUMNS FROM #__{vm}_product");
                $rows = $database->record;
                while (list(, $Field) = each($rows)) {
                    $product_fields[$Field->Field] = $Field->Field;
                }
                // Change the Field Names
                // leave empty for auto_increment
                $product_fields["product_id"] = "''";
                // Update Product Parent ID to the new one
                $product_fields["product_parent_id"] = "'" . $d["product_id"] . "'";
                // Rename the SKU
                $product_fields["product_sku"] = "CONCAT(product_sku,'_" . $d["product_id"] . "')";

                $rows = Array();
                $database->query("SHOW COLUMNS FROM #__{vm}_product_price");
                $rows = $database->record;
                while (list(, $Field) = each($rows)) {
                    $price_fields[$Field->Field] = $Field->Field;
                }

                foreach ($d["child_items"] as $child_id) {
                    $q = "INSERT INTO #__{vm}_product ";
                    $q .= "SELECT " . implode(",", $product_fields) . " FROM #__{vm}_product WHERE product_id='$child_id'";
                    $db->setQuery($q);
                    $db->query();
                    $new_product_id = $db->last_insert_id();

                    $q = "INSERT INTO #__{vm}_product_attribute
                  SELECT '$new_product_id', attribute_name, attribute_value
                  FROM #__{vm}_product_attribute WHERE product_id='$child_id'";
                    $db->setQuery($q);
                    $db->query();

                    $price_fields["product_price_id"] = "''";
                    $price_fields["product_id"] = "'$new_product_id'";

                    $q = "INSERT INTO #__{vm}_product_price ";
                    $q .= "SELECT " . implode(",", $price_fields) . " FROM #__{vm}_product_price WHERE product_id='$child_id'";
                    $db->setQuery($q);
                    $db->query();
                }
            }

            // End Cloning
        }
        if ($d['clone_product'] == 'Y') {
            $vmLogger->info("Product was successfully cloned.");
        } else {
            $vmLogger->info("Product was successfully added.");
        }

        //Implement Ticket #3713
        $no_delivery = intval(mosGetParam($_REQUEST, "no_delivery"));
        $promo = intval(mosGetParam($_REQUEST, "promo"));
        $no_delivery_order = intval(mosGetParam($_REQUEST, "no_delivery_order"));
        $contain_alcohol= intval(mosGetParam($_REQUEST, "contain_alcohol"));
        $is_bestseller= intval(mosGetParam($_REQUEST, "is_bestseller"));
        $never_bestseller= intval(mosGetParam($_REQUEST, "never_bestseller"));
        $no_tax = intval(mosGetParam($_REQUEST, "no_tax"));
        $next_day_delivery = intval(mosGetParam($_REQUEST, "next_day_delivery"));
        $deluxe = mosGetParam($_REQUEST, "deluxe", "0");
        $petite = mosGetParam($_REQUEST, "petite", "0");
        $supersize = mosGetParam($_REQUEST, "supersize", "0");
        $sub_3 = mosGetParam($_REQUEST, "sub_3", "0");
        $sub_6 = mosGetParam($_REQUEST, "sub_6", "0");
        $sub_12 = mosGetParam($_REQUEST, "sub_12", "0");
        $must_be_combined = intval(mosGetParam($_REQUEST, "must_be_combined"));
        $show_sale_overlay = intval(mosGetParam($_REQUEST, "show_sale_overlay"));
        $tuefri_delivery = intval(mosGetParam($_REQUEST, "tuefri_delivery"));
        //Ticket #4975
        $extra_touches_menu = intval(mosGetParam($_REQUEST, "extra_touches_menu"));
        $product_type = mosGetParam($_REQUEST, "product_type_option", "");

        $product_sold_out = intval(mosGetParam($_REQUEST, "product_sold_out"));
        $product_out_of_season = intval(mosGetParam($_REQUEST, "product_out_of_season"));
        $no_special = mosGetParam($_REQUEST, "no_special", "0");
        $canonical_category_id = (int) mosGetParam($_REQUEST, "canonical_category_id", "0");
        $surprise_publish = intval(mosGetParam($_REQUEST, "surprise_publish"));
        $query = "INSERT INTO #__vm_product_options
        ( 
            `product_id`, 
            `no_delivery`, 
            `promo`, 
            `next_day_delivery`, 
            `no_tax`, 
            `extra_touches_menu`,
            `deluxe`,
            `supersize`,
            `petite`,
            `sub_3`,
            `sub_6`,
            `sub_12`,
            `must_be_combined`, 
            `show_sale_overlay`, 
            `tuefri_delivery`, 
            `product_type`, 
            `no_special`, 
            `no_delivery_order`, 
            `contain_alcohol`, 
            `is_bestseller`, 
            `never_bestseller`, 
            `surprise_publish`,
            `product_sold_out`,
            `product_out_of_season`,
            `canonical_category_id`
        ) 
        VALUES (
            " . $d["product_id"] . ", 
            $no_delivery, 
            '".$promo."', 
            $next_day_delivery, 
            $no_tax, 
            $extra_touches_menu,
            $deluxe,
            $supersize,
            $petite,
            '" . $sub_3 . "',
            '" . $sub_6 . "',
            '" . $sub_12 . "',    
            $must_be_combined,
            '".$show_sale_overlay."',
            '" . $tuefri_delivery . "', 
            '" . $product_type . "', 
            '" . $no_special . "', 
            '" . $no_delivery_order . "', 
            '" . $contain_alcohol . "', 
            '" . $is_bestseller . "', 
            '" . $never_bestseller . "', 
            '" . $surprise_publish . "',
            '" . $product_sold_out . "',
            '" . $product_out_of_season . "',
            " . $canonical_category_id . "
        )";
        $db->setQuery($query);
        $db->query();

        $rating = mt_rand(43, 48) / 10;
        $reviewCount = mt_rand(1000, 3000);
        $q = "INSERT into tbl_product_fake_reviews (product_id, rating, review_count) VALUES ('" . $d["product_id"] . "', '" . $rating . "', '" . $reviewCount . "')";
        $db->setQuery($q);
        $db->query();

        return true;
    }

    /**
     * Function to update product $d['product_id'] in the product table
     *
     * @param array $d The input vars
     * @return boolean True, when the product was updated, false when not
     */
    function update(&$d) {
        global $vmLogger, $my, $mosConfig_live_site;
        if (!$this->validate($d)) {
            return false;
        }

        if (!process_images($d)) {
            return false;
        }
        $d["product_sku"] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/i', '', $d["product_sku"]);
        $d["product_sku"] = strip_tags($d["product_sku"]);
        $d["product_sku"] = explode(' ', $d["product_sku"]);
        $d["product_sku"] = $d["product_sku"][0];
        $timestamp = time();
        $db = new ps_DB;


        include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
        $ResizeAndSave = new ResizeImageAndSaveToS3();
        $ResizeAndSave->resizeProductImageAndSave((object)$d,IMAGEPATH .'product/'. $d["product_full_image"],$db);

        $product_old = false;
        $sql = "SELECT * FROM #__{vm}_product WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_old);

        $product_price_old = false;
        $sql = "SELECT * FROM #__{vm}_product_price WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_price_old);

        $product_options_old = false;
        $sql = "SELECT * FROM #__{vm}_product_options WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_options_old);

        $product_xref_old = false;
        $sql = "SELECT `category_id` FROM #__{vm}_product_category_xref WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $product_xref_old = $db->loadObjectList();

        $product_xref_array_old = array();

        foreach ($product_xref_old as $xref_v)
        {
            $product_xref_array_old[] = $xref_v->category_id;;
        }
        // added for the advanced attribute hack
        // strips the trailing semi-colon from an attribute
        if (';' == substr($d["product_advanced_attribute"], strlen($d["product_advanced_attribute"]) - 1, 1)) {
            $d["product_advanced_attribute"] = substr($d["product_advanced_attribute"], 0, strlen($d["product_advanced_attribute"]) - 1);
        }
        // added for the custom attribute hack
        // strips the trailing semi-colon from an attribute
        if (';' == substr($d["product_custom_attribute"], strlen($d["product_custom_attribute"]) - 1, 1)) {
            $d["product_custom_attribute"] = substr($d["product_custom_attribute"], 0, strlen($d["product_custom_attribute"]) - 1);
        }

        if (empty($d["product_special"]))
            $d["product_special"] = "N";
        if (empty($d["product_publish"]))
            $d["product_publish"] = "N";


        $d["not_apply_discount"] = isset($d["not_apply_discount"]) ? intval($d["not_apply_discount"]) : 0;
        if (@$d['product_full_image_action'] == 'auto_resize') {
            $product_thumb_image = $d['product_sku'] . "/" . $d["product_thumb_image"];
        } else {
            $product_thumb_image = $d["product_thumb_image"];
        }

        $q = "UPDATE #__{vm}_product SET ";
        $q .= "product_sku='" . $d["product_sku"] . "',";
        $q .= "vendor_id='" . $d["vendor_id"] . "',";
        $q .= "product_name='" . $d["product_name"] . "',";
        $q .= "product_s_desc='" . $d["product_s_desc"] . "',";
        $q .= "product_desc='" . $d["product_desc"] . "',";
        $q .= "product_desc_city='" . $d["product_desc_city"] . "',";
        $q .= "product_publish='" . $d["product_publish"] . "',";
        //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
        $q .= "product_related='" . (isset($d["product_related"]) ? $d["product_related"] : '') . "',";
        $q .= "product_weight='" . $d["product_weight"] . "',";
        $q .= "product_weight_uom='" . $d["product_weight_uom"] . "',";
        $q .= "product_length='" . $d["product_length"] . "',";
        $q .= "product_width='" . $d["product_width"] . "',";
        $q .= "product_height='" . $d["product_height"] . "',";
        $q .= "product_lwh_uom='" . $d["product_lwh_uom"] . "',";
        $q .= "product_unit='" . $d["product_unit"] . "',"; // Changed Packaging - Added
        $q .= "product_packaging='" . (($d["product_box"] << 16) | ($d["product_packaging"] & 0xFFFF)) . "',"; // Changed Packaging - Added
        $q .= "product_url='" . $d["product_url"] . "',";
        $q .= "product_in_stock='" . $d["product_in_stock"] . "',";
        $q .= "product_available_date='";
        $q .= $d["product_available_date_timestamp"] . "',";
        $q .= "product_availability='" . $d["product_availability"] . "',";
        $q .= "product_special='" . $d["product_special"] . "',";
        $q .= "product_discount_id='" . $d["product_discount_id"] . "',";
        $q .= "product_thumb_image='" . $product_thumb_image . "',";
        $q .= "product_full_image='" . $d["product_full_image"] . "',";
        $q .= "attribute='" . $d["product_advanced_attribute"] . "',";
        $q .= "custom_attribute='" . $d["product_custom_attribute"] . "',";
        $q .= "product_tax_id='" . $d["product_tax_id"] . "',";
        $q .= "ingredient_list='" . $d["ingredient_list"] . "',";
        $q .= "meta_info='" . $d["page_title"] . "[--2010--]" . $d["meta_description"] . "[--2010--]" . $d["meta_keywords"] . "',";
        $q .= "meta_info_fr='" . $d["page_title_fr"] . "[--2010--]" . $d["meta_description_fr"] . "[--2010--]" . $d["meta_keywords_fr"] . "',";
        $q .= "mdate='$timestamp', ";
        $q .= "not_apply_discount='" . $d["not_apply_discount"] . "',  ";
        $q .= "product_coupon_discount='" . (isset($d["product_coupon_discount"]) ? $d["product_coupon_discount"] : '') . "',  ";
        $q .= "alias='" . $d["alias"] . "'  ";

        $q .= "WHERE product_id='" . $d["product_id"] . "'";
        //$q .= "AND vendor_id='" . $d['vendor_id'] . "'";
        //die($q);
        $db->setQuery($q);
        $db->query();

        $product_new = false;
        $sql = "SELECT * FROM #__{vm}_product WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_new);

        if ($product_new)
        {
            $product_changes = array();

            foreach ($product_new as $k_new => $v_new)
            {
                if ($k_new != 'mdate')
                {
                    if ($product_old->$k_new != $v_new)
                    {
                        $product_changes[]  = array('name' => $k_new, 'old' => $product_old->$k_new, 'new' => $v_new, 'username' => $my->username);
                    }
                }
            }
        }


        /* notify the shoppers that the product is here */
        /* see zw_waiting_list */
        if ($d["product_in_stock"] > "0") {
            require_once( CLASSPATH . 'zw_waiting_list.php');
            $zw_waiting_list = new zw_waiting_list;
            $zw_waiting_list->notify_list($d["product_id"]);
        }

        // Check for download
        $q_dl = "SELECT attribute_name,attribute_value FROM #__{vm}_product_attribute WHERE ";
        $q_dl .= "product_id='" . $d["product_id"] . "' AND attribute_name='download' ";
        $db->query($q_dl);
        $db->next_record();
        if ($db->num_rows() > 0) { // found one
            $q_dl = "SELECT file_id from #__{vm}_product_files WHERE ";
            $q_dl .= "file_product_id='" . $d["product_id"] . "' AND file_title='" . $GLOBALS['vmInputFilter']->safeSQL($db->f("attribute_value")) . "'";
            $db->query($q_dl);
            $db->next_record();
            $d["file_id"] = $db->f("file_id");

            if (@$d['downloadable'] != "Y") {

                // delete the attribute
                $q_del = "DELETE FROM #__{vm}_product_attribute WHERE ";
                $q_del .= "product_id='" . $d["product_id"] . "' AND attribute_name='download'";
                $db->query($q_del);

                if (!empty($d["file_id"])) {
                    require_once( CLASSPATH . 'ps_product_files.php' );
                    $ps_product_files = new ps_product_files();
                    // Delete the existing file entry
                    $ps_product_files->delete($d);
                }
            } else { // update the attribute
                require_once( CLASSPATH . 'ps_product_files.php' );
                $ps_product_files = new ps_product_files();

                if (!empty($_FILES['file_upload']['name'])) {
                    // Set file-add values
                    $d["file_published"] = "1";
                    $d["upload_dir"] = "DOWNLOADPATH";
                    $d["file_title"] = $_FILES['file_upload']['name'];
                    $d["file_url"] = "";

                    $ps_product_files->add($d);
                    $qu = "UPDATE #__{vm}_product_attribute ";
                    $qu .= "SET attribute_value = '" . $d["file_title"] . "' ";
                    $qu .= "WHERE product_id='" . $d["product_id"] . "' AND attribute_name='download'";
                    $db->query($qu);
                } else {
                    $d["file_id"] = "";
                    $qu = "UPDATE #__{vm}_product_attribute ";
                    $qu .= "SET attribute_value = '" . $d['filename'] . "' ";
                    $qu .= "WHERE product_id='" . $d["product_id"] . "' AND attribute_name='download'";
                    $db->query($qu);
                }

                if (!empty($d["file_id"])) {
                    // Now: Delete the existing file entry
                    $ps_product_files->delete($d);
                }
            }
        } else {  // found none
            require_once( CLASSPATH . 'ps_product_files.php' );
            $ps_product_files = new ps_product_files();
            if (@$d['downloadable'] == "Y" && !empty($_FILES['file_upload']['name'])) {
                // Set file-add values
                $d["file_published"] = "1";
                $d["upload_dir"] = "DOWNLOADPATH";
                $d["file_title"] = $_FILES['file_upload']['name'];
                $d["file_url"] = "";
                $ps_product_files->add($d);

                // Insert an attribute called "download", attribute_value: filename
                $q2 = "INSERT INTO #__{vm}_product_attribute ";
                $q2 .= "(product_id,attribute_name,attribute_value) ";
                $q2 .= "VALUES ('" . $d["product_id"] . "','download','" . $d["file_title"] . "')";
                $db->setQuery($q2);
                $db->query();
            } elseif (@$d['downloadable'] == "Y") {
                // Insert an attribute called "download", attribute_value: filename
                $q2 = "INSERT INTO #__{vm}_product_attribute ";
                $q2 .= "(product_id,attribute_name,attribute_value) ";
                $q2 .= "VALUES ('" . $d["product_id"] . "','download','" . $d["filename"] . "')";
                $db->setQuery($q2);
                $db->query();
            }
        }
        // End download check

        $q = "UPDATE #__{vm}_product_mf_xref SET ";
        $q .= "manufacturer_id='" . $d['manufacturer_id'] . "' ";
        $q .= "WHERE product_id = '" . $d['product_id'] . "'";
        $db->setQuery($q);
        $db->query();


        /* If is Item, update attributes */
        if ($d["product_parent_id"]) {
            $q = "SELECT attribute_name FROM #__{vm}_product_attribute_sku ";
            $q .= "WHERE product_id='" . $d["product_parent_id"] . "' ";
            $q .= "ORDER BY attribute_list,attribute_name";

            $db->setQuery($q);
            $db->query();

            $db2 = new ps_DB;
            $i = 0;
            while ($db->next_record()) {
                $i++;
                $q2 = "UPDATE #__{vm}_product_attribute SET ";
                $q2 .= "attribute_value='" . $d["attribute_$i"] . "' ";
                $q2 .= "WHERE product_id = '" . $d["product_id"] . "' ";
                $q2 .= "AND attribute_name = '" . $db->f("attribute_name") . "' ";
                $db2->setQuery($q2);
                $db2->query();
            }
            /* If it is a Product, update Category */
        } else {
            // DELETE ALL OLD CATEGORY_XREF ENTRIES!
            $q = "DELETE FROM #__{vm}_product_category_xref ";
            $q .= "WHERE product_id = '" . $d["product_id"] . "' ";
            $db->setQuery($q);
            $db->query();

            // NOW Re-Insert
            foreach ($d["product_categories"] as $category_id) {
                $q = "INSERT INTO #__{vm}_product_category_xref ";
                $q .= "(category_id,product_id) ";
                $q .= "VALUES ('$category_id','" . $d["product_id"] . "')";
                $db->setQuery($q);
                $db->query();
            }
        }

        $product_xref_new = false;
        $sql = "SELECT `category_id` FROM #__{vm}_product_category_xref WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $product_xref_new = $db->loadObjectList();

        $product_xref_array_new = array();

        foreach ($product_xref_new as $xref_v)
        {
            $product_xref_array_new[] = $xref_v->category_id;
        }

        $product_xref_result = array_diff($product_xref_array_old, $product_xref_array_new);

        if (sizeof($product_xref_result) > 0)
        {
            $product_changes[]  = array('name' => 'category_xref', 'old' => implode(',', $product_xref_array_old), 'new' => implode(',', $product_xref_array_new), 'username' => $my->username);
        }


        if (!empty($d["related_products"])) {
            /* Insert Pipe separated Related Product IDs */
            $related_products = implode("|", $d["related_products"]);

            $q = "REPLACE INTO #__{vm}_product_relations (product_id, related_products)";
            $q .= " VALUES( '" . $d["product_id"] . "', '$related_products') ";
            $db->setQuery($q);
            $db->query();
        } else {
            $q = "DELETE FROM #__{vm}_product_relations ";
            $q .= " WHERE product_id='" . $d["product_id"] . "'";
            $db->setQuery($q);
            $db->query();
        }

        // UPDATE THE PRICE, IF EMPTY ADD 0
        if (empty($d['product_currency'])) {
            $d['product_currency'] = $_SESSION['vendor_currency'];
        }

        // look if we have a price for this product
        $q = "SELECT product_price_id, price_quantity_start, price_quantity_end FROM #__{vm}_product_price ";
        $q .= "WHERE shopper_group_id = '" . $d["shopper_group_id"] . "' ";
        $q .= "AND product_id = '" . $d["product_id"] . "'";
        $db->query($q);


        if ($db->next_record()) {

            $d["product_price_id"] = $db->f("product_price_id");
            require_once ( CLASSPATH . 'ps_product_price.php');
            $my_price = new ps_product_price;

            if (@$d['product_price'] != '') {
                // update prices
                $d["price_quantity_start"] = $db->f("price_quantity_start");
                $d["price_quantity_end"] = $db->f("price_quantity_end");

                $my_price->update($d);
            } else {
                // delete the price
                $my_price->delete($d);
            }
        } else {
            if ($d['product_price'] != '') {
                // add the price
                $d["price_quantity_start"] = 0;
                $d["price_quantity_end"] = "";
                require_once ( CLASSPATH . 'ps_product_price.php');
                $my_price = new ps_product_price;
                $my_price->add($d);
            }
        }

        $product_price_new = false;
        $sql = "SELECT * FROM #__{vm}_product_price WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_price_new);

        $product_price_new = false;
        $sql = "SELECT * FROM #__{vm}_product_price WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_price_new);

        if ($product_price_new)
        {
            foreach ($product_price_new as $k_new => $v_new)
            {
                if ($k_new != 'mdate')
                {
                    if ($product_price_old->$k_new != $v_new)
                    {
                        $product_changes[]  = array('name' => $k_new, 'old' => $product_price_old->$k_new, 'new' => $v_new, 'username' => $my->username);
                    }
                }
            }
        }



        /** Product Type - Begin */
        $product_id = $d["product_id"];

        $q = "SELECT * FROM #__{vm}_product_product_type_xref WHERE ";
        $q .= "product_id='$product_id' ";
        $db->query($q);

        $dbpt = new ps_DB;
        $dbp = new ps_DB;

        // For every Product Type
        while ($db->next_record()) {
            $product_type_id = $db->f("product_type_id");

            $q = "SELECT * FROM #__{vm}_product_type_parameter WHERE ";
            $q .= "product_type_id='$product_type_id' ";
            $q .= "ORDER BY parameter_list_order";
            $dbpt->query($q);

            /*      $q  = "SELECT * FROM #__{vm}_product_type_$product_type_id WHERE ";
              $q .= "product_id='$product_id'";
              $dbp->query($q);
              if (!$dbp->next_record()) {  // Add record if not exist (Items)
              $q  = "INSERT INTO #__{vm}_product_type_$product_type_id (product_id) ";
              $q .= "VALUES ('$product_id')";
              $dbp->setQuery($q); $dbp->query();
              } */

            // Update record
            $q = "UPDATE #__{vm}_product_type_$product_type_id SET ";
            $q .= "product_id='$product_id'";
            while ($dbpt->next_record()) {
                if ($dbpt->f("parameter_type") != "B") { // if it is not breaker
                    $value = $d["product_type_" . $product_type_id . "_" . $dbpt->f("parameter_name")];
                    if ($dbpt->f("parameter_type") == "V" && is_array($value))
                        $value = join(",", $value);
                    if ($value == "") {
                        $value = "NULL";
                    } else {
                        $value = "'$value'";
                    }
                    $q .= ",`" . $dbpt->f("parameter_name") . "`=" . $value;
                }
            }
            $q .= " WHERE product_id = '" . $d['product_id'] . "'";
            $dbp->setQuery($q);
            $dbp->query();
        }
        /** Product Type - End */
        $vmLogger->info("Product was successfully updated");


        //Implement Ticket #3713
        global $database;
        $sql = "SELECT * FROM #__vm_product_options WHERE product_id = " . $d["product_id"];
        $database->setQuery($sql);
        $rows = $database->loadObjectList();

        $no_delivery = intval(mosGetParam($_REQUEST, "no_delivery"));
        $promo = intval(mosGetParam($_REQUEST, "promo"));
        $no_delivery_order = intval(mosGetParam($_REQUEST, "no_delivery_order"));
        $contain_alcohol = intval(mosGetParam($_REQUEST, "contain_alcohol"));
        $is_bestseller = intval(mosGetParam($_REQUEST, "is_bestseller"));
        $never_bestseller = intval(mosGetParam($_REQUEST, "never_bestseller"));
        $no_tax = intval(mosGetParam($_REQUEST, "no_tax"));
        $next_day_delivery = intval(mosGetParam($_REQUEST, "next_day_delivery"));
        $fhid = intval(mosGetParam($_REQUEST, "fhid"));
        $deluxe = mosGetParam($_REQUEST, "deluxe", "0");
        $petite = mosGetParam($_REQUEST, "petite", "0");
        $supersize = mosGetParam($_REQUEST, "supersize", "0");
        $sub_3 = mosGetParam($_REQUEST, "sub_3", "0");
        $sub_6 = mosGetParam($_REQUEST, "sub_6", "0");
        $sub_12 = mosGetParam($_REQUEST, "sub_12", "0");
        $must_be_combined = intval(mosGetParam($_REQUEST, "must_be_combined"));
        $show_sale_overlay = intval(mosGetParam($_REQUEST, "show_sale_overlay"));
        $tuefri_delivery = intval(mosGetParam($_REQUEST, "tuefri_delivery"));
        //Implement Ticket #4975
        $extra_touches_menu = intval(mosGetParam($_REQUEST, "extra_touches_menu"));
        $product_type = mosGetParam($_REQUEST, "product_type_option", "");
        $surprise_publish = intval(mosGetParam($_REQUEST, "surprise_publish"));
        $no_special = mosGetParam($_REQUEST, "no_special", "0");
        $canonical_category_id = (int) mosGetParam($_REQUEST, "canonical_category_id", "0");
        $product_sold_out = intval(mosGetParam($_REQUEST, "product_sold_out"));
        $product_out_of_season = intval(mosGetParam($_REQUEST, "product_out_of_season"));
        if (empty($rows[0]->product_id)) {
            $query = "INSERT INTO #__vm_product_options(
                `product_id`, 
                `no_delivery`, 
                `promo`, 
                `next_day_delivery`, 
                `no_tax`, 
                `extra_touches_menu`,
                `deluxe`,
                `supersize`,
                `petite`,
                `sub_3`,
               `sub_6`,
               `sub_12`,
                `must_be_combined`, 
                `show_sale_overlay`, 
                `contain_alcohol`, 
                `is_bestseller`, 
                `never_bestseller`, 
                `tuefri_delivery`, 
                `product_type`, 
                `no_special`,
                `surprise_publish`,
                `product_sold_out`,
                `product_out_of_season`,
                `canonical_category_id`
            ) 
            VALUES(
                " . $d["product_id"] . ", 
                $no_delivery, 
                '" . $promo . "',
                $next_day_delivery, 
                $no_tax, 
                $extra_touches_menu,
                $deluxe,
                $supersize,
                $petite,
                '" . $sub_3 . "',
                '" . $sub_6 . "',
                '" . $sub_12 . "',    
                $must_be_combined,
                '".$show_sale_overlay."',
                '".$contain_alcohol."',
                '".$is_bestseller."',
                '".$never_bestseller."',
                $tuefri_delivery, 
                '" . $product_type . "', 
                '" . $no_special . "',
                '" . $surprise_publish . "',
                '" . $product_sold_out . "',
                '" . $product_out_of_season . "',
                " . $canonical_category_id . "
            )";
            $database->setQuery($query);
            $database->query();

            //     var_dump($database);
            //     die(ob_get_contents());
        } else {
            $query = "UPDATE #__vm_product_options SET
                `no_delivery`=$no_delivery, 
                `promo`='" . $promo . "', 
                `next_day_delivery`=$next_day_delivery, 
                `no_tax`=$no_tax,
                `supersize`=$supersize,
                `deluxe`=$deluxe,
                `petite`=$petite,
                `sub_3`='" . $sub_3 . "',
                `sub_6`='" . $sub_6 . "',
                `sub_12`='" . $sub_12 . "',    
                `must_be_combined`=$must_be_combined, 
                `show_sale_overlay`='".$show_sale_overlay."', 
                `extra_touches_menu`=$extra_touches_menu, 
                `tuefri_delivery`='" . $tuefri_delivery . "', 
                `product_type`='" . $product_type . "', 
                `no_special`='" . $no_special . "', 
                `product_sold_out`='" . $product_sold_out . "', 
                `product_out_of_season`='" . $product_out_of_season . "', 
                `surprise_publish`='" . $surprise_publish . "',
                `no_delivery_order`='" . $no_delivery_order . "',
                `contain_alcohol`='" . $contain_alcohol . "',
                `is_bestseller`='" . $is_bestseller . "',
                `never_bestseller`='" . $never_bestseller . "',
                `canonical_category_id`=" . $canonical_category_id . "
            WHERE `product_id`=" . $d["product_id"];
            $database->setQuery($query);
            $database->query();

            //     var_dump($database);
            //     die(ob_get_contents());
        }


        $product_options_new = false;
        $sql = "SELECT * FROM #__{vm}_product_options WHERE `product_id`='" . $d["product_id"] . "'";
        $db->query($sql);
        $db->loadObject($product_options_new);

        if ($product_options_new)
        {
            foreach ($product_options_new as $k_new => $v_new)
            {
                if ($k_new != 'mdate')
                {
                    if ($product_options_old->$k_new != $v_new)
                    {
                        $product_changes[]  = array('name' => $k_new, 'old' => $product_options_old->$k_new, 'new' => $v_new, 'username' => $my->username);
                    }
                }
            }
        }
        if (isset($product_changes) && sizeof($product_changes) > 0)
        {
            $product_change_imports = array();

            foreach ($product_changes as $product_change)
            {
                $product_change_imports[] = "(".$d["product_id"].", '".$product_change['name']."', '".$db->getEscaped($product_change['old'])."', '".$db->getEscaped($product_change['new'])."', '".$product_change['username']."', DATE_SUB(NOW(), INTERVAL 4 HOUR))";
            }

            if ($mosConfig_live_site == 'https://bloomex.com.au')
            {
                $product_change_table = "#__{vm}_product_history_live";
            }
            else
            {
                $product_change_table = "#__{vm}_product_history_stage";
            }

            $product_change_sql = "INSERT INTO `".$product_change_table."`
            (`product_id`, `name`, `old`, `new`, `username`, `date`) VALUES ".implode(',', $product_change_imports)."";

            $db->setQuery($product_change_sql);
            $db->query();
        }

        return true;
    }

    /**
     * Function to delete product(s) $d['product_id'] from the product table
     *
     * @param array $d The input vars
     * @return boolean True, when the product was deleted, false when not
     */
    function delete(&$d) {

        $product_id = $d["product_id"];

        if (is_array($product_id)) {
            foreach ($product_id as $product) {
                if (!$this->delete_product($product, $d))
                    return false;
            }
            return true;
        }
        else {
            return $this->delete_product($product_id, $d);
        }
    }

    /**
     * The function that holds the code for deleting
     * one product from the database and all related tables
     * plus deleting files related to the product
     *
     * @param int $product_id
     * @param array $d The input vars
     * @return boolean True on success, false on error
     */
    function delete_product($product_id, &$d) {
        global $db, $vmLogger;

        if (!$this->validate_delete($product_id, $d)) {
            return false;
        }
        /* If is Product */
        if ($this->is_product($product_id)) {
            /* Delete all items first */
            $q = "SELECT product_id FROM #__{vm}_product WHERE product_parent_id='$product_id'";
            $db->setQuery($q);
            $db->query();
            while ($db->next_record()) {
                $d2["product_id"] = $db->f("product_id");
                if (!$this->delete($d2)) {
                    return false;
                }
            }

            /* Delete attributes */
            $q = "DELETE FROM #__{vm}_product_attribute_sku WHERE product_id='$product_id' ";
            $db->setQuery($q);
            $db->query();

            /* Delete categories xref */
            $q = "DELETE FROM #__{vm}_product_category_xref WHERE product_id = '$product_id' ";
            $db->setQuery($q);
            $db->query();
        }
        /* If is Item */ else {
            /* Delete attribute values */
            $q = "DELETE FROM #__{vm}_product_attribute WHERE product_id='$product_id'";
            $db->setQuery($q);
            $db->query();
        }
        /* For both Product and Item */

        /* Delete product - manufacturer xref */
        $q = "DELETE FROM #__{vm}_product_mf_xref WHERE product_id='$product_id'";
        $db->setQuery($q);
        $db->query();

        /* Delete Product - ProductType Relations */
        $q = "DELETE FROM `#__{vm}_product_product_type_xref` WHERE `product_id`=$product_id";
        $db->setQuery($q);
        $db->query();

        /* Delete product votes */
        $q = "DELETE FROM #__{vm}_product_votes WHERE product_id='$product_id'";
        $db->setQuery($q);
        $db->query();

        /* Delete product reviews */
        $q = "DELETE FROM #__{vm}_product_reviews WHERE product_id='$product_id'";
        $db->setQuery($q);
        $db->query();

        /* Delete Image files */
        if (!process_images($d)) {
            return false;
        }
        /* Delete other Files and Images files */
        require_once( CLASSPATH . 'ps_product_files.php' );
        $ps_product_files = new ps_product_files();

        $db->query("SELECT file_id FROM #__{vm}_product_files WHERE file_product_id='$product_id'");
        while ($db->next_record()) {
            $d["file_id"] = $db->f("file_id");
            $ps_product_files->delete($d);
        }

        /* Delete Product Relations */
        $q = "DELETE FROM #__{vm}_product_relations WHERE product_id = '$product_id'";
        $db->setQuery($q);
        $db->query();

        /* Delete Prices */
        $q = "DELETE FROM #__{vm}_product_price WHERE product_id = '$product_id'";
        $db->setQuery($q);
        $db->query();

        /* Delete entry FROM #__{vm}_product table */
        $q = "DELETE FROM #__{vm}_product WHERE product_id = '$product_id'";
        $db->setQuery($q);
        $db->query();

        /* If only deleting an item, go to the parent product page after
         * * the deletion. This had to be done here because the product id
         * * of the item to be deleted had to be passed as product_id */
        if (!empty($d["product_parent_id"])) {
            $d["product_id"] = $d["product_parent_id"];
            $d["product_parent_id"] = "";
        }
        $vmLogger->info("Deleted Product ID: $product_id");
        return true;
    }

    /**
     * Function to check if the vendor_id of the product
     * $d['product_id'] matches the vendor_id associated with the
     * user that calls this function
     *
     * @param array $d
     * @return boolean True, when vendor_id matches, false when not
     */
    function check_vendor($d) {

        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        $db = new ps_DB;
        $q = "SELECT vendor_id  FROM #__{vm}_product ";
        $q .= "WHERE vendor_id = '$ps_vendor_id' ";
        $q .= "AND product_id = '" . $d["product_id"] . "' ";
        $db->setQuery($q);
        $db->query();
        if ($db->next_record()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to create a ps_DB object holding the data of product $d['product_id']
     * from the table #__{vm}_product
     *
     * @param int $product_id
     * @return ps_DB DB object holding all data for product $product_id
     */
    function sql($product_id) {
        $db = new ps_DB;

        $q = "SELECT p.*,s.full_image_link_webp,s.medium_image_link_webp FROM #__{vm}_product as p
         left join jos_vm_product_s3_images as s on s.product_id = p.product_id 
                                                 WHERE p.product_id='$product_id' ";

        $db->setQuery($q);
        $db->query();
        return $db;
    }

    /**
     * Function to create a db object holding the data of all child items of
     * product $product_id
     *
     * @param int $product_id
     * @return ps_DB object that holds all items of product $product_id
     */
    function items_sql($product_id) {
        $db = new ps_DB;

        $q = "SELECT * FROM #__{vm}_product ";
        $q .= "WHERE product_parent_id='$product_id' ";
        $q .= "ORDER BY product_name";

        $db->setQuery($q);
        $db->query();
        return $db;
    }

    /**
     * Function to check whether a product is a parent product or not
     *
     * @param int $product_id
     * @return boolean True when the product is a parent product, false when product is a child item
     */
    function is_product($product_id) {
        $db = new ps_DB;

        $q = "SELECT product_parent_id FROM #__{vm}_product ";
        $q .= "WHERE product_id='$product_id' ";

        $db->setQuery($q);
        $db->query();
        $db->next_record();
        if ($db->f("product_parent_id") == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to create a DB object that holds all information
     * from the attribute tables about item $item_id AND/OR product $product_id
     *
     * @param int $item_id The product_id of the item
     * @param int $product_id The product_id of the parent product
     * @param string $attribute_name The name of the attribute to filter
     * @return ps_DB The db object...
     */
    function attribute_sql($item_id = "", $product_id = "", $attribute_name = "") {
        $db = new ps_DB;
        if ($item_id and $product_id) {
            $q = "SELECT * FROM #__{vm}_product_attribute,#__{vm}_product_attribute_sku ";
            $q .= "WHERE #__{vm}_product_attribute.product_id = '$item_id' ";
            $q .= "AND #__{vm}_product_attribute_sku.product_id ='$product_id' ";
            if ($attribute_name) {
                $q .= "AND #__{vm}_product_attribute.attribute_name = $attribute_name ";
            }
            $q .= "AND #__{vm}_product_attribute.attribute_name = ";
            $q .= "#__{vm}_product_attribute_sku.attribute_name ";
            $q .= "ORDER BY attribute_list,#__{vm}_product_attribute.attribute_name";
        } elseif ($item_id) {
            $q = "SELECT * FROM #__{vm}_product_attribute ";
            $q .= "WHERE product_id=$item_id ";
            if ($attribute_name) {
                $q .= "AND attribute_name = '$attribute_name' ";
            }
        } elseif ($product_id) {
            $q = "SELECT * FROM #__{vm}_product_attribute_sku ";
            $q .= "WHERE product_id ='$product_id' ";
            if ($attribute_name) {
                $q .= "AND #__{vm}_product_attribute.attribute_name = $attribute_name ";
            }
            $q .= "ORDER BY attribute_list,attribute_name";
        } else {
            /* Error: no arguments were provided. */
            return 0;
        }

        $db->setQuery($q);
        $db->query();

        return $db;
    }

    /**
     * Function to return the product ids of all child items of product $pid
     *
     * @param int $pid The ID of the parent product
     * @return array $list
     */
    function get_child_product_ids($pid) {
        $db = new ps_DB;
        $q = "SELECT product_id FROM #__{vm}_product ";
        $q .= "WHERE product_parent_id='$pid' ";

        $db->setQuery($q);
        $db->query();

        $i = 0;
        $list = Array();
        while ($db->next_record()) {
            $list[$i] = $db->f("product_id");
            $i++;
        }
        return $list;
    }

    /**
     * Function to quickly check whether a product has child products or not
     *
     * @param int $pid The id of the product to check
     * @return boolean True when the product has childs, false when not
     */
    function parent_has_children($pid) {
        $db = new ps_DB;
        if (empty($GLOBALS['product_info'][$pid]["parent_has_children"])) {
            $q = "SELECT product_id as num_rows FROM #__{vm}_product WHERE product_parent_id='$pid' ";
            $db->setQuery($q);
            $db->query();
            if ($db->next_record()) {
                $GLOBALS['product_info'][$pid]["parent_has_children"] = True;
            } else {
                $GLOBALS['product_info'][$pid]["parent_has_children"] = False;
            }
        }
        return $GLOBALS['product_info'][$pid]["parent_has_children"];
    }

    /**
     * Function to quickly check whether a product has attributes or not
     *
     * @param int $pid The id of the product to check
     * @return boolean True when the product has attributes, false when not
     */
    function product_has_attributes($pid) {
        if (is_array($pid)) {
            return false;
        }
        $db = new ps_DB;
        if (empty($GLOBALS['product_info'][$pid]["product_has_attributes"])) {
            $q = "SELECT product_id FROM #__{vm}_product_attribute_sku WHERE product_id='$pid' ";
            $db->setQuery($q);
            $db->query();
            if ($db->next_record()) {
                $GLOBALS['product_info'][$pid]["product_has_attributes"] = True;
            } else {
                $GLOBALS['product_info'][$pid]["product_has_attributes"] = False;
            }
        }
        return $GLOBALS['product_info'][$pid]["product_has_attributes"];
    }

    /**
     * Get the value of the field $field_name for product $product_id from the product table
     *
     * @param int $product_id
     * @param string $field_name
     * @return string The value of the field $field_name for that product
     */
    function get_field($product_id, $field_name) {
        $db = new ps_DB;
        if (empty($GLOBALS['product_info'][$product_id][$field_name])) {
            $q = "SELECT  product_id, `$field_name` FROM #__{vm}_product WHERE product_id='$product_id'";
            $db->query($q);
            if ($db->next_record()) {
                $GLOBALS['product_info'][$product_id][$field_name] = $db->f($field_name);
            } else {
                $GLOBALS['product_info'][$product_id][$field_name] = false;
            }
        }
        return $GLOBALS['product_info'][$product_id][$field_name];
    }

    /**
     * Sets a global value for a fieldname for a specific product
     * Is to be used by other scripts to populate a field value for a prodct
     * that was already fetched from the database - so it doesn't need to e fetched again
     * Can be also used to override a value
     *
     * @param int $product_id
     * @param string $field_name
     * @param mixed $value
     */
    function set_field($product_id, $field_name, $value) {

        $GLOBALS['product_info'][$product_id][$field_name] = $value;
    }

    /**
     * This is a very time consuming function. 
     * It fetches the category flypage for a specific product id
     *
     * @param int $product_id
     * @return string The flypage value for that product
     */
    function get_flypage($product_id) {

        if (empty($_SESSION['product_sess'][$product_id]['flypage'])) {
            $db = new ps_DB;
            $productParentId = $product_id;
            do {
                $q = "SELECT
                                `#__{vm}_product`.`product_parent_id` AS product_parent_id,
                                `#__{vm}_category`.`category_flypage`
                        FROM
                                `#__{vm}_product`

                        LEFT JOIN `#__{vm}_product_category_xref` ON `#__{vm}_product_category_xref`.`product_id` = `#__{vm}_product`.`product_id`
                        LEFT JOIN `#__{vm}_category` ON `#__{vm}_product_category_xref`.`category_id` = `#__{vm}_category`.`category_id`

                        WHERE `#__{vm}_product`.`product_id`='$productParentId'
                        ";

                $db->query($q);
                $db->next_record();
                $productParentId = $db->f("product_parent_id");
            } while ($db->f("product_parent_id") && !$db->f("category_flypage"));

            if ($db->f("category_flypage")) {
                $_SESSION['product_sess'][$product_id]['flypage'] = $db->f("category_flypage");
            } else {
                $_SESSION['product_sess'][$product_id]['flypage'] = FLYPAGE;
            }
        }
        return $_SESSION['product_sess'][$product_id]['flypage'];
    }

    /**
     * Function to get the name of the vendor the product is associated with
     *
     * @param int $product_id
     * @return string The name of the vendor
     */
    function get_vendorname($product_id) {
        $db = new ps_DB;

        $q = "SELECT #__{vm}_vendor.vendor_name FROM #__{vm}_product, #__{vm}_vendor ";
        $q .= "WHERE #__{vm}_product.product_id='$product_id' ";
        $q .= "AND #__{vm}_vendor.vendor_id=#__{vm}_product.vendor_id";

        $db->query($q);
        $db->next_record();
        if ($db->f("vendor_name")) {
            return $db->f("vendor_name");
        } else {
            return "";
        }
    }

    /**
     * Function to get the name of a vendor by its id
     * @author pablo
     * @param int $vendor_id
     * @return string The name of the vendor
     */
    function get_vend_idname($vendor_id) {
        $db = new ps_DB;

        $q = "SELECT vendor_name,vendor_id FROM #__{vm}_vendor ";
        $q .= "WHERE vendor_id='$vendor_id'";

        $db->query($q);
        $db->next_record();
        if ($db->f("vendor_name")) {
            return $db->f("vendor_name");
        } else {
            return "";
        }
    }

    /**
     * Function to get the vendor_id of a product
     * @author pablo
     * @param int $product_id
     * @return int The vendor id
     */
    function get_vendor_id($product_id) {
        $db = new ps_DB;
        if (empty($_SESSION['product_sess'][$product_id]['vendor_id'])) {
            $q = "SELECT vendor_id FROM #__{vm}_product ";
            $q .= "WHERE product_id='$product_id' ";

            $db->query($q);
            $db->next_record();
            if ($db->f("vendor_id")) {
                $_SESSION['product_sess'][$product_id]['vendor_id'] = $db->f("vendor_id");
            } else {
                $_SESSION['product_sess'][$product_id]['vendor_id'] = "";
            }
        }
        return $_SESSION['product_sess'][$product_id]['vendor_id'];
    }

    /**
     * Function to get the manufacturer id the product $product_id is assigned to
     * @author soeren
     * @param int $product_id
     * @return int The manufacturer id
     */
    function get_manufacturer_id($product_id) {
        $db = new ps_DB;

        $q = "SELECT manufacturer_id FROM #__{vm}_product_mf_xref ";
        $q .= "WHERE product_id='$product_id' ";

        $db->query($q);
        $db->next_record();
        if ($db->f("manufacturer_id")) {
            return $db->f("manufacturer_id");
        } else {
            return false;
        }
    }

    /**
     * Functon to get the name of the manufacturer this product is assigned to
     *
     * @param int $product_id
     * @return string the manufacturer name
     */
    function get_mf_name($product_id) {
        $db = new ps_DB;

        $q = "SELECT mf_name,#__{vm}_manufacturer.manufacturer_id FROM #__{vm}_product_mf_xref,#__{vm}_manufacturer ";
        $q .= "WHERE product_id='$product_id' ";
        $q .= "AND #__{vm}_manufacturer.manufacturer_id=#__{vm}_product_mf_xref.manufacturer_id";

        $db->query($q);
        $db->next_record();
        if ($db->f("mf_name")) {
            return $db->f("mf_name");
        } else {
            return "";
        }
    }

    /**
     * Prints the img tag for the given product image
     *
     * @param string $image The name of the imahe OR the full URL to the image
     * @param string $args Additional attributes for the img tag
     * @param int $resize 
     * (1 = resize the image by using height and width attributes, 
     * 0 = do not resize the image)
     * @param string $path_appendix The path to be appended to IMAGEURL / IMAGEPATH
     */
    function show_image($image, $args = "", $resize = 1, $path_appendix = "product") {
        echo $this->image_tag($image, $args, $resize, $path_appendix);
    }

    /**
     * Returns the img tag for the given product image
     *
     * @param string $image The name of the imahe OR the full URL to the image
     * @param string $args Additional attributes for the img tag
     * @param int $resize 
     * (1 = resize the image by using height and width attributes, 
     * 0 = do not resize the image)
     * @param string $path_appendix The path to be appended to IMAGEURL / IMAGEPATH
     * @return The HTML code of the img tag
     */
    function image_tag($image, $args = "", $resize = 1, $path_appendix = "product") {
        global $mosConfig_live_site;
        $border = "";
        if (!strpos($args, "border="))
            $border = "border=\"0\"";

        if ($image != "") {
            // URL
            if (substr($image, 0, 4) == "http")
                $url = $image;

            // local image file
            else {
                if (PSHOP_IMG_RESIZE_ENABLE == '1' && $resize == 1)
                    $url = $mosConfig_live_site . "/components/com_virtuemart/show_image_in_imgtag.php?filename=" . urlencode($image) . "&newxsize=" . PSHOP_IMG_WIDTH . "&newysize=" . PSHOP_IMG_HEIGHT . "&fileout=";
                else
                    $url = IMAGEURL . $path_appendix . "/" . $image;
            }
        }
        else {
            $url = IMAGEURL . NO_IMAGE;
        }
        $html_height_width = "";
        $height_greater = false;
        if (file_exists(IMAGEPATH . $path_appendix . "/" . $image)) {
            $arr = @getimagesize(IMAGEPATH . $path_appendix . "/" . $image);
            $html_height_width = $arr[3];
            $height_greater = $arr[0] < $arr[1];
            if ((PSHOP_IMG_WIDTH < $arr[0] || PSHOP_IMG_HEIGHT < $arr[1]) && $resize != 0) {
                if ($height_greater)
                    $html_height_width = " height=\"" . PSHOP_IMG_HEIGHT . "\"";
                else
                    $html_height_width = " width=\"" . PSHOP_IMG_WIDTH . "\"";
            }
        }

        if ((PSHOP_IMG_RESIZE_ENABLE != '1') && ($resize == 1)) {
            /* if( $height_greater )
              $html_height_width = " height=\"".PSHOP_IMG_HEIGHT."\"";
              else
              $html_height_width = " width=\"".PSHOP_IMG_WIDTH."\"";
             */
            $products_per_row = 4;

            $imgWidth = ( $products_per_row > 3 ) ? (int) ( (int) PSHOP_IMG_WIDTH * 3 / $products_per_row ) : PSHOP_IMG_WIDTH;
            $imgHeight = ( $products_per_row > 3 ) ? (int) ( (int) PSHOP_IMG_HEIGHT * 3 / $products_per_row ) : PSHOP_IMG_HEIGHT;

            //$html_height_width = " height=\"$imgHeight\" width=\"$imgWidth\"";
            $html_height_width = ' height="262px" ';
        }

        return "<img src=\"$url\" $html_height_width $args $border />";
    }

    function image_tag2($image, $args = "", $resize = 1, $path_appendix = "product") {
        global $mosConfig_live_site;

        $border = "";
        if (!strpos($args, "border="))
            $border = "border=\"0\"";

        if ($image != "") {
            // URL
            if (substr($image, 0, 4) == "http")
                $url = $image;

            // local image file
            else {
                $url = IMAGEURL . $path_appendix . "/" . $image;
            }
        } else {
            $url = IMAGEURL . NO_IMAGE;
        }
        $html_height_width = 'width="100%"';
        return "<img src=\"$url\" $html_height_width $args $border />";
    }

    /**
     * Get the tax rate...
     * @author soeren
     * @return int The tax rate found
     */
    static function get_taxrate() {

        $ps_vendor_id = isset($_SESSION["ps_vendor_id"]) ? $_SESSION["ps_vendor_id"] : '';
        $auth = isset($_SESSION["auth"]) ? $_SESSION["auth"] : '';

        if (!defined('_PSHOP_ADMIN')) {

            $db = new ps_DB;

            if (isset($auth["show_price_including_tax"]) AND $auth["show_price_including_tax"] == 1) {

                if (TAX_MODE == '0') {
                    if ($auth["user_id"] > 0) {

                        $q = "SELECT state, country FROM #__{vm}_user_info WHERE user_id='" . $auth["user_id"] . "'";
                        $db->query($q);

                        $db->next_record();
                        $state = $db->f("state");
                        $country = $db->f("country");

                        $q = "SELECT tax_rate FROM #__{vm}_tax_rate WHERE tax_country='$country' ";
                        if (!empty($state)) {
                            $q .= "AND tax_state='$state'";
                        }
                        $db->query($q);
                        if ($db->next_record()) {
                            $_SESSION['taxrate'][$ps_vendor_id] = $db->f("tax_rate");
                        } else {
                            $_SESSION['taxrate'][$ps_vendor_id] = 0;
                        }
                    } else {
                        $_SESSION['taxrate'][$ps_vendor_id] = 0;
                    }
                } elseif (TAX_MODE == '1') {
                    if (empty($_SESSION['taxrate'][$ps_vendor_id])) {
                        // let's get the store's tax rate
                        $q = "SELECT tax_rate FROM #__{vm}_vendor, #__{vm}_tax_rate ";
                        $q .= "WHERE tax_country=vendor_country AND #__{vm}_vendor.vendor_id='1' ";
                        $q .= "ORDER BY `tax_rate` DESC";
                        $db->query($q);
                        if ($db->next_record()) {
                            $_SESSION['taxrate'][$ps_vendor_id] = $db->f("tax_rate");
                        } else {
                            $_SESSION['taxrate'][$ps_vendor_id] = 0;
                        }
                    }
                    return $_SESSION['taxrate'][$ps_vendor_id];
                }
            } else {
                $_SESSION['taxrate'][$ps_vendor_id] = 0;
            }

            return $_SESSION['taxrate'][$ps_vendor_id];
        } else {
            return 0;
        }
    }

    /**
     * Function to get the tax rate of product $product_id
     * If not found, it uses get_taxrate()
     *
     * @param int $product_id
     * @param int $weight_subtotal (tax virtual/zero-weight items?)
     * @return int The tax rate for the product
     */
    static function get_product_taxrate($product_id, $weight_subtotal = 0) {
        return 0;
        if (($weight_subtotal != 0 or TAX_VIRTUAL == '1') && TAX_MODE == '0') {
            $_SESSION['product_sess'][$product_id]['tax_rate'] = self::get_taxrate();
            return $_SESSION['product_sess'][$product_id]['tax_rate'];
        } elseif (($weight_subtotal == 0 or TAX_VIRTUAL != '1' ) && TAX_MODE == '0') {
            $_SESSION['product_sess'][$product_id]['tax_rate'] = 0;
            return $_SESSION['product_sess'][$product_id]['tax_rate'];
        } elseif (TAX_MODE == '1') {

            if (empty($_SESSION['product_sess'][$product_id]['tax_rate'])) {
                $db = new ps_DB;
                // Product's tax rate id has priority!
                $q = "SELECT product_weight, tax_rate FROM #__{vm}_product, #__{vm}_tax_rate ";
                $q .= "WHERE product_tax_id=tax_rate_id AND product_id='$product_id'";
                $db->query($q);
                if ($db->next_record()) {
                    $rate = $db->f("tax_rate");
                    $product_weight = $db->f('product_weight');
                    if ($weight_subtotal == 0 && $product_weight > 0) {
                        $weight_subtotal = $product_weight;
                    }
                } else {
                    // if we didn't find a product tax rate id, let's get the store's tax rate
                    //$rate = $this->get_taxrate();
                    $rate = self::get_taxrate();
                }
                if ($weight_subtotal != 0 or TAX_VIRTUAL == '1') {
                    $_SESSION['product_sess'][$product_id]['tax_rate'] = $rate;
                    return $rate;
                } else {
                    $_SESSION['product_sess'][$product_id]['tax_rate'] = 0;
                    return 0;
                }
            } else {
                return $_SESSION['product_sess'][$product_id]['tax_rate'];
            }
        }
        return 0;
    }

    /**
     * Function to get the "pure" undiscounted and untaxed price 
     * of product $product_id. Used by the administration section.
     *
     * @param int $product_id
     * @return array The product price information
     */
    static function get_retail_price($product_id) {

        $db = new ps_DB;
        $q = "SELECT p.product_price,p.product_currency, p.saving_price,p.discount_for_customer,pm.end_promotion, p.compare_at, `pm`.`discount` as promotion_discount,
          k.product_tax_id
FROM #__{vm}_product_price as p
JOIN jos_vm_product as k on k.product_id = p.product_id
LEFT JOIN (SELECT 
                CASE 
                    WHEN pmp.category_id > 0  THEN x.product_id
                    ELSE pmp.product_id
                END AS `product_id`,pmp.discount,pmp.end_promotion
                FROM `jos_vm_products_promotion` as pmp 
left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
 WHERE p.product_id='$product_id' AND ";
        $q .= "shopper_group_id='5'";
        $db->setQuery($q);
        $db->query();
        if ($db->next_record()) {
            $price_info["product_price"] = $db->f("product_price");
            $price_info["saving_price"] = $db->f("saving_price");
            $price_info["discount_for_customer"] = $db->f("discount_for_customer");
            $price_info["product_currency"] = $db->f("product_currency");
            $price_info["compare_at"] = $db->f("compare_at");
            $price_info["promotion_discount"] = $db->f("promotion_discount");
            $price_info["end_promotion"] = $db->f("end_promotion");
            $price_info["product_tax_id"] = $db->f("product_tax_id");
        } else {
            $price_info["product_price"] = "";
            $price_info["saving_price"] = "";
            $price_info["product_currency"] = isset($_SESSION['vendor_currency']) ? $_SESSION['vendor_currency'] : '';
            $price_info["compare_at"] = "";
        }
        return $price_info;
    }

    /**
     * Get the price of product $product_id for the shopper group associated
     * with $auth['user_id'] - including shopper group discounts
     *
     * @param int $product_id
     * @param boolean $check_multiple_prices Check if the product has more than one price for that shopper group?
     * @return array The product price information
     */
    function get_price($product_id, $check_multiple_prices = false) {
        $auth = $_SESSION['auth'];
        $cart = $_SESSION['cart'];

        if (empty($GLOBALS['product_info'][$product_id]['price']) || !empty($GLOBALS['product_info'][$product_id]['price']["product_has_multiple_prices"]) || $check_multiple_prices) {
            $db = new ps_DB;

            if (empty($_SESSION['product_sess'][$product_id]['vendor_id'])) {
                $_SESSION['product_sess'][$product_id]['vendor_id'] = $vendor_id = 1;
            } else {
                $vendor_id = $_SESSION['product_sess'][$product_id]['vendor_id'];
            }

            $shopper_group_id = $auth["shopper_group_id"];
            $shopper_group_discount = $auth["shopper_group_discount"];


            $GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_id'] = $default_shopper_group_id = 5;
            $GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_discount'] = $default_shopper_group_discount = 0;

            $price_info = Array();
            if (!$check_multiple_prices) {
                /* Added for Volume based prices */
                // This is an important decision: we add up all product quantities with the same product_id,
                // regardless to attributes. This gives "real" volume based discount, because our simple attributes
                // depend on one and the same product_id
                $quantity = 0;
                for ($i = 0; $i < $cart["idx"]; $i++) {
                    if (isset($cart[$i]["product_id"])) {
                        if ($cart[$i]["product_id"] == $product_id) {
                            $quantity += $cart[$i]["quantity"];
                        }
                    }
                }

                $volume_quantity_sql = " AND (('$quantity' >= price_quantity_start AND '$quantity' <= price_quantity_end)
                                OR (price_quantity_end='0') OR ('$quantity' > price_quantity_end)) ORDER BY price_quantity_end DESC";
                /* End Addition */
            } else {
                $volume_quantity_sql = " ORDER BY price_quantity_start";
            }

            // Getting prices
            //
			// If the shopper group has a price then show it, otherwise
            // show the default price.
            if (!empty($shopper_group_id)) {
                $q = "SELECT product_price, product_price_id, product_currency FROM #__{vm}_product_price WHERE product_id='$product_id' AND ";
                $q .= "shopper_group_id='$shopper_group_id' $volume_quantity_sql";
                $db->setQuery($q);
                $db->query();
                if ($db->next_record()) {
                    $price_info["product_price"] = $db->f("product_price");
                    if ($check_multiple_prices) {
                        $price_info["product_base_price"] = $db->f("product_price");
                        $price_info["product_has_multiple_prices"] = $db->num_rows() > 1;
                    }
                    $price_info["product_price_id"] = $db->f("product_price_id");
                    $price_info["product_currency"] = $db->f("product_currency");
                    $price_info["item"] = true;
                    $GLOBALS['product_info'][$product_id]['price'] = $price_info;
                    return $GLOBALS['product_info'][$product_id]['price'];
                }
            }
            // Get default price
            $q = "SELECT product_price, product_price_id, product_currency FROM #__{vm}_product_price WHERE product_id='$product_id' AND ";
            $q .= "shopper_group_id='$default_shopper_group_id' $volume_quantity_sql";
            $db->setQuery($q);
            $db->query();
            if ($db->next_record()) {
                $price_info["product_price"] = $db->f("product_price") * ((100 - $shopper_group_discount) / 100);
                if ($check_multiple_prices) {
                    $price_info["product_base_price"] = $price_info["product_price"];
                    $price_info["product_has_multiple_prices"] = $db->num_rows() > 1;
                }
                $price_info["product_price_id"] = $db->f("product_price_id");
                $price_info["product_currency"] = $db->f("product_currency");
                $price_info["item"] = true;
                $GLOBALS['product_info'][$product_id]['price'] = $price_info;
                return $GLOBALS['product_info'][$product_id]['price'];
            }

            // No price found
            $GLOBALS['product_info'][$product_id]['price'] = false;
            return $GLOBALS['product_info'][$product_id]['price'];
        } else {
            return $GLOBALS['product_info'][$product_id]['price'];
        }
    }

    /**
     * Adjusts the price from get_price for the selected attributes
     * @author Nathan Hyde <nhyde@bigDrift.com>
     * @author curlyroger from his post at <http://www.phpshop.org/phpbb/viewtopic.php?t=3052>
     *
     * @param int $product_id
     * @param string $description
     * @return array The adjusted price information
     */
    function get_adjusted_attribute_price($product_id, $description = '') {

        global $mosConfig_secret;
        $auth = $_SESSION['auth'];
        $price = $this->get_price($product_id);

        $base_price = $price["product_price"];
        $setprice = 0;
        $set_price = false;
        $adjustment = 0;

        // We must care for custom attribute fields! Their value can be freely given
        // by the customer, so we mustn't include them into the price calculation
        // Thanks to AryGroup@ua.fm for the good advice
        if (empty($_REQUEST["custom_attribute_fields"])) {
            if (!empty($_SESSION["custom_attribute_fields"])) {
                $custom_attribute_fields = mosGetParam($_SESSION, "custom_attribute_fields", Array());
                $custom_attribute_fields_check = mosGetParam($_SESSION, "custom_attribute_fields_check", Array());
            } else
                $custom_attribute_fields = $custom_attribute_fields_check = Array();
        }
        else {
            $custom_attribute_fields = $_SESSION["custom_attribute_fields"] = mosGetParam($_REQUEST, "custom_attribute_fields", Array());
            $custom_attribute_fields_check = $_SESSION["custom_attribute_fields_check"] = mosGetParam($_REQUEST, "custom_attribute_fields_check", Array());
        }

        // if we've been given a description to deal with, get the adjusted price
        if ($description != '') { // description is safe to use at this point cause it's set to ''
            $attribute_keys = explode(";", $description);

            foreach ($attribute_keys as $temp_desc) {

                $temp_desc = trim($temp_desc);
                // Get the key name (e.g. "Color" )
                $this_key = substr($temp_desc, 0, strpos($temp_desc, ":"));

                if (in_array($this_key, $custom_attribute_fields)) {
                    if (@$custom_attribute_fields_check[$this_key] == md5($mosConfig_secret . $this_key)) {
                        // the passed value is valid, don't use it for calculating prices
                        continue;
                    }
                }

                $i = 0;

                $start = strpos($temp_desc, "[");
                $finish = strpos($temp_desc, "]", $start);

                $o = substr_count($temp_desc, "[");
                $c = substr_count($temp_desc, "]");
                //echo "open: $o<br>close: $c<br>\n";
                // check to see if we have a bracket
                if (True == is_int($finish)) {
                    $length = $finish - $start;

                    // We found a pair of brackets (price modifier?)
                    if ($length > 1) {
                        $my_mod = substr($temp_desc, $start + 1, $length - 1);
                        //echo "before: ".$my_mod."<br>\n";
                        if ($o != $c) { // skip the tests if we don't have to process the string
                            if ($o < $c) {
                                $char = "]";
                                $offset = $start;
                            } else {
                                $char = "[";
                                $offset = $finish;
                            }
                            $s = substr_count($my_mod, $char);
                            for ($r = 1; $r < $s; $r++) {
                                $pos = strrpos($my_mod, $char);
                                $my_mod = substr($my_mod, $pos + 1);
                            }
                        }
                        $oper = substr($my_mod, 0, 1);

                        $my_mod = substr($my_mod, 1);


                        // if we have a number, allow the adjustment
                        if (true == is_numeric($my_mod)) {
                            // Now add or sub the modifier on
                            if ($oper == "+") {
                                $adjustment += $my_mod;
                            } else if ($oper == "-") {
                                $adjustment -= $my_mod;
                            } else if ($oper == '=') {
                                // NOTE: the +=, so if we have 2 sets they get added
                                // this could be moded to say, if we have a set_price, then
                                // calc the diff from the base price and start from there if we encounter
                                // another set price... just a thought.

                                $setprice += $my_mod;
                                $set_price = true;
                            }
                        }
                        $temp_desc = substr($temp_desc, $finish + 1);
                        $start = strpos($temp_desc, "[");
                        $finish = strpos($temp_desc, "]");
                    }
                }
                $i++; // not necessary, but perhaps interesting? ;)
            }
        }

        // no set price was set from the attribs
        if ($set_price == false) {
            $price["product_price"] = $base_price + $adjustment;
        } else {
            // otherwise, set the price
            // add the base price to the price set in the attributes
            // then subtract the adjustment amount
            // we could also just add the set_price to the adjustment... not sure on that one.
            // $setprice += $adjustment;
            $setprice *= 1 - ($auth["shopper_group_discount"] / 100);
            $price["product_price"] = $setprice;
        }

        // don't let negative prices get by, set to 0
        if ($price["product_price"] < 0) {
            $price["product_price"] = 0;
        }
        // Get the DISCOUNT AMOUNT
        $discount_info = $this->get_discount($product_id);

        $my_taxrate = $this->get_product_taxrate($product_id);
        //print_r($price);
        //echo "<br/><br/><br/>";

        if (!empty($discount_info["amount"])) {
            if ($auth["show_price_including_tax"] == 1) {
                switch ($discount_info["is_percent"]) {
                    //case 0: $price["product_price"] = (($price["product_price"]*($my_taxrate+1))-$discount_info["amount"])/($my_taxrate+1); break;
                    //case 1: $price["product_price"] = ($price["product_price"] - $discount_info["amount"]/100*$price["product_price"]); break;
                    case 0: $price["product_price"] = (($price["product_price"]) - $discount_info["amount"]);
                        break;
                    case 1: $price["product_price"] = ($price["product_price"] - ($discount_info["amount"] / 100) * $price["product_price"]);
                        break;
                }
            } else {
                switch ($discount_info["is_percent"]) {
                    case 0: $price["product_price"] = (($price["product_price"]) - $discount_info["amount"]);
                        break;
                    case 1: $price["product_price"] = ($price["product_price"] - ($discount_info["amount"] / 100) * $price["product_price"]);
                        break;
                }
            }
        }

        //print_r($price);

        return $price;
    }

    /**
     * This function can parse an "advanced / custom attribute"
     * description like
     * Size:big[+2.99]; Color:red[+0.99]
     * and return the same string with values, tax added
     * Size: big (+3.47), Color: red (+1.15)
     * 
     * @param string $description
     * @param int $product_id
     * @return string The reformatted description
     */
    function getDescriptionWithTax($description, $product_id = 0) {
        global $CURRENCY_DISPLAY, $mosConfig_secret;
        $auth = $_SESSION['auth'];
        $description = stripslashes($description);
        // if we've been given a description to deal with, get the adjusted price
        if ($description != '' && stristr($description, "[") && $product_id != 0) {
            if ($auth["show_price_including_tax"] == 1) {
                $my_taxrate = $this->get_product_taxrate($product_id);
            } else {
                $my_taxrate = 0.00;
            }

            // We must care for custom attribute fields! Their value can be freely given
            // by the customer, so we mustn't include them into the price calculation
            // Thanks to AryGroup@ua.fm for the good advice
            if (empty($_REQUEST["custom_attribute_fields"])) {
                if (!empty($_SESSION["custom_attribute_fields"])) {
                    $custom_attribute_fields = mosGetParam($_SESSION, "custom_attribute_fields", Array());
                    $custom_attribute_fields_check = mosGetParam($_SESSION, "custom_attribute_fields_check", Array());
                } else {
                    $custom_attribute_fields = $custom_attribute_fields_check = Array();
                }
            } else {
                $custom_attribute_fields = $_SESSION["custom_attribute_fields"] = mosGetParam($_REQUEST, "custom_attribute_fields", Array());
                $custom_attribute_fields_check = $_SESSION["custom_attribute_fields_check"] = mosGetParam($_REQUEST, "custom_attribute_fields_check", Array());
            }

            $attribute_keys = explode(";", $description);

            foreach ($attribute_keys as $temp_desc) {

                $temp_desc = trim($temp_desc);
                // Get the key name (e.g. "Color" )
                $this_key = substr($temp_desc, 0, strpos($temp_desc, ":"));

                if (in_array($this_key, $custom_attribute_fields)) {
                    if (@$custom_attribute_fields_check[$this_key] == md5($mosConfig_secret . $this_key)) {
                        // the passed value is valid, don't use it for calculating prices
                        continue;
                    }
                }
                $i = 0;

                $start = strpos($temp_desc, "[");
                $finish = strpos($temp_desc, "]", $start);

                $o = substr_count($temp_desc, "[");
                $c = substr_count($temp_desc, "]");

                // check to see if we have a bracket
                if (True == is_int($finish)) {
                    $length = $finish - $start;

                    // We found a pair of brackets (price modifier?)
                    if ($length > 1) {
                        $my_mod = substr($temp_desc, $start + 1, $length - 1);

                        //echo "before: ".$my_mod."<br>\n";
                        if ($o != $c) { // skip the tests if we don't have to process the string
                            if ($o < $c) {
                                $char = "]";
                                $offset = $start;
                            } else {
                                $char = "[";
                                $offset = $finish;
                            }
                            $s = substr_count($my_mod, $char);
                            for ($r = 1; $r < $s; $r++) {
                                $pos = strrpos($my_mod, $char);
                                $my_mod = substr($my_mod, $pos + 1);
                            }
                        }

                        $value_notax = substr($my_mod, 1);

                        if (abs($value_notax) > 0) {
                            $value_taxed = $value_notax * ($my_taxrate + 1);

                            $description = str_replace($value_notax, $CURRENCY_DISPLAY->getFullValue($value_taxed), $description);
                        } elseif ($my_mod === "+0" || $my_mod === '-0') {
                            $description = str_replace("[" . $my_mod . "]", '', $description);
                        }
                        $temp_desc = substr($temp_desc, $finish + 1);
                        $start = strpos($temp_desc, "[");
                        $finish = strpos($temp_desc, "]");
                    }
                }
                $i++; // not necessary, but perhaps interesting? ;)
            }
        }
        $description = str_replace($CURRENCY_DISPLAY->symbol, '@saved@', $description);
        $description = str_replace("[", " (", $description);
        $description = str_replace("]", ")", $description);
        $description = str_replace(":", ": ", $description);
        $description = str_replace(";", "<br/>", $description);
        $description = str_replace('@saved@', $CURRENCY_DISPLAY->symbol, $description);

        return $description;
    }

    /*     * ************************************************************************
     * * name: show_price
     * * created by: soeren
     * * description: display a Price, formatted and with Discounts
     * * parameters: int product_id
     * * returns:
     * ************************************************************************* */

    /**
     * Function to calculate the price, apply discounts from the discount table
     * and reformat the price
     *
     * @param int $product_id
     * @param boolean $hide_tax Wether to show the text "(including X.X% tax)" or not
     * @return string The formatted price
     */
    function show_price($product_id, $hide_tax = false) {
        global $VM_LANG, $CURRENCY_DISPLAY, $vendor_mail;
        $auth = $_SESSION['auth'];

        $product_name = htmlentities($this->get_field($product_id, 'product_name'), ENT_QUOTES);
        $no_price_html = "&nbsp;<a href=\"mailto:$vendor_mail?subject=" . $VM_LANG->_PHPSHOP_PRODUCT_CALL . ": $product_name\">" . $VM_LANG->_PHPSHOP_PRODUCT_CALL . "</a>";

        if ($auth['show_prices']) {
            // Get the DISCOUNT AMOUNT
            $discount_info = $this->get_discount($product_id);

            // Get the Price according to the quantity in the Cart
            $price_info = $this->get_price($product_id);
            // Get the Base Price of the Product
            $base_price_info = $this->get_price($product_id, true);
            if ($price_info === false) {
                $price_info = $base_price_info;
            }
            $html = "";
            $undiscounted_price = 0;
            //   var_dump($price_info);
            if (isset($price_info["product_price_id"]) && $price_info["product_price"] > 0) {

                $base_price = $base_price_info["product_price"] + $base_price_info["product_price"] * 0.1;
                $price = $price_info["product_price"];
                //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
                //if ($auth["show_price_including_tax"] == 1) {
                //	$my_taxrate = $this->get_product_taxrate($product_id);
                //	$base_price += ($my_taxrate * $base_price);
                //}
                //else {
                //	$my_taxrate = 0;
                //}
                // Calculate discount
                if (!empty($discount_info["amount"])) {
                    $undiscounted_price = $base_price;
                    switch ($discount_info["is_percent"]) {
                        case 0: $base_price -= $discount_info["amount"];
                            break;
                        case 1: $base_price *= (100 - $discount_info["amount"]) / 100;
                            break;
                    }
                }
                $text_including_tax = "";
                if (!empty($my_taxrate)) {
                    $tax = $my_taxrate * 100;
                    // only show "including x % tax" when it shall
                    // not be hidden
                    if (!$hide_tax && $auth["show_price_including_tax"] == 1 && VM_PRICE_SHOW_INCLUDINGTAX) {
                        $text_including_tax = $VM_LANG->_PHPSHOP_INCLUDING_TAX;
                        eval("\$text_including_tax = \"$text_including_tax\";");
                    }
                }
                if (!empty($discount_info["amount"])) {
                    $html .= "<table width='100%' ><tr><td><strong>Bloomex $VM_LANG->_PHPSHOP_PRODUCT_RPRICE: </strong></td><td> <span style=\"color:red;\">\n<strike>";
                    $html .= $CURRENCY_DISPLAY->getFullValue($undiscounted_price);
                    $html .= "</strike> $text_including_tax</span></td>";
                    $html .= "</tr><tr><td><span style=\"font-weight:bold\">\nBLOOMEX $VM_LANG->_PHPSHOP_PRODUCT_SPRICE: </td><td>";
                    $html .= $CURRENCY_DISPLAY->getFullValue($base_price);
                    $html .= "</span> ";
                    $html .= $text_including_tax;
                    $html .= "</td></tr><tr><td> <span style=\"color:red;\">";
                    $html .= "$VM_LANG->_PHPSHOP_PRODUCT_SVPRICE:</span></td><td> ";
                    //if($discount_info["is_percent"]==1)
                    //$html .= $discount_info["amount"]."%";
                    //	else
                    $html .= $CURRENCY_DISPLAY->getFullValue($undiscounted_price - $base_price);
                    $html .= "</td></tr></table>";
                } else {
                    $html .= "<table width='100%'><tr><td><span style=\"font-weight:bold\">\n$VM_LANG->_PHPSHOP_PRODUCT_NPRICE: </td><td>";
                    $html .= $CURRENCY_DISPLAY->getFullValue($base_price);
                    $html .= "</span>\n ";
                    $html .= $text_including_tax;
                    $html .= "</td></tr></table>";
                }







                // Check if we need to display a Table with all Quantity <=> Price Relationships
                if ($base_price_info["product_has_multiple_prices"] && !$hide_tax) {
                    $db = new ps_DB;
                    // Quantity Discount Table
                    $q = "SELECT product_price, price_quantity_start, price_quantity_end FROM #__{vm}_product_price
				  WHERE product_id='$product_id' AND shopper_group_id='" . $auth["shopper_group_id"] . "' ORDER BY price_quantity_start";
                    $db->query($q);

                    //         $prices_table = "<table align=\"right\">
                    $prices_table = "<table width=\"100%\">
					  <thead><tr class=\"sectiontableheader\">
					  <th>" . $VM_LANG->_PHPSHOP_CART_QUANTITY . "</th>
					  <th>" . $VM_LANG->_PHPSHOP_CART_PRICE . "</th>
					  </tr></thead>
					  <tbody>";
                    $i = 1;
                    while ($db->next_record()) {

                        $prices_table .= "<tr class=\"sectiontableentry$i\"><td>" . $db->f("price_quantity_start") . " - " . $db->f("price_quantity_end") . "</td>";
                        $prices_table .= "<td>";
                        if (!empty($my_taxrate))
                            $prices_table .= $CURRENCY_DISPLAY->getFullValue(($my_taxrate + 1) * $db->f("product_price"));
                        else
                            $prices_table .= $CURRENCY_DISPLAY->getFullValue($db->f("product_price"));
                        $prices_table .= "</td></tr>";
                        $i == 1 ? $i++ : $i--;
                    }
                    $prices_table .= "</tbody></table>";
                    if (@$_REQUEST['page'] == "shop.browse") {
                        $html .= mm_ToolTip($prices_table);
                    } else
                        $html .= $prices_table;
                }
            }
            // No price, so display "Call for pricing"
            else {
                $html = $no_price_html;
            }
            return $html;
        } else
            return $no_price_html;
    }

    function show_price_minimum($product_id) {
        global $database, $my;
        //IMPLEMENT #5055
        $aPrice = $this->get_retail_price($product_id);
        if (!empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0) {
            $product_price = ($aPrice["product_price"] + $aPrice["product_price"] * 0.1) - $aPrice["saving_price"];
        } else {
            $product_price = $aPrice["product_price"];
            $product_price = $product_price + $product_price * 0.1;
        }


        $product_price = floatval($product_price);
        $price_html = LangNumberFormat::number_format($product_price, 2, '.', '') . "";
        return $price_html;
    }

    /*     * ************************************************************************
     * * name: get_discount
     * * created by: soeren
     * * description: display a Price, formatted and with Discounts
     * * parameters: int product_id
     * * returns:
     * ************************************************************************* */

    /**
     * Get the information about the discount for a product
     *
     * @param int $product_id
     * @return array The discount information
     */
    function get_discount($product_id) {
        global $mosConfig_lifetime;

        // We use the Session now to store the discount info for
        // each product. But this info can change regularly,
        // so we check if the session time has expired
        if (empty($_SESSION['product_sess'][$product_id]['discount_info']) || (time() - $_SESSION['product_sess'][$product_id]['discount_info']['create_time'] ) > $mosConfig_lifetime) {
            $db = new ps_DB;
            $starttime = time();
            $year = date('Y');
            $month = date('n');
            $day = date('j');
            // get the beginning time of today
            $endofday = mktime(0, 0, 0, $month, $day, $year) - 1440;

            // Get the DISCOUNT AMOUNT
            $q = "SELECT amount,is_percent FROM #__{vm}_product,#__{vm}_product_discount ";
            $q .= "WHERE product_id='$product_id' AND (start_date<='$starttime' OR start_date=0) AND (end_date>='$endofday' OR end_date=0) ";
            $q .= "AND product_discount_id=discount_id";
            $db->query($q);
            if ($db->next_record()) {
                $discount_info["amount"] = $db->f("amount");
                $discount_info["is_percent"] = $db->f("is_percent");
            } else {
                $discount_info["amount"] = 0;
                $discount_info["is_percent"] = 0;
            }
            $discount_info['create_time'] = time();
            $_SESSION['product_sess'][$product_id]['discount_info'] = $discount_info;
            return $discount_info;
        } else
            return $_SESSION['product_sess'][$product_id]['discount_info'];
    }

    function show_snapshot2($product_sku, $show_price = true, $show_addtocart = true, $category_id = 0, $i = 0, $r1 = 0, $r2 = 0, $r3 = 0) {

        echo $this->product_snapshot2($product_sku, $show_price, $show_addtocart, $category_id, $i, $r1, $r2, $r3);
    }

    function product_snapshot2($product_sku, $show_price = true, $show_addtocart = true, $category_id = 0, $i = 0, $r1 = 0, $r2 = 0, $r3 = 0) {

        global $sess, $VM_LANG, $mm_action_url, $mosConfig_lang, $mosConfig_live_site, $database, $my, $mosConfig_footerCategories;

        //CALCULATE SHOPPER GROUP DISCOUNT
        $ShopperGroupDiscount = 0;
        $query = " SELECT SG.shopper_group_discount 
						FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id  	
						WHERE  SVX.user_id = " . $my->id . " LIMIT 1";
        $database->setQuery($query);
        $ShopperGroupDiscount = $database->loadResult();

        $nShopperGroupDiscount = 0;
        if (!empty($ShopperGroupDiscount)) {
            if (!empty($ShopperGroupDiscount) && $ShopperGroupDiscount > 0) {
                $nShopperGroupDiscount = floatval($ShopperGroupDiscount) / 100;
            }
        }

        $db = new ps_DB;

        require_once(CLASSPATH . 'ps_product_category.php');
        $ps_product_category = new ps_product_category;
        $q = "SELECT product_id, product_name, product_parent_id, product_thumb_image,product_sku FROM #__{vm}_product WHERE product_sku='$product_sku'";
        $db->query($q);
        $html = "";
        if ($db->next_record()) {

            $cid = $ps_product_category->get_cid($db->f("product_id"));
            //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
            /* if (strlen($db->f("product_name"))<=35) {
              $html .= "<span class=\"product-name\">".$db->f("product_name")."</span><br>\n";
              } else {
              $html .= "<span class=\"product-name\">".$db->f("product_name")."</span>\n";
              } */
            if ($db->f("product_parent_id")) {
                $url = "?page=shop.product_details&category_id=$cid&flypage=" . $this->get_flypage($db->f("product_parent_id"));
                $url .= "&product_id=" . $db->f("product_parent_id");
            } else {
                $url = "?page=shop.product_details&category_id=$cid&flypage=" . $this->get_flypage($db->f("product_id"));
                $url .= "&product_id=" . $db->f("product_id");
            }

            $html .= "<a class=\"productlist_a_title\" title=\"" . $db->f("product_name") . "\" href=\"" . $sess->url($mm_action_url . "index.php" . $url) . "\">";
            $html .= "<span class=\"product-name\">" . $db->f("product_name") . "</span>\n";
            $html .= "</a><br />\n";



            //IMPLEMENT PRICE
            $product_price = 0;
            $aPrice = $this->get_retail_price($db->f("product_id"));
            $my_taxrate = $this->get_product_taxrate($db->f("product_id"));
            /* echo $db->f("product_id");
              print_r($aPrice);
              echo "<br/>"; */
            $sale = '';
            if (!empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0) {
                $product_price = (($aPrice['product_price'] - $aPrice['saving_price']) + ($aPrice['product_price']) * $my_taxrate);
                //$sale = "<img src='/images/sale.png' class='sale' />";
            } else {
                $product_price = $aPrice["product_price"] + $aPrice["product_price"] * $my_taxrate;
            }

            //$product_price = floatval($product_price) - ( floatval($product_price) * floatval($nShopperGroupDiscount) );
            $product_price = floatval($product_price);
            //IMPLEMENT PRICE

            $html .= "<span class=\"product-sku\">" . $db->f("product_sku") . "</span>\n";
            if (_SHOW_PRICES == '1' && $show_price) {
                // Show price, but without "including X% tax"
                if (empty($product_price) || $product_price <= 0) {
                    $product_name = htmlentities($this->get_field($product_id, 'product_name'), ENT_QUOTES);
                    $price_html = "&nbsp;<a href=\"mailto:$vendor_mail?subject=" . $VM_LANG->_PHPSHOP_PRODUCT_CALL . ": $product_name\">" . $VM_LANG->_PHPSHOP_PRODUCT_CALL . "</a>";
                } else {
                    $price_html = LangNumberFormat::number_format($product_price, 2, '.', '') . "";
                }

                $html .= "<span class=\"product-price\">" . $price_html . "</span>$sale\n";
                //$html .= $this->show_price( $db->f("product_id"), true );
            }

            //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
            $html .= "<a title=\"" . $db->f("product_name") . "\" href=\"" . $sess->url($mm_action_url . "index.php" . $url) . "\">";
            $html .= $this->image_tag($db->f("product_thumb_image"), "alt=\"" . $db->f("product_name") . "\"");
            $html .= "</a><br />\n";

            if (USE_AS_CATALOGUE != 1 && $show_addtocart && !strstr($html, $VM_LANG->_PHPSHOP_PRODUCT_CALL)) {
                switch ($mosConfig_lang) {
                    case 'french':
                        $sBtnImage = "button_fr.png";
                        break;

                    case 'english':
                    default:
                        /*
                          for christmas new_button2.png
                          not for christmas button.png
                         */
                        $sBtnImage = "new_button2.png";
                        break;
                }

                $html .= "<div class='form-add-cart' id='div_" . $db->f("product_id") . "'>
									<form class='add-to-cart' action='index.php' method=\"post\" name=\"addtocart\" id=\"formAddToCart_" . $db->f("product_id") . "\">\n                
										<a class='add-to-cart mod-add-to-cart' name='" . $db->f("product_id") . "' onclick='return false;' title=\"" . $VM_LANG->_PHPSHOP_CART_ADD_TO . ": " . $db->f("product_name") . "\" href=\"#\"><img alt='Add to Cart' src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/ps_image/" . $sBtnImage . "\" border='0' class=\"add-to-cart-img\"></a>
										<input type=\"hidden\" name=\"category_id_" . $db->f("product_id") . "\" value=\"\" />\n
										<input name=\"quantity_" . $db->f("product_id") . "\" class=\"inputbox\" type=\"hidden\" size=\"3\" value=\"1\" />                
										<input type=\"hidden\" name=\"product_id_" . $db->f("product_id") . "\" value=\"" . $db->f("product_id") . "\" />\n
										<input type=\"hidden\" name=\"price_" . $db->f("product_id") . "\" value=\"" . $product_price . "\" />\n
									</form></div>";
            }
        }

        return $html;
    }

    /**
     * display a snapshot of a product based on the product sku.
     * This was written to provide a quick way to display a product inside of modules
     *
     * @param string $product_sku The SKU identifying the product
     * @param boolean $show_price Show the product price?
     * @param boolean $show_addtocart Show the add-to-cart link?
     */
    function show_snapshot($product_sku, $show_price = true, $show_addtocart = true) {

        echo $this->product_snapshot($product_sku, $show_price, $show_addtocart);
    }

    function show_snapshot_landing($product_sku, $show_price = true, $show_addtocart = true) {

        echo $this->product_snapshot_landing($product_sku, $show_price, $show_addtocart);
    }

    function product_snapshot($product_sku, $show_price = true, $show_addtocart = true) {

        global $sess, $VM_LANG, $mm_action_url, $mosConfig_lang, $mosConfig_live_site;

        $db = new ps_DB;

        require_once(CLASSPATH . 'ps_product_category.php');
        $ps_product_category = new ps_product_category;
        $q = "SELECT product_id, product_name, product_parent_id, product_thumb_image,product_sku FROM #__{vm}_product WHERE product_sku='$product_sku'";
        $db->query($q);
        $html = "";
        if ($db->next_record()) {
            $html .= '<div class="product-list">';
            $cid = $ps_product_category->get_cid($db->f("product_id"));
            //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
            $html .= "<span class=\"product-title-related\">" . $db->f("product_name") . "</span>\n";
            $html .= "<span class=\"sku-code-related\">" . $db->f("product_sku") . "</span>\n";
            //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
            if (_SHOW_PRICES == '1' && $show_price) {
                $html .= "<span class=\"price-related\">" . $this->show_price($db->f("product_id"), true) . "</span>\n";
            }
            if ($db->f("product_parent_id")) {
                $url = "?page=shop.product_details&category_id=$cid&flypage=" . $this->get_flypage($db->f("product_parent_id"));
                $url .= "&product_id=" . $db->f("product_parent_id");
            } else {
                $url = "?page=shop.product_details&category_id=$cid&flypage=" . $this->get_flypage($db->f("product_id"));
                $url .= "&product_id=" . $db->f("product_id");
            }
            $html .= "<a title=\"" . $db->f("product_name") . "\" href=\"" . $sess->url($mm_action_url . "index.php" . $url) . "\">";
            $html .= $this->image_tag($db->f("product_thumb_image"), "alt=\"" . $db->f("product_name") . "\"");
            $html .= "</a><br />\n";


            if (USE_AS_CATALOGUE != 1 && $show_addtocart && !strstr($html, $VM_LANG->_PHPSHOP_PRODUCT_CALL)) {
                switch ($mosConfig_lang) {
                    case 'french':
                        $sBtnImage = "button_fr.png";
                        break;

                    case 'english':
                    default:
                        $sBtnImage = "button.png";
                        break;
                }

                $aPrice = ps_product::get_price($db->f("product_id"));
                $html .= "<div class='form-add-cart' id='div_" . $db->f("product_id") . "'>
									<form class='add-to-cart' action='index.php' method=\"post\" name=\"addtocart\" id=\"formAddToCart_" . $db->f("product_id") . "\">\n                
										<a class='add-to-cart mod-add-to-cart' name='" . $db->f("product_id") . "' onclick='return false;' title=\"" . $VM_LANG->_PHPSHOP_CART_ADD_TO . ": " . $db->f("product_name") . "\" href=\"#\"><img src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/ps_image/" . $sBtnImage . "\" border='0'></a>
										<input type=\"hidden\" name=\"category_id_" . $db->f("product_id") . "\" value=\"\" />\n
										<input name=\"quantity_" . $db->f("product_id") . "\" class=\"inputbox\" type=\"hidden\" size=\"3\" value=\"1\" />                
										<input type=\"hidden\" name=\"product_id_" . $db->f("product_id") . "\" value=\"" . $db->f("product_id") . "\" />\n
										<input type=\"hidden\" name=\"price_" . $db->f("product_id") . "\" value=\"" . $aPrice['product_price'] . '!!!' . "\" />\n
									</form></div>";
            }
            $html .= '<br /></div>';
        }

        return $html;
    }

    function product_snapshot_landing($product_sku, $show_price = true, $show_addtocart = true) {

        global $sess, $VM_LANG, $mm_action_url, $mosConfig_lang, $mosConfig_live_site;

        $db = new ps_DB;

        require_once(CLASSPATH . 'ps_product_category.php');
        $ps_product_category = new ps_product_category;
        $q = "SELECT product_id, product_name, product_parent_id, product_thumb_image,product_sku FROM #__{vm}_product WHERE product_sku='$product_sku'";
        $db->query($q);
        $html = "";
        if ($db->next_record()) {
            $cid = $ps_product_category->get_cid($db->f("product_id"));

            if ($db->f("product_parent_id")) {
                $url = "?page=shop.product_details&category_id=$cid&flypage=" . $this->get_flypage($db->f("product_parent_id"));
                $url .= "&product_id=" . $db->f("product_parent_id");
            } else {
                $url = "?page=shop.product_details&category_id=$cid&flypage=" . $this->get_flypage($db->f("product_id"));
                $url .= "&product_id=" . $db->f("product_id");
            }

            //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
            $html .= "<a class=\"product-title\" href=\"" . $sess->url($mm_action_url . "index.php" . $url) . "\">" . $db->f("product_name") . "</a>\n";
            $html .= "<span class=\"sku-code\">" . $db->f("product_sku") . "</span>\n";
            //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
            if (_SHOW_PRICES == '1' && $show_price) {
                $html .= "<span class=\"price\">" . $this->show_price_minimum($db->f("product_id"), true) . "</span>\n";
            }

            $html .= "<div class=\"product-image\"><a title=\"" . $db->f("product_name") . "\" href=\"" . $sess->url($mm_action_url . "index.php" . $url) . "\">";
            $html .= $this->image_tag($db->f("product_thumb_image"), "alt=\"" . $db->f("product_name") . "\"");
            $html .= "</a></div>\n";


            if (USE_AS_CATALOGUE != 1 && $show_addtocart && !strstr($html, $VM_LANG->_PHPSHOP_PRODUCT_CALL)) {
                switch ($mosConfig_lang) {
                    case 'french':
                        $sBtnImage = "button_fr.png";
                        break;

                    case 'english':
                    default:
                        /*
                          for christmas new_button2.png
                          not for christmas button.png
                         */
                        $sBtnImage = "new_button2.png";
                        break;
                }

                $aPrice = ps_product::get_price($db->f("product_id"));
                $html .= "<div class='form-add-cart' id='div_" . $db->f("product_id") . "'>
									<form class='add-to-cart' action='index.php' method=\"post\" name=\"addtocart\" id=\"formAddToCart_" . $db->f("product_id") . "\">\n                
										<a class='add-to-cart mod-add-to-cart' name='" . $db->f("product_id") . "' onclick='return false;' title=\"" . $VM_LANG->_PHPSHOP_CART_ADD_TO . ": " . $db->f("product_name") . "\" href=\"#\"><img alt='Add to Cart' src=\"" . $mosConfig_live_site . "/components/com_virtuemart/shop_image/ps_image/" . $sBtnImage . "\" border='0'></a>
										<input type=\"hidden\" name=\"category_id_" . $db->f("product_id") . "\" value=\"\" />\n
										<input name=\"quantity_" . $db->f("product_id") . "\" class=\"inputbox\" type=\"hidden\" size=\"3\" value=\"1\" />                
										<input type=\"hidden\" name=\"product_id_" . $db->f("product_id") . "\" value=\"" . $db->f("product_id") . "\" />\n
										<input type=\"hidden\" name=\"price_" . $db->f("product_id") . "\" value=\"" . $aPrice['product_price'] . '!!!' . "\" />\n
									</form></div>";
            }
        }

        return $html;
    }

    /**
     * Prints a drop-down list of vendor names and their ids.
     *
     * @param int $vendor_id
     */
    function list_vendor($vendor_id = '1') {

        $db = new ps_DB;

        $q = "SELECT vendor_id,vendor_name FROM #__{vm}_vendor ORDER BY vendor_name";
        $db->query($q);
        $db->next_record();

        // If only one vendor do not show list
        if ($db->num_rows() == 1) {
            echo "<input type=\"hidden\" name=\"vendor_id\" value=\"";
            echo $db->f("vendor_id");
            echo "\" />";
            echo $db->f("vendor_name");
        } elseif ($db->num_rows() > 1) {
            $db->reset();
            $code = "<select name=\"vendor_id\">\n";
            while ($db->next_record()) {
                $code .= "  <option value=\"" . $db->f("vendor_id") . "\"";
                if ($db->f("vendor_id") == $vendor_id) {
                    $code .= " selected=\"selected\"";
                }
                $code .= ">" . $db->f("vendor_name") . "</option>\n";
            }
            $code .= "</select><br />\n";
            echo $code;
        }
    }

    /**
     * Print the name of vendor $vend_id
     *
     * @param int $vend_id
     */
    function show_vendorname($vend_id) {

        echo $this->getVendorName($vend_id);
    }

    /**
     * Return the name of vendor $id
     *
     * @param unknown_type $id
     * @return unknown
     */
    function getVendorName($id) {

        $db = new ps_DB;

        $q = "SELECT vendor_name FROM #__{vm}_vendor WHERE vendor_id='$id'";
        $db->query($q);
        $db->next_record();
        return $db->f("vendor_name");
    }

    /**
     * Prints a drop-down list of manufacturer names and their ids.
     *
     * @param int $manufacturer_id
     */
    function list_manufacturer($manufacturer_id = '0') {

        $db = new ps_DB;

        $q = "SELECT manufacturer_id,mf_name FROM #__{vm}_manufacturer ORDER BY mf_name";
        $db->query($q);
        $db->next_record();

        // If only one vendor do not show list
        if ($db->num_rows() == 1) {

            echo "<input type=\"hidden\" name=\"manufacturer_id\" value=\"";
            echo $db->f("manufacturer_id");
            echo "\" />";
            echo $db->f("mf_name");
        } elseif ($db->num_rows() > 1) {
            $db->reset();
            $code = "<select name=\"manufacturer_id\">\n";
            while ($db->next_record()) {
                $code .= "  <option value=\"" . $db->f("manufacturer_id") . "\"";
                if ($db->f("manufacturer_id") == $manufacturer_id) {
                    $code .= " selected=\"selected\"";
                }
                $code .= ">" . $db->f("mf_name") . "</option>\n";
            }
            $code .= "</select><br />\n";
            echo $code;
        } else {
            echo "<input type=\"hidden\" name=\"manufacturer_id\" value=\"1\" />Please create at least one Manufacturer!!";
        }
    }

    /**
     * Use this function if you need the weight of a product
     *
     * @param int $prod_id
     * @return int The weight of the product
     */
    function get_weight($prod_id) {

        $db = new ps_DB;

        $q = "SELECT product_weight FROM #__{vm}_product WHERE product_id='" . $prod_id . "'";
        $db->query($q);
        $db->next_record();
        return $db->f("product_weight");
    }

    /**
     * Print the availability HTML code for product $prod_id
     *
     * @param int $prod_id
     */
    function show_availability($prod_id) {
        echo $this->get_availability($prod_id);
    }

    /**
     * Returns the availability information as HTML code
     * @author soeren
     * @param unknown_type $prod_id
     * @return unknown
     */
    function get_availability($prod_id) {
        global $VM_LANG;

        $html = "";

        $is_parent = $this->parent_has_children($prod_id);
        if (!$is_parent) {
            $db = new ps_DB;

            $q = "SELECT product_available_date,product_availability,product_in_stock  FROM #__{vm}_product WHERE ";
            $q .= "product_id='" . $prod_id . "'";

            $db->query($q);
            $db->next_record();
            $pad = $db->f("product_available_date");
            $pav = $db->f("product_availability");

            $heading = "<div style=\"text-decoration:underline;font-weight:bold;\">" . $VM_LANG->_PHPSHOP_AVAILABILITY . "</div><br />";

            if (CHECK_STOCK == '1') {
                if ($db->f("product_in_stock") < 1) {
                    $html .= $VM_LANG->_PHPSHOP_CURRENTLY_NOT_AVAILABLE . "<br />";
                    if ($pad > time()) {
                        $html .= $VM_LANG->_PHPSHOP_PRODUCT_AVAILABLE_AGAIN;
                        $html .= date("d.m.Y", $pad) . "<br /><br />";
                        define('_PHSHOP_PRODUCT_NOT_AVAILABLE', '1');
                    }
                } elseif ($pad > time()) {
                    $html .= $VM_LANG->_PHPSHOP_CURRENTLY_NOT_AVAILABLE . "<br />";
                    $html .= $VM_LANG->_PHPSHOP_PRODUCT_AVAILABLE_AGAIN;
                    $html .= date("d.m.Y", $pad) . "<br /><br />";
                    define('_PHSHOP_PRODUCT_NOT_AVAILABLE', '1');
                } else {
                    $html .= "<span style=\"font-weight:bold;\">" . $VM_LANG->_PHPSHOP_PRODUCT_FORM_IN_STOCK . ": </span>" . $db->f("product_in_stock") . "<br /><br />";
                }
            }
            if (!empty($pav)) {
                if (stristr($pav, "gif") || stristr($pav, "jpg") || stristr($pav, "png")) {
                    // we think it's a pic then...
                    $html .= "<span style=\"font-weight:bold;\">" . $VM_LANG->_PHPSHOP_DELIVERY_TIME . ": </span><br /><br />";
                    $html .= "<img align=\"middle\" src=\"" . IMAGEURL . "availability/" . $pav . "\" border=\"0\" alt=\"$pav\" />";
                } else {
                    $html .= "<span style=\"font-weight:bold;\">" . $VM_LANG->_PHPSHOP_DELIVERY_TIME . ": </span>";
                    $html .= $pav;
                }
            }
            if (!empty($html)) {
                $html = $heading . $html;
            }
        }
        return $html;
    }

    /**
     * Modifies the product_publish field and toggles it from Y to N or N to Y
     * for product $d['product_id']
     * @deprecated 
     * @param int $d $d['task'] must be "publish" or "unpublish"
     * @return unknown
     */
    function product_publish(&$d) {
        global $db, $vmLogger;

        $this->handlePublishState($d);
        return;
    }

    function get_il($product_id) {
        global $db;

        $q = "SELECT 
            `l`.`igl_quantity` as `quantity`, 
            `o`.`igo_product_name` as `name`
        FROM `product_ingredients_lists` as `l`
        LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
        WHERE `l`.`product_id`=" . $product_id . "";
        $db->setQuery($q);
        $rows = $db->loadObjectList();

        $html = '<div id="i_l_' . $product_id . '">';

        foreach ($rows as $row) {
            $html .= $row->quantity . ' x ' . $row->name . '<br/>';
        }

        $html .= '<br/><a href="#i_l" onclick="edit_i_l(' . $product_id . ');">[Edit]</a></div>';
        /*
          echo '<pre>';
          print_r($rows);
          echo '</pre>';
         */
        return $html;
    }
    function show_product_list_mini($products) {
        global $database, $mosConfig_live_site,$my,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $mosConfig_lang, $mm_action_url, $sess, $VM_LANG, $cur_template, $sef,$mosConfig_show_compare_at_price;
        $show_with_canonical = true;
        if (sizeof($products) > 0) {
            $orderBy = ($_COOKIE['product_ordering']) ? 'product_real_price '.$_COOKIE['product_ordering'] : 'product_real_price asc';

            $query = "SELECT 
                `p`.`product_id`, 
                `p`.`product_name`, 
                `p`.`product_sku`, 
                `s`.`small_image_link_webp`, 
                `s`.`small_image_link_jpeg`, 
                `p`.`alias`, 
                `pp`.`product_price`,
                `pp`.`discount_for_customer`,
                `pm`.`discount` as promotion_discount,
                CASE 
                    WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                    ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                END AS `product_real_price`,
                `c`.`category_flypage`, 
                `c`.`category_id`, 
                `c`.`category_name`, 
                `c`.`alias` AS 'category_alias', 
                `fr`.`rating`, 
                `fr`.`review_count`,  
                `po`.`no_delivery`,  
                `po`.`no_delivery_order`,  
                `po`.`show_sale_overlay`,  
                `po`.`is_bestseller`,  
                `po`.`promo`, 
                `pm`.`end_promotion`, 
                `po`.`product_sold_out`,  
                `po`.`product_out_of_season`
            FROM `jos_vm_product` AS `p`
                LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
                LEFT JOIN (SELECT 
                                CASE 
                                    WHEN pmp.category_id > 0  THEN x.product_id
                                    ELSE pmp.product_id
                                END AS `product_id`,pmp.discount,pmp.end_promotion
                                FROM `jos_vm_products_promotion` as pmp 
                left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
                WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
                LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
            ";
            $query .= "
                LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
                LEFT JOIN `tbl_product_fake_reviews` AS `fr` ON `fr`.`product_id`=`p`.`product_id`
            WHERE `p`.`product_id` IN (" . implode(',', $products) . ") 
            GROUP BY `p`.`product_sku` ORDER BY  $orderBy";

            $database->setQuery($query);
            $products_obj = $database->loadObjectList();

            ?>

            <div class="products products_list_mini">
                <div class="row">
                    <?php
                    $items = [];
                    if ($products_obj) {
                        foreach ($products_obj as $p=>$product_obj) {
                            $product_old_price = number_format(round($product_obj->product_price, 2), 2, '.', '');
                            $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
                            if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                                $product_real_price = round($product_real_price - $product_real_price*$product_obj->discount_for_customer/100,2);
                            }

                            $product_rating = round($product_obj->rating, 1);
                            $savingPrice = $product_old_price - $product_real_price;

                            if ($show_with_canonical) {
                                $link = $sef->getCanonicalProduct($product_obj->alias, true);
                                $canonical = true;
                            } else {
                                $canonical = false;
                                $link = (!empty($GLOBALS['real_uri']) ? $GLOBALS['real_uri'] : '') . $product_obj->alias . '/';
                            }
                            $items[] = [
                                'data-item_name' => $product_obj->product_name,
                                'data-item_id' => $product_obj->product_sku,
                                'data-price' =>  number_format(round($product_real_price, 2), 2, '.', ''),
                                'data-discount' =>  number_format(round($savingPrice, 2), 2, '.', ''),
                                'data-index' =>  $p,
                                'data-item_category' => $product_obj->category_name,
                                'data-item_variant' => 'standard',
                                'data-quantity' => "1"
                            ]
                            ?>

                            <div class="col-6 col-sm-6 col-md-3 col-lg-3 wrapper" price_ordering="<?php echo $product_real_price; ?>" rating_ordering="<?php echo $product_rating; ?>">
                                <div class="inner">
                                    <a class="product-title" href="<?php echo $link;?>"
                                       onclick='event.preventDefault(); selectItemFromList(
                                               <?= json_encode($items[$p]); ?>,
                                               "<?= $sef->h1 ?? $sef->real_uri; ?>",
                                               "<?= $sef->real_uri ; ?>",
                                               "<?php echo $link; ?>"
                                           )'
                                    <?php echo (($canonical == true) ? '' : 'rel="nofollow"'); ?> >
                                        <?php
                                        if($product_obj->promotion_discount) {
                                            if(date("Y-m-d") == $product_obj->end_promotion || $product_obj->end_promotion == '0000-00-00') {
                                                echo '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
                                            } else {
                                                echo '<div class="new promotion_product">
                                            <span>Sale Ends In: </span> 
                                            <span class="promotion_countdown promotion_product_' . $product_obj->product_id . '" product_id="' . $product_obj->product_id . '" date_end="' . date("m/d/Y", strtotime($product_obj->end_promotion)) . '"></span>
                                            </div>';
                                            }
                                        }
                                        ?>
                                        <div class="product-image">
                                            <?php if ($product_obj->product_out_of_season) { ?>
                                                <div class="product_out_of_season"><img src="/templates/bloomex_adaptive/images/out_of_season.webp" alt="out of season" /></div>
                                            <?php }elseif ($product_obj->product_sold_out) { ?>
                                                <div class="product_sold_out"><img src="/templates/bloomex_adaptive/images/sold_out_en.webp" alt="sold out" /></div>
                                            <?php } ?>

                                            <?php if ($product_obj->no_delivery_order) {
                                                echo '<div class="product_no_delivery_order">'. $VM_LANG->_PHPSHOP_FREE_SHIPPING_PRODUCT .'</div>';
                                            } elseif($product_obj->show_sale_overlay) {
                                                echo '<div class="product_show_sale_overlay">'. $VM_LANG->_PHPSHOP_SHOW_SALE_OVERAL .'</div>';
                                            } elseif($product_obj->is_bestseller) {
                                                echo '<div class="product_is_bestseller">'. $VM_LANG->_PHPSHOP_BEST_SELLER .'</div>';
                                            } ?>

                                            <div class="product_image_loader"></div>

                                            <img style="display: none;" class="product_image_real" src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product_obj->small_image_link_jpeg : $product_obj->small_image_link_webp); ?>" alt="name: <?php echo $product_obj->product_name; ?>">
                                        </div>
                                        <span class="product-title"><?php echo $product_obj->product_name; ?></span>
                                    </a>
                                    <?php
                                    if ($product_old_price != $product_real_price && $mosConfig_show_compare_at_price) {
                                        ?>
                                        <div style="font-size: 15px">Compare at: <span class="old_price"><s>$<?php echo $product_old_price; ?></s></span></div>
                                        <?php
                                    }
                                    ?>
                                    <?php if ($product_obj->product_real_price=='0.00' && $product_obj->no_delivery==0 && $product_obj->promo=='0'){ ?>
                                        <a style="display: block;text-align: center;margin: 20px auto;" href="tel:1800905147"><div class="add">Call For Pricing</div></a>
                                    <?php }else{ ?>
                                        <?php if ($product_obj->product_out_of_season) { ?>
                                            <div class="add" product_id="<?php echo $product_obj->product_id; ?>">Out Of Season</div>
                                        <?php }elseif ($product_obj->product_sold_out) { ?>
                                            <div class="add" product_id="<?php echo $product_obj->product_id; ?>">Sold Out</div>
                                        <?php } else { ?>
                                            <div style="font-size: 14px;color: #A40001;font-weight: bold;">Bloomex Price: <span class="price">$<?php echo $product_real_price; ?></span></div>
                                            <div class="form-add-cart" id="div_<?php echo $product_obj->product_id; ?>">
                                                <form action="/index.php" method="post" name="addtocart" id="formAddToCart_<?php echo $product_obj->product_id; ?>">
                                                    <input name="quantity_<?php echo $product_obj->product_id; ?>" class="inputbox" type="hidden" size="3" value="1">


                                                    <div class="add add_and_reload" product_id="<?php echo $product_obj->product_id; ?>">Add to Cart</div>

                                                    <input type="hidden" name="category_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_id; ?>">
                                                    <input type="hidden" name="product_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_id; ?>">
                                                    <input type="hidden" name="price_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_real_price; ?>">
                                                    <input type="hidden" name="sku_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_sku; ?>">
                                                    <input type="hidden" name="name_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_name; ?>">
                                                    <input type="hidden" name="discount_<?php echo $product_obj->product_id; ?>" value="<?php echo $savingPrice; ?>">
                                                    <input type="hidden" name="category_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_name; ?>">

                                                </form>
                                            </div>
                                        <?php }} ?>
                                </div>
                            </div>
                            <?php
                        }
                    }

                    ?>
                    <script type='text/javascript'>
                        pushGoogleAnalytics(
                            'view_item_list',
                            <?= json_encode($items); ?>,
                            null,
                            null,
                            "AUD",
                            "<?= $sef->h1 ?? $sef->real_uri; ?>",
                            "<?= $sef->real_uri ; ?>"
                        );

                    </script>
                </div>
            </div>
            <?php
        }
    }
    function show_product_list($products, $category_id = false,$start = 0) {
        $limit = isMobileDevice() ? 12 : 24;
        global $database, $mosConfig_live_site,$my,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $mosConfig_lang, $mm_action_url, $sess, $VM_LANG, $cur_template, $sef,$mosConfig_show_compare_at_price;
        $show_with_canonical = true;
        if (sizeof($products) > 0) {
            $orderBy = ($_COOKIE['product_ordering']) ? 'product_real_price '.$_COOKIE['product_ordering'] : 'product_real_price asc';

            $query = "SELECT 
                `p`.`product_id`, 
                `p`.`product_name`, 
                `p`.`product_sku`, 
                `s`.`medium_image_link_jpeg`, 
                `s`.`medium_image_link_webp`, 
                `p`.`alias`, 
                `pp`.`product_price`,
                `pp`.`discount_for_customer`,
                `pm`.`discount` as promotion_discount,
                CASE 
                    WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                    ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                END AS `product_real_price`,
                `c`.`category_flypage`, 
                `c`.`category_id`, 
                `c`.`category_name`, 
                `c`.`alias` AS 'category_alias', 
                `fr`.`rating`, 
                `fr`.`review_count`,  
                `po`.`no_delivery`,  
                `po`.`no_delivery_order`,  
                `po`.`show_sale_overlay`,  
                `po`.`is_bestseller`,  
                `po`.`promo`, 
                `pm`.`end_promotion`, 
                `po`.`product_sold_out`,  
                `po`.`product_out_of_season`
            FROM `jos_vm_product` AS `p`
                LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
                LEFT JOIN (SELECT 
                                CASE 
                                    WHEN pmp.category_id > 0  THEN x.product_id
                                    ELSE pmp.product_id
                                END AS `product_id`,pmp.discount,pmp.end_promotion
                                FROM `jos_vm_products_promotion` as pmp 
                left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
                WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
                LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
            ";
            if ($category_id) {
                $query .= " AND `cx`.`category_id`=" . $category_id . " ";
            }
            $query .= "
                LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
                LEFT JOIN `tbl_product_fake_reviews` AS `fr` ON `fr`.`product_id`=`p`.`product_id`
            WHERE `p`.`product_id` IN (" . implode(',', $products) . ") 
            GROUP BY `p`.`product_sku`";
            $order = " ORDER BY  $orderBy";
            if (isset($_REQUEST['option']) &&  $_REQUEST['option'] == 'com_best_seller') {
                $order = " ORDER BY FIELD(p.product_id, " . implode(',', $products) . ")";
            }

            if (($_REQUEST['option'] == 'com_virtuemart' AND ( isset($_REQUEST['page']) AND $_REQUEST['page'] == 'shop.browse')) OR $ajax) {
                $database->setQuery($query.$order,$start,$limit);
            }else{
                $database->setQuery($query.$order);
            }

            $products_obj = $database->loadObjectList();
            if ($products_obj) {
                if(!$ajax){
            ?>
            <div class="container products">
                <div class="row">          
                    <?php
                    }
                        $items = [];
                        foreach ($products_obj as $p=>$product_obj) {
                            $product_old_price = number_format(round($product_obj->product_price, 2), 2, '.', '');
                            $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
                            if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                                $product_real_price = round($product_real_price - $product_real_price*$product_obj->discount_for_customer/100,2);
                            }

                            $product_rating = round($product_obj->rating, 1);
                            $savingPrice = $product_old_price - $product_real_price;
                            $sub_3 = false;
                            $sub_9 = false;
                            $sub_12 = false;
                            /*
                              $sub_3 = round($product_obj->sub_3, 2);
                              $sub_9 = round($product_obj->sub_9, 2);
                              $sub_12 = round($product_obj->sub_12, 2);
                             */

                            Switch ($mosConfig_lang) {
                                case 'french':
                                    $sBtnImage = "button_fr.png";

                                    $product_obj->product_name = !empty($product_obj->fr_product_name) ? $product_obj->fr_product_name : $product_obj->product_name;
                                    $product_obj->alias = !empty($product_obj->fr_alias) ? $product_obj->fr_alias : $product_obj->alias;
                                    $product_obj->category_alias = !empty($product_obj->fr_category_alias) ? $product_obj->fr_category_alias : $product_obj->category_alias;

                                    break;

                                case 'english':
                                default:
                                    /*
                                      for christmas button_christmas.png
                                      not for christmas button.png
                                     */

                                    $sBtnImage = "button.png";
                                    break;
                            }
                            if ($show_with_canonical) {
                                $link = $sef->getCanonicalProduct($product_obj->alias, true);
                                $canonical = true;
                            } else {
                                $canonical = false;
                                $link = (!empty($GLOBALS['real_uri']) ? $GLOBALS['real_uri'] : '') . $product_obj->alias . '/';
                            }

                            $items[] = [
                                   'data-item_name' => $product_obj->product_name,
                                   'data-item_id' => $product_obj->product_sku,
                                   'data-price' =>  number_format(round($product_real_price, 2), 2, '.', ''),
                                   'data-discount' =>  number_format(round($savingPrice, 2), 2, '.', ''),
                                   'data-index' =>  $p + $start,
                                   'data-item_category' => $product_obj->category_name,
                                   'data-item_variant' => 'standard',
                                   'data-quantity' => "1"
                                ]

                            ?>


                            <div class="col-6 col-sm-6 col-md-3 col-lg-3 wrapper" price_ordering="<?php echo $product_real_price; ?>" rating_ordering="<?php echo $product_rating; ?>">
                                <div class="inner">
                                    <a class="product-title" href="<?php echo $link;?>"
                                       onclick='event.preventDefault(); selectItemFromList(
                                        <?= json_encode($items[$p]); ?>,
                                        "<?= $sef->h1 ?? $sef->real_uri; ?>",
                                        "<?= $sef->real_uri ; ?>",
                                        "<?php echo $link; ?>"
                                    )'
                                    <?php echo (($canonical == true) ? '' : 'rel="nofollow"'); ?> >
                                    <?php
                                    if($product_obj->promotion_discount) {
                                        if(date("Y-m-d") == $product_obj->end_promotion || $product_obj->end_promotion == '0000-00-00') {
                                            echo '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
                                        } else {
                                            echo '<div class="new promotion_product">
                                            <span>Sale Ends In: </span> 
                                            <span class="promotion_countdown promotion_product_' . $product_obj->product_id . '" product_id="' . $product_obj->product_id . '" date_end="' . date("m/d/Y", strtotime($product_obj->end_promotion)) . '"></span>
                                            </div>';
                                        }
                                    }
                                    ?>
                                    <div class="product-image">
                                        <?php if ($product_obj->product_out_of_season) { ?>
                                             <div class="product_out_of_season"><img src="/templates/bloomex_adaptive/images/out_of_season.webp" alt="out of season" /></div>
                                        <?php }elseif ($product_obj->product_sold_out) { ?>
                                            <div class="product_sold_out"><img src="/templates/bloomex_adaptive/images/sold_out_en.webp" alt="sold out" /></div>
                                        <?php } ?>

                                        <?php if ($product_obj->no_delivery_order) {
                                            echo '<div class="product_no_delivery_order">'. $VM_LANG->_PHPSHOP_FREE_SHIPPING_PRODUCT .'</div>';
                                       }
                                       if($product_obj->show_sale_overlay) {
                                            echo '<div class="product_show_sale_overlay">'. $VM_LANG->_PHPSHOP_SHOW_SALE_OVERAL .'</div>';
                                       }
                                       if($product_obj->is_bestseller) {
                                            echo '<div class="product_is_bestseller">'. $VM_LANG->_PHPSHOP_BEST_SELLER .'</div>';
                                       } ?>
                                        <div class="product_image_placeholder" style="background: url('<?php echo $mosConfig_live_site; ?>/images/stories/noimage-225x262.png');">
                                            <img
                                                    src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product_obj->medium_image_link_jpeg : $product_obj->medium_image_link_webp); ?>"
                                                    loading="lazy"
                                                    decoding="async"
                                                    alt="name: <?php echo $product_obj->product_name; ?>"
                                                    class="product_image_real"
                                            >
                                        </div>
                                    </div>
                                    <span class="product-title"><?php echo $product_obj->product_name; ?></span>
                                    </a>
                                    <?php
                                    if ($product_old_price != $product_real_price && $mosConfig_show_compare_at_price) {
                                        ?>
                                        <div style="font-size: 15px">Compare at: <span class="old_price"><s>$<?php echo $product_old_price; ?></s></span></div>
                                        <?php
                                    }
                                    ?>
                                    <?php if ($product_obj->product_real_price=='0.00' && $product_obj->no_delivery==0 && $product_obj->promo=='0'){ ?>
                                    <a style="display: block;text-align: center;margin: 20px auto;" href="tel:1800905147"><div class="add">Call For Pricing</div></a>
                                <?php }else{ ?>
                            <?php if ($product_obj->product_out_of_season) { ?>
                                <div class="add" product_id="<?php echo $product_obj->product_id; ?>">Out Of Season</div>
                            <?php }elseif ($product_obj->product_sold_out) { ?>
                                <div class="add" product_id="<?php echo $product_obj->product_id; ?>">Sold Out</div>
                            <?php } else { ?>
                                        <div style="font-size: 14px;color: #A40001;font-weight: bold;">Bloomex Price: <span class="price">$<?php echo $product_real_price; ?></span></div>
                                    <div class="form-add-cart" id="div_<?php echo $product_obj->product_id; ?>">
                                        <form action="/index.php" method="post" name="addtocart" id="formAddToCart_<?php echo $product_obj->product_id; ?>">
                                            <input name="quantity_<?php echo $product_obj->product_id; ?>" class="inputbox" type="hidden" size="3" value="1">


                                            <div class="add" product_id="<?php echo $product_obj->product_id; ?>">Add to Cart</div>

                                            <input type="hidden" name="category_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_id; ?>">
                                            <input type="hidden" name="product_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_id; ?>">
                                            <input type="hidden" name="price_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_real_price; ?>">
                                            <input type="hidden" name="sku_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_sku; ?>">
                                            <input type="hidden" name="name_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_name; ?>">
                                            <input type="hidden" name="discount_<?php echo $product_obj->product_id; ?>" value="<?php echo $savingPrice; ?>">
                                            <input type="hidden" name="category_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_name; ?>">

                                        </form>
                                    </div>
                            <?php }} ?>
                                </div>
                            </div>
                            <?php
                        }

            if(!$ajax){
                    ?>
                    <script type='text/javascript'>
                        pushGoogleAnalytics(
                            'view_item_list',
                            <?= json_encode($items); ?>,
                            null,
                            null,
                            "AUD",
                            "<?= $sef->h1 ?? $sef->real_uri; ?>",
                            "<?= $sef->real_uri ; ?>"
                        );

                    </script>
                </div>
            </div>
            <?php
            }
        }
        }
    }
    public function showExtraProductListNew(array $products, array $categories)
    {
        global $database, $cur_template;

        $cur_template = 'bloomex_adaptive';

        $query = "SELECT 
            `p`.`product_id`, 
            `p`.`product_name`, 
            `p`.`product_s_desc`, 
            `p`.`product_sku`, 
            `s`.`small_image_link_webp`, 
            `s`.`small_image_link_jpeg`, 
            `p`.`alias`, 
            `pp`.`product_price`,
                            `pm`.`discount` as promotion_discount,
                CASE 
                    WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                    ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                END AS `product_real_price`,
            `c`.`category_flypage`, 
            `c`.`category_id`, 
            `c`.`category_name`, 
                    `c`.`alias` AS 'category_alias', 
            CASE
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['vase'] . "%' THEN 'Vase'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['cart'] . "%' THEN 'Full Size Greeting'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['treat'] . "%' THEN 'Treats'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['balloon'] . "%' THEN 'Balloon'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['wine'] . "%' THEN 'Wine'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['teddy'] . "%' THEN 'Teddy'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['special'] . "%' THEN 'Special Touch'
                WHEN `c`.`alias` LIKE '%" . self::EXTRA_TOUCH_ALIAS['diy_bulk'] . "%' THEN 'DIY Bulk Accessories'
            END AS `type`
            FROM `jos_vm_product` AS `p`
            LEFT JOIN (SELECT 
                            CASE 
                                WHEN pmp.category_id > 0  THEN x.product_id
                                ELSE pmp.product_id
                            END AS `product_id`,pmp.discount,pmp.end_promotion
                            FROM `jos_vm_products_promotion` as pmp 
            left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
            WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
            LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
            WHERE `p`.`product_id` IN (" . implode(',', $products) . ") 
            AND 
                `c`.`category_id` IN (" . implode(',', $categories) . ")
            GROUP BY
                `p`.`product_sku`
            ORDER BY 
                product_real_price ASC";

        $database->setQuery($query);
        $products_obj = $database->loadObjectList();

        if ($products_obj) {
            shuffle($products_obj);
        }
        $extraProducts = null;
        if ($products_obj) {
            $extraProducts = $this->getExtraProductItems($products_obj);
        }

        ob_start();
        if($extraProducts) {
            $this->showExtraProductItems($extraProducts);
        } else {
            $this->printProducts($products_obj);
        }

        return ob_get_clean();
    }
    private function getExtraProductItems($products): array
    {
        $extraProducts = [];

        foreach ($products as $product) {

            if($product->small_image_link_webp === '') {
                continue;
            }
            if ($product->type === 'Vase' && (!isset($extraProducts['vase']) || count($extraProducts['vase']) < 5)) {
                $extraProducts['vase'][] = $product;
            } else if ($product->type === 'Treats' && (!isset($extraProducts['treat']) || count($extraProducts['treat']) < 5)) {
                $extraProducts['treat'][] = $product;
            } else if ($product->type === 'Balloon' && (!isset($extraProducts['balloon']) || count($extraProducts['balloon']) < 5)) {
                $extraProducts['balloon'][] = $product;
            } else if ($product->type === 'Wine' && (!isset($extraProducts['wine']) || count($extraProducts['wine']) < 5)) {
                $extraProducts['wine'][] = $product;
            } else if ($product->type === 'Teddy' && (!isset($extraProducts['teddy']) || count($extraProducts['teddy']) < 5)) {
                $extraProducts['teddy'][] = $product;
            } else if ($product->type === 'Full Size Greeting' && (!isset($extraProducts['fullSize']) || count($extraProducts['fullSize'])< 5)) {
                $extraProducts['fullSize'][] = $product;
            } else if ($product->type === 'Special Touch' && (!isset($extraProducts['special']) || count($extraProducts['special']) < 5)) {
                $extraProducts['special'][] = $product;
            } else if ($product->type === 'DIY Bulk Accessories' && (!isset($extraProducts['diy_bulk']) ||  count($extraProducts['diy_bulk']) < 5)) {
                $extraProducts['diy_bulk'][] = $product;
            }
        }

        return $extraProducts;
    }
    private function showExtraProductItems($extraProducts)
    {
        global $VM_LANG;

        if($extraProducts['fullSize']) {
            $this->printExtraProducts($extraProducts['fullSize'], $VM_LANG->_FULL_SIZE_GREETING_CARD, self::EXTRA_TOUCH_IMAGES['card']);
        }
        if($extraProducts['vase']) {
            $this->printExtraProducts($extraProducts['vase'], $VM_LANG->_GLASS_VASE, self::EXTRA_TOUCH_IMAGES['vase']);
        }
        if($extraProducts['teddy']) {
            $this->printExtraProducts($extraProducts['teddy'], $VM_LANG->_CUDDLY_TEDDY_BEAR, self::EXTRA_TOUCH_IMAGES['teddy']);
        }
        if($extraProducts['treat']) {
            $this->printExtraProducts($extraProducts['treat'], $VM_LANG->_GOURMENT_TREATS, self::EXTRA_TOUCH_IMAGES['treat']);
        }
        if($extraProducts['balloon']) {
            $this->printExtraProducts($extraProducts['balloon'], $VM_LANG->_COLOURFUL_BALLOON,  self::EXTRA_TOUCH_IMAGES['balloon']);
        }
        if($extraProducts['wine']) {
            $this->printExtraProducts($extraProducts['wine'], $VM_LANG->_PREMIUM_WINE_AND_BEER, self::EXTRA_TOUCH_IMAGES['wine']);
        }
        if($extraProducts['special']) {
            $this->printExtraProducts($extraProducts['special'], $VM_LANG->_SPECIAL_TOUCH, self::EXTRA_TOUCH_IMAGES['special']);
        }
        if($extraProducts['diy_bulk']) {
            $this->printExtraProducts($extraProducts['diy_bulk'], $VM_LANG->_DIY_BULK_ACCESSOR, self::EXTRA_TOUCH_IMAGES['diy_bulk']);
        }
    }

    private function printExtraProducts(array $products, string $title, string $image)
    {
        global $mosConfig_lang, $mosConfig_live_site,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $VM_LANG;

        $extraProducts = sprintf('<div class="extra-touch-wrapper" onclick="toggleElement(`%s`)"><span title="%s">

            <img class="extra-touch-image" src="/images/extra_products/%s" alt="%s"><span class="extra-touch-title">%s <i  id="%s head" class="arrowRight" aria-hidden="true"></i></span></span>
            </div><div  id="%s" class="row extra_products_new extra-products-modern" style="display:none">',

            $title,
            $VM_LANG->_CLICK_TO_VIEW,
            $image,
            $VM_LANG->_CLICK_TO_VIEW,
            $title,
            $title,
            $title
        );
        echo $extraProducts;

        foreach ($products as $k => $product_obj) {
            $product_old_price = number_format(round($product_obj->product_price, 2), 2, '.', '');
            $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
            $savingPrice = $product_old_price - $product_real_price;
            ?>
            <div class="col-3 col-sm-3 col-md-2 col-lg-2 wrapper extraProductBox tooltripHoverBox " price_ordering="<?php echo $product_real_price; ?>" rating_ordering="<?php echo $product_rating; ?>" onclick='this.querySelector(".extraProductCheckbox").click();'>
                <?php echo ($product_obj->product_s_desc!='')?'<div class="tooltripDiv"><span class="visible-xs closeTooltrip">X</span>'.strip_tags($product_obj->product_s_desc).'</div>':'';?>
                <div class="inner">
                    <a class="product-title"><span><?php echo $product_obj->product_name; ?></span></a>
                    <?php
                    if($product_obj->promotion_discount) {
                        if(date("Y-m-d") == $product_obj->end_promotion || $product_obj->end_promotion == '0000-00-00') {
                            echo '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
                        } else {
                            echo '<div class="new promotion_product" style="font-size: 13px">
                                            <span>Sale Ends In: </span> 
                                            <span class="promotion_countdown promotion_product_' . $product_obj->product_id . '" product_id="' . $product_obj->product_id . '" date_end="' . date("m/d/Y", strtotime($product_obj->end_promotion)) . '"></span>
                                        </div>';
                        }
                    }
                    ?>
                    <div class="product-image">

                        <div class="product_image_loader"></div>
                        <img style="display: none;" class="product_image_real" src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product_obj->small_image_link_jpeg : $product_obj->small_image_link_webp); ?>" alt="name: <?php echo $product_obj->product_name; ?>">

                    </div>
                    <span>
                                <span class="price">$<span><?php echo $product_real_price; ?></span></span>

                            </span>
                    <div class="form-add-cart-extra" id="div_<?php echo $product_obj->product_id; ?>">
                        <form action="<?php echo $mosConfig_live_site; ?>/index.php" method="post" name="addtocart" id="formAddToCart_<?php echo $product_obj->product_id; ?>">
                            <input name="quantity_<?php echo $product_obj->product_id; ?>" class="inputbox" type="hidden" size="3" value="1">

                            <div >
                                <div class="container">
                                    <input type="checkbox" name="extra_products" class="extraProductCheckbox" onclick="addProductToItems(<?php echo $product_obj->product_id; ?>);" value="<?php echo $product_obj->product_id; ?>">
                                    <span class="checkmark"></span>
                                </div>
                            </div>

                            <input type="hidden" name="category_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_id; ?>">
                            <input type="hidden" name="product_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_id; ?>">
                            <input type="hidden" name="price_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_real_price; ?>">
                            <input type="hidden" name="sku_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_sku; ?>">
                            <input type="hidden" name="name_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_name; ?>">
                            <input type="hidden" name="discount_<?php echo $product_obj->product_id; ?>" value="<?php echo $savingPrice; ?>">
                            <input type="hidden" name="category_<?php echo $product_obj->product_id; ?>" value="<?= $product_obj->category_name ?>">

                        </form>
                    </div>
                </div>
            </div>

            <?php
        }
        echo '</div>';
    }

    private function printProducts(array $products)
    {
        global $mosConfig_lang, $mosConfig_live_site,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $VM_LANG;

        echo '<div class="row extra_products_new extra-products-modern">';
        foreach ($products as $k => $product_obj) {
            if ($k >= self::EXTRA_TOUCH_PRODUCTS_COUNT) {
                break;
            }
            $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');

            ?>
            <div class="col-3 col-sm-3 col-md-2 col-lg-2 wrapper extraProductBox tooltripHoverBox <?php echo ($k > 7) ? 'hidden-xs' : ''; ?>"
                 price_ordering="<?php echo $product_real_price; ?>"
                 onclick='this.querySelector(".extraProductCheckbox").click();'>

                <?php echo ($product_obj->product_s_desc != '') ? '<div class="tooltripDiv"><span class="visible-xs closeTooltrip">X</span>' . strip_tags($product_obj->product_s_desc) . '</div>' : ''; ?>

                <div class="inner">
                    <a class="product-title-extra"><span><?php echo $product_obj->product_name; ?></span></a>
                    <span>
                        <span class="price" style="color: #706767"><span><?php echo '$' . number_format($product_real_price, 2, '.', ''); ?></span></span>

                    </span>
                    <div class="product-image" title='<?= strip_tags($product_obj->product_s_desc) ?>'>

                        <div class="product_image_loader"></div>
                        <img  style="display: none;" class="product_image_real"
                             src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product_obj->small_image_link_jpeg : $product_obj->small_image_link_webp); ?>"
                             alt="<?php echo $product_obj->product_name; ?>">
                    </div>
                    <div class="form-add-cart-extra" id="div_<?php echo $product_obj->product_id; ?>">
                        <form action="<?php echo $mosConfig_live_site; ?>/index.php" method="post"
                              name="addtocart" id="formAddToCart_<?php echo $product_obj->product_id; ?>">
                            <input name="quantity_<?php echo $product_obj->product_id; ?>" class="inputbox"
                                   type="hidden" size="3" value="1">

                            <div>
                                <div class="container">
                                    <input type="checkbox" name="extra_products" class="extraProductCheckbox"
                                           value="<?php echo $product_obj->product_id; ?>">
                                    <span class="checkmark"></span>
                                </div>
                            </div>

                            <input type="hidden" name="category_id_<?php echo $product_obj->product_id; ?>"
                                   value="<?php echo $product_obj->category_id; ?>">
                            <input type="hidden" name="product_id_<?php echo $product_obj->product_id; ?>"
                                   value="<?php echo $product_obj->product_id; ?>">
                            <input type="hidden" name="price_<?php echo $product_obj->product_id; ?>"
                                   value="<?php echo $product_real_price; ?>">
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    function show_extra_product_list_new($products) {
        global $database, $mosConfig_live_site, $mosConfig_lang, $mm_action_url, $sess, $VM_LANG, $cur_template, $sef;

        $cur_template = 'bloomex_adaptive';

        $query = "SELECT 
                    `p`.`product_id`, 
                    `p`.`product_name`, 
                    `p`.`product_sku`, 
                     `p`.`product_s_desc`, 
                    `p`.`product_thumb_image`, 
                    `p`.`alias`, 
                    `pp`.`product_price`,
                    `pp`.`discount_for_customer`,
                    `pm`.`discount` as promotion_discount,
                    CASE 
                        WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                        ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                    END AS `product_real_price`,
                    `c`.`category_flypage`, 
                    `c`.`category_id`, 
                    `c`.`category_name`, 
                    `c`.`alias` AS 'category_alias', 
                    `fr`.`rating`, 
                    `pm`.`end_promotion`,
                    `fr`.`review_count`
                FROM `jos_vm_product` AS `p`
                    LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                    LEFT JOIN (SELECT 
                                    CASE 
                                        WHEN pmp.category_id > 0  THEN x.product_id
                                        ELSE pmp.product_id
                                    END AS `product_id`,pmp.discount,pmp.end_promotion
                                    FROM `jos_vm_products_promotion` as pmp 
                    left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
                    WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
                    LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
                    LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
                    LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
                    LEFT JOIN `tbl_product_fake_reviews` AS `fr` ON `fr`.`product_id`=`p`.`product_id`
                    LEFT JOIN `jos_vm_tax_rate` AS `tr` on `tr`.`tax_rate_id`=`p`.`product_tax_id`
                WHERE `p`.`product_id` IN (" . implode(',', $products) . ") 
                GROUP BY `p`.`product_sku` ORDER BY product_real_price ASC";
        //AND cx.`product_list` !='' 

        $database->setQuery($query);
        $products_obj = $database->loadObjectList();

        //        if ($products_obj) {
        //            shuffle($products_obj);
        //        }
        /*
          echo '<pre>';
          print_r($products_obj);
          echo '</pre>';
         */

        ob_start();
        ?>

        <div class="row extra_products_new">
            <?php
            if ($products_obj) {
                foreach ($products_obj as $product_obj) {

                    $product_old_price = number_format(round($product_obj->product_price, 2), 2, '.', '');
                    $product_real_price = number_format(round($product_obj->product_real_price, 2), 2, '.', '');
                    if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
                        $product_real_price = round($product_real_price - $product_real_price*$product_obj->discount_for_customer/100,2);
                    }
                    $product_rating = round($product_obj->rating, 1);
                    $savingPrice = $product_old_price - $product_real_price;
                    Switch ($mosConfig_lang) {
                        case 'french':
                            $sBtnImage = "button_fr.png";

                            $product_obj->product_name = !empty($product_obj->fr_product_name) ? $product_obj->fr_product_name : $product_obj->product_name;
                            $product_obj->alias = !empty($product_obj->fr_alias) ? $product_obj->fr_alias : $product_obj->alias;
                            $product_obj->category_alias = !empty($product_obj->fr_category_alias) ? $product_obj->fr_category_alias : $product_obj->category_alias;
                            break;

                        case 'english':
                        default:
                            /*
                              for christmas button_christmas.png
                              not for christmas button.png
                             */

                            $sBtnImage = "button.png";
                            break;
                    }

                    $url = '?page=shop.product_details&category_id=' . $product_obj->category_id . '&flypage=' . $this->get_flypage($product_obj->product_id) . '&product_id=' . $product_obj->product_id;

                    //$link = $mosConfig_live_site . '/' . $product_obj->category_alias . '/' . $product_obj->alias;
                    $link = $sef->getCanonicalProduct($product_obj->alias, true);
                    ?>
                    <div class="col-3 col-sm-3 col-md-2 col-lg-2 wrapper extraProductBox tooltripHoverBox " price_ordering="<?php echo $product_real_price; ?>" rating_ordering="<?php echo $product_rating; ?>" onclick='this.querySelector(".extraProductCheckbox").click();'>
                        <?php echo ($product_obj->product_s_desc!='')?'<div class="tooltripDiv"><span class="visible-xs closeTooltrip">X</span>'.strip_tags($product_obj->product_s_desc).'</div>':'';?>
                        <div class="inner">
                            <a class="product-title"><span><?php echo $product_obj->product_name; ?></span></a>
                            <?php
                            if($product_obj->promotion_discount) {
                                if(date("Y-m-d") == $product_obj->end_promotion || $product_obj->end_promotion == '0000-00-00') {
                                    echo '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
                                } else {
                                    echo '<div class="new promotion_product" style="font-size: 13px">
                                            <span>Sale Ends In: </span> 
                                            <span class="promotion_countdown promotion_product_' . $product_obj->product_id . '" product_id="' . $product_obj->product_id . '" date_end="' . date("m/d/Y", strtotime($product_obj->end_promotion)) . '"></span>
                                        </div>';
                                }
                            }
                            ?>
                            <div class="product-image">

                                    <div class="product_image_loader"></div>
                                    <img style="display: none;" class="product_image_real" src="/components/com_virtuemart/shop_image/product/<?php echo $product_obj->product_thumb_image; ?>" alt="name: <?php echo $product_obj->product_name; ?>">

                            </div>
                            <span>
                                <span class="price">$<span><?php echo $product_real_price; ?></span></span>

                            </span>
                            <div class="form-add-cart-extra" id="div_<?php echo $product_obj->product_id; ?>">
                                <form action="<?php echo $mosConfig_live_site; ?>/index.php" method="post" name="addtocart" id="formAddToCart_<?php echo $product_obj->product_id; ?>">
                                    <input name="quantity_<?php echo $product_obj->product_id; ?>" class="inputbox" type="hidden" size="3" value="1">


                                    <?php
                                    if ($product_real_price && $product_real_price=='0.00') {
                                        ?>
                                        <a href='tel:1800905147'><div class=' call_for_pricing'><?php echo $VM_LANG->_PHPSHOP_PRODUCT_CALL;?></div></a>
                                        <?php
                                    }
                                    ?>

                                    <div >
                                        <div class="container">
                                            <input type="checkbox" name="extra_products" class="extraProductCheckbox" onclick="addProductToItems(<?php echo $product_obj->product_id; ?>);" value="<?php echo $product_obj->product_id; ?>">
                                            <span class="checkmark"></span>
                                        </div>
                                    </div>

                                    <input type="hidden" name="category_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->category_id; ?>">
                                    <input type="hidden" name="product_id_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_id; ?>">
                                    <input type="hidden" name="price_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_real_price; ?>">
                                    <input type="hidden" name="sku_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_sku; ?>">
                                    <input type="hidden" name="name_<?php echo $product_obj->product_id; ?>" value="<?php echo $product_obj->product_name; ?>">
                                    <input type="hidden" name="discount_<?php echo $product_obj->product_id; ?>" value="<?php echo $savingPrice; ?>">
                                    <input type="hidden" name="category_<?php echo $product_obj->product_id; ?>" value="<?= $product_obj->category_name ?>">

                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

}

// ENd of CLASS ps_product
?>
