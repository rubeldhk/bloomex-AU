<?php
/**
* @version $Id: offline.php 3495 2006-05-15 01:44:00Z stingrey $
* @package Joomla
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

global $database;
global $mosConfig_live_site, $mosConfig_lang;

$adminOffline = false;

if (!defined( '_INSTALL_CHECK' )) {
	// this method is different from 1.1 because the session handling is not the same
	session_name( md5( $mosConfig_live_site ) );
	session_start();
	
	if (class_exists( 'mosUser' )) {
		// restore some session variables
		$admin 				= new mosUser( $database );
		$admin->id 			= intval( mosGetParam( $_SESSION, 'session_user_id', '' ) );
		$admin->username 	= strval( mosGetParam( $_SESSION, 'session_username', '' ) );
		$admin->usertype 	= strval( mosGetParam( $_SESSION, 'session_usertype', '' ) );
		$session_id 		= mosGetParam( $_SESSION, 'session_id', '' );
		$logintime 			= mosGetParam( $_SESSION, 'session_logintime', '' );
	
		// check against db record of session
		if ($session_id == md5( $admin->id . $admin->username . $admin->usertype . $logintime )) {
			$query = "SELECT *"
			. "\n FROM #__session"
			. "\n WHERE session_id = '$session_id'"
			. "\n AND username = " . $database->Quote( $admin->username )
			. "\n AND userid = " . intval( $admin->id )
			;
			$database->setQuery( $query );
			if (!$result = $database->query()) {
				echo $database->stderr();
			}
			if ($database->getNumRows( $result ) == 1) {
				define( '_ADMIN_OFFLINE', 1 );
			}
		}
	}
}

if (!defined( '_ADMIN_OFFLINE' ) || defined( '_INSTALL_CHECK' )) {
	@include_once ('language/' . $mosConfig_lang . '.php' );
	$cur_template = 'rhuk_solarflare_ii';
	
	// needed to seperate the ISO number from the language file constant _ISO
	$iso =explode( '=', _ISO );
	// xml prolog
	echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $mosConfig_sitename; ?> - Offline</title>
		<link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/templates/css/offline.css" type="text/css" />
		<link rel="shortcut icon" href="<?php echo $mosConfig_live_site; ?>/images/favicon.ico" />
		<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
	</head>
	<body>
	
		<p>&nbsp;</p>
		<table width="550" align="center" class="outline">
		<tr>
			<td width="60%" height="50" align="center">

			</td>
		</tr>
		<tr>
			<td align="center">
				<h1>
					<?php echo $mosConfig_sitename; ?>
				</h1>
			</td>
		</tr>
		<?php
		if ( $mosConfig_offline == 1 ) {
			?>
			<tr>
				<td width="39%" align="center">
					<h2>
						<?php echo $mosConfig_offline_message; ?>
					</h2>
				</td>
			</tr>
			<?php
		} else if (@$mosSystemError) {
			?>
			<tr>
				<td width="39%" align="center">
					<h2>
						<?php echo $mosConfig_error_message; ?>
					</h2>
					<?php echo $mosSystemError; ?>
				</td>
			</tr>
			<?php
		} else {
			?>
			<tr>
				<td width="39%" align="center">
				<h2>
					<?php echo _INSTALL_WARN; ?>
				</h2>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
	
	</body>
	</html>
	<?php
	exit( 0 );
}
?>