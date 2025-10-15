<?php
// So we share
require_once( 'configuration.php' );

// Lets connect
$conn = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}

// Which database?
if (!mysql_select_db($mosConfig_db, $conn)) {
    echo 'Could not select database';
    exit;
}

// Grab the request
$url = mysql_real_escape_string($_GET['city']);

// Grab the language
$lang = mysql_real_escape_string($_GET['lang']);

// Build query
$sql = "SELECT * FROM tbl_landing_pages WHERE url='" . $url . "' AND lang=1 ORDER BY id DESC LIMIT 1";


$result = mysql_query($sql, $conn);

if(mysql_num_rows($result)<1){

	mysql_free_result($result);
	mysql_close($conn);
	header('location: http://bloomex.ca');
	exit();

}

while ($row = mysql_fetch_assoc($result)) {
    // Varibles
	$city = $row['city'];
	$prov = $row['province'];
	$tele = $row['telephone'];
	$activate_loc = (int)$row['enable_location'];
	$location_address = $row['location_address'];
	$location_country = $row['location_country'];
	$location_postcode = $row['location_postcode'];
	$location_telephone = $row['location_telephone'];
}

$query_limittime = "SELECT options FROM tbl_options WHERE type='cut_off_time' ";
$result = mysql_query($query_limittime, $conn);

$thesameday	= 0;
while ($row = mysql_fetch_assoc($result)) {
	$sOptionParam		=$row['options'];
	$aOptionParam		= explode( "[--1--]", $sOptionParam );
	$nTimeLimit		 	= $aOptionParam[0]*60 + $aOptionParam[1];
	
	if( intval($aOptionParam[0]) >= 12 ) {               			
		$sTime = (intval($aOptionParam[0]) - 12).":".$aOptionParam[1]." PM";
	}else {
		$sTime = intval($aOptionParam[0]).":".$aOptionParam[1]." AM";
	}
	
	$nHourNow		 = intval(date('H',time()));
	$nMinuteNow		 = intval(date('i',time()));
	$nTimeNow		 = $nHourNow*60 + $nMinuteNow;
	//$nTimeNow		 = $nHourNow*60 + $nMinuteNow + ($mosConfig_offset*60*60);
	
	
	if( $nTimeNow >= $nTimeLimit ) {
		$thesameday = 0;
	}else {
		$thesameday = 1;
	}
	
	//echo $nTimeNow."===".$nHourNow."===".$nMinuteNow."===".$nTimeLimit."===".$thesameday;
}

mysql_free_result($result);

require_once('class.landingpage.php');

$landingpage = new Landingpages('en',$conn);

$landingpage->init();

// mysql_close($link);

$sMetaSameDay	= '<title>[City] Florist - [City] Flowers | [City] Flower Delivery | Send Flowers to [City] [Province] [Country]</title>
					<meta name="description" content="50% off [City] Flowers from Bloomex Canada - Order Flowers online from your dedicated [City] Florist. Send Flowers to [City] - Same Day Flower Delivery" />
					<meta name="keywords" content="[City] florist, [City] flowers, [City] flower delivery, send flowers to [City], [City] flower shop, [City] fresh flowers, [City] Mothers Day Flowers, [City] valentines flowers, [City] valentines day flowers, [City] sympathy flowers, [City] friendship flowers, [City] thank you flowers" />';

$sMetaNextDay	= '<title>[City] Florist - [City] Flowers | [City] Flower Delivery | Send Flowers to [City] [Province] [Country]</title>
					<meta name="description" content="50% off [City] Flowers from Bloomex Canada - Order Flowers online from your dedicated [City] Florist. Send Flowers to [City] - Next Day Flower Delivery" />
					<meta name="keywords" content="[City] florist, [City] flowers, [City] flower delivery, send flowers to [City], [City] flower shop, [City] fresh flowers, [City] Mothers Day Flowers, [City] valentines flowers, [City] valentines day flowers, [City] sympathy flowers, [City] friendship flowers, [City] thank you flowers" />';
					
$safari       	= strpos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
$chrome        = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;	

$sW4Lang	= "";	
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php if( $thesameday ) { 
	echo str_replace( array("[City]", "[Province]", "[Country]"),  array($city, $prov, "Canada"),  $sMetaSameDay );
}else {
	echo str_replace( array("[City]", "[Province]", "[Country]"),  array($city, $prov, "Canada"),  $sMetaNextDay );
}?>

<meta name="Generator" content="Joomla! - Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved." />
<meta name="robots" content="index, follow" />	
	<link rel="shortcut icon" href="https://bloomex.ca/images/bloomex.ico"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/css/landing_template.css" />
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/css/landing_ie8.css" />
	<![endif]-->
<?php
	if ($chrome ) {
		echo '<link rel="stylesheet" type="text/css" href="'.$GLOBALS['mosConfig_live_site'].'/templates/bloomex7/css/landing_chrome.css" />';
	}elseif ($safari ) {
		echo '<link rel="stylesheet" type="text/css" href="'.$GLOBALS['mosConfig_live_site'].'/templates/bloomex7/css/landing_safari.css" />';
	}
