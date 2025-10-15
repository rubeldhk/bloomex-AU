<?php
require_once 'ValidImageName.php';
if(isset( $_POST['name'] ) && $_POST['name'] != ''){
    if( isset( $_POST['createNewName'] ) && $_POST['createNewName'] != '' ){
        echo ValidImageName::instance()->createNewName( $_POST['name'], $_POST['extension'], $_POST['folder'] );
        echo '[--1--]';
        echo ValidImageName::instance()->createNewName( $_POST['nameProduct'], $_POST['extension'], $_POST['folder'] );
    }
    else{
        echo ValidImageName::instance()->isset_image_name_product( $_POST['name'], $_POST['extension'], $_POST['folder'] );   
    }
}
?>
