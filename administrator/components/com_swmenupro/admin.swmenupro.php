<?php
/**
* swmenupro v5.0
* http://swonline.biz
* Copyright 2006 Sean White
**/

// ensure this file is being included by a parent file
//error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

if (file_exists($mosConfig_absolute_path.'/administrator/components/com_swmenupro/language/default.ini'))
{
$filename = $mosConfig_absolute_path.'/administrator/components/com_swmenupro/language/default.ini';
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);
include($mosConfig_absolute_path.'/administrator/components/com_swmenupro/language/'.$contents);
}else{
include($mosConfig_absolute_path.'/administrator/components/com_swmenupro/language/english.php');
}


require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mosConfig_absolute_path . "/includes/frontend.php");
require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.swmenupro.class.php");


$cid = mosGetParam( $_REQUEST, 'cid', array(0) );

if (!is_array( $cid )) {
	$cid = array(0);
}
switch ($task)
{
	case 'preview':
	preview($cid[0], $option );
	break;
	
	case 'images':
	imageManager($cid[0], $option );
	break;
	
	case 'imageFiles':
	imageFiles($cid[0], $option );
	break;
	
	case "new":
	editModule( '0', $option);
	break;

	case "saveedit":
	saveconfig($cid[0], $option);
	break;

	case 'uploadfile':
	uploadPackage( );
	break;
	
	case 'uploadlanguage':
	uploadPackage( );
	break;
	
	case 'changelanguage':
	changeLanguage( );
	break;

	case "upgrade":
	upgrade($option);
	break;

	case "exportMenu":
	$msg= exportMenu($cid[0], $option);
	mosRedirect( "index2.php?task=showmodules&option=$option&limit=$limit&limitstart=$limitstart",$msg );
	break;

	case "imagesave":
	saveImages($cid[0], $option);
	break;

	case "manualsave":
	saveCSS($cid[0], $option);
	break;

	case "editDhtmlMenu":
	editDhtmlMenu( $cid[0], $option );
	break;

	case "editCSS":
	editCSS( $cid[0], $option );
	break;

	case "remove":
	{
		if(is_array($cid) && count($cid) >1)
		{
			foreach($cid as $delid)
			{
				removeMyMenu( $delid, $option );
			}
		}
		else
		{
			$delid = $cid[0];
			removeMyMenu( $delid, $option );
		}
	} break;

	default:
	showModules( $option );
	break;
}


function showModules( $option )
{
	global $database, $my, $mainframe,$mosConfig_absolute_path;

	$limit =  intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

	// get the total number of records
	$database->setQuery( "SELECT count(*) FROM #__modules WHERE (module='mod_swmenupro') AND (params!='')");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	require_once( $mosConfig_absolute_path."/administrator/includes/pageNavigation.php" );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$database->setQuery( "SELECT m.*, u.name AS editor, g.name AS groupname"
	. "\nFROM #__modules AS m"
	. "\nLEFT JOIN #__users AS u ON u.id = m.checked_out"
	. "\nLEFT JOIN #__groups AS g ON g.id = m.access"
	. "\nWHERE (module='mod_swmenupro') "
	. "\nAND (m.params!='') "
	. "\nORDER BY position, ordering"
	. "\nLIMIT $limitstart,$limit"
	);
	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	$lists=array();
	$menus[]= mosHTML::makeOption( '-999',_SW_SELECT_MENU );
	//$menus[]= mosHTML::makeOption( 'standard','Mambo Standard' );
	$menus[]= mosHTML::makeOption( 'popoutmenu',_SW_TIGRA_MENU );
	$menus[]= mosHTML::makeOption( 'gosumenu',_SW_MYGOSU_MENU );
	$menus[]= mosHTML::makeOption( 'clickmenu',_SW_CLICK_MENU );
	$menus[]= mosHTML::makeOption( 'clicktransmenu',_SW_CLICK_TRANS_MENU );
	$menus[]= mosHTML::makeOption( 'slideclick',_SW_SLIDECLICK_MENU );
	$menus[]= mosHTML::makeOption( 'treemenu',_SW_TREE_MENU );
	$menus[]= mosHTML::makeOption( 'transmenu',_SW_TRANS_MENU );
	$menus[]= mosHTML::makeOption( 'tabmenu',_SW_TAB_MENU );
	$menus[]= mosHTML::makeOption( 'dynamictabmenu',_SW_DYN_TAB_MENU );

	$lists['menutype']= mosHTML::selectList( $menus, 'menutype','id="menutype" class="inputbox" style="width:180px" ','value', 'text' );

	$query = 'SELECT DISTINCT #__modules.title AS value, #__modules.id AS id FROM #__modules WHERE module="mod_swmenupro" AND (params!="")';
	$database->setQuery( $query );
	$menus = $database->loadObjectList();
	$menutypes3[]= mosHTML::makeOption( '-999', _SW_SELECT_STYLE );

	foreach($menus as $menutypes2){
		$menutypes3[]= mosHTML::makeOption( addslashes($menutypes2->id), addslashes($menutypes2->value) );
	}
	$lists['menustyle']= mosHTML::selectList( $menutypes3, 'menu_id','id="menu_id" class="inputbox" size="1" style="width:180px" ','value', 'text');

	HTML_swmenupro::showModules( $rows, $option, $pageNav,$lists,$menus );
	HTML_swmenupro::footer( );
}

function preview( &$cid, $option )
{
	global $database,$mosConfig_absolute_path;
	include($mosConfig_absolute_path.'/administrator/components/com_swmenupro/preview.php');
}

function imageManager( &$cid, $option )
{
	global $database,$mosConfig_absolute_path,$mosConfig_live_site;
	include($mosConfig_absolute_path.'/administrator/components/com_swmenupro/ImageManager/manager.php');
}

function imageFiles( &$cid, $option )
{
	global $database,$mosConfig_absolute_path,$mosConfig_live_site;
	include($mosConfig_absolute_path.'/administrator/components/com_swmenupro/ImageManager/images.php');
}

function removeMyMenu( &$cid, $option )
{
	global $database,$mosConfig_absolute_path;
	$cid = mosGetParam( $_REQUEST, 'id', 0 );
	$database->setQuery( "SELECT * FROM #__modules WHERE id = '$cid'" );
	$database->query();
	$database->loadObject($row);

	$database->setQuery( "DELETE FROM #__modules WHERE id = '$cid'" );
	$database->query();
	$database->setQuery( "DELETE FROM #__modules_menu WHERE moduleid = '$cid'" );
	$database->query();

	$database->setQuery( "DELETE FROM #__swmenu_config WHERE id = '$cid'" );
	$database->query();
	$database->setQuery( "DELETE FROM #__swmenu_extended WHERE moduleID = '$cid'" );
	$database->query();

	$file = $mosConfig_absolute_path."/modules/mod_swmenupro/styles/menu".$cid.".css";
	if ( file_exists($file)){
		unlink ($file);
	}
	$file = $mosConfig_absolute_path."/modules/mod_swmenupro/cache/menu".$cid.".cache";
	if ( file_exists($file)){
		unlink ($file);
	}
	$msg=_SW_DELETE_MODULE_MESSAGE;
	$limit = intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	mosRedirect( "index2.php?task=showmodules&option=$option&limit=$limit&limitstart=$limitstart",$msg );
}


