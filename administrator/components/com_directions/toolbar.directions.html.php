<?php

defined('_VALID_MOS') or die('Restricted access');

class TOOLBAR_FTDIMessages {
    static function _DEFAULT() {
        global $my;
        
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::customicon('get_unpublished', 'parse.png', 'parse.png', 'Unpublished Routes', false);
        mosMenuBar::spacer();
        mosMenuBar::customicon('get_published', 'parse.png', 'parse.png', 'Published Routes', false);
        mosMenuBar::spacer();
        //if ($my->usertype == 'Super Administrator') {
            mosMenuBar::unpublishList('');
            mosMenuBar::spacer();
        mosMenuBar::publishList('');
        mosMenuBar::spacer();
        //}
        mosMenuBar::endTable();
    }
    
    static function _EDIT() {
        mosMenuBar::startTable();
        mosMenuBar::customIconConfirm('remake', 'scanner_t.png', 'Remake', false, 'Unpublish Route and return orders to driver app?');
        mosMenuBar::spacer();
        mosMenuBar::customicon('get-pdf', 'getpdf.png', 'getpdf.png', 'PDF', false);
        mosMenuBar::spacer();
        mosMenuBar::save();
        mosMenuBar::spacer();
        mosMenuBar::cancel( 'cancel', 'Close' );
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }
}

?>
