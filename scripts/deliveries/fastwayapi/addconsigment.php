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
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        $order_id = (int) $_REQUEST['order_id'];
        require_once 'bloomexorder.php';
        $order = new BloomexOrder($order_id);


        $result_qq = $mysqli->query("select * from jos_vm_order_history where order_id = '$order->_id' and order_status_code='H'");

        $result_qqq = $mysqli->query("select * from jos_vm_order_history where order_id = '$order->_id' and order_status_code='J'");

        if ($result_qq->num_rows > $result_qqq->num_rows) {
            ?>
            <h2>Fast label already sent</h2>
            <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
            <?php
            die();
        }

        $wh = $order->filter($order->_warehouse);

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
            if ($result) {
                $rs = json_decode($result, true); // json to object
            } else {
                die("Fastway didn't answer anything. Please check address.");
            }
        }
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
                if (isset($rs['error']) && $rs['error'] != '') {
                    ?> 
                    <h3>Error while processing shipment</h3>
                    Error Description: <font color="#ff0000"><?php echo $rs['error']; ?></font> <br/>
                    RQ/RS:<br/>
                    <?php
                    echo "<pre>Request:";
                    var_dump($curl_options);
                    echo "</pre>";
                    echo "<pre>Response:";
                    var_dump($result);
                    echo "</pre>";
                } else {
                    ?>
                    <h2>Shipment submitted succesfull</h2>
                    <?php
                    if ($rs['result']['ConsignmentID']) {
                        date_default_timezone_set('Australia/Sydney');
                        $timestamp = time(); // + ( (-1) * 60 * 60 )
                        $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);


                        $mysqli->query("UPDATE `jos_vm_orders` 
                SET 
                    `order_status`='C'
                WHERE `order_id`=" . $order->_id . "
                ");

                        $history_comment = $mysqli->real_escape_string("Fastway ID: " . $rs['result']['ConsignmentID']);
                        if (isset($rs['result']['Items'][0]['labels'][0]['labelNumber'])) {
                            $history_comment .= $mysqli->real_escape_string(" labelNumber: " . $rs['result']['Items'][0]['labels'][0]['labelNumber']);
                        }

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
                    " . $order->_id . ",
                    'C', 
                    '" . $mysqlDatetime . "',
                    '" . $mysqli->real_escape_string($_REQUEST['sender']) . "', 
                    '" . $mysqli->real_escape_string($history_comment) . "')
                ");


                        $query = "INSERT INTO `jos_vm_orders_deliveries`
                    (
                        `order_id`,
                        `delivery_type`,
                        `dateadd`,
                        `pin`,
                        `shipment_id`,
                        `active`
                    ) 
                    VALUES (
                        " . $order->_id . ",
                        " . $mysqli->real_escape_string($_REQUEST['delivery_id']) . ",
                        '" . $mysqlDatetime . "',
                        '" . $mysqli->real_escape_string($rs['result']['Items'][0]['labels'][0]['labelNumber']) . "',
                        '" . $mysqli->real_escape_string($rs['result']['ConsignmentID']) . "',
                        '1'
                    )";
                        $mysqli->query($query);

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
                        if (isset($rs1['error']) && $rs1['error'] != '') {

                            echo ('Error : ' . $rs1['error']);
                        } else {

                            ob_get_clean();
                            ?>
                            <embed
                                type="application/pdf"
                                src="data:application/pdf;base64,<?php echo base64_encode($result_curl1); ?>"
                                id="pdfDocument"
                                width="100%"
                                height="100%" />
                            <script type="text/javascript">
                                window.opener.jQuery(".delivery_icon_<?php echo $order->_id; ?>").removeClass('default').attr('href', '').attr('order_id', "").find('img').attr('src', '/templates/bloomex7/images/deliveries/FastWay_logo.png');
                                window.opener.jQuery(".delivery_icon_span_<?php echo $order->_id; ?>").html('Updated');
                            </script>
                <?php
                die;
            }
        }
    }
}
?>
            <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
        </div>
    </body>
</html>