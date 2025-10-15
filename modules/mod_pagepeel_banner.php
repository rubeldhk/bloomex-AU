<?php 

// @version $Id: mod_pagepeel_banner.php 1.0
// Andy Sikumbang
// http://www.templateplazza.com
// @based on www.webpicasso.de pageear script and mod_banner.php

// @version $Id: mod_banners.php 6087 2006-12-24 18:59:57Z robs $
// @package Joomla
// @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
// @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php

defined( '_VALID_MOS' ) or die( 'Restricted access' ) ;
$clientids = $params->get( 'banner_cids', '5' );
// peel setting
$peelspeed = $params->get( 'peelspeed', '4' );
$peelmirror = $params->get( 'peelmirror', '1' );
$peelnomirrorclr = $params->get( 'peelnomirrorclr', 'FFFFFF' );
$peellinktarget = $params->get( 'peellinktarget', 'self' );
$peelautoopen = $params->get( 'peelautoopen', 'false' );
$peelcloseautoopen = $params->get( 'peelcloseautoopen', '5' );
$peeldirection = $params->get( 'peeldirection', 'rt' );
$alterlink = $params->get( 'alterlink', 'http://www.templateplazza.com' );
$alterimage = $params->get( 'alterimage', 'animated_ads.jpg' );
$peelsmallimage = $params->get( 'peelsmallimage', 'clickhere.jpg' );
$peelsmallwidth = $params->get( 'peelsmallwidth', '100' );
$peelbigwidth = $params->get( 'peelbigwidth', '500' );
$peelautoopenable = $params->get( 'peelautoopenable', 'disable' );
$peelautoopenbehaviour = $params->get( 'peelautoopenbehaviour', 'reload' );



$banner = null;

$where = '';
if ( $clientids != '' ) {
	$clientidsArray = explode( ',', $clientids );
	mosArrayToInts( $clientidsArray );
	$where = "\n AND ( cid=" . implode( " OR cid=", $clientidsArray ) . " )";
}

$query = "SELECT *"
. "\n FROM #__banner"
. "\n WHERE showBanner=1 "
. $where
;
$database->setQuery( $query );
$banners = $database->loadObjectList();
$numrows = count( $banners );

$bannum = 0;
if ($numrows > 1) {
    $numrows--;
	mt_srand( (double) microtime()*1000000 );
	$bannum = mt_rand( 0, $numrows );
}

if ($numrows){
	$banner = $banners[$bannum];

	$query = "UPDATE #__banner"
	. "\n SET impmade = impmade + 1"
	. "\n WHERE bid = " . (int) $banner->bid
	;
	$database->setQuery( $query );
	if(!$database->query()) {
		echo $database->stderr( true );
		return;
	}
	$banner->impmade++;

	if ($numrows > 0) {
		// Check if this impression is the last one and print the banner
		if ($banner->imptotal == $banner->impmade) {

			$query = "INSERT INTO #__bannerfinish ( cid, type, name, impressions, clicks, imageurl, datestart, dateend )"
			. "\n VALUES ( " . (int) $banner->cid . ", " . $database->Quote( $banner->type ) . ", "
			. $database->Quote( $banner->name ) . ", " . (int) $banner->impmade . ", " . (int) $banner->clicks
			. ", " . $database->Quote( $banner->imageurl ) . ", " . $database->Quote( $banner->date ) . ", 'now()' )"
			;
			$database->setQuery($query);
			if(!$database->query()) {
				die($database->stderr(true));
			}

			$query = "DELETE FROM #__banner"
			. "\n WHERE bid = " . (int) $banner->bid
			;
			$database->setQuery($query);
			if(!$database->query()) {
				die($database->stderr(true));
			}
		}

		if (trim( $banner->custombannercode )) {
			echo $banner->custombannercode;
		} else if (preg_match( "/(\.bmp|\.gif|\.jpg|\.jpeg|\.png\.swf$)$/i", $banner->imageurl )) {
			$imageurl 	= 'https://bloomex.ca/images/banners/'. $banner->imageurl;
			$link		= sefRelToAbs( 'index.php?option=com_banners&task=click&bid='. $banner->bid );

		} 
	}
} else {
	$imageurl 	= 'https://bloomex.ca/modules/pagepeel_banner/'."$alterimage".'';
	$link		= $alterlink;
}
?>

<script src="https://bloomex.ca/modules/pagepeel_banner/AC_OETags.js"language="javascript"></script>  
<script type="text/javascript">
/********************************************************************************************
* PageEar advertising CornerAd by Webpicasso Media
* Leave copyright notice.  
*
* @copyright www.webpicasso.de
* @author    christian harz <pagepeel-at-webpicasso.de>
*********************************************************************************************/
/*
 *  Konfiguration / Configuration
 */ 

//  URL to small image 
var pagearSmallImg = 'https://bloomex.ca/modules/pagepeel_banner/<?php echo $peelsmallimage; ?>'; 
// URL to small pageear swf
var pagearSmallSwf = 'https://bloomex.ca/modules/pagepeel_banner/pageear_s.swf'; 

// URL to big image
var pagearBigImg = '<?php echo $imageurl;?>'; 
// URL to big pageear swf
var pagearBigSwf = 'https://bloomex.ca/modules/pagepeel_banner/pageear_b.swf'; 

// Movement speed of small pageear 1-4 (2=Standard)
var speedSmall = <?php echo $peelspeed; ?>; 
// Mirror image ( true | false )
var mirror = '<?php if ($peelmirror == 0) { echo "false"; } elseif ($peelmirror == 1) { echo "true"; }  ?>'; 

// Color of pagecorner if mirror is false
var pageearColor = '<?php echo $peelnomirrorclr; ?>';  

