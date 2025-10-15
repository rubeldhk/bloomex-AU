<?php

date_default_timezone_set('Australia/Sydney');


class AusPost {

    var $username = null;
    var $password = null;
    var $account = null;
    var $access = null;
    var $order_id = null;
    var $BloomexOrder = null;
    var $serviceUrl = 'https://digitalapi.auspost.com.au/shipping/v1';
    var $shipmentReference = '';

    function __construct($order_id = '', $warehouse = '') {
        require_once('bloomexorder.php');
        $this->BloomexOrder = new BloomexOrder();
        if ($order_id) {
            $this->BloomexOrder->GetOrderDetails(intval($order_id));
        }
        if ($warehouse) {
            $this->BloomexOrder->_WH = $warehouse;
        }
        $this->warehouse = $this->BloomexOrder->getsenderondemand();
        $this->username = $this->warehouse['username'];
        $this->password = $this->warehouse['Password'];
        $this->account = $this->warehouse['customerNumber'];
        $this->access = base64_encode($this->username . ":" . $this->password);
        $this->order_id = $order_id;
        $this->shipmentReference = $order_id . '-' . time();

        if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id)) {
            mkdir($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id, 0777);
        }

        if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'dev.' || substr($_SERVER['HTTP_HOST'], 0, 6) == 'stage.') {
            $this->serviceUrl = 'https://digitalapi.auspost.com.au/test/shipping/v1';
        }
    }

    function getaccounts() {

        $service_accounts_url = $this->serviceUrl . '/accounts/' . $this->account;
        $ch_accounts = curl_init($service_accounts_url);
        curl_setopt($ch_accounts, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_accounts, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_accounts, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_accounts, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Account-Number: ' . $this->account . '',
            'Authorization: Basic ' . $this->access . ''
        ));
        $curl_accounts_response = curl_exec($ch_accounts); // Execute REST Request
        if (curl_errno($ch_accounts)) {
            echo 'Curl error: ' . curl_error($ch_accounts) . "\n";
            die;
        }
        curl_close($ch_accounts);
        if (!$curl_accounts_response) {
            echo 'Failed loading ' . "\n";
            echo $curl_accounts_response . "\n";
            die;
        } else {
            $response_accounts = json_decode($curl_accounts_response);
            if ($response_accounts && isset($response_accounts->errors) && is_array($response_accounts->errors)) {
                foreach ($response_accounts->errors as $q) {
                    echo $q->message . "<br>";
                    die;
                }
            }
        }
        $choose_type = '<select name="shipping_type">';
        if ($response_accounts->postage_products) {
            foreach ($response_accounts->postage_products as $r) {
                //   if (stripos($r->type, 'POST') !== false && stripos($r->type, 'INTL ') === false) {
                $selected = ($r->type == "PARCEL POST") ? ' selected="selected" ' : '';
                $choose_type .= '<option value="' . $r->product_id . '" ' . $selected . '>' . $r->type . '</option>';
                //   }
            }
        }
        $choose_type .= '</select>';
        return $choose_type;
    }

    function printlabels($labels, $sender) {
        if ($labels->labels) {

            foreach ($labels->labels as $label) {

                $service_url_print_label = $this->serviceUrl . '/labels/' . $label->request_id;
                $ch_print_label = curl_init($service_url_print_label);
                curl_setopt($ch_print_label, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch_print_label, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch_print_label, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_print_label, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Account-Number: ' . $this->account . '',
                    'Authorization: Basic ' . $this->access . ''
                ));
                $curl_response_print_label = curl_exec($ch_print_label); // Execute REST Request
                if (curl_errno($ch_print_label)) {
                    echo 'Curl error: ' . curl_error($ch_print_label) . "\n";
                    die;
                }
                curl_close($ch_print_label);
                if (!$curl_response_print_label) {
                    echo 'Failed loading ' . "\n";
                    echo $curl_response_print_label . "\n";
                    die;
                } else {
                    $response_print_label = json_decode($curl_response_print_label);
                    if ($response_print_label && isset($response_print_label->errors) && is_array($response_print_label->errors)) {
                        foreach ($response_print_label->errors as $p) {
                            echo $p->message . "<br>";
                            die;
                        }
                    }
                    if ($response_print_label && isset($response_print_label->labels) && is_array($response_print_label->labels)) {
                        foreach ($response_print_label->labels as $t) {
                            echo "Your Shipment printing status is " . $t->status . "<br>";
                            if ($t->status == 'AVAILABLE') {

                                $this->BloomexOrder->attachlabel(intval($this->order_id), $t->url, $sender);

                                echo "Your Shipment printing url is <a target='_blank' href='" . $t->url . "'>here</a><hr>";
                            }
                        }
                    }
                    //print label
                    $shipment_label_print_response = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/shipment_label_print_response.json", "w") or die("Unable to open file!");
                    fwrite($shipment_label_print_response, $curl_response_print_label);
                    fclose($shipment_label_print_response);
                    //end print label
                }
            }
        }
    }

    function checkSuburbStateZipValid($state, $subarb, $zip) {
        $res = array();
        $service_url = $this->serviceUrl . '/address?suburb=' . urlencode($subarb) . '&state=' . urlencode($state) . '&postcode=' . urlencode($zip);

        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Account-Number: ' . $this->account . '',
            'Authorization: Basic ' . $this->access . ''
        ));
        $curl_response = curl_exec($ch); // Execute REST Request
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch) . "\n";
            die;
        }

        //check suburb zip state valid
        $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/validate_suburb_request.json", "w") or die("Unable to open file!");
        fwrite($request_file, $service_url);
        $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/validate_suburb_response.json", "w") or die("Unable to open file!");
        fwrite($response_file, $curl_response);
        fclose($response_file);
        fclose($request_file);
        //end check suburb zip state valid

        curl_close($ch);
        if (!$curl_response) {
            $res['error'] = 'suburb validation error' . "\n";
            die(json_encode($res));
        } else {
            $response = json_decode($curl_response);
            if ($response && isset($response->errors) && is_array($response->errors)) {
                foreach ($response->errors as $p) {
                    $res['error'] = $p->message;
                }
                die(json_encode($res));
            }
        }
    }

    function ValidateShipment($data_string) {
        $res = [];
        $service_url = $this->serviceUrl . '/shipments/validation';
        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Account-Number: ' . $this->account . '',
            'Authorization: Basic ' . $this->access . '',
            'Content-Length: ' . strlen($data_string)
        ));
        $curl_response = curl_exec($ch); // Execute REST Request
        if (curl_errno($ch)) {
            $res['error'] = 'Curl error: ' . curl_error($ch);
        }

