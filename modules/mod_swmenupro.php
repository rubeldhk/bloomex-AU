<?php
/**
* swmenupro v5.5
* http://swmenupro.com
* Copyright 2006 Sean White
**/

//error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


global $database, $my, $Itemid, $mosConfig_offset,$mosConfig_live_site,$mainframe,$mosConfig_absolute_path,$mosConfig_lang;

require_once($mosConfig_absolute_path."/modules/mod_swmenupro/styles.php");
require_once($mosConfig_absolute_path."/modules/mod_swmenupro/functions.php");

$do_menu=1;
$template = @$params->get( 'template' ) ? strval( $params->get('template') ) :  "All";
$language = @$params->get( 'language' ) ? strval( $params->get('language') ) :  "All";
$component = @$params->get( 'component' ) ? strval( $params->get('component') ) :  "All";

if($template!=""  && $template!="All"  ){
	if($mainframe->getTemplate()!=$template){$do_menu=0;}
}
if($language!=""  && $language!="All" ){
	if($mosConfig_lang!=$language){$do_menu=0;}
}
if($component!=""  && $component!="All" ){

	if(trim( mosGetParam( $_REQUEST, 'option', '' ) )!=$component){$do_menu=0;}
}

if($do_menu){

$menu = @$params->get( 'menutype' ) ? strval( $params->get('menutype') ) :  "mainmenu";
$id = @$params->get( 'moduleID' )?intval( $params->get('moduleID') ) :  0;
$menustyle = @$params->get( 'menustyle' )? strval( $params->get('menustyle') ) :  "popoutmenu";
$parent_level = @$params->get('parent_level') ? intval( $params->get('parent_level') ) :  0;
$levels = @$params->get('levels') ? intval( $params->get('levels') ) :  25;
$parent_id = @$params->get('parentid') ? intval( $params->get('parentid') ) :  0;
$active_menu = @$params->get('active_menu') ? intval( $params->get('active_menu') ) :  0;
$hybrid = @$params->get('hybrid') ? intval( $params->get('hybrid') ) :  0;
$editor_hack = @$params->get('editor_hack') ? intval( $params->get('editor_hack') ) :  0;
$sub_indicator = @$params->get('sub_indicator') ? intval( $params->get('sub_indicator') ) :  0;
$css_load = @$params->get('cssload') ? $params->get('cssload'): 0 ;
$use_table = @$params->get('tables') ? $params->get('tables'): 0 ;
$cache = @$params->get('cache') ? $params->get('cache'): 0 ;
$cache_time = @$params->get('cache_time') ? $params->get('cache_time'): "1 hour" ;
$selectbox_hack = @$params->get('selectbox_hack') ? intval( $params->get('selectbox_hack') ) :  0;
$padding_hack = @$params->get('padding_hack') ? intval( $params->get('padding_hack') ) :  0;
$show_shadow = @$params->get('show_shadow') ? intval( $params->get('show_shadow') ) :  0;

$my_task = trim( mosGetParam( $_REQUEST, 'task', '' ) );
if(($my_task=="edit" || $my_task=="new") && $editor_hack) {
  $editor_hack=0;
}else{
  $editor_hack=1;	
}

$query = "SELECT * FROM #__swmenu_config WHERE id = ".$id;
$database->setQuery( $query );
$result = $database->loadObjectList();
$swmenupro=array();
while (list ($key, $val) = each ($result[0]))
{
    $swmenupro[$key]=$val;
}


$content= "\n<!--swMenuPro5.6 ".$menustyle." by http://www.swmenupro.com-->\n";   

if($menu && $id && $menustyle){
	if($css_load==2){
		include_once( "modules/mod_swmenupro/load_css_script.php" );
		$content.= "<script type='text/javascript'>\n";
		$content.= "<!--\n";
		$content.= "SWimportStyleSheet('".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$id.".css');\n";
		$content.= "-->\n";
		$content.= "</script>\n";
	}else if($css_load==1){
    	$content.= "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$id.".css' rel='stylesheet' />\n";	
	}
	if(($menu=="virtuemart"||$menu=="virtueprod")&&$parent_id){
    $parent_id=$parent_id+10000;
	}
	$ordered=swGetMenu($menu,$id,$hybrid,$cache,$cache_time,$use_table,$parent_id,$levels);
	if (count($ordered)){
 		if ($parent_level){   
 	   		$ordered=sw_getsubmenu($ordered,$parent_level,$levels,$menu);
 	   		if($active_menu){$active_menu=sw_getactive($ordered);}
 		} 
 		if ($active_menu&&!$parent_level){   
 	    	$active_menu=sw_getactive($ordered);
 	    	$ordered = chain('ID', 'PARENT', 'ORDER', $ordered, $parent_id, $levels); 
 		}
 		
	}

	if(count($ordered)){
		if ($menustyle == "clickmenu"){$content.= doClickMenu($ordered, $swmenupro, $css_load,$active_menu,$selectbox_hack,$padding_hack);}
		if ($menustyle == "treemenu"){$content.= doTreeMenu($ordered, $swmenupro, $css_load,$active_menu);}
		if ($menustyle == "popoutmenu"){$content.= doPopoutMenu($ordered, $swmenupro, $css_load, $active_menu);}
		if ($menustyle == "gosumenu" && $editor_hack){$content.= doGosuMenu($ordered, $swmenupro, $active_menu, $css_load,$selectbox_hack,$padding_hack);}
		if ($menustyle == "transmenu"){$content.= doTransMenu($ordered, $swmenupro, $active_menu, $sub_indicator, $parent_id, $css_load,$selectbox_hack,$show_shadow,$padding_hack);}
		if ($menustyle == "tabmenu"){$content.= doTabMenu($ordered, $swmenupro, $parent_id, $css_load,$active_menu);}
		if ($menustyle == "dynamictabmenu"){$content.= doDynamicTabMenu($ordered, $swmenupro, $parent_id, $css_load,$active_menu);}
		if ($menustyle == "slideclick"){$content.= doSlideClick($ordered, $swmenupro, $css_load,$active_menu,$selectbox_hack,$padding_hack);}
		if ($menustyle == "clicktransmenu"){$content.= doClickTransMenu($ordered, $swmenupro, $css_load,$active_menu,$selectbox_hack,$padding_hack);}
	}
}
$content.="\n<!--End SWmenuPro menu module-->\n";

return $content;
}
?>



