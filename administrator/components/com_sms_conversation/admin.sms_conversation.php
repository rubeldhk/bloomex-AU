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
date_default_timezone_set('Australia/Sydney');
require_once( $mainframe->getPath('admin_html') );

$sectionid = intval(mosGetParam($_REQUEST, 'sectionid', 0));

$cid = josGetArrayInts('cid');

switch ($task) {
    case 'new':
        editsmstext(0,  $option);
        break;

    case 'edit':
        editsmstext($id,  $option);
        break;

    case 'editA':
        editsmstext(intval($cid[0]),  $option);
        break;

    case 'save':
        save_sms( $task,$sent);
        break;

    case 'remove':
        remove_sms($cid, $option);
        break;

    case 'cancel':
        cancel_sms();
        break;

    default:

        view_sms($sectionid,$option);
        break;
}



function view_sms($sectionid,$option) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0));
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = array();
    if ($search) {
        $where[] = "LOWER( s.title ) LIKE '%$search%' OR  s.number  LIKE '%$search%'";
    }

    $query = "SELECT COUNT(*)"
            . "\n FROM (SELECT * FROM sms_conversation"
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : "" )."  group by number ) as s"
    ;
    $database->setQuery($query);
    $total = $database->loadResult();
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);


    $query = "SELECT s.*, h.messageID,h.datetime as action_date,h.direction as last_action_type,h.text as last_message 
                FROM sms_conversation as s 
                left join (SELECT m1.*
                FROM jos_sms_history m1 LEFT JOIN jos_sms_history m2
                 ON (m1.phone = m2.phone AND m1.messageID < m2.messageID)
                WHERE m2.messageID IS NULL) as h on h.phone=s.number "
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' ). " group by s.number order by h.messageID desc"
    ;

    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadAssocList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }


    HTML_sms_conversation::showsms_texts($rows, $pageNav,$search);
}




function editsmstext($uid = 0,  $option) {
    global $database,  $mainframe,$my;

     if(isset($_GET['number'])) {
         $number = urlencode($_GET['number']);
         $number = str_replace("+", "%2B",$number);
         $number = trim($database->getEscaped(urldecode($number)));
    }else{
        $number='';
    }

    $query = "SELECT * "
            . "\n FROM sms_conversation"
            . " WHERE id='".$uid."' OR number='".$number."'";

    $database->setQuery($query);
    $row = $database->loadObjectList();

    $number = isset($row[0]->number)?$row[0]->number:$number;
    $query_numbers = "SELECT messageID "
            . "\n FROM jos_sms_history"
            . " WHERE phone='".$database->getEscaped($number)."' ORDER BY messageID ASC";

    $database->setQuery($query_numbers);
    $row_numbers = $database->loadObjectList();
    $list = '';
    if($row_numbers){
        $numItems = count($row_numbers);
          $i = 0;
            foreach($row_numbers as $r){
                if(++$i === $numItems) {
                    $list .= $r->messageID;
                  }else{
                    $list .= $r->messageID.",";
                  }
            }
    }

    $query = "UPDATE `sms_conversation`
        SET  
            `last_modified`='" . time() . "' 
        WHERE 
            `number`='" . $database->getEscaped($number) . "'
        ";
    $database->setQuery($query);
    $database->query();


    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }




    HTML_sms_conversation::editsmstext($row[0], $option,$list,$my->username,$number);
}




function removeslashes($string)
{
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}

function remove_sms(&$cid,  $option) {
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }
    $cids = implode(',', $cid);

    $query_select = "SELECT number  FROM sms_conversation"
            . "\n WHERE id IN ( $cids )"
    ;
    $database->setQuery($query_select);
  $sms_id = $database->loadObjectList();
 if($sms_id){
  foreach($sms_id as $id){

     $query_numbers = "DELETE FROM jos_sms_history"
            . "\n WHERE phone  = '". $id->number."'"
    ;
    $database->setQuery($query_numbers);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }
  }
 }
    $query = "DELETE FROM sms_conversation"
            . "\n WHERE id IN ( $cids )"
    ;
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    
    
    $msg = $total . " Item(s) Deleted";
    mosRedirect('index2.php?option=' . $option , $msg);
}

function cancel_sms() {
    
    
    mosRedirect('index2.php?option=com_sms_conversation');
}

?>