//validate shipment
        $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/validate_shipment_request.json", "w") or die("Unable to open file!");
        fwrite($request_file, $data_string);
        $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/validate_shipment_response.json", "w") or die("Unable to open file!");
        fwrite($response_file, $curl_response);
        fclose($response_file);
        fclose($request_file);
//end validate shipment

        curl_close($ch);
        if (!$curl_response) {
            $res['error'] = 'Failed loading please check inputs';
            die(json_encode($res));
        } else {
            $response = json_decode($curl_response);
            if ($response && $response->errors && is_array($response->errors)) {
                foreach ($response->errors as $e) {
                    $res['error'] = $e->message;
                }
                die(json_encode($res));
            }
        }
    }

    function createshipment($request) {

        $this->checkSuburbStateZipValid($this->BloomexOrder->_StateOrProvinceCode, $this->BloomexOrder->_City, $this->BloomexOrder->_PostalCode);

        $service_url = $this->serviceUrl . '/shipments';
        $shipping_type = $request['shipping_type'];
        $authority_to_leave = $request['authority_to_leave'];
        $data = array(
            'shipments' =>
            array(
                0 =>
                array(
                    'shipment_reference' => $this->shipmentReference,
                    'customer_reference_1' => 'Order ' . $request['order_id'],
                    'customer_reference_2' => '',
                    'from' =>
                    array(
                        'name' => '' . $this->warehouse['PersonName'] . '',
                        'lines' =>
                        array(
                            0 => '' . $this->warehouse['StreetLines1'] . $this->warehouse['StreetLines2'] . '',
                        ),
                        'suburb' => '' . $this->warehouse['City'] . '',
                        'state' => '' . $this->warehouse['StateOrProvinceCode'] . '',
                        'postcode' => '' . $this->warehouse['PostalCode'] . '',
                        'phone' => '' . $this->warehouse['PhoneNumber'] . '',
                    ),
                    'to' =>
                    array(
                        'name' => '' . $this->BloomexOrder->_PersonName . '',
                        'business_name' => '' . $this->BloomexOrder->_CompanyName . '',
                        'lines' =>
                        array(
                            0 => '' . $this->BloomexOrder->_StreetLines1 . $this->BloomexOrder->_StreetLines2 . '',
                        ),
                        'suburb' => '' . $this->BloomexOrder->_City . '',
                        'state' => '' . $this->BloomexOrder->_StateOrProvinceCode . '',
                        'postcode' => '' . $this->BloomexOrder->_PostalCode . '',
                        'phone' => '' . $this->BloomexOrder->_PhoneNumber . '',
                        'delivery_instructions' =>  ($authority_to_leave) ? ' Authority To Leave  ' : '' . $this->BloomexOrder->_CustomerComments,
                    ),
                    'features' =>
                    array(
                        'PICKUP_DATE' =>
                        array(
                            'attributes' =>
                            array(
                                'date' => $_REQUEST['pickup']
                            )
                        )
                    )
        )));
        $boxes = explode(',', $request['parcels']);
        $weights = explode(',', $request['weights']);
        $lengths = explode(',', $request['lengths']);
        $widths = explode(',', $request['widths']);
        $heights = explode(',', $request['heights']);
        foreach ($boxes as $k => $box) {
            $data['shipments'][0]['customer_reference_2'] .= 'item-' . $k . ", ";
            $data['shipments'][0]['items'][] = array(
                'item_reference' => 'item-' . $k,
                'product_id' => '' . $shipping_type . '',
                'length' => '' . $lengths[$k] . '',
                'height' => '' . $heights[$k] . '',
                'width' => '' . $widths[$k] . '',
                'weight' => '' . $weights[$k] . '',
                'authority_to_leave' => ($authority_to_leave) ? 'true' : 'false',
            );
        }
        $res = array();
        $data_string = json_encode($data);

        $this->ValidateShipment($data_string);



        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Account-Number: ' . $this->account . '',
            'Authorization: Basic ' . $this->access . '',
            'Content-Length: ' . strlen($data_string)
        ));
        $curl_response = curl_exec($ch); // Execute REST Request
        if (curl_errno($ch)) {
            $res['error'] = 'Curl error: ' . curl_error($ch);
        }

