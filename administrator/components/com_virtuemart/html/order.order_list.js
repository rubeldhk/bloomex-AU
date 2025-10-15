var Base64 = {_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) {
    var t = "";
    var n, r, i, s, o, u, a;
    var f = 0;
    e = Base64._utf8_encode(e);
    while (f < e.length) {
        n = e.charCodeAt(f++);
        r = e.charCodeAt(f++);
        i = e.charCodeAt(f++);
        s = n >> 2;
        o = (n & 3) << 4 | r >> 4;
        u = (r & 15) << 2 | i >> 6;
        a = i & 63;
        if (isNaN(r)) {
            u = a = 64
        } else if (isNaN(i)) {
            a = 64
        }
        t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
    }
    return t
}, decode: function (e) {
    var t = "";
    var n, r, i;
    var s, o, u, a;
    var f = 0;
    e = e.replace(/[^A-Za-z0-9+/=]/g, "");
    while (f < e.length) {
        s = this._keyStr.indexOf(e.charAt(f++));
        o = this._keyStr.indexOf(e.charAt(f++));
        u = this._keyStr.indexOf(e.charAt(f++));
        a = this._keyStr.indexOf(e.charAt(f++));
        n = s << 2 | o >> 4;
        r = (o & 15) << 4 | u >> 2;
        i = (u & 3) << 6 | a;
        t = t + String.fromCharCode(n);
        if (u != 64) {
            t = t + String.fromCharCode(r)
        }
        if (a != 64) {
            t = t + String.fromCharCode(i)
        }
    }
    t = Base64._utf8_decode(t);
    return t
}, _utf8_encode: function (e) {
    e = e.replace(/rn/g, "n");
    var t = "";
    for (var n = 0; n < e.length; n++) {
        var r = e.charCodeAt(n);
        if (r < 128) {
            t += String.fromCharCode(r)
        } else if (r > 127 && r < 2048) {
            t += String.fromCharCode(r >> 6 | 192);
            t += String.fromCharCode(r & 63 | 128)
        } else {
            t += String.fromCharCode(r >> 12 | 224);
            t += String.fromCharCode(r >> 6 & 63 | 128);
            t += String.fromCharCode(r & 63 | 128)
        }
    }
    return t
}, _utf8_decode: function (e) {
    var t = "";
    var n = 0;
    var r = c1 = c2 = 0;
    while (n < e.length) {
        r = e.charCodeAt(n);
        if (r < 128) {
            t += String.fromCharCode(r);
            n++
        } else if (r > 191 && r < 224) {
            c2 = e.charCodeAt(n + 1);
            t += String.fromCharCode((r & 31) << 6 | c2 & 63);
            n += 2
        } else {
            c2 = e.charCodeAt(n + 1);
            c3 = e.charCodeAt(n + 2);
            t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
            n += 3
        }
    }
    return t
}}
jQuery(document).ready(function () {
    $(".order_id_type").focus(function () {
        $(".order_id_type").not(this).val('');
    });
    $('a.delivery_company').on('click', function (event) {
        $('.delivery_icon_span').addClass('hidden').html('Updating...');
        $(this).next('span').removeClass('hidden');
    });
    $('a.delivery_company.default').on('click', function (event) {
        event.preventDefault();
        if ($(this).hasClass('default')) {


            var order_id = parseInt($(this).attr('order_id'));
            var order_i = parseInt($(this).attr('i'));
            var has_active_shipment_less_one_day = false;
            $.ajax({
                url: 'index2.php',
                type: 'POST',
                async: false,
                dataType: 'json',
                order_id: order_id,
                data: {
                    option: 'com_ajaxorder',
                    task: 'CheckOldDeliveries',
                    order_id: order_id
                },
                success: function (data)
                {
                    if (data.result) {
                        return has_active_shipment_less_one_day = true;
                    }
                }
            });
            if (has_active_shipment_less_one_day) {
                $('.delivery_icon_span_' + order_id).addClass('hidden');
                alert('This order already has active shipment. Please cancel it');
                return false;
            }
            $(this).hide();
            var warehouse_code = $('#warehouse' + order_i).val();
            $('.delivery_loader[order_id="' + order_id + '"]').show();
            $('.delivery_icon_span').addClass('hidden').html('Updating...');
            $('.delivery_icon_span_' + order_id).removeClass('hidden');
            $.ajax({
                url: 'index2.php',
                type: 'POST',
                dataType: 'json',
                order_id: order_id,
                warehouse_code: warehouse_code,
                data: {
                    option: 'com_ajaxorder',
                    task: 'getDeliveries',
                    warehouse_code: warehouse_code,
                    order_id: order_id
                },
                success: function (data)
                {
                    if (data.result) {
                        var deliveries_html = '<div class="row deliveries_popup">';
                        $.each(data.rows, function (key, value) {
                            deliveries_html += '<div class="col-md-4 deliveries_company">';
                            deliveries_html += '<a target="_blank" href="' + value.sent_endpoint + '?delivery_id=' + value.id + '&order_id=' + order_id + '&wh=' + warehouse_code + '&sender=' + sender + '">';
                            deliveries_html += '<img src="/templates/bloomex7/images/deliveries/' + value.name + '_logo_lg.png">';
                            deliveries_html += '</a>';
                            deliveries_html += '</div>';
                        });
                        deliveries_html += '</div>';
                        $('#openModal').show();
                        $('#popup_details').html(deliveries_html);
                    } else {
                        alert('Verify that the warehouse has been assigned');
                    }
                    $('.delivery_loader[order_id="' + order_id + '"]').hide();
                    $('.delivery_company[order_id="' + order_id + '"]').show();
                }
            });
        }
    });
    $('a.delivery_company.unactive').click(function (event) {
        event.preventDefault();
    });
    $(".close_popup").bind('click', function () {
        jQuery("#openModal").hide()
        jQuery("#popup_details").html('')
    })

    if (document.querySelectorAll('.order-detail').length == 1)
    {
        $('.order-detail').trigger('click');
    }

    if (warehouse_only != false) {
        jQuery("#vmMenuID").css('display', 'block');
    }
    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    (function ($) {
        if (typeof $.fn.prop !== 'function')
            $.fn.prop = function (name, value) {
                if (typeof value === 'undefined') {
                    return this.attr(name);
                } else {
                    return this.attr(name, value);
                }
            };
    })(jQuery);
    $(".check_all").change(function () {
        $(".order_checkbox").prop('checked', $(this).prop("checked"));
        print_orders_input()
    })

    function print_orders_input() {
        var aOrderId = new Array();
        jQuery(".order_checkbox:checked").each(function (id) {
            orderId = jQuery(".order_checkbox:checked").get(id);
            if (isNumeric(orderId.value))
                aOrderId.push(orderId.value);
        });
        sOrderId = aOrderId.join(",");
        if (sOrderId != '') {
            var statuses = "<div class='status_list' style='display: inline-block;margin-left: 7px;'>";
            statuses += statuses_list;
            statuses += "</div>";
            var type = jQuery('#select_print_type').val();
            var url1 = "/administrator/index3.php?page=order.order_printdetails2&order_id=" + sOrderId + "&no_menu=1&option=com_virtuemart";
            var url2 = "/administrator/index3.php?page=order.order_printlabel&order_id=" + sOrderId + "&no_menu=1&option=com_virtuemart";
            var url3 = "/administrator/index3.php?page=order.order_printlabel2&order_id=" + sOrderId + "&no_menu=1&option=com_virtuemart";
            var url4 = "/administrator/index3.php?page=order.order_perforatelabels&order_id=" + sOrderId + "&no_menu=1&option=com_virtuemart";
            var url5 = "/administrator/index3.php?page=order.order_printablegiftcard&order_id=" + sOrderId + "&no_menu=1&option=com_virtuemart";
            var url6 = "/administrator/index3.php?page=order.order_instructions&order_id=" + sOrderId + "&no_menu=1&option=com_virtuemart";
            var append = "<a  class='print_orders_a'  style='cursor:pointer;margin-left:5px' href=\"javascript: void window.open('" + url1 + "', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\" > <img title=\"Wiew\" src=\"/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
            append += "<a class='print_orders_a'  style='cursor:pointer;margin-left:5px' href=\"javascript: void window.open('" + url2 + "', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"> <img title=\"Label\"   src=\"/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
            append += "<a class='print_orders_a'  style='cursor:pointer;margin-left:5px' href=\"javascript: void window.open('" + url3 + "', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\" > <img  title=\"Label2\"  src=\"/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
            append += "<a class='print_orders_a'  style='cursor:pointer;margin-left:5px' href=\"javascript: void window.open('" + url4 + "', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"> <img  title=\"NEW FORM\"  src=\"/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
            append += "<a class='print_orders_a'  style='cursor:pointer;margin-left:5px' href=\"javascript: void window.open('" + url5 + "', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"> <img  title=\"GIFT\"  src=\"/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
            append += "<a class='print_orders_a'  style='cursor:pointer;margin-left:5px' href=\"javascript: void window.open('" + url6 + "', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"> <img  title=\"Special Instructions\"  src=\"/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>";
            if (jQuery('#print_orders_div').find('.print_orders_a').length == 0) {
                jQuery("#print_orders_div").append(append)
                jQuery("#print_orders_div").append(statuses)
                jQuery("#print_orders_div").append("<input type='button'  onclick='update_orders_statuses()' style='cursor:pointer;margin-left:5px' class='update_orders_statuses'  data='" + sOrderId + "' value='change orders statuses'>")
                jQuery("#print_orders_div").append("<input type='button'  onclick='send_orders_to_PeoplePost()' style='cursor:pointer;margin-left:5px' class='send_orders_to_PeoplePost'  data='" + sOrderId + "' value='send orders to PeoplePost'>")
                jQuery("#print_orders_div").append("<input type='button'  onclick='send_orders_to_AusPost()' style='cursor:pointer;margin-left:5px' class='send_orders_to_AusPost'  data='" + sOrderId + "' value='send orders to AusPost Express'>")
                jQuery("#print_orders_div").append("<input type='button'  onclick='send_orders_to_AusPostOnDemand()' style='cursor:pointer;margin-left:5px' class='send_orders_to_AusPostOnDemand'  data='" + sOrderId + "' value='send orders to AusPost On Demand'>")
                jQuery("#print_orders_div").append("<input type='button'  onclick='send_orders_to_ShipStation()' style='cursor:pointer;margin-left:5px' class='send_orders_to_ShipStation'  data='" + sOrderId + "' value='send orders to ShipStation'>")


            } else {
                jQuery('.print_orders_a').remove()
                jQuery('.update_orders_statuses').remove()
                jQuery('.status_list').remove()
                jQuery('.send_orders_to_PeoplePost').remove()
                jQuery('.send_orders_to_AusPost').remove()
                jQuery('.send_orders_to_AusPostOnDemand').remove()
                jQuery('.send_orders_to_ShipStation').remove()
                jQuery('.download_orders_list').remove()
                jQuery("#print_orders_div").append(append)
                jQuery("#print_orders_div").append(statuses)
                jQuery("#print_orders_div").append("<input type='button' onclick='update_orders_statuses()' style='cursor:pointer;margin-left:5px' class='update_orders_statuses' data='" + sOrderId + "' value='change orders statuses'>")
                jQuery("#print_orders_div").append("<input type='button' onclick='send_orders_to_PeoplePost()' style='cursor:pointer;margin-left:5px' class='send_orders_to_PeoplePost' data='" + sOrderId + "' value='send orders to PeoplePost'>")
                jQuery("#print_orders_div").append("<input type='button' onclick='send_orders_to_AusPost()' style='cursor:pointer;margin-left:5px' class='send_orders_to_AusPost' data='" + sOrderId + "' value='send orders to AusPost Express'>")
                jQuery("#print_orders_div").append("<input type='button' onclick='send_orders_to_AusPostOnDemand()' style='cursor:pointer;margin-left:5px' class='send_orders_to_AusPostOnDemand' data='" + sOrderId + "' value='send orders to AusPost On Demand'>")
                jQuery("#print_orders_div").append("<input type='button' onclick='send_orders_to_ShipStation()' style='cursor:pointer;margin-left:5px' class='send_orders_to_ShipStation' data='" + sOrderId + "' value='send orders to ShipStation'>")
            }
            jQuery("#print_orders_div").append("<input type='button' onclick='download_orders_list()' style='cursor:pointer;margin-left:5px' class='download_orders_list' data='" + sOrderId + "' value='Download Orders List'>")

        } else {

            jQuery('.print_orders_a').remove()
            jQuery('.status_list').remove()
            jQuery('.update_orders_statuses').remove()
            jQuery('.send_orders_to_PeoplePost').remove()
            jQuery('.send_orders_to_AusPost').remove()
            jQuery('.send_orders_to_AusPostOnDemand').remove()
            jQuery('.send_orders_to_ShipStation').remove()
            jQuery('.download_orders_list').remove()
        }

    }
    jQuery("input[name*='order_id']").change(function () {

        print_orders_input();
    })

});


