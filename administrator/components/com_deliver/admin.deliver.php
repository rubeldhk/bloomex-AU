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

//die($act);
switch ($act) {
	//=============================================================================================
        case 'unavailable_delivery':
            Switch ($task)
            {
                case 'new':
                    editUnavailableDelivery( '0', $option);
                break;

                case 'edit':
                    editUnavailableDelivery( intval( $cid[0] ), $option );
                break;

                case 'editA':
                    editUnavailableDelivery( $id, $option );
                break;

                case 'save':
                    saveUnavailableDelivery( $option );
                break;

                case 'remove':
                    removeUnavailableDelivery( $cid, $option );
                break;

                case 'cancel':
                    cancelUnavailableDelivery();
                break;
            
                default:
                    showUnavailableDelivery( $option );
            }
        break;
    



	case "driver_option":
		switch ($task) {
			case 'checkDriverLogin':
				checkDriverLogin();
				break;

			case 'new':
				editDriverOption('0', $option);
				break;

			case 'edit':
				editDriverOption(intval($cid[0]), $option);
				break;

			case 'editA':
				editDriverOption($id, $option);
				break;

			case 'save':
				saveDriverOption($option);
				break;

			case 'remove':
				removeDriverOption($cid, $option);
				break;

			case 'cancel':
				cancelDriverOption();
				break;

			default:
				showDriverOption($option);
				break;
		}
		break;

	//=============================================================================================
	case "free_shipping":
		switch ($task) {
			case 'new':
				editFreeShipping( '0', $option);
				break;

			case 'edit':
				editFreeShipping( intval( $cid[0] ), $option );
				break;

			case 'editA':
				editFreeShipping( $id, $option );
				break;

			case 'save':
				saveFreeShipping( $option );
				break;

			case 'remove':
				removeFreeShipping( $cid, $option );
				break;

			case 'cancel':
				cancelFreeShipping();
				break;

			default:
				showFreeShipping( $option );
				break;
		}
		break;

	//=============================================================================================
	case "postal_code":
		switch ($task) {
			case 'new':
				editPostalCode( '0', $option);
				break;

			case 'edit':
				editPostalCode( intval( $cid[0] ), $option );
				break;

			case 'editA':
				editPostalCode( $id, $option );
				break;

			case 'save':
				savePostalCode( $option );
				break;

			case 'remove':
				removePostalCode( $cid, $option );
				break;

			case 'publish':
				changePostalCode( $cid, 1, $option );
				break;

			case 'unpublish':
				changePostalCode( $cid, 0, $option );
				break;

			case 'orderup':
				orderPostalCode( intval( $cid[0] ), -1, $option );
				break;

			case 'orderdown':
				orderPostalCode( intval( $cid[0] ), 1, $option );
				break;

			case 'cancel':
				cancelPostalCode();
				break;

			default:
				showPostalCode( $option );
				break;
		}
		break;

	//=============================================================================================
	case "cut_off_time":
		switch ($task) {
			case 'save':
				saveCutOffTime( $option );
				break;

			default:
				editCutOffTime( $option );
				break;
		}
		break;
            

	//=============================================================================================
	default:
		switch ($task) {
			case 'create':
				create_item();
				break;
			case 'update_sundays':
				update_sundays();
				break;
			case 'delete':
				delete_item();
				break;
			default:
				showCalendar($option);
				break;
		}
		break;

}
//=================================================== Unavailable delivery OPTION ===================================================
function showUnavailableDelivery($option)
{
    global $database, $mainframe, $mosConfig_list_limit;
    
    $limit = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
    $limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
    
    $query = "SELECT COUNT(*) FROM tbl_unavailable_delivery";
    $database->setQuery( $query );
    $total = $database->loadResult();

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav( $total, $limitstart, $limit  );

    // get the subset (based on limits) of required records
    $query = "SELECT * FROM tbl_unavailable_delivery ORDER BY id DESC";
    $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
    $rows = $database->loadObjectList();

    HTML_Deliver::showUnavailableDelivery( $rows, $pageNav, $option );
    
}

