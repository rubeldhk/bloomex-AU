<?php
var_dump(gd_info());
//            echo "<pre>";print_r($test);die;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//
////class SMSParam
////{
////
////    public $CellNumber;
////    public $AccountKey;
////    public $MessageBody;
////
////}
////
////$mosConfig_limit_sms_sender_AccountKey = 'A01PePmB6y4N8L4M1KLlloZd0JII5Hi7';
////$client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
////$parameters = new SMSParam;
////$parameters->CellNumber = +61450492014;
////$parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
////$parameters->MessageBody = 'Hi Yuliya (number +61488827343)';
////$Result_id = $client->SendMessageExtended($parameters);
////
////
////echo "<pre>";
////print_r($Result_id->SendMessageExtendedResult->MessageID);
////echo "<pre>";
////print_r($Result_id->SendMessageExtendedResult);
////die;
//
////e803ca99c6e0440fa84010729373997d:d32e64d1a38241688f4e17e96822cdd5
//
//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//    CURLOPT_URL => "https://ssapi.shipstation.com/carriers",
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => "",
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => "GET",
//    CURLOPT_HTTPHEADER => array(
//        "Host: ssapi.shipstation.com",
//        "Authorization: Basic ZTgwM2NhOTljNmUwNDQwZmE4NDAxMDcyOTM3Mzk5N2Q6ZDMyZTY0ZDFhMzgyNDE2ODhmNGUxN2U5NjgyMmNkZDU="
//    ),
//));
//
//$response = curl_exec($curl);
//
//curl_close($curl);
////echo "<pre>";print_r($response);
//
//$curl = curl_init();
//$post1 = array (
//    'carrierCode' => 'fastway_au',
//    'serviceCode' => NULL,
//    'packageCode' => NULL,
//    'fromPostalCode' => '6090',
//    'toState' => 'WA',
//    'toCountry' => 'AU',
//    'toPostalCode' => '6070',
//    'toCity' => 'Malaga',
//    'weight' =>
//        array (
//            'value' => 3,
//            'units' => 'ounces',
//        ),
//    'dimensions' =>
//        array (
//            'units' => 'inches',
//            'length' => 7,
//            'width' => 5,
//            'height' => 6,
//        ),
//    'confirmation' => 'delivery',
//    'residential' => false,
//);
//$post = array (
//    'orderNumber' => 'TEST-ORDER-API-DOCS111',
////    'orderKey' => '0f6bec18-3e89-4881-83aa-f392d84f4c74',
//    'orderDate' => '2024-07-29T08:46:27.0000000',
//    'paymentDate' => '2024-07-29T08:46:27.0000000',
//    'shipByDate' => '2024-07-05T00:00:00.0000000',
//    'orderStatus' => 'awaiting_shipment',
//    'customerUsername' => 'headhoncho@whitehouse.gov',
//    'customerEmail' => 'headhoncho@whitehouse.gov',
//    'billTo' =>
//        array (
//            'name' => 'The President',
//            'company' => 'US Govt',
//            'street1' => '452 Nepean Highway',
//            'street2' => null,
//            'street3' => NULL,
//            'city' => 'MOUNT MARTHA',
//            'state' => 'ACT',
//            'postalCode' => '2000',
//            'country' => 'AU',
//            'phone' => '555-555-5555',
////            'residential' => true,
//        ),
//    'shipTo' =>
//        array (
//            'name' => 'The President',
//            'company' => 'US Govt',
//            'street1' => '450 Nepean Highway',
//            'street2' => null,
//            'street3' => NULL,
//            'city' => 'MOUNT MARTHA',
//            'state' => 'ACT',
//            'postalCode' => '2000',
//            'country' => 'AU',
//            'phone' => '555-555-5555',
////            'residential' => true,
//        ),
//    'items' =>
//        array (
//            0 =>
//                array (
////                    'lineItemKey' => 'vd08-MSLbtx',
//                    'sku' => 'ABC123',
//                    'name' => 'Test item #1',
////                    'imageUrl' => NULL,
////                    'weight' =>
////                        array (
////                            'value' => 24,
////                            'units' => 'ounces',
////                        ),
//                    'quantity' => 2,
//                    'unitPrice' => 99.99,
////                    'taxAmount' => 2.5,
////                    'shippingAmount' => 5,
////                    'warehouseLocation' => 'Aisle 1, Bin 7',
////                    'options' =>
////                        array (
////                            0 =>
////                                array (
////                                    'name' => 'Size',
////                                    'value' => 'Large',
////                                ),
////                        ),
////                    'productId' => 123456,
////                    'fulfillmentSku' => NULL,
////                    'adjustment' => false,
////                    'upc' => '32-65-98',
//                ),
//            1 =>
//                array (
////                    'lineItemKey' => NULL,
//                    'sku' => 'DISCOUNT CODE',
//                    'name' => '10% OFF',
//                    'imageUrl' => NULL,
////                    'weight' =>
////                        array (
////                            'value' => 0,
////                            'units' => 'ounces',
////                        ),
//                    'quantity' => 1,
//                    'unitPrice' => -20.55,
////                    'taxAmount' => NULL,
////                    'shippingAmount' => NULL,
////                    'warehouseLocation' => NULL,
////                    'options' =>
////                        array (
////                        ),
////                    'productId' => 123456,
////                    'fulfillmentSku' => 'SKU-Discount',
////                    'adjustment' => true,
////                    'upc' => NULL,
//                ),
//        ),
//    'amountPaid' => 218.73,
////    'taxAmount' => 5,
//    'shippingAmount' => 10,
//    'customerNotes' => 'Please ship as soon as possible!',
////    'internalNotes' => 'Customer called and would like to upgrade shipping',
////    'gift' => true,
////    'giftMessage' => 'Thank you!',
////    'paymentMethod' => 'Credit Card',
////    'requestedShippingService' => 'Priority Mail',
//    'packageCode' => 'package',
//    'confirmation' => 'delivery',
//    'shipDate' => '2015-07-02',
////    'weight' =>
////        array (
////            'value' => 25,
////            'units' => 'ounces',
////        ),
////    'dimensions' =>
////        array (
////            'units' => 'inches',
////            'length' => 7,
////            'width' => 5,
////            'height' => 6,
////        ),
////    'insuranceOptions' =>
////        array (
////            'provider' => 'carrier',
////            'insureShipment' => true,
////            'insuredValue' => 200,
////        ),
////    'internationalOptions' =>
////        array (
////            'contents' => NULL,
////            'customsItems' => NULL,
////        ),
////    'advancedOptions' =>
////        array (
////            'warehouseId' => NULL,
////            'nonMachinable' => false,
////            'saturdayDelivery' => false,
////            'containsAlcohol' => false,
////            'mergedOrSplit' => false,
////            'mergedIds' =>
////                array (
////                ),
////            'parentId' => NULL,
////            'storeId' => NULL,
////            'customField1' => 'Custom data that you can add to an order. See Custom Field #2 & #3 for more info!',
////            'customField2' => 'Per UI settings, this information can appear on some carrier\'s shipping labels. See link below',
////            'customField3' => 'https://help.shipstation.com/hc/en-us/articles/206639957',
////            'source' => 'Webstore',
////            'billToParty' => NULL,
////            'billToAccount' => NULL,
////            'billToPostalCode' => NULL,
////            'billToCountryCode' => NULL,
////        ),
////    'tagIds' =>
////        array (
////            0 => 53974,
////        ),
//);
//curl_setopt_array($curl, array(
//    CURLOPT_URL => "https://ssapi.shipstation.com/orders/createorder",
////    CURLOPT_URL => "https://ssapi.shipstation.com/shipments/getrates",
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => "",
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => "POST",
//    CURLOPT_POSTFIELDS =>json_encode($post),
//    CURLOPT_HTTPHEADER => array(
//        "Host: ssapi.shipstation.com",
//        "Authorization:  Basic ZTgwM2NhOTljNmUwNDQwZmE4NDAxMDcyOTM3Mzk5N2Q6ZDMyZTY0ZDFhMzgyNDE2ODhmNGUxN2U5NjgyMmNkZDU=",
//        "Content-Type: application/json"
//    ),
//));
//
//$response = curl_exec($curl);
//if (!curl_errno($curl)) {
//    $info = curl_getinfo($curl);
//    echo "<pre>";print_r($info);
//    echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
//}
//curl_close($curl);
//echo "<pre>";print_r($response);die;