function refundPopup(nOrderId,listId = null){
    Swal.fire({
        title: 'Select reason for status Refund',
        width: '580px',
        html: `
                            <select id="rejectReason" class="swal2-select" style="border-radius: 6px;padding: 5px;width: 70%;">
                                <option value="">-- Select a reason --</option>
                                <option>Customer's issue</option>
                                <option>Location for the date</option>
                                <option>Wh missed</option>
                                <option>Wh incomplete, wrong action</option>
                                <option>Wh quality</option>
                                <option>Stock issue</option>
                                <option>Local florist failed to deliver/bad quality/incomplete by LF</option>
                                <option>Overnight courier non delivery</option>
                                <option>Overnight courier delay/quality</option>
                                <option>LD issue</option>
                                <option>Logistics issue</option>
                                <option>Extra charge/discount</option>
                                <option>Delivery fee to save</option>
                                <option>Fraud</option>
                                <option>System issue</option>
                                <option>CS mistake</option>
                                <option>PPI</option>
                            </select><br>
                            <textarea id="description" name="description" placeholder="Enter description..." style="width:70%;border-radius: 10px; padding: 10px; box-sizing:border-box; height:80px; margin-top:8px;"></textarea>
                        `,
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const reason = document.getElementById('rejectReason').value;
            const description = document.getElementById('description').value;

            if (!reason) {
                Swal.showValidationMessage('Please select a reason!');
                return false;
            }
            if (!description) {
                Swal.showValidationMessage('Please add a reason description!');
                return false;
            }

            return { reason, description };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const {reason, description} = result.value;

            $.post("index2.php", {
                option: "com_ajaxorder",
                task: "saveRefoundReason",
                order_id: nOrderId,
                reason: reason,
                description: description
            }, function (resp) {
                if (resp == "success") {

                    const reasonText = "<b>Reason:</b> " + reason +
                        (description ? " (" + description + ")" : "");

                    if(listId){
                        update_status(listId, 0,reasonText)
                    }else{
                        listId =  $("#orderdetailinfo" + nOrderId).attr('listId');
                        $("#order_status" + listId).val('R')
                        if ($("input[name='notify_customer_inside']").is(":checked")) {
                            $('.notify_customer_click_' + listId).prop('checked', true)
                            $('#notify_customer' + listId).val("Y")
                        }
                        update_status(listId, 0,$("textarea[name='order_comment_inside']").val().trim() + "\n" + reasonText)
                    }

                } else {
                    Swal.fire("Error", "Failed to save reason: " + resp.message, "error");
                }
            });
        }
    });
}

