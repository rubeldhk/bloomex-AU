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
defined('_VALID_MOS') or die('Restricted access');
DEFINE('DEBUG', true);
require_once( $mainframe->getPath('admin_html') );
require_once( $mainframe->getPath('class') );


$act = mosGetParam($_REQUEST, "act", "");
$cid = josGetArrayInts('cid');
$step = 0;

//die($act);
switch ($act) {


    //=============================================================================================
    default:
        switch ($task) {
            case 'new':
                editCompanyGroupPages('0', $option);
                break;

            case 'edit':
                editCompanyGroupPages(intval($cid[0]), $option);
                break;

            case 'editA':
                editCompanyGroupPages($id, $option);
                break;

            case 'save':
                saveCompanyGroupPages($option);
                break;

            case 'remove':
                removeCompanyGroupPages($cid, $option);
                break;

            case 'cancel':
                cancelCompanyGroupPages();
                break;
            case 'parse_xlsx':
                parse_xlsx();
                break;
            case 'add_domains':
                add_domains();
                break;
            default:
                showCompanyGroupPages($option);
                break;
        }
        break;
}

function add_domains() {
    global $database;
    $k = 0;
    $f = 0;
    $p = 0;
    $text = '';
    if ($_SESSION['domains'] && count($_SESSION['domains']) > 0) {
        $insert_str = '';
        $domain = '';
        foreach ($_SESSION['domains'] as $q => $dom) {

            $sql_check = "SELECT *
                                FROM `company_groups`
                                WHERE `company_domain` LIKE '" . $database->getEscaped($dom[0]) . "'";
            $database->setQuery($sql_check);
            $total = $database->loadResult();
            if (!$total) {
                $insert_str .= "('" . $database->getEscaped($dom[1]) . "','" . $database->getEscaped($dom[0]) . "','16'),";
                update_users_groups($dom[0], '16');
                $f++;
                //$text.=$dom[1]." - ".$dom[0]." <span style='color:blue;float:right'>Added</span><br>";
            } else {
                $p++;
                //$text.=$dom[1]." - ".$dom[0]." <span style='color:red;float:right'>Already Exist</span><br>";
            }
            $k++;
            unset($_SESSION['domains'][$q]);
            if ($k == 20) {
                break;
            }
        }
        $text .= "<span>Parsed $k Domains.<br> $f - New<br> $p - Already Exist<br>Last Parsed Line - " . $dom[1] . " - " . $dom[0] . "</span><br><br>";

        $insert_str = rtrim($insert_str, ",");
        if ($insert_str != '') {
            $sql_insert = "INSERT INTO company_groups  (company_name, company_domain,company_group_id) VALUES $insert_str ";
            $database->setQuery($sql_insert);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }


        $out = array('result' => $text, 'new' => $f, 'exist' => $p, 'header_text' => "Parsing Line - " . $dom[1] . " - " . $dom[0] . "");
    } else {
        $text = 'Process Finished';
        $out = array('result' => $text);
    }

    exit(json_encode($out));
}

function update_users_groups($domain, $shop_group_id) {
    global $database;

    $domain = '@' . $database->getEscaped($domain);
    $query_users = "SELECT id FROM  jos_users  where email LIKE  '%$domain' or username LIKE '%$domain'";
    $database->setQuery($query_users);
    $rows_users = $database->loadObjectList();

    $inserts = array();
    if (count($rows_users) > 0) {
        foreach ($rows_users as $r) {
            $inserts[] = "(" . $r->id . ", " . $shop_group_id . ")";
        }
    }
    if (sizeof($inserts) > 0) {
        $query = "INSERT INTO `jos_vm_shopper_vendor_xref`
        (
            `user_id`, 
            `shopper_group_id`
        ) 
        VALUES
            " . implode(',', $inserts) . "
        ON DUPLICATE KEY UPDATE `shopper_group_id`=" . $shop_group_id . "";

        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        }
    }
}

function parse_xlsx() {
    global $database;
    $query = "SELECT domain FROM  jos_free_email_domains ";
    $database->setQuery($query);
    $free_domains = $database->loadObjectList();
    $except_domains = array();
    foreach ($free_domains as $v) {
        $except_domains[] = $v->domain;
    }


    require_once $_SERVER['DOCUMENT_ROOT']."/scripts/simplexlsx.class.php";
    $xlsx = new SimpleXLSX($_FILES['file']['tmp_name']);
    $sheetNames = array_keys($xlsx->sheetNames());
    $sheet_num = $sheetNames[0];
    if (0 < $_FILES['file']['error']) {

        $res = array('Error: ' . $_FILES['file']['error']);
    } else {

        list($num_cols, $num_rows) = $xlsx->dimension($sheet_num);
        $k = 0;
        if ($xlsx->rows($sheet_num)) {
            unset($_SESSION['domains']);
            foreach ($xlsx->rows(2) as $r) {
                $a = array();
                for ($i = 0; $i < $num_cols; $i++) {
                    if ($database->getEscaped($r[$i]) != "") {
                        $a[] = $database->getEscaped($r[$i]);
                    }
                }

                if ($a && !in_array($a[0], $except_domains)) {
                    $_SESSION['domains'][] = $a;
                }

            }
            $res = array('success', count($_SESSION['domains']));
        } else {
            $res = array('invalid file');
        }
    }
    exit(json_encode($res));
}

