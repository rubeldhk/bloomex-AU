<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('toolbar_html');

Switch ($task) 
{	
    case 'new':
    case 'edit':
        TOOLBAR_CorporateApp::_EDIT();
    break;

    default:
        TOOLBAR_CorporateApp::_DEFAULT();
    break;
}
?>
