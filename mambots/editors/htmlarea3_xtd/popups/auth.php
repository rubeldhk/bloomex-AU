<?php
defined( '_VALID_MOS' ) or die( 'Direct access to this location is not allowed!' );

$base_path = str_replace( "/mambots/editors/htmlarea3_xtd/popups", "", str_replace("\\", "/", dirname( __FILE__ )));
global $my, $mosConfig_live_site, $database;

error_reporting(0);
// Call from the Backend?
// must start the session to answer this question
include_once( $base_path.'/configuration.php');

session_name( md5( $mosConfig_live_site ) );
@session_start();

if(  isset( $_SESSION['session_user_id']) && isset( $_SESSION['session_username']) ) {

  require_once( $base_path."/administrator/includes/auth.php" );
  // mainframe is an API workhorse, lots of 'core' interaction routines
  $mainframe = new mosMainFrame( $database, "", $base_path );

  $database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
}

// Call from the Frontend
else {

  include_once ("$base_path/globals.php");
  require_once ("$base_path/configuration.php");
  require_once ("$base_path/includes/mambo.php");
  if (file_exists( "$base_path/components/com_sef/sef.php" )) {
	  require_once( "$base_path/components/com_sef/sef.php" );
  } else {
	  require_once( "$base_path/includes/sef.php" );
  }
  
  $database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
  // mainframe is an API workhorse, lots of 'core' interaction routines
  $mainframe = new mosMainFrame( $database, "", $base_path );
  $mainframe->initSession();
  $acl = new gacl_api();
  
  // get the information about the current user from the sessions table
  $my = $mainframe->getUser();

}

?>
