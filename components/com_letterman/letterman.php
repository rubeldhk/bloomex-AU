<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* Letterman Newsletter Component
* 
* @package Letterman
* @author Soeren
* @copyright Soeren Eberhardt <soeren@virtuemart.net>
    (who just needed an easy and *working* Newsletter component for Mambo 4.5.1 and mixed up Newsletter and YaNC)
* @copyright Mark Lindeman <mark@pictura-dp.nl> 
    (parts of the Newsletter component by Mark Lindeman; Pictura Database Publishing bv, Heiloo the Netherland)
* @copyright Adam van Dongen <adam@tim-online.nl>
    (parts of the YaNC component by Adam van Dongen, www.tim-online.nl)
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
*/

// load the html drawing class, MUST include the option for components
require_once( $mosConfig_absolute_path . '/components/com_letterman/letterman.html.php');
require_once( $mosConfig_absolute_path . '/components/com_letterman/letterman.class.php');

// Load configuration in constructor
$letterman = new mosLetterman($database);

if( !@include( $mosConfig_absolute_path . "/administrator/components/com_letterman/language/$mosConfig_lang.messages.php" ) ) {
	include( $mosConfig_absolute_path . "/administrator/components/com_letterman/language/english.messages.php" );
}

/* END CONFIG */
$pop = mosGetParam( $_REQUEST, 'pop', 0 );
$access = !$mainframe->getCfg( 'shownoauth' );
$task = trim( mosGetParam( $_REQUEST, 'task', "" ) );
$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
$subscriber = mosGetParam( $_REQUEST, 'subscriber', '' );
$limit 		= intval( mosGetParam( $_REQUEST, 'limit', $mosConfig_list_limit ) );
$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

$letterman_rights = Array();
// Editor usertype check
$letterman_rights['is_editor'] = $is_editor = (strtolower($my->usertype) == 'editor' || strtolower($my->usertype) == 'administrator' || strtolower($my->usertype) == 'super administrator' );
// Sender usertype check
$letterman_rights['is_sender'] = (strtolower($my->usertype) == 'manager' || strtolower($my->usertype) == 'administrator' || strtolower($my->usertype) == 'super administrator' );
// Who can delete?
$letterman_rights['can_delete'] = (strtolower($my->usertype) == 'administrator' || strtolower($my->usertype) == 'super administrator' );

$GLOBALS['Itemid'] = intval( mosGetParam( $_REQUEST, 'Itemid' ) );
if($GLOBALS['Itemid']== "" ) {
    $database->setQuery ( "SELECT id FROM #__menu WHERE link LIKE '%com_letterman%'" );
     $database->loadObject($myid);
    if( !empty($myid->id))
        $GLOBALS['Itemid'] = $myid->id;
    else
        $GLOBALS['Itemid'] = 1;
}

$database->setQuery ( "SELECT name FROM #__menu WHERE id='$Itemid'" );
$menuname = $database->loadResult();

HTML_letterman::header();

switch ($task) {
    case 'view':
		showItem( $id, $gid, $is_editor, $pop, $option );
        // showItem ( $id );
        break;
        
	case "edit":
		if( $letterman_rights['is_editor'] )
            editNewsletter( $id, $option );
		break;

	case "save":
        if( $letterman_rights['is_editor'] )
            saveNewsletter( $option );
		break;
      
	case "cancel":
        if( $letterman_rights['is_editor'] )
            cancelNewsletter( $option );
		break;  
        
	case "sendNow":
		if( $letterman_rights['is_sender'] ) {
            HTML_letterman::send_bar( $id );
            lm_sendNewsletter( $id, $option );
        }
		break;

	case "sendMail":
		if( $letterman_rights['is_sender'] )
            lm_sendMail();
		break;

	case "remove":
		if( $letterman_rights['can_delete'] )
            removeNewsletter( $id, $option );
		break;

    case 'subscribe': 
        HTML_letterman::subscribe( $subscriber); 
        break;
        
    case 'unsubscribe';
        HTML_letterman::unsubscribe( $subscriber);
        break;

    case 'confirm': 
        confirmSubscriber( $subscriber ); 
        break;
        
    default:
        HTML_letterman::subscriber_bar();
        if( $letterman_rights['is_editor'] ) {
            HTML_letterman::new_bar();
        }
        listAll( $letterman_rights );
}

