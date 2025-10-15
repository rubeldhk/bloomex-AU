<?php

// -----------------------------------------------------------------
// interface
interface IContainer {

    function get($dataset, $parameter);
}

// -----------------------------------------------------------------
// -----------------------------------------------------------------
// Container classes
class com_landingpages_funeralBanner implements IContainer {

    function get($dataset, $parameter) {

        return "<a href='{$dataset['link']}'><!-- {CANVAS} -->
                    <div id='landing-banner-div-data-funeral'><br />
                        <b>{$dataset['city']}</b><br />
                        <b class='landing-banner-div-data-text'>{$dataset['text']}</b><br />
                        <b><span class='landing-banner-div-data-phone'><a style='color: #000;' title=\"Click to call\" href=\"tel:{$dataset['phone']}\"> {$dataset['phone']}</a></span></b>
                    </div>
                    <img src='{$dataset['sHeaderImage']}' class='banner-size'>
                </a>";
    }

}

class com_landingbasketpagesBanner extends com_landingpages_funeralBanner {

    function get($dataset, $parameter) {
        //return parent::get($dataset);
        return str_replace("<!-- {CANVAS} -->", "<div id='map-canvas-banner'><div id='map_canvas' ></div></div>", parent::get($dataset, $parameter));
    }

}

class com_landingpagesBanner extends com_landingpages_funeralBanner {

    function get($dataset, $parameter) {
        return str_replace("<!-- {CANVAS} -->", "<div id='map-canvas-banner'><div id='map_canvas' ></div></div>", parent::get($dataset, $parameter));
    }

}

class com_frontpageBanner implements IContainer {

