<?php

$service_url = 'https://digitalapi.auspost.com.au/test/shipping/v1/accounts/1015728073';


$data = array(
    'account_number' => '1015728073',
    'name' => 'Abc Xyz Co',
    'valid_from' => '2017-02-24',
    'valid_to' => '2999-12-31',
    'expired' => false,
    'addresses' =>
    array(
        0 =>
        array(
            'type' => 'MERCHANT_LOCATION',
            'lines' =>
            array(
                0 => 'Unit 9 12-18 Victoria St E',
                1 => 'Lidcombe',
            ),
            'suburb' => 'Sydney',
            'postcode' => '2141',
            'state' => 'NSW',
        ),
    ),
    'merchant_location_id' => 'ABC',
    'credit_blocked' => 'false'
);

$data_string = json_encode($data);
$service_url = $service_url . "?account_number=1015728073";
echo "service url : <pre>";
print_r($service_url);
echo "request : <pre>";

$ch = curl_init($service_url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json',
    'Account-Number: 1015728073',
    'Authorization: Basic ODU4ZDlkZjEtZmEyZS00ZTIxLTgxNzAtZGIzYjg3YTMxY2VjOngxYWFkNDc1ZDEyNDAyZmQ0OTEx'
));
$curl_response = curl_exec($ch); // Execute REST Request
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch) . "\n";
    die;
}
curl_close($ch);
var_dump(json_decode($curl_response));
