<?php
header('Content-Type: text/html; charset=utf-8');

global $database,$VM_LANG,$mosConfig_ga4_gtm,$my,$sef,$mosConfig_absolute_path,$mosConfig_enable_fast_checkout, $mosConfig_live_site,$mosConfig_fast_checkout_salt, $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $restricted_categories_list;
if (!isset($sess)) {
    require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
    $sess = new ps_session;
}
include_once $mosConfig_absolute_path . '/core/db/Query.php';
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
require_once $mosConfig_absolute_path . '/language/modulesLanguage.php';

$searchword = isset($_REQUEST['searchword']) ? trim($_REQUEST['searchword']) : '';
$option = isset($_REQUEST['option']) ? trim($_REQUEST['option']) : '';

if ($sef->landing_type > 0) {
    Switch ($sef->landing_type) {
        case 1:
        case 4:
            $cityType = $VM_LANG->_FLOWER_DELIVERY;
            break;
        case 2:
            $cityType = $VM_LANG->_BASKET_DELIVERY;
            break;
        case 3:
            $cityType = $VM_LANG->_SYMPATHY_DELIVERY;
            break;
    }
}

$sql = "SELECT * FROM `tbl_smm_tools`";
$database->setQuery($sql);
$database->loadObject($smmTools);


$resourceManagers = (new Query())
    ->from('jos_vm_resource_managers')
    ->where(['status' => '1'])
    ->order_by('queue ASC')
    ->all();


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <script>
            var SessionUserId = '<?php echo $my->id ?: ($_SESSION['checkout_ajax']['user_id'] ?:'guest'); ?>';
            var SessionUserName = '<?php echo $my->name ?: 'guest'; ?>';
            var SessionUserEmail = '<?php echo $my->email; ?>';

        </script>
<?php
    foreach ($resourceManagers as $resource) {
        echo includeResource($resource,'header_content');
    }
