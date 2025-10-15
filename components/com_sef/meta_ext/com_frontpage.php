<?php
/**
 * shCustomTags support for com_frontpage
 * Yannick Gaultier, shumisha
 * shumisha@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: com_frontpage.php 197 2007-12-08 19:01:20Z silianacom-svn $
 *  This module must set $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag,$shCustomLangTag, $shCustomRobotsTag according to specific component output
 *  
 * if you set a variable to '', this will ERASE the corresponding meta tag
 * if you set a variable to null, this will leave the corresponding meta tag UNCHANGED  
 *     
 * {shSourceVersionTag: Version x - 2007-09-20} 
 *     
 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_lang, $sh_LANG, $mosConfig_live_site, $sefConfig, $database;

$shLangName = empty($lang) ? $mosConfig_lang : shGetNameFromIsoCode( $lang);
$shLangIso = isset($lang) ? $lang : shGetIsoCodeFromName( $mosConfig_lang);
//-------------------------------------------------------------
 
global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, $shCustomLangTag, $shCustomRobotsTag;

global $mosConfig_MetaDesc, $mosConfig_MetaKeys;

$shCustomLangTag = $shLangIso; // V 1.2.4.t bug #127
$shCustomRobotsTag = 'index, follow';
$shCustomDescriptionTag = $mosConfig_MetaDesc;
$shCustomKeywordsTag = $mosConfig_MetaKeys;

$query = 'SELECT id, name FROM #__menu WHERE `link` LIKE \'%com_frontpage%\'';
$database->setQuery($query);
$database->loadObject($shTitle);
$shCustomTitleTag = $GLOBALS['mosConfig_sitename'].(empty($shTitle)?'' :' | '.$shTitle->name) ;		

?>
