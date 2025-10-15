<?php
////////////////////////////////////////////////////1111122222
// START save_cart
// -----------------------------------------------------------

if (isset($_POST['Submit']) && $_POST['Submit'] == 'Logout' && isset($_SESSION['user_save_cart']))
    unset($_SESSION['user_save_cart']);
$default_tz = date_default_timezone_get();
date_default_timezone_set('Australia/Sydney'); // Normal zone
$saveDate = date("Y-m-d H:i:s", time()); // Normal time
$saveUserId = ( isset($_SESSION["auth"]["user_id"]) ) ? $_SESSION["auth"]["user_id"] : null;
date_default_timezone_set($default_tz);
if (!isset($_SESSION['user_save_cart']) && $saveUserId) {
    $cart = null;
    for ($i = 0; $i < $_SESSION['cart']['idx']; $i++) {
        $cart .= ( ( $cart ) ? ',' : '' ) . ( $_SESSION["cart"][$i]["product_id"] . ':' . $_SESSION["cart"][$i]["quantity"] );
    }
    $q = "SELECT date, cart FROM jos_vm_save_cart WHERE user_id='$saveUserId'";
    $database->setQuery($q);
    $saveResult = $database->loadObjectList();
    if (!$saveResult) {
        $q = "INSERT INTO jos_vm_save_cart ( user_id, cart, date ) VALUES ( '$saveUserId', '$cart', now() )";
        $database->setQuery($q);
        $database->query();
    } else {
        if ($cart == '')
            $cart = $saveResult->cart;
        $q = "UPDATE jos_vm_save_cart SET cart='$cart', date=now() WHERE user_id='$saveUserId'";
        $database->setQuery($q);
        $database->query();
    }
    $_SESSION['user_save_cart'] = true;
}
// ---------------------------------------------------------------
// END save_cart
$pageR = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : null;
$option = !empty($_REQUEST['option']) ? trim($_REQUEST['option']) : "";



