<?php

$order_id = $_REQUEST['manifest'] ?? die('no manifest #');


$access = base64_encode("507bb323-a42a-41bf-ad57-5678dea2f8a5:x0b0885a26392b9bdc60");
$account = '3010387570';
$service_accounts_url = 'https://digitalapi.auspost.com.au/test/shipping/v1/orders/' . $order_id;

$ch_accounts = curl_init($service_accounts_url);
curl_setopt($ch_accounts, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch_accounts, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch_accounts, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_accounts, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json',
    'Account-Number: ' . $account . '',
    'Authorization: Basic ' . $access . ''
));

$curl_accounts_response = curl_exec($ch_accounts);

echo $service_accounts_url;
echo "<hr>";

echo $curl_accounts_response;

