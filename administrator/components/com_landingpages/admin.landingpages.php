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
defined('_VALID_MOS') or die('Restricted access');

require_once( $mainframe->getPath('admin_html') );
require_once('landingpages.class.php');


global $mosConfig_absolute_path;

require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/language.class.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/languages/english.php" );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/Log/Log.php" );

$act = mosGetParam($_REQUEST, "act", "");
$cid = josGetArrayInts('cid');
$step = 0;

//die($act);
switch ($act) {
    default:
        switch ($task) {
            case 'parse_xlsx':
                parse_xlsx( );
                break;
            case 'get_current_list':
                get_current_list( );
                break;
            case 'new':
                editLandingPages('0', $option);
                break;

            case 'edit':
                editLandingPages(intval($cid[0]), $option);
                break;

            case 'editA':
                editLandingPages($id, $option);
                break;

            case 'save':
                saveLandingPages($option);
                break;

            case 'remove':
                removeLandingPages($cid, $option);
                break;

            case 'cancel':
                cancelLandingPages();
                break;

            default:
                showLandingPages($option);
                break;
        }
        break;
}
function get_current_list(){
    ob_end_clean();
    global $database;
    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
    $phpexcel = new PHPExcel();

    $query = "SELECT t.*,i.category 
FROM tbl_landing_pages as t 
Left Join tbl_landing_pages_info as i on i.landing_url=t.url
 Group By t.url ORDER BY t.city ASC limit ".$_REQUEST['startFrom'].",".$_REQUEST['rowsCount']." ";

    $page = $phpexcel->setActiveSheetIndex(0);

    $page->setCellValue('A1', 'ID');
    $page->setCellValue('B1', 'City');
    $page->setCellValue('C1', 'URL');
    $page->setCellValue('D1', 'Province');
    $page->setCellValue('E1', 'Telephone');
    $page->setCellValue('F1', 'Enable Location');
    $page->setCellValue('G1', 'Location Address');
    $page->setCellValue('H1', 'Location Postcode');
    $page->setCellValue('I1', 'Location Telephone');
    $page->setCellValue('J1', 'Latitude');
    $page->setCellValue('K1', 'Longitude');
    $page->setCellValue('L1', 'Nearby Cities');
    $page->setCellValue('M1', 'Categories');
    $i = 2;
    $database->setQuery($query);
    $landings_obj = $database->loadObjectList();

    foreach ($landings_obj as $landing_obj) {
        set_time_limit(60);
        $query = 'SELECT GROUP_CONCAT(DISTINCT(category_name)) as categories   FROM jos_vm_category WHERE category_id in ("'.implode('","',array_map('getEscaped',unserialize($landing_obj->category))).'")';
        $database->setQuery($query);
        $categories_res = false;
        $database->loadObject($categories_res);

        $page->setCellValue('A'.$i, $database->getEscaped($landing_obj->id));
        $page->setCellValue('B'.$i, $database->getEscaped($landing_obj->city));
        $page->setCellValue('C'.$i, $database->getEscaped($landing_obj->url));
        $page->setCellValue('D'.$i, $database->getEscaped($landing_obj->province));
        $page->setCellValue('E'.$i, $database->getEscaped($landing_obj->telephone));
        $page->setCellValue('F'.$i, $database->getEscaped($landing_obj->enable_location));
        $page->setCellValue('G'.$i, $database->getEscaped($landing_obj->location_address));
        $page->setCellValue('H'.$i, $database->getEscaped($landing_obj->location_postcode));
        $page->setCellValue('I'.$i, $database->getEscaped($landing_obj->location_telephone));
        $page->setCellValue('J'.$i, $database->getEscaped($landing_obj->lat));
        $page->setCellValue('K'.$i, $database->getEscaped($landing_obj->lng));
        $page->setCellValue('L'.$i, $database->getEscaped($landing_obj->nearby_cities));
        $page->setCellValue('M'.$i, $database->getEscaped($categories_res->categories));

        $i++;
    }

    $page->setTitle('Landings List');



    $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
    header('Content-Disposition: attachment;filename=landings_list.xlsx');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $objWriter->save('php://output');
    die;
}
function parse_xlsx(){
    global $database;
    require_once $_SERVER['DOCUMENT_ROOT']."/scripts/simplexlsx.class.php";
    $xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
    $sheetNames = array_keys($xlsx->sheetNames());
    $sheet_num = $sheetNames[0];

    $sheet_header=array(
        'ID',
        'City',
        'URL',
        'Province',
        'Telephone',
        'Enable Location',
        'Location Address',
        'Location Postcode',
        'Location Telephone',
        'Latitude',
        'Longitude',
        'Nearby Cities',
        'Categories',
    );



    $res=array();
    if ( 0 < $_FILES['file']['error'] ) {
        $res = array('invalid file');
    }else {
        $parsed_result_arr = $xlsx->rowsEx($sheet_num);

        if ($parsed_result_arr) {
            foreach($parsed_result_arr as $m=>$p){
                if($m==0){
                    continue;
                }
                foreach($p as $q=>$k){
                    if($sheet_header[$q] == 'Categories' && $k['value']!='') {

                        $query = 'SELECT DISTINCT(category_id) as categories   FROM jos_vm_category WHERE category_name in ("'.implode('","',explode(',',$k['value'])).'")';
                        $database->setQuery($query);
                        $k['value'] = serialize($database->loadResultArray());

                    }
                        $parsed_result[$m][$sheet_header[$q]]=$k['value'];
                }
            }

            foreach ($parsed_result as $r) {
                set_time_limit(60);
                if($r["ID"]!='' && $r["URL"]!=''){

                        $database->setQuery('UPDATE tbl_landing_pages SET 
                        nearby_cities="'.$database->getEscaped($r['Nearby Cities']).'" ,
                        city="'.$database->getEscaped($r['City']).'" ,
                        url="'.$database->getEscaped($r['URL']).'" ,
                        province="'.$database->getEscaped($r['Province']).'" ,
                        telephone="'.$database->getEscaped($r['Telephone']).'" ,
                        enable_location="'.$database->getEscaped($r['Enable Location']).'" ,
                        location_address="'.$database->getEscaped($r['Location Address']).'" ,
                        location_postcode="'.$database->getEscaped($r['Location Postcode']).'" ,
                        location_telephone="'.$database->getEscaped($r['Location Telephone']).'" ,
                        lat="'.$database->getEscaped($r['Latitude']).'" ,
                        lng="'.$database->getEscaped($r['Longitude']).'" ,
                        lng="'.$database->getEscaped($r['Longitude']).'" 
                        WHERE id= "'.$r["ID"].'"');
                        $database->query();

                        if($r["Categories"] != '') {
                            $database->setQuery('UPDATE tbl_landing_pages_info SET 
                                category="'.$database->getEscaped($r["Categories"]).'" 
                                WHERE landing_url= "'.$r["URL"].'"');
                            $database->query();
                        }

                } elseif($r["URL"]!='') {
                    $database->setQuery('INSERT INTO tbl_landing_pages (
                        nearby_cities,
                        city,
                        url,
                        province,
                        telephone,
                        enable_location,
                        location_address,
                        location_postcode,
                        location_telephone,
                        lat,
                        lng) VALUES  
                        ("'.$database->getEscaped($r['Nearby Cities']).'" ,
                        "'.$database->getEscaped($r['City']).'" ,
                        "'.$database->getEscaped($r['URL']).'" ,
                        "'.$database->getEscaped($r['Province']).'" ,
                        "'.$database->getEscaped($r['Telephone']).'" ,
                        "'.$database->getEscaped($r['Enable Location']).'" ,
                        "'.$database->getEscaped($r['Location Address']).'" ,
                        "'.$database->getEscaped($r['Location Postcode']).'" ,
                        "'.$database->getEscaped($r['Location Telephone']).'" ,
                        "'.$database->getEscaped($r['Latitude']).'",
                        "'.$database->getEscaped($r['Longitude']).'")');
                    if(!$database->query()){
                        continue;
                    }

                    $types = array('landing', 'basket', 'sympathy');
                    foreach ($types as $k => $type) {
                        $query = "SELECT * FROM tbl_landing_pages_info WHERE `type`='" . $type . "' AND landing_url='" . strip_tags($r['URL']) . "' limit 1";

                        $database->setQuery($query);
                        if (!$database->query()) {
                            echo $database->getErrorMsg();
                            echo "error";
                            exit(0);
                        }

                        $rows = $database->loadResult();
                        if ($rows) {
                                $query_info = "UPDATE tbl_landing_pages_info
                                  SET 
                                  category='" . $r['Categories'] . "'
                                    WHERE `type`='" . $type . "' AND landing_url='" . strip_tags($r['URL']) . "'";
                            $database->setQuery($query_info);
                            if (!$database->query()) {
                                echo $database->getErrorMsg();
                                echo "error";
                                exit(0);
                            }
                        } else {

                                $query_info = "INSERT INTO  tbl_landing_pages_info
                      (category,landing_url,type)
                       VALUES (
                      '" . $r['Categories'] . "','" . strip_tags($r['URL']) . "','" . $type . "')";

                                $database->setQuery($query_info);
                                if (!$database->query()) {
                                    echo $database->getErrorMsg();
                                    echo "error";
                                    exit(0);
                                }

                        }
                    }
                }
            }
            $res=array('success');
        }
        else{
            $res = array('invalid file');
        }
    }
    exit(json_encode($res));

}
//=================================================== LandingPages OPTION ===================================================
function showLandingPages($option) {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));

    $filter_key = trim(mosGetParam($_POST, "filter_key"));
    $not_default = trim(mosGetParam($_POST, "not_default"));
    $where = "";
    $aWhere = array();


    if ($not_default) {
        $not_default_checked = "checked";
        $aWhere[] = " (i.category!='')  ";
    } else {
        $not_default_checked = "";
    }

    if ($filter_key) {
        $aWhere[] = " (t.province LIKE '%$filter_key%' OR t.url LIKE '%$filter_key%' OR t.city LIKE '%$filter_key%') ";
    }

    if (count($aWhere))
        $where = " WHERE " . implode(" AND ", $aWhere);

    // get the total number of records
    $query = "SELECT t.* FROM tbl_landing_pages as t Left Join tbl_landing_pages_info as i on i.landing_url=t.url $where  Group By t.url";
    $database->setQuery($query);
    $total = count($database->loadObjectList());

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // get the subset (based on limits) of required records
    $query = "SELECT t.*,i.category FROM tbl_landing_pages as t Left Join tbl_landing_pages_info as i on i.landing_url=t.url  $where  Group By t.url ORDER BY t.city ASC";
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    $lists = array();

    $lists['filter_key'] = $filter_key;
    $lists['not_default'] = $not_default_checked;

    HTML_LandingPages::showLandingPages($rows, $pageNav, $option, $lists);
}

