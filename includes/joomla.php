<?php
/**
 * @version $Id: joomla.php 4801 2006-08-28 16:10:28Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');
define('_MOS_MAMBO_INCLUDED', 1);


//create alerts reporting here 
require_once ('alerts.class.php');
global $alerts;
$alerts = new joomAlert();

/**
 * Page generation time
 * @package Joomla
 */
class mosProfiler {

    /** @var int Start time stamp */
    var $start = 0;

    /** @var string A prefix for mark messages */
    var $prefix = '';

    /**
     * Constructor
     * @param string A prefix for mark messages
     */
    function __construct($prefix = '') {
        $this->start = $this->getmicrotime();
        $this->prefix = $prefix;
    }

    /**
     * @return string A format message of the elapsed time
     */
    function mark($label) {
        return sprintf("\n<div class=\"profiler\">$this->prefix %.3f $label</div>", $this->getmicrotime() - $this->start);
    }

    /**
     * @return float The current time in milliseconds
     */
    static function getmicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

}

if (phpversion() < '4.2.0') {
    require_once( dirname(__FILE__) . '/compat.php41x.php' );
}
if (phpversion() < '4.3.0') {
    require_once( dirname(__FILE__) . '/compat.php42x.php' );
}
if (version_compare(phpversion(), '5.0') < 0) {
    require_once( dirname(__FILE__) . '/compat.php50x.php' );
}

//@set_magic_quotes_runtime(0);

if (@$mosConfig_error_reporting === 0 || @$mosConfig_error_reporting === '0') {
    error_reporting(0);
} else if (@$mosConfig_error_reporting > 0) {
    error_reporting($mosConfig_error_reporting);
}

require_once( $mosConfig_absolute_path . '/includes/version.php' );
require_once( $mosConfig_absolute_path . '/includes/database.mysqli.php' );
require_once( $mosConfig_absolute_path . '/includes/gacl.class.php' );
require_once( $mosConfig_absolute_path . '/includes/gacl_api.class.php' );
require_once( $mosConfig_absolute_path . '/includes/phpmailer/class.phpmailer.php' );
require_once( $mosConfig_absolute_path . '/includes/joomla.xml.php' );
require_once( $mosConfig_absolute_path . '/includes/phpInputFilter/class.inputfilter.php' );
require_once( $mosConfig_absolute_path . '/includes/gacl_api.class.php' );
require_once( $mosConfig_absolute_path . '/configuration.php' );

$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
if ($database->getErrorNum()) {
    $mosSystemError = $database->getErrorNum();
    $basePath = dirname(__FILE__);
    include $basePath . '/../configuration.php';
    include $basePath . '/../offline.php';
    exit();
}
$database->debug($mosConfig_debug??false);
$acl = new gacl_api();



// platform neurtral url handling
if (isset($_SERVER['REQUEST_URI'])) {
    $request_uri = $_SERVER['REQUEST_URI'];
} else {
    $request_uri = $_SERVER['SCRIPT_NAME'];
    // Append the query string if it exists and isn't null
    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
        $request_uri .= '?' . $_SERVER['QUERY_STRING'];
    }
}
$_SERVER['REQUEST_URI'] = $request_uri;

// current server time
$now = date('Y-m-d H:i', time());
DEFINE('_CURRENT_SERVER_TIME', $now);
DEFINE('_CURRENT_SERVER_TIME_FORMAT', '%Y-%m-%d %H:%M:%S');

// Non http/https URL Schemes
$url_schemes = 'data:, file:, ftp:, gopher:, imap:, ldap:, mailto:, news:, nntp:, telnet:, javascript:, irc:';
DEFINE('_URL_SCHEMES', $url_schemes);

// disable strict mode in MySQL 5
if (!defined('_JOS_SET_SQLMODE')) {
    /** ensure that functions are declared only once */
    define('_JOS_SET_SQLMODE', 1);

    // if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
    if (strpos($database->getVersion(), '5') === 0) {
        $query = "SET sql_mode = 'MYSQL40'";
        $database->setQuery($query);
        $database->query();
    }
}
if (!function_exists('setMenuItem')) {
    function setMenuItem($obj, $parent_obj = false, $additionally = '', $need_carret = false)
    {
        global $mosConfig_live_site, $database, $sef, $mosConfig_lang;
        $return = new stdClass();
        $return->a = '';
        $return->link = '';
        $return->name = '';
        $return->target = '';
        $return->nofollow = '';
        //f($obj, $obj->new_type, $obj->alias, $obj->name);
        Switch ($obj->new_type) {

            case 'vm_category':
                $link = $sef->getCategoryURIById($obj->link);
//                if ($sef->city) {
//
//                    if ($mosConfig_lang == "french") {
//                        $link = "fr/" . $sef->city->url . ltrim($link, "fr");
//                    } else {
//                        $link = ($sef->city->url??'') . "/" . $link;
//                    }
//                }
                $return->link = $link;
                break;
            case 'url':
                //  f($obj, $obj->new_type, $obj->alias, $obj->name, $obj->link);
                // not sure if we need this. can't imagine use cases
                if ($parent_obj) {
                    $return->link .= (isset($parent_obj->category_alias) && $parent_obj->category_alias) ? $parent_obj->category_alias . "/" : ""; //url and vm_category type have no alias
                }
                $return->link .= ($mosConfig_lang == "french") ? $sef->translateURI('fr', $obj->link) : $obj->link;
                //    f($obj, $obj->new_type, $obj->alias, $obj->name, $obj->link, $return->link);
                break;
            default:
                $return->link = (($mosConfig_lang == "french") ? 'fr/' : '');
                if ($parent_obj) {
                    $return->link .= ($parent_obj->alias) ? $parent_obj->alias . "/" : ""; //url and vm_category type have no alias
                }
                $return->link .= $obj->alias . "/";

                break;
        }
        //f($return->link, $obj, $parent_obj);
        if (empty($obj->link)) {
            $return->link = '';
        }

        $return->name = ucfirst($obj->name);

        $return->target = '';

        if (isset($obj->nofollow) && ($obj->nofollow == '1')) {
            $return->nofollow = 'rel="nofollow"';
        }

        if (!empty($return->link)) {
            $return->a = '<a ' . $return->target . ' ' . $return->nofollow . ' href="/' . $return->link . '" ' . $additionally . '>' . $return->name . '' . ($need_carret ? '<b class="caret"></b>' : '') . '</a>';
        } else {
            $return->a = '<a href="#" ' . $additionally . '>' . $return->name . '' . ($need_carret ? '<b class="caret"></b>' : '') . '</a>';
        }
        //   d($obj->name, $parent_obj,$obj, $return);
        return $return;
    }
}
class LangNumberFormat {

    static function number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',') {
        global $iso_client_lang;
        return ( $iso_client_lang == "fr" ) ? number_format($number, $decimals, ',', '') . "$" : "$" . number_format($number, $decimals, '.', '');
    }

}

/**
 * @package Joomla
 * @abstract
 */
class mosAbstractLog {

    /** @var array */
    var $_log = null;

    /**
     * Constructor
     */
    function __construct() {
        $this->__constructor();
    }

    /**
     * Generic constructor
     */
    function __constructor() {
        $this->_log = array();
    }

    /**
     * @param string Log message
     * @param boolean True to append to last message
     */
    function log($text, $append = false) {
        $n = count($this->_log);
        if ($append && $n > 0) {
            $this->_log[count($this->_log) - 1] .= $text;
        } else {
            $this->_log[] = $text;
        }
    }

    /**
     * @param string The glue for each log item
     * @return string Returns the log
     */
    function getLog($glue = '<br/>', $truncate = 9000, $htmlSafe = false) {
        $logs = array();
        foreach ($this->_log as $log) {
            if ($htmlSafe) {
                $log = htmlspecialchars($log);
            }
            $logs[] = substr($log, 0, $truncate);
        }
        return implode($glue, $logs);
    }

}

/**
 * Task routing class
 * @package Joomla
 * @abstract
 */
class mosAbstractTasker {

    /** @var array An array of the class methods to call for a task */
    var $_taskMap = null;

    /** @var string The name of the current task */
    var $_task = null;

    /** @var array An array of the class methods */
    var $_methods = null;

    /** @var string A url to redirect to */
    var $_redirect = null;

    /** @var string A message about the operation of the task */
    var $_message = null;

    // action based access control

    /** @var string The ACO Section */
    var $_acoSection = null;

    /** @var string The ACO Section value */
    var $_acoSectionValue = null;

    /**
     * Constructor
     * @param string Set the default task
     */
    function __construct($default = '') {
        $this->_taskMap = array();
        $this->_methods = array();
        foreach (get_class_methods(get_class($this)) as $method) {
            if (substr($method, 0, 1) != '_') {
                $this->_methods[] = strtolower($method);
                // auto register public methods as tasks
                $this->_taskMap[strtolower($method)] = $method;
            }
        }
        $this->_redirect = '';
        $this->_message = '';
        if ($default) {
            $this->registerDefaultTask($default);
        }
    }

    /**
     * Sets the access control levels
     * @param string The ACO section (eg, the component)
     * @param string The ACO section value (if using a constant value)
     */
    function setAccessControl($section, $value = null) {
        $this->_acoSection = $section;
        $this->_acoSectionValue = $value;
    }

    /**
     * Access control check
     */
    function accessCheck($task) {
        global $acl, $my;

        // only check if the derived class has set these values
        if ($this->_acoSection) {
            // ensure user has access to this function
            if ($this->_acoSectionValue) {
                // use a 'constant' task for this task handler
                $task = $this->_acoSectionValue;
            }
            return $acl->acl_check($this->_acoSection, $task, 'users', $my->usertype);
        } else {
            return true;
        }
    }

    /**
     * Set a URL to redirect the browser to
     * @param string A URL
     */
    function setRedirect($url, $msg = null) {
        $this->_redirect = $url;
        if ($msg !== null) {
            $this->_message = $msg;
        }
    }

    /**
     * Redirects the browser
     */
    function redirect() {
        if ($this->_redirect) {
            mosRedirect($this->_redirect, $this->_message);
        }
    }

    /**
     * Register (map) a task to a method in the class
     * @param string The task
     * @param string The name of the method in the derived class to perform for this task
     */
    function registerTask($task, $method) {
        if (in_array(strtolower($method), $this->_methods)) {
            $this->_taskMap[strtolower($task)] = $method;
        } else {
            $this->methodNotFound($method);
        }
    }

    /**
     * Register the default task to perfrom if a mapping is not found
     * @param string The name of the method in the derived class to perform if the task is not found
     */
    function registerDefaultTask($method) {
        $this->registerTask('__default', $method);
    }

    /**
     * Perform a task by triggering a method in the derived class
     * @param string The task to perform
     * @return mixed The value returned by the function
     */
    function performTask($task) {
        $this->_task = $task;

        $task = strtolower($task);
        if (isset($this->_taskMap[$task])) {
            $doTask = $this->_taskMap[$task];
        } else if (isset($this->_taskMap['__default'])) {
            $doTask = $this->_taskMap['__default'];
        } else {
            return $this->taskNotFound($this->_task);
        }

        if ($this->accessCheck($doTask)) {
            return call_user_func(array(&$this, $doTask));
        } else {
            return $this->notAllowed($task);
        }
    }

    /**
     * Get the last task that was to be performed
     * @return string The task that was or is being performed
     */
    function getTask() {
        return $this->_task;
    }

    /**
     * Basic method if the task is not found
     * @param string The task
     * @return null
     */
    function taskNotFound($task) {
        echo 'Task ' . $task . ' not found';
        return null;
    }

    /**
     * Basic method if the registered method is not found
     * @param string The name of the method in the derived class
     * @return null
     */
    function methodNotFound($name) {
        echo 'Method ' . $name . ' not found';
        return null;
    }

    /**
     * Basic method if access is not permitted to the task
     * @param string The name of the method in the derived class
     * @return null
     */
    function notAllowed($name) {
        echo __DIR__ . __FILE__ . _NOT_AUTH;

        return null;
    }

}

/**
 * Class to support function caching
 * @package Joomla
 */
class mosCache {

    /**
     * @return object A function cache object
     */
    static function &getCache($group = '') {
        global $mosConfig_absolute_path, $mosConfig_caching, $mosConfig_cachepath, $mosConfig_cachetime;

        require_once( $mosConfig_absolute_path . '/includes/joomla.cache.php' );

        $options = array(
            'cacheDir' => $mosConfig_cachepath . '/',
            'caching' => $mosConfig_caching,
            'defaultGroup' => $group,
            'lifeTime' => $mosConfig_cachetime
        );
        $cache = new JCache_Lite_Function($options);
        return $cache;
    }

    /**
     * Cleans the cache
     */
    static function cleanCache($group = false) {
        global $mosConfig_caching;
        if ($mosConfig_caching) {
            $cache = & mosCache::getCache($group);
            $cache->clean($group);
        }
    }

}

/**
 * Joomla! Mainframe class
 *
 * Provide many supporting API functions
 * @package Joomla
 */
class mosMainFrame {

    /** @var database Internal database class pointer */
    var $_db = null;

    /** @var object An object of configuration variables */
    var $_config = null;

    /** @var object An object of path variables */
    var $_path = null;

    /** @var mosSession The current session */
    var $_session = null;

    /** @var string The current template */
    var $_template = null;

    /** @var array An array to hold global user state within a session */
    var $_userstate = null;

    /** @var array An array of page meta information */
    var $_head = null;

    /** @var string Custom html string to append to the pathway */
    var $_custom_pathway = null;

    /** @var boolean True if in the admin client */
    var $_isAdmin = false;

    /**
     * Class constructor
     * @param database A database connection object
     * @param string The url option
     * @param string The path of the mos directory
     */
    function __construct(&$db, $option, $basePath, $isAdmin = false) {
        $this->_db = & $db;

        // load the configuration values
        $this->_setTemplate($isAdmin);
        $this->_setAdminPaths($option, $this->getCfg('absolute_path'));
        if (isset($_SESSION['session_userstate'])) {
            $this->_userstate = & $_SESSION['session_userstate'];
        } else {
            $this->_userstate = null;
        }
        $this->_head = array();
        $this->_head['title'] = $GLOBALS['mosConfig_sitename'];
        $this->_head['meta'] = array();
        $this->_head['custom'] = array();

        //set the admin check
        $this->_isAdmin = (boolean) $isAdmin;

        $now = date('Y-m-d H:i:s', time());
        $this->set('now', $now);
    }

    /**
     * Gets the id number for a client
     * @param mixed A client identifier
     */
    function getClientID($client) {
        switch ($client) {
            case '2':
            case 'installation':
                return 2;
                break;

            case '1':
            case 'admin':
            case 'administrator':
                return 1;
                break;

            case '0':
            case 'site':
            case 'front':
            default:
                return 0;
                break;
        }
    }

    /**
     * Gets the client name
     * @param int The client identifier
     * @return strint The text name of the client
     */
    function getClientName($client_id) {
        // do not translate
        $clients = array('site', 'admin', 'installer');
        return mosGetParam($clients, $client_id, 'unknown');
    }

    /**
     * Gets the base path for the client
     * @param mixed A client identifier
     * @param boolean True (default) to add traling slash
     */
    function getBasePath($client = 0, $addTrailingSlash = true) {
        global $mosConfig_absolute_path;

        switch ($client) {
            case '0':
            case 'site':
            case 'front':
            default:
                return mosPathName($mosConfig_absolute_path, $addTrailingSlash);
                break;

            case '2':
            case 'installation':
                return mosPathName($mosConfig_absolute_path . '/installation', $addTrailingSlash);
                break;

            case '1':
            case 'admin':
            case 'administrator':
                return mosPathName($mosConfig_absolute_path . '/administrator', $addTrailingSlash);
                break;
        }
    }

    /**
     * @param string
     */
    function setPageTitle($title = null) {
        if (@$GLOBALS['mosConfig_pagetitles']) {
            $title = trim(htmlspecialchars($title));
            $title = stripslashes($title);
            $this->_head['title'] = $title ? $GLOBALS['mosConfig_sitename'] . ' - ' . $title : $GLOBALS['mosConfig_sitename'];
        }

//        echo '<pre>';
//            var_dump(debug_backtrace());
//        echo '</pre>';
    }

    function setPageTitle2($title = null) {
        if ($title) {
            $title = trim(htmlspecialchars($title));
            $title = stripslashes($title);
            $this->_head['title'] = $title;
        }

//        echo '<pre>';
//            var_dump(debug_backtrace());
//        echo '</pre>';
    }

    /**
     * @param string The value of the name attibute
     * @param string The value of the content attibute
     * @param string Text to display before the tag
     * @param string Text to display after the tag
     */
    function addMetaTag($name, $content, $prepend = '', $append = '') {
        $name = trim(htmlspecialchars($name));
        $content = trim(htmlspecialchars($content));
        $prepend = trim($prepend);
        $append = trim($append);
        $this->_head['meta'][] = array($name, $content, $prepend, $append);
    }

    function addMetaTag2($name, $content, $prepend = '', $append = '') {
        $name = trim(htmlspecialchars($name));
        $content = trim(htmlspecialchars(strip_tags($content)));
        $prepend = trim($prepend);
        $append = trim($append);

        $n = count($this->_head['meta']);
        $bExist = false;
        for ($i = 0; $i < $n; $i++) {
            if ($this->_head['meta'][$i][0] == $name) {
                $content = trim(htmlspecialchars($content));
                if ($content)
                    $this->_head['meta'][$i][1] = $content;

                $bExist = true;
            }
        }

        if (!$bExist)
            $this->_head['meta'][] = array($name, $content, $prepend, $append);
    }

    /**
     * @param string The value of the name attibute
     * @param string The value of the content attibute to append to the existing
     * Tags ordered in with Site Keywords and Description first
     */
    function appendMetaTag($name, $content) {
        $name = trim(htmlspecialchars($name));
        $n = count($this->_head['meta']);
        for ($i = 0; $i < $n; $i++) {
            if ($this->_head['meta'][$i][0] == $name) {
                $content = trim(htmlspecialchars($content));
                if ($content) {
                    if (!$this->_head['meta'][$i][1]) {
                        $this->_head['meta'][$i][1] = $content;
                    } else {
                        $this->_head['meta'][$i][1] = $content . ', ' . $this->_head['meta'][$i][1];
                    }
                }
                return;
            }
        }
        $this->addMetaTag($name, $content);
    }

    /**
     * @param string The value of the name attibute
     * @param string The value of the content attibute to append to the existing
     */
    function prependMetaTag($name, $content) {
        $name = trim(htmlspecialchars($name));
        $n = count($this->_head['meta']);
        for ($i = 0; $i < $n; $i++) {
            if ($this->_head['meta'][$i][0] == $name) {
                $content = trim(htmlspecialchars($content));
                $this->_head['meta'][$i][1] = $content . $this->_head['meta'][$i][1];
                return;
            }
        }
        $this->addMetaTag($name, $content);
    }

    /**
     * Adds a custom html string to the head block
     * @param string The html to add to the head
     */
    function addCustomHeadTag($html) {
        $this->_head['custom'][] = trim($html);
    }

    /**
     * @return string
     */
    function getHead() {
        $head = array();
        $head[] = '<title>' . $this->_head['title'] . '</title>';
        foreach ($this->_head['meta'] as $meta) {
            if ($meta[2]) {
                $head[] = $meta[2];
            }
            $head[] = '<meta name="' . $meta[0] . '" content="' . $meta[1] . '" />';
            if ($meta[3]) {
                $head[] = $meta[3];
            }
        }
        foreach ($this->_head['custom'] as $html) {
            $head[] = $html;
        }
        return implode("\n", $head) . "\n";
    }

    /**
     * @return string
     */
    function getPageTitle() {
        return $this->_head['title'];
    }

    /**
     * @return string
     */
    function getCustomPathWay() {
        return $this->_custom_pathway;
    }

    function appendPathWay($html) {
        $this->_custom_pathway[] = $html;
    }

    /**
     * Gets the value of a user state variable
     * @param string The name of the variable
     */
    function getUserState($var_name) {
        if (is_array($this->_userstate)) {
            return mosGetParam($this->_userstate, $var_name, null);
        } else {
            return null;
        }
    }

    /**
     * Gets the value of a user state variable
     * @param string The name of the user state variable
     * @param string The name of the variable passed in a request
     * @param string The default value for the variable if not found
     */
    function getUserStateFromRequest($var_name, $req_name, $var_default = null) {
        if (is_array($this->_userstate)) {
            if (isset($_REQUEST[$req_name])) {
                $this->setUserState($var_name, $_REQUEST[$req_name]);
            } else if (!isset($this->_userstate[$var_name])) {
                $this->setUserState($var_name, $var_default);
            }

            // filter input
            $iFilter = new InputFilter();
            $this->_userstate[$var_name] = $iFilter->process($this->_userstate[$var_name]);

            return $this->_userstate[$var_name];
        } else {
            return null;
        }
    }

    /**
     * Sets the value of a user state variable
     * @param string The name of the variable
     * @param string The value of the variable
     */
    function setUserState($var_name, $var_value) {
        if (is_array($this->_userstate)) {
            $this->_userstate[$var_name] = $var_value;
        }
    }

    /**
     * Initialises the user session
     *
     * Old sessions are flushed based on the configuration value for the cookie
     * lifetime. If an existing session, then the last access time is updated.
     * If a new session, a session id is generated and a record is created in
     * the jos_sessions table.
     */
    function initSession() {
        // initailize session variables
        $session 	=& $this->_session;
        $session 	= new mosSession( $this->_db );

        // purge expired sessions
      //  $session->purge('core');

        // Session Cookie `name`
        $sessionCookieName 	= mosMainFrame::sessionCookieName();
        // Get Session Cookie `value`
        $sessioncookie 		= strval( mosGetParam( $_COOKIE, $sessionCookieName, null ) );

        // Session ID / `value`
        $sessionValueCheck 	= mosMainFrame::sessionCookieValue( $sessioncookie );

        // Check if existing session exists in db corresponding to Session cookie `value`
        // extra check added in 1.0.8 to test sessioncookie value is of correct length
        if ( $sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $session->load($sessionValueCheck) ) {
            // update time in session table
            $session->time = time();
          //  $session->update();
        } else {
            // Remember Me Cookie `name`
            $remCookieName = mosMainFrame::remCookieName_User();

            // test if cookie found
            $cookie_found = false;
            if ( isset($_COOKIE[$sessionCookieName]) || isset($_COOKIE[$remCookieName]) || isset($_POST['force_session']) ) {
                $cookie_found = true;
            }

            // check if neither remembermecookie or sessioncookie found
            if (!$cookie_found) {
                // create sessioncookie and set it to a test value set to expire on session end
                setcookie( $sessionCookieName,
                    '-',
                    false,
                    "/"
                );
            } else {
                // otherwise, sessioncookie was found, but set to test val or the session expired, prepare for session registration and register the session
                $url = strval( mosGetParam( $_SERVER, 'REQUEST_URI', null ) );
                // stop sessions being created for requests to syndicated feeds
                if ( strpos( $url, 'option=com_rss' ) === false && strpos( $url, 'feed=' ) === false ) {
                    $session->guest 	= 1;
                    $session->username 	= '';
                    $session->time 		= time();
                    $session->gid 		= 0;
                    // Generate Session Cookie `value`
                    $session->generateId();

                    if (!$session->insert()) {
                        die( $session->getError() );
                    }

                    // create Session Tracking Cookie set to expire on session end
                    setcookie( $sessionCookieName,
                        $session->getCookie(),
                        false,
                        "/"
                    );
                }
            }

            // Cookie used by Remember me functionality
            $remCookieValue	= strval( mosGetParam( $_COOKIE, $remCookieName, null ) );

            // test if cookie is correct length
            if ( strlen($remCookieValue) > 64 ) {
                // Separate Values from Remember Me Cookie
                $remUser	= substr( $remCookieValue, 0, 32 );
                $remPass	= substr( $remCookieValue, 32, 32 );
                $remID		= intval( substr( $remCookieValue, 64  ) );

                // check if Remember me cookie exists. Login with usercookie info.
                if ( strlen($remUser) == 32 && strlen($remPass) == 32 ) {
                    $this->login( $remUser, $remPass, 1, $remID );
                }
            }
        }
    }

    /*
     * Function used to conduct admin session duties
     * Added as of 1.0.8
     * Deperciated 1.1
     */

