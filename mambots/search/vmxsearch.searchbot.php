<?php
/**
* VirtueMart Extended Search Bot (based on work from Soeren Eberhardt & Pietro Gallo)
* @version 1.5
* @copyright (C) Copyright 2007 by Alejandro Kurczyn
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL

14/Feb/2007	A.Kurczyn	1.1		Fixed parent_filter var handling
15/Feb/2007	A.Kurczyn	1.2		Fixed review_from var handling
18/Feb/2007	A.Kurczyn	1.3		Added manufacturer,category,thumbnail support
29/Oct/2007	A.Kurczyn	1.5		MySQL 5 compatible, added keyword density
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Get VM vars

include_once($mosConfig_absolute_path."/administrator/components/com_virtuemart/virtuemart.cfg.php");

/** Register search function inside Mambo's API */
$_MAMBOTS->registerFunction( 'onSearch', 'vmExtendedSearch' );

/**
* Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
*/
function vmExtendedSearch( $text, $phrase='', $ordering='' ) {
  global $database;

  $text = trim( $text );
  if ($text == '') {
    return array();
  }
	
	$text	= $database->getEscaped($text);
// Get mambot parameters
	// check if param query has previously been processed
	if ( !isset($_MAMBOTS->_search_mambot_params['vmxsearch']) ) {
		// load mambot params info
		$query = "SELECT params FROM #__mambots WHERE element = 'vmxsearch.searchbot' AND folder = 'search'";
		$database->setQuery( $query );
		$database->loadObject($mambot);		
		
		// save query to class variable
		$_MAMBOTS->_search_mambot_params['vmxsearch'] = $mambot;
	}
	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['vmxsearch'];	
	$botParams = new mosParameters( $mambot->params );

	$fields = array();
	$thumbnail_flag = $botParams->def( 'thumbnail_flag', 1 );
	$thumbnailx = $botParams->def( 'thumbnailx', 40 );
	$thumbnaily = $botParams->def( 'thumbnaily', 40 );
	if ($botParams->def( 'name_flag', 1 ) == 1) $fields[] = 'p.product_name';
	if ($botParams->def( 'sku_flag', 1 ) == 1) $fields[] = 'p.product_sku';
	if ($botParams->def( 'desc_flag', 1 ) == 1) $fields[] = 'p.product_desc';
	if ($botParams->def( 'sdesc_flag', 1 ) == 1) $fields[] = 'p.product_s_desc';
	if ($botParams->def( 'url_flag', 1 ) == 1) $fields[] = 'p.product_url';
	if ($botParams->def( 'review_flag', 1 ) == 1) $fields[] = 'r.comment';
	if ($botParams->def( 'manufacturer_flag', 1 ) == 1) $fields[] = 'm.mf_name';
	if ($botParams->def( 'category_flag', 1 ) == 1) $fields[] = 'c.category_name';

	$oos_where = (PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != 1) ? 'and product_in_stock > 0' : '';
	switch ($botParams->def( 'parent_filter', 'both' )) {
		case 'parent':
		$parent_where = "AND (p.product_parent_id='' OR p.product_parent_id='0')";
		break;
		case 'child':
		$parent_where = "AND (p.product_parent_id > '0')";
		break;
		case 'both':
		$parent_where = '';
		break;
	}
	
// Build search logic
	$wheres = array();
	switch ($phrase) {
		case 'exact':
			$wheres2 = array();
			foreach ($fields as $field) {
				$wheres2[] = "$field LIKE '%$text%'";
			}
			$where = '(' . implode( ') OR (', $wheres2 ) . ')';
			break;
		case 'all':
		case 'any':
		default:
			$words = explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word) {
				$wheres2 = array();
				foreach ($fields as $field) {
					$wheres2[] = "$field LIKE '%$word%'";
				}
				$wheres[] = implode( ' OR ', $wheres2 );
			}
			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}

	switch ($ordering) {
		case 'newest':
		default:
			$order = 'p.cdate DESC';
			break;
		case 'oldest':
			$order = 'p.cdate ASC';
			break;
		case 'popular':
			$order = 'p.product_name ASC';
			break;
		case 'alpha':
			$order = 'p.product_name ASC';
			break;
		case 'category':
			$order = 'p.category_name ASC';
			break;
	}

	if ($botParams->def( 'density_flag', 1 ) == 1) {
		$whole_text = ", CONCAT_WS(' ',LOWER(".implode( '), LOWER(', $fields ).')) AS whole_text ';
	}
	else {
		$whole_text = '';
	}	

