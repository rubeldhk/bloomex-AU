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
	//=============================================================================================

	//=============================================================================================
	case "postal_code":
		switch ($task) {		
			case 'new':
				editPostalCode( '0', $option);
				break;
		
			case 'edit':
				editPostalCode( intval( $cid[0] ), $option );
				break;
		
			case 'editA':				
				editPostalCode( $id, $option );
				break;
		
			case 'save':
				savePostalCode( $option );
				break;
		
			case 'remove':
				removePostalCode( $cid, $option );
				break;
		
			case 'publish':
				changePostalCode( $cid, 1, $option );
				break;
		
			case 'unpublish':
				changePostalCode( $cid, 0, $option );
				break;
				
			case 'orderup':
				orderPostalCode( intval( $cid[0] ), -1, $option );
				break;
		
			case 'orderdown':
				orderPostalCode( intval( $cid[0] ), 1, $option );
				break;
				
			case 'cancel':
				cancelPostalCode();
				break;
		
			default:
				showPostalCode( $option );
				break;
		}
		break;
		
	//=============================================================================================
		
	//=============================================================================================
	default:
		break;
	
}


//=================================================== POSTAL CODE OPTION ===================================================
function showPostalCode( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_options WHERE type='postal_code'";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_options WHERE type='postal_code' ORDER BY ordering";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_postDeliver::showpPostalCode( $rows, $pageNav, $option );
}


function editPostalCode( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mospostDeliver( $database );
	// load the row from the db table
	$row->load( (int)$id );
	
	$aOptions 			= explode( "[--1--]", $row->options );
	$undeliver			= $aOptions[3];

	if (!$id) {
		$row->ordering 	= 0;
		$row->published = 1;
		$undeliver		= 1;
	}
	
	if( $undeliver == null ) $undeliver		= 1;
	
	
	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );
	$lists['undeliver']	= mosHTML::yesnoRadioList( "undeliver", "", $undeliver, "No", "Yes" );
	
	HTML_postDeliver::editPostalCode( $row, $option, $lists );
}


function savePostalCode( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mospostDeliver( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$location_name	= mosGetParam( $_POST, "location_name", "" );
	$deliver_day	= mosGetParam( $_POST, "deliver_day", "" );
	$price			= mosGetParam( $_POST, "price", "" );
	$undeliver		= mosGetParam( $_POST, "undeliver", "" );
	
	$row->type 		= "postal_code";
	$row->options 	= $location_name."[--1--]".$deliver_day."[--1--]".$price."[--1--]".$undeliver;	
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$row->updateOrder( "type='postal_code'" );
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save Postal Code Successfully" );
}


function removePostalCode( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_options WHERE id = $value AND type='postal_code'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Remove Postal Code Successfully" );
}


function changePostalCode( $cid=null, $state=0, $option ) {
	global $database, $my, $act;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $state ? 'publish' : 'unpublish';
		mosErrorAlert( "Select an item to $action" );
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE tbl_options SET published = " . (int) $state . " WHERE ( $cids )";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( $state ) {
		$msg	= "Publish Postal Code Successfully";
	}else {
		$msg	= "UnPublish Postal Code Successfully";
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", $msg );
}


function orderPostalCode( $uid, $inc, $option ) {
	global $database, $act;

	$row = new mospostDeliver( $database );
	$row->load( (int)$uid );
	$row->updateOrder();
	$row->move( $inc, "published >= 0 AND `type` = 'postal_code' " );
	$row->updateOrder();

	mosRedirect( "index2.php?option=$option&act=$act", "Reorder Postal Code Successfully" );
}


function cancelPostalCode() {
	mosRedirect('index2.php?option=com_postdeliver&act=postal_code');
}


//========================================================================================
function do_upload( $file, $dest_dir ) {
	global $clearUploads;
	
	if( $act ) {
		$act	= "&act=$act";
	}

	$format = substr( $file['name'], -3 );

	$allowable = array (
		//'bmp',
		//'csv',
		//'doc',
		//'epg',
		'gif',
		//'ico',
		'jpg',
		'jpeg',
		//'odg',
		//'odp',
		//'ods',
		//'odt',
		//'pdf',
		'png',
		//'ppt',
		//'swf',
		//'txt',
		//'xcf',
		//'xls'
	);

    $noMatch = 0;
	foreach( $allowable as $ext ) {
		if ( strcasecmp( $format, $ext ) == 0 ) {
			$noMatch = 1;
		}
	}
	
    if(!$noMatch){
		return false;
    }

    $sFileName	= strtolower(time().".$format");
	if ( !move_uploaded_file($file['tmp_name'], $dest_dir.$sFileName) ){
		return false;
	} 
	
	$clearUploads = true;
	return $sFileName;
}
?>
