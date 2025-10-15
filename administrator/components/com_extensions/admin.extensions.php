<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once $mainframe->getPath('admin_html');

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$id = intval(mosGetParam($_REQUEST, 'id', 0));
$cid = josGetArrayInts('cid');
$option = 'com_extensions';
Switch ($task)
{

    
    case 'cancel':
        cancel_extension();
    break;

    case 'new':
        edit_extension( '0', $option);
        break;
    case 'edit':
        edit_extension( $id, $option );
        break;

    case 'remove':
        remove_extension($cid, $option);
    break;
    case 'save':
        save_extension( $option );
        break;
    default:
        default_extension($option);
    break;
}

function cancel_extension()
{
    mosRedirect('index2.php?option=com_extensions');
}

function remove_extension(&$cid,  $option)
{
    global $database;

    $total = count($cid);
    if ($total < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }



    $cids = implode(',', $cid);
    $query = "DELETE FROM extensions"
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

function save_extension($option)
{
    global $database;
    $id = intval(mosGetParam($_REQUEST, "id", 0));
    $abandonment  = intval(mosGetParam($_REQUEST, "abandonment", 0));
    $occassion  = intval(mosGetParam($_REQUEST, "occassion", 0));
    $call_back  = intval(mosGetParam($_REQUEST, "call_back", 0));
    $access  = intval(mosGetParam($_REQUEST, "access", 0));
    $ext  = intval(mosGetParam($_REQUEST, "ext", 0));
    if (!$id)
    {
        $query = "INSERT INTO `extensions` ( `ext`,`abandonment`,`occassion`,`access`,`call_back`) "
            . "\n VALUES ( '$ext','$abandonment','$occassion','$access','$call_back')";
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->stderr() . "');</script>\n";
            exit();
        }
    }else{
        $query = "UPDATE `extensions`"
            . "\n SET `ext`='$ext',`abandonment`='$abandonment',`occassion`='$occassion',`access`='$access',`call_back`='$call_back' WHERE `id`=".$id;
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }
    }
    mosRedirect( "index2.php?option=$option", "Save Extension Successfully" );
}


function edit_extension($id)
{
    global $database;
    
    $query = "SELECT * FROM `extensions` WHERE `id`=".$id."";
    $database->setQuery($query);
    $row = false;
    $database->loadObject($row);

    HTML_extensions::edit_extension($row);
}

function default_extension($option)
{
    global $database, $mainframe, $mosConfig_list_limit;
    
    $limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart = intval( $mainframe->getUserStateFromRequest( "viewpl{$option}limitstart", 'limitstart', 0 ) );
    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    
    $where = '';
    
    if (isset($search) && $search != "") 
    {
        $where = "WHERE ext LIKE '%$search%'";
    }
    
    $query = "SELECT COUNT(*) FROM `extensions` ".$where;

    $database->setQuery($query);
    $total = $database->loadResult();
    require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT  *
    FROM  `extensions` 
    $where";
    
    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    HTML_extensions::default_extension($search, $rows, $pageNav);
}

?>