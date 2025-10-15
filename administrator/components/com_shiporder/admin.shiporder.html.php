<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_ShipOrder {
    
    function defaultview($return_a)
    {
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
                height: 500px;
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
            @media print {
                body * { visibility: hidden; }
                .right_info * { visibility: visible; }
                #map_canvas {visibility: visible;position:absolute; top: 5px; left: 5px;}
            }
        </style>
        
        <script type="text/javascript">
            var a_driver_options = {}; 
            var labelIndex = 0;
            
            $( document ).ready(function() 
            {
                a_warehouse_address = [];
                
                <?php
                foreach ($return_a['warehouse_address'] as $warehouse_id => $warehouse_address)
                {
                    ?>
                    a_warehouse_address[<?php echo $warehouse_id; ?>] = '<?php echo $warehouse_address; ?>';       
                    <?php
                }
                ?>
                        
                <?php
                foreach ($return_a['driver_options'] as $warehouse_id => $drivers_options)
                {
                    ?>

                    a_driver_options[<?php echo $warehouse_id; ?>] = {}; 

                    <?php
                    $driver_option_all = array();

                    foreach ($drivers_options as $driver_option)
                    {
                        $driver_option_all[] = "".(int)$driver_option['driver_id']."|".htmlspecialchars($driver_option['title'])."|".htmlspecialchars($driver_option['description'])."";
                    }
                    ?>
                        a_driver_options[<?php echo $warehouse_id; ?>] = ["<?php echo implode('" , "', $driver_option_all); ?>"];
                    <?php
                }
                ?>
                        
                $('#warehouse_id').change(function() 
                {   
                    if (markers['W'])
                    {
                        markers['W'].setMap(null);
                    }
                    
                    localStorage.setItem('warehouse_id', $(this).val());
                    
                    $('#driver_select').find('option').remove();
                    
                    var warehouse_name = $('#warehouse_id option:selected').text();
                    
                    if (a_warehouse_address[$(this).val()])
                    {
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
                                    title: 'Warehouse '+warehouse_name
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
                        a_driver_options[$(this).val()].forEach(function(item, i, arr) 
                        {
                            item_a = item.split('|');

                            $('#driver_select').append( $('<option value="'+item_a[0]+'">'+item_a[1]+'</option>'));
                        });
                    }
                    else
                    {
                        alert('No drivers');
                    }
                });
                
                $('#driver_select').change(function() 
                {
                    $('#tracking_number_tr').val('').hide();
                    
                    driver_select = $(this).val();
                    
                    localStorage.setItem('driver_id', driver_select);
                    
                    if (a_driver_options[$('#warehouse_id').val()])
                    {
                        a_driver_options[$('#warehouse_id').val()].forEach(function(item, i, arr) 
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
                                    $('#tracking_number_tr').show();
                                    $('#tracking_number_textarea').focus();
                                    /*
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
                                    */
                                }
                            }
                        });
                    }
                    else
                    {
                        alert('No drivers');
                    }
                });
                
                
                $("#tracking_number_textarea").keyup(function(event)
                {
                    if (event.keyCode == 13)
                    {
                        event.preventDefault();

                        $('#order_id').focus();
                    }
                });
                
                $("#order_id").keyup(function(event)
                {
                    if (event.keyCode == 13)
                    {
                        event.preventDefault();

                        $('#search_order').trigger('click');
                    }
                });
                
                $('#search_order').click(function(event)
                {
                    event.preventDefault();
                    
                    if ($('#warehouse_id').val() == '')
                    {
                        alert('Choose warehouse, please.');
                    }
                    else
                    {
                        if ($('#order_id').val() == '')
                        {
                            alert('Order ID is incorrect.');
                        }
                        else
                        {
                            $('.se-pre-con').fadeIn('slow');

                            tracking_number = ($('#tracking_number_textarea').val()) ? $('#tracking_number_textarea').val() : '';
                            
                            $.ajax({
                                data: {
                                    option: 'com_shiporder',
                                    task: 'get_order_address',
                                    order_id: $('#order_id').val(),
                                    warehouse_id: localStorage.getItem('warehouse_id'),
                                    driver_id: localStorage.getItem('driver_id'),
                                    description: $('#driver_textarea').val(),
                                    tracking_number: tracking_number
                                },
                                type: 'POST',
                                dataType: 'json',
                                url: 'index2.php',
                                success: function(data)
                                {
                                    if(data.order_id &&  ($('#order_id_'+data.order_id).html() == null)){

                                    a_orders_info = [];
                                    a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                                    if (!a_orders_info)
                                    {
                                        a_orders_info = [];
                                    }

                                    new_object = {};
                                    new_object[data.order_id] = {'order_status': data.order_status, 'warehouse_name': data.warehouse_name, 'driver_description': data.driver_service_name, 'driver_information': data.driver_description, 'address': data.address, 'tracking_number': data.tracking_number, 'driver_service_name': data.driver_service_name };
                                    a_orders_info.push(new_object);
                                    localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info));

                                    draw_orders();

                                    }
                                    $('#order_id').val('');
                                    $('#tracking_number_textarea').val('').focus();
                                    $('.se-pre-con').fadeOut('slow');
                                }
                            });
                        }
                    }
                });
                
                $('#print_map').click(function(event)
                {
                    event.preventDefault();
                    
                    var content = window.document.getElementById("map_canvas"); 
                    var content2 = window.document.getElementById("table_orders_div"); 
                    var newWindow = window.open();
                    
                    var new_content = '<style media="print">';
                    new_content += ' body {margin:0.5in;font-family:times}';
                    new_content += ' table {width: 100%; border-collapse: collapse; margin-top: 20px;}';
                    new_content += '';
                    new_content += '</style>';
                    new_content += '<style>';
                    new_content += ' table {width: 100%; border-collapse: collapse; margin-top: 20px;}';
                    new_content += ' td,th {border: 1px solid black; padding: 20px;}';
                    new_content += ' .th_delete {display: none;}';
                    new_content += ' tr.danger {display: none; background-color: #f2dede;}';
                    new_content += '</style>';
                    new_content += '<div style="width: '+$('#map_canvas').css('width')+'; height: '+$('#map_canvas').css('height')+'; position: relative; overflow: hidden;">'+content.innerHTML+'</div>';
                    new_content += content2.innerHTML;
                    
                    newWindow.document.write(new_content);
                    
                    //delete_all();
                    
                    newWindow.print();
                });
                
                $('#in_transit').click(function(event)
                {
                    a_orders_info = [];
                    a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                    if (!a_orders_info)
                    {
                        a_orders_info = [];
                    }                

                    if (a_orders_info.length > 0)
                    {
                        $('.se-pre-con').fadeIn('slow');
                        
                        $.ajax({
                            data: {
                                option: 'com_shiporder',
                                task: 'in_transit',
                                a_orders_info: localStorage.getItem('a_orders_info')
                            },
                            type: 'POST',
                            dataType: 'html',
                            url: 'index2.php',
                            success: function(data)
                            {
                                delete_all();
                                $('.se-pre-con').fadeOut('slow');
                            }
                        });
                    }
                });
                  
            });
            
            var markers = [];
            var map;
            
            function initMap() 
            {
                var ll = new google.maps.LatLng(-33.871726, 151.206782);
                
                map = new google.maps.Map(document.getElementById('map_canvas'), {
                center: {lat: -33.871726, lng: 151.206782},
                zoom: 8
                });

                window.bounds = new google.maps.LatLngBounds();
                
                draw_orders();
            };
             
            function delete_all()
            {
                a_orders_info.forEach(function(item, i, arr) 
                {
                    var key = Object.keys(item)[0];
                    
                    if (markers[key])
                    {
                        markers[key].setMap(null);
                        delete markers[key];
                    }
                });
                
                $('#table_orders').remove();

                localStorage.removeItem('a_orders_info');

                draw_orders();
            }

            var order_iteration = 1;
            
            function draw_orders()
            {
                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));

                if (!a_orders_info)
                {
                    a_orders_info = [];
                }                
                
                //$('#table_orders').remove();
                
                if (a_orders_info.length > 0)
                {
                    console.log($('#table_orders').html());
                    if ($('#table_orders').html() == null)
                    {
                        table_html = '<table id="table_orders" class="table table-striped">';
                        table_html += '<tr>';
                        table_html += '<th>#</th>';
                        table_html += '<th>Order ID</th>';
                        table_html += '<th>Order Status</th>';
                        table_html += '<th>Driver Option</th>';
                        table_html += '<th>Address</th>';
                        table_html += '<th class="th_delete"></th>';
                        table_html += '</tr></table>';

                        $('#table_orders_div').html(table_html);
                    }
                    geocoder = new google.maps.Geocoder();

                    a_adresses = [];

                    a_orders_info.forEach(function(item, i, arr) 
                    {
                        var key = Object.keys(item)[0];
                        //console.log(item);
           
                        var it = i+1;
                        
                        if ($('#order_id_'+key).html() == null)
                        {
                            geocoder.geocode({'address': item[key].address}, function (results, status) 
                            {
                                if (status == google.maps.GeocoderStatus.OK)
                                {
                                    var marker = new google.maps.Marker({
                                        map: map,
                                        position: results[0].geometry.location,
                                        label: ''+order_iteration+'',
                                        title: 'Order ID '+key
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
                                
                                tr_html = '<tr id="order_id_'+key+'" class="'+tr_class+'">';
                                tr_html += '<td>'+order_iteration+'</td>';
                                tr_html += '<td>'+key+'</td>';
                                tr_html += '<td>'+item[key].order_status+'</td>';
                                tr_html += '<td>Driver: '+item[key].driver_description+'';
                                tr_html += '<br/>Driver information: '+item[key].driver_information+'';
                                if (item[key].tracking_number != '')
                                {
                                    tr_html += '<br/>Tracking number: '+item[key].tracking_number+'';
                                }
                                tr_html += '</td>';
                                tr_html += '<td>'+item[key].address+'</td>';
                                tr_html += '<td class="th_delete"><button type="button" class="btn btn-default btn-sm" onclick="delete_order('+key+');"><span class="glyphicon glyphicon-trash"></span> Trash</button></td>';
                                tr_html += '</tr>';

                                $('#table_orders tr:last').after(tr_html);
                                order_iteration = order_iteration+1;
                            });
                        }
                        else
                        {
                            console.log('order id '+key+' exist');
                        }
                    });
                }
            }
            
            function delete_order(order_id)
            {
                a_orders_info = [];
                a_orders_info = JSON.parse(localStorage.getItem('a_orders_info'));
                
                if (markers[order_id])
                {
                    markers[order_id].setMap(null);
                    $('#order_id_'+order_id).remove();
                    delete markers[order_id];
                }
                
                a_orders_info.forEach(function(item, i, arr) 
                {
                    var key = Object.keys(item)[0];

                    if (key == order_id)
                    {
                        a_orders_info.splice(i, 1);
                    }
                });
                
                localStorage.setItem('a_orders_info', JSON.stringify(a_orders_info));
                
                draw_orders();
            }

        </script>
        
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
                        <tr id="tracking_number_tr" style="display: none;">
                            <td>
                                Tracking number:
                            </td>
                            <td>
                                <textarea class="form-control" id="tracking_number_textarea" rows="3" cols="40"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Order ID:
                            </td>
                            <td>
                                <span class="form-inline" role="form">
                                <input class="form-control" type="text" id="order_id"> <button id="search_order" type="button" class="btn btn-success">Search</button>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button id="in_transit" type="button" class="btn btn-primary">In transit</button>
                            </td>
                            <td>
                                <button id="print_map" type="button" class="btn btn-info">Print</button>   
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button onclick="delete_all();" type="button" class="btn btn-danger">Clear</button>
                            </td>
                            <td>
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
        <?php
    }
}