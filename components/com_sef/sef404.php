<?php

/**
 * SEF module for Joomla!
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: sef404.php 314 2008-04-01 19:06:29Z silianacom-svn $
 *
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_VALID_MOS'))
    die('Direct Access to this location is not allowed.');
// V 1.2.4.p
define('_OPENSEF', '1');

global $database, $URI, $my, $option, $index, $base,
       $mosConfig_live_site, $mosConfig_lang,$mosConfig_404_category, $shHttpsSave;

// shumisha 2007-03-13 added URL and iso code caching
require_once(sh404SEF_FRONT_ABS_PATH . 'shCache.php');

// Hack in mos database as required
if (!is_object(@$database)) {
    $database = new database($GLOBALS['mosConfig_host'], $GLOBALS['mosConfig_user'], $GLOBALS['mosConfig_password'], $GLOBALS['mosConfig_db'], $GLOBALS['mosConfig_dbprefix']);
}

$REQUEST = $SRU = $_SERVER['REQUEST_URI'];
_log('$URI', $URI);
//echo get_class($URI);die();
// Lets some quick sanity checks
switch ($URI->path) {

    case $base:
    case $base . $index: {
        _log('Processing homepage');
        $option = (isset($_GET['option'])) ? $_GET['option'] : (isset($_REQUEST['option'])) ? $_REQUEST['option'] : null;
        // shumisha : need to reset language to default if we are loading homepage. It's a bad fix !
        // shumisha 2077-03-28 : this breaks language when URL is home, but other data is passed through
        // POST. So let's try to improve by checking also that no option is requested
        if (is_null($option)) {

            _log('$option is not set');

            // V x redirect to Homepage if livesite/index.php or livesite/index.php?lang=xx is requested.
            if ((empty($URI->querystring) && empty($_POST)) // do not redirect if there is some post data!, will break ajax and more
                || ( (count($_GET) + count($_POST)) == 1 && !empty($_REQUEST['lang']))) {
                $shLang = mosGetparam($_REQUEST, 'lang');
                if (!empty($shLang)) {
                    $shTemp = shGetNameFromIsoCode($shLang);
                    if ($shTemp == $mosConfig_lang)
                        $shTemp = '';
                    else
                        $shTemp = $shLang;
                } else
                    $shTemp = '';
                $shAnchor = empty($URI->anchor) ? '' : '#' . $URI->anchor;
                if (!empty($sefConfig->shForcedHomePage)) { // V 1.2.4.t
                    $shTmp = $shTemp . $shAnchor;
                    $dest = shFinalizeURL($sefConfig->shForcedHomePage . (empty($shTmp) ? '' : '/' . $shTmp));
                } else {
                    $shRewriteBit = empty($shTemp) ? '/' : $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
                    $dest = shFinalizeURL($GLOBALS['mosConfig_live_site'] . $shRewriteBit . $shTemp . $shAnchor);
                }
                if (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) { // IIS adds index.php to $_SERVER['REQUEST_URI'], even if it
                    // was not requested by visitor
                    if (empty($sefConfig->shForcedHomePage)) {
                        $target = str_replace('index.php', '', $shSaveRequestURI);
                        $target = str_replace('Index.php', '', $target);  // sometimes IIS uses Index.php instead of index.php
                        $current = $URI->protocol . '://' . $URI->host . (!sh404SEF_USE_NON_STANDARD_PORT || empty($URI->port) ? '' : ':' . $URI->port)
                            . $target . $shAnchor;
                    } else {
                        $current = $dest;  // if there is a splash page, we don't know what to do, so avoid infinite loop
                    }
                } else {
                    $current = $URI->protocol . '://' . $URI->host . (!sh404SEF_USE_NON_STANDARD_PORT || empty($URI->port) ? '' : ':' . $URI->port)
                        . $shSaveRequestURI . $shAnchor;
                }
                if (sh404SEF_REDIRECT_IF_INDEX_PHP && $dest != $current && strpos($current, 'index2.php') === false) {// do not redirect if index2.php as well
                    _log('Redirecting index.php or index.php?lang=xx from ' . $current . ' to ' . $dest);
                    shRedirect($dest);
                }
            }

            if (empty($_GET['lang']) && empty($_REQUEST['lang'])) {
                $_GET['lang'] = shGetIsoCodeFromName($mosConfig_lang);
                $_REQUEST['lang'] = shGetIsoCodeFromName($mosConfig_lang);
            }
            shCheckVMCookieRedirect(); // V 1.2.4.t
            // fix those funky polls by ensuring we have an Itemid for the homepage
            // V 1.2.4.q modified query, first item of menu is not always ordering = 1 !!!
            $query = "SELECT `id`,`link` FROM #__menu where ((`menutype`='mainmenu') AND (`published` > 0) AND (`access` <= '" . (isset($my) ? (intval(@$my->id)) : 0) . "')) ORDER BY parent, ordering";

            //$database->setQuery($query, 0, 1);
            $database->setQuery($query);  // 10/08/2007 22:08:35  mambo compat
            if ($index == 'index.php') {  // prevents loading homepage if accessing index2.php (ajax calls)
                if (($row = $database->loadRow())) {
                    $_GET['Itemid'] = $_REQUEST['Itemid'] = $Itemid = $row[0];
                    $_SERVER['QUERY_STRING'] = $QUERY_STRING = str_replace('index.php?', '', $row[1]) . '&Itemid=' . $Itemid;
                    $REQUEST_URI = $base . 'index.php?' . $QUERY_STRING;
                    $_SERVER['REQUEST_URI'] = $REQUEST_URI;
                    $shCurrentPageNonSef = 'index.php?' . $QUERY_STRING;  // V 1.2.4.s
                    _log('Homepage non-sef = ' . $shCurrentPageNonSef);
                    $matches = array();
                    if (preg_match("/option=([a-zA-Z_0-9]+)/", $QUERY_STRING, $matches)) {
                        $_GET['option'] = $_REQUEST['option'] = $option = $matches[1];
                    }

                    // shumisha V 1.2.4.k better handling of homepage if it is not com_frontpage
                    parse_str($QUERY_STRING, $vars);
                    $_GET = array_merge($_GET, $vars);
                    $_REQUEST = array_merge($_REQUEST, $vars);

                    // version x allow automatic language detection
                    if (!shIsSearchEngine())
                        shGuessLanguageAndRedirect($QUERY_STRING);

                    // V x 01/09/2007 19:07:17 stronger protection
                    if (defined('RG_EMULATION') && RG_EMULATION == 1) {  // only if allowed by config
                        // Extract to globals
                        while (list($key, $value) = each($_GET)) {
                            if ($key != "GLOBALS") {
                                $GLOBALS[$key] = $value;
                            }
                            $GLOBALS['mosConfig_absolute_path'] = sh404SEF_ABS_PATH; // restore absolute path
                        }
                    }

                    unset($matches);
                    if (!headers_sent()) {
                        header('HTTP/1.0 200 OK');
                    } else {
                        $url = $GLOBALS['mosConfig_live_site'] . $_SERVER['QUERY_STRING'];
                        _log('Headers already sent on homepage');
                        die("<br />SH404SEF : headers were already sent when I got control!<br />Killed at line " . __LINE__ . " in " . basename(__FILE__) . ": HEADERS ALREADY SENT (200)<br />URL=" . @$url . '<br />OPTION=' . @$option);
                    }
                }
            }
        }
        // V 1.2.4.j : optionnally redirect non-sef URL to SEF counterpart. This does not work however if Joomfish is active
        // as Joomfish has not been initialiaed at this point in time, and thus cannot translate. So we disable this function
        // if Joomfish is running (but enable it again if JF is running but current language is default languages, as this
        // is what JF will return)
        else {

            if ($sefConfig->shRedirectNonSefToSef && (!empty($URI->url)) && empty($_POST)) {
                // try fetching from DB

                $shSefUrl = null;
                $shNonSefUrl = str_replace(empty($shHttpsSave) ? $mosConfig_live_site : $shHttpsSave, '', $URI->url);
                $shNonSefUrl = ltrim($shNonSefUrl, '/');
                if (!preg_match('/(&|\?)lang=[a-zA-Z]{2,3}/iU', $shNonSefUrl))
                    $shNonSefUrl = shSetURLVar($shNonSefUrl, 'lang', shGetIsoCodeFromName($GLOBALS['mosConfig_lang']));
                $urlType = shGetSefURLFromCacheOrDB($shNonSefUrl, $shSefUrl);
                if ($urlType == sh404SEF_URLTYPE_AUTO || $urlType == sh404SEF_URLTYPE_CUSTOM) {  // found a match
                    $shRewriteBit = $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
                    $shSefUrl = $GLOBALS['mosConfig_live_site'] . $shRewriteBit . ltrim($shSefUrl, '/')
                        . (($URI->anchor) ? "#" . $URI->anchor : '');
                    _log('redirecting non-sef to existing SEF : ' . $shSefUrl);
                    shRedirect($shSefUrl);
                }
                if (!shIsMultilingual() // V 1.2.4.q
                    || (shIsMultilingual() == 'joomfish'  // does not work with Nokkaew
                        && shGetNameFromIsoCode(shDecideRequestLanguage()) == $GLOBALS['mosConfig_lang'] )) { // $mosConfig_lang is still deafult lang, as
                    // language has not been discovered yet
                    $GLOBALS['mosConfig_defaultLang'] = $mosConfig_lang;  // V 1.2.4.t joomfish not initialised so we must do
                    // this otherwise shGetDefaultLanguage will not work
                    $lang = mosGetParam($_GET, 'lang', $mosConfig_lang);
                    $shSefUrl = sefRelToAbs($URI->url, $lang);
                    if (strpos($shSefUrl, 'option=com') === false) {
                        _log('redirecting non-sef to newly created SEF : ' . $shSefUrl);
                        shRedirect($shSefUrl);
                    }
                }
            }
        }
        break;
    }
    case "": {
        die(_COM_SEF_STRANGE . " URI->path=" . $URI->path . ":<br />" . basename(__FILE__) . "-" . __LINE__);
    }
    default: {



        // sanity check ok so process URI
        // strip out the base
        // V 1.2.4.q we may have to decode URl
        // V 1.3.1 always decode !
        //if ($sefConfig->shEncodeUrl) {
        if (!empty($URI->path)) {
            $URI->path = shUrlDecode($URI->path);
            _log('URL decoding of URI->path');
        }
        if (count($URI->querystring) > 0) {
            foreach ($URI->querystring as $key => $value)
                $URI->querystring[$key] = rawurldecode($value);
        }
        if (!empty($URI->anchor)) {
            $URI->anchor = rawurldecode($URI->anchor);
        }
        //}
        // V 1.2.4.t 301 redirect if Virtuemart cookie check. This has to be from a pre-existing link in Search engine index
        shCheckVMCookieRedirect();
        $path = preg_replace("/^" . preg_quote($base, "/") . "/", "", $URI->path);

        $path_array = explode("/", $path);
        _log('Extracted path array : ', $path_array);
        $ext = getExt($path_array);
        $sef_ext_class = "sef_" . $ext['name'];

        // V 1.2.4.p if other than J! SEF, then use sh404SEF to decode
        if ($sef_ext_class != 'sef_content' && $sef_ext_class != 'sef_component') {
            $sef_ext_class = 'sef_404';
            $ext['path'] = sh404SEF_FRONT_ABS_PATH . 'sef_ext.php';
            $ext['name'] = '404';
        }

        //Dit is meestal sef_404 en anders heeft het te maken met sef advance
        require_once($ext['path']);
        eval("\$sef_ext = new $sef_ext_class;");

        $pos = 0;

        if (isset($_REQUEST['option'])) {
            $pos = array_search($_REQUEST['option'], $path_array);
        }
        if (!(($sef_ext_class == "sef_content")or ( $sef_ext_class == "sef_component"))) {
            if ($pos == 0) {
                array_unshift($path_array, "option");
            }
        }

        $_SEF_SPACE = $sefConfig->replacement;

        $QUERY_STRING = $sef_ext->revert($path_array, $pos);

        _log('Reverted query string = ' . $QUERY_STRING);
        //http://localhost/sil_base/content/view/12/1/mosConfig_absolute_path,ailleurs/
        shDoSecurityChecks($QUERY_STRING, false); // check this newly created URL
        // V 1.2.4.l added automatic redirect of Joomla standard SEF to sh404SEF URL.
        // V 1.2.4.p restrict automatic redirect to Joomla own sef, otherwise it breaks opensef/sefadvance sef_ext files
        // V x : allow redirect even if Joomfish, if URL is already in DB but check if reverted string is valid
        // may not be so in case of attacks or badly formed J! SEF url
        if (is_valid($QUERY_STRING) && ($sef_ext_class == 'sef_content' || $sef_ext_class == 'sef_component')) {  // if we have Joomla standard SEF
            if ($sefConfig->shRedirectJoomlaSefToSef && $URI->url && empty($_POST)) {// and are set to auto-redirect to SEF URLs
                // try fetching from DB
                $shSefUrl = null;
                $nonSefURL = 'index.php?' . $QUERY_STRING;
                if (strpos($nonSefURL, 'lang=') === false)
                    $nonSefURL = shSetURLVar($nonSefURL, 'lang', shGetIsoCodeFromName($GLOBALS['mosConfig_lang']));
                $urlType = shGetSefURLFromCacheOrDB(shSortURL($nonSefURL), $shSefUrl);
                if ($urlType == sh404SEF_URLTYPE_AUTO || $urlType == sh404SEF_URLTYPE_CUSTOM) {  // found a match
                    $shRewriteBit = $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
                    $shSefUrl = (empty($shHttpsSave) ? $mosConfig_live_site : $shHttpsSave) . $shRewriteBit . ltrim($shSefUrl, '/')
                        . (($URI->anchor) ? "#" . $URI->anchor : '');
                    _log('Redirecting J! sef URl to existing SEF URL : ' . $shSefUrl);
                    shRedirect($shSefUrl);
                }
                if (!shIsMultilingual() // V w 01/09/2007 22:02:40 if no joomfish
                    || (shIsMultilingual() == 'joomfish'   // or joomfish, but default language, then we can redirect
                        && shGetNameFromIsoCode(shDecideRequestLanguage()) == $GLOBALS['mosConfig_lang'])   // $mosConfig_lang is still deafult lang, as
                    // language has not been discovered yet
                ) {
                    $shSefUrl = (empty($shHttpsSave) ? $mosConfig_live_site : $shHttpsSave) . '/index.php?' . $QUERY_STRING;
                    $GLOBALS['mosConfig_defaultLang'] = $mosConfig_lang;  // V x 01/09/2007 22:05:11 joomfish not initialised so we must do
                    // this otherwise shGetDefaultLanguage will not work
                    // check if language in URL
                    $shIsoCode = '';
                    $shTemp = explode('lang,', $shSefUrl);
                    if (!empty($shTemp[1]))
                        $shIsoCode = explode('&', $shTemp[1]);
                    $shSefUrl = sefRelToAbs($shSefUrl, shGetNameFromIsoCode($shIsoCode));
                    if (strpos($shSefUrl, '/content/findkey') === false  // if this is no more a J! SEF URL, then redirect
                        && strpos($shSefUrl, '/content/view') === false && strpos($shSefUrl, '/content/section') === false && strpos($shSefUrl, '/content/category') === false && strpos($shSefUrl, '/content/blogsection') === false && strpos($shSefUrl, '/content/blogcategory') === false && strpos($shSefUrl, '/content/archivesection') === false && strpos($shSefUrl, '/content/archivecategory') === false && strpos($shSefUrl, '/content/new') === false && strpos($shSefUrl, '/content/vote') === false && strpos($shSefUrl, '/component/option') === false) {
                        _log('Redirecting J! sef URl to newly created SEF URL : ' . $shSefUrl);
                        shRedirect($shSefUrl);
                    }
                }
            }
        }
        if (is_valid($QUERY_STRING)) {

            // V x : fix for login/logout in other than defautl language
            if ((isset($_GET['option']) && ($_GET['option'] == 'login' || $_GET['option'] == 'logout')) ||
                (isset($_POST['option']) && ($_POST['option'] == 'login' || $_POST['option'] == 'logout'))) {

                // we must preserve $option no matter what
                $_SERVER['QUERY_STRING'] = '';
                $REQUEST_URI = $base . 'index.php';
                $_SERVER['REQUEST_URI'] = $REQUEST_URI;
                $shCurrentPageNonSef = 'index.php';
                _log('Non-sef URl forced to index.php (login/logout)');
            } else {

                $anchor = ($URI->anchor) ? '#' . $URI->anchor : '';
                $incomingQueryString = str_replace('&amp;', '&', $URI->getQueryString());
                $incomingQueryString = empty($incomingQueryString) ? '' : '&' . $incomingQueryString;
                $_SERVER['QUERY_STRING'] = $QUERY_STRING = str_replace('&?', '&', $QUERY_STRING . $incomingQueryString . $anchor);
                $REQUEST_URI = $base . 'index.php?' . $QUERY_STRING;
                $shCurrentPageNonSef = 'index.php?' . $QUERY_STRING; // V 1.2.4.s
                _log('Non-sef URL : ' . $shCurrentPageNonSef);
                $_SERVER['REQUEST_URI'] = $REQUEST_URI;
                // V 1.2.4.t better handling of params

                parse_str($QUERY_STRING, $vars);
                $_GET = array_merge($_GET, $vars);
                $_REQUEST = array_merge($_REQUEST, $vars);
                //unset($matches); // V 1.2.4.t
                // version x allow automatic language detection
                shGuessLanguageAndRedirect($QUERY_STRING);

                // V x 01/09/2007 19:07:17 stronger protection
                if (defined('RG_EMULATION') && RG_EMULATION == 1) {  // only if allowed by config
                    // Extract to globals
                    while (list($key, $value) = each($_GET)) {
                        if ($key != "GLOBALS") {
                            $GLOBALS[$key] = $value;
                        }
                    }
                    $GLOBALS['mosConfig_absolute_path'] = sh404SEF_ABS_PATH; // restore absolute path
                }
            }
            if (!headers_sent()) {
                header('HTTP/1.0 200 OK');
            } else {
                $url = $GLOBALS['mosConfig_live_site'] . "/index.php?" . $_SERVER['QUERY_STRING'];
                _log('Headers already sent before getting control');
                print_r($path_array);
                die("<br />SH404SEF : headers were already sent when I got control!<br />Killed at line " . __LINE__ . " in " . basename(__FILE__) . ": HEADERS ALREADY SENT (200)<br />URL=" . @$url . '<br />OPTION=' . @$option);
            }
        } else {

            // 1.2.4t let's check if better result with trailing slash
            // or without
            $shLastBit = $path_array[count($path_array) - 1];
            if ((empty($sefConfig->suffix) || (!empty($sefConfig->suffix) && strpos($shLastBit, $sefConfig->suffix) === false) ) && ( empty($sefConfig->addfile) || (!empty($sefConfig->addfile) && strpos($shLastBit, $sefConfig->addfile) === false) )) {
                $shTempPathArray = $path_array;
                if (!empty($shLastBit))   // if URl does not end with a /
                    $shTempPathArray[] = '';  // add one
                else
                    unset($shTempPathArray[count($shTempPathArray) - 1]); // if it ends with a /, remove it
                $shQueryString = $sef_ext->revert($shTempPathArray, $pos);
                if (is_valid($shQueryString)) {  //let's redirect to this new URL
                    $dest = str_replace($mosConfig_live_site . $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '', $URI->url);
                    $dest = trim(str_replace($mosConfig_live_site, '', $dest), '/');
                    _log('Redirecting to same with trailing slash added');
                    shRedirect($mosConfig_live_site . $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode]
                        . $dest . (empty($shLastBit) ? '' : '/'));
                }
            }
            // V 1.3.1 : check for aliases
            $incomingUrl = $path = $database->getEscaped($path);
            if (!empty($URI->querystring)) {
                $tmp = '';
                foreach ($URI->querystring as $k => $v)
                    $tmp .= '&' . $k . '=' . $v;
                $incomingUrl .= '?' . ltrim($tmp, '&');
            }
            $query = "SELECT newurl FROM #__sh404sef_aliases WHERE alias = '" . $incomingUrl . "'";
            $database->setQuery($query);
            $dest = $database->loadResult();

            if (!empty($dest) && $dest != $incomingUrl) {  // redirect to alias
                if ($dest == sh404SEF_HOMEPAGE_CODE) {
                    if (!empty($sefConfig->shForcedHomePage)) {
                        $dest = shFinalizeURL($sefConfig->shForcedHomePage);
                    } else {
                        $dest = shFinalizeURL($GLOBALS['mosConfig_live_site']);
                    }
                } else
                    $dest = sefRelToAbs($dest);

                if ($dest != $incomingUrl) {
                    _log('Redirecting to ' . $dest . ' from alias ' . $incomingUrl);
                    shRedirect($dest);
                }
            }
            // bad URL, so check to see if we've seen it before
            // V 1.2.4.k 404 errors logging is now optional
            if ($sefConfig->shLog404Errors) {
                $query = "SELECT * FROM #__redirection WHERE oldurl = '" . $path . "'";
                $database->setQuery($query);
                $results = $database->loadObjectList();

                if ($results) {
                    // we have, so update counter
                    $database->setQuery("UPDATE #__redirection SET cpt=(cpt+1) WHERE oldurl = '" . $path . "'");
                    $database->query();
                } else {
                    // record the bad URL
                    $query = 'INSERT INTO `#__redirection` ( `cpt` , `rank`, `oldurl` , `newurl` , `dateadd` ) ' // V 1.2.4.q
                        . ' VALUES ( \'1\', \'0\',\'' . $path . '\', \'\', CURDATE() );'
                        . ' ';
                    $database->setQuery($query);
                    $database->query();
                }
                // add more details about 404 into security log file
                if ($sefConfig->shSecLogAttacks) {
                    $sep = "\t";
                    $logData = date('Y-m-d') . $sep . date('H:i:s') . $sep . 'Page not found (404)' . $sep . $_SERVER['REMOTE_ADDR'] . $sep;
                    $logData .= getHostByAddr($_SERVER['REMOTE_ADDR']) . $sep;
                    $logData .= $_SERVER['HTTP_USER_AGENT'] . $sep . $_SERVER['REQUEST_METHOD'] . $sep . $_SERVER['REQUEST_URI'];
                    $logData .= empty($_SERVER['HTTP_REFERER']) ? "\n" : $sep . $_SERVER['HTTP_REFERER'] . "\n";
                    shLogToSecFile($logData);
                }
            }
            // redirect to the error page
            // You MUST create a static content page with the title 404 for this to work properly
            //$mosmsg = "FILE NOT FOUND: ".$path;
            //
            $mosmsg = ' (' . $mosConfig_live_site . '/' . ltrim($path, '/') . ')'; // V 1.2.4.t
            $_GET['mosmsg'] = $_REQUEST['mosmsg'] = $mosmsg;
            $option = "com_content";
            $task = "view";
            //$Front404 = 0;
            // V 1.2.4.t  set Itemid to homepage // V 1.3.1 RC : param to set Itemid
            if (!sh404SEF_PAGE_NOT_FOUND_FORCED_ITEMID) {
                $query = "SELECT `id` FROM #__menu where ((`menutype`='mainmenu') AND (`published` > 0) AND (`access` <= '"
                    . (isset($my) ? (intval(@$my->id)) : 0) . "')) ORDER BY parent, ordering";
                $database->setQuery($query);  // 10/08/2007 22:11:05 mambo compat
                $database->loadObject($shHomePage);
                $Itemid = (empty($shHomePage)) ? null : $shHomePage->id;
            } else
                $Itemid = sh404SEF_PAGE_NOT_FOUND_FORCED_ITEMID;

            if ($sefConfig->page404 == '9999999')  // V 1.2.4.t 404 goes to frontpage not allowed anymore. Protect against older
                $sefConfig->page404 == '0';           // configuration values carried over when upgrading

            if ($sefConfig->page404 == '0') {
                $_SERVER['QUERY_STRING'] = 'option=com_page_not_found';
                $_SERVER['REQUEST_URI'] = $base . 'index.php?' . $_SERVER['QUERY_STRING'];
                $_GET['option'] = $_REQUEST['option'] = 'com_page_not_found';
                $_REQUEST['mosmsg'] = '';
            } else {
                $id = $sefConfig->page404;  // wrong Itemid
                $_SERVER['QUERY_STRING'] = 'option=com_content&task=view&id=' . $id
                    . (empty($Itemid) ? '' : '&Itemid=' . $Itemid)
                    . '&mosmsg=' . $mosmsg;
                $_SERVER['REQUEST_URI'] = $base . 'index.php?' . $_SERVER['QUERY_STRING'];
                $_GET['option'] = $_REQUEST['option'] = $option;
                $_GET['task'] = $_REQUEST['task'] = $task;
                $_GET['Itemid'] = $_REQUEST['Itemid'] = $Itemid;
                $_GET['id'] = $_REQUEST['id'] = $id;
            }
// V 1.2.4.t add HTTP_REFERER def or else sometimes mosmsg is not printed by Joomla
// version u 26/08/2007 13:00:27 added trailing /
            $_SERVER['HTTP_REFERER'] = empty($sefConfig->shForcedHomePage) ?
                trim($mosConfig_live_site, '/') . '/' : $sefConfig->shForcedHomePage;

            if (!headers_sent()) {
                header('HTTP/1.1 404 NOT FOUND');
// V x : include error page, faster thant loading Joomla 404 page. Not recommended though, why not show
// your site ?
                if (is_readable(sh404SEF_FRONT_ABS_PATH . '404-Not-Found.tpl.html')) {
                    $errorPage = file_get_contents(sh404SEF_FRONT_ABS_PATH . '404-Not-Found.tpl.html');
                    if ($errorPage !== false) {
                        $errorPage = str_replace('%sh404SEF_404_URL%', $mosmsg, $errorPage);
                        $errorPage = str_replace('%sh404SEF_404_SITE_URL%', $GLOBALS['mosConfig_live_site'], $errorPage);
                        $errorPage = str_replace('%sh404SEF_404_SITE_NAME%', $GLOBALS['mosConfig_sitename'], $errorPage);
                        echo $errorPage;
                        die();
                    }
                }
            } else {
                $url = sefRelToAbs($GLOBALS['mosConfig_live_site'] . "/index.php?" . $_SERVER['QUERY_STRING']);
                print_r($path_array);
                die("<br />SH404SEF : headers were already sent when I got control!<br />Killed at line " . __LINE__ . " in " . basename(__FILE__) . ": HEADERS ALREADY SENT (200)<br />URL=" . @$url . '<br />OPTION=' . @$option);
            }
        } //end bad url
    }//
}

// V 1.2.4.t check if this is a request for VM cookie check AND done by a search engine
// if so, this has to be an old link left over in search engine index, and  we must 301 redirectt to
// same URl without vmvhk/
function shCheckVMCookieRedirect() {

    global $shCurrentPageURL;

    if (shIsSearchEngine() && strpos($shCurrentPageURL, 'vmchk/') !== false) {
        shRedirect(str_replace('vmchk/', '', $shCurrentPageURL));
    }
}

/*
 * 404SEF SUPPORT FUNCTIONS
 */

