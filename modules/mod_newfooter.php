<?php

class Table {

    private $countTd = 1;
    private $id = null;
    private $result = null;

    function __construct($countTd = null, $id = null) {
        if (!is_null($countTd))
            $this->countTd = $countTd;
        if (!is_null($id))
            $this->id = " id='$id'";
    }

    function createTable() {
        $this->result = "<table {$this->id} align='center'>";
    }

    // $dataset = aray( 'td1', 'td2', .... 'tdN' );
    function set($dataset) {
        if (is_array($dataset)) {
            $width = (int) ( 100 / count($dataset) );
            $this->result .= "<tr>";
            foreach ($dataset as $value) {
                $this->result .= "<td width='$width%' valign='top'>$value</td>";
            }
            if (count($dataset) < $this->countTd) {
                $colspan = $this->countTd - count($dataset);
                $this->result .= "<td colspan='$colspan'  width='" . ( $width * $colspan ) . "%'></td>";
            }
            $this->result .= "</tr>";
        }
    }

    function getTable() {
        return $this->result .= "</table>";
    }

}

class FooterContentItems {

    private $countTd = 3;
    private $countTr = 1;
    private $limitText = 550;
    private $link = "/index.php?option=com_content&task=view&id=";

    function select() {
        global $database;
        $query = "SELECT C.id, C.title, C.introtext FROM jos_content_frontpage AS CF LEFT JOIN jos_content AS C ON C.id = CF.content_id WHERE C.state = 1";
        $database->setQuery($query, 0, $this->countTr * $this->countTd);
        return $database->loadObjectList();
    }

    function create() {
        $table = new Table(3, 'footer-table-content');
        $table->createTable();
        $result = $this->select();
        if ($result) {
            $dataset = array();
            foreach ($result as $value) {
                if (count($dataset) == 3) {
                    $table->set($dataset);
                    $dataset = array();
                }
                $dataset[] = $this->content($value);
            }
        }
        if (isset($dataset) AND count($dataset) > 0) {
            $table->set($dataset);
        }
        return $table->getTable();
    }

    function content($line) {
        if (strlen($line->introtext) > $this->limitText)
            $line->introtext = preg_replace('/[^ ]+$/s', '', substr($line->introtext, 0, $this->limitText)) . ' ...';
        return "<a href='{$this->link}{$line->id}'><div id='footer-table-content-title'>$line->title</div></a>
                <div id='footer-table-content-introtext'>$line->introtext</div>";
    }

}

if (mosCountModules("user7")) {
    // mosLoadModules('user7');
}

$FooterContentItems = new FooterContentItems();
if (isset($_REQUEST['page']) && ($_REQUEST['page'] != 'checkout.index') && ($_REQUEST['page'] != 'shop.cart')) {
    echo $FooterContentItems->create();
}

if (mosCountModules("user7_2")) {
    //  mosLoadModules('user7_2');
}

