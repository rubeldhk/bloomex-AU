<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

session_name(md5($mosConfig_live_site));
session_start();

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$order_id = ($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';
if (!$order_id)
    die('order id can not be empty');
$order_sql = $mysqli->query("SELECT 
  o.warehouse, 
  o.ddate, 
  `i`.`street_number`, 
  `i`.`street_name`, 
  `i`.`zip`, 
  `i`.`suite`, 
  `i`.`state`, 
  `i`.`city`, 
  `i`.`country`, 
  `i`.`phone_1`, 
  `w`.`warehouse_name` as `sender_name`, 
  `w`.`warehouse_email`, 
  `w`.`phone` as `sender_phone_number`, 
  `w`.`street_name` as `pickup_street_name`, 
  `w`.`street_number` as `pickup_street_number`, 
  `w`.`postal_code` as `pickup_postal_code`, 
  `w`.`city` as `pickup_city` ,
  `w`.`state` as `pickup_state` 
FROM 
  jos_vm_orders as o 
  left join jos_vm_warehouse as w on w.warehouse_code = o.warehouse 
  left join jos_vm_order_user_info as i on i.order_id = o.order_id 
  and `i`.`address_type` = 'ST' 
WHERE 
  o.order_id ='$order_id'");
if ($order_sql->num_rows > 0) {
    $order_obj = $order_sql->fetch_object();

    $warehouse = $order_obj->warehouse;
    $sender_name = $order_obj->sender_name;
    $sender_phone_number = ($order_obj->sender_phone_number) ? $order_obj->sender_phone_number : '11111111111';
    $sender_email = $order_obj->warehouse_email;
    $pickup_address1 = $order_obj->pickup_street_number . " " . $order_obj->pickup_street_name;
    $pickup_postal_code = $order_obj->pickup_postal_code;
    $pickup_city = $order_obj->pickup_city;
    $pickup_state = $order_obj->pickup_state;
    $pickup_country = "Australia";
    $ddate = date('Y-m-d', strtotime($order_obj->ddate));
    $delivery_address = $order_obj->suite . ' ' . $order_obj->street_number . ' ' . $order_obj->street_name . ' ' . $order_obj->city . ' ' . $order_obj->state . ' ' . $order_obj->country . ' ' . $order_obj->zip;
    $pickup_phone_number = ($order_obj->sender_phone_number) ? $order_obj->sender_phone_number : '11111111111';
}
$service_url = 'https://staging.pickfleet.com/api/';
Switch ($warehouse) {
    case 'WH14':
        $api_key = 'bGl2ZS1aV08yVlBZNlVUTEJVOUlTRlJJSzJVU086UU5HVU5IUVhNTEMzTTFBSw==';
        $api_email = 'info@polarkool.com.au';
        $timestamp = 36000;
        break;

    default:
        $api_key = 'bGl2ZS1aV08yVlBZNlVUTEJVOUlTRlJJSzJVU086UU5HVU5IUVhNTEMzTTFBSw==';
        $api_email = 'info@polarkool.com.au';
        $timestamp = 39600;
        break;
}

function curl_pickfleet($data, $json, $fnc = 'task/createTask') {
    global $api_key, $api_email, $service_url;

    $data_string = json_encode($data);

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Content-length:' . strlen($data_string),
        'Authorization: ' . $api_key . '',
        'email: ' . $api_email . ''
    );

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $service_url . $fnc);
    curl_setopt($connection, CURLOPT_POST, true);
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);



    $response = curl_exec($connection);


//    curl_setopt($connection, CURLOPT_HEADER, true);
//    curl_setopt($connection, CURLINFO_HEADER_OUT, true);
//       $request = fopen($_SERVER["DOCUMENT_ROOT"]."/create_shipment_request.txt", "w") or die("Unable to open file!");
//       fwrite($request, json_encode($headers) . PHP_EOL . json_encode($data));
//       $response_file = fopen($_SERVER["DOCUMENT_ROOT"]."/create_shipment_response.txt", "w") or die("Unable to open file!");
//       fwrite($response_file, $response);
//       fclose($response_file);
//       fclose($request);
    echo '<div style="display: none">Request: <pre>';
    echo $data_string;
    echo '</pre><br>Response: <pre>';
    echo $response;
    echo '</pre></div>';

    if ($response === false) {
        die('Error fetching data: ' . curl_error($connection));
    }

    curl_close($connection);

    if ($json == true) {
        $response = json_decode($response, true);
    }

    return $response;
}

