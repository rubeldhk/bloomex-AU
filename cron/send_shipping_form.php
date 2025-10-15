<?php

class SMSParam {

    public $CellNumber;
    public $AccountKey;
    public $MessageBody;

}

$time = time();

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);


date_default_timezone_set('Australia/Sydney');

$datetime = date('Y-m-d G:i:s');
$datetime_from = date('Y-m-d G:i:s', strtotime('-30 minutes', strtotime(date('Y-m-d G:i:s'))));
$datetime_to = date('Y-m-d G:i:s', strtotime('-8 minutes', strtotime(date('Y-m-d G:i:s'))));

$query = "SELECT 
    `a`.*
FROM tbl_cart_abandonment AS `a`
WHERE  
    `a`.`status`='wait_delivery_address'
    AND 
    `a`.`datetime_dt` BETWEEN '" . $datetime_from . "' AND '" . $datetime_to . "'
LIMIT 25";

$result = $mysqli->query($query);

echo $query;
if (!$result) {
    die('Select error: ' . $mysqli->error);
}

while ($obj = $result->fetch_object()) {
    var_dump($obj);
    if($obj->user_info_id) {
        $cache = str_rot13($obj->order_id . ';' . ($obj->user_info_id??''));
        $link_href = 'checkout/2/' . $cache;
    }else{
        $from_string = array("{order_id}", "{user_id}");
        $to_string   = array($obj->order_id, $obj->user_id);
        $cache = str_replace($from_string, $to_string, $mosConfig_fast_checkout_salt);
        $link_href = 'fast-checkout-shipping-form/' . $cache;
    }

    $query = "SELECT
                `email_subject`, 
                `email_html` 
                FROM `jos_vm_emails` 
                WHERE 
                    `email_type`='12' 
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

    $confirmation_obj->email_html = str_replace('{ShippingFormLink}', 'https://bloomex.com.au/' . $link_href, $confirmation_obj->email_html);
    $confirmation_obj->email_html = str_replace('{phpShopBTName}', $obj->first_name, $confirmation_obj->email_html);
    $confirmation_obj->email_subject = str_replace('{phpShopBTName}', $obj->first_name, $confirmation_obj->email_subject);

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
        $m->From($mosConfig_mailfrom_noreply, 'Bloomex Australia');

        $addto = $m->AddTo($email_to);

        if ($addto) {
            $m->Subject($confirmation_obj->email_subject);
            echo "<hr>1";
            var_dump($confirmation_obj->email_subject);
            echo "<hr>2";
            $m->Html($confirmation_obj->email_html);
            $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
            if ($c) {
                if (!$m->Send($c)) {

                    echo "<pre>";
                    var_dump($m->History);
                    print_r($m->History);
                    list($tm1, $ar1) = each($m->History[0]);
                    list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                } else {
                    echo $confirmation_obj->email_html;
                    echo 'Mail sent to  ' . $email_to . "<hr>";
                }
                $m->Disconnect();
            }
        } else {
            echo "Wrong email address    " . $email_to;
        }

        $query = "UPDATE `tbl_cart_abandonment`
                SET 
                    `status`='sent_shipping_form',
                    `datetime_dt`='" . $datetime . "'
                WHERE  
                    `id`=" . $obj->id . "
                ";
        $mysqli->query($query);
        $query = "INSERT INTO `jos_vm_order_history` (`order_id`,`order_status_code`,`comments`,`date_added`,`user_name`) VALUES (" . $obj->order_id . ",'PD','sent shipping form request','$datetime','Cron')";
        $mysqli->query($query);
    }

    $obj->number = formatMobileNumber($obj->number);
    if ($obj->number) {
        $query = "SELECT
                `template` 
                FROM `jos_sms_templates` 
                WHERE 
                    `template_type`='12' 
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


        $sms_template_obj->template = str_replace('{phpShopBTName}', $obj->first_name, $sms_template_obj->template);
        $sms_template_obj->template = str_replace('{ShippingFormLink}', 'https://bloomex.com.au/fill/', $sms_template_obj->template);



        $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
        $parameters = new SMSParam;
        $parameters->CellNumber = $obj->number;
        $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
        $parameters->MessageBody = $sms_template_obj->template;
        $a = $client->SendMessageExtended($parameters);
    }
}
$result->close();
$mysqli->close();

function formatMobileNumber($num) {
    if (substr($num, 0, 1) == '+')
        return $num;
    if (substr($num, 0, 2) == '61')
        return "+" . $num;
    if (substr($num, 0, 2) == '04' || substr($num, 0, 2) == '05')
        return "+61" . substr($num, 1);
    return false;
}
