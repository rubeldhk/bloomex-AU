<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
define('_VALID_MOS', true);
define('_JEXEC', true);
include 'configuration.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';
global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);


 function getCanonicalProduct($alias, $relative = false) {
     global $database;
    $query = "SELECT
            `c`.`alias`
        FROM `jos_vm_product` AS `p`
        INNER JOIN `jos_vm_product_options` AS `po`
            ON `po`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_category` AS `c`
            ON `c`.`category_id`=`po`.`canonical_category_id`
            AND
            `c`.`category_publish`='Y'
        WHERE
            `p`.`alias`='" . $alias . "'
            AND
            `p`.`product_publish`='Y'
        ";



    $database->setQuery($query);

    $category_obj = false;

    $canonical = '';

    if ($database->loadObject($category_obj)) {
        $canonical = getCanonicalCategory($category_obj->alias, $relative);
    } else {
        $query = "SELECT
                `c`.`alias`
            FROM `jos_vm_product` AS `p`
            INNER JOIN `jos_vm_product_category_xref` AS `pc_x`
                ON
                `pc_x`.`product_id`=`p`.`product_id`
            INNER JOIN `jos_vm_category` AS `c`
                ON
                `c`.`category_id`=`pc_x`.`category_id`
                AND
                `c`.`category_publish`='Y'
            WHERE 
                `p`.`alias`='" . $alias . "'
                AND
                `p`.`product_publish`='Y'
            GROUP BY `c`.`alias`
            ORDER BY `c`.`category_id` DESC LIMIT 1";

        $database->setQuery($query);

        $category_obj = false;

        $canonical = '';
        $database->loadObject($category_obj);
        if($category_obj){
            $canonical = getCanonicalCategory($category_obj->alias, $relative);
        }
    }
    if($canonical){
        return $canonical . $alias . '/';
    }else{
        return '';
    }
}
 function getCanonicalCategory($alias, $relative = false) {
    global $mosConfig_live_site,$database;

    $aliases = [];

    $category_parent_id = 1;

    $i = 1;
    while ($category_parent_id > 0) {
        $query = "SELECT
                `c`.`category_id`,
                `c`.`alias`,
                `c2`.`alias` AS `parent_alias`,
                `c_x`.`category_parent_id`
            FROM `jos_vm_category` AS `c`
            LEFT JOIN `jos_vm_category_xref` AS `c_x`
                ON `c_x`.`category_child_id`=`c`.`category_id`
            LEFT JOIN `jos_vm_category` AS `c2`
                ON `c2`.`category_id`=`c_x`.`category_parent_id`
            WHERE
                `c`.`alias`='" . $alias . "'
                AND  
                `c`.`category_publish`='Y'
            ";

        $database->setQuery($query);

        $category_obj = false;
        if ($database->loadObject($category_obj)) {

            if ($relative == false) {
                if (isset($GLOBALS['city_obj']) AND in_array($category_obj->category_id, [18, 183]) AND $i == 1) {
                    Switch ($category_obj->category_id) {
                        case 18:
                            $aliases[] = 'florist-online';
                            break;
                        case 183:
                            $aliases[] = 'gift-hamper-basket';
                            break;
                    }
                    $aliases[] = $GLOBALS['city_obj']->url;
                    $category_parent_id = 0;
                    return $mosConfig_live_site . (sizeof($aliases) > 0 ? '/' . implode('/', $aliases) : '') . '/';
                    break;
                }
            }
            $alias = $category_obj->parent_alias;
            $aliases[] = $category_obj->alias;
            $category_parent_id = $category_obj->category_parent_id;
        } else {
            $category_parent_id = 0;
        }

        $i++;
    }

    return ($relative ? '' : $mosConfig_live_site) . ((!$relative AND isset($GLOBALS['city_obj'])) ? '/' . $GLOBALS['city_obj']->url : '') . (sizeof($aliases) > 0 ? '/' . implode('/', array_reverse($aliases)) . '/' : '');
}