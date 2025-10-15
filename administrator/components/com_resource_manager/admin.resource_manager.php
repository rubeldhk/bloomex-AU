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
$id = mosGetParam($_REQUEST, "id", "");
$limitstart = mosGetParam($_REQUEST, "limitstart", null);
$limit = mosGetParam($_REQUEST, "limit", null);
$task = mosGetParam($_REQUEST, "task", null);
$step = 0;

switch ($act) {
    case 'new':
        if ($task == 'remove') {
            remove($option);
        } else if ($task == 'show') {
            show($option);
        } else {
            edit('0', $option);
        }
        break;

    case 'edit':
        edit($id, $option);
        break;

    case 'save':
        save($option);
        break;
        
    default:
        show($option);
        break;
}

function show($option)
{
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mainframe, $mosConfig_list_limit;

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');

    if ($mysqli->connect_errno) {
        die("Mysql connection error: " . $mysqli->connect_error);
    }

    $limit = (int)$mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = (int)$mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $search = trim(mosGetParam($_REQUEST, 'search', ''));

    $where = "";
    if (!empty($search)) {
        $safeSearch = $mysqli->real_escape_string($search);
        $where = "WHERE name LIKE '%" . $safeSearch . "%'";
    }

    $countQuery = "SELECT COUNT(DISTINCT id) AS total
                    FROM jos_vm_resource_managers
                   $where";
    $countResult = $mysqli->query($countQuery);
    $row = $countResult->fetch_assoc();
    $total = (int)$row['total'];
    $countResult->free();

    require_once($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT 
            *
        FROM 
            jos_vm_resource_managers
        $where
        ORDER BY 
            queue ASC
        LIMIT {$limitstart}, {$limit}
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        die("SyntaxError: " . $mysqli->error);
    }

    $rows = [];
    while ($obj = $result->fetch_object()) {
        $rows[] = $obj;
    }

    $result->free();
    $mysqli->close();

    HTML_Resource_Manager_Settings::show($rows, $pageNav, $option, $search);
}

function edit($id, $option)
{
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');

    if ($mysqli->connect_errno) {
        die("Mysql connection error: " . $mysqli->connect_error);
    }

    $id = (int)$id;
    $query = "SELECT *
        FROM `jos_vm_resource_managers`
        WHERE id = $id";
    $result = $mysqli->query($query);
    
    if (!$result) {
        die("SyntaxError greeting card: " . $mysqli->error);
    }
    
    $row = $result->fetch_object();
    if (!$row) {
        $row = (object) [
            'id' => $id,
            'name' => '',
            'description' => '',
            'type' => '',
            'headerContent' => '',
            'footerContent' => '',
            'bodyContent' => '',
            'status' => '',
            'queue' => '',
            'alias' => '',
        ];
    }

    $mysqli->close();

    HTML_Resource_Manager_Settings::edit($row, $option);
}

function save($option)
{
    global $my;
    
    $name = mosGetParam($_POST, "name");
    $description = mosGetParam($_POST, "description");
    $type = mosGetParam($_POST, "type", 0);
    $headerContent = $_POST["headerContent"] ?: "";
    $footerContent = $_POST["footerContent"] ?: "";
    $bodyContent = $_POST["bodyContent"] ?: "";
    $status = mosGetParam($_POST, "status", "0");
    $queue = mosGetParam($_POST, "queue", "1");
    $alias = mosGetParam($_POST, "alias", "");

    $row = new mosResourceManagerOptionSettings();
    if (!$row->bind($_POST)) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $row->name = $name;
    $row->description = $description;
    $row->type = $type;
    $row->header_content = $headerContent;
    $row->body_content = $bodyContent;
    $row->footer_content = $footerContent;
    $row->author_name = $my->username;
    $row->status = $status;
    $row->queue = $queue;
    $row->alias = $alias;
    $row->created_at = date('y-m-d H:i:s');
    $row->updated_at = date('y-m-d H:i:s');

    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    
    mosRedirect("index2.php?option=$option&act=show", "Saved Resource Successfully");
}

function remove($option)
{
    global $database;

    $cid = $_POST['cid'];
    if (count($cid)) {
        foreach ($cid as $value) {
            $query = "DELETE FROM jos_vm_resource_managers WHERE id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }
    }

    mosRedirect("index2.php?option=$option&act=show", "Remove Resource Successfully");
}
