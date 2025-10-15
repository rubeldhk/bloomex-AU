<?php

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

global $mosConfig_absolute_path;

require_once( $mainframe->getPath( 'front_html' ) );
$task	= mosGetParam( $_REQUEST, "task", "" );

switch ($task) {	
	case 'send':
	default:
		send_NewLetter( );
		break;
}


function send_NewLetter() {
	global $database, $iso_client_lang, $mosConfig_live_site, $mosConfig_offset;
	
	$email_address	= htmlentities(mosGetParam( $_REQUEST, "email", "" ), ENT_QUOTES);
		
	$query 	= "SELECT COUNT(*) FROM tbl_newsletter WHERE email_address='$email_address'";
	$database->setQuery($query);
	$bExist 	= $database->loadResult();
	
	if( $bExist ) {
		echo "exist";
	}else {
		$query 	= "INSERT INTO tbl_newsletter(email_address, cdate) VALUES('$email_address', '".date("Y-m-d H:i:s")."' )";
		$database->setQuery($query);
		$bInsert 	= $database->query();
		
		echo "success";
	}
	
	exit(0);
}


?>
