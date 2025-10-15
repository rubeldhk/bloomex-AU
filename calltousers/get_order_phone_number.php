<?php
class Orders_Numbers {

    var $link = '';
    var $day_count = '';
    var $day_count_strtotime = '';
    var $day_count_strtotime_minus = '';
    var $day_1_year_ago = '';
    var $day_1_year_ago_day_count = '';
    var $day_2_year_ago = '';
    var $day_2_year_ago_day_count = '';
    var $day_3_year_ago = '';
    var $day_3_year_ago_day_count = '';
    var $ext = '';
    var $user = '';
    var $password = '';

    function __construct() {
        include "../configuration.php";
        $this->link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
        if (!$this->link) {
            die('Could not connect: ' . mysql_error());
        }
        if (!mysql_select_db($mosConfig_db)) {
            die('Could not select database: ' . mysql_error());
        }
        $this->day_count = 3;

        include "extensions.php";


        $this->user = $user_pass[0];
        $this->password = $user_pass[1];
    }

    function get_abandonment_numbers() {
        $datetime_from = strtotime("-20 minutes", time());
        $datetime_to = strtotime("-1 hours", time());
        $query_numbers = "SELECT 
                        a.id,number,user_id,link,first_name  
                        FROM  `tbl_cart_abandonment` a
                        LEFT JOIN `tbl_cart_abortment_phone_system` ap on ap.id=a.id
                        WHERE  (( status  = 'abandonment' ) OR ( status  = 'sent' )) AND  call_customer != 'D'  AND  number  != ''  AND
                        `date` < " . $datetime_from . "  AND `date` > " . $datetime_to . " AND  ap.id is NULL limit 0,50";

        $result_numbers = mysql_query($query_numbers, $this->link);
        if (!$result_numbers) {
            die('Select error: ' . mysql_error());
        }
      
        $arr = array();

        if (mysql_affected_rows() != 0) {
            $q = "INSERT INTO tbl_cart_abortment_phone_system (id,time) VALUES  ";
            while ($row_numbers = mysql_fetch_assoc($result_numbers)) {
                $row_numbers['number'] = preg_replace("/[^0-9]/", "", trim($row_numbers['number']));
                $check = false;
        if (strlen($row_numbers['number']) > 8 && strlen($row_numbers['number']) < 11) {
                    $check = true;
                }
                if ($check) {
                    $arr[] = array($row_numbers['user_id'], $row_numbers['number'], $row_numbers['first_name']);
                    $check = false;
                }
                $q.='(' .$row_numbers['id'] . ', NOW() ),';
            }
            $q = rtrim($q, ',');
            $result = mysql_query($q, $this->link);
            if (!$result) {
                die('updating phone numbers error: ' . mysql_error());
            }
        }
        return json_encode($arr);
    }

    function get_order_numbers() {

        $query_numbers = "SELECT 
                        o.order_id as id,
                        ou.country as country,
                        ou.phone_1 as phone
                        FROM  `jos_vm_orders` as o
                        left join jos_vm_order_user_info as ou on ou.order_id = o.order_id
                        WHERE   ( o.`customer_occasion` LIKE  'BIRTH' OR o.`customer_occasion` LIKE  'ANNIV') AND ou.call_customer='L' AND  ou.address_type = 'BT' AND country = 'CAN' 
                        AND  DATE_FORMAT( FROM_UNIXTIME(o.cdate) ,  '%e-%b' ) LIKE DATE_FORMAT( DATE_ADD( NOW( ) , INTERVAL " . $this->day_count . " DAY ) ,  '%e-%b' )
                        Group by phone ";
        $result_numbers = mysql_query($query_numbers, $this->link);
        if (!$result_numbers) {
            die('Select error: ' . mysql_error());
        }

        if (mysql_affected_rows() != 0) {
            $arr = array();
            while ($row_numbers = mysql_fetch_assoc($result_numbers)) {

                $row_numbers['phone'] = preg_replace("/[^0-9]/", "", trim($row_numbers['phone']));
                $check = false;
                if (strlen($row_numbers['phone']) == 11)
                    $check = true;
                if (strlen($row_numbers['phone']) == 10) {
                    $check = true;
                    $row_numbers['phone'] = "1" . $row_numbers['phone'];
                }
                if ($check) {
                    $check = false;
                    $arr[] = array($row_numbers['id'], $row_numbers['phone'], $row_numbers['country']);
                }
            }
            echo json_encode($arr);
        }
    }

