<?php
/**
 * $Id: toolbar.xmap.php 8 2007-09-27 00:11:32Z root $
 * $LastChangedDate: 2007-09-26 18:11:32 -0600 (mié, 26 sep 2007) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// load language file
if( file_exists($GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_xmap/language/'.$GLOBALS['mosConfig_lang'].'.php') ) {
	require_once( $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_xmap/language/'.$GLOBALS['mosConfig_lang'].'.php' );
} else {
	require_once( $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_xmap/language/english.php' );
}
// load html output class
require_once( $mainframe->getPath( 'toolbar_html' ) );

$act = mosGetParam( $_REQUEST, 'act', '' );
if ($act) {
	$task = $act;
}

switch ($task) {
	default:
		TOOLBAR_xmap::_DEFAULT();
		break;
}
?>
