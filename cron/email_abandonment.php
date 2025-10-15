<?php



$time = time();
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/administrator/components/com_virtuemart/classes/ps_comemails.php';


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
            $link_href = get_our_short_url($obj->link . '&coupon_code=' . $code, 'utm_source=cart-abandonment&utm_medium=Email&utm_campaign=cart-abandonment');
            $link_href_sms = get_our_short_url($obj->link . '&coupon_code=' . $code, 'utm_source=cart-abandonment&utm_medium=SMS&utm_campaign=cart-abandonment');
            $query = "SELECT
                `email_subject`, 
                `email_html` 
                FROM `jos_vm_emails` 
                WHERE 
                    `email_type`='11' 
                    AND 
                    `recipient_type`='1'
                ";

            $confirmation_result = $mysqli->query($query);


            if ($confirmation_result->num_rows > 0) {
                $confirmation_obj = $confirmation_result->fetch_object();
            }
            if ($confirmation_result) {
                $confirmation_result->close();
            }



            $products_html = getAbandonmentItemsInfo($obj->link);
            $confirmation_obj->email_html = str_replace('{AbandonmentItems}', $products_html, $confirmation_obj->email_html);
            $confirmation_obj->email_html = str_replace('{CouponCode}', $code, $confirmation_obj->email_html);
            $confirmation_obj->email_html = str_replace('{AbandonmentLink}', 'https://bloomex.com.au/cart/' . $link_href, $confirmation_obj->email_html);
            $confirmation_obj->email_html = str_replace('{AbandonmentFirstName}', $obj->first_name, $confirmation_obj->email_html);
            $confirmation_obj->email_subject = str_replace('{AbandonmentFirstName}', $obj->first_name, $confirmation_obj->email_subject);

            // get email to send
            $query = "SELECT
                `ui`.`user_email` 
            FROM `jos_vm_user_info` AS `ui`  
            WHERE  
                `ui`.`user_id`=" . $obj->user_id . " 
                AND 
                `ui`.`address_type`='BT' 
            ";

            $result_email = $mysqli->query($query);
            if ($result_email) {

                while ($obj_email = $result_email->fetch_object()) {
                    $email_to = $obj_email->user_email;
                }

                $m = new MAIL5;
                $m->context(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
                $m->From($mosConfig_mailfrom_personal, 'Bloomex Australia');
                $addto = $m->AddTo($email_to);
                if ($addto) {
                    $m->Addbcc('aleksandr@bloomex.ca');
                    $m->Subject($confirmation_obj->email_subject);
                    echo "<hr>1";
                    var_dump($confirmation_obj->email_subject);
                    echo "<hr>2";
                    $m->Html($confirmation_obj->email_html);
                    $c = $m->Connect($abandonment_smtphost, (int) $abandonment_smtpport, $abandonment_smtpuser, $abandonment_smtppass, $abandonment_smtpprotocol, 20);
                    if ($c) {
                        if (!$m->Send($c)) {

                            echo "<pre>";
                            var_dump($m->History);
                            print_r($m->History);
                            list($tm1, $ar1) = each($m->History[0]);
                            list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                        } else {

                            echo 'Mail sent to  ' . $email_to . "<br>";
                        }
                        $m->Disconnect();
                    }
                } else {
                    echo "Wrong email address    " . $email_to;
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

/* returns a result form url */

function curl_get_result($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
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

function getAbandonmentItemsInfo($products) {
    global $mysqli;
    $products = str_replace('?cart_products=', '', $products);

    $pieces = explode(";", $products);
    $quantities = array();
    $products_id = '(';
    if ($pieces) {
        foreach ($pieces as $p) {
            $p_id = explode(",", $p);
            $products_id .= $p_id[0] . ',';
            $quantities[$p_id[0]] = $p_id[1]??1;
        }
    }
    $products_id = rtrim($products_id, ',');
    $products_id .= ')';


    $query = "SELECT 
                `c`.`category_id`,
            `p`.`product_id`, 
            `p`.`product_name`, 
            `p`.`product_sku`, 
            `p`.`product_thumb_image`, 
            `p`.`alias`, 
            `pp`.`product_price`,
                (`pp`.`product_price`-`pp`.`saving_price`) AS `product_real_price`,
            `c`.`category_id`, 
            `c`.`alias` AS 'category_alias'  
        FROM `jos_vm_product` AS `p`
            LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
            WHERE `p`.`product_id` IN " . $products_id . "
            GROUP BY `p`.`product_sku` ORDER BY product_real_price ASC";

    $result_products = $mysqli->query($query);
    $items = '';
    if ($result_products) {
        while ($item_obj = $result_products->fetch_object()) {
            $canonical = getCanonicalCategory($item_obj->category_alias);
            $url = $canonical . $item_obj->alias . '/';

            $items .= '<tr><td><table cellpadding="0" cellspacing="0" style="border-radius:4px; width: 500px">
							<tbody>
								<tr>
									<td align="center" style="width: 250px; padding-top: 10px; padding-bottom: 10px;" width="250"><a href="' . $url . '?utm_source=email&utm_medium=cart-abandonment&utm_campaign=product" target="_blank"><img alt="' . $item_obj->product_name . '" src="https://bloomex.com.au/components/com_virtuemart/shop_image/product/' . $item_obj->product_thumb_image . '" style="border-width: 0" width="200"></a></td>
									<td style="width: 250px; vertical-align: top;" width="250">
									<table align="left" cellpadding="0" cellspacing="0" style="width: 250px">
										<tbody>
											<tr>
												<td align="left" style="padding: 25px 15px 15px 15px; text-align: left; font-size: 14px; line-height: 18px;"><multiline> <a href="' . $url . '?utm_source=email&utm_medium=cart-abandonment&utm_campaign=product" style="text-decoration: none; color: #808080; font-family: Arial, Helvetica, sans-serif;" target="_blank">' . $item_obj->product_name . '</a></multiline></td>
											</tr>
											<tr>
												<td align="left" style="font-family: Arial, Helvetica, sans-serif; font-size: 24px; line-height:30px; letter-spacing:1px; color: #333333; font-weight:400; padding-bottom: 10px; text-align: left; padding-left: 15px;"><multiline> <strong> <span style="font-size: 26px; color: #4B4B4B;">$' . number_format($item_obj->product_real_price, 2, '.', ' ') . '</span></strong> <span style="color: #FF5050; text-decoration: line-through; font-size: 22px;">$' . number_format($item_obj->product_price, 2, '.', ' ') . '</span></multiline></td>
											</tr>
											<tr>
												<td align="left" height="50" style="padding-bottom: 15px; padding-left: 15px;">
												<table align="left" cellpadding="0" cellspacing="0" style="width:120px; text-align:center;" width="120px">
													<tbody>
														<tr>
															<td style="background-color:#c90411; border-radius:18px; height:36px;" width="120"><a href="' . $url . '?utm_source=email&utm_medium=cart-abandonment&utm_campaign=product" style="font-size:12px;text-decoration:none;font-family:Montserrat, Arial, sans-serif;text-align:center;color:#FFFFFF;display:block;line-height:36px;" target="_blank">ORDER NOW</a></td>
														</tr>
													</tbody>
												</table>
												</td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td></tr>';
        }
    }

    return $items;
}

function getCanonicalCategory($alias, $relative = false) {

    global $mosConfig_live_site, $mysqli;

    $aliases = [];

    $category_parent_id = 1;

    $i = 1;
    while ($category_parent_id > 0) {
        $query = "SELECT
                `c`.`category_id`,
                `c`.`alias`,
                `c2`.`alias` AS `parent_alias`,
                `c_x`.`category_parent_id`
            FROM `jos_vm_category` AS `c`
            LEFT JOIN `jos_vm_category_xref` AS `c_x`
                ON `c_x`.`category_child_id`=`c`.`category_id`
            LEFT JOIN `jos_vm_category` AS `c2`
                ON `c2`.`category_id`=`c_x`.`category_parent_id`
            WHERE
                `c`.`alias`='" . $alias . "'
                AND  
                `c`.`category_publish`='Y'
            ";

        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $category_obj = $result->fetch_object();
            $alias = $category_obj->parent_alias;
            $aliases[] = $category_obj->alias;
            $category_parent_id = $category_obj->category_parent_id;
        } else {
            $category_parent_id = 0;
        }

        $i++;
    }

    return 'https://bloomex.com.au/' . (sizeof($aliases) > 0 ? '/' . implode('/', array_reverse($aliases)) . '/' : '');
}

$mysqli->close();