//create shipment
        $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/create_shipment_request.json", "w") or die("Unable to open file!");
        fwrite($request_file, $data_string);
        $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/create_shipment_response.json", "w") or die("Unable to open file!");
        fwrite($response_file, $curl_response);
        fclose($response_file);
        fclose($request_file);
//end create shipment



        curl_close($ch);
        if (!$curl_response) {
            $res['error'] = 'Failed loading please check inputs';
        } else {
            $response = json_decode($curl_response);
            if ($response && $response->errors && is_array($response->errors)) {
                foreach ($response->errors as $e) {
                    $res['error'] = $e->message;
                }
            }

            if ($response && isset($response->shipments) && is_array($response->shipments)) {
                $res['shipment'] = 'Your shipment created successfully';
                foreach ($response->shipments as $shipment) {
                    $this->BloomexOrder->addshipment(intval($request['order_id']), $shipment->shipment_id, $shipment->items[0]->tracking_details->article_id, $request['sender'], $curl_response);
                }
                $res_label = $this->createlabel($response, intval($request['order_id']));
                $res = array_merge($res, $res_label);
            }
            die(json_encode($res));
        }
    }

    function createlabel($response, $order_id) {
        $res = array();
        if ($response->shipments) {

            foreach ($response->shipments as $shipment) {
                $service_url_label = $this->serviceUrl . '/labels';
                $items = array();
                foreach ($shipment->items as $i) {
                    $items[] = array('item_id' => $i->item_id);
                }

                $data_label = array(
                    'preferences' =>
                    array(
                        0 =>
                        array(
                            "format" => "PDF",
                            'type' => 'PRINT',
                            'groups' =>
                            array(
                                0 =>
                                array(
                                    'group' => 'Parcel Post',
                                    'layout' => 'THERMAL-LABEL-A6-1PP',
                                    'branded' => true,
                                    'left_offset' => 0,
                                    'top_offset' => 0,
                                ),
                                1 =>
                                array(
                                    'group' => 'Express Post',
                                    'layout' => 'THERMAL-LABEL-A6-1PP',
                                    'branded' => true,
                                    'left_offset' => 0,
                                    'top_offset' => 0,
                                ),
                            ),
                        ),
                    ),
                    'shipments' =>
                    array(
                        0 =>
                        array(
                            'shipment_id' => '' . $shipment->shipment_id . '',
                            'items' => $items
                        ),
                    ),
                );

                $data_string_label = json_encode($data_label);
                $ch_label = curl_init($service_url_label);
                curl_setopt($ch_label, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch_label, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch_label, CURLOPT_POSTFIELDS, $data_string_label);
                curl_setopt($ch_label, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_label, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Account-Number: ' . $this->account . '',
                    'Authorization: Basic ' . $this->access . '',
                    'Content-Length: ' . strlen($data_string_label)
                ));
                $curl_response_label = curl_exec($ch_label); // Execute REST Request
                if (curl_errno($ch_label)) {
                    $res['error'] = 'Curl error: ' . curl_error($ch_label);
                }
                curl_close($ch_label);
                if (!$curl_response_label) {
                    $res['error'] = 'Failed loading ';
                } else {
                    $response_label = json_decode($curl_response_label);
                    if ($response_label && $response_label->errors && is_array($response_label->errors)) {
                        foreach ($response_label->errors as $u) {
                            $res['error'] = $u->message;
                        }
                    }
                    if ($response_label && $response_label->message) {
                        $this->BloomexOrder->addlabeljson($order_id, $curl_response_label);
                        $res['label'] = $response_label->message;
                    }
//create shipment label
                    $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/create_label_request.json", "w") or die("Unable to open file!");
                    fwrite($request_file, $data_string_label);
                    $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/create_label_response.json", "w") or die("Unable to open file!");
                    fwrite($response_file, $curl_response_label);
                    fclose($response_file);
                    fclose($request_file);
//end create shipment label
                }
            }
        }
        return $res;
    }

    function deleteshipment($request) {
        $checkshipment = $this->BloomexOrder->get_shipment_id(intval($request['order_id']));
        $res = array();
        if ($checkshipment['shipment_id']) {

            $service_url = $this->serviceUrl . '/shipments/' . $checkshipment['shipment_id'];

            $ch = curl_init($service_url); // Create REST Request
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Account-Number: ' . $this->account . '',
                'Authorization: Basic ' . $this->access . ''
            ));
            $ch_response = curl_exec($ch); // Execute REST Request
            if (curl_errno($ch)) {
                $res['error'] = 'Curl error: ' . curl_error($ch) . "\n";
            }

            //delete shipment
            $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/delete_shipment_request.json", "w") or die("Unable to open file!");
            fwrite($request_file, $service_url);
            $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $this->order_id . "/delete_shipment_response.json", "w") or die("Unable to open file!");
            fwrite($response_file, $ch_response);
            fclose($response_file);
            fclose($request_file);
            //end delete shipment


            $response_delete = json_decode($ch_response);
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
                $res['msg'] = '<br>Shipment Id (' . $checkshipment['shipment_id'] . ') successfully deleted';
                $this->BloomexOrder->deleteshipment(intval($request['order_id']), $request['sender'], $checkshipment['shipment_id']);
            } else {

                if ($response_delete && $response_delete->errors && is_array($response_delete->errors)) {
                    foreach ($response_delete->errors as $e) {
                        $res['error'] = $e->message;
                    }
                } else {
                    $res['msg'] = 'The shipment has been deleted.';
                }
            }
            curl_close($ch);
        } else {
            $res['error'] = 'Shipment already deleted';
        }
        die(json_encode($res));
    }

    function createmanifest($request) {
        $res = array();
        date_default_timezone_set('Australia/Sydney');
        $order_reference = $request['warehouse'] . " " . date('d-m-Y H-i');
        $checkwarehoses = $this->BloomexOrder->checkwarehousesbyorder($request['orders'], $request['warehouse']);
        if ($checkwarehoses) {
            $res['error'] = '<br>These order(s) have different warehouses <br>' . $checkwarehoses;
        } else {
            $shipments = $this->BloomexOrder->get_shipments($request['orders']);
            if (!$shipments) {
                $res['error'] = 'There is not active shipment';
            } else {
                $service_order_url = $this->serviceUrl . '/orders';
                $shipment_order = array();
                if ($shipments) {
                    foreach ($shipments as $shipment_id => $order_id) {
                        $shipment_order[] = array('shipment_id' => $shipment_id);
                    }
                }
                $data_order = array(
                    'order_reference' => $order_reference,
                    'payment_method' => 'CHARGE_TO_ACCOUNT',
                    'shipments' => $shipment_order
                );

                $data_order_string = json_encode($data_order);
                $ch_order = curl_init($service_order_url);
                curl_setopt($ch_order, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch_order, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch_order, CURLOPT_POSTFIELDS, $data_order_string);
                curl_setopt($ch_order, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_order, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Account-Number: ' . $this->account . '',
                    'Authorization: Basic ' . $this->access . '',
                    'Content-Length: ' . strlen($data_order_string)
                ));
                $curl_order_response = curl_exec($ch_order); // Execute REST Request
                if (curl_errno($ch_order)) {
                    $res['error'] = 'Curl error: ' . curl_error($ch_order);
                }
                if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference)) {
                    mkdir($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference, 0777);
                }
