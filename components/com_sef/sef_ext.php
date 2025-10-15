<?php

/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: sef_ext.php 223 2008-01-14 21:25:06Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_VALID_MOS'))
    die('Direct Access to this location is not allowed.');


global $_SEF_SPACE, $lowercase;

class sef_component {

    function revert(&$url_array, $pos) {
        global $_SEF_SPACE;

        $QUERY_STRING = '';
        $url_array = array_filter($url_array); // V x : traling slash can cause empty array element

        if (strcspn($url_array[1], ',') == strlen($url_array[1])) {
            // This is a nocache url
            $x = 0;
            $c = count($url_array);
            while ($x < $c) {
                if (isset($url_array[$x]) && $url_array[$x] != '' && isset($url_array[$x + 1]) && $url_array[$x + 1] != '') {
                    $_GET[$url_array[$x]] = $_REQUEST[$url_array[$x]] = $url_array[$x + 1];
                    $QUERY_STRING .= '&' . $url_array[$x] . '=' . $url_array[$x + 1];
                }
                $x += 2;
            }
        } else {
            //This is a default mambo SEF url for a component
            foreach ($url_array as $value) {
                $temp = explode(",", $value);
                if (isset($temp[0]) && $temp[0] != '' && isset($temp[1]) && $temp[1] != "") {
                    $_GET[$temp[0]] = $_REQUEST[$temp[0]] = $temp[1];
                    $QUERY_STRING .= "&$temp[0]=$temp[1]";
                }
            }
        }

        //return str_replace("&option","option",$QUERY_STRING);
        return ltrim($QUERY_STRING, '&');
    }

}

class sef_content {

