<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');

date_default_timezone_set('Australia/Sydney');

require_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

function setUrl($url) {
    global $mosConfig_live_site;
    
    return '<url>
        <loc>'.$mosConfig_live_site.'/'.$url.'</loc>
        <lastmod>'.date('c').'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>';
}

function setXml($filename, $urls) {
    $urls = array_map('setUrl', $urls);
    
    $xml = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.implode('', $urls).'</urlset>';
    
    $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/sitemaps/'.$filename, 'w');
    fwrite($fp, $xml);
    fclose($fp);
    
    return true;
}

function getCategoryCanonical($alias) {
    global $mysqli;
    
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
            `c`.`alias`='".$alias."'
            AND
            `c`.`category_publish`='Y'
        ";

        $result_parent = $mysqli->query($query);

        if ($result_parent->num_rows > 0) {
            $parent_obj = $result_parent->fetch_object();
            
            $alias = $parent_obj->parent_alias;
            $aliases[] = $parent_obj->alias;
            $category_parent_id = $parent_obj->category_parent_id;
            
            $result_parent->close();
        }
        else {
            $category_parent_id = 0;
        }
    }
    
    return implode('/', array_reverse($aliases));
}

$files = [];

//MENU
$menu_urls = [];

$query = "SELECT
    `m`.`id`,
    `m`.`alias`,
    `m`.`parent`,
    `m`.`new_type`
FROM `jos_menu` AS `m`
WHERE 
    `m`.`published`='1' 
    AND 
    `m`.`menutype`='Bloomex_top'
    AND
    `m`.`new_type`!='vm_category'
";

$result = $mysqli->query($query);

$menus = array();
while ($obj = $result->fetch_object()) {
    $menus[$obj->parent][] = $obj;
}
$result->close();

foreach ($menus[0] as $menu) {
    
    if (!empty($obj->new_type)) {
        $menu_urls[] = $menu->alias.'/';
    }
    
    if (array_key_exists($menu->id, $menus)) {
        foreach ($menus[$menu->id] as $submenu) {
            $menu_urls[] = $submenu->alias.'/';
        }
    }
}

setXml('content.xml', $menu_urls);
$files[] = 'content.xml';

unset($menu_urls);
//!MENU

//CATEGORIES
$categories_urls = [];

$query = "SELECT
    `c`.`category_id`,
    `c`.`alias`
FROM `jos_vm_category` AS `c`
INNER JOIN `jos_vm_category_options` AS `c_o`
    ON
    `c_o`.`category_id`=`c`.`category_id`
    AND
    `c_o`.`sitemap_publish`='1'
WHERE
    `c`.`category_publish`='Y'
";

$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    $categories_urls[$obj->category_id] = getCategoryCanonical($obj->alias).'/';
}
$result->close();

setXml('categories.xml', $categories_urls);
$files[] = 'categories.xml';

//unset($categories_urls);

//!CATEGORIES

//PRODUCTS
$products_urls = [];

$query = "SELECT
    `p`.`alias`,
    `c`.`alias` AS 'category_alias'
FROM `jos_vm_product` AS `p`
INNER JOIN `jos_vm_product_options` AS `po`
    ON `po`.`product_id`=`p`.`product_id`
INNER JOIN `jos_vm_category` AS `c`
    ON 
    `c`.`category_id`=`po`.`canonical_category_id`
    AND
    `c`.`category_publish`='Y'
WHERE
    `p`.`product_publish`='Y'
";

$result = $mysqli->query($query);

while ($obj = $result->fetch_object()) {
    $category_canonical = getCategoryCanonical($obj->category_alias);
    
    if (!empty($category_canonical)) {
        $products_urls[] = $category_canonical.'/'.$obj->alias.'/';
    }
}
$result->close();

setXml('products.xml', $products_urls);
$files[] = 'products.xml';

unset($products_urls);

//!PRODUCTS

//CITIES
$cities_url = [];

$query = "SELECT
    LOWER(`lp`.`url`) as url
FROM `tbl_landing_pages` AS `lp`
";

$result = $mysqli->query($query);
    
$i = 1;
while ($obj = $result->fetch_object()) {
    if (in_array($obj->url, array('canberra', 'gold-coast'))) {
        $cities_url[] = ''.$obj->url.'/flowers/';
        $cities_url[] = ''.$obj->url.'/gift-baskets/';
    }
    else {
        $cities_url[] = 'flower-delivery/'.$obj->url.'/';
        $cities_url[] = 'gift-hamper-basket/'.$obj->url.'/';
    }
    //$cities_url[] = 'sympathy-flowers/'.$obj->url.'/';
        
    foreach ($categories_urls as $category_id => $category_url) {
        $old_lp = array(
            18,
            //16,
            183
        );
        
        if (in_array($category_id, $old_lp)) {
            continue;
        }
                
        $cities_url[] = $obj->url.'/'.$category_url;

        if ((sizeof($cities_url) % 10000) == 0) {
            setXml('cities-'.$i.'.xml', $cities_url);
            $files[] = 'cities-'.$i.'.xml';
            $i++;
            
            $cities_url = [];
        }
    }
}
$result->close();

if (sizeof($cities_url) > 0) {
    setXml('cities-'.$i.'.xml', $cities_url);
    $files[] = 'cities-'.$i.'.xml';
}

unset($cities_url, $categories_urls);
//!CITIES

$xml = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($files as $file) {
    $xml .= '<sitemap>
    <loc>'.$mosConfig_live_site.'/sitemaps/'.$file.'</loc>
    </sitemap>';
}
$xml .= '</sitemapindex>';

$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/sitemaps/sitemap.xml', 'w');
fwrite($fp, $xml);
fclose($fp);

$mysqli->close();

?>