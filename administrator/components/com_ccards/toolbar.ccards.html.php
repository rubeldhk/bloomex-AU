<?php

class TOOLBAR_CCARDS {
    function _EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::save('save');
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
        mosMenuBar::publish('publish', 'Block');
        mosMenuBar::spacer();
        mosMenuBar::unpublish('unpublish', 'Unblock');
        mosMenuBar::spacer();
        mosMenuBar::editListX();
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }
}
?>
