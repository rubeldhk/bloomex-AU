<?php

// this to be used as include in update_status_intransit_to_delivered.php
/*
$time = time();
include(str_replace('/cron', '', __DIR__) . '/configuration.php');
if (!$mosConfig_host)
    die('no config');
$timestamp = time() + ($mosConfig_offset * 60 * 60);
$cron_username = ' Cron Bot';
$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
require_once $mosConfig_absolute_path . '/includes/joomla.php';
global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
*/
defined('_VALID_MOS') or die('Restricted access');
$time = time();
if (!$mosConfig_host)
    die('no config');

date_default_timezone_set('Australia/Sydney');

$cron_username = 'Cron Bot - delivery service api';
//$timestamp = time() + ($mosConfig_offset * 60 * 60);
//$mysqlDatetime = date("Y-m-d G:i:s", time() + ($mosConfig_offset * 60 * 60));
$mysqlDatetime = date('Y-m-d H:i:s');
$DeliveryService = new DeliveryService();


$query = "SELECT 
    `o`.`order_id`,
    `o`.`warehouse`,
    `e`.`name`,
    `d`.`pin`,
    `d`.`shipment_id`,
    `o`.`ddate`
FROM `jos_vm_orders` as `o`
LEFT JOIN `jos_vm_orders_deliveries` as `d` 
    on 
    `d`.`order_id`=`o`.`order_id`
LEFT JOIN `jos_vm_deliveries` as `e` 
    on 
    `e`.`id`=`d`.`delivery_type`
WHERE 
    `d`.`id` is not null 
    AND 
    STR_TO_DATE(`o`.`ddate` , '%d-%m-%Y' ) >= '" . date('Y-m-d') . "' group by `o`.`order_id`
";