    function initSessionAdmin($option, $task) {
        global $_VERSION, $mosConfig_admin_expired;

        // logout check
        if ($option == 'logout') {
            require $GLOBALS['mosConfig_absolute_path'] . '/administrator/logout.php';
            exit();
        }

        $site = $GLOBALS['mosConfig_live_site'];

        // check if session name corresponds to correct format
        if (session_name() != md5($site)) {
            echo "<script>document.location.href='index.php'</script>\n";
            exit();
        }

        // restore some session variables
        $my = new mosUser($this->_db);
        $my->id = intval(mosGetParam($_SESSION, 'session_user_id', ''));
        $my->username = strval(mosGetParam($_SESSION, 'session_username', ''));
        $my->usertype = strval(mosGetParam($_SESSION, 'session_usertype', ''));
        $my->gid = intval(mosGetParam($_SESSION, 'session_gid', ''));
        $my->params = mosGetParam($_SESSION, 'session_user_params', '');

        $session_id = mosGetParam($_SESSION, 'session_id', '');
        $logintime = mosGetParam($_SESSION, 'session_logintime', '');

        // check to see if session id corresponds with correct format
        if ($session_id == md5($my->id . $my->username . $my->usertype . $logintime)) {
            // if task action is to `save` or `apply` complete action before doing session checks.
            if ($task != 'save' && $task != 'apply') {
                // test for session_life_admin
                if (@$GLOBALS['mosConfig_session_life_admin']) {
                    $session_life_admin = $GLOBALS['mosConfig_session_life_admin'];
                } else {
                    $session_life_admin = 1800;
                }

                // purge expired admin sessions only		
                $past = time() - $session_life_admin;
                $query = "DELETE FROM #__session"
                        . "\n WHERE time < '$past'"
                        . "\n AND guest = 1"
                        . "\n AND gid = 0"
                        . "\n AND userid <> 0"
                ;
                $this->_db->setQuery($query);
              //  $this->_db->query();

                // update session timestamp
                $current_time = time();
                $query = "UPDATE #__session"
                        . "\n SET time = '$current_time'"
                        . "\n WHERE session_id = '$session_id'"
                ;
                $this->_db->setQuery($query);
               // $this->_db->query();

                // set garbage cleaning timeout
                $this->setSessionGarbageClean();

                // check against db record of session
                $query = "SELECT COUNT( session_id )"
                        . "\n FROM #__session"
                        . "\n WHERE session_id = '$session_id'"
                        . "\n AND username = " . $this->_db->Quote($my->username)
                        . "\n AND userid = " . intval($my->id)
                ;
                $this->_db->setQuery($query);
                $count = $this->_db->loadResult();

                // if no entry in session table that corresponds boot from admin area
                if ($count == 0) {
                    $link = NULL;

                    if ($_SERVER['QUERY_STRING']) {
                        $link = 'index2.php?' . $_SERVER['QUERY_STRING'];
                    }

                    // check if site designated as a production site 
                    // for a demo site disallow expired page functionality
                    // link must also be a Joomla link to stop malicious redirection
                    if ($link && strpos($link, 'index2.php?option=com_') === 0 && $_VERSION->SITE == 1 && @$mosConfig_admin_expired === '1') {
                        $now = time();

                        $file = $this->getPath('com_xml', 'com_users');
                        $params = new mosParameters($my->params, $file, 'component');

                        // return to expired page functionality
                        $params->set('expired', $link);
                        $params->set('expired_time', $now);

                        // param handling
                        if (is_array($params->toArray())) {
                            $txt = array();
                            foreach ($params->toArray() as $k => $v) {
                                $txt[] = "$k=$v";
                            }
                            $saveparams = implode("\n", $txt);
                        }

                        // save expired page info to user data
                        $query = "UPDATE #__users"
                                . "\n SET params = '$saveparams'"
                                . "\n WHERE id = $my->id"
                                . "\n AND username = '$my->username'"
                                . "\n AND usertype = '$my->usertype'"
                        ;
                        $this->_db->setQuery($query);
                        $this->_db->query();
                    }

                    echo "<script>document.location.href='index.php?mosmsg=Admin Session Expired'</script>\n";
                    exit();
                } else {
                    // load variables into session, used to help secure /popups/ functionality
                    $_SESSION['option'] = $option;
                    $_SESSION['task'] = $task;
                }
            }
        } else if ($session_id == '') {
            // no session_id as user has person has not attempted to login
            echo "<script>document.location.href='index.php?mosmsg=You need to login'</script>\n";
            exit();
        } else {
            // session id does not correspond to required session format
            echo "<script>document.location.href='index.php?mosmsg=Invalid Session'</script>\n";
            exit();
        }

        return $my;
    }

    /*
     * Function used to set Session Garbage Cleaning
     * garbage cleaning set at configured session time + 600 seconds
     * Added as of 1.0.8
     * Deperciated 1.1
     */

    function setSessionGarbageClean() {
        /** ensure that funciton is only called once */
        if (!defined('_JOS_GARBAGECLEAN')) {
            define('_JOS_GARBAGECLEAN', 1);

            $garbage_timeout = $this->getCfg('session_life_admin') + 600;
            @ini_set('session.gc_maxlifetime', $garbage_timeout);
        }
    }

    /*
     * Static Function used to generate the Session Cookie Name
     * Added as of 1.0.8
     * Deperciated 1.1
     */

    static function sessionCookieName() {
        global $mainframe;

        //return md5( 'site' . $mainframe->getCfg( 'live_site' ) );	
        // start fix
        return md5('site' . str_replace('http://', '', str_replace('https://', '', $mainframe->getCfg('live_site'))));
    }

    /*
     * Static Function used to generate the Session Cookie Value
     * Added as of 1.0.8
     * Deperciated 1.1
     */

    static function sessionCookieValue($id = null) {
        global $mainframe;

        $type = $mainframe->getCfg('session_type');

        $browser = @$_SERVER['HTTP_USER_AGENT'];

        switch ($type) {
            case 2:
                // 1.0.0 to 1.0.7 Compatibility
                // lowest level security
                $value = md5($id . $_SERVER['REMOTE_ADDR']);
                break;

            case 1:
                // slightly reduced security - 3rd level IP authentication for those behind IP Proxy 
                $remote_addr = explode('.', $_SERVER['REMOTE_ADDR']);
                $ip = $remote_addr[0] . '.' . $remote_addr[1] . '.' . $remote_addr[2];
                $value = mosHash($id . $ip . $browser);
                break;
            case 3:
                // Highest security level - new default for 1.0.8 and beyond
                $ip = '0.0.0.0';
                $value = mosHash($id . $ip);
                break;
            default:
                // Highest security level - new default for 1.0.8 and beyond
                $ip = $_SERVER['REMOTE_ADDR'];
                $value = mosHash($id . $ip . $browser);
                break;
        }

        return $value;
    }

    /*
     * Static Function used to generate the Rememeber Me Cookie Name for Username information
     * Added as of 1.0.8
     * Depreciated 1.1
     */

    function remCookieName_User() {
        $value = mosHash('remembermecookieusername' . mosMainFrame::sessionCookieName());

        return $value;
    }

    /*
     * Static Function used to generate the Rememeber Me Cookie Name for Password information
     * Added as of 1.0.8
     * Depreciated 1.1
     */

    function remCookieName_Pass() {
        $value = mosHash('remembermecookiepassword' . mosMainFrame::sessionCookieName());

        return $value;
    }

    /*
     * Static Function used to generate the Remember Me Cookie Value for Username information
     * Added as of 1.0.8
     * Depreciated 1.1
     */

    function remCookieValue_User($username) {
        $value = md5($username . mosHash(@$_SERVER['HTTP_USER_AGENT']));

        return $value;
    }

    /*
     * Static Function used to generate the Remember Me Cookie Value for Password information
     * Added as of 1.0.8
     * Depreciated 1.1
     */

    function remCookieValue_Pass($passwd) {
        $value = md5($passwd . mosHash(@$_SERVER['HTTP_USER_AGENT']));

        return $value;
    }

    /**
     * Login validation function
     *
     * Username and encoded password is compare to db entries in the jos_users
     * table. A successful validation updates the current session record with
     * the users details.
     */
    // For Facebook
    /*
      function login_facebook( $username ) {
      global $database;
      $temp_username 	= strval( mosGetParam( $_GET, 'mail', '' ) );

      $query = "SELECT email"
      . "\n FROM #__users"
      . "\n WHERE email = '$username'"
      ;

      $database->setQuery( $query );
      $rows = $database->loadObjectList();
      foreach($rows as $user_m) { $t_mail = $user_m->email; };
      if ( $t_mail!= $username) {
      $query = "INSERT INTO jos_users (name, username, email, password, usertype, activation) values ('".$temp_username."', '".$temp_username."', '".$temp_username."', '".$temp_username."', 'Registered', '0')";
      $this->_db->setQuery( $query );
      $this->_db->query();
      };

      global $acl, $_VERSION;
      $bypost = 0;

      // if no username and password passed from function, then function is being called from login module/component
      if (!$username) {
      //$username 	= $mail;
      $username 	= strval( mosGetParam( $_GET, 'mail', '' ) );

      $bypost 	= 1;
      // Session Cookie `name`
      $sessionCookieName 	= mosMainFrame::sessionCookieName();
      // Get Session Cookie `value`
      $sessioncookie 		= strval( mosGetParam( $_COOKIE, $sessionCookieName, null ) );
      // extra check to ensure that Joomla! sessioncookie exists
      if (!($sessioncookie == '-' || strlen($sessioncookie) == 32)) {
      header( 'HTTP/1.0 403 Forbidden' );
      mosErrorAlert( _NOT_AUTH );
      return;
      }

      josSpoofCheck(NULL,1);
      }

      $row = null;
      if (!$username) {
      echo "<script> alert(\""._LOGIN_INCOMPLETE."\"); window.history.go(-1); </script>\n";
      exit();
      } else {
      if ( $remember && strlen($username) == 32 && $userid ) {
      // query used for remember me cookie
      $harden = mosHash( @$_SERVER['HTTP_USER_AGENT'] );

      $query = "SELECT id, name, username, password, email, usertype, block, gid"
      . "\n FROM #__users"
      . "\n WHERE email = $username"
      ;
      $this->_db->setQuery( $query );
      $this->_db->loadObject($user);



      $check_username = md5( $user->username . $harden );
      //$check_password = md5( $user->password . $harden );

      if ( $check_username == $username ) {
      $row = $user;
      }
      } else {
      // query used for login via login module
      $query = "SELECT id, name, username, email, password, usertype, block, gid"
      . "\n FROM #__users"
      . "\n WHERE email = '$username'"
      //. "\n AND password = '$passwd'"
      ;
      $this->_db->setQuery( $query );
      $this->_db->loadObject( $row );
      }

      if (is_object($row)) {
      // user blocked from login
      if ($row->block == 1) {
      mosErrorAlert(_LOGIN_BLOCKED);
      }

      // fudge the group stuff
      $grp = $acl->getAroGroup( $row->id );
      $row->gid = 1;
      if ($acl->is_group_child_of( $grp->name, 'Registered', 'ARO' ) || $acl->is_group_child_of( $grp->name, 'Public Backend', 'ARO' )) {
      // fudge Authors, Editors, Publishers and Super Administrators into the Special Group
      $row->gid = 2;
      }
      $row->usertype = $grp->name;

      // initialize session data
      $session 			=& $this->_session;
      $session->guest 	= 0;
      $session->username 	= $row->username;
      $session->userid 	= intval( $row->id );
      $session->usertype 	= $row->usertype;
      $session->gid 		= intval( $row->gid );
      $session->update();

      // check to see if site is a production site
      // allows multiple logins with same user for a demo site
      if ( $_VERSION->SITE ) {
      // delete any old front sessions to stop duplicate sessions
      $query = "DELETE FROM #__session"
      . "\n WHERE session_id != '$session->session_id'"
      . "\n AND username = '$row->username'"
      . "\n AND userid = $row->id"
      . "\n AND gid = $row->gid"
      . "\n AND guest = 0"
      ;
      $this->_db->setQuery( $query );
      $this->_db->query();
      }

      // update user visit data
      $currentDate = date("Y-m-d\TH:i:s");

      $query = "UPDATE #__users"
      . "\n SET lastvisitDate = '$currentDate'"
      . "\n WHERE id = $session->userid"
      ;
      $this->_db->setQuery($query);
      if (!$this->_db->query()) {
      die($this->_db->stderr(true));
      }

      // set remember me cookie if selected
      $remember = strval( mosGetParam( $_POST, 'remember', '' ) );
      if ( $remember == 'yes' ) {
      // cookie lifetime of 365 days
      $lifetime 		= time() + 365*24*60*60;
      $remCookieName 	= mosMainFrame::remCookieName_User();
      $remCookieValue = mosMainFrame::remCookieValue_User( $row->username ) . mosMainFrame::remCookieValue_Pass( $row->password ) . $row->id;
      setcookie( $remCookieName, $remCookieValue, $lifetime, '/' );

      }
      mosCache::cleanCache();
      } else {
      if ( $bypost ) {
      mosErrorAlert(_LOGIN_INCORRECT);

      } else {
      $this->logout();
      mosRedirect('index.php');
      }
      exit();
      }
      }
      mosRedirect('index.php?option=com_login');
      }
     */


    //end for facebook
    function login($username = null, $passwd = null, $remember = 0, $userid = NULL) {
        global $acl, $_VERSION,$mosConfig_sitename, $mosConfig_absolute_path;

        $bypost = 0;

        // if no username and password passed from function, then function is being called from login module/component
        if (!$username || !$passwd) {
            $username = strval(mosGetParam($_POST, 'username', ''));
            $passwd = strval(mosGetParam($_POST, 'passwd', ''));
            $passwd = md5($passwd);
            $this->logLoginAttempt((object)['username' => $username ?? 'empty'], 'empty', $passwd);
            $bypost = 1;

            // Session Cookie `name`
            $sessionCookieName = mosMainFrame::sessionCookieName();
            // Get Session Cookie `value`
            $sessioncookie = strval(mosGetParam($_COOKIE, $sessionCookieName, null));
            // extra check to ensure that Joomla! sessioncookie exists			
            if (!($sessioncookie == '-' || strlen($sessioncookie) == 32)) {
                header('HTTP/1.0 403 Forbidden');
                mosErrorAlert(_NOT_AUTH);
                return;
            }

            josSpoofCheck(NULL, 1);
        }

        $row = null;
        if (!$username || !$passwd) {
            echo "<script> alert(\"" . _LOGIN_INCOMPLETE . "\"); window.history.go(-1); </script>\n";
            exit();
        } else {
            if ($remember && strlen($username) == 32 && strlen($passwd) == 32 && $userid) {
                // query used for remember me cookie
                $harden = mosHash(@$_SERVER['HTTP_USER_AGENT']);

                $query = "SELECT id, name, username, password, usertype, block, gid"
                        . "\n FROM #__users"
                        . "\n WHERE id = $userid"
                ;
                $this->_db->setQuery($query);
                $this->_db->loadObject($user);

                $check_username = md5($user->username . $harden);
                $check_password = md5($user->password . $harden);

                if ($check_username == $username && $check_password == $passwd) {
                    $row = $user;
                }
            } else {
                // query used for login via login module
                $query = "SELECT id, name, email, username, password, usertype, block, gid"
                        . "\n FROM #__users"
                        . "\n WHERE username = '$username'"
                        . "\n AND password = '$passwd'"
                ;
                $this->_db->setQuery($query);
                $this->_db->loadObject($row);
            }

            if (is_object($row)) {
                $this->logLoginAttempt($row, 'success', $passwd);
                // user blocked from login
                if ($row->block == 1) {
                    mosErrorAlert(_LOGIN_BLOCKED);
                    $this->logLoginAttempt($row, 'blocked', $passwd);
                }

                // fudge the group stuff
                $grp = $acl->getAroGroup($row->id);
                $row->gid = 1;
                if ($acl->is_group_child_of($grp->name, 'Registered', 'ARO') || $acl->is_group_child_of($grp->name, 'Public Backend', 'ARO')) {
                    // fudge Authors, Editors, Publishers and Super Administrators into the Special Group
                    $row->gid = 2;
                }
                $row->usertype = $grp->name;

                // initialize session data
                $session = & $this->_session;
                $session->guest = 0;
                $session->username = $row->username;
                $session->userid = intval($row->id);
                $session->usertype = $row->usertype;
                $session->gid = intval($row->gid);
                $session->update();

                // check to see if site is a production site
                // allows multiple logins with same user for a demo site
                if ($_VERSION->SITE) {
                    // delete any old front sessions to stop duplicate sessions
                    $query = "DELETE FROM #__session"
                            . "\n WHERE session_id != '$session->session_id'"
                            . "\n AND username = '$row->username'"
                            . "\n AND userid = $row->id"
                            . "\n AND gid = $row->gid"
                            . "\n AND guest = 0"
                    ;
                    $this->_db->setQuery($query);
                    $this->_db->query();
                }

                // update user visit data

                date_default_timezone_set('Australia/Sydney');

                $currentDate = date("Y-m-d\TH:i:s");

                $query = "UPDATE #__users"
                        . "\n SET lastvisitDate = '$currentDate'"
                        . "\n WHERE id = $session->userid"
                ;

                $this->_db->setQuery($query);
                if (!$this->_db->query()) {
                    die($this->_db->stderr(true));
                }

                // set remember me cookie if selected
                $remember = strval(mosGetParam($_POST, 'remember', ''));
                if ($remember == 'yes') {
                    // cookie lifetime of 365 days
                    $lifetime = time() + 365 * 24 * 60 * 60;
                    $remCookieName = mosMainFrame::remCookieName_User();
                    $remCookieValue = mosMainFrame::remCookieValue_User($row->username) . mosMainFrame::remCookieValue_Pass($row->password) . $row->id;
                    setcookie($remCookieName,
                        $remCookieValue,
                        $lifetime,
                        "/; SameSite=Strict",
                        $mosConfig_sitename,
                        true);
                }
                $this->createUserSessionCartData($row);
                mosCache::cleanCache();

            } else {
                $this->logLoginAttempt((object)['username' => $username], 'not found');
                if ($bypost) {
                    mosErrorAlert(_LOGIN_INCORRECT);
                } else {
                    $this->logout();
                    mosRedirect('index.php');
                }
                exit();
            }
        }
    }

