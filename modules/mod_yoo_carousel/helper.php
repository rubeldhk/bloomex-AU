<?php
/**
* YOOcarousel Joomla! Module
*
* @version   1.0.3
* @author    yootheme.com
* @copyright Copyright (C) 2007 YOOtheme Ltd. & Co. KG. All rights reserved.
* @license	 GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class modYOOcarouselHelper
{
	function renderItem(&$item, &$params, &$access)
	{
		global $mainframe;

		$item->text 	= $item->introtext;
		$item->groups 	= '';
		$item->readmore = (trim($item->fulltext) != '');
		$item->metadesc = '';
		$item->metakey 	= '';
		$item->access 	= '';
		$item->created 	= '';
		$item->modified = '';
		
		include(modYOOcarouselHelper::getLayoutPath('mod_yoo_carousel', '_item'));
	}
	
	function getList(&$params, &$access)
	{
		global $mainframe, $database, $my;

		$now 				= _CURRENT_SERVER_TIME;
		$noauth 			= !$mainframe->getCfg( 'shownoauth' );
		$nullDate 			= $database->getNullDate();

		$catid 				= intval( $params->get( 'catid' ) );
		$items 				= intval( $params->get( 'items', 0 ) );
		$randomize          = $params->get('randomize', 0);
		
		// query to determine article count
		$query = "SELECT a.id, a.introtext, a.fulltext , a.images, a.attribs, a.title, a.state"
		."\n FROM #__content AS a"
		."\n INNER JOIN #__categories AS cc ON cc.id = a.catid"
		."\n INNER JOIN #__sections AS s ON s.id = a.sectionid"
		."\n WHERE a.state = 1"
		. ( $noauth ? "\n AND a.access <= " . (int) $my->gid . " AND cc.access <= " . (int) $my->gid . " AND s.access <= " . (int) $my->gid : '' )
		."\n AND (a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " ) "
		."\n AND (a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )"
		."\n AND a.catid = " . (int) $catid
		."\n AND cc.published = 1"
		."\n AND s.published = 1"
		."\n ORDER BY a.ordering"
		;
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($randomize) shuffle($rows);

		return array_slice($rows, 0, $items);
	}	

	function getLayoutPath($module, $template)
	{
		global $mosConfig_absolute_path;
		
		return $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . $template . '.php';
	}
}