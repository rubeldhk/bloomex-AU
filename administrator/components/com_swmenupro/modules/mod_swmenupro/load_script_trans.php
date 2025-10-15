<?php
/**
* swmenupro v4.5
* http://swonline.biz
* Copyright 2006 Sean White
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

## Loads load_script function
load_scriptTrans();


/**---------------------------------------------------------------------**/
function load_scriptTrans() {
	global $mosConfig_live_site;

   	echo "<script type=\"text/javascript\" src=\"".$mosConfig_live_site."/modules/mod_swmenupro/transmenu_Packed.js\"></script>\n";
  // echo "<script type=\"text/javascript\" src=\"".$mosConfig_live_site."/modules/mod_swmenupro/transmenu.js\"></script>\n";

}
//---------------------------------------------------------------------

?>
