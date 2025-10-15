<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
 *
 * @version $Id: ps_product_category.php,v 1.14.2.5 2006/04/05 18:16:53 soeren_nb Exp $
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
 * The class to manage and show product categories
 *
 */
class ps_product_category extends vmAbstractObject {
    var $classname = "ps_product_category";

    function __construct() {
        if(isset($_REQUEST['category_id']) AND isset($_REQUEST['category_publish'])){

            $db = new ps_DB;
            $category_id = (int)$_REQUEST['category_id'];
            $searchable = isset($_REQUEST['searchable']) ? $_REQUEST['searchable'] : '';

            $q  = "SELECT * FROM #__{vm}_category_unsearchable where category_id='$category_id'";
            $db->setQuery($q);   $db->query();
            if ($db->next_record()) {
                if(!$searchable){
                    $q  = "DELETE FROM #__{vm}_category_unsearchable WHERE category_id='$category_id'";
                    $db->setQuery($q);   $db->query();
                }
            }else{
                if($searchable){
                    $q = "INSERT INTO #__{vm}_category_unsearchable (category_id)  VALUES ($category_id)";
                    $db->setQuery($q);   $db->query();
                }

            }

        }


    }

    /**
     * Validates all product category fields and uploaded image files
     * on category creation.
     *
     * @param array $d The input vars
     * @return boolean True when validation successful, false when not
     */
    function validate_add(&$d) {
        global $vmLogger;
        $valid = true;
        if (!$d["category_name"]) {
            $vmLogger->err( "You must enter a name for the category.");
            $valid = False;
        }

        /** Image Upload Validation **/

        // do we have an image URL or an image File Upload?
        if (!empty( $d['category_thumb_image_url'] )) {
            // Image URL
            if (substr( $d['category_thumb_image_url'], 0, 4) != "http") {
                $vmLogger->err( "Image URL must begin with http." );
                $valid =  false;
            }

            $d["category_thumb_image"] = $d['category_thumb_image_url'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_thumb_image", "category")) {
                $valid = false;
            }
        }

        if (!empty( $d['category_thumb_image_url_fr'] )) {
            // Image URL
            if (substr( $d['category_thumb_image_url_fr'], 0, 4) != "http") {
                $vmLogger->err( "Image URL must begin with http." );
                $valid =  false;
            }

            $d["category_thumb_image_fr"] = $d['category_thumb_image_url_fr'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_thumb_image_fr", "category")) {
                $valid = false;
            }
        }

        if (!empty( $d['category_full_image_url'] )) {
            // Image URL
            if (substr( $d['category_full_image_url'], 0, 4) != "http") {
                $vmLogger->err( "Image URL must begin with http." );
                return false;
            }
            $d["category_full_image"] = $d['category_full_image_url'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_full_image", "category")) {
                $valid = false;
            }
        }

        if (!empty( $d['category_full_image_url_fr'] )) {
            // Image URL
            if (substr( $d['category_full_image_url_fr'], 0, 4) != "http") {
                $vmLogger->err( "Image URL must begin with http." );
                return false;
            }
            $d["category_full_image_fr"] = $d['category_full_image_url_fr'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_full_image_fr", "category")) {
                $valid = false;
            }
        }
        
        $checkAlias = $this->checkAlias($d['alias'], $d['category_id']);
        
        if ($checkAlias->result == false) {
            $err = 'Alias is busy, check:<br/>';
            
            foreach ($checkAlias->categories AS $category_obj) {
                $err .= '<a href="/administrator/index2.php?option=com_virtuemart&page=product.product_category_form&category_id='.$category_obj->category_id.'" target="_blank">'.$category_obj->category_name.'</a><br/>';
            }

            $vmLogger->err($err);
            $valid = false;
        }

        return $valid;

    }

    /**
     * Validates all product category fields and uploaded image files
     * on category update.
     *
     * @param array $d The input vars
     * @return boolean True when validation successful, false when not
     */
    function validate_update(&$d) {
        global $vmLogger;
        $valid = true;

        if (!$d["category_name"]) {
            $vmLogger->err( "You must enter a name for the category." );
            $valid = False;
        }
        elseif ($d["category_id"] == $d["category_parent_id"]) {
            $vmLogger->err( "Category parent cannot be same category." );
            $valid = False;
        }
        $db =new  ps_DB;

        $q = "SELECT category_thumb_image,category_full_image FROM #__{vm}_category_data_fr WHERE category_id='". $d["category_id"] . "'";
        $db->query( $q );
        $db->next_record();
        $french_database = array();
        $french_database[] = $db->f("category_thumb_image");
        $french_database[] = $db->f("category_full_image");

        $q = "SELECT category_thumb_image,category_full_image FROM #__{vm}_category WHERE category_id='". $d["category_id"] . "'";
        $db->query( $q );
        $db->next_record();

        /** Image Upload Validation **/
        // do we have an image URL or an image File Upload?
        if (!empty( $d['category_thumb_image_url'] )) {
            // Image URL
            if (substr( $d['category_thumb_image_url'], 0, 4) != "http") {
                $vmLogger->err( "An Image URL must begin with 'http'." );
                $valid =  false;
            }

            // if we have an uploaded image file, prepare this one for deleting.
            if( $db->f("category_thumb_image") && substr( $db->f("category_thumb_image"), 0, 4) != "http") {
                $_REQUEST["category_thumb_image_curr"] = $db->f("category_thumb_image");
                $d["category_thumb_image_action"] = "delete";
                if (!validate_image( $d, "category_thumb_image", "category")) {
                    return false;
                }
                $_REQUEST["category_thumb_image_curr"] = $french_database[0];
                $d["category_thumb_image_action"] = "delete";
                if (!validate_image( $d, "category_thumb_image", "category")) {
                    return false;
                }
            }
            $d["category_thumb_image"] = $d['category_thumb_image_url'];
        }
        else {

            // File Upload
            if (!validate_image( $d, "category_thumb_image", "category")) {
                $valid = false;
            }
            // File Upload

            if( $d["category_thumb_image_action"] == "delete" )
            {
                $_REQUEST["category_thumb_image_curr"] = $french_database[0];
                if (!validate_image( $d, "category_thumb_image", "category")) {
                    $valid = false;
                }
            }

        }

        if (!empty( $d['category_full_image_url'] )) {
            // Image URL
            if (substr( $d['category_full_image_url'], 0, 4) != "http") {
                $vmLogger->err( "Image URL must begin with 'http'." );
                return false;
            }
            // if we have an uploaded image file, prepare this one for deleting.
            if( $db->f("category_full_image") && substr( $db->f("category_full_image"), 0, 4) != "http") {
                $_REQUEST["category_full_image_curr"] = $db->f("category_full_image");
                $d["category_full_image_action"] = "delete";
                if (!validate_image( $d, "category_full_image", "category")) {
                    return false;
                }
                $_REQUEST["category_full_image_curr"] = $french_database[1];
                $d["category_full_image_action"] = "delete";
                if (!validate_image( $d, "category_full_image", "category")) {
                    return false;
                }
            }
            $d["category_full_image"] = $d['category_full_image_url'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_full_image", "category")) {
                $valid = false;
            }
            if( $d["category_full_image_action"] == "delete" )
            {
                $_REQUEST["category_full_image_curr"] = $french_database[1];
                if (!validate_image( $d, "category_full_image", "category")) {
                    $valid = false;
                }
            }
        }

        if (!empty( $d['category_thumb_image_url_fr'] )) {
            // Image URL
            if (substr( $d['category_thumb_image_url_fr'], 0, 4) != "http") {
                $vmLogger->err( "An Image URL must begin with 'http'." );
                $valid =  false;
            }

            // if we have an uploaded image file, prepare this one for deleting.
            if( $french_database[0] && substr( $french_database[0], 0, 4) != "http") {
                $_REQUEST["category_thumb_image_fr_curr"] = $french_database[0];
                $d["category_thumb_image_fr_action"] = "delete";
                if (!validate_image( $d, "category_thumb_image_fr", "category")) {
                    return false;
                }
            }
            $d["category_thumb_image_fr"] = $d['category_thumb_image_url_fr'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_thumb_image_fr", "category")) {
                $valid = false;
            }
        }

        if (!empty( $d['category_full_image_url_fr'] )) {
            // Image URL
            if (substr( $d['category_full_image_url_fr'], 0, 4) != "http") {
                $vmLogger->err( "Image URL must begin with 'http'." );
                return false;
            }
            // if we have an uploaded image file, prepare this one for deleting.
            if( $french_database[1] && substr( $french_database[1], 0, 4) != "http") {
                $_REQUEST["category_full_image_fr_curr"] = $french_database[1];
                $d["category_full_image_fr_action"] = "delete";
                if (!validate_image( $d, "category_full_image_fr", "category")) {
                    return false;
                }
            }
            $d["category_full_image_fr"] = $d['category_full_image_url_fr'];
        }
        else {
            // File Upload
            if (!validate_image( $d, "category_full_image_fr", "category")) {
                $valid = false;
            }
        }
        
        $checkAlias = $this->checkAlias($d['alias'], $d['category_id']);
        
        if ($checkAlias->result == false) {
            $err = 'Alias is busy, check:<br/>';
            
            foreach ($checkAlias->categories AS $category_obj) {
                $err .= '<a href="/administrator/index2.php?option=com_virtuemart&page=product.product_category_form&category_id='.$category_obj->category_id.'" target="_blank">'.$category_obj->category_name.'</a><br/>';
            }

            $vmLogger->err($err);
            $valid = false;
        }
        
        return $valid;
    }
    
    private function checkAlias($alias, $category_id) {
        global $database;
        
        $return = (object)[
            'result' => true
        ];
        
        $query = "SELECT
            `c`.`category_id`,
            `c`.`category_name`
        FROM `jos_vm_category` AS `c`
        INNER JOIN `jos_vm_category_xref` AS `c_x`
            ON
            `c_x`.`category_child_id`=`c`.`category_id`
        INNER JOIN `jos_vm_category_xref` AS `c_x2`
            ON
            `c_x2`.`category_parent_id`=`c_x`.`category_parent_id`
        WHERE 
            `c`.`alias`='".$database->getEscaped($alias)."'
            AND
            `c`.`category_id`!=".(int)$category_id."
            AND
            `c_x2`.`category_child_id`=`c`.`category_id`
        ";
        
        $database->setQuery($query);
        $categories_obj = $database->loadObjectList();
        
        if ((!is_null($categories_obj)) AND (count($categories_obj) > 0)) {
            $return->result = false;
            $return->categories = $categories_obj;
        }
        
        return $return;
    }

