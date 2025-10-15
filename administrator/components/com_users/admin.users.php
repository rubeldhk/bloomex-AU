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

//if (!$acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_users' )) {
if ($my->gid != 25 && $my->gid != 24) {
    mosRedirect('index2.php', _NOT_AUTH);
}

require_once( $mainframe->getPath('admin_html') );
require_once( $mainframe->getPath('class') );

$cid = josGetArrayInts('cid');

switch ($task) {
    case 'new':
        editUser(0, $option);
        break;

    case 'edit':
        editUser(intval($cid[0]), $option);
        break;
    case 'update_user_block':
        update_user_block();
        break;
    case 'editA':
        editUser($id, $option);
        break;

    case 'save':
    case 'apply':
        // check to see if functionality restricted for use as demo site
        if ($_VERSION->RESTRICT == 1) {
            mosRedirect('index2.php?mosmsg=Functionality Restricted');
        } else {
            saveUser($task);
        }
        break;

    case 'remove':
        removeUsers($cid, $option);
        break;

    case 'block':
        // check to see if functionality restricted for use as demo site
        if ($_VERSION->RESTRICT == 1) {
            mosRedirect('index2.php?mosmsg=Functionality Restricted');
        } else {
            changeUserBlock($cid, 1, $option);
        }
        break;

    case 'unblock':
        changeUserBlock($cid, 0, $option);
        break;

    case 'logout':
        logoutUser($cid, $option, $task);
        break;

    case 'flogout':
        logoutUser($id, $option, $task);
        break;

    case 'cancel':
        cancelUser($option);
        break;

    case 'contact':
        $contact_id = mosGetParam($_POST, 'contact_id', '');
        mosRedirect('index2.php?option=com_contact&task=editA&id=' . $contact_id);
        break;
    case 'send_new_password':
        send_new_password();
        break;
    case 'send_bucks':
        send_bucks();
        break;
    default:
        showUsers($option);
        break;
}

function send_bucks() {
    global $mosConfig_mailfrom_noreply, $mosConfig_fromname;
    $email = mosGetParam($_REQUEST, "email");
    $bucks = $_POST['bucks'] ? $_POST['bucks'] : '';
    if (!$bucks) {
        $msg = "Bucks are empty or wrong";
        die($msg);
    } else {
        $msg = 'Bloomex Bucks sent successfully to ' . $email;
        $subject = 'Bloomex Bucks';
        $message = 'Your Bloomex ' . $bucks . '<br>

Thank you - we appreciate your business.<br>
Bloomex<br>
1-800-905-147
';
        mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $email, $subject, $message, 1);
    }
    exit($msg);
}

function RandomString($count) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < $count; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

function send_new_password() {
    global $database, $mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_live_site;
    $user_id = mosGetParam($_REQUEST, "user_id");
    $new_pass = RandomString(10);
    $query = "UPDATE `jos_users` SET `password` = MD5( '" . $new_pass . "' ) WHERE `id` ='" . $user_id . "'";
    $database->setQuery($query);
    if (!$database->query()) {
        $msg = "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>";
        die($msg);
    } else {
        $msg = 'success';
        $query = "SELECT email FROM  `jos_users` WHERE `id` ='" . $user_id . "'";
        $database->setQuery($query);
        $email = $database->loadResult();
        $subject = 'New Bloomex Password';

        $md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';
        $md5_hash = $user_id . '-' . md5($md5_salt . $user_id . $md5_salt);



        $message = 'Your new password is: ' . $new_pass . '

To change your password please go to: ' . $mosConfig_live_site . '/account/password-reset/' . $md5_hash . '

Thank you - we appreciate your business.

Bloomex
1-800-905-147
';
        mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $email, $subject, $message);
    }
    exit($msg);
}

function update_user_block() {
    global $database, $my;
    $user_id = mosGetParam($_REQUEST, "user_id");
    $block = mosGetParam($_REQUEST, "block", "0");
    $reason = mosGetParam($_REQUEST, "block_reason", "");
    $reason_type = mosGetParam($_REQUEST, "block_reason_type", "");

    $query = "INSERT INTO tbl_users_block_history( user_id, username,block,reason_type,reason,datetime ) VALUES( '$user_id','$my->username','$block','$reason_type','$reason',now() )";
    $database->setQuery($query);
    $database->query() or die($database->stderr());

    $query = "UPDATE jos_users SET block = '$block' where id = '$user_id'";
    $database->setQuery($query);
    $database->query() or die($database->stderr());
    exit('success');
}

