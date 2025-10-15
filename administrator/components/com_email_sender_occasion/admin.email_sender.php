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
        editemailtext(0,  $option);
        break;

    case 'edit':
        editemailtext($id,  $option);
        break;

    case 'editA':
        editemailtext(intval($cid[0]),  $option);
        break;

    case 'save':
        save_email( $task,$sent);
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

        view_email($sectionid,$option);
        break;
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
                   . "\n SET `sent`=$state WHERE id IN ( $cids )";
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


    mosRedirect('index2.php?option=' . $option .  '&mosmsg=' . $msg);
}


function view_email($sectionid,$option) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0));
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = [];
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

    
    
    $query = "SELECT * "
            . "\n FROM tbl_email_text"
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
            . " ORDER BY date DESC"

    ;
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();
    
    foreach($rows as $row){
    $query_total = "SELECT count(email) as total"
            . "\n FROM tbl_emails_for_sending"
            . " WHERE text_id=".$row->id;

    $database->setQuery($query_total);
    $emails_total = $database->loadObjectList();
    
    $query_sents = "SELECT count(email) as sents"
            . "\n FROM tbl_emails_for_sending"
            . " WHERE `sent`= '1' AND text_id=".$row->id;

    $database->setQuery($query_sents);
    $emails_sents = $database->loadObjectList();

    $row->sentvstotal = $emails_sents[0]->sents." / ".$emails_total[0]->total;    

    }
    
    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_email_sender::showemail_texts($rows, $pageNav,$search);
}
function list_emails($row_emails){
    $html = "<table border='1'><tr>"
            . "<th>Email</th>"
            . "<th>Date For Email</th>"
            . "<th>Date Sent Email</th>";
    foreach($row_emails as $em){
        if($em->sent)
            $sent = "Sent";
        else
            $sent = 'have not  Sent yet';
        if($em->datesend != 0)
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
function editemailtext($uid = 0,  $option) {
    global $database, $my, $mainframe;
    global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;

    $nullDate = $database->getNullDate();
    
    $query = "SELECT * "
            . "\n FROM tbl_email_text"
            . " WHERE id=".$uid;

    $database->setQuery($query);
    $row = $database->loadObjectList();
    
    $query_emails = "SELECT email,sent,date,datesend "
            . "\n FROM tbl_emails_for_sending"
            . " WHERE text_id='".$uid."' ORDER BY email";

    $database->setQuery($query_emails);
    $row_emails = $database->loadObjectList();
    $list = list_emails($row_emails);
    
    
    
    $to_string ='';
    if(isset($row_emails)&& !empty($row_emails)){
        $to_array = array();
        foreach($row_emails as $email){
            $to_array[] = $email->email;
        }
        $to_string = implode(",", $to_array);
        
    }
    $row[0]->to = $to_string;

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }


    $params = new mosParameters('', $mainframe->getPath('com_xml', 'com_email_sender'), 'component');

    HTML_email_sender::editemailtext($row[0],$params,  $option,$list);
}
function removeslashes($string)
{
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}
function save_email( $task) {  
    global $database, $my, $mainframe, $mosConfig_offset;
    $id = intval(mosGetParam($_REQUEST, "id", 0));
    $subject = mosGetParam($_REQUEST, "subject", "");
    $html = $_REQUEST['introtext']?$_REQUEST['introtext']:'';
    $date_send = mosGetParam($_REQUEST, "date_send", '');
    $publish  = intval(mosGetParam($_REQUEST, "publish", 0));
    if(!isset($publish)){
        $publish = 0;
    }

   $text_id =  $id;
     $query = "SELECT id"
            . "\n FROM `tbl_email_text`"
            . "\n WHERE id = ".$id
    ;
    $database->setQuery($query);
    $email_id = $database->loadObjectList();
    if($email_id){
       
            $query = "UPDATE `tbl_email_text`"
                   . "\n SET `sent`='$publish',`text`='$html',`date`='$date_send',`subject`='$subject' WHERE `id`=".$id;
           $database->setQuery($query);
            if (!$database->query()) {
               echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
               exit();
           }
            $text_id =  $id;  
           
    }else{

            $query = "INSERT INTO `tbl_email_text` ( `text`,`date`,`subject`,`sent`) "
                            . "\n VALUES ( '$html','$date_send','$subject','$publish')"
                    ;
     
                    $database->setQuery($query);
                    if (!$database->query()) {
                        echo "<script> alert('" . $database->stderr() . "');</script>\n";
                        exit();
                    }
   $text_id =  $database->insertid();
    }
    
    $list_email_to = mosGetParam($_REQUEST, "to", "");
    $list_email_to = removeslashes ($list_email_to);
    
    $list_email_to_str = $list_email_to;
    $list_email_to = explode(',', $list_email_to);
    $list_email_to = array_map('trim',$list_email_to);
    

    if(end($list_email_to) == ''){
        array_pop($list_email_to);
    }
    $list_email_to = array_unique($list_email_to);

                             $query = "SELECT email"
                             . "\n FROM `tbl_emails_for_sending`"
                             . "\n WHERE `text_id` = '$text_id'" ;
                     $database->setQuery($query);
                     $email_check = $database->loadResultArray();
                     if($email_check){
                         $array_diff = array_diff($email_check,$list_email_to);
                         if(!empty($array_diff)){
                             foreach($array_diff as $diff){
                                 $query_emails = "DELETE FROM tbl_emails_for_sending"
                                        . "\n WHERE email='$diff' AND text_id IN ( $text_id )"
                                ;
                                $database->setQuery($query_emails);
                                if (!$database->query()) {
                                    echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                                    exit();
                                }
                             }
                         }
                     }
                         
                     

     foreach($list_email_to as $key => $email){   
           $query = "SELECT id"
                             . "\n FROM `tbl_emails_for_sending`"
                             . "\n WHERE `text_id` = '$text_id' AND `email`='$email'" ;
                     $database->setQuery($query);

                     $email_check = $database->loadObjectList();
                     if($email_check){

                                $query_email = "UPDATE `tbl_emails_for_sending`"
                                       . "\n SET `date`='$date_send' WHERE `text_id`='$text_id' AND `email`='$email'";

                               $database->setQuery($query_email);
                                if (!$database->query()) {
                                   echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                                   exit();
                               }
                     }else{
                         
                                    $query_email = "INSERT INTO `tbl_emails_for_sending` ( `date`,`email`,`sent`,`text_id`) "
                                                    . "\n VALUES ( '$date_send','$email','0','$text_id')"
                                            ;
                                            $database->setQuery($query_email);
                                            if (!$database->query()) {
                                                echo "<script> alert('" . $database->stderr() . "');</script>\n";
                                                exit();
                                            }                         
                         
                     }

     }

 if($publish == 1){
     $today = date("Y-m-d");
     if(date("Y-m-d", strtotime($date_send)) == $today){
        $msg = ' Email Template Successfully Saved and will be sent to orders today';
     }else{
        $msg = ' Email Template Successfully Saved and will be sent to orders at : '.$date_send;
     }
 }else{
    $msg = 'Email Template  Successfully Saved';
 }   
  mosRedirect('index2.php?option=com_email_sender', $msg);

}

function remove_email(&$cid,  $option) {
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
    mosRedirect('index2.php?option=' . $option , $msg);
}

function cancel_email() {
    
    
    mosRedirect('index2.php?option=com_email_sender');
}


?>