    /**
     * Validates all product category fields and uploaded image files
     * on category deletion.
     *
     * @param mixed $category_id The category_id (or IDs when it's an array)
     * @param array $d The input vars
     * @return boolean True when validation successful, false when not
     */
    function validate_delete( $category_id, &$d) {
        global $vmLogger;
        $db = new ps_DB;

        if (empty( $category_id )) {
            $vmLogger->err( "Please select a category to delete." );
            return False;
        }

        // Check for children
        $q  = "SELECT * FROM #__{vm}_category_xref where category_parent_id='$category_id'";
        $db->setQuery($q);   $db->query();
        if ($db->next_record()) {
            $vmLogger->err( "This category has children - please delete those children first.");
            return False;
        }

        $q = "SELECT category_thumb_image,category_full_image FROM #__{vm}_category_data_fr WHERE category_id='" . $category_id . "'";
        $db->query( $q );
        $db->next_record();
        $french_database = array();
        $french_database[] = $db->f("category_thumb_image");
        $french_database[] = $db->f("category_full_image");

        $q = "SELECT category_thumb_image,category_full_image FROM #__{vm}_category WHERE category_id='$category_id'";
        $db->query( $q );
        $db->next_record();

        /* Prepare category_thumb_image for Deleting */
        if( !stristr( $db->f("category_thumb_image"), "http") ) {
            $_REQUEST["category_thumb_image_curr"] = $db->f("category_thumb_image");
            $d["category_thumb_image_action"] = "delete";
            if (!validate_image($d,"category_thumb_image","category")) {
                $vmLogger->err( "Failed deleting Category Images!" );
                return false;
            }
        }

        if( !stristr( $db->f("category_thumb_image_fr"), "http") ) {
            $_REQUEST["category_thumb_image_curr_fr"] = $french_database[0];
            $d["category_thumb_image_action_fr"] = "delete";
            if (!validate_image($d,"category_thumb_image_fr","category")) {
                $vmLogger->err( "Failed deleting Category Images!" );
                return false;
            }
        }

        /* Prepare product_full_image for Deleting */
        if( !stristr( $db->f("category_full_image"), "http") ) {
            $_REQUEST["category_full_image_curr"] = $db->f("category_full_image");
            $d["category_full_image_action"] = "delete";
            if (!validate_image($d,"category_full_image","category")) {
                return false;
            }
        }

        if( !stristr( $db->f("category_full_image_fr"), "http") ) {
            $_REQUEST["category_full_image_curr_fr"] = $french_database[1];
            $d["category_full_image_action_fr"] = "delete";
            if (!validate_image($d,"category_full_image_fr","category")) {
                return false;
            }
        }

        return True;
    }

    /**
     * Creates a new category record and a category_xref record
     * with the appropriate parent and child ids
     * @author pablo
     * @author soeren
     *
     * @param array $d
     * @return mixed - int category_id on success, false on error
     */
    function add( &$d ) {
        global $vmLogger,$mosConfig_live_site,$my;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        $db = new ps_DB;
        $timestamp = time();

        if ($this->validate_add($d)) {

            if (!process_images($d)) {
                return false;
            }

            while(list($key,$value)= each($d)) {
                if (!is_array($value))
                    $d[$key] = addslashes($value);
            }
            // Let's find out the last category in
            // the level of the new category
            $q = "SELECT MAX(list_order) AS list_order FROM #__{vm}_category_xref,#__{vm}_category ";
            $q .= "WHERE category_parent_id='".$d["parent_category_id"]."' ";
            $q .= "AND category_child_id=category_id ";
            $db->query( $q );
            $db->next_record();

            $list_order = intval($db->f("list_order"))+1;

            if (empty($d["category_publish"])) {
                $d["category_publish"] = "N";
            }

            $q = "INSERT into #__{vm}_category (vendor_id, category_name, ";
            $q .= "category_publish, category_description, category_description_city,category_browsepage, products_per_row, ";
            $q .= "category_flypage, category_thumb_image, category_full_image, cdate, mdate, list_order, meta_info, meta_info_fr, alias) ";
            $q .= "VALUES ('$ps_vendor_id','";
            $q .= $d["category_name"] . "','";
            if ($d["category_publish"] != "Y") {
                $d["category_publish"] = "N";
            }
            $q .= $d["category_publish"] . "','";
            $q .= $d["category_description"] . "','";
            $q .= $d["category_description_city"] . "','";
            $q .= $d["category_browsepage"] . "','";
            $q .= $d["products_per_row"] . "','";
            $q .= $d["category_flypage"] . "','";
            $q .= $d["category_thumb_image"] . "','";
            $q .= $d["category_full_image"] . "','";
            $q .= $timestamp . "','";
            $q .= $timestamp . "', '";
            $q .= $list_order . "',";
            $q .= "'".$d["page_title"]."[--2010--]".$d["meta_description"]."[--2010--]".$d["meta_keywords"]."',";
            $q .= "'".$d["page_title_fr"]."[--2010--]".$d["meta_description_fr"]."[--2010--]".$d["meta_keywords_fr"]."',";
            $q .= "'".$d["alias"]."'";
            $q .= ")";
            $db->setQuery($q);
            $db->query();

            $d["category_id"] = $category_id = $db->last_insert_id();

            include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
            $ResizeAndSave = new ResizeImageAndSaveToS3();
            $ResizeAndSave->resizeCategoryImageAndSave((object)$d,IMAGEPATH .'category/'. $d["category_full_image"],$db);

            if ($mosConfig_live_site == 'https://bloomex.com.au') {
                $category_change_table = "#__{vm}_category_history_live";
            } else {
                $category_change_table = "#__{vm}_category_history_stage";
            }

            $category_change_sql = "INSERT INTO `" . $category_change_table . "`
                        (`category_id`, `name`, `username`, `date`) VALUES
                        (" . $category_id . ", 'Created', '" . $my->username . "', DATE_SUB(NOW(), INTERVAL 4 HOUR))";

            $db->setQuery($category_change_sql);
            $db->query();


            $newdata=array();
            $newdata['category_name']=$d["category_name"];
            $newdata['category_description']=$d["category_description"];
            $newdata['meta_info']=$d["page_title"]."[--2010--]".$d["meta_description"]."[--2010--]".$d["meta_keywords"];
            $newdata['alias']=$d["alias"];
            $newdata['category_publish']=$d["category_publish"];

            if($newdata){
                mosChangesNotification('category_add','',$newdata,$category_id);
            }


            $q = "INSERT into #__{vm}_category_xref ";
            $q .= "(category_parent_id, category_child_id) ";
            $q .= "VALUES ('";
            $q .= $d["parent_category_id"] . "','";
            $q .= $category_id . "')";
            $db->setQuery($q);
            $db->query();

            $q = "INSERT INTO #__{vm}_category_data_fr ";
            $q .= "(category_id, category_thumb_image, category_full_image) ";
            $q .= "VALUES ('" . $category_id . "','" . $d["category_thumb_image_fr"] . "','" . $d["category_full_image_fr"] . "')";
            $db->setQuery($q);
            $db->query();
            
            $category_type = mosGetParam($_REQUEST, "category_type_option","");
            $sitemap_publish = mosGetParam($_REQUEST, "sitemap_publish","");
            $h1 = mosGetParam($_REQUEST, "h1","");
            $h1_city = mosGetParam($_REQUEST, "h1_city","");
            $description_footer = isset($_POST['description_footer']) ? $_POST['description_footer'] : '';
            $description_footer_city = isset($_POST['description_footer_city']) ? $_POST['description_footer_city'] : '';
            $canonical_category_id = isset($_POST['canonical_category_id']) ? $_POST['canonical_category_id'] : '';
            $child_list_publish = mosGetParam($_REQUEST, "child_list_publish","");
            
            if (empty($sitemap_publish)) {
                $sitemap_publish = '0';
            }
            
            if (empty($child_list_publish)) {
                $child_list_publish = '0';
            }

            global $mosConfig_absolute_path, $database;
            
            $query = "INSERT INTO `jos_vm_category_options`
            ( 
                `category_id`, 
                `category_type`,
                `sitemap_publish`,
                `h1`,
                `h1_city`,
                `description_footer`,
                `description_footer_city`,
                `canonical_category_id`,
                `child_list_publish`
            )
            VALUES (
                '".$category_id."',
                '".$category_type."',
                '".$sitemap_publish."',
                '".$h1."',
                '".$h1_city."',
                '".$database->getEscaped($description_footer)."',
                '".$database->getEscaped($description_footer_city)."',
                '".$database->getEscaped($canonical_category_id)."',
                '".$child_list_publish."'
            )";
            
            $db->setQuery($query);
            $db->query();
        

            require_once($mosConfig_absolute_path."/includes/class.upload.php");

            $sError	= "";

            $sImage_bg = "";
            if( !empty($_FILES['background']['name']) ) {
                $handle = new upload($_FILES['background']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body  	= 'header_'.time();
                    $handle->image_resize         		= false;
                    $handle->image_x             	 	= 735;
                    $handle->image_y              		= 193;
                    $handle->file_max_size 			= '1048576';

                    $handle->process($mosConfig_absolute_path."/images/header_images/");
                    if ($handle->processed) {
                        $sImage_bg = $handle->file_dst_name;
                    } else {
                        $sError 	= 'Errror for Header Image: ' . $handle->error;
                    }
                }
            }

            $sImage	= "";
            if( !empty($_FILES['header_image']['name']) ) {
                $handle = new upload($_FILES['header_image']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body  	= 'header_'.time();
                    $handle->image_resize         		= false;
                    $handle->image_x             	 	= 735;
                    $handle->image_y              		= 193;
                    $handle->file_max_size 			= '1048576';

                    $handle->process($mosConfig_absolute_path."/images/header_images/");
                    if ($handle->processed) {
                        $sImage = $handle->file_dst_name;
                    } else {
                        $sError 	= 'Errror for Header Image: ' . $handle->error;
                    }
                }
            }

            $sImageFr	= "";
            if( !empty($_FILES['header_image_fr']['name']) ) {
                $handle = new upload($_FILES['header_image_fr']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body  	= 'header_fr_'.time();
                    $handle->image_resize         		= false;
                    $handle->image_x             	 	= 735;
                    $handle->image_y              		= 193;
                    $handle->file_max_size 			= '1048576';

                    $handle->process($mosConfig_absolute_path."/images/header_images/");
                    if ($handle->processed) {
                        $sImageFr = $handle->file_dst_name;
                    } else {
                        $sError 	= 'Errror for French Header Image: ' . $handle->error;
                    }
                }
            }
            //END

            if( (!empty($_FILES['background']['name']) || !empty($_FILES['header_image']['name']) || !empty($_FILES['header_image_fr']['name'])) && $sError != "" ) {
                $vmLogger->info( $sError);
                return False;
            }else {
                $sql = "INSERT INTO #__vm_category_header_img  VALUES (NULL, $category_id, '$sImage', '$sImageFr', '$sImage_bg')";
                $database->setQuery($sql);
                $database->query();

                $vmLogger->info( "Successfully added new category: ".$d['category_name'].'.');
                return $category_id;
            }

        }
        else {
            return False;
        }

    }

