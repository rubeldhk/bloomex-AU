<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$sql = "SELECT COUNT(*) as orders_count FROM jos_vm_orders;";
$res =  $mysqli->query($sql);
$order_count = $res->fetch_assoc();
$sites = array('bloomex.ca','bloomex.com.au','bloomexusa.com');
$auth = 'test:ahs0hij3Ah';
$md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';
$orders_count=0;
foreach ($sites as $site){
    $service_url = 'https://'.$site . '/get_orders_count.php';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $auth);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'key=' . md5($md5_salt . 'ORDERS_COUNT' . $md5_salt));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    $curl_response = curl_exec($curl);
    if ($curl_response) {
        $response = json_decode($curl_response);
        curl_close($curl);
    }
    if($response){
        $orders_count+=$response->orders_count;
    }
    echo $site.' orders count '.$response->orders_count;
}

$orders_count+=1480043;
if($orders_count>0){
    $mysqli->query("UPDATE tbl_orders_count set orders_count=".$orders_count."");
}

$mysqli->close();

