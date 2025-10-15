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

	default:
		switch ($task) {
                        case 'getCsv':
                            getCsv();
                        break;
                        case 'parseCsv':
                            parseCsv();
                        break;
                        case 'getFile':
                            getFile();
                        break;
                        case 'parseXlxs':
                            parseXlxs();
                        break;
			case 'new':
				editAssignOrder( '0', $option);
				break;
		
			case 'edit':
				editAssignOrder( intval( $cid[0] ), $option );
				break;
		
			case 'editA':
				editAssignOrder( $id, $option );
				break;
		
			case 'save':
				saveAssignOrder( $option );
				break;
		
			case 'remove':
				removeAssignOrder( $cid, $option );
				break;		
				
			case 'cancel':
				cancelAssignOrder();
				break;
            case 'deliverable':
                changePostalCode( $cid, 1, $option );
                break;

            case 'undeliverable':
                changePostalCode( $cid, 0, $option );
                break;
			//default:
            case 'publish':
            Publish_UnpublshPostalCode( $cid, 1, $option );
                break;
            case 'out_of_rown':
                changeOutOfTown($cid, 1, $option);
                break;
            case 'not_out_of_town':
                changeOutOfTown($cid, 0, $option);
                break;
            case 'block_shipstation':
                changeBlockShipstation($cid, 1, $option);
                break;
            case 'unblock_shipstation':
                changeBlockShipstation($cid, 0, $option);
                break;
            case 'unpublish':
                Publish_UnpublshPostalCode( $cid, 0, $option );
                break;
            default:
				showAssignOrder( $option );
				break;
		}
		break;
	
}
function changeBlockShipstation($cid = null, $state = 0, $option) {
    global $database, $my, $act;

    if (!is_array($cid) || count($cid) < 1) {
        $action = $state ? 'block shipstation' : 'unblock shipstation';
        mosErrorAlert("Select an item to $action");
    }

    mosArrayToInts($cid);
    $cids = 'id=' . implode(' OR id=', $cid);

    $query = "UPDATE jos_postcode_warehouse SET block_shipstation = " . (int) $state . " WHERE ( $cids )";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if ($state) {
        $msg = "Postal Code Blocked Successfully";
    } else {
        $msg = "Postal Code UnBlocked Successfully";
    }

    mosRedirect("index2.php?option=$option&act=$act", $msg);
}

function changeOutOfTown($cid = null, $state = 0, $option) {
    global $database, $my, $act;

    if (!is_array($cid) || count($cid) < 1) {
        $action = $state ? 'deliverable' : 'undeliverable';
        mosErrorAlert("Select an item to $action");
    }

    mosArrayToInts($cid);
    $cids = 'id=' . implode(' OR id=', $cid);

    $query = "UPDATE jos_postcode_warehouse SET out_of_town = " . (int) $state . " WHERE ( $cids )";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if ($state) {
        $msg = "Deliverable Postal Code Successfully";
    } else {
        $msg = "Undeliverable Postal Code Successfully";
    }

    mosRedirect("index2.php?option=$option&act=$act", $msg);
}

