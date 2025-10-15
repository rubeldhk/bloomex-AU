<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en-US"
    lang="en-US">
    <head>
        
    </head>
    <body>
<?php
    require_once '../configuration.php';
    /*
    $order_id = $_GET['order_id'];
    $sender = $_GET['sender'];
    
    $cfg = new FastWayCfg();
    $lnk = mysql_connect($cfg->host, $cfg->user, $cfg->pw) or die ('Not connected : ' . mysql_error());
    mysql_select_db($cfg->db,$lnk) or die("No database connection : ".mysql_error());
    */
    require_once "mysql.php";
    $data = base64_decode(strrev($_GET['data']));
    $data_a = explode('||', $data);
    
    foreach ($data_a as $v) 
    { 
        $v_a = explode('|', $v);
        $_GET[$v_a[0]] =  $v_a[1];
    }
    $order_id = $_GET['order_id'];
    $sender = $_GET['sender'];
    $wh = $_GET['warehouse'];
    
    require 'configuration.php';
    
    $link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
    mysql_select_db($mosConfig_db, $link);

    $query = "select comments from jos_vm_order_history where order_id = '$order_id' and order_status_code='H' order by order_status_history_id desc limit 0,1";
    
    $result = mysql_query($query);
    if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysql_error());
    }
    
    $row = mysql_fetch_array($result);
    
    $aComments = explode (" ",$row['comments']);
    $sConsignment_id = trim($aComments['1']);

    $curl_options = array(
        CURLOPT_URL => $mosConfig_fw_api_host . 'removeconsignment',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'UserID=' . $mosConfig_fw_user_id . '&ConsignmentID=' . $sConsignment_id . '&api_key=' . $mosConfig_fw_api_key,
        CURLOPT_HTTP_VERSION => 1.0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, $curl_options);
    $result_curl = curl_exec($curl);
       
    if (curl_errno($curl)) {
        echo 'Error : ' . curl_error($curl);
    } else {
        $rs = json_decode($result_curl,true); // json to object
    }
    
    if ($rs['error']) {
        echo ('Error : ' . $rs['error']);
    } else {
        date_default_timezone_set('Australia/Sydney');
        $timestamp = time() /* + ( (-1) * 60 * 60 ) */;
        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
        //$file = 'labels/'.$cfg->user.'/'.$order_id.'.pdf';
        $file = 'labels/'.$mosConfig_fw_user_id.'/'.$order_id.'.pdf';
        //unlink($file);
        
        require_once $mosConfig_absolute_path.'/MediaBloomexCa.php';

        $media_bloomex_ca = new MediaBloomexCa();
        $media_bloomex_ca->delete('/bloomex.com.au/fastway_labels/'.$mosConfig_fw_user_id.'/'.$order_id.'.pdf');
        
        $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code,date_added,comments,user_name) VALUES ('$order_id', '" . $mosConfig_status_cancel_fast_label . "', '$mysqlDatetime', '" . htmlspecialchars("ID: " . $sConsignment_id, ENT_QUOTES) . "', '$sender')";
        $r = mysql_query($q);
        
        $q = "UPDATE `jos_vm_orders` SET `order_status`='" . $mosConfig_status_cancel_fast_label . "' WHERE `order_id`='$order_id'";
        $r = mysql_query($q);
        ?>
        <h2>Fast label canceled</h2>
        <?php
    }
?>
<br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
</body>
</html>