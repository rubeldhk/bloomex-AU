<?php
/**
* ==================================================================
* Contacts XTD - Contacts Extended
* 
* Author: Kurt Banfi
* Email: mambo@clockbit.com
* Website: www.clockbit.com
* Version: 1.0.1
* 
* Contacts XTD is a component for Mambo 4.5.2 and 
* derived from the Mambo Contacts Component:
* ==================================================================
* @version $Id: contxtd.php,v 1.4 2005/10/21 21:35:22 cubalibre Exp $
* @package Mambo
* @subpackage Contact
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
* ==================================================================
* 
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
* 
* This program is distributed WITHOUT ANY WARRANTY; 
* without even the implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* 
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/copyleft/gpl.html
* ==================================================================
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


/** @CubaLibre: include language files */
if (!defined( '_CONTXTD_LANG_INCLUDED' )) {
    if (file_exists("components/com_contxtd/language/".$mosConfig_lang.".php") ) {
        include_once("components/com_contxtd/language/".$mosConfig_lang.".php");
    } else {
        include_once("components/com_contxtd/language/english.php");
    }
}

// load the html drawing class
require_once( $mainframe->getPath( 'front_html' ) );
require_once( $mainframe->getPath( 'class' ) );

$mainframe->setPageTitle( _CONTACT_TITLE );

if ( !isset( $op ) ) {
	$op = '';
}

//Load Vars
$con_id 	= intval( mosGetParam( $_REQUEST ,'con_id', 0 ) );
$contact_id = intval( mosGetParam( $_REQUEST ,'contact_id', 0 ) );
$catid 		= intval( mosGetParam( $_REQUEST ,'catid', 0 ) );

/**
* @CubaLibre: added parameter cat_id to sendmail call 
*/
switch( $task ) {
	case 'blog':
		blogContacts( $id );
		break;
		
	case 'view':
		contactpage( $contact_id );
		break;

	case 'vcard':
		vCard( $contact_id );
		break;

	default:
		listContacts( $option, $catid );
		break;
}

switch( $op ) {
	case 'sendmail':
		sendmail( $cat_id, $con_id, $option );
		break;
}


function listContacts( $option, $catid ) {
	global $mainframe, $database, $my;
	global $mosConfig_live_site;
	global $Itemid;

	/**
	* Query to retrieve all categories that belong under the contxtds section and that are published.
	* @CubaLibre: changed table names 
	*/
	$query = "SELECT *, COUNT(a.id) AS numlinks"
	. "\n FROM #__categories AS cc"
	. "\n LEFT JOIN #__contxtd_details AS a ON a.catid = cc.id"
	. "\n WHERE a.published='1'"
	. "\n AND cc.section='com_contxtd_details'"
	. "\n AND cc.published='1'"
	. "\n AND a.access <= '". $my->gid ."'"
	. "\n AND cc.access <= '". $my->gid ."'"
	. "\n GROUP BY cc.id"
	. "\n ORDER BY cc.ordering"
	;
	$database->setQuery( $query );
	$categories = $database->loadObjectList();

	$count = count( $categories );
	if ( ( $count < 2 ) && ( @$categories[0]->numlinks == 1 ) ) {
		// if only one record exists loads that record, instead of displaying category list
		contactpage( $option, 0 );
	} else {
		$rows = array();
		$currentcat = NULL;
		if ( $catid ) {
			// url links info for category
			/** @CubaLibre: changed table names */
			$query = "SELECT *"
			. "\n FROM #__contxtd_details"
			. "\n WHERE catid = '". $catid."'"
			 . "\n AND published='1'"
			 . "\n AND access <= '". $my->gid ."'"
			. "\n ORDER BY ordering"
			;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			// current category info
			$query = "SELECT name, description, image, image_position"
			. "\n FROM #__categories"
			. "\n WHERE id = '". $catid ."'"
			. "\n AND published = '1'"
			. "\n AND access <= '". $my->gid ."'"
			;
			$database->setQuery( $query );
			$database->loadObject( $currentcat );
		}

		// Parameters
		$menu =new  mosMenu( $database );
		$menu->load( $Itemid );
		$params =new  mosParameters( $menu->params );

		/**
		* @CubaLibre: added/modified parameters
		* @param company
		* @param telephone
		* @param mobile
		*/ 
		$params->def( 'page_title', 1 );
		$params->def( 'header', $menu->name );
		$params->def( 'pageclass_sfx', '' );
		$params->def( 'headings', 1 );
		$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
		$params->def( 'description_text', _CONTACTS_DESC );
		$params->def( 'image', -1 );
		$params->def( 'image_align', 'right' );
		$params->def( 'other_cat_section', 1 );
		$params->def( 'other_cat', 1 );
		$params->def( 'cat_description', 1 );
		$params->def( 'cat_items', 1 );
		// Table Display control
		$params->def( 'headings', 1 );
		$params->def( 'position', '1' );
		$params->def( 'company', '1');
		$params->def( 'email', '0' );
		/** @CubaLibre: modified phone */
		$params->def( 'telephone', '1' );
		$params->def( 'mobile', '1' );
		$params->def( 'fax', '1' );
		

		if ( $catid ) {
			$params->set( 'type', 'category' );
		} else {
			$params->set( 'type', 'section' );
		}
		
		// page description
		$currentcat->descrip = '';
		if( ( @$currentcat->description ) <> '' ) {
			$currentcat->descrip = $currentcat->description;
		} else if ( !$catid ) {
			// show description
			if ( $params->get( 'description' ) ) {
				$currentcat->descrip = $params->get( 'description_text' );
			}
		}

		// page image
		$currentcat->img = '';
		$path = $mosConfig_live_site .'/images/stories/';
		if ( ( @$currentcat->image ) <> '' ) {
			$currentcat->img = $path . $currentcat->image;
			$currentcat->align = $currentcat->image_position;
		} else if ( !$catid ) {
			if ( $params->get( 'image' ) <> -1 ) {
				$currentcat->img = $path . $params->get( 'image' );
				$currentcat->align = $params->get( 'image_align' );
			}
		}

		// page header
		$currentcat->header = '';
		if ( @$currentcat->name <> '' ) {
			$currentcat->header = $params->get( 'header' ) .' - '. $currentcat->name;
		} else {
			$currentcat->header = $params->get( 'header' );
		}

		// used to show table rows in alternating colours
		$tabclass = array( 'sectiontableentry1', 'sectiontableentry2' );
		
		// Dynamic Page Title
		$mainframe->SetPageTitle( $menu->name );
		
		/** @CubaLibre: display page title */
		HTML_contxtd::displayheader( $params );
				
		/** @CubaLibre: changed class name */
		HTML_contxtd::displaylist( $categories, $rows, $catid, $currentcat, $params, $tabclass );
	}
}