?>



        <link rel="apple-touch-icon-precomposed" sizes="57x57" href="/apple-touch-icon-57x57.png" />
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/apple-touch-icon-114x114.png" />
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon-precomposed" sizes="60x60" href="/apple-touch-icon-60x60.png" />
        <link rel="apple-touch-icon-precomposed" sizes="120x120" href="/apple-touch-icon-120x120.png" />
        <link rel="apple-touch-icon-precomposed" sizes="76x76" href="/apple-touch-icon-76x76.png" />
        <link rel="apple-touch-icon-precomposed" sizes="152x152" href="/apple-touch-icon-152x152.png" />
        <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196" />
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" />
        <link rel="icon" type="image/png" href="/favicon-128.png" sizes="128x128" />
        <meta name="application-name" content="Bloomex.com.au"/>
        <meta name="msapplication-TileColor" content="#FFFFFF" />
        <meta name="msapplication-TileImage" content="mstile-144x144.png" />
        <meta name="msapplication-square70x70logo" content="mstile-70x70.png" />
        <meta name="msapplication-square150x150logo" content="mstile-150x150.png" />
        <meta name="msapplication-wide310x150logo" content="mstile-310x150.png" />
        <meta name="msapplication-square310x310logo" content="mstile-310x310.png" />
        <meta name="facebook-domain-verification" content="8iappiv5sz0li1lnmnjturvoqu2a9i" />

        <link rel="alternate" hreflang="en-au" href="<?php echo $mosConfig_live_site . ($sef->real_uri  ? '/'.$sef->real_uri.'/' : '/'); ?>" />
        <meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
        <meta name="google-site-verification" content="Jks0OL2yDsKvuGy1U--DjZ5-eloaSfmFjP2ZY-SvTD8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5"/>
        <meta name="msvalidate.01" content="33B5C4121F677B9E2E6FDD6C43A0D68F" />

        <link rel="preload stylesheet" type="text/css" href="/templates/bloomex_adaptive/css/jquery-ui.css" as="style">
        <link rel="preload stylesheet" type="text/css" href="/templates/bloomex_adaptive/css/bootstrap.min.css?renew=0186"  as="style"/>
        <link rel="preload stylesheet" type="text/css" href="/templates/bloomex_adaptive/css/bloomex_adaptive.css?renew=0186"  as="style"/>
        <link rel="preload stylesheet" type="text/css" href="/templates/bloomex_adaptive/css/bloomex_adaptive_media.css?renew=0186"  as="style"/>
        <script language="javascript" src="/templates/bloomex_adaptive/js/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script language="javascript" src="/templates/bloomex_adaptive/js/jquery-ui.js" type="text/javascript"></script>
        <script language="javascript" src="/includes/js/joomla.javascript.js" type="text/javascript"></script>
        <script language="javascript" src="/includes/js/google_analitics.js?renew=5" type="text/javascript"></script>

        <?php
        echo $sef_metatags;
        if (!empty($sef->canonical)) {
            ?>
            <link rel="canonical" href="<?php echo $sef->canonical; ?>" />
            <?php
        }
        ?>

    </head>
    <body>


        <?php if($smmTools->mobile_coupon_popup && isMobileDevice()){?>
        <div class="mobile_popup">
            <div class="inner">
                <div class="close_btn"></div>
                <div class="title">SAVE 20%</div>
                <div class="description">Code = MBX20</div>
                <a class="btn" href="tel:1800905147">Click to Call and SAVE!</a>
            </div>
        </div>
        <?php } ?>
        <div class="mobile_phone">
            <a href="tel:1800905147">
                <img alt="1 (800) 905-147" src="/templates/<?php echo $cur_template; ?>/images/phone.svg" /> <span>1 (800) 905-147</span>
            </a>
        </div>
        <div class="mobile_menu_wrapper"></div>
        <div class="mobile_menu">
            <?php if (isMobileDevice()) {include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/new_mobile_menu.php';} ?>
        </div>
        <div class="container alert alert-success" id="ExitPopUpPromotion" style="display: none">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            <span style="text-transform: uppercase;"><?= $VM_LANG->_EXIT_POPUP_PROMOTION_MESSAGE ?></span>
        </div>

        <div class="container alert alert-success" id="overlayClientPromotion" style="display: none">
        </div>

        <div class="page">
            <?php
            $query = "SELECT `price` FROM `jos_freeshipping_price` WHERE `public`=1";

            $free_shipping_result = false;
            $database->setQuery($query);
            $database->loadObject($free_shipping_result);
            if($free_shipping_result){
            ?>
                <script>
                    var freeShippingPrice = '<?= $free_shipping_result->price; ?>';
                </script>
                <div class="container top_progress">
                    <div class="row">
                        <div id="freeShippingProgressBar"></div>
                        <p class="spend_more_price"></p>
                    </div>
                </div>
            <?php }?>
            <?php  if($smmTools->free_gift_top_popup
                && !checkProductExistInShoppingCart($smmTools->free_gift_popup_first_product_id)
                && !checkProductExistInShoppingCart($smmTools->free_gift_popup_second_product_id)){?>
            <div class="topCountDownMain container-fluid  top_0 alert alert-secondary alert-dismissible hidden-xs">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-4 col-md-offset-1 col-xs-12 col-sm-4 free_gift_text padding0">
                                    <p class="topCountDownText text-center margin0"></p>
                                </div>
                                <div class="col-md-2 col-xs-12 col-sm-4 text-white padding0">
                                    <p id="topCountDown"></p>
                                </div>

                                <div class="col-md-3 col-xs-12 col-sm-4 padding0">
                                    <div class="topCountDownAddToCart">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>

                    function topCountDown(distance,ides,names) {

                        var newId = ($('#topCountDownId').val() == ides[0])?ides[1]:ides[0];
                        var newName = ($('#topCountDownId').val() == ides[0])?names[1]:names[0];
                        $('.topCountDownText').text(newName)
                        $('.topCountDownAddToCart').html(
                            '<div class="form-add-cart" id="div_'+newId+'">' +
                            '<form action="/index.php" method="post" name="addtocart" id="formAddToCart_'+newId+'">' +
                            '<input name="quantity_'+newId+'" class="inputbox" type="hidden" size="3" value="1">' +
                            '<input type="hidden" id="topCountDownId" value="'+newId+'">' +
                            '<div class="add btn btn-danger" product_id="'+newId+'">ADD GIFT TO CART</div>' +
                            '<input type="hidden" name="product_id_'+newId+'" value="'+newId+'></form></div>'
                        );

                        // Update the count down every 1 second
                        var x = setInterval(function() {

                            // Time calculations for minutes and seconds
                            var minutes = Math.floor((distance % (60 * 60)) / (60));
                            var seconds = Math.floor((distance % (60)));

                            // Output the result in an element with id="demo"
                            document.getElementById("topCountDown").innerHTML = "<span>00</span> <span>"+ ('0' + minutes).slice(-2) + "</span> <span> " + ('0' + seconds).slice(-2) + "</span>";
                            document.cookie = "topCountDown="+distance+"; path=/;secure;";
                            distance--;
                            // If the count down is over, write some text
                            if (distance < 1) {
                                clearInterval(x);
                                topCountDown(300,ides,names);
                            }
                        }, 1000);

                    }
                    topCountDown(
                        (document.cookie.replace(/(?:(?:^|.*;\s*)topCountDown\s*\=\s*([^;]*).*$)|^.*$/, "$1"))
                         ?parseInt(document.cookie.replace(/(?:(?:^|.*;\s*)topCountDown\s*\=\s*([^;]*).*$)|^.*$/, "$1"))
                        :300,
                        <?php
                            echo json_encode([$smmTools->free_gift_popup_first_product_id,$smmTools->free_gift_popup_second_product_id]);
                         ?>
                        ,
                        <?php
                        echo json_encode([getProductNameByProductId($smmTools->free_gift_popup_first_product_id),getProductNameByProductId($smmTools->free_gift_popup_second_product_id)]);
                        ?>
                    )
                </script>
            </div><!--top_0-->
            <?php
            }

            if(!isMobileDevice()) {
            ?>
            <div class="container top_1">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-xs-12 col-sm-7  col-md-7 deliveries_outside">
                                    For Flower Deliveries outside of Australia
                                    <a target="_blank" href="https://bloomex.co.nz">
                                        <img alt="New Zealand" width="18px" height="14px" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/Flags/nz.webp" /> NZ
                                    </a>
                                    <a target="_blank" href="https://bloomex.com.au/serenata-flowers/">
                                        <img alt="United Kingdom" width="18px" height="14px" src="/templates/<?php echo $cur_template; ?>/images/Flags/gb.webp" /> UK
                                    </a>
                                    <a target="_blank" href="https://bloomex.ca/">
                                        <img alt="Canada" width="18px" height="14px" src="/templates/<?php echo $cur_template; ?>/images/Flags/CA.webp" /> CA
                                    </a>
                                    <a target="_blank" href="https://bloomexusa.com/">
                                        <img alt="USA" width="18px" height="14px" src="/templates/<?php echo $cur_template; ?>/images/Flags/us.webp" /> US
                                    </a>

                                </div>
                                <div class="col-sm-5 col-md-5 d-none d-sm-block contact_account">
                                    <a href="/contact/">
                                        <img alt="Contact Us" src="/templates/<?php echo $cur_template; ?>/images/contact_us.webp" /> Contact Us
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--top_1-->
            <?php } ?>

            <div class="container top_2 d-none d-lg-block p-3">

                            <div class="d-flex align-items-end top-header">
                                <div class="col-4 col-sm-3 col-md-4 col-lg-2 logo">
                                    <a href="/">
                                        <img class="h-100" alt="Bloomex Australia" src="/templates/<?php echo $cur_template; ?>/images/bloomexlogo.webp" />
                                    </a>
                                </div>
                                <div class=" col-xs-2 col-sm-3 col-md-2 d-xs-none landing h-100">
                                    <div class="landing_inner">
                                        <?php
                                        if ($sef->landing_type > 0) {
                                            ?>
                                            <?php echo $sef->city->city??''; ?> <?php echo $cityType??''; ?>!
                                            <span class="call">Call Now!</span>
                                            <?php $landing_phone = isset($sef->city->phone) ? $sef->city->phone : '1-800-905-147'; ?>
                                            <span class="phone"><a href="tel:<?php echo $landing_phone; ?>"><?php echo $landing_phone; ?></a></span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xs-8 col-sm-6 col-md-8 d-none d-lg-block">

                                        <div class="d-flex flex-row justify-content-end h-100 phone_chat_search">

                                                <div class="align-self-end mx-auto">
                                                    <div class="search input-group">
                                                        <input id="mainsearch" type="text" class="form-control" name="searchword" placeholder="Search" value="">
                                                        <span class="input-group-btn search" id="search_btn">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ab0917" class="bi bi-search" viewBox="0 0 16 16">
                                                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <?php  if($smmTools->show_search_keywords){
                                                        ?>

                                                        <div class="col-xs-12 search_keywords">
                                                            <?php foreach(json_decode($smmTools->keywords) as $keyword) {
                                                                echo '<a href="/search/'.$keyword->keyword_tag.'">'.$keyword->keyword_name.'</a>';
                                                            } ?>
                                                        </div>

                                                    <?php } ?>
                                                    <script type="text/javascript">
                                                        var inputElement = document.getElementById("mainsearch");
                                                        inputElement.addEventListener("keyup", function (event) {
                                                            if (event.keyCode === 13) {
                                                                event.preventDefault();
                                                                var searchValue = inputElement.value;
                                                                var baseUrl = window.location.origin;
                                                                var searchUri = '/search/';
                                                                window.location = baseUrl + searchUri + searchValue + '/';
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                                <div class="align-self-end me-3 text-center">
                                                    <a href="tel:1800905147" style="margin-bottom: 1px">
                                                        <svg width="23" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
                                                            <rect x="0.819824" y="0.5" width="40" height="40" rx="20" fill="#AA0A0C"></rect>
                                                            <path d="M15.4398 19.29C16.8798 22.12 19.1998 24.44 22.0298 25.88L24.2298 23.68C24.5098 23.4 24.8998 23.32 25.2498 23.43C26.3698 23.8 27.5698 24 28.8198 24C29.085 24 29.3394 24.1054 29.5269 24.2929C29.7145 24.4804 29.8198 24.7348 29.8198 25V28.5C29.8198 28.7652 29.7145 29.0196 29.5269 29.2071C29.3394 29.3946 29.085 29.5 28.8198 29.5C24.3111 29.5 19.9871 27.7089 16.799 24.5208C13.6109 21.3327 11.8198 17.0087 11.8198 12.5C11.8198 12.2348 11.9252 11.9804 12.1127 11.7929C12.3003 11.6054 12.5546 11.5 12.8198 11.5H16.3198C16.585 11.5 16.8394 11.6054 17.0269 11.7929C17.2145 11.9804 17.3198 12.2348 17.3198 12.5C17.3198 13.75 17.5198 14.95 17.8898 16.07C17.9998 16.42 17.9198 16.81 17.6398 17.09L15.4398 19.29Z" fill="white"></path>
                                                        </svg>
                                                        <span style="font-size: 16px;color: #ab0917">1 (800) 905-147</span>
                                                        <br><span>Toll-Free 24/7</span>
                                                    </a>
                                                </div>
                                                <div class="align-self-end me-3 text-center">
                                                    <a href="#chat" onclick="openchat();">
                                                        <svg width="20" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1266 22.1995C16.7081 22.5979 17.4463 23.0228 18.3121 23.3511C19.9903 23.9874 21.244 24.0245 21.8236 23.9917C23.1167 23.9184 23.2907 23.0987 22.5972 22.0816C21.8054 20.9202 21.0425 19.6077 21.1179 18.1551C22.306 16.3983 23 14.2788 23 12C23 5.92487 18.0751 1 12 1C5.92487 1 1 5.92487 1 12C1 18.0751 5.92487 23 12 23C13.4578 23 14.8513 22.7159 16.1266 22.1995ZM12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21C13.3697 21 14.6654 20.6947 15.825 20.1494C16.1635 19.9902 16.5626 20.0332 16.8594 20.261C17.3824 20.6624 18.1239 21.1407 19.0212 21.481C19.4111 21.6288 19.7674 21.7356 20.0856 21.8123C19.7532 21.2051 19.4167 20.4818 19.2616 19.8011C19.1018 19.0998 18.8622 17.8782 19.328 17.2262C20.3808 15.7531 21 13.9503 21 12C21 7.02944 16.9706 3 12 3Z" fill="#AA0A0C"/>
                                                        </svg>
                                                        <p class="margin0">Chat</p>
                                                    </a>
                                                </div>
                                                <div class="align-self-end me-3 text-center">
                                                    <a href="/account/">
                                                        <svg width="25" height="27" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12.1304 4C13.1912 4 14.2087 4.42143 14.9588 5.17157C15.7089 5.92172 16.1304 6.93913 16.1304 8C16.1304 9.06087 15.7089 10.0783 14.9588 10.8284C14.2087 11.5786 13.1912 12 12.1304 12C11.0695 12 10.0521 11.5786 9.30194 10.8284C8.5518 10.0783 8.13037 9.06087 8.13037 8C8.13037 6.93913 8.5518 5.92172 9.30194 5.17157C10.0521 4.42143 11.0695 4 12.1304 4ZM12.1304 6C11.5999 6 11.0912 6.21071 10.7162 6.58579C10.3411 6.96086 10.1304 7.46957 10.1304 8C10.1304 8.53043 10.3411 9.03914 10.7162 9.41421C11.0912 9.78929 11.5999 10 12.1304 10C12.6608 10 13.1695 9.78929 13.5446 9.41421C13.9197 9.03914 14.1304 8.53043 14.1304 8C14.1304 7.46957 13.9197 6.96086 13.5446 6.58579C13.1695 6.21071 12.6608 6 12.1304 6ZM12.1304 13C14.8004 13 20.1304 14.33 20.1304 17V20H4.13037V17C4.13037 14.33 9.46037 13 12.1304 13ZM12.1304 14.9C9.16037 14.9 6.03037 16.36 6.03037 17V18.1H18.2304V17C18.2304 16.36 15.1004 14.9 12.1304 14.9Z" fill="#AA0A0C"></path>
                                                        </svg>

                                                        <p class="margin0">Account</p>
                                                    </a>
                                                </div>
                                                <div class="align-self-end  text-center position-relative">
                                                    <a href="/cart/"  id="mobile_cart">
                                                        <svg width="23" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.5405 18C18.071 18 18.5797 18.2107 18.9547 18.5858C19.3298 18.9609 19.5405 19.4696 19.5405 20C19.5405 20.5304 19.3298 21.0391 18.9547 21.4142C18.5797 21.7893 18.071 22 17.5405 22C16.4305 22 15.5405 21.1 15.5405 20C15.5405 18.89 16.4305 18 17.5405 18ZM1.54053 2H4.81053L5.75053 4H20.5405C20.8057 4 21.0601 4.10536 21.2476 4.29289C21.4352 4.48043 21.5405 4.73478 21.5405 5C21.5405 5.17 21.4905 5.34 21.4205 5.5L17.8405 11.97C17.5005 12.58 16.8405 13 16.0905 13H8.64053L7.74053 14.63L7.71053 14.75C7.71053 14.8163 7.73687 14.8799 7.78375 14.9268C7.83063 14.9737 7.89422 15 7.96053 15H19.5405V17H7.54053C6.43053 17 5.54053 16.1 5.54053 15C5.54053 14.65 5.63053 14.32 5.78053 14.04L7.14053 11.59L3.54053 4H1.54053V2ZM7.54053 18C8.07096 18 8.57967 18.2107 8.95474 18.5858C9.32981 18.9609 9.54053 19.4696 9.54053 20C9.54053 20.5304 9.32981 21.0391 8.95474 21.4142C8.57967 21.7893 8.07096 22 7.54053 22C6.43053 22 5.54053 21.1 5.54053 20C5.54053 18.89 6.43053 18 7.54053 18ZM16.5405 11L19.3205 6H6.68053L9.04053 11H16.5405Z" fill="#AA0A0C"/>
                                                        </svg>
                                                        <span class="yellow_cart_items"></span>
                                                        <p class="margin0"  style="margin-top: 3px;">Cart</p>
                                                    </a>
                                                </div>

                                        </div>

                                </div>
                            </div><!--row-->


            </div><!--top_2-->
            <div class="container top_3">
                <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/new_menu.php'; ?>
            </div>
            <!--top_3-->
            <?php
            if ($perm->is_registered_customer($auth['user_id'])) {
                $sqlPendingDeliveryOrder = "SELECT o.order_id,i.user_info_id FROM jos_vm_orders as o
                  join tbl_cart_abandonment as i on 
                  i.user_id=o.user_id and
                  (i.status='wait_delivery_address' OR i.status='sent_shipping_form') and i.user_info_id !='' 
                   WHERE o.order_status='PD' and o.user_id=" . $_SESSION['auth']['user_id'] . " order by o.order_id desc limit 1";
                $database->setQuery($sqlPendingDeliveryOrder);
                $pendingDeliveryOrder = false;
                $database->loadObject($pendingDeliveryOrder);
                if ($pendingDeliveryOrder) {
                    if ($_REQUEST['checkoutStep'] != 2) {

                        if($pendingDeliveryOrder->user_info_id) {
                            $cache = str_rot13($pendingDeliveryOrder->order_id . ';' . ($pendingDeliveryOrder->user_info_id??''));
                            $link_href = '/checkout/2/' . $cache;
                        }else{
                            $from_string = array("{order_id}", "{user_id}");
                            $to_string   = array($pendingDeliveryOrder->order_id, $pendingDeliveryOrder->user_id);
                            $cache = str_replace($from_string, $to_string, $mosConfig_fast_checkout_salt);
                            $link_href = '/fast-checkout-shipping-form/' . $cache;
                        }
                        mosRedirect($link_href . '/');
                        die;
                    }
                    ?>
                    <div class="container top_4">
                        <div class="row alert alert-danger alert-dismissible show fill_delivery_info_alert"
                             style="margin-bottom:0px;text-align: center;background: red;color: white;text-transform: uppercase;text-decoration: underline;font-size: 18px;margin-bottom: 0px;text-align: center;" role="alert">
                            <a style="color:white" target="_blank" href="<?php echo $link_href??''; ?>">PLEASE ADD DELIVERY INFORMATION <strong>HERE</strong></a>

                        </div>
                    </div>

                    <?php
                }
            }

            if ($sef->homepage || ($sef->real_uri == "best_sellers")) {
                ?>
            <div class="container top_4">
                <div class="row">
                    <div class="col-md-12 padding0 sale_banners">
                        <?php  include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/slider.php'; ?>
                    </div>
                </div>
            </div>
                <div class="container top_5">
                    <?php if(!isMobileDevice()) { include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/categories.php';} ?>
                </div>
                <?php
            }

            if (isset($_REQUEST['mosmsg'])) {
                ?>
                <div class="container top_5">

                    <div role="alert" class="col-sm-12 alert <?php echo isset($_REQUEST['mosmsgsuccess'])?'alert-info':'alert-danger'; ?> alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><?php echo urldecode($_REQUEST['mosmsg']); ?></div>

                </div>
            <?php }

                mosLoadModules('newbody', -1);

                if (!((isset($_REQUEST['page']) && (in_array($_REQUEST['page'], ['new_checkout.index', 'shop.cart', 'checkout.thankyou', 'shop.product_details']))))) {
                    include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/today_special.php';
                }

                if ($sef->homepage) {
                 include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/surveys.php';
                }


            foreach ($resourceManagers as $resource) {
                echo includeResource($resource,'body_content');
            }

            ?>
            <div id="subscribe-alert" class="container alert " role="alert">
                <p id="alert-text" class="text-center m-0"></p>
            </div>
            <div  class=" container bottom_1">
                <div class="row">

                                <div class="col-12 col-md-12 col-lg-12 col-xl-4 signup_text px-3">
                                    SIGNUP TO RECEIVE SPECIAL OFFERS via EMAIL & SMS
                                </div>
                                <div class="col-12 col-md-6 col-lg-6 col-xl-4 signup_input px-3">
                                    <form role="form" class="d-flex justify-content-center">

                                            <input type="email" id="subscribe_input_email" class="subscribe_input" placeholder="Enter Your Email">
                                                <span class="subscribe_button input-group-btn" onclick="subscribeUser(); return false">
                                                    <span class="glyphicon glyphicon-envelope"></span> SUBSCRIBE
                                                </span>

                                    </form>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6 col-xl-4 signup_social text-right px-3">
                                    <?php /*
                                      <div>
                                      <img alt="Blogger" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/Blogger.svg" />
                                      </div>-->
                                      <!--                        <div>
                                      <a href="https://plus.google.com/111066892299376710481/posts" target="_blank">
                                      <img alt="Google+" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/google-plus.svg" />
                                      </a>
                                      </div>--> */ ?>
                                    <div>
                                        <a href="https://twitter.com/BloomexAU" target="_blank">
                                            <img alt="Twitter" src="/templates/<?php echo $cur_template; ?>/images/icon_x.webp" />
                                        </a>
                                    </div>
                                    <div>
                                        <a href="https://www.facebook.com/BloomexAustralia/" target="_blank">
                                            <img alt="Facebook" src="/templates/<?php echo $cur_template; ?>/images/icon_facebook.webp" />
                                        </a>
                                    </div>
                                    <div>
                                        <a href="https://www.instagram.com/bloomex_australia_/" target="_blank">
                                            <img alt="Instagram" src="/templates/<?php echo $cur_template; ?>/images/icon_instagram.webp" />
                                        </a>
                                    </div>
                                    <div>
                                        <a href="https://www.pinterest.com.au/bloomex_australia/" target="_blank">
                                            <img alt="Pinterest" src="/templates/<?php echo $cur_template; ?>/images/icon_pinterest.webp" />
                                        </a>
                                    </div>
                                    <div>
                                        <a href="/community-partners/">
                                            <img alt="Partners" src="/templates/<?php echo $cur_template; ?>/images/icon_partners.webp" />
                                        </a>
                                    </div>
                                    <div>
                                        <a href="https://blog.bloomex.com.au/" target="_blank">
                                            <img alt="Blog" src="/templates/<?php echo $cur_template; ?>/images/icon_blog.webp" />
                                        </a>
                                    </div>
                                </div>

                </div>
            </div>
            <div class="container bottom_2">
                <div class="row">
                    <div class="col-xs-12 site_map">
                        <?php
                            $sitemap_a = '<a href="/sitemap/">Site Map</a>';
                        if (isset($GLOBALS['footer_links']) && is_array($GLOBALS['footer_links'])) {
                            if (count($GLOBALS['footer_links']) > 0) {
                                ?>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-xs-12 footer-links-wrapper">
                                            <?php
                                            $hide_cities = '';



                                            foreach ($GLOBALS['footer_links'] as $k => $link_obj) {
                                                if (isMobileDevice()) {
                                                    if ($k > 11) {
                                                        $link_obj->show_hide = false;
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
                        } else {
                            echo $GLOBALS['footer_links']??'';
                        }
                        ?>

                        <img alt="Site Map" width="15px" height="15px" src="/templates/<?php echo $cur_template; ?>/images/site-map.svg" /> <?php echo $sitemap_a; ?>
                    </div>
                </div>
            </div>
            <div class="container bottom_3">
                <div class="row">
                    <div class="col-xs-12 col-lg-6">
                        <p>Bloomex delivers Flowers, Gift Baskets and Plants Coast-to-Coast. You can trust us to deliver the best quality product at the best price. We have locations across Australia to ensure that the freshest flowers and the tastiest treats are delivered on time. You can trust us with your most important moments.</p>
                    </div>
                    <?php
                    $query = "SELECT 
                                   * 
                                FROM `jos_menu`
                                WHERE 
                                    `jos_menu`.`published`='1' 
                                    AND 
                                    `jos_menu`.`show`='1' 
                                    AND 
                                    `jos_menu`.`parent` in ('305','284') 
                                    AND 
                                    `jos_menu`.`menutype`='Bloomex_top'
                                 ";

                    $database->setQuery($query);
                    $menus = $database->loadObjectList();
                    ?>
                    <div class="col-xs-12 col-lg-3">
                        <h5>Help/Account</h5>

                        <ul class="footer_menu">
                            <?php
                            foreach ($menus as $menu) {
                                if($menu->parent != 305)
                                    continue;
                                $link_obj = setMenuItem($menu);
                                ?>
                                <li><?php echo $link_obj->a; ?></li>
                            <?php }
                            ?>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-lg-3">
                        <h5>Policies</h5>
                        <ul class="footer_menu">
                            <?php
                            foreach ($menus as $menu) {
                                if($menu->parent != 284)
                                    continue;
                                $link_obj = setMenuItem($menu);
                                ?>
                                <li><?php echo $link_obj->a; ?></li>
                            <?php }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container bottom_4">
                    <div class=" copyright">
                        <p> &copy; 2005-<?php echo date('Y'); ?>  Bloomex - Order Flowers Quickly and Securely for Australia Delivery. Order from a Trusted Australian Online Florist</p>
                    </div>
            </div>

            <script src="/templates/bloomex_adaptive/js/bootstrap.min.js?ref=0257"></script>
            <script src="/templates/bloomex_adaptive/js/func.js?ref=0257"></script>
            <script src="/templates/bloomex_adaptive/js/bloomex.js?ref=0257"></script>
            <script src="/templates/bloomex_adaptive/js/klaviyo.js?ref=0257"></script>

            <?php

            mosLoadModules('footercon');
            $checkoutThankYouPage = (isset($_REQUEST['page']) && $_REQUEST['page'] == 'checkout.thankyou')?true:false;

            ?>
        </div><!--!page-->
        <div id="ajaxloader"></div>

        <?php

        if (!$checkoutThankYouPage && !isMobileDevice()) {

            $query = "SELECT * FROM `jos_vm_exit_popup` WHERE is_active='1' LIMIT 1";
            $database->setQuery($query);
            $exitPopup = false;
            $database->loadObject($exitPopup);

             if ($exitPopup) { ?>
                <div class="modal fade" id="BeforeYouLeaveDiv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" >
                    <div class="modal-dialog" role="document">
                        <div class="modal-content before-you-leave-content" style="
                                background-image: url('/images/exit_popup/<?= $exitPopup->image ?>');
                                background-repeat: no-repeat;
                                background-size: auto;
                                aspect-ratio: 16 / 10">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <a class="btn btn-primary" style="margin-left: <?= $exitPopup->position_y?>px;margin-top:<?= $exitPopup->position_x?>px;background-color:<?= $exitPopup->btn_color ?>;<?= $exitPopup->btn_style ?>" onclick="setExitPopupClick('<?= $exitPopup->product_id ?>'); return false"><?= $exitPopup->btn_title ?></a>
                        </div>
                    </div>
                </div>
        <?php }
        } ?>

        <script>
            function gtag_report_conversion(url) {
                return false;
            }
        </script>

    <?php
    foreach ($resourceManagers as $resource) {
        echo includeResource($resource,'footer_content');
    }
    ?>
    </body>
</html>
