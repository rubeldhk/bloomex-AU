<?php
/**
 * @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Category
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');
global $mosConfig_absolute_path;
   date_default_timezone_set('Australia/Sydney');
/* require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_database.php" );
  require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_html.php" ); */

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_AjaxOrder {

    const AMAZON_S3 = 's3';

    const AMAZON_AWS = 'amazonaws.com';

    static function imageToBase64($image){

        $curl = curl_init('http://media.bloomex.ca/'.$image);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, 'test:ahs0hij3Ah');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $curl_response = curl_exec($curl);
        curl_close($curl);

        $imageData = base64_encode($curl_response);
        $mime_types = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'odt' => 'application/vnd.oasis.opendocument.text ',
            'docx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'gif' => 'image/gif',
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'bmp' => 'image/bmp'
        );
        $ext = pathinfo($image, PATHINFO_EXTENSION);

        if (array_key_exists($ext, $mime_types)) {
            $a = $mime_types[$ext];
        }
        return 'data: '.$a.';base64,'.$imageData;
    }

    private static function imageFromS3($image): string
    {
        global $mosConfig_amazon_central, $mosConfig_amazon_bucket_name;

        return sprintf('https://%s.%s.%s.%s/%s', $mosConfig_amazon_bucket_name, self::AMAZON_S3, $mosConfig_amazon_central, self::AMAZON_AWS, $image);
    }

    private static function checkImageExists($url): bool
    {
        $headers = @get_headers($url);
        return $headers && strpos($headers[0], '200') !== false;
    }

    function RateHistoryRefresh($option, $rows) {
        ?>
        <table id="RateHistoryData_new" width="100%" style="table-layout:fixed; word-wrap: break-word;" class="adminform">
            <tr>
                <th width="10%">rating</th>
                <th width="10%">coef orders amount</th>
                <th width="10%">coef positive orders</th>
                <th width="10%">money average</th>
                <th width="10%">coef obsence words</th>
                <th width="10%">coef threat words</th>
                <th width="10%">positive orders</th>
                <th width="10%">negative orders</th>
                <th width="10%">date added</th>
            </tr>

            <?php
            if($rows){

                foreach ($rows as $rating_h) {
                    ?>
                    <tr>
                        <td><?php echo $rating_h->rating; ?></td>
                        <td><?php echo $rating_h->coef_orders_amount; ?></td>
                        <td><?php echo $rating_h->coef_positive_orders; ?></td>
                        <td><?php echo $rating_h->coef_money_average; ?></td>
                        <td><?php echo $rating_h->coef_obsence_words; ?></td>
                        <td><?php echo $rating_h->coef_threat_words; ?></td>
                        <td><?php echo $rating_h->positive_orders; ?></td>
                        <td><?php echo $rating_h->negative_orders; ?></td>
                        <td><?php echo $rating_h->date; ?></td>
                    </tr>

                    <?php
                }
            }
            ?>
        </table>
        <?php
        require_once '../end_access_log.php';
        exit(0);
    }
    function loadOrderHistory($option, $rows) {
        global $mosConfig_live_site,$mosConfig_groups_can_see_hidden_comments,$my,$database;
        $sImagePath = $mosConfig_live_site . "/administrator/images/";
         $query= "SELECT id from tbl_mix_user_group  WHERE user_id = ". $my->id." AND user_group_id in (".implode(',',$mosConfig_groups_can_see_hidden_comments).")";
        $database->setQuery($query);
        $checkUserCanSeeHiddenComments	= $database->loadResult();
        ?>

        <table width="100%" class="adminform">
            <tr>
                <th width="20%" style="text-align:left;">Date Added</th>
                <th width="10%">C/N</th>
                <th width="10%">W/N</th>
                <th width="10%">N/R</th>
                <th width="10%">F/D</th>
                <th width="10%">Status</th>
                <th width="10%">User name</th>
                <th width="40%">Comment</th>
            </tr>
            <?php
            foreach ($rows as $item) {
                    $images = '';
                    if($item->images){
                        foreach($item->images as $img_history){
                            if(strpos($img_history->image_link, "bloomex.com.au") !== false || strpos($img_history->image_link, "bloomex.ca") !== false) {
                                $img_history->image_link = HTML_AjaxOrder::imageToBase64($img_history->image_link);
                                $images .= "<img  class='history_attached_image' history_id=" . $img_history->history_id . " style='width:60px;padding: 10px;cursor: pointer;' src='" . $img_history->image_link . "'/>";
                            } else {
                                $img_history->thumb_link = HTML_AjaxOrder::imageFromS3($img_history->thumb_link);
                                $img_history->image_link = HTML_AjaxOrder::imageFromS3($img_history->image_link);
                                $thumbExists = HTML_AjaxOrder::checkImageExists($img_history->thumb_link);
                                $imageExists = HTML_AjaxOrder::checkImageExists($img_history->image_link);

                                if (!$thumbExists || !$imageExists) {
                                    $img_history->thumb_link = 'https://blx-public-resouces.s3.us-west-2.amazonaws.com/icons/file-unavailable-thumb.png';
                                    $img_history->image_link = 'https://blx-public-resouces.s3.us-west-2.amazonaws.com/icons/file-unavailable.png';
                                }
                                $images .= "<img  class='history_image_original_" . $img_history->id . "' style='display:none' src='" . $img_history->image_link . "'/>";
                                $images .= "<img  class='history_attached_image history_image_id_" . $img_history->id . "' history_id=" . $img_history->id . " style='width:60px;padding: 10px;cursor: pointer;' src='" . $img_history->image_link . "'/>";
                            }
                        }
                    }
                    $videos = '';
                    if ($item->videos) {
                        foreach ($item->videos as $key => $video_history) {
                            $index = $key + 1;
                            $videos .= "<button class='history_attached_video' history_id=" . $video_history->history_id . " video_link='" . HTML_AjaxOrder::imageFromS3($video_history->video_link) . "' style='margin: 5px;'>Video " . $index . "</button>";
                        }
                    }
                ?>
                <tr>
                    <td style="white-space:nowrap"><?php echo $item->date_added; ?></td>
                    <td style="text-align:center;"><img src="<?php echo ( intval($item->customer_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                    <td style="text-align:center;"><img src="<?php echo ( intval($item->warehouse_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                    <td style="text-align:center;"><img src="<?php echo ( intval($item->recipient_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                    <td style="text-align:center;"><img src="<?php echo ( intval($item->security_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                    <td style="text-align:center;"><strong><?php echo $item->order_status_name; ?></strong></td>
                    <td style="text-align:center;"><strong><?php echo $item->user_name; ?></strong></td>
                    <td style="text-align:left;word-break: break-word;">
                    <?php if($item->order_status_code == 'hc' && !$checkUserCanSeeHiddenComments){
                        echo '<span style="color:red">You are not allowed to see this comment </span>';
                    } else {
                        echo ( $item->comments != "" ) ? $item->comments : ""; echo ( $item->images) ? "<div class='view_images'>View Attached Image(s)</div>" : ""; ?><div style="display:none;" class="history_<?php echo $item->order_status_history_id; ?>"><?php echo $images; ?></div>

                        <?php echo ( $item->comments == '' && $item->videos) ? "<div class='view_videos'>View Attached Video(s)</div>" : "";  ?>
                        <div style="display:none;" class="history_video_<?php echo $item->order_status_history_id; ?>"><?php echo $videos; ?></div>
                    <?php } ?>
                </tr>
                <?php
            }
            ?>
        </table>

     <script>
            $('.history_attached_image').click(function(){
                $('#myModal').show()
               $('#img01').attr('src',$(this).attr('src'));
               $('.mini_images').html($('.history_'+$(this).attr('history_id')).html());
                $('.mini_images').find('.history_attached_image').click(function(){
                    $('#img01').attr('src',$(this).attr('src'));
                })
                $('.mini_videos').hide()
            })
            $('.close_modal').click(function(){
                $('#myModal').hide();
                $('.mini_videos').html('');
               $('.mini_images').html('');
            })
            $('.view_images').click(function(){
                if($(this).next().hasClass('show_images')){
                    $(this).next().removeClass('show_images').hide();
                }else{
                    $(this).next().addClass('show_images').show();
                }
            })

            $('.view_videos').click(function () {
                jQuery('.arrow-button').hide()
                var videoContainer = $(this).siblings('div[class^="history_video_"]');
                if (videoContainer.hasClass('show_video')) {
                    videoContainer.removeClass('show_video').hide();
                } else {
                    videoContainer.addClass('show_video').show();
                }
            });
            $('.history_attached_video').click(function (event) {
                event.preventDefault();

                jQuery('.arrow-button').hide()
                $('#img01').hide();
                $('.mini_videos').show();
                $('.rotate-button').hide();
                $('#myModal').show();
                var src = $(this).attr('video_link');
                var videoElement = document.createElement('video');
                videoElement.controls = true;
                videoElement.width = 600;
                videoElement.src = src;
                // videoElement.style = "margin-top: 14vh;";
                videoElement.type = 'video/webm';

                $('.mini_videos').empty();
                $('.mini_videos').append(videoElement);
            });
        </script>
        <?php
        require_once '../end_access_log.php';
        exit(0);
    }

    function loadAjaxOrder($option, $aInfomation, $aList) {
        global $mosConfig_live_site, $database, $my;
        $sImagePath = $mosConfig_live_site . "/administrator/images/";

        date_default_timezone_set('Australia/Sydney');

        if(isset($aInfomation["ForeignOrder"])) {
            date_default_timezone_set('Pacific/Auckland');
        }

        ?>
        <script type="text/javascript">
            $.jtabber({
                mainLinkTag: "#Tab a", // much like a css selector, you must have a 'title' attribute that links to the div id name
                activeLinkClass: "selected", // class that is applied to the tab once it's clicked
                hiddenContentClass: "hiddencontent", // the class of the content you are hiding until the tab is clicked
                showDefaultTab: 1, // 1 will open the first tab, 2 will open the second etc.  null will open nothing by default
                showErrors: true, // true/false - if you want errors to be alerted to you
                effect: 'slide', // null, 'slide' or 'fade' - do you want your content to fade in or slide in?
                effectSpeed: 'fast' // 'slow', 'medium' or 'fast' - the speed of the effect
            });
            function rateChange(id, user_name) {
                if ($("textarea[name=rate_comment_inside]").val() == '') {
                    alert("Please enter comment!");
                    return;
                }
                if ($("input[name=ChangeRate]").val() > 10) {
                    alert("should be less than 10, Please enter again!");
                    return;
                }

                $("#updateRateReport").html('<img src="<?php echo (isset($sImgLoading)) ? $sImgLoading : ''; ?>" align="absmiddle"/> Update...');
                $("#updateRateReport").css("display", "block");
                /*
                $.post("index2.php",
                        {option: "com_ajaxorder",
                            task: "changeRate",
                            id: id,
                            user_name: user_name,
                            rate: $("input[name=ChangeRate]").val(),
                            comment: $("textarea[name=rate_comment_inside]").val()
                        },
                function (data) {
                    var aData = data.split("[--1--]");
                    //alert("Data Loaded: " + data);
                    if (aData[0] == "success") {
                        $('#RateHistoryData').html();
                        var header = " <tr><th width='50%'>Comment</th><th width='10%'>Previous rating</th><th width='20%'>Date</th><th width='20%'>User</th></tr>";
                        $("#RateHistoryData").html(header + aData[1]);
                        $("#numberRate").html("Current Customer's Rating: " + $("input[name=ChangeRate]").val());
                        //RateHistoryData
                        $("#updateRateReport").html("Update Rating Successful.");
                    } else {
                        $("#updateRateReport").html("Update Rating Wrong.");
                    }
                }
                );*/
    
                $.ajax({
                    url: 'index2.php',
                    type: 'POST',
                    data: { 
                        option: 'com_ajaxorder',
                        task: 'changeRate',
                        id: id,
                        rate: $("#ChangeRate").val(),
                        comment: $("textarea[name=rate_comment_inside]").val()
                    },
                    dataType: 'json',
                    success: function(json) {
                        if (json.result) {
                            $('#RateHistoryData > tbody:last-child').append(json.tr);
                            $('#user_rate').text(json.user_rate);
                            $("#updateRateReport").html("Update Rating Successful.");
                        }
                        else {
                            $("#updateRateReport").html("Update Rating Wrong.");
                        }
                    }
                });
            }
            jQuery('.marking_name').mouseover(function () {
                if (!jQuery('.marking_tooltrip').hasClass('hide_tooltrip')) {
                    jQuery(this).find('.marking_tooltrip').show()
                }
            });
            jQuery('.marking_name').mouseout(function () {
                jQuery(this).find('.marking_tooltrip').hide()
            });
            function update_mark_history(){
                $("#refreshOrderHistory").html('<div style="font: bold 11px Tahoma;color:#FF6600;line-height:24px;"><img src="<?php echo $sImgLoading; ?>" align="absmiddle"/> Loading...</div>');

                $.post("index2.php",
                    {option: "com_ajaxorder",
                        task: "markRemove",
                        desc: $('#mark_history_description').val(),
                        mark_id: $('#mark_history_id').val(),
                        order_id: $('#mark_history_order_id').val(),
                        published:$('#mark_history_published').val()
                    },
                    function (data){
                        if($('#mark_history_published').val()=='N'){
                            $("#mark_"+$('#mark_history_id').val()).removeClass('label-primary').addClass('label-default')
                            $("#mark_"+$('#mark_history_id').val()).attr('publish','Y')
                            $('.mark_history_update_msg').html('mark unpublished')
                        }else{
                            $("#mark_"+$('#mark_history_id').val()).removeClass('label-default').addClass('label-primary')
                            $("#mark_"+$('#mark_history_id').val()).attr('publish','N')
                            $('.mark_history_update_msg').html('mark published')
                        }
                        $('button[name=refresh-order-history]').trigger('click');
                    }
                );
            }
            function  close_popup(){
                jQuery("#openModal").hide()
                jQuery("#popup_details").html('')
            }
            function action_mark (mark_id,order_id,published="N") {

                if($('#mark_'+mark_id).attr('publish')=='N'){
                    var action = 'Unpublish Mark';
                }else{
                    var action = 'Publish Mark';
                }
                var mark_history_description = '<div class="">';
                mark_history_description += '<textarea class="form-control" style="width: auto;margin: 0px auto 10px " maxlength="120" placeholder="maximum length 120 characters" id="mark_history_description"  rows="3" cols="45"></textarea>';
                mark_history_description += '<input type="hidden" id="mark_history_published" value="'+$('#mark_'+mark_id).attr('publish')+'">';
                mark_history_description += '<input type="hidden" id="mark_history_order_id" value="'+order_id+'">';
                mark_history_description += '<input type="hidden" id="mark_history_id" value="'+mark_id+'">';
                mark_history_description += '<button type="button" style="margin-right: 10px;" class="btn btn-success" onclick="update_mark_history()">Save</button>';
                mark_history_description += '<button type="button"  onclick="close_popup()" class="btn btn-default">Close</button>';
                mark_history_description += '<span class="mark_history_update_msg"></span>';
                mark_history_description += '</div>';
                $('#openModal').show();
                $('.modalDialog>div').css("width","400px");
                $('#popup_details').html(mark_history_description);
                $('#popup_title').html("<h4>"+action+"</h4>");

            };
        </script>


        <style type="text/css">
            .mark_history_update_msg{
                margin-top: 10px;
                display: block;
                text-align: center;
                color: #398439;
                font-size: 18px;
            }
            .marking_tooltrip {
                display: none;
                background-color: #000;
                padding: 10px 5px 5px 5px;
                border-radius: 10px;
                color: #fff;
                width: -webkit-fill-available;
                position: absolute;
                z-index: 10;
                font-size: 12px;
                top: 23px;
                left: 0;
                opacity: 0.8;
                word-break: break-all;
                white-space: normal;
            }
            #popup_title h4{
                border-bottom: 1px solid #ccc;
                text-align: center;
                padding-bottom: 10px;
            }
            .marking_name {
                font-family: Verdana;
                font-weight: 400;
                position: relative;
                font-size: 12px;
                display: block;
                float: left;
                padding: 4px 5px;
                cursor: pointer;
            }
            .mailbotAjaxResult {
                clear: right;
                float: right;
            }
            .refresh-customer-rating{
                float: right;
                font-family: Verdana;
                font-weight: 400;
                position: relative;
                font-size: 12px;
                display: block;
                border: none;
                line-height: 1;
            }
            .tooltrip_autor{
                margin-top: 10px;
                display: block;
                font-size: 10px;
                color: #337ab7;
            }
            table.order-header{
                margin:5px 0px 5px 0px;
            }

            table.order-header td {
                background-color:#BE4C34;
                font:bold 12px Tahoma, Verdana;
                text-transform:uppercase;
                text-align:center;
                line-height:160%;
                color:#FFF;
            }

            table.adminform th{
                font:bold 12px Tahoma, Verdana;
                text-align:center;
            }

            table.adminform {
                margin:0px 0px 10px 0px;
            }

            table.adminform td{
                font:normal 11px Tahoma, Verdana;
                line-height:140%;
                padding:5px;
            }

            table.adminform td.title{
                font:bold 11px Tahoma, Verdana;
                line-height:140%;
            }

            table.adminform td.title2{
                font:bold 11px Tahoma, Verdana;
                text-align:right;
                padding-right:8px;
                line-height:140%;
            }

            div.close-button{
                font:bold 11px Tahoma, Verdana;
                margin:0px 0px 0px 10px;
                text-transform:none;
                line-height:170%;
                color:#FFFF00;
                cursor:pointer;
                float:left;
            }

            input.button {
                cursor:pointer;
            }


            #Tab a, #Tab a:active, #Tab a:visited {
                font:normal 11px Tahoma;
                border-top:1px solid #CCC;
                border-left:1px solid #CCC;
                border-right:1px solid #CCC;
                text-decoration:none;
                background:#E6D48E;
                margin-right:5px;
                padding:5px;
                outline:none;
                display:block;
                float:left;
                color:#000;
            }

            #Tab a.selected, #Tab a.selected:active, #Tab a.selected:visited {
                text-decoration:none;
                background:#C51D1D;
                color:#fff;
                outline:none;
            }

            .hiddencontent {
                padding:10px 5px 5px 5px;
                border:1px solid #D5D5D5;
                background:#fff;
                display:none;
            }
            #editOrderHistory {
                max-height: 253px;
                overflow-y: auto;
                overflow-x: hidden;
            }
            #userRateingnew{
                max-height: 200px;
                overflow-y: auto;
            }
            .clear {
                clear:both;
            }
        </style>
        <input type='hidden' name='rate_user_name' value='<?php echo $aInfomation["UserName"]; ?>'>
        <table class="order-header" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tr>
                <td>
                    <div class="close-button">(X)Close</div>
                    Order Detail
                </td>
            </tr>
        </table>


        <table cellpadding="0" cellspacing="0" width="100%" border="0">
            <tr>
                <td width="40%" style="border:none;vertical-align:top;">
                    <table width="100%" class="adminform">
                        <tr>
                            <th colspan="2">Purchase Order</th>
                        </tr>
                        <tr>
                            <td width="35%" class="title">Order Number:</td>
                            <td width="65%"><?php echo sprintf("%08d", $aInfomation["OrderInfo"]->order_id); ?></td>
                        </tr>
                        <tr>
                            <td class="title">Delivery Date:</td>
                            <script type="text/javascript">
                            function showCalendar(id, format) {
                            var el = document.getElementById(id);
                            if (calendar != null) {
                              // we already have some calendar created
                              calendar.hide();                 // so we hide it first.
                            } else {
                              // first-time call, create the calendar.
                              var cal = new Calendar(false, null, selected, closeHandler);
                              // uncomment the following line to hide the week numbers
                              // cal.weekNumbers = false;
                              calendar = cal;                  // remember it in the global var
                              cal.setRange(1900, 2070);        // min/max year allowed.
                              cal.create();
                            }
                            calendar.setDateFormat(format);    // set the specified date format
                            calendar.parseDate(el.value);      // try to parse the text in field
                            calendar.sel = el;                 // inform it what input field we use
                            calendar.showAtElement(el);        // show the calendar below it

                            return false;
                            }


                            jQuery( '.file-form' ).submit( function( e ) {

                                    jQuery('#upload-button').val('Uploading .... ')
                                    jQuery('#error_text').hide()
                                   jQuery.ajax( {
                                     url: '/administrator/components/com_ajaxorder/uploadfileinhistory.php',
                                     type: 'POST',
                                     data: new FormData( jQuery(this)[0] ),
                                     processData: false,
                                     contentType: false,
                                    success: function(data)
                                        {
                                            if(data=='success'){
                                                jQuery('#upload-button').val('Upload Image')
                                            }else{
                                                jQuery('#error_text').html(data).show()
                                            }
                                        },
                                        error: function(data) {
                                            console.log(data);
                                        }
                                   } );


                             e.preventDefault();
                                } );

                    $('.view_images').click(function(){
                        if($(this).next().hasClass('show_images')){
                            $(this).next().removeClass('show_images').hide();
                        }else{
                            $(this).next().addClass('show_images').show();
                        }
                    })
                            $('.view_videos').click(function () {
                                jQuery('.arrow-button').hide()
                                var videoContainer = $(this).siblings('div[class^="history_video_"]');
                                if (videoContainer.hasClass('show_video')) {
                                    videoContainer.removeClass('show_video').hide();
                                } else {
                                    videoContainer.addClass('show_video').show();
                                }
                            });
                            $('.history_attached_video').click(function (event) {
                                event.preventDefault();

                                jQuery('.arrow-button').hide()
                                $('#img01').hide();
                                $('.mini_videos').show();
                                $('.rotate-button').hide();
                                $('#myModal').show();
                                var src = $(this).attr('video_link');
                                var videoElement = document.createElement('video');
                                videoElement.controls = true;
                                videoElement.width = 600;
                                // videoElement.style = "margin-top: 14vh;";
                                videoElement.src = src;
                                videoElement.type = 'video/webm';

                                $('.mini_videos').empty();
                                $('.mini_videos').append(videoElement);
                            });


                $('.history_attached_image').click(function(){
                    $('#myModal').show()
                   $('#img01').attr('src',$(this).attr('src'));
                   $('.mini_images').html($('.history_'+$(this).attr('history_id')).html());
                    $('.mini_videos').hide()

                $('.mini_images').find('.history_attached_image').click(function(){
                    $('#img01').attr('src',$(this).attr('src'));
                })

                })
                $('.close_modal').click(function(){
                    $('#myModal').hide();
                    $('.mini_videos').html('');
                   $('.mini_images').html('');
                })



                            </script>
                            <td><input type="text" name="ddate_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" id="ddate_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" value="<?php echo date('d-m-Y', strtotime($aInfomation["OrderInfo"]->ddate)); ?>" onclick="return showCalendar('ddate_<?php echo $aInfomation["OrderInfo"]->order_id; ?>', 'dd-mm-y');" readonly>
                                <input type="button" onclick="return updateddate('<?php echo $aInfomation["OrderInfo"]->order_id; ?>');" value="Update">
                                <br/>
                                <input id="notify_warehouse_inside_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" type="checkbox"> Notify warehouse?
                                <br/>
                                <input id="notify_customer_inside_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" type="checkbox"> Notify customer?
                                <div id="ddate_loading"></div></td>
                        </tr>
                        <?php if ($aInfomation["OrderInfo"]->coupon_discount > 0 && $aInfomation["OrderInfo"]->coupon_code) { ?>
                            <tr>
                                <td class="title">Coupon Code:</td>
                                <td>
                                    <b><?php echo $aInfomation["OrderInfo"]->coupon_code; ?></b>
                                    <?php if ($aInfomation["OrderInfo"]->coupon_type == "percent") { ?>
                                        ( -<?php echo $aInfomation["OrderInfo"]->coupon_value; ?>% )
                                    <?php } else { ?>
                                        ( -$<?php echo $aInfomation["OrderInfo"]->coupon_value; ?> )
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td class="title">Customer Instructions:</td>
                            <td><?php echo $aInfomation["OrderInfo"]->customer_comments; ?></td>
                        </tr>
                        <tr>
                            <td class="title">Occasion:</td>
                            <td><?php echo $aInfomation["OrderInfo"]->customer_occasion; ?></td>
                        </tr>
                        <tr>
                            <td class="title">Operator:</td>
                            <td><?php echo $aInfomation["OrderInfo"]->operator; ?></td>
                        </tr>
                        <tr>
                            <?php if (!empty($aInfomation['OrderInfo']->order_call_type)): ?>
                                <td class="title">Type of Order:</td>
                                <td><?php echo $aInfomation["OrderInfo"]->order_call_type; ?></td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <td class="title">Order Date:</td>
                            <td><?php echo date("d-M-Y H:i ", $aInfomation["OrderInfo"]->cdate); ?></td>
                        </tr>
                        <tr>
                            <td class="title">Order Status:</td>
                            <td><?php echo $aInfomation["OrderInfo"]->order_status; ?></td>
                        </tr>
                        <tr>
                            <td class="title">Card Message:</td>
                            <td><?php echo nl2br(htmlspecialchars_decode($aInfomation["OrderInfo"]->customer_note, ENT_QUOTES)); ?></td>
                        </tr>
                        <tr>
                            <td class="title">IP Address:</td>
                            <td><?php echo $aInfomation["OrderInfo"]->ip_address; ?></td>
                        </tr>
                        <tr>
                            <td class="title">Customer ID:</td>
                            <td><?php echo $aInfomation["OrderInfo"]->user_id; ?></td>
                        </tr>
                        <!--<tr>
                            <td class="title">Operator:</td>
                            <td><?php /* echo $aInfomation["OperatorInfo"]; */ ?></td>
                        </tr>	-->
                    </table>
                </td>
                <td width="60%" style="border:none;vertical-align:top;">
                    <div id="Tab">
                        <a href="#" title="orderStatusChange">Order Status Change</a>
                        <a href="#" title="orderHistory">Order History</a>
                        <?php if ($my->prevs->warehouse_only == false) { ?>
                            <a href="#" title="editOrder">Edit Order</a>
                        <?php } ?>
                        <a href="#" title="editCardMessage">Edit Card Message</a>
                        <a href="#" title="specialInstructionsComments">Special Instructions & Comments</a>
                        <a href="#" title="ordercondition">Order Condition</a>
                        <?php if ($aInfomation['PartnerOrderHistory']) { ?>
                            <a href="#" title="partnerOrderHistory">Partner Assigned Order History</a>
                        <?php } ?>
                        <a href="#" title="Other">Other</a>
                        <div class="clear"></div>
                    </div>
                    <div id="orderStatusChange" class="hiddencontent">
                        <table width="100%" class="adminform">
                            <tr>
                                <th colspan="3">Order Status Change</th>
                            </tr>
                            <tr>
                                <td width="15%" class="title">
                                    Order Status:
                                </td>
                                <td width="55%">
                                    <?php
                                    echo $aList['OrderStatus'];
                                    echo $aList['OrderWareHouse'];
                                    echo $aList['OrderPriority'];
                                    ?>
                                </td>
                                <td width="30%">
                                    <input id="current_priority_inside<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="current_priority_inside" value="<?php echo $aInfomation["OrderInfo"]->priority; ?>" type="hidden">
                                    <input id="current_warehouse_inside<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="current_warehouse_inside" value="<?php echo $aInfomation["OrderInfo"]->warehouse; ?>" type="hidden">
                                    <input id="current_order_status_inside<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="current_order_status_inside2" value="<?php echo $aInfomation["order_status_code"]; ?>" type="hidden">
                                    <input name="current_order_status_inside" value="<?php echo $aInfomation["OrderInfo"]->order_status; ?>" type="hidden">
                                    <div id ="updateOrderStatusReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:150%;"></div>
                                    <div id ="sendOrderToIRISInside" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:150%;"></div>
                                    <input type="button" class="button update-status-inside" id ="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="update-status-inside" value="Update Status" />
                                </td>
                            </tr>
                            <tr>
                                <td width="15%" class="title">
                                    Comment:
                                </td>
                                <td width="55%">
                                    <textarea name="order_comment_inside" rows="12" cols="65"></textarea>
                                </td>
                                <td width="30%">
                                    <input name="notify_warehouse_inside"    type="checkbox"/> Notify Production?<br>
                                    <input name="notify_customer_inside"   type="checkbox"/> Notify Customer?<br>
                                    <input name="notify_security_inside" type="checkbox"> Fraud Detection?<br>
                                    <?php if ($aInfomation["ShippingInfo"]->user_email) { ?>
                                        <input name="notify_recipient_inside" type="checkbox"> Notify Recipient?<br>
                                    <?php } ?>
                               <!--     <input name="notify_supervisior_inside" checked="checked"  type="checkbox"> Notify Supervisior?<br>-->
                                    <input name="include_comment_inside" checked="checked" type="checkbox"/> Include this comment?<br>
                               <!--     <input name="notify_iris_inside" value="Y" type="checkbox"> Notify IRIS?-->
                                </td>
                            </tr>
                             <tr>
                                 <td colspan="3"  class="title">

                                    <form class="file-form" id="fileform_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" action="/administrator/components/com_ajaxorder/uploadfileinhistory.php" method="POST" enctype="multipart/form-data">
                                        <div style="float: left;width: 40%;">
                                            <input type="file" id="fileUploadHistory" accept='image/*' name="fileUploadHistory[]" multiple="">
                                            <input id="uploadfile" name="uploadfilehidden" value="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" type="hidden">
                                        </div>
                                        <div style="float: left;width: 50%;">

                                            <input  style="float: left;width: 40%;" text_id ="<?php echo $aInfomation["OrderInfo"]->order_id; ?>"  class="button send_substitution_text" value="Send Substitution Text " type="button">
                                            <a target="_blank" href="/administrator/index2.php?option=com_sms_conversation&task=edit&hidemainmenu=1&number=<?php echo html_entity_decode($aInfomation["BillingInfo"]->phone_1, ENT_QUOTES); ?>"  class="send_sms">Send SMS</a>
                                            <div  style="float: right;width: 50%;" id ="success_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" ></div>
                                        </div>
                                        <input  name="username" value="<?php echo $my->username; ?>" type="hidden">
                                        <input id="order_status_code_<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="order_status_code" value="P" type="hidden">
                                        <button  type="submit" style="display:none" id="upload-button_<?php echo $aInfomation["OrderInfo"]->order_id; ?>">Upload Image</button>
                                        <div style="color:red;display: none;" id="error_text">File is empty or has wrong format, please try another file</div>
                                     </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="orderHistory" class="hiddencontent">

                        <div style="margin-bottom: 10px;" class="row">

                            <div class="col-md-4">
                                <?php echo $aInfomation["OrderMark"]; ?>
                                <input  type="button" class="btn btn-primary mb-2 order-mark pull-left clear" style="width: 100%; margin-top: 6px;" id ="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="order-mark-update" value="Mark" />

                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control" style="width: 100%"  maxlength="120" placeholder="maximum length 120 characters" id="mark_description" name="order_mark_description" rows="3" cols="45"></textarea>
                            </div>

                        </div>


                        <button type="button" style="float: right" class="btn btn-xs btn-warning button refresh-order-history" id ="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="refresh-order-history">Refresh Order History</button>
                        <input type="button" class="btn btn-xs  btn-info button customerSentEmailCount" style="float: right;margin-right:5px" customerEmail ="<?php echo $aInfomation["BillingInfo"]->user_email; ?>" value="Does customer have not resolved emails ?">

                        <div class="order_mark_list" >
                            <?php
                            if ($aInfomation["OrderMarkHistory"]) {
                                foreach ($aInfomation["OrderMarkHistory"] as $k => $order_mark) {


                                    if($order_mark->published=="Y"){
                                        $published_mark="N";
                                        $btn_class="label-primary";
                                        $icon_image='<img  src="'.$sImagePath.'success.png">';
                                    }else{
                                        $published_mark="Y";
                                        $btn_class="label-default";
                                        $icon_image='<img   src="'.$sImagePath.'remove.png">';
                                    }

                                    if($my->gid ==25 || in_array($my->username,['natalie@bloomex.ca','Casey@bloomex.com.au'])){
                                        $mark_onclick = "action_mark(".$order_mark->id.",".$aInfomation["OrderInfo"]->order_id.")";
                                    }else{
                                        $mark_onclick ='';
                                    }

                                    $mark_comment = ( $order_mark->description && $order_mark->order_mark_name) ? "<span class='marking_tooltrip'>" . $order_mark->description ."<span class='tooltrip_autor'>".$order_mark->user_name."<br>". $order_mark->date_added."</span></span>" : '';
                                    echo "<span style='border: none;margin:2px 5px 0px 0px;' class='label $btn_class marking_name' id='mark_".$order_mark->id."' publish='".$published_mark."' onclick=".$mark_onclick.">" . $order_mark->order_mark_name . $mark_comment . "</span>";
                                }
                            }
                            ?>
                        </div>

                        <div id ="refreshOrderHistory">
                            <table width="100%" class="adminform">
                                <tr>
                                    <th width="20%" style="text-align:left;">Date Added</th>
                                    <th width="10%">C/N</th>
                                    <th width="10%">W/N</th>
                                    <th width="10%">N/R</th>
                                    <th width="10%">F/D</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">User name</th>
                                    <th width="40%">Comment</th>
                                </tr>
                                <?php

                                global $mosConfig_groups_can_see_hidden_comments;
                                $query= "SELECT id from tbl_mix_user_group  WHERE user_id = ". $my->id." AND user_group_id in (".implode(',',$mosConfig_groups_can_see_hidden_comments).")";
                                $database->setQuery($query);
                                $checkUserCanSeeHiddenComments	= $database->loadResult();

                                foreach ($aInfomation["OrderHistoryInfo"] as $item) {
                                    $images = '';
                                    if($item->images){
                                        foreach($item->images as $img_history){
                                            if(strpos($img_history->image_link, "bloomex.com.au") !== false || strpos($img_history->image_link, "bloomex.ca") !== false) {
                                                $img_history->image_link = HTML_AjaxOrder::imageToBase64($img_history->image_link);
                                                $images .= "<img  class='history_attached_image' history_id=" . $img_history->history_id . " style='width:60px;padding: 10px;cursor: pointer;' src='" . $img_history->image_link . "'/>";
                                            } else {
                                                $img_history->thumb_link = HTML_AjaxOrder::imageFromS3($img_history->thumb_link);
                                                $img_history->image_link = HTML_AjaxOrder::imageFromS3($img_history->image_link);
                                                $thumbExists = HTML_AjaxOrder::checkImageExists($img_history->thumb_link);
                                                $imageExists = HTML_AjaxOrder::checkImageExists($img_history->image_link);

                                                if (!$thumbExists || !$imageExists) {
                                                    $img_history->thumb_link = 'https://blx-public-resouces.s3.us-west-2.amazonaws.com/icons/file-unavailable-thumb.png';
                                                    $img_history->image_link = 'https://blx-public-resouces.s3.us-west-2.amazonaws.com/icons/file-unavailable.png';
                                                }
                                                $images .= "<img  class='history_image_original_" . $img_history->id . "' style='display:none' src='" . $img_history->image_link . "'/>";
                                                $images .= "<img  class='history_attached_image history_image_id_" . $img_history->id . "' history_id=" . $img_history->id . " style='width:60px;padding: 10px;cursor: pointer;' src='" . $img_history->image_link . "'/>";
                                            }
                                        }
                                    }
                                    $videos = '';
                                    if ($item->videos) {
                                        foreach ($item->videos as $key => $video_history) {
                                            $index = $key + 1;
                                            $videos .= "<button class='history_attached_video' history_id=" . $video_history->history_id . " video_link='" . HTML_AjaxOrder::imageFromS3($video_history->video_link) . "' style='margin: 5px;'>Video " . $index . "</button>";
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td style="white-space:nowrap"><?php echo $item->date_added; ?></td>
                                        <td style="text-align:center;"><img src="<?php echo ( intval($item->customer_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                                        <td style="text-align:center;"><img src="<?php echo ( intval($item->warehouse_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                                        <td style="text-align:center;"><img src="<?php echo ( intval($item->recipient_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                                        <td style="text-align:center;"><img src="<?php echo ( intval($item->security_notified) > 0 ) ? $sImagePath . "tick.png" : $sImagePath . "publish_x.png"; ?>"/></td>
                                        <td style="text-align:center;"><strong><?php echo $item->order_status_name; ?></strong></td>
                                        <td style="text-align:center;"><strong><?php echo $item->user_name; ?></strong></td>
                                        <td style="text-align:left;word-break: break-word;">
                                        <?php if($item->order_status_code == 'hc' && !$checkUserCanSeeHiddenComments){
                                            echo '<span style="color:red">You are not allowed to see this comment </span>';
                                        } else {
                                             echo ( $item->comments != "" ) ? $item->comments : "";
                                            echo ( $item->images) ? "<div class='view_images'>View Attached Image(s)</div>" : ""; ?>
                                            <div style="display:none;" class="history_<?php echo $item->order_status_history_id; ?>"><?php echo $images; ?></div>
                                            <?php echo ( $item->videos) ? "<div class='view_videos'>View Attached Video(s)</div>" : "";  ?>
                                            <div style="display:none;" class="history_video_<?php echo $item->order_status_history_id; ?>"><?php echo $videos; ?></div>
                                        <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                          <div id="myModal" class="modal">
                        <span class="close_modal">&times;</span>
                        <img class="modal-content" id="img01">
                        <div style="text-align: center;"  class="mini_images"></div>
                        <div style="text-align: center;" class="mini_videos"></div>
                        </div>
                    </div>
                    <?php if ($my->prevs->warehouse_only == false) { ?>
                        <div id="editOrder" class="hiddencontent">
                            <div id ="updateOrderReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                            <table width="100%" class="adminform" border="1">
                                <tr>
                                    <td colspan="3">
                                        <div id="loadOrderCartDetailReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                                        <div id="loadOrderCartDetail">
                                            <table width="100%" class="adminform" border="1">
                                                <tr>
                                                    <th width="40">#</th>
                                                    <th width="15%" style="text-align:left;">SKU</th>
                                                    <th width="60%" style="text-align:left;">Product Name</th>
                                                    <th width="10%">Quantity</th>
                                                    <th width="10%" colspan="2">Actions</th>
                                                </tr>
                                                <?php
                                                $i = 0;
                                                $nSubTotal = 0;
                                                $nTaxTotal = 0;
                                                foreach ($aInfomation["OrderItem"] as $Item) {
                                                    $i++;
                                                    $nSubTotal += $Item->product_final_price * $Item->product_quantity;


                                                    $nTaxTotal += ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
                                                    ?>
                                                    <tr>
                                                        <td style="text-align:center;vertical-align:top;"><?php echo $i; ?></td>
                                                        <td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku;
                                                    ?></td>
                                                        <td style="padding-right:10px;"><?php echo $Item->order_item_name; ?></td>
                                                        <td style="text-align:center;vertical-align:top;">
                                                            <input type="number" min="1" onkeyup="if(this.value<0){this.value= this.value * -1} else if(this.value==0){ this.value=1}" name="order_item_quantity<?php echo $Item->order_item_id; ?>" value="<?php echo $Item->product_quantity; ?>" size="5" maxlength="3"  />
                                                        </td>
                                                        <td style="text-align:center;vertical-align:top;">
                                                            <input title="Update Quantity" src="<?php echo $mosConfig_live_site ?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Quantity" type="image" border="0" class="update-quantity" id="<?php echo $Item->order_item_id . "[----]" . $aInfomation["OrderInfo"]->order_id . "[----]" . $aInfomation["ShippingInfo"]->country . "[----]" . $aInfomation["ShippingInfo"]->country; ?>" onclick="return false;"/>
                                                        </td>
                                                        <td style="text-align:center;vertical-align:top;">
                                                            <input title="Delete Product Item" <?php echo (count($aInfomation["OrderItem"])==1)?'disabled':''; ?>  src="<?php echo $mosConfig_live_site ?>/components/com_virtuemart/shop_image/ps_image/delete_f2.gif" alt="Delete Product Item" type="image" border="0" class="delete-order-item" id="<?php echo $Item->order_item_id . "[----]" . $aInfomation["OrderInfo"]->order_id . "[----]" . $aInfomation["ShippingInfo"]->country . "[----]" . $aInfomation["ShippingInfo"]->country; ?>" onclick="return false;"/>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="background-color:#CBDCED;" class="title2" width="90%">Delivery Fee:<?php echo $aList['OrderStandingShpping']; ?></td>
                                    <td  style="background-color:#CBDCED;" width="10%">
                                        <input title="Update Standard Shipping" src="<?php echo $mosConfig_live_site ?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Standard Shipping" type="image" border="0" class="update-standard-shipping" id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>"  onclick="return false;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <table width="100%" class="adminform" border="1">
                                            <tr>
                                                <th colspan="4">Add Product</th>
                                            </tr>
                                            <tr>
                                                <th width="80%" style="text-align:left;">Product Name</th>
                                                <th width="10%">Size</th>
                                                <th width="10%">Quantity</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                            <tr>
                                            <script type="text/javascript">
                                                var searchInput = $('#searchInput');
                                                var addProductId = $('#add_product_id');
                                                searchInput.on('input', function () {
                                                    var sku = searchInput.val();
                                                    if (sku.length > 1) {
                                                        $.post("index2.php",
                                                            {
                                                                option: "com_ajaxorder",
                                                                task: "getProductSizePrice",
                                                                product_sku: sku
                                                            },
                                                            function (data) {
                                                                data = JSON.parse(data);
                                                                var divSelect = document.getElementById('bouquet_size');
                                                                var selectElementBouquet = document.querySelector('select[name="select_bouquet"]');

                                                                if (selectElementBouquet) {
                                                                    selectElementBouquet.parentNode.removeChild(selectElementBouquet);
                                                                }

                                                                var selectElement = document.createElement("select");
                                                                selectElement.name = 'select_bouquet';
                                                                if (data.product_size) {
                                                                    var option1 = document.createElement("option");
                                                                    option1.value = "standard";
                                                                    option1.text = "Regular";
                                                                    selectElement.appendChild(option1);

                                                                    if (data.deluxe !== 0) {
                                                                        var option2 = document.createElement("option");
                                                                        option2.value = "deluxe";
                                                                        option2.text = "Deluxe";
                                                                        selectElement.appendChild(option2);
                                                                    }
                                                                    if (data.supersize !== 0) {
                                                                        var option3 = document.createElement("option");
                                                                        option3.value = "supersize";
                                                                        option3.text = "Supersize";
                                                                        selectElement.appendChild(option3);
                                                                    }
                                                                    selectElement.disabled = false;
                                                                } else {
                                                                    selectElement.disabled = true;
                                                                }
                                                                divSelect.appendChild(selectElement);
                                                                var q = new RegExp(searchInput.val(), 'ig');
                                                                var field = addProductId.find('option');
                                                                addProductId.find('option:selected').removeAttr('selected');
                                                                for (var i = 0, l = field.length; i < l; i += 1) {
                                                                    if ($(field[i]).text().match(q)) {
                                                                        addProductId.val($(field[i]).val());
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        );
                                                    }
                                                });

                                            </script>
                                            <td style="vertical-align:top;">
                                                Search: <input class="type-search" id="searchInput" placeholder="Search SKU" type="text" />
                                                <br/><br/>
                                                <?php echo $aList['cboProduct']; ?></td>
                                                <td style="text-align:center;vertical-align:top;">
                                                    <div id="bouquet_size"></div>
                                                </td>

                                            <td style="text-align:center;vertical-align:top;">
                                                <input type="number" name="add_order_item_quantity"  onkeyup="if(this.value<0){this.value= this.value * -1} else if(this.value==0){ this.value=1}" value="1" min="1" size="5" maxlength="3"  />
                                            </td>
                                            <td style="text-align:center;">
                                                <input title="Add Product" src="<?php echo $mosConfig_live_site ?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Add Product" type="image" border="0" class="add-product" id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;" />
                                            </td>
                                </tr>
                            </table>
                    </td>
                </tr>
            </table>
            </div>
        <?php } ?>
        <div id="editCardMessage" class="hiddencontent">
            <table width="100%" class="adminform">
                <tr>
                    <th colspan="4">Edit Card Message</th>
                </tr>
                <tr>
                    <td><strong>Card Message:</strong></td>
                <span style="display:none"> <?Php var_dump($my->prevs->warehouse_only); ?> </span>
                <td><textarea name="order_customer_note" rows="5" cols="30"  <?php if ($my->prevs->warehouse_only != false) echo "disabled" ?>><?php echo $aInfomation["OrderInfo"]->customer_note; ?></textarea></td>
                <td><strong>Signature:</strong></td>
                <td><textarea name="order_customer_signature" rows="5" cols="30"  <?php if ($my->prevs->warehouse_only != false) echo "disabled" ?>><?php echo $aInfomation["OrderInfo"]->customer_signature; ?></textarea></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3">
                        <?php if ($my->prevs->warehouse_only == false) { ?>
                            <div id ="updateCardMessageReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                            <input type="button" class="button update-card-message" id ="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="update-card-message" value="Update Card Messange" />
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="specialInstructionsComments" class="hiddencontent">
            <table width="100%" class="adminform">
                <tr>
                    <td width="50%"><textarea name="order_customer_comments" rows="8" cols="40"  <?php if ($my->prevs->warehouse_only != false) echo "disabled" ?>><?php echo $aInfomation["OrderInfo"]->customer_comments; ?></textarea></td>
                    <td width="50%">
                        <?php if ($my->prevs->warehouse_only == false) { ?>
                            <div id ="updateSpecialInstructionsReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                            <input type="button" class="button update-special-instructions" id ="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="update-special-instructions" value="Update Special Instructions" />
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>

                    <div id="ordercondition" class="hiddencontent">
                        <div id="ordercondition_update_result"></div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" <?php echo ($aInfomation["OrderInfo"]->soft_fraud )?'checked':'';?> name="soft_fraud" order_id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" type="checkbox" value="" id="soft_fraud">
                            <div class="form-check-label" for="soft_fraud">
                                <p class="text-warning">Soft Fraud</p>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" <?php echo ($aInfomation["OrderInfo"]->hard_fraud )?'checked':'';?> order_id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="hard_fraud" type="checkbox" value="" id="hard_fraud">
                            <div class="form-check-label" for="hard_fraud">
                                <p class="text-danger">Hard Fraud</p>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input  checkbox-lg" <?php echo ($aInfomation["OrderInfo"]->inadequate_customer_behavior )?'checked':'';?> order_id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="inadequate_customer_behavior " type="checkbox" value="" id="inadequate_customer_behavior">
                            <div class="form-check-label" for="inadequate_customer_behavior ">
                                <p class="text-info">Inadequate Customer Behavior</p>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" <?php echo ($aInfomation["OrderInfo"]->fair_chargeback_suspecting )?'checked':'';?> order_id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" name="fair_chargeback_suspecting" type="checkbox" value="" id="fair_chargeback_suspecting">
                            <div class="form-check-label" for="fair_chargeback_suspecting">
                                <p class="text-primary">Fair Chargeback Suspecting</p>
                            </div>
                        </div>
                    </div>



                    <div id="partnerOrderHistory" class="hiddencontent">

                        <div style="display:block;clear:both;margin: 10px 0px;">
                            <input type="button" class="button btn btn-primary btn-xs" id="sendproofofdeliverynotification" value="Send Proof Of Delivery Notification To Partner">
                            <span id ="sendproofofdeliverymessage" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></span>

                        </div>
                        <table  width="100%" style="table-layout:fixed; word-wrap: break-word;" class="adminform">
                            <tr>
                                <th width="10%">partner</th>
                                <th width="10%">status</th>
                                <th width="10%">price</th>
                                <th width="10%">comments</th>
                                <th width="10%">time</th>
                            </tr>
                            <?php
                            if($aInfomation["PartnerOrderHistory"] ) {

                                foreach ($aInfomation["PartnerOrderHistory"] as $item) {

                                    ?>
                                    <tr>
                                        <td><?php echo $item->partner_name; ?></td>
                                        <td><?php echo $item->status; ?></td>
                                        <td><?php echo $item->price; ?></td>
                                        <td style="word-break: break-word;"><?php echo $item->comments; ?></td>
                                        <td><?php echo $item->time; ?></td>

                                    </tr>
                                    <?php
                                }

                            }
                            ?>
                        </table>
                    </div>

                    <div id="Other" class="hiddencontent">
                        <div id ="resendconfirmationreport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                        <table width="100%" class="adminform">
                            <tr>
                                <td>
                                    <input type="button" class="button update-status  btn btn-primary btn-xs" id="resendconfirmation" name="Submit" value="Resend Confirmation Email">
                                </td>

                                <td>
                                    <input type="button" class="button  btn btn-info btn-xs" id="resendshippingform" value="Resend Shipping Form">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <script type="text/javascript">
                        jQuery('#sendproofofdeliverynotification').click(function () {
                            $("#sendproofofdeliverymessage").html('<img src="<?php echo (isset($sImgLoading)) ? $sImgLoading : ''; ?>" align="absmiddle"/> Sending');
                            $("#sendproofofdeliverymessage").css("display", "block");
                            $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "SendPODToPartner",
                                    order_id: <?php echo $aInfomation["OrderInfo"]->order_id; ?>
                                },
                                function (data) {
                                    data = JSON.parse(data);
                                    if (data.status == "success") {
                                        $("#sendproofofdeliverymessage").html("Email Sent Succesfully to "+data.email);
                                    } else {
                                        $("#sendproofofdeliverymessage").html("Error sending: "+data.error);
                                    }
                                }
                            );});
                        jQuery('#resendconfirmation').click(function () {
                            $("#resendconfirmationreport").html('<img src="<?php echo (isset($sImgLoading)) ? $sImgLoading : ''; ?>" align="absmiddle"/> Sending');
                            $("#resendconfirmationreport").css("display", "block");
                            $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "SendMailAgain",
                                    order_id: <?php echo $aInfomation["OrderInfo"]->order_id; ?>
                                },
                                function (data) {
                                    data = JSON.parse(data);
                                    if (data.status == "success") {
                                        $("#resendconfirmationreport").html("Confirmation Email Sent Succesfully to "+data.email);
                                    } else {
                                        $("#resendconfirmationreport").html("Error sending: "+data.error);
                                    }
                                }
                            );});
                        jQuery('#resendshippingform').click(function () {
                            $("#resendconfirmationreport").html('<img src="<?php echo (isset($sImgLoading)) ? $sImgLoading : ''; ?>" align="absmiddle"/> Sending');
                            $("#resendconfirmationreport").css("display", "block");
                            $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "SendShippingForm",
                                    order_id: <?php echo $aInfomation["OrderInfo"]->order_id; ?>,
                                    user_id: <?php echo $aInfomation["OrderInfo"]->user_id; ?>,
                                    zip: "<?php echo html_entity_decode($aInfomation["ShippingInfo"]->zip, ENT_QUOTES); ?>",
                                    bill_user_email: "<?php echo html_entity_decode($aInfomation["BillingInfo"]->user_email, ENT_QUOTES); ?>",
                                    bill_user_first_name: "<?php echo html_entity_decode($aInfomation["BillingInfo"]->first_name, ENT_QUOTES); ?>",
                                },
                                function (data) {
                                    data = JSON.parse(data);
                                    if (data.status == "success") {
                                        $("#resendconfirmationreport").html("Email Sent Succesfully to "+data.email);
                                    } else {
                                        $("#resendconfirmationreport").html("Error sending: "+data.error);
                                    }
                                }
                            );});

                    </script>

        </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:0px;border:none;">
                <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <tr>
                        <td width="50%" style="border:none;vertical-align:top;">
                            <table width="100%" class="adminform">
                                <tr>
                                    <th colspan="2">Billing Information</th>
                                <tr>
                                <tr>
                                    <td class="title2">First Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_first_name" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->first_name, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Last Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_last_name" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->last_name, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Company Name:</td>
                                    <td><input type="text" name="bill_company_name" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->company, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Country<font color="red">*</font>:</td>
                                    <td><?php echo $aList['BillingInfoCountry']; ?></td>
                                </tr>
                                <tr>
                                    <td class="title2">State/Province/Region<font color="red">*</font>:</td>
                                    <td><div id="bill_state_container"><?php echo $aInfomation['BillingInfoState']; ?></div></td>
                                </tr>
                                <tr>
                                    <td class="title2">Zip Code / Postal Code<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_zip_code" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->zip, ENT_QUOTES); ?>" size="40" maxlength="10" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">City<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_city" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->city, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td  class="title2"><b>Suite/Apt: </b>
                                    </td>
                                    <td >
                                        <input name="bill_address_suite" size="40" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->suite, ENT_QUOTES); ?>"  type="text"   />
                                    </td>
                                </tr>
                                <tr>
                                    <td  class="title2"><b>Street Number <font color="red">*</font>:</b>
                                    </td>
                                    <td >
                                        <input name="bill_address_street_number" size="40" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->street_number, ENT_QUOTES); ?>"  type="text"  />
                                    </td>
                                </tr>
                                <tr>
                                    <td  class="title2"><b>Street Name <font color="red">*</font>:</b>
                                    </td>
                                    <td>
                                        <input  name="bill_address_street_name" size="40" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->street_name, ENT_QUOTES); ?>"  type="text"  />
                                </tr>
                                <tr>
                                    <td  class="title2"><b>District :</b>
                                    </td>
                                    <td>
                                        <input  name="bill_district" size="40" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->district, ENT_QUOTES); ?>"  type="text"  />
                                </tr>
                                <?php

                                    echo '<tr><td style="text-align: right"> Old Address :</td><td>' . $aInfomation["BillingInfo"]->address_1;
                                    echo '</td></tr>';

                                ?>
                                <tr>
                                    <td class="title2">Phone<font color="red">*</font>:</td>
                                    <td><input type="text" name="bill_phone" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->phone_1, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Evening Phone:</td>
                                    <td><input type="text" name="bill_evening_phone" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->extra_field_1, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <?php if ($my->prevs->warehouse_only == false) { ?>
                                    <tr>
                                        <td class="title2">Email:</td>
                                        <td><input type="text" name="bill_email" value="<?php echo html_entity_decode($aInfomation["BillingInfo"]->user_email, ENT_QUOTES); ?>" size="40" /></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="title2">&nbsp;</td>
                                    <td> <?php if ($my->prevs->warehouse_only == false) { ?>
                                            <div id="update_billing_result" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                                            <input class="button update-billing" id="<?php echo $aInfomation["BillingInfo"]->order_info_id; ?>" name="update_billing" value="Update Billing Info" type="button">
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" style="border:none;vertical-align:top;">
                            <table width="100%" class="adminform">
                                <tr>
                                    <th colspan="2">Shipping Information</th>
                                <tr>
                                <tr>
                                    <td class="title2">Address Type<font color="red">*</font>:</td>
                                    <td>
                                        <select name="address_type2" id="address_type2" size="1" class="inputbox">
                                            <option value="Home/Residence" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "home/residence") echo "selected"; ?>>Home/Residence</option>
                                            <option value="Business" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "business") echo "selected"; ?>>Business</option>
                                            <option value="Funeral Home" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "funeral home") echo "selected"; ?>>Funeral Home</option>
                                            <option value="Hospital" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "hospital") echo "selected"; ?>>Hospital</option>
                                            <option value="School" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "school") echo "selected"; ?>>School</option>
                                            <option value="Place of Worship" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "place of worship") echo "selected"; ?>>Place of Worship</option>
                                            <option value="Hotel" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "hotel") echo "selected"; ?>>Hotel</option>
                                            <option value="Nursing Home" <?php if (strtolower($aInfomation["ShippingInfo"]->address_type2) == "nursing home") echo "selected"; ?>>Nursing Home</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title2">First Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_first_name" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->first_name, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Last Name<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_last_name" value="<?php echo $aInfomation["ShippingInfo"]->last_name; ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Company Name:</td>
                                    <td><input type="text" name="deliver_company_name" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->company, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Country<font color="red">*</font>:</td>
                                    <td><?php echo $aList['ShippingInfoCountry']; ?></td>
                                </tr>
                                <tr>
                                    <td class="title2">State/Province/Region<font color="red">*</font>:</td>
                                    <td><div id="deliver_state_container"><?php echo $aInfomation['ShippingInfoState']; ?></div></td>
                                </tr>
                                <tr>
                                    <td class="title2">Zip Code / Postal Code<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_zip_code" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->zip, ENT_QUOTES); ?>" size="40" maxlength="7" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">City<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_city" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->city, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td  class="title2"><b>Suite/Apt: </b>
                                    </td>
                                    <td >
                                        <input name="deliver_address_suite" size="40" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->suite, ENT_QUOTES); ?>"  type="text"   />

                                    </td>


                                </tr>
                                <tr>
                                    <td  class="title2"><b>Street Number <font color="red">*</font>:</b>
                                    </td>
                                    <td >
                                        <input name="deliver_address_street_number" size="40" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->street_number, ENT_QUOTES); ?>"  type="text"  />

                                    </td>

                                </tr>
                                <tr>
                                    <td  class="title2"><b>Street Name <font color="red">*</font>:</b>
                                    </td>
                                    <td>
                                        <input  name="deliver_address_street_name" size="40" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->street_name, ENT_QUOTES); ?>"  type="text"  />
                                    </td>


                                                <!--<td class="title2">Address 1<font color="red">*</font>:</td>
                                                <td><input type="text" name="deliver_address_1" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->address_1, ENT_QUOTES); ?>" size="40" /></td>
                                                </tr>
                                                <tr>
                                                <td class="title2">Address 2:</td>
                                                <td><input type="text" name="deliver_address_2" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->address_2, ENT_QUOTES); ?>" size="40" /></td>-->
                                </tr>
                                <tr>
                                    <td  class="title2"><b>District:</b>
                                    </td>
                                    <td >
                                        <input name="deliver_district" size="40" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->district, ENT_QUOTES); ?>"  type="text"  />

                                    </td>

                                </tr>
                                <?php

                                    echo '<tr><td style="text-align: right"> Old Address :</td><td>' . ($aInfomation["ShippingInfo"]->address_1) . " " . ($aInfomation["ShippingInfo"]->address_2);
                                    echo '</td></tr>';

                                ?>
                                <tr>
                                    <td class="title2">Phone<font color="red">*</font>:</td>
                                    <td><input type="text" name="deliver_phone" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->phone_1, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <tr>
                                    <td class="title2">Evening Phone:</td>
                                    <td><input type="text" name="deliver_evening_phone" value="<?php echo html_entity_decode(/* $aInfomation["ShippingInfo"]->phone_2 */$aInfomation["ShippingInfo"]->extra_field_1, ENT_QUOTES); ?>" size="40" /></td>
                                </tr>
                                <?php if ($my->prevs->warehouse_only == false) { ?>
                                    <tr>
                                        <td class="title2">Email:</td>
                                        <td><input type="text" name="deliver_email" value="<?php echo html_entity_decode($aInfomation["ShippingInfo"]->user_email, ENT_QUOTES); ?>" size="40" /></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="title2">&nbsp;</td>
                                    <td>
                                        <?php if ($my->prevs->warehouse_only == false) { ?>
                                            <div id="update_deliver_result" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                                            <input class="button update-deliver" id="<?php echo $aInfomation["ShippingInfo"]->order_info_id; ?>" name="update_shipping" value="Update Shipping Info" type="button">
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border:none;">
                <div id="loadOrderItemDetailReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                <div id="loadOrderItemDetail">
                    <table width="100%" class="adminform" border="1">
                        <tr>
                            <th width="20">#</th>
                            <th width="54%" style="text-align:left;">Product Name</th>
                            <th width="7%" style="text-align:left;">SKU</th>
                            <th width="7%">Quantity</th>
                            <th width="10%">Product Price (Net)</th>
                            <th width="10%">Product Price (Gross)</th>
                            <th width="10%">Total</th>
                        </tr>
                        <?php
                        $i = 0;
                        $nSubTotal = 0;
                        $nTaxTotal = 0;


                        foreach ($aInfomation["OrderItem"] as $Item) {

                            $i++;
//                                if($Item->product_final_price == $Item->product_item_price){
//                                    $Item->product_final_price = $Item->product_final_price;
//                                }

                            /*
                              if( $aInfomation["ShopperGroupDiscount"] ) {
                              $Item->product_item_price 	= $Item->product_item_price  - ( $Item->product_item_price * doubleval($aInfomation["ShopperGroupDiscount"]) );
                              $Item->product_final_price 	= $Item->product_final_price  - ( $Item->product_final_price * doubleval($aInfomation["ShopperGroupDiscount"]) );
                              }
                             */
//                                if ($aInfomation["nStateTax"]) {
//                                    $nFinalProduct = $Item->product_item_price + ($Item->product_item_price * $aInfomation["nStateTax"]);
//                                } else {
                            $nFinalProduct = $Item->product_final_price;
//                                }
                            $nSubTotal += $nFinalProduct * $Item->product_quantity;
                            //echo  $nFinalProduct." - ".$Item->product_item_price." = ". ($nFinalProduct - $Item->product_item_price)."<br/><br/>";
                            $nTaxTotal += ( $nFinalProduct - $Item->product_item_price ) * $Item->product_quantity;
                            // $nSubTotal += $nTaxTotal;


                            ?>
                            <tr>
                                <td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
                                <td style="padding-right:10px;">
                                    <strong><?php echo $Item->order_item_name; ?></strong>
                                    <div style="margin: 5px 0px 0px 0px;"><?php echo  $Item->ingredient_list; ?></div>
                                </td>
                                <td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
                                <td style="text-align:center;vertical-align:top;"><strong><?php echo $Item->product_quantity; ?></strong></td>
                                <td style="text-align:center;vertical-align:top;">$<?php echo number_format($Item->product_item_price, 2, ".", ""); ?></td>
                                <td style="text-align:center;vertical-align:top;">$<?php echo number_format($nFinalProduct, 2, ".", ""); ?></td>
                                <td style="text-align:center;vertical-align:top;"><strong>$<?php echo number_format($nFinalProduct * $Item->product_quantity, 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }

                        /* if( $aInfomation["ShopperGroupDiscount"] ) {
                          $nSubTotal = $nSubTotal - ( $nSubTotal * doubleval($aInfomation["ShopperGroupDiscount"]) );
                          } */
                        ?>
                        <tr>
                            <td style="background-color:#CBDCED;" class="title2" colspan="6">SubTotal:</td>
                            <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format($nSubTotal, 2, ".", ""); ?></strong></td>
                        </tr>
                        <?php if ($aInfomation["OrderInfo"]->order_discount > 0) { ?>
<!--                            <tr>-->
<!--                                <td style="background-color:#CBDCED;" class="title2" colspan="6">Discount:</td>-->
<!--                                <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$--><?php //echo number_format($aInfomation["OrderInfo"]->order_discount, 2, ".", ""); ?><!--</strong></td>-->
<!--                            </tr>-->
                            <?php
                        }
                        ?>
                        <?php
                        $coupon_code_string = !empty($aInfomation["OrderInfo"]->coupon_code) ? $aInfomation["OrderInfo"]->coupon_code : "";

                        if ($aInfomation["OrderInfo"]->coupon_discount > 0 && strpos($coupon_code_string, "PC-") === false) {
                            ?>
                            <tr>
                                <td style="background-color:#CBDCED;" class="title2" colspan="6">Coupon Discount:</td>
                                <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format($aInfomation["OrderInfo"]->coupon_discount, 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>

                            <?php
                                if ($my->prevs->warehouse_only && ($aInfomation["OrderInfo"]->order_shipping!=14.95) && ($aInfomation["OrderInfo"]->order_shipping > 0)) {
                                    $aInfomation["OrderInfo"]->order_shipping=14.95;
                                    $aInfomation["OrderInfo"]->order_total = $nSubTotal + $aInfomation["OrderInfo"]->order_shipping;
                                }
                            $sql_pc = "SELECT id FROM tbl_platinum_club WHERE user_id=" . $aInfomation["OrderInfo"]->user_id . " AND `end_datetime` IS NULL";
                            $database->setQuery($sql_pc);
                            $pc_text='';
                            if($database->loadResult()){
                                $pc_text = '(Platinum Club)';
                            }

                            ?>

                        <?php if ($aInfomation["ShopperGroupDiscount"]) { ?>
                            <tr>
                                <td style="background-color:#CBDCED;" class="title2" colspan="6">Corporate Discount:</td>
                                <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format(($aInfomation["ShopperGroupDiscount"]), 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Fee:</td>
                            <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format($aInfomation["OrderInfo"]->order_shipping, 2, ".", "")." ".$pc_text; ?></strong></td>
                        </tr>
                        <?php if ($aInfomation["OrderInfo"]->coupon_discount > 0 && strpos($coupon_code_string, "PC-") !== false) { ?>
                            <tr>
                                <td style="background-color:#CBDCED;" class="title2" colspan="6">Shipping Discount:</td>
                                <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format($aInfomation["OrderInfo"]->coupon_discount, 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>

                        <?php  if ($aInfomation["used_bucks"]) { ?>
                            <tr>
                                <td style="background-color:#CBDCED;" class="title2" colspan="6">Used Bucks:</td>
                                <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format($aInfomation["used_bucks"], 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php  if ($aInfomation["used_credits"]) { ?>
                            <tr>
                                <td style="background-color:#CBDCED;" class="title2" colspan="6">Used Credits:</td>
                                <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format($aInfomation["used_credits"], 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td style="background-color:#CBDCED;" class="title2" colspan="6">Total:</td>
                            <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format(/* $nSubTotal + $nTaxTotal + $aInfomation["OrderInfo"]->order_shipping - $aInfomation["OrderInfo"]->coupon_discount - $aInfomation["OrderInfo"]->order_discount */ $aInfomation["OrderInfo"]->order_total, 2, ".", ""); ?></strong></td>
                        </tr>
                        <?php  if ($aInfomation["donated_price"]) { ?>
                            <tr>
                                <td style="background-color:#CBDCED;text-align: right"  colspan="6">Donated Price (not included into total):</td>
                                <td style="background-color:#CBDCED;"><strong>$<?php echo number_format($aInfomation["donated_price"], 2, ".", ""); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td style="border:none;vertical-align:top;">
                <?php
                $aDeliverInfo = explode("|", $aInfomation["OrderInfo"]->ship_method_id);

                if (!empty($aDeliverInfo[count($aDeliverInfo) - 1]) && $aDeliverInfo[count($aDeliverInfo) - 1] == "Free") {
                    $nDeliveryPrice = "<b>FREE</b>";
                    $nDeliveryTax = "$0.00";
                } else {
                    $nDeliveryPrice = "$" . $aDeliverInfo[3];

                    if (floatval($aInfomation["OrderInfo"]->order_shipping) > floatval($aDeliverInfo[3])) {
                        $nDeliveryTax = "$" . number_format($aInfomation["OrderInfo"]->order_shipping - $aDeliverInfo[3], 2, ".", "");
                    } else {
                        $nDeliveryTax = "$0.00";
                    }
                }
                ?>
                <table width="100%" class="adminform">
                    <tr>
                        <th colspan="2">Deliver Information</th>
                    </tr>
                    <tr>
                        <td width="35%" class="title">Carrier:</td>
                        <td width="65%"><?php echo $aDeliverInfo[1]; ?></td>
                    </tr>

                    <tr>
                        <td class="title">Delivery Price:</td>
                        <td>$<?php echo number_format($aInfomation["OrderInfo"]->order_shipping, 2, ".", ""); ?></td>
                    </tr>

                </table>
            </td>
            <td style="border:none;vertical-align:top;">
                <table width="100%" class="adminform" border="1">
                    <tr>
                        <th width="40%" style="text-align:left;">Payment Method</th>
                        <th width="20%" style="text-align:left;">Account Name</th>
                        <th width="20%">Account Number</th>
                        <th width="20%">Expire Date</th>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;"><?php echo $aInfomation["PaymentInfo"]->payment_method_name; ?></td>
                        <td style="vertical-align:top;"><?php echo $aInfomation["PaymentInfo"]->order_payment_name; ?></td>
                        <td style="text-align:center;vertical-align:top;">
                            <?php
                            $sCC_Result = HTML_AjaxOrder::asterisk_pad($aInfomation["PaymentInfo"]->account_number, 0, true);
                            echo "<b id='CCInfo_" . $aInfomation["OrderInfo"]->order_id . "'>$sCC_Result</b>";
                            //echo HTML_AjaxOrder::asterisk_pad($aInfomation["PaymentInfo"]->account_number, 0, true);
                            if ($aInfomation["PaymentInfo"]->order_payment_trans_id) {
                                echo '<br/>Transaction ID: <b>' . $aInfomation["PaymentInfo"]->order_payment_trans_id . '</b>';
                            }
                            //echo '<br/>(CVV Code: '.$aInfomation["PaymentInfo"]->order_payment_code.')' ;
                            if (strcmp(trim($sCC_Result), "NOT SAVED") != 0) {
                                ?>
                                <br/>
                                <div id="btnRemoveCCInfo">
                                    <input type="button" id="removeCCInfo" value="Remove" class="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" />
                                </div>

                                <div id="msgRemoveCCInfo" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="text-align:center;vertical-align:top;"><?php echo date("M-Y", $aInfomation["PaymentInfo"]->order_payment_expire); ?></td>
                    </tr>
                </table>
                <table width="100%" class="adminform" border="1">
                    <tr>
                        <th style="text-align:left;">Payment Log</th>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">
                            <?php
                            if ($aInfomation["PaymentInfo"]->order_payment_log) {
                                echo $aInfomation["PaymentInfo"]->order_payment_log;
                            } else {
                                echo "./.";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </table><br/><br/>
        <?php
        require_once '../end_access_log.php';
        exit(0);
    }

    function loadOrderItemDetail($option, $aInfomation) {
        ?>
        <table width="100%" class="adminform" border="1">
            <tr>
                <th width="20">#</th>
                <th width="54%" style="text-align:left;">Product Name</th>
                <th width="7%" style="text-align:left;">SKU</th>
                <th width="7%">Quantity</th>
                <th width="10%">Product Price (Net)</th>
                <th width="10%">Product Price (Gross)</th>
                <th width="10%">Total</th>
            </tr>
            <?php
            $i = 0;
            $nSubTotal = 0;
            $nTaxTotal = 0;
            if (count($aInfomation["OrderItem"])) {
                foreach ($aInfomation["OrderItem"] as $Item) {

                    $i++;
                    $nSubTotal += $Item->product_final_price * $Item->product_quantity;
                    $nTaxTotal += ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
                    ?>
                    <tr>
                        <td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
                        <td style="padding-right:10px;">
                            <strong><?php echo $Item->order_item_name; ?></strong>
                            <div style="margin: 5px 0px 0px 0px;"><?php echo html_entity_decode(/* $Item->product_attribute */'', ENT_QUOTES); ?></div>
                        </td>
                        <td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
                        <td style="text-align:center;vertical-align:top;"><strong><?php echo $Item->product_quantity; ?></strong></td>
                        <td style="text-align:center;vertical-align:top;">$<?php echo number_format($Item->product_item_price, 2, ".", ""); ?></td>
                        <td style="text-align:center;vertical-align:top;">$<?php echo number_format($Item->product_final_price, 2, ".", ""); ?></td>
                        <td style="text-align:center;vertical-align:top;"><strong>$<?php echo number_format($Item->product_final_price * $Item->product_quantity, 2, ".", ""); ?></strong></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td style="background-color:#CBDCED;" class="title2" colspan="6">SubTotal:</td>
                <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format($nSubTotal, 2, ".", ""); ?></strong></td>
            </tr>
            <?php if ($aInfomation["OrderInfo"]->order_discount > 0) { ?>
                <tr>
                    <td style="background-color:#CBDCED;" class="title2" colspan="6">Discount:</td>
                    <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format($aInfomation["OrderInfo"]->order_discount, 2, ".", ""); ?></strong></td>
                </tr>
                <?php
            }
            ?>
            <?php if ($aInfomation["OrderInfo"]->coupon_discount > 0) { ?>
                <tr>
                    <td style="background-color:#CBDCED;" class="title2" colspan="6">Coupon Discount:</td>
                    <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format($aInfomation["OrderInfo"]->coupon_discount, 2, ".", ""); ?></strong></td>
                </tr>
                <?php
            }
            ?>
            <?php if ($aInfomation["ShopperGroupDiscount"]) { ?>
                <tr>
                    <td style="background-color:#CBDCED;" class="title2" colspan="6">Corporate Discount:</td>
                    <td style="background-color:#CBDCED;color:#FF3300;"><strong>-$<?php echo number_format(($aInfomation["ShopperGroupDiscount"]), 2, ".", ""); ?></strong></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Fee:</td>
                <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format($aInfomation["OrderInfo"]->order_shipping, 2, ".", ""); ?></strong></td>
            </tr>
            <!--
            <tr>
                <td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Tax:</td>
                <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format($aInfomation["OrderInfo"]->order_shipping_tax, 2, ".", ""); ?></strong></td>
            </tr>
            !-->
            <tr>
                <td style="background-color:#CBDCED;" class="title2" colspan="6">Total:</td>
                <td style="background-color:#CBDCED;color:#FF3300;"><strong>$<?php echo number_format($aInfomation["OrderInfo"]->order_total, 2, ".", ""); ?></strong></td>
            </tr>
        </table>
        [==1==]$<?php echo number_format($aInfomation["OrderInfo"]->order_total, 2, ".", ""); ?>

        <?php
        require_once '../end_access_log.php';
        exit(0);
    }

    function loadOrderCart($option, $aInfomation) {
        global $mosConfig_live_site;
        ?>
        <table width="100%" class="adminform" border="1">
            <tr>
                <th width="40">#</th>
                <th width="15%" style="text-align:left;">SKU</th>
                <th width="60%" style="text-align:left;">Product Name</th>
                <th width="10%">Quantity</th>
                <th width="10%" colspan="2">Actions</th>
            </tr>
            <?php
            $i = 0;
            $nSubTotal = 0;
            $nTaxTotal = 0;
            foreach ($aInfomation["OrderItem"] as $Item) {
                $i++;
                $nSubTotal += $Item->product_final_price * $Item->product_quantity;
                $nTaxTotal += ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
                ?>
                <tr>
                    <td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
                    <td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
                    <td style="padding-right:10px;"><?php echo $Item->order_item_name; ?></td>
                    <td style="text-align:center;vertical-align:top;">
                        <input type="text" name="order_item_quantity<?php echo $Item->order_item_id; ?>" value="<?php echo $Item->product_quantity; ?>" size="5" maxlength="3"  />
                    </td>
                    <td style="text-align:center;vertical-align:top;">
                        <input title="Update Quantity" src="<?php echo $mosConfig_live_site ?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Quantity" type="image" border="0" class="update-quantity2" id="<?php echo $Item->order_item_id; ?>[----]<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;"/>
                    </td>
                    <td style="text-align:center;vertical-align:top;">
                        <input title="Delete Product Item" <?php echo (count($aInfomation["OrderItem"])==1)?'disabled':''; ?>  src="<?php echo $mosConfig_live_site ?>/components/com_virtuemart/shop_image/ps_image/delete_f2.gif" alt="Delete Product Item" type="image" border="0" class="delete-order-item2" id="<?php echo $Item->order_item_id; ?>[----]<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;"/>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
        require_once '../end_access_log.php';
        exit(0);
    }

    function searchOrderForm($option, $aInfomation, $orderID, $msg = "") {
        global $mosConfig_live_site, $mosConfig_absolute_path;
        ?>
        <style>
            div.msg {
                font: bold 11px Tahoma,Verdana;
                padding:10px 0px 10px 0px;
                text-align:center;
                color:#DF0000;
            }

            table.order-header{
                margin:5px 0px 5px 0px;
            }

            table.order-header td {
                background-color:#BE4C34;
                font:bold 12px Tahoma, Verdana;
                text-transform:uppercase;
                text-align:center;
                line-height:160%;
                color:#FFF;
            }

            table.adminform th{
                font:bold 12px Tahoma, Verdana;
                text-align:center;
            }

            table.adminform {
                margin:0px 0px 10px 0px;
            }

            table.adminform td{
                font:normal 11px Tahoma, Verdana;
                border-right:1px solid #D5D5D5;
                border-bottom:1px solid #D5D5D5;
                line-height:140%;
                padding:5px;
            }

            table.adminform td.title{
                font:bold 11px Tahoma, Verdana;
                line-height:140%;
            }

            table.adminform td.title2{
                font:bold 11px Tahoma, Verdana;
                text-align:right;
                padding-right:8px;
                line-height:140%;
            }

            input.button {
                cursor:pointer;
            }

        </style>
        <script type="text/javascript">
            function searchOrder() {
                if (document.adminForm.order_id.value == "") {
                    alert("Please enter your order id first.")
                    return;
                }
                /*document.adminForm.ingredient_list.value = "";*/
                document.adminForm.submit();
            }

            function submitOrder(order_status) {
                document.adminForm.change_order_status.value = order_status;
                document.adminForm.submit();
            }
        </script>

        <form action="index2.php?option=com_ajaxorder&task=searchOrderForm" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th colspan="2">
                        Produce Order
                    </th>
                </tr>
                <tr>
                    <td width="10%">&nbsp;</td>
                    <td width="90%" align="left">
                        Order ID (ex. bloom-ORDER_ID):
                        <input type="text" name="order_id" value="" size="30" />
                        <input type="button" name="Search" value="Search" onclick="searchOrder();"/>

                        <script type="text/javascript">
                            document.adminForm.order_id.focus();
                        </script>
                    </td>
                </tr>
            </table>
            <br/>
            <br/>
            <table width="100%" class="adminform" border="1">
                <tr>
                    <th align="left" style="text-align:left;padding:10px 10px 10px 15px;">Order Detail Informartion</th>
                </tr>
                <tr>
                    <td>
                        <?php
                        if ($msg)
                            echo "<div class='msg'>$msg</div>";

                        if ($orderID == "") {
                            echo "No Data";
                        } elseif (isset($aInfomation["OrderInfo"]->order_id) && $aInfomation["OrderInfo"]->order_id > 0) {
                            ?>

                            <table width="100%" class="adminform">
                                <tr>
                                    <td width="15%" class="title" style='font-size:24px'>Order ID:</td>
                                    <td width="85%" style='font-size:24px'><?php echo sprintf("%08d", $aInfomation["OrderInfo"]->order_id); ?></td>
                                </tr>
                                <tr>
                                    <td class="title" style='font-size:24px'>Order Status:</td>
                                    <td style='font-size:24px'><?php echo $aInfomation["OrderStatus"]; ?></td>
                                </tr>
                            </table>
                            <table width="100%" class="adminform" border="1">
                                <tr>
                                    <th width="30">#</th>
                                    <th width="120" style="text-align:left;">Image</th>
                                    <th width="100" style="text-align:center;">Quantity</th>
                                    <th style="text-align:left;">Ingredient List</th>
                                </tr>
                                <?php
                                $i = 0;
                                foreach ($aInfomation["OrderItem"] as $Item) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
                                        <td style="text-align:left;vertical-align:top;">
                                            <strong style='font-size:24px'><?php echo $Item->order_item_name; ?></strong><br/><br/>
                                            <?php
                                            if (is_file($mosConfig_absolute_path . "/components/com_virtuemart/shop_image/product/" . $Item->product_full_image)) {
                                                $sImage = $mosConfig_live_site . "/components/com_virtuemart/shop_image/product/" . $Item->product_full_image;
                                            } else {
                                                $sImage = $mosConfig_live_site . "/components/com_virtuemart/shop_image/product/nophoto.jpg";
                                            }
                                            ?>
                                            <div align="center"style="border:1px solid #703E9D;">
                                                <img src="<?php echo $sImage ?>"/>
                                            </div>
                                        </td>
                                        <td style="text-align:center;vertical-align:top;"><strong><?php echo $Item->product_quantity; ?></strong></td>
                                        <td style="padding-right:10px;vertical-align:top;font-size:24px;"><?php echo str_replace("\n", "<br/>", stripslashes($Item->ingredient_list)); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="4" style="padding:20px 0px 20px 20px;">
                                        <div id="displaySubstitution" style="display:none;">
                                            <b id="displaySubstitutionTitle" style="padding:0px 0px 5px 0px;display:block;">&nbsp;</b>
                                            <textarea name="comment" rows="10" cols="70"></textarea><br/>
                                            <input type="button" value="Submit Substitution" onclick="submitOrder(document.adminForm.change_order_status.value);" style="font:bold 11px Tahoma,Verdana;color:#FF0000;padding:5px 10px 5px 10px;cursor:pointer;margin:5px 0px 0px 0px;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding:20px 0px 20px 20px;">
                                        <input type="button" value="Order Completed" onclick="submitOrder('E');" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" value="Order Completed With Minor Substitution" onclick="displaySubstitution('M');" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <!--<input type="button" value="Order Completed With Major Substitution" onclick="displaySubstitution('i');" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" />-->
                                    </td>
                                </tr>
                            </table>

                            <script type="text/javascript">
                                function displaySubstitution(status) {
                                    document.getElementById('displaySubstitution').style.display = "block";
                                    if (status == "M") {
                                        document.getElementById('displaySubstitutionTitle').innerHTML = "What items substituted and with what product?";
                                        document.adminForm.change_order_status.value = "M";
                                    } else {
                                        document.getElementById('displaySubstitutionTitle').innerHTML = "What has caused the major substitution?";
                                        document.adminForm.change_order_status.value = "i";
                                    }
                                }
                            </script>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <br/><br/><br/><br/><br/><br/><br/><br/>

            <input type="hidden" name="option" value="com_ajaxorder" />
            <input type="hidden" name="task" value="searchOrderForm" />
            <input type="hidden" name="order_id_research" value="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" />
            <input type="hidden" name="change_order_status" value="" />
        </form>
        <?php
    }

    function packageOrder($option, $aInfomation, $orderID, $msg = "") {
        global $mosConfig_live_site, $mosConfig_absolute_path;
        ?>
        <style>
            div.msg {
                font: bold 11px Tahoma,Verdana;
                padding:10px 0px 10px 0px;
                text-align:center;
                color:#DF0000;
            }

            table.order-header{
                margin:5px 0px 5px 0px;
            }

            table.order-header td {
                background-color:#BE4C34;
                font:bold 12px Tahoma, Verdana;
                text-transform:uppercase;
                text-align:center;
                line-height:160%;
                color:#FFF;
            }

            table.adminform th{
                font:bold 12px Tahoma, Verdana;
                text-align:center;
            }

            table.adminform {
                margin:0px 0px 10px 0px;
            }

            table.adminform td{
                font:normal 11px Tahoma, Verdana;
                border-right:1px solid #D5D5D5;
                border-bottom:1px solid #D5D5D5;
                line-height:140%;
                padding:5px;
            }

            table.adminform td.title{
                font:bold 11px Tahoma, Verdana;
                line-height:140%;
            }

            table.adminform td.title2{
                font:bold 11px Tahoma, Verdana;
                text-align:right;
                padding-right:8px;
                line-height:140%;
            }

            input.button {
                cursor:pointer;
            }
        </style>
        <script type="text/javascript">
            function searchOrder() {
                if (document.adminForm.order_id.value == "") {
                    alert("Please enter your order id first.")
                    return;
                }

                /*document.adminForm.ingredient_list.value = "";*/
                document.adminForm.submit();
            }

            function submitOrder(order_status) {
                document.adminForm.change_order_status.value = order_status;
                document.adminForm.submit();
            }
        </script>

        <form action="index2.php?option=com_ajaxorder&task=packageOrder" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th colspan="2">
                        Package Order
                    </th>
                </tr>
                <tr>
                    <td width="10%">&nbsp;</td>
                    <td width="90%" align="left">
                        Order ID (bloom-ORDER_ID):
                        <input type="text" name="order_id" value="" size="30" />
                        <input type="button" name="Search" value="Search" onclick="searchOrder();"/>

                        <script type="text/javascript">
                            document.adminForm.order_id.focus();
                        </script>
                    </td>
                </tr>
            </table>
            <br/>
            <br/>
            <table width="100%" class="adminform" border="1">
                <tr>
                    <th align="left" style="text-align:left;padding:10px 10px 10px 15px;">Order Detail Informartion</th>
                </tr>
                <tr>
                    <td>
                        <?php
                        if ($msg)
                            echo "<div class='msg'>$msg</div>";

                        if ($orderID == "") {
                            echo "No Data";
                        } elseif (isset($aInfomation["OrderInfo"]->order_id) && $aInfomation["OrderInfo"]->order_id > 0) {
                            ?>
                            <table width="100%" class="adminform">
                                <tr>
                                    <td width="15%" class="title" style='font-size:24px'>Order ID:</td>
                                    <td width="85%" style='font-size:24px'><?php echo sprintf("%08d", $aInfomation["OrderInfo"]->order_id); ?></td>
                                </tr>
                                <tr>
                                    <td class="title" style='font-size:24px'>Order Status:</td>
                                    <td style='font-size:24px'><?php echo $aInfomation["OrderStatus"]; ?></td>
                                </tr>
                            </table>
                            <table width="100%" class="adminform" border="1">
                                <tr>
                                    <td colspan="4">
                                        <div style="display:block;font-size:12px;"><b>The subsituation message:</b></div>
                                        <div style="display:block;font-size:14px;"><?php echo $aInfomation["SubsituationMessage"]; ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="30">#</th>
                                    <th width="120" style="text-align:left;">Image</th>
                                    <th width="100" style="text-align:center;">Quantity</th>
                                    <th style="text-align:left;">Ingredient List</th>
                                </tr>
                                <?php
                                $i = 0;
                                foreach ($aInfomation["OrderItem"] as $Item) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
                                        <td style="text-align:left;vertical-align:top;">
                                            <strong style='font-size:24px'><?php echo $Item->order_item_name; ?></strong><br/><br/>
                                            <?php
                                            if (is_file($mosConfig_absolute_path . "/components/com_virtuemart/shop_image/product/" . $Item->product_full_image)) {
                                                $sImage = $mosConfig_live_site . "/components/com_virtuemart/shop_image/product/" . $Item->product_full_image;
                                            } else {
                                                $sImage = $mosConfig_live_site . "/components/com_virtuemart/shop_image/product/nophoto.jpg";
                                            }
                                            ?>
                                            <div align="center"style="border:1px solid #703E9D;">
                                                <img src="<?php echo $sImage ?>"/>
                                            </div>
                                        </td>
                                        <td style="text-align:center;vertical-align:top;"><strong><?php echo $Item->product_quantity; ?></strong></td>
                                        <td style="padding-right:10px;vertical-align:top;font-size:24px;"><?php echo str_replace("\n", "<br/>", stripslashes($Item->ingredient_list)); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="4" style="padding:20px 0px 20px 20px;">
                                        <input type="button" value="Order passed QC and packaged" onclick="submitOrder('Q');" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" value="Order sent back to designer for review" onclick="submitOrder('C');" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" />
                                    </td>
                                </tr>
                            </table>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <br/><br/><br/><br/><br/><br/><br/><br/>

            <input type="hidden" name="option" value="com_ajaxorder" />
            <input type="hidden" name="task" value="packageOrder" />
            <input type="hidden" name="order_id_research" value="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" />
            <input type="hidden" name="change_order_status" value="" />
        </form>
        <?php
    }

    function packagingDelivery($option, $msg) {
        global $mosConfig_live_site, $mosConfig_absolute_path;
        ?>
        <style>
            div.msg {
                font: bold 11px Tahoma,Verdana;
                padding:10px 0px 10px 0px;
                text-align:center;
                color:#DF0000;
            }

            table.order-header{
                margin:5px 0px 5px 0px;
            }

            table.order-header td {
                background-color:#BE4C34;
                font:bold 12px Tahoma, Verdana;
                text-transform:uppercase;
                text-align:center;
                line-height:160%;
                color:#FFF;
            }

            table.adminform th{
                font:bold 12px Tahoma, Verdana;
                text-align:center;
            }

            table.adminform {
                margin:0px 0px 10px 0px;
            }

            table.adminform td{
                font:normal 11px Tahoma, Verdana;
                border-right:1px solid #D5D5D5;
                border-bottom:1px solid #D5D5D5;
                line-height:140%;
                padding:5px;
            }

            table.adminform td.title{
                font:bold 11px Tahoma, Verdana;
                line-height:140%;
            }

            table.adminform td.title2{
                font:bold 11px Tahoma, Verdana;
                text-align:right;
                padding-right:8px;
                line-height:140%;
            }

            input.button {
                cursor:pointer;
            }
        </style>
        <script type="text/javascript">
            function searchOrder() {
                if (document.adminForm.order_id.value == "") {
                    alert("Please enter your order id first.")
                    return;
                }

                /*document.adminForm.ingredient_list.value = "";*/
                document.adminForm.submit();
            }
        </script>

        <form action="index2.php?option=com_ajaxorder&task=packagingDelivery" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th colspan="2">
                        Packaging Delivery
                    </th>
                </tr>
                <tr>
                    <td width="10%">&nbsp;</td>
                    <td width="90%" align="left">
                        Order ID (bloom-ORDER_ID):
                        <input type="text" name="order_id" value="" size="30" />
                        <input type="button" name="Search" value="Search" onclick="searchOrder();"/>

                        <script type="text/javascript">
                            document.adminForm.order_id.focus();
                        </script>
                    </td>
                </tr>
            </table>
            <br/>
            <br/>
            <table width="100%" class="adminform" border="1">
                <tr>
                    <th align="left" style="text-align:left;padding:10px 10px 10px 15px;">Packaging Delivery Informartion</th>
                </tr>
                <tr>
                    <td>
                        <?php
                        if ($msg) {
                            echo "<div class='msg'>$msg</div>";
                        } else {
                            echo "No Data";
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <br/><br/><br/><br/><br/><br/><br/><br/>

            <input type="hidden" name="option" value="com_ajaxorder" />
            <input type="hidden" name="task" value="packagingDelivery" />
        </form>
        <?php
    }

    function shipOrder($option, $aInfomation, $sOrderListID, $aList, $sOrderSuccessful_data = "", $aDriversOptions, $construct_body_map_data, $html3) {
        global $mosConfig_live_site, $mosConfig_absolute_path, $database;
        $VM_LANG = new vmLanguage();
        ?>
        <style>
            div.msg {
                font: bold 11px Tahoma,Verdana;
                padding:10px 0px 10px 0px;
                text-align:center;
                color:#DF0000;
            }

            div.msg2 {
                font: bold 11px Tahoma,Verdana;
                float:left;
                padding:10px 0px 10px 30px;
                text-align:center;
                color:#DF0000;
            }

            table.order-header{
                margin:5px 0px 5px 0px;
            }

            table.order-header td {
                background-color:#BE4C34;
                font:bold 12px Tahoma, Verdana;
                text-transform:uppercase;
                text-align:center;
                line-height:160%;
                color:#FFF;
            }

            table.adminform th{
                font:bold 12px Tahoma, Verdana;
                text-align:center;
            }

            table.adminform {
                margin:0px 0px 10px 0px;
            }

            table.adminform td{
                font:normal 11px Tahoma, Verdana;
                border-right:1px solid #D5D5D5;
                border-bottom:1px solid #D5D5D5;
                line-height:140%;
                padding:5px;
            }

            table.adminform td.title{
                font:bold 11px Tahoma, Verdana;
                line-height:140%;
            }

            table.adminform td.title2{
                font:bold 11px Tahoma, Verdana;
                text-align:right;
                padding-right:8px;
                line-height:140%;
            }

            input.button {
                cursor:pointer;
            }
        </style>
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
        <script type="text/javascript" src="https://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
        <script src="https://malsup.github.com/jquery.form.js"></script>-->
        <script type="text/javascript">

                            /*         $(function () {
                             $('input[name="order_id"]').keypress(function (event) {
                             if (event.which == '13') {
                             event.preventDefault();
                             //alert("Enter");
                             }
                             })
                             });
                             */

                            var addresses = '';
                            var addr_line = '';
                            var addr_line_number = '';
                            var img_number = 0;
                            var markers = new Array();

                            var removeID = "";
                            var order_id = "";//$j("#order_id").val();//document.adminForm.order_id.value;
                            var drivers_name_and_telephone_number = "";//$j("#bloomex_driver_drivers_name_and_telephone_number").val();//document.getElementById("bloomex_driver_drivers_name_and_telephone_number").value;
                            var telephone_or_email = "";//$j("#local_driver_telephone_or_email").val();//document.getElementById("local_driver_telephone_or_email").value;
                            var telephone_or_email2 = "";//$j("#courier_telephone_or_email").val();//document.getElementById("courier_telephone_or_email").value;
                            var tracking_number = "";//$j("#courier_tracking_number").val();//document.getElementById("courier_tracking_number").value;
                            var driver_option_type = "";//$j("#driver_option_type").val();//document.adminForm.driver_option_type.value;
                            var warehouse_id = "";//$j("#warehouse_id").val();//document.adminForm.warehouse_id.value;
                            var option = "";
                            var task = "";
                            var order_id_research = "";
                            var removeID = "";
                            var confirm = "";
                            var ajax_post_search = "";
                            function searchOrder() {
                                <?php

                                if (!empty($VM_LANG->_VM_BARCODE_PREFIX)) {
                                    echo "s_VM_BARCODE_PREFIX = '" . $VM_LANG->_VM_BARCODE_PREFIX . "';";
                                } else {
                                    echo "s_VM_BARCODE_PREFIX = '';";
                                }
                                ?>
                                if (document.adminForm.order_id.value == "") {
                                    alert("Please enter your order id first.");
                                    return false;
                                }
                                var driver_option_type = document.adminForm.driver_option_type.value;
                                aOrderIdFormat = document.adminForm.order_id.value.split("-");
                                $.ajax({
                                    type: 'POST',
                                    url: "index2.php",
                                    data: {
                                        option: "com_ajaxorder",
                                        task: "getOrderAssignedDeliveryService",
                                        order_id: document.getElementById('order_id').value
                                    },
                                    dataType: 'json',
                                    async: false,
                                    success: function (data) {
                                        if (data.result == true) {
                                            document.adminForm.warehouse_id.value = data.warehouse_id;
                                            buildSelectBox(data.warehouse_id,driver_option_type);
                                            if(data.driver_option_type!=null) {
                                                document.adminForm.driver_option_type.value = data.driver_option_type;
                                            }
                                            checkOption(document.adminForm.driver_option_type.value);
                                            document.getElementById("courier_tracking_number").value = data.tracking_number
                                            if( data.description) {
                                                document.getElementById("courier_telephone_or_email").value = data.description
                                            }
                                        }
                                    }
                                })

                                if (document.adminForm.warehouse_id.value == "") {
                                    alert("Please select Warehouse first.")
                                    document.adminForm.order_id.value = "";
                                    return false;
                                }

                                if (document.adminForm.driver_option_type.value == "") {
                                    alert("Please choose Driver first.");
                                    document.adminForm.order_id.value = "";
                                    return false;
                                }

                                aSelectedValue = document.adminForm.driver_option_type.value.split(" - ");

                                if (aSelectedValue[0].toLowerCase() == "Bloomex Driver".toLowerCase()) {
                                    if (document.getElementById("bloomex_driver_drivers_name_and_telephone_number").value == "") {
                                        alert("Please enter Drivers first.");
                                        document.adminForm.order_id.value = "";
                                        return false;
                                    }
                                }

                                if (aSelectedValue[0].toLowerCase() == "Local Driver".toLowerCase()) {
                                    if (document.getElementById("local_driver_telephone_or_email").value == "") {
                                        alert("Please enter Telephone or Email first.");
                                        document.adminForm.order_id.value = "";
                                        return false;
                                    }
                                }

                                if (aSelectedValue[0].toLowerCase() == "Courier".toLowerCase()) {
                                    if (document.getElementById("courier_telephone_or_email").value == "") {
                                        alert("Please enter Telephone or Email first.");
                                        return false;
                                    }

                                    if (document.getElementById("courier_tracking_number").value == "") {
                                        alert("Please scan Tracking Number first.");
                                        document.adminForm.order_id.value = "";
                                        return false;
                                    }
                                    document.getElementById("courier_tracking_number").value = TrimString(document.getElementById("courier_tracking_number").value);
                                    if (aSelectedValue[1] == "Purolator" && document.getElementById("courier_tracking_number").value.length > 12) {
                                        alert("Purolater Tracking Code can not exceed 12 characters.");
                                        document.adminForm.order_id.value = "";
                                        return false;
                                    }
                                }
                                preLoad();

                                return false;
                            }

                            function add_string()
                            {
                                var item = document.getElementById('Search');
                                item.disabled = true;
                                if (!markers)
                                    markers = new Array();
                                removeID = document.adminForm.removeID.value;
                                order_id = document.getElementById('order_id').value;
                                drivers_name_and_telephone_number = document.getElementById('bloomex_driver_drivers_name_and_telephone_number').value;
                                telephone_or_email = document.getElementById('local_driver_telephone_or_email').value;
                                telephone_or_email2 = document.getElementById('courier_telephone_or_email').value;
                                tracking_number = document.getElementById('courier_tracking_number').value;
                                driver_option_type = document.getElementById('driver_option_type').value;
                                warehouse_id = document.getElementById('warehouse_id').value;
                                option = 'com_ajaxorder';//document.getElementById('option').value;
                                task = 'shipOrder';//document.getElementById('task').value;
                                order_id_research = document.getElementById('order_id_research').value;
                                removeID = document.getElementById('removeID').value;
                                confirm = "";//document.getElementById('confirm').value;
                                ajax_post_search = document.getElementById('ajax_post_search').value = 'true';
                                $(function () {
                                    $.post("index2.php"/*"/administrator/components/com_ajaxorder/post_map.php"*/, {removeID: removeID, ajax_post_search: ajax_post_search,
                                        option: option,
                                        task: task,
                                        order_id_research: order_id_research,
                                        removeID:removeID,
                                                confirm: confirm,
                                        order_id: order_id,
                                        drivers_name_and_telephone_number: drivers_name_and_telephone_number,
                                        telephone_or_email: telephone_or_email,
                                        telephone_or_email2: telephone_or_email2,
                                        tracking_number: tracking_number,
                                        driver_option_type: driver_option_type,
                                        warehouse_id: warehouse_id},
                                    function (data) {

                                        //document.getElementById('post_result').value = data;
                                        $("#container_address").html($('#container_address', data).html());

                                        // say_operation
                                        $("#say_operation").html($('#say_operation22', data).html());

                                        // say_operation
                                        $("#hidden_address").html($('#address_print22', data).html());

                                        // all orders id
                                        $("#order_id_research").val($('#full_markers', data).html());

                                        // warehouse22
                                        var warehouse22 = $('#warehouse22', data).html();
                                        markers[0].setMap(null);
                                        address = warehouse22;

                                        if (address)
                                        {
                                            geocoder.geocode({'address': address}, function (results, status) {
                                                if (status == google.maps.GeocoderStatus.OK) {
                                                    markers[0] = new google.maps.Marker({
                                                        map: map,
                                                        draggable: true,
                                                        position: results[0].geometry.location,
                                                        title: results[0].formatted_address,
                                                        icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=w|CCCCCC|0000FF'
                                                    });
                                                } else {
                                                    alert("Could not find the address " + address);
                                                }
                                            });
                                        }


                                        var delete_number = $('#delete_number', data).html();
                                        if (!delete_number || delete_number == '')
                                        {
                                            if ($('#insert_number', data).html()) {

                                                var new_data_set = $('#insert_number', data).html().split("[--1--]");
                                                if ($('#insert_number', data) && new_data_set[0] != '')
                                                {

                                                    var count_addr_line_number = addr_line_number.length;
                                                    addr_line_number[count_addr_line_number] = new_data_set[1];
                                                    addr_line[new_data_set[1]] = new_data_set[4];

                                                    address = addr_line[new_data_set[1]];

                                                    //if( address )
                                                    //    {
                                                    geocoder.geocode({'address': address}, function (results, status) {
                                                        if (status == google.maps.GeocoderStatus.OK) {
                                                            markers[new_data_set[1]] = new google.maps.Marker({
                                                                map: map,
                                                                draggable: true,
                                                                position: results[0].geometry.location,
                                                                title: results[0].formatted_address,
                                                                icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + ((img_number == 0) ? 'w|CCCCCC|0000FF' : img_number) + '|FF0000|000000'
                                                            });
                                                            img_number++;
                                                        } else {
                                                            removeShipOrder(new_data_set[1]);
                                                            alert("Could not find the address " + address);
                                                        }
                                                    });
                                                    //   }

                                                }
                                            }
                                        }
                                        else
                                        {
                                            if (markers[delete_number])
                                                markers[delete_number].setMap(null);

                                            document.adminForm.removeID.value = "";

                                            $("#order_id_research").val($('#full_markers', data).html());
                                            var full_markers = new Array();
                                            full_markers = $('#full_markers', data).html().split(",");

                                            var i = 0;
                                            while (full_markers[i])
                                            {

                                                markers[full_markers[i]].setIcon('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + (i + 1) + '|FF0000|000000');
                                                i++;
                                            }
                                            img_number--;
                                            if ($('#address_print22', data).html() == '')
                                                img_number = 1;

                                        }
                                        var item2 = document.getElementById('Search');
                                        item2.disabled = false;

                                    });
                                });
                                document.getElementById('courier_tracking_number').value = '';
                            }

                            function removeShipOrder(removeID) {
                                var item = document.getElementById('Search');
                                if (item.disabled)
                                {
                                    alert('Removal is possible after the sketches of all markers on the map');
                                    return true;
                                }

                                document.adminForm.removeID.value = removeID;
                                add_string();
                            }

                            function preLoad() // 'add', 'remove'
                            {
                                var item = document.getElementById('Search');
                                if (item.disabled)
                                {
                                    alert('Adding addresses possible after the sketches of all markers on the map');
                                    return true;
                                }
                                option = 'com_ajaxorder';//document.getElementById('option').value;
                                task = 'shipOrderAddress';
                                order_id = document.getElementById('order_id').value;
                                $(function () {
                                    $.post("index2.php", {option: option, task: task, order_id: order_id},
                                    function (data) {
                                        if ($('#address', data).html() != '')
                                        {
                                            address = $('#address', data).html();
                                            geocoder.geocode({'address': address}, function (results, status) {
                                                if (status == google.maps.GeocoderStatus.OK) {
                                                    add_string();
                                                    document.getElementById('order_id').value = '';
                                                } else {
                                                    $("#say_operation").html("<p style='color:#FF0000;'>Could not find the address " + address + "</p>");
                                                }
                                            });
                                        }
                                        else {
                                            $("#say_operation").html("<p style='color:#FF0000;'>bad " + order_id + "</p>");
                                        }

                                    });
                                });
                            }



                            function submitOrder(confirm_value) {

                                if (document.adminForm.warehouse_id.value == "") {
                                    alert("Please select Warehouse first.")
                                    document.adminForm.order_id.value = "";
                                    return;
                                }

                                if (document.adminForm.driver_option_type.value == "") {
                                    alert("Please choose Driver first.")
                                    document.adminForm.order_id.value = "";
                                    return;
                                }

                                aSelectedValue = document.adminForm.driver_option_type.value.split(" - ");

                                if (aSelectedValue[0].toLowerCase() == "Bloomex Driver".toLowerCase()) {
                                    if (document.getElementById("bloomex_driver_drivers_name_and_telephone_number").value == "") {
                                        alert("Please enter Drivers first.")
                                        document.adminForm.order_id.value = "";
                                        return;
                                    }
                                }

                                if (aSelectedValue[0].toLowerCase() == "Local Driver".toLowerCase()) {
                                    if (document.getElementById("local_driver_telephone_or_email").value == "") {
                                        alert("Please enter Telephone or Email first.")
                                        document.adminForm.order_id.value = "";
                                        return;
                                    }
                                }

                                if (aSelectedValue[0].toLowerCase() == "Courier".toLowerCase()) {
                                    if (document.getElementById("courier_telephone_or_email").value == "") {
                                        alert("Please enter Telephone or Email first.")
                                        return;
                                    }
                                }

                                //document.getElementById('ajax_post_search').value = 'true';
                                document.adminForm.confirm.value = confirm_value;
                                document.adminForm.submit();
                            }

                            sDriverOptionType = "<?php echo $aList['service_name']; ?>";

                            function checkOption(selectedValue) {
                                aSelectedValue = selectedValue.split(" - ");
                                sPrefix = "";

                                if (aSelectedValue[0].toLowerCase() == "Bloomex Driver".toLowerCase() && selectedValue) {
                                    document.getElementById("bloomex_driver").style.display = "block";
                                    sPrefix = "bloomex_driver";
                                } else {
                                    document.getElementById("bloomex_driver").style.display = "none";
                                    document.getElementById("bloomex_driver_drivers_name_and_telephone_number").value = "";
                                }

                                if (aSelectedValue[0].toLowerCase() == "Local Driver".toLowerCase() && selectedValue) {
                                    document.getElementById("local_driver").style.display = "block";
                                    sPrefix = "local_driver";
                                } else {
                                    document.getElementById("local_driver").style.display = "none";
                                    document.getElementById("local_driver_telephone_or_email").value = "";
                                }

                                if (aSelectedValue[0].toLowerCase() == "Courier".toLowerCase() && selectedValue) {
                                    document.getElementById("courier").style.display = "block";
                                    sPrefix = "courier";

                                    cutPIN();
                                } else {
                                    document.getElementById("courier").style.display = "none";
                                    document.getElementById("courier_telephone_or_email").value = "";
                                    document.getElementById("courier_tracking_number").value = "";
                                }

                                if (selectedValue) {
                                    aDriverOptionType = sDriverOptionType.split("[--2--]");
                                    for (i = 0; i < aDriverOptionType.length; i++) {
                                        if (aDriverOptionType[i]) {
                                            aDriverOptionTypeItem = aDriverOptionType[i].split("[--1--]");

                                            if (aDriverOptionTypeItem[1] == selectedValue && document.getElementById(sPrefix + "_" + aDriverOptionTypeItem[2])) {
                                                document.getElementById(sPrefix + "_" + aDriverOptionTypeItem[2]).value = aDriverOptionTypeItem[3];

                                            }
                                        }
                                    }
                                }
                            }

                            function buildSelectBox(warehouse_id, selectedValue) {
                                if (!selectedValue) {
                                    selectedValue = 1;
                                }
                                //console.log("call buildselectbox: " + warehouse_id + " warehouse selectedvalue: " + selectedValue);
                                if (document.getElementById("bloomex_driver")) {
                                    document.getElementById("bloomex_driver").style.display = "none";
                                }
                                if (document.getElementById("local_driver")) {
                                    document.getElementById("local_driver").style.display = "none";
                                }
                                if (document.getElementById("courier")) {
                                    document.getElementById("courier").style.display = "none";
                                }
                                if (document.adminForm.driver_option_type) {
                                    document.adminForm.driver_option_type.length = 0;
                                    addOption(document.adminForm.driver_option_type, "-------------- Select --------------", "");


                                    aDriverOptionType = sDriverOptionType.split("[--2--]");
                                    for (i = 0; i < aDriverOptionType.length; i++) {
                                        if (aDriverOptionType[i]) {
                                            aDriverOptionTypeItem = aDriverOptionType[i].split("[--1--]");

                                            if (aDriverOptionTypeItem[0] == warehouse_id) {
                                                if (selectedValue == aDriverOptionTypeItem[1]) {
                                                    addOption(document.adminForm.driver_option_type, aDriverOptionTypeItem[1], aDriverOptionTypeItem[1], 1);
                                                } else {
                                                    addOption(document.adminForm.driver_option_type, aDriverOptionTypeItem[1], aDriverOptionTypeItem[1], 0);
                                                }
                                            }
                                        }
                                    }
                                }
                            }



                            function addOption(selectbox, text, value, default_selected) {
                                var optn = document.createElement("OPTION");
                                optn.text = text;
                                optn.value = value;
                                if (default_selected) {
                                    optn.selected = true;
                                }
                                selectbox.options.add(optn);
                            }

                            function cutPIN() {
                                aSelectedValue = document.adminForm.driver_option_type.value.split(" - ");

                                if (aSelectedValue[0].toLowerCase() == "Courier".toLowerCase() && aSelectedValue[1] == "Purolator") {
                                    document.getElementById("courier_tracking_number").value = TrimString(document.getElementById("courier_tracking_number").value);
                                    var sScanString = TrimString(document.getElementById("courier_tracking_number").value);
                                    if (sScanString.length > 13) {
                                        document.getElementById("courier_tracking_number").value = sScanString.substr(11, sScanString.length - 22);
                                    }
                                }
                            }

                            function TrimString(sInString) {
                                sInString = sInString.replace(/^\s+/g, "");// strip leading
                                return sInString.replace(/\s+$/g, "");// strip trailing
                            }


        </script>

        <?php
        //require_once 'ConstructBodyMapData.php';
        //$construct_body_map_data = new ConstructBodyMapData();
        $data_warehouse = isset($_POST['warehouse_id']) ? $_POST['warehouse_id'] : '';
        if (!isset($_POST['warehouse_id'])) {
            $construct_body_map_data->last_map_data_get();
            $warehouse = $construct_body_map_data->warehouse();
            $driver = $construct_body_map_data->driver();
            $data_warehouse = $warehouse;
        }
        ?>


        <form action="index2.php?option=com_ajaxorder&task=shipOrder" method="post" name="adminForm" onsubmit="return searchOrder();"> <!-- onsubmit="return searchOrder();"> -->
            <table class="adminheading">
                <tr>
                    <th colspan="2">
                        Ship Order
                    </th>
                </tr>
            </table>
            <br/>
            <br/>
            <table width="100%" class="adminform" border="1">
                <tr>
                    <th align="left" style="text-align:left;padding:10px 10px 10px 15px;">Orders Detail Information</th>
                </tr>
                <tr>
                    <td align="left" style="text-align:left;padding:10px 10px 10px 15px;">
                        <table width="100%" class="adminform">
                            <tr>
                                <td class="title" width="100">Warehouse:</td>
                                <td>
                                    <?php
                                    echo $aList['warehouse_id'];
                                    if (isset($warehouse) AND $warehouse != '') {
                                        ?>
                                        <script>

                                            var count = document.getElementById("warehouse_id").options.length - 1;
                                            for (var i = 0; i <= count; i++) {
                                                if (document.getElementById("warehouse_id").options[i].value == <?php echo $warehouse; ?>) {
                                                    document.getElementById("warehouse_id").selectedIndex = i;
                                                    buildSelectBox(i);
                                                    break;
                                                }
                                            }
                                        </script>
                                        <?php
                                        $aList['warehouse_id_selected'] = $warehouse;
                                        $aList['driver_option_type'] = $driver;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Driver</b>:</td>
                                <td>
                                    <select onchange="checkOption(this.value);" size="1" class="inputbox" name="driver_option_type" id="driver_option_type">
                                        <option selected="selected" value="">
                                            ------ Select ------
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">																		
                                    <span id="bloomex_driver" style="display:none;float:left;margin:0px 0px 0px 0px;">
                                        <span style="padding:0px 0px 30px 0px;display:block;width:240px;float:left;text-align:left;font-weight:bold;">Drivers Name and Telephone Number:</span> 
                                        <textarea type="text" rows="4" cols="40" value="" id="bloomex_driver_drivers_name_and_telephone_number" name="drivers_name_and_telephone_number" style="float:left;"><?php echo $aDriversOptions['drivers_name_and_telephone_number']; ?></textarea>
                                    </span>

                                    <span id="local_driver" style="display:none;float:left;margin:0px 0px 0px 0px;">
                                        <span style="padding:0px 0px 30px 0px;display:block;width:240px;float:left;text-align:left;font-weight:bold;">Telephone or Email:</span> 
                                        <textarea type="text" rows="4" cols="40" value="" id="local_driver_telephone_or_email" name="telephone_or_email" style="float:left;"><?php echo isset($aDriversOptions['telephone_or_email']) ?? ''; ?></textarea>
                                    </span>

                                    <span id="courier" style="display:none;float:left;margin:0px 0px 0px 0px;">
                                        <span style="padding:0px 0px 30px 0px;display:block;width:240px;float:left;text-align:left;font-weight:bold;">Telephone or Email:</span> 
                                        <textarea type="text" rows="4" cols="40" value="" id="courier_telephone_or_email" name="telephone_or_email2" style="float:left;"><?php echo isset($aDriversOptions['telephone_or_email']) ?? ''; ?></textarea><br/>
                                        <span style="padding:0px 0px 30px 0px;display:block;width:240px;float:left;text-align:left;font-weight:bold;">Tracking Number (manual input) (optional):</span>
                                        <textarea onchange="cutPIN();" type="text" rows="3" cols="40" value="" id="courier_tracking_number" name="tracking_number" style="float:left;"></textarea>
                                    </span>
                                    <script type="text/javascript">

        <?php if ($aList['warehouse_id_selected']) { ?>
                                            buildSelectBox("<?php echo $aList['warehouse_id_selected']; ?>", "<?php echo $aList['driver_option_type']; ?>");
        <?php } ?>
        <?php if ($aList['driver_option_type']) { ?>
                                            checkOption("<?php echo $aList['driver_option_type']; ?>");
        <?php } ?>
                                    </script>
                                </td>
                            <tr>
                                <td width="230"><b>Order ID: </b>example (bloom-2154215) (please scan the barcode from the New Form)</td>
                                <td align="left">
                                    <div style="padding:5px 0px 0px 0px;display:block;float:left;text-align:left">
                                        <input type="text" name="order_id" id="order_id" value="" size="30"/>
                                        <input type="hidden" name="ajax_post_search" id="ajax_post_search" value=""/>
                                        <input type="button" name="Search" id="Search" value="Search" onclick="searchOrder();"/>
                                        <div id="say_operation"><?php if ($sOrderSuccessful_data) echo $sOrderSuccessful_data; ?></div>
                                        <script type="text/javascript">
                                            document.adminForm.order_id.focus();
                                        </script>
                                    </div>

                                </td>
                            </tr>
                </tr>
            </table>
        </td>
        </tr>

        </table>

        <table cellpadding="0" cellspacing="0" width="100%" border="0">
            <tr>
                <td>
                    <div id="container_address">

                        <?php
                        $addr_line = array();
                        $addr_line_number = array();
                        $construct_body_map_data->work($sOrderListID, isset($aInfomation["OrderItem"]) ? $aInfomation["OrderItem"] : '');
                        $new_map_data = $construct_body_map_data->get();
                        $addr_line = $new_map_data['addr_line'];
                        $addr_line_number = $new_map_data['addr_line_number'];
                        $address_print = $new_map_data['address_print'];
                        $insert_data = $new_map_data['insert_data'];
                        if ($html3 == '') {
                            echo $new_map_data['html'];
                        } else {
                            echo $html3;
                        }

                        echo '<table id="submit_print"><tr>
                            <td style="padding:20px 0px 20px 20px;" colspan="6">
                                <input type="button" value="In Transit" onclick="submitOrder(1);" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" />	
                                <input name="print_button" type="button" onClick="print_map();" style="font:bold 11px Tahoma,Verdana;padding:5px 10px 5px 10px;cursor:pointer;" value="Print map">
                            </td>
                        </tr></table>';
                        ?>
                    </div>
                    <?php
                    // result map
                    echo $construct_body_map_data->table_end();
                    require_once 'bloomexorder.php';
                    $addresses = array();

                    $query3 = "SELECT warehouse_id, warehouse_name FROM jos_vm_warehouse WHERE warehouse_id='" . $data_warehouse . "' LIMIT 1";
                    $database->setQuery($query3);
                    $result3 = $database->loadObjectList();
                    //$sender_options = new SenderOptions( $my->username );
                    $name = '';
                    if (isset($result3[0])) {
                        $name = strtolower($result3[0]->warehouse_name);
                    }

                    $sender_options = new SenderOptions($name);
                    $addr_line_number[0] = '0';
                    $addr_line['0'] = '0[--2--]' . $sender_options->City . ', ' . $sender_options->StreetName . ' ' . $sender_options->StreetNumber . ', ' . $sender_options->PostalCode . ', ' . 'Australia';

                    if ($html3 == '') {
                        ?>
                        <script>
                            var item = document.getElementById('Search');
                            item.disabled = true;
                        </script>
                        <?php
                    }
                    ?>

                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
                    <script type="text/javascript" src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>

<!--                    <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>-->
                    <script 
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY&sensor=false">
                    </script>
                    <script>

                        var geocoder = '';
                        var map = '';
                        //var markers = new Array();
                        //var markers_ident = new Array();

                        addr_line_second = "<?php echo implode('[--1--]', $addr_line) ?>";
                        addr_line_second = addr_line_second.split("[--1--]");
                        addr_line = new Array();

                        var count_addr_line_second = addr_line_second.length;
                        for (var i = 0; i < count_addr_line_second; i++)
                        {
                            addr_line_htrid = addr_line_second[i].split("[--2--]");
                            addr_line[addr_line_htrid[0]] = addr_line_htrid[1];
                        }

                        addr_line_number = "<?php echo implode('[--1--]', $addr_line_number) ?>";
                        addr_line_number = addr_line_number.split("[--1--]");
                        //alert(addr_line_number);


                        geocoder = new google.maps.Geocoder();
                        geocoder.geocode({'address': addr_line['0']}, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                var mapOptions = {
                                    disableDefaultUI: true,
                                    zoom: 7,
                                    center: results[0].geometry.location,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                }
                                map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                            }
                        });
                        function initialize() {
                            // var geocoder;
                            //  var map;
                            // Create an object containing LatLng, population.


                            var count = addr_line_number.length;
                            var timer = setInterval(draw_marker, 1000);
                            var address = '';
                            var new_img = 0;

                            var img_i = 0;



                            function count_limit()
                            {
                                if (img_i >= count) {
                                    clearInterval(timer);
                                    var item = document.getElementById('Search');
                                    item.disabled = false;
                                    return true;
                                }
                                return false;
                            }

                            var ccc = new Array();
                            ccc_count = addr_line_number.length;
                            for (var r = 0; r < ccc_count; r++)
                            {
                                if (r == 0)
                                {
                                    ccc[r] = addr_line_number[ccc_count - 1];
                                }
                                else
                                {
                                    ccc[r] = addr_line_number[r - 1];
                                }
                            }
                            //alert(ccc);
                            for (var r = 0; r < ccc_count; r++)
                            {
                                addr_line_number[r] = ccc[r];
                            }

                            if (!markers)
                                markers = new Array();
                            function draw_marker() {
                                if (count_limit())
                                    return true;
                                new_img += (img_i < 1) ? 10 : 1;
                                for (; img_i < new_img; img_i++)
                                {
                                    if (count_limit())
                                        return true;
                                    if (addr_line_number[img_i] != 0)
                                    {
                                        var str_point = ($("#order_id_research").val() != '') ? ',' : '';
                                        $("#order_id_research").val($("#order_id_research").val() + str_point);
                                        $("#order_id_research").val($("#order_id_research").val() + addr_line_number[img_i]);
                                    }

                                    address = addr_line[addr_line_number[img_i]];
                                    if (address)
                                    {
                                        geocoder.geocode({'address': address}, function (results, status) {
                                            if (status == google.maps.GeocoderStatus.OK) {
                                                // alert(addr_line_number[img_number]);
                                                markers[addr_line_number[img_number]] = new google.maps.Marker({
                                                    map: map,
                                                    draggable: true,
                                                    position: results[0].geometry.location,
                                                    title: results[0].formatted_address,
                                                    icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + ((img_number == 0) ? 'w|CCCCCC|0000FF' : img_number) + '|FF0000|000000'
                                                });
                                                img_number++;
                                            }
                                        });
                                    }
                                }
                            }
                        }

                        jQuery(document).ready(function () {
                            initialize();
                        });

                        function print_map()
                        {
                            var a1 = $("#wrapper").css("display");
                            var a2 = $(".menubar").css("display");
                            var a3 = $(".menubackgr").css("display");
                            var a4 = $(".adminform").css("display");
                            var a5 = $(".adminheading").css("display");
                            var a6 = $(".smallgrey").css("display");
                            var a7 = $(".footer").css("display");
                            var a8 = $("#submit_print").css("display");


                            var p_l = $('#map_canvas').css('padding-left');
                            var w = $('#map_canvas').css('width');
                            var h = $('#map_canvas').css('height');

                            $('#map_canvas').css('width', '8in');
                            $('#map_canvas').css('height', '4.8in');
                            $('#map_canvas').css('padding-left', '1');

                            $('#wrapper').css('display', 'none');
                            $('.menubar').css('display', 'none');
                            $('.menubackgr').css('display', 'none');
                            $('.adminform').css('display', 'none');
                            $('.adminheading').css('display', 'none');
                            $('.smallgrey').css('display', 'none');
                            $('.footer').css('display', 'none');
                            $('#submit_print').css('display', 'none');
                            $('#hidden_address').css('display', 'block');
                            window.print();
                            $('#wrapper').css('display', a1);
                            $('.menubar').css('display', a2);
                            $('.menubackgr').css('display', a3);
                            $('.adminform').css('display', a4);
                            $('.adminheading').css('display', a5);
                            $('.smallgrey').css('display', a6);
                            $('.footer').css('display', a7);
                            $('#submit_print').css('display', a8);
                            $('#hidden_address').css('display', 'none');
                            $('#map_canvas').css('width', w);
                            $('#map_canvas').css('height', h);
                            $('#map_canvas').css('padding-left', p_l);
                        }
                    </script>

                    <?php
                    //}
                    ?>

                </td>
            </tr>
        </table>









<!--        <div name="map_canvas" id="map_canvas" style="height:600px;display:block;float:center;width:1000px;"></div>-->
        <br/><br/>
        <div align="left">
            <div id="hidden_address" style="display:none;float:left;">
                <?php
                echo $address_print;
                ?>

            </div>
        </div>


        <input type="hidden" name="option" id="option" value="com_ajaxorder" />
        <input type="hidden" name="task" id="task" value="shipOrder" />
        <input type="hidden" name="order_id_research" id="order_id_research" value="<?php echo $sOrderListID; ?>" />
        <input type="hidden" name="removeID" id="removeID" value="" />
        <input type="hidden" name="confirm" id="confirm" value="" />
        <input type="hidden" name="post_result" id="post_result" value="" />
        </form>
        <?php
    }

    function asterisk_pad($str, $display_length, $reversed = false) {

        $total_length = strlen($str);

        if ($total_length > $display_length) {
            if (!$reversed) {
                for ($i = 0; $i < $total_length - $display_length; $i++) {
                    $str[$i] = "*";
                }
            } else {
                for ($i = $total_length - 1; $i >= $total_length - $display_length; $i--) {
                    $str[$i] = "*";
                }
            }
        }

        return($str);
    }

}
?>
