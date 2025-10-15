<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

date_default_timezone_set('Australia/Sydney');
$sent_message_ides=array();

class SMSParam {
    public $AccountKey;
    public $MessageNumber;
    public $MessageCount;
}


$client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
$parameters = new SMSParam;
$parameters -> AccountKey = $mosConfig_limit_sms_sender_AccountKey;



    $query_incoming_message_id = "SELECT messageID as incoming_message_id FROM `jos_sms_history` WHERE direction = 'incoming' order by messageID desc limit 1";

    $result_incoming_message_id = $mysqli->query($query_incoming_message_id);
    if (!$result_incoming_message_id) {
        die('Select error: '.$mysqli->error);
    }
    while ($incoming_message_id_obj = $result_incoming_message_id->fetch_object()) {
        if($incoming_message_id_obj->incoming_message_id) {
            $incoming_message_id = $incoming_message_id_obj->incoming_message_id;
        }
    }
    $result_incoming_message_id->close();


$parameters -> MessageNumber  = isset($incoming_message_id)?$incoming_message_id:0;
$parameters -> MessageNumber  = 0;
unset($parameters -> MessageCount);
$Result = $client->GetIncomingMessagesAfterID($parameters);

echo '<br>incomming messages:<br><pre>';
print_r($parameters);
print_r($Result);
echo '</pre>';

$parameters -> MessageCount   = 1000;
$Result_sent = $client->GetSentMessages($parameters);

echo '<br>GetSentMessages messages:<br><pre>';
print_r($parameters);
print_r($Result_sent);
echo '</pre>';

$sent_messages = array();
if($Result_sent->GetSentMessagesResult->SMSOutgoingMessage){
    if(is_array($Result_sent->GetSentMessagesResult->SMSOutgoingMessage)){
        foreach($Result_sent->GetSentMessagesResult->SMSOutgoingMessage as $k=>$m){

                $sent_messages[$k]['messageID']=$m->MessageID;
                $sent_messages[$k]['datetime']=date("Y-m-d H:i:s",strtotime($m->SendDate));
                $sent_messages[$k]['status']=$m->Status;
                $sent_message_ides[]=$m->MessageID;
        }
    }else{

            $sent_messages[0]['messageID']=$Result_sent->GetSentMessagesResult->SMSOutgoingMessage->MessageID;
            $sent_messages[0]['datetime']=date("Y-m-d H:i:s",strtotime($Result_sent->GetSentMessagesResult->SMSOutgoingMessage->SendDate));
            $sent_messages[0]['status']=$Result_sent->GetSentMessagesResult->SMSOutgoingMessage->Status;
            $sent_message_ides[]=$Result_sent->GetSentMessagesResult->SMSOutgoingMessage->MessageID;

    }
}

$incoming_messages = array();
if(isset($Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage)){
    if(is_array($Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage)){
        foreach($Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage as $p=>$f){
            $incoming_messages[$p]['messageID']=$f->MessageNumber;
            $incoming_messages[$p]['datetime']=date("Y-m-d H:i:s",strtotime($f->ReceivedDate));
            $incoming_messages[$p]['text']=$f->Message;
            $incoming_messages[$p]['direction']='incoming';
            $incoming_messages[$p]['phone']=preg_replace('/[^0-9]/', '', $f->PhoneNumber);
        }
    }else{
        $incoming_messages[0]['messageID']=$Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage->MessageNumber;
        $incoming_messages[0]['datetime']=date("Y-m-d H:i:s",strtotime($Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage->ReceivedDate));
        $incoming_messages[0]['text']=$Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage->Message;
        $incoming_messages[0]['direction']='incoming';
        $incoming_messages[0]['phone']=preg_replace('/[^0-9]/', '', $Result->GetIncomingMessagesAfterIDResult->SMSIncomingMessage->PhoneNumber);
    }
}

$sent_messages = array_reverse($sent_messages);
$incoming_messages = array_reverse($incoming_messages);


$query_get_sent_messages= "SELECT messageID,status FROM `jos_sms_history` WHERE direction='outgoing'  and messageID in (".implode(',',$sent_message_ides).")";

$result_get_sent_messages = $mysqli->query($query_get_sent_messages);
if (!$result_get_sent_messages) {
    die('Select error: '.$mysqli->error);
}
while ($get_sent_messages_obj = $result_get_sent_messages->fetch_object()) {
    foreach ($sent_messages as $k=>$s){
        if($s['messageID'] == $get_sent_messages_obj->messageID && $s['status']!=$get_sent_messages_obj->status){
            $query_update="UPDATE jos_sms_history SET status = '".$mysqli->real_escape_string($s['status'])."',datetime='".$mysqli->real_escape_string($s['datetime'])."' WHERE messageID='".$mysqli->real_escape_string($s['messageID'])."'";
            $mysqli->query($query_update);

        }
    }
}
$result_get_sent_messages->close();



$inserts = array();
$new_incoming_sms=false;
if($incoming_messages){

    foreach($incoming_messages as $i){
        $new_incoming_sms=true;
        $inserts[] = "(
                    '".$mysqli->real_escape_string($i['messageID'])."',
                    '".$mysqli->real_escape_string($i['datetime'])."',
                    '".$mysqli->real_escape_string($i['text'])."',
                    '".$mysqli->real_escape_string($i['phone'])."',
                    'incoming'
                )";

    }
}




if (sizeof($inserts) > 0) {
    $query = "INSERT INTO
            `jos_sms_history`
            (
                `messageID`,
                `datetime`,
                `text`,
                `phone`,
                `direction`
            )
            VALUES
                ".implode(',', $inserts)."
            ";
   $mysqli->query($query);
}

$mysqli->close();
if($new_incoming_sms){
    $subject='new incoming sms';
    $html_send='new incoming sms, check <a target="_blank" href="'.$mosConfig_live_site.'/administrator/index2.php?option=com_sms_conversation">here</a>';
    $m = new MAIL5;
    $m->From($mosConfig_mailfrom);
    $m->AddTo('vipcustomers@bloomex.ca');
    $m->Subject($subject);
    $m->Html($html_send);
    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);

    if (!$c) {
        echo "<pre>";
        var_dump($m->History);
        die(print_r($m->Result));
    }

    if (!$m->Send($c)) {
        echo "<pre>";
        var_dump($m->History);
        list($tm1, $ar1) = each($m->History[0]);
        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
    }
    $m->Disconnect();
}

?>
