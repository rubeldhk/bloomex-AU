<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

require_once($mainframe->getPath('admin_html'));
global $mosConfig_absolute_path;

$task = mosGetParam($_REQUEST, 'task', '');
$id = mosGetParam($_REQUEST, 'id', '');

try {
    switch ($task) {
        case 'add':
            ExitPagePopUp::add();
            break;
        case 'delete':
            ExitPagePopUp::delete();
            break;
        case 'update':
            ExitPagePopUp::update($id);
            break;
        case 'check_image':
            $file = $mosConfig_absolute_path . '/images/exit_popup/' . $_POST['name'];
            if(file_exists($file)) {
                echo 'error';
            }
            break;
        default:
            ExitPagePopUp::create();
            break;
    }
} catch (\Exception $e) {
    echo 'Banners Exception: ' . $e->getMessage();
}