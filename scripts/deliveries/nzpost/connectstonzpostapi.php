<?php

date_default_timezone_set('Australia/Sydney');

class NZPost
{

    var $access = '';
    var $pickup_address_id = '';
    var $delivery_address_id = '';
    var $order_id = null;
    var $BloomexOrder = null;
    var $serviceUrl = 'https://api.nzpost.co.nz';
    var $serviceUrlAccess = 'https://oauth.nzpost.co.nz/as/token.oauth2';
    var $shipmentReference = '';
    var $siteCode = 12484;

    var $clientId = '';
    var $clientSecret = '';

    function __construct($order_id = '', $warehouse = '')
    {
        require_once('bloomexorder.php');
        $this->BloomexOrder = new BloomexOrder();
        if ($order_id) {
            $this->BloomexOrder->GetOrderDetails(intval($order_id));
        }
        if ($warehouse) {
            $this->BloomexOrder->_WH = $warehouse;
        }

        $this->warehouse = $this->BloomexOrder->getsender();
        $this->clientId = $this->warehouse['clientId'];
        $this->clientSecret = $this->warehouse['clientSecret'];
        $this->order_id = $order_id;
        $this->shipmentReference = $order_id . '-' . time();
        $this->access = $this->getAccess();

        $pickupAddress  = $this->getAddressIdByZAddress($this->warehouse['StreetLines1'] . ' ' . $this->warehouse['StreetLines2'] . ','.$this->warehouse['District'].',' . $this->warehouse['City'] . ' ' . $this->warehouse['PostalCode']);
        $this->pickup_address_id = $pickupAddress['result']?$pickupAddress['dpid']:0;

        if ($order_id) {
            $deliveryAddress  = $this->getAddressIdByZAddress($this->BloomexOrder->_StreetLines1 . ' ' . $this->BloomexOrder->_StreetLines2 . ($this->BloomexOrder->_District?','.$this->BloomexOrder->_District:'') . ',' . $this->BloomexOrder->_City . ' ' . $this->BloomexOrder->_PostalCode);
            $this->delivery_address_id = $deliveryAddress['result']?$deliveryAddress['dpid']:0;
        }



    }

    function getAccess()
    {

        $ch_access = curl_init($this->serviceUrlAccess . '?client_id=' . $this->clientId . '&client_secret=' . $this->clientSecret . '&grant_type=client_credentials');
        curl_setopt($ch_access, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_access, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch_access, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_access, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $curl_access_response = curl_exec($ch_access); // Execute REST Request


        if (curl_errno($ch_access)) {
            echo 'Curl error: ' . curl_error($ch_access) . "\n";
            die;
        }
        curl_close($ch_access);
        if (!$curl_access_response) {
            echo 'Failed loading ' . "\n";
            echo $curl_access_response . "\n";
            die;
        } else {
            $response_access = json_decode($curl_access_response);
            if ($response_access && isset($response_access->access_token)) {
                return $response_access->access_token;
            }
        }
    }

    function getAddressIdByZAddress($address)
    {
        $res = [];
        $service_url = $this->serviceUrl . '/ParcelAddress/2.0/domestic/addresses?q=' . urlencode($address) . '&count=1';
        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . ''
        ));

        $curl_response = curl_exec($ch); // Execute REST Request
        if (curl_errno($ch)) {
            $res['error'] = 'Curl error: ' . curl_error($ch) . "\n";

        }

