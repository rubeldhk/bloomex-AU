<?php

$url_components = parse_url($_SERVER['REQUEST_URI']);
parse_str($url_components['query']??'', $params);
$_COOKIE['product_ordering'] = isset($_COOKIE['product_ordering'])?$_COOKIE['product_ordering']:((isset($params['sort']) && $params['sort']=='cheap')?'asc':((isset($params['sort']) && $params['sort']=='expensive')?'desc':''));

/**
 * @version $Id: index.php 4750 2006-08-25 01:08:30Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// Set flag that this is a parent file
include(__DIR__ . "/includes/krumo/class.krumo.php");
//debuggers
include(__DIR__ . "/includes/kint.phar");
include(__DIR__ . "/includes/kint_fleo.php");
define('TGN_IP', '95.174.110.97');
define('_VALID_MOS', 1);
if (isset($_REQUEST['dbgg'])) {
    if ($_REQUEST['dbgg']) {
        $expire = time() + 30 * 24 * 3600;
        setcookie("tgn_debug", "1", $expire,'/; SameSite=Strict',"",true,true);
        $_COOKIE['tgn_debug'] = true;
    } else {
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
    define('DB_DEBUG', false);
    krumo::disable();
    Kint::$enabled_mode = false;
}
if (isset($_REQUEST['fhid']) && isset($_REQUEST['pid']) && isset($_REQUEST['cobrand'])) {
    $pid = $_REQUEST['pid'];
    $fhid = $_REQUEST['fhid'];
    $cobrand = $_REQUEST['cobrand'];
    setcookie("funeral_FHID", $fhid, time() + 24 * 60 * 60);
    $_COOKIE['funeral_FHID'] = $fhid;
    setcookie("funeral_PID", $pid, time() + 24 * 60 * 60);
    $_COOKIE['funeral_PID'] = $pid;
    setcookie("funeral_COBRAND", $cobrand, time() + 24 * 60 * 60);
    $_COOKIE['funeral_COBRAND'] = $cobrand;
    if ($option != "com_virtuemart") {
        $redirect = "https://" . $_SERVER['HTTP_HOST'] . "/index.php?page=shop.browse&category_id=312&option=com_virtuemart&pid=" . $_REQUEST['pid'] . "&fhid=" . $_REQUEST['fhid'] . "&cobrand=" . $_REQUEST['cobrand'];
        header("Location: $redirect");
    }
}
# SEO#1181 - lowercase uri
# we don't want to lowercase the query
$uri = explode('?', $_SERVER['REQUEST_URI']);
if ($uri[0] != strtolower($uri[0])) {
    $query = isset($uri[1]) ? '?' . $uri[1] : '';
    $redirect = "//" . $_SERVER['HTTP_HOST'] . strtolower($uri[0]) . $query;
    http_response_code(301);
    header("Location: $redirect");
}
if ((substr($_SERVER['REQUEST_URI'], -1) != "/") && !(strpos($_SERVER['REQUEST_URI'], "?") | strpos($_SERVER['REQUEST_URI'], ".php"))) {
    http_response_code(301);
    header("Location: " . $_SERVER['REQUEST_URI'] . "/");
}
#parse queries into request arrays
if (isset($uri[1])) {
    parse_str($uri[1], $query);
    #sometimes query is not array - WAT
    if (is_array($query)) {
        array_merge($_REQUEST, $query);
        array_merge($_GET, $query);
    }
    $_SERVER['REQUEST_URI'] = $uri[0];
    $_SERVER['REQUEST_QUERY'] = $uri[1];
}


require( 'globals.php' );
require_once( 'configuration.php' );
require_once( 'includes/joomla.php' );
include_once $mosConfig_absolute_path.'/vendor/autoload.php';

$showOnlyJpegImageVersion = false;
if (isSafariBelow14()) {
    $showOnlyJpegImageVersion = true;
}


global $mosConfig_site_unavailable;

if ($mosConfig_site_unavailable) {

    require_once(__DIR__ . '/components/com_under_maintenance/index.php');

    exit();

}

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once $mosConfig_absolute_path . '/includes/router.php';
global $sef;
$sef = new newSef();
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $sef->run($_SERVER['REQUEST_URI']);
}

#force debug for production
if (isset($_REQUEST['dbgg']) and ( $_REQUEST['dbgg'] == 'qasdf') and $_SERVER['REMOTE_ADDR'] == TGN_IP) {
    $mosConfig_debug = 1;
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    ini_set('display_errors', 'On');
}

//START injection 
if (isset($mosConfig_live) AND $mosConfig_live == 1) {
    foreach ($_POST as $k => $v) {
        $request = $database->getEscaped(serialize($_REQUEST));

        if (preg_match('/(tbl_|base64|<script|jos_|$j|atob|onload)/si', $v)) {
            $query = "INSERT INTO `jos_vm_order_data` 
            (`post_get`, `uri`, `var_name`, `var_value`, `ip`, `x_forward`, `date`, `request`) 
            VALUES 
            ('post', '" . $database->getEscaped($_SERVER['REQUEST_URI']) . "', '" . $database->getEscaped($k) . "', '" . $database->getEscaped($v) . "', '" . $database->getEscaped($_SERVER['REMOTE_ADDR']) . "', '" . $database->getEscaped($_SERVER['HTTP_X_FORWARDED_FOR']) . "', NOW(), '" . $request . "')";
            $database->setQuery($query);
            $database->query();
        }
    }

    foreach ($_GET as $k => $v) {
        $request = $database->getEscaped(serialize($_REQUEST));
        if (preg_match('/(tbl_|base64|<script|jos_|$j|atob|onload)/si', $v)) {
            $query = "INSERT INTO `jos_vm_order_data` 
            (`post_get`, `uri`, `var_name`, `var_value`, `ip`, `x_forward`, `date`, `request`) 
            VALUES 
            ('get', '" . $database->getEscaped($_SERVER['REQUEST_URI']) . "', '" . $database->getEscaped($k) . "', '" . $database->getEscaped($v) . "', '" . $database->getEscaped($_SERVER['REMOTE_ADDR']) . "', '" . $database->getEscaped($_SERVER['HTTP_X_FORWARDED_FOR']) . "', NOW(), '" . $request . "')";
            $database->setQuery($query);
            $database->query();
        }
    }
}
//END injection 
global $city, $prov, $tele, $activate_loc, $location_address, $location_country, $location_postcode, $location_telephone, $category_id;
//Installation sub folder check, removed for work with SVN
if (file_exists('installation/index.php') && $_VERSION->SVN == 0) {
    define('_INSTALL_CHECK', 1);
    include ( $mosConfig_absolute_path . '/offline.php');
    exit();
}


// Grab the language

$lang = (isset($_GET['lang'])) ? $_GET['lang'] : '';


// displays offline/maintanance page or bar
if ($mosConfig_offline == 1) {
    require( $mosConfig_absolute_path . '/offline.php' );
}

// load system bot group
$_MAMBOTS->loadBotGroup('system');

// trigger the onStart events
$_MAMBOTS->trigger('onStart');

//NEW SEF

//require_once $mosConfig_absolute_path . '/includes/router.php';

//!NEW SEF

if (file_exists($mosConfig_absolute_path . '/components/com_sef/sef.php')) {
    require_once( $mosConfig_absolute_path . '/components/com_sef/sef.php' );
} else {
    require_once( $mosConfig_absolute_path . '/includes/sef.php' );
}

//    
//echo '<pre>AFTER SEF<br/>';
//    print_r($_GET);
//echo '</pre>'; 
//    
require_once 'start_access_log.php';

require_once( $mosConfig_absolute_path . '/includes/frontend.php' );

//echo '<pre>AFTER FRONTEND<br/>';
//    print_r($_GET);
//echo '</pre>'; 
// retrieve some expected url (or form) arguments
$option = strval(strtolower(mosGetParam($_REQUEST, 'option')));
$Itemid = intval(mosGetParam($_REQUEST, 'Itemid', null));

if ($option == '') {
    if ($Itemid) {
        $query = "SELECT id, link"
                . "\n FROM #__menu"
                . "\n WHERE menutype = 'mainmenu'"
                . "\n AND id = '$Itemid'"
                . "\n AND published = '1'"
        ;
        $database->setQuery($query);
    } else {
        $query = "SELECT id, link"
                . "\n FROM #__menu"
                . "\n WHERE menutype = 'mainmenu'"
                . "\n AND published = 1"
                . "\n ORDER BY parent, ordering"
        ;
        $database->setQuery($query, 0, 1);
    }
    $menu = new mosMenu($database);
    if ($database->loadObject($menu)) {
        $Itemid = $menu->id;
    }
    $link = $menu->link;
    if (($pos = strpos($link, '?')) !== false) {
        $link = substr($link, $pos + 1) . '&Itemid=' . $Itemid;
    }
    parse_str($link, $temp);
    /** this is a patch, need to rework when globals are handled better */
    foreach ($temp as $k => $v) {
        $GLOBALS[$k] = $v;
        $_REQUEST[$k] = $v;
        if ($k == 'option') {
            $option = $v;
        }
    }
}
if (!$Itemid) {
// when no Itemid give a default value
    $Itemid = 99999999;
}