HTML_letterman::footer();

function lm_email_check($email){
	// First, we check that there's one @ symbol, and that the lengths are right
	if (!preg_match("/[^@]{1,64}@[^@]{1,255}/", $email)) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!preg_match("/^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
			return false;
		}
	}
	if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}
function extended_email_check( $email ) {
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_mailfrom;
	
   	require_once( $mosConfig_absolute_path.'/administrator/components/com_letterman/includes/email_validation.php');
	$validator =new  email_validation_class();
	$localdata = parse_url( $mosConfig_live_site );
	$tmp = explode( '@', $mosConfig_mailfrom );
	$mailuser = $tmp[0];
	$mailserver = $tmp[1];
	
    /* how many seconds to wait before each attempt to connect to the
	   destination e-mail server */
	$validator->timeout=10;

	/* how many seconds to wait for data exchanged with the server.
	   set to a non zero value if the data timeout will be different
		 than the connection timeout. */
	$validator->data_timeout=0;

	/* user part of the e-mail address of the sending user
	   (info@phpclasses.org in this example) */
	$validator->localuser=$mailuser;

	/* domain part of the e-mail address of the sending user */
	$validator->localhost=$mailserver;

	/* Set to 1 if you want to output of the dialog with the
	   destination mail server */
	$validator->debug=0;

	/* Set to 1 if you want the debug output to be formatted to be
	displayed properly in a HTML page. */
	$validator->html_debug=1;


	/* When it is not possible to resolve the e-mail address of
	   destination server (MX record) eventually because the domain is
	   invalid, this class tries to resolve the domain address (A
	   record). If it fails, usually the resolver library assumes that
	   could be because the specified domain is just the subdomain
	   part. So, it appends the local default domain and tries to
	   resolve the resulting domain. It may happen that the local DNS
	   has an * for the A record, so any sub-domain is resolved to some
	   local IP address. This  prevents the class from figuring if the
	   specified e-mail address domain is valid. To avoid this problem,
	   just specify in this variable the local address that the
	   resolver library would return with gethostbyname() function for
	   invalid global domains that would be confused with valid local
	   domains. Here it can be either the domain name or its IP address. */
	$validator->exclude_address="";
	
	$result = $validator->ValidateEmailBox($email);
	
	return $result;
}

function listAll( $letterman_rights )
{
    global $database, $gid, $mosConfig_offset, $mosConfig_absolute_path,
    		$Itemid, $menuname, $limit, $limitstart;
    $now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

    $sql = "SELECT id, subject, send, hits FROM `#__letterman`"
    ."\nWHERE ";
    if( !$letterman_rights['is_editor'] )
        $sql.="\npublished=1 AND";
    
    $sql .= "\naccess <= $gid ";
    if( !$letterman_rights['is_editor'] ) {
        $sql .= "\nAND (publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now') ";
        $sql .= "\nAND (publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now')";
    }
    
    $database->setQuery( $sql );
	$database->query();
	$num_rows = $database->getNumRows();
	
	require_once( $mosConfig_absolute_path.'/includes/pageNavigation.php');
	$pageNav = new mosPageNav( $num_rows, $limitstart, $limit );
	
	$sql .= "\nORDER BY created DESC";
	$sql .= "\nLIMIT $limitstart, $limit";
	$database->setQuery( $sql );
    
	$newsletters = $database->loadObjectList();
    
    echo $database->getErrorMsg();
    
    HTML_letterman::listAll( $menuname , $newsletters, $letterman_rights, $pageNav );

}

