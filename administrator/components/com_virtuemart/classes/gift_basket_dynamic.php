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
    header('location: http://bloomex.ca');
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <title><?php echo $city; ?> Gift Baskets - <?php echo $city; ?> Fruit Baskets | <?php echo $city; ?> Gourmet Gift Baskets | <?php echo $city; ?> Holiday Gift Baskets | <?php echo $city; ?> Corporate Gift Baskets - Bloomex <?php echo $city; ?></title>
        <meta name="description" content="Buy <?php echo $city; ?> Gift Baskets - Save 70%! Bloomex delivers Gourmet Gift Baskets, Fruit Baskets and Corporate Gift Baskets. Order online or over the phone for <?php echo $city; ?> gift, fruit and Holiday Gift Basket delivery." />
        <meta name="keywords" content="<?php echo $city; ?> gift baskets, <?php echo $city; ?> fruit baskets, <?php echo $city; ?> chocolate basket, <?php echo $city; ?> corporate gift baskets, <?php echo $city; ?> corporate gift,  Bloomex gift basket, <?php echo $city; ?> coffee basket, <?php echo $city; ?> tea basket, <?php echo $city; ?> gourmet baskets, <?php echo $city; ?> food baskets, <?php echo $city; ?> gift basket delivery, <?php echo $city; ?> corporate gift basket, <?php echo $city; ?> candy gift basket, <?php echo $city; ?> cookie gift basket, <?php echo $city; ?> fruit basket delivery, <?php echo $city; ?> gifts, <?php echo $city; ?> gift hamper, <?php echo $city; ?> hamper" />

        <meta name="Generator" content="Joomla! - Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved." />
        <meta name="robots" content="index, follow" />
        <link rel="shortcut icon" href="http://bloomex.ca/images/bloomex.ico" />
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
                        <a href="http://bloomex.ca/index.php?lang=fr"><img border="0" width="40" height="11" alt="" src="http://bloomex.ca/templates/bloomex7/images/francaise.gif"></a>
                        <div class="delivery-intro">&nbsp;&nbsp;&nbsp;&nbsp;
                            FOR DELIVERIES OUTSIDE OF CANADA&nbsp;&nbsp;
                            <a href="http://bloomexusa.com/"><img border="0" align="absbottom" height="14" src="http://bloomex.ca/templates/bloomex7/images/flag_usa.png" style="margin-top: 3px;">&nbsp;USA</a>&nbsp;|&nbsp;
                            <a href="http://www.serenataflowers.com"><img border="0" align="absbottom" height="14" src="http://bloomex.ca/templates/bloomex7/images/flag_great_britain.png" style="margin-top: 3px;">&nbsp;UK&nbsp;</a>&nbsp;|&nbsp;
                            <a href="http://bloomex.com.au"><img border="0" align="absbottom" height="14" src="http://bloomex.ca/templates/bloomex7/images/flag_australia.png" style="margin-top: 3px;">&nbsp;AUS&nbsp;</a>
                        </div><br>
                    </td>

                    <TD width="26" class="borderRight"></TD>
                </TR>
                <TR>
                    <TD class="borderLeft" width="28"></TD>

                    <TD style="PADDING-LEFT: 10px; VERTICAL-ALIGN: middle" width="500">Easy ordering for <b><?php echo $city; ?> Flower 
                            Delivery</b> at:&nbsp;&nbsp;&nbsp;



                        <IMG  style="VERTICAL-ALIGN: bottom" height="13" alt="" src="http://bloomex.ca/templates/bloomex7/images/phone.gif" 
                              width="13" border=0>&nbsp;<B>1-888-912-5666</B>

                    </TD>
                    <TD align="right">
                        <TABLE cellSpacing="0" cellPadding="0" width="224" border="0" class="top-img-menu">
                            <TBODY>
                                <TR>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="http://bloomex.ca/">

                                            <IMG  alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/home_landing.jpg"
                                                  border="0">
                                        </A>
                                    </TD>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="http://bloomex.ca/index.php?option=com_user&task=UserDetails&Itemid=1&link=top">

                                            <IMG height="20" alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/myAccount.gif" width="101" 
                                                 border="0">

                                        </A>
                                    </TD>
                                    <TD style="VERTICAL-ALIGN: bottom;">
                                        <A href="http://bloomex.ca/index.php?page=shop.cart&option=com_virtuemart&Itemid=80&link=top">

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
                    <TD colSpan=2 width="731" height="193" align="left" valign="top" scope="col" background="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/gift_baskets_banner.jpg" onclick="location.href='<?php echo $mosConfig_live_site; ?>/index.php?page=shop.browse&category_id=83&option=com_virtuemart&Itemid=73'" style="cursor:pointer;background-repeat:no-repeat; background-position:5px 0px;">
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

                        <script type="text/javascript" src="http://bloomex.ca/modules/mod_swmenupro/transmenu_Packed.js"></script>

                        <!--swMenuPro5.6 transmenu by http://www.swmenupro.com-->

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
                                        <a id="menu77303" href="http://bloomex.ca/index.php?option=com_virtuemart&page=shop.browse&category_id=7&Itemid=1&link=down" 
                                           >Specials</a>
                                    </td> 
                                    <td> 
                                        <a id="menu77246" href=" http://bloomex.ca/index.php?page=shop.browse&category_id=25&option=com_virtuemart&Itemid=80 
                                           &link=down" > 