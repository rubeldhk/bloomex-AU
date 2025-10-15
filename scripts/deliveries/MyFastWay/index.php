<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

session_name(md5($mosConfig_live_site));
session_start();

$serviceUrl = 'https://api.myfastway.com.au';
$tokenUrl = 'https://identity.fastway.org/connect/token';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$orderId = ($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';

$scopes = [
    "AUS"=>"fw-fl2-api-au",
    "NZL"=>"fw-fl2-api-nz"
];
$countryValidaValues = [
    "AUS"=>"AU",
    "NZL"=>"NZ",
    "IRL"=>"IE",
    "ZAF"=>"ZA"
];

if (!$orderId)
    die('order id can not be empty');
$orderSql = $mysqli->query("SELECT 
  o.warehouse, 
  `i`.`country`,
  `i`.`city`,
  `i`.`zip`
FROM 
  jos_vm_orders as o 
  left join jos_vm_order_user_info as i on i.order_id = o.order_id and `i`.`address_type` = 'ST' 
WHERE 
  o.order_id ='$orderId'");
if ($orderSql->num_rows > 0) {
    $order = $orderSql->fetch_object();
    $warehouse = $order->warehouse;
    $country = $order->country;
} else {
    die('order id is empty');
}


Switch ($warehouse) {
//perthgit status
    case 'p01':
        $apiUserId = "fw-fl2-PER1230126-8ae775dcdcc1";
        $apiKey = "d655fbb0-b3b4-4777-a8e7-a589f32da3c6";
        break;
//sydney
    case 'WH12':
        $apiUserId = "fw-fl2-SYD1250207-316a77401ac7";
        $apiKey = "c4ead439-f99b-4b05-a6fb-cb0ccff1284b";
        break;
//brisbane
    case 'WH14':
        $apiUserId = "fw-fl2-BRI1090078-77ebf25e4498";
        $apiKey = "59951d9b-6d8a-4c00-b972-0b9b4c1b098e";
        break;
//melbourne
    case 'WH15':
        $apiUserId = "fw-fl2-MEL0230217-68f01ce0c04b";
        $apiKey = "c0b14a6c-618e-499b-9272-d6a93423e749";
        break;
//adelaide
    case 'WH16':
        $apiUserId = "fw-fl2-ADL0080174-39bbb9640a7d";
        $apiKey = "a648669d-7772-487a-8ec1-1595c0bb7ee4";
        break;
//New Zeland
    case 'bcz':
        $apiUserId = "fw-fl2-AUK0220526-c0d667ad9d1c";
        $apiKey = "78ca959c-7cc4-4755-9b42-37e6983ef8df";
        $serviceUrl = 'https://api.myfastway.co.nz';
        break;

}
if (!isset($apiKey)){
    die('invalid warehouse');
}

function curlMyFastWayGetToken($scopes,$country,$apiKey,$apiUserId)
{
    global $tokenUrl;


    $tokenData = [
        "client_id"=>$apiUserId,
        "client_secret"=>$apiKey,
        "scope"=>$scopes[$country]??$scopes['AUS'],
        "grant_type"=>"client_credentials",
    ];
    $data = http_build_query($tokenData);

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $tokenUrl);
    curl_setopt($connection, CURLOPT_POST, true);
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($connection);
    $responseData = json_decode($response);

    echo '<div style="display: none">Request Bearer: <pre>';
    echo $data;
    echo '</pre><br>Response Bearer: <pre>';
    echo $response;
    echo '</pre></div>';

    if (curl_errno($connection)) {
        echo 'Error : ' . curl_error($connection);
    }

    curl_close($connection);

    if (!isset($responseData->access_token)){
        die('invalid account');
    }
    return $responseData->access_token;

}

function curlMyFastWayGetRequest($bearer,$fnc,$customMethod='')
{
    global $serviceUrl;

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: bearer ' . $bearer . ''
    );

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $serviceUrl. $fnc);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    if($customMethod)
    {
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $customMethod);
    }
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($connection); // Execute REST Request


    echo '<div style="display: none">Request: <pre>';
    echo $fnc;
    echo '</pre><br>Response: <pre>';
    echo $response;
    echo '</pre></div>';

    if(curl_errno($connection)){
        echo 'Curl error: ' . curl_error($connection) . "\n";die;
    }


    if (curl_getinfo($connection, CURLINFO_HTTP_CODE) == 200 && $customMethod) {
        return 'success';
    } elseif (!$response) {
        echo 'Failed loading ' . "\n";
        echo $response . "\n";die;
    } else {
        $responseData = json_decode($response);
        if($responseData && isset($responseData->errors) && is_array($responseData->errors))
        {
            foreach($responseData->errors as $q){
                echo $q->message."<br>";
            }
            die;
        }

    }
    curl_close($connection);
    return $response;

}