    private function createUserSessionCartData($row) {

        $query = "SELECT id"
            . "\n FROM tbl_saved_session_data"
            . "\n WHERE user_id =  '".$this->_db->getEscaped((int) $row->id)."'"
        ;
        $this->_db->setQuery($query);
        if (!$this->_db->loadResult()) {
            $mysqlDatetime = date('Y-m-d G:i:s', time());
            $query = "INSERT INTO tbl_saved_session_data ( user_id,user_name,user_email,session_data,date_added,date_modify) VALUES ( 
                                '".$this->_db->getEscaped((int) $row->id)."',
                                '".$this->_db->getEscaped($row->username)."',
                                '".$this->_db->getEscaped($row->email)."',
                                '".$this->_db->getEscaped(serialize($_SESSION['cart'] ?? [] ))."',
                                '" . $mysqlDatetime . "',
                                '" . $mysqlDatetime . "'
                                )";
            $this->_db->setQuery($query);
            if (!$this->_db->query()) {
                die($this->_db->stderr(true));
            }
        }

    }

    private function mergeCartItems($arr1,$arr2)
    {
        $newCartItems = $arr1;
        if(is_array($arr1) && is_array($arr2)) {
            unset($arr1['idx'],$arr2['idx']);
            foreach($arr1 as $k => $v) {
                $arr1[$v['product_id']] = $v;
                unset($arr1[$k]);
            }
            foreach($arr2 as $k2 => $v2) {
                $arr2[$v2['product_id']] = $v2;
                unset($arr2[$k2]);
            }

            $newCartItems = array_values(array_replace($arr2,$arr1));
            $newCartItems['idx'] = count($newCartItems);

        }
        return $newCartItems;
    }
    public function updateUserSessionCartData($data) {
        global $my;
        if(!$my->id){
            $_SESSION['session_filled'] = false;
            return false;
        }

        if(isset($_SESSION['cart']) && !$_SESSION['session_filled']){

            $query = "SELECT session_data"
                . "\n FROM tbl_saved_session_data"
                . "\n WHERE user_id =  '".$this->_db->getEscaped((int) $my->id)."'"
            ;
            $this->_db->setQuery($query);
            $oldData = $this->_db->loadResult();
            if ($oldData) {
                $_SESSION['session_filled'] = true;
                $_SESSION['cart'] = $data = $this->mergeCartItems($data, unserialize($oldData));

            }

        }

        date_default_timezone_set('Australia/Sydney');
        $mysqlDatetime = date('Y-m-d G:i:s', time());
        $query = "UPDATE `tbl_saved_session_data`  SET 
                                     `session_data`='".$this->_db->getEscaped(serialize($data))."',
                                     `date_modify`='".$mysqlDatetime."'
                                     WHERE `user_id`='" . (int) $my->id . "' ";
        $this->_db->setQuery($query);
        if (!$this->_db->query()) {
            die($this->_db->stderr(true));
        }
    }

    private function logLoginAttempt($user, $status, $providedPassword = null) {
        $phone = null;
        if ($user && isset($user->id) ) {
            $query = "SELECT phone_1 FROM jos_vm_user_info as ui 
                   WHERE user_id = " . (int)$user->id . " 
                   AND address_type = 'BT'";
            $this->_db->setQuery($query);
            $phone = $this->_db->loadResult();
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $query = "INSERT INTO `jos_login_attempts` 
            (`user_id`, `name`, `username`, `email`, `password_db`, `password_provided`, `phone`, `status`, `is_blocked`, `user_agent`, `created_at`) 
          VALUES 
            ('" . (int)$user->id . "', 
             '" . $this->_db->getEscaped($user->name ?? '') . "', 
             '" . $this->_db->getEscaped($user->username ?? '') . "', 
             '" . $this->_db->getEscaped($user->email ?? '') . "', 
             '" . $this->_db->getEscaped($user->password ?? '') . "', 
             '" . $this->_db->getEscaped($providedPassword ?? '') . "', 
             '" . $this->_db->getEscaped($phone ?? '') . "', 
             '" . $this->_db->getEscaped($status) . "', 
             '" . (int)($user->block ?? 0) . "', 
             '" . $this->_db->getEscaped($userAgent) . "',
             CONVERT_TZ(NOW(3), 'UTC', 'Australia/Sydney'))";

        $this->_db->setQuery($query);
        $this->_db->query();
    }


    function login_social($user_id) {
        global $acl, $_VERSION,$mosConfig_sitename;

        $harden = mosHash(@$_SERVER['HTTP_USER_AGENT']);

        $query = "SELECT id, name, username, password, usertype, block, gid"
                . "\n FROM #__users"
                . "\n WHERE id = $user_id";
        $this->_db->setQuery($query);
        $this->_db->loadObject($row);
        if (is_object($row)) {
            // user blocked from login
            if ($row->block == 1) {
                mosErrorAlert(_LOGIN_BLOCKED);
            }

            // fudge the group stuff
            $grp = $acl->getAroGroup($row->id);
            $row->gid = 1;
            if ($acl->is_group_child_of($grp->name, 'Registered', 'ARO') || $acl->is_group_child_of($grp->name, 'Public Backend', 'ARO')) {
                // fudge Authors, Editors, Publishers and Super Administrators into the Special Group
                $row->gid = 2;
            }
            $row->usertype = $grp->name;

            // initialize session data
            $session = & $this->_session;
            $session->guest = 0;
            $session->username = $row->username;
            $session->userid = intval($row->id);
            $session->usertype = $row->usertype;
            $session->gid = intval($row->gid);
            $session->update();


            // delete any old front sessions to stop duplicate sessions
            $query = "DELETE FROM #__session"
                    . "\n WHERE session_id != '$session->session_id'"
                    . "\n AND username = '$row->username'"
                    . "\n AND userid = $row->id"
                    . "\n AND gid = $row->gid"
                    . "\n AND guest = 0"
            ;
            $this->_db->setQuery($query);
            $this->_db->query();


            // update user visit data
            date_default_timezone_set('Australia/Sydney');

            $currentDate = date("Y-m-d\TH:i:s");

            $query = "UPDATE #__users"
                    . "\n SET lastvisitDate = '$currentDate'"
                    . "\n WHERE id = $session->userid"
            ;
            $this->_db->setQuery($query);
            if (!$this->_db->query()) {
                die($this->_db->stderr(true));
            }

            // set remember me cookie if selected
            $remember = strval(mosGetParam($_POST, 'remember', ''));

            // cookie lifetime of 2 hrs
            $lifetime = time() + 2 * 60 * 60;
            $remCookieName = mosMainFrame::remCookieName_User();
            $remCookieValue = mosMainFrame::remCookieValue_User($row->username) . mosMainFrame::remCookieValue_Pass($row->password) . $row->id;
            setcookie($remCookieName,
                $remCookieValue,
                $lifetime,
                "/; SameSite=Strict",
                $mosConfig_sitename,
                true
            );

            mosCache::cleanCache();
            return true;
        }
        return false;
    }

    /**
     * User logout
     *
     * Reverts the current session record back to 'anonymous' parameters
     */
    function logout($resource = null) {
        global $mosConfig_sitename;
        $this->logLoginAttempt((object)[
            'username' =>  $_SESSION['auth']['username'],
            'id' =>  $_SESSION['auth']['user_id']
        ], 'logout from ' . $resource );
        mosCache::cleanCache();

        $session = & $this->_session;
        $session->guest = 1;
        $session->username = '';
        $session->userid = '';
        $session->usertype = '';
        $session->gid = 0;

        $session->update();

        // kill remember me cookie
        $lifetime = time() - 86400;
        $remCookieName = mosMainFrame::remCookieName_User();
        setcookie($remCookieName,
            ' ',
            $lifetime,
            "/; SameSite=Strict",
            $mosConfig_sitename,
            true
        );

        @session_destroy();
    }

    /**
     * @return mosUser A user object with the information from the current session
     */
    function getUser() {
        global $database;

        $user = new mosUser($this->_db);

        $user->id = intval($this->_session->userid);
        $user->username = $this->_session->username;
        $user->usertype = $this->_session->usertype;
        $user->gid = intval($this->_session->gid);

        if ($user->id) {
            $query = "SELECT id, name, email, block, sendEmail, registerDate, lastvisitDate, activation, params"
                    . "\n FROM #__users"
                    . "\n WHERE id = " . intval($user->id)
            ;
            $database->setQuery($query);
            $database->loadObject($my);

            $user->params = $my->params;
            $user->name = $my->name;
            $user->email = $my->email;
            $user->block = $my->block;
            $user->sendEmail = $my->sendEmail;
            $user->registerDate = $my->registerDate;
            $user->lastvisitDate = $my->lastvisitDate;
            $user->activation = $my->activation;
        }

        return $user;
    }

    public function need_help()
    {
        global $mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_need_help;

        $name = (string)mosGetParam($_POST, 'name', '');
        $email = (string)mosGetParam($_POST, 'email', '');
        $message = (string)mosGetParam($_POST, 'message', '');
        $phone = (string)mosGetParam($_POST, 'phone', '');
        $emailMsg = sprintf("<b>Email</b>: %s<br> <b>Name</b>: %s<br> <b>Phone</b>: %s<br> <b>Message</b>: %s", $email, $name, $phone, $message);

        mosMail($mosConfig_mailfrom_noreply, $mosConfig_fromname, $mosConfig_need_help, 'Need Help? Contact Us Here', $emailMsg, 1);

    }
    /**
     * @param string The name of the variable (from configuration.php)
     * @return mixed The value of the configuration variable or null if not found
     */
    function getCfg($varname) {
        $varname = 'mosConfig_' . $varname;
        if (isset($GLOBALS[$varname])) {
            return $GLOBALS[$varname];
        } else {
            return null;
        }
    }

    function _setTemplate($isAdmin = false) {
        global $Itemid,$mosConfig_sitename;
        $mosConfig_absolute_path = $this->getCfg('absolute_path');

        if ($isAdmin) {
            $query = "SELECT template"
                    . "\n FROM #__templates_menu"
                    . "\n WHERE client_id = 1"
                    . "\n AND menuid = 0"
            ;
            $this->_db->setQuery($query);
            $cur_template = $this->_db->loadResult();
            $path = "$mosConfig_absolute_path/administrator/templates/$cur_template/index.php";
            if (!file_exists($path)) {
                $cur_template = 'joomla_admin';
            }
        } else {
            $assigned = (!empty($Itemid) ? " OR menuid = $Itemid" : '' );


            $cur_template = 'bloomex_adaptive';

            // TemplateChooser Start
            $jos_user_template = strval(mosGetParam($_COOKIE, 'jos_user_template', ''));
            $jos_change_template = strval(mosGetParam($_REQUEST, 'jos_change_template', $jos_user_template));
            if ($jos_change_template) {
                // clean template name
                $jos_change_template = preg_replace('#\W#', '', $jos_change_template);
                if (strlen($jos_change_template) >= 40) {
                    $jos_change_template = substr($jos_change_template, 0, 39);
                }

                // check that template exists in case it was deleted
                if (file_exists($mosConfig_absolute_path . '/templates/' . $jos_change_template . '/index.php')) {
                    $lifetime = 60 * 10;
                    $cur_template = $jos_change_template;
                    setcookie('jos_user_template', "$jos_change_template", time() + $lifetime,
                              '/; SameSite=Strict',
                              $mosConfig_sitename,
                              true);
                } else {
                    setcookie('jos_user_template', '', time() - 3600,
                              '/; SameSite=Strict',
                              $mosConfig_sitename,
                              true);
                }
            }
            // TemplateChooser End
        }

        $this->_template = $cur_template;
    }

    function getTemplate() {
        return $this->_template;
    }

    /**
     * Determines the paths for including engine and menu files
     * @param string The current option used in the url
     * @param string The base path from which to load the configuration file
     */
    function _setAdminPaths($option, $basePath = '.') {
        $option = strtolower($option);

        $this->_path = new stdClass();

        // security check to disable use of `/`, `\\` and `:` in $options variable 
        if (strpos($option, '/') !== false || strpos($option, '\\') !== false || strpos($option, ':') !== false) {
            mosErrorAlert('Restricted access');
            return;
        }

        $prefix = substr($option, 0, 4);
        if ($prefix != 'com_' && $prefix != 'mod_') {
            // ensure backward compatibility with existing links
            $name = $option;
            $option = "com_$option";
        } else {
            $name = substr($option, 4);
        }

        // components
        if (file_exists("$basePath/templates/$this->_template/components/$name.html.php")) {
            $this->_path->front = "$basePath/components/$option/$name.php";
            $this->_path->front_html = "$basePath/templates/$this->_template/components/$name.html.php";
        } else if (file_exists("$basePath/components/$option/$name.php")) {
            $this->_path->front = "$basePath/components/$option/$name.php";
            $this->_path->front_html = "$basePath/components/$option/$name.html.php";
        }

        if (file_exists("$basePath/administrator/components/$option/admin.$name.php")) {
            $this->_path->admin = "$basePath/administrator/components/$option/admin.$name.php";
            $this->_path->admin_html = "$basePath/administrator/components/$option/admin.$name.html.php";
        }

        if (file_exists("$basePath/administrator/components/$option/toolbar.$name.php")) {
            $this->_path->toolbar = "$basePath/administrator/components/$option/toolbar.$name.php";
            $this->_path->toolbar_html = "$basePath/administrator/components/$option/toolbar.$name.html.php";
            $this->_path->toolbar_default = "$basePath/administrator/includes/toolbar.html.php";
        }

        if (file_exists("$basePath/components/$option/$name.class.php")) {
            $this->_path->class = "$basePath/components/$option/$name.class.php";
        } else if (file_exists("$basePath/administrator/components/$option/$name.class.php")) {
            $this->_path->class = "$basePath/administrator/components/$option/$name.class.php";
        } else if (file_exists("$basePath/includes/$name.php")) {
            $this->_path->class = "$basePath/includes/$name.php";
        }

        if ($prefix == 'mod_' && file_exists("$basePath/administrator/modules/$option.php")) {
            $this->_path->admin = "$basePath/administrator/modules/$option.php";
            $this->_path->admin_html = "$basePath/administrator/modules/mod_$name.html.php";
        } else if (file_exists("$basePath/administrator/components/$option/admin.$name.php")) {
            $this->_path->admin = "$basePath/administrator/components/$option/admin.$name.php";
            $this->_path->admin_html = "$basePath/administrator/components/$option/admin.$name.html.php";
        } else {
            $this->_path->admin = "$basePath/administrator/components/com_admin/admin.admin.php";
            $this->_path->admin_html = "$basePath/administrator/components/com_admin/admin.admin.html.php";
        }
    }

    /**
     * Returns a stored path variable
     *
     */
    function getPath($varname, $option = '') {
        global $mosConfig_absolute_path;
        if ($option) {
            $temp = $this->_path;
            $this->_setAdminPaths($option, $this->getCfg('absolute_path'));
        }
        $result = null;
        if (isset($this->_path->$varname)) {
            $result = $this->_path->$varname;
        } else {
            switch ($varname) {
                case 'corporate':
                    $result = $mosConfig_absolute_path . "/administrator/components/com_users/corporate.user.class.php";
                    break;

                case 'com_xml':
                    $name = substr($option, 4);
                    $path = "$mosConfig_absolute_path/administrator/components/$option/$name.xml";
                    if (file_exists($path)) {
                        $result = $path;
                    } else {
                        $path = "$mosConfig_absolute_path/components/$option/$name.xml";
                        if (file_exists($path)) {
                            $result = $path;
                        }
                    }
                    break;

                case 'mod0_xml':
                    // Site modules
                    if ($option == '') {
                        $path = $mosConfig_absolute_path . "/modules/custom.xml";
                    } else {
                        $path = $mosConfig_absolute_path . "/modules/$option.xml";
                    }
                    if (file_exists($path)) {
                        $result = $path;
                    }
                    break;

                case 'mod1_xml':
                    // admin modules
                    if ($option == '') {
                        $path = $mosConfig_absolute_path . '/administrator/modules/custom.xml';
                    } else {
                        $path = $mosConfig_absolute_path . "/administrator/modules/$option.xml";
                    }
                    if (file_exists($path)) {
                        $result = $path;
                    }
                    break;

                case 'bot_xml':
                    // Site mambots
                    $path = $mosConfig_absolute_path . "/mambots/$option.xml";
                    if (file_exists($path)) {
                        $result = $path;
                    }
                    break;

                case 'menu_xml':
                    $path = $mosConfig_absolute_path . "/administrator/components/com_menus/$option/$option.xml";
                    if (file_exists($path)) {
                        $result = $path;
                    }
                    break;

                case 'installer_html':
                    $path = $mosConfig_absolute_path . "/administrator/components/com_installer/$option/$option.html.php";
                    if (file_exists($path)) {
                        $result = $path;
                    }
                    break;

                case 'installer_class':
                    $path = $mosConfig_absolute_path . "/administrator/components/com_installer/$option/$option.class.php";
                    if (file_exists($path)) {
                        $result = $path;
                    }
                    break;
            }
        }
        if ($option) {
            $this->_path = $temp;
        }
        return $result;
    }

    /**
     * Detects a 'visit'
     *
     * This function updates the agent and domain table hits for a particular
     * visitor.  The user agent is recorded/incremented if this is the first visit.
     * A cookie is set to mark the first visit.
     */
    function detect() {
        global $mosConfig_enable_stats,$mosConfig_sitename;
        if ($mosConfig_enable_stats == 1) {
            if (mosGetParam($_COOKIE, 'mosvisitor', 0)) {
                return;
            }
            setcookie('mosvisitor',
                1,
                false,
                "/; SameSite=Strict",
                $mosConfig_sitename,
                true
            );

            if (phpversion() <= '4.2.1') {
                $agent = getenv('HTTP_USER_AGENT');
                $domain = @gethostbyaddr(getenv("REMOTE_ADDR"));
            } else {
                if (isset($_SERVER['HTTP_USER_AGENT'])) {
                    $agent = $_SERVER['HTTP_USER_AGENT'];
                } else {
                    $agent = 'Unknown';
                }

                $domain = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
            }

            $browser = mosGetBrowser($agent);

            $query = "SELECT COUNT(*)"
                    . "\n FROM #__stats_agents"
                    . "\n WHERE agent = '$browser'"
                    . "\n AND type = 0"
            ;
            $this->_db->setQuery($query);
            if ($this->_db->loadResult()) {
                $query = "UPDATE #__stats_agents"
                        . "\n SET hits = ( hits + 1 )"
                        . "\n WHERE agent = '$browser'"
                        . "\n AND type = 0"
                ;
                $this->_db->setQuery($query);
            } else {
                $query = "INSERT INTO #__stats_agents"
                        . "\n ( agent, type )"
                        . "\n VALUES ( '$browser', 0 )"
                ;
                $this->_db->setQuery($query);
            }
            $this->_db->query();

            $os = mosGetOS($agent);

            $query = "SELECT COUNT(*)"
                    . "\n FROM #__stats_agents"
                    . "\n WHERE agent = '$os'"
                    . "\n AND type = 1"
            ;
            $this->_db->setQuery($query);
            if ($this->_db->loadResult()) {
                $query = "UPDATE #__stats_agents"
                        . "\n SET hits = ( hits + 1 )"
                        . "\n WHERE agent = '$os'"
                        . "\n AND type = 1"
                ;
                $this->_db->setQuery($query);
            } else {
                $query = "INSERT INTO #__stats_agents"
                        . "\n ( agent, type )"
                        . "\n VALUES ( '$os', 1 )"
                ;
                $this->_db->setQuery($query);
            }
            $this->_db->query();

            // tease out the last element of the domain
            $tldomain = explode("\.", $domain);
            $tldomain = $tldomain[count($tldomain) - 1];

            if (is_numeric($tldomain)) {
                $tldomain = "Unknown";
            }

            $query = "SELECT COUNT(*)"
                    . "\n FROM #__stats_agents"
                    . "\n WHERE agent = '$tldomain'"
                    . "\n AND type = 2"
            ;
            $this->_db->setQuery($query);
            if ($this->_db->loadResult()) {
                $query = "UPDATE #__stats_agents"
                        . "\n SET hits = ( hits + 1 )"
                        . "\n WHERE agent = '$tldomain'"
                        . "\n AND type = 2"
                ;
                $this->_db->setQuery($query);
            } else {
                $query = "INSERT INTO #__stats_agents"
                        . "\n ( agent, type )"
                        . "\n VALUES ( '$tldomain', 2 )"
                ;
                $this->_db->setQuery($query);
            }
            $this->_db->query();
        }
    }

    /**
     * @return correct Itemid for Content Item
     */
    function getItemid($id, $typed = 1, $link = 1, $bs = 1, $bc = 1, $gbs = 1) {
        global $Itemid;

        $_Itemid = '';

        if ($_Itemid == '' && $typed && $this->getStaticContentCount()) {
            $exists = 0;
            foreach ($this->get('_ContentTyped', array()) as $key => $value) {
                // check if id has been tested before, if it is pull from class variable store
                if ($key == $id) {
                    $_Itemid = $value;
                    $exists = 1;
                    break;
                }
            }
            // if id hasnt been checked before initaite query
            if (!$exists) {
                // Search for typed link
                $query = "SELECT id"
                        . "\n FROM #__menu"
                        . "\n WHERE type = 'content_typed'"
                        . "\n AND published = 1"
                        . "\n AND link = 'index.php?option=com_content&task=view&id=$id'"
                ;
                $this->_db->setQuery($query);
                // pull existing query storage into temp variable
                $ContentTyped = $this->get('_ContentTyped', array());
                // add query result to temp array storage
                $ContentTyped[$id] = $this->_db->loadResult();
                // save temp array to main array storage
                $this->set('_ContentTyped', $ContentTyped);

                $_Itemid = $ContentTyped[$id];
            }
        }

        if ($_Itemid == '' && $link && $this->getContentItemLinkCount()) {
            $exists = 0;
            foreach ($this->get('_ContentItemLink', array()) as $key => $value) {
                // check if id has been tested before, if it is pull from class variable store
                if ($key == $id) {
                    $_Itemid = $value;
                    $exists = 1;
                    break;
                }
            }
            // if id hasnt been checked before initaite query
            if (!$exists) {
                // Search for item link
                $query = "SELECT id"
                        . "\n FROM #__menu"
                        . "\n WHERE type = 'content_item_link'"
                        . "\n AND published = 1"
                        . "\n AND link = 'index.php?option=com_content&task=view&id=$id'"
                ;
                $this->_db->setQuery($query);
                // pull existing query storage into temp variable
                $ContentItemLink = $this->get('_ContentItemLink', array());
                // add query result to temp array storage
                $ContentItemLink[$id] = $this->_db->loadResult();
                // save temp array to main array storage
                $this->set('_ContentItemLink', $ContentItemLink);

                $_Itemid = $ContentItemLink[$id];
            }
        }

        if ($_Itemid == '') {
            $exists = 0;
            foreach ($this->get('_ContentSection', array()) as $key => $value) {
                // check if id has been tested before, if it is pull from class variable store
                if ($key == $id) {
                    $_Itemid = $value;
                    $exists = 1;
                    break;
                }
            }
            // if id hasnt been checked before initaite query
            if (!$exists) {
                $query = "SELECT ms.id AS sid, ms.type AS stype, mc.id AS cid, mc.type AS ctype, i.id as sectionid, i.id As catid, ms.published AS spub, mc.published AS cpub"
                        . "\n FROM #__content AS i"
                        . "\n LEFT JOIN #__sections AS s ON i.sectionid = s.id"
                        . "\n LEFT JOIN #__menu AS ms ON ms.componentid = s.id "
                        . "\n LEFT JOIN #__categories AS c ON i.catid = c.id"
                        . "\n LEFT JOIN #__menu AS mc ON mc.componentid = c.id "
                        . "\n WHERE ( ms.type IN ( 'content_section', 'content_blog_section' ) OR mc.type IN ( 'content_blog_category', 'content_category' ) )"
                        . "\n AND i.id = $id"
                        . "\n ORDER BY ms.type DESC, mc.type DESC, ms.id, mc.id"
                ;
                $this->_db->setQuery($query);
                $links = $this->_db->loadObjectList();

                if (count($links)) {
                    foreach ($links as $link) {
                        if ($link->stype == 'content_section' && $link->sectionid == $id && !isset($content_section) && $link->spub == 1) {
                            $content_section = $link->sid;
                        }

                        if ($link->stype == 'content_blog_section' && $link->sectionid == $id && !isset($content_blog_section) && $link->spub == 1) {
                            $content_blog_section = $link->sid;
                        }

                        if ($link->ctype == 'content_blog_category' && $link->catid == $id && !isset($content_blog_category) && $link->cpub == 1) {
                            $content_blog_category = $link->cid;
                        }

                        if ($link->ctype == 'content_category' && $link->catid == $id && !isset($content_category) && $link->cpub == 1) {
                            $content_category = $link->cid;
                        }
                    }
                }

                if (!isset($content_section)) {
                    $content_section = null;
                }

                // pull existing query storage into temp variable
                $ContentSection = $this->get('_ContentSection', array());
                // add query result to temp array storage
                $ContentSection[$id] = $content_section;
                // save temp array to main array storage
                $this->set('_ContentSection', $ContentSection);

                $_Itemid = $ContentSection[$id];
            }
        }

        if ($_Itemid == '') {
            $exists = 0;
            foreach ($this->get('_ContentBlogSection', array()) as $key => $value) {
                // check if id has been tested before, if it is pull from class variable store
                if ($key == $id) {
                    $_Itemid = $value;
                    $exists = 1;
                    break;
                }
            }
            // if id hasnt been checked before initaite query
            if (!$exists) {
                if (!isset($content_blog_section)) {
                    $content_blog_section = null;
                }

                // pull existing query storage into temp variable
                $ContentBlogSection = $this->get('_ContentBlogSection', array());
                // add query result to temp array storage
                $ContentBlogSection[$id] = $content_blog_section;
                // save temp array to main array storage
                $this->set('_ContentBlogSection', $ContentBlogSection);

                $_Itemid = $ContentBlogSection[$id];
            }
        }

        if ($_Itemid == '') {
            $exists = 0;
            foreach ($this->get('_ContentBlogCategory', array()) as $key => $value) {
                // check if id has been tested before, if it is pull from class variable store
                if ($key == $id) {
                    $_Itemid = $value;
                    $exists = 1;
                    break;
                }
            }
            // if id hasnt been checked before initaite query
            if (!$exists) {
                if (!isset($content_blog_category)) {
                    $content_blog_category = null;
                }

                // pull existing query storage into temp variable
                $ContentBlogCategory = $this->get('_ContentBlogCategory', array());
                // add query result to temp array storage
                $ContentBlogCategory[$id] = $content_blog_category;
                // save temp array to main array storage
                $this->set('_ContentBlogCategory', $ContentBlogCategory);

                $_Itemid = $ContentBlogCategory[$id];
            }
        }

        if ($_Itemid == '') {
            // ensure that query is only called once		
            if (!$this->get('_GlobalBlogSection') && !defined('_JOS_GBS')) {
                define('_JOS_GBS', 1);

                // Search in global blog section
                $query = "SELECT id "
                        . "\n FROM #__menu "
                        . "\n WHERE type = 'content_blog_section'"
                        . "\n AND published = 1"
                        . "\n AND componentid = 0"
                ;
                $this->_db->setQuery($query);
                $this->set('_GlobalBlogSection', $this->_db->loadResult());
            }

            $_Itemid = $this->get('_GlobalBlogSection');
        }

        if ($_Itemid == '') {
            $exists = 0;
            foreach ($this->get('_ContentCategory', array()) as $key => $value) {
                // check if id has been tested before, if it is pull from class variable store
                if ($key == $id) {
                    $_Itemid = $value;
                    $exists = 1;
                    break;
                }
            }
            // if id hasnt been checked before initaite query
            if (!$exists) {
                if (!isset($content_category)) {
                    $content_category = null;
                }

                // pull existing query storage into temp variable
                $ContentCategory = $this->get('_ContentCategory', array());
                // add query result to temp array storage
                //$ContentCategory[$id] 	= $this->_db->loadResult();	
                $ContentCategory[$id] = $content_category;
                // save temp array to main array storage
                $this->set('_ContentCategory', $ContentCategory);

                $_Itemid = $ContentCategory[$id];
            }
        }

        if ($_Itemid == '') {
            // ensure that query is only called once		
            if (!$this->get('_GlobalBlogCategory') && !defined('_JOS_GBC')) {
                define('_JOS_GBC', 1);

                // Search in global blog category
                $query = "SELECT id "
                        . "\n FROM #__menu "
                        . "\n WHERE type = 'content_blog_category'"
                        . "\n AND published = 1"
                        . "\n AND componentid = 0"
                ;
                $this->_db->setQuery($query);
                $this->set('_GlobalBlogCategory', $this->_db->loadResult());
            }

            $_Itemid = $this->get('_GlobalBlogCategory');
        }

        if ($_Itemid != '') {
            // if Itemid value discovered by queries, return this value
            return $_Itemid;
        } else if ($Itemid != 99999999 && $Itemid === 0) {
            // if queries do not return Itemid value, return Itemid of page - if it is not 99999999
            return $Itemid;
        }
    }

    /**
     * @return number of Published Blog Sections
     * Kept for Backward Compatability
     */
    function getBlogSectionCount() {
        return 1;
    }

    /**
     * @return number of Published Blog Categories
     * Kept for Backward Compatability
     */
    function getBlogCategoryCount() {
        return 1;
    }

    /**
     * @return number of Published Global Blog Sections
     * Kept for Backward Compatability
     */
    function getGlobalBlogSectionCount() {
        return 1;
    }

    /**
     * @return number of Static Content
     */
    function getStaticContentCount() {
        // ensure that query is only called once		
        if (!$this->get('_StaticContentCount') && !defined('_JOS_SCC')) {
            define('_JOS_SCC', 1);

            $query = "SELECT COUNT( id )"
                    . "\n FROM #__menu "
                    . "\n WHERE type = 'content_typed'"
                    . "\n AND published = 1"
            ;
            $this->_db->setQuery($query);
            // saves query result to variable
            $this->set('_StaticContentCount', $this->_db->loadResult());
        }

        return $this->get('_StaticContentCount');
    }

    /**
     * @return number of Content Item Links
     */
    function getContentItemLinkCount() {
        // ensure that query is only called once		
        if (!$this->get('_ContentItemLinkCount') && !defined('_JOS_CILC')) {
            define('_JOS_CILC', 1);

            $query = "SELECT COUNT( id )"
                    . "\n FROM #__menu "
                    . "\n WHERE type = 'content_item_link'"
                    . "\n AND published = 1"
            ;
            $this->_db->setQuery($query);
            // saves query result to variable
            $this->set('_ContentItemLinkCount', $this->_db->loadResult());
        }

        return $this->get('_ContentItemLinkCount');
    }

    /**
     * @param string The name of the property
     * @param mixed The value of the property to set
     */
    function set($property, $value = null) {
        $this->$property = $value;
    }

    /**
     * @param string The name of the property
     * @param mixed  The default value
     * @return mixed The value of the property
     */
    function get($property, $default = null) {
        if (isset($this->$property)) {
            return $this->$property;
        } else {
            return $default;
        }
    }

    /** Is admin interface?
     * @return boolean
     * @since 1.0.2
     */
    function isAdmin() {
        return $this->_isAdmin;
    }

}

/**
 * Component database table class
 * @package Joomla
 */
class mosComponent extends mosDBTable {

    /** @var int Primary key */
    var $id = null;

    /** @var string */
    var $name = null;

    /** @var string */
    var $link = null;

    /** @var int */
    var $menuid = null;

    /** @var int */
    var $parent = null;

    /** @var string */
    var $admin_menu_link = null;

    /** @var string */
    var $admin_menu_alt = null;

    /** @var string */
    var $option = null;

    /** @var string */
    var $ordering = null;

    /** @var string */
    var $admin_menu_img = null;

    /** @var int */
    var $iscore = null;

    /** @var string */
    var $params = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__components', 'id', $db);
    }

}

/**
 * Utility class for all HTML drawing classes
 * @package Joomla
 */
class mosHTML {

    static function makeOption($value, $text = '', $value_name = 'value', $text_name = 'text') {
        $obj = new stdClass;
        $obj->$value_name = $value;
        $obj->$text_name = trim($text) ? $text : $value;
        return $obj;
    }

    function writableCell($folder, $relative = 1, $text = '', $visible = 1) {
        $writeable = '<b><font color="green">Writeable</font></b>';
        $unwriteable = '<b><font color="red">Unwriteable</font></b>';

        echo '<tr>';
        echo '<td class="item">';
        echo $text;
        if ($visible) {
            echo $folder . '/';
        }
        echo '</td>';
        echo '<td align="left">';
        if ($relative) {
            echo is_writable("../$folder") ? $writeable : $unwriteable;
        } else {
            echo is_writable("$folder") ? $writeable : $unwriteable;
        }
        echo '</td>';
        echo '</tr>';
    }

    /**
     * Generates an HTML select list
     * @param array An array of objects
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param string The name of the object variable for the option value
     * @param string The name of the object variable for the option text
     * @param mixed The key that is selected
     * @returns string HTML for the select list
     */
    static function selectList(&$arr, $tag_name, $tag_attribs, $key, $text, $selected = NULL) {
        // check if array
        if (is_array($arr)) {
            reset($arr);
        }

        $html = "\n<select name=\"$tag_name\" id=\"$tag_name\" $tag_attribs>";
        $count = count($arr);

        for ($i = 0, $n = $count; $i < $n; $i++) {
            $k = $arr[$i]->$key;
            $t = $arr[$i]->$text;
            $id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

            $extra = '';
            $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
            if (is_array($selected)) {
                foreach ($selected as $obj) {
                    $k2 = $obj->$key;
                    if ($k == $k2) {
                        $extra .= " selected=\"selected\"";
                        break;
                    }
                }
            } else {
                $extra .= ($k == $selected ? " selected=\"selected\"" : '');
            }
            $html .= "\n\t<option value=\"" . $k . "\"$extra>" . $t . "</option>";
        }
        $html .= "\n</select>\n";

        return $html;
    }

    /**
     * Writes a select list of integers
     * @param int The start integer
     * @param int The end integer
     * @param int The increment
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param mixed The key that is selected
     * @param string The printf format to be applied to the number
     * @returns string HTML for the select list
     */
    function integerSelectList($start, $end, $inc, $tag_name, $tag_attribs, $selected, $format = "") {
        $start = intval($start);
        $end = intval($end);
        $inc = intval($inc);
        $arr = array();

        for ($i = $start; $i <= $end; $i += $inc) {
            $fi = $format ? sprintf("$format", $i) : "$i";
            $arr[] = mosHTML::makeOption($fi, $fi);
        }

        return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
    }

    /**
     * Writes a select list of month names based on Language settings
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param mixed The key that is selected
     * @returns string HTML for the select list values
     */
    function monthSelectList($tag_name, $tag_attribs, $selected) {
        $arr = array(
            mosHTML::makeOption('01', _JAN),
            mosHTML::makeOption('02', _FEB),
            mosHTML::makeOption('03', _MAR),
            mosHTML::makeOption('04', _APR),
            mosHTML::makeOption('05', _MAY),
            mosHTML::makeOption('06', _JUN),
            mosHTML::makeOption('07', _JUL),
            mosHTML::makeOption('08', _AUG),
            mosHTML::makeOption('09', _SEP),
            mosHTML::makeOption('10', _OCT),
            mosHTML::makeOption('11', _NOV),
            mosHTML::makeOption('12', _DEC)
        );

        return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
    }

