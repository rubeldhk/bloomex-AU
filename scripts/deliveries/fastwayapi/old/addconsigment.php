<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en-US"
    lang="en-US">
    <head>
        <link rel="icon"
              type="image/png"
              href="resourses/corpLogo.gif" />
        <link rel="stylesheet" href="resourses/style.css" />
        <script src="resourses/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="resourses/jquery.printElement.min.js"></script>
        <style>
        </style>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#simplePrint").click(function() {
                    printElem({
                        printBodyOptions:
                                {
                                    styleToAdd: 'padding:0px;margin:0px;color:#FFFFFF !important;'
                                },
                        overrideElementCSS: [
                            'resourses/print.css',
                            {href: 'resourses/print.css', media: 'print'}]
                    });
                });

                $("#printTracks").click(function() {
                    printElem2({});
                });
            });
            function printElem(options) {
                $('#toPrint').printElement(options);
            }
            function printElem2(options) {
                $('#tracks').printElement(options);
            }

        </script>
    </head>
    <body>
        <?php
        require_once 'configuration.php';
        $cfg = new FastWayCfg();
        $link = mysql_connect($cfg->host, $cfg->user, $cfg->pw) or die ('Not connected : ' . mysql_error());

        function filter_($data) {
            $data = trim(htmlentities(strip_tags($data)));
            if (get_magic_quotes_gpc())
                $data = stripslashes($data);
            $data = mysql_real_escape_string($data);
            return $data;
        }

        require_once('bloomexorder.php');
        $order = new BloomexOrder(filter_($_REQUEST['id']));
        $sender = $_REQUEST['sender'];
        
        $qq  = "select * from jos_vm_order_history where order_id = '$order->_id' and order_status_code='f'";
        $result_qq = mysql_query($qq);
        $qqq = "select * from jos_vm_order_history where order_id = '$order->_id' and order_status_code='c'";
        $result_qqq = mysql_query($qqq);
        
        if (mysql_num_rows($result_qq) > mysql_num_rows($result_qqq)) {
        ?>
        <h2>Fast label already sent</h2>
        <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
        <?php
        die();
        }
        $consigment['UserID'] = $cfg->user_id;
        $consigment['ContactName'] = $order->_PersonName;
        $consigment['CompanyName'] = $order->_CompanyName;
        $consigment['Address1'] = $order->_StreetLines1;
        $consigment['Address2'] = $order->_StreetLines2;
        $consigment['Suburb'] = $order->_City;
        $consigment['Postcode'] = $order->_PostalCode;
        $consigment['ContactPhone'] = $order->_PhoneNumber;
        ($order->_CustomerComments != '') ? $consigment['SpecialInstruction1'] = $order->_CustomerComments : '';
        $consigment['CompanyName'] = $order->_CompanyName;
        $consigment['CompanyName'] = $order->_CompanyName;
        $consigment['CompanyName'] = $order->_CompanyName;
        $consigment['CompanyName'] = $order->_CompanyName;
        //$consigment['Items[0].Reference'] = /* filter_($_REQUEST['reference']) */ '1';
        //$consigment['Items[0].Quantity'] = /* filter_($_REQUEST['reference']) */ '1';
        //$consigment['Items[0].Weight'] = /* filter_($_REQUEST['reference']) */ '1';
        $count = count($_REQUEST['Reference']);
        for ($i = 0; $i != $count; $i++) {
            $consigment['Items['.$i.'].Reference'] = $_REQUEST['Reference'][$i];
            $consigment['Items['.$i.'].Quantity'] = $_REQUEST['Count'][$i];
            $consigment['Items['.$i.'].Weight'] = $_REQUEST['Weight'][$i];
        }
        $consigment['api_key'] = $cfg->api_key;
        
        $curl_options = array(
            CURLOPT_URL => $cfg->api_host . 'addconsignment',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($consigment),
            CURLOPT_HTTP_VERSION => 1.0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false
        );
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $result = curl_exec($curl);
        
        if (curl_errno($curl)) {
            echo 'Error : ' . curl_error($curl);
        } else {
            $rs = json_decode($result,true); // json to object
        }