    function loadAjaxOrder() {
        $order_id = $_POST['order_id'];

        $query_bill = "SELECT u.first_name,u.last_name,u.user_email,ui.first_name as recipient_name,o.order_total,oc.order_occasion_name,o.ddate,o.customer_note,o.customer_comments
            FROM  `jos_vm_order_user_info`  as u
left join `jos_vm_orders` as o on o.order_id=u.order_id
left join `jos_vm_order_user_info` as ui on ui.order_id=u.order_id
left join `jos_vm_order_occasion` as oc on oc.order_occasion_code = o.customer_occasion
            WHERE u.`address_type` = 'BT' AND ui.`address_type` = 'ST' AND u.`order_id` =" . $order_id;

        $order_user_res_bill = mysql_query($query_bill, $this->link);
        if (!$order_user_res_bill) {
            die('Select error: ' . mysql_error());
        }

        if (mysql_affected_rows() != 0) {
            $row_user_details_bill = mysql_fetch_assoc($order_user_res_bill);
        }
        $query_order_details = "SELECT * 
FROM  `jos_vm_order_item` 
WHERE  `order_id` =" . $order_id;

        $order_details = mysql_query($query_order_details, $this->link);
        if (!$order_details) {
            die('Select error: ' . mysql_error());
        }

        if (mysql_affected_rows() != 0) {
            $arr = array();
            while ($row_order_details = mysql_fetch_assoc($order_details)) {
                $arr[] = $row_order_details;
            }
        }

        $user_details = $row_user_details_bill ? $row_user_details_bill : '';
        $order_details = $arr ? $arr : '';
        $res = array('user_details' => $user_details, 'order_details' => $order_details);
        echo json_encode($res);
    }

    function httpGet() {
        $this->ext = $_POST['ext'];
        $url = "http://" . $this->user . ":" . $this->password . "@sip2.bloomex.ca:8080/paneltest/callfile/callfile_data_bloomex.php?ext=" . $this->ext . "";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($data, true);

        if (!empty($result))
            echo $data;
        else
            echo 'NO';
    }

    function loadAbandonment() {
        $user_id = $_POST['user_id'];

        $query = "SELECT * 
FROM  `tbl_cart_abandonment` 
WHERE  `user_id` ='" . $user_id . "'  
AND  `status` LIKE  'abandonment' OR `status` LIKE  'sent' ORDER BY  `date` DESC ";

        $user_res = mysql_query($query, $this->link);
        if (!$user_res) {
            echo $query;
            die('Select error: ' . mysql_error());
        }

        if (mysql_affected_rows() != 0) {
            $row = mysql_fetch_assoc($user_res);
            if ($row['link'] != '') {
                $link = $row['link'];
                $link = substr(trim($link), 15);
                $products = explode(';', $link);
                $product_ies = '';
                $i = count($products);
                foreach ($products as $p) {
                    $i--;
                    $p = explode(',', $p);
                    if ($i == 0)
                        $product_ies .= $p[0];
                    else
                        $product_ies .= $p[0] . ',';
                }
            }
        }


        $query_user_details = "SELECT * 
FROM  `jos_vm_user_info` 
WHERE  `user_id` ='" . $user_id . "'
AND  `address_type` LIKE  'BT'";

        $user_details = mysql_query($query_user_details, $this->link);
        if (!$user_details) {
            echo $query_user_details;
            die('Select error: ' . mysql_error());
        }
        $row_user_details = '';
        if (mysql_affected_rows() != 0) {

            $row_user_details = mysql_fetch_assoc($user_details);
        }
        $abandonment_product_list = array();
        if ($product_ies != '') {

            $query_abandonment_products = "SELECT product_name,product_sku 
FROM  `jos_vm_product` 
WHERE  `product_id` in (" . $product_ies . ")";

            $abandonment_products = mysql_query($query_abandonment_products, $this->link);
            if (!$abandonment_products) {
                echo $query_abandonment_products;
                die('Select error: ' . mysql_error());
            }

            if (mysql_affected_rows() != 0) {
                $i = 0;
                while ($row_abandonment_products = mysql_fetch_assoc($abandonment_products)) {
                    $abandonment_product_list[$i]['sku'] = $row_abandonment_products['product_sku'];
                    $abandonment_product_list[$i]['name'] = $row_abandonment_products['product_name'];
                    $i++;
                };
            }
        }

        $res = array('abandonment_product_list' => $abandonment_product_list, 'user_details' => $row_user_details, 'abandonment' => $row);
        echo json_encode($res);
    }

