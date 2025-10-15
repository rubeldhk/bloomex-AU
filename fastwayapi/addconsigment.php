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
        <style>
        </style>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#simplePrint").click(function () {
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

                $("#printTracks").click(function () {
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
//        require_once 'configuration.php';
//        $cfg = new FastWayCfg();
//        $link = mysql_connect($cfg->host, $cfg->user, $cfg->pw) or die('Not connected : ' . mysql_error());
require_once "mysql.php";
        require '../configuration.php';
        $link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

        function filter_($data) {
            $data = trim(htmlentities(strip_tags($data)));
            if (get_magic_quotes_gpc())
                $data = stripslashes($data);
            $data = mysql_real_escape_string($data);
            return $data;
        }

        require_once('bloomexorder.php');
        //$order = new BloomexOrder(filter_($_REQUEST['id']));
        $order = new BloomexOrder(filter_($_POST['order_id']));


        $sender = filter_($_POST['sender']);

        $qq = "select * from jos_vm_order_history where order_id = '$order->_id' and order_status_code='H'";
        $result_qq = mysql_query($qq);
        $qqq = "select * from jos_vm_order_history where order_id = '$order->_id' and order_status_code='J'";
        $result_qqq = mysql_query($qqq);

        if (mysql_num_rows($result_qq) > mysql_num_rows($result_qqq)) {
            ?>
            <h2>Fast label already sent</h2>
            <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
            <?php
            die();
        }

        $wh = filter_($_POST['warehouse']);

        require 'configuration.php';

        $consigment['UserID'] = $mosConfig_fw_user_id; //$cfg->user_id;
        $consigment['ContactName'] = $order->_PersonName;
        $consigment['CompanyName'] = $order->_CompanyName;
        $consigment['Address1'] = $order->_StreetLines1;
        $consigment['Address2'] = $order->_StreetLines2;
        $consigment['Suburb'] = $order->_City;
        $consigment['Postcode'] = $order->_PostalCode;
        $consigment['ContactPhone'] = $order->_PhoneNumber;
        ($order->_CustomerComments != '') ? $consigment['SpecialInstruction1'] = $order->_CustomerComments : '';
        //$consigment['Items[0].Reference'] = /* filter_($_REQUEST['reference']) */ '1';
        //$consigment['Items[0].Quantity'] = /* filter_($_REQUEST['reference']) */ '1';
        //$consigment['Items[0].Weight'] = /* filter_($_REQUEST['reference']) */ '1';
        $count = count($_REQUEST['Reference']);
        for ($i = 0; $i != $count; $i++) {
            $consigment['Items[' . $i . '].Reference'] = $_REQUEST['Reference'][$i];
            $consigment['Items[' . $i . '].Quantity'] = $_REQUEST['Count'][$i];
            $consigment['Items[' . $i . '].Weight'] = $_REQUEST['Weight'][$i];
            //medium labels for brisbane
            if (isset($_REQUEST['Packaging'][$i])) {
                if ($_REQUEST['Packaging'][$i] == 1) {
                    $consigment['Items[' . $i . '].Packaging'] = 17;   
                }
            }
        }
        $consigment['api_key'] = $mosConfig_fw_api_key; //$cfg->api_key;

        $curl_options = array(
            CURLOPT_URL => $mosConfig_fw_api_host . 'addconsignment',
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
            $rs = json_decode($result, true); // json to object
        }

//        echo "<pre>Response:";
//        var_dump($rs);
//        echo "</pre>";
//        die;
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
                    Error Description: <font color="#ff0000"><?php echo $rs['error']; ?></font> <br/>
                    RQ/RS:<br/>
                    <?php
                    echo "<pre>Request:";
                    var_dump($curl_options);
                    echo "</pre>";
                    echo "<pre>Response:";
                    var_dump($rs);
                    echo "</pre>";
                } else {
                    ?>
                    <h2>Shipment submitted succesfull</h2>
                    <?php
                    /*
                      echo "<!--<pre>Request:";
                      var_dump($curl_options);
                      echo "</pre>";
                      echo "<pre>Response:";
                      var_dump($rs);
                      echo "</pre>-->";
                     */
                    if ($rs['result']['ConsignmentID']) {
                        date_default_timezone_set('Australia/Sydney');
                        $timestamp = time(); // + ( (-1) * 60 * 60 )
                        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
                        // Update the Order History.
                        $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code,date_added,comments,user_name) VALUES ('$order->_id', '" . $mosConfig_status_fast_label . "', '$mysqlDatetime', '" . htmlspecialchars("ID: " . $rs['result']['ConsignmentID'], ENT_QUOTES) . "', '$sender')";

                        $r = mysql_query($q);
                        if (!$r) {
                            echo "Order history update failed. File addconsigment.php: " . mysql_error();
                            echo "<!--<query> $q </query>" . "error:" . mysql_error() . "-->";
                        }
                $query = "INSERT INTO `jos_vm_orders_deliveries`
                    (
                        `order_id`,
                        `delivery_type`,
                        `dateadd`,
                        `pin`,
                        `active`
                    ) 
                    VALUES (
                        " . $order->_id . ",
                        '2',
                        '" . $mysqlDatetime . "',
                        '" . mysql_real_escape_string($rs['result']['ConsignmentID']) . "',
                        '1'
                    )";
                $r = mysql_query($query);
                if (!$r) {

                    echo $query ." error:" . mysql_error();
                }
                        $curl_options1 = array(
                            CURLOPT_URL => $mosConfig_fw_api_host . 'getlabelpdf',
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => 'CustomerUserID=' . $mosConfig_fw_user_id . '&api_key=' . $mosConfig_fw_api_key . '&ConsignmentID=' . $rs['result']['ConsignmentID'] . '&target=0',
                            CURLOPT_HTTP_VERSION => 1.0,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HEADER => false
                        );

                        $curl1 = curl_init();
                        curl_setopt_array($curl1, $curl_options1);
                        $result_curl1 = curl_exec($curl1);

                        if (curl_errno($curl1)) {
                            echo 'Error : ' . curl_error($curl1);
                        }
                        if ($rs1['error']) {
                            echo ('Error : ' . $rs1['error']);
                        } else {
                            /*
                            require_once $mosConfig_absolute_path . '/MediaBloomexCa.php';

                            $media_bloomex_ca = new MediaBloomexCa();

                            $path_name = '/bloomex.com.au/fastway_labels/' . $mosConfig_fw_user_id . '/';
                            $pdf_name = $order->_id . '.pdf';
                            

                            echo '<img src="data:'.base64_encode($result_curl1).'">';
                            $media_bloomex_ca->delete($path_name . $pdf_name);
                            $media_bloomex_ca->fupload($pdf_name, $path_name, $result_curl1);
             
                            echo "<script>window.location.replace('print.php?order_id={$order->_id}&user_id={$mosConfig_fw_user_id}&pdf=".base64_encode($result_curl1)."');</script>";
                            */
                            ob_get_clean();
                            ?>
                            <embed
                            type="application/pdf"
                            src="data:application/pdf;base64,<?php echo base64_encode($result_curl1); ?>"
                            id="pdfDocument"
                            width="100%"
                            height="100%" hidden/>
                            <?php
                            die;
                        }
                        //echo $newfile;
                        ?>
                        <!---------------Label printing.-->
                        <?php
                    }
                }
            }
            ?>
            <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
        </div>
    </body>
</html>