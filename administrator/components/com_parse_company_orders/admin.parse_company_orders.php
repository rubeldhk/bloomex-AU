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

global $mosConfig_absolute_path;

require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php" );
require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );


$act			= mosGetParam( $_REQUEST, "act", "" );
$cid 			= josGetArrayInts( 'cid' );
$step			= 0;

//die($act);
switch ($act) {		
	
		
	//=============================================================================================
	default:
		switch ($task) {	
                    case 'change_status':
                        change_status();
                    break;
            case 'change_operator':
                change_operator();
                break;
			case 'remove':
				removeCompanyParseOrders( $cid, $option );
				break;
            case 'editA':
                editCompanyParseOrders( $id, $option );
                break;

			case 'cancel':
				cancelCompanyParseOrders();
				break;
            case 'parse_xlsx':
                parse_xlsx( );
                break;
            case 'save_parsed_results':
                save_parsed_results( );
                break;
            case 'pay_now':
                pay_now( );
                 break;
            case 'send_payment_link':
                send_payment_link( );
                break;

			default:
				showCompanyParseOrders( $option );
				break;
		}
		break;
	
}

        function change_operator()
        {
            global $database, $my, $mosConfig_offset;

            $timestamp = time() + ($mosConfig_offset * 60 * 60);
            $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
            $id = (int)$_POST['id'];
            $operator_code= $_POST['operator'];

            $query = "SELECT * FROM `tbl_company_parse_orders` WHERE `id`=".$id."";
            $database->setQuery($query);
            $database->query();
            $parse_result = false;
            $database->loadObject($parse_result);

            if ($parse_result)
            {

            $query = "SELECT order_status_code FROM `jos_vm_order_status` WHERE `order_status_name` like '".$parse_result->status."'";
            $database->setQuery($query);
            $database->query();
            $status_result = $database->loadResult();
            $status = 'P';
            if($status_result){
                $status = $status_result;
            }
                $query = "SELECT * FROM `jos_vm_order_occasion` WHERE `order_occasion_code`='".$operator_code."'";
                $database->setQuery($query);
                $database->query();
                $operator_result = false;
                $database->loadObject($operator_result);

                if ($operator_result)
                {

                    if ($parse_result->orders) {
                        $orders = unserialize($parse_result->orders);
                        if ($orders) {
                            foreach ($orders as $order) {
                                $query = "SELECT operator_code FROM tbl_order_operator WHERE order_id = '".$order."' and operator_code !='0' ";;
                                $database->setQuery($query);
                                $r = $database->loadResult();

                                if($r){
                                    $query = "UPDATE `tbl_order_operator` SET `operator_code`='".$operator_code."' WHERE order_id = '".$order."'";
                                    $database->setQuery($query);

                                    if (!$database->query())
                                    {
                                        $return['result'] = false;
                                        $return['error'] = $database->getErrorMsg();
                                        exit(0);
                                    }
                                }else{
                                    $query = "INSERT INTO tbl_order_operator (	order_id,
                                                    operator_code,cdate)
                                                VALUES ('$order',
                                                '$operator_code','".$mysqlDatetime."')";
                                    $database->setQuery($query);
                                    if (!$database->query())
                                    {
                                        $return['result'] = false;
                                        $return['error'] = $database->getErrorMsg();
                                        exit(0);
                                    }
                                }


                                $query_insert_history = "INSERT INTO #__vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments, user_name)
                                            VALUES ('$order',
                                                    '{$status}',
                                                    '".$mysqlDatetime."',
                                                    1,
                                                'Change Operator from corporate orders.', '".$database->getEscaped($my->username)."')";
                                                        $database->setQuery($query_insert_history);
                                                        if (!$database->query()) {
                                                            $text=$query_insert_history."<br>".$database->getErrorMsg();
                                                            $out = array('result'=>'error','msg'=>$text);
                                                            exit(json_encode($out));
                                                        }



                            }
                            $return['result'] = true;
                        }else{
                                $return['result'] = false;
                                $return['error'] = 'orders list saved in wrong format.';

                        }
                    }else{
                        $return['result'] = false;
                        $return['error'] = 'orders list is empty.';
                    }


                }
                else
                {
                    $return['result'] = false;
                    $return['error'] = 'Operator is not exists.';
                }
            }
            else
            {
                $return['result'] = false;
                $return['error'] = 'Company order is not exists.';
            }

            echo json_encode($return);

            exit(0);
        }
