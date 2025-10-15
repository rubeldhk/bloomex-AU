<?php
global $database, $mosConfig_absolute_path;

$query = "SELECT `m`.* 
FROM `jos_menu` AS `m`
WHERE `m`.`published`='1' AND `m`.`menutype`='Bloomex_top'
ORDER BY `m`.`parent`, `m`.`ordering` ASC";

$database->setQuery($query);
$menus = $database->loadObjectList();

$parents_ids = array();
foreach ($menus as $menu) {
    $parents_ids[$menu->parent][] = $menu;
}
unset($menus);
?>
<div>
    <div class="">
        <ul class="wrapper">
            <li class="">
                Menu <div class="remove"></div>
            </li>
            <?php
            $menus_ids = array();
            if (!isset($sess)) {
                require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
                $sess = new ps_session;
            }
            foreach ($parents_ids[0] as $menu) {
                if (!in_array($menu->id, $menus_ids)) {
                    if (is_array($parents_ids[$menu->id]) AND sizeof($parents_ids[$menu->id]) > 0) {
                        ?>
                        <li class="">
                            <span class="plus"></span>
                            <span class="minus"></span>
                            <a href="<?php echo sefRelToAbs($menu->link); ?>" class=""><?php echo $menu->name; ?></a>
                            <ul class="inner">
                                <?php
                                foreach ($parents_ids[$menu->id] as $submenu) {
                                    
                                    /*<li><a href="<?php echo $sess->url($submenu->link); ?>"><?php echo $submenu->name; ?></a></li>*/
                                    ?>
                                    <li><a href="<?php echo sefRelToAbs($submenu->link); ?>"><?php echo $submenu->name; ?></a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li><a href="<?php echo sefRelToAbs($menu->link); ?>"><?php echo $menu->name; ?></a></li>
                        <?php
                        /*<li><a href="<?php echo $sess->url($menu->link); ?>"><?php echo $menu->name; ?></a></li>*/
                    }

                    $menus_ids[] = $menu->id;
                }
            }

            unset($menus_ids, $parents_ids);
            ?>
        </ul>
        <?php
        $query = "SELECT `m`.* 
        FROM `jos_menu` AS `m`
        WHERE `m`.`published`='1' AND `m`.`menutype`='left_menu_1'
        ORDER BY `m`.`parent`, `m`.`ordering` ASC LIMIT 6";

        $database->setQuery($query);
        $menus = $database->loadObjectList();
        ?>
        <ul class="mobile_left_menu"> 
            <?php
            foreach ($menus as $menu) {
                ?>
                <li><a href="<?php echo sefRelToAbs($menu->link); ?>"><?php echo $menu->name; ?></a></li>
                <?php
                /*<li><a href="<?php echo $sess->url($menu->link); ?>"><?php echo $menu->name; ?></a></li>*/
            }
            ?>
        </ul>
        <!--        <li><a href="">Sign In</a> | <a href="">Join</a></li>  -->
    </div>
</div>
<?php

