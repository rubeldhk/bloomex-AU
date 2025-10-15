<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

session_name(md5($mosConfig_live_site));
session_start();

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

if (isset($_GET['data'])) {
    $data = base64_decode(strrev($_GET['data']));
    $data_a = explode('||', $data);

    foreach ($data_a as $v) {
        $v_a = explode('|', $v);

        $_POST[$v_a[0]] = $v_a[1];
    }
}

Switch ($_POST['warehouse']) {
    case 'WH15':
        $api_key = 'U027f64b1be0f4c5e16dc71290f2525d0cd336b7c1f37b4d6';
        break;

    case 'WH12':
    case 'WH14':
    default:
        $api_key = 'U5af8229aacdf575b59b7fa38c92c839b1032a29ecdc5b979';
        break;
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

Switch ($task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'cancel':
        $order_id = (int) $_POST['order_id'];

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
            `ui`.`phone_1`
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        WHERE `o`.`order_id`=" . $order_id . "");

        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $ddate = date('Y-m-d', strtotime($order_obj->ddate));
            $do = 'do' . $order_obj->order_id;

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
                WHERE `order_id`=" . $order_obj->order_id . "
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
                    " . $order_obj->order_id . ",
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
        }

        break;

    default:

        $order_id = (int) $_POST['order_id'];

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
            $do = 'do' . $order_obj->order_id;

            $fields = array();

            $fields[] = array(
                'date' => $ddate,
                'do' => $do,
                'address' => $order_obj->street_number . ' ' . $order_obj->street_name . ' ' . $order_obj->city . ' ' . $order_obj->state . ' ' . $order_obj->zip,
                'delivery_time' => '09=>00 AM - 09=>00 PM',
                'deliver_to' => $order_obj->first_name . ' ' . $order_obj->last_name . ' ' . (!empty($order_obj->company) ? '(' . $order_obj->company . ')' : ''),
                'phone' => $order_obj->phone_1,
                'notify_email' => 'detrack_delivery@bloomex.com.au',
                'notify_url' => 'https://bloomex.com.au/detrack_api/push.php',
                'assign_to' => '',
                'instructions' => $order_obj->customer_comments
            );

            $fields[0]['items'] = array();

            $items_sql = $mysqli->query("SELECT 
                `order_item_sku`,
                `order_item_name`,
                `product_quantity`
            FROM `jos_vm_order_item`
            WHERE `order_id`=" . $order_obj->order_id . "");

            while ($item_obj = $items_sql->fetch_object()) {
                $fields[0]['items'][] = array(
                    'sku' => $item_obj->order_item_sku,
                    'desc' => $item_obj->order_item_name,
                    'qty' => $item_obj->product_quantity
                );
            }

            $result = curl_detrack($fields, true, 'create.json');

            if ($result['info']['failed'] == 0) {

                $mosConfig_status_sent_detrack = '6';

                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $mosConfig_status_sent_detrack . "'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");


                $history_comment = 'JobID: ' . $result['results'][0]['do'] . '.';

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
                    '" . $mosConfig_status_sent_detrack . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_POST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");

                $fields = array(
                    'do' => $do
                );

                $result = curl_detrack($fields, false, 'label.pdf');
                ?>
                <embed src="data:application/pdf;base64,<?php echo base64_encode($result); ?>" width="900" height="700" type="application/pdf">
                <?php
            } else {
                ?>
                <?php echo $result['results'][0]['errors'][0]['message']; ?>
                <?php
            }
        }
        ?>
        <?php
        break;
} 