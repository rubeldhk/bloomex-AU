<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: vmAbstractObject.class.php,v 1.1 2005/10/27 16:09:13 soeren_nb Exp $
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
 * The abstract class for all virtuemart entities
 * @abstract 
 * @author soeren
 */
class vmAbstractObject {
	/**
	 * Abstract function for validating input values before adding an item
	 * @abstract 
	 * @param array $d
	 * @return boolean True on success, false on failure
	 */
	function validate_add( &$d ) {
		return true;
	}
	/**
	 * Abstract function for validating input values before updating an item
	 * @abstract 
	 * @param array $d
	 * @return boolean True on success, false on failure
	 */
	function validate_update( &$d ) {
		return true;
	}
	/**
	 * Abstract function for validating input values before deleting an item
	 * @abstract 
	 * @param array $d
	 * @return boolean True on success, false on failure
	 */
	function validate_delete($id = 0, &$d ) {
		return true;
	}
	/**
	 * Prepare the change of the pulish state of an item
	 *
	 * @param array $d The REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function get_child_categories_list($child_categories=''){
        global  $database;
        if($child_categories){

                    $get_child_categories = "SELECT group_concat(category_child_id) as child_list
                                FROM `jos_vm_category_xref`
                                WHERE `category_parent_id` in (".$child_categories.")";

                    $database->setQuery($get_child_categories);
                    $res =  $database->loadResult();
                    return $res;
        }else{
            return false;
        }


    }
    function get_product_publish_unpublish($product_id){
        global  $database;
        $sql = "SELECT product_publish
                                FROM `jos_vm_product`
                                WHERE `product_id` LIKE ".$product_id."";
        $database->setQuery($sql);
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


	function handlePublishState( $d ) {
		global $vmLogger, $database,$mosConfig_live_site,$my;

        if ($mosConfig_live_site == 'https://bloomex.com.au') {
            $category_change_table = "jos_vm_category_history_live";
            $product_change_table = "jos_vm_product_history_live";
        } else {
            $category_change_table = "jos_vm_category_history_stage";
            $product_change_table = "jos_vm_product_history_stage";
        }
		if( !empty($d['product_id'])) {
				$table_name = "#__{vm}_product";
				$publish_field_name = 'product_publish';
				$field_name = 'product_id';

            $product_publish_unpublish_old = $this->get_product_publish_unpublish($d["product_id"]);
            $product_change_sql = "INSERT INTO `" . $product_change_table . "`
                                (`product_id`, `name`, `old`, `new`, `username`, `date`) VALUES (".$d["product_id"].", 'product_publish', '".$database->getEscaped($product_publish_unpublish_old)."', '".($d["task"] == 'unpublish' ? 'N' : 'Y')."', '".$my->username."', DATE_SUB(NOW(), INTERVAL 4 HOUR))";
            $database->setQuery($product_change_sql);
            $database->query();



		}
		elseif( !empty($d['category_id'])) {

            $query_select_old_data = "SELECT category_name,category_description,meta_info,alias,category_publish FROM jos_vm_category WHERE  category_id='" . $d["category_id"] . "'";
            $database->setQuery( $query_select_old_data );
            $olddata=$database->loadAssocList();
            $newdata=array();
            $newdata=$olddata[0];
            $newdata['category_publish']=($d["task"] == 'unpublish' ? 'N' : 'Y');
            if($olddata && $newdata){
                mosChangesNotification('category_update',$olddata[0],$newdata,$d["category_id"]);
            }

            $table_name = "#__{vm}_category";
                    $publish_field_name = 'category_publish';
                    $field_name = 'category_id';
 
                    $child_categories = $d["category_id"];
                   if($d["task"]=='unpublish'){
                        $res = $this->get_child_categories_list($child_categories);
                        if($res){
                            $child_categories=$res.','.$child_categories;
                            $second_child_categories = $this->get_child_categories_list($child_categories);
                            if($second_child_categories){
                                $child_categories =$second_child_categories.','.$child_categories;
                            }

                        }
                    }
                            $child_categories_arr = array_unique(explode(",", $child_categories));
                            foreach ($child_categories_arr as $k=>$child_category_id) {
                                $category_child_publish_unpublish_old = $this->get_category_publish_unpublish($child_category_id);
                                $category_change_imports_child[] = "(" . $child_category_id . ", 'category_publish', '".$category_child_publish_unpublish_old."', '".($d["task"] == 'unpublish' ? 'N' : 'Y')."', '" . $my->username . "', DATE_SUB(NOW(), INTERVAL 4 HOUR))";
                            }
                            $category_change_sql_child = "INSERT INTO `" . $category_change_table . "`
                                (`category_id`, `name`, `old`, `new`, `username`, `date`) VALUES " . implode(',', $category_change_imports_child) . "";
                            $database->setQuery($category_change_sql_child);
                            $database->query();

                    $query_child_category = "UPDATE `jos_vm_category` AS `c`
                            SET
                                `c`.`category_publish`='".($d["task"] == 'unpublish' ? 'N' : 'Y')."'
                            WHERE 
                                `c`.`category_id` in (".$child_categories.")
                            ";
                    $database->setQuery($query_child_category);
                    $database->query();

            $get_query_child_category = "SELECT group_concat(category_name) as updated_child_categories FROM  `jos_vm_category` 
                            WHERE `category_id` in (".$child_categories.")";
            $database->setQuery($get_query_child_category);
            $get_child_category = $database->loadResult();
            $vmLogger->info('List Categories : '.$get_child_category);

                    //MENU UPDATE
                    $query = "UPDATE `jos_menu` AS `m`
                    SET
                        `m`.`published`='".($d["task"] == 'unpublish' ? '0' : '1')."'
                    WHERE 
                        `m`.`new_type`='vm_category'
                        AND
                        `m`.`link`='".$d["category_id"]."'
                    ";
                    
                    $database->setQuery($query);
                    $database->query();
                    //!MENU UPDATE
		}
		elseif( !empty( $d['payment_method_id'])) {
				$table_name = "#__{vm}_payment_method";
				$publish_field_name = 'payment_enabled';
				$field_name = 'payment_method_id';
		}
                elseif( !empty( $d['order_occasion_id'])) {
				$table_name = "#__{vm}_order_occasion";
				$publish_field_name = 'published';
				$field_name = 'order_occasion_id';
		}
		elseif( !empty( $d['order_status_id'])) {
            $table_name = "#__{vm}_order_status";
            $publish_field_name = 'publish';
            $field_name = 'order_status_id';
        }
		else {
			$vmLogger->err( 'Could not determine the item type that is to be (un)published.');
			return false;
		}
		
		return $this->changePublishState( $d[$field_name], $d['task'], $table_name, $publish_field_name, $field_name );
		
	}
	/**
	 * Updates the $publish_field_name of the item(s) $itemId to Y or N ($task)
	 * in the table $table_name for field $field_name
	 *
	 * @param int/array $itemId (A single integer is later converted into an array)
	 * @param string $task Either 'publish' or 'unpublish'
	 * @param string $table_name
	 * @param string $publish_field_name
	 * @param string $field_name
	 * @return boolean
	 */
	function changePublishState( $itemId, $task, $table_name, $publish_field_name, $field_name ) {
		global $vmLogger;
		
		$db = new ps_DB();
		$value = ($task == 'unpublish') ? 'N' : 'Y';
        if($field_name=='order_occasion_id' OR $field_name=='order_status_id'){
                   $value = ($task == 'unpublish') ? '0' : '1'; 
                }
		if( !is_array( $itemId )) {
			$set[] = $itemId;
		}
		else {
			$set =& $itemId;
		}
		$set = implode( ',', $set );
		
		$q = "UPDATE `$table_name` SET `$publish_field_name` = '$value' ";

		$q .= "WHERE FIND_IN_SET( `$field_name`, '$set' )";
                if($field_name !='order_occasion_id' && $field_name !='order_status_id'){
		$q .= " AND `vendor_id`=".$_SESSION['ps_vendor_id'];
                }

		$db->query( $q );
		
		$vmLogger->info($field_name.'(s) '.$set.' was/were '.$task.'ed.' );
		
		return true;
	}
}
