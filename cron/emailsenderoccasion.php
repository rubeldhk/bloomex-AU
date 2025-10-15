<?php
$time = time();
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$query = "SELECT 
    *
FROM `tbl_occasion_email`
WHERE published='1'
";

$result = $mysqli->query($query);

if (!$result) {
    die('Select error: '.$mysqli->error);
}

while ($obj = $result->fetch_object()) {
    $day_count = $obj->day_count;
    $day_count_strtotime = strtotime("+" . $day_count . " day", time());
    $day_count_strtotime_minus = strtotime("-" . $day_count . " day", time());
    $now = time();
    $day_1_year_ago = strtotime("-1 year ", time());
    $day_1_year_ago_day_count = strtotime("-1 year +" . $day_count . " day", time());
    $day_2_year_ago = strtotime("-2 year", time());
    $day_2_year_ago_day_count = strtotime("-2 year +" . $day_count . " day", time());
    $day_3_year_ago = strtotime("-3 year", time());
    $day_3_year_ago_day_count = strtotime("-3 year +" . $day_count . " day", time());

    $query_emails = "SELECT  
        `ou`.`user_id`,
        `o`.`order_total` AS 'order_total',
        `o`.`order_id`,
        `ou`.`user_email` AS 'sender_email',
        `ou`.`first_name` AS 'sender_name',
        `ou`.`last_name` AS 'sender_last_name',
        `o`.`cdate` AS 'date_of_purchase',
        `o`.`ddate`,
        `o`.`customer_occasion`,
        `ou`.`phone_1` AS 'sender_phone',
        `ou`.`state` AS 'sender_province',
        `ouship`.`first_name` AS 'recipient_name',
        `ouship`.`last_name` AS 'recipient_last_name'
    FROM `jos_vm_orders` AS `o`
    LEFT JOIN `jos_vm_order_user_info` AS `ou` 
        ON
            `ou`.`order_id`=`o`.`order_id`
    LEFT JOIN `jos_vm_order_user_info` AS `ouship` 
        ON 
            `ouship`.`order_id`=`ou`.`order_id`
    LEFT JOIN `tbl_unsubscribe_comments` AS `com` 
        ON 
            `com`.`email`=`ou`.`user_email`
    LEFT JOIN `tbl_occasion_sent_emails` AS `sent_emails` 
        ON 
            `sent_emails`.`email`=`ou`.`user_email` 
            AND 
            `sent_emails`.`text_id`='".$obj->id."'
    WHERE 
        (`o`.`order_total` BETWEEN ".$obj->first_price." AND ".$obj->last_price.")
        AND 
        `com`.`id` IS NULL
        AND 
        `sent_emails`.`id` IS NULL
        AND
        `o`.`customer_occasion` LIKE '".$obj->occasion."'
        AND 
        `ou`.`address_type`='BT'
        AND 
        `ou`.`user_email`!='' 
        AND 
        `ouship`.`address_type`='ST'
        AND 
        ((
            `o`.`cdate`<UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) 
            AND
            `o`.`cdate`>UNIX_TIMESTAMP(DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), INTERVAL $day_count DAY)))
        OR 
        (
            `o`.`cdate`<UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 2 YEAR)) 
            AND
            `o`.`cdate`>UNIX_TIMESTAMP(DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 2 YEAR), INTERVAL $day_count DAY)))
        OR 
        (
            `o`.`cdate`<UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 YEAR)) 
            AND
            `o`.`cdate`>UNIX_TIMESTAMP(DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 3 YEAR), INTERVAL $day_count DAY)))
        OR 
        (
            `o`.`cdate`<UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 4 YEAR)) 
            AND
            `o`.`cdate`>UNIX_TIMESTAMP(DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 4 YEAR), INTERVAL $day_count DAY)))
        OR 
        (
            `o`.`cdate`<UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 5 YEAR)) 
            AND
            `o`.`cdate`>UNIX_TIMESTAMP(DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 5 YEAR), INTERVAL $day_count DAY)))
        )
    GROUP BY `ou`.`user_email`
    ";
    
    $result_emails = $mysqli->query($query_emails);

    if (!$result_emails) {
        die('Select error: '.$mysqli->error);
    }

    if ($result_emails->num_rows != 0) {
        echo '<br/><br/><strong> Mail with subject </strong>  ('.$obj->subject.') <br/> ';
        $i = 0;
        
        while ($email_obj = $result_emails->fetch_object()) {
            $i ++;
            $user_id = $email_obj->user_id;
            $user_id_sec = md5($email_obj->user_id.'BLOOMEX');
            $user_email = $email_obj->sender_email;
            $action = $mosConfig_live_site . "/unsubscribe.php";
            $href = "<a style=' background-color: red;padding: 10px;border-radius: 10px;cursor: pointer;color: white;text-decoration: none;font-weight: bold;' href='" . $mosConfig_live_site . "/unsubscribe.php?user_id=".$email_obj->user_id."&user=" . $user_id_sec . "&user_email=" . $user_email . "'>unsubscribe</a>";

            $query_province = "SELECT 
                `state_name`
            FROM `jos_vm_state`
            WHERE 
                `state_2_code`='".$email_obj->sender_province."'
                AND 
                `country_id`=38
            ";
            
            $result_province = $mysqli->query($query_province);

            if (!$result_province) {
                die('Select error: '.$mysqli->error);
            }
            
            if ($result_province->num_rows != 0) {
                $province_obj = $result_province->fetch_object();
                $province = $province_obj->state_name;
            } 
            else {
                $province = $email_obj->sender_province;
            }
            $result_province->close();

            $html_send = str_replace('{sender_name}', (isset($email_obj->sender_name) ? $email_obj->sender_name : ""), $obj->text);
            $html_send = str_replace('{sender_last_name}', (isset($email_obj->sender_last_name) ? $email_obj->sender_last_name : ""), $html_send);
            $html_send = str_replace('{user_email}', (isset($email_obj->sender_email) ? $email_obj->sender_email : ""), $html_send);
            $html_send = str_replace('{user_id}', (isset($email_obj->user_id) ? $email_obj->user_id : ""), $html_send);
            $html_send = str_replace('{order_id}', (isset($email_obj->order_id) ? $email_obj->order_id : ""), $html_send);
            $html_send = str_replace('{order_total}', (isset($email_obj->order_total) ? $email_obj->order_total : ""), $html_send);
            $html_send = str_replace('{date_of_purchase}', (isset($email_obj->date_of_purchase) ? $email_obj->date_of_purchase : ""), $html_send);
            $html_send = str_replace('{ddate}', (isset($email_obj->ddate) ? $email_obj->ddate : ""), $html_send);
            $html_send = str_replace('{customer_occasion}', (isset($email_obj->customer_occasion) ? $email_obj->customer_occasion : ""), $html_send);
            $html_send = str_replace('{sender_phone}', (isset($email_obj->sender_phone) ? $email_obj->sender_phone : ""), $html_send);
            $html_send = str_replace('{sender_province}', (isset($province) ? $province : ""), $html_send);
            $html_send = str_replace('{recipient_name}', (isset($email_obj->recipient_name) ? $email_obj->recipient_name : ""), $html_send);
            $html_send = str_replace('{recipient_last_name}', (isset($email_obj->recipient_last_name) ? $email_obj->recipient_last_name : ""), $html_send);
            $html_send = str_replace('{sender_email}', (isset($email_obj->sender_email) ? $email_obj->sender_email : ""), $html_send);
            $html_send = str_replace('{unsubscribe}', $href, $html_send);
            $subject = $row['subject'];
            $subject = str_replace('{recipient_name}', (isset($email_obj->recipient_name) ? $email_obj->recipient_name : ""), $subject);

            $m = new MAIL5;
            $m->From($mosConfig_mailfrom);
            $m->AddTo($email_obj->sender_email);
            $m->Subject($subject);
            $m->Html($html_send);
            $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);

            if (!$c) {
                var_dump($m->History);
                die(print_r($m->Result));
            }
            if ($m->Send($c)) {
                $now = time();
                $query = "INSERT INTO `tbl_occasion_sent_emails`
                (
                    `email`,
                    `sent_date`,
                    `text_id`
                ) 
                VALUES (
                    '".$email_obj->sender_email."',
                    ".$now.",
                    ".$obj->id."
                )";
                
                $mysqli->query($query);
                
                echo 'Mail sent to  ' . $email_obj->sender_email . PHP_EOL;
                echo '<br>';
            } 
            else {
                echo "<pre>";
                var_dump($m->History);
                print_r($m->History);
                list($tm1, $ar1) = each($m->History[0]);
                list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
            }
            $m->Disconnect();
        }
        $result_emails->close();
        echo PHP_EOL . PHP_EOL . "we sent email to " . $i . " recipients" . PHP_EOL;
    } 
    else {
        $result_emails->close();
        continue;
    }
}

$result->close();
$mysqli->close();

echo PHP_EOL . PHP_EOL . "Process took " . (time() - $time) . " seconds";

?>