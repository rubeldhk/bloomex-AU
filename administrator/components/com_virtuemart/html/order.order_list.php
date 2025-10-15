<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: order.order_list.php,v 1.6.2.4 2006/03/28 19:40:15 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
mm_showMyFileName(__FILE__);
global $mosConfig_locale, $mosConfig_FastLabelUrl, $my,$mosConfig_HideStatusInOrderListMenu;

setlocale(LC_TIME, $mosConfig_locale);
$show = mosGetParam($_REQUEST, "show", "");
$another_sites = mosGetParam($_REQUEST, "another_sites", "");
$mkeyword = mosGetParam($_REQUEST, "mkeyword", "");
$delivery_month = mosGetParam($_REQUEST, "delivery_month", "");
$delivery_day = mosGetParam($_REQUEST, "delivery_day", "");
$delivery_year = mosGetParam($_REQUEST, "delivery_year", "");
$order_created = mosGetParam($_REQUEST, "order_created1", "");
//$ordern 			= mosGetParam( $_REQUEST, "ordern", "" );
$warehouse_filter = ($my->prevs->warehouse_only) ? $my->prevs->warehouse_only : mosGetParam($_REQUEST, "warehouse_filter", "");
$shipping_province_filter = mosGetParam($_REQUEST, "shipping_province_filter", "");
$order_id_filter = mosGetParam($_REQUEST, 'order_id_filter', null);
$partner_order_id = mosGetParam($_REQUEST, 'partner_order_id', null);
$nz_order_id = mosGetParam($_REQUEST, 'nz_order_id', null);
$product_sku_filter = mosGetParam($_REQUEST, 'product_sku_filter', null);
$customer_name_filter = mosGetParam($_REQUEST, 'customer_name_filter', null);
$user_email_filter = mosGetParam($_REQUEST, 'user_email_filter', null);
$phonenumber_filter = mosGetParam($_REQUEST, 'phonenumber_filter', null);
$delivery_date_from = mosGetParam($_REQUEST, 'delivery_date_from', null);
$delivery_date_to = mosGetParam($_REQUEST, 'delivery_date_to', null);
$filter_condition = mosGetParam($_REQUEST, "filter_condition", "");
echo '<input type="hidden" name="rate_user_name" value="' . $my->username . '" />';
date_default_timezone_set('Australia/Sydney');
$form_code = "";

if (strlen($delivery_month) == 1) {
    $delivery_month = '0' . $delivery_month;
}
if($limit > 100){
    $limit = 100;
}
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );
/* orders feed
  require_once($mosConfig_absolute_path.'/administrator/components/com_servermanager/loadfunc.php' );
  loadfunc('Order Manager');
 */
//==============================================================================================================
global $database, $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_status_cancel_fast_label, $mosConfig_status_fast_label, $mosConfig_limit_sms_sender_AccountKey;
$sImgLoading = "$mosConfig_live_site/administrator/components/com_virtuemart/html/jquery_ajax.gif";
echo '<script type="text/javascript" src="' . $mosConfig_live_site . '/administrator/components/com_virtuemart/html/jquery.js" ></script>';
echo '<script type="text/javascript" src="' . $mosConfig_live_site . '/administrator/templates/' . $cur_template . '/js/jquery-2.2.4.min.js"></script>';
echo '<script type="text/javascript" src="' . $mosConfig_live_site . '/administrator/components/com_virtuemart/html/jtabber.js" ></script>';
echo '<script type="text/javascript" src="' . $mosConfig_live_site . '/administrator/components/com_virtuemart/html/jquery.selectboxes.min.js" ></script>';
mosCommonHTML::loadBootstrap();
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<?php
//$sql = "SELECT order_occasion_code FROM #__vm_order_occasion GROUP BY order_occasion_code";
//$database->setQuery($sql);
//$oOccasionCode = $database->loadObjectList();
//
//$sOccasionCode	= "";
//foreach ( $oOccasionCode as $Item ) {
//	$sOccasionCode	.= "'{$Item->order_occasion_code}',";
//}
//$sOccasionCode	= substr( $sOccasionCode, 0, strlen($sOccasionCode) - 1 );
//==============================================================================================================

