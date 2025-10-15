<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);


Class InboxChecker {

    protected $mysqli = null;
    protected $imap_cfg = null;
    protected $imap_connect = null;
    protected $smtp = null;
    protected $smtp_func = null;
    protected $smtp_connect = null;
    protected $count_emails = 10;
    protected $coupon_prefix = 'RGC-';
    protected $coupon_count_symbols = 6;
    var $coupon_symbols = array(
        'B',
        'D',
        'G',
        'H',
        'J',
        'K',
        'L',
        'M',
        'N',
        'Q',
        'R',
        'T',
        'V',
        'W',
        'X',
        'Z',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9'
    );
    var $sending_emails = array();
    var $inbox_folder = 'INBOX';
    var $processed_folder = 'INBOX.process';

    function __construct() {
        global $mosConfig_inbox_checker_cfg, $mysqli;
        /*
        $mosConfig_inbox_checker_cfg = (object)array(
            'server' => 'mail.flowerswholesale.ca',
            'user' => 'test2@bloomex.ca',
            'password' => 'e]h~u48FQuoM',
            'port_imap' => 993,
            'port_smtp' => 465,
            'protocol' => 'tls'
        );
        */
        $this->imap_cfg = $mosConfig_inbox_checker_cfg;
        $this->mysqli = $mysqli;

        include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

        $this->smtp = new MAIL5;
        $this->smtp_func = new FUNC5;
        $this->smtp->From('reviews@bloomex.com.au');
        
        $this->smtp_connect = $this->smtp->Connect($this->imap_cfg->server, $this->imap_cfg->port_smtp, $this->imap_cfg->user, $this->imap_cfg->password, $this->imap_cfg->protocol, 20);

//        echo "<hr><pre>";
//        print_r('smtp connect');
//        echo "<pre>";
//        print_r($this->smtp_connect);

        $this->checkInbox();
//
//        echo '<pre>';
//        print_r($this->sending_emails);
//        echo '</pre>';
    }

    function __destruct() {
        imap_close($this->imap_connect);
        $this->smtp->Disconnect();
        $this->mysqli->close();
    }

    private function checkInbox() {
        $this->imapConnect();

//        echo "<hr><pre>";
//        print_r('imap connect');
//        echo "<pre>";
//        print_r($this->imap_connect);

        $emails = $this->getEmails();
        $checked_emails = $this->checkEmails($emails);

        $this->sendCoupons($checked_emails);
    }

    private function imapConnect() {
        $this->imap_connect = imap_open('{' . $this->imap_cfg->server . ':' . $this->imap_cfg->port_imap . '/imap/ssl/novalidate-cert}' . $this->inbox_folder, $this->imap_cfg->user, $this->imap_cfg->password)
                or die('Could not connect: ' . imap_last_error());
    }

    private function getEmails() {
        $imap_check = imap_check($this->imap_connect);

//        echo "<hr><pre>";
//        print_r('check imap connect');
//        echo "<pre>";
//        print_r($imap_check);

        $check = imap_mailboxmsginfo($this->imap_connect);
        if ($check) {
            echo "Date: " . $check->Date . "<br />\n";
            echo "Driver: " . $check->Driver . "<br />\n";
            echo "Mailbox: " . $check->Mailbox . "<br />\n";
            echo "Messages: " . $check->Nmsgs . "<br />\n";
            echo "Recent: " . $check->Recent . "<br />\n";
            echo "Unread: " . $check->Unread . "<br />\n";
            echo "Deleted: " . $check->Deleted . "<br />\n";
            echo "Size: " . $check->Size . "<br />\n";
        } else {
            echo "imap_mailboxmsginfo() failed: " . imap_last_error() . "<br />\n";
        }


        $number_message = 1;
        $i = 1;

        $not_need_emails = array();
        $emails = array();
        while (($number_message <= $imap_check->Nmsgs) AND ( $i <= $this->count_emails)) {

            $message_header = imap_headerinfo($this->imap_connect, $number_message);

            $email_from = $message_header->from[0]->mailbox . '@' . $message_header->from[0]->host;

            if (!in_array($email_from, $emails)) {
                $emails[$number_message] = $email_from;

                $i++;
            } else {
                $not_need_emails[$number_message] = $email_from;
            }

            $number_message++;
        }

//        echo "<hr><pre>";
//        print_r('no need emails');
//        echo "<pre>";
//        print_r($not_need_emails);

        $this->moveMails($not_need_emails);

        return $emails;
    }

    private function checkEmails($emails) {
        $checked_emails = array();
        $unchecked_emails = array();

        foreach ($emails as $key => $email) {
            $query = "SELECT
                `e`.`email`
            FROM `tbl_email_newsletter` AS `e`
            WHERE 
                `e`.`email`='" . $this->mysqli->real_escape_string($email) . "'
                AND 
                `e`.`send_date` > DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 3 MONTH
            ";

            $result = $this->mysqli->query($query);

            if ($result->num_rows == 0) {
                if($this->smtp_func->is_mail($email)){
                    $checked_emails[$key] = $email;
                }else{
                    $unchecked_emails[$key] = $email;
                }
            } else {
                $unchecked_emails[$key] = $email;
            }
            $result->close();
        }
        $this->moveMails($unchecked_emails);


//        echo "<hr><pre>";
//        print_r('emails already exist');
//        echo "<pre>";
//        print_r($unchecked_emails);

        unset($emails, $unchecked_emails);

//        echo "<hr><pre>";
//        print_r('emails ready to send');
//        echo "<pre>";
//        print_r($checked_emails);

        return $checked_emails;
    }

    private function sendCoupons($checked_emails) {
        $email_details = $this->getEmailDetails();

        foreach ($checked_emails as $key => $email) {
            $coupon_code = $this->generateCode();

            if ($coupon_code) {
                $email_send_status = $this->sendEmail($email, $coupon_code, $email_details);
                if ($email_send_status == '1') {
                    $query = "INSERT INTO `tbl_email_newsletter`
                (
                    `email`, 
                    `coupon_code`,
                    `send_date`
                ) 
                VALUES (
                    '" . $this->mysqli->real_escape_string($email) . "',
                    '" . $this->mysqli->real_escape_string($coupon_code) . "',
                    '" . date('Y-m-d') . "'
                )";
                    if (!$this->mysqli->query($query)) {
                        die('Delete Error: ' . $this->mysqli->error);
                    }

                    $this->sending_emails[$key] = array(
                        'email' => $email,
                        'coupon_code' => $coupon_code
                    );
                }
            }
        }
//        echo "<hr><pre>";
//        print_r('move email after creating coupon');
//        echo "<pre>";
//        print_r($this->sending_emails);
        $this->moveMails($this->sending_emails);
    }

    private function checkCode($code) {
        $return = (object) array('result' => false);

        $query = "SELECT 
            `c`.`coupon_id`
        FROM `jos_vm_coupons` AS `c`
        WHERE 
            `c`.`coupon_code`='" . $this->mysqli->real_escape_string($code) . "'
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows == 0) {
            $return->result = true;
        }
        $result->close();

        return $return;
    }

    private function generateCode() {
        $code = $this->coupon_prefix;

        for ($i = 1; $i <= $this->coupon_count_symbols; $i++) {
            $code .= $this->coupon_symbols[array_rand($this->coupon_symbols)];
        }

        if ($this->checkCode($code)->result == true) {
            $query = "INSERT INTO `jos_vm_coupons`
            (
                `coupon_code`, 
                `percent_or_total`,
                `coupon_type`,
                `coupon_value`
            ) 
            VALUES (
                '" . $this->mysqli->real_escape_string($code) . "',
                'total',
                'gift',
                '20.00'
            )";
            
//            echo '<pre>jos_vm_coupons<br/>';
//            print_r($query);
//            echo '</pre>';

            if (!$this->mysqli->query($query)) {
                die('Insert Error: ' . $this->mysqli->error);
            } else {

//                echo "<hr><pre>";
//                print_r('insert coupon code');
//                echo "<pre>";
//                print_r($query);
//                echo "<pre>";
//                print_r($this->mysqli);

                return $code;
            }
        } else {
            return $this->generateCode();
        }
    }

    private function getEmailDetails() {
        $email_details = (object) array();

        $query = "SELECT
            `e`.`email_subject`, 
            `e`.`email_html` 
        FROM `jos_vm_emails` AS `e`
        WHERE 
            `e`.`email_type`='8'
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $email_details = $result->fetch_object();
        }
        $result->close();

        return $email_details;
    }

    private function sendEmail($email, $coupon_code, $email_details) {


        $this->smtp->AddTo($email);
        $this->smtp->Subject($email_details->email_subject);
        $this->smtp->Html(str_replace('{phpShopReviewCoupon}', $coupon_code, $email_details->email_html));

        $email_send_status = $this->smtp->Send($this->smtp_connect);
        echo "<hr/>Send email to ".$email." code ".$coupon_code." status ".$email_send_status." <hr/>";
        $this->smtp->delto();

        return $email_send_status;
    }

    private function moveMails($emails) {
        if (count($emails) > 0) {
            $imap_move = imap_mail_move($this->imap_connect, implode(',', array_keys($emails)), $this->processed_folder);
            imap_expunge($this->imap_connect);
        }
    }

}

$InboxChecker = new InboxChecker();
unset($InboxChecker);

