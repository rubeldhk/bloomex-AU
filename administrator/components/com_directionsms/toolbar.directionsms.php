<?php
defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('toolbar_html');

Switch ($task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'new':
    case 'edit':
        TOOLBAR_DIRECTIONSMS::_EDIT();
    break;

    default:
        TOOLBAR_DIRECTIONSMS::_DEFAULT();
    break;
}

?>