if($_REQUEST['task']=='download_orders_list'){
    global $database;

    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
    $phpexcel = new PHPExcel();
    $page = $phpexcel->setActiveSheetIndex(0);

    $page->setCellValue('A1', 'Order #');
    $page->setCellValue('B1', 'Recipient First Name');
    $page->setCellValue('C1', 'Recipient Last Name');
    $page->setCellValue('D1', 'Address Line 1');
    $page->setCellValue('E1', 'Amount Paid');
    $page->setCellValue('F1', 'City');
    $page->setCellValue('G1', 'Country Code');
    $page->setCellValue('H1', 'State');
    $page->setCellValue('I1', 'Postal Code');
    $page->setCellValue('J1', 'Item Name / Title');
    $page->setCellValue('K1', 'Buyer Email');
    $page->setCellValue('L1', 'Order Date');
    $page->setCellValue('M1', 'Shipping Paid');
    $page->setCellValue('N1', 'Recipient Phone');
    $page->setCellValue('O1', 'Recipient Company');
    $page->setCellValue('P1', 'Buyer First Name');
    $page->setCellValue('Q1', 'Buyer Last Name');
    $page->setCellValue('R1', 'Buyer Phone');
    $page->setCellValue('S1', 'Notes from the Buyer');
    $page->setCellValue('T1', 'Item SKU');
    $page->setCellValue('U1', 'Item Quantity');
    $page->setCellValue('V1', 'Item Unit Price');
    $page->setCellValue('W1', 'Notes to the Buyer');
    $i = 2;

                        $query = "SELECT o.order_id,
                        o.order_shipping as order_shipping,
                        o.customer_note,
                        o.order_total as order_total,
                        oi.first_name as ship_first_name,
                        oi.last_name as ship_last_name,
                        oi.phone_1 as ship_phone_1,
                        ob.phone_1 as bill_phone_1,
                        ob.first_name as bill_first_name,
                        ob.last_name as bill_last_name,
                        oi.company as ship_company,
                         FROM_UNIXTIME(o.cdate + 11 * 3600, '%d %M, %Y') as order_date,
                        GROUP_CONCAT(oi.suite ,' ' ,oi.street_number , ' ' , oi.street_name) as address_1,
                        oi.city,jvc.country_name ,
                        jvs.state_name ,oi.zip,ob.user_email,
                        CASE
                            WHEN jvoh.order_id THEN \"Resend\"
                            ELSE \"\"
                        END as notes_to_buyer,
                        ot.order_item_name as item,
                        ot.order_item_sku as item_sku,
                        ot.product_quantity as item_qty,
                        ot.product_final_price as item_price
                        FROM `jos_vm_orders` as o
                        left join jos_vm_order_user_info as oi on oi.order_id=o.order_id and oi.address_type='ST'
                        left join jos_vm_order_user_info as ob on ob.order_id=o.order_id and ob.address_type='BT'
                        left join jos_vm_order_item as ot on ot.order_id=o.order_id 
                         left join jos_vm_country jvc  on jvc.country_3_code  = oi.country  
                        left join jos_vm_state jvs on jvs.state_2_code  = oi.state and jvs.country_id  = jvc.country_id 
                        left join jos_vm_order_history jvoh  on jvoh.order_id = o.order_id and jvoh.order_status_code  = 'Y' 
                        WHERE o.`order_id`in (".$database->getEscaped($_REQUEST['orders']).")  group by o.order_id";
    $database->setQuery($query);
    $products_obj = $database->loadObjectList();

    foreach ($products_obj as $product_obj) {
        $page->setCellValue('A'.$i, $product_obj->order_id);
        $page->setCellValue('B'.$i, $product_obj->ship_first_name);
        $page->setCellValue('C'.$i, $product_obj->ship_last_name);
        $page->setCellValue('D'.$i, $product_obj->address_1);
        $page->setCellValue('E'.$i, $product_obj->order_total);
        $page->setCellValue('F'.$i, $product_obj->city);
        $page->setCellValue('G'.$i, $product_obj->country_name);
        $page->setCellValue('H'.$i, $product_obj->state_name);
        $page->setCellValue('I'.$i, $product_obj->zip);
        $page->setCellValue('J'.$i, $product_obj->item);
        $page->setCellValue('K'.$i, $product_obj->user_email);
        $page->setCellValue('L'.$i, $product_obj->order_date);
        $page->setCellValue('M'.$i, $product_obj->order_shipping);
        $page->setCellValue('N'.$i, $product_obj->ship_phone_1);
        $page->setCellValue('O'.$i, $product_obj->ship_company);
        $page->setCellValue('P'.$i, $product_obj->bill_first_name);
        $page->setCellValue('Q'.$i, $product_obj->bill_last_name);
        $page->setCellValue('R'.$i, $product_obj->bill_phone_1);
        $page->setCellValue('S'.$i, $product_obj->customer_note);
        $page->setCellValue('T'.$i, $product_obj->item_sku);
        $page->setCellValue('U'.$i, $product_obj->item_qty);
        $page->setCellValue('V'.$i, $product_obj->item_price);
        $page->setCellValue('W'.$i, $product_obj->notes_to_buyer);
        $i++;
    }

    $page->setTitle('Products List');

    ob_end_clean();

    $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=orders_list.xlsx');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $objWriter->save('php://output');
    die;
}

$queryOccasion = "SELECT order_occasion_code, order_occasion_name FROM jos_vm_order_occasion  where published=1";
$database->setQuery($queryOccasion);
$rowsOccasion = $database->loadObjectList();


$list = "SELECT O.order_id,O.color,pw.block_shipstation, O.order_status, O.cdate,AT.user_email AS 'delivery_email', O.mdate, O.warehouse,v.score,O.customer_occasion, "
        . "(select user_name from jos_vm_order_history h where h.order_id=O.order_id order by h.order_status_history_id desc limit 1) as operator,"
        . " O.ddate,order_total, O.user_id,O.order_shipping,";
$list .= "UI.first_name, UI.last_name, UI.company, UI.city, UI.phone_1, UI.address_1 , O.username, AT.address_type2, "; //delivery address type
$list .= " IFNULL(`p1`.`partner`, IFNULL(`p2`.`partner`, '')) AS `partner_name`, 
    IFNULL(`p1`.`partner_order_id`, IFNULL(`p2`.`api_order_id`, '')) AS `partner_order_id`,
    IFNULL(`sub`.`sub_order_id`, IFNULL(`sub`.`sub_order_id`, '')) AS `suborder`,
    `e`.`shopper_group_name`,
    `lpo`.`partner_id`
     ";
$list .= "FROM #__{vm}_orders AS O ";
$list .= (!empty($another_sites) ? "INNER" : "LEFT") . " JOIN tbl_feed_in AS fi ON fi.order_id=O.order_id ";
if (!empty($another_sites)) {
    $list .= "AND fi.feed_supplier='" . $another_sites . "' ";
}
$list .="LEFT JOIN #__{vm}_order_user_info AS `AT` ON AT.order_id = O.order_id AND `AT`.`address_type`='ST'";
$list .= " LEFT JOIN #__{vm}_order_user_info AS UIS ON O.order_id=UIS.order_id  ";
$list .= "LEFT JOIN jos_vm_orders_extra as e on e.order_id=O.order_id and e.shopper_discount_value > 0 ";
$list .= "LEFT JOIN `jos_vm_partners_orders` AS `p1`
    ON 
    `p1`.`order_id`=`O`.`order_id`
";
$list .= "LEFT JOIN `jos_vm_api2_orders` AS `p2`
    ON 
    `p2`.`order_id`=`O`.`order_id`
 ";
$list .= "LEFT JOIN jos_postcode_warehouse as pw on pw.postal_code = `AT`.zip and pw.published = 1  and pw.block_shipstation = 1  ";
$list .= "LEFT JOIN `tbl_local_parthners_orders` AS `lpo`
    ON 
    `lpo`.`order_id`=`O`.`order_id`