//======================================================================================
$("#exportOrderText").click(function () {
    var aOrderId = new Array();
    $("input:checked").each(function (id) {
        orderId = $("input:checked").get(id);
        aOrderId.push(orderId.value);
    });
    if (aOrderId.length <= 0 && $("input[name='ups_order_id']").val() == "") {
        alert("Please choose some orders to export!");
        return;
    }
    sOrderId = aOrderId.join(",");
    if ($("input[name='ups_order_id']").val()) {
        sOrderId = sOrderId + "," + $("input[name='ups_order_id']").val();
    }


    $.post("index2.php",
            {option: "com_ajaxorder",
                task: "exportUPSConnect",
                order_id: sOrderId
            },
            function (data) {
                if (data != "error") {
                    $("input:checked").attr("checked", false);
                    $("#exportResult").html("<b>Right click to <a href='" + data + "' target='_blank'>Order.txt</a> and choose Save As this file to your pc.</b>");
                    $("input[name='ups_order_id']").val("");
                } else {
                    alert("Export Order.txt file error!")
                }
            }
    );
});
$('select[name=order_status]').change(function () {
    var order_status = this.value;
    orderID = str_replace('order_status', '', $(this).attr("id"));
    if (order_status == 'C' || order_status == 'Z' || order_status == 'X' || order_status == 'D') {
        $('.notify_customer_click_' + orderID).prop('checked', true)
        $('#notify_customer' + orderID).val("Y")
    }
    if ((order_status == 'T') || (order_status == 'F') || (order_status == 'L')) {

        $('#partner' + orderID).select2({
            placeholder: "Search a Value",
            allowClear: true
        }).css('display', '');
    } else
    {
        $('#partner' + orderID).css('display', 'none');
        $('.select2-container').css('display', 'none');
    }
});
function str_replace(search, replace, subject) {
    return subject.split(search).join(replace);
}
$(".order-detail").click(function () {
    let orderID = $(this).attr("id");
    orderNumber = $(this).attr("rel");
    if (!orderID) {
        alert('Load order information wrong');
        return;
    }

    $(".orderdetailinfo").html("");
    $("#ajaxorderresult" + orderID).css("display", "block");
    $("#ajaxorderresult" + orderID).html('<img src="' + sImgLoading + '" align="absmiddle"/> Loading...');
    $.post("index2.php",
            {option: "com_ajaxorder", task: "default", id: orderID},
            function (data) {
                //alert(data);
                aData = data.split("[--1--]");
                if (aData[0] != "error") {
                    $("#ajaxorderresult" + orderID).css("display", "none");
                    $("#orderdetailinfo" + orderID).html(data);
                    $(".close-button").click(function () {
                        $("#orderdetailinfo" + orderID).html("");
                    });
//======================================================================================
                    $("#removeCCInfo").click(function () {
                        orderID = $(this).attr("class");
                        if (orderID > 0) {
                            if (confirm("Do you want to remove this Credit Card Number information? ")) {
                                $("#btnRemoveCCInfo").css("display", "none");
                                $("#msgRemoveCCInfo").css("display", "block");
                                $("#msgRemoveCCInfo").html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
                                //alert( $("select[name='bill_country']").val() + '===' + $("select[name='bill_state']").val() );
                                $.post("index2.php",
                                        {option: "com_ajaxorder",
                                            task: "removeCCInfo",
                                            order_id: orderID
                                        },
                                        function (data) {
                                            if (data == "success") {
                                                $("#msgRemoveCCInfo").html("Update Successful.");
                                                $("#CCInfo_" + orderID).html("NOT SAVED");
                                            } else {
                                                $("#msgRemoveCCInfo").html("Update Wrong.");
                                                $("#btnRemoveCCInfo").css("display", "block");
                                            }
                                        }
                                );
                            }
                        }

                    });
//======================================================================================
                    $(".update-billing").click(function () {
//                    if (!isValidZipCode($("input[name='bill_zip_code']").val())) {
//                        alert("Please enter your billing postcode again!");
//                        return;
//                    }

                        $("#update_billing_result").css("display", "block");
                        $("#update_billing_result").html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateBilling",
                                    id: $(this).attr("id"),
                                    bill_type: $("select[name='bill_type']").val(),
                                    bill_company_name: $("input[name='bill_company_name']").val(),
                                    bill_first_name: $("input[name='bill_first_name']").val(),
                                    bill_last_name: $("input[name='bill_last_name']").val(),
                                    bill_middle_name: $("input[name='bill_middle_name']").val(),
                                    bill_suite: $("input[name='bill_address_suite']").val(),
                                    bill_street_number: $("input[name='bill_address_street_number']").val(),
                                    bill_street_name: $("input[name='bill_address_street_name']").val(),
                                    bill_district: $("input[name='bill_district']").val(),
                                    bill_city: $("input[name='bill_city']").val(),
                                    bill_zip_code: $("input[name='bill_zip_code']").val(),
                                    bill_country: $("select[name='bill_country']").val(),
                                    bill_state: $("select[name='bill_state']").val(),
                                    bill_phone: $("input[name='bill_phone']").val(),
                                    bill_evening_phone: $("input[name='bill_evening_phone']").val(),
                                    bill_fax: $("input[name='bill_fax']").val(),
                                    bill_email: $("input[name='bill_email']").val()
                                },
                                function (data) {
                                    if (data == "success") {
                                        $("#update_billing_result").html("Update Successful.");
                                    } else {

                                        $("#update_billing_result").html("Update Wrong.");
                                    }
                                }
                        );
                    });
                    $(".update-deliver").click(function () {
//                    if (!isValidZipCode($("input[name='deliver_zip_code']").val())) {
//                        alert("Please enter your shipping postcode again!");
//                        return;
//                    }
                        $("#update_deliver_result").css("display", "block");
                        $("#update_deliver_result").html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateDeliver",
                                    id: $(this).attr("id"),
                                    deliver_type: $("select[name='deliver_type']").val(),
                                    address_type2: $("select[name='address_type2']").val(),
                                    deliver_company_name: $("input[name='deliver_company_name']").val(),
                                    deliver_first_name: $("input[name='deliver_first_name']").val(),
                                    deliver_last_name: $("input[name='deliver_last_name']").val(),
                                    deliver_middle_name: $("input[name='deliver_middle_name']").val(),
                                    deliver_suite: $("input[name='deliver_address_suite']").val(),
                                    deliver_street_number: $("input[name='deliver_address_street_number']").val(),
                                    deliver_street_name: $("input[name='deliver_address_street_name']").val(),
                                    deliver_district: $("input[name='deliver_district']").val(),
                                    deliver_city: $("input[name='deliver_city']").val(),
                                    deliver_zip_code: $("input[name='deliver_zip_code']").val(),
                                    deliver_country: $("select[name='deliver_country']").val(),
                                    deliver_state: $("select[name='deliver_state']").val(),
                                    deliver_phone: $("input[name='deliver_phone']").val(),
                                    deliver_evening_phone: $("input[name='deliver_evening_phone']").val(),
                                    deliver_fax: $("input[name='deliver_fax']").val(),
                                    deliver_email: $("input[name='deliver_email']").val()
                                },
                                function (data) {
                                    if (data == "success") {
                                        $("#update_deliver_result").html("Update Successful.");
                                    } else {
                                        $("#update_deliver_result").html("Update Wrong.");
                                    }
                                }
                        );
                    });
                    $(".refresh-order-history").click(function () {
                        $("#refreshOrderHistory").html('<div style="font: bold 11px Tahoma;color:#FF6600;line-height:24px;"><img src="' + sImgLoading + '" align="absmiddle"/> Loading...</div>');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "loadOrderHistory",
                                    id: $(".refresh-order-history").attr("id")
                                },
                                function (data) {
                                    $("#refreshOrderHistory").html(data);
                                }
                        );
                    });
                    $(".customerSentEmailCount").click(function () {
                        let el = $(this);
                        el.attr('disabled', true).html('Loading...');
                        $.post("index2.php",
                            {option: "com_ajaxorder",
                                task: "checkCustomerSentEmailsCount",
                                sender: $(".customerSentEmailCount").attr("customerEmail")
                            },
                            function (data) {
                                data = JSON.parse(data);
                                if (data.result) {
                                    el.val("Confirm Customer Sent Emails (" + data.count + ")").removeClass('customerSentEmailCount btn-info').addClass('confirmCustomerSentEmail btn-success').attr('disabled', false);
                                    el.unbind('click');
                                    $(".confirmCustomerSentEmail").click(function () {
                                        let el = $(this);
                                        el.attr('disabled', true).html('Loading...');
                                        $.post("index2.php",
                                            {option: "com_ajaxorder",
                                                task: "confirmCustomerSentEmail",
                                                sender: $(".confirmCustomerSentEmail").attr("customerEmail")
                                            },
                                            function (data) {
                                                data = JSON.parse(data);
                                                if (data.result) {
                                                    el.replaceWith('<span class="text-success mailbotAjaxResult">' + data.msg + '</span>');
                                                } else {
                                                    el.replaceWith('<span class="text-danger mailbotAjaxResult">' + data.error + '</span>');
                                                }

                                            }
                                        );
                                    });

                                } else {
                                    el.replaceWith('<span class="text-danger mailbotAjaxResult">' + data.error + '</span>');
                                }

                            }
                        );
                    });
                    jQuery('#ordercondition .form-check-input').on("click", function () {
                        $("#ordercondition_update_result").html('<img src="' + sImgLoading + '" align="absmiddle"/> Please Wait...');
                        $("#ordercondition").css('cursor', 'not-allowed');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "SetOrderCondition",
                                    soft_fraud: $("#soft_fraud").is(":checked"),
                                    hard_fraud: $("#hard_fraud").is(":checked"),
                                    inadequate_customer_behavior: $("#inadequate_customer_behavior").is(":checked"),
                                    fair_chargeback_suspecting: $("#fair_chargeback_suspecting").is(":checked"),
                                    order_id: $(this).attr('order_id')
                                },
                                function (data) {
                                    data = JSON.parse(data);
                                    if (data.result) {
                                        $("#ordercondition_update_result").html("Order Condition Changed Successfully");
                                    } else {
                                        $("#ordercondition_update_result").html("Error" + data.error);
                                    }
                                    $("#ordercondition").css('cursor', 'auto');
                                }
                        );
                    });
                    $(".order-mark").click(function () {
                        $("#refreshOrderHistory").html('<div style="font: bold 11px Tahoma;color:#FF6600;line-height:24px;"><img src="' + sImgLoading + '" align="absmiddle"/> Loading...</div>');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "markOrder",
                                    desc: $('#mark_description').val(),
                                    selected_mark: $('#order_mark').val(),
                                    selected_mark_name: $('#order_mark option:selected').text(),
                                    id: $(".order-mark").attr("id")
                                },
                                function (data) {

                                    var new_mark = '<span style="border: none;margin:2px 5px 0px 0px;" class="label label-primary marking_name" publish="N" onclick="action_mark(' + data + ',' + $(".order-mark").attr("id") + ')" id="mark_' + data + '">' + $('#order_mark option:selected').text();
                                    if ($('#mark_description').val() != '') {
                                        new_mark += '<span class="marking_tooltrip" style="display: none;">' + $('#mark_description').val() + '</span>';
                                    }
                                    new_mark += '</span>';
                                    $('.order_mark_list').append(new_mark)

                                    $('button[name=refresh-order-history]').trigger('click');
                                }
                        );
                    });
                    $('.refresh-customer-rating').click(function () {


                        $("#RateHistoryData_new").html('<div style="font: bold 11px Tahoma;color:#FF6600;line-height:24px;"><img src="' + sImgLoading + '" align="absmiddle"/> Loading...</div>');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "RateHistoryRefresh",
                                    email: $(".refresh-customer-rating").attr("email")
                                },
                                function (data) {
                                    $("#RateHistoryData_new").html(data);
                                    var new_rate = jQuery('#RateHistoryData_new').find('td').first().text();
                                    if (new_rate) {
                                        if (new_rate >= 1 && new_rate < 3) {
                                            new_rate = '<span style="color:red">' + new_rate + ' (very bad)</span>';
                                        } else if (new_rate >= 3 && new_rate < 5) {
                                            new_rate = '<span style="color:red">' + new_rate + ' (bad)</span>';
                                        } else if (new_rate >= 5 && new_rate < 6.5) {
                                            new_rate = '<span style="color:yellow">' + new_rate + ' (okay)</span>';
                                        } else if (new_rate >= 6.5 && new_rate < 8) {
                                            new_rate = '<span style="color:green">' + new_rate + ' (good)</span>';
                                        } else if (new_rate >= 8 && new_rate < 9) {
                                            new_rate = '<span style="color:green">' + new_rate + ' (very good)</span>';
                                        } else if (new_rate >= 9 && new_rate <= 10) {
                                            new_rate = '<span style="color:green">' + new_rate + ' (great)</span>';
                                        }
                                        jQuery('#numberRate span').html(new_rate);
                                        $('#userRateingnew').css({'overflow-y': 'auto'})
                                    }else {
                                        jQuery('#numberRate span').html('Not Found');
                                    }
                                }
                        );
                    });
                    $(".send_substitution_text").click(function () {
                        nOrderId = $(this).attr("text_id");
                        $("#success_" + nOrderId).html('<img src="' + sImgLoading + '" align="absmiddle"/> ');
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "send_substitution_text",
                                    id: nOrderId,
                                    user_name: $("input[name='rate_user_name']").val()},
                                function (data) {
                                    if (data == "success") {
                                        $("#success_" + nOrderId).html('SMS Sent Successful');
                                    } else {
                                        $("#success_" + nOrderId).html(data);
                                    }

                                }
                        );
                    });
                    $('select[name=order_status_inside]').change(function () {
                        var order_status = this.value;
                        if (order_status == 'C' || order_status == 'Z' || order_status == 'X' || order_status == 'D') {
                            $("input[name='notify_customer_inside']").prop('checked', true)
                        }
                    });
                    $(".update-status-inside").click(function () {
                        nOrderId = $(this).attr("id");
                        if ($("select[name='order_status_inside']").val() == $("#current_order_status_inside" + nOrderId).val() &&
                                $("select[name='warehouse_inside']").val() == $("#current_warehouse_inside" + nOrderId).val() &&
                                $("select[name='priority_inside']").val() == $("#current_priority_inside" + nOrderId).val() && !jQuery.trim($("textarea[name='order_comment_inside']").val()) && $("select[name='order_status_inside']").val() != 'T') {
                            alert('Please change the Order Status or add Comment first!');
                            ;
                            return;
                        }

                        if ($("input[name='notify_warehouse_inside']").is(":checked")) {
                            bNotifyWarehouseInside = "Y";
                        } else {
                            bNotifyWarehouseInside = "";
                        }

                        if ($("input[name='notify_customer_inside']").is(":checked")) {
                            bNotifyCustomerInside = "Y";
                        } else {
                            bNotifyCustomerInside = "";
                        }

                        if ($("input[name='notify_security_inside']").is(":checked")) {
                            bNotifySecurityInside = "Y";
                        } else {
                            bNotifySecurityInside = "";
                        }

                        if ($("input[name='notify_supervisior_inside']").is(":checked")) {
                            bNotifySupervisiorInside = "Y";
                        } else {
                            bNotifySupervisiorInside = "";
                        }
                        if ($("input[name='notify_recipient_inside']").is(":checked")) {
                            bNotifyRecipientInside = "Y";
                        } else {
                            bNotifyRecipientInside = "";
                        }
                        if ($("input[name='include_comment_inside']").is(":checked")) {
                            bIncludeCommentInside = "Y";
                        } else {
                            bIncludeCommentInside = "0";
                            $("textarea[name='order_comment_inside']").val("");
                        }



                        var encodedString = Base64.encode('order_id|' + nOrderId + '||sender|' + sender + '||warehouse|' + $("select[name='warehouse_inside']").val() + '');
                        encodedString = encodedString.split('').reverse().join('');
                        switch ($("#order_status_inside").val()) {
                            case 'R':
                                refundPopup(nOrderId);
                                break
                            case '6':
                                popupwindow(deliveries_arr.DeTrack.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '7':
                                popupwindow(deliveries_arr.DeTrack.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '4':
                                popupwindow(deliveries_arr.GoPeople.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '5':
                                popupwindow(deliveries_arr.GoPeople.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case 'H':
                                popupwindow(deliveries_arr.MyFastWay.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case 'J':
                                popupwindow(deliveries_arr.MyFastWay.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '16':
                                popupwindow(deliveries_arr.CouriersPlease.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '17':
                                popupwindow(deliveries_arr.CouriersPlease.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '9':
                                popupwindow(deliveries_arr.Optimoroute.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '10':
                                popupwindow(deliveries_arr.Optimoroute.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '8':
                                popupwindow(deliveries_arr.AusPost.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '13':
                                popupwindow(deliveries_arr.AusPost.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case  '15':
                                popupwindow(deliveries_arr.AusPostOnDemand.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '14':
                                popupwindow(deliveries_arr.AusPostOnDemand.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '11':
                                popupwindow(deliveries_arr.PickFleet.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '12': //
                                popupwindow(deliveries_arr.PickFleet.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '19':
                                popupwindow(deliveries_arr.NzPost.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '20': //
                                popupwindow(deliveries_arr.NzPost.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '22':
                                popupwindow(deliveries_arr.AusFast.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '23': //
                                popupwindow(deliveries_arr.AusFast.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '24':
                                popupwindow(deliveries_arr.ShipStation.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '25':
                                popupwindow(deliveries_arr.ShipStation.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            case '27':
                                popupwindow(deliveries_arr.StarTrack.send + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700, nOrderId);
                                break
                            case '28':
                                popupwindow(deliveries_arr.StarTrack.cancel + '&order_id=' + nOrderId + '&warehouse=' + $("select[name='warehouse_inside']").val(), 'Void Shipment', 900, 700);
                                break
                            default:
                                var formData = new FormData();
                                var images = new Array();
                                var issetUploadedImages = false;
                                if ($('#fileUploadHistory')[0].files.length > 0) {
                                    $.each($('#fileUploadHistory')[0].files, function (key, value) {
                                        images.push(value);
                                        formData.append('images[]', value);
                                        issetUploadedImages = true;
                                    });
                                }

                                formData.append('option', 'com_virtuemart');
                                formData.append('func', 'orderStatusSet');
                                formData.append('page', 'order.order_list');
                                formData.append('order_id', $(this).attr("id"));
                                formData.append('notify_warehouse', bNotifyWarehouseInside);
                                formData.append('notify_customer', bNotifyCustomerInside);
                                formData.append('notify_security', bNotifySecurityInside);
                                formData.append('notify_recipient', bNotifyRecipientInside);
                                formData.append('notify_supervisior', bNotifySupervisiorInside);
                                formData.append('include_comment', bIncludeCommentInside);
                                formData.append('ajax_action', '1');
                                formData.append('order_comment', $("textarea[name='order_comment_inside']").val());
                                formData.append('priority', $("select[name='priority_inside']").val());
                                formData.append('current_priority', $("input[name='current_priority_inside']").val());
                                formData.append('warehouse', $("select[name='warehouse_inside']").val());
                                formData.append('current_warehouse', $("input[name='current_warehouse_inside']").val());
                                formData.append('order_status', $("select[name='order_status_inside']").val());
                                formData.append('current_order_status', $("input[name='current_order_status_inside']").val());
                                formData.append('user_name', $("input[name='rate_user_name']").val());
                                $("#updateOrderStatusReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
                                $("#updateOrderStatusReport").css("display", "block");
                                $.ajax({
                                    url: 'index2.php',
                                    data: formData,
                                    type: 'POST',
                                    contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                                    processData: false, // NEEDED, DON'T OMIT THIS
                                    // ... Other options like success and etc
                                }).done(function (data) {
                                    if (trim(data) == "-success") {

                                        $("#updateOrderStatusReport").html("Update Successful.");
                                        $("textarea[name='order_comment_inside']").val("");
                                        $("#current_priority_inside" + nOrderId).val($("select[name='priority_inside']").val());
                                        $("#current_warehouse_inside" + nOrderId).val($("select[name='warehouse_inside']").val());
                                        $("#current_order_status_inside" + nOrderId).val($("select[name='order_status_inside']").val());
                                        $("#order_status" + orderNumber).selectOptions($("select[name='order_status_inside']").val(), true);
                                        $("#warehouse" + orderNumber).selectOptions($("select[name='warehouse_inside']").val(), true);
                                        $("#priority" + orderNumber).selectOptions($("select[name='priority_inside']").val(), true);
                                    } else {
                                        $("#updateOrderStatusReport").html("Update Wrong.");
                                    }
                                    $("#order_status_code_" + nOrderId).val($("select[name='order_status_inside']").val())
                                    if (issetUploadedImages) {
                                        $("#upload-button_" + nOrderId).submit()
                                    }

                                }
                                );
                                break
                        }

                    });
                    $(".update-card-message").click(function () {
                        $("#updateCardMessageReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
                        $("#updateCardMessageReport").css("display", "block");
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateCardMessage",
                                    customer_note: $("textarea[name='order_customer_note']").val(),
                                    customer_signature: $("textarea[name='order_customer_signature']").val(),
                                    order_id: $(this).attr("id")
                                },
                                function (data) {
                                    //alert("Data Loaded: " + data);
                                    if (data == "success") {
                                        $("#updateCardMessageReport").html("Update Successful.");
                                    } else {
                                        $("#updateCardMessageReport").html("Update Wrong.");
                                    }
                                }
                        );
                    });
                    $(".update-special-instructions").click(function () {
                        $("#updateSpecialInstructionsReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
                        $("#updateSpecialInstructionsReport").css("display", "block");
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateSpecialInstructions",
                                    customer_comments: $("textarea[name='order_customer_comments']").val(),
                                    order_id: $(this).attr("id")
                                },
                                function (data) {
                                    //alert("Data Loaded: " + data);
                                    if (data == "success") {
                                        $("#updateSpecialInstructionsReport").html("Update Successful.");
                                    } else {
                                        $("#updateSpecialInstructionsReport").html("Update Wrong.");
                                    }
                                }
                        );
                    });
                    $(".update-coupon-discount").click(function () {
                        orderID = $(this).attr("id");
                        if (!isValidNumberic($("input[name='order_coupon_discount']").val())) {
                            alert("Invalid coupon discount, please enter again!");
                            return;
                        }

                        $("#updateOrderReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Update Order Coupon Discount...');
                        $("#updateOrderReport").css("display", "block");
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateCouponDiscount",
                                    order_coupon_discount: $("input[name='order_coupon_discount']").val(),
                                    order_id: orderID
                                },
                                function (data) {
                                    //alert("Data Loaded: " + data);
                                    if (data == "success") {
                                        $("#updateOrderReport").html("Update Order Coupon Discount Successful.");
                                        loadOrderItemDetail(orderID);
                                    } else {
                                        $("#updateOrderReport").html("Update Order Coupon Discount Wrong.");
                                    }
                                }
                        );
                    });
                    $(".update-discount").click(function () {
                        orderID = $(this).attr("id");
                        if (!isValidNumberic($("input[name='order_discount']").val())) {
                            alert("Invalid discount, please enter again!");
                            return;
                        }

                        $("#updateOrderReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Update Order Discount...');
                        $("#updateOrderReport").css("display", "block");
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateDiscount",
                                    order_discount: $("input[name='order_discount']").val(),
                                    order_id: orderID
                                },
                                function (data) {
                                    //alert("Data Loaded: " + data);
                                    if (data == "success") {
                                        $("#updateOrderReport").html("Update Order Discount Successful.");
                                        loadOrderItemDetail(orderID);
                                    } else {
                                        $("#updateOrderReport").html("Update Order Discount Wrong.");
                                    }
                                }
                        );
                    });
                    $(".update-standard-shipping").click(function () {
                        orderID = $(this).attr("id");
                        $("#updateOrderReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Update Standard Shipping...');
                        $("#updateOrderReport").css("display", "block");
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "updateStandardShipping",
                                    standard_shipping: $("select[name='standard_shipping']").val(),
                                    order_id: orderID
                                },
                                function (data) {
                                    //alert("Data Loaded: " + data);
                                    if (data == "success") {
                                        $("#updateOrderReport").html("Update Standard Shipping Successful.");
                                        loadOrderItemDetail(orderID);
                                    } else {
                                        $("#updateOrderReport").html("Update Standard Shipping Wrong.");
                                    }
                                }
                        );
                    });
                    $(".add-product").click(function () {
                        orderID = $(this).attr("id");
                        if ($("select[name='add_product_id']").val() <= 0) {
                            alert("Please choose a product!");
                            return;
                        }


                        if (!isValidInteger($("input[name='add_order_item_quantity']").val())) {
                            alert("Invalid quantity, please enter again!");
                            return;
                        }

                        $("#updateOrderReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Add Product Item...');
                        $("#updateOrderReport").css("display", "block");
                        $.post("index2.php",
                                {option: "com_ajaxorder",
                                    task: "addProductItem",
                                    add_product_id: $("select[name='add_product_id']").val(),
                                    add_product_quantity: $("input[name='add_order_item_quantity']").val(),
                                    order_id: orderID,
                                    select_bouquet: $("select[name='select_bouquet']").val(),
                                },
                                function (data) {
                                    // alert("Data Loaded: " + data);
                                    if (data == "success") {
                                        $("#updateOrderReport").html("Add Product Item Successful.");
                                        loadOrderItemDetail(orderID);
                                        loadOrderCart(orderID);
                                    } else {
                                        $("#updateOrderReport").html("Add Product Item Wrong.");
                                    }
                                }
                        );
                    });
                    $(".update-quantity").click(function () {
                        aVar = $(this).attr("id").split("[----]");
                        if (!isValidInteger($("input[name='order_item_quantity" + aVar[0] + "']").val())) {
                            alert("Invalid quantity, please enter again!");
                            return;
                        }

                        updateOrderItemQuantity($(this).attr("id"), $("input[name='order_item_quantity" + aVar[0] + "']").val());
                    });
                    $(".delete-order-item").click(function () {

                        if (!confirm("Do you want to remove this product item?")) {
                            return;
                        }

                        deleteOrderItem($(this).attr("id"));
                    });
                    // $('.refresh-customer-rating').trigger('click')
                }

                $('html, body').animate({
                    scrollTop: $(".order-header").offset().top
                }, 1000);
            });
});
//======================================================================================

function update_status(id, price,comment='') {


    $("#ajaxaction" + id).css("display", "none");
    $("#ajaxloader" + id).html('<img src="' + sImgLoading + '" align="absmiddle"/> Updating...');
    $("#ajaxloader" + id).css("display", "block");
    $.post("index2.php",
            {option: "com_virtuemart",
                func: "orderStatusSet",
                page: "order.order_list",
                order_id: $("#order_id" + id).val(),
                notify_warehouse: $("#notify_warehouse" + id).val(),
                notify_recipient: $("#notify_recipient" + id).val(),
                notify_customer: $("#notify_customer" + id).val(),
                notify_supervisior: $("#notify_supervisior" + id).val(),
                priority: $("#priority" + id).val(),
                rel: id,
                current_priority: $("#current_priority" + id).val(),
                warehouse: $("#warehouse" + id).val(),
                color: $("#color" + id).val(),
                current_warehouse: $("#current_warehouse" + id).val(),
                order_status: $("#order_status" + id).val(),
                current_order_status: $("#current_order_status" + id).val(),
                current_partner: $("#partner" + id).val(),
                order_comment: comment,
                partner_price: price,
                ajax_action: "1",
                user_name: $("input[name='rate_user_name']").val()},
            function (data) {
                //alert("Data Loaded: " + data);
                //$("#ajaxloader"+id).css("display", "none");
                $("#ajaxloader" + id).css("display", "none");
                $("#ajaxaction" + id).css("display", "block");
                $("#ajaxresult" + id).css("display", "block");
                if (data.indexOf("success") != -1) {
                    $("#ajaxresult" + id).html("Update Successful.");
                    var arr = data.split("-");
                    id = arr[0];
                    $(".notify_customer").attr("checked", false);
                    $(".notify_warehouse").attr("checked", false);
                    $(".notify_supervisior").attr("checked", false);
                    $(".notify_recipient").attr("checked", false);
                    $("#notify_customer" + id).val("N");
                    $("#current_priority" + id).val($("#priority" + id).val());
                    $("#current_warehouse" + id).val($("#warehouse" + id).val());
                    $("#current_order_status" + id).val($("#order_status" + id).val());
                    $(".order_list_status_" + $("#order_id" + id).val()).selectOptions($("#order_status" + id).val(), true);
                } else {
                    $("#ajaxresult" + id).html(data);
                }
            }
    );
}
function popupwindow(url, title, w, h, order_id = '') {
    if (order_id) {
        var has_active_shipment_less_one_day = false;
        $.ajax({
            url: 'index2.php',
            type: 'POST',
            async: false,
            dataType: 'json',
            order_id: order_id,
            data: {
                option: 'com_ajaxorder',
                task: 'CheckOldDeliveries',
                order_id: order_id
            },
            success: function (data)
            {
                if (data.result) {
                    return has_active_shipment_less_one_day = true;
                }
            }
        });
        if (has_active_shipment_less_one_day) {
            $('.delivery_icon_span_' + order_id).addClass('hidden');
            alert('This order already has active shipment. Please cancel it');
            return false;
        }
    }
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}
$(".update-status").click(function () {
    id = $(this).attr("id");
    var encodedString = Base64.encode('order_id|' + $("#order_id" + id).val() + '||sender|' + sender + '||warehouse|' + $("#warehouse" + id).val() + '');
    encodedString = encodedString.split('').reverse().join('');
    console.log(id, $("#warehouse" + id), $("#order_id" + id));
    switch ($("#order_status" + id).val()) {
        case 'R':
            refundPopup($("#order_id" + id).val(),id);
            break
        case '6':
            popupwindow(deliveries_arr.DeTrack.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '7':
            popupwindow(deliveries_arr.DeTrack.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '4':
            popupwindow(deliveries_arr.GoPeople.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '5':
            popupwindow(deliveries_arr.GoPeople.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '9':
            popupwindow(deliveries_arr.Optimoroute.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '10':
            popupwindow(deliveries_arr.Optimoroute.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '15':
            popupwindow(deliveries_arr.AusPostOnDemand.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '14':
            popupwindow(deliveries_arr.AusPostOnDemand.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '8':
            popupwindow(deliveries_arr.AusPost.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '13':
            popupwindow(deliveries_arr.AusPost.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '11':
            popupwindow(deliveries_arr.PickFleet.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '12':
            popupwindow(deliveries_arr.PickFleet.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '19':
            popupwindow(deliveries_arr.NzPost.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '20':
            popupwindow(deliveries_arr.NzPost.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '22':
            popupwindow(deliveries_arr.AusFast.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '23':
            popupwindow(deliveries_arr.AusFast.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '24':
            popupwindow(deliveries_arr.ShipStation.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '25':
            popupwindow(deliveries_arr.ShipStation.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '27':
            popupwindow(deliveries_arr.StarTrack.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '28':
            popupwindow(deliveries_arr.StarTrack.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case 'T': // Send to Partner
            $("#modalDiv").css('display', 'block');
            var block = document.getElementById("modalDiv");
            block.style.display = "block";
            block.style.top = Math.floor(getClientHeight() / 2 + getBodyScrollTop()) + "px";
            block.style.left = Math.floor(getClientWidth() / 2 + getBodyScrollLeft()) + "px";
            block.style.margin = "-" + Math.floor(200 / 2) + "px 0px 0px -" + Math.floor(300 / 2) + "px";
            var option_id = $("#partner" + id).val();
            var percent = $("#partner_price option[value='" + option_id + "']").text();
            var price = $("#order_total_forpartner" + id).val();
            var price_in = price * percent / 100;
            $("#price_partner").val(Number(price_in).toFixed(2));
            $("#pRec").val(percent);
            $("#pRec").attr('checked', 'checked');
            $("#id_update").val(id);
            $("#recomenden_val").html("Recomended for this partner is " + percent + "%");
            break
        case 'H':
            popupwindow(deliveries_arr.MyFastWay.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case 'J': //
            popupwindow(deliveries_arr.MyFastWay.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        case '16':
            popupwindow(deliveries_arr.CouriersPlease.send + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700, $("#order_id" + id).val());
            break
        case '17': //
            popupwindow(deliveries_arr.CouriersPlease.cancel + '&order_id=' + $("#order_id" + id).val() + '&warehouse=' + $("#warehouse" + id).val(), 'Void Shipment', 900, 700);
            break
        default:
            update_status(id, 0);
    }
});
function SetPartner_price(percent_off) {
    var id = $("#id_update").val();
    var percent = 0;
    if (percent_off == 100) {
        percent = $("#partner_price option[value='" + $("#partner" + id).val() + "']").text();
    } else
        percent = percent_off;
    var price = $("#order_total_forpartner" + id).val();
    var price_in = price * percent / 100;
    $("#price_partner").val(Number(price_in).toFixed(2)); //Number(x).toFixed(2)
}
function SavePartner() {
    var id = $("#id_update").val();
    var price = $("#price_partner").val();
    $("#modalDiv").css('display', 'none');
    update_status(id, price);
}


//======================================================================================
$(".notify_customer").click(function () {
    id = $(this).attr("id");
    if ($(this).is(":checked") == true) {
        $("#notify_customer" + id).val("Y");
    } else {
        $("#notify_customer" + id).val("N");
    }

    /*alert($("#notify_customer"+id).val());*/
});
$(".notify_warehouse").click(function () {
    id = $(this).attr("id");
    if ($(this).is(":checked") == true) {
        $("#notify_warehouse" + id).val("Y");
    } else {
        $("#notify_warehouse" + id).val("N");
    }
});
$(".notify_recipient").click(function () {
    id = $(this).attr("id");
    if ($(this).is(":checked") == true) {
        $("#notify_recipient" + id).val("Y");
    } else {
        $("#notify_recipient" + id).val("N");
    }
});
$(".notify_supervisior").click(function () {
    id = $(this).attr("id");
    if ($(this).is(":checked") == true) {
        $("#notify_supervisior" + id).val("Y");
    } else {
        $("#notify_supervisior" + id).val("N");
    }
});
//======================================================================================


function update_orders_statuses() {
    var orders = jQuery(".update_orders_statuses").attr('data');
    var selected_status = jQuery("#status_list").val();
    if (selected_status == 'none') {
        alert('please select status')
        return false;
    }
    if (orders != '') {
        var orders_a = new Array;
        orders_a = orders.split(",");
        orders_a.forEach(function (order_id) {

            var rel = jQuery("#" + order_id).attr('rel')
            jQuery("#order_status" + rel).val(selected_status)
            jQuery("#notify_customer" + rel).val("Y");
            jQuery("#ajaxaction" + rel + " .update-status").click()

        });
    }
}
function send_orders_to_PeoplePost() {
    var orders = jQuery(".send_orders_to_PeoplePost").attr('data');
    if (orders != '') {
        window.open("genereate_PeoplePost.php?orders=" + orders, '_blank');
    }
}
function send_orders_to_ShipStation() {
    var orders = jQuery(".send_orders_to_ShipStation").attr('data');
    var encodedString = Base64.encode('order_id|' + orders + '||sender|' + sender + '||delivery_id|12');
    encodedString = encodedString.split('').reverse().join('');
    if (orders != '') {
        popupwindow('/scripts/deliveries/shipstation/index.php?data=' + encodedString, 'Create Shipment', 900, 700);
    }
}
function send_orders_to_AusPost() {
    var orders = jQuery(".send_orders_to_AusPost").attr('data');
    var encodedString = Base64.encode('order_id|' + orders + '||sender|' + sender + '');
    encodedString = encodedString.split('').reverse().join('');
    if (orders != '') {
        popupwindow('/scripts/deliveries/auspost/CreateShipment.php?data=' + encodedString, 'Create Shipment', 900, 700);
    }
}
function download_orders_list() {
    var orders = jQuery(".download_orders_list").attr('data');
    jQuery(".download_orders_list").attr('disabled',true);
    $.ajax({
        data:
            {
                option: 'com_virtuemart',
                task: 'download_orders_list',
                page: 'order.order_list',
                orders: orders
            },
        url: "index2.php",
        async: true,
        cache: false,
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = 'orders_list.xlsx';
            a.click();
            window.URL.revokeObjectURL(url);
            jQuery(".download_orders_list").removeAttr('disabled')
        }
    });
}

function send_orders_to_AusPostOnDemand() {
    var orders = jQuery(".send_orders_to_AusPostOnDemand").attr('data');
    console.log(orders);
    var encodedString = Base64.encode('order_id|' + orders + '||sender|' + sender + '');
    encodedString = encodedString.split('').reverse().join('');
    if (orders != '') {
        popupwindow('/scripts/deliveries/auspost_ondemand/CreateShipment.php?data=' + encodedString, 'Create Shipment', 900, 700);
    }
}
//======================================================================================
function updateOrderItemQuantity(item_id, order_item_quantity) {
    aVar = item_id.split("[----]");
    if (!item_id) {
        return;
    }

    $("#updateOrderReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Update Quantity...');
    $("#updateOrderReport").css("display", "block");
    $.post("index2.php",
            {option: "com_ajaxorder",
                task: "updateQuantity",
                order_item_quantity: order_item_quantity,
                item_id: item_id
            },
            function (data) {
                //alert("Data Loaded: " + data);
                if (data == "success") {
                    $("#updateOrderReport").html("Update Quantity Successful.");
                    loadOrderItemDetail(aVar[1]);
                } else {
                    $("#updateOrderReport").html("Update Quantity Wrong.");
                }
            }
    );
}


//======================================================================================
function deleteOrderItem(item_id) {
    aVar = item_id.split("[----]");
    if (!item_id) {
        return;
    }

    $("#updateOrderReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Deleting Order Product Item...');
    $("#updateOrderReport").css("display", "block");
    $.post("index2.php",
            {option: "com_ajaxorder",
                task: "deleteOrderItem",
                item_id: item_id
            },
            function (data) {
                //alert("Data Loaded: " + data);
                if (data == "success") {
                    $("#updateOrderReport").html("Delete Order Product Item Successful.");
                    loadOrderItemDetail(aVar[1]);
                    loadOrderCart(aVar[1]);
                } else {
                    $("#updateOrderReport").html("Delete Order Product Item Wrong.");
                }
            }
    );
}


//======================================================================================
function loadOrderItemDetail(order_id) {
    if (!order_id) {
        return;
    }

    $("#loadOrderItemDetailReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Loading...');
    $("#loadOrderItemDetailReport").css("display", "block");
    $.post("index2.php",
            {option: "com_ajaxorder",
                task: "loadOrderItemDetail",
                order_id: order_id
            },
            function (data) {
                aData = data.split('[==1==]');
                $("#loadOrderItemDetail").html(aData[0]);
                $("#total_for_order_" + order_id).html('<b>' + aData[1] + '</b>');
                $("#loadOrderItemDetailReport").css("display", "none");
            }
    );
}


//======================================================================================
function loadOrderCart(order_id) {
    if (!order_id) {
        return;
    }

    $("#loadOrderCartDetailReport").html('<img src="' + sImgLoading + '" align="absmiddle"/> Loading...');
    $("#loadOrderCartDetailReport").css("display", "block");
    $.post("index2.php",
            {option: "com_ajaxorder",
                task: "loadOrderCart",
                order_id: order_id
            },
            function (data) {
                $("#loadOrderCartDetail").html(data);
                $("#loadOrderCartDetailReport").css("display", "none");
                //======================================================================================
                $(".update-quantity2").click(function () {
                    aVar = $(this).attr("id").split("[----]");
                    if (!isValidInteger($("input[name='order_item_quantity" + aVar[0] + "']").val())) {
                        alert("Invalid quantity, please enter again!");
                        return;
                    }

                    updateOrderItemQuantity($(this).attr("id"), $("input[name='order_item_quantity" + aVar[0] + "']").val());
                });
                //======================================================================================
                $(".delete-order-item2").click(function () {

                    if (!confirm("Do you want to remove this product item?")) {
                        return;
                    }

                    deleteOrderItem($(this).attr("id"));
                });
            }
    );
}

function changeBillingState(selected_value) {
    $("#bill_state_container").html('Loading...');
    $.post("index2.php",
            {option: "com_phoneorder",
                task: "getsate",
                selector_id: "bill_state",
                country_id: selected_value
            },
            function (data) {
                if (data != "error") {
                    $("#bill_state_container").html(data);
                } else {
                    $("#bill_state_container").html("There aren't any states of this country. Please chose other one!");
                }
            }
    );
}


function changeShippingState(selected_value) {
    $("#deliver_state_container").html('Loading...');
    $.post("index2.php",
            {option: "com_phoneorder",
                task: "getsate",
                selector_id: "deliver_state",
                country_id: selected_value
            },
            function (data) {
                if (data != "error") {
                    $("#deliver_state_container").html(data);
                } else {
                    $("#deliver_state_container").html("There aren't any states of this country. Please chose other one!");
                }
            }
    );
}

//======================================================================================
function isValidNumberic(strValue) {
    var objRegExp = /(^\d+[\.]?\d*$)/;
    return objRegExp.test(strValue);
}

function isValidInteger(strValue) {
    var objRegExp = /(^\d+$)/;
    return objRegExp.test(strValue);
}

function isValidZipCode(value) {
    var re = /^[A-Za-z0-9\s]{4,5}$/;
    return (re.test(value));
}
function getBodyScrollTop()
{
    return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
}

function getBodyScrollLeft()
{
    return self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft);
}

function getClientWidth()
{
    return document.compatMode == 'CSS1Compat' && !window.opera ? document.documentElement.clientWidth : document.body.clientWidth;
}

function getClientHeight()
{
    return document.compatMode == 'CSS1Compat' && !window.opera ? document.documentElement.clientHeight : document.body.clientHeight;
}

function updateddate(orderID)
{
    $('#ddate_loading').html('Loading...');
    var newddate = $("#ddate_" + orderID).val();
    if ($("#notify_warehouse_inside_" + orderID).prop("checked") == true)
    {
        var notify_warehouse_inside = 1;
    } else
    {
        var notify_warehouse_inside = 0;
    }

    if ($("#notify_customer_inside_" + orderID).prop("checked") == true)
    {
        var notify_customer_inside = 1;
    } else
    {
        var notify_customer_inside = 0;
    }

    $.ajax({
        url: 'index2.php',
        type: "POST",
        data: {option: 'com_ajaxorder', task: 'updateDDate', order_id: orderID, notify_warehouse: notify_warehouse_inside, notify_customer: notify_customer_inside, ajax_action: 1, ddate: newddate},
        success: function (data)
        {
            //console.log(data);
            $('#ddate_list_' + orderID).html(data);
            $('#ddate_loading').html('Success');
        },
        dataType: 'html'
    });
}

function UpdateColor(i, order_id)
{
    $('#color_loader_' + i).show();
    $.ajax({
        url: 'index2.php',
        type: "POST",
        data: {option: 'com_ajaxorder', task: 'updateColor', order_id: order_id, color: $('#color' + i).val()},
        success: function (data)
        {
            $('#color_loader_' + i).hide();
            $('#color_ok_' + i).show();
            setTimeout(function () {
                $('#color_ok_' + i).hide()
            }, 3000);
        },
        dataType: 'html'
    });
}

         
    function ClearFilters(){
              
    $('input[name="order_id_filter"]').val('');
    $('input[name="nz_order_id"]').val('');
    $('input[name="partner_order_id"]').val('');
    $('input[name="product_sku_filter"]').val('');
    $('input[name="customer_name_filter"]').val('');
    $('input[name="user_email_filter"]').val('');
    $('input[name="phonenumber_filter"]').val('');
    $('#order_created1').val('');
    $('#delivery_date_from').val('');
    $('#delivery_date_to').val('');
    $('#warehouse_filter').val('');
    $('#shipping_province_filter').val('');
    $('select[name="filter_condition"]').val('');
    document.adminForm.show.value = '';
    //document.adminForm.limitstart.value = '0';
    document.adminForm.submit();
          }



function createAndPrint(order_id, carrier) {
    if (!checkDeliveryExist(order_id, carrier)) {
        $.ajax({
            url: 'index2.php',
            type: 'POST',
            dataType: 'json',
            order_id: order_id,
            data: {
                option: 'com_ajaxorder',
                task: 'sendOrderToCarrier',
                order_id: order_id,
                carrier: carrier
            },
            success: function (data) {
                if (data.success) {
                    // runSpinner();
                    // createCarriersButtonsForDelete(order_id, data.shipment_id);
                    // printOrder(data.shipment_id,carrier,order_id);
                    // Swal.close();
                    let options = {
                        title: 'Shipment for order '+order_id,
                        text: data.shipment,
                        icon: 'success',
                        showCancelButton: true,
                        showDenyButton: true,
                        denyButtonText: 'Cancel label',
                        confirmButtonText: "Print",
                        cancelButtonText: "Close",
                    };
                    Swal.fire(options).then((result) => {
                        if (result.isConfirmed) {
                            printOrder(data.shipment_id, carrier, order_id);
                        } else if (result.isDenied) {
                            runSpinner();
                            cancelDelivery(data.shipment_id, carrier, order_id)
                        }
                    });
                    $('.order_list_status_'+order_id).val(status_sent_nzpost)
                } else {
                    Swal.fire({
                        title: 'Creating label error',
                        text:  data.error,
                        icon: 'error',
                        showConfirmButton: true
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: 'Creating label error. Please use the standard delivery company button to see the error message.',
                    icon: 'error',
                    showConfirmButton: true
                });
                console.error("AJAX request failed:", status, error);
            }
        });
    }
}

function createCarriersButtonsForDelete(order_id, shipment_id, carrier) {
    let cancelLink = getCancelLink(carrier);
    let linkSelector = 'a[order_id="' + order_id + '"]';
    let linkElement = document.querySelector(linkSelector);

    if (linkElement) {
        linkElement.style.backgroundColor = 'white';
        linkElement.setAttribute('href', '#');
        linkElement.setAttribute('target', '_blank');
        linkElement.setAttribute('title', shipment_id + ' Click to cancel.');
        linkElement.setAttribute('onclick', `confirmDeletion('${cancelLink}?order_id=${order_id}&shipment_id=${shipment_id}',${order_id}); return false;`);
        linkElement.classList.remove('default');

    } else {
        console.log('Element with order_id ' + order_id + ' not found.');
    }
}


function checkDeliveryExist(order_id, carrier) {
    let inDelivery = false;
    $.ajax({
        url: 'index2.php',
        type: 'POST',
        async: false,
        dataType: 'json',
        order_id: order_id,
        data: {
            option: 'com_ajaxorder',
            task: 'checkOrderHasActiveDelivery',
            order_id: order_id
        },
        success: function (data) {
            if (data.result === true) {
                let options = {
                    title: 'The order already has a label!',
                    text: 'Order ' + order_id + ' already has a label from ' + carrier,
                    icon: 'success',
                    showCancelButton: true,
                    showDenyButton: true,
                    denyButtonText: 'Cancel label',
                    confirmButtonText: "Print",
                    cancelButtonText: "Close",
                };

                if (data.diffDays >= 1) {
                    options.denyButtonText = 'Create New'
                    options.title = `Old label created: ${data.created}`
                }

                Swal.fire(options).then((result) => {
                    if (result.isConfirmed) {
                    printOrder(data.shipment_id,carrier,order_id);
                } else if (result.isDenied && data.diffDays >= 1) {
                    runSpinner();
                    cancelDeliveryAndCreateNew(data.shipment_id, carrier, order_id)
                } else if (result.isDenied) {
                    runSpinner();
                    cancelDelivery(data.shipment_id, carrier, order_id)
                }
            });
                inDelivery = true;
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX request failed:", status, error);
        }
    });
    return inDelivery;
}

function cancelDeliveryAndCreateNew(shipment_id, carrier, order_id){
    let cancelLink = getCancelLink(carrier);
    let link = `${cancelLink}?order_id=${order_id}&shipment_id=${shipment_id}`;
    $.ajax({
        url: link,
        type: "POST",
        success: function(response) {
            Swal.close();
            if (response.error) {
                Swal.fire({
                    title: "Error",
                    text: response.message,
                    icon: "error"
                });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({
                title: "Error",
                text: "An error occurred while deleting the order.",
                icon: "error"
            });
        }
    });
    createAndPrint(order_id, carrier);
}



function getCancelLink(carrier) {
    let link = '';
    $.ajax({
        url: 'index2.php',
        type: 'POST',
        async: false,
        dataType: 'json',
        carrier: carrier,
        data: {
            option: 'com_ajaxorder',
            task: 'getCancelLink',
            carrier: carrier
        },
        success: function (data) {
            if (data.result === true) {
                link = data.cancel_link;
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX request failed:", status, error);
        }
    });
    return link;
}

document.querySelectorAll('.button-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        let orderId = this.getAttribute('data-order-id');
        let action = this.getAttribute('data-action');

        runSpinner();
        createAndPrint(orderId, action)
    });
});

function printOrder(shipment_id,carrier,order_id) {
    runSpinner();
    $.ajax({
        url: 'index2.php',
        type: "POST",
        data: {
            option: 'com_ajaxorder',
            task: 'printShipmentLabel',
            carrier: carrier,
            order_id: order_id,
            shipment_id: shipment_id
        },
        cache: false,
        xhr: function () {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 2) {
                    if (xhr.status == 200) {
                        xhr.responseType = "blob";
                    } else {
                        xhr.responseType = "text";
                    }
                }
            };
            return xhr;
        },
        success: function(data) {
            Swal.close();
            if (data) {

                //Convert the Byte Data to BLOB object.
                var blob = new Blob([data], { type: "application/octetstream" });

                //Check the Browser type and download the File.
                var isIE = false || !!document.documentMode;
                if (isIE) {
                    window.navigator.msSaveBlob(blob, fileName);
                } else {
                    var url = window.URL || window.webkitURL;
                    link = url.createObjectURL(blob);
                    var a = $("<a />");
                    a.attr("download", 'label.pdf');
                    a.attr("href", link);
                    $("body").append(a);
                    a[0].click();
                    $("body").remove(a);
                }


            } else {

                Swal.fire({
                    title: "Error",
                    text: "An error occurred while printing label.",
                    icon: "error"
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX request failed:", status, error);
        }
    });

}


function confirmDeletion(cancelLink,order_id) {

    Swal.fire({
        title: "Cancel label?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        didOpen: () => {
        Swal.getConfirmButton().addEventListener("click", () => {
        runSpinner();
    $.ajax({
        url: cancelLink,
        type: "POST",
        success: function(response) {
            Swal.close();
            if (response.error) {
                Swal.fire({
                    title: "Error",
                    text: response.message,
                    icon: "error"
                });
            } else {
                Swal.fire({
                    title: "Label has been cancelled.",
                    text: response.message,
                    icon: "success",
                    timer: 2000
            });
                $('.order_list_status_'+order_id).val(status_cancel_nzpost)
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({
                title: "Error",
                text: "An error occurred while deleting the order.",
                icon: "error"
            });
        }
    });
});
},
    allowOutsideClick: () => !Swal.isLoading()
});
}

function cancelDelivery(shipment_id, carrier, order_id){
    let cancelLink = getCancelLink(carrier);
    let link = `${cancelLink}?order_id=${order_id}&shipment_id=${shipment_id}`
    confirmDeletion(link,order_id);

}

function runSpinner(){
    Swal.fire({
        title: 'Loading...',
        html: '<div class="loading-spinner"></div>',
        allowEscapeKey: false,
        allowOutsideClick: false,
        showConfirmButton: false,
    });
}
