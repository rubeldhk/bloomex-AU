<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

session_name(md5($mosConfig_live_site));
session_start();

$serviceUrl = 'https://ssapi.shipstation.com';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

if (isset($_GET['data'])) {
    $data = base64_decode(strrev($_GET['data']));
    $data_a = explode('||', $data);

    foreach ($data_a as $v) {
        $v_a = explode('|', $v);

        $_REQUEST[$v_a[0]] = $v_a[1];
    }
}
$orders = explode(",", $_REQUEST['order_id']);

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

date_default_timezone_set('Australia/Sydney');
$mysqlDatetime = date("Y-m-d G:i:s", time());

if (!$orders)
    die('order id can not be empty');

$ordersList = [];
$ordersDetails = "<table class='table table-bordered table-striped' style='margin-top: 20px'><tr>
<th>Order Id</th>
<th>Customer Name</th>
<th>Recipient Name</th>
<th>Recipient Address</th>
<th>Delivery Date</th>
</tr>";
$query = "SELECT o.order_id,
                        o.order_shipping as order_shipping,
                        o.customer_note,
                        o.customer_comments,
                        o.ddate,
                        o.order_total as order_total,
                        oi.first_name as ship_first_name,
                        oi.last_name as ship_last_name,
                        oi.phone_1 as ship_phone_1,
                        ob.phone_1 as bill_phone_1,
                        ob.first_name as bill_first_name,
                        ob.last_name as bill_last_name,
                        oi.company as ship_company,
                        ob.company as bill_company,
                        FROM_UNIXTIME(o.cdate + 11 * 3600, '%Y-%m-%d') as order_date,
                        CONCAT(oi.suite ,' ' ,oi.street_number , ' ' , oi.street_name) as ship_address_1,
                        CONCAT(ob.suite ,' ' ,ob.street_number , ' ' , ob.street_name) as bill_address_1,
                        oi.city as ship_city,
                        ob.city as bill_city,
                        ois.state_3_code as ship_state,
                        obs.state_3_code as bill_state,
                        oi.country as ship_country,
                        ob.country as bill_country,
                        oi.zip as ship_zip,
                        ob.zip as bill_zip,
                        ob.user_email,
                        pw.block_shipstation,
                        jvoh.resend_count,
                        jkoh.old_canceled_orders_count
                        FROM `jos_vm_orders` as o
                        left join jos_vm_order_user_info as oi on oi.order_id=o.order_id and oi.address_type='ST'
                        left join jos_vm_state as ois on ois.state_2_code=oi.state 
                        left join jos_vm_order_user_info as ob on ob.order_id=o.order_id and ob.address_type='BT' 
                        left join jos_vm_state as obs on obs.state_2_code=ob.state 
                        left join jos_postcode_warehouse as pw on pw.postal_code = oi.zip and pw.published = 1 and pw.block_shipstation = 1 
                        left join (select count(order_id) as resend_count,order_id from jos_vm_order_history where order_status_code  = 'Y' and order_id in (".$mysqli->escape_string(implode(",", $orders)).") group by order_id) as  jvoh on jvoh.order_id=o.order_id 
                        left join (SELECT count(order_id) AS old_canceled_orders_count,order_id FROM `jos_vm_orders_deliveries` WHERE delivery_type='12' and active='0' and  order_id in (".$mysqli->escape_string(implode(",", $orders)).") group by order_id) as  jkoh on jkoh.order_id=o.order_id 
                        WHERE o.`order_id`in (".$mysqli->escape_string(implode(",", $orders)).")  group by o.order_id";

$ordersSql = $mysqli->query($query);
if ($ordersSql && $ordersSql->num_rows > 0) {
   while ($order = $ordersSql->fetch_object()){
       $ordersList[] = $order;
       $ordersDetails.='<tr>
                        <td>'.$order->order_id.'</td>
                        <td>'.$order->bill_first_name.' '.$order->bill_last_name.'</td>
                        <td>'.$order->ship_first_name.' '.$order->ship_last_name.'</td>
                        <td>'.$order->ship_address_1.', '.$order->ship_city.', '.$order->ship_state.', '.$order->ship_country.', '.$order->ship_zip.'</td>
                        <td>'.$order->ddate.'</td>
                        </tr>';
   };
    $ordersDetails.='</table>';
} else {
    die('order id is empty');
}


