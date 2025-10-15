<?php
/**
* @version $Id: contact.php 4730 2006-08-24 21:25:37Z stingrey $
* @package Joomla
* @subpackage Contact
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

// load the html drawing class
require_once( $mainframe->getPath( 'front_html' ) );
require_once( $mainframe->getPath( 'class' ) );

$mainframe->setPageTitle( _CONTACT_TITLE );


//Load Vars
$op			= strval( mosGetParam( $_REQUEST, 'op', '' ) );
$con_id 	= intval( mosGetParam( $_REQUEST ,'con_id', 0 ) );
$contact_id = intval( mosGetParam( $_REQUEST ,'contact_id', 0 ) );
$catid 		= intval( mosGetParam( $_REQUEST ,'catid', 0 ) );

switch( $op ) {
	case 'sendmail':
		sendmail( $con_id, $option );
		break;
}

switch( $task ) {
    	case 'check_company_discount':
            check_company_discount();
            break;
            
	case 'corporate_account':
            CorporateAccount();
            break;

	case 'view':
		contactpage( $contact_id );
		break;

    case 'need_help':
        needHelp();
        break;
	case 'vcard':
		vCard( $contact_id );
		break;

	default:
		listContacts( $option, $catid );
		break;
}
function needHelp()
{
    global $database, $mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_need_help, $mosConfig_google_captcha_backend;

    $email = (string)mosGetParam($_REQUEST, 'email', '');
    $name = (string)mosGetParam($_REQUEST, 'name', '');
    $phone = (string)mosGetParam($_REQUEST, 'phone', '');
    $message = (string)mosGetParam($_REQUEST, 'message', '');
    $policy = (int)mosGetParam($_REQUEST, 'policy', 0);
    $orderNumber = (string)mosGetParam($_REQUEST, 'order_number', '');
    $recaptchaResponse = isset($_REQUEST['g-recaptcha-response']) ? $_REQUEST['g-recaptcha-response'] : '';

    // Basic validations
    if ($email == '' || $name == '') {
        mosRedirect('/contact/','User Name and Email required fields.');
        return;
    }
    if ($message == '') {
        mosRedirect('/contact/','Message is a required field.');
        return;
    }

    if ($recaptchaResponse === '') {
        mosRedirect('/contact/','Captcha verification failed.');
        return;
    }
    $recaptchaSecretKey = $mosConfig_google_captcha_backend ?? '6LdJvGgUAAAAAPH2Fo5RBuQy_EIkhm-6wgQuineo';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=".$recaptchaSecretKey."&response=" . $recaptchaResponse . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    if(curl_error($ch))
    {
        mosRedirect('/contact/','Captcha verification error.');
        return;
    }
    $capcha_res = json_decode($server_output);
    if (!$capcha_res || empty($capcha_res->success))
    {
        mosRedirect('/contact/','Incorrect CAPTCHA');
        return;
    }

    $query = sprintf(
        "UPDATE jos_users SET Marketing_Opt_in='%s' WHERE email='%s' OR username='%s'",
        $policy,
        $email,
        $email
    );
    $database->setQuery($query);
    $database->query();

    // Build email body
    $emailMsg = sprintf("<b>Email</b>: %s<br> <b>Agreement Checkbox Checked</b>: %s<br> <b>Name</b>: %s<br> <b>Phone</b>: %s<br> <b>Order number</b>: %s<br> <b>Message</b>: %s",
        htmlspecialchars($email),
        $policy ? 'Yes' : 'No',
        htmlspecialchars($name),
        htmlspecialchars($phone),
        htmlspecialchars($orderNumber),
        nl2br(htmlspecialchars($message))
    );

    // Handle attachments: max 10 files, each up to 15MB
    $attachments = array();
    $tempFilesToCleanup = array();
    if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
        $names = $_FILES['attachments']['name'];
        $sizes = $_FILES['attachments']['size'];
        $tmps  = $_FILES['attachments']['tmp_name'];
        $errs  = $_FILES['attachments']['error'];

        $fileCount = 0;
        for ($i = 0; $i < count($names); $i++) {
            if ($errs[$i] === UPLOAD_ERR_NO_FILE) { continue; }
            if ($errs[$i] !== UPLOAD_ERR_OK) {
                mosRedirect('/contact/','There was an error uploading one of your files.');
                return;
            }
            $fileCount++;
            if ($fileCount > 10) {
                mosRedirect('/contact/','You can upload up to 10 files.');
                return;
            }
            if ($sizes[$i] > 15 * 1024 * 1024) {
                mosRedirect('/contact/','One of the files exceeds the 15MB limit.');
                return;
            }
            if (is_uploaded_file($tmps[$i])) {
                // Preserve original filename by copying to a temp path with that basename
                $originalName = basename($names[$i]);
                $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
                $tempDir = sys_get_temp_dir();
                $destPath = rtrim($tempDir, '/').'/contact_'.uniqid('', true).'_'.$safeName;
                if (!@move_uploaded_file($tmps[$i], $destPath)) {
                    // Fallback to copy if move_uploaded_file fails (e.g. already moved)
                    if (!@copy($tmps[$i], $destPath)) {
                        mosRedirect('/contact/','There was an error saving one of your files.');
                        return;
                    }
                }
                $attachments[] = $destPath;
                $tempFilesToCleanup[] = $destPath;
            }
        }
    }

    // Send email with attachments; set reply-to to the user
    $sendResult = mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_need_help, 'Need Help? Contact Us Here', $emailMsg, 1, NULL, NULL, (count($attachments) ? $attachments : NULL), $email, $name);

    // Cleanup temp files
    if (!empty($tempFilesToCleanup)) {
        foreach ($tempFilesToCleanup as $f) {
            if (is_file($f)) { @unlink($f); }
        }
    }

    setcookie('klaviyoEmail', $email, time() + (86400 * 30), "/");
    mosRedirect('/contact/?mosmsgsuccess=true&mosmsg=Your request has been registered and forwarded to specialists.');
}
function check_company_discount() {
    $return = (object)array(
        'result' => false
    );
    
    $email = mosGetParam($_REQUEST, 'email', '');
    $group_id = mosGetuserShoperGroupId($email);
    
    if ($group_id != false) {
        $return->result = true;
    }
    
    echo json_encode($return);
    die;
}

function listContacts( $option, $catid ) {
	global $mainframe, $database, $my;
	global $mosConfig_live_site;
	global $Itemid;

	/* Query to retrieve all categories that belong under the contacts section and that are published. */
	$query = "SELECT *, COUNT( a.id ) AS numlinks"
	. "\n FROM #__categories AS cc"
	. "\n LEFT JOIN #__contact_details AS a ON a.catid = cc.id"
	. "\n WHERE a.published = 1"
	. "\n AND cc.section = 'com_contact_details'"
	. "\n AND cc.published = 1"
	. "\n AND a.access <= $my->gid"
	. "\n AND cc.access <= $my->gid"
	. "\n GROUP BY cc.id"
	. "\n ORDER BY cc.ordering"
	;
	$database->setQuery( $query );
	$categories = $database->loadObjectList();

	$count = count( $categories );

	 if($option == 'com_contact') {
            tileContacts();    
		// if only one record exists loads that record, instead of displying category list
		//contactpage( $option, 0 );
	} else {
		$rows 		= array();
		$currentcat = NULL;

		// Parameters
		$menu = $mainframe->get( 'menu' );
		$params = new mosParameters( $menu->params );

		$params->def( 'page_title', 		1 );
		$params->def( 'header', 			$menu->name );
		$params->def( 'pageclass_sfx', 		'' );
		$params->def( 'headings', 			1 );
		$params->def( 'back_button', 		$mainframe->getCfg( 'back_button' ) );
		$params->def( 'description_text', 	_CONTACTS_DESC );
		$params->def( 'image', 				-1 );
		$params->def( 'image_align', 		'right' );
		$params->def( 'other_cat_section', 	1 );
		// Category List Display control
		$params->def( 'other_cat', 			1 );
		$params->def( 'cat_description', 	1 );
		$params->def( 'cat_items', 			1 );
		// Table Display control
		$params->def( 'headings', 			1 );
		$params->def( 'position', 			1 );
		$params->def( 'email', 				0 );
		$params->def( 'phone', 				1 );
		$params->def( 'fax', 				1 );
		$params->def( 'telephone', 			1 );

		if( $catid == 0 ) {
			$catid = $params->get( 'catid', 0 );
		}

		if ( $catid ) {
			$params->set( 'type', 'category' );
		} else {
			$params->set( 'type', 'section' );
		}

		if ( $catid ) {
			// url links info for category
			$query = "SELECT *"
			. "\n FROM #__contact_details"
			. "\n WHERE catid = $catid"
			 . "\n AND published =1"
			 . "\n AND access <= $my->gid"
			. "\n ORDER BY ordering"
			;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			// current category info
			$query = "SELECT id, name, description, image, image_position"
			. "\n FROM #__categories"
			. "\n WHERE id = $catid"
			. "\n AND published = 1"
			. "\n AND access <= $my->gid"
			;
			$database->setQuery( $query );
			$database->loadObject( $currentcat );

			/*
			Check if the category is published or if access level allows access
			*/
			if (!$currentcat->name) {
				mosNotAuth();
				return;
			}
		}

		// page description
		$currentcat->descrip = '';
		if( isset($currentcat->description) && ($currentcat->description != '') ) {
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
		if ( isset($currentcat->image) && ($currentcat->image != '') ) {
			$currentcat->img = $path . $currentcat->image;
			$currentcat->align = $currentcat->image_position;
		} else if ( !$catid ) {
			if ( $params->get( 'image' ) != -1 ) {
				$currentcat->img = $path . $params->get( 'image' );
				$currentcat->align = $params->get( 'image_align' );
			}
		}

		// page header
		$currentcat->header = '';
		if ( isset($currentcat->name) && ($currentcat->name != '') ) {
			$currentcat->header = $params->get( 'header' ) .' - '. $currentcat->name;
		} else {
			$currentcat->header = $params->get( 'header' );
		}

		// used to show table rows in alternating colours
		$tabclass = array( 'sectiontableentry1', 'sectiontableentry2' );

		HTML_contact::displaylist( $categories, $rows, $catid, $currentcat, $params, $tabclass );
	}
}

