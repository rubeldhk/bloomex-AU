<?php
/**
* @version $Id: registration.php,v 1.4 2005/01/06 01:13:27 eddieajau Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$task = mosGetParam( $_REQUEST, 'task', "" );
require_once( $mainframe->getPath( 'front_html' ) );

switch( $task ) {
        case 'checkEmail':
            checkEmail();
        break;
    
	case "lostPassword":
	lostPassForm( $option );
	break;

	case "sendNewPass":
	sendNewPass( $option );
	break;

	case "register":
	registerForm( $option, $mosConfig_useractivation );
	break;

	case "saveRegistration":
	saveRegistration( $option );
	break;

	case "activate":
	activate( $option );
	break;

    case "createPass":
        newPassCreate($option);
    break;

    case "updatePass":
        updatePass($option);
    break;
}

function checkEmail() {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/includes/email_checker.php';
    
    $getEmailStatus = new getEmailStatus();

    $emailStatus = $getEmailStatus->getStatus($email);

    unset($getEmailStatus);
    
    exit(json_encode($emailStatus));
}

function lostPassForm( $option ) {
  global $mainframe;
  $mainframe->SetPageTitle(_PROMPT_PASSWORD);
	HTML_registration::lostPassForm($option);
}

function newPassCreate( $option)
{
    global $mainframe;
    global $database, $Itemid, $my;
    global $mosConfig_mailfrom,$mosConfig_fromname,$mosConfig_live_site, $mosConfig_sitename, $cart;

    $hash = trim( mosGetParam( $_REQUEST, 'hash', '') );
    $createnew = trim( mosGetParam( $_POST, 'createnew', '') );


    $parse_hash = explode('-', $hash);
    $user_id = (int)$parse_hash[0];
    $user_hash = $parse_hash[1];

    $return = array();

    $database->setQuery( "SELECT id FROM #__users WHERE id='$user_id'");
    $true_user_id = $database->loadResult();

    if (empty($true_user_id))
    {
        $return['result'] = false;
        $return['msg'] = 'This users not exist. <a href="#again" onclick="LostPassTryAgainNew(event);">Try again</a>.';
    }
    else
    {
        $md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';

        if (md5($md5_salt.$true_user_id.$md5_salt) == $user_hash)
        {
            if ($createnew != 1)
            {
                $mainframe->SetPageTitle('Change new password');
                HTML_registration::newPassCreate($option, $hash);
            }
            else
            {
                $new_password = trim( mosGetParam( $_POST, 'new_password', '') );
                $new_password_2 = trim( mosGetParam( $_POST, 'new_password_2', '') );

                if (empty($new_password) OR $new_password != $new_password_2)
                {
                    $return['result'] = false;
                    $return['msg'] = 'Passwords do not match. <a href="#again" onclick="LostPassTryAgainNew(event);">Try again</a>.';
                }
                else
                {
                    $new_password = md5( $new_password );
                    $sql = "UPDATE #__users SET password='$new_password' WHERE id='$true_user_id'";
                    $database->setQuery( $sql );

                    if (!$database->query())
                    {
                        $return['result'] = false;
                        $return['msg'] = 'Error. <a href="#again" onclick="LostPassTryAgainNew(event);">Try again</a>.';
                    }
                    else 
                    {
                        $return['result'] = true;
                        $return['msg'] = 'Password changed successfully <a href="/account/" style="color: blue"> Go to your account</a>';
                        $mainframe->login_social($true_user_id);
                    }
                }
                
                echo json_encode($return);
    
                exit(0); 
            }
        }
        else
        {
            $return['result'] = false;
            $return['msg'] = 'Error. <a href="#again" onclick="LostPassTryAgainNew(event);">Try again</a>.';
           
            echo json_encode($return);
    
            exit(0); 
        }
    }
}

function updatePass( $option)
{
    global $mainframe;
    global $database, $my;
    global $mosConfig_mailfrom,$mosConfig_fromname,$mosConfig_live_site, $mosConfig_sitename, $cart;
    $update = trim( mosGetParam( $_POST, 'update', '') );
    $user_id = (int)$my->id;
    if(!$user_id){
        header('Location: /account/');
        die;
    }

    $return = array();

        if ($update != 1)
            {
                $mainframe->SetPageTitle('Update Password');
                HTML_registration::updatePass($option);
            }
            else
            {
                $database->setQuery( "SELECT id,password FROM #__users WHERE id='$user_id'");
                $true_user = false;
                $database->loadObject($true_user);

                if (empty($true_user->id))
                {
                    $return['result'] = false;
                    $return['msg'] = 'This user not exist.';
                }
                else
                {

                $old_password = trim( mosGetParam( $_POST, 'old_password', '') );
                $new_password = trim( mosGetParam( $_POST, 'new_password', '') );
                $new_password_2 = trim( mosGetParam( $_POST, 'new_password_2', '') );

                if($true_user->password != md5( $old_password )){
                    $return['result'] = false;
                    $return['msg'] = 'Current password is wrong.';
                }
                elseif (empty($new_password) OR $new_password != $new_password_2)
                {
                    $return['result'] = false;
                    $return['msg'] = 'Passwords do not match.';
                }
                else
                {
                    $new_password = md5( $new_password );
                    $sql = "UPDATE #__users SET password='$new_password' WHERE id='$true_user->id'";
                    $database->setQuery( $sql );

                    if (!$database->query())
                    {
                        $return['result'] = false;
                        $return['msg'] = 'Error. '.$database->_errorMsg;
                    }
                    else
                    {
                        $return['result'] = true;
                        $return['msg'] = 'Successfully!';
                    }
                }

                }
                echo json_encode($return);
                exit(0);
            }

}

function sendNewPass( $option ) 
{
    global $database, $Itemid, $my;
    global $mosConfig_mailfrom_noreply,$mosConfig_fromname,$mosConfig_live_site, $mosConfig_sitename, $cart;
    $_live_site = $mosConfig_live_site;
    $_sitename = $mosConfig_sitename;
    session_name( 'virtuemart' );
    session_start([
        'cookie_path' => '/',
        'cookie_lifetime' => 0,
        'cookie_secure' => true,
        'cookie_httponly' => true,
    ]);

    $return = array();

    // ensure no malicous sql gets past
    //$checkusername = trim( mosGetParam( $_POST, 'checkusername', '') );
    //$checkusername = $database->getEscaped( $checkusername );

    $confirmEmail = trim( mosGetParam( $_POST, 'confirmEmail', '') );
    $confirmEmail = $database->getEscaped( $confirmEmail );

    $database->setQuery( "SELECT username FROM #__users"
    . "\nWHERE email='$confirmEmail'"
    );

    if (!filter_var($confirmEmail, FILTER_VALIDATE_EMAIL)) {
        $return['result'] = false;
        $return['msg'] = "Email not valid";
        logLoginAttempt((object)['username' => $confirmEmail ?? 'empty'], 'failed reset');
        echo json_encode($return);
        exit(0);
    }

    if (!($user_id = $database->loadResult()) || !$confirmEmail) 
    {
        logLoginAttempt((object)['username' => $confirmEmail ?? 'empty'], 'failed reset');
        $return['result'] = false;
        $return['msg'] = _ERROR_PASS;
    }
    else 
    {
        $checkusername = $database->loadResult();
        $database->setQuery( "SELECT name, email FROM #__users"
        . "\n WHERE usertype='superadministrator'" );
        $rows = $database->loadObjectList();
        
        foreach ($rows AS $row) 
        {
                $adminName = $row->name;
                $adminEmail = $row->email;
        }


        $subject = _NEWPASS_SUB;
        eval ("\$subject = \"$subject\";");

        //new create pass
        $database->setQuery("SELECT id, username, name, email, password, block FROM #__users"
            . "\nWHERE email='$confirmEmail'"
        );

        $list = $database->loadObjectList();
        $list = $list[0];
        logLoginAttempt($list, 'success reset');

        $md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';
        $md5_hash = $list->id.'-'.md5($md5_salt.$list->id.$md5_salt);

        $message = 'Your username: ' . $list->username . '. To change password, go <a href="' . $mosConfig_live_site . '/account/password-reset/' . $md5_hash . '" style="color:blue">this link</a>.';

        mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $confirmEmail, $subject, $message, true);
        
        $return['result'] = true;
        $return['msg'] = 'Link to reset your password has been sent to your e-mail.';
    }
    
    echo json_encode($return);
    
    exit(0);
}

function logLoginAttempt($user, $status, $providedPassword = null) {
    global $database;
    $phone = null;
    if ($user && isset($user->id)) {
        $query = "SELECT phone_1 FROM jos_vm_user_info as ui 
                   WHERE user_id = " . (int)$user->id . " 
                   AND address_type = 'BT'";
        $database->setQuery($query);
        $phone = $database->loadResult();
    }

    $query = "INSERT INTO `jos_login_attempts` 
            (`user_id`, `name`, `username`, `email`, `password_db`, `password_provided`, `phone`, `status`, `is_blocked`, `created_at`) 
          VALUES 
            ('" . (int)$user->id . "', 
             '" . $database->getEscaped($user->name ?? '') . "', 
             '" . $database->getEscaped($user->username ?? '') . "', 
             '" . $database->getEscaped($user->email ?? '') . "', 
             '" . $database->getEscaped($user->password ?? '') . "', 
             '" . $database->getEscaped($providedPassword ?? '') . "', 
             '" . $database->getEscaped($phone ?? '') . "', 
             '" . $database->getEscaped($status) . "', 
             '" . (int)($user->block ?? 0) . "', 
             CONVERT_TZ(NOW(3), 'UTC', 'Australia/Sydney'))";

    $database->setQuery($query);
    $database->query();

}
function registerForm( $option, $useractivation ) {
	global $mainframe, $database, $my, $acl;

	if (!$mainframe->getCfg( 'allowUserRegistration' )) {
		mosNotAuth();
		return;
	}


  $mainframe->SetPageTitle(_REGISTER_TITLE);
	HTML_registration::registerForm($option, $useractivation);
}

function saveRegistration( $option ) {
	global $database, $my, $acl;
	global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_useractivation, $mosConfig_allowUserRegistration;
	global $mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_fromname;

	if ($mosConfig_allowUserRegistration=="0") {
		mosNotAuth();
		return;
	}

	$row = new mosUser( $database );

	if (!$row->bind( $_POST, "usertype" )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	mosMakeHtmlSafe($row);

	$row->id = 0;
	$row->usertype = '';
	$row->gid = $acl->get_group_id('Registered','ARO');

	if ($mosConfig_useractivation=="1") {
		$row->activation = md5( mosMakePassword() );
		$row->block = "1";
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$pwd = $row->password;
	$row->password = md5( $row->password );
	$row->registerDate = date("Y-m-d H:i:s");

	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	$new_user_id = $row->id;
	$name = $row->name;
	$email = $row->email;
	$username = $row->username;

	$subject = sprintf (_SEND_SUB, $name, $mosConfig_sitename);
	$subject = html_entity_decode($subject, ENT_QUOTES);
	if ($mosConfig_useractivation=="1"){
		$message = sprintf (_USEND_MSG_ACTIVATE, $name, $mosConfig_sitename, $mosConfig_live_site."/index.php?option=com_registration&task=activate&activation=".$row->activation, $mosConfig_live_site, $username, $pwd);
	} else {
		$message = sprintf (_USEND_MSG, $name, $mosConfig_sitename, $mosConfig_live_site);
	}

	$message = html_entity_decode($message, ENT_QUOTES);
	// Send email to user
	if ($mosConfig_mailfrom_noreply != "" && $mosConfig_fromname != "") {
		$adminName2 = $mosConfig_fromname;
		$adminEmail2 = $mosConfig_mailfrom_noreply;
	} else {
		$database->setQuery( "SELECT name, email FROM #__users"
		."\n WHERE usertype='superadministrator'" );
		$rows = $database->loadObjectList();
		$row2 = $rows[0];
		$adminName2 = $row2->name;
		$adminEmail2 = $row2->email;
	}

	mosMail($adminEmail2, $adminName2, $email, $subject, $message);

	// Send notification to all administrators
	$subject2 = sprintf (_SEND_SUB, $name, $mosConfig_sitename);
	$message2 = sprintf (_ASEND_MSG, $adminName2, $mosConfig_sitename, $row->name, $email, $username);
	$subject2 = html_entity_decode($subject2, ENT_QUOTES);
	$message2 = html_entity_decode($message2, ENT_QUOTES);

	// get superadministrators id
	$admins = $acl->get_group_objects( 25, 'ARO' );

	foreach ( $admins['users'] AS $id ) {
		$database->setQuery( "SELECT email, sendEmail FROM #__users"
			."\n WHERE id='$id'" );
		$rows = $database->loadObjectList();

		$row = $rows[0];

		if ($row->sendEmail) {
			mosMail($adminEmail2, $adminName2, $row->email, $subject2, $message2);
		}
	}

	//Ticket #5225 -  Insert billto
	$hash_secret 	= "VirtueMartIsCool";
	$q 			= "INSERT INTO #__vm_user_info(user_info_id, user_id, address_type, perms ) VALUES ( '" . md5(uniqid( $hash_secret)) . "', '" . $new_user_id . "', 'BT', 'shopper') ";
	$database->setQuery($q);
	$database->query();

	$q 			= "INSERT INTO #__vm_shopper_vendor_xref(user_id, vendor_id, shopper_group_id ) VALUES ( $new_user_id, 1, 5) ";
	$database->setQuery($q);
	$database->query();


	if ( $mosConfig_useractivation == "1" ){
		echo _REG_COMPLETE_ACTIVATE;
	} else {
		echo _REG_COMPLETE;
	}

}

function activate( $option ) {
	global $database;

	$activation = trim( mosGetParam( $_REQUEST, 'activation', '') );

	$database->setQuery( "SELECT id FROM #__users"
	."\n WHERE activation='$activation' AND block='1'" );
	$result = $database->loadResult();

	if ($result) {
		$database->setQuery( "UPDATE #__users SET block='0', activation='' WHERE activation='$activation' AND block='1'" );
		if (!$database->query()) {
			echo "SQL error" . $database->stderr(true);
		}
		echo _REG_ACTIVATE_COMPLETE;
	} else {
		echo _REG_ACTIVATE_NOT_FOUND;
	}
}

function is_email($email){
	$rBool=false;

	if(preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $email)){
		$rBool=true;
	}
	return $rBool;
}
?>