Switch ($task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'cancel':


        $order_sql = $mysqli->query("SELECT 
            `o`.`order_id`, 
            `o`.`ddate`, 
            `o`.`customer_comments`, 
            `ui`.`first_name`,
            `ui`.`last_name`,
            `ui`.`street_number`,
            `ui`.`street_name`,
            `ui`.`zip`,
            `ui`.`state`,
            `ui`.`city`,
            `ui`.`country`,
            `ui`.`phone_1`,
            od.pin
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        INNER JOIN `jos_vm_orders_deliveries` AS `od` ON `od`.`order_id`=`o`.`order_id`
        WHERE `o`.`order_id`=" . $order_id . "");

        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $status_code = 'A';

            $fields = new stdClass();
            $fields->jobId = $order_obj->pin;
            $fields->cancellation_reason = 'NA';
            $result = curl_pickfleet($fields, true, 'task/deleteTask');
            if ($result['response'] == 'Success') {

                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $status_code . "'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_obj->order_id'";
                $mysqli->query($query);

                $history_comment = 'Pickfleet Delivery Cancel.';

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
            }
            echo $result['response'];
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
            `ui`.`phone_1`
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        WHERE `o`.`order_id`=" . $order_id . "");
        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $ddate = date('Y-m-d', strtotime($order_obj->ddate));


            $fields = new stdClass();

            $fields->order_type = 'dropoff';
            $fields->customer_tracking_id = $order_obj->order_id;
            $fields->date_after = ($_REQUEST['date_after']) ? strtotime($_REQUEST['date_after']) - $timestamp : 0;
            $fields->date_after = intval($fields->date_after) * 1000; //milliseconds lul
            $fields->date_before = ($_REQUEST['date_before']) ? strtotime($_REQUEST['date_before']) - $timestamp : 0;
            $fields->date_before = intval($fields->date_before) * 1000; //milliseconds lul
            $fields->sender_name = $sender_name;
            $fields->sender_phone_number = $sender_phone_number;
            $fields->sender_email = $sender_email;
            $fields->order_id = $order_obj->order_id;
            $fields->business_name = 'Bloomex';
            $fields->first_name = $order_obj->first_name;
            $fields->last_name = $order_obj->last_name;
            $fields->middle_name = '';
            $fields->phone_number = (strlen($order_obj->phone_1) >= 10) ? $order_obj->phone_1 : '11111111111';
            $fields->postal_code = $order_obj->zip;
            $fields->email = $sender_email;
            $fields->city = $order_obj->city;
            $fields->country = $order_obj->country;
            $fields->address1 = $order_obj->suite . ' ' . $order_obj->street_number . ' ' . $order_obj->street_name;
            $fields->address2 = '';
            $fields->pickup_address1 = $pickup_address1;
            $fields->pickup_address2 = "";
            $fields->pickup_city = $pickup_city;
            $fields->pickup_country = "Australia";
            $fields->pickup_state = $pickup_state;
            $fields->pickup_postal_code = $pickup_postal_code;
            $fields->pickup_phone_number = $sender_phone_number;
            $fields->pickup_date_after = ($_REQUEST['pickup_date_after']) ? strtotime($_REQUEST['pickup_date_after']) - $timestamp : 0;
            $fields->pickup_date_after = intval($fields->pickup_date_after) * 1000;
            $fields->pickup_date_before = ($_REQUEST['pickup_date_before']) ? strtotime($_REQUEST['pickup_date_before']) - $timestamp : 0;
            $fields->pickup_date_before = intval($fields->pickup_date_before) * 1000;
            $fields->state = $order_obj->state;
            $fields->signature_pod = true;
            $fields->max_time_spent_at_delivery_location = 0;
            $fields->photo_pod = true;
            $fields->barcode_pod = true;
            $fields->post_url = 'https://bloomex.com.au/pickfleet.php';
            $fields->special_instructions = $order_obj->customer_comments;


            $item = array(
                'item_sku' => 'BLX',
                'item_description' => 'Bloomex Box',
                'item_quantity' => 1
            );
            
            $fields->items[] = (object) $item;

            $result = curl_pickfleet($fields, true, 'task/createTask');

            if ($result['response'] == 'Success') {
                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='C'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $history_comment = 'PickFleet JobID: ' . $result['jobId'] . '.';

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
                        `active`
                    ) 
                    VALUES (
                        " . $order_obj->order_id . ",
                        " . $mysqli->real_escape_string($_REQUEST['delivery_id']) . ",
                        '" . $mysqlDatetime . "',
                        '" . $mysqli->real_escape_string($result['jobId']) . "',
                        '1'
                    )";
                $mysqli->query($query);




                include_once $_SERVER['DOCUMENT_ROOT'] . '/barcode_new/src/BarcodeGenerator.php';
                include_once $_SERVER['DOCUMENT_ROOT'] . '/barcode_new/src/BarcodeGeneratorPNG.php';
                $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = base64_encode($generator->getBarcode($result['jobId'], $generator::TYPE_CODE_128));
                ?>
                <link rel="stylesheet" href="/scripts/deliveries/pickfleet/main.css">
                <div class="label_div">
                    <div class="label_wrapper">
                        <div class="labels">
                            <div class="line">

                                <div class="address_to">
                                    <strong>Shipping Details </strong></br>
                                    <?php echo $order_obj->first_name . " " . $order_obj->last_name; ?>
                                    <br/>
                                    <?php echo $order_obj->state; ?> <?php echo $order_obj->city; ?> <?php echo $order_obj->zip; ?>
                                    <br/>
                                    <?php echo $fields->address1; ?><?php echo (!empty($order_obj->suite) ? '<br/>Apt# ' . $order_obj->suite : ''); ?>
                                    <?php echo (!empty($order_obj->phone_1) ? '<br/>Phone ' . $order_obj->phone_1 : ''); ?>
                                    <br/>
                                </div>

                                <div class= "address_title">
                                    Order ID : <strong><?php echo $order_obj->order_id; ?></strong><br>
                                    Job ID : <strong><?php echo $result['jobId']; ?></strong><br>
                                </div>
                            </div>
                            <hr>

                            <div class="line">

                                <div class="barcode">
                                    <img src="data:image/png;base64,<?php echo $barcode; ?>" />
                                    <br/>
                                    <span class="barcode_text"><?php echo $result['jobId']; ?></span>
                                </div>
                                <div class="sticker_logo">
                                    <img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/deliveries/PickFleet_logo_lg.png">
                                </div>

                            </div>

                        </div>
                    </div>
                </div>


                <script type="text/javascript">
                    window.opener.jQuery(".delivery_icon_<?php echo $order_obj->order_id; ?>").removeClass('default').attr('href', '').attr('order_id', "").find('img').attr('src', '/templates/bloomex7/images/deliveries/PickFleet_logo.png');
                    window.opener.jQuery(".delivery_icon_span_<?php echo $order_obj->order_id; ?>").html('Updated');
                </script>
                <?php
            } else {
                ?>
                <?php echo $result['response']; ?>
                <hr>
                <?php print_r($result); ?>
                <hr>
                <?php echo json_encode($fields); ?>
                <?php
            }
        }
        die;
    default:
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <?php
                    echo '<br>Order Id : ' . $order_id;
                    echo '<br>Delivery Date : ' . $ddate;
                    echo '<br>Delivery Address : ' . $delivery_address . "<hr>";
                    ?>
                    <form method="post">
                        <div class="row">
                            <div  class="col-md-6">
                                <label for="date_after">Date After</label>
                                <input type="text" class="form-control" name="date_after" id="date_after" value="<?php echo $ddate . ' 18:00:00'; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_before">Date Before</label>
                                <input type="text" class="form-control" name="date_before" id="date_before" value="<?php echo $ddate . ' 20:00:00'; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div  class="col-md-6">
                                <label for="pickup_date_after">Pickup Date After</label>
                                <input type="text" class="form-control" name="pickup_date_after" id="pickup_date_after" value="<?php echo $ddate . ' 15:00:00'; ?>">
                            </div>
                            <div  class="col-md-6">
                                <label for="pickup_date_before">Pickup Date Before</label>
                                <input type="text" class="form-control" name="pickup_date_before" id="pickup_date_before" value="<?php echo $ddate . ' 18:00:00'; ?>">
                            </div>
                        </div>
                        <input type="hidden" name="delivery_id" value="<?php echo $_REQUEST['delivery_id']; ?>">
                        <input type="hidden" name="sender" value="<?php echo $_REQUEST['sender']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="task" value="create">
                        <button type="submit" name="create_shipment" style="margin-left: 0px" class="btn btn-primary">Create Shipment</button>

                    </form>
                </div>
            </div>
        </div>

        <?php
        break;
}
?>


