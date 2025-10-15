<?php
defined('_VALID_MOS') or die('Restricted access');

class HTML_ShipOrder {

    static function defaultview($return_a) {
        mosCommonHTML::loadBootstrap();
        ?>
        <style>
            #map_canvas {
                height: 100%;
                display: block;
                /*float: center;*/
                /*width: 1000px;*/
            }    
            .table {
                width: 50%;
            }
            .table-nonfluid {
                width: auto !important;
            }
            .table.table-striped {
                width: 100%;
            }
            #table_info td {
                vertical-align: middle;
            }
            .table td{
                font-size: 14px !important;
            }
            .left_info {
                float: left;
                width: 435px;
                /*height: 500px;*/
            }
            .right_info {
                width: 100%;
                height: 500px;
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
            #noreturn_address {
                display: none;
            }
            td.th_delete button {
                margin-right: 5px;
            }
            /*
            @media print {
                body * { visibility: hidden; }
                .right_info * { visibility: visible; }
                #map_canvas {visibility: visible;position:absolute; top: 5px; left: 5px;}
            }*/

            @media print {
                body>* {
                    display: none;
                }
            }
        </style>

        <script type="text/javascript">
            var a_driver_options = {};
            var labelIndex = 0;
            var last_order = [];
            var last_order_id = [];
            var alphabet = 'BCDEFGHIJKLMNOPQRSTUVWXYZ';
            var a_orders_info = [];
                        
            $(document).ready(function ()
            {
                a_warehouse_address = [];

        <?php
        foreach ($return_a['warehouse_address'] as $warehouse_id => $warehouse_address) {
            ?>
                    a_warehouse_address[<?php echo $warehouse_id; ?>] = '<?php echo $warehouse_address; ?>';
            <?php
        }
        ?>

        <?php
        foreach ($return_a['driver_options'] as $warehouse_id => $drivers_options) {
            ?>

                    a_driver_options[<?php echo $warehouse_id; ?>] = {};

            <?php
            $driver_option_all = array();

            foreach ($drivers_options as $driver_option) {
                $driver_option_all[] = "" . (int) $driver_option['driver_id'] . "|" . htmlspecialchars($driver_option['title']) . "|" . htmlspecialchars($driver_option['description']) . "";
            }
            ?>
                    a_driver_options[<?php echo $warehouse_id; ?>] = ["<?php echo implode('" , "', $driver_option_all); ?>"];
            <?php
        }
        ?>

                $('#warehouse_id').change(function ()
                {
                    $('#last_order').attr('checked', false);
                    
                    if (markers['W']) {
                        markers['W'].setMap(null);
                    }

                    localStorage.setItem('warehouse_id', $(this).val());

                    $('#driver_select').find('option').remove();

                    var warehouse_name = $('#warehouse_id option:selected').text();

                    if (a_warehouse_address[$(this).val()])
                    {
                        console.log(a_warehouse_address[$(this).val()]);
                        geocoder = new google.maps.Geocoder();
                        geocoder.geocode({'address': a_warehouse_address[$(this).val()]}, function (results, status)
                        {
                            if (status == google.maps.GeocoderStatus.OK)
                            {
                                map.setCenter(results[0].geometry.location);
                                img_number = 1;

                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location,
                                    label: 'W',
                                    title: 'Warehouse ' + warehouse_name
                                });

                                markers['W'] = marker;

                                bounds.extend(marker.getPosition());
                                map.fitBounds(bounds);
                            }
                        });
                    }

                    $('#driver_select').append($('<option value="">-------------- Select --------------</option>'));

                    if (a_driver_options[$(this).val()])
                    {
                        a_driver_options[$(this).val()].forEach(function (item, i, arr)
                        {
                            item_a = item.split('|');

                            $('#driver_select').append($('<option value="' + item_a[0] + '">' + item_a[1] + '</option>'));
                        });
                    } else
                    {
                        alert('No drivers');
                    }
                });

                $('#driver_select').change(function ()
                {
                    $('#tracking_number_tr').remove();

                    driver_select = $(this).val();

                    localStorage.setItem('driver_id', driver_select);

                    if (a_driver_options[$('#warehouse_id').val()])
                    {
                        a_driver_options[$('#warehouse_id').val()].forEach(function (item, i, arr)
                        {
                            item_a = item.split('|');

                            //console.log(item_a[0]+' == '+driver_select);

                            if (item_a[0] == driver_select)
                            {
                                re = /Courier/i;

                                item_d = item_a[2].split('[--1--]');

                                $('#driver_textarea').val(item_d[1]);
                                $('#driver_information').show();
                                if (item_a[1].match(re))
                                {
                                    tracking_tr = '<tr id="tracking_number_tr">';
                                    tracking_tr += '<td>';
                                    tracking_tr += 'Tracking number:';
                                    tracking_tr += '</td>';
                                    tracking_tr += '<td>';
                                    tracking_tr += '<textarea class="form-control" id="tracking_number_textarea" rows="3" cols="40"></textarea>';
                                    tracking_tr += '</td>';
                                    tracking_tr += '</tr>';

                                    $('#driver_information').after(tracking_tr);
                                    $('#tracking_number_textarea').focus();
                                }
                            }
                        });
                    } else
                    {
                        alert('No drivers');
                    }
                });

                $("#order_id").keyup(function (event)
                {
                    if (event.keyCode == 13)
                    {
                        event.preventDefault();

                        $('#search_order').trigger('click');
                    }
                });

                $('#search_order').click(function (event)
                {
                    event.preventDefault();

                    if ($('#warehouse_id').val() == '')
                    {
                        alert('Choose warehouse, please.');
                    } else
                    {
                        if ($('#order_id').val() == '')
                        {
                            alert('Order ID is incorrect.');
                        } else
                        {
                            a_orders_info = [];
                            a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                            if (!a_orders_info) {
                                a_orders_info = [];
                            }

                            if (a_orders_info.length == 23) {
                                alert('Maximum 23 orders on one direction.');
                                return false;
                            }

                            if (a_orders_info.length > 0) {

                                var exist = false;

                                $.each(a_orders_info, function (i, item) {
                                    var key = Object.keys(item)[0];

                                    if (key == $('#order_id').val()) {
                                        exist = true;
                                    }
                                });

                                if (exist) {
                                    alert('This order already exist.');
                                    return false;
                                }
                            }

                            $('.se-pre-con').fadeIn('slow');

                            tracking_number = ($('#tracking_number_textarea').val()) ? $('#tracking_number_textarea').val() : '';
                            
                            $.ajax({
                                data: {
                                    option: 'com_shiporder_directions',
                                    task: 'get_order_address',
                                    order_id: $('#order_id').val(),
                                    warehouse_id: localStorage.getItem('warehouse_id'),
                                    driver_id: localStorage.getItem('driver_id'),
                                    description: $('#driver_textarea').val(),
                                    tracking_number: tracking_number,
                                    queue: alphabet[a_orders_info.length]
                                },
                                type: 'POST',
                                dataType: 'json',
                                url: 'index2.php',
                                success: function (data)
                                {
                                    a_orders_info = [];
                                    a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                                    if (!a_orders_info) {
                                        a_orders_info = [];
                                    }
                                    
                                    new_object = {};
                                    new_object[data.order_id] = {
                                        'order_status': data.order_status, 
                                        'warehouse_name': data.warehouse_name, 
                                        'driver_description': data.driver_service_name, 
                                        'driver_information': data.driver_description, 
                                        'address': data.address, 
                                        'tracking_number': data.tracking_number, 
                                        'driver_service_name': data.driver_service_name, 
                                        'warehouse_id': data.warehouse_id, 
                                        'driver_id': data.driver_id, 
                                        'score': data.score,
                                        'ddate': data.ddate,
                                        'company': data.company,
                                        'suite': data.suite,
                                        'street_number': data.street_number,
                                        'street_name': data.street_name,
                                        'zip': data.zip,
                                        'phone': data.phone,
                                        'special_instructions': data.special_instructions,
                                        'full_name': data.full_name,
                                        'dtime': data.dtime,
                                        'queue': data.queue,
                                        'drivers_rates': data.drivers_rates,
                                        'drivers_rate': ((data.drivers_rate > 0) ? data.drivers_rate : ((data.drivers_rates.length > 0) ? data.drivers_rates[0].id_rate : 0)),
                                        'shipment': data.shipment,
                                    };
                                    
                                    a_orders_info.push(new_object);
                                    localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info));

                                    draw_orders();
                                    //draw_orders_new();

                                    $('#order_id').val('');
                                    $('#order_id').focus();
                                    $('.se-pre-con').fadeOut('slow');
                                }
                            });
                        }
                    }
                });

                $('#print_map').click(function (event) {
                    event.preventDefault();

                    var content = window.document.getElementById("map_canvas");
                    var content2 = window.document.getElementById("table_orders_div");
                    var newWindow = window.open();

                    var new_content = '<style media="print">';
                    new_content += ' body {margin:0.5in;font-family:times}';
                    new_content += ' table {width: 100%; border-collapse: collapse; margin-top: 2px;}';
                    new_content += '';
                    new_content += '</style>';
                    new_content += '<style>';
                    new_content += ' table {width: 100%; border-collapse: collapse; margin-top: 2px;}';
                    new_content += ' td,th {border: 1px solid black; padding: 5px 2px; font-size: 18px; line-height: 18px;}';
                    new_content += ' .th_delete {display: none;}';
                    new_content += ' tr.danger {display: none; background-color: #f2dede;}';
                    new_content += '</style>';
                    let departuretime = jQuery('#departureTime').val().split(' ');
                    new_content += '<div style="font-size: 30px;">Route #&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;,  ' + jQuery('#warehouse_id > option:selected').text() + ', ' + jQuery('#driver_select > option:selected').text() + ', ' + departuretime[0] + '</div>';
                    new_content += '<div style="width: ' + $('#map_canvas').css('width') + '; height: ' + $('#map_canvas').css('height') + '; position: relative; overflow: hidden;">' + content.innerHTML + '</div>';
                    new_content += content2.innerHTML;

                    newWindow.document.write(new_content);

                    //delete_all();

                    newWindow.print();
                });

                $('#in_transit').click(function (event) {
                    event.preventDefault();
                    
                    a_orders_info = [];
                    a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                    if (!a_orders_info) {
                        a_orders_info = [];
                    }
                    
                    var destination = '';
                    
                    if ($('#noreturn').is(':checked')) {
                        destination = $('#noreturn_address').val();
                    }

                    if (a_orders_info.length > 0) {
                        var a_orders_info_2 = a_orders_info;
                        localStorage.setItem('last_order', JSON.stringify(a_orders_info_2.pop()));
                    
                        $('.se-pre-con').fadeIn('slow');

                        $.ajax({
                            data: {
                                option: 'com_shiporder_directions',
                                task: 'in_transit',
                                a_orders_info: localStorage.getItem('a_orders_info'),
                                destination: destination,
                                departureTime: $('#departureTime').val()
                            },
                            type: 'POST',
                            dataType: 'html',
                            url: 'index2.php',
                            success: function (data) {
                                delete_all();
                                $('.se-pre-con').fadeOut('slow');
                            }
                        });
                    }
                });

                $('#noreturn').change(function () {
                    if (this.checked) {
                        $('#noreturn_address').val('').show();
                    } else {
                        $('#noreturn_address').hide();
                    }
                });

                $('#google_sheet').click(function (event) {
                    event.preventDefault();
                    $(this).hide();

                    handleClientLoad();

                });
                
                $('#get_csv').click(function (event) {
                    event.preventDefault();
                    
                    a_orders_info = [];
                    a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));
                    
                    console.log(a_orders_info);

                    if (!a_orders_info) {
                        a_orders_info = [];
                    }
                    
                    console.log(a_orders_info);

                    new_obj = [];

                    arr = [];
                    arr.push('Delivery Date');
                    arr.push('Order Number');
                    arr.push('Company');
                    arr.push('Suite/Apt #');
                    arr.push('Street number');
                    arr.push('Street name');
                    arr.push('Zip/Postal Code');
                    arr.push('Phone');
                    arr.push('Full Name');
                    arr.push('Special Instructions');
                    arr.push('Delivery Time');
                    new_obj.push(arr);

                    $.each(a_orders_info, function (o_k, o_v) {
                        var key_new = Object.keys(o_v)[0];
                        var order_obj = o_v[key_new];
   
                        arr = [];
                        arr.push(order_obj.ddate);
                        arr.push(key_new);
                        arr.push(order_obj.company);
                        arr.push(order_obj.suite);
                        arr.push(order_obj.street_number);
                        arr.push(order_obj.street_name);
                        arr.push(order_obj.zip);
                        arr.push(order_obj.phone);
                        arr.push(order_obj.full_name);
                        arr.push(order_obj.special_instructions);
                        arr.push(order_obj.dtime);

                        new_obj.push(arr);
                    });

                    exportToCsv('export_route.csv', new_obj);
                });
                
                jQuery('#create_route').click(function(event) {
                    event.preventDefault();

                    //draw_orders();

                    //update date
                    if(new Date($('#departureTime').val()) < new Date)
                        $('#departureTime').flatpickr({
                            enableTime: true,
                            dateFormat: "Y-m-d H:i:s",
                            time_24hr: true
                        }).setDate(new Date);

                    draw_orders_new();
                });
                
                
                $('#departureTime').flatpickr({
                    enableTime: true,
                    dateFormat: "Y-m-d H:i:s",
                    time_24hr: true
                });

                if (localStorage.getItem('last_order') !== 'undefined') {
                    last_order = JSON.parse(localStorage.getItem('last_order'));

                    if (last_order) {
                        last_order_id = Object.keys(last_order)[0];

                        $('#last_order_form').attr('title', last_order[last_order_id].address);
                        $('#last_order_form').show();
                        $('#last_order_id_span').text(last_order_id);
                    }
                }
                
                $('#last_order').change(function () {
                    if (this.checked) {
                        if (markers['W']) {
                            markers['W'].setMap(null);
                        }

                        geocoder = new google.maps.Geocoder();
                        geocoder.geocode({'address': last_order[last_order_id].address}, function (results, status)
                        {
                            if (status == google.maps.GeocoderStatus.OK)
                            {
                                map.setCenter(results[0].geometry.location);
                                img_number = 1;

                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location,
                                    label: '$',
                                    title: 'Order ' + last_order_id
                                });

                                markers['W'] = marker;

                                bounds.extend(marker.getPosition());
                                map.fitBounds(bounds);
                            }
                        });
                    } 
                    else {
                        draw_wh();
                    }
                });
                
                jQuery('#scan_id').change(function(e) {
                    a_orders_info = [];
                    a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                    if (!a_orders_info) {
                        a_orders_info = [];
                    }
                    
                    let scan_id = parseInt(jQuery(this).val());
                    
                    if (scan_id > 0) {
                        jQuery.ajax({
                            type: 'POST',
                            url: 'index2.php',
                            data: ({
                                option: 'com_shiporder_directions',
                                task: 'get-scan',
                                scan_id: scan_id,
                                warehouse_id: localStorage.getItem('warehouse_id'),
                                driver_id: localStorage.getItem('driver_id'),
                                description: $('#driver_textarea').val(),
                                tracking_number: '',
                                queue: alphabet[a_orders_info.length]
                            }),
                            dataType: 'json',
                            context: this,
                            beforeSend: function() {
                                jQuery('.se-pre-con').fadeIn('slow');
                            },
                            success: function(json) {
                                if (json.result) {
                                    delete_all();
                                    
                                    jQuery.each(json.orders, function(i, data) {
                                        new_object = {};
                                        new_object[data.order_id] = {
                                            'order_status': data.order_status, 
                                            'warehouse_name': data.warehouse_name, 
                                            'driver_description': data.driver_service_name, 
                                            'driver_information': data.driver_description, 
                                            'address': data.address, 
                                            'tracking_number': data.tracking_number, 
                                            'driver_service_name': data.driver_service_name, 
                                            'warehouse_id': data.warehouse_id, 
                                            'driver_id': data.driver_id, 
                                            'score': data.score,
                                            'ddate': data.ddate,
                                            'company': data.company,
                                            'suite': data.suite,
                                            'street_number': data.street_number,
                                            'street_name': data.street_name,
                                            'zip': data.zip,
                                            'phone': data.phone,
                                            'special_instructions': data.special_instructions,
                                            'full_name': data.full_name,
                                            'dtime': data.dtime,
                                            'queue': data.queue,
                                            'drivers_rates': data.drivers_rates,
                                            'drivers_rate': ((data.drivers_rate > 0) ? data.drivers_rate : ((data.drivers_rates.length > 0) ? data.drivers_rates[0].id_rate : 0)),
                                            'shipment': data.shipment,
                                        };
                                        
                                        a_orders_info.push(new_object);
                                        localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info));
                                    });


                                    draw_orders();
                                    
                                    jQuery('.se-pre-con').fadeOut('slow');
                                }
                                else {
                                    alert('Error');
                                    jQuery('.se-pre-con').fadeOut('slow');
                                }
                            }
                        });
                    }
                });
                
            });
            
            var markers = [];
            var map;
            var directionsService;
            var directionsDisplay;
            
            //localStorage.removeItem('a_orders_info');
            $(document).ready(function ()
            {
                draw_orders();
            })
            
            function initMap() {
                directionsService = new google.maps.DirectionsService;
                directionsDisplay = new google.maps.DirectionsRenderer;
                var ll = new google.maps.LatLng(43.711207, -79.389394);

                map = new google.maps.Map(document.getElementById('map_canvas'), {
                    center: {lat: 43.711207, lng: -79.389394},
                    zoom: 8
                });

                window.bounds = new google.maps.LatLngBounds();
                
                
                draw_wh();
                //draw_orders();
                //draw_orders_new();
            }

            function delete_all() {
                a_orders_info.forEach(function (item, i, arr) {
                    var key = Object.keys(item)[0];

                    if (markers[key]) {
                        markers[key].setMap(null);
                        delete markers[key];
                    }
                });

                $('#table_orders').remove();
                localStorage.removeItem('a_orders_info');
                //localStorage.clear();
                $('#all_distance').text('');
                $('#all_duration').text('');

                draw_orders();
                //draw_orders_new();
                //localStorage.clear();
            }

            var order_iteration = 0;
            
            function draw_wh() {
                setTimeout(function() {
                    var warehouse_id_selected = parseInt(localStorage.getItem('warehouse_id'));

                    if (warehouse_id_selected > 0) {
                        $('#warehouse_id').val(warehouse_id_selected).trigger('change');
                    }
                }, 1000);
            }

            function draw_orders_new() {
                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                if (!a_orders_info) {
                    a_orders_info = [];
                }
                
                a_orders_info.forEach(function(item, i, arr) 
                {
                    var key = Object.keys(item)[0];

                    if (markers[key])
                    {
                        markers[key].setMap(null);
                        delete markers[key];
                    }
                });

                if (a_orders_info.length > 0) {
                    directionsDisplay.setMap(map);
                    geocoder = new google.maps.Geocoder();

                    var waypts = [];
                    var a_addresses = [];

                    $.each(a_orders_info, function (i, item) {
                        var key = Object.keys(item)[0];
                        var it = i + 1;

                        var tr_class = '';

                        waypts.push({
                            location: item[key].address,
                            stopover: true
                        });

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                let pos = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };
                            }, function () {
                                handleLocationError(true, markerme);
                            });
                        } 
                        else {
                            window.alert('Geolocation is not supported');
                        }

                        if (i + 1 === a_orders_info.length) {
                            /*
                             * origin: a_warehouse_address[$('#warehouse_id').val()],
                             destination: a_warehouse_address[$('#warehouse_id').val()],
                             */

                            if ($('#noreturn').is(':checked')) {
                                var destination = $('#noreturn_address').val();
                            } 
                            else if ($('#last_order').is(':checked')) {
                                var destination = last_order[last_order_id].address;
                            }
                            else {
                                var destination = a_warehouse_address[$('#warehouse_id').val()];
                            }

                            directionsService.route({
                                origin: a_warehouse_address[$('#warehouse_id').val()],
                                destination: destination,
                                waypoints: waypts,
                                optimizeWaypoints: true,
                                travelMode: 'DRIVING',
                                transitOptions: {
                                    departureTime: new Date(Date.parse($('#departureTime').val()))
                                },
                            }, function (response, status) {
                                if (status === 'OK') {
                                    console.log(response) ;
                                    directionsDisplay.setDirections(response);

                                    $.each(response.routes[0].legs, function (k, v) {
                                        console.log('LEG: ' + v.end_location);
                                    });

                                    $('#table_orders_div').html('');

                                    table_html = '<table id="table_orders" class="table table-striped">';
                                    table_html += '<tr>';
                                    table_html += '<th>#</th>';
                                    table_html += '<th>Order ID</th>';
                                    table_html += '<th class="th_delete">Delivery Date</th>';
                                    table_html += '<th class="th_delete">Driver Option</th>';
                                    table_html += '<th>Address</th>';
                                    table_html += '<th>Full name (Company)</th>';
                                    table_html += '<th>Distance/Duration/Arrival Time</th>';
                                    table_html += '<th class="th_delete">Driver rate</th>';
                                    table_html += '<th class="th_delete"></th>';
                                    table_html += '</tr></table>';

                                    $('#table_orders_div').html(table_html);

                                    var a_orders_info_new = [];
                                    
                                    $.each(response.routes[0].waypoint_order, function (w_k, w_v) {
                                        var order_id = Object.keys(a_orders_info[w_v])[0];
                                        a_orders_info_new[w_k] = a_orders_info[w_v];
                                        a_orders_info_new[w_k][order_id].queue = alphabet[w_k];
                                    });
                                    
                                    console.log('a_orders_info_new');
                                    console.log(a_orders_info_new);
                                    
                                    var all_duration_new = 0;
                                    var all_distance_new = 0;
                                    
                                    $.each(response.routes[0].legs, function (l_k, l_v) {
                                        all_duration_new = all_duration_new + l_v.duration.value + 300;
                                        all_distance_new = all_distance_new + l_v.distance.value;
                                    });
                                    
                                    localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info_new));
                                    
                                    var start_time_leg_a = $('#departureTime').val().split(' ')[1].split(':');

                                    $.each(a_orders_info_new, function (o_k, o_v) {
                                        var key_new = Object.keys(o_v)[0];

                                        tr_html = '<tr id="order_id_' + key_new + '" class="' + tr_class + '">';
                                        tr_html += '<td>' + alphabet[o_k] + '</td>';
                                        tr_html += '<td><a target="_blank" href="./index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart&order_id=' + key_new + '">' + key_new + '</a></td>';
                                        tr_html += '<td class="th_delete">' + o_v[key_new].ddate + ' ' + o_v[key_new].dtime + '</td>';
                                        tr_html += '<td class="th_delete">Driver: ' + o_v[key_new].driver_description + '';
                                        tr_html += '<br/>Driver information: ' + o_v[key_new].driver_information + '';
                                        if (o_v[key_new].tracking_number != '') {
                                            tr_html += '<br/>Tracking number: ' + o_v[key_new].tracking_number + '';
                                        }
                                        tr_html += '</td>';
                                        tr_html += '<td>' + o_v[key_new].address + '</td>';
                                        tr_html += '<td>' + o_v[key_new].full_name + ' ('+o_v[key_new].company+')</td>';

                                        var duration_leg = parseInt(response.routes[0].legs[o_k].duration.value);
                                        
                                        if (o_k > 0) {
                                            duration_leg = duration_leg + 180;
                                        }
                                        
                                        var min_leg =  duration_leg / 60;
                                        var hour_leg = min_leg / 60;

                                        hour_leg = parseInt(Math.floor(hour_leg % 24));
                                        min_leg = parseInt(Math.floor(min_leg % 60));

                                        var new_hour = parseInt(parseInt(start_time_leg_a[0])+hour_leg);
                                        var new_min = parseInt(parseInt(start_time_leg_a[1])+min_leg);
                                        
                                        if (new_min > 60) {
                                            new_hour = new_hour + 1;
                                            new_min = new_min - 60;
                                        }
                                        
                                        if (new_min < 10) {
                                            new_min = '0'+new_min;
                                        }
                                        var start_time_leg_new_a = new_hour+':'+new_min;
                                        
                                        start_time_leg_a = start_time_leg_new_a.split(':');
                                        
                                        tr_html += '<td>' + response.routes[0].legs[o_k].distance.text + '/' + response.routes[0].legs[o_k].duration.text + '/'+start_time_leg_new_a+'</td>';
                                        tr_html += '<td class="th_delete">';
                                            if (o_v[key_new].drivers_rates.length > 0) {
                                                tr_html += '<select class="form-control" name="driver_rate" onchange="set_driver_rate(' + key_new + ', this);">';
                                                jQuery.each(o_v[key_new].drivers_rates, function(i, element) {
                                                    tr_html += '<option value="' + element.id_rate + '"' + (((o_v[key_new].drivers_rate == element.id_rate) || (i == 0 && o_v[key_new].drivers_rate == '')) ? 'selected' : '') + '>' + element.name + '</option>';
                                                });
                                                tr_html += '</select>';   
                                            }
                                        tr_html += '</td>';
                                        tr_html += '<td class="th_delete">';
                                        
                                        var av_class = 'btn-default';
                                        var score = parseInt(o_v[key_new].score);
                                        if (score == 0) {
                                            av_class = 'btn-danger';
                                        }
                                        else if (score > 0 && score < 100) {
                                            av_class = 'btn-warning';
                                        }
                                        else {
                                            av_class = 'btn-success';
                                        }
                                        
                                        tr_html += '<button type="button" class="btn '+av_class+' btn-sm" onclick="address_verification(' + key_new + ');">Address Match '+o_v[key_new].score+'%</button>';
                                        tr_html += '<button type="button" class="btn btn-default btn-sm" onclick="delete_order(' + key_new + ');"><span class="glyphicon glyphicon-trash"></span> Trash</button>';
                                        tr_html += '</td>';
                                        tr_html += '</tr>';

                                        all_duration_new = all_duration_new + 180;

                                        $('#table_orders tr:last').after(tr_html);
                                    });

                                    var all_distance = (all_distance_new / 1000).toFixed(2);
                                    var all_distance_html = all_distance+' km';
                                    var startTime = new Date($('#departureTime').val());

                                    if (all_distance > 15)  {
                                        var all_distance_html = '<span style="font-weight: bold; color: red;">'+all_distance+' km</span>';
                                    }
                                    else if (all_distance > 7)  {
                                        var all_distance_html = '<span style="font-weight: bold;">'+all_distance+' km</span>';
                                    }

                                    $('#all_distance').html(all_distance_html);

                                    var min = all_duration_new / 60;
                                    var hour = min / 60;

                                    hour = parseInt(Math.floor(hour % 24));
                                    min = parseInt(Math.floor(min % 60));
                                    var day = hour + ':' + min + ':' + Math.floor(all_duration_new % 60);

                                    startTime.setSeconds(startTime.getSeconds()+all_duration_new)

                                    $('#all_duration').text(day);
                                    $('#time_duration').text(startTime.getHours().toString().padStart(2, '0')+':'+startTime.getMinutes().toString().padStart(2, '0'))
                                } else {
                                    directionsDisplay.setMap(null);
                                    window.alert('Directions request failed due to ' + status);
                                }
                            });
                        }
                        /*});*/
                    });
                    
                    console.log(markers);
                } else {
                    directionsDisplay.setMap(null);
                }
            }
            
            function draw_orders()
            {
                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                if (!a_orders_info) {
                    a_orders_info = [];
                }                
                
                directionsDisplay.setMap(null);
                
                //$('#table_orders').remove();
                
                if (a_orders_info.length > 0)
                {
                    $('#table_orders_div').html('');

                    table_html = '<table id="table_orders" class="table table-striped">';
                    table_html += '<tr>';
                    table_html += '<th>#</th>';
                    table_html += '<th>Order ID</th>';
                    table_html += '<th>Delivery Date</th>';
                    table_html += '<th>Driver Option</th>';
                    table_html += '<th>Address</th>';
                    table_html += '<th>Full name (Company)</th>';
                    table_html += '<th>Distance/Duration</th>';
                    table_html += '<th class="th_delete">Driver rate</th>';
                    table_html += '<th class="th_delete"></th>';
                    table_html += '</tr></table>';

                    $('#table_orders_div').html(table_html);
                    
                    geocoder = new google.maps.Geocoder();

                    a_adresses = [];
                    var order_it = 0;
                    
//                    $.each(markers, function (m_k, m_v) {
//                        console.log(markers[m_k]);
//                        //markers[m_k].setMap(null);
//                    });
                    
                    a_orders_info.forEach(function(item, i, arr) 
                    {
                        var key = Object.keys(item)[0];

                        if (markers[key])
                        {
                            markers[key].setMap(null);
                            delete markers[key];
                        }
                    });
                    
                    $.each(a_orders_info, function (i, item) {   
                        var key = Object.keys(item)[0];

                        geocoder.geocode({'address': item[key].address}, function (results, status) 
                        {
                            if (status == google.maps.GeocoderStatus.OK)
                            {
                                let icon_color = 'red';
                                
                                switch (item[key].shipment) {
                                    case 3:
                                        icon_color = 'blue';
                                        break;
                                    case 2:
                                        icon_color = 'pink';
                                        break;
                                    default:
                                        icon_color = 'red';
                                }
                                
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location,
                                    icon: {
                                        url: 'https://maps.google.com/mapfiles/ms/icons/' + icon_color + '.png',
                                        labelOrigin: new google.maps.Point(13, 10)
                                    },
                                    label: {
                                        text: '' + alphabet[order_it] + '',
                                        color: '#000',
                                        fontSize: '15px'
                                    },
                                    title: 'Order ID ' + key
                                });

                                markers[key] = marker;

                                bounds.extend(marker.getPosition());
                                map.fitBounds(bounds);

                                var tr_class = '';
                            }
                            else
                            {
                                var tr_class = 'danger';
                            }

                            tr_html = '<tr id="order_id_' + key + '" class="' + tr_class + '">';
                            tr_html += '<td>' + alphabet[order_it] + '</td>';
                            tr_html += '<td><a target="_blank" href="./index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart&order_id_filter=' + key + '">' + key + '</a></td>';
                            tr_html += '<td>' + item[key].ddate + ' ' + item[key].dtime + '</td>';
                            tr_html += '<td>Driver: ' + item[key].driver_description + '';
                            tr_html += '<br/>Driver information: ' + item[key].driver_information + '';
                            if (item[key].tracking_number != '') {
                                tr_html += '<br/>Tracking number: ' + item[key].tracking_number + '';
                            }
                            tr_html += '</td>';
                            tr_html += '<td>' + item[key].address + '</td>';
                            tr_html += '<td>' + item[key].full_name + ' ('+item[key].company+')</td>';
                            tr_html += '<td></td>';
                            tr_html += '<td class="th_delete">';
                                if (item[key].drivers_rates.length > 0) {
                                    tr_html += '<select class="form-control" name="driver_rate" onchange="set_driver_rate(' + key + ', this);">';
                                    jQuery.each(item[key].drivers_rates, function(i, element) {
                                        tr_html += '<option value="' + element.id_rate + '"' + (((item[key].drivers_rate == element.id_rate) || (i == 0 && item[key].drivers_rate == '')) ? 'selected' : '') + '>' + element.name + '</option>';
                                    });
                                    tr_html += '</select>';   
                                }
                            tr_html += '</td>';
                            tr_html += '<td class="th_delete">';

                            var av_class = 'btn-default';
                            var score = parseInt(item[key].score);
                            if (score == 0) {
                                av_class = 'btn-danger';
                            }
                            else if (score > 0 && score < 100) {
                                av_class = 'btn-warning';
                            }
                            else {
                                av_class = 'btn-success';
                            }

                            tr_html += '<button type="button" class="btn '+av_class+' btn-sm" onclick="address_verification(' + key + ');">Address Match '+item[key].score+'%</button>';
                            tr_html += '<button type="button" class="btn btn-default btn-sm" onclick="delete_order(' + key + ');"><span class="glyphicon glyphicon-trash"></span> Trash</button>';
                            tr_html += '</td>';
                            tr_html += '</tr>';
                            
                            $('#table_orders tr:last').after(tr_html);
                            
                            order_it = order_it+1;
                        });
                    });
                    
                    console.log(markers);
                }
            }

            function delete_order(order_id)
            {
                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                if (markers[order_id])
                {
                    markers[order_id].setMap(null);
                    $('#order_id_' + order_id).remove();
                    delete markers[order_id];
                }

                a_orders_info.forEach(function (item, i, arr)
                {
                    var key = Object.keys(item)[0];

                    console.log(key + ' == ' + order_id);

                    if (key == order_id)
                    {
                        a_orders_info.splice(i, 1);
                    }
                });

                if (a_orders_info.length == 0) {
                    delete_all();

                    return true;
                } else {
                    localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info));

                    draw_orders();
                    //draw_orders_new();
                }
            }
            
            function set_driver_rate(order_id, id_rate) {
                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                a_orders_info.forEach(function (item, i, arr)
                {
                    var key = Object.keys(item)[0];

                    if (key == order_id)
                    {
                        item[key].drivers_rate = id_rate.value;
                    }
                    
                    console.log('item');
                    console.log(item);
                });
                
                a_orders_info = a_orders_info.reverse();

                localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info));

                draw_orders();
                //draw_orders_new();
            }
        </script>
        <!-- AIzaSyD90676zBk2mwTtvQiFddfElhV9VOF6-aE AIzaSyCbgBBv7UHnq9eGv7i4_ieksgXv8qBTGLg  AIzaSyCbgBBv7UHnq9eGv7i4_ieksgXv8qBTGLg-->

        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY&callback=initMap">
        </script>
        <?php
