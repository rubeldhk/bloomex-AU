<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/*
 * CREATE TABLE IF NOT EXISTS `tbl_exit_page_pop_up` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(11) NOT NULL,
  `message` text NOT NULL,
  `product_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

// include support libraries
require_once( $mainframe->getPath('admin_html') );
require_once('loadfunc.php' );
$task = mosGetParam($_REQUEST, 'task', '');
$wf = mosGetParam($_REQUEST, 'wf', '');
$dfrom = mosGetParam($_REQUEST, 'dfrom', '');
$dto = mosGetParam($_REQUEST, 'dto', '');
$norder = mosGetParam($_REQUEST, 'norder', '');
$ordersrc = mosGetParam($_REQUEST, 'ordersrc', '');


function savefunc() {
    global $database;
    $lang = $_REQUEST['lang'];
    $message = mysql_real_escape_string($_REQUEST['popup_message']);
    $sku = mysql_real_escape_string($_REQUEST['product_id']);
    $active = $_REQUEST['popup_active'];
    
    $q = "INSERT INTO tbl_exit_page_pop_up  (lang, message, product_id, active) VALUES ";
    $q .= "('" . $lang . "', ";
    $q .= "'" . $message . "', ";
    $q .= "'" . $sku . "', ";
    $q .= "'" . $active . "') ";
    
    $result = $database->setQuery($q);
    $database->query();
    

}

function updatefunc($id) {
    global $database,$mosConfig_absolute_path;
    $popup_message=mysql_real_escape_string($_REQUEST['popup_message']);
  
    $lang=$_REQUEST['lang'];
    $product_id=mysql_real_escape_string($_REQUEST['product_id']);
    $popup_active=$_REQUEST['popup_active'];
    $check_active = $_REQUEST['check_active'];
    if($check_active && !empty($popup_active)){
    $q = "UPDATE tbl_exit_page_pop_up SET
        `active`='" .$popup_active . "'
    WHERE `id`=". $id;
    } elseif($check_active && empty($popup_active)) {
    $q = "UPDATE tbl_exit_page_pop_up SET
        `active`='0'
        WHERE `id`=". $id;
        
    } else {
    $q = "UPDATE tbl_exit_page_pop_up SET
        `message`='" .$popup_message . "'
        ,`lang`='" .$lang . "'
        ,`product_id`='" .$product_id . "'
        ,`active`='" .$popup_active . "'
        WHERE `id`=". $id;
    }    
    $result = $database->setQuery($q);
    $database->query();

    
}


function upload_images()
{
  
}

function removeDirectory( $dir ) {
    if ( $objs = glob($dir."/*" )) {
       foreach( $objs as $obj ) {
         is_dir( $obj ) ? removeDirectory( $obj ) : unlink( $obj );
       }
    }
    rmdir($dir);
  }


function upload( $ident_image )
{
    global $mosConfig_absolute_path;
    
    $city = "";
    if($_REQUEST['city'] != '')
    {
        $city = $_REQUEST['city']."/";
    }
    $dir = $mosConfig_absolute_path."/templates/bloomex7/images/".$city;
       
    if( $_FILES["filename_".$ident_image]["tmp_name"] != '' )
    {
        if($_FILES["filename_".$ident_image]["size"] > 1024*3*1024 )
        {
          $html = "<span style='color=#0000FF;'>File size is more than three megabytes.</span>";
          exit;
        }
        else
        {
            if(is_uploaded_file($_FILES["filename_".$ident_image]["tmp_name"]))
            {
                if (!is_dir($dir)) mkdir($dir);
                move_uploaded_file($_FILES["filename_".$ident_image]["tmp_name"], $dir."footer_banner_".$ident_image.".jpg");
                return true;
            }
            return false;            
        }
    }
    return true;
}


function deletefunc($id) { 
    global $database;
    $q = 'DELETE FROM tbl_exit_page_pop_up  WHERE id="' . $id . '"';
    $html = $q;
    $database->setQuery($q);
    $database->query();
    if ($database->GetErrorMsg()) {
        $html .="<br/>" . $database->GetErrorMsg();
    } else {
         $q = 'DELETE FROM tbl_exit_page_pop_up  WHERE db_id="' . $id . '"';
    $database->setQuery($q);
    $database->query();
        $html .="<br/>" . "Server deleted sucessfull";
    }
    return $html;
}

switch ($task) {
    case "edit":
        ExitPagePopUp::Edit($id);
        break;
    case "new":
        ExitPagePopUp::Add();
        break;
    case "save":
        ExitPagePopUp::Main(savefunc());
        break;
    case "update":
        ExitPagePopUp::Main(updatefunc($id));
        break;
    case "delete":
        ExitPagePopUp::Main(deletefunc($id));
        break;
    case "load_order":
        ExitPagePopUp::Main(loadfunc($wf,$dfrom,$dto,$norder,$ordersrc));
        break;
    default:
        ExitPagePopUp::Main();
        break;
}

?>