function change_status()
{
    global $database, $my, $mosConfig_offset;
    
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    $id = (int)$_POST['id'];
    $id_status = (int)$_POST['status'];

    $query = "SELECT * FROM `tbl_company_parse_orders` WHERE `id`=".$id."";
    $database->setQuery($query);
    $database->query();
    $parse_result = false;
    $database->loadObject($parse_result);
    
    if ($parse_result)
    {
        $query = "SELECT * FROM `jos_vm_order_status` WHERE `order_status_id`=".$id_status."";
        $database->setQuery($query);
        $database->query();
        $status_result = false;
        $database->loadObject($status_result);
        
        if ($status_result)
        {
            $query = "UPDATE `jos_vm_orders` SET `order_status`='".$status_result->order_status_code."' WHERE `order_id` IN (".implode(',', unserialize($parse_result->orders)).")";
            $database->setQuery($query);
            
            if (!$database->query())
            {
                $return['result'] = false;
                $return['error'] = $database->getErrorMsg();
            }
            else
            {
                $history_inserts = array();

                foreach (unserialize($parse_result->orders) as $order_id)
                {
                    $history_inserts[] = "(".$order_id.", '".$status_result->order_status_code."', '".$mysqlDatetime."', 'Change status from corporate orders.', '".$database->getEscaped($my->username)."')";
                }
                
                $query = "UPDATE `tbl_company_parse_orders` SET `status`='".strtolower($status_result->order_status_name)."' WHERE `id`=".$id."";
                $database->setQuery($query);
                
                if (!$database->query())
                {
                    $return['result'] = false;
                    $return['error'] = $database->getErrorMsg();
                }
                else
                {
                    $query = "INSERT INTO `jos_vm_order_history` 
                    (
                        `order_id`, `order_status_code`, `date_added`, `comments`, `user_name`
                    )
                    VALUES ".implode(',', $history_inserts)."";
                    $database->setQuery($query);

                    if (!$database->query())
                    {
                        $return['result'] = false;
                        $return['error'] = $database->getErrorMsg();
                    }
                    else
                    {
                        $return['result'] = true;
                    }
                }
            }
        }
        else
        {
            $return['result'] = false;
            $return['error'] = 'Status is not exists.';
        }
    }
    else
    {
        $return['result'] = false;
        $return['error'] = 'Company order is not exists.';
    }
    
    echo json_encode($return);
                    
    exit(0);
}



