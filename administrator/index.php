<?php

/**
 * @version $Id: index.php 4750 2006-08-25 01:08:30Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// Set flag that this is a parent file
define('_VALID_MOS', 1);

$aHost = explode(".", $_SERVER["HTTP_HOST"]);
if (strtolower($aHost[0]) == "www") {
    header("Location: http://bloomex.com.au" . $_SERVER["REQUEST_URI"]);
}



if (!file_exists('../configuration.php')) {
    header('Location: ../installation/index.php');
    exit();
}

require( '../globals.php' );
require_once( '../configuration.php' );
$mosConfig_user = $mosConfig_user_adm;
$mosConfig_password = $mosConfig_password_adm;
require_once( '../includes/joomla.php' );
include_once ( $mosConfig_absolute_path . '/language/' . $mosConfig_lang . '.php' );

//Installation sub folder check, removed for work with SVN
if (file_exists('../installation/index.php') && $_VERSION->SVN == 0) {
    define('_INSTALL_CHECK', 1);
    include ($mosConfig_absolute_path . '/offline.php');
    exit();
}

$option = strtolower(strval(mosGetParam($_REQUEST, 'option', NULL)));
require_once '../start_access_log.php';
// mainframe is an API workhorse, lots of 'core' interaction routines
$mainframe = new mosMainFrame($database, $option, '..', true);

if (isset($_POST['submit']) || isset($_POST['verify'])) {
    session_name(md5($mosConfig_live_site));
    session_start([
        'cookie_path' => '/',
        'cookie_lifetime' => 0,
        'cookie_secure' => true,
        'cookie_httponly' => true,
    ]);

    if (isset($_POST['verify'])) {
        $authUserId = $_SESSION['session_user_id'] ?? null;

        if (! isValidMfaCode($authUserId)) {
            $tryCount = $_SESSION['session_verification_count'] ?? 1;
            $_SESSION['session_verification_count'] = $tryCount + 1;

            if ($_SESSION['session_verification_count'] > 3) {
                $user = null;
                $query = "SELECT * FROM `jos_users` WHERE `id`={$authUserId}";
                $database->setQuery($query);
                $database->loadObject($user);
                generateMultiFactorAuthCode($user);
            }

            $errorMessage = "Invalid code";
            initGzip();
            $path = $mosConfig_absolute_path . '/administrator/templates/' . $mainframe->getTemplate() . '/verification.php';
            require_once($path);
            doGzip();
            exit();
        } else {
            $_SESSION['session_verification_count'] = 0;
        }

        $query = "UPDATE `jos_vm_users_mfa` SET `has_verified` = 1 WHERE user_id = {$authUserId}";
        $database->setQuery($query);
        $database->query();

        $my = null;
        $query = sprintf("
                SELECT 
                    `tnug`.`area_name`, u.*, m.*  
                FROM 
                    `#__users` AS `u`
                LEFT JOIN `#__messages_cfg` AS `m` ON `u`.`id` = `m`.`user_id` AND `m`.`cfg_name` = 'auto_purge'
                LEFT JOIN 
                    `tbl_mix_user_group` AS `tmug`
                ON
                    `tmug`.`user_id` = `u`.`id`
                LEFT JOIN 
                    `tbl_new_user_group` AS `tnug`
                ON
                    `tnug`.`id` = `tmug`.`user_group_id`
                WHERE 
                    `u`.`id` = '%s'",
            $authUserId
        );

        $database->setQuery($query);
        $database->loadObject($my);
    } else {
        /** escape and trim to minimize injection of malicious sql */
        $usrname = $database->getEscaped(mosGetParam($_POST, 'usrname', NULL));
        $pass = !empty($_POST['pass']) ? $_POST['pass'] : NULL; //$database->getEscaped(mosGetParam($_POST, 'pass', NULL));

        if ($pass == NULL) {
            echo "<script>alert('Please enter a password'); document.location.href='index.php?mosmsg=Please enter a password'</script>\n";
            exit();
        } else {
            $pass = md5($pass);
        }

        $my = null;
        $query = sprintf("
                SELECT 
                    `tnug`.`area_name`, u.*, m.*  
                FROM 
                    `#__users` AS `u`
                LEFT JOIN `#__messages_cfg` AS `m` ON `u`.`id` = `m`.`user_id` AND `m`.`cfg_name` = 'auto_purge'
                LEFT JOIN 
                    `tbl_mix_user_group` AS `tmug`
                ON
                    `tmug`.`user_id` = `u`.`id`
                LEFT JOIN 
                    `tbl_new_user_group` AS `tnug`
                ON
                    `tnug`.`id` = `tmug`.`user_group_id`
                WHERE 
                    `u`.`username` = '%s'
                AND
                    `u`.`password` = '%s'
                AND 
                    `u`.`block` = 0",
            $usrname,
            $pass
        );

        $database->setQuery($query);
        $database->loadObject($my);
    }


    /** find the user group (or groups in the future) */
    if (@$my->id) {
        $grp = $acl->getAroGroup($my->id);
        $my->gid = $grp->group_id;
        $my->usertype = $grp->name;


        if ((!isset($_POST['verify']) && strcmp($my->password, $pass)) || !$acl->acl_check('administration', 'login', 'users', $my->usertype)) {
            mosErrorAlert("Incorrect Username, Password, or Access Level.  Please try again", "document.location.href='index.php'");
        }


        // construct Session ID
        $logintime = time();
        $session_id = md5($my->id . $my->username . $my->usertype . $logintime);

        if (isset($_POST['submit']) && $mosConfig_admin_mfa_enabled && isMultiFactorAuthenticationRequired($my)) {
            generateMultiFactorAuthCode($my);
            $authUserId = $my->id;

            $_SESSION['session_user_id'] = $my->id;
            $_SESSION['session_verification_count'] = isset($_SESSION['session_verification_count'])
                ? (int)$_SESSION['session_verification_count']
                : 1;

            session_write_close();
            initGzip();
            $path = $mosConfig_absolute_path . '/administrator/templates/' . $mainframe->getTemplate() . '/verification.php';
            require_once($path);
            doGzip();
            exit();
        }

        // add Session ID entry to DB
        $query = "INSERT INTO #__session"
                . "\n SET time = '$logintime', session_id = '$session_id', userid = $my->id, usertype = '$my->usertype', username = '$my->username'"
        ;
        $database->setQuery($query);
        if (!$database->query()) {
            echo $database->stderr();
        }

        // check if site designated as a production site 
        // for a demo site allow multiple logins with same user account
        if ($_VERSION->SITE == 1) {
            // delete other open admin sessions for same account
            $query = "DELETE FROM #__session"
                    . "\n WHERE userid = $my->id"
                    . "\n AND username = '$my->username'"
                    . "\n AND usertype = '$my->usertype'"
                    . "\n AND session_id != '$session_id'"
                    // this ensures that frontend sessions are not purged
                    . "\n AND guest = 1"
                    . "\n AND gid = 0"
            ;
            $database->setQuery($query);
            if (!$database->query()) {
                echo $database->stderr();
            }
        }

        $_SESSION['session_id'] = $session_id;
        $_SESSION['session_user_id'] = $my->id;
        $_SESSION['session_username'] = $my->username;
        $_SESSION['session_usertype'] = $my->usertype;
        $_SESSION['session_gid'] = $my->gid;
        $_SESSION['session_logintime'] = $logintime;
        $_SESSION['session_user_params'] = $my->params;
        $_SESSION['session_userstate'] = array();

        session_write_close();

        $expired = 'index2.php';

        // check if site designated as a production site 
        // for a demo site disallow expired page functionality
        if ($_VERSION->SITE == 1 && @$mosConfig_admin_expired === '1') {
            $file = $mainframe->getPath('com_xml', 'com_users');
            $params = new mosParameters($my->params, $file, 'component');

            $now = time();

            // expired page functionality handling
            $expired = $params->def('expired', '');
            $expired_time = $params->def('expired_time', '');

            // if now expired link set or expired time is more than half the admin session life set, simply load normal admin homepage 	
            $checktime = ( $mosConfig_session_life_admin ? $mosConfig_session_life_admin : 1800 ) / 2;
            if (!$expired || ( ( $now - $expired_time ) > $checktime )) {
                $expired = 'index2.php';
            }
            // link must also be a Joomla link to stop malicious redirection			
            if (strpos($expired, 'index2.php?option=com_') !== 0) {
                $expired = 'index2.php';
            }

            // clear any existing expired page data
            $params->set('expired', '');
            $params->set('expired_time', '');

            // param handling
            if (is_array($params->toArray())) {
                $txt = array();
                foreach ($params->toArray() as $k => $v) {
                    $txt[] = "$k=$v";
                }
                $saveparams = implode("\n", $txt);
            }

            // save cleared expired page info to user data

            date_default_timezone_set('Australia/Sydney'); 
            
            $currentDate = date("Y-m-d\TH:i:s");

            $query = "UPDATE #__users"
                    . "\n SET params = '$saveparams', lastvisitDate = '$currentDate'"
                    . "\n WHERE id = $my->id"
                    . "\n AND username = '$my->username'"
                    . "\n AND usertype = '$my->usertype'"
            ;
            $database->setQuery($query);
            $database->query();
        }

        //==================================================================================						
        $query = "SELECT last_updated FROM tbl_pass_log WHERE user_id = $my->id";
        $database->setQuery($query);
        $lastUpdatePass = $database->loadResult();
        $lastUpdatePass = strtotime($lastUpdatePass);

        $query = "SELECT registerDate FROM #__users WHERE id = $my->id";
        $database->setQuery($query);
        $registerDate = strtotime($database->loadResult());

        // redirects page to admin homepage by default or expired page
        echo "<script>document.location.href='$expired';</script>\n";
        exit();
    } else {
        mosErrorAlert("Incorrect Username, Password.  Please try again", "document.location.href='index.php?mosmsg=Incorrect Username, Password. Please try again'");
    }
} else {
    initGzip();
    $path = $mosConfig_absolute_path . '/administrator/templates/' . $mainframe->getTemplate() . '/login.php';
    require_once( $path );
    doGzip();
}
require_once '../end_access_log.php';
if (isset($database)) {
    $database->close();
}