    /**
     * Updates a category record and its category_xref record
     *
     * @author pablo
     * @author soeren
     *
     * @param array $d
     * @return boolean true on success, false on error
     */
    function get_child_categories_list($child_categories=''){
        global  $database;
        $get_child_categories = "SELECT group_concat(category_child_id) as child_list
                                FROM `jos_vm_category_xref`
                                WHERE `category_parent_id` in (".$child_categories.")";

        $database->setQuery($get_child_categories);
        $res =  $database->loadResult();
        return $res;


    }
    function get_category_publish_unpublish($category_id){
        global  $database;
        $sql = "SELECT category_publish
                                FROM `jos_vm_category`
                                WHERE `category_id` LIKE ".$category_id."";
        $database->setQuery($sql);
        $res =  $database->loadResult();
        return $res;
    }



    function update(&$d) {
        global $vmLogger, $database,$mosConfig_live_site,$my;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        $db = new ps_DB;

        $timestamp = time();

        foreach ($d as $key => $value) {
            if (!is_array($value))
                $d[$key] = addslashes($value);
        }
        if ($this->validate_update($d)) {
            if (!process_images($d)) {
                return false;
            }


            $category_old = false;
            $sql = "SELECT * FROM #__{vm}_category WHERE `category_id`='" . $d["category_id"] . "'";
            $db->query($sql);
            $db->loadObject($category_old);

            $query_select_old_data = "SELECT category_name,category_description,meta_info,alias,category_publish FROM #__{vm}_category WHERE  category_id='" . $d["category_id"] . "'";
            $db->query( $query_select_old_data );
            $olddata=$db->loadAssocList();

            $newdata=array();
            $newdata['category_name']=$d["category_name"];
            $newdata['category_description']=$d["category_description"];
            $newdata['meta_info']=$d["page_title"]."[--2010--]".$d["meta_description"]."[--2010--]".$d["meta_keywords"];
            $newdata['alias']=$d["alias"];
            $newdata['category_publish']=$d["category_publish"];

            if($olddata && $newdata){
                mosChangesNotification('category_update',$olddata[0],$newdata,$d["category_id"]);
            }



            $q = "UPDATE #__{vm}_category SET ";
            $q .= "category_name='" . $d["category_name"];
            if (!isset($d["category_publish"])) {
                $d["category_publish"] = "N";
            }
            $q .= "',category_publish='" . $d["category_publish"];
            $q .= "',category_description='" . $d["category_description"];
            $q .= "',category_description_city='" . $d["category_description_city"];
            $q .= "',category_browsepage='" . $d["category_browsepage"];
            $q .= "',products_per_row='" . $d["products_per_row"];
            $q .= "',category_flypage='" . $d["category_flypage"] ."',";
            if( !($d["category_thumb_image_action"] != "delete" && $d["category_thumb_image"] == '' ) ) $q .= "category_thumb_image='" . $d["category_thumb_image"] ."',";
            if( !($d["category_full_image_action"] != "delete" && $d["category_full_image"] == '' ) ) $q .= "category_full_image='" . $d["category_full_image"] ."',";
            $q .= "mdate='$timestamp";
            $q .= "', list_order='" . $d["list_order"]."',";
            $q .= "meta_info='".$d["page_title"]."[--2010--]".$d["meta_description"]."[--2010--]".$d["meta_keywords"]."',";
            $q .= "meta_info_fr='".$d["page_title_fr"]."[--2010--]".$d["meta_description_fr"]."[--2010--]".$d["meta_keywords_fr"]."',";
            $q .= "alias='".$d["alias"]."' ";
            $q .= " WHERE category_id='" . $d["category_id"] . "' ";
            $q .= "AND vendor_id='$ps_vendor_id' ";

            $db->setQuery($q);
            $db->query();


            include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
            $ResizeAndSave = new ResizeImageAndSaveToS3();
            $ResizeAndSave->resizeCategoryImageAndSave((object)$d,IMAGEPATH .'category/'. $d["category_full_image"],$db);

            $category_new = false;
            $sql = "SELECT * FROM #__{vm}_category WHERE `category_id`='" . $d["category_id"] . "'";
            $db->query($sql);
            $db->loadObject($category_new);

            if ($category_new) {
                $category_changes = array();

                foreach ($category_new as $k_new => $v_new) {
                    if ($k_new != 'mdate') {
                        if ($category_old->$k_new != $v_new) {
                            $category_changes[] = array('name' => $k_new, 'old' => $category_old->$k_new, 'new' => $v_new, 'username' => $my->username);
                        }
                    }
                }
            }

            if (sizeof($category_changes) > 0) {
                $category_change_imports = array();

                foreach ($category_changes as $category_change) {
                    $category_change_imports[] = "(" . $d["category_id"] . ", '" . $category_change['name'] . "', '" . $db->getEscaped($category_change['old']) . "', '" . $db->getEscaped($category_change['new']) . "', '" . $category_change['username'] . "', DATE_SUB(NOW(), INTERVAL 4 HOUR))";
                }

                if ($mosConfig_live_site == 'https://bloomex.com.au') {
                    $category_change_table = "#__{vm}_category_history_live";
                } else {
                    $category_change_table = "#__{vm}_category_history_stage";
                }

                $category_change_sql = "INSERT INTO `" . $category_change_table . "`
                            (`category_id`, `name`, `old`, `new`, `username`, `date`) VALUES " . implode(',', $category_change_imports) . "";

                $db->setQuery($category_change_sql);
                $db->query();
            }

            $child_categories = $d["category_id"];
            if($d["category_publish"]=='N'){
                $res = $this->get_child_categories_list($child_categories);
                if($res){
                    $child_categories=$res.','.$child_categories;
                    $second_child_categories = $this->get_child_categories_list($child_categories);
                    if($second_child_categories){
                        $child_categories =$second_child_categories.','.$child_categories;
                    }

                    $child_categories_arr = array_unique(explode(",", $child_categories));
                    foreach ($child_categories_arr as $k=>$child_category_id) {
                        if($k==0){
                            continue;
                        }
                        $category_child_publish_unpublish_old = $this->get_category_publish_unpublish($child_category_id);
                        $category_change_imports_child[] = "(" . $child_category_id . ", 'category_publish', '".$category_child_publish_unpublish_old."', 'N', '" . $my->username . "', DATE_SUB(NOW(), INTERVAL 4 HOUR))";
                    }
                    $category_change_sql_child = "INSERT INTO `" . $category_change_table . "`
                                (`category_id`, `name`, `old`, `new`, `username`, `date`) VALUES " . implode(',', $category_change_imports_child) . "";

                    $db->setQuery($category_change_sql_child);
                    $db->query();
                }
            }

            $query_child_category = "UPDATE `jos_vm_category` AS `c`
                            SET
                                `c`.`category_publish`='".$d["category_publish"]."'
                            WHERE 
                                `c`.`category_id` in (".$child_categories.")
                            ";
            $database->setQuery($query_child_category);
            $database->query();




            //MENU UPDATE
            $query = "UPDATE `jos_menu` AS `m`
            SET
                `m`.`alias`='".$database->getEscaped($d["alias"])."',
                `m`.`published`='".($d["category_publish"] == 'N' ? '0' : '1')."'
            WHERE 
                `m`.`new_type`='vm_category'
                AND
                `m`.`link`='".$d["category_id"]."'
            ";
            
            $database->setQuery($query);
            $database->query();
            //!MENU UPDATE
            
            //die( $q );

            /*
            ** update #__{vm}_category/ x-reference table with parent-child relationship
            */
            $q = "UPDATE #__{vm}_category_xref SET ";
            $q .= "category_parent_id='" . $d["category_parent_id"];
            $q .= "' WHERE category_child_id='" . $d["category_id"] . "'";
            $db->setQuery($q);
            $db->query();
            
            $category_type = mosGetParam($_REQUEST, "category_type_option","");
            $sitemap_publish = mosGetParam($_REQUEST, "sitemap_publish","");
            $h1 = mosGetParam($_REQUEST, "h1","");
            $h1_city = mosGetParam($_REQUEST, "h1_city","");
            $description_footer = isset($_POST['description_footer']) ? $_POST['description_footer'] : '';
            $description_footer_city = isset($_POST['description_footer_city']) ? $_POST['description_footer_city'] : '';
            $canonical_category_id = isset($_POST['canonical_category_id']) ? $_POST['canonical_category_id'] : '';
            $child_list_publish = mosGetParam($_REQUEST, "child_list_publish","");
            
            if (empty($sitemap_publish)) {
                $sitemap_publish = '0';
            }
            
            if (empty($child_list_publish)) {
                $child_list_publish = '0';
            }

            $query = "UPDATE `jos_vm_category_options` SET 
                `category_type`='".$category_type."',
                `sitemap_publish`='".$sitemap_publish."',
                `h1`='".$h1."',
                `h1_city`='".$h1_city."',
                `description_footer`='".$database->getEscaped($description_footer)."',
                `description_footer_city`='".$database->getEscaped($description_footer_city)."',
                `canonical_category_id`='".$database->getEscaped($canonical_category_id)."',
                `child_list_publish`='".$child_list_publish."'
            WHERE `category_id`='" . $d["category_id"] . "'";
            
            $db->setQuery($query);
            $db->query();

            $q = "SELECT category_id FROM #__{vm}_category_data_fr WHERE category_id='" . $d["category_id"] . "'";
            $db->query( $q );
            if( $db->next_record() )
            {
                $q1 = null;
                $q2 = null;
                if( !($d["category_thumb_image_action"] != "delete" && $d["category_thumb_image_fr"] == '' ) ) $q1 = "category_thumb_image='" . $d["category_thumb_image_fr"] ."'";
                if( !($d["category_full_image_action"] != "delete" && $d["category_full_image_fr"] == '' ) ) $q2 = "category_full_image='" . $d["category_full_image_fr"] ."'";
                if( $q || $q2 )
                {
                    $q = "UPDATE #__{vm}_category_data_fr SET ";
                    $q .= ( $q1 ) ? ( ( $q2 ) ? $q1.','.$q2 : $q1 ) : $q2;
                    $q .= " WHERE category_id='" . $d["category_id"] . "'";
                }

            }
            else
            {
                $q = "INSERT INTO #__{vm}_category_data_fr ";
                $q .= "(category_id, category_thumb_image, category_full_image) ";
                $q .= "VALUES ('" . $d["category_id"] . "','" . $d["category_thumb_image_fr"] . "','" . $d["category_full_image_fr"] . "')";
            }


            $db->setQuery($q);
            $db->query();
            /* Re-Order the category table IF the list_order has been changed */
            if( intval($d['list_order']) != intval($d['currentpos'])) {
                $dbu = new ps_DB;

                /* Moved UP in the list order */
                if( intval($d['list_order']) < intval($d['currentpos']) ) {

                    $q = "SELECT category_id FROM #__{vm}_category_xref,#__{vm}_category ";
                    $q .= "WHERE category_parent_id='".$d["category_parent_id"]."' ";
                    $q .= "AND category_child_id=category_id ";
                    $q .= "AND category_id <> '" . $d["category_id"] . "' ";
                    $q .= "AND list_order >= '" . intval($d["list_order"]) . "'";
                    $db->query( $q );

                    while( $db->next_record() ) {
                        $dbu->query("UPDATE #__{vm}_category SET list_order=list_order+1 WHERE category_id='".$db->f("category_id")."'");
                    }
                }
                /* Moved DOWN in the list order */
                else {

                    $q = "SELECT category_id FROM #__{vm}_category_xref,#__{vm}_category ";
                    $q .= "WHERE category_parent_id='".$d["category_parent_id"]."' ";
                    $q .= "AND category_child_id=category_id ";
                    $q .= "AND category_id <> '" . $d["category_id"] . "' ";
                    $q .= "AND list_order > '" . intval($d["currentpos"]) . "'";
                    $q .= "AND list_order <= '" . intval($d["list_order"]) . "'";
                    $db->query( $q );

                    while( $db->next_record() ) {
                        $dbu->query("UPDATE #__{vm}_category SET list_order=list_order-1 WHERE category_id='".$db->f("category_id")."'");
                    }

                }
            } /* END Re-Ordering */

            // Problem: When the parent id has changed, the category is
            // in a new level. We now need to change the list order value
            // of the category to the value: recent MAXIMUM + 1
            if( $d["category_parent_id"] != $d["current_parent_id"] ) {
                // Let's find out the last category in
                // the new level of the category
                $q = "SELECT MAX(list_order) AS list_order FROM #__{vm}_category_xref,#__{vm}_category ";
                $q .= "WHERE category_parent_id='".$d["category_parent_id"]."' ";
                $q .= "AND category_child_id=category_id ";
                $q .= "AND category_id <> '".$d["category_id"]."'";
                $db->query( $q );
                $db->next_record();

                $q = "UPDATE #__{vm}_category SET list_order=".$db->f("list_order")."+1 WHERE category_id='".$d["category_id"]."'";
                $db->query( $q );
            }

            //Upload Header Image
            global $mosConfig_absolute_path, $database;
            require_once($mosConfig_absolute_path."/includes/class.upload.php");

            $sImage	= "";
            $sError	= "";

            $sql		= " SELECT * FROM #__vm_category_header_img	WHERE category_id =" . $d["category_id"];
            $database->setQuery($sql);
            $rows	= $database->loadObjectList();

            if((isset($_FILES['background']['name']) AND !empty($_FILES['background']['name'])) || (isset($_REQUEST['remove_background']) AND intval($_REQUEST['remove_background']) == 1) ) {
                if( !empty($rows[0]->background) && is_file($mosConfig_absolute_path."/images/header_images/".$rows[0]->background) ) {
                    $sql = "UPDATE  #__vm_category_header_img  SET background = '' WHERE category_id = " . $d["category_id"];
                    $database->setQuery($sql);
                    $database->query();

                    @unlink($mosConfig_absolute_path."/images/header_images/".$rows[0]->background);
                }
            }

            if((isset($_FILES['header_image']['name']) AND !empty($_FILES['header_image']['name'])) || (isset($_REQUEST['remove_header_image']) AND intval($_REQUEST['remove_header_image']) == 1 )) {
                if( !empty($rows[0]->header_image) && is_file($mosConfig_absolute_path."/images/header_images/".$rows[0]->header_image) ) {
                    $sql = "UPDATE  #__vm_category_header_img  SET header_image = '' WHERE category_id = " . $d["category_id"];
                    $database->setQuery($sql);
                    $database->query();

                    @unlink($mosConfig_absolute_path."/images/header_images/".$rows[0]->header_image);
                }
            }

            if((isset($_FILES['header_image']['name']) AND !empty($_FILES['header_image_fr']['name'])) || (isset($_REQUEST['remove_header_image_fr']) AND intval($_REQUEST['remove_header_image_fr']) == 1 )) {
                if( !empty($rows[0]->header_image_fr) && is_file($mosConfig_absolute_path."/images/header_images/".$rows[0]->header_image_fr) ) {
                    $sql = "UPDATE  #__vm_category_header_img  SET header_image_fr = '' WHERE category_id = " . $d["category_id"];
                    $database->setQuery($sql);
                    $database->query();

                    @unlink($mosConfig_absolute_path."/images/header_images/".$rows[0]->header_image_fr);
                }
            }

            $sImage_bg = "";
            if( !empty($_FILES['background']['name']) ) {
                $handle = new upload($_FILES['background']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body  	= 'header_'.time();
                    $handle->image_resize         		= false;
                    $handle->image_x             	 	= 735;
                    $handle->image_y              		= 193;
                    $handle->file_max_size 			= '1048576';
                    $handle->process($mosConfig_absolute_path."/images/header_images/");
                    if ($handle->processed) {
                        $sImage_bg = $handle->file_dst_name;
                    } else {
                        $sError 	= 'Error for Header Image: ' . $handle->error;
                    }
                }
            }


            if( !empty($_FILES['header_image']['name']) ) {
                $handle = new upload($_FILES['header_image']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body  	= 'header_'.time();
                    $handle->image_resize         		= false;
                    $handle->image_x             	 	= 735;
                    $handle->image_y              		= 193;
                    $handle->file_max_size 			= '1048576';
                    $handle->process($mosConfig_absolute_path."/images/header_images/");
                    if ($handle->processed) {
                        $sImage = $handle->file_dst_name;
                    } else {
                        $sError 	= 'Error for Header Image: ' . $handle->error;
                    }
                }
            }

            $sImageFr	= "";
            if( !empty($_FILES['header_image_fr']['name']) ) {
                $handle = new upload($_FILES['header_image_fr']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body  	= 'header_fr_'.time();
                    $handle->image_resize         		= false;
                    $handle->image_x             	 	= 735;
                    $handle->image_y              		= 193;
                    $handle->file_max_size 			= '1048576';
                    $handle->process($mosConfig_absolute_path."/images/header_images/");
                    if ($handle->processed) {
                        $sImageFr = $handle->file_dst_name;
                    } else {
                        $sError 	= 'Error for French Header Image: ' . $handle->error;
                    }
                }
            }
            //END

            if( (!empty($_FILES['background']['name']) ||  !empty($_FILES['header_image']['name']) || !empty($_FILES['header_image_fr']['name'])) && $sError != "" ) {
                $vmLogger->info( $sError);
                return False;
            }else {
                if( !empty($_FILES['background']['name']) ) {
                    if( !empty($rows[0]->id) ) {
                        $sql = "UPDATE  #__vm_category_header_img  SET background = '$sImage_bg' WHERE category_id = " . $d["category_id"];
                        $database->setQuery($sql);
                        $database->query();
                    }else {
                        $sql = "INSERT INTO #__vm_category_header_img(category_id,background)  VALUES (".$d["category_id"].", '$sImage_bg')";
                        $database->setQuery($sql);
                        $database->query();
                    }
                }

                if( !empty($_FILES['header_image']['name']) ) {
                    if( !empty($rows[0]->id) ) {
                        $sql = "UPDATE  #__vm_category_header_img  SET header_image = '$sImage' WHERE category_id = " . $d["category_id"];
                        $database->setQuery($sql);
                        $database->query();
                    }else {
                        $sql = "INSERT INTO #__vm_category_header_img(category_id,header_image)  VALUES (".$d["category_id"].", '$sImage')";
                        $database->setQuery($sql);
                        $database->query();
                    }
                }

                if( !empty($_FILES['header_image_fr']['name']) ) {
                    if( !empty($rows[0]->id) ) {
                        $sql = "UPDATE  #__vm_category_header_img  SET header_image_fr = '$sImageFr' WHERE category_id = " . $d["category_id"];
                        $database->setQuery($sql);
                        $database->query();
                    }else {
                        $sql = "INSERT INTO #__vm_category_header_img(category_id,header_image_fr)  VALUES (".$d["category_id"].", '$sImageFr')";
                        $database->setQuery($sql);
                        $database->query();
                    }
                }

                //echo $sql;
                //die();
                $get_query_child_category = "SELECT group_concat(category_name) as updated_child_categories FROM  `jos_vm_category` 
                            WHERE `category_id` in (".$child_categories.")";
                $database->setQuery($get_query_child_category);
                $get_child_category = $database->loadResult();

                $vmLogger->info( "Successfully updated category: ".$d['category_name'].'.<br><b>List Categories : </b> '.$get_child_category);
                return True;
            }
        }
        else {
            return False;
        }
    }

