<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');
require_once( $mainframe->getPath('admin_html') );
function view(){
     global $database;
     $query = "SELECT * from jos_freeshipping_price";
     $database->setQuery( $query);
    $rows = $database->loadObjectList();
    $price = $rows[0]->price;
    $public = $rows[0]->public;
EditFreeShipping::open($price,$public); 
}
view();
switch ($task) {
	case 'save':
		save();
		break;
            	default:
}
function save(){
    global $database;
    $price = $_REQUEST['price']?$_REQUEST['price']:'';
    $public = $_REQUEST['public']?$_REQUEST['public']:'';
    
$query = "UPDATE jos_freeshipping_price"
	. "\n SET public = " . intval( $public )." , price=".intval($price)."";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
        EditFreeShipping::open($price,$public);
}
?>