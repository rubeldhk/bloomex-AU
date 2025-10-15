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
require_once( $mainframe->getPath( 'front_html' ) );
switch ($task) {		
	case 'thankyou':
		HTML_MyContest::thankYou();
		break;
		
	case 'saveForm':
		saveForm(  );
		break;
	
	case 'makeForm':
		HTML_MyContest::makeForm();
		break;
		
	default:
		HTML_MyContest::landingPage( );
		break;
}


function saveForm( ) {	
	global $database;
	
	$first_name				= mosGetParam( $_REQUEST, "first_name"		, "" );
	$last_name				= mosGetParam( $_REQUEST, "last_name"		, "" );
	$address				= mosGetParam( $_REQUEST, "address"			, "" );	
	$city					= mosGetParam( $_REQUEST, "city"			, "" );
	$province				= mosGetParam( $_REQUEST, "province"		, "" );
	$postal_code			= mosGetParam( $_REQUEST, "postal_code"		, "" );
	$email_address			= mosGetParam( $_REQUEST, "email_address"	, "" );
	$telephone				= mosGetParam( $_REQUEST, "telephone"		, "" );
	$desc					= mosGetParam( $_REQUEST, "desc"			, "" );
	$notification			= mosGetParam( $_REQUEST, "notification"	, "" );
	$keep_me				= mosGetParam( $_REQUEST, "keep_me"			, "" );
	
	
	$query 			= "INSERT INTO tbl_contest(  first_name, 
												 last_name, 
												 address, 
												 city, 
												 province, 
												 postal_code, 
												 email_address, 
												 telephone, 
												 `desc`, 
												 keep_me,
												 notification ) 
				   	   VALUES( 	'$first_name', 
				   	   			'$last_name', 
				   	   			'$address', 
				   	   			'$city', 
				   	   			'$province', 
				   	   			'$postal_code', 
				   	   			'$email_address', 
				   	   			'$telephone', 
				   	   			'$desc', 
				   	   			'$keep_me',
				   	   			'$notification')";
	$database->setQuery($query);
	$database->query();	
	$insertid	= $database->insertid();

	if( $insertid ) {
		mosRedirect( "hoodwinkedtoo-thankyou.html" );
	}else {
		mosRedirect( "hoodwinkedtoo-enter.html" );
	}
}


?>
