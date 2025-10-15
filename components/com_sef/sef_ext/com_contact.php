<?php
/**
 * sh404SEF support for com_contact component.
 * Copyright Yannick Gaultier (shumisha) - 2007
 * shumisha@gmail.com
 * @version     $Id: com_contact.php 229 2008-01-21 19:53:39Z silianacom-svn $
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

       
// do something about that Itemid thing
if (preg_match('/Itemid=[0-9]+/i', $string) === false) { // if no Itemid in non-sef URL
  //global $Itemid;
  if ($sefConfig->shInsertGlobalItemidIfNone && !empty($shCurrentItemid)) {
    $string .= '&Itemid='.$shCurrentItemid;  // append current Itemid 
    $Itemid = $shCurrentItemid;
    shAddToGETVarsList('Itemid', $Itemid); // V 1.2.4.m
  }  
  if ($sefConfig->shInsertTitleIfNoItemid)
  	$title[] = $sefConfig->shDefaultMenuItemName ? 
      $sefConfig->shDefaultMenuItemName : getMenuTitle($option, null, $shCurrentItemid );
  $shItemidString = $sefConfig->shAlwaysInsertItemid ? 
    _COM_SEF_SH_ALWAYS_INSERT_ITEMID_PREFIX.$sefConfig->replacement.$shCurrentItemid
    : '';
} else {  // if Itemid in non-sef URL
  $shItemidString = $sefConfig->shAlwaysInsertItemid ? 
    _COM_SEF_SH_ALWAYS_INSERT_ITEMID_PREFIX.$sefConfig->replacement.$Itemid
    : '';
}
  
// for contact page we always add something before contact name
$task = isset($task) ? @$task : null;
$Itemid = isset($Itemid) ? @$Itemid : null;
$shName = shGetComponentPrefix($option);
$shName = empty($shName) ? getMenuTitle($option, null, $Itemid ) : $shName;
if (!empty($shName) && $shName != '/') $title[] = $shName;  // V x

// fetch contact name
if (!empty($contact_id)) {
  shRemoveFromGETVarsList('contact_id');
  $query  = "SELECT name, id FROM #__contact_details" ;
  $query .= "\n WHERE id=".$contact_id;
  $database->setQuery( $query );
  if (shTranslateUrl($option, $shLangName))
    $database->loadObject($result);
  else $database->loadObject($result, false); 
	if (!empty($result)) $title[] = $result->name;
	else $title[] = $contact_id;
}   
// V 1.2.4.q : process catid
if (!empty($catid)) {
  shRemoveFromGETVarsList('catid');
  $query  = "SELECT name, id FROM #__categories" ;
  $query .= "\n WHERE id=".$catid;
  $database->setQuery( $query );
  if (shTranslateUrl($option, $shLangName))
    $database->loadObject($result);
  else $database->loadObject($result, false);  
	if (!empty($result)) $title[] = $result->name;
	else $title[] = $catid;
} 
 
if ((@$task == "view") && isset($sefConfig->suffix)) {
	$title[count($title)-1] .= $sefConfig->suffix;
}
else {
	$title[] = '/';
}

shRemoveFromGETVarsList('option');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
shRemoveFromGETVarsList('lang');
if (!empty($task))
  shRemoveFromGETVarsList('task');
// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------	
	
?>
