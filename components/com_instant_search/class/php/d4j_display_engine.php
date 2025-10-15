<?php

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

if (!defined('_D4J_DISPLAY_ENGINE_INCLUDED')) {
	define('_D4J_DISPLAY_ENGINE_INCLUDED', 1);

	function writePopupCode( $action, $id = '', $content = '' ) {
		echo '<div id="'.$action.($id != '' ? '_'.$id : '').'_form" class="hiddenDiv" style="display: none;">';
		echo "$content</div>\n";
	}
}
?>