function showUsers($option) {
    global $database, $mainframe, $my, $acl, $mosConfig_list_limit;

    $filter_type = $mainframe->getUserStateFromRequest("filter_type{$option}", 'filter_type', 0);
    $filter_type_new = $mainframe->getUserStateFromRequest("filter_type_new{$option}", 'filter_type_new', 0);
    $filter_type_new_2 = $mainframe->getUserStateFromRequest("filter_type_new_2{$option}", 'filter_type_new_2', 0);
    $filter_logged = intval($mainframe->getUserStateFromRequest("filter_logged{$option}", 'filter_logged', 0));
    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    $where = array();
    if($limit > 100){
        $limit = 100;
    }
    if (isset($search) && $search != "") {
        $where[] = "(a.username LIKE '%$search%' OR a.id LIKE '%$search%' OR a.email LIKE '%$search%' OR a.name LIKE '%$search%')";
    }

    /*
      if ( $filter_type ) {
      if ( $filter_type == 'Public Frontend' ) {
      $where[] = "a.usertype = 'Registered' OR a.usertype = 'Author' OR a.usertype = 'Editor'OR a.usertype = 'Publisher'";
      } else if ( $filter_type == 'Public Backend' ) {
      $where[] = "a.usertype = 'Manager' OR a.usertype = 'Administrator' OR a.usertype = 'Super Administrator'";
      } else {
      $where[] = "a.usertype = LOWER( '$filter_type' )";
      }
      }
     */

    if ($filter_logged == 1) {
        $where[] = "s.userid = a.id";
    } else if ($filter_logged == 2) {
        $where[] = "s.userid IS NULL";
    }

    // exclude any child group id's for this user
    $pgids = $acl->get_group_children($my->gid, 'ARO', 'RECURSE');

    if (is_array($pgids) && count($pgids) > 0) {
        $where[] = "(a.gid NOT IN (" . implode(',', $pgids) . "))";
    }

    $where[] = "g.group_id != 18";



    $query_search = "SELECT a.id";

    $query_main = "SELECT a.*, g.name AS groupname";
    //$query = "SELECT a.*";
    //$query .= ", IFNULL(`n_g`.`departments_name`, 'Registered') as `groupname`";


    $query = '';
    $query .= "\n FROM #__users AS a"
            . "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id" // map user to aro
            . "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.aro_id" // map aro to group
            . "\n INNER JOIN #__core_acl_aro_groups AS g ON g.group_id = gm.group_id";

    if (!empty($filter_type_new_2) AND $filter_type_new_2 != '0') {
        $query .= "\n INNER JOIN tbl_new_user_group AS `n_g` ON `n_g`.`area_name` LIKE '%" . $filter_type_new_2 . "%'";

        $query .= "\n INNER JOIN `tbl_mix_user_group` as `m_u_g` ON `m_u_g`.`user_group_id`=`n_g`.`id` AND `m_u_g`.`user_id`=a.id";
    } elseif (!empty($filter_type_new) AND $filter_type_new != '0') {
        $query .= "\n INNER JOIN `tbl_mix_user_group` as `m_u_g` ON `m_u_g`.`user_group_id`=" . (int) $filter_type_new . " AND `m_u_g`.`user_id`=a.id";
    }


    if ($filter_logged == 1 || $filter_logged == 2) {
        $query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
    }

    $query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : "");



    $database->setQuery($query_search . $query);
    $database->query();
    $total = $database->getNumRows();

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $database->setQuery($query_main . $query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    $template = 'SELECT s.userid FROM #__session AS s WHERE s.userid = %d';
    $n = count($rows);
    for ($i = 0; $i < $n; $i++) {
        $row = &$rows[$i];
        $query = sprintf($template, intval($row->id));
        $database->setQuery($query);
        $database->query();
        $row->loggedin = $database->getNumRows();
    }

    // get list of Groups for dropdown filter
    $query = "SELECT name AS value, name AS text"
            . "\n FROM #__core_acl_aro_groups"
            . "\n WHERE name != 'ROOT'"
            . "\n AND name != 'USERS'"
    ;
    $types[] = mosHTML::makeOption('0', '- Select Group -');
    $database->setQuery($query);
    $types = array_merge($types, $database->loadObjectList());
    $lists['type'] = mosHTML::selectList($types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type");
    $query = "SELECT id AS value, departments_name AS text"
            . "\n FROM tbl_new_user_group"
    ;
    $types_new[] = mosHTML::makeOption('0', '- Select Group -');
//    $types_new[] = mosHTML::makeOption('0', 'Registered');
    $database->setQuery($query);
    $types_new = array_merge($types_new, $database->loadObjectList());
    $lists['type_new'] = mosHTML::selectList($types_new, 'filter_type_new', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type_new");

    $types_new_2[] = mosHTML::makeOption('0', '- Select Value -');

    $types_new_2[] = mosHTML::makeOption('full_menus', 'Full Menus');
    $types_new_2[] = mosHTML::makeOption('manage_orders', 'Manage Orders');
    $types_new_2[] = mosHTML::makeOption('manage_products', 'Manage Products');
    $types_new_2[] = mosHTML::makeOption('manage_coupons', 'Manage Coupons');
    $types_new_2[] = mosHTML::makeOption('manage_content', 'Manage Content');
    $types_new_2[] = mosHTML::makeOption('view_reports', 'Director+ Reports');
    $types_new_2[] = mosHTML::makeOption('view_reports_2', 'Sales Managers Reports');
    $types_new_2[] = mosHTML::makeOption('view_reports_3', 'Customer Service Managers Reports');
    $types_new_2[] = mosHTML::makeOption('view_reports_4', 'Sales and Customer Service Reports');
    $types_new_2[] = mosHTML::makeOption('view_reports_5', 'Production Reports');
    $types_new_2[] = mosHTML::makeOption('add_user', 'Manage Users');
    $types_new_2[] = mosHTML::makeOption('user_list', 'Manage Frontend Users');

    $types_new_2[] = mosHTML::makeOption('manage_deliveries', 'Manage Deliveries');
    $types_new_2[] = mosHTML::makeOption('produce_order', 'Produce Order');
    $types_new_2[] = mosHTML::makeOption('package_order', 'Package Order');
    $types_new_2[] = mosHTML::makeOption('ship_order', 'Ship Order');
    $types_new_2[] = mosHTML::makeOption('packaging_delivery', 'Packaging Delivery');
    $types_new_2[] = mosHTML::makeOption('show_account_number', 'Show Account Number');
    $types_new_2[] = mosHTML::makeOption('postal_code_warehouse_manager', 'Postal Code Warehouse Manager');
    $types_new_2[] = mosHTML::makeOption('proflowers_order_manager', 'Proflowers Order Manager');

    $types_new_2[] = mosHTML::makeOption('phone_order_manager', 'Phone Order Manager');
    $types_new_2[] = mosHTML::makeOption('postal_code', 'Postal Code');
    $types_new_2[] = mosHTML::makeOption('driver_option', 'Driver Option');
    $types_new_2[] = mosHTML::makeOption('tax_manager', 'Tax Manager');
    $types_new_2[] = mosHTML::makeOption('metatag_cfg', 'Metatag CFG');
    $types_new_2[] = mosHTML::makeOption('seo_link', 'SEO Link');
    $types_new_2[] = mosHTML::makeOption('searchlog', 'Search Log');
    $types_new_2[] = mosHTML::makeOption('free_shipping', 'Free Shipping');

    $types_new_2[] = mosHTML::makeOption('com_xmlorder', 'XML Order');
    $types_new_2[] = mosHTML::makeOption('com_testimonial', 'Testimonial Manager');
    $types_new_2[] = mosHTML::makeOption('shipping_surcharge', 'Shipping Surcharge Manager');
    $types_new_2[] = mosHTML::makeOption('com_edit_email_banner', 'Edit Email Banner');
    $types_new_2[] = mosHTML::makeOption('com_edit_title_category', 'Edit Landing page title');
    $types_new_2[] = mosHTML::makeOption('com_edit_banner', 'Edit Top Banner');
    $types_new_2[] = mosHTML::makeOption('com_featured_product', 'Edit Featured Product');
    $types_new_2[] = mosHTML::makeOption('com_edit_corners', 'Edit Corners');
    $types_new_2[] = mosHTML::makeOption('com_donation_vars', 'Donation Vars');
    $types_new_2[] = mosHTML::makeOption('com_landing_products', 'Landing Products');
    $types_new_2[] = mosHTML::makeOption('com_slider', 'Slider');
    $types_new_2[] = mosHTML::makeOption('com_email_sender_occasion', 'Email Sender Occaion');
    $types_new_2[] = mosHTML::makeOption('com_sms_conversation', 'SMS Conversation');
    $types_new_2[] = mosHTML::makeOption('com_email_sender', 'Email Sender');
    $types_new_2[] = mosHTML::makeOption('com_sms_sender', 'SMS Sender');
    $types_new_2[] = mosHTML::makeOption('com_platinum_cart', 'Platinum Club');
    $types_new_2[] = mosHTML::makeOption('com_company_groups', 'Company Shopper Group');
    $types_new_2[] = mosHTML::makeOption('com_extensions', 'Extensions');
    $types_new_2[] = mosHTML::makeOption('com_parse_company_orders', 'Bulk Corporate Orders');
    $types_new_2[] = mosHTML::makeOption('com_bad_emails', 'Blocked Emails');
    $types_new_2[] = mosHTML::makeOption('com_thankyou_review_links', 'Thank You Review Links');
    $types_new_2[] = mosHTML::makeOption('com_warehouse_order_limit', 'Warehouse orders limits');

    $lists['type_new_2'] = mosHTML::selectList($types_new_2, 'filter_type_new_2', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type_new_2");

    // get list of Log Status for dropdown filter
    $logged[] = mosHTML::makeOption(0, 'Registered');
    $logged[] = mosHTML::makeOption(1, 'Logged In');
    $lists['logged'] = mosHTML::selectList($logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_logged");

    HTML_users::showUsers($rows, $pageNav, $search, $option, $lists);
}

/**
 * Edit the user
 * @param int The user ID
 * @param string The URL option
 */
function editUser($uid = '0', $option = 'users') {
    global $database, $my, $acl, $mainframe;

    $msg = checkUserPermissions(array($uid), "edit", true);
    if ($msg) {
        echo "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1);</script>\n";
        exit;
    }

    $row = new mosUser($database);
    // load the row from the db table
    $row->load((int) $uid);

    if ($uid) {
        $query = "SELECT *"
                . "\n FROM #__contact_details"
                . "\n WHERE user_id = $row->id"
        ;
        $database->setQuery($query);
        $contact = $database->loadObjectList();
        $query_bucks = "SELECT bucks FROM `tbl_bucks`  WHERE user_id = $row->id";
        $database->setQuery($query_bucks);
        $bucks = $database->loadResult();
        $bloomex_bucks = '';
        if ($bucks) {
            $query = "SELECT * FROM `tbl_bucks_history`  WHERE user_id = $row->id";
            $database->setQuery($query);
            $bucks_history = $database->loadObjectList();
            if ($bucks_history) {
                $bloomex_bucks .= "
<div id='bucks_history'>
    <table class=\"adminform\" border='1'>
        <tr>
            <th>Order Id</th>
            <th>Used Bucks</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>";

                foreach ($bucks_history as $o) {
                    $date_bucks = new DateTime($o->date_added);
                    $date_bucks->add(new DateInterval('PT10H'));
                    $date_bucks_formatted = $date_bucks->format('Y-m-d H:m:s');
                    $bloomex_bucks .= "<tr>
                <td>$o->order_id</td>
                <td>$o->used_bucks</td>
                <td>$date_bucks_formatted</td>
                <td>$o->date_added</td>
            </tr>";
                    $lastorderDate = $o->date_added;
                }
                $bloomex_bucks .= "
    </table>
</div>";
            }
            $bloomex_bucks .= "<div id='bucks' style='font-size: 14px;font-weight: bold;margin: 5px;color: #c46055;'>Current Bloomex Bucks accumulated is: $" . $bucks . "</div>";
        }
    } else {
        $contact = NULL;
        $row->block = 0;
        $bloomex_bucks = '';
    }

    $query = "SELECT 
        `ucu`.*
    FROM `jos_vm_users_credits_uses` AS `ucu`
    WHERE `ucu`.`user_id`=" . $row->id . " order by ucu.id
    ";

    $database->setQuery($query);
    $user_credits_uses = $database->loadObjectList();

    $query = "SELECT 
        `uc`.`credits`
    FROM `jos_vm_users_credits` AS `uc`
    WHERE `uc`.`user_id`=" . $row->id . "";

    $user_credits = false;
    $database->setQuery($query);
    $database->loadObject($user_credits);

    // check to ensure only super admins can edit super admin info
    if (( $my->gid < 25 ) && ( $row->gid == 25 )) {
        mosRedirect('index2.php?option=com_users', _NOT_AUTH);
    }

    $my_group = strtolower($acl->get_group_name($row->gid, 'ARO'));
    if ($my_group == 'super administrator' && $my->gid != 25) {
        $lists['gid'] = '<input type="hidden" name="gid" value="' . $my->gid . '" /><strong>Super Administrator</strong>';
    } else if ($my->gid == 24 && $row->gid == 24) {
        $lists['gid'] = '<input type="hidden" name="gid" value="' . $my->gid . '" /><strong>Administrator</strong>';
    } else {
        // ensure user can't add group higher than themselves
        $my_groups = $acl->get_object_groups('users', $my->id, 'ARO');
        if (is_array($my_groups) && count($my_groups) > 0) {
            $ex_groups = $acl->get_group_children($my_groups[0], 'ARO', 'RECURSE');
        } else {
            $ex_groups = array();
        }

        $gtree = $acl->get_group_children_tree(null, 'USERS', false);

        // remove users 'above' me
        $i = 0;
        while ($i < count($gtree)) {
            if (in_array($gtree[$i]->value, $ex_groups)) {
                array_splice($gtree, $i, 1);
            } else {
                $i++;
            }
        }

        $lists['gid'] = mosHTML::selectList($gtree, 'gid', 'size="10"', 'value', 'text', $row->gid);
    }

    // build the html select list
    $query = "SELECT * FROM tbl_users_block_history WHERE user_id = " . $row->id . " order by id desc";
    $database->setQuery($query);
    $lists['user_block_history'] = $database->loadObjectList();
    // build the html select list
    $lists['sendEmail'] = mosHTML::yesnoRadioList('sendEmail', 'class="inputbox" size="1"', $row->sendEmail);

    $file = $mainframe->getPath('com_xml', 'com_users');
    //$params = new mosUserParameters($row->params, $file, 'component');
    //=============================================================================================
    $query = "SELECT user_group_id FROM tbl_mix_user_group WHERE user_id = " . $row->id;
    $database->setQuery($query);
    $user_group_id = $database->loadResult();

    $query = "SELECT * FROM tbl_new_user_group ORDER BY id";
    $database->setQuery($query);
    $oNewUserGroup = $database->loadObjectList();

    $lists['newgroup'] = '<select size="18" name="gid"><option value="">&nbsp;Select User Group</option>'; //<option value="29">&nbsp;Public Frontend</option>';	

    if ($row->gid == 18 || $my->gid != 25) {
        $lists['newgroup'] .= '<option value="18" selected="selected">.&nbsp;-&nbsp;Registered</option>';
    } else {
        $lists['newgroup'] .= '<option value="18">.&nbsp;-&nbsp;Registered</option>';
    }

    /* 	if( $row->gid == 19 ) {
      $lists['newgroup']	.= '<option value="19" selected="selected">.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Author</option>';
      }else {
      $lists['newgroup']	.= '<option value="19">.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Author</option>';
      }

      if( $row->gid == 20 ) {
      $lists['newgroup']	.= '<option value="20" selected="selected">.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Editor</option>';
      }else {
      $lists['newgroup']	.= '<option value="20">.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Editor</option>';
      }

      if( $row->gid == 21 ) {
      $lists['newgroup']	.= '<option value="21" selected="selected">.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Publisher</option>';
      }else {
      $lists['newgroup']	.= '<option value="21">.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Publisher</option>';
      }

      if( $row->gid == 25 ) {
      $lists['newgroup']	.= '<option value="30" selected="selected">-&nbsp;Public Backend</option>';
      }else {
      $lists['newgroup']	.= '<option value="30">-&nbsp;Public Backend</option>';
      }
     */
    if (count($oNewUserGroup)) {
        foreach ($oNewUserGroup as $item) {
            $aAccessArea = explode("[--1--]", $item->area_name);
            if (in_array("full_menus", $aAccessArea)) {
                $current_gid = 25;
            } else {
                $current_gid = 24;
            }

            if (strtolower($item->departments_name) == "admin" && $my->gid != 25)
                continue;

            if ($user_group_id == $item->id && ( $row->gid == 24 || $row->gid == 25 )) {
                $lists['newgroup'] .= '<option value="' . $current_gid . '[--1--]' . $item->id . '" selected="selected">&nbsp;&nbsp;-&nbsp;' . $item->departments_name . '</option>';
            } else {
                $lists['newgroup'] .= '<option value="' . $current_gid . '[--1--]' . $item->id . '">&nbsp;&nbsp;-&nbsp;' . $item->departments_name . '</option>';
            }
        }
    }


    $lists['newgroup'] .= '</select>';


    HTML_users::edituser($row, $contact, $lists, $option, $uid, $params, $bloomex_bucks, $user_credits, $user_credits_uses);
}

function saveUser($task) {
    global $database, $my, $acl;
    global $mosConfig_live_site, $mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_sitename;



    //========================= NEW USER GROUP ==========================
    if ($_POST['gid']) {
        $aGID = explode("[--1--]", $_POST['gid']);
        if (count($aGID)) {
            $_POST['gid'] = $aGID[0];
            $user_group_id = $aGID[1];
        }
    }
    //===================================================================	

    $userIdPosted = mosGetParam($_POST, 'id');
    if ($userIdPosted) {
        $msg = checkUserPermissions(array($userIdPosted), 'save', in_array($my->gid, array(24, 25)));
        if ($msg) {
            echo "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1);</script>\n";
            exit;
        }
    }


    $row = new mosUser($database);
    if (!$row->bind($_POST)) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    // sanitise fields
    $row->id = (int) $row->id;
    // sanitise gid field
    $row->gid = (int) $row->gid;

    $isNew = !$row->id;
    $pwd = '';
    $user_credits_result = false;
    $user_credits_value = 0;
    // MD5 hash convert passwords
    if ($isNew) {
        // new user stuff
        if ($row->password == '') {
            $pwd = mosMakePassword();
            $row->password = md5($pwd);
        } else {
            $pwd = $row->password;
            $row->password = md5($row->password);
        }
        $row->registerDate = date('Y-m-d H:i:s');
    } else {
        $original = new mosUser($database);
        $original->load((int) $row->id);

        // existing user stuff
        if ($row->password == '') {
            // password set to null if empty
            $row->password = null;
        } else {
            $row->password = md5($row->password);
        }

        // if group has been changed and where original group was a Super Admin
        if ($row->gid != $original->gid) {
            if ($original->gid == 25) {
                // count number of active super admins
                $query = "SELECT COUNT( id )"
                        . "\n FROM #__users"
                        . "\n WHERE gid = 25"
                        . "\n AND block = 0"
                ;
                $database->setQuery($query);
                $count = $database->loadResult();

                if ($count <= 1) {
                    // disallow change if only one Super Admin exists
                    echo "<script> alert('You cannot change this users Group as it is the only active Super Administrator for your site'); window.history.go(-1); </script>\n";
                    exit();
                }
            }

            $user_group = strtolower($acl->get_group_name($original->gid, 'ARO'));
            if (( $user_group == 'super administrator' && $my->gid != 25)) {
                // disallow change of super-Admin by non-super admin
                echo "<script> alert('You cannot change this users Group as you are not a Super Administrator for your site'); window.history.go(-1); </script>\n";
                exit();
            } else if ($my->gid == 24 && $original->gid == 24) {
                // disallow change of super-Admin by non-super admin
                echo "<script> alert('You cannot change the Group of another Administrator as you are not a Super Administrator for your site'); window.history.go(-1); </script>\n";
                exit();
            } // ensure user can't add group higher than themselves done below
        }

        $query = "SELECT 
            `uc`.`credits`
        FROM `jos_vm_users_credits` AS `uc`
        WHERE `uc`.`user_id`=" . $row->id . "";

        $user_credits = false;

        $database->setQuery($query);
        $user_credits_result = $database->loadObject($user_credits);

        $user_credits_value = $user_credits->credits;
    }

    // if user is made a Super Admin group and user is NOT a Super Admin		
    if ($row->gid == 25 && $my->gid != 25) {
        // disallow creation of Super Admin by non Super Admin users
        echo "<script> alert('You cannot create a user with this user Group level, only Super Administrators have this ability'); window.history.go(-1); </script>\n";
        exit();
    }

    // if user is made a Super Admin group and user is NOT a Super Admin		
    if ($row->gid != $my->gid && $my->id == $row->id) {
        // disallow creation of Super Admin by non Super Admin users
        echo "<script> alert('You cannot change Group level of yourself, please use another Super Administrators to do this'); window.history.go(-1); </script>\n";
        exit();
    }


    // Security check to avoid creating/editing user to higher level than himself: response to artf4529.
    /* if (!in_array($row->gid,getGIDSChildren($my->gid))) {
      // disallow creation of Super Admin by non Super Admin users
      echo "<script> alert('You cannot create a user with this user Group level, only Super Administrators have this ability'); window.history.go(-1); </script>\n";
      exit();
      } */

    // save usertype to usetype column
    $query = "SELECT name"
            . "\n FROM #__core_acl_aro_groups"
            . "\n WHERE group_id = " . (int) $row->gid
    ;
    $database->setQuery($query);
    $usertype = $database->loadResult();
    $row->usertype = $usertype;

    // save params
    $params = mosGetParam($_POST, 'params', '');
    if (is_array($params)) {
        $txt = array();
        foreach ($params as $k => $v) {
            $txt[] = "$k=$v";
        }
        $row->params = implode("\n", $txt);
    }

    if (!$row->check()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $row->checkin();

    // updates the current users param settings
    if ($my->id == $row->id) {
        //session_start();
        $_SESSION['session_user_params'] = $row->params;
        session_write_close();
    }


    //========================= NEW USER GROUP ==========================
    if ($user_group_id) {
        $query = "SELECT user_id FROM tbl_mix_user_group WHERE user_id = $row->id";
        $database->setQuery($query);
        $user_id = $database->loadResult();

        if ($user_id) {
            $query = "UPDATE tbl_mix_user_group SET user_group_id = $user_group_id WHERE user_id = $user_id";
            $database->setQuery($query);
            $database->query() or die($database->stderr());
        } else {
            $query = "INSERT INTO tbl_mix_user_group( user_id, user_group_id ) VALUES( $row->id, $user_group_id )";
            $database->setQuery($query);
            $database->query() or die($database->stderr());
        }
    }
    //===================================================================
    // update the ACL
    if (!$isNew) {
        $query = "SELECT aro_id"
                . "\n FROM #__core_acl_aro"
                . "\n WHERE value = " . (int) $row->id
        ;
        $database->setQuery($query);
        $aro_id = $database->loadResult();

        $query = "UPDATE #__core_acl_groups_aro_map"
                . "\n SET group_id = $row->gid"
                . "\n WHERE aro_id = $aro_id"
        ;
        $database->setQuery($query);
        $database->query() or die($database->stderr());
    }

    // for new users, email username and password
    if ($isNew) {
        $query = "SELECT email"
                . "\n FROM #__users"
                . "\n WHERE id = " . (int) $my->id
        ;
        $database->setQuery($query);
        $adminEmail = $database->loadResult();

        $subject = _NEW_USER_MESSAGE_SUBJECT;
        $message = sprintf(_NEW_USER_MESSAGE, $row->name, $mosConfig_sitename, $mosConfig_live_site, $row->username, $pwd);

        if ($mosConfig_mailfrom_noreply != "" && $mosConfig_fromname != "") {
            $adminName = $mosConfig_fromname;
            $adminEmail = $mosConfig_mailfrom_noreply;
        } else {
            $query = "SELECT name, email"
                    . "\n FROM #__users"
                    // administrator
                    . "\n WHERE gid = 25"
            ;
            $database->setQuery($query);
            $admins = $database->loadObjectList();
            $admin = $admins[0];
            $adminName = $admin->name;
            $adminEmail = $admin->email;
        }

        mosMail($adminEmail, $adminName, $row->email, $subject, $message);
    }

    if (!$isNew) {
        // if group has been changed
        if ($original->gid != $row->gid) {
            // delete user acounts active sessions
            logoutUser($row->id, 'com_users', 'change');
        }
    }
    $credits = floatval($_POST['user_credits']);

    if ($user_credits_result === false OR $user_credits_value != $credits) {

        $credits_comments = 'Change credits from $' . number_format($user_credits_value, 2) . ' to $' . number_format($credits, 2) . '.';

        $query = "INSERT INTO `jos_vm_users_credits_uses`
        ( 
            `user_id`,
            `comments`,
            `username`,
            `datetime`
        )
        VALUES (
            " . (int) $row->id . ",
            '" . $database->getEscaped($credits_comments) . "',
            '" . $database->getEscaped($my->username) . "',
            '" . date('Y-m-d H:i:s') . "'
        )";

        $database->setQuery($query);
        $database->query();

        if ($user_credits_result === false) {
            $query = "INSERT INTO `jos_vm_users_credits` 
            (
                `user_id`,
                `credits`
            )
            VALUES (
                " . (int) $row->id . ",
                '" . $credits . "'
            )";

            $database->setQuery($query);
            $database->query();
        } else {
            $query = "UPDATE `jos_vm_users_credits` 
            SET
                `credits`='" . $credits . "'
            WHERE `user_id`=" . (int) $row->id . "
            ";

            $database->setQuery($query);
            $database->query();
        }
    }
    switch ($task) {
        case 'apply':
            $msg = 'Successfully Saved changes to User: ' . $row->name;
            mosRedirect('index2.php?option=com_users&task=editA&hidemainmenu=1&id=' . $row->id, $msg);
            break;

        case 'save':
        default:
            $msg = 'Successfully Saved User: ' . $row->name;
            mosRedirect('index2.php?option=com_users', $msg);
            break;
    }
}

/**
 * Cancels an edit operation
 * @param option component option to call
 */
function cancelUser($option) {
    mosRedirect('index2.php?option=' . $option . '&task=view');
}

function removeUsers($cid, $option) {
    global $database, $acl, $my;
    global $mosConfig_mailfrom_noreply, $mosConfig_live_site, $mosConfig_fromname, $mos_debug_email;

    if (!is_array($cid) || count($cid) < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    $msg = checkUserPermissions($cid, 'delete');

    if (!$msg && count($cid)) {
        $obj = new mosUser($database);
        foreach ($cid as $id) {
            $obj->load($id);
            $count = 2;
            if ($obj->gid == 25) {
                // count number of active super admins
                $query = "SELECT COUNT( id )"
                        . "\n FROM #__users"
                        . "\n WHERE gid = 25"
                        . "\n AND block = 0"
                ;
                $database->setQuery($query);
                $count = $database->loadResult();
            }

            if ($count <= 1 && $obj->gid == 25) {
                // cannot delete Super Admin where it is the only one that exists
                $msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";
            } else {

                $query = "SELECT `email`, `username` FROM `jos_users` WHERE `id`=$id LIMIT 1";
                $database->setQuery($query);
                $user_email = $database->loadObjectList();
                $user_email = $user_email[0];

                mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $user_email->email, $mosConfig_fromname . ' user account - Delete.', 'Your account name "' . $user_email->username . '" has been deleted!', 1);



                // delete user
                $deleted = $obj->delete($id);
                $msg = ($deleted)?'User(s) was deleted successfully.':$obj->getError();

                // delete user acounts active sessions
                logoutUser($id, 'com_users', 'remove');
            }
        }
    }

    mosRedirect('index2.php?option=' . $option, $msg);
}

/*
  function removeUsers( $cid, $option ) {
  global $database, $acl, $my;

  if (!is_array( $cid ) || count( $cid ) < 1) {
  echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
  exit;
  }

  if ( count( $cid ) ) {
  $obj = new mosUser( $database );
  foreach ($cid as $id) {
  // check for a super admin ... can't delete them
  $groups 	= $acl->get_object_groups( 'users', $id, 'ARO' );
  $this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );
  if ( $this_group == 'super administrator' && $my->gid != 25 ) {
  $msg = "You cannot delete a Super Administrator";
  } else if ( $id == $my->id ){
  $msg = "You cannot delete Yourself!";
  } else if ( ( $this_group == 'administrator' ) && ( $my->gid == 24 ) ){
  $msg = "You cannot delete another `Administrator` only `Super Administrators` have this power";
  } else {
  $obj->load( $id );
  $count = 2;
  if ( $obj->gid == 25 ) {
  // count number of active super admins
  $query = "SELECT COUNT( id )"
  . "\n FROM #__users"
  . "\n WHERE gid = 25"
  . "\n AND block = 0"
  ;
  $database->setQuery( $query );
  $count = $database->loadResult();
  }

  if ( $count <= 1 && $obj->gid == 25 ) {
  // cannot delete Super Admin where it is the only one that exists
  $msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";
  } else {
  // delete user
  $obj->delete( $id );
  $msg = $obj->getError();

  // delete user acounts active sessions
  logoutUser( $id, 'com_users', 'remove' );
  }
  }
  }
  }

  mosRedirect( 'index2.php?option='. $option, $msg );
  }
 */

/**
 * Blocks or Unblocks one or more user records
 * @param array An array of unique category id numbers
 * @param integer 0 if unblock, 1 if blocking
 * @param string The current url option
 */
function changeUserBlock($cid = null, $block = 1, $option) {
    global $database;

    $action = $block ? 'block' : 'unblock';

    if (count($cid) < 1) {
        echo "<script type=\"text/javascript\"> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $msg = checkUserPermissions($cid, $action);
    if ($msg) {
        echo "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode(',', $cid);

    $query = "UPDATE #__users"
            . "\n SET block = $block"
            . "\n WHERE id IN ( $cids )"
    ;
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    // if action is to block a user
    if ($block == 1) {
        foreach ($cid as $id) {
            // delete user acounts active sessions
            logoutUser($id, 'com_users', 'block');
        }
    }

    mosRedirect('index2.php?option=' . $option);
}

/*
  function changeUserBlock( $cid=null, $block=1, $option ) {
  global $database;

  if (count( $cid ) < 1) {
  $action = $block ? 'block' : 'unblock';
  echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
  exit;
  }

  $cids = implode( ',', $cid );

  $query = "UPDATE #__users"
  . "\n SET block = $block"
  . "\n WHERE id IN ( $cids )"
  ;
  $database->setQuery( $query );
  if (!$database->query()) {
  echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  exit();
  }

  // if action is to block a user
  if ( $block == 1 ) {
  foreach( $cid as $id ) {
  // delete user acounts active sessions
  logoutUser( $id, 'com_users', 'block' );
  }
  }

  mosRedirect( 'index2.php?option='. $option );
  }
 */

/**
 * @param array An array of unique user id numbers
 * @param string The current url option
 */
function logoutUser($cid = null, $option, $task) {
    global $database, $my;

    if (is_array($cid)) {
        if (count($cid) < 1) {
            mosRedirect('index2.php?option=' . $option, 'Please select a user');
        }

        foreach ($cid as $cidA) {
            $temp = new mosUser($database);
            $temp->load($cidA);

            // check to see whether a Administrator is attempting to log out a Super Admin
            if (!( $my->gid == 24 && $temp->gid == 25 )) {
                $id[] = $cidA;
            }
        }
        $ids = implode(',', $id);
    } else {
        $temp = new mosUser($database);
        $temp->load($cid);

        // check to see whether a Administrator is attempting to log out a Super Admin
        if ($my->gid == 24 && $temp->gid == 25) {
            echo "<script> alert('You cannot log out a Super Administrator'); window.history.go(-1); </script>\n";
            exit();
        }
        $ids = $cid;
    }

    $query = "DELETE FROM #__session"
            . "\n WHERE userid IN ( $ids )"
    ;
    $database->setQuery($query);
    $database->query();

    switch ($task) {
        case 'flogout':
            mosRedirect('index2.php', $database->getErrorMsg());
            break;

        case 'remove':
        case 'block':
        case 'change':
            return;
            break;

        default:
            mosRedirect('index2.php?option=' . $option, $database->getErrorMsg());
            break;
    }
}

/**
 * Check if users are of lower permissions than current user (if not super-admin) and if the user himself is not included
 *
 * @param array of userId $cid
 * @param string $actionName to insert in message.
 * @return string of error if error, otherwise null
 * Added 1.0.11
 */
function checkUserPermissions($cid, $actionName, $allowActionToMyself = false) {
    global $database, $acl, $my;

    $msg = null;
    if (is_array($cid) && count($cid)) {
        $obj = new mosUser($database);
        foreach ($cid as $id) {
            if ($id != 0) {
                $obj->load($id);
                $groups = $acl->get_object_groups('users', $id, 'ARO');
                $this_group = strtolower($acl->get_group_name($groups[0], 'ARO'));
            } else {
                $this_group = 'Registered';  // minimal user group
                $obj->gid = $acl->get_group_id($this_group, 'ARO');
            }

            if (!$allowActionToMyself && $id == $my->id) {
                $msg .= 'You cannot ' . $actionName . ' Yourself!';
            } else if (($obj->gid == $my->gid && !in_array($my->gid, array(24, 25))) || ($obj->gid && !in_array($obj->gid, getGIDSChildren($my->gid)))) {
                $msg .= 'You cannot ' . $actionName . ' a `' . $this_group . '`. Only higher-level users have this power. ';
            }
        }
    }

    return $msg;
}

/**
 * Added 1.0.11
 */
function getGIDSChildren($gid) {
    global $database;

    $standardlist = array(-2,);

    $query = "SELECT g1.group_id, g1.name"
            . "\n FROM #__core_acl_aro_groups g1"
            . "\n LEFT JOIN #__core_acl_aro_groups g2 ON g2.lft >= g1.lft"
            . "\n WHERE g2.group_id = " . $gid
            . "\n ORDER BY g1.name"
    ;
    $database->setQuery($query);
    $array = $database->loadResultArray();

    if ($gid > 0) {
        $standardlist[] = -1;
    }
    $array = array_merge($array, $standardlist);

    return $array;
}

/**
 * Added 1.0.11
 */
function getGIDSParents($gid) {
    global $database;

    $query = "SELECT g1.group_id, g1.name"
            . "\n FROM #__core_acl_aro_groups g1"
            . "\n LEFT JOIN #__core_acl_aro_groups g2 ON g2.lft <= g1.lft"
            . "\n WHERE g2.group_id = " . $gid
            . "\n ORDER BY g1.name"
    ;
    $database->setQuery($query);
    $array = $database->loadResultArray();

    return $array;
}
?>
