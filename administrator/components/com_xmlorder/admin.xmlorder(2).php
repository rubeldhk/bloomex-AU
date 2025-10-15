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
	case "account":
		switch ($task) {		
			case 'new':
				editAccount( '0', $option);
				break;
		
			case 'edit':
				editAccount( intval( $cid[0] ), $option );
				break;
		
			case 'editA':
				editAccount( $id, $option );
				break;
		
			case 'save':
				saveAccount( $option );
				break;
		
			case 'remove':
				removeAccount( $cid, $option );
				break;
		
			case 'publish':
				changeAccount( $cid, 1, $option );
				break;
		
			case 'unpublish':
				changeAccount( $cid, 0, $option );
				break;
				
			case 'cancel':
				cancelAccount();
				break;
		
			default:
				showAccount( $option );
				break;
		}
		break;
		
	//=============================================================================================
	default:
		switch ($task) {		
			case 'search':
			default:
				showOrder( $option );
				break;
		}
		break;
	
}


function showOrder( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$text_filter	= mosGetParam( $_REQUEST, "text_filter", "" );
	
	
	if( $text_filter ) {
		 $sWhere	= " WHERE ( PF.partner_order_id LIKE '%$text_filter%' OR  O.order_id LIKE '%$text_filter%' OR  PF.partner_name LIKE '%$text_filter%' ) ";
	}
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) AS total FROM tbl_xmlorder AS PF, #__vm_orders AS O 
			  WHERE PF.order_id = O.order_id $sWhere  ORDER BY O.cdate DESC";
	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_xmlorder AS PF INNER JOIN #__vm_orders AS O ON PF.order_id = O.order_id 
			  LEFT JOIN #__vm_warehouse AS W ON O.warehouse = W.warehouse_code 
			  LEFT JOIN #__vm_order_status AS OS ON OS.order_status_code = O.order_status $sWhere  ORDER BY O.cdate DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	HTML_XmlOrder::showOrder( $rows, $pageNav, $option );
}


//=================================================== POSTAL CODE OPTION ===================================================
function showAccount( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_partners";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_partners ORDER BY partner_name";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	

	HTML_XmlOrder::showAccount( $rows, $pageNav, $option );
}


function editAccount( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosXmlOrder( $database );
	// load the row from the db table
	$row->load( (int)$id );

	if (!$id) {
		$row->ordering 	= 0;
		$row->published = 1;
	}
	
	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );
	
	HTML_XmlOrder::editAccount( $row, $option, $lists );
}


