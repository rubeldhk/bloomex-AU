<?php
global $database;

/* shoul dbe $sef->landing_type */

if (isset($_REQUEST['option'])) {
    if ($_REQUEST['option'] == 'com_companies') {
        $menu_type = 'left_company_menu';
    } elseif ($_REQUEST['option'] == 'com_landingpages' AND $_REQUEST['type'] == 'basket') {
        $menu_type = 'left_menu_GBLP';
    } elseif ($_REQUEST['option'] == 'com_landingpages' AND $_REQUEST['type'] == 'sympathy') {
        $menu_type = 'left_menu_SLP';
    } elseif ($_REQUEST['option'] == 'com_landingpages') {
        $menu_type = 'left_menu_FLP';
    } else {
        $menu_type = 'left_menu_1';
    }
}


$query = "SELECT 
   * 
FROM `jos_menu`
WHERE 
    `jos_menu`.`published`='1' 
    AND 
    `jos_menu`.`show`='1' 
    AND 
    `jos_menu`.`menutype`='" . $database->getEscaped($menu_type) . "'
ORDER BY RAND() LIMIT 6";

$database->setQuery($query);
$menus = $database->loadObjectList();
?>
<ul class="left_menu">
    <?php
    foreach ($menus as $menu) {
        $link_obj = setMenuItem($menu);
        ?>
        <li><?php echo $link_obj->a; ?></li>
    <?php }
    ?>
</ul>