function sef_ext_exists($this_name) {
    global $database, $sefConfig;

// check for sef_ext
    $this_name = str_replace($sefConfig->replacement, " ", $this_name);
    $this_name = str_replace('\'', '', $this_name);  // V 1.2.4.t 21/08/2007 20:45:58 bug #165
    $sql = "SELECT `id`,`link` FROM #__menu  WHERE ((`name` LIKE '%" . $this_name . "%') AND (`published` > 0))";
    $database->setQuery($sql);
    $rows = @$database->loadObjectList();

    if ($database->getErrorNum()) {
        die($database->stderr());
    }

    if (@count($rows) > 0) {
        $option = str_replace("index.php?option=", "", $rows[0]->link);
        if (file_exists(sh404SEF_ABS_PATH . "components/$option/sef_ext.php")) {
            return @$rows[0];
        } else {
            unset($rows);
        }
    }

    return null;
}

function getExt($URL_ARRAY) {
    global $database, $sefConfig;

    $ext = array();
    $row = sef_ext_exists($URL_ARRAY[0]);
    $ext['path'] = sh404SEF_FRONT_ABS_PATH . 'sef_ext.php';

    if (is_object($row)) {
        $_GET['option'] = $_REQUEST['option'] = $option = str_replace("index.php?option=", "", $row->link);
        $_GET['Itemid'] = $_REQUEST['Itemid'] = $row->id;
        $ext['path'] = sh404SEF_ABS_PATH . "components/$option/sef_ext.php";
    } elseif ((strpos($URL_ARRAY[0], "com_") !== false) or ( $URL_ARRAY[0] == "component")) {
        $_GET['option'] = $_REQUEST['option'] = $option = "com_component";
    } elseif ($URL_ARRAY[0] == 'content') {
        $_GET['option'] = $_REQUEST['option'] = $option = "com_content";
    } else {
        $option = "404";
    }
    $ext['name'] = str_replace("com_", "", $option);

    return $ext;
}