function editDhtmlMenu($id, $option){
	global $database, $my, $mainframe, $mosConfig_absolute_path,$mosConfig_dbprefix;
	global $mosConfig_lang, $mosConfig_offset,$mosConfig_db;
  
	
	$new=intval( mosGetParam( $_REQUEST, 'newmenu', 0 ));
	if($new){$id=0;}
	if(!$id){
		$id=intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$menuid = mosGetParam( $_REQUEST, 'menu_id', 0 );
		if(!$id && $menuid){
			$cid=$menuid;
		}else{
			$cid=$id;
		}
	}else{
		$cid=$id;
	}
	$swmenupro_array=array();
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	$row = new mosModule( $database );
	// load the row from the db table
	$row->load( $cid );
	$params = mosParseParams( $row->params );
	$menu = @$params->menutype ? $params->menutype : 'mainmenu';
	$menustyle = @$params->menustyle;
	$hybrid = @$params->hybrid ? $params->hybrid: 0 ;
	$css_load = @$params->cssload ? $params->cssload: 0 ;
	$use_table = @$params->tables ? $params->tables: 0 ;
	$moduleID = @$params->moduleID;
	$parent_id = @$params->parentid ? $params->parentid : '0';
	$modulename = $row->title;
	$cache = @$params->cache ? $params->cache : 0;
	$moduleclass_sfx = @$params->moduleclass_sfx ? $params->moduleclass_sfx : "";
	$cache_time = @$params->cache_time ? $params->cache_time : "1 hour";
	$active_menu = @$params->active_menu ? $params->active_menu : 0;
	$parent_level = @$params->parent_level ? $params->parent_level: 0;
	$levels = @$params->levels ? $params->levels: 0;
	$onload_hack = @$params->onload_hack ? $params->onload_hack: 0;
	$editor_hack = @$params->editor_hack ? $params->editor_hack: 0;
	$sub_indicator = @$params->sub_indicator ? $params->sub_indicator: 0;
	$selectbox_hack = @$params->selectbox_hack ? $params->selectbox_hack: 0;
	$padding_hack = @$params->padding_hack ? $params->padding_hack: 0;
	$show_shadow = @$params->show_shadow ? $params->show_shadow: 0;
	$template = @$params->template ? $params->template: "";
	$language = @$params->language ? $params->language: "";
	$component = @$params->component ? $params->component: "";

	$limit = intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$pageNav->limit=$limit;
	$pageNav->limitstart=$limitstart;

	
	
	
	if(!$id){
		$menustyle = mosGetParam( $_REQUEST, 'menutype', "popoutmenu" );
		$copyCSS = mosGetParam( $_REQUEST, 'copyCSS', "" );
		$modulename = "";
		$row->id=0;
		if($copyCSS){
			$swmenupro_array=swGetMenuLinks2($menu,$cid,$hybrid,1);
		}else{
			$swmenupro_array=swGetMenuLinks2($menu,0,$hybrid,1);
		}
	}else{
		$swmenupro_array=swGetMenuLinks2($menu,$row->id,$hybrid,1);
	}
	if (count($swmenupro_array)){
		$ordered = chain2('ID', 'PARENT', 'ORDER', $swmenupro_array, $parent_id, $levels);
		$i=0;
		foreach ($ordered as $row2){
			if($menustyle=="clickmenu" || $menustyle=="tabmenu" || $menustyle=="dynamictabmenu"|| $menustyle=="slideclick"){
				if ((@$ordered[($i)]['indent']>1 )){
					$ordered[$i]['indent']=1;
				}
				if ((@$ordered[($i+1)]['PARENT']==$row2['ID'] )){
					$ordered[$i]['TYPE']="folder";
				}else{
					$ordered[$i]['TYPE']="doc";
				}
				if ((@$ordered[($i)]['indent']==1 )){
					$ordered[$i]['TYPE']="doc";
				}
			}else{
				if ((@$ordered[($i+1)]['PARENT']==$row2['ID'] )){
					$ordered[$i]['TYPE']="folder";
				}else{
					$ordered[$i]['TYPE']="doc";
				}
			}
			$i++;
		}
	}else{
		$ordered = array();
		$menudisplay=0;
	}

	if(!$id){
		
		$row2 = new swmenuproMenu( $database );

		if($cid>0){
			$row2->load( $cid );
			$row2->id=0;
		}else{
			if($menustyle=="treemenu"){
				if (!$row2->treemenu()) {
					echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			if($menustyle=="tabmenu" || $menustyle=="dynamictabmenu"){
				if (!$row2->tabmenu()) {
					echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			if($menustyle=="clickmenu" || $menustyle=="clicktransmenu"){
				if (!$row2->clickmenu()) {
					echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			if($menustyle=="slideclick"){
				if (!$row2->slideclickmenu()) {
					echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			if($menustyle=="gosumenu" || $menustyle=="transmenu" ){
				if (!$row2->gosumenu()) {
					echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}

	}else{
		$row2 = new swmenuproMenu( $database );
		$row2->load( $id );
	}

	$padding1 = explode("px", $row2->main_padding);
	$padding2 = explode("px", $row2->sub_padding);
	for($i=0;$i<4; $i++){
		$padding1[$i]=trim($padding1[$i]);
		$padding2[$i]=trim($padding2[$i]);
	}
	$border1 = explode(" ", $row2->main_border);
	$border2 = explode(" ", $row2->sub_border);

	$border1[0]=rtrim(trim($border1[0]),'px');
	$border2[0]=rtrim(trim($border2[0]),'px');
	$border1[1]=trim($border1[1]);
	$border2[1]=trim($border2[1]);
	$border1[2]=trim($border1[2]);
	$border2[2]=trim($border2[2]);

	$border3 = explode(" ", $row2->main_border_over);
	$border4 = explode(" ", $row2->sub_border_over);

	$border3[0]=rtrim(trim($border3[0]),'px');
	$border4[0]=rtrim(trim($border4[0]),'px');
	$border3[1]=trim($border3[1]);
	$border4[1]=trim($border4[1]);
	$border3[2]=trim($border3[2]);
	$border4[2]=trim($border4[2]);

	$database->setQuery( "SELECT position, ordering, showtitle, title FROM #__modules"
	. "\nORDER BY ordering"
	);
	if (!($orders = $database->loadObjectList())) {
		echo $database->stderr();
		return false;
	}
	$lists=array();
	$query = "SELECT position, description"
	. "\n FROM #__template_positions"
	. "\n WHERE position != ''"
	. "\n ORDER BY position"
	;
	$database->setQuery( $query );
	// hard code options for now
	$positions = $database->loadObjectList();

	$orders2 = array();
	$pos = array();
	foreach ($positions as $position) {
		$orders2[$position->position] = array();
		//$pos[] = mosHTML::makeOption( $position->position, $position->description );
	}

	$l = 0;
	$r = 0;
	for ($i=0, $n=count( $orders ); $i < $n; $i++) {
		$ord = 0;
		if (array_key_exists( $orders[$i]->position, $orders2 )) {
			$ord =count( array_keys( $orders2[$orders[$i]->position] ) ) + 1;
		}
		$orders2[$orders[$i]->position][] = mosHTML::makeOption( $ord, $ord.'::'.addslashes( $orders[$i]->title ) );
	}

	// make an array for the left and right positions
	foreach ( array_keys( $orders2 ) as $v ) {
		$ord = count( array_keys( $orders2[$v] ) ) + 1;
		$orders2[$v][] = mosHTML::makeOption( $ord, $ord.'::last' );
		##$pos[] = mosHTML::makeOption( 'left' );
		##$pos[] = mosHTML::makeOption( 'right' );
		$pos[] = mosHTML::makeOption( $v );
	}

	// build the html select list
	$lists['module_position'] = mosHTML::selectList( $pos, 'position2',
	'class="inputbox" size="1" onchange="changeDynaList(\'ordering\',orders,this.options[this.selectedIndex].value, originalPos, originalOrder);"',
	'value', 'text', $row->position ? $row->position : 'left' );

	// get selected pages for $lists['selections']

	$query = 'SELECT menuid AS value FROM #__modules_menu WHERE moduleid='. $row->id;
	$database->setQuery( $query );
	$lookup = $database->loadObjectList();

	$cssload[]= mosHTML::makeOption( '0', _SW_CSS_DYNAMIC_SELECT );
	$cssload[]= mosHTML::makeOption( '1', _SW_CSS_LINK_SELECT );
	$cssload[]= mosHTML::makeOption( '2', _SW_CSS_IMPORT_SELECT );
	$cssload[]= mosHTML::makeOption( '3', _SW_CSS_NONE_SELECT );
	$lists['cssload']= mosHTML::selectList( $cssload, 'cssload','id="cssload" class="inputbox" size="1" style="width:200px" ','value', 'text', $css_load ? $css_load : '0' );

	$cachet[]= mosHTML::makeOption( '10 seconds',  _SW_10SECOND_SELECT );
	$cachet[]= mosHTML::makeOption( '1 minute', _SW_1MINUTE_SELECT );
	$cachet[]= mosHTML::makeOption( '30 minutes', _SW_30MINUTE_SELECT );
	$cachet[]= mosHTML::makeOption( '1 hour', _SW_1HOUR_SELECT );
	$cachet[]= mosHTML::makeOption( '6 hours', _SW_6HOUR_SELECT );
	$cachet[]= mosHTML::makeOption( '12 hours', _SW_12HOUR_SELECT );
	$cachet[]= mosHTML::makeOption( '1 day', _SW_1DAY_SELECT );
	$cachet[]= mosHTML::makeOption( '3 days', _SW_3DAY_SELECT );
	$cachet[]= mosHTML::makeOption( '1 week', _SW_1WEEK_SELECT );
	$lists['cache_time']= mosHTML::selectList( $cachet, 'cache_time','id="cache_time" class="inputbox" size="1" style="width:200px" ','value', 'text', $cache_time ? $cache_time : '1 hour' );

	$tables[]= mosHTML::makeOption( '0', _SW_SHOW_TABLES_SELECT );
	$tables[]= mosHTML::makeOption( '1', _SW_SHOW_BLOGS_SELECT );
	$lists['tables']= mosHTML::selectList( $tables, 'tables','id="tables" class="inputbox" size="1" style="width:200px" ','value', 'text', $use_table ? $use_table : '0' );

	$lists['parent_level'] = mosHTML::integerSelectList(0,10,1, 'parent_level', 'class="inputbox"', $parent_level );
	$lists['levels'] = mosHTML::integerSelectList(0,10,1, 'levels', 'class="inputbox"', $levels );
	$lists['hybrid'] = mosHTML::yesnoRadioList( 'hybrid', 'class="inputbox"', $hybrid );
	$lists['active_menu'] = mosHTML::yesnoRadioList( 'active_menu', 'class="inputbox"', $active_menu );
	$lists['cache'] = mosHTML::yesnoRadioList( 'cache', 'class="inputbox"', $cache );
	$lists['onload_hack'] = mosHTML::yesnoRadioList( 'onload_hack', 'class="inputbox"', $onload_hack );
	$lists['editor_hack'] = mosHTML::yesnoRadioList( 'editor_hack', 'class="inputbox"', $editor_hack );

	$lists['sub_indicator'] = mosHTML::yesnoRadioList( 'sub_indicator', 'class="inputbox"', $sub_indicator);
	$lists['selectbox_hack'] = mosHTML::yesnoRadioList( 'selectbox_hack', 'class="inputbox"', $selectbox_hack );
	$lists['padding_hack'] = mosHTML::yesnoRadioList( 'padding_hack', 'class="inputbox"', $padding_hack );
	$lists['show_shadow'] = mosHTML::yesnoRadioList( 'show_shadow', 'class="inputbox"', $show_shadow );

	$lists['showtitle'] = mosHTML::yesnoRadioList( 'showtitle', 'class="inputbox"', $row->showtitle?$row->showtitle:0);
	$lists['access']        = mosAdminMenus::Access( $row );
	// build the html select list for published
	$lists['published'] =mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published?$row->published:0);

	$query = 'SELECT DISTINCT #__menu.menutype AS value FROM #__menu';
	$database->setQuery( $query );
	$menutypes = $database->loadObjectList();
	//$menutypes3[]= mosHTML::makeOption( '-999', 'Select Source Menu' );
	//$menutypes3[]= mosHTML::makeOption( '-999', '-----------------' );
	$menutypes3[]= mosHTML::makeOption( 'swcontentmenu', _SW_SOURCE_CONTENT_SELECT );
	$menutypes3[]= mosHTML::makeOption( '-999', '-----------------');
	if(file_exists($mosConfig_absolute_path."/components/com_virtuemart/virtuemart.php")){
	$menutypes3[]= mosHTML::makeOption( 'virtuemart', 'Virtuemart Categories' );
	$menutypes3[]= mosHTML::makeOption( 'virtueprod', 'Virtuemart Products' );
	$menutypes3[]= mosHTML::makeOption( '-999', '-----------------');
	}
	
	if(file_exists($mosConfig_absolute_path."/components/com_mtree/mtree.php")){
	$menutypes3[]= mosHTML::makeOption( 'mosetstree', 'Mosets Tree component' );
	//$menutypes3[]= mosHTML::makeOption( 'virtueprod', 'Virtuemart Products' );
	$menutypes3[]= mosHTML::makeOption( '-999', '-----------------');
	}
	
	
	$menutypes3[]= mosHTML::makeOption( '-999', _SW_SOURCE_EXISTING_SELECT );
	$menutypes3[]= mosHTML::makeOption( '-999','-----------------' );



	foreach($menutypes as $menutypes2){
		$menutypes3[]= mosHTML::makeOption( addslashes($menutypes2->value), addslashes($menutypes2->value) );
	}
	$lists['menutype']= mosHTML::selectList( $menutypes3, 'menutype',' id="menutype" class="inputbox" size="1" style="width:200px" onchange="changeDynaList(\'parentid\',orders2,document.getElementById(\'menutype\').options[document.getElementById(\'menutype\').selectedIndex].value, originalPos2, originalOrder2);"','value', 'text', $menu ? $menu : 'mainmenu' );
	$categories3[]= mosHTML::makeOption( 0, 'TOP' );


	$sql =  "SELECT DISTINCT #__sections.id AS value, #__sections.title AS text
                FROM #__sections                                    
                INNER JOIN #__content ON #__content.sectionid = #__sections.id
                AND #__sections.published = 1
                ";

	$database->setQuery( $sql );
	$sections = $database->loadObjectList();
	$categories3[]= mosHTML::makeOption( -999, '--------' );
	$categories3[]= mosHTML::makeOption( -999, 'Sections' );
	$categories3[]= mosHTML::makeOption( -999, '--------' );
	foreach($sections as $sections2){
		$categories3[]= mosHTML::makeOption( ($sections2->value), $sections2->text );
	}
	$categories3[]= mosHTML::makeOption( -999, '----------' );
	$categories3[]= mosHTML::makeOption( -999, 'Categories' );
	$categories3[]= mosHTML::makeOption( -999, '----------' );


	$sql =  "SELECT DISTINCT #__categories.id AS value, #__categories.title AS text
                FROM #__categories                                  
                INNER JOIN #__content ON #__content.catid = #__categories.id
                AND #__categories.published = 1
                ";
	$database->setQuery( $sql );
	$categories = $database->loadObjectList();

	foreach($categories as $categories2){
		$categories3[]= mosHTML::makeOption( ($categories2->value+1000), $categories2->text );
	}

	foreach($categories3 as $category){
		$menuitems['swcontentmenu'][] = mosHTML::makeOption( $category->value, addslashes($category->text) );

	}

if(file_exists($mosConfig_absolute_path."/components/com_virtuemart/virtuemart.php")){
	$categories4[]= mosHTML::makeOption( 0, 'All Categories (top)' );


	$sql =  "SELECT DISTINCT #__vm_category.category_id AS value, #__vm_category.category_name AS text
                FROM #__vm_category ";

	$database->setQuery( $sql );
	$sections = $database->loadObjectList();
	$categories4[]= mosHTML::makeOption( -999, '--------' );
	$categories4[]= mosHTML::makeOption( -999, 'Categories' );
	$categories4[]= mosHTML::makeOption( -999, '--------' );
	foreach($sections as $sections2){
		$categories4[]= mosHTML::makeOption( ($sections2->value), $sections2->text );
	}

	foreach($categories4 as $category){
		$menuitems['virtuemart'][] = mosHTML::makeOption( $category->value, addslashes($category->text) );
		$menuitems['virtueprod'][] = mosHTML::makeOption( $category->value, addslashes($category->text) );
	}
}
if(file_exists($mosConfig_absolute_path."/components/com_mtree/mtree.php")){
	$categories5[]= mosHTML::makeOption( 0, 'All Categories (top)' );


	$sql =  "SELECT DISTINCT #__mt_cats.cat_id AS value, #__mt_cats.cat_name AS text
                FROM #__mt_cats ";

	$database->setQuery( $sql );
	$sections = $database->loadObjectList();
	$categories5[]= mosHTML::makeOption( -999, '--------' );
	$categories5[]= mosHTML::makeOption( -999, 'Categories' );
	$categories5[]= mosHTML::makeOption( -999, '--------' );
	foreach($sections as $sections2){
		$categories5[]= mosHTML::makeOption( ($sections2->value), $sections2->text );
	}

	foreach($categories5 as $category){
		$menuitems['mosetstree'][] = mosHTML::makeOption( $category->value, addslashes($category->text) );
		//$menuitems['virtueprod'][] = mosHTML::makeOption( $category->value, addslashes($category->text) );
	}
}
	$menuitems2=array();
	$database->setQuery( "SELECT m.*"
	. "\n FROM #__menu m"
	//. "\n WHERE type != 'url'"
	//. "\n WHERE type != 'separator'"
	. "\n WHERE published = '1'"
	. "\n ORDER BY menutype, parent, ordering"
	);
	$mitems = $database->loadObjectList();
	$mitems_temp = $mitems;

	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ( $mitems as $v ) {
		$id = $v->id;
		$pt = $v->parent;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push( $list, $v );
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	$list = swmenuTreeRecurse( intval( $mitems[0]->parent ), '', array(), $children );

	// Code that adds menu name to Display of Page(s)
	$text_count = "0";
	$mitems_spacer = "";
	foreach ($list as $list_a) {
		foreach ($mitems_temp as $mitems_a) {
			if ($mitems_a->id == $list_a->id) {
				// Code that inserts the blank line that seperates different menus
				if ($mitems_a->menutype <> $mitems_spacer) {
					$list_temp[] = mosHTML::makeOption( -99, '----' );
					$menuitems[$mitems_a->menutype][] = mosHTML::makeOption( 0, "TOP" );
					$mitems_spacer = $mitems_a->menutype;
				}
				$text = addslashes($mitems_a->menutype." / ".$list_a->treename);
				$text2 = addslashes($list_a->treename);
				$list_temp[] = mosHTML::makeOption( $list_a->id, $text );
				$menuitems[$mitems_a->menutype][] = mosHTML::makeOption( $list_a->id, $text2 );
				if ( strlen($text) > $text_count) {
					$text_count = strlen($text);
				}
			}
		}
	}
	$list = $list_temp;

	$mitems2 = array();
	$mitems2[] = mosHTML::makeOption( 0, 'All' );
	$mitems2[] = mosHTML::makeOption( -99, '----' );
	$mitems2[] = mosHTML::makeOption( -999, 'None' );

	foreach ($list as $item) {
		$mitems2[] = mosHTML::makeOption( $item->value, $item->text );
	}
	$lists['selections'] = mosHTML::selectList( $mitems2, 'selections[]', 'class="inputbox" size="20" style="width:580px" multiple="multiple"', 'value', 'text', $lookup?$lookup:0 );

	$database->setQuery( "SELECT DISTINCT #__templates_menu.template AS text FROM #__templates_menu WHERE client_id=0"	);
	$list = $database->loadObjectList();

	$template2 = array();
	$template2[] = mosHTML::makeOption( "All", 'All' );
	//$template[] = mosHTML::makeOption( -99, '----' );
	//$template[] = mosHTML::makeOption( -999, 'None' );

	foreach ($list as $item) {
		$template2[] = mosHTML::makeOption( $item->text, $item->text );
	}
	$lists['templates'] = mosHTML::selectList( $template2, 'template', 'class="inputbox"  style="width:130px" ', 'text', 'text', $template );

	if(TableExists($mosConfig_dbprefix."languages",$mosConfig_db)) {
	
	$database->setQuery( "SELECT DISTINCT #__languages.name AS text, #__languages.code AS value FROM #__languages"	);
	$list = $database->loadObjectList();

	$language2 = array();
	$language2[] = mosHTML::makeOption( "All", 'All' );
	//$template[] = mosHTML::makeOption( -99, '----' );
	//$template[] = mosHTML::makeOption( -999, 'None' );

	foreach ($list as $item) {
		$language2[] = mosHTML::makeOption( $item->value, $item->text );
	}
	$lists['languages'] = mosHTML::selectList( $language2, 'language', 'class="inputbox"  style="width:130px" ', 'value', 'text', $language );
	}else{
		
		$lists['languages']=$mosConfig_lang;
	}
	$database->setQuery( "SELECT DISTINCT #__components.name AS text, #__components.option AS value FROM #__components WHERE link !=''"	);
	$list = $database->loadObjectList();

	$component2 = array();
	$component2[] = mosHTML::makeOption( "All", 'All' );
	$component2[] = mosHTML::makeOption( "com_content", 'Content' );
	//$template[] = mosHTML::makeOption( -999, 'None' );

	foreach ($list as $item) {
		$component2[] = mosHTML::makeOption( $item->value, $item->text );
	}
	$lists['components'] = mosHTML::selectList( $component2, 'component', 'class="inputbox"  style="width:130px" ', 'value', 'text', $component );

	$align[]= mosHTML::makeOption( 'left','left' );
	$align[]= mosHTML::makeOption( 'right','right' );
	$align[]= mosHTML::makeOption( 'texttop','texttop' );
	$align[]= mosHTML::makeOption( 'absmiddle','absmiddle' );
	$align[]= mosHTML::makeOption( 'baseline','baseline' );
	$align[]= mosHTML::makeOption( 'absbottom','absbottom' );
	$align[]= mosHTML::makeOption( 'bottom','bottom' );
	$align[]= mosHTML::makeOption( 'middle','middle' );
	$align[]= mosHTML::makeOption( 'top','top' );
	$lists['align']= mosHTML::selectList( $align, 'tree-image_align','id="tree-image_align" class="inputbox" onchange="treeInfoUpdate();"','value', 'text', '' );

	$lists['showname'] = mosHTML::yesnoSelectList( 'tree-image_showname', 'class="inputbox" id="tree-image_showname" onchange="treeInfoUpdate();"', 1 );
	$lists['target'] = mosHTML::yesnoSelectList( 'tree-image_target', 'class="inputbox" id="tree-image_target" onchange="treeInfoUpdate();"', 1 );
	$lists['showitem'] = mosHTML::yesnoSelectList( 'tree-image_showitem', 'class="inputbox" id="tree-image_showitem" onchange="treeInfoUpdate();"', 1 );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( '0', _SW_CSS_SELECT );
	$cssload[]= mosHTML::makeOption( 'border:', _SW_COMPLETE_BORDER_SELECT );
	$cssload[]= mosHTML::makeOption( 'border-top:', _SW_BORDER_TOP_SELECT );
	$cssload[]= mosHTML::makeOption( 'border-right:', _SW_BORDER_RIGHT_SELECT );
	$cssload[]= mosHTML::makeOption( 'border-bottom:', _SW_BORDER_BOTTOM_SELECT );
	$cssload[]= mosHTML::makeOption( 'border-left:', _SW_BORDER_LEFT_SELECT );
	$cssload[]= mosHTML::makeOption( 'padding:', _SW_PADDING_SELECT );
	$cssload[]= mosHTML::makeOption( 'margin:', _SW_MARGIN_SELECT );
	$cssload[]= mosHTML::makeOption( 'background:', _SW_BACKGROUND_SELECT );
	$cssload[]= mosHTML::makeOption( 'text', _SW_TEXT_SELECT );
	$cssload[]= mosHTML::makeOption( 'font:', _SW_FONT_SELECT );
	$cssload[]= mosHTML::makeOption( 'offsets', _SW_OFFSET_SELECT );
	$cssload[]= mosHTML::makeOption( 'dimensions', _SW_DIMENSION_SELECT );
	$cssload[]= mosHTML::makeOption( 'effects', _SW_EFFECT_SELECT );
	$lists['ncsstype']= mosHTML::selectList( $cssload, 'ncsstype','id="ncsstype" class="inputbox" size="1" style="width:200px" onchange="showCSS(\'ncsstype\');"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype']= mosHTML::selectList( $cssload, 'ocsstype','id="ocsstype" class="inputbox" size="1" style="width:200px" onchange="showCSS(\'ocsstype\');"','value', 'text', $css_load ? $css_load : '0' );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'none', 'none' );
	$cssload[]= mosHTML::makeOption( 'solid', 'solid' );
	$cssload[]= mosHTML::makeOption( 'dashed', 'dashed' );
	$cssload[]= mosHTML::makeOption( 'inset', 'inset' );
	$cssload[]= mosHTML::makeOption( 'outset', 'outset' );
	$cssload[]= mosHTML::makeOption( 'grooved', 'grooved' );
	$cssload[]= mosHTML::makeOption( 'double', 'double' );
	$lists['ncsstype-border-style']= mosHTML::selectList( $cssload, 'ncsstype-border-style','id="ncsstype-border-style" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype-border-style']= mosHTML::selectList( $cssload, 'ocsstype-border-style','id="ocsstype-border-style" class="inputbox" size="1" style="width:100px" ','value', 'text', $css_load ? $css_load : '0' );
	$lists['main_border_style']= mosHTML::selectList( $cssload, 'main_border_style','id="main_border_style" class="inputbox" onchange="doMainBorder();" size="1" style="width:100px"','value', 'text', $border1[1] );
	$lists['sub_border_style']= mosHTML::selectList( $cssload, 'sub_border_style','id="sub_border_style" class="inputbox" onchange="doSubBorder();" size="1" style="width:100px"','value', 'text', $border2[1] );
	$lists['main_border_over_style']= mosHTML::selectList( $cssload, 'main_border_over_style','id="main_border_over_style" class="inputbox" onchange="doMainBorder();" size="1" style="width:100px"','value', 'text', $border3[1] );
	
	
	if($menustyle=="slideclick"){
	$lists['sub_border_over_style']= mosHTML::selectList( $cssload, 'sub_border_over_style','id="sub_border_over_style" class="inputbox" onchange="doAccordSubBorder();" size="1" style="width:100px"','value', 'text', $border4[1] );
	}else{
	$lists['sub_border_over_style']= mosHTML::selectList( $cssload, 'sub_border_over_style','id="sub_border_over_style" class="inputbox" onchange="doSubBorder();" size="1" style="width:100px"','value', 'text', $border4[1] );
		
	}
	$lists['ncsstype-border-width'] = mosHTML::integerSelectList(0,10,1, 'ncsstype-border-width', 'id="ncsstype-border-width" class="inputbox"', 0 );
	$lists['ocsstype-border-width'] = mosHTML::integerSelectList(0,10,1, 'ocsstype-border-width', 'id="ocsstype-border-width" class="inputbox"', 0 );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'repeat', 'repeat' );
	$cssload[]= mosHTML::makeOption( 'repeat-x', 'repeat-x' );
	$cssload[]= mosHTML::makeOption( 'repeat-y', 'repeat-y' );
	$cssload[]= mosHTML::makeOption( 'no-repeat', 'no-repeat' );
	$lists['ncsstype-background-repeat']= mosHTML::selectList( $cssload, 'ncsstype-background-repeat','id="ncsstype-background-repeat" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype-background-repeat']= mosHTML::selectList( $cssload, 'ocsstype-background-repeat','id="ocsstype-background-repeat" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'Arial, Helvetica, sans-serif', 'Arial, Helvetica, sans-serif' );
	$cssload[]= mosHTML::makeOption( '\'Times New Roman\', Times, serif', 'Times New Roman, Times, serif' );
	$cssload[]= mosHTML::makeOption( 'Georgia, \'Times New Roman\', Times, serif', 'Georgia, Times New Roman, Times, serif' );
	$cssload[]= mosHTML::makeOption( 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif' );
	$cssload[]= mosHTML::makeOption( 'Geneva, Arial, Helvetica, sans-serif', 'Geneva, Arial, Helvetica, sans-serif' );
	$cssload[]= mosHTML::makeOption( 'Tahoma, Arial, sans-serif', 'Tahoma, Arial, sans-serif' );
	$lists['ncsstype-font-family']= mosHTML::selectList( $cssload, 'ncsstype-font-family','id="ncsstype-font-family" class="inputbox" size="1" style="width:200px"','value', 'text', '0' );
	$lists['ocsstype-font-family']= mosHTML::selectList( $cssload, 'ocsstype-font-family','id="ocsstype-font-family" class="inputbox" size="1" style="width:200px"','value', 'text', '0' );
	$lists['font_family']= mosHTML::selectList( $cssload, 'font_family','id="font_family" class="inputbox" size="1" style="width:230px"','value', 'text', $row2->font_family );
	$lists['sub_font_family']= mosHTML::selectList( $cssload, 'sub_font_family','id="sub_font_family" class="inputbox" size="1" style="width:230px"','value', 'text', $row2->sub_font_family );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'normal', 'normal' );
	$cssload[]= mosHTML::makeOption( 'italic', 'italic' );
	$cssload[]= mosHTML::makeOption( 'oblique', 'oblique' );
	$lists['ncsstype-font-style']= mosHTML::selectList( $cssload, 'ncsstype-font-style','id="ncsstype-font-style" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype-font-style']= mosHTML::selectList( $cssload, 'ocsstype-font-style','id="ocsstype-font-style" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'normal', 'normal' );
	$cssload[]= mosHTML::makeOption( 'bold', 'bold' );
	$cssload[]= mosHTML::makeOption( 'bolder', 'bolder' );
	$cssload[]= mosHTML::makeOption( 'lighter', 'lighter' );
	$lists['ncsstype-font-weight']= mosHTML::selectList( $cssload, 'ncsstype-font-weight','id="ncsstype-font-weight" class="inputbox" size="1" style="width:100px"','value', 'text', 'normal' );
	$lists['ocsstype-font-weight']= mosHTML::selectList( $cssload, 'ocsstype-font-weight','id="ocsstype-font-weight" class="inputbox" size="1" style="width:100px"','value', 'text', 'normal' );
	$lists['font_weight']= mosHTML::selectList( $cssload, 'font_weight','id="font_weight" class="inputbox" size="1" style="width:100px"','value', 'text', $row2->font_weight );
	$lists['font_weight_over']= mosHTML::selectList( $cssload, 'font_weight_over','id="font_weight_over" class="inputbox" size="1" style="width:100px"','value', 'text', $row2->font_weight_over );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'none', 'none' );
	$cssload[]= mosHTML::makeOption( 'underline', 'underline' );
	$cssload[]= mosHTML::makeOption( 'overline', 'overline' );
	$cssload[]= mosHTML::makeOption( 'line-through', 'line-through' );
	$cssload[]= mosHTML::makeOption( 'blink', 'blink' );
	$lists['ncsstype-text-decoration']= mosHTML::selectList( $cssload, 'ncsstype-text-decorations','id="ncsstype-text-decoration" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype-text-decoration']= mosHTML::selectList( $cssload, 'ocsstype-text-decorations','id="ocsstype-text-decoration" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'left', 'left' );
	$cssload[]= mosHTML::makeOption( 'right', 'right' );
	$cssload[]= mosHTML::makeOption( 'center', 'center' );
	$cssload[]= mosHTML::makeOption( 'justify', 'justify' );
	$lists['ncsstype-text-align']= mosHTML::selectList( $cssload, 'ncsstype-text-align','id="ncsstype-text-align" class="inputbox" size="1" style="width:100px"','value', 'text', 'left' );
	$lists['ocsstype-text-align']= mosHTML::selectList( $cssload, 'ocsstype-text-align','id="ocsstype-text-align" class="inputbox" size="1" style="width:100px"','value', 'text', 'left' );
	$lists['main_align']= mosHTML::selectList( $cssload, 'main_align','id="main_align" class="inputbox" size="1" style="width:100px"','value', 'text', $row2->main_align );
	$lists['sub_align']= mosHTML::selectList( $cssload, 'sub_align','id="sub_align" class="inputbox" size="1" style="width:100px"','value', 'text', $row2->sub_align );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'none', 'none' );
	$cssload[]= mosHTML::makeOption( 'capitalize', 'capitalize' );
	$cssload[]= mosHTML::makeOption( 'uppercase', 'uppercase' );
	$cssload[]= mosHTML::makeOption( 'lowercase', 'lowercase' );
	$lists['ncsstype-text-transform']= mosHTML::selectList( $cssload, 'ncsstype-text-transform','id="ncsstype-text-transform" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype-text-transform']= mosHTML::selectList( $cssload, 'ocsstype-text-transform','id="ocsstype-text-transform" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );

	$cssload=array();
	$cssload[]= mosHTML::makeOption( 'normal', 'normal' );
	$cssload[]= mosHTML::makeOption( 'pre', 'pre' );
	$cssload[]= mosHTML::makeOption( 'nowrap', 'nowrap' );
	$lists['ncsstype-white-space']= mosHTML::selectList( $cssload, 'ncsstype-white-space','id="ncsstype-white-space" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );
	$lists['ocsstype-white-space']= mosHTML::selectList( $cssload, 'ocsstype-white-space','id="ocsstype-white-space" class="inputbox" size="1" style="width:100px"','value', 'text', $css_load ? $css_load : '0' );

	$cssload=array();
	if($menustyle=="popoutmenu"){
		$cssload[]= mosHTML::makeOption( 'relative', 'relative' );
		$cssload[]= mosHTML::makeOption( 'absolute', 'absolute' );
	}else{
		$cssload[]= mosHTML::makeOption( 'left', 'left' );
		$cssload[]= mosHTML::makeOption( 'right', 'right' );
		$cssload[]= mosHTML::makeOption( 'center', 'center' );
	}
	$lists['position']= mosHTML::selectList( $cssload, 'position','id="position" class="inputbox" size="1" style="width:120px"','value', 'text', $row2->position ? $row2->position : '0' );

	$cssload=array();
	//$cssload[]= mosHTML::makeOption( '', 'Select Menu Item Group' );
	$cssload[]= mosHTML::makeOption( 'active', _SW_AUTO_SELECT );
	$cssload[]= mosHTML::makeOption( 'all', _SW_AUTO_ALL_SELECT );
	$cssload[]= mosHTML::makeOption( 'top', _SW_AUTO_TOP_SELECT );
	$cssload[]= mosHTML::makeOption( 'sub', _SW_AUTO_SUB_SELECT );
	$cssload[]= mosHTML::makeOption( 'parent', _SW_AUTO_FOLDER_SELECT );
	$cssload[]= mosHTML::makeOption( 'child', _SW_AUTO_DOCUMENT_SELECT );
	$lists['autoassign']= mosHTML::selectList( $cssload, 'autoassign','id="autoassign" class="inputbox" onchange="doSelectChange();" size="1" style="width:200px"','value', 'text');

	$cssload=array();
	$cssload[]= mosHTML::makeOption( '', _SW_ATTRIBUTE_SELECT );
	$cssload[]= mosHTML::makeOption( 'image1', _SW_ATTRIBUTE_IMAGE_SELECT );
	$cssload[]= mosHTML::makeOption( 'image2', _SW_ATTRIBUTE_OVER_IMAGE_SELECT );
	$cssload[]= mosHTML::makeOption( 'showname', _SW_ATTRIBUTE_SHOW_NAME_SELECT );
	$cssload[]= mosHTML::makeOption( 'dontshowname', _SW_ATTRIBUTE_DONT_SHOW_NAME_SELECT );
	$cssload[]= mosHTML::makeOption( 'imageleft', _SW_ATTRIBUTE_IMAGE_LEFT_SELECT );
	$cssload[]= mosHTML::makeOption( 'imageright', _SW_ATTRIBUTE_IMAGE_RIGHT_SELECT );
	$cssload[]= mosHTML::makeOption( 'islink', _SW_ATTRIBUTE_IS_LINK_SELECT );
	$cssload[]= mosHTML::makeOption( 'isnotlink', _SW_ATTRIBUTE_IS_NOT_LINK_SELECT );
	$cssload[]= mosHTML::makeOption( 'showitem', _SW_ATTRIBUTE_SHOW_ITEM_SELECT );
	$cssload[]= mosHTML::makeOption( 'dontshowitem', _SW_ATTRIBUTE_DONT_SHOW_ITEM_SELECT );
	$cssload[]= mosHTML::makeOption( 'normalcss', _SW_ATTRIBUTE_CSS_SELECT );
	$cssload[]= mosHTML::makeOption( 'overcss', _SW_ATTRIBUTE_OVER_CSS_SELECT );
	$lists['autoattrib']= mosHTML::selectList( $cssload, 'autoattrib','id="autoattrib" class="inputbox" onchange="doImageChange();" size="1" style="width:200px"','value', 'text');


	$cssload=array();
	if($menustyle=="transmenu"){
		$cssload[]= mosHTML::makeOption( 'horizontal/down', 'horizontal/down/right' );
		$cssload[]= mosHTML::makeOption( 'vertical/right', 'vertical/right' );
		$cssload[]= mosHTML::makeOption( 'horizontal/up', 'horizontal/up' );
		$cssload[]= mosHTML::makeOption( 'vertical/left', 'vertical/left' );
		$cssload[]= mosHTML::makeOption( 'horizontal/left', 'horizontal/down/left' );
	}else if($menustyle=="slideclick"){
		//$cssload[]= mosHTML::makeOption( 'horizontal/', 'horizontal/down' );
		$cssload[]= mosHTML::makeOption( 'vertical', 'vertical' );
		$cssload[]= mosHTML::makeOption( 'horizontal/h', 'horizontal/horizontal' );
		$cssload[]= mosHTML::makeOption( 'horizontal/d', 'horizontal/down' );
	}else if($menustyle=="clicktransmenu"){
		//$cssload[]= mosHTML::makeOption( 'horizontal/down', 'horizontal/down' );
		$cssload[]= mosHTML::makeOption( 'vertical/right', 'vertical/right' );
		//$cssload[]= mosHTML::makeOption( 'horizontal/up', 'horizontal/up' );
		$cssload[]= mosHTML::makeOption( 'vertical/left', 'vertical/left' );
	}else{
		$cssload[]= mosHTML::makeOption( 'horizontal', 'horizontal' );
		$cssload[]= mosHTML::makeOption( 'vertical', 'vertical' );
	}
	$lists['orientation']= mosHTML::selectList( $cssload, 'orientation','id="orientation" class="inputbox" size="1" style="width:120px"','value', 'text', $row2->orientation ? $row2->orientation : '0' );




	switch ($menustyle)
	{
		case "popoutmenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.tigra.html.php");
		popoutMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "clickmenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.click.html.php");
		clickMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "clicktransmenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.clicktrans.html.php");
		clickTransMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "treemenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.tree.html.php");
		treeMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "gosumenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.mygosu.html.php");
		gosuMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "slideclick":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.slideclick.html.php");
		slideClickConfig($row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "transmenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.trans.html.php");
		transMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "tabmenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.csstab.html.php");
		tabMenuConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;
		case "dynamictabmenu":
		require_once( $mosConfig_absolute_path . "/administrator/components/com_swmenupro/admin.dynamictab.html.php");
		dynamicTabConfig( $row2,$row, $menu,$pageNav, $padding1, $padding2, $border1, $border2, $border3, $border4, $modulename, $ordered, $parent_id,$orders2, $lists,$menuitems,$moduleclass_sfx);
		HTML_swmenupro::footer( );
		break;

	}

}



function saveconfig($id, $option){

	global $database, $my, $mainframe,$mosConfig_absolute_path,$mosConfig_lang;

	$moduleid = mosGetParam( $_REQUEST, 'moduleID', array(0) );
	$menutype = mosGetParam( $_REQUEST, 'menutype', "mainmenu" );
	$menu = mosGetParam( $_REQUEST, 'menuid', array() );
	$export = mosGetParam( $_REQUEST, 'export2', 0 );
	$rowid = mosGetParam( $_REQUEST, 'rowid', array() );
	$showname= mosGetParam( $_REQUEST, 'showname', array() );
	$imagealign= mosGetParam( $_REQUEST, 'imagealign', array() );
	$targetlevel= mosGetParam( $_REQUEST, 'targetlevel', array() );
	$returntask = mosGetParam( $_REQUEST, 'returntask', "showmodules" );
	$msg=_SW_SAVE_MENU_MESSAGE;

	$row = new mosModule( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->position=mosGetParam($_POST, "position2", "left");
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder( "position='$row->position'" );

	$row->module="mod_swmenupro";

	$parent_id=mosGetParam( $_REQUEST, 'parentid', 0 );
	$levels=mosGetParam( $_REQUEST, 'levels', 0 );

	$moduleID = $row->id;
	$menustyle = mosGetParam( $_REQUEST, 'menustyle', 'popoutmenu' );
	$css_load = mosGetParam( $_REQUEST, 'cssload', 0 );
	$hybrid = mosGetParam( $_REQUEST, 'hybrid', 0 );
	$active_menu = mosGetParam( $_REQUEST, 'active_menu', 0 );
	$editor_hack = mosGetParam( $_REQUEST, 'editor_hack', 0 );
	$parent_level = mosGetParam( $_REQUEST, 'parent_level', 0 );
	$cache = mosGetParam( $_REQUEST, 'cache', 0 );
	$cache_time = mosGetParam( $_REQUEST, 'cache_time', "1 hour" );
	$moduleclass_sfx = mosGetParam( $_REQUEST, 'moduleclass_sfx', "" );
	$tables = mosGetParam( $_REQUEST, 'tables', 0 );

	$sub_indicator = mosGetParam( $_REQUEST, 'sub_indicator', 0 );

	$selectbox_hack = mosGetParam( $_REQUEST, 'selectbox_hack', 0 );
	$padding_hack = mosGetParam( $_REQUEST, 'padding_hack', 0 );
	$show_shadow = mosGetParam( $_REQUEST, 'show_shadow', 0 );

	$template = mosGetParam( $_REQUEST, 'template', "" );
	$language = mosGetParam( $_REQUEST, 'language', "" );

	$component = mosGetParam( $_REQUEST, 'component', "" );



	if(($row->module != "mod_mainmenu")){
		$params = "menutype=".$menutype."\n";
		$params.= "menustyle=".$menustyle."\n";
		$params.= "moduleID=".$row->id."\n";
		$params.= "levels=".$levels."\n";
		$params.= "parentid=".$parent_id."\n";
		$params.= "parent_level=".$parent_level."\n";
		$params.= "hybrid=".$hybrid."\n";
		$params.= "active_menu=".$active_menu."\n";
		$params.= "tables=".$tables."\n";
		$params.= "cssload=".$css_load."\n";
		$params.= "sub_indicator=".$sub_indicator."\n";
		$params.= "selectbox_hack=".$selectbox_hack."\n";
		$params.= "padding_hack=".$padding_hack."\n";
		$params.= "show_shadow=".$show_shadow."\n";
		$params.= "cache=".$cache."\n";
		$params.= "cache_time=".$cache_time."\n";
		$params.= "moduleclass_sfx=".$moduleclass_sfx."\n";
		$params.= "editor_hack=".$editor_hack."\n";
		$params.= "template=".$template."\n";
		$params.= "language=".$language."\n";
		$params.= "component=".$component."\n";
		$row->params = $params;




		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}

	$menus = mosGetParam( $_POST, 'selections', array() );

	$database->setQuery( "DELETE FROM #__modules_menu WHERE moduleid='$row->id'" );
	$database->query();

	foreach ($menus as $menuid){
		$database->setQuery( "INSERT INTO #__modules_menu"
		. "\nSET moduleid='$row->id', menuid='$menuid'"
		);
		$database->query();
	}





	$row = new swmenuproMenu( $database );

	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->id=$row->id?$row->id:$moduleID;

	$database->setQuery( "SELECT * FROM #__swmenu_config WHERE id='".$row->id."'");
	$database->query();
	$database->loadObject($count);

	if($count >= 1) {
		$ret = $row->_db->updateObject( $row->_tbl, $row, $row->_tbl_key );
	} else {
		$ret = $row->_db->insertObject( $row->_tbl, $row, $row->_tbl_key );
	}

	$name=strval( mosGetParam( $_REQUEST, 'name', "" ) );
	$data2=strval( mosGetParam( $_REQUEST, 'php_out', "" ) );
	$menuid=$row->id ;
	$limit=intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart=intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$data3=explode("}}",$data2);

	for ($i=0;$i<(count($data3)-1);$i++){
		$data4=explode(";;",$data3[$i]);
/*
		$val=trim($data4[0]."-id");
		$item= trim( mosGetParam( $_REQUEST, $val, 0 ) );

		$val=trim($data4[0]."_normal_css");
		$normal_css= trim( mosGetParam( $_REQUEST, $val, "" ) );

		$val=trim($data4[0]."_over_css");
		$over_css= trim( mosGetParam( $_REQUEST, $val, "" ) );


		$val=trim($data4[0]."_showitem");
		$showitem= trim( mosGetParam( $_REQUEST, $val, 1 ) );

		$val=trim($data4[0]."_target");
		$targetlevel= trim( mosGetParam( $_REQUEST, $val, 1 ) );
*/
		
		$item=$data4[23];
		$normal_css= $data4[20];
		$over_css= $data4[21];
		$showitem=$data4[22];
		$targetlevel=$data4[6];
		//echo $data4[8]."<br>".$item;
		//$image= ( mosGetParam( $_REQUEST, trim($data4[0]."-image"), "" ) );
		if($data4[8]){
			$image=substr($data4[8],3).",".$data4[10].",".$data4[11].",".$data4[13].",".$data4[12];
		}else{

			$image="";
		}
		$showname=$data4[18];
		$imagealign=$data4[19];
		if($data4[9]){
			$imageover=substr($data4[9],3).",".$data4[14].",".$data4[15].",".$data4[17].",".$data4[16];
		}else{
			$imageover="";

		}

		//$targetlevel=1;

		$database->setQuery( "SELECT COUNT(*) FROM #__swmenu_extended WHERE menu_id='".$item."' AND moduleID='".$menuid."'" );
		$database->query();
		$exists=$database->loadResult();
		if($exists && $item){

			$database->setQuery( "UPDATE #__swmenu_extended SET image ='".$image."', image_over='".$imageover."', show_name='".$showname."', image_align='".$imagealign."', target_level='".$targetlevel."', normal_css='".$normal_css."', over_css='".$over_css."', show_item='".$showitem."' WHERE menu_id='".$item."' AND moduleID='".$menuid."'");
			$database->query();


		}elseif($item){

			$database->setQuery( "INSERT INTO #__swmenu_extended VALUES ('','".$item."','".$image."','".$imageover."','".$menuid."','".$showname."','".$imagealign."','".$targetlevel."','".$normal_css."','".$over_css."','".$showitem."','')");
			$database->query();

		}
	}
	if($export){

		$msg=exportMenu($row->id,$option);
	}
	if($cache){
		$file = $mosConfig_absolute_path."/modules/mod_swmenupro/cache/menu".$row->id.".cache";
		$data="";

		if ( !file_exists($file)){
			touch ($file);
			$handle = fopen ($file, 'w'); // Let's open for read and write
			// $filedate=$now;
			$swmenupro_array=swGetMenuLinks2($menutype,$row->id,$hybrid,$tables);
			$ordered = chain2('ID', 'PARENT', 'ORDER', $swmenupro_array, $parent_id, $levels);
			foreach ($ordered as $swarray){
				$data.=implode("'..'",$swarray)."\n";
			}
			fwrite ($handle, $data); // Don't forget to increment the counter
			fclose ($handle); // Done
		}else{
			$handle = fopen ($file, 'w'); // Let's open for read and write
			$swmenupro_array=swGetMenuLinks2($menutype,$row->id,$hybrid,$tables);
			$ordered = chain2('ID', 'PARENT', 'ORDER', $swmenupro_array, $parent_id, $levels);
			foreach ($ordered as $swarray){
				$data.=implode("'..'",$swarray)."\n";
			}
			fwrite ($handle, $data); // Don't forget to increment the counter
			fclose ($handle);
		}

	}


	$limit = intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

	if($returntask=="editDhtmlMenu"){
		echo "<span class='message'>".$msg."</span>\n";
		editDhtmlMenu($row->id, $option);

	}else{
		mosRedirect( "index2.php?task=$returntask&option=$option&limit=$limit&limitstart=$limitstart",$msg );
	}
}



function exportMenu($id,$option){
	global $mosConfig_absolute_path,$database;
	include( $mosConfig_absolute_path . "/modules/mod_swmenupro/styles.php");
	$css="";

	$database->setQuery( "SELECT * FROM #__swmenu_config WHERE id='".$id."'");
	//$database->setQuery( $query );
	$new_data = $database->query();
	$swmenupro= mysql_fetch_assoc($new_data);

	$row = new mosModule( $database );
	// load the row from the db table
	$row->load( $id );
	$params = mosParseParams( $row->params );
	$menu = @$params->menutype ? $params->menutype : 'mainmenu';
	$menustyle = @$params->menustyle;
	$hybrid = @$params->hybrid ? $params->hybrid: 0 ;
	$css_load = @$params->cssload ? $params->cssload: 0 ;
	$use_table = @$params->tables ? $params->tables: 0 ;
	$levels = @$params->levels ? $params->levels: 25 ;
	$show_shadow = @$params->show_shadow ? $params->show_shadow: 0 ;
	$moduleID = @$params->moduleID;
	$parent_id = @$params->parentid ? $params->parentid : '0';
	$modulename = $row->title;

	$swmenupro_array=array();

	$swmenupro_array=swGetMenuLinks2($menu,$row->id,$hybrid,$use_table);

	if (count($swmenupro_array)){

		$ordered = chain2('ID', 'PARENT', 'ORDER', $swmenupro_array, 0, 25);


	}

	switch ($menustyle)
	{
		case "popoutmenu":
		$css=  TigraMenuStyle($swmenupro,$ordered);
		break;
		case "clickmenu":
		$css=   ClickMenuStyle($swmenupro,$ordered);
		break;
		case "slideclick":
		$css=   SlideClickStyle($swmenupro,$ordered);
		break;
		case "treemenu":
		$css=  TreeMenuStyle($swmenupro,$ordered);
		break;
		case "gosumenu":
		$css=   gosuMenuStyle($swmenupro,$ordered);
		break;
		case "transmenu":
		$css=  transMenuStyle($swmenupro,$ordered,$show_shadow);
		break;
		case "tabmenu":
		$css=   cssTabMenuStyle($swmenupro,$ordered);
		break;
		case "clicktransmenu":
		$css=   clickTransMenuStyle($swmenupro,$ordered);
		break;

		case "dynamictabmenu":
		$css=   dynamicTabMenuStyle($swmenupro,$ordered);
		break;

	}


	$file = $mosConfig_absolute_path."/modules/mod_swmenupro/styles/menu".$id.".css";
	if ( !file_exists($file)){
		touch ($file);
		$handle = fopen ($file, 'w'); // Let's open for read and write


	}
	else{
		$handle = fopen ($file, 'w'); // Let's open for read and write

	}
	rewind ($handle); // Go back to the beginning

	if(fwrite ($handle, $css)){
		$msg=_SW_SAVE_MENU_CSS_MESSAGE;
	}else{
		$msg=_SW_NO_SAVE_MENU_CSS_MESSAGE;
	} // Don't forget to increment the counter

	fclose ($handle); // Done


	return $msg;




	$limit = intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	//  mosRedirect( "index2.php?task=showmodules&option=$option&limit=$limit&limitstart=$limitstart",$msg );
}

function saveCSS($id, $option){

	global $database, $my, $mainframe,$mosConfig_absolute_path;

	$returntask = mosGetParam( $_REQUEST, 'returntask', "showmodules" );
	$css=mosGetParam($_POST,'filecontent',"");
	$id=mosGetParam($_POST,'mid',0);

	$css=str_replace( '\\', '', $css );
	$file = $mosConfig_absolute_path."/modules/mod_swmenupro/styles/menu".$id.".css";
	if ( !file_exists($file)){
		touch ($file);
		$handle = fopen ($file, 'w'); // Let's open for read and write


	}
	else{
		$handle = fopen ($file, 'w'); // Let's open for read and write

	}
	rewind ($handle); // Go back to the beginning

	fwrite ($handle, $css); // Don't forget to increment the counter
	fclose ($handle); // Done


	//echo $css;
	$limit = intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$msg=_SW_SAVE_CSS_MESSAGE;

	if($returntask=="editCSS"){
		sleep(4);
		echo "<span class='message'>".$msg."</span>\n";
		editCSS($id, $option);

	}else{
		mosRedirect( "index2.php?task=$returntask&option=$option&limit=$limit&limitstart=$limitstart",$msg );
	}

}


function editCSS( $id,$option ) {
	global $mosConfig_absolute_path, $database;
	if(!$id){$id=intval( mosGetParam( $_REQUEST, 'id', 0 ) );}

	$file = $mosConfig_absolute_path .'/modules/mod_swmenupro/styles/menu'. $id .'.css';


	if ($fp = fopen( $file, 'r' )) {
		$content = fread( $fp, filesize( $file ) );
		//$content = htmlspecialchars( $content );
		$limit = intval( mosGetParam( $_REQUEST, 'limit', 10 ) );
		$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
		$row = new mosModule( $database );
		// load the row from the db table
		$row->load( $id );
		$params = mosParseParams( $row->params );
		$menu->source = @$params->menutype ? $params->menutype : 'mainmenu';
		$menu->name=$row->title;
		HTML_swmenupro::editCSS($id, $content,$limit, $limitstart,$menu);
		HTML_swmenupro::footer( );
	} else {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation Failed: Could not open'. $file );
	}
}



function chain2($primary_field, $parent_field, $sort_field, $rows, $root_id=0, $maxlevel=25)
{
	$c = new chain2($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel);
	return $c->chainmenu_table;
}

class chain2
{
	var $table;
	var $rows;
	var $chainmenu_table;
	var $primary_field;
	var $parent_field;
	var $sort_field;

	function chain2($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel)
	{
		$this->rows = $rows;
		$this->primary_field = $primary_field;
		$this->parent_field = $parent_field;
		$this->sort_field = $sort_field;
		$this->buildchain($root_id,$maxlevel);
	}

	function buildchain($rootcatid,$maxlevel)
	{
		$row_array = array_values($this->rows);
		$row_array_size = sizeOf($row_array);
		for ($i=0;$i<$row_array_size;$i++)
		{
			$row = $row_array[$i];
			$this->table[$row[$this->parent_field]][ $row[$this->primary_field]] = $row;
		}
		$this->makeBranch($rootcatid,0,$maxlevel);
	}


	function makeBranch($parent_id,$level,$maxlevel)
	{
		$rows=$this->table[$parent_id];
		$key_array1 = array_keys($rows);
		$key_array_size1 = sizeOf($key_array1);
		for ($j=0;$j<$key_array_size1;$j++)
		//  foreach($rows as $key=>$value)
		{
			$key = $key_array1[$j];
			$rows[$key]['key'] = $this->sort_field;
		}

		usort($rows,'chainmenuCMP2');
		$row_array = array_values($rows);
		$row_array_size = sizeOf($row_array);
		for ($i=0;$i<$row_array_size;$i++)
		// foreach($rows as $item)
		{
			$item = $row_array[$i];
			$item['ORDER']=($i+1);
			$item['indent'] = $level;
			$this->chainmenu_table[] = $item;
			if((isset($this->table[$item[$this->primary_field]])) && (($maxlevel>$level+1) || ($maxlevel==0)))
			{
				$this->makeBranch($item[$this->primary_field], $level+1, $maxlevel);
			}
		}
	}
}

function chainmenuCMP2($a,$b)
{
	if($a[$a['key']] == $b[$b['key']])
	{
		return 0;
	}
	return($a[$a['key']]<$b[$b['key']])?-1:1;
}


function swmenuTreeRecurse($id, $indent, $list, &$children, $maxlevel=9999, $level=0) {
	if (@$children[$id] && $level <= $maxlevel) {
		foreach ($children[$id] as $v) {
			$id = $v->id;
			$txt = $v->name;
			$pt = $v->parent;
			$list[$id] = $v;
			$list[$id]->treename = "$indent$txt";
			$list[$id]->children = count( @$children[$id] );
			$list = swmenuTreeRecurse( $id, "$indent$txt/", $list, $children, $maxlevel, $level+1 );
		}
	}
	return $list;
}




function swGetMenuLinks2($menu,$id,$hybrid,$use_tables){
	global $mosConfig_lang, $mosConfig_mbf_content,$database,$my,$mosConfig_absolute_path,$mosConfig_offset;
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	$swmenupro_array=array();
	if ($menu=="swcontentmenu") {
		$sql =  "SELECT #__sections.* , #__swmenu_extended.*
                FROM #__sections LEFT JOIN #__swmenu_extended ON #__sections.id = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                INNER JOIN #__content ON #__content.sectionid = #__sections.id
                AND #__sections.published = 1
                AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  )
                AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )
                AND ( publish_down != '2020-01-01 00:00:00')
                ORDER BY #__content.ordering
                ";
		$database->setQuery( $sql   );
		$result = $database->loadObjectList();

		for($i=0;$i<count($result);$i++) {
			$result2=$result[$i];

			if($use_tables){
				$url="index.php?option=com_content&task=section&id=" . $result2->id ;
			}else{
				$url="index.php?option=com_content&task=blogsection&id=" . $result2->id ;
			}

			$swmenupro_array[] =array("TITLE" => $result2->title, "URL" =>  $url , "ID" => $result2->id  , "PARENT" => 0 ,  "ORDER" => $result2->ordering, "IMAGE" => $result2->image, "IMAGEOVER" => $result2->image_over, "SHOWNAME" => $result2->show_name, "IMAGEALIGN" => $result2->image_align, "TARGETLEVEL" => $result2->target_level, "TARGET" => 0,"ACCESS" => $result2->access,"NCSS" => $result2->normal_css,"OCSS" => $result2->over_css,"SHOWITEM" => $result2->show_item );
		}

		$sql =  "SELECT #__categories.* , #__swmenu_extended.*
                FROM #__categories LEFT JOIN #__swmenu_extended ON (#__categories.id+1000) = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                INNER JOIN #__content ON #__content.catid = #__categories.id
                AND #__categories.published = 1
                AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  )
                AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )
                AND ( publish_down != '2020-01-01 00:00:00')
                ORDER BY #__content.ordering
                ";

		$database->setQuery( $sql   );
		$result = $database->loadObjectList();

		for($i=0;$i<count($result);$i++) {
			$result2=$result[$i];


			if($use_tables){
				$url="index.php?option=com_content&task=category&id=" . $result2->id ;
			}else{
				$url="index.php?option=com_content&task=blogcategory&id=" . $result2->id ;
			}

			$swmenupro_array[] =array("TITLE" => $result2->title, "URL" =>  $url , "ID" => $result2->id+1000  , "PARENT" => $result2->section ,  "ORDER" => $result2->ordering, "IMAGE" => $result2->image, "IMAGEOVER" => $result2->image_over, "SHOWNAME" => $result2->show_name, "IMAGEALIGN" => $result2->image_align, "TARGETLEVEL" => $result2->target_level, "TARGET" => 0,"ACCESS" => $result2->access,"NCSS" => $result2->normal_css,"OCSS" => $result2->over_css,"SHOWITEM" => $result2->show_item );
		}

		$sql =  "SELECT #__content.* , #__swmenu_extended.*
                FROM #__content LEFT JOIN #__swmenu_extended ON (#__content.id+10000) = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                INNER JOIN #__categories ON #__content.catid = #__categories.id
                AND #__content.state = 1
                AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  )
                AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )
                AND ( publish_down != '2020-01-01 00:00:00')
                ORDER BY #__content.ordering
                ";
		$database->setQuery( $sql   );
		$result = $database->loadObjectList();

		for($i=0;$i<count($result);$i++) {
			$result2=$result[$i];


			$url="index.php?option=com_content&task=view&id=" . $result2->id ;
			$swmenupro_array[] =array("TITLE" => $result2->title, "URL" =>  $url , "ID" => $result2->id+10000  , "PARENT" => $result2->catid+1000 ,  "ORDER" => $result2->ordering, "IMAGE" => $result2->image, "IMAGEOVER" => $result2->image_over, "SHOWNAME" => $result2->show_name, "IMAGEALIGN" => $result2->image_align, "TARGETLEVEL" => $result2->target_level, "TARGET" => 0,"ACCESS" => $result2->access,"NCSS" => $result2->normal_css,"OCSS" => $result2->over_css,"SHOWITEM" => $result2->show_item );
		}
	}else if ($menu=="virtuemart" || $menu=="virtueprod") {
		$sql =  "SELECT #__vm_category.* , #__swmenu_extended.*,#__vm_category_xref.*
                FROM #__vm_category LEFT JOIN #__swmenu_extended ON #__vm_category.category_id = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                INNER JOIN #__vm_category_xref ON #__vm_category_xref.category_child_id= #__vm_category.category_id
                AND #__vm_category.category_publish = 'Y'
                ORDER BY #__vm_category.list_order
                ";
		$database->setQuery( $sql   );
		$result = $database->loadObjectList();

		for($i=0;$i<count($result);$i++) {
			$result2=$result[$i];
			$url="index.php?option=com_virtuemart&page=shop.browse&category_id=" . $result2->category_id . "&Itemid=".($result2->category_id+10000) ;
			$swmenupro_array[] =array("TITLE" => $result2->category_name, "URL" =>  $url , "ID" => $result2->category_id  , "PARENT" => $result2->category_parent_id ,  "ORDER" => $result2->list_order, "IMAGE" => $result2->image, "IMAGEOVER" => $result2->image_over, "SHOWNAME" => $result2->show_name, "IMAGEALIGN" => $result2->image_align, "TARGETLEVEL" => $result2->target_level, "TARGET" => 0,"ACCESS" => 0,"NCSS" => $result2->normal_css,"OCSS" => $result2->over_css,"SHOWITEM" => $result2->show_item  );
		
		if ($menu=="virtueprod") {
		$sql =  "SELECT #__vm_product.* , #__swmenu_extended.*,#__vm_product_category_xref.*
                FROM #__vm_product LEFT JOIN #__swmenu_extended ON (#__vm_product.product_id+1000) = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                INNER JOIN #__vm_product_category_xref ON #__vm_product_category_xref.product_id= #__vm_product.product_id
                AND #__vm_product.product_publish = 'Y'
                AND #__vm_product_category_xref.category_id = $result2->category_id
          
                ";
		$database->setQuery( $sql   );
		$result3 = $database->loadObjectList();
		for($j=0;$j<count($result3);$j++) {
			$result4=$result3[$j];
			$url="index.php?option=com_virtuemart&page=shop.product_details&flypage=shop.flypage&product_id=".$result4->product_id."&category_id=" . $result4->category_id . "&manufacturer_id=".$result4->vendor_id."&Itemid=".($result2->category_id+10000) ;
			$swmenupro_array[] =array("TITLE" => $result4->product_name, "URL" =>  $url , "ID" => ($result4->product_id+1000)  , "PARENT" => ($result2->category_id?($result2->category_id):0) ,  "ORDER" => $result2->list_order, "IMAGE" => $result4->image, "IMAGEOVER" => $result4->image_over, "SHOWNAME" => $result4->show_name, "IMAGEALIGN" => $result4->image_align, "TARGETLEVEL" => $result4->target_level, "TARGET" => 0,"ACCESS" => 0,"NCSS" => $result4->normal_css,"OCSS" => $result4->over_css,"SHOWITEM" => $result4->show_item  );
		}
		}
		}


	}else if ($menu=="mosetstree" ) {
		$sql =  "SELECT #__mt_cats.* , #__swmenu_extended.*
                FROM #__mt_cats LEFT JOIN #__swmenu_extended ON #__mt_cats.cat_id = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                AND #__mt_cats.cat_approved = '1'
                AND #__mt_cats.cat_published = '1'
                AND #__mt_cats.cat_links > 0
                ORDER BY #__mt_cats.ordering
                ";
		$database->setQuery( $sql   );
		$result = $database->loadObjectList();

		for($i=0;$i<count($result);$i++) {
			$result2=$result[$i];
			$url="index.php?option=com_mtree&task=listcats&cat_id=" . $result2->cat_id . "&Itemid=".($result2->cat_id) ;
			$swmenupro_array[] =array("TITLE" => $result2->cat_name, "URL" =>  $url , "ID" => $result2->cat_id  , "PARENT" => $result2->cat_parent ,  "ORDER" => $result2->ordering, "IMAGE" => $result2->image, "IMAGEOVER" => $result2->image_over, "SHOWNAME" => $result2->show_name, "IMAGEALIGN" => $result2->image_align, "TARGETLEVEL" => $result2->target_level, "TARGET" => 0,"ACCESS" => 0,"NCSS" => $result2->normal_css,"OCSS" => $result2->over_css,"SHOWITEM" => $result2->show_item  );
		
		
		}
	}else{
		if ($hybrid){
			$sql =  "SELECT #__content.*,#__swmenu_extended.*
                FROM #__content LEFT JOIN #__swmenu_extended ON (#__content.id+100000) = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                INNER JOIN #__categories ON #__content.catid = #__categories.id
                AND #__content.state = 1
                AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  )
                AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )
                AND ( publish_down != '2020-01-01 00:00:00')
                ORDER BY #__content.catid,#__content.ordering
                ";
			$database->setQuery( $sql   );
			$hybrid_content = $database->loadObjectList();


			$sql =  "SELECT #__categories.*,#__swmenu_extended.*
                FROM #__categories LEFT JOIN #__swmenu_extended ON (#__categories.id+10000) = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."'OR #__swmenu_extended.moduleID IS NULL)
                AND #__categories.published = 1
                ORDER BY #__categories.ordering
                ";
			$database->setQuery( $sql   );
			$hybrid_cat = $database->loadObjectList();
		}

		$sql = "SELECT #__menu.* , #__swmenu_extended.*
                FROM #__menu LEFT JOIN #__swmenu_extended ON #__menu.id = #__swmenu_extended.menu_id
                AND (#__swmenu_extended.moduleID = '".$id."' OR #__swmenu_extended.moduleID IS NULL)
                WHERE #__menu.menutype = '".$menu."' AND published = '1'
                ORDER BY parent, ordering
            ";

		$database->setQuery( $sql   );
		$result = $database->loadObjectList();

		$swmenupro_array=array();

		for($i=0;$i<count($result);$i++) {
			$result2=$result[$i];


			switch ($result2->type) {

				case 'url':
				if (preg_match( "/index.php\?/i", $result2->link )) {
					if (!preg_match( "/Itemid=/i", $result2->link )) {
						$result2->link .= "&Itemid=$result2->id";
					}
				}
				break;

				default:
				$result2->link .= "&Itemid=$result2->id";
				break;
			}
			$swmenupro_array[] =array("TITLE" => $result2->name, "URL" =>  $result2->link , "ID" => $result2->id  , "PARENT" => $result2->parent ,  "ORDER" => $result2->ordering, "IMAGE" => $result2->image, "IMAGEOVER" => $result2->image_over, "SHOWNAME" => $result2->show_name, "IMAGEALIGN" => $result2->image_align, "TARGETLEVEL" => $result2->target_level, "TARGET" => $result2->browserNav,"ACCESS" => $result2->access,"NCSS" => $result2->normal_css,"OCSS" => $result2->over_css,"SHOWITEM" => $result2->show_item );

			if ($hybrid){
				$opt=array();
				parse_str($result2->link, $opt);
				$opt['task'] = @$opt['task'] ? $opt['task']: 0;
				$opt['id'] = @$opt['id'] ? $opt['id']: 0;


				if ($opt['task']=="blogcategory" || $opt['task']=="category" ) {

					for($j=0;$j<count($hybrid_content);$j++){
						$row=$hybrid_content[$j];
						if($row->catid==$opt['id']){

							$url="index.php?option=com_content&task=view&id=" . $row->id ."&Itemid=".$result2->id;
							$swmenupro_array[] =array("TITLE" => $row->title, "URL" =>  $url , "ID" => $row->id+100000  , "PARENT" => $result2->id ,  "ORDER" => $row->ordering, "IMAGE" => $row->image, "IMAGEOVER" => $row->image_over, "SHOWNAME" => $row->show_name, "IMAGEALIGN" => $row->image_align, "TARGETLEVEL" => $row->target_level, "TARGET" => 0,"ACCESS" => $row->access,"NCSS" => $row->normal_css,"OCSS" => $row->over_css,"SHOWITEM" => $row->show_item );
						}
					}
				}else if ($opt['task']=="blogsection" || $opt['task']=="section" ) {

					for($j=0;$j<count($hybrid_cat);$j++){
						$row=$hybrid_cat[$j];
						if($row->section==$opt['id'] && $opt['id']){
							//$j=count($hybrid_cat);

							if($use_tables){
								$url="index.php?option=com_content&task=category&id=".$row->id."&Itemid=".$result2->id;
							}else{
								$url="index.php?option=com_content&task=blogcategory&id=".$row->id."&Itemid=".$result2->id;
							}
							$swmenupro_array[] =array("TITLE" => $row->title, "URL" =>  $url , "ID" => $row->id+10000  , "PARENT" => $result2->id ,  "ORDER" => $row->ordering, "IMAGE" => $row->image, "IMAGEOVER" => $row->image_over, "SHOWNAME" => $row->show_name, "IMAGEALIGN" => $row->image_align, "TARGETLEVEL" => $row->target_level, "TARGET" => 0,"ACCESS" => $row->access,"NCSS" => $row->normal_css,"OCSS" => $row->over_css,"SHOWITEM" => $row->show_item );

							for($k=0;$k<count($hybrid_content);$k++){
								$row2=$hybrid_content[$k];
								if($row2->catid==$row->id){

									$url="index.php?option=com_content&task=view&id=" . $row2->id ."&Itemid=".$result2->id;
									$swmenupro_array[] =array("TITLE" => $row2->title, "URL" =>  $url , "ID" => $row2->id+100000  , "PARENT" => $row->id+10000 ,  "ORDER" => $row2->ordering, "IMAGE" => $row2->image, "IMAGEOVER" => $row2->image_over, "SHOWNAME" => $row2->show_name, "IMAGEALIGN" => $row2->image_align, "TARGETLEVEL" => $row2->target_level, "TARGET" => 0,"ACCESS" => $row2->access,"NCSS" => $row2->normal_css,"OCSS" => $row2->over_css,"SHOWITEM" => $row2->show_item );
								}
							}
						}
					}
				}
			}
		}
	}

	return $swmenupro_array;
}



