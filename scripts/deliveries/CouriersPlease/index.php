<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
require_once('../warehouses.php');
session_name(md5($mosConfig_live_site));
session_start();
if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'dev.' || substr($_SERVER['HTTP_HOST'], 0, 5) == 'stage') {
    $serviceUrl = 'https://api-test.couriersplease.com.au';
} else {
    $serviceUrl = 'https://api.couriersplease.com.au';
}


$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$orderId = ($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';

function getsender($warehouse)
{

    $warehouse_obj = new warehouses($warehouse);
    $Warehouse = $warehouse_obj->warehouse;

    switch ($warehouse) {
//perth - our one
        case 'p01':
            $Warehouse['auth'] = base64_encode('113160501:B5339D31D5D65DED77931564DC53447CCCA24A841B16E09E3433054A427D0EAB');
            break;
//brisbane
        case 'WH14':
            $Warehouse['auth'] = base64_encode('113160477:CFEE21071E88C4C99D8BAB1EA9DF7DCC8C51D68BD247B5B73290F181D1E88B65');
            break;
//melbourne
        case 'WH15':
            $Warehouse['auth'] = base64_encode('113160493:13CE625668490B0499408D3EDFC32391653ED8647DA6E5BEF1DB2408237A1604');
            break;
//adelaide
        case 'WH16':
            $Warehouse['auth'] = base64_encode('113160485:4CF939122BFF0F0C75AA6210E567319CC15B74AAAFBEC12595AABF52ED4F9305');
            break;

        default:
            $Warehouse['auth'] = base64_encode('113159487:18C7E516443328DCCB66FCB8A13D45025A0A40415E7EF424019BC266012C3DFD');
            break;
    }
    if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'dev.' || substr($_SERVER['HTTP_HOST'], 0, 5) == 'stage') {
        $Warehouse['auth'] = base64_encode('111222626:1ADEEB9C4A3D25E3479A0EC087F8D005DA7352292056B3B353E5F488DFE41036');
    }
    if (!isset($Warehouse['auth'])) {
        die('invalid credentials');
    }
    return $Warehouse;
}

if (!$orderId)
    die('order id can not be empty');

