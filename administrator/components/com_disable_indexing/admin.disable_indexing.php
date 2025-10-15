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
            case 'getCsv':
                getCsv();
                break;
            case 'parseCsv':
                parseCsv();
                break;

            default:
                showDisableIndexing($option);
                break;
        }
        break;

}


function getCsv()
{
    global $database;

    $query = "SELECT * FROM `tbl_disable_indexing`";

    ob_end_clean();

    header('Content-Description: File Transfer');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename=list.csv');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: no-cache');

    $fp = fopen('php://output', 'w');

    $csv[0] = array(
        'Url'
    );
    fputcsv($fp, $csv[0]);

    $i = 1;
    $database->setQuery($query);
    $postalcodes_obj = $database->loadObjectList();

    foreach ($postalcodes_obj as $postalcode_obj) {
        $csv[$i] = array(
            $postalcode_obj->url
        );
        fputcsv($fp, $csv[$i]);

        $i++;
    }
    fclose($fp);

    die;
}

function parseCsv()
{
    global $database;

    $return = array();
    $return['result'] = false;

    $tmp_name = $_FILES['file']['tmp_name'];

    $csv = array_map('str_getcsv', file($_FILES['file']['tmp_name']));

    $inserts = $removesAUS = $removesNZL = array();


    unset($csv[0]);

    foreach ($csv as $line) {
        $line = array_map('trim', $line);


        $inserts[] = "(
            '" . $database->getEscaped(trim(parse_url(urldecode($line[0]), PHP_URL_PATH), '/')) . "'
        )";
    }

    if (sizeof($inserts) > 0) {
        $return['result'] = true;
        $return['inserts'] = $inserts;
        $return['sizeof_inserts'] = sizeof($inserts);

        $query = "DELETE FROM `tbl_disable_indexing`";
        $database->setQuery($query);
        $database->query();


        $query = "INSERT INTO 
        `tbl_disable_indexing` (`url`)
        VALUES " . implode(',', $inserts) . "";

        $database->setQuery($query);
        $database->query();
    } else {
        $return['error'] = 'Incorrect Text File.';
    }

    echo json_encode($return);
    die;
}


//=================================================== POSTAL CODE OPTION ===================================================
function showDisableIndexing($option)
{
    global $database, $mainframe, $mosConfig_list_limit;

    mosCommonHTML::loadBootstrap(true);

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
    $url = mosGetParam($_REQUEST, "url", "");

    $where = array();
    if ($url) {
        $where[] = "`url`LIKE '" . $database->getEscaped($url) . "'";
    }

    // get the total number of records
    $query = "SELECT COUNT(*) AS total FROM tbl_disable_indexing
            " . (sizeof($where) > 0 ? "WHERE " . implode(' AND ', $where) . "" : '') . "";

    $database->setQuery($query);
    $total = $database->loadResult();

    require_once($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // get the subset (based on limits) of required records
    $query = "SELECT * FROM tbl_disable_indexing
			  " . (sizeof($where) > 0 ? "WHERE " . implode(' AND ', $where) . "" : '') . "
			 ";

    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();


    HTML_DisableIndexing::showDisableIndexing($rows, $pageNav, $option);
}


?>
