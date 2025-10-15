<?php

/**
* @version 1.0
* @package Custom QuickIcons
* @copyright (C) 2005 Halil Köklü <halilkoklu at gmail dot com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'toolbar_html' ) );

$task = mosGetParam( $_REQUEST, 'task', '');

global $mosConfig_lang;

// Load Language File
if (file_exists('components/com_customquickicons/lang/' . $mosConfig_lang . '.php'))
  include_once('components/com_customquickicons/lang/' . $mosConfig_lang . '.php');
else
  include_once('components/com_customquickicons/lang/english.php');

switch($task) {
	case 'new':
	case 'edit':
		QI_Toolbar::_edit();
		break;
	default:
		QI_Toolbar::_show();
		break;
}

?>