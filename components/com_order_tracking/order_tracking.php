<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );
require_once( $mainframe->getPath( 'front_html' ) );

global $mosConfig_live_site,$database;

parse_str(base64_decode(trim(mosGetParam( $_GET, "data" ))), $output);
$order_id 	= $output['order_id'];



if(!$order_id){
    $data = 'there is no order id  to track delivery';
}else {
    $DeliveryService = new DeliveryService();
    $query = "SELECT 
        `e`.`name`,
        `d`.`order_id`, 
        `d`.`pin` 
    from `jos_vm_orders_deliveries` as `d` 
LEFT JOIN `jos_vm_deliveries` as `e` 
    on 
    `e`.`id`=`d`.`delivery_type`
WHERE 
    `d`.`order_id` = '" . $order_id . "' AND `d`.`active`=1 order by `d`.`id` DESC limit 1";

    $database->setQuery($query);
    $res = $database->loadObjectlist();
//    $res=[];
//    $res[0]=new stdClass();
//    $res[0]->name='AusPost';
//    $res[0]->pin='Ve8K0ECLXKIAAAF70lod3jVg';
//    $res[0]->order_id='1754408';
//    $res[0]->warehouse='';

    if ($res) {
        foreach ($res as $r) {
            $data = '';
            $delivered = false;
            switch ($r->name) {
                case 'FastWay':
                    $data = $DeliveryService->get_fastway_tracking($r->order_id);
                    break;
                case 'GoPeople':
                    $data = $DeliveryService->get_gopeople_tracking($r->order_id, $r->warehouse);
                    break;
                case 'AusPost':
                    $data = $DeliveryService->get_auspost_tracking($r->order_id, $r->warehouse,$r->pin);
                    break;
                case 'DeTrack':
                    $data = $DeliveryService->get_detrack_tracking($r->order_id, $r->warehouse,$r->pin);
                    break;
                case 'PickFleet':
                    $data = $DeliveryService->get_pickfleet_tracking($r->order_id, $r->warehouse,$r->pin);
                    break;
                default:
                    $data = "No Trackable Delivery Service";
            }
        }
    } else {
        $data = 'there is no active delivery for the order';
    }
}
    HTML_order_tracking::viewPage($data,$order_id);