//create manifest
                $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference . "/create_order_from_shipment_request.json", "w") or die("Unable to open file!");
                fwrite($request_file, $data_order_string);
                $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference . "/create_order_from_shipment_response.json", "w") or die("Unable to open file!");
                fwrite($response_file, $curl_order_response);
                fclose($response_file);
                fclose($request_file);
//end create manifest


                curl_close($ch_order);
                if (!$curl_order_response) {
                    $res['error'] = 'Failed loading';
                } else {
                    $response_order = json_decode($curl_order_response);
                    if ($response_order && $response_order->errors && is_array($response_order->errors)) {
                        foreach ($response_order->errors as $e_order) {
                            $res['error'] = $e_order->message;
                        }
                    }

                    if ($response_order && $response_order->order && $response_order->order->order_id && $response_order->order->shipments && is_array($response_order->order->shipments)) {
                        $res['msg'] = "Your mainfest created successfully<br>";
                        $res['mainfest_id'] = $response_order->order->order_id;
                        foreach ($response_order->order->shipments as $r) {
                            $this->BloomexOrder->addmanifest($shipments[$r->shipment_id], $request['sender'], $response_order->order->order_id, $request['warehouse'], $curl_order_response);
                        }
                    }
                }
            }
        }
        die(json_encode($res));
    }

    function print_manifest($request) {
        $service_url_print_manifest = $this->serviceUrl . '/accounts/' . $this->account . '/orders/' . $request['manifest_id'] . '/summary';
        $ch_print_manifest = curl_init($service_url_print_manifest);
        curl_setopt($ch_print_manifest, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_print_manifest, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch_print_manifest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_print_manifest, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Account-Number: ' . $this->account . '',
            'Authorization: Basic ' . $this->access . ''
        ));
        $curl_response_print_manifest = curl_exec($ch_print_manifest); // Execute REST Request
