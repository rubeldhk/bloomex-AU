<?php 
/* ************************************************ 
* The Flash Mod 
* Version 2.0
* Copyright (C) 2006 by Michael Carico - All rights reserved
* Written by Michael Carico
* Released under GNU/GPL License - http://www.gnu.org/copyleft/gpl.htm
* Website http://www.kabam.net
************************************************ */
# Don't allow direct acces to the file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

#--------------------------------------
# Functions 
#--------------------------------------
if (!defined( '_FMinlineCode' )) {
  /** ensure that functions are declared only once */
  define( '_FMinlineCode', 1 );

  function FMinlineCode($fm_path, $fm_source, $fm_width, $fm_height, $fm_version, $fm_quality, $fm_wmode, $fm_loop, $fm_name) {

    echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"";
    echo " codebase=\"https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=".$fm_version."\""; 
    echo " width=\"".$fm_width."\""; 
    echo " height=\"".$fm_height."\"";
    if ($fm_name <> '') echo " id=\"".$fm_name."\"";
    echo ">";
    echo " <param name=\"movie\" value=\"".$fm_path.$fm_source."\"/>";
    echo " <param name=\"quality\" value=\"".$fm_quality."\" />";
    if ($fm_wmode <> 'window') echo " <param name=\"wmode\" value=\"".$fm_wmode."\" />";
    if ($fm_loop == 'false') echo " <param name=\"loop\" value=\"".$fm_loop."\" />";
    echo "<embed src=\"".$fm_path.$fm_source."\""; 
    echo " quality=\"".$fm_quality."\"";
    echo " pluginspage=\"http://www.macromedia.com/go/getflashplayer\""; 
    echo " type=\"application/x-shockwave-flash\"";
    echo " width=\"".$fm_width."\""; 
    echo " height=\"".$fm_height."\"";
    if ($fm_wmode <> 'window') echo " wmode=\"".$fm_wmode."\"";
    if ($fm_loop == 'false') echo " loop=\"".$fm_loop."\"";
    if ($fm_name <> '') echo " name=\"".$fm_name."\"";
    echo "></embed>";
    echo "</object>";
  } // end FMinlineCode

} // end check functions defined 
#--------------------------------------

#--------------------------------------
# Main Body 
#--------------------------------------
# Ensure access to core functions
global $mainframe;

# Paramaeters 
$fm_path    = $params->def('fm_path','images/flash/');
$fm_source  = $params->def('fm_source','');
$fm_width   = $params->def('fm_width','');
$fm_height  = $params->def('fm_height','');
$fm_version = $params->def('fm_version','6.0.0.0');
$fm_quality = $params->def('fm_quality','high');
$fm_wmode   = $params->def('fm_wmode','window');
$fm_loop    = $params->def('fm_loop','true');
$fm_name    = $params->def('fm_name','');
$fm_usejs   = $params->def('fm_usejs','yes');

# Display SWF
  if ($fm_usejs == 'yes') {
    if (!defined( '_FMjsInclude' )) {
      define( '_FMjsInclude', 1 );
      echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"modules/mod_flashmod.js\"></script>\n";
    }	  
    echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "AC_FL_RunContent(";
    echo "'codebase','https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=".$fm_version."'";
	echo ",'width','".$fm_width."'"; 
    echo ",'height','".$fm_height."'";
    if ($fm_name <> '') echo ",'id','".$fm_name."'";
    echo ",'src','".$fm_path.$fm_source."'";
	echo ",'quality','".$fm_quality."'";
    if ($fm_wmode <> 'window') echo ",'wmode','".$fm_wmode."'";
    if ($fm_loop == 'false') echo ",'loop','".$fm_loop."'";
    echo ",'pluginspage','https://www.macromedia.com/go/getflashplayer'";	
    echo ",'movie','".$fm_path.$fm_source."'";
    echo ");";
   echo "</script>\n";
    echo "<noscript>";
    FMinlineCode($fm_path, $fm_source, $fm_width, $fm_height, $fm_version, $fm_quality, $fm_wmode, $fm_loop, $fm_name);
    echo "</noscript>";
  } else {
    FMinlineCode($fm_path, $fm_source, $fm_width, $fm_height, $fm_version, $fm_quality, $fm_wmode, $fm_loop, $fm_name);
  }
?>