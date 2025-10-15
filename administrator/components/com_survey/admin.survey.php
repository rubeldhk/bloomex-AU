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


switch ($task) {

    default:
        showSurvey( $option );
        break;
}






//=================================================== LandingPages OPTION ===================================================
function showSurvey( $option ) {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
    $filter_key 	= trim(mosGetParam( $_POST, "filter_key" ));

    $where 	= "";
    $aWhere	= array();

    if( $filter_key ) {
        $aWhere[]	= " (order_id LIKE '%$filter_key%' OR  user_id LIKE '%$filter_key%') ";
    }

    if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);

    // get the total number of records
    $query = "SELECT COUNT(*) FROM tbl_survey $where";
    $database->setQuery( $query );
    $total = $database->loadResult();

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav( $total, $limitstart, $limit  );

    // get the subset (based on limits) of required records
    $query = "SELECT * FROM tbl_survey  $where ORDER BY survey_date ASC";
    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    $lists 	= array();
    $types	= array();
    $lists['filter_key']	= $filter_key;

    HTML_Survey::showSurvey( $rows, $pageNav, $option, $lists );
}

?>