function is_valid($string) {
    global $base, $index;
    if (empty($string))
        $state = false;
    else if (($string == $index ) | ($string == $base . $index )) {
        $state = true;
    } else {
        $state = false;
        require_once(sh404SEF_FRONT_ABS_PATH . 'sef_ext.php');
        $sef_ext = new sef_404;
        $option = (isset($_GET['option'])) ? $_GET['option'] : (isset($_REQUEST['option'])) ? $_REQUEST['option'] : null;

        $vars = array();
        if (is_null($option)) {
            parse_str($string, $vars);
            if (isset($vars['option'])) {
                $option = $vars['option'];
            }
        }
        switch ($option) {
            case is_null($option):
                break;
            case "login": /* Beat: makes this also compatible with CommunityBuilder login module */
            case "logout": {
                $state = true;
                break;
            }
            default: {
                if (is_valid_component($option)) {
                    if ((!($option == "com_content")) | (!($option == "content"))) {
                        $state = true;
                    } else {
                        $title = $sef_ext->getContentTitles($_REQUEST['task'], $_REQUEST['id']);
                        if (count($title) > 0) {
                            $state = true;
                        }
                    }
                }
// shumisha check if this is homepage+lang=xx
                else {
                    if (substr($string, 0, 5) == 'lang=')
                        $state = true;
                }
// shumisha end of change
            }
        }
    }
    return $state;
}

