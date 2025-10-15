<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$service_url = 'https://api.optimoroute.com/v1/delete_order';
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
    
    $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    
    $query = "SELECT
        `o`.`order_id`,
        `o`.`warehouse`,
        `od`.`pin`
    FROM `jos_vm_orders_deliveries` AS `od`
    INNER JOIN `jos_vm_orders` AS `o`
        ON
        `o`.`order_id`=`od`.`order_id`
    WHERE
        `od`.`order_id`=" . $order_id . "
        AND
        `od`.`active`='1'
    ";
    
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        
        $order_obj = $result->fetch_object();

        $post_obj = (object) array (
            'orderNo' => $order_obj->pin
        );

        $curl = curl_init($service_url . '?key=' . $api_key);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_obj));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        $curl_response = curl_exec($curl); 
        $json_response = json_decode($curl_response);


        if ($json_response->success) {
            $mysqlDatetime = date("Y-m-d G:i:s");

            $query = "INSERT INTO `jos_vm_order_history` (	
                `order_id`,
                `order_status_code`,
                `date_added`,
                `comments`, 
                `user_name`
            )
            VALUES (
                " . $order_obj->order_id . ",
                'A',
                '" . $mysqlDatetime . "',
                '" . $mysqli->real_escape_string('Optimoroute Successfully canceled.') . "',
                '" . $mysqli->real_escape_string($my['session_username']) . "'
            )";

            $mysqli->query($query);

            $query = "UPDATE `jos_vm_orders` SET `order_status`='A'
                      WHERE `order_id`=".$order_obj->order_id."";
            $mysqli->query($query);

            $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id=".$order_obj->order_id."";
            $mysqli->query($query);

            ?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>Optimoroute Cancel</title>
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                    <link rel="stylesheet" href="/scripts/resources/bootstrap4/bootstrap.min.css">
                    <link rel="stylesheet" href="/scripts/deliveries/optimoroute/main.css">
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                delivery cancelled successfully
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="window.close();">Close window</button>
                            </div>
                        </div>
                    </div>
                    <script src="/templates/bloomex7/js/jquery-2.2.3.min.js"></script>
                    <script type="text/javascript">
                        window.opener.jQuery('.delivery_icon_<?php echo $order_obj->order_id; ?>')
                        .addClass('default')
                        .attr('order_id', '<?php echo $order_obj->order_id; ?>')
                        .attr('href', '')
                        .find('img')
                        .attr('src', '/templates/bloomex7/images/deliveries/delivery_logo.png');
                        window.opener.jQuery('.delivery_icon_span_<?php echo $order_obj->order_id;?>').text('Updated');
                    </script>
                </body>
            </html>
            <?php

        }
        else {
            ?>
            Error.
            <?php
        }
    }
    else {
        ?>
        No active delivery.
        <?php
    }
    $result->close();
    
    $mysqli->close();
}



