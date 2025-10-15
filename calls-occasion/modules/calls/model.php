<?php

class calls_model {

    private $mysqli = null;
    private $key;
    private $statuses = array(
        1 => 'Buyer',
        2 => 'Hot lead',
        3 => 'No interested',
        4 => 'Voice mail',
    );
    private $one_extension = 0;

    public function __construct() {
        global $mysqli, $one_extension;

        $this->mysqli = $mysqli;
        $this->one_extension = $one_extension;
    }

    public function getDefault() {
        $return = array();
        $return['result'] = true;

        return json_encode($return);
    }

    public function getLogin($extension, $project) {
        $return = array();
        $return['result'] = false;

        if ((int) $extension > 0) {
            $return['result'] = true;

            $_SESSION['extension'] = $extension;
            $_SESSION['project'] = $project;
        }

        return json_encode($return);
    }

    public function getLogout() {
        $return = array();
        $return['result'] = false;
        unset($_SESSION['extension'], $_SESSION['project']);

        return json_encode($return);
    }

    public function callAttempt() {
        $return = array();
        $return['result'] = false;

        $json = $this->getNumber();

        $obj = json_decode($json);
        $return['rawnumber'] = $obj;
        $return['json']=$json;
        if ($obj->result !== false) {
            $return['get_number'] = true;

            $json1 = $this->setCall($obj->obj);
            $obj1 = json_decode($json1);

            if ($obj1->result !== false) {
                $return['get_number'] = true;
                $return['result'] = true;
            } else {
                $return['curl'] = $obj1->curl;
                $return['error'] = $obj1->error;
            }
            $return['obj'] = $obj->obj;
            $return['query'] = $obj->query;
            $return['histories'] = $obj->histories;
            $return['sip_url'] = $obj1->sip_url;
        }

        echo json_encode($return);
    }

    private function getUserIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    private function setHistory($id, $comment) {
        $query = "INSERT INTO `tbl_calls_occasion_history`
        (
            `id_number`,
            `comment`,
            `datetime_add`
        )
        VALUES (
            " . $id . ",
            '" . $this->mysqli->real_escape_string($comment) . "',
            '" . date('Y-m-d H:i:s') . "'
        )";

        return $this->mysqli->query($query);
    }