function getCsv() {
    global $database;
    
    $query = "SELECT
        `pw`.*,
        IFNULL(`w`.`warehouse_name`, 'NOWAREHOUSEASSIGNED') AS `warehouse_name`,
        IFNULL(`smw`.`warehouse_name`, 'NOWAREHOUSEASSIGNED') AS `sameday_warehouse_name`
    FROM `jos_postcode_warehouse` AS `pw`
    LEFT JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_id`=`pw`.`warehouse_id`
    LEFT JOIN `jos_vm_warehouse` AS `smw` ON `smw`.`warehouse_id`=`pw`.`same_day_warehouse_id`
    ORDER BY `pw`.`postal_code` ASC";

    ob_end_clean();
    
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename=postalcodes.csv');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: no-cache');
    
    $fp = fopen('php://output', 'w');
    
    $csv[0] = array(
        'Province',
        'City',
        'FSA (Forward Sortation Area)', 
        'Warehouse', 
        'Same Day Warehouse',
        'Days in Route', 
        'Deliverable', 
        'Published',
        'Out of Town',
        'Additional Delivery Surcharge',
        'Country',
        'Block Shipstation',
    );
    fputcsv($fp, $csv[0]);
    
    $i = 1;
    $database->setQuery($query);
    $postalcodes_obj = $database->loadObjectList();
        
    foreach ($postalcodes_obj as $postalcode_obj) {
        $csv[$i] = array(
            $postalcode_obj->province,
            $postalcode_obj->city,
            '\''.$postalcode_obj->postal_code.'\'',
            $postalcode_obj->warehouse_name,
            $postalcode_obj->sameday_warehouse_name,
            $postalcode_obj->days_in_route,
            $postalcode_obj->deliverable,
            $postalcode_obj->published,
            $postalcode_obj->out_of_town,
            $postalcode_obj->additional_delivery_fee,
            $postalcode_obj->country,
            $postalcode_obj->block_shipstation
        );
        fputcsv($fp, $csv[$i]);
        
        $i++;
    }
    fclose($fp);
    
    die;
}

function parseCsv() {
    global $database;
    
    $return = array();
    $return['result'] = false;

    $tmp_name = $_FILES['file']['tmp_name'];
    
    $csv = array_map('str_getcsv', file($_FILES['file']['tmp_name']));
    
    $warehouses = $inserts = $removesAUS = $removesNZL = array();

    $query = "SELECT 
        `w`.`warehouse_name`,
        `w`.`warehouse_id`
    FROM `jos_vm_warehouse` AS `w`";

    $database->setQuery($query);
    $warehouses_obj = $database->loadObjectList();

    foreach ($warehouses_obj as $warehouse_obj) {
        $warehouses[strtolower($warehouse_obj->warehouse_name)] = $warehouse_obj->warehouse_id;
    }

    unset($csv[0]);

    foreach ($csv as $line) {
        $line = array_map('trim', $line);

        $deliverable = (empty($line[6]) OR $line[6] == '0') ? 0 : 1;
        $published = (empty($line[7]) OR $line[7] == '0') ? 0 : 1;
        $warehouse_id = (array_key_exists(strtolower($line[3]), $warehouses)) ? $warehouses[strtolower($line[3])] : 0;
        $sameday_warehouse_id = (array_key_exists(strtolower($line[4]), $warehouses)) ? $warehouses[strtolower($line[4])] : 0;
        $postal_code = preg_replace('/([^\d]+)/siu', '', $line[2]);
        $out_of_town = (empty($line[8]) OR $line[8] == '0') ? 0 : 1;
        $block_shipstation = (empty($line[11]) OR $line[11] == '0') ? 0 : 1;
        if($line[10] == 'AUS') {
            $removesAUS[] = "'".$database->getEscaped($postal_code)."'";
        } elseif ($line[10] == 'NZL') {
            $removesNZL[] = "'".$database->getEscaped($postal_code)."'";
        }

        $inserts[] = "(
            '".$database->getEscaped($line[0])."',
            '".$database->getEscaped($line[1])."',
            '".$database->getEscaped($postal_code)."',
            '".$warehouse_id."',
            '".$sameday_warehouse_id."',
            '".(int)$line[5]."',
            '".$deliverable."',
            '".$published."',
            '".$out_of_town."',
            '".$block_shipstation."',
            '".(int)$line[9]."',
            '".$database->getEscaped($line[10])."'
        )";
    }
    
    if (sizeof($inserts) > 0) {
        $return['result'] = true;
        $return['inserts'] = $inserts;
        $return['sizeof_inserts'] = sizeof($inserts);

        $query = "DELETE FROM 
        `jos_postcode_warehouse` 
        WHERE (`postal_code` IN (".implode(',', $removesAUS).") AND country='AUS')";
        $database->setQuery($query);
        $database->query();

        $query = "DELETE FROM 
        `jos_postcode_warehouse` 
        WHERE (`postal_code` IN (".implode(',', $removesNZL).") AND country='NZL')";
        $database->setQuery($query);
        $database->query();



        $query = "INSERT INTO 
        `jos_postcode_warehouse` 
        (
            `province`,
            `city`,
            `postal_code`,
            `warehouse_id`,
            `same_day_warehouse_id`,
            `days_in_route`,
            `deliverable`,
            `published`,
            `out_of_town`,
            `block_shipstation`,
            `additional_delivery_fee`,
            `country`
        )
        VALUES ".implode(',', $inserts)."";

        $database->setQuery($query);
        $database->query();
    }
    else {
        $return['error'] = 'Incorrect Text File.';
    }

    echo json_encode($return);
    die;
}

function getFile() {
    global $database;
    
    $query = "SELECT
        `pw`.*,
        IFNULL(`w`.`warehouse_name`, 'NOWAREHOUSEASSIGNED') AS `warehouse_name`,
        IFNULL(`smw`.`warehouse_name`, 'NOWAREHOUSEASSIGNED') AS `sameday_warehouse_name`
    FROM `jos_postcode_warehouse` AS `pw`
    LEFT JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_id`=`pw`.`warehouse_id`
    LEFT JOIN `jos_vm_warehouse` AS `smw` ON `smw`.`warehouse_id`=`pw`.`same_day_warehouse_id`
    ORDER BY `pw`.`postal_code` ASC";
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
    
    $phpexcel = new PHPExcel(); 
    $page = $phpexcel->setActiveSheetIndex(0);

    $page->setCellValue('A1', 'Rec. Province');
    $page->setCellValue('B1', 'Rec. City');
    $page->setCellValue('C1', 'FSA (Forward Sortation Area)');
    $page->setCellValue('D1', 'Warehouse');
    $page->setCellValue('E1', 'SameDay Warehouse');
    $page->setCellValue('F1', 'Days in Route');
    $page->setCellValue('G1', 'Deliverable');
    $page->setCellValue('H1', 'Published');
    $page->setCellValue('I1', 'Out of Town');
    $page->setCellValue('J1', 'Additional Delivery Surcharge');
    $page->setCellValue('K1', 'Country');
    $page->setCellValue('L1', 'Block Shipstation');

    $i = 2;

    $database->setQuery($query);
    $postalcodes_obj = $database->loadObjectList();
        
    foreach ($postalcodes_obj as $postalcode_obj) {
        $page->setCellValue('A'.$i, $postalcode_obj->province);
        $page->setCellValue('B'.$i, $postalcode_obj->city);
        $page->setCellValue('C'.$i, $postalcode_obj->postal_code);
        $page->setCellValue('D'.$i, $postalcode_obj->warehouse_name);
        $page->setCellValue('E'.$i, $postalcode_obj->sameday_warehouse_name);
        $page->setCellValue('F'.$i, $postalcode_obj->days_in_route);
        $page->setCellValue('G'.$i, $postalcode_obj->deliverable);
        $page->setCellValue('H'.$i, $postalcode_obj->published);
        $page->setCellValue('I' . $i, $postalcode_obj->out_of_town);
        $page->setCellValue('J' . $i, $postalcode_obj->additional_delivery_fee);
        $page->setCellValue('K' . $i, $postalcode_obj->country);
        $page->setCellValue('L' . $i, $postalcode_obj->block_shipstation);

        $i++;
    }
    
    $page->setTitle('postal_codes');

    ob_end_clean();

    $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');                                                                                                           
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=postalcodes.xlsx');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $objWriter->save('php://output');
    die;
}

