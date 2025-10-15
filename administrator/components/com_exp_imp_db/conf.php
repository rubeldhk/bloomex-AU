<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
    $start_date = "22-2-2013";
    $end_date = "24-2-2013";

    $timestamp = time() + ($mosConfig_offset * 60 * 60);
    $payment_method_state = "";
    $payment_method = "";
    $name_on_card = "";
    $credit_card_number = "";
    $credit_card_security_code = "";
    $expire_month = "";
    $expire_year = ""; 
    $find_us = 1;
    $credit_card_security_code = "";
    
    $nPartnerOrderID = 1; 
    $mosConfig_host_1 = '192.168.100.250';
    $mosConfig_user_1 = 'locfb_dev';
    $mosConfig_password_1 = 'f2mBXB5XSktj';
    $mosConfig_db_1 = 'locfb_dev';
    $mosConfig_dbprefix_1 = 'jos_';
    $zip_cod_e = 'T8X';
?>
