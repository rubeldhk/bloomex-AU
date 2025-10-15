<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once $mainframe->getPath('admin_html');

$cid = josGetArrayInts('cid');
$option = 'com_ccards';

Switch ($task) {
    case 'publish':
        changeBlock($cid, 1);
    break;

    case 'unpublish':
        changeBlock($cid, 0);
    break;

    case 'remove':
        delete_view($cid);
    break;

    case 'save':
        save_view();
    break;

    case 'new':
    case 'edit':
        edit_view($cid);
    break;

    default:
        default_view($option);
    break;
}

function changeBlock($cid, $block) {
    global $database;
    
    if (count($cid) < 1) {
        echo "<script type=\"text/javascript\"> alert('Select an item'); window.history.go(-1);</script>\n";
        exit;
    }
    
    $sql = "UPDATE `jos_vm_user_ccards` SET `block`='".$block."' WHERE `id` IN (".implode(',', $cid).")";
    $database->setQuery($sql);
    $database->query();

    $msg = 'Successfully!';
    
    mosRedirect('index2.php?option=com_ccards', $msg);
}

function delete_view($cid) {
    global $database;
    
    if (count($cid) < 1) {
        echo "<script type=\"text/javascript\"> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    $sql = "DELETE FROM `jos_vm_user_ccards` WHERE `id` IN (".implode(',', $cid).")";
    $database->setQuery($sql);

    $database->query();

    $msg = 'Deleted successfully!';

    mosRedirect('index2.php?option=com_ccards', $msg);
}

function save_view() {
    global $database;
    
    $ccard_id = mosGetParam($_REQUEST, 'ccard_id', false);

    $mask = mosGetParam($_REQUEST, 'mask', false);
    $block = mosGetParam($_REQUEST, 'block', false);
    $email = mosGetParam($_REQUEST, 'email', false);
    
    $query = "SELECT `u`.`id` 
    FROM `jos_users` AS `u` 
    WHERE `u`.`email`='".$database->getEscaped($email)."'";
    $database->setQuery($query);
    $row = false;
    $database->loadObject($row);
    
    if (!$row) {
        $row->id = 0;
    }
    
    $msg = '';
    
    if ($ccard_id) {
        $sql = "UPDATE `jos_vm_user_ccards` SET `mask`='".$database->getEscaped($mask)."', `block`='".(int)$block."', `user_id`=".$row->id." WHERE `id`=".(int)$ccard_id;
        $msg .= 'Updated successfully!';
    }
    else {
        $sql = "INSERT INTO `jos_vm_user_ccards`
        (
            `mask`,
            `block`,
            `user_id`
        )
        VALUES (
            '".$database->getEscaped($mask)."',
            '".(int)$block."',
            ".$row->id."
        )";
        $msg .= 'Added successfully!';
    }
    
    $database->setQuery($sql);
    $database->query();

    mosRedirect('index2.php?option=com_ccards', $msg);
}

function edit_view($cid) {
    global $database;
    
    $query = "SELECT `c`.*, `u`.`email` 
    FROM `jos_vm_user_ccards` AS `c`
    LEFT JOIN `jos_users` AS `u` ON `u`.`id`=`c`.`user_id`
    WHERE `c`.`id`=".(int)$cid[0]."";
    $database->setQuery($query);
    $row = false;
    $database->loadObject($row);

    HTML_CCARDS::edit_view($row);
}

function default_view($option) {
    global $database, $mainframe, $mosConfig_list_limit;
    
    $limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart = intval( $mainframe->getUserStateFromRequest( "viewpl{$option}limitstart", 'limitstart', 0 ) );
    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    $orderby = $mainframe->getUserStateFromRequest("orderby{$option}", 'orderby', '');
    $orderby = $database->getEscaped(trim($orderby));

    $where = '';
    
    if (isset($search) && $search != "") {
        $where = "WHERE `c`.`mask` LIKE '%$search%'";
    }
    
    $order_by = "ORDER BY `u`.`email` ASC";
    
    if (isset($orderby) && $orderby != "") {
        $order_by = "ORDER BY ".$orderby."";
    }
    
    $query = "SELECT COUNT(`id`) FROM `jos_vm_user_ccards` AS `c` ".$where."";

    $database->setQuery($query);
    $total = $database->loadResult();
    require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT  `c`.*, `u`.`email`
    FROM  `jos_vm_user_ccards` AS  `c` 
    LEFT JOIN `jos_users` AS `u` ON `u`.`id`=`c`.`user_id`
    ".$where."
    ".$order_by."";
    
    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    HTML_CCARDS::default_view($rows, $pageNav, $search, $orderby);
}
