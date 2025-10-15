<?php
/**
 * sh404SEF support for com_user component.
 * Copyright Yannick Gaultier (shumisha) - 2007
 * shumisha@gmail.com
 * @version     $Id: com_user.php 229 2008-01-21 19:53:39Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 * 
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
$shLangIso = shLoadPluginLanguage( 'com_user', $shLangIso, '_COM_SEF_SH_VIEW_DETAILS');
// ------------------  load language file - adjust as needed ----------------------------------------

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
if (!empty($limit))  
shRemoveFromGETVarsList('limit');
if (isset($limitstart)) 
  shRemoveFromGETVarsList('limitstart'); // limitstart can be zero

$task = isset($task) ? @$task : null;   // make sure $task is defined
switch ($task) {
	case 'UserDetails' :
	  $title[] = $sh_LANG[$shLangIso]['_COM_SEF_SH_VIEW_DETAILS'];
	  shRemoveFromGETVarsList('task');
	break;
  
	default:
	  $dosef = false;  
	break;  
}
  
// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------
  
?>
