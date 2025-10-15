<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
/**
 * Installation routine for Joomap.
 * Creates the settings table in the Joomla! database
 * @author Daniel Grothe
 * @see JoomapConfig.php
 * @package Joomap_Admin
 */

// load language file
$pathLangFile	= $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/language/';
$tmp_lng 		= $GLOBALS['mosConfig_lang'];
if( isset( $GLOBALS['mosConfig_alang'] ) && !empty( $GLOBALS['mosConfig_alang'] )){
    if( file_exists( $pathLangFile . $GLOBALS['mosConfig_alang'] . '.php' )){
        $tmp_lng = $GLOBALS['mosConfig_alang'];
    }
}

if( file_exists( $pathLangFile . $tmp_lng . '.php' )){
    include_once( $pathLangFile . $tmp_lng . '.php' );
}else{
    if( file_exists( $pathLangFile . 'german.php' )){
        $tmp_lng = 'german.php';
        echo 'Sprachendatei [ '. $GLOBALS['mosConfig_lang'] .' ] nicht gefunden, verwende stattdessen [ Deutsch ]<br />';
    }else{
        $tmp_lng = 'english.php';
        echo 'Language file [ '. $GLOBALS['mosConfig_lang'] .' ] not found, using default language: english<br />';
    }
    include_once( $pathLangFile . $tmp_lng );
}

function com_install() {
	include( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/classes/JoomapConfig.php' );
	
	echo '<table class="adminlist" style="width:auto"><tr class="row0"><td>&rarr;</td><td>'."\n";
	
	JoomapConfig::create();
	
	echo '</td></tr>'."\n";
	
	if( JoomapConfig::restore() )
		echo '<tr class="row1"><td>&rarr;</td><td>'._JOOMAP_MSG_SET_RESTORED.'</td></tr>'."\n";
	
	echo "</table>\n";
}

?>