?>
<link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/modules/mod_123clickmenu/css/style.css" type="text/css" media="screen, projection"/>
<!--[if lte IE 7]>
        <link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_absolute_path; ?>/modules/mod_123clickmenu/css/ie.css" media="screen" />
<![endif]-->  
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_123clickmenu/js/hoverIntent.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_123clickmenu/js/jquery.dropdown.js"></script>


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
   	
    <TD style="PADDING-LEFT: 10px; VERTICAL-ALIGN: middle" width="500">Easy ordering for <b><?php echo $city; ?> Florist </b> at:&nbsp;&nbsp;&nbsp;    	
	
              
    <IMG  style="VERTICAL-ALIGN: bottom" height="13" alt="" src="http://bloomex.ca/templates/bloomex7/images/phone.gif" width="13" border=0>&nbsp;<B>1-888-912-5666</B>
              	 
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
          <A href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_user&task=UserDetails&Itemid=1&link=top">
           	
               <IMG height="20" alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/myAccount.gif" width="101" 
border="0">
              	
           </A>
          </TD>
          <TD style="VERTICAL-ALIGN: bottom;">
          <A href="<?php echo $mosConfig_live_site; ?>/index.php?page=shop.cart&option=com_virtuemart&Itemid=80&link=top">
            	
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
      <TD colSpan=2 width="731" height="193" align="left" valign="top" scope="col" style="background-position:8px 0;" background="<?php echo $mosConfig_live_site;?>/templates/bloomex7/images/bloomex_header_v3.jpg"  onclick="location.href='<?php echo $mosConfig_live_site;?>/index.php?page=shop.browse&category_id=73&option=com_virtuemart&Itemid=1&lang=en'" class="center-top-banner">
		<div style="display:block;text-align:left;font-size:12px;width:155px;padding:0px 0px 0px 10px;margin:130px 0px 0px 0px;float:left;line-height:150%;">
	        <b><?php echo $city; ?> Flowers</b><br>
	        <b style="display:block;"><font color="#EE1111">Call Now!</font></b> <?php echo $tele ?>
		</div>
		<?php 
			//echo $mosConfig_absolute_path . "/city_img/$url.jpg";
			/*if( is_file($mosConfig_absolute_path . "/city_img/$url.jpg") ) {
				$sImgBanner	= $mosConfig_live_site . "/city_img/$url.jpg";
				echo '<IMG height="129" alt="" src="'.$sImgBanner.'"  border="0" style="float:right;margin:8px 8px 0px 0px;">';
			}else {
				if( strlen($city) >= 15 ) {
					$city	= $city . "<br/>";
					$margin	= "margin:25px 25px 0px 0px;";
				}else {
					$margin	= "margin:35px 25px 0px 0px;";
				}
				echo "<div  style='display:none; float:right; height:192px; width:369px; margin:0px 4px 0px 0px; background:url($mosConfig_live_site/city_img/florist_default_image.jpg) top right no-repeat;'>
						<span style='$margin width:205px; height:40px; font:bold 16px Tahoma;text-transform:uppercase; float:right; display:block; text-align:center; color:#9E2F42;'>$city Florist</span>
					  <div>";
			}*/
		?>
		
	  </TD>
    <TD class="borderRight" width="26"></TD>
   </TR>