//print manifest
        if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference)) {
            mkdir($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference, 0777);
        }
        $request_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference . "/create_order_from_shipment_request.json", "w") or die("Unable to open file!");
        fwrite($request_file, $data_order_string);
        $response_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/auspost_ondemand_request_response/" . $order_reference . "/create_order_from_shipment_response.json", "w") or die("Unable to open file!");
        fwrite($response_file, $curl_order_response);
        fclose($response_file);
        fclose($request_file);
//end print manifest


        if (curl_errno($ch_print_manifest)) {
            die('Curl error: ' . curl_error($ch_print_manifest));
        }
        curl_close($ch_print_manifest);
        if (!$curl_response_print_manifest) {
            die('Failed loading ');
        } else {

            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($curl_response_print_manifest));
            header('Content-disposition: inline; filename="mainfest"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            die($curl_response_print_manifest);
        }
    }

}

if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'createshipment') {
    $auspost = new AusPost($_REQUEST['order_id']);
    $auspost->createshipment($_REQUEST);
}
if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'deleteshipment') {
    $auspost = new AusPost($_REQUEST['order_id']);
    $auspost->deleteshipment($_REQUEST);
}
if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'createmanifest') {
    $auspost = new AusPost('', $_REQUEST['warehouse']);
    $auspost->createmanifest($_REQUEST);
}
if ($_REQUEST && isset($_REQUEST['action']) && $_REQUEST['action'] == 'printmanifest') {
    $auspost = new AusPost('', $_REQUEST['warehouse']);
    $auspost->print_manifest($_REQUEST);
}
