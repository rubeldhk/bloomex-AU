<?php
/**
 * $Id: install.xmap.php 82 2008-02-03 23:14:53Z root $
 * $LastChangedDate: 2008-02-03 17:14:53 -0600 (dom, 03 feb 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// load language file
$pathLangFile	= $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_xmap/language/';
$tmp_lng 		= $GLOBALS['mosConfig_lang'];
if( isset( $GLOBALS['mosConfig_alang'] ) && !empty( $GLOBALS['mosConfig_alang'] )){
    if( file_exists( $pathLangFile . $GLOBALS['mosConfig_alang'] . '.php' )){
        $tmp_lng = $GLOBALS['mosConfig_alang'];
    }
}

if( file_exists( $pathLangFile . $tmp_lng . '.php' )){
    include_once( $pathLangFile . $tmp_lng . '.php' );
}else{
    $tmp_lng = 'english.php';
    echo 'Language file [ '. $GLOBALS['mosConfig_lang'] .' ] not found, using default language: english<br />';
    include_once( $pathLangFile . $tmp_lng );
}

function com_install() {
	global $mosConfig_absolute_path,$mosConfig_live_site;
	include( $mosConfig_absolute_path . '/administrator/components/com_xmap/classes/XmapConfig.php' );
	
	echo '<table>';
	echo '<tr><td><img src="',$mosConfig_live_site,'/administrator/components/com_xmap/images/logo.jpg" /></td>';
	echo '<td>';
	echo '<table class="adminlist" style="width:auto"><tr class="row0"><td>&rarr;</td><td>'."\n";
	
	XmapConfig::create();
	
	echo '</td></tr>'."\n";
	
	if( XmapConfig::restore() )
		echo '<tr class="row1"><td>&rarr;</td><td>'._XMAP_MSG_SET_RESTORED.'</td></tr>'."\n";
	
	echo "</table></td>\n";
	echo "</tr>";
	echo '<tr><td colspan="2"><h3 style="padding:0;margin:0">Xmap is a sitemap component for Joomla!</h3>
		Settings can be configured in the <a href="index2.php?option=com_xmap">&rarr; component menu</a>!<br />
		Author website: <a href="http://joomla.vargas.co.cr" target="_blank">joomla.vargas.co.cr</a><br />
		Based on <a href="http://www.ko-ca.com" target="_blank">Joomap</a> by  Daniel Grothe<br />
		<br /></td></tr>';
	echo "</table>";
}