    function revert(&$url_array, $pos) { // V 1.2.4.l  // updated based on includes/sef.php.
        //   var_dump($url_array);
        $url_array = array_filter($url_array); // V x : traling slash can cause empty array element
        $uri = explode('content/', $_SERVER['REQUEST_URI']);
        $option = 'com_content';
        $_GET['option'] = $option;
        $_REQUEST['option'] = $option;
        $pos = array_search('content', $url_array);

        // language hook for content
        $lang = '';
        foreach ($url_array as $key => $value) {
            if (!strcasecmp(substr($value, 0, 5), 'lang,')) {
                $temp = explode(',', $value);
                if (isset($temp[0]) && $temp[0] != '' && isset($temp[1]) && $temp[1] != '') {
                    $_GET['lang'] = $temp[1];
                    $_REQUEST['lang'] = $temp[1];
                    $lang = $temp[1];
                }
                unset($url_array[$key]);
            }
        }

        if (isset($url_array[$pos + 8]) && $url_array[$pos + 8] != '' && in_array('category', $url_array) && ( strpos($url_array[$pos + 5], 'order,') !== false ) && ( strpos($url_array[$pos + 6], 'filter,') !== false )) {
            // $option/$task/$sectionid/$id/$Itemid/$order/$filter/$limit/$limitstart
            $task = $url_array[$pos + 1];
            $sectionid = $url_array[$pos + 2];
            $id = $url_array[$pos + 3];
            $Itemid = $url_array[$pos + 4];
            $order = str_replace('order,', '', $url_array[$pos + 5]);
            $filter = str_replace('filter,', '', $url_array[$pos + 6]);
            $limit = $url_array[$pos + 7];
            $limitstart = $url_array[$pos + 8];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['sectionid'] = $sectionid;
            $_REQUEST['sectionid'] = $sectionid;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;
            $_GET['order'] = $order;
            $_REQUEST['order'] = $order;
            $_GET['filter'] = $filter;
            $_REQUEST['filter'] = $filter;
            $_GET['limit'] = $limit;
            $_REQUEST['limit'] = $limit;
            $_GET['limitstart'] = $limitstart;
            $_REQUEST['limitstart'] = $limitstart;

            $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&order=$order&filter=$filter&limit=$limit&limitstart=$limitstart";
        } else if (isset($url_array[$pos + 7]) && $url_array[$pos + 7] != '' && $url_array[$pos + 5] > 1000 && ( in_array('archivecategory', $url_array) || in_array('archivesection', $url_array) )) {
            // $option/$task/$id/$limit/$limitstart/year/month/module
            $task = $url_array[$pos + 1];
            $id = $url_array[$pos + 2];
            $limit = $url_array[$pos + 3];
            $limitstart = $url_array[$pos + 4];
            $year = $url_array[$pos + 5];
            $month = $url_array[$pos + 6];
            $module = $url_array[$pos + 7];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['limit'] = $limit;
            $_REQUEST['limit'] = $limit;
            $_GET['limitstart'] = $limitstart;
            $_REQUEST['limitstart'] = $limitstart;
            $_GET['year'] = $year;
            $_REQUEST['year'] = $year;
            $_GET['month'] = $month;
            $_REQUEST['month'] = $month;
            $_GET['module'] = $module;
            $_REQUEST['module'] = $module;

            $QUERY_STRING = "option=com_content&task=$task&id=$id&limit=$limit&limitstart=$limitstart&year=$year&month=$month&module=$module";
        } else if (isset($url_array[$pos + 7]) && $url_array[$pos + 7] != '' && $url_array[$pos + 6] > 1000 && ( in_array('archivecategory', $url_array) || in_array('archivesection', $url_array) )) {
            // $option/$task/$id/$Itemid/$limit/$limitstart/year/month
            $task = $url_array[$pos + 1];
            $id = $url_array[$pos + 2];
            $Itemid = $url_array[$pos + 3];
            $limit = $url_array[$pos + 4];
            $limitstart = $url_array[$pos + 5];
            $year = $url_array[$pos + 6];
            $month = $url_array[$pos + 7];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;
            $_GET['limit'] = $limit;
            $_REQUEST['limit'] = $limit;
            $_GET['limitstart'] = $limitstart;
            $_REQUEST['limitstart'] = $limitstart;
            $_GET['year'] = $year;
            $_REQUEST['year'] = $year;
            $_GET['month'] = $month;
            $_REQUEST['month'] = $month;

            $QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart&year=$year&month=$month";
        } else if (isset($url_array[$pos + 7]) && $url_array[$pos + 7] != '' && in_array('category', $url_array) && ( strpos($url_array[$pos + 5], 'order,') !== false )) {
            // $option/$task/$sectionid/$id/$Itemid/$order/$limit/$limitstart
            $task = $url_array[$pos + 1];
            $sectionid = $url_array[$pos + 2];
            $id = $url_array[$pos + 3];
            $Itemid = $url_array[$pos + 4];
            $order = str_replace('order,', '', $url_array[$pos + 5]);
            $limit = $url_array[$pos + 6];
            $limitstart = $url_array[$pos + 7];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['sectionid'] = $sectionid;
            $_REQUEST['sectionid'] = $sectionid;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;
            $_GET['order'] = $order;
            $_REQUEST['order'] = $order;
            $_GET['limit'] = $limit;
            $_REQUEST['limit'] = $limit;
            $_GET['limitstart'] = $limitstart;
            $_REQUEST['limitstart'] = $limitstart;

            $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&order=$order&limit=$limit&limitstart=$limitstart";
        } else if (isset($url_array[$pos + 6]) && $url_array[$pos + 6] != '') {
            // $option/$task/$sectionid/$id/$Itemid/$limit/$limitstart
            $task = $url_array[$pos + 1];
            $sectionid = $url_array[$pos + 2];
            $id = $url_array[$pos + 3];
            $Itemid = $url_array[$pos + 4];
            $limit = $url_array[$pos + 5];
            $limitstart = $url_array[$pos + 6];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['sectionid'] = $sectionid;
            $_REQUEST['sectionid'] = $sectionid;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;
            $_GET['limit'] = $limit;
            $_REQUEST['limit'] = $limit;
            $_GET['limitstart'] = $limitstart;
            $_REQUEST['limitstart'] = $limitstart;

            $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart";
        } else if (isset($url_array[$pos + 5]) && $url_array[$pos + 5] != '') {
            // $option/$task/$id/$Itemid/$limit/$limitstart
            $task = $url_array[$pos + 1];
            $id = $url_array[$pos + 2];
            $Itemid = $url_array[$pos + 3];
            $limit = $url_array[$pos + 4];
            $limitstart = $url_array[$pos + 5];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;
            $_GET['limit'] = $limit;
            $_REQUEST['limit'] = $limit;
            $_GET['limitstart'] = $limitstart;
            $_REQUEST['limitstart'] = $limitstart;

            $QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart";
        } else if (isset($url_array[$pos + 4]) && $url_array[$pos + 4] != '' && ( in_array('archivecategory', $url_array) || in_array('archivesection', $url_array) )) {
            // $option/$task/$year/$month/$module
            $task = $url_array[$pos + 1];
            $year = $url_array[$pos + 2];
            $month = $url_array[$pos + 3];
            $module = $url_array[$pos + 4];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['year'] = $year;
            $_REQUEST['year'] = $year;
            $_GET['month'] = $month;
            $_REQUEST['month'] = $month;
            $_GET['module'] = $module;
            $_REQUEST['module'] = $module;

            $QUERY_STRING = "option=com_content&task=$task&year=$year&month=$month&module=$module";
        } else if (!(isset($url_array[$pos + 5]) && $url_array[$pos + 5] != '') && isset($url_array[$pos + 4]) && $url_array[$pos + 4] != '') {
            // $option/$task/$sectionid/$id/$Itemid
            $task = $url_array[$pos + 1];
            $sectionid = $url_array[$pos + 2];
            $id = $url_array[$pos + 3];
            $Itemid = $url_array[$pos + 4];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['sectionid'] = $sectionid;
            $_REQUEST['sectionid'] = $sectionid;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;

            $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid";
        } else if (!(isset($url_array[$pos + 4]) && $url_array[$pos + 4] != '') && (isset($url_array[$pos + 3]) && $url_array[$pos + 3] != '')) {
            // $option/$task/$id/$Itemid
            $task = $url_array[$pos + 1];
            $id = $url_array[$pos + 2];
            $Itemid = $url_array[$pos + 3];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;
            $_GET['Itemid'] = $Itemid;
            $_REQUEST['Itemid'] = $Itemid;

            $QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid";
        } else if (!(isset($url_array[$pos + 3]) && $url_array[$pos + 3] != '') && (isset($url_array[$pos + 2]) && $url_array[$pos + 2] != '')) {
            // $option/$task/$id
            $task = $url_array[$pos + 1];
            $id = $url_array[$pos + 2];

            // pass data onto global variables
            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;
            $_GET['id'] = $id;
            $_REQUEST['id'] = $id;

            $QUERY_STRING = "option=com_content&task=$task&id=$id";
        } else if (!(isset($url_array[$pos + 2]) && $url_array[$pos + 2] != '') && (isset($url_array[$pos + 1]) && $url_array[$pos + 1] != '')) {
            // $option/$task
            $task = $url_array[$pos + 1];

            $_GET['task'] = $task;
            $_REQUEST['task'] = $task;

            $QUERY_STRING = 'option=com_content&task=' . $task;
        }

        if ($lang != '') {
            $QUERY_STRING .= '&amp;lang=' . $lang;
        }

        return $QUERY_STRING;
    }

}