    function get($dataset, $parameter) {

        global $mosConfig_live_site;
        //return "<a href='{$dataset['link']}'><img src='{$dataset['sHeaderImage']}' class='banner-size'></a>";
        $data = null;
        $i = 0;
        if (isset($dataset[0]->id)) {
            foreach ($dataset as $item) {
                $data .= (( is_null($data) ) ? "" : "[--1--]") . "<div id='sliderBanner$i' style='display:none; height:265px'><a href='{$item->src}' class='a{CNEXT}'><img src='$mosConfig_live_site/images/header_images/{$item->image}' class='banner-size'></a></div>";
                $i++;
            }
        } else {

            foreach ($dataset as $item) {
                $data .= (( is_null($data) ) ? "" : "[--1--]") . "<div id='sliderBanner$i' style='display:none; height:265px'><a href='{$item->src}' class='a{CNEXT}'><img src='$mosConfig_absolute_path/company_images/{$item->url}/slider/{$item->image}' class='banner-size'></a></div>";
                $i++;
            }
        }
        if ($_REQUEST['option'] != 'com_landingpages') {
            return "<div id='previousSlider'>&nbsp;</div>
                <div id='nextSlider'>&nbsp;</div>
                <div id='pauseSlider'>&nbsp;</div>
                <div id='playSlider'>&nbsp;</div>
                <div id='showSlide'></div>
                <script>
                //// SLIDER
                \$j(document).ready (function(){
                    \$j('#nextSlider').css( 'margin-left', parseInt(\$j('#new-main-banner-banner').css('width')) - parseInt(\$j('#nextSlider').css('width')) );
                    \$j('#pauseSlider').css( 'margin-left', (parseInt(\$j('#new-main-banner-banner').css('width')) - parseInt(\$j('#pauseSlider').css('width')))/2 );
                    \$j('#playSlider').css( 'margin-left', (parseInt(\$j('#new-main-banner-banner').css('width')) - parseInt(\$j('#playSlider').css('width')))/2 );
                    heightPN('previousSlider', 'new-main-banner-banner');
                    heightPN('nextSlider', 'new-main-banner-banner');
                    heightPN('pauseSlider', 'new-main-banner-banner');
                    heightPN('playSlider', 'new-main-banner-banner');

                    function heightPN(idPN, idBanner){
                        \$j('#'+idPN).css( 'margin-top', (parseInt(\$j('#'+idBanner).css('height'))/2) - (parseInt(\$j('#'+idPN).css('height'))/2) );
                    }

                    \$j('#nextSlider').click(function(){
                        step++;
                        CNEXT++;
                        if( step>=limit ) step=0;
                        clearTimeout(lastTimeout);
                        prevFade();
                    });

                    \$j('#previousSlider').click(function(){
                        step--;
                        CNEXT++;
                        if( step<0 ) step=limit-1;
                        clearTimeout(lastTimeout);
                        prevFade();
                    });
                    
                    \$j('#showSlide').hover(function(){
                            \$j('#pauseSlider').css('visibility', startPause?'hidden':'visible');
                            \$j('#playSlider').css('visibility', startPause?'visible':'hidden');
                    });    
                    
                   

                    \$j('#playSlider').click(function(){
                        startPause = false;
                        \$j('#nextSlider').trigger('click');
                    });

                    \$j('#pauseSlider').click(function(){
                        startPause = true;
                        clearTimeout(lastTimeout);
                    });
                    
                });
                /////////////////////////////
                var lastTimeout = null;
                var step = 0;
                var time = 6000;
                var fadeTime = 2000;
                var slidersBanner = \"$data\".split('[--1--]');
                var limit = slidersBanner.length;
                var CNEXT = 0;
                var startPause = false;
                if(limit > 0){
                    \$j.each(slidersBanner, function(key, value){
                        \$j('#new-main-banner-banner').append(value);
                        //if(key===0)\$j('#sliderBanner'+key).css('display', 'block');
                   });
                   
              var limit = slidersBanner.length;                
                
                
                function runSlideBanner(){
                   \$j('#showSlide .a'+CNEXT).fadeOut(fadeTime, prevFade);
                   step++;
                   CNEXT++;
                   if( step>=limit ) step=0;
                }
               }
               
               function prevFade(){
               var content=\$j('#sliderBanner'+step).html().replace(/\{CNEXT\}/g,CNEXT);
                   \$j('#showSlide').html(content);
                 
                   if( !startPause ) lastTimeout = setTimeout(runSlideBanner,time);
               }
               
                prevFade();

                </script>";
        }
    }

}

class com_companiesBanner extends com_frontpageBanner {

    function get($dataset, $parameter) {

        return parent::get($dataset, $parameter);
    }

}

// -----------------------------------------------------------------
// -----------------------------------------------------------------
// base classes
class Banner {

    private $baseClass = 'com_frontpageBanner';

    protected function create($dataset, $parameter) {
        if (is_null($parameter))
            return null;
        $parameter = (class_exists($parameter . "Banner")) ? $parameter . "Banner" : $this->baseClass;
        $class = new $parameter();
        return $class->get($dataset, $parameter);
    }

}

// -----------------------------------------------------------------
// -----------------------------------------------------------------
// call class
final class RevCenterConstruct extends Banner {

    // data
    private $pid = '';
    private $fhid = '';
    private $cobrand = '';
    private $category_id = null;
    private $option = null;
    private $page = null;
    private $width_main = null;
    private $add = null;
    private $sImage = null;
    private $sHeaderImage = null;
    private $lang = array('english' => 'en', 'french' => 'fr');
    private $bCategoryHeader = false;
    // result
    private $result = '';

    function __construct() {
        $this->category_id = !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
        $this->option = !empty($_REQUEST['option']) ? trim($_REQUEST['option']) : "";
        $this->url = !empty($_REQUEST['url']) ? trim($_REQUEST['url']) : "";
        $this->page = !empty($_REQUEST['page']) ? trim($_REQUEST['page']) : "";
        $this->width_main = null;
        $this->add = null;
    }

