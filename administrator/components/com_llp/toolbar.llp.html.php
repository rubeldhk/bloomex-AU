<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

Class TOOLBAR_LLP 
{
    
    function _DEFAULT() 
    {
        mosMenuBar::startTable();
        mosMenuBar::save();
        mosMenuBar::endTable();
    }
    
}

?>

