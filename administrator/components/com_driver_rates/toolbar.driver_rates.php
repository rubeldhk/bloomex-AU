<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('toolbar_html');

Switch ($task) {	
    case 'postalcode_new':
    case 'postalcode_edit':
        TOOLBAR_ComDriverRates::_PC_EDIT();
    break;

    case 'new':
    case 'rate_edit':
        TOOLBAR_ComDriverRates::_EDIT();
    break;

    case 'postalcodes_list':
        TOOLBAR_ComDriverRates::_PC_DEFAULT();
    break;

    default:
        TOOLBAR_ComDriverRates::_DEFAULT();
    break;
}
?>