";
$list .= "LEFT JOIN `jos_vm_sub_orders_xref` AS `sub`
    ON 
    `sub`.`sub_order_id`=`O`.`order_id`
";
$list .= "LEFT JOIN tbl_address_validation as v ON v.order_id = O.order_id ";
$list .= "LEFT JOIN #__{vm}_order_user_info AS UI ON O.order_id=UI.order_id and  UI.address_type='BT' WHERE ";

$count = "SELECT count( O.order_id) as num_rows FROM #__{vm}_orders AS O ";

$q = '';

$order_id_filter_l = "";
$title = '';
if (!empty($order_id_filter)) {
    $q = " O.order_id = $order_id_filter AND ";
}
if (!empty($nz_order_id)) {
    $q = " `p2`.`api_order_id` = $nz_order_id AND ";
}

if (!empty($partner_order_id)) {
    $q = " `p1`.`partner_order_id` = $partner_order_id AND ";
}

if (!empty($user_email_filter)) {
    $q .= " UI.user_email LIKE '%$user_email_filter%' AND ";
}

if (!empty($phonenumber_filter)) {
    $q .= " UI.phone_1 LIKE '%" . $phonenumber_filter  . "%' AND ";
}

if (!empty($delivery_date_from) && !empty($delivery_date_to)) {
    $q .= " (date_format(str_to_date(O.ddate, '%d-%m-%Y'), '%Y-%m-%d') BETWEEN '" . $delivery_date_from . "' AND '" . $delivery_date_to . "' OR O.ddate BETWEEN '" . $delivery_date_from . "' AND '" . $delivery_date_to . "' ) AND ";
} elseif (!empty($delivery_date_from)) {
    $q .= " (date_format(str_to_date(O.ddate, '%d-%m-%Y'), '%Y-%m-%d') >= '" . $delivery_date_from . "' OR O.ddate >= '" . $delivery_date_from . "') AND ";
} elseif (!empty($delivery_date_to)) {
    $q .= " (date_format(str_to_date(O.ddate, '%d-%m-%Y'), '%Y-%m-%d') <= '" . $delivery_date_to . "' OR O.ddate <= '" . $delivery_date_to . "') AND ";
}



if (!empty($show)) {
    $q .= "order_status = '$show' AND ";
}

if (!empty($order_created)) {
    $order_created = explode('-', $order_created);
    $order_created_mtime_start = mktime(0, 0, 0, $order_created[1], $order_created[2], $order_created[0]);
    $order_created_mtime_finish = $order_created_mtime_start + 86400;

    $q .= "O.cdate >= " . $order_created_mtime_start . " AND O.cdate <= " . $order_created_mtime_finish . " AND ";
}


if (!empty($warehouse_filter) && $warehouse_filter != 'NOWAREHOUSEASSIGNED') {
    $q_warehouse_filter = "warehouse = '$warehouse_filter' AND ";
} elseif ($warehouse_filter == 'NOWAREHOUSEASSIGNED') {
    $q_warehouse_filter = "( warehouse = '' OR warehouse IS NULL OR warehouse = 'NOWAREHOUSEASSIGNED' OR warehouse = 'NOWAR' ) AND ";
}

if (!empty($q_warehouse_filter)) {
    $q .= $q_warehouse_filter;
}
if (!empty($shipping_province_filter)) {
    if ($shipping_province_filter == 'NZ') {
        $q .= " UIS.country= 'NZL'  AND UIS.address_type='ST' AND ";
    } else {
        $q .= " UIS.state= '" . $shipping_province_filter . "'  AND UIS.address_type='ST' AND ";
    }
}


if (!empty($customer_name_filter)) {
    $q .= " ((UI.last_name LIKE '%$customer_name_filter%' AND UI.address_type='BT') OR (UI.last_name LIKE '%$customer_name_filter%' AND UI.address_type='ST') ) ";
} else {
    $q .= " 1=1 ";
}


if($_REQUEST['search']){
    $limitstart = 0;
}


$list .= $q . " GROUP BY O.order_id ORDER BY O.order_id  DESC LIMIT $limitstart, " . $limit;
if($q!=' 1=1 ') {
$count .= " LEFT JOIN #__{vm}_order_user_info AS UI ON O.order_id=UI.order_id and  UI.address_type='BT'
            LEFT JOIN #__{vm}_order_user_info AS UIS ON O.order_id=UIS.order_id and  UIS.address_type='ST' 
            WHERE ".$q;
}

f($list,$count);
// ==============Filter order by SKU=============
if (!empty($product_sku_filter)) {

    $q = "SELECT 
    O.order_id, O.order_status, O.cdate, O.mdate, O.warehouse,O.color,
    (select user_name from jos_vm_order_history h where h.order_id=O.order_id order by h.order_status_history_id desc limit 1) as operator,
    O.ddate,order_total, O.user_id,UI.first_name, UI.last_name, UI.company, UI.city, UI.phone_1, UI.address_1 , O.username, AT.address_type2  
    FROM #__{vm}_orders AS O 
    INNER JOIN #__{vm}_order_item AS OI ON OI.order_id=O.order_id AND OI.order_item_sku LIKE '%" . $product_sku_filter . "%'
    LEFT JOIN #__{vm}_order_user_info AS AT ON AT.order_id = O.order_id AND `AT`.`address_type`='ST'
    LEFT JOIN #__{vm}_order_user_info AS UI ON O.order_id=UI.order_id WHERE  UI.address_type='BT'";
    
    $list = $q . " GROUP BY O.order_id ORDER BY O.order_id  DESC LIMIT $limitstart, " . $limit;

    $count = "SELECT count( O.order_id) as num_rows FROM #__{vm}_orders AS O 
    INNER JOIN #__{vm}_order_item AS OI ON OI.order_id=O.order_id AND OI.order_item_sku LIKE '%" . $product_sku_filter . "%'";
}



echo "<!--<br>List query:" . $list . "
    count: $count<br/><br/>LIMIT $limit-->";
f($list);

$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");

echo '<div id="query-info" style="display:none" >Pagination query: ' . $count . "====" . $num_rows;
echo "<br>List query:" . $list . "
    count: $count<br/><br/>LIMIT/NUM ROWS $limit/$num_rows</div>";
