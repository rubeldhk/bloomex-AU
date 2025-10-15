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

//die($act);
switch ($act) {		
	
	default:
		switch ($task) {		
			case 'new':
				editPartners( '0', $option);
				break;
		
			case 'edit':
				editPartners( intval( $cid[0] ), $option );
				break;
		
			case 'editA':
                                $partner_id			= mosGetParam( $_REQUEST, "partner_id", "" );
				editPartners( $partner_id, $option );
				break;
		
			case 'save':
				savePartners( $option );
				break;
		
			case 'remove':
				removePartners( $cid, $option );
				break;
				
			case 'cancel':
				cancelPartners();
				break;
		
			default:
				showPartners( $option );
				break;
		}
		break;
	
}



//=================================================== LandingPages OPTION ===================================================
function showPartners( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	
	$filter_key 	= trim(mosGetParam( $_POST, "filter_key" ));
		
	$where 	= "";
	$aWhere	= array();
	
	
	if( $filter_key ) {
		$aWhere[]	= " (partner_name LIKE '%$filter_key%' OR partner_email LIKE '%$filter_key%') ";
	}
	
	if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_local_parthners $where";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_local_parthners $where ORDER BY partner_name ASC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	$lists 	= array();	
	$types	= array();
		
	$lists['filter_key']	= $filter_key;

	HTML_Partners::showPartners( $rows, $pageNav, $option, $lists );
}


function editPartners( $partner_id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosPartners( $database );
	// load the row from the db table
	$row->load( (int)$partner_id );

	if (!$partner_id) {
		$row->partner_name		= "";
		$row->partner_email					= "";
                $row->partner_phone					= "";
		$row->note					= "";
                $row->partner_price					= "";
		
	}
	
	$lists = array();
	$types	= array();
	
		
	HTML_Partners::editPartners( $row, $option, $lists );
}

function get_file_extension($file_name) {
  return substr(strrchr($file_name,'.'),1);
}

function savePartners( $option ) {
	global $database, $mosConfig_absolute_path, $act;
	
	$row = new mosPartners( $database );
	
		
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save Partner Successfully" );
}


function removePartners( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_local_parthners WHERE partner_id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option", "Remove Partner Successfully" );
}


function cancelPartners() {
	mosRedirect('index2.php?option=com_partners');
}
