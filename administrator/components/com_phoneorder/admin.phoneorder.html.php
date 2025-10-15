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
global $mosConfig_absolute_path;
/* require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_database.php" );
  require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_html.php" ); */

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_PhoneOrder {

    //============================================= PHONE ORDER ===============================================
    static function savePhoneOrderSuccess($option,$orderProductsJson,$orderObj,$stripePaymentLinkUrl) {
        global $mosConfig_ga4_gtm;
        $order_id = mosGetParam($_REQUEST, "order_id", "");
        ?>


        <style type="text/css">
            a.place-link:link, a.place-link:visited {
                font:bold 12px Tahoma, Verdana;
                text-transform:uppercase;
                text-decoration:none;
                color:blue;
            }

            a.place-link:hover {
                text-decoration:underline;
                font-style:italic;
            }
        </style>
        <p>&nbsp;</p>
        <?php if ($stripePaymentLinkUrl && !empty($stripePaymentLinkUrl)): ?>

            <a href="<?php echo $stripePaymentLinkUrl; ?>" target="_blank" class="place-link">Click to Pay via Stripe</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;

        <?php endif; ?>

        <a href="index2.php?option=<?php echo $option ?>" class="place-link">Place New Order</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
        <a href="index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart" class="place-link">Check Order list</a>
        <h2>Order ID  #<?php echo $order_id;?></h2><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
        <?php
    }

    function selectDeliveryOption($aInfomation) {
        global $mosConfig_live_site, $mosConfig_lang;
        ?>
        <script type="text/javascript">
            function moveMonth(sCurrentDate) {
                $("#loadCalendarAjax").css('display', 'block');
                var items = $("input[name='product_id[]']");
                var items_array = items.serialize();
                var select_sub = false;
                
                $.each($("select[name='select_sub_[]']:visible option:selected"), function (index, value) {
                select_sub = true;
                });
    
                $.post("index2.php",
                        {option: "com_phoneorder",
                            task: "selectDeliveryOption",
                            delivery_date: sCurrentDate,
                            delivery_postalcode: $("input[name='deliver_zip_code']").val(),
                            delivery_state: $("input[name='state_checked_value']").val(),
                            shipping_method: $("select[name='shipping_method']").val(),
                            product_id: items_array,
                            select_sub: select_sub,
                            user_id: $("input[name='user_id']").val(),
                            coupon_discount_code: $("input[name='coupon_discount_code_1']").val(),
                            delivery_option_new: $("select[name='deliver_state']").val() + '[--1--]' + $("input[name='deliver_zip_code']").val()+ '[--1--]' + $("input[name='deliver_city']").val()+ '[--1--]' + $("select[name='deliver_country']").val()
                        },
                        function (data) {
                            $('#selectDeliveryOptionData').html(data);
                        }
                );
            }

            let oldLabel = null;

            function changeDay(isBlended, date, message) {
                if (isBlended) {
                    var selectedRadio = $("#deliverySurcharge input[name='shipping_method_radio']:checked");
                    if (selectedRadio.length > 0) {
                        const regex = /\$\d+\.\d{2}/g;
                        let replacement = $('.special-deliver-holiday').html();
                        oldLabel = selectedRadio.siblings("label").html();
                        const newLabel = oldLabel.replace(regex, `<span style="color: red;">${replacement}</span>`);
                        selectedRadio.siblings("label").html(newLabel);
                    }

                    $(".calendar-day-holiday").mouseout(function () {
                        if ($(this).hasClass("calendar-day-holiday")) {
                            // Restore the original label text if it was modified
                            if (oldLabel) {
                                $("#deliverySurcharge input[name='shipping_method_radio']:checked").siblings("label").html(oldLabel);
                                oldLabel = null; // Reset the oldLabel variable
                            }
                        }
                    });
                }
                $('#currentSelectDay').html(date);
                if (message)
                {
                    $('#message').css('display', 'block');
                    $('#message').html(message);
                }
                else
                {
                    $('#message').html("");
                    $('#message').css('display', 'none');
                }
            }
            function chooseDay(isBlended, date, total_delivery_price) {

                if (isBlended) {

                    $("input[name='is_blended_day']").val("1");

                }
                document.getElementById('delivery_date').value = date;
                $("input[name='deliver_fee']").val(total_delivery_price);
                nTotalDeliveryPrice = total_delivery_price;
                updateDeliveryFee();
                $.modal.close();
                sOptionString = $("input[name='zip_checked_value']").val();
                changeDeliver(sOptionString, "");

            }

        </script>
        <div id="selectDeliveryOptionData">
            <h2 class="select-delivery-option"><?php echo _DELIVERY_CALENDAR; ?></h2>
            <div class="delivery-calendar">
                <div class="delivery-calendar-left">
                    <?php echo _DELIVERY_CALENDAR_NOTE; ?>
                    <?php if( $aInfomation['holiday']) { ?>
                        <br>
                        <div class="alert alert-warning" role="alert">
                            <?php echo $aInfomation['holiday']; ?>
                        </div>
                    <?php } ?>

                    <div class="print-calendar">
                        <div class="month-actions">
                            <?php
                            if ($aInfomation['PreDeliveryDate']) {
                                $sPreOnClick = "moveMonth('" . $aInfomation['PreDeliveryDate'] . "');return false;";
                            } else {
                                $sPreOnClick = "return false;";
                            }
                            $aCurrentDeliveryDate = explode("/", $aInfomation['CurrentDeliveryDate']);
                            $sMonthLabel = date("F Y", strtotime($aCurrentDeliveryDate[2] . "-" . $aCurrentDeliveryDate[0] . "-01 00:00:00"));
                            //echo $aInfomation['CurrentDeliveryDate']."=========";
                            if ($mosConfig_lang == 'french') {
                                $aEnglishMonth = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                $aFrenchMonth = array("Janvier", "F&egrave;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&egrave;cembre");
                                $sMonthLabel = str_replace($aEnglishMonth, $aFrenchMonth, $sMonthLabel);
                            }
                            ?>
                            <a href="#" onclick="<?php echo $sPreOnClick; ?>" class="pre-month" id="asdfasd">&nbsp;</a>
                            <?php echo $sMonthLabel; ?>
                            <a href="#" onclick="moveMonth('<?php echo $aInfomation['NextDeliveryDate']; ?>');
                        return false;" class="next-month">&nbsp;</a>
                        </div>
                        <?php echo $aInfomation['Calendar']; ?>
                    </div>
                    <div id="loadCalendarAjax"><?php echo _AJAX_WAITTING; ?></div>
                </div>
                <div class="delivery-calendar-right">

                    <div id="selectDayNote" class="select-day-note">
                        <div id="deliverySurcharge">
                            <b><?php echo _DELIVERY_SURCHARGE; ?></b><br/>
                            <?php if ($aInfomation['ShippingMethod']) { ?>
                                <div id="yourAddressDelivery">
                                    <?php echo $aInfomation['ShippingMethod']['text']; ?>
                                </div>
                            <?php } ?>
                            <div class="other-delivery">
                                <div id="message">
                                </div>
                                <!--
                                <div id="specialDeliver">
                                    &nbsp;&nbsp;- <?php echo _DELIVERY_SPECIAL_DAY; ?>: <span id="nSpecialDeliver" class="delivery-money"></span>
                                </div>
                                <?php
                                $aCurrentDeliveryDate = explode("/", $aInfomation['CurrentDeliveryDate']);

                                if ($aInfomation['CutOffTime'] && intval($aCurrentDeliveryDate[1]) == intval(date("j")) && intval($aCurrentDeliveryDate[0]) == intval(date("m")) && intval($aCurrentDeliveryDate[2]) == intval(date("Y"))) {
                                    ?>
                                                            <div id="deliverSameDay">
                                                                &nbsp;&nbsp;- <?php echo _DELIVERY_SAME_DAY; ?>: <span class="delivery-money"><?php echo $aInfomation['CutOffTime']; ?></span>
                                                            </div>
                                <?php } ?>

                                -->
                            </div>
                        </div>
                    </div>
                    <div class="select-date-note"><?php echo _DELIVERY_CALENDAR_NOTE2; ?></div>
                    <?php
                    $sDeliverDateLabel = date("l, M d Y", strtotime($aInfomation['CurrentDeliveryDate']));
                    if ($mosConfig_lang == 'french') {
                        $aEnglishMonth = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oc", "Nov", "Dec");
                        $aFrenchMonth = array("Jan", "F&egrave;v", "Mar", "Avr", "Mai", "Jui", "Juil", "Ao&ucirc;t", "Sep", "Oct", "Nov", "D&egrave;c");
                        $sDeliverDateLabel = str_replace($aEnglishMonth, $aFrenchMonth, $sDeliverDateLabel);

                        $aEnglishWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                        $aFrenchWeek = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
                        $sDeliverDateLabel = str_replace($aEnglishWeek, $aFrenchWeek, $sDeliverDateLabel);
                    }
                    ?>
                    <div id="currentSelectDay" class="current-select-day">
                        <?php echo $sDeliverDateLabel; ?>
                    </div>
                    <div class="select-day-holidays">
                        <b><?php echo _HOLIDAYS; ?>:</b><br/>
                        <div id="selectDayHolidays">
                            <?php
                            if(isset($aInfomation['Blended']) && $aInfomation['Blended'] != '') {
                                $text = $aInfomation['Blended'];
                                echo "<ul><li><span style='color: red;'>$text</span></li></ul>";
                            }
                            else if (isset($aInfomation['unavailable_date']) && count($aInfomation['unavailable_date'])) {
                                echo "<ul>";
                                foreach ($aInfomation['unavailable_date'] as $Item) {
                                    echo "<li>$Item</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<ul><li>None</li></ul>";
                            }
                            ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <?php
        exit(0);
    }

    function makePhoneOrder($option, $aInfomation, $orderCallType = null) {
        global $mosConfig_live_site, $database,$mosConfig_stripe_enable;
            $url = $mosConfig_live_site;
        ?>

        <script type="text/javascript" src="<?php echo $url; ?>/templates/bloomex7/js/jquery.simplemodal.js"></script>
        <style type="text/css">

            .select_type_error{
                border: 2px solid red;
                background: none;
            }
            #changedEmailCheckingResponse{
                text-align: center;
            }
            #changedEmailCheckingResponse .clear{
                color: #856404;
                background-color: #fff3cd;
                border-color: #ffeeba;
            }
            #changedEmailCheckingResponse .overwrite{
                color: #155724;
                background-color: #d4edda;
                border-color: #c3e6cb;
            }
            #changedEmailCheckingResponse .cancel{
                color: #721c24;
                background-color: #f8d7da;
                border-color: #f5c6cb;
            }
            .donation_price_pre,.donation_name_pre{
                color:red
            }
            .hideEl{
                display: none;
            }
            .showEl{
                display: block;
            }
            /* Container */
            #selectDeliveryOptionContainer {height:556px; width:668px; left:50%; top:15%; margin-left:-368px; background-color:#fff; border:3px solid #483278;}
            #selectDeliveryOptionContainer a.modalCloseImg {background:url(<?php echo $url; ?>/templates/bloomex7/images/calendar_close.jpg) no-repeat; width:20px; height:20px; display:inline; z-index:3200; position:absolute; top:7px; right:7px; cursor:pointer;}
            #selectDeliveryOptionContainer #basicModalContent {padding:8px;}

            div#selectDeliveryOption {}

            div.special-deliver-holiday {
                display:block;
                color: #0030cc;
                float:left;
                margin: 5px 0px 0px 6px;
            }
            td.calendar-day-holiday{
                min-height:80px;
                font-size:11px;
                position:relative;
                background-color: #e0b6b6;
                cursor:pointer;
            }

            h2.select-delivery-option {
                font:bold 12px Tahoma, Verdana, Arial;
                background-color:#7351B5;
                padding:10px 0px 10px 15px;
                margin:0px 0px 0px 0px;
                color:#FFFFFF;
                display:block;
            }

            div.delivery-calendar {
                font:normal 11px Tahoma, Verdana, Arial;
                display:block;
                width:100%;
            }

            div.delivery-calendar-left {
                margin:10px 10px 0px 15px;
                display:block;
                width:355px;
                float:left;
            }

            div.delivery-calendar-right {
                background-color:#D6E7D5;
                margin:10px 0px 10px 0px;
                padding:5px 5px 10px 5px;
                height:480px;
                width:265px;
                display:block;
                float:left;
            }

            div.current-select-day {
                font:bold 12px Tahoma, Verdana, Arial;
                background-color:#FFFFFF;
                margin:10px 0px 0px 0px;
                padding:7px 5px 7px 5px;
                color:#FF6600;
                display:block;
            }

            div.select-day-holidays {
                font:normal 12px Tahoma, Verdana, Arial;
                margin:10px 0px 0px 0px;
                padding:0px 5px 7px 5px;
                color:#000000;
                display:block;
            }

            div.select-day-holidays ul {
                margin:10px 0px 0px 0px;
                padding:0px 0px 0px 20px;
                line-height:140%;
            }

            div.select-day-holidays ul li{
                margin:0px 0px 0px 0px;
            }

            a.pre-month:link, a.pre-month:visited, a.pre-month:hover  {
                background:url(<?php echo $url; ?>/templates/bloomex7/images/pre_month.jpg) 10px 2px no-repeat;
                padding:0px 5px 0px 10px;
            }

            a.next-month:link, a.next-month:visited, a.next-month:hover  {
                background:url(<?php echo $url; ?>/templates/bloomex7/images/next_month.jpg) 0px 2px no-repeat;
                padding:0px 15px 0px 0px;
            }

            div.select-day-note {
                background:url(<?php echo $url; ?>/templates/bloomex7/images/bg_calenda.jpg) top left no-repeat;
                font:normal 11px Tahoma, Verdana, Arial;
                background-color:#FFFFFF;
                margin:0px 0px 0px 0px;
                padding:5px;
                height:275px;
                display:block;
            }

            div.select-date-note {
                font:bold 11px Tahoma, Verdana, Arial;
                margin:10px 0px 0px 0px;
                display:block;
            }

            div#yourAddressDelivery {
                margin:100px 0px 0px 0px;
            }

            div#yourAddressDelivery span.express_image {
                background:url(<?php echo $url; ?>/templates/bloomex7/images/car_delivery.png) top left no-repeat;
                margin:0px 5px 0px 0px;
                width:56px;
                height:24px;
                display:block;
                float:left;
            }


            div#yourAddressDelivery span.express_image_fr {
                background:url(<?php echo $url; ?>/templates/bloomex7/images/car_delivery_fr.png) top left no-repeat;
                margin:0px 5px 0px 0px;
                width:56px;
                height:24px;
                display:block;
                float:left;
            }

            div#yourAddressDelivery div.txt-1{
                margin:0px 0px 5px 0px;
                display:block;
                width:100%;
                float:left;
            }

            div#yourAddressDelivery div.txt-1 input {
                float:left;
            }

            div#yourAddressDelivery div.txt-1 label {
                float:left;
                width:230px;
            }

            div.print-calendar {
                margin:10px 0px 0px 0px;
                border-left:1px solid #CCCCCC;
                border-bottom:1px solid #CCCCCC;
                display:block;
            }

            div.month-actions {
                font:bold 12px Tahoma, Verdana, Arial;
                padding:10px 0px 10px 0px;
                background-color:#7351B5;
                text-align:center;
                color:#FFFFFF;
            }

            div#specialDeliver {
                display:none;
            }

            div.special-deliver {
                display:block;
                color:#0099CC;
                float:left;
                margin:5px 0px 0px 0px;
            }

            span.delivery-money {
                font-weight:bold;
                color:#0099CC;
            }

            div#deliverySurcharge {
                line-height:140%;
            }

            /* calendar */
            table.calendar	{
                width:100%;
                height:auto;
                display:block;
                border:none;
            }

            table.calendar	td {
                border-top:1px solid #CCCCCC;
                border-right:1px solid #CCCCCC;
            }

            tr.calendar-row	{
                vertical-align:top
            }

            td.calendar-day{
                min-height:80px;
                font-size:11px;
                position:relative;
                background-color:#FFFFFF;
            }

            * html div.calendar-day {
                height:80px;
            }
            td.calendar-day:hover, td.calendar-day-holiday:hover{
                background:#D2FBB5;
            }
            div.day-number-holiday	{
                margin: 1px 0px 0px 4px;
                padding: 4px 2px 4px 2px;
                background-color: #bd3434;
                text-align: center;
                font-weight: bold;
                color: #fff;
                width: 40px;
                float: left;
            }

            td.calendar-day-np {
                background:#E8E8E8;
                min-height:80px;
            }

            * html div.calendar-day-np {
                height:80px;
            }

            td.calendar-day-head {
                background:#7351B5;
                color:#FFFFFF;
                font-weight:bold;
                text-align:center;
                padding:5px;
            }
            div.day-number	{
                margin:-4px 0px 0px -4px;
                padding:4px 2px 4px 2px;
                background-color:#999;
                text-align:center;
                font-weight:bold;
                color:#fff;
                width:20px;
                float:left;
            }
            /* shared */
            td.calendar-day, td.calendar-day-np {
                width:50px;
                height:55px;
                padding:5px;
                cursor:pointer;
            }

            div.calendar-today {
                background-color:#FF6600;
            }

            div#loadCalendarAjax {
                background:url(<?php echo $url; ?>/templates/bloomex7/images/loading.gif) no-repeat;
                margin:20px 0px 0px 0px;
                padding:12px 0px 0px 40px;
                height:32px;
                display:none;
            }

            /*********end check out**********/
            /*********pop up********/

            table.product-list tr.header td {
                font:bold 12px Tahoma, Verdana;
                color:#993333;
            }

            table.product-list tr td  table.product-item{
                border:1px solid #CCC;
                margin-top:5px;
            }

            table.product-list tr td  table.product-item td{
                font:normal 12px Tahoma, Verdana;
            }

            table.product-list tr td  table.product-item td a{
                font:normal 11px Tahoma, Verdana;
            }

            table.product-list tr td  table.product-item td.price {
                color:#FF0000;
                font-weight:bold;
            }

            table.product-list tr td  table.product-item td.product-name {
                color:#0000FF;
                font-weight:bold;
            }

            table.product-list td.price2, div.extra-fee, td.calculate-price {
                font:bold 12px Tahoma, Verdana;
                color:#FF0000;
                text-align:left;
            }

            div.extra-fee span {
                font:normal 12px Tahoma, Verdana;
                line-height:160%;
            }

            table.product-list td.title2 {
                font:bold 12px Tahoma, Verdana;
                text-align:right;
                color:#0000FF;
            }

            input.btn {
                font:bold 12px Tahoma, Verdana;
                cursor:pointer;
                padding:3px;
                width: 18%;
                float: right;
            }
            #image_prew{
                max-width: 18%;
                float: right;
                margin-top: 20px;
            }
            #image_prew img{
                max-width: 100%;
            }

            input.btn2 {
                font:bold 12px Tahoma, Verdana;
                cursor:pointer;
                padding:2px;
            }

            select {
                font:normal 12px Tahoma, Verdana;
            }

            select.cbo-product {
                margin-left:10px;
            }

            table.billing-info {
                border:1px solid #CCC;
                margin-top:10px;
            }

            table.billing-info td.header {
                font:bold 13px Tahoma, Verdana;
                border-bottom: 1px solid #993333;;
                text-transform:uppercase;
                text-align:center;
                color:#993333;
                padding:7px;
            }

            table.billing-info td.header2 {
                font:bold 12px Tahoma, Verdana;
                color:#993333;
                padding:7px;
            }

            table.billing-info td.title{
                font:bold 11px Tahoma, Verdana;
                padding:5px 5px 5px 15px;
                vertical-align:top;
            }

            div.error-msg {
                font:bold 11px Tahoma, Verdana;
                color:#FF0000;
                padding:5px;
            }

            td.notice {
                font:bold 12px Tahoma, Verdana;
                text-indent:15px;
                color:#FF6600;
            }

            td.cut-off-time {
                font:normal 12px Tahoma, Verdana;
                padding:5px;
            }

            td.cut-off-time span{
                color:#FF0000;
                padding-left:10px;
            }

            div#deliver-address-default {
                font:normal 11px Tahoma, Verdana;
                padding:0px 10px 0px 20px;
                line-height:20px;
            }

            div.msgReport {
                font:bold 11px Tahoma, Verdana;
                margin:20px 0px 0px 0px;
                text-align:left;
                display:block;
                color:#3366FF;

            }

            div.before-check-account {
                font:bold 12px Tahoma, Verdana;
                margin:20px 0px 40px 0px;
                text-align:center;
                display:block;
                color:#3366FF;

            }

            div.after-check-account {
                display:none;
            }

            .ac_results {
                padding: 0px;
                border: 1px solid black;
                background-color: white;
                overflow: hidden;
                z-index: 99999;
            }

            .ac_results ul {
                width: 100%;
                list-style-position: outside;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .ac_results li {
                margin: 0px;
                padding: 2px 5px;
                cursor: default;
                display: block;
                /*
                if width will be 100% horizontal scrollbar will apear
                when scroll mode will be used
                */
                /*width: 100%;*/
                font: menu;
                font-size: 12px;
                /*
                it is very important, if line-height not setted or setted
                in relative units scroll will be broken in firefox
                */
                line-height: 16px;
                overflow: hidden;
            }

            .ac_results li strong{
                color:red;
            }

            .ac_loading {
                background: white url('indicator.gif') right center no-repeat;
            }

            .ac_odd {
                background-color: #eee;
            }

            .ac_over {
                background-color: #0A246A;
                color: white;
            }

            #selectProductId {
                font:bold 12px Tahoma, Verdana;
                padding:4px 2px 4px 5px;
                width: 80%;
                float: left;
            }
            .payment_methods img {
                box-shadow: 3px 2px 4px 0px rgba(0, 0, 0, .2);
                vertical-align: middle;
                width: 25px;
                margin-right: 5px;
                border: 1px solid #b5b2b7;
                border-radius: 5px;
            }
            .payment_methods .cdisabled {
                background-position: 0 -25px;
                opacity: 0.3;
            } #place_result{
                border: 1px solid #ccc;
                padding: 5px;
                border-radius: 10px;
                margin-top: 6px;
                display: none;
            }
            .place_result_item{
                cursor: pointer;
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
                padding-top: 5px;
            }
            .pac-container{
                width: 400px !important;
            }
            .frmSearch{
                width: 50%;
            }
            .frmSearch #suggestions{
                width: 80%;
            }

            .frmSearch #suggestions div {
                font-size: 15px;
                font-weight: bold;
                line-height: 22px;
                cursor: pointer;
            }
            .frmSearch #suggestions div:hover{
                background-color: #ccc;
            }
        </style>

        <div id="product-item-default" style="display:none">
            <div id="product-item-{noItem}">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" class="product-item">
                    <tr>
                        <td width="5%" style="text-align:center;"><strong>{noItem}.</strong></td>
                        <td width="35%" class="product-name">{item-name}<input type="hidden" value="{item-id}" id="product-id-item-{noItem}" name="product_id[]"/></td>
                        <td width="20%"><strong>{recipe}</strong></td>
                        <td width="10%" class="price">{item-price}</td>
                        <!--<td width="10%" class="price">{item-tax}</td>-->
                        <td width="10%">
                            <select class="deluxe_supersize_{noItem}" {show_deluxe_supersize} name="show_deluxe_supersize[]"  onchange="select_deluxe_supersize(this.value, {real_price}, {noItem}, {real_tax})">
                                <option value="0">STANDARD</option>
                                <option value="{petite}">PETITE (-{petite} $)</option>
                                <option value="{deluxe}">DELUXE (+{deluxe} $)</option>
                                <option value="{supersize}">SUPERSIZE  (+{supersize} $)</option>
                            </select>
                        </td>
                        <td width="10%">
                        <select class="select_sub_{noItem}" {show_hide_select_sub} name="select_sub_[]"  onchange="select_sub(this.value, {noItem}, {real_tax});">
                            <option value="">--select--</option>
                            <option value="sub_3" attr="{sub_3}" {sub_3_d}>3 months</option>
                            <option value="sub_6" attr="{sub_6}" {sub_6_d}>6 months</option>
                            <option value="sub_12" attr="{sub_12}" {sub_12_d}>12 months</option>
                        </select>
                    </td>
                        <td width="10%"><input type="text" size="7" maxlength="3" name="quantity[]" alt="{real_price}" id="quantity-item-{noItem}" value="{quantity-value}" onkeyup="only_number(this, this.title);" onblur="checkNumberProduct(this.value, {real_price}, {real_tax}, {noItem}, 0);" onfocus="saveNumberProduct(this.value);"/>
                        <td width="5%" class="price" real_price="{real_price}"  price="{item-subtotal-price}"  id="item-subtotal-{noItem}">{item-subtotal}</td>
                        <td width="5%" id="deleteActionLink"><a href="#" onclick="deleteItem({noItem}, {real_price}, {real_tax});
                                        return false;" id="deleteItem" >Delete</a></td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="deliver-address-item" style="display:none">
            <input type="radio" name="deliver_address_item" value="{value}" title="{country_state}" {status} />{text}<br/>
        </div>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Phone Order Manager:
                        <small>Add New</small>

                    </th>
                </tr>
                <tr>
                    <td style="padding: 10px 0px">
                        <b style="color:orange;font-size: 15px">Please first choose the product type to create an order with Australian or New Zealand products </b>
                        <select id="selectProductsList"  onchange="disableSelectAfterChoice()">
                            <option value="">-- choose products type--</option>
                            <option value="au" <?= (isset($_REQUEST['products_filter']) && $_REQUEST['products_filter'] == 'au')?'selected':''; ?>>AU products</option>
                            <option value="nz" <?= (isset($_REQUEST['products_filter']) && $_REQUEST['products_filter'] == 'nz')?'selected':''; ?>>NZ products</option>
                        </select>
                    </td>
                </tr>
            </table>
            <table width="100%" class="adminform">
                <tr>
                    <th colspan="2">Phone Order Information Detail</th>
                </tr>

                <tr>
                    <td colspan="2">
                        <table width="99%" cellpadding="5" cellspacing="0" border="0" class="product-list" align="center">
                            <tr class="header">
                                <td width="5%" style="text-align:center;">No</td>
                                <td width="35%">Product Name</td>
                                <td width="20%">Recipe</td>
                                <td width="10%">Price</td>
                                <td width="10%">Petite,Deluxe or Supersize</td>
                                <td width="10%">Subscribe</td>
                                <td width="10%">Quantity</td>
                                <td width="5%">Subtotal</td>
                                <td width="5%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="8" id="product-list-items" style="text-align:center;"></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="title2">Total:</td>
                                <td colspan="2" class="price2"  price="0"  id="total-price">$0.00</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!--===================================================================-->

                <tr>
                    <td colspan="2">
                        <div class="error-msg" >Please add all product items of order before update quantity!</div>
                        <div class="frmSearch">
                            <input type="text" id="selectProductId" autocomplete="off" />
                            <input type="hidden" id="select_product_id" value="" />
                            <input type="button" value="Add Product" id="addProductItem" class="btn" />
                            <div style="display:none;" id="image_prew"><img src=""></div>
                            <div id="suggestions"></div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td width="50%" valign="top">
                        <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">
                            <tr>
                                <td colspan="2" class="header" style="text-align:center;">Billing Information</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="header2">Account Information</td>
                            </tr>
                            <tr>
                                <td width="30%" class="title">Username<font color="red">*</font>:</td>
                                <td width="70%"><input type="text" name="user_name" value="" size="30" /></td>
                            </tr>
                            <!--<tr>
                                    <td class="title">Password<font color="red">*</font>:</td>
                                    <td><input type="text" name="pass" value="" size="30" /></td>
                            </tr>
                            <tr>
                                    <td class="title">Confirm Password<font color="red">*</font>:</td>
                                    <td><input type="text" name="pass_confirm" value="" size="30" /></td>
                            </tr>-->
                            <tr>
                                <td class="title">E-mail<font color="red">*</font>:</td>
                                <td><input type="text" name="account_email" value="" size="30" /></td>
                            </tr>
                            <tr>
                                <td class="title">&nbsp;</td>
                                <td>
                                    <div class="error-msg" id="error-report">Please enter email to find exist info in your system first!<br/>If your account isn't exist, you must create an new account.</div>
                                    <input type="button" value="Check Account Info" id="checkAccInfo" class="btn2" />&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="button" value="Create New Account" id="createAccInfo" class="btn2" />&nbsp;&nbsp;&nbsp;&nbsp;
                                    <!--<input type="button" value="Copy to Deliver Info" id="copyInfo" class="btn2" />-->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div id="changedEmailCheckingResponse"></div>
                                </td>
                            </tr>
                        </table>
                        <div class="after-check-account">
                            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info biling" align="center">
                                <tr>
                                    <td colspan="2" class="header2">Billing Information</td>
                                </tr>
                                <tr>
                                    <td width="30%" class="title">Company Name Title </td>
                                    <td width="70%"><input type="text" name="bill_company_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">First Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_first_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Last Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_last_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Middle Name:</td>
                                    <td><input type="text" name="bill_middle_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Suite/Apt: </b>
                                    </td>
                                    <td >
                                        <input name="bill_address_suite" size="30" value=""  type="text" maxlength="24" />

                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Street Number <font color="red">*</font>:</b>
                                    </td>
                                    <td >
                                        <input  name="bill_address_street_number" size="30" value="" type="text" maxlength="24"  />

                                    </td>

                                </tr>
                                <tr>
                                    <td class="title">Street Name <font color="red">*</font>:</b>
                                    </td>
                                    <td>
                                        <input name="bill_address_street_name" size="30" value="" type="text" maxlength="64"  />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">District:</td>
                                    <td><input type="text" name="bill_district" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">City<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_city" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Zip Code / Postal Code<font color="red">*</font>:</td>
                                    <td>
                                        <input type="text" name="bill_zip_code" value="" size="30" maxlength="7" /><br>
                                        Example: <b>1234</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Country<font color="red">*</font>:</td>
                                    <td><?php echo $aInfomation['bill_country']; ?></td>
                                </tr>
                                <tr>
                                    <td class="title">State/Province/Region<font color="red">*</font>:</td>
                                    <td><div id="bill_state_container"><?php echo $aInfomation['bill_state']; ?></div></td>
                                </tr>
                                <tr>
                                    <td class="title">Phone<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_phone" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Evening Phone:</td>
                                    <td><input type="text" name="bill_evening_phone" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Fax:</td>
                                    <td><input type="text" name="bill_fax" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title"></td>
                                    <td>
                                        <div id="update_billing_result" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                                        <input type="button" value="Update Billing Info" id="updatebilling" class="btn2" />&nbsp;&nbsp;&nbsp;</td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">
                                <tr>
                                    <td colspan="2" class="header">Coupon Discount Code</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><div class="error-msg">Please update all product items and quantity of order before submit coupon code !</div></td>
                                </tr>
                                <tr>
                                    <td class="title" width="30%">Your code:</td>
                                    <td width="70%">
                                        <input type="text" name="coupon_discount_code" value="" size="32" />
                                        <input type="hidden" name="coupon_discount_code_1" value="" />
                                        <input type="hidden" name="coupon_discount_price" value="0" />
                                        <input type="hidden" name="coupon_discount_value" value="" />
                                        <input type="hidden" name="coupon_discount_type" value="" />
                                        <input type="hidden" name="coupon_discount_percent_or_total" value="" />
                                        <input type="hidden" name="coupon_discount_product_aplly_coupon" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">&nbsp;</td>
                                    <td>
                                        <div class="msgReport" id="couponCode" style="display:none;text-align:left;padding:0px 0px 6px 0px;margin:0px;">&nbsp;</div>
                                        <input type="button" value="Check Coupon Code" id="checkCouponCode" class="btn2" />
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info payment_methods_table" align="center">
                                <tr>
                                    <td colspan="2" class="header">Payment Method</td>
                                </tr>

                                <tr  class="<?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>">
                                    <td colspan="2" class="title">
                                        <input name="payment_method_state" id="cart_payment_method" value="live" checked="checked" type="radio">
                                        <span class="payment_methods">
                                            <img src="/templates/bloomex7/images/payment_methods/visa.png" id="icon_visa" alt="visa">
                                            <img src="/templates/bloomex7/images/payment_methods/master_card.png"  id="icon_mastercard" alt="master card">
                                            <img src="/templates/bloomex7/images/payment_methods/american_express.png" id="icon_amex" alt="american express">
                                            <img src="/templates/bloomex7/images/payment_methods/dinner_club.png" id="icon_dinersclub" alt="diners club">
                                        </span>
                                        <br>
                                        <input name="payment_method_state" id="offline_payment_method" value="offline" type="radio">
                                        <label for="offline_payment_method">Select this option ONLY if your credit card was declined</label>
                                    </td>
                                </tr>

                                <tr class="stripe_payment_method_tr  <?php echo ($mosConfig_stripe_enable)?'showEl':'hideEl'; ?>">
                                    <td colspan="2" class="title">
                                        <input name="payment_method_state" id="stripe_payment_method" value="stripe" <?php echo ($mosConfig_stripe_enable)?'checked="checked"':''; ?> type="radio">
                                        <label for="stripe_payment_method">Pay secure via Stripe. <a href="https://stripe.com/en-ca/resources/more/secure-payment-systems-explained" target="_blank">Read more about  Stripe Payment Processing.</a>  </label>
                                    </td>
                                </tr>
                                <tr class="card_details <?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>">
                                    <td class="title">Name On Card<font color="red">*</font>:</td>
                                    <td><input type="text" name="name_on_card" value="" size="30" /></td>
                                </tr>
                                <tr class="card_details <?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>" >
                                    <td class="title">Credit Card Number<font color="red">*</font>:</td>
                                    <td><input type="text" name="credit_card_number" value="" size="30" maxlength="16" /><div id="check_cards" style="display: none; color: red; font-weight: bold; margin-top: 10px; font-size: 12px;"></div></td>
                                </tr>
                                <tr class="card_details <?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>" >
                                    <td class="title">Credit Card Security Code<font color="red">*</font>:</td>
                                    <td><input type="text" name="credit_card_security_code" value="" size="30" maxlength="4" /></td>
                                </tr>
                                <tr class="card_details <?php echo ($mosConfig_stripe_enable)?'hideEl':'showEl'; ?>" >
                                    <td class="title">Expiration Date<font color="red">*</font>:</td>
                                    <td><?php echo $aInfomation['expire_month']; ?>&nbsp;/&nbsp;<?php echo $aInfomation['expire_year']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width="50%" valign="top">
                        <div class="after-check-account">
                            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info delivery" align="center">
                                <tr>
                                    <td colspan="2" class="header">Delivery Information</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="header2"><input type="radio" name="exist_address_deliver" value="0"/> Please choose any exist delivery information below:</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div id="deliver-address-default"><div class="error-msg">None</div></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="header2"><input type="radio" class="add_address_deliver"  name="exist_address_deliver" value="1" checked/> Or add new delivery information below:  <input type="button" onclick="copy_from_biling_to_delivery()" value="Copy From Billing"></td>
                                </tr>
                                <tr>
                                    <td width="30%" class="title">Address Nickname<font color="red">*</font>:</td>
                                    <td width="70%"><input type="text" name="address_user_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">First Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_first_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Last Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_last_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Middle Name:</td>
                                    <td><input type="text" name="deliver_middle_name" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Business/Funeral Names :</td>
                                    <td><input type="text" name="deliver_company_name" value="" size="30" maxlength="32" /></td>
                                </tr>

                                <tr>
                                    <td class="title">Suite/Apt: </b>
                                    </td>
                                    <td >
                                        <input name="deliver_address_suite" size="30" value=""  type="text" maxlength="24" />

                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Street Number <font color="red">*</font>:</b>
                                    </td>
                                    <td >
                                        <input  name="deliver_address_street_number" size="30" value="" type="text" maxlength="24"  />

                                    </td>

                                </tr>
                                <tr>
                                    <td class="title">Street Name <font color="red">*</font>:</b>
                                    </td>
                                    <td>
                                        <input name="deliver_address_street_name" size="30" value="" type="text" maxlength="64"  />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">District:</td>
                                    <td><input type="text" name="deliver_district" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">City<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_city" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Zip Code / Postal Code<font color="red">*</font>:</td>
                                    <td>
                                        <input type="text" name="deliver_zip_code" value="" size="30" maxlength="7" /><br>
                                        Example: <b>1234</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Country<font color="red">*</font>:</td>
                                    <td><?php echo $aInfomation['deliver_country']; ?></td>
                                </tr>
                                <tr>
                                    <td class="title">State/Province/Region<font color="red">*</font>:</td>
                                    <td><div id="deliver_state_container"><?php echo $aInfomation['deliver_state']; ?></div></td>
                                </tr>
                                <tr>
                                    <td class="title">Phone<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_phone" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Evening Phone:</td>
                                    <td><input type="text" name="deliver_evening_phone" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Cell Phone:</td>
                                    <td><input type="text" name="deliver_cell_phone" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Fax:</td>
                                    <td><input type="text" name="deliver_fax" value="" size="30" maxlength="32" /></td>
                                </tr>
                                <tr>
                                    <td class="title">Recipient's Email Address:</td>
                                    <td><input type="text" name="deliver_recipient_email" value="" size="30" maxlength="255" /></td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">
                                <tr>
                                    <td colspan="2" class="header">Delivery Options</td>
                                </tr>
                                <tr>
                                    <td class="title" width="30%" >Occasion:</td>
                                    <td width="70%" ><?php echo $aInfomation['occasion']; ?></td>

                                </tr>
                                <tr>
                                    <td class="title" width="30%" >Type:</td>
                                    <td width="70%" >
                                        <select name="order_create_type" id="order_create_type" size="1">
                                            <option value="Phone Order">Phone Order</option>
                                            <option value="Chat Order">Chat Order</option>
                                        </select>
                                    </td>

                                </tr>
                                <tr>
                                    <td class="title" width="30%" >Sales line:</td>
                                    <td width="70%" ><?php echo $aInfomation['sales_line'];  ?></td>
                                    </tr>
                                <tr id="bucks" style="display: none">
                                    <td class="title">Remeed Bloomex Bucks: (<span id="bucks_value"></span>)</td>
                                    <td>
                                        <input type="checkbox" name="redeem_bucks" id="redeem_bucks" value="0" onclick="if(this.checked){this.value = 1} else {this.value = 0}">
                                        <input type="hidden" disabled name="bucks" value="">
                                    </td>
                                </tr>

                                <tr id="credits" style="display: none">
                                    <td class="title">Remeed Credits: (<span id="credits_value"></span>)</td>
                                    <td>
                                        <input type="checkbox" name="redeem_credits" id="redeem_credits" value="0" onclick="if(this.checked){this.value = 1} else {this.value = 0}">
                                        <input type="hidden" disabled name="credits" value="">
                                    </td>
                                </tr>

                                <tr id="donation_tr" style="display: none">
                                    <td class="title">I would like to donate: <span class="donation_price_pre"></span> to <span class="donation_name_pre"></span></td>
                                    <td>
                                        <input type="checkbox" name="donate" id="donate" value="0" onclick="if (this.checked) {
                                                            this.value = 1
                                                        } else {
                                                            this.value = 0
                                                        }">
                                        <input type="hidden" disabled name="donation_name" value="">
                                        <input type="hidden" disabled name="donation_price" value="">
                                        <input type="hidden" disabled name="donation_id" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Delivery Method:</td>
                                    <td><?php echo $aInfomation['shipping_method']; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="cut-off-time">
                                        <?php
                                        printf($aInfomation['DELIVERY_DATE'], $aInfomation['time'], date("h:i A"));
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Delivery Date:</td>
                                    <td>
                                        <?php //echo $aInfomation['delivery_day']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$aInfomation['delivery_month']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$aInfomation['delivery_year'];  ?>
                                        <div style="display:none;" id="selectDeliveryOption" >&nbsp;</div>
                                        <input type="text" name="delivery_date" id="delivery_date" readonly maxlength="10" />
                                        <img src="<?php echo $url; ?>/templates/bloomex7/images/icon_calendar.png" id="btnSelectDeliveryOption" align="absmiddle"  style="cursor:pointer;margin:0px 0px 0px 0px;" />

                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Delivery Extra Fee:</td>
                                    <td>
                                        <input type="number" id='deliver_extra' class="extra-fee" onchange="nDeliverExtra = parseFloat(this.value);updateDeliveryFee();" value="0.00">$
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">Card Message:</td>
                                    <td><textarea name="card_msg" cols="45" rows="3"></textarea></td>
                                </tr>
                                <tr>
                                    <td class="title">Signature:</td>
                                    <td><textarea name="signature" cols="45" rows="3"></textarea></td>
                                </tr>
                                <tr>
                                    <td class="title">Instructions and comments:</td>
                                    <td><textarea name="card_comment" cols="45" rows="3"></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="before-check-account">
                            Please "Check Account Info" or "Create New Account" to continue
                        </div>
                        <div class="after-check-account">
                            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="billing-info" align="center">
                                <tr>
                                    <td colspan="2" class="header">Order Price Detail</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="notice">Please choose products, fill all Billing Info and Shipping Info, Coupon Discount before Calculate Order Detail</td>
                                </tr>
                                <tr>
                                    <td class="title" width="20%" >Total Items Price: </td>
                                    <td width="80%" class="calculate-price" id="calcualte-total-items-price">N/A</td>
                                </tr>
                                <tr>
                                    <td class="title" width="20%" >Corporate Discount: </td>
                                    <td width="80%" class="calculate-price" id="shopper-group-discount">N/A</td>
                                </tr>
                             <!--   <tr>
                                    <td class="title" width="20%" >Items Tax: </td>
                                    <td width="80%" class="calculate-price" id="calcualte-total-items-tax">N/A</td>
                                </tr>-->
                                <tr>
                                    <td class="title">Delivery Fee:</td>
                                    <td class="calculate-price" id="calcualte-deliver-fee">N/A</td>
                                </tr>

                                <tr>
                                    <td class="title">Redeem Bloomex Bucks: </td>
                                    <td class="calculate-price" id="used_bucks">N/A</td>
                                </tr>
                                <tr>
                                    <td class="title">Redeem Credits: </td>
                                    <td class="calculate-price" id="used_credits">N/A</td>
                                </tr>
                                <tr>
                                    <td class="title">Discount Price:</td>
                                    <td class="calculate-price" id="calcualte-discount-price">N/A</td>
                                </tr>
                                <tr>
                                    <td class="title"><span class="donation_name"></span> Donation: </td>
                                    <td class="calculate-price donation_price">N/A</td>
                                </tr>
                                <tr>
                                    <td class="title">Total Price: </td>
                                    <td class="calculate-price" id="calcualte-total-price">N/A</td>
                                </tr>
                                <tr>
                                    <td class="title"></td>
                                    <td>
                                        <div class="msgReport" id="msgCheckoutReport" style="text-align:left;">&nbsp;</div><br/>
                                        <input type="button" value="Calculate Order Price" id="calculateOrderPrice" class="btn2" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" value="Save Order" id="saveOrder" class="btn2" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
            <script language="javascript" type="text/javascript">
                //============================================= DELIVER FUNCTIONS =============================================
                oForm = document.adminForm;
                var nDeliverExtraDay = 0;
                var nDeliverExtra = 0; //this input by operator
                var nDeliverExtraFee = 0;
                var nDeliverFee = 0;
                var nSpecialDeliverExtraFee = 0;
                var nPostalCodeDeliverExtraFee = 0;
                var nZipCheckedValue = 0;
                var nExtraDayMonth = 0;
                var nDaysValid = 0;
                var nMonthsValid = 0;
                var nYearsValid = 0;
                var bDeliverZipCode = 0;
                var bExistDeliverAddress = 0;
                var sZipCode = "";
                var bFreeShipping = 0;
                var sFreeShipping = "<?php echo $aInfomation['FreeShipping']; ?>";
                var aOption = new Array();
                var aUnAvailableDate = new Array();
                var aUnAvailableItem = new Array();

                var nMinuteNow = <?php echo $aInfomation['minute_now']; ?>;
                var nHourNow = <?php echo $aInfomation['hour_now']; ?>;
                var nDayNow = <?php echo $aInfomation['day_now']; ?>;
                var nDaysOfMonthNow = <?php echo $aInfomation['days_of_month_now']; ?>;
                var nYearNow = <?php echo $aInfomation['year_now']; ?>;
                var nMonthNow = <?php echo $aInfomation['month_now']; ?>;
                var bCutOffTime = <?php echo $aInfomation['cut_off_time']; ?>;
                var nIndex = parseFloat(nDayNow);
                var sUnAvailableDate = "<?php echo $aInfomation['unavailable_date']; ?>";
                var sSpecialDeliver = "<?php echo $aInfomation['special_deliver']; ?>";
                var nDeliverExtraFeeForSameDay = "<?php echo $aInfomation['option_param'][2]; ?>";
                var sDeliverMethodFee = "<?php echo $aInfomation['shipping_method_list_fee']; ?>";
                var sDeliverZipCode = jQuery.trim($("input[name='deliver_zip_code']").val());
                var bIsValidZipCode = 1;
                var sCannotDeliver = "We are unable to deliver to this location please select a different location";
                var sDeliverFeeExtraSameDay = " <span>Additional {money} delivery fee will apply for same day delivery</span>";
                var sSpecialDeliverExtraFee = " <span>(Deliver extra fee for the special day)</span>";
                var sPostalCodeDeliverExtraFee = " <span>(Deliver extra fee follow postal code)</span>";
                var sDeliverFeeTax = " (With deliver fee: ";
                var sUnAvailableDateText = " - No delivery service  ";
                var sUnAvailableDateMsg = "Your delivery date is not incorrect or unable to deliver!";
                var products = [];
                var countries_json = '<?php echo json_encode($aInfomation['countries']); ?>';
                var sStateTax = "<?php echo $aInfomation['state_tax']; ?>";
                var nStateTax = 0;
                var nTotalDeliveryPrice = 0; // from calendar
                var showInputs = false; // from calendar

                function updateDeliveryFee() {
                    $("input[name='deliver_fee']").val(parseFloat(nTotalDeliveryPrice) + nDeliverExtra);
                }
                
                function modalOpen(dialog) {
                    dialog.overlay.fadeIn('fast', function () {
                        dialog.container.fadeIn('fast', function () {
                            dialog.data.hide().slideDown('fast');
                        });
                    });
                }

                function copy_from_biling_to_delivery() {
                    var inputs_biling = $('.billing-info.biling td input')
                    $.each(inputs_biling, function (index, value) {
                        $('.billing-info.delivery td input[name=deliver_' + value.name.substr(5) + ']').val($(value).val())
                    });
                    var selects_biling = $('.billing-info.biling td select')
                    $.each(selects_biling, function (index, value) {
                        $('.billing-info.delivery td select[name=deliver_' + value.name.substr(5) + ']').val($(value).val())
                    });
                }
                function closeCalendar() {
                    $("#selectDeliveryOption").css('display', 'none');
                }

                $("#btnSelectDeliveryOption").click(function () {
                    if (bIsValidZipCode <= 0) {
                        alert(sCannotDeliver);
                        return;
                    }

                    if (nTotalPrice <= 0) {
                        alert("You haven't any products!");
                        return;
                    }

                    var items = $("input[name='product_id[]']");
                    var items_array = items.serialize();
                    $.post("index2.php",
                            {option: "com_phoneorder",
                                task: "selectDeliveryOption",
                                delivery_date: nMonthNow + "/" + nDayNow + "/" + nYearNow,
                                delivery_postalcode: $("input[name='deliver_zip_code']").val(),
                                delivery_state: $("input[name='state_checked_value']").val(),
                                user_id: $("input[name='user_id']").val(),
                                shipping_method: $("select[name='shipping_method']").val(),
                                product_id: items_array,
                                subtotalprice: $("#total-price").attr('price'),
                                coupon_discount_code: $("input[name='coupon_discount_code_1']").val(),
                                delivery_option_new: $("select[name='deliver_state']").val() + '[--1--]' + $("input[name='deliver_zip_code']").val()+ '[--1--]' + $("input[name='deliver_city']").val()+ '[--1--]' + $("select[name='deliver_country']").val()
                            },
                            function (data) {
                                $('#selectDeliveryOption').html(data);
                                $("#selectDeliveryOption").css('display', 'block');
                                $('#selectDeliveryOption').modal({onOpen: modalOpen, onClose: closeCalendar(), position: ["25%", "25%"], containerId: 'selectDeliveryOptionContainer', containerCss: 'selectDeliveryOptionContainer'});
                            }
                    );
                });


                var typingTimer;
                const doneTypingInterval = 500;
                const inputField = document.getElementById('selectProductId');
                const suggestionsContainer = document.getElementById('suggestions');

                inputField.addEventListener('input',  () => {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        fetchData(inputField.value)
                    }, doneTypingInterval);
                });

                     async function fetchData(query) {

                         if (!checkProductType()) {
                             return;
                         }

                        if (query.length < 3) {
                            return;
                        }
                        try {
                            let products_filter = document.getElementById('selectProductsList').value
                            const response = await fetch(`/administrator/components/com_phoneorder/autocomplete.php?term=${query}&products_filter=${products_filter}`);
                            const data = await response.json();

                            products = [];
                            sProductInformation = [];
                            suggestionsContainer.innerHTML = '';
                            data.forEach(suggestion => {
                                products.push(suggestion.productinformation);
                                sProductInformation += suggestion.productinformation.replace(/\\/g, "");
                                const div = document.createElement('div');
                                div.innerHTML = suggestion.label;
                                div.addEventListener('click', () => {
                                    $("#select_product_id").val(suggestion.id);
                                    inputField.value = suggestion.value;
                                    suggestionsContainer.innerHTML = '';
                                    $('#image_prew').hide()
                                });
                                div.addEventListener('mouseover', () => {
                                    $('#image_prew').show()
                                    $('#image_prew img').attr('src', "../components/com_virtuemart/shop_image/product/" + suggestion.href)
                                });
                                div.addEventListener('mouseout', () => {
                                    $('#image_prew').hide()
                                });
                                suggestionsContainer.appendChild(div);
                            });
                        } catch (error) {
                            console.error('Error fetching suggestions:', error);
                        }
                    }




                $("#checkCouponCode").click(function () {
                    if (jQuery.trim($("input[name='coupon_discount_code']").val()) == "") {
                        $("input[name='coupon_discount_code_value']").val("");
                        $("input[name='coupon_discount_code_type']").val("");
                        $("#couponCode").css('display', 'block');
                        $("#couponCode").html('<font color="red">Please enter your coupon code!</font>');
                        //alert("Please enter your coupon code!");
                        return;
                    }

                    sArrayOfProductID = "";
                    sArrayOfQuantityID = "";
                    sArrayOfProDuctColor = "";
                    if (nSubTotalWithOutTax) {
                        for (k = 0; k <= nTotalItem; k++) {
                            if ($("#product-id-item-" + k).val() && $("#quantity-item-" + k).val()) {
                                sArrayOfProductID = sArrayOfProductID + $("#product-id-item-" + k).val() + ",";
                                sArrayOfQuantityID = sArrayOfQuantityID + $("#quantity-item-" + k).val() + ",";
                                sArrayOfProDuctColor = sArrayOfProDuctColor + $("#balloon_" + k).val() + ",";
                            }
                        }

                        sArrayOfProductID = sArrayOfProductID.substring(0, sArrayOfProductID.length - 1);
                        sArrayOfQuantityID = sArrayOfQuantityID.substring(0, sArrayOfQuantityID.length - 1);
                        sArrayOfProDuctColor = sArrayOfProDuctColor.substring(0, sArrayOfProDuctColor.length - 1);
                    }

                    /*nSubTotalWithOutTax,*/

                    $.post("index2.php",
                            {option: "com_phoneorder",
                                task: "check_counpon_code",
                                coupon_discount_code: $("input[name='coupon_discount_code']").val(),
                                nSubTotalWithOutTax: nSubTotalWithOutTax,
                                product_id_string: sArrayOfProductID,
                                product_qty_string: sArrayOfQuantityID,
                                shopper_group_discount: parseFloat($("input[name='shopper_group_discount']").val()),
                                ajaxSend: function () {
                                    $("#couponCode").css('display', 'block');
                                    $("#couponCode").html('<img src="<?php echo $url; ?>/components/com_virtuemart/html/jquery_ajax.gif" align="absmiddle"/>&nbsp;&nbsp;Coupon Code is checking...');
                                }
                            },
                            function (data) {
                                aData = data.split("[--1--]");
                                if (jQuery.trim(aData[0]) == "success")
                                {
                                    $("#couponCode").html(aData[1]);
                                    $("input[name='coupon_discount_price']").val(aData[2]);
                                    $("input[name='coupon_discount_type']").val(aData[3]);
                                    $("input[name='coupon_discount_value']").val(aData[4]);
                                    $("input[name='coupon_discount_percent_or_total']").val(aData[5]);
                                    $("input[name='coupon_discount_product_aplly_coupon']").val(aData[6]);
                                    $("input[name='coupon_discount_code_1']").val(aData[7]);

                                    //alert(aData[2]+"==="+aData[3]);
                                } else {
                                    $("input[name='coupon_discount_price']").val("");
                                    $("input[name='coupon_discount_type']").val("");
                                    $("input[name='coupon_discount_value']").val("");
                                    $("input[name='coupon_discount_percent_or_total']").val("");
                                    $("input[name='coupon_discount_product_aplly_coupon']").val("");
                                    $("#couponCode").css('display', 'block');
                                    $("#couponCode").html(aData[1]);
                                }
                            }
                    );
                    //				alert($("input[name='coupon_discount_code_value']").val() + "===" + $("input[name='coupon_discount_code_type']").val());
                });
                $("#calculateOrderPrice").click(function () {
                    if($('#deliver_extra').val()==''){
                        $('#deliver_extra').val('0').change()
                        nDeliverExtra=0;
                    }
                    if (nTotalPrice <= 0) {
                        alert("You haven't any products!");
                        return;
                    }
                    if ($('#delivery_date').val() == '') {
                        alert("You haven't delivery date!");
                        return;
                    }

                    //nTotalPrice = parseFloat($("input[name=sub_total_price]").val());
                    nCounponDiscountValue = parseFloat($("input[name='coupon_discount_price']").val());
                    nTotalItemPrice = nTotalPrice;

                    ShopperGroupDiscount = 0;
                    nShopperGroupDiscount = parseFloat($("input[name='shopper_group_discount']").val());
                    if (nShopperGroupDiscount > 0) {
                        $("#shopper-group-discount").html("$" + formatAsMoney(nTotalPrice * nShopperGroupDiscount));
                        ShopperGroupDiscount = (nTotalPrice * nShopperGroupDiscount);
                    }

                    nTotalDeliveryFee = parseFloat($("input[name=deliver_fee]").val());

                    TotalItemPrice = nTotalItemPrice - ShopperGroupDiscount;
                    
                    //================Subscription delivery fee=================
                    var select_sub = false;
    var select_sub_months = 0;


    $.each($("select[name='select_sub_[]']:visible option:selected"), function (index, value) {
        if ($(this).val() == 'sub_3') {
            if (select_sub_months < 3) {
                select_sub_months = 3;
            }
        }

        if ($(this).val() == 'sub_6') {
            if (select_sub_months < 6) {
                select_sub_months = 6;
            }
        }

        if ($(this).val() == 'sub_12') {
            if (select_sub_months < 12) {
                select_sub_months = 12;
            }
        }

        select_sub = true;
    });

    if (select_sub) {
        nTotalDeliveryFee = parseFloat(parseFloat($("input[name=deliver_fee]").val()) * select_sub_months);
    } else {
        nTotalDeliveryFee = parseFloat($("input[name=deliver_fee]").val());
    }
                    //================/Subscription delivery fee================

                    if (nCounponDiscountValue && ($("input[name='coupon_discount_code']").val().indexOf('PC-') == -1))
                    {
                        if (nCounponDiscountValue < TotalItemPrice)
                        {
                            TotalItemPrice = TotalItemPrice - nCounponDiscountValue;
                        } else
                        {
                            TotalItemPrice = 0;
                        }
                    }
                    if($("input[name='redeem_bucks']").val()!='0'){

                        var bucks = $("input[name='bucks']").val();
                        if(bucks > parseFloat(TotalItemPrice)){
                            var used_bucks = TotalItemPrice;
                            TotalItemPrice = 0;
                        }else{
                            var used_bucks = bucks
                            TotalItemPrice = TotalItemPrice - bucks
                        }
                        $("#used_bucks").html("$" + formatAsMoney(used_bucks));
                        $("input[name='used_bucks_price']").val(parseFloat(used_bucks));
                    }else{
                        $("input[name='used_bucks_price']").val(0);
                        $("#used_bucks").html("N/A");
                    }
                    TotalItemPrice += nTotalDeliveryFee;

                    if ($("input[name='redeem_credits']").val() != '0') {

                        var credits = $("input[name='credits']").val();
                        if (credits > parseFloat(TotalItemPrice)) {
                            var used_credits = TotalItemPrice;
                            TotalItemPrice = 0;
                        } else {
                            var used_credits = credits
                            TotalItemPrice = TotalItemPrice - credits
                        }
                        $("#used_credits").html("$" + formatAsMoney(used_credits));
                        $("input[name='used_credits_price']").val(parseFloat(used_credits));
                    } else {
                        $("input[name='used_credits_price']").val(0);
                        $("#used_credits").html("N/A");
                    }

                    $("#shopper-group-discount").html("$" + formatAsMoney(ShopperGroupDiscount));
                    $("#calcualte-total-items-price").html("$" + formatAsMoney(nTotalPrice));
                    $('#calcualte-deliver-fee').html('$' + formatAsMoney(nTotalDeliveryFee));

                    $('#calcualte-total-deliver-fee').html('$' + formatAsMoney(nTotalDeliveryFee));
                    $('#calcualte-discount-price').html('$' + formatAsMoney(nCounponDiscountValue));
                    $("#calcualte-total-price").html("$" + formatAsMoney(TotalItemPrice));
                    $("input[name='total_price']").val(parseFloat(TotalItemPrice));
                    $("input[name='sub_total_price']").val(nTotalItemPrice);

                    if ($("input[name='donate']").val() != '0' && $("input[name='donation_id']")) {
                        $(".donation_price").html("$" + formatAsMoney($("input[name='donation_price']").val()));
                        $(".donation_name").html($("input[name='donation_name']").val());
                        $("#calcualte-total-price").append("<span style='color:red'> + $" + formatAsMoney($("input[name='donation_price']").val()) + "</span>")
                    } else {
                        $(".donation_price").html('N/A');
                        $(".donation_name").html('');
                    }

                });

                $("input[name='payment_method_state']").click(function(){

                    ($(this).val() == 'stripe') ? jQuery(".card_details").slideUp(500) : jQuery(".card_details").slideDown(500)
                })

                $("#saveOrder").click(function () {
                    let button = $(this);
                    aCurrentDeliveryDate = jQuery.trim($("input[name='delivery_date']").val()).split("/");
                    nDaysValid = aCurrentDeliveryDate[1];
                    nMonthsValid = aCurrentDeliveryDate[0];
                    nYearsValid = aCurrentDeliveryDate[2];
                    $("#calculateOrderPrice").trigger('click');
                    if (nTotalPrice <= 0) {
                        return;
                    }
                    if (must_be_combined > 0 && jQuery('#product-list-items').find('.product-item').length == 1) {
                        alert('The product must be combined. Add one more product.');
                        return;
                    }


                    if (bIsValidZipCode <= 0) {
                        alert(sCannotDeliver);
                        return;
                    }

                        myArray = {"user_name": "User Name",
                            "account_email": "Account Email",
                            "bill_first_name": "Billing First Name",
                            "bill_last_name": "Billing Last Name",
                            "bill_city": "Billing City",
                            "bill_zip_code": "Billing Zip Code",
                            "bill_country": "Billing Country",
                            "bill_phone": "Billing Phone Number",
                            "address_user_name": "Address Nickname",
                            "deliver_first_name": "Deliver First Name",
                            "deliver_last_name": "Deliver Last Name",
                            "deliver_address_street_number": "Deliver street number",
                            "deliver_address_street_name": "Deliver street name",
                            "deliver_city": "Deliver City",
                            "deliver_zip_code": "Deliver Zip Code",
                            "deliver_country": "Deliver Country",
                            "deliver_state": "Deliver State",
                            "deliver_phone": "Deliver Phone Number",
                            "delivery_date": "Delivery Date",
                            "name_on_card": "Name On Card",
                            "credit_card_number": "Credit Card Number",
                            "credit_card_security_code": "Cedit Card Security Code",
                            "expire_month": "Expire Month"
                        };
                    if ($("input[name='total_price']").val() == '0'
                        || $("input[name='payment_method_state']:checked").val() == 'stripe'
                        || $("input[name='payment_method_state']:checked").val() == 'offline'
                    ) {
                        delete myArray.name_on_card;
                        delete myArray.credit_card_number;
                        delete myArray.credit_card_security_code;
                        delete myArray.expire_month;
                    }
                    selectElementArray = new Array("bill_country", "bill_state", "deliver_country", "deliver_state",  "expire_month");
                    var objValid = "";
                    for (key in myArray) {
                        if (jQuery.inArray(key, selectElementArray) != -1) {
                            objValid = "select[name='" + key + "']";
                        } else {
                            objValid = "input[name='" + key + "']";
                        }



                        if (key == "deliver_zip_code") {
                            if (!isValidZipCode($("input[name='deliver_zip_code']").val())) {
                                alert("Please enter your deliver postcode again!");
                                return;
                            }
                        }

                        if (key == "bill_state") {
                            if ($("select[name='bill_state']").val() == "NA") {
                                alert("Please select a Billing State!");
                                return;
                            }
                        }

                        if (key == "deliver_state") {
                            if ($("select[name='deliver_state']").val() == "NA") {
                                alert("Please select a Deliver State!");
                                return;
                            }
                        }
                        if (jQuery.trim($(objValid).val()) == "") {
                            alert('Please enter your "' + myArray[key] + '" !');
                            $(objValid).focus();
                            return;
                        }
                    }


                    sArrayOfProductID = "";
                    sArrayOfQuantityID = "";
                    sArrayOfDeluxeSupersizeID = "";
                    sArrayOfProDuctColor = "";
                    sArrayOfSelectSub = "";
                    if (nTotalItem) {
                        for (k = 0; k <= nTotalItem; k++) {
                            if ($("#product-id-item-" + k).val() && $("#quantity-item-" + k).val()) {
                                sArrayOfProductID = sArrayOfProductID + $("#product-id-item-" + k).val() + ",";
                                sArrayOfQuantityID = sArrayOfQuantityID + $("#quantity-item-" + k).val() + ",";
                                sArrayOfDeluxeSupersizeID = sArrayOfDeluxeSupersizeID + $(".deluxe_supersize_" + k + " option:selected").text() + ",";
                                sArrayOfProDuctColor = sArrayOfProDuctColor + $("#balloon_" + k).val() + ",";
                                sArrayOfSelectSub = sArrayOfSelectSub + $(".select_sub_" + k).val() + ",";
                            }
                        }

                        sArrayOfProductID = sArrayOfProductID.substring(0, sArrayOfProductID.length - 1);
                        sArrayOfQuantityID = sArrayOfQuantityID.substring(0, sArrayOfQuantityID.length - 1);
                        sArrayOfSelectSub = sArrayOfSelectSub.substring(0, sArrayOfSelectSub.length - 1);
                        sArrayOfProDuctColor = sArrayOfProDuctColor.substring(0, sArrayOfProDuctColor.length - 1);
                    }


                    /*alert(sArrayOfProductID + "===" + sArrayOfQuantityID + "===" + sArrayOfQuantityID + "===" + nTotalItem);
                     return;*/
                    button.attr('disabled',true);
                    if (confirm('Do you want to save this order?') == true) {
                        $.post("index2.php",
                                {option: "com_phoneorder",
                                    task: "save",
                                    //check_CC_payment: 			"1",
                                    user_id: $("input[name='user_id']").val(),
                                    user_name: $("input[name='user_name']").val(),
                                    account_email: $("input[name='account_email']").val(),
                                    bill_company_name: $("input[name='bill_company_name']").val(),
                                    bill_first_name: $("input[name='bill_first_name']").val(),
                                    bill_last_name: $("input[name='bill_last_name']").val(),
                                    bill_middle_name: $("input[name='bill_middle_name']").val(),
                                    bill_suite: $("input[name='bill_address_suite']").val(),
                                    bill_street_number: $("input[name='bill_address_street_number']").val(),
                                    bill_street_name: $("input[name='bill_address_street_name']").val(),
                                    //bill_address_1: 				$("input[name='bill_address_1']").val(),
                                    //bill_address_2: 				$("input[name='bill_address_2']").val(),
                                    bill_city: $("input[name='bill_city']").val(),
                                    bill_district: $("input[name='bill_district']").val(),
                                    bill_zip_code: $("input[name='bill_zip_code']").val(),
                                    bill_country: $("select[name='bill_country']").val(),
                                    bill_state: $("select[name='bill_state']").val(),
                                    bill_phone: $("input[name='bill_phone']").val(),
                                    bill_evening_phone: $("input[name='bill_evening_phone']").val(),
                                    deluxe_supersize: sArrayOfDeluxeSupersizeID,
                                    bill_fax: $("input[name='bill_fax']").val(),
                                    delivery_address_type2: $('#delivery_address_type2').val(),
                                    address_user_name: $("input[name='address_user_name']").val(),
                                    deliver_first_name: $("input[name='deliver_first_name']").val(),
                                    deliver_last_name: $("input[name='deliver_last_name']").val(),
                                    deliver_middle_name: $("input[name='deliver_middle_name']").val(),
                                    deliver_company_name: $("input[name='deliver_company_name']").val(),
                                    deliver_suite: $("input[name='deliver_address_suite']").val(),
                                    deliver_street_number: $("input[name='deliver_address_street_number']").val(),
                                    deliver_street_name: $("input[name='deliver_address_street_name']").val(),
                                    donate: ($("input[name='donate']").val() != '0' && $("input[name='donation_id']")) ? $("input[name='donation_id']").val() : 0,
                                    deliver_phone: $("input[name='deliver_phone']").val(),
                                    used_bucks_price: $("input[name='used_bucks_price']").val(),
                                    used_credits_price: $("input[name='used_credits_price']").val(),
                                    deliver_evening_phone: $("input[name='deliver_evening_phone']").val(),
                                    deliver_cell_phone: $("input[name='deliver_cell_phone']").val(),
                                    deliver_city: $("input[name='deliver_city']").val(),
                                    deliver_district: $("input[name='deliver_district']").val(),
                                    deliver_zip_code: $("input[name='deliver_zip_code']").val(),
                                    deliver_country: $("select[name='deliver_country']").val(),
                                    deliver_state: $("select[name='deliver_state']").val(),
                                    deliver_evening_phone: $("input[name='deliver_evening_phone']").val(),
                                    deliver_cell_phone: $("input[name='deliver_cell_phone']").val(),
                                    deliver_fax: $("input[name='deliver_fax']").val(),
                                    deliver_recipient_email: $("input[name='deliver_recipient_email']").val(),
                                    occasion: $("select[name='occasion']").val(),
                                    order_create_type: $("select[name='order_create_type']").val(),
                                    order_call_type: $("input[name='order_call_type']").val(),
                                    sales_line: $("select[name='sales_line']").val(),
                                    shipping_method: $("select[name='shipping_method']").val(),
                                    card_msg: $("textarea[name='card_msg']").val(),
                                    signature: $("textarea[name='signature']").val(),
                                    card_comment: $("textarea[name='card_comment']").val(),
                                    delivery_date: jQuery.trim($("input[name='delivery_date']").val()),
                                    payment_method_state: $("input[name='payment_method_state']:checked").val(),
                                    name_on_card: $("input[name='name_on_card']").val(),
                                    credit_card_number: $("input[name='credit_card_number']").val(),
                                    credit_card_security_code: $("input[name='credit_card_security_code']").val(),
                                    expire_month: $("select[name='expire_month']").val(),
                                    expire_year: $("select[name='expire_year']").val(),
                                    total_price: $("input[name='total_price']").val(),
                                    coupon_discount_code: jQuery.trim($("input[name='coupon_discount_code']").val()),
                                    coupon_discount_price: $("input[name='coupon_discount_price']").val(),
                                    coupon_discount_type: $("input[name='coupon_discount_type']").val(),
                                    coupon_discount_value: $("input[name='coupon_discount_value']").val(),
                                    coupon_discount_percent_or_total: $("input[name='coupon_discount_percent_or_total']").val(),
                                    coupon_discount_product_aplly_coupon: $("input[name='coupon_discount_product_aplly_coupon']").val(),
                                    deliver_fee: $("input[name='deliver_fee']").val(),
                                    deliver_fee_type: $("input[name='deliver_fee_type']").val(),
                                    sub_delivery: nTotalDeliveryFee,
                                    sub_total_price: $("input[name='sub_total_price']").val(),
                                    total_tax: $("input[name='total_tax']").val(),
                                    total_deliver_tax_fee: $("input[name='total_deliver_tax_fee']").val(),
                                    exist_address_deliver: $("input[name='exist_address_deliver']:checked").val(),
                                    deliver_address_item: $("input[name='deliver_address_item']:checked").val(),
                                    shopper_group_discount: $("input[name='shopper_group_discount']").val(),
                                    product_balloon: sArrayOfProDuctColor,
                                    product_id: sArrayOfProductID,
                                    quantity: sArrayOfQuantityID,
                                    select_sub: sArrayOfSelectSub,
                                    state_tax: nStateTax,
                                    free_shipping: bFreeShipping,
                                    is_blended_day: $("input[name='is_blended_day']").val(),
                                    ajaxSend: function () {
                                        $("#msgCheckoutReport").html('<img src="<?php echo $url; ?>/administrator/components/com_virtuemart/html/jquery_ajax.gif" align="absmiddle"/>&nbsp;&nbsp;Order checkout is processing...');
                                    }
                                },
                                function (data) {
                                    aData = data.split("[--1--]");
                                    if (jQuery.trim(aData[0]) == "save_order_success") {
                                        var transaction_info = {
                                            'transaction_id': 'pom_' + aData[2],
                                            'shipping': nTotalDeliveryFee
                                        }
                                        pushPurchaseGoogleAnalytics('purchase', googleAnalyticsItems, formatAsMoney(TotalItemPrice), transaction_info);

                                        location.href = "index2.php?option=com_phoneorder&task=save_order_success&order_id="+ aData[2]+"&mosmsg=" + aData[1];
                                    } else {
                                        button.removeAttr('disabled');
                                        $("#msgCheckoutReport").html(aData[1]);
                                    }
                                }
                        );
                    } else {
                        button.removeAttr('disabled');
                    }
                });
                if (sDeliverZipCode != "") {
                    bDeliverZipCode = 1;
                }




                $("input[name='deliver_zip_code']").blur(function () {
                    $("input[name='delivery_date']").val('')
                    if (bExistDeliverAddress && jQuery.trim($("input[name='deliver_zip_code']").val())) {
                        $("input[name='zip_checked_value']").val($("input[name='deliver_zip_code']").val());
                    }
                    get_donation(jQuery.trim($("input[name='deliver_zip_code']").val()))
                    changeDeliver();
                });

                $('#deliver_state').change(function () {
                    if (bExistDeliverAddress && jQuery.trim($('#deliver_state').val())) {
                        $("input[name='state_checked_value']").val($('#deliver_state').val());
                    }

                    changeDeliver();
                });
                function changeDeliver() {
                    aCurrentDeliveryDate = jQuery.trim($("input[name='delivery_date']").val()).split("/");
                    nDaysValid = aCurrentDeliveryDate[1];
                    nMonthsValid = aCurrentDeliveryDate[0];
                    nYearsValid = aCurrentDeliveryDate[2];
                    if (bCutOffTime <= 0 && (jQuery.trim($("input[name='delivery_date']").val()) == nMonthNow + "/" + nDayNow + "/" + nYearNow)) {
                        nDeliverExtraFee = nDeliverExtraFeeForSameDay;
                    } else {
                        nDeliverExtraFee = 0;
                    }
                    nDeliverExtraFee += nDeliverExtra;
                    nSpecialDeliverExtraFee = isSpecialDate(nMonthsValid, nDaysValid);
                }

                function changeUnAvailableDate(nCurrentMonth) {
                    if (sUnAvailableDate) {
                        aUnAvailableDate = sUnAvailableDate.split("[--1--]");
                        nCurrentMonth = parseFloat(nCurrentMonth);
                        for (i = 0; i < 32; i++) {
                            oForm.deliver_day.options[i].style.color = "black";
                            oForm.deliver_day.options[i].text = oForm.deliver_day.options[i].value;
                        }


                        for (i = 0; i < aUnAvailableDate.length; i++) {
                            if (aUnAvailableDate[i] != "") {
                                aUnAvailableItem = aUnAvailableDate[i].split("/");
                                if (nCurrentMonth == parseFloat(aUnAvailableItem[0])) {
                                    oForm.deliver_day.options[aUnAvailableItem[1]].style.color = "red";
                                    oForm.deliver_day.options[aUnAvailableItem[1]].text = oForm.deliver_day.options[aUnAvailableItem[1]].text + sUnAvailableDateText;
                                }
                            }
                        }
                    }
                }


                function setSelector(objectElement, valueSelected) {
                    for (var i = 0; i < objectElement.options.length; i++) { //loop through all form elements
                        if (objectElement.options[i].value == valueSelected) {
                            objectElement.options[i].selected = true;
                        }
                    }
                }


                function isUnAvailableDate(nCurrentMonth, nCurrentDay) {
                    if (sUnAvailableDate) {
                        aUnAvailableDate = sUnAvailableDate.split("[--1--]");
                        for (i = 0; i < aUnAvailableDate.length; i++) {
                            if (aUnAvailableDate[i]) {
                                aUnAvailableItem = aUnAvailableDate[i].split("/");
                                //alert(parseFloat(nCurrentMonth) + "==" + parseFloat(aSpecialDeliverItem[0]) + "&&" +  parseFloat(nCurrentDay) + "==" + parseFloat(aSpecialDeliverItem[1]));
                                //alert( nCurrentMonth + "==" + aUnAvailableItem[0] + "----" + nCurrentDay + "==" + aUnAvailableItem[1] );
                                if (nCurrentMonth == aUnAvailableItem[0] && nCurrentDay == aUnAvailableItem[1]) {
                                    return true;
                                    break;
                                }
                            }
                        }
                        return false;
                    }
                    return false;
                }


                function isSpecialDate(nCurrentMonth, nCurrentDay) {
                    if (sSpecialDeliver) {
                        aSpecialDeliver = sSpecialDeliver.split("[--1--]");
                        for (i = 0; i < aSpecialDeliver.length; i++) {
                            if (aSpecialDeliver[i]) {
                                aSpecialDeliverItem = aSpecialDeliver[i].split("/");
                                if (parseFloat(nCurrentMonth) == parseFloat(aSpecialDeliverItem[0]) && parseFloat(nCurrentDay) == parseFloat(aSpecialDeliverItem[1])) {
                                    return aSpecialDeliverItem[2];
                                    break;
                                }
                            }
                        }
                        return 0;
                    }
                    return 0;
                }





                function getDeliverMethodFee(methodID, sType) {
                    if (sDeliverMethodFee) {
                        aDeliverMethodFee = sDeliverMethodFee.split("[--2--]");
                        for (i = 0; i < aDeliverMethodFee.length; i++) {
                            if (aDeliverMethodFee[i]) {
                                aDeliverMethodFeeItem = aDeliverMethodFee[i].split("[--1--]");
                                if (methodID == aDeliverMethodFeeItem[0]) {
                                    if (sType == "tax") {
                                        return aDeliverMethodFeeItem[2];
                                    } else {


                                        return aDeliverMethodFeeItem[1];
                                    }
                                    break;
                                }
                            }
                        }
                        return 0;
                    }
                }

                //============================================= PRODUCT FUNCTIONS =============================================
                var nTotalPrice = 0;
                var nTotal = 0;
                var googleAnalyticsItems = [];
                var must_be_combined = 0;

                var nSubTotalWithOutTax = 0;
                var nTotalTax = 0;
                var nTotalDeliverTax = 0;
                var nDeliverTaxRate = 0;
                var nTotalItem = 0;
                var aAccountInfo = new Array();
                var sProductInformation = <?php echo json_encode($aInfomation['Product']); ?>;

                function disableSelectAfterChoice() {
                    const select = document.getElementById("selectProductsList");
                    if (select.value) {
                        $('#selectProductsList').removeClass('select_type_error')
                        select.disabled = true;
                    }
                }
                function checkProductType() {
                    if($('#selectProductsList').val() == ''){
                        $('#selectProductsList').addClass('select_type_error')
                        $("#select_product_id").val("");
                        $("#selectProductId").val("");
                        alert('Please choose products type first!');
                        return false;
                    }
                    return true;
                }

                function addProductItem(select_id, quality) {
                    if (!select_id && !checkProductType()) {
                        return;
                    }

                    quality = typeof quality !== 'undefined' ? quality : 1;
                    productID = typeof select_id !== 'undefined' ? select_id : document.adminForm.select_product_id.value;
                    availableProduct = false;
                    i = 1;
                    while ($('#product-id-item-' + i).val())
                    {
                        prodID = $('#product-id-item-' + i).val();
                        if (productID == prodID) {
                            availableProduct = true;
                            quantID = "#quantity-item-" + i;
                        }
                        i++;
                    }
                    if (productID > 0 && !availableProduct) {
                        nTotalItem++;
                        sContent = $("#product-item-default").html().replace(/{noItem}/g, nTotalItem);
                        aProductInformation = sProductInformation.split("[--2--]");
                        var nSubTotal = 0;
                        //                                var nTotal = 0;
                        for (i = 0; i < aProductInformation.length; i++) {
                            aProductItem = aProductInformation[i].split("[--1--]");
                            if (aProductItem[0] == productID) {

                                googleAnalyticsItems.push({
                                    'item_name': aProductItem[2],
                                    'item_id': aProductItem[0],
                                    'price': aProductItem[3],
                                    'item_category': 'phone_order',
                                    'quantity': quality
                                });

                                sContent = sContent.replace(/{recipe}/g, aProductItem[11]);
                                sContent = sContent.replace(/{item-id}/g, aProductItem[0]);
                                sContent = sContent.replace(/{item-name}/g, "[SKU: " + aProductItem[1] + "] - " + aProductItem[2]);
                                sContent = sContent.replace(/{noItem}/g, nTotalItem);
                                sContent = sContent.replace(/{quantity-value}/g, quality);
                                sContent = sContent.replace(/{item-price}/g, formatAsMoney(parseFloat(aProductItem[3])));
                                sContent = sContent.replace(/{item-tax}/g, (aProductItem[4] * 100) + "%");
                                sContent = sContent.replace(/{real_price}/g, parseFloat(aProductItem[3]));
                                sContent = sContent.replace(/{real_tax}/g, aProductItem[4]);
                                if (aProductItem[6] != 0 || aProductItem[5] != 0) {
                                    sContent = sContent.replace(/{show_deluxe_supersize}/g, "style='display:block'");
                                    sContent = sContent.replace(/{deluxe}/g, aProductItem[5]);
                                    sContent = sContent.replace(/{supersize}/g, aProductItem[6]);
                                    sContent = sContent.replace(/{petite}/g, aProductItem[12]);
                                } else {
                                    sContent = sContent.replace(/{show_deluxe_supersize}/g, "style='display:none'");
                                }
                                //                                        sContent = sContent.replace(/{show_deluxe_supersize}/g, aProductItem[5]);
                                //nSubTotal			= ( parseFloat(aProductItem[3]) * parseFloat(aProductItem[4]) )  + parseFloat(aProductItem[3]);
                                if (aProductItem[7] != 0 || aProductItem[8] != 0 || aProductItem[9] != 0) {
                                    sContent = sContent.replace(/{show_hide_select_sub}/g, "style='display:block'");
                                    sContent = sContent.replace(/{sub_3}/g, aProductItem[7]);
                                    sContent = sContent.replace(/{sub_6}/g, aProductItem[8]);
                                    sContent = sContent.replace(/{sub_12}/g, aProductItem[9]);
                                    sContent = sContent.replace(/{sub_3_d}/g, '');
                                    sContent = sContent.replace(/{sub_6_d}/g, '');
                                    sContent = sContent.replace(/{sub_12_d}/g, '');
                                } else {
                                    sContent = sContent.replace(/{show_hide_select_sub}/g, "style='display:none'");
                                    sContent = sContent.replace(/{sub_3_d}/g, 'disabled');
                                    sContent = sContent.replace(/{sub_6_d}/g, 'disabled');
                                    sContent = sContent.replace(/{sub_12_d}/g, 'disabled');
                                }
                                nTotalTax = nTotalTax + (parseFloat(aProductItem[3]) * parseFloat(aProductItem[4]));
                                nSubTotalWithOutTax = nSubTotalWithOutTax + parseFloat(aProductItem[3]);
                                nSubTotal = formatAsMoney(parseFloat(aProductItem[3]));
                                nTotalPrice = nTotalPrice + parseFloat(parseFloat(aProductItem[3] * quality));
                                //nTotalPrice		= nTotal;
                                must_be_combined = must_be_combined + parseInt(aProductItem[10]);
                                //nTotal			= formatAsMoney(nTotalPrice);
                                sContent = sContent.replace(/{item-subtotal-price}/g, nSubTotal);

                                $("#quantity-item-" + nTotalItem).val(nSubTotal);
                                $("#total-price").html("$" + formatAsMoney(nTotalPrice));
                                $("#total-price").attr('price', formatAsMoney(nTotalPrice));
                                sContent = sContent.replace(/{item-subtotal}/g, "$" + nSubTotal);
                                break;
                            }
                        }
                        $("#product-list-items").append(sContent);
                        $("#select_product_id").val("");
                        $("#selectProductId").val("");
                        RecalculationItems();
                    }
                    else if (availableProduct) {
                        $(quantID).val(parseInt($(quantID).val(), 10) + 1);
                        RecalculationItems();
                        $("#select_product_id").val("");
                        $("#selectProductId").val("");

                    } else {
                        alert('Please choose products for your order form!');
                    }
                }

                function RecalculationItems()
                {
                    //console.log($('product-list-items.div').length);
                    //count_items = $('#product-list-items').children().length;

                    i = 1;

                    var total = 0.00;

                    $('#product-list-items').children().each(function (index)
                    {
                        //console.log($(this).html());

                        if ($(this).html() == '')
                        {
                            i++;
                            return true;
                        } else
                        {
                            deluxe_price = parseFloat($(this).find('.deluxe_supersize_' + i).val());
                            sub_price = parseFloat($(this).find('#item-subtotal-' + i).attr('price'));
                            product_price = parseFloat($(this).find('#item-subtotal-' + i).attr('real_price'));
                            product_quantity = parseInt($(this).find('#quantity-item-' + i).val());
                            
                            product_total = parseFloat((sub_price ? sub_price : (product_price + deluxe_price)) * product_quantity);

                            $(this).find('#item-subtotal-' + i).html("$" + formatAsMoney(product_total))
                            total += product_total;


                            //console.log(product_total);
                            i++;
                        }
                    });

                    total = total.toFixed(2);

                    //console.log(total);

                    nTotalPrice =nTotal = formatAsMoney(total);
                    nSubTotalWithOutTax = formatAsMoney(total);
                    //nTotalPrice = formatAsMoney(total);
                    $("#total-price").html("$" + nTotal);
                    $("#total-price").attr('price', nTotal);
                    /*
                     nTotal = formatAsMoney(nTotal);
                     $("#total-price").html("$" + nTotal);
                     $("#total-price").attr('price', nTotal);
                     */
                    if (jQuery.trim($("input[name='coupon_discount_code']").val()) != "") {
                        $("#checkCouponCode").click()
                    }
                }
                function deleteItem(noItem, real_price, real_tax) {
                    if (confirm('Do you want to delete this item?') == true) {
                        nQuantity = parseFloat($("#quantity-item-" + noItem).val());
                        real_price = $("#item-subtotal-" + noItem).attr('price')
                        nDownPrice = nQuantity * real_price;
                        //
                        nTotalPrice = nTotalPrice - nDownPrice;
                        nSubTotalWithOutTax = nSubTotalWithOutTax - real_price * nQuantity;
                        //alert( real_price + "===" + real_tax + "===" + nDownPrice + "===" + nTotalPrice );

                        $("#total-price").html("$" + formatAsMoney(nTotalPrice));
                        $("#product-item-" + noItem).html("");
                        RecalculationItems();
                    }
                }


                function saveNumberProduct(number_items) {
                    $("input[name='product_temp']").val(number_items);
                }

                function only_number(self, quantity_per_bunch)
                {
                    self.value = self.value.replace(/[^\d]/gi, '');
                    if (!self.value || self.value == 0)
                        self.value = quantity_per_bunch;
                }

                function checkNumberProduct(number_items, real_price, real_tax, noItem, deluxe_supersize) {
                    if (number_items > 0) {
                        if (!deluxe_supersize) {

                            nExtraNumber = parseInt(number_items) - parseInt($("input[name='product_temp']").val());
                            real_price = parseFloat($("#item-subtotal-" + noItem).attr('price'))
                            nTotalPrice = parseFloat($("#total-price").attr('price'));
                            nExtraRealPrice = (real_price * number_items);
                            nExtraRealPrice = formatAsMoney(nExtraRealPrice);
                            if (nExtraNumber > 0) {
                                nExtraPrice = (real_price * nExtraNumber);
                                nTotalTax = nTotalTax + parseFloat(nExtraPrice);
                                //nExtraPrice		= ( real_tax * nExtraPrice ) + nExtraPrice;
                                nSubTotalWithOutTax = nSubTotalWithOutTax + nExtraPrice;
                                nTotal = parseFloat(nTotalPrice) + nExtraPrice;
                                nTotalPrice = nTotal;
                            } else {
                                nExtraPrice = (real_price * nExtraNumber);
                                //nTotalTax			= nTotalTax + ( parseFloat(nExtraPrice) * parseFloat(real_tax) );
                                nTotalTax = nTotalTax + parseFloat(nExtraPrice);
                                //nExtraPrice		= ( real_tax * nExtraPrice ) + nExtraPrice;
                                nSubTotalWithOutTax = nSubTotalWithOutTax + nExtraPrice;
                                nTotal = parseFloat(nTotalPrice) + nExtraPrice;
                                nTotalPrice = nTotal

                            }

                        } else {
                            nExtraRealPrice = (real_price * number_items);
                            nExtraRealPrice = formatAsMoney(nExtraRealPrice);
                            var total_old = (parseFloat($("#item-subtotal-" + noItem).attr('price')) * number_items);
                            nTotal = parseFloat(nTotalPrice) - total_old + parseFloat(nExtraRealPrice);
                            nSubTotalWithOutTax = nTotalPrice = nTotal;

                        }

                        $("#item-subtotal-" + noItem).attr('price', real_price)
                        $("#item-subtotal-" + noItem).html("$" + nExtraRealPrice);
                        nTotal = formatAsMoney(nTotal);
                        $("#total-price").html("$" + nTotal);
                        $("#total-price").attr('price', nTotal);


                    } else {
                        //$("#quantity-item-"+noItem).focus();
                        alert("Please enter a number for product quantity!");
                    }
                    RecalculationItems();
                }

                function select_deluxe_supersize(value, real_price, noItem, real_tax) {
                    var number_items = $('#quantity-item-' + noItem).val()
                    real_price = parseFloat(real_price) + parseFloat(value);

                    checkNumberProduct(number_items, real_price, real_tax, noItem, value)
                    $('.ing_'+noItem).hide();
                    if(value > 0) {
                        $('.ing_'+value+'_'+noItem).show();
                    }else{
                        $('.ing_standard_'+noItem).show();
                    }
                    RecalculationItems();
                }
                function select_sub(sub, noItem, real_tax) {
                    var value = $(".select_sub_" + noItem + " option:selected").attr("attr");
                    //console.log(value, noItem, real_tax);
    var quantity_items = $('#quantity-item-' + noItem).val();

    $('#product_price_' + noItem).html('$' + parseFloat(value));

    var product_subtotal = parseFloat(parseFloat(quantity_items) * (parseFloat(value)));

    product_subtotal = product_subtotal.toFixed(2);
    //console.log(product_subtotal);
    $('#product_subtotal_' + noItem).val(product_subtotal);
    $('#item-subtotal-' + noItem).attr('price', value);
    $('#item-subtotal-' + noItem).html('$' + formatAsMoney(product_subtotal));

    RecalculationItems();
                }
                $("#addProductItem").click(function () {
                    addProductItem();
                });
                $("#checkAccInfo").click(function() {
                    checkAccInfo();
                })
                $("input[name='account_email']").change(function() {
                        checkEmailAddressExist();
                })

                $("#createAccInfo").click(function () {
                    if (!checkProductType()) {
                        return;
                    }
                    $("#checkAccInfo").hide();
                    $("#bucks").hide();
                    $("#credits").hide();
                    $("input[name='bucks']").val('');
                    $("input[name='credits']").val('');
                    $("#bucks_value").text('');
                    $("#credits_value").text('');
                    sEmail = jQuery.trim($("input[name='account_email']").val());
                    if (sEmail != "") {
                        $.ajax({
                            data: "email=" + sEmail,
                            type: "POST",
                            dataType: "html",
                            url: "?option=com_phoneorder&task=check_user_bt_info",
                            success: function (data, textStatus) {
                                var res = JSON.parse(data);
                                if (res.result) {
                                    checkAccInfo();
                                    return;
                                }
                            }
                        })

                        $("input[name='shopper_group_discount']").val('');
                        $("#couponCode").html('');
                        $("#checkCouponCode").show();
                        $("input[name='coupon_discount_code']").attr('disabled', false);
                        $.ajax({
                            data: "email=" + sEmail,
                            type: "POST",
                            dataType: "html",
                            url: "?option=com_phoneorder&task=check_corporate_user",
                            success: function (data) {
                                var res = JSON.parse(data);
                                if (res.result) {
                                    if (res.corporate_discount) {
                                        var corporate_discount = res.corporate_discount;
                                        $("input[name='shopper_group_discount']").val(corporate_discount / 100);
                                        $("#couponCode").css('display', 'block');
                                        $("#couponCode").html('<font color="red">Customer will have ' + corporate_discount + '% corporate discount</font>');
                                    }
                                    $("#checkCouponCode").hide();
                                    $("input[name='coupon_discount_code']").attr('disabled', true);
                                    $("input[name='coupon_discount_price']").val(0);
                                }
                            }
                        })
                    }
                    if (confirm('Do you want to clear all fields data in "Billing Information" and "Deliver Information"? \nWill we create a new account?') == true) {
                        $("input[name='user_name']").val("");
                        $("input[name='user_id']").val("0");
                        $("input[name='bill_company_name']").val("");
                        $("input[name='bill_first_name']").val("");
                        $("input[name='bill_last_name']").val("");
                        $("input[name='bill_middle_name']").val("");
                        $("input[name='bill_address_suite']").val("");
                        $("input[name='bill_address_street_number']").val("");
                        $("input[name='bill_address_street_name']").val("");
                        $("input[name='bill_city']").val("");
                        $("input[name='bill_district']").val("");
                        $("input[name='bill_zip_code']").val("");
                        $("input[name='bill_phone']").val("");
                        $("input[name='bill_evening_phone']").val("");
                        $("input[name='bill_fax']").val("");

                        $("input[name='deliver_city']").val("");
                        $("input[name='deliver_district']").val("");
                        $("input[name='deliver_zip_code']").val("");
                        $("input[name='deliver_phone']").val("");
                        $("input[name='deliver_evening_phone']").val("");
                        $("input[name='deliver_fax']").val("");
                        $("input[name='deliver_company_name']").val("");
                        $("input[name='deliver_first_name']").val("");
                        $("input[name='deliver_last_name']").val("");
                        $("input[name='deliver_middle_name']").val("");
                        $("input[name='deliver_address_suite']").val("");
                        $("input[name='deliver_address_street_number']").val("");
                        $("input[name='deliver_address_street_name']").val("");
                        $(".after-check-account").css("display", "block");
                        $(".before-check-account").css("display", "none");
                        showInputs = true
                        $("input[name='exist_address_deliver'][value='1']").attr('checked', true);
                        $("#deliver-address-default").html('<div class="error-msg">None</div>');
                        $("#error-report").html("You are creating a new account information!");
                        get_donation()
                    }
                });
                $("#updatebilling").click(function () {
                    $("#update_billing_result").css("display", "block");
                    $("#update_billing_result").html('<img src="/administrator/components/com_virtuemart/html/jquery_ajax.gif" align="absmiddle"/> Updating...');
                    $.post("index2.php",
                            {
                                option: "com_phoneorder",
                                task: "updateBillingInfo",
                                user_id: $("input[name='user_id']").val(),
                                bill_company_name: $("input[name='bill_company_name']").val(),
                                bill_first_name: $("input[name='bill_first_name']").val(),
                                bill_last_name: $("input[name='bill_last_name']").val(),
                                bill_middle_name: $("input[name='bill_middle_name']").val(),
                                bill_suite: $("input[name='bill_address_suite']").val(),
                                bill_street_number: $("input[name='bill_address_street_number']").val(),
                                bill_street_name: $("input[name='bill_address_street_name']").val(),
                                bill_city: $("input[name='bill_city']").val(),
                                bill_district: $("input[name='bill_district']").val(),
                                bill_zip_code: $("input[name='bill_zip_code']").val(),
                                bill_country: $("select[name='bill_country']").val(),
                                bill_state: $("select[name='bill_state']").val(),
                                bill_phone: $("input[name='bill_phone']").val(),
                                bill_evening_phone: $("input[name='bill_evening_phone']").val(),
                            },
                            function (data) {
                                var res = JSON.parse(data);
                                console.log(res)
                                if (res) {
                                    $("#update_billing_result").html("Update Successful.");
                                    //     $('#donation_tr').show()
                                    //     $('.donation_price_pre').html('$'+res['1'])
                                    //     $('.donation_name_pre').html(res['0'])
                                    //     $("input[name='donation_price']").val(res['1'])
                                    //     $("input[name='donation_name']").val(res['0'])
                                    //     $("input[name='donation_id']").val(res['3'])
                                } else {
                                    $("#update_billing_result").html("Update Wrong.");
                                }
                            }
                    )

                })

                function clearEnteredData (){
                    checkAccInfo();
                    $("#changedEmailCheckingResponse").html('').hide()
                }
                function cancelSavedData (){
                    $(".after-check-account").css("display", "none");
                    $(".before-check-account").css("display", "block");
                    showInputs = false;
                    $("#changedEmailCheckingResponse").html('').hide()
                    $("#deliver-address-default").html('<div class="error-msg">None</div>');
                }
                function overwriteData (accountInfo){
                    $("input[name='user_name']").val(accountInfo.username);
                    $("input[name='user_id']").val(accountInfo.id);
                    $("#changedEmailCheckingResponse").html('').hide()
                }

                function checkEmailAddressExist() {
                    $("#changedEmailCheckingResponse").html('').hide()
                    sEmail = jQuery.trim($("input[name='account_email']").val());
                    if (sEmail != "") {
                        $.post("index2.php",
                            {option: "com_phoneorder",
                                task: "check_email_address_exist",
                                email: sEmail
                            },
                            function (data) {
                                var res = JSON.parse(data);
                                if (res.result && showInputs) {

                                        if(res.blocked) {
                                            $("#error-report").html("This account is blocked");
                                            $(".after-check-account").css("display", "none");
                                            $(".before-check-account").css("display", "block");
                                            showInputs = false
                                            $("#deliver-address-default").html('<div class="error-msg">None</div>');
                                            return false;
                                        }else{
                                            let accountInfo = res.accountInfo
                                            $("#error-report").html("");
                                            $("#changedEmailCheckingResponse").html( `<h3 style="color:blue">System found some info with this email! You can overwrite with the server data or leave the data you have alreaady entered.</h3>` +
                                                `<input type="button" value="clear entered data and display saved data" onclick='clearEnteredData()' class="btn clear" />` +
                                                `<input type="button" value="overwrite server data with the new data you entered" onclick='overwriteData(${JSON.stringify(accountInfo)})' class="btn overwrite" />` +
                                                `<input type="button" value="cancel the process and start over" onclick='cancelSavedData()' class="btn cancel" />`
                                            ).show();

                                        }

                                }
                            })
                    }
                }



                function checkAccInfo() {
                    sEmail = jQuery.trim($("input[name='account_email']").val());
                    if (sEmail != "") {
                        $.ajax({
                            data: "email=" + sEmail,
                            type: "POST",
                            dataType: "html",
                            url: "?option=<?php echo $option ?>&task=check_account_info",
                            success: function (data, textStatus) {
                                data = jQuery.trim(data);
                                if (data == "blocked") {
                                    $("#error-report").html("This account is blocked");
                                    $(".after-check-account").css("display", "none");
                                    $(".before-check-account").css("display", "block");
                                    showInputs = false;
                                    $("#deliver-address-default").html('<div class="error-msg">None</div>');
                                    return false;
                                }
                                if (data == "error") {
                                    $("#error-report").html("This account information is not exist! Please try again or create a new account information!");
                                    $(".after-check-account").css("display", "none");
                                    $(".before-check-account").css("display", "block");
                                    showInputs = false;
                                    $("#deliver-address-default").html('<div class="error-msg">None</div>');
                                } else {
                                    aData = data.split("[--3--]");
                                    if (aData[2] != 0) {
                                        $("input[name='shopper_group_discount']").val(aData[2]);
                                        $("#couponCode").css('display', 'block');
                                        $("#couponCode").html('<font color="red">Customer already has ' + aData[2] * 100 + '% discount</font>');
                                        $("#checkCouponCode").hide();
                                        $("input[name='coupon_discount_code']").attr('disabled', true);
                                        $("input[name='coupon_discount_price']").val(0);
                                    }
                                    aAccountInfo = aData[0].split("[--1--]");
                                    aDeliver = aData[1].split("[--2--]");
                                    if (aData[6]) {
                                        sMsg = aData[5] + "<br/>" + aData[6];
                                    } else {
                                        sMsg = aData[5];
                                    }
                                    if (aData[3]) {
                                        $("#bucks").show()
                                        $("input[name='bucks']").val(aData[3]);
                                        $("#bucks_value").text("$"+aData[3])
                                    }else{
                                        $("#bucks").hide()
                                        $("input[name='bucks']").val('');
                                        $("#bucks_value").text('')
                                    }
                                    if (aData[4]) {
                                        $("#credits").show()
                                        $("input[name='credits']").val(aData[4]);
                                        $("#credits_value").text("$" + aData[4])
                                    } else {
                                        $("#credits").hide()
                                        $("input[name='credits']").val('');
                                        $("#credits_value").text('')
                                    }

                                    $("#error-report").html(sMsg);
                                    //$("#createAccInfo").css('display', 'none');
                                    $("input[name='exist_address_deliver'][value='0']").attr('checked', true);
                                    if (aAccountInfo[2] != 0) {
                                        $("input[name='user_name']").val(aAccountInfo[0]);
                                        $("input[name='user_id']").val(aAccountInfo[2]);
                                        $("input[name='bill_company_name']").val(aAccountInfo[5]);
                                        $("input[name='bill_first_name']").val(aAccountInfo[8]);
                                        $("input[name='bill_last_name']").val(aAccountInfo[7]);
                                        $("input[name='bill_middle_name']").val(aAccountInfo[9]);
                                        //$("input[name='bill_address_1']").val(aAccountInfo[13]);
                                        //$("input[name='bill_address_2']").val(aAccountInfo[14]);
                                        $("input[name='bill_city']").val(aAccountInfo[15]);
                                        //                                        setSelector(oForm.bill_state, aAccountInfo[16]);
                                        //                                        setSelector(oForm.bill_country, aAccountInfo[17]);
                                        jQuery('#bill_country').val(aAccountInfo[17]).trigger('change');
                                        setTimeout('jQuery(\'#bill_state\').val(aAccountInfo[16]);', 3000);

                                        $("input[name='bill_zip_code']").val(aAccountInfo[18]);
                                        $("input[name='bill_phone']").val(aAccountInfo[10]);
                                        $("input[name='bill_evening_phone']").val(aAccountInfo[11]);
                                        $("input[name='bill_fax']").val(aAccountInfo[12]);
                                        $(".after-check-account").css("display", "block");
                                        $(".before-check-account").css("display", "none");
                                        showInputs = true;
                                        $("input[name='bill_address_suite']").val(aAccountInfo[20]);
                                        $("input[name='bill_address_street_number']").val(aAccountInfo[21]);
                                        $("input[name='bill_address_street_name']").val(aAccountInfo[22]);
                                        $("input[name='bill_district']").val(aAccountInfo[23]);

                                    } else {
                                        $("input[name='user_name']").val(aAccountInfo[1]);
                                        $("input[name='user_id']").val(aAccountInfo[0]);
                                        $("input[name='bill_company_name']").val("");
                                        $("input[name='bill_first_name']").val("");
                                        $("input[name='bill_last_name']").val("");
                                        $("input[name='bill_middle_name']").val("");
                                        //$("input[name='bill_address_1']").val("");
                                        //$("input[name='bill_address_2']").val("");
                                        $("input[name='bill_address_suite']").val("");
                                        $("input[name='bill_address_street_number']").val("");
                                        $("input[name='bill_address_street_name']").val("");
                                        $("input[name='bill_city']").val("");
                                        $("input[name='bill_district']").val("");
                                        $("input[name='bill_zip_code']").val("");
                                        $("input[name='bill_phone']").val("");
                                        $("input[name='bill_evening_phone']").val("");
                                        $("input[name='bill_fax']").val("");
                                        $(".after-check-account").css("display", "none");
                                        $(".before-check-account").css("display", "block");
                                        showInputs = false;
                                    }


                                    if (aDeliver[0]) {
                                        nCompare = 0;
                                        $("#deliver-address-default").html("");
                                        for (j = 0; j < aDeliver.length; j++) {
                                            aDeliverTemp = aDeliver[j].split("[--1--]");
                                            if (aDeliverTemp[0] != "") {
                                                sContentTemp = $("#deliver-address-item").html().replace(/{value}/g, aDeliverTemp[0] + "[--1--]" + aDeliverTemp[1] + "[--1--]" + aDeliverTemp[2] + "[--1--]" + aDeliverTemp[5]);
                                                sContentTemp = sContentTemp.replace(/{text}/g, aDeliverTemp[3]);
                                                sContentTemp = sContentTemp.replace(/{country_state}/g, aDeliverTemp[4]);
                                                if (aDeliverTemp[5] == "undeliver") {
                                                    nCompare++;
                                                }


                                                if (j == nCompare) {
                                                    sContentTemp = sContentTemp.replace(/{status}/g, "checked");
                                                    $("#deliver-address-default").html($("#deliver-address-default").html() + sContentTemp);
                                                    $("input[name='zip_checked_value']").val(aDeliverTemp[2]);
                                                    aCurrentStateTax = aDeliverTemp[4].split("_");
                                                    nStateTax = getTaxFollowDeliveryInfo(aCurrentStateTax[0], aCurrentStateTax[1]);
                                                    if (nStateTax == -1)
                                                        nStateTax = 0;
                                                    //alert(nStateTax);
                                                } else {
                                                    sContentTemp = sContentTemp.replace(/{status}/g, "");
                                                    //												sContentTemp	= sContentTemp.replace(/{text}/g, sContentTemp );
                                                    $("#deliver-address-default").html($("#deliver-address-default").html() + sContentTemp);
                                                }
                                            }
                                        }

                                        var address_id_arr = $("input[name='deliver_address_item']:checked").val().split("[--1--]");
                                        edit_delivery_address(address_id_arr[0]);

                                        if (nCompare == aDeliver.length - 1) {
                                            bIsValidZipCode = 0;
                                        } else {
                                            bIsValidZipCode = 1;
                                        }


                                        $("input[name='deliver_address_item']").click(function () {

                                            $("input[name='exist_address_deliver'][value='0']").attr('checked', true);
                                            $("input[name='exist_address_deliver'][value='1']").attr('checked', false);
                                            bExistDeliverAddress = 0;
                                            aDeliverTemp = $("input[name='deliver_address_item']:checked").val().split("[--1--]");

                                            aDeliverSt = $("input[name='deliver_address_item']:checked").attr('title').split("_");

                                            if (aDeliverTemp[3] == "undeliver") {
                                                bIsValidZipCode = 0;
                                                alert(sCannotDeliver);
                                                return;
                                            } else {
                                                bIsValidZipCode = 1;
                                                $("input[name='zip_checked_value']").val(aDeliverTemp[2]);
                                                $("input[name='state_checked_value']").val(aDeliverSt[1]);
                                            }

                                            /*sZipCode		= aDeliverTemp[2];*/

                                            aCurrentStateTax = $("input[name='deliver_address_item']:checked").attr('title').split("_");
                                            nStateTax = getTaxFollowDeliveryInfo(aCurrentStateTax[0], aCurrentStateTax[1]);
                                            if (nStateTax == -1)
                                                nStateTax = 0;
                                            //alert(nStateTax);
                                            edit_delivery_address(aDeliverTemp[0]);
                                            changeDeliver();
                                        });
                                        $("input[name='exist_address_deliver'][value='1']").click(function () {
                                            $("input[name='deliver_address_item']").attr('checked', false);
                                            bExistDeliverAddress = 1;
                                            //sZipCode				= "";
                                            changeDeliver();
                                        });
                                    } else {
                                        $("#deliver-address-default").html('<div class="error-msg">None</div>');
                                    }
                                }

                                // CALL DEFAULT
                                $("input[name='delivery_date']").val();
                                aFreeShipping = sFreeShipping.split(",");
                                for (i = 0; i < aFreeShipping.length; i++) {
                                    if (aFreeShipping[i] == nMonthNow + "/" + nDayNow + "/" + nYearNow) {
                                        bFreeShipping = 1;
                                        break;
                                    }
                                }
                                //console.log(sFreeShipping + "=====" + bFreeShipping);

                                changeDeliver();
                            }
                        });
                    } else {
                        alert("Please enter account email to check infomation!");
                    }
                };
                $('.add_address_deliver').click(function () {
                    $("input[name='deliver_first_name']").val('')
                    $("input[name='deliver_last_name']").val('')
                    $("input[name='deliver_address_suite']").val('')
                    $("input[name='deliver_address_street_number']").val('')
                    $("input[name='deliver_address_street_name']").val('')
                    $("input[name='deliver_city']").val('')
                    $("input[name='deliver_district']").val('')
                    $("input[name='deliver_zip_code']").val('')
                    $("input[name='deliver_phone']").val('')
                    $("input[name='address_user_name']").val('')
                    $("input[name='deliver_company_name']").val('')
                    $("input[name='deliver_evening_phone']").val('')
                    $("input[name='deliver_cell_phone']").val('')
                    $("input[name='deliver_recipient_email']").val('')
                    $("#deliver_country").val('AUS')
                    $("#deliver_state").val('AL')

                })
                function edit_delivery_address(delivery_info_id) {
                    $.post("index2.php",
                            {option: "com_phoneorder",
                                task: "edit_delivery_address",
                                delivery_info_id: delivery_info_id
                            },
                            function (data) {
                                var res = JSON.parse(data);
                                if (res) {
                                    $("input[name='deliver_first_name']").val(res.first_name)
                                    $("input[name='deliver_last_name']").val(res.last_name)
                                    $("input[name='deliver_address_suite']").val(res.suite)
                                    $("input[name='deliver_address_street_number']").val(res.street_number)
                                    $("input[name='deliver_address_street_name']").val(res.street_name)
                                    $("input[name='deliver_city']").val(res.city)
                                    $("input[name='deliver_district']").val(res.district)
                                    $("input[name='deliver_zip_code']").val(res.zip)
                                    $("input[name='deliver_phone']").val(res.phone_1)
                                    $("#deliver_country").val(res.country).change()
                                    $("#deliver_state").val(res.state)
                                    $("input[name='deliver_company_name']").val(res.company)
                                    $("input[name='deliver_evening_phone']").val(res.phone_2)
                                    $("input[name='deliver_recipient_email']").val(res.user_email)
                                    $("input[name='deliver_cell_phone']").val(res.extra_field_1)

                                    if (res.address_type2 == 'B') {
                                        res.address_type2 = 'Business'
                                    }
                                    if (res.address_type2 == 'R' || res.address_type2 == '') {
                                        res.address_type2 = 'Home/Residence'
                                    }
                                    $("#delivery_address_type2").val(res.address_type2).change()
                                    get_donation(res.zip)
                                }

                            }
                    );
                }


                function get_donation(zip) {
                    $.post("index2.php",
                            {
                                option: "com_phoneorder",
                                task: "get_donation",
                                zip: zip
                            },
                            function (data) {
                                var res = JSON.parse(data);
                                if (res) {
                                    $('#donation_tr').show()
                                    $('.donation_price_pre').html('$' + res['1'])
                                    $('.donation_name_pre').html(res['0'])
                                    $("input[name='donation_price']").val(res['1'])
                                    $("input[name='donation_name']").val(res['0'])
                                    $("input[name='donation_id']").val(res['3'])
                                }
                            }
                    )
                }
                function getTaxFollowDeliveryInfo(sCountry, sState) {
                    //				alert(sCountry + "===" + sState+ "===" + sStateTax);
                    if (sStateTax && sCountry && sState) {
                        aStateTax = sStateTax.split("[--2--]");
                        for (i = 0; i < aStateTax.length; i++) {
                            aStateTaxItem = aStateTax[i].split("[--1--]");
                            if (aStateTaxItem[0] == sCountry && aStateTaxItem[1] == sState) {
                                return aStateTaxItem[2];
                            }
                        }
                    }
                    return -1;
                }


                $("#bill_country").change(function () {
                    $("#bill_state_container").html('Loading...');
                    $.post("index2.php",
                            {option: "com_phoneorder",
                                task: "getsate",
                                selector_id: "bill_state",
                                country_id: $(this).val()
                            },
                            function (data) {
                                if (data != "error") {
                                    $("#bill_state_container").html(data);
                                } else {
                                    $("#bill_state_container").html("There aren't any states of this country. Please chose other one!");
                                }
                            }
                    );
                });
                $("#deliver_country").change(function () {
                    autocomplete_shipping.componentRestrictions.country = $(this).val();
                    if( $(this).val() == 'NZL' && $('.stripe_payment_method_tr').hasClass('hideEl')) {
                            $('.payment_methods_table > tbody > tr').hide();
                            $('.stripe_payment_method_tr').show();
                            $('#stripe_payment_method').attr('checked', true);
                    } else {
                        $('.payment_methods_table > tbody > tr').removeAttr('style')
                        if($('.stripe_payment_method_tr').hasClass('showEl')){
                            $('#stripe_payment_method').attr('checked', true);
                        }else{
                            $('#cart_payment_method').attr('checked', true);
                        }

                    }
                    $("#deliver_state_container").html('Loading...');
                    $.post("index2.php",
                            {option: "com_phoneorder",
                                task: "getsate",
                                selector_id: "deliver_state",
                                country_id: $(this).val()
                            },
                            function (data) {
                                if (data != "error") {
                                    $("#deliver_state_container").html(data);
                                } else {
                                    $("#deliver_state_container").html("There aren't any states of this country. Please chose other one!");
                                }
                            }
                    );
                });
                $("#copyInfo").click(function () {
                    if (aAccountInfo[0] != "") {
                        $("input[name='address_user_name']").val(aAccountInfo[5]);
                        $("input[name='deliver_first_name']").val(aAccountInfo[8]);
                        $("input[name='deliver_last_name']").val(aAccountInfo[7]);
                        $("input[name='deliver_middle_name']").val(aAccountInfo[9]);
                        //$("input[name='deliver_address_1']").val(aAccountInfo[13]);
                        //$("input[name='deliver_address_2']").val(aAccountInfo[14]);
                        $("input[name='deliver_city']").val(aAccountInfo[15]);
                        setSelector(oForm.deliver_state, aAccountInfo[16]);
                        setSelector(oForm.deliver_country, aAccountInfo[17]);
                        $("input[name='deliver_zip_code']").val(aAccountInfo[18]);
                        $("input[name='deliver_phone']").val(aAccountInfo[10]);
                        $("input[name='deliver_evening_phone']").val(aAccountInfo[11]);
                        $("input[name='deliver_fax']").val(aAccountInfo[12]);
                    }
                });
                function formatAsMoney(mnt) {
                    mnt -= 0;
                    mnt = (Math.round(mnt * 100)) / 100;
                    return (mnt == Math.floor(mnt)) ? mnt + '.00' : ((mnt * 10 == Math.floor(mnt * 10)) ? mnt + '0' : mnt);
                }

                function isValidZipCode(value) {
                    var re = /^[A-Za-z0-9\s]{4,5}$/;
                    return (re.test(value));
                }


                $("input[name='credit_card_number']").blur(function () {
                    var first = parseInt(jQuery(this).val()[0]);
                    $('.payment_methods img').addClass('cdisabled');
                    if (first == 4) {
                        $('#icon_visa').removeClass('cdisabled');
                    } else if (first == 5 || first == 6) {
                        $('#icon_mastercard').removeClass('cdisabled')
                    } else if (first == 3) {
                        $('#icon_amex').removeClass('cdisabled')
                    }
                });

                //-->
            </script>


            <?php
            if ((isset($_REQUEST['cart_products']) AND ! empty($_REQUEST['cart_products']))) {
                $products = explode(';', $_REQUEST['cart_products']);


                foreach ($products as $product) {
                    $product = explode(',', $product);

                    if ((!empty($product[0]) AND ! empty($product[1]))) {
                        $cart_product_id = (int) $product[0];
                        $cart_product_quantity = (int) $product[1];
                        ?>
                        <script>
                            $(document).ready(function () {
                                addProductItem(<?php echo $cart_product_id; ?>, <?php echo $cart_product_quantity; ?>);
                            });
                        </script>
                        <?php
                    }
                }
            }
            if ((isset($_REQUEST['user_id']) AND ! empty($_REQUEST['user_id']))) {
                $user_id_auto = $_REQUEST['user_id'];
                $query = "SELECT email FROM #__users WHERE id = '{$user_id_auto}'";
                $database->setQuery($query);
                $oRow = $database->loadObjectList();
                $user_email_auto = $oRow[0];
                if ($user_email_auto) {
                    ?>
                    <script>
                        $(document).ready(function () {
                            $("input[name='account_email']").val("<?php echo $user_email_auto->email; ?>");
                            checkAccInfo();
                        });
                    </script>
                    <?php
                }
            }
            ?>

            <script src="/administrator/components/com_phoneorder/googleaddresscomplete.js?ref=3"></script>
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFRP59njojtx0eXlHmvYyGAtWZFwvRSLU&libraries=places&callback=initAutocomplete&language=en" async defer=""></script>


            <input type="hidden" name="zip_checked_value" value="" />
            <input type="hidden" name="state_checked_value" value="" />
            <input type="hidden" name="deliver_fee" value="0" />
            <input type="hidden" name="deliver_fee_type" value="Other" />
            <input type="hidden" name="deliver_surchage" value="0" />
            <input type="hidden" name="total_deliver_tax_fee" value="0" />
            <input type="hidden" name="total_tax" value="0" />
            <input type="hidden" name="sub_total_price" value="0" />
            <input type="hidden" name="total_price" value="0" />
            <input type="hidden" name="product_temp" value="0" />
            <input type="hidden" name="user_id" value="0" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="save" />
            <input type="hidden" name="used_bucks_price" value="0" />
            <input type="hidden" name="used_credits_price" value="0" />
            <input type="hidden" name="order_call_type" value="<?php echo $orderCallType; ?>" />
            <input type="hidden" name="shopper_group_discount" value="" />
            <input type="hidden" name="is_blended_day" value="0" />
        </form>
        <?php
    }

    //============================================= XML ORDER ===============================================
    function makeXMLOrder($option, $aInfomation) {
        global $mosConfig_live_site;
        ?>
        <style type="text/css">
            td.title {
                font:bold 12px Tahoma, Verdana;
                text-align:right;
            }


            input.btn {
                font:bold 12px Tahoma, Verdana;
                cursor:pointer;
                padding:3px;
            }
        </style>
        <script type="text/javascript">
                        function validXmlFile() {
                            if (document.adminForm.xml_file.value == "") {
                                alert("Please select your XML file before upload!");
                                document.adminForm.xml_file.focus();
                                return;
                            }

                            document.adminForm.submit();
                        }

        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        XML Order Manager:
                        <small>Add New</small>
                    </th>
                </tr>
            </table>

            <table width="100%" class="adminform" cellspacing="5" >
                <tr>
                    <td width="20%" align="right" class="title" style="text-align:right;padding-right:20px;"><strong>XML Order source file:</strong> </td>
                    <td width="80%" align="left"><a href="<?php echo $mosConfig_live_site . "/administrator/components/com_phoneorder/order.xml" ?>"><strong>Order XML file</strong></a> (Right click and choose Save Link As...)</td>
                </tr>
                <tr>
                    <td width="20%" align="right" class="title" style="text-align:right;vertical-align:top;padding-top:10px;padding-right:20px;"><strong>UnAvailable Date List:<br/>(Month/Day)</strong> </td>
                    <td width="80%" align="left" style="font:normal 12px Tahoma, Verdana; line-height:25px;">
                        <?php
                        for ($i = 0; $i < count($aInfomation["unavailable"]); $i++) {
                            echo "<strong>" . $aInfomation["unavailable"][$i]->name . "</strong> (" . $aInfomation["unavailable"][$i]->options . ")<br/>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="20%" align="right" class="title" style="text-align:right;vertical-align:top;padding-top:10px;padding-right:20px;"><strong>Payment Method:</strong> </td>
                    <td width="80%" align="left" style="font:normal 12px Tahoma, Verdana; line-height:25px;">
                        <?php
                        for ($i = 0; $i < count($aInfomation["shipping"]); $i++) {
                            echo "<strong>" . $aInfomation["shipping"][$i]->shipping_rate_name . "</strong><br/>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="20%" align="right" class="title" style="text-align:right;"><strong>XML Order file:</strong> </td>
                    <td width="80%" align="left"><input type="file" name="xml_file" size="60"/></td>
                </tr>
                <tr>
                    <td width="20%" align="right" class="title" style="text-align:right;"></td>
                    <td width="80%" align="left"><input class="btn" type="button" name="submit2" value="Upload & Save Order" onclick="validXmlFile();"/></td>
                </tr>
            </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="xml_order" />
            <input type="hidden" name="task" value="save_order_xml" />
        </form>
        <?php
    }

}
?>