    /**
     * Generates an HTML select list from a tree based query list
     * @param array Source array with id and parent fields
     * @param array The id of the current list item
     * @param array Target array.  May be an empty array.
     * @param array An array of objects
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param string The name of the object variable for the option value
     * @param string The name of the object variable for the option text
     * @param mixed The key that is selected
     * @returns string HTML for the select list
     */
    function treeSelectList(&$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected) {

        // establish the hierarchy of the menu
        $children = array();
        // first pass - collect children
        foreach ($src_list as $v) {
            $pt = $v->parent;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }
        // second pass - get an indent list of the items
        $ilist = mosTreeRecurse(0, '', array(), $children);

        // assemble menu items to the array
        $this_treename = '';
        foreach ($ilist as $item) {
            if ($this_treename) {
                if ($item->id != $src_id && strpos($item->treename, $this_treename) === false) {
                    $tgt_list[] = mosHTML::makeOption($item->id, $item->treename);
                }
            } else {
                if ($item->id != $src_id) {
                    $tgt_list[] = mosHTML::makeOption($item->id, $item->treename);
                } else {
                    $this_treename = "$item->treename/";
                }
            }
        }
        // build the html select list
        return mosHTML::selectList($tgt_list, $tag_name, $tag_attribs, $key, $text, $selected);
    }

    /**
     * Writes a yes/no select list
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param mixed The key that is selected
     * @returns string HTML for the select list values
     */
    function yesnoSelectList($tag_name, $tag_attribs, $selected, $yes = _CMN_YES, $no = _CMN_NO) {
        $arr = array(
            mosHTML::makeOption('0', $no),
            mosHTML::makeOption('1', $yes),
        );

        return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
    }

    /**
     * Generates an HTML radio list
     * @param array An array of objects
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param mixed The key that is selected
     * @param string The name of the object variable for the option value
     * @param string The name of the object variable for the option text
     * @returns string HTML for the select list
     */
    static function radioList(&$arr, $tag_name, $tag_attribs, $selected = null, $key = 'value', $text = 'text') {
        reset($arr);
        $html = "";
        for ($i = 0, $n = count($arr); $i < $n; $i++) {
            $k = $arr[$i]->$key;
            $t = $arr[$i]->$text;
            $id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

            $extra = '';
            $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
            if (is_array($selected)) {
                foreach ($selected as $obj) {
                    $k2 = $obj->$key;
                    if ($k == $k2) {
                        $extra .= " selected=\"selected\"";
                        break;
                    }
                }
            } else {
                $extra .= ($k == $selected ? " checked=\"checked\"" : '');
            }
            $html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"" . $k . "\"$extra $tag_attribs />";
            $html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
        }
        $html .= "\n";

        return $html;
    }

    /**
     * Writes a yes/no radio list
     * @param string The value of the HTML name attribute
     * @param string Additional HTML attributes for the <select> tag
     * @param mixed The key that is selected
     * @returns string HTML for the radio list
     */
    static function yesnoRadioList($tag_name, $tag_attribs, $selected, $yes = _CMN_YES, $no = _CMN_NO) {
        $arr = array(
            mosHTML::makeOption('0', $no),
            mosHTML::makeOption('1', $yes)
        );

        return mosHTML::radioList($arr, $tag_name, $tag_attribs, $selected);
    }

    /**
     * @param int The row index
     * @param int The record id
     * @param boolean
     * @param string The name of the form element
     * @return string
     */
    static function idBox($rowNum, $recId, $checkedOut = false, $name = 'cid') {
        if ($checkedOut) {
            return '';
        } else {
            return '<input type="checkbox" class="order_checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="isChecked(this.checked);" />';
        }
    }

    function sortIcon($base_href, $field, $state = 'none') {
        global $mosConfig_live_site;

        $alts = array(
            'none' => _CMN_SORT_NONE,
            'asc' => _CMN_SORT_ASC,
            'desc' => _CMN_SORT_DESC,
        );
        $next_state = 'asc';
        if ($state == 'asc') {
            $next_state = 'desc';
        } else if ($state == 'desc') {
            $next_state = 'none';
        }

        $html = "<a href=\"$base_href&field=$field&order=$next_state\">"
                . "<img src=\"$mosConfig_live_site/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
                . "</a>";
        return $html;
    }

    /**
     * Writes Close Button
     */
    static function CloseButton(&$params, $hide_js = NULL) {
        // displays close button in Pop-up window
        if ($params->get('popup') && !$hide_js) {
            ?>
            <script language="javascript" type="text/javascript">
                <!--
            document.write('<div align="center" style="margin-top: 30px; margin-bottom: 30px;">');
                document.write('<a href="#" onclick="javascript:window.close();"><span class="small"><?php echo _PROMPT_CLOSE; ?></span></a>');
                document.write('</div>');
                //-->
            </script>
            <?php
        }
    }

    /**
     * Writes Back Button
     */
    static function BackButton(&$params, $hide_js = NULL) {
        // Back Button
        if ($params->get('back_button') && !$params->get('popup') && !$hide_js) {
            ?>
            <div class="back_button">
                <a href='javascript:history.go(-1)'>
                    <?php echo _BACK; ?></a>
            </div>
            <?php
        }
    }

    /**
     * Cleans text of all formating and scripting code
     */
    static function cleanText(&$text) {
        $text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
        $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
        $text = preg_replace('/<!--.+?-->/', '', $text);
        $text = preg_replace('/{.+?}/', '', $text);
        $text = preg_replace('/&nbsp;/', ' ', $text);
        $text = preg_replace('/&amp;/', ' ', $text);
        $text = preg_replace('/&quot;/', ' ', $text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text);

        return $text;
    }

    /**
     * Writes Print icon
     */
    function PrintIcon(&$row, &$params, $hide_js, $link, $status = NULL) {
        if ($params->get('print') && !$hide_js) {
            // use default settings if none declared
            if (!$status) {
                $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
            }

            // checks template image directory for image, if non found default are loaded
            if ($params->get('icons')) {
                $image = mosAdminMenus::ImageCheck('printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT, _CMN_PRINT);
            } else {
                $image = _ICON_SEP . '&nbsp;' . _CMN_PRINT . '&nbsp;' . _ICON_SEP;
            }

            if ($params->get('popup') && !$hide_js) {
                // Print Preview button - used when viewing page
                ?>
                <script language="javascript" type="text/javascript">
                <!--
                    document.write('<td align="right" width="100%" class="buttonheading">');
                    document.write('<a href="#" onclick="javascript:window.print(); return false;" title="<?php echo _CMN_PRINT; ?>">');
                    document.write('<?php echo $image; ?>');
                    document.write('</a>');
                    document.write('</td>');
                    //-->
                </script>
                <?php
            } else {
                // Print Button - used in pop-up window
                ?>
                <td align="right" width="100%" class="buttonheading">
                    <a href="<?php echo $link; ?>" target="_blank" onclick="window.open('<?php echo $link; ?>', 'win2', '<?php echo $status; ?>');
                            return false;" title="<?php echo _CMN_PRINT; ?>">
                           <?php echo $image; ?></a>
                </td>
                <?php
            }
        }
    }

    /**
     * simple Javascript Cloaking
     * email cloacking
     * by default replaces an email with a mailto link with email cloacked
     */
    function emailCloaking($mail, $mailto = 1, $text = '', $email = 1) {
        // convert text
        $mail = mosHTML::encoding_converter($mail);
        // split email by @ symbol
        $mail = explode('@', $mail);
        $mail_parts = explode('.', $mail[1]);
        // random number
        $rand = rand(1, 100000);

        $replacement = "\n <script language='JavaScript' type='text/javascript'>";
        $replacement .= "\n <!--";
        $replacement .= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
        $replacement .= "\n var path = 'hr' + 'ef' + '=';";
        $replacement .= "\n var addy" . $rand . " = '" . @$mail[0] . "' + '&#64;';";
        $replacement .= "\n addy" . $rand . " = addy" . $rand . " + '" . implode("' + '&#46;' + '", $mail_parts) . "';";

        if ($mailto) {
            // special handling when mail text is different from mail addy
            if ($text) {
                if ($email) {
                    // convert text
                    $text = mosHTML::encoding_converter($text);
                    // split email by @ symbol
                    $text = explode('@', $text);
                    $text_parts = explode('.', $text[1]);
                    $replacement .= "\n var addy_text" . $rand . " = '" . @$text[0] . "' + '&#64;' + '" . implode("' + '&#46;' + '", @$text_parts) . "';";
                } else {
                    $replacement .= "\n var addy_text" . $rand . " = '" . $text . "';";
                }
                $replacement .= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>' );";
                $replacement .= "\n document.write( addy_text" . $rand . " );";
                $replacement .= "\n document.write( '<\/a>' );";
            } else {
                $replacement .= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>' );";
                $replacement .= "\n document.write( addy" . $rand . " );";
                $replacement .= "\n document.write( '<\/a>' );";
            }
        } else {
            $replacement .= "\n document.write( addy" . $rand . " );";
        }
        $replacement .= "\n //-->";
        $replacement .= '\n </script>';

        // XHTML compliance `No Javascript` text handling
        $replacement .= "<script language='JavaScript' type='text/javascript'>";
        $replacement .= "\n <!--";
        $replacement .= "\n document.write( '<span style=\'display: none;\'>' );";
        $replacement .= "\n //-->";
        $replacement .= "\n </script>";
        $replacement .= _CLOAKING;
        $replacement .= "\n <script language='JavaScript' type='text/javascript'>";
        $replacement .= "\n <!--";
        $replacement .= "\n document.write( '</' );";
        $replacement .= "\n document.write( 'span>' );";
        $replacement .= "\n //-->";
        $replacement .= "\n </script>";

        return $replacement;
    }

    function encoding_converter($text) {
        // replace vowels with character encoding
        $text = str_replace('a', '&#97;', $text);
        $text = str_replace('e', '&#101;', $text);
        $text = str_replace('i', '&#105;', $text);
        $text = str_replace('o', '&#111;', $text);
        $text = str_replace('u', '&#117;', $text);

        return $text;
    }

}

/**
 * Category database table class
 * @package Joomla
 */
class mosCategory extends mosDBTable {

    /** @var int Primary key */
    var $id = null;

    /** @var int */
    var $parent_id = null;

    /** @var string The menu title for the Category (a short name) */
    var $title = null;

    /** @var string The full name for the Category */
    var $name = null;

    /** @var string */
    var $image = null;

    /** @var string */
    var $section = null;

    /** @var int */
    var $image_position = null;

    /** @var string */
    var $description = null;

    /** @var boolean */
    var $published = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var int */
    var $ordering = null;

    /** @var int */
    var $access = null;

    /** @var string */
    var $params = null;
    var $en_meta_title = null;/** @var string */
    var $en_meta_description = null;/** @var string */
    var $en_meta_keyword = null;/** @var string */

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__categories', 'id', $db);
    }

    // overloaded check function
    function check() {
        // check for valid name
        if (trim($this->title) == '') {
            $this->_error = "Your Category must contain a title.";
            return false;
        }
        if (trim($this->name) == '') {
            $this->_error = "Your Category must have a name.";
            return false;
        }

        // check for existing name
        $query = "SELECT id"
                . "\n FROM #__categories "
                . "\n WHERE name = '$this->name'"
                . "\n AND section = '$this->section'"
        ;
        $this->_db->setQuery($query);

        $xid = intval($this->_db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->_error = "There is a category already with that name, please try again.";
            return false;
        }
        return true;
    }

}

/**
 * Section database table class
 * @package Joomla
 */
class mosSection extends mosDBTable {

    /** @var int Primary key */
    var $id = null;

    /** @var string The menu title for the Section (a short name) */
    var $title = null;

    /** @var string The full name for the Section */
    var $name = null;

    /** @var string */
    var $image = null;

    /** @var string */
    var $scope = null;

    /** @var int */
    var $image_position = null;

    /** @var string */
    var $description = null;

    /** @var boolean */
    var $published = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var int */
    var $ordering = null;

    /** @var int */
    var $access = null;

    /** @var string */
    var $params = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__sections', 'id', $db);
    }

    // overloaded check function
    function check() {
        // check for valid name
        if (trim($this->title) == '') {
            $this->_error = "Your Section must contain a title.";
            return false;
        }
        if (trim($this->name) == '') {
            $this->_error = "Your Section must have a name.";
            return false;
        }
        // check for existing name
        $query = "SELECT id"
                . "\n FROM #__sections "
                . "\n WHERE name = '$this->name'"
                . "\n AND scope = '$this->scope'"
        ;
        $this->_db->setQuery($query);

        $xid = intval($this->_db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->_error = "There is a section already with that name, please try again.";
            return false;
        }
        return true;
    }

}

/**
 * Module database table class
 * @package Joomla
 */
class mosContent extends mosDBTable {

    /** @var int Primary key */
    var $id = null;

    /** @var string */
    var $title = null;

    /** @var string */
    var $title_alias = null;

    /** @var string */
    var $introtext = null;

    /** @var string */
    var $fulltext = null;

    /** @var int */
    var $state = null;

    /** @var int The id of the category section */
    var $sectionid = null;

    /** @var int DEPRECATED */
    var $mask = null;

    /** @var int */
    var $catid = null;

    /** @var datetime */
    var $created = null;

    /** @var int User id */
    var $created_by = null;

    /** @var string An alias for the author */
    var $created_by_alias = null;

    /** @var datetime */
    var $modified = null;

    /** @var int User id */
    var $modified_by = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var datetime */
    var $frontpage_up = null;

    /** @var datetime */
    var $frontpage_down = null;

    /** @var datetime */
    var $publish_up = null;

    /** @var datetime */
    var $publish_down = null;

    /** @var string */
    var $images = null;

    /** @var string */
    var $urls = null;

    /** @var string */
    var $attribs = null;

    /** @var int */
    var $version = null;

    /** @var int */
    var $parentid = null;

    /** @var int */
    var $ordering = null;

    /** @var string */
    var $page_title_fr = null;
    var $metakey_fr = null;
    var $metadesc_fr = null;
    var $page_title = null;
    var $metakey = null;

    /** @var string */
    var $metadesc = null;

    /** @var int */
    var $access = null;

    /** @var int */
    var $hits = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__content', 'id', $db);
    }

    /**
     * Validation and filtering
     */
    function check() {
        // filter malicious code
        $ignoreList = array('introtext', 'fulltext');
        $this->filter($ignoreList);

        /*
          TODO: This filter is too rigorous,
          need to implement more configurable solution
          // specific filters
          $iFilter = new InputFilter( null, null, 1, 1 );
          $this->introtext = trim( $iFilter->process( $this->introtext ) );
          $this->fulltext =  trim( $iFilter->process( $this->fulltext ) );
         */

        if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '') {
            $this->fulltext = '';
        }

        return true;
    }

    /**
     * Converts record to XML
     * @param boolean Map foreign keys to text values
     */
    function toXML($mapKeysToText = false) {
        global $database;

        if ($mapKeysToText) {
            $query = "SELECT name"
                    . "\n FROM #__sections"
                    . "\n WHERE id = $this->sectionid"
            ;
            $database->setQuery($query);
            $this->sectionid = $database->loadResult();

            $query = "SELECT name"
                    . "\n FROM #__categories"
                    . "\n WHERE id $this->catid"
            ;
            $database->setQuery($query);
            $this->catid = $database->loadResult();

            $query = "SELECT name"
                    . "\n FROM #__users"
                    . "\n WHERE id = $this->created_by"
            ;
            $database->setQuery($query);
            $this->created_by = $database->loadResult();
        }

        return parent::toXML($mapKeysToText);
    }

}

/**
 * Module database table class
 * @package Joomla
 */
class mosMenu extends mosDBTable {

    /** @var int Primary key */
    var $id = null;

    /** @var string */
    var $menutype = null;

    /** @var string */
    var $name = null;

    /** @var string */
    var $link = null;

    /** @var int */
    var $type = null;

    /** @var int */
    var $published = null;

    /** @var int */
    var $show = null;

    /** @var int */
    var $nofollow = null;

    /** @var int */
    var $componentid = null;

    /** @var int */
    var $parent = null;

    /** @var int */
    var $sublevel = null;

    /** @var int */
    var $ordering = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var datetime */
    var $checked_out_time = null;

    /** @var boolean */
    var $pollid = null;

    /** @var string */
    var $browserNav = null;

    /** @var int */
    var $access = null;

    /** @var int */
    var $utaccess = null;

    /** @var string */
    var $params = null;

    /** @var string */
    var $alias = null;

    /** @var string */
    var $page_title = null;

    /** @var string */
    var $metakey = null;

    /** @var string */
    var $metadesc = null;

    /** @var string */
    var $new_type = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__menu', 'id', $db);
    }

    function check() {
        $this->id = (int) $this->id;
        $this->params = (string) trim($this->params . ' ');
        return true;
    }

    function load($id = null) {
        parent::load($id);

        $this->name = htmlspecialchars($this->name, ENT_QUOTES);
    }

}

/**
 * Users Table Class
 *
 * Provides access to the jos_user table
 * @package Joomla
 */
class mosUser extends mosDBTable {

    /** @var int Unique id */
    var $id = null;

    /** @var string The users real name (or nickname) */
    var $name = null;

    /** @var string The login name */
    var $username = null;

    /** @var string email */
    var $email = null;

    /** @var string MD5 encrypted password */
    var $password = null;

    /** @var string */
    var $usertype = null;

    /** @var int */
    var $block = null;

    /** @var int */
    var $sendEmail = null;

    /** @var int The group id number */
    var $gid = null;

    /** @var datetime */
    var $registerDate = null;

    /** @var datetime */
    var $lastvisitDate = null;

    /** @var string activation hash */
    var $activation = null;

    /** @var string */
    var $params = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$database) {
        parent::__construct('#__users', 'id', $database);
    }

    /**
     * Validation and filtering
     * @return boolean True is satisfactory
     */
    function check() {
        global $mosConfig_uniquemail;
        $this->username = $this->email;
        // Validate user information
        if (trim($this->name) == '') {
            $this->_error = _REGWARN_NAME;
            return false;
        }

//        if (trim($this->username) == '') {
//            $this->_error = _REGWARN_UNAME;
//            return false;
//        }
        // check that username is not greater than 25 characters
        $username = $this->username;
        if (strlen($username) > 50) {
            $this->username = substr($username, 0, 50);
        }

        // check that password is not greater than 50 characters
        $password = $this->password;
        if (strlen($password) > 50) {
            $this->password = substr($password, 0, 50);
        }

//        if (preg_match("/[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]/i", $this->username) || strlen($this->username) < 3) {
//            $this->_error = sprintf(_VALID_AZ09, _PROMPT_UNAME, 2);
//            return false;
//        }

        if ((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email) == false)) {
            $this->_error = _REGWARN_MAIL;
            return false;
        }

        // check for existing username
        $query = "SELECT id"
                . "\n FROM #__users "
                . "\n WHERE username = '$this->username'"
                . "\n AND id != " . (int) $this->id
        ;

        $this->_db->setQuery($query);
        $xid = intval($this->_db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->_error = _REGWARN_INUSE;
            return false;
        }

        if ($mosConfig_uniquemail) {
            // check for existing email
            $query = "SELECT id"
                    . "\n FROM #__users "
                    . "\n WHERE email = '$this->email'"
                    . "\n AND id != " . (int) $this->id
            ;
            $this->_db->setQuery($query);
            $xid = intval($this->_db->loadResult());
            if ($xid && $xid != intval($this->id)) {
                $this->_error = _REGWARN_EMAIL_INUSE;
                return false;
            }
        }

        return true;
    }

    function store($updateNulls = false) {
        global $acl, $migrate;
        $section_value = 'users';

        $k = $this->_tbl_key;
        $key = $this->$k;
        if ($key && !$migrate) {
            // existing record
            $ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
            // syncronise ACL
            // single group handled at the moment
            // trivial to expand to multiple groups
            $groups = $acl->get_object_groups($section_value, $this->$k, 'ARO');
            $acl->del_group_object($groups[0], $section_value, $this->$k, 'ARO');
            $acl->add_group_object($this->gid, $section_value, $this->$k, 'ARO');

            $object_id = $acl->get_object_id($section_value, $this->$k, 'ARO');
            $acl->edit_object($object_id, $section_value, $this->_db->getEscaped($this->name), $this->$k, 0, 0, 'ARO');
        } else {
            // new record
            $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
            // syncronise ACL
            $acl->add_object($section_value, $this->_db->getEscaped($this->name), $this->$k, null, null, 'ARO');
            $acl->add_group_object($this->gid, $section_value, $this->$k, 'ARO');
        }
        if (!$ret) {
            $this->_error = strtolower(get_class($this)) . "::store failed <br />" . $this->_db->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }

    function delete($oid = null) {
        global $acl;

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval($oid);
        }
        $aro_id = $acl->get_object_id('users', $this->$k, 'ARO');
//		$acl->del_object( $aro_id, 'ARO', true );

        $query = "DELETE FROM $this->_tbl"
                . "\n WHERE $this->_tbl_key = '" . $this->$k . "'"
        ;
        $this->_db->setQuery($query);

        if ($this->_db->query()) {
            // cleanup related data
            // :: private messaging
            $query = "DELETE FROM #__messages_cfg"
                    . "\n WHERE user_id = " . $this->$k . ""
            ;
            $this->_db->setQuery($query);
            if (!$this->_db->query()) {
                $this->_error = $this->_db->getErrorMsg();
                return false;
            }
            $query = "DELETE FROM #__messages"
                    . "\n WHERE user_id_to = " . $this->$k . ""
            ;
            $this->_db->setQuery($query);
            if (!$this->_db->query()) {
                $this->_error = $this->_db->getErrorMsg();
                return false;
            }

            return true;
        } else {
            $this->_error = $this->_db->getErrorMsg();
            return false;
        }
    }

    /**
     * Gets the users from a group
     * @param string The value for the group (not used 1.0)
     * @param string The name for the group
     * @param string If RECURSE, will drill into child groups
     * @param string Ordering for the list
     * @return array
     */
    function getUserListFromGroup($value, $name, $recurse = 'NO_RECURSE', $order = 'name') {
        global $acl;

        // Change back in
        //$group_id = $acl->get_group_id( $value, $name, $group_type = 'ARO');
        $group_id = $acl->get_group_id($name, $group_type = 'ARO');
        $objects = $acl->get_group_objects($group_id, 'ARO', 'RECURSE');

        if (isset($objects['users'])) {
            $gWhere = '(id =' . implode(' OR id =', $objects['users']) . ')';

            $query = "SELECT id AS value, name AS text"
                    . "\n FROM #__users"
                    . "\n WHERE block = '0'"
                    . "\n AND " . $gWhere
                    . "\n ORDER BY " . $order
            ;
            $this->_db->setQuery($query);
            $options = $this->_db->loadObjectList();
            return $options;
        } else {
            return array();
        }
    }

}

/**
 * Template Table Class
 *
 * Provides access to the jos_templates table
 * @package Joomla
 */
class mosTemplate extends mosDBTable {

    /** @var int */
    var $id = null;

    /** @var string */
    var $cur_template = null;

    /** @var int */
    var $col_main = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$database) {
        parent::__construct('#__templates', 'id', $database);
    }

}

function formatMobileNumber($num) {
    $num = preg_replace('/[^0-9]/', '', $num);
    if (substr($num, 0, 1) == '+')
        return substr($num, 1);
    if (substr($num, 0, 2) == '61')
        return  $num;
    if (substr($num, 0, 2) == '04' || substr($num, 0, 2) == '05')
        return "61" . substr($num, 1);
    return false;
}

/**
 * Utility function to return a value from a named array or a specified default
 * @param array A named array
 * @param string The key to search for
 * @param mixed The default value to give if no key found
 * @param int An options mask: _MOS_NOTRIM prevents trim, _MOS_ALLOWHTML allows safe html, _MOS_ALLOWRAW allows raw input
 */
define("_MOS_NOTRIM", 0x0001);
define("_MOS_ALLOWHTML", 0x0002);
define("_MOS_ALLOWRAW", 0x0004);

function mosGetParam(&$arr, $name, $def = null, $mask = 0) {
    static $noHtmlFilter = null;
    static $safeHtmlFilter = null;
    $tags = '';
    $attr = '';
    $tag_method = '';
    $attr_method = '';
    $xss_auto = '';
    $return = null;
    if (isset($arr[$name])) {
        $return = $arr[$name];

        if (is_string($return)) {
            //7751
            //$return = utf8_decode($return);
            // trim data
            if (!($mask & _MOS_NOTRIM)) {
                $return = trim($return);
            }

            if ($mask & _MOS_ALLOWRAW) {
                // do nothing
            } else if ($mask & _MOS_ALLOWHTML) {
                // do nothing - compatibility mode
            } else {
                // send to inputfilter
                if (is_null($noHtmlFilter)) {
                    $noHtmlFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);
                }
                $return = $noHtmlFilter->process($return);

                if (empty($return) && is_numeric($def)) {
                    // if value is defined and default value is numeric set variable type to integer
                    $return = intval($return);
                }
            }
            // account for magic quotes setting
            if (!get_magic_quotes_gpc()) {
                $return = addslashes($return);
            }
        }
        return ($name == 'Itemid') ? intval($return) : $return;
    } else {
        return $def;
    }
}