function editUnavailableDelivery( $id, $option ) 
{
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosUnavailableDelivery( $database );
	// load the row from the db table
	$row->load( (int)$id );


    $query="SELECT postal_code,concat(postal_code,'  (',province,')') as postal_code_name FROM `jos_postcode_warehouse` where province!='' and postal_code!='' group by postal_code order by province";
    $database->setQuery( $query );
    $postalCodesList = $database->loadObjectList();

    $query="SELECT city,concat(city,'  (',province,')') as city_name FROM `jos_postcode_warehouse` where province!='' and city!='' group by city  order by province";
    $database->setQuery( $query );
    $citiesList = $database->loadObjectList();



    $states = $cities = $postalCodes = [];
    $jsonData=json_decode(html_entity_decode($row->json_data));
    if($jsonData){
    	if($jsonData->states){
            $states=$jsonData->states;
		}

        if($jsonData->cities){
            $cities=$jsonData->cities;
        }

        if($jsonData->postalCodes){
            $postalCodes=$jsonData->postalCodes;
        }
	}

	HTML_Deliver::editUnavailableDelivery( $row, $option,$postalCodesList,$citiesList,$states,$cities,$postalCodes);
}

function cancelUnavailableDelivery() 
{
    mosRedirect('index2.php?option=com_deliver&act=unavailable_delivery');
}

function saveUnavailableDelivery( $option ) 
{
    global $database, $mosConfig_absolute_path, $act;

    $row = new mosUnavailableDelivery( $database );
    
    if (!$row->bind( $_POST )) 
    {
            echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
            exit();
    }

    $dateRange = explode(' - ',$_POST['dateRange']);

    if($dateRange){
    	$row->available_from_date=$dateRange[0];
    	$row->available_until_date=$dateRange[1];
	}

    $states =  mosGetParam( $_POST, "states", "");
    $cities = mosGetParam( $_POST, "cities", "");
    $postalCodes = mosGetParam( $_POST, "postalCodes", "");
    $row->json_data=json_encode(['states'=>$states,'cities'=>$cities,'postalCodes'=>$postalCodes]);

    $row->description = mosGetParam( $_POST, "description", "");
    
    if (!$row->store()) 
    {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }

    mosRedirect( "index2.php?option=$option&act=$act", "Save Unavailable Delivery Option Successfully" );
}

function removeUnavailableDelivery( &$cid, $option ) 
{
    global $database, $act, $mosConfig_absolute_path;

    if (count( $cid )) 
    {
        foreach ($cid as $value) 
        {
            $query = "DELETE FROM tbl_unavailable_delivery WHERE id = $value";
            $database->setQuery( $query );
            if (!$database->query()) 
            {
                echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
            }
        }
    }

    mosRedirect( "index2.php?option=$option&act=$act", "Remove Unavailable Delivery Option Successfully" );
}

//===
//
//=================================================== SHIPPING SURCHARGE OPTION ===================================================
function showShippingSurcharge( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_shipping_surcharge";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_shipping_surcharge ORDER BY date ASC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_Deliver::showShippingSurcharge( $rows, $pageNav, $option );
}


function editShippingSurcharge( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosShippingSurcharge( $database );
	// load the row from the db table
	$row->load( (int)$id );

	HTML_Deliver::editShippingSurcharge( $row, $option);
}


function saveShippingSurcharge( $option ) {
	global $database, $mosConfig_absolute_path, $act;


	$row = new mosShippingSurcharge( $database );

	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$date		= trim(mosGetParam( $_POST, "date", "" ));
	$aDate		= explode("-", $date);
	$row->date	= $aDate[2]."-".$aDate[0]."-".$aDate[1];
	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}




	mosRedirect( "index2.php?option=$option&act=$act", "Save Free Shipping Option Successfully" );
}


