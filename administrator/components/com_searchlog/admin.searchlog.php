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


$act			= mosGetParam( $_REQUEST, "act", "" );
$cid 			= josGetArrayInts( 'cid' );
$step			= 0;

switch ($task) {
	default:
		showSearchLog( $option );
		break;
}

//=================================================== SearchLog OPTION ===================================================
function showSearchLog( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_key 	= trim(mosGetParam( $_POST, "filter_key", '' ));
		
	$where 	= "";
	$aWhere	= array();
	
	if( $filter_key ) {
		$aWhere[]	= " search_word LIKE '%$filter_key%' ";
	}
	
	if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);
	
	// get the total number of records
	$query = "SELECT id FROM tbl_track_search_queries $where group by search_word ";
	$database->setQuery( $query );
	$total = count($database->loadObjectList());

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT *,COUNT(search_word) as search_count FROM tbl_track_search_queries $where group by search_word ORDER BY search_count DESC ";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();


	HTML_SearchLog::showSearchLog( $rows, $pageNav, $option, $filter_key );
}



?>