<?php
/* mod_ninja_secret_panel
* By Richie Mortimer and Daniel Chapman
* http://www.ninjoomla.com 
* Copyright (C) 2007 Richie Mortimer and Daniel Chapman, www.ninjoomla.com - Code so sharp, it hurts.
* email: ravenlife@raven-webdesign.com, daniel@ninjoomla.com 
* date: 2 September, 2007
* Release: 1.0
* PHP Code License : http://www.gnu.org/copyleft/gpl.html GNU/GPL 
* JavaScript Code & CSS  : http://creativecommons.org/licenses/by-nc-sa/3.0/
*
* Changelog
* 
* 1.0 September 11, 07 : 
*       Initial Version
* 
*/
###################################################################
//Ninja Secret Panel Module
//Copyright (C) 2007 Richie Mortimer and Daniel Chapman. Ninjoomla.com. All rights reserved.
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program; if not, write to the Free Software
//Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
###################################################################


  defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

  $inc_mootools = $params->get( 'inc_mootools','1' );
  $ninsecret_name = $params->get( 'ninsecret_name' );
  $ninsecret_text = $params->get( 'ninsecret_text' );
  $ninsecret_background = $params->get( 'ninsecret_background' );
  $ninsecret_width = $params->get( 'ninsecret_width' );
  $ninsecret_hover = $params->get( 'ninsecret_hover' );
  $ninsecret_boxtext = $params->get( 'ninsecret_boxtext' );
  $ninsecret_paneltop = $params->get( 'ninsecret_paneltop' );
  $ninsecret_panelleft = $params->get( 'ninsecret_panelleft' );
  $ninsecret_paneltopie6 = $params->get( 'ninsecret_paneltopie6' );
  $ninsecret_panelleftie6 = $params->get( 'ninsecret_panelleftie6' );
  $ninsecret_boxpos = $params->get( 'ninsecret_boxpos' );
  $inc_js = $params->get( 'inc_js' );
  $open_meth = $params->get( 'open_meth' );
  $close_meth = $params->get( 'close_meth' );
  $ninsecret_zindex = $params->get( 'ninsecret_zindex' );
  
  global $mosConfig_live_site;
  
  $headertag = '<link href="'.$mosConfig_live_site.'/modules/mod_ninjasecret/mod_ninjasecret.css" rel="stylesheet" type="text/css" />'."\n";
  
  //Because IE is such a hunk of crap we need to include special css and JS for IE6 
  //we use the $_SERVER['HTTP_USER_AGENT'] variable to access information about the browser being used to view the page
  $ua = $_SERVER['HTTP_USER_AGENT'];
    
	//look for MSIE
	if ((preg_match( "/MSIE/",  $ua))&&(preg_match( "/6/",  $ua))) {
			//IE6 is such a hunk of crap that it doesn't support CSS mouse hovers so we can't use the mouseout close
			$close_meth = 1;
			
			//if the ie6 margins are set use them isntead of the normal margins
			if ($ninsecret_paneltopie6){
				$ninsecret_paneltop = $ninsecret_paneltopie6;
			}
			
			if ($ninsecret_panelleftie6){
				$ninsecret_panelleft = $ninsecret_panelleftie6;
			}	 
	}

  	  
	if ($inc_js){
    
	if ($inc_mootools)
  $headertag .=  ' <script src="modules/mod_ninjasecret/mootools.v1.11.js" type="text/javascript" language="javascript"></script>'."\n";

  $headertag .=  '<script src="modules/mod_ninjasecret/mod_ninjasecret.js" type="text/javascript" language="javascript"></script>'."\n";
	}    

$headertag .= '<style type="text/css">'."\n";

$headertag .= '#ninsecret_panelwrap {
	width:'.$ninsecret_width.'px;
	margin-top:'.$ninsecret_paneltop.'px;
	margin-left:'.$ninsecret_panelleft.'px;
	z-index:'.$ninsecret_zindex.';
}'."\n";
$headertag .= 'div#ninsecret {
                	color:'.$ninsecret_text.';
                }'."\n";
$headertag .= '#ninsecret_panelwrap:hover div#ninsecret, #ninsecret_panelwrap.ninsecret_wraphover div#ninsecret {
                	color:'.$ninsecret_hover.';
                }'."\n";
$headertag .= '#ninsecret_panel {
                	background-color:'.$ninsecret_background.';
                	color:'.$ninsecret_boxtext.' !important;'."\n";
if ($ninsecret_boxpos) { 
$headertag .= 'left: -120px;'."\n";
} else { 
$headertag .= 'left: 10px;'."\n";
 } 
$headertag .='}
</style>
<script type="text/javascript" >
	var nscrt_open_meth ='.$open_meth.';
	var nscrt_close_meth ='.$close_meth.';
</script>'."\n";

$buffer = ob_get_contents();
$hdrPos = strpos ($buffer, '</head>');
$buffer = substr ($buffer, 0, $hdrPos) . "\n$headertag\n". substr($buffer, $hdrPos);
ob_clean();
echo $buffer;
	
?>

	<div id="ninsecret_panelwrap" class="use_css">
    	<div id="ninsecret"><?php echo $ninsecret_name?></div>
    	<div id="ninsecret_panel">
    	<div id="nscrt_close_div"></div>
    	<?php mosLoadModules('secret', -2); ?>
    	</div>
  </div>
    
