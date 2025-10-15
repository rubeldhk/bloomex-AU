<?php
date_default_timezone_set('Australia/Sydney');
include "../configuration.php";
include "MAIL5.php";  
$db = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

if ($db->connect_errno > 0) 
{
    die('Unable to connect to database [' . $db->connect_error . ']');
}

//$email_body="<a target='_blank' href='".$mosConfig_live_site."/downloadreport.php?date=".date('Y-m-d')."'> Here </a> You can download the report";

$email_body = 'You can download the report';


//$my='eldarv@bloomex.ca';

$email1='brendan@bloomex.ca';
$email2='mark@bloomex.ca';
$email3='eroth@legacy.com';
$email4='Pmatarangas@legacy.com';
$email5='Proche@legacy.com';
$email6='Aallen@legacy.com';
$email7='amee@legacy.com';
//$email0='danielyanlevon89@mail.ru';
$subject='Legacy Product Sold for '.date("F j");
           

$m = new MAIL5;

$m->From($mosConfig_mailfrom);

//$m->AddTo($my);
$m->AddTo($email1);
$m->AddTo($email2);
$m->AddTo($email3);
$m->AddTo($email4);
$m->AddTo($email5);
$m->AddTo($email6);
$m->AddTo($email7);
$m->Subject($subject);
$m->html($email_body);

if($_GET['date'])
{
    $date = $_GET['date'];
}
else
{
    $date =date("Y-m-d");
}

$rows = array();

$sql = "SELECT 
    `order_item_sku` AS `Product Sku`,
    `order_item_name` AS `Product Name`,
    SUM(`product_quantity`)  AS `Product Quantity`
FROM `jos_vm_order_item`
WHERE
    DATE_FORMAT(FROM_UNIXTIME(`cdate`+11*60*60), '%Y-%m-%d %H:%i:%s') BETWEEN '{$date} 00:00:01' AND '{$date} 23:59:59' AND `order_item_sku` like 'AL%'
GROUP BY `order_item_sku`"; 
    
if (!$result = $db->query($sql)) 
{
    die('There was an error running the select query [' . $db->error . ']');
}
        
echo $result->num_rows;

if ($result->num_rows > 0) 
{
    $flag = false;
    $email_content = '';
    
    while ($row = $result->fetch_assoc()) 
    {
        if (!$flag)
        {
            $email_content .= implode(',', array_keys($row))."\r\n";
            $flag = true;
        }
        
        $email_content .= implode(',', array_values($row))."\r\n";
    }
   
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

?>

