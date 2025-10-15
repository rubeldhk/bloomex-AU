<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
$products = '';
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';

define('_VALID_MOS', 'true');
define('_JEXEC', 'true');

date_default_timezone_set('Australia/Sydney');
$datetime = date('Y-m-d G:i:s');

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);

$datetime_from = date('Y-m-d G:i:s', strtotime('-30 days', strtotime(date('Y-m-d G:i:s'))));
$datetime_now = date('Y-m-d G:i:s');

$query = "SELECT GROUP_CONCAT(products SEPARATOR ', ') as products 
            from (
                SELECT 
                CONCAT(p.product_id) as products
            FROM 
              jos_vm_order_item i 
              Inner Join jos_vm_orders o ON o.order_id = i.order_id 
              left join jos_vm_api2_orders as a on a.order_id = o.order_id
              inner join jos_vm_product as p on p.product_sku = i.`order_item_sku` 
                        and p.product_publish = 'Y'
              inner join jos_vm_product_options as m on m.product_id = p.product_id 
                        and m.product_sold_out = 0 
                        and m.product_out_of_season = 0
        				AND m.never_bestseller != '1'
            WHERE 
              FROM_UNIXTIME(o.cdate + 11 * 60 * 60, '%Y-%m-%d') between '$datetime_from' 
              and '$datetime_now' 
              and o.order_status not in ('X', 'O') and a.id is null
            group by 
              i.`order_item_sku` 
            order by 
              SUM(i.product_quantity) desc limit 32
        ) as r ";

$result = $mysqli->query($query);

if (!$result) {
    $result->close();
    $mysqli->close();
    die('No result');
}

if ($result->num_rows > 0) {
    $products = $result->fetch_row();
    $query = "INSERT INTO tbl_best_sellers (products,date_added) VALUES ( '{$products[0]}','{$datetime_now}')";
    $mysqli->query($query);

    $query = "UPDATE jos_vm_product_options set is_bestseller = '0'";
    $mysqli->query($query);

    $query = "UPDATE jos_vm_product_options set is_bestseller = '1' WHERE product_id in (" . $products[0] . ")";
    $mysqli->query($query);

    echo "we add new best sellers products  $products[0] <br>";
}else{
    echo "No result";
}

$result->close();
$mysqli->close();