</TBODY></TABLE>
<!-----TOP MENU & SEARCH---->
<TABLE id="fullPage" class="fullPage" cellSpacing="0" cellPadding="0" width="790" align="center" border="0">
 <TBODY>

  <TR>
    <TD  class="borderLeft left-top-menu-space<?php echo $sW4Lang; ?>"></TD>
    <TD height="32" class="center-top-menu-space<?php echo $sW4Lang; ?>" style="BACKGROUND-COLOR: rgb(216,190,232);"  >
       <!--TOP MENU-->
   	<?		
		$params	= array();
		$params['menutype'] 			= 'Bloomex_top';
		$params['class_sfx'] 			= '';
		$params['menu_images'] 		= 0;
		$params['menu_images_align'] 	= 0;
		$params['expand_menu'] 		= 0;
		$params['activate_parent'] 		= 0;
		$params['indent_image'] 		= 0;
		$params['indent_image1'] 		= 'indent1.png';
		$params['indent_image2'] 		= 'indent2.png';
		$params['indent_image3'] 		= 'indent3.png';
		$params['indent_image4'] 		= 'indent4.png';
		$params['indent_image5'] 		= 'indent5.png';
		$params['indent_image6'] 		= 'indent.png';
		$params['spacer'] 				= '';
		$params['end_spacer'] 			= '';
		$params['full_active_id'] 		= 0;
		
		$sql = "SELECT * FROM jos_menu WHERE menutype = '". $params['menutype'] ."'  AND published = 1 AND parent = 0 ORDER BY ordering";
		$result = mysql_query($sql, $conn);	
		
		if( mysql_num_rows($result) > 0 ) {
		?>
		<ul class="dropdown">
			<?php 
				while ($row = mysql_fetch_object($result)) {
					$links = mosGetMenuLink2( $row, $params, null, $menuclass );
					
					$sql 		= "SELECT * FROM jos_menu WHERE menutype = '". $params['menutype'] ."' AND published = 1" . $and . "\n AND parent = ".$row->id." ORDER BY ordering";
					$result2 	= mysql_query($sql, $conn);	
			?>	
				<li>
					<?php echo $links; ?>
					
					<?php 
						if( mysql_num_rows($result2) > 0 ) {
					?>
				 		<ul class="sub_menu">
							<?php 
								while ($row2 = mysql_fetch_object($result2)) {
									$links2 = mosGetMenuLink2( $row2, $params, null, $menuclass );
									
									$sql = "SELECT * FROM jos_menu WHERE menutype = '". $params['menutype'] ."' AND published = 1" . $and . "\n AND parent = ".$row2->id." ORDER BY ordering";
									$result3 	= mysql_query($sql, $conn);	
							?>
						 			 <li>
										<?php echo $links2; ?>
										
										<?php 
											if( mysql_num_rows($result3) > 0 ) {
										?>
												<ul>
													<?php 
														while ($row3 = mysql_fetch_object($result3)) {
															$links3 = mosGetMenuLink2( $row3, $params, null, $menuclass );
															
													?>
								 							<li><?php echo $links3; ?></li>
													<?php 
														}
													?>
								 				</ul>
										<?php 
											}
										?>
									</li>
							<?php 
								}
							?>
				 		</ul>
					<?php 
						}
					?>
			 	</li>
			<?php		
				}
			?>	
		</ul>
		<?php
		}
		
		
		function mosGetMenuLink2( $mitem, $params, $open=null, $menuclass = "", $other_type	= 0 ) {
			global $Itemid, $mosConfig_live_site, $mainframe;
			
			$txt = '';
			
			switch ($mitem->type) {
				case 'separator':
				case 'component_item_link':
					$mitem->link = "";
					break;
					
				case 'url':
					if ( eregi( 'index.php\?', $mitem->link ) ) {
						if ( !eregi( 'Itemid=', $mitem->link ) ) {
							$mitem->link .= '&Itemid='. $mitem->id;
						}
					}
					break;
					
				case 'content_item_link':
				case 'content_typed':
					$temp = split('&task=view&id=', $mitem->link);
						
					if ( $mitem->type == 'content_typed' ) {
						$mitem->link .= '&Itemid='. $mitem->id;
					} else {
						$mitem->link .= '&Itemid='. $mitem->id;
					}
					break;
		
				default:
					$mitem->link .= '&Itemid='. $mitem->id;
					break;
			}
		
			// Active Menu highlighting		
			if ( $params['full_active_id'] ) {
				// support for `active_custom_menu` of 'Link - Component Item'	
				if ( $id == '' && $mitem->type == 'component_item_link' ) {
					parse_str( $mitem->link, $url );
				}
				
				// support for `active_custom_menu` of 'Link - Url' if link is relative
				if ( $id == '' && $mitem->type == 'url' && strpos( 'http', $mitem->link ) === false) {
					parse_str( $mitem->link, $url );
				}
			}
		
		
			switch ($mitem->browserNav) {
				// cases are slightly different
				case 1:
					// open in a new window
					$txt = '<a href="/'. $mitem->link .'" target="_blank" class="'. $menuclass .'" '. $id .'>'. $mitem->name .'</a>';
					break;
		
				case 2:
					// open in a popup window
					$txt = "<a href=\"#\" onclick=\"javascript: window.open('". $mitem->link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\" class=\"$menuclass\" ". $id .">". $mitem->name ."</a>\n";
					break;
		
				case 3:
					// don't link it
					if( $mitem->type == "separator" && $mitem->link == "" && $other_type == 1  ) {
						$txt = '<span class="mainlevel">'. $mitem->name .'</span>';
					}else  {
						$txt = '<a href="#" class="'. $menuclass .'" '. $id .' onclick="return false;">'. $mitem->name .'</a>';
					}					
					break;
		
				default:	
					// open in parent window
					$txt = '<a href="'. $mitem->link .'" class="'. $menuclass .'" '. $id .'>'. $mitem->name .'</a>';
					break;
			}
		
			return $txt;
		}
	?>
 	<!--END MENU-->
    </TD>
    <TD  height="32" class="center2-top-menu-space<?php echo $sW4Lang; ?>"  style="background-color: rgb(216,190,232);">
        
<!-- AJAX Header Rotator Module (v1.1) starts here -->
<script src="http://bloomex.ca/modules/mod_jw_ajaxhr/prototype.lite.js" type="text/javascript"></script>
<script src="http://bloomex.ca/modules/mod_jw_ajaxhr/moo.fx.js" type="text/javascript"></script>
<script src="http://bloomex.ca/modules/mod_jw_ajaxhr/moo.fx.pack.js" type="text/javascript"></script>
<script src="http://bloomex.ca/modules/mod_jw_ajaxhr/rotator.js" type="text/javascript"></script>
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
	mySlideData[countArticle++] = new Array(' http://bloomex.ca/images/stories/headers/contacts_new.gif','#','','');