function list_categories_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {
   $ps_vendor_id=1;
    $db = new ps_DB;
    $level++;

    $q = "SELECT category_id, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
    $q .= "WHERE #__{vm}_category_xref.category_parent_id='$cid' ";
    $q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
    $q .= "AND #__{vm}_category.vendor_id ='$ps_vendor_id' ";
    $q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
    $db->setQuery($q);   $db->query();

$res='';
    while ($db->next_record()) {
        $child_id = $db->f("category_child_id");

        $selected = '';
            if(in_array($child_id,$selected_categories)) {
                $selected = "selected=\"selected\"";
            }
           $res.= "<option  $selected value=\"$child_id\">\n";


        for ($i=0;$i<$level;$i++) {
            $res.= "&#151;";
        }
        $res.= "|$level|";
            $res.= "&nbsp;" . $db->f("category_name")."</option>";
        $res.= list_categories_tree($category_id, $child_id, $level, $selected_categories);
    }
        return $res;
}

function list_categories($name, $category_id, $selected_categories=Array()) {
    $db = new ps_DB;
    $q  = "SELECT category_parent_id FROM #__{vm}_category_xref ";
    if( $category_id )
        $q .= "WHERE category_child_id='$category_id'";
    $db->setQuery($q);   $db->query();
    $db->next_record();
    $category_id=$db->f("category_parent_id");
    $res= "<select class=\"inputbox\" size=\"10\" multiple=\"multiple\" name=\"$name\">\n";
    $res.= list_categories_tree($category_id,'0', '0', $selected_categories);
    $res.= "</select>\n";

    return $res;
}