$category_id = !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
$page = !empty($_REQUEST['page']) ? trim($_REQUEST['page']) : "";
header('Content-Type: text/html; charset=utf-8');

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
require_once "$mosConfig_absolute_path/language/modulesLanguage.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml"  lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
        <meta name="google-site-verification" content="Jks0OL2yDsKvuGy1U--DjZ5-eloaSfmFjP2ZY-SvTD8" />
        <meta name="format-detection" content="telephone=no" />
        <?php mosShowHead(); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/css/template_css.css?renew=092817" />
        <?php
        /*
          for christmas - yes
          not for christmas - no
         */
        ?>
        <?php
        /*
          <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/css/template_red.css?renew=092817" />
         * 
         */
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/css/newdesign.css?renew=092817" />
        <!--[if lt IE 9]>
            <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!-- Standard iPhone, iPod touch -->
        <link rel="apple-touch-icon" sizes="57x57" href="/templates/<?php echo $cur_template; ?>/images/apple-touch-icon-114.png" />
        <!-- Retina iPhone, iPod touch -->
        <link rel="apple-touch-icon" sizes="114x114" href="/templates/<?php echo $cur_template; ?>/images/apple-touch-icon-114.png" />
        <!-- Standard iPad -->
        <link rel="apple-touch-icon" sizes="72x72" href="/templates/<?php echo $cur_template; ?>/images/apple-touch-icon-144.png" />
        <!-- Retina iPad -->
        <link rel="apple-touch-icon" sizes="144x144" href="/templates/<?php echo $cur_template; ?>/images/apple-touch-icon-144.png" />
        <script type="text/javascript">
            sImgLoading = "<?php echo $GLOBALS['mosConfig_live_site']; ?>/administrator/components/com_virtuemart/html/jquery_ajax.gif";
            sScriptPath = "<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/js/";
            sLang = "<?php echo $mosConfig_lang; ?>"
        </script>
        <link href='https://fonts.googleapis.com/css?family=Lobster|Muli|Open+Sans:400italic,400,600,700|Open+Sans+Condensed:700|Pathway+Gothic+One&subset=latin,latin-ext' rel='stylesheet' type='text/css'/>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/js/jquery.address.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/js/jquery.simplemodal.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/js/func-1.js?renew=120824"></script>


            <script type="text/javascript">

               //this support adding to cart on search page
               sVM_UPDATING = "<?php echo $_GLOBALS['VM_LANG']->_VM_UPDATING; ?>";
               sSecurityUrl = "<?php echo ( defined(SECUREURL) ? SECUREURL : $_GLOBALS['mosConfig_live_site'] ); ?>";
               sVM_ADD_PRODUCT_SUCCESSFUL = "<?php echo $_GLOBALS['VM_LANG']->_VM_ADD_PRODUCT_SUCCESSFUL; ?>";
               sVM_ADD_PRODUCT_UNSUCCESSFUL = "<?php echo $_GLOBALS['VM_LANG']->_VM_ADD_PRODUCT_UNSUCCESSFUL; ?>";
               var current_page = '<?php echo $pageR; ?>';
               var $j = jQuery.noConflict();
               var validNavigation = false;

               var badBrowser = false;
               if (jQuery.browser.msie) {
                   if (jQuery.browser.version < 10) {
                       badBrowser = true;
                   }
               }
               if (jQuery.browser.opera) {
                   if (jQuery.browser.version < 14) {
                       badBrowser = true;
                   }
               }
               if (jQuery.browser.safari) {
                   badBrowser = true;
               }
               if (isMobile() == false && badBrowser == false) {
                   setTimeout(function () {

                       if (window.history && window.history.pushState && (window.history.state != 'forward')) {
                           console.log("forward pushed");
                           //window.history.pushState('forward', null, window.location.pathname + '#forward');
                       }
                       console.log('pop up declared');
                       if (window.addEventListener) {
                           window.addEventListener("popstate", function (e) {
                               console.log('popupstate ' + e.state)
                               if (e.state == null && window.location.pathname != "/") {
                                   console.log("valinav");
                                   validNavigation = true;
                                   history.go(-1);
                               }
                           });
                       } else {
                           window.attachEvent("popstate", function (e) {
                               console.log('popupstate ' + e.state)
                               if (e.state == null && window.location.pathname != "/") {
                                   console.log("valinav");
                                   validNavigation = true;
                                   history.go(-1);
                               }
                           });
                       }

                   }, 0);
               }
               function isMobile() {
                   return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino|android|ipad|playbook|silk/i.test(navigator.userAgent || navigator.vendor || window.opera) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test((navigator.userAgent || navigator.vendor || window.opera).substr(0, 4)))
                   return false;
               }

               function wireUpEvents() {

                   // Attach the event keypress to exclude the F5 refresh
                   $j(document).bind('keypress', function (e) {
                       if (e.keyCode == 116) {
                           validNavigation = true;
                       }
                   });
                   // Attach the event click for all links in the page
                   $j("a").click(function () {
                       validNavigation = true;
                   });
                   $j(".show-cart-now").bind("click", function () {
                       validNavigation = true;
                   });
                   // Attach the event submit for all forms in the page
                   $j("form").bind("submit", function () {
                       validNavigation = true;
                   });
                   // Attach the event click for all inputs in the page
                   $j("input[type=submit]").bind("click", function () {
                       validNavigation = true;
                   });
               }

               function getCookie(name) {
                   var matches = document.cookie.match(new RegExp(
                           "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                           ));
                   return matches ? decodeURIComponent(matches[1]) : undefined;
               }

               function popupGift() {
                   var prodId = $j('#popup_prid').val();
                   var price = $j('#popup_price').val();
                   var data = "option=com_virtuemart&page=shop.cart&func=cartadd2&action=ajax&product_id=" + prodId + "&category_id=&quantity=1&price=" + price + "&ajaxSend=undefined";
                   $j.ajax({
                       url: "<?php echo $GLOBALS['mosConfig_live_site']; ?>/index.php",
                       type: "POST",
                       data: data,
                       success: function (data) {
                           //    console.log('============' + data + '============');
                       }
                   });
               }

               function imgPreload(imgSrc) {
                   image = new Image();
                   image.src = imgSrc;
               }
               imgPreload("<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/exit_pop_up_3.png");
               var popupDate = new Date(new Date().getTime() + 24 * 3600 * 1000);
               var myId = <?php echo $my->id; ?>;
               if (myId != 0) {
                   //     document.cookie = "shown_popup=1; path=/; expires=" + popupDate;
               }




               function closePopup() {
                   $j('#popup_wrapper').css('display', 'none');
                   $j('#confirmExit').css('display', 'none');
                   showGiftMessage();
               }
               function closePopupDelay() {
                   var date = new Date();
                   var coeff = 1000 * 5;
                   var delay = coeff - (Math.floor(date.getTime())) % coeff;
                   setTimeout(function () {
                       closePopup();
                   }, delay);
               }

               function showGiftMessage() {
<?php
if (($pageR == "checkout.index") || ($pageR == "shop.cart")) {
    echo "location.reload();";
} else {
    ?>
                       $j.post(cartLink,
                               {
                                   getCart: true,
                                   lang: '<?php echo $iso_client_lang; ?>'
                               },
                               function (data) {
                                   var issetData = false;
                                   var countLines = 0;
                                   var countLinesLimit = 5;
                                   cartValue(data.cart.length, data.total);
                               }, 'json');
                       var prodName = $j('#popup_prod_name').val();
                       $j('#newCartReveal').html('<tbody><tr><td>We have added a  ' + prodName + '  to your cart!');
                       $j('#newCartReveal').css('display', 'block');
                       var date = new Date();
                       var coeff = 1000 * 10;
                       var delay = coeff - (Math.floor(date.getTime())) % coeff;
                       setTimeout(function () {
                           $j('#newCartReveal').css('display', 'none');
                           $j('#newCartReveal').html('');
                           //   getCart();
                       }, delay);
<?php } ?>
               }
               // webkit hack

               $j(window).load(function () {
                   if (isMobile() == false && badBrowser == false) {
                       //console.log('beforeuplaod declared');
<?php if ($_REQUEST['page'] != 'checkout.index') { ?>
                           document.cookie = "shown_popup=1; path=/; expires=-1";
                           /*window.onbeforeunload = function () {
                            
                            if (!(validNavigation)) {
                            if (getCookie('shown_popup') != 1 && window.location.hash != '#forward') {
                            $j('.popup_wrapper').css('display', 'block');
                            $j('#confirmExit').css('display', 'block');
                            var message = $j('#popup_message').val();
                            popupGift();
                            document.cookie = "shown_popup=1; path=/; expires=" + popupDate;
                            return message;
                            }
                            }
                            }*/
<?php } ?>

                       $j(document).mousemove(function () {
                           wireUpEvents();
                       });
                   } else {
                       if ($j('[name=corners]').length) {
                           document.getElementsByName("corners")[0].style.display = "none";
                           document.getElementsByName("corners")[1].style.display = "none";
                       }
                   }

                   $j('#hidden-testimonial').show();

               });




            </script>



            <link href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/css/media-queries.css?ref=1" rel="stylesheet" type="text/css"/>
            <link rel="shortcut icon" href="https://bloomex.ca/images/bloomex.ico"/>
            <style>
                #confirmExit {
                    position: fixed;
                    left: 25%;
                    top: 50%;
                    display: none;
                    z-index: 3101;
                    color:#8B008B;
                    font-size: 9pt;
                    font-weight: bold;
                    height: 250px;
                    width: 848px;
                    background-image: url(<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/exit_pop_up_3.png);
                }
            </style>
            <script>
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                            m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-50366851-1', 'bloomex.com.au');
                ga('send', 'pageview');
                function showDiv() {
                    $j('.popup_wrapper').css('display', 'block');
                    $j('#confirmExit').css('display', 'block');
                }

            </script>


            <meta name="msvalidate.01" content="C7183B66539664B3B5324016205F7413" />          
            <!-- Yandex.Metrika counter -->
<!--            <script src="https://mc.yandex.ru/metrika/watch.js" type="text/javascript"></script>-->
<!--            <script type="text/javascript">-->
<!--                            try {-->
<!--                                var yaCounter41621419 = new Ya.Metrika({-->
<!--                                    id: 41621419,-->
<!--                                    clickmap: true,-->
<!--                                    trackLinks: true,-->
<!--                                    accurateTrackBounce: true,-->
<!--                                    webvisor: true,-->
<!--                                    trackHash: true-->
<!--                                });-->
<!--                            } catch (e) {-->
<!--                            }-->
<!--            </script>-->
<!--            <noscript><div><img src="https://mc.yandex.ru/watch/41621419" style="position:absolute; left:-9999px;" alt="" /></div></noscript>-->
            <!-- /Yandex.Metrika counter -->
    </head>
    <body >
        <?php
        $q = "SELECT * FROM tbl_exit_page_pop_up WHERE lang='" . $iso_client_lang . "' AND active=1";
        $resultPopup = $database->setQuery($q);
        $database->query();
        $resultPopup = $database->loadAssocList();
        if (!empty($resultPopup)) {
            $isShowPopup = 1;
        } else {
            $isShowPopup = 0;
        }
        $indx = rand(0, (count($resultPopup) - 1));

        $prodId = $resultPopup[$indx]['product_id'];
        $popupMes = $resultPopup[$indx]['message'];

        $q = "SELECT product_name, product_price, product_thumb_image FROM `jos_vm_product` INNER JOIN `jos_vm_product_price` ON jos_vm_product.product_id = jos_vm_product_price.product_id AND jos_vm_product_price.product_id=" . $prodId;
        $resultPrice = $database->setQuery($q);
        $database->query();
        $resultPrice = $database->loadAssocList();
        $popupPrice = $resultPrice[0]['product_price'];
        $popupPrName = $resultPrice[0]['product_name'];
        $popupPrImg = $resultPrice[0]['product_thumb_image'];


        echo '
            <input type="hidden" id = "show_popup_div" value="' . $isShowPopup . '">
            <input type="hidden" id = "popup_prid" value="' . $prodId . '">
            <input type="hidden" id = "popup_price" value="' . $popupPrice . '">
            <input type="hidden" id = "popup_message" value="' . strip_tags($popupMes) . '">
            <input type="hidden" id = "popup_prod_name" value="' . $popupPrName . '">';
        ?>


        </div>
        <?php ?>
        <style>
            #new_popup2{
                left: -300px;
                position: fixed;
                bottom: 30%;
                width: 348px;
                height: 404px;
                display: block;
                z-index: 101;
            }

            .b-popup-open{
                float: right;
                background-image: url(/templates/bloomex7/images/open_popup.png);
                height: 404px;
                width: 48px;
                cursor: pointer;
                box-shadow: 0px 0px 10px #000;
            }
            .b-popup-content2 {
                /*background-image: url(/templates/bloomex7/images/click_to_call.jpg);*/
                float: left;
                width: 300px;
                height: 404px;
                background-color: #fff;
                border-radius:5px;
                box-shadow: 0px 0px 10px #000;
                background-size: 100% auto;
                background-repeat: no-repeat;
            }
            #lhc_status_container {
                /*display:none !important;*/
            }
            .b-popup-content2 div{
                width: 85%;
                margin: 25px auto;
                font-size: 14px;
                text-align: justify;
            }
            .b-popup-content2 div p{
                margin-bottom: 25px;

            }
            .b-popup-content2 div span{
                font-size: 14px;
                font-weight: bold;
            }
            #new_popup2_mobile_wrapp{
                pointer-events: none;
                position: fixed;
                width: 100%;
                height: 280px;
                bottom: 0px;
            }
            #new_popup2_mobile {
                pointer-events: all;
                margin: 0 auto;
                position: relative;
                bottom: -232px;
                width: 404px;
                height: 280px;
                display: block;
                z-index: 101;
            }
            .b-popup-open_mobile{
                height: 48px;
                width: 404px;
                cursor: pointer;
                box-shadow: 0px 0px 10px #000;
            }
            .b-popup-content2_mobile {
                text-align: center;
                float: left;
                width: 404px;
                height: 232px;
                background-color: #fff;
                border-radius:5px;
                box-shadow: 0px 0px 10px #000;
                background-size: 100% auto;
                background-repeat: no-repeat;
            }
            .b-popup-open_mobile:after {
                background: url(/templates/bloomex7/images/close_popup.png) no-repeat 0 0;
                transform: rotate(90deg);
                content: "";
                position: absolute;
                width: 48px;
                height: 404px;
                top: -177px;
                left: 177px;
                z-index: 1;
            }
            .b-popup-open_mobile_close:after {
                background: url(/templates/bloomex7/images/open_popup.png) no-repeat 0 0;
            }
        </style>
        <?php
        require_once $mosConfig_absolute_path . '/Mobile_Detect.php';
        $detect = new Mobile_Detect;
        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
        if (false/* $deviceType != 'computer' */) {
            ?>
            <div id="new_popup2_mobile_wrapp">
                <div id="new_popup2_mobile">
                    <div class="b-popup-open_mobile"></div>
                    <div class="b-popup-content2_mobile">
                        <div>
                            <p><span>Bloomex offers three Customer Service options...</span></p>
                            <p>For immediate Customer Service please visit our <a href="https://bloomex.com.au/chat/" target="_blank">Live Chat Support</a> (available 24/7)</p>
                            <p>Alternatively you can send an email to <a href="mailto:wecare@bloomex.com.au">wecare@bloomex.com.au</a> with Order # in Subject (up to 12 hour response time)</p>
                            <p>You can also call <a href="tel:1-800-905-147">1-800-905-147</a> (up to 24 hour response time)</p>
                            <p>Thank you - we appreciate your business!</p>
                        </div>
                    </div>

                </div>
            </div>

            <script type="text/javascript">

                jQuery('.b-popup-open_mobile').click(function () {
                    if (jQuery(this).hasClass('popup_close2')) {
                        jQuery(this).removeClass('popup_close2').removeClass('b-popup-open_mobile_close')
                        jQuery('#new_popup2_mobile').animate({
                            bottom: "-232px"
                        }, 1500)
                    } else {
                        jQuery(this).addClass('popup_close2').addClass('b-popup-open_mobile_close')
                        jQuery('#new_popup2_mobile').animate({
                            bottom: "0px"
                        }, 1500)
                    }
                })

            </script>

            <?php
        } else {
            ?>
            <div id="new_popup2" style="display: none">
                <div class="b-popup-content2">
                    <div>
                        <p><span>Bloomex offers three Customer Service options...</span></p>
                        <p>For immediate Customer Service please visit our <a href="https://bloomex.com.au/chat/" target="_blank">Live Chat Support</a> (available 24/7)</p>
                        <p>Alternatively you can send an email to <a href="mailto:wecare@bloomex.com.au">wecare@bloomex.com.au</a> with Order # in Subject (up to 12 hour response time)</p>
                        <p>You can also call <a href="tel:1-800-905-147">1-800-905-147</a> (up to 24 hour response time)</p>
                        <p>Thank you - we appreciate your business!</p>
                    </div>
                </div>

                <div class="b-popup-open"></div>
            </div>

            <script type="text/javascript">

                jQuery('.b-popup-open').click(function () {
                    if (jQuery(this).hasClass('popup_close2')) {
                        jQuery(this).removeClass('popup_close2').css({'backgroundImage': 'url(/templates/bloomex7/images/open_popup.png)'})
                        jQuery('#new_popup2').animate({
                            left: "-300px"
                        }, 1500)
                    } else {

                        jQuery(this).addClass('popup_close2').css({'backgroundImage': 'url(/templates/bloomex7/images/close_popup.png)'})
                        jQuery('#new_popup2').animate({
                            left: "0px"
                        }, 1500)
                    }
                })
            </script>
        <?php } ?>

        <div id='new-wrapper'>
            <div id='new-main-container'>
                <table cellpadding="0" cellspacing="0" id='new-header'>
                    <tr>
                        <td rowspan="2" id="new-bloomex-logo-container">
                            <a href="/" id='new-bloomex-logo'><IMG alt="logo" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/<?php echo modulesLanguage::get('bloomexlogo'); ?>" border="0"/></a>
                        </td>
                        <td rowspan="2" id="new-bloomex-logo-container" align="center" valign="top">
                            <?php mosLoadModules('toplive', -1); ?>
                        </td>
                        <td class="new-header-table-right-cell" style='width: 345px'>
                            <div id='new-deliveries-outside-block'>
                                <?php echo modulesLanguage::get('newDeliveriesOutsideBlock'); ?>
                                <a class="new-deliverys-outside-link" href="https://bloomex.ca/"><img alt="CA" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/ca.png" class="new-deliverys-outside-flag"/> CA</a>&nbsp;<img alt="line" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/line.png" />
                                <a class="new-deliverys-outside-link" href="https://www.serenataflowers.com/"><img alt="UK" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/gb.png" class="new-deliverys-outside-flag"/> UK</a>&nbsp;<img alt="line" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/line.png" />
                                <a class="new-deliverys-outside-link" href="https://bloomexusa.com/"><img alt="USA" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/us.png" class="new-deliverys-outside-flag"/> US</a> <img alt="line" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/line.png" />
                                <a class="new-deliverys-outside-link" href="https://www.hipper.com/"><img alt="FR" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/fr.png" class="new-deliverys-outside-flag"/> FR</a>
                            </div>
                        </td>
                        <td style="padding-left: 5px;" class="new-header-table-right-cell">
                            <div style="padding-top: 10px; float: left">
                                <a href="https://m.bloomex.com.au">
                                    <img alt="mobile site" style="cursor: pointer; height: 20px;" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/mobile_site_en.png">
                                </a>
                            </div>
                            <div style="padding-top: 10px; padding-left: 10px; float: left">
                                <a href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/index.php?option=com_contact">
                                    <img alt="contact us"  style=" cursor: pointer; height: 20px;" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/contact_us_en.png" />
                                </a>
                            </div> 
                            <div id="new-account-menu" style=""><ul id="mainlevelnew-account-menu-item"><li><a href="/index.php?page=account.index&amp;option=com_virtuemart&amp;Itemid=465" class="mainlevelnew-account-menu-item">My Account</a></li>
                                    <?php if (isset($_SESSION['legacy_id'])) { ?>
                                        <li><a href="/index.php?option=com_virtuemart&page=checkout.index_legacy" class="mainlevelnew-account-menu-item">Return to Checkout</a></li>
                                    <?php } else { ?>
                                        <li><a href="/index.php?page=shop.cart&amp;option=com_virtuemart&amp;Itemid=80" class="mainlevelnew-account-menu-item">Shopping Cart</a></li>
                                    <?php } ?>
                                </ul>
                        </td>
                        <td width="350" class="new-header-table-right-cell"><div id="new-account-menu"><?php mosLoadModules('accmenu', -1); ?></div></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="new-header-table-right-cell">
                            <table width="100%"  cellpadding="0" cellspacing="0" align="right">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td width="346">
                                        <?php mosLoadModules('newsearch', -1); ?>
                                    </td>
                                    <td width="204">
                                        <?php mosLoadModules('revealcart', -1); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div id="new-main-menu"><?php /* mosLoadModules('mainmenu', -1); */ ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="width: 100%;">
                            <div id="new-product-menu">
                                <?php mosLoadModules('revealmenu', -1); ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <?php mosLoadModules('revealbann', -1); ?>

                <div id='new-content'><?php mosLoadModules('newbody', -1); ?></div>

                <div id='new-footer'><?php mosLoadModules('newfooter', -1); ?></div>
            </div>
        </div>
        <div id="popup_wrapper" class="popup_wrapper" onmousemove="closePopupDelay();
                        return false;" onclick="closePopup();
                                return false;"></div>
        <div id="confirmExit">
            <table id="popup-table">
                <tr>
                    <td>
                        <div id="popup_label">
                            <?php
                            echo $popupMes;
                            ?>
                        </div>
                    </td>

                    <td>
                        <div id="popup-product-img">
                            <img alt="image" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/components/com_virtuemart/shop_image/product/<?php echo $popupPrImg; ?>">
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Google Code for Remarketing Tag -->
        <!--------------------------------------------------
        Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
        --------------------------------------------------->
        <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = 970258087;
            var google_custom_params = window.google_tag_params;
            var google_remarketing_only = true;
            /* ]]> */
        </script>
        <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
        </script>

        <noscript>
            <div style="display:inline;">
                <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/970258087/?value=0&amp;guid=ON&amp;script=0"/>
            </div>
        </noscript>
        <?php
        if ($deviceType != 'computer') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ()
                {
                    function getCookie(name) {
                        var matches = document.cookie.match(new RegExp(
                                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                                ));
                        return matches ? decodeURIComponent(matches[1]) : undefined;
                    }

                    document.getElementById('pop2').onclick = function ()
                    {
                        document.getElementById('pop_div').style.display = 'none';
                    }

                    var block2 = getCookie("pop2");

                    // if (block2 != 1)
                    if (false)
                    {
                        setTimeout(function () {
                            //   jQuery('#pop_div').show();
                            document.cookie = "pop2=1";
                        }, 3000);
                    }

                    function countdownmanual(elementName, minutes, seconds)
                    {
                        var element, endTime, hours, mins, msLeft, time;

                        function twoDigits(n)
                        {
                            return (n <= 9 ? "0" + n : n);
                        }

                        function updateTimer()
                        {
                            msLeft = endTime - (+new Date);
                            if (msLeft < 1000) {
                                jQuery('.pop_div').hide();
                            } else {
                                time = new Date(msLeft);
                                hours = time.getUTCHours();
                                if (!hours) {
                                    hours = "00";
                                }
                                mins = time.getUTCMinutes();
                                element.innerHTML = (hours ? hours + ':' + twoDigits(mins) : mins) + ':' + twoDigits(time.getUTCSeconds());
                                setTimeout(updateTimer, time.getUTCMilliseconds() + 500);
                            }
                        }

                        element = document.getElementById(elementName);
                        endTime = (+new Date) + 1000 * (60 * minutes + seconds) + 500;
                        updateTimer();
                    }
                    //  countdownmanual("countdown_pop", 15, 00);
                });
            </script>

            <style>
                #pop_div{
                    display: none;
                    height: 215px;
                    font-weight: bold;
                    position: fixed;
                    width: 1230px;
                    bottom: 0px;
                    z-index: 100;
                }
                .countdown-amount,#countdown_pop{
                    font-size: 35px !important;
                }
                .pop2 a{
                    color: #000;
                }
                .pop2{
                    padding-top: 10px;
                    position: absolute;
                    bottom: 0;
                    left: 450px;
                    width: 270px;
                    background-image: url('images/pop_landing_bg_red.png');
                    height: 180px;
                    background-size: 270px 180px;
                    background-repeat: no-repeat;
                    text-align: center;
                    color: #000;
                    font-size: 23px;
                }
                #lhc_need_help_container {
                    display: none;
                }
            </style>

            <div id="pop_div">
                <a href="tel:+1300361597" id="pop2">
                    <div class="pop2">CLICK TO CALL<br/>SAVE 15% NOW<br/>CODE = MBX15<div id="countdown_pop"></div>
                    </div>
                </a>
            </div>
    <?php
}
if ($deviceType == 'computer') {
    ?>

            <?php
        }
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
                po.src = 'https://chat.bloomex.ca/index.php/chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true/(department)/2/(theme)/2?r=' + referrer + '&l=' + location;
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
    </body>
</html>