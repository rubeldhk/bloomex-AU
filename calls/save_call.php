<?php

include_once './config.php';

$id_info = (int) $_POST['id_info'];
$number_id = (int) $_POST['number_id'];
$type = $_POST['type'];
$no_more = $_POST['no_more'];
$answer_type = $mysqli->real_escape_string($_POST['answer_type']);
$note = $mysqli->real_escape_string($_POST['note']);
$tel = $mysqli->real_escape_string($_POST['number']);
$order = $mysqli->real_escape_string($_POST['order']);
$ext = $mysqli->real_escape_string($_POST['ext']);

if ($type == 'abandonment') {
    $query = "UPDATE `tbl_cart_abandonment`
    SET 
        `call_customer`='".$no_more."'  
    WHERE `id`='".$id_info."'
    ";

    $mysqli->query($query);
} 
elseif($type == 'ocassion') {
    $query = "UPDATE `jos_vm_order_user_info`
    SET 
        `call_customer`='".$no_more."'  
    WHERE `order_id`='".$id_info."'
    ";

    $mysqli->query($query);
} 
elseif($type == 'call_back') {
    $query = "INSERT INTO `calls_callback_history`
    (
        `tel`,
        `date`,
        `order`,
        `comment`,
        `ext`
    )
    VALUES (
        '".$tel."',
        '".date('Y-m-d G:i:s')."',
        '".$order."',
        '".$note."',
        '".$ext."'
    )";
    
    $mysqli->query($query);


}

$query = "UPDATE `tbl_numbers_to_give`
    SET 
        `note`='".$note."',
        `answer_type`='".$answer_type."',
        `end_datetime`='".date('Y-m-d G:i:s')."'
    WHERE `id_info`=".$id_info."
    ";

$mysqli->query($query);

$mysqli->close();
?>