    /**
     * Controller for Deleting Records.
     * @param $d Holds the category_id(s) of the category(/ies) to be deleted
     */
    function delete( &$d ) {

        $record_id = $d["category_id"];

        if( is_array( $record_id)) {
            foreach( $record_id as $record) {
                if( !$this->delete_record( $record, $d ))
                    return false;
            }
            return true;
        }
        else {
            return $this->delete_record( $record_id, $d );
        }
    }
    /**
     * Deletes one Record.
     */
    function delete_record( $record_id, &$d ) {
        global $ps_product, $db, $vmLogger;

        if (!$this->validate_delete($record_id, $d)) {
            return False;
        }
        // Delete all products from that category
        // We must filter out those products that are in more than one category!

        // Case 1: Products are assigned to more than on category
        // so let's only delete the __{vm}_product_category_xref entry
        $q = "CREATE TEMPORARY TABLE IF NOT EXISTS `#__tmp_prod` AS
            (SELECT * FROM `#__{vm}_product_category_xref` 
            WHERE `category_id`='$record_id');";
        $db->query( $q );
        $q = "SELECT #__{vm}_product_category_xref.product_id
          FROM `#__{vm}_product_category_xref`, `#__tmp_prod` 
          WHERE #__{vm}_product_category_xref.product_id=#__tmp_prod.product_id 
            AND #__{vm}_product_category_xref.category_id!='$record_id';";
        $db->query( $q );
        if( $db->num_rows() > 0 ) {
            $i = 0;
            $q = "DELETE FROM #__{vm}_product_category_xref WHERE product_id IN (";
            while( $db->next_record() ) {
                $q .= "'".$db->f("product_id")."'";
                if( $i++ < $db->num_rows()-1 )
                    $q .= ",";
            }
            $q .= ") AND category_id='$record_id'";
            $db->query( $q );
        }
        else {
            // Case 2: Products are assigned to this category only
            $q = "DELETE FROM #__{vm}_product_category_xref WHERE `category_id`='$record_id'";
            $db->query ( $q );
        }

        $q = "DELETE FROM #__{vm}_category WHERE category_id='$record_id'";
        $db->setQuery($q);  $db->query();

        mosChangesNotification('category_delete','','',$record_id);
        $q  = "DELETE FROM #__{vm}_category_xref WHERE category_child_id='$record_id'";
        $db->setQuery($q);   $db->query();

        /* Delete Image files */
        if (!process_images($d)) {
            return false;
        }
        $vmLogger->info( "Successfully deleted category ID: $record_id." );
        return True;
    }
    /**
     * This function is repsonsible for returning an array containing category information
     * @param boolean Show only published products?
     * @param string the keyword to filter categories
     */
    function getCategoryTreeArray( $only_published=true, $keyword = "" ) {

        $db = new ps_DB;
        if( empty( $GLOBALS['category_info']['category_tree'])) {

            // Get only published categories
            $query  = "SELECT category_id, category_description, category_name,category_child_id as cid, category_parent_id as pid,list_order, category_publish
						FROM #__{vm}_category, #__{vm}_category_xref WHERE ";
            if( $only_published ) {
                $query .= "#__{vm}_category.category_publish='Y' AND ";
            }
            $query .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
            if( !empty( $keyword )) {
                $query .= "AND ( category_name LIKE '%$keyword%' ";
                $query .= "OR category_description LIKE '%$keyword%' ";
                $query .= ") ";
            }
            $query .= "ORDER BY #__{vm}_category.list_order ASC, #__{vm}_category.category_name ASC";

            // initialise the query in the $database connector
            // this translates the '#__' prefix into the real database prefix
            
            $db->query( $query );

            $categories = Array();
            // Transfer the Result into a searchable Array

            while( $db->next_record() ) {
                $categories[$db->f("cid")]["category_child_id"] = $db->f("cid");
                $categories[$db->f("cid")]["category_parent_id"] = $db->f("pid");
                $categories[$db->f("cid")]["category_name"] = $db->f("category_name");
                $categories[$db->f("cid")]["category_description"] = $db->f("category_description");
                $categories[$db->f("cid")]["list_order"] = $db->f("list_order");
                $categories[$db->f("cid")]["category_publish"] = $db->f("category_publish");
            }

            $GLOBALS['category_info']['category_tree'] = $categories;
            return $GLOBALS['category_info']['category_tree'];
        }
        else {
            return $GLOBALS['category_info']['category_tree'];
        }
    }