f($count);
//==============================================================================================================
// Create the Page Navigation
$pageNav = new vmPageNav($num_rows, $limitstart, $limit);

// Create the List Object with page navigation
$listObj = new listFactory($pageNav);

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_ORDER_LIST_LBL, IMAGEURL . "ps_image/orders.gif", $modulename, "order_list");
$query = "SELECT partner_id, partner_name FROM tbl_local_parthners ORDER BY partner_name ASC";
$database->setQuery($query);
$partners = $database->loadObjectList();

$query = "SELECT partner_id, partner_price FROM tbl_local_parthners ORDER BY partner_name ASC";
$database->setQuery($query);
$partners_price = $database->loadObjectList();

$query = "SELECT state_2_code, state_name FROM #__vm_state where country_id=13";
$database->setQuery($query);
$shippingProvincesRows = $database->loadObjectList();
$oProvinces = new stdClass;
$oProvinces->state_2_code = "";
$oProvinces->state_name = " - All Provinces - ";
$shippingProvinces = array_merge([(object) [
        'state_2_code' => '',
        'state_name' => ' - All Provinces - '
    ],
    (object) [
        'state_2_code' => 'NZ',
        'state_name' => 'New Zealand'
    ]
        ]
        , $shippingProvincesRows);


$query = "SELECT warehouse_code, warehouse_name,timezone FROM #__vm_warehouse where published=1 ORDER BY warehouse_name ASC";
$database->setQuery($query);
$rows = $database->loadObjectList();

$oWareHouse = new stdClass;
$oWareHouse->warehouse_name = " - All Warehouse - ";
$oWareHouse->warehouse_code = "";

$oWareHouse2 = new stdClass;
$oWareHouse2->warehouse_name = "NO WAREHOUSE ASSIGNED";
$oWareHouse2->warehouse_code = "NOWAREHOUSEASSIGNED";

$aWareHouse = array();
$aWareHouse[0] = $oWareHouse;
$aWareHouse[1] = $oWareHouse2;

$warehouseFilterRows = array_merge($aWareHouse, $rows);

$q = "SELECT * FROM jos_vm_deliveries";
$database->setQuery($q);
$deliveries = $database->loadAssocList();
foreach ($deliveries as $k => $a) {
    $parts = parse_url($a['cancel_endpoint']);
    parse_str($parts['query'], $output);
    $deliveries_arr[$a['name']]['name'] = $a['name'];
    $deliveries_arr[$a['name']]['id'] = $a['id'];
    $deliveries_arr[$a['name']]['send'] = $a['sent_endpoint'] . "?delivery_id=" . $a['id'] . "&sender=" . $my->username;
    $deliveries_arr[$a['name']]['cancel'] = $a['cancel_endpoint'] . (($output['task']) ? "&" : "?") . "sender=" . $my->username;
}


$q = "SELECT * FROM jos_vm_order_status WHERE publish='1'";
$database->setQuery($q);
$statuses_list_arr = $database->loadObjectList();
$statuses_list = "<select name='status_list' id='status_list'>";
$statuses_list .= "<option value='none'>Select Status</option>";
foreach ($statuses_list_arr as $a) {

    $statuses_list .= "<option value='$a->order_status_code'>$a->order_status_name</option>";
}
$statuses_list .= "</select>";


if ($my->prevs->warehouse_only == false) {
    echo "<div class=\"form-inline\" role=\"form\">
<div class=\"form-group\"><strong>Warehouse Filter:</strong> " . mosHTML::selectList($warehouseFilterRows, "warehouse_filter", "size='1'  class='form-control input-sm' onchange='document.adminForm.action=\"\";document.adminForm.submit()'", "warehouse_code", "warehouse_name", $warehouse_filter) . "</div>
<div class=\"form-group\"><strong>Shipping Province Filter:</strong> " . mosHTML::selectList($shippingProvinces, "shipping_province_filter", "size='1'  class='form-control input-sm' onchange='document.adminForm.action=\"\";document.adminForm.submit()'", "state_2_code", "state_name", $shipping_province_filter) . "</div>
  <div class=\"form-group\"><strong> Order Condition: </strong>
                                        <select class=\"form-control input-sm\" name=\"filter_condition\" onchange='document.adminForm.action=\"\";document.adminForm.submit()'>
                                            <option value=''>--Select Condition--</option>
                                            <option value='soft_fraud' " . (($filter_condition == 'soft_fraud') ? ' selected' : '') . ">soft fraud</option>
                                            <option value='hard_fraud' " . (($filter_condition == 'hard_fraud') ? ' selected' : '') . ">hard fraud</option>
                                            <option value='inadequate_customer_behavior' " . (($filter_condition == 'inadequate_customer_behavior') ? ' selected' : '') . ">inadequate customer behavior</option>
                                            <option value='fair_chargeback_suspecting' " . (($filter_condition == 'fair_chargeback_suspecting') ? ' selected' : '') . ">fair chargeback suspecting</option>
                                        </select>
                                        </div>
                                        


</div>
		<p>
			<a href='index2.php?option=com_phoneorder'><b>PLACE NEW ORDER</b></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                        <a href='/importlf.php?type=ofs&key=" . date('dmy', time()) . "' target='_blank'><b>IMPORT OFS ORDERS</b></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <a href='/importlf.php?type=lfp&key=" . date('dmy', time()) . "' target='_blank'><b>IMPORT LFP ORDERS</b></a>	
			<div id='exportResult'></div>
		</p>
		";
    ?>
    <div style="float: left;cursor: pointer;margin-bottom: 10px;    font-size: 14px;color: #C64934;font-weight: bold;">
        Check All: <input type="checkbox" style="zoom: 1.1;" class="check_all">
    </div>

    <?php
}
?>
<div  style="float: left;cursor: pointer;" id="print_orders_div" >
</div>

