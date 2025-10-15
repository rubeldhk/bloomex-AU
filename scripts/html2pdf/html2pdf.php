<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('_VALID_MOS', 'true');
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once 'html2pdf.class.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

ob_start();
$id = (int) $_GET['id'];
$q = "SELECT `o`.`orders`, `o`.`company_discount`, `i`.* FROM `tbl_company_parse_orders` AS `o`
    INNER JOIN `jos_vm_user_info` AS `i` ON `i`.`user_id`=`o`.`user_id` AND `i`.`address_type`='BT'
    WHERE `o`.`id`=" . $id . "";
$company_sql = $mysqli->query($q);
if (!$company_sql) {
    printf("Errormessage: %s\n", $mysqli->error);
    printf("Query: %s\n", $q);
}
if ($company_sql->num_rows > 0) {
    $company_obj = $company_sql->fetch_object();

    $total_array = array();
    $total_array['delivery_fee'] = 0;
    $total_array['coupon_discount'] = 0;
    $total_array['corporate_discount'] = 0;
    $total_array['subtotal'] = 0;
    $total_array['delivery_fee'] = 0;
    $total_array['tax'] = 0;
    $total_array['total'] = 0;

    $orders_sql = $mysqli->query("SELECT * FROM `jos_vm_orders` WHERE `order_id` IN (" . implode(',', unserialize($company_obj->orders)) . ")");

    while ($order_out = $orders_sql->fetch_object()) {
        $total_array['delivery_fee'] += $order_out->order_shipping;
        $total_array['coupon_discount'] += $order_out->coupon_discount;
        $total_array['corporate_discount_one'] = $order_out->order_subtotal * ($company_obj->company_discount / 100);
        $total_array['subtotal'] += $order_out->order_subtotal;
        $total_array['tax'] += $order_out->order_tax;
        $total_array['total'] += $order_out->order_total; }

    $total_array['corporate_discount'] = $total_array['subtotal'] * ($company_obj->company_discount / 100);

    $items_array = array();

    $items_sql = $mysqli->query("SELECT `product_id`, `order_item_name`, `order_item_sku`, `order_item_name`, `product_item_price`, `product_quantity`, `product_final_price` FROM `jos_vm_order_item` WHERE `order_id` IN (" . implode(',', unserialize($company_obj->orders)) . ")");

    while ($item_out = $items_sql->fetch_object()) {
        if (!array_key_exists($item_out->product_id, $items_array)) {
            $items_array[$item_out->product_id] = array();
            $items_array[$item_out->product_id]['quantity'] = $item_out->product_quantity;
            $items_array[$item_out->product_id]['name'] = $item_out->order_item_name;
            $items_array[$item_out->product_id]['sku'] = $item_out->order_item_sku;
            $items_array[$item_out->product_id]['price'] = $item_out->product_final_price;
        } else {
            $items_array[$item_out->product_id]['quantity'] += $item_out->product_quantity;
        }
    }
    ?>
    <html>
        <head>
            <style>
                .item_table td, th, thead {
                    text-align: left;
                }
                .item_table thead {
                    background-color: #E0E0E0; 
                }
                .item_table th {
                    border: 1px solid;
                    padding: 5px;
                }
                .item_table td {
                    background-color: #DFE8EF; 
                    padding: 5px;
                }
                .price_table td {
                    background-color: #CBDCEE;
                    padding: 5px 10px 5px 10px;
                    text-align: center;
                    font-size: 20px;
                } 
                .logo img {
                    max-width: 100px;
                }
            </style>
        </head>
        <body>
            <div style="text-align: center;" class="logo">
                <img width="100px" src="https://bloomex.com.au/templates/bloomex7/images/bloomexlogo.png" alt="bloomex" />
            </div> 
            <div style="text-align: left; margin-bottom: 10px; margin-left: 10px;">
                <table>
                    <tr>
                        <td colspan="2">Customer information:</td>
                    </tr>
                    <tr>
                        <td>Company:</td>
                        <td><?php echo $company_obj->company; ?></td>
                    </tr>
                    <tr>
                        <td>Full name:</td>
                        <td><?php echo $company_obj->first_name . ' ' . $company_obj->last_name; ?></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><?php echo $company_obj->street_number . ' ' . $company_obj->street_name; ?></td>
                    </tr>
                    <tr>
                        <td>City:</td>
                        <td><?php echo $company_obj->city; ?></td>
                    </tr>
                    <tr>
                        <td>Postal code:</td>
                        <td><?php echo $company_obj->zip; ?></td>
                    </tr>
                    <tr>
                        <td>Country:</td>
                        <td><?php echo $company_obj->country; ?></td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td><?php echo $company_obj->phone_1; ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?php echo $company_obj->user_email; ?></td>
                    </tr>
                </table>
            </div>
            <div style="text-align: center; margin-left: 10px;">
                <table width="100%" class="item_table">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                Product name
                            </th>
                            <th>
                                SKU
                            </th>
                            <th>
                                Quantity
                            </th>
                            <th>
                                Product Price
                            </th>
                            <th>
                                Total
                            </th>
                        </tr>
                    </thead>
                    <?php
                    $item_i = 1;

                    foreach ($items_array as $item) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $item_i; ?>.
                            </td>
                            <td>
                                <?php echo $item['name']; ?>
                            </td>
                            <td>
                                <?php echo $item['sku']; ?>
                            </td>
                            <td>
                                <?php echo $item['quantity']; ?>
                            </td>
                            <td>
                                $<?php echo number_format($item['price'], 2); ?>
                            </td>
                            <td>
                                $<?php echo number_format($item['quantity'] * $item['price'], 2); ?>
                            </td>
                        </tr>
                        <?php
                        $item_i++;
                    }
                    ?>
                </table>
            </div>
            <div style="margin-top: 10px; margin-left: 10px;">
                <table width="100%">
                    <tr>
                        <td width="50%" style="text-align: left;" align="left">
                            Bloomex Pty Ltd <br/>
                            Unit 9, 12-18 Victoria Street East<br/>
                            Lidcombe, NSW<br/>
                            2141<br/>
                            ABN 27147609443
                        </td>
                        <td width="50%" style="text-align: right;" align="right">
                            <table class="price_table">
                                <tr>
                                    <td>
                                        Sub total
                                    </td>
                                    <td>
                                        $<?php echo number_format($total_array['subtotal'], 2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Coupon discount
                                    </td>
                                    <td>
                                        -$<?php echo number_format($total_array['coupon_discount'], 2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Corporate discount
                                    </td>
                                    <td>
                                        -$<?php echo number_format($total_array['corporate_discount'], 2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Delivery Fee
                                    </td>
                                    <td>
                                        $<?php echo number_format($total_array['delivery_fee'], 2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total
                                    </td>
                                    <td>
                                        $<?php echo number_format($total_array['total'], 2); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
    </html>
    <?php
    $mysqli->close();
}
$content = ob_get_clean();
//die;
try {

    $html2pdf = new HTML2PDF('P', 'A4', 'fr');
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($content);
    $filename = 'bloomexau' . $id . '-' . date('mdy') . '.pdf';
    $filename_path = 'bloomex.ca/pdf_files/' . $filename;
    $real_file_path = 'https://' . $mosConfig_email_sender_ftp_host . '/' . $filename_path;
    $temp_file = tempnam(sys_get_temp_dir(), 'Tux') . '.pdf';
    $handle = fopen($temp_file, "w");
    fwrite($handle, "writing to tempfile");
    fclose($handle);
    $html2pdf->Output($temp_file, 'F');

    header("Content-type:application/pdf");
    header("Content-Disposition:inline;filename='$temp_file");
    echo file_get_contents($temp_file);
    die();
    ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass, $temp_file, $filename_path);


    require_once($mosConfig_absolute_path . '/administrator/components/com_virtuemart/classes/language.class.php');
    require_once($mosConfig_absolute_path . '/administrator/components/com_virtuemart/classes/ps_main.php');
    define('ADMINPATH', $mosConfig_absolute_path . '/administrator/components/com_virtuemart/');
    define('CLASSPATH', ADMINPATH . 'classes/');
    die("hello kitty" . __LINE__ . $real_file_path);

    header("Content-type:application/pdf");
    header("Content-Disposition:inline;filename='$filename");
    echo file_get_contents($real_file_path);

//    $email_to = 'danielyanlevon89@mail.ru';
//    $second_email = 'danielyanlevon89@gmail.com';
//
//    $subject = "Bulk Company Orders Invoice";
//    $html_send = "<a href='".$real_file_path."' target='_blank'>click</a> here to see the file";
//    $o = vmMail($mosConfig_mailfrom, $mosConfig_fromname, $email_to, $subject, $html_send, '',true,$second_email);
} catch (HTML2PDF_exception $e) {
    echo 'HTML2pdf.' . $e;
    exit;
}

function ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass, $photo, $filename_path) {

    $ftp = ftp_connect($mosConfig_email_sender_ftp_host);
    if (ftp_login($ftp, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass)) {
        ftp_pasv($ftp, true);


        $trackErrors = ini_get('track_errors');
        ini_set('track_errors', 1);

        $res = ftp_size($ftp, $filename_path);
        if ($res == -1) {


            if (!@ftp_put($ftp, $filename_path, $photo, FTP_BINARY)) {
                die("error while uploading file");
            }
        } else {
            return false;
        }
    } else {
        die("Could not login to FTP account");
    }



    ftp_close($ftp);
}
