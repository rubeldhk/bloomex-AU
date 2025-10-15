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
$cid 		= josGetArrayInts( 'cid' );
$step		= 0;

switch ($task) {		
	case 'new':
		editTestimonial( '0', $option);
		break;

	case 'edit':
		editTestimonial( intval( $cid[0] ), $option );
		break;

	case 'editA':
		editTestimonial( $id, $option );
		break;

	case 'save':
		saveTestimonial( $option );
		break;

	case 'remove':
		removeTestimonial( $cid, $option );
		break;	
		
	case 'publish':
		changeTestimonial( $cid, 1, $option );
		break;

	case 'unpublish':
		changeTestimonial( $cid, 0, $option );
		break;
					
	case 'cancel':
		cancelTestimonial();
		break;

	default:
		showTestimonial( $option );
		break;
}


//=================================================== TESTIMONIALS OPTION ===================================================
function showTestimonial( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 		= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_keywords 	= trim(mosGetParam( $_REQUEST, "filter_keywords", "" ));
	
	$where = "";
	if( !empty($filter_keywords)  ) {
		$where	= " WHERE client_name LIKE '%$filter_keywords%' OR msg LIKE '%$filter_keywords%' OR city_name LIKE '%$filter_keywords%' ";
	}
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_testimonials $where ORDER BY id DESC";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_testimonials $where ORDER BY id DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	HTML_Testimonial::showTestimonial( $rows, $pageNav, $option, $filter_keywords );
}


function editTestimonial( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosTestimonial( $database );
	// load the row from the db table
	$row->load( (int)$id );
	
	if (!$id) {
		$row->published = 1;
	}
	
	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );
	
	HTML_Testimonial::editTestimonial( $row, $option, $lists );
}


function saveTestimonial( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosTestimonial( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
		
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	mosRedirect( "index2.php?option=$option", "The Testimonial was saved successfully" );
}


function changeTestimonial( $cid=null, $state=0, $option ) {
	global $database, $my, $act;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $state ? 'publish' : 'unpublish';
		mosErrorAlert( "Select an item to $action" );
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE tbl_testimonials SET published = " . (int) $state . " WHERE ( $cids )";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( $state ) {
		$msg	= "Testimonial(s) were published successfully";
	}else {
		$msg	= "Testimonial(s) were unpublished successfully";
	}
	
	mosRedirect('index2.php?option=com_testimonial', $msg);
}


function removeTestimonial( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_testimonials WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option", "The Testimonial(s) were removeted successfully" );
}

function cancelTestimonial() {
	mosRedirect('index2.php?option=com_testimonial');
}

?>
