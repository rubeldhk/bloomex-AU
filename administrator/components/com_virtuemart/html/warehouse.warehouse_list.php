<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: coupon.coupon_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
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
mm_showMyFileName( __FILE__ );

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

if (!empty($keyword)) {
  
	$list  = "SELECT * FROM #__{vm}_warehouse WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_warehouse WHERE ";
	$q  = "(warehouse_code LIKE '%$keyword%' OR ";
	$q .= "warehouse_name LIKE '%$keyword%' ";
	$q .= ") ";
	$q .= "ORDER BY warehouse_id ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else  {
	$list  = "SELECT * FROM #__{vm}_warehouse ";
	$list .= "ORDER BY warehouse_id ";
	$list .= "LIMIT $limitstart, " . $limit;
	$count = "SELECT count(*) as num_rows FROM #__{vm}_warehouse ";
}

$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");
  
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_WAREHOUSE_LIST_LBL, IMAGEURL."ps_image/warehouse.gif", $modulename, "warehouse_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_WAREHOUSE_LIST_CODE => '',
					$VM_LANG->_PHPSHOP_WAREHOUSE_LIST_NAME => '',
					$VM_LANG->_PHPSHOP_WAREHOUSE_LIST_EMAIL => '',
					$VM_LANG->_PHPSHOP_WAREHOUSE_LIST_TIMEZONE => '',
					$VM_LANG->_PHPSHOP_WAREHOUSE_LIST_LBL => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_PUBLISHED => '',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

$db->query($list);
$i = 0;
while ($db->next_record()) {

	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("warehouse_id"), false, "warehouse_id" ) );
    
	$tmp_cell = "<a href=\"". $sess->url($_SERVER['PHP_SELF']."?page=warehouse.warehouse_form&limitstart=$limitstart&keyword=$keyword&warehouse_id=" . $db->f("warehouse_id")) ."\">".$db->f("warehouse_code")."</a>";
	$listObj->addCell( $tmp_cell );
	
        $listObj->addCell( $db->f("warehouse_name"));
        $listObj->addCell( $db->f("warehouse_email"));
        $listObj->addCell( $db->f("timezone"));
        $listObj->addCell( $db->f("list_warehouse"));
        $listObj->addCell( $db->f("published")?"Yes":"No");

	$listObj->addCell( $ps_html->deleteButton( "warehouse_id", $db->f("warehouse_id"), "deleteWarehouse", $keyword, $limitstart ) );

	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );


?>