function tileContacts() {
        global $mainframe, $database, $my, $Itemid;

        $query = "SELECT * FROM #__contact_details where published = 1 order by ordering";
        
        $database->setQuery( $query );
	$rows = $database->loadObjectList();  
  
    
    HTML_contact::displayTile($rows);
    
}
function contactpage( $contact_id ) {
	global $mainframe, $database, $my, $Itemid;

	$query = "SELECT a.id AS value, CONCAT_WS( ' - ', a.name, a.con_position ) AS text, a.catid, cc.access AS cat_access"
	. "\n FROM #__contact_details AS a"
	. "\n LEFT JOIN #__categories AS cc ON cc.id = a.catid"
	. "\n WHERE a.published = 1"
	. "\n AND cc.published = 1"
	. "\n AND a.access <= $my->gid"
	. "\n ORDER BY a.default_con DESC, a.ordering ASC"
	;
	$database->setQuery( $query );
	$checks = $database->loadObjectList();

	$count = count( $checks );
	if ($count) {
		if ($contact_id < 1) {
			$contact_id = $checks[0]->value;
		}

		$query = "SELECT a.*, cc.access AS cat_access"
		. "\n FROM #__contact_details AS a"
		. "\n LEFT JOIN #__categories AS cc ON cc.id = a.catid"
		. "\n WHERE a.published = 1"
		. "\n AND a.id = $contact_id"
		. "\n AND a.access <= $my->gid"
		;
		$database->SetQuery($query);
		$contacts = $database->LoadObjectList();

		if (!$contacts){
			echo _NOT_AUTH;
			return;
		}
		$contact = $contacts[0];	
			
		/*
		* check whether category access level allows access
		*/
		if ( $contact->cat_access > $my->gid ) {	
			mosNotAuth();  
			return;
		}

		$list = array();
		foreach ( $checks as $check ) {
			if ( $check->catid == $contact->catid ) {
				$list[] = $check;
			}
		}		
		// creates dropdown select list
		$contact->select = mosHTML::selectList( $list, 'contact_id', 'class="inputbox" onchange="ViewCrossReference(this);"', 'value', 'text', $contact_id );

		// Adds parameter handling
		$params = new mosParameters( $contact->params );

		$params->set( 'page_title', 			0 );
		$params->def( 'pageclass_sfx', 			'' );
		$params->def( 'back_button', 			$mainframe->getCfg( 'back_button' ) );
		$params->def( 'print', 					!$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'name', 					1 );
		$params->def( 'email', 					0 );
		$params->def( 'street_address', 		1 );
		$params->def( 'suburb', 				1 );
		$params->def( 'state', 					1 );
		$params->def( 'country', 				1 );
		$params->def( 'postcode', 				1 );
		$params->def( 'telephone', 				1 );
		$params->def( 'fax', 					1 );
		$params->def( 'misc', 					1 );
		$params->def( 'image', 					1 );
		$params->def( 'email_description', 		1 );
		$params->def( 'email_description_text', _EMAIL_DESCRIPTION );
		$params->def( 'email_form', 			1 );
		$params->def( 'email_copy', 			0 );
		// global pront|pdf|email
		$params->def( 'icons', 					$mainframe->getCfg( 'icons' ) );
		// contact only icons
		$params->def( 'contact_icons', 			0 );
		$params->def( 'icon_address', 			'' );
		$params->def( 'icon_email', 			'' );
		$params->def( 'icon_telephone', 		'' );
		$params->def( 'icon_fax', 				'' );
		$params->def( 'icon_misc', 				'' );
		$params->def( 'drop_down', 				0 );
		$params->def( 'vcard', 					0 );


		if ( $contact->email_to && $params->get( 'email' )) {
			// email cloacking
			$contact->email = mosHTML::emailCloaking( $contact->email_to );
		}

		// loads current template for the pop-up window
		$pop = intval( mosGetParam( $_REQUEST, 'pop', 0 ) );
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
				$params->set( 'marker_address', _CONTACT_ADDRESS );
				$params->set( 'marker_email', _CONTACT_EMAIL );
				$params->set( 'marker_telephone', _CONTACT_TELEPHONE );
				$params->set( 'marker_fax', _CONTACT_FAX );
				$params->set( 'marker_misc', _CONTACT_MISC );
				$params->set( 'column_width', '100' );
				break;
			case 2:
			// none
				$params->set( 'marker_address', '' );
				$params->set( 'marker_email', '' );
				$params->set( 'marker_telephone', '' );
				$params->set( 'marker_fax', '' );
				$params->set( 'marker_misc', '' );
				$params->set( 'column_width', '0' );
				break;
			default:
			// icons
				$image1 = mosAdminMenus::ImageCheck( 'con_address.png', '/images/M_images/', $params->get( 'icon_address' ), '/images/M_images/', _CONTACT_ADDRESS, _CONTACT_ADDRESS );
				$image2 = mosAdminMenus::ImageCheck( 'emailButton.png', '/images/M_images/', $params->get( 'icon_email' ), '/images/M_images/', _CONTACT_EMAIL, _CONTACT_EMAIL );
				$image3 = mosAdminMenus::ImageCheck( 'con_tel.png', '/images/M_images/', $params->get( 'icon_telephone' ), '/images/M_images/', _CONTACT_TELEPHONE, _CONTACT_TELEPHONE );
				$image4 = mosAdminMenus::ImageCheck( 'con_fax.png', '/images/M_images/', $params->get( 'icon_fax' ), '/images/M_images/', _CONTACT_FAX, _CONTACT_FAX );
				$image5 = mosAdminMenus::ImageCheck( 'con_info.png', '/images/M_images/', $params->get( 'icon_misc' ), '/images/M_images/', _CONTACT_MISC, _CONTACT_MISC );
				$params->set( 'marker_address', $image1 );
				$params->set( 'marker_email', $image2 );
				$params->set( 'marker_telephone', $image3 );
				$params->set( 'marker_fax', $image4 );
				$params->set( 'marker_misc', $image5 );
				$params->set( 'column_width', '40' );
				break;
		}

		// params from menu item
		$menu 			= $mainframe->get( 'menu' );
		$menu_params 	= new mosParameters( $menu->params );

		$menu_params->def( 'page_title', 1 );
		$menu_params->def( 'header', $menu->name );
		$menu_params->def( 'pageclass_sfx', '' );

		HTML_contact::viewcontact( $contact, $params, $count, $list, $menu_params );
	} else {
		$params = new mosParameters( '' );
		$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
		HTML_contact::nocontact( $params );
	}
}


