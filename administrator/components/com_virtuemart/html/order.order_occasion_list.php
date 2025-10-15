<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: order.order_status_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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
	$list  = "SELECT * FROM #__{vm}_order_occasion WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_order_occasion WHERE ";
	$q  = "(order_occasion_code LIKE '%$keyword%' ";
	$q .= "OR order_occasion_name LIKE '%$keyword%' ";
	$q .= "OR order_occasion_desc LIKE '%$keyword%' ";
	$q .= ") ";
	$q .= "ORDER BY list_order ASC";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$q = "";
	$list  = "SELECT * FROM #__{vm}_order_occasion WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_order_occasion WHERE";
	$q  = "(order_occasion_id <>'' ";
	$q .= ") ";
	$q .= "ORDER BY list_order ASC";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
//echo $count;
$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_ORDER_OCCATION_LIST_MNU, "", $modulename, "order_occasion_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_NAME => '',
					$VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_CODE => '',
					$VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_DESCRIPTION => '',
                                        $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_PUBLISHED => '',
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
	$listObj->addCell( mosHTML::idBox( $i, $db->f("order_occasion_id"), false, "order_occasion_id" ) );

	$tmp_cell = "<a href=\"".$sess->url($_SERVER['PHP_SELF'] . "?page=$modulename.order_occasion_form&limitstart=$limitstart&keyword=$keyword&order_occasion_id=".$db->f("order_occasion_id"))."\">".$db->f("order_occasion_name")."</a>";
	$listObj->addCell( $tmp_cell );
	
    $listObj->addCell( $db->f("order_occasion_code"));
    $listObj->addCell( $db->f("order_occasion_desc"));
     $tmpcell = "<a href=\"". $sess->url( $_SERVER['PHP_SELF']."?page=order.order_occasion_list&order_occasion_id=".$db->f("order_occasion_id")."&func=changePublishState" );
		if ($db->f("published")=='0') {
			$tmpcell .= "&task=publish\">";
		} 
		else { 
			$tmpcell .= "&task=unpublish\">";
		}
		$tmpcell .= vmCommonHTML::getYesNoIcon( ($db->f("published")==1)? 'Y':'N', "Publish", "Unpublish" );
		$tmpcell .= "</a>";

	 $listObj->addCell($tmpcell);
	
	$listObj->addCell( $ps_html->deleteButton( "order_occasion_id", $db->f("order_occasion_id"), "orderOccasionDelete", $keyword, $limitstart ) );

	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );
?>