function get_Version($directory){

	global $mosConfig_absolute_path,$database,$mainframe;
	
	
	if(file_exists($mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php')){
	require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
	$componentBaseDir	= $directory;


	$xmlDoc = new DOMIT_Lite_Document();
	$xmlDoc->resolveErrors( true );

	if (!$xmlDoc->loadXML( $componentBaseDir , false, true )) {
		continue;
	}

	$root = &$xmlDoc->documentElement;


	$element 			= &$root->getElementsByPath('version', 1);
	$version 		= $element ? $element->getText() : '';
	}else{

	$parser =new  mosXMLDescription($directory);
	if ($parser->getType() == 'component'){		
	
	$version 		= $parser->getVersion('component');
	}else{
		
		$version 		= $parser->getVersion('module');
	}
	}
	return $version;


}

function changeLanguage(){
	
	global $mosConfig_absolute_path;
	
	$lang=strval( mosGetParam( $_REQUEST, 'language', "english.php" ));
	
	
	$file = $mosConfig_absolute_path."/administrator/components/com_swmenupro/language/default.ini";
	if ( !file_exists($file)){
		touch ($file);
		$handle = fopen ($file, 'w'); // Let's open for read and write


	}
	else{
		$handle = fopen ($file, 'w'); // Let's open for read and write

	}
	rewind ($handle); // Go back to the beginning

	if(fwrite ($handle, $lang)){
		$msg=_SW_SAVE_MENU_CSS_MESSAGE;
	}else{
		$msg=_SW_NO_SAVE_MENU_CSS_MESSAGE;
	} // Don't forget to increment the counter

	fclose ($handle); // Done

	
	mosRedirect( "index2.php?task=upgrade&option=com_swmenupro",$msg );
	
	
	
}

function upgrade($option,$installdir=""){

	global $mosConfig_absolute_path,$database,$mainframe;
	global $mosConfig_dbprefix;
	global $mosConfig_db;

	//require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
	$componentBaseDir	= mosPathName( $mosConfig_absolute_path . '/administrator/components/' );
	$componentDirs 		= mosReadDirectory( $componentBaseDir );
	
	$row->message="";
	$row->database_version=1;
	$columncount=0;
	
	if(TableExists($mosConfig_dbprefix."swmenu_extended",$mosConfig_db)){
		$database->setQuery("SELECT * FROM #__swmenu_extended");
		$mysql_result =  $database->query();

		while ($column_data = mysql_fetch_field ($mysql_result)) {
			$columncount++;
		}
	  if($columncount<12){
	  	$row->message.=sprintf(_SW_TABLE_UPGRADE,'#__swmenu_extended')."<br />";
	  	$database->setQuery("ALTER TABLE `#__swmenu_extended` 
  			ADD `normal_css` mediumtext,
  			ADD `over_css` mediumtext,
  			ADD `show_item` int(11) NOT NULL default '1',
  			ADD `extra` mediumtext
 			 ");
		$database->query();
	  	$row->database_version=0;
	  }
	}else{
		$row->message.=sprintf(_SW_TABLE_CREATE,'#__swmenu_extended')."<br />";
		$database->setQuery("CREATE TABLE `#__swmenu_extended` (
  			`ext_id` int(11) NOT NULL auto_increment,
 			`menu_id` int(11) NOT NULL default '0',
  			`image` varchar(100) default NULL,
  			`image_over` varchar(100) default NULL,
  			`moduleID` int(11) NOT NULL default '0',
  			`show_name` int(2) NOT NULL default '1',
  			`image_align` varchar(20) NOT NULL default 'left',
  			`target_level` int(11) NOT NULL default '1',
  			`normal_css` mediumtext,
  			`over_css` mediumtext,
  			`show_item` int(11) NOT NULL default '1',
  			`extra` mediumtext,
 			 PRIMARY KEY  (`ext_id`)
			) ");
		$database->query();
	}
	$columncount=0;
	if(TableExists($mosConfig_dbprefix."swmenu_config",$mosConfig_db)){
		$database->setQuery("SELECT * FROM #__swmenu_config");
		$mysql_result =  $database->query();

		while ($column_data = mysql_fetch_field ($mysql_result)) {
			$columncount++;
			
		}
	  if($columncount<42){
	  	$row->message.=sprintf(_SW_TABLE_UPGRADE,'#__swmenu_config')."<br />";
	  	$database->setQuery("ALTER TABLE `#__swmenu_config` 
  			ADD `extra` mediumtext,
  			MODIFY orientation varchar(20)
 			 ");
		$database->query();
	  	$row->database_version=0;
	  }
	}else{
		$row->message.=sprintf(_SW_TABLE_UPGRADE,'#__swmenu_config')."<br />";
		$database->setQuery("CREATE TABLE `#__swmenu_config` (
  			`id` int(11) NOT NULL default '0',
  			`main_top` smallint(8) default '0',
 			`main_left` smallint(8) default '0',
  			`main_height` smallint(8) default '20',
  			`sub_border_over` varchar(30) default '0',
  			`main_width` smallint(8) default '100',
  			`sub_width` smallint(8) default '100',
  			`main_back` varchar(7) default '#4682B4',
  			`main_over` varchar(7) default '#5AA7E5',
  			`sub_back` varchar(7) default '#4682B4',
  			`sub_over` varchar(7) default '#5AA7E5',
  			`sub_border` varchar(30) default '#FFFFFF',
  			`main_font_size` smallint(8) default '0',
  			`sub_font_size` smallint(8) default '0',
  			`main_border_over` varchar(30) default '0',
  			`sub_font_color` varchar(7) default '#000000',
  			`main_border` varchar(30) default '#FFFFFF',
  			`main_font_color` varchar(7) default '#000000',
  			`sub_font_color_over` varchar(7) default '#FFFFFF',
  			`main_font_color_over` varchar(7) default '#FFFFFF',
  			`main_align` varchar(8) default 'left',
  			`sub_align` varchar(8) default 'left',
  			`sub_height` smallint(7) default '20',
  			`position` varchar(10) default 'absolute',
  			`orientation` varchar(20) default 'horizontal',
  			`font_family` varchar(50) default 'Arial',
  			`font_weight` varchar(10) default 'normal',
  			`font_weight_over` varchar(10) default 'normal',
  			`level2_sub_top` int(11) default '0',
  			`level2_sub_left` int(11) default '0',
  			`level1_sub_top` int(11) NOT NULL default '0',
  			`level1_sub_left` int(11) NOT NULL default '0',
  			`main_back_image` varchar(100) default NULL,
  			`main_back_image_over` varchar(100) default NULL,
  			`sub_back_image` varchar(100) default NULL,
  			`sub_back_image_over` varchar(100) default NULL,
  			`specialA` varchar(50) default '80',
  			`main_padding` varchar(40) default '0px 0px 0px 0px',
  			`sub_padding` varchar(40) default '0px 0px 0px 0px',
  			`specialB` varchar(100) default '50',
  			`sub_font_family` varchar(50) default 'Arial',
  			PRIMARY KEY  (`id`)
			)");
		$database->query();
	}
	
	
	$database->setQuery("SELECT COUNT(*) FROM `#__components` WHERE admin_menu_link LIKE '%com_swmenupro%'");
	$com_entries=$database->loadResult();
  	
  	if($com_entries!=1){
  		$row->message.=_SW_UPDATE_LINKS."<br />";
  		$database->setQuery("DELETE FROM `#__components` WHERE admin_menu_link like '%com_swmenupro%'");
  		$database->query();
  		
  		$database->setQuery("INSERT INTO `#__components` VALUES ('', 'swMenuPro', 'option=com_swmenupro', 0, 0, 'option=com_swmenupro', 'swMenuPro', 'com_swmenupro', 0, 'js/ThemeOffice/component.png', 0, '')");
  		$database->query();
  	}
  	
  	if(file_exists($mosConfig_absolute_path . '/modules/mod_swmenupro.xml')){
  		$row->module_version=get_Version($mosConfig_absolute_path . '/modules/mod_swmenupro.xml');
  		$row->new_module_version=get_Version($mosConfig_absolute_path . '/administrator/components/com_swmenupro/modules/mod_swmenupro.sw');
  		if($row->module_version<$row->new_module_version){
  			if(copydirr($mosConfig_absolute_path."/administrator/components/com_swmenupro/modules",$mosConfig_absolute_path."/modules",0775,false)){
				unlink($mosConfig_absolute_path . '/modules/mod_swmenupro.xml');
  				rename($mosConfig_absolute_path."/modules/mod_swmenupro.sw",$mosConfig_absolute_path."/modules/mod_swmenupro.xml");
				$row->message.=_SW_MODULE_SUCCESS."<br />";
			}else{
				$row->message.=_SW_MODULE_FAIL."<br />";
			}
  		}
  	}else{
  		if(copydirr($mosConfig_absolute_path."/administrator/components/com_swmenupro/modules",$mosConfig_absolute_path."/modules",0775,false)){
				rename($mosConfig_absolute_path."/modules/mod_swmenupro.sw",$mosConfig_absolute_path."/modules/mod_swmenupro.xml");
				$row->message.=_SW_MODULE_SUCCESS."<br />";
			}else{
				$row->message.=_SW_MODULE_FAIL."<br />";
			}
  	}
  	
	
	$row->component_version=get_Version($mosConfig_absolute_path . '/administrator/components/com_swmenupro/swmenupro.xml');
	$row->module_version=get_Version($mosConfig_absolute_path . '/modules/mod_swmenupro.xml');
	
$langfile="english.php";	
	if (file_exists($mosConfig_absolute_path.'/administrator/components/com_swmenupro/language/default.ini'))
{
	$filename = $mosConfig_absolute_path.'/administrator/components/com_swmenupro/language/default.ini';
$handle = fopen($filename, "r");
$langfile = fread($handle, filesize($filename));
fclose($handle);
	
}
	
	$basedir =$mosConfig_absolute_path . "/administrator/components/com_swmenupro/language/"; 
    $handle=opendir($basedir);
    $lang=array();
    $lists=array(); 
     while ($file = readdir($handle)) { 
     if ($file == "." || $file == ".." || $file == "default.ini") { } else { 
     	$lang[]= mosHTML::makeOption( $file, $file );
	    }
      $lists['langfiles']= mosHTML::selectList( $lang, 'language','id="language" class="inputbox" size="1" style="width:200px"','value', 'text',$langfile);
     } 
     closedir($handle); 
  
	HTML_swmenupro::upgradeForm( $row,$lists );
	HTML_swmenupro::footer( );
}

function listdir($basedir){
    $handle=opendir($basedir); 
     while ($file = readdir($handle)) { 
     if ($file == "." || $file == "..") { } else { print "<a href=$file>$file</a><br>n"; }
      
     } 
     closedir($handle); 

return $result;  
} 


function copydirr($fromDir,$toDir,$chmod=0775,$verbose=false)
/*
copies everything from directory $fromDir to directory $toDir
and sets up files mode $chmod
*/
{
	//* Check for some errors
	$errors=array();
	$messages=array();
	if (!is_writable($toDir))
	$errors[]='target '.$toDir.' is not writable';
	if (!is_dir($toDir))
	$errors[]='target '.$toDir.' is not a directory';
	if (!is_dir($fromDir))
	$errors[]='source '.$fromDir.' is not a directory';
	if (!empty($errors))
	{
		if ($verbose)
		foreach($errors as $err)
		echo '<strong>Error</strong>: '.$err.'<br />';
		return false;
	}
	//*/
	$exceptions=array('.','..');
	//* Processing
	$handle=opendir($fromDir);
	while (false!==($item=readdir($handle)))
	if (!in_array($item,$exceptions))
	{
		//* cleanup for trailing slashes in directories destinations
		$from=str_replace('//','/',$fromDir.'/'.$item);
		$to=str_replace('//','/',$toDir.'/'.$item);
		//*/
		if (is_file($from))
		{
			if (@copy($from,$to))
			{
				chmod($to,$chmod);
				touch($to,filemtime($from)); // to track last modified time
				$messages[]='File copied from '.$from.' to '.$to;
			}
			else
			$errors[]='cannot copy file from '.$from.' to '.$to;
		}
		if (is_dir($from))
		{
			if (@mkdir($to))
			{
				chmod($to,$chmod);
				$messages[]='Directory created: '.$to;
			}
			else
			$errors[]='cannot create directory '.$to;
			copydirr($from,$to,$chmod,$verbose);
		}
	}
	closedir($handle);
	//*/
	//* Output
	if ($verbose)
	{
		foreach($errors as $err)
		echo '<strong>Error</strong>: '.$err.'<br />';
		foreach($messages as $msg)
		echo $msg.'<br />';
	}
	//*/
	return true;
}

function uploadPackage(  ) {
	global $mosConfig_absolute_path;

	$userfile = mosGetParam( $_FILES, 'userfile', null );

	if (!$userfile) {

		exit();
	}

	$userfile_name = $userfile['name'];

	$msg = '';
	$resultdir = uploadFile( $userfile['tmp_name'], $userfile['name'], $msg );
	$msg=extractArchive($userfile['name']);

	if(file_exists($msg."/swmenupro.xml")){
	$upload_version=get_Version($msg."/swmenupro.xml");
	}else{
		$upload_version=0;
	}
	$current_version=get_Version($mosConfig_absolute_path . '/administrator/components/com_swmenupro/swmenupro.xml');

	
	if($upload_version=="swmenupro_language_file"){
		if(copydirr($msg,$mosConfig_absolute_path . '/administrator/components/com_swmenupro/language',0775,false)){
		$message=_SW_LANGUAGE_SUCCESS;
		}else{
			$message=_SW_LANGUAGE_FAIL;
		}
	
	
	}else if($current_version<$upload_version){
		if(copydirr($msg,$mosConfig_absolute_path . '/administrator/components/com_swmenupro',0775,false)){
		$message=_SW_COMPONENT_SUCCESS;
		}else{
			$message=_SW_COMPONENT_FAIL;
		}
	}else{

		$message=_SW_INVALID_FILE;
	}
    deldir($msg);
	unlink($mosConfig_absolute_path."/media/".$userfile['name']);

	mosRedirect( "index2.php?&option=com_swmenupro&task=upgrade",$message );

}

/**
* @param string The name of the php (temporary) uploaded file
* @param string The name of the file to put in the temp directory
* @param string The message to return
*/
function uploadFile( $filename, $userfile_name, &$msg ) {
	global $mosConfig_absolute_path;
	$baseDir = mosPathName( $mosConfig_absolute_path . '/media' );

	if (file_exists( $baseDir )) {
		if (is_writable( $baseDir )) {
			if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				if (mosChmod( $baseDir . $userfile_name )) {
					return true;
				} else {
					$msg = 'Failed to change the permissions of the uploaded file.';
				}
			} else {
				$msg = 'Failed to move uploaded file to <code>/media</code> directory.';
			}
		} else {
			$msg = 'Upload failed as <code>/media</code> directory is not writable.';
		}
	} else {
		$msg = 'Upload failed as <code>/media</code> directory does not exist.';
	}
	return false;
}

function extractArchive($filename) {
	global $mosConfig_absolute_path;

	$base_Dir 		= mosPathName( $mosConfig_absolute_path . '/media/' );

	$archivename 	= $base_Dir . $filename;
	$tmpdir 		= uniqid( 'install_' );

	$extractdir 	= mosPathName( $base_Dir . $tmpdir );
	$archivename 	= mosPathName( $archivename, false );

	//$this->unpackDir( $extractdir );

	if (preg_match( '/.zip$/i', $archivename )) {
		// Extract functions
		require_once( $mosConfig_absolute_path . '/administrator/includes/pcl/pclzip.lib.php' );
		require_once( $mosConfig_absolute_path . '/administrator/includes/pcl/pclerror.lib.php' );
		//require_once( $mosConfig_absolute_path . '/administrator/includes/pcl/pcltrace.lib.php' );
		//require_once( $mosConfig_absolute_path . '/administrator/includes/pcl/pcltar.lib.php' );
		$zipfile = new PclZip( $archivename );
		//if($this->isWindows()) {
		//		define('OS_WINDOWS',1);
		//	} else {
		//		define('OS_WINDOWS',0);
		//	}

		$ret = $zipfile->extract( PCLZIP_OPT_PATH, $extractdir );
		if($ret == 0) {
			$this->setError( 1, 'Unrecoverable error "'.$zipfile->errorName(true).'"' );
			return false;
		}
	} else {
		require_once( $mosConfig_absolute_path . '/includes/Archive/Tar.php' );
		$archive = new Archive_Tar( $archivename );
		$archive->setErrorHandling( PEAR_ERROR_PRINT );

		if (!$archive->extractModify( $extractdir, '' )) {
			$this->setError( 1, 'Extract Error' );
			return false;
		}
	}


	return $extractdir;
}


function deldir( $dir ) {
	$current_dir = opendir( $dir );
	$old_umask = umask(0);
	while ($entryname = readdir( $current_dir )) {
		if ($entryname != '.' and $entryname != '..') {
			if (is_dir( $dir . $entryname )) {
				deldir( mosPathName( $dir . $entryname ) );
			} else {
				@chmod($dir . $entryname, 0777);
				unlink( $dir . $entryname );
			}
		}
	}
	umask($old_umask);
	closedir( $current_dir );
	return rmdir( $dir );
}

function TableExists($tablename, $db) {
  
   // Get a list of tables contained within the database.
   $result = mysql_list_tables($db);
   $rcount = mysql_num_rows($result);

   // Check each in list for a match.
   for ($i=0;$i<$rcount;$i++) {
       if (mysql_tablename($result, $i)==$tablename) return true;
   }
   return false;
}
?>
