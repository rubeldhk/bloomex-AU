<?php

/**
 * @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Category
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_VALID_MOS') or die('Restricted access');

require_once( $mainframe->getPath('admin_html') );

//$mosConfig_live_site = $mainframe->getCfg('live_site');

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

switch (strtolower($task)) {
    case 'edit_ing':
        ?>
        <script type="text/javascript">




            function SaveIngredient(product_id_real)
            {
                var product_name = jQuery("#product_name").val();
                var landing_price = jQuery("#landing_price").val();
                var foreign_price = jQuery("#foreign_price").val();
                var product_id = jQuery("#product_id").val();
                var type = jQuery("#type").val();
                var bold = jQuery("#bold").attr("checked") ? 1 : 0;

                jQuery("#add_product").html('Loading...');

                jQuery.ajax({
                    data:
                            {
                                option: 'com_ingredients',
                                task: 'save_ing',
                                product_id_real: product_id_real,
                                product_name: product_name,
                                landing_price: landing_price,
                                foreign_price: foreign_price,
                                product_id: product_id,
                                bold: bold,
                                type: type
                            },
                    type: "POST",
                    dataType: "html",
                    url: "index2.php",
                    success: function (data)
                    {
                        jQuery("#add_product").html('<font color="green">Success</font> Updating page...');
                        setTimeout(function () {
                            window.location.href = "index2.php?option=com_ingredients";
                        }, 1000);
                    }
                });
            }

        </script>

        <?php

        edit_ing();

        break;

    case 'add_new':
        add_new();
        break;
    case 'download_list':
        download_list();
        break;
    case 'upload_list':
        upload_list();
        break;

    case 'update_price':

        update_price();

        break;

    case 'delete_ing':

        delete_ing();

        break;

    case 'save_ing':

        save_ing();

        break;

    default:
        ?>



        <?php

        default_list();
}

function save_ing() {
    global $database;

    $product_id_real = (int) $_REQUEST['product_id_real'];
    $product_id = (int) $_REQUEST['product_id'];
    $product_name = $database->getEscaped($_REQUEST['product_name']);
    $landing_price = str_replace(',', '.', $database->getEscaped($_REQUEST['landing_price']));
    $foreign_price = str_replace(',', '.', $database->getEscaped($_REQUEST['foreign_price']));
    $bold = (int) $_REQUEST['bold'];
    $type = $database->getEscaped($_REQUEST['type']);
    $sql_i = "UPDATE `product_ingredient_options`
    SET 
        `igo_id`=" . $product_id . ", 
        `igo_product_name`='" . $product_name . "',
        `landing_price`='" . $landing_price . "',
        `foreign_price`='" . $foreign_price . "',
        `bold`='" . $bold . "',
        `type`='" . $type . "'
    WHERE `igo_id`=" . $product_id_real . "";

    $database->setQuery($sql_i);
    $database->query();

    exit(0);
}

function edit_ing() {
    global $database;

    $product_id = (int) $_REQUEST['id'];

    $query = "SELECT * FROM `product_ingredient_options` WHERE `igo_id`=" . $product_id . " LIMIT 1";

    $database->setQuery($query);
    $lists = $database->loadObjectList();

    $list = $lists[0];

    HTML_Disp::edit_ing($list);
}

function delete_ing() {
    global $database;

    $product_id = (int) $_REQUEST['product_id'];

    $sql_i = "DELETE FROM `product_ingredient_options` WHERE `igo_id`=" . $product_id . "";

    $database->setQuery($sql_i);
    $database->query();

    exit(0);
}

function download_list() {
    global $database;
    header('Content-Type: text/csv; utf-8');
    header('Content-Disposition: attachment; filename="ingredient-list-au.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    ob_clean();
    $query = "SELECT * from product_ingredient_options order by igo_id";
    $database->setQuery($query);
    $list = $database->loadRowList();
    $handle = fopen("php://output", "w");
    $headers = array('id', 'Name', 'Price AUD', 'Price USD', 'Add-on','Type');
    fputcsv($handle, $headers);
    foreach ($list as $v) {
        $row = array($v[0], $v[1], number_format($v[4], 4), number_format($v[3], 4), $v[2], $v[5]);
        fputcsv($handle, $row);
    }
    fclose($handle);
    die();
}

function upload_list() {
    global $database;
    $csv = $_REQUEST['ingredient_list'];
    $lines = explode(PHP_EOL, $csv);
    $array = array();
    foreach ($lines as $line) {
        $array[] = str_getcsv($line);
    }
    unset($array[0]);
   $query = "INSERT INTO product_ingredient_options (igo_id,igo_product_name,landing_price,foreign_price,bold,`type`) VALUES ";
    foreach ($array as $v) {
        if ($v[0]) {
            $bold = ($v[4])?1:0;
            $query .= sprintf("(%d,'%s','%f','%f','%d','%s' ),", $database->getEscaped($v[0]), $database->getEscaped($v[1]), $database->getEscaped($v[2]), $database->getEscaped($v[3]), $bold,$database->getEscaped($v[5]));
        }
    }
    $query = rtrim($query, ",");
    $query .= " ON DUPLICATE KEY UPDATE igo_product_name=values(igo_product_name),foreign_price=values(foreign_price),landing_price=values(landing_price),bold=values(bold),type=values(type);";
    $database->setQuery($query);
    if (!$database->query()) {
        die(__LINE__ . 'Update error: ' . $database->_errorMsg);
    } else {
        die("all ok");
    }
}

function add_new() {
    global $database;

    $product_name = $database->getEscaped($_REQUEST['product_name']);
    $type = $database->getEscaped($_REQUEST['type']);
    $landing_price = str_replace(',', '.', $database->getEscaped($_REQUEST['landing_price']));
    $foreign_price = str_replace(',', '.', $database->getEscaped($_REQUEST['foreign_price']));
    $bold = (int) $_REQUEST['bold'];

    echo '<pre>';
    print_r($_REQUEST);
    echo '</pre>';

    $sql_i = "INSERT INTO `product_ingredient_options`
    (
        `igo_product_name`,
        `bold`,
        `landing_price`,
        `foreign_price`,
        `type`
    ) 
    VALUES (
        '" . $product_name . "',
        '" . $bold . "',
        '" . $landing_price . "',
        '" . $foreign_price . "',
        '" . $type . "'
    )";

    $database->setQuery($sql_i);
    $database->query();

    echo $sql_i;

    exit(0);
}

function update_price() {
    global $database;
    $rate = (float) str_replace(',', '.', $database->getEscaped($_REQUEST['rate']));
    if ($rate) {
        $sql_update = "UPDATE  product_ingredient_options  SET landing_price = ROUND(foreign_price*" . $rate . ",2) where foreign_price > 0 ";
        $database->setQuery($sql_update);
        if (!$database->query()) {
            echo $sql_update;
            die(__LINE__ . 'Update error: ' . $database->_errorMsg);
        }
        echo '<font color="green">Price Updated Successfully</font>';
    } else {
        echo 'Rate Can\'t be null';
    }
    exit(0);
}

function default_list() {
    global $database, $mainframe;

    //$config = & JFactory::getConfig();

    $query = "SELECT * FROM `product_ingredient_options` ORDER BY `igo_id` ASC";

    $database->setQuery($query);
    $lists = $database->loadObjectList();


    HTML_Disp::default_list($lists);
}
