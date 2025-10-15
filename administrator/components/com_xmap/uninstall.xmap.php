<?php 
/**
 * $Id: uninstall.xmap.php 24 2007-09-29 16:46:17Z root $
 * $LastChangedDate: 2007-09-29 10:46:17 -0600 (sáb, 29 sep 2007) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

/**
 * Uninstall routine for Xmap.
 * Drops the settings table from the Joomla! database
 * @author Daniel Grothe
 * @see XmapConfig.php
 * @package Xmap_Admin
 * @version $Id: uninstall.xmap.php 24 2007-09-29 16:46:17Z root $
 */
function com_uninstall() {
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_xmap/classes/XmapConfig.php' );
	XmapConfig::backup();
	XmapConfig::remove();
}
