<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$order_id = (int) $_REQUEST['order_id'];
require_once 'bloomexorder.php';
$order = new BloomexOrder($order_id);
$warehouse = $order->_warehouse;
$sender = $order->filter($_REQUEST['sender']);
$delivery_id = $order->filter($_REQUEST['delivery_id']);
session_name(md5($mosConfig_live_site));
session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Fast Label</title>
        <link rel="stylesheet" href="./resourses/style_t.css" />
        <script>
            var count_div = 1;
            var count_fs = 1;
            function AddItem() {
                var firstform = document.getElementById('fs1');
                var button = document.getElementById('add');
                count_fs++;
                var newitem = '<div id="pi' + count_div + '"><label for="reference' + count_div + '">Piece Reference:</label><input class="width" type="text" value="' + document.getElementsByName('order_id')[0].value + '" name="Reference[' + count_div + ']" id="reference_' + count_div + '" maxlength="32"/></div>';
                newitem += '<div id="pi' + count_div + '"><label for="weight' + count_div + '">Piece Weight:</label><input type="number" value="3" name="Weight[' + count_div + ']" id="weight_' + count_div + '"/> lb.</div>';
                newitem += '<div id="pi' + count_div + '"><label for="count' + count_div + '">Total Pieces:</label><input type="number" value="1" name="Count[' + count_div + ']" id="count_' + count_div + '"/> pcs.</div><br>';
                if (packaging == 1) {
                    newitem += '<div id="pi' + count_div + '"><label for="packaging' + count_div + '">Use medium flat rate:</label><input type="checkbox" value="1" name="Packaging[' + count_div + ']" id="packaging_' + count_div + '" checked></div><br>';
                }
                count_div++;

                var newnode = document.createElement('fieldset');

                newnode.id = 'fs1' + count_fs;
                newnode.innerHTML = newitem;
                firstform.insertBefore(newnode, button);
            }
            function DelItem() {
                if (count_fs > 1) {
                    var firstform = document.getElementById('fs1');
                    var last = document.getElementById('fs1' + count_fs);
                    firstform.removeChild(last);
                    count_fs--;
                    count_div = count_div - 3;
                }
            }
        </script>
    </head>
    <body><br/><br/><br/><br/>
        <div id="Fedex">
            <form id="myForm" class="form" action="addconsigment.php" method="POST">
                <?php

                $label_query = $mysqli->query("SELECT
                    `label_type` 
                FROM `fastway_label_postcodes` 
                WHERE 
                    `warehouse` LIKE '" . $mysqli->real_escape_string($warehouse) . "' 
                    AND 
                    `city` LIKE '$order->_City' 
                    AND 
                    `postal_code` like '$order->_PostalCode'
                        AND ((label_type like 'RED') OR (label_type like 'ORANGE') OR (label_type like 'GREEN'))
                LIMIT 1");

                $packaging = 0;
                if ($label_query->num_rows > 0) {
                    $packaging = 1;
                }

                ?>

                <script type="text/javascript">
                    var packaging = <?php echo $packaging; ?>;
                </script>
                <div id="parentId">
                    <fieldset id="fs1">
                        <legend>Packages</legend>
                        <fieldset id="fs11">
                            <div id="pi1"><label for="reference">Piece Reference:</label><input class="width" type="text"  size="40" value="<?php echo $order_id; ?>" name="Reference[0]" id="reference_0" maxlength="32"/></div>
                            <div id="pi2"><label for="weight">Piece Weight:</label><input type="number" value="3" name="Weight[0]" id="weight_0"/> lb.</div>
                            <div id="pi3"><label for="count">Total Pieces:</label><input type="number" value="1" name="Count[0]" id="count_0"/> pcs.</div>
                            <?php
                            if ($packaging == 1) {
                                ?>
                                <div id="pi4"><label for="packaging">Use medium flat rate:</label><input type="checkbox" value="1" name="Packaging[0]" id="packaging_0" checked></div>
                                <?php
                            }
                            ?>
                        </fieldset>
                        <input type="button" value="+" onClick="AddItem();" ID="add"></input>
                        <input type="button" value="-" onClick="DelItem();" ID="del"></input>
                    </fieldset>
                </div>
                <input class="submit" type="submit" value="Send to FastWay"/>          
                <input  type="hidden" name="order_id" value="<?php echo $order_id; ?>"/>
                <input  type="hidden" name="sender" value="<?php echo $sender; ?>"/>
                <input  type="hidden" name="delivery_id" value="<?php echo $delivery_id; ?>"/>
            </form>
        </div>
    </body>
</html>
