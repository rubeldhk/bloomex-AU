<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_WarehouseOrderLimit {
    
    public function edit_new($warehouses) {
        mosCommonHTML::loadCKeditor();
        mosCommonHTML::loadBootstrap();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Warehouses</a></li>

                </ul>
                <div id="tabs-1">
                    <table class="adminlist">
                        <thead>
                            <tr>
                                <th>
                                    Warehouse Id
                                </th>
                                <th>
                                    Warehouse Code
                                </th>
                                <th>
                                    Warehouse Name
                                </th>
                                <th>
                                    Orders Limit
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (sizeof($warehouses) > 0) {
                                foreach ($warehouses as $warehouse) {
                                    ?>
                                    <tr>
                                        <td><?php echo $warehouse->warehouse_id; ?></td>
                                        <td><?php echo $warehouse->warehouse_code; ?></td>
                                        <td><?php echo $warehouse->warehouse_name; ?></td>
                                        <td><input min="0" step="1" type="number" class="warehouse_static_limit" warehouse_id="<?php echo $warehouse->warehouse_id; ?>" value="<?php echo $warehouse->orders_count; ?>"></td>
                                    </tr>
                                    <?php
                                }
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>

            <script src="/templates/bloomex7/js/jquery.min.js" type="text/javascript"></script>
            <script src="/templates/bloomex7/js/jquery-ui-1.10.3.custom.min.js"></script>
            <link rel="stylesheet" href="/templates/bloomex7/css/smoothness/jquery-ui-1.10.3.custom.css">

            <script type="text/javascript">
                $( document ).ready(function() {
                        $('.customy').click(function(){

                            var warehouseStaticLimits = [];
                            $('.warehouse_static_limit').each(function( index ) {
                                if($( this ).val()){
                                    let w = {};
                                    w.warehouse_id = $( this ).attr('warehouse_id');
                                    w.warehouse_limit = $( this ).val();
                                    warehouseStaticLimits.push(w);
                                }
                            });

                            $.post("index2.php",
                                {
                                    warehouseStaticLimits: warehouseStaticLimits,
                                    option: "com_warehouse_order_limit",
                                    task: "update"
                                },
                                function (data) {
                                    if(data=='success'){
                                        alert('Updated Successfully')
                                        location.reload()
                                    }else{
                                        alert(data)
                                    }
                                }
                            );
                        });
                    $('#tabs').tabs();
                });
            </script>
        </form>
        <?php
    }
    

}

