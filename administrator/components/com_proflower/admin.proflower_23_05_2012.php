<?php
/**
* @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );


$act			= mosGetParam( $_REQUEST, "act", "" );
$cid 			= josGetArrayInts( 'cid' );
$step			= 0;

switch ($task) {		
	case 'search':
	default:
		showProFlower( $option );
		break;
}


//=================================================== POSTAL CODE OPTION ===================================================
function showProFlower( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$text_filter	= mosGetParam( $_REQUEST, "text_filter", "" );	
	$status_filter	= mosGetParam( $_REQUEST, "status_filter", "" );
	$orderby_filter	= mosGetParam( $_REQUEST, "orderby_filter", "O.cdate DESC" );	
	
	$aWhere			= array();
	if( $text_filter ) {
		 $aWhere[]	= "( PF.proflower_id LIKE '%$text_filter%' OR  O.order_id LIKE '%$text_filter%' ) ";
	}
	
	if( $status_filter ) {
		 $aWhere[]	= " O.order_status = '$status_filter' ";
	}	
	$sWhere	= count($aWhere) ? " WHERE " . implode( " AND ", $aWhere ) : "";
	
	// get the total number of records
	$query = "SELECT COUNT(*) AS total FROM #__vm_order_proflower AS PF, #__vm_orders AS O 
			  WHERE PF.order_id = O.order_id $sWhere  ORDER BY O.cdate DESC";
	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT *, O.mdate FROM #__vm_order_proflower AS PF INNER JOIN #__vm_orders AS O ON PF.order_id = O.order_id 
			  LEFT JOIN #__vm_warehouse AS W ON O.warehouse = W.warehouse_code 
			  LEFT JOIN #__vm_order_status AS OS ON OS.order_status_code = O.order_status $sWhere  ORDER BY $orderby_filter";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	
	$aList				= array();	
	$types[] 			= mosHTML::makeOption( "O.cdate DESC", "- - - - - - SELECT - - - - - -" );
	$types[] 			= mosHTML::makeOption( "PF.order_id", "Proflower Order Id - ASC" );
	$types[] 			= mosHTML::makeOption( "PF.order_id DESC", "Proflower Order Id - DESC" );
	$types[] 			= mosHTML::makeOption( "O.order_id", "Bloomex Order Id - ASC" );
	$types[] 			= mosHTML::makeOption( "O.order_id DESC", "Bloomex Order Id - DESC" );
	$types[] 			= mosHTML::makeOption( "O.order_status", "Status of the order - ASC" );
	$types[] 			= mosHTML::makeOption( "O.order_status DESC", "Status of the order - DESC" );	
	$aList['orderby'] 	= mosHTML::selectList( $types, 'orderby_filter', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $orderby_filter );
	
	
	$query 				= "SELECT * FROM #__vm_order_status ORDER BY order_status_name";
	$database->setQuery( $query );
	$orderStatus 		= $database->loadObjectList();
	
	unset($types);
	$types[] 			= mosHTML::makeOption( "", "- - - - - SELECT ORDER STATUS - - - - -" );
	$sOrderStatus		= "";
	if( count($orderStatus) ) {
		foreach ($orderStatus as $items) {
			$types[] = mosHTML::makeOption( $items->order_status_code, $items->order_status_name );
		}
	}
	$aList['status_filter']	= mosHTML::selectList( $types, 'status_filter', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $status_filter );
	
		
	HTML_ProFlower::showProFlower( $rows, $pageNav, $option, $aList );
}

?>
