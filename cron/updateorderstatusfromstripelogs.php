<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
$o = 0;
$b = 0;
$m = 0;
include_once '../configuration.php';
include "MAIL5.php";

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
require_once $mosConfig_absolute_path . '/includes/joomla.php';

date_default_timezone_set('Australia/Sydney');
$mysqlDatetime = date('Y-m-d G:i:s');

$datetime_from_to_update_order = date('Y-m-d G:i:s', strtotime('-180 minutes', strtotime(date('Y-m-d G:i:s'))));

$query = "Select id,session_id,bulk_id,order_id,deliver_country from tbl_stripe_orders_adm_logs where order_status = 'pending_stripe' and `date_added` < '" . $datetime_from_to_update_order . "'  ";

$result = $mysqli->query($query);

if (!$result) {
    $mysqli->close();
    die('No result');
}
$records = $result->fetch_all(MYSQLI_ASSOC);

$cancelledOrders = [];
foreach ($records as $obj) {

    try{
        require_once $mosConfig_absolute_path.'/includes/stripe/init.php';
        $stripeSecretKey = ($obj['deliver_country'] == 'NZL')?$mosConfig_nz_stripe_secret_key:$mosConfig_au_stripe_secret_key;
        $stripe = new \Stripe\StripeClient($stripeSecretKey);
        $stripeSession = $stripe->checkout->sessions->retrieve($obj['session_id']);
        $stripe_transaction_id = ($stripeSession->payment_intent??'');
        $stripe_payment_status = ($stripeSession->payment_status??'');
        $mosmsg = 'Payment executed by Stripe Successfully';
        $payment_place = 'Cron Update Status Script';
    }catch (Exception $e){
        continue;
    }
        if($stripe_transaction_id && $stripe_payment_status=='paid'){

            if($obj['order_id']){

                setStripeOrderAsPaid($obj['order_id'],$obj['session_id'],$payment_place,$obj['deliver_country']);
                $o++;
                echo "we update status to paid  for order  ".$obj['order_id']."<br>";
            } elseif ($obj['bulk_id']){

                setStripeBulkOrdersAsPaid($obj['bulk_id'],$obj['session_id'],$payment_place);
                $b++;
                echo "we update status to paid  for bulk order  ".$obj['bulk_id']."<br>";
            }
        } else {
            if($obj['order_id']){

                $query = "SELECT 
                  user_email
                FROM  jos_vm_order_user_info 
                WHERE 
                  address_type = 'BT' 
                  and order_id = ".$obj['order_id'];
                $result = $mysqli->query($query);
                $orderInfo = $result->fetch_object();



                $query = "UPDATE `tbl_stripe_orders_adm_logs`
                SET 
                    `order_status`='canceled'
                WHERE  
                    id = '".$obj['id']."'";

                $mysqli->query($query);


                $orderStatus = 'X';

                $query = "UPDATE `jos_vm_orders` SET `order_status`='{$orderStatus}' WHERE `order_id`=" . $obj['order_id'];
                $result = $mysqli->query($query);
                if (!$result) {
                    $mysqli->close();
                    die('Error save jos_vm_order_history');
                }

                $historyQuery = "INSERT INTO `jos_vm_order_history` (
                        `order_id`,
                        `order_status_code`,
                        `date_added`,
                        `customer_notified`,
                        `comments`,
                        `user_name`
                    ) VALUES (
                        '{$obj['order_id']}',
                        '{$orderStatus}',
                        '{$mysqlDatetime}',
                         0,
                        'Сancelled by cron because no payment was received from Stripe.',
                        'Check payment cron'
                    )";

                $result = $mysqli->query($historyQuery);
                if (!$result) {
                    $mysqli->close();
                    die('Error save jos_vm_order_history');
                }
                $m++;
                echo "we update status to cancelled  for order  ".$obj['order_id']."<br>";

                if($orderInfo->user_email){

                    $mail_Subject = "Not Paid Order #" . $obj['order_id'];
                    $mail_Content = str_replace('{order_id}', $obj['order_id'],
                        " <p>Hello! Thank you for your business with Bloomex.<br />
                        It seems like your order was not paid, therefore it has been cancelled.<br />
                        However, if you would still like to proceed with the delivery, please reach out to our Customer Service Team, referencing the same order number, and we will be happy to assist you in completing the order ({order_id}).<br />
                        Thank you for your understanding.<br />
                        Best regards,<br />
                        Bloomex</p>
                        
                        <p>--------<br />
                        Thank you</p>");


                    $m = new MAIL5;
                    $m->From($mosConfig_mailfrom_noreply, 'Bloomex Australia');
                    $m->AddTo($orderInfo->user_email);
                    $m->Subject($mail_Subject);
                    $m->Html($mail_Content);

                    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);

                    if ($m->Send($c)) {
                        echo '<br/>[' .  $obj['order_id'] . '] Mail sent to ' . $orderInfo->user_email."<br>";
                    } else {
                        '<br /><pre>';
                        print_r($m->History);
                        list($tm1, $ar1) = each($m->History[0]);
                        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                        echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
                    }


                }
            }
            if($stripeSession->status !== 'expired') {
                $stripe->checkout->sessions->expire($obj['session_id']);
            }

        }

}
$result->close();
$mysqli->close();

if ($o) {
    echo "we update status for $o order(s) <br>";
}elseif($b){
    echo "we update status for $b bulk order(s) <br>";
}else{
    echo "no valid payments to update order";
}