function editLandingPages($id, $option) {
    global $database, $my, $mosConfig_absolute_path;

    require_once(CLASSPATH . 'vmAbstractObject.class.php' );
    require_once(CLASSPATH . 'ps_database.php');


    $row = new mosLandingPages($database);
    // load the row from the db table
    $row->load((int) $id);

    if (!$id) {
        $row->id = "";
        $row->city = "";
        $row->province = "";
        $row->url = "";
        $row->telephone = "";
        $row->location_address = "";
        $row->location_country = "";
        $row->location_postcode = "";
        $row->location_telephone = "";
        $row->category_id = "";
        $row->enable_location = 0;
        $row->nearby_cities = "";
    }

    $lists = array();
    $lists['enable_location'] = mosHTML::yesnoRadioList("enable_location", "", $row->enable_location);

    $query 	= "SELECT state_name,state_3_code FROM #__vm_state 
				WHERE country_id = '13' 
				ORDER BY state_name";
    $database->setQuery($query);
    $rows_province	= $database->loadObjectList();
    $lists['province'] = mosHTML::selectList($rows_province, "province", "size='1'", "state_3_code", "state_name", $row->province);

    $query_info = "SELECT category FROM tbl_landing_pages_info WHERE landing_url='" . strip_tags($row->url) . "'  limit 1";
    $database->setQuery($query_info);
    $info = $database->loadResult();

    $categories_list = list_categories('categories[]', '',$info?unserialize($info):[] );

    HTML_LandingPages::editLandingPages($row, $option, $lists, $categories_list);
}

function getEscaped($var) {
    global $database;

    return $database->getEscaped($var);
}

