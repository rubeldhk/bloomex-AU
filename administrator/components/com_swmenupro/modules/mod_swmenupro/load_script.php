<?php
/**
* swmenupro v4.5
* http://swonline.biz
* Copyright 2006 Sean White
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

## Loads load_script function
load_script();


/**---------------------------------------------------------------------**/

function load_script() {
	global $mosConfig_live_site;

echo "<script type = \"text/javaScript\" src=\"".$mosConfig_live_site."/modules/mod_swmenupro/menu_Packed.js\"></script>";
//echo "<script type = \"text/javaScript\" src=\"".$mosConfig_live_site."/modules/mod_swmenupro/menu.js\"></script>";

}
//---------------------------------------------------------------------

?>