function removeShippingSurcharge( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;

	if (count( $cid )) {
		foreach ($cid as $value) {
			$query = "DELETE FROM tbl_shipping_surcharge WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect( "index2.php?option=$option&act=$act", "Remove Free Shipping Option Successfully" );
}


function cancelShippingSurcharge() {
	mosRedirect('index2.php?option=com_deliver&act=free_shipping');
}


//=================================================== FREE SHIPPING OPTION ===================================================
function showFreeShipping( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_freeshipping";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_freeshipping ORDER BY id";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_Deliver::showFreeShipping( $rows, $pageNav, $option );
}


function editFreeShipping( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosFreeShipping( $database );
	// load the row from the db table
	$row->load( (int)$id );

	HTML_Deliver::editFreeShipping( $row, $option);
}


function saveFreeShipping( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosFreeShipping( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$freedate		= trim(mosGetParam( $_POST, "freedate", "" ));
	$aFreedate		= explode("-", $freedate);
	$row->freedate	= strtotime($aFreedate[2]."-".$aFreedate[0]."-".$aFreedate[1]);

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	mosRedirect( "index2.php?option=$option&act=$act", "Save Free Shipping Option Successfully" );
}


function removeFreeShipping( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;

	if (count( $cid )) {
		foreach ($cid as $value) {
			$query = "DELETE FROM tbl_freeshipping WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect( "index2.php?option=$option&act=$act", "Remove Free Shipping Option Successfully" );
}


function cancelFreeShipping() {
	mosRedirect('index2.php?option=com_deliver&act=free_shipping');
}


//=================================================== DRIVER OPTION ===================================================


function showDriverOption($option) {
	global $database, $mainframe, $mosConfig_list_limit, $my;

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
	$filter = $database->getEscaped($mainframe->getUserStateFromRequest("view{$option}filter", 'filter', ''));
	$warehouse_filter = $database->getEscaped($mainframe->getUserStateFromRequest("view{$option}warehouse_filter", 'warehouse_filter', ''));

	$where = "WHERE 1=1 ";
	$where_total = "WHERE 1=1";

	if ($my->gid != 25) {
		if (isset($my->routes_warehouses)) {
			$where .= " AND DO.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ")";
			$where_total .= " AND DO.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ")";
		}
	}


	if ($filter) {
		$where .= " AND (DO.driver_name like '%" . $filter . "%' OR DO.service_name like '%" . $filter . "%' OR DO.login  like '%" . $filter . "%' OR DO.id  like '" . $filter . "' ) ";
		$where_total .= " AND (driver_name like '%" . $filter . "%' OR service_name like '%" . $filter . "%' OR description like '%" . $filter . "%' OR login like '%" . $filter . "%')";
	}

	if ($warehouse_filter) {
		$where .= " AND (`DO`.`warehouse_id`=" . $warehouse_filter . ")";
		$where_total .= " AND (`warehouse_id`=" . $warehouse_filter . ")";
	}

	// jos_vm_warehouse.vendor_id field act as "published"
	$query = "SELECT * FROM jos_vm_warehouse WHERE vendor_id = 1 ORDER BY warehouse_name";
	$database->setQuery($query);
	$aWareHouse = $database->loadObjectList();

	//  need to add "empty" warehouse first so it go as null when we check filter. - defaults to first warehouse on list otherwise
	$warehouse_dummy = (object) ['warehouse_id' => '', 'warehouse_name' => 'All Warehouses'];
	$warehouses = array_merge([$warehouse_dummy], $aWareHouse);

	$warehousesList = mosHTML::selectList($warehouses, 'warehouse_filter', 'size="1" onchange="document.forms[0].submit()" ', 'warehouse_id', 'warehouse_name', $warehouse_filter);

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_driver_option $where_total";
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	// get the subset (based on limits) of required records
	$query = "SELECT DO.*, VMW.warehouse_name,US.username FROM tbl_driver_option AS DO
		LEFT JOIN  jos_vm_warehouse AS VMW ON DO.warehouse_id = VMW.warehouse_id
		LEFT JOIN jos_users as US ON US.id=DO.created_by $where ORDER BY VMW.warehouse_name";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();



	HTML_Deliver::showDriverOption($rows, $pageNav, $option, $filter, $warehousesList);
}

function editDriverOption($id, $option) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosDriverOption($database);
	// load the row from the db table
	$row->load((int) $id);

	$aList = array();
	$aDriversOptions = array();
	$types = array();
	$types[] = mosHTML::makeOption("Bloomex Driver", "Bloomex Driver");
	$types[] = mosHTML::makeOption("Local Driver", "Local Driver");
	$types[] = mosHTML::makeOption("Courier", "Courier");
	$types[] = mosHTML::makeOption("Seasoned Driver", "Seasoned Driver");
	$aList['driver_option_type'] = mosHTML::selectList($types, 'driver_option_type', 'class="inputbox" size="1" onchange="checkOption(this.value);"', 'value', 'text', $row->driver_option_type);

	$query = "SELECT * FROM jos_vm_warehouse ORDER BY warehouse_name";
	$database->setQuery($query);
	$aWareHouse = $database->loadObjectList();
	$aList['warehouse_id'] = mosHTML::selectList($aWareHouse, 'warehouse_id', 'size="1" style="float:left;"', 'warehouse_id', 'warehouse_name', $row->warehouse_id);

	$query = "SELECT 
            `x`.`id`,
            `r`.`id_rate`,
            `r`.`name`,
            `r`.`rate`,
            `r`.`rate_driver`,
            IFNULL(`x`.`rate`, `r`.`rate`) AS `driver_rate`
        FROM `jos_driver_rates` AS `r`
        LEFT JOIN `jos_driver_rate_xref` AS `x`
            ON
            `x`.`id_driver`=" . $row->id . "
            AND
            `x`.`id_rate`=`r`.`id_rate`
        WHERE
            `r`.`warehouse_id`=" . $row->warehouse_id . "
        GROUP BY 
            `r`.`id_rate`
        ORDER BY 
            `r`.`orderby` asc
        ";

	$database->setQuery($query);
	$rates_obj = $database->loadObjectList();

	HTML_Deliver::editDriverOption($row, $option, $aList, $rates_obj);
}

function saveDriverOption($option) {
	global $database, $mosConfig_absolute_path, $act, $my;
	/*
      if ($my->usertype != 'Super Administrator' && $my->id != trim(mosGetParam($_POST, "created_by", ""))) {
      mosRedirect("index2.php?option=$option&act=$act", "You Don't Have permision to update this item");
      }
     */
	$row = new mosDriverOption($database);

	if ($my->gid != 25) {
		if (isset($my->routes_warehouses)) {
			if (!in_array($_POST['warehouse_id'], $my->routes_warehouses)) {
				mosRedirect("index2.php?option=$option&act=$act", "You Don't Have permision to update this item");
			}
		}
	}
	if ($_POST['driver_option_type'] == 'Bloomex Driver') {
		$uniques = array(
			'driver_name' => 'Driver Name',
			'login' => 'Driver Login',
			'service_name' => 'Service Name'
		);

		foreach ($uniques as $field_name => $title) {
			$driver = false;

			$query = "SELECT 
                `d`.`id`
            FROM `tbl_driver_option` AS `d`
            WHERE
                `d`.`" . $field_name . "`='" . $database->getEscaped($_POST[$field_name]) . "'
                AND
                `d`.`id`!=" . (int) $_POST['id'] . "
            ";
			$database->setQuery($query);
			$database->loadObject($driver);
			if ($driver) {
				echo '<script>alert("' . $title . ' Should be unique");window.history.go(-1);</script>';
				exit();
			}
		}
	}


	if (!empty($_POST['password']) AND mb_strlen($_POST['password']) < 5) {
		echo '<script>alert("Password Cannot be less than 5 characters");window.history.go(-1);</script>';
		exit();
	}
	if (!empty($_POST['login']) AND mb_strlen($_POST['login']) < 5) {
		echo '<script>alert("Login Cannot be less than 5 characters");window.history.go(-1);</script>';
		exit();
	}

	if (!$row->bind($_POST)) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if (empty($_POST['description'])) {
		$description = "Service_name_and_telephone_number[--1--]$row->service_name.$row->number";
		$row->description = $description;
	}

	if ($row->password) {
		$row->password = md5(trim(mosGetParam($_POST, "password", "")));
	} else {
		$row->password = trim(mosGetParam($_POST, "old_password", ""));
	}

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	foreach ($_POST['rate'] as $rateXrefId => $rateXrefValue) {
		if ($rateXrefId) {
			$queryRate = "INSERT INTO jos_driver_rate_xref (id_driver,id_rate,rate) VALUES ({$_POST['id']},$rateXrefId,{$database->getEscaped($rateXrefValue)}) 
            ON DUPLICATE KEY UPDATE  `rate`={$database->getEscaped($rateXrefValue)} ";
			$database->setQuery($queryRate);
			$database->query();
		}
	}
	mosRedirect("index2.php?option=$option&act=$act", "Save Driver Option Successfully");
}

function removeDriverOption(&$cid, $option) {
	global $database, $act, $mosConfig_absolute_path;

	if (count($cid)) {
		foreach ($cid as $value) {
			$query = "DELETE FROM tbl_driver_option WHERE id = $value";
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect("index2.php?option=$option&act=$act", "Remove Driver Option Successfully");
}

function cancelDriverOption() {
	mosRedirect('index2.php?option=com_deliver&act=driver_option');
}

//=================================================== POSTAL CODE OPTION ===================================================
function showPostalCode( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_options WHERE type='postal_code'";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_options WHERE type='postal_code' ORDER BY ordering";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_Deliver::showpPostalCode( $rows, $pageNav, $option );
}


function editPostalCode( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosDeliver( $database );
	// load the row from the db table
	$row->load( (int)$id );

	$aOptions 			= explode( "[--1--]", $row->options );
	$undeliver			= $aOptions[3];

	if (!$id) {
		$row->ordering 	= 0;
		$row->published = 1;
		$undeliver		= 1;
	}

	if( $undeliver == null ) $undeliver		= 1;


	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );
	$lists['undeliver']	= mosHTML::yesnoRadioList( "undeliver", "", $undeliver, "No", "Yes" );

	HTML_Deliver::editPostalCode( $row, $option, $lists );
}


function savePostalCode( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosDeliver( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$location_name	= mosGetParam( $_POST, "location_name", "" );
	$deliver_day	= mosGetParam( $_POST, "deliver_day", "" );
	$price			= mosGetParam( $_POST, "price", "" );
	$undeliver		= mosGetParam( $_POST, "undeliver", "" );

	$row->type 		= "postal_code";
	$row->options 	= $location_name."[--1--]".$deliver_day."[--1--]".$price."[--1--]".$undeliver;

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->updateOrder( "type='postal_code'" );

	mosRedirect( "index2.php?option=$option&act=$act", "Save Postal Code Successfully" );
}


function removePostalCode( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;

	if (count( $cid )) {
		foreach ($cid as $value) {
			$query = "DELETE FROM tbl_options WHERE id = $value AND type='postal_code'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect( "index2.php?option=$option&act=$act", "Remove Postal Code Successfully" );
}


function changePostalCode( $cid=null, $state=0, $option ) {
	global $database, $my, $act;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $state ? 'publish' : 'unpublish';
		mosErrorAlert( "Select an item to $action" );
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE tbl_options SET published = " . (int) $state . " WHERE ( $cids )";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( $state ) {
		$msg	= "Publish Postal Code Successfully";
	}else {
		$msg	= "UnPublish Postal Code Successfully";
	}

	mosRedirect( "index2.php?option=$option&act=$act", $msg );
}


function orderPostalCode( $uid, $inc, $option ) {
	global $database, $act;

	$row = new mosDeliver( $database );
	$row->load( (int)$uid );
	$row->updateOrder();
	$row->move( $inc, "published >= 0 AND `type` = 'postal_code' " );
	$row->updateOrder();

	mosRedirect( "index2.php?option=$option&act=$act", "Reorder Postal Code Successfully" );
}


function cancelPostalCode() {
	mosRedirect('index2.php?option=com_deliver&act=postal_code');
}


//=================================================== DELIVER OPTION ===================================================
function update_sundays() {
	global $database;
	function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
	{
		$startDate = strtotime($startDate);
		$endDate = strtotime($endDate);

		$dateArr = array();

		do
		{
			if(date("w", $startDate) != $weekdayNumber)
			{
				$startDate += (24 * 3600); // add 1 day
			}
		} while(date("w", $startDate) != $weekdayNumber);


		while($startDate <= $endDate)
		{
			$dateArr[] = date('Y-m-d', $startDate);
			$startDate += (7 * 24 * 3600); // add 7 days
		}

		return($dateArr);
	}
	$dateArr = getDateForSpecificDayBetweenDates('2017-01-01', '2100-12-31', 0);
	$ins = '';
	foreach($dateArr as $d){
		$query = "SELECT * FROM tbl_delivery_options where calendar_day ='".$d."'";
		$database->setQuery( $query );
		$num = $database->loadResult();
		if(!$num){
			$ins.="('".$d."','unavaliable','0','Sunday'),";
		}
	}
	$ins = rtrim($ins,',');
	$option	= mosGetParam( $_REQUEST, "option" );
	if($ins==''){
		$msg = 'There are not new items to add';
		exit($msg);
	}
	$query = "INSERT INTO tbl_delivery_options (calendar_day,type,price,name) VALUES $ins ";
	$database->setQuery( $query );
	if (!$database->query()) {
		$msg = "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>";
	}else{
		$msg = 'success';
	}
	exit($msg);

}
function get_events(){
	global $database;
	$query = "SELECT * FROM tbl_delivery_options ORDER BY id ASC";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$events = '[';
	foreach($rows as $r){
		$date_php = date("Y,m,d", strtotime($r->calendar_day));
		$date_php_arr = explode(",",$date_php);
		$date_php_arr[1] =$date_php_arr[1]-1;
		$date_java = implode($date_php_arr,',');
		$date = "new Date(".$date_java.")";
		if($r->price!=0){
			$price="( ".$r->price.'$ )';
		}else{
			$price='';
		}

		$events.='{"title":"'.$r->name.'","html": "'.$r->type.' Delivery '.$price.'","datetime": '.$date.',"classname":"'.$r->type.'","price":"'.$r->price.'","id":"'.$r->id.'"},';

	}
	$events = rtrim($events,',');
	$events .= ']';
	return $events;
}
function showCalendar( $option ) {
	$events =  get_events();
	HTML_Deliver::showCalendar( $events, $option );
}
function delete_item(){
	global $database;
	$id	= mosGetParam( $_REQUEST, "id" );
	$query = "DELETE FROM tbl_delivery_options where id ='".$id."'";
	$database->setQuery( $query );
	if (!$database->query()) {
		$msg = "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>";
		die($msg);
	}else{
		$msg = 'success';
	}
	exit($msg);
}
function create_item(){
	global $database;
	$option	= mosGetParam( $_REQUEST, "option" );
	$name	= $database->getEscaped(mosGetParam( $_REQUEST, "name" ));
	$type	= $database->getEscaped(strtolower(mosGetParam( $_REQUEST, "type" )));
	$price	= $database->getEscaped(mosGetParam( $_REQUEST, "price" ));
	$date_str	= $database->getEscaped(mosGetParam( $_REQUEST, "date" ));

	$date_arr = explode(",", $date_str);
	foreach($date_arr as $date){
		$query = "INSERT INTO tbl_delivery_options (calendar_day,type,price,name) VALUES ('".$date."','".$type."','".$price."','".$name."')";
		$database->setQuery( $query );
		if (!$database->query()) {
			$msg = "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>";
			die($msg);
		}else{
			$msg = 'Item Addedd Successfully';
		}
	}

	mosRedirect( "index2.php?option=$option", $msg );


}

//=================================================== SPECIAL DELIVER OPTION ===================================================
function showSpecialDeliver( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_years 	= intval( $mainframe->getUserStateFromRequest( "view{$option}filter_years", 'filter_years', 0 ) );

	$where = "";
	/*if( $filter_years > 0  ) {
		$where	= " AND name LIKE '%/$filter_years' ";
	}*/

	// get the total number of records
	$query = "SELECT COUNT(*) FROM tbl_options WHERE type='special_deliver' $where";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT * FROM tbl_options WHERE type='special_deliver' $where ORDER BY id DESC";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_Deliver::showSpecialDeliver( $rows, $pageNav, $option );
}


function editSpecialDeliver( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosDeliver( $database );
	// load the row from the db table
	$row->load( (int)$id );

	if (!$id) {
		$row->ordering 	= 0;
		$row->published = 1;
	}

	$lists = array();
	$lists['publish']	= mosHTML::yesnoRadioList( "published", "", $row->published );

	HTML_Deliver::editSpecialDeliver( $row, $option, $lists );
}


function saveSpecialDeliver( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosDeliver( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$days 	= mosGetParam( $_POST, "days" );
	$months = mosGetParam( $_POST, "months" );
	/*$years 	= mosGetParam( $_POST, "years" );*/

	$row->type = "special_deliver";
	$row->name = "$months/$days";
	//$row->name = "$months/$days/$years";

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->updateOrder( "type='special_deliver'" );

	mosRedirect( "index2.php?option=$option&act=$act", "Save Special Deliver Option Successfully" );
}


function removeSpecialDeliver( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;

	if (count( $cid )) {
		foreach ($cid as $value) {
			$query = "DELETE FROM tbl_options WHERE id = $value AND type='special_deliver'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect( "index2.php?option=$option&act=$act", "Remove Special Deliver Option Successfully" );
}


function changeSpecialDeliver( $cid=null, $state=0, $option ) {
	global $database, $my, $act;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $state ? 'publish' : 'unpublish';
		mosErrorAlert( "Select an item to $action" );
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE tbl_options SET published = " . (int) $state . " WHERE ( $cids )";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( $state ) {
		$msg	= "Publish Special Deliver Option Successfully";
	}else {
		$msg	= "UnPublish Special Deliver Option Successfully";
	}

	mosRedirect( "index2.php?option=$option&act=$act", $msg );
}


function orderSpecialDeliver( $uid, $inc, $option ) {
	global $database, $act;

	$row = new mosDeliver( $database );
	$row->load( (int)$uid );
	$row->updateOrder();
	$row->move( $inc, "published >= 0 AND `type` = 'special_deliver' " );
	$row->updateOrder();

	mosRedirect( "index2.php?option=$option&act=$act", "Reorder Special Deliver Option Successfully" );
}


function cancelSpecialDeliver() {
	mosRedirect('index2.php?option=com_deliver&act=special_deliver');
}


//=================================================== CUT OFF TIME CONFIGURATION ===================================================
function editCutOffTime( $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$query = "SELECT * FROM tbl_options WHERE type='cut_off_time'";
	$database->setQuery( $query );
	$row = $database->loadRow();

	HTML_Deliver::editCutOffTime( $row, $option );
}


function saveCutOffTime( $option ) {
	global $database, $mosConfig_absolute_path, $act;

	$row = new mosDeliver( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$hours 			= mosGetParam( $_POST, "hours", "" );
	$minutes 		= mosGetParam( $_POST, "minutes", "" );
	$deliver_fee	= mosGetParam( $_POST, "deliver_fee", "" );
	$row->options	= $hours."[--1--]".$minutes."[--1--]".$deliver_fee;
	$row->type		= "cut_off_time";
	$row->name		= "Cut Off Time Configuration";

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	mosRedirect( "index2.php?option=$option&act=$act", "Cut Off Time Configuration and Deliver Extra Fee successfully" );
}

//========================================================================================
function do_upload( $file, $dest_dir ) {
	global $clearUploads;

	if( $act ) {
		$act	= "&act=$act";
	}

	$format = substr( $file['name'], -3 );

	$allowable = array (
		//'bmp',
		//'csv',
		//'doc',
		//'epg',
		'gif',
		//'ico',
		'jpg',
		'jpeg',
		//'odg',
		//'odp',
		//'ods',
		//'odt',
		//'pdf',
		'png',
		//'ppt',
		//'swf',
		//'txt',
		//'xcf',
		//'xls'
	);

    $noMatch = 0;
	foreach( $allowable as $ext ) {
		if ( strcasecmp( $format, $ext ) == 0 ) {
			$noMatch = 1;
		}
	}

    if(!$noMatch){
		return false;
    }

    $sFileName	= strtolower(time().".$format");
	if ( !move_uploaded_file($file['tmp_name'], $dest_dir.$sFileName) ){
		return false;
	}

	$clearUploads = true;
	return $sFileName;
}

?>
