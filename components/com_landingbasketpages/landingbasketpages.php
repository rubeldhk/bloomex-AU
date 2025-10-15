<?php
/**
* @version $Id: contact.php 4730 2006-08-24 21:25:37Z stingrey $
* @package Joomla
* @subpackage Contact
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

// load the html drawing class
require_once( $mainframe->getPath( 'front_html' ) );
//require_once( $mainframe->getPath( 'class' ) );



switch( $task ) {
	default:
		viewPage( );
		break;
}

function viewPage(  ) {
	global $mainframe, $database, $my, $Itemid;

	$url 	= trim(mosGetParam( $_GET, "url" ));
	$lang 	= trim(mosGetParam( $_GET, "lg" ));
	//print_r($_REQUEST);	
	
	/*if( $lang	== 1 ) {
		$query = "SELECT * FROM tbl_landing_pages WHERE lang = $lang AND (url='" . $url . "' OR url='" . str_replace( "/", "", $url) . "') ";
	}else {
		$url		= str_replace("/", "", $url) . "/index.html";
		$query 	= "SELECT * FROM tbl_landing_pages WHERE lang = $lang AND url='" . $url . "'";
	}
         * 
         */
        $query 	= "SELECT * FROM tbl_landing_pages WHERE lang = $lang AND url='" . $url . "'";
	
	$database->setQuery( $query );
	$Page = $database->loadObjectList();
        
        $query_meta_default = "SELECT * FROM tbl_metatag_all WHERE lang='".$Page[0]->lang."' AND type='basket'  ";
        $database->setQuery($query_meta_default);
        $meta_info_def = $database->loadObjectList();

        $Page[0]->meta = $meta_info_def[0];
        
        
        if( $lang	== 1 ) {
		$query1 = "SELECT * FROM jos_vm_landing_page_gift WHERE lang = $lang AND num='0' AND position='categ_product' ";
	}else {
		
		$query1 	= "SELECT * FROM jos_vm_landing_page_gift WHERE lang = $lang AND  num='0' AND position='categ_product' ";
	}
	
	$database->setQuery( $query1 );
        $type = $database->loadObjectList();
       
	//echo "<br/><br/>";
	//print_r($Page);
	//echo "<br/><br/>";
	//die($query);
	
	if( count($Page) ) {
		HTML_LandingBasketPages::viewPage($Page[0]);
	} else {
		header('location: http://bloomex.ca');
	}
}
?>