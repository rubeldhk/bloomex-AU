<?php
/**
* swmenupro v5
* http://swonline.biz
* Copyright 2006 Sean White
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

## Loads load_script function
load_scriptSlideClick();


/**---------------------------------------------------------------------**/
function load_scriptSlideClick() {
	global $mosConfig_live_site;
	echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/prototype.lite.js"></script>';
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/moo.fx.js"></script>';
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/moo.fx.pack.js"></script>';
}
//---------------------------------------------------------------------

?>