//=================================================== LandingPages OPTION ===================================================
function showCompanyGroupPages($option) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
    $filter_groups = mosGetParam($_POST, "filter_groups", -1);
    $filter_key = trim(mosGetParam($_POST, "filter_key"));

    $where = "";
    $aWhere = array();

    if ($filter_groups > 0) {
        $aWhere[] = " c.company_group_id = $filter_groups ";
    }

    if ($filter_key) {
        $aWhere[] = " (c.company_name LIKE '%$filter_key%' OR c.company_domain LIKE '%$filter_key%') ";
    }

    if (count($aWhere))
        $where = " WHERE " . implode(" AND ", $aWhere);

    // get the total number of records
    $query = "SELECT COUNT(*) FROM company_groups as c Left Join jos_vm_shopper_group as s on s.shopper_group_id = c.company_group_id $where";
    $database->setQuery($query);
    $total = $database->loadResult();

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // get the subset (based on limits) of required records
    $query = "SELECT c.*,s.shopper_group_name as company_croup_name FROM company_groups as c Left Join jos_vm_shopper_group as s on s.shopper_group_id = c.company_group_id $where ORDER BY c.id DESC";
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    $query_croup = "SELECT shopper_group_id,shopper_group_name FROM  jos_vm_shopper_group ";
    $database->setQuery($query_croup);
    $rows_group = $database->loadObjectList();


    $lists = array();
    $groups = array();
    $groups[] = mosHTML::makeOption("-1", "------ Select Shopper Group ------");
    foreach ($rows_group as $r) {
        $groups[] = mosHTML::makeOption($r->shopper_group_id, $r->shopper_group_name);
    }
    $lists['filter_groups'] = mosHTML::selectList($groups, 'filter_groups', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_groups);

    $lists['filter_key'] = $filter_key;

    HTML_CompanyGroupPages::showCompanyGroupPages($rows, $pageNav, $option, $lists);
}

function editCompanyGroupPages($id, $option) {
    global $database, $my, $mosConfig_absolute_path;

    $row = new mosCompanyGroupPages($database);
    // load the row from the db table
    $row->load((int) $id);

    if (!$id) {
        $row->company_domain = '';
        $row->company_name = "";
        $row->company_group_id = "";
        $row->company_invoice = "";
    }

    $lists = array();
    $groups = array();

    $query_croup = "SELECT shopper_group_id,shopper_group_name FROM  jos_vm_shopper_group ";
    $database->setQuery($query_croup);
    $rows_group = $database->loadObjectList();

    $groups = array();
    $groups[] = mosHTML::makeOption("-1", "------ Select Shopper Group ------");
    foreach ($rows_group as $r) {
        $groups[] = mosHTML::makeOption($r->shopper_group_id, $r->shopper_group_name);
    }
    $lists['groups'] = mosHTML::selectList($groups, 'company_group_id', 'class="inputbox" size="1"', 'value', 'text', $row->company_group_id);



    HTML_CompanyGroupPages::editCompanyGroupPages($row, $option, $lists);
}

function saveCompanyGroupPages($option) {
    global $database, $mosConfig_absolute_path, $act;
    $free_domain_check_query = "SELECT id FROM  jos_free_email_domains  where domain LIKE '" . $database->getEscaped($_POST["company_domain"]) . "'";
    $database->setQuery($free_domain_check_query);
    $free_domain_check = $database->loadObjectList();

    if (count($free_domain_check) > 0) {
        echo "<script> alert('You can not set up company shopper group with free email domain'); window.history.go(-1); </script>\n";
        exit();
    }
    if ($_POST["id"] == '') {
        $query_check_double = "SELECT company_domain FROM  company_groups  where company_domain LIKE '" . $database->getEscaped($_POST["company_domain"]) . "'";
        $database->setQuery($query_check_double);
        $rows_check_double = $database->loadObjectList();
        if (count($rows_check_double) > 0) {
            echo "<script> alert('company domain already exist'); window.history.go(-1); </script>\n";
            exit();
        }
    }
    $query_check = "SELECT company_domain FROM  company_groups  where id LIKE '" . $_POST["id"] . "'";
    $database->setQuery($query_check);
    $rows_check = $database->loadObjectList();
    $company_domain = '';
    if (count($rows_check) > 0) {
        $company_domain = $rows_check[0]->company_domain;
    }
    $row = new mosCompanyGroupPages($database);
    if (isset($_POST['company_invoice'])) {
        $_POST['company_invoice'] = 1;
    } else {
        $_POST['company_invoice'] = 0;
    }
    if (!$row->bind($_POST)) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    // save the changes
    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }


    $domain = $database->getEscaped($_POST['company_domain']);
    $croup_id = $database->getEscaped($_POST['company_group_id']);
    if ($croup_id > 0 & $domain != '') {
        update_users_groups($domain, $croup_id);
    }

    if ($company_domain != '' & $company_domain != $domain) {
        update_users_groups($company_domain, '5');
    }

    mosRedirect("index2.php?option=$option&act=$act", "Save Company Successfully");
}

function removeCompanyGroupPages(&$cid, $option) {
    global $database, $act, $mosConfig_absolute_path;

    if (count($cid)) {
        foreach ($cid as $value) {

            $query_check = "SELECT company_domain FROM  company_groups  where id LIKE '" . $value . "'";
            $database->setQuery($query_check);
            $rows_check = $database->loadObjectList();

            if (count($rows_check) > 0) {
                $company_domain = $rows_check[0]->company_domain;
                update_users_groups($company_domain, '5');
            }

            $query = "DELETE FROM company_groups WHERE id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }
    }

    mosRedirect("index2.php?option=$option", "Remove Company Successfully");
}

function cancelCompanyGroupPages() {
    mosRedirect('index2.php?option=com_company_groups');
}

?>