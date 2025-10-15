<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once $mainframe->getPath('admin_html');

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$id = intval(mosGetParam($_REQUEST, 'id', 0));
$cid = josGetArrayInts('cid');

Switch ($task)
{

    
    case 'cancel':
        cancel_donation_vars();
    break;

    case 'new':
        edit_donation_vars( '0', $option);
        break;
    case 'edit':
        edit_donation_vars( $id, $option );
        break;

    case 'remove':
        remove_donation_vars($cid, $option);
    break;
    case 'save':
        save_donation_vars( $option );
        break;
    default:
        default_donation_vars($option);
    break;
}

function cancel_donation_vars()
{
    mosRedirect('index2.php?option=com_donation_vars');
}

function remove_donation_vars(&$cid,  $option)
{
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }



    $cids = implode(',', $cid);
    $query = "DELETE FROM tbl_donation_vars"
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

function save_donation_vars($option)
{
    global $database;
    $id = intval(mosGetParam($_REQUEST, "id", 0));
    $name  = mosGetParam($_REQUEST, "name", '');
    $price  = mosGetParam($_REQUEST, "price", '');
    $text  = mosGetParam($_REQUEST, "donation_text",'');
    $warehouse_id  = intval(mosGetParam($_REQUEST, "warehouse_id", 0));
    $published  = intval(mosGetParam($_REQUEST, "published", 0));

    if (!$id)
    {
        $query = "INSERT INTO `tbl_donation_vars` ( `name`,`price`,`text`,`warehouse_id`,`published`) "
            . "\n VALUES ( '$name','$price','$text','$warehouse_id','$published')";
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->stderr() . "');</script>\n";
            exit();
        }
    }else{
        $query = "UPDATE `tbl_donation_vars`"
            . "\n SET `name`='$name',`price`='$price',`text`='$text',`warehouse_id`='$warehouse_id',`published`='$published' WHERE `id`=".$id;
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }
    }
    mosRedirect( "index2.php?option=$option", "Save Donation Vars Successfully" );
}


function edit_donation_vars($id)
{
    global $database;
    
    $query = "SELECT * FROM `tbl_donation_vars` WHERE `id`=".$id."";
    $database->setQuery($query);
    $row = false;
    $database->loadObject($row);

    if ($row == false) {
        $row = (object)[];
    }
    $types = array();
    $types[] = mosHTML::makeOption("", "------ Select Warehouse ------");
    $query = "SELECT * FROM jos_vm_warehouse ORDER BY warehouse_name ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    if (count($rows)) {
        foreach ($rows as $warehouse) {
            $types[] = mosHTML::makeOption($warehouse->warehouse_id, $warehouse->warehouse_name);
        }
    }
    $row->warehouse_list = mosHTML::selectList($types, 'warehouse_id', 'class="inputbox" size="1"', 'value', 'text', isset($row->warehouse_id) ? $row->warehouse_id : '');
    $row->published 		= mosHTML::yesnoradioList( 'published', '', isset($row->published) ? $row->published : '');

    $query_used = "SELECT * FROM `tbl_used_donation` WHERE `donation_id`=".$id."";
    $database->setQuery($query_used);;
    $row->used_donates = $database->loadObjectList();

    HTML_donation_vars::edit_donation_vars($row);
}

function default_donation_vars($option)
{
    global $database, $mainframe, $mosConfig_list_limit;
    
    $limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart = intval( $mainframe->getUserStateFromRequest( "viewpl{$option}limitstart", 'limitstart', 0 ) );
    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    
    $where = '';
    
    if (isset($search) && $search != "") 
    {
        $where = "WHERE v.name LIKE '%$search%'";
    }
    
    $query = "SELECT COUNT(*) FROM `tbl_donation_vars` as v ".$where;

    $database->setQuery($query);
    $total = $database->loadResult();
    require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT  v.*,w.warehouse_name,count(d.order_id) as 'orders_count',sum(d.donation_price) as 'total_donated_price'
    FROM  `tbl_donation_vars` as v
    left join jos_vm_warehouse as w on w.warehouse_id=v.warehouse_id
    left join tbl_used_donation as d on d.donation_id = v.id
    $where group by v.id";

    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    HTML_donation_vars::default_donation_vars($rows, $pageNav,$search);
}

?>