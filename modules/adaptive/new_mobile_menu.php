<?php
global $database, $mosConfig_absolute_path;

$query = "SELECT `m`.* 
FROM `jos_menu` AS `m`
WHERE 
    `m`.`published`='1'
    AND 
    `m`.`show`='1' 
    AND 
    `m`.`menutype`='Bloomex_top'
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

            foreach ($parents_ids[0] as $menu) {
                if (!in_array($menu->id, $menus_ids)) {
                    if (isset($parents_ids[$menu->id]) AND is_array($parents_ids[$menu->id]) AND sizeof($parents_ids[$menu->id]) > 0) {

                        $link_obj = setMenuItem($menu);
                        ?>
                        <li class="">
                            <span class="plus"></span>
                            <span class="minus"></span>
                            <?php echo $link_obj->a; ?>
                            <ul class="inner">
                                <?php
                                foreach ($parents_ids[$menu->id] as $submenu) {

                                    $link_obj = setMenuItem($submenu, $menu);
                                    ?>
                                    <li><?php echo $link_obj->a; ?></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                    }
                    else {
                        $link_obj = setMenuItem($menu);
                        ?>
                        <li><?php echo $link_obj->a; ?></li>
                        <?php
                    }

                    $menus_ids[] = $menu->id;
                }
            }

            unset($menus_ids, $parents_ids);
            ?>
        </ul>
        <?php
        $menu_type = 'left_menu_1';
        if ($sef->landing_type > 0) {
            $menu_type = $sef->landing_type == 1 ? 'left_menu_flp' : 'left_menu_gblp';
        }

        $query = "SELECT 
            `m`.* 
        FROM `jos_menu` AS `m`
        WHERE 
            `m`.`published`='1' 
            AND 
            `m`.`show`='1' 
            AND 
            `m`.`menutype`='".$database->getEscaped($menu_type)."'
        ORDER BY `m`.`parent`, `m`.`ordering` ASC LIMIT 6";

        $database->setQuery($query);
        $menus = $database->loadObjectList();
        ?>
        <ul class="mobile_left_menu"> 
            <?php
            foreach ($menus as $menu) {
                $link_obj = setMenuItem($menu);
                ?>
                <li><?php echo $link_obj->a; ?></li>
                <?php
                /*<li><a href="<?php echo $sess->url($menu->link); ?>"><?php echo $menu->name; ?></a></li>*/
            }
            ?>
        </ul>

        <ul class="delivery_outside">
            <li class="">
                <span class="plus"></span>
                <span class="minus"></span>
                <span class="delivery_outside_text">Deliveries outside of Australia</span>
                <ul class="inner">
                    <li>
                        <a target="_blank" href="https://bloomex.co.nz">
                            <img alt="New Zealand" width="18px" height="14px" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/Flags/nz.webp" /> New Zealand
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="https://bloomex.com.au/serenata-flowers/">
                            <img alt="United Kingdom" width="18px" height="14px" src="/templates/<?php echo $cur_template; ?>/images/Flags/gb.webp" /> UK
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="https://bloomexusa.com/">
                            <img alt="USA" width="18px" height="14px" src="/templates/<?php echo $cur_template; ?>/images/Flags/us.webp" /> USA
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="https://bloomex.ca/">
                            <img alt="Canada" width="18px" height="14px" src="/templates/<?php echo $cur_template; ?>/images/Flags/CA.webp" /> Canada
                        </a>
                    </li>

                </ul>
            </li>
        </ul>



    </div>
</div>
<?php

