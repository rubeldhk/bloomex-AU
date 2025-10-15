<?php
header('Content-Type: text/html; charset=utf-8');
class AV {

    var $client = '';
    var $path_to_wsdl = '';
    var $order = '';

    function __construct() {
        $this->path_to_wsdl = $_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/resourses/AddressValidationService_v2.wsdl";
        ini_set("soap.wsdl_cache_enabled", "0");
        $this->client = new SoapClient($this->path_to_wsdl, array('trace' => 1, 'Major' => '2'));
    }

    function CreateRequest($order_id, $bt = false) {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/resourses/fedex-common.php");
        require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/getorderinformation.php");
        include($_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/access.php");
        $this->order = $order = new GetOrderInformation($order_id, $bt);

        $request['WebAuthenticationDetail'] = array(
            'UserCredential' => array(
                'Key' => $FedexConfig['Key'],
                'Password' => $FedexConfig['Password']
            )
        );
        $request['ClientDetail'] = array(
            'AccountNumber' => $FedexConfig['AccountNumber'], //getProperty('shipaccount')
            'MeterNumber' => $FedexConfig['MeterNumber'] //getProperty('meter')
        );
        $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Address Validation Request v2 using PHP ***');

        $request['Version'] = array('ServiceId' => 'aval', 'Major' => '2', 'Intermediate' => '0', 'Minor' => '0');

        $request['RequestTimestamp'] = date('c');
        $request['Options'] = array('CheckResidentialStatus' => 1,
            'MaximumNumberOfMatches' => 5,
            'StreetAccuracy' => 'LOOSE',
            'DirectionalAccuracy' => 'LOOSE',
            'CompanyNameAccuracy' => 'LOOSE',
            'ConvertToUpperCase' => 1,
            'RecognizeAlternateCityNames' => 1,
            'ReturnParsedElements' => 1);

        $request['AddressesToValidate'] = array(
            0 => array(
                'AddressId' => $order->_id,
                'Address' => array(
                    'StreetLines' => array(strtoupper($order->_StreetLines1)),
                    'City' => strtoupper($order->_City),
                    'StateOrProvinceCode' => strtoupper($order->_StateOrProvinceCode),
                    'PostalCode' => strtoupper($order->_PostalCode),
                    'CountryCode' => strtoupper($order->_CountryCode)
                )
            ),
        );

        return $request;
    }

    function CheckAddressValidation($order_id, $bt = false) {
        require_once('fedexmessage.php');

        $client = $this->client;
        $request = $this->CreateRequest($order_id, $bt);

        $response = $client->addressValidation($request);
        $result['request'] = $request;
        $result['response'] = $response;
        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
            $addressResult = $response->AddressResults;
            $result['valid'] = 1;

            if ($addressResult->ProposedAddressDetails->Score) {
                $result['score'] = $addressResult->ProposedAddressDetails->Score;
            } else {
                $result['score'] = 0;
            }

            if ($addressResult->ProposedAddressDetails->Score == 100) {
                $result['result'] = "success";
                $result['mess'] = 'Address Match 100%';
                $result['Residential'] = $addressResult->ProposedAddressDetails->ResidentialStatus;
            } else {
                $str = '';
                $result['result'] = "Need Modifications <br> Address Match  " . $addressResult->ProposedAddressDetails->Score . "% ";
                $smess = '';
                $suite_exist = false;
                $address_match_table = '';
                $request['AddressesToValidate'][0]['Address']['StreetLines'] = $request['AddressesToValidate'][0]['Address']['StreetLines'][0];
                $request_address = $request['AddressesToValidate'][0]['Address'];
                $response_address = $addressResult->ProposedAddressDetails->Address;
                if ($result['score'] != 0) {

                    $address_match_table = "<br><br><table style='width:1000px'><tr><td>Name</td><td>Our Value</td><td>Suggested Value</td></tr>";
                    foreach ($request_address as $k => $v) {
                        $str .= $v . ' ';
                        $td = '';
                        if (trim($v) == trim($response_address->$k)) {
                            $td .= "<tr><td>" . $k . "</td><td>" . $v . "</td><td>" . $response_address->$k . "</td><td></td></tr>";
                        } else {
                            $suite_exist = true;
                            $values = '"' . $order_id . '","' . $k . '","' . $response_address->$k . '","' . $v . '"';
                            $td .= "<tr style='color:red'><td>" . $k . "</td><td>" . $v . "</td><td>" . $response_address->$k . "</td><td><input type='button' value='Update' onclick='change(" . $values . ",this);' style='width:80px;cursor:pointer;'  class='change_value'><div class='update_resiult'></div></td></tr>";
                        }
                        $address_match_table .= $td;
                    }
                    $address_match_table .= "</table>";
                } else {
                    foreach ($request_address as $k => $v) {
                        $str .= $v . ' ';
                    }
                }


                if (is_array($addressResult->ProposedAddressDetails->Changes)) {
                    foreach ($addressResult->ProposedAddressDetails->Changes as &$value) {
                        $smess .= $FedexMessages[$value] . "<br>";
                    }
                } else {
                    $smess .= $FedexMessages[$addressResult->ProposedAddressDetails->Changes];
                }
                if ($addressResult->ProposedAddressDetails->Score == 95 && !$suite_exist) {
                    $smess .= '<p>probably missing suite number</p>';
                }
                $smess = "<div style='font-weight:bold'>" . $smess . "</div>";
                $smess = $smess . '<table id="table_data" style="table-layout: auto;width:585px;  text-align:center; border:1px solid #ccc;">';  //$response->ParsedAddress;
                $smess = $smess . '<tr><td style="border: 1px solid #ccc;">Name</td><td style="border: 1px solid #ccc;">Value</td><td style="border: 1px solid #ccc;">Fedex message</td></tr>';
                if (isset($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements) && is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements)) {
                    foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements as &$value) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                    }
                } else {
                    if (isset($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode) && $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode != NULL) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Changes] . '</td></tr>';
                    }
                }

                if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements)) {
                    foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements as &$value) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                    }
                } else {
                    if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine != NULL) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Changes] . '</td></tr>';
                    }
                }
                if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements)) {
                    foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements as &$value) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                    }
                } else {
                    if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity != NULL) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Changes] . '</td></tr>';
                    }
                }
                if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements)) {
                    foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements as &$value) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                    }
                } else {
                    if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode != NULL) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Changes] . '</td></tr>';
                    }
                }
                if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements)) {
                    foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements as &$value) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                    }
                } else {
                    if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode != NULL) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Changes] . '</td></tr>';
                    }
                }
                if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements)) {
                    foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements as &$value) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                    }
                } else {
                    if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode != NULL) {
                        $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Changes] . '</td></tr>';
                    }
                }
                $smess = $smess . '</table>';


                $smess = $smess . $address_match_table;
                $result['result'] .= "<br>Old Address: <strong>" . $str . "</strong>";
                $result['mess'] = $smess;
            }
        } else {
            $result['score'] = 0;
            $result['valid'] = 0;
        }

        require_once('getorderinformation.php');
        $ord = new GetOrderInformation($order_id);
        $ord->update_score($result['score'], $order_id);
        return $result;
    }

    function CheckAddressValidationAndShow($order_id) {
        require_once('fedexmessage.php');
        require_once('getorderinformation.php');
        $client = $this->client;
        $request = $this->CreateRequest($order_id);
        $order = $this->order;
        $smess = '';
        try {
            if (setEndpoint('changeEndpoint')) {
                $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }

            $response = $client->addressValidation($request);

            $result['response'] = $response;
            //    echo $client->__getLastRequest();
            //    echo $client->__getLastResponse();
            //    die();
            $address = $order->_StreetLines1 . ' ' . $order->_City . ' ' . $order->_StateOrProvinceCode . ' ' . $order->_PostalCode . ' ' . $order->_CountryCode;
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                $addressResult = $response->AddressResults;
                if ($addressResult->ProposedAddressDetails->Score > 94) {
                    $result['result'] = "success";
                    $smess = ' <table><tr><td>Address for verification:</td><td><div id="for_map">' . $address . '</div></td></tr>';
                    $smess = $smess . '<tr><td> <div id="MessFromFed"> FedEX messages: </td><td style="font-weight: bold;">';
                    if (is_array($addressResult->ProposedAddressDetails->Changes)) {
                        foreach ($addressResult->ProposedAddressDetails->Changes as &$value) {
                            $smess = $smess . $FedexMessages[$value] . "<br>";
                        }
                        $smess .= "</td></tr></table>";
                    } else {
                        $smess = $smess . '<div > ';
                        $smess = $smess . $FedexMessages[$addressResult->ProposedAddressDetails->Changes];
                        $smess .= "</td></tr></table>";
                    }

                    $smess = $smess . '</div><br><br>';
                    $smess = $smess . '<table style="table-layout: auto; text-align:left; border:1px solid #ccc;">';  //$response->ParsedAddress;
                    $smess = $smess . '<tr><td style="border: 1px solid #ccc;">Name</td><td style="border: 1px solid #ccc;">Value</td><td style="border: 1px solid #ccc;">Fedex message</td></tr>';
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Changes] . '</td></tr>';
                        }
                    }

                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Changes] . '</td></tr>';
                        }
                    }
                    $smess = $smess . '</table>';
                } else {
                    $result['result'] = "need modifications";
                    $smess = ' <table><tr><td>Address for verification:</td><td><div id="for_map">' . $address . '</div></td></tr>';
                    $smess = $smess . '<tr><td><div id="MessFromFed"> FedEX messages: </td><td style="font-weight: bold;">';
                    if (is_array($addressResult->ProposedAddressDetails->Changes)) {
                        foreach ($addressResult->ProposedAddressDetails->Changes as &$value) {
                            $smess = $smess . $FedexMessages[$value] . "<br>";
                        }
                        $smess .= "</td></tr></table>";
                    } else {
                        $smess = $smess . $FedexMessages[$addressResult->ProposedAddressDetails->Changes];
                        $smess .= "</td></tr></table>";
                    }
                    $smess = $smess . '</div><br><br>';
                    $smess = $smess . '<table id="table_data" style="table-layout: auto;width:585px; text-align:center; border:1px solid #ccc;">';  //$response->ParsedAddress;
                    $smess = $smess . '<tr><td style="border: 1px solid #ccc;">Name</td><td style="border: 1px solid #ccc;">Value</td><td style="border: 1px solid #ccc;">Fedex message</td></tr>';
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedUrbanizationCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Changes] . '</td></tr>';
                        }
                    }

                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStreetLine->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedCity->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedStateOrProvinceCode->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedPostalCode->Elements->Changes] . '</td></tr>';
                        }
                    }
                    if (is_array($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements)) {
                        foreach ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements as &$value) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $value->Name . '</td><td style="border: 1px solid #ccc;">' . $value->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$value->Changes] . '</td></tr>';
                        }
                    } else {
                        if ($addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode != NULL) {
                            $smess = $smess . '<tr><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Name . '</td><td style="border: 1px solid #ccc;">' . $addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Value . '</td><td style="border: 1px solid #ccc;">' . $FedexMessages[$addressResult->ProposedAddressDetails->ParsedAddress->ParsedCountryCode->Elements->Changes] . '</td></tr>';
                        }
                    }
                    $smess = $smess . '</table>';
                }
                $result['mess'] = $smess;
                $result['valid'] = 1;
                $result['score'] = $addressResult->ProposedAddressDetails->Score;
                $ord = new GetOrderInformation($order_id);
                $ord->update_score($addressResult->ProposedAddressDetails->Score, $order_id);
            } else {
                $result['result'] = "need modifications";
                $smess = $smess . '<div id="MessFromFed"> FedEX messages: Service Temporarily Unavailable </div> ';
                $result['mess'] = $smess;
                $result['valid'] = 0;
            }
        } catch (SoapFault $exception) {
            $result['result'] = "fail-soap";
            $smess = $smess . '<div id="MessFromFed"> FedEX messages: Service Temporarily Unavailable </div> ';
            $result['mess'] = $smess;
            $result['valid'] = 0;
        }
        $result['mess'] = '<table class="result_table"><tr><td>response</td><td>' . $result['result'] . '</td></tr><tr><td>result</td><td>' . $result['mess'] . '</td></tr></table>';
        return $result;
    }

}

