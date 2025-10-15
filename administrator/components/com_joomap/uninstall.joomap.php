<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
/**
 * Uninstall routine for Joomap.
 * Drops the settings table from the Joomla! database
 * @author Daniel Grothe
 * @see JoomapConfig.php
 * @package Joomap_Admin
 * @version $Id: install.joomap.php,v 0.1 2006/03/16 20:27:27 mic Exp $
 */
function com_uninstall() {
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/classes/JoomapConfig.php' );
	JoomapConfig::backup();
	JoomapConfig::remove();
}

?>