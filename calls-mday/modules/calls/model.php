<?php

class calls_model
{

    private $mysqli = null;
    private $key;
    private $statuses = array(
        1 => 'Buyer',
        2 => 'Hot lead',
        3 => 'No interested',
        4 => 'Voice mail',
    );
    private $one_extension = 0;

    public function __construct()
    {
        global $mysqli, $one_extension;

        $this->mysqli = $mysqli;
        $this->one_extension = $one_extension;
    }

    public function getDefault()
    {
        $return = array();
        $return['result'] = true;

        return json_encode($return);
    }

    public function getLogin($extension, $project)
    {
        $return = array();
        $return['result'] = false;

        if ((int)$extension > 0) {
            $return['result'] = true;

            $_SESSION['extension'] = $extension;
            $_SESSION['project'] = $project;
        }

        return json_encode($return);
    }

    public function getLogout()
    {
        $return = array();
        $return['result'] = false;
        unset($_SESSION['extension'], $_SESSION['project']);

        return json_encode($return);
    }

    public function callAttempt()
    {
        $return = array();
        $return['result'] = false;

        $json = $this->getNumber();

        $obj = json_decode($json);
        $return['rawnumber'] = $obj;
        $return['json'] = $json;
        if ($obj->result !== false) {
            $return['get_number'] = true;

            $json1 = $this->setCall($obj->obj);
            $obj1 = json_decode($json1);

            if ($obj1->result !== false) {
                $return['get_number'] = true;
                $return['result'] = true;
            } else {
                $this->updateError();
                $return['curl'] = $obj1->curl;
                $return['error'] = $obj1->error;
            }
            $return['obj'] = $obj->obj;
            $return['query'] = $obj->query;

        }

        echo json_encode($return);
    }
    
    public function updateError()
    {
        $query = "UPDATE 
            `tbl_calls_mday_2025_2` AS `cc`
            SET 
                `cc`.`datetime_call`='" . date('Y-m-d H:i:s') . "',
                `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "',
                `cc`.`ip`='" . $this->getUserIpAddr() . "',
                `cc`.`api_call_result`='Bad number',
                `cc`.`status`=3
            WHERE 
                `cc`.`key`='" . $this->key . "'
            ";
        
        return $this->mysqli->query($query);
    }

    private function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }



    private function getNumber()
    {
        $return = array();
        $return['result'] = false;

        $this->key = time() . random_int(1000, 9999);

        $query = "UPDATE 
        `tbl_calls_mday_2025_2` AS `cc`
        SET 
            `cc`.`key`='" . $this->key . "',
            `cc`.`datetime_call`='" . date('Y-m-d H:i:s') . "',
            `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "',
            `cc`.`ip`='" . $this->getUserIpAddr() . "'
        WHERE 
            (
                HOUR(DATE_ADD(NOW(), INTERVAL `cc`.`gmt_offset` HOUR)) BETWEEN 1 AND 17
            )
            AND
            (
                (
                    `cc`.`datetime_call` IS NULL
                )
                OR
                (
                    '" . date('Y-m-d H:i:s', strtotime('+3 minutes')) . "' >= `cc`.`datetime_next`
                )
            )
            AND
            `cc`.`key`=''
            ORDER BY `cc`.`id` ASC LIMIT 1
        ";

        $return['query'] = $query;

        if ($this->mysqli->query($query)) {
            $query = "SELECT 
                `cc`.`id`,
                `cc`.`order_id`,
                `cc`.`country`,
                `ou_i`.`first_name`,
                `ou_i`.`last_name`,
                `ou_i`.`company`,
                `cc`.`email`,
                `cc`.`number` AS `phone`,
                `ou_i`.`city`,
                `o`.`customer_occasion`,
                `o`.`customer_note`,
                `o`.`user_id`,
                `o`.`order_total`,
                `os`.`order_status_name`,
                `ou_s`.`first_name` AS 'recipient_name',
                FROM_UNIXTIME(`o`.`cdate`) AS 'cdate',
                GROUP_CONCAT(oi.order_item_name SEPARATOR ', ') AS products
            FROM `tbl_calls_mday_2025_2` AS `cc`
            INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
                ON 
                    `ou_i`.`order_id`=`cc`.`order_id`
                    AND
                    `ou_i`.`address_type`='BT'
            INNER JOIN `jos_vm_order_user_info` AS `ou_s` 
                ON 
                    `ou_s`.`order_id`=`cc`.`order_id`
                    AND
                    `ou_s`.`address_type`='ST'
            INNER JOIN `jos_vm_orders` AS `o`
                ON
                    `o`.`order_id`=`cc`.`order_id`
            INNER JOIN `jos_vm_order_status` AS `os`
                ON
                    `os`.`order_status_code`=`o`.`order_status`
            INNER JOIN 
                jos_vm_order_item AS oi ON oi.order_id = o.order_id 
            WHERE `cc`.`key`='" . $this->key . "' 
            GROUP BY o.order_id";

            $result = $this->mysqli->query($query);

            if ($result->num_rows > 0) {
                $return['result'] = true;

                $obj = $result->fetch_object();
                $return['obj'] = $obj;

                $this->sendEmail($obj);
                $this->sendSms($obj);
                

            }

            $result->close();
        } else {
            $return['error'] = $this->mysqli->error;
            $return['query'] = $query;
        }

        return json_encode($return);
    }
    
