<?php

defined('_VALID_MOS') or die('Restricted access');

Class TOOLBAR_WarehouseOrderLimit {
    
    function _DEFAULT() {
        mosMenuBar::startTable();
        mosMenuBar::customY('update','reload_f2.png','Update');
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

    
}

?>