//        echo '<pre>';
//            print_r($return_a);
//        echo '</pre>';
        ?>
        <div class="se-pre-con"></div>
        <div class="wrapper">
            <div class="well left_info">
                <table class="table table-nonfluid" id="table_info">
                    <tbody>
                        <tr>
                            <td>
                                Scan:
                            </td>
                            <td>
                                <?php echo $return_a['scan_select']; ?>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                Order details
                            </th>
                        </tr>
                        <tr>
                            <td>
                                Warehouse:
                            </td>
                            <td>
                                <?php echo $return_a['warehouses_select']; ?>
                            </td>
                        </tr>
                        <tr id="last_order_form">
                            <td>
                                Start from last order (<span id="last_order_id_span"></span>):
                            </td>
                            <td>
                                <div class="">
                                    <label>
                                        <input type="checkbox" id="last_order"> 
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Driver:
                            </td>
                            <td>
                                <select id="driver_select" class="form-control">
                                    <option value="">-------------- Select --------------</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="driver_information" style="display: none;">
                            <td>
                                Information:
                            </td>
                            <td>
                                <textarea class="form-control" id="driver_textarea" rows="3" cols="40"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Order ID:
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="order_id" class="form-control" type="text" id="order_id">
                                </div>
                                <button type="submit" id="search_order" class="btn btn-success">Search</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button id="create_route" type="button" class="btn btn-primary">Optimization Route</button>
                            </td>
                            <td>
                                <button id="in_transit" type="button" class="btn btn-primary">In transit</button>
                                <button id="print_map" type="button" class="btn btn-info">Print</button>   
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="">
                                    <label>
                                        <input type="checkbox" id="noreturn" name="noreturn" value="1"> 
                                        Do not return to warehouse?
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="text" id="noreturn_address" placeholder="Address">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button id="google_sheet" type="button" style="display: none" class="btn btn-warning">Get Google Sheet</button>
                                <button id="authorize-button" style="display: none;">Authorize</button>
                            </td>
                            <td>
                                <button id="signout-button" style="display: none;">Sign Up</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button id="get_csv" type="button" class="btn btn-warning">Get CSV</button>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button onclick="delete_all();" type="button" class="btn btn-danger">Clear</button>
                            </td>
                            <td>
                                <table class="table table-nonfluid" style="width: 100% !important; background-color: transparent !important;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                Start route:
                                            </td>
                                            <td>
                                                <input type="text" id="departureTime" value="<?php echo date('Y-m-d H:i:s'); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">
                                                Est. time in route:
                                            </td>
                                            <td style="text-align: right;">
                                                <span id="all_duration">
                                                    
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">
                                                Arrival Time:
                                            </td>
                                            <td style="text-align: right;">
                                                <span id="time_duration">
                                                    
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">
                                                Distance:
                                            </td>
                                            <td style="text-align: right;" id="all_distance">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!--
                                <span id="all_distance"></span>
                                <br/>
                                <span id="all_duration"></span>-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="well right_info" id="map_div">
                <div id="map_canvas"></div>
            </div>
        </div>
        <div id="table_orders_div">        
        </div>

        <script type="text/javascript">
            var spreadsheetId = '';
            var spreadsheetUrl = '';
            var sheetId = '';

            // Client ID and API key from the Developer Console
            var CLIENT_ID = '420686801673-jena616s39s621tjh01em2ff5kvv3j9t.apps.googleusercontent.com';
            var CLIENT_ID = '933341403081-5lqd8jrtr2lh684fq0578j6bj9empdnr.apps.googleusercontent.com';

            // Array of API discovery doc URLs for APIs used by the quickstart
            var DISCOVERY_DOCS = ["https://sheets.googleapis.com/$discovery/rest?version=v4"];

            // Authorization scopes required by the API; multiple scopes can be
            // included, separated by spaces.
            var SCOPES = 'https://www.googleapis.com/auth/analytics.readonly https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/spreadsheets';

            var authorizeButton = document.getElementById('authorize-button');
            var signoutButton = document.getElementById('signout-button');

            /**
             *  On load, called to load the auth2 library and API client library.
             */
            function handleClientLoad() {
                gapi.load('client:auth2', initClient);
            }

            /**
             *  Initializes the API client library and sets up sign-in state
             *  listeners.
             */
            function initClient() {
                gapi.client.init({
                    discoveryDocs: DISCOVERY_DOCS,
                    clientId: CLIENT_ID,
                    scope: SCOPES
                }).then(function () {
                    // Listen for sign-in state changes.
                    gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

                    // Handle the initial sign-in state.
                    updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
                    authorizeButton.onclick = handleAuthClick;
                    signoutButton.onclick = handleSignoutClick;
                });
            }

            /**
             *  Called when the signed in status changes, to update the UI
             *  appropriately. After a sign-in, the API is called.
             */
            function updateSigninStatus(isSignedIn) {
                if (isSignedIn) {
                    authorizeButton.style.display = 'none';
                    signoutButton.style.display = 'block';

                    createSheet();
                } else {
                    authorizeButton.style.display = 'block';
                    signoutButton.style.display = 'none';
                }
            }

            /**
             *  Sign in the user upon button click.
             */
            function handleAuthClick(event) {
                gapi.auth2.getAuthInstance().signIn();
            }

            /**
             *  Sign out the user upon button click.
             */
            function handleSignoutClick(event) {
                gapi.auth2.getAuthInstance().signOut();
            }

            /**
             * Append a pre element to the body containing the given message
             * as its text node. Used to display the results of the API call.
             *
             * @param {string} message Text to be placed in pre element.
             */
            function appendPre(message) {
                //var pre = document.getElementById('content');
                //var textContent = document.createTextNode(message + '\n');
                //pre.appendChild(textContent);
            }

            function clearSheet() {
                gapi.client.sheets.spreadsheets.values.clear({
                    spreadsheetId: spreadsheetId,
                    range: 'A2:J'
                }).then(function (response) {
                    appendPre(response);
                }, function (response) {
                    appendPre('Error: ' + response.result.error.message);
                });
            }

            function appendSheet() {

                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                if (!a_orders_info) {
                    a_orders_info = [];
                }

                new_obj = {};
                new_obj.values = [];

                console.log(new_obj);

                arr = [];
                arr.push('Delivery Date');
                arr.push('Order Number');
                arr.push('Company');
                arr.push('Suite/Apt #');
                arr.push('Street number');
                arr.push('Street name');
                arr.push('Zip/Postal Code');
                arr.push('Phone');
                arr.push('Full Name');
                arr.push('Special Instructions');
                arr.push('Delivery Time');
                new_obj.values.push(arr);

                $.each(a_orders_info, function (o_k, o_v) {
                    var key_new = Object.keys(o_v)[0];

                    var order_obj = o_v[key_new];

                    arr = [];
                    arr.push(order_obj.ddate);
                    arr.push(key_new);
                    arr.push(order_obj.company);
                    arr.push(order_obj.suite);
                    arr.push(order_obj.street_number);
                    arr.push(order_obj.street_name);
                    arr.push(order_obj.zip);
                    arr.push(order_obj.phone);
                    arr.push(order_obj.full_name);
                    arr.push(order_obj.special_instructions);
                    arr.push(order_obj.dtime);

                    new_obj.values.push(arr);
                });

                console.log(new_obj);
                var responseJson = '' + JSON.stringify(new_obj) + '';
                gapi.client.sheets.spreadsheets.values.append({
                    spreadsheetId: spreadsheetId,
                    range: 'A1',
                    resource: responseJson,
                    valueInputOption: 'USER_ENTERED',
                }).then(function (response) {
                    updateSheet();
                }, function (response) {
                    appendPre('Error: ' + response.result.error.message);
                });
            }

            function updateSheet() {
                gapi.client.sheets.spreadsheets.batchUpdate({
                    spreadsheetId: spreadsheetId,
                    "requests": [
                        {
                            "autoResizeDimensions": {
                                "dimensions": {
                                    "sheetId": sheetId,
                                    "dimension": "COLUMNS",
                                    "startIndex": 0,
                                    "endIndex": 20
                                }
                            }
                        }]
                }).then(function (response) {
                    $('.se-pre-con').hide();
                    //document.getElementById('report_loader').style.display='none';
                    //document.getElementById('content').innerHTML = 'The report will be opened in new window. If You don\'t see it yet - click here: <a target="_blank" href="'+spreadsheetUrl+'">'+spreadsheetUrl+'</a>';
                    window.open(spreadsheetUrl);
                }, function (response) {
                    appendPre('Error: ' + response.result.error.message);
                });
            }

            function createSheet() {
                //document.getElementById('report_loader').style.display='block';
                $('.se-pre-con').show();
                gapi.client.sheets.spreadsheets.create({
                    "properties": {
                        "title": 'Routes'
                    },
                    "sheets": [
                        {
                            "properties": {
                                "title": 'Data'
                            },
                        }
                    ],
                }).then(function (response) {
                    spreadsheetId = response.result.spreadsheetId;
                    spreadsheetUrl = response.result.spreadsheetUrl;
                    sheetId = response.result.sheets[0].properties.sheetId;

                    appendSheet();
                }, function (response) {
                    appendPre('Error: ' + response.result.error.message);
                });
            }
            
        function address_verification(order_id) {
            var url = '/scripts/deliveries/fedex/av.php?option=AddressValidation&order_id=' + order_id;
            var child = window.open(url, '_blank');
            var timer = setInterval(checkChild, 100);
            function checkChild() {
                if (child.closed) {
                    window.location.reload()
                    clearInterval(timer);
                }
            }
        }
        
        function exportToCsv(filename, rows) {
            var processRow = function (row) {
                var finalVal = '';
                for (var j = 0; j < row.length; j++) {
                    var innerValue = row[j] === null ? '' : row[j].toString();
                    if (row[j] instanceof Date) {
                        innerValue = row[j].toLocaleString();
                    };
                    var result = innerValue.replace(/"/g, '""');
                    if (result.search(/("|,|\n)/g) >= 0)
                        result = '"' + result + '"';
                    if (j > 0)
                        finalVal += ',';
                    finalVal += result;
                }
                return finalVal + '\n';
            };

            var csvFile = '';
            for (var i = 0; i < rows.length; i++) {
                csvFile += processRow(rows[i]);
            }

            var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
            if (navigator.msSaveBlob) { // IE 10+
                navigator.msSaveBlob(blob, filename);
            } 
            else {
                var link = document.createElement("a");
                if (link.download !== undefined) { // feature detection
                    // Browsers that support HTML5 download attribute
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", filename);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }

        </script>
        <!--src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBT2YTYOaRxWXJW9-4QtLOO-Od2shHIOwY&callback=initMap">-->
        <!--
        <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD90676zBk2mwTtvQiFddfElhV9VOF6-aE&callback=initMap">
        </script>-->


        <script async defer src="https://apis.google.com/js/api.js">
        </script>
        <link rel="stylesheet" href="../administrator/components/com_shiporder_directions/flatpickr/style.min.css">
        <script src="../administrator/components/com_shiporder_directions/flatpickr/flatpickr.min.js"></script>
        <?php
    }

}