    private function sendSms($obj)
    {
        return true;
        global $mosConfig_limit_sms_sender_AccountKey;

        $smsBody = 'Send Mother\'s Day flowers with Bloomex - Save 20% with Coupon CODE: MOM2024. Make her Day with a fabulous Bouquet! Visit https://bloomex.com.au/season-holiday/mothers-day/';

            $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
            $parameters = new stdClass();
            $parameters->CellNumber = $obj->phone;
            $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
            $parameters->MessageBody = $smsBody;
            $result = $client->SendMessageExtended($parameters);

        if ($result->SendMessageExtendedResult->QueuedSuccessfully == 1) {

            $mysqlDatetime = date("Y-m-d G:i:s");
            $MessageID = $result->SendMessageExtendedResult->MessageID;

            $query = "INSERT INTO `jos_sms_history`
                (
                    `messageID`,
                    `phone`,
                    `datetime`,
                    `text`,
                    `direction`,
                    `operator`,
                    `status`
                ) 
                VALUES  (
                    '" . $this->mysqli->real_escape_string($MessageID) . "',
                    '" . $this->mysqli->real_escape_string( $obj->phone) . "',
                    '" . $mysqlDatetime . "',
                    '" . $this->mysqli->real_escape_string($smsBody) . "',
                    'outgoing',
                    '" . $this->mysqli->real_escape_string($_SESSION['extension']) . "',
                    'pending'
                )";
            $this->mysqli->query($query);

            $query = "INSERT INTO `sms_conversation`
                (
                    `title`,
                    `text`,
                    `number`,
                    `last_modified`
                ) 
                VALUES  (
                    'MDay Calls',
                    '" . $this->mysqli->real_escape_string($smsBody) . "',
                    '" . $this->mysqli->real_escape_string( $obj->phone) . "',
                    " . time() . "
                )";
            $this->mysqli->query($query);
        }

        return $result;
    }
    