$res = new AV();
if (isset($_GET['option'])) {
    if ($_GET['option'] == 'AddressValidation' && $_GET['order_id']) {
        ?>
        <html lang="en">
            <head>
                <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
            </head>
            <body>
                <div id="dialog-confirm" title="Update Information"></div>
                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                <script type="text/javascript">
                    $.ajax({
                        type: 'POST',
                        url: 'av.php',
                        timeout: 20000,
                        data: {
                            order_id: <?php echo $_REQUEST['order_id'] ?>, option: "CheckAddressValidation"

                        },
                        success: function (data) {
                            data = $.parseJSON(data);
                            document.getElementById("fedex_result").innerHTML = '<table class="result_table"><tr><td>Response</td><td>' + data.result + '</td></tr><tr><td>Result</td><td>' + data.mess + '</td></tr></table>';

                        },
                        error: function (x, t, m) {
                            document.getElementById("loader").style.display = "none";
                            document.getElementById("Fedex").style.display = "none";
                            document.getElementById("return").innerHTML = "FedEx's server not responding. Try again later."
                            document.getElementById("return_bl").style.display = "block";
                        }
                    });

                    function isInt(x) {
                        var y = parseInt(x, 10);
                        return !isNaN(y) && x == y && x.toString() == y.toString();
                    }

                    function change(order_id, type, value, request, element) {
                        var type_db = '';
                        if (type == 'City') {
                            type_db = 'city';
                        } else if (type == 'StateOrProvinceCode') {
                            type_db = 'state';
                        } else if (type == 'PostalCode') {
                            type_db = 'zip';
                        } else {
                            var suite = '';
                            var street_number = '';
                            var street_name = '';

                            type_db = 'StreetLines';
                            var result = value.split(' ')

                            var len = result.length
                            var j = 0;
                            $.each(result, function (index, value) {
                                if (index == 0) {
                                    street_number = value;
                                } else {
                                    if (index == len - 1) {
                                        if (parseInt(value) == value) {
                                            suite = value;
                                        } else {
                                            if (j == 0) {
                                                street_name = street_name + value;
                                            } else {
                                                street_name = street_name + " " + value;
                                            }
                                            j++;
                                        }
                                    } else {
                                        if (j == 0) {
                                            street_name = street_name + value;
                                        } else {
                                            street_name = street_name + " " + value;
                                        }
                                        j++;
                                    }
                                }
                            });
                            document.getElementById("dialog-confirm").innerHTML = "<p><strong>Request Address:  </strong>" + request + "</p><p><strong>Responce Address:  </strong>" + value + "</p>" +
                                    "<table><tr><td>suite</td><td><input id='suite' value='" + suite + "'></td></tr>" +
                                    "<tr><td>street_number</td><td><input id='street_number' value='" + street_number + "'></td></tr>" +
                                    "<tr><td>street_name</td><td><input id='street_name' value='" + street_name + "'></td></tr></table>" +
                                    "<p>please note response address may miss apt#, you still need to add it if available</p>";

                            $("#dialog-confirm").dialog({
                                resizable: false,
                                height: "auto",
                                width: 400,
                                modal: true,
                                close: function () {
                                    element.value = 'Update'
                                    $(this).dialog("close");
                                },
                                buttons: {
                                    "Update": function () {
                                        suite = $('#suite').val();
                                        street_number = $('#street_number').val();
                                        street_name = $('#street_name').val();
                                        change_value(order_id, 'suite', suite, element);
                                        change_value(order_id, 'street_number', street_number, element);
                                        change_value(order_id, 'street_name', street_name, element);
                                        element.value = 'Update'
                                        $(this).dialog("close");
                                    },
                                    Cancel: function () {
                                        element.value = 'Update'
                                        $(this).dialog("close");
                                    }
                                }

                            });

                            console.log(result)
                        }
                        element.value = 'processing'

                        if (type_db != 'StreetLines') {
                            change_value(order_id, type_db, value, element);
                        }

                    }

                    function change_value(order_id, type_db, value, element) {

                        $.ajax({
                            type: 'POST',
                            url: 'av.php',
                            timeout: 20000,
                            data: {
                                order_id: order_id, type_db: type_db, option: "changevalue", new_value: value

                            },
                            success: function (data) {
                                if (data != 'success') {
                                    element.nextElementSibling.innerHTML = "Value didn't changed";
                                } else {

                                    $.ajax({
                                        type: 'POST',
                                        url: 'av.php',
                                        timeout: 20000,
                                        data: {
                                            order_id: order_id, option: "CheckAddressValidation"

                                        },
                                        success: function (data) {
                                            data = $.parseJSON(data);
                                            document.getElementById("fedex_result").innerHTML = '<table class="result_table"><tr><td>Response</td><td>' + data.result + '</td></tr><tr><td>Result</td><td>' + data.mess + '</td></tr></table>';
                                            element.src = 'resourses/change.png'
                                        },
                                        error: function (x, t, m) {
                                            document.getElementById("loader").style.display = "none";
                                            document.getElementById("Fedex").style.display = "none";
                                            document.getElementById("return").innerHTML = "FedEx's server not responding. Try again later."
                                            document.getElementById("return_bl").style.display = "block";
                                        }
                                    });

                                }

                            },
                            error: function (x, t, m) {
                                element.nextElementSibling.innerHTML = "Value didn't changed";
                            }
                        });
                    }


                </script>

                <div id="fedex_result"></div>
                <input type="button" value="Close this window" onclick="self.close();window.opener.location.reload()">
                &nbsp;&nbsp;<input type="button" value="Update this window" onclick="window.location.reload()">
                <style>

                    .result_table td{
                        border: 1px solid #ccc;
                        border-spacing: 12px;
                        text-align: center;
                        padding: 10px;
                    }
                    #table_data{
                        width: 1000px !important;
                    }


                </style>
            </body>
        </html>
        <?php
    }
}
if (isset($_POST['option'])) {

    if ($_POST['option'] == 'CheckAddressValidationAndShow') {
        $order_id = $_POST['order_id'];
        $result = $res->CheckAddressValidationAndShow($order_id);
        exit(json_encode($result));
    } elseif ($_POST['option'] == 'CheckAddressValidation') {
        $order_id = $_POST['order_id'];
        $result = $res->CheckAddressValidation($order_id);
        exit(json_encode($result));
    } elseif ($_POST['option'] == 'changevalue') {
        $order_id = $_POST['order_id'];
        $type_db = $_POST['type_db'];
        $new_value = $_POST['new_value'];
        require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/getorderinformation.php");
        $ord = new GetOrderInformation($order_id);
        $result = $ord->update_order_information($new_value, $type_db, $order_id);
        exit($result);
    }
}
if (isset($option)) {
    if ($option == 'CheckAddressValidation') {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/getorderinformation.php");
        $result = $res->CheckAddressValidation($order_id);
    }
}
?>
