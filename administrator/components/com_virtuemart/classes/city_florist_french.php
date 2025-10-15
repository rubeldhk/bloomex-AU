<?php

header('Content-Type: text/html; charset=ISO-8859-1');
echo "<?xml version=\"1.0\"?>";
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
$sql = "SELECT * FROM tbl_landing_pages WHERE url='" . $url . "' AND lang=2 ORDER BY id DESC LIMIT 1";


$result = mysql_query($sql, $conn);

if(mysql_num_rows($result)<1){

	mysql_free_result($result);
	mysql_close($conn);
	header('location: http://bloomex.ca');
	exit();

}

while ($row = mysql_fetch_assoc($result)) {
    // Varibles
	$city 	= $row['city'];
	$prov 	= $row['province'];
	$tele 	= $row['telephone'];
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

$landingpage = new Landingpages('fr',$conn);

$landingpage->init();


$sMetaSameDay	= '<title> Fleuriste [City] – Fleurs [City] | Livraison de fleur a [City] | Envoie de fleurs a [City] Québec  Canada</title>
					<meta name="description" content="50% Fleurs de [City] de Bloomex Canada – Commandez des fleurs en ligne de votre Fleuriste de [City]" />
					<meta name="keywords" content=" Fleuriste [City]  ais, fleurs [City], livraison de fleur à [City], envoyez des fleurs à [City], Magasin de fleurs à [City], fleurs fraîches à [City], Fleurs pour la fête des màres, Fleurs Valentin [City], fleurs de sympathie à [City],  fleurs d’amitié à [City], fleurs de remerciement [City] " />';

$sMetaNextDay	= '<title> Fleuriste [City] ais – Fleurs [City] | Livraison de fleurs à [City], envoyez des fleurs è [City] Québec  Canada</title> 
					<meta name="description" content=" Rabais de 50% sur des fleurs provenants de [City] de Bloomex Canada – Commandez des fleurs en ligne de votre fleuriste [City] ais dédié. Envoyez des fleurs à [City] – livraison la journée suivante" />
					<meta name="keywords" content=" Fleuriste [City] ais, Fleurs [City] aises, Livraison de fleurs è [City], envoyez des fleurs à [City], Magasin de fleurs à [City], Fleurs fraîches [City], Fleurs pour la fête des mères [City], Fleurs pour la Saint Valentin à [City], Fleurs de sympathie à [City],  Fleurs d’amitié à [City], Fleurs de remerciements à [City] " />';
			
$safari       	= strpos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
$chrome        = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;	

$sW4Lang	= "-fr";					
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
    <TD class="borderLeft" width="28" height="6"></TD>
    <TD colSpan=2></TD>
    <TD class="borderRight" width="26"></TD>
    </TR>
    <TR>
     <TD width="28" height="6" class="borderLeft"></TD>
               	<TD style="padding-left: 10px; vertical-align: middle; padding-top: 10px;" colspan="2"><A href="http://bloomex.ca/index.php?lang=en"><IMG 
src="http://bloomex.ca/templates/bloomex7/images/english.gif" border="0" width="40" height="11" alt=""></A></TD>
         	
     <TD width="26" class="borderRight"></TD>
  </TR>
  <TR>
    <TD class="borderLeft" width="28"></TD>
       <TD style="PADDING-LEFT: 10px; VERTICAL-ALIGN: middle" width="500">Commande facile pour <b>Fleuriste  <?php echo $city; ?></b> &agrave;:
		
               <IMG  style="VERTICAL-ALIGN: bottom" height="13" alt="" src="http://bloomex.ca/templates/bloomex7/images/phone.gif" width="13" 
border=0>&nbsp;<B>1-888-912-5666</B>
              	 
    </TD>
    <TD align="right">
      <TABLE cellSpacing="0" cellPadding="0" width="224" border="0" class="top-img-menu">
        <TBODY>
        <TR>
	<TD style="VERTICAL-ALIGN: bottom;">
	   <A href="index.php">
           	
               <IMG  alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/home_landing_fr.jpg"
border="0">
 </A>
 </TD>
          <TD style="VERTICAL-ALIGN: bottom;">
          <A href="http://bloomex.ca/index.php?lang=fr&option=com_user&task=UserDetails&Itemid=1">
                          <IMG height="20" alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/myAccount_fr.gif" width="101" border="0">
              	
           </A>
          </TD>
          <TD style="VERTICAL-ALIGN: bottom;">
          <A href="http://bloomex.ca/index.php?lang=fr&page=shop.cart&option=com_virtuemart&Itemid=80">
                            <IMG height="20" alt="" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/shoppingCart_fr.gif" border="0">
               	
          </A>
         </TD>
       </TR>
      </TBODY></TABLE>
    </TD>
    <TD class="borderRight" width="26"></TD>
   </TR>
   <TR>
      <TD class="borderLeft" width="28"></TD>
       <TD colSpan=2 width="731" height="193" align="left" valign="top" scope="col" style="background-position:8px 0;" background="<?php echo $mosConfig_live_site;?>/templates/bloomex7/images/bloomex_header_french_v3.jpg"  onclick="location.href='<?php echo $mosConfig_live_site;?>/index.php?land=fr'" class="center-top-banner">
		<div style="display:block;text-align:left;font-size:12px;width:155px;padding:0px 0px 0px  10px;margin:130px 0px 0px 0px;float:left;line-height:150%;">
	        <b>Fleurs <?php echo $city; ?></b><br>
	        <b style="display:block;"><font color="#EE1111">Composez!</font></b> <?php echo $tele ?>
		</div>
		<?php 
			//echo $mosConfig_absolute_path . "/city_img/$url.jpg";
			/*if( is_file($mosConfig_absolute_path . "/city_img/" . $url . ".jpg") ) {
				$sImgBanner	= $mosConfig_live_site . "/city_img/$url.jpg";
				echo '<IMG height="129" alt="" src="'.$sImgBanner.'"  border="0" style="float:right;margin:8px 8px 0px 0px;">';
			}else {
				if( strlen($city) >= 13 ) {
					$city	= "<br/>" . $city;
					$margin	= "margin:15px 5px 0px 0px;";
				}else {
					$margin	= "margin:25px 5px 0px 0px;";
				}
				echo "<div  style='display:block; float:right; height:130px; width:250px; margin:8px 8px 0px 0px; background:url($mosConfig_live_site/city_img/florist_default_image.jpg) top right no-repeat;'>
						<span style='$margin width:165px; height:40px; font:bold 14px Tahoma;  float:right; display:block; text-align:center; color:#9E2F42;'>Fleuriste $city</span>
					   <div>";
			}*/
		?>		
	  </TD>
    <TD class="borderRight" width="26"></TD>
   </TR>
</TBODY></TABLE>
<!-- Menu and Search -->
<TABLE  id="fullPage" class="fullPage" cellSpacing="0" cellPadding="0" width="790" align="center" border="0">
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
			global $Itemid, $mosConfig_live_site, $conn;
			
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
		
			
			
			if( $mitem->type != "separator" ) {
				$mitem->link	.= "&lang=fr";
				
				$sql 		= "SELECT `value` FROM jos_jf_content WHERE reference_table = 'menu' AND language_id = 2 AND reference_field = 'name' AND reference_id = ".$mitem->id;			
				$result 	= mysql_query($sql, $conn);
				
				if(mysql_num_rows($result)>0){	
					$row 	= mysql_fetch_row($result);
					if( !empty($row[0]) ) {
						$mitem->name	= ($row[0]);
					}
				}				
			}
			
			switch ($mitem->browserNav) {
				// cases are slightly different
				case 1:
					// open in a new window
					$txt = '<a href="'. $mitem->link .'" target="_blank" class="'. $menuclass .'" '. $id .'>'. $mitem->name .'</a>';
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
	mySlideData[countArticle++] = new Array('http://bloomex.ca/images/stories/headersfr/21.gif','#','','');
mySlideData[countArticle++] = new Array('http://bloomex.ca/images/stories/headersfr/12.gif','#','','');
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
<!--END TOP MENU & SEARCH--->

<!--Phone Number Place Holder -->



<!-----BODY ---->
<TABLE class="fullPage"  cellSpacing="0" cellPadding="0" width="790" align="center" border="0">
  <TBODY>
  <TR>
    <TD class="borderLeft" width="28"></TD>
    <TD style="BACKGROUND-IMAGE: url(http://bloomex.ca/templates/bloomex7/images/leftBg.gif); BACKGROUND-POSITION: 100% 50%; 	background-repeat: no-repeat; 
BACKGROUND-COLOR: rgb(216,190,232);" vAlign=top width="164">
        <!----  LEGT      ---->
   <TABLE cellSpacing="0" cellPadding="0" width="100%" border=0 >
        <TBODY>
         <TR>
          <TD align=center><br>
<link rel="stylesheet" type="text/css" media="all" href="http://bloomex.ca/components/com_instant_search/class/css/d4j_common_css.css" title="green" />

<script type="text/javascript">
	var mosConfig_live_site = "http://bloomex.ca";
</script>

<script type="text/javascript" src="http://bloomex.ca/components/com_instant_search/class/js/d4j_common_include.compact.js"></script>

<script type="text/javascript" src="http://bloomex.ca/components/com_instant_search/class/js/d4j_display_engine.compact.js"></script>

<script type="text/javascript" src="http://bloomex.ca/components/com_instant_search/class/js/d4j_ajax_engine.compact.js"></script>

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
var text = 'Rechercher...';
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
var _PROMPT_KEYWORD = 'Rechercher les mots-cls';
var _SEARCH_MATCHES = '%d rsultat(s)';
// current search #
var search_order = 0;
var display_order_id = 0;
</script>
<script type="text/javascript" src="http://bloomex.ca/components/com_instant_search/instant_search.compact.js"></script>
<!-- Initiate AJAX engine for instant search form /-->


<form action="http://bloomex.ca/index.php" method="post" name="instantSearchForm">
<div class="search">
<input alt="search" class="inputbox" type="text" name="searchword" size="15" value="Rechercher..."  onblur="if(this.value=='') this.value='Rechercher...';" 
onfocus="if(this.value=='Rechercher...') this.value='';" onkeyup="if (this.value.length < max_chars) { prepareSearch(this.value); } else { this.value = 
this.value.substring(0, max_chars - 1); }" /><input type="submit" value="ok" class="button"/><div id="instant_search_form" class="hiddenDiv" style="display: 
none;"></div>
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
			echo '<b>Livraison fleuriste &agrave; '.$city.'</b>';
			echo '<br />';
			if( !empty($tele) ) {
				echo $tele;
			}else {
				echo $location_telephone;	
			}	 
			 echo '</div>';			 
		// }

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
</table>			

</td>
		</tr>
		</table>
		<br/></TD>
         </TR>
         <TR>
         <TD>
    <IMG height=70 alt="" src="http://bloomex.ca/templates/bloomex7/images/three.jpg" width="164" border=0>
	         </TD>
         </TR>
         <TR>
         <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
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
			</td>
		</tr>
		</table>
		<br/></TD>
         </TR>
         <TR>
         <TD><IMG height=70 alt="" src="http://bloomex.ca/templates/bloomex7/images/four.jpg" width="164" border=0></TD>
         </TR>
         <TR>
         <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
					<tr>
				<th valign="top">
					Live Support via Skype				</th>
			</tr>
					<tr>
			<td>
				
<br />
<a href="skype:Bloomex1?call" onclick="return skypeCheck();">
<img border="0" src="https://bloomex.ca/modules/jomskype/call_blue.png" /></a><br />
<a href="skype:Bloomex1?chat" onclick="return skypeCheck();">
<img border="0" src="https://bloomex.ca/modules/jomskype/chat_blue.png" /></a>			</td>
		</tr>
		</table>
				<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<table width="100%">
        <tr>
            <td>
                <a class="mainlevel" href="http://bloomex.ca/index.php?lang=fr&page=shop.cart&option=com_virtuemart&Itemid=1">
                Voir le panier</a>
            </td>
        </tr>
        <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0"><tr><td><img src="http://bloomex.ca/modules/images/shop_cart.gif" width="32" 
height="32"></td><td valign="top"><p><b>Votre panier est actuellement vide.</b><p><font size=1><i>Si vous avez des difficults, SVP composez le 1-888-912-5666 pour 
commandez par tlphone</i></font></td></td></tr></table></td>
        </tr>
    </table>
			</td>
		</tr>
		</table>
		<br/></TD>
         </TR>
          
     </TBODY></TABLE>
<p>  
               <center><a href="mailto:avecsoins@bloomex.ca">AvecSoins@bloomex.ca</a></center>
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
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
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
				<tr><td>&nbsp;<a href="/index.php?page=shop.browse&amp;category_id=42&amp;option=com_virtuemart&amp;Itemid=114&amp;lang=fr"><img style="margin: 0px 0px 0px 1px" src="/templates/bloomex7/images/xmas_flowersFR.gif" border="0" alt=" "></a> </td><td>&nbsp;<a href="/index.php?page=shop.browse&amp;category_id=83&amp;option=com_virtuemart&amp;Itemid=73&amp;lang=fr"><img style="margin: 0px 0px 0px 1px" src="/templates/bloomex7/images/gift_basketsFR.gif" border="0" alt=" "></a> </td></tr>
				</tbody>
			</table>
                	
         		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<table cellspacing="0" cellpadding="0" width="100%" border="0"><tbody>

<tr><td colspan=3>&nbsp;</td></tr>		
<tr>
  
<?php
/*
<!--Product 1-->
*/

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=fr';
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
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=1&link=main&lang=fr">&nbsp;&nbsp;&nbsp;&nbsp;<img 
height="17" src="http://bloomex.ca/components/com_virtuemart/shop_image/ps_image/button_fr.gif" width="80" border="0" /></a><br 
/></td><td align="center" width="33%">


<!--Product 2 --><?php
$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=fr';
$landingpage->nextproduct()

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/><span 
style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>">
<img height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink ?>" width="170" border="0" /></a><br /><br />
<a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=47&link=main&lang=fr">&nbsp;&nbsp;&nbsp;&nbsp;
<img height="17" src="http://bloomex.ca/components/com_virtuemart/shop_image/ps_image/button_fr.gif" width="80" border="0" /></a><br 
/></td><td align="center" width="33%">

<!-- Product 3--><?php
$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=fr';
$landingpage->nextproduct();
?>

<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/><span 
style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br /><a title="<?php echo $title; ?>" 
href="http://bloomex.ca/index.php?page=shop.product_details&flypage=shop.flypage&product_id=<?php echo $productid; ?>&category_id=106&manufacturer_id=0&option=com_virtuemart&Itemid=155 
&link=main"><img height="200" alt="Delicate Delight" src="<?php echo $iLink; ?>" width="170" border="0" 
/></a><br /><br /><a title="Add to Cart: Delicate Delight" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=1&link=main&lang=fr"><img 
height="17" src="http://bloomex.ca/components/com_virtuemart/shop_image/ps_image/button_fr.gif" width="80" border="0" /></a><br 
/></td></tr><tr><td align="center"><p><span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: 
rgb(102,102,102)"></span></p><p>


<!---Product 4 --><?php

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=fr';
$landingpage->nextproduct();

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br />
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; COLOR: rgb(204,0,0);"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>"><img height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink; ?>" width="170"  border="0" /></a><br /><br /><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=47&link=main&lang=fr">&nbsp;&nbsp;&nbsp;&nbsp;<img 
height="17" src="http://bloomex.ca/components/com_virtuemart/shop_image/ps_image/button_fr.gif" width="80" border="0" /></a><br 
/></p></td><td align="center"><p><span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"></span>

<!-- Product 5 --><?php
$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=fr';
$landingpage->nextproduct();

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/>
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>"><img height="200" alt="<?php echo $title; ?>" src="<?php echo $iLink; ?>" width="170" border="0" /></a><br /><br 
/><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=1&link=main&lang=fr">&nbsp;&nbsp;&nbsp;&nbsp;<img 
height="17" src="http://bloomex.ca/components/com_virtuemart/shop_image/ps_image/button_fr.gif" width="80" border="0" /></a><br 
/></p></td><td align="center"><p><span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"></span><span 
style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"></span></p><p>

<!--Product 6 ---><?php

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=fr';
$landingpage->nextproduct();

?>
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $title; ?></span><br/>
<span style="FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: rgb(102,102,102)"><?php echo $sku; ?></span> 
<span style="FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: rgb(204,0,0)"><?php echo $price; ?></span><br />
<a title="<?php echo $title; ?>" href="<?php echo $pLink; ?>"><img height="200" alt="<?php echo $title; ?>" 
src="<?php echo $iLink; ?>" width="170" 
border="0" /></a><br /><br /><a title="Add to Cart: <?php echo $title; ?>" style="COLOR: rgb(255,255,255)" 
href="https://bloomex.ca/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $productid; ?>&option=com_virtuemart&Itemid=47&link=main&lang=fr"><img 
height="17" src="http://bloomex.ca/components/com_virtuemart/shop_image/ps_image/button_fr.gif" width="80" border="0" /></a><br 
/></td>

</tr><tr>
</tr>
  </table>
    			</td>
		</tr>
		</table>
		         <br/>
         <div align="center"></div>
                  <TABLE style="MARGIN-LEFT: 0px" cellSpacing=0 cellPadding=0 width="100%" border=0>
         <TBODY>
         <TR>
          <TD align=middle><IMG height=23 alt="" src="http://bloomex.ca/templates/bloomex7/images/ocassion.gif" width=186 border=0></TD>
                         <TD align=middle><IMG height=23 alt="" src="http://bloomex.ca/templates/bloomex7/images/flowergift_fr.gif" width=186 border=0></TD>
             	
                        <TD align=middle width=186><IMG height=23 alt="" src="http://bloomex.ca/templates/bloomex7/images/featured_fr.gif" width=186 border=0></TD>
             	
        </TR>
        <TR valign="top">
          <TD style="background: Rgb(239,237,237);">
          		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=47" class="mainlevel" 
>Anniversaire</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=9&amp;option=com_virtuemart&amp;Itemid=48" class="mainlevel" 
>Jour d'anniversaire</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;root=43&amp;category_id=44&amp;option=com_virtuemart&amp;Itemid=126" 
class="mainlevel" >Cadeaux d'affaires</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=10&amp;option=com_virtuemart&amp;Itemid=49" class="mainlevel" 
>Félicitations</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=11&amp;option=com_virtuemart&amp;Itemid=127" class="mainlevel" 
>Souhaits de guérison</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;root=43&amp;category_id=46&amp;option=com_virtuemart&amp;Itemid=128" 
class="mainlevel" >Cadeaux pour lui</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=12&amp;option=com_virtuemart&amp;Itemid=129" class="mainlevel" 
>Pour la maison</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=13&amp;option=com_virtuemart&amp;Itemid=130" class="mainlevel" 
>Juste parce que&#133;</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;root=43&amp;category_id=47&amp;option=com_virtuemart&amp;Itemid=131" 
class="mainlevel" >Cadeaux pour enfants</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=14&amp;option=com_virtuemart&amp;Itemid=132" class="mainlevel" 
>Amour</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=15&amp;option=com_virtuemart&amp;Itemid=133" class="mainlevel" 
>Naissance</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=37&amp;option=com_virtuemart&amp;Itemid=1" class="mainlevel" 
>Fête</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=135" class="mainlevel" 
>Sympathies</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=17&amp;option=com_virtuemart&amp;Itemid=136" class="mainlevel" 
>Remerciements</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;root=43&amp;category_id=49&amp;option=com_virtuemart&amp;Itemid=137" 
class="mainlevel" >Cadeaux de mariage</a></td></tr>
</table>			</td>
		</tr>
		</table>
		          </TD>
          <TD style="background: Rgb(239,237,237);">
          		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=19&amp;option=com_virtuemart&amp;Itemid=50" class="mainlevel" 
>Bouquets mélangés</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=64&amp;option=com_virtuemart&amp;Itemid=51" class="mainlevel" 
>Tous les arrangements</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=55&amp;option=com_virtuemart&amp;Itemid=52" class="mainlevel" 
>Les meilleurs vendeurs</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=32&amp;option=com_virtuemart&amp;Itemid=53" class="mainlevel" 
>Plantes fleurissantes</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php" class="mainlevel" >Pièces ma&Icirc;tresses</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=52&amp;option=com_virtuemart&amp;Itemid=138" class="mainlevel" 
>Chocolats &amp; Desserts</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=21&amp;option=com_virtuemart&amp;Itemid=139" class="mainlevel" 
>Marguerites</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=34&amp;option=com_virtuemart&amp;Itemid=140" class="mainlevel" 
>Jardin</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=53&amp;option=com_virtuemart&amp;Itemid=141" class="mainlevel" 
>Cadeaux gourmands</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=22&amp;option=com_virtuemart&amp;Itemid=142" class="mainlevel" 
>Iris</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=143" class="mainlevel" 
>Lis</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=60&amp;option=com_virtuemart&amp;Itemid=144" class="mainlevel" 
>Nouveaut&#233;s</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=24&amp;option=com_virtuemart&amp;Itemid=145" class="mainlevel" 
>Orchid&#233;es</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=35&amp;option=com_virtuemart&amp;Itemid=146" class="mainlevel" 
>Plantes et arbres en pot</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=148" class="mainlevel" 
>Roses</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=26&amp;option=com_virtuemart&amp;Itemid=149" class="mainlevel" 
>Douzaine de Roses</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=27&amp;option=com_virtuemart&amp;Itemid=150" class="mainlevel" 
>Deux Douzaines de Roses</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=62&amp;option=com_virtuemart&amp;Itemid=151" class="mainlevel" 
>En vente</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=28&amp;option=com_virtuemart&amp;Itemid=152" class="mainlevel" 
>Lis Stargazer</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=29&amp;option=com_virtuemart&amp;Itemid=153" class="mainlevel" 
>Tournesols</a></td></tr>
<tr align="left"><td><a href="http://bloomex.ca/index.php?lang=fr&page=shop.browse&amp;category_id=71&amp;option=com_virtuemart&amp;Itemid=124" class="mainlevel" 
>Fruit Frais</a></td></tr>
</table>			</td>
		</tr>
		</table>
		          </TD>
          <TD style="vertical-align:top;background-color:white;font-family:Verdana, arial, sans serif;font-size: 0.75em;color: rgb(81,81,81);width: 186px;padding: 
0px;padding-top: 10px;background-image: url(templates/bloomex7/images/featBottom.gif);background-repeat: no-repeat;background-position: bottom left;" width=186>
          		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="center" >
				<td width="100%"><?php

$title = $landingpage->productname();
$sku = $landingpage->productsku();
$iLink = 'http://bloomex.ca/components/com_virtuemart/shop_image/product/' . $landingpage->productimage();
$price = $landingpage->productprice();
$productid = $landingpage->productid();
$pLink = 'http://bloomex.ca/index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&category_id=22&product_id=' . $productid  . '&Itemid=47&lang=en';
$landingpage->close()

?>
					<span style="font-weight:bold;font-size: 11px; color:#666666;"><?php echo $title; ?></span><br>
<br /><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;"><?php echo $sku; ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;font-size: 11px; color:#CC0000;"><span style="font-weight:bold">
<?php echo $price; ?></span>
 </span>
<a title="<?php echo $title; ?>" 
href="<?php echo $pLink; ?>"><img 
src="<?php echo $iLink; ?>" width="170" height="200" alt="<?php echo $title; ?>" border="0" 
/></a><br />
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
				<ul id="mainlevel-nav"><li><a href="http://bloomex.ca/index.php?lang=fr&option=com_frontpage&amp;Itemid=1" class="mainlevel-nav" 
id="active_menu-nav">Page d'accueil</a></li><li><a href="http://bloomex.ca/index.php?lang=fr&option=com_contxtd&amp;task=blog&amp;Itemid=3" class="mainlevel-nav" 
>Contactez-nous</a></li><li><a href="http://bloomex.ca/index.php?lang=fr&option=com_search&amp;Itemid=5" class="mainlevel-nav" >Recherche</a></li><li><a 
href="http://bloomex.ca/index.php?lang=fr&option=com_login&amp;Itemid=36" class="mainlevel-nav" >Entrer</a></li></ul>			</td>
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
  				
<table cellspacing="10" cellpadding="10" border="0" align="center" style="width: 50%;" class="htmtableborders"><tbody><tr><td width="35%"><p align="center"><a 
href="http://bloomex.ca/index.php?lang=fr&option=com_content&task=view&id=17&Itemid=47" class="footer_menu"><b>&nbsp;Politique d'utilisation</b></a></p></td><td 
width="35%"><p align="center"><a href="http://bloomex.ca/index.php?lang=fr&option=com_content&task=view&id=18&Itemid=48" class="footer_menu"><b>Politique 
d'intimité</b></a></p></td><td width="35%"><p align="center"><a href="http://bloomex.ca/index.php?lang=fr&option=com_mambomap&Itemid=154" 
class="footer_menu"><b>Carte de site</b></a></p></td></tr></tbody></table>
<p align="center">

<?php 
$sContentSameDay	= 'Fleuriste de [City] vous offre une grande sélection de fleurs  fraiches coupées directement de notre boutique de fleurs de [City] pour une  livraison du jour même. Commandez vos fleurs en ligne 24 heurs sur 24. <br/><br/>
						Bloomex Fleuriste de [City] offre une grande sélection <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=247">de  bouquet d\'anniversaire</a>, <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=259">de  fleurs de sympathie</a> et des cadeaux anniversaires. Nous avons aussi des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=80">roses</a>,  des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=78">lis</a>,  des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=30&amp;option=com_virtuemart&amp;Itemid=80">tulipes</a>,  des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=31&amp;option=com_virtuemart&amp;Itemid=103">plantes  en pots</a> et des <a href="http://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=44&amp;Itemid=169">paniers  cadeaux</a>. <br /><br />
					  	Commandez des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=41&amp;option=com_virtuemart&amp;Itemid=80">centres  de tables pour l\'Action de Grâce</a>, des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=42&amp;option=com_virtuemart&amp;Itemid=80">fleurs  de Noël</a>, des <a href="http://bloomex.ca/index.php?page=shop.product_details&amp;category_id=106&amp;flypage=shop.flypage&amp;product_id=34&amp;option=com_virtuemart&amp;Itemid=1">roses  de St-Valentin</a>, des fleurs de Pâques et des fleurs pour la fête des mères  maintenant et nous garantirons la livraison pour les fêtes. ';
$sContentNextDay		= 'Fleuriste de [City] vous offre une grande sélection de fleurs  fraiches coupées directement de notre boutique de fleurs de [City] pour une  livraison le jour suivant. Commandez vos fleurs en ligne 24 heurs sur 24. <br/><br/>
						Bloomex Fleuriste de [City] offre une grande sélection <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=8&amp;option=com_virtuemart&amp;Itemid=247">de  bouquet d\'anniversaire</a>, <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=16&amp;option=com_virtuemart&amp;Itemid=259">de  fleurs de sympathie</a> et des cadeaux anniversaires. Nous avons aussi des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=25&amp;option=com_virtuemart&amp;Itemid=80">roses</a>,  des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=23&amp;option=com_virtuemart&amp;Itemid=78">lis</a>,  des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=30&amp;option=com_virtuemart&amp;Itemid=80">tulipes</a>,  des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=31&amp;option=com_virtuemart&amp;Itemid=103">plantes  en pots</a> et des <a href="http://bloomex.ca/index.php?option=com_content&amp;task=view&amp;id=44&amp;Itemid=169">paniers  cadeaux</a>. <br /> <br />
					  	Commandez des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=41&amp;option=com_virtuemart&amp;Itemid=80">centres  de tables pour l\'Action de Grâce</a>, des <a href="http://bloomex.ca/index.php?page=shop.browse&amp;category_id=42&amp;option=com_virtuemart&amp;Itemid=80">fleurs  de Noël</a>, des <a href="http://bloomex.ca/index.php?page=shop.product_details&amp;category_id=106&amp;flypage=shop.flypage&amp;product_id=34&amp;option=com_virtuemart&amp;Itemid=1">roses  de St-Valentin</a>, des fleurs de Pâques et des fleurs pour la fête des mères  maintenant et nous garantirons la livraison pour les fêtes. ';

if( $thesameday ) { 
	echo str_replace( array("[City]", "[Province]", "[Country]"),  array($city, $prov, "Canada"),  $sContentSameDay );
}else {
	echo str_replace( array("[City]", "[Province]", "[Country]"),  array($city, $prov, "Canada"),  $sContentNextDay );
}?>
</p><br />
<CENTER>
<strong>Nous livrons aux villes suivant prs de Quebec:<br />
</strong> <a href="http://bloomex.ca/fleuriste/<?php echo $prov; ?>/index.html">Fleurs 	Quebec</a> | <a 
href="http://bloomex.ca/fleuriste/sherbrooke/index.html">Fleurs Sherbrooke</a> | <a href="http://bloomex.ca/fleuriste/trois_rivieres/index.html">Fleurs 
Trois-Rivires</a> | <a 

href="http://bloomex.ca/fleuriste/acton_vale/index.html">Fleurs Acton Vale</a> | <br/><a 

href="http://bloomex.ca/fleuriste/alma/index.html">Fleurs Alma</a> 
| <a 

href="http://bloomex.ca/fleuriste/amos/index.html">Fleurs Amos</a> | <a href="http://bloomex.ca/fleuriste/ancienne_lorette/index.html">Fleurs Ancienne-Lorette 
</a> | 
<a href="http://bloomex.ca/fleuriste/baie_comeau/index.html">Fleurs Baie-Comeau</a> | <br/><a 

href="http://bloomex.ca/fleuriste/beauport/index.html">Fleurs Beauport</a> | <a 

href="http://bloomex.ca/fleuriste/becancour/index.html">Fleurs Bcancour</a>  | <a href="http://bloomex.ca/fleuriste/berthierville/index.html">Fleurs 
Berthierville</a> | <a href="http://bloomex.ca/fleuriste/bonaventure/index.html">Fleurs Bonaventure</a> | <br/><a 
href="http://bloomex.ca/fleuriste/buckingham/index.html">Fleurs Buckingham</a> | <a href="http://bloomex.ca/fleuriste/cap_rouge/index.html">Fleurs Cap-Rouge</a> | 
<a href="http://bloomex.ca/fleuriste/charlesbourg/index.html">Fleurs Charlesbourg</a> | <a href="http://bloomex.ca/fleuriste/chateauguay/index.html">Fleurs 
Chteauguay</a>  | <br/><a href="http://bloomex.ca/fleuriste/chicoutimi/index.html">Fleurs Chicoutimi</a> | <a 
href="http://bloomex.ca/fleuriste/cowansville/index.html">Fleurs Cowansville</a> | <a href="http://bloomex.ca/fleuriste/des_monts/index.html">Fleurs des Monts</a> |  
<a href="http://bloomex.ca/fleuriste/des_saules/index.html">Fleurs des Saules</a> | <a href="http://bloomex.ca/fleuriste/des_sentiers/index.html">Fleurs des 
Sentiers</a> | <br/><a href="http://bloomex.ca/fleuriste/dolbeau_mistassini/index.html">Fleurs Dolbeau-Mistassini </a> | <a 
href="http://bloomex.ca/fleuriste/drummondville/index.html">Fleurs Drummondville</a> | <a href="http://bloomex.ca/fleuriste/duberger/index.html">Fleurs Duberger</a> 
| <a href="http://bloomex.ca/fleuriste/granby/index.html">Fleurs Granby</a> | <br/> <a href="http://bloomex.ca/fleuriste/hull/index.html">Fleurs Hull</a> | <a 
href="http://bloomex.ca/fleuriste/joliette/index.html">Fleurs Joliette</a> | <a href="http://bloomex.ca/fleuriste/la_baie/index.html">Fleurs La Baie</a> | <a 
href="http://bloomex.ca/fleuriste/la_sarre/index.html">Fleurs La Sarre</a> | <a href="http://bloomex.ca/fleuriste/la_tuque/index.html">Fleurs La Tuque</a> | <br/><a 
href="http://bloomex.ca/fleuriste/lachenaie/index.html">Fleurs Lachenaie</a> | <a href="http://bloomex.ca/fleuriste/lachute/index.html">Fleurs Lachute</a> | <a 
href="http://bloomex.ca/fleuriste/lebourgneuf/index.html">Fleurs Lebourgneuf</a>  | <a href="http://bloomex.ca/fleuriste/les_coteaux/index.html">Fleurs Les 
Coteaux</a> | <br/> 
<a href="http://bloomex.ca/fleuriste/levis/index.html">Fleurs <?php echo $city; ?></a> | <a href="http://bloomex.ca/fleuriste/loretteville/index.html">Fleurs 
Loretteville</a> | <a 

href="http://bloomex.ca/fleuriste/magog/index.html">Fleurs Magog</a> | <a 

href="http://bloomex.ca/fleuriste/matane/index.html">Fleurs Matane</a> | <br/><a 

href="http://bloomex.ca/fleuriste/montcalm/index.html">Fleurs Montcalm</a>
| <a 

href="http://bloomex.ca/fleuriste/mont_laurier/index.html">Fleurs Mont-Laurier</a>
| <a 

href="http://bloomex.ca/fleuriste/montmagny/index.html">Fleurs Montmagny</a>

| <a 

href="http://bloomex.ca/fleuriste/neufchatel/index.html">Fleurs Neufchtel</a>
| <br/><a 

href="http://bloomex.ca/fleuriste/rimouski/index.html">Fleurs Rimouski</a>
| <a 

href="http://bloomex.ca/fleuriste/riviere_du_loup/index.html">Fleurs Rivire-du-Loup</a> | <a 

href="http://bloomex.ca/fleuriste/rouyn_noranda/index.html">Fleurs Rouyn-Noranda</a> | <a 

href="http://bloomex.ca/fleuriste/saguenay/index.html">Fleurs Saguenay</a>
| <br/><a 

href="http://bloomex.ca/fleuriste/saint_georges/index.html">Fleurs Saint-Georges</a> | <a 

href="http://bloomex.ca/fleuriste/saint_hyacinthe/index.html">Fleurs Saint-Hyacinthe</a> | <a 

href="http://bloomex.ca/fleuriste/saint_jean_baptiste/index.html">Fleurs Saint-Jean-Baptiste</a> | <a 

href="http://bloomex.ca/fleuriste/saint_jerome/index.html">Fleurs Saint-Jrme</a>
| <br/><a 

href="http://bloomex.ca/fleuriste/saint_louis/index.html">Fleurs Saint-Louis</a> | <a 

href="http://bloomex.ca/fleuriste/saint_malo/index.html">Fleurs Saint-Malo</a> | <a 

href="http://bloomex.ca/fleuriste/saint_roch/index.html">Fleurs Saint-Roch</a> | <a 

href="http://bloomex.ca/fleuriste/saint_sauveur/index.html">Fleurs Saint-Sauveur</a>
| <br/><a 

href="http://bloomex.ca/fleuriste/salaberry_valleyfield/index.html">Fleurs Salaberry Valleyfield</a> | <a 

href="http://bloomex.ca/fleuriste/sept_iles/index.html">Fleurs Sept-Iles</a> | <a 

href="http://bloomex.ca/fleuriste/shawinigan/index.html">Fleurs Shawinigan</a> | <a 

href="http://bloomex.ca/fleuriste/sorel_tracy/index.html">Fleurs Sorel-Tracy</a>
| <br/><a 

href="http://bloomex.ca/fleuriste/thetford_mines/index.html">Fleurs Thetford-Mines</a> | <a 

href="http://bloomex.ca/fleuriste/val_d_or/index.html">Fleurs Val d'Or</a> | <a 

href="http://bloomex.ca/fleuriste/vanier/index.html">Fleurs Vanier</a> | <a 

href="http://bloomex.ca/fleuriste/victoriaville/index.html">Fleurs Victoriaville</a>
| <br/><a 

href="http://bloomex.ca/fleuriste/ville_marie/index.html">Fleurs Ville-Marie</a> | <a 

href="http://bloomex.ca/fleuriste/villeneuve/index.html">Fleurs Villeneuve</a>


<br /><br />
			
			
<p align="center"><strong>Major Canada Delivery Areas:</strong><br />
    <a href="http://bloomex.ca/florist/toronto/index.html">Fleurs Toronto</a> | <a href="http://bloomex.ca/florist/ottawa">Fleurs Ottawa</a> | <a href="http://bloomex.ca/florist/montreal/index.html">Fleurs Montreal</a> | <a href="http://bloomex.ca/florist/calgary/index.html"> Fleurs Flowers</a> | <a href="http://bloomex.ca/florist/vancouver/index.html">Fleurs Vancouver</a> | <a href="http://bloomex.ca/florist/halifax/index.html">Fleurs Halifax</a> | <a href="http://bloomex.ca/florist/winnipeg">Fleurs Winnipeg</a> <br /><br />
  &copy; 2011 Bloomex - Order Flowers Quickly and Securely for Canada  Delivery :: Order from a Trusted Canadian Online Florist</p>
 </CENTER><br/><br/>
    </TD></TR>
  </TBODY></TABLE>
  
 	<!--LEFT Conner Tab-->
	<a href="/index.php?option=com_virtuemart&page=shop.browse&category_id=169&Itemid=1&lang=fr">
		<img border="0" style="bottom: 0pt;position: fixed;left: 0pt;z-index:10" src="/templates/bloomex7/images/Gourmet-Collection-corner-VD.png">
	</a>
	
	<!--RIGHT Conner Tab-->
	<a href="/index.php?option=com_virtuemart&page=shop.browse&category_id=109&Itemid=73&lang=fr">
		<img border="0" style="bottom: 0pt;position: fixed;right: 0pt;z-index:10" src="/templates/bloomex7/images/designer-collection-corner-VD.png">
	</a>
	
</BODY>
</HTML>
<?php
	mysql_close($conn);
?>