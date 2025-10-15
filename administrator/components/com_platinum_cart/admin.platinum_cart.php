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
        editplatinum(0,  $option);
        break;

    case 'edit':
        editplatinum($id,  $option);
        break;

    case 'editA':
        editplatinum(intval($cid[0]),  $option);
        break;

    case 'save':
        saveplatinum( $task,$sent);
        break;

    case 'remove':
        remove_platinum($cid, $option);
        break;

    case 'cancel':
        cancel_platinum();
        break;

    default:

        view_platinum_club_list($sectionid,$option);
        break;
}

function view_platinum_club_list($sectionid,$option) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0));
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = [];
    
    if ($search) {
        $where[] = "( LOWER( p.user_id ) LIKE '%$search%' OR LOWER( u.email ) LIKE '%$search%' OR LOWER( u.name ) LIKE '%$search%' )";
    }
    
    $query = "SELECT 
        COUNT(DISTINCT(`p`.`user_id`)) AS `count`
    FROM `tbl_platinum_club` as `p` 
    LEFT JOIN `jos_users` as `u` on `u`.`id`=`p`.`user_id`
    ".(count($where) ? " WHERE ".implode(' AND ', $where) : '')."
    GROUP BY `p`.`user_id` ORDER BY `p`.`start_datetime` DESC
    ";
    
    $row = false;
    $database->setQuery($query);
    $database->loadObject($row);
    

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav((isset($row->count) ? $row->count : 0), $limitstart, $limit);
    
    $query = "SELECT p.*,u.name,u.email "
            . "\n FROM tbl_platinum_club as p LEFT JOIN jos_users as u on u.id=p.user_id "
            . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
            . " GROUP BY p.user_id ORDER BY p.start_datetime DESC"

    ;
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();
    

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_platinum_cart::show_platinum_club_list($rows, $pageNav,$search);
}

function editplatinum($uid = 0,  $option) {
    global $database, $my, $mainframe;
    global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;

    $nullDate = $database->getNullDate();
    
    $query = "SELECT * "
            . "\n FROM tbl_platinum_club"
            . " WHERE id=".$uid;

    $database->setQuery($query);
    $row = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }
    HTML_platinum_cart::editplatinum($row[0], $option);
}
function removeslashes($string)
{
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}
function saveplatinum( $task) {  
    global $database, $my, $mainframe, $mosConfig_offset;
    
    $id = intval(mosGetParam($_REQUEST, "id", 0));
    $user_id = mosGetParam($_REQUEST, "user_id", "");
    $date_platinum = mosGetParam($_REQUEST, "date_platinum", '');
if($date_platinum=='' || $user_id==''){
    $msg = 'Date and User Id can not be empty';  
  mosRedirect('index2.php?option=com_platinum_cart', $msg); 
}elseif(intval($user_id)==0){
    $msg = 'User Id must be number';  
  mosRedirect('index2.php?option=com_platinum_cart', $msg); 
    
}else{
    //$date_platinum = strtotime($date_platinum);
     $query = "SELECT id"
            . "\n FROM `tbl_platinum_club`"
            . "\n WHERE id = ".$id
    ;
    $database->setQuery($query);
    $sms_id = $database->loadObjectList();
    if($sms_id){
       
            $query = "UPDATE `tbl_platinum_club`"
                   . "\n SET `user_id`='$user_id',`start_datetime`='$date_platinum' WHERE `id`=".$id;
           $database->setQuery($query);
            if (!$database->query()) {
               echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
               exit();
           }
           
    }else{

            $query = "INSERT INTO `tbl_platinum_club` ( `user_id`,`start_datetime`) "
                            . "\n VALUES ( '$user_id','$date_platinum')"
                    ;
     
                    $database->setQuery($query);
                    if (!$database->query()) {
                        echo "<script> alert('" . $database->stderr() . "');</script>\n";
                        exit();
                    }
   $text_id =  $database->insertid();
    }
    
    $msg = 'Platinum User Successfully Saved';  
    mosRedirect('index2.php?option=com_platinum_cart', $msg);
}
   

}

function remove_platinum(&$cid,  $option) {
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }



    $cids = implode(',', $cid);
    $query = "DELETE FROM tbl_platinum_club"
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

function cancel_platinum() {
    
    
    mosRedirect('index2.php?option=com_platinum_cart');
}


?>