class sef_404 {

    function create($string, &$vars, &$shAppendString, $shLanguage, $shSaveString = '') {
        // V 1.2.4.j shAppendString will be added to sef url, but not saved in DB or cache
        global $database, $sefConfig;
        _log('Calling sef404 create function with ' . $string);
        if ($sefConfig->shInsertGlobalItemidIfNone && !empty($GLOBALS['Itemid'])) {  // V 1.2.4.t
            $shCurrentItemid = $GLOBALS['Itemid'];
        } else
            $shCurrentItemid = null;
        $index = str_replace($GLOBALS['mosConfig_live_site'], '', $_SERVER['PHP_SELF']);
        $base = dirname($index);
        $base .= ($base == '/') ? '' : '/';

        extract($vars);
        if (isset($title))  // V 1.2.4.r : protect against components using 'title' as GET vars (com_jim for instance)
            $sh404SEF_title = $title;  // means that $sh404SEF_title has to be used in plugins or extensions
        $title = array();  // V 1.2.4.r
        // Plug-in system.
        $shDoNotOverride = in_array(str_replace('com_', '', $option), $sefConfig->shDoNotOverrideOwnSef);

        // look first in component own /sef_ext/ dir for a com_component_name.php plugin file
        if ((file_exists(sh404SEF_ABS_PATH . 'components/' . $option . '/sef_ext/' . $option . '.php')) && ($shDoNotOverride                   // and param said do not override
                || (!$shDoNotOverride              // or param said override, but we don't have a plugin
                    && !file_exists(sh404SEF_ABS_PATH
                        . 'components/com_sef/sef_ext/' . $option . '.php')) )) {
            // Load the plug-in file.
            _log('Loading component own sh404SEF plugin');
            include(sh404SEF_ABS_PATH . 'components/' . $option . '/sef_ext/' . $option . '.php');
        }
        // then look for sh404SEF own plugin
        else if (file_exists(sh404SEF_ABS_PATH . 'components/com_sef/sef_ext/' . $option . '.php')) {
            _log('Loading built-in plugin');
            include(sh404SEF_ABS_PATH . 'components/com_sef/sef_ext/' . $option . '.php');
        }
        // else if no plugin nowhere default to Joomla own sef
        else {
            _log('Falling back to sefGetLocation');
            if (empty($sefConfig->defaultComponentStringList[str_replace('com_', '', $option)]))
                $title[] = getMenuTitle($option, (isset($task) ? @$task : null), null, null, $shLanguage);
            else
                $title[] = $sefConfig->defaultComponentStringList[str_replace('com_', '', $option)];
            if ($title[0] != '/')
                $title[] = '/';  // V 1.2.4.q getMenuTitle can now return '/'
            if (count($title) > 0) {
                // V 1.2.4.q use $shLanguage insted of $lang  (lang name rather than lang code)
                $string = sef_404::sefGetLocation($string, $title, (isset($task) ? @$task : null), (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), (isset($shLanguage) ? @$shLanguage : null));
            }
        }

        return $string;
    }

