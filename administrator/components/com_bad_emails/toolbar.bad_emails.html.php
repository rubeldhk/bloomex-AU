<?php

defined('_VALID_MOS') or die('Restricted access');

Class TOOLBAR_ComBadEmails {
    
    function _EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::save();
        mosMenuBar::spacer();
        mosMenuBar::cancel( 'cancel', 'Close' );
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

    function _DEFAULT() {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::deleteList();
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }
    
}

?>

