<?php

/**
* @version 1.0
* @package Custom QuickIcons
* @copyright (C) 2005 Halil K�kl� <halilkoklu at gmail dot com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or
  die( 'Direct Access to this location is not allowed.' );
 
// include support libraries
require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );
 
// handle the task
$task = mosGetParam( $_REQUEST, 'task', '' );
$id = mosGetParam( $_REQUEST, 'id', NULL);
$cid 		= mosGetParam( $_POST, 'cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

global $mosConfig_lang;

// Load Language File
if (file_exists('components/com_customquickicons/lang/' . $mosConfig_lang . '.php'))
  include_once('components/com_customquickicons/lang/' . $mosConfig_lang . '.php');
else
  include_once('components/com_customquickicons/lang/english.php');
 
switch ($task) {
	case "new":
		editIcon(null, $option);
		break;
	case "edit":
		editIcon($id, $option);
		break;
	case "delete":
		deleteIcon($cid, $option);
		break;
	case "save":
		saveIcon(1, $option);
		break;
	case "apply":
		saveIcon(0, $option);
		break;
	case "publish":
		changeIcon($cid, 1, $option);
		break;
	case "unpublish":
		changeIcon($cid, 0, $option);
		break;
	case "orderUp":
		orderIcon($id, -1, $option);
		break;
	case "orderDown":
		orderIcon($id, 1, $option);
		break;
	case "chooseIcon":
		chooseIcon($option);
		break;
	case 'saveorder':
		saveOrder( $cid, $option );
	break;
  default:
    show($option);
    break;
}

// Function to show the Items
function show($option) {
	global $database;
	// Load Items
	$query = 	"SELECT * FROM #__custom_quickicons" .
						" ORDER BY" .
						"  ordering";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	
	// Output
	QuickIcons::show($rows, $option);
}

// Function to edit the Item
function editIcon($id, $option) {
	global $database, $my;
	
	// Load Item
	$row = new CustomQuickIcons();
	$row->load($id);
	
	if (isset($id)) {
		// do stuff for existing records
		$row->checkout($my->id);
	} else {
		// do stuff for new records
		$row->imagepos = 'top';
		$row->ordering = 0;
		$row->published = 1;
	}
	
	$query = "SELECT ordering AS value, title AS text FROM #__custom_quickicons" .
			" ORDER BY ordering";
	
	$list = mosAdminMenus::SpecificOrdering($row, $id, $query, 1);
	
	QuickIcons::edit($row, $list, $option);
}

// Publish an Item
function changeIcon($cid, $action, $option) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$errMsg = $action ? _QI_ERR_SELECT_PBL : _QI_ERR_SELECT_UPBL;
		echo "<script> alert('$errMsg'); window.history.go(-1);</script>\n";
		exit();
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__custom_quickicons " . 
		"SET published = $action " . 
		"WHERE id IN ( $cids )";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	mosRedirect( "index2.php?option=$option" );
}

// Save Icon
function saveIcon($redirect, $option) {
	global $database;
	
	// Get Infos
	$row = new CustomQuickIcons();
	if (!$row->bind( $_POST )) {
		echo "<script> alert('1".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// pre-save checks
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('3".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder();
	
	if ($redirect)
		mosRedirect("index2.php?option=$option");
	else
		mosRedirect("index2.php?option=$option&task=edit&id=".$row->id);
}

// Reorder an Item
function orderIcon($id, $inc, $option) {
	global $database;
	
	// Cleaning ordering
	$query = "SELECT id, ordering FROM #__custom_quickicons" .
			" ORDER BY ordering";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	
	$i=0;
	foreach ($rows as $row) {
		$query = "UPDATE #__custom_quickicons" .
				" SET ordering = $i" .
				" WHERE id = " . $row->id;
		$database->setQuery($query);
		$database->query();
		$i++;
	}
	
	$query = "SELECT ordering FROM #__custom_quickicons" .
			" WHERE id = $id";
	$database->setQuery($query);
	$database->loadObject($row);
	
	if ($row) {
		$newOrder = $row->ordering + $inc;
		
		$query = "SELECT id FROM #__custom_quickicons" .
				"	WHERE ordering = $newOrder";
		$database->setQuery($query);
		$database->loadObject($row2);
		
		if ($row2) {
			$query = "UPDATE #__custom_quickicons" .
					" SET ordering = $newOrder" .
					" WHERE id = $id";
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			$query = "UPDATE #__custom_quickicons" .
					" SET ordering = " . $row->ordering .
					" WHERE id = " . $row2->id;
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
		
		mosRedirect("index2.php?option=$option");
	}
	else
		var_dump($row);exit;
		mosRedirect("index2.php?option=$option");
}

/* This feature (save order) is added by Eric C. Thanks Eric! */
//Save ordering of icons
function saveOrder( &$cid, $option ) {
	global $database;

	$total		= count( $cid );
	$order 		= mosGetParam( $_POST, 'order', array(0) );
	
	for( $i=0; $i < $total; $i++ ) {
		$query = "UPDATE #__custom_quickicons" .
				" SET ordering = $order[$i]" .
				" WHERE id = $cid[$i]";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

		// update ordering
		$row = new CustomQuickicons( $database );
		$row->load( $cid[$i] );
		$row->updateOrder();
	}
	
	$msg 	= 'New ordering saved';
	mosRedirect("index2.php?option=$option", $msg);
} // saveOrder

// Delete icons
function deleteIcon( &$cid, $option) {
	global $database;

	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__custom_quickicons" . 
				" WHERE id IN ( $cids )";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option", _QI_MSG_SUC_DELETED );
}

function chooseIcon($option) {
	
	$handle = opendir('images/');
	
	$imgs = array();
	while($file = readdir($handle))
	{
		if (strpos($file, '.jpg') || strpos($file, '.jpeg') 
					|| strpos($file, '.gif') || strpos($file, '.png'))
			$imgs[] = $file;
	}
	
	closedir($handle);
	
	QuickIcons::chooseIcon($imgs, $option);	
}

?>