// mainframe is an API workhorse, lots of 'core' interaction routines
$mainframe = new mosMainFrame($database, $option, '.');
$mainframe->initSession();
//NEW METATAGS
$sef_metatags = $sef->setMetaTags();
//!NEW METATAGS
// trigger the onAfterStart events
$_MAMBOTS->trigger('onAfterStart');

// checking if we can find the Itemid thru the content
if ($option == 'com_content' && $Itemid === 0) {
    $id = intval(mosGetParam($_REQUEST, 'id', 0));
    $Itemid = $mainframe->getItemid($id);
}

/** do we have a valid Itemid yet?? */
if ($Itemid === 0) {
    /** Nope, just use the homepage then. */
    $query = "SELECT id"
            . "\n FROM #__menu"
            . "\n WHERE menutype = 'mainmenu'"
            . "\n AND published = 1"
            . "\n ORDER BY parent, ordering"
    ;
    $database->setQuery($query, 0, 1);
    $Itemid = $database->loadResult();
}

// patch to lessen the impact on templates
if ($option == 'search') {
    $option = 'com_search';
}

// loads english language file by default
if ($mosConfig_lang == '') {
    $mosConfig_lang = 'english';
}
include_once( $mosConfig_absolute_path . '/language/' . $mosConfig_lang . '.php' );