function blogContacts( $id ) {
	global $database, $mainframe, $mosConfig_offset, $Itemid;

	// Parameters
	if ( $Itemid ) {
		$menu = new mosMenu( $database );
		$menu->load( $Itemid );
		$params =new  mosParameters( $menu->params );
	} else {
		$menu = "";
		$params =new  mosParameters( '' );
	}

	// new blog multiple section handling
	if ( !$id ) {
		$categories = (explode(',', $params->def( 'categoryid', 0 )));
		
		$query = "SELECT *"
		. "\n FROM #__categories AS cc"
		. "\n LEFT JOIN #__contxtd_details AS cd ON cd.catid = cc.id"
		. "\n WHERE cd.published='1'"
		. "\n AND cc.section='com_contxtd_details'"
		. "\n AND cc.published='1'"
		. "\n AND cd.access <= '". $my->gid ."'"
		. "\n AND cc.access <= '". $my->gid ."'"
		. "\n GROUP BY cd.id"
		. "\n ORDER BY cc.ordering, cd.ordering"
		;
	} else {
		$query = "SELECT *"
		. "\n FROM #__contxtd_details AS cd"
		. "\n WHERE cd.catid= '". $id ."'"
		. "\n AND cd.published='1'"
		. "\n AND cd.access <= '". $my->gid ."'"
		. "\n ORDER BY cd.ordering"
		;	
	}			

	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	// Dynamic Page Title
	$mainframe->SetPageTitle( $menu->name );
	
	/** @CubaLibre: display page title */
	HTML_contxtd::displayheader( $params );

	foreach( $rows as $row ) {
		contactpage( $row->id );
	}

}


