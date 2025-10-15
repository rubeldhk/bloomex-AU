<?php
/**
* swmenupro v4.5
* http://swonline.biz
* Copyright 2006 Sean White
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

## Loads load_script function
load_css_script();


/**---------------------------------------------------------------------**/

function load_css_script() {
	global $mosConfig_live_site;
	?>
	<script type="text/javascript">
	<!--
		function SWimportStyleSheet(shtName){
			// add style sheet via javascript
			var link = document.createElement( 'link' );
			link.setAttribute( 'href', shtName );
			link.setAttribute( 'type', 'text/css' );
			link.setAttribute( 'rel', 'stylesheet' );			
			var head = document.getElementsByTagName('head').item(0);
			head.appendChild(link);
		}
		-->
		</script>		
<?php
}
//---------------------------------------------------------------------

?>
