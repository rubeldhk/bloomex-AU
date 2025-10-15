<?php
/**
* @version $Id: install.letterman.php,v 1.3 2005/11/14 18:55:56 soeren Exp $
* @package Letterman
* @copyright (C) 2005 Soeren
* @license GNU / GPL
* @author soeren
* Letterman is Free Software
*/
function com_install(){
	global $database;

	// add new admin menu images
	$database->setQuery( "UPDATE `#__components` SET admin_menu_img = 'js/ThemeOffice/edit.png' WHERE admin_menu_link = 'option=com_letterman'");
	$database->query();
	
	$database->setQuery( "UPDATE `#__components` SET admin_menu_img = 'js/ThemeOffice/users.png' WHERE admin_menu_link = 'option=com_letterman&task=subscribers'");
	$database->query();
	
	$database->setQuery( "UPDATE `#__components` SET admin_menu_img = 'js/ThemeOffice/config.png' WHERE admin_menu_link = 'option=com_letterman&task=config'");
	$database->query();}
?>