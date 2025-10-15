<?php
/**
 * Enter description here...
 *
 * @package JL
 */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * @ignore 
 */
require_once( $mainframe->getPath( 'toolbar_html' ) );
/**
 * @ignore 
 */
require_once( $mainframe->getPath( 'toolbar_default' ) );

switch($act) 
{
	/* jlconf switch */
	case "jlconf":
		switch ($task){
			case 'saveedit':
				MENU_joomlalib::JLCONF_SAVEEDIT();
				break;
			
			default:
				MENU_joomlalib::JLCONF_DEFAULT();
				break;
		}
		break;
	
	/* jlapp switch */
	case "jlapp":
		switch ($task){
			case 'edit':
				MENU_joomlalib::JLAPP_EDIT();
				break;
			
			case 'view': 
			default:
				MENU_joomlalib::JLAPP_DEFAULT();
		}
		break;
	
	/* jllog */
	case 'jllog':
		switch($task){
			case 'maintain':
				MENU_joomlalib::JLLOG_MAINTAIN();
				break;
			
			default:
				MENU_joomlalib::JLLOG_DEFAULT();
		}
		break;
	
		default:
		MENU_joomlalib::DEFAULT_MENU();
}
?>