function generateMultiFactorAuthCode($user)
{
    global $mosConfig_fromname, $mosConfig_mailfrom, $database, $mosConfig_mfa_mailfrom;

    $generatedDate = date("Y-m-d\TH:i:s");
    $code = mt_rand(1000, 9999);
    $subject = 'Bloomex Admin Verification Code';
    $content = "We received a request to access your Bloomex Admin Account with username: {$user->username}. Your verification code is {$code}";

    $mailFrom = $mosConfig_mfa_mailfrom ?? $mosConfig_mailfrom;

    mosMail($mailFrom, $mosConfig_fromname, $user->email, $subject, $content);

    $query = "
        INSERT INTO `jos_vm_users_mfa` 
            (`user_id`, `mfa_code`, `last_generated`, `has_verified`)
        VALUES 
            ({$user->id}, '{$code}', '{$generatedDate}', 0)
        ON DUPLICATE KEY UPDATE
          `mfa_code` = VALUES(`mfa_code`), 
          `last_generated` = VALUES(`last_generated`),
          `has_verified` = VALUES(`has_verified`)"
    ;
    $database->setQuery($query);
    $database->query();
}

function isMultiFactorAuthenticationRequired($user)
{
    global $database;
    $userAuthCodeObject = null;
    $query = "SELECT * from `jos_vm_users_mfa` WHERE user_id = {$user->id} order by last_generated desc  LIMIT 1";
    $database->setQuery($query);
    $database->loadObject($userAuthCodeObject);

    if (! $userAuthCodeObject || $userAuthCodeObject->has_verified == 0
        || (time() - strtotime($userAuthCodeObject->last_generated)) > 7 * 86400
    ) {
        return true;
    } else {
        return false;
    }
}

function isValidMfaCode($userId)
{
    global $database;

    if (!$userId) {
        return false;
    }

    $userAuthCodeObject = null;
    $query = "SELECT * from `jos_vm_users_mfa` WHERE user_id = {$userId} order by last_generated desc LIMIT 1";
    $database->setQuery($query);
    $database->loadObject($userAuthCodeObject);

    $verificationCode = $database->getEscaped(mosGetParam($_POST, 'verification_code', NULL));

    return $userAuthCodeObject && $userAuthCodeObject->mfa_code == $verificationCode;
}
