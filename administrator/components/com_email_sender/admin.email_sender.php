<?php

/**
 * @version $Id: admin.content.php 4672 2006-08-23 15:14:19Z stingrey $
 * @package Joomla
 * @subpackage Content
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

$sectionid = intval(mosGetParam($_REQUEST, 'sectionid', 0));

$cid = josGetArrayInts('cid');

switch ($task) {
    case 'new':
        editemailtext(0, $option);
        break;

    case 'edit':
        editemailtext($id, $option);
        break;

    case 'editA':
        editemailtext(intval($cid[0]), $option);
        break;

    case 'save':
        save_email($task);
        break;
    case 'updateEmail':
        updateEmail();
        break;
    case 'addOrderId':
        addOrderId();
        break;

    case 'publish':
        changestatus($cid, 1, $option);
        break;

    case 'unpublish':
        changestatus($cid, 0, $option);
        break;
    case 'remove':
        remove_email($cid, $option);
        break;

    case 'cancel':
        cancel_email();
        break;

    default:

        view_email($sectionid, $option);
        break;
}

function updateEmail() {
    global $database, $option, $my;
    $id = mosGetParam($_POST, "id", 0);
    $email = $database->getEscaped(mosGetParam($_POST, "email", ''));

    if (!$id) {
        die('<span class="text-error">Id is not exist.</span>');
    }

    $query = "update tbl_emails_for_sending set email='" . $database->getEscaped($email) . "' where id=" . $id;
    $database->setQuery($query);
    $database->query();
    die('<span class="text-success">success</span>');
}

function addOrderId() {
    global $database;
    $order_id = mosGetParam($_POST, "order_id", 0);
    $res['result'] = false;
    if (!$order_id) {
        $res['msg'] = '<span class="text-danger">Id is not exist.</span>';
        die(json_encode($res));
    }

    $sql = "SELECT `order_id`,`user_email` FROM `jos_vm_order_user_info` WHERE `address_type`='BT' and user_email!='' and order_id =" . $database->getEscaped($order_id);
    $database->setQuery($sql);
    $orderInfo = false;
    $database->loadObject($orderInfo);
    if (!$orderInfo) {
        $res['msg'] = '<span class="text-danger">Order Id is not exist.</span>';
        die(json_encode($res));
    }

    $orderInfo->emailValid = true;
    if (!filter_var($orderInfo->user_email, FILTER_VALIDATE_EMAIL)) {
        $orderInfo->emailValid = false;
    }
    $res['result'] = true;
    $res['obj'] = $orderInfo;
    die(json_encode($res));
}

function changestatus($cid = null, $state = 0, $option) {
    global $database, $my;
    if (count($cid) < 1) {
        $action = $state == 1 ? 'publish' : ($state == -1 ? 'archive' : 'unpublish');
        echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $total = count($cid);
    $cids = implode(',', $cid);


    $query = "UPDATE `tbl_email_text`"
            . "\n SET `publish`=$state WHERE id IN ( $cids )";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    switch ($state) {
        case 1:
            $msg = $total . ' Item(s) successfully Published';
            break;

        default:
            $msg = $total . ' Item(s) successfully UnPublished';
            break;
    }


    mosRedirect('index2.php?option=' . $option . '&mosmsg=' . $msg);
}

function view_email($sectionid, $option) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0));
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    $where[] = "1=1";

    if ($search) {
        $where[] = "LOWER( subject ) LIKE '%$search%'";
    }

    $query = "SELECT COUNT(*)"
            . "\n FROM tbl_email_text"
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : "" )
    ;
    $database->setQuery($query);
    $total = $database->loadResult();
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);



    $query = "SELECT * ,t.publish as 'publish' "
            . "\n FROM tbl_email_text t left join jos_vm_order_status os on t.order_status_code=os.order_status_code"
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
            . " ORDER BY date DESC"

    ;
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    foreach ($rows as $row) {
        $query_total = "SELECT count(*) as total"
                . "\n FROM tbl_emails_for_sending"
                . " WHERE text_id=" . $row->id;

        $database->setQuery($query_total);
        $emails_total = $database->loadObjectList();

        $query_sents = "SELECT count(*) as sents"
                . "\n FROM tbl_emails_for_sending"
                . " WHERE `sent_datetime` > 0 AND text_id=" . $row->id;

        $database->setQuery($query_sents);
        $emails_sents = $database->loadObjectList();

        $row->sentvstotal = $emails_sents[0]->sents . " / " . $emails_total[0]->total;
    }

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_email_sender::showemail_texts($rows, $pageNav, $search);
}

function list_emails($row_emails) {
    $html = "<table border='1'><tr>"
            . "<th>Email</th>"
            . "<th>Date For Email</th>"
            . "<th>Date Sent Email</th>";
    foreach ($row_emails as $em) {
        if ($em->sent)
            $sent = "Sent";
        else
            $sent = 'have not  Sent yet';
        if ($em->datesend != 0)
            $datesend = $em->datesend;
        else
            $datesend = '';
        $html .= "<tr>";
        $html .= "<td>$em->email</td>";
        $html .= "<td>$em->date</td>";
        $html .= "<td>$datesend</td>";
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

function editemailtext($uid = 0, $option) {
    global $database, $my, $mainframe;
    global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;

    $nullDate = $database->getNullDate();

    $query = "SELECT * "
            . " FROM tbl_email_text"
            . " WHERE id=" . $uid;

    $database->setQuery($query);
    $row = $database->loadObjectList();

    $query_emails = "SELECT user_email as 'email',s.order_id,sent_datetime,send_status,s.id  "
            . " FROM tbl_emails_for_sending s "
            . " INNER JOIN jos_vm_order_user_info ui on ui.order_id = s.order_id and ui.address_type='BT' "
            . " WHERE text_id='" . $uid . "' ORDER BY s.order_id";

    $database->setQuery($query_emails);
    $emailsList = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_email_sender::editemailtext($row[0], $option, $emailsList);
}

function save_email($task) {
    global $database, $my, $mainframe, $mosConfig_offset;
    $id = intval(mosGetParam($_REQUEST, "id", 0));
    $title = mosGetParam($_REQUEST, "title", "");
    $subject = mosGetParam($_REQUEST, "subject", "");
    $html = $_REQUEST['introtext'] ? $_REQUEST['introtext'] : '';
    $date_send = mosGetParam($_REQUEST, "date_send", '');
    $order_status = mosGetParam($_REQUEST, "order_status", '');
    $publish = intval(mosGetParam($_REQUEST, "publish", 0));
    $mark_status = mosGetParam($_REQUEST, "mark_status", '');
    $mark_description = mosGetParam($_REQUEST, "mark_description", '');
    $username = $my->username;
    f($my);
    if (!isset($publish)) {
        $publish = 0;
    }
    $query = "SELECT id"
            . "\n FROM `tbl_email_text`"
            . "\n WHERE id = " . $id;
    $database->setQuery($query);
    $res = $database->loadResult();
    if ($res) {
        $query = "UPDATE `tbl_email_text`"
                . "\n SET `title`= '" . $database->getEscaped($title) . "',`publish`='$publish',`text`='" . $database->getEscaped($html) . "',`order_status_code`='$order_status',`date`='$date_send',`subject`='" . $database->getEscaped($subject) . "',`order_status_code`='$order_status',`mark_status`='$mark_status',`mark_description`='$mark_description' WHERE `id`=" . $id;
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        $text_id = $id;
    } else {

        $query = "INSERT INTO `tbl_email_text` ( `title`,`text`,`date`,`subject`,`publish`,`order_status_code`,`username`,`mark_status`,`mark_description`) "
                . "\n VALUES ( '$title','$html','$date_send','$subject','$publish','$order_status','$username','$mark_status', '$mark_description')"
        ;

        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->stderr() . "');</script>\n";
            exit();
        }
        $text_id = $database->insertid();
    }
    $list_email_to = json_decode($_REQUEST["to"]);
    $list_email_to = array_unique($list_email_to, SORT_REGULAR);
    $dataList = [];
    array_walk(
            $list_email_to,
            function (&$value, $key) use (&$dataList) {
        if (isset($value->id)) {
            $dataList['id_' . $value->id] = $value;
        } else {
            $dataList[] = $value;
        }
    }
    );

    $query = "SELECT id"
            . "\n FROM `tbl_emails_for_sending`"
            . "\n WHERE `text_id` = '$text_id'";
    $database->setQuery($query);
    $res = $database->loadObjectList();
    if ($res) {
        foreach ($res as $r) {
            if ($dataList && $dataList['id_' . $r->id]) {
                $query_email = "UPDATE `tbl_emails_for_sending`"
                        . "\n SET `order_id`='{$database->getEscaped($dataList['id_' . $r->id]->order_id)}' WHERE  `id`='$r->id'";
                $database->setQuery($query_email);
                if (!$database->query()) {
                    echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                    exit();
                }
                unset($dataList['id_' . $r->id]);
            } else {
                $query_emails = "DELETE FROM tbl_emails_for_sending WHERE  id =" . $r->id;
                $database->setQuery($query_emails);
                if (!$database->query()) {
                    echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                    exit();
                }
            }
        }
    }



    foreach ($dataList as $key => $l) {
        $query_email = "INSERT INTO `tbl_emails_for_sending` ( `order_id`,`text_id`) "
                . "\n VALUES ( '$l->order_id','$text_id')";
        $database->setQuery($query_email);
        if (!$database->query()) {
            echo "<script> alert('" . $database->stderr() . "');</script>\n";
            exit();
        }
    }

    if ($publish == 1) {
        $today = date("Y-m-d");
        if (date("Y-m-d", strtotime($date_send)) == $today) {
            $msg = ' Email Template Successfully Saved and will be sent to orders today';
        } else {
            $msg = ' Email Template Successfully Saved and will be sent to orders at : ' . $date_send;
        }
    } else {
        $msg = 'Email Template  Successfully Saved';
    }
    mosRedirect('index2.php?option=com_email_sender', $msg);
}

function remove_email(&$cid, $option) {
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }



    $cids = implode(',', $cid);
    $query = "DELETE FROM tbl_email_text"
            . "\n WHERE id IN ( $cids )"
    ;
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $query_emails = "DELETE FROM tbl_emails_for_sending"
            . "\n WHERE text_id IN ( $cids )"
    ;
    $database->setQuery($query_emails);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }


    $msg = $total . " Item(s) Deleted";
    mosRedirect('index2.php?option=' . $option, $msg);
}

function cancel_email() {


    mosRedirect('index2.php?option=com_email_sender');
}

?>