function setStripeOrderAsPaid($order_id = null,$stripeSessionId = null, $payment_place = null,$deliver_country = null) {

    $order_id = $order_id??mosGetParam($_REQUEST, "order_id", "");
    $stripeSessionId = $stripeSessionId??mosGetParam($_REQUEST, 'session_id', '');
    $mosmsg = strip_tags(mosGetParam($_REQUEST, 'mosmsg', 'Payment executed by Stripe Successfully'));
    $deliver_country = $deliver_country??mosGetParam($_REQUEST, 'deliver_country', '');
    $payment_place = $payment_place??mosGetParam($_REQUEST, 'payment_place', '');

    global $database, $mosConfig_offset,$my,$mosConfig_absolute_path,$mosConfig_au_stripe_secret_key,$mosConfig_nz_stripe_secret_key,$mosConfig_mailfrom, $mosConfig_fromname;
    date_default_timezone_set('Australia/Sydney');
    $mysqlDatetime = date("Y-m-d G:i:s");

    if($stripeSessionId && $payment_place) {
        $query = "UPDATE `#__vm_orders` SET `order_status`='A' WHERE `order_id`=" . $order_id . "";
        $database->setQuery($query);
        $database->query();

        try{
            $stripeSecretKey = ($deliver_country == 'NZL') ? $mosConfig_nz_stripe_secret_key : $mosConfig_au_stripe_secret_key;
            require_once $mosConfig_absolute_path . '/includes/stripe/init.php';
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            $stripeSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
            $stripe_transaction_id = ($stripeSession->payment_intent ?? '');
            $stripe_payment_status = ($stripeSession->payment_status??'');
            if($stripe_payment_status == 'paid') {
            $stripe->paymentIntents->update(
                $stripe_transaction_id,
                [
                    'metadata' => [
                        'order_id' => $order_id,
                        'order place method' => $payment_place
                    ],
                    'description' => 'Order# ' . $order_id
                ]
            );

                $username = $my->username??'';
            $query = "INSERT INTO #__vm_order_history(	order_id,
                                                    order_status_code,
                                                    date_added,
                                                    comments,
                                                    user_name)
                    VALUES ('$order_id',
                            'A',
                            '{$mysqlDatetime}',
                            'Payed From: {$payment_place} , Stripe Transaction Id: {$stripe_transaction_id}',
                            '{$username}')";
        $database->setQuery($query);
        $database->query();

        $query = "INSERT INTO #__vm_order_payment(	order_id,
                                                        order_payment_code,
                                                        payment_method_id,
                                                        order_payment_number,
                                                        order_payment_expire,
                                                        order_payment_log,
                                                        order_payment_name,
                                                        order_payment_trans_id)
                        VALUES ({$order_id},
                                '',
                                3,
                                'NOT SAVED',
                                '',
                                '{$mosmsg}',
                                '',
                                '{$stripe_transaction_id}'
                                )";

        $database->setQuery($query);
        $database->query();

        $query = "UPDATE `tbl_stripe_orders_adm_logs` SET `order_status`='paid'
            WHERE `session_id`='" . $database->getEscaped($stripeSessionId) . "' 
            and order_id = '" . $database->getEscaped($order_id) . "'";
        $database->setQuery($query);
        $database->query();

            $query = "SELECT user_email from jos_vm_order_user_info WHERE address_type = 'BT' and order_id = " . $order_id;
            $database->setQuery($query);
            $order = false;
            $database->loadObject($order);
            if ($order && $order->user_email) {
                mosMail($mosConfig_mailfrom, $mosConfig_fromname, $order->user_email, 'Bloomex payment executed', 'Payment executed by Stripe Successfully, order #' . $order_id, 1);
            }

        }
        }catch (Exception $e){
            $_REQUEST['mosmsg'] = 'Session id does not exist in Stripe system';
            unset($_REQUEST['mosmsgsuccess']);
        }
    }
}

function setStripeBulkOrdersAsPaid($bulk_id = null,$stripeSessionId = null, $payment_place = null) {

    $bulk_id = $bulk_id??mosGetParam($_REQUEST, "bulk_id", "");
    $stripeSessionId = $stripeSessionId??mosGetParam($_REQUEST, 'session_id', '');
    $mosmsg = strip_tags(mosGetParam($_REQUEST, 'mosmsg', 'Payment executed by Stripe Successfully'));
    $payment_place = $payment_place??mosGetParam($_REQUEST, 'payment_place', '');

    global $database, $mosConfig_offset,$my,$mosConfig_absolute_path,$mosConfig_au_stripe_secret_key;
    date_default_timezone_set('Australia/Sydney');
    $mysqlDatetime = date("Y-m-d G:i:s");

    if($stripeSessionId && $payment_place) {

        $query = "SELECT * FROM `tbl_company_parse_orders` WHERE `id`=".$bulk_id."";
        $bulk_result = false;
        $database->setQuery($query);
        $database->loadObject($bulk_result);
        if (!$bulk_result) {
            echo "wrong bulk id";
            exit(0);
        }

        require_once $mosConfig_absolute_path . '/includes/stripe/init.php';
        $stripe = new \Stripe\StripeClient($mosConfig_au_stripe_secret_key);
        $stripeSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
        $stripe_transaction_id = ($stripeSession->payment_intent ?? '');
        $stripe_payment_status = ($stripeSession->payment_status??'');
        $stripe_payment_amount = ($stripeSession->amount_total??'');



        if($stripe_payment_status == 'paid') {
            $stripe->paymentIntents->update(
                $stripe_transaction_id,
                [
                    'metadata' => [
                        'Bulk Id ' => $bulk_id,
                        'order place method' => $payment_place
                    ],
                    'description' => 'Bulk Order# ' . $bulk_id
                ]
            );


            $query = "INSERT INTO `tbl_company_parse_payments`
                    (
                        `list_id`,
                        `card_mask`,
                        `amount`,
                        `trans_id`,
                        `date`
                    )
                    VALUES (
                        " . (int)$bulk_id . ",
                        '',
                        '" . $database->getEscaped( $stripe_payment_amount/100) . "',
                        '" . $database->getEscaped($stripe_transaction_id) . "',
                        '" . $mysqlDatetime . "'
                    )";

            $database->setQuery($query);
            $database->query();

            $query = "Update tbl_company_parse_orders set status='paid' 
            WHERE id in ($bulk_id)";
            $database->setQuery($query);
            if (!$database->query()) {
                echo $database->getErrorMsg();
                echo "cant set status to Paid for id=".$bulk_id;
                exit(0);
            }

            $database->setQuery($query);
            $database->query();



            $username = $my->username??'';
            $query_insert_history = "INSERT INTO jos_vm_order_history(	order_id,
												order_status_code,
												date_added,
												customer_notified,
												comments, user_name) VALUES ";

            $query_insert_payment = "INSERT INTO jos_vm_order_payment(	
                                                order_id,
                                                order_payment_code,
                                                payment_method_id,
                                                order_payment_number,
                                                order_payment_expire,
                                                order_payment_log,
                                                order_payment_name,
                                                order_payment_trans_id
												) VALUES ";

            if( $bulk_result->orders && $bulk_id) {
                $orders = unserialize($bulk_result->orders);
                if ($orders) {
                    $order_str = '';
                    foreach ($orders as $order) {
                        $order_str .= $order . ",";

                        $query_insert_history.="('$order',
                        'A',
                        '".$mysqlDatetime."',
                        0,
                        'Payed From: {$payment_place} , Stripe Transaction Id: {$stripe_transaction_id}',
                         '".$database->getEscaped($username)."'),";

                        $query_insert_payment.="('$order',
						'',
						 3,
						'NOT SAVED',
						'',
						'".$database->getEscaped($mosmsg)."',
						'',
						 '".$database->getEscaped($stripe_transaction_id)."'),";

                    }
                    $query_insert_history=rtrim($query_insert_history,',');
                    $database->setQuery($query_insert_history);
                    if (!$database->query()) {
                        echo $database->getErrorMsg();
                        echo "error";
                        exit(0);
                    }



                    $query_insert_payment=rtrim($query_insert_payment,',');
                    $database->setQuery($query_insert_payment);
                    if (!$database->query()) {
                        echo $database->getErrorMsg();
                        echo "error";
                        exit(0);
                    }


                    $order_str = rtrim($order_str, ',');
                    $sql = "UPDATE jos_vm_orders set order_status='A' WHERE order_id in ($order_str)";
                    $database->setQuery($sql);
                    if (!$database->query()) {
                        echo $database->getErrorMsg();
                        echo "error";
                        exit(0);
                    }
                }
            }
            $query = "UPDATE `tbl_stripe_orders_adm_logs` SET `order_status`='paid'
                WHERE `session_id`='" . $database->getEscaped($stripeSessionId) . "' 
                and bulk_id = '".$database->getEscaped($bulk_id)."'";
            $database->setQuery($query);
            $database->query();


        }
    }
}
function addRegisteredUser($email,$upass,$first_name = '',$last_name = '',$phone = '',$country = '') {
    global $database;

    $usertype = 'Registered';
    $gid = '18'; //shopper
    $params = "admin_language=
language=
editor=
helpsite=
timezone=0";
    $command = "INSERT INTO jos_users( name, username, email, usertype, block, gid,registerDate,params,password )
 VALUES( '{$database->getEscaped($first_name)}', '{$database->getEscaped($email)}', '{$database->getEscaped($email)}' , '{$usertype}' , 0, '{$gid}',now(),'{$params}','".$database->getEscaped(md5($upass))."' )";

    $database->setQuery($command);
    $database->query();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }
    $need_id1 = $database->insertid();


    $command = "INSERT INTO jos_core_acl_aro VALUES ('', 'users', '$need_id1', '0', '" . $database->getEscaped($email) . "', 0)";
    $database->setQuery($command);
    $database->query();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }
    $need_id2 = $database->insertid();

    $command = "INSERT INTO jos_core_acl_groups_aro_map VALUES ('18', '', '$need_id2')";
    $database->setQuery($command);
    $database->query();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }


    // Insert billto
    $q = "INSERT INTO jos_vm_user_info (user_info_id,user_id,first_name,last_name,country,phone_1,address_type,address_type_name,cdate,mdate,address_type2,suite,street_number,street_name,perms) VALUES (";
    $q .= "'" . md5(uniqid("VirtueMartIsCool")) . "',";
    $q .= "'" . $need_id1 . "',";
    $q .= "'" . $database->getEscaped($first_name) . "',";
    $q .= "'" . $database->getEscaped($last_name) . "',";
    $q .= "'" . $database->getEscaped($country) . "',";
    $q .= "'" . $database->getEscaped($phone) . "',";
    $q .= "'BT',";
    $q .= "'-default-',";
    $q .= "'" . time() . "',";
    $q .= "'" . time() . "',";
    $q .= "'',";
    $q .= "'',";
    $q .= "'',";
    $q .= "'',";
    $q .= "'shopper')  ";

    $database->setQuery($q);
    $database->query();


    return $need_id1;
}


function getWarehouseFromStateRelation($state)
{
    $warehouseStateRelation = [
        'AT' => 'WH12',
        'NW' => 'WH12',
        'NT' => 'WH14',
        'QL' => 'WH14',
        'SA' => 'WH16',
        'TA' => 'WH15',
        'VI' => 'WH15',
        'WA' => 'p01',

    ];
    return $warehouseStateRelation[$state] ?? 'NOWAREHOUSEASSIGNED';
}

function getCountryByIp()
{
    $ip = getRealIpAddr();

    $url = "http://ip-api.com/php/" . $ip;
    $return = 'AU';
    $curl = curl_init($url); // Create REST Request
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.manifest-v8+xml'));
    $curl_response = curl_exec($curl); // Execute REST Request
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl) . "\n";
    }
    curl_close($curl);

    if ($curl_response) {
        $res = unserialize($curl_response);
        $return = $res['countryCode'];
    }
    return $return;
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Strip slashes from strings or arrays of strings
 * @param mixed The input string or array
 * @return mixed String or array stripped of slashes
 */
function mosStripslashes(&$value) {
    $ret = '';
    if (is_string($value)) {
        $ret = stripslashes($value);
    } else {
        if (is_array($value)) {
            $ret = array();
            foreach ($value as $key => $val) {
                $ret[$key] = mosStripslashes($val);
            }
        } else {
            $ret = $value;
        }
    }
    return $ret;
}

/**
 * Copy the named array content into the object as properties
 * only existing properties of object are filled. when undefined in hash, properties wont be deleted
 * @param array the input array
 * @param obj byref the object to fill of any class
 * @param string
 * @param boolean
 */
function mosBindArrayToObject($array, &$obj, $ignore = '', $prefix = NULL, $checkSlashes = true) {
    if (!is_array($array) || !is_object($obj)) {
        return (false);
    }

    foreach (get_object_vars($obj) as $k => $v) {
        if (substr($k, 0, 1) != '_') {   // internal attributes of an object are ignored
            if (strpos($ignore, $k) === false) {
                if ($prefix) {
                    $ak = $prefix . $k;
                } else {
                    $ak = $k;
                }

                if (isset($array[$ak])) {
                    $obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? mosStripslashes($array[$ak]) : $array[$ak];
                }
            }
        }
    }

    return true;
}

/**
 * Utility function to read the files in a directory
 * @param string The file system path
 * @param string A filter for the names
 * @param boolean Recurse search into sub-directories
 * @param boolean True if to prepend the full path to the file name
 */
function mosReadDirectory($path, $filter = '.', $recurse = false, $fullpath = false) {
    $arr = array();
    if (!@is_dir($path)) {
        return $arr;
    }
    $handle = opendir($path);

    while ($file = readdir($handle)) {
        $dir = mosPathName($path . '/' . $file, false);
        $isDir = is_dir($dir);
        if (($file != ".") && ($file != "..")) {
            if (preg_match("/$filter/", $file)) {
                if ($fullpath) {
                    $arr[] = trim(mosPathName($path . '/' . $file, false));
                } else {
                    $arr[] = trim($file);
                }
            }
            if ($recurse && $isDir) {
                $arr2 = mosReadDirectory($dir, $filter, $recurse, $fullpath);
                $arr = array_merge($arr, $arr2);
            }
        }
    }
    closedir($handle);
    asort($arr);
    return $arr;
}

function mosGetuserShoperGroupId($username) {

    global $database;
    $username = substr(strrchr($username, "@"), 1);
    $select_company_group_id_query = "SELECT company_group_id FROM `company_groups` WHERE  company_domain LIKE '" . $database->getEscaped($username) . "'";
    $database->setQuery($select_company_group_id_query);
    $company_group_row = $database->loadObjectList();
    if ($username == '')
        return false;
    if ($company_group_row) {
        return $company_group_row[0]->company_group_id;
    } else {
        return false;
    }
}


function checkProductExistInShoppingCart($product_id) {

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product) {
            if($product['product_id'] == $product_id){
                return true;
            }
        }
        return false;
    }else {
        return false;
    }

}

function checkShoppingCartContainAlcohol() {

      if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] AS $cart_product) {
                if (isset($cart_product['product_id']) &&
                    isset($cart_product['containAlcohol']) &&
                    $cart_product['containAlcohol'] == '1')
                {
                       return true;
                }
            }
        }
    return false;
}

function getProductNameByProductId($product_id) {

    global $database;
    $query = "SELECT product_name FROM `jos_vm_product` WHERE  product_id = " . $product_id;
    $database->setQuery($query);
    $row = $database->loadObjectList();

    if ($product_id == '')
        return false;
    if ($row) {
        return $row[0]->product_name;
    } else {
        return false;
    }
}

function mosCheckOrderHasCorporateDiscount($order_id) {

    global $database;
    $select_group_query = "SELECT shopper_group_name FROM `jos_vm_orders_extra` WHERE  order_id LIKE '" . $order_id . "' and shopper_discount_value > 0";
    $database->setQuery($select_group_query);
    $group_row = $database->loadObjectList();
    if ($order_id == '')
        return false;
    if ($group_row) {
        return $group_row[0]->shopper_group_name;
    } else {
        return false;
    }
}

function mosChangesNotification($action, $olddata, $newdata, $id) {
    global $seo_changes_notification_email_address, $mosConfig_mailfrom, $mosConfig_fromname, $my, $mosConfig_live_site;
    $subject = '';
    $message = '';
    switch ($action) {
        case 'category_delete':
            $subject = $action . ' action called category id = ' . $id;
            $message = $my->username . ' deleted category ' . $id . ' at ' . date('Y-m-d H:i');
            break;
        case 'category_update':
            $subject = $action . ' action called category id = ' . $id;
            $message = $my->username . ' updated category <strong>' . $id . '</strong> at ' . date('Y-m-d H:i');
            $message .= '<table border="1"><tr><td></td><td>name</td><td>description</td><td>alias</td><td>meta info</td><td>Published</td></tr>';
            $message .= '<tr><td>Old Data</td><td>' . $olddata['category_name'] . '</td><td>' . $olddata['category_description'] . '</td><td>' . $olddata['alias'] . '</td><td>' . $olddata['meta_info'] . '</td><td>' . $olddata['category_publish'] . '</td></tr>';
            $message .= '<tr><td>New Data</td><td>' . $newdata['category_name'] . '</td><td>' . $newdata['category_description'] . '</td><td>' . $newdata['alias'] . '</td><td>' . $newdata['meta_info'] . '</td><td>' . $newdata['category_publish'] . '</td></tr>';
            $message .= '</table>';
            break;
        case 'category_add':
            $subject = $action . ' action called ';
            $message = $my->username . ' added category <strong>' . $id . '</strong> at ' . date('Y-m-d H:i');
            $message .= '<table border="1"><tr><td></td><td>name</td><td>description</td><td>alias</td><td>meta info</td><td>Published</td></tr>';
            $message .= '<tr><td>New Data</td><td>' . $newdata['category_name'] . '</td><td>' . $newdata['category_description'] . '</td><td>' . $newdata['alias'] . '</td><td>' . $newdata['meta_info'] . '</td><td>' . $newdata['category_publish'] . '</td></tr>';
            $message .= '</table>';
            break;
        case 'menu_item_update':
            $subject = $action . ' action called menu item id = ' . $id;
            $message = $my->username . ' Updated Menu <strong>' . $id . '</strong> at ' . date('Y-m-d H:i');
            $message .= '<table border="1"><tr><td></td><td>name</td><td>alias</td><td>type</td><td>Published</td><td>Show</td></tr>';
            $olddata['published'] = ($olddata['published']) ? 'YES' : 'NO';
            $newdata['published'] = ($newdata['published']) ? 'YES' : 'NO';
            $message .= '<tr><td>Old Data</td><td>' . $olddata['name'] . '</td><td>' . $olddata['alias'] . '</td><td>' . $olddata['new_type'] . '</td><td>' . $olddata['published'] . '</td><td>' . ($olddata['show'] ? 'YES' : 'NO') . '</td></tr>';
            $message .= '<tr><td>New Data</td><td>' . $newdata['name'] . '</td><td>' . $newdata['alias'] . '</td><td>' . $newdata['new_type'] . '</td><td>' . $newdata['published'] . '</td><td>' . ($newdata['show'] ? 'YES' : 'NO') . '</td></tr>';
            $message .= '</table>';
            break;
        case 'menu_item_delete':
            $subject = $action . ' action called  menu item id = ' . $id;
            $message = $my->username . ' deleted menu item  <strong>' . $id . '</strong> at ' . date('Y-m-d H:i');
            break;
        case 'menu_item_added':
            $subject = $action . ' action called ';
            $message = $my->username . ' added menu item <strong>' . $id . '</strong> at ' . date('Y-m-d H:i');
            $message .= '<table border="1"><tr><td></td><td>name</td><td>alias</td><td>type</td><td>Published</td><td>Show</td></tr>';
            $newdata['published'] = ($newdata['published']) ? 'YES' : 'NO';
            $message .= '<tr><td>New Data</td><td>' . $newdata['name'] . '</td><td>' . $newdata['alias'] . '</td><td>' . $newdata['new_type'] . '</td><td>' . $newdata['published'] . '</td><td>' . $newdata['show'] . '</td></tr>';
            $message .= '</table>';
            break;
        default:
            break;
    }
    if ($message && $subject) {
        $subject .= ' Site: ' . $mosConfig_live_site;
        mosMail($mosConfig_mailfrom, 'Change Notification', $seo_changes_notification_email_address, $subject, $message, 1);
    }
}

function moslogerrors($subject, $text) {
    global $mosConfig_mailfrom, $mosConfig_error_notification_email_address, $mosConfig_live_site;
    $subject .= ' Site: ' . $mosConfig_live_site;
    mosMail($mosConfig_mailfrom, 'Error Notification ' . $mosConfig_live_site, $mosConfig_error_notification_email_address, $subject, $text, 1);
}

function mosCheckuserShoperGroupinvoice($username) {

    global $database;
    $username = substr(strrchr($username, "@"), 1);
    $select_company_group_id_query = "SELECT company_invoice FROM `company_groups` WHERE  company_domain LIKE '" . $username . "'";
    $database->setQuery($select_company_group_id_query);
    $company_group_row = $database->loadObjectList();
    if ($username == '')
        return false;
    if ($company_group_row) {
        return $company_group_row[0]->company_invoice;
    } else {
        return false;
    }
}

/**
 * Utility function redirect the browser location to another url
 *
 * Can optionally provide a message.
 * @param string The file system path
 * @param string A filter for the names
 */
function mosRedirect($url, $msg = '') {

    global $mainframe;

    // specific filters
    $iFilter = new InputFilter();
    $url = $iFilter->process($url);
    if (!empty($msg)) {
        $msg = $iFilter->process($msg);
    }

    if ($iFilter->badAttributeValue(array('href', $url))) {
        $url = $GLOBALS['mosConfig_live_site'];
    }

    if (trim($msg)) {
        if (strpos($url, '?')) {
            $url .= '&mosmsg=' . urlencode($msg);
        } else {
            $url .= '?mosmsg=' . urlencode($msg);
        }
    }

    if (headers_sent()) {
        echo "<script>document.location.href='$url';</script>\n";
    } else {
        @ob_end_clean(); // clear output buffer
        header('HTTP/1.1 301 Moved Permanently');
        header("Location: " . $url);
    }
    exit();
}

function mosErrorAlert($text, $action = 'window.history.go(-1);', $mode = 1) {
    $text = nl2br($text);
    $text = addslashes($text);
    $text = strip_tags($text);

    switch ($mode) {
        case 2:
            echo "<script>$action</script> \n";
            break;

        case 1:
        default:
            echo "<script>alert('$text'); $action</script> \n";
            //echo '<noscript>';
            //mosRedirect( @$_SERVER['HTTP_REFERER'], $text );
            //echo '</noscript>';
            break;
    }

    exit;
}

function mosTreeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1) {

    if (@$children[$id] && $level <= $maxlevel) {
        foreach ($children[$id] as $v) {
            $id = $v->id;

            if ($type) {
                $pre = '<sup>L</sup>&nbsp;';
                $spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            } else {
                $pre = '- ';
                $spacer = '&nbsp;&nbsp;';
            }

            if ($v->parent == 0) {
                $txt = $v->name;
            } else {
                $txt = $pre . $v->name;
            }
            $pt = $v->parent;
            $list[$id] = $v;
            $list[$id]->treename = "$indent$txt";
            $list[$id]->children = count(@$children[$id]);

            $list = mosTreeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
        }
    }
    return $list;
}

/**
 * Function to strip additional / or \ in a path name
 * @param string The path
 * @param boolean Add trailing slash
 */
function mosPathName($p_path, $p_addtrailingslash = true) {
    $retval = "";

    $isWin = (substr(PHP_OS, 0, 3) == 'WIN');

    if ($isWin) {
        $retval = str_replace('/', '\\', $p_path);
        if ($p_addtrailingslash) {
            if (substr($retval, -1) != '\\') {
                $retval .= '\\';
            }
        }
        // Remove double \\
        $retval = str_replace('\\\\', '\\', $retval);
    } else {
        $retval = str_replace('\\', '/', $p_path);
        if ($p_addtrailingslash) {
            if (substr($retval, -1) != '/') {
                $retval .= '/';
            }
        }
        // Remove double //
        $retval = str_replace('//', '/', $retval);
    }

    return $retval;
}

/**
 * Class mosMambot
 * @package Joomla
 */
class mosMambot extends mosDBTable {

    /** @var int */
    var $id = null;

    /** @var varchar */
    var $name = null;

    /** @var varchar */
    var $element = null;

    /** @var varchar */
    var $folder = null;

    /** @var tinyint unsigned */
    var $access = null;

    /** @var int */
    var $ordering = null;

    /** @var tinyint */
    var $published = null;

    /** @var tinyint */
    var $iscore = null;

    /** @var tinyint */
    var $client_id = null;

    /** @var int unsigned */
    var $checked_out = null;

    /** @var datetime */
    var $checked_out_time = null;

    /** @var text */
    var $params = null;

    function __construct(&$db) {
        parent::__construct('#__mambots', 'id', $db);
    }

}

/**
 * Module database table class
 * @package Joomla
 */
class mosModule extends mosDBTable {

    /** @var int Primary key */
    var $id = null;

    /** @var string */
    var $title = null;

    /** @var string */
    var $showtitle = null;

    /** @var int */
    var $content = null;

    /** @var int */
    var $ordering = null;

    /** @var string */
    var $position = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var boolean */
    var $published = null;

    /** @var string */
    var $module = null;

    /** @var int */
    var $numnews = null;

    /** @var int */
    var $access = null;

    /** @var string */
    var $params = null;

    /** @var string */
    var $iscore = null;

    /** @var string */
    var $client_id = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__modules', 'id', $db);
    }

    // overloaded check function
    function check() {
        // check for valid name
        if (trim($this->title) == '') {
            $this->_error = "Your Module must contain a title.";
            return false;
        }

        return true;
    }

}

/**
 * Session database table class
 * @package Joomla
 */
class mosSession extends mosDBTable {

    /** @var int Primary key */
    var $session_id = null;

    /** @var string */
    var $time = null;

    /** @var string */
    var $userid = null;

    /** @var string */
    var $usertype = null;

    /** @var string */
    var $username = null;

    /** @var time */
    var $gid = null;

    /** @var int */
    var $guest = null;

