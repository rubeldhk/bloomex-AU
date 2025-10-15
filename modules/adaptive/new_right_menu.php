<?php
global $database;
$query = "SELECT 
   * 
FROM `jos_menu`
WHERE 
    `jos_menu`.`published`='1' 
    AND 
    `jos_menu`.`show`='1' 
    AND 
    `jos_menu`.`menutype`='" . $database->getEscaped('rightmenu_01') . "'
ORDER BY RAND() LIMIT 6";

$database->setQuery($query);
$database->query($query);
$menus = $database->loadObjectList();
?>
<ul class="right_menu">
    <?php
    foreach ($menus as $menu) {
        $link_obj = setMenuItem($menu);
        ?>
        <li><?php echo $link_obj->a; ?></li>
    <?php }
    ?>
</ul>

