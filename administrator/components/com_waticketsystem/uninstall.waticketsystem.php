<?php
/**
 * FileName: install.waticketsystem.php
 * Date: 22/05/2006
 * License: GNU General Public License
 * Script Version #: 2.0.0
 * JOS Version #: 1.0.x
 * Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
 */

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

function com_uninstall()
{
	global $database;
	
	// setup settings
	require('components/com_waticketsystem/admin.waticketsystem.html.php');
	$wats = new watsSettings( $database );
	
	if ( $wats->get( 'upgrade' ) == 0 )
	{
		// uninstall
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_category`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_groups`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_highlight`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_msg`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_permissions`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_settings`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_ticket`;" );
		$database->query();
		$database->setQuery( "DROP TABLE IF EXISTS `#__wats_users`;" );
		$database->query();
	}

	return '<div style="text-align: center;"><h3>WebAmoebaTicketSystem</h3><p>Thanks for using the WebAmoeba TicketSystem</p></div>';
}

?>