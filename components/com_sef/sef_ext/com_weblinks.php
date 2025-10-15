<?php

/**
 * sh404SEF support for com_weblinks component.
 * Copyright Yannick Gaultier (shumisha) - 2007
 * shumisha@gmail.com
 * @version     $Id: com_weblinks.php 229 2008-01-21 19:53:39Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */
 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG, $sefConfig;  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
$shLangIso = shLoadPluginLanguage( 'com_weblinks', $shLangIso, '_COM_SEF_SH_CREATE_NEW_LINK');
// ------------------  load language file - adjust as needed ----------------------------------------
$task = isset($task) ? @$task : null;
$Itemid = isset($Itemid) ? @$Itemid : null;
$shWeblinksName = shGetComponentPrefix($option);
$shWeblinksName = empty($shWeblinksName) ?  
		getMenuTitle($option, isset($task) ? $task:null, isset($Itemid) ? $Itemid:null, null, $shLangName) : $shWeblinksName;
$shWeblinksName = (empty($shWeblinksName) || $shWeblinksName == '/') ? 'Newsfeed':$shWeblinksName;
if (!empty($shWeblinksName)) $title[] = $shWeblinksName;

if (!empty($catid)) { // V 1.2.4.q
  $arg2[] = sef_404::getcategories($catid, $shLangName);
  $title = array_merge($title, $arg2);
} 
switch ($task) {
  case 'view':                                         
    if (!empty($id)) {
	    $query = 'SELECT title, id FROM #__weblinks WHERE id = "'.$id.'"';
	    $database->setQuery($query);
	    if (shTranslateURL($option, $shLangName))
	      $rows = $database->loadObjectList( );
	    else  $rows = $database->loadObjectList( null, false);
	    if ($database->getErrorNum()) {
		    die( $database->stderr());
	    }elseif( @count( $rows ) > 0 ){
		    if( !empty( $rows[0]->title ) ){
			    $title[] = $rows[0]->title;
		    }
	    }
    }
    else $title[] = '/'; // V 1.2.4.s
  break;
  case 'new':
	  $title[] = $sh_LANG[$shLangIso]['_COM_SEF_SH_CREATE_NEW_LINK'] . $sefConfig->suffix;
  break;
  default:
	  $title[] = '/'; // V 1.2.4.s
	break;
}

shRemoveFromGETVarsList('option');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
shRemoveFromGETVarsList('lang');
if (!empty($catid))
  shRemoveFromGETVarsList('catid');
if (!empty($task))
  shRemoveFromGETVarsList('task');
if (!empty($id))  
  shRemoveFromGETVarsList('id');

// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------

?>
