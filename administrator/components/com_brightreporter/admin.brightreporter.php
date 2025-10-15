<?php

/* * **************************************************************************************************
 * Package : Brightcode Reporter
 * Author : Theo van der Sluijs
 * Llink : http://www.brightcode.eu
 * Copyright (C) : 2007 Brightcode.eu
 * Email : info@brightcode.eu
 * Date : October 2007
 * Package Code License :  Commercial License / http://www.brightcode.eu
 * Joomla! API Code License : http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * JavaScript Code & CSS : Commercial License / http://www.brightcode.eu
 * ***************************************************************************************************
 * Copyrights (c) 2007
 * All rights reserved. Brightcode.eu
 *
 * This program is Commercial software.
 * Unauthorized reproduction is not allowed.
 * Read the complete license model on our site before using this product
 * http://www.brightcode.eu
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.
 *
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * *************************************************************************************************** */

// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_weblinks'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}
// In file admin.mycomp.php
require_once($mainframe->getPath('admin_html'));
require_once('SqlFormatter.php');
if (!$act || $act == '') {
    $act = "reports";
}

$cid = josGetArrayInts('cid');

switch ($act) {
    case 'config':
        switch ($task) {
            case 'deletefield':
                showconfig($option);
                break;
            case "savesettings":
                saveConfig($option);
                break;
            default:
                showconfig($option);
                break;
        }
        break;

    case 'about':
        HTML_BrightReporter::showAbout($option);
        break;

    case 'reports':
        switch ($task) {
            case 'publish':
                changePublish($cid, 1, $option, $act);
                break;
            case 'unpublish':
                changePublish($cid, 0, $option, $act);
                break;
            case 'deletefield':
                $cid = mosGetParam($_REQUEST, 'cid', false);
                editreport($cid, $option, $act, 1, 1);
                break;
            case 'showlist':
                showlist($option, $act);
                break;
            case 'showexplain':
                showreport($option, $act, 4);
                break;
            case 'saverun':
                savereport($option, $act, 2);
                break;
            case 'save':
                savereport($option, $act, 0);
                break;
            case 'apply':
                savereport($option, $act, 1);
                break;
            case 'edit':
                editreport($cid, $option, $act, $what);
                break;
            case 'editfield':
                editreport($cid, $option, $act, 1);
                break;
            case 'new':
                addreport($option, $act);
                break;
            case 'block':
                changeReportBlock($cid, 1, $option, $act);
                break;
            case 'unblock':
                changeReportBlock($cid, 0, $option, $act);
                break;
            case 'remove':
                deleteReport($cid, $option, $act);
                break;
            case 'showreport':
                showreport($option, $act, 0);
                break;
            case 'PrepReport':
                PrepReport($cid, $option, $act);
                break;
            case 'showexcell':
                showreport($option, $act, 1);
                break;
            case 'google_sheets':
                showreport($option, $act, 2);
                break;
            case 'show_explain':
                show_explain($option);
                break;
            default:
                showlist($option, $act);
                break;
        }
        break;
}

