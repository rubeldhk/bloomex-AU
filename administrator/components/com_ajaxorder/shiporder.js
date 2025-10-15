

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

    if (document.adminForm.order_id.value == "") {
        alert("Please enter your order id first.");
        return false;
    }

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
                buildSelectBox(data.warehouse_id);
                document.adminForm.driver_option_type.value = data.driver_option_type;
                checkOption(data.driver_option_type);
                document.getElementById("courier_tracking_number").value = data.tracking_number

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
    removeID = document.adminForm.removeID.value;
    order_id = document.getElementById('order_id').value;
    drivers_name_and_telephone_number = document.getElementById('bloomex_driver_drivers_name_and_telephone_number').value;
    telephone_or_email = document.getElementById('local_driver_telephone_or_email').value;
    telephone_or_email2 = document.getElementById('courier_telephone_or_email').value;
    tracking_number = document.getElementById('courier_tracking_number').value;
    driver_option_type = document.getElementById('driver_option_type').value;
    warehouse_id = document.getElementById('warehouse_id').value;
    option = 'com_ajaxorder'; //document.getElementById('option').value;
    task = 'shipOrder'; //document.getElementById('task').value;

    removeID = document.getElementById('removeID').value;
    confirm = ""; //document.getElementById('confirm').value;
    ajax_post_search = document.getElementById('ajax_post_search').value = 'true';
    $(function () {
        $.post("index2.php", {
            removeID: removeID,
            ajax_post_search: ajax_post_search,
            option: option,
            task: task,
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
                    address = warehouse22;
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
                    } else
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
    option = 'com_ajaxorder'; //document.getElementById('option').value;
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
                    } else {
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


function TrimString(sInString) {
    sInString = sInString.replace(/^\s+/g, ""); // strip leading
    return sInString.replace(/\s+$/g, ""); // strip trailing
}