function custom_sort($a,$b) {
    return $a['address']>$b['address'];
}
function sort_orders($orders){

    $lines = array();
    if($orders){
        usort($orders, "custom_sort");
        $j=0;
        
        $items_arr=array();
        //$deluxe_supersize_arr=array();

        foreach($orders as $k=>$line){

            $model = new mosCompanyParseOrders( );
            $check = $model->check_sku($orders[$k]['shipping_product_sku']);
            if($orders[$k] && $orders[$k]['shipping_product_sku']!='' && $check){
                if($lines[$j-1] && $lines[$j-1]['address']==$orders[$k]['address']){
                    
                    $lines[$j-1]['delivery_fee'] += $orders[$k]['delivery_fee'];
      
                    if($orders[$k]['shipping_product_sku']){
                        if(!$orders[$k]['quantity'] || $orders[$k]['quantity']<=0){
                            $orders[$k]['quantity']=1;
                        }
                        if(!is_array($lines[$j-1]['sku'])){
                            $lines[$j-1]['sku'] = array();
                        }
                        $quantity_array = array_fill(0, $orders[$k]['quantity'], $orders[$k]['shipping_product_sku']);
                        $lines[$j-1]['sku'] = array_merge($lines[$j-1]['sku'],$quantity_array);

                    }

                    /*
                    if($orders[$k]['shipping_deluxe_supersize']=='S' OR $orders[$k]['shipping_deluxe_supersize']=='D'){
                        $deluxe_supersize_arr[$j-1][$orders[$k]['shipping_product_sku']]=$orders[$k]['shipping_deluxe_supersize'];
                    }*/

                    $items_arr[$j-1][$k] = array('sku'=>$orders[$k]['shipping_product_sku'],'deluxe_supersize'=>$orders[$k]['shipping_deluxe_supersize'],'quantity'=>$orders[$k]['quantity']);

                    unset($orders[$k]);
                    continue;
                }
                else {
                    $lines[$j] = $orders[$k];
                    
                    if($orders[$k]['shipping_product_sku']){
                        if(!$orders[$k]['quantity'] || $orders[$k]['quantity']<=0){
                            $orders[$k]['quantity']=1;
                        }
                        if(!is_array($lines[$j]['sku'])){
                            $lines[$j]['sku'] = array();
                        }
                        $quantity_array = array_fill(0, $orders[$k]['quantity'], $orders[$k]['shipping_product_sku']);
                        $lines[$j]['sku'] = array_merge($lines[$j]['sku'],$quantity_array);
                        unset($lines[$j]['quantity']);
                    }

                    /*
                    if($orders[$k]['shipping_deluxe_supersize']=='S' OR $orders[$k]['shipping_deluxe_supersize']=='D'){
                        $deluxe_supersize_arr[$j][$orders[$k]['shipping_product_sku']]=$orders[$k]['shipping_deluxe_supersize'];
                    }*/

                    $items_arr[$j][$k] = array('sku'=>$orders[$k]['shipping_product_sku'],'deluxe_supersize'=>$orders[$k]['shipping_deluxe_supersize'],'quantity'=>$orders[$k]['quantity']);


                    if($orders[$k]['shipping_full_size_card']=='Y' OR $orders[$k]['shipping_full_size_card']=='YES' or $orders[$k]['shipping_full_size_card']=='+'){
                        $lines[$j]['sku']['shipping_full_size_card'] = 'Y';
                    }
                }
                if($orders[$k+1]){
                    if($orders[$k]['address']==$orders[$k+1]['address']){
                        
                        $lines[$j]['delivery_fee'] += $orders[$k+1]['delivery_fee'];

                        $check_sku = $model->check_sku($orders[$k+1]['shipping_product_sku']);
                        if($check_sku){
                            if($orders[$k+1]['shipping_product_sku']) {
                                if (!$orders[$k + 1]['quantity'] || $orders[$k + 1]['quantity'] <= 0) {
                                    $orders[$k + 1]['quantity'] = 1;
                                }
                                if(!is_array($lines[$j]['sku'])){
                                    $lines[$j]['sku'] = array();
                                }
                                $quantity_array = array_fill(0, $orders[$k + 1]['quantity'], $orders[$k + 1]['shipping_product_sku']);
                                $lines[$j]['sku'] = array_merge($lines[$j]['sku'], $quantity_array);
                            }
                            /*
                            if($orders[$k+1]['shipping_deluxe_supersize']=='S' OR $orders[$k+1]['shipping_deluxe_supersize']=='D'){
                                $deluxe_supersize_arr[$j][$orders[$k+1]['shipping_product_sku']]=$orders[$k+1]['shipping_deluxe_supersize'];
                            }*/
                            
                            $items_arr[$j][$k+1] = array('sku'=>$orders[$k+1]['shipping_product_sku'],'deluxe_supersize'=>$orders[$k+1]['shipping_deluxe_supersize'],'quantity'=>$orders[$k + 1]['quantity']);

                            if($orders[$k+1]['shipping_full_size_card']=='Y' OR $orders[$k+1]['shipping_full_size_card']=='YES' or $orders[$k+1]['shipping_full_size_card']=='+'){
                                $lines[$j]['sku']['shipping_full_size_card'] = 'Y';
                            }
                        }
                        unset($orders[$k+1]);
                    }
                }
                $j++;
            }

        }
    }

    if(count($lines)>0){
        foreach($lines as $k=>$l){
            
            $sku_arr=array_merge($items_arr[$k]);
            $m = count($items_arr[$k]);
            $lines[$k]['shipping_product_sku']=array_count_values($l['sku']);
            foreach($lines[$k]['shipping_product_sku'] as $t=>$s){
                if($t=='Y'){
                    $sku_arr[$m]['sku']='RP-06';
                    $sku_arr[$m]['quantity']=1;
                    $sku_arr[$m]['deluxe_supersize']='';
                }
            }
            $lines[$k]['shipping_product_sku']=$sku_arr;
            unset($lines[$k]['sku']);
            unset($lines[$k]['shipping_full_size_card']);
            unset($lines[$k]['shipping_deluxe_supersize']);
        }
    }

    return $lines;

}

