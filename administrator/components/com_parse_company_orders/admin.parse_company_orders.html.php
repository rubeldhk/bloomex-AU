
<?php
/**
 * @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Category
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_CompanyParseOrders {

    //============================================= Location OPTION ===============================================
    static function showCompanyParseOrders(&$rows, &$pageNav, $option, $lists) {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Parsed Company Orders</th>
                </tr>
                <tr>
                    <td align="right" style="padding:0px 20px 10px 0px;">
                        <b>Filter By:&nbsp;</b>
                        <input type="text" value="<?php echo $lists['filter_key']; ?>" name="filter_key" size="30" />
                        <select name="filter_status" id="filter_status" class="inputbox" size="1" onchange="document.adminForm.submit();">
                            <option value="-1" selected="selected">------ Select Status ------</option>
                            <option <?php if($lists['filter_status']=='pending') echo 'selected'; ?> value="pending">Pending</option>
                            <option <?php if($lists['filter_status']=='paid') echo 'selected'; ?> value="paid">Paid</option>
                        </select>
                    </td>

                </tr>
            </table>
            <table class="adminlist table_parsed_orders">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
                    <th width="10%" nowrap="nowrap">ID</th>
                    <th width="40%" nowrap="nowrap">Company Name</th>
                    <th width="10%" nowrap="nowrap">Orders date</th>
                    <th width="30%" nowrap="nowrap">Parsed Orders Count</th>
                    <th width="10%" nowrap="nowrap">Operator</th>
                    <th width="20%" nowrap="nowrap">Status</th>
                    <th width="20%" nowrap="nowrap">Total Price</th>
                    <th width="5%">&nbsp;</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link = 'index2.php?option=com_parse_company_orders&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosHTML::idBox($i, $row->id);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Company"><b style="font:bold 11px Tahoma;"><?php echo $row->id; ?></b></a></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Company"><b style="font:bold 11px Tahoma;"><?php echo $row->company_name; ?></b></a></td>
                        <td><?php echo $row->orders_date; ?></td>
                        <td><?php echo $row->parsed_orders_count; ?></td>
                        <td><?php echo $row->operator; ?></td>
                        <td><?php echo $row->status; ?></td>
                        <td><?php echo round($row->total_price, 2); ?></td>

                         <td>&nbsp;</td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script>
            $(function() {
            $('.upload_xlsx').click(function(){
                $( "#dialog" ).dialog({
                    width: 1200,
                    left:50
                });
                $( "#fileToUploadform" ).show();
            })
            $('.parse_file').click(function(){
                //$('.parse_file').val('Processing...').attr('disabled','disabled');
                var file_data = $('#xlsxfileform').prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                form_data.append('option', "<?php echo $option; ?>");
                form_data.append('task', "parse_xlsx");
                $.ajax({
                    url: './index2.php',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    async: true,
                    success: function(data){
                        {
                           if(data){
                               if(data[0]!='invalid file') {

                                   var html = "<p>Below you see  information from file.If you want to send more than one product to same address You must  just add new line with new sku.<br>" +
                                       "If you parsed product and don't see him below please check his in our database. <br> If anything is wrong you must change in file and re-upload.  Please use correct file format.</p>";
                                   html = html + "<table class='parsing_result_table'>";
                                   html = html + "<tr><td><b>Email</b></b></td><td>" + data.email + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Company Name</b></td><td>" + data.billing_company_name + "</td></tr>";
                                   html = html + "<tr><td><b>Billing First Name</b></td><td>" + data.billing_first_name + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Last Name</b></td><td>" + data.billing_last_name + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Suite</b></td><td>" + data.billing_suite + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Street Number</b></td><td>" + data.billing_street_number + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Street Name</b></td><td>" + data.billing_street_name + "</td></tr>";
                                   html = html + "<tr><td><b>Billing City</b></td><td>" + data.billing_city + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Zip</b></td><td>" + data.billing_zip + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Country</b></td><td>" + data.billing_country + "</td></tr>";
                                   html = html + "<tr><td><b>Billing State</b></td><td>" + data.billing_state + "</td></tr>";
                                   html = html + "<tr><td><b>Billing Phone</b></td><td>" + data.billing_phone + "</td></tr>";
                                   var table_orders = 'Please check Sku. There is not information for that Sku';
                                   if(data.orders[0]){
                                   table_orders = "<table>";

                                   var table_orders_th = "<tr>";
                                   $.each(data.orders[0], function (key, value) {
                                       table_orders_th = table_orders_th + "<th>" + key.replace(/_/g, " ") + "</th>";
                                   })
                                   table_orders_th = table_orders_th + "</tr>";
                                   table_orders = table_orders + table_orders_th

                                   $.each(data.orders, function (key, value) {
                                       table_orders = table_orders + "<tr>";
                                       $.each(value, function (k, v) {
                                           if (k == 'shipping_product_sku') {
                                               var table_sku = '';
                                               if (v != '') {
                                                   table_sku = table_sku + "<table><tr><th>Product Sku</th><th>Petite/Deluxe/Supersize</th><th>Quantity</th></tr>";
                                                   $.each(v, function (d, s) {
                                                       var deluxe_supersize='';
                                                       if(s.deluxe_supersize=='S'){
                                                           deluxe_supersize="Supersize";
                                                       }else if (s.deluxe_supersize=='D'){
                                                           deluxe_supersize="Deluxe";
                                                       }else if (s.deluxe_supersize=='P'){
                                                            deluxe_supersize="Petite";}
                                                       else if (s.deluxe_supersize=='SNPC'){
                                                           deluxe_supersize="Supersize";
                                                       }else{
                                                           deluxe_supersize="";
                                                       }
                                                       table_sku = table_sku + "<tr><td><b>" + s.sku + "</b></td><td>" + deluxe_supersize + "</td><td>" + s.quantity + "</td></tr>";
                                                   })
                                                   table_sku = table_sku + "</table>";
                                               }
                                               table_orders = table_orders + "<td>" + table_sku + "</td>";
                                           } else {
                                               table_orders = table_orders + "<td>" + v + "</td>";
                                           }
                                       })
                                       table_orders = table_orders + "</tr>";
                                   });

                                   table_orders = table_orders + "</table>";
                               }
                                   html=html+"<tr><td colspan='2'>"+table_orders+"</td></tr>";
                                   html=html+"</table>";

                                   $('.parse_file').val('Upload').removeAttr('disabled');
                                   $('.parsing_result').html(html);
                                   $('.save_parsed_file').show().val('Save').removeAttr('disabled');

                               }else{
                                   $('.parsing_result').html(data[0]+" Please change file");
                                   $('.parse_file').val('Upload').removeAttr('disabled');
                                   $('.save_parsed_file').hide();
                               }

                           }else{
                               $('.parsing_result').html("Please change file");
                               $('.parse_file').val('Upload').removeAttr('disabled');
                               $('.save_parsed_file').hide();
                           }
                        }
                    }
                });

            })

               $('.save_parsed_file').click(function(){
                   $('.save_parsed_file').val('Processing...').attr('disabled','disabled');
                    $.post("./index2.php", {option: "<?php echo $option;?>",task: "save_parsed_results"}, function(result){
                        if(result){
                               var  obj = JSON.parse(result);
                            if($.trim(obj.result)!='error'){
                                $('.parsing_result').html(obj.msg);
                                $('.save_parsed_file').hide().val('Save').removeAttr('disabled');
                            }else{
                                $('.save_parsed_file').hide();
                                $('.parsing_result').html(obj.msg);
                            }
                        }else{
                            $('.save_parsed_file').hide();
                            $('.parsing_result').html("No data to add into database.Please check file and re-upload");
                        }
                    });


               })
                $('.correct_format').click(function(){
                    $.post("./index2.php", {option: "<?php echo $option;?>",task: "file_download"}, function(result){

                    })

                })

            })
        </script>

        <div id="dialog" title="Parse Xlsx File">
            <form style="display: none;" id="fileToUploadform">
                <input type="file" name="fileToUpload" id="xlsxfileform">
                <input type="button" class="parse_file" value="Upload" name="submit">
                <a href="/administrator/components/com_parse_company_orders/correct_format.xlsx" download>
                <input type="button" class="correct_format" value="Download Correct Format" name="dounload">
                </a>
                <input style="display: none;" type="button" class="save_parsed_file" value="Save" name="save_parsed_file">
            </form><br>

            <div class="parsing_result"></div>
            <div style="color:red" class="parsing_error"></div>
        </div>
        <?php
    }


    function editCompanyParseOrders(&$row, $option, $statuses,$operators) {
        global $mosConfig_live_site,$mosConfig_stripe_enable;
        /*
        echo '<pre>';
            print_r($row);
            
        echo '</pre>';*/
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                
                $('#order_status').change(function() {
                    $('.se-pre-con').fadeIn('slow');
                    
                    $.ajax({
                        data: {
                            option: 'com_parse_company_orders',
                            task: 'change_status',
                            id: $('#id').val(),
                            status: $(this).val()
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: 'index2.php',
                        success: function(data)
                        {
                            if (data.result)
                            {
                                location.reload();
                            }
                            else
                            {
                                $('.se-pre-con').fadeOut('slow');
                                alert(data.error);
                            }
                        }
                    });
                });
                $('#operator').change(function() {
                    $('.se-pre-con').fadeIn('slow');

                    $.ajax({
                        data: {
                            option: 'com_parse_company_orders',
                            task: 'change_operator',
                            id: $('#id').val(),
                            operator: $(this).val()
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: 'index2.php',
                        success: function(data)
                        {
                            if (data.result)
                            {
                                location.reload();
                            }
                            else
                            {
                                $('.se-pre-con').fadeOut('slow');
                                alert(data.error);
                            }
                        }
                    });
                });
            });
        </script>
        <script src="/includes/js/google_analitics.js?ver=<?= time() ?>" type="text/javascript"></script>
        <style>
            .hideEl{
                display: none;
            }
            .showEl{
                display: block;
            }
            .send_payment_result{
                word-break: break-word;
                padding: 0 10px;
            }
            .button{
                background: #b9bf44;
                border-radius: 20px;
                padding: 7px;
                color: white;
                font-weight: bolder;
                border: 1px solid #b9bf44;
                float: left;
                margin-right: 10px;
                cursor: pointer;
            }
            .se-pre-con {
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: url(/images/Preloader_8.gif) center no-repeat #fff;
                display: none;
            }
        </style>
        <div class="se-pre-con"></div>
        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        <?php echo $row->company_name ." Parsed Orders" ?>

                    </th>
                </tr>
            </table>

            <table class="adminform">
                <tr>
                    <td style="vertical-align: top;">
                            <table class="adminform">

                                <tr>
                                    <th colspan="2">Company  Detail</th>
                                <tr>
                                <tr>
                                    <td width="150"><b>Company Name:</b></td>
                                    <td><?php echo $row->company_name; ?></td>
                                </tr>
                                <tr>
                                    <td><b>User ID:</b></td>
                                    <td><?php echo $row->username; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Status:</b></td>
                                    <td><?php echo $row->status; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Price:</b></td>
                                    <td>$<?php echo round($row->total_price,2); ?></td>
                                </tr>
                                <tr>
                                    <td><b>Orders date:</b></td>
                                    <td>
                                        <?php 
                                        $orders = unserialize($row->orders); 
                                        $orders_details = $row->orders_details[$orders[0]];
                                        echo date("Y-m-d H:i:s",$orders_details->cdate); 
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Change status:</b></td>
                                    <td>
                                        <?php 
                                        
                                        echo $statuses; 
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Change Operator:</b></td>
                                    <td>
                                        <?php

                                        echo $operators;
                                        ?>
                                    </td>
                                </tr>
                                </table>
                        </td>
                        <td style="vertical-align: text-bottom;">

                            <?php if ($row->payment == false) {?>

                        <table width="100%" cellpadding="0" cellspacing="5" border="0" class="adminform billing-info" align="center">
                            <tr>
                                <th colspan="2">Payment</th>
                            <tr>
                            <tr class="<?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>">
                                <td class="title">Name On Card<font color="red">*</font>:</td>
                                <td><input type="text" name="name_on_card" class="name_on_card" value="" size="30" /></td>
                            </tr>
                            <tr class="<?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>">
                                <td class="title">Credit Card Number<font color="red">*</font>:</td>
                                <td><input type="text" name="credit_card_number" class="credit_card_number" value="" size="30" maxlength="16" /></td>
                            </tr>
                            <tr class="<?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>">
                                <td class="title">Credit Card Security Code<font color="red">*</font>:</td>
                                <td><input type="text" name="credit_card_security_code" class="credit_card_security_code"  value="" size="30" maxlength="4" /></td>
                            </tr>
                            <tr class="<?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>">
                                <td class="title">Expiration Date<font color="red">*</font>:</td>
                                <td><?php echo $row->expire_month; ?>&nbsp;/&nbsp;<?php echo $row->expire_year; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="button" class="button <?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>" id="send_payment" value="Send Payment">
                                    <input type="button" class="button <?php echo ($mosConfig_stripe_enable)?'showEl':'hideEl'; ?>" id="send_payment_link" value="Get Payment Link">
                                    <a href="/scripts/html2pdf/html2pdf.php?id=<?php echo $row->id; ?>" target="_blank">
                                        <input type="button" value="Get invoice" class="button"/>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="send_payment_result"></div>
                                </td>
                            </tr>
                        </table>

                <?php }else{?>
                            <table style="border:none;" width="100%" cellpadding="0" cellspacing="5" border="0" class="adminform billing-info" align="center">
                                <tr>
                                    <th colspan="2">Payment</th>
                                </tr>
                                <tr>
                                    <td class="title">Card mask:</td>
                                    <td><?php echo $row->payment->card_mask; ?></td>
                                </tr>
                                <tr>
                                    <td class="title">Date:</td>
                                    <td><?php echo $row->payment->date; ?></td>
                                </tr>
                                <tr>
                                    <td class="title">Amount:</td>
                                    <td>$<?php echo number_format($row->payment->amount, 2, '.', ''); ?></td>
                                </tr>
                                <tr>
                                    <td class="title">Trans ID:</td>
                                    <td><?php echo $row->payment->trans_id; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                <a href="/scripts/html2pdf/html2pdf.php?id=<?php echo $row->id; ?>" target="_blank">
                                        <img src="images/get_invoice_button.png" alt="Get invoice" class="send_payment"/>
                                    </a>
                                    </td>
                                </tr>
                            </table>
                            <?php } ?>

                        </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table class="adminform">
                                <tr>
                                    <th>Order Id</th>
                                    <th>Order Total</th>
                                    <th>Order Status</th>
                                    <th>Order Delivery Date</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Street Name</th>
                                    <th>Street Number</th>
                                    <th>Suite</th>
                                    <th>Address_1</th>
                                    <th>Zip</th>
                                    <th>Phone</th>
                                    <th>Customer Instructions</th>
                                </tr>
                        <?php
                        if($row->orders && $row->orders_details){
                            $orders = unserialize($row->orders);
                            foreach($orders as $order){
                               $orders_details = $row->orders_details[$order];

                                $link=$mosConfig_live_site."/administrator/index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart&order_id_filter=".$order;
                                ?>
                                <tr>
                                    <td><a href='<?php echo $link;?>' target='_blank'><?php echo $order;?></a></td>
                                    <td>$<?php echo round($orders_details->order_total,2);?></td>
                                    <td><?php echo $orders_details->order_status_name; ?></td>
                                    <td><?php echo $orders_details->ddate;?></td>
                                    <td><?php echo $orders_details->first_name;?></td>
                                    <td><?php echo $orders_details->last_name;?></td>
                                    <td><?php echo $orders_details->country;?></td>
                                    <td><?php echo $orders_details->state;?></td>
                                    <td><?php echo $orders_details->city;?></td>
                                    <td><?php echo $orders_details->street_name;?></td>
                                    <td><?php echo $orders_details->street_number;?></td>
                                    <td><?php echo $orders_details->suite;?></td>
                                    <td><?php echo $orders_details->address_1;?></td>
                                    <td><?php echo $orders_details->zip;?></td>
                                    <td><?php echo $orders_details->phone_1;?></td>
                                    <td><?php echo $orders_details->customer_comments;?></td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </table>
                    </td>
                </tr>

            </table>



        <script>
    $(function() {
        $('#send_payment_link').click(function(){
            $.post( "index2.php", {
                option: "com_parse_company_orders",
                task: "send_payment_link",
                id:"<?php echo ($_GET['id'])?$_GET['id']:'';?>",
                ajaxSend: function () {
                    $('.send_payment_result').html('Please Wait')

                }
            },  function( data ) {
                var response = JSON.parse(data)
                $('.send_payment_result').html(response.status)
                if (response.items !== undefined) {
                    response.items.forEach(function (arrayItem) {
                        pushPurchaseGoogleAnalytics(arrayItem.eventName, arrayItem.items, parseFloat(arrayItem.value), arrayItem.transaction_info);
                    });
                }
            });
        })
        $('#send_payment').click(function(){
            if($('.name_on_card').val()==''){
                alert('Please Enter Card Name')
                return false;
            }
            if($('.credit_card_number').val()==''){
                alert('Please Enter Card Number')
                return false;
            }
            if($('.credit_card_security_code').val()==''){
                alert('Please Enter Card Security Code ')
                return false;
            }
            if($('.expire_month').val()==''){
                alert('Please Choose Expire Month ')
                return false;
            }
            $.post( "index2.php", {
                option: "com_parse_company_orders",
                task: "pay_now",
                id:"<?php echo ($_GET['id'])?$_GET['id']:'';?>",
                name_on_card: $('.name_on_card').val(),
                credit_card_number: $('.credit_card_number').val(),
                credit_card_security_code: $('.credit_card_security_code').val(),
                expire_month: $('.expire_month').val(),
                expire_year: $('.expire_year').val(),
                ajaxSend: function () {
                    $('.send_payment_result').html('Please Wait')

                }
            },  function( data ) {
                var response = JSON.parse(data)
                $('.send_payment_result').html(response.status)
                if($.trim(response.status) == 'Payment Approved'){
                    $('.send_payment_result').html('<b>Please wait 30 sec after payment to create the invoice.</b>')
                    setTimeout(() => {
                        location.reload();
                }, 30000);
                }
                if (response.items !== undefined) {
                    response.items.forEach(function (arrayItem) {
                        pushPurchaseGoogleAnalytics(arrayItem.eventName, arrayItem.items, parseFloat(arrayItem.value), arrayItem.transaction_info);
                    });
                }
            });

        })
    })
        </script>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" id="id" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
        </form>
        <?php
    }


}
?>