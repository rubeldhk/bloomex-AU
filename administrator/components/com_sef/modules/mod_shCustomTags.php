<?php
/**
* shCustomTags Joomla module : php module
* Copyright (C) 2006-2007 Yannick Gaultier (shumisha). All rights reserved.
* Released under the http://www.gnu.org/copyleft/gpl.html GNU/GPL, doc/license and credits.txt
* This is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* {shSourceVersionTag: Version x - 2007-09-20} 
*/
  defined( '_VALID_MOS' ) or die( 'Restricted access' );
  
  // V 1.2.4.t  check if sh404SEF is running
if (function_exists('shLoadPluginlanguage')) {
  
  global $mosConfig_absolute_path, $mosConfig_live_site;
  global $shModuleName;
  
  // support for improved TITLE, DESCRIPTION, KEYWORDS and ROBOTS head tag
  global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, 
         $shCustomRobotsTag, $shCustomLangTag;
  // these variables can be set throughout your php code in components, bots or other modules
  // the last one wins !
   
  /* Module name : the same structure is used for several module */
  $shModuleName = 'shCustomTags';
  
if (!function_exists('shGetIsoCodeFromName')){ // V 1.2.4.m missing '' around function name
// shumisha utility function to obtain iso code from language name
function shGetIsoCodeFromName($langName) {
  global $database, $shIsoCodeCache, $mosConfig_locale, $mosConfig_lang;
  // V 1.2.4.h was failing if Joomfish is not installed : #__languages does not exist !! 
  if (!isset( $shIsoCodeCache[$langName])) {
    $type = shIsMultilingual();
    if ($type != false) {
      $query = 'SELECT '.($type == 'joomfish' ? 'iso, code':'mambo,name')
               .' FROM '.($type == 'joomfish' ? '#__languages':'#__nok_language').' WHERE 1';
      $database->setQuery($query);
      $rows = $database->loadObjectList();
      foreach ($rows as $row) {
        $shIsoCodeCache[($type == 'joomfish' ? $row->code:$row->name)] = ($type == 'joomfish' ? $row->iso:$row->mambo);
      }
    } else { // no joomfish, so it has to be default language
      $shTemp = explode( '_', $mosConfig_locale);
      $langName = $mosConfig_lang;
      $shIsoCodeCache[$mosConfig_lang] = $shTemp[0] ? $shTemp[0] : 'en';
    }
  }
  return empty($shIsoCodeCache[$langName]) ? 'en' : $shIsoCodeCache[$langName];
}
}
  
  /* get this module parameters and store them in variables */
  
  $customTagsCount = 3;
  for ($i=1; $i<= $customTagsCount; $i++) {
    $shCustomText[] = $params->get( 'customText'.$i);
    $shInsertType[] = $params->get( 'insertType'.$i);
    $shCustomTag[] = $params->get( 'customTag'.$i);
  }

// utility function to insert data into an html buffer, after, instead or before 
// one or more instances of a tag. If last parameter is 'first', then only the 
// first occurence of the tag is replaced, or the new value is inserted only 
// after or before the first occurence of the tag

 
  if (!function_exists('shCleanUpTitle')) {
    function shCleanUpTitle( $title) {
      return trim(stripslashes(html_entity_decode($title)));
    }
  } 
 
  if (!function_exists('shCleanUpDesc')) {
    function shCleanUpDesc( $desc) {
      $desc = stripslashes(html_entity_decode(strip_tags($desc, '<br><br /><p></p>'), ENT_NOQUOTES));
      $desc = str_replace('<br>',' - ', $desc);  // otherwise, one word<br >another becomes onewordanother
      $desc = str_replace('<br />',' - ', $desc);
      $desc = str_replace('<p>',' - ', $desc);
      $desc = str_replace('</p>',' - ', $desc);
      while (strpos($desc, ' -  - ') !== false) {
        $desc = str_replace(' -  - ', ' - ', $desc);
      }  
      $desc = str_replace("&#39;",'\'', $desc);
      $desc = str_replace("&#039;",'\'', $desc);
      $desc = str_replace('"', '\'', $desc);
      $desc = str_replace("\r",'', $desc);
      $desc = str_replace("\n",'', $desc);
      return substr( trim($desc), 0, 512);
    }
  } 
  
  if (!function_exists('shInsertCustomTagInBuffer')) {
    function shInsertCustomTagInBuffer( $buffer, $tag, $where, $value, $firstOnly) {
      if (!$buffer || !$tag || !$value) return $buffer;
      $bits = explode($tag, $buffer);
      if (count($bits) < 2) return $buffer;
      $result = $bits[0];
      $maxCount = count($bits)-1; 
      switch ($where) {
        case 'instead' :
          for ($i=0; $i < $maxCount; $i++) {
            $result .= ($firstOnly == 'first' ? ($i==0 ? $value:$tag):$value).$bits[$i+1];
          }  
          break;
        case 'after' :
          for ($i=0; $i < $maxCount; $i++) {
            $result .= $tag.($firstOnly == 'first' ? ($i==0 ? $value:$tag):$value).$bits[$i+1];
          }
          break;
        default :
          for ($i=0; $i < $maxCount; $i++) {
            $result .= ($firstOnly == 'first' ? ($i==0 ? $value:$tag):$value).$tag.$bits[$i+1];
          }
          break;
      }
      return $result;
    }
  }
  						
  if (!function_exists('shDoLinkReadMoreCallback')) {
	function shDoLinkReadMoreCallback($matches) {
  		if (count($matches) != 3) return empty($matches) ? '' : $matches[0];
  		$mask = '<td class="contentheading" width="100%">%%shM1%%title="%%shTitle%%" class="readon">%%shM2%%&nbsp;[%%shTitle%%]</a>';
  		$result = str_replace('%%shM2%%', $matches[2], $mask);
  		// we may have captured more than we want, if there are several articles, but only the last one has
  		// a Read more link (first ones may be intro-only articles). Need to make sure we are fetching the right title
  		$otherArticles = explode( '<td class="contentheading" width="100%">', $matches[1]);
  		$articlesCount = count ($otherArticles);
  		$matches[1] = $otherArticles[$articlesCount-1];
  		unset($otherArticles[$articlesCount-1]);
  		
  		$bits = explode ('class="contentpagetitle">', $matches[1]);
		if (count ($bits) > 1) {  // there is a linked title
			$titleBits = array();
			preg_match('/(.*)(<script|<\/a>)/isU', $bits[1], $titleBits); // extract title-may still have <h1> tags
			$title = shCleanUpTitle( trim($titleBits[1]));
		} else {  // title is not linked
			$titleBits = array();
			preg_match('/(.*)(<script|<a\s*href=|<\/td>)/isU', $matches[1], $titleBits); // extract title-may still have <h1> tags
			$title = str_replace('<h1>', '', $titleBits[1]);
  			$title = str_replace('</h1>', '', $title);
			$title = shCleanUpTitle( trim($title));
		}
  		$result = str_replace('%%shTitle%%', $title, $result);
  		// restore possible additionnal articles
  		$articles = implode( '<td class="contentheading" width="100%">', $otherArticles);
  		$matches[1] = (empty($articles) ? '': $articles . '<td class="contentheading" width="100%">') . $matches[1];
  		$result = str_replace('%%shM1%%', $matches[1], $result);
  		$result = str_replace('%%shM2%%', $matches[2], $result);
  		$result = str_replace( 'class="contentpagetitle">', 'title="'.$title.'" class="contentpagetitle">', $result);
  		return $result;
  	}
  }
  
  if (!function_exists('shDoRedirectOutboundLinksCallback')) {
	function shDoRedirectOutboundLinksCallback($matches) {
  		if (count($matches) != 2) return empty($matches) ? '' : $matches[0];
  		if (strpos($matches[1], $GLOBALS['mosConfig_live_site']) === false){
  			$mask = '<a href="'.$GLOBALS['mosConfig_live_site'].'/index.php?option=com_sef&shtask=redirect&shtarget=%%shM1%%"';
  			$result = str_replace('%%shM1%%', $matches[1], $mask);
  		} else $result = $matches[0];
  		return $result;
  	}
  }
  
  if (!function_exists('shDoInsertOutboundLinksImageCallback')) {
	function shDoInsertOutboundLinksImageCallback($matches) {
  		if (count($matches) != 2 && count($matches) != 3) return empty($matches) ? '' : $matches[0];
  		global $sefConfig;
  		if ( $matches[1] != $GLOBALS['mosConfig_live_site']
  			&& $matches[1] != $GLOBALS['mosConfig_live_site'].'/'
  			&& substr($matches[1], 0, 7) == 'http://' 
  			&& substr($matches[1], 0, strlen($GLOBALS['mosConfig_live_site'])) != $GLOBALS['mosConfig_live_site']){
  			$mask = '<a href="%%shM1%%" >%%shM2%%<img border="0" alt="%%shM3%%" src="'
  			.$GLOBALS['mosConfig_live_site'].'/administrator/components/com_sef/images/'
  			.$sefConfig->shImageForOutboundLinks
  			.'"/></a>';
  			$result = str_replace('%%shM1%%', $matches[1], $mask);
  			if (!empty($matches[2])) {
  				$result = str_replace('%%shM2%%', $matches[2], $result);
  				$result = str_replace('%%shM3%%', $matches[2], $result);
  			} else {
  				$result = str_replace('%%shM3%%', $matches[1], $result);
  			}
  		} else $result = $matches[0];
  		return $result;
  	}
  }
  
  if (!function_exists('shDoTitleTags')) {
    function shDoTitleTags( $buffer) {
    // Replace TITLE and DESCRIPTION and KEYWORDS
    if (!$buffer) return $buffer;
    global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, 
           $shCustomRobotsTag, $shCustomLangTag, $sefConfig, $shCurrentPagePath, $shCurrentPageNonSef,
           $database, $mosConfig_lang, $shHomeLink;
           
    // V 1.2.4.t protect against error if using shCustomtags without sh404SEF activated         
    // this should not happen, so we simply do nothing
    if (!isset($sefConfig))
      return;
      
    // check if there is a manually created set of tags from tags file
    // need to get them from DB
    if ($sefConfig->shMetaManagementActivated) {
      //  plugin system to automatically build title and description tags on a component per component basis
      global $option;
      $shDoNotOverride = in_array( str_replace('com_', '', $option), $sefConfig->shDoNotOverrideOwnSef);

	  if ((file_exists(sh404SEF_ABS_PATH.'components/'.$option.'/meta_ext/'.$option.'.php'))
	  	&& ($shDoNotOverride                   // and param said do not override 
             || (!$shDoNotOverride              // or param said override, but we don't have a plugin   
                 && !file_exists(sh404SEF_ABS_PATH.'components/com_sef/meta_ext/'.$option.'.php'))  )) {
            _log('Loading component own meta plugin');  // Load the plug-in file
      		include(sh404SEF_ABS_PATH.'components/'.$option.'/meta_ext/'.$option.'.php');
      } // then look for sh404SEF own plugin 
		else if (file_exists(sh404SEF_ABS_PATH.'components/com_sef/meta_ext/'.$option.'.php')) {
			_log('Loading built-in meta plugin');
			include(sh404SEF_ABS_PATH.'components/com_sef/meta_ext/'.$option.'.php');
	  }     
	 
	  // now read manually setup tags
      if (shSortUrl($shCurrentPageNonSef) == shCleanUpLang(shCleanUpAnchor($shHomeLink))) { // V 1.2.4.t homepage custom tags
        $sql = 'SELECT id, metadesc, metakey, metatitle, metalang, metarobots FROM #__sh404SEF_meta WHERE newurl = \''.sh404SEF_HOMEPAGE_CODE.'\'';
      } else {  
        // V 1.2.4.t make sure we have lang info and properly sorted params
        if (!preg_match( '/(&|\?)lang=[a-zA-Z]{2,3}/iU', $shCurrentPageNonSef)) {  // no lang string, let's add default
          $shTemp = explode( '_', $GLOBALS['mosConfig_locale']);
          $shLangTemp = $shTemp[0] ? $shTemp[0] : 'en';
          $shCurrentPageNonSef .= '&lang='.$shLangTemp;
        } 
        $shCurrentPageNonSef = shSortUrl($shCurrentPageNonSef);
        $sql = 'SELECT id, metadesc, metakey, metatitle, metalang, metarobots FROM #__sh404SEF_meta WHERE newurl = \''.ltrim($shCurrentPageNonSef, '/').'\'';
      }  
      $database->setQuery($sql);
      $shCustomTags = null;
      $database->loadObject($shCustomTags);
      if ( !empty($shCustomTags)) {
	      $shCustomTitleTag = !empty($shCustomTags->metatitle) ? $shCustomTags->metatitle : $shCustomTitleTag;
	      $shCustomDescriptionTag = !empty($shCustomTags->metadesc) ? $shCustomTags->metadesc : $shCustomDescriptionTag; 
	      $shCustomKeywordsTag = !empty($shCustomTags->metakey) ? $shCustomTags->metakey : $shCustomKeywordsTag;
	      $shCustomRobotsTag = !empty($shCustomTags->metarobots) ? $shCustomTags->metarobots : $shCustomRobotsTag;
	      $shCustomLangTag = !empty($shCustomTags->metalang) ? $shCustomTags->metalang : $shCustomLangTag;
      }
      
      // then insert them in page
      if (!is_null($shCustomTitleTag))
        $buffer = preg_replace( '/\<\s*title\s*\>.*\<\s*\/title\s*\>/isU', '<title>'
        		.shCleanUpTitle($sefConfig->prependToPageTitle.$shCustomTitleTag.$sefConfig->appendToPageTitle).'</title>', $buffer);  		
      if (!is_null($shCustomDescriptionTag))
        $buffer = preg_replace( '/\<\s*meta\s+name\s*=\s*"description.*\/\>/isU', '<meta name="description" content="'
                  .shCleanUpDesc($shCustomDescriptionTag).'" />', $buffer);
      if (!is_null($shCustomKeywordsTag))
        $buffer = preg_replace( '/\<\s*meta\s+name\s*=\s*"keywords.*\/\>/isU', '<meta name="keywords" content="'
                  .shCleanUpDesc($shCustomKeywordsTag).'" />', $buffer);  
      if (!is_null($shCustomRobotsTag))
        if (strpos($buffer, '<meta name="robots" content="') !== false) 
          $buffer = preg_replace( '/\<\s*meta\s+name\s*=\s*"robots.*\/\>/isU', '<meta name="robots" content="'
                  .$shCustomRobotsTag.'" />', $buffer);   
        else if (!empty($shCustomRobotsTag))  
          $buffer = shInsertCustomTagInBuffer( $buffer, '</head>', 'before', '<meta name="robots" content="'
                  .$shCustomRobotsTag.'" />', 'first');      
                  
      if (!is_null($shCustomLangTag))
        $shLang = $shCustomLangTag;
      else $shLang = $mosConfig_lang;
      if (strpos($buffer, '<meta http-equiv="Content-Language"') !== false) 
          $buffer = preg_replace( '/\<\s*meta\s+http-equiv\s*=\s*"Content-Language".*\/\>/isU', '<meta http-equiv="Content-Language" content="'.$shLang.'" />', $buffer); 
      else if (!empty($shCustomLangTag))
        $buffer = shInsertCustomTagInBuffer( $buffer, '</head>', 'before', '<meta http-equiv="Content-Language" content="'.$shLang.'" />', 'first');
    } 
                  
    // remove Generator tag
    if ($sefConfig->shRemoveGeneratorTag)
      $buffer = preg_replace( '/<meta\s*name="Generator"\s*content=".*\/>/isU','', $buffer); 

    // put <h1> tags around content elements titles 
    if ($sefConfig->shPutH1Tags) {
    	if (strpos($buffer, 'class="componentheading') !== false) {
			$buffer = preg_replace( '/<div class="componentheading([^>]*)>\s*(.*)\s*<\/div>/isU',
                              '<div class="componentheading$1><h1>$2</h1></div>', $buffer);    	
    		$buffer = preg_replace( '/<td class="contentheading([^>]*)>\s*(.*)\s*<\/td>/isU',
                              '<td class="contentheading$1><h2>$2</h2></td>', $buffer);
    	} else {  // replace contentheading by h1
    		$buffer = preg_replace( '/<td class="contentheading([^>]*)>\s*(.*)\s*<\/td>/isU',
                              '<td class="contentheading$1><h1>$2</h1></td>', $buffer);
    	}  
    }                               
	// version x : add title to read on link
    if ($sefConfig->shInsertReadMorePageTitle) {
    	if (strpos( $buffer, 'class="readon"') !== false) {
			$buffer = preg_replace_callback( '/<td class="contentheading" width="100%">(.*)class="readon">(.*)<\/a>/isU',
									'shDoLinkReadMoreCallback', $buffer);						
    	}
    }
    
    // version x : if multiple h1 headings, replace them by h2
    if ($sefConfig->shMultipleH1ToH2 && substr_count( strtolower($buffer), '<h1>') > 1) {
    	$buffer = str_replace( '<h1>', '<h2>', $buffer);
    	$buffer = str_replace( '<H1>', '<h2>', $buffer);
    	$buffer = str_replace( '</h1>', '</h2>', $buffer);
    	$buffer = str_replace( '</H1>', '</h2>', $buffer);
    }

    // Version x insert rel="nofollow" on pdf and print links
    if ($sefConfig->shInsertNoFollowPDFPrint) {
    	if (strpos( $buffer, 'do_pdf=1') !== false) {
			$buffer = preg_replace( '/<a href="'.preg_quote($GLOBALS['mosConfig_live_site'], '/').'\/index2\.php(.*)do_pdf=1([^>]*)/isU',
								'<a rel="nofollow" href="'.$GLOBALS['mosConfig_live_site'].'/index2.php$1do_pdf=1$2', 
								$buffer);						
    	}						
    	if (strpos( $buffer, 'pop=1') !== false) {
			$buffer = preg_replace( '/<a href="'.preg_quote($GLOBALS['mosConfig_live_site'], '/').'\/index2\.php(.*)pop=1([^>]*)/isU',
								'<a rel="nofollow" href="'.$GLOBALS['mosConfig_live_site'].'/index2.php$1pop=1$2', 
								$buffer);						
    	}						
    }
    // V 1.3.1 : replace outbounds links by internal redirects
    if (sh404SEF_REDIRECT_OUTBOUND_LINKS) {
    	$buffer = preg_replace_callback( '/<\s*a\s*href\s*=\s*"(.*)"/isU',
									'shDoRedirectOutboundLinksCallback', $buffer);
    }
    // V 1.3.1 : add symbol to outbounds links
    if ($sefConfig->shInsertOutboundLinksImage) {
    	$buffer = preg_replace_callback( '/<\s*a\s*href\s*=\s*"(.*)"\s*>(.*)<\/a>/isU',
									'shDoInsertOutboundLinksImageCallback', $buffer);
    }
    
    // all done                                   
    return $buffer;
    }
  }    

	// begin main output --------------------------------------------------------
	/* shumisha 2006-02-04 : testing of output buffering level has been removed
  as there is simply no way to predict what is the proper level
  Normally, level should be one, as when modules are included by Joomla only one
  level is active.  However, if the server on which the site is loaded has 
  output_buffering set to "on", then there is an added level. And therefore when
  modules are loaded, ob level should be 2 on these servers. However, PHP is 
  not consistent on this matter : 
  * if output_buffering is set to on or to 4096 in php.ini, then there is an 
    added level of ob in the script. 
  * If output_buffering is set to 4096 (or another figure) through .htaccess, 
  then there is also an added level of ob. 
  * BUT, if you set output_buffering = "on" in your .htaccess file, then 
    there is NO output_buffering addedlevel
    SO if ob_level == 2, I cannot decide whether this is because of server side
    output buffering, or because the module is loaded from mosloadposition mambot
    
    So I decided to remove check altogether.
  */
  
  	//$level = @ob_get_level();
	
	//if ($level > 0) {  // if only one level of output buffering then module 
	                    // is being included normally, not from inside a content
			                // using mosloadposition mambo
			                
	$shPage = ob_get_contents();
    ob_clean();  // remove current page content, it is stored in $shPage
	// do TITLE and DESCRIPTION and KEYWORDS and ROBOTS tags replacement

    $shPage = shDoTitleTags( $shPage);

    // do usual, custom insert or replace functions (same as shCustomHeadTag Module)                      
     for ($shCurrent = 0; $shCurrent < $customTagsCount; $shCurrent++) {
       switch ($shInsertType[$shCurrent]){
         case '0': // insert in <head> section
           $shPage = shInsertCustomTagInBuffer( $shPage, '</head>', 'before', 
                       $shCustomText[$shCurrent], 'first');
         break;
         case '1': // replace first occurence of tag
           $shPage = shInsertCustomtagInBuffer( $shPage, $shCustomTag[$shCurrent], 
                       'instead', $shCustomText[$shCurrent], 'first');
         break;
         case '2': // replace all occurence of tag
           $shPage = shInsertCustomTagInBuffer( $shPage, $shCustomTag[$shCurrent], 
                       'instead', $shCustomText[$shCurrent], 'all');
         break;
         case '3': // output as regular module
           $shPage .= $shCustomText[$shCurrent];
         break;
         case '4': // use in a preg_replace
	   if (!empty($shCustomTag[$shCurrent]) && (!empty($shCustomText[$shCurrent])))
             $shPage = preg_replace( $shCustomTag[$shCurrent], $shCustomText[$shCurrent], $shPage );
         break;
       }  
     }
     // now restore buffered output, including whatever changes we have made 
     // to the page 

     echo $shPage;

  //}  // if not level 1 of output buffering, then no output at all.
} // end of shLoadPluginLanguage check
