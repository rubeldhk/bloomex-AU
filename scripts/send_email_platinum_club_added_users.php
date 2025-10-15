<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

require_once '../cron/MAIL5.php';
$start_date = date("Y-m-d", strtotime("-4 months"));
$end_date = date("Y-m-d");

$query = "SELECT 
    `o`.`order_id`,
    `o`.`user_id`,
    FROM_UNIXTIME(`o`.`cdate`) AS 'Order Date',
    `f`.`user_email`,
    `f`.`first_name`,
    `o`.`cdate` 
FROM `jos_vm_order_item` AS `i`
LEFT JOIN `jos_vm_orders` AS `o` 
    ON `o`.`order_id`=`i`.`order_id`
LEFT JOIN `tbl_platinum_club` AS `p` 
    ON `p`.`user_id`=`o`.`user_id`
LEFT JOIN `jos_vm_order_user_info` AS `f` 
    ON `f`.`order_id`=`o`.`order_id` 
        AND
        `f`.`user_id`=`o`.`user_id`
        AND
        `address_type`='BT'
WHERE 
    (`i`.`order_item_sku` LIKE 'PC-01' OR `i`.`order_item_sku` LIKE 'PC-01SP') 
    AND 
    (FROM_UNIXTIME(`o`.`cdate`) BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59')
    AND 
    `p`.`id` IS NULL
GROUP BY `f`.`user_id`";

$result = $mysqli->query($query);

if ($result->num_rows == 0) {
    $result->close();
    
    echo $query;
    die(__LINE__ . 'Select error: '.$mysqli->error);
}

$emails = array();
$i = 0;

while ($obj = $result->fetch_object()) {
    $emails[$i]['user_email'] = $obj->user_email;
    $emails[$i]['first_name'] = $obj->first_name;
    $emails[$i]['user_id'] = $obj->user_id;
    $emails[$i]['cdate'] = $obj->cdate;
    $i++;
}
$result->close();

$new_users = '';
if (!empty($emails)) {
    foreach ($emails as $row) {

        $sql_pc = "INSERT INTO `tbl_platinum_club`
        (
            `user_id`,
            `start_datetime`
        ) 
        VALUES (
            '{$row["user_id"]}', 
            FROM_UNIXTIME({$row['cdate']})
        )";
            
        if (!$mysqli->query($sql_pc)) {
            echo $sql_pc;
            die(__LINE__ . 'Select error: '.$mysqli->error);
        }
        $new_users .= $row['user_email'] . "<br>";
        $shopper_subject = "Your Bloomex Platinum Club Membership";
        $shopper_html = "Dear " . $row['first_name'] . ",<br/><br/>
                                    Congratulations on becoming a Bloomex Platinum Club Member. You will now receive Free Regular Shipping on all your orders by logging in under this email address.
                                    Call or order online at your convenience.<br/><br/>
                                    Best Regards,<br/>
                                    Bloomex Australia<br/>
                                    1800 451 637<br/><br/>
                                    <img src='http://media.bloomex.ca/coupon_logo.png' />";

        $m = new MAIL5;
        $m->From($mosConfig_mailfrom);
        $m->AddTo($row['user_email']);
        $m->Subject($shopper_subject);
        $m->Html($shopper_html);
        $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
        
        if (!$c) {
            die(print_r($m->Result));
        }
        
        if ($m->Send($c)) {
            
        } 
        else {
            '<br /><pre>';
            print_r($m->History);
            list($tm1, $ar1) = each($m->History[0]);
            list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
            echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
        }
        $m->Disconnect();
    }
}

echo "added  platinum club members list<br><br>";
echo "<pre>";
print_r($new_users);

$mysqli->close();

?>

