<?php
date_default_timezone_set('Australia/Sydney');

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$db = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

//$email_body="<a target='_blank' href='".$mosConfig_live_site."/downloadreport.php?date=".date('Y-m-d')."'> Here </a> You can download the report";

$datetime = strtotime("-1 day");
if($_GET['date'])
{
    $date = $_GET['date'];
}
else
{
    $date = date("Y-m-d",$datetime);
}

$email_body = 'You can download the report';

//$my = 'verdiev.ed@gmail.com';

$email1='brendan@bloomex.ca';
$email2='mark@bloomex.ca';
//$email3='eroth@legacy.com';
$email4='Pmatarangas@legacy.com';
$email5='Proche@legacy.com';
$email6='Aallen@legacy.com';
$email7='amee@legacy.com';
//$email0='danielyanlevon89@mail.ru';
$subject='Legacy Product Sold for '.date("F j", $datetime);
           

$m = new MAIL5;

$m->From($mosConfig_mailfrom);

//$m->AddTo($my);

//$m->AddTo($email1);
$m->AddTo($email1);
$m->AddTo($email2);
$m->AddTo($email4);
$m->AddTo($email5);
$m->AddTo($email6);
$m->AddTo($email7);
$m->Subject($subject);


$rows = array();

$sql = "SELECT 
    `order_item_sku` AS `Product Sku`,
    `order_item_name` AS `Product Name`,
    SUM(`product_quantity`)  AS `Product Quantity`
FROM `jos_vm_order_item`
WHERE
    DATE_FORMAT(FROM_UNIXTIME(`cdate`+11*60*60), '%Y-%m-%d %H:%i:%s') BETWEEN '{$date} 00:00:01' AND '{$date} 23:59:59' AND `order_item_sku` like 'AL%'
GROUP BY `order_item_sku`"; 
    
$sql = "SELECT 
    `order_item_sku` AS `Product Sku`, 
    `order_item_name` AS `Product Name`, 
    SUM(`product_quantity`) AS `Product Quantity` 
FROM `jos_vm_order_item` 
WHERE 
(FROM_UNIXTIME(`cdate`+11*3600, '%Y-%m-%d %H:%i:%s') BETWEEN '".$date." 00:00:00' AND '".$date." 23:59:59')
AND `order_item_sku` like 'AL%' GROUP BY `order_item_sku`";

if (!$result = $db->query($sql)) 
{
    $db->close();
    die('There was an error running the select query [' . $db->error . ']');
}
        
echo $result->num_rows;

$db->query("INSERT INTO `tbl_legacy_mails` (`date`, `count_product`) VALUES (FROM_UNIXTIME(".time()."+11*3600, '%Y-%m-%d %H:%i:%s'), ".$result->num_rows.")"); 

//echo $db->errno . ") " . $db->error;

if ($result->num_rows > 0) 
{
    $flag = false;
    $email_content = '';
    
    while ($row = $result->fetch_assoc()) 
    {
        if (!$flag)
        {
            $email_table = '<table border="1" cellpadding="10" cellspacing="10" style="border-collapse:collapse;">';
            $email_table .= '<tr>';
            
            foreach (array_keys($row) as $k)
            {
                $email_table .= '<th>'.$k.'</th>';
            }
            $email_table .= '</tr>';
            
            $email_content .= implode(',', array_keys($row))."\r\n";
            $flag = true;
        }
        
        $email_content .= implode(',', array_values($row))."\r\n";
        $email_table .= '<tr>';
            
        foreach (array_values($row) as $v)
        {
            $email_table .= '<td>'.$v.'</td>';
        }
        $email_table .= '</tr>';
    }
    
    $email_table .= '</table>';
    
    $m->html($email_body.'<br/><br/>'.$email_table);
   
    $m->attach($email_content, 'text/csv', 'report.csv', 'utf-8', $encoding = null, $disposition = null, $id = null, $debug = null);

    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);

    if (!$c) 
    {
        var_dump($m->History);
        die(print_r($m->Result));
    }

    if ($m->Send($c)) 
    {
        echo 'Mail sent successfully';
        echo '<br>';
    } 
    else 
    {
        echo "<pre>";
        var_dump($m->History);
        print_r($m->History);
        list($tm1, $ar1) = each($m->History[0]);
        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
        echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
    }
}
$m->Disconnect();
$result->close();
$db->close();
?>

