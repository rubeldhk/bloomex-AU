<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$service_url = 'https://api.optimoroute.com/v1/create_order';
$api_key = 'da78e40ab5593429ce645488e666da710SQmaQzOWLs';
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

session_name(md5($mosConfig_live_site));
session_start();

date_default_timezone_set('Australia/Sydney');

$return = array();
$return['result'] = false;
$my = isset($_SESSION['session_username']) ? $_SESSION : false;



if (!empty($my['session_username'])) {
    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');

    Switch (isset($_GET['action']) ? $_GET['action'] : '') {

        case 'send':
            $return = (object) array(
                'result' => false,
                'error' => '',
                'response' => '',
            );

            $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

            $query = "SELECT
                `o`.`order_id`,
                `o`.`customer_note`, 
                `o`.`customer_comments`, 
                `o`.`ddate`, 
                `o`.`warehouse`, 
                CONCAT(`ui`.`first_name`, ' ', `ui`.`last_name`) AS 'customer_name',
                CONCAT(`ui`.`street_number`, ' ', `ui`.`street_name`) AS 'address',
                `ui`.`suite`,
                `ui`.`city`,
                `ui`.`state`,
                `ui`.`zip`,
                `ui`.`country`,
                `ui`.`phone_1`,
                `ui`.`company`,
                `bi`.`user_email`
            FROM `jos_vm_orders` AS `o`
            INNER JOIN `jos_vm_order_user_info` AS `ui` 
                ON 
                `ui`.`order_id`=`o`.`order_id` 
                AND 
                `ui`.`address_type`='ST'
            INNER JOIN `jos_vm_order_user_info` AS `bi` 
                ON 
                `bi`.`order_id`=`o`.`order_id` 
                AND 
                `bi`.`address_type`='BT'
            WHERE
                `o`.`order_id`=" . $order_id . "
            ";

            $result = $mysqli->query($query);

            if ($result->num_rows > 0) {
                $order_obj = $result->fetch_object();

                $ddate = date("Y-m-d", strtotime($order_obj->ddate));


                $pin = $order_obj->order_id;

                $address = $order_obj->address . " " . (!empty($order_obj->suite) ? 'Apt# ' . $order_obj->suite : '') . " " . $order_obj->city . " " . $order_obj->state . " AUS";
                $post_obj =  array (
                    'operation' => 'CREATE',
                    'orderNo' => $order_obj->order_id,
                    'date' => $ddate,
                    'duration'=> 60,
                    'type' => 'D',
                    'load1' => (int)(isset($_POST['amount_of_packages']) ? $_POST['amount_of_packages'] : 1),
                    'location' => array (
                                        'locationNo'=> 'LOC_'.$order_obj->order_id,
                                        'address'=> $address,
                                        'locationName'=> $order_obj->company,
                                ),
                    'timeWindows' => array(
                        array (
                            'twFrom'=> '13:00',
                            'twTo'=> '15:00',
                        )
                    ),
                    'email' => $order_obj->user_email,
                    'notificationPreference' => 'email',
                    'notes' => $order_obj->customer_comments
                );

                $success = true;

                for ($i = 1; $i <= (int) $_POST['amount_of_packages']; $i++) {
                    $curl = curl_init($service_url . '?key=' . $api_key);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_obj));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                    $curl_response = curl_exec($curl);
                    $json_response = json_decode($curl_response);

                    $return->response = $curl_response;

                    if (!$json_response->success && $json_response->message!='Order with specified orderNo already exists in the system.') {
                        $success = false;
                        $return->result = false;
                        $return->error = $json_response->message;
                    }
                }

                if ($success == true) {
                    $return->result = true;
                    $return->order_id = $order_obj->order_id;
                    $return->clones = (int) $_POST['amount_of_packages'] - 1;

                    $mysqlDatetime = date("Y-m-d G:i:s");

                    $query = "UPDATE `jos_vm_orders` SET `order_status`='G'
                WHERE `order_id`=" . $order_obj->order_id . "";
                    $mysqli->query($query);


                    $query = "INSERT INTO `jos_vm_order_history` (	
                        `order_id`,
                        `order_status_code`,
                        `date_added`,
                        `comments`, 
                        `user_name`
                    )
                    VALUES (
                        " . $order_obj->order_id . ",
                        'G',
                        '" . $mysqlDatetime . "',
                        '" . $mysqli->real_escape_string('Optimoroute Successfully placed order #' . $pin . '.') . "',
                        '" . $mysqli->real_escape_string($my['session_username']) . "'
                    )";

                    $mysqli->query($query);

                    $delivery_id = (int) $_REQUEST['delivery_id'];
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
                            " . $delivery_id . ",
                            '" . $mysqlDatetime . "',
                            '" . $pin . "',
                            '1'
                        )";

                    $mysqli->query($query);

                    include_once $_SERVER['DOCUMENT_ROOT'] . '/barcode_new/src/BarcodeGenerator.php';
                    include_once $_SERVER['DOCUMENT_ROOT'] . '/barcode_new/src/BarcodeGeneratorPNG.php';

                    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

                    $return->barcode = base64_encode($generator->getBarcode($pin, $generator::TYPE_CODE_128));

                }
            }
            else {
                $return->error = 'Order is not exist.';
            }
            $result->close();
            echo json_encode($return);

            break;

        default:
            $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

            $query = "SELECT
                `o`.`order_id`,
                `o`.`customer_note`, 
                `o`.`customer_comments`, 
                `o`.`ddate`, 
                CONCAT(`ui`.`first_name`, ' ', `ui`.`last_name`) AS 'customer_name',
                CONCAT(`ui`.`street_number`, ' ', `ui`.`street_name`) AS 'address',
                `ui`.`suite`,
                `ui`.`city`,
                `ui`.`state`,
                `ui`.`zip`,
                `ui`.`country`,
                `ui`.`phone_1`
            FROM `jos_vm_orders` AS `o`
            INNER JOIN `jos_vm_order_user_info` AS `ui` 
                ON 
                `ui`.`order_id`=`o`.`order_id` 
                AND 
                `ui`.`address_type`='ST'
            WHERE
                `o`.`order_id`=" . $order_id . "
            ";

            $result = $mysqli->query($query);

            if ($result->num_rows > 0) {

                $order_obj = $result->fetch_object();

                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Optimoroute</title>
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                    <link rel="stylesheet" href="/scripts/resources/bootstrap.min.css">
                    <link rel="stylesheet" href="/scripts/deliveries/optimoroute/main.css">
                </head>
                <body>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <form id="send-form">
                                <div class="form-group">
                                    <label for="amount_of_packages">Amount of packages</label>
                                    <input type="number" class="form-control" id="amount_of_packages" name="amount_of_packages" value="1" min="1">
                                </div>
                                <input type="hidden" name="task" value="send">
                                <?php
                                foreach ($_GET as $k => $v) {
                                    ?>
                                    <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
                                    <?php
                                }
                                ?>
                                <button type="submit" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="sr-only">Loading...</span>
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="label_div">
                    <div class="label_wrapper">
                        <div class="labels">
                            <div class="line">

                                <div class="address_to">
                                    <strong>Shipping Details </strong></br>
                                    <?php echo $order_obj->customer_name; ?>
                                    <br/>
                                    <?php echo $order_obj->state; ?> <?php echo $order_obj->city; ?> <?php echo $order_obj->zip; ?>
                                    <br/>
                                    <?php echo $order_obj->address; ?><?php echo (!empty($order_obj->suite) ? ',<br/>Apt# ' . $order_obj->suite : ''); ?>
                                    <?php echo (!empty($order_obj->phone_1) ? ',<br/>Phone ' . $order_obj->phone_1 : ''); ?>
                                    <br/>
                                </div>

                                <div class= "address_title">
                                    Order ID : <strong><?php echo $order_obj->order_id; ?></strong><br>
                                    Ship Date : <strong><?php echo $order_obj->ddate; ?></strong><br>
                                </div>
                            </div>
                            <hr>

                            <div class="line">

                                <div class="barcode">
                                    <img src="" />
                                    <br/>
                                    <span class="barcode_text"></span>
                                </div>
                                <div class="sticker_logo">
                                    <img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/deliveries/Optimoroute_logo_lg.png">
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <script src="/templates/bloomex7/js/jquery-2.2.3.min.js"></script>
                <script src="/scripts/deliveries/optimoroute/main.js"></script>
                </body>
                </html>
                <?php
            }
            else {
                ?>
                Error.
                <?php
            }
            $result->close();

            break;
    }

    $mysqli->close();
}



