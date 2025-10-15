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


switch ($task) {
    case 'details':
        showDetails( $id, $option );
        break;
    default:
        showStripeOrdersLogs( $option );
        break;
}


function showStripeOrdersLogs( $option ) {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
    $order_id 	= trim(mosGetParam( $_POST, "order_id", '' ));
    $user_email 	= trim(mosGetParam( $_POST, "user_email", '' ));
    $order_status 	= trim(mosGetParam( $_POST, "order_status", '' ));
    $transaction_id 	= trim(mosGetParam( $_POST, "transaction_id", '' ));
    $session_id 	= trim(mosGetParam( $_POST, "session_id", '' ));

    $where 	= "";
    $aWhere	= array();

    if( $user_email ) {
        $aWhere[]	= " user_email LIKE '%$user_email%' ";
    }
    if( $order_id ) {
        $aWhere[]	= " order_id LIKE '%$order_id%' ";
    }
    if( $order_status ) {
        $aWhere[]	= " order_status LIKE '%$order_status%' ";
    }
    if( $transaction_id ) {
        $aWhere[]	= " transaction_details LIKE '%$transaction_id%' ";
    }
    if( $session_id ) {
        $aWhere[]	= " order_data LIKE '%$session_id%' ";
    }

    if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);
    $groupBy = ' group by user_id,order_status,order_total';

    // get the total number of records
    $query = "SELECT id FROM tbl_stripe_orders_logs $where $groupBy";
    $database->setQuery( $query );
    $total = count($database->loadObjectList());

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav( $total, $limitstart, $limit  );

    // get the subset (based on limits) of required records
    $query = "SELECT * FROM tbl_stripe_orders_logs $where $groupBy ORDER BY date_added desc";
    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    $query = "SELECT user_id as 'paid_users_count' from tbl_stripe_orders_logs tsol where order_status = 'paid'";
    $database->setQuery( $query);
    $paid_users_count = count($database->loadObjectList());

    $query = "SELECT  l1.user_id as 'leave_users_count' from tbl_stripe_orders_logs as l1
            left join  (select user_id,id from tbl_stripe_orders_logs where order_status = 'paid') as l2 on l2.user_id = l1.user_id
            where l1.order_status = 'canceled' and l2.id is null group by l1.user_id ";
    $database->setQuery( $query );
    $leave_users_count = count($database->loadObjectList());

    $query = "SELECT  l1.user_id as 'came_back_users_count' 
            from tbl_stripe_orders_logs as l1
            where l1.order_status = 'paid' and  l1.user_id = ANY
              (SELECT user_id
              FROM tbl_stripe_orders_logs
              WHERE order_status = 'canceled') ";
    $database->setQuery( $query );
    $came_back_users_count = count($database->loadObjectList());

    $query = "SELECT  l1.user_id as 'first_time_payed_users_count' from tbl_stripe_orders_logs as l1
            left join   tbl_stripe_orders_logs as l2 on l2.order_status = 'canceled' and l2.user_id = l1.user_id
            where l1.order_status = 'paid' and l2.id is null  ";
    $database->setQuery( $query );
    $first_time_payed_users_count = count($database->loadObjectList());

    HTML_StripeOrdersLogs::showStripeOrdersLogs( $rows, $pageNav, $option, $user_email,$order_id,$order_status,$transaction_id,$session_id,$paid_users_count,$leave_users_count,$came_back_users_count,$first_time_payed_users_count );
}


function showDetails( $id, $option ) {
    global $database;
    $row = false;
    $query = "SELECT * FROM `tbl_stripe_orders_logs` WHERE `id`=".$id;

    $database->setQuery($query);
    $database->loadObject($row);

    HTML_StripeOrdersLogs::showDetails( $row, $option );
}
?>