mySlideData[countArticle++] = new Array(' http://bloomex.ca/images/stories/headers/same_day.gif','#','','');
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
    <TD class="borderRight2 right-top-menu-space<?php echo $sW4Lang; ?>" height="24"></TD>
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
href="/components/com_instant_search/class/css/d4j_common_css.css" title="green" />

<script type="text/javascript">
	var mosConfig_live_site = "http://bloomex.ca";
</script>

<script type="text/javascript" 
src="/components/com_instant_search/class/js/d4j_common_include.compact.js"></script>

<script type="text/javascript" 
src="/components/com_instant_search/class/js/d4j_display_engine.compact.js"></script>

<script type="text/javascript" 
src="/components/com_instant_search/class/js/d4j_ajax_engine.compact.js"></script>

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


<form action="http://bloomex.ca/index.php" method="post" name="instantSearchForm">
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
		// if($activate_loc == 1)	 {		 
			 //Location Based SEO			 
			 echo '<div id="addvloc" style="color:#000000;">';
			echo '<b>'.$city.' Flower Delivery</b>';
			echo '<br />';
			if( !empty($tele) ) {
				echo $tele;
			}else {
				echo $location_telephone;	
			}					 
			 echo '</div>';			 
		 //}
		 ?>
		<br />
		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
			
<!--LEFT MENU-->			
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php 
$params['menutype'] 	= 'left_menu_1';
$menuclass			= "mainlevel";
$sql = "SELECT * FROM jos_menu WHERE menutype = '". $params['menutype'] ."'  AND published = 1 AND parent = 0 ORDER BY ordering";
$result = mysql_query($sql, $conn);	

if( mysql_num_rows($result) > 0 ) {
	while ($row = mysql_fetch_object($result)) {
		$links = mosGetMenuLink2( $row, $params, null, $menuclass,  1 );
?>
		<tr align="left">
			<td><?php echo $links;?></td>
		</tr>
<?php		
	}
}

$params['menutype'] 	= 'left_menu_2';
$menuclass			= "mainlevel";
$sql = "SELECT * FROM jos_menu WHERE menutype = '". $params['menutype'] ."'  AND published = 1 AND parent = 0 ORDER BY ordering";
$result = mysql_query($sql, $conn);	

if( mysql_num_rows($result) > 0 ) {
	while ($row = mysql_fetch_object($result)) {
		$links = mosGetMenuLink2( $row, $params, null, $menuclass,  1 );
?>
		<tr align="left">
			<td><?php echo $links;?></td>
		</tr>
<?php		
	}
}
?>
</table>			</td>
		</tr>
		</table>
		<br/></TD>
         </TR>
         
         <TR>
         <TD><IMG height=70 alt="" src="http://bloomex.ca/templates/bloomex7/images/three.jpg" width="164" border=0>
         </TD>
         </TR>
         <TR>
         <TD>		
	 	<table cellpadding="0" cellspacing="0" class="moduletable">
			<?php
			$params['menutype'] 	= 'left_menu_3';
			$menuclass			= "mainlevel";
			$sql = "SELECT * FROM jos_menu WHERE menutype = '". $params['menutype'] ."'  AND published = 1 AND parent = 0 ORDER BY ordering";
			$result = mysql_query($sql, $conn);	
			
			if( mysql_num_rows($result) > 0 ) {
				while ($row = mysql_fetch_object($result)) {
					$links = mosGetMenuLink2( $row, $params, null, $menuclass,  1 );
			?>
					<tr align="left">
						<td><?php echo $links;?></td>
					</tr>
			<?php		
				}
			}
			?>
		</table>
		
		<br/></TD>
         </TR>
         <TR>
         <TD><IMG height=70 alt="" src="http://bloomex.ca/templates/bloomex7/images/four.jpg" width="164" border=0></TD>
         </TR>
         <TR>
         <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<table width="100%">
        <tr>
            <td>
                <a class="mainlevel" 
href="http://bloomex.ca/index.php?page=shop.cart&option=com_virtuemart&Itemid=1&link=side">
                Show Cart</a>
            </td>
        </tr>
        <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0"><tr><td><img 
src="http://bloomex.ca/modules/images/shop_cart.gif" width="32" height="32"></td><td valign="top"><p><b>Your Cart is 
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
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
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
			<table width="100%" cellspacing="5" cellpadding="0" border="0">
				<tbody>
				<tr>
					<td align="center">
						<a href="/index.php?page=shop.browse&category_id=73&option=com_virtuemart&Itemid=1&lang=en">
							<img border="0" alt=" " src="<?php echo $mosConfig_live_site;?>/templates/bloomex7/images/VD-Main-Banner.png" style="margin: 0px 0px 0px 0px">
						</a> 
					</td>
				</tr>
				</tbody>
			</table>
                		
         		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<table cellspacing="0" cellpadding="0" width="100%" border="0"><tbody>
