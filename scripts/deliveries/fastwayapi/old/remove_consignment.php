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
    
    $order_id = $_GET['order_id'];
    $sender = $_GET['sender'];
    
    $cfg = new FastWayCfg();
    $lnk = mysql_connect($cfg->host, $cfg->user, $cfg->pw) or die ('Not connected : ' . mysql_error());
    mysql_select_db($cfg->db,$lnk) or die("No database connection : ".mysql_error());

    $query = "select comments from jos_vm_order_history where order_id = '$order_id' and order_status_code='f' order by order_status_history_id desc limit 0,1";
    $result = mysql_query($query);
    if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysql_error());
    }
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $comments = $row;
    }

    $aComments = explode (" ",$comments['comments']);
    $sConsignment_id = trim($aComments['1']);

    $curl_options = array(
        CURLOPT_URL => $cfg->api_host . 'removeconsignment',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'UserID=' . $cfg->user_id . '&ConsignmentID=' . $sConsignment_id . '&api_key=' . $cfg->api_key,
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
        unlink('labels/consignment_' . $sConsignment_id . '.pdf');
        $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code,date_added,comments,user_name) VALUES ('$order_id', '" . $cfg->status_cancel_fast_label . "', '$mysqlDatetime', '" . htmlspecialchars("ID: " . $sConsignment_id, ENT_QUOTES) . "', '$sender')";
        $r = mysql_query($q);
        ?>
        <h2>Fast label canceled</h2>
        <?php
    }
?>
<br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
</body>
</html>