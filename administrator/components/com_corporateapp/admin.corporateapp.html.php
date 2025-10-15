<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<?php
defined('_VALID_MOS') or die('Restricted access');

Class HTML_CorporateApp {
    
    public function edit_new($option, $row = false, $rows, $users = false, $orders = false) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Information</a></li>
                    <?php
                    if ($row)  {
                        ?>
                        <li><a href="#tabs-2">Users</a></li>
                        <li><a href="#tabs-3">Orders</a></li>
                        <?php
                    }
                    ?>
                </ul>
                <div id="tabs-1">
                    <table class="adminlist">
                        <tr>
                            <td>
                                Company name:
                            </td>
                            <td>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($row->name); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                URL:
                            </td>
                            <td>
                                <input type="text" name="url" value="<?php echo htmlspecialchars($row->url); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                User Discount:
                            </td>
                            <td>
                                <select name="shopper_group_id">
                                    <?php
                                    foreach ($rows as $group) {
                                        ?>
                                        <option value="<?php echo $group->shopper_group_id; ?>"<?php echo ($group->shopper_group_id == $row->shopper_group_id ? ' selected' : ''); ?>>
                                            <?php echo $group->shopper_group_name; ?> (<?php echo $group->shopper_group_discount; ?>)
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Corp %:
                            </td>
                            <td>
                                <input type="text" name="assign" value="<?php echo number_format($row->assign, 2, '.', ''); ?>">
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tabs-2">
                    <?php
                    if ($users AND sizeof($users) > 0) {
                        ?>
                        <table class="adminlist">
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Registrastion Date
                                </th>
                            </tr>
                            <?php
                            foreach ($users AS $user) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="/administrator/index2.php?page=admin.user_form&user_id=<?php echo $user->id; ?>&option=com_virtuemart" target="_blank">
                                            <?php echo htmlspecialchars($user->name); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($user->email); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($user->registerDate); ?>
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
                <div id="tabs-3">
                    <?php
                    if ($orders AND sizeof($orders) > 0) {
                        ?>
                        <table class="adminlist">
                            <tr>
                                <th>
                                    Order ID
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    User
                                </th>
                                <th>
                                    Order subtotal
                                </th>
                                <th>
                                    Shipping
                                </th>
                                <th>
                                    Order total
                                </th>
                                <th>
                                    Creation date
                                </th>
                                <th>
                                    Delivery date
                                </th>
                            </tr>
                            <?php
                            foreach ($orders AS $order) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="/administrator/index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart&order_id_filter=<?php echo $order->order_id; ?>" target="_blank">
                                            <?php echo $order->order_id; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($order->order_status_name); ?>
                                    </td>
                                    <td>
                                        <a href="/administrator/index2.php?page=admin.user_form&user_id=<?php echo $order->user_id; ?>&option=com_virtuemart" target="_blank">
                                            <?php echo htmlspecialchars($order->name); ?>
                                        </a>
                                    </td>
                                    <td>
                                        $<?php echo number_format($order->order_subtotal, 2, '.', ''); ?>
                                    </td>
                                    <td>
                                        $<?php echo number_format($order->order_shipping, 2, '.', ''); ?>
                                    </td>
                                    <td>
                                        $<?php echo number_format($order->order_total, 2, '.', ''); ?>
                                    </td>
                                    <td>
                                        <?php echo $order->creation_date; ?>
                                    </td>
                                    <td>
                                        <?php echo $order->ddate; ?>
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
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <script src="/templates/bloomex7/js/jquery-2.2.3.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="/templates/bloomex7/css/smoothness/jquery-ui-1.10.3.custom.css">
        <script src="/templates/bloomex7/js/jquery-ui-1.10.3.custom.min.js"></script>
        <style>
            table.adminlist a {
                color: #C64934 !important;
            }
        </style>
        <script type="text/javascript">
            $( document ).ready(function() {
                
                $('#tabs').tabs();
                
            });
        </script>
        <?php
    }
    
    public function default_list($option, $rows, $pageNav, $search) {
        ?>
        <style>
            table.head_table {
                width: 450px; 
                float: left; 
                margin-bottom: 15px;
            }
            #update-links-button {
                font-size: 14px;
                color: #ffffff;
                padding: 10px;
                background-color: #adacac;
                border: 1px solid #adacac;
            }
            #update-links-button:hover {
                text-decoration: none;
                background-color: #ffffff;
                color: #adacac;
            }
            #update-links-loader {
                color: #adacac;
                font-size: 10px;
                margin-left: 10px;
                font-size: 14px;
            }
            
        </style>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading head_table">
                <tr>
                    <td align="right">
                        <a href="#" id="update-links-button">
                            Update Links Database 
                        </a>
                        <span id="update-links-loader">
                        </span>
                    </td>
                    <td align="left">
                        Filter:
                    </td>
                    <td>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="text_area" onChange="document.adminForm.submit();" />
                    </td>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="2%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                    </th>
                    <th style="text-align: left;" width="20%">
                        Name
                    </th>
                    <th style="text-align: left;" width="20%">
                        URL
                    </th>
                    <th style="text-align: left;" width="10%">
                        User Discount
                    </th>
                    <th style="text-align: left;" width="10%">
                        Assigned to corporation
                    </th>
                    <th style="text-align: left;" width="5%">
                        Users
                    </th>
                    <th style="text-align: left;" width="5%">
                        Orders
                    </th>
                </tr>
                <?php
                $i = 0;
                foreach ($rows as $row) {
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="row<?php echo $i; ?>">
                        <td align="center">
                            <?php echo $pageNav->rowNumber($i); ?>
                        </td>
                        <td align="center">
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <a href="./index2.php?option=<?php echo $option; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
                                <?php echo htmlspecialchars($row->name); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->url); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->shopper_group_name.' ('.$row->shopper_group_discount.'%)'); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->assign.'%'); ?>
                        </td>
                        <td>
                            <?php echo $row->count_users; ?>
                        </td>
                        <td>
                            <?php echo $row->count_orders; ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </table>
            <?php
            echo $pageNav->getListFooter();
            ?>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
        <script type="text/javascript">
            $( document ).ready(function() {
                
                $('#update-links-button').click(function(event) {
                    event.preventDefault();
                    
                    $('#update-links-loader').html('Loading...');
                     $.ajax({
                        data: {
                            manager: 'corporateapp'
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: '/updateseolinks.php',
                        success: function(data) {
                            if (data.result) {
                                $('#update-links-loader').text('Success.');
                            }
                            else {
                                $('#update-links-loader').text('Error.');
                            }
                        }
                    });
                });
                
            });
        </script>
        <?php
    }
    
}

?>