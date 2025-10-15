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
	case "setup_category":
		switch ($task) {
			case 'save':
				saveSetupCategory( $option );
				break;
				
			default:
				HTML_LandingPages::setupCategory( $option );
				break;
		}
		break;
		
	//=============================================================================================
	default:
		switch ($task) {		
			case 'new':
				editLandingPages( '0', $option);
				break;
		
			case 'edit':
				editLandingPages( intval( $cid[0] ), $option );
				break;
		
			case 'editA':
				editLandingPages( $id, $option );
				break;
		
			case 'save':
				saveLandingPages( $option );
				break;
		
			case 'remove':
				removeLandingPages( $cid, $option );
				break;
				
			case 'cancel':
				cancelLandingPages();
				break;
		
			default:
				showLandingPages( $option );
				break;
		}
		break;
	
}



//=================================================== LandingPages OPTION ===================================================
function showLandingPages( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_lang 	= mosGetParam( $_POST, "filter_lang", -1 );
	$filter_key 	= trim(mosGetParam( $_POST, "filter_key" ));
		
	$where 	= "";
	$aWhere	= array();
	
	if( $filter_lang > 0  ) {
		$aWhere[]	= " lang = $filter_lang ";
	}
	
	if( $filter_key ) {
		$aWhere[]	= " (province LIKE '%$filter_key%' OR url LIKE '%$filter_key%') ";
	}
	
	if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_landing_pages $where";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_landing_pages $where ORDER BY city ASC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	$lists 	= array();	
	$types	= array();
	$types[] 	= mosHTML::makeOption( "-1", "------ Select Language ------" );
	$types[] 	= mosHTML::makeOption( "1", "English" );
	$types[] 	= mosHTML::makeOption( "2", "French" );
	$lists['filter_lang'] 	= mosHTML::selectList( $types, 'filter_lang', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_lang );
	
	$lists['filter_key']	= $filter_key;

	HTML_LandingPages::showLandingPages( $rows, $pageNav, $option, $lists );
}


function editLandingPages( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosLandingPages( $database );
	// load the row from the db table
	$row->load( (int)$id );

	if (!$id) {
		$row->location_address		= 1;
		$row->lang					= 1;
		$row->city					= "";
		$row->province				= "";
		$row->url					= "";
		$row->telephone				= "";
		$row->location_address 		= "";
		$row->location_country 	 	= "";
		$row->location_postcode 	= "";
		$row->location_telephone 	= "";
		$row->category_id 	 		= "";
		$row->enable_location 	 	= 0;
		
	}
	
	$lists = array();
	$types	= array();
	$lists['enable_location']	= mosHTML::yesnoRadioList( "enable_location", "", $row->enable_location );
	
	$types[] 	= mosHTML::makeOption( "-1", "------ Select Language ------" );
	$types[] 	= mosHTML::makeOption( "1", "English" );
	$types[] 	= mosHTML::makeOption( "2", "French" );
	$lists['lang'] 	= mosHTML::selectList( $types, 'lang', 'class="inputbox" size="1"', 'value', 'text', $row->lang );
	
	$query 	= "SELECT country_name, country_name AS country_value FROM #__vm_country ORDER BY country_name ASC";
	$database->setQuery($query);
	$rows	= $database->loadObjectList();		
	$oCountry	=  new stdClass;
	$oCountry->country_name 	= " ------------------ Country ------------------ ";
	$oCountry->country_value 	= "";
	$aCountry	= array();
	$aCountry[]	= $oCountry;
	$rows = array_merge($aCountry, $rows);
	$lists['location_country']	= mosHTML::selectList( $rows, "location_country", "size='1'", "country_value", "country_name", $row->location_country );

		
	HTML_LandingPages::editLandingPages( $row, $option, $lists );
}


function saveLandingPages( $option ) {
	global $database, $mosConfig_absolute_path, $act;
	
	$row = new mosLandingPages( $database );
	
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save Landing Pages Successfully" );
}


function removeLandingPages( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_landing_pages WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option", "Remove Landing Pages Successfully" );
}


function cancelLandingPages() {
	mosRedirect('index2.php?option=com_landingpages');
}



function saveSetupCategory( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$category_id	= mosGetParam( $_POST, "category_id", 0 );
	if( $category_id ) {
		$sql = "UPDATE tbl_landing_pages SET category_id = ".$category_id;
		$database->setQuery($sql);
		$database->query();
		
	}
	
	mosRedirect( "index2.php?option=$option", "Save Category Product Configuration successfully" );
}
?>