function parseXlxs() {
    global $database;
    
    $return = array();
    $return['result'] = false;

    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
    
    $tmp_name = $_FILES['file']['tmp_name'];
    $inputFileType = PHPExcel_IOFactory::identify($tmp_name); 
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($tmp_name);
    $sheet = $objPHPExcel->getActiveSheet()->toArray(); 
    
    unset($sheet[0]);
    
    if (is_array($sheet)) {
        $warehouses = $inserts = $removes = array();
        
        $query = "SELECT 
            `w`.`warehouse_name`,
            `w`.`warehouse_id`
        FROM `jos_vm_warehouse` AS `w`";
        
        $database->setQuery($query);
        $warehouses_obj = $database->loadObjectList();
        
        foreach ($warehouses_obj as $warehouse_obj) {
            $warehouses[$warehouse_obj->warehouse_name] = $warehouse_obj->warehouse_id;
        }
        
        foreach ($sheet as $line) {
            $line = array_map('trim', $line);
            
            $deliverable = (empty($line[6]) OR $line[6] == '0') ? 0 : 1;
            $published = (empty($line[7]) OR $line[7] == '0') ? 0 : 1;
            $warehouse_id = (array_key_exists($line[3], $warehouses)) ? $warehouses[$line[3]] : 0;
            $sameday_warehouse_id = (array_key_exists($line[4], $warehouses)) ? $warehouses[$line[4]] : 0;
            $out_of_town = (empty($line[8]) OR $line[8] == '0') ? 0 : 1;
            $block_shipstation = (empty($line[11]) OR $line[11] == '0') ? 0 : 1;
            /*
            $inserts[] = array(
                'province' => $line[0],
                'city' => $line[1],
                'postal_code' => $line[2],
                'warehouse' => $warehouse_id,
                'days_in_route' => (int)$line[4],
                'deliverable' => $delivarable,
                'published' => $published
            );*/
            
            $removes[] = "'".$database->getEscaped($line[2])."'";
            $inserts[] = "(
                '".$database->getEscaped($line[0])."',
                '".$database->getEscaped($line[1])."',
                '".$database->getEscaped($line[2])."',
                '".$warehouse_id."',
                '".$sameday_warehouse_id."',
                '".(int)$line[5]."',
                '".$deliverable."',
                '".$published."',
                '".$out_of_town."',
                '".$block_shipstation."',
                '".(int)$line[9]."',
                '".$line[10]."'
            )";
        }

        if (sizeof($inserts) > 0) {
            $return['result'] = true;
            $return['inserts'] = $inserts;
            $return['sizeof_inserts'] = sizeof($inserts);
            
            $query = "DELETE FROM 
            `jos_postcode_warehouse` 
            WHERE `postal_code` IN (".implode(',', $removes).")";
            
            $database->setQuery($query);
            $database->query();
            
            $query = "INSERT INTO 
            `jos_postcode_warehouse` 
            (
                `province`,
                `city`,
                `postal_code`,
                `warehouse_id`,
                `same_day_warehouse_id`,
                `days_in_route`,
                `deliverable`,
                `published`,
                `out_of_town`,
                `block_shipstation`,
                `additional_delivery_fee`,
                `country`
            )
            VALUES ".implode(',', $inserts)."";
            
            $database->setQuery($query);
            $database->query();
        }
        else {
            $return['error'] = 'Incorrect Text File.';
        }
    }
    else {
        $return['error'] = 'Incorrect File.';
    }
    
    echo json_encode($return);
    die;
}
//=================================================== POSTAL CODE OPTION ===================================================
function showAssignOrder( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

        mosCommonHTML::loadBootstrap(true);
        
	$limit 					= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 			= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$postcode_name_filter	= mosGetParam( $_REQUEST, "postcode_name_filter", "" );
	$postcode_city_filter	= mosGetParam( $_REQUEST, "postcode_city_filter", "" );
	$short_filter			= mosGetParam( $_REQUEST, "short_filter", "ASC" );
        $warehouse_id_filter			= mosGetParam( $_REQUEST, "warehouse_id_filter", 0 );
        $country_filter			= mosGetParam( $_REQUEST, "country_filter", '' );

        $where = array();
        if (!empty($postcode_name_filter)) {
            $wh_obj = false;
            $zip_symbols = mb_strlen($postcode_name_filter);

            while (($wh_obj == false) AND ($zip_symbols > 0)) {
                $postcode_name_filter = substr($postcode_name_filter, 0, $zip_symbols);
                
                $query = "SELECT 
                    `pwh`.`id`
                FROM `jos_postcode_warehouse` AS `pwh` 
                WHERE 
                    `pwh`.`postal_code` LIKE '".$postcode_name_filter."'
                ";

                $database->setQuery($query);
                $wh_obj = false;
                $database->loadObject($wh_obj);

                $zip_symbols--;
            }
            
            $where[] = "`p`.postal_code LIKE '".$postcode_name_filter."'";
        }
	
        if ($postcode_city_filter) {
            $where[] = "`p`.`city`LIKE '".$postcode_city_filter."'";
        }
        if ((int)$warehouse_id_filter > 0) {
            $where[] = "`p`.`warehouse_id`=".(int)$warehouse_id_filter."";
        }
        if ($country_filter) {
            $where[] = "`p`.`country`='".$country_filter."'";
        }
    
	// get the total number of records
	$query = "SELECT COUNT(*) AS total FROM #__postcode_warehouse AS `p` left join  #__vm_warehouse on #__vm_warehouse.warehouse_id = `p`.warehouse_id
            ".(sizeof($where) > 0 ? "WHERE ".implode(' AND ', $where)."" : '')."";
        
	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT w1.warehouse_name,w1.warehouse_code,w2.warehouse_name as `same_warehouse_name`,w2.warehouse_code as `same_warehouse_code` , p.*  FROM #__postcode_warehouse as p
left join  #__vm_warehouse as w1 on w1.warehouse_id = p.warehouse_id
left join  #__vm_warehouse as w2 on w2.warehouse_id = p.same_day_warehouse_id
			  ".(sizeof($where) > 0 ? "WHERE ".implode(' AND ', $where)."" : '')."
			  ORDER BY p.postal_code $short_filter";

	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
        
        $query = "SELECT 
            `w`.`warehouse_id`,
            `w`.`warehouse_name`
        FROM
        `jos_vm_warehouse` AS `w`
        ORDER BY `w`.`warehouse_name` ASC";
        $database->setQuery($query);
        $warehouses = $database->loadObjectList();
    
	HTML_AssignOrder::showAssignOrder( $rows, $warehouses, $pageNav, $option );
}


