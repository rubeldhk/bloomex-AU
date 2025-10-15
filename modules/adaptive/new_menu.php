<?php
global $database, $iso_client_lang, $mosConfig_live_site;

$query = "SELECT 
    `m`.* 
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
    <nav class="navbar navbar-expand">
    <div class="container">
        <div class="navbar-header col-12 d-lg-none">
            <div>
                <a class="navbar-link mobile_menu_toggle"  href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="17.5" viewBox="0 0 448 512">
                        <path fill="#ffffff"  d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/>
                    </svg>
                    <p class="margin0">Menu</p>
                </a>
                <a class="navbar-link" href="/cart/" id="mobile_cart">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 576 512">
                        <path fill="#ffffff" d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                    </svg>
                    <span class="yellow_cart_items"></span>
                    <p class="margin0">Cart</p>
                </a>
                <a class="navbar-link" href="#" onclick="openchat();" id="mobile_chat">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 576 512">
                        <path fill="#ffffff" d="M284 224.8a34.1 34.1 0 1 0 34.3 34.1A34.2 34.2 0 0 0 284 224.8zm-110.5 0a34.1 34.1 0 1 0 34.3 34.1A34.2 34.2 0 0 0 173.6 224.8zm220.9 0a34.1 34.1 0 1 0 34.3 34.1A34.2 34.2 0 0 0 394.5 224.8zm153.8-55.3c-15.5-24.2-37.3-45.6-64.7-63.6-52.9-34.8-122.4-54-195.7-54a406 406 0 0 0 -72 6.4 238.5 238.5 0 0 0 -49.5-36.6C99.7-11.7 40.9 .7 11.1 11.4A14.3 14.3 0 0 0 5.6 34.8C26.5 56.5 61.2 99.3 52.7 138.3c-33.1 33.9-51.1 74.8-51.1 117.3 0 43.4 18 84.2 51.1 118.1 8.5 39-26.2 81.8-47.1 103.5a14.3 14.3 0 0 0 5.6 23.3c29.7 10.7 88.5 23.1 155.3-10.2a238.7 238.7 0 0 0 49.5-36.6A406 406 0 0 0 288 460.1c73.3 0 142.8-19.2 195.7-54 27.4-18 49.1-39.4 64.7-63.6 17.3-26.9 26.1-55.9 26.1-86.1C574.4 225.4 565.6 196.4 548.3 169.5zM285 409.9a345.7 345.7 0 0 1 -89.4-11.5l-20.1 19.4a184.4 184.4 0 0 1 -37.1 27.6 145.8 145.8 0 0 1 -52.5 14.9c1-1.8 1.9-3.6 2.8-5.4q30.3-55.7 16.3-100.1c-33-26-52.8-59.2-52.8-95.4 0-83.1 104.3-150.5 232.8-150.5s232.9 67.4 232.9 150.5C517.9 342.5 413.6 409.9 285 409.9z"/>
                    </svg>
                    <p class="margin0">Chat</p>
                </a>
                <a class="navbar-link" href="/account/" id="mobile_account">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="17.5" viewBox="0 0 448 512">
                        <path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/>
                    </svg>
                    <p class="margin0">Account</p>
                </a>
                <a class="navbar-link" href="tel:1800905147" id="mobile_call">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512">
                        <path fill="#ffffff"  d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>
                    </svg>
                    <p class="margin0">Call</p>
                </a>
                <a class="navbar-link" href="#" id="mobile_search">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512">
                        <path fill="#ffffff"  d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                    </svg>
                    <p class="margin0">Search</p>
                </a>


            </div>
            <div style="clear: both;"></div>
            <div class="mobile_search">
                <form role="form" action="/search/" method="get" id="mobile_search_form"> 
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchword" placeholder="Search by Keyword, Product SKU or Price" value="<?php echo !empty($searchword) ? htmlspecialchars($searchword) : ''; ?>">
                        <span class="input-group-btn" id="mobile_search_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ab0917" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <?php //collapse ;?>
        <div class="collapse navbar-collapse d-block d-xs-none d-sm-none">
            <ul class="nav navbar-nav">
                <?php
                $menus_ids = array();
                foreach ($parents_ids[0] as $menu) {
                    if (!in_array($menu->id, $menus_ids)) {
                        if (isset($parents_ids[$menu->id]) AND is_array($parents_ids[$menu->id]) AND sizeof($parents_ids[$menu->id]) > 0) {
                            
                            $link_obj = setMenuItem($menu, false, 'class="dropdown-toggle disabled" data-toggle="dropdown"', true);
                            ?>
                            <li class="nav-item dropdown">
                                <?php echo $link_obj->a; ?>
                                <ul class="dropdown-menu">
                                    <?php
                                    if($menu->rotate)
                                        shuffle($parents_ids[$menu->id]);
                                    foreach ($parents_ids[$menu->id] as $submenu) {
                                        
                                        $link_obj = setMenuItem($submenu, $menu);
                                        ?>
                                        <li><?php echo $link_obj->a; ?></li>
                                        <?php
                                        /*<li><a href="<?php echo sefRelToAbs($submenu->link); ?>"><?php echo $submenu->name; ?></a></li>*/
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }
                        else {
                            $link_obj = setMenuItem($menu);
                            ?>
                            <li class="nav-item"><?php echo $link_obj->a; ?></li>
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
        <div class="d-xs-none  md_search_wrapper">
            <form role="form" action="/search/" method="get" id="search_md_form">
                <div class="input-group">
                    <input type="text" class="form-control" name="searchword" placeholder="Search by Keyword, Product SKU or Price" value="<?php echo !empty($searchword) ? htmlspecialchars($searchword) : ''; ?>">
                    <span class="input-group-btn" id="search_md_btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ab0917" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </span>
                </div>
            </form>
        </div>
    </div>
</nav>
<?php