class DeliveryService{
    function get_tracking_number($order_id,$comment_patern){
        global $database;
        $query = "SELECT comments FROM `jos_vm_order_history`  WHERE order_id='$order_id' and order_status_code in ('Z','G','C','6','11','9','4','8','H') and comments like '%".$comment_patern."%' ORDER BY `order_status_history_id` desc limit 1 ";
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
        $tracking_number = $this->get_tracking_number($order_id,'labelNumber: ');

        if(!$tracking_number){
            return 'No Tracking Number Saved';
        }
        $res = '';
        if($tracking_number){
            $res .= '<p><b>Tracking Number : </b> '.$tracking_number.'</p>';
            $res .= '<p><b>Tracking Link : </b> <a target="_blank" href="https://www.aramex.com.au/tools/track?l='.$tracking_number.'">https://www.aramex.com.au/tools/track?l='.$tracking_number.'</a></p>';
        }
        return $res;
    }
    function get_gopeople_tracking($order_id,$warehouse){
        $tracking_number = $this->get_tracking_number($order_id,'Tracking number: ');
        $tracking_number = explode(' JobID: ', strip_tags($tracking_number));
        if(count($tracking_number)>1){
            $tracking_number = array_shift($tracking_number);
        }else{
            return 'No Tracking Number Saved';
        }

        $res = '';
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

                    $res .= '<p><b>Tracking Code : </b> '.$tracking_number.'</p>';
                    $res .= '<p><b>Tracking Link : </b><a href="https://www.gopeople.com.au/tracking/?code='.$tracking_number.'">https://www.gopeople.com.au/tracking/?code='.$tracking_number.'</a> </p>';

            }
        }
        return $res;
    }
    function get_detrack_tracking($order_id,$warehouse,$tracking_number){
        global $database;
        $query = "SELECT ddate FROM `jos_vm_orders`  WHERE order_id='$order_id' ";
        $database->setQuery($query);
        $result = false;
        $database->loadObject($result);
        if(!$tracking_number){
            return 'No Tracking Number Saved';
        }
        $res = '';
        if($tracking_number){

            Switch ($warehouse) {
                case 'WH15':
                    $api_key = 'U027f64b1be0f4c5e16dc71290f2525d0cd336b7c1f37b4d6';
                    break;

                case 'WH12':
                case 'WH14':
                default:
                    $api_key = 'U5af8229aacdf575b59b7fa38c92c839b1032a29ecdc5b979';
                    break;
            }

            $data = array("date" => date('Y-m-d',strtotime($result->ddate)));
            $data_string = json_encode($data);

            $headers = array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-length:' . strlen($data_string),
                'X-API-KEY: ' . $api_key . ''
            );

            $connection = curl_init();
            curl_setopt($connection, CURLOPT_URL, 'https://app.detrack.com/api/v1/deliveries/view/all.json');
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($connection, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($connection, CURLOPT_HEADER, 0);
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
            if(isset($response['deliveries'])){
                foreach($response['deliveries'] as $r){
                    if($r['do']==$tracking_number){
                        $res .= ($r['tracking_status'])?'<p><b>Tracking Status : </b> '.$r['tracking_status'].'</p>':'';
                        $res .= ($r['tracking_link'])?'<p><b>Tracking Link : </b><a target="_blank" href="'.$r['tracking_link'].'"> '.$r['tracking_link'].'</a></p>':'';
                    }
                }
            }
        }
        return $res;
    }
    function get_auspost_tracking($order_id,$warehouse,$tracking_number){
        if(!$tracking_number){
            return 'No Tracking Number Saved';
        }
        $res = '';
        if($tracking_number){

            if (substr($_SERVER['HTTP_HOST'],0, 4)=='dev.' || substr($_SERVER['HTTP_HOST'],0, 6)=='stage.') {
                $Warehouse['username'] = '507bb323-a42a-41bf-ad57-5678dea2f8a5';
                $Warehouse['Password'] = 'x0b0885a26392b9bdc60';
                $Warehouse['customerNumber'] = '2010387570';
                $serviceUrl = 'https://digitalapi.auspost.com.au/test/shipping/v1';
            }else{
                $Warehouse['username'] = 'bb174c1e-da3f-4bac-909e-fce17cd1620c';
                $Warehouse['Password'] = 'x837e796a7973b080e0c';
                $Warehouse['customerNumber'] = '0007767904';
                $serviceUrl = 'https://digitalapi.auspost.com.au/shipping/v1';
            }
            $account = $Warehouse['customerNumber'];
            $access = base64_encode($Warehouse['username'].":".$Warehouse['Password']);


            $service_url = $serviceUrl.'/shipments?shipment_ids='.$tracking_number;
            $ch = curl_init($service_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Account-Number: '.$account.'',
                'Authorization: Basic '.$access.''
            ));
            $curl_response = curl_exec($ch); // Execute REST Request
            if(curl_errno($ch)){
                echo 'Curl error: ' . curl_error($ch) . "\n";die;
            }
            curl_close($ch);

            $response = json_decode($curl_response, true);

                if(isset($response['shipments'][0]['items'][0]['tracking_details']['article_id'])){
                    $tracking_id = $response['shipments'][0]['items'][0]['tracking_details']['article_id'];

                    $service_track_url = $serviceUrl.'/track?tracking_ids='.$tracking_id;
                    $ch_track = curl_init($service_track_url);
                    curl_setopt($ch_track, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch_track, CURLOPT_CUSTOMREQUEST, "GET");
                    curl_setopt($ch_track, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_track, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Account-Number: '.$account.'',
                        'Authorization: Basic '.$access.''
                    ));
                    $curl_response_track = curl_exec($ch_track); // Execute REST Request
                    if(curl_errno($ch_track)){
                        echo 'Curl error: ' . curl_error($ch_track) . "\n";die;
                    }
                    curl_close($ch_track);

                    $response_track = json_decode($curl_response_track, true);

                    if(isset($response_track['tracking_results'][0]['trackable_items'][0]['events'])){
                        foreach($response_track['tracking_results'][0]['trackable_items'][0]['events'] as $t){
                            $res .= ($t['location'])?'<p><b>location : </b> '.$t['location'].'</p>':'';
                            $res .= ($t['description'])?'<p><b>Description : </b> '.$t['description'].'</p>':'';
//                            $res .= ($t['date'])?date("Y-m-d H:i:s",strtotime($t['date'])):'';
                            $res .= '<hr style="border: 1px solid black;">';

                        }

                    }
                }
        }
        return ($res)?$res:'No information on this order';
    }
    function get_pickfleet_tracking($order_id,$warehouse,$tracking_number){
        if(!$tracking_number){
            return 'No Tracking Number Saved';
        }
        $res = '';
        if($tracking_number){
            $res .= '<p><b>Tracking Number : </b> '.$tracking_number.'</p>';
            $res .= '<p><b>Tracking Link : </b><a href="https://staging.pickfleet.com/app/#/tracking">https://staging.pickfleet.com/app/#/tracking</a> </p>';
        }
        return $res;
    }
}
?>