function editAssignOrder( $id, $option ) {
	global $database, $my, $mosConfig_absolute_path;

	$row = new mosAssignOrder( $database );
	// load the row from the db table
	$row->load( (int)$id );
    if (!$id) {
        $row->deliverable = 1;
        $row->published = 1;
        $row->days_in_route = 1;
        $row->out_of_town = 1;
    }
	$query 	= "SELECT warehouse_id, warehouse_name FROM #__vm_warehouse ORDER BY warehouse_name ASC";
	$database->setQuery($query);
	$rows	= $database->loadObjectList();
    $oWareHouseRow = new stdClass;
    $oWareHouseRow->warehouse_name = "NO WAREHOUSE ASSIGNED";
    $oWareHouseRow->warehouse_id = "NOWAREHOUSEASSIGNED";
    $aWareHouseRow = array();
    $aWareHouseRow[0] = $oWareHouseRow;
    $warehouseRows = array_merge($aWareHouseRow, $rows);
    $lists = array();
    $lists['deliverable']	= mosHTML::yesnoRadioList( "deliverable", "", $row->deliverable );
    $lists['published']	= mosHTML::yesnoRadioList( "published", "", $row->published );
    $lists['out_of_town'] = mosHTML::yesnoRadioList("out_of_town", "", $row->out_of_town);
    $lists['block_shipstation'] = mosHTML::yesnoRadioList("block_shipstation", "", $row->block_shipstation);
	$lists['warehouse']	= mosHTML::selectList( $warehouseRows, "warehouse_id", "size='1'", "warehouse_id", "warehouse_name", $row->warehouse_id );
    $lists['same_day_warehouse']= mosHTML::selectList( $warehouseRows, "same_day_warehouse_id", "size='1'", "warehouse_id", "warehouse_name", $row->same_day_warehouse_id);

	HTML_AssignOrder::editAssignOrder( $row, $option, $lists );
}