$database->setQuery($query);
$res = $database->loadObjectlist();
if($res){
    foreach($res as $r) {
        $tracking_comment='';
        $delivered=false;
        switch ($r->name){
            case 'MyFastWay':
                $tracking_comment = $DeliveryService->get_fastway_tracking($r->order_id);
                if($tracking_comment=='Your parcel has been delivered as per your instructions.' || $tracking_comment=='Your parcel has been delivered and a signature obtained.'){
                    $delivered=true;
                }
                break;
            case 'GoPeople':
                $tracking_comment = $DeliveryService->get_gopeople_tracking($r->order_id,$r->warehouse);
                if($tracking_comment=='jobClosed'){
                    $delivered=true;
                }
                break;
            case 'AusPost':
            case 'AusPostOnDemand':
                $tracking_comment = $DeliveryService->get_auspost_tracking($r->warehouse,$r->pin);
                if($tracking_comment == 'Delivered'){
                    $delivered=true;
                }
                break;
            case 'ShipStation':
                $tracking_comment = $DeliveryService->get_shipstation_tracking($r->order_id,$r->shipment_id);
                if($tracking_comment == 'shipped'){
                    $delivered=true;
                }
                break;
            default:
                $return['tracking_system'] = "No Delivery Service Assigned";
        }

        if($tracking_comment){

            $q = "SELECT comments FROM `jos_vm_order_history` WHERE order_id=".$r->order_id." and comments='".$database->getEscaped($tracking_comment)."'";
            $database->setQuery($q);
            $res = $database->loadResult();

            if(!$res){
                    $status_code='Z';
                if($delivered){
                    $status_code='D';
                    $database->setQuery("UPDATE `jos_vm_orders` 
                    SET `order_status`='" . $status_code . "'
                    WHERE `order_id`=" . $r->order_id . "
                ");
                $database->query();
                }
                $q = "INSERT INTO jos_vm_order_history ";
                $q .= "(order_id,date_added,order_status_code,comments,user_name) VALUES (";
                $q .= "'" . $r->order_id . "', '$mysqlDatetime', '".$status_code."', '".$database->getEscaped($tracking_comment)."', '" .$r->name . ' '.$cron_username . "')";
                $database->setQuery($q);
                $database->query();
                $i++;
            }
        }

    }
}
if ($i > 0) {
    echo $i . " Orders have been updated" . PHP_EOL;
} else {
    echo 'there is no orders to update status';
}


class DeliveryService{
    function get_tracking_number($order_id,$comment_patern){
        global $database;
        $query = "SELECT comments
        FROM `jos_vm_order_history`  WHERE order_id='$order_id' and (order_status_code = 'Z' OR order_status_code ='G') and comments like '%".$comment_patern."%' ORDER BY `order_status_history_id` desc limit 1 ";
        $database->setQuery($query);
        $result = false;
        $database->loadObject($result);


        if (isset($result->comments)) {
            $tracking_number = explode($comment_patern, strip_tags($result->comments));
            if(count($tracking_number)>1){
                return array_pop($tracking_number);
            }else{
                return false;
            }
        } else {
            return false;
        }
    }
    function get_fastway_tracking($order_id){
        $tracking_number = $this->get_tracking_number($order_id,'Tracking number Fastway: ');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://www.fastway.com.au/tracking-api/?callback&LabelNo='.$tracking_number.'&dataFormat=json');
        $result = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($result);
        if($obj && $obj->result && $obj->result->Scans){
            if(is_array($obj->result->Scans)){
                $res = end($obj->result->Scans);
                return $res->StatusDescription;
            }

        }
    }
    function get_gopeople_tracking($order_id,$warehouse){
        $tracking_number = $this->get_tracking_number($order_id,'Tracking number: ');
        $tracking_number = explode(' JobID: ', strip_tags($tracking_number));
        if(count($tracking_number)>1){
            $tracking_number = array_shift($tracking_number);
        }else{
            $tracking_number=false;
        }

        if($tracking_number){

        Switch ($warehouse) {
            case 'WH15':
                $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MjU3MzQ5MzQsImp0aSI6IjAraXZhYlRhS25sXC9idWRnMlM1MlI3bjIycW8yY2JMZGtqbHZtUjE4d084PSIsImlzcyI6InBlb3BsZXBvc3QuY29tLmF1IiwiZGF0YSI6eyJ1c2VySWQiOjMyNzQwfX0.vd1ROyZSQyyoVmE3YwrhZ6bE0w54WKx7qo3hhSWsnyHV4yL52Ylm24g7gfaG5GKx5RxNJl3O1dSDyDiiFoJv8w';
                break;
            case 'WH12':
                $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE0OTE4NzA0NDIsImp0aSI6IllvV3BWTVl1WTFGK3Era2U3NnFoRFZGTkNNdTFTUTZLUWx2NVl3elNnc1E9IiwiaXNzIjoicGVvcGxlcG9zdC5jb20uYXUiLCJkYXRhIjp7InVzZXJJZCI6NTE0NH19.BoB-eJjn1Z95fAMXzwOj1gzaSqcW51mDoXFSoGqDRetwlbQ4Axzd93MvKlv9Xosy80eTCxJHfzt8Xt7sYBYRJg';
                break;

            case 'WH14':
                $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MDcyNDE1NDYsImp0aSI6IndvNjhNdk02QTZhXC9KSXdHQmY4c1JEamlcLzFDcndNXC9td2YrNlBiYnVYM1U9IiwiaXNzIjoicGVvcGxlcG9zdC5jb20uYXUiLCJkYXRhIjp7InVzZXJJZCI6MjQ3MTl9fQ.Rr4aX79Sr3rjVoyQOfwSCkS3xw8YtAipkj-5ipQX7UXZpTEJ_4BUxu17vv3GxuFQcZ9Dk-0AzmwWK1BIFpNwfQ';
                break;
        }
        $fields = array(
                'code' => $tracking_number
        );
        $data_string = json_encode($fields);

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-length:' . strlen($data_string),
            'X-API-KEY: ' . $gopeople_token . ''
        );

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, 'https://www.gopeople.com.au/ppost/tracking.php');
        curl_setopt($connection, CURLOPT_POST, true);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($connection, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($connection);

        if ($response === false) {
            die('Error fetching data: ' . curl_error($connection));
        }
        curl_close($connection);

            $response = json_decode($response, true);
            if($response && $response['result']){
                if($response['result']['comments'] && is_array($response['result']['comments'])){
                    $res = array_shift(array_values($response['result']['comments']));
                    return $res['content'];
                }elseif($response['result']['jobClosed']){
                    return 'jobClosed';
                }

            }
        }

    }
    function get_auspost_tracking($warehouse,$tracking_number){
        $username = 'bb174c1e-da3f-4bac-909e-fce17cd1620c';
        $password = 'x837e796a7973b080e0c';
        $access = base64_encode($username . ":" . $password);

        if($tracking_number){
            switch ($warehouse) {
                case "WH12":
                    $customerNumber = '0007767904';
                    break;
                case "WH14":
                    $customerNumber = '0007860077';
                    break;
                case "WH15":
                    $customerNumber = '0008348363';
                    break;
                case "WH16":
                    $customerNumber = '0004720960';
                    break;
                case "p01":
                    $customerNumber = '0008348371';
                    break;
                default :
                    $customerNumber = '0007767904';
            }

            $url = 'https://digitalapi.auspost.com.au/shipping/v1/track?tracking_ids='.$tracking_number;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Account-Number: ' . $customerNumber . '',
                'Authorization: Basic ' . $access . ''
            ));
            $curl_response = curl_exec($ch); // Execute REST Request
            if (curl_errno($ch)) {
                return false;
            }
            curl_close($ch);
            if (!$curl_response) {
                return false;
            } else {
                $response = json_decode($curl_response);
                if ($response && isset($response->errors) && is_array($response->errors)) {
                    return false;
                } elseif ($response && isset($response->tracking_results) && is_array($response->tracking_results)) {

                        foreach ($response->tracking_results as $results) {
                            if($results->status == 'Delivered') {
                                return $results->status;
                            }

                            if($results && isset($results->trackable_items) && is_array($results->trackable_items) && isset($results->trackable_items[0]->events)){

                                return $results->trackable_items[0]->events[0]->description.'<br>'.
                                       $results->trackable_items[0]->events[0]->location.'  '.date("D j M G:i",strtotime($results->trackable_items[0]->events[0]->date));

                            }
                        }
                    return false;

                }
            }

        }

    }
    function get_shipstation_tracking($order_id,$tracking_number){
        $username = 'e803ca99c6e0440fa84010729373997d';
        $password = 'd32e64d1a38241688f4e17e96822cdd5';
        $access = base64_encode($username . ":" . $password);
        $datetime = date('Y-m-d', strtotime(' - 3 days'));
        if($tracking_number){

            //$this->check_shipstation_label($order_id,$tracking_number,$access);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://ssapi.shipstation.com/orders?modifyDateStart=".$datetime,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Host: ssapi.shipstation.com",
                    "Authorization: Basic $access"
                ),
            ));

            $curl_response = curl_exec($curl);

            if (curl_errno($curl)) {
                return false;
            }
            curl_close($curl);
            if (!$curl_response) {
                return false;
            } else {
                $response = json_decode($curl_response);

                if ($response && isset($response->orders) && is_array($response->orders)) {

                        foreach ($response->orders as $results) {

                            if($results->orderStatus == 'shipped' && $results->orderNumber == $order_id && $results->orderId == $tracking_number) {

                                return $results->orderStatus;
                            }
                        }
                    return false;

                }
            }

        }

    }


    function check_shipstation_label($order_id,$tracking_number,$access){



            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://ssapi.shipstation.com/shipments?orderId=".$tracking_number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Host: ssapi.shipstation.com",
                    "Authorization: Basic $access"
                ),
            ));

            $curl_response = curl_exec($curl);

        echo "<pre>";print_r($curl_response);
        die;
            if (curl_errno($curl)) {
                return false;
            }
            curl_close($curl);
            if (!$curl_response) {
                return false;
            } else {

                $response = json_decode($curl_response);

                if ($response && isset($response->orders) && is_array($response->orders)) {

                    foreach ($response->orders as $results) {

                        if($results->orderStatus == 'shipped' && $results->orderNumber == $order_id && $results->orderId == $tracking_number) {

                            return $results->orderStatus;
                        }
                    }
                    return false;

                }
            }



    }


}