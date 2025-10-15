<?php

class checkStartPage {

    function check() {
        global $iso_client_lang;
        if (isset($_GET['option']) && ( $_GET['option'] == 'com_landingpages' || $_GET['option'] == 'com_landingbasketpages' ))
            return true;
        $startPage = str_replace("index.php", "", str_replace("/", "", $_SERVER['REQUEST_URI']));
        $startPage = str_replace("?lang=$iso_client_lang", "", $startPage);
        return ( strlen($startPage) < 1 || ( isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_frontpage' ) ) ? true : false;
    }

}

if (!checkStartPage::check())
    return false;


global $iso_client_lang, $database, $mosConfig_live_site;

$query = 'SELECT * FROM jos_vm_edit_banner_href WHERE id="1"';
$database->setQuery($query);
$thinbanner = $database->loadObjectList();
$thinbanner = $thinbanner[0];
?>

<table width="100%" cellpadding="0" cellspacing="0" id="new-main-table">
    <tr>
        <td width="25%" id="new-main-banner-menu" valign="top">
            <?php
            if ($_REQUEST['option'] == 'com_landingpages') {
                if($_REQUEST['type'] && $_REQUEST['type']=='basket'){
                    $RevealMenu = new RevealMenu('left_menu_gblp ', null, 'w-rbm', 'ddmenu-rbm', 'top-rbm');
                }elseif ($_REQUEST['type'] && $_REQUEST['type']=='sympathy'){
                    $RevealMenu = new RevealMenu('left_menu_slp ', null, 'w-rbm', 'ddmenu-rbm', 'top-rbm');
                }else{
                    $RevealMenu = new RevealMenu('left_menu_flp ', null, 'w-rbm', 'ddmenu-rbm', 'top-rbm');
                }
            } else {
                $RevealMenu = new RevealMenu('left_menu_1', null, 'w-rbm', 'ddmenu-rbm', 'top-rbm');
            }
                echo $RevealMenu->menu();

            ?>
        </td>
        <td width="50%" id="new-main-banner-banner" valign="top">

            <?php
            mosLoadModules('revcenter');
            $city = htmlspecialchars($_REQUEST['url']);
            $query = "SELECT city 
FROM  `tbl_landing_pages` 
WHERE  `url` LIKE  '$city' ";
            $database->setQuery($query);
            $city_name = $database->loadObjectList();
            if ($_REQUEST['type'] == 'basket') {

                $lang = 'Gift Baskets  Delivery';
            }
            elseif ($_REQUEST['type'] == 'sympathy') {
                $lang = 'Sympathy Flowers';
            } else {

                $lang = 'Florist Online';
            }
            $city_flower_delivery = '<div id="bar_on_map"><div id="city_flower_delivery"><h1>' . ucfirst($city_name[0]->city) . '  ' . $lang . '</h1></div></div>';
            if ($_REQUEST['option'] == 'com_landingpages') {
                echo $city_flower_delivery;
            }


            $query = "SELECT * FROM jos_vm_slider WHERE public='1'  ORDER BY queue ASC";
            $database->setQuery($query);
            $result = $database->loadObjectList();

            //print_r($result);
            $data = null;
            $i = 0;
            foreach ($result as $item) {
                $data .= (( is_null($data) ) ? "" : "[--1--]") . "<div id='sliderBanner$i' style='display:none; height:265px'><a href='{$item->src}' class='a{CNEXT}'><img src='$mosConfig_live_site/images/header_images/{$item->image}' class='banner-size'></a></div>";
                $i++;
            }

            ?>

        </td>
        <td width="25%" id="new-main-banner-testomonials" valign="top">
            <?php require_once 'modules/mod_testimonial.php'; ?>
        </td>
    </tr>
</table>
<div id="resizedBanner" style="margin-bottom: -25px">
    <div id="appendTopBodyBanner">
        <!--<a href="<?php echo $thinbanner->href; ?>">
            <img alt="banner" title="<?php echo $thinbanner->title; ?>" src="<?php echo $mosConfig_live_site; ?>/images/banners/<?php echo $thinbanner->image; ?>" />
        </a>-->
        <a href="<?php echo modulesLanguage::get('appendMainPageBannerImageLink'); ?>">
                <img alt="banner" src="<?php echo $mosConfig_live_site; ?>/images/banners/<?php echo modulesLanguage::get('appendMainPageBannerImage'); ?>" />
        </a>
    </div>
</div>
<script>
    $j(document).ready(function () {
        checkHeight();
        $j(window).resize(function () {
            checkHeight();
        });
        function checkHeight() {
            $j('#new-main-table').css('display', (($j(window).height() < 550) ? 'none' : 'table'));
            /* $j('#resizedBanner').css('display', (($j(window).height() >= 500) ? 'none' : 'block')); */
            $j('#resizedBanner').css('display', 'block');
        }

        $j('#appendTopBodyBanner img').css('width', '100%');
    });

</script>