<?php
if ($my->prevs->warehouse_only == false) {
    ?>
    <div style='display:block;text-align:right;margin:10px 20px 10px 10px'>
        <a href="#" onclick="window.open('<?php echo $mosConfig_FastLabelUrl ?>/get_open_manifest.php?sender=<?php echo $my->username; ?>',
                        'Canpar Manifest',
                        'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=400, height=400, top=200, left=200')"><b>Close Manifest</b></a>&nbsp;
        <b>(<a href="<?php echo $mosConfig_FastLabelUrl ?>/manifest/" target="_blank">View Saved</a>)</b>
    </div>

    <?php
}
$oWareHouseRow = new stdClass;
$oWareHouseRow->warehouse_name = "NO WAREHOUSE ASSIGNED";
$oWareHouseRow->warehouse_code = "NOWAREHOUSEASSIGNED";
$aWareHouseRow = array();
$aWareHouseRow[0] = $oWareHouseRow;
$warehouseRows = array_merge($aWareHouseRow, $rows);

/*$q = "SELECT * from jos_vm_priority ORDER BY list_priority";
$database->setQuery($q);
$priority_arr = $database->loadObjectList();*/
?>
<div align="center" class="statuses">
    <?php
    $navi_db = new ps_DB;
    $q = "SELECT order_status_code, order_status_name ";
    $q .= "FROM #__{vm}_order_status WHERE vendor_id = '$ps_vendor_id'";
    $navi_db->query($q);
    while ($navi_db->next_record()) {
        if(isset($mosConfig_HideStatusInOrderListMenu) && in_array($navi_db->f("order_status_code"),$mosConfig_HideStatusInOrderListMenu)) {
            continue;
        }
        ?>

        <a href="<?php $sess->purl($_SERVER['PHP_SELF'] . "?page=$modulename.order_list&show=" . $navi_db->f("order_status_code")) ?>"
            <?php echo ($show == $navi_db->f("order_status_code")) ? 'class="active_status"' : ''; ?>>
            <b><?php echo $navi_db->f("order_status_name") ?></b></a>
        |
        <?php
    }
    ?>
<!--    <a href="--><?php //$sess->purl($_SERVER['PHP_SELF'] . "?page=$modulename.order_list&another_sites=onlinefloristsydney") ?><!--"><b>OFS orders</b></a> | -->
<!--    <a href="--><?php //$sess->purl($_SERVER['PHP_SELF'] . "?page=$modulename.order_list&another_sites=localflorist") ?><!--"><b>LFP orders</b></a> |-->
    <a href="<?php $sess->purl($_SERVER['PHP_SELF'] . "?page=$modulename.order_list&show=") ?>"
        <?php echo ($show == '') ? 'class="active_status"' : ''; ?>><b><?php echo $VM_LANG->_PHPSHOP_ALL ?></b></a>
</div>
<br />
<?php
echo  mosHTML::selectList($partners_price, "partner_price", " class=' partner_price form-control nopadding' size='1' id='partner_price' style='display:none' ", "partner_id", "partner_price");
$listObj->writePagination();
$listObj->startTable();

// these are the columns in the table
$columns = Array("#" => '',
    "" => "", //"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => '',
    'Order ID' => 'width=5%',
    'Delivery' => 'width=7%',
     'Occasion'=>'width=6%',
    'VIEW' => '',
    'Label' => '',
    'Label2' => '',
    'NEW FORM' => '',
    'SP' => '',
    'GIFT' => '',
    'Carrier' => '',
    'Shipstation is enabled' => '',
    $VM_LANG->_PHPSHOP_ORDER_PRINT_NAME . " (Operator)" => '',
    //'Recipient'=>'',
    $VM_LANG->_PHPSHOP_ORDER_LIST_CDATE . "&" . $VM_LANG->_PHPSHOP_ORDER_LIST_MDATE => 'width=10%',
    'Create Date By Warehouse Timezone' => 'width=10%',
    $VM_LANG->_PHPSHOP_ORDER_LIST_STATUS => '',
    //'Delivery Company' => '',
    //'Warehouse & Priority' => '',
    'Warehouse & Address Type' => '',
    $VM_LANG->_PHPSHOP_UPDATE => 'width=15%',
    //'Rate'=>'width=10%',
    "Total" => 'width=7%');
$listObj->writeTableHeader($columns);

