<?php
/**
 * @version $Id: index2.php 4750 2006-08-25 01:08:30Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
include(__DIR__ . "/../includes/kint.phar");
include(__DIR__ . "/../includes/kint_fleo.php");
if (isset($_REQUEST['debug'])) {
    if ($_REQUEST['debug']) {
        error_reporting(E_ALL);
        $expire = time() + 30 * 24 * 3600;
        setcookie("tgn_debug", "1", $expire, '/; SameSite=Strict',"",true,true);
        $_COOKIE['tgn_debug'] = true;
    } else {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        setcookie('tgn_debug', "0", time() - 3600, '/; SameSite=Strict',"",true,true);
        if (isset($_COOKIE['tgn_debug'])) {
            unset($_COOKIE['tgn_debug']);
        }
    }
}
if (isset($_COOKIE['tgn_debug'])) {
    define('DB_DEBUG', true);
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_startup_errors', 1);
} else {
    ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE);
    define('DB_DEBUG', false);
    Kint::$enabled_mode = false;
}
// Set flag that this is a parent file
define('_VALID_MOS', 1);
header("Content-type: text/html; charset=utf-8");
$aHost = explode(".", $_SERVER["HTTP_HOST"]);
if (strtolower($aHost[0]) == "www") {
    header("Location: https://bloomex.com.au" . $_SERVER["REQUEST_URI"]);
}



if (!file_exists('../configuration.php')) {
    header('Location: ../installation/index.php');
    exit();
}

require( '../globals.php' );
require_once( '../configuration.php' );
$mosConfig_user = $mosConfig_user_adm;
$mosConfig_password = $mosConfig_password_adm;
require_once( $mosConfig_absolute_path . '/includes/joomla.php' );
include_once( $mosConfig_absolute_path . '/language/' . $mosConfig_lang . '.php' );
require_once( $mosConfig_absolute_path . '/administrator/includes/admin.php' );
include_once $mosConfig_absolute_path.'/vendor/autoload.php';

// must start the session before we create the mainframe object
session_name(md5($mosConfig_live_site));
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 0,
    'cookie_secure' => true,
    'cookie_httponly' => true,
]);

$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
$task = strval(mosGetParam($_REQUEST, 'task', ''));
$mosmsg = strval(strip_tags(mosGetParam($_REQUEST, 'mosmsg', '')));
// mainframe is an API workhorse, lots of 'core' interaction routines
$mainframe = new mosMainFrame($database, $option, '..', true);

// admin session handling
$my = $mainframe->initSessionAdmin($option, $task);
/* area_name, departments_name */

$warehouse_only = false;
$query = "SELECT `warehouse_code` FROM `tbl_mix_user_group` AS `MUG`
INNER JOIN `tbl_new_user_group` AS `NUG` ON `NUG`.`id`=`MUG`.`user_group_id`
LEFT JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_code`=`NUG`.`departments_name`
WHERE `MUG`.`user_id`=" . $my->id . " AND `NUG`.`area_name` LIKE '%manage_warehouse_orders%' AND `w`.`warehouse_id` IS NOT NULL";
$database->setQuery($query);
$database->loadObject($warehouse_only);

if (!isset($my->prevs)) {
    $my->prevs = new stdClass();
}

if ($warehouse_only) {
    $my->prevs->warehouse_only = $warehouse_only->warehouse_code;
} else {
    $my->prevs->warehouse_only = false;
}

// initialise some common request directives
$act = strtolower(mosGetParam($_REQUEST, 'act', ''));
$section = mosGetParam($_REQUEST, 'section', '');
$no_html = intval(mosGetParam($_REQUEST, 'no_html', 0));
$id = intval(mosGetParam($_REQUEST, 'id', 0));

$cur_template = $mainframe->getTemplate();

// default admin homepage
if ($option == '') {
    $option = 'com_admin';
}

// set for overlib check
$mainframe->set('loadOverlib', false);

// precapture the output of the component
require_once( $mosConfig_absolute_path . '/editor/editor.php' );

ob_start();
if ($task == 'corporate-user' && $option == 'corporate') {
    $path = $mainframe->getPath('corporate');
    require_once ( $path );
} else if ($path = $mainframe->getPath('admin')) {
    require_once ( $path );
}

$_MOS_OPTION['buffer'] = ob_get_contents();
ob_end_clean();

initGzip();

// start the html output
if ($no_html == 0) {
    // loads template file
    if (!file_exists($mosConfig_absolute_path . '/administrator/templates/' . $cur_template . '/index.php')) {
        echo 'TEMPLATE ' . $cur_template . ' NOT FOUND';
    } else {
        require_once( $mosConfig_absolute_path . '/administrator/templates/' . $cur_template . '/index.php' );
    }
} else {
    mosMainBody_Admin();
}

// displays queries performed for page
if ($mosConfig_debug) {
    echo $database->_ticker . ' queries executed';
    echo '<pre>';
    foreach ($database->_log as $k => $sql) {
        echo $k + 1 . ' (' . $database->_log_time[$k] . ') ' . "\n" . $sql . '<hr />';
    }
}

doGzip();


// if task action is 'save' or 'apply' redo session check
if ($task == 'save' || $task == 'apply') {
    $mainframe->initSessionAdmin($option, '');
}
if (DB_DEBUG) {
    f($database->getlog());
    f($database->getSlowlog());
}
echo error_reporting();
f($_REQUEST);
