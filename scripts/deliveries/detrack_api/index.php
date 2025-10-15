<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once$_SERVER['DOCUMENT_ROOT'] . "/includes/kint.phar";
include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/kint_fleo.php";

session_name(md5($mosConfig_live_site));
session_start();

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$order_id = (int) $_REQUEST['order_id'];
$order_sql = $mysqli->query("SELECT warehouse FROM jos_vm_orders WHERE order_id='$order_id'");
if ($order_sql->num_rows > 0) {
    $order_obj = $order_sql->fetch_object();
    $warehouse = $order_obj->warehouse;
}

Switch ($warehouse) {
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

    $raw = $response;
    if ($response === false) {
        die('Error fetching data: ' . curl_error($connection));
    }

    curl_close($connection);

    if ($json == true) {
        $response = json_decode($response, true);
    }
    if (isset($_REQUEST['debug'])) {
        f($data, $data_string, $raw, $fnc);
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
             od.shipment_id
        FROM `jos_vm_orders` AS `o` 
        INNER JOIN `jos_vm_order_user_info` AS `ui` ON `ui`.`order_id`=`o`.`order_id` AND `ui`.`address_type`='ST'
        INNER JOIN `jos_vm_orders_deliveries` AS `od` ON `od`.`order_id`=`o`.`order_id`
        WHERE `o`.`order_id`=" . $order_id . " and od.active=1 ");

        if ($order_sql->num_rows > 0) {
            $order_obj = $order_sql->fetch_object();

            $ddate = date('Y-m-d', strtotime($order_obj->ddate));

            $fields = array(
                array(
                    'date' => $ddate, //Date in yyyy-mm-dd format
                    'do' => $order_obj->shipment_id,
                )
            );

            $result = curl_detrack($fields, true, 'delete.json');

            if ($result['info']['failed'] == 0) {


                $status_code = 'A';


                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $status_code . "'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");

                $query = "UPDATE `jos_vm_orders_deliveries` SET active = 0
                  WHERE order_id='$order_obj->order_id'";
                $mysqli->query($query);

                $history_comment = 'Detrack Delivery Cancel. JobID: '.$order_obj->shipment_id;

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
                Success.
                <script type="text/javascript">
                    window.opener.jQuery(".delivery_icon_<?php echo $order_obj->order_id; ?>").addClass('default').attr('href', '').attr('order_id', "<?php echo $order_obj->order_id; ?>").find('img').attr('src', '/templates/bloomex7/images/deliveries/delivery_logo.png');
                    window.opener.jQuery(".delivery_icon_span_<?php echo $order_obj->order_id; ?>").html('Updated')
                </script>
                <?php
            } else {
                ?>
                <?php echo $result['results'][0]['errors'][0]['message']; ?>
                <?php
            }
        }

        break;

    default:



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

            $orderGetOldRowsCount = $mysqli->query("SELECT 
            count(id) as oldShippingsCount 
           FROM `jos_vm_orders_deliveries` WHERE `order_id`=" . $order_id . "");
            $orderGetOldRowsCountObj = $orderGetOldRowsCount->fetch_object();

            $do = 'do' . $order_obj->order_id.($orderGetOldRowsCountObj->oldShippingsCount?"-".$orderGetOldRowsCountObj->oldShippingsCount++:"-0");

            $fields = array();

            $fields[] = array(
                'date' => $ddate,
                'do' => $do,
                'address' => $order_obj->street_number . ' ' . $order_obj->street_name . ' ' . $order_obj->city . ' ' . $order_obj->state . ' ' . $order_obj->zip,
                'delivery_time' => '09=>00 AM - 09=>00 PM',
                'deliver_to' => $order_obj->first_name . ' ' . $order_obj->last_name . ' ' . (!empty($order_obj->company) ? '(' . $order_obj->company . ')' : ''),
                'phone' => $order_obj->phone_1,
                'notify_email' => 'detrack_delivery@bloomex.com.au',
                'notify_url' => 'https://bloomex.com.au/scripts/deliveries/detrack_api/push.php',
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



                $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='C'
                WHERE `order_id`=" . $order_obj->order_id . "
                ");


                $history_comment = 'Detrack Delivery Created JobID: ' . $result['results'][0]['do'] . '.';

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
                        '" . $mysqli->real_escape_string($result['results'][0]['do']) . "',
                        '" . $mysqli->real_escape_string($result['results'][0]['do']) . "',
                        '1'
                    )";
                $mysqli->query($query);



                $fields = array(
                    'do' => $do
                );

                $result = curl_detrack($fields, false, 'label.pdf');

                if (!isset($_REQUEST['debug'])) {
                    ?>
                    <embed src="data:application/pdf;base64,<?php echo base64_encode($result); ?>" width="900" height="700" type="application/pdf">
                    <?php
                } else {
                    f($result);
                }
                ?>
                <script type="text/javascript">
                    window.opener.jQuery(".delivery_icon_<?php echo $order_obj->order_id; ?>").removeClass('default').attr('href', '').attr('order_id', "").find('img').attr('src', '/templates/bloomex7/images/deliveries/DeTrack_logo.png');
                    window.opener.jQuery(".delivery_icon_span_<?php echo $order_obj->order_id; ?>").html('Updated');
                </script>
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