$db->query($list);
$i = 0;
while ($db->next_record()) {

    $listObj->newRow();
    //$db->f("order_id")

    if ($db->f('partner_id')) {
        $displaystyle = '';
    } else {
        $displaystyle = " style='display:none' ";
    }
    //echo "disp:$displaystyle";
    // The row number
    $listObj->addCell($pageNav->rowNumber($i));

    // The Checkbox
    $tmp_cell = mosHTML::idBox($i, $db->f("order_id"), false, "order_id");

    $listObj->addCell($tmp_cell);

    $tmp_cell = "<a href=\"#\" onclick=\"return false;\" class=\"order-detail\" rel=\"" . $i . "\" id=\"" . $db->f("order_id") . "\">" . sprintf("%08d", $db->f("order_id")) . "</a><br />";

    if ($db->f("shopper_group_name")) {
        $tmp_cell .= 'Corporate (' . $db->f("shopper_group_name") . ')';
    }

    if ((int) $db->f('partner_order_id') > 0) {
        $tmp_cell .= '<span style="background:yellow">' . $db->f('partner_name') . ' [' . $db->f('partner_order_id') . ']</span>';
    }
    
    if ($db->f("suborder")) {
        $tmp_cell .= 'Subscription';
    }


    $listObj->addCell($tmp_cell);

    $tmp_cell = '<span id="ddate_list_' . $db->f("order_id") . '">' . date('d-M-Y', strtotime($db->f('ddate'))) . '</span>';
    $listObj->addCell($tmp_cell);

    $tmp_cell = '<span ';
    $order_occasion_name='';
    foreach($rowsOccasion as $r){
        if($r->order_occasion_code == $db->f('customer_occasion')){
            $order_occasion_name =$r->order_occasion_name;
        }
    }

    if ($order_occasion_name == 'Funeral') {
        $tmp_cell .= 'style="border: 1px solid black; padding: 5px;"';
    }
    $tmp_cell .= '>' . $order_occasion_name . '</span>';
    $listObj->addCell($tmp_cell);

    $details_url = $sess->url($_SERVER['PHP_SELF'] . "?page=order.order_printdetails2&amp;order_id=" . $db->f("order_id") . "&amp;no_menu=1");
    $details_url = defined('_PSHOP_ADMIN') ? str_replace("index2.php", "index3.php", $details_url) : str_replace("index.php", "index2.php", $details_url);
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\">";
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
    $listObj->addCell($details_link);


    $details_url = $sess->url($_SERVER['PHP_SELF'] . "?page=order.order_printlabel&amp;order_id=" . $db->f("order_id") . "&amp;no_menu=1");
    $details_url = defined('_PSHOP_ADMIN') ? str_replace("index2.php", "index3.php", $details_url) : str_replace("index.php", "index2.php", $details_url);
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,width=640,height=480,directories=no,location=no');\">";
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
    $listObj->addCell($details_link);

    $details_url = $sess->url($_SERVER['PHP_SELF'] . "?page=order.order_printlabel2&amp;order_id=" . $db->f("order_id") . "&amp;no_menu=1");
    $details_url = defined('_PSHOP_ADMIN') ? str_replace("index2.php", "index3.php", $details_url) : str_replace("index.php", "index2.php", $details_url);
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,width=640,height=480,directories=no,location=no');\">";
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
    $listObj->addCell($details_link);

    $details_url = $sess->url($_SERVER['PHP_SELF'] . "?page=order.order_perforatelabels&amp;order_id=" . $db->f("order_id") . "&amp;no_menu=1");
    $details_url = defined('_PSHOP_ADMIN') ? str_replace("index2.php", "index3.php", $details_url) : str_replace("index.php", "index2.php", $details_url);
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,width=640,height=480,directories=no,location=no');\">";
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
    $listObj->addCell($details_link);

    $details_url = $sess->url($_SERVER['PHP_SELF'] . "?page=order.order_instructions&amp;order_id=" . $db->f("order_id") . "&amp;no_menu=1");
    $details_url = defined('_PSHOP_ADMIN') ? str_replace("index2.php", "index3.php", $details_url) : str_replace("index.php", "index2.php", $details_url);
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,width=640,height=480,directories=no,location=no');\">";
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
    $listObj->addCell($details_link);

    $details_url = $sess->url($_SERVER['PHP_SELF'] . "?page=order.order_printablegiftcard.php&amp;order_id=" . $db->f("order_id") . "&amp;no_menu=1");
    $details_url = defined('_PSHOP_ADMIN') ? str_replace("index2.php", "index3.php", $details_url) : str_replace("index.php", "index2.php", $details_url);
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,width=640,height=480,directories=no,location=no');\">";
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
    $listObj->addCell($details_link);


    $tmp_cell = $db->f('first_name') . ' ' . $db->f('last_name');
    if ($perm->check('admin') && defined('_PSHOP_ADMIN')) {
        $url = $_SERVER['PHP_SELF'] . "?page=admin.user_form&amp;user_id=" . $db->f("user_id");
        $tmp_cell = '<a href="' . $sess->url($url) . '">' . $tmp_cell . '</a>';
    }

    if ((int) $db->f('partner_order_id') > 0) {
        $details_link = "<div class='button-container'>";
        $details_link .= "<a href='#' class='button-link order-" . $db->f("order_id") . " carrier-NzPost' data-order-id='" . $db->f("order_id") . "'' data-action='NzPost' style='margin-right: 10px;'>";
        $details_link .= "<img src='$mosConfig_live_site/images/M_images/carriers/nzpost.png'  /></a>";
        $details_link .= "</div>";
        $listObj->addCell($details_link);
    } else {
        $listObj->addCell('');
    }
    $listObj->addCell('<img src="images/'.(($db->f("block_shipstation") == '1')?'publish_x.png':'tick.png').'" width="12" height="12" border="0" />');
    $listObj->addCell($tmp_cell . " (" . $db->f("operator") . ") ");

    $listObj->addCell(strftime("%d-%b-%y %H:%M", $db->f("cdate")) . '<br />'
            . strftime("%d-%b-%y %H:%M", $db->f("mdate")));

    $warehouseTimeZone = 'Australia/Sydney';
    foreach ($warehouseRows as $r) {
        if ($r->warehouse_code == $db->f("warehouse") && $r->timezone) {
            $warehouseTimeZone = $r->timezone;
        }
    }

    $date = new DateTime(date("d-M-y H:i", $db->f("cdate")));
    $date->setTimezone(new DateTimeZone($warehouseTimeZone));
    $listObj->addCell($date->format('d-M-y H:i (e)'));

    $score = $db->f("score");
    if ($score == 0) {
        $btn = 'btn-danger';
    } elseif ($score > 0 && $score < 100) {
        $btn = 'btn-warning';
    } else {
        $btn = 'btn-success';
    }
    $orderStatus = "<select name=\"order_status\" class=\"inputbox form-control nopadding order_list_status_{$db->f('order_id')}\" id='order_status$i' size='1'>\n";
    foreach ($statuses_list_arr as $s) {
        $orderStatus .= "<option value=\"" . $s->order_status_code . "\"";
        if ($db->f("order_status") == $s->order_status_code)
            $orderStatus .= " selected=\"selected\">";
        else
            $orderStatus .= ">";
        $orderStatus .= $s->order_status_name . "</option>\n";
    }
    $orderStatus .= "</select>\n";
    $orderStatus .= mosHTML::selectList($partners, "partner$i", " class=' partner form-control nopadding' size='1' id='partner$i' $displaystyle ", "partner_id", "partner_name", ($db->f("partner_id") ? $db->f("partner_id") : ''));

    if ($db->f('partner_name') != '') {
        $orderStatus .='<br/><input type="button" class="button btn btn-xs btn-info address_verification_nzpost" order_id = "' . $db->f("order_id") . '"  list_id = "' . $i . '" value="Check Address in NzPost" />';
    } else {
        $orderStatus .='<br/><input type="button" class="button btn btn-xs ' . $btn . ' address_verification score' . $db->f("order_id") . '" order_id = "' . $db->f("order_id") . '"  list_id = "' . $i . '" value="Address Match ' . $score . '%" />';
    }

    $listObj->addCell($orderStatus);
    $color_array = array(
        "black" => "BLACK",
        "orange" => "ORANGE",
        "lime" => "LIME",
        "red" => "RED",
        "brown" => "Brown",
        "not_valid" => "NOT VALID COLOUR"
    );
    $color_arr = array();
    $j = 0;
    foreach ($color_array as $k => $v) {
        $color_list = new stdClass;
        $color_list->color_name = $v;
        $color_list->color_code = $k;
        $color_arr[$j] = $color_list;
        $j++;
    }

    $delivery_html = '<div order_id="' . $db->f("order_id") . '" class="delivery_loader"></div>';

    
    //--------replace priority list with address type, #5990, YI-------------
    $address_array=array(
        "Home/Residence",
        "Business",
        "Hospital",
        "School",
        "Funeral Home",
        "Nursing/Retirement Home",
        "Place of Worship" ,
        "Hotel",
    );
    
    $addressType = "<select name=\"addressType\" class=\"inputbox form-control nopadding\"  size='1' id='addressType$i'>\n";
    foreach ($address_array as $t) {
        $addressType .= "<option value=\"" . $t . "\"";
        if ($db->f("address_type2") == $t)
            $addressType .= " selected=\"selected\">";
        else
            $addressType .= ">";
        $addressType .= $t . "</option>\n";
    }

    
    $addressType .= "</select>\n";


    $listObj->addCell("<div class='form-inline'>" . mosHTML::selectList($warehouseRows, "warehouse$i", "size='1' class='form-control nopadding' id='warehouse$i'", "warehouse_code", "warehouse_name", $db->f("warehouse"))
            . $addressType
            . mosHTML::selectList($color_arr, "color$i", "size='1' id='color$i' class='form-control nopadding' onchange='UpdateColor(" . $i . ", " . $db->f("order_id") . ");'", "color_code", "color_name", $db->f("color")) . '<div style="text-align: center;"><img src="/images/color_loader.gif" style="display: none;" id="color_loader_' . $i . '"><img src="/images/color_ok.png" style="display: none;" id="color_ok_' . $i . '"></div></div>');
    $listObj->addCell('
            <input type="checkbox" class="inputbox notify_customer notify_customer_click_' . $i . '" value="" id="' . $i . '"  /> N/C
            <input type="checkbox" class="inputbox notify_warehouse" value="" id="' . $i . '"  /> N/W
            <span style="display:' . (!empty($db->f("delivery_email")) ? 'inline' : 'none') . '">
                <input type="checkbox" title="Notify Recipient?" class="inputbox notify_recipient" value="" id="' . $i . '"/> N/R
            </span>
           <div class="updatestatusclear"></div>
						<!--<input type="checkbox" class="inputbox notify_supervisior" value="" id="' . $i . '" />Notify Supervisior?<br />-->
						<div id ="ajaxloader' . $i . '" style="display:none;font: bold 11px Tahoma;color:#FF6600;"></div>
						<div id ="ajaxaction' . $i . '">
							<div id ="ajaxresult' . $i . '" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:150%;"></div>
							<div id ="sendOrderToIRIS' . $i . '" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:150%;"></div>
							<input type="button" class="button update-status  btn btn-primary btn-xs" id = "' . $i . '" name="Submit" value="Update Status" />
						</div>');

    $order_total = $db->f("order_total");
    if ($my->prevs->warehouse_only && ($db->f("order_shipping") != 14.95) && ($db->f("order_shipping") > 0)) {
        $order_total = $db->f("order_total") - $db->f("order_shipping") + 14.95;
    }

    //fuck this trash - Kirill
    $soft_fraud = $hard_fraud = $inadequate_customer_behavior = $fair_chargeback_suspecting = '';

    $listObj->addCell("<span id='total_for_order_" . $db->f("order_id") . "'><b>" . $CURRENCY_DISPLAY->getFullValue($order_total) . "</b></span>" . "<input id='order_total_forpartner" . $i . "' name='order_total_forpartner" . $i . "' type='hidden' value='" . $db->f("order_total") . "'><br><div id ='order_condition'>" . $soft_fraud . $hard_fraud . $inadequate_customer_behavior . $fair_chargeback_suspecting . "</div>");

    $listObj->newRow();
    $listObj->addCell('<div id ="ajaxorderresult' . $db->f("order_id") . '" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
						<div class="orderdetailinfo" id="orderdetailinfo' . $db->f("order_id") . '" listId="'.$i.'" style="text-align:left;background-color:#FFF;"></div>'
            , "colspan='20'");


    $form_code .= '	<input type="hidden" name="current_order_status' . $i . '" id="current_order_status' . $i . '" value="' . $db->f("order_status") . '" />
					<input type="hidden" name="current_warehouse' . $i . '" id="current_warehouse' . $i . '" value="' . $db->f("warehouse") . '" />
					<input type="hidden" name="current_priority' . $i . '" id="current_priority' . $i . '" value="' . $db->f("priority") . '" />
					<input type="hidden" name="notify_customer' . $i . '" id="notify_customer' . $i . '" value="N" />
					<input type="hidden" name="notify_recipient' . $i . '" id="notify_recipient' . $i . '" value="N" />
	                <input type="hidden" name="notify_warehouse' . $i . '" id="notify_warehouse' . $i . '" value="N" />
	             <!--   <input type="hidden" name="notify_supervisior' . $i . '" id="notify_supervisior' . $i . '" value="N" />-->
					<input type="hidden" name="order_id' . $i . '" id="order_id' . $i . '" value="' . $db->f("order_id") . '" />';

    $i++;
}
$listObj->writeTable();

$listObj->endTable();

$aUPSOrderId = mosGetParam($_POST, 'order_id');
$sUPSOrderId = mosGetParam($_POST, 'ups_order_id', "");

if (count($aUPSOrderId)) {
    if ($sUPSOrderId) {
        $sUPSOrderId = $sUPSOrderId . "," . implode(",", $aUPSOrderId);
    } else {
        $sUPSOrderId = implode(",", $aUPSOrderId);
    }
}

//echo $sUPSOrderId."---------------------------";
echo $form_code . '<input type="hidden" name="ups_order_id" value="' . $sUPSOrderId . '" />';


$listObj->writeFooter($keyword, "&show=" . $show . "&another_sites=" . $another_sites . "",false);
?>
<div id="modalDiv" style="position:absolute;display:none;top:250px; left:40% ;background: #fff;border:1px solid black;">
    <input id='id_update' type='hidden' value=''>
    <br>
    <div id="recomenden_val"></div>
    <label for='price_partner'>Price </label>
    <input id='price_partner' name='price_partner' type='text'  value=''/>

    <input type='radio' id='p80' name='percent_off' value='80'onClick='SetPartner_price(80);'>80%
    <input type='radio' id='p90' name='percent_off' value='90'onClick='SetPartner_price(90);'>90%
    <input type='radio' id='pRec' name='percent_off' checked value=''onClick='SetPartner_price(100);'>Recomended
    <input type='button' name='save_partner' value='Ok' onClick='SavePartner();'>
</div>


<style>
    .nopadding {
        padding: 0 !important;
        margin: 0 !important;
        width: 150px;
    }
    div.form-inline {
        margin-bottom: 5px;
    }
    .updatestatusclear{
        height: 8px;
    }
    .form-inline .form-group {
        margin-right: 2px;
        margin-top: 3px;
    }
    .result_table tr td:first-child{
        font-weight: bold;
        font-size: 18px;
    }
    .result_table {
        width: 570px;
    }
    .result_table td{
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    .modalDialog {
        position: fixed;
        font-family: Arial, Helvetica, sans-serif;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(0,0,0,0.8);
        z-index: 1;
        opacity: 1;
        -webkit-transition: opacity 400ms ease-in;
        -moz-transition: opacity 400ms ease-in;
        transition: opacity 400ms ease-in;
    }
    .modalDialog > div {
        width: 750px;
        position: relative;
        margin: 10% auto;
        padding: 20px 0px 13px 0px;
        border-radius: 12px;
        background: #fff;
    }
    .close:hover  {
        background: #00d9ff;
        cursor: pointer
    }

    .close {
        background: #606061;
        color: #FFFFFF;
        line-height: 25px;
        position: absolute;
        right: 1px;
        z-index: 2;
        text-align: center;
        top: 0px;
        width: 24px;
        text-decoration: none;
        font-weight: bold;
        -webkit-border-radius: 12px;
        -moz-border-radius: 12px;
        border-radius: 12px;
        -moz-box-shadow: 1px 1px 3px #000;
        -webkit-box-shadow: 1px 1px 3px #000;
        box-shadow: 1px 1px 3px #000;
    }
    #openModal{
        display: none;
    }

    #popup_details {
        text-align: center;
    }

    #popup_details input {
        font-size: 10px;
        border: 1px solid silver;
    }

    a.delivery_company {
        width: 60px;
        display: block;
        text-align: center;
        border-radius: 10px;
    }
    a.delivery_company:hover {
        background-color: #d3fded;
    }
    a.delivery_company.unactive {
        background-color: #e1e110;
    }
    a.delivery_company.default {
        background: #dbc3ed;
        text-align: center;
    }
    a.delivery_company img {
        max-width: 100%;
        border: 1px solid #737373;
        padding: 0px 5px;
        border-radius: 10px;
    }
    a.delivery_company.default img {
    }
    a.delivery_company.default:hover {
        background-color: #b58fff;
    }
    a.delivery_company.unactive img {
        border: 1px solid #bbb8b8;
        -webkit-filter: grayscale(100%);
        filter: grayscale(100%);
    }
    .deliveries_company {
        text-align: center;
        margin: 10px 0px;
        padding: 0px 10px;
    }
    .deliveries_company a {
        overflow: hidden;
        display: block;
        margin: 10px;
        background-color: #dbc3ed;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
    }
    .deliveries_popup img {
        height: 75px;
        max-width: 100%;
    }
    .deliveries_company:hover a {
        background-color: #b58fff;
    }
    .delivery_loader {
        display: none;
        width: 50px;
        height: 50px;
        background: url(/templates/bloomex7/images/deliveries/delivery_loader.gif);
    }

    .button-container {
        display: flex;
    }


    .button-link {
        display: inline-block;
        color: #fff;
        border: none;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }


    .button-link:hover {
        transform: scale(1.1);
    }

    .button-link:active {
        transform: scale(0.9);
    }

