<?php

/*
 * sh404SEF support for xmap component
 * By Kaizer, M (Mirjam)
 * Tested with Xmap 1.1 and sh404SEF Version_1.3_RC - build_150 and Joom!Fish v1.8.2 (2007-12-16).
 * An example/guide in writing this was com_docman.php by  Yannick Gaultier (shumisha) [2007-09-19 18:35:29Z].
 * @version 0.1 $Id: com_xmap.php 2008-05-06 21:35
 * License : GNU/GPL 
 */

// you don't want anybody but "joomla" accessing your files
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ------------------  standard plugin initialize function - don't change 
global $sh_LANG, $sefConfig;
$shLangName = '';
;
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);
$dosef = 1;
// ------------------  standard plugin initialize function - don't change 
// Define the function that gets the menu name
if (!function_exists('shXmapMenuName')) {

    function shXmapMenuName($Itemid, $option, $shLangName) {
        $shXmapMenuName = shGetComponentPrefix($option);
        $shXmapMenuName = empty($shXmapMenuName) ? getMenuTitle($option, null, $Itemid, null, $shLangName) : $shXmapMenuName;
        $shXmapMenuName = (empty($shXmapMenuName) || $shXmapMenuName == '/') ? 'sitemap' : $shXmapMenuName;
        return $shXmapMenuName;
    }

}

// Use the function to put the menuname int $title, which is what goes in the sef-ified URL
$title[] = shXmapMenuName($Itemid, $option, $shLangName);

// Some cleaning up of variables in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
    shRemoveFromGETVarsList('Itemid');

// ------------------  standard plugin finalize function - don't change 
if ($dosef) {
    $string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString, (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change 
?>
