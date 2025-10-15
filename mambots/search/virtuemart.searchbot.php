<?php
/**
* VirtueMart Search Bot
* @version 1.0
* @package VirtueMart
* @copyright (C) Copyright 2004-2005 by Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/** Register search function inside Mambo's API */
$_MAMBOTS->registerFunction( 'onSearch', 'botSearchVM' );

/**
* Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
*/
function botSearchVM( $text, $phrase='', $ordering='' ) {
  global $database;
  $text = trim( $text );
  if ($text == '') {
    return array();
  }
 	$text	= $database->getEscaped($text);
	$wheres = array();
	switch ($phrase) {
		case 'exact':
			$wheres2 = array();
			$wheres2[] = "LOWER(product_name) LIKE '%$text%'";
			$wheres2[] = "LOWER(product_sku) LIKE '%$text%'";
			$wheres2[] = "LOWER(product_desc) LIKE '%$text%'";
			$wheres2[] = "LOWER(product_s_desc) LIKE '%$text%'";
			$wheres2[] = "LOWER(product_url) LIKE '%$text%'";
			$where = '(' . implode( ') OR (', $wheres2 ) . ')';
			break;
		case 'all':
		case 'any':
		default:
			$words = explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word) {
				$wheres2 = array();
				$wheres2[] = "LOWER(product_name) LIKE '%$text%'";
				$wheres2[] = "LOWER(product_sku) LIKE '%$text%'";
				$wheres2[] = "LOWER(product_desc) LIKE '%$text%'";
				$wheres2[] = "LOWER(product_s_desc) LIKE '%$text%'";
				$wheres2[] = "LOWER(product_url) LIKE '%$text%'";
				$wheres[] = implode( ' OR ', $wheres2 );
			}
			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}

	switch ($ordering) {
		case 'newest':
		default:
			$order = '#__vm_product.cdate DESC';
			break;
		case 'oldest':
			$order = '#__vm_product.cdate ASC';
			break;
		case 'popular':
			$order = '#__vm_product.product_name ASC';
			break;
		case 'alpha':
			$order = '#__vm_product.product_name ASC';
			break;
		case 'category':
			$order = '#__vm_category.category_name ASC';
			break;
	}
    
  
  $database->setQuery( " SELECT id, name FROM  `#__menu` WHERE link LIKE '%com_virtuemart%' AND published=1 AND access=0");
  $database->loadObject( $Item );
  $ItemName = !empty( $Item->name ) ? $Item->name : "Shop"; 
  $ItemName	= $database->getEscaped($ItemName);
  $Itemid = !empty( $Item->id ) ? $Item->id : "1";

  $query = "SELECT  product_name as title,"
               . "\n    FROM_UNIXTIME( #__vm_product.cdate, '%Y-%m-%d %H:%i:%s'  ) AS created," 
               . "\n    product_s_desc AS text,"
               . "\n    CONCAT('$ItemName/',#__vm_category.category_name) as section,"
               
               . "\n    CONCAT('index.php?option=com_virtuemart&page=shop.product_details&flypage=',#__vm_category.category_flypage,'&category_id=',#__vm_category.category_id,'&product_id=', #__vm_product.product_id, '&Itemid=".$Itemid."' ) as href,"
               . "\n    '2' as browsernav"
               . "\n FROM #__vm_product"
               . "\n LEFT JOIN `#__vm_product_category_xref` ON `#__vm_product_category_xref`.`product_id` = `#__vm_product`.`product_id`"
               . "\n LEFT JOIN `#__vm_category` ON `#__vm_product_category_xref`.`category_id` = `#__vm_category`.`category_id`"

               . "\n WHERE $where"
			   . "\n AND (product_parent_id='' OR product_parent_id='0')"
			   . "\n AND product_publish='Y'"
               . "\n  GROUP BY #__vm_product.product_id ORDER BY $order" ;
               
  $database->setQuery( $query );

  $row = $database->loadObjectList();
  
  return $row;
}

?>
