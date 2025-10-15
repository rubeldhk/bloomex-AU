<?php
/**
 * shCustomTags support for com_content
 * Yannick Gaultier, shumisha
 * shumisha@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: com_content.php 216 2008-01-01 11:54:15Z silianacom-svn $
 *
 *  This module must set $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag,$shCustomLangTag, $shCustomRobotsTag according to specific component output
 *  
 * if you set a variable to '', this will ERASE the corresponding meta tag
 * if you set a variable to null, this will leave the corresponding meta tag UNCHANGED  
 *     
 * {shSourceVersionTag: Version x - 2007-09-20} 
 *     
 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $Itemid, $database;

global $mosConfig_lang, $sh_LANG, $mosConfig_live_site, $sefConfig;

$task = mosGetParam($_REQUEST, 'task', null);
$catid = mosGetParam($_REQUEST, 'catid', null);
$id = mosGetParam($_REQUEST, 'id', null);
$limit = mosGetParam($_REQUEST, 'limit', null);
$limitstart = mosGetParam($_REQUEST, 'limitstart', null);

$shLangName = empty($lang) ? $mosConfig_lang : shGetNameFromIsoCode( $lang);
$shLangIso = isset($lang) ? $lang : shGetIsoCodeFromName( $mosConfig_lang);
$shLangIso = shLoadPluginLanguage( 'com_content', $shLangIso, '_COM_SEF_SH_CREATE_NEW');
//-------------------------------------------------------------
 
global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, $shCustomLangTag, $shCustomRobotsTag;
 
$shCustomLangTag = $shLangIso;  // V 1.2.4.t bug #127
$shCustomRobotsTag = 'index, follow';
$title= array(); 
$task = isset($task) ? @$task : null; 
switch ($task) {
	case 'archivecategory':
	case 'archivesection' :
	  $shCustomTitleTag = $sh_LANG[$shLangIso]['_COM_SEF_SH_ARCHIVE']
      . ' '.$sefConfig->replacement.' '. $mosConfig_live_site;
	break;
	case 'new' :
	  $title[] = $sh_LANG[$shLangIso]['_COM_SEF_SH_CREATE_NEW'];
	  if (!empty($sectionid)) {
      $q = 'SELECT id, title FROM #__sections WHERE id = '.$sectionid;
      $database->setQuery($q);
      $database->loadObject( $sectionTitle);
      if ($sectionTitle) {
        $title[] = $sectionTitle->title;
      }  
    }
	break;
 	case 'edit':
	break;
	default:
	  // use regular function to get content titles. However, force use of full Title instead of Alias
	  $shSaveAlias = $sefConfig->UseAlias;
	  $sefConfig->UseAlias = false;
	  // V 1.2.4.t protect against sef_ext.php not being included
	  if (!class_exists('sef_404'))
	    require_once(sh404SEF_ABS_PATH.'components/com_sef/sef_ext.php');
		$title = sef_404::getContentTitles($task, $id, $Itemid, $shLangName);
		$sefConfig->UseAlias = $shSaveAlias;
		// V 1.2.4.t try better handling of multipages article (use of mospagebreak)
		if ($task == 'view' && !empty($limit) ) {  // this is multipage article
		  //ob_start( 'ob_gzhandler' );
		  $shPageTitle = '';
		  $sql = 'SELECT c.id, c.fulltext, c.introtext  FROM #__content AS c WHERE id=\''.$id.'\'';
		  $database->setQuery($sql);
      	  $database->loadObject( $contentElement);
      	  if ($database->getErrorNum()) {
				die( $database->stderr());
		  }
		  $contentText = $contentElement->introtext.$contentElement->fulltext;
      	  if (!empty($contentElement) && ( strpos( $contentText, 'mospagebreak' ) !== false )) { // search for mospagebreak tags
          // copied over from mosPaging mambot
          // expression to search for
 	      $regex = '/{(mospagebreak)\s*(.*?)}/i';
 	      // find all instances of mambot and put in $matches
	      $shMatches = array();
	      preg_match_all( $regex, $contentText, $shMatches, PREG_SET_ORDER );
     	  // adds heading or title to <site> Title
  			$page_text = $limitstart + 1;
  			if ($sefConfig->pagetext && (false !== strpos($sefConfig->pagetext, '%s')))
   	      $shPageTitle = str_replace('%s', $page_text, $sefConfig->pagetext);
        else
  	      $shPageTitle = _PN_PAGE .' '. $page_text;
  	    if (empty($limitstart)) {
      	    if ( $shMatches[0][2] ) {
      				parse_str( html_entity_decode( $shMatches[0][2] ), $args );
      				if ( @$args['heading'] ) {
      					$shPageTitle = stripslashes( $args['heading'] );
      				}
      			}
        } else {
          if ( $shMatches[$limitstart-1][2] ) {
    				parse_str( html_entity_decode( $shMatches[$limitstart-1][2] ), $args );
    				if ( @$args['title'] ) {
    					$shPageTitle = stripslashes( $args['title'] );
    				}
    			}
        } 
      }
		  if (!empty($shPageTitle))  // found a heading, we should use that as a Title
        $title[] = shCleanUpTitle($shPageTitle);   
    }
    
	  // V 1.2.4.j 2007/04/11 : numerical ID, on some categories only
	  if ($sefConfig->shInsertNumericalId && isset($sefConfig->shInsertNumericalIdCatList) 
         && !empty($id) && ($task == 'view')) {
      
      $q = 'SELECT id, catid, created FROM #__content WHERE id = '.$id;
      $database->setQuery($q);
      if (shTranslateUrl($option, $shLangName)) // V 1.2.4.m
        $database->loadObject( $contentElement);
      else $database->loadObject( $contentElement, false);  
      if (!empty($contentElement)) { // V 1.2.4.t
        $foundCat = array_search(@$contentElement->catid, $sefConfig->shInsertNumericalIdCatList);
        if (($foundCat !== null && $foundCat !== false) 
            || ($sefConfig->shInsertNumericalIdCatList[0] == ''))  { // test both in case PHP < 4.2.0
          $shTemp = explode(' ', $contentElement->created);
          $title[] = str_replace('-','', $shTemp[0]).$contentElement->id;
	      } 
      }
    }
    
    // V 1.2.4.k 2007/04/25 : if activated, insert edition id and name from iJoomla magazine
    if (!empty($ed) && $sefConfig->shActivateIJoomlaMagInContent && $id && ($task == 'view')) {
      $q = 'SELECT id, title FROM #__magazine_categories WHERE id = '.$ed;
      $database->setQuery($q);
      if (shTranslateUrl($option, $shLangName)) // V 1.2.4.m
        $database->loadObject( $issueName, false);
      else $database->loadObject( $issueName);  
      if ($issueName) {
        $title[] = ($sefConfig->shInsertIJoomlaMagIssueId ? $ed.$sefConfig->replacement:'')
          .$issueName->title;
      } 
    }
    // end of edition id insertion
    $title = array_reverse( $title);
    $shCustomTitleTag = ltrim(implode( ' | ', $title), '/ | ');		
	}

?>