function changePublish($cid = null, $publish = 1, $option, $act) {
    global $database;

    $action = $publish ? '1' : '0';

    if (count($cid) < 1) {
        echo "<script type=\"text/javascript\"> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    mosArrayToInts($cid);

    $query = "UPDATE `jos_jeporter` SET `published`='" . $action . "'
    WHERE `id` IN (" . implode(',', $cid) . ")";

    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    mosRedirect('index2.php?option=' . $option . '&act=' . $act);
}

function showconfig($option) {
    HTML_BrightReporter::displayConfigForm($option);
}

/**
 * Saves the record from an edit form submit
 * @param string The current GET/POST option
 */
function saveConfig($option) {

    $configfile = "components/" . $option . "/config.brightreporter.php";
    @chmod($configfile, 0766);
    $permission = is_writable($configfile);
    if (!$permission) {
        $mosmsg = "Config file not writeable!";
        mosRedirect("index2.php?option=$option&act=config", $mosmsg);
        //break;
    }

    $user = trim(mosGetParam($_POST, 'user', ''));
    $excelorlist = trim(mosGetParam($_POST, 'excelorlist', ''));

    $config = "<?php\n";
    $config .= "// no direct access to file";
    $config .= "defined( '_VALID_MOS' ) or die( 'Restricted access' );\n";
    $config .= "\$user = \"" . $user . "\";\n";
    $config .= "\$excelorlist = \"" . $excelorlist . "\";\n";
    $config .= "?>";

    if ($fp = fopen("$configfile", "w")) {
        fputs($fp, $config, strlen($config));
        fclose($fp);
    }
    mosRedirect("index2.php?option=$option&act=config", "Settings saved");
}

function showlist($option, $act) {
    global $database, $mainframe, $mosConfig_list_limit, $my;
    $search = trim(mosGetParam($_POST, "search"));

    $query = "SELECT 
        `area_name` 
    FROM `tbl_new_user_group` AS NUG, 
        `tbl_mix_user_group` AS MUG 
    WHERE NUG.id = MUG.user_group_id AND MUG.user_id =" . (int) $my->id . "";

    $database->setQuery($query);
    $my_info = false;
    $database->loadObject($my_info);

    $my_info_areas = explode('[--1--]', $my_info->area_name);

    $full_access = false;
    if (in_array('full_menus', $my_info_areas) OR in_array('view_reports', $my_info_areas)) {
        $level = 1;

        if (in_array('full_menus', $my_info_areas)) {
            $full_access = true;
        }
    } elseif (in_array('view_reports_2', $my_info_areas)) {
        $level = 2;
    } elseif (in_array('view_reports_3', $my_info_areas)) {
        $level = 3;
    } elseif (in_array('view_reports_4', $my_info_areas)) {
        $level = 4;
    } elseif (in_array('view_reports_5', $my_info_areas)) {
        $level = 5;
    } else {
        $level = false;
    }
    if (isset($_GET['cid']) && !empty($_GET['cid'])) {
        $cid = $_GET['cid'];

        $cids = 'id=' . $cid;
        $jids = 'jeportid=' . $cid;

        if ($search) {
            $cids .= " AND ( LOWER( title ) LIKE '%$search%' OR LOWER( memo ) LIKE '%$search%' )";
        }

        $sql = "SELECT * FROM #__jeporter WHERE ( $cids )";
        $database->setQuery($sql);
        $report = NULL;
        $database->loadObject($report);

        $cid = $report->id;
        $title = $report->title;
        $jquery = $report->jquery;

        $query = "SELECT * FROM #__jeporter_fields WHERE ( $jids )";
        $database->setQuery($query);
        $rows = $database->loadObjectList();

        HTML_BrightReporter::PrepReport($cid, $option, $act, $title, $jquery, $rows);
    } else {

        if ($level !== false) {

            $aWhere = array();
            $where = '';
            $inner = '';

            $inner = "INNER JOIN `jos_jeporter_level_xref` AS `x` ON `x`.`id_jeporter`=`r`.`id` AND `x`.`level`='" . $level . "'";

            if ($full_access != true) {
                $aWhere[] = "`r`.`published`='1' ";
            }
            if ($search) {
                $aWhere[] = " ( LOWER( r.title ) LIKE '%$search%' OR LOWER( r.memo ) LIKE '%$search%' )";
            }

            if (count($aWhere))
                $where = " WHERE " . implode(" AND ", $aWhere);

            $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 100));
            $limitstart = intval($mainframe->getUserStateFromRequest("viewpl{$option}limitstart", 'limitstart', 0));

            $query = "SELECT count(DISTINCT r.id)
            FROM `jos_jeporter` AS `r`
            " . $inner . "
            " . $where . "
             ";
            $database->setQuery($query);
            $total = $database->loadResult();

            require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
            $pageNav = new mosPageNav($total, $limitstart, $limit);

            $query = "SELECT * 
            FROM `jos_jeporter` AS `r`
            " . $inner . "
            " . $where . "
             GROUP BY r.id
            ";

            $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
            $rows = $database->loadObjectList();

            HTML_BrightReporter::displayReports($rows, $pageNav, $option, $act,$search);
        }
    }
}

