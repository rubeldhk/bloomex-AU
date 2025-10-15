<?php
/**
 * @version $Id: contact.class.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Contact
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



class mosCompanyParseOrders extends mosDBTable {
    /** @var int Primary key */
    var $id 				= null;
    /** @var string */
    var $user_id 				= null;
    /** @var string */
    var $orders 				= null;
    /** @var int */
    var $status				= null;
    /** @var int */
    var $company_name				= null;

    var $rows=array();
    var $total=0;
    /**
     * @param database A database connector object
     */
    function __construct() {
        global $database;
        parent::__construct( 'tbl_company_parse_orders', 'id', $database );
    }
    function get_paid($list_id) {
        global $database;
        
        $query = "SELECT * FROM `tbl_company_parse_payments` WHERE `list_id`=".$list_id."";
        $payment_result = false;
        $database->setQuery($query);
        $database->loadObject($payment_result);
        
        return $payment_result;
    }
    function get_username($user_id) {
        global $database;
        $query = "SELECT username FROM jos_users where id='".$user_id."'";
        $database->setQuery( $query );
        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "error";
            exit(0);
        }
        return $database->loadResult();
    }
    function getquery($where='') {
        global $database;

        $query = "SELECT * FROM tbl_company_parse_orders $where ORDER BY `orders_date` DESC";
        $database->setQuery( $query );

        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "error";
            exit(0);
        }

        $this->rows = $database->loadObjectList();
        $this->total = count($this->rows);
        $this->totalprice = $this->gettotalprice();
    }
    function getbillingdetails($orders_serial = ''){
        global $database;
        if ($orders_serial) {
            $orders = unserialize($orders_serial);
            if($orders){
                $order_str = $orders[0];
                $query = "
SELECT *
FROM jos_vm_order_user_info  
WHERE address_type='BT' AND order_id =($order_str)";

                $database->setQuery($query);
                if (!$database->query()) {
                    echo $database->getErrorMsg();
                    echo "error";
                    exit(0);
                }
                return $database->loadObjectList();
            }else{
                exit('1 invalid orders list');
            }
        }
    }
    function getordersdetails($orders_serial = '')
    {
        global $database;
        if ($orders_serial) {
            $orders = unserialize($orders_serial);
            if($orders) {
                $order_str = '';
                foreach ($orders as $order) {
                    $order_str .= $order . ",";
                }
                $order_str = rtrim($order_str, ',');
                $query = "
SELECT o.order_total,o.customer_comments,o.order_id,o.cdate,o.ddate,i.first_name,i.last_name,
i.country,i.state,i.city,i.street_name,i.street_number,i.suite,i.address_1,i.zip,i.phone_1, `s`.`order_status_name`, `s`.`order_status_id`
FROM `jos_vm_orders` as o
left join jos_vm_order_user_info as i on i.order_id=o.order_id and i.address_type='ST'
inner join jos_vm_order_status as s on s.order_status_code=o.order_status
WHERE o.order_id in ($order_str)";

                $database->setQuery($query);
                if (!$database->query()) {
                    echo $database->getErrorMsg();
                    echo "error";
                    exit(0);
                }
                return $database->loadObjectList();
            }else{
                exit('2 invalid orders list');
            }
        }
    }
    function getpaymentmethods(){
        global $database;
        $query = "SELECT creditcard_code,creditcard_name FROM #__vm_creditcard";
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        return mosHTML::selectList($rows, "payment_method", "size='1'", "creditcard_code", "creditcard_name");

    }

    function getstatuses($selected_status){
        global $database;
        $query = "SELECT `order_status_id`, `order_status_name` FROM `jos_vm_order_status`";
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        return mosHTML::selectList($rows, "order_status", "size='1'", "order_status_id", "order_status_name", $selected_status);

    }
    function getoperators($orders_str){
        global $database;
        $query = "SELECT `order_occasion_code`, `order_occasion_name` FROM `jos_vm_order_occasion` WHERE `published`='0'";
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        $selected_operator='No';
        if ($orders_str) {
            $orders = unserialize($orders_str);
            if ($orders) {
                $order_str = '';
                foreach ($orders as $order) {
                    $order_str .= $order . ",";
                }
                $order_str = rtrim($order_str, ',');
                $query = "SELECT operator_code FROM tbl_order_operator WHERE order_id in ($order_str)";;
                $database->setQuery($query);
                $r = $database->loadResult();
                if($r)
                    $selected_operator = $r;
            }
        }


        return mosHTML::selectList($rows, "operator", "size='1'", "order_occasion_code", "order_occasion_name", $selected_operator);

    }
    function listMonth($list_name, $selected_item = "", $extra = "") {
        $sString = "";
        $list = array("" => "Month",
            "01" => "January",
            "02" => "February",
            "03" => "March",
            "04" => "April",
            "05" => "May",
            "06" => "June",
            "07" => "July",
            "08" => "August",
            "09" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December");

        $sString = "<select class='{$list_name}' name='{$list_name}' {$extra}>";
        foreach ($list as $key => $value) {
            $sString .= "<option value='{$key}'>{$value}</option>";
        }

        $sString .= "</select>";
        return $sString;
    }

    function listYear($list_name, $selected_item = "", $extra = "", $max = 7, $from = 2009, $direct = "up") {
        $sString = "";

        $sString = "<select class='{$list_name}' name='{$list_name}' {$extra}>";
        for ($i = 0; $i < $max; $i++) {
            $value = $from + $i;
            $text = $from + $i;
            if ($selected_item == $value) {
                $sString .= "<option selected value='" . $value . "'>" . $text . "</option>";
            } else {
                $sString .= "<option value='" . $value . "'>" . $text . "</option>";
            }
        }

        $sString .= "</select>";
        return $sString;
    }

    function gettotalprice($orders_serial=''){
        global $database;
        if($orders_serial){
            $orders = unserialize($orders_serial);
            if($orders) {
                $order_str = '';
                foreach ($orders as $order) {
                    $order_str .= $order . ",";
                }
                $order_str = rtrim($order_str, ',');
                $query = "SELECT sum(order_total) FROM `jos_vm_orders` WHERE order_id in ($order_str)";
                $database->setQuery($query);
                if (!$database->query()) {
                    echo $database->getErrorMsg();
                    echo "error";
                    exit(0);
                }
                return $database->loadResult();
            }else{
                exit('3 invalid orders list');
            }
        }else{

            if($this->rows){
                foreach($this->rows as $k=>$row){
                    $this->rows[$k]->total_price='';
                    $this->rows[$k]->parsed_orders_count = 0;

                    if($row->orders){
                        $orders = unserialize($row->orders);
                        $order_str='';
                        $c=0;
                        if($orders) {
                            foreach ($orders as $order) {
                                $c++;
                                $order_str .= $order . ",";
                            }
                            $order_str = rtrim($order_str, ',');
                            $query = "SELECT sum(order_total) FROM `jos_vm_orders` WHERE order_id in ($order_str)";
                            $database->setQuery($query);
                            if (!$database->query()) {
                                echo $database->getErrorMsg();
                                echo "error";
                                exit(0);
                            }
                            $this->rows[$k]->total_price = $database->loadResult();
                            $this->rows[$k]->parsed_orders_count = $c;
                        }else{
                            exit('4 invalid orders list');
                        }
                    }
                }
            }

        }

    }


    function set_paid($id,$orders_serial='') {
        global $database,$mosConfig_offset,$my;
        $query = "Update tbl_company_parse_orders set status='paid' 
        WHERE id in ($id)";
        $database->setQuery($query);
        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "cant set status to Paid for id=".$id;
            exit(0);
        }
        $timestamp = time() + ($mosConfig_offset * 60 * 60);
        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
        $query_insert_history = "INSERT INTO jos_vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments, user_name) VALUES ";
        if($orders_serial && $id) {
            $orders = unserialize($orders_serial);
            if ($orders) {
                $order_str = '';
                foreach ($orders as $order) {
                    $order_str .= $order . ",";

                    $query_insert_history.="('$order',
						'A',
						'".$mysqlDatetime."',
						0,
						'Paid from Bulk Corporate orders form', '".$database->getEscaped($my->username)."'),";
                }
                $query_insert_history=rtrim($query_insert_history,',');
                $database->setQuery($query_insert_history);
                if (!$database->query()) {
                    echo $database->getErrorMsg();
                    echo "error";
                    exit(0);
                }

                $order_str = rtrim($order_str, ',');
                $sql = "UPDATE jos_vm_orders set order_status='A' WHERE order_id in ($order_str)";
                $database->setQuery($sql);
                if (!$database->query()) {
                    echo $database->getErrorMsg();
                    echo "error";
                    exit(0);
                }
            }
        }

    }
    function deleteorders($id,$orders_serial=''){
        global $database;
        if($orders_serial && $id) {
            $orders = unserialize($orders_serial);
            if($orders) {
                $order_str = '';
                foreach ($orders as $order) {
                    $order_str .= $order . ",";
                }
                $order_str = rtrim($order_str, ',');

                $sql_arr = array();
                $sql_arr[] = "DELETE  FROM jos_vm_orders WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_history WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_payment WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_user_info  WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_item  WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_item_ingredient  WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_product_type  WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_vm_order_pick_up  WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM jos_used_donations  WHERE order_id in ($order_str)";
                $sql_arr[] = "DELETE  FROM tbl_company_parse_orders  WHERE id in ($id)";

                foreach ($sql_arr as $sql) {
                    $database->setQuery($sql);
                    if (!$database->query()) {
                        echo $database->getErrorMsg();
                        echo "error";
                        exit(0);
                    }
                }

            }else{
                exit('5 invalid orders list');
            }
        }else{
            exit('There are not orders to delete');
        }
    }
    function process_payment_centralization($PaymentVarCentralization) {
        global $mosConfig_payment_centralization_url, $mosConfig_payment_centralization_auth;

        $curl = curl_init($mosConfig_payment_centralization_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_payment_centralization_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($PaymentVarCentralization));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $curl_response = curl_exec($curl);
        $json = json_decode($curl_response, true);

        return $json;
    }
    function get_country_name($country){
        global $database;
        $query="SELECT country_3_code
FROM `jos_vm_country`
WHERE `country_name` LIKE '".$country."' OR `country_2_code` LIKE '" . $country . "' OR country_3_code  LIKE '" . $country . "'";
        $database->setQuery($query);
        $res = $database->loadResult();
        if($res){
            return $res;
        }else{
            return $country;
        }

    }
    function get_state_name($state){
        global $database;
        $query="SELECT state_2_code
FROM `jos_vm_state`
WHERE `country_id` ='13'
AND `state_name` LIKE '" . $state . "' OR `state_2_code` LIKE '" . $state . "' OR state_3_code  LIKE '" . $state . "'";
        $database->setQuery($query);
        $res = $database->loadResult();
        if($res){
            return $res;
        }else{
            return $state;
        }

    }

    function check_sku($sku){
        global $database;
        $query = "SELECT product_id
            FROM `jos_vm_product`
            WHERE `product_sku` LIKE '".$sku."'";
        $database->setQuery($query);
        
        $check = $database->loadObjectList();
        if ($check) {
            return $check[0]->product_id;
        } else {
            return false;
        }
    }

    function get_tax_rate($state,$country){
        global $database;
        $query = "SELECT * FROM jos_vm_tax_rate where  
`tax_state` LIKE '".$state."'
AND `tax_country` LIKE '".$country."'";
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        return $rows;
    }
    function create_orders($parsed_from_file){
        global $database, $my, $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname;
        $timestamp = time() + ($mosConfig_offset * 60 * 60);
        $order_default_status = 'O';
        set_time_limit(60);


        $country = $this->get_country_name($parsed_from_file['billing_country']);
        $state = $this->get_state_name($parsed_from_file['billing_state']);



        $query = "SELECT user_id FROM #__vm_user_info WHERE user_email = '{$parsed_from_file['email']}' AND address_type = 'BT'";
        $database->setQuery($query);
        $user_id = intval($database->loadResult());

        $addr = $parsed_from_file['billing_suite'] . ' ' . $parsed_from_file['billing_street_number'] . ' ' . $parsed_from_file['billing_street_name'];
                
        require_once CLASSPATH.'ps_for_checkout.php';
        $ps_for_checkout = new ps_for_checkout;
        
        if (intval($database->loadResult())) {
            $query = " UPDATE #__vm_user_info
							SET address_type_name	= '-default-',
								company				= '{$database->getEscaped($parsed_from_file['billing_company_name'])}',
								last_name			= '{$database->getEscaped($parsed_from_file['billing_last_name'])}',
								first_name			= '{$database->getEscaped($parsed_from_file['billing_first_name'])}',
								middle_name			= '{$database->getEscaped($parsed_from_file['billing_first_name'])}',
								phone_1				= '{$database->getEscaped($parsed_from_file['billing_phone'])}',
                                phone_2				= '{$database->getEscaped($parsed_from_file['billing_phone'])}',
								address_1			= '{$database->getEscaped($addr)}',
								address_2			= ' ',
								city				= '{$database->getEscaped($parsed_from_file['billing_city'])}',
								state				= '{$state}',
								country				= '{$country}',
								zip					= '".$database->getEscaped(str_replace(' ', '', $parsed_from_file['billing_zip']))."',
								suite					= '{$database->getEscaped($parsed_from_file['billing_suite'])}',
								street_number					= '{$database->getEscaped($parsed_from_file['billing_street_number'])}',
								street_name					= '{$database->getEscaped($parsed_from_file['billing_street_name'])}'

					   	   WHERE user_email = '{$database->getEscaped($parsed_from_file['email'])}' AND address_type = 'BT'";

            $database->setQuery($query);
            $database->query();


        } else {

            if (!$user_id) {
                $query = "INSERT INTO #__users( name, username, email, usertype, block, gid ) VALUES( '{$database->getEscaped($parsed_from_file['email'])}', '{$database->getEscaped($parsed_from_file['email'])}', '{$database->getEscaped($parsed_from_file['email'])}' , 'Registered' , 0, 18 )";
                $database->setQuery($query);
                $database->query();
                $user_id = $database->insertid();


                $shgid_check = mosGetuserShoperGroupId($parsed_from_file['email']);
                if ($shgid_check) {
                    $q = "INSERT INTO jos_vm_shopper_vendor_xref ";
                    $q .= "(user_id,vendor_id,shopper_group_id) ";
                    $q .= "VALUES ('$user_id','1','" . $shgid_check . "')";

                    $database->setQuery($q);
                    $database->query();
                }

                $query = "INSERT INTO #__core_acl_aro( section_value, value, order_value, name, hidden ) VALUES( 'users', {$user_id}, 0, '{$database->getEscaped($parsed_from_file['email'])}', 0 )";
                $database->setQuery($query);
                $database->query();
                $aro_id = $database->insertid();

                $query = "INSERT INTO #__core_acl_groups_aro_map( group_id, section_value, aro_id ) VALUES( 18, '', {$aro_id} )";
                $database->setQuery($query);
                $database->query();
                
                $query = "INSERT INTO `jos_vm_users_rating` (`user_id`,`rate`) VALUES (".$user_id.", 3)";
                $database->setQuery($query);
                $database->query();
            }
            $user_info_id = md5($user_id . time());
            $query = "INSERT INTO #__vm_user_info( user_info_id,
															user_id,
															address_type,
															address_type_name,
															company,
															last_name,
															first_name,
															middle_name,
															phone_1,
                                                                                                                        phone_2,
															address_1,
															address_2,
															city,
															state,
															country, zip,
															user_email,
                                                                                                                        extra_field_1,
															perms, suite, street_number, street_name )
						   	   VALUES(  '" . $user_info_id . "',
						   	   			{$user_id},
						   	   			'BT',
						   	   			'-default-',
						   	   			'{$database->getEscaped($parsed_from_file['billing_company_name'])}',
						   	   			'{$database->getEscaped($parsed_from_file['billing_last_name'])}',
						   	   			'{$database->getEscaped($parsed_from_file['billing_first_name'])}',
						   	   			'{$database->getEscaped($parsed_from_file['billing_first_name'])}',
						   	   			'{$database->getEscaped($parsed_from_file['billing_phone'])}',
                                                                                '{$database->getEscaped($parsed_from_file['billing_phone'])}',
						   	   			'{$database->getEscaped($addr)}',
						   	   			' ',
						   	   			'{$database->getEscaped($parsed_from_file['billing_city'])}',
						   	   			'{$state}',
						   	   			'{$country}',
						   	   			'".$database->getEscaped(str_replace(' ', '', $parsed_from_file['billing_zip']))."',
						   	   			'{$database->getEscaped($parsed_from_file['email'])}',
                                         '{$database->getEscaped($parsed_from_file['billing_phone'])}',
										'shopper',
                                        '{$database->getEscaped($parsed_from_file['billing_suite'])}',
                                        '{$database->getEscaped($parsed_from_file['billing_street_number'])}',
                                        '{$database->getEscaped($parsed_from_file['billing_street_name'])}'


                                                                                )";
            $database->setQuery($query);
            $database->query();


        }

        $query = " SELECT SG.shopper_group_discount
						FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id
						WHERE  SVX.user_id = {$user_id} LIMIT 1";
        $database->setQuery($query);
        $ShopperGroupDiscount = $database->loadResult();

        $user_discount = 0;

        if ($ShopperGroupDiscount) {
            $user_discount = $ShopperGroupDiscount;
            $ShopperGroupDiscount = $ShopperGroupDiscount / 100;
        }
        $orders_id=array();
        foreach($parsed_from_file['orders'] as $m=>$row){
            if($row){

                //get shipping tax
                $country = $this->get_country_name($row['shipping_country']);
                $state = $this->get_state_name($row['shipping_state']);

                $delivery_fee = $row['delivery_fee'] ? floatval($row['delivery_fee']) : 14.95;
                $only_delivery_tax = 0;

                $tax = 0;


                if($row['shipping_product_sku']){
                    
                    $subtotal = 0;
                    $total = 0;
                    $query_insert_item = "INSERT INTO #__vm_order_item (   order_id,
															user_info_id,
															vendor_id,
															product_id,
															order_item_sku,
															order_item_name,
															product_quantity,
															product_item_price,
															product_final_price,
															order_item_currency,
															order_status,
															product_attribute,
															cdate,
															mdate )
					   	   VALUES ";
                    
                    $query_inserts_item = array();
                    
                    foreach($row['shipping_product_sku'] as $f=>$p){
                        $product_id=$this->check_sku($p['sku']);
                        $del_super_txt = '';
                        $del_super = 0;
                        $sql = "SELECT deluxe,supersize,petite FROM #__vm_product_options WHERE product_id = $product_id LIMIT 1";
                        $database->setQuery($sql);
                        $res = $database->loadObjectList();
                        if($res){
                            $deluxe = $res[0]->deluxe;
                            $supersize = $res[0]->supersize;
                            $petite = $res[0]->petite;
                        }

                        $select_bouquet = '';
                        
                        if ($p['deluxe_supersize'] == 'D') {
                            $del_super_txt = ' (deluxe) ';
                            $del_super = $deluxe;
                            $select_bouquet = 'deluxe';
                        }
                        if ($p['deluxe_supersize'] == "S") {
                            $del_super_txt = ' (supersize) ';
                            $del_super = $supersize;
                            $select_bouquet = 'supersize';
                        }
                        if ($p['deluxe_supersize'] == "P") {
                            $del_super_txt = ' (petite) ';
                            $del_super = $petite;
                            $select_bouquet = 'petite';
                        }
                        if ($p['deluxe_supersize'] == "SNPC") {
                            $del_super_txt = ' (supersize) ';
                            $del_super = 0;
                            $select_bouquet = 'supersize';
                        }

                        $query_product = "
                                            SELECT p.product_price ,p.saving_price,s.product_desc,p.product_currency,s.product_name,s.product_sku FROM jos_vm_product_price as p
                                            left join jos_vm_product as s  on s.product_id=p.product_id WHERE p.product_id = '$product_id'
                                            ";
                        $database->setQuery($query_product);
                        $value = $database->loadObjectList();
                        if($value){
                            $product_final_price =$product_item_price = $del_super + round($value[0]->product_price-$value[0]->saving_price,2);

                            if ($ShopperGroupDiscount > 0) {
                                $product_item_price = $product_item_price - ( $product_item_price * doubleval($ShopperGroupDiscount) );
                            }
                            $subtotal+=$p['quantity'] * $product_final_price;

                            $value[0]->product_name =$value[0]->product_name . $del_super_txt;
                            $total+=$p['quantity'] * $product_item_price;
                                                                        
                            $query_inserts_item[] = array('insert' => " ( {order_id},
                                '{$user_info_id}',
                                1,
                                " . $product_id . ",
                                '" . $database->getEscaped($value[0]->product_sku) . "',
                                '" . $database->getEscaped($value[0]->product_name) . "',
                                " . intval($p['quantity']) . ",
                                " . $product_item_price . ",
                                " . $product_final_price . ",
                                '" . $value[0]->product_currency . "',
                                '" . $order_default_status . "',
                                '" . $database->getEscaped($value[0]->product_desc) . "',
                                '{$timestamp}',
                                '{$timestamp}'
                                 )",
                                'product_id' => $product_id,
                                'quantity' => intval($p['quantity']),
                                'select_bouquet' => $select_bouquet
                            );
                        }else{
                            $text=$product_id." have not price.";
                            $out = array('result'=>'error','msg'=>$text);
                            exit(json_encode($out));
                        }
                    }

                }else{
                    $text=" There are  not parsed  products. please be sure sku names are correct.";
                    $out = array('result'=>'error','msg'=>$text);
                    exit(json_encode($out));
                }
                if (!empty($_SERVER['REMOTE_ADDR'])) {
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ip_address = "unknown";
                }
                $order_number = md5("order" . $m.$user_id . time());
                $user_info_id = md5($m.$user_id . time());
                $total+=$delivery_fee;

                $query_insert_order = "INSERT INTO #__vm_orders( user_id,
												 vendor_id,
												 order_number,
												 user_info_id,
												 order_total,
												 order_subtotal,
												 order_tax,
												 order_tax_details,
												 order_shipping,
												 order_shipping_tax,
												 coupon_discount,
												 order_currency,
												 order_status,
												 cdate,
												 mdate,
												 ddate,
												 ship_method_id,
												 customer_note,
												 customer_signature,
												 customer_occasion,
												 customer_comments,
												 ip_address,
												 coupon_code,
												 coupon_type,
												 coupon_value,
												 username )
			 
												 
												 
				   	   VALUES( 	{$user_id},
				   	   			1,
				   	   			'{$order_number}',
				   	   			'{$user_info_id}',
				   	   			{$total},
				   	   			{$subtotal},
				   	   			{$tax},
				   	   			'',
				   	   			{$delivery_fee},
				   	   		   	{$only_delivery_tax},
				   	   		   	'',
				   	   		   	'',
				   	   		   	'".$order_default_status."',
				   	   		   	" . $timestamp . ",
				   	   		   	" . $timestamp . ",
				   	   		   	'" . $row['shipping_delivery_date'] . "',
				   	   		   	'',
						   	   	'" . $database->getEscaped($row['shipping_card_message']) . "',
						   	   	'" . $database->getEscaped($row['customer_signature']) . "',
						   	   	'',
						   	   	'" . $database->getEscaped($row['shipping_customer_instructions']) . "',
						   	   	'" . $database->getEscaped($ip_address) . "',
								'',
						   	   	'',
						   	   	'',
						   	   	'" . $database->getEscaped($parsed_from_file['email']) . "' )";


                $database->setQuery($query_insert_order);
                if (!$database->query()) {
                    $text=$query_insert_order."<br>".$database->getErrorMsg();
                    $out = array('result'=>'error','msg'=>$text);
                    exit(json_encode($out));
                }
                $order_id = $database->insertid();




                $shopper_group_obj = false;

                $query = "SELECT 
        `g`.`shopper_group_discount`,
        `g`.`shopper_group_name`,
        `g`.`shopper_group_id`
    FROM `jos_vm_shopper_vendor_xref` AS `x`
    INNER JOIN `jos_vm_shopper_group` AS `g` ON `g`.`shopper_group_id`=`x`.`shopper_group_id`
    WHERE `x`.`user_id`=" . $user_id . "";

                $database->setQuery($query);
                $database->loadObject($shopper_group_obj);

                if ($shopper_group_obj) {
                    $query = "INSERT INTO `jos_vm_orders_extra`
        (
            `order_id`,
            `shopper_discount_value`,
            `shopper_group_id`,
            `shopper_group_name`
        )
        VALUES (
            " . $order_id . ",
            '" . number_format($subtotal * floatval($shopper_group_obj->shopper_group_discount) / 100, 2, '.', '') . "',
            '" . $shopper_group_obj->shopper_group_id . "',
            '" . $database->getEscaped($shopper_group_obj->shopper_group_name) . "'
        )";

                    $database->setQuery($query);
                    $database->query();
                }







                include_once ($mosConfig_absolute_path.'/administrator/components/com_virtuemart/classes/language.class.php');
                include_once ($mosConfig_absolute_path.'/administrator/components/com_virtuemart/languages/english.php');
                //ORDER TO WAREHOUSE
                $VM_LANG = new vmLanguage();

                $query = "SELECT WH.warehouse_email, WH.warehouse_code FROM #__vm_warehouse AS WH, #__postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code = '" . str_replace(' ', '', $row['shipping_zip']) . "'";
                $database->setQuery($query);
                $oWarehouse = $database->loadObjectList();

                if (count($oWarehouse)) {
                    $oWarehouse = $oWarehouse[0];
                    $warehouse_code = $oWarehouse->warehouse_code;
                    $warehouse_email = $oWarehouse->warehouse_email;


                    $query = "UPDATE #__vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
                    $database->setQuery($query);
                    $database->query();

                    if ($warehouse_code) {
                        $mail_Subject = $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #" . $order_id;
                        $mail_Content = str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
                        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $warehouse_email, $mail_Subject, $mail_Content, 1);
                    }
                } else {
                    $query = "UPDATE #__vm_orders SET warehouse='NOWAREHOUSEASSIGNED', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
                    $database->setQuery($query);
                    $database->query();
                }
                //!ORDER TO WAREHOUSE
                $orders_id[]=$order_id;
                
                $query_insert_item = rtrim(str_replace('{order_id}', $order_id, $query_insert_item), ',');
                
                if (count($query_insert_item)  > 0) {
                    foreach ($query_inserts_item as $query_inserts_item_one) {
                        $query_inserts_item_one['insert'] = str_replace('{order_id}', $order_id, $query_inserts_item_one['insert']);
                        
                        $database->setQuery($query_insert_item.$query_inserts_item_one['insert']);

                        if (!$database->query()) {
                            $text = $query_insert_item.$query_inserts_item_one['insert'] . "<br>" . $database->getErrorMsg();
                            $out = array('result' => 'error', 'msg' => $text);
                            exit(json_encode($out));
                        }
                        $order_item_id = $database->insertid();
                        
                        //ORDER ITEM INGREDIENTS
                        $ps_for_checkout->setOrderItemIngredients($order_id, $order_item_id, $query_inserts_item_one['product_id'], $query_inserts_item_one['quantity'], $query_inserts_item_one['select_bouquet']);
                        //END
                    }
                }
                /*
                $query_insert_item = rtrim(str_replace('{order_id}',$order_id,$query_insert_item),',');
                $database->setQuery($query_insert_item);
                if (!$database->query()) {
                    $text=$query_insert_item."<br>".$database->getErrorMsg();
                    $out = array('result'=>'error','msg'=>$text);
                    exit(json_encode($out));
                }*/
                
                $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
                $query_insert_history = "INSERT INTO #__vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments, user_name)
				VALUES ('$order_id',
						'".$order_default_status."',
						'".$mysqlDatetime."',
						1,
						'Parsed  from ".$database->getEscaped($parsed_from_file['billing_company_name'])." file', '".$database->getEscaped($parsed_from_file['email'])."')";
                $database->setQuery($query_insert_history);
                if (!$database->query()) {
                    $text=$query_insert_history."<br>".$database->getErrorMsg();
                    $out = array('result'=>'error','msg'=>$text);
                    exit(json_encode($out));
                }


                $query_insert_order_info_bt = "INSERT INTO #__vm_order_user_info (  order_id,
													user_id,
													address_type,
													address_type_name,
													company,
													last_name,
													first_name,
													middle_name,
													phone_1,
                                                                                                        phone_2,
													address_1,
													address_2,
													city,
													state,
													country,
													zip,
													user_email, suite,street_number, street_name )
				   	   VALUES(  '" . $order_id . "',
				   	   			{$user_id},
				   	   			'BT',
				   	   			'-default-',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_company_name']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_last_name']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_first_name']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_first_name']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_phone']) . "',
                                '" . $database->getEscaped($parsed_from_file['billing_phone']) . "',
				   	   			'" . $database->getEscaped($addr) . "',
				   	   			'" . $database->getEscaped($addr) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_city']) . "',
				   	   			'" . $database->getEscaped($state) . "',
				   	   			'" . $database->getEscaped($country) . "',
				   	   			'" . $database->getEscaped(str_replace(' ', '', $parsed_from_file['billing_zip'])) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['email']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_suite']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_street_number']) . "',
				   	   			'" . $database->getEscaped($parsed_from_file['billing_street_name']) . "')";
                $database->setQuery($query_insert_order_info_bt);
                if (!$database->query()) {
                    $text=$query_insert_order_info_bt."<br>".$database->getErrorMsg();
                    $out = array('result'=>'error','msg'=>$text);
                    exit(json_encode($out));
                }


                $query_insert_order_info_st = "INSERT INTO #__vm_order_user_info (  order_id,
													user_id,
													address_type,
													address_type2,
													address_type_name,
													company,
													last_name,
													first_name,
													middle_name,
													phone_1,
													phone_2,
													address_1,
													address_2,
													city,
													state,
													country,
													zip,
													user_email, extra_field_1, suite,street_number, street_name )
				   	   VALUES(  '" . $order_id . "',
				   	   			{$user_id},
				   	   			'ST',
				   	   			'Business',
                                '-default-',
			   	   				'" . $database->getEscaped($row['shipping_company_name']) . "',
				   	   			'" . $database->getEscaped($row['shipping_last_name']) . "',
				   	   			'" . $database->getEscaped($row['shipping_first_name']) . "',
				   	   			'" . $database->getEscaped($row['shipping_first_name']) . "',
				   	   			'" . $database->getEscaped($row['shipping_phone']) . "',
				   	   			'" . $database->getEscaped($row['shipping_phone']) . "',
				   	   			'" . $database->getEscaped($row['address']) . "',
				   	   			'" . $database->getEscaped($row['address']) . "',
				   	   			'" . $database->getEscaped($row['shipping_city']) . "',
				   	   			'" . $database->getEscaped($state) . "',
				   	   			'" . $database->getEscaped($country) . "',
				   	   			'" . $database->getEscaped(str_replace(' ', '', $row['shipping_zip'])) . "',
				   	   			'',
				   	   			'',
                                '" . $database->getEscaped($row['shipping_suite']) . "',
				   	   			'" . $database->getEscaped($row['shipping_street_number']) . "',
				   	   			'" . $database->getEscaped($row['shipping_street_name']) . "'
				   	   			 )";

                $database->setQuery($query_insert_order_info_st);
                if (!$database->query()) {
                    $text=$query_insert_order_info_st."<br>".$database->getErrorMsg();
                    $out = array('result'=>'error','msg'=>$text);
                    exit(json_encode($out));
                }

            }
        }
        if(count($orders_id)>0){
            $orders_ser = serialize($orders_id);
            $query_insert_tbl="INSERT INTO tbl_company_parse_orders ( user_id,orders,status,company_name,company_discount, orders_date)  VALUES(  '{$user_id}', '{$database->getEscaped($orders_ser)}', 'pending', '".$database->getEscaped($parsed_from_file['billing_company_name'])."', '".$user_discount."', DATE_SUB(NOW(), INTERVAL 5 HOUR) )";
            $database->setQuery($query_insert_tbl);
            if (!$database->query()) {
                $text=$query_insert_tbl."<br>".$database->getErrorMsg();
                $out = array('result'=>'error','msg'=>$text);
                exit(json_encode($out));
            }
            $item_id = $database->insertid();
            return $item_id;
        }else{
            return false;
        }


    }

}

?>