//$curl = curl_init();

//curl_setopt_array($curl, array(
//    CURLOPT_URL => "https://ssapi.shipstation.com/webhooks",
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => "",
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => "GET",
//    CURLOPT_HTTPHEADER => array(
//        "Host: ssapi.shipstation.com",
//        "Authorization: Basic ZTgwM2NhOTljNmUwNDQwZmE4NDAxMDcyOTM3Mzk5N2Q6ZDMyZTY0ZDFhMzgyNDE2ODhmNGUxN2U5NjgyMmNkZDU="
//    ),
//));


//curl_setopt_array($curl, array(
//    CURLOPT_URL => "https://ssapi.shipstation.com/orders?modifyDateStart=2024-07-22",
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => "",
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => "GET",
//    CURLOPT_HTTPHEADER => array(
//        "Host: ssapi.shipstation.com",
//        "Authorization: Basic ZTgwM2NhOTljNmUwNDQwZmE4NDAxMDcyOTM3Mzk5N2Q6ZDMyZTY0ZDFhMzgyNDE2ODhmNGUxN2U5NjgyMmNkZDU="
//    ),
//));
//
//$response = curl_exec($curl);
//
//curl_close($curl);
//include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
//
//$list_id = 'nd194jxctd21b';
//
//$config = new \EmsApi\Config([
//    'apiUrl'    => 'https://email.bloomex.info/api',
//    'apiKey'    => '0ae3983d859a4aa22cc5fec3872fb5b214f49d1e',
//]);
//
//\EmsApi\Base::setConfig($config);
//
//$endpoint = new EmsApi\Endpoint\ListSubscribers();
//$response = $endpoint->emailSearch($list_id, 'john.doe@doe.com');
//if($response->body['status'] != 'success') {
//    $response = $endpoint->create($list_id, [
//        'EMAIL'    => 'john.doe@doe.com',
//        'FNAME'    => 'John',
//        'LNAME'    => 'Doe'
//    ]);
//}
?>


<script>
    (function(d) {
        var cm = d.createElement('scr' + 'ipt'); cm.type = 'text/javascript'; cm.async = true;
        cm.src = 'https://kcsafexvff.chat.digital.ringcentral.com/chat/6225bd357a82bc6f829b354f/loader.js';
        var s = d.getElementsByTagName('scr' + 'ipt')[0]; s.parentNode.insertBefore(cm, s);
    }(document));
</script>