function contactpage( $contact_id ) {
	global $mainframe, $database, $my, $Itemid, $task;
	
	/** @CubaLibre: changed table name */
	$query = "SELECT a.id AS value, CONCAT_WS( ' - ', a.name, a.con_position ) AS text"
	. "\n FROM #__contxtd_details AS a"
	. "\n LEFT JOIN #__categories AS cc ON cc.id = a.catid"
	. "\n WHERE a.published = '1'"
	. "\n AND cc.published = '1'"
	. "\n AND a.access <=". $my->gid
	. "\n AND cc.access <=". $my->gid
	. "\n ORDER BY a.default_con DESC, a.ordering ASC"
	;


	$database->setQuery( $query );
	$list = $database->loadObjectList();
	$count = count( $list );

	if ( $count ) {

		if ( $contact_id < 1 ) {
		    $contact_id = $list[0]->value;
		}
		/** @CubaLibre: changed table name */
		$query = "SELECT *"
		. "\n FROM #__contxtd_details"
		. "\n WHERE published = '1'"
		. "\n AND id = ".$contact_id
		. "\n AND access <=". $my->gid;
		$database->SetQuery($query);
		$contacts = $database->LoadObjectList();

		if (!$contacts){
			echo _NOT_AUTH;
			return;
		}
		$contact = $contacts[0];
		// creates dropdown select list
		$contact->select = mosHTML::selectList( $list, 'contact_id', 'class="inputbox" onchange="ViewCrossReference(this);"', 'value', 'text', $contact_id );

		// Adds parameter handling
		$params =new  mosParameters( $contact->params );

		/**
		* @CubaLibre: added parameters
		* @param company
		* @param telephone1
		* @param telephone2
		* @param website
		* @param mobile1
		* @param mobile2
		* @param misc1
		* @param misc2
		* @param email_redirect
		* @param icon_website
		* @param icon_mobile 
		*/ 
		$params->set( 'page_title', 0 );
		$params->def( 'pageclass_sfx', '' );
		$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
		$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'name', '1' );
		$params->def( 'company', '1' );
		$params->def( 'email', '0' );
		$params->def( 'street_address', '1' );
		$params->def( 'suburb', '1' );
		$params->def( 'state', '1' );
		$params->def( 'country', '1' );
		$params->def( 'postcode', '1' );
		$params->def( 'telephone1', '1' );
		$params->def( 'telephone2', '1' );
		$params->def( 'mobile1'. '1' );
		$params->def( 'mobile2', '1' );
		$params->def( 'website', '1' );
		$params->def( 'fax', '1' );
		$params->def( 'misc1', '1' );
		$params->def( 'misc2', '1' );
		$params->def( 'image', '1' );
		$params->def( 'email_description', '1' );
		$params->def( 'email_description_text', _EMAIL_DESCRIPTION );
		$params->def( 'email_form', '1' );
		$params->def( 'email_copy', '1' );
		$params->def( 'email_redirect', '1' );
		// global pront|pdf|email
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );
		// contact only icons
		$params->def( 'contact_icons', 0 );
		$params->def( 'icon_address', '' );
		$params->def( 'icon_email', '' );
		$params->def( 'icon_website', '' );
		$params->def( 'icon_telephone', '' );
		$params->def( 'icon_mobile', '' );
		$params->def( 'icon_fax', '' );
		$params->def( 'icon_misc', '' );
		$params->def( 'drop_down', '0' );
		/** @CubaLibre: changed default to off */
		$params->def( 'vcard', '0' );


		if ( $contact->email_to && $params->get( 'email' )) {
			// email cloacking
			$contact->email = mosHTML::emailCloaking( $contact->email_to );
		}

		// loads current template for the pop-up window
		$pop = mosGetParam( $_REQUEST, 'pop', 0 );
		if ( $pop ) {
			$params->set( 'popup', 1 );
			$params->set( 'back_button', 0 );
		}

		if ( $params->get( 'email_description' ) ) {
			$params->set( 'email_description', $params->get( 'email_description_text' ) );
		} else {
			$params->set( 'email_description', '' );
		}

		// needed to control the display of the Address marker
		$temp = $params->get( 'street_address' )
		. $params->get( 'suburb' )
		. $params->get( 'state' )
		. $params->get( 'country' )
		. $params->get( 'postcode' )
		;
		$params->set( 'address_check', $temp );

		// determines whether to use Text, Images or nothing to highlight the different info groups
		switch ( $params->get( 'contact_icons' ) ) {
			case 1:
			// text
				/**
				* @CubaLibre: added/modified parameters
				* @param marker_company
				* @param marker_website
				* @param marker_mobile
				* @param marker_misc2
				*/
				$params->set( 'marker_company', _CONTACT_COMPANY );
				$params->set( 'marker_address', _CONTACT_ADDRESS );
				$params->set( 'marker_email', _CONTACT_EMAIL );
				$params->set( 'marker_website', _CONTACT_WEBSITE );
				$params->set( 'marker_telephone', _CONTACT_TELEPHONE );
				$params->set( 'marker_mobile', _CONTACT_MOBILE);
				$params->set( 'marker_fax', _CONTACT_FAX );
				$params->set( 'marker_misc', _CONTACT_MISC );
				$params->set( 'marker_misc2', _CONTACT_MISC2 );
				$params->set( 'column_width', '100px' );
				break;
			case 2:
			// none
				/**
				* @CubaLibre: added/modified parameters
				* @param marker_company
				* @param marker_website
				* @param marker_mobile
				* @param marker_misc2
				*/
				$params->set( 'marker_company', '' );
				$params->set( 'marker_address', '' );
				$params->set( 'marker_email', '' );
				$params->set( 'marker_website', '' );
				$params->set( 'marker_telephone', '' );
				$params->set( 'marker_mobile', '');
				$params->set( 'marker_fax', '' );
				$params->set( 'marker_misc', '' );
				$params->set( 'marker_misc2', '' );
				$params->set( 'column_width', '0px' );
				break;
			default:
			// icons
				/**
				* @CubaLibre: added/modified images and parameters
				* @param marker_company
				* @param marker_website
				* @param marker_mobile
				* @param marker_misc2
				*/
				$image1 = mosAdminMenus::ImageCheck( 'con_address.png', '/images/M_images/', $params->get( 'icon_company' ) );
				$image2 = mosAdminMenus::ImageCheck( 'con_address.png', '/images/M_images/', $params->get( 'icon_address' ) );
				$image3 = mosAdminMenus::ImageCheck( 'emailButton.png', '/images/M_images/', $params->get( 'icon_email' ) );
				$image4 = mosAdminMenus::ImageCheck( 'weblink.png', '/images/M_images/', $params->get( 'icon_website' ) );
				$image5 = mosAdminMenus::ImageCheck( 'con_tel.png', '/images/M_images/', $params->get( 'icon_telephone' ) );
				$image6 = mosAdminMenus::ImageCheck( 'con_tel.png', '/images/M_images/', $params->get( 'icon_mobile' ) );
				$image7 = mosAdminMenus::ImageCheck( 'con_fax.png', '/images/M_images/', $params->get( 'icon_fax' ) );
				$image8 = mosAdminMenus::ImageCheck( 'con_info.png', '/images/M_images/', $params->get( 'icon_misc' ) );
				$image9 = mosAdminMenus::ImageCheck( 'con_info.png', '/images/M_images/', $params->get( 'icon_misc2' ) );
				$params->set( 'marker_company', $image1 );
				$params->set( 'marker_address', $image2 );
				$params->set( 'marker_email', $image3 );
				$params->set( 'marker_website', $image4 );
				$params->set( 'marker_telephone', $image5 );
				$params->set( 'marker_mobile', $image6 );
				$params->set( 'marker_fax', $image7 );
				$params->set( 'marker_misc', $image8 );
				$params->set( 'marker_misc2', $image9 );
				$params->set( 'column_width', '40px' );
				break;
		}

		// params from menu item
		$menu = new mosMenu( $database );
		$menu->load( $Itemid );	
		$menu_params =new  mosParameters( $menu->params );		
		
		$menu_params->def( 'page_title', 1 );
		$menu_params->def( 'header', $menu->name );
		$menu_params->def( 'pageclass_sfx', '' );
		
		// Dynamic Page Title
		$mainframe->SetPageTitle( $menu->name );
		
		/** @CubaLibre: changed class name */
		HTML_contxtd::viewcontact( $contact, $params, $count, $list, $menu_params, $task );
	} else {
		$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
		/** @CubaLibre: changed class name */
		HTML_contxtd::nocontact( $params );
	}
}

