<?php
defined('_VALID_MOS') or die('Restricted access');

class TOOLBAR_DIRECTIONSMS {
    function _EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::save();
        mosMenuBar::spacer();
        mosMenuBar::cancel();
        mosMenuBar::endTable();
    }
    
    function _DEFAULT() {
        mosMenuBar::startTable();
        mosMenuBar::publishList();
        mosMenuBar::spacer();
        mosMenuBar::unpublishList();
        mosMenuBar::spacer();
        mosMenuBar::deleteList();
        mosMenuBar::spacer();
        mosMenuBar::editListX();
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::endTable();
    }
}

?>