</style>

<div id="openModal"  class="modalDialog">
    <div>
        <p  title="Close" class="close close_popup">X</p>
        <div id="popup_title"></div>
        <div id="popup_details"></div>
    </div>
</div>

<script type="text/javascript">
    var sender = "<?php echo $my->username; ?>";
    var warehouse_only = "<?php echo $my->prevs->warehouse_only ? 'true' : 'false'; ?>";
    var statuses_list = "<?php echo $statuses_list; ?>";
    var sImgLoading = "<?php echo $sImgLoading; ?>";
    var deliveries_arr = JSON.parse('<?php echo json_encode($deliveries_arr); ?>');
    $(".address_verification").click(function ()
    {
        var order_id = $(this).attr("order_id");
        var url = '/scripts/deliveries/fedex/av.php?option=AddressValidation&order_id=' + order_id;
        var child = window.open(url, '_blank');
        var timer = setInterval(checkChild, 100);
        function checkChild() {
            if (child.closed) {
                window.location.reload()
                clearInterval(timer);
            }
        }


    });

    $(".address_verification_nzpost").click(function ()
    {
        var order_id = $(this).attr("order_id");
        var url = '/scripts/deliveries/nzpost/AddressValidation.php?order_id=' + order_id;
        var child = window.open(url, '_blank');
    });

    var status_sent_nzpost =  "<?php echo $mosConfig_status_sent_nzpost; ?>";
    var status_cancel_nzpost =  "<?php echo $mosConfig_status_cancel_nzpost; ?>";
</script>

<script type="text/javascript" src="/administrator/components/com_virtuemart/html/order.order_list.js?ver=11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>