function saveAssignOrder( $option ) {
	global $database, $mosConfig_absolute_path, $act;
	$id				= mosGetParam( $_REQUEST, 'id', 0 );
	$warehouse_id	= mosGetParam( $_REQUEST, 'warehouse_id', 0 );
	$postal_code	= mosGetParam( $_REQUEST, 'postal_code', '' );
	$task2			= mosGetParam( $_REQUEST, 'task2', '' );
	
	$query 	= "SELECT id  FROM #__postcode_warehouse WHERE warehouse_id = {$warehouse_id} AND postal_code = '$postal_code'";
	$database->setQuery( $query );
	$nID 	= $database->loadResult();
	
	if( $task2 == 'new' ) {
		if( $nID ) $bExist = true;
	}else {
		if( $nID != $id && $nID ) {
			$bExist = true;
		}else {
			$bExist = false;	
		}		
	}

//	die($task2."===".$bExist."===".$nID."===".$id);
	if( !$bExist ) {	
		$row = new mosAssignOrder( $database );
		if (!$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}		
		
		// save the changes
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		mosRedirect( "index2.php?option=$option&act=$act", "Save Postal Code Successfully" );
	}else {
		if( $task2 == "new" ) {
			mosRedirect( "index2.php?option=$option&task=new", "This Postal Code for Warehouse is exist" );
		}else {
			mosRedirect( "index2.php?option=$option&task=$task2&id=$id", "This Postal Code for Warehouse is exist" );
		}
	}
}