function is_valid_component($this) {
    $state = false;
    $path = sh404SEF_ABS_PATH . 'components/';

    if (is_dir($path)) {
        if (($contents = opendir($path))) {
            while (($node = readdir($contents)) !== false) {
                if ($node != '.' && $node != '..') {
                    if (is_dir($path . '/' . $node) && $this == $node) {
                        $state = true;
                        break;
                    }
                }
            }
        }
    }
    return $state;
}

// V 1.2.4.t returns sef url with added pagination information
function shAddPaginationInfo($limit, $limitstart, $iteration, $url, $location, $shSeparator = null) {
    global $sefConfig, $database;

    if (empty($shSeparator))
        $shSeparator = (substr($location, -1) == '/') ? '' : '/';
    if (!empty($limit)) {
        $pagenum = intval($limitstart / $limit);
        $pagenum++;
// Make sure we do not end in infite loop here.
        if ($pagenum < $iteration)
            $pagenum = $iteration;
    } else {
        $pagenum = $iteration;
    }
// shumisha added to handle table-category and table-section which may have variable number of items per page
// There still will be a problem with filter, which may reduce the total number of items. Thus the item we are looking for
    if ((strpos($url, 'option=com_search')) || (strpos($url, 'option=com_content') &&
            ( (strpos($url, 'task=category'))
//|| (strpos( $url, 'task=blogcategory'))
                || (strpos($url, 'task=section'))
//|| (strpos( $url, 'task=blogsection'))
            )) || (strpos($url, 'option=com_virtuemart') && $sefConfig->shVmUsingItemsPerPage)) {
        $shMultPageLength = $sefConfig->pagerep . $limit;
    } else
        $shMultPageLength = '';
// shumisha : modified to add # of items per page to URL, for table-category or section-category

    if (!empty($sefConfig->pageTexts[$GLOBALS['mosConfig_lang']]) && (false !== strpos($sefConfig->pageTexts[$GLOBALS['mosConfig_lang']], '%s'))) {
        $page = str_replace('%s', $pagenum, $sefConfig->pageTexts[$GLOBALS['mosConfig_lang']]) . $shMultPageLength;
    } else {
        $page = $sefConfig->pagerep . $pagenum . $shMultPageLength;
    }
// V 1.2.4.t special processing to replace page number by headings
    $shPageNumberWasReplaced = false;
    if ($sefConfig->shMultipagesTitle && strpos($url, 'option=com_content') !== false && strpos($url, 'task=view') !== false && !empty($limit)) {  // this is multipage article
        parse_str($url, $shParams);
        if (!empty($shParams['id'])) {
            $shPageTitle = '';
            $sql = 'SELECT c.id, c.fulltext, c.introtext  FROM #__content AS c WHERE id=\'' . $shParams['id'] . '\'';
            $database->setQuery($sql);
            $database->loadObject($contentElement);
            if ($database->getErrorNum()) {
                die($database->stderr());
            }
            $contentText = $contentElement->introtext . $contentElement->fulltext;
            if (!empty($contentElement) && ( strpos($contentText, 'mospagebreak') !== false )) { // search for mospagebreak tags
// copied over from mosPaging mambot
// expression to search for
                $regex = '/{(mospagebreak)\s*(.*?)}/i';
// find all instances of mambot and put in $matches
                $shMatches = array();
                preg_match_all($regex, $contentText, $shMatches, PREG_SET_ORDER);
// adds heading or title to <site> Title
                if (empty($limitstart)) {  // if first page use heading of first mospagebreak
                    /* if ( $shMatches[0][2] ) {
                      parse_str( html_entity_decode( $shMatches[0][2] ), $args );
                      if ( @$args['heading'] ) {
                      $shPageTitle = stripslashes( $args['heading'] );
                      }
                      } */
                } else {  // for other pages use title of mospagebreak
                    if ($shMatches[$limitstart - 1][2]) {
                        parse_str(html_entity_decode($shMatches[$limitstart - 1][2]), $args);
                        if (@$args['title']) {
                            $shPageTitle = stripSlashes($args['title']);
                        }
                    }
                }
            }
            if (!empty($shPageTitle)) { // found a heading, we should use that as a Title
                $location .= $shSeparator . titleToLocation($shPageTitle);
                $shPageNumberWasReplaced = true;
            }
        }
    }

    // V u 26/08/2007 10:35:38 trim url as per user settings
    $suffixthere = 0;
    if (!empty($sefConfig->suffix) && ($sefConfig->suffix != '/') && subStr($location, -strLen($sefConfig->suffix)) == $sefConfig->suffix)
        $suffixthere = strLen($sefConfig->suffix);
    $takethese = str_replace('|', '', $sefConfig->friendlytrim);
    $location = trim(substr($location, 0, strlen($location) - $suffixthere), $takethese);

    // add page number
    if (!$shPageNumberWasReplaced && isset($limitstart) && ($limitstart != 0          // if not first page, add items per page
            || ($limitstart == 0         // if first page, we may add number of items per page if the
                && ((strpos($url, 'option=com_virtuemart')   // requested number of items per page is not the default one
                        && $sefConfig->shVmUsingItemsPerPage && $limit != $GLOBALS['mosConfig_list_limit']  // // for Virtuemart, default is Joomla global default
                    ) || (
                        ((strpos($url, 'option=com_search')) || (strpos($url, 'option=com_content') && // for regular content, default
                                ((strpos($url, 'task=category')) || (strpos($url, 'task=section')))))   // can be set for each menu item
                        && $limit != shGetDefaultDisplayNumFromURL($url)   // so we need to fetch the display_num param for the
                    )              // Itemid found in the URL, if any. if none, use default
                )
            )
        )
    ) {
        $location .= $shSeparator . $page;
    }
    // add suffix
    if ($sefConfig->suffix && $location != '/' && substr($location, -1) != '/') {
        $location = $sefConfig->suffix == '/' ?
            $location . $sefConfig->suffix : str_replace($sefConfig->suffix, '', $location) . $sefConfig->suffix;
    }

    // add default index file
    if ($sefConfig->addFile) { // V 1.2.4.t
        if ((empty($sefConfig->suffix) || (!empty($sefConfig->suffix) && subStr($location, -strLen($sefConfig->suffix)) != $sefConfig->suffix)))
            $location .= (substr($location, -1) == '/' ? '' : '/') . $sefConfig->addFile;
    }
    return ltrim($location, '/');
}