    private function setCall($obj)
    {

        $return = array();

        $return['result'] = false;

        $user = 'prog';
        $password = 'fathuaViey0onga';

        switch (strtoupper($obj->country)){
            case 'AUSTRALIA':
                $country = 'AUS';
                break;
            case 'CANADA':
                $country = 'CAN';
                break;
            case 'NEW ZEALAND':
                $country = 'NZL';
                break;
            case 'SINGAPORE':
                $country = 'SGP';
                break;
            case 'UNITED KINGDOM':
                $country = 'GBR';
                break;
            case 'UNITED STATES':
                $country = 'USA';
                break;
            case 'NZL':
                $country = 'NZL';
                break;
            default:
                $country = 'AUS';
        }

        $type_num = 3;
        $phone = preg_replace("/\D/", '', $obj->phone);
        if ($country == "AUS") {
            $phone = substr($phone, -9);
        }

        $return['db_number'] = $obj->phone;
        $return['call_number'] = $phone;
        $url = 'http://sip2.bloomex.ca:8080/paneltest/autorecall/call.php?ext=' . (int)$_SESSION['extension'] . '&tel=' . $phone . '&code=' . $country . '&call=' . $type_num;
        $return['sip_url'] = $url;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $data = curl_exec($ch);

        $return['curl'] = $data;

        if (!curl_errno($ch)) {
            if ((int)$data == 1) {
                $return['result'] = true;
            } else {
                $return['error'] = "unexpected call api response:" . $data;
            }
        } else {
            $return['error'] = curl_error($ch);
        }

        if ($return['result'] === false ) {
            $query = "UPDATE 
            `tbl_calls_mday_2025_2` AS `cc`
            SET   `cc`.`curl_response`='" . ($return['error'] ?? 'curl result is false'). "',
                `cc`.`api_call_result`='" . $this->mysqli->real_escape_string($data) . "',
            WHERE 
               `cc`.`key`='" . $this->key . "'
            ";

            $return['error'] = "bad number :" . $phone;
        } else {
            if ($return['result']) {
                $query = "UPDATE 
                    `tbl_calls_mday_2025_2` AS `cc`
                    SET 
                        `cc`.`api_call_result`='" . $this->mysqli->real_escape_string($data) . "',
                    WHERE 
                       `cc`.`key`='" . $this->key . "'
                    ";
            }
        }
        curl_close($ch);

        $this->mysqli->query($query);

        return json_encode($return);
    }

    public function sendEmail($obj)
    {

        global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $mosConfig_mailfrom, $mosConfig_fromname,
               $mosConfig_smtphost,$mosConfig_smtpport,$mosConfig_smtpuser,$mosConfig_smtppass,$mosConfig_smtpprotocol;

        define('_VALID_MOS', true);
        define('_JEXEC', true);

        global $mosConfig_absolute_path;
        $mosConfig_absolute_path = $_SERVER['DOCUMENT_ROOT'];

        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/joomla.php';

        global $database;
        $database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
        require_once $_SERVER['DOCUMENT_ROOT'] . '/administrator/components/com_virtuemart/classes/ps_comemails.php';

        $ps_comemails = new ps_comemails;

        $email_query = "SELECT
            `email_subject`, 
            `email_html` 
        FROM `jos_vm_emails` 
        WHERE 
            `id`='23' 
        ";

        $email_result = $this->mysqli->query($email_query);
        $email_obj = $email_result->fetch_object();
        $email_result->close();

        require_once $_SERVER['DOCUMENT_ROOT'] . 'includes/MAIL5/MAIL5.php';

        $m = new MAIL5;
        $m->From($mosConfig_mailfrom);
        $send = false;
        if (FUNC5::is_mail($obj->email)) {
            $addto = $m->AddTo($obj->email);
            $bcc = $m->addbcc('outgoingcorporateemail@bloomex.com.au');
            $m->Subject($email_obj->email_subject);
            $m->Html($email_obj->email_html);
            $c = $m->Connect($mosConfig_smtphost, (int)$mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
            if ($c) {
                $send = $m->Send($c);
                if (!$send) {
                    echo "<pre>";
                    var_dump($m->History);
                    print_r($m->History);
                    list($tm1, $ar1) = each($m->History[0]);
                    list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
                }
                $m->Disconnect();
            }
        }

        if ($send) {
            $query = "UPDATE `tbl_calls_mday_2025_2`
            SET
                `sent_email`='1'
            WHERE `id`=" . $obj->id . "
            ";

            $this->mysqli->query($query);
        }

        unset($ps_comemails, $database);

        return $send;
    }

    public function reSendEmail()
    {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`,
            `ou_i`.`user_id`,
            `cc`.`email`
        FROM `tbl_calls_mday_2025_2` AS `cc`
        INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
            ON 
                `ou_i`.`order_id`=`cc`.`order_id`
                AND
                `ou_i`.`address_type`='BT'
        WHERE `cc`.`id`=" . (int)$_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $return['result'] = true;

            $obj = $result->fetch_object();
            $this->sendEmail($obj);
        }

        $result->close();

        return json_encode($return);
    }

    public function reQueue()
    {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`
        FROM `tbl_calls_mday_2025_2` AS `cc`
        WHERE `cc`.`id`=" . (int)$_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $query = "UPDATE `tbl_calls_mday_2025_2`
            SET
                `key`='',
                `datetime_next`='" . $this->mysqli->real_escape_string($_POST['datetime'] . ':00') . "',
                `status`='2'
            WHERE `id`=" . $obj->id . "
            ";

            if ($this->mysqli->query($query)) {
                $return['result'] = true;

                $comment = 'Queued on ' . $_POST['datetime'] . ':00.';

                if (!empty($_POST['comment'])) {
                    $comment .= "\r\nComment: " . $_POST['comment'] . '.';
                }


            }
        }

        $result->close();

        return json_encode($return);
    }