function getCitiesIdsByNames($names) {
    global $database;

    $query = "SELECT 
        GROUP_CONCAT(DISTINCT(`lp`.`id`) SEPARATOR ',') AS `ids`
    FROM `tbl_landing_pages` AS `lp`
    WHERE 
        `lp`.`city` IN ('" . implode("','", array_map('getEscaped', $names)) . "')
    ";


    $database->setQuery($query);
    $cities_obj = false;
    $database->loadObject($cities_obj);

    return array_map('trim', explode(',', $cities_obj->ids));
}

function addhideprefix($t)
{
    return 'hide_'.trim($t);
}
function removehideprefix($t)
{
    if($t!='' && strlen($t)>5 && substr($t,0,5)=='hide_'){
        return trim(substr($t,5));
    }else{
        return trim($t);
    }
}
function declare_show($n)
{
    return array($n,'1');
}
function declare_hide($n)
{
    return array($n,'0');
}
function saveLandingPages($option) {
    global $database, $mosConfig_absolute_path, $act;

    $now_cities=array();
    if($_POST['nearby_cities'] && $_POST['nearby_cities'][0]){
        $show_cities = array_map('trim', explode(',', $_POST['nearby_cities'][0]));
        $now_show_cities = array_map('declare_show',getCitiesIdsByNames($show_cities));
    }
    if($_POST['nearby_cities'] && $_POST['nearby_cities'][1]) {
        $hide_cities = array_map('removehideprefix', explode(',', $_POST['nearby_cities'][1]));
        $now_hide_cities = array_map('declare_hide',getCitiesIdsByNames($hide_cities));
    }
    $now_cities=array_merge($now_show_cities, $now_hide_cities);


    if($_POST['nearby_cities'] && $_POST['nearby_cities'][1] &&  $_POST['nearby_cities'][1]!=''){
        $hide_nearby_cities = array_map('addhideprefix', explode(',', $_POST['nearby_cities'][1]));
        $_POST['nearby_cities']=$_POST['nearby_cities'][0].','.implode(',',$hide_nearby_cities);
    }else{
        $_POST['nearby_cities']=$_POST['nearby_cities'][0];
    }

    $query = "SELECT * FROM tbl_landing_pages WHERE url='" . strip_tags($_POST['url']) . "' limit 1";
    $database->setQuery($query);
    $rows = $database->loadResult();
    if ($rows && !$_POST['id']) {
        mosRedirect("index2.php?option=$option&act=$act", "Url already exist please enter other url");
    }


    $row = new mosLandingPages($database);

    $_POST['url'] = strip_tags($_POST['url']);
    if (!$row->bind($_POST)) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    // save the changes
    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $types = array('landing', 'basket', 'sympathy');
    foreach ($types as $k => $type) {
        $query = "SELECT * FROM tbl_landing_pages_info WHERE `type`='" . $type . "' AND landing_url='" . strip_tags($_POST['url']) . "' limit 1";

        $database->setQuery($query);
        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "error";
            exit(0);
        }

        $category_seriize = serialize($_POST['categories']);

        $rows = $database->loadResult();
        if ($rows) {
            if ($category_seriize == '') {
                $query_info = "DELETE FROM  tbl_landing_pages_info   WHERE `type`='" . $type . "' AND landing_url='" . strip_tags($_POST['url']) . "'";
            } else {
                $query_info = "UPDATE tbl_landing_pages_info
                                  SET 
                                  category='" . $category_seriize . "'
                                    WHERE `type`='" . $type . "' AND landing_url='" . strip_tags($_POST['url']) . "'";
            }

            $database->setQuery($query_info);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "error";
                exit(0);
            }
        } else {
            if ($category_seriize != '') {

                $query_info = "INSERT INTO  tbl_landing_pages_info
                      (category,landing_url,type)
                       VALUES (
                      '" . $category_seriize . "','" . strip_tags($_POST['url']) . "','" . $type . "')";

                $database->setQuery($query_info);
                if (!$database->query()) {
                    echo $database->getErrorMsg();
                    echo "error";
                    exit(0);
                }
            }
        }
    }
    mosRedirect("index2.php?option=$option&act=$act", "Save Landing Pages Successfully");
}

function removeLandingPages(&$cid, $option) {
    global $database, $act, $mosConfig_absolute_path;

    if (count($cid)) {
        foreach ($cid as $value) {
            $query = "SELECT url FROM tbl_landing_pages WHERE id = $value";
            $database->setQuery($query);
            $rows = $database->loadRow();
            $url = '';
            if ($rows) {
                $url = $rows[0];
            }
            $query = "DELETE FROM tbl_landing_pages WHERE id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            } else {
                $query = "DELETE FROM tbl_landing_pages_info WHERE landing_url = '" . $url . "'";
                $database->setQuery($query);
                if (!$database->query()) {
                    echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                }
            }
        }
    }

    mosRedirect("index2.php?option=$option", "Remove Landing Pages Successfully");
}

function cancelLandingPages() {
    mosRedirect('index2.php?option=com_landingpages');
}

?>