    function revert(&$url_array, $pos) {
        GLOBAL $database, $sefConfig;
        $QUERY_STRING = '';
        $req = str_replace('option/', '', implode('/', $url_array));
        if ($req != '/')
            $req = ltrim($req, '/'); // V x
        $req = str_replace("//", "/", $req);
        _log('sef404 reverting URL : ' . $req);
        $sql = "SELECT oldurl, newurl FROM #__redirection WHERE oldurl = '" . $req . "' ORDER BY rank ASC LIMIT 1"; // V 1.2.4.q
        $database->setQuery($sql);
        // V 1.2.4.s
        $result = $database->loadObject($row);

        if ($result) {
            // use the cached url
            $string = $row->newurl;
            _log('sef404 reverting URL : found : ' . $row->newurl);
            // update the count
            $database->setQuery("UPDATE #__redirection SET cpt=(cpt+1) WHERE `newurl` = '" . $row->newurl . "' AND `oldurl` = '" . $row->oldurl . "'");  // V 1.2.4.q
            $database->query();
            $string = str_replace('&amp;', '&', $string);

            $redirection_array = explode('&', parse_url($string, PHP_URL_QUERY));

            $iscategory = false;
            $iscategory_id = null;

            foreach ($redirection_array as $redirection_one)
            {
                $query_array = explode('=', $redirection_one);

                if ($query_array[0] == 'page' AND $query_array[1] == 'shop.browse')
                {
                    $iscategory = true;
                }

                if ($query_array[0] == 'category_id')
                {
                    $iscategory_id = (int)$query_array[1];
                }
            }

            if ($iscategory == true)
            {
                if ($iscategory_id > 0)
                {
                    $iscategory_row = false;
                    $sql = "SELECT `category_publish` FROM `jos_vm_category` WHERE `category_id`=".$iscategory_id."";

                    $database->setQuery($sql);
                    $result = $database->loadObject($iscategory_row);

                    if ($iscategory_row->category_publish == 'N')
                    {
                        $string = preg_replace('/category_id=([0-9]+)/si', 'category_id=321', $string);
                    }
                }
            }

            $QUERY_STRING = str_replace('index.php?', '', $string);
        }

        return $QUERY_STRING;
    }