    /** @var string */
    var $_session_cookie = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__session', 'session_id', $db);
    }

    /**
     * @param string Key search for
     * @param mixed Default value if not set
     * @return mixed
     */
    function get($key, $default = null) {
        return mosGetParam($_SESSION, $key, $default);
    }

    /**
     * @param string Key to set
     * @param mixed Value to set
     * @return mixed The new value
     */
    function set($key, $value) {
        $_SESSION[$key] = $value;
        return $value;
    }

    /**
     * Sets a key from a REQUEST variable, otherwise uses the default
     * @param string The variable key
     * @param string The REQUEST variable name
     * @param mixed The default value
     * @return mixed
     */
    function setFromRequest($key, $varName, $default = null) {
        if (isset($_REQUEST[$varName])) {
            return mosSession::set($key, $_REQUEST[$varName]);
        } else if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return mosSession::set($key, $default);
        }
    }

    /**
     * Insert a new row
     * @return boolean
     */
    function insert() {
        $ret = $this->_db->insertObject($this->_tbl, $this);

        if (!$ret) {
            $this->_error = strtolower(get_class($this)) . "::store failed <br />" . $this->_db->stderr();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Update an existing row
     * @return boolean
     */
    function update($updateNulls = false) {
        $ret = $this->_db->updateObject($this->_tbl, $this, 'session_id', $updateNulls);

        if (!$ret) {
            $this->_error = strtolower(get_class($this)) . "::store failed <br />" . $this->_db->stderr();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Generate a unique session id
     * @return string
     */
    function generateId() {
        $failsafe = 20;
        $randnum = 0;

        while ($failsafe--) {
            $randnum = md5(uniqid(microtime(), 1));
            $new_session_id = mosMainFrame::sessionCookieValue($randnum);

            if ($randnum != '') {
                $query = "SELECT $this->_tbl_key"
                        . "\n FROM $this->_tbl"
                        . "\n WHERE $this->_tbl_key = '$new_session_id'"
                ;
                $this->_db->setQuery($query);
                if (!$result = $this->_db->query()) {
                    die($this->_db->stderr(true));
                }

                if ($this->_db->getNumRows($result) == 0) {
                    break;
                }
            }
        }

        $this->_session_cookie = $randnum;
        $this->session_id = $new_session_id;
    }

    /**
     * @return string The name of the session cookie
     */
    function getCookie() {
        return $this->_session_cookie;
    }

    /**
     * Purge lapsed sessions
     * @return boolean
     */
    function purge($inc = 1800, $and = '') {
        global $mainframe;

        if ($inc == 'core') {
            $past_logged = time() - $mainframe->getCfg('lifetime');
            $past_guest = time() - 900;

            $query = "DELETE FROM $this->_tbl"
                    . "\n WHERE ("
                    // purging expired logged sessions
                    . "\n ( time < '$past_logged' )"
                    . "\n AND guest = 0"
                    . "\n AND gid > 0"
                    . "\n ) OR ("
                    // purging expired guest sessions
                    . "\n ( time < '$past_guest' )"
                    . "\n AND guest = 1"
                    . "\n AND userid = 0"
                    . "\n )"
            ;
        } else {
            // kept for backward compatability
            $past = time() - $inc;
            $query = "DELETE FROM $this->_tbl"
                    . "\n WHERE ( time < '$past' )"
                    . $and
            ;
        }
        $this->_db->setQuery($query);

        return $this->_db->query();
    }

}

function mosObjectToArray($p_obj) {
    $retarray = null;
    if (is_object($p_obj)) {
        $retarray = array();
        foreach (get_object_vars($p_obj) as $k => $v) {
            if (is_object($v))
                $retarray[$k] = mosObjectToArray($v);
            else
                $retarray[$k] = $v;
        }
    }
    return $retarray;
}

/**
 * Checks the user agent string against known browsers
 */
function mosGetBrowser($agent) {
    global $mosConfig_absolute_path;

    require( $mosConfig_absolute_path . '/includes/agent_browser.php' );

    if (preg_match("/msie[\/\sa-z]*([\d\.]*)/i", $agent, $m) && !preg_match("/webtv/i", $agent) && !preg_match("/omniweb/i", $agent) && !preg_match("/opera/i", $agent)) {
        // IE
        return "MS Internet Explorer $m[1]";
    } else if (preg_match("/netscape.?\/([\d\.]*)/i", $agent, $m)) {
        // Netscape 6.x, 7.x ...
        return "Netscape $m[1]";
    } else if (preg_match("/mozilla[\/\sa-z]*([\d\.]*)/i", $agent, $m) && !preg_match("/gecko/i", $agent) && !preg_match("/compatible/i", $agent) && !preg_match("/opera/i", $agent) && !preg_match("/galeon/i", $agent) && !preg_match("/safari/i", $agent)) {
        // Netscape 3.x, 4.x ...
        return "Netscape $m[1]";
    } else {
        // Other
        $found = false;
        foreach ($browserSearchOrder as $key) {
            if (preg_match("/$key.?\/([\d\.]*)/i", $agent, $m)) {
                $name = "$browsersAlias[$key] $m[1]";
                return $name;
                break;
            }
        }
    }

    return 'Unknown';
}

/**
 * Checks the user agent string against known operating systems
 */
function mosGetOS($agent) {
    global $mosConfig_absolute_path;

    require( $mosConfig_absolute_path . '/includes/agent_os.php' );

    foreach ($osSearchOrder as $key) {
        if (preg_match("/$key/i", $agent)) {
            return $osAlias[$key];
            break;
        }
    }

    return 'Unknown';
}

/**
 * @param string SQL with ordering As value and 'name field' AS text
 * @param integer The length of the truncated headline
 */
function mosGetOrderingList($sql, $chop = '30') {
    global $database;

    $order = array();
    $database->setQuery($sql);
    if (!($orders = $database->loadObjectList())) {
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        } else {
            $order[] = mosHTML::makeOption(1, 'first');
            return $order;
        }
    }
    $order[] = mosHTML::makeOption(0, '0 first');
    for ($i = 0, $n = count($orders); $i < $n; $i++) {

        if (strlen($orders[$i]->text) > $chop) {
            $text = substr($orders[$i]->text, 0, $chop) . "...";
        } else {
            $text = $orders[$i]->text;
        }

        $order[] = mosHTML::makeOption($orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
    }
    $order[] = mosHTML::makeOption($orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . ' last');

    return $order;
}

/**
 * Makes a variable safe to display in forms
 *
 * Object parameters that are non-string, array, object or start with underscore
 * will be converted
 * @param object An object to be parsed
 * @param int The optional quote style for the htmlspecialchars function
 * @param string|array An optional single field name or array of field names not
 * 					 to be parsed (eg, for a textarea)
 */
function mosMakeHtmlSafe(&$mixed, $quote_style = ENT_QUOTES, $exclude_keys = '') {
    if (is_object($mixed)) {
        foreach (get_object_vars($mixed) as $k => $v) {
            if (is_array($v) || is_object($v) || $v == NULL || substr($k, 1, 1) == '_') {
                continue;
            }
            if (is_string($exclude_keys) && $k == $exclude_keys) {
                continue;
            } else if (is_array($exclude_keys) && in_array($k, $exclude_keys)) {
                continue;
            }
            $mixed->$k = htmlspecialchars($v, $quote_style);
        }
    }
}

/**
 * Checks whether a menu option is within the users access level
 * @param int Item id number
 * @param string The menu option
 * @param int The users group ID number
 * @param database A database connector object
 * @return boolean True if the visitor's group at least equal to the menu access
 */
function mosMenuCheck($Itemid, $menu_option, $task, $gid) {
    global $database, $mainframe;

    if ($Itemid != '' && $Itemid != 0 && $Itemid != 99999999) {
        $query = "SELECT *"
                . "\n FROM #__menu"
                . "\n WHERE id = $Itemid"
        ;
    } else {
        $dblink = "index.php?option=$menu_option";

        if ($task != '') {
            $dblink .= "&task=$task";
        }

        $query = "SELECT *"
                . "\n FROM #__menu"
                . "\n WHERE link LIKE '$dblink%'"
        ;
    }
    $database->setQuery($query);
    $results = $database->loadObjectList();
    $access = 0;

    foreach ($results as $result) {
        $access = max($access, $result->access);
    }

    // save menu information to global mainframe
    if (isset($results[0])) {
        // loads menu info of particular Itemid
        $mainframe->set('menu', $results[0]);
    } else {
        // loads empty Menu info
        $mainframe->set('menu', new mosMenu($database));
    }

//    return ($access <= $gid);
    return true;
}

/**
 * Returns formated date according to current local and adds time offset
 * @param string date in datetime format
 * @param string format optional format for strftime
 * @param offset time offset if different than global one
 * @returns formated date
 */
function mosFormatDate($date, $format = "", $offset = NULL) {
    global $mosConfig_offset;
    if ($format == '') {
        // %Y-%m-%d %H:%M:%S
        $format = _DATE_FORMAT_LC;
    }
    if (is_null($offset)) {
        $offset = $mosConfig_offset;
    }
    if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)) {
        $date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        $date = $date > -1 ? strftime($format, $date + ($offset * 60 * 60)) : '-';
    }
    return $date;
}

/**
 * Returns current date according to current local and time offset
 * @param string format optional format for strftime
 * @returns current date
 */
function mosCurrentDate($format = "") {
    global $mosConfig_offset;
    if ($format == "") {
        $format = _DATE_FORMAT_LC;
    }
    $date = strftime($format, time() + ($mosConfig_offset * 60 * 60));
    return $date;
}

/**
 * Utility function to provide ToolTips
 * @param string ToolTip text
 * @param string Box title
 * @returns HTML code for ToolTip
 */
function mosToolTip($tooltip, $title = '', $width = '', $image = 'tooltip.png', $text = '', $href = '#', $link = 1) {
    global $mosConfig_live_site;

    if ($width) {
        $width = ', WIDTH, \'' . $width . '\'';
    }
    if ($title) {
        $title = ', CAPTION, \'' . $title . '\'';
    }
    if (!$text) {
        $image = $mosConfig_live_site . '/includes/js/ThemeOffice/' . $image;
        $text = '<img src="' . $image . '" border="0" alt="tooltip"/>';
    }
    $style = 'style="text-decoration: none; color: #333;"';
    if ($href) {
        $style = '';
    } else {
        $href = '#';
    }

    $mousover = 'return overlib(\'' . $tooltip . '\'' . $title . ', BELOW, RIGHT' . $width . ');';

    $tip = "<!-- Tooltip -->\n";
    if ($link) {
        $tip .= '<a href="' . $href . '" onmouseover="' . $mousover . '" onmouseout="return nd();" ' . $style . '>' . $text . '</a>';
    } else {
        $tip .= '<span onmouseover="' . $mousover . '" onmouseout="return nd();" ' . $style . '>' . $text . '</span>';
    }

    return $tip;
}

/**
 * Utility function to provide Warning Icons
 * @param string Warning text
 * @param string Box title
 * @returns HTML code for Warning
 */
function mosWarning($warning, $title = 'Joomla! Warning') {
    global $mosConfig_live_site;

    $mouseover = 'return overlib(\'' . $warning . '\', CAPTION, \'' . $title . '\', BELOW, RIGHT);';

    $tip = "<!-- Warning -->\n";
    $tip .= '<a href="javascript:void(0)" onmouseover="' . $mouseover . '" onmouseout="return nd();">';
    $tip .= '<img src="' . $mosConfig_live_site . '/includes/js/ThemeOffice/warning.png" border="0"  alt="warning"/></a>';

    return $tip;
}

function mosCreateGUID() {
    srand((double) microtime() * 1000000);
    $r = rand();
    $u = uniqid(getmypid() . $r . (double) microtime() * 1000000, 1);
    $m = md5($u);
    return($m);
}

function mosCompressID($ID) {
    return(Base64_encode(pack("H*", $ID)));
}

function mosExpandID($ID) {
    return ( implode(unpack("H*", Base64_decode($ID)), '') );
}

/**
 * Function to create a mail object for futher use (uses phpMailer)
 * @param string From e-mail address
 * @param string From name
 * @param string E-mail subject
 * @param string Message body
 * @return object Mail object
 */
function mosCreateMail($from = '', $fromname = '', $subject, $body) {
    global $mosConfig_absolute_path, $mosConfig_sendmail;
    global $mosConfig_smtpauth, $mosConfig_smtpuser;
    global $mosConfig_smtppass, $mosConfig_smtphost;
    global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailer;
    global $mosConfig_smtpport;

    $mail = new mosPHPMailer();

    $mail->PluginDir = $mosConfig_absolute_path . '/includes/phpmailer/';
    $mail->SetLanguage('en', $mosConfig_absolute_path . '/includes/phpmailer/language/');
    $mail->CharSet = substr_replace(_ISO, '', 0, 8);
    $mail->IsMail();
    $mail->From = $from ? $from : $mosConfig_mailfrom;
    $mail->FromName = $fromname ? $fromname : $mosConfig_fromname;
    $mail->Mailer = $mosConfig_mailer;

    // Add smtp values if needed
    if ($mosConfig_mailer == 'smtp') {
        $mail->SMTPAuth = $mosConfig_smtpauth;
        $mail->Username = $mosConfig_smtpuser;
        $mail->Password = $mosConfig_smtppass;
        $mail->Host = $mosConfig_smtphost;
        $mail->Port = $mosConfig_smtpport;
    } else

    // Set sendmail path
    if ($mosConfig_mailer == 'sendmail') {
        if (isset($mosConfig_sendmail))
            $mail->Sendmail = $mosConfig_sendmail;
    } // if

    $mail->Subject = $subject;
    $mail->Body = $body;

    return $mail;
}

/**
 * Mail function (uses phpMailer)
 * @param string From e-mail address
 * @param string From name
 * @param string/array Recipient e-mail address(es)
 * @param string E-mail subject
 * @param string Message body
 * @param boolean false = plain text, true = HTML
 * @param string/array CC e-mail address(es)
 * @param string/array BCC e-mail address(es)
 * @param string/array Attachment file name(s)
 * @param string/array ReplyTo e-mail address(es)
 * @param string/array ReplyTo name(s)
 * @return boolean
 */
function mosMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL) {
    global $mosConfig_debug;

    // Filter from, fromname and subject
    if (!JosIsValidEmail($from) || !JosIsValidName($fromname) || !JosIsValidName($subject)) {
        return false;
    }

    $mail = mosCreateMail($from, $fromname, $subject, $body);

    // activate HTML formatted emails
    if ($mode) {
        $mail->IsHTML(true);
    }

    if (is_array($recipient)) {
        foreach ($recipient as $to) {
            if (!JosIsValidEmail($to)) {
                return false;
            }
            $mail->AddAddress($to);
        }
    } else {
        if (!JosIsValidEmail($recipient)) {
            return false;
        }
        $mail->AddAddress($recipient);
    }
    if (isset($cc)) {
        if (is_array($cc)) {
            foreach ($cc as $to) {
                if (!JosIsValidEmail($to)) {
                    return false;
                }
                $mail->AddCC($to);
            }
        } else {
            if (!JosIsValidEmail($cc)) {
                return false;
            }
            $mail->AddCC($cc);
        }
    }
    if (isset($bcc)) {
        if (is_array($bcc)) {
            foreach ($bcc as $to) {
                if (!JosIsValidEmail($to)) {
                    return false;
                }
                $mail->AddBCC($to);
            }
        } else {
            if (!JosIsValidEmail($bcc)) {
                return false;
            }
            $mail->AddBCC($bcc);
        }
    }
    if ($attachment) {
        if (is_array($attachment)) {
            foreach ($attachment as $fname) {
                $mail->AddAttachment($fname);
            }
        } else {
            $mail->AddAttachment($attachment);
        }
    }
    //Important for being able to use mosMail without spoofing...
    if ($replyto) {
        if (is_array($replyto)) {
            reset($replytoname);
            foreach ($replyto as $to) {
                $toname = ((list( $key, $value ) = each($replytoname)) ? $value : '');
                if (!JosIsValidEmail($to) || !JosIsValidName($toname)) {
                    return false;
                }
                $mail->AddReplyTo($to, $toname);
            }
        } else {
            if (!JosIsValidEmail($replyto) || !JosIsValidName($replytoname)) {
                return false;
            }
            $mail->AddReplyTo($replyto, $replytoname);
        }
    }

    $mailssend = $mail->Send();

    if ($mosConfig_debug) {
        //$mosDebug->message( "Mails send: $mailssend");
    }
    if ($mail->error_count > 0) {
        //$mosDebug->message( "The mail message $fromname <$from> about $subject to $recipient <b>failed</b><br /><pre>$body</pre>", false );
        //$mosDebug->message( "Mailer Error: " . $mail->ErrorInfo . "" );
    }
    //return $mailssend;

    return $mail->error_count . '|' . $mail->ErrorInfo;
}


function check_address_verification($order_id) {
    global $mosConfig_adm_link, $mosConfig_adm_auth;
    $service_url = $mosConfig_adm_link . '/scripts/deliveries/fedex/av.php';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_adm_auth);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'order_id=' . $order_id . '&option=CheckAddressValidation');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    $curl_response = curl_exec($curl);
}

// mosMail

/**
 * Checks if a given string is a valid email address
 *
 * @param	string	$email	String to check for a valid email address
 * @return	boolean
 */
function JosIsValidEmail($email) {
    $valid = preg_match('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email);

    return $valid;
}

/**
 * Checks if a given string is a valid (from-)name or subject for an email
 *
 * @since		1.0.11
 * @deprecated	1.5
 * @param		string		$string		String to check for validity
 * @return		boolean
 */
function JosIsValidName($string) {
    /*
     * The following regular expression blocks all strings containing any low control characters:
     * 0x00-0x1F, 0x7F
     * These should be control characters in almost all used charsets.
     * The high control chars in ISO-8859-n (0x80-0x9F) are unused (e.g. http://en.wikipedia.org/wiki/ISO_8859-1)
     * Since they are valid UTF-8 bytes (e.g. used as the second byte of a two byte char),
     * they must not be filtered.
     */
    $invalid = preg_match('/[\x00-\x1F\x7F]/', $string);
    if ($invalid) {
        return false;
    } else {
        return true;
    }
}

/**
 * Initialise GZIP
 */
function initGzip() {
    global $mosConfig_gzip, $do_gzip_compress;

    $do_gzip_compress = FALSE;
    if ($mosConfig_gzip == 1) {
        $phpver = phpversion();
        $useragent = mosGetParam($_SERVER, 'HTTP_USER_AGENT', '');
        $canZip = mosGetParam($_SERVER, 'HTTP_ACCEPT_ENCODING', '');

        $gzip_check = 0;
        $zlib_check = 0;
        $gz_check = 0;
        $zlibO_check = 0;
        $sid_check = 0;
        if (strpos($canZip, 'gzip') !== false) {
            $gzip_check = 1;
        }
        if (extension_loaded('zlib')) {
            $zlib_check = 1;
        }
        if (function_exists('ob_gzhandler')) {
            $gz_check = 1;
        }
        if (ini_get('zlib.output_compression')) {
            $zlibO_check = 1;
        }
        if (ini_get('session.use_trans_sid')) {
            $sid_check = 1;
        }

        if ($phpver >= '4.0.4pl1' && ( strpos($useragent, 'compatible') !== false || strpos($useragent, 'Gecko') !== false )) {
            // Check for gzip header or northon internet securities or session.use_trans_sid
            if (( $gzip_check || isset($_SERVER['---------------']) ) && $zlib_check && $gz_check && !$zlibO_check && !$sid_check) {
                // You cannot specify additional output handlers if
                // zlib.output_compression is activated here
                ob_start('ob_gzhandler');
                return;
            }
        } else if ($phpver > '4.0') {
            if ($gzip_check) {
                if ($zlib_check) {
                    $do_gzip_compress = TRUE;
                    ob_start();
                    ob_implicit_flush(0);

                    header('Content-Encoding: gzip');
                    return;
                }
            }
        }
    }
    ob_start();
}

/**
 * Perform GZIP
 */
function doGzip() {
    global $do_gzip_compress;
    if ($do_gzip_compress) {
        /**
         * Borrowed from php.net!
         */
        $gzip_contents = ob_get_contents();
        ob_end_clean();

        $gzip_size = strlen($gzip_contents);
        $gzip_crc = crc32($gzip_contents);

        $gzip_contents = gzcompress($gzip_contents, 9);
        $gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        echo $gzip_contents;
        echo pack('V', $gzip_crc);
        echo pack('V', $gzip_size);
    } else {
        ob_end_flush();
    }
}
if (!function_exists('getCategoryCanonical')) {

    function getCategoryCanonical($alias) {
        global $database;

        $category_parent_id = 1;

        while ($category_parent_id > 0) {
            $query = "SELECT
            `c`.`alias`,
            `c2`.`alias` AS `parent_alias`,
            `c_x`.`category_parent_id`
        FROM `jos_vm_category` AS `c`
        LEFT JOIN `jos_vm_category_xref` AS `c_x`
            ON `c_x`.`category_child_id`=`c`.`category_id`
        LEFT JOIN `jos_vm_category` AS `c2`
            ON 
            `c2`.`category_id`=`c_x`.`category_parent_id`
            AND
            `c2`.`category_publish`='Y'
        INNER JOIN `jos_vm_category_options` AS `c_o`
            ON
            `c_o`.`category_id`=`c`.`category_id`
            AND
            `c_o`.`sitemap_publish`='1'
        WHERE
            `c`.`alias`='" . $alias . "'
        ";

            $database->setQuery($query);
            $parent_obj = false;
            $database->loadObject($parent_obj);

            if ($parent_obj) {
                $alias = $parent_obj->parent_alias;
                $aliases[] = $parent_obj->alias;
                $category_parent_id = $parent_obj->category_parent_id;
            } else {
                $category_parent_id = 0;
            }
        }

        return '/' . implode('/', array_reverse($aliases)) . '/';;
    }

}
/**
 * Random password generator
 * @return password
 */
function mosMakePassword($length = 8) {
    $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $len = strlen($salt);
    $makepass = '';
    mt_srand(10000000 * (double) microtime());
    for ($i = 0; $i < $length; $i++)
        $makepass .= $salt[mt_rand(0, $len - 1)];
    return $makepass;
}

if (!function_exists('html_entity_decode')) {

    /**
     * html_entity_decode function for backward compatability in PHP
     * @param string
     * @param string
     */
    function html_entity_decode($string, $opt = ENT_COMPAT) {

        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);

        if ($opt & 1) { // Translating single quotes
            // Add single quote to translation table;
            // doesn't appear to be there by default
            $trans_tbl["&apos;"] = "'";
        }

        if (!($opt & 2)) { // Not translating double quotes
            // Remove double quote from translation table
            unset($trans_tbl["&quot;"]);
        }

        return strtr($string, $trans_tbl);
    }

}

/**
 * Plugin handler
 * @package Joomla
 */
class mosMambotHandler {

    /** @var array An array of functions in event groups */
    var $_events = null;

    /** @var array An array of lists */
    var $_lists = null;

    /** @var array An array of mambots */
    var $_bots = null;

    /** @var int Index of the mambot being loaded */
    var $_loading = null;

    /** Added as of 1.0.8 to ensure queries are only called once * */

    /** @var array An array of the content mambots in the system */
    var $_content_mambots = null;

    /** @var array An array of the content mambot params */
    var $_content_mambot_params = array();

    /** @var array An array of the content mambot params */
    var $_search_mambot_params = array();

    /**
     * Constructor
     */
    function __construct() {
        $this->_events = array();
    }

    /**
     * Loads all the bot files for a particular group
     * @param string The group name, relates to the sub-directory in the mambots directory
     */
    function loadBotGroup($group) {
        global $database, $my;

        $group = trim($group);
        if (is_object($my)) {
            $gid = $my->gid;
        } else {
            $gid = 0;
        }

        $group = trim($group);

        switch ($group) {
            case 'content':
                if (!defined('_JOS_CONTENT_MAMBOTS')) {
                    /** ensure that query is only called once */
                    define('_JOS_CONTENT_MAMBOTS', 1);

                    $query = "SELECT folder, element, published, params"
                            . "\n FROM #__mambots"
                            . "\n WHERE access <= $gid"
                            . "\n AND folder = 'content'"
                            . "\n ORDER BY ordering"
                    ;
                    $database->setQuery($query);

                    // load query into class variable _content_mambots
                    if (!($this->_content_mambots = $database->loadObjectList())) {
                        //echo "Error loading Mambots: " . $database->getErrorMsg();
                        return false;
                    }
                }

                // pull bots to be processed from class variable 
                $bots = $this->_content_mambots;
                break;

            default:
                $query = "SELECT folder, element, published, params"
                        . "\n FROM #__mambots"
                        . "\n WHERE published >= 1"
                        . "\n AND access <= $gid"
                        . "\n AND folder = '$group'"
                        . "\n ORDER BY ordering"
                ;
                $database->setQuery($query);

                if (!($bots = $database->loadObjectList())) {
                    //echo "Error loading Mambots: " . $database->getErrorMsg();
                    return false;
                }
                break;
        }

        // load bots found by queries
        $n = count($bots);
        for ($i = 0; $i < $n; $i++) {
            $this->loadBot($bots[$i]->folder, $bots[$i]->element, $bots[$i]->published, $bots[$i]->params);
        }

        return true;
    }

    /**
     * Loads the bot file
     * @param string The folder (group)
     * @param string The elements (name of file without extension)
     * @param int Published state
     * @param string The params for the bot
     */
    function loadBot($folder, $element, $published, $params = '') {
        global $mosConfig_absolute_path;
        global $_MAMBOTS;

        $path = $mosConfig_absolute_path . '/mambots/' . $folder . '/' . $element . '.php';
        if (file_exists($path)) {
            $this->_loading = count($this->_bots);
            $bot = new stdClass;
            $bot->folder = $folder;
            $bot->element = $element;
            $bot->published = $published;
            $bot->lookup = $folder . '/' . $element;
            $bot->params = $params;
            $this->_bots[] = $bot;

            require_once( $path );

            $this->_loading = null;
        }
    }

    /**
     * Registers a function to a particular event group
     * @param string The event name
     * @param string The function name
     */
    function registerFunction($event, $function) {
        $this->_events[$event][] = array($function, $this->_loading);
    }

    /**
     * Makes a option for a particular list in a group
     * @param string The group name
     * @param string The list name
     * @param string The value for the list option
     * @param string The text for the list option
     */
    function addListOption($group, $listName, $value, $text = '') {
        $this->_lists[$group][$listName][] = mosHTML::makeOption($value, $text);
    }

    /**
     * @param string The group name
     * @param string The list name
     * @return array
     */
    function getList($group, $listName) {
        return $this->_lists[$group][$listName];
    }

