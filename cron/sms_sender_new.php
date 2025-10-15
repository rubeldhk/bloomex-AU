<?php

class SMSParam 
{
    public $AccountKey;
    public $TemplateBody;
    public $CellNumbers = array();
    public $Args = array();
    public $Options;
}

$time = time();

include_once '../configuration.php';
require_once 'MAIL5.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$now = date("Y-m-d  H:i:s");

$query_text = "SELECT 
    `t`.`id`, 
    `t`.`text` 
FROM `tbl_sms_text` AS `t`
INNER JOIN `tbl_numbers_for_sending` AS `n` 
    ON `n`.`text_id`=`t`.`id` 
        AND 
        `n`.`date`<='$now' 
        AND 
        `n`.`sent`='0'
WHERE 
    `t`.`date`<='".$now."' 
    AND 
    `t`.`sent`='1'
LIMIT 1";

$result = $mysqli->query($query_text);

if ($result) {
    $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
    $parameters = new SMSParam;

    $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
    
    $obj = $result->fetch_object();
    
    $out_text = mysql_fetch_assoc($sql_text);

    $out_text['text'] = str_replace("\r\n", '', trim($out_text['text']));

    preg_match_all('/{(.*?)}/siu', $out_text['text'], $vars);

    $our_vars = $vars[1];

    $it = 1;
    $parameters->TemplateBody = preg_replace_callback('/{(.*?)}/siu', function($match) use(&$it) {
    return '<'.$it++.'>';
    }, $out_text['text']);

    for ($i = 0; $i < sizeof($our_vars); $i++)
    {
        ${'args_'.$i} =  array();
    }

    $ids_update = array();
    $ids_delete = array();

    $TemplateBody = preg_replace('/<([0-9]+)>/si', '', $parameters->TemplateBody);
    
    $query_numbers = "SELECT `n`.`id`, `n`.`number`, `i`.`last_name`, `i`.`first_name`, `i`.`middle_name`, `i`.`phone_1`
    FROM `tbl_numbers_for_sending` AS `n`
    LEFT JOIN `jos_vm_user_info` AS `i` ON `i`.`fax`=`n`.`number`
    LEFT JOIN `tbl_not_receive` AS `nr` ON `nr`.`number`=`n`.`number`
    WHERE `n`.`date` <= '$now' AND `n`.`sent`='0' AND `nr`.`id` IS NULL AND `n`.`text_id`=".$out_text['id']."
    GROUP BY `n`.`id` ORDER BY `n`.`id` LIMIT 1000";
    
    $sql_numbers = mysql_query($query_numbers);

    while ($out_numbers = mysql_fetch_array($sql_numbers)) 
    {
        $parameters->CellNumbers[] = $out_numbers['number']; 
        
        foreach ($our_vars as $key => $one_var)
        {
            Switch ($one_var)
            {
                case 'first_name':
                case 'user_name':
                    $arg = isset($out_numbers['first_name']) ? $out_numbers['first_name'] : '';
                    ${'args_'.$key}[] = $arg;
                    $TemplateBody_real = $TemplateBody.$arg;
                break;

                case 'last_name':
                    $arg = isset($out_numbers['last_name']) ? $out_numbers['last_name'] : '';
                    ${'args_'.$key}[] = $arg;
                    $TemplateBody_real = $TemplateBody.$arg;
                break;

                case 'user_number':
                    $arg = isset($out_numbers['number']) ? $out_numbers['number'] : '';
                    ${'args_'.$key}[] = $arg;
                    $TemplateBody_real = $TemplateBody.$arg;
                break;

                case 'middle_name':
                    $arg = isset($out_numbers['middle_name']) ? $out_numbers['middle_name'] : '';
                    ${'args_'.$key}[] = $arg;
                    $TemplateBody_real = $TemplateBody.$arg;
                break;
            }
        }
        
        if (mb_strlen($TemplateBody_real) > 158)
        {
            foreach ($our_vars as $key => $one_var)
            {
                array_pop(${'args_'.$key});
            }

            array_pop($parameters->CellNumbers);

            $ids_delete[] = $out_numbers['id'];
        }
        else 
        {
            $ids_update[] = $out_numbers['id']; 
        }
    }
    
    for ($i = 0; $i < sizeof($our_vars); $i++)
    {
        $parameters->Args[] = ${'args_'.$i};
    }

    $parameters->Options = array('MsgContentType=ASCII');
    
    if (sizeof($ids_update) > 0)
    {
        mysql_query("UPDATE `tbl_numbers_for_sending` SET `sent`='1', `datesend`='" . $now . "' where `id` IN (".implode(',', $ids_update).")");
    }

    if (sizeof($ids_delete) > 0)
    {
        mysql_query("UPDATE `tbl_numbers_for_sending` SET `sent`='2', `datesend`='" . $now . "' where `id` IN (".implode(',', $ids_delete).")");
    }
    
    if (sizeof($parameters->CellNumbers) > 0)
    {
        $result = $client->SendTemplatedBulkMessageWithOptions($parameters);

        preg_match('/([0-9]+) messages queued successfully. ([0-9]+) messages failed to queue./siu', $result->SendTemplatedBulkMessageWithOptionsResult, $counts);

        mysql_query("UPDATE `tbl_sms_text` SET `success`=`success`+".(int)$counts[1].", `fail`=`fail`+".(int)$counts[2]." WHERE `id`=".$out_text['id']."");

        $m = new MAIL5;
        $m->From('cron@bloomex.ca');
        $m->AddTo('cronerror@bloomex.ca');
        $m->Subject('Cron BL.COM.AU sms_sender_new');
        $m->Html($result->SendTemplatedBulkMessageWithOptionsResult);

        $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20); 

        $m->Send($c);
    }
}