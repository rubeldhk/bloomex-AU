<?php
/**
 * Created by PhpStorm.
 * User: ekataev
 * Date: 16.08.2018
 * Time: 16:58
 */

$json = array(
    "date" => "2014-02-13",
    "address" => "63 Ubi Avenue 1 Singapore 408937",
    "delivery_time" => "09:00 AM - 12:00 PM",
    "deliver_to" => "John Tan",
    "phone" => "+6591234567",
    "notify_email" => "john.tan@example.com",
    "notify_url" => "http://www.example.com/notify.php",
    "assign_to" => "GT1234H",
    "instructions" => "Call customer upon arrival.",
    "zone" => "East",
    "reason" => "Fail",
    "note" => "Recipient commented that delivery is very prompt.",
    "received_by" => "John",
    "image" => 1,
    "view_image_url" => "https://app.detrack.com/deliveries/photo/52cbaf87f92ea105a4000115.jpg",
    "do" => "DO1131287",
    "status" => "Delivered",
    "time" => "2014-02-13T09:30:45+08:00",
    "pod_lat" => 1.32502083807714,
    "pod_lng" => 103.893779271220,
    "pod_address" => "63 Ubi Avenue 1, Singapore 408937",
    "items" => array(
        array(
            "sku" => "T0201",
            "desc" => "Test Item #01",
            "qty" => 1,
            "reject" => 0,
            "reason" => ""
        ),
        array(
            "sku" => "T0202",
            "desc" => "Test Item #02",
            "qty" => 5,
            "reject" => 0,
            "reason" => ""
        ),
        array(
            "sku" => "T0203",
            "desc" => "Test Item #03",
            "qty" => 10,
            "reject" => 0,
            "reason" => ""
        )
    )
);
$data = json_encode($json);
//echo $data;
$curl = curl_init('https://dev1.amazon.bloomex.com.au/detrack_api/push.php');
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_USERPWD, 'test:ahs0hij3Ah');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

                $curl_response = curl_exec($curl);
                echo $curl_response;