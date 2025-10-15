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

        /*
          $query = "SELECT
          `e`.`ext`
          FROM `extensions` AS `e`
          WHERE `e`.`ext`=".(int)$extension."
          ";

          $result = $this->mysqli->query($query);

          if ($result->num_rows > 0) {
          $return['result'] = true;

          $obj = $result->fetch_object();
          $_SESSION['extension'] = $obj->ext;
          }

          $result->close();
         */

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

    public function startCall()
    {
        $return = array();
        $return['result'] = false;
        $phone = $_POST['phone'];
        $country = $_POST['country'] ?? 'AUS';
        $return['phone'] = $phone;
        if ($phone) {
            $json1 = $this->setCall($phone, $country);
            $return['call_result'] = json_decode($json1);
            $return['result'] = true;
        }
        echo json_encode($return);
    }

    public function getNextPrevCorpUser()
    {
        $return = array();
        $return['result'] = false;

        $json = $this->getNumber();
        $return['debug_json'] = $json;
        $obj = json_decode($json);
        $return['debug_obj'] = $obj;
        if ($obj->result !== false) {
            $return['get_number'] = true;
            $return['obj'] = $obj->obj;
            $return['result'] = true;
        }

        echo json_encode($return);
    }

    public function callAttempt() {
        $return = array();
        $return['result'] = false;

        $json = $this->getNumber();
        $obj = json_decode($json);
        if ($obj->result !== false) {
            $return['get_number'] = true;
            $return['result'] = true;
            $return['obj'] = $obj->obj;
            $return['query'] = $obj->query;
            $return['histories'] = $obj->histories;
        } else {
            $return['get_number'] = false;
            $return['obj'] = $obj->obj;
            $return['query'] = $obj->query;
            $return['error'] = $obj->error;
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
        $query = "INSERT INTO `tbl_calls_christmas_2024_history`
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

        $action = isset($_POST['action']) ? $_POST['action'] : false;
        $corporate_call_id = isset($_POST['id']) ? $_POST['id'] : false;

        if ($action && $action == 'prev' && $corporate_call_id) {
            $query = "SELECT 
                    `cc`.`id`,
                    `cc`.`order_id`,
                    `cc`.`country`,
                    `cc`.`status`,
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
                    `ou_r`.`first_name` as 'recipient_name',
                    FROM_UNIXTIME(`o`.`cdate`) AS 'cdate'
                FROM `tbl_calls_christmas_2024` AS `cc`
                INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
                    ON 
                        `ou_i`.`order_id`=`cc`.`order_id`
                        AND
                        `ou_i`.`address_type`='BT'
                LEFT JOIN `jos_vm_order_user_info` AS `ou_r` 
                    ON 
                        `ou_r`.`order_id`=`cc`.`order_id`
                        AND
                        `ou_r`.`address_type`='ST'
                INNER JOIN `jos_vm_orders` AS `o`
                    ON
                        `o`.`order_id`=`cc`.`order_id`
                INNER JOIN `jos_vm_order_status` AS `os`
                    ON
                        `os`.`order_status_code`=`o`.`order_status`
                WHERE `cc`.`id` < " . $this->mysqli->real_escape_string($corporate_call_id) . "
                    AND `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "'
                ORDER BY `cc`.`id` DESC
                LIMIT 1
                ";
            $result = $this->mysqli->query($query);

            $return['query'] = $query;
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
            }
            $result->close();
        } else if ($action && $action == 'next' && $corporate_call_id) {
            $query = "SELECT 
                    `cc`.`id`,
                    `cc`.`order_id`,
                    `cc`.`country`,
                    `cc`.`status`,
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
                    `ou_r`.`first_name` as 'recipient_name',
                    FROM_UNIXTIME(`o`.`cdate`) AS 'cdate'
                FROM `tbl_calls_christmas_2024` AS `cc`
                INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
                    ON 
                        `ou_i`.`order_id`=`cc`.`order_id`
                        AND
                        `ou_i`.`address_type`='BT'
                LEFT JOIN `jos_vm_order_user_info` AS `ou_r` 
                    ON 
                        `ou_r`.`order_id`=`cc`.`order_id`
                        AND
                        `ou_r`.`address_type`='ST'
                INNER JOIN `jos_vm_orders` AS `o`
                    ON
                        `o`.`order_id`=`cc`.`order_id`
                INNER JOIN `jos_vm_order_status` AS `os`
                    ON
                        `os`.`order_status_code`=`o`.`order_status`
                WHERE `cc`.`id` > " . $this->mysqli->real_escape_string($corporate_call_id) . "
                    AND (`cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "' OR (`cc`.`extension`='' AND `cc`.`key`=''))
                ORDER BY `cc`.`id` ASC
                LIMIT 1";
            $result = $this->mysqli->query($query);
            $return['query'] = $query;
            if ($result->num_rows > 0) {
                $return['result'] = true;
                $obj = $result->fetch_object();

                $query = "UPDATE 
                    `tbl_calls_christmas_2024` AS `cc`
                    SET 
                        `cc`.`key`='" . $this->key . "',
                        `cc`.`datetime_call`='" . date('Y-m-d H:i:s') . "',
                        `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "',
                        `cc`.`ip`='" . $this->getUserIpAddr() . "'
                    WHERE 
                        id = '".$obj->id."' LIMIT 1
                    ";
                $this->mysqli->query($query);

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
            }
            $result->close();
        } else {
            $query = "UPDATE 
            `tbl_calls_christmas_2024` AS `cc`
            SET 
                `cc`.`key`='" . $this->key . "',
                `cc`.`datetime_call`='" . date('Y-m-d H:i:s') . "',
                `cc`.`extension`='" . $this->mysqli->real_escape_string($_SESSION['extension']) . "',
                `cc`.`ip`='" . $this->getUserIpAddr() . "'
            WHERE 
                (
                    HOUR(DATE_ADD(NOW(), INTERVAL `cc`.`gmt_offset` HOUR)) BETWEEN 9 AND 18
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
                    `cc`.`status`,
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
                    `ou_r`.`first_name` as 'recipient_name',
                    FROM_UNIXTIME(`o`.`cdate`) AS 'cdate'
                FROM `tbl_calls_christmas_2024` AS `cc`
                INNER JOIN `jos_vm_order_user_info` AS `ou_i` 
                    ON 
                        `ou_i`.`order_id`=`cc`.`order_id`
                        AND
                        `ou_i`.`address_type`='BT'
                LEFT JOIN `jos_vm_order_user_info` AS `ou_r` 
                    ON 
                        `ou_r`.`order_id`=`cc`.`order_id`
                        AND
                        `ou_r`.`address_type`='ST'
                INNER JOIN `jos_vm_orders` AS `o`
                    ON
                        `o`.`order_id`=`cc`.`order_id`
                INNER JOIN `jos_vm_order_status` AS `os`
                    ON
                        `os`.`order_status_code`=`o`.`order_status`
                WHERE `cc`.`key`='" . $this->key . "'
                ";

                $result = $this->mysqli->query($query);

                $return['query'] = $query;
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
                    $this->sendEmail($obj);
                    $return['histories'] = array();

                    $getHistories = json_decode($this->getHistories($obj->id));
                    if ($getHistories->result) {
                        $return['histories'] = $getHistories->histories;
                    }
                }

                $result->close();
            }
        }
        return json_encode($return);
    }

    private function setCall($phone, $country) {
        $return = array();
        $return['result'] = false;

        $user = 'prog';
        $password = 'fathuaViey0onga';
        $type_num = 3;
        $phone = preg_replace("/\D/", '', $phone);
        if ($country == "AUS") {
            $phone = substr($phone, -9);
        }
        if (strlen($phone) === 10) {
            $phone = '1' . $phone;
        }
        $return['call_number'] = $phone;
        $url = 'http://sip2.bloomex.ca:8080/paneltest/autorecall/call.php?ext=' . (int) $_SESSION['extension'] . '&tel=' . $phone . '&code='.$country.'&call=' . $type_num;
        $return['sip_url'] = $url;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $data = curl_exec($ch);
        //$data = 1; //for test

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

        if ($return['result'] === false) {
            $query = "UPDATE 
            `tbl_calls_christmas_2024` AS `cc`
            SET 
                `cc`.`api_call_result`='" . $this->mysqli->real_escape_string($data) . "',
            WHERE 
               `cc`.`key`='" . $this->key . "'
            ";

            $return['error'] = "bad number :" . $phone;
        } else {
                $query = "UPDATE 
                    `tbl_calls_christmas_2024` AS `cc`
                    SET 
                        `cc`.`api_call_result`='" . $this->mysqli->real_escape_string($data) . "',
                    WHERE 
                       `cc`.`key`='" . $this->key . "'
                    ";

        }
        curl_close($ch);

        $this->mysqli->query($query);

        return json_encode($return);
    }

    public function sendEmail($obj) {
        global $mosConfig_live_site, $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $mosConfig_mailfrom, $mosConfig_fromname;

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
            `id`='21' 
        ";

        $email_result = $this->mysqli->query($email_query);
        $email_obj = $email_result->fetch_object();
        $email_result->close();


        $send = mosMail($mosConfig_mailfrom, $mosConfig_fromname, $obj->user_email, $email_obj->email_subject, $email_obj->email_html, 1, 'outgoingcorporateemail@bloomex.ca', NULL);

//        $send = 1;

        if ($send == 1) {
            $query = "UPDATE `tbl_calls_christmas_2024`
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
        FROM `tbl_calls_christmas_2024` AS `cc`
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
        FROM `tbl_calls_christmas_2024` AS `cc`
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $query = "UPDATE `tbl_calls_christmas_2024`
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

        //$id = 145;

        $query = "SELECT 
            `cc`.`comment`,
            `cc`.`datetime_add`
        FROM `tbl_calls_christmas_2024_history` AS `cc`
        WHERE `cc`.`id_number`=" . (int) $id . "
        ORDER BY `cc`.`id` ASC
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $return['result'] = true;
            $return['histories'] = array();

            while ($obj = $result->fetch_object()) {
                $obj->comment = nl2br($obj->comment);

                $return['histories'][] = $obj;
            }
        }

        $result->close();

        return json_encode($return);
    }

    public function setStatus($status) {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `cc`.`id`
        FROM `tbl_calls_christmas_2024` AS `cc`
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $query = "UPDATE `tbl_calls_christmas_2024`
            SET
                `status`='" . $status . "'
            WHERE `id`=" . $obj->id . "
            ";

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
        FROM `tbl_calls_christmas_2024` AS `cc`
            WHERE `cc`.`key`='' and status not like '2' and gmt_offset not like ''
        ";

        $result_all = $this->mysqli->query($query);

        if ($result_all->num_rows > 0) {
            $return['result'] = true;

            $obj_all = $result_all->fetch_object();

            $return['all'] = $obj_all->count;

            $query = "SELECT 
                COUNT(`cc`.`id`) AS `count`
            FROM `tbl_calls_christmas_2024` AS `cc`
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
            FROM `tbl_calls_christmas_2024` AS `cc`
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
        FROM `tbl_calls_christmas_2024` AS `cc`
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