function showItem( $uid, $gid, $is_editor, $pop, $option ) {
	global $database, $mainframe;
	global $mosConfig_offset, $mosConfig_live_site;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	if ($is_editor) {
		$xwhere='';
	} 
    else {
		$xwhere = ""
		. "\n	AND published=1 "
		. "\n	AND (publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now')"
		. "\n	AND (publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now')"
		;
	}

    $sql = "SELECT id, subject AS title, send, created, hits, html_message AS text FROM #__letterman"
    ."\nWHERE id=$uid $xwhere"
    ."\nAND access <= $gid "
    ."\nORDER BY created DESC";
    $database->setQuery( $sql );
	$row = null;
	if ($database->loadObject( $row )) {
    
        $item = new mosLetterman($database);
        $item->hit( $row->id );
        
		HTML_letterman::showItem( $row, $gid );
        
	} 
    else {
		echo _NOT_AUTH;
		return;
	}
}
function saveSubscriber($name, $email){
	global $database, $my, $Itemid, $mosConfig_live_site, 
			$mosConfig_absolute_path, $lm_params;
    
    $name = addslashes(strip_tags($name));
    $email = addslashes(strip_tags($email));
    
    $row = new mosLettermanSubscribers( $database );
    
    if( $lm_params->get( 'extended_email_validation', '1') && function_exists('GetMXRR')) {
		if( !extended_email_check( $email ) ) {
			mosRedirect( sefRelToAbs('index.php?option=com_letterman&amp;task=subscribe&amp;Itemid='.$Itemid), LM_VALID_EMAIL_PLEASE);;
		}
    }
    else {
	    if (!lm_email_check($email)) {
			mosRedirect( sefRelToAbs('index.php?option=com_letterman&amp;task=subscribe&amp;Itemid='.$Itemid), LM_VALID_EMAIL_PLEASE);
		}
    }
 	// load the row from the db table
    $row->subscriber_id = "";
 	$row->user_id = $my->id;
 	$row->subscriber_name = $name;
 	$row->subscriber_email = $email;
	$row->subscribe_date = date( "Y-m-d H:i:s" );
	
    if (!$row->store()) {
		echo "<script type=\"text/javascript\"> alert('".LM_SAME_EMAIL_TWICE."'); window.history.go(-1); </script>\n";
	}
	else{
        $subscriberhash = md5($database->insertid());
        $subject = str_replace( "[mosConfig_live_site]", $mosConfig_live_site, LM_SUBSCRIBE_SUBJECT );
        $confirmlink = sefRelToAbs($mosConfig_live_site."/index.php?option=com_letterman&task=confirm&subscriber=$subscriberhash");
        $content = str_replace( "[LINK]", $confirmlink, LM_SUBSCRIBE_MESSAGE );
        if( $my->id ) {
        	$content = str_replace( "[NAME]", $my->name, $content );
        }
        else {
        	$content = str_replace( "[NAME]", $name, $content );
        }
        $content = str_replace( "[mosConfig_live_site]", $mosConfig_live_site, $content );
        
        if( !$send = mosMail("", "", $email, $subject, $content) ) {
          echo '<script type="text/javascript">alert("'.LM_ERROR_SENDING_SUBSCRIBE. $send . '");</script>';
        }
    
        echo LM_SUCCESS_SUBSCRIBE."<br/><br/>";
        echo '<a href="' . sefRelToAbs("index.php?option=com_letterman&amp;Itemid=$Itemid") . '">'.LM_RETURN_TO_NL.'</a><br /><br />';

	}
}