    public function setStatus($status)
    {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`
        FROM `tbl_calls_mday_2025_2` AS `cc`
        WHERE `cc`.`id`=" . (int)$_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        $action = '';
        switch ($status) {
            case 1:
                $action = 'Buyer';
                break;
            case 2:
                $action = 'Hot lead';
                break;
            case 3:
                $action = 'No interested';
                break;
            case 4:
                $action = 'Voice mail';
                break;
        }

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();
            $query = sprintf("UPDATE `tbl_calls_mday_2025_2`
                SET
                    `status`='%s',
                    `comment`='%s',
                    `vm_sent`='%s',
                    `api_call_result`='%s'
                WHERE `id`=" . $obj->id . "
                ",
                $status,
                $_POST['comment'],
                $status == 4,
                $action
            );

            if ($this->mysqli->query($query)) {
                $return['result'] = true;
            }
        }

        $result->close();

        return json_encode($return);
    }

    public function getCount()
    {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            COUNT(`cc`.`id`) AS `count`
        FROM `tbl_calls_mday_2025_2` AS `cc`
            WHERE `cc`.`key`='' and status not like '2' and gmt_offset not like ''
        ";

        $result_all = $this->mysqli->query($query);

        if ($result_all->num_rows > 0) {
            $return['result'] = true;

            $obj_all = $result_all->fetch_object();

            $return['all'] = $obj_all->count;

            $query = "SELECT 
                COUNT(`cc`.`id`) AS `count`
            FROM `tbl_calls_mday_2025_2` AS `cc`
            WHERE 
                `cc`.`datetime_call` BETWEEN '" . date('Y-m-d') . " 00:00:00' AND '" . date('Y-m-d') . " 23:59:59'
                AND
                `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "'
            ";

            $result_ext = $this->mysqli->query($query);

            $obj_ext = $result_ext->fetch_object();

            $return['ext'] = $obj_ext->count;

            $result_ext->close();

            $query = "SELECT 
                COUNT(`cc`.`id`) AS `count`
            FROM `tbl_calls_mday_2025_2` AS `cc`
            WHERE 
                `cc`.`datetime_call` BETWEEN '" . date('Y-m-d') . " 00:00:00' AND '" . date('Y-m-d') . " 23:59:59'
            ";

            $result_all_ext = $this->mysqli->query($query);

            $obj_all_ext = $result_all_ext->fetch_object();

            $return['all_ext'] = $obj_all_ext->count;

            $result_all_ext->close();
        }
        $result_all->close();

        return json_encode($return);
    }

    public function sendEmails()
    {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`,
            `ou_i`.`user_id`,
            `ou_i`.`user_email`
        FROM ``tbl_calls_mday_2025_2`` AS `cc`
        INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
            ON 
                `ou_i`.`order_id`=`cc`.`order_id`
                AND
                `ou_i`.`address_type`='BT'
        WHERE 
            (
                (`cc`.`status` = '1')
                OR
                (`cc`.`status` = '2')
            )
            AND
            `cc`.`sent_email`='0'
        ORDER BY `cc`.`id` ASC LIMIT 1
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $return['result'] = true;

            $obj = $result->fetch_object();

            $return['obj'] = $obj;

            $return['send'] = $this->sendEmail($obj);
        }

        $result->close();

        return json_encode($return);
    }

}
