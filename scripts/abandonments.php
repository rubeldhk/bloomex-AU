<?php

include_once '../configuration.php';

if (!$mosConfig_host)
{
    die('no config');
}

$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

if (!$link) 
{
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($mosConfig_db)) 
{
    die('Could not select database: ' . mysql_error());
}


function get_retail_price($product_id) 
{
    $price_info = array();
    
    $q = mysql_query("SELECT `vendor_id`, `product_tax_id` FROM `jos_vm_product` WHERE `product_id`=".$product_id."");

    if (mysql_num_rows($q) > 0)
    {
        $out = mysql_fetch_array($q);

        $vendor_id = $out['vendor_id'];
        $product_tax_id = $out['product_tax_id'];

        $q = mysql_query("SELECT `shopper_group_id` FROM `jos_vm_shopper_group` WHERE `vendor_id`='$vendor_id' AND `default`='1'");
        $out = mysql_fetch_array($q);

        $default_shopper_group_id = $out['shopper_group_id'];

        $q = mysql_query("SELECT `product_price`,`product_currency`,`saving_price`,`compare_at` FROM `jos_vm_product_price` WHERE `product_id`='$product_id' AND `shopper_group_id`='$default_shopper_group_id'");
        $out = mysql_fetch_array($q);

        $price_info["product_price"] = $out['product_price'];
        $price_info["saving_price"] = $out['saving_price'];
        $price_info["product_currency"] = $out['product_currency'];
        $price_info["compare_at"] = $out['compare_at'];
        $price_info["product_tax_id"] = $product_tax_id;
    }
    
    return $price_info;
}
?>
<html xmlns="https://www.w3.org/1999/xhtml">
    <head>
        <title>Cart abandonment report</title>
        <link rel="stylesheet" href="../templates/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../templates/bootstrap/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="../templates/bootstrap/css/bootstrap-datetimepicker.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="../templates/bootstrap/js/bootstrap.min.js"></script>
        <script src="../templates/bootstrap/js/moment.js"></script>
        <script src="../templates/bootstrap/js/bootstrap-datetimepicker.js"></script>
    </head>
    
    <body>
        <div class="container">
            <?php
            if (!isset($_POST['submit']))
            {
            ?>
                <div class="row">
                    <div class="col-md-4">
                        <h2>Cart abandonment report</h2>
                        <form role="form" method="post">
                            <div class="form-group">
                                <label for="start_date">Start date</label>
                                <div class="input-group date" id="start_date_picker">
                                    <input type="text" class="form-control" id="start_date" name="start_date"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="end_date">End date</label>
                                <div class="input-group date" id="end_date_picker">
                                    <input type="text" class="form-control" id="end_date" name="end_date"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-default">Send</button>
                        </form>
                    </div>
                </div>

                <script type="text/javascript">
                    $(function () {
                        $('#start_date_picker').datetimepicker({
                            format: 'YYYY-MM-DD'
                        });
                        $('#end_date_picker').datetimepicker({
                            format: 'YYYY-MM-DD'
                        });
                    });
                </script>
            <?php
            }
            else
            {
                $sql = mysql_query("SELECT 
                    `c`.`id`,
                    `c`.`user_id`,
                    FROM_UNIXTIME(`c`.`date` + 11*60*60) AS 'date',
                    `c`.`number`,
                    `c`.`first_name`,
                    `c`.`link`,
                    `u`.`email`,
                    `u`.`username`
                FROM `tbl_cart_abandonment` AS `c`
                    LEFT JOIN `jos_users` AS `u` 
                        ON
                            `u`.`id`=`c`.`user_id`
                WHERE 
                    `c`.`status`!='finished' AND `c`.`number`!= '' 
                    AND 
                    FROM_UNIXTIME((`c`.`date`+11*60*60)) BETWEEN '".mysql_real_escape_string($_POST['start_date'])." 00:00:00' AND '".mysql_real_escape_string($_POST['end_date'])." 23:59:59' 
                ORDER BY `c`.`date` DESC");
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <h2>Result for <?php echo htmlspecialchars($_POST['start_date']); ?> - <?php echo htmlspecialchars($_POST['end_date']); ?></h2>
                <?php
                if (mysql_num_rows($sql) > 0)
                {
                    ?>
                    <table class="table table-responsive table-striped">
                        <tbody>
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    Date
                                </th>
                                <th>
                                    User id
                                </th>
                                <th>
                                    Username
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Number
                                </th>
                                <th>
                                    Value of products
                                </th>
                                <th>
                                    Billing state
                                </th>
                                <th>
                                    Billing city
                                </th>
                                <th>
                                    Shipping state
                                </th>
                                <th>
                                    Shipping city
                                </th>
                            </tr>
                    <?php
                    while ($out = mysql_fetch_array($sql))
                    {
                        $link = str_replace('?cart_products=', '', $out['link']);

                        $link_a = explode(';', $link);

                        $products_a = array();

                        foreach ($link_a as $link_one)
                        {
                            $link_one_a = explode(',', $link_one);

                            if ($link_one_a[0] > 0)
                            {
                                $products_a[$link_one_a[0]] = $link_one_a[1];   
                            }
                        }

                        $out_shopper_discount = array();
                        $out_shopper_discount['shopper_group_discount'] = 0;

                        $sql_shopper_discount = mysql_query("SELECT 
                            `SG`.`shopper_group_discount` 
                        FROM `jos_vm_shopper_vendor_xref` AS `SVX` 
                            INNER JOIN `jos_vm_shopper_group` AS `SG` 
                            ON 
                                `SG`.`shopper_group_id`=`SVX`.`shopper_group_id`	
                        WHERE 
                            `SVX`.`user_id`=".$out['user_id']." LIMIT 1");

                        if (mysql_num_rows($sql_shopper_discount) > 0)
                        {
                            $out_shopper_discount = mysql_fetch_array($sql_shopper_discount);
                        }

                        $shopper_discount = 0;

                        if (!empty($out_shopper_discount['shopper_group_discount'])) 
                        {
                            if (!empty($out_shopper_discount['shopper_group_discount']) && $out_shopper_discount['shopper_group_discount'] > 0) 
                            {
                                $shopper_discount = floatval($out_shopper_discount['shopper_group_discount']) / 100;
                            }
                        }
                        
                        $order_subtotal = 0;
                        
                        foreach ($products_a as $product_id => $product_quantity)
                        {
                            $aPrice = get_retail_price($product_id);

                            if (!empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0) 
                            {
                                $product_price = ($aPrice["product_price"] + $aPrice["product_price"] * 0.1) - $aPrice["saving_price"];
                            } 
                            else 
                            {
                                $product_price = $aPrice["product_price"];
                                $product_price = $product_price + $product_price * 0.1;
                            }

                            $product_price = floatval($product_price) - ( floatval($product_price) * floatval($nShopperGroupDiscount) );

                            $order_subtotal += $product_price*$product_quantity;
                        }
                        
                        $order_subtotal = number_format($order_subtotal, 2, '.', '');
                        
                        $sql_s_info = mysql_query("SELECT 
                            `city`,
                            `state`
                        FROM `jos_vm_user_info`
                        WHERE `address_type`='ST' AND `user_id`=".$out['user_id']."");
                        
                        $out_s_info = mysql_fetch_array($sql_s_info);
                        
                        $sql_b_info = mysql_query("SELECT 
                            `city`,
                            `state`
                        FROM `jos_vm_user_info`
                        WHERE `address_type`='BT' AND `user_id`=".$out['user_id']."");
                        
                        $out_b_info = mysql_fetch_array($sql_b_info);
                        ?>
                        <tr>
                            <td>
                                <?php echo $out['id']; ?>
                            </td>
                            <td>
                                <?php echo $out['date']; ?>
                            </td>
                            <td>
                                <?php echo $out['user_id']; ?>
                            </td>
                            <td>
                                <?php echo $out['username']; ?>
                            </td>
                            <td>
                                <?php echo $out['email']; ?>
                            </td>
                            <td>
                                <?php echo $out['number']; ?>
                            </td>
                            <td>
                                $<?php echo $order_subtotal; ?>
                            </td>
                            <td>
                                <?php echo $out_b_info['state']; ?>
                            </td>
                            <td>
                                <?php echo $out_b_info['city']; ?>
                            </td>
                            <td>
                                <?php echo $out_s_info['state']; ?>
                            </td>
                            <td>
                                <?php echo $out_s_info['city']; ?>
                            </td>
                        </tr>
                            
                        <?php
                    }
                    ?>
                        </tbody>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <h2>No rows</h2>
                    <?php
                }
                ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </body>
</html>