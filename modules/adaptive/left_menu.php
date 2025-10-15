<?php

global $database;

if (isset($_REQUEST['option']) AND isset($_REQUEST['type']) AND $_REQUEST['option'] == 'com_landingpages' AND $_REQUEST['type']=='basket'){
    $menu_type = 'left_menu_gblp';
}
elseif (isset($_REQUEST['option']) AND isset($_REQUEST['type']) AND $_REQUEST['option'] == 'com_landingpages' AND $_REQUEST['type'] == 'sympathy') {
    $menu_type = 'left_menu_slp';
}
elseif (isset($_REQUEST['option']) AND $_REQUEST['option'] == 'com_landingpages') {
    $menu_type = 'left_menu_flp';
}
else {
    $menu_type = 'left_menu_1';
}

$query = "SELECT 
    `m`.* 
FROM `jos_menu` AS `m`
WHERE `m`.`published`='1' AND `m`.`menutype`='".$database->getEscaped($menu_type)."'
ORDER BY `m`.`parent`, `m`.`ordering` ASC LIMIT 6";

$database->setQuery($query);
$menus = $database->loadObjectList();
?>
<ul class="left_menu"> 
<?php
foreach ($menus as $menu) {
    /*
    ?>
    <li><a href="<?php echo $sess->url($menu->link); ?>"><?php echo $menu->name; ?></a></li>
    <li><a href="<?php echo sefRelToAbs($menu->link); ?>"><?php echo $menu->name; ?></a></li>
    <?php*/
    ?>
    <li><a href="/<?php echo $menu->alias; ?>"/><?php echo $menu->name; ?></a></li>
    <?php
}