    function getContentTitles($task, $id, $Itemid = 0, $shLang = null) {  // V 1.2.4.q can force language
        global $database, $sefConfig, $mosConfig_lang;
        $title = array();
        $shLang = empty($shLang) ? $mosConfig_lang : $shLang;
        $sql = '';
        switch ($task) {
            case 'section':  // V 1.2.4.s
                $shTemp = getMenuTitle(null, $task, (isset($Itemid) ? @$Itemid : null), '', $shLang);
                if ($sefConfig->shInsertContentTableName)
                    if (!empty($sefConfig->shContentTableName))
                        $title[] = $sefConfig->shContentTableName;
                    else
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                if (!empty($id)) {
                    $sql = "SELECT title AS section" . (shTranslateURL('com_content', $shLang) ? ',id' : '') . " FROM #__sections WHERE id=$id";
                } else {
                    if (!$sefConfig->shInsertContentTableName || !empty($sefConfig->shContentTableName))
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                }
                break;
            case 'blogsection':
                $shTemp = getMenuTitle(null, $task, (isset($Itemid) ? @$Itemid : null), '', $shLang); // v 1.2.4.t
                if ($sefConfig->shInsertContentBlogName)
                    if (!empty($sefConfig->shContentBlogName))
                        $title[] = $sefConfig->shContentBlogName;
                    else
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                if (!empty($id)) {
                    $sql = "SELECT title AS section" . (shTranslateURL('com_content', $shLang) ? ',id' : '') . " FROM #__sections WHERE id=$id";
                } else {
                    if (!$sefConfig->shInsertContentBlogName || !empty($sefConfig->shContentBlogName))
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                }
                break;
            case 'category':  // V 1.2.4.s added instead of inserting /Table
                $shTemp = getMenuTitle(null, $task, (isset($Itemid) ? @$Itemid : null), '', $shLang);
                if ($sefConfig->shInsertContentTableName)
                    if (!empty($sefConfig->shContentTableName))
                        $title[] = $sefConfig->shContentTableName;
                    else
                        if (!empty($shTemp))
                            $title[] = $shTemp;

                if (!empty($id)) { // V x : apply ShowSection param
                    if ($sefConfig->ShowSection == true && SH404SEF_COMPAT_SHOW_SECTION_IN_CAT_LINKS) {
                        // V 1.2.4.m if displaying a category or a category blog
                        $sql = 'SELECT s.title AS section' . (shTranslateURL('com_content', $shLang) ? ',s.id AS secid ' : '') . ', c.title AS category'
                            . (shTranslateURL('com_content', $shLang) ? ',c.id as catid' : '') . ' FROM #__categories as c ' .
                            'LEFT JOIN #__sections AS s ' .
                            'ON c.section=s.id ' .
                            'WHERE c.id=' . $id;
                    } else {
                        $sql = "SELECT title AS category" . (shTranslateURL('com_content', $shLang) ? ',id' : '') . " FROM #__categories WHERE id=$id";
                    }
                } else {
                    if (!$sefConfig->shInsertContentTableName || !empty($sefConfig->shContentTableName))
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                }
                break;
            case 'blogcategory':
                $shTemp = getMenuTitle(null, $task, (isset($Itemid) ? @$Itemid : null), '', $shLang); // V 1.2.4.t
                if ($sefConfig->shInsertContentBlogName)
                    if (!empty($sefConfig->shContentBlogName))
                        $title[] = $sefConfig->shContentBlogName;
                    else
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                if (!empty($id)) {  // if several cat in blogcategory, then id==0, so we don't get in here
                    // V 1.2.4.m if displaying a category or a category blog, we now disregard the
                    // $sefConfig->ShowCat param, as this would cause issue if set to false
                    if ($sefConfig->ShowSection == true) {
                        // V 1.2.4.m if displaying a category or a category blog
                        $sql = "SELECT s.title AS section" . (shTranslateURL('com_content', $shLang) ? ',s.id AS secid ' : '') . ", c.title AS category" . (shTranslateURL('com_content', $shLang) ? ',c.id as catid' : '') . " FROM #__categories as c " .
                            "LEFT JOIN #__sections AS s " .
                            "ON c.section=s.id " .
                            "WHERE c.id=$id";
                    } else {
                        $sql = "SELECT title AS category" . (shTranslateURL('com_content', $shLang) ? ',id' : '') . " FROM #__categories WHERE id=$id";
                    }
                } else { // V 1.2.4.m if blogcategory with more than one category, then use getMenuTitle() instead
                    if (!$sefConfig->shInsertContentBlogName || !empty($sefConfig->shContentBlogName))
                        if (!empty($shTemp))
                            $title[] = $shTemp;
                }
                // V 1.2.4.q : add table suffix if task= category, to distinguish between blog cat and table cat
                //if ($task == 'category')  // removed in V 1.2.4.s
                //$title[] = _COM_SEF_SH_CAT_TABLE_SUFFIX;
                break;
            case 'view':
                if (!empty($id)) {
                    if ($sefConfig->UseAlias == true) {
                        // verify title alias is not empty
                        $database->setQuery("SELECT title_alias" . (shTranslateURL('com_content', $shLang) ? ',id' : '') . " FROM #__content WHERE id=$id");
                        if ($ta = $database->loadResult())
                            $title_field = "title_alias";
                        else
                            $title_field = "title";
                    }else {
                        $title_field = "title";
                    }
                    if (($sefConfig->ShowSection == true) || ($sefConfig->ShowCat == false)) {
                        $sql = "SELECT " . (($sefConfig->ShowSection == true) ? "s.title AS section" . (shTranslateURL('com_content', $shLang) ? ',s.id AS secid, ' : ', ') : "" ) . " " .
                            (($sefConfig->ShowCat == false) ? "" : "c.title AS category" . (shTranslateURL('com_content', $shLang) ? ',c.id AS catid' : '') . ', ' ) . " a." . $title_field . " AS title" . (shTranslateURL('com_content', $shLang) ? ',a.id as id' : '') . " FROM #__content as a " .
                            "LEFT JOIN #__sections AS s " .
                            "ON a.sectionid=s.id " .
                            "LEFT JOIN #__categories AS c " .
                            "ON a.catid=c.id " .
                            "WHERE a.id=$id";
                    } else {
                        $sql = "SELECT " . (($sefConfig->ShowCat == false) ? "" : "c.title AS category" . (shTranslateURL('com_content', $shLang) ? ',c.id AS catid' : '') . ", " ) . " a." . $title_field . " AS title" . (shTranslateURL('com_content', $shLang) ? ',a.id as id' : '') . " FROM #__content as a " .
                            "LEFT JOIN #__categories AS c " .
                            "ON a.catid=c.id " .
                            "WHERE a.id=$id";
                    }
                }
                break;
            default :
                $sql = '';
        }
        if ($sql) {
            $row = null;
            $database->setQuery($sql);
            $shKind = shIsMultilingual();
            if ($shKind == 'mambo')
                $shLangCode = shGetIsoCodeFromName($shLang);
            else
                $shLangCode = $shLang;
            if (isset($shLang) && shIsMultilingual()) { // V 1.2.4.q  can force language
                $database->loadObject($row, true, $shLangCode);
            } else {
                $database->loadObject($row);
            }
            if (isset($row->section)) {
                $title[] = $row->section;
            }
            if (isset($row->category)) {
                $title[] = $row->category;
            }
            if (isset($row->title))
                $title[] = $row->title;
        }
        if ($task == 'section' || $task == 'blogsection')
            $title[] = "/"; // V w
        if ($task == 'category' || $task == 'blogcategory')
            $title[] = "/"; // V w
        return $title;
    }

