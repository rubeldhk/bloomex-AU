<?php //defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/*
  echo '<script language="javascript" type="text/javascript">
  document.location="http://'.$_SERVER['SERVER_NAME'].'/index.php?url='.$_GET['city'].'&lg=1&lp=lp";
  </script>
  ';
 */
//echo '<script language="javascript" type="text/javascript">document.location="http://'.$_SERVER['SERVER_NAME'].'/index.php?url='.$_GET['city'].'&lg=1&lp=lp";</script>    ';
// So we share
require_once( 'configuration.php' );

// Lets connect
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Which database?
if (!mysql_select_db($mosConfig_db, $link)) {
    echo 'Could not select database';
    exit;
}

// Grab the request
$the_locale = mysql_real_escape_string($_GET['city']);

// Grab the language
$lang = mysql_real_escape_string($_GET['lang']);

// Build query
$sql = "SELECT * FROM tbl_landing_pages WHERE (url='" . $the_locale . "' OR url='" . str_replace("/", "", $the_locale) . "') AND lang=1";


$result = mysql_query($sql, $link);

if (mysql_num_rows($result) < 1) {
    mysql_free_result($result);
    mysql_close($link);
    header('location: ');
    exit();
}

while ($row = mysql_fetch_assoc($result)) {
    // Varibles
    $city = $row['city'];
    $prov = $row['province'];
    $tele = $row['telephone'];
    $activate_loc = (int) $row['enable_location'];
    $location_address = $row['location_address'];
    $location_country = $row['location_country'];
    $location_postcode = $row['location_postcode'];
    $location_telephone = $row['location_telephone'];
    $category_id = $row['category_id'];
}
mysql_free_result($result);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta name="google-site-verification" content="APtWhYDJvxgO0ehktEjH900A5gW4WqFDVXG8LVE16zA" />
        <title><?php echo $city; ?> Florist - <?php echo $city; ?> Flowers | <?php echo $city; ?> Flower Delivery | Send Flowers to <?php echo $city; ?> Australia</title>

        <meta name="description" content="50% off <?php echo $city; ?> Flowers from Bloomex Australia - Order Flowers online from your dedicated <?php echo $city; ?> Florist. Send Flowers to <?php echo $city; ?> - Same Day Flower Delivery" />

        <meta name="keywords" content="<?php echo $city; ?> florist, <?php echo $city; ?> flowers, <?php echo $city; ?> flower delivery, send flowers to <?php echo $city; ?>, <?php echo $city; ?> flower shop, <?php echo $city; ?> fresh flowers, <?php echo $city; ?> Mothers Day Flowers, <?php echo $city; ?> valentines flowers, <?php echo $city; ?> valentines day flowers, <?php echo $city; ?> sympathy flowers, <?php echo $city; ?> friendship flowers, <?php echo $city; ?> thank you flowers" />

        <script type="text/javascript" src="/modules/luckyphoto/LuckyPhoto.js"></script>
        <meta name="Generator" content="Joomla! - Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved." />
        <meta name="robots" content="index, follow" />
        <base href="/" />
        <link rel="shortcut icon" href="/images/bloomex.ico" />
        <!--
                <link rel="stylesheet" type="text/css" href="/templates/bloomex7/css/template_css.css" />
        -->
        <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/css/template_css.css" />
        <!--[if IE]>
                <link rel="stylesheet" type="text/css" href="/templates/bloomex7/css/ie8.css" />
        <![endif]-->
        <!--[if IE 6]>
                <link rel="stylesheet" type="text/css" href="/templates/bloomex7/css/ie6.css" />
        <![endif]-->
        <!--[if IE 8]>
                <link rel="stylesheet" type="text/css" href="/templates/bloomex7/css/ie8.css" />
        <![endif]-->
        <script type="text/javascript">
            sImgLoading	= "/administrator/components/com_virtuemart/html/jquery_ajax.gif";
            sScriptPath	= "/templates/bloomex7/js/";
        </script>
        <script type="text/javascript" src="/templates/bloomex7/js/jquery.js"></script>
        <script type="text/javascript" src="/templates/bloomex7/js/jquery.simplemodal.js"></script>
        <script type="text/javascript" src="/templates/bloomex7/js/func.js"></script>
        <link rel="shortcut icon" href="https://bloomex.ca/images/bloomex.ico"/>

        <!--================ MOUSEFLOW ========================-->
        <script type="text/javascript">document.write(unescape("%3Cscript src='" + (("https:" == document.location.protocol) ? "https" : "http") + "://b.mouseflow.com/projects/c805ef78-25f7-4da2-afd1-25f9c4c0ea7b.js' type='text/javascript'%3E%3C/script%3E"));</script>

    </head>
    <body>
        <!-- S1 -->
        <TABLE id=fullPage cellSpacing="0" cellPadding="0" width="790" align="center"  border=0><TBODY>
                <TR>
                    <TD class="borderLeft" width="16" height="6"></TD>
                    <TD colSpan=2></TD>
                    <TD class="borderRight" width="16"></TD>
                </TR>
                <TR>
                    <TD width="28" height="6" class="borderLeft"></TD>

                    <TD style="padding-left: 10px; vertical-align: middle; padding-top: 5px;" colspan="2">
                        <div class="delivery-intro">&nbsp;&nbsp;&nbsp;&nbsp;
                            DELIVERIES OUTSIDE OF AUSTRALIA&nbsp;&nbsp;
                            <a href="http://bloomexusa.com"><img src="/images/flag_usa.png" border="0" height="14" align="absbottom" style="margin-top: 3px;">&nbsp;USA&nbsp;</a>&nbsp;|&nbsp;
                            <a href="http://www.serenataflowers.com"><img src="/images/flag_great_britain.png" border="0" height="14" align="absbottom" style="margin-top: 3px;">&nbsp;UK&nbsp;</a>&nbsp;|&nbsp;
                            <a href="http://bloomex.ca"><img src="/images/flag_canada.png" border="0" height="14" align="absbottom" style="margin-top: 3px;">&nbsp;CAN</a>

                        </div><br>
                    </TD> 
                    <TD width="16" class="borderRight"></TD>
                </TR>
                <TR>
                    <TD class="borderLeft" width="16"></TD>
                    <TD style="VERTICAL-ALIGN: middle" width="443">Easy ordering for <b><?php echo $city; ?> Flowers</b> at:&nbsp;<b><?php echo $tele; ?></b></TD>

                    <TD align="right">
                        <TABLE cellSpacing="0" cellPadding="0" width="224" border="0">
                            <TBODY>
                                <TR>
                                    <TD style="VERTICAL-ALIGN: bottom;">&nbsp;

                                    </TD>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="/index.php?page=account.index&option=com_virtuemart&Itemid=1">

                                            <IMG alt="" src="/templates/bloomex7/images/myAccount.gif" border="0">


                                        </A>
                                    </TD>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="/index.php?page=shop.cart&option=com_virtuemart&Itemid=80">

                                            <IMG alt="" src="/templates/bloomex7/images/shoppingCart.gif" border="0">

                                        </A>
                                    </TD>
                                </TR>
                            </TBODY></TABLE>

                    </TD>
                    <TD class="borderRight" width="16"></TD>
                </TR>
                <TR>
                    <TD class="borderLeft" width="16"></TD>
                    <?php
                    if ($activate_loc) {
                        ?>                        
                        <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
                        <script>  
                            function initialize() {
                                var geocoder;
                                var map;
                                // Create an object containing LatLng, population.
                                geocoder = new google.maps.Geocoder();
                                var address = "<?php echo $location_address . ", " . $location_postcode . ", " . $location_country; ?>";
                                geocoder.geocode( { 'address': address}, function(results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        var mapOptions = {
                                            disableDefaultUI: true,
                                            zoom: 10,
                                            center: results[0].geometry.location,
                                            mapTypeId: google.maps.MapTypeId.ROADMAP
                                        }
                                        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                                        //map.setCenter(results[0].geometry.location);
                                        var marker = new google.maps.Marker({
                                            map: map,
                                            position: results[0].geometry.location,
                                            title: address
                                        });
                                        var populationOptions = {
                                            strokeColor: '#000000',
                                            strokeOpacity: 0.8,
                                            strokeWeight: 1,
                                            fillColor: '#ffff00',
                                            fillOpacity: 0.20,
                                            map: map,
                                            center: results[0].geometry.location,
                                            radius: 10000
                                        };
                                        cityCircle = new google.maps.Circle(populationOptions);   
                                    } else {
                                        alert("Adress: "+address+" \nGeocode was not successful for the following reason: " + status);
                                    }
                                });
                            }                           
                                                                      
                            jQuery(document).ready(function() {
                                initialize();
                            });
                        </script>
                    <?php } ?>
                    <TD colSpan=2 width="731" height="193" align="left" valign="top" scope="col" style="background-color:#ffffff;">
                        <div style="z-index:9999;position:absolute;height:192px;display:block;float:left;width:368px;background:url('<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/bloomex-logo.png') top left no-repeat">
                            <div style="display:block;float:left;width:100%;">
                                <img height="60" style="float:left; margin:0px 0px 0px 0px; border: none;" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/australian_flag.jpg" > 
                            </div>
                            <div style="display:block;text-align:left;font-size:12px;width:155px;padding:0px 0px 0px 10px;margin:70px 0px 0px 0px;float:left;line-height:150%;">
                                <b><?php echo $city; ?>, <?php echo $prov; ?></b><br>
                                    <b><font color="#EE1111">Call Now!</font></b> <span style="white-space:nowrap"><?php echo $tele ?></span>
                            </div>
                        </div>
                        <div id="map_canvas" style="height:191px;display:block;float:right;width:397px;"></div>
                    </TD>
                    <TD class="borderRight" width="16"></TD>

                </TR>
            </TBODY></TABLE>
        <!-----TOP MENU & SEARCH---->
        <table id="fullPage" cellspacing="0" cellpadding="0" width="790" align="center" border="0">
            <tbody>

                <tr>
                    <td class="borderLeft" width="16"></td>
                    <td height="42" class="left-top-menu">

                        <link rel="stylesheet" href="/modules/mod_123clickmenu/css/style.css" type="text/css" media="screen, projection">
                            <!--[if lte IE 7]>
                                    <link rel="stylesheet" type="text/css" href="/var/www/stage1.bloomex.com.au/modules/mod_123clickmenu/css/ie.css" media="screen" />
                                <![endif]-->
                            <script type="text/javascript" language="javascript" src="/modules/mod_123clickmenu/js/hoverIntent.js"></script>
                            <script type="text/javascript" language="javascript" src="/modules/mod_123clickmenu/js/jquery.dropdown.js"></script>
                            <ul class="dropdown">

                                <li>
                                    <a href="/index.php?page=shop.browse&amp;category_id=145&amp;option=com_virtuemart&amp;Itemid=255" class="">Specials</a>			
                                    <ul class="sub_menu">
                                        <!--<li>
                                            <a href="/index.php/Mothers-Day-Specials/View-all-products.html?category_parent_id=54" class="">Mothers Day Specials</a>								
                                        </li>-->
                                        <li>
                                            <a href="/index.php/Designer-Collections/View-all-products.html" class="">Designer's Collection - 1/2$</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Gourmet-Collection/View-all-products.html?category_parent_id=145" class="">Gourmet Collection 1/2$</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Combo-Savings/View-all-products.html" class="">Online Specials</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Half-Price-Roses/View-all-products.html" class="">Half Price Roses</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/1/2-Price-Bouquets/View-all-products.html" class="">1/2 Price Bouquets</a>								
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=7&amp;Itemid=246" class="">Occasions</a>			
                                    <ul class="sub_menu">
                                        <!--<li>
                                            <a href="/index.php/Mother-s-Day/View-all-products.html?category_parent_id=37" class="">Mothers Day Flowers</a>								
                                        </li>-->
                                        <li>
                                            <a href="/index.php/Sympathy-Funeral/View-all-products.html" class="">Sympathy and Funeral</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Anniversary/View-all-products.html" class="">Anniversary</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Birthday/View-all-products.html" class="">Birthday</a>								
                                            <ul>
                                                <li><a href="/index.php/Mom-s-Birthday/View-all-products.html" class="">Moms Birthday</a></li>
                                                <li><a href="/index.php/Wife-s-Birthday/View-all-products.html" class="">Wife's Birthday</a></li>
                                                <li><a href="/index.php/Friend-s-Birthday/View-all-products.html" class="">Friend's Birthday</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="/index.php/Congratulations/View-all-products.html" class="">Congratulations</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Get-Well/View-all-products.html" class="">Get Well</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Housewarming/View-all-products.html" class="">Housewarming</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Just-Because/View-all-products.html" class="">Just Because</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Love-Romance/View-all-products.html" class="">Love and Romance</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/New-Baby/View-all-products.html" class="">New Baby</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Thank-You/View-all-products.html" class="">Thank You</a>								
                                        </li>
                                        <li>
                                            <a href="http://www.bunchesdirect.com.au" class="">Wedding Flowers</a>								
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="/index.php/Flowers/View-all-products.html" class="">Flowers</a>			
                                    <ul class="sub_menu">
                                        <li>
				       <a href="/index.php/Australian-Native-Flowers/View-all-products.html?category_parent_id=18" class="">Australian Flowers</a>								
					</li>										
                                        <li>
                                            <a href="/index.php/New-Arrivals/View-all-products.html?category_parent_id=54" class="">New Arrivals</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/All-Mixed-Bouquets/View-all-products.html" class="">Mixed Bouquets</a>								
                                            <ul>
                                                <li><a href="/index.php/Lilies-and-Iris/View-all-products.html" class="">Lilies &amp; Iris Collection</a></li>
                                                <li><a href="/index.php/Roses-and-Lilies/View-all-products.html" class="">Roses &amp; Lilies Collection</a></li>
                                                <li><a href="/index.php/Daisies-and-Roses/View-all-products.html" class="">Daisies &amp; Roses Collection</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="/index.php/Roses/View-all-products.html" class="">Roses</a>								
                                            <ul>
                                                <li><a href="/index.php/Dozen-Roses/View-all-products.html" class="">Dozen Roses</a></li>
                                                <li><a href="/index.php/24-Roses-and-more/View-all-products.html" class="">24 Roses</a></li>
                                                <li><a href="/index.php/36-Roses/View-all-products.html" class="">36 Roses</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="/index.php/Lillies/View-all-products.html" class="">Lilies</a>								
                                            <ul>
                                                <li><a href="/index.php/Alstromeria-Peruvian-Lilies/View-all-products.html" class="">Alstromeria (Peruvian Lilies)</a></li>
                                                <li><a href="/index.php/Stargazer-Lilies/View-all-products.html" class="">Stargazer Lilies</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="/index.php/Daisies/View-all-products.html" class="">Daisies</a>								
                                            <ul>
                                                <li><a href="/index.php?page=shop.browse&amp;category_id=157&amp;option=com_virtuemart&amp;Itemid=255" class="">Gerberas</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="/index.php/Iris/View-all-products.html" class="">Iris</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Orchids/View-all-products.html" class="">Orchids &amp; Exotic Flowers</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/About-Us/Flower-Care.html" class="">Flower Guide</a>								
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="/index.php?page=shop.browse&amp;category_id=65&amp;option=com_virtuemart&amp;Itemid=255" class="">Price</a>			
                                    <ul class="sub_menu">
                                        <li>
                                            <a href="/index.php/Under-$50/View-all-products.html" class="">Under $50</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/$50-to-$60/View-all-products.html" class="">$50 to $60</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/$60-to-$70/View-all-products.html" class="">$60 - $70</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/$70-to-$80/View-all-products.html" class="">$70 - $80</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/$80-to-$90/View-all-products.html" class="">$80 - $90</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/$90-to-$100/View-all-products.html" class="">$90 - $100</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Over-$100/View-all-products.html" class="">Over $100</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Extra-Touches/Custom-Order/Detailed-product-flyer.html" class="">Any Budget Custom Made</a>								
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="/index.php/Gift-Baskets/View-all-products.html?category_parent_id=0" class="">Gift Baskets</a>			
                                    <ul class="sub_menu">
                                        <!--<li>
                                            <a href="/index.php/Mothers-Day-Gift-Baskets/View-all-products.html?category_parent_id=54" class="">Mothers Day Gift Baskets</a>								
                                        </li>-->
                                        <li>
                                            <a href="/index.php/Fruit-Baskets/View-all-products.html?category_parent_id=183" class="">Fruit Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Holiday-Baskets/View-all-products.html?category_parent_id=183" class="">Holiday Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Sweets-Baskets/View-all-products.html?category_parent_id=183" class="">Sweets Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Coffee-Tea-Baskets/View-all-products.html?category_parent_id=183" class="">Coffee &amp; Tea Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Gourmet-Baskets/View-all-products.html?category_parent_id=183" class="">Gourmet Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Tea-Baskets/View-all-products.html?category_parent_id=183" class="">Tea Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Coffee-Baskets/View-all-products.html?category_parent_id=183" class="">Coffee Baskets</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Snack-Baskets/View-all-products.html?category_parent_id=183" class="">Snack Baskets</a>								
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="#" class="" onclick="return false;">My Account</a>			
                                    <ul class="sub_menu">
                                        <li>
                                            <a href="/index.php/View-your-cart-content.html" class="">Shopping Cart</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/View-your-account-details.html" class="">Account Details</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/View-your-account-details.html" class="">My Orders</a>								
                                        </li>
                                        <li>
                                            <a href="/index.php/Log-in.html" class="">Account Login/ Logout</a>								
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="#" class="" onclick="return false;">Support</a>			
                                    <ul class="sub_menu">
                                        <li>
                                            <a href="#" onclick="javascript: window.open('https://bloomex.ca/liveperson/livehelp.php?page=user_qa.php&amp;department=1&amp;tab=1', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false" class="">Live Chat</a>

                                        </li>
                                        <li>
                                            <a href="/index.php/About-Us/Easy-Ordering-Video-Tutorial.html" class="">How to Order</a>								
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                    </td>
                    <td height="42" width="300" style="vertical-align:top;background:#D8BEE8 url('/templates/bloomex7/images/for_deliver.png') top right no-repeat;">
                        <a href="/" style="float:right;margin:0px 0px 0px 0px;width:280px;height:42px;">
                            <div style="color:#ffffff; float:right;margin:4px 0px 0px 0px;width:85px;text-transform:uppercase;"><?php echo $city; ?></div>
                        </a>
                    </td>
                    <td class="borderRight" width="16" height="29"></td>
                </tr>
            </tbody></table>


        <!-----END TOP MENU & SEARCH---->

        <!-----BODY ---->
        <TABLE cellSpacing="0" cellPadding="0" width="790" align="center" border="0" style="BACKGROUND-COLOR: rgb(239,229,246);">
            <TBODY>
                <TR>
                    <TD class="borderLeft" width="16"></TD>
                    <td style="BACKGROUND-IMAGE: url(/templates/bloomex7/images/leftBg.gif); BACKGROUND-POSITION: 100% 50%; 	background-repeat: no-repeat; BACKGROUND-COLOR: rgb(216,190,232);" valign="top" width="164">
                        <!----  LEGT      ---->
                        <table cellspacing="0" cellpadding="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td align="center"><br>
                                            <link rel="stylesheet" type="text/css" media="all" href="/components/com_instant_search/class/css/d4j_common_css.css" title="green">

                                                <script type="text/javascript">
                                                    var mosConfig_live_site = "";
                                                </script>

                                                <script type="text/javascript" src="/components/com_instant_search/class/js/d4j_common_include.compact.js"></script>

                                                <script type="text/javascript" src="/components/com_instant_search/class/js/d4j_display_engine.compact.js"></script>

                                                <script type="text/javascript" src="/components/com_instant_search/class/js/d4j_ajax_engine.compact.js"></script>

                                                <!-- Initiate AJAX engine for instant search form \-->
                                                <div id="instant_search_node" style="display:none"></div>
                                                <script type="text/javascript">
                                                    // Path to ajax backend script
                                                    var instant_search_backend_script = mosConfig_live_site+'/index2.php?option=com_instant_search&task=ajaxcall&no_html=1';
                                                    // connection setting
                                                    var persistent = false;
                                                    // option settings
                                                    var show_option = false;
                                                    var option_display = 'horizontal';
                                                    var option_pos = 'result_bottom';
                                                    var auto_refresh = true;
                                                    // search box settings
                                                    var text = 'search...';
                                                    var showhideresult = false;
                                                    // search settings
                                                    var delay_time = 300;
                                                    var min_chars = 3;
                                                    var max_chars = 20;
                                                    var phrase = 'any';
                                                    var ordering = 'popular';
                                                    var result_length = 100;
                                                    var limit = 20;
                                                    var enable_sef = 0;
                                                    var display = 'field';
                                                    var final_result = false;
                                                    var result_nav = 1;
                                                    var limitStart = 0;
                                                    // result box settings
                                                    var result_bgcolor = '#f2f2f2';
                                                    var result_width = 550;
                                                    var result_height = 300;
                                                    var padding_to = 'right';
                                                    var padding_width = 300;
                                                    // searching settings
                                                    var loading_status = 1;
                                                    var loading_text = 'Searching... Please wait..';
                                                    // language settings
                                                    var _PROMPT_KEYWORD = 'Search Keyword';
                                                    var _SEARCH_MATCHES = 'returned %d matches';
                                                    // current search #
                                                    var search_order = 0;
                                                    var display_order_id = 0;
                                                </script>
                                                <script type="text/javascript" src="/components/com_instant_search/instant_search.compact.js"></script>
                                                <!-- Initiate AJAX engine for instant search form /-->


                                                <form action="/" method="post" name="instantSearchForm">
                                                    <div class="search">
                                                        <input alt="search" class="inputbox" type="text" name="searchword" size="15" value="search..." 
                                                               onblur="if(this.value=='') this.value='search...'" 
                                                               onfocus="if(this.value=='search...') this.value=''" 
                                                               onkeyup="if (this.value.length < max_chars) { prepareSearch(this.value); } else { this.value = this.value.substring(0, max_chars - 1); }" 
                                                               data-cip-id="cIPJQ342845639">
                                                            <input type="submit" value="ok" class="button"><div id="instant_search_form" class="hiddenDiv" style="display: none;"></div>
                                                                </div>
                                                                <input type="hidden" name="option" value="search">
                                                                    </form>
                                                                    </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>

                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>		<table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                <tbody><tr>
                                                                                        <td>

                                                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                <tbody><tr align="left"><td><span class="mainlevel">- - - - - - - - - - - - - - - - - -</span></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">  SHOP BY OCCASION:</span></td></tr>
                                                                                                    <!--<tr align="left"><td><a href="/index.php/Mother-s-Day/View-all-products.html?category_parent_id=37" class="mainlevel">Mothers Day Flowers</a></td></tr>-->
                                                                                                    <tr align="left"><td><a href="/index.php/Birthday/View-all-products.html" class="mainlevel">Birthday Flowers</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Anniversary/View-all-products.html" class="mainlevel">Anniversary Flowers</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Get-Well/View-all-products.html" class="mainlevel">Get Well Flowers</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Sympathy-Funeral/View-all-products.html" class="mainlevel">Sympathy Flowers</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Love-Romance/View-all-products.html" class="mainlevel">Love and Romance</a></td></tr>
                                                                                                    <tr align="left"><td><a href="http://www.bunchesdirect.com.au" class="mainlevel">Wedding Flowers</a></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">- - - - - - - - - - - - - - - - - -</span></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">SHOP BY PRODUCT:</span></td></tr>
                                                                                                    <!--<tr align="left"><td><a href="/index.php/Mothers-Day-Gift-Baskets/View-all-products.html?category_parent_id=54" class="mainlevel">Mothers Day Gift Baskets</a></td></tr>-->
                                                                                                    <tr align="left"><td><a href="/index.php/Roses/View-all-products.html" class="mainlevel">Roses</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Lillies/View-all-products.html" class="mainlevel">Lilies</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/All-Mixed-Bouquets/View-all-products.html" class="mainlevel">Mixed</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Iris/View-all-products.html" class="mainlevel">Iris</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Daisies/View-all-products.html" class="mainlevel">Daisies</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Gift-Baskets/View-all-products.html?category_parent_id=0" class="mainlevel">Gift Baskets</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Fruit-Baskets/View-all-products.html?category_parent_id=183" class="mainlevel">Fruit Baskets</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/New-Arrivals/View-all-products.html?category_parent_id=54" class="mainlevel">New Arrivals!</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Best-Sellers/View-all-products.html" class="mainlevel">Best Sellers</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Extra-Touches/View-all-products.html" class="mainlevel">Vases-Cards-Chocolates</a></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">- - - - - - - - - - - - - - - - - -</span></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">SHOP BY PRICE:</span></td></tr>
                                                                                                </tbody></table>			</td>
                                                                                    </tr>
                                                                                </tbody></table>
                                                                        </td>
                                                                    </tr>
                                                                    <tr><td style="padding-bottom:7px;">		<table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                <tbody><tr>
                                                                                        <td>

                                                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                <tbody><tr align="left"><td><a href="/index.php/Under-$50/View-all-products.html" class="mainlevel">Under $50</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/$50-to-$60/View-all-products.html" class="mainlevel">$50 to $60</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/$60-to-$70/View-all-products.html" class="mainlevel">$60 to $70</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/$70-to-$80/View-all-products.html" class="mainlevel">$70 to $80</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/$80-to-$90/View-all-products.html" class="mainlevel">$80 to $90</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/$90-to-$100/View-all-products.html" class="mainlevel">$90 to $100</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/Over-$100/View-all-products.html" class="mainlevel">Over $100</a></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">- - - - - - - - - - - - - - - - - -</span></td></tr>
                                                                                                    <tr align="left"><td><span class="mainlevel">TOP DELIVERY REGIONS:</span></td></tr>
                                                                                                    <tr align="left"><td><a href="/sydney-florist/sydney-flowers/index.html" class="mainlevel">Sydney, NSW</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/melbourne-florist/melbourne-flowers/index.html" class="mainlevel">Melbourne, VIC</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/brisbane-florist/brisbane-flowers/index.html" class="mainlevel">Brisbane, QLD</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/canberra-florist/canberra-flowers/index.html" class="mainlevel">Canberra, ACT</a></td></tr>
                                                                                                </tbody></table>			</td>
                                                                                    </tr>
                                                                                </tbody></table>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><img height="70" alt="" src="/templates/bloomex7/images/geotrust_logo.jpg" width="164" border="0">
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="padding-top:6px;">		<table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                <tbody><tr>
                                                                                        <td>

                                                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                <tbody><tr align="left"><td><a href="/index.php/About-Us/delivery-Policy.html" class="mainlevel">Delivery Policy</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/About-Us/Next-Day-Delivery-Guaranteed.html" class="mainlevel">Next Day Delivery</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/About-Us/Return-Policy.html" class="mainlevel">Return Policy</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/View-your-account-details.html" class="mainlevel">Track Orders</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/component/option,com_contxtd/Itemid,3/task,blog/" class="mainlevel">Contact Us</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/About-Us/Customer-Comments.html" class="mainlevel">About US</a></td></tr>
                                                                                                    <tr align="left"><td><a href="/index.php/About-Us/Flower-Care.html" class="mainlevel">Flower Care</a></td></tr>
                                                                                                </tbody></table>			</td>
                                                                                    </tr>
                                                                                </tbody></table>
                                                                            <table cellpadding="0" cellspacing="0" class="moduletable-nletter">
                                                                                <tbody><tr>
                                                                                        <th valign="top">
                                                                                            E-Mail Exclusives				</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <div class="caption">
                                                                                                Sign up to receive special offers and promotions from Bloomex:
                                                                                            </div>
                                                                                            <div class="nletter">
                                                                                                <form name="Fnletter" method="POST" action="">
                                                                                                    <input name="email_address" type="text" size="20" value="Enter Email" class="tbox" data-cip-id="cIPJQ342845640">
                                                                                                        <input type="button" id="btn-nletter" name="btn-nletter" class="btn-nletter" value="&nbsp;">
                                                                                                            </form>
                                                                                                            <span class="loading" id="msg-nletter">&nbsp;</span>	
                                                                                                            </div>

                                                                                                            <script type="text/javascript">
                                                                                                                $j(document).ready(function(){
                                                                                                                    $j("#btn-nletter").click(function () {
                                                                                                                        $j("#msg-nletter").attr("style", "display:none"); 
                                                                                                                        var email_address	= $j("input[name='email_address']").val();
		
                                                                                                                        if( !jQuery.trim(email_address) )  {
                                                                                                                            alert("Please enter your email address!");	
                                                                                                                            return;
                                                                                                                        }		
		
                                                                                                                        if( !(/^\w+([\.-]*\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email_address)) ) {
                                                                                                                            alert("Your email address is incorrect! Eg: example@email.com");	
                                                                                                                            return;
                                                                                                                        }	
				
                                                                                                                        $j.post( "index.php",
                                                                                                                        { 	option: 		"com_nletter", 
                                                                                                                            task:		"send",
                                                                                                                            email:		email_address,
                                                                                                                            ajaxSend: function(){
                                                                                                                                $j("#msg-nletter").html("Sending..."); 
                                                                                                                                $j("#msg-nletter").attr("style", "display:block; color:#0000ff"); 
                                                                                                                            }
                                                                                                                        },			
                                                                                                                        function(data){
                                                                                                                            if( data == "exist" ) {	
                                                                                                                                $j("#msg-nletter").html("Sorry, your email already exists in our mailing list"); 
                                                                                                                                $j("#msg-nletter").attr("style", "display:block; color:#ff0000"); 						
                                                                                                                            }else {
                                                                                                                                $j("#msg-nletter").html("Your email was successfully added to our mailing list"); 
                                                                                                                                $j("#msg-nletter").attr("style", "display:block; color:#0000ff"); 				
                                                                                                                                $j("input[name='email_address']").val("Enter Email");
                                                                                                                            }
                                                                                                                        }
                                                                                                                    );
                                                                                                                    });	
	
	
                                                                                                                    $j("input[name='email_address']").click(function () {
                                                                                                                        if( $j("input[name='email_address']").val() == "Enter Email" ) 	$j("input[name='email_address']").val("");
                                                                                                                    });	
                                                                                                                });
                                                                                                            </script>	
                                                                                                            </td>
                                                                                                            </tr>
                                                                                                            </tbody></table>
                                                                                                            <br></td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <hr>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td>		<table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                            <tbody><tr>
                                                                                                                                    <th valign="top">
                                                                                                                                        Live Support via Skype				</th>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td>

                                                                                                                                        <br>
                                                                                                                                            <a href="skype:Bloomex1?call" onclick="return skypeCheck();">
                                                                                                                                                <img border="0" src="https://bloomex.ca/modules/jomskype/call_blue.png"></a><br>
                                                                                                                                                <a href="skype:Bloomex1?chat" onclick="return skypeCheck();">
                                                                                                                                                    <img border="0" src="https://bloomex.ca/modules/jomskype/chat_blue.png"></a>			</td>
                                                                                                                                                </tr>
                                                                                                                                                </tbody></table>
                                                                                                                                                <br></td>
                                                                                                                                                    </tr>
                                                                                                                                                    </tbody></table>
                                                                                                                                                    <p>  

                                                                                                                                                    </p><center><a href="mailto:wecare@bloomex.com.au">wecare@bloomex.com.au</a></center>
                                                                                                                                                    <!----  END LEGT      ---->

                                                                                                                                                    </TD>
                                                                                                                                                    <TD valign=top width="568" align="left" style="background-color: #FFFFFF;">
                                                                                                                                                        <!---- BODY ---->
                                                                                                                                                        <!--<table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                                                                                                                                <tbody>
                                                                                                                                                                        <tr>
                                                                                                                                                                                <td>&nbsp;
                                                                                                                                                                                        <a href="/index.php/Valentine-s-Day/View-all-products.html">
                                                                                                                                                                                                <div style="text-align: center">
                                                                                                                                                                                                    <img border="0" alt=" " src="/templates/bloomex7/images/VD-Main-Banner.png">
                                                                                                                                                                                                </div>
                                                                                                                                                                                        </a> 
                                                                                                                                                                                </td>
                                                                                                                                                                        </tr>
                                                                                                                                                                </tbody>
                                                                                                                                                        </table>-->

                                                                                                                                                        <!--FLOWER and GIFT BASKET BANNER-->
                                                                                                                                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                                                                                            <tbody>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td> 
                                                                                                                                                                         <tr>
                                                                                                                                                                               <td>        		
                                                                                                                                                                                 <?php
                                                                                                                                                                                                  require_once 'templateBanners.php';
                                                                                                                                                                                                  $title_category = new templateBanners( $link );
                                                                                                                                                                                                   echo $title_category->get_upper();

                                                                                                                                                                                       // if (mosCountModules( "christmas" )) { 
                                                                                                                                                                                    //       mosLoadModules ( "christmas", -1 );          
                                                                                                                                                                                   // } 	
                                                                                                                                                                                ?>
                                                                                                                                                                                 </td>
                                                                                                                                                                                </tr>
                                                                                                  
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>
                                                                                                                                                            </tbody>
                                                                                                                                                        </table>

                                                                                                                                                        <TABLE cellSpacing="0" cellPadding="0" border=0 align="center">
                                                                                                                                                            <TBODY>
                                                                                                                                                            <!--<tr>
                                                                                                                                                                    <td>        		
                                                                                                                                                                            <img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/starting-at-29.jpg" style="margin:13px 0px 0px 14px;" />
                                                                                                                                                                    </td>
                                                                                                                                                            </tr>-->

                                                                                                                                                                <TR>
                                                                                                                                                                    <TD align="center">
                                                                                                                                                                        <!-- BEGIN HelpOnClick CODE -->
                                                                                                                                                                        <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>
                                                                                                                                                                            <TR><TD height=57 width=155>
                                                                                                                                                                                    <a href='javascript:void(0)' onclick='window.open("http://app.helponclick.com/help?lang=en&a=4d9f177b48504f4bb25f0580b86aab83","hoc_chat_login","width=720,height=550,scrollbars=no,status=0,toolbar=no,location=no,resizable=no")'>
                                                                                                                                                                                        <img style="float:left;" border="0" alt="Click for Live Chat" src="/templates/bloomex7/images/livechat.gif"/>
                                                                                                                                                                                    </a>
                                                                                                                                                                                </TD></TR></TABLE>

                                                                                                                                                                        <!-- END HelpOnClick CODE --> 	
                                                                                                                                                                    </TD>
                                                                                                                                                                </TR>
                                                                                                                                                                <TR>
                                                                                                                                                                    <TD >



                                                                                                                                                                        <br>
                                                                                                                                                                            <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                <tr>
                                                                                                                                                                                    <td>


                                                                                                                                                                                        <table border="0" cellpadding="3" cellspacing="5" width="100%">
                                                                                                                                                                                            <?php
                                                                                                                                                                                            $nLimit = 9;
                                                                                                                                                                                            if ($category_id) {
                                                                                                                                                                                                $q = "SELECT DISTINCT  P.product_id 
                                                                                                                                                                                                      FROM jos_vm_product AS P 
                                                                                                                                                                                                        INNER JOIN jos_vm_product_category_xref AS PCX ON P.product_id = PCX.product_id 
                                                                                                                                                                                                        INNER JOIN jos_vm_category AS C ON C.category_id = PCX.category_id 
                                                                                                                                                                                                        INNER JOIN jos_vm_product_price AS VMP ON P.product_id = VMP.product_id 
                                                                                                                                                                                                        WHERE C.category_id='$category_id' AND P.product_publish='Y' 
                                                                                                                                                                                                        ORDER BY  (VMP.product_price - VMP.saving_price) ASC";
                                                                                                                                                                                                $result = mysql_query($q, $link);
                                                                                                                                                                                                $count=mysql_num_rows($result);       
                                                                                                                                                                                                 $rand_prod=array();
                                                                                                                                                                                                 $j=0;
                                                                                                                                                                                                 while($row = mysql_fetch_assoc($result)) {
                                                                                                                                                                                                   
                                                                                                                                                                                                    $rand_prod[$j]=$row["product_id"];
                                                                                                                                                                                                    $j++;
                                                                                                                                                                                                  
                                                                                                                                                                                                }
                                                                                                                                                                                                
                                                                                                                                                                                                $rand_keys = array_rand($rand_prod, $nLimit);
                                                                                                                                                                                                
                                                                                                                                                                                                 $list_id=$rand_prod[$rand_keys[0]].','.$rand_prod[$rand_keys[1]].','.$rand_prod[$rand_keys[2]].','.$rand_prod[$rand_keys[3]].','.$rand_prod[$rand_keys[4]].','.$rand_prod[$rand_keys[5]].','.$rand_prod[$rand_keys[6]].','.$rand_prod[$rand_keys[7]].','.$rand_prod[$rand_keys[8]];
                                                                                                                                                                                                 
                                                                                                                                                                                                $q = "SELECT DISTINCT P.product_sku,  P.product_name, P.product_id, P.product_thumb_image, VMP.product_price,  VMP.saving_price 
                                                                                                                                                                                                      FROM jos_vm_product AS P 
                                                                                                                                                                                                        INNER JOIN jos_vm_product_category_xref AS PCX ON P.product_id = PCX.product_id 
                                                                                                                                                                                                        INNER JOIN jos_vm_category AS C ON C.category_id = PCX.category_id 
                                                                                                                                                                                                        INNER JOIN jos_vm_product_price AS VMP ON P.product_id = VMP.product_id 
                                                                                                                                                                                                        WHERE C.category_id='$category_id' AND P.product_publish='Y' 
                                                                                                                                                                                                        AND   P.product_id in ($list_id)  
                                                                                                                                                                                                        ORDER BY  (VMP.product_price - VMP.saving_price) ASC";// LIMIT $nLimit";
                                                                                                                                                                                                $result = mysql_query($q, $link);
                                                                                                                                                                                                $count=mysql_num_rows($result);                    
                                                                                                                                                                                                $i = 1;
                                                                                                                                                                                               
                                                                                                                                                                              
                                                                                                                                                                                                global $mosConfig_absolute_path;
                                                                                                                                                                                                require_once $mosConfig_absolute_path.'/Rounding.php';
                                                                                                                                                                                               
                                                                                                                                                                                                while($row = mysql_fetch_assoc($result)) {
                                                                                                                                                                                                    
                                                                                                                                                                            
                                                                                                                                                                                                  
                                                                                                                                                                                                    $sTitlte = $row['product_name'];
                                                                                                                                                                                                    $sSKU = $row['product_sku'];

                                                                                                                                                                                                    if (!empty($row["saving_price"]) && $row["saving_price"] > 0 && $row["product_price"] >= 0) {
                                                                                                                                                                                                        $product_price = $row["product_price"] - $row["saving_price"];
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $product_price = $row["product_price"];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $percent = ( $row['product_sku'] == 'VC-01' ) ? 1 : 1;
                                                                                                                                                                                                    $nPrice = "$" . number_format(Rounding::instance()->rounding($product_price * $percent, 2), 2, '.', '');
                                                                                                                                                                                                    $nProductID = $row['product_id'];
                                                                                                                                                                                                    $sImage = $row['product_thumb_image'];
                                                                                                                                                                                                    $sLink = "$mosConfig_live_site/index.php?option=com_virtuemart&Itemid=257&category_id=$category_id&flypage=shop.flypage&lang=en&manufacturer_id=0&page=shop.product_details&product_id=$nProductID";

                                                                                                                                                                                                    if ($i == 1) {
                                                                                                                                                                                                        echo "<tr>";
                                                                                                                                                                                                    }
                                                                                                                                                                                                    ?>			<td align="center">
                                                                                                                                                                                                        <span style="font-weight:bold;font-size: 11px; color:#666666;"><?php echo $sTitlte; ?></span><br>
                                                                                                                                                                                                            <br /><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;"><?php echo $sSKU; ?></span>
                                                                                                                                                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;font-size: 11px; color:#CC0000;"><span style="font-weight:bold">
                                                                                                                                                                                                                    <?php echo $nPrice; ?></span>

                                                                                                                                                                                                            </span>
                                                                                                                                                                                                            <a title="<?php echo $sTitlte; ?>" href="<?php echo $sLink; ?>"><img  class="product-random" height="200" width="170" border="0" alt="<?php echo $sTitlte; ?>" src="<?php echo $mosConfig_live_site; ?>/components/com_virtuemart/shop_image/product/<?php echo $sImage; ?>" /></a><br />
                                                                                                                                                                                                            <div class='form-add-cart'>
                                                                                                                                                                                                                <a href="/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $nProductID; ?>&option=com_virtuemart&Itemid=1&link=main" class='mod-add-to-cart'  title="Add to Cart: <?php echo $sTitlte; ?>" href="#"><IMG height=17 src="/components/com_virtuemart/shop_image/ps_image/button.gif" width=80 border=0></a>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                    </td>
                                                                                                                                                                                                    <?php
                                                                                                                                                                                                    if ($i % 3 == 0 && $i > 1 && $i < $nLimit) {
                                                                                                                                                                                                        echo "</tr><tr>";
                                                                                                                                                                                                    }


                                                                                                                                                                                                    if ($i == $nLimit) {
                                                                                                                                                                                                        echo "</tr>";
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $i++;
                                                                                                                                                                                                 
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>
                                                                                                                                                                                        </table>
                                                                                                                                                                                    </td>

                                                                                                                                                                                </tr>
                                                                                                                                                                            </table>
                                                                                                                                                                            <br>
                                                                                                                                                                                <div align="center"></div>
                                                                                                                                                                                <TABLE style="MARGIN-LEFT: 0px" cellSpacing=0 cellPadding=0 width="100%" border=0>
                                                                                                                                                                                    <TBODY>
                                                                                                                                                                                        <TR>
                                                                                                                                                                                            <TD align=middle><IMG height=23 alt="" src="/templates/bloomex7/images/ocassion.gif" width=186 border=0></TD>

                                                                                                                                                                                            <TD align=middle><IMG height=23 alt="" src="/templates/bloomex7/images/flowergift.gif" width=186 border=0></TD>



                                                                                                                                                                                            <TD align=middle width=186><IMG height=23 alt="" src="/templates/bloomex7/images/featured.gif" width=186 border=0></TD>

                                                                                                                                                                                        </TR>
                                                                                                                                                                                        <TR valign="top">
                                                                                                                                                                                            <TD >
                                                                                                                                                                                                <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td>

                                                                                                                                                                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Anniversary/View-all-products.html" class="mainlevel" >Anniversary</a></td></tr>

                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Birthday/View-all-products.html" class="mainlevel" >Birthday</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Congratulations/View-all-products.html" class="mainlevel" >Congratulations</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Thank-You/View-all-products.html" class="mainlevel" >Thank You</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Get-Well/View-all-products.html" class="mainlevel" >Get Well</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Housewarming/View-all-products.html" class="mainlevel" >Housewarming</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Just-Because/View-all-products.html" class="mainlevel" >Just Because</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Love-Romance/View-all-products.html" class="mainlevel" >Love and Romance</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/New-Baby/View-all-products.html" class="mainlevel" >New Baby</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Sympathy-Funeral/View-all-products.html" class="mainlevel" >Sympathy and Funeral</a></td></tr>

                                                                                                                                                                                                        </table>			</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                </table>
                                                                                                                                                                                            </TD>
                                                                                                                                                                                            <TD >
                                                                                                                                                                                                <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td>


                                                                                                                                                                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Combo-Savings/View-all-products.html?category_parent_id=145" class="mainlevel" >Online specials</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Gift-Baskets/View-all-products.html?category_parent_id=0" class="mainlevel" >Gift Baskets</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Fruit-Baskets/View-all-products.html?category_parent_id=183" class="mainlevel" >Fruit Baskets</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/All-Mixed-Bouquets/View-all-products.html" class="mainlevel" >Mixed Bouquets</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/All-Arrangements/View-all-products.html" class="mainlevel" >Arrangements</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Roses/View-all-products.html" class="mainlevel" >Roses</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Lillies/View-all-products.html" class="mainlevel" >Lilies</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Daisies/View-all-products.html" class="mainlevel" >Daisies</a></td></tr>

                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Lillies/View-all-products.html" class="mainlevel" >Iris</a></td></tr>
                                                                                                                                                                                                        <tr align="left"><td><a href="/index.php/Orchids/View-all-products.html" class="mainlevel" >Orchids</a></td></tr>
                                                                                                                                                                                                        <!--<tr align="left"><td><a href="/index.php/Sunflowers/View-all-products.html" class="mainlevel" >Sunflowers</a></td></tr>-->
                                                                                                                                                                                                        </table>			</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                </table>
                                                                                                                                                                                            </TD>
                                                                                                                                                                                            <td style="vertical-align:top;background-color:white;font-family:Verdana, arial, sans serif;font-size: 0.75em;color: rgb(81,81,81);width: 186px;padding: 0px;padding-top: 10px;" width="186">
                                                                                                                                                                                                <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                                    <tbody><tr>
                                                                                                                                                                                                        <td width="100%">
                                                                                                                                                                                                        <span style="font-weight:bold;font-size: 11px; color:#666666;">Designer's Collection I</span><br>
                                                                                                                                                                                                        <br><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;">DC01</span>
                                                                                                                                                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;font-size: 11px; color:#CC0000;">$45.00</span>
                                                                                                                                                                                                        <a title="Designer's Collection I" href="/index.php/1/2-Price-Bouquets/Designer-s-Collection-I/Detailed-product-flyer.html"><img class="product-random" height="200" width="170" border="0" alt="Designer's Collection I" src="/components/com_virtuemart/shop_image/product/ff85cc6e2e512121ad1d0502e82a45b8.jpg"></a><br>
                                                                                                                                                                                                        <br>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        </tbody></table>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        </TR></TBODY>
                                                                                                                                                                                                        </TABLE>
                                                                                                                                                                                                        </TD></TR></TBODY></TABLE>
                                                                                                                                                                                                        <!----END BODY---->
                                                                                                                                                                                                        </TD>
                                                                                                                                                                                                        <TD class="borderRight2" width=16></TD>
                                                                                                                                                                                                        </TR>
                                                                                                                                                                                                        <TR>
                                                                                                                                                                                                        <TD class="borderLeft"></TD>

                                                                                                                                                                                                        <TD colspan=2 style="BACKGROUND-COLOR: rgb(216,190,232)" align=right>
                                                                                                                                                                                                        <TABLE cellSpacing=0 cellPadding=0 border=0 width=568>
                                                                                                                                                                                                        <TR>
                                                                                                                                                                                                        <TD style="PADDING-LEFT: 16px" height=24> 
                                                                                                                                                                                                        <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                        <td>
                                                                                                                                                                                                        <ul id="mainlevel-nav"><li><a href="/" class="mainlevel-nav" id="active_menu-nav">Home</a></li><li><a href="/index.php/component/option,com_contxtd/Itemid,3/task,blog/" class="mainlevel-nav" >Contact US</a></li><li><a href="/index.php/Search.html" class="mainlevel-nav" >Search</a></li><li><a href="/index.php/Log-in.html" class="mainlevel-nav" >LOGIN</a></li></ul>			</td>

                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        </table>
                                                                                                                                                                                                        </TD>
                                                                                                                                                                                                        </TR>
                                                                                                                                                                                                        </TABLE>
                                                                                                                                                                                                        </TD>
                                                                                                                                                                                                        <TD class="borderRight2" width=16></TD>
                                                                                                                                                                                                        </TR>
                                                                                                                                                                                                        </TABLE>

                                                                                                                                                                                                        <!----END BODY ---->
                                                                                                                                                                                                        <TABLE cellSpacing=0 cellPadding=0 width=790 align=center border=0>
                                                                                                                                                                                                        <TBODY>
                                                                                                                                                                                                        <TR>
                                                                                                                                                                                                        <!-- <TD class="borderLeftBottom" width=29 height=11></TD>
                                                                                                                                                                                                        <TD class="borderBottom" width=734></TD>
                                                                                                                                                                                                        <TD class="borderRightBottom" width=27 height=11></TD>
                                                                                                                                                                                                        -->
                                                                                                                                                                                                        <TD class="borderLeftBottom" width="17" height="17" style="min-width:17px;"><img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/templates/bloomex7/images/borderLeftbottom.gif" width="17" height="17" /></TD>
                                                                                                                                                                                                        <TD class="borderBottom" width="755" style="min-width:755px;"></TD>
                                                                                                                                                                                                        <TD class="borderRightBottom" width="18" height="17" style="min-width:18px;"><img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/templates/bloomex7/images/borderRightBottom.gif" width="18" height="17" /></TD>
                                                                                                                                                                                                        </TR>
                                                                                                                                                                                                        <TR>
                                                                                                                                                                                                        <TD colspan=3 align="center">

                                                                                                                                                                                                        <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                        <td>
                                                                                                                                                                                                        <table border="0" cellspacing="5" cellpadding="5" class="htmtableborders" align="center" style="width: 50%"><tbody><tr><td><p align="center"><a href="/index.php?option=com_content&amp;task=view&amp;id=17&amp;Itemid=47">&nbsp;Term of Use</a></p></td><td><p align="center"><a href="/index.php?option=com_content&amp;task=view&amp;id=18&amp;Itemid=48">Privacy Policy</a></p></td><td><p align="center"><a href="/index.php?option=com_content&amp;task=view&amp;id=84&amp;Itemid=48">Site Map</a></p></td></tr></tbody></table>			</td>
                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        </table>
                                                                                                                                                                                                        <table cellspacing="0" cellpadding="0" border="0" align="center" style="width: 90%">

                                                                                                                                                                                                        <tbody>
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                        <td style="text-align:left;">
                                                                                                                                                                                                        <div id="fb-root"></div>
                                                                                                                                                                                                        <script>(function(d, s, id) {
                                                                                                                                                                                                        var js, fjs = d.getElementsByTagName(s)[0];
                                                                                                                                                                                                        if (d.getElementById(id)) {return;}
                                                                                                                                                                                                        js = d.createElement(s); js.id = id;
                                                                                                                                                                                                        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                                                                                                                                                                                                        fjs.parentNode.insertBefore(js, fjs);
                                                                                                                                                                                                        }(document, 'script', 'facebook-jssdk'));</script>

                                                                                                                                                                                                        <div class="fb-like"  data-send="false" data-width="450" data-show-faces="false"></div>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                        </tr>

                                                                                                                                                                                                        </tbody>
                                                                                                                                                                                                        </table>
                                                                                                                                                                                                        <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                        <td>
                                                                                                                                                                                                        <table border="0" cellspacing="5" cellpadding="5" width="775" height="406" class="htmtableborders" align="center"><tbody><tr><td style="color: #49536e"><div align="justify">   </div><p class="MsoNormal" align="justify"><span style="color: #595959">&copy; 2011&nbsp;Bloomex Inc - At Bloomex we are devoted to making it simple for you to send flowers online for any occasion. We provide a wide range of flower bouquets and arrangement that you can explore right here on our website. You can find flowers for <a href="/index.php/Birthday/View-all-products.html">Birthdays</a> , <a href="/index.php/Sympathy-Funeral/View-all-products.html">Sympathy</a> , <a href="/index.php/Love-Romance/View-all-products.html">Love &amp; Romance</a> , <a href="/index.php/Thank-You/View-all-products.html">Thank You</a> , <a href="/index.php/New-Baby/View-all-products.html">New Baby</a> , and more.</span></p><div align="justify">  </div><p class="MsoNormal" align="justify"><span style="color: #595959">We are dedicated to delivering fresh flowers with the highest quality. In order to deliver this promise to our customers, we have established a strong bargaining power with the best flower growers in the world. This has enabled us to purchase and deliver quality flowers at a price that is 35 &ndash; 40% cheaper than other online and traditional florists, and we pass our savings on to our customers. We also have established relationships with local florists across Australia to make sure that we deliver the freshest flowers possible and delivered on time. Bloomex deploys the smartest technology to make flower delivery is efficient and fast. </span></p><div align="justify">  </div><p class="MsoNormal" align="justify"><span style="color: #595959">Our customer service team is ready to provide outstanding service via phone (TOLL FREE 1800-451-637) or live chat. You can place your flower order online. We will help you find and deliver the perfect flower <a href="/index.php/All-Arrangements/View-all-products.html">arrangements</a> , <a href="/index.php/All-Mixed-Bouquets/View-all-products.html">bouquets</a> , <a href="/index.php/Roses/View-all-products.html">roses</a> , <a href="/index.php/Lillies/View-all-products.html">lilies</a> , <a href="/index.php/Daisies/View-all-products.html">daisies </a> for as <a href="/index.php/Root-category-65/Category-69/View-all-products.html">low as $30</a>  or <a href="/index.php/Root-category-65/Category-70/View-all-products.html">higher than $50</a> .</span></p><div align="justify">  </div><p class="MsoNormal" align="justify"><span style="color: #595959">In order to make sure that the flowers are delivered on time we have a Next Day Delivery policy. All you have to do is order by 2:30pm of the receiver&rsquo;s time. We send flowers to almost any location in New South Wales, Australia Capital Territory, Queensland and Victoria. Major delivery areas include: <a href="/index.php/florist/sydney/">Sydney Florist</a> , <a href="/index.php/florist/melbourne_florist/">Melbourne Florist</a>  , <a href="/index.php/florist/brisbane/">Brisbane Florist</a> , <a href="/index.php/florist/canberra/">Canberra Florist</a> , </span></p><div align="justify">  </div><div align="center"><span style="color: #595959"><a href="http://www.bloomex.ca">Bloomex.ca</a>  &bull; <a href="http://www.bloomexusa.com">Bloomexusa.com</a>  &bull; <a href="http://www.bloomex.com.au">Bloomex.com.au</a></span></div><p style="text-align: center" class="MsoNormal" align="center"><span style="color: #595959"> </span></p>  </td></tr><tr><td><table border="0" style="width: 100%"><tbody><tr><td><p style="margin: 0cm 0cm 0pt; text-align: center" class="MsoNormal" align="center"><strong><span style="font-size: 7.5pt; color: #cc0000; font-family: Verdana">Only one promotional offer per order. Promotional offers can not be combined.</span></strong><span style="font-size: 7.5pt; color: #cc0000; font-family: Verdana">&nbsp;</span></p>  </td></tr></tbody></table></td></tr><tr><td><div align="center"><strong>Major Delivery Areas:</strong><br />  <a href="/index.php/florist/sydney/">Sydney Flowers</a> |  <a href="/index.php/florist/newcastle/">Newcastle Flowers</a> |  <a href="/index.php/florist/melbourne_florist/">Melbourne Flowers</a> |  <a href="/index.php/florist/geelong/">Geelong Flowers</a> |  <a href="/index.php/florist/brisbane/">Brisbane Flowers</a> |  <a href="/index.php/florist/gold_coast/">Gold Coast Flowers</a>&nbsp;  <a href="/index.php/florist/toowoomba/">Toowoomba Flowers</a> |  <a href="/index.php/florist/canberra/">Canberra Flowers</a> </div></td></tr></tbody></table>			</td>

                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        </table>


                                                                                                                                                                                                        <!--FOOTER!-->


                                                                                                                                                                                                        <div align="center">
                                                                                                                                                                                                        &copy; 2012 bloomex.com.au</div>

                                                                                                                                                                                                        <div align="center">

                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div align="center"></div>

                                                                                                                                                                                                        </TD></TR>
                                                                                                                                                                                                        </TBODY></TABLE>



                                                                                                                                                                                                        <!-- #1206 (29/06/2011)-->
                                                                                                                                                                                                        <script type="text/javascript">

                                                                                                                                                                                                        var _gaq = _gaq || [];
                                                                                                                                                                                                        _gaq.push(['_setAccount', 'UA-24439979-1']);
                                                                                                                                                                                                        _gaq.push(['_trackPageview']);

                                                                                                                                                                                                        (function() {
                                                                                                                                                                                                        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                                                                                                                                                                                                        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                                                                                                                                                                                                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                                                                                                                                                                                                        })();

                                                                                                                                                                                                        </script>

                                                                                                                                                                                                                      <?php       echo $title_category->get_corner();               ?>
                                                                                                                                                                                                      <!--  <a href="/index.php/Half-Price-Roses/View-all-products.html">
                                                                                                                                                                                                        <img border="0" src="/templates/bloomex7/images/Roses.png" style="bottom: 0pt;position: fixed;left: 0pt;z-index:10"> 
                                                                                                                                                                                                        </a>
                                                                                                                                                                                                        <a href="/index.php/Designer-Collections/View-all-products.html">
                                                                                                                                                                                                        <img border="0" src="/templates/bloomex7/images/Designers-Collection_NEW.png" style="bottom: 0pt;position: fixed;right: 0pt;z-index:10">
                                                                                                                                                                                                        </a>-->

                                                                                                                                                                                                        </BODY>
                                                                                                                                                                                                        </HTML>
                                                                                                                                                                                                                                <?php
                                                                                                                                                                                                                                mysql_close($link);
                                                                                                                                                                                                                                ?>
                                                                                                                                                                                                        <!-- 1328728722 -->