global $cur_template, $iso_client_lang;
?>
<div class="container-fluid bottom_1">
    <div class="row">
        <div class="col-xs-12">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 signup_text"> 
                        SIGNUP TO RECEIVE SPECIAL OFFERS via EMAIL & SMS
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 signup_input"> 
                        <form role="form">
                            <div class="input-group">
                                <input type="email" placeholder="Enter Your Email">
                                <span class="input-group-btn">
                                    <span class="glyphicon glyphicon-envelope"></span> SUBSCRIBE
                                </span>
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 signup_social"> 
                        <?php
                        /*
                        <div>
                            <img alt="Blogger" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/Blogger.svg" />
                        </div>-->
<!--                        <div>
                            <a href="https://plus.google.com/111066892299376710481/posts" target="_blank">
                                <img alt="Google+" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/google-plus.svg" />
                            </a>
                        </div>-->*/?>
                        <div>
                            <a href="https://twitter.com/BloomexAU" target="_blank">
                                <img alt="Twitter" src="/templates/<?php echo $cur_template; ?>/images/twitter.svg" />
                            </a>
                        </div>
                        <div>
                            <a href="https://www.facebook.com/BloomexAustralia/" target="_blank">
                                <img alt="Facebook" src="/templates/<?php echo $cur_template; ?>/images/facebook.svg" />
                            </a>
                        </div>
                        <div>
                            <a href="https://instagram.com/bloomex_australia_?igshid=YmMyMTA2M2Y=" target="_blank">
                                <img alt="Instagram" src="/templates/<?php echo $cur_template; ?>/images/instagram.svg" />
                            </a>
                        </div>
                        <div>
                            <a href="https://www.pinterest.com.au/bloomex_australia/" target="_blank">
                                <img alt="Pinterest" src="/templates/<?php echo $cur_template; ?>/images/pinterest.svg" />
                            </a>
                        </div>
                        <div>
                            <a href="/community-partners/">
                                <img alt="Partners" src="/templates/<?php echo $cur_template; ?>/images/Partners.svg" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid bottom_2">
    <div class="row">
        <div class="col-xs-12 site_map">
            <?php
            global $mosConfig_lang;
            if ($mosConfig_lang == 'english') {
                $sitemap_a = '<a href="/sitemap/">Site Map</a>';
            } 
            else {
                $sitemap_a = '<a href="/PlanduSite.html">Plan du Site</a>';
            }
            /*
            ?>
            <a href="/florist/sydney/">Sydney Flowers | </a>
            <a href="/florist/brisbane/">Brisbane Flowers | </a>
            <a href="/florist/perth/">Perth Flowers | </a>
            <a href="/florist/melbourne/">Melbourne Flowers | </a>
            <a href="/florist/adelaide/">Adelaide Flowers</a>
            * 
            */
            ?>
            <?php
            if (is_array($GLOBALS['footer_links'])) {
                if (count($GLOBALS['footer_links']) > 0) {
                    ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 footer-links-wrapper">
                                <?php
                                $hide_cities = '';




                                foreach ($GLOBALS['footer_links'] as $k=>$link_obj) {
                                    if(isMobileDevice()){
                                        if($k>11){
                                            $link_obj->show_hide=false;
                                        }
                                    }
                                    if ($link_obj->show_hide) {
                                        ?>
                                        <div class="col-md-3 col-xs-6">
                                            <a href="<?php echo $link_obj->link; ?>"><?php echo $link_obj->name; ?></a>
                                        </div>
                                        <?php
                                    } else {
                                        $hide_cities .= '<div class="col-md-3 col-xs-6 hidden_cities"  style="display:none"><a href="' . $link_obj->link . '">' . $link_obj->name . '</a></div>';
                                    }
                                }

                                if ($hide_cities) {
                                    echo '<span class="city_load_more">' . _MORE . '</span>' . $hide_cities;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            else {
                echo $GLOBALS['footer_links'];        
            }
            ?>

            <img alt="Site Map" width="15px" height="15px"  src="/templates/<?php echo $cur_template; ?>/images/site-map.svg" /> <?php echo $sitemap_a; ?>
        </div>
    </div>
</div>
<div class="container-fluid bottom_3">
    <div class="row">
        <div class="col-xs-12 copyright">
            &copy; 2005-<?php echo date('Y'); ?> <?php echo $GLOBALS['mosConfig_sitename']; ?>
        </div>
    </div>
</div>
<?php
$query = "SELECT `country_2_code`,`country_3_code` FROM `jos_vm_country`";
$database->setQuery($query);
$res =  $database->loadObjectList();
foreach($res as $r){
    $counrties[$r->country_2_code] = $r->country_3_code;
}
$query = "SELECT `state_2_code`,`state_3_code` FROM `jos_vm_state`";
$database->setQuery($query);
$res =  $database->loadObjectList();
foreach($res as $r){
    $states_short_name[$r->state_3_code] = $r->state_2_code;
}
?>
<script type="text/javascript">
    var client_lang = '<?php echo $iso_client_lang; ?>';
    var countries_json = '<?php echo json_encode($counrties); ?>';
    var states_short_name_json = '<?php echo json_encode($states_short_name); ?>';
</script>
<script src="/templates/<?php echo $cur_template; ?>/js/slick.min.js"></script>
<script src="/templates/<?php echo $cur_template; ?>/js/bootstrap.min.js"></script>
<!--<script src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/js/bootstrap.offcanvas.min.js"></script>-->
<script src="/templates/<?php echo $cur_template; ?>/js/func.js"></script>
<script src="/templates/<?php echo $cur_template; ?>/js/inputmask.js?ref=1"></script>
<script src="/templates/<?php echo $cur_template; ?>/js/bloomex.js?ref=10"></script>
<script src="/templates/<?php echo $cur_template; ?>/js/googleaddress.js?ref=10"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<?php global $prod;
$chatlink = 'https://chat.bloomex.ca';
?>

<script type="text/javascript">
    var LHCChatOptions = {};
    LHCChatOptions.opt = {widget_height: 340, widget_width: 300, popup_height: 520, popup_width: 500, domain: 'bloomex.com.au'};
    (function () {
        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://') + 1)) : '';
        var location = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
        po.src = '<?php echo $chatlink;?>/index.php/chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true/(department)/2/(theme)/2?r=' + referrer + '&l=' + location;
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
    })();

    var auto_close_chat_it = 0;

    function auto_close_chat() {
        if (typeof lh_inst !== 'undefined') {
            auto_close_chat_it++;

            if (auto_close_chat_it == 8) {
                lh_inst.lhc_need_help_hide();
            } else {
                setTimeout('auto_close_chat()', 500);
            }
        } else {
            setTimeout('auto_close_chat()', 500);
        }
    }

    jQuery(document).ready(function () {
        auto_close_chat();
    });
</script>
<?php
mosLoadModules('footercon');
// OLD SCRIPTS
if (isset($_GET['page']) && $_GET['page'] != 'checkout.thankyou') {
    ?>  
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));</script>
    <?php
}
/*
 * <script type="text/javascript">
    try {
        var pageTracker = _gat._getTracker("UA-232639-1");
        pageTracker._trackPageview();
    } catch (err) {
    }
</script>
 */
?>


