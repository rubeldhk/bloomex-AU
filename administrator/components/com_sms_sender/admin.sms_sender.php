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
$limit_pagination = 100;
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
    case 'pagination_numbers':
        pagination_numbers();
        break;
    case 'publish':
        changestatus($cid, 1, $option);
        break;

    case 'unpublish':
        changestatus($cid, 0, $option);
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
function pagination_numbers(){
    global $database,$limit_pagination;
    if($_POST && $_POST['page']){
        $uid = $_POST['text_id'];
        $total = $_POST['total'];
        if(isset($_POST["page"])){
            $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
            if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
        }else{
            $page_number = 1; //if there's no page number, set it to 1
        }

        $page_position = (($page_number-1) * $limit_pagination);

        $query_numbers = "SELECT number,sent,date,datesend,message_id "
            . "\n FROM tbl_numbers_for_sending"
            . " WHERE text_id='".$uid."' ORDER BY number limit $page_position,$limit_pagination";

        $database->setQuery($query_numbers);
        $row_numbers = $database->loadObjectList();
        if($row_numbers)
            $list = list_numbers($row_numbers,0,$limit_pagination,$page_number,$total);

        die($list);
    }
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


    $query = "UPDATE `tbl_sms_text`"
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


function view_sms($sectionid,$option) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0));
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = [];
    if ($search) {
        $where[] = "LOWER( title ) LIKE '%$search%'";
    }

    $query = "SELECT COUNT(*)"
        . "\n FROM tbl_sms_text"
        . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : "" )
    ;
    $database->setQuery($query);
    $total = $database->loadResult();
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);



    $query = "SELECT * "
        . "\n FROM tbl_sms_text"
        . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
        . " ORDER BY date DESC"

    ;
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    foreach($rows as $row){
        $query_total = "SELECT count(number) as total"
            . "\n FROM tbl_numbers_for_sending"
            . " WHERE text_id=".$row->id;

        $database->setQuery($query_total);
        $numbers_total = $database->loadObjectList();

        $query_sents = "SELECT count(number) as sents"
            . "\n FROM tbl_numbers_for_sending"
            . " WHERE `sent`= '1' AND text_id=".$row->id;

        $database->setQuery($query_sents);
        $numbers_sents = $database->loadObjectList();

        $row->sentvstotal = $numbers_sents[0]->sents." / ".$numbers_total[0]->total;

    }

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_sms_sender::showsms_texts($rows, $pageNav,$search);
}

function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<ul class="pagination_numbers">';

        $right_links    = $current_page + 3;
        $previous       = $current_page - 3; //previous link
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link

        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; //first link
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
            for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                if($i > 0){
                    $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page'.$i.'">'.$i.'</a></li>';
                }
            }
            $first_link = false; //set first link to false
        }

        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }

        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){
            $next_link = ($i > $total_pages)? $total_pages : $i;
            $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
            $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
        }

        $pagination .= '</ul>';
    }
    return $pagination; //return pagination links
}

