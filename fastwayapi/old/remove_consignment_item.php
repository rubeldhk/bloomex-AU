<?php
    require_once 'configuration.php';
    
    $consignment_item_id = $_GET['consignment_id'];
    
    $cfg = new FastWayCfg();
    $lnk = mysql_connect('db2.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com', 'BLCOMA_stage', 'C8BdVi2D2p2QskSH') or die ('Not connected : ' . mysql_error());
    mysql_select_db('BLCOMA_stage',$lnk) or die("No database connection : ".mysql_error());
    
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
        $q = "INSERT INTO jos_vm_order_history(order_id, order_status_code, date_added, comments) VALUES (" . $order_id . ", " . $cfg->status_cancel_fast_label . ", " . $mysqlDatetime . ", " . $comment . ")";
        $r = mysql_query($q);
        if (!$r) {
            echo "Order history update failed : ". mysql_error();
            echo "<!--<query> $q </query>" . "error:" . mysql_error() . "-->";
        }
        if (!unlink('labels/' . $consignment_id . '.pdf')) {
            echo 'Error deleting file : labels/' . $consignment_id . '.pdf';
        }
    }
?>