/**
* @CubaLibre: added parameter cat_id for redirect after sending email 
*/
function sendmail( $cat_id, $con_id, $option ) {
	global $database, $Itemid;
	global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_mailfrom, $mosConfig_fromname;
	/** @CubaLibre: changed table name */
	$query = "SELECT * FROM #__contxtd_details WHERE id='$con_id'";
	$database->setQuery( $query );
	$contact = $database->loadObjectList();

	$default = $mosConfig_sitename.' '. _ENQUIRY;
	$email = trim( mosGetParam( $_POST, 'email', '' ) );
	$text 	= trim( mosGetParam( $_POST, 'text', '' ) );
	$name = trim( mosGetParam( $_POST, 'name', '' ) );
	$subject = trim( mosGetParam( $_POST, 'subject', $default ) );
	$email_copy = mosGetParam( $_POST, 'email_copy', 0 );
	$email_redirect = mosGetParam( $_POST, 'email_redirect', 0 );

	if ( !$email || !$text || ( is_email( $email )==false ) ) {
		echo "<script>alert (\""._CONTACT_FORM_NC."\"); window.history.go(-1);</script>";
		exit(0);
	}
	$prefix = sprintf( _ENQUIRY_TEXT, $mosConfig_live_site );
	$text 	= $prefix ."\n". $name. ' <'. $email .'>' ."\n\n". stripslashes( $text );

	mosMail( $email, $name , $contact[0]->email_to, $mosConfig_fromname .': '. $subject, $text );

	if ( $email_copy ) {
		$copy_text = sprintf( _COPY_TEXT, $contact[0]->name, $mosConfig_sitename );
		$copy_text = $copy_text ."\n\n". $text .'';
		$copy_subject = _COPY_SUBJECT . $subject;
		mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $email, $copy_subject, $copy_text );
	}
	?>
	<script>
	alert( "<?php echo _THANK_MESSAGE; ?>" );
	<?php
	if ( $email_redirect ) {
		/**
		 * @CubaLibre: Return to contact details page
		 */
		 ?>
		 document.location.href='<?php echo sefRelToAbs( 'index.php?option='. $option .'&task=view&contact_id=' . $con_id .'&Itemid='. $Itemid ); ?>';
		 </script>
		 <?php
	} else {
		/**
		 * @CubaLibre: Return to contact category page
		 */
		 ?>
		 document.location.href='<?php echo sefRelToAbs( 'index.php?option='. $option .'&catid=' . $cat_id .'&Itemid='. $Itemid ); ?>';
		 </script>
		 <?php
	}
}