    // =========================================================================
    // public functions
    // =========================================================================
    function create() {
        global $mosConfig_lang;

        if (isset($_REQUEST['fhid']) & isset($_REQUEST['pid']) & isset($_REQUEST['cobrand'])) {
            $this->pid = $_REQUEST['pid'];
            $this->fhid = $_REQUEST['fhid'];
            $this->cobrand = $_REQUEST['cobrand'];
            setcookie("funeral_FHID", $this->fhid, time() + 24 * 60 * 60);
            setcookie("funeral_PID", $this->pid, time() + 24 * 60 * 60);
            setcookie("funeral_COBRAND", $this->cobrand, time() + 24 * 60 * 60);
        }

        if ($this->pid & $this->fhid & $this->cobrand) {
            $url = 'http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?';
            $url .= "fhid=" . $this->fhid;
            $url .= "&cobrand=" . $this->cobrand;
            $url .= "&pid=" . $this->pid;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $json = curl_exec($ch);
            $funeral = json_decode($json);
        }

        $Obituary = ( isset($funeral->Obituary) ) ? $funeral->Obituary : null;
        if ($Obituary) {
            //$this->sImage = $mosConfig_live_site . "/images/header_images/LegacyHeader.jpg";
            $this->result .= "<a href='http://{$_SERVER['HTTP_HOST']}/index.php?page=shop.browse&category_id=139&option=com_virtuemart&pid={$this->pid}&fhid={$this->fhid}&cobrand={$this->cobrand}' id='FuneralHeader" . ( ( $mosConfig_lang == 'french' ) ? "FR" : "" ) . "'>
                                <div class='shift-legacy'>
                                    " . ( ( $mosConfig_lang == 'french' ) ? "Envoyer des fleurs à la mémoire des" : "Send Flowers in Memory of" ) . " <br/>{$Obituary->FullName}
                                </div>
                              </a>";
        } else {
            $banners = $this->queryBanneHref();
            $banner_href = $banner_title = $banner_image = array();
            if ($banners) {
                foreach ($banners as $row) {
                    $banner_href[$row->id] = (isset($row->href)) ? $row->href : '';
                    $banner_title[$row->id] = (isset($row->title)) ? $row->title : '';
                    $banner_image[$row->id] = (isset($row->image)) ? $row->image : '';
                }
            }
            $this->bCategoryHeader = false;
            if ($this->option == "com_virtuemart" && $this->page == "shop.browse" && $this->category_id > 0) {
                $this->_updateImage();
                $this->bCategoryHeader = true;
                if ($this->sImage) {
                    $this->sHeaderImage = $this->sImage;
                } else {
                    $this->sImage = ( $mosConfig_lang == 'french' ) ? 2 : 1;
                    $this->sImage = $banner_image[$this->sImage];
                    $this->updateImageHeader($this->sImage);
                }

                $this->result .= $this->standartBanner();
            }

            if (( $this->option == 'com_landingpages' ) || ( $this->option == 'com_landingbasketpages' )) {
                $this->width_main = ( $this->option == 'com_landingpages' ) ? ( ( $banners[0]->enable_location ) ? '50%' : '100%' ) : '100%';
                $this->add = ( $this->option == 'com_landingpages' ) ? '' : 'gift_';
            } else {
                $this->width_main = '100%'; //'735px';
                $this->add = ( $this->option == 'com_landingpages_funeral' ) ? 'funeral_' : '';
            }
            if (!$this->bCategoryHeader) {
                //landing banner center
                switch ($mosConfig_lang) {
                    case 'french':
                        //$this->updateImageHeader($banner_image[2]);
                        if ($this->checkLanding()) {
                            $bannerData = $this->bannerData();
                            $stxt = "Composez";
                            $prov = ( isset($prov) ) ? $prov : $bannerData[0]->province;
                            $this->result .= $this->landingBanner(
                                    array('city' => $bannerData[0]->city, 'province' => $bannerData[0]->province, 'text' => $stxt, 'phone' => $bannerData[0]->telephone)
                            );
                        } elseif ($this->checkCompany()) {
                            $this->result .= $this->companyBanner();
                        } else {
                            $this->result .= $this->standartBanner();
                        }
                        break;
                    case 'english':
                    default:
                        //$this->updateImageHeader($banner_image[1]);
                        if ($this->checkLanding()) {
                            $bannerData = $this->bannerData();
                            $prov = $stxt = "";
                            if (( $this->option == 'com_landingpages' ) || ( $this->option == 'com_landingbasketpages')) {
                                $prov = "," . $bannerData[0]->province;
                                
                                //$city_name = ($_REQUEST['type'] == 'basket') ? ' Gift Basket Delivery' : ' Flower Delivery';
                                
                                if ($_REQUEST['type'] == 'basket') {
                                    $city_name = ' Gift Basket Delivery';
                                }
                                elseif ($_REQUEST['type'] == 'sympathy') {
                                    $city_name = ' Sympathy Flowers';
                                }
                                else {
                                    $city_name = ' Florist Online';
                                }
                                
                                $stxt = "Call Now !";
                            } elseif ($this->option == 'com_landingpages_funeral') {
                                $prov = ' Sympathy Flowers';
                                $stxt = "";
                                $city_name = '';
                            }
                            $this->result .= $this->landingBanner(
                                    array('city' => $bannerData[0]->city . $city_name, 'province' => $prov, 'text' => $stxt, 'phone' => $bannerData[0]->telephone)
                            );
                        } elseif ($this->checkCompany()) {
                            $this->result .= $this->companyBanner();
                        } else {
                            $this->result .= $this->standartBanner();
                        }
                        break;
                }
            }
        }


        return $this->result;
    }