function deleteSubscriber($name, $email ){
	global $database, $Itemid, $my, $mosConfig_live_site;

    if( $name != "" )
        $check = "SELECT user_id FROM #__letterman_subscribers WHERE subscriber_name = '" . $name . "' AND subscriber_email = '" . $email . "'";
    else
        $check = "SELECT user_id FROM #__letterman_subscribers WHERE subscriber_email = '" . $email . "'";
    $database->setQuery( $check );
    $database->loadObject( $result );

    if( !$result ) {
        mosRedirect( sefReltoAbs("index.php?option=com_letterman&Itemid=$Itemid"), _ERROR_PASS );
    }
    else {
        if( $name != "" )
            $query = "DELETE FROM #__letterman_subscribers WHERE subscriber_name = '" . $name . "' AND subscriber_email = '" . $email . "'";
        else
            $query = "DELETE FROM #__letterman_subscribers WHERE subscriber_email = '" . $email . "'";
        $database->setQuery($query);
        $database->query();
        
        $subject = str_replace( "[mosConfig_live_site]", $mosConfig_live_site, LM_UNSUBSCRIBE_SUBJECT );
        $content = str_replace( "[NAME]", $name, LM_UNSUBSCRIBE_MESSAGE );
        $content = str_replace( "[mosConfig_live_site]", $mosConfig_live_site, $content );
        
        if( !$send = mosMail("", "", $email, $subject, $content) ) {
          echo '<script type="text/javaScript">alert("'.LM_ERROR_SENDING_UNSUBSCRIBE . $send . '");</script>';
        }
		 
		echo LM_SUCCESS_UNSUBSCRIBE."<br/><br/>";
		echo '<a href="' . sefRelToAbs("index.php?option=com_letterman&amp;Itemid=$Itemid") . '">'.LM_RETURN_TO_NL.'</a><br /><br />';
    }
}

function confirmSubscriber( $subscriber ){
    global $database, $Itemid;
    $subscriber = addslashes(strip_tags($subscriber));
    
	$database->setQuery( "SELECT confirmed FROM #__letterman_subscribers WHERE md5(subscriber_id) = '" . $subscriber . "'" );
    $database->loadObject( $result );
	
    if( $result ) {
        $query = "UPDATE #__letterman_subscribers SET confirmed = 1 WHERE md5(subscriber_id) = '" . $subscriber . "'";
        $database->setQuery($query);
        $database->query();
        echo "<h3>".LM_SUCCESS_CONFIRMATION."</h3><br/>";
        echo '<a href="' . sefRelToAbs("index.php?option=com_letterman&amp;Itemid=$Itemid") . '">'.LM_RETURN_TO_NL.'</a><br /><br />';
    }
    else {
		echo LM_ERROR_CONFIRM_ACC_NOTFOUND;
	}
}

function editNewsletter( $uid, $option ) {
	global $database, $my;

	$row = new mosLetterman( $database );
	// load the row from the db table
	$row->load( $uid );
    
    if( !empty($row->checked_out)) {
        if( $row->checked_out != $my->id )
            mosRedirect( "index.php?option=$option&Itemid=$Itemid", _NOT_AUTH );
    }
    
	if ($uid) {
		$row->checkout( $my->id );
	} else {
		// initialise new record
		$row->published = 0;
	}

	// make the select list for the image positions
	$yesno[] = mosHTML::makeOption( '0', 'No' );
	$yesno[] = mosHTML::makeOption( '1', 'Yes' );

	// build the html select list
	$publist = mosHTML::selectList( $yesno, 'published', 'class="inputbox" size="2"',
	'value', 'text', $row->published );
	
	// get list of groups
	$database->setQuery( "SELECT id AS value, name AS text FROM #__groups ORDER BY id" );
	$groups = $database->loadObjectList();	if (!($orders = $database->loadObjectList())) {
		echo $database->stderr();
		return false;
	}

	// build the html select list
	$glist = mosHTML::selectList( $groups, 'access', 'class="inputbox" size="1"',
	'value', 'text', intval( $row->access ) );


	HTML_Letterman::editNewsletter( $row, $publist, $option , $glist );
}

function saveNewsletter( $option ) {
	global $database, $my, $Itemid;

	$row = new mosLetterman( $database );
	if (!$row->bind( $_POST )) {
		echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( "index.php?option=$option&Itemid=$Itemid", _E_ITEM_SAVED );
}
/**
* Cancels an edit operation
* @param string The current url option
*/
function cancelNewsletter( $option ) {
	global $database, $Itemid;
	$row = new mosLetterman( $database );
	$row->bind( $_POST );
	$row->checkin();
	mosRedirect( "index.php?option=$option&Itemid=$Itemid" );
}

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeNewsletter( $id, $option ) {
	global $database;

	$item = new mosLetterman( $database );
    if (!$item->delete( $id )) {
        echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    }
    else {
        mosRedirect( "index.php?option=$option&Itemid=$Itemid" );
    }
}

?>