    /**
     * Vul in de array $title de onderdelen waaruit de link moet bestaan
     * Bijvoorbeeld: menuitem, categorie, itemnaam
     * Deze functie last de boel aan elkaar
     *
     * @param  string $url
     * @param  array $title
     * @param  string $task
     * @param  int $limit
     * @param  int $limitstart
     * @return sefurl
     */
    function sefGetLocation($url, &$title, $task = null, $limit = null, $limitstart = null, $langParam = null) {
        //GLOBAL $database, $sefConfig;
        GLOBAL $database, $sefConfig, $mosConfig_lang, $option,
               $shHomeLink;
        // shumisha end of change
        $lang = empty($langParam) ? $mosConfig_lang : $langParam;
        // V 1.2.4.k added homepage check : needed in case homepage is not com_frontpage
        if (empty($shHomeLink)) {
            // V 1.2.4.q modified query, first item of menu is not always ordering = 1 !!!
            $query = "SELECT `id`,`link` FROM #__menu where ((`menutype`='mainmenu') AND (`published` > 0) AND (`access` <= '" . (isset($my) ? (intval(@$my->gid)) : 0) . "')) ORDER BY parent, ordering";
            $database->setQuery($query);  // 10/08/2007 22:11:05 mambo compat
            $database->loadObject($shHomePage);
            if ($shHomePage) {
                if ((substr($shHomePage->link, 0, 9) == 'index.php')  // if link on homepage is a local page
                    && (!preg_match('/Itemid=[0-9]*/', $shHomePage->link))) {  // and it does not have an Itemid
                    $shHomePage->link .= ($shHomePage->link == 'index.php' ? '?' : '&') . 'Itemid=' . $shHomePage->id;  // then add itemid
                }
                $shHomeLink = $shHomePage->link;
                $shHomeLink = 'index.php';
                if (!strpos($shHomeLink, 'lang=')) {
                    $shDefaultIso = shGetIsoCodeFromName(shGetDefaultLang());
                    $shSepString = (substr($shHomeLink, -9) == 'index.php' ? '?' : '&');
                    $shHomeLink .= $shSepString . 'lang=' . $shDefaultIso;
                }
                $shHomeLink = shSortUrl($shHomeLink);  // $homeLink has lang info, whereas $homepage->link may or may not
            }
        }
        // shumisha : try to avoid duplicate content when using Joomfish by always adding &lang=xx to url (stored in DB).
        // warning : must add &lang=xx only if it does not exists already, which happens for the joomfish language selection modules or search results
        if (!strpos($url, 'lang=')) {
            $shSepString = (substr($url, -9) == 'index.php' ? '?' : '&');
            $url.= $shSepString . 'lang=' . shGetIsoCodeFromName($lang);
        }
        // shumisha end of fix
        //shorten the url for storage and for consistancy
        $url = str_replace('&amp;', '&', $url);

        // V 1.2.4.q detect multipage homepage
        $shMultiPageHomePageFlag = shIsHomepage($url);

        // get all the titles ready for urls
        $location = array();
        foreach ($title as $titlestring) {      // V 1.2.4.t removed array_filter as it prevents '0' values in URL
            $location[] = titleToLocation($titlestring);
        }
        $location = implode("/", $location); // V 1.2.4.t
        // V 1.2.4.t remove duplicate /
        $location = preg_replace('/\/{2,}/', '/', $location);
        $location = substr($location, 0, sh404SEF_MAX_SEF_URL_LENGTH); // trim to max length V 1.2.4.t
        // shumisha protect against querying for empty location
        if (empty($location))  // V 1.2.4.t
            if ((!shIsMultilingual() || (shIsMultilingual() && shIsDefaultlang($lang))) && !$sefConfig->addFile && !$shMultiPageHomePageFlag) // V 1.2.4.q : need to go further and add pagination
                return ''; // if location is empty, and no Joomfish, or Joomfish but this is default language, then there is nothing to add to url before querying DB


// shumisha end of change


//check for non-sef url first and avoid repeative lookups
        //we only want to look for title variations when adding new
        //this should also help eliminate duplicates.
        // shumisha 2003-03-13 added URL Caching
        $realloc = '';
        $urlType = shGetSefURLFromCache($url, $realloc);
        if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) {  // V 1.2.4.t
            // shumisha end of addition
            $realloc = false;
            if ($urlType == sh404SEF_URLTYPE_NONE) {
                $query = "SELECT oldurl from #__redirection WHERE newurl = '" . $url . "'";
                $database->setQuery($query);
                //if ($realloc = $database->loadResult()) {
                if ($database->loadObject($shTemp))
                    $realloc = $shTemp->oldurl;
            }
            if ($realloc) {
                // found a match, so we aredone
                //Dat betekent dus, dat de functie create(), slecht gekozen is
                // shumisha : removed this die() that I do not understand!
                //die('regel292 in sef_ext.php');
                // shumisha end of removal
            } else {
                // this is new, so we need to insert the new title.
                //Hier worden eindelijk de nieuwe links gemaakt
                $iteration = 1;
                $realloc = false;
                $prev_temploc = '';

                do {
                    // temploc is $location, unless we're on a second or greater iteration,
                    // then its $location.$iteration
                    if (!empty($location))
                        $shSeparator = (substr($location, -1) == '/') ? '' : '/';
                    else
                        $shSeparator = '';

                    $temploc = shAddPaginationInfo($limit, $limitstart, $iteration, $url, $location, $shSeparator); // v 1.2.4.t
                    // V 1.2.4.t
                    if ($shMultiPageHomePageFlag && ('/' . $temploc == $location)    // if homepage
                        && (!shIsMultilingual()       // and no Joomfish
                            || ( shIsMultilingual()       // or Joomfish
                                && shIsDefaultLang($lang)))) {  // but this is default language
                        // this is start page of multipage homepage, return home or forced home
                        if (!empty($sefConfig->shForcedHomePage))  // V 1.2.4.t
                            return str_replace($GLOBALS['mosConfig_live_site'] . '/', '', $sefConfig->shForcedHomePage);
                        else
                            return '';
                    }

                    // V 1.2.4.k here we need to check for other-than-default-language homepage
                    // remove lang
                    $v1 = shCleanUpLang($url); // V 1.2.4.t
                    $v2 = shCleanUpLang($shHomeLink); // V 1.24.t
                    if ($v1 == $v2 || $v1 == 'index.php') {  // check if this is homepage
                        if (shIsMultilingual() && !shIsDefaultLang($lang))   // V 1.2.4.m : insert language code based on param
                            $temploc = shGetIsoCodeFromName($lang) . '/';
                        else
                            $temploc = '';  // if homepage in not-default-language, then add language code even if param says opposite


// as we otherwise would not be able to switch language on the frontpage
                    } else

                        if (shInsertIsoCodeInUrl($option, $lang)) {  // V 1.2.4.m : insert language code based on param
                            // V 1.2.4.q : pass URL lang info, as may not be current lang
                            $temploc = shGetIsoCodeFromName($lang) . '/' . $temploc;   // V 1.2.4.q  must be forced lang, not default
                        }

                    if ($temploc != '') {
                        // see if we have a result for this location
                        // V 1.2.4.r without mod_rewrite
                        $temploc = shAdjustToRewriteMode($temploc);
                        $sql = "SELECT id, newurl, rank, dateadd FROM #__redirection WHERE oldurl = '" . $temploc
                            . "' ORDER BY rank ASC"; // V 1.2.4.q
                        $database->setQuery($sql);
                        if ($iteration > 9999) {
                            var_dump($sql);
                            die('Too many pages :' . $temploc . '##');
                        }
                        $dburl = null; // V 1.2.4.t initialize $dburl to avoid notices error if cache disabled
                        $dbUrlId = null; // V 1.2.4.t
                        $urlType = sh404SEF_URLTYPE_NONE;
                        // shumisha 2007-03-13 added URL caching, check for various URL for same content
                        if ($sefConfig->shUseURLCache)
                            $urlType = shGetNonSefURLFromCache($temploc, $dburl);
                        $newMaxRank = 0;
                        $shDuplicate = false;
                        if ($sefConfig->shRecordDuplicates || $urlType == sh404SEF_URLTYPE_NONE) {  // V 1.2.4.s
                            $dbUrlList = $database->loadObjectList();
                            if (count($dbUrlList) > 0) {
                                $dburl = $dbUrlList[0]->newurl;
                                $dbUrlId = $dbUrlList[0]->id;
                                if (empty($dburl)) {  // V 1.2.4.t url was found in DB, but was a 404
                                    $urlType = sh404SEF_URLTYPE_404;
                                } else {
                                    $newMaxRank = $dbUrlList[count($dbUrlList) - 1]->rank + 1;
                                    $urlType = $dbUrlList[0]->dateadd == '0000-00-00' ?
                                        sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
                                }
                            }
                        }
                        if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
                            if ($dburl == $url) {
                                // found the matching object
                                // it probably should have been found sooner
                                // but is checked again here just for CYA purposes
                                // and to end the loop
                                $realloc = $temploc;
                            } else
                                $shDuplicate = true;
                            // else, didn't find it, increment and try again
                            // shumisha added this to close the loop if working on frontpage
                            // as domain.tld/index.php?lang=xx and domain.tld/index.php?option=com_frontpage&Itemid=1&lang=xx both must end up in domain.tld/xx/ (if xx is not default language of course - in that case, they must endup in domain.tld)
                            // this is true also if Joomfish is not installed and there is no language information in the url
                            // V 1.2.4.q  this is a duplicate so we must indert it with incremented rank;
                            if ($shDuplicate && $sefConfig->shRecordDuplicates) {
                                shAddSefUrlToDBAndCache($url, $temploc, ($shDuplicate ? $newMaxRank : 0), $urlType);
                            }
                            $realloc = $temploc; // to close the loop
                            // shumisha end of addition
                        } else {
                            //title not found, chechk 404
                            if ($sefConfig->shLog404Errors) { // V 1.2.4.m
                                if ($urlType == sh404SEF_URLTYPE_404 && !empty($dbUrlId)) {  // we already have seen that it is a 404
                                    $id = $dbUrlId; // V 1.2.4.t
                                } elseif ($urlType == sh404SEF_URLTYPE_404) {
                                    $query = "SELECT `id` FROM #__redirection WHERE `oldurl` = '$temploc' AND `newurl` = ''";
                                    $database->setQuery($query);
                                    $id = $database->loadResult();
                                } else
                                    $id = null;
                            } else
                                $id = null;  // V 1.2.4.m if we are not logging 404 errors, then no need to check for


// previous hit of this page.
                            if (!empty($id)) {
                                // V 1.2.4.q : need to update dateadd to 0, as otherwise this redir will be seen as a custom redir
                                // this makes all such 404 errors 'disappear' from the 404 log, but no other solution
                                $query = "UPDATE #__redirection SET `newurl` = '" .
                                    addslashes(urldecode($url)) . "',`dateadd` = '0000-00-00' WHERE `id` = '$id'";
                                $database->setQuery($query);
                                if (!$database->query()) {
                                    var_dump($query);
                                } else
                                    shAddSefURLToCache($url, $temploc, sh404SEF_URLTYPE_AUTO); // v 1.2.4.t
                            } else {
                                /* put it in the database */
                                shAddSefUrlToDBAndCache($url, $temploc, 0, sh404SEF_URLTYPE_AUTO);
                            }
                            $realloc = $temploc;
                        }
                    }
                    $prev_temploc = $temploc;
                    $iteration++;
                    // shumisha allow loop exit if $temploc = '' (homepage)
                    //} while (!$realloc);
                } while ((!$realloc) && ($temploc != ''));
            }
        } // shumisha : enf of check if URL is in cache
        return $realloc;
    }

    function getcategories($catid, $shLang = null) {
        global $GLOBALS, $database, $sefConfig;

        if (empty($cat_table))
            $cat_table = "#__categories";
        $title = ''; // V 1.2.4.q
        $shLang = empty($shLang) ? $mosConfig_lang : $shLang;
        if (isset($catid) && $catid != 0) {
            $query = 'SELECT name' . (shTranslateURL('com_content', $shLang) ? ',id' : '') . ' FROM ' . $cat_table . ' WHERE id = "' . $catid . '"';
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                die($database->stderr());
            } elseif (@count($rows) > 0) {
                if (!empty($rows[0]->name)) {
                    $title = $rows[0]->name;
                }
            }
        }
        return $title;
    }

}

?>
