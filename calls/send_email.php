<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

include "../cron/MAIL5.php";

date_default_timezone_set('America/Toronto');

$email = $mysqli->real_escape_string($_POST['email']);
$cart = $mysqli->real_escape_string($_POST['cart']);
$first_name = $mysqli->real_escape_string($_POST['first_name']);
$return['result'] = false;

if ($email && $cart) {
    $code = generateRandomString(7, 'CS-');
    
    $query_coupon = "INSERT INTO `jos_vm_coupons`
    (
        `coupon_code`, 
        `percent_or_total`,
        `coupon_type`,
        `coupon_value`,
        `expiry_date`
    ) 
    VALUES (
        '".$code."',
        'percent',
        'gift',
        '20.00',
        DATE_ADD(NOW(), INTERVAL 24 HOUR)
    )";
    
    $result_coupon = $mysqli->query($query_coupon);
    if ($result_coupon) {
        $link_href = get_our_short_url($cart.'&coupon_code='.$code);
        
        $subject = $first_name . " your order items and coupon code 20% off!";

        $html_send = '<a href="https://bloomex.com.au?utm_source=email&utm_medium=cart-abandonment&utm_campaign=logo">
        <img src="http://media.bloomex.ca/bloomex.ca/image1.png" style="border-width: 0"></a><br> Dear ' . $obj->first_name . ',<br><br>
        We see you\'ve left items in your cart. To complete your purchase <strong>(and receive an additional 20% discount your coupon code is ' . $code . ' )</strong> simply <a href="' . $link_href . '?utm_source=email&utm_medium=cart-abandonment&utm_campaign=coupon" target="_blank">click on this link </a>- you will be automatically directed to our pre-populated, secure order page to input the additional required fields to complete your purchase and schedule delivery.
        <br> <br> Thank You,
        <br> <br> Bloomex Australia <br>1800 451 637';

        $m = new MAIL5;
        $m->From($mosConfig_mailfrom);
        $addto = $m->AddTo($email);

        if ($addto) {
            $m->Subject($subject);
            $m->Html($html_send);
            $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
            if ($c) {
                if (!$m->Send($c)) {
                    $return['error'] = $m->History;
                    echo "<pre>";
                    var_dump($m->History);
                    print_r($m->History);
                    list($tm1, $ar1) = each($m->History[0]);
                    list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                } else {

                    $return['result'] = true;
                }
                $m->Disconnect();
            } else {
                $return['error'] = 'Wrong connection to email serveice';
            }
        } else {
            $return['error'] = "Wrong email address    " . $email;
        }

    } else {
        $return['error'] = "Error insert coupon ";
    }

}

echo json_encode($return);

$mysqli->close();

function get_our_short_url($link) {
    return create_hash(8, $link);
}

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
            $cart1[$k]['amount'] = $item[1];
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
function generateRandomString($length, $prefix) {
    global $mysqli;
    
    $characters = '2345679ABCDEHJKLMNOPQSUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    $randomString = $prefix.$randomString;
    
    $query = "SELECT 
        *
    FROM `jos_vm_coupons` 
    WHERE `coupon_code` = '".$randomString."'";
    
    $result = $mysqli->query($query);
    
    if (!$result) {
        die('Select error: ' . $mysqli->error);
    }

    if ($result->num_rows == 0) {
        $result->close();
        
        return $randomString;
    }
    else {
        $result->close();
        
        generateRandomString($length);
    }
}