// V 1.2.4.m
global $shHomeLink;
$shHomeLink = null;

function sefRelToAbs($string, $shLanguageParam = null) {

    global $database, $sefConfig, $_SEF_SPACE, $mosConfig_lang,
        // shumisha 2007-03-13 added URL caching
           $shGETVars, $shRebuildNonSef,
        // V 1.2.4.m
           $shHomeLink,
        // V 1.2.4.q
           $mosConfig_live_site, $shHttpsSave, $my;

    _log('Entering sefrelToAbs with ' . $string . ' | Lang = ' . $shLanguageParam);

    // if superadmin, display non-sef URL, for testing/setting up purposes
    if (sh404SEF_NON_SEF_IF_SUPERADMIN && !empty($my) && $my->usertype == 'Super Administrator') {
        _log('Returning non-sef because superadmin said so.');
        return $string;
    }
    // return unmodified anchors
    if (substr($string, 0, 1) == '#') {  // V 1.2.4.t
        return $string;
    }
    // V 1.2.4.q quick fix for shared SSL server : if https, switch to non sef
    if (!empty($shHttpsSave) && $sefConfig->shForceNonSefIfHttps) {
        _log('Returning sefRelToAbs : Forced non sef if https');
        return shFinalizeURL($string);
    }

    $shOrigString = $string;
    $shMosMsg = shGetMosMsg($string); // V x 01/09/2007 22:45:52
    $string = shCleanUpMosMsg($string); // V x 01/09/2007 22:45:52
    // V x : removed shJoomfish module. Now we set $mosConfi_lang here
    $shOrigLang = $mosConfig_lang; // save current language
    if (shIsMultilingual() == 'mambo') { // V x : mambo compat
        $core = & mamboCore::getMamboCore();
        $shSaveOrigLocale = $core->mosConfig_locale;
    }

    $shLanguage = shGetURLLang($string);  // target language in URl is always first choice
    if (empty($shLanguage))
        $shLanguage = isset($shLanguageParam) ? $shLanguageParam : $mosConfig_lang;

    // V 1.3.1 protect against those drop down lists
    if (strpos($string, 'this.options[selectedIndex].value') !== false) {
        $string .= '&amp;lang=' . shGetIsoCodeFromName($shLanguage);
        return $string;
    }
    $mosConfig_lang = $shLanguage;
    _log('Language used : ' . $shLanguage);
    if (shIsMultilingual() == 'mambo') { // V x : mambo compat
        mamboCore::set('mosConfig_locale', shGetIsoCodeFromName($shLanguage));
    }

    // V 1.2.4.t workaround for old links like option=compName instead of option=com_compName
    if (strpos(strtolower($string), 'option=login') === false && strpos(strtolower($string), 'option=logout') === false &&
        strpos(strtolower($string), 'option=&') === false && substr(strtolower($string), -7) != 'option=' && strpos(strtolower($string), 'option=cookiecheck') === false && strpos(strtolower($string), 'option=') !== false && strpos(strtolower($string), 'option=com_') === false) {
        $string = str_replace('option=', 'option=com_', $string);
    }
    // V 1.2.4.k added homepage check : needed in case homepage is not com_frontpage
    if (empty($shHomeLink)) {  // first, find out about homepage link, from DB. homepage is not always /index.php or similar
        // it can be a link to anything, a page, a component,...
        $query = "SELECT `id`,`link` FROM #__menu where ((`menutype`='mainmenu') AND (`published` > 0) AND (`access` <= '" . (isset($my) ? (intval(@$my->gid)) : 0) . "')) ORDER BY parent, ordering";
        $database->setQuery($query);
        $database->loadObject($shHomePage);
        if ($shHomePage) {
            if ((substr($shHomePage->link, 0, 9) == 'index.php')  // if link on homepage is a local page
                && (!preg_match('/Itemid=[0-9]*/', $shHomePage->link))) {  // and it does not have an Itemid
                $shHomePage->link .= ($shHomePage->link == 'index.php' ? '?' : '&') . 'Itemid=' . $shHomePage->id;  // then add itemid
            }
            $shHomeLink = $shHomePage->link;
            if (!strpos($shHomeLink, 'lang=')) {
                // V 1.2.4.q protect against not existing
                $shDefaultIso = shGetIsoCodeFromName(shGetDefaultLang());
                $shSepString = (substr($shHomeLink, -9) == 'index.php' ? '?' : '&');
                $shHomeLink .= $shSepString . 'lang=' . $shDefaultIso;
            }
            $shHomeLink = shSortUrl($shHomeLink);  // $shHomeLink has lang info, whereas $homepage->link may or may not
        }
        _log('HomeLink = ' . $shHomeLink);
    }
    if ($shHomeLink) {  // now check URL against our homepage, so as to always return / if homepage
        $v1 = ltrim(str_replace($mosConfig_live_site, '', $string), '/');
        // V 1.2.4.m : remove anchor if any
        $v2 = explode('#', $v1);
        $v1 = $v2[0];
        $shAnchor = isset($v2[1]) ? '#' . $v2[1] : '';
        $shSepString = (substr($v1, -9) == 'index.php' ? '?' : '&');
        $shLangString = $shSepString . 'lang=' . shGetIsoCodeFromName($shLanguage);
        if (!strpos($v1, 'lang=')) {
            $v1 .= $shLangString;
        }
        $v1 = str_replace('&amp;', '&', shSortURL($v1));
        // V 1.2.4.t check also without pagination info
        if (strpos($v1, 'limitstart=0') !== false) {  // the page has limitstart=0
            $stringNoPag = shCleanUpPag($v1);  // remove paging info to be sure this is not homepage
        } else
            $stringNoPag = null;
        if ($v1 == $shHomeLink || $v1 == 'index.php' . $shLangString || $stringNoPag == $shHomeLink) { // V 1.2.4.t 24/08/2007 11:07:49
            $shTemp = ($v1 == $shHomeLink || shIsDefaultLang($shLanguage) ?
                '' : shGetIsoCodeFromName($shLanguage) . '/');  //10/08/2007 17:28:14
            if (!empty($shMosMsg)) // V x 01/09/2007 22:48:01
                $shTemp .= '?' . $shMosMsg;
            if (!empty($sefConfig->shForcedHomePage)) { // V 1.2.4.t
                $shTmp = $shTemp . $shAnchor;
                $ret = shFinalizeURL($sefConfig->shForcedHomePage . (empty($shTmp) ? '' : '/' . $shTmp));
                $mosConfig_lang = $shOrigLang;
                if (shIsMultilingual() == 'mambo') {
                    mamboCore::set('mosConfig_locale', $shSaveOrigLocale);
                }
                _log('Returning sefRelToAbs 1 with ' . $ret);
                return $ret;
            } else {
                $shRewriteBit = shIsDefaultLang($shLanguage) ? '/' : $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
                $ret = shFinalizeURL($GLOBALS['mosConfig_live_site'] . $shRewriteBit . $shTemp . $shAnchor);
                $mosConfig_lang = $shOrigLang;
                if (shIsMultilingual() == 'mambo') {
                    mamboCore::set('mosConfig_locale', $shSaveOrigLocale);
                }
                _log('Returning sefRelToAbs 2 with ' . $ret);
                return $ret;
            }
        }
    }

    // V 1.3.1 : added default Itemid per component
    $tmp = shGetURLVar($string, 'Itemid');
    if ($sefConfig->UseDefaultItemids && empty($tmp)) {
        $tmp = shGetURLVar($string, 'option');
        $defaultItemid = shGetDefaultItemid($tmp);
        if (!empty($defaultItemid)) {
            $string = shSetURLVar($string, 'Itemid', $defaultItemid);
        }
    }

    // V 1.2.4.j string to be appended to URL, but not saved to DB
    $shAppendString = '';
    $shRebuildNonSef = array();
    $shComponentType = '';  // V w initialize var to avoid notices

    $newstring = str_replace($GLOBALS['mosConfig_live_site'] . '/', '', $string);

    // check for url to same site, but with SSL
    //$liveSiteSsl = str_replace('http://', 'https://', $GLOBALS['mosConfig_live_site']);
    //$newStringSsl = str_replace($liveSiteSsl.'/', '', $string);

    $letsGo = substr($newstring, 0, 9) == 'index.php' && !preg_match('/^(([^:\/?#]+):)/i', $newstring) && !preg_match('/this\.options\[selectedIndex\]\.value/i', $newstring);
    //$letsGoSsl = substr($newStringSsl,0,9) == 'index.php'
    //	&& !preg_match('/^(([^:\/?#]+):)/i', $newStringSsl)
    //	&& !preg_match('/this\.options\[selectedIndex\]\.value/i', $newStringSsl);

    if ($letsGo
        //|| $letsGoSsl
    ) {
        // Replace & character variations.
        $string = str_replace(array('&amp;', '&#38;'), array('&', '&'), $letsGo ? $newstring : $newStringSsl);

        $newstring = $string; // V 1.2.4.q
        $shSaveString = $string;
        // warning : must add &lang=xx (only if it does not exists already), so as to be able to recognize the SefURL in the db if it's there
        if (!strpos($string, 'lang=')) {
            $shSepString = (substr($string, -9) == 'index.php' ? '?' : '&');
            $anchorTable = explode('#', $string); // V 1.2.4.m remove anchor before adding language
            $string = $anchorTable[0];
            $string .= $shSepString . 'lang=' . shGetIsoCodeFromName($shLanguage)
                . (!empty($anchorTable[1]) ? '#' . $anchorTable[1] : ''); // V 1.2.4.m then stitch back anchor
        }

        $URI = new sh_Net_URL($string);

        // V 1.2.4.l need to save unsorted URL
        if (count($URI->querystring) > 0) {
            // Import new vars here.
            $option = null;
            $task = null;
            //$sid = null;  V 1.2.4.s
            // sort GET parameters to avoid some issues when same URL is produced with options not
            // in the same order, ie index.php?option=com_virtuemart&category_id=3&Itemid=2&lang=fr
            // Vs index.php?category_id=3&option=com_virtuemart&Itemid=2&lang=fr
            ksort($URI->querystring);  // sort URL array
            $string = shSortUrl($string);
            // now we are ready to extract vars
            $shGETVars = $URI->querystring;
            extract($URI->querystring, EXTR_REFS);
        }
        if (empty($option)) {// V 1.2.4.r protect against empty $option : we won't know what to do
            $mosConfig_lang = $shOrigLang;
            if (shIsMultilingual() == 'mambo') {
                mamboCore::set('mosConfig_locale', $shSaveOrigLocale);
            }
            _log('Returning sefRelToAbs 3 with ' . $shOrigString);
            return $shOrigString;
        }
        $shOption = str_replace('com_', '', $option);
        switch ($shOption) {
            case (in_array($shOption, $sefConfig->skip)):
                $shComponentType = 'skip';
                break;
            case (in_array($shOption, $sefConfig->nocache)):
                $shComponentType = 'noCache';
                break;
            default:
                $shComponentType = 'sh404SEF';
                break;
        }

        // V 1.2.4.s : fallback to to JoomlaSEF if no extension available
        // V 1.2.4.t : this is too early ; it prevents manual custom redirect to be checked agains the requested non-sef URL
        if (($shComponentType == 'sh404SEF') && !file_exists(sh404SEF_ABS_PATH . 'components/com_sef/sef_ext/' . $option . '.php') && !file_exists(sh404SEF_ABS_PATH . 'components/' . $option . '/sef_ext.php') && !file_exists(sh404SEF_ABS_PATH . 'components/' . $option . '/sef_ext/' . $option . '.php')  // V 1.2.4.s native plugin can be in comp own /sef_ext/dir - allows deliv of plugin with comp
        )
            $shComponentType = 'sh404SEFFallback';
        _log('Component type = ' . $shComponentType);
        // is there a named anchor attached to $string? If so, strip it off, we'll put it back later.
        if ($URI->anchor)
            $string = str_replace('#' . $URI->anchor, '', $string);  // V 1.2.4.m
// shumisha special homepage processing (in other than default language)
        if ((shIsHomePage($string)) || ($string == 'index.php')  // 10/08/2007 18:13:43
        ) {
            $sefstring = '';
            $urlType = shGetSefURLFromCacheOrDB($string, $sefstring);
            _log('Checking string against DB and cache | string = ' . $string . ' | r�sultat = ' . $urlType);
            if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) && !empty($limit)) {
                _log('Special homepage processing nondefault lang | string = ' . $string . ' | sefstring = ' . $sefstring);
                $urlType = shGetSefURLFromCacheOrDB(shCleanUpPag($string), $sefstring); // V 1.2.4.t check also without page info
                //to be able to add pagination on custom
                //redirection or multi-page homepage
                _log('Special homepage processing nondefault lang 2 | string = ' . $string . ' | sefstring = ' . $sefstring . ' | url type = ' . $urlType);
                if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
                    $sefstring = shAddPaginationInfo(@$limit, @$limitstart, 1, $string, $sefstring, null);
                    // that's a new URL, so let's add it to DB and cache
                    _log('Special homepage processing nondefault lang 3 | string = '
                        . $string . ' | sefstring = ' . $sefstring . ' | url type = ' . $urlType);
                    shAddSefUrlToDBAndCache($string, $sefstring, 0, $urlType);  // created url must be of same type as original
                }
                if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) {
                    require_once(sh404SEF_FRONT_ABS_PATH . 'sef_ext.php');
                    $sef_ext = new sef_404();
                    // Rewrite the URL now.
                    $sefstring = $sef_ext->create($string, $URI->querystring, $shAppendString, $shLanguage, $shOrigString); // V 1.2.4.s added original string
                }
            } else if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404)) {  // not found but no $limit
                $sefstring = shGetIsoCodeFromName($shLanguage) . '/';
                // maybe there has been a 404 on this one before. If so, we must update
                shAddSefUrlToDBAndCache($string, $sefstring, 0, sh404SEF_URLTYPE_AUTO); // create it
                _log('Special homepage processing nondefault lang 4 | string = '
                    . $string . ' | sefstring = ' . $sefstring . ' | url type = ' . $urlType);
            }
            // V 1.2.4.j : added $shAppendString to pass non sef parameters. For use with parameters that won't be stored in DB
            $ret = $GLOBALS['mosConfig_live_site'] . $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode]
                . $sefstring . $shAppendString;
            if (!empty($shMosMsg)) // V x 01/09/2007 22:48:01
                $ret .= (empty($shAppendString) || $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode] == '/index.php?/' ? '?' : '&') . $shMosMsg;
            $ret = shFinalizeURL($ret);
            $mosConfig_lang = $shOrigLang;
            if (shIsMultilingual() == 'mambo') {
                mamboCore::set('mosConfig_locale', $shSaveOrigLocale);
            }
            _log('Returning sefRelToAbs 4 with ' . $ret);
            return $ret;
        }

        if (isset($option) && !($option == 'com_content' && @$task == 'edit')) { // V x 29/08/2007 23:19:48
            // check also that option = com_content, otherwise, breaks some comp
            /* Beat: sometimes task is not set, e.g. when $string = "index.php?option=com_frontpage&Itemid=1" */
            switch ($shComponentType) {
                case 'skip': {
                    $sefstring = $shSaveString;  // V 1.2.4.q : restore untouched URL, except anchor
                    // which will be added later
                    break;
                }
                case 'noCache': {
                    if (isset($URI))
                        unset($URI);
                    $sefstring = 'component/';
                    $URI = new sh_Net_URL(shSortUrl($shSaveString));
                    if (count($URI->querystring) > 0) {
                        foreach ($URI->querystring as $key => $value) {
                            $sefstring .= "$key,$value/";
                        }
                        $sefstring = str_replace('option/', '', $sefstring);
                    }
                    break;
                }
                case 'sh404SEFFallback': // v 1.2.4.t
                    $urlType = shGetSefURLFromCacheOrDB($string, $sefstring); // V 1.2.4.t

                    if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) && !empty($limit)) {
                        $urlType = shGetSefURLFromCacheOrDB(shCleanUpPag($string), $sefstring);
                        if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
                            $sefstring = shAddPaginationInfo(@$limit, @$limitstart, 1, $string, $sefstring, null);
                            // that's a new URL, so let's add it to DB and cache
                            shAddSefUrlToDBAndCache($string, $sefstring, 0, $urlType);
                        }
                    }
                    // if not found then fall back to Joomla! SEF
                    if ($urlType == sh404SEF_URLTYPE_NONE) {
                        if (isset($URI))
                            unset($URI);
                        $sefstring = 'component/';
                        $URI = new sh_Net_URL(shSortUrl($shSaveString));
                        if (count($URI->querystring) > 0) {
                            foreach ($URI->querystring as $key => $value) {
                                $sefstring .= "$key,$value/";
                            }
                            $sefstring = str_replace('option/', '', $sefstring);
                        }
                    }
                    break;
                default: {
                    $sefstring = '';
                    $urlType = shGetSefURLFromCacheOrDB($string, $sefstring); // V 1.2.4.t

                    if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) && !empty($limit)) {
                        $urlType = shGetSefURLFromCacheOrDB(shCleanUpPag($string), $sefstring); // search without pagination info
                        if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
                            $sefstring = shAddPaginationInfo(@$limit, @$limitstart, 1, $string, $sefstring, null);
                            // that's a new URL, so let's add it to DB and cache
                            shAddSefUrlToDBAndCache($string, $sefstring, 0, $urlType);
                        }
                    }

                    if ($urlType == sh404SEF_URLTYPE_NONE) {
                        // If component has its own sef_ext plug-in included.
                        $shDoNotOverride = in_array($shOption, $sefConfig->shDoNotOverrideOwnSef);
                        if (file_exists(sh404SEF_ABS_PATH . 'components/' . $option . '/sef_ext.php') && ($shDoNotOverride                   // and param said do not override
                                || (!$shDoNotOverride              // or param said override, but we don't have a plugin either in sh404SEF dir or component sef_ext dir
                                    && (!file_exists(sh404SEF_ABS_PATH
                                            . 'components/com_sef/sef_ext/' . $option . '.php') &&
                                        !file_exists(sh404SEF_ABS_PATH
                                            . 'components/' . $option . '/sef_ext/' . $option . '.php') )
                                ))) {
                            // Load the plug-in file. V 1.2.4.s changed require_once to include
                            include_once(sh404SEF_ABS_PATH . 'components/' . $option . '/sef_ext.php');
                            $_SEF_SPACE = $sefConfig->replacement;
                            $comp_name = str_replace('com_', '', $option);
                            eval("\$sef_ext = new sef_$comp_name;");
                            // V x : added default string in params
                            if (empty($sefConfig->defaultComponentStringList[$comp_name]))
                                $title[] = getMenuTitle($option, null, isset($Itemid) ? @$Itemid : null, null, $shLanguage); // V 1.2.4.x
                            else
                                $title[] = $sefConfig->defaultComponentStringList[$comp_name];
                            // V 1.2.4.r : clean up URL BEFORE sending it to sef_ext files, to have control on what they do
                            // remove lang information, we'll put it back ourselves later
                            //$shString = preg_replace( '/(&|\?)lang=[a-zA-Z]{2,3}/iU' ,'', $string);
                            // V 1.2.4.t use original non-sef string. Some sef_ext files relies on order of params, which may
                            // have been changed by sh404SEF
                            $shString = preg_replace('/(&|\?)lang=[a-zA-Z]{2,3}/iU', '', $shSaveString);
                            $finalstrip = explode("|", $sefConfig->stripthese);
                            $shString = str_replace('&', '&amp;', $shString);
                            _log('Sending to own sef_ext.php plugin : ' . $shString);
                            $sefstring = $sef_ext->create($shString);
                            _log('Created by sef_ext.php plugin : ' . $sefstring);
                            $sefstring = str_replace("%10", "%2F", $sefstring);
                            $sefstring = str_replace("%11", $sefConfig->replacement, $sefstring);
                            $sefstring = rawurldecode($sefstring);
                            if ($sefstring == $string) {
                                if (!empty($shMosMsg)) // V x 01/09/2007 22:48:01
                                    $string .= '?' . $shMosMsg;
                                $ret = shFinalizeURL($string);
                                $mosConfig_lang = $shOrigLang;
                                if (shIsMultilingual() == 'mambo') {
                                    mamboCore::set('mosConfig_locale', $shSaveOrigLocale);
                                }
                                _log('Returning sefRelToAbs 5 with ' . $ret);
                                return $ret;
                            } else {
                                // V 1.2.4.p : sef_ext extensions for opensef/SefAdvance do not always replace '
                                $sefstring = str_replace('\'', $sefConfig->replacement, $sefstring);
                                // some ext. seem to html_special_chars URL ?
                                $sefstring = str_replace('&#039;', $sefConfig->replacement, $sefstring); // V w 27/08/2007 13:23:56
                                $sefstring = str_replace(' ', $_SEF_SPACE, $sefstring);
                                $sefstring = str_replace(' ', '', (shInsertIsoCodeInUrl($option, $shLanguage) ? // V 1.2.4.q
                                        shGetIsoCodeFromName($shLanguage) . '/' : '')
                                    . titleToLocation($title[0]) . '/' . $sefstring . (($sefstring != '') ? $sefConfig->suffix : ''));
                                if (!empty($sefConfig->suffix))
                                    $sefstring = str_replace('/' . $sefConfig->suffix, $sefConfig->suffix, $sefstring);

                                //$finalstrip = explode("|", $sefConfig->stripthese);
                                $sefstring = str_replace($finalstrip, $sefConfig->replacement, $sefstring);
                                $sefstring = str_replace($sefConfig->replacement . $sefConfig->replacement . $sefConfig->replacement, $sefConfig->replacement, $sefstring);
                                $sefstring = str_replace($sefConfig->replacement . $sefConfig->replacement, $sefConfig->replacement, $sefstring);
                                $suffixthere = 0;
                                if (!empty($sefConfig->suffix) && strpos($sefstring, $sefConfig->suffix) !== false)  // V 1.2.4.s
                                    $suffixthere = strlen($sefConfig->suffix);
                                $takethese = str_replace("|", "", $sefConfig->friendlytrim);
                                $sefstring = trim(substr($sefstring, 0, strlen($sefstring) - $suffixthere), $takethese);
                                $sefstring .= $suffixthere == 0 ? '' : $sefConfig->suffix;  // version u 26/08/2007 17:27:16
                                // V 1.2.4.m store it in DB so as to be able to use sef_ext plugins really !
                                $string = str_replace('&amp;', '&', $string);
                                // V 1.2.4.r without mod_rewrite
                                $shSefString = shAdjustToRewriteMode($sefstring);
                                // V 1.2.4.p check for various URL for same content
                                $dburl = ''; // V 1.2.4.t prevent notice error
                                $urlType = sh404SEF_URLTYPE_NONE;
                                if ($sefConfig->shUseURLCache)
                                    $urlType = shGetNonSefURLFromCache($shSefString, $dburl);
                                $newMaxRank = 0; // V 1.2.4.s
                                $shDuplicate = false;
                                if ($sefConfig->shRecordDuplicates || $urlType == sh404SEF_URLTYPE_NONE) {  // V 1.2.4.q + V 1.2.4.s+t
                                    $sql = "SELECT newurl, rank, dateadd FROM #__redirection WHERE oldurl = '"
                                        . $shSefString . "' ORDER BY rank ASC";
                                    $database->setQuery($sql);
                                    $dbUrlList = $database->loadObjectList();
                                    if (count($dbUrlList) > 0) {
                                        $dburl = $dbUrlList[0]->newurl;
                                        $newMaxRank = $dbUrlList[count($dbUrlList) - 1]->rank + 1;
                                        $urlType = $dbUrlList[0]->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
                                    }
                                }
                                if ($urlType != sh404SEF_URLTYPE_NONE && ($dburl != $string))
                                    $shDuplicate = true;
                                $urlType = $urlType == sh404SEF_URLTYPE_NONE ? sh404SEF_URLTYPE_AUTO : $urlType;
                                _log('Adding from sef_ext to DB : ' . $shSefString . ' | rank = ' . ($shDuplicate ? $newMaxRank : 0));
                                shAddSefUrlToDBAndCache($string, $shSefString, ($shDuplicate ? $newMaxRank : 0), $urlType);
                            }
                        }
                        // Component has no own sef extension.
                        else {
                            $string = trim($string, "&?");

                            // V 1.2.4.q a trial in better handling homepage articles
                            if (shIsCurrentPageHome() && ($option == 'com_content')    // com_content component on homepage
                                && (isset($task)) && ($task == 'view') && $sefConfig->guessItemidOnHomepage) {
                                $string = preg_replace('/(&|\?)Itemid=[^&]*/i', '', $string);  // we remove Itemid, as com_content plugin
                                $Itemid = null;                                     // will hopefully do a better job at finding the right one
                                unset($URI->querystring['Itemid']);
                                unset($shGETVars['Itemid']);
                            }

                            require_once(sh404SEF_FRONT_ABS_PATH . 'sef_ext.php');
                            $sef_ext = new sef_404();
                            // Rewrite the URL now. // V 1.2.4.s added original string
                            $sefstring = $sef_ext->create($string, $URI->querystring, $shAppendString, $shLanguage, $shOrigString);
                        }
                    }
                }
            } // end of cache check shumisha
            if (isset($sef_ext))
                unset($sef_ext);
            // V 1.2.4.j
            // V 1.2.4.r : checked for double //
            // V 1.2.4.r try sef without mod_rewrite
            $shRewriteBit = $shComponentType == 'skip' ? '/' : $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
            if (strpos($sefstring, 'index.php') === 0)
                $shRewriteBit = '/';  // V 1.2.4.t bug #119
            $string = $GLOBALS['mosConfig_live_site'] . $shRewriteBit . ltrim($sefstring, '/')
                . $shAppendString
                . (empty($shMosMsg) ? '' :
                    (empty($shAppendString) || $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode] == '/index.php?/' ? '?' : '&') . $shMosMsg)
                . (($URI->anchor) ? "#" . $URI->anchor : '');
        }
        else {  // V x 03/09/2007 13:47:37 editing content
            $shComponentType = 'skip';  // will prevent turning & into &amp;
        }
        $ret = $string;
        // $ret = str_replace('itemid', 'Itemid', $ret); // V 1.2.4.t bug #125
    }
    if (!isset($ret))
        $ret = $string;
    if (!empty($shMosMsg) && strpos($ret, $shMosMsg) === false) // V x 01/09/2007 23:02:00 
        $ret .= (strpos($ret, '?') === false || $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode] == '/index.php?/' ? '?' : '&') . $shMosMsg;
    $ret = ($shComponentType == 'sh404SEF') ? shFinalizeURL($ret) : $ret;  // V w 27/08/2007 13:21:28
    $mosConfig_lang = $shOrigLang;
    if (shIsMultilingual() == 'mambo') {
        mamboCore::set('mosConfig_locale', $shSaveOrigLocale);
    }
    return $ret;
}

