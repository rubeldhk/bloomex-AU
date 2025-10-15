<?php

function getaccounts() {
    $access = base64_encode("bb174c1e-da3f-4bac-909e-fce17cd1620c:x837e796a7973b080e0c");
    $account = '0007767904';
    $service_accounts_url = 'https://digitalapi.auspost.com.au/shipping/v1/accounts/'.$account;
    $ch_accounts = curl_init($service_accounts_url);
    curl_setopt($ch_accounts, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch_accounts, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_accounts, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_accounts, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Account-Number: '.$account.'',
        'Authorization: Basic '.$access.''
    ));
    $curl_accounts_response = curl_exec($ch_accounts); // Execute REST Request
    if(curl_errno($ch_accounts)){
        echo 'Curl error: ' . curl_error($ch_accounts) . "\n";die;
    }
    curl_close($ch_accounts);
    if (!$curl_accounts_response) {
        echo 'Failed loading ' . "\n";
        echo $curl_accounts_response . "\n";die;
    } else {
        $response_accounts = json_decode($curl_accounts_response);
        if($response_accounts && isset($response_accounts->errors) && is_array($response_accounts->errors))
        {
            foreach($response_accounts->errors as $q){
                echo $q->message."<br>";die;
            }
        }
    }
    $choose_type='<select name="shipping_type">';
    if($response_accounts->postage_products){
        foreach($response_accounts->postage_products as $r){
            $choose_type.='<option value="'.$r->product_id.'">'.$r->type.'</option>';
        }
    }
    $choose_type.='</select>';
    return $choose_type;
}

echo getaccounts();