function PrepReport($cid, $option, $act) {
    global $database;

    mosArrayToInts($cid);
    $cids = 'id=' . implode(' OR id=', $cid);
    $jids = 'jeportid=' . implode(' OR jeportid=', $cid);

    $sql = "SELECT * FROM #__jeporter WHERE ( $cids )";
    $database->setQuery($sql);
    $report = NULL;
    $database->loadObject($report);

    $cid = $report->id;
    $title = $report->title;
    $jquery = $report->jquery;

    $query = "SELECT * FROM #__jeporter_fields WHERE ( $jids )";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    HTML_BrightReporter::PrepReport($cid, $option, $act, $title, $jquery, $rows);
}

function changeReportBlock($cid = null, $block = 1, $option, $act) {
    global $database;

    $action = $block ? 'block' : 'unblock';

    if (count($cid) < 1) {
        echo "<script type=\"text/javascript\"> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    mosArrayToInts($cid);
    $cids = 'id=' . implode(' OR id=', $cid);

    $query = "UPDATE #__jeporter"
            . "\n SET block = " . (int) $block
            . "\n WHERE ( $cids )"
    ;
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    mosRedirect('index2.php?option=' . $option . '&act=' . $act);
}

function addreport($option, $act) {
    global $database;

    $cid = 0;
    $title = '';
    $jquery = '';
    $memo = '';

    $tablelist = $database->getTableList();
    $variables = 0;
    $fieldid = 0;

    HTML_BrightReporter::addEditReport($option, $cid, $title, $jquery, $act, $tablelist, $variables, $fieldid, $memo);
}

function editreport($cid, $option, $act, $what = 0, $delete = 0) {
    global $database;

    if (!$cid) {
        $cid = mosGetParam($_REQUEST, 'cid', false);
    }

    if (0 == $what) {
        mosArrayToInts($cid);
        $cids = 'id=' . implode(' OR id=', $cid);
        $jids = 'jeportid=' . implode(' OR jeportid=', $cid);
    } else {
        $cids = 'id=' . $cid;
        $jids = 'jeportid=' . $cid;
    }

    if ($what != 3) {
        $fieldid = mosGetParam($_REQUEST, 'fieldid', false);
    }

    if (1 == $delete) {
        $sql = "DELETE FROM #__jeporter_fields WHERE id = " . $fieldid;
        $database->setQuery($sql);

        if (!$database->query()) {
            echo $database->stderr();
            return false;
        }
        $fieldid = 0;
    }

    $sql = "SELECT * FROM #__jeporter WHERE ( $cids )";
    $database->setQuery($sql);
    $report = NULL;
    $database->loadObject($report);

    $cid = $report->id;
    $title = $report->title;
    $jquery = $report->jquery;
    $memo = $report->memo;
    $levels = array();

    $query = "SELECT * FROM `jos_jeporter_level_xref` AS `x`
    WHERE `x`.`id_jeporter`=" . $report->id . "";
    $database->setQuery($query);
    $levels_obj = $database->loadObjectList();

    foreach ($levels_obj as $level_obj) {
        $levels[] = $level_obj->level;
    }
    $tablelist = $database->getTableList();

    $query = "SELECT * FROM #__jeporter_fields WHERE ( $jids ) ";
    $database->setQuery($query);
    $variables = $database->loadObjectList();

    HTML_BrightReporter::addEditReport($option, $cid, $title, SqlFormatter::format($jquery, false), $act, $tablelist, $variables, $fieldid, $memo, $levels);
}

function savereport($option, $act, $what = 0) {
    global $database;
    $cid = mosGetParam($_REQUEST, 'id', false);
    $title = mosGetParam($_REQUEST, 'title', false);
    $jquery = mosGetParam($_REQUEST, 'jquery', false, _MOS_ALLOWRAW);
    $memo = mosGetParam($_REQUEST, 'memo', false);

    $fieldid = mosGetParam($_REQUEST, 'fieldid', false);
    $fieldkindid = mosGetParam($_REQUEST, 'fieldkindid', false);
    $fieldname = mosGetParam($_REQUEST, 'fieldname', false);
    $fieldcode = mosGetParam($_REQUEST, 'fieldcode', false, _MOS_ALLOWRAW);

    $msg = "";

    $pattern = '/\binsert \b|\bupdate \b|\bdrop \b|\bdelete \b|\balter \b|\breplace \b|\btruncate \b/i';
    if (preg_match($pattern, $jquery)) {
        echo "<script type=\"text/javascript\"> alert('Database alteration operations are not allowed in reports. Please contact System administrator'); window.history.go(-1);</script>\n";
        exit;
    }

    $ewhat = 1;

    if ($fieldname) {
        $fieldname = str_replace(" ", "_", $fieldname);
        if (!$fieldid) {
            $sql = "INSERT INTO #__jeporter_fields (`jeportid`,`fieldkindid`,`fieldname`, `fieldcode`) VALUES ( " . $cid . ", " . $fieldkindid . ", '" . $fieldname . "', '" . $fieldcode . "')";
            $msg .= 'Insert Var Field successfully! ';
        } else {
            $sql = "UPDATE #__jeporter_fields SET `fieldkindid` = " . $fieldkindid . ", `fieldname` = '" . $fieldname . "', `fieldcode` = '" . $fieldcode . "' WHERE id = " . $fieldid;
            $msg .= 'Updated Var Field successfully! ';
        }
        $database->setQuery($sql);
        if (!$database->query()) {
            echo $database->stderr();
            return false;
        }
        $ewhat = 3;
    }

    if ($cid == 0) {
        $sql = "INSERT INTO #__jeporter (`title`,`jquery`,`createdon`, `block`, `memo`) VALUES ( '" . $title . "', '" . $jquery . "', NOW(),0, '" . $memo . "')";
        $msg .= 'Report successfully inserted! ';
    } else {
        $sql = "UPDATE #__jeporter SET `title` = '" . $title . "', `jquery` = '" . $jquery . "', `memo`= '" . $memo . "' WHERE id = " . $cid;
        $msg .= 'Report successfully Updated! ';
    }

    $database->setQuery($sql);
    if (!$database->query()) {
        echo $database->stderr();
        return false;
    }

    if ($cid == 0) {
        $cid = $database->insertid();
    }
    $query = "DELETE FROM `jos_jeporter_level_xref` WHERE `id_jeporter`=" . $cid . "";
    $database->setQuery($query);
    $database->query();

    if (sizeof($_POST['levels']) > 0) {
        $inserts = array();

        foreach ($_POST['levels'] as $level) {
            $inserts[] = "(" . $cid . ", " . (int) $level . ")";
        }

        if (sizeof($inserts) > 0) {
            $query = "INSERT INTO `jos_jeporter_level_xref`
            (
                `id_jeporter`,
                `level`
            )
            VALUES " . implode(',', $inserts) . "";

            $database->setQuery($query);
            $database->query();
        }
    }
    if (0 == $what) {
        mosRedirect('index2.php?option=' . $option . '&act=' . $act, $msg);
    } elseif (1 == $what) {
        editreport($cid, $option, $act, $ewhat);
    } elseif (2 == $what) {
        mosRedirect('index2.php?option=' . $option . '&act=reports&cid=' . $cid);
    }
}

function deleteReport($cid = NULL, $option, $act) {
    global $database;
    if (count($cid) < 1) {
        echo "<script type=\"text/javascript\"> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    mosArrayToInts($cid);
    $cids = 'id=' . implode(' OR id=', $cid);
    $jids = 'jeportid=' . implode(' OR jeportid=', $cid);

    $sql = "DELETE FROM #__jeporter WHERE ( $cids )";
    $database->setQuery($sql);

    if (!$database->query()) {
        echo $database->stderr();
        return false;
    }

    $sql = "DELETE FROM #__jeporter_fields WHERE ( $jids )";
    $database->setQuery($sql);

    if (!$database->query()) {
        echo $database->stderr();
        return false;
    }

    $msg = 'Report successfully Deleted!';

    mosRedirect('index2.php?option=' . $option . '&act=' . $act, $msg);
}

function showreport($option, $act, $excel = 0) {
    global $database, $my;

    $cid = mosGetParam($_REQUEST, 'cid', false);

    if (is_array($cid)) {
        $cid = (int) $cid[0];
    }

    $sql = "SELECT * FROM #__jeporter WHERE id= " . $cid;
    $database->setQuery($sql);
    $report = NULL;
    $database->loadObject($report);

    $cid = $report->id;
    $title = $report->title;
    $jquery = $report->jquery;
    
    if ($excel == 4) {
        $jquery = 'EXPLAIN '.$jquery;
    }

    $sql = "SELECT * FROM #__jeporter_fields WHERE jeportid= " . $cid;
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    $i = 0;
    $variables='';
    foreach ($rows as $row) {
        $raw = mosGetParam($_REQUEST, $row->fieldname, false);

        if (is_array($raw)) {
            $fieldName = implode("','", $raw);
        } else {
            $fieldName = trim((string)$raw);
        }
        $variables.=$row->fieldname."\t".$fieldName."\t";
        $jquery = str_replace('${' . $row->fieldname . '}', $fieldName, $jquery);

        $i++;
    }

    $query_storage = "INSERT INTO `tbl_queries_storage`
    (
        `user_id`,
        `user_email`,
        `type`,
        `query`,
        `datetime`
    )
    VALUES (
        " . (int) $my->id . ",
        '" . $database->getEscaped($my->username) . "',
        'report',
        '" . $database->getEscaped($jquery) . "',
        '" . date('Y-m-d G:i:s') . "'
    )";

    $database->setQuery($query_storage);
    $database->query();

    $start = microtime(true);

    $database->setQuery($jquery);
    if (!$database->query()) {
        echo $database->getErrorMsg();
        echo "error";
        exit(0);
    }
    $rows = $database->loadAssocList();

    if ($excel == 0) {
        $time_diff_secs = microtime(true) - $start;
        HTML_BrightReporter::showReport($jquery, $rows, $title, $option, $act, $cid, $time_diff_secs);
    } elseif ($excel == 1) {
        HTML_BrightReporter::showExcell($rows, $title, $option, $act, $cid,$variables);
    } elseif ($excel == 4) {
        HTML_BrightReporter::showReport($jquery, $rows, $title, $option, $act, $cid);
    } elseif ($excel == 2) {
        ?>
        <style>
            .se-pre-con {
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: url(/images/Preloader_8.gif) center no-repeat #fff;
            }
        </style>
        <div class="se-pre-con" id="report_loader"></div>
        <script type="text/javascript">
            window.onload = function () {
                document.getElementById('report_loader').style.display = 'none';
            }
        </script>
        <?php

        HTML_BrightReporter::showGoogleSheets($jquery, $rows, $title, $option, $act, $cid);
    }
}
function show_explain($option){
    global $database;
    $query = $_REQUEST['query']?$_REQUEST['query']:'';
    $return['result']=false;
    if($query){
        $database->setQuery('EXPLAIN '.$query);
        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "error";
            exit(0);
        }
        $row_explain = $database->loadAssocList();
        $return['result']=true;
        $return['res']=HTML_BrightReporter::parse_rows($row_explain);
    }
    die(json_encode($return));

}
?>
