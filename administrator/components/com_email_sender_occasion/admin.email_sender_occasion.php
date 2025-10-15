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
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php" );
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
function changestatus($cid = null, $published = 0, $option) {
    global $database, $my;
    if (count($cid) < 1) {
        $action = $published == 1 ? 'publish' : ($published == 0 ? 'archive' : 'unpublish');
        echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $total = count($cid);
    $cids = implode(',', $cid);

 
            $query = "UPDATE `tbl_occasion_email`"
                   . "\n SET `published`=$published WHERE id IN ( $cids )";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    switch ($published) {
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
        $where[] = "LOWER( subject ) LIKE '%$search%' OR LOWER( occasion_name ) LIKE '%$search%'";
    }
    $query = "SELECT COUNT(*)"
            . "\n FROM tbl_occasion_email"
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : "" )
    ;
    $database->setQuery($query);
    $total = $database->loadResult();
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    
    
    $query = "SELECT em.*,oc.order_occasion_name as occasion_name"
            . "\n FROM tbl_occasion_email as em LEFT JOIN #__vm_order_occasion as oc ON oc.order_occasion_code = em.occasion"
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' );

    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();
    
    
    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }



    
    
    HTML_occasion_email_sender::showemail_texts($rows, $pageNav,$search);
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
            . "\n FROM tbl_occasion_email"
            . " WHERE id=".$uid;

    $database->setQuery($query);
    $row = $database->loadObjectList();


    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }


    $params = new mosParameters('', $mainframe->getPath('com_xml', 'com_email_sender_occasion'), 'component');
$query = "SELECT order_occasion_code, order_occasion_name FROM #__vm_order_occasion ORDER BY list_order ASC";
    $database->setQuery($query);
    $occasions = $database->loadObjectList();



    
    HTML_occasion_email_sender::editemailtext($row[0],$params,  $option,$occasions);
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
    $first_price = intval(mosGetParam($_REQUEST, "first_price", 0));
    $last_price = intval(mosGetParam($_REQUEST, "last_price", 0));
    $day_count = intval(mosGetParam($_REQUEST, "day_count", 0));
    $publish  = intval(mosGetParam($_REQUEST, "published", 0));
    $occasion  = mosGetParam($_REQUEST, "occasion", '');
    if(!isset($publish)){
        $publish = 0;
    }
    
   $text_id =  $id;
     $query = "SELECT id"
            . "\n FROM `tbl_occasion_email`"
            . "\n WHERE id = ".$id
    ;
    $database->setQuery($query);
    $email_id = $database->loadObjectList();
    $html = $database->getEscaped($html);   
    if($email_id){
       
            $query = "UPDATE `tbl_occasion_email`"
                   . "\n SET `published`='$publish',`text`='$html',`subject`='$subject',`first_price`='$first_price',`day_count`='$day_count',`last_price`='$last_price',`occasion`='$occasion'  WHERE `id`=".$id;
            $database->setQuery($query);
            if (!$database->query()) {
               echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
               exit();
           }
            $text_id =  $id;  
           
    }else{

            $query = "INSERT INTO `tbl_occasion_email` ( `text`,`first_price`,`subject`,`day_count`,`last_price`,`occasion`,`published`) "
                            . "\n VALUES ( '$html','$first_price','$subject','$day_count','$last_price','$occasion','$publish')"
                    ;
     
                    $database->setQuery($query);
                    if (!$database->query()) {
                        echo "<script> alert('" . $database->stderr() . "');</script>\n";
                        exit();
                    }
   $text_id =  $database->insertid();
    }
    

    $msg = 'Email Template  Successfully Saved';

  mosRedirect('index2.php?option=com_email_sender_occasion', $msg);

}

function remove_email(&$cid,  $option) {
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }



    $cids = implode(',', $cid);
    $query = "DELETE FROM tbl_occasion_email"
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

function cancel_email() {
    
    
    mosRedirect('index2.php?option=com_email_sender_occasion');
}


?>