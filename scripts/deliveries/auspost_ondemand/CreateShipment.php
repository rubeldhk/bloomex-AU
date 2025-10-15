<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
if (isset($_GET['data'])) {
    $data = base64_decode(strrev($_GET['data']));
    $data_a = explode('||', $data);

    foreach ($data_a as $v) {
        $v_a = explode('|', $v);

        $_REQUEST[$v_a[0]] = $v_a[1];
    }
}
f($data,$data_a,$v_a);
date_default_timezone_set("Australia/Sydney");
$orders = explode(",", $_REQUEST['order_id']);
require_once('connectstoauspostapi.php');
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="../../resources/jquery.js"></script>
<script src="../../resources/jquery-ui.js"></script>
<script src="../../resources/bootstrap.min.js"></script>
<script src="../../resources/jquery.binarytransport.js"></script>

<link rel="stylesheet" href="../../resources/bootstrap.min.css" />
<link rel="stylesheet" href="style.css" />
<script>
    $(function () {
        $(".pickup").datepicker({"dateFormat": "yy-mm-dd", 'minDate': 0});
        $('.add-box').click(function () {
            $(this).prev('.cp_fieldset').append('<fieldset class="box_fieldset">' + $(this).prev('.cp_fieldset').find('.box_fieldset').html() + '<input type="button" class="remove-box button" value="Remove Box"></fieldset>')
        })
        $(document).on("click", ".remove-box", function () {
            $(this).parent().remove()
        })
        $('.delete_shipment').click(function () {
            var order_id = $(this).attr('order_id');
            var sender = $(this).attr('sender');
            $('#collapse' + order_id).addClass('show')
            $('#collapse' + order_id).find('.card-body').html('<p>Plase Wait... </p>')
            $.post("connectstoauspostapi.php", {action: "deleteshipment", order_id: order_id, sender: sender},
                    function (dataofconfirm) {
                        var obj = jQuery.parseJSON(dataofconfirm);
                        if (obj.error) {
                            $('#heading' + order_id).html("<span style='color:red'>" + obj.error + "</span>")
                        } else {
                            $('#heading' + order_id).html(obj.msg)
                        }
                    });

        })
        $('.submit_shipment').click(function () {
            var order_id = $(this).attr('order_id');
            var form = document.getElementById('create_shipment_form' + order_id);

            if (!$('#create_shipment_form' + order_id).find('.weight').val()) {
                alert('please add weight')
                return;
            }
            if (!$('#create_shipment_form' + order_id).find('.length').val()) {
                alert('please add length')
                return;
            }
            if (!$('#create_shipment_form' + order_id).find('.width').val()) {
                alert('please add width')
                return;
            }
            if (!$('#create_shipment_form' + order_id).find('.height').val()) {
                alert('please add height')
                return;
            }

            var fd = new FormData(form);
            var weights = [];
            var stuff = $('#create_shipment_form' + order_id).find('input[name="weight[]"]').each(function (i, item) {
                weights.push(item.value);
            });
            fd.append('weights', weights);


            var lengths = [];
            var stuff = $('#create_shipment_form' + order_id).find('input[name="length[]"]').each(function (i, item) {
                lengths.push(item.value);
            });
            fd.append('lengths', lengths);

            var widths = [];
            var stuff = $('#create_shipment_form' + order_id).find('input[name="width[]"]').each(function (i, item) {
                widths.push(item.value);
            });
            fd.append('widths', widths);

            var heights = [];
            var stuff = $('#create_shipment_form' + order_id).find('input[name="height[]"]').each(function (i, item) {
                heights.push(item.value);
            });
            fd.append('heights', heights);

            var parcels = [];
            var stuff = $('#create_shipment_form' + order_id).find('input[name="parcel[]"]').each(function (i, item) {
                parcels.push(i);
            });
            fd.append('parcels', parcels);
            fd.delete('height[]');
            fd.delete('width[]');
            fd.delete('weight[]');
            fd.delete('length[]');
            fd.delete('parcel[]');
            fd.append('action', 'createshipment');
            $('#create_shipment_form' + order_id).html('<p>Plase Wait... </p>')
            $.ajax({
                url: "connectstoauspostapi.php",
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                type: 'POST',
                success: function (dataofconfirm) {
                    if (dataofconfirm.error) {
                        $('#create_shipment_form' + order_id).html("<span style='color:red'>" + dataofconfirm.error + "</span>")
                    } else {
                        $('#create_shipment_form' + order_id).html(dataofconfirm.shipment + "<br>" + dataofconfirm.label);
                        setTimeout(function () {
                            location.reload();
                        }, 5000);
                    }
                }
            });

        })

        $('#create_manifest').click(function () {
            var orders = '';
            $('.order_checkbox:checked').each(function () {
                orders += ($(this).val()) + ',';

            });
            if (orders == '') {
                alert('Please Choose orders')
                return;
            }

            var sender = $(this).attr('sender');
            $('#create_manifest_div').html('Please Wait...');

            $.post("connectstoauspostapi.php", {action: "createmanifest", warehouse: $('.choose_warehouse').val(), orders: orders, sender: "<?php echo $_REQUEST['sender']; ?>"},
                    function (dataofconfirm) {
                        var obj = jQuery.parseJSON(dataofconfirm);
                        if (obj.error) {
                            $('#create_manifest_div').html("<br><span style='color:red'>" + obj.error + "</span>");
                        } else {
                            $('#create_manifest_div').html("<br>" + obj.msg + " <span class='download_mainfest btn btn-primary' warehouse='" + $('.choose_warehouse').val() + "' manifest_id='" + obj.mainfest_id + "'> Download Manifest</span>");
                        }
                    });

        })
        $(document).on("click", ".download_mainfest", function () {
            var manifest_id = $(this).attr('manifest_id');
            var warehouse = $(this).attr('warehouse')
            $.ajax({
                url: "connectstoauspostapi.php",
                data: {action: "printmanifest", warehouse: warehouse, manifest_id: manifest_id, sender: "<?php echo $_REQUEST['sender']; ?>"},
                type: "GET",
                dataType: 'binary',
                success: function (result) {
                    var url = URL.createObjectURL(result);
                    var $a = $('<a />', {
                        'href': url,
                        'download': 'manifest.pdf',
                        'text': "click"
                    }).hide().appendTo("body")[0].click();
                    setTimeout(function () {
                        URL.revokeObjectURL(url);
                    }, 100);
                }
            });

        })
    });
    function toggle(source) {
        checkboxes = document.getElementsByClassName('order_checkbox');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>

<div class="accordion" id="accordion">
    <?php
    foreach ($orders as $order_id) {
        require_once('bloomexorder.php');

        $BloomexOrder = new BloomexOrder();
        $BloomexOrder->GetOrderDetails(intval($order_id));
        $warehouse = $BloomexOrder->getsenderondemand();
        $auspost = new AusPost($order_id);
        $checkshipment = $BloomexOrder->get_shipment_id($order_id);
        if ($checkshipment) {
            $btn_text = 'There is already a shipment for this order. Click to open';
            $btn_class = 'btn-success';
            $manifest_info = '';
            $delete_shipment = '<button class="btn btn-danger delete_shipment" sender="' . $_REQUEST['sender'] . '" order_id="' . $order_id . '">Delete Shipment</button>';
            if ($checkshipment['json_manifest']) {
                $btn_text = 'There is already a manifest for this order. Click to open';
                $btn_class = 'btn-warning';
                $delete_shipment = '';
                $manifest_info = '<span class="span_details"><strong>Manifest Id:</strong>' . $checkshipment['manifest_id'] . '</span>';
            }
        } else {
            $btn_text = 'Create shipment';
            $btn_class = 'btn-secondary';
            $delete_shipment = '';
            $manifest_info = '';
        }
        //get shipping types list
        //$choose_type = $auspost->getaccounts();
        ?>
        <div class="card">
            <div class="card-header" id="heading<?php echo $order_id; ?>">
                <span class="span_details">
                    <?php if (!$manifest_info) { ?>
                        <input type="checkbox" class="order_checkbox" name="checkbox_order_id[]" value="<?php echo $order_id; ?>" >
                    <?php } ?>
                    <strong>Order Id:</strong><?php echo $order_id; ?>
                </span>
                <span class="span_details"><strong>Warehouse:</strong><?php echo $warehouse['WarehouseName'] ?? 'NO WAREHOUSE ASSIGNED';f($warehouse) ?></span>
                <span class="span_details"><strong>Shipment Id:</strong><?php echo $checkshipment['shipment_id']; ?></span>
                <?php echo $manifest_info; ?>
                <span class="span_details">
                    <h5 class="mb-0">
                        <button class="btn <?php echo $btn_class; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $order_id; ?>" aria-expanded="true" aria-controls="collapse<?php echo $order_id; ?>">
                            <?php echo $btn_text; ?>
                        </button>
                        <?php echo $delete_shipment; ?>
                    </h5>
                </span><hr>
            </div>

            <div id="collapse<?php echo $order_id; ?>" class="collapse" aria-labelledby="heading<?php echo $order_id; ?>" data-parent="#accordion">
                <div class="card-body">
                    <?php
                    if ($checkshipment) {
                        if ($checkshipment['json_manifest']) {
                            echo "<span class='download_mainfest btn btn-primary' warehouse='" . $checkshipment['warehouse'] . "' manifest_id='" . $checkshipment['manifest_id'] . "'> Download Manifest</span>";
                        } elseif ($checkshipment['json_label']) {
                            $labels = json_decode($checkshipment['json_label']);
                            $auspost->printlabels($labels, $_REQUEST['sender']);
                        }
                    } else {
                        ?>
                        <form action="" id="create_shipment_form<?php echo $order_id; ?>" method="post">
                            <div class="cp_fieldset">
                                <fieldset>
                                    <legend>Date</legend>
                                    <div>
                                        <span id="lable">Order Delivery Date:</span>
                                        <input type="text" name="ddate" class="pickup" value="<?php echo $BloomexOrder->_deliverydate; ?>" disabled="1" style="opacity:0.7">
                                        <br>
                                        <span id="lable">Order pickup date:</span>
                                        <input type="text" name="pickup" class="pickup" value="<?php echo date('Y-m-d'); ?>" required="">
                                    </div><br>
                                    <div>
                                        <span id="lable">Avaliable Shipping Type:</span>
                                        <select name="shipping_type">
                                            <option value="AFT">On Demand Afternoon</option>
                                            <option value="SAT">On Demand Saturday</option>
                                            <option value="TON">On Demand Tonight</option>
                                        </select>
                                    </div><br>
                                    <div>
                                        <span id="lable">Authority To Leave:</span>
                                        <input type="checkbox" class="authority_to_leave" name="authority_to_leave" checked="checked">
                                    </div>
                                </fieldset>
                                <fieldset class="box_fieldset">
                                    <legend>Parcel</legend>
                                    <div>
                                        <span id="lable">Weight:</span>
                                        <input name="weight[]" type="number" step="0.01" min="0" class="weight" value="1" required=""> kg<br>
                                        <span id="lable">Length:</span>
                                        <input type="number" step="1" name="length[]" min="5" class="length" value="50" required=""> cm<br>
                                        <span id="lable">Width:</span>
                                        <input type="number" step="1" name="width[]" min="5" class="width" value="15" required=""> cm<br>
                                        <span id="lable">Height:</span>
                                        <input type="number" step="1" name="height[]"  min="5" class="height" value="10" required=""> cm<br>
                                        <input name="parcel[]" type="hidden">
                                    </div>
                                </fieldset>
                            </div>
                            <input  type="button" class="add-box button"  value="Add Box">
                            <input name="sender" value="<?php echo $_REQUEST['sender']; ?>" type="hidden">
                            <input name="order_id" value="<?php echo $order_id; ?>" type="hidden">
                            <input name="submit" type="button" class="button submit_shipment" order_id="<?php echo $order_id; ?>"  value="Submit">

                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>



        <?php
    }
    echo "<p>Choose All : <input type='checkbox' onClick='toggle(this)' class='chooseAll'></p> <p>Choose Warehouse : " . $BloomexOrder->get_warehouse_list() . "</p>"
    ?>
    <span id="create_manifest_div">
        <input name="create_manifest" type="button" class="btn btn-success" id="create_manifest" value="Create Manifest">
    </span>
</div>
<?php
f($auspost);



