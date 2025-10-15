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
// no direct access
defined('_VALID_MOS') or die('Restricted access');

require_once( $mainframe->getPath('admin_html') );
require_once( $mainframe->getPath('class') );

$types = array('landing', 'basket', 'sympathy','flower-delivery');

switch ($task) {

    case 'save':
        SaveLandingPagesDefault($option);
        break;

    default:
        LandingPagesDefault($option);
        break;
}

function LandingPagesDefault($option) {
    global $database, $types;

    $query_info = "SELECT * FROM tbl_landing_pages_info WHERE landing_url='default' AND  `type` in ('".implode("','", $types)."') group by `type` ";
    $database->setQuery($query_info);
    $row_info = $database->loadAssocList();


    $query_cat = "SELECT count(x.product_id) as product_count,p.product_name,c.category_id,c.category_name FROM `jos_vm_category` as c
LEFT JOIN jos_vm_product_category_xref as x on x.category_id=c.category_id
LEFT JOIN jos_vm_product as p on p.product_id=x.product_id
WHERE c.`category_publish` LIKE 'Y' AND p.product_publish LIKE 'Y' group by c.category_id";
    $database->setQuery($query_cat);
    $categories = $database->loadAssocList();

    $query = "SELECT *
    FROM `jos_landing_changes`
    ORDER BY `datetime` DESC LIMIT 100";

    $database->setQuery($query);
    $changes = $database->loadObjectList();

    HTML_LandingPages_Default::LandingPages_Default($row, $option, $row_info, $categories, $changes);
}

function SaveLandingPagesDefault($option) {
    global $database, $my,$types;

    date_default_timezone_set('Australia/Sydney');


    foreach ($types as $k => $type) {
        $query = "SELECT * FROM tbl_landing_pages_info WHERE `type`='" . $type . "' AND landing_url='default' limit 1";
        $database->setQuery($query);
        if (!$database->query()) {
            echo $database->getErrorMsg();
            echo "error";
            exit(0);
        }
        //$rows = $database->loadResult();
        $lpi_obj = false;
        $database->loadObject($lpi_obj);
        if ($lpi_obj) {

            $changes = array();
            $keys = array(
                'en_content',
                'en_left_pop',
                'en_center_pop',
                'en_right_pop',
                'right_pop_publish',
                'category'
            );
            foreach ($lpi_obj as $key => $value) {
                if (in_array($key, $keys)) {
                    $new_value = $_POST[$key][$k];

                    if ($key == 'category') {
                        $new_value = serialize($new_value);
                    }

                    if ($value != $new_value) {
                        $changes[$key] = array(
                            'old' => $database->getEscaped($value),
                            'new' => $database->getEscaped($new_value)
                        );
                    }
                }
            }

            if (sizeof($changes) > 0) {
                $query = "INSERT INTO
                `jos_landing_changes`
                (
                    `type`,
                    `landing_url`,
                    `changes`,
                    `datetime`,
                    `username`
                )
                VALUES (
                    '" . $type . "',
                    'default',
                    '" . $database->getEscaped(serialize($changes)) . "',
                    '" . date("Y-m-d G:i:s") . "',
                    '" . $database->getEscaped($my->username) . "'
                )
                ";

                $database->setQuery($query);
                $database->query();
            }
            $query_info = "UPDATE tbl_landing_pages_info
                SET 
                en_content='" . $database->getEscaped($_POST['en_content'][$k]) . "',
                en_left_pop='" . $database->getEscaped($_POST['en_left_pop'][$k]) . "',
                en_center_pop='" . $database->getEscaped($_POST['en_center_pop'][$k]) . "',
                en_right_pop='" . $database->getEscaped($_POST['en_right_pop'][$k]) . "',
                right_pop_publish= IFNULL('" . $database->getEscaped($_POST['publish'][$k]) . "', 0),
                category='" . $database->getEscaped(serialize($_POST['category'][$k])) . "'
                  WHERE `type`='" . $type . "' AND landing_url='default'";

            $database->setQuery($query_info);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "error";
                exit(0);
            }
        } else {
            $query_info = "INSERT INTO  tbl_landing_pages_info
            (en_content,en_left_pop,en_center_pop,en_right_pop,right_pop_publish,category,landing_url,type)
            VALUES (
                '" . $database->getEscaped($_POST['en_content'][$k]) . "',
                '" . $database->getEscaped($_POST['en_left_pop'][$k]) . "',
                '" . $database->getEscaped($_POST['en_center_pop'][$k]) . "',
                '" . $database->getEscaped($_POST['en_right_pop'][$k]) . "',
                IFNULL('" . $database->getEscaped($_POST['publish'][$k]) . "', 0),
                '" . $database->getEscaped(serialize($_POST['category'][$k])) . "',
                'default',
                '" . $type . "')";

            $database->setQuery($query_info);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "error";
                exit(0);
            }
        }
    }
    mosRedirect("index2.php?option=$option", "Save Landing Pages Default Information Successfully");
}
