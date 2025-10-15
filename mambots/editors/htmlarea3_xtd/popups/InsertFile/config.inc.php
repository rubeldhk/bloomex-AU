<?php
defined( '_VALID_MOS' ) or die( 'Direct access to this location is not allowed!' );
// $Id: config.inc.php, v 1.01.4 2004/04/19 12:05:04 bpfeifer Exp $
/**
* HTMLArea3 XTD addon - InsertFile
* Based on Al Rashid's File Manager
* @package Mambo Open Source
* @Copyright � 2004 Bernhard Pfeifer aka novocaine
* @ All rights reserved
* @ Mambo Open Source is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.01.4 $
**/
global $database,$mosConfig_live_site, $mosConfig_absolute_path;

$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
$database->setQuery( "SELECT id FROM #__mambots WHERE element = 'htmlarea3_xtd' AND folder = 'editors'" );
$id = $database->loadResult();
$mambot =new  mosMambot( $database );
$mambot->load( $id );
$params =new  mosParameters( $mambot->params );

$MY_DOCUMENT_ROOT 		= $mosConfig_absolute_path."/images/stories";	// if you are using Docman change this to '/dmdocuments';
$MY_BASE_URL 			= '/images/stories';	// if you are using Docman change this to '/dmdocuments';
$MY_ALLOW_EXTENSIONS	= array('html', 'doc', 'xls', 'txt', 'gif', 'pdf', 'gz', 'tar', 'zip', 'rar', 'bzip', 'sql', 'swf', 'mov', 'jpeg', 'jpg', 'png'); //add file types here, e. g. 'gif', 'jpeg', 'jpg', 'png', 'pdf'
$MY_DENY_EXTENSIONS		= array('php', 'php3', 'php4', 'phtml', 'shtml', 'cgi', 'pl'); //add file types here
$MY_LIST_EXTENSIONS		= array('html', 'doc', 'xls', 'txt', 'pdf', 'gz', 'tar', 'zip', 'rar', 'sql', 'swf', 'mov', 'gif', 'jpeg', 'jpg', 'png');	//add file types here
$MY_ALLOW_DELETE_FILE 	= true;	// set to false if file deleting should be disabled
$MY_ALLOW_UPLOAD_FILE 	= true;	// set to false if file uploads should be disabled
$MY_ALLOW_DELETE_FOLDER = true;	// set to false if directory deleting should be disabled
$MY_ALLOW_CREATE_FOLDER = true;	// set to false if directory creation should be disabled
$MY_MAX_FILE_SIZE 		= 2*1024*1024;
$MY_LANG 				= $params->get( 'language', 'en' );
$MY_DATETIME_FORMAT		= "d.m.Y H:i";	// set your date and time format

// DO NOT EDIT BELOW
$MY_NAME = 'insertfiledialog';
$lang_file = 'lang/lang-'.$MY_LANG.'.php';
if (is_file('lang/lang-'.$MY_LANG.'.php')) {
	require($lang_file);
} else {
	require('lang/lang-en.php');
}
$MY_PATH = '/';
$MY_UP_PATH = '/';


