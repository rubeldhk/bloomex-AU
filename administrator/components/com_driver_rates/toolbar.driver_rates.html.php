<?php

defined('_VALID_MOS') or die('Restricted access');

Class TOOLBAR_ComDriverRates {

    static function _EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::save();
        mosMenuBar::spacer();
        mosMenuBar::cancel('cancel', 'Close');
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

    static function _PC_EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::save('postalcode_save');
        mosMenuBar::spacer();
        mosMenuBar::cancel('postalcode_cancel', 'Close');
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

    static function _DEFAULT() {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::deleteList('');
        mosMenuBar::spacer();
        mosMenuBar::editList('rate_edit');
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::spacer();
        /* mosMenuBar::cancel('cancel', 'Close');
          mosMenuBar::spacer(); */
        mosMenuBar::endTable();
    }

    static function _PC_DEFAULT() {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::deleteList('', '', 'postalcode_remove');
        mosMenuBar::spacer();
        mosMenuBar::editList('postalcode_edit');
        mosMenuBar::spacer();
        mosMenuBar::addNewX('postalcode_new');
        mosMenuBar::spacer();
        mosMenuBar::cancel('cancel', 'Close');
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

}
?>

