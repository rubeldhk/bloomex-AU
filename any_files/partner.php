<?php
include (__DIR__."/../configuration.php");

if (!$mosConfig_host) 
{
    die('no config');
}
$link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password,$mosConfig_db);
if (mysqli_connect_errno()) {
    die ("Failed to connect to MySQL: " . mysqli_connect_error());
}

?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Bloomex order processing</title>
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script> 
        <script type="text/javascript">
            var accept = 0;
            var decline = 0;

            function showAccept()
            {
                alert('Please fill comment field');
                
                $('#text').focus();
            }
        </script>
    </head>

    <body>
        <style>
        body 
        {
            background-color: #F9F0F0;
            width: 800px;
            margin: auto;
            font-size: 16px;
            font-family: Arial;
        }
        
        .content
        {
            background-color: #FFFFFF;
            min-height: 400px;
            padding: 20px;
        }
        
        .accept_button
        {
            padding: 10px 5px;
            background-color: green;
            color: #fff;
            text-decoration: none;
        }
        
        .decline_button
        {
            padding: 10px 5px;
            background-color: red;
            color: #fff;
            text-decoration: none;
        }
        
        .activeClass
        {
            border: 4px solid blue;
        }
        #text{
            width: 100%;
            min-height: 100px;
            margin: 20px 0;
            display: block;
            padding: 0;
        }
        
        </style>
    <?php

    Switch ($_REQUEST['act'])
    {
        case 'save':
            
            $key = mysqli_real_escape_string($link,$_REQUEST['key']);
            
            if (!empty($key))
            {
                $comment = mysqli_real_escape_string($link,$_REQUEST['text']);
                $confirm = ($_REQUEST['confirm'] ? 1 : 0);


                $q_partner = mysqli_query($link,"SELECT tbl_local_parthners_orders.partner_id, order_id, partner_name, partner_price, confirm, `key`  
                FROM tbl_local_parthners_orders
                LEFT JOIN tbl_local_parthners  on tbl_local_parthners_orders.partner_id = tbl_local_parthners.partner_id
                WHERE tbl_local_parthners_orders.`key` = '" . $key . "'
                ");

                $a_partner = mysqli_fetch_array($q_partner);

                $partner_id = (int)$a_partner['partner_id'];
                $order_id = (int)$a_partner['order_id'];
                $price = $a_partner['partner_price'];
                $pass = $a_partner['key'];

                if ($pass == $key) 
                {
                    $cf = ($confirm == 1) ? '1' : '-1';

                    mysqli_query($link,"UPDATE `tbl_local_parthners_orders` set confirm=" . $cf . ", mtime=null WHERE order_id = '$order_id' and partner_id = '$partner_id'");

                    $order_status = ($confirm > 0) ? 'F' : 'L';
                    $order_status_name = ($confirm > 0) ? 'Confirm Partner' : 'Cancel Partner';
                    mysqli_query($link,"UPDATE jos_vm_orders SET
                    order_status='" . $order_status . "' 
                    WHERE order_id='" . $order_id . "'");


                    mysqli_query($link,"INSERT INTO tbl_local_parthners_orders_history (order_id, partner_id, status, price, comments, time)
                    VALUES (". $order_id . ", " . $partner_id . ", '" . $order_status_name . "',  '" . $price . "', '" . $comment . "', NOW() ) ");

                    $old_tz = date_default_timezone_get();
                    date_default_timezone_set('Australia/Sydney');
                    $mysqlDatetime = date("Y-m-d G:i:s");
                    date_default_timezone_set($old_tz);
            
                    mysqli_query($link,"INSERT INTO jos_vm_order_history 
                    (order_id, order_status_code, date_added, comments) VALUES (
                    " . $order_id . ", '" . $order_status . "', '".$mysqlDatetime."', 'Order " . (($confirm > 0) ? 'confirmed' : 'declined' ) . " by Partner " . mysqli_real_escape_string($link,$a_partner['partner_name'])." With comment: ".$comment . "')");

                    echo '<div class="content">Thank you! You can close this window now.</div>';
                }
                else
                {
                    ?>
                    <div class="content">
                        You do not have access to or have already taken a decision on this request. Please contact us to access or change the decision. Thank you!
                    </div>
                    <?php
                }
            }
        break;
            
        default:
            
            $key = mysqli_real_escape_string($link,$_GET['key']);

            if (!empty($key))
            {
                $confirm = '';
                if (isset($_GET['confirm']))
                {
                    ?>
                    <script type="text/javascript">
                        window.onload=function(){
                            showAccept();
                        }

                    </script>
                    <?php
                }
                $q_partner = mysqli_query($link,"SELECT tbl_local_parthners_orders.partner_id, order_id, partner_name, partner_price, confirm, `key`  FROM tbl_local_parthners_orders
                LEFT JOIN tbl_local_parthners  on tbl_local_parthners_orders.partner_id = tbl_local_parthners.partner_id
                WHERE tbl_local_parthners_orders.`key` = '" . $key . "'");

                $a_partner = mysqli_fetch_array($q_partner);

                $partner_id = (int)$a_partner['partner_id'];
                $price = $a_partner['partner_price'];
                $order_id = (int)$a_partner['order_id'];
                $confirmed = (int)$a_partner['confirm'];
                $pass = $a_partner['key'];

                if(!$order_id)
                   die('Order ID is wrong');

                if ($pass == $key) 
                {

                    $a_partner = mysqli_fetch_array(mysqli_query($link,"SELECT partner_name, partner_email, partner_phone, price FROM tbl_local_parthners_orders, tbl_local_parthners WHERE tbl_local_parthners_orders.order_id = '" . $order_id . "' AND tbl_local_parthners_orders.partner_id = tbl_local_parthners.partner_id "));

                    $a_order = mysqli_fetch_array(mysqli_query($link,"SELECT * FROM jos_vm_orders AS O, jos_vm_order_occasion AS OC WHERE O.order_id='" . $order_id . "' AND O.customer_occasion = OC.order_occasion_code"));

                    $a_order_status = mysqli_fetch_array(mysqli_query($link,"SELECT order_status_name FROM jos_vm_order_status WHERE order_status_code = '" . $a_order['order_status'] . "'"));

                    $a_user_info = mysqli_fetch_array(mysqli_query($link,"SELECT * FROM jos_vm_order_user_info WHERE user_id='" . $a_order['user_id'] . "'  AND order_id='" . $a_order['order_id'] . "' AND address_type='ST'"));

                    $a_state = mysqli_fetch_array(mysqli_query($link,"SELECT S.state_name FROM jos_vm_state S, jos_vm_country AS C WHERE C.country_id = S.country_id AND C.country_3_code = '" . $a_user_info['country'] . "' AND  S.state_2_code = '" . $a_user_info['state'] . "'"));

                    $a_country = mysqli_fetch_array(mysqli_query($link,"SELECT country_name FROM jos_vm_country WHERE country_3_code = '".$a_user_info['country']."'"));
                ?>

                    <div class="content">
                        <img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/bloomexlogo.png" width="150px" style="float: left;">
                            <span style="font-style: italic;
                            font-size: 26px;
                            float: left;
                            padding: 20px;
                            color: green;">Bloomex order<br/>processing</span>
                            <div style="padding-bottom: 10px;"></div>
                        <table border="0" cellspacing="0" cellpadding="2" width="100%">
                        <!-- begin customer information --> 

                        <tr> 
                            <td>ABN:</td>
                            <td>27 147 609 443</td>
                        </tr>
                        <tr> 
                            <td>Order Number:</td>
                            <td><?php echo $a_order['order_id']; ?></td>
                        </tr>
                        <tr> 
                            <td>Order Date:</td>
                            <td><?php echo date("d-M-Y H:i", $a_order['cdate']); ?></td>
                        </tr>
                        <tr> 
                            <td><b>Delivery Date:</b></td>
                            <td><b><?php echo date($a_order['ddate']); ?></B></td>
                        </tr>

                        <tr> 
                            <td>Order Status:</td>
                            <td><?php echo $a_order_status['order_status_name']; ?></td>
                        </tr>

                        <tr valign="top"> 
                            <td width="50%"> 
                                <!-- Begin ShipTo --> 
                                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                                    <tr> 
                                        <td colspan="2"><strong>Delivery Information</strong></td>
                                    </tr>
                                    <tr> 
                                        <td>Company :</td>
                                        <td><?php echo $a_user_info['company']; ?></td>
                                    </tr>
                                    <tr> 
                                        <td>Full Name :</td>
                                        <td><?php echo $a_user_info['first_name'].' '.$a_user_info['middle_name'].' '.$a_user_info['last_name']; ?></td>
                                    </tr>
                                    <tr valign="top"> 
                                        <td>Address :</td>
                                        <td><?php echo $a_user_info['address_1'].'<br />'.$a_user_info['address_2']; ?></td>
                                    </tr>
                                    <tr> 
                                        <td>City :</td>
                                        <td><?php echo $a_user_info['city']; ?></td>
                                    </tr>  
                                    <tr> 
                                        <td>State :</td>
                                        <td><?php echo $a_state['state_name']; ?></td>
                                    </tr>
                                    <tr> 
                                        <td>Postcode :</td>
                                        <td><?php echo $a_state['zip']; ?></td>
                                    </tr>
                                    <tr> 
                                        <td>Country :</td>
                                        <td><?php echo $a_country['country_name']; ?></td>
                                    </tr>
                                    <tr> 
                                        <td>Phone :</td>
                                        <td><?php echo $a_user_info['phone_1']; ?></td>
                                    </tr>

                                </table>

                            </td>
                        </tr>
                        <tr> 
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>

                        <tr class="sectiontableheader"> 
                            <th align="left" colspan="2">Order Items</th>
                        </tr>
                        <tr> 
                            <td colspan="2"> 
                                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                                    <tr align="left"> 
                                        <th><b>Img</b></th>
                                        <th><b>Qty</b></th>
                                        <th> <b> Name</b></th>
                                        <th>SKU</th>
                                    </tr>
                                    <?php
                                    $OrderItems = '  ';

                                    $q_items = mysqli_query($link,"SELECT * FROM jos_vm_order_item WHERE jos_vm_order_item.order_id='" . $order_id . "'");

                                    $subtotal = 0;

                                    while ($a_items = mysqli_fetch_array($q_items))
                                    {
                                        $OrderItems.='<tr align="left" valign="top">';

                                        $a_product = mysqli_fetch_array(mysqli_query($link,"
                                                    SELECT p.product_desc,`s`.`medium_image_link_webp` 
                                                    FROM jos_vm_product as p 
                                                    LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
                                                    WHERE p.product_id = " . $a_items['product_id']."
                                                    "));


                                        $OrderItems.=' <td ><img src="'.$mosConfig_aws_s3_bucket_public_url . $a_product['medium_image_link_webp'].'" style="max-width: 200px;"/></td>';
                                        $OrderItems.=' <td width="10%"><b><u> ' . $a_items['product_quantity'] . '</u></b></td> <td width="45%"><b><u>';
                                        $OrderItems.=$a_items['order_item_name'] . "</u></b><hr>";
                                        $OrderItems.="<i><font size=1>" . $a_product['product_desc'] . "</font></i></td>";
                                        $OrderItems.='<td width="10%">' . $a_items['order_item_sku'] . '</td></tr>';
                                    }


                                    ?>

                                    <?php echo $OrderItems; ?>
                                </table>
                                <br/>
                                <table width="100%">
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr class="sectiontableheader">
                                        <td align="left" colspan="1">Customer's note</td>
                                        <td><?php echo $a_order['customer_note']; ?></td>
                                    </tr>
                                    <tr>
                                        <td> Occasion :</td>
                                        <td><?php echo $a_order['order_occasion_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Instructions and comments </b><br/><small> (we keep this information confidential)</small> :</td>
                                        <td><?php echo $a_order['customer_comments']; ?></td>
                                    </tr>
                                    <tr class="sectiontableheader">
                                        <th align="left">Order Total:</th>
                                        <th align="left">$<?php echo $a_partner['price']; ?></th>
                                    </tr>
                                </table>

                        </tr>


                    </table>
                       
                        <div id="accept" style="padding-top: 20px;">
                            Please leave your comment (if any) here:
                            <br/>
                            <form action="?act=save" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $a_order['order_id']; ?>">
                            <input type="hidden" name="partner_id" value="<?php echo $partner_id; ?>">
                            <input type="hidden" name="key" value="<?php echo $key; ?>">
                            <textarea name="text" id="text"></textarea>
                            <br/>
                            <div style="text-align: center;">
                                <input type="submit" name="confirm" class="accept_button" value="Accept Order" />
                                <input type="submit" name="decline" class="decline_button" value="Decline Order" />
                            </div>
                            </form>
                        </div>

                    </div>

                <?php
                }
                else
                {
                    ?>
                    <div class="content">
                        You do not have access to or have already taken a decision on this request. Please contact us to access or change the decision. Thank you!
                    </div>
                    <?php
                }
            }
        break;
    }

    ?>

    </body>
</html>
