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
    case 'download':
        showSurvey($option,true);
        break;
    default:
        showSurvey( $option,false );
        break;
}


//=================================================== LandingPages OPTION ===================================================
function showSurvey( $option,$download ) {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
    $filter_key 	= trim(mosGetParam( $_POST, "filter_key" ));
    $order_by 	= trim(mosGetParam( $_POST, "order_by" ));
    switch ($order_by){
        case '1':
            $orderBy='order by book_datetime DESC';
            break;
        case '2':
            $orderBy='order by book_datetime ASC';
            break;
        case '3';
            $orderBy='order by survey_date DESC';
            break;
        case '4';
            $orderBy='order by survey_date ASC';
            break;
        default:
            $orderBy='order by book_datetime DESC';
            break;
    }

    $where 	= "";
    $aWhere	= array();

    if( $filter_key ) {
        $aWhere[]	= " (o.order_id LIKE '%$filter_key%' OR  o.user_id LIKE '%$filter_key%') ";
    }

    if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);

    // get the total number of records
    $query = "SELECT COUNT(*) FROM tbl_survey_after_order $where";
    $database->setQuery( $query );
    $total = $database->loadResult();

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav( $total, $limitstart, $limit  );

    // get the subset (based on limits) of required records
    $query = "SELECT o.*,s.survey_page_open_datetime,s.email_open_datetime,s.date as survey_send_datetime FROM tbl_survey_after_order as o left join tbl_cron_survey_send as s on s.order_id=o.order_id and s.type='order'  $where $orderBy";
    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    $lists 	= array();
    $types	= array();
    $lists['filter_key']	= $filter_key;
    $lists['order_by']	= $order_by;
if($download){
    ob_clean();

    $data = "";

    $fields = array_keys(get_object_vars($rows[0]));

    $columns = count($fields);
    // Put the name of all fields to $out.
    for ($i = 0; $i < $columns; $i++) {
        $data .= $fields[$i]."\t";
    }
    $data .= "\n";
    for($k=0; $k < count( $rows ); $k++) {
        $row = $rows[$k];
        $line = '';

        foreach ($row as $value) {
            $value = strip_tags(str_replace('"', '""', $value));
            $line .= '"' . $value . '"' . "\t";
        }
        $data .= trim($line)."\n";
    }

    $data = str_replace("\r","",$data);

    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=jeports.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Lacation: excel.htm?id=yes");
    print $data ;
    die();
}else{

    HTML_Survey_after_order::showSurvey( $rows, $pageNav, $option, $lists );
}
}

?>