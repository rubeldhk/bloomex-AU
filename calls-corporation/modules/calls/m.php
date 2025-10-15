<?php

class calls_model {

    private $mysqli = null;
    private $corp_group_id = '16';
    private $key;

    public function __construct() {
        global $mysqli;

        $this->mysqli = $mysqli;
    }

    public function getDefault() {
        $return = array();
        $return['result'] = true;

        return json_encode($return);
    }

    public function getLogin($extension) {
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
          setcookie('extension', $obj->ext, time() + 3600 * 10);
          }

          $result->close();
         */
        if ((int) $extension > 0) {
            $return['result'] = true;

            setcookie('extension', (int) $extension, time() + 3600 * 10);
        }

        return json_encode($return);
    }

    public function getLogout() {
        $return = array();
        $return['result'] = false;

        setcookie('extension', '', -1);

        return json_encode($return);
    }

    public function callAttempt() {
        $return = array();
        $return['result'] = false;

        $json = $this->getNumber();
        $return['debug_json'] = $json;
        $obj = json_decode($json);
        $return['debug_obj'] = $obj;
        if ($obj->result !== false) {
            $return['get_number'] = true;

            $json1 = $this->setCall($obj->obj);
            $return['debug_json_set_call'] = $json1;
            $obj1 = json_decode($json1);
            $return['debug_json_set_obj1'] = $obj1;
            if ($obj1->result !== false) {
                $return['get_number'] = true;
                $return['result'] = true;
                $return['obj'] = $obj->obj;
            } else {
                $return['error'] = $obj->error;
            }
        }

        echo json_encode($return);
    }

    private function ErrorReport($curl, $msg = "") {
        global $mosConfig_smtphost, $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol;
        $error_id = time();

        $message = "msg: " . $msg . '<hr/>';
        $message .= "Curl Error: " . curl_error($curl) . '<hr/>';
        $message .= 'Curl info: ' . json_encode(curl_getinfo($curl)) . '<hr/>';
        $message .= 'backtrace: ' . json_encode(debug_backtrace(0)) . '<hr/>';
        $message .= 'backtrace2: ' . serialize(debug_backtrace(0)) . '<hr/>';
        $message .= 'json_error: ' . json_last_error() . '<hr/>';

        $m = new \MAIL5;
        $m->AddTo('kzrajevsky@bloomex.ca');
        $m->AddCC('errors@bloomex.com.au');
        $m->Subject('Calls Corp Error ' . $error_id);
        $m->Html($message, 'utf-8');
        $m->from("errors@bloomex.com.au");
        $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
        $return = $m->Send($c);
        return $error_id . "-" . $return;
    }

    private function getUserIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getCount() {
        $return = array();
        $return['result'] = true;

        $query = "SELECT
            COUNT(`cc`.`id`) AS `count`
        FROM `jos_new_corporate_calls` AS `cc`
        WHERE 
            (    
            `cc`.`datetime_call` < '" . date('Y-m-d H:i:s', time() - 24 * 60) . "'
                AND
            `cc`.`key`=''
            )
        ";

        $result = $this->mysqli->query($query);
        if ($result->num_rows > 0) {
            $return['all'] = $result->fetch_object()->count;
        }
        $result->close();

        return json_encode($return);
    }

    function removeWhiteSpaces($str) {
        return preg_replace('/\s+/', ' ', trim($str));
    }

    private function getNumber() {
        $return = array();
        $return['result'] = false;
        $return['errors'] = [];

        $this->key = time() . mt_rand(1000, 9999);


        $query = "UPDATE 
        `jos_new_corporate_calls` AS `cc`
        SET 
            `cc`.`key`='" . $this->key . "',
            `cc`.`datetime_call`='" . date('Y-m-d H:i:s') . "',
            `cc`.`ext`='" . $this->mysqli->real_escape_string($_COOKIE['extension']) . "',
            `cc`.`ip`='" . $this->getUserIpAddr() . "'
        WHERE  
            ( " . (int) date('H') . "+`cc`.`timezone_offset`) between 9 AND 18 
                AND        
            (    
            `cc`.`datetime_call` < '" . date('Y-m-d H:i:s', time() - 24 * 60) . "'
                AND
            `cc`.`key`=''
            )
        ORDER BY `cc`.`id` DESC LIMIT 1
        
        ";
        $return['update_query'] = $this->removeWhiteSpaces($query);
        if ($this->mysqli->query($query)) {
            $query = "SELECT 
                `cc`.`id`,
                `cc`.`order_id`,
                `cc`.`first_name`,
                `cc`.`last_name`,
                `cc`.`company_name`,
                `cc`.`email`,
                `cc`.`phone`,
                `ou_i`.`city`,
                `o`.`customer_occasion`,
                `o`.`customer_note`,
                `o`.`user_id`,
                `os`.`order_status_name`,
                FROM_UNIXTIME(`o`.`cdate`) AS 'cdate'
            FROM `jos_new_corporate_calls` AS `cc`
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
            WHERE `cc`.`key`='" . $this->key . "'
            ";
            $return['select_query'] = $this->removeWhiteSpaces($query);

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
                $return['obj'] = $obj;
            } else {
                $return['errors'][] = $this->mysqli->error;
            }
        } else {
            $return['errors'][] = $this->mysqli->error;
        }

        return json_encode($return);
    }

    private function setCall($obj) {
        $return = array();
        $return['result'] = false;

        $user = 'test';
        $password = 'Ue7equo8';
        $country = 'AUS';
        $type_num = 5;

        $url = 'http://' . $user . ':' . $password . '@sip2.bloomex.ca:8080/paneltest/autorecall/call.php?ext=' . (int) $_COOKIE['extension'] . '&tel=' . $obj->phone . '&code=' . $country . '&call=' . $type_num;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);

        $return['curl'] = $data;

        if (!curl_errno($ch)) {
            if ((int) $data == 1) {
                $return['result'] = true;
            } else {
                $return['error'] = "unexpected call api response:" . $data." check if your phone software is online and contact the system administrator";
            }
        } else {
            $return['error'] = curl_error($ch);
        }
        if ($return['result']) {
            $query = "UPDATE 
                `jos_new_corporate_calls` AS `cc`
                SET 
                    `cc`.`api_call_result`='" . $this->mysqli->real_escape_string($data) . "'
                WHERE 
                   `cc`.`key`='" . $this->key . "'
                ";
        } else {
            $return['error_num'] = $this->ErrorReport($ch, "<hr>time:" . date("D M j G:i:s T Y") . "<hr>url:" . $url . "<hr>errror:" . $return['error'] . "<hr>");
            $query = "UPDATE 
            `jos_new_corporate_calls` AS `cc`
            SET 
                `cc`.`key`='',
                `cc`.`api_call_result`='" . $return['error_num'] . " | " . $return['error'] . "'
            WHERE 
               `cc`.`key`='" . $this->key . "'
            ";
        }
        curl_close($ch);

        $this->mysqli->query($query);

        return json_encode($return);
    }

    public function saveCompany() {
        $return = array();
        $return['result'] = false;
        $id = (int) $_POST['id'];

        $query = "UPDATE 
            `jos_new_corporate_calls` AS `cc`
            SET 
                `cc`.`status`='1'
            WHERE 
                `cc`.`id`=" . $id . "
            ORDER BY `cc`.`id` ASC LIMIT 1
            ";

        if ($this->mysqli->query($query)) {
            $domain = $this->mysqli->real_escape_string($_POST['company_domain']);
            $corporate_domain = !$this->isFreeDomain($domain);

            if ($corporate_domain) {
                $query = "INSERT INTO `company_groups`
                (
                    `company_name`,
                    `company_domain`,
                    `company_group_id`
                )
                VALUES (
                    '" . $this->mysqli->real_escape_string($_POST['company_name']) . "',
                    '" . $this->mysqli->real_escape_string($_POST['company_domain']) . "',
                    $this->corp_group_id
                )";
                if ($this->mysqli->query($query)) {
                    $update_query = "UPDATE `jos_new_corporate_calls`
                    SET
                        `datetime_email`='" . date('Y-m-d H:i:s') . "',
                        `result_status`='" . ('2') . "'
                    WHERE `id`=" . $id . "
                    ";
                    $this->mysqli->query($update_query);
                    $this->setGroup($this->mysqli->real_escape_string($_POST['company_domain']));
                    $return['result'] = true;
                } else {
                    $return['error'] = 'Cannot insert in company group. Duplicate domain? ' . $_POST['company_name'];
                    $return['insert_query'] = $this->removeWhiteSpaces($query);
                    $return['insert_error'] = $this->mysqli->error;
                }
            } else {
                $return['error'] = 'Free domain ' . $_POST['company_name'];
            }
        } else {
            $return['error'] = 'Cannot update call';
            $return['update_query'] = $query;
            $return['update_error'] = $this->mysqli->error;
        }

        return json_encode($return);
    }

    private function isFreeDomain($domain) {
        $query = "SELECT id from jos_free_email_domains where domain like '$domain' ";

        $result = $this->mysqli->query($query);

        return ($result->num_rows > 0);
    }

    private function setGroup($domain) {
        global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $mosConfig_mailfrom, $mosConfig_fromname;

        $query = "SELECT
            `u`.`id`,
            `u`.`email`,
            `ui`.`first_name`
        FROM `jos_users` AS `u`
        INNER JOIN `jos_vm_user_info` AS `ui` ON
            `ui`.`user_id`=`u`.`id`
            AND
            `ui`.`address_type`='BT'
        WHERE 
            `u`.`email` LIKE '%@" . $domain . "'
            OR
            `u`.`username` LIKE '%@" . $domain . "'
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {

            $inserts = array();

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
                `email_type`='6' 
                AND 
                `recipient_type`='1'
            ";

            $email_result = $this->mysqli->query($email_query);
            $email_obj = $email_result->fetch_object();

            while ($obj = $result->fetch_object()) {
                $inserts[] = "(" . $obj->id . ", " . $this->corp_group_id . ")";

                $mosConfig_mailfrom = "corporate@bloomex.com.au";
                mosMail($mosConfig_mailfrom, $mosConfig_fromname, $obj->email, $email_obj->email_subject, str_replace(array('[COMPANY]', '[FNAME]'), array($name, $obj->first_name), $email_obj->email_html), 1);
            }

            unset($ps_comemails, $database);
            $email_result->close();

            if (sizeof($inserts) > 0) {
                $query = "INSERT INTO `jos_vm_shopper_vendor_xref`
                (
                    `user_id`, 
                    `shopper_group_id`
                ) 
                VALUES
                    " . implode(',', $inserts) . "
                ON DUPLICATE KEY UPDATE `shopper_group_id`=" . $this->corp_group_id . "";

                $this->mysqli->query($query);
            }
        }

        $result->close();
    }

    public function stakeHolder() {
        $return = array();
        $return['result'] = false;

        $query = "SELECT 
            `o`.`user_id`
        FROM `jos_new_corporate_calls` AS `cc`
        INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`cc`.`order_id`
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $query = "SELECT 
                `o`.`id`
            FROM `jos_vm_user_options` AS `o`
            WHERE `o`.`user_id`='" . $obj->user_id . "'
            ";

            $result = $this->mysqli->query($query);

            if ($result->num_rows > 0) {
                $query = "UPDATE 
                `jos_vm_user_options` 
                SET
                    `corp_stakeholder`='1'
                WHERE `user_id`='" . $obj->user_id . "'";
            } else {
                $query = "INSERT INTO 
                `jos_vm_user_options` 
                (
                    `user_id`,
                    `corp_stakeholder`
                )
                VALUES (
                    '" . $obj->user_id . "',
                    '1'
                )";
            }

            if ($this->mysqli->query($query)) {
                $return['result'] = true;
            }
        }

        $result->close();

        return json_encode($return);
    }

    public function noCorp() {
        $return = array();
        $return['result'] = true;
        return json_encode($return);
    }

    public function markAsFreeDomain($domain) {
        $domain_escaped =$this->mysqli->real_escape_string($domain);
        $query = "insert into jos_free_email_domains (domain) values( '$domain_escaped') ";

        $result = $this->mysqli->query($query);
        $return = array();
        $return['query'] = $query;
        $return['mysqli_error'] = $this->mysqli->errror;
        $return['result'] = $result;
        return json_encode($return);
    }

    public function reQueue() {
        $return = array();
        $return['result'] = false;
        $query = "SELECT 
            `cc`.`id`
        FROM `jos_new_corporate_calls` AS `cc`
        WHERE `cc`.`id`=" . (int) $_POST['id'] . "
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $query = "UPDATE `jos_new_corporate_calls`
            SET
                `key`=''
            WHERE `id`=" . (int) $_POST['id'] . "
            ";

            if ($this->mysqli->query($query)) {
                $return['result'] = true;
            }
        }

        $result->close();

        return json_encode($return);
    }

}