    // =========================================================================
    // private functions
    // =========================================================================
    private function _updateImage() {
        global $mosConfig_lang;
        $rows = $this->queryBanneHrefLanding();
        switch ($mosConfig_lang) {
            case 'french':
                $this->updateImage($rows[0]->header_image_fr);
                break;
            case 'english':
            default:
                $this->updateImage($rows[0]->header_image);
        }
    }

    private function queryBanneHrefLanding() {
        return $this->loadObjectList("SELECT * FROM #__vm_category_header_img	WHERE category_id = '$this->category_id'");
    }

    private function queryBanneHref() {
        $query = "SELECT * FROM jos_vm_edit_banner_href ORDER BY id ASC";
        if ($this->option == 'com_landingpages') {
            $query = "SELECT case `lang` when 'en' then 1 else 2 end AS id, `href` FROM  jos_vm_landing_page_title_categoty WHERE position='top' ORDER BY 1 ";
        } elseif ($this->option == 'com_landingbasketpages') {
            $query = "SELECT case `lang` when 'en' then 1 else 2 end AS id, `href` FROM `jos_vm_landing_page_gift` WHERE `position` = 'top' ORDER BY 1";
        } elseif ($this->option == 'com_landingpages_funeral') {
            $query = "SELECT case `lang` when 'en' then 1 else 2 end AS id, `href` FROM `jos_vm_landing_page_funeral_banner` WHERE `position` = 'top' ORDER BY 1";
        }
        return $this->loadObjectList($query);
    }

    private function updateImageHeader($image) {
        global $mosConfig_live_site, $mosConfig_lang;
        if ($this->checkLanding()) {
            $this->sHeaderImage = "$mosConfig_live_site/images/{$this->lang[$mosConfig_lang]}/landing_main_{$this->add}{$this->lang[$mosConfig_lang]}0.png";
        } elseif ($this->checkcompany()) {
            $this->sHeaderImage = "$mosConfig_live_site/images/banners/$image";
        } else {
            $this->sHeaderImage = "$mosConfig_live_site/images/banners/$image";
        }
    }

