<?php

function getcontentname($id){
    global $database;
    $query_content = 'SELECT title  FROM  `jos_content`  WHERE  `id` ="'.$id.'" LIMIT 1';
    $database->setQuery($query_content);
    $content_name = $database->loadObjectList();
    return $content_name[0]->title;
}
function getcityname($url){
    global $database;
    $query_city = 'SELECT city  FROM  `tbl_landing_pages`  WHERE  `url` ="'.$url.'" LIMIT 1';
    $database->setQuery($query_city);
    $city_name = $database->loadObjectList();
    return $city_name[0]->city;
}
function getcatparentname($category_id){
    global $database;
    $cat_parent_query = 'SELECT category_parent_id FROM  `jos_vm_category_xref` WHERE  `category_child_id` ='.$category_id.' LIMIT 1';
    $database->setQuery($cat_parent_query);
    $cat_parent_id = $database->loadObjectList();
    if($cat_parent_id){
        $query_cat = 'SELECT category_name,category_id  FROM  `jos_vm_category`  WHERE  `category_id` ='.$cat_parent_id[0]->category_parent_id.' LIMIT 1';
        $database->setQuery($query_cat);
        $cat_name = $database->loadObjectList();
        return $cat_name[0];
    }else{
        return;
    }
}
function getcatname($category_id){
    global $database;
    $query_cat = 'SELECT category_name  FROM  `jos_vm_category`  WHERE  `category_id` ='.$category_id.' LIMIT 1';
    $database->setQuery($query_cat);
    $cat_name = $database->loadObjectList();
    return $cat_name[0]->category_name;
}
function getprodname($product_id){
    global $database;
    $query_prod = 'SELECT product_name  FROM  `jos_vm_product`  WHERE  `product_id` ='.$product_id.' LIMIT 1';
    $database->setQuery($query_prod);
    $prod_name = $database->loadObjectList();
    return $prod_name[0]->product_name;
}

global $iso_client_lang;

$breadcrumb_array = array();


$show_filter = false;

if (($_REQUEST['option'] == 'com_frontpage' AND $_REQUEST['Itemid'] == '1') OR $_REQUEST['option'] == 'com_landingpages' OR $_REQUEST['option'] == 'com_page_not_found') {
    $show_filter = true;
}

    
if ($_REQUEST['option'] == 'com_virtuemart' && $_REQUEST['page'] == 'shop.browse')
{
    $show_filter = true;
    
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');

    $category_id =$_REQUEST['category_id'];
    $cat_name = getcatname($category_id);
    $cat_parent = getcatparentname($category_id);
    if ($cat_parent)
    {
        $link='https://'.$_SERVER['HTTP_HOST'].'/index.php?option=com_virtuemart&category_id='.$cat_parent->category_id.'&lang='.$iso_client_lang.'&page=shop.browse';
        require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
        $sess = new ps_session;
        if (isset($sess) && strpos($link, 'com_virtuemart')) {
            $link = $sess->url($link);
        }

        $breadcrumb_array[] = array('link' => $link, 'name' => $cat_parent->category_name);
    }
    if($category_id){
        if ( in_array($category_id, $categories_array)) {
            $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>'.$cat_name.'</h1>');
        }else{
            $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => $cat_name);
        }
    }else{
        $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>View all products in shop</h1>');
    }
}
elseif ($_REQUEST['option'] == 'com_virtuemart' && $_REQUEST['page'] == 'shop.product_details')
{

    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');

    $category_id =$_REQUEST['category_id'];
    $cat_name = getcatname($category_id);
    $cat_parent = getcatparentname($category_id);

    require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
    $sess = new ps_session;

    $link_par='https://'.$_SERVER['HTTP_HOST'].'/index.php?option=com_virtuemart&category_id='.$cat_parent->category_id.'&lang='.$iso_client_lang.'&page=shop.browse';
    if (isset($sess) && strpos($link_par, 'com_virtuemart')) {
        $link_par = $sess->url($link_par);
    }

    $link='https://'.$_SERVER['HTTP_HOST'].'/index.php?option=com_virtuemart&category_id='.$category_id.'&lang='.$iso_client_lang.'&page=shop.browse';
    if (isset($sess) && strpos($link, 'com_virtuemart')) {
        $link = $sess->url($link);
    }


    if ($cat_parent)
    {
        $breadcrumb_array[] = array('link' => $link_par, 'name' => $cat_parent->category_name);
    }

    $breadcrumb_array[] = array('link' => $link, 'name' => $cat_name);
}
elseif ($_REQUEST['option'] == 'com_virtuemart' && $_REQUEST['page'] == 'account.index')
{
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>Account</h1>');
}
elseif ($_REQUEST['option'] == 'com_login')
{
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>Login</h1>');
}
elseif ($_REQUEST['option'] == 'com_virtuemart' && $_REQUEST['page'] == 'shop.cart')
{
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>Shopping Cart</h1>');
}
elseif ($_REQUEST['option'] == 'com_virtuemart' && $_REQUEST['page'] == 'checkout.index')
{
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>Checkout</h1>');
}
elseif ($_REQUEST['option'] == 'com_virtuemart' && $_REQUEST['page'] == 'checkout.thankyou')
{
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => '<h1>Thank you</h1>');
}
elseif($_REQUEST['option'] == 'com_content')
{
    $id = $_REQUEST['id'];
    $content_name = getcontentname($id);

    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => $content_name);
}
elseif ($_REQUEST['option'] == 'com_landingpages')
{
    global $VM_LANG;
    $url = $_REQUEST['url'];
    $city_name = getcityname($url);

    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');

    if($_REQUEST['type']=='basket'){
        $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => $city_name.' Gift Baskets  Delivery');
    }
    elseif ($_REQUEST['type']=='sympathy') {
        $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => $city_name.' Sympathy Flowers');
    }else{
        $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'name' => $city_name.' Flower Delivery');
    }
}
elseif ($_REQUEST['option'] == 'com_search') {
    $breadcrumb_array[] = array('link' => 'https://'.$_SERVER['HTTP_HOST'], 'name' => 'Home');
    $breadcrumb_array[] = array('link' => '', 'name' => 'Search "'.htmlspecialchars($searchword).'"');
}
    
