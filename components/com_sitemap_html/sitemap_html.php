<?php

global $database, $mosConfig_lang;

$topCities = [
    'sydney' => 'Sydney',
    'brisbane' => 'Brisbane',
    'melbourne' => 'Melbourne',
    'adelaide' => 'Adelaide',
    'perth' => 'Perth'
];


    require_once $mosConfig_absolute_path . '/includes/router.php';
    $sef = new newSef();
    require_once $mosConfig_absolute_path . '/includes/joomla.php';
    if (!function_exists('setMenuItemSitemap')) {

        function setMenuItemSitemap($obj, $parent_obj = false) {
            global $mosConfig_live_site, $database;

            $return = new stdClass();
            $return->link = '';
            $return->name = '';
            $return->target = '';

            Switch ($obj->new_type) {

                default:
                    $return->link = ($parent_obj AND $parent_obj->new_type == 'vm_category') ? '/' . $parent_obj->alias . '/' . $obj->alias : '/' . $obj->alias;
                    break;
            }

            if (empty($obj->link)) {
                $return->link = '';
            }

            $return->name = mb_strtoupper(mb_substr($obj->name, 0, 1)) . mb_substr($obj->name, 1);

            return $return;
        }

    }
    if (!function_exists('getCategoryCanonical')) {

        function getCategoryCanonical($alias) {
            global $database;

            $aliases = [];
            $category_parent_id = 1;

            while ($category_parent_id > 0) {
                $query = "SELECT
            `c`.`alias`,
            `c2`.`alias` AS `parent_alias`,
            `c_x`.`category_parent_id`
        FROM `jos_vm_category` AS `c`
        LEFT JOIN `jos_vm_category_xref` AS `c_x`
            ON `c_x`.`category_child_id`=`c`.`category_id`
        LEFT JOIN `jos_vm_category` AS `c2`
            ON 
            `c2`.`category_id`=`c_x`.`category_parent_id`
            AND
            `c2`.`category_publish`='Y'
        INNER JOIN `jos_vm_category_options` AS `c_o`
            ON
            `c_o`.`category_id`=`c`.`category_id`
            AND
            `c_o`.`sitemap_publish`='1'
        WHERE
            `c`.`alias`='" . $alias . "'
        ";

                $database->setQuery($query);
                $parent_obj = false;
                $database->loadObject($parent_obj);

                if ($parent_obj) {
                    $alias = $parent_obj->parent_alias;
                    $aliases[] = $parent_obj->alias;
                    $category_parent_id = $parent_obj->category_parent_id;
                } else {
                    $category_parent_id = 0;
                }
            }

            $return = [];
            $return['link'] = '/' . implode('/', array_reverse($aliases)) . '/';
            $return['level'] = sizeof($aliases) - 1;

            return $return;
        }

    }

    $html_sitemap = '
<style>
    div.sitemap_container {  
        background-color: #fff;
        border: 1px solid #E6E6E6;
    }
    div.sitemap_container .title {
        font-weight: bold;
        color: #6e6a6d;
        font-size: 20px;
        text-align: left;
        margin: 5px 0px;
    }
    div.sitemap_container a, div.sitemap_container span {
        padding: 2px 0px;
        color: #a067a2;
        font-weight: bold;
    }
    div.sitemap_container a:hover {
        color: #b763b9;
        text-decoration: underline;
    }
    div.sitemap_container a.sub {
        margin-left: 20px;
    }
    div.sitemap_column {
        background-color: #fff;
    }
</style>';

