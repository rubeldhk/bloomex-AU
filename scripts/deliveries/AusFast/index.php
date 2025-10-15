<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
require_once('../warehouses.php');

$host = 'api.transvirtual.com.au';
$serviceUrl = 'https://'.$host;



$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$orderId = ($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';



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
    default :
        $tokenKey = '56792|GKFKWNHKWS';
        break;

}
$warehouse_obj = new warehouses($warehouse);

function curlAusFast($data,$tokenKey, $fnc = '/Api/Consignment',$customMethod = 'POST')
{
    global $serviceUrl,$host;
    $data_string = json_encode($data);

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Host: '.$host,
        'Content-length:' . strlen($data_string),
        'Authorization: ' . $tokenKey . ''
    );

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $serviceUrl . $fnc);
    curl_setopt($connection, CURLOPT_POST, true);
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $customMethod);
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



            $fields = ["UniqueId" => $order_obj->shipment_id];

            $result = curlAusFast($fields, $tokenKey,'/Api/Consignment','DELETE');

            if (isset($result->StatusCode) && $result->StatusCode == 200 ){

                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $mosConfig_status_cancel_ausfast . "'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_obj->order_id'";
                $mysqli->query($query);

                $history_comment = 'AusFast Delivery Cancel.';

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
                    '" . $mosConfig_status_cancel_ausfast . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");
                echo "AusFast Delivery Canceled";
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
            `ui`.`state`,
            `ui`.`city`,
            `ui`.`country`,
            `ui`.`phone_1`,
            `ui`.`phone_2`,
            `ui`.`address_1`,
            `ui`.`address_2`,
            `ui`.`user_email`
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        WHERE `o`.`order_id`=" . $orderId . "");
        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $streetlines='';
            if ($order_obj->suite) {
                $streetlines = $order_obj->suite . '-';
            }
            if ($order_obj->street_number) {
                $streetlines .=html_entity_decode($order_obj->street_number) . ' ';
            }
            if ($order_obj->street_name) {
                $streetlines .=html_entity_decode($order_obj->street_name);
            }
            $streetlines = ($streetlines) ? $streetlines : html_entity_decode($order_obj->address_1);
            $streetlines2 = ($streetlines) ? '' : html_entity_decode($order_obj->address_2);
            $ddate = date('Y-m-d', strtotime($order_obj->ddate));

            $fields = [
                "UniqueId" => $orderId,
                "Number" => "Bloomex-".$orderId,
                "ConsignmentServiceType" => "Standard",
                "Date" => $ddate,
                "CustomerCode" => "BLOOMEX",
//                "SenderName" => $warehouse_obj->warehouse['PersonName'],
                "SenderName" => 'Bloomex Pty Ltd',
                "SenderAddress" => $warehouse_obj->warehouse['StreetNumber'] . ' ' . $warehouse_obj->warehouse['StreetName'],
                "SenderAddress2" => "",
                "SenderSuburb" => $warehouse_obj->warehouse['City'],
                "SenderState" => $warehouse_obj->warehouse['StateOrProvinceCode'],
                "SenderPostcode" => $warehouse_obj->warehouse['PostalCode'],
                "SenderReference" => "Test 1",
                "ConsignmentSenderContact" => $warehouse_obj->warehouse['PersonName'],
                "ConsignmentSenderPhone" => "",
                "SenderEmail" => $warehouse_obj->warehouse['WarehouseEmail'],
                "ConsignmentPickupSpecialInstructions" => "Stair Access Only",
                "ConsignmentSenderIsResidential" => "n",
                "ReceiverName" => html_entity_decode( $order_obj->first_name  . ' ' . $order_obj->last_name),
                "ReceiverAddress" => $streetlines.$streetlines2,
                "ReceiverAddress2" => "",
                "ReceiverSuburb" => $order_obj->city,
                "ReceiverState" => $order_obj->state,
                "ReceiverPostcode" => $order_obj->zip,
                "ConsignmentReceiverContact" => html_entity_decode( $order_obj->first_name  . ' ' . $order_obj->last_name),
                "ConsignmentReceiverPhone" => $order_obj->phone_1,
                "ReceiverEmail" =>  $order_obj->user_email,
                "ConsignmentReceiverIsResidential" => "y",
                "ReturnPdfLabels" => "y",
                "ReturnPdfConsignment" => "n",
                "PickupRequest" => "y",
                "SpecialInstructions" => html_entity_decode($order_obj->customer_comments),
                "ConsignmentOtherReferences" => "",
                "ConsignmentOtherReferences2" => "",
                "AdditionalServiceList" => "",
            ];
            $count = count($_REQUEST['Reference']);
            for ($i = 0; $i != $count; $i++) {
                $fields['Rows'][] = [
                    "Reference" => $_REQUEST['Reference'][$i],
                    "ConsignmentLengthUom" => "mm",
                    "QtyDecimal" => 1,
                    "Description"=> $_REQUEST['Reference'][$i],
                    "ItemContentsDescription" => "",
                    "Width" => "75",
                    "Length" => "300",
                    "Height" => "50",
                    "Weight"=> $_REQUEST['Weight'][$i],
                    "DangerousGoodsUNNumber" => "",
                    "DangerousGoodsClass" => "",
                    "DangerousGoodsSubRisk" => "",
                    "DangerousGoodsPackagingGroup" => "",
                    "Items" => [[
                        "Barcode" => "bloom-".$orderId,
                    ]]
                ];
            }


            $result = curlAusFast($fields, $tokenKey);

            if (isset($result->Data) && isset($result->Data->Id) && isset($result->StatusCode) && $result->StatusCode == 200 ) {
                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='".$mosConfig_status_sent_ausfast."'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $history_comment = 'AusFast ID: ' . $result->Data->Id;
                $history_comment .= '<br>ConsignmentNumber: ' . $result->Data->ConsignmentNumber??'';

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
                    '".$mosConfig_status_sent_ausfast."', 
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
                        '" . $mysqli->real_escape_string($result->Data->ConsignmentNumber??'') . "',
                        '" . $order_obj->order_id . "',
                        '1'
                    )";
                $mysqli->query($query);

//                $resultPrice = curlAusFast($fields, $tokenKey,'/Api/PriceEstimate');
//                echo "<pre>";print_r($resultPrice);die;
//                echo "<h4>Total price of label is <b>$".$resultPrice->Data->Rows->GrandPrice??''."</b></h4>";


                    ?>
                    <embed
                            type="application/pdf"
                            src="data:application/pdf;base64,<?php echo $result->Data->PdfLabels; ?>"
                            id="pdfDocument"
                            width="100%"
                            height="100%" />
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
                                <input type="number" value="0.5" name="Weight[0]" id="weight_0"/> kg.
                            </div>
                            <div id="pi5">
                                <label for="count">Total Pieces:</label>
                                <input type="number" value="1" name="Count[0]" id="count_0"/> pcs.
                            </div>
                        </fieldset>
                        <input type="button" value="+" onClick="AddItem();" ID="add"></input>
                        <input type="button" value="-" onClick="DelItem();" ID="del"></input>
                    </fieldset>
                </div>
                <input class="submit" type="submit" value="Send to AusFast"/>
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
                newitem += '<div id="pi' + count_div + '"><label for="weight' + count_div + '">Piece Weight:</label><input type="number" value="0.5" name="Weight[' + count_div + ']" id="weight_' + count_div + '"/> kg.</div>';
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


<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap.min.css">

<script src="/templates/bloomex_adaptive/js/jquery-3.7.1.min.js"></script>
<script src="/templates/bloomex_adaptive/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="/scripts/deliveries/AusFast/main.css">