    /**
     * This function is used for the frontend to display a
     * complete link list of top-level categories
     *
     * @param int $category_id The category to be highlighted
     * @param string $links_css_class The css class that marks mainlevel links
     * @param string $list_css_class (deprecated)
     * @param string $highlighted_style The css styles that format the hightlighted category
     * @return string HTML code with the link list
     */
    function get_category_tree( $category_id=0,
                                $links_css_class="mainlevel",
                                $list_css_class="mm123",
                                $highlighted_style="font-style:italic;" ) {
        global $sess;

        $categories = ps_product_category::getCategoryTreeArray();

        // Copy the Array into an Array with auto_incrementing Indexes
        $key = array_keys($categories);
        $size = sizeOf($key);
        $category_tmp = Array();
        for ($i=0; $i<$size; $i++)
            $category_tmp[$i] = &$categories[$key[$i]];

        $html = "";
        /** FIRST STEP
         * Order the Category Array and build a Tree of it
         **/
        $nrows = count( $category_tmp );

        $id_list = array();
        $row_list = array();
        $depth_list = array();

        for($n = 0 ; $n < $nrows ; $n++)
            if($category_tmp[$n]["category_parent_id"] == 0)
            { array_push($id_list,$category_tmp[$n]["category_child_id"]);
                array_push($row_list,$n);
                array_push($depth_list,0);
            }

        $loop_count = 0;
        while(count($id_list) < $nrows) {
            if( $loop_count > $nrows )
                break;
            $id_temp = array();
            $row_temp = array();
            $depth_temp = array();
            for($i = 0 ; $i < count($id_list) ; $i++) {
                $id = $id_list[$i];
                $row = $row_list[$i];
                $depth = $depth_list[$i];
                array_push($id_temp,$id);
                array_push($row_temp,$row);
                array_push($depth_temp,$depth);
                for($j = 0 ; $j < $nrows ; $j++)
                    if(($category_tmp[$j]["category_parent_id"] == $id)
                        && (array_search($category_tmp[$j]["category_child_id"],$id_list) == NULL))
                    { array_push($id_temp,$category_tmp[$j]["category_child_id"]);
                        array_push($row_temp,$j);
                        array_push($depth_temp,$depth + 1);
                    }
                if (array_key_exists($j, $category_tmp)) {
                    if( empty( $categories[@$category_tmp[$j]["category_parent_id"]] )) {

                        array_push($id_temp,"");
                        array_push($row_temp,"");
                        array_push($depth_temp,"");
                    }
                }
            }
            $id_list = $id_temp;
            $row_list = $row_temp;
            $depth_list = $depth_temp;
            $loop_count++;
        }

        /** SECOND STEP
         * Find out if we have subcategories to display
         **/
        $allowed_subcategories = Array();
        if( !empty( $categories[$category_id]["category_parent_id"] ) ) {
            // Find the Root Category of this category
            $root = $categories[$category_id];
            $allowed_subcategories[] = $categories[$category_id]["category_parent_id"];
            // Loop through the Tree up to the root
            while( !empty( $root["category_parent_id"] )) {
                $allowed_subcategories[] = $categories[$root["category_child_id"]]["category_child_id"];
                $root = $categories[$root["category_parent_id"]];
            }
        }
        // Fix the empty Array Fields
        if( $nrows < count( $row_list ) ) {
            $nrows = count( $row_list );
        }

        // Now show the categories
        for($n = 0 ; $n < $nrows ; $n++) {

            if( !isset( $row_list[$n] ) || !isset( $category_tmp[$row_list[$n]]["category_child_id"] ) )
                continue;
            if( $category_id == $category_tmp[$row_list[$n]]["category_child_id"] )
                $style = $highlighted_style;
            else
                $style = "";

            $allowed = false;
            if( $depth_list[$n] > 0 ) {
                // Subcategory!
                if( isset( $root ) && in_array( $category_tmp[$row_list[$n]]["category_child_id"], $allowed_subcategories )
                    || $category_tmp[$row_list[$n]]["category_parent_id"] == $category_id
                    || $category_tmp[$row_list[$n]]["category_parent_id"] == @$categories[$category_id]["category_parent_id"]) {
                    $allowed = true;

                }
            }
            else
                $allowed = true;
            $append = "";
            if( $allowed ) {
                if( $style == $highlighted_style ) {
                    $append = 'id="active_menu"';
                }
                if( $depth_list[$n] > 0 )
                    $css_class = "sublevel";
                else
                    $css_class = $links_css_class;

                $catname = shopMakeHtmlSafe( $category_tmp[$row_list[$n]]["category_name"] );

                $html .= '
          <a title="'.$catname.'" style="display:block;'.$style.'" class="'. $css_class .'" href="'. $sess->url(URL."index.php?page=shop.browse&amp;category_id=".$category_tmp[$row_list[$n]]["category_child_id"]) .'" '.$append.'>'
                    . str_repeat("&nbsp;&nbsp;&nbsp;",$depth_list[$n]) . $catname
                    . ps_product_category::products_in_category( $category_tmp[$row_list[$n]]["category_child_id"] )
                    .'</a>';
            }
        }

        return $html;
    }