//TOP MENU
    if (!function_exists('createMenuTree')) {

        function createMenuTree($menus, $menu_id = 0) {
            $branch = [];

            foreach ($menus as $menu) {
                if ($menu_id == $menu->parent) {
                    $child_branch = createMenuTree($menus, $menu->id);

                    if (array_key_exists($menu->id, $branch) == false) {
                        $branch[$menu->id] = [];
                    }

                    if (sizeof($child_branch) > 0) {
                        $branch[$menu->id] = $child_branch;
                    }

                    $branch[$menu->id]['info'] = $menu;
                }
            }

            return $branch;
        }

    }
    if (!function_exists('setMenu')) {

        function setMenu($menus, $level, $aliases = [], $aliases_obj = []) {
            global $html_sitemap, $mosConfig_lang, $sef;
            f($menus['info']->name, $menus, $level, $aliases, $aliases_obj);
            if ($mosConfig_lang == 'french' AND!empty($menus['info']->fr_name)) {
                $menus['info']->name = $menus['info']->fr_name;
            }
            if (!empty($menus['info']->new_type) && $menus['info']->link!='') {
                if ($menus['info']->new_type != 'url') {
                    $alias_obj = setMenuItem($menus['info']);
                    if ($mosConfig_lang == 'french') {
                        $alias_obj->link = $sef->translateURI('fr', $alias_obj->link);
                    }
                } else {
                    $alias_obj = setMenuItem($menus['info']);
                }
                $html_sitemap .= '<a href="/' . $alias_obj->link . '">' . str_repeat('-', $level) . ' ' . $menus['info']->name . '</a>';
                $aliases[] = $menus['info']->alias;
                $aliases_obj[] = $menus['info'];
            } else {
                $html_sitemap .= '<span>' . str_repeat('-', $level) . ' ' . $menus['info']->name . '</span>';
            }

            unset($menus['info']);

            if (sizeof($menus) > 0) {
                foreach ($menus as $menu) {
                    setMenu($menu, $level + 1, $aliases, $aliases_obj);
                }
            }
        }

    }
    $html_sitemap .= '<div class="container sitemap_container">';
    $html_sitemap .= '<div class="row">';
    $html_sitemap .= '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 sitemap_column">';
    $html_sitemap .= '<div class="title">' . (($mosConfig_lang == 'french') ? 'Menu principal' : 'Top Menu') . '</div>';

    $query = "SELECT
    `m`.`id`,
    `m`.`alias`,
    `m`.`parent`,
    `m`.`new_type`,
    `m`.`name`,
    `m`.`link`, 
    `j`.`value` AS 'fr_name' 
FROM `jos_menu` AS `m`
LEFT JOIN `jos_jf_content` AS `j` ON 
    `j`.`reference_id`=`m`.`id` 
    AND 
    `j`.`reference_table`='menu' 
    AND 
    `j`.`reference_field`='name' 
WHERE 
    `m`.`published`='1' 
    AND 
    `m`.`menutype`='Bloomex_top'
    AND
    `m`.`new_type`!='vm_category'";
    $database->setQuery($query);
    $menus = $database->loadObjectList();

    $menus_tree = createMenuTree($menus);

    foreach ($menus_tree as $menu) {
        $html_sitemap .= setMenu($menu, 0);
    }

    unset($menus, $menus_tree);

//CATEGORIES
    if (!function_exists('createCategoryTree')) {

        function createCategoryTree($categories, $category_child_id = 0) {
            $branch = [];

            foreach ($categories as $category) {
                if ($category_child_id == $category->category_parent_id) {
                    $child_branch = createCategoryTree($categories, $category->category_id);

                    if (array_key_exists($category->category_id, $branch) == false) {
                        $branch[$category->category_id] = [];
                    }

                    if (sizeof($child_branch) > 0) {
                        $branch[$category->category_id] = $child_branch;
                    }

                    $branch[$category->category_id]['info'] = $category;
                }
            }

            return $branch;
        }

    }
    if (!function_exists('setCategory')) {

        function setCategory($categories, $level, $aliases = []) {
            global $html_sitemap, $mosConfig_lang, $sef;

            $alias = ((sizeof($aliases) > 0) ? '/' . implode('/', $aliases) : '') . '/' . $categories['info']->alias;
            if ($mosConfig_lang == 'french' AND!empty($categories['info']->category_name_fr)) {
                $categories['info']->category_name = $categories['info']->category_name_fr;
                $alias = $sef->translateURI('fr', ((sizeof($aliases) > 0) ? '/' . implode('/', $aliases) : '') . '/' . $categories['info']->alias);
            }
            $html_sitemap .= '<a href="' . $alias . '/">' . str_repeat('-', $level) . ' ' . $categories['info']->category_name . '</a>';

            $aliases[] = $categories['info']->alias;

            unset($categories['info']);

            if (sizeof($categories) > 0) {
                foreach ($categories as $category) {
                    setCategory($category, $level + 1, $aliases);
                }
            }
        }

    }
    $query = "SELECT
    `c`.`category_id`,
    `c`.`category_name`,
    `c`.`alias`,
    `j`.`value` as category_name_fr,
    `cc_x`.`category_parent_id`
