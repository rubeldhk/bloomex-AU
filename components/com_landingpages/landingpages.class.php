<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class LandingPage {

    var $_type = '';
    var $_lang = '';
    var $_langnum = 1;
    var $_selectcolumns = '';
    var $_selectcolumnslanding = '';
    var $_url = '';
    var $_limit = 24;


    function __construct() {
        global $iso_client_lang, $sef;
        $this->_type = mosGetParam($_GET, "type") ? trim(mosGetParam($_GET, "type")) : 'landing';
        $this->_lang = $iso_client_lang;
        $this->_url = trim(mosGetParam($_GET, "url"));
        if ($this->_type == 'landing') {
            $sef->landing_type = 1;
        } elseif($this->_type == 'sympathy') {
            $sef->landing_type = 3;
        } elseif($this->_type == 'flower-delivery') {
            $sef->landing_type = 4;
        } else {
            $sef->landing_type = 2;
        }

        if ($this->_lang == 'en') {
            $this->_langnum = 1;
            $this->_selectcolumns = 'en_content as content,en_left_pop as left_pop,en_center_pop as center_pop,en_right_pop as right_pop,right_pop_publish,category';
            $this->_selectcolumnslanding = '
            id,
            city,
            province,
            telephone,
            url,
            enable_location,
            location_address,
            location_country,
            location_postcode,
            location_telephone,
            lat,
            lng
            ';
        } else {
            $this->_langnum = 2;
            $this->_selectcolumns = 'fr_content as content,fr_left_pop as left_pop,fr_center_pop as center_pop,fr_right_pop as right_pop,right_pop_publish,category';
            $this->_selectcolumnslanding = '
                id,
                province,
                telephone,
                url,
                enable_location,
                location_address,
                location_country,
                location_postcode,
                location_telephone,
                lat,
                lng,
                case when city_fr IS NULL or city_fr = ""
                then city
                else city_fr
                end as city
               
            ';
        }
    }

    function settimezone($province) {

        $timezones = array(
            'AT' => 'Australia/Sydney',
            'NW' => 'Australia/Sydney',
            'NSW' => 'Australia/Sydney',
            'NT' => 'Australia/Darwin',
            'QL' => 'Pacific/Guam',
            'QLD' => 'Pacific/Guam',
            'SA' => 'Australia/Adelaide',
            'TA' => 'Australia/Hobart',
            'VI' => 'Australia/Melbourne',
            'VIC' => 'Australia/Melbourne',
            'WA' => 'Australia/Perth'
        );
        if (isset($timezones[$province])) {
            date_default_timezone_set($timezones[$province]);
        } else {
            date_default_timezone_set('Australia/Sydney');
        }
    }

    function getproducts($category) {
        global $database;

        $products = array();

        if ($category) {
            $category_arr = unserialize($category);
            if (is_array($category_arr)) {
                $category_str = implode(',', $category_arr);

                $query = "SELECT 
                `p`.`product_id`,
                                CASE 
                    WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                    ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
                END AS `product_real_price`
                FROM 
                    `jos_vm_product` AS `p`  
                   join  `jos_vm_product_category_xref` AS `cx` on cx.product_id=p.product_id
                 LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
                LEFT JOIN `jos_vm_products_promotion` AS `pm` ON  (`pm`.`product_id`=`p`.`product_id` or 
 `p`.`product_id` = ANY (SELECT product_id FROM jos_vm_product_category_xref WHERE category_id = pm.category_id)) 
                and pm.public = 1  and   ((CURRENT_DATE BETWEEN pm.start_promotion AND pm.end_promotion) OR (WEEKDAY(NOW()) = pm.week_day))
                WHERE 
                    `cx`.`category_id` IN (" . $category_str . ") 
                    AND
                    `p`.`product_id`=`cx`.`product_id` 
                    AND 
                    `p`.`product_publish`='Y'  order by product_real_price asc 
                 LIMIT 0, " . $this->_limit . "";

                $database->setQuery($query);
                $products_obj = $database->loadObjectList();
                shuffle($products_obj);
                foreach ($products_obj as $product_obj) {
                    $products[] = $product_obj->product_id;
                }
            }
        }
        return $products;
    }

    function getdefaultvalues() {
        global $database;

        $query_info_default = "
                         SELECT " . $this->_selectcolumns . "
                FROM `tbl_landing_pages_info`
                WHERE `landing_url` LIKE 'default'
                AND `type` LIKE '" . $this->_type . "'
         ";
        $database->setQuery($query_info_default);
        $content_info_def = $database->loadAssocList();

        return $content_info_def[0];
    }

    function check_url() {
        global $database;
        //cleanup
        $this->_url = str_replace("_", "", $this->_url);
        $this->_url = str_replace("%", "", $this->_url);
        $query = "
                SELECT id
                FROM `tbl_landing_pages`
                WHERE enable_location='1' and `url` LIKE '" . $this->_url . "'
         ";
        $database->setQuery($query);
        $landing = $database->loadResult();
        if ($landing) {
            return true;
        } else {
            return false;
        }
    }

    function getinfo() {
        global $database;
        $query_info = "
                         SELECT " . $this->_selectcolumns . "
                FROM `tbl_landing_pages_info`
                WHERE `landing_url` LIKE '" . $this->_url . "'
                AND `type` LIKE '" . $this->_type . "'
         ";
        $database->setQuery($query_info);
        $content_info = $database->loadAssocList();

        $query = "
                SELECT " . $this->_selectcolumnslanding . "
                FROM `tbl_landing_pages`
                WHERE `url` LIKE '" . $this->_url . "'
         ";
        $database->setQuery($query);
        $landing_info = $database->loadAssocList();

        if ($content_info && $landing_info) {
            $info = array_merge($content_info[0], $landing_info[0]);
        } elseif ($content_info && !$landing_info) {
            $info = $content_info[0];
        } else {
            $info = $landing_info[0];
        }
        return $info;
    }

}