function save_parsed_results(){

    if($_SESSION['parsed_from_file'] && count($_SESSION['parsed_from_file'])>0){
        $model = new mosCompanyParseOrders( );
        $item_id = $model->create_orders($_SESSION['parsed_from_file']);
        if($item_id){
            $text='Parsed data saved successfully you can check it  <a style="color:blue" href="/administrator/index2.php?option=com_parse_company_orders&task=editA&hidemainmenu=1&id='.$item_id.'">Here</a>';
            $out = array('result'=>'ok','msg'=>$text);
        }else{
            $text='No data to add into database.Please check file and re-upload';
            $out = array('result'=>'error','msg'=>$text);

        }
    }else{
        $text='No data to add into database.Please check file and re-upload';
        $out = array('result'=>'error','msg'=>$text);
    }
    exit(json_encode($out));

}

function parse_xlsx(){
    global $database;
    require_once "../scripts/simplexlsx.class.php";
    $xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
    $sheetNames = array_keys($xlsx->sheetNames());
    $sheet_num = $sheetNames[0];
    $order_row = array();
    $sheet_header=array(
        'shipping_product_sku',
        'shipping_deluxe_supersize',
        'shipping_company_name',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_suite',
        'shipping_street_number',
        'shipping_street_name',
        'shipping_city',
        'shipping_zip',
        'shipping_country',
        'shipping_state',
        'shipping_phone',
        'shipping_delivery_date',
        'shipping_card_message',
        'customer_signature',
        'shipping_full_size_card',
        'shipping_customer_instructions',
        'quantity',
        'delivery_fee',
    );

    if ( 0 < $_FILES['file']['error'] ) {
        unset($_SESSION['parsed_from_file']);

        $res = array('invalid file');
    }else {
        $parsed_result_arr = $xlsx->rowsEx($sheet_num);
        
        if ($parsed_result_arr) {
            foreach($parsed_result_arr as $p){
                foreach($p as $k){
                $parsed_result[]=$k;

                }
            }
            $res=array();
            foreach($parsed_result as $sheet){

                if($sheet['name']=='B1'){
                    $res['email']=$sheet['value'];
                }
            }

            if($res['email']!=''){
                $query = "SELECT *  FROM #__vm_user_info WHERE user_email = '{$res['email']}' AND address_type = 'BT' AND user_info_id !='' limit 1";
                $database->setQuery($query);
                if ($billing = $database->loadObjectList()) {
                    $res['billing_company_name'] = $billing[0]->company;
                    $res['billing_first_name'] = $billing[0]->first_name;
                    $res['billing_last_name'] = $billing[0]->last_name;
                    $res['billing_suite'] = $billing[0]->suite;
                    $res['billing_street_number'] = $billing[0]->street_number;
                    $res['billing_street_name'] = $billing[0]->street_name;
                    $res['billing_city'] = $billing[0]->city;
                    $res['billing_zip'] = $billing[0]->zip;
                    $res['billing_country'] = $billing[0]->country;
                    $res['billing_state'] = $billing[0]->state;
                    $res['billing_phone'] = $billing[0]->phone_1;
                }
            }

            $parsed_result_num = $xlsx->rows($sheet_num);
            
            if($parsed_result_num){
                $continue=true;
                foreach($parsed_result_num as $o=>$p){
                    if($p[4]){
                        $continue=false;
                    }
                    if($continue) {
                        continue;
                    }
                    $address='';
                    foreach($sheet_header as $s=>$z){
                        if($z=='shipping_first_name' || $z=='shipping_last_name' || $z=='shipping_suite' || $z=='shipping_street_number' || $z=='shipping_street_name' || $z=='shipping_city' || $z=='shipping_state' || $z=='shipping_country' || $z=='shipping_zip'){
                            $address .=$p[$s]." ";
                        }
                        $order_row[$z]=$database->getEscaped(trim($p[$s]));
                    }
                    $order_row['address']=$address;
                    
//                    echo '<pre>';
//                    print_r($order_row);
//                    echo '</pre>';
                    $res['orders'][]=$order_row;
                }
            }
            extract($res);
            $res['orders'] = sort_orders($orders);
            
            $_SESSION['parsed_from_file']=$res;

        }
        else{
            $res = array('invalid file');

            unset($_SESSION['parsed_from_file']);
        }
    }
    exit(json_encode($res));

}

