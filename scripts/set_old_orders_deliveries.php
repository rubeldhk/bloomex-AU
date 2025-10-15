<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$start = $_GET['start'];

$orders_sql = $mysqli->query("SELECT * FROM (SELECT * FROM jos_vm_order_history ORDER BY order_status_history_id  DESC) AS o where o.order_status_code in ('H','6','4') GROUP BY order_id  LIMIT ".$start.", 10000");

date_default_timezone_set('Australia/Sydney');
$timestamp = time();
$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
$Detrack=0;
$GoPeople=0;
$Fastway=0;
$count=0;
if ($orders_sql->num_rows > 0)
{
    $inserts = array();

    while($orders_obj = $orders_sql->fetch_object())
    {
        switch ($orders_obj->order_status_code){
            case 'H':
                $delivery_type=2;
                $Fastway++;
                break;
            case '6':
                $delivery_type=1;
                $Detrack++;
                break;
            case '4':
                $delivery_type=3;
                $GoPeople++;
                break;
        }
        $count++;
        $inserts[] = "('".$orders_obj->order_id."', '".$delivery_type."','".$mysqlDatetime."',1)";
    }

    $orders_sql->close();

    $mysqli->query("INSERT INTO `jos_vm_orders_deliveries` (`order_id`, `delivery_type`,`dateadd`,`active`) VALUES ".implode(',', $inserts)."");
    printf("Errormessage: %s\n", $mysqli->error);
}

$mysqli->close();

echo 'We Updated '.$count.' Orders<br>';
echo 'Fastway : '.$Fastway.'<br>';
echo 'Detrack : '.$Detrack.'<br>';
echo 'GoPeople : '.$GoPeople.'<br>';

?>