// V 1.2.4.q detect homepage, disregarding language and pagination
function shIsHomepage($string) {
    global $shHomeLink, $mosConfig_live_site;
    $shTempString = rtrim(str_replace($mosConfig_live_site, '', $string), '/');
    return shSortUrl(shCleanUpLangAndPag($shTempString)) == shSortUrl(shCleanUpLangAndPag($shHomeLink)); // version t added sorting
}

function getMenuTitle($option, $task, $id = null, $string = null, $shLanguage = null) {
    global $database, $sefConfig, $shHomeLink;

    $shLanguage = empty($shLanguage) ? $GLOBALS['mosConfig_lang'] : $shLanguage;
    // V 1.2.4.q must also check if homepage, in any language. If homepage, must return $title[]='/'
    // language info and limit/limistart pagination will be added at final stage by sefGetLocation()
    // V 1.2.4.t must also check that menu item is published !!

    if (!empty($string)) {  // V 1.2.4.q replaced isset by empty
        $sql = "SELECT name, link,id FROM #__menu WHERE link = '$string' AND published = '1'";
    } elseif (!empty($id)) {
        $sql = "SELECT name, link,id FROM #__menu WHERE id = '" . $id . "' AND published='1'";
    } elseif (!empty($option)) {
        $sql = 'SELECT name, link,id FROM #__menu WHERE published=\'1\' AND link LIKE \'index.php?option=' . $option . '%\'';
    } else {
        return '/'; // don't know what else we could do, just go home
    }
    $database->setQuery($sql);
    if (isset($shLanguage) && shIsMultilingual()) {
        $rows = @$database->loadObjectList('', true, $shLanguage);
    } else {
        $rows = @$database->loadObjectList();
    }
    if ($database->getErrorNum()) {
        die($database->stderr());
    } elseif (@count($rows) > 0) {
        $shLink = shSortUrl($rows[0]->link . ($rows[0]->link == 'index.php' ? '?' : '&') . 'Itemid=' . $rows[0]->id);
        if (!shIsHomepage($shLink)) {  // V1.2.4.q homepage detection
            if (!empty($rows[0]->name)) {
                $title = $rows[0]->name;
            }
        } else
            $title = '/'; // this is homepage
    } else {
        $title = str_replace('com_', '', $option);
    }
    return $title;
}

?>