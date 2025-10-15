<?php
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
$the_locale = mysql_real_escape_string($_GET['request']);

// Grab the language
$lang = mysql_real_escape_string($_GET['lang']);

// Build query
$sql = "SELECT * FROM tbl_giftbasket_pages WHERE (url='" . $the_locale . "' OR url='" . str_replace("/", "", $the_locale) . "');";


$result = mysql_query($sql, $link);

if (mysql_num_rows($result) < 1) {

    mysql_free_result($result);
    mysql_close($link);
    header('location: https://bloomex.ca');
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
}

mysql_free_result($result);

mysql_close($link);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title><?php echo $city; ?> Gift Baskets - <?php echo $city; ?> Fruit Baskets | <?php echo $city; ?> Gourmet Gift Baskets | <?php echo $city; ?> Holiday Gift Baskets | <?php echo $city; ?> Corporate Gift Baskets - Bloomex <?php echo $city; ?></title>
        <meta name="description" content="Buy <?php echo $city; ?> Gift Baskets - Save 70%! Bloomex delivers Gourmet Gift Baskets, Fruit Baskets and Corporate Gift Baskets. Order online or over the phone for <?php echo $city; ?> gift, fruit and Holiday Gift Basket delivery." />
        <meta name="keywords" content="<?php echo $city; ?> gift baskets, <?php echo $city; ?> fruit baskets, <?php echo $city; ?> chocolate basket, <?php echo $city; ?> corporate gift baskets, <?php echo $city; ?> corporate gift,  Bloomex gift basket, <?php echo $city; ?> coffee basket, <?php echo $city; ?> tea basket, <?php echo $city; ?> gourmet baskets, <?php echo $city; ?> food baskets, <?php echo $city; ?> gift basket delivery, <?php echo $city; ?> corporate gift basket, <?php echo $city; ?> candy gift basket, <?php echo $city; ?> cookie gift basket, <?php echo $city; ?> fruit basket delivery, <?php echo $city; ?> gifts, <?php echo $city; ?> gift hamper, <?php echo $city; ?> hamper" />

        <meta name="Generator" content="Joomla! - Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved." />
        <meta name="robots" content="index, follow" />
        <link rel="shortcut icon" href="https://bloomex.ca/images/bloomex.ico" />
        <link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/css/gift_landing_template.css" />
        <link rel="shortcut icon" href="https://bloomex.ca/images/bloomex.ico"/>
        <script type="text/javascript">
            function bookmarksite(title, url){
                if (document.all)
                    window.external.AddFavorite(url, title);
                else if (window.sidebar)
                    window.sidebar.addPanel(title, url, "")
            }
        </script>

    </head>
    <body>
        <!-- Dynamic --> 	

        <TABLE id="fullPage" class="fullPage" cellSpacing="0" cellPadding="0" width="790" align="center"  border=0><TBODY>
                <TR>
                    <TD width="28" height="6" class="borderLeft"></TD>

                    <td colspan="2" style="padding-left: 10px; vertical-align: middle; padding-top: 5px;">
                        <a href="https://bloomex.ca/index.php?lang=fr"><img border="0" width="40" height="11" alt="" src="https://bloomex.ca/templates/bloomex7/images/francaise.gif"></a>
                        <div class="delivery-intro">&nbsp;&nbsp;&nbsp;&nbsp;
                            FOR DELIVERIES OUTSIDE OF CANADA&nbsp;&nbsp;
                            <a href="https://bloomexusa.com/"><img border="0" align="absbottom" height="14" src="https://bloomex.ca/templates/bloomex7/images/flag_usa.png" style="margin-top: 3px;">&nbsp;USA</a>&nbsp;|&nbsp;
                            <a href="https://www.serenataflowers.com"><img border="0" align="absbottom" height="14" src="https://bloomex.ca/templates/bloomex7/images/flag_great_britain.png" style="margin-top: 3px;">&nbsp;UK&nbsp;</a>&nbsp;|&nbsp;
                            <a href="https://bloomex.com.au"><img border="0" align="absbottom" height="14" src="https://bloomex.ca/templates/bloomex7/images/flag_australia.png" style="margin-top: 3px;">&nbsp;AUS&nbsp;</a>
                        </div><br>
                    </td>

                    <TD width="26" class="borderRight"></TD>
                </TR>
                <TR>
                    <TD class="borderLeft" width="28"></TD>

                    <TD style="PADDING-LEFT: 10px; VERTICAL-ALIGN: middle" width="500">Easy ordering for <b><?php echo $city; ?> Flower 
                            Delivery</b> at:&nbsp;&nbsp;&nbsp;



                        <IMG  style="VERTICAL-ALIGN: bottom" height="13" alt="" src="https://bloomex.ca/templates/bloomex7/images/phone.gif" 
                              width="13" border=0>&nbsp;<B>1-888-912-5666</B>

                    </TD>
                    <TD align="right">
                        <TABLE cellSpacing="0" cellPadding="0" width="224" border="0" class="top-img-menu">
                            <TBODY>
                                <TR>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="https://bloomex.ca/">

                                            <IMG  alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/home_landing.jpg"
                                                  border="0">
                                        </A>
                                    </TD>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="https://bloomex.ca/index.php?option=com_user&task=UserDetails&Itemid=1&link=top">

                                            <IMG height="20" alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/myAccount.gif" width="101" 
                                                 border="0">

                                        </A>
                                    </TD>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="https://bloomex.ca/index.php?page=shop.cart&option=com_virtuemart&Itemid=80&link=top">

                                            <IMG height="20" alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/shoppingCart.gif" border="0">

                                        </A>
                                    </TD>
                                </TR>
                            </TBODY></TABLE>
                    </TD>
                    <TD class="borderRight" width="26"></TD>
                </TR>
                <TR>
                    <TD class="borderLeft" width="28"></TD>
                    <TD colSpan=2 width="731" height="193" align="left" valign="top" scope="col" background="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/gift_baskets_banner.jpg" onclick="location.href='<?php echo $mosConfig_live_site; ?>/index.php?page=shop.browse&amp;category_id=83&amp;option=com_virtuemart&amp;Itemid=73'" style="cursor:pointer;background-repeat:no-repeat; background-position:5px 0px;">
                        <div style="display:block;text-align:left;font-size:12px;width:155px;padding:0px 0px 0px 10px;margin:130px 0px 0px 0px;float:left;line-height:150%;">
                            <b><?php echo $city; ?>, <?php echo $prov; ?></b><br>
                                <b><font color="#EE1111">Call Now!</font></b> <?php echo $tele ?>
                        </div>
                    </TD>
                    <TD class="borderRight" width="26"></TD>
                </TR>
            </TBODY></TABLE>
        <!-----TOP MENU & SEARCH---->
        <TABLE id="fullPage" class="fullPage" cellSpacing="0" cellPadding="0" width="790" align="center" border="0">
            <TBODY>

                <TR>
                    <TD class="borderLeft" width="28" ></TD>
                    <TD height="32" width="412"style="BACKGROUND-COLOR: rgb(216,190,232);"  >

                        <script type="text/javascript" src="https://bloomex.ca/modules/mod_swmenupro/transmenu_Packed.js"></script>

                        <!--swMenuPro5.6 transmenu by https://www.swmenupro.com-->

                        <style type='text/css'>
                            <!--
                            .transMenu77 {
                                position:absolute ; 
                                overflow:hidden; 
                                left:-1000px; 
                                top:-1000px; 
                            }
                            .transMenu77 .content {
                                position:absolute  ; 
                            }
                            .transMenu77 .items {
                                border: 0px dashed #FFFFFF ; 
                                position:relative ; 
                                left:0px; top:0px; 
                                z-index:2; 
                            }
                            .transMenu77  td
                            {
                                padding: 5px 8px 5px 8px !important;  
                                font-size: 12px !important ; 
                                font-family: Arial, Helvetica, sans-serif !important ; 
                                text-align: left !important ; 
                                font-weight: normal !important ; 
                                color: #FFFFFF !important ; 
                            } 
                            #subwrap77 
                            { 
                                text-align: left ; 
                            }
                            .transMenu77  .item.hover td
                            { 
                                color: #020028 !important ; 
                            }
                            .transMenu77 .item { 
                                text-decoration: none ; 
                                cursor:pointer; 
                            }
                            .transMenu77 .background {
                                background-color: #674d7a !important ; 
                                position:absolute ; 
                                left:0px; top:0px; 
                                z-index:1; 
                                opacity:0.8; 
                                filter:alpha(opacity=80) 
                            }
                            .transMenu77 .shadowRight { 
                                position:absolute ; 
                                z-index:3; 
                                top:3px; width:2px; 
                                opacity:0.8; 
                                filter:alpha(opacity=80)
                            }
                            .transMenu77 .shadowBottom { 
                                position:absolute ; 
                                z-index:1; 
                                left:3px; height:2px; 
                                opacity:0.8; 
                                filter:alpha(opacity=80)
                            }
                            .transMenu77 .item.hover {
                                background-color: #E8A9FF !important ; 
                            }
                            .transMenu77 .item img { 
                                margin-left:10px !important ; 
                            }
                            table.menu77 {
                                top: 0px; 
                                left: 3px; 
                                position:relative ; 
                                margin:0px !important ; 
                                border: 0px dashed #FFFFFF ; 
                                z-index: 1; 
                            }
                            table.menu77 a{
                                margin:0px !important ; 
                                padding: 10px 8px 2px 8px !important ; 
                                display:block !important; 
                                position:relative !important ; 
                            }
                            div.menu77 a,
                            div.menu77 a:visited,
                            div.menu77 a:link {
                                height:23px; 
                                font-size: 12px !important ; 
                                font-family: Arial, Helvetica, sans-serif !important ; 
                                text-align: left !important ; 
                                font-weight: normal !important ; 
                                color: #1B003C !important ; 
                                text-decoration: none !important ; 
                                margin-bottom:0px !important ; 
                                display:block !important; 
                                white-space:nowrap ; 
                            }
                            div.menu77 td {
                                border-bottom: 1px solid #FFFFFF ; 
                                border-top: 1px solid #FFFFFF ; 
                                border-left: 1px solid #FFFFFF ; 
                            } 
                            div.menu77 td.last77 {
                                border-right: 1px solid #FFFFFF ; 
                            } 
                            #trans-active77 a{
                                color: #1B003C !important ; 
                                background-color: #E8A9FF !important ; 
                            } 
                            #menu77 a.hover   { 
                                color: #1B003C !important ; 
                                background-color: #E8A9FF !important ; 
                                display:block; 
                            }
                            #menu77 span {
                                display:none; 
                            }
                            #menu77 a img.seq1,
                            .transMenu77 img.seq1,
                            {
                                display:    inline; 
                            }
                            #menu77 a.hover img.seq2,
                            .transMenu77 .item.hover img.seq2 
                            {
                                display:   inline; 
                            }
                            #menu77 a.hover img.seq1,
                            #menu77 a img.seq2,
                            .transMenu77 img.seq2,
                            .transMenu77 .item.hover img.seq1
                            {
                                display:   none; 
                            }
                            #trans-active77 a img.seq1
                            {
                                display: none;
                            }
                            #trans-active77 a img.seq2
                            {
                                display: inline;
                            }

                            #addvloc { padding: 5px 0px 0px 8px; }

                            -->
                        </style>
                        <div id="wrap77" class="menu77" align="left">
                            <table cellspacing="0" cellpadding="0" id="menu77" class="menu77" > 
                                <tr> 
                                    <td> 
                                        <a id="menu77303" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=7&Itemid=1&link=down" 
                                           >Specials</a>
                                    </td> 
                                    <td> 
                                        <a id="menu77246" href=" https://bloomex.ca/index.php?page=shop.browse&category_id=25&option=com_virtuemart&Itemid=80 
                                           &link=down" > Roses</a>
                                    </td> 
                                    <td> 
                                        <a id="menu77262" 
                                           href="https://bloomex.ca/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=9&amp;Itemid=1&link=down" 
                                           >Birthday</a>
                                    </td> 
                                    <td> 
                                        <a id="menu77284" href=" 
                                           https://bloomex.ca/index.php?page=shop.browse&category_id=11&option=com_virtuemart&Itemid=127&link=down" >Get Well</a>
                                    </td>
                                    <td> 
                                        <a id="menu77289" href=" 
                                           https://bloomex.ca/index.php?page=shop.browse&root=65&category_id=69&option=com_virtuemart&Itemid=155&link=down" >Under 
                                            $20</a>
                                    </td> 
                                    <td class="last77"> 
                                        <a id="menu77305" >Help/Account</a>
                                    </td> 
                                </tr> 
                            </table></div> 
                    </TD>
                    <TD  height="32" width="321" style="background-color: rgb(216,190,232);">

                        <!-- AJAX Header Rotator Module (v1.1) starts here -->
                        <script src="https://bloomex.ca/modules/mod_jw_ajaxhr/prototype.lite.js" type="text/javascript"></script>
                        <script src="https://bloomex.ca/modules/mod_jw_ajaxhr/moo.fx.js" type="text/javascript"></script>
                        <script src="https://bloomex.ca/modules/mod_jw_ajaxhr/moo.fx.pack.js" type="text/javascript"></script>
                        <script src="https://bloomex.ca/modules/mod_jw_ajaxhr/rotator.js" type="text/javascript"></script>
                        <style type="text/css" media="all"> 
                            <!--
                            #rotator {position:relative;}
                            #rotator a {cursor:default;}
                            #rotator img {border:0;margin:0;}
                            --> 
                        </style>
                        <div id="rotator"></div>
                        <script type="text/javascript">
                            countArticle = 0;
                            var mySlideData = new Array();
                            mySlideData[countArticle++] = new Array('/images/stories/headers/contacts_new.gif','#','','');
                            mySlideData[countArticle++] = new Array('/images/stories/headers/same_day.gif','#','','');
                            var slideShowDelay = 7000;				
                        </script>
                        <script type="text/javascript">
                            function addLoadEvent(func) {
                                var oldonload = window.onload;
                                if (typeof window.onload != 'function') {
                                    window.onload = func;
                                } else {
                                    window.onload = function() {
                                        oldonload();
                                        func();
                                    } } }
                            function startSlideshow() {
                                initSlideShow($('rotator'), mySlideData);
                            }
                            addLoadEvent(startSlideshow);
                        </script>
                        <!-- AJAX Header Rotator Module (v1.1) ends here -->
                    </TD>
                    <TD class="borderRight" width="26" height="24"></TD>
                </TR>
            </TBODY></TABLE>
        <!-----END TOP MENU & SEARCH---->
        <!--Phone Number Place Holder -->


        <!-----BODY ---->
        <TABLE id="fullPage" class="fullPage" cellSpacing="0" cellPadding="0" width="790" align="center" border="0">
            <TBODY>
                <TR>
                    <TD class="borderLeft" width="28"></TD>
                    <TD style="BACKGROUND-IMAGE: url(templates/bloomex7/images/leftBg.gif); BACKGROUND-POSITION: 100% 50%; 	
                        background-repeat: no-repeat; BACKGROUND-COLOR: rgb(216,190,232);" vAlign=top width="164">
                        <!----  LEGT      ---->
                        <TABLE cellSpacing="0" cellPadding="0" width="100%" border=0 >
                            <TBODY>
                                <TR>
                                    <TD align=center><br>
                                            <link rel="stylesheet" type="text/css" media="all" 
                                                  href="https://bloomex.ca/components/com_instant_search/class/css/d4j_common_css.css" title="green" />

                                            <script type="text/javascript">
                                                var mosConfig_live_site = "https://bloomex.ca";
                                            </script>

                                            <script type="text/javascript" 
                                            src="https://bloomex.ca/components/com_instant_search/class/js/d4j_common_include.compact.js"></script>

                                            <script type="text/javascript" 
                                            src="https://bloomex.ca/components/com_instant_search/class/js/d4j_display_engine.compact.js"></script>

                                            <script type="text/javascript" 
                                            src="https://bloomex.ca/components/com_instant_search/class/js/d4j_ajax_engine.compact.js"></script>

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
                                            <script type="text/javascript" src="https://bloomex.ca/components/com_instant_search/instant_search.compact.js"></script>
                                            <!-- Initiate AJAX engine for instant search form /-->


                                            <form action="https://bloomex.ca/index.php" method="post" name="instantSearchForm">
                                                <div class="search">
                                                    <input alt="search" class="inputbox" type="text" name="searchword" size="15" value="search..."  onblur="if(this.value=='') 
    this.value='search...';" onfocus="if(this.value=='search...') this.value='';" onkeyup="if (this.value.length < max_chars) { 
    prepareSearch(this.value); } else { this.value = this.value.substring(0, max_chars - 1); }" /><input type="submit" 
                                                           value="ok" class="button"/><div id="instant_search_form" class="hiddenDiv" style="display: none;"></div>
                                                </div>
                                                <input type="hidden" name="option" value="search" />
                                            </form>
                                    </TD>
                                </TR>
                                <TR>
                                    <TD>

                                    </TD>
                                </TR>
                                <TR>
                                    <TD><?php
