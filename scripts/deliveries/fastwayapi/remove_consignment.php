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
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $order_id = (int) $_REQUEST['order_id'];
    require_once 'bloomexorder.php';
    $order = new BloomexOrder($order_id);
    $wh = $order->filter($order->_warehouse);
    require 'configuration.php';
    


    $query = "select * from jos_vm_orders_deliveries where order_id = '$order_id' limit 0,1";
    
    $result = $mysqli->query($query);
    if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysql_error());
    }
    
    $row = $result->fetch_assoc();


    $curl_options = array(
        CURLOPT_URL => $mosConfig_fw_api_host . 'removeconsignment',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'UserID=' . $mosConfig_fw_user_id . '&ConsignmentID=' . $row['shipment_id'] . '&api_key=' . $mosConfig_fw_api_key,
        CURLOPT_HTTP_VERSION => 1.0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, $curl_options);
//    $result_curl = curl_exec($curl);
       
    if (curl_errno($curl)) {
        echo 'Error : ' . curl_error($curl);
    } else {
        //$rs = json_decode($result_curl,true); // json to object
    }

if (isset($rs['error']) && $rs['error']!='') {
        echo ('Error : ' . $rs['error']);
    } else {
        date_default_timezone_set('Australia/Sydney');
        $timestamp = time() /* + ( (-1) * 60 * 60 ) */;
        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

        $file = 'labels/'.$mosConfig_fw_user_id.'/'.$order_id.'.pdf';
        //unlink($file);
        
        require_once '../../Classes/MediaBloomexCa.php';

        $media_bloomex_ca = new MediaBloomexCa();
        $media_bloomex_ca->delete('/bloomex.com.au/fastway_labels/'.$mosConfig_fw_user_id.'/'.$order_id.'.pdf');


    $status_code = 'A';

    $history_comment = 'Fastway Delivery cancel.';
        $mysqli->query("INSERT INTO `jos_vm_order_history`
                (
                    `order_id`, 
                    `order_status_code`, 
                    `date_added`, 
                    `user_name`, 
                    `comments`
                )
                VALUES
                (
                    " . $order_id . ",
                    '" . $status_code . "', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");

        $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='" . $status_code . "'
                WHERE `order_id`=" . $order_id . "
                ");

        $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_id'";
        $mysqli->query($query);

        ?>
        <h2>Fast label canceled</h2>
    <script type="text/javascript">
        window.opener.jQuery(".delivery_icon_<?php echo $order_id;?>").addClass('default').attr('href','').attr('order_id',"<?php echo $order_id;?>").find('img').attr('src','/templates/bloomex7/images/deliveries/delivery_logo.png');
        window.opener.jQuery(".delivery_icon_span_<?php echo $order_id;?>").html('Updated')
    </script>
        <?php
    }
?>
<br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
</body>
</html>