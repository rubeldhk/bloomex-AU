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

require_once($mainframe->getPath('admin_html'));
require_once($mainframe->getPath('class'));


$act = mosGetParam($_REQUEST, "act", "");
$cid = josGetArrayInts('cid');
$step = 0;

//die($act);
switch ($act) {

    default:
        switch ($task) {
            case 'new':
                editThirdPartyFlorist('0', $option);
                break;

            case 'edit':
                editThirdPartyFlorist(intval($cid[0]), $option);
                break;

            case 'editA':
                $id = mosGetParam($_REQUEST, "id", "");
                editThirdPartyFlorist($id, $option);
                break;

            case 'save':
                saveThirdPartyFlorist($option);
                break;

            case 'remove':
                removeThirdPartyFlorist($cid, $option);
                break;

            case 'cancel':
                cancelThirdPartyFlorist();
                break;

            default:
                showThirdPartyFlorist($option);
                break;
        }
        break;

}


//=================================================== LandingPages OPTION ===================================================
function showThirdPartyFlorist($option)
{
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));

    $filter_key = trim(mosGetParam($_POST, "filter_key"));

    $where = "";
    $aWhere = array();


    if ($filter_key) {
        $aWhere[] = " (name LIKE '%$filter_key%' OR email LIKE '%$filter_key%') ";
    }

    if (count($aWhere)) $where = " WHERE " . implode(" AND ", $aWhere);

    // get the total number of records
    $query = "SELECT COUNT(*) FROM tbl_third_party_florist $where";
    $database->setQuery($query);
    $total = $database->loadResult();

    require_once($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // get the subset (based on limits) of required records
    $query = "SELECT * FROM tbl_third_party_florist $where ORDER BY name ASC";
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    $lists = array();
    $types = array();

    $lists['filter_key'] = $filter_key;

    HTML_ThirdPartyFlorist::showThirdPartyFlorist($rows, $pageNav, $option, $lists);
}


function editThirdPartyFlorist($id, $option)
{
    global $database, $my, $mosConfig_absolute_path;

    $row = new mosThirdPartyFlorist($database);
    // load the row from the db table
    $row->load((int)$id);

    if (!$id) {
        $row->name = "";
        $row->email = "";
        $row->phone = "";
        $row->note = "";
        $row->price = "";
        $row->type = "1";
    }

    $lists = array();
    $types = array();


    HTML_ThirdPartyFlorist::editThirdPartyFlorist($row, $option, $lists);
}

function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

function saveThirdPartyFlorist($option)
{
    global $database, $mosConfig_absolute_path, $act;

    $row = new mosThirdPartyFlorist($database);


    if (!$row->bind($_POST)) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    // save the changes
    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    mosRedirect("index2.php?option=$option&act=$act", "Save Third Party Florist Successfully");
}


function removeThirdPartyFlorist(&$cid, $option)
{
    global $database, $act, $mosConfig_absolute_path;

    if (count($cid)) {
        foreach ($cid as $value) {
            $query = "DELETE FROM tbl_third_party_florist WHERE id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }
    }

    mosRedirect("index2.php?option=$option", "Third Party Florist Removed Successfully");
}


function cancelThirdPartyFlorist()
{
    mosRedirect('index2.php?option=com_third_party_florist');
}
