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
require_once( $mainframe->getPath('class') );


$act = mosGetParam($_REQUEST, "act", "");
$cid = josGetArrayInts('cid');
$step = 0;

//die($act);
switch ($act) {
    case "account":
        switch ($task) {
            case 'new':
                editAccount('0', $option);
                break;

            case 'edit':
                editAccount(intval($cid[0]), $option);
                break;

            case 'editA':
                editAccount($id, $option);
                break;

            case 'save':
                saveAccount($option);
                break;

            case 'remove':
                removeAccount($cid, $option);
                break;

            case 'publish':
                changeAccount($cid, 1, $option);
                break;

            case 'unpublish':
                changeAccount($cid, 0, $option);
                break;

            case 'cancel':
                cancelAccount();
                break;

            default:
                showAccount($option);
                break;
        }
        break;


    case "product":
        switch ($task) {
            case 'export':
            case 'export_save':
                exportProductFile($task, $cid);
                break;

            default:
                exportProduct($option);
                break;
        }
        break;


    //=============================================================================================
    default:
        switch ($task) {
            case 'search':
            default:
                showOrder($option);
                break;
        }
        break;
}

function exportProductFile($task, $cid) {
    global $database, $mosConfig_absolute_path, $mosConfig_live_site, $option, $act;

    $partner_id = intval(mosGetParam($_REQUEST, 'partner_id'));
    $sId = implode(",", $cid);


    $query = " SELECT  VM.product_id, VM.product_sku, VM.product_name, VM.product_desc, VM.product_full_image,
							VMP.product_currency, VMP.product_price, VM.product_in_stock, VTR.tax_rate 
					FROM jos_vm_product AS VM LEFT JOIN jos_vm_product_price AS VMP ON VM.product_id = VMP.product_id
					LEFT JOIN  jos_vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id
					LEFT JOIN  jos_vm_product_discount AS VPD ON VPD.discount_id = VM.product_discount_id WHERE VM.product_id IN ($sId)";
    $database->setQuery($query);
    $products = $database->loadObjectList();

    $sProduct = '<?xml version="1.0" encoding="utf-8"?>
					<products version="1.0.0">';

    if (count($products)) {
        foreach ($products as $row) {
            if (is_file("$mosConfig_absolute_path/components/com_virtuemart/shop_image/product/" . $row->product_full_image)) {
                $product_image = "$mosConfig_live_site/components/com_virtuemart/shop_image/product/" . $row->product_full_image;
            }

            if ($row->is_percent) {
                $discount_type = "percent";
            } else {
                $discount_type = "total";
            }

            $product_image = htmlentities(htmlspecialchars($product_image, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
            $product_sku = htmlentities(htmlspecialchars(strip_tags($row->product_sku), ENT_QUOTES), ENT_QUOTES, 'UTF-8');
            $product_name = htmlentities(htmlspecialchars(strip_tags($row->product_name, "<br>"), ENT_QUOTES), ENT_QUOTES, 'UTF-8');
            $product_desc = htmlentities(htmlspecialchars(strip_tags($row->product_desc, "<br>"), ENT_QUOTES), ENT_QUOTES, 'UTF-8');
            $product_currency = htmlentities(htmlspecialchars($row->product_currency, ENT_QUOTES), ENT_QUOTES, 'UTF-8');

            $sProduct .= '<product>
								<id>' . $row->product_id . '</id>
								<sku>' . $product_sku . '</sku>
								<name><![CDATA[' . $product_name . ']]></name>
								<desc><![CDATA[' . $product_desc . ']]></desc>
								<image><![CDATA[' . $product_image . ']]></image>										
								<tax>' . floatval($row->tax_rate) . '</tax>
								<discount>' . floatval($row->amount) . '</discount>				
								<discount_type>' . $discount_type . '</discount_type>				
								<price>' . floatval($row->product_price) . '</price>				
								<curency>' . $product_currency . '</curency>				
							</product>';
        }
    }
    $sProduct .= '</products>';



    $query = "SELECT * FROM tbl_partners WHERE id = $partner_id";
    $database->setQuery($query);
    $users = $database->loadRow();

    if ($users[0]) {
        require_once("$mosConfig_absolute_path/xml_service/ftp.class.php");

        $tempFile = "$mosConfig_absolute_path/xml_service/tmp/products_temp.xml";
        $fh = fopen($tempFile, 'w') or die("can't open file");
        fwrite($fh, $sProduct);
        fclose($fh);

        $ftp = new ClsFTP($users[3], $users[4], $users[2]);


        if ($ftp->put("/httpdocs/" . date("Ymd", time()) . "_products.xml", $tempFile)) {
            $msg = "Generate Product XML file for " . $users[1] . " successful.";
        } else {
            $msg = "Generate Product XML file for " . $users[1] . " unsuccessful.";
        }

        $query = "	UPDATE tbl_partners SET product_updated_time = '" . date("Y-m-d H:i:s", time()) . "' WHERE partner_name = '" . $users[1] . "'";
        $database->setQuery($query);
        $database->query();

        $ftp->close();
    }

    if ($task == 'export_save') {
        $query = "	UPDATE tbl_partners SET product_filter_id = '" . $sId . "' WHERE partner_name = '" . $users[1] . "'";
        $database->setQuery($query);
        $database->query();
    }


    mosRedirect("index2.php?option=$option&act=$act", $msg);
}

function exportProduct($option) {
    global $database;

    $sql = " SELECT product_id AS id, product_sku, product_name FROM jos_vm_product ORDER BY product_name";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    $query = " SELECT * FROM tbl_partners ORDER BY partner_name ASC";
    $database->setQuery($query);
    $partners = $database->loadObjectList();

    if (count($partners)) {
        $partnerList = mosHTML::selectList($partners, "partner_id", "size='1'", "id", "partner_name");
    }

    HTML_XmlOrder::exportProduct($rows, $partnerList, $option);
}

function showOrder($option) {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
    $text_filter = mosGetParam($_REQUEST, "text_filter", "");

    $sWhere = $sWhere2 = '';
    if ($text_filter) {
        $sWhere = " WHERE ( PF.partner_order_id LIKE '%$text_filter%' OR  O.order_id LIKE '%$text_filter%' OR  PF.partner_name LIKE '%$text_filter%' ) ";
        $sWhere2 = " WHERE ( PF.partner_order_id LIKE '%$text_filter%' OR  O.order_id LIKE '%$text_filter%' OR  PF.partner LIKE '%$text_filter%' ) ";
        $sWhere3 = " WHERE ( PF.api_order_id LIKE '%$text_filter%' OR  O.order_id LIKE '%$text_filter%' OR  PF.partner LIKE '%$text_filter%' ) ";
    }


    // get the total number of records
    $query = "SELECT COUNT(*) AS total FROM tbl_xmlorder AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id 
			  $sWhere  ORDER BY O.cdate DESC";
    $database->setQuery($query);
    $total = $database->loadResult();

    //and from the new table
    $query = "SELECT COUNT(*) AS total FROM jos_vm_partners_orders AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id 
			  $sWhere2  ORDER BY O.cdate DESC";
    $database->setQuery($query);
    $total2 = $database->loadResult();
    
    $query = "SELECT COUNT(*) AS total FROM jos_vm_api2_orders AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id 
    $sWhere3  ORDER BY O.cdate DESC";
    $database->setQuery($query);
    $total3 = $database->loadResult();
    
    $total =$total+ $total2 + $total3;
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // get the subset (based on limits) of required records
    $query = "(SELECT O.*, W.*, OS.*, PF.partner_order_id, PF.partner_name,PF.created_date FROM tbl_xmlorder AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id 
			  LEFT JOIN jos_vm_warehouse AS W ON O.warehouse = W.warehouse_code 
			  LEFT JOIN jos_vm_order_status AS OS ON OS.order_status_code = O.order_status 
                           $sWhere )
UNION
(SELECT O.*, W.*, OS.*, PF.partner_order_id, PF.partner as \"parthner_name\",PF.datetime as \"created_date\" FROM jos_vm_partners_orders AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id 
			  LEFT JOIN jos_vm_warehouse AS W ON O.warehouse = W.warehouse_code 
			  LEFT JOIN jos_vm_order_status AS OS ON OS.order_status_code = O.order_status 
                           $sWhere2 )
UNION
(SELECT O.*, W.*, OS.*, PF.api_order_id as `partner_order_id`, PF.partner as \"parthner_name\",PF.datetime_add as \"created_date\" FROM jos_vm_api2_orders AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id 
			  LEFT JOIN jos_vm_warehouse AS W ON O.warehouse = W.warehouse_code 
			  LEFT JOIN jos_vm_order_status AS OS ON OS.order_status_code = O.order_status 
                           $sWhere3 )
ORDER BY cdate DESC";
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    HTML_XmlOrder::showOrder($rows, $pageNav, $option);
}

//=================================================== POSTAL CODE OPTION ===================================================
function showAccount($option) {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));

    // get the total number of records
    $query = "SELECT COUNT(*) FROM tbl_partners";
    $database->setQuery($query);
    $total = $database->loadResult();

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // get the subset (based on limits) of required records
    $query = "SELECT * FROM tbl_partners ORDER BY ftp_username";
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();


    HTML_XmlOrder::showAccount($rows, $pageNav, $option);
}