        curl_close($ch);
        if (!$curl_response) {
            $res['error'] = 'curl error getAddressIdByZAddress';

        } else {
            $response = json_decode($curl_response);
            if ($response && $response->addresses && is_array($response->addresses)) {

                $res['result'] = 'success';
                $res['dpid'] = $response->addresses[0]->dpid ?: $this->getFirstElementFromSuggestedAddresses($address);

            } else {
                $res['error'] = "Address is invalid and NzPost can't  find addressId ($address)";

            }
        }
        return $res;
    }

    function getOptions()
    {

        $urlQuery = '?pickup_dpid=' . $this->pickup_address_id . '&delivery_dpid=' . $this->delivery_address_id . '&weight=3&height=20&length=50&width=20';
        $options_url = $this->serviceUrl . '/ShippingOptions/2.0/domestic';

        $options_url .= $urlQuery;

        $ch_options = curl_init($options_url);
        curl_setopt($ch_options, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_options, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_options, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_options, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . ''
        ));

        $curl_options_response = curl_exec($ch_options); // Execute REST Request

        if (curl_errno($ch_options)) {
            echo 'Curl error: ' . curl_error($ch_options) . "\n";
            die;
        }
        curl_close($ch_options);
        if (!$curl_options_response) {
            echo 'Failed loading ' . "\n";
            echo $curl_options_response . "\n";
            die;
        } else {
            $response_options = json_decode($curl_options_response,true);
            if ($response_options && isset($response_options['errors']) && is_array($response_options['errors'])) {
                foreach ($response_options['errors'] as $q) {
                    echo $q['message'] . "<br>";
                    die;
                }
            }
        }

        $price = array_column($response_options['services'], 'price_including_gst');
        array_multisort($price, SORT_ASC, $response_options['services']);

        return $response_options;

    }

    function getFirstElementFromSuggestedAddresses($address)
    {
        $res = $this->getSuggestedAddresses($address);
        if($res['addresses']){
            reset($res['addresses']);
            return key($res['addresses']);
        }
        return null;
    }
    function getSuggestedAddresses($address)
    {
        $res = [];

        $options_url = $this->serviceUrl . '/addresschecker/1.0/find?address_line_1='.urlencode($address);

        $ch_options = curl_init($options_url);
        curl_setopt($ch_options, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_options, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_options, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_options, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . ''
        ));

        $curl_options_response = curl_exec($ch_options); // Execute REST Request

        if (curl_errno($ch_options)) {
            $res['error'] =  'Curl error: ' . curl_error($ch_options) . "\n";
        }
        curl_close($ch_options);
        if (!$curl_options_response) {
            $res['error'] = 'Failed loading ' . $curl_options_response . "\n";
        } else {
            $response_options = json_decode($curl_options_response,true);
            if ($response_options && isset($response_options['errors']) && is_array($response_options['errors'])) {
                foreach ($response_options['errors'] as $q) {
                    $res['error'] .= $q['message'];
                }
            } else {
                $res['result'] = 'success';
                foreach ($response_options['addresses'] as $q) {
                    $res['addresses'][$q['DPID']] = $q['FullAddress'];
                }

            }
        }


        return $res;

    }


    function getSuggestedAddressDetails($dpid)
    {

        $res = [];
        $options_url = $this->serviceUrl . '/addresschecker/1.0/details?dpid='.$dpid;

        $ch_options = curl_init($options_url);
        curl_setopt($ch_options, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_options, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_options, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_options, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . ''
        ));

        $curl_options_response = curl_exec($ch_options); // Execute REST Request

        if (curl_errno($ch_options)) {
            $res['error'] =  'Curl error: ' . curl_error($ch_options) . "\n";
        }
        curl_close($ch_options);
        if (!$curl_options_response) {
            $res['error'] = 'Failed loading ' . $curl_options_response . "\n";
        } else {
            $response_options = json_decode($curl_options_response,true);
            if ($response_options && isset($response_options['errors']) && is_array($response_options['errors'])) {
                foreach ($response_options['errors'] as $q) {
                    $res['error'] .= $q['message'];
                }
            } else {

                $res['result'] = 'success';
                $res['details'] = [
                    'suite' => $response_options['details'][0]['UnitValue'],
                    'street_number' => $response_options['details'][0]['StreetNumber'] .$response_options['details'][0]['StreetAlpha'],
                    'street_name' => $response_options['details'][0]['RoadName'] .' '.$response_options['details'][0]['RoadTypeName'],
                    'district' => $response_options['details'][0]['Suburb'],
                    'city' => $response_options['details'][0]['CityTown'],
                    'zip' => $response_options['details'][0]['Postcode']
                ];

            }
        }


        return $res;

    }


    function createshipment($request)
    {
        $service_url = $this->serviceUrl . '/parcellabel/v3/labels';
        $option_type = $request['option_type'];
        list($service_code, $carrier) = explode('|', $option_type);

        $boxes = explode(',', $request['parcels']);
        $weights = explode(',', $request['weights']);
        $lengths = explode(',', $request['lengths']);
        $widths = explode(',', $request['widths']);
        $heights = explode(',', $request['heights']);



        $add_ons = $request['add_ons']?[$request['add_ons']]:[];
        if(isset($request['cpsr']) && $request['cpsr']) {
            array_push($add_ons,'CPSR');
        }
        $parcels = [];
        foreach ($boxes as $k => $box) {
            $parcels[] = [
                "service_code" => $service_code,
                "return_indicator" => "OUTBOUND",
                "add_ons" => $add_ons,
                "description" => "",
                "dimensions" => [
                    "length_cm" => (int)$lengths[$k],
                    "width_cm" => (int)$widths[$k],
                    "height_cm" => (int)$heights[$k],
                    "weight_kg" => (int)$weights[$k]
                ]
            ];

        }
        $res = array();


        $data = [
            "carrier" => $carrier,
            "orientation" => "LANDSCAPE",
            "despatch_date" => $this->BloomexOrder->_deliverydate,
            "format" => "PDF",
            "job_number" => (int)substr($request['order_id'], -6, 6),
            "sender_reference_1" => 'Bloomex order ' . $request['order_id'],
            "sender_details" => [
                "name" => $this->warehouse['PersonName'],
                "phone" => $this->warehouse['PhoneNumber'],
                "email" => $this->warehouse['WarehouseEmail'],
                "company_name" => $this->warehouse['CompanyName'],
                "site_code" => $this->siteCode
            ],
            "receiver_details" => [
                "name" => $this->BloomexOrder->_PersonName,
                "phone" => $this->BloomexOrder->_PhoneNumber,
                "email" => $this->BloomexOrder->_PersonEmail,

            ],
            "pickup_address" => [
                "company_name" => $this->warehouse['CompanyName'],
                "dpid" => (int) $this->pickup_address_id,
                "country_code" => "NZ"
            ],
            "delivery_address" => [
                "company_name" => $this->BloomexOrder->_CompanyName,
                "country_code" => "NZ",
                "instructions" => $this->BloomexOrder->_CustomerComments,
                "dpid" => (int) $this->delivery_address_id
            ],
            "parcel_details" => $parcels
        ];


        $data_string = json_encode($data);

        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . '',
            'Content-Length: ' . strlen($data_string)
        ));
        $curl_response = curl_exec($ch); // Execute REST Request
        if (curl_errno($ch)) {
            $res['error'] = 'Curl error: ' . curl_error($ch);
        }




        curl_close($ch);
        if (!$curl_response) {
            $res['error'] = 'Failed loading please check inputs';
        } else {
            $response = json_decode($curl_response);
            if ($response && isset($response->errors) && $response->errors && is_array($response->errors)) {
                foreach ($response->errors as $e) {
                    $res['error'] = $e->message . ' (' . $e->details . ')';
                }
            }

            if ($response && $response->success && isset($response->consignment_id)) {
                $res['shipment'] = 'Your shipment created successfully: Consignment ID: ' . $response->consignment_id;
                $res['shipment_id'] = $response->consignment_id;
                $res['order_id'] = $request['order_id'];
                $res['success'] = true;
                $this->BloomexOrder->addshipment(intval($request['order_id']), $response->consignment_id, $request['sender'], $curl_response);
            }

        }
        die(json_encode($res));
    }


    function getTrackingNumber($shipment_id)
    {
        $service_url_status = $this->serviceUrl . '/ParcelLabel/v3/labels/' . $shipment_id . '/status';

        $ch_status = curl_init($service_url_status);
        curl_setopt($ch_status, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_status, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_status, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_status, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . ''
         ));
        $curl_response_status = curl_exec($ch_status); // Execute REST Request


        if (curl_errno($ch_status)) {
            return ['error' => 'Curl error: ' . curl_error($ch_status)];
        }
        curl_close($ch_status);

        if (!$curl_response_status) {
            return ['error' => 'Failed loading '];
        } else {
            $response = json_decode($curl_response_status);
            if ($response && isset($response->labels) && $response->labels && is_array($response->labels)) {
                return $response->labels[0]->tracking_reference;
            }

        }
        return '';
    }


    function print_label($request)
    {
        $service_url_label = $this->serviceUrl . '/ParcelLabel/v3/labels/' . $request['shipment_id'] . '/?format=PDF&page=1';

        $ch_label = curl_init($service_url_label);
        curl_setopt($ch_label, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_label, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_label, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_label, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'client_id: ' . $this->clientId . '',
            'Authorization: Bearer ' . $this->access . '',
        ));
        $curl_response_label = curl_exec($ch_label); // Execute REST Request


        if (curl_errno($ch_label)) {
            return ['error' => 'Curl error: ' . curl_error($ch_label)];
        }
        curl_close($ch_label);

        if (!$curl_response_label) {
            return ['error' => 'Failed loading '];
        } else {

            $trackingNumber = $this->getTrackingNumber($request['shipment_id']);

            $this->BloomexOrder->addshipmentTrackingNumber(intval($request['order_id']), $trackingNumber,$request['shipment_id'], $request['sender']??$_SESSION['session_username']);

            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . strlen($curl_response_label));
            header('Content-disposition: inline; filename="label"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            die($curl_response_label);
        }
    }

    function deleteshipment($request)
    {
        $checkshipment = $this->BloomexOrder->get_shipment_id(intval($request['order_id']));
        $res = array();
        if ($checkshipment['shipment_id']) {
            $res['message'] = '<br>Consignment ID (' . $checkshipment['shipment_id'] . ') successfully deleted';
            $this->BloomexOrder->deleteshipment(intval($request['order_id']), $request['sender']??$_SESSION['session_username'], $checkshipment['shipment_id']);

        } else {
            $res['error'] = 'Shipment already deleted';
        }
        die(json_encode($res));
    }

    function createmanifest($request)
    {

        $res = array();
        $checkwarehouses = $this->BloomexOrder->checkwarehousesbyorder($request['orders'], $request['warehouse']);
        if ($checkwarehouses) {
            $res['error'] = '<br>These order(s) have different warehouses <br>' . $checkwarehouses;
        } else {
            $shipments = $this->BloomexOrder->get_shipments($request['orders']);
            if (!$shipments) {
                $res['error'] = 'There are not active shipments';
            } elseif(count($shipments) > 1 and strtolower($request['choose_carrier']) == 'pace') {
                $res['error'] = 'There are more than one shipments for carrier PACE';
            } else {
                $service_order_url = $this->serviceUrl . '/parcelpickup/v3/bookings';

                $pickup = new DateTime($request['pickup']);
                $pickup->setTimezone(new DateTimeZone('	Pacific/Auckland'));

                $data_order = [
                    "carrier" => $request['choose_carrier'],
                    "caller" => $this->warehouse['CompanyName'],
                    "name" => $this->warehouse['PersonName'],
                    "phone" => $this->warehouse['PhoneNumber'],
                    "pickup_date_time" => $pickup->format('Y-m-d')."T10:00:00",
                    "parcel_quantity" => count($shipments),
                    "service_code" => $request['service_code'],
                    "pickup_address" => [
                        "name" => $this->warehouse['PersonName'],
                        "phone" => $this->warehouse['PhoneNumber'],
                        "email" => $this->warehouse['WarehouseEmail'],
                        "company_name" => $this->warehouse['CompanyName'],
                        "address_id" => $this->pickup_address_id
                    ],
                ];

                if(strtolower($request['choose_carrier']) == 'pace'){
                    $this->BloomexOrder->GetOrderDetails(intval(rtrim($request['orders'],',')));
                    $data_order['delivery_address'] = [
                        "name" => $this->BloomexOrder->_PersonName,
                        "phone" => $this->BloomexOrder->_PhoneNumber,
                        "email" => $this->BloomexOrder->_PersonEmail,
                        "company_name" => $this->BloomexOrder->_CompanyName,
                        "address_id" => $this->getAddressIdByZAddress($this->BloomexOrder->_StreetLines1 . ' ' . $this->BloomexOrder->_StreetLines2 . ',' . $this->BloomexOrder->_City . ' ' . $this->BloomexOrder->_PostalCode),
                        "country_code" => "NZ",
                        "instructions" => $this->BloomexOrder->_CustomerComments
                    ];
                }


                $data_order_string = json_encode($data_order);
                $ch_order = curl_init($service_order_url);
                curl_setopt($ch_order, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch_order, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch_order, CURLOPT_POSTFIELDS, $data_order_string);
                curl_setopt($ch_order, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_order, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'client_id: ' . $this->clientId . '',
                    'Authorization: Bearer ' . $this->access . '',
                    'Content-Length: ' . strlen($data_order_string)
                ));
                $curl_order_response = curl_exec($ch_order); // Execute REST Request
                if (curl_errno($ch_order)) {
                    $res['error'] = 'Curl error: ' . curl_error($ch_order);
                }



                curl_close($ch_order);
                if (!$curl_order_response) {
                    $res['error'] = 'Failed loading';
                } else {
                    $response_order = json_decode($curl_order_response);
                    if ($response_order && $response_order->errors && is_array($response_order->errors)) {
                        foreach ($response_order->errors as $e_order) {
                            $res['error'] = $e_order->message . ' (' . $e_order->details . ')';
                        }
                    }

                    if ($response_order && $response_order->success && $response_order->results->job_id && $response_order->results->job_number) {
                        $res['msg'] = "Your mainfest created successfully. Manifest id : ".$response_order->results->job_id." <br>";
                        foreach ($shipments as $r) {

                            $this->BloomexOrder->addmanifest($r, $request['sender'], $response_order->results->job_id,$response_order->results->job_number, $request['warehouse'], $curl_order_response);
                        }
                    }
                }
            }
        }
        die(json_encode($res));
    }


}

if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'createshipment') {
    $nzpost = new NZPost($_REQUEST['order_id']);
    $nzpost->createshipment($_REQUEST);
}
if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'deleteshipment') {
    $nzpost = new NZPost($_REQUEST['order_id']);
    $nzpost->deleteshipment($_REQUEST);
}
if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'createmanifest') {
    $nzpost = new NZPost('', $_REQUEST['warehouse']);
    $nzpost->createmanifest($_REQUEST);
}

if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'printlabel') {
    $nzpost = new NZPost($_REQUEST['order_id']);
    $nzpost->print_label($_REQUEST);
}
