<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
require_once('../warehouses.php');
session_name(md5($mosConfig_live_site));
session_start();

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$gopeople_host = 'https://api.gopeople.com.au'; //'http://ppost-api.stagingserver.com.au';

$order_id = (int) $_REQUEST['order_id'];
$delivery_id = isset($_REQUEST['delivery_id'])?(int) $_REQUEST['delivery_id']:'';
$order_sql = $mysqli->query("SELECT warehouse FROM jos_vm_orders WHERE order_id='$order_id'");
if ($order_sql->num_rows > 0) {
    $order_obj = $order_sql->fetch_object();
    $warehouse = $order_obj->warehouse;
}

Switch ($warehouse) {
    case 'WH15':
        $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MjU3MzQ5MzQsImp0aSI6IjAraXZhYlRhS25sXC9idWRnMlM1MlI3bjIycW8yY2JMZGtqbHZtUjE4d084PSIsImlzcyI6InBlb3BsZXBvc3QuY29tLmF1IiwiZGF0YSI6eyJ1c2VySWQiOjMyNzQwfX0.vd1ROyZSQyyoVmE3YwrhZ6bE0w54WKx7qo3hhSWsnyHV4yL52Ylm24g7gfaG5GKx5RxNJl3O1dSDyDiiFoJv8w';
        break;
    case 'WH12':
        $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE0OTE4NzA0NDIsImp0aSI6IllvV3BWTVl1WTFGK3Era2U3NnFoRFZGTkNNdTFTUTZLUWx2NVl3elNnc1E9IiwiaXNzIjoicGVvcGxlcG9zdC5jb20uYXUiLCJkYXRhIjp7InVzZXJJZCI6NTE0NH19.BoB-eJjn1Z95fAMXzwOj1gzaSqcW51mDoXFSoGqDRetwlbQ4Axzd93MvKlv9Xosy80eTCxJHfzt8Xt7sYBYRJg';
        break;
    case 'p01':
        $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE0OTE4NzA0NDIsImp0aSI6IllvV3BWTVl1WTFGK3Era2U3NnFoRFZGTkNNdTFTUTZLUWx2NVl3elNnc1E9IiwiaXNzIjoicGVvcGxlcG9zdC5jb20uYXUiLCJkYXRhIjp7InVzZXJJZCI6NTE0NH19.BoB-eJjn1Z95fAMXzwOj1gzaSqcW51mDoXFSoGqDRetwlbQ4Axzd93MvKlv9Xosy80eTCxJHfzt8Xt7sYBYRJg';
        break;

    case 'WH14':
        $gopeople_token = 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MDcyNDE1NDYsImp0aSI6IndvNjhNdk02QTZhXC9KSXdHQmY4c1JEamlcLzFDcndNXC9td2YrNlBiYnVYM1U9IiwiaXNzIjoicGVvcGxlcG9zdC5jb20uYXUiLCJkYXRhIjp7InVzZXJJZCI6MjQ3MTl9fQ.Rr4aX79Sr3rjVoyQOfwSCkS3xw8YtAipkj-5ipQX7UXZpTEJ_4BUxu17vv3GxuFQcZ9Dk-0AzmwWK1BIFpNwfQ';
        break;
}



Switch (isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'cancel':
        GoPeopleCancel();
        break;

    case 'GoPeopleBook':
        GoPeopleBook();
        break;

    case 'GoPeopleQuote':
        GoPeopleQuote();
        break;

    default:
        default_view($warehouse,$delivery_id);
        break;
}

