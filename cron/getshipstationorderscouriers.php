<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
$i = 0;
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';

define('_VALID_MOS', 'true');
define('_JEXEC', 'true');

date_default_timezone_set('Australia/Sydney');
$mysqlDatetime = date("Y-m-d G:i:s", time());
$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);


$query = "select d.shipment_id,o.order_id,w.warehouse_name
FROM jos_vm_orders as o 
LEFT JOIN jos_vm_warehouse as w on w.warehouse_code=o.warehouse
JOIN jos_vm_orders_deliveries AS d on d.order_id=o.order_id AND d.active = 1 and d.delivery_type = 12 
LEFT JOIN tbl_shipstation_orders_couriers as s on s.shipment_id = d.shipment_id where s.id is null order by o.order_id desc limit 20";
$result = $mysqli->query($query);
if (!$result) {
    $result->close();
    $mysqli->close();
    die('No result');
}

while ($obj = $result->fetch_object()) {

    $bearer = base64_encode($mosConfig_shipstation_api_key);
    $curl = curl_init();
    $curlOptions = [
        CURLOPT_URL => 'https://ssapi.shipstation.com/shipments?orderId='.$obj->shipment_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST =>  "GET",
        CURLOPT_HTTPHEADER => [
            "Host: ssapi.shipstation.com",
            "Authorization:  Basic $bearer",
            "Content-Type: application/json"
        ],
    ];
    curl_setopt_array($curl, $curlOptions);
    $response = curl_exec($curl);

    if ($response) {

        $responseData = json_decode($response);
        if($responseData && isset($responseData->shipments) && !empty($responseData->shipments))
        {
            $serviceCodes = [
                'australia_post' =>	'https://auspost.com.au/parcels-mail/track.html#/track?id=',
                'star_track'	=> 'https://startrack.com.au/track/details/',
                'fastway_au' =>	'https://www.aramex.com.au/tools/track/?l='
            ];
            $tracking_url = isset($serviceCodes[$responseData->shipments[0]->carrierCode])?$serviceCodes[$responseData->shipments[0]->carrierCode]. $responseData->shipments[0]->trackingNumber:'';
            $create_date =date("Y-m-d H:i:s",strtotime($responseData->shipments[0]->createDate));
            $query = "INSERT INTO `tbl_shipstation_orders_couriers` (`shipment_id`,
                                                                    `order_id`,
                                                                    `warehouse`,
                                                                    `carrier_code`,
                                                                    `tracking_url`,
                                                                    `tracking_number`,
                                                                    `datetime_from_shipstation`,
                                                                    `datetime_added`
                                               ) VALUES 
                                                                                (
                                                                                 '".$obj->shipment_id."',
                                                                                 '".$obj->order_id."',
                                                                                 '".$obj->warehouse_name."',
                                                                                 '".$mysqli->escape_string($responseData->shipments[0]->carrierCode)."',
                                                                                 '".$mysqli->escape_string($tracking_url)."',
                                                                                 '".$mysqli->escape_string($responseData->shipments[0]->trackingNumber)."',
                                                                                 '".$mysqli->escape_string($create_date)."',
                                                                                 '".$mysqli->escape_string($mysqlDatetime)."'
                                                                                 )";
            $mysqli->query($query);
            $i++;
        }
    }


}
$result->close();
$mysqli->close();

if ($i) {
    echo "we scan and create $i new tracking items <br>";
}else{
    echo "no new shipments";
}