    /**
     * Function to print a table containing all categories sorted and structured
     * It goes through the category table and establishes
     * the category tree based on the parent-child relationships
     * defnied in the category_xref table.
     * This is VERY recursive...
     * @deprecated
     *
     * @param unknown_type $class
     * @param unknown_type $category_id
     * @param unknown_type $level
     */
    function traverse_tree_down($class="",$category_id="0", $level="0") {
        static $ibg = 0;
        global $sess, $mosConfig_live_site, $VM_LANG;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $db = new ps_DB;
        $class = "maintext";

        $level++;

        $q = "SELECT * FROM #__{vm}_category,#__{vm}_category_xref ";
        $q .= "WHERE #__{vm}_category_xref.category_parent_id='";
        $q .= $category_id . "' AND ";
        $q .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
        $q .= "AND #__{vm}_category.vendor_id='$ps_vendor_id' ";
        $q .= "ORDER BY list_order asc ";
        $db->setQuery($q);
        $db->query();

        while ($db->next_record()) {
            $product_count = $this->product_count($db->f("category_child_id"));
            if ($level % 2)
                $bgcolor=SEARCH_COLOR_1;
            else
                $bgcolor=SEARCH_COLOR_2;
            $ibg++;
            echo "<tr bgcolor=\"$bgcolor\">\n";
            echo "<td><input style=\"display:none;\" id=\"cb$ibg\" name=\"cb[]\" value=\"".$db->f("category_id")."\" type=\"checkbox\" />&nbsp;$ibg</td><td>";
            for ($i=0; $i<$level; $i++) {
                echo "&nbsp;&nbsp;&nbsp;";
            }
            echo "&#095;&#095;|$level|&nbsp;";
            echo "<a href=\"" ;
            echo $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_category_form&category_id=" . $db->f("category_child_id"). "&category_parent_id=" . $db->f("category_parent_id");
            echo "\">";
            echo $db->f("category_name") . "</a></td>\n";
            echo "<td>&nbsp;&nbsp;" . $db->f("category_description");
            echo "</td>\n<td>".$product_count ." ". $VM_LANG->_PHPSHOP_PRODUCTS_LBL."&nbsp;<a href=\"";
            echo $_SERVER['PHP_SELF'] . "?page=product.product_list&category_id=" . $db->f("category_child_id")."&option=com_virtuemart";
            echo "\">[ ".$VM_LANG->_PHPSHOP_SHOW." ]</a>\n</td>\n";
            //echo "<td>". $db->f("list_order")."</td>";
            echo "<td>";
            if ($db->f("category_publish")=='N') {
                echo "<img src=\"". $mosConfig_live_site ."/administrator/images/publish_x.png\" border=\"0\" />";
            }
            else {
                echo "<img src=\"". $mosConfig_live_site ."/administrator/images/tick.png\" border=\"0\" />\n";
            }
            echo "<td width=\"5%\"><div align=\"center\">\n";
            echo mShop_orderUpIcon( $db->row, $db->num_rows(), $ibg ) . "\n&nbsp;" . mShop_orderDownIcon( $db->row, $db->num_rows(), $ibg );
            echo "</div></td>\n";
            echo "<td width=\"5%\">";
            echo "<a class=\"toolbar\" href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=".$_REQUEST['page'] ."&func= productCategoryDelete&category_id=". $db->f("category_id") ."\"";
            echo " onclick=\"return confirm('". $VM_LANG->_PHPSHOP_DELETE_MSG ."');\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('Delete$ibg','','". IMAGEURL ."ps_image/delete_f2.gif',1);\">";
            echo "<img src=\"". IMAGEURL ."ps_image/delete.gif\" alt=\"Delete this record\" name=\"delete$ibg\" align=\"middle\" border=\"0\" /></a></td>\n";
            $this->traverse_tree_down($class, $db->f("category_child_id"), $level);
        }
    }