//=================================================== LandingPages OPTION ===================================================

function showCompanyParseOrders( $option ) {

	global $database, $mainframe, $mosConfig_list_limit, $mosConfig_offset;

        $timestamp = time() + ($mosConfig_offset * 60 * 60);
        
	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$filter_key 	= trim(mosGetParam( $_POST, "filter_key" ));
    $filter_status 	= mosGetParam( $_POST, "filter_status" );

    $where 	= "";
    $aWhere	= array();

    if( $filter_status && $filter_status!='-1') {
        $aWhere[]	= " status = '$filter_status' ";
    }

    if( $filter_key ) {
        $aWhere[]	= " company_name LIKE '%$filter_key%' ";
    }

    if( count($aWhere) ) $where	= " WHERE " . implode(" AND ", $aWhere);
    $model = new mosCompanyParseOrders( );
    $model->getquery($where);
    $rows = $model->rows;

    $total = $model->total;
    
        foreach ($rows as $key => $row) {
            $rows[$key]->operator = 'None';
            
            $orders = unserialize($row->orders);
            
            if (sizeof($orders) > 0) {
                $query = "SELECT `oc`.`order_occasion_name` FROM `tbl_order_operator` AS `op`
                    LEFT JOIN `jos_vm_order_occasion` AS `oc` ON `oc`.`order_occasion_code`=`op`.`operator_code`
                WHERE `op`.`order_id` IN (".implode(',', $orders).")";
                $database->setQuery($query);
                $r = $database->loadResult();

                if ($r) {
                    $rows[$key]->operator = $r;
                }
            }
        }
        
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$lists 	= array();
	$lists['filter_key']	= $filter_key;
	$lists['filter_status']	= $filter_status;
	HTML_CompanyParseOrders::showCompanyParseOrders( $rows, $pageNav, $option, $lists);
}

