<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
require_once( $mainframe->getPath('admin_html') );
function view() {
    global $database;
    $query = "SELECT installed,last_install_date from tbl_install";
    $database->setQuery( $query);
    $row = false;
    $database->loadObject($row);
    
    $installed = '';
    $last_install_date = '';
    if ($row) {
        $installed = $row->installed;
        $last_install_date = $row->last_install_date;
    }
    Install::open($installed,$last_install_date);
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
    $installed = $_REQUEST['installed']?$_REQUEST['installed']:0;
$query = "UPDATE tbl_install"
	. "\n SET installed = " . intval( $installed )."";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
    Install::open($installed);
}
?>