    /**
     * Function to calculate and return the number of products in category $category_id
     * @author pablo
     * @author soeren
     *
     * @param int $category_id
     * @return int The number of products found
     */
    function product_count($category_id) {
        global $perm;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        $db = new ps_DB;
        if( !isset($GLOBALS['category_info'][$category_id]['product_count'] )) {

            $count  = "SELECT count(#__{vm}_product.product_id) as num_rows from #__{vm}_product,#__{vm}_product_category_xref, #__{vm}_category WHERE ";
            $q = "";
            if (defined('_PSHOP_ADMIN' )) {
                if (!$perm->check( "admin,storeadmin")) {
                    $q .= "#__{vm}_product.vendor_id = '$ps_vendor_id' AND ";
                }
            }
            $q .= "#__{vm}_product_category_xref.category_id='$category_id' ";
            $q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
            $q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
            //$q .= "AND #__{vm}_product.product_parent_id='' ";
            if( !$perm->check("admin,storeadmin") ) {
                $q .= " AND product_publish='Y'";
                if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
                    $q .= " AND product_in_stock > 0 ";
                }
            }
            $count .= $q;
            $db->query($count);
            $db->next_record();
            $GLOBALS['category_info'][$category_id]['product_count'] = $db->f("num_rows");
        }
        return $GLOBALS['category_info'][$category_id]['product_count'];
    }

    /**
     * Prints a drop-down list with all categories sorted and structured
     * @author pablo
     * @param int $category_id
     * @param int $level
     */
    function traverse_tree_up($category_id, $level=0) {
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $db = new ps_DB;

        $level++;
        $q = "SELECT #__{vm}_category.category_name,category_child_id,category_parent_id FROM #__{vm}_category, #__{vm}_category_xref ";
        $q .= "WHERE #__{vm}_category_xref.category_child_id=' ";
        $q .= "$category_id' AND ";
        $q .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_parent_id ";
        $q .= "AND #__{vm}_category.vendor_id = $ps_vendor_id ";
        $db->setQuery($q);   $db->query();
        while ($db->next_record()) {
            if ($level == 1) {
                echo "<option selected=\"selected\" value=\"" . $db->f("category_child_id");
            }
            else {
                echo "<option value=\"" . $db->f("category_child_id");
            }
            echo "\">" . $db->f("category_name") . "</option>";

            $this->traverse_tree_up($db->f("category_parent_id"), $level);
        }
    }

    /**
     * Prints a drop-down list with all categories. The category $category_id
     * with the given product_id is preselected.
     * @author pablo
     * @param int $product_id
     * @param int $category_id
     * @param string $name The name of the select element
     */
    function list_category($product_id="",$category_id="",$name = "category_id") {
        $db = new ps_DB;
        global $VM_LANG;

        echo "<select class=\"inputbox\" name=$name>\n";

        if ($product_id and !$category_id) {
            $q = "SELECT category_id from #__{vm}_product_category_xref WHERE product_id='$product_id'";
            $db->setQuery($q);   $db->query();
            $db->next_record();
            if (!$db->f("category_id")) {
                echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
            }
            $this->list_tree($db->f("category_id"));
        }
        elseif ($category_id) {
            echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
            $this->list_tree($category_id);
        }
        else {
            echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
            $this->list_tree();
        }

        echo "</select>\n";

        return True;
    }


    /**
     * Prints a bulleted of the childen of this category if they exist.
     * @author soeren
     * @param unknown_type $category_id
     */
    function print_child_list($category_id) {
        echo $this->get_child_list($category_id);
    }

    /**
     * creates a bulleted of the childen of this category if they exist
     * @author pablo
     * @param int $category_id
     * @return string The HTML code
     */

    function get_child_list_new($category_id) {
        global $database,$showOnlyJpegImageVersion, $mosConfig_live_site,$mosConfig_aws_s3_bucket_public_url, $mosConfig_lang,$sef;

        $query = "SELECT
                `c`.`category_id`,
                `c`.`category_full_image`,
                `c`.`category_name`,
                s.full_image_link_webp,
                s.full_image_link_jpeg,
                `c`.`alias`,
                `cp`.`alias` as 'parent_alias',
                count(pc.product_id) as products_count
            FROM `jos_vm_category_xref` AS `c_x`
            INNER JOIN `jos_vm_category` AS `c` ON 
                `c`.`category_id`=`c_x`.`category_child_id`
                AND 
                `c`.`category_publish`='Y'
            LEFT JOIN  jos_vm_category_s3_images as s on s.category_id = c.category_id
            INNER JOIN `jos_vm_category` AS `cp` ON 
                `cp`.`category_id`=`c_x`.`category_parent_id`
            INNER JOIN `jos_vm_category_options` AS `co`
                ON
                `co`.`category_id`=`c`.`category_id`
                AND
                `co`.`child_list_publish`='1'
            
       LEFT JOIN jos_vm_product_category_xref as pc on pc.category_id = c.category_id  
       INNER JOIN jos_vm_product as p on p.product_id = pc.product_id and p.product_publish = 'Y'
            WHERE 
                `c_x`.`category_parent_id`=".$category_id."
            GROUP BY c.category_id
            ORDER BY `c`.`list_order` ASC";

        $database->setQuery($query);

        $categories = $database->loadObjectList();
        foreach ($categories as $category) {

            $category->category_full_image = (!empty($category->category_full_image)) ? $category->category_full_image : 'no_image1.png';

            $link = '/'.(!empty($sef->real_uri) ? $sef->real_uri : '') . "/" . $category->alias . '/';

            ?>
            <div class="col-6 col-sm-3 col-md-3 col-lg-3 wrapper">
                <div class="inner">
                    <div class="image">
                        <a title="<?php echo $category->category_name; ?>" href="<?php echo $link; ?>">
                            <div class="product_image_placeholder product_image_placeholder__subcategory" style="background: url('<?php echo $mosConfig_live_site; ?>/images/stories/noimage-225x262.png');">
                                <img
                                        src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $category->full_image_link_jpeg : $category->full_image_link_webp) ; ?>"
                                        loading="lazy"
                                        decoding="async"
                                        alt="<?php echo $category->category_name; ?>"
                                        class="subcategory_image_real"
                                >
                            </div>
                        </a>
                    </div>
                    <a title="<?php echo $category->category_name; ?>" href="<?php echo $link; ?>">
                        <div class="title">
                            <?php echo $category->category_name; ?>
                            <div class="count">
                                <?php echo $category->products_count; ?>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
            <?php
        }
    }

    function get_child_list($category_id) {
        global $sess, $ps_product, $mosConfig_lang;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $db = new ps_DB;
        $db2 = new ps_DB;

        $q = "SELECT category_id, category_full_image, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
        $q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_id' ";
        $q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
        $q .= "AND #__{vm}_category.vendor_id='$ps_vendor_id' ";
        $q .= "AND #__{vm}_category.category_publish='Y' ";
        $q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
        $db->setQuery($q);
        $db->query();

        ob_start();

        if ($db->num_rows() > 0 ) {
            while($db->next_record()) {
                if( $mosConfig_lang == 'french' )
                {
                    $q2 = "SELECT category_full_image FROM #__{vm}_category_data_fr ";
                    $q2 .= "WHERE category_id='".$db->f("category_child_id")."' ";
                    $q2 .= "LIMIT 1 ";
                    $db2->setQuery($q2);
                    $db2->query();
                    $db2->next_record();
                    if ( $db2->f("category_full_image") ) {
                        $image_name = $db2->f("category_full_image");
                    }
                }
                else
                {
                    if ( $db->f("category_full_image") ) {
                        $image_name = $db->f("category_full_image");
                    }

                }
                ?>
                <div class="col-6 col-sm-3 col-md-3 col-lg-3 wrapper">
                    <div class="inner">
                        <div class="image">
                            <a title="<?php echo $db->f("category_name"); ?>" href="<?php echo $sess->url(URL."index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=".$db->f("category_id")); ?>">
                                <div class="subcategory_image_loader"></div>
                                <img style="display: none;" class="subcategory_image_real" alt="<?php echo $db->f("category_name"); ?>" src="/components/com_virtuemart/shop_image/category/<?php echo $image_name; ?>" />
                            </a>
                        </div>
                        <a title="<?php echo $db->f("category_name"); ?>" href="<?php echo $sess->url(URL."index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=".$db->f("category_id")); ?>">
                            <div class="title">
                                <?php echo $db->f("category_name"); ?>
                            </div>
                        </a>
                        <div class="count">
                            <?php echo ps_product_category::products_in_category( $db->f("category_id") ); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Prints the result of get_subcategory
     *
     * @param unknown_type $category_id
     * @param unknown_type $css_class
     */
    function print_subcategory($category_id, $css_class = "") {
        echo $this->get_subcategory( $category_id, $css_class );
    }
    /**
     * Creates a link list to subcategories of category $category_id
     *
     * @param int $category_id
     * @param string $css_class The CSS to be applied to the link
     * @return string HTML code
     */
    function get_subcategory( $category_id, $css_class = "" ) {
        global $sess;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        if( $css_class != "" ) {
            $class= "class=\"$css_class\"";
        }
        else
            $class = "";

        $db = new ps_DB;

        $q = "SELECT category_id, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
        $q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_id' ";
        $q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
        //$q .= "AND #__{vm}_category.vendor_id='$ps_vendor_id' ";
        $q .= "AND #__{vm}_category.category_publish='Y' ";
        $q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
        $db->setQuery($q);
        $db->query();
        $html = "";
        $nbsp = "&nbsp;&nbsp;&nbsp;";
        while($db->next_record()) {
            $html .= "<a style=\"display:block;\" class=\"sublevel\" title=\"".$db->f("category_name")."\" href=\"";
            $html .= $sess->url(URL . "index.php?page=shop.browse&root=$category_id&category_id=" .$db->f("category_child_id"));
            $html .= "\" $class>$nbsp".$db->f("category_name");
            $html .= ps_product_category::products_in_category( $db->f("category_child_id") );
            $html .= "</a>\n";
        }

        return $html;
    }

    /**
     * Shows the Number of Products in category $category_id
     *
     * @param int $category_id
     * @return string The number in brackets
     */
    function products_in_category( $category_id ) {
        if( PSHOP_SHOW_PRODUCTS_IN_CATEGORY == '1' ) {
            $num = ps_product_category::product_count($category_id);
            if( empty($num) && ps_product_category::has_childs( $category_id )) {
                $db = new ps_DB;
                $q = "SELECT category_child_id FROM #__{vm}_category_xref ";
                $q .= "WHERE category_parent_id='$category_id' ";
                $db->query($q);
                while( $db->next_record() ) {
                    $num += ps_product_category::product_count($db->f("category_child_id"));
                }
            }

            return $num;
        }
        else
            return '';

    }

    /**
     * tests for template/default pathway arrow separator
     * @author FTW Stroker
     * @return string The separator for the pathway breadcrumbs
     */
    static function pathway_separator() {
        global $mainframe, $mosConfig_absolute_path, $mosConfig_live_site;
        $imgPath =  'templates/' . $mainframe->getTemplate() . '/images/arrow.png';
        if (file_exists( "$mosConfig_absolute_path/$imgPath" )){
            $img = '<img src="' . $mosConfig_live_site . '/' . $imgPath . '" height="9" width="9" border="0" alt="arrow" />';
        } else {
            $imgPath = '/images/M_images/arrow.png';
            if (file_exists( $mosConfig_absolute_path . $imgPath )){
                $img = '<img src="' . $mosConfig_live_site . '/images/M_images/arrow.png" height="9" width="9" alt="arrow" />';
            } else {
                $img = '&gt;';
            }
        }
        return $img;
    }

    /**
     * Lists all categories in a drop-down list
     *
     * @param string $name The name of the select element
     * @param int $category_id The category ID
     * @param array $selected_categories The ids of the categories to be pre-selected
     * @param int $size The size of the select element
     * @param boolean $toplevel List only top-level categories?
     * @param boolean $multiple Allow multiple selections?
     */
    function list_all($name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false) {

        $db = new ps_DB;

        $q  = "SELECT category_parent_id FROM #__{vm}_category_xref ";
        if( $category_id )
            $q .= "WHERE category_child_id='$category_id'";
        $db->setQuery($q);   $db->query();
        $db->next_record();
        $category_id=$db->f("category_parent_id");
        $multiple = $multiple ? "multiple=\"multiple\"" : "";

        echo "<select class=\"inputbox\" size=\"$size\" $multiple name=\"$name\">\n";
        if( $toplevel ) {
            echo "<option value=\"0\">Default-Top Level</option>\n";
        }
        $this->list_tree_new($category_id, '0', '0', $selected_categories);
        echo "</select>\n";
    }

    /**
     * Returns a drop-down list with all child categories of a given category $category_parent_id
     *
     * @param int $category_parent_id
     * @param int $category_id When not empty, a drop-down list is created
     * @param int $list_order The pre-selected list element
     * @return string HTML code of a select list
     */
    function list_level( $category_parent_id, $category_id='0', $list_order=0 ) {

        $db = new ps_DB;
        if (!$category_id) {
            return _CMN_NEW_ITEM_LAST;
        }
        else {

            $q  = "SELECT list_order,category_id,category_name,category_child_id FROM #__{vm}_category, #__{vm}_category_xref ";
            $q .= "WHERE category_parent_id='$category_parent_id' ";
            $q .= "AND category_child_id=category_id ";
            $q .= "ORDER BY list_order ASC";
            $db->query( $q );

            $html = "<select class=\"inputbox\" name=\"list_order\">\n";
            while( $db->next_record() ) {
                if( $list_order == $db->f("list_order") ) {
                    $selected = "selected=\"selected\"";
                }
                else {
                    $selected = "";
                }
                $html .= "<option value=\"".$db->f("list_order")."\" $selected>"
                    .$db->f("list_order").". ".$db->f("category_name")
                    ."</option>\n";
            }
            $html .= "</select>\n";
            return $html;
        }
    }

    /**
     * Creates structured option fields for all categories
     *
     * @param int $category_id A single category to be pre-selected
     * @param int $cid Internally used for recursion
     * @param int $level Internally used for recursion
     * @param array $selected_categories All category IDs that will be pre-selected
     */
    function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {

        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        if(!$ps_vendor_id)
            $ps_vendor_id=1;
        $db = new ps_DB;

        $level++;

        $q = "SELECT category_id, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
        $q .= "WHERE #__{vm}_category_xref.category_parent_id='$cid' ";
        $q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
        $q .= "AND #__{vm}_category.vendor_id ='$ps_vendor_id' ";
        $q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
        $db->setQuery($q);   $db->query();

        while ($db->next_record()) {
            $child_id = $db->f("category_child_id");
            if ($child_id != $cid) {
                $selected = ($child_id == $category_id) ? "selected=\"selected\"" : "";
                if( $selected == "" && @$selected_categories[$child_id] == "1") {
                    $selected = "selected=\"selected\"";
                }
                echo "<option $selected value=\"$child_id\">\n";
            }
            for ($i=0;$i<$level;$i++) {
                echo "&#151;";
            }
            echo "|$level|";
            echo "&nbsp;" . $db->f("category_name")."</option>";
            $this->list_tree($category_id, $child_id, $level, $selected_categories);
        }
    }
    function list_tree_new($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {
        $db = new ps_DB;

        $q = "SELECT category_parent_id, category_child_id,category_name,
            `category_publish`
        FROM #__{vm}_category,#__{vm}_category_xref ";
        $q .= "WHERE  #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
        $q .= "ORDER BY #__{vm}_category_xref.category_parent_id ASC";
        $db->setQuery($q);   $db->query();
$categories = $db->loadAssocList();

$categories_assoc = array();
foreach($categories as $category){
    if($category['category_parent_id']==0){
        $categories_assoc[$category['category_child_id']] = $category;
    }
}
foreach($categories as $c){
    foreach($categories_assoc as $k=>$m){
        if($c['category_parent_id'] == $k){
            $categories_assoc[$k]['children'][$c['category_child_id']] = $c;
        }
    }
}
foreach($categories as $p){
    foreach($categories_assoc as $f=>$o){
        if(isset($o['children'])){
            foreach($o['children'] as $d=>$a){
                if($p['category_parent_id'] == $d){
                    $categories_assoc[$f]['children'][$d]['children'][$p['category_child_id']] = $p;
                }
            }
        }
    }
}


foreach($categories_assoc as $q){

                $selected = ($q['category_child_id'] == $category_id) ? "selected=\"selected\"" : "";
                if( $selected == "" && @$selected_categories[$q['category_child_id'] ] == "1") {
                    $selected = "selected=\"selected\"";
                }
                echo "<option $selected value=".$q['category_child_id'].">\n";
            echo "&#151;|1|";
            echo "&nbsp;" . $q['category_name']."".(($q['category_publish'] == 'N') ? '&nbsp;&nbsp;&nbsp;| U' : '')."</option>";
            if(isset($q['children'])){
                foreach($q['children'] as $q1){
                    $selected = ($q1['category_child_id'] == $category_id) ? "selected=\"selected\"" : "";
                    if( $selected == "" && @$selected_categories[$q1['category_child_id']] == "1") {
                        $selected = "selected=\"selected\"";
                    }
                    echo "<option $selected value=".$q1['category_child_id'].">\n";
                    echo "&#151;&#151;|2|";
                    echo "&nbsp;" . $q1['category_name'] . "".(($q['category_publish'] == 'N') ? '&nbsp;&nbsp;&nbsp;| U' : '')."</option>";

                    if(isset($q1['children'])){
                        foreach($q1['children'] as $q2){
                            $selected = ($q2['category_child_id'] == $category_id) ? "selected=\"selected\"" : "";
                            if( $selected == "" && @$selected_categories[$q2['category_child_id']] == "1") {
                                $selected = "selected=\"selected\"";
                            }
                            echo "<option $selected value=".$q2['category_child_id'].">\n";
                            echo "&#151;&#151;&#151;|3|";
                            echo "&nbsp;" . $q2['category_name'] . "".(($q['category_publish'] == 'N') ? '&nbsp;&nbsp;&nbsp;| U' : '')."</option>";
                        }
                    }

                }
            }

}

    }
    /**
     * Returns the category name of the first category product $product_id is assigned
     *
     * @param int $product_id
     * @return string The categotry name
     */
    function get_name($product_id) {
        $db = new ps_DB;

        $q = "SELECT #__{vm}_category.category_id, category_name FROM #__{vm}_category,#__{vm}_product_category_xref ";
        $q .= "WHERE product_id='$product_id' ";
        $q .= "AND #__{vm}_category.category_id = #__{vm}_product_category_xref.category_id ";
        $db->setQuery($q);   $db->query();

        $db->next_record();

        return $db->f("category_name");
    }

    function get_name2($category_id) {
        $db = new ps_DB;
        $q = "SELECT category_id, category_name FROM #__{vm}_category ";
        $q .= "WHERE category_id='$category_id' AND category_name!='Checkout Specials' ";
        $db->setQuery($q);
        $db->query();
        $db->next_record();
        return $db->f("category_name");
    }

    function get_category_fon($category_id) {
        $db = new ps_DB;
        $q = "SELECT background FROM #__{vm}_category_header_img ";
        $q .= "WHERE category_id='$category_id' ";
        $db->setQuery($q);
        $db->query();
        $db->next_record();
        return $db->f("background");
    }
    /**
     * Returns the category ID of the first category
     * assigned to the given product ID
     * @param int $product_id The product id
     * @return int The category id
     */
    function get_cid($product_id) {
        $db = new ps_DB;

        $q = "SELECT #__{vm}_category.category_id FROM #__{vm}_category,#__{vm}_product_category_xref ";
        $q .= "WHERE product_id='$product_id' ";
        $q .= "AND #__{vm}_category.category_id = #__{vm}_product_category_xref.category_id ";
        $db->query( $q );
        $db->next_record();

        return (int)$db->f('category_id');
    }

    /**
     * Returns the category description.
     * @author soeren
     * @param int $category_id
     * @return string The category description
     */
    function get_description($category_id) {
        global $city_obj;
        $db = new ps_DB;

        $q = "SELECT category_id, category_description,category_description_city FROM #__{vm}_category ";
        $q .= "WHERE category_id='$category_id' ";
        $db->setQuery($q);   $db->query();

        $db->next_record();
        $category_description = $db->f("category_description");
        if ($city_obj && $city_obj->city && $city_obj->state) {
            $category_description = str_replace('{state_name}',$city_obj->state,str_replace('{city_name}',$city_obj->city,$db->f("category_description_city")));
        }

        return $category_description;
    }

    /**
     * Checks for childs of the category $category_id
     *
     * @param int $category_id
     * @return boolean True when the category has childs, false when not
     */
    function has_childs($category_id) {
        $db = new ps_DB;
        if( empty( $GLOBALS['category_info'][$category_id]['has_childs'] )) {
            $q = "SELECT category_child_id FROM #__{vm}_category_xref ";
            $q .= "WHERE category_parent_id='$category_id' ";
            $db->setQuery($q);   $db->query();

            if ($db->num_rows() > 0)
                $GLOBALS['category_info'][$category_id]['has_childs'] = true;
            else
                $GLOBALS['category_info'][$category_id]['has_childs'] = false;
        }
        return $GLOBALS['category_info'][$category_id]['has_childs'];
    }
    /**
     * Prints a navigation list (=breadcrumbs) to be used in the pathway
     *
     * @param int $category_id
     */
    function navigation_list($category_id) {
        echo $this->get_navigation_list($category_id);
    }

    /**
     * Creates navigation list of categories
     * @author pablo
     * @author soeren
     * @param int $category_id
     */
    function get_navigation_list($category_id) {
        global $sess, $mosConfig_live_site;
        $db = new ps_DB;

        static $i=0;
        static $html = "";
        $q = "SELECT category_id, category_name,category_parent_id FROM #__{vm}_category, #__{vm}_category_xref WHERE ";
        $q .= "#__{vm}_category_xref.category_child_id='$category_id' ";
        $q .= "AND #__{vm}_category.category_id='$category_id'";
        $db->setQuery($q);   $db->query();
        $db->next_record();
        if ($db->f("category_parent_id")) {
            $link = "<a class=\"pathway\" href=\"";
            $link .= $sess->url($_SERVER['PHP_SELF'] . "?page=shop.browse&amp;category_id=$category_id");
            $link .= "\">";
            $link .= $db->f("category_name");
            $link .= "</a>";
            $category_list[$i++] = " ".$this->pathway_separator()." ". $link;
            $this->get_navigation_list($db->f("category_parent_id"));
        }
        else {
            $link = "<a class=\"pathway\" href=\"";
            $link .= $sess->url($_SERVER['PHP_SELF'] . "?page=shop.browse&amp;category_id=$category_id");
            $link .= "\">";
            $link .= $db->f("category_name");
            $link .= "</a>";
            $category_list[$i++] = $link;

        }
        while (list(, $value) = each($category_list)) {
            $html .= $value;
        }

        return $html;
    }

    function checkProductIsFlowerType($product_id) {
        $db = new ps_DB;
        $q = "SELECT product_id,jvco.category_id,jvcx.category_parent_id  from jos_vm_product_category_xref jvpcx 
left join jos_vm_category_options jvco on jvco .category_id = jvpcx .category_id and jvco.category_type = 1 
left join jos_vm_category_xref jvcx on jvcx.category_child_id = jvpcx.category_id 
WHERE (jvcx.category_child_id  =18 || jvcx.category_parent_id = 18) and jvpcx.product_id = ".$product_id;
        $db->setQuery($q);
        $db->query();
        $db->next_record();

        if ($db->f("product_id")) {
            return true;
        }

        return false;
    }
    /**
     * Changes the category List Order
     * @author soeren
     *
     * @param unknown_type $d
     * @return unknown
     */
    function reorder( &$d ) {
        global $db;

        if( !empty( $d['category_id'] )) {
            $cid = $d['category_id'][0];

            switch( $d["task"] ) {
                case "orderup":
                    $q = "SELECT list_order,category_parent_id FROM #__{vm}_category,#__{vm}_category_xref ";
                    $q .= "WHERE category_id='".$cid[0]."' ";
                    $q .= "AND category_child_id='".$cid[0]."' ";
                    $db->query($q);
                    $db->next_record();
                    $currentpos = $db->f("list_order");
                    $category_parent_id = $db->f("category_parent_id");

                    // Get the (former) predecessor and update it
                    $q = "SELECT list_order,#__{vm}_category.category_id FROM #__{vm}_category, #__{vm}_category_xref ";
                    $q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_parent_id' ";
                    $q .= "AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id ";
                    $q .= "AND list_order='". intval($currentpos - 1) . "'";
                    $db->query($q);
                    $db->next_record();
                    $pred = $db->f("category_id");

                    // Update the category and decrease the list_order
                    $q = "UPDATE #__{vm}_category ";
                    $q .= "SET list_order=list_order-1 ";
                    $q .= "WHERE category_id='".$cid[0]."'";
                    $db->query($q);

                    $q = "UPDATE #__{vm}_category ";
                    $q .= "SET list_order=list_order+1 ";
                    $q .= "WHERE category_id='$pred'";
                    $db->query($q);

                    break;

                case "orderdown":
                    $q = "SELECT list_order,category_parent_id FROM #__{vm}_category,#__{vm}_category_xref ";
                    $q .= "WHERE category_id='".$cid[0]."' ";
                    $q .= "AND category_child_id='".$cid[0]."' ";
                    $db->query($q);
                    $db->next_record();
                    $currentpos = $db->f("list_order");
                    $category_parent_id = $db->f("category_parent_id");

                    // Get the (former) successor and update it
                    $q = "SELECT list_order,#__{vm}_category.category_id FROM #__{vm}_category, #__{vm}_category_xref ";
                    $q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_parent_id' ";
                    $q .= "AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id ";
                    $q .= "AND list_order='". intval($currentpos + 1) . "'";
                    $db->query($q);
                    $db->next_record();
                    $succ = $db->f("category_id");

                    $q = "UPDATE #__{vm}_category ";
                    $q .= "SET list_order=list_order+1 ";
                    $q .= "WHERE category_id='".$cid[0]."' ";
                    $db->query($q);

                    $q = "UPDATE #__{vm}_category ";
                    $q .= "SET list_order=list_order-1 ";
                    $q .= "WHERE category_id='$succ'";
                    $db->query($q);

                    break;
                case "saveorder":
                    $i = 0;
                    foreach( $d['category_id'] as $category_id ) {
                        if( !is_numeric( $d['order'][$i] ) ) {
                            $d['error'] = "Error: Please use numbers only for ordering the list!";
                            return false;
                        }
                        $i++;
                    }
                    $i = 0;
                    foreach( $d['category_id'] as $category_id ) {
                        $q = "UPDATE #__{vm}_category ";
                        $q .= "SET list_order= ".$d['order'][$i];
                        $q .= " WHERE category_id='".$category_id."' ";
                        $db->query($q);
                        $i++;
                    }
                    break;
            }
        }
        return true;
    }
         
    private function createCategoryTree ($categories, $category_child_id = 0) {
        $branch = [];
        
        foreach ($categories as $category) {
            if ($category_child_id == $category->category_parent_id) {
                $child_branch = $this->createCategoryTree($categories, $category->category_id);
                
                if (array_key_exists($category->category_id, $branch) == false) {
                    $branch[$category->category_id] = [];
                }

                if (sizeof($child_branch) > 0) {
                    $branch[$category->category_id] = $child_branch;
                }
                
                $branch[$category->category_id]['info'] = $category;
            }
        }
        
        return $branch;
    }

    
    public function getCategories($keyword = false) {
        global $database;
        
        $query = "SELECT
            `c`.`category_id`,
            `c`.`category_name`,
            `c`.`category_description`,
            `c`.`category_publish`,
            `cc_x`.`category_parent_id`
        FROM `jos_vm_category` AS `c`
        INNER JOIN `jos_vm_category_xref` AS `cc_x`
            ON
            `cc_x`.`category_child_id`=`c`.`category_id`
        ";
        
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        
        $categories_tree = $this->createCategoryTree($rows);
        
        return $categories_tree;
    }

}
?>
