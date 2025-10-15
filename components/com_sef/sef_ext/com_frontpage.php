<?php
/**
 * sh404SEF support for com_frontpage component.
 * Yannick Gaultier (shumisha)
 * shumisha@gmail.com
 * @version     $Id: com_frontpage.php 229 2008-01-21 19:53:39Z silianacom-svn $
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

                                         
  shRemoveFromGETVarsList('option');
  if (!empty($lang))
    shRemoveFromGETVarsList('lang'); 
  if (isset($Itemid))   
  shRemoveFromGETVarsList('Itemid');
  shRemoveFromGETVarsList('limit');
  if (isset($limitstart)) 
    shRemoveFromGETVarsList('limitstart'); // limitstart can be zero
    
  $Itemid = isset($Itemid) ? @$Itemid : null;  
  $title[] = getMenuTitle($option, null, $Itemid, null, $shLangName );
	
// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------
  
?>