function sendmail( $con_id, $option ) {
	global $mainframe, $database, $Itemid;
	global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_db;

	// simple spoof check security
	josSpoofCheck(1);

	$query = "SELECT *"
	. "\n FROM #__contact_details"
	. "\n WHERE id = $con_id"
	;
	$database->setQuery( $query );
	$contact 	= $database->loadObjectList();

	if (count( $contact ) > 0) {
		$default 	= $mosConfig_sitename.' '. _ENQUIRY;
		$email 		= strval( mosGetParam( $_POST, 'email', 		'' ) );
		$text 		= strval( mosGetParam( $_POST, 'text', 			'' ) );
		$name 		= strval( mosGetParam( $_POST, 'name', 			'' ) );
		$subject 	= strval( mosGetParam( $_POST, 'subject', 		$default ) );
		$email_copy = strval( mosGetParam( $_POST, 'email_copy', 	0 ) );

		$menu 			= $mainframe->get( 'menu' );
		$mparams 		= new mosParameters( $menu->params );
		$bannedEmail 	= $mparams->get( 'bannedEmail', 	'' );
		$bannedSubject 	= $mparams->get( 'bannedSubject', 	'' );
		$bannedText 	= $mparams->get( 'bannedText', 		'' );
		$sessionCheck 	= $mparams->get( 'sessionCheck', 	1 );

		// check for session cookie
		if  ( $sessionCheck ) {
			// Session Cookie `name`
			$sessionCookieName 	= mosMainFrame::sessionCookieName();
			// Get Session Cookie `value`
			$sessioncookie 		= mosGetParam( $_COOKIE, $sessionCookieName, null );

			if ( !(strlen($sessioncookie) == 32 || $sessioncookie == '-') ) {
				mosErrorAlert( _NOT_AUTH );
			}
		}

		// Prevent form submission if one of the banned text is discovered in the email field
		if ( $bannedEmail ) {
			$bannedEmail = explode( ';', $bannedEmail );
			foreach ($bannedEmail as $value) {
				if ( stristr($email, $value) ) {
					mosErrorAlert( _NOT_AUTH );
				}
			}
		}
		// Prevent form submission if one of the banned text is discovered in the subject field
		if ( $bannedSubject ) {
			$bannedSubject = explode( ';', $bannedSubject );
			foreach ($bannedSubject as $value) {
				if ( stristr($subject, $value) ) {
					mosErrorAlert( _NOT_AUTH );
				}
			}
		}
		// Prevent form submission if one of the banned text is discovered in the text field
		if ( $bannedText ) {
			$bannedText = explode( ';', $bannedText );
			foreach ($bannedText as $value) {
				if ( stristr($text, $value) ) {
					mosErrorAlert( _NOT_AUTH );
				}
			}
		}

		// test to ensure that only one email address is entered
		$check = explode( '@', $email );
		if ( strpos( $email, ';' ) || strpos( $email, ',' ) || strpos( $email, ' ' ) || count( $check ) > 2 ) {
			mosErrorAlert( _CONTACT_MORE_THAN );
		}

		if ( !$email || !$text || ( JosIsValidEmail( $email ) == false ) ) {
			mosErrorAlert( _CONTACT_FORM_NC );
		}
		$prefix = sprintf( _ENQUIRY_TEXT, $mosConfig_live_site );
		$text 	= $prefix ."\n". $name. ' <'. $email .'>' ."\n\n". stripslashes( $text );

		$success = mosMail( $email, $name , $contact[0]->email_to, $mosConfig_fromname .': '. $subject, $text );
		if (!$success) {
			mosErrorAlert( _CONTACT_FORM_NC );
		}

		// parameter check
		$params = new mosParameters( $contact[0]->params );
		$emailcopyCheck = $params->get( 'email_copy', 0 );

		// check whether email copy function activated
		if ( $email_copy && $emailcopyCheck ) {
			$copy_text = sprintf( _COPY_TEXT, $contact[0]->name, $mosConfig_sitename );
			$copy_text = $copy_text ."\n\n". $text .'';
			$copy_subject = _COPY_SUBJECT . $subject;

			$success = mosMail( $mosConfig_mailfrom_noreply, $mosConfig_fromname, $email, $copy_subject, $copy_text );
			if (!$success) {
				mosErrorAlert( _CONTACT_FORM_NC );
			}
		}
		
		$link = sefRelToAbs( 'index.php?option=com_contact&task=view&contact_id='. $contact[0]->id .'&Itemid='. $Itemid );

		mosRedirect( $link, _THANK_MESSAGE );
	}
}