function getOrderDetails($orderId)
{
    global $mysqli;
    $order_sql = $mysqli->query("SELECT 
            `o`.`order_id`, 
            `o`.`ddate`, 
            `o`.`customer_comments`, 
            `uis`.`user_email` as shipping_user_email,
            `uis`.`company` as shipping_company,
            `uis`.`first_name` as shipping_first_name,
            `uis`.`last_name` as shipping_last_name,
            `uis`.`street_number` as shipping_street_number,
            `uis`.`street_name` as shipping_street_name,
            `uis`.`zip` as shipping_zip,
            `uis`.`suite` as shipping_suite,
            `uis`.`state` as shipping_state,
            `uis`.`city` as shipping_city,
            `uis`.`country` as shipping_country,
            `uis`.`phone_1` as shipping_phone_1,
            `uis`.`address_1` as shipping_address_1,
            `uis`.`address_2` as shipping_address_2
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `uis` ON `uis`.`order_id`=`o`.`order_id` AND `uis`.`address_type`='ST'
        WHERE `o`.`order_id`=" . $orderId . "");
    if ($order_sql->num_rows > 0) {
        return $order_sql->fetch_object();
    }

    die('order id is woring');

}

$orderSql = $mysqli->query("SELECT 
  o.warehouse
FROM 
  jos_vm_orders as o 
WHERE 
  o.order_id ='$orderId'");
if ($orderSql->num_rows > 0) {
    $order = $orderSql->fetch_object();
    $warehouse = $order->warehouse;
} else {
    die('order id is empty');
}


?>
<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap.min.css">

<script src="/templates/bloomex_adaptive/js/jquery-3.7.1.min.js"></script>
<script src="/templates/bloomex_adaptive/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="/scripts/deliveries/CouriersPlease/main.css">
<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap-material-datetimepicker.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
      integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<style>
    @font-face {
        font-family: 'Material Icons';
        font-style: normal;
        font-weight: 400;
        src: url(https://fonts.gstatic.com/s/materialicons/v55/flUhRq6tzZclQEJ-Vdg-IuiaDsNc.woff2) format('woff2');
    }
</style>

<?php


function curlCouriersPleaseGetRequest($fnc, $auth)
{
    global $serviceUrl;

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . $auth . ''
    );

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $serviceUrl . $fnc);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($connection); // Execute REST Request

    echo '<div style="display: none">Request: <pre>';
    echo $fnc;
    echo '</pre><br>Response: <pre>';
    echo $response;
    echo '</pre></div>';
    if (curl_errno($connection)) {
        echo 'Curl error: ' . curl_error($connection) . "\n";
        die;
    }
    if (!$response) {
        echo 'Failed loading ' . "\n";
        echo $response . "\n";
        die;
    } else {

        $responseData = json_decode($response);
        if ($responseData && isset($responseData->data->errors) && is_array($responseData->data->errors)) {
            foreach ($responseData->data->errors as $q) {
                echo $q->description . "<br>";
            }
            die;
        }

    }
    curl_close($connection);
    return $responseData;

}


function curlCouriersPlease($data, $auth, $fnc = '')
{
    global $serviceUrl;
    $data_string = json_encode($data);

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Content-length:' . strlen($data_string),
        'Authorization: Basic ' . $auth . ''
    );

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $serviceUrl . $fnc);
    curl_setopt($connection, CURLOPT_POST, true);
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($connection); // Execute REST Request

    echo '<div style="display: none">Request: <pre>';
    echo $data_string;
    echo '</pre><br>Response: <pre>';
    echo $response;
    echo '</pre></div>';

    if (curl_errno($connection)) {
        echo 'Curl error: ' . curl_error($connection) . "\n";
        die;
    }
    curl_close($connection);
    if (!$response) {
        echo 'Failed loading ' . "\n";
        echo $response . "\n";
        die;
    } else {
        $responseData = json_decode($response);
        if ($responseData && isset($responseData->data->errors) && is_array($responseData->data->errors)) {
            foreach ($responseData->data->errors as $q) {
                echo $q->description . "<br>";
            }
            die;
        }
    }

    return $responseData;
}