function send_payment_link() {

    global $mosConfig_au_stripe_secret_key,$mosConfig_absolute_path,$mosConfig_live_site,$database;
    date_default_timezone_set('Australia/Sydney');
    $mysqlDatetime = date('Y-m-d G:i:s');
    $result=[];
    if(!$_POST['id']) {
        $result = ['status' => 'Request Has Not "id" '];
        echo json_encode($result);
        exit(0);
    }

    $id=$_POST['id'];
    $row = new mosCompanyParseOrders($database);
    $row->load((int)$id);

    if(!$row->orders) {
        $result = ['status' => 'There are not orders'];
        echo json_encode($result);
        exit(0);
    }

    $row->total_price = $row->gettotalprice($row->orders);
    $billing_details = $row->getbillingdetails($row->orders);

    if(!$billing_details) {
        $result = ['status' => 'Wrong billing information'];
        echo json_encode($result);
        exit(0);
    }

    $aud = array('AUS', 'CAN', 'NZL', '');
    $currency = in_array($billing_details[0]->country, $aud) ? 'AUD' : 'USD';

    try {
        require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
        $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);

        $success_url_payment_link = "https://bloomex.com.au/account/?bulk_id=$id&payment_place=bulkpaymentlink&session_id={CHECKOUT_SESSION_ID}&mosmsgsuccess=true&mosmsg=Payment executed by Stripe Successfully";


        $orderItems[] =  [
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => number_format(abs($row->total_price), 2, ".", "") * 100,
                'product_data' => [
                    'name' => htmlentities('Bulk Order# ' . $id, ENT_QUOTES)
                ],
            ],
            'quantity' => 1
        ];
        $sessionParams = [
            "success_url" => $success_url_payment_link,
            "mode" => "payment",
            "line_items" => $orderItems,
            "expires_at" => time() + 24 * 60 * 60,
            "custom_text" => ['submit' => ['message' => 'ATTENTION!!! After paying the system will redirect to the company site. Please don\'t close this page.']],
        ];

        $stripeSession = $stripe->checkout->sessions->create($sessionParams);

        $sql_pc = "INSERT INTO tbl_stripe_orders_adm_logs 
                            (bulk_id, session_id, 
                            payment_url,order_status, date_added) 
                            VALUES ('{$id}',  '{$stripeSession->id}', '{$stripeSession->url}', 'pending_stripe','{$mysqlDatetime}')";
        $database->setQuery($sql_pc);
        $database->query();

        $result['status'] = "Payment URL <p>{$stripeSession->url}</p>";
        $googleItems = getGoogleAnalyticsItems($id);
        if(count($googleItems) > 0) {
            $_SESSION['google_analytics_items'] = $googleItems;
            $result['items'] = $googleItems;
        }
    } catch (Exception $e) {
        $payment_msg = $e->getMessage();
        $result = ['status' => $payment_msg];
    }

    echo json_encode($result);
    exit(0);

}