function GoPeopleCancel() {
    global $gopeople_host, $gopeople_token, $mysqli;
    date_default_timezone_set('Australia/Sydney');
    $timestamp = time() /* + ( (-1) * 60 * 60 ) */;
    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
    $order_id = (int) $_REQUEST['order_id'];

    $order_sql = $mysqli->query("SELECT `order_id` FROM `jos_vm_orders` WHERE `order_id`=" . $order_id . "");

    if ($order_sql->num_rows > 0) {


        $query = "select pin from jos_vm_orders_deliveries where order_id = '$order_id' limit 0,1";

        $result = $mysqli->query($query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysql_error());
        }

        $row = $result->fetch_assoc();

        if ($row['pin']) {
            $curl = curl_init();

            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: ' . $gopeople_token . ''
            );

            curl_setopt($curl, CURLOPT_URL, $gopeople_host . '/job?id=' . trim($row['pin']));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $gopeople_response = json_decode(curl_exec($curl));
            curl_close($curl);

            $status_code = 'A';

            $history_comment = 'GoPeople Delivery cancel.';
            $mysqli->query("INSERT INTO `jos_vm_order_history`
                (
                    `order_id`, 
                    `order_status_code`, 
                    `date_added`, 
                    `user_name`, 
                    `comments`
                )
                VALUES
                (
                    " . $order_id . ",
                    '" . $status_code . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");

            $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $status_code . "'
                WHERE `order_id`=" . $order_id . "
                ");

            $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_id'";
            $mysqli->query($query);





            echo 'Job has been cancelled.';
            ?>
            <script type="text/javascript">
                window.opener.jQuery(".delivery_icon_<?php echo $order_id;?>").addClass('default').attr('href','').attr('order_id',"<?php echo $order_id;?>").find('img').attr('src','/templates/bloomex7/images/deliveries/delivery_logo.png');
                window.opener.jQuery(".delivery_icon_span_<?php echo $order_id;?>").html('Updated')
            </script>
            <div style="display: none;">
                Headers: <pre><?php print_r($headers); ?></pre>
                Url: <pre><?php echo $gopeople_host . '/job?id=' . trim($row['pin']); ?></pre>
                Response: <pre><?php print_r($gopeople_response); ?></pre>
            </div>
            <?php
        }
    } else {
        
    }
}

