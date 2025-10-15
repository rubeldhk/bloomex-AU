<?php
    require_once 'configuration.php';
    
    $consignment_item_id = $_GET['consignment_id'];
    
    $cfg = new FastWayCfg();

    $mysqli = new mysqli('db2.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com', 'BLCOMA_stage', 'C8BdVi2D2p2QskSH', 'BLCOMA_stage');
    $mysqli->set_charset('utf8');
    
    $curl_options = array(
        CURLOPT_URL => $cfg->api_host . 'removeconsignmentitem',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'UserID=19576&ConsignmentItemID=' . $consignment_item_id . '&api_key=33bd710badba6e4557f03e2289e02653',
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
        $comment = htmlspecialchars('ID: ' . $consignment_id,ENT_QUOTES);
        date_default_timezone_set('Australia/Sydney');
        $timestamp = time(); 
        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
            
        $q = "INSERT INTO `jos_vm_order_history`
        (
            `order_id`, 
            `order_status_code`, 
            `date_added`, 
            `comments`
        ) 
        VALUES (
            " . $order_id . ",
            " . $cfg->status_cancel_fast_label . ",
            " . $mysqlDatetime . ", " . $comment . "
        )";
        
        if (!$mysqli->query($q)) {
            echo "Order history update failed : ".$mysqli->error;
            echo "<!--<query> $q </query>" . "error:".$mysqli->error. "-->";
        }
        if (!unlink('labels/' . $consignment_id . '.pdf')) {
            echo 'Error deleting file : labels/' . $consignment_id . '.pdf';
        }
    }
    
    $mysqli->close();
?>