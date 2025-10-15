<?php
date_default_timezone_set('Australia/Sydney');
class SMSParam {

    public $CellNumber;
    public $AccountKey;
    public $MessageBody;

}

class sms_history {

    public $AccountKey;
    public $mysqli;
    public $mosConfig_live_site;

    function __construct() {
        include $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

        $this->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
        $this->mosConfig_live_site = $mosConfig_live_site;

        $this->mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
        $this->mysqli->set_charset('utf8');
    }

    function update_last_modified($number) {
        $query = "UPDATE `sms_conversation`
        SET  
            `last_modified`='" . time() . "' 
        WHERE 
            `number`=" . $number . "
        ";

        $this->mysqli->query($query);
    }

    function formatMobileNumber($num) {
        $num = preg_replace('/[^0-9]/', '', $num);
        if (substr($num, 0, 1) == '+')
            return substr($num, 1);
        if (substr($num, 0, 2) == '61')
            return  $num;
        if (substr($num, 0, 2) == '04' || substr($num, 0, 2) == '05')
            return "61" . substr($num, 1);
        return false;
    }

    function get_history() {

        $number = $this->formatMobileNumber($_POST['number']);
        $query_numbers = "SELECT
            `datetime`,status,
            `text`,`direction`,operator
        FROM `jos_sms_history` where phone='" . $this->mysqli->real_escape_string($number) . "' order by datetime desc
        ";

        $result = $this->mysqli->query($query_numbers);

        if (!$result) {
            die('Select error: ' . $this->mysqli->error);
        }
        $mesage_html = "<div class='reply_history' style='float:left;width:100%;max-height: 335px;overflow: auto;'>";
        while ($obj = $result->fetch_object()) {
            if ($obj->direction == 'outgoing') {
                $class_name='outgoing';
                $author = $obj->operator?$obj->operator:'';
               } else {
                $class_name='incoming';
                $author = 'Customer';
                 }
            $mesage_html .= "<div class='customer_reply ".$class_name."'><div class='text_author'>" . $author . "</div><div class='text_author_icon'></div> <div class='text'>" . $obj->text . "</div><div class='text_time'>" . $obj->datetime . "<br>" . $obj->status . "</div></div>";
        }
        $mesage_html .= "</div>";
        $result->close();
        return $mesage_html;
    }

    function send_sms() {
    $operator=isset($_POST['operator'])?$_POST['operator']:'';
        $query = "SELECT
            `id` 
        FROM `tbl_not_receive` 
        WHERE 
            `number`='" . $this->mysqli->real_escape_string($_POST['number']??'') . "'
        ";

        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $result->close();

            return 'error';
        } else {
            $result->close();


            $query_name = "SELECT
            `last_name`,
            `first_name`,
            `phone_1`,
            `user_id`
        FROM `jos_vm_user_info`
        WHERE 
            `phone_1`='" . $this->mysqli->real_escape_string($_POST['number']??'') . "'
        ";

            $result = $this->mysqli->query($query_name);

            if (!$result) {
                die('Select error: ' . $this->mysqli->error);
            }

            $html_send = $_POST['text']??'';
            if ($result->num_rows != 0) {
                $obj = $result->fetch_object();
                $html_send = str_replace('{user_name}', (isset($obj->first_name) ? $obj->first_name : ""), $html_send);
                $html_send = str_replace('{user_last_name}', (isset($obj->last_name) ? $obj->last_name : ""), $html_send);
                $html_send = str_replace('{user_number}', (isset($_POST['number']) ? $_POST['number'] : ""), $html_send);
            } else {
                $html_send = str_replace('{user_name}', "", $html_send);
                $html_send = str_replace('{user_last_name}', "", $html_send);
                $html_send = str_replace('{user_number}', (isset($_POST['number']) ? $_POST['number'] : ""), $html_send);
            }
            $result->close();

            $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
            $parameters = new SMSParam;

            $_POST['number'] = $this->formatMobileNumber($_POST['number']??'');
            if ($_POST['number']) {

                $parameters->CellNumber = $_POST['number'];
                $parameters->AccountKey = $this->AccountKey;
                $parameters->MessageBody = $html_send;

                $Result_id = $client->SendMessageExtended($parameters);
                $mysqlDatetime = date("Y-m-d G:i:s");

                if ($Result_id->SendMessageExtendedResult->QueuedSuccessfully == 1) {
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
                    '" . $this->mysqli->real_escape_string($Result_id->SendMessageExtendedResult->MessageID) . "',
                    '" . $this->mysqli->real_escape_string($_POST['number']) . "',
                    '" . $mysqlDatetime . "',
                    '" . $this->mysqli->real_escape_string($html_send) . "',
                    'outgoing',
                    '" . $this->mysqli->real_escape_string($operator) . "',
                    'pending'
                )";

                    $this->mysqli->query($query);

            if ($_POST['new_number'] == '1') {
                $query = "INSERT INTO `sms_conversation`
                (
                    `title`,
                    `text`,
                    `number`,
                    `last_modified`
                ) 
                VALUES  (
                    '" . $this->mysqli->real_escape_string($_POST['title']) . "',
                    '" . $this->mysqli->real_escape_string($_POST['text']) . "',
                    '" . $this->mysqli->real_escape_string($_POST['number']) . "',
                    " . time() . "
                )";

                $this->mysqli->query($query);
            }
                    return $_POST['number'];
                } else {
                    return $Result_id->SendMessageExtendedResult->ErrorMessage . '{--1--}error';
                }
            } else {
                return 'Sorry - not a mobile number please call and email client instead.{--1--}error';
            }

        }
    }

    function check_new_messages() {
        $new_message_numbers = array();
        $query_numbers = "SELECT
            `number`,
            `last_modified`
        FROM `sms_conversation`
        ";

        $result = $this->mysqli->query($query_numbers);

        if (!$result) {
            die('Select error: ' . $this->mysqli->error);
        }

        while ($obj = $result->fetch_object()) {
            $number = $this->formatMobileNumber($obj->number);
            $last_modified = date("Y-m-d H:i:s", $obj->last_modified);

            $query_ides = "SELECT
                `messageID` 
            FROM `jos_sms_history`
            WHERE 
                `phone`='" . $number . "'  and datetime > '$last_modified' and direction='incoming'
            ORDER BY `messageID` DESC LIMIT 1 
            ";

            $result_ides = $this->mysqli->query($query_ides);

            if (!$result_ides) {
                die('Select error: ' . $this->mysqli->error);
            }

            if ($result_ides->num_rows != 0) {
                $new_message_numbers[] = $number;
            }
            $result_ides->close();
        }
        $result->close();

        return json_encode($new_message_numbers);
    }

}

$sms_history = new sms_history();

$action = $_POST['action'];
switch ($action) {
    case "get_history":
        echo $sms_history->get_history();
        break;
    case "check_new_messages":
        echo $sms_history->check_new_messages();
        break;
    default :
        echo $sms_history->send_sms();
        break;
}
$sms_history->mysqli->close();