function CorporateAccount(  ) {
    global $mainframe, $database, $Itemid,$acl;
    global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_db, $my,$mosConfig_google_captcha_backend;
    if(!strpos($_SERVER['HTTP_USER_AGENT'],'Firefox') && !strpos($_SERVER['HTTP_USER_AGENT'],'Trident')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=".$mosConfig_google_captcha_backend."&response=" . $_REQUEST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if(curl_error($ch))
        {
            die('error:' . curl_error($ch));
        }
        $capcha_res = json_decode($server_output);
        if (!$capcha_res->success)
        {
            die("Incorrect CAPTCHA");
        }
    }


	$first_name 		= strval( mosGetParam( $_POST, 'first_name', 	'' ) );
	$last_name 		= strval( mosGetParam( $_POST, 'last_name', 		'' ) );
	$title 			= strval( mosGetParam( $_POST, 'title', 			'' ) );
	$company 		= strval( mosGetParam( $_POST, 'company', 		'' ) );
	$phone 			= strval( mosGetParam( $_POST, 'phone', 		'' ) );
	$phone2 		= strval( mosGetParam( $_POST, 'phone2', 		'' ) );
	$phone3 		= strval( mosGetParam( $_POST, 'phone3', 		'' ) );
	$email 			= strval( mosGetParam( $_POST, 'uname', 			'' ) );
	$city			= strval( mosGetParam( $_POST, 'city', 			'' ) );
	$state			= strval( mosGetParam( $_POST, 'state', 			'' ) );
	$uname			= strval( mosGetParam( $_POST, 'email', 		'' ) );
	$upass			= strval( mosGetParam( $_POST, 'upass', 			'' ) );
        $corporateapp		= (int)$_POST['corporateapp'];
	$shopper_group_id	= (int)$_POST['shopper_group_id'];
	
	//Ticket #5707: Corporate Page modifications
	//Create  Account is "Corporate"
	$data = array(
            'name' => $database->getEscaped($first_name . " ". $last_name), 
            'username' => $database->getEscaped($uname), 
            'email' => $database->getEscaped($uname), 
            'password' => $upass, 
            'password2' => $upass
        );
        
	$row = new mosUser( $database );
        
	if (!$row->bind($data)) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
        
        
	//$row->id = '';
	//$row->usertype = '';
	$row->gid = $acl->get_group_id('Registered','ARO');

        
	if (!$row->check()) {
            echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
            exit();
        }
        
        
	$password 			= $upass;
	$row->password 		= md5($password);
	$row->registerDate 	= date("Y-m-d H:i:s");
        
	if (!$row->store()) {
            echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
            exit();
	}

        $row->checkin();
        
	$new_user_id = $row->id;

	$hash_secret 	= "VirtueMartIsCool";
        $phoneFull = $phone . $phone2 . $phone3;
	$q = "INSERT INTO #__vm_user_info
        (
            `user_info_id`, 
            `user_id`, 
            `address_type`, 
            `company`, 
            `title`, 
            `state`, 
            `country`, 
            `last_name`, 
            `first_name`, 
            `phone_1`, 
            `city`, 
            `user_email`, 
            `perms` 
        ) 
        VALUES (
            '".md5(uniqid($hash_secret))."',
            '".$new_user_id."', 
            'BT', 
            '".$database->getEscaped($company)."', 
            '".$database->getEscaped($title)."', 
            '".$database->getEscaped($state)."', 
            'AUS', 
            '".$database->getEscaped($last_name)."', 
            '".$database->getEscaped($first_name)."', 
            '".$database->getEscaped($phoneFull)."', 
            '".$database->getEscaped($city)."', 
            '".$database->getEscaped($uname)."', 
            'shopper'
        )
        ";
	$database->setQuery($q);
	$database->query();	
	
        if ($corporateapp > 0) {
            $query = "INSERT INTO `jos_corporateapp_users_xref`
            (
                `corporate_id`,
                `user_id`
            ) 
            VALUES ( 
                ".$corporateapp.",
                ".$new_user_id."
            )";		
            
            $database->setQuery($query);
            $database->query();
        }
        else {
//            $q 					= "SELECT shopper_group_id FROM #__vm_shopper_group WHERE shopper_group_name = 'Corporate'";
//            $database->setQuery($q);
//            $shopper_group_id	= $database->loadResult();
        
            $shopper_group_id = 5;
        }
	
	$q 			= "INSERT INTO #__vm_shopper_vendor_xref(user_id, vendor_id, shopper_group_id ) VALUES ( $new_user_id, 1, $shopper_group_id) ";		
	$database->setQuery($q);
	$database->query();
	//End of Ticket #5707
	
	
	$subject  	= "A new sign up for Corporate Account Manager  from ". $first_name . " " . $last_name;
	$body 		= "A new sign up for Corporate Account Manager  from ". $first_name . " " . $last_name . " and contains the following data:\r\n\r\n";
	$body 		.= "First Name: " . $first_name . "\r\n\r\n";
	$body 		.= "Last Name: " . $last_name . "\r\n\r\n";
	$body 		.= "Title: " . $title . "\r\n\r\n";
	$body 		.= "Company: " . $company . "\r\n\r\n";
	$body 		.= "Phone: (" . $phone . ") $phone2 $phone3\r\n\r\n";
	$body 		.= "E-mail: " . $email . "\r\n\r\n";
	$body 		.= "City: " . $city . "\r\n\r\n";
	$body 		.= "State/Province: " . $state . "\r\n\r\n";
		
	$success = mosMail( $mosConfig_mailfrom, $mosConfig_fromname , "corporate@bloomex.com.au", $subject, $body );
	//$success = mosMail( $email, $first_name . " " . $last_name , "imx47mail@gmail.com", $subject, $body );
//	echo $body."<br/><br/>";
	
	
	$subject  	= "New Corporate Account Pending";
	$body 		= "Thank You - your application for a Bloomex Corporate Account has been received. A dedicated Corporate Account Manager will contact you shortly to introduce themselves, present our product selection, answer any questions you may have and finalize the application process.\r\n\r\n";
	$body 		.= "Username: " . $uname . "\r\n";
	$body 		.= "Password: " .  $password. "\r\n\r\n";
	$body 		.= "If you have any questions in the meantime or wish to place an order with multiple address deliveries please call 1-800-768-357.\r\n\r\n";
	$body 		.= "Best Regards,\r\n\r\n";
	$body 		.= "Bloomex Corporate Sales Team\r\n";
	$body 		.= "1-800-768-357";

        if ($corporateapp > 0) {
            $subject  	= "Bloomex Partnerships <corporate@bloomex.com.au>";
            
            $body 		= "Thank You - your application for a Bloomex Partnership Account is activated.\r\n\r\n";
            $body 		.= "Username: " . $email . "\r\n";
            $body 		.= "Password: " . $password . "\r\n\r\n";
            $body 		.= "Best Regards,\r\n\r\n";
            $body 		.= "Bloomex Partnership Team\r\n";
            $body 		.= "1-866-690-8426";
        }

	$success = mosMail( "corporate@bloomex.com.au", $mosConfig_fromname , $email, $subject, $body );
	//$success = mosMail( $email, $first_name . " " . $last_name , "imx47mail@gmail.com", $subject, $body );

	//$link = sefRelToAbs( 'index.php?option=com_content&task=view&id=41&Itemid=242' ) . "&success=1";
	//$link = sefRelToAbs( 'index.php?page=shop.browse&category_id=171&option=com_virtuemart&Itemid=124' );	
	//die($success);/index.php?option=com_login&Itemid=295
    $msg='?mosmsgsuccess=true&mosmsg=Thank You - your application for a Bloomex Corporate Account has been received. A dedicated Corporate Account Manager will contact you shortly to introduce themselves, present our product selection, answer any questions you may have and finalize the application process.';

    if ($corporateapp > 0) {
            $mainframe->login_social($new_user_id);
            $link = 'index.php';
        }
        else {
            $link = sefRelToAbs( '/account/'.$msg );
        }
        mosRedirect( $link);
}

