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
	case "updHist":
		updHist( $option );
		break;
		
		default:
		switch ($task) {		
			case 'new':
				editOperatorsCodes( '0', $option);
				break;
		
			case 'edit':
				editOperatorsCodes( intval( $cid[0] ), $option );
				break;
		
			case 'editA':
				editOperatorsCodes( $id, $option );
				break;
		
			case 'save':
				saveOperatorsCodes( $option );
				break;
		
			case 'remove':
				removeOperatorsCodes( $cid, $option );
				break;
				
			case 'cancel':
				cancelOperatorsCodes();
				break;
		
			default:
				showOperatorsCodes( $option );
				break;
		}
		

		break;
	
}

//=================================================== LandingPages OPTION ===================================================
function showOperatorsCodes( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

		
		
	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_operators_codes ";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_operators_codes ORDER BY name ASC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	

	HTML_OperatorsCodes::showOperatorsCodes( $rows, $pageNav, $option );
}


function editOperatorsCodes( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosOperatorsCodes( $database );
	// load the row from the db table
	$row->load( (int)$id );

	if (!$id) {
		$row->name		= '';
		$row->code					= '';
		
		
	}
	
	

		
	HTML_OperatorsCodes::editOperatorsCodes( $row, $option );
}


function saveOperatorsCodes( $option ) {
	global $database, $mosConfig_absolute_path, $act;
	
	$row = new mosOperatorsCodes( $database );
	
	
	
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Save operator Successfully" );
}


function removeOperatorsCodes( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM tbl_operators_codes WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option", "Remove operator Successfully" );
}


function cancelOperatorsCodes() {
	mosRedirect('index2.php?option=com_operatorscodes');
}

function updHist( $option ) {
	global $database, $mosConfig_absolute_path, $act;
	
	$sql	= "SELECT * FROM tbl_operators_codes";
	$database->setQuery($sql);
	$oOperator	= $database->loadObjectList();
	
	if( count($oOperator) ) {
		foreach( $oOperator as $PageItem ) {
			$oldurl	= $PageItem->code;
                        $newurl	= $PageItem->id;			
			$sOperOrder	= "SELECT order_id FROM jos_vm_orders WHERE customer_occasion='".$oldurl."'";
			$database->setQuery($sOperOrder);
			$bExistOrder		= $database->loadObjectList();
			if( count($bExistOrder) ) {
                            foreach( $bExistOrder as $OrderItem ) {
                                $sExist	= "SELECT COUNT(*) FROM tbl_order_operator WHERE operator_id='".$newurl."' AND order_id='".$OrderItem->order_id."'";
                                
                                $database->setQuery($sExist);
                                $bExistR		= $database->loadResult();
                                if( !$bExistR ) {
				$sInsertOrderH	= "INSERT INTO tbl_order_operator (operator_id, order_id) VALUES('".$newurl."','".$OrderItem->order_id."')";
                                 
				$database->setQuery($sInsertOrderH);
				$database->query();
                                }
                            }
                        }
                }
        }
	
	mosRedirect( "index2.php?option=$option", "Update history successfully" );
}

?>