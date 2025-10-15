<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$file = file('orders.csv',FILE_IGNORE_NEW_LINES);

$inserts = array();

$gmt_offsets = array(
    'CAN' => array(
        'AB' => '-7',
        'BC' => '-8',
        'MB' => '-6',
        'NB' => '-5',
        'NL' => '-3.5',
        'NT' => '-7',
        'NS' => '-4',
        'NU' => '-5',
        'ON' => '-5',
        'PE' => '-4',
        'QC' => '-5',
        'SK' => '-6',
        'YT' => '-8'
    ),
    'AUS' => array(
        'AT' => '+10',
        'NW' => '+10',
        'NT' => '+10.5',
        'QL' => '+10',
        'SA' => '+9.5',
        'TA' => '+10',
        'VI' => '+10',
        'WA' => '+8'
    )
);

$i = 0;

$query = "SELECT 
        `ui`.`order_id`,
        `ui`.`user_id`,
        `ui`.`phone_1`,
        `ui`.`state`,
        `ui`.`country`
    FROM `jos_vm_order_user_info` AS `ui`
    WHERE 
        `ui`.`address_type`='BT'
        AND ui.order_id in (" . implode(', ', $file) . ")
    ORDER BY `ui`.`order_id` DESC 
    ";

$result = $mysqli->query($query);
echo "$query";
echo "<hr>";

$query_xmas = "INSERT INTO `tbl_calls_christmas_2024`
        (
            `order_id`,
            `number`,
            `country`,
            `state`,
            `gmt_offset`
        )
        VALUES ";

$xmas_includes = [];


while ($obj = $result->fetch_object()) {
    $matches = [];
    preg_match('/(\(?\d{1,3}\)?)[ .-]{0,1}(\d{1,4})[ .-]{0,1}(\d{1,4})[ .-]{0,1}(\d{1,4})/', $obj->phone_1, $matches);
    $number = preg_replace('/\D/', '', $matches[0]);
    $xmas_includes[] = "(
            " . $obj->order_id . ",
            '" . $number . "',
            '" . $obj->country . "',
            '" . $obj->state . "',
            '" . ($gmt_offsets[$obj->country][$obj->state]??'+10') . "'
        )";
}
$result->close();


$query_xmas .= implode(',', $xmas_includes);
$mysqli->query($query_xmas);
$mysqli->close();

echo $query_xmas;