    private function updateImage($image) {
        global $mosConfig_live_site, $mosConfig_absolute_path;
        $this->sImage = (!empty($image) && is_file($mosConfig_absolute_path . "/images/header_images/" . $image) ) ? $mosConfig_live_site . "/images/header_images/" . $image : null;
    }

    private function standartBanner($href = null) {
        global $database, $iso_client_lang;
        /* $dataset['link'] = $href;
          $dataset['sHeaderImage'] = $this->sHeaderImage; */
        $query_queue = "SELECT options FROM tbl_options where type = 'slider'";
        $database->setQuery($query_queue);
        $result_queue = $database->loadResult();
        if($result_queue=="random"){
            $order_by = 'ORDER BY rand()';
        }else{
            $order_by = 'ORDER BY queue';
        }
        $query = "SELECT * FROM jos_vm_slider WHERE  public='1' ".$order_by;

        $database->setQuery($query);
        $result = $database->loadObjectList();
        if (!$result)
            return null;
        return parent::create($result/* $dataset */, $this->option);
    }

    // $dataset: array( 'link' => '', 'city' => '', 'province' => '', 'text' => '', 'phone' => '' )
    private function landingBanner($dataset) {
        $dataset['width_main'] = $this->width_main;
        $dataset['sHeaderImage'] = $this->sHeaderImage;
        return parent::create($dataset, $this->option);
    }

    private function companyBanner() {

        global $database, $iso_client_lang;

        $query_slider = "SELECT slider FROM tbl_company_pages WHERE url='$this->url'";
        $database->setQuery($query_slider);
        $result = $database->loadObjectList();
        $slider = json_decode($result[0]->slider, true);
        if (!empty($slider)) {
            $query_slider = "SELECT slider,url,src_slider1,src_slider2,src_slider3 FROM tbl_company_pages WHERE url='$this->url'";
            $database->setQuery($query_slider);
            $result_slider = $database->loadObjectList();
            $slider = json_decode($result_slider[0]->slider, true);
            $result = null;
            $i = 1;
            foreach ($slider as $k => $s) {
                $a = 'src_slider' . $i;
                $result[$k]->src = $result_slider[0]->{$a};
                $result[$k]->image = $s;
                $result[$k]->url = $result_slider[0]->url;
                $i++;
            }
        } else {
            $query = "SELECT * FROM jos_vm_slider WHERE public='1' AND lang='$iso_client_lang' AND type='company' AND public='1' ORDER BY queue";
            $database->setQuery($query);
            $result = $database->loadObjectList();
        }
        if (!$result)
            return null;
        return parent::create($result/* $dataset */, $this->option);
    }

    private function checkLanding() {
        return ( ( $this->option == 'com_landingpages' ) || ( $this->option == 'com_landingbasketpages') || ( $this->option == 'com_landingpages_funeral') ) ? true : false;
    }

    private function checkCompany() {
        return ( $this->option == 'com_companies' ) ? true : false;
    }

    private function bannerData() {
        $url = trim(mosGetParam($_GET, "url"));
        $lang = trim(mosGetParam($_GET, "lg"));
        $query = null;
        if (( $this->option == 'com_landingpages' ) || ( $this->option == 'com_landingbasketpages' )) {
            $query = "SELECT * FROM tbl_landing_pages WHERE  (url='$url' OR url='" . str_replace("/", "", $url) . "') ";
        }
        if ($this->option == 'com_landingpages_funeral') {
            $query = "SELECT * FROM tbl_landing_pages_funeral WHERE  url='$url'  ";
        }
        if ($this->option == 'com_companies') {
            $query = "SELECT * FROM tbl_company_pages WHERE url='$url'  ";
        }

        return $this->loadObjectList($query);
    }

    private function loadObjectList($query) {
        global $database;
        $database->setQuery($query);
        return $database->loadObjectList();
    }

}

// -----------------------------------------------------------------

$RevCenterConstruct = new RevCenterConstruct();
echo $RevCenterConstruct->create();
?>