function vCard( $id ) {
	global $database;
	global $mosConfig_sitename, $mosConfig_live_site;

	$contact	= new mosContact( $database );
	$contact->load( (int)$id );	
	$params = new mosParameters( $contact->params );
	
	$show = $params->get( 'vcard', 0 );	
	if ( $show ) {	
	// check to see if VCard option hsa been activated
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
	
		$v->setPhoneNumber( $contact->telephone, 'PREF;WORK;VOICE' );
		$v->setPhoneNumber( $contact->fax, 'WORK;FAX' );
		$v->setName( $surname, $firstname, $middlename, '' );
		$v->setAddress( '', '', $contact->address, $contact->suburb, $contact->state, $contact->postcode, $contact->country, 'WORK;POSTAL' );
		$v->setEmail( $contact->email_to );
		$v->setNote( $contact->misc );
		$v->setURL( $mosConfig_live_site, 'WORK' );
		$v->setTitle( $contact->con_position );
		$v->setOrg( $mosConfig_sitename );
	
		$filename	= str_replace( ' ', '_', $contact->name );
		$v->setFilename( $filename );
	
		$output 	= $v->getVCard( $mosConfig_sitename );
		$filename 	= $v->getFileName();
	
		// header info for page
		header( 'Content-Disposition: attachment; filename='. $filename );
		header( 'Content-Length: '. strlen( $output ) );
		header( 'Connection: close' );
		header( 'Content-Type: text/x-vCard; name='. $filename );	
		header( 'Cache-Control: store, cache' );
		header( 'Pragma: cache' );
	
		print $output;
	} else {
		mosNotAuth();  
		return;
	}
}
?>