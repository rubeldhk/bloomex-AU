<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

require_once( $mainframe->getPath('admin_html') );

$cid = josGetArrayInts( 'cid' );
    
Switch ($task)
{
    case 'save':
        save();
    break;

    case 'publish':
        change($cid, 1);      
    break;

    case 'unpublish':
        change($cid, 0);      
    break;

    case 'remove':
        remove($cid);
    break;	

    case 'new':
        edit('0');
    break;

    case 'edit':
        edit(intval($cid[0]));
    break;

    case 'move':
        move();
    break;

    case 'cancel':
        
    default: 
        display();
}

function remove($cid)
{
    global $database, $act, $mosConfig_absolute_path;
	
    mosArrayToInts( $cid );
    
    $cids = 'id=' . implode( ' OR id=', $cid );
	
    $query = "DELETE FROM tbl_thankyoupage_images WHERE ( $cids )";
    $database->setQuery( $query );
    
    if (!$database->query()) 
    {
        $msg = 'Error';
    }
    else 
    {
        $msg = 'The image(s) were removeted successfully';
    }
    
    mosRedirect('index2.php?option=com_thankyoupage', $msg);
}

function change($cid, $state=0) 
{
    global $database, $my, $act;

    if (!is_array( $cid ) OR count( $cid ) < 1) 
    {
        $action = $state ? 'publish' : 'unpublish';
        mosErrorAlert( "Select an item to $action" );
    }

    mosArrayToInts( $cid );
    
    $cids = 'id=' . implode( ' OR id=', $cid );

    $query = "UPDATE tbl_thankyoupage_images SET publish = '" . (int) $state . "' WHERE ( $cids )";
    $database->setQuery( $query );
    
    if (!$database->query()) 
    {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
    }

    if ($state) 
    {
        $msg = 'Image(s) were published successfully';
    }
    else 
    {
        $msg = 'Image(s) were unpublished successfully';
    }

    mosRedirect('index2.php?option=com_thankyoupage', $msg);
}

function display ()
{
    global $database;
    
    $query = "SELECT * FROM `tbl_thankyoupage_images` ORDER BY `queue`";
    
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    
    ThankYouPage_Images::display($rows);
}

function edit($id)
{
    global $database;
    
    $row = null;
    
    if ($id)
    {
        $query = "SELECT * FROM `tbl_thankyoupage_images` WHERE `id`=".$id;
    
        $database->setQuery($query);
        $row = $database->loadRow();
    }
    
    ThankYouPage_Images::edit($row);
}

function save()
{
    global $database, $mosConfig_absolute_path;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $url = addslashes($_POST['url']);
    
//    echo '<pre>';
//    print_r($_POST);
//    echo '</pre>';
//    echo '<pre>';
//    print_r($_GET);
//    echo '</pre>';
//    echo '<pre>';
//    print_r($_FILES);
//    echo '</pre>';
//    
//    die;
    if ($_FILES["image"]["size"] > 0)
    {
        copy($_FILES["image"]["tmp_name"], $mosConfig_absolute_path.'/images/thankyou_page_images/'.$_FILES["image"]["name"]);
        
        $image_link = '/images/thankyou_page_images/'.$_FILES["image"]["name"];
    }
        
    if ($id)
    {
        $query = "UPDATE `tbl_thankyoupage_images` SET ".($image_link ? "`image_link`='".$image_link."'," : '')." `url`='".$url."' WHERE `id`=".$id;
    
        $database->setQuery($query);
        $database->query();
    }
    else
    {
        $query = "SELECT * FROM `tbl_thankyoupage_images` ORDER BY `queue` DESC LIMIT 1";
    
        $database->setQuery($query);
        $new_row = $database->loadRow();
        
        $query = "INSERT INTO `tbl_thankyoupage_images` (".($image_link ? "`image_link`," : "")." `url`, `queue`) VALUES (".($image_link ? "'".$image_link."', " : "")." '".$url."', ".($new_row[4]+1).")";

//        echo $query;
//        die;
        $database->setQuery($query);
        $database->query();
    }
    
    mosRedirect('index2.php?option=com_thankyoupage');
}

function move()
{
    $id = isset($_POST['id_mobile_queue']) ? (int)$_POST['id_mobile_queue'] : 0;
    $updown = $_POST['updown'];
    
    $row = null;
    
    if ($id)
    {
        $query = "SELECT * FROM `tbl_thankyoupage_images` WHERE `id`=".$id;
    
        $database->setQuery($query);
        $row = $database->loadRow();
        
        if ($row)
        {
            $query = "SELECT * FROM `tbl_thankyoupage_images` WHERE `id_category`=".$row[2]." AND `queue` ".($updown == 'up' ? '<' : '>')." ".$row[3]." ORDER BY `queue` LIMIT 1";
    
            $database->setQuery($query);
            $new_row = $database->loadRow();


            $query = "UPDATE `tbl_thankyoupage_images` SET `queue`=".$row[3]." WHERE `id_mobile_queue`=".$new_row[0]."";

            $database->setQuery($query);
            $database->query();

            $query = "UPDATE `tbl_thankyoupage_images` SET `queue`=".$new_row[3]." WHERE `id_mobile_queue`=".$row[0]."";

            $database->setQuery($query);
            $database->query();
        }
    }
}

?>
