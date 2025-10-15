<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');


if (isset($_POST['submit'])) {
    $mask = ((isset($_POST['mask'])) ? $mysqli->real_escape_string(rtrim($_POST['mask'], '-')) : '');
    $type = ((isset($_POST['type'])) ? $mysqli->real_escape_string($_POST['type']) : '');
    $value = ((isset($_POST['value'])) ? (int)$_POST['value'] : '');
    
    $coupons = array();
    
    $query = "SELECT 
        `c`.`coupon_code`
    FROM `jos_vm_coupons` AS `c`
    WHERE
        `c`.`coupon_code` LIKE '" . $mask . "-%'
    ";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        while ($obj = $result->fetch_object()) {
            $coupons[] = $obj->coupon_code;
        }
    }
    
    $file = file($_FILES['csv_file']['tmp_name']);

    $inserts = array();

    foreach ($file as $line) {
        $line_a = explode('|', $line);
        $coupon_code = $mysqli->real_escape_string(trim($line_a[0]));
        
        if (!in_array($coupon_code, $coupons)) {
            $inserts[] = "(
                '" . $coupon_code . "',
                '" . $type . "',
                'gift',
                '" . $value . "'
            )";
            
            if (count($inserts) == 5000) {
                $query = "INSERT INTO `jos_vm_coupons`  
                (
                    `coupon_code`, 
                    `percent_or_total`,
                    `coupon_type`,
                    `coupon_value`
                )
                VALUES " . implode(', ', $inserts) . "
                ";

                if ($mysqli->query($query)) {
                    echo 'ok';
                }
                else {
                    echo $mysqli->error;
                }
                $inserts = array();
            }   
        }
        else {
            ?>
            Coupon <?php echo $coupon_code; ?> already exists.<br/>
            <?php
        }
    }
    
    if (count($inserts) > 0) {
        $query = "INSERT INTO `jos_vm_coupons`  
        (
            `coupon_code`, 
            `percent_or_total`,
            `coupon_type`,
            `coupon_value`
        )
        VALUES " . implode(', ', $inserts) . "
        ";

        if ($mysqli->query($query)) {
            echo 'ok';
        }
        else {
            echo $mysqli->error;
        }
        $inserts = array();
    }   
}
else {
    ?>
    <form action="?" enctype="multipart/form-data" method="post">
        File <input type="file" name="csv_file">
        <br/>
        Mask <input type="text" name="mask">
        <br/>
        Value <input type="text" name="value">
        <br/>
        Type 
        <select name="type">
            <option value="percent">Percent</option>
            <option value="total">Total</option>
        </select>
        <br/>
        <button name="submit">Do</button>
    </form>
    <?php
}
$mysqli->close();