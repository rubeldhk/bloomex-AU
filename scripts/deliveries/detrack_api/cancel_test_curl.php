<?php


include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$ddate = "2014-02-13";
$do = 'do1131287';
$fields = array(
    array(
        'date' => $ddate, //Date in yyyy-mm-dd format
        'do' => $do,
    )
);

$result = curl_detrack($fields, true, 'delete.json');

if ($result['info']['failed'] == 0) {
    $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $mosConfig_status_cancel_detrack . "'
                WHERE `order_id`=1131287
                ");



    $history_comment = 'Delivery cancel.';

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
                    '1131287',
                    '" . $mosConfig_status_cancel_detrack . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_POST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");
    ?>
    Success.
    <?php
} else {
    ?>
    <?php echo $result['results'][0]['errors'][0]['message']; ?>
    <?php
}


function curl_detrack($data, $json, $fnc = 'create.json') {
    global $api_key;

    $data_string = json_encode($data);

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Content-length:' . strlen($data_string),
        'X-API-KEY: ' . $api_key . ''
    );

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, 'https://app.detrack.com/api/v1/deliveries/' . $fnc);
    curl_setopt($connection, CURLOPT_POST, true);
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($connection);

    if ($response === false) {
        die('Error fetching data: ' . curl_error($connection));
    }

    curl_close($connection);

    if ($json == true) {
        $response = json_decode($response, true);
    }

    return $response;
}