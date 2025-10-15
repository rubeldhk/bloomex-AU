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

global $mosConfig_absolute_path;

require_once( $mainframe->getPath( 'front_html' ) );/*
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/virtuemart.cfg.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/language.class.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/english.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/Log/Log.php" );
*/
switch ($task) {
	case 'reminderSave':
		reminderSave( );
		break;
		
	default:
		HTML_MailChimp::reminderForm( $sMsg );
		break;
}


function reminderSave() {
	global $database, $iso_client_lang, $mosConfig_live_site;
	
	require_once 'inc/MCAPI.class.php';
	require_once 'inc/config.inc.php'; //contains apikey
	
	$occPerson		= mosGetParam($_REQUEST, "occPerson", "");
	$aOccPerson		= explode(" ", $occPerson);
	$FNAME			= $aOccPerson[0];
	$LNAME			= $aOccPerson[1];
	$occOccation		= mosGetParam($_REQUEST, "occOccation", "");
	$occDate		= mosGetParam($_REQUEST, "occDate", "");
	$occEmail		= mosGetParam($_REQUEST, "occEmail", "");

	$api = new MCAPI($apikey);

	$merge_vars = array(	'FNAME' => $FNAME, 
						'LNAME' => $LNAME, 
						'date' => $occDate,
						'multi_choice' => $occOccation,
	);

	// By default this sends a confirmation email - you will not see new members
	// until the link contained in it is clicked!
	$retval = $api->listSubscribe( $listId, $occEmail, $merge_vars);

	if ($api->errorCode){
		echo "Error:<br/>";
		echo "Unable to load listSubscribe()!<br/>";
		echo "\tCode=".$api->errorCode."<br/>";
		echo "\tMsg=".$api->errorMessage."<br/>";
		die();
	} else {
	    $sMsg	 = "Thanks for your subscribed!";
	}
	
	/*print_r($api);
	die();*/
	HTML_MailChimp::reminderForm( $sMsg );
}


?>
