
<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

Switch ((isset($_GET['action']) ? $_GET['action'] : '')) {
    case 'parse':
        $file = file('./orders_for_refund3.txt');
        $inserts = array();

        foreach ($file as $line) {
            $line = trim($line);

            if (!empty($line)) {
                $order_id = (int) str_replace('bloom-0', '', $line);

                $query = "SELECT 
                    `t`.`id`
                FROM `tmp_refund_approved_script` AS `t`
                WHERE
                    `t`.`order_id`=" . $order_id . "
                ";

                $result = $mysqli->query($query);

                if ($result->num_rows == 0) {
                    $inserts[] = "(
                        " . $order_id . "
                    )";
                }
            }
        }

        $inserts = array_unique($inserts);

        if (count($inserts) > 0) {
            $query = "INSERT INTO `tmp_refund_approved_script`
            (
                `order_id`
            )
            VALUES
                " . implode(',', $inserts) . "
            ";

            if ($mysqli->query($query)) {
                ?>
                Success
                <?php
            } else {
                echo $mysqli->error;
            }
        }
        break;

    default:
        $query = "SELECT
            `t`.`id`,
            `t`.`order_id`,
            `ui`.`user_email`
        FROM `tmp_refund_approved_script` AS `t`
        INNER JOIN `jos_vm_order_user_info` AS `ui`
            ON
            `ui`.`order_id`=`t`.`order_id`
            AND
            `ui`.`address_type`='BT'
        WHERE
            `t`.`status`='0'
        ORDER BY 
            `t`.`id` ASC LIMIT 50
        ";

        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            while ($obj = $result->fetch_object()) {
                $m = new MAIL5;
                $m->From('emily@bloomex.ca');
                $addto = $m->AddTo($obj->user_email);

                if ($addto) {
                    //$m->Subject('Order Update - delivery delay & free upgrade');
                    $m->Subject('Mother\'s Day Order Update - full refund');
                    $str = 'Dear Customer,<br/><br/>We are really sorry to disappoint you. We will not be able to deliver your order due to supply issue. Local growers are sold out and overseas supply is limited due to COVID-19 transportation restrictions.   We appreciate your patience and understanding during this unprecedented time. <br/><br/>We are really sorry. Full refund is issued and it will appear on your credit card in 3 days.<br/><br/>Please give us a chance to serve you again. Please use coupon "smart" for your next purchase.<br/><br/>Sincerely<br/> Bloomex Team';
                    //   $m->Html("Dear Customer,<br/><br/>We are facing a lot of challenges in supply route due to COVID-19 pandemic. Our fresh after Mother' s day shipment arrived with delay. We are really sorry to inform you that your order will be delivered with one day delay on  May 13th.<br/><br/>We will upgrade your order free of charge as a token of appreciation for your patience and understanding of all the challenges we are facing during this unprecedented time.<br/><br/>Yours<br/><br/>Bloomex Team");
                    $m->Html($str);
                    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
                    if ($c) {
                        if (!$m->Send($c)) {
                            echo 'Mail not sent to  ' . $obj->user_email . '<br/>';
                        } else {
                            echo 'Mail sent to  ' . $obj->user_email . '<br/>';

                            $order_status = '1';
                            //$ddate = "13-05-2020";
                            //$order_status = "U";

                            $query = "UPDATE `jos_vm_orders` AS `o`
                              SET
                              `o`.`order_status`='" . $order_status . "'
                              WHERE
                              `o`.`order_id`=" . $obj->order_id . "
                              ";
                            /*
                              $query = "UPDATE `jos_vm_orders` AS `o`
                              SET
                              `o`.`ddate`='" . $ddate . "'
                              WHERE
                              `o`.`order_id`=" . $obj->order_id . "
                              "; */
                            $mysqli->query($query);

                            $query = "INSERT INTO `jos_vm_order_history`
                            (
                                `order_id`,
                                `order_status_code`,
                                `warehouse`,
                                `date_added`,
                                `customer_notified`,
                                `warehouse_notified`,
                                `comments`,
                                `user_name`
                            )
                            VALUES (
                                " . $obj->order_id . ",
                                '" . $order_status . "',
                                '" . $obj->warehouse . "',
                                '" . date('Y-m-d G:i:s', time() + ($mosConfig_offset * 60 * 60)) . "',
                                '1',
                                '0',
                               '" . $mysqli->real_escape_string($str) . "',
                                'Script'
                            )
                            ";
                            $mysqli->query($query);

                            $query = "UPDATE `tmp_refund_approved_script` AS `t`
                            SET
                                `t`.`status`='1'
                            WHERE
                                `t`.`id`=" . $obj->id . "
                            ";
                            $mysqli->query($query);
                        }
                        $m->Disconnect();
                    }
                } else {
                    echo 'Wrong email address ' . $email_to;
                }
            }
        }
        $result->close();

        break;
}
$mysqli->close();