<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap.min.css">
<script src="/templates/bloomex_adaptive/js/jquery-3.7.1.min.js"></script>
<script src="/templates/bloomex_adaptive/js/bootstrap.min.js"></script>
<script src="/templates/bloomex_adaptive/js/moment-with-locales.min.js"></script>
<script src="/templates/bloomex_adaptive/js/bootstrap-material-datetimepicker.js"></script>

<link rel="stylesheet" href="/templates/bloomex_adaptive/css/bootstrap-material-datetimepicker.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<style>

    @font-face {
        font-family: 'Material Icons';
        font-style: normal;
        font-weight: 400;
        src: url(https://fonts.gstatic.com/s/materialicons/v55/flUhRq6tzZclQEJ-Vdg-IuiaDsNc.woff2) format('woff2');
    }

    .material-icons {
        color: #fff;
        font-family: 'Material Icons';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
    .btn{
        margin:10px;
    }

</style>
<script>

                    $("#date_before").bootstrapMaterialDatePicker({format: 'YYYY-MM-DD  HH:mm:00', switchOnClick: true});
                    $("#date_after").bootstrapMaterialDatePicker({format: 'YYYY-MM-DD  HH:mm:00', switchOnClick: true});
                    $("#pickup_date_before").bootstrapMaterialDatePicker({format: 'YYYY-MM-DD  HH:mm:00', switchOnClick: true});
                    $("#pickup_date_after").bootstrapMaterialDatePicker({format: 'YYYY-MM-DD  HH:mm:00', switchOnClick: true});
</script>