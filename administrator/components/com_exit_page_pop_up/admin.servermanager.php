<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

// include support libraries
require_once( $mainframe->getPath('admin_html') );
require_once('loadfunc.php' );
//D:\Projects_Mariya\bloomex.ca\bare.repository\administrator\components\com_servermanager\admin.servermanager.html.php
$task = mosGetParam($_REQUEST, 'task', '');
$wf = mosGetParam($_REQUEST, 'wf', '');
$dfrom = mosGetParam($_REQUEST, 'dfrom', '');
$dto = mosGetParam($_REQUEST, 'dto', '');
$norder = mosGetParam($_REQUEST, 'norder', '');
$ordersrc = mosGetParam($_REQUEST, 'ordersrc', '');


function savefunc() {
    global $database;
    $key="LRWvbvldfgERUFG;AekghbnzyfgUKA956";//pass
//$shifr=encode($password, $key);

//$rashift=encode(shifr, $key);
    $title=$_REQUEST['title'];
    $host=$_REQUEST['host'];
    $login= $_REQUEST['login'];
    $password=$_REQUEST['password'];
    $db_name=$_REQUEST['db_name'];
    $start_date=$_REQUEST['start_date'];
    $interval=$_REQUEST['interval'];
    $cod=$_REQUEST['cod'];
    $search_user=$_REQUEST['search_user'];
    
    $q = "INSERT INTO tbl_db_connect_data ( `title`,`host`,`login`,`password`,`db_name`,`start_date`,`interval`,`cod`,`search_user`) VALUES ";
    $q.= '("' .  $title . '",';
    $q.= '"' .$host . '",';
    $q.= '"' .$login . '",';
    $q.= '"' . mysql_escape_string(encode($password,$key)) . '",';
    $q.= '"' . $db_name . '",';
    $q.= 'UNIX_TIMESTAMP("' . $start_date  . '"),';
     $q.= '"' . $interval . '",';
     $q.= '"' . $cod . '",';
    $q.= '"' . $search_user . '" )';
   $html='';
  
    //$html = htmlentities($q);
    $database->setQuery($q);
    $database->query();
    $res_id=$database->insertid();
    if ($database->GetErrorMsg()) {
        $html .="<br/>" .htmlentities($database->GetErrorMsg());
    } else {
       $q = "INSERT INTO tbl_last_load ( `db_id`,`last_time`,`block`) VALUES ";
    $q.= '(' .  $res_id . ',';
    $q.= 'UNIX_TIMESTAMP("' . $start_date  . '"),0)';
    $database->setQuery($q);
    $database->query();

      $path=$mosConfig_absolute_path . '/orderfeed';
     $params_connect_db = array();
  
    foreach(file($path."/".$title) as $line)
    {
        list($key, $val) = explode("=", $line);
        $params_connect_db[$key] = $val;     
    }
            $host=trim($params_connect_db['host']);
            $login=trim($params_connect_db['login']);
            $db_name=trim($params_connect_db['db_name']);
            $password=trim($params_connect_db['password']);//encode( rtrim($row['password']), $key);

     
        // Lets connect
$link = mysql_connect( $host,  $login, $password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Which database?
if (!mysql_select_db($db_name , $link)) {
    echo 'Could not select database';
    exit;
}
    $sql = "CREATE TABLE IF NOT EXISTS `tbl_last_loading` (`last_date_timest` int(11) NULL)";
    $result = mysql_query($sql, $link);
        $sql = "delete from `tbl_last_loading`";
    $result = mysql_query($sql, $link);
    $sql = "insert into `tbl_last_loading` (`last_date_timest`) VALUES(UNIX_TIMESTAMP( '".$start_date."'))";
    $result = mysql_query($sql, $link);

        $html .="<br/>" . "Server added sucessfull, table aded ";
    }
    return $html;
}

function updatefunc($id) {
    global $database,$mosConfig_absolute_path;
    $key="LRWvbvldfgERUFG;AekghbnzyfgUKA956";//pass
    
    if( !upload_images() )
    {
       return $html = "<br/>" . "<span style='color=#0000FF;'>Error loading banner.</span>";
    }
    $title=$_REQUEST['title'];
    $host=$_REQUEST['host'];
    $login= $_REQUEST['login'];
    $password=$_REQUEST['password'];
    $db_name=$_REQUEST['db_name'];
    $start_date=$_REQUEST['start_date'];
    $interval=$_REQUEST['interval'];
    $cod=$_REQUEST['cod'];
    $search_user=$_REQUEST['search_user'];
    $q = "UPDATE tbl_db_connect_data set
        `title`='" .$title . "'
        ,`start_date`=UNIX_TIMESTAMP('" .$start_date . "') 
        ,`interval`='" .$interval . "'
        ,`cod`='" .$cod . "'
        ,`search_user`='" .$search_user . "'
        WHERE `id`=". $id;
    $html='';
    //$html = htmlentities($q);
    $database->setQuery($q);
    $database->query();
    
    if ($database->GetErrorMsg()) {
        $html .="<br/>" .htmlentities($database->GetErrorMsg());
    } else {
       $q = "REPLACE tbl_last_load (  `db_id`,`last_time`,`block`) VALUES ";
    $q.= '(' .  $id . ',';
    $q.= 'UNIX_TIMESTAMP("' . $start_date  . '"),0)';
    $database->setQuery($q);
    $database->query();
    
    $path=$mosConfig_absolute_path . '/orderfeed';
     $params_connect_db = array();
  
    foreach(file($path."/".$title) as $line)
    {
        list($key, $val) = explode("=", $line);
        $params_connect_db[$key] = $val;     
    }
            $host=trim($params_connect_db['host']);
            $login=trim($params_connect_db['login']);
            $db_name=trim($params_connect_db['db_name']);
            $password=trim($params_connect_db['password']);//encode( rtrim($row['password']), $key);

       $link = mysql_connect( $host,  $login, $password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Which database?
if (!mysql_select_db($db_name , $link)) {
    echo 'Could not select database';
    exit;
}
    $sql = "CREATE TABLE IF NOT EXISTS `tbl_last_loading` (`last_date_timest` int(11) NULL)";
    $result = mysql_query($sql, $link);
        $sql = "delete from `tbl_last_loading`";
    $result = mysql_query($sql, $link);
    $sql = "insert into `tbl_last_loading` (`last_date_timest`) VALUES(UNIX_TIMESTAMP( '".$start_date."'))";
    $result = mysql_query($sql, $link); 
        
        $html .="<br/>" . "Server updated sucessfull";

    }
    return $html;
}


function upload_images()
{
    if( isset($_REQUEST['delete_images']) )
    {
        global $mosConfig_absolute_path;
    
        $city = "";
        if($_REQUEST['city'] != '')
        {
            $city = $_REQUEST['city']."/";
        }
        $dir = $mosConfig_absolute_path."/templates/bloomex7/images/".$city;
        removeDirectory( $dir );
        return true;
    }
    for( $i = 0; $i < 4; $i++ )
    {
        if(!upload( $i )) return false;
    }
    return true;
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
    $q = 'DELETE FROM tbl_db_connect_data  WHERE id="' . $id . '"';
    $html = $q;
    $database->setQuery($q);
    $database->query();
    if ($database->GetErrorMsg()) {
        $html .="<br/>" . $database->GetErrorMsg();
    } else {
         $q = 'DELETE FROM tbl_last_load  WHERE db_id="' . $id . '"';
    $database->setQuery($q);
    $database->query();
        $html .="<br/>" . "Server deleted sucessfull";
    }
    return $html;
}

switch ($task) {
    case "edit":
        ServerManagerScreens::Edit($id);
        break;
    case "new":
        ServerManagerScreens::Add();
        break;
    case "save":
        ServerManagerScreens::Main(savefunc());
        break;
    case "update":
        ServerManagerScreens::Main(updatefunc($id));
        break;
    case "delete":
        ServerManagerScreens::Main(deletefunc($id));
        break;
    case "load_order":
        ServerManagerScreens::Main(loadfunc($wf,$dfrom,$dto,$norder,$ordersrc));
        break;
    default:
        ServerManagerScreens::Main();
        break;
}

?>