function curlMyFastWay($data,$bearer, $fnc = '/api/consignments')
{
    global $serviceUrl;
    $data_string = json_encode($data);

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Content-length:' . strlen($data_string),
        'Authorization: bearer ' . $bearer . ''
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

    echo '<div style="display: none">Request Data: <pre>';
    echo $data_string;
    echo '</pre><br>Response Url: <pre>';
    echo $serviceUrl . $fnc;
    echo '</pre><br>Response: <pre>';
    echo $response;
    echo '</pre></div>';

    if(curl_errno($connection)){
        echo 'Curl error: ' . curl_error($connection) . "\n";die;
    }
    curl_close($connection);
    if (!$response) {
        echo 'Failed loading ' . "\n";
        echo $response . "\n";die;
    } else {
        $responseData = json_decode($response);
        if($responseData && isset($responseData->errors) && is_array($responseData->errors))
        {
            foreach($responseData->errors as $q){
                echo $q->message."<br>";
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
            `ui`.`country`,
            od.shipment_id
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        INNER JOIN `jos_vm_orders_deliveries` AS `od` ON `od`.`order_id`=`o`.`order_id`
        WHERE `o`.`order_id`=" . $orderId . "");

        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();


            $bearer = curlMyFastWayGetToken($scopes,$order_obj->country,$apiKey,$apiUserId);
            $result = curlMyFastWayGetRequest($bearer,'/api/consignments/'.$order_obj->shipment_id.'/reason/3',"DELETE");
            if ($result=='success') {

                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $mosConfig_status_cancel_aramex . "'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_obj->order_id'";
                $mysqli->query($query);

                $history_comment = 'Fastway Delivery Cancel.';

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
                    '" . $mosConfig_status_cancel_aramex . "', 
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
                echo "Aramex Delivery Canceled";
            }

        }
        die;

    case 'create':
        $order_sql = $mysqli->query("SELECT 
            `o`.`order_id`, 
            `o`.`ddate`, 
            `o`.`customer_comments`, 
            `ui`.`company`,
            `ui`.`first_name`,
            `ui`.`last_name`,
            `ui`.`street_number`,
            `ui`.`street_name`,
            `ui`.`zip`,
            `ui`.`suite`,
            `ui`.`district`,
            `ui`.`state`,
            `ui`.`city`,
            `ui`.`country`,
            `ui`.`phone_1`,
            `ui`.`phone_2`,
            `ui`.`address_1`,
            `ui`.`address_2`
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        WHERE `o`.`order_id`=" . $orderId . "");
        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $streetlines='';
            if ($order_obj->suite) {
                $streetlines = $order_obj->suite . '/';
            }
            if ($order_obj->street_number) {
                $streetlines .=html_entity_decode($order_obj->street_number) . ' ';
            }
            if ($order_obj->street_name) {
                $streetlines .=html_entity_decode($order_obj->street_name) . ' ';
            }
            if ($order_obj->district) {
                $streetlines .=html_entity_decode($order_obj->district);
            }
            $streetlines = ($streetlines) ? $streetlines : html_entity_decode($order_obj->address_1);
            $streetlines2 = ($streetlines) ? '' : html_entity_decode($order_obj->address_2);
            $ddate = date('Y-m-d', strtotime($order_obj->ddate));

            $fields['To'] = [
                "ContactCode"=> "",
                "BusinessName"=> html_entity_decode($order_obj->company),
                "ContactName"=> html_entity_decode( $order_obj->first_name  . ' ' . $order_obj->last_name),
                "PhoneNumber"=> ($order_obj->phone_1) ? $order_obj->phone_1 : $order_obj->phone_2,
                "Email"=> "",
                "Address" => [
                  "StreetAddress"=> $streetlines.$streetlines2,
                  "AdditionalDetails"=> "",
                  "Locality"=> $order_obj->city,
                  "StateOrProvince"=> $order_obj->state,
                  "PostalCode"=> $order_obj->zip,
                  "Country"=> $countryValidaValues[$order_obj->country]??"AU"
                ]
            ];
            $count = count($_REQUEST['Reference']);
            for ($i = 0; $i != $count; $i++) {
                $fields['Items'][] = [
                  "Quantity"=> $_REQUEST['Count'][$i],
                  "Reference"=> $_REQUEST['Reference'][$i],
                  "PackageType"=> "P",
                  "Length"=> 50,
                  "Width"=> 20,
                  "Height"=> 15,
                  "WeightDead"=> $_REQUEST['Weight'][$i],
                ];
            }
            $fields['InstructionsPublic'] = $order_obj->customer_comments;

            $bearer = curlMyFastWayGetToken($scopes,$order_obj->country,$apiKey,$apiUserId);

            $result = curlMyFastWay($fields, $bearer,'/api/consignments');



            if (isset($result->data) && isset($result->data->conId)) {
                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='".$mosConfig_status_sent_aramex."'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $history_comment = 'Aramex ID: ' . $result->data->conId;
                $history_comment .= '<br>Label: ' . $result->data->items[0]->label??'';

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
                    '".$mosConfig_status_sent_aramex."', 
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
                        '" . $mysqli->real_escape_string($result->data->items[0]->label??'') . "',
                        '" . $mysqli->real_escape_string($result->data->conId) . "',
                        '1'
                    )";
                $mysqli->query($query);
                echo "<h4>Total price of label is <b>$".$result->data->total."</b></h4>";
                $result = curlMyFastWayGetRequest($bearer,'/api/consignments/'.$result->data->conId.'/labels');


                    ?>
                    <embed
                            type="application/pdf"
                            src="data:application/pdf;base64,<?php echo base64_encode($result); ?>"
                            id="pdfDocument"
                            width="100%"
                            height="100%" />
                    <script type="text/javascript">
                        window.opener.jQuery(".delivery_icon_<?php echo $orderId; ?>").removeClass('default').attr('href', '').attr('order_id', "").find('img').attr('src', '/templates/bloomex7/images/deliveries/FastWay_logo.png');
                        window.opener.jQuery(".delivery_icon_span_<?php echo $orderId; ?>").html('Updated');
                    </script>
                    <?php
                    die;


            } else {
                ?>
                <?php print_r($result); ?>
                <hr>
                <?php echo json_encode($fields); ?>
                <?php
            }
        }
        die;
    default:
        ?>

            <form id="myForm" class="form" method="POST">
                <?php

                $label_query = $mysqli->query("SELECT
                    `label_type` 
                FROM `fastway_label_postcodes` 
                WHERE 
                    `warehouse` LIKE '" . $mysqli->real_escape_string($warehouse) . "' 
                    AND 
                    `city` LIKE '".$mysqli->real_escape_string($order->city)."' 
                    AND 
                    `postal_code` like '".$mysqli->real_escape_string($order->zip)."'
                        AND ((label_type like 'RED') OR (label_type like 'ORANGE') OR (label_type like 'GREEN'))
                LIMIT 1");

                $packaging = 0;
                if ($label_query->num_rows > 0) {
                    $packaging = 1;
                }

                ?>

                <script type="text/javascript">
                    var packaging = <?php echo $packaging; ?>;
                </script>
                <div id="parentId">
                    <fieldset id="fs1">
                        <legend>Packages</legend>
                        <fieldset id="fs11">
                            <div id="pi1">
                                <label for="reference">Piece Reference:</label>
                                <input class="width"
                                        type="text" size="40"
                                        value="<?php echo $orderId; ?>"
                                        name="Reference[0]"
                                        id="reference_0"
                                        maxlength="32"/>
                            </div>
                            <div id="pi4">
                                <label for="weight">Piece Weight:</label>
                                <input type="number" value="1" name="Weight[0]" id="weight_0"/> kg.
                            </div>
                            <div id="pi5">
                                <label for="count">Total Pieces:</label>
                                <input type="number" value="1" name="Count[0]" id="count_0"/> pcs.
                            </div>
                            <?php
//                            if ($packaging == 1) {
//                                ?>
<!--                                <div id="pi6">-->
<!--                                    <label for="packaging">Use medium flat rate:</label>-->
<!--                                    <input type="checkbox"-->
<!--                                         value="1"-->
<!--                                         name="Packaging[0]"-->
<!--                                         id="packaging_0"-->
<!--                                         checked>-->
<!--                                </div>-->
<!--                                --><?php
//                            }
                            ?>
                        </fieldset>
                        <input type="button" value="+" onClick="AddItem();" ID="add"></input>
                        <input type="button" value="-" onClick="DelItem();" ID="del"></input>
                    </fieldset>
                </div>
                <input class="submit" type="submit" value="Send to Aramex"/>
                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>"/>
                <input type="hidden" name="sender" value="<?php echo $_REQUEST['sender']; ?>"/>
                <input type="hidden" name="task" value="create">
            </form>

        <script>
            var count_div = 1;
            var count_fs = 1;

            function AddItem() {
                var firstform = document.getElementById('fs1');
                var button = document.getElementById('add');
                count_fs++;
                var newitem = '<div id="pi' + count_div + '"><label for="reference' + count_div + '">Piece Reference:</label><input class="width" type="text" value="' + document.getElementsByName('order_id')[0].value + '" name="Reference[' + count_div + ']" id="reference_' + count_div + '" maxlength="32"/></div>';
                newitem += '<div id="pi' + count_div + '"><label for="weight' + count_div + '">Piece Weight:</label><input type="number" value="1" name="Weight[' + count_div + ']" id="weight_' + count_div + '"/> kg.</div>';
                newitem += '<div id="pi' + count_div + '"><label for="count' + count_div + '">Total Pieces:</label><input type="number" value="1" name="Count[' + count_div + ']" id="count_' + count_div + '"/> pcs.</div><br>';
//                if (packaging == 1) {
//                    newitem += '<div id="pi' + count_div + '"><label for="packaging' + count_div + '">Use medium flat rate:</label><input type="checkbox" value="1" name="Packaging[' + count_div + ']" id="packaging_' + count_div + '" checked></div><br>';
//                }
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


<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap.min.css">

<script src="/templates/bloomex_adaptive/js/jquery-3.7.1.min.js"></script>
<script src="/templates/bloomex_adaptive/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="/scripts/deliveries/MyFastWay/main.css">
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
