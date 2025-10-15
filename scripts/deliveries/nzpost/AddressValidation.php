<?php
    if (isset($_REQUEST['order_id'])) {

        $resultTableColumns = [
            'suite' => 'Suite',
            'street_number' => 'StreetNumber',
            'street_name' => 'StreetName',
            'district' => 'District',
            'city' => 'City',
            'zip' => 'PostalCode'
        ];

        require_once('connectstonzpostapi.php');

        $order_id = $_REQUEST['order_id'];

        $nzpost = new NZPost($order_id);


        $currentAddress = $nzpost->BloomexOrder->_StreetLines1 . ' ' . $nzpost->BloomexOrder->_StreetLines2 . ($nzpost->BloomexOrder->_District?', '.$nzpost->BloomexOrder->_District:'') . ', ' . $nzpost->BloomexOrder->_City . ' ' . $nzpost->BloomexOrder->_PostalCode;

        if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'getSuggestedAddresses') {
            $address = $_REQUEST['q'];
            $result = $nzpost->getSuggestedAddresses($address);
            exit(json_encode($result));
        } elseif (isset($_REQUEST['method']) && $_REQUEST['method'] == 'getSuggestedAddressDetails') {
            $dpid = $_REQUEST['dpid'];
            $result = $nzpost->getSuggestedAddressDetails($dpid);
            exit(json_encode($result));
        } elseif (isset($_REQUEST['method']) && $_REQUEST['method'] == 'changeValue') {
            $type_db = $_REQUEST['type_db'];
            $new_value = $_REQUEST['new_value'];
            $nzpost->BloomexOrder->update_order_information($new_value, $type_db, $order_id);

        }else{
            $currentAddressDetails = $nzpost->getSuggestedAddressDetails($nzpost->delivery_address_id??0);
        }
//        echo "<pre>";print_r($response_options);die;
        ?>

        <html lang="en">
        <head>
            <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
        </head>
        <body>
        <div id="dialog-confirm" title="Update Information"></div>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <div class="ui-widget">
            <p style="text-align: center"><b>Current Address</b></p>

            <input type="text" id="address" value="<?php echo $currentAddress; ?>" />
            <div id="suggesstion_box"></div>
        </div>

        <div class="ui-widget" style="margin-top:2em; font-family:Arial">
            <p style="text-align: center"><b>Address Details From NzPost Service</b></p>
            <div id="suggested_addresses" style="height: 400px;overflow: auto;" class="ui-widget-content">
            <?php
                $address_match_table = '<table style=\'width:100%\' border="1"><tr><th>Name</th><th>Current Value</th><th>Suggested Value</th><th>Action</th></tr>';

                foreach($resultTableColumns as $key=>$val){
                    $style='';
                    if($nzpost->BloomexOrder->{_.$val}!=($currentAddressDetails['details'][$key]??'')) {
                        $style="style='color:red'";
                    }
                    $address_match_table .= "<tr ><td ".$style."> ".$val."</td><td >".$nzpost->BloomexOrder->{_.$val}."</td><td class='suggested_address {$key}'>" . ($currentAddressDetails['details'][$key]??'') . "</td><td><input type='button' value='Update' new_value='".($currentAddressDetails['details'][$key]??'')."' onclick=change('".$key."',$order_id,this) class='change_value_{$key} '><span style='display: none'>Please wait...</span></td></tr>";
                }

                echo $address_match_table.'</table>';
              ?>
            </div>
        </div>


        <script type="text/javascript">

            $(document).ready(function() {
                $("#address").keyup(function(e) {
                    var timer;

                    if (this.value.length > 2) {
                        if (timer){
                            clearTimeout(timer);
                        }
                        timer = setTimeout(function(){
                            $.ajax({
                                type: "POST",
                                url: "AddressValidation.php",
                                data: {
                                    q:$("#address").val(),order_id: <?php echo $order_id ?>, method: "getSuggestedAddresses"
                                },
                                beforeSend: function() {
                                    $("#address").css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
                                },
                                success: function(data) {
                                    data = $.parseJSON(data);
                                    let suggesstion = '<ul id="addresses_list">';
                                    if(data.result) {
                                        for (var key in data.addresses) {
                                            suggesstion += '<li onClick="selectSuggestedAddress('+key+',this)">'+data.addresses[key]+'</li>';
                                        }
                                    } else {
                                        suggesstion += '<li>'+data.error+'</li>';
                                    }
                                    suggesstion += '</ul>'
                                    $("#suggesstion_box").show();
                                    $("#suggesstion_box").html(suggesstion);
                                    $("#address").css("background", "#FFF");
                                }
                            });
                        }, 500);
                    }
                });

                $("#address").keyup();
            });

            function selectSuggestedAddress(dpid,el) {
                $("#address").val($(el).text());
                $("#suggesstion_box").hide();
                $.ajax({
                    type: "POST",
                    url: "AddressValidation.php",
                    data: {
                        dpid:dpid,order_id: <?php echo $order_id ?>, method: "getSuggestedAddressDetails"
                    },
                    beforeSend: function() {
                        $(".suggested_address").html('').css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
                    },
                    success: function(data) {
                        $(".suggested_address").css({"background": "none"});
                        data = $.parseJSON(data);
                        if(data.result) {

                            for (var key in data.details) {
                                $("."+key).html(data.details[key]);
                                $(".change_value_"+key).attr('new_value',data.details[key]);
                            }
                        }
                    }
                });
            }


            

            function change(type_db,order_id,el) {

                $.ajax({
                    type: 'POST',
                    url: 'AddressValidation.php',
                    timeout: 20000,
                    data: {
                        order_id: order_id, type_db: type_db, method: "changeValue", new_value: $(el).attr('new_value')

                    },
                    beforeSend: function() {
                        $(el).attr('disabled',true).hide();
                        $(el).next('span').show()
                    },
                    success: function(data) {
                        $(el).next('span').html('Success')
                    }
                });
            }


        </script>


        <style>

            .result_table td {
                border: 1px solid #ccc;
                border-spacing: 12px;
                text-align: center;
                padding: 10px;
            }

            #address {
                margin: 0 auto;
                display: block;
                border: 2px solid #0033a0 !important;
                outline: none;
                font-size: 16px;
                line-height: 1.25;
                font-weight: 600;
                width: 80%;
                height: 56px !important;
                border-radius: 3px;
            }
            .ui-widget {
                margin: auto;
                width: 80%;
                border: 3px solid #ccc;
                padding: 5px;
            }

            #suggested_addresses th, #suggested_addresses td {
                padding-top: 10px;
                padding-bottom: 20px;
                text-align: center;
            }

            #suggesstion_box {
                width: 80%;
                margin: 0 auto;
            }
            #addresses_list {
                list-style: none;
                margin-top: -3px;
                padding: 0;
            }
            #addresses_list li{
                padding: 10px;
                background: #f0f0f0;
                border-bottom: #bbb9b9 1px solid;
                margin: 5px 0px;
                cursor: pointer;
            }
        </style>
        </body>
        </html>
        <?php


    }





    ?>

