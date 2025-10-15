<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.2
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze. All rights reserved!
* @license http://www.konze.de/ Copyrighted Commercial Software
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mainframe->getPath( 'toolbar_html' ) );
require_once( $mainframe->getPath( 'toolbar_default' ) );

switch ($task) {
  case "data":
    menuakoforms::DATA_MENU();
    break;

  case "datadetails":
    menuakoforms::DETAILS_MENU();
    break;

  case "about":
    menuakoforms::ABOUT_MENU();
    break;

  case "language":
    menuakoforms::LANG_MENU();
    break;

  case "new":
  case "edit":
    menuakoforms::EDIT_MENU();
    break;

  case "settings":
    menuakoforms::CONFIG_MENU();
    break;

  case "fields":
    menuakoforms::FIELDS_MENU();
    break;

  case "newfields":
  case "editfields":
    menuakoforms::EDITFIELDS_MENU();
    break;

  default:
    menuakoforms::FORMS_MENU();
    break;
}
?>