$breadcrumb_sizeof = sizeof($breadcrumb_array);

if ($breadcrumb_sizeof > 0 OR $show_filter == true)
{
    $breadcrumb_links = array();

    $i = 0;
    foreach ($breadcrumb_array as $breadcrumb_one)
    {
        if ($i == $breadcrumb_sizeof-1)
        {
            $breadcrumb_links[] = '<div class="breadcrumb_div"><span class="breadcrumb_active">'.$breadcrumb_one['name'].'</span></div>';
        }
        else
        {
            $breadcrumb_links[] = '<div class="breadcrumb_div"><a class="breadcrumb_link" href="'.$breadcrumb_one['link'].'">'.$breadcrumb_one['name'].'</a></div>';
        }
        $i++;
    }

    ?>

    <div class="container breadcrumbs_wrapper">
        <div class="row">
            <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 breadcrumbs"> 
                <?php
                if ($breadcrumb_sizeof > 1) {
                    echo implode('<div class="breadcrumbs_slash">></div>', $breadcrumb_links);
                }
                else {
                    echo implode('', $breadcrumb_links);
                }
                ?>
            </div>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 breadcrumbs"> 
                <?php
                if ($show_filter == true) {

                    $fr = '';

                    if ($iso_client_lang == 'fr') {
                        $fr = 'fr';
                    }

                    $product_ordering_a = array(
                        /*
                        1 => array(
                            'title' => 'Relevance', 
                            'type' => 'desc',
                            'image' => ''
                        ),*/
                        2 => array(
                            'title' => 'Price', 
                            'type' => 'desc',
                            'image' => 'ordering_desc_new.png'
                        ),
                        3 => array(
                            'title' => 'Price', 
                            'type' => 'asc',
                            'image' => 'ordering_asc_new.png'
                        ),
                    );

                    $product_ordering = isset($_COOKIE['product_ordering']) ? (int)$_COOKIE['product_ordering'] : 2;

                    if ($product_ordering == 1) {
                        $type_ordering = 'desc';
                    }
                    else {
                        $type_ordering = 'asc';
                    }
                    ?>
                    <div class="filter_wrapper">
                        <!--
                        <div class="filter_title">
                            Sort by
                        </div>-->
                        <div class="filter_select">
                            <img alt="sort_by_price_asc" src="/templates/bloomex_adaptive/images/sort_by_price_asc.png" style="display: none;" />
                            <img alt="sort_by_price_desc" src="/templates/bloomex_adaptive/images/sort_by_price_desc.png" style="display: none;" />
                            <img alt="sort_by_price_fr_asc" src="/templates/bloomex_adaptive/images/sort_by_price_fr_asc.png" style="display: none;" />
                            <img alt="sort_by_price_fr_desc" src="/templates/bloomex_adaptive/images/sort_by_price_fr_desc.png" style="display: none;" />
                            <div class="product_ordering_active <?php echo $type_ordering; ?> <?php echo $fr; ?>">
                            </div>
                            <!--
                            <div class="product_ordering_wrapper">
                                <?php
                                foreach ($product_ordering_a as $key => $ordering) {
                                    ?>
                                        <div class="product_ordering <?php echo ($product_ordering == $key ? 'active' : ''); ?>" type="<?php echo $key; ?>">
                                            <div class="product_ordering_title">
                                                <?php echo $ordering['title']; ?>
                                            </div> 
                                            <div class="product_ordering_image">
                                                <?php echo (!empty($ordering['image']) ? '<img src="/templates/bloomex7/images/'.$ordering['image'].'" alt="'.$ordering['title'].' '.$ordering['type'].'" />' : '') ?>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                    <?php
                                }
                                ?>
                            </div>-->
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>