// URL to open on pageear click
var jumpTo = '<?php echo $link; ?>' ;

// Browser target  (new) or self (self)
var openLink = '<?php echo $peellinktarget; ?>'; 

// Opens pageear automaticly (false:deactivated | 0.1 - X seconds to open) 
var openOnLoad = <?php echo $peelautoopen; ?>; 

// Second until pageear close after openOnLoad
var closeOnLoad = <?php echo $peelcloseautoopen; ?>; 

// Set direction of pageear in left or right top browser corner (lt: left | rt: right )
var setDirection = '<?php echo $peeldirection; ?>'; 

//add by remush
var autoopen = '<?php echo $peelautoopenable; ?>';
var behaviour = '<?php echo $peelautoopenbehaviour; ?>';

/*
 *  Do not change anything after this line
 */ 

// Flash check vars
var requiredMajorVersion = 6;
var requiredMinorVersion = 0;
var requiredRevision = 0;

// Copyright
var copyright = 'Webpicasso Media, www.webpicasso.de';

// Size small peel 
var thumbWidth  = <?php echo $peelsmallwidth; ?>;
var thumbHeight = <?php echo $peelsmallwidth; ?>;

// Size big peel
var bigWidth  = <?php echo $peelbigwidth; ?>;
var bigHeight = <?php echo $peelbigwidth; ?>;

// Css style default x-position
var xPos = 'right';

// GET - Params
var queryParams = 'pagearSmallImg='+escape(pagearSmallImg); 
queryParams += '&pagearBigImg='+escape(pagearBigImg); 
queryParams += '&pageearColor='+pageearColor; 
queryParams += '&jumpTo='+escape(jumpTo); 
queryParams += '&openLink='+escape(openLink); 
queryParams += '&mirror='+escape(mirror); 
queryParams += '&copyright='+escape(copyright); 
queryParams += '&speedSmall='+escape(speedSmall); 
queryParams += '&openOnLoad='+escape(openOnLoad); 
queryParams += '&closeOnLoad='+escape(closeOnLoad); 
queryParams += '&setDirection='+escape(setDirection); 





function openPeel(){
	//document.getElementById('teks').value = 'open'; 
	document.getElementById('bigDiv').style.top = '0px'; 
	document.getElementById('bigDiv').style[xPos] = '0px';
	document.getElementById('thumbDiv').style.top = '-1000px';
		
}

function closePeel(){
	//document.getElementById('teks').value = 'close'; 
	document.getElementById("thumbDiv").style.top = "0px";
	document.getElementById("bigDiv").style.top = "-1000px";
	//setTimeout("openPeel()",3000);
}

function autoPeel(){
	openPeel();
}

function writeObjects () { 
    
    // Get installed flashversion
    var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
    
    // Check direction 
    if(setDirection == 'lt') {
        xPosBig = 'left:-1000px';  
        xPos = 'left';      
    } else {
        xPosBig = 'right:1000px';
        xPos = 'right';              
    }
    
	
    // Write div layer for big swf
    document.write('<div id="bigDiv" style="position:absolute;width:'+ bigWidth +'px;height:'+ bigHeight +'px;z-index:9999;'+xPosBig+';top:-1000px;">');    	
    
    // Check if flash exists/ version matched
    if (hasReqestedVersion) {    	
    	AC_FL_RunContent(
    				"src", pagearBigSwf+'?'+ queryParams,
    				"width", bigWidth,
    				"height", bigHeight,
    				"align", "middle",
    				"id", "bigSwf",
    				"quality", "high",
    				"bgcolor", "#FFFFFF",
    				"name", "bigSwf",
    				"wmode", "transparent",
    				"allowScriptAccess","always",
    				"type", "application/x-shockwave-flash",
    				'codebase', 'https://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab',
    				"pluginspage", "https://www.adobe.com/go/getflashplayer"
    	);
    } else {  // otherwise do nothing or write message ...    	 
    	document.write('no flash installed');  // non-flash content
    } 
    // Close div layer for big swf
    document.write('</div>'); 
    
    // Write div layer for small swf
    document.write('<div id="thumbDiv" style="position:absolute;width:'+ thumbWidth +'px;height:'+ thumbHeight +'px;z-index:9999;'+xPos+':0px;top:0px;">');
    
    // Check if flash exists/ version matched
    if (hasReqestedVersion) {    	
    	AC_FL_RunContent(
    				"src", pagearSmallSwf+'?'+ queryParams,
    				"width", thumbWidth,
    				"height", thumbHeight,
    				"align", "middle",
    				"id", "bigSwf",
    				"quality", "high",
    				"bgcolor", "#FFFFFF",
    				"name", "bigSwf",
    				"wmode", "transparent",
    				"allowScriptAccess","always",
    				"type", "application/x-shockwave-flash",
    				'codebase', 'https://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab',
    				"pluginspage", "https://www.adobe.com/go/getflashplayer"
    	);
    } else {  // otherwise do nothing or write message ...    	 
    	document.write('no flash installed');  // non-flash content
    } 
    document.write('</div>');  
   
}


//added by REMUSH
//for Set cookies
function createCookie(name,value,hours) {
  if (hours) {
    var date = new Date();
    date.setTime(date.getTime()+(hours*60*60));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}
//end added by REMUSH

</script>
<script type="text/javascript">    
    writeObjects();	
	if(autoopen == "enable"){			
		if(behaviour == "once"){
			
			cookie = readCookie("auto_open_pagepeel");
			if (cookie != 1) {
				openPeel();	
				createCookie("auto_open_pagepeel", "1");
			} 
		}else{
			
			openPeel();	
		}
	}
</script>