//Get Virtuemart module ID
  
	$database->setQuery( " SELECT id, name FROM  `#__menu` WHERE link LIKE '%com_virtuemart%' AND published=1 AND access=0");
	$database->loadObject( $Item );
	$ItemName = !empty( $Item->name ) ? $Item->name : "Shop";	
 	 $ItemName	= $database->getEscaped($ItemName);
	$Itemid = !empty( $Item->id ) ? $Item->id : "1";
	
	if ($thumbnail_flag == 1 ) {
		  $query = "SELECT CASE p.product_full_image WHEN '' THEN p.product_name ELSE CONCAT(p.product_name,'<img style=\"float:right\" src=\"../components/com_virtuemart/show_image_in_imgtag.php?filename=',p.product_full_image,'&newxsize=$thumbnaily\&newysize=$thumbnaily\&fileout=\">') END as title,";
	}
	else {
	  $query = "SELECT p.product_name as title,";
	}

    $query = $query . "\n    FROM_UNIXTIME( p.cdate, '%Y-%m-%d %H:%i:%s'  ) AS created," 
               . "\n    p.product_s_desc AS text,"
               . "\n    CONCAT('$ItemName/',c.category_name) as section,"
               . "\n    CONCAT('index.php?option=com_virtuemart&page=shop.product_details&flypage=',IFNULL(c.category_flypage,'".FLYPAGE."'),'&category_id=',IFNULL(c.category_id,''),'&product_id=',p.product_id,'&Itemid=',".$Itemid.") as href,"
               . "\n    '2' as browsernav ".$whole_text
               . "\n FROM #__vm_product p"
               . "\n LEFT JOIN #__vm_product_reviews r ON (r.product_id = p.product_id) "
               . "\n LEFT JOIN #__vm_product_mf_xref mx ON (mx.product_id = p.product_id) LEFT JOIN #__vm_manufacturer m ON (m.manufacturer_id = mx.manufacturer_id), "
               . "\n #__vm_product_category_xref cx, #__vm_category c"
               . "\n WHERE ($where)"
               . "\n AND cx.product_id = p.product_id"
               . "\n AND cx.category_id = c.category_id"
			   . "\n $parent_where"
			   . "\n AND p.product_publish='Y'"
			   . "\n $oos_where"
               . "\n  GROUP BY p.product_id  ORDER BY $order" ;
//echo "\n<!--QUERY:\n$query\n-->\n";
  $database->setQuery( $query );

  $row = $database->loadObjectList();
  
	if (!empty($row) && $botParams->def( 'density_flag', 1 ) == 1) {
		$txt = strtolower ($text);
		if ($phrase != 'exact')	$txt_array = explode(' ',$txt);
		else $txt_array[0] = $txt;
		$i = 0;
		foreach ($row as $result) {
			$count = 0;
			foreach ($txt_array as $txt) {
				$count = substr_count($result->whole_text, $txt) + $count;
			}
			$row[$i]->count = $count;
			$i++;
		}
		pg_keydenseSort($row,'count','DESC');
	}
	return $row;
	
}

function pg_keydenseSort(&$data, $key, $order)
{
	for ($i = count($data) - 1; $i >= 0; $i--)
	{
		$swapped = false;
		for ($j = 0; $j < $i; $j++)
		{
			if ($order == 'ASC')
			{
				if ($data[$j]->$key > $data[$j + 1]->$key)
				{
					$tmp = $data[$j];
					$data[$j] = $data[$j + 1];       
					$data[$j + 1] = $tmp;
					$swapped = true;
				}
			}
			else
			{
				if ($data[$j]->$key < $data[$j + 1]->$key)
				{
					$tmp = $data[$j];
					$data[$j] = $data[$j + 1];       
					$data[$j + 1] = $tmp;
					$swapped = true;
				}
			}

		}
		if (!$swapped) return;
	}
}
?>
