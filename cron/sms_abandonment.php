<?php

class SMSParam {

    public $CellNumber;
    public $AccountKey;
    public $MessageBody;

}

$time = time();
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';


$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
global $mysqli;
date_default_timezone_set('Australia/Sydney');
$datetime_from = date('Y-m-d G:i:s', strtotime('-60 minutes', strtotime(date('Y-m-d G:i:s'))));
$datetime_to = date('Y-m-d G:i:s', strtotime('-15 minutes', strtotime(date('Y-m-d G:i:s'))));

$query = "DELETE FROM `jos_vm_carts` WHERE dateadd < NOW() - INTERVAL 7 DAY";

$result = $mysqli->query($query);

if (!$result) {
    die('DELETE error: ' . $mysqli->error);
}

$query = "SELECT
    `a`.* 
FROM `tbl_cart_abandonment` AS `a`
WHERE  
    `a`.`status`='abandonment' 
    AND 
    `a`.`datetime_dt` BETWEEN '" . $datetime_from . "'  AND '" . $datetime_to . "' 
LIMIT 10";

$result = $mysqli->query($query);

if (!$result) {
    die('Select error: ' . $mysqli->error);
}

while ($obj = $result->fetch_object()) {
    $code = generateRandomString(10);
    $query = "SELECT
        `c`.`coupon_id` 
    FROM `jos_vm_coupons` AS `c` 
    WHERE  
        `c`.`coupon_code`='" . $code . "'
    ";

    $result_coupon = $mysqli->query($query);
    if (!$result_coupon) {
        die('Select error: ' . $mysqli->error);
    }

    if ($result_coupon->num_rows == 0) {
        $query = "INSERT INTO `jos_vm_coupons`
        (
            `coupon_code`, 
            `percent_or_total`,
            `coupon_type`,
            `coupon_value`,
            `expiry_date`
        ) 
        VALUES (
            '" . $code . "',
            'percent',
            'gift',
            '20.00',
            DATE_ADD(NOW(), INTERVAL 48 HOUR)
        )
        ";

        if ($mysqli->query($query)) {
            // get the short url

            $link_href_sms = get_our_short_url($obj->link . '&coupon_code=' . $code, 'utm_source=cart-abandonment&utm_medium=SMS&utm_campaign=cart-abandonment');


            if($obj->number) {

            $obj->number = formatMobileNumber($obj->number);
            $query = "SELECT
                `template`
                FROM `jos_sms_templates` 
                WHERE 
                    `template_type`='11' 
                    AND 
                    `recipient_type`='1'
                ";
            $sms_template_result = $mysqli->query($query);
            if ($sms_template_result->num_rows > 0) {
                $sms_template_obj = $sms_template_result->fetch_object();
            }
            if ($sms_template_result) {
                $sms_template_result->close();
            }



            $sms_template_obj->template = str_replace('{AbandonmentFirstName}', $obj->first_name, $sms_template_obj->template);
            $sms_template_obj->template = str_replace('{AbandonmentLink}', 'https://bloomex.com.au/' . $link_href_sms, $sms_template_obj->template);
            $sms_template_obj->template = str_replace('{CouponCode}', $code, $sms_template_obj->template);

            try {
                $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
                $parameters = new SMSParam;
                $parameters->CellNumber = $obj->number;
                $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
                $parameters->MessageBody = $sms_template_obj->template;
                $client->SendMessageExtended($parameters);
                echo json_encode($client);
                echo "<hr>";
            } catch (Exception $e) {
                echo 'error smsgateway';
            }
                $send_time = date('Y-m-d H:i:s');
                $query = "UPDATE `tbl_cart_abandonment`
                SET 
                    `status`='sent',
                    `datetime_dt`='" . $send_time . "'
                WHERE  
                    `id`=" . $obj->id . "
                ";
                $mysqli->query($query);

            }
        } else {
            echo "Error insert coupon ";
        }
    }
    $result_coupon->close();
}
$result->close();



function create_hash($length = 4, $link, $params = '') {
    global $mysqli;
    $letters_array = range('a', 'z');
    $numbers_array = range(0, 9);

    $all_array = array_merge($letters_array, $numbers_array);
    shuffle($all_array);

    $hash = '';

    for ($i = 1; $i <= $length; $i++) {
        $hash .= $all_array[array_rand($all_array)];
    }
    $result = $mysqli->query("SELECT `id` FROM `jos_vm_carts` WHERE `hash` like '" . $hash . "'");

    if (!$result) {
        die('Select error: ' . $mysqli->error);
    }

    if ($result->num_rows == 0) {
        parse_str(ltrim($link, "?"), $url);
        $cart = explode(";", $url['cart_products']);
        foreach ($cart as $k => $v) {
            $item = explode(",", $v);
            $cart1[$k]['product_id'] = $item[0];
            $cart1[$k]['amount'] = $item[1]??1;
        }
        $cart_ready = json_encode($cart1);
        echo $link . "<br>";
        var_dump($url);
        $q = "INSERT INTO `jos_vm_carts` (`hash`, `coupon`,`products`,`get_parameters`, `dateadd`)"
                . " VALUES ('" . $hash . "',"
                . "'" . $url['coupon_code'] . "',"
                . " '" . $cart_ready . "',"
                . " '" . $params . "',"
                . " NOW())";
        echo "$q<hr/>";
        $result = $mysqli->query($q);

        if (!$result) {
            die('Select error: ' . $mysqli->error);
        }
        return $hash;
    } else {
        create_hash($length, $link);
    }
}

function get_our_short_url($link, $params = '') {
    return create_hash(8, $link, $params);
}

function formatMobileNumber($num) {
    if (substr($num, 0, 1) == '+')
        return $num;
    if (substr($num, 0, 2) == '61')
        return "+" . $num;
    if (substr($num, 0, 2) == '04' || substr($num, 0, 2) == '05')
        return "+61" . substr($num, 1);
    return false;
}

function generateRandomString($length = 10) {
    $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



$mysqli->close();