function removeAssignOrder( &$cid, $option ) {
	global $database, $act, $mosConfig_absolute_path;
	
	if (count( $cid )) {		
		foreach ($cid as $value) {			
			$query = "DELETE FROM #__postcode_warehouse WHERE id = $value";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}				
		}
	}
	
	mosRedirect( "index2.php?option=$option&act=$act", "Remove Postal Code Successfully" );
}

function Publish_UnpublshPostalCode( $cid=null, $state=0, $option ) {
    global $database, $my, $act;

    if (!is_array( $cid ) || count( $cid ) < 1) {
        $action = $state ? 'publish' : 'unpublish';
        mosErrorAlert( "Select an item to $action" );
    }

    mosArrayToInts( $cid );
    $cids = 'id=' . implode( ' OR id=', $cid );

    $query = "UPDATE jos_postcode_warehouse SET published = " . (int) $state . " WHERE ( $cids )";
    $database->setQuery( $query );
    if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
    }

    if( $state ) {
        $msg	= "Published Postal Code Successfully";
    }else {
        $msg	= "UnPublished Postal Code Successfully";
    }

    mosRedirect( "index2.php?option=$option&act=$act", $msg );
}
function changePostalCode( $cid=null, $state=0, $option ) {
    global $database, $my, $act;

    if (!is_array( $cid ) || count( $cid ) < 1) {
        $action = $state ? 'deliverable' : 'undeliverable';
        mosErrorAlert( "Select an item to $action" );
    }

    mosArrayToInts( $cid );
    $cids = 'id=' . implode( ' OR id=', $cid );

    $query = "UPDATE jos_postcode_warehouse SET deliverable = " . (int) $state . " WHERE ( $cids )";
    $database->setQuery( $query );
    if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
    }

    if( $state ) {
        $msg	= "Deliverable Postal Code Successfully";
    }else {
        $msg	= "Undeliverable Postal Code Successfully";
    }

    mosRedirect( "index2.php?option=$option&act=$act", $msg );
}
function cancelAssignOrder() {
	mosRedirect('index2.php?option=com_assignorder');
}

?>