FROM `jos_vm_category` AS `c`
INNER JOIN `jos_vm_category_xref` AS `cc_x`
    ON
    `cc_x`.`category_child_id`=`c`.`category_id`
INNER JOIN `jos_vm_category_options` AS `c_o`
    ON
    `c_o`.`category_id`=`c`.`category_id`
    AND
    `c_o`.`sitemap_publish`='1'
    LEFT JOIN `jos_jf_content` AS `j` ON 
    `j`.`reference_id`=`c`.`category_id` 
    AND 
    `j`.`reference_table`='vm_category' 
    AND 
    `j`.`reference_field`='category_name' 
WHERE
    `c`.`category_publish`='Y'
";

    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $categories_tree = createCategoryTree($rows);

    $html_sitemap .= '<div class="title">' . (($mosConfig_lang == 'french') ? 'Cat?gories' : 'Categories') . '</div>';

    foreach ($categories_tree as $category) {
        $html_sitemap .= setCategory($category, 0);
    }

    $html_sitemap .= '</div>';

//FLOWERS LANDING

    $html_sitemap .= '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 sitemap_column">';
    $html_sitemap .= '<div class="title">' . (($mosConfig_lang == 'french') ? 'Fleurs' : 'Flower Delivery') . '</div>';
// 12828 is max id in old pages
    $query = 'SELECT
    `lp`.`id`,
    `lp`.`url`,
    `lp`.`city`
FROM `tbl_landing_pages` AS `lp` WHERE  id < 12828 and city not in ("'.implode('","', $topCities).'")
ORDER BY `lp`.`id` ASC
';

    $database->setQuery($query);
    $flowers_lp = $database->loadObjectList();

    $parents_ids = array();
    foreach ($topCities as $k=>$c) {
        $html_sitemap .= '<a href="/' . (($mosConfig_lang == 'french') ? 'fleuriste' : 'flower-delivery') . '/' . $k . '/">' . $c . ' ' . (($mosConfig_lang == 'french') ? 'Fleurs' : 'Flower Delivery') . '</a>';
    }
    foreach ($flowers_lp as $lp_obj) {
        $html_sitemap .= '<a href="/' . (($mosConfig_lang == 'french') ? 'fleuriste' : 'flower-delivery') . '/' . $lp_obj->url . '/">' . $lp_obj->city . ' ' . (($mosConfig_lang == 'french') ? 'Fleurs' : 'Flower Delivery') . '</a>';
    }

    $html_sitemap .= '</div>';


//FLOWERS LANDING

$html_sitemap .= '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 sitemap_column">';
$html_sitemap .= '<div class="title">' . (($mosConfig_lang == 'french') ? 'Paniers cadeaux' : 'Gift Hampers') . '</div>';

foreach ($topCities as $k=>$c) {

    $html_sitemap .= '<a href="/' . (($mosConfig_lang == 'french') ? 'panier_cadeau' : 'gift-hamper-basket'). '/' . $k . '/">' . $c . ' ' . (($mosConfig_lang == 'french') ? 'Paniers cadeaux' : 'Gift Hampers') . '</a>';
}

foreach ($flowers_lp as $i=>$lp_obj) {
    if($i < 5000)
        continue;
    $html_sitemap .= '<a href="/' . (($mosConfig_lang == 'french') ? 'panier_cadeau' : 'gift-hamper-basket') . '/' . $lp_obj->url . '/">' . $lp_obj->city . ' ' . (($mosConfig_lang == 'french') ? 'Paniers cadeaux' : 'Gift Hampers') . '</a>';
}

$html_sitemap .= '</div>';


    echo $html_sitemap;
