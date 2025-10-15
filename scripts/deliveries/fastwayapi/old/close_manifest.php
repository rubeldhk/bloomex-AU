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

    require_once 'configuration.php';
    
    $manifest_id = $_REQUEST['manifest_id'];
    $sender = $_REQUEST['sender'];
    
    $cfg = new FastWayCfg();
    $lnk = mysql_connect($cfg->host, $cfg->user, $cfg->pw) or die ('Not connected : ' . mysql_error());
    mysql_select_db($cfg->db,$lnk) or die("No database connection : ".mysql_error());
    
    $curl_options = array(
        CURLOPT_URL => $cfg->api_host . 'closemanifest',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'UserID=' . $cfg->user_id . '&ManifestID=' . $manifest_id . '&api_key=' . $cfg->api_key,
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
        $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code,date_added,comments,user_name) VALUES ('$manifest_id', '" . $cfg->status_cancel_fast_label . "', '$mysqlDatetime', '" . htmlspecialchars("Manifest_ID: " . $manifest_id, ENT_QUOTES) . "', '$sender')";
        $r = mysql_query($q);
        
        $file = $rs['result']['pdf'];
        $newfile = 'labels/runsheet_' . $manifest_id . '.pdf';
        
        if (!copy($file, $newfile)) {
            echo "Не удалось скопировать $file...\n";
        }
        ?>
        <h2>Manifest closed</h2>
        <br><center><a href="javascript: w=window.open('<?php print $file;?>'); w.print(); ">Print runsheet.</a></center>
        <?php
    }
?>
</body>
</html>