<tr><td colspan=3>&nbsp;</td></tr>		
<tr>

<!--Product 1--><?php
/*
$title = "Shades of Pink";
$sku = 'LF22-10';

$iLink = '/components/com_virtuemart/shop_image/product/b0262e7d1731cb3fae334208052b26e7.jpg';
$price = '$19.99';
$productid = 61;
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=152&Itemid=47&lang=en';
*/

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->nextproduct()


?>


<td align="center" width="34%">
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br />
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><span 
style="FONT-WEIGHT: bold"> <?php echo $price; ?></span> </span><br /><a title="<?php echo $title; ?>" 
href="<?php echo $pLink; ?>"><img 
height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink; ?>" width="170" border="0" 
/></a><br /><br /><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255);" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=1&link=main">&nbsp;&nbsp;&nbsp;&nbsp;<img 
height="17" src="/components/com_virtuemart/shop_image/ps_image/button.gif" width="80" border="0" /></a><br 
/></td><td align="center" width="33%">


<!--Product 2 --><?php
/*
$title = "Twenty Tulip Bouquet";
$sku = 'LF19-30';
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=152&Itemid=47&lang=en';
$iLink = '/components/com_virtuemart/shop_image/product/a6a7a06616f56309e4172d85ff2bdc1d.jpg';
$price = '$34.95';
$productid = 458;
*/
$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->nextproduct()

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/><span 
style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>">
<img height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink ?>" width="170" border="0" /></a><br /><br />
<a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=47&link=main">&nbsp;&nbsp;&nbsp;&nbsp;
<img height="17" src="/components/com_virtuemart/shop_image/ps_image/button.gif" width="80" border="0" /></a><br 
/></td><td align="center" width="33%">

<!-- Product 3--><?php
$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->nextproduct();
?>

<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/><span 
style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br /><a title="<?php echo $title; ?>" 
href="http://bloomex.ca/index.php?page=shop.product_details&flypage=shop.flypage&product_id=<?php echo $productid; ?>&category_id=106&manufacturer_id=0&option=com_virtuemart&Itemid=155 
&link=main"><img height="200" alt="Delicate Delight" src="<?php echo $iLink; ?>" width="170" border="0" 
/></a><br /><br /><a title="Add to Cart: Delicate Delight" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=1&link=main"><img 
height="17" src="/components/com_virtuemart/shop_image/ps_image/button.gif" width="80" border="0" /></a><br 
/></td></tr><tr><td align="center"><p><span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: 
rgb(102,102,102)"></span></p><p>


<!---Product 4 --><?php
/*
$title = '12 Mixed Colour Long Stem Roses';
$sku = 'LF11-55';
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=106&product_id=30&Itemid=47&lang=en';
$iLink = '/components/com_virtuemart/shop_image/product/4e901a310250c2e2306ded0108ef264b.jpg';
$price = '$37.95';
$productid = 30;
*/

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->nextproduct();

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br />
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; COLOR: rgb(204,0,0);"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>"><img height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink; ?>" width="170"  border="0" /></a><br /><br /><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=47&link=main">&nbsp;&nbsp;&nbsp;&nbsp;<img 
height="17" src="/components/com_virtuemart/shop_image/ps_image/button.gif" width="80" border="0" /></a><br 
/></p></td><td align="center"><p><span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"></span>

<!-- Product 5 --><?php
/*
$title = 'Blooming Planter Basket';
$sku = 'LF54-33';
$iLink = '/components/com_virtuemart/shop_image/product/9d85f2526c7c1b373c41b923d766d60b.jpg';
$price = '$44.95';
$productid = 124;
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=106&product_id=' . $productid . '&Itemid=47&lang=en';
*/
$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->nextproduct();

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/>
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>"><img height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink; ?>" width="170" border="0" /></a><br /><br 
/><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=1&link=main">&nbsp;&nbsp;&nbsp;&nbsp;<img 
height="17" src="/components/com_virtuemart/shop_image/ps_image/button.gif" width="80" border="0" /></a><br 
/></p></td><td align="center"><p><span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"></span><span 
style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"></span></p><p>

<!--Product 6 ---><?php

/*
$title = 'Hugs and Kisses';
$sku = ' LF81-02';
$pLink = 'http://bloomex.ca/index.php?page=shop.product_details&category_id=68&flypage=shop.flypage&product_id=636&option=com_virtuemart&Itemid=1&lang=en';
$iLink = '/components/com_virtuemart/shop_image/product/b088ae60db19c8f25f1f31a5210b6bca.jpg';
$price = '$54.99';
$productid = 636;
*/

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->nextproduct();

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/>
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span> 
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>"><img height="200" alt="<?php echo $title; ?>" 
src="<?php echo $iLink; ?>" width="170" 
border="0" /></a><br /><br /><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=47&link=main"><img 
height="17" src="/components/com_virtuemart/shop_image/ps_image/button.gif" width="80" border="0" /></a><br /></td>

