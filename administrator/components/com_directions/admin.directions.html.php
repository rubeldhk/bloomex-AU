<?php
defined('_VALID_MOS') or die('Restricted access');

Class HTML_directions {

    public static function edit_new($option, $route) {
        ?>
        <script src="./templates/joomla_admin/js/tabs.js" type="text/javascript"></script>
        <form action="index2.php?option=com_directions" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>
                        Route #<?php echo $route->id; ?>( <?php echo $route->warehouse_name; ?>)
                    </td>
                    <td>
                        <a href="/administrator/index2.php?option=com_deliver&act=driver_option&task=editA&hidemainmenu=1&id=<?php echo $route->driver_id; ?>" >Driver [ID]</a> : 
                        <select name="driver_id">
                            <?php
                            foreach ($route->drivers as $driver) {
                                ?>
                                <option value="<?php echo $driver->id; ?>" <?php echo (($route->driver_id == $driver->id) ? 'selected' : ''); ?>>
                                    <?php echo $driver->name; ?>&nbsp;[<?php echo $driver->id; ?>]
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <div id="tabs">
                            <ul>
                                <li tab-id="#tabs-1">Orders</li>
                                <li tab-id="#tabs-2">History</li>
                            </ul>
                            <div id="tabs-1">
                                <table class="adminlist routes">
                                    <tr>
                                        <th></th>
                                        <th>Order ID</th>
                                        <th>Status</th>
                                        <th>Billable</th>
                                        <th>Zone</th>
                                        <th>Rate</th>
                                        <th>Driver Rate</th>
                                        <th>Last Driver Update</th>
                                        <th>Delivered</th>
                                    </tr>
                                    <?php
                                    foreach ($route->orders as $order) {
                                        if ($order->status == '0') {
                                            $class = 'remaining_count';
                                        } elseif ($order->status == '1') {
                                            $class = 'done_count';
                                        } elseif ($order->status == '2') {
                                            $class = 'investigation_count';
                                        } elseif ($order->status == '3') {
                                            $class = 'questionable_count';
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $order->queue; ?></td>
                                            <td>
                                                <a target="_blank" href="/administrator/index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart&order_id_filter=<?php echo $order->order_id; ?>">
                                                    <?php echo $order->order_id; ?>
                                                </a>
                                            </td>
                                            <td class="<?php echo $class; ?>"></td>
                                            <td>
                                                <select name="billable[]">
                                                    <option value="1" <?php echo (($order->billable == '1') ? 'selected' : ''); ?>>
                                                        Yes
                                                    </option>
                                                    <option value="0" <?php echo (($order->billable == '0') ? 'selected' : ''); ?>>
                                                        No
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rates[]" title="Zone">
                                                    <option value="0">
                                                        Choose
                                                    </option>
                                                    <?php
                                                    foreach ($route->rates AS $rate_obj) {
                                                        ?>
                                                        <option value="<?php echo $rate_obj->id_rate; ?>" <?php echo (($order->id_rate == $rate_obj->id_rate) ? 'selected' : ''); ?>>
                                                            <?php echo $rate_obj->name; ?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" placeholder="0.00" required name="custom_rates[]" min="0" value="<?php echo $order->rate; ?>" step="0.50" title="Rate" >
                                            </td>
                                            <td>
                                                <input type="number" placeholder="0.00" required name="custom_rates_driver[]" min="0" value="<?php echo $order->driver_rate; ?>" step="0.50" title="Driver Rate" >
                                            </td>
                                            <td><?php echo $order->last_update_datetime; ?></td>
                                            <td><?php echo $order->delivered_datetime; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>
                            <div id="tabs-2">
                                <?php
                                if (count($route->histories) > 0) {
                                    ?>
                                    <table class="adminlist">
                                        <tr>
                                            <th>Text</th>
                                            <th>Username</th>
                                            <th>Date</th>
                                        </tr>
                                        <?php
                                        foreach ($route->histories as $history) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $history->text; ?>
                                                </td>
                                                <td>
                                                    <?php echo $history->username; ?>
                                                </td>
                                                <td>
                                                    <?php echo $history->datetime; ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <img onload="document.getElementById('show_map').style.display = 'none';this.style.display = 'block'" onerror="document.getElementById('show_map').style.display = 'block';this.style.display = 'none'" src="<?php echo $route->map_image; ?>" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $route->id; ?>" />
            <input type="hidden" name="scan_session_token" value="<?php echo $route->scan_session_token; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <script type="text/javascript" language="javascript">
            function setTabs(id) {
                jQuery('#'+id+' > ul > li:first-of-type').addClass('active');
                jQuery('#'+id+' > div:not(:first-of-type)').hide();

                jQuery('#'+id+' > ul > li').click(function(e) {
                    e.preventDefault();

                    jQuery('#'+id+' > ul > li').removeClass('active');
                    jQuery('#'+id+' > div').hide();
                    jQuery(this).addClass('active');
                    jQuery(''+jQuery(this).attr('tab-id')+'').show();
                });
            }
            jQuery(document).ready(function () {

                setTabs('tabs');

            });

        </script>

        <style>

        </style>
        <?php
    }

    public static function default_list($option, $my, $warehouse_id_selected, $driver_id_selected, $show_completed, $order_route_id_selected, $rows, $warehouses, $drivers, $pageNav, $date_from, $date_to, $only_get_unpublished) {
        mosCommonHTML::loadCalendar();
        ?>
        <style>
            table.adminlist.routes tr {
                background-color: #fff;
            }
            table.adminlist.routes tr:hover {
                background-color: #f1f1f1;
            }
            table.adminlist.routes td.remaining_count {
                font-weight: bold;
                background-color: #f8f846;
                text-align: center;
            }
            table.adminlist.routes td.done_count {
                font-weight: bold;
                background-color: #53d053;
                text-align: center;
            }
            table.adminlist.routes td.investigation_count {
                font-weight: bold;
                background-color: #fa6363;
                text-align: center;
            }
            table.adminlist.routes td.questionable_count {
                font-weight: bold;
                background-color: #FFA011;
                text-align: center;
            }
            table.adminlist.routes tr.completed {
                display: none;
            }
            div.filter {
                float: left;
                padding: 10px 0px;
            }
            tr.invisible {
                display: none;
                background-color: #f3f3f3 !important;
            }
            tr.invisible div.loader {
                width: 100%;
                display: none;
                text-align: center;
            }
            tr.invisible div.close {
                display: none;
                margin-right: 20px;
                float: left;
            }
            tr.invisible div.close img:hover {
                opacity: 0.7;
                cursor: pointer;
            }
            tr.invisible td {
                padding: 0px;
            }
            div.orders {
                float: left;
                width: 50%;
            }
            table.orders {
                width: 100%;
            }
            table.orders tr:hover {
                background-color: #fff !important;
            }
            tr.invisible table.orders td {
                padding: 4px;
            }
        </style>
        <?php echo ($only_get_unpublished) ? '<div >Only<span class="message"> Unpublished</span> routes are shown</div>' : ''; ?>
        <form action="index2.php?option=com_directions" id="route_form" method="post" name="adminForm">
            <div class="filter">
                Warehouse
                <select id="warehouse" name="warehouse">
                    <option value="">All</option>
                    <?php
                    foreach ($warehouses as $warehouse) {
                        ?>
                        <option value="<?php echo $warehouse->warehouse_id; ?>" <?php echo ($warehouse_id_selected == $warehouse->warehouse_id) ? 'selected' : ''; ?>><?php echo $warehouse->warehouse_name; ?></option>
                        <?php
                    }
                    ?>
                </select>
                Driver
                <?php if ($warehouse_id_selected) { ?>
                    <select id="driver" name="driver">
                        <option value="">All</option>
                        <?php
                        foreach ($drivers as $driver) {
                            ?>
                            <option value="<?php echo $driver->id; ?>" <?php echo ($driver_id_selected == $driver->id) ? 'selected' : ''; ?>><?php echo $driver->name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                <?php } else { ?>
                    <input disabled="true" value="select warehouse first">
                <?php } ?>
                Order id/Route id <input type="text" name="order_route_id" id="order_route_id" value="<?php echo $order_route_id_selected; ?>">
                Date From <input type="text"  class="text_area" name="date_from" id="date_from"  value="<?php echo $date_from; ?>">
                <input type="reset" class="button" value="..." onclick="return showCalendar('date_from', 'y-mm-dd');" />
                Date To <input type="text" class="text_area" name="date_to" id="date_to"  value="<?php echo $date_to; ?>">
                <input type="reset" class="button" value="..." onclick="return showCalendar('date_to', 'y-mm-dd');" />
                <input type="checkbox" id="zerovalue" name="zerovalue" />
                <label for="scales">0 Rate</label>
                <input type="submit" class="button" value="filter" />
                <input type="button" class="button" onclick="resetfilter()" value="reset" />


            </div>
            <table class="adminlist routes">
                <tr>
                    <?php
                    //if ($my->usertype == 'Super Administrator') {
                    ?>
                    <th style="text-align: left;" width="5%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                    </th>
                    <?php //} ?>
                    <th style="text-align: left;" width="20%">
                        Direction
                    </th>
                    <th style="text-align: left;" width="20%">
                        Warehouse
                    </th>
                    <th style="text-align: left;" width="20%">
                        Driver
                    </th>
                    <th style="text-align: left;" width="5%">
                        Remaining
                    </th>
                    <th style="text-align: left;" width="5%">
                        Done
                    </th>
                    <th style="text-align: left;" width="5%">
                        Investigation
                    </th>
                    <th style="text-align: left;" width="5%">
                        Questionable
                    </th>
                    <th style="text-align: left;" width="5%">
                        Username
                    </th>
                    <th style="text-align: left;" width="10%">
                        Date
                    </th>
                </tr>
                <?php
                $i = 0;
                foreach ($rows as $row) {
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="row<?php echo $i; ?>">
                        <?php
                        //if ($my->usertype == 'Super Administrator') {
                        ?>
                        <td>
                            <?php echo mosCommonHTML::CheckedOutProcessing($row, $i) ?>
                        </td>
                        <?php //}?>
                        <td>
                            <a target="_blank" class="route" href="/administrator/index2.php?option=<?php echo $option; ?>&amp;task=edit&amp;id=<?php echo htmlspecialchars($row->id); ?>" route_id="<?php echo htmlspecialchars($row->id); ?>">
                                Route #<?php echo $row->id; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->warehouse_name); ?>
                        </td>
                        <td>
                            <a target="_blank" href="/administrator/index2.php?option=com_deliver&amp;act=driver_option&amp;task=editA&amp;hidemainmenu=1&amp;id=<?php echo $row->driver_id; ?>">
                                <?php echo htmlspecialchars($row->service_name); ?></a> [<?php echo $row->driver_id; ?>]
                        </td>
                        <td class="remaining_count">
                            <?php echo htmlspecialchars($row->remaining_count); ?>
                        </td>
                        <td class="done_count">
                            <?php echo htmlspecialchars($row->done_count); ?>
                        </td>
                        <td class="investigation_count">
                            <?php echo htmlspecialchars($row->investigation_count); ?>
                        </td>
                        <td class="questionable_count">
                            <?php echo htmlspecialchars($row->questionable_count); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->username); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->datetime); ?>
                        </td>
                    </tr>
                    <tr class="invisible" route_id="<?php echo htmlspecialchars($row->id); ?>">
                        <td colspan="8">
                            <div class="loader">
                                <img src="/administrator/images/direction_loader.gif" alt="Loading..." />
                            </div>
                            <div class="close">
                                <img src="/administrator/images/direction_close_4.png" alt="Close" />
                            </div>
                            <div class="orders"></div>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
        <script type="text/javascript">
            function resetfilter() {

                jQuery('#date_from').val('');
                jQuery('#date_to').val('');
                jQuery('#order_route_id').val('');
                jQuery('#driver').val('');
                jQuery('#warehouse').val('');
                jQuery('#zezovalue').val('');
                jQuery('form[name="adminForm"]').submit();

            }
            jQuery(document).ready(function () {

                jQuery('#warehouse').change(function (event) {
                    jQuery('#driver').val('0');
                    jQuery('form[name="adminForm"]').submit();
                });

                jQuery('#show_completed').click(function (event) {
                    jQuery('form[name="adminForm"]').submit();
                });

                jQuery('#driver').change(function (event) {
                    jQuery('form[name="adminForm"]').submit();
                });


                jQuery('div.close img').click(function (event) {
                    event.preventDefault();
                    jQuery('tr.invisible, tr.invisible td div.close, tr.invisible td div.loader, tr.invisible td div.orders').hide();
                });

            });
        </script>
        <?php
    }

}
