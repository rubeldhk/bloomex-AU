<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
$i = 0;
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
require_once $mosConfig_absolute_path . '/includes/joomla.php';
date_default_timezone_set('Australia/Sydney');
$datetime = date('Y-m-d G:i:s');

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$datetime_from_to_cancel = date('Y-m-d G:i:s', strtotime('-180 minutes', strtotime(date('Y-m-d G:i:s'))));
$datetime_from_to_create_order = date('Y-m-d G:i:s', strtotime('-10 minutes', strtotime(date('Y-m-d G:i:s'))));


$query = "Select id,order_data,user_id from tbl_stripe_orders_logs where order_status = 'pending_stripe' and order_id is null and `date_added` < '" . $datetime_from_to_create_order . "' ORDER BY `date_added` DESC ";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('No result');
}

while ($obj = $result->fetch_object()) {

    $sessionData = unserialize($obj->order_data);

    $stripeSessionId = $sessionData['checkout_ajax']['stripeSessionId']??'';

    if(!$stripeSessionId){

        $query = "UPDATE `tbl_stripe_orders_logs`
                SET 
                    `order_status`='canceled'
                WHERE  
                    `date_added` < '" . $datetime_from_to_cancel . "' and order_id is null and id = ".$obj->id;
        $mysqli->query($query);

        continue;
    }

    include_once $mosConfig_absolute_path.'/administrator/components/com_virtuemart/classes/ps_for_checkout.php';
    $ps_for_checkout = new ps_for_checkout;

    require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
    $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);
    $stripeSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
    $stripe_transaction_id = ($stripeSession->payment_intent??'');
    $stripe_payment_status = ($stripeSession->payment_status??'');
    $mosmsg = 'Payment executed by Stripe Successfully';


    if($stripe_transaction_id && $stripe_payment_status=='paid'){
        $i++;
        $stripeResponse = [
            $stripe_transaction_id,
            $mosmsg,
            $stripeSession->customer_details->email,
            $stripeSession->customer_details->name,
            $stripeSession->customer_details->phone,
            $stripeSession->customer_details->address->country,
            $stripeSession->customer_details->address->postal_code
        ];

        if($obj->user_id) {
            $ps_for_checkout->SetOrder($stripeResponse,false,$obj->id,$sessionData);
        }else{
            $ps_for_checkout->createFastOrder($stripeResponse,$obj->id,$sessionData);
        }

    } else {

        $query = "UPDATE `tbl_stripe_orders_logs`
                SET 
                    `order_status`='canceled'
                WHERE  
                    `date_added` < '" . $datetime_from_to_cancel . "' and order_id is null and id = ".$obj->id;
        $mysqli->query($query);
    }

}
$result->close();
$mysqli->close();

if ($i) {
    echo "we create $i new order(s) <br>";
}else{
    echo "no valid payments to create new order";
}