</tr>
<tr></tr></tbody></table>
    			</td>
		</tr>
		</table>
		         <br/>
         
                  <TABLE style="MARGIN-LEFT: 0px" cellSpacing=0 cellPadding=0 width="100%" border=0>
         <TBODY>
         <TR>
          <TD align=middle><IMG height=23 alt="" src="http://bloomex.ca/templates/bloomex7/images/ocassion.gif" width=186 
border=0></TD>
           	
              <TD align=middle><IMG height=23 alt="" src="http://bloomex.ca/templates/bloomex7/images/flowergift.gif" 
width=186 border=0></TD>
              	
           	
             <TD align=middle width=186><IMG height=23 alt="" src="http://bloomex.ca/templates/bloomex7/images/featured.gif" 
width=186 border=0></TD>
             	
        </TR>
        <TR valign="top">
          <TD style="background: Rgb(239,237,237);">
          		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=47&link=bot" 
class="mainlevel" >Anniversary</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=9&amp;option=com_virtuemart&amp;Itemid=48&link=bot" 
class="mainlevel" >Birthday</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=44&amp;option=com_virtuemart&amp;Itemid=126&link=bot" 
class="mainlevel" >Business Gifts</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=10&amp;option=com_virtuemart&amp;Itemid=49&link=bot" 
class="mainlevel" >Congratulations</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=11&amp;option=com_virtuemart&amp;Itemid=127&link=bot" 
class="mainlevel" >Get Well</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=46&amp;option=com_virtuemart&amp;Itemid=128&link=bot" 
class="mainlevel" >Gifts For Him</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=12&amp;option=com_virtuemart&amp;Itemid=129&link=bot" 
class="mainlevel" >Housewarming</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=13&amp;option=com_virtuemart&amp;Itemid=130&link=bot" 
class="mainlevel" >Just Because</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=47&amp;option=com_virtuemart&amp;Itemid=131&link=bot" 
class="mainlevel" >Kid's Gifts</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=14&amp;option=com_virtuemart&amp;Itemid=132&link=bot" 
class="mainlevel" >Love and Romance</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=15&amp;option=com_virtuemart&amp;Itemid=133&link=bot" 
class="mainlevel" >New Baby</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=37&amp;option=com_virtuemart&amp;Itemid=1&link=bot" 
class="mainlevel" >Holiday</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=135&link=bot" 
class="mainlevel" >Sympathy and Funeral</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=17&amp;option=com_virtuemart&amp;Itemid=136&link=bot" 
class="mainlevel" >Thank You</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;root=43&amp;category_id=49&amp;option=com_virtuemart&amp;Itemid=137&link=bot" 
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
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=19&amp;option=com_virtuemart&amp;Itemid=50&link=bot" 
class="mainlevel" >All Mixed Bouquets</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=64&amp;option=com_virtuemart&amp;Itemid=51&link=bot" 
class="mainlevel" >All Arrangements</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=55&amp;option=com_virtuemart&amp;Itemid=52&link=bot" 
class="mainlevel" >Best Sellers</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=32&amp;option=com_virtuemart&amp;Itemid=53&link=bot" 
class="mainlevel" >Blooming Plants</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=52&amp;option=com_virtuemart&amp;Itemid=138&link=bot" 
class="mainlevel" >Chocolates and Desserts</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=21&amp;option=com_virtuemart&amp;Itemid=139&link=bot" 
class="mainlevel" >Daisies</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=34&amp;option=com_virtuemart&amp;Itemid=140&link=bot" 
class="mainlevel" >Garden</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=53&amp;option=com_virtuemart&amp;Itemid=141&link=bot" 
class="mainlevel" >Gourmet Gifts</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=22&amp;option=com_virtuemart&amp;Itemid=142&link=bot" 
class="mainlevel" >Iris</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=143&link=bot" 
class="mainlevel" >Lilies</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=60&amp;option=com_virtuemart&amp;Itemid=144&link=bot" 
class="mainlevel" >New Arrivals</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=24&amp;option=com_virtuemart&amp;Itemid=145&link=bot" 
class="mainlevel" >Orchids</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=35&amp;option=com_virtuemart&amp;Itemid=146&link=bot" 
class="mainlevel" >Potted Plants and Trees</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=148&link=bot" 
class="mainlevel" >Roses</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=26&amp;option=com_virtuemart&amp;Itemid=149&link=bot" 
class="mainlevel" >One Dozen Roses</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=27&amp;option=com_virtuemart&amp;Itemid=150&link=bot" 
class="mainlevel" >Two Dozen Roses</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=62&amp;option=com_virtuemart&amp;Itemid=151&link=bot" 
class="mainlevel" >Special Savings</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=28&amp;option=com_virtuemart&amp;Itemid=152&link=bot" 
class="mainlevel" >Stargazer Lilies</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=29&amp;option=com_virtuemart&amp;Itemid=153&link=bot" 
class="mainlevel" >Sunflowers</a></td></tr>
<tr align="left"><td><a 
href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=71&amp;option=com_virtuemart&amp;Itemid=124&link=bot" 
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
/*
$title = 'Designers Collection I';
$sku = 'DC112-01';
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=66&product_id=903&Itemid=47';
$iLink = '/components/com_virtuemart/shop_image/product/74bb0c4b1201f0d5e662a2636c49b66c.jpg';
$price = '$25.00';
$productid = 903;
*/

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = '/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->close()


