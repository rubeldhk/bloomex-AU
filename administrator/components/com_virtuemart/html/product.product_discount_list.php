<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_discount_list.php,v 1.5 2005/09/29 20:02:18 soeren_nb Exp $
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

$aWhere			= array();
if(isset($_REQUEST["search_discount_type"]) AND $_REQUEST["search_discount_type"] == "amount" ) {
	$search_discount_type	= " discount_type ='amount' OR discount_type IS NULL ";
}elseif (isset($_REQUEST["search_discount_type"])) {
	$search_discount_type	= " discount_type ='".$_REQUEST["search_discount_type"]."' ";
}

if(isset($_REQUEST["search_discount_type"])) 	$aWhere['search_discount_type']		=  $search_discount_type;
if( $keyword ) 						$aWhere['keyword']					=  "(start_date LIKE '%$keyword%' OR end_date LIKE '%$keyword%' OR amount LIKE '%$keyword%' ) ";

if( count($aWhere) ) $sWhere	= " WHERE " . implode( " AND ", $aWhere);


$list  = "SELECT * FROM #__{vm}_product_discount ".(isset($sWhere) ? $sWhere : '')." ";
$list .= "ORDER BY discount_id DESC ";
$list .= "LIMIT $limitstart, " . $limit;
$count = "SELECT count(*) as num_rows FROM #__{vm}_product_discount ".(isset($sWhere) ? $sWhere : '')." ";

$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_LIST_LBL, IMAGEURL."ps_image/percentage.png", $modulename, "product_discount_list");
echo '<div align="right" style="margin:0px 0px 15px 0px;">
		<b>Discount Type:</b>&nbsp;
		<select name="search_discount_type" onchange="document.adminForm.submit();">
			<option value="">------------------ All ------------------</option>';
			
if( $_REQUEST["search_discount_type"] == "amount" ) {
	echo '<option value="amount" selected>Amount</option>';
}else {
	echo '<option value="amount">Amount</option>';
}	
		
if( $_REQUEST["search_discount_type"] == "coupon" ) {
	echo '<option value="coupon" selected>Coupon Code</option>';
}else {
	echo '<option value="coupon">Coupon Code</option>';
}
echo '</select></div>';	

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_AMOUNT => '',
					$VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_AMOUNTTYPE => '',
					$VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_STARTDATE => '',
					$VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ENDDATE => '',
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
	$listObj->addCell( mosHTML::idBox( $i, $db->f("discount_id"), false, "discount_id" ) );

	$sValue	= $db->f("is_percent")=='1' ? $db->f("amount")."%" : "$" . $db->f("amount");
	$sValue	= $db->f("discount_type")=='coupon' ? $db->f("coupon_code") . " (" . $sValue . ")" :  $sValue;
	
	$tmp_cell = '<a href="'.$sess->url( $_SERVER['PHP_SELF'].'?page=product.product_discount_form&discount_id='.$db->f("discount_id") ).'">'.$sValue.' </a>';	
	$listObj->addCell( $tmp_cell );
	
	
	if ($db->f("discount_type") == "coupon") {
		$discount_type = "Coupon Code";		
	}else {
		$discount_type = "Amount";
	}
	$tmp_cell = $db->f("is_percent")=='1' ? $discount_type . " - " . $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ISPERCENT : $discount_type .  " - " . $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ISTOTAL;
	$listObj->addCell( $tmp_cell );
	
	if($db->f("start_date")) 
		$tmp_cell = strftime("%Y-%m-%d", $db->f("start_date"));
	else
		$tmp_cell = "-";
	$listObj->addCell( $tmp_cell );
	
    if($db->f("end_date")) 
		$tmp_cell = strftime("%Y-%m-%d", $db->f("end_date"));
	else
		$tmp_cell = "-";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $ps_html->deleteButton( "discount_id", $db->f("discount_id"), "discountDelete", $keyword, $limitstart ) );

	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );

?>