if ($activate_loc == 1) {

    //Location Based SEO

    echo '<div id="addvloc">';

    echo '<strong>Bloomex Flowers ' . $city . '</strong><br />'; // Intro
    echo $location_address . ',<br />';
    echo $city . '<br />';
    echo $prov . '<br />';
    echo $location_postcode;
    echo '<br /><br />';
    echo $location_telephone;

    echo '</div>';
}
?><br /><table cellpadding="0" cellspacing="0" class="moduletable">
                                            <tr>
                                                <td>

                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=80&link=side" 
                                                                    class="mainlevel" >Roses</a></td></tr>
                                                        <tr align="left"><td><a href="https://bloomex.ca/index.php?option=com_content&task=view&id=44&Itemid=169&link=side" 
                                                                                class="mainlevel" >Gift Baskets</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=19&amp;option=com_virtuemart&amp;Itemid=73&link=side" 
                                                                    class="mainlevel" >Mixed Bouquets</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=64&amp;option=com_virtuemart&amp;Itemid=74&link=side" 
                                                                    class="mainlevel" >Arrangements</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=78&link=side" 
                                                                    class="mainlevel" >Lilies</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=22&amp;option=com_virtuemart&amp;Itemid=77&link=side" 
                                                                    class="mainlevel" >Iris</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=31&amp;option=com_virtuemart&amp;Itemid=103&link=side" 
                                                                    class="mainlevel" >Plants</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=84&amp;Itemid=123&link=side" 
                                                                    class="mainlevel" >Fresh Fruit</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=52&amp;option=com_virtuemart&amp;Itemid=124&link=side" 
                                                                    class="mainlevel" >Chocolates &amp; Desserts</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&category_id=9&option=com_virtuemart&Itemid=49&link=side" 
                                                                    class="mainlevel" >Birthday</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&category_id=11&option=com_virtuemart&Itemid=255&link=side" 
                                                                    class="mainlevel" >Get Well</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=91&link=side" 
                                                                    class="mainlevel" >Sympathy</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=82&amp;option=com_virtuemart&amp;Itemid=231&link=side" 
                                                                    class="mainlevel" >Extra Touches</a></td></tr>
                                                    </table>			</td>
                                            </tr>
                                        </table>
                                        <br/></TD>
                                </TR>
                                <TR>
                                    <TD><IMG height=70 alt="" src="https://bloomex.ca/templates/bloomex7/images/two.jpg" width="164" border=0></TD>
                                </TR>
                                <TR>
                                    <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
                                            <tr>
                                                <td>

                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=62&amp;option=com_virtuemart&amp;Itemid=101&link=side" 
                                                                    class="mainlevel" >Special Savings</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;root=54&amp;category_id=58&amp;option=com_virtuemart&amp;Itemid=155&link=side" 
                                                                    class="mainlevel" >Free Shipping offer</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=55&amp;option=com_virtuemart&amp;Itemid=94&link=side" 
                                                                    class="mainlevel" >Best Sellers</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;root=65&amp;category_id=69&amp;option=com_virtuemart&amp;Itemid=155&link=side" 
                                                                    class="mainlevel" >By price: under $20</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;root=65&amp;category_id=66&amp;option=com_virtuemart&amp;Itemid=155&link=side" 
                                                                    class="mainlevel" >         $20 - $30</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;root=65&amp;category_id=67&amp;option=com_virtuemart&amp;Itemid=155&link=side" 
                                                                    class="mainlevel" >         $30 - $40</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=shop.browse&amp;root=65&amp;category_id=70&amp;option=com_virtuemart&amp;Itemid=155&link=side" 
                                                                    class="mainlevel" >           $50 and up</a></td></tr>
                                                        <tr align="left"><td><a href="https://bloomex.ca/index.php?option=com_currencyconverter&amp;Itemid=237&link=side" 
                                                                                class="mainlevel" >Currency Converter</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=36&amp;Itemid=238&link=side" class="mainlevel" 
                                                                    >Bloomex Fair Play</a></td></tr>
                                                    </table>			</td>
                                            </tr>
                                        </table>
                                        <br/></TD>
                                </TR>
                                <TR>
                                    <TD><IMG height=70 alt="" src="https://bloomex.ca/templates/bloomex7/images/three.jpg" width="164" border=0>
                                    </TD>
                                </TR>
                                <TR>
                                    <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
                                            <tr>
                                                <td>

                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=29&amp;Itemid=220&link=side" class="mainlevel" 
                                                                    >Delivery Policy</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=34&amp;Itemid=229&link=side" class="mainlevel" 
                                                                    >Same Day Delivery</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=35&amp;Itemid=230&link=side" class="mainlevel" 
                                                                    >Return Policy</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=31&amp;Itemid=200&link=side" class="mainlevel" 
                                                                    >Photo on delivery</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=account.index&amp;option=com_virtuemart&amp;Itemid=1&link=side" class="mainlevel" 
                                                                    >Track Orders</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?page=reminder.index&amp;option=com_virtuemart&amp;Itemid=190&link=side" class="mainlevel" 
                                                                    >Reminder Service</a></td></tr>
                                                        <tr align="left"><td><a href="https://bloomex.ca/index.php?option=com_easybook&amp;Itemid=236&link=side" class="mainlevel" 
                                                                                >Feedback</a></td></tr>
                                                        <tr align="left"><td><a href="https://bloomex.ca/index.php?option=com_contxtd&amp;task=blog&amp;Itemid=3&link=side" 
                                                                                class="mainlevel" >Contact Us</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=12&amp;Itemid=221&link=side" class="mainlevel" 
                                                                    >About US</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=15&amp;Itemid=222&link=side" class="mainlevel" 
                                                                    >Flower Care</a></td></tr>
                                                        <tr align="left"><td><a 
                                                                    href="https://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=33&amp;Itemid=228&link=side" class="mainlevel" 
                                                                    >Jobs at Bloomex</a></td></tr>
                                                    </table>			</td>
                                            </tr>
                                        </table>
                                        <br/></TD>
                                </TR>
                                <TR>
                                    <TD><IMG height=70 alt="" src="https://bloomex.ca/templates/bloomex7/images/four.jpg" width="164" border=0></TD>
                                </TR>
                                <TR>
                                    <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
                                            <tr>
                                                <td>
                                                    <table width="100%">
                                                        <tr>
                                                            <td>
                                                                <a class="mainlevel" 
                                                                   href="https://bloomex.ca/index.php?page=shop.cart&option=com_virtuemart&Itemid=1&link=side">
                                                                    Show Cart</a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0"><tr><td><img 
                                                                                src="https://bloomex.ca/modules/images/shop_cart.gif" width="32" height="32"></td><td valign="top"><p><b>Your Cart is 
                                                                                    currently empty.</b> <p><font size=1><i>If you are experiencing difficulties with placing an order online, please call our 
                                                                                            toll free number 888-912-5666 to place your order over the phone</i></font></td></td></tr></table></td>
                                                                                    </tr>
                                                                                    </table>
                                                                                    </td>
                                                                                    </tr>
                                                                                    </table>
                                                                                    <br/></TD>
                                                                                    </TR>

                                                                                    </TBODY></TABLE>
                                                                                    <center>
                                                                                        <!-- end GeoTrust Smart Icon tag -->
                                                                                        &nbsp;</center>
                                                                                    <p>  

                                                                                        <center><a href="mailto:WeCare@BloomEx.ca">WeCare@BloomEx.ca</a></center>
                                                                                        <!----  END LEGT      ---->
                                                                                        </TD>
                                                                                        <TD vAlign=top width="568" align="left">

                                                                                            <!---- BODY ---->
                                                                                            <TABLE cellSpacing="0" cellPadding="0" border=0 align="center">
                                                                                                <TBODY>
                                                                                                    <!--Home Banner--> 
                                                                                                    <TR>
                                                                                                        <TD align="center">
                                                                                                            <!-- Bloomex Live Help -->
                                                                                                            <script type="text/javascript">
                                                                                                            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "https://www.");
                                                                                                            document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' 
                                                                                                            type='text/javascript'%3E%3C/script%3E"));
                                                                                                            </script>
                                                                                                            <script type="text/javascript">
                                                                                                            try {
                                                                                                                var pageTracker = _gat._getTracker("UA-232639-1");
                                                                                                                pageTracker._trackPageview();
                                                                                                            } catch(err) {}</script>

                                                                                                            <!-- Google Code -->
                                                                                                            <!-- Google Code for All Site Visitors Remarketing List -->
                                                                                                            <script type="text/javascript">
                                                                                                            <!--
                                                                                                            var google_conversion_id = 1067327793;
                                                                                                            var google_conversion_language = "en";
                                                                                                            var google_conversion_format = "3";
                                                                                                            var google_conversion_color = "666666";
                                                                                                            var google_conversion_label = "BHNLCMXOuAEQscL4_AM";
                                                                                                            var google_conversion_value = 0;
                                                                                                            //-->
                                                                                                            </script>
                                                                                                            <script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
                                                                                                            </script>
                                                                                                            <noscript>
                                                                                                                <div style="display:inline;">
                                                                                                                    <img height="1" width="1" style="border-style:none;" alt="" 
                                                                                                                         src="https://www.googleadservices.com/pagead/conversion/1067327793/?label=BHNLCMXOuAEQscL4_AM&amp;guid=ON&amp;script=0"/>
                                                                                                                </div>
                                                                                                            </noscript>

                                                                                                            <!-- copyright 2010 Bloomex -->



                                                                                                        </TD>
                                                                                                    </TR>
                                                                                                    <TR>
                                                                                                        <TD align="center">

                                                                                                            <table width="100%" cellspacing="5" cellpadding="0" border="0"><tbody>       <tr>
                                                                                                                        <td>&nbsp;
                                                                                                                            <a href="/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=18&amp;Itemid=1">
                                                                                                                                <img style="margin: 0px 0px 0px 1px" src="/templates/bloomex7/images/flowers_banner.jpg" border="0" alt=""></a> </td><td>&nbsp;
                                                                                                                            <a href="/index.php?page=shop.browse&amp;category_id=83&amp;option=com_virtuemart&amp;Itemid=73"><img style="margin: 0px 0px 0px 1px" src="/templates/bloomex7/images/GB_banner-sm.gif" border="0" alt=" "></a> </td>
                                                                                                                    </tr></tbody></table>


                                                                                                            <table cellpadding="0" cellspacing="0" class="moduletable_disabled">
                                                                                                                <tr>
                                                                                                                    <td>


                                                                                                                        <!-- New Products -->

                                                                                                                        <table cellspacing="0" cellpadding="4" width="90%">
                                                                                                                            <tbody>
                                                                                                                                <tr>
                                                                                                                                    <td align="center" valign="top" width="50%">
                                                                                                                                        <p>
                                                                                                                                            <a title="Fruit Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=84&Itemid=123"><strong>Fruit Baskets</strong></a><br /><a href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=84&Itemid=123"><img height="200" src="https://bloomex.ca/images/fruit-baskets.jpg" width="170" border="0" />&nbsp;&nbsp;</a>
                                                                                                                                            <a title="Fruit Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=84&Itemid=123"><br /></a>
                                                                                                                                            <br />
                                                                                                                                        </p>
                                                                                                                                    </td>
                                                                                                                                    <td align="center" valign="top" width="50%">
                                                                                                                                        <p>
                                                                                                                                            <a title="Valentine Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=117&Itemid=123"><strong>Valentine Baskets</strong></a><br /><a href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=117&Itemid=123"><img height="200" src="/images/holiday.jpg" width="170" border="0" />&nbsp;&nbsp;</a>
                                                                                                                                            <a title="Valentine Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=117&Itemid=123"><br /></a>
                                                                                                                                            <br />
                                                                                                                                        </p>
                                                                                                                                    </td>

                                                                                                                                    <td align="center" valign="top" width="50%">
                                                                                                                                        <p>
                                                                                                                                            <a title="Sweets Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=85&Itemid=123"><strong>Sweets Baskets</strong></a>
                                                                                                                                            <br />
                                                                                                                                            <a href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=85&Itemid=123"><img height="200" src=" https://bloomex.ca/images/sweets-baskets.jpg" width="170" border="0" /></a>
                                                                                                                                            <a title="Sweets Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=85&Itemid=123"></a>
                                                                                                                                        </p>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td align="center" valign="top" width="50%">
                                                                                                                                        <p>
                                                                                                                                            <a title="Coffee and Tea Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=86&Itemid=123"><strong>Coffee and Tea Baskets</strong>
                                                                                                                                                <br />
                                                                                                                                                <img height="200" src=" https://bloomex.ca/images/coffee-tea-baskets.jpg" width="170" border="0" />&nbsp;&nbsp;</a> 
                                                                                                                                        </p>
                                                                                                                                    </td>

                                                                                                                                    <td align="center" valign="top" width="50%">
                                                                                                                                        <p>
                                                                                                                                            <strong><a href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=92&Itemid=123">Custom Order</a></strong>
                                                                                                                                            <br />
                                                                                                                                            <a title="Custom Order" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=92&Itemid=123"><img height="200" src=" https://bloomex.ca/images/custom-baskets.jpg" width="170" border="0" />&nbsp;&nbsp;</a> 
                                                                                                                                            <strong><a href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=92&Itemid=123"></a></strong>
                                                                                                                                        </p>
                                                                                                                                    </td>

                                                                                                                                    <td align="center" valign="top" width="50%">
                                                                                                                                        <p>
                                                                                                                                            <a title="Gourmet Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=87&Itemid=123"><strong>Gourmet Baskets</strong></a>
                                                                                                                                            <br />
                                                                                                                                            <a href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=87&Itemid=123"><img height="200" src=" https://bloomex.ca/images/gourmet-baskets.jpg" width="170" border="0" /></a> 
                                                                                                                                            <a title="Gourmet Baskets" href="https://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=87&Itemid=123"></a>
                                                                                                                                        </p>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            </tbody>

                                                                                                                        </table>


                                                                                                                        <!-- /New Products -->


                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </table>
                                                                                                            <br/>

                                                                                                            <TABLE style="MARGIN-LEFT: 0px" cellSpacing=0 cellPadding=0 width="100%" border=0>
                                                                                                                <TBODY>
                                                                                                                    <TR>
                                                                                                                        <TD align=middle><IMG height=23 alt="" src="https://bloomex.ca/templates/bloomex7/images/ocassion.gif" width=186 
                                                                                                                                              border=0></TD>

                                                                                                                        <TD align=middle><IMG height=23 alt="" src="https://bloomex.ca/templates/bloomex7/images/flowergift.gif" 
                                                                                                                                              width=186 border=0></TD>


                                                                                                                        <TD align=middle width=186><IMG height=23 alt="" src="https://bloomex.ca/templates/bloomex7/images/featured.gif" 
                                                                                                                                                        width=186 border=0></TD>

                                                                                                                    </TR>
                                                                                                                    <TR valign="top">
                                                                                                                        <TD style="background: Rgb(239,237,237);">
                                                                                                                            <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                <tr>
                                                                                                                                    <td>

                                                                                                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=47&link=bot" 
                                                                                                                                                        class="mainlevel" >Anniversary</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=9&amp;option=com_virtuemart&amp;Itemid=48&link=bot" 
                                                                                                                                                        class="mainlevel" >Birthday</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=44&amp;option=com_virtuemart&amp;Itemid=126&link=bot" 
                                                                                                                                                        class="mainlevel" >Business Gifts</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=10&amp;option=com_virtuemart&amp;Itemid=49&link=bot" 
                                                                                                                                                        class="mainlevel" >Congratulations</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=11&amp;option=com_virtuemart&amp;Itemid=127&link=bot" 
                                                                                                                                                        class="mainlevel" >Get Well</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=46&amp;option=com_virtuemart&amp;Itemid=128&link=bot" 
                                                                                                                                                        class="mainlevel" >Gifts For Him</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=12&amp;option=com_virtuemart&amp;Itemid=129&link=bot" 
                                                                                                                                                        class="mainlevel" >Housewarming</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=13&amp;option=com_virtuemart&amp;Itemid=130&link=bot" 
                                                                                                                                                        class="mainlevel" >Just Because</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=47&amp;option=com_virtuemart&amp;Itemid=131&link=bot" 
                                                                                                                                                        class="mainlevel" >Kid's Gifts</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=14&amp;option=com_virtuemart&amp;Itemid=132&link=bot" 
                                                                                                                                                        class="mainlevel" >Love and Romance</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=15&amp;option=com_virtuemart&amp;Itemid=133&link=bot" 
                                                                                                                                                        class="mainlevel" >New Baby</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=37&amp;option=com_virtuemart&amp;Itemid=1&link=bot" 
                                                                                                                                                        class="mainlevel" >Holiday</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=135&link=bot" 
                                                                                                                                                        class="mainlevel" >Sympathy and Funeral</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=17&amp;option=com_virtuemart&amp;Itemid=136&link=bot" 
                                                                                                                                                        class="mainlevel" >Thank You</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=49&amp;option=com_virtuemart&amp;Itemid=137&link=bot" 
                                                                                                                                                        class="mainlevel" >Wedding Gifts</a></td></tr>
                                                                                                                                        </table>			</td>
                                                                                                                                </tr>
                                                                                                                            </table>
                                                                                                                        </TD>
                                                                                                                        <TD style="background: Rgb(239,237,237);">
                                                                                                                            <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                <tr>
                                                                                                                                    <td>

                                                                                                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=19&amp;option=com_virtuemart&amp;Itemid=50&link=bot" 
                                                                                                                                                        class="mainlevel" >All Mixed Bouquets</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=64&amp;option=com_virtuemart&amp;Itemid=51&link=bot" 
                                                                                                                                                        class="mainlevel" >All Arrangements</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=55&amp;option=com_virtuemart&amp;Itemid=52&link=bot" 
                                                                                                                                                        class="mainlevel" >Best Sellers</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=32&amp;option=com_virtuemart&amp;Itemid=53&link=bot" 
                                                                                                                                                        class="mainlevel" >Blooming Plants</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=52&amp;option=com_virtuemart&amp;Itemid=138&link=bot" 
                                                                                                                                                        class="mainlevel" >Chocolates and Desserts</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=21&amp;option=com_virtuemart&amp;Itemid=139&link=bot" 
                                                                                                                                                        class="mainlevel" >Daisies</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=34&amp;option=com_virtuemart&amp;Itemid=140&link=bot" 
                                                                                                                                                        class="mainlevel" >Garden</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=53&amp;option=com_virtuemart&amp;Itemid=141&link=bot" 
                                                                                                                                                        class="mainlevel" >Gourmet Gifts</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=22&amp;option=com_virtuemart&amp;Itemid=142&link=bot" 
                                                                                                                                                        class="mainlevel" >Iris</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=143&link=bot" 
                                                                                                                                                        class="mainlevel" >Lilies</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=60&amp;option=com_virtuemart&amp;Itemid=144&link=bot" 
                                                                                                                                                        class="mainlevel" >New Arrivals</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=24&amp;option=com_virtuemart&amp;Itemid=145&link=bot" 
                                                                                                                                                        class="mainlevel" >Orchids</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=35&amp;option=com_virtuemart&amp;Itemid=146&link=bot" 
                                                                                                                                                        class="mainlevel" >Potted Plants and Trees</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=148&link=bot" 
                                                                                                                                                        class="mainlevel" >Roses</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=26&amp;option=com_virtuemart&amp;Itemid=149&link=bot" 
                                                                                                                                                        class="mainlevel" >One Dozen Roses</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=27&amp;option=com_virtuemart&amp;Itemid=150&link=bot" 
                                                                                                                                                        class="mainlevel" >Two Dozen Roses</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=62&amp;option=com_virtuemart&amp;Itemid=151&link=bot" 
                                                                                                                                                        class="mainlevel" >Special Savings</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=28&amp;option=com_virtuemart&amp;Itemid=152&link=bot" 
                                                                                                                                                        class="mainlevel" >Stargazer Lilies</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=29&amp;option=com_virtuemart&amp;Itemid=153&link=bot" 
                                                                                                                                                        class="mainlevel" >Sunflowers</a></td></tr>
                                                                                                                                            <tr align="left"><td><a 
                                                                                                                                                        href="https://bloomex.ca/index.php?page=shop.browse&amp;category_id=71&amp;option=com_virtuemart&amp;Itemid=124&link=bot" 
                                                                                                                                                        class="mainlevel" >Fresh Fruits</a></td></tr>
                                                                                                                                        </table>			</td>
                                                                                                                                </tr>
                                                                                                                            </table>
                                                                                                                        </TD>
                                                                                                                        <TD style="vertical-align:top;background-color:white;font-family:Verdana, arial, sans serif;font-size: 
                                                                                                                            0.75em;color: rgb(81,81,81);width: 186px;padding: 0px;padding-top: 10px;background-image: 
                                                                                                                            url(templates/bloomex7/images/featBottom.gif);background-repeat: no-repeat;background-position: bottom left;" width=186>
                                                                                                                            <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                                                <tr>
                                                                                                                                    <td>
                                                                                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                                                                                            <tr align="center" >
                                                                                                                                                <td width="100%"><?php
                                        $title = 'The Perfect Gift';
                                        $sku = 'LF72-72';
                                        $pLink = 'https://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=88&product_id=349&Itemid=47';
                                        $iLink = 'https://bloomex.ca/components/com_virtuemart/shop_image/product/LF72-72_thumb.jpg';
                                        $price = '$69.95';
                                        $productid = 349;
