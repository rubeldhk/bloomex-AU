<?php
	/**
	 * The Joomap component administrator toolbar
	 * @author Daniel Grothe
	 * @see admin.joomap.php
	 * @package Joomap_Admin
	 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// load language file
if( file_exists($GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/language/'.$GLOBALS['mosConfig_lang'].'.php') ) {
	require_once( $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/language/'.$GLOBALS['mosConfig_lang'].'.php' );
} else {
	require_once( $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/language/english.php' );
}
// load html output class
require_once( $mainframe->getPath( 'toolbar_html' ) );

$act = mosGetParam( $_REQUEST, 'act', '' );
if ($act) {
	$task = $act;
}

switch ($task) {
	default:
		TOOLBAR_joomap::_DEFAULT();
		break;
}
?>
