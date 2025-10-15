<?php
/**
* Letterman Newsletter Component
* 
* @package Letterman
* @author: Soeren
* @copyright Soeren Eberhardt <soeren@virtuemart.net>
    (who just needed an easy and *working* Newsletter component for Mambo 4.5.1 and mixed up Newsletter and YaNC)
* @copyright Mark Lindeman <mark@pictura-dp.nl> 
    (parts of the Newsletter component by Mark Lindeman; Pictura Database Publishing bv, Heiloo the Netherland)
* @copyright Adam van Dongen <adam@tim-online.nl>
    (parts of the YaNC component by Adam van Dongen, www.tim-online.nl)
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
*/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
global $mosConfig_lang, $mosConfig_absolute_path;
if( !@include_once( $mosConfig_absolute_path . "/administrator/components/com_letterman/language/$mosConfig_lang.messages.php" ) )
  include_once( $mosConfig_absolute_path . "/administrator/components/com_letterman/language/english.messages.php" );
  
require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ( $task ) {

	case "new":
	case "edit":
		MENU_letterman::EDIT_MENU();
		break;
	case 'compose':
		MENU_letterman::COMPOSE_MENU();
		break;
	case 'composeNow':
		MENU_letterman::CONFIRM_COMPOSE_MENU();
		break;
	case "sendNow":
		MENU_letterman::SEND_MENU();
		break;
	
	case "importSubscribers": 
		MENU_letterman::SUBSCRIBER_IMPORT_MENU();
		break;
	
	case "subscribers":
	case "deleteSubscriber":
		MENU_letterman::SUBSCRIBE_MENU();
		break;
	case "assignUsers":
	case "editSubscriber":
		MENU_letterman::SUBSCRIBER_EDIT_MENU();
		break;
	case 'config':
		MENU_letterman::CONFIG_MENU();
		break;
	default:
		MENU_letterman::DEFAULT_MENU();
		break;
}
?>
