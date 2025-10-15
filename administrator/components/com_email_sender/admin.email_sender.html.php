<?php
/**
 * @version $Id: admin.content.html.php 4070 2006-06-20 16:09:29Z stingrey $
 * @package Joomla
 * @subpackage Content
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
 * @subpackage Content
 */
class HTML_email_sender {

    /**
     * Writes a list of the content items
     * @param array An array of content objects
     */
//	function showemail_texts( &$rows,  $pageNav,$search,&$lists ) {
    static function showemail_texts(&$rows, $pageNav, $search) {
        global $my, $acl, $database, $mosConfig_offset;
        mosCommonHTML::loadOverlib();
        ?>


        <form action="index2.php?option=com_email_sender" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th class="edit" rowspan="2" nowrap>

                    </th>

                    <td width="right" valign="top">

                    </td>
                    <td width="right" valign="top">

                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Filter:
                    </td>
                    <td>
                        <input type="text" name="search" value="<?php echo $search; ?>" class="text_area" onChange="document.adminForm.submit();" />
                    </td>
                </tr>
            </table>

            <table class="adminlist">
                <tr>
                    <th>
                        id
                    </th>
                    <th>
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
                    </th>
                    <th style="text-align: left">
                        Title 
                    </th>
                    <th style="text-align: left">
                        Subject
                    </th>
                    <th style="text-align: left">
                        Order status 
                    </th>
                    <th align="center">
                        Published
                    </th>
                    <th align="center">
                        Sent
                    </th>
                    <th align="center">
                        Created(UTC-5)
                    </th>
                    <th align="center">
                        Created by
                    </th>
                </tr>
                <?php
                $k = 0;
                $nullDate = $database->getNullDate();
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = &$rows[$i];

                    $link = 'index2.php?option=com_email_sender&task=edit&hidemainmenu=1&id=' . $row->id;

                    $date = mosFormatDate($row->date, _CURRENT_SERVER_TIME_FORMAT);

                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td  align="center">
                            <?php echo $row->id; ?>
                        </td>
                        <td align="center">
                            <?php echo $checked; ?>
                        </td>
                        <td align="left">
                            <?php echo $row->title; ?>
                        </td>
                        <td align="left">
                            <a href="<?php echo $link; ?>" title="Edit Email">
                                <?php echo htmlspecialchars($row->subject, ENT_QUOTES); ?>
                            </a>
                        </td>
                        <td align="left">
                            <?php echo $row->order_status_name; ?>
                        </td>
                        <td align="center">
                            <?php
                            if ($row->publish) {
                                $alt = 'Published';
                                $img = 'publish_g.png';
                            } else {
                                $alt = 'Unpublished';
                                $img = 'publish_x.png';
                            }
                            ?>
                            <a href="javascript: void(0);"  onclick="return listItemTask('cb<?php echo $i; ?>', '<?php echo $row->publish ? "unpublish" : "publish"; ?>')">
                                <img src="images/<?php echo $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                            </a>
                        </td>
                        <td align="center">
                            <?php echo $row->sentvstotal; ?>
                        </td>
                        <td align="center">
                            <?php echo $date; ?>
                        </td>
                        <td align="center">
                            <?php echo $row->username; ?>
                        </td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>
            <?php mosCommonHTML::ContentLegend(); ?>
            <input type="hidden" name="option" value="com_email_sender" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    /**
     * Writes the edit form for new and existing content item
     *
     * A new record is defined when <var>$row</var> is passed with the <var>id</var>
     * property set to 0.
     * @param mosContent The category object
     * @param string The html for the groups select list
     */
    static function editemailtext(&$row, $option, $emailsList) {
        global $database;
        mosCommonHTML::loadBootstrap();
        mosMakeHtmlSafe($row);

        $nullDate = $database->getNullDate();
        $create_date = null;

        if (isset($row->date) && $row->date != $nullDate) {
            $create_date = mosFormatDate($row->date, _CURRENT_SERVER_TIME_FORMAT);
        }
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <script src="/administrator/components/com_email_sender/js/jquery.datetimepicker.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>

            .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
            .ui-timepicker-div dl { text-align: left; }
            .ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
            .ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
            .ui-timepicker-div td { font-size: 90%; }
            .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

            .ui-timepicker-rtl{ direction: rtl; }
            .ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
            .ui-timepicker-rtl dl dt{ float: right; clear: right; }
            .ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }
            #file-form{
                float: left;
            }
            .update_icon,.remove_icon{
                font-size: 15px;
                cursor: pointer;
                margin: 0 10px;
            }

