<?php
/*
// The "AJAX Header Rotator" Module for Joomla 1.0.x Version 1.1
// License: http://www.gnu.org/copyleft/gpl.html
// Authors: Fotis Evangelou - George Chouliaras
// Copyright (c) 2006 JoomlaWorks.gr
// Project page at http://www.joomlaworks.gr
// Feel free to modify as you wish!
// ***Last update: September 23rd, 2006***
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_offset, $mosConfig_live_site, $mosConfig_locale, $mainframe, $iso_client_lang;
// module parameters
$uniqueid = trim( $params->get( 'uniqueid' ) );
$imagefolder = trim( $params->get( 'imagefolder' ) );
$delay = intval($params->get( 'delay', '6000' ) );
?>

<!-- AJAX Header Rotator Module (v1.1) starts here mf-->
<script src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/modules/mod_jw_ajaxhr/prototype.lite.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/modules/mod_jw_ajaxhr/moo.fx.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/modules/mod_jw_ajaxhr/moo.fx.pack.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/modules/mod_jw_ajaxhr/rotator.js" type="text/javascript"></script>
<style type="text/css" media="all">

<!--
#<?php echo $uniqueid; ?> {
    position: relative;
     width: 307px;
    height: 0px;
    margin-left:40px;  
    float: right;
}
#<?php echo $uniqueid; ?> a {cursor:default;}
#<?php echo $uniqueid; ?> img {border:0;margin:0;}
#rotator img{
  height: 46px;
}

#rotatorContainer{
    display: none;
    width: 306px;
    height: 46px;
    float: right;
    border: 1px solid #DAE0E5;
    border-right: none;
}

#rotatorContainerFull{
    position: absolute;
}
--> 
</style>
<div id="<?php echo $uniqueid; ?>"></div>
<script type="text/javascript">
	countArticle = 0;
	var mySlideData = new Array();
	<?php
	function listImages($dirname='.') {
      return glob($dirname .'*.{jpg,png,jpeg,gif}', GLOB_BRACE);
    }
	$catalog = listImages("./".$imagefolder."".$iso_client_lang."/");
	$total = count ($catalog);
	for ($i=0;$i<$total;$i++) {
	  $file = $catalog[$i];
	  if ($i==$total-1) {
	    print "mySlideData[countArticle++] = new Array('$file','#','','');\n";
	  } else {
	    print "mySlideData[countArticle++] = new Array('$file','#','','');\n";
	  }
	}
	?>
	var slideShowDelay = <?php echo $delay; ?>;				
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
	initSlideShow($('<?php echo $uniqueid; ?>'), mySlideData);
        $j('#rotatorContainerFull').css('width', parseInt($j('#new-content').css('width')));
        $j('#rotatorContainer').css('display', 'block');
	}
	addLoadEvent(startSlideshow);
</script>
<!-- AJAX Header Rotator Module (v1.1) ends here -->
