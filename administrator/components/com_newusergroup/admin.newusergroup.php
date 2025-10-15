<?php
/**
* @version $Id: admin.banners.php 4556 2006-08-18 18:29:18Z stingrey $
* @package Joomla
* @subpackage NewUserGroups
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ensure user has access to this function
/*if (!($acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' )| $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'com_newusergroup' ))) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}
*/
require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );

$cid = josGetArrayInts( 'cid' );

switch ($task) {	
	// BANNER EVENTS

	case 'new':
		editNewUserGroup( null, $option );
		break;

	case 'cancel':
		cancelEditNewUserGroup();
		break;

	case 'save':
		saveNewUserGroup( $task );
		break;

	case 'edit':
		editNewUserGroup( $cid[0], $option );
		break;

	case 'editA':
		editNewUserGroup( $id, $option );
		break;

	case 'remove':
		removeNewUserGroup( $cid );
		break;


	default:
		viewNewUserGroups( $option );
		break;
}


function viewNewUserGroups( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_new_user_group";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT * FROM tbl_new_user_group";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_NewUserGroups::showNewUserGroups( $rows, $pageNav, $option );
}

function editNewUserGroup( $id, $option ) {
	global $database, $my;
	$lists = array();

	$row = new mosNewUserGroup($database);
	$row->load( (int)$id );

	HTML_NewUserGroups::NewUserGroupForm( $row, $option );
}

function saveNewUserGroup( $task ) {
	global $database;

	$row 		= new mosNewUserGroup($database);
	$aAreaName	= mosGetParam( $_REQUEST, "area_name" );
	$sAreaName	= implode( "[--1--]", $aAreaName );
	
	$msg = 'Saved User Group info';
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$row->area_name = $sAreaName;
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( 'index2.php?option=com_newusergroup', $msg );
}

function cancelEditNewUserGroup() {
	global $database;

	$row = new mosNewUserGroup($database);
	$row->bind( $_POST );
	$row->checkin();

	mosRedirect( 'index2.php?option=com_newusergroup' );
}

function removeNewUserGroup( $cid ) {
	global $database;
	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM tbl_new_user_group WHERE id IN ( $cids )";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}
	mosRedirect( 'index2.php?option=com_newusergroup' );
}

?>