// frontend login & logout controls
$return = strval(mosGetParam($_REQUEST, 'return', NULL));
$message = intval(mosGetParam($_POST, 'message', 0));
if ($option == 'login') {
    $mainframe->login();

    // JS Popup message
    if ($message) {
        ?>
        <script language="javascript" type="text/javascript">
            <!--//
        alert("<?php echo _LOGIN_SUCCESS; ?>");
            //-->
        </script>
        <?php
    }

    if ($return && !( strpos($return, 'com_registration') || strpos($return, 'com_login') )) {
        // checks for the presence of a return url 
        // and ensures that this url is not the registration or login pages
        mosRedirect($return);
    } else {
        mosRedirect($mosConfig_live_site . '/index.php');
    }
} else if ($option == 'logout') {
    $mainframe->logout("index " . $option);

    if ($return && !( strpos($return, 'com_registration') || strpos($return, 'com_login') )) {
        // checks for the presence of a return url 
        // and ensures that this url is not the registration or logout pages
        mosRedirect($return);
    } else {
        mosRedirect($mosConfig_live_site . '/index.php');
    }
}else if ($option == 'need_help') {
    $mainframe->need_help();
    mosRedirect($mosConfig_live_site . $return.'?mosmsgsuccess=true&mosmsg=Your request has been registered and forwarded to specialists.');
}

/** get the information about the current user from the sessions table */
$my = $mainframe->getUser();

// detect first visit
//$mainframe->detect();
// set for overlib check
$mainframe->set('loadOverlib', false);

$gid = intval($my->gid);

// gets template for page
$cur_template = $mainframe->getTemplate();
/** temp fix - this feature is currently disabled */
/** @global A places to store information from processing of the component */
$_MOS_OPTION = array();

// precapture the output of the component
require_once( $mosConfig_absolute_path . '/editor/editor.php' );

ob_start();


if ($path = $mainframe->getPath('front')) {
    $task = strval(mosGetParam($_REQUEST, 'task', ''));
    $ret = mosMenuCheck($Itemid, $option, $task, $gid);

    if ($ret) {
        require_once( $path );
    } else {
        mosNotAuth();
    }
} else {
    /*
      header('HTTP/1.0 404 Not Found');

      $result = file_get_contents("./404.txt");
      echo $result; */
}


$_MOS_OPTION['buffer'] = ob_get_contents();

ob_end_clean();

initGzip();

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// display the offline alert if an admin is logged in
if (defined('_ADMIN_OFFLINE')) {
    include( $mosConfig_absolute_path . '/offlinebar.php' );
}
//=========================================================
// Grab the request
//==========================================================
// loads template file
if (!file_exists($mosConfig_absolute_path . '/templates/' . $cur_template . '/index.php')) {
    echo _TEMPLATE_WARN . $cur_template;
} else {
    require_once( $mosConfig_absolute_path . '/templates/' . $cur_template . '/index.php' );
    echo '<!-- ' . time() . ' -->';
}

// displays queries performed for page
if ($mosConfig_debug) {
    echo $database->_ticker . ' queries executed';

    foreach ($database->_log as $k => $sql) {
        echo '<div class="col-xs-12">';
        echo $k + 1 . "<br/>" . $database->_microtimelog[$k] . "ms<br/>" . $sql;
        k($database->_backtracelog[$k]);
        echo "</div>";
    }
}
require_once 'end_access_log.php';
if (isset($database)) {
    $database->close();
}
if (DB_DEBUG) {
    f($database->getlog());
    f($database->getSlowlog());
    f($sef);
    f($_SESSION);
    f($_REQUEST);
}
doGzip();
