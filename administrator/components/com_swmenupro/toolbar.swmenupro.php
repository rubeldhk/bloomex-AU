<?php
/**
* swmenupro v2.0
* http://swonline.biz
* Copyright 2004 Sean White
**/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ( $task ) {

        case "new":
			// menucontact::MODULE_EDIT_MENU();
			 break;
		
		case "edit":
			// menucontact::MODULE_EDIT_MENU();
			 break;
		
		case "imageEdit":
			// menucontact::IMAGE_EDIT_MENU();
			 break;

		case "moduleEdit":
			// menucontact::MODULE_EDIT_MENU();
			 break;
		
		case "editDhtmlMenu":
			// menucontact::DHTML_MENU();
			 break;
		case "editCSS":
			// menucontact::MANUAL_CSS();
			 break;

		case "showModules":
			// menucontact::MODULE_MENU();
			 break;
		case "dhtml":
			// menucontact::SHOW_DHTML_MENU();
			 break;
        
        default:
              //  menucontact::DEFAULT_MENU();
                break;
}
?>