function saveAccount( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosXmlOrder( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$path	= trim(mosGetParam( $_REQUEST, "path" ));	
	
	if( !file_exists( "$mosConfig_absolute_path/media/partner_service/$path" ) ) {		
		if( !mkdir("$mosConfig_absolute_path/media/partner_service/$path") ) {
			$msg	= "<br/>The partner root folder wasn't create. Please create <b>media/partner_service/$path</b> manual.";
		}else {
			@copy( "$mosConfig_absolute_path/media/index.html", "$mosConfig_absolute_path/media/partner_service/$path/index.html");
			
			if( !mkdir("$mosConfig_absolute_path/media/partner_service/$path/order_processed") ) {
				$msg	= $msg . "<br/>The Order Processed folder wasn't create. Please create <b>media/partner_service/$path/order_processed</b> manual.";
			}else {
				@copy( "$mosConfig_absolute_path/media/index.html", "$mosConfig_absolute_path/media/partner_service/$path/order_processed/index.html");
			}
		}
	}else {
		if( !is_file("$mosConfig_absolute_path/media/partner_service/$path/index.html") ) {
			@copy( "$mosConfig_absolute_path/media/index.html", "$mosConfig_absolute_path/media/partner_service/$path/index.html");
		}		
		
		if( !file_exists( "$mosConfig_absolute_path/media/partner_service/$path/order_processed" ) ) {	
			if( !mkdir("$mosConfig_absolute_path/media/partner_service/$path/order_processed") ) {
				$msg	= $msg .  "<br/>The Order Processed folder wasn't create. Please create <b>media/partner_service/$path/order_processed</b> manual.";
			}
		}
		
		if( !is_file("$mosConfig_absolute_path/media/partner_service/$path/order_processed/index.html") ) {
			@copy( "$mosConfig_absolute_path/media/index.html", "$mosConfig_absolute_path/media/partner_service/$path/order_processed/index.html");
		}		
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save Account Successfully. ".$msg );
}


function removeAccount( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_partners WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Remove Postal Code Successfully" );
}


function changeAccount( $cid=null, $state=0, $option ) {
	global $database, $my, $act;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $state ? 'publish' : 'unpublish';
		mosErrorAlert( "Select an item to $action" );
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE tbl_partners SET published = " . (int) $state . " WHERE ( $cids )";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( $state ) {
		$msg	= "Publish Account Successfully";
	}else {
		$msg	= "UnPublish Account Successfully";
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", $msg );
}


function cancelAccount() {
	mosRedirect('index2.php?option=com_xmlorder&act=account');
}


//=================================================== XmlOrder OPTION ===================================================
function showUnAvailableXmlOrder( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_years 	= intval( $mainframe->getUserStateFromRequest( "view{$option}filter_years", 'filter_years', 0 ) );
		
	$where = "";
	/*if( $filter_years > 0  ) {
		$where	= " AND name LIKE '%/$filter_years' ";
	}*/
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_options WHERE type='XmlOrder_option' $where";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_options WHERE type='XmlOrder_option' $where ORDER BY id DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_XmlOrder::showUnAvailableXmlOrder( $rows, $pageNav, $option );
}


function editUnAvailableXmlOrder( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosXmlOrder( $database );
	// load the row from the db table
	$row->load( (int)$id );

	if (!$id) {
		$row->ordering 	= 0;
		$row->published = 1;
	}
	
	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );
	
	HTML_XmlOrder::editUnAvailableXmlOrder( $row, $option, $lists );
}


function saveUnAvailableXmlOrder( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosXmlOrder( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$days 	= mosGetParam( $_POST, "days" );
	$months = mosGetParam( $_POST, "months" );
	/*$years 	= mosGetParam( $_POST, "years" );*/	
	
	$row->type = "XmlOrder_option";
	$row->name = "$months/$days";
	//$row->name = "$months/$days/$years";
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$row->updateOrder( "type='XmlOrder_option'" );
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save XmlOrder Option Successfully" );
}


function removeUnAvailableXmlOrder( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_options WHERE id = $value AND type='XmlOrder_option'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Remove XmlOrder Option Successfully" );
}


function changeUnAvailableXmlOrder( $cid=null, $state=0, $option ) {
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
		$msg	= "Publish XmlOrder Option Successfully";
	}else {
		$msg	= "UnPublish XmlOrder Option Successfully";
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", $msg );
}


function orderUnAvailableXmlOrder( $uid, $inc, $option ) {
	global $database, $act;

	$row = new mosXmlOrder( $database );
	$row->load( (int)$uid );
	$row->updateOrder();
	$row->move( $inc, "published >= 0 AND `type` = 'XmlOrder_option' " );
	$row->updateOrder();

	mosRedirect( "index2.php?option=$option&act=$act", "Reorder XmlOrder Option Successfully" );
}


function cancelUnAvailableXmlOrder() {
	mosRedirect('index2.php?option=com_XmlOrder');
}


//=================================================== SPECIAL XmlOrder OPTION ===================================================
function showSpecialXmlOrder( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_years 	= intval( $mainframe->getUserStateFromRequest( "view{$option}filter_years", 'filter_years', 0 ) );
		
	$where = "";
	/*if( $filter_years > 0  ) {
		$where	= " AND name LIKE '%/$filter_years' ";
	}*/
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_options WHERE type='special_XmlOrder' $where";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_options WHERE type='special_XmlOrder' $where ORDER BY id DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	HTML_XmlOrder::showSpecialXmlOrder( $rows, $pageNav, $option );
}


function editSpecialXmlOrder( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosXmlOrder( $database );
	// load the row from the db table
	$row->load( (int)$id );

	if (!$id) {
		$row->ordering 	= 0;
		$row->published = 1;
	}
	
	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );
	
	HTML_XmlOrder::editSpecialXmlOrder( $row, $option, $lists );
}


function saveSpecialXmlOrder( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosXmlOrder( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$days 	= mosGetParam( $_POST, "days" );
	$months = mosGetParam( $_POST, "months" );
	/*$years 	= mosGetParam( $_POST, "years" );*/	
	
	$row->type = "special_XmlOrder";
	$row->name = "$months/$days";
	//$row->name = "$months/$days/$years";
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$row->updateOrder( "type='special_XmlOrder'" );
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save Special XmlOrder Option Successfully" );
}


function removeSpecialXmlOrder( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_options WHERE id = $value AND type='special_XmlOrder'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Remove Special XmlOrder Option Successfully" );
}


function changeSpecialXmlOrder( $cid=null, $state=0, $option ) {
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
		$msg	= "Publish Special XmlOrder Option Successfully";
	}else {
		$msg	= "UnPublish Special XmlOrder Option Successfully";
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", $msg );
}


function orderSpecialXmlOrder( $uid, $inc, $option ) {
	global $database, $act;

	$row = new mosXmlOrder( $database );
	$row->load( (int)$uid );
	$row->updateOrder();
	$row->move( $inc, "published >= 0 AND `type` = 'special_XmlOrder' " );
	$row->updateOrder();

	mosRedirect( "index2.php?option=$option&act=$act", "Reorder Special XmlOrder Option Successfully" );
}


function cancelSpecialXmlOrder() {
	mosRedirect('index2.php?option=com_XmlOrder&act=special_XmlOrder');
}


//=================================================== CUT OFF TIME CONFIGURATION ===================================================
function editCutOffTime( $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$query = "SELECT * FROM tbl_options WHERE type='cut_off_time'";
	$database->setQuery( $query );
	$row = $database->loadRow();

	HTML_XmlOrder::editCutOffTime( $row, $option );
}


function saveCutOffTime( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosXmlOrder( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
		
	$hours 			= mosGetParam( $_POST, "hours", "" );
	$minutes 		= mosGetParam( $_POST, "minutes", "" );
	$XmlOrder_fee	= mosGetParam( $_POST, "XmlOrder_fee", "" );
	$row->options	= $hours."[--1--]".$minutes."[--1--]".$XmlOrder_fee;
	$row->type		= "cut_off_time";
	$row->name		= "Cut Off Time Configuration";
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Cut Off Time Configuration and XmlOrder Extra Fee successfully" );
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