function list_numbers($row_numbers,$start,$limit,$page_number,$total){
    $html = "<table border='1'><tr>"
        . "<th>Number</th>"
        . "<th>Date For SMS</th>"
        . "<th>Date Sent SMS</th>";
    $j=0;
    foreach($row_numbers as $k=>$em){

        if($k<$start){
            continue;
        }
        if($j==$limit){
            break;
        }
        if($em->sent)
            $sent = "Sent";
        else
            $sent = 'have not  Sent yet';
        if($em->datesend != 0)
            $datesend = $em->datesend;
        else
            $datesend = '';

        $html .= "<tr>";
        $html .= "<td>$em->number</td>";
        $html .= "<td>$em->date</td>";
        $html .= "<td>$datesend</td>";
        $html .= "</tr>";
        $j++;
    }
    $html .= "</table>";
    $html .=  paginate_function($limit, $page_number, $total,ceil($total/$limit));
    return $html;
}
function editsmstext($uid = 0,  $option) {
    global $database, $my, $mainframe,$limit_pagination;
    global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;

    $nullDate = $database->getNullDate();

    $query = "SELECT * "
        . "\n FROM tbl_sms_text"
        . " WHERE id=".$uid;

    $database->setQuery($query);
    $row = $database->loadObjectList();

    $query_numbers = "SELECT number,sent,date,datesend,message_id "
        . "\n FROM tbl_numbers_for_sending"
        . " WHERE text_id='".$uid."' ORDER BY number";

    $database->setQuery($query_numbers);
    $row_numbers = $database->loadObjectList();
    $total = count($row_numbers);
    if($row_numbers)
        $list = list_numbers($row_numbers,0,$limit_pagination,1,$total);

    $to_string ='';
    if(isset($row_numbers)&& !empty($row_numbers)){
        $to_array = array();
        foreach($row_numbers as $number){
            $to_array[] = $number->number;
        }
        $to_string = implode(",", $to_array);

    }
    $row[0]->to = $to_string;

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }


    $params = new mosParameters('', $mainframe->getPath('com_xml', 'com_sms_sender'), 'component');

    HTML_sms_sender::editsmstext($row[0],$params,  $option,$list,$total);
}
function removeslashes($string)
{
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}
function save_sms( $task) {
    global $database, $my, $mainframe, $mosConfig_offset;
    $id = intval(mosGetParam($_REQUEST, "id", 0));
    $title = mosGetParam($_REQUEST, "title", "");
    $html = $_REQUEST['introtext']?$_REQUEST['introtext']:'';
    $date_send = mosGetParam($_REQUEST, "date_send", '');
    $publish  = intval(mosGetParam($_REQUEST, "publish", 0));
    if(!isset($publish)){
        $publish = 0;
    }

    $text_id =  $id;
    $query = "SELECT id"
        . "\n FROM `tbl_sms_text`"
        . "\n WHERE id = ".$id
    ;
    $database->setQuery($query);
    $sms_id = $database->loadObjectList();
    if($sms_id){

        $query = "UPDATE `tbl_sms_text`"
            . "\n SET `sent`='$publish',`text`='$html',`date`='$date_send',`title`='$title' WHERE `id`=".$id;
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        $text_id =  $id;

    }else{

        $query = "INSERT INTO `tbl_sms_text` ( `text`,`date`,`title`,`sent`) "
            . "\n VALUES ( '$html','$date_send','$title','$publish')"
        ;

        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->stderr() . "');</script>\n";
            exit();
        }
        $text_id =  $database->insertid();
    }

    $list_number_to = mosGetParam($_REQUEST, "to", "");
    $list_number_to = removeslashes ($list_number_to);

    $list_number_to_str = $list_number_to;
    $list_number_to = explode(',', $list_number_to);
    $list_number_to = array_map('trim',$list_number_to);


    if(end($list_number_to) == ''){
        array_pop($list_number_to);
    }
    $list_number_to = array_unique($list_number_to);

    $query = "SELECT number"
        . "\n FROM `tbl_numbers_for_sending`"
        . "\n WHERE `text_id` = '$text_id'" ;
    $database->setQuery($query);
    $number_check = $database->loadResultArray();
    if($number_check){
        $array_diff = array_diff($number_check,$list_number_to);
        if(!empty($array_diff)){
            foreach($array_diff as $diff){
                $query_numbers = "DELETE FROM tbl_numbers_for_sending"
                    . "\n WHERE number='$diff' AND text_id IN ( $text_id )"
                ;
                $database->setQuery($query_numbers);
                if (!$database->query()) {
                    echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                    exit();
                }
            }
        }
    }



    foreach($list_number_to as $key => $number){
        $query = "SELECT id"
            . "\n FROM `tbl_numbers_for_sending`"
            . "\n WHERE `text_id` = '$text_id' AND `number`='$number'" ;
        $database->setQuery($query);

        $number_check = $database->loadObjectList();
        if($number_check){

            $query_number = "UPDATE `tbl_numbers_for_sending`"
                . "\n SET `date`='$date_send' WHERE `text_id`='$text_id' AND `number`='$number'";

            $database->setQuery($query_number);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                exit();
            }
        }else{

            $query_number = "INSERT INTO `tbl_numbers_for_sending` ( `date`,`number`,`sent`,`text_id`,`message_id`) "
                . "\n VALUES ( '$date_send','$number','0','$text_id','0')"
            ;
            $database->setQuery($query_number);
            if (!$database->query()) {
                echo "<script> alert('" . $database->stderr() . "');</script>\n";
                exit();
            }

        }

    }

    if($publish == 1){
        $today = date("Y-m-d");
        if(date("Y-m-d", strtotime($date_send)) == $today){
            $msg = ' SMS Template Successfully Saved and will be sent to orders today';
        }else{
            $msg = ' SMS Template Successfully Saved and will be sent to orders at : '.$date_send;
        }
    }else{
        $msg = 'SMS Template  Successfully Saved';
    }
    mosRedirect('index2.php?option=com_sms_sender', $msg);

}

function remove_sms(&$cid,  $option) {
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }



    $cids = implode(',', $cid);
    $query = "DELETE FROM tbl_sms_text"
        . "\n WHERE id IN ( $cids )"
    ;
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $query_numbers = "DELETE FROM tbl_numbers_for_sending"
        . "\n WHERE text_id IN ( $cids )"
    ;
    $database->setQuery($query_numbers);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }


    $msg = $total . " Item(s) Deleted";
    mosRedirect('index2.php?option=' . $option , $msg);
}

function cancel_sms() {


    mosRedirect('index2.php?option=com_sms_sender');
}

class SMSParam {

    public $CellNumber;
    public $AccountKey;
    public $MessageBody;

}

function get_customer_reply($message_id){
    global $mosConfig_limit_sms_sender_AccountKey;
    $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
    $parameters = new SMSParam;
    $parameters->AccountKey =$mosConfig_limit_sms_sender_AccountKey;
//                     $parameters->MessageID = 243231557;
    $parameters->MessageID = $message_id;
    $Result_id = $client->GetRepliesToMessage($parameters);
    $res = $Result_id->GetRepliesToMessageResult->SMSIncomingMessage;
    $mesage_html = '';
    if($res) {
        if(is_array($res)){
            foreach($res as $r){
                $mesage_html .=$r->Message." <hr>" ;
            }
        }else{
            $mesage_html .= $res->Message ;

        }

    }
    return $mesage_html;

}
?>