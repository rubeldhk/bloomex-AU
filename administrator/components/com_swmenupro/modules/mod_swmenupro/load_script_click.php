<?php
/**
* swmenupro v4.5
* http://swonline.biz
* Copyright 2006 Sean White
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

## Loads load_script function
load_scriptClick();


/**---------------------------------------------------------------------**/
function load_scriptClick() {
	global $mosConfig_live_site;
	echo "<script type=\"text/javascript\" src=\"".$mosConfig_live_site."/modules/mod_swmenupro/ClickShowHideMenu_Packed.js\"></script>";
	//echo "<script type=\"text/javascript\" src=\"".$mosConfig_live_site."/modules/mod_swmenupro/ClickShowHideMenu.js\"></script>";

}
//---------------------------------------------------------------------

?>