    function late_call() {
        $order_id = $_POST['order_id'];
        $user_id = $_POST['user_id'];
        $sql = "UPDATE jos_vm_order_user_info SET call_customer='L' WHERE order_id='" . $order_id . "' ";
        $sql_aban = "UPDATE tbl_cart_abandonment SET call_customer='L' WHERE   user_id='" . $user_id . "'";

        $sql_res = mysql_query($sql, $this->link);
        if (!$sql_res) {
            die('Select error: ' . mysql_error());
        }
        $sql_res_aban = mysql_query($sql_aban, $this->link);
        if (!$sql_res_aban) {
            die('Select error: ' . mysql_error());
        }
        return true;
    }

    function done_call() {
        $order_id = $_POST['order_id'];
        $user_id = $_POST['user_id'];
        $sql = "UPDATE jos_vm_order_user_info SET call_customer='D' WHERE order_id='" . $order_id . "' ";
        $sql_aban = "UPDATE tbl_cart_abandonment SET call_customer='D' WHERE   user_id='" . $user_id . "'";
        $sql_res = mysql_query($sql, $this->link);
        if (!$sql_res) {
            die('Select error: ' . mysql_error());
        }
        $sql_res_aban = mysql_query($sql_aban, $this->link);
        if (!$sql_res_aban) {
            die('Select error: ' . mysql_error());
        }
        return true;
    }

    function check_new_call() {

        $this->ext = $_POST['ext'];
        $url = "http://" . $this->user . ":" . $this->password . "@sip2.bloomex.ca:8080/paneltest/callfile/callfile_data_bloomex.php?ext=" . $this->ext . "";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($data, true);
        if ($_POST['phone_number'] == $result[1])
            echo 'NO';
        else
            echo "YES";
    }

}

$numbers_class = new Orders_Numbers;

if ($_GET['action'] == 'abandonment')
    die($numbers_class->get_abandonment_numbers());
if ($_GET['test'] == 'abandonment')
    die($numbers_class->loadAbandonment());



$result = '';
if ($_POST['post_name']) {


    switch ($_POST['post_name']) {

        case 'numbers':

            $result = $numbers_class->get_order_numbers();

            break;
        case 'curl':

            $result = $numbers_class->httpGet();

            break;
        case 'check':

            $result = $numbers_class->check_new_call();

            break;
        case 'late_call':

            $result = $numbers_class->late_call();

            break;

        case 'done_call':

            $result = $numbers_class->done_call();

            break;
        case 'details':

            $result = $numbers_class->loadAjaxOrder();
            break;
        case 'abandonment':

            $result = $numbers_class->loadAbandonment();

            break;
        default:
            $result = $numbers_class->get_order_numbers();
    }
} else {
    $result = $numbers_class->get_order_numbers();
}
return $result;