Switch ($task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'cancel':

        $order_sql = $mysqli->query("SELECT 
            `o`.`order_id`, 
            `o`.`warehouse`, 
            `ui`.`country`,
            od.shipment_id
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        INNER JOIN `jos_vm_orders_deliveries` AS `od` ON `od`.`order_id`=`o`.`order_id`
        WHERE `o`.`order_id`=" . $orderId);

        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $status_code = 'C';

            $warehouseDetails = getsender($order_obj->warehouse);

            $fields = [
                "consignmentCode" => html_entity_decode($order_obj->shipment_id),
            ];

            $result = curlCouriersPlease($fields, $warehouseDetails['auth'], '/v1/domestic/shipment/cancel');

            if (isset($result->responseCode) && $result->responseCode == 'Success') {

                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $status_code . "'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_obj->order_id'";
                $mysqli->query($query);

                $history_comment = 'CouriersPlease Delivery Cancel.';

                date_default_timezone_set('Australia/Sydney');
                $mysqlDatetime = date("Y-m-d G:i:s", time());

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
                    " . $order_obj->order_id . ",
                    '" . $status_code . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");
                ?>
                <script type="text/javascript">
                    window.opener.jQuery(".delivery_icon_<?php echo $order_obj->order_id; ?>").addClass('default').attr('href', '').attr('order_id', "<?php echo $order_obj->order_id; ?>").find('img').attr('src', '/templates/bloomex7/images/deliveries/delivery_logo.png');
                    window.opener.jQuery(".delivery_icon_span_<?php echo $order_obj->order_id; ?>").html('Updated')
                </script>
                <?php
                echo "CouriersPlease Delivery Canceled";
            }

        } else {
            echo "Order $orderId has not CouriersPlease delivery for cancelling";
        }
        die;
    case 'quote':
        $warehouseDetails = getsender($warehouse);
        $order_obj = getOrderDetails($orderId);
        $fields = [
            "fromSuburb" => html_entity_decode($warehouseDetails['City']),
            "fromPostcode" => html_entity_decode($warehouseDetails['PostalCode']),
            "toSuburb" => html_entity_decode($order_obj->shipping_city),
            "toPostcode" => html_entity_decode($order_obj->shipping_zip),
        ];
        $count = count($_REQUEST['Reference']);
        for ($i = 0; $i != $count; $i++) {
            $fields['items'][] = [
                "quantity" => $_REQUEST['Count'][$i],
                "length" => $_REQUEST['Length'][$i],
                "width" => $_REQUEST['Width'][$i],
                "height" => $_REQUEST['Height'][$i],
                "physicalWeight" => $_REQUEST['Weight'][$i],
            ];
        }

        $result = curlCouriersPlease($fields, $warehouseDetails['auth'], '/v2/domestic/quote');

        if (isset($result->responseCode) && $result->responseCode == 'SUCCESS' && isset($result->data)) {
            $rateCardCode = '<select name="rateCardCode">';
            foreach ($result->data as $res) {
                $rateCardCode .= '<option value="' . $res->RateCardCode . '">' . $res->RateCardDescription . ' ($' . $res->CalculatedFreightCharge . ')</option>';
            }
            $rateCardCode .= '</select>';
            ?>
            <form id="myForm" class="form" method="POST">
                <div id="parentId">
                    <fieldset id="fs1">
                        <?php echo $rateCardCode ?>
                    </fieldset>
                </div>
                <input class="submit" type="submit" value="Send To CouriersPlease"/>
                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>"/>
                <input type="hidden" name="sender" value="<?php echo $_REQUEST['sender']; ?>"/>
                <input type="hidden" name="request" value='<?php echo serialize($_REQUEST); ?>'/>
                <input type="hidden" name="task" value="create">
            </form>
            <?php

        }

        die;
    case 'create':
        $order_obj = getOrderDetails($orderId);
        $warehouseDetails = getsender($warehouse);
        $streetlinesBilling = '';
        $streetlinesBilling .= html_entity_decode($warehouseDetails['StreetNumber']) . ' ';
        $streetlinesBilling .= html_entity_decode($warehouseDetails['StreetName']);
        $streetlinesShipping = '';
        if ($order_obj->shipping_suite) {
            $streetlinesShipping = $order_obj->shipping_suite . '-';
        }
        if ($order_obj->shipping_street_number) {
            $streetlinesShipping .= html_entity_decode($order_obj->shipping_street_number) . ' ';
        }
        if ($order_obj->shipping_street_name) {
            $streetlinesShipping .= html_entity_decode($order_obj->shipping_street_name);
        }
        $streetlinesShipping = ($streetlinesShipping) ? $streetlinesShipping : html_entity_decode($order_obj->shipping_address_1);
        $ddate = date('Y-m-d', strtotime($order_obj->ddate));

        $request = unserialize($_REQUEST['request']);
        $fields = [
            "pickupFirstName" => html_entity_decode($warehouseDetails['WarehouseName']),
            "pickupLastName" => html_entity_decode($warehouseDetails['PersonName']),
            "pickupCompanyName" => html_entity_decode($warehouseDetails['CompanyName']),
            "pickupEmail" => html_entity_decode($warehouseDetails['WarehouseEmail']),
            "pickupAddress1" => html_entity_decode($streetlinesBilling),
            "pickupSuburb" => html_entity_decode($warehouseDetails['City']),
            "pickupState" => html_entity_decode($warehouseDetails['StateOrProvinceCode']),
            "pickupPostcode" => html_entity_decode($warehouseDetails['PostalCode']),
            "pickupPhone" => html_entity_decode($warehouseDetails['PhoneNumber']),
            "pickupIsBusiness" => 'true',
            "destinationFirstName" => html_entity_decode($order_obj->shipping_first_name),
            "destinationLastName" => html_entity_decode($order_obj->shipping_last_name),
            "destinationCompanyName" => html_entity_decode($order_obj->shipping_company),
            "destinationEmail" => "courierpleasemail@bloomex.ca",
            "destinationAddress1" => html_entity_decode($streetlinesShipping),
            "destinationSuburb" => html_entity_decode($order_obj->shipping_city),
            "destinationState" => html_entity_decode($order_obj->shipping_state),
            "destinationPostcode" => html_entity_decode($order_obj->shipping_zip),
            "destinationPhone" => html_entity_decode($order_obj->shipping_phone_1),
            "destinationIsBusiness" => 'false',
            "contactFirstName" => html_entity_decode($order_obj->shipping_first_name),
            "contactLastName" => html_entity_decode($order_obj->shipping_last_name),
            "contactCompanyName" => html_entity_decode($order_obj->shipping_company),
            "contactEmail" => "courierpleasemail@bloomex.ca",
            "contactAddress1" => html_entity_decode($streetlinesShipping),
            "contactSuburb" => html_entity_decode($order_obj->shipping_city),
            "contactState" => html_entity_decode($order_obj->shipping_state),
            "contactPostcode" => html_entity_decode($order_obj->shipping_zip),
            "contactPhone" => html_entity_decode($order_obj->shipping_phone_1),
            "contactIsBusiness" => 'false',
            "referenceNumber" => html_entity_decode($request['Reference'][0]),
            "termsAccepted" => 'true',
            "dangerousGoods" => 'false',
            "specialInstruction" => html_entity_decode($order_obj->customer_comments),
            "rateCardId" => $_REQUEST['rateCardCode'],
        ];
        $count = count($request['Reference']);
        for ($i = 0; $i != $count; $i++) {
            $fields['items'][] = [
                "quantity" => $request['Count'][$i],
                "length" => $request['Length'][$i],
                "width" => $request['Width'][$i],
                "height" => $request['Height'][$i],
                "physicalWeight" => $request['Weight'][$i],
            ];
        }

        $result = curlCouriersPlease($fields, $warehouseDetails['auth'], '/v2/domestic/shipment/create');

        if (isset($result->responseCode) && $result->responseCode == 'SUCCESS' && isset($result->data)) {
            $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='C'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

            $history_comment = 'CouriersPlease consignmentCode: ' . $result->data->consignmentCode ?? '';


            date_default_timezone_set('Australia/Sydney');
            $mysqlDatetime = date("Y-m-d G:i:s", time());


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
                    " . $order_obj->order_id . ",
                    'C', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");


            $query = "INSERT INTO `jos_vm_orders_deliveries`
                    (
                        `order_id`,
                        `delivery_type`,
                        `dateadd`,
                        `pin`,
                        `shipment_id`,
                        `active`
                    ) 
                    VALUES (
                        " . $order_obj->order_id . ",
                        " . $mysqli->real_escape_string($_REQUEST['delivery_id']) . ",
                        '" . $mysqlDatetime . "',
                        '" . $mysqli->real_escape_string($result->data->consignmentCode ?? '') . "',
                        '" . $mysqli->real_escape_string($result->data->consignmentCode ?? '') . "',
                        '1'
                    )";
            $mysqli->query($query);

            $result = curlCouriersPleaseGetRequest('/v1/domestic/shipment/label?consignmentNumber=' . $result->data->consignmentCode ?? '', $warehouseDetails['auth']);
            if (isset($result->responseCode) && $result->responseCode == 'SUCCESS' && isset($result->data->label)) {
                ?>
                <embed
                        type="application/pdf"
                        src="data:application/pdf;base64,<?php echo $result->data->label; ?>"
                        id="pdfDocument"
                        width="100%"
                        height="100%"/>
                <script type="text/javascript">
                    window.opener.jQuery(".delivery_icon_<?php echo $orderId; ?>").removeClass('default').attr('href', '').attr('order_id', "").find('img').attr('src', '/templates/bloomex7/images/deliveries/CouriersPlease_logo.png');
                    window.opener.jQuery(".delivery_icon_span_<?php echo $orderId; ?>").html('Updated');
                </script>
                <?php
            }

            die;


        } else {
            ?>
            <?php print_r($result); ?>
            <hr>
            <?php echo json_encode($fields); ?>
            <?php
        }

        die;
    default:
        ?>

        <form id="myForm" class="form" method="POST">
            <div id="parentId">
                <fieldset id="fs1">
                    <legend>Packages</legend>
                    <fieldset id="fs11">
                        <div id="pi1">
                            <label for="reference">Reference Number:</label>
                            <input class="width"
                                   type="text" size="40"
                                   value="bloomex_<?php echo $orderId; ?>"
                                   name="Reference[0]"
                                   id="reference_0"
                                   maxlength="32"/>
                        </div>
                        <div id="pi4">
                            <label for="weight">Piece Weight:</label>
                            <input type="number" value="3" name="Weight[0]" id="weight_0"/> kg.
                        </div>
                        <div id="pi4">
                            <label for="length">Piece Length:</label>
                            <input type="number" value="50" name="Length[0]" id="length_0"/> cm.
                        </div>
                        <div id="pi4">
                            <label for="height">Piece Height:</label>
                            <input type="number" value="15" name="Height[0]" id="height_0"/> cm.
                        </div>
                        <div id="pi4">
                            <label for="width">Piece Width:</label>
                            <input type="number" value="20" name="Width[0]" id="width_0"/> cm.
                        </div>
                        <div id="pi5">
                            <label for="count">Total Pieces:</label>
                            <input type="number" value="1" name="Count[0]" id="count_0"/> pcs.
                        </div>

                    </fieldset>
                    <input type="button" value="+" onClick="AddItem();" ID="add">
                    <input type="button" value="-" onClick="DelItem();" ID="del">
                </fieldset>
            </div>
            <input class="submit" type="submit" value="Get Quote From CouriersPlease"/>
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>"/>
            <input type="hidden" name="sender" value="<?php echo $_REQUEST['sender']; ?>"/>
            <input type="hidden" name="task" value="quote">
        </form>

        <script>
            var count_div = 1;
            var count_fs = 1;

            function AddItem() {
                var firstform = document.getElementById('fs1');
                var button = document.getElementById('add');
                count_fs++;
                var newitem = '<div id="pi' + count_div + '"><label for="reference' + count_div + '">Piece Reference:</label><input class="width" type="text" value="bloomex_' + document.getElementsByName('order_id')[0].value + '" name="Reference[' + count_div + ']" id="reference_' + count_div + '" maxlength="32"/></div>';
                newitem += '<div id="pi' + count_div + '"><label for="weight' + count_div + '">Piece Weight:</label><input type="number" value="3" name="Weight[' + count_div + ']" id="weight_' + count_div + '"/> kg.</div>';
                newitem += '<div id="pi' + count_div + '"><label for="length' + count_div + '">Piece Length:</label><input type="number" value="50" name="Length[' + count_div + ']" id="length_' + count_div + '"/> cm.</div>';
                newitem += '<div id="pi' + count_div + '"><label for="height' + count_div + '">Piece Height:</label><input type="number" value="15" name="Height[' + count_div + ']" id="height_' + count_div + '"/> cm.</div>';
                newitem += '<div id="pi' + count_div + '"><label for="width' + count_div + '">Piece Width:</label><input type="number" value="20" name="Width[' + count_div + ']" id="width_' + count_div + '"/> cm.</div>';
                newitem += '<div id="pi' + count_div + '"><label for="count' + count_div + '">Total Pieces:</label><input type="number" value="1" name="Count[' + count_div + ']" id="count_' + count_div + '"/> pcs.</div><br>';
                count_div++;

                var newnode = document.createElement('fieldset');

                newnode.id = 'fs1' + count_fs;
                newnode.innerHTML = newitem;
                firstform.insertBefore(newnode, button);
            }

            function DelItem() {
                if (count_fs > 1) {
                    var firstform = document.getElementById('fs1');
                    var last = document.getElementById('fs1' + count_fs);
                    firstform.removeChild(last);
                    count_fs--;
                    count_div = count_div - 3;
                }
            }
        </script>
        <?php
        break;
} ?>

