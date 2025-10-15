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
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );


$act			= mosGetParam( $_REQUEST, "act", "" );
$cid 			= josGetArrayInts( 'cid' );
$step			= 0;
var_dump($cid);
//die($act);
switch ($act) {		
	//=============================================================================================
		//=============================================================================================
	default:
		switch ($task) {		
			
		
			case 'send':
				//sendConfirmEmail( $cid, $option );
				break;
                        case 'GetList':
                            GetList($option);
                            break;
		
			default:
				showConfirmEmailList( $option );
				break;
		}
		break;
	
}



//=================================================== LandingPages OPTION ===================================================
function GetList($option){
   global $database, $mainframe, $mosConfig_list_limit;
  $confirms_id			= mosGetParam( $_REQUEST, "confirms", "" );
   $table='';
   $query = "SELECT text_email FROM tbl_confirm_email_text where id=".$confirms_id;
$database->setQuery($query);
$text_confirms = $database->loadObjectList();
   $text_confirm=   $text_confirms[0]->text_email;
    $date_confirm			= mosGetParam( $_REQUEST, "date_conf", "" );
    $row_num=0;
    if (isset($_FILES['filename'])) {
	
	$handle = fopen($_FILES['filename']['tmp_name'], "r") ;
         while (($data = fgetcsv($handle)) !== FALSE) {
            
             $num = count($data);
            
           
            $query = "SELECT  `order`.`order_id`, `status`.`order_status_name`,`order_user`.`first_name`, `order_user`.`user_email`
                FROM `jos_vm_orders` as `order`,`jos_vm_order_status` as `status`,`jos_vm_order_user_info` as `order_user`   WHERE `order`.`order_id`=".$data[0]." and `order`.`order_status`=`status`.`order_status_code` and `order_user`.`order_id`=`order`.`order_id` and `order_user`.`address_type`='BT'";
            $database->setQuery($query);
            $row = $database->loadObjectList(); 
            if(count($row)>0){
                 $table.="<tr>";
            mosMakeHtmlSafe($row);  
	    $checked 	= mosHTML::idBox( $row_num, $row[0]->order_id);
            $row_num++;
            $table.="<td>".$checked." </td>";
            $table.="<td name='id".$data[0]."'>".$data[0]."</td>";
            $table.="<td name='user_name".$data[0]."'>".$row[0]->first_name."</td>";
            $table.="<td name='user_email".$data[0]."'>".$row[0]->user_email."</td>";
             $table.="<td name='order_status".$data[0]."'>".$row[0]->order_status_name."</td>";
             if(isset($data[1])){
               $table.="<td name='order_track".$data[0]."'>".$data[1]."</td>";   
             }else{
                 $table.="<td name='order_track".$data[0]."'></td>";   
             }
            $query 		= "SELECT * FROM #__vm_order_history AS OH, #__vm_order_status AS OS  WHERE OH.order_status_code = OS.order_status_code AND OH.order_id = $data[0] ORDER BY OH.order_status_history_id DESC LIMIT 2";
	$database->setQuery($query);
	$rows	= $database->loadObjectList();	
	 
         $sImagePath = $mosConfig_live_site . "/administrator/images/";
       $html='<td>';
        $html.='<table width="100%" class="adminform">';
           $html.=' <tr>';
             $html.='   <th width="20%" style="text-align:left;">Date Added</th>';
             $html.='   <th width="10%">Customer Notified?</th>';
             $html.='   <th width="10%">Warehouse Notified?</th>';
             $html.='   <th width="10%">IRIS Notified?</th>';
             $html.='   <th width="10%">Status</th>';
             $html.='   <th width="10%">User name</th>';
            $html.='   <th width="40%">Comment</th>';
          $html.='  </tr>';
          
            foreach ($rows as $item) {
                
                $html.='   <tr>';
                   $html.='    <td style="white-space:nowrap"> '.$item->date_added.'</td>';
                   $temp=( intval($item->customer_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png";
                  $html.='     <td style="text-align:center;"><img src="'.$temp.'"/></td>';
                     $temp=( intval($item->warehouse_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png";
                    $html.='   <td style="text-align:center;"><img src="'.$temp.'"/></td>';
                     $temp=( intval($item->iris_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png";
                  $html.='     <td style="text-align:center;"><img src="'.$temp.'"/></td>';
                   $html.='    <td style="text-align:center;"><strong>'.$item->order_status_name.'</strong></td>';
                   $html.='    <td style="text-align:center;"><strong> '.$item->user_name.'</strong></td>';
                    $temp=( $item->comments != "" ) ? $item->comments : "./.";
                   $html.='    <td style="text-align:left;">'.$temp.'</td>';
               $html.='    </tr>';
            }		
         $html.='  </table>';
       $html.='</td>';
         $table.=$html;
        
            
            $table.="</tr>";
            }
        }
      
}

$query = "SELECT id, name FROM tbl_confirm_email_text ORDER BY id ASC";
$database->setQuery($query);

$confirms = $database->loadObjectList();
$query = "SELECT id, text_email FROM tbl_confirm_email_text ORDER BY id ASC";
$database->setQuery($query);
$text_confirms = $database->loadObjectList();

        $text_confirm=mosHTML::selectList($confirms, "confirms", "size='1' id='confirms'", "id", "name",'');
               
    
	HTML_ConfirmEmail::showConfirmEmailList(  $option, $table,$text_confirm, $date_confirm); 
}
function showConfirmEmailList( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;
        $query = "SELECT id, name FROM tbl_confirm_email_text ORDER BY id ASC";
$database->setQuery($query);

$confirms = $database->loadObjectList();
$query = "SELECT id, text_email FROM tbl_confirm_email_text ORDER BY id ASC";
$database->setQuery($query);
$text_confirms = $database->loadObjectList();

        $text_confirm=mosHTML::selectList($confirms, "confirms", "size='1' id='confirms'", "id", "name",'');
    
	HTML_ConfirmEmail::showConfirmEmail(  $option ,$text_confirm);
}




function sendConfirmEmail( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	var_dump($cid);
	if (count( $cid )) {		
		foreach ($cid as $value) {
                    
			//$query = "DELETE FROM tbl_landing_pages WHERE id = $value";
			//$database->setQuery( $query );
			//if (!$database->query()) {
			//	echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			//}				
		}
	}
	
	mosRedirect( "index2.php?option=$option", "Send confirmation emails Successfully" );
}


?>