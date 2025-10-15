<?php
global $iso_client_lang, $mosconfiglang, $sef;

$show_filter = false;

if (isset($_REQUEST['option'])) {
    if ($_REQUEST['option'] == 'com_page_not_found') {
        $show_filter = true;
    } elseif ($_REQUEST['option'] == 'com_virtuemart' AND ( isset($_REQUEST['page']) AND $_REQUEST['page'] == 'shop.browse')) {
        $show_filter = true;
    }
} else {
    $show_filter = true;
}

if (!$sef->homepage) {
    $sizeof_aliases = isset($GLOBALS['aliases']) ? count($GLOBALS['aliases']) : 0;
    $breadcrumbs = [];

    if ($sizeof_aliases > 0) {

        $base = "/"; //ALL YOUR BASE BELONG TO US
        $home_str = "home";
        $breadcrumbs[] = '<div class="breadcrumb_div"><a class="breadcrumb_link" href="' . $base . '">' . $home_str . '</a></div>';
        $i = 1;
        $link = "";
        foreach ($GLOBALS['aliases'] as $alias) {

            $link = $link . $alias . '/';
            $url = $base . $link;

            if ($i == 1 AND $sef->city) {
                $breadcrumbs[] = '<div class="breadcrumb_div"><span class="breadcrumb_active breadcrumb_link">' . htmlspecialchars(str_replace('_', ' ', str_replace('-', ' ', urldecode($alias)))) . '</span></div>';
            } elseif (($i == 2 AND in_array($alias, ['order-details'])) || ($i == 1 AND in_array($alias, ['order-tax-invoice']))) {
                //WTF is Partnership-Account?
                $breadcrumbs[] = '<div class="breadcrumb_div"><span class="breadcrumb_active breadcrumb_link">' . htmlspecialchars(str_replace('-', ' ', urldecode($alias))) . '</span></div>';
            } else {
                $breadcrumbs[] = '<div class="breadcrumb_div">
                    <a class="breadcrumb_link" href="' . htmlspecialchars($url) . '">
                        <span  ' . (($i == $sizeof_aliases ) ? 'class="breadcrumb_active"' : '') . '>
                            ' . htmlspecialchars(str_replace('_', ' ', str_replace('-', ' ', urldecode($alias)))) . '
                        </span>
                        <meta content="' . $i . '" />
                    </a></div>';
            }
            $i++;
        }
    }
    if (isset($breadcrumbs[0])) {
        ?>

        <div class="container breadcrumbs_wrapper">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-10 col-lg-10 breadcrumbs" >
                    <?php
                    echo implode('<div class="breadcrumbs_slash">></div>', $breadcrumbs);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-2 breadcrumbs">
                    <?php
                    if ($show_filter == true) {
                        $sortbypriceLabel = 'Sort by price';
                        $product_ordering = isset($_COOKIE['product_ordering']) ? $_COOKIE['product_ordering'] : '';
                        $sorting_class = '';
                        if ($product_ordering === 'desc') {
                            $sorting_class = 'glyphicon-sort-by-attributes-alt';
                        } elseif ($product_ordering === 'asc') {
                            $sorting_class = 'glyphicon-sort-by-attributes';
                        }
                        ?>
                        <div class="filter_wrapper">
                            <div class="filter_select">
                                <p class="sort_by_select"><?php echo $sortbypriceLabel; ?>
                                    <span class="glyphicon <?php echo $sorting_class; ?> "></span></p>
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
}
