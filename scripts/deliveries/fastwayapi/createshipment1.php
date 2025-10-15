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
        <script src="resourses/jquery.min.js"></script> <!-- 1.4.4 -->
        <script type="text/javascript" src="resourses/jquery.printElement.min.js"></script>
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
        require_once '../configuration.php';
        
        //$cfg = new DatabaseOptions();
        //$link = mysql_connect($cfg->host, $cfg->user, $cfg->pw);
        $link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

        function filter_($data) {
            $data = trim(htmlentities(strip_tags($data)));
            if (get_magic_quotes_gpc())
                $data = stripslashes($data);
            $data = mysql_real_escape_string($data);
            return $data;
        }

        $sender_object = new SenderOptions(filter_($_REQUEST['sender']));

        $shipper_num = $sender_object->shipper_num;

        require_once('bloomexorder.php');
        $order = new BloomexOrder(intval($_REQUEST['id']));
        echo "<pre>";
        var_dump($order);
        function parsePhoneNumber($sString) {
            $aResult = array();
            $aString = array();
            if (preg_match("/\d{10}/", $sString, $aString, PREG_OFFSET_CAPTURE)) {
                $aResult["AreaCode"] = substr($aString[0][0], 0, 3);
                $aResult["Phone"] = substr($aString[0][0], 3, strlen($aString[0][0]) - 3);
            } elseif (preg_match("/\d{3}\.\d{3}\.\d{4}/", $sString, $aString, PREG_OFFSET_CAPTURE)) {
                $aString = explode(".", $aString[0][0]);
                $aResult["AreaCode"] = $aString[0];
                $aResult["Phone"] = $aString[1] . $aString[2];
            } elseif (preg_match("/\d{3}\-\d{3}\-\d{4}/", $sString, $aString, PREG_OFFSET_CAPTURE)) {
                $aString = explode("-", $aString[0][0]);
                $aResult["AreaCode"] = $aString[0];
                $aResult["Phone"] = $aString[1] . $aString[2];
            } elseif (preg_match("/\(\d{3}\)\s\d{3}\-\d{4}/", $sString, $aString, PREG_OFFSET_CAPTURE)) {
                $aString = explode(")", $aString[0][0]);
                $aResult["AreaCode"] = substr($aString[0], 1, 3);
                $aResult["Phone"] = trim($aString[1]);
            } else {
                $aResult["AreaCode"] = substr($sString, 0, 3);
                $aResult["Phone"] = substr($sString, 3, strlen($sString) - 3);
            }
            return $aResult;
        }

        $ddate = array();
        $ddate = explode("-", filter_($_REQUEST['date']));
        try {

            $client = new SoapClient($canpar_business_url, $SOAP_OPTIONS);
            //Complex Type: Address
            //receiver adress
            $receiver = array();
            $receiver['address_line_1'] = $order->_StreetLines1;
            ($order->_StreetLines2) ? $receiver['address_line_2'] = $order->_StreetLines2 : NULL;
            $receiver['city'] = $order->_City;
            $receiver['country'] = 'CA';
            $receiver['name'] = $order->_CompanyName . " " . $order->_PersonName;
            //$phone = parsePhoneNumber($order->_PhoneNumber);
            //$receiver['phone'] = $phone["AreaCode"] . $phone["Phone"];
            $receiver['phone'] = preg_replace("/[^0-9]/", "", $order->_PhoneNumber);
            $receiver['postal_code'] = str_replace(" ", "", $order->_PostalCode);
            $receiver['province'] = $order->_StateOrProvinceCode;
            //Complex Type: Address
            //sender adress
            $sender = array();
            $sender['address_line_1'] = $sender_object->StreetNumber . " " . $sender_object->StreetName;
            $sender['city'] = $sender_object->City;
            $sender['country'] = 'CA';
            $sender['name'] = $sender_object->Name;
            $sender['postal_code'] = str_replace(" ", "", $sender_object->PostalCode);
            $sender['province'] = $sender_object->Province;
            //Complex Type: Package
            $package = "";
            for ($i = 0; $i < filter_($_REQUEST['count']); $i++) {
                $package.="<ns2:packages><ns2:reference>$order->_id</ns2:reference><ns2:reported_weight>" . filter_($_REQUEST['weight']) . "</ns2:reported_weight><ns2:width>1</ns2:width></ns2:packages>";
            }

            $package = new SoapVar($package, XSD_ANYXML);
            //Complex Type: Shipment
            $shipment = array();
            $shipment['delivery_address'] = $receiver;
            $shipment['nsr'] = filter_($_REQUEST['nsr']);
            $shipment['packages'] = $package;
            $shipment['pickup_address'] = $sender;
            $shipment['service_type'] = filter_($_REQUEST['service']);
            $shipment['shipper_num'] = $shipper_num;
            $shipment['shipping_date'] = date("Y-m-d", mktime(0, 0, 1, $ddate[0], $ddate[1], $ddate[2])) . "T17:00:00";
            $shipment['user_id'] = $user_id;

            //Complex Type: ProcessShipmentRq   
            $request = array();
            $request['password'] = $password;
            $request['shipment'] = $shipment;
            $request['user_id'] = $user_id;

            //Method: processShipment
            $rq = array();
            $rq['request'] = $request;
            $rs = $client->processShipment($rq);
            ?>
            <html>
                <head>
                    <title>Canpar - Create Shipment</title>
                </head>
                <body>
                    <div style="font-family:Tahoma, Verdana; font-size:12px;">
                        <div id="loading">The Shipment Info is sending to canpar. Please wait...</div>
                        <?php
                        if ($rs) {
                            ?>	

                            <script type="text/javascript">
                                <!--
                                document.getElementById("loading").style.display = "none";
                                //-->
                            </script>
                            <?php
                            if ($rs->return->error) {
                                ?> 
                                <h3>Error while processing shipment</h3>
                                Error Description: <font color="#ff0000"><?php echo $rs->return->error; ?></font> <br/>
                                RQ/RS:<br/>
                                <?php
                                echo "<pre>Request:";
                                var_dump($client->__getLastRequest());
                                echo "</pre>";
                                echo "<pre>Response:";
                                var_dump($client->__getLastResponse());
                                echo "</pre>";
                            } else {
                                ?>
                                <h2>Shipment submitted succesfull</h2>

                                <?php
                                echo "<!--<pre>Request:";
                                var_dump($client->__getLastRequest());
                                echo "</pre>";
                                echo "<pre>Response:";
                                var_dump($client->__getLastResponse());
                                echo "</pre>-->";

                                if (isset($rs->return->processShipmentResult->message)) {
                                    echo "<br/>Additional Canpar message:" . $rs->return->processShipmentResult->message;
                                }
                                echo "<br/>Estimated delivery date: " . $rs->return->processShipmentResult->shipment->estimated_delivery_date . "<br/>";
                                if ($rs->return->processShipmentResult->shipment->id) {

                                    date_default_timezone_set('Australia/Sydney');
                                    $timestamp = time() /* + ( (-1) * 60 * 60 ) */;
                                    $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);

                                    // Update the Order History.
                                    $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
								VALUES ('$order->_id', '" . SENT_ORDER_STATUS . "', '$mysqlDatetime', '" . htmlspecialchars("ID: " . $rs->return->processShipmentResult->shipment->id, ENT_QUOTES) . "','Canparapi')";

                                    $r = mysql_query($q);
                                    if (!$r) {
                                        echo "order history update failed!";
                                    }
                                    echo "<!--<query> $q </query>" . "error:" . mysql_error() . "-->";
                                    //GET CANPAR LABEL

                                    $printer = new SoapClient($canpar_business_url, $SOAP_OPTIONS);

                                    $printer_request['id'] = $rs->return->processShipmentResult->shipment->id;
                                    /// $printer_request['horizontal'] = '1';
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
                                    if ($printer_rs->return->error) {
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
                                    }
                                }
                            }
                        }
                        ?>
                        <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/><br/>
                    </div>
                </body>
            </html>
            <?php
        } catch (SoapFault $fault) {
            print_r($fault);
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
        ?>
<?php mysql_close(); ?>