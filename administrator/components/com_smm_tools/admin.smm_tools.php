<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
require_once($mainframe->getPath('admin_html'));
function view()
{
    global $database;
    $query = "SELECT * from tbl_smm_tools";
    $database->setQuery($query);
    $row = false;
    $database->loadObject($row);
    SmmTools::open($row);
}

view();
switch ($task) {
    case 'save':
        save();
        break;
    default:
}
function save()
{
    global $database;
    $free_gift_top_popup = $_REQUEST['free_gift_top_popup'] ? $_REQUEST['free_gift_top_popup'] : 0;
    $mobile_coupon_popup = $_REQUEST['mobile_coupon_popup'] ? $_REQUEST['mobile_coupon_popup'] : 0;
    $show_search_keywords = $_REQUEST['show_search_keywords'] ? $_REQUEST['show_search_keywords'] : 0;
    $keywords = $_REQUEST['keywords'] ? $_REQUEST['keywords'] : [];
    $free_gift_popup_first_product_id = $_REQUEST['free_gift_popup_first_product_id'] ? $_REQUEST['free_gift_popup_first_product_id'] : '';
    $free_gift_popup_second_product_id = $_REQUEST['free_gift_popup_second_product_id'] ? $_REQUEST['free_gift_popup_second_product_id'] : '';
    $show_free_gift_radio_buttons = $_REQUEST['show_free_gift_radio_buttons'] ? $_REQUEST['show_free_gift_radio_buttons'] : 0;
    $free_gift_radio_first_product_id = $_REQUEST['free_gift_radio_first_product_id'] ? $_REQUEST['free_gift_radio_first_product_id'] : '';
    $free_gift_radio_first_product_name = $_REQUEST['free_gift_radio_first_product_name'] ? $_REQUEST['free_gift_radio_first_product_name'] : '';
    $free_gift_radio_second_product_id = $_REQUEST['free_gift_radio_second_product_id'] ? $_REQUEST['free_gift_radio_second_product_id'] : '';
    $free_gift_radio_second_product_name = $_REQUEST['free_gift_radio_second_product_name'] ? $_REQUEST['free_gift_radio_second_product_name'] : '';

    $query = "UPDATE tbl_smm_tools"
        . "\n SET free_gift_top_popup = " . intval($free_gift_top_popup) . "
	,mobile_coupon_popup = " . intval($mobile_coupon_popup) . "
	,show_search_keywords = " . intval($show_search_keywords) . "
	,keywords = '" . $database->getEscaped(json_encode($keywords)) . "'
	,free_gift_popup_first_product_id = '" . $database->getEscaped($free_gift_popup_first_product_id) . "'
	,free_gift_popup_second_product_id = '" . $database->getEscaped($free_gift_popup_second_product_id) . "'
	,show_free_gift_radio_buttons = '" . $database->getEscaped($show_free_gift_radio_buttons) . "'
	,free_gift_radio_first_product_id = '" . $database->getEscaped($free_gift_radio_first_product_id) . "'
	,free_gift_radio_first_product_name = '" . $database->getEscaped($free_gift_radio_first_product_name) . "'
	,free_gift_radio_second_product_id = '" . $database->getEscaped($free_gift_radio_second_product_id) . "'
	,free_gift_radio_second_product_name = '" . $database->getEscaped($free_gift_radio_second_product_name) . "'
	";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

}

?>