?>
					<span style="font-weight:bold;font-size: 11px; color:#666666;"><?php echo $title; ?></span><br>
<br /><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;"><?php echo $sku; ?></span>
<span style="font-weight:bold;font-size: 11px; color:#CC0000;"><span 
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
				  <li><a href="http://bloomex.ca?link=foot" class="mainlevel-nav" 
id="active_menu-nav">Bloomex Flowers</a></li>
				  <li><a 
href="http://bloomex.ca/index.php?option=com_contxtd&amp;task=blog&amp;Itemid=3&link=foot" class="mainlevel-nav" >Contact 
Us</a></li><li><a href="http://bloomex.ca/index.php?option=com_search&amp;Itemid=5&link=foot" class="mainlevel-nav" 
>Search</a></li><li><a href="http://bloomex.ca/index.php?option=com_login&amp;Itemid=36&link=foot" class="mainlevel-nav" 
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
href="http://bloomex.ca/index.php?option=com_content&task=view&id=17&Itemid=47&link=foot">&nbsp;Term of Use</a></p></td><td 
width="35%"><p align="center"><a 
href="http://bloomex.ca/index.php?option=com_content&task=view&id=18&Itemid=48&link=foot">Privacy Policy</a></p></td><td 
width="35%"><p align="center"><a href="http://bloomex.ca/index.php?option=com_joomap&Itemid=154&link=foot">Site 
Map</a></p></td></tr></tbody></table><table class="htmtableborders" style="WIDTH: 90%" cellspacing="5" cellpadding="5" 
align="center" border="0"><tbody><tr><td>
<p align="center">

<?php 
$sContentSameDay	= '[City] Florist offering a large selection Fresh Cut Flowers shipped directly from our [City] Flower Shop for Same Day [City] Flower Delivery. Order Flowers Online 24 hours a day. <br/><br/>
						Bloomex [City] offers a wide selection of&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;root=7&amp;category_id=9&amp;option=com_virtuemart&amp;Itemid=68&amp;link=foot">Birthday  Flowers</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=91&amp;link=side">Sympathy  Flowers</a>&nbsp;and&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=69&amp;link=foot">Anniversary  Gifts</a>, as well as&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=80&amp;link=foot">Roses</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=78&amp;link=foot">Lilies</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=30&amp;option=com_virtuemart&amp;Itemid=85&amp;link=foot">Tulips</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=31&amp;option=com_virtuemart&amp;Itemid=103">Potted  Plants</a>&nbsp;and&nbsp;<a href="http://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=44&amp;Itemid=169">Gift  Baskets</a>. <br /><br />
					  	Order&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=41&amp;option=com_virtuemart&amp;Itemid=113&amp;link=foot">Thanksgiving  Centerpieces</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=42&amp;option=com_virtuemart&amp;Itemid=114&amp;link=foot">Christmas  Flowers</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=73&amp;option=com_virtuemart&amp;Itemid=1&amp;vmcchk=1">Valentines  Roses</a>,&nbsp;<a href="http://bloomex.ca/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=74&amp;Itemid=1">Easter  Flowers</a>&nbsp;and&nbsp;<a href="http://bloomex.ca/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=76&amp;Itemid=1&amp;vmcchk=1">Mothers  Day Flowers</a>&nbsp;now and we\'ll  guarantee holiday delivery.';
$sContentNextDay		= '[City] Florist offering a large selection Fresh Cut Flowers shipped directly from our [City] Flower Shop for Next Day [City] Flower Delivery. Order Flowers Online 24 hours a day. <br/><br/>
						Bloomex [City] offers a wide selection of&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;root=7&amp;category_id=9&amp;option=com_virtuemart&amp;Itemid=68&amp;link=foot">Birthday  Flowers</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=91&amp;link=side">Sympathy  Flowers</a>&nbsp;and&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=69&amp;link=foot">Anniversary  Gifts</a>, as well as&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=80&amp;link=foot">Roses</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=78&amp;link=foot">Lilies</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=30&amp;option=com_virtuemart&amp;Itemid=85&amp;link=foot">Tulips</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=31&amp;option=com_virtuemart&amp;Itemid=103">Potted  Plants</a>&nbsp;and&nbsp;<a href="http://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=44&amp;Itemid=169">Gift  Baskets</a>. <br /> <br />
					  	Order&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=41&amp;option=com_virtuemart&amp;Itemid=113&amp;link=foot">Thanksgiving  Centerpieces</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=42&amp;option=com_virtuemart&amp;Itemid=114&amp;link=foot">Christmas  Flowers</a>,&nbsp;<a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=73&amp;option=com_virtuemart&amp;Itemid=1&amp;vmcchk=1">Valentines  Roses</a>,&nbsp;<a href="http://bloomex.ca/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=74&amp;Itemid=1">Easter  Flowers</a>&nbsp;and&nbsp;<a href="http://bloomex.ca/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=76&amp;Itemid=1&amp;vmcchk=1">Mothers  Day Flowers</a>&nbsp;now and we\'ll  guarantee holiday delivery.';