function GoPeopleBook() {
    global $gopeople_host, $gopeople_token, $mysqli;

    $return = array();

    $order_id = (int) $_POST['order_id'];

    $order_sql = $mysqli->query("SELECT `order_id` FROM `jos_vm_orders` WHERE `order_id`=" . $order_id . "");

    if ($order_sql->num_rows > 0) {
        $order_obj = $order_sql->fetch_object();

        $gopeople_data = array();

        $gopeople_data['quoteId'] = $_POST['quote_id'];
        $gopeople_data['description'] = 'Flowers';
        $gopeople_data['note'] = 'Please do not fold';
        $gopeople_data['ref'] = $order_obj->order_id;

        $curl = curl_init();

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: ' . $gopeople_token . ''
        );

        curl_setopt($curl, CURLOPT_URL, $gopeople_host . '/book');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($gopeople_data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_response = curl_exec($curl);
        $gopeople_response = json_decode($curl_response);
        curl_close($curl);
        $return['raw'] = $gopeople_response;


        if ($gopeople_response->errorCode == '0') {
            include_once $_SERVER['DOCUMENT_ROOT'] . '/barcode_new/src/BarcodeGenerator.php';
            include_once $_SERVER['DOCUMENT_ROOT'] . '/barcode_new/src/BarcodeGeneratorPNG.php';

            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

            $return['barcodes'] = array();

            foreach ($gopeople_response->result->barcodes AS $key => $barcode) {
                $return['barcodes'][$key]['image'] = base64_encode($generator->getBarcode($barcode->text, $generator::TYPE_CODE_128));
                $return['barcodes'][$key]['text'] = $barcode->text;
            }

            $return['result'] = true;
            $return['tracking_code'] = $gopeople_response->result->trackingCode;
            //$return['barcodes'] = $gopeople_response->result->barcodes;
            $return['address_from'] = $gopeople_response->result->addressFrom;
            $return['address_to'] = $gopeople_response->result->addressTo;
            $return['zone'] = $gopeople_response->result->zone;
            $return['runner'] = (!is_array($gopeople_response->result->runner)) ? array() : $gopeople_response->result->runner;

            $return['headers'] = print_r(json_encode($headers), true);
            $return['url'] = $gopeople_host . '/book';
            $return['request'] = print_r(json_encode($gopeople_data), true);
            $return['response'] = print_r($gopeople_response, true);


            $history_comment = 'GoPeople Tracking number: ' . $gopeople_response->result->trackingCode . ' JobID: ' . $gopeople_response->result->jobId . '.';

            date_default_timezone_set('Australia/Sydney');
            $timestamp = time();
            $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

            $mysqli->query("INSERT INTO `jos_vm_order_history`
                (`order_id`, `order_status_code`, `date_added`, `user_name`, `comments`)
            VALUES
                (" . $order_id . ", 'C', '" . $mysqlDatetime . "', '" . $mysqli->real_escape_string($_SESSION['auth']['username']) . "', '" . $mysqli->real_escape_string($history_comment) . "')");

            $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='C'
                WHERE `order_id`=" . $order_id . "
                ");

            $query = "INSERT INTO `jos_vm_orders_deliveries`
                    (
                        `order_id`,
                        `delivery_type`,
                        `dateadd`,
                        `pin`,
                        `active`
                    ) 
                    VALUES (
                        " . $order_id . ",
                        " . $mysqli->real_escape_string($_REQUEST['delivery_id']) . ",
                        '" . $mysqlDatetime . "',
                        '" . $mysqli->real_escape_string($gopeople_response->result->jobId) . "',
                        '1'
                    )";
            $mysqli->query($query);

        } else {
            $return['result'] = false;
            $return['error'] = $gopeople_response->message;
        }
    } else {
        $return['result'] = false;
        $return['error'] = 'No order.';
    }

    echo json_encode($return);

    $order_sql->close();

    $mysqli->close();
}

function GoPeopleQuote() {
    global $gopeople_host, $gopeople_token, $mysqli;

    $warehouse_obj = new warehouses($_POST['warehouse']);
    $Warehouse = $warehouse_obj->warehouse;
    $return = array();

    $order_id = (int) $_POST['order_id'];

    $order_sql = $mysqli->query("SELECT `order_id` FROM `jos_vm_orders` WHERE `order_id`=" . $order_id . "");

    if ($order_sql->num_rows > 0) {
        $order_obj = $order_sql->fetch_object();

        $shipping_sql = $mysqli->query("SELECT `u`.*, `s`.`state_3_code` FROM `jos_vm_order_user_info` AS `u`  INNER JOIN `jos_vm_state` AS `s` ON `s`.`state_2_code`=`u`.`state` WHERE `u`.`order_id`=" . $order_obj->order_id . " AND `u`.`address_type`='ST'");

        if ($shipping_sql->num_rows > 0) {
            $shipping_obj = $shipping_sql->fetch_object();

            $gopeople_data = array();

            $gopeople_data['addressFrom'] = array();

            if($Warehouse){
                $gopeople_data['addressFrom']['unit'] = 'Unit 1';
                $gopeople_data['addressFrom']['address1'] = $Warehouse['StreetLines1'].' '. $Warehouse['StreetLines2'];
                $gopeople_data['addressFrom']['suburb'] = $Warehouse['City'];
                $gopeople_data['addressFrom']['state'] = $Warehouse['StateOrProvinceCode'];
                $gopeople_data['addressFrom']['postcode'] = $Warehouse['PostalCode'];
                $gopeople_data['addressFrom']['contactName'] = $Warehouse['PersonName'];
                $gopeople_data['addressFrom']['sendUpdateSMS'] = false;
                $gopeople_data['addressFrom']['isCommercial'] = true;
                $gopeople_data['addressFrom']['companyName'] = $Warehouse['CompanyName'];
            }

            $gopeople_data['addressTo'] = array();

            $gopeople_data['addressTo']['unit'] = $shipping_obj->suite;
            $gopeople_data['addressTo']['address1'] = $shipping_obj->street_name . ' ' . $shipping_obj->street_number;
            $gopeople_data['addressTo']['suburb'] = $shipping_obj->city;
            $gopeople_data['addressTo']['state'] = $shipping_obj->state_3_code;
            $gopeople_data['addressTo']['postcode'] = $shipping_obj->zip;
            $gopeople_data['addressTo']['contactName'] = $shipping_obj->first_name . ' ' . $shipping_obj->last_name;
            $gopeople_data['addressTo']['contactNumber'] = $shipping_obj->phone_1;
            $gopeople_data['addressTo']['sendUpdateSMS'] = false;
            $gopeople_data['addressTo']['contactEmail'] = $shipping_obj->user_email;
            $gopeople_data['addressTo']['isCommercial'] = false;
            $gopeople_data['addressTo']['companyName'] = $shipping_obj->company;

            $parcels = array();

            for ($i = 0; $i < sizeof($_POST['length']); $i++) {
                $parcels[$i]['type'] = 'flowers';
                $parcels[$i]['number'] = '1';
                $parcels[$i]['weight'] = $_POST['weight'][$i];
                $parcels[$i]['length'] = $_POST['length'][$i];
                $parcels[$i]['width'] = $_POST['width'][$i];
                $parcels[$i]['height'] = $_POST['height'][$i];
            }

            $gopeople_data['parcels'] = $parcels;

            date_default_timezone_set('Australia/Sydney');
            $gopeople_data['pickupAfter'] = gmdate("Y-m-d H:i:s", strtotime($_POST['pickupafter'])) . '+1000';
            $gopeople_data['dropOffBy'] = gmdate("Y-m-d H:i:s", strtotime($_POST['dropoffby'])) . '+1000';

            $gopeople_data['onDemand'] = true;
            $gopeople_data['setRun'] = true;


            $curl = curl_init();

            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: ' . $gopeople_token . ''
            );

            curl_setopt($curl, CURLOPT_URL, $gopeople_host . '/quote');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($gopeople_data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_response = curl_exec($curl);
            $gopeople_response = json_decode($curl_response);
            curl_close($curl);
            $return['raw'] = $gopeople_response;


if (!is_dir($_SERVER["DOCUMENT_ROOT"]."/auspost_request_response/".$order_obj->order_id)) {
    mkdir($_SERVER["DOCUMENT_ROOT"]."/auspost_request_response/".$order_obj->order_id, 0777);
}
$request_file = fopen($_SERVER["DOCUMENT_ROOT"]."/auspost_request_response/".$order_obj->order_id."/gopeople_request.json", "w") or die("Unable to open file!");
fwrite($request_file, json_encode($gopeople_data)."\n".json_encode($headers));
$response_file = fopen($_SERVER["DOCUMENT_ROOT"]."/auspost_request_response/".$order_obj->order_id."/gopeople_response.json", "w") or die("Unable to open file!");
fwrite($response_file, $curl_response);
fclose($response_file);
fclose($request_file);


            if ($gopeople_response->errorCode == '0') {
                //setRunPriceList shiftList
                $return['price_list'] = array();

                if (isset($gopeople_response->result->onDemandPriceList) AND sizeof($gopeople_response->result->onDemandPriceList) > 0) {
                    $return['result'] = true;

                    $return['price_list'][0]['title'] = 'On Demand Price list';
                    $return['price_list'][0]['prices'] = $gopeople_response->result->onDemandPriceList;

                    $return['order_id'] = $order_obj->order_id;
                } else {
                    $return['result'] = false;
                    $return['error'] = 'No pricelist.';
                }

                if (isset($gopeople_response->result->setRunPriceList) AND sizeof($gopeople_response->result->setRunPriceList) > 0) {
                    $return['price_list'][1]['title'] = 'Set run Price list';
                    $return['price_list'][1]['prices'] = $gopeople_response->result->setRunPriceList;
                }

                if (isset($gopeople_response->result->shiftList) AND sizeof($gopeople_response->result->shiftList) > 0) {
                    $return['price_list'][2]['title'] = 'Shift list';
                    $return['price_list'][2]['prices'] = $gopeople_response->result->shiftList;
                }
            } else {
                $return['result'] = false;
                $return['error'] = $gopeople_response->message;
            }
        } else {
            $return['result'] = false;
            $return['error'] = 'No shipping info.';
        }
    } else {
        $return['result'] = false;
        $return['error'] = 'No order.';
    }

    echo json_encode($return);

    $shipping_sql->close();
    $order_sql->close();

    $mysqli->close();
}

function default_view($warehouse,$delivery_id) {
    global $mysqli, $mosConfig_live_site;

    $order_id = (int) $_REQUEST['order_id'];
    $warehouses  = ['WH12','WH14','WH15','p01'];
    if (!in_array($warehouse,$warehouses)) {
        die('Sorry, you don\'t have access');
    }

    $order_sql = $mysqli->query("SELECT `order_id`, `ddate` FROM `jos_vm_orders` WHERE `order_id`=" . $order_id . "");

    if ($order_sql->num_rows > 0) {
        ?>
        <html>
            <head>
                <script type="text/javascript" src="/templates/bloomex7/bootstrap/js/jquery-1.12.4.min.js"></script>
                <script type="text/javascript" src="/templates/bloomex7/bootstrap/js/moment-with-locales.min.js"></script>
                <script type="text/javascript" src="/templates/bloomex7/bootstrap/js/bootstrap.min.js"></script>
                <script type="text/javascript" src="/templates/bloomex7/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
                <link rel="stylesheet" href="/templates/bloomex7/bootstrap/css/bootstrap.min.css" type="text/css">
                <link rel="stylesheet" href="/templates/bloomex7/bootstrap/css/bootstrap-theme.min.css" type="text/css">
                <link rel="stylesheet" href="/templates/bloomex7/bootstrap/css/bootstrap-datetimepicker.min.css" type="text/css">
                <link rel="stylesheet" type="text/css" media="print" href="/templates/bloomex7/bootstrap/css/bootstrap.min.css">
                <style>
                    .gopeople_label {
                        padding-left: 5px;
                        padding-top: 5px;
                        border: 1px solid black;
                        overflow: hidden;
                        margin-bottom: 10px;
                        width: 140mm;
                        height: 90mm;
                        box-sizing: border-box;
                    }
                    .gopeople_sticker_logo img {
                        max-width: 100%;
                    }
                    .gopeople_sticker {
                        margin-bottom: 20px;
                        padding: 0;
                    }
                    .gopeople_sticker img {
                        max-width: 100%;
                    }
                    .gopeople_stickers {
                        padding: 0px;
                        padding-right: 15px;
                        text-align: right;
                    }
                    .gopeople_sticker_logo {
                        padding: 0;
                        padding-right: 15px;
                        margin: 0;
                        text-align: right;
                        margin-top: -40px;
                    }
                    .gopeople_address_to {
                        height: 170px;
                        font-weight: bold;
                        font-size: 20px;
                        padding: 0px;
                        padding-top: 20px;
                    }
                    .gopeople_address_from {
                        padding: 0px;
                    }
                    .gopeople_barcode_text {
                        font-size: 16px;
                        letter-spacing: 9px;
                        font-weight: bold;
                    }
                    .gopeople_zone {
                        border: 1px solid;
                        top: 0px;
                        left: 0px;
                        font-weight: bold;
                        padding: 10px;
                        text-align: left;
                        font-size: 20px;
                    }
                    .gopeople_zone_name {
                        text-align: right;
                        float: right;
                    }
                    .gopeople_address_title 
                    {
                        padding: 0px;
                    }
                    @media print {
                        @page {
                            size: 140mm 100mm; 
                            margin: 0;
                            padding: 0;
                        }
                        @Page CompanyLetterHead {
                            display: none;
                        }
                        .gopeople_label {
                            page-break-after: always;
                            width: 100%;
                            height: 87mm;
                            margin-top: 10mm;
                            padding-left: 5px;
                            padding-top: 5px;
                        }
                    }
                </style>
                <script type="text/javascript">
                    var $j = jQuery.noConflict();
                    var i_package = 0;

                    function GoPeopleBook(object_id, order_id, warehouse,delivery_id)
                    {
                        $j('.gopeople_loader').show();

                        $j.ajax({
                            data:
                                    {
                                        task: 'GoPeopleBook',
                                        order_id: order_id,
                                        delivery_id: delivery_id,
                                        quote_id: object_id,
                                        warehouse: warehouse
                                    },
                            type: "POST",
                            dataType: 'json',
                            url: "",
                            success: function (data)
                            {
                                if (data.result == true)
                                {
                                    $j('#gopeople_box_2').hide();

                                    var address_from_html = '<div class="col-xs-2 col-md-2 gopeople_address_title">';
                                    address_from_html += 'From:';
                                    address_from_html += '</div>';
                                    address_from_html += '<div class="col-xs-10 col-md-10">';
                                    address_from_html += data.address_from.companyName;
                                    address_from_html += '<br/>';
                                    address_from_html += data.address_from.contactName;
                                    address_from_html += '<br/>';
                                    address_from_html += data.address_from.state + ' ' + data.address_from.suburb + ' ' + data.address_from.postcode;
                                    address_from_html += '<br/>';
                                    address_from_html += data.address_from.address1 + ' ' + data.address_from.unit;
                                    address_from_html += '<br/>';
                                    address_from_html += 'Phone: ' + data.address_from.contactNumber;
                                    address_from_html += '<br/>';
                                    address_from_html += '</div>';

                                    var address_to_html = '<div class="col-xs-3 col-md-3 gopeople_address_title">';
                                    address_to_html += 'To:';
                                    address_to_html += '</div>';
                                    address_to_html += '<div class="col-xs-9 col-md-9">';
                                    address_to_html += data.address_to.companyName;
                                    address_to_html += '<br/>';
                                    address_to_html += data.address_to.contactName;
                                    address_to_html += '<br/>';
                                    address_to_html += data.address_to.state + ' ' + data.address_to.suburb + ' ' + data.address_to.postcode;
                                    address_to_html += '<br/>';
                                    address_to_html += data.address_to.address1 + ' ' + data.address_to.unit;
                                    address_to_html += '<br/>';
                                    address_to_html += '</div>';

                                    var box_3 = '';

                                    console.log(Object.keys(data.barcodes).length + '/2');

                                    var barcode_iteration = Object.keys(data.barcodes).length / 2;

                                    for (it = 0; it < Object.keys(data.barcodes).length; it += 1)
                                    {
                                        console.log(barcode_iteration);
                                        if(data.runner.name){
                                            var runner_name =data.runner.name
                                        }else{
                                            var runner_name = '';
                                        };

                                        box_3 += '<div class="gopeople_label">';
                                        box_3 += '<div class="col-xs-6 col-md-6 gopeople_zone">';
                                        box_3 += data.zone;
                                        box_3 += '<span class="gopeople_zone_name">';
                                        box_3 += runner_name
                                        box_3 += '</span>';
                                        box_3 += '</div>';
                                        box_3 += '<div class="clearfix"></div>';
                                        box_3 += '<div class="col-xs-8 col-md-8 gopeople_address_to">';
                                        box_3 += address_to_html;
                                        box_3 += '</div>';
                                        box_3 += '<div class="col-xs-4 col-md-4 gopeople_sticker_logo">';
                                        box_3 += '<img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/gopeople_bloomex_logo.png">';
                                        box_3 += '<br/>';
                                        box_3 += '<img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/gopeople_logo.png">';
                                        box_3 += '</div>';
                                        box_3 += '<div class="col-xs-5 col-md-5 gopeople_address_from">';
                                        box_3 += address_from_html;
                                        box_3 += '</div>';
                                        box_3 += '<div class="col-xs-7 col-md-7 gopeople_stickers">';
                                        box_3 += '<div class="col-xs-12 col-md-12 gopeople_sticker">';
                                        box_3 += '<img src="data:image/png;base64,' + data.barcodes[it].image + '" />';
                                        box_3 += '<br/>';
                                        box_3 += '<span class="gopeople_barcode_text">' + data.barcodes[it].text + '</span>';
                                        box_3 += '</div>';
                                        box_3 += '</div>';
                                        box_3 += '</div>';
                                    }

                                    $j('#gopeople_box_3').html(box_3);

                                    var box_3_append = '<div style="display: none;">';
                                    box_3_append += 'Headers: <pre>' + data.headers + '</pre>';
                                    box_3_append += 'Url: <pre>' + data.url + '</pre>';
                                    box_3_append += 'Request: <pre>' + data.request + '</pre>';
                                    box_3_append += 'Response: <pre>' + data.response + '</pre>';
                                    box_3_append += '</div>';

                                    $j('#gopeople_box_3').append(box_3_append);

                                    setTimeout(function () {
                                        $j('#all_row').hide();
                                        $j('.gopeople_loader').hide();
                                        $j('#gopeople_box_3').show();
                                        window.print();
                                    }, 3000);
                                } else
                                {
                                    $j('.gopeople_loader').hide();
                                    alert(data.error);
                                }
                            }
                        });
                    }

                    function BackToFirst() {
                        $j('.gopeople_loader').show();
                        $j('#gopeople_box_1').show();
                        $j('#gopeople_box_2').html('').hide();
                        $j('.gopeople_loader').hide();
                        $j('#sendfinish').show();
                    }

                    function AddPackage()
                    {
                        i_package++;

                        var html_package = '<div class="form-horizontal" id="package_' + i_package + '">';
                        html_package += '<div class="form-group">';
                        html_package += '<label for="length_' + i_package + '" class="col-sm-2 control-label">Length</label>';
                        html_package += '<div class="col-sm-10">';
                        html_package += '<input type="number" class="form-control" name="length_' + i_package + '" id="length_' + i_package + '" min="1" value="67">';
                        html_package += '</div>';
                        html_package += '</div>';
                        html_package += '<div class="form-group">';
                        html_package += '<label for="width_' + i_package + '" class="col-sm-2 control-label">Width</label>';
                        html_package += '<div class="col-sm-10">';
                        html_package += '<input type="number" class="form-control" name="width_' + i_package + '" id="width_' + i_package + '" min="1" value="15">';
                        html_package += '</div>';
                        html_package += '</div>';
                        html_package += '<div class="form-group">';
                        html_package += '<label for="height_' + i_package + '" class="col-sm-2 control-label">Height</label>';
                        html_package += '<div class="col-sm-10">';
                        html_package += '<input type="number" class="form-control" name="height_' + i_package + '" id="height_' + i_package + '" min="1" value="20">';
                        html_package += '</div>';
                        html_package += '</div>';
                        html_package += '<div class="form-group">';
                        html_package += '<label for="weight_' + i_package + '" class="col-sm-2 control-label">Weight</label>';
                        html_package += '<div class="col-sm-10">';
                        html_package += '<input type="number" class="form-control" name="weight_' + i_package + '" id="weight_' + i_package + '" min="1" value="1">';
                        html_package += '</div>';
                        html_package += '</div>';
                        html_package += '</div>';

                        $j('#packages').append(html_package)
                    }

                    function DeletePackage()
                    {
                        $j('#package_' + i_package).remove();
                        i_package--;
                    }

                    function FinishOrder()
                    {
                        var weight_a = new Array();
                        var length_a = new Array();
                        var width_a = new Array();
                        var height_a = new Array();

                        bad_package = 0;

                        for (i = 0; i <= i_package; i++)
                        {
                            $j("input[name^=length_" + i + "]").parent().parent().removeClass('has-error');
                            $j("input[name^=width_" + i + "]").parent().parent().removeClass('has-error');
                            $j("input[name^=height_" + i + "]").parent().parent().removeClass('has-error');
                            $j("input[name^=weight_" + i + "]").parent().parent().removeClass('has-error');

                            weight_v = $j("input[name^=weight_" + i + "]").val();
                            length_v = $j("input[name^=length_" + i + "]").val();
                            width_v = $j("input[name^=width_" + i + "]").val();
                            height_v = $j("input[name^=height_" + i + "]").val();

                            if (length_v == '' || !length_v.match(/^\d+$/))
                            {
                                $j("input[name^=length_" + i + "]").parent().parent().addClass('has-error');

                                bad_package = 1;
                            } else
                            {
                                length_a.push($j("input[name^=length_" + i + "]").val());
                            }
                            if (width_v == '' || !width_v.match(/^\d+$/))
                            {
                                $j("input[name^=width_" + i + "]").parent().parent().addClass('has-error');
                                bad_package = 1;
                            } else
                            {
                                width_a.push($j("input[name^=width_" + i + "]").val());
                            }
                            if (height_v == '' || !height_v.match(/^\d+$/))
                            {
                                $j("input[name^=height_" + i + "]").parent().parent().addClass('has-error');
                                bad_package = 1;
                            } else
                            {
                                height_a.push($j("input[name^=height_" + i + "]").val());
                            }
                            if (weight_v == '' || !weight_v.match(/^\d+$/))
                            {
                                $j("input[name^=weight_" + i + "]").parent().parent().addClass('has-error');
                                bad_package = 1;
                            } else
                            {
                                weight_a.push($j("input[name^=weight_" + i + "]").val());
                            }
                        }

                        var order_id = $j("#sendfinish").attr("order_id");
                        var warehouse = $j("#sendfinish").attr("wh");
                        var delivery_id = $j("#sendfinish").attr("delivery_id");

                        if (bad_package == 0)
                        {
                            $j('.gopeople_loader').show();

                            $j.ajax({
                                data:
                                        {
                                            task: 'GoPeopleQuote',
                                            order_id: order_id,
                                            warehouse: warehouse,
                                            weight: weight_a,
                                            length: length_a,
                                            width: width_a,
                                            height: height_a,
                                            pickupafter: $j('#pickUpAfter').val(),
                                            dropoffby: $j('#dropOffBy').val()
                                        },
                                warehouse: warehouse,
                                type: "POST",
                                dataType: 'json',
                                url: "",
                                success: function (data)
                                {
                                    if (data.result == true)
                                    {
                                        var gopeople_table = '<table class="table table-bordered table-hover" id="gopeople_table">';
                                        gopeople_table += '<thead>';
                                        gopeople_table += '<tr>';
                                        gopeople_table += '<th>';
                                        gopeople_table += 'Service Name';
                                        gopeople_table += '</th>';
                                        gopeople_table += '<th>';
                                        gopeople_table += 'Amount';
                                        gopeople_table += '</th>';
                                        gopeople_table += '<th>';
                                        gopeople_table += 'Pickup After';
                                        gopeople_table += '</th>';
                                        gopeople_table += '<th>';
                                        gopeople_table += 'Drop Off By';
                                        gopeople_table += '</th>';
                                        gopeople_table += '</tr>';
                                        gopeople_table += '</thead>';

                                        $j.each(data.price_list, function (k, v) {
                                            gopeople_table += '<th class="gopeople_th_pricelist" colspan="4">';
                                            gopeople_table += v.title;
                                            gopeople_table += '</th>';

                                            $j.each(v.prices, function (k1, v1) {
                                                gopeople_table += '<tr onclick="GoPeopleBook(\'' + v1.objectId + '\', ' + data.order_id + ', \'' + warehouse + '\',\''+delivery_id+'\');">';
                                                gopeople_table += '<td>';
                                                gopeople_table += v1.serviceName;
                                                gopeople_table += '</td>';
                                                gopeople_table += '<td>';
                                                gopeople_table += v1.amount + ' ' + v1.currency;
                                                gopeople_table += '</td>';
                                                gopeople_table += '<td>';
                                                gopeople_table += v1.pickupAfter;
                                                gopeople_table += '</td>';
                                                gopeople_table += '<td>';
                                                gopeople_table += v1.dropOffBy;
                                                gopeople_table += '</td>';
                                                gopeople_table += '</tr>';
                                            });

                                        });

                                        gopeople_table += '</table>';

                                        $j('#gopeople_box_1').hide();
                                        $j('#gopeople_box_2').html('<div class="col-xs-6 col-xs-offset-3"><input type="button" onclick="BackToFirst();" value="Back" class="btn btn-warning btn-block"></div>' + gopeople_table);
                                        $j('.gopeople_loader').hide();
                                        $j('#gopeople_box_2').show();
                                        $j('#sendfinish').hide();
                                    } else
                                    {
                                        $j('.gopeople_loader').hide();
                                        alert(data.error);
                                    }
                                }
                            });
                        }
                    }
                </script>
                <style>
                    .row {
                        margin: 0px; 
                    }
                    .package {
                        background-color: #CFE9F9;
                        margin-bottom: 10px;
                    }

                    .input_error {
                        border: 1px solid red;
                    }
                    #gopeople_table td {
                        cursor: pointer;
                    }
                    .form-horizontal {
                        padding: 10px;
                        border: 1px solid;
                        margin-bottom: 10px;
                        border: 1px solid #00B4D9;
                    }
                    .gopeople_logo {
                        background-color: #00B4D9;
                        margin-bottom: 10px;
                    }
                    .gopeople_loader {
                        position: fixed;
                        left: 0px;
                        top: 0px;
                        width: 100%;
                        height: 100%;
                        z-index: 9999;
                        background: url(/images/Preloader_8.gif) center no-repeat #fff;
                        display: none;
                    }
                    .gopeople_th_pricelist {
                        background-color: #00b4d9;
                        color: #fff;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <?php
                $order_obj = $order_sql->fetch_object();
                ?>
                <div class="row" id="all_row">
                    <div class="gopeople_loader"></div>
                    <div class="col-xs-6 col-xs-offset-3">
                        <div class="col-xs-12">
                            <div class="gopeople_logo">
                                <img src="/templates/bloomex7/images/gopeolpe.png" alt="gopeople" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div id="gopeople_box_1">
                            <div class="col-xs-6 col-xs-offset-3">
                                Order Id: <?php echo $order_obj->order_id; ?><br/>
                                Delivery date: <?php echo $order_obj->ddate; ?><br/>
                                <div class="form-horizontal" >
                                    <div class="form-group">
                                        <label for="pickUpAfter" class="col-sm-4 control-label">Pickup after</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="pickUpAfter" id="pickUpAfter">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="dropOffBy" class="col-sm-4 control-label">Drop off by</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="dropOffBy" id="dropOffBy">
                                        </div>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    $j(function () {
                                        $j('#pickUpAfter').datetimepicker({
                                            language: 'en',
                                            sideBySide: true,
                                            minuteStepping: 10,
                                            defaultDate: "<?php echo $order_obj->ddate; ?> 11:00",
                                            format: 'DD-MM-YYYY HH:mm'
                                        });
                                        $j('#dropOffBy').datetimepicker({
                                            language: 'en',
                                            sideBySide: true,
                                            minuteStepping: 10,
                                            defaultDate: "<?php echo $order_obj->ddate; ?> 20:00",
                                            format: 'DD-MM-YYYY HH:mm'
                                        });
                                    });
                                </script>
                            </div>
                            <div id="packages" class="col-xs-6 col-xs-offset-3">
                                <div class="form-horizontal" id="package_0">
                                    <div class="form-group">
                                        <label for="length_0" class="col-sm-2 control-label">Length</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name="length_0" id="length_0" min="1" value="67">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="width_0" class="col-sm-2 control-label">Width</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name="width_0" id="width_0" min="1" value="15">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="height_0" class="col-sm-2 control-label">Height</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name="height_0" id="height_0" min="1" value="20">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight_0" class="col-sm-2 control-label">Weight</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name="weight_0" id="weight_0" min="1" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="one_package" style="display: none;">

                            </div>
                            <div class="col-xs-6 col-xs-offset-3">
                                <p>
                                    <input type="button" onclick="AddPackage();" value="Add new" class="btn btn-info btn-block">
                                    <input type="button" onclick="DeletePackage();" value="Delete last" class="btn btn-danger btn-block">
                                </p>
                            </div>
                        </div>
                        <div id="gopeople_box_2" class="col-xs-12" style="display: none;"></div>

                        <form class="col-xs-6 col-xs-offset-3">
                            <input onclick="FinishOrder();" id="sendfinish" wh="<?php echo htmlspecialchars($warehouse); ?>" order_id="<?php echo $order_obj->order_id; ?>" delivery_id="<?php echo $delivery_id; ?>" class="btn btn-success btn-block" type="button" value="Proceed to pricing"/>
                        </form>
                    </div>
                </div>
                <div id="gopeople_box_3" class="" style="width: 100%; display: none;"></div>
            </body>
        </html>   
        <?php
    }
    $order_sql->close();

    $mysqli->close();
}
