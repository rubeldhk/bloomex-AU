<?php

class SMSParam {

    public $CellNumber;
    public $AccountKey;
    public $MessageBody;

}
date_default_timezone_set('Australia/Sydney');
$time = time();
$mysqlDatetime = date("Y-m-d G:i:s");
$order_status = 'i';
        
include "../configuration.php";

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$datetime_from = date('Y-m-d G:i:s', strtotime('-30 minutes', strtotime(date('Y-m-d G:i:s'))));
$datetime_to = date('Y-m-d G:i:s');

$query = "SELECT
    `order_id`,
    `message_id` 
FROM `tbl_substitution_messages`
WHERE  
    `answer`='0'
    AND 
    `order_id`>0
    AND 
    `message_id`>0 
    AND 
    `date_added`BETWEEN '" . $datetime_from . "'  AND '" . $datetime_to . "' 
LIMIT 25
";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('Select error: '.$mysqli->error);
}

while ($obj = $result->fetch_object()) {
    $result->close();
    
    $order_id = $obj->order_id;
    $message_id = $obj->message_id;

    $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');

    $parameters = new stdClass;
    $parameters->AccountKey =$mosConfig_limit_sms_sender_AccountKey;
    $parameters->MessageID = trim($message_id);
    $Result_id = $client->GetRepliesToMessage($parameters);
    $res = $Result_id->GetRepliesToMessageResult->SMSIncomingMessage;

    $mesage_html = '';
    if ($res) {
        if (is_array($res)) {
            foreach ($res as $r) {
                $mesage_html .= $r->Message;
                $number = $r->PhoneNumber;
            }
        }
        else {
            $mesage_html .= $res->Message;
            $number = $res->PhoneNumber;
        }

        $query = "UPDATE `jos_vm_orders`
        SET 
            `order_status`='".$order_status."' 
        WHERE `order_id`='".$order_id."'
        ";
        
        $mysqli->query($query);

        $query_insert = "INSERT INTO `jos_vm_order_history`
        (	
            `order_id`,
            `date_added`,
            `order_status_code`,
            `comments`, 
            `user_name`
        )
        VALUES (
            '".$order_id."',
            '".$mysqlDatetime."',
            '".$order_status."'
            'Get sms: ".mysql_real_escape_string(trim($mesage_html))."',
            'cron job'
        )";

        $mysqli->query($query);

        if (trim($mesage_html) == 'YES') {
            //get current status
            $query_select = "SELECT
                `order_status_code` 
            FROM `jos_vm_order_history` `
            WHERE 
                `order_id`='$order_id' 
            ORDER BY `order_status_history_id` DESC LIMIT 1
            ";
            
            $result_select = $mysql->query($query_select);
            
            $select_obj = $result_select->fetch_object();
            $result_select->close();
            
            $status = $select_obj->order_status_code;
            //add history with current status
            $query_insert = "INSERT INTO `jos_vm_order_history`
            (	
                `order_id`,
                `date_added`,
                `order_status_code`,
                `comments`, 
                `user_name`
            )
            VALUES (
                '$order_id',
                '".$mysqlDatetime."',
                '".$status."',
                'Add Supersize in order products', 
                'cron job'
            )";
            
            $result_insert = $mysql->query($query_insert);
            if (!$result_insert) {
                $result_insert->close();
                $mysqli->close();
                die('Insert error: '.$mysqli->error);
            }
            $result_insert->close();

            //update order item name add (supersize)
            $query_update = "UPDATE `jos_vm_order_item` 
            SET     
                `order_item_name`= CASE WHEN LOCATE('(supersize)', `order_item_name`) = 0 THEN CONCAT(`order_item_name`, '(supersize)') ELSE `order_item_name` END 
            WHERE 
                `order_id`='".$order_id."'
            ";
            
            $result_update = $mysql->query($query_update);
            if (!$result_update) {
                $result_update->close();
                $mysqli->close();
                die('Update item error '.$mysqli->error);
            }
            $result_update->close();
            
            //update answer to 1
            $query_update = "UPDATE `tbl_substitution_messages`
            SET 
                `answer`='1' 
            WHERE `order_id`='".$order_id."'
            ";
            
            $result_update = $mysql->query($query_update);
            if (!$result_update) {
                $result_update->close();
                $mysqli->close();
                die('Update answer error '.$mysqli->error);
            }
            $result_update->close();
        }
    }
}
echo "<br>Process took " . (time() - $time) . " seconds";