//        print '<pre>';
//        print_r($rs);
//        print '</pre>';
        ?>
        <div style="font-family:Tahoma, Verdana; font-size:12px;">
            <div id="loading">The Shipment Info is sending to Fast Way. Please wait...</div>
            <?php
            if ($rs) {
                ?>	
                <script type="text/javascript">
                    document.getElementById("loading").style.display = "none";
                </script>
                <?php
                if ($rs['error']) {
                    ?> 
                    <h3>Error while processing shipment</h3>
                    Error Description: <font color="#ff0000"><?php echo $rs->error; ?></font> <br/>
                    RQ/RS:<br/>
                    <?php
                    echo "<<pre>Request:";
                    var_dump($curl_options);
                    echo "</pre>";
                    echo "<pre>Response:";
                    var_dump($rs);
                    echo "</pre>";
                } else {
                    ?>
                    <h2>Shipment submitted succesfull</h2>
                    <?php
                    echo "<!--<pre>Request:";
                    var_dump($curl_options);
                    echo "</pre>";
                    echo "<pre>Response:";
                    var_dump($rs);
                    echo "</pre>-->";
                    /*if (isset($rs->return->processShipmentResult->message)) { 
                        echo "<br/>Additional Canpar message:" . $rs->return->processShipmentResult->message;
                    }
                    echo "<br/>Estimated delivery date: " . $rs->return->processShipmentResult->shipment->estimated_delivery_date . "<br/>";
                    */
                    if ($rs['result']['ConsignmentID']) {
                        $timestamp = time() /* + ( (-1) * 60 * 60 ) */;
                        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
                        // Update the Order History.
                        $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code,date_added,comments,user_name) VALUES ('$order->_id', '" . $cfg->status_fast_label . "', '$mysqlDatetime', '" . htmlspecialchars("ID: " . $rs['result']['ConsignmentID'], ENT_QUOTES) . "', '$sender')";
                        /* if (!($db->insert_sql($q))) {

                          $db->print_last_error(true);
                          } */
                        $r = mysql_query($q);
                        if (!$r) {
                            echo "Order history update failed. File addconsigment.php: ". mysql_error();
                            echo "<!--<query> $q </query>" . "error:" . mysql_error() . "-->";
                        }
                        //---------------Generate a PDF with Fastway labels.
                        $curl_options1 = array(
                            CURLOPT_URL => $cfg->api_host . 'generatefastwaylabel',
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => 'UserID=' . $cfg->user_id . '&api_key=' . $cfg->api_key . '&ConsignmentID=' . $rs['result']['ConsignmentID'],
                            CURLOPT_HTTP_VERSION => 1.0,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HEADER => false
                        );
    
                        $curl1 = curl_init();
                        curl_setopt_array($curl1, $curl_options1);
                        $result_curl1 = curl_exec($curl1);
       
                        if (curl_errno($curl1)) {
                            echo 'Error : ' . curl_error($curl1);
                        } else {
                            $rs1 = json_decode($result_curl1,true); // json to object
                        }
    
                        //print('<pre>');
                        //print_r($rs1);
                        //print('</pre>');
    
                        if ($rs1['error']) {
                            echo ('Error : ' . $rs1['error']);
                        } else {
                            $file = $rs1['result']['pdf'];
                            $filename = $rs['result']['ConsignmentID'];
                            $newfile = 'labels/consignment_' . $filename . '.pdf';
        
                            if (!copy($file, $newfile)) {
                                echo "Не удалось скопировать $file...\n";
                            }
                        }
                        ?>
                        <!---------------Label printing.-->
                        <br><center><a href="javascript: w=window.open('<?php print $file;?>'); w.print(); ">Print labels.</a></center>
                        <?php
                        //GET CANPAR LABEL
                        /*$printer_request['id'] = $rs['result']['ConsignmentID'];
                        // $printer_request['horizontal'] = '1';
                        $printer_request['password'] = $password;
                        $printer_request['thermal'] = $sender_object->ThermalPrinter;
                        $printer_request['user_id'] = $user_id;
                        $printer_rq['request'] = $printer_request;
                        $printer_rs = $printer->getLabels($printer_rq);
                        //$printer_rs=$printer->getLabels($printer_rq);
                        /*  echo "<pre>Request:";
                          var_dump($printer->__getLastRequest());
                          echo "</pre>";
                          echo "<pre>Response:";
                          var_dump($printer->__getLastResponse());
                          echo "</pre>";
                         */
                        /*if ($printer_rs->return->error) {
                            ?>
                            <h3>Error while processing labels</h3>
                            Error Description: <font color="#ff0000"><?php echo $printer_rs->return->error; ?></font> <br/>
                            Printer RQ/RS:<br/>
                            <?php
                            echo "<pre>Request:";
                            var_dump($client->__getLastRequest());
                            echo "</pre>";
                            echo "<pre>Response:";
                            var_dump($client->__getLastResponse());
                            echo "</pre>";
                        } else {
                            $img = "<div id=\"toPrint\" >";
                            foreach ($printer_rs->return->labels as $k => $v) {
                                $fp = fopen('labels/label_' . $k . "_" . $order->_id . '.png', 'wb');
                                fwrite($fp, base64_decode($v));
                                fclose($fp);
                                $img.= '<img style=\'display:block;width:90%;margin:0 auto\' class="label" src="labels/label_' . $k . "_" . $order->_id . '.png" /><br style="page-break-after:always">';
                            }
                            $img.= "</div>";
                            echo "<br/><br/><span id=\"simplePrint\" class=\"printspan\">Print all labels</span>";
                            echo "<div style=\"display:none\">" . $img . "</div>";
                        }*/
                    }
                }
            }
            ?>
            <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
        </div>
    </body>
</html>