if( $thesameday ) { 
	echo str_replace( array("[City]", "[Province]", "[Country]"),  array($city, $prov, "Canada"),  $sContentSameDay );
}else {
	echo str_replace( array("[City]", "[Province]", "[Country]"),  array($city, $prov, "Canada"),  $sContentNextDay );
}?>
</p>

</td></tr><tr><td><table style="WIDTH: 100%" border="0"><tbody><tr><td>&nbsp;</td>
				        <td>&nbsp;</td></tr></tbody></table></td></tr></tbody></table>			
</td>
		</tr>
		</table>
 
<!--FOOTER!-->
    

<CENTER>
<strong>Other Areas Served in Ottawa:<br />
</strong> 
<a href="http://bloomex.ca/florist/stittsville/index.html">Stittsville Flowers</a> | <a 
href="http://bloomex.ca/florist/peterborough/index.html">Peterborough Flowers</a> | <a 
href="http://bloomex.ca/florist/pembroke/index.html">Pembroke Flowers</a> | <a 
href="http://bloomex.ca/florist/ottawa/index.html">Ottawa Flowers</a> | <br/><a 
href="http://bloomex.ca/florist/orleans/index.html">Orleans Flowers</a> | <a 
href="http://bloomex.ca/florist/nepean/index.html">Nepean Flowers</a> | <a 
href="http://bloomex.ca/florist/manotick/index.html">Manotick Flowers</a> | <a href="http://bloomex.ca/florist/<?php echo 
$city; ?>/index.html"><?php echo $city; ?> Flowers</a> | <br/><a href="http://bloomex.ca/florist/kanata/index.html">Kanata 
Flowers</a> | <a href="http://bloomex.ca/florist/huntley/index.html">Huntley Flowers</a> | <a 
href="http://bloomex.ca/florist/hull/index.html">Hull Flowers</a> | <a 
href="http://bloomex.ca/florist/greely/index.html">Greely Flowers</a> | <br/><a 
href="http://bloomex.ca/florist/gloucester/index.html">Gloucester Flowers</a> | <a 
href="http://bloomex.ca/florist/gatineau/index.html">Gatineau Flowers</a> | <a 
href="http://bloomex.ca/florist/cumberland/index.html">Cumberland Flowers</a> | <a 
href="http://bloomex.ca/florist/cornwall/index.html">Cornwall Flowers</a> | <br/><!-- next -->


<br /><br />
			
<p align="center"><strong>Major Canada Delivery Areas:</strong><br />
    <a href="http://bloomex.ca/florist/toronto/index.html">Toronto Flowers</a> | <a href="http://bloomex.ca/florist/ottawa">Ottawa Flowers</a> | <a href="http://bloomex.ca/florist/montreal/index.html">Montreal Flowers</a> | <a href="http://bloomex.ca/florist/calgary/index.html">Calgary Flowers</a> | <a href="http://bloomex.ca/florist/vancouver/index.html">Vancouver Flowers</a> | <a href="http://bloomex.ca/florist/halifax/index.html">Halifax Flowers</a> | <a href="http://bloomex.ca/florist/winnipeg">Winnipeg Flowers</a> <br />
    <br />
  &copy; 2011 Bloomex - Order Flowers Quickly and Securely for Canada  Delivery :: Order from a Trusted Canadian Online Florist</p>
</CENTER><br/>
			
			</TD></TR>
  </TBODY></TABLE>
  
<div id="sidebar"><a href="http://mobile.bloomex.ca" onClick="javascript: 
pageTracker._trackPageview('/mobile-site/');">&nbsp;</a></div>

	<!--LEFT Conner Tab-->
	<a href="/index.php?option=com_virtuemart&page=shop.browse&category_id=169&Itemid=1">
		<img border="0" style="bottom: 0pt;position: fixed;left: 0pt;z-index:10" src="/templates/bloomex7/images/Gourmet-Collection-corner-VD.png">
	</a>
	
	<!--RIGHT Conner Tab-->
	<a href="/index.php?option=com_virtuemart&page=shop.browse&category_id=109&Itemid=73">
		<img border="0" style="bottom: 0pt;position: fixed;right: 0pt;z-index:10" src="/templates/bloomex7/images/designer-collection-corner-VD.png">
	</a>
</BODY>
</HTML><?php

mysql_close($conn);

?>