?>
                                                                                                                                                    <span style="font-weight:bold;font-size: 11px; color:#666666;"><?php echo $title; ?></span><br>
                                                                                                                                                        <br /><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;"><?php echo $sku; ?></span>
                                                                                                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;font-size: 11px; color:#CC0000;"><span 
                                                                                                                                                                style="font-weight:bold">
                                                                                                                                                    <?php echo $price; ?></span>
                                                                                                                                                        </span>
                                                                                                                                                        <a title="<?php echo $title; ?>" 
                                                                                                                                                           href="<?php echo $pLink; ?>"><img 
                                                                                                                                                                src="<?php echo $iLink; ?>" width="170" 
                                                                                                                                                                height="200" alt="<?php echo $title; ?>" border="0" /></a><br />
                                                                                                                                                        <br />
                                                                                                                                                </td>
                                                                                                                                            </tr>
                                                                                                                                        </table>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            </table>
                                                                                                                        </TD>
                                                                                                                    </TR></TBODY>
                                                                                                            </TABLE>
                                                                                                        </TD></TR></TBODY></TABLE>
                                                                                            <!----END BODY---->
                                                                                        </TD>
                                                                                        <TD class="borderRight" width=26></TD>
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
                                                                                                                        <ul id="mainlevel-nav">
                                                                                                                            <li><a href="https://bloomex.ca?link=foot" class="mainlevel-nav" 
                                                                                                                                   id="active_menu-nav">Bloomex Flowers</a></li>
                                                                                                                            <li><a 
                                                                                                                                    href="https://bloomex.ca/index.php?option=com_contxtd&amp;task=blog&amp;Itemid=3&link=foot" class="mainlevel-nav" >Contact 
                                                                                                                                    Us</a></li><li><a href="https://bloomex.ca/index.php?option=com_search&amp;Itemid=5&link=foot" class="mainlevel-nav" 
                                                                                                                                              >Search</a></li><li><a href="https://bloomex.ca/index.php?option=com_login&amp;Itemid=36&link=foot" class="mainlevel-nav" 
                                                                                                                                                   >LOGIN</a></li></ul>			</td>
                                                                                                                </tr>
                                                                                                            </table>
                                                                                                        </TD>
                                                                                                    </TR>
                                                                                                </TABLE>
                                                                                            </TD>
                                                                                            <TD class="borderRight" width=26></TD>
                                                                                        </TR>
                                                                                        </TABLE>
                                                                                        <!----END BODY ---->
                                                                                        <TABLE cellSpacing=0 cellPadding=0 width=790 align=center border=0>
                                                                                            <TBODY>
                                                                                                <TR>
                                                                                                    <TD class="borderLeftBottom" width=29 height=11></TD>
                                                                                                    <TD class="borderBottom" width=734></TD>
                                                                                                    <TD class="borderRightBottom" width=27 height=11></TD>
                                                                                                </TR>
                                                                                                <TR>
                                                                                                    <TD colspan=3 align="center">
                                                                                                        <table cellpadding="0" cellspacing="0" class="moduletable">
                                                                                                            <tr>
                                                                                                                <td>
                                                                                                                    <table class="htmtableborders" style="WIDTH: 50%" cellspacing="10" cellpadding="10" 
                                                                                                                           align="center" border="0"><tbody><tr><td width="35%"><p align="center"><a 
                                                                                                                                            href="https://bloomex.ca/index.php?option=com_content&task=view&id=17&Itemid=47&link=foot">&nbsp;Term of Use</a></p></td><td 
                                                                                                                                    width="35%"><p align="center"><a 
                                                                                                                                            href="https://bloomex.ca/index.php?option=com_content&task=view&id=18&Itemid=48&link=foot">Privacy Policy</a></p></td><td 
                                                                                                                                    width="35%"><p align="center"><a href="https://bloomex.ca/index.php?option=com_joomap&Itemid=154&link=foot">Site 
                                                                                                                                            Map</a></p></td></tr></tbody></table><table class="htmtableborders" style="WIDTH: 90%" cellspacing="5" cellpadding="5" 
                                                                                                                                                                align="center" border="0"><tbody><tr><td><p align="center">Bloomex <?php echo $city; ?>'s fresh cut <a 
                                                                                                                                            href="https://bloomex.ca">flowers</a> are shipped directly from our distribution center for <a 
                                                                                                                                            href="https://bloomex.ca/florist/<?php echo $city; ?>/index.html"><?php echo $city; ?> flower delivery</a>. Order flowers 
                                                                                                                                        online 24 hours a day from an <i><?php echo $city; ?> florist</i>.&nbsp;If you&nbsp;would &nbsp;like a same day  delivery of 
                                                                                                                                        <i>flowers in <?php echo $city; ?></i>, please place an order before 1pm the day of delivery. </p>
                                                                                                                                    <p align="center">Bloomex has a wide selection of <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&root=7&category_id=9&option=com_virtuemart&Itemid=68&link=foot">birthday 
                                                                                                                                            flowers</a> and <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=8&option=com_virtuemart&Itemid=69&link=foot">anniversary 
                                                                                                                                            gifts</a>, as well as&nbsp;<a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=25&option=com_virtuemart&Itemid=80&link=foot">roses</a>, <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=23&option=com_virtuemart&Itemid=78&link=foot">lilies</a>, <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=30&option=com_virtuemart&Itemid=85&link=foot">tulips</a>, <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=31&option=com_virtuemart&Itemid=103&link=foot">plants</a>, 
                                                                                                                                        gourmet <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&root=43&category_id=45&option=com_virtuemart&Itemid=117&link=foot">gift 
                                                                                                                                            baskets</a>. </p><p align="center">Order <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=41&option=com_virtuemart&Itemid=113&link=foot">Thanksgiving 
                                                                                                                                            centerpieces</a>, <a 
                                                                                                                                            href="https://bloomex.ca/index.php?page=shop.browse&category_id=42&option=com_virtuemart&Itemid=114&link=foot">Christmas 
                                                                                                                                            Flowers</a>, Valentines Roses, Easter Flowers and Mother's Day gifts now and we'll ship them later for guaranteed holiday 
                                                                                                                                        delivery.</p></td></tr><tr><td><table style="WIDTH: 100%" border="0"><tbody><tr><td>&nbsp;</td>
                                                                                                                                                <td>&nbsp;</td></tr></tbody></table></td></tr></tbody></table>			
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </table>

                                                                                                        <!--FOOTER!-->


                                                                                                        <CENTER>
                                                                                                            <strong>Other Areas Served in Ottawa:<br />
                                                                                                            </strong> 
                                                                                                            <a href="https://bloomex.ca/florist/stittsville/index.html">Stittsville Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/peterborough/index.html">Peterborough Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/pembroke/index.html">Pembroke Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/ottawa/index.html">Ottawa Flowers</a> | <br/><a 
                                                                                                                href="https://bloomex.ca/florist/orleans/index.html">Orleans Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/nepean/index.html">Nepean Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/manotick/index.html">Manotick Flowers</a> | <a href="https://bloomex.ca/florist/<?php echo
                                                                                                                                                    $city;
                                                                                                                                                    ?>/index.html"><?php echo $city; ?> Flowers</a> | <br/><a href="https://bloomex.ca/florist/kanata/index.html">Kanata 
                                                                                                                Flowers</a> | <a href="https://bloomex.ca/florist/huntley/index.html">Huntley Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/hull/index.html">Hull Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/greely/index.html">Greely Flowers</a> | <br/><a 
                                                                                                                href="https://bloomex.ca/florist/gloucester/index.html">Gloucester Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/gatineau/index.html">Gatineau Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/cumberland/index.html">Cumberland Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/cornwall/index.html">Cornwall Flowers</a> | <br/><!-- next -->


                                                                                                            <br /><br />


                                                                                                            <strong>Major Canada Delivery Areas:</strong><br /> <a href="https://bloomex.ca/florist/toronto/index.html">Toronto 
                                                                                                                Flowers</a> | <a href="https://bloomex.ca/florist/ottawa/index.html">Ottawa Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/montreal/index.html">Montreal Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/calgary/index.html">Calgary Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/vancouver/index.html">Vancouver Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/halifax/index.html">Halifax Flowers</a> | <a 
                                                                                                                href="https://bloomex.ca/florist/winnipeg/index.html">Winnipeg Flowers</a>
                                                                                                        </CENTER><br/><br />
                                                                                                        <p class="MsoNormal" style="MARGIN: 0cm 0cm 0pt; TEXT-ALIGN: center" align="center"><strong><span 
                                                                                                                    style="FONT-SIZE: 7.5pt; COLOR: rgb(204,0,0); FONT-FAMILY: Verdana">Only one promotional offer per order.<br />Promotional 
                                                                                                                    offers cannot be combined.</span></strong><span style="FONT-SIZE: 7.5pt; COLOR: rgb(204,0,0); FONT-FAMILY: 
                                                                                                                                                            Verdana">&nbsp;</span></p><br /><br />

                                                                                                        <div align="center">&copy; 2010 Bloomex 
                                                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img height="28" 
                                                                                                                                                                                         src="https://bloomex.ca/images/stories/bloomex/logoBtm.gif" width="71" border="0" /></div>
                                                                                                        <br /><br /></TD></TR>
                                                                                            </TBODY></TABLE>

                                                                                        <div id="sidebar"><a href="https://mobile.bloomex.ca" onClick="javascript: 
    pageTracker._trackPageview('/mobile-site/');">&nbsp;</a></div>

                                                                                        </BODY>
                                                                                        </HTML>
