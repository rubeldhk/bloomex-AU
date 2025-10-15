<?php

defined('_VALID_MOS') or die('Restricted access');

Class TOOLBAR_ComSmsTemplates {
    
   static function _EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::save();
        mosMenuBar::spacer();
        mosMenuBar::cancel( 'cancel', 'Close' );
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

   static function _DEFAULT() {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::deleteList('');
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }
    
}

?>