    private function getNumber() {
        $return = array();
        $return['result'] = false;

        $this->key = time() . mt_rand(1000, 9999);

        $gmt_hour = gmdate('G');

        $query = "UPDATE 
        `tbl_calls_occasion` AS `cc`
        SET 
            `cc`.`key`='" . $this->key . "',
            `cc`.`datetime_call`='" . date('Y-m-d H:i:s') . "',
            `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "',
            `cc`.`ip`='" . $this->getUserIpAddr() . "'
        WHERE 
            (
                HOUR(DATE_ADD(NOW(), INTERVAL `cc`.`gmt_offset` HOUR)) BETWEEN 8 AND 17
            )
            AND
            (
                (
                    `cc`.`datetime_call`='0000-00-00 00:00:00'
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
                `ou_i`.`user_email`,
                `cc`.`number` AS `phone`,
                `ou_i`.`city`,
                `o`.`customer_occasion`,
                `o`.`customer_note`,
                `o`.`user_id`,
                `os`.`order_status_name`,
                FROM_UNIXTIME(`o`.`cdate`) AS 'cdate'
            FROM `tbl_calls_occasion` AS `cc`
            INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
                ON 
                    `ou_i`.`order_id`=`cc`.`order_id`
                    AND
                    `ou_i`.`address_type`='BT'
            INNER JOIN `jos_vm_orders` AS `o`
                ON
                    `o`.`order_id`=`cc`.`order_id`
            INNER JOIN `jos_vm_order_status` AS `os`
                ON
                    `os`.`order_status_code`=`o`.`order_status`
            LEFT JOIN `jos_vm_api2_orders` AS `ao` 
                ON
                    `ao`.`order_id` = `cc`.`order_id`
            WHERE `cc`.`key`='" . $this->key . "'
                AND `ao`.`id` IS NULL
            ";

            $result = $this->mysqli->query($query);

            if ($result->num_rows > 0) {
                $return['result'] = true;

                $obj = $result->fetch_object();

                $query = "SELECT 
                    SUM(`o`.`order_total`) AS `total`
                FROM `jos_vm_orders` AS `o`
                WHERE `o`.`user_id`=" . $obj->user_id . " 
                ORDER BY `o`.`cdate` DESC LIMIT 3";

                $last_3_result = $this->mysqli->query($query);
                $last_3_obj = $last_3_result->fetch_object();
                $obj->last_3_total = number_format($last_3_obj->total, 2);

                $last_3_result->close();

                $return['obj'] = $obj;

//                $this->sendEmail($obj);

                $return['histories'] = array();

                $getHistories = json_decode($this->getHistories($obj->id));

                if ($getHistories->result == true) {
                    $return['histories'] = $getHistories->histories;
                }
            }

            $result->close();
        } else {
            $return['error'] = $this->mysqli->error;
            $return['query'] = $query;
        }

        return json_encode($return);
    }

    private function setCall($obj) {
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
        $return['db_number'] = $obj->phone;
        $return['call_number'] = $phone;
        $url = 'http://sip2.bloomex.ca:8080/paneltest/autorecall/call.php?ext=' . (int) $_SESSION['extension'] . '&tel=' . $phone . '&code=' . $country . '&call=' . $type_num;
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
            if ((int) $data == 1) {
                $return['result'] = true;
            } else {
                $return['error'] = "unexpected call api response:" . $data;
            }
        } else {
            $return['error'] = curl_error($ch);
        }

        if ($return['result'] === false && mb_strlen($phone) < 10) {
            $query = "UPDATE 
            `tbl_calls_occasion` AS `cc`
            SET 
                `cc`.`api_call_result`='" . $this->mysqli->real_escape_string($data) . "',
            WHERE 
               `cc`.`key`='" . $this->key . "'
            ";

            $return['error'] = "bad number :" . $phone;
        } else {
            if ($return['result']) {
                $query = "UPDATE 
                    `tbl_calls_occasion` AS `cc`
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

    public function sendEmail($obj) {
        global $mosConfig_live_site, $mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass,
        $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_debug,
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
            `id`='22' 
            AND 
            `recipient_type`='1'
        ";

        $email_result = $this->mysqli->query($email_query);
        $email_obj = $email_result->fetch_object();
        $email_result->close();

     require_once $_SERVER['DOCUMENT_ROOT'] . 'includes/MAIL5/MAIL5.php';

        $m = new MAIL5;
        $m->From($mosConfig_mailfrom);
        $send = false;
        if (FUNC5::is_mail($obj->user_email)) {
            $addto = $m->AddTo($obj->user_email);
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

        if ($send == 1) {
            $query = "UPDATE `tbl_calls_occasion`
            SET
                `sent_email`='1'
            WHERE `id`=" . $obj->id . "
            ";

            $this->mysqli->query($query);

            $this->setHistory($obj->id, 'Email was sent on ' . $obj->user_email . '.');
        } else {
            $this->setHistory($obj->id, 'Email doesn\'t sent on ' . $obj->user_email . '.');
        }

        unset($ps_comemails, $database);

        return $send;
    }

    public function reSendEmail() {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`,
            `ou_i`.`user_id`,
            `ou_i`.`user_email`
        FROM `tbl_calls_occasion` AS `cc`
        INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
            ON 
                `ou_i`.`order_id`=`cc`.`order_id`
                AND
                `ou_i`.`address_type`='BT'
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
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

    public function reQueue() {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`
        FROM `tbl_calls_occasion` AS `cc`
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $query = "UPDATE `tbl_calls_occasion`
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

                $this->setHistory($obj->id, $comment);
            }
        }

        $result->close();

        return json_encode($return);
    }

    public function getHistories($id) {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`comment`,
            `cc`.`datetime_add`
        FROM `tbl_calls_occasion_history` AS `cc`
        WHERE `cc`.`id_number`=" . (int) $id . "
        ORDER BY `cc`.`id` ASC
        ";

        $result = $this->mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            $return['result'] = true;
            $return['histories'] = array();

            while ($obj = $result->fetch_object()) {
                $obj->comment = nl2br($obj->comment);

                $return['histories'][] = $obj;
            }
            $result->close();
        }

        return json_encode($return);
    }

    public function setStatus($status) {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`
        FROM `tbl_calls_occasion` AS `cc`
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        $action = '';
        switch ($status) {
            case 1:
                $action = 'Buyer';
                break;
            case 2:
                $action = 'Manager Call Back';
                break;
            case 3:
                $action = 'Not Interested';
                break;
        }

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();
            $query = sprintf("UPDATE `tbl_calls_occasion`
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

                $comment = 'Status changed to ' . $this->statuses[$status] . '.';

                if (!empty($_POST['comment'])) {
                    $comment .= "\r\nComment: " . $_POST['comment'] . '.';
                }

                $this->setHistory($obj->id, $comment);
            }
        }

        $result->close();

        return json_encode($return);
    }

    public function getCount() {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            COUNT(`cc`.`id`) AS `count`
        FROM `tbl_calls_occasion` AS `cc`
            WHERE `cc`.`key`='' and status not like '2' and gmt_offset not like ''
        ";

        $result_all = $this->mysqli->query($query);

        if ($result_all->num_rows > 0) {
            $return['result'] = true;

            $obj_all = $result_all->fetch_object();

            $return['all'] = $obj_all->count;

            $query = "SELECT 
                COUNT(`cc`.`id`) AS `count`
            FROM `tbl_calls_occasion` AS `cc`
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
            FROM `tbl_calls_occasion` AS `cc`
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

    public function sendEmails() {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`,
            `ou_i`.`user_id`,
            `ou_i`.`user_email`
        FROM `tbl_calls_occasion` AS `cc`
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
