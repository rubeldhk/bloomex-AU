<?php

class TOOLBAR_extensions
{
    function _EDIT() {
        global $id;

        mosMenuBar::startTable();
        mosMenuBar::save();
        mosMenuBar::spacer();
        if ( $id ) {
            mosMenuBar::cancel( 'cancel', 'Close' );
        } else {
            mosMenuBar::cancel();
        }
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

    function _DEFAULT() 
    {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::trash();
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }
}
?>