            @-webkit-keyframes rotating /* Safari and Chrome */ {
                from {
                    -webkit-transform: rotate(0deg);
                    -o-transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                to {
                    -webkit-transform: rotate(360deg);
                    -o-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }
            @keyframes rotating {
                from {
                    -ms-transform: rotate(0deg);
                    -moz-transform: rotate(0deg);
                    -webkit-transform: rotate(0deg);
                    -o-transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                to {
                    -ms-transform: rotate(360deg);
                    -moz-transform: rotate(360deg);
                    -webkit-transform: rotate(360deg);
                    -o-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }
            .rotating {
                -webkit-animation: rotating 2s linear infinite;
                -moz-animation: rotating 2s linear infinite;
                -ms-animation: rotating 2s linear infinite;
                -o-animation: rotating 2s linear infinite;
                animation: rotating 2s linear infinite;
            }
            .bg-add{
                background: #ccc;
            }
        </style>

        <script language="javascript" type="text/javascript">
                                function findValueInArray(value, arr) {
                                    var result = "Doesn't exist";

                                    for (var i = 0; i < arr.length; i++) {
                                        var name = arr[i];
                                        if (name == value) {
                                            result = 'Exist';
                                            break;
                                        }
                                    }

                                    return result;
                                }

                                function getOrdersAlreadyAdded() {
                                    let orders = [];
                                    $('.list_item').each(function () {
                                        orders.push($(this).data('order_id'));
                                    });
                                    return orders;
                                }

                                function getAllOrders() {
                                    let orders = [];
                                    $('.list_item').each(function () {
                                        let arr = {};
                                        arr.order_id = $(this).data('order_id')
                                        arr.email = $(this).find('input[type="email"]').val()
                                        if ($(this).data('id')) {
                                            arr.id = $(this).data('id');
                                        }
                                        orders.push(arr);
                                    })
                                    return orders;
                                }

                                jQuery(document).ready(function () {

                                    jQuery('#date_send').datetimepicker({
                                        timeFormat: 'HH:mm:ss',
                                        stepHour: 2,
                                        stepMinute: 10,
                                        stepSecond: 10,
                                        minDate: "-1D",
                                        dateFormat: 'yy-mm-dd',
                                        constrainInput: true,

                                    });

                                    function ObjectLength(object) {
                                        var length = 0;
                                        for (var key in object) {
                                            if (object.hasOwnProperty(key)) {
                                                ++length;
                                            }
                                        }
                                        return length;
                                    }

                                    jQuery('#file-form').submit(function (e) {
                                        e.preventDefault();
                                        jQuery('#upload-button').val('Uploading .... ')
                                        jQuery('#error_text').hide()
                                        jQuery('#message').html('').hide();
                                        jQuery.ajax({
                                            url: '/administrator/components/com_email_sender/getemailslist.php',
                                            type: 'POST',
                                            data: new FormData(this),
                                            processData: false,
                                            contentType: false,
                                            success: function (data)
                                            {
                                                if (data) {
                                                    jQuery('#upload-button').val('Upload');
                                                    response = jQuery.parseJSON(data);
                                                    if (response.result) {
                                                        $.each(response.emails, function (key, value) {
                                                            let existOrders = getOrdersAlreadyAdded();
                                                            if (findValueInArray(value.order_id, existOrders) == 'Exist')
                                                            {
                                                                $('#message').append('<p class="text-danger">Order ' + value.order_id + ' already exist.</p>').show()
                                                            } else {
                                                                let tr = '<tr class="list_item bg-add"  data-order_id="' + value.order_id + '">\n' +
                                                                        '                 <td>' + value.order_id + '<br><span>added</span></td>\n' +
                                                                        '                 <td><div class="emailInputDiv ' + (value.emailValid ? 'has-success' : 'has-error') + '"><input type="email" value="' + value.email + '"class="form-control" readonly="readonly"></td>\n' +
                                                                        '                 <td>' + value.sent_datetime + '</td>\n' +
                                                                        '                 <td></td>\n' +
                                                                        '                 <td><i class="fa fa-remove remove_icon pull-left" aria-hidden="true"></i></td>\n' +
                                                                        '                     </tr>';
                                                                jQuery('#emails').append(tr)
                                                                $('#message').html('<span class="text-success">' + ObjectLength(response.emails) + ' Orders added success.</span>').show()
                                                            }
                                                        });

                                                    } else {
                                                        jQuery('#error_text').html(response.msg).show()
                                                    }
                                                } else {
                                                    jQuery('#error_text').show()
                                                }
                                            }
                                        });
                                        e.preventDefault();
                                    });

                                });

                                $(document).on('click', '.remove_icon', function () {
                                    $(this).parents('.list_item').fadeOut('slow').remove();
                                });
                                $(document).on('click', '#to_do_add', function () {
                                    jQuery('#message').html('').hide();
                                    let el = $(this);
                                    el.attr('disabled', true).text('please wait...');
                                    $.post("index2.php",
                                            {option: "com_email_sender",
                                                task: "addOrderId",
                                                order_id: $('#to_do_order_id').val()
                                            },
                                            function (data) {
                                                if (data) {
                                                    response = jQuery.parseJSON(data);
                                                    if (response.result) {
                                                        let existOrders = getOrdersAlreadyAdded();
                                                        if (findValueInArray(response.obj.order_id, existOrders) == 'Exist')
                                                        {
                                                            $('#message').html('<span class="text-danger">Order already exist.</span>').show()
                                                        } else
                                                        {
                                                            let tr = '<tr  class="list_item bg-add" data-order_id="' + response.obj.order_id + '">\n' +
                                                                    '                 <td>' + response.obj.order_id + '<br><span>added</span></td>\n' +
                                                                    '                 <td><div class="emailInputDiv ' + (response.obj.emailValid ? 'has-success' : 'has-error') + '"><input type="email" value="' + response.obj.user_email + '"class="form-control" readonly="readonly"></td>\n' +
                                                                    '                 <td>0000-00-00 00:00:00</td>\n' +
                                                                    '                 <td></td>\n' +
                                                                    '                 <td><i class="fa fa-remove remove_icon pull-left" aria-hidden="true"></i></td>\n' +
                                                                    '                     </tr>';
                                                            $('#emails').append(tr);
                                                            $('#message').html('<span class="text-success">Order Id added success.</span>').show()
                                                        }
                                                    } else {
                                                        $('#message').html(response.msg).show()
                                                    }
                                                }
                                                el.removeAttr('disabled').text('Save');
                                            }
                                    );
                                });
                                $(document).on('click', '.update_icon', function () {
                                    let el = $(this);
                                    var list_id = el.data('id');
                                    el.addClass('rotating');
                                    $.post("index2.php",
                                            {option: "com_email_sender",
                                                task: "updateEmail",
                                                email: $('#list-item-' + list_id + ' input[type="email"]').val(),
                                                id: list_id
                                            },
                                            function (data) {
                                                if (data) {
                                                    $('#list-item-' + list_id + ' .msg').html(data)
                                                }
                                                el.removeClass('rotating')
                                            }
                                    );
                                });

                                function validateEmail(email) {
                                    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                    return re.test(email);
                                }


                                $(document).on('input', '.emailInputDiv input[type="email"]', function () {
                                    if (validateEmail($(this).val())) {
                                        $(this).parent('.emailInputDiv').removeClass('has-error').addClass('has-success');
                                    } else {
                                        $(this).parent('.emailInputDiv').removeClass('has-success').addClass('has-error');
                                    }
                                })

                                function submitbutton(pressbutton) {
                                    var form = document.adminForm;
                                    if (pressbutton != 'cancel') {
                                        // do field validation
                                        if (form.subject.value == "") {
                                            alert("Email item must have a subject");
                                        } else {
        <?php getEditorContents('editor1', 'introtext'); ?>
                                            let orders = getAllOrders();
                                            form.to.value = JSON.stringify(orders)
                                            submitform(pressbutton);
                                        }
                                    } else {
                                        submitform(pressbutton);
                                    }
                                }

        </script>

        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th class="edit">
                        Email Item:
                        <small>
                            <?php echo isset($row->id) ? 'Edit' : 'New'; ?>
                        </small>

                    </th>
                </tr>
            </table>

            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td width="60%" valign="top">
                        <table width="100%" class="adminform">
                            <tr>
                                <td width="100%">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <th colspan="2">
                                                Item Details
                                            </th>
                                        </tr>
                                        <tr>
                                            <td  colspan="2"><strong> You can use {user_name} , {user_last_name} , {user_email}, {order_id} placeholders</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Subject:
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="subject" size="30" maxlength="255" value="<?php echo isset($row->subject) ? $row->subject : ''; ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Order Status:
                                            </td>
                                            <td>
                                                <select name="order_status" class="form-control">
                                                    <option value="">NO CHANGE</option>
                                                    <?php
                                                    $query = "SELECT order_status_code, order_status_name FROM jos_vm_order_status WHERE `publish`='1' ";
                                                    $database->setQuery($query);
                                                    $statuses = $database->loadObjectList();
                                                    foreach ($statuses as $status) {
                                                        $selected = ($status->order_status_code == $row->order_status_code) ? ' selected="selected" ' : '';
                                                        ?>
                                                        <option value ="<?php echo $status->order_status_code; ?>" <?php echo $selected; ?>><?php echo $status->order_status_name; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Title:
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="title" size="30" maxlength="255" value="<?php echo isset($row->title) ? $row->title : ''; ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Mark status:
                                            </td>
                                            <td>
                                                <select name="mark_status" class="form-control">
                                                    <option value="">NO CHANGE</option>
                                                    <?php
                                                    $query = "SELECT order_mark_code, order_mark_name FROM jos_vm_order_mark";
                                                    $database->setQuery($query);
                                                    $marks = $database->loadObjectList();
                                                    foreach ($marks as $mark) {
                                                        $selected = isset($row->mark_status) && $mark->order_mark_code == $row->mark_status;
                                                        ?>
                                                        <option value ="<?php echo $mark->order_mark_code; ?>" <?php echo $selected ? 'selected' : ''?>><?php echo $mark->order_mark_name; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Mark description:
                                            </td>
                                            <td>
                                                <textarea style="width: 100%" id="mark_description" name="mark_description" maxlength="120" placeholder="maximum length 120 characters" rows="5" cols="30" ><?php echo isset($row->mark_description) ? $row->mark_description : ''; ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <td width = "100%">
                                Email Text:
                                <script src = '/ckeditor/ckeditor.js'></script>
                                <textarea class="text_area" id='introtext' name="introtext"><?php echo isset($row->text) ? $row->text : ''; ?></textarea>
                                <script> CKEDITOR.replace("introtext");</script>
                            </td>
                        </table>
                    </td>
                    <td valign="top" width="40%">
                        <table class="adminform">
                            <tr>
                                <th colspan="2">
                                    Email Info
                                </th>
                            </tr>

                            <tr>
                                <td valign="top" align="right">
                                    Published:
                                </td>
                                <td>
                                    <input type="checkbox" name="publish" value="1" <?php echo isset($row->publish) ? 'checked="checked"' : ''; ?> />
                                </td>
                            </tr>
                            <tr>
                                <td>System Time(UTC):</td>
                                <td><span id="system_time"></span></td>
                            </tr>
                            <tr>
                                <td valign="top" align="right">
                                    <strong>
                                        Start Time(UTC)
                                    </strong>
                                </td>
                                <td>
                                    <?php if (!$create_date) { ?>
                                        <input type="text" name="date_send" class="form-control" id="date_send" value="<?php echo date('Y-m-d H:i:s'); ?> ">
                                    <?php } else { ?>
                                        <input type="text" name="date_send" class="form-control" id="date_send" value="<?php echo $create_date; ?> ">
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <p id="message" class="text-center"></p>

                                    <table class="table table-bordered list-group" id="emails">
                                        <tr>
                                            <th>Order Id</th>
                                            <th>Customer Email</th>
                                            <th>Sent Datetime</th>
                                            <th>Sent Status</th>
                                            <th>Action</th>
                                        </tr>

                                        <?php
                                        foreach ($emailsList as $r) {
                                            $status = 0;
                                            if ($r->send_status == 'ok') {
                                                $status = 1;
                                            }
                                            ?>
                                            <tr id="list-item-<?php echo $r->id; ?>" class="list_item" data-order_id="<?php echo $r->order_id; ?>" data-id="<?php echo $r->id; ?>">
                                                <td> <?php echo$r->order_id; ?></td>
                                                <td>
                                                    <div class="emailInputDiv <?php echo ((filter_var($r->email, FILTER_VALIDATE_EMAIL)) ? 'has-success' : 'has-error'); ?>" >
                                                        <input <?php echo (($r->send_status == 'ok') ? "disabled" : ""); ?> type = "email" value = "<?php echo $r->email; ?>" class="form-control" readonly="readonly">
                                                    </div>
                                                </td>
                                                <td><?php echo $r->sent_datetime; ?></td>
                                                <td><?php echo $r->send_status; ?></td>
                                                <td>
                                                    <i class="fa fa-refresh update_icon" data-id="<?php echo $r->id; ?>" aria-hidden="true"></i>
                                                    <i class="fa fa-remove remove_icon" data-id="<?php echo $r->id; ?>" aria-hidden="true"></i>
                                                    <p class="msg"></p>
                                                </td>
                                            </tr> 
                                        <?php } ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="mask" value="0" />
            <input type="hidden" name="to" value="" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>

        <form id="file-form" class="form-inline" action="/administrator/components/com_email_sender/getemailslist.php" method="POST">
            <input type="file" id="file-select" name="file_emails" class="form-control" multiple/>
            <button type="submit" class="btn btn-success" id="upload-button">Upload</button>
            <a href="https://media.bloomex.ca/bloomex.ca/orders_list_correct_format.csv"  download="orders_list_correct_format">
                <button class="btn btn-info" type="button" >Download Correct Format</button>
            </a>
            <p style="color:red;display: none;" id="error_text">File is empty or has wrong format, please try another file</p>
        </form>

        <script>
            function startTime() {
                var date = new Date();
                document.getElementById('system_time').innerHTML = date.toUTCString();
                setTimeout(startTime, 1000);
            }

            startTime();
        </script>
        <?php
    }

}