function pay_now() {
    global $database, $mosConfig_offset,$mosConfig_payment_centralization,$vendor_currency,$mosConfig_absolute_path,$mosConfig_test_card_numbers;
    $vendor_currency = "AUD";
    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $result = '';

    if($_POST['id']) {
        $id=$_POST['id'];
        $row = new mosCompanyParseOrders($database);
        $row->load((int)$id);
        if ($row->orders != '') {
            $row->total_price = $row->gettotalprice($row->orders);
            $billing_details = $row->getbillingdetails($row->orders);
            if ($billing_details) {

                require_once($mosConfig_absolute_path . '/components/com_ajaxorder/nab/payment.php');
                $credit_card_number = ($_POST['credit_card_number'])?$_POST['credit_card_number']:'';
                $expire_month = ($_POST['expire_month'])? $_POST['expire_month']:'';
                $expire_year = ($_POST['expire_year'])?$_POST['expire_year']:'';
                    $credit_card_security_code = ($_POST['credit_card_security_code'])?$_POST['credit_card_security_code']:'';
                    if (in_array($credit_card_number, $mosConfig_test_card_numbers)) {
                        $row->total_price = 0.05;
                    }

                $aResult = array();
                $order_number = 'blau_'.date('YmdHi').'_'.mt_rand(10000000, 99999999);
                if ($mosConfig_payment_centralization == true) {
                    $PaymentVarCentralization = array(
                        'project' => 'bloomex.com.au',
                        'order_number' => $order_number,
                        'amount' => $row->total_price,
                        'cardholder_name' => ($_POST['name_on_card'])?$_POST['name_on_card']:'',
                        'card_number' => $credit_card_number,
                        'exp_month' => sprintf('%02d', $expire_month),
                        'exp_year' => substr($expire_year, -2),
                        'cvv' => $credit_card_security_code,
                        'currency' => $vendor_currency,
                        'first_name' => $billing_details[0]->first_name,
                        'last_name' => $billing_details[0]->last_name,
                        'billing_address_line_1' => (!empty($billing_details[0]->suite) ? $billing_details[0]->suite.'#, ': '').$billing_details[0]->street_number.' '.$billing_details[0]->street_name,
                        'billing_address_line_2' => '',
                        'billing_city' => $billing_details[0]->city,
                        'billing_state' => $billing_details[0]->state,
                        'billing_country' => $billing_details[0]->country,
                        'billing_zip' => $billing_details[0]->zip,
                        'billing_phone' => $billing_details[0]->phone_1,
                        'billing_email' => $billing_details[0]->user_email,
                        'billing_ip' => $_SERVER['REMOTE_ADDR']
                    );

                    $aResult = $row->process_payment_centralization($PaymentVarCentralization);

                    $aResult['MessageInfo']['messageID'] = $aResult['order_payment_trans_id'];
                    $aResult['Payment']['TxnList']['Txn']['responseText'] = $aResult['order_payment_log'];
                    $aResult[0] = $aResult['order_payment_log'];

                    if ($aResult['approved'] == 1) {
                        $aResult['Status']['statusCode'] = 'A';
                    }

                }
                else {

                    $aData = array();
                    $aData[0] = $row->orders;
                    $aData[1] = date("YdmHiu") . "000" + 600;
                    $aData[2] = number_format($row->total_price, 2, '', '');
                    $aData[3] = $row->orders;
                    $aData[4] = $credit_card_number;
                    $aData[5] = sprintf("%02d", $expire_month) . "/" . substr($expire_year, -2, 2);
                    $aData[6] = $credit_card_security_code; //cvv
                    $aResult = processNABpayment($aData);
                }


                if ($aResult["approved"] == 1) {
                    $row->set_paid($id,$row->orders);

                    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
                    $query = "INSERT INTO `tbl_company_parse_payments`
                    (
                        `list_id`,
                        `card_mask`,
                        `amount`,
                        `trans_id`,
                        `date`
                    )
                    VALUES (
                        ".(int)$id.",
                        '".$database->getEscaped($aResult['card_mask'])."',
                        '".$database->getEscaped($row->total_price)."',
                        '".$database->getEscaped($aResult['order_payment_trans_id'])."',
                        '".$mysqlDatetime."'
                    )";

                    $database->setQuery($query);
                    $database->query();

                    $googleItems = getGoogleAnalyticsItems($id);
                    if(count($googleItems) > 0) {
                        $_SESSION['google_analytics_items'] = $googleItems;
                        $result = ['items' => $googleItems];
                    }
                    $result['status'] = "Payment Approved";
                    echo json_encode($result);
                    exit(0);
                } 
                else {
                    $result = ['status' => "<b style='font-size:12px;color:#EF000A;background-color:#FDFF00;padding:10px;line-height:200%;'>" . $aResult["order_payment_log"] . "</b>"];
                    echo json_encode($result);
                    exit(0);
                }
            }
        }
    }
    else {
        $result = ['status' => 'Request Has Not "id" '];

        echo json_encode($result);
        exit(0);
    }
}

        function getGoogleAnalyticsItems(int $id): array
        {
            global $database;

            $sql = sprintf("SELECT
            po.orders
        FROM tbl_company_parse_orders AS po
        WHERE po.id = '%s'",
                $id
            );
            $database->setQuery($sql);
            $parsedOrders = null;
            $database->loadObject($parsedOrders);

            $googleAnalyticsItems = [];
            if($parsedOrders) {
                $orders = unserialize($parsedOrders->orders);
                foreach($orders as $order) {
                    $sqlOrderItem = sprintf("SELECT 
                    *
                FROM jos_vm_order_item AS oi
                WHERE order_id='%s'",
                        $order
                    );
                    $database->setQuery($sqlOrderItem);
                    $orderItems = $database->loadObjectList();
                    $googleItems = [];
                    foreach($orderItems as $item) {
                        $googleItems[] = [
                            'item_name' => $item->order_item_name,
                            'item_id' => $item->product_id,
                            'price' => round($item->product_final_price, 2),
                            'item_category' => 'bulk_orders',
                            'quantity' => $item->product_quantity
                        ];
                    }
                    $orderSql = sprintf("SELECT 
                    *
                FROM jos_vm_orders AS o
                WHERE o.order_id='%s'",
                        $order
                    );
                    $database->setQuery($orderSql);
                    $orderItem = null;
                    $database->loadObject($orderItem);
                    $transactionInfo = [
                        'transaction_id' => 'corp_' . $orderItem->order_id,
                        'tax' => (float)$orderItem->order_tax,
                        'shipping' => round($orderItem->order_shipping, 2),
                        'coupon' => $orderItem->coupon_discount ?? ''
                    ];
                    if (count($googleItems) > 0) {
                        $googleAnalyticsItems[] = [
                            'eventName' => 'purchase',
                            'items' => $googleItems,
                            'value' => round($orderItem->order_total, 2),
                            'transaction_info' => $transactionInfo
                        ];
                    }
                }
            }

            return $googleAnalyticsItems;
        }

        function editCompanyParseOrders( $id, $option )
        {
            global $database, $my, $mosConfig_absolute_path;

            $row = new mosCompanyParseOrders($database);
            $row->load((int)$id);
            if (!$id) {
                mosRedirect('index2.php?option=com_parse_company_orders');
            }
            $row->total_price = 0;
            $row->orders_details = '';
            $row->username = $row->get_username($row->user_id);
            $row->payment = $row->get_paid($id);
            if ($row->orders != ''){
                $row->total_price = $row->gettotalprice($row->orders);
                $orders_details = $row->getordersdetails($row->orders);
                foreach($orders_details as $k=>$order_det){
                    $orders_details[$order_det->order_id] = $orders_details[$k];
                    $selected_status = $orders_details[$k]->order_status_id;
                    unset($orders_details[$k]);
                }
                $row->orders_details=$orders_details;
                //if($row->status=='corp pending' OR $row->status=='pending'){
                    $row->payment_method =  $row->getpaymentmethods();
                    $row->expire_month =  $row->listMonth("expire_month", null, " size='1' ");
                    $row->expire_year =  $row->listYear("expire_year", date("Y"), " size='1' ", 30, date("Y"));
                //}
            }
            
            $statuses = $row->getstatuses($selected_status);
            $operators = $row->getoperators($row->orders);
            HTML_CompanyParseOrders::editCompanyParseOrders( $row, $option, $statuses,$operators);
        }
function removeCompanyParseOrders( &$cid, $option ) {

        if (count( $cid )) {		
		foreach ($cid as $value) {
            $model = new mosCompanyParseOrders( );
            $model->getquery("Where id='".$value."'");
            $rows = $model->rows;
            if(count($rows)>0){
                $row=$rows[0];
                $model->deleteorders($value,$row->orders);
            }
		}
	
	}
	
	mosRedirect( "index2.php?option=$option", "Remove Item Successfully" );
}


function cancelCompanyParseOrders() {
	mosRedirect('index2.php?option=com_parse_company_orders');
}


?>