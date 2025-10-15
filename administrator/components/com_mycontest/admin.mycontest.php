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
	case 'exporExcel':
		exporExcel( $option );
		break;
		
	default:
		showMyContest( $option );
		break;
}


//=================================================== POSTAL CODE OPTION ===================================================
function exporExcel( $option ) {
	global $database;
	$exp	=	new ExportToExcel();
	
	$query = "SELECT *  FROM tbl_contest ORDER BY id DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	$exp->exportWithQuery($rows,"contest.xls",$conn);
	exit(0);
}

function showMyContest( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	
	// get the total number of records
	$query = "SELECT COUNT(*) AS total FROM tbl_contest ORDER BY id DESC";
	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT *  FROM tbl_contest ORDER BY id DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	HTML_MyContest::showMyContest( $rows, $pageNav, $option );
}


class ExportToExcel
{
	
	function exportWithPage($php_page,$excel_file_name)
	{
		$this->setHeader($excel_file_name);
		require_once "$php_page";
	
	}
	function setHeader($excel_file_name)//this function used to set the header variable
	{
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-Disposition: attachment; filename=$excel_file_name"); 
		header("Pragma: no-cache"); 
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");	
	}
	function exportWithQuery($rows,$excel_file_name,$conn)//to export with query
	{
		$header="<table border=1px><tr><th colspan='12'>Alliance contest List</th></tr>";
		if( count($rows) ) {
			foreach ($rows as $row) {
				$body.="<tr>";
				$body.="<td>".$row->id."</td>";
				$body.="<td>".$row->first_name."</td>";
				$body.="<td>".$row->last_name."</td>";
				$body.="<td>".$row->address."</td>";
				$body.="<td>".$row->city."</td>";
				$body.="<td>".$row->province."</td>";
				$body.="<td>".$row->postal_code."</td>";
				$body.="<td>".$row->email_address."</td>";
				$body.="<td>".$row->telephone."</td>";
				$body.="<td>".$row->desc."</td>";
				$body.="<td>".($row->notification ? "Yes" : "No")."</td>";
				$body.="<td>".$row->created_date."</td>";
				$body.="</tr>";	
			}
		}
	
		$this->setHeader($excel_file_name);
		echo $header.$body."</table";
		die();
	}


}
?>
