<?php

class SMSParam {

    public $CellNumber;
    public $AccountKey;
    public $MessageBody;

}
date_default_timezone_set('Australia/Sydney');
$time = time();
include "../configuration.php";
$pass = ($_SERVER['argv']['pass']) ? $_SERVER['argv']['pass'] : $_GET['pass'];

//if (/*isset($pass) && $pass == $mosConfig_email_sender_get_parameter*/ 1) {


$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$now = date("Y-m-d  H:i:s");

$query = "SELECT 
    *
FROM `tbl_sms_text`
WHERE 
    `date`<='$now'
    AND 
    `sent`='1'
";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('Select error: '.$mysqli->error);
}

while ($obj = $result->fetch_object()) {

    $query_numbers = "SELECT 
        *
    FROM `tbl_numbers_for_sending`
    WHERE 
        `date`<='".$now."'
        AND 
        `sent`='0' 
        AND 
        `text_id`='".$obj->id."'
    LIMIT ".$mosConfig_limit_sms_sender."
    ";
    
    $result_numbers = $mysqli->query($query_numbers);

    if (!$result_numbers) {
        $result_numbers->close();
        $mysqli->close();
        die('Select error: '.$mysqli->error);
    }
        
    if ($result_numbers->num_rows != 0) {
        echo '<br/><br/><strong> SMS with title </strong>  ('.$obj->title.') <br/> ';
        $i = 0;
        
        while ($obj_number = $result_numbers->fetch_object()) {
            $result_numbers->close();
            
            $query_name = "SELECT 
                `last_name`,
                `first_name`,
                `phone_1`,
                `user_id`
            FROM `jos_vm_user_info`
            WHERE 
                `phone_1` LIKE '".$obj_number->number."'
            ";
            
            $result_name = $mysqli->query($query_name);

            if (!$result_name) {
                $result_name->close();
                $mysqli->close();
                die('Select error: '.$mysqli->error);
            }

            $html_send = $obj->text;
            
            if ($result_name->num_rows != 0) {
                $obj_name = $result_name->fetch_object();
                
                $html_send = str_replace('{user_name}', (isset($obj_name->first_name) ? $obj_name->first_name : ""), $html_send);
                $html_send = str_replace('{user_last_name}', (isset($obj_name->last_name) ? $obj_name->last_name : ""), $html_send);
            }
            else {
                $html_send = str_replace('{user_name}', '', $html_send);
                $html_send = str_replace('{user_last_name}', '', $html_send);
            }
            
            $result_name->close();
            
            $html_send = str_replace('{user_number}', (isset($obj_number->number) ? $obj_number->number : ""), $html_send);
            $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
            $parameters = new SMSParam;
            $parameters->CellNumber = $obj_number->number;
            $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
            $parameters->MessageBody = $html_send;
            $Result_id = $client->SendMessageExtended($parameters);

            if ($Result_id->SendMessageExtendedResult->QueuedSuccessfully == 1) {
                $i ++;
                $query = "UPDATE `tbl_numbers_for_sending`
                SET 
                    `sent`='1',
                    `message_id`='".$Result_id->SendMessageExtendedResult->MessageID."',
                    `datesend`='".$now."'
                WHERE
                    `id`='".$obj_number->id."'
                ";
                $mysqli->query($query);
                
                echo '<br/><strong> SMS  sent to </strong>  ' . $obj_number->number;
            } 
            else {
                $query = "DELETE FROM `tbl_numbers_for_sending`
                WHERE 
                    `id`='".$obj_number->id."'
                ";
                $mysqli->query($query);
            }
        }
        echo "<br><br><strong> we sent SMS to " . $i . " numbers</strong>";
    } 
    else {
        $result_numbers->close();
        continue;
    }
}
$result->close();
$mysqli->close();

echo "<br>Process took " . (time() - $time) . " seconds";

?>