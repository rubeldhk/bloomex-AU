<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.2
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze. All rights reserved!
* @license http://www.konze.de/ Copyrighted Commercial Software
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class menuakoforms {
  function DETAILS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::deleteList( '\nThis will clear all selected results!', 'removedetails', 'Delete Entries');
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function DATA_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::publishList();
    mosMenuBar::unpublishList();
    mosMenuBar::divider();
    mosMenuBar::deleteList( '\nThis will clear all stored results!', 'removedata', 'Delete Entries');
    mosMenuBar::custom('exportdata','save.png','save_f2.png','CSV Export',true);
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function ABOUT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
   function LANG_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savefile', 'Save File' );
    mosMenuBar::cancel();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function EDIT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save();
    mosMenuBar::cancel();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function FORMS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::publishList();
    mosMenuBar::unpublishList();
    mosMenuBar::divider();
    mosMenuBar::addNew();
    mosMenuBar::editList();
    mosMenuBar::deleteList();
    mosMenuBar::divider();
    mosMenuBar::custom('addmenu','copy.png','copy_f2.png','Add to Mainmenu',true);
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function FIELDS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::publishList( 'publishfields' , 'Publish fields' );
    mosMenuBar::unpublishList( 'unpublishfields' , 'Unpublish fields' );
    mosMenuBar::divider();
    mosMenuBar::addNew( 'newfields' , 'New field' );
    mosMenuBar::editList( 'editfields' , 'Edit field' );
    mosMenuBar::deleteList( '' , 'removefields' , 'Delete fields' );
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function EDITFIELDS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savefields' , 'Save Field');
    mosMenuBar::cancel( 'fields' , 'Cancel' );
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  function CONFIG_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savesettings', 'Save Settings' );
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
}
?>