    /**
     * Calls all functions associated with an event group
     * @param string The event name
     * @param array An array of arguments
     * @param boolean True is unpublished bots are to be processed
     * @return array An array of results from each function call
     */
    function trigger($event, $args = null, $doUnpublished = false) {
        $result = array();

        if ($args === null) {
            $args = array();
        }
        if ($doUnpublished) {
            // prepend the published argument
            array_unshift($args, null);
        }
        if (isset($this->_events[$event])) {
            foreach ($this->_events[$event] as $func) {
                if (function_exists($func[0])) {
                    if ($doUnpublished) {
                        $args[0] = $this->_bots[$func[1]]->published;
                        $result[] = call_user_func_array($func[0], $args);
                    } else if ($this->_bots[$func[1]]->published) {
                        $result[] = call_user_func_array($func[0], $args);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Same as trigger but only returns the first event and
     * allows for a variable argument list
     * @param string The event name
     * @return array The result of the first function call
     */
    function call($event) {
        $doUnpublished = false;

        $args = & func_get_args();
        array_shift($args);

        if (isset($this->_events[$event])) {
            foreach ($this->_events[$event] as $func) {
                if (function_exists($func[0])) {
                    if ($this->_bots[$func[1]]->published) {
                        return call_user_func_array($func[0], $args);
                    }
                }
            }
        }
        return null;
    }

}

/**
 * Tab Creation handler
 * @package Joomla
 */
class mosTabs {

    /** @var int Use cookies */
    var $useCookies = 0;

    /**
     * Constructor
     * Includes files needed for displaying tabs and sets cookie options
     * @param int useCookies, if set to 1 cookie will hold last used tab between page refreshes
     */
    function __construct($useCookies, $xhtml = NULL) {
        global $mosConfig_live_site, $mainframe;

        if ($xhtml) {
            $mainframe->addCustomHeadTag('<link rel="stylesheet" type="text/css" media="all" href="includes/js/tabs/tabpane.css" id="luna-tab-style-sheet" />');
        } else {
            echo "<link id=\"luna-tab-style-sheet\" type=\"text/css\" rel=\"stylesheet\" href=\"" . $mosConfig_live_site . "/includes/js/tabs/tabpane.css\" />";
        }

        echo "<script type=\"text/javascript\" src=\"" . $mosConfig_live_site . "/includes/js/tabs/tabpane_mini.js\"></script>";

        $this->useCookies = $useCookies;
    }

    /**
     * creates a tab pane and creates JS obj
     * @param string The Tab Pane Name
     */
    function startPane($id) {
        echo "<div class=\"tab-page\" id=\"" . $id . "\">";
        echo "<script type=\"text/javascript\">\n";
        echo "	var tabPane1 = new WebFXTabPane( document.getElementById( \"" . $id . "\" ), " . $this->useCookies . " )\n";
        echo "</script>\n";
    }

    /**
     * Ends Tab Pane
     */
    function endPane() {
        echo "</div>";
    }

    /*
     * Creates a tab with title text and starts that tabs page
     * @param tabText - This is what is displayed on the tab
     * @param paneid - This is the parent pane to build this tab on
     */

    function startTab($tabText, $paneid) {
        echo "<div class=\"tab-page\" id=\"" . $paneid . "\">";
        echo "<h2 class=\"tab\">" . $tabText . "</h2>";
        echo "<script type=\"text/javascript\">\n";
        echo "  tabPane1.addTabPage( document.getElementById( \"" . $paneid . "\" ) );";
        echo "</script>";
    }

    /*
     * Ends a tab page
     */

    function endTab() {
        echo "</div>";
    }

}

/**
 * Common HTML Output Files
 * @package Joomla
 */
class mosAdminMenus {

    /**
     * build the select list for Menu Ordering
     */
    static function Ordering(&$row, $id) {
        global $database;

        if ($id) {
            $query = "SELECT ordering AS value, name AS text"
                    . "\n FROM #__menu"
                    . "\n WHERE menutype = '$row->menutype'"
                    . "\n AND parent = $row->parent"
                    . "\n AND published != -2"
                    . "\n ORDER BY ordering"
            ;
            $order = mosGetOrderingList($query);
            $ordering = mosHTML::selectList($order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));
        } else {
            $ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . _CMN_NEW_ITEM_LAST;
        }
        return $ordering;
    }

    /**
     * build the select list for access level
     */
    static function Access(&$row) {
        global $database;

        $query = "SELECT id AS value, name AS text"
                . "\n FROM #__groups"
                . "\n ORDER BY id"
        ;
        $database->setQuery($query);
        $groups = $database->loadObjectList();
        $access = mosHTML::selectList($groups, 'access', 'class="inputbox" size="3"', 'value', 'text', intval($row->access));

        return $access;
    }

    /**
     * build the select list for parent item
     */
    static function Parent(&$row) {
    global $database;

    $id = '';
    if($row->id) {
        $id = "\n AND id != $row->id";
    }

    // get a list of the menu items
    // excluding the current menu item and its child elements
    $query = "SELECT m.*"
            . "\n FROM #__menu m"
            . "\n WHERE menutype = '$row->menutype'"
            . "\n AND published != -2"
            . $id
            . "\n ORDER BY parent, ordering"
    ;
    $database->setQuery($query);
    $mitems = $database->loadObjectList();

    // establish the hierarchy of the menu
    $children = array();

    if ($mitems) {
        // first pass - collect children
        foreach ($mitems as $v) {
            $pt = $v->parent;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }
    }

    // second pass - get an indent list of the items
    $list = mosTreeRecurse(0, '', array(), $children, 20, 0, 0);

    // assemble menu items to the array
    $mitems = array();
    $mitems[] = mosHTML::makeOption('0', 'Top');

    foreach ($list as $item) {
        $mitems[] = mosHTML::makeOption($item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
    }

    $output = mosHTML::selectList($mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', $row->parent);

    return $output;
}

/**
 * build a radio button option for published state
 */
static function Published(&$row) {
    $published = mosHTML::yesnoRadioList('published', 'class="inputbox"', $row->published);
    return $published;
}

function Show(&$row) {
    $show = mosHTML::yesnoRadioList('show', 'class="inputbox"', $row->show);
    return $show;
}

function Nofollow(&$row) {
    $show = mosHTML::yesnoRadioList('nofollow', 'class="inputbox"', $row->nofollow);
    return $show;
}

/**
 * build the link/url of a menu item
 */
static function Link(&$row, $id, $link = NULL) {
    global $mainframe;

    if ($id) {
        switch ($row->type) {
            case 'content_item_link':
            case 'content_typed':
                // load menu params
                $params = new mosParameters($row->params, $mainframe->getPath('menu_xml', $row->type), 'menu');

                if ($params->get('unique_itemid')) {
                    $row->link .= '&Itemid=' . $row->id;
                } else {
                    $temp = explode('&task=view&id=', $row->link);
                    $row->link .= '&Itemid=' . $mainframe->getItemid($temp[1], 0, 0);
                }

                $link = $row->link;
                break;

            default:
                if ($link) {
                    $link = $row->link;
                } else {
                    $link = $row->link . '&amp;Itemid=' . $row->id;
                }
                break;
        }
    } else {
        $link = NULL;
    }

    return $link;
}

/**
 * build the select list for target window
 */
static function Target(&$row) {
    $click[] = mosHTML::makeOption('0', 'Parent Window With Browser Navigation');
    $click[] = mosHTML::makeOption('1', 'New Window With Browser Navigation');
    $click[] = mosHTML::makeOption('2', 'New Window Without Browser Navigation');
    $target = mosHTML::selectList($click, 'browserNav', 'class="inputbox" size="4"', 'value', 'text', intval($row->browserNav));
    return $target;
}

/**
 * build the multiple select list for Menu Links/Pages
 */
static function MenuLinks(&$lookup, $all = NULL, $none = NULL, $unassigned = 1) {
    global $database;

    // get a list of the menu items
    $query = "SELECT m.*"
            . "\n FROM #__menu AS m"
            . "\n WHERE m.published = 1"
            //. "\n AND m.type != 'separator'"
            //. "\n AND NOT ("
            //	. "\n ( m.type = 'url' )"
            //	. "\n AND ( m.link LIKE '%index.php%' )"
            //	. "\n AND ( m.link LIKE '%Itemid=%' )"
            //. "\n )"
            . "\n ORDER BY m.menutype, m.parent, m.ordering"
    ;
    $database->setQuery($query);
    $mitems = $database->loadObjectList();
    $mitems_temp = $mitems;

    // establish the hierarchy of the menu
    $children = array();
    // first pass - collect children
    foreach ($mitems as $v) {
        $id = $v->id;
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
    }
    // second pass - get an indent list of the items
    $list = mosTreeRecurse(intval($mitems[0]->parent), '', array(), $children, 20, 0, 0);

    // Code that adds menu name to Display of Page(s)
    $text_count = 0;
    $mitems_spacer = $mitems_temp[0]->menutype;
    foreach ($list as $list_a) {
        foreach ($mitems_temp as $mitems_a) {
            if ($mitems_a->id == $list_a->id) {
                // Code that inserts the blank line that seperates different menus
                if ($mitems_a->menutype != $mitems_spacer) {
                    $list_temp[] = mosHTML::makeOption(-999, '----');
                    $mitems_spacer = $mitems_a->menutype;
                }

                // do not display `url` menu item types that contain `index.php` and `Itemid`
                if (!($mitems_a->type == 'url' && strpos($mitems_a->link, 'index.php') !== false && strpos($mitems_a->link, 'Itemid=') !== false)) {
                    $text = $mitems_a->menutype . ' | ' . $list_a->treename;
                    $list_temp[] = mosHTML::makeOption($list_a->id, $text);

                    if (strlen($text) > $text_count) {
                        $text_count = strlen($text);
                    }
                }
            }
        }
    }
    $list = $list_temp;

    $mitems = array();
    if ($all) {
        // prepare an array with 'all' as the first item
        $mitems[] = mosHTML::makeOption(0, 'All');
        // adds space, in select box which is not saved
        $mitems[] = mosHTML::makeOption(-999, '----');
    }
    if ($none) {
        // prepare an array with 'all' as the first item
        $mitems[] = mosHTML::makeOption(-999, 'None');
        // adds space, in select box which is not saved
        $mitems[] = mosHTML::makeOption(-999, '----');
    }
    if ($none) {
        // prepare an array with 'all' as the first item
        $mitems[] = mosHTML::makeOption(99999999, 'Unassigned');
        // adds space, in select box which is not saved
        $mitems[] = mosHTML::makeOption(-999, '----');
    }

    // append the rest of the menu items to the array
    foreach ($list as $item) {
        $mitems[] = mosHTML::makeOption($item->value, $item->text);
    }
    $pages = mosHTML::selectList($mitems, 'selections[]', 'class="inputbox" size="26" multiple="multiple"', 'value', 'text', $lookup);
    return $pages;
}

/**
 * build the select list to choose a category
 */
static function Category(&$menu, $id, $javascript = '') {
    global $database;

    $query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`"
            . "\n FROM #__sections AS s"
            . "\n INNER JOIN #__categories AS c ON c.section = s.id"
            . "\n WHERE s.scope = 'content'"
            . "\n ORDER BY s.name, c.name"
    ;
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    $category = '';
    if ($id) {
        foreach ($rows as $row) {
            if ($row->value == $menu->componentid) {
                $category = $row->text;
            }
        }
        $category .= '<input type="hidden" name="componentid" value="' . $menu->componentid . '" />';
        $category .= '<input type="hidden" name="link" value="' . $menu->link . '" />';
    } else {
        $category = mosHTML::selectList($rows, 'componentid', 'class="inputbox" size="10"' . $javascript, 'value', 'text');
        $category .= '<input type="hidden" name="link" value="" />';
    }
    return $category;
}

/**
 * build the select list to choose a section
 */
function Section(&$menu, $id, $all = 0) {
    global $database;

    $query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`"
            . "\n FROM #__sections AS s"
            . "\n WHERE s.scope = 'content'"
            . "\n ORDER BY s.name"
    ;
    $database->setQuery($query);
    if ($all) {
        $rows[] = mosHTML::makeOption(0, '- All Sections -');
        $rows = array_merge($rows, $database->loadObjectList());
    } else {
        $rows = $database->loadObjectList();
    }

    if ($id) {
        foreach ($rows as $row) {
            if ($row->value == $menu->componentid) {
                $section = $row->text;
            }
        }
        $section .= '<input type="hidden" name="componentid" value="' . $menu->componentid . '" />';
        $section .= '<input type="hidden" name="link" value="' . $menu->link . '" />';
    } else {
        $section = mosHTML::selectList($rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text');
        $section .= '<input type="hidden" name="link" value="" />';
    }
    return $section;
}

/**
 * build the select list to choose a component
 */
static function Component(&$menu, $id) {
    global $database;

    $query = "SELECT c.id AS value, c.name AS text, c.link"
            . "\n FROM #__components AS c"
            . "\n WHERE c.link != ''"
            . "\n ORDER BY c.name"
    ;
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if ($id) {
        // existing component, just show name
        $component = '';
        foreach ($rows as $row) {
            if ($row->value == $menu->componentid) {
                $component = $row->text;
            }
        }
        $component .= '<input type="hidden" name="componentid" value="' . $menu->componentid . '" />';
    } else {
        $component = mosHTML::selectList($rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text');
    }
    return $component;
}

/**
 * build the select list to choose a component
 */
static function ComponentName(&$menu, $id) {
    global $database;

    $query = "SELECT c.id AS value, c.name AS text, c.link"
            . "\n FROM #__components AS c"
            . "\n WHERE c.link != ''"
            . "\n ORDER BY c.name"
    ;
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $component = 'Component';
    foreach ($rows as $row) {
        if ($row->value == $menu->componentid) {
            $component = $row->text;
        }
    }

    return $component;
}

/**
 * build the select list to choose an image
 */
function Images($name, &$active, $javascript = NULL, $directory = NULL) {
    global $mosConfig_absolute_path;

    if (!$directory) {
        $directory = '/images/stories';
    }

    if (!$javascript) {
        $javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='..$directory/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
    }

    $imageFiles = mosReadDirectory($mosConfig_absolute_path . $directory);
    $images = array(mosHTML::makeOption('', '- Select Image -'));
    foreach ($imageFiles as $file) {
        if (preg_match("/bmp|gif|jpg|png/i", $file)) {
            $images[] = mosHTML::makeOption($file);
        }
    }
    $images = mosHTML::selectList($images, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

    return $images;
}

/**
 * build the select list for Ordering of a specified Table
 */
function SpecificOrdering(&$row, $id, $query, $neworder = 0) {
    global $database;

    if ($neworder) {
        $text = _CMN_NEW_ITEM_FIRST;
    } else {
        $text = _CMN_NEW_ITEM_LAST;
    }

    if ($id) {
        $order = mosGetOrderingList($query);
        $ordering = mosHTML::selectList($order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));
    } else {
        $ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . $text;
    }
    return $ordering;
}

/**
 * Select list of active users
 */
function UserSelect($name, $active, $nouser = 0, $javascript = NULL, $order = 'name', $reg = 1) {
    global $database, $my;

    $and = '';
    if ($reg) {
        // does not include registered users in the list
        $and = "\n AND gid > 18";
    }

    $query = "SELECT id AS value, name AS text"
            . "\n FROM #__users"
            . "\n WHERE block = 0 and name!=''"
            . $and
            . "\n ORDER BY $order"
    ;
    $database->setQuery($query);
    if ($nouser) {
        $users[] = mosHTML::makeOption('0', '- No User -');
        $users = array_merge($users, $database->loadObjectList());
    } else {
        $users = $database->loadObjectList();
    }

    $users = mosHTML::selectList($users, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

    return $users;
}

/**
 * Select list of positions - generally used for location of images
 */
function Positions($name, $active = NULL, $javascript = NULL, $none = 1, $center = 1, $left = 1, $right = 1) {
    if ($none) {
        $pos[] = mosHTML::makeOption('', _CMN_NONE);
    }
    if ($center) {
        $pos[] = mosHTML::makeOption('center', _CMN_CENTER);
    }
    if ($left) {
        $pos[] = mosHTML::makeOption('left', _CMN_LEFT);
    }
    if ($right) {
        $pos[] = mosHTML::makeOption('right', _CMN_RIGHT);
    }

    $positions = mosHTML::selectList($pos, $name, 'class="inputbox" size="1"' . $javascript, 'value', 'text', $active);

    return $positions;
}

/**
 * Select list of active categories for components
 */
function ComponentCategory($name, $section, $active = NULL, $javascript = NULL, $order = 'ordering', $size = 1, $sel_cat = 1) {
    global $database;

    $query = "SELECT id AS value, name AS text"
            . "\n FROM #__categories"
            . "\n WHERE section = '$section'"
            . "\n AND published = 1"
            . "\n ORDER BY $order"
    ;
    $database->setQuery($query);
    if ($sel_cat) {
        $categories[] = mosHTML::makeOption('0', _SEL_CATEGORY);
        $categories = array_merge($categories, $database->loadObjectList());
    } else {
        $categories = $database->loadObjectList();
    }

    if (count($categories) < 1) {
        mosRedirect('index2.php?option=com_categories&section=' . $section, 'You must create a category first.');
    }

    $category = mosHTML::selectList($categories, $name, 'class="inputbox" size="' . $size . '" ' . $javascript, 'value', 'text', $active);

    return $category;
}

/**
 * Select list of active sections
 */
function SelectSection($name, $active = NULL, $javascript = NULL, $order = 'ordering') {
    global $database;

    $categories[] = mosHTML::makeOption('0', _SEL_SECTION);
    $query = "SELECT id AS value, title AS text"
            . "\n FROM #__sections"
            . "\n WHERE published = 1"
            . "\n ORDER BY $order"
    ;
    $database->setQuery($query);
    $sections = array_merge($categories, $database->loadObjectList());

    $category = mosHTML::selectList($sections, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

    return $category;
}

/**
 * Select list of menu items for a specific menu
 */
function Links2Menu($type, $and) {
    global $database;

    $query = "SELECT *"
            . "\n FROM #__menu"
            . "\n WHERE type = '$type'"
            . "\n AND published = 1"
            . $and
    ;
    $database->setQuery($query);
    $menus = $database->loadObjectList();

    return $menus;
}

/**
 * Select list of menus
 * @param string The control name
 * @param string Additional javascript
 * @return string A select list
 */
function MenuSelect($name = 'menuselect', $javascript = NULL) {
    global $database;

    $query = "SELECT params"
            . "\n FROM #__modules"
            . "\n WHERE module = 'mod_mainmenu'"
    ;
    $database->setQuery($query);
    $menus = $database->loadObjectList();
    $total = count($menus);
    $menuselect = array();
    for ($i = 0; $i < $total; $i++) {
        $params = mosParseParams($menus[$i]->params);
        $menuselect[$i] = new stdClass;
        $menuselect[$i]->value = $params->menutype;
        $menuselect[$i]->text = $params->menutype;
    }
    // sort array of objects
    //SortArrayObjects($menuselect, 'text', 1);

    $menus = mosHTML::selectList($menuselect, $name, 'class="inputbox" size="10" ' . $javascript, 'value', 'text');

    return $menus;
}

/**
 * Internal function to recursive scan the media manager directories
 * @param string Path to scan
 * @param string root path of this folder
 * @param array  Value array of all existing folders
 * @param array  Value array of all existing images
 */
function ReadImages($imagePath, $folderPath, &$folders, &$images) {
    $imgFiles = mosReadDirectory($imagePath);

    foreach ($imgFiles as $file) {
        $ff_ = $folderPath . $file . '/';
        $ff = $folderPath . $file;
        $i_f = $imagePath . '/' . $file;

        if (is_dir($i_f) && $file != 'CVS' && $file != '.svn') {
            $folders[] = mosHTML::makeOption($ff_);
            mosAdminMenus::ReadImages($i_f, $ff_, $folders, $images);
        } else if (preg_match("/bmp|gif|jpg|png/i", $file) && is_file($i_f)) {
            // leading / we don't need
            $imageFile = substr($ff, 1);
            $images[$folderPath][] = mosHTML::makeOption($imageFile, $file);
        }
    }
}

/**
 * Internal function to recursive scan the media manager directories
 * @param string Path to scan
 * @param string root path of this folder
 * @param array  Value array of all existing folders
 * @param array  Value array of all existing images
 */
function ReadImagesX(&$folders, &$images) {
    global $mosConfig_absolute_path;

    if ($folders[0]->value != '*0*') {
        foreach ($folders as $folder) {
            $imagePath = $mosConfig_absolute_path . '/images/stories' . $folder->value;
            $imgFiles = mosReadDirectory($imagePath);
            $folderPath = $folder->value . '/';

            foreach ($imgFiles as $file) {
                $ff = $folderPath . $file;
                $i_f = $imagePath . '/' . $file;

                if (preg_match("/bmp|gif|jpg|png/i", $file) && is_file($i_f)) {
                    // leading / we don't need
                    $imageFile = substr($ff, 1);
                    $images[$folderPath][] = mosHTML::makeOption($imageFile, $file);
                }
            }
        }
    } else {
        $folders = array();
        $folders[] = mosHTML::makeOption('None');
    }
}

function GetImageFolders(&$temps, $path) {
    if ($temps[0]->value != 'None') {
        foreach ($temps as $temp) {
            if (substr($temp->value, -1, 1) != '/') {
                $temp = $temp->value . '/';
                $folders[] = mosHTML::makeOption($temp, $temp);
            } else {
                $temp = $temp->value;
                $temp = ampReplace($temp);
                $folders[] = mosHTML::makeOption($temp, $temp);
            }
        }
    } else {
        $folders[] = mosHTML::makeOption('None Selected');
    }

    $javascript = "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0);\"";
    $getfolders = mosHTML::selectList($folders, 'folders', 'class="inputbox" size="1" ' . $javascript, 'value', 'text', '/');

    return $getfolders;
}

function GetImages(&$images, $path, $base = '/') {
    if (is_array($base) && count($base) > 0) {
        if ($base[0]->value != '/') {
            $base = $base[0]->value . '/';
        } else {
            $base = $base[0]->value;
        }
    } else {
        $base = '/';
    }

    if (!isset($images[$base])) {
        $images[$base][] = mosHTML::makeOption('');
    }

    $javascript = "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
    $getimages = mosHTML::selectList($images[$base], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" ' . $javascript, 'value', 'text', null);

    return $getimages;
}

function GetSavedImages(&$row, $path) {
    $images2 = array();

    foreach ($row->images as $file) {
        $temp = explode('|', $file);
        if (strrchr($temp[0], '/')) {
            $filename = substr(strrchr($temp[0], '/'), 1);
        } else {
            $filename = $temp[0];
        }
        $images2[] = mosHTML::makeOption($file, $filename);
    }

    $javascript = "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \"";
    $imagelist = mosHTML::selectList($images2, 'imagelist', 'class="inputbox" size="10" ' . $javascript, 'value', 'text');

    return $imagelist;
}

/**
 * Checks to see if an image exists in the current templates image directory
 * if it does it loads this image.  Otherwise the default image is loaded.
 * Also can be used in conjunction with the menulist param to create the chosen image
 * load the default or use no image
 */
static function ImageCheck($file, $directory = '/images/M_images/', $param = NULL, $param_directory = '/images/M_images/', $alt = NULL, $name = 'image', $type = 1, $align = 'middle', $title = NULL, $admin = NULL) {
    global $mosConfig_absolute_path, $mosConfig_live_site, $mainframe;

    $cur_template = $mainframe->getTemplate();

    $name = ( $name ? ' name="' . $name . '"' : '' );
    $title = ( $title ? ' title="' . $title . '"' : '' );
    $alt = ( $alt ? ' alt="' . $alt . '"' : ' alt=""' );
    $align = ( $align ? ' align="' . $align . '"' : '' );

    // change directory path from frontend or backend
    if ($admin) {
        $path = '/administrator/templates/' . $cur_template . '/images/';
    } else {
        $path = '/templates/' . $cur_template . '/images/';
    }

    if ($param) {
        $image = $mosConfig_live_site . $param_directory . $param;
        if ($type) {
            $image = '<img src="' . $image . '" ' . $alt . $name . $align . ' border="0" />';
        }
    } else if ($param == -1) {
        $image = '';
    } else {
        if (file_exists($mosConfig_absolute_path . $path . $file)) {
            $image = $mosConfig_live_site . $path . $file;
        } else {
            // outputs only path to image
            $image = $mosConfig_live_site . $directory . $file;
        }

        // outputs actual html <img> tag
        if ($type) {
            $image = '<img src="' . $image . '" ' . $alt . $name . $title . $align . ' border="0" />';
        }
    }

    return $image;
}

/**
 * Checks to see if an image exists in the current templates image directory
 * if it does it loads this image.  Otherwise the default image is loaded.
 * Also can be used in conjunction with the menulist param to create the chosen image
 * load the default or use no image
 */
function ImageCheckAdmin($file, $directory = '/administrator/images/', $param = NULL, $param_directory = '/administrator/images/', $alt = NULL, $name = NULL, $type = 1, $align = 'middle', $title = NULL) {
    /*
      global $mosConfig_absolute_path, $mosConfig_live_site, $mainframe;

      $cur_template = $mainframe->getTemplate();

      $name 	= ( $name 	? ' name="'. $name .'"' 	: '' );
      $title 	= ( $title 	? ' title="'. $title .'"' 	: '' );
      $alt 	= ( $alt 	? ' alt="'. $alt .'"' 		: ' alt=""' );
      $align 	= ( $align 	? ' align="'. $align .'"' 	: '' );

      $path 	= '/administrator/templates/'. $cur_template .'/images/';

      if ( $param ) {
      $image = $mosConfig_live_site. $param_directory . $param;
      if ( $type ) {
      $image = '<img src="'. $image .'" '. $alt . $name . $align .' border="0" />';
      }
      } else if ( $param == -1 ) {
      $image = '';
      } else {
      if ( file_exists( $mosConfig_absolute_path . $path . $file ) ) {
      $image = $mosConfig_live_site . $path . $file;
      } else {
      // outputs only path to image
      $image = $mosConfig_live_site. $directory . $file;
      }

      // outputs actual html <img> tag
      if ( $type ) {
      $image = '<img src="'. $image .'" '. $alt . $name . $title . $align .' border="0" />';
      }
      }
     */
    // functionality consolidated into ImageCheck
    $image = mosAdminMenus::ImageCheck($file, $directory, $param, $param_directory, $alt, $name, $type, $align, $title, $admin = 1);

    return $image;
}

static function menutypes() {
    global $database;

    $query = "SELECT params"
            . "\n FROM #__modules"
            . "\n WHERE module = 'mod_mainmenu'"
            . "\n ORDER BY title"
    ;
    $database->setQuery($query);
    $modMenus = $database->loadObjectList();

    $query = "SELECT menutype"
            . "\n FROM #__menu"
            . "\n GROUP BY menutype"
            . "\n ORDER BY menutype"
    ;
    $database->setQuery($query);
    $menuMenus = $database->loadObjectList();

    $menuTypes = '';
    foreach ($modMenus as $modMenu) {
        $check = 1;
        mosMakeHtmlSafe($modMenu);
        $modParams = mosParseParams($modMenu->params);
        $menuType = @$modParams->menutype;
        if (!$menuType) {
            $menuType = 'mainmenu';
        }

        // stop duplicate menutype being shown
        if (!is_array($menuTypes)) {
            // handling to create initial entry into array
            $menuTypes[] = $menuType;
        } else {
            $check = 1;
            foreach ($menuTypes as $a) {
                if ($a == $menuType) {
                    $check = 0;
                }
            }
            if ($check) {
                $menuTypes[] = $menuType;
            }
        }
    }
    // add menutypes from jos_menu
    foreach ($menuMenus as $menuMenu) {
        $check = 1;
        foreach ($menuTypes as $a) {
            if ($a == $menuMenu->menutype) {
                $check = 0;
            }
        }
        if ($check) {
            $menuTypes[] = $menuMenu->menutype;
        }
    }

    // sorts menutypes
    asort($menuTypes);

    return $menuTypes;
}

/*
 * loads files required for menu items
 */

function menuItem($item) {
    global $mosConfig_absolute_path;

    $path = $mosConfig_absolute_path . '/administrator/components/com_menus/' . $item . '/';
    include_once( $path . $item . '.class.php' );
    include_once( $path . $item . '.menu.html.php' );
}

}

class mosCommonHTML {

function loadCKeditor() {
    global $mosConfig_live_site;
    ?>

    <script src="/ckeditor/ckeditor.js"></script>
    <style>
        .ck-editor__editable_inline {
            min-heigth: 200px !important;
        }
        .ck-editor__editable {
            min-height: 200px;
        }
    </style>
    <?php
}

function loadBootstrap($need_js = false) {
    global $mosConfig_live_site;
    ?>

    <link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/css/bootstrap.min.css" type="text/css" />

    <?php
    if ($need_js == true) {
        ?>
        <script src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/jquery-2.2.3.min.js"></script>
        <script src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/bootstrap.min.js"></script>
        <?php
    }
    ?>
    <?php
}

function loadBootstrap4($need_js = false) {
    global $mosConfig_live_site;
    ?>

    <link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/bootstrap4/css/bootstrap.min.css" type="text/css" />

    <?php
    if ($need_js == true) {
        ?>
        <script src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/jquery-2.2.3.min.js"></script>
        <script src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/bootstrap4/js/bootstrap.min.js"></script>
        <?php
    }
    ?>
    <?php
}

function ContentLegend() {
    ?>
    <table cellspacing="0" cellpadding="4" border="0" align="center">
        <tr align="center">
            <td>
                <img src="images/publish_y.png" width="12" height="12" border="0" alt="Pending" />
            </td>
            <td>
                Published, but is <u>Pending</u> |
            </td>
            <td>
                <img src="images/publish_g.png" width="12" height="12" border="0" alt="Visible" />
            </td>
            <td>
                Published and is <u>Current</u> |
            </td>
            <td>
                <img src="images/publish_r.png" width="12" height="12" border="0" alt="Finished" />
            </td>
            <td>
                Published, but has <u>Expired</u> |
            </td>
            <td>
                <img src="images/publish_x.png" width="12" height="12" border="0" alt="Finished" />
            </td>
            <td>
                Not Published
            </td>
        </tr>
        <tr>
            <td colspan="8" align="center">
                Click on icon to toggle state.
            </td>
        </tr>
    </table>
    <?php
}

function menuLinksContent(&$menus) {
    ?>
    <script language="javascript" type="text/javascript">
            function go2(pressbutton, menu, id) {
                var form = document.adminForm;

                // assemble the images back into one field
                var temp = new Array;
                for (var i = 0, n = form.imagelist.options.length; i < n; i++) {
                    temp[i] = form.imagelist.options[i].value;
                }
                form.images.value = temp.join('\n');

                if (pressbutton == 'go2menu') {
                    form.menu.value = menu;
                    submitform(pressbutton);
                    return;
                }

                if (pressbut        ton == 'go2menuitem') {
                    form.menu.value = menu;
                    form.menuid.value = id;
                    submitform(pressbutton);
                    return;
                }
            }
            </script>
            <?php
            foreach ($menus as $menu) {
                ?>
                <tr>
                    <td colspan="2">
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        Menu
                    </td>
                    <td>
                        <a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );" title="Go to Menu">
                            <?php echo $menu->menutype; ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        Link Name
                    </td>
                    <td>
                        <strong>
                            <a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );" title="Go to Menu Item">
                                <?php echo $menu->name; ?>
                            </a>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        State
                    </td>
                    <td>
                        <?php
                        switch ($menu->published) {
                            case -2:
                                echo '<font color="red">Trashed</font>';
                                break;
                            case 0:
                                echo 'UnPublished';
                                break;
                            case 1:
                            default:
                                echo '<font color="green">Published</font>';
                                break;
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <input type="hidden" name="menu" value="" />
            <input type="hidden" name="menuid" value="" />
            <?php
        }

        function menuLinksSecCat(&$menus) {
            ?>
            <script language="javascript" type="text/javas            cript">
                function go2(pressbutton, menu                , id) {
                var form = document.admi                nForm;

                if (pressbutton == 'go2m                    enu') {
                form.menu.value                     = menu;
                submitform(pressb                    utton);
                return;
                }

                if (pressbutton == 'go2menui                    tem') {
                form.menu.value                     = menu;
                form.menuid.valu                    e = id;
                submitform(pressb                    utton);
                return;
                }
                }
            </script>
            <?php
            foreach ($menus as $menu) {
                ?>
                <tr>
                    <td colspan="2">
                        <hr/>
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        Menu
                    </td>
                    <td>
                        <a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );" title="Go to Menu">
                            <?php echo $menu->menutype; ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        Type
                    </td>
                    <td>
                        <?php echo $menu->type; ?>
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        Item Name
                    </td>
                    <td>
                        <strong>
                            <a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );" title="Go to Menu Item">
                                <?php echo $menu->name; ?>
                            </a>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td width="90px" valign="top">
                        State
                    </td>
                    <td>
                        <?php
                        switch ($menu->published) {
                            case -2:
                                echo '<font color="red">Trashed</font>';
                                break;
                            case 0:
                                echo 'UnPublished';
                                break;
                            case 1:
                            default:
                                echo '<font color="green">Published</font>';
                                break;
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <input type="hidden" name="menu" value="" />
            <input type="hidden" name="menuid" value="" />
            <?php
        }

        static function checkedOut(&$row, $overlib = 1) {
            $hover = '';
            if ($overlib) {
                $date = mosFormatDate($row->checked_out_time, '%A, %d %B %Y');
                $time = mosFormatDate($row->checked_out_time, '%H:%M');
                $checked_out_text = '<table>';
                $checked_out_text .= '<tr><td>' . $row->editor . '</td></tr>';
                $checked_out_text .= '<tr><td>' . $date . '</td></tr>';
                $checked_out_text .= '<tr><td>' . $time . '</td></tr>';
                $checked_out_text .= '</table>';
                $hover = 'onMouseOver="return overlib(\'' . $checked_out_text . '\', CAPTION, \'Checked Out\', BELOW, RIGHT);" onMouseOut="return nd();"';
            }
            $checked = '<img src="images/checked_out.png" ' . $hover . '/>';

            return $checked;
        }

        /*
         * Loads all necessary files for JS Overlib tooltips
         */

        static function loadOverlib() {
            global $mosConfig_live_site, $mainframe;

            if (!$mainframe->get('loadOverlib')) {
                // check if this function is already loaded
                ?>
                <script language="javascript" type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
                <script language="javascript" type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_hideform_mini.js"></script>
                <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
                <?php
                // change state so it isnt loaded a second time
                $mainframe->set('loadOverlib', true);
            }
        }

        /*
         * Loads all necessary files for JS Calendar
         */

        function loadCalendar() {
            global $mosConfig_live_site;
            ?>
            <link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site; ?>/includes/js/calendar/calendar-mos.css" title="green" />
            <!-- import the calendar script -->
            <script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/calendar/calendar_mini.js"></script>
            <!-- import the language module -->
            <script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/calendar/lang/calendar-en.js"></script>
            <?php
        }

        function AccessProcessing(&$row, $i) {
            if (!$row->access) {
                $color_access = 'style="color: green;"';
                $task_access = 'accessregistered';
            } else if ($row->access == 1) {
                $color_access = 'style="color: red;"';
                $task_access = 'accessspecial';
            } else {
                $color_access = 'style="color: black;"';
                $task_access = 'accesspublic';
            }

            $href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task_access . '\')" ' . $color_access . '>
		' . $row->groupname . '
		</a>'
            ;

            return $href;
        }

       static function CheckedOutProcessing(&$row, $i) {
            global $my;
            $checked = '';
            if (isset($row->checked_out)) {
                if ($row->checked_out) {
                    $checked = mosCommonHTML::checkedOut($row);
                } else {
                    $checked = mosHTML::idBox($i, $row->id, ($row->checked_out && $row->checked_out != $my->id));
                }
            } else {
                $checked = mosHTML::idBox($i, $row->id);
            }
            return $checked;
        }

        function PublishedProcessing(&$row, $i) {
            $img = $row->published ? 'publish_g.png' : 'publish_x.png';
            $task = $row->published ? 'unpublish' : 'publish';
            $alt = $row->published ? 'Published' : 'Unpublished';
            $action = $row->published ? 'Unpublish Item' : 'Publish item';

            $href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '">
		<img src="images/' . $img . '" border="0" alt="' . $alt . '" />
		</a>'
            ;

            return $href;
        }

        function ShowProcessing(&$row, $i) {
            $img = $row->show ? 'publish_g.png' : 'publish_x.png';
            $task = $row->show ? 'unshow' : 'show';
            $alt = $row->show ? 'Show' : 'Unshow';
            $action = $row->show ? 'Unshow Item' : 'Show item';

            $href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '">
		<img src="images/' . $img . '" border="0" alt="' . $alt . '" />
		</a>'
            ;

            return $href;
        }

        /*
         * Special handling for newfeed encoding and possible conflicts with page encoding and PHP version
         * Added 1.0.8
         * Static Function
         */

        function newsfeedEncoding($rssDoc, $text) {
            if (!defined('_JOS_FEED_ENCODING')) {
                // determine encoding of feed
                $feed = $rssDoc->toNormalizedString(true);
                $feed = strtolower(substr($feed, 0, 150));
                $feedEncoding = strpos($feed, 'encoding=&quot;utf-8&quot;');

                if ($feedEncoding !== false) {
                    // utf-8 feed
                    $utf8 = 1;
                } else {
                    // non utf-8 page
                    $utf8 = 0;
                }

                define('_JOS_FEED_ENCODING', $utf8);
            }

            if (!defined('_JOS_SITE_ENCODING')) {
                // determine encoding of page
                if (strpos(strtolower(_ISO), 'utf') !== false) {
                    // utf-8 page
                    $utf8 = 1;
                } else {
                    // non utf-8 page
                    $utf8 = 0;
                }

                define('_JOS_SITE_ENCODING', $utf8);
            }

            if (phpversion() >= 5) {
                // handling for PHP 5
                if (_JOS_FEED_ENCODING) {
                    // handling for utf-8 feed
                    if (_JOS_SITE_ENCODING) {
                        // utf-8 page
                        $encoding = 'html_entity_decode';
                    } else {
                        // non utf-8 page
                        $encoding = 'utf8_decode';
                    }
                } else {
                    // handling for non utf-8 feed
                    if (_JOS_SITE_ENCODING) {
                        // utf-8 page
                        $encoding = '';
                    } else {
                        // non utf-8 page
                        $encoding = 'utf8_decode';
                    }
                }
            } else {
                // handling for PHP 4
                if (_JOS_FEED_ENCODING) {
                    // handling for utf-8 feed
                    if (_JOS_SITE_ENCODING) {
                        // utf-8 page
                        $encoding = '';
                    } else {
                        // non utf-8 page
                        $encoding = 'utf8_decode';
                    }
                } else {
                    // handling for non utf-8 feed
                    if (_JOS_SITE_ENCODING) {
                        // utf-8 page
                        $encoding = 'utf8_encode';
                    } else {
                        // non utf-8 page
                        $encoding = 'html_entity_decode';
                    }
                }
            }

            if ($encoding) {
                $text = $encoding($text);
            }
            $text = str_replace('&apos;', "'", $text);

            return $text;
        }

    }

    /**
     * Sorts an Array of objects
     */
    function SortArrayObjects_cmp(&$a, &$b) {
        global $csort_cmp;

        if ($a->$csort_cmp['key'] > $b->$csort_cmp['key']) {
            return $csort_cmp['direction'];
        }

        if ($a->$csort_cmp['key'] < $b->$csort_cmp['key']) {
            return -1 * $csort_cmp['direction'];
        }

        return 0;
    }

    /**
     * Sorts an Array of objects
     * sort_direction [1 = Ascending] [-1 = Descending]
     */
    function SortArrayObjects(&$a, $k, $sort_direction = 1) {
        global $csort_cmp;

        $csort_cmp = array(
            'key' => $k,
            'direction' => $sort_direction
        );

        usort($a, 'SortArrayObjects_cmp');

        unset($csort_cmp);
    }

    /**
     * Sends mail to admin
     */
    function mosSendAdminMail($adminName, $adminEmail, $email, $type, $title, $author) {
        global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_live_site;

        $subject = _MAIL_SUB . " '$type'";
        $message = _MAIL_MSG;
        eval("\$message = \"$message\";");

        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $adminEmail, $subject, $message);
    }

    /*
     * Includes pathway file
     */

    function mosPathWay() {
        global $mosConfig_absolute_path;

        $Itemid = intval(mosGetParam($_REQUEST, 'Itemid', ''));
        require_once ( $mosConfig_absolute_path . '/includes/pathway.php' );
    }

    /**
     * Displays a not authorised message
     *
     * If the user is not logged in then an addition message is displayed.
     */
    function mosNotAuth() {
        global $my;

        echo _NOT_AUTH;
        if ($my->id < 1) {
            echo "<br />" . _DO_LOGIN;
        }
    }

    /**
     * Replaces &amp; with & for xhtml compliance
     *
     * Needed to handle unicode conflicts due to unicode conflicts
     */
    function ampReplace($text) {
        $text = str_replace('&&', '*--*', $text);
        $text = str_replace('&#', '*-*', $text);
        $text = str_replace('&amp;', '&', $text);
        $text = preg_replace('|&(?![\w]+;)|', '&amp;', $text);
        $text = str_replace('*-*', '&#', $text);
        $text = str_replace('*--*', '&&', $text);

        return $text;
    }

    /**
     * Prepares results from search for display
     * @param string The source string
     * @param int Number of chars to trim
     * @param string The searchword to select around
     * @return string
     */
    function mosPrepareSearchContent($text, $length = 200, $searchword) {
// strips tags won't remove the actual jscript
        $text = preg_replace("'<script[^>]*>.*?</script>'si", "", $text);
        $text = preg_replace('/{.+?}/', '', $text);

//$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is','\2', $text );
// replace line breaking tags with whitespace
        $text = preg_replace("'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text);

        $text = mosSmartSubstr(strip_tags($text), $length, $searchword);

        return $text;
    }

    /**
     * returns substring of characters around a searchword
     * @param string The source string
     * @param int Number of chars to return
     * @param string The searchword to select around
     * @return string
     */
    function mosSmartSubstr($text, $length = 200, $searchword) {
        $wordpos = strpos(strtolower($text), strtolower($searchword));
        $halfside = intval($wordpos - $length / 2 - strlen($searchword));
        if ($wordpos && $halfside > 0) {
            return '...' . substr($text, $halfside, $length) . '...';
        } else {
            return substr($text, 0, $length);
        }
    }

    /**
     * Chmods files and directories recursively to given permissions. Available from 1.0.0 up.
     * @param path The starting file or directory (no trailing slash)
     * @param filemode Integer value to chmod files. NULL = dont chmod files.
     * @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
     * @return TRUE=all succeeded FALSE=one or more chmods failed
     */
    function mosChmodRecursive($path, $filemode = NULL, $dirmode = NULL) {
        $ret = TRUE;
        if (is_dir($path)) {
            $dh = opendir($path);
            while ($file = readdir($dh)) {
                if ($file != '.' && $file != '..') {
                    $fullpath = $path . '/' . $file;
                    if (is_dir($fullpath)) {
                        if (!mosChmodRecursive($fullpath, $filemode, $dirmode))
                            $ret = FALSE;
                    } else {
                        if (isset($filemode))
                            if (!@chmod($fullpath, $filemode))
                                $ret = FALSE;
                    } // if
                } // if
            } // while
            closedir($dh);
            if (isset($dirmode))
                if (!@chmod($path, $dirmode))
                    $ret = FALSE;
        } else {
            if (isset($filemode))
                $ret = @chmod($path, $filemode);
        } // if
        return $ret;
    }

// mosChmodRecursive

    /**
     * Chmods files and directories recursively to mos global permissions. Available from 1.0.0 up.
     * @param path The starting file or directory (no trailing slash)
     * @param filemode Integer value to chmod files. NULL = dont chmod files.
     * @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
     * @return TRUE=all succeeded FALSE=one or more chmods failed
     */
    function mosChmod($path) {
        global $mosConfig_fileperms, $mosConfig_dirperms;
        $filemode = NULL;
        if ($mosConfig_fileperms != '')
            $filemode = octdec($mosConfig_fileperms);
        $dirmode = NULL;
        if ($mosConfig_dirperms != '')
            $dirmode = octdec($mosConfig_dirperms);
        if (isset($filemode) || isset($dirmode))
            return mosChmodRecursive($path, $filemode, $dirmode);
        return TRUE;
    }

// mosChmod

    /**
     * Function to convert array to integer values
     * @param array
     * @param int A default value to assign if $array is not an array
     * @return array
     */
    function mosArrayToInts(&$array, $default = null) {
        if (is_array($array)) {
            $n = count($array);
            for ($i = 0; $i < $n; $i++) {
                $array[$i] = intval($array[$i]);
            }
        } else {
            if (is_null($default)) {
                return array();
            } else {
                return array($default);
            }
        }
    }

    /*
     * Function to handle an array of integers
     * Added 1.0.11
     */

    function josGetArrayInts($name, $type = NULL) {
        if ($type == NULL) {
            $type = $_POST;
        }

        $array = mosGetParam($type, $name, array(0));

        mosArrayToInts($array);

        if (!is_array($array)) {
            $array = array(0);
        }

        return $array;
    }

    /**
     * Utility class for helping with patTemplate
     */
    class patHTML {

        /**
         * Converts a named array to an array or named rows suitable to option lists
         * @param array The source array[key] = value
         * @param mixed A value or array of selected values
         * @param string The name for the value field
         * @param string The name for selected attribute (use 'checked' for radio of box lists)
         */
        function selectArray(&$source, $selected = null, $valueName = 'value', $selectedAttr = 'selected') {
            if (!is_array($selected)) {
                $selected = array($selected);
            }
            foreach ($source as $i => $row) {
                if (is_object($row)) {
                    $source[$i]->selected = in_array($row->$valueName, $selected) ? $selectedAttr . '="true"' : '';
                } else {
                    $source[$i]['selected'] = in_array($row[$valueName], $selected) ? $selectedAttr . '="true"' : '';
                }
            }
        }

        /**
         * Converts a named array to an array or named rows suitable to checkbox or radio lists
         * @param array The source array[key] = value
         * @param mixed A value or array of selected values
         * @param string The name for the value field
         */
        function checkArray(&$source, $selected = null, $valueName = 'value') {
            patHTML::selectArray($source, $selected, $valueName, 'checked');
        }

        /**
         * @param mixed The value for the option
         * @param string The text for the option
         * @param string The name of the value parameter (default is value)
         * @param string The name of the text parameter (default is text)
         */
        function makeOption($value, $text, $valueName = 'value', $textName = 'text') {
            return array(
                $valueName => $value,
                $textName => $text
            );
        }

        /**
         * Writes a radio pair
         * @param object Template object
         * @param string The template name
         * @param string The field name
         * @param int The value of the field
         * @param array Array of options
         * @param string Optional template variable name
         */
        function radioSet(&$tmpl, $template, $name, $value, $a, $varname = null) {
            patHTML::checkArray($a, $value);

            $tmpl->addVar('radio-set', 'name', $name);
            $tmpl->addRows('radio-set', $a);
            $tmpl->parseIntoVar('radio-set', $template, is_null($varname) ? $name : $varname );
        }

        /**
         * Writes a radio pair
         * @param object Template object
         * @param string The template name
         * @param string The field name
         * @param int The value of the field
         * @param string Optional template variable name
         */
        function yesNoRadio(&$tmpl, $template, $name, $value, $varname = null) {
            $a = array(
                patHTML::makeOption(0, 'No'),
                patHTML::makeOption(1, 'Yes')
            );
            patHTML::radioSet($tmpl, $template, $name, $value, $a, $varname);
        }

    }

    /**
     * Provides a secure hash based on a seed
     * @param string Seed string
     * @return string
     */
    function mosHash($seed) {
        return md5($GLOBALS['mosConfig_secret'] . md5($seed));
    }

    /**
     * Format a backtrace error
     * @since 1.0.5
     */
    function mosBackTrace() {
        if (function_exists('debug_backtrace')) {
            echo '<div align="left">';
            foreach (debug_backtrace() as $back) {
                if (@$back['file']) {
                    echo '<br />' . str_replace($GLOBALS['mosConfig_absolute_path'], '', $back['file']) . ':' . $back['line'];
                }
            }
            echo '</div>';
        }
    }

    function josSpoofCheck($header = NULL, $alt = NULL) {
//$validate = mosGetParam($_POST, josSpoofValue($alt), 0);
//
//// probably a spoofing attack
//if (!$validate) {
//    header('HTTP/1.0 403 Forbidden');
//    mosErrorAlert(_NOT_AUTH);
//    return;
//}
// First, make sure the form was posted from a browser.
// For basic web-forms, we don't care about anything
// other than requests from a browser:   
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            header('HTTP/1.0 403 Forbidden');
            mosErrorAlert(_NOT_AUTH);
            return;
        }

// Make sure the form was indeed POST'ed:
//  (requires your html form to use: action="post")
        if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
            header('HTTP/1.0 403 Forbidden');
            mosErrorAlert(_NOT_AUTH);
            return;
        }

        if ($header) {
            // Attempt to defend against header injections:
            $badStrings = array(
                'Content-Type:',
                'MIME-Version:',
                'Content-Transfer-Encoding:',
                'bcc:',
                'cc:'
            );

            // Loop through each POST'ed value and test if it contains
            // one of the $badStrings:
            foreach ($_POST as $k => $v) {
                foreach ($badStrings as $v2) {
                    if (strpos($v, $v2) !== false) {
                        header("HTTP/1.0 403 Forbidden");
                        mosErrorAlert(_NOT_AUTH);
                        return;
                    }
                }
            }

            // Made it past spammer test, free up some memory
            // and continue rest of script:
            unset($k, $v, $v2, $badStrings);
        }
    }

    /**
     * Method to determine a hash for anti-spoofing variable names
     *
     * @return	string	Hashed var name
     * @static
     */
    function josSpoofValue($alt = NULL) {
        global $mainframe;

        if ($alt) {
            if ($alt == 1) {
                $random = date('Ymd');
            } else {
                $random = $alt . date('Ymd');
            }
        } else {
            $random = date('dmY');
        }
// the prefix ensures that the hash is non-numeric
// otherwise it will be intercepted by globals.php
        $validate = 'j' . mosHash($mainframe->getCfg('db') . $random);

        return $validate;
    }

// ----- NO MORE CLASSES OR FUNCTIONS PASSED THIS POINT -----
// Post class declaration initialisations
// some version of PHP don't allow the instantiation of classes
// before they are defined

    /** @global mosPlugin $_MAMBOTS */
    $_MAMBOTS = new mosMambotHandler();


function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return explode(',', $ip)[0];
}
function includeResource($resource,$place) {
    global $sef;
    $includeResource = false;
    if($resource['alias'] == ''){
        $includeResource = true;
    }

    if($resource['alias'] != '' &&
        array_filter(
            explode(',',$resource['alias']),
            function($item) use ($sef) {
                return trim($item, '/') ==  $sef->real_uri;
            }
        )
    ) {
        $includeResource = true;
    }

    return $includeResource ? $resource[$place] : '';
}

function isMobileDevice() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i"
        , ($_SERVER["HTTP_USER_AGENT"]??''));
}

function getBrowser() {
    $ub = "";
    $u_agent = $_SERVER['HTTP_USER_AGENT']??'';
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }elseif(preg_match('/Firefox/i',$u_agent)){
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }elseif(preg_match('/OPR/i',$u_agent)){
        $bname = 'Opera';
        $ub = "Opera";
    }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
        $bname = 'Apple Safari';
        $ub = "Safari";
    }elseif(preg_match('/Netscape/i',$u_agent)){
        $bname = 'Netscape';
        $ub = "Netscape";
    }elseif(preg_match('/Edge/i',$u_agent)){
        $bname = 'Edge';
        $ub = "Edge";
    }elseif(preg_match('/Trident/i',$u_agent)){
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }else {
            $version= $matches['version'][1]??'';
        }
    }else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

function isSafariBelow14() {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    // Check if it's Safari but not Chrome/Chromium/Edge
    if (strpos($ua, 'Safari') !== false &&
        strpos($ua, 'Chrome') === false &&
        strpos($ua, 'Chromium') === false &&
        strpos($ua, 'Edg') === false) {

        // Try to extract Safari version from "Version/x.y"
        if (preg_match('/Version\/(\d+(\.\d+)?)/', $ua, $matches)) {
            $version = (float) $matches[1];
            return $version < 14;
        }
    }
    return false;
}

    ?>
