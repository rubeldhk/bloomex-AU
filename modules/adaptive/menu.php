<?php

global $database, $iso_client_lang;

$query = "SELECT 
    `m`.* , 
    `j`.`value` AS 'fr_name' 
FROM `jos_menu` AS `m`
LEFT JOIN `jos_jf_content` AS `j` ON 
    `j`.`reference_id`=`m`.`id` 
    AND 
    `j`.`reference_table`='menu' 
    AND 
    `j`.`reference_field`='name' 
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
<div class="navbar" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <div class="hidden-lg hidden-md">
                <button type="button" class="mobile_menu_toggle">
                    <img alt="menu icon" src="/templates/<?php echo $cur_template; ?>/images/m_menu.svg" />
                    <span>Menu</span>
                </button>
                <a class="navbar-link" href="#" id="mobile_search">
                    <img alt="search icon" src="/templates/bloomex_adaptive/images/m_search.svg" /> Search
                </a>
                <a class="navbar-link" href="tel:1800905147" id="mobile_call">
                    <img alt="call icon" src="/templates/bloomex_adaptive/images/m_phone.svg" /> Call
                </a>
                <!--
                <a class="navbar-link" href="/index.php?option=com_contact">
                    <img alt="mail letter" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex_adaptive/images/mail_letter_white.svg" /> Contact Us
                </a>
                <a class="navbar-link" href="/index.php?page=account.index&amp;option=com_virtuemart&amp;Itemid=465">
                    <img alt="my account" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex_adaptive/images/account_white.svg" /> Account
                </a>-->
                <a class="navbar-link" href="/index.php?page=shop.cart&amp;option=com_virtuemart&amp;Itemid=80&amp;lang=en" style="float: right;">
                    <div class="icon">
                        <img alt="Cart" src="/templates/bloomex_adaptive/images/cart.svg">
                    </div>
                </a>
            </div>
            <div style="clear: both;"></div>
            <div class="mobile_search">
                <form role="form" action="/search.html" method="get" id="mobile_search_form"> 
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchword" placeholder="Search by Keyword, Product SKU or Price" value="<?php echo !empty($searchword) ? htmlspecialchars($searchword) : ''; ?>">
                        <span class="input-group-btn" id="mobile_search_btn">
                            <span class="glyphicon glyphicon-search"></span>
                        </span>
                    </div>
                </form>
            </div>
            <!--<a class="navbar-brand" href="#">Bootstrap theme</a>-->
        </div>
        <?php //collapse ;?>
        <div class="collapse navbar-collapse hidden-sm hidden-xs">
            <ul class="nav navbar-nav">
                <?php
                $menus_ids = array();
                foreach ($parents_ids[0] as $menu) {
                    if (!in_array($menu->id, $menus_ids)) {
                        if (is_array($parents_ids[$menu->id]) AND  sizeof($parents_ids[$menu->id]) > 0) {
                            ?>
                            <li class="dropdown">
                                <a href="<?php echo sefRelToAbs($menu->link); ?>" class="dropdown-toggle disabled" data-toggle="dropdown"><?php echo $menu->name; ?><b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <?php
                                    foreach ($parents_ids[$menu->id] as $submenu) {
                                        ?>
                                        <li><a href="<?php echo sefRelToAbs($submenu->link); ?>"><?php echo $submenu->name; ?></a></li>
                                        <?php
                                        /*<li><a href="<?php echo $sess->url($submenu->link); ?>"><?php echo $submenu->name; ?></a></li>*/
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }
                        else {
                            ?>
                            <li><a href="<?php echo sefRelToAbs($menu->link); ?>"><?php echo $menu->name; ?></a></li>
                            <?php
                        }
                        
                        $menus_ids[] = $menu->id;
                    }
                }
                
                unset($menus_ids, $parents_ids);
                ?>
                <li class="md_search visible-md">
                    <a id="md_search"><span class="glyphicon glyphicon-search"></span></a>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
        <div class="hidden-xs md_search_wrapper">
            <form role="form" action="/search.html" method="get" id="search_md_form">
                <div class="input-group">
                    <input type="text" class="form-control" name="searchword" placeholder="Search by Keyword, Product SKU or Price" value="<?php echo !empty($searchword) ? htmlspecialchars($searchword) : ''; ?>">
                    <span class="input-group-btn" id="search_md_btn">
                        <span class="glyphicon glyphicon-search"></span>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
<?php