function editAccount($id, $option) {
    global $database, $my, $mosConfig_absolute_path;

    $row = new mosXmlOrder($database);
    // load the row from the db table
    $row->load((int) $id);

    if (!$id) {
        $row->ordering = 0;
        $row->published = 1;
    }

    $lists = array();
    $lists['publish'] = mosHTML::yesnoRadioList("published", "", $row->published);

    HTML_XmlOrder::editAccount($row, $option, $lists);
}

function saveAccount($option) {
    global $database, $mosConfig_absolute_path, $act;

    $row = new mosXmlOrder($database);
    if (!$row->bind($_POST)) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    // save the changes
    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }


    require "$mosConfig_absolute_path/xml_service/ftp.class.php";
    $ftp = new ClsFTP($row->ftp_username, $row->ftp_password, $row->domain_name);

    if (!$ftp->cd("/httpdocs/order_processed")) {
        if (!$ftp->mkdir("/httpdocs/order_processed")) {
            $msg = $msg . "<br/>The Order Processed folder wasn't create. Please create <b>/httpdocs/order_processed</b> manual.";
        }
    }

    mosRedirect("index2.php?option=$option&act=$act", "Save Account Successfully. " . $msg);
}

function removeAccount(&$cid, $option) {
    global $database, $act, $mosConfig_absolute_path;

    if (count($cid)) {
        foreach ($cid as $value) {
            $query = "DELETE FROM tbl_partners WHERE id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }
    }

    mosRedirect("index2.php?option=$option&act=$act", "Remove Postal Code Successfully");
}

function changeAccount($cid = null, $state = 0, $option) {
    global $database, $my, $act;

    if (!is_array($cid) || count($cid) < 1) {
        $action = $state ? 'publish' : 'unpublish';
        mosErrorAlert("Select an item to $action");
    }

    mosArrayToInts($cid);
    $cids = 'id=' . implode(' OR id=', $cid);

    $query = "UPDATE tbl_partners SET published = " . (int) $state . " WHERE ( $cids )";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if ($state) {
        $msg = "Publish Account Successfully";
    } else {
        $msg = "UnPublish Account Successfully";
    }

    mosRedirect("index2.php?option=$option&act=$act", $msg);
}

function cancelAccount() {
    mosRedirect('index2.php?option=com_xmlorder&act=account');
}

?>
