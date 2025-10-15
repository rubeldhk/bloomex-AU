<?php

/**
 * @version $Id: admin.users.php 4797 2006-08-28 05:08:06Z eddiea $
 * @package Joomla
 * @subpackage Users
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');


require_once( $mainframe->getPath('admin_html') );

switch ($task) {
    case 'save':
        saveUser();
        break;
    case 'edit':
    default:
        HTML_PrivateProfile::editUser();
        break;
}

function saveUser() {
    global $database, $my, $acl;
    global $mosConfig_live_site, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_sitename;

    $oldpassword = mosGetParam($_POST, 'oldpassword');
    $password = mosGetParam($_POST, 'password');


    if ($oldpassword == $password) {
        $msg = "You must create a new password.";
        mosRedirect('index2.php?option=com_privateprofile', $msg);
    }

    if ($my->id) {
        $sql = "SELECT * FROM jos_users WHERE id = " . $my->id;
        $database->setQuery($sql);
        $Profile = $database->loadObjectList();
        $Profile = $Profile[0];

        if ($Profile->password == md5($oldpassword)) {
            $sql = "UPDATE jos_users SET password = '" . md5($password) . "' WHERE id = " . $my->id;
            $database->setQuery($sql);
            $Profile = $database->query();

            $query = "SELECT id FROM tbl_pass_log WHERE user_id = " . $my->id;
            $database->setQuery($query);
            $isExist = $database->loadResult();

            if ($isExist) {
                $query = "UPDATE tbl_pass_log SET last_updated = '" . date("Y-m-d  H:i:s") . "' WHERE user_id = " . $my->id;
                $database->setQuery($query);
                $database->query() or die($database->stderr());
            } else {
                $query = "INSERT INTO tbl_pass_log(last_updated, user_id) VALUES('" . date("Y-m-d  H:i:s") . "', $my->id )";
                $database->setQuery($query);
                $database->query() or die($database->stderr());
            }


            $msg = 'Your password saved successful!';
            mosRedirect('index2.php', $msg);
        } else {
            $msg = 'Your old password is incorrect. Please try again';
            mosRedirect('index2.php?option=com_privateprofile', $msg);
        }
    }
}

function cancelUser($option) {
    mosRedirect('index2.php');
}

?>