function curlShipStationApi($data='', $fnc = '/orders/createorders',$customMethod='POST')
{
    global $serviceUrl,$mosConfig_shipstation_api_key;
    $bearer = base64_encode($mosConfig_shipstation_api_key);
    $curl = curl_init();
    $curlOptions = [
        CURLOPT_URL => $serviceUrl.$fnc,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $customMethod,
        CURLOPT_HTTPHEADER => [
            "Host: ssapi.shipstation.com",
            "Authorization:  Basic $bearer",
            "Content-Type: application/json"
        ],
    ];

    if($customMethod == 'POST') {
        $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    curl_setopt_array($curl, $curlOptions);

    $response = curl_exec($curl);

    echo '<div style="display: none">Request Data: <pre>';
    echo json_encode($data);
    echo '</pre><br>Response Url: <pre>';
    echo $serviceUrl . $fnc;
    echo '</pre><br>Response: <pre>';
    echo $response;
    echo '</pre></div>';

    if(curl_errno($curl)){
        echo 'Curl error: ' . curl_error($curl) . "\n";die;
    }
    curl_close($curl);
    if (!$response) {
        echo 'Failed loading ' . "\n";
        echo $response . "\n";die;
    } else {
        $responseData = json_decode($response);
        if($responseData && isset($responseData->hasErrors) && $responseData->hasErrors==true)
        {
            echo '</pre>'.$responseData;die;
        }
    }

    return $responseData;
}

function getQrCodeUrl ($order_id) {

    global $mysqli,$mosConfig_driverApp_link;

    $query = "SELECT 
            `q`.`id`,
            `q`.`token`
        FROM `jos_vm_orders_qr` AS `q`
        WHERE 
            `q`.`order_id`=" . $order_id . "
        ";

    $qrSql =  $mysqli->query($query);

    if ($qrSql && $qrSql->num_rows > 0) {

        $qr_obj = $qrSql->fetch_object();
        $qr_link = $mosConfig_driverApp_link . '/create/' . $qr_obj->token;

    } else {

        $qr_token = md5('scanit' . $order_id);
        $qr_link = $mosConfig_driverApp_link . '/create/' . $qr_token ;

        $query = "INSERT INTO `jos_vm_orders_qr`
            (
                `order_id`,
                `token`
            )
            VALUES (
                " . $order_id . ",
                '" . $qr_token . "'
            )
            ";

        $mysqli->query($query);

    }

    return $qr_link;
}
function getQrCodeImageUrl ($order_id) {
    global $mosConfig_email_sender_ftp_host,$mosConfig_absolute_path;
    $imagePath = $mosConfig_absolute_path."/administrator/images/qrCodes/qr_{$order_id}.png";

    $file_folder = 'bloomex.com.au/qrcodes/' . $order_id;
    $image_name = time() . '_' . mt_rand(1000, 9999) . '.png';
    $image_link = $file_folder . '/' . $image_name;

    ftp_move_file( $imagePath, $image_link, $file_folder);

    return 'https://media.bloomex.ca/' . $image_link;
}
function ftp_is_dir($ftp, $dir)
{
    $pushd = ftp_pwd($ftp);

    if ($pushd !== false && @ftp_chdir($ftp, $dir)) {
        ftp_chdir($ftp, $pushd);
        return true;
    }

    return false;
}
function ftp_move_file($photo, $filename, $dir)
{
    global $mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass;
    $ftp = ftp_connect($mosConfig_email_sender_ftp_host);

    if (ftp_login($ftp, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass)) {
        ftp_pasv($ftp, true);

        if (!ftp_is_dir($ftp, $dir)) {
            if (!ftp_mkdir($ftp, $dir)) {
                exit("There was a problem while creating $dir\n");
            }
        }

        $trackErrors = ini_get('track_errors');
        ini_set('track_errors', 1);

        $res = ftp_size($ftp, $filename);
        if ($res == -1) {
            if (!@ftp_put($ftp, $filename, $photo, FTP_BINARY)) {
                die("error while uploading file");
            }
        } else {
            return false;
        }
    } else {
        die("Could not login to FTP account");
    }

    ftp_close($ftp);
}

Switch ($task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'cancel':
        $orderId = $_REQUEST['order_id']??'';
        if (!$orderId)
            die('order id can not be empty');

        $order_sql = $mysqli->query("SELECT 
            `o`.`order_id`, 
            `ui`.`country`,
            od.shipment_id
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        INNER JOIN `jos_vm_orders_deliveries` AS `od` ON `od`.`order_id`=`o`.`order_id`
        WHERE `o`.`order_id`=" .  $mysqli->escape_string($orderId) . " order by id desc");

        if ($order_sql->num_rows > 0) {

            $ordersToDelete = $order_sql->fetch_all(MYSQLI_ASSOC);
           foreach($ordersToDelete as $order_obj){


           $result = curlShipStationApi('','/orders/'.$order_obj['shipment_id'].'','DELETE');

            if ($result->success) {

                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $mosConfig_status_cancel_shipstation . "'
                WHERE `order_id`=" . $mysqli->escape_string($order_obj['order_id'])."
                ");

                $query = "UPDATE `jos_vm_orders_deliveries` set active='0' 
                  WHERE order_id='".$mysqli->escape_string($order_obj['order_id'])."' and shipment_id='" . $mysqli->escape_string($order_obj['shipment_id']) . "'";
                $mysqli->query($query);

                $history_comment = $result->message??'';



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
                    " . $order_obj['order_id'] . ",
                    '" . $mosConfig_status_cancel_shipstation . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->escape_string($history_comment) . "')
                ");
                echo '<br>'.$result->message??'';
            } else {
                echo 'There is no order to delete in ShipStation';
            }
           }

        }
        die;

    case 'create':


        if ($ordersList) {
            $data = [];
            $m=0;
           foreach($ordersList as $order) {

               if($order->block_shipstation) {
                   echo "<br>Postal code  ".$order->ship_zip." is blocked for shipstation";
                   continue;
               }

               $orderNumber = 'Bloomex-'.$order->order_id;

               if($order->resend_count || $order->old_canceled_orders_count) {
                   $queryCheck = "SELECT order_id,pin FROM `jos_vm_orders_deliveries` WHERE order_id = $order->order_id and delivery_type='12'";
                   $orderCheckDelivery = $mysqli->query($queryCheck);
                   if ($orderCheckDelivery->num_rows > 0) {
                       $orderNumber.='.R'.($order->resend_count + $order->old_canceled_orders_count);
                   }

               }


               $data[$m] = [
                   'orderNumber' => $orderNumber,
                   'orderKey' => $orderNumber,
                   'orderDate' => $order->order_date,
                   'shipByDate' => date('Y-m-d', strtotime($order->ddate)),
                   'orderStatus' => 'awaiting_shipment',
                   'customerUsername' => $order->user_email,
                   'customerEmail' => $order->user_email,
                   'billTo' =>
                       [
                           'name' => $order->bill_first_name.' '.$order->bill_last_name,
                           'company' => $order->bill_company,
                           'street1' => $order->bill_address_1,
                           'city' => $order->bill_city,
                           'state' => $order->bill_state,
                           'postalCode' => $order->bill_zip,
                           'country' => $countryValidaValues[$order->bill_country]??'AU',
                           'phone' => $order->bill_phone_1,
                       ],
                   'shipTo' =>
                       [
                           'name' => $order->ship_first_name.' '.$order->ship_last_name,
                           'company' => $order->ship_company,
                           'street1' => $order->ship_address_1,
                           'city' => $order->ship_city,
                           'state' => $order->ship_state,
                           'postalCode' => $order->ship_zip,
                           'country' => $countryValidaValues[$order->ship_country]??'AU',
                           'phone' => $order->ship_phone_1
                       ],
                   'amountPaid' => $order->order_total,
                   'shippingAmount' => $order->order_shipping,
                   'giftMessage' => "$order->customer_note",
                   'internalNotes' => $order->resend_count ? 'Resend':'',
                   'customerNotes' => "$order->customer_comments",
                   'packageCode' => 'package',
                   'confirmation' => 'delivery',
                   'shipDate' => date('Y-m-d', strtotime($order->ddate)),
                   'advancedOptions' => [
                           'customField1' => $order->order_id,
                           'customField2' => getQrCodeUrl ($order->order_id),
                           'customField3' => getQrCodeImageUrl ($order->order_id),
                       ],
               ];

                   $items_sql = $mysqli->query("SELECT 
                    `order_item_sku`,
                    `order_item_name`,
                    `product_final_price`,
                    `product_quantity`
                    FROM `jos_vm_order_item`
                    WHERE `order_id`=" . $order->order_id . "");

                   while ($item_obj = $items_sql->fetch_object()) {
                       $data[$m]['items'][] =[
                           'sku' => $item_obj->order_item_sku,
                           'name' => $item_obj->order_item_name,
                           'quantity' => $item_obj->product_quantity,
                           'unitPrice' => $item_obj->product_final_price
                       ];
                   }
                   $m++;
           }

           if(!isset($data) || !$data)
               die('empty data to send to ShipStation');

            $result = curlShipStationApi($data,'/orders/createorders');

            if(isset($_REQUEST['update_deliver_data']))
                die;


            if (isset($result->results)) {
                foreach($result->results as $res) {

                    $order_id = strtok(str_replace('Bloomex-', ' ', $res->orderNumber), '.');
                    if($res->success) {
                        $mysqli->query("UPDATE `jos_vm_orders` 
                            SET 
                                `order_status`='" . $mosConfig_status_sent_shipstation . "'
                            WHERE `order_id`=" . $order_id . "
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
                                " . $order_id . ",
                                " . $mysqli->real_escape_string($_REQUEST['delivery_id']) . ",
                                '" . $mysqlDatetime . "',
                                '" . $mysqli->real_escape_string($res->orderNumber) . "',
                                '" . $mysqli->real_escape_string($res->orderId) . "',
                                '1'
                            )";
                                $mysqli->query($query);

                        $history_comment = 'Shipstation Order ID: ' . $res->orderId.' , orderNumber: '.$res->orderNumber;
                        $status = $mosConfig_status_sent_shipstation;

                    }else{
                        $history_comment = 'Shipstation error message: ' . $res->errorMessage;
                        $status = 'U';

                    }



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
                            '" . $status . "', 
                            '" . $mysqlDatetime . "',
                            '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                            '" . $mysqli->real_escape_string($history_comment) . "')
                        ");

                    echo "<br>".$history_comment." (Bloomex order: ".$order_id.")";



                    if(isset($_REQUEST['addtag'])) {
                        $data = [
                            'orderId' => $res->orderId,
                            'tagId' => $_REQUEST['addtag'],
                        ];

                        $result = curlShipStationApi($data,'/orders/addtag');

                        if ($result->success) {
                            $history_comment = $result->message??'';
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
                                '" . $status . "', 
                                '" . $mysqlDatetime . "',
                                '" . $mysqli->escape_string($_REQUEST['sender']) . "', 
                                '" . $mysqli->escape_string($history_comment) . "')
                            ");
                            echo '<br>'.$result->message??'';
                        } else {
                            echo 'There is some issue to add tag into order in ShipStation';
                        }
                    }

                }
                    die;
            } else {
                ?>
                <?php print_r($result); ?>
                <hr>
                <?php echo json_encode($data); ?>
                <?php
            }
        }
        die;
    default:
        ?>
        <div class="container">
            <div class="row">
                <div class="col-8 col-offset-2">
                    <form id="myForm" class="form" method="POST">
                        <?php echo $ordersDetails;?>
                        <input class="submit btn btn-success" type="submit" value="Send to ShipStation"/>
                        <input type="hidden" name="order_id" value="<?php echo $_REQUEST['order_id']; ?>"/>
                        <input type="hidden" name="sender" value="<?php echo $_REQUEST['sender']; ?>"/>
                        <input type="hidden" name="delivery_id" value="<?php echo $_REQUEST['delivery_id']; ?>"/>
                        <input type="hidden" name="task" value="create">
                    </form>
                </div>
            </div>
        </div>
        <?php
        break;
} ?>


<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap.min.css">

<script src="/templates/bloomex_adaptive/js/jquery-3.7.1.min.js"></script>
<script src="/templates/bloomex_adaptive/js/bootstrap.min.js"></script>