function is_email($email){
	$rBool=false;

	if  ( preg_match( "/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/" , $email ) ){
		$rBool=true;
	}
	return $rBool;
}

function vCard( $id ) {
	global $database;
	global $mosConfig_sitename, $mosConfig_absolute_path, $mosConfig_live_site;
	
	$contact = new mosContxtd( $database );
	$contact->load( $id );
	$name 	= explode( ' ', $contact->name );
	$count 	= count( $name );

	// handles conversion of name entry into firstname, surname, middlename distinction
	$surname	= '';
	$middlename	= '';
	
	switch( $count ) {
		case 1:
			$firstname		= $name[0];
			break;
			
		case 2:
			$firstname 		= $name[0];
			$surname		= $name[1];
			break;
			
		default:
			$firstname 		= $name[0];
			$surname		= $name[$count-1];
			for ( $i = 1; $i < $count - 1 ; $i++ ) {
				$middlename	.= $name[$i] .' ';
			}		
			break;
	}
	$middlename	= trim( $middlename );

	$v 	= new MambovCard();
	
	$v->setPhoneNumber( $contact->telephone1, 'WORK;VOICE' );
	/** @CubaLibre: added second phone info */
	$v->setPhoneNumber( $contact->telephone2, 'HOME;VOICE' );
	/** @CubaLibre: added first mobile phone info */
	$v->setPhoneNumber( $contact->mobile1, 'WORK;CELL' );
	/** @CubaLibre: added second mobile phone info */
	$v->setPhoneNumber( $contact->mobile2, 'HOME;CELL' );
	$v->setPhoneNumber( $contact->fax, 'WORK;FAX' );
	$v->setName( $surname, $firstname, $middlename, '' );
	$v->setAddress( '', '', $contact->address, $contact->suburb, $contact->state, $contact->postcode, $contact->country, 'WORK;POSTAL' );
	$v->setEmail( $contact->email_to );
	/** @CubaLibre: added second misc info */
	$v->setNote( $contact->misc1 ." - ". $contact->misc2);
	/** @CubaLibre: changed to reflect website info */
	$v->setURL( $contact->website, 'WORK' );
	$v->setTitle( $contact->con_position );
	/** @CubaLibre: changed to reflect company info */
	$v->setOrg( $contact->company );

	$filename	= str_replace( ' ', '_', $contact->name );
	$v->setFilename( $filename );
	
	$output 	= $v->getVCard( $mosConfig_sitename );
	$filename = $v->getFileName();

	// header info for page
	header( 'Content-Disposition: attachment; filename='. $filename );
	header( 'Content-Length: '. strlen( $output ) );
	header( 'Connection: close' );
	header( 'Content-Type: text/x-vCard; name='. $filename );
	
	print $output;
}

?>
