<?php
/**
 * $Id: content.plugin.php 24 2007-09-29 16:46:17Z root $
 * $LastChangedDate: 2007-09-29 10:46:17 -0600 (sáb, 29 sep 2007) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

// Register with the Plugin Manager
$tmp = new Xmap_content;
XmapPlugins::addPlugin( $tmp );

/** Handles standard Joomla Content */
class Xmap_content {

	function isOfType(&$xmap, &$parent) {
		//var_dump($parent);
		if ( $parent->type === 'component' && strpos($parent->link,'option=com_content') > 0 ) {
			return true;
		}
		switch( $parent->type ) {
		case 'content_blog_category':
		case 'content_category':
		case 'content_section':
		case 'content_blog_section':
		case 'content_typed':
			return true;
		}
		return false;
	}

	/** return a node-tree */
	function &getTree(&$xmap, &$parent) {
		
		$result = null;
		if ($parent->type === 'component') {
			$task = preg_replace("/.*view=([^&]+).*/",'$1',$parent->link);
			$id = preg_replace("/.*[&\?]id=([0-9]+).*/",'$1',$parent->link);
			$type = "content_$task";
		} else {
			$type = $parent->type;
			$id = $parent->componentid;
		}
		switch( $type ) {
		case 'content_blog_category':
			$params = $this->_paramsToArray( $parent->params );
	                if ( $id == 0 )  // Multi section
	                        $id  = mosGetParam($params,'categoryid',$id);
			$result = $this->getContentCategory($xmap, $parent, $id, $params);
			 break;
		case 'content_category':
			if ( $xmap->sitemap->expand_category ) {
				$params = $this->_paramsToArray( $parent->params );
				$result = $this->getContentCategory($xmap, $parent, $id, $params);
			}
			break;
		case 'content_section':
			if( $xmap->sitemap->expand_section ) {
				$params = $this->_paramsToArray( $parent->params );
				$result = $this->getContentSection($xmap, $parent, $id, $params);
			}
			break;
		case 'content_blog_section':
			if( $xmap->sitemap->expand_section ) {
				$params = $this->_paramsToArray( $parent->params );
				$result = $this->getContentBlogSection($xmap, $parent, $id, $params);
			}
			break;
		case 'content_typed':
			global $database;
			$database->setQuery("SELECT modified, created FROM #__content WHERE id=". $id);
			$database->loadObject( $item );
			if( $item->modified == '0000-00-00 00:00:00' )
				$item->modified = $item->created;
			$parent->modified = $this->_toTimestamp( $item->modified );
			break;
		}
		return $result;
	}
	
	/** Get all content items within a content category.
	 * Returns an array of all contained content items. */
	function &getContentCategory(&$xmap, &$parent, $catid, &$params) {
		global $database;
		$orderby = isset($params['orderby']) && !empty($params['orderby']) ? $params['orderby'] : 'rdate';
		$orderby = $this->_orderby_sec( $orderby );

/** 
* TODO: Check if this query can be optimized!!
**/
		$isJ15 = ($parent->type =='component'? 1 : 0); 
		$query =
		  "SELECT a.id, a.title, a.modified, a.created"
                . ( $isJ15 ? ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
                             ',CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug'
                   : '')
		. "\n FROM #__content AS a" . ($isJ15 ? ',#__categories AS c':'')
		. "\n WHERE a.catid in (".$catid.")"
		. ( $isJ15 ? "\n AND a.catid=c.id":'')
		. "\n AND a.state='1'"
		. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. ( $xmap->noauth ? '' : "\n AND a.access<='". $xmap->gid ."'" )	// authentication required ?
		. "\n ORDER BY ". $orderby .""
		;
		$database->setQuery( $query );
		$items = $database->loadObjectList();

		$content = array();

		if (count($items) > 0) {
			foreach($items as $item) {
				$node = new stdclass();
				$node->id = $parent->id;
				$node->browserNav = $parent->browserNav;
				$node->name = $item->title;
				
				if( $item->modified == '0000-00-00 00:00:00' )
					$item->modified = $item->created;
				$node->modified = $this->_toTimestamp( $item->modified );
				if ($isJ15) {	
					$node->link = 'index.php?option=com_content&amp;view=article&amp;catid='.$item->catslug.'&amp;id='.$item->slug;
				} else {
					$node->link = 'index.php?option=com_content&amp;task=view&amp;id='.$item->id;
				}
	
				$content[] = $node;	// add this content item as a node to the list
	    		}
	    	}
	    	return $content;
	}

	/** Get all Categories within a Section.
	 * Also call getCategory() for each Category to include it's items */
	function &getContentSection(&$xmap, &$parent, $secid, &$params ) {
		global $database;

		$orderby = isset($params['orderby']) ? $params['orderby'] : '';
		$orderby = $this->_orderby_sec( $orderby );

		$isJ15 = ($parent->type =='component'? 1 : 0); 
		$query =
		  "SELECT a.id, a.title, a.name, a.params".($isJ15? ",a.alias":"")
                . ( $isJ15 ? ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' : '')
		. "\n FROM #__categories AS a"
		. "\n LEFT JOIN #__content AS b ON b.catid = a.id "
		. "\n AND b.state = '1'"
		. "\n AND ( b.publish_up = '0000-00-00 00:00:00' OR b.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. "\n AND ( b.publish_down = '0000-00-00 00:00:00' OR b.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. ( $xmap->noauth ? '' : "\n AND b.access <= ". $xmap->gid )		// authentication required ?
		. "\n WHERE a.section = '". $secid ."'"
		. "\n AND a.published = '1'"
		. ( $xmap->noauth ? '' : "\n AND a.access <= ". $xmap->gid )		// authentication required ?
		. "\n GROUP BY a.id"
		. ( @$params['empty_cat'] ? '' : "\n HAVING COUNT( b.id ) > 0" )	// hide empty categories ?
		. "\n ORDER BY ". $orderby
		;
		$database->setQuery( $query );
		$items = $database->loadObjectList();

		$layout = '';
		if ($isJ15 && preg_match('/^.*&layout=([a-z]+).*/',$parent->link,$matches)) {
			$layout = '&amp;layout='.$matches[1];
		}

		$content = array();
		foreach($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->name = ($isJ15?$item->title : $item->name);
			$node->browserNav = $parent->browserNav;
			if ($isJ15) {
				$node->link = 'index.php?option=com_content&amp;view=category'.$layout.'&amp;id='.$item->slug;
			} else {
				$node->link = 'index.php?option=com_content&amp;task=category&amp;sectionid='.$secid.'&amp;id='.$item->id;
			}
			if( $xmap->sitemap->expand_category ) {
				$node->tree = $this->getContentCategory($xmap, $parent, $item->id, $params);
			}
				
			$content[] = $node;
	    	}
	    return $content;
	}

	/** Return an array with all Items in one or more Sections */
	function &getContentBlogSection(&$xmap, &$parent, $secid, &$params ) {
		global $database;

		$order_pri = isset($params['orderby_pri']) ? $params['orderby_pri'] : '';
		$order_sec = isset($params['orderby_sec']) && !empty($params['orderby_sec']) ? $params['orderby_sec'] : 'rdate';
		$order_pri	= $this->_orderby_pri( $order_pri );
		$order_sec	= $this->_orderby_sec( $order_sec );
		if ( $secid == 0 )  // Multi section
			$secid 	= mosGetParam($params,'sectionid',$secid);
		$where		= $this->_where( 1, $xmap->access, $xmap->noauth, $xmap->gid, $secid, date('Y-m-d H:i:s',$xmap->now) );
		
		$isJ15 = ($parent->type =='component'? 1 : 0); 
		$query =
		  "SELECT a.id, a.title, a.modified, a.created"
                . ( $isJ15 ? ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' : '')
		. "\n FROM #__content AS a"
		. "\n INNER JOIN #__categories AS cc ON cc.id = a.catid"
		. "\n LEFT JOIN #__users AS u ON u.id = a.created_by"
		. "\n LEFT JOIN #__content_rating AS v ON a.id = v.content_id"
		. "\n LEFT JOIN #__sections AS s ON a.sectionid = s.id"
		. "\n LEFT JOIN #__groups AS g ON a.access = g.id"
		. "\n WHERE ". implode( "\n AND ", $where )
		. "\n AND s.access <= ".$xmap->gid
		. "\n AND cc.access <= ".$xmap->gid
		. "\n AND s.published = 1"
		. "\n AND cc.published = 1"
		. "\n ORDER BY $order_pri $order_sec";

		$database->setQuery( $query );
		$items = $database->loadObjectList();
		
		$content = array();
		foreach($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->browserNav = $parent->browserNav;
			$node->name = $item->title;

			if( $item->modified == '0000-00-00 00:00:00' )
			$item->modified = $item->created;
			$node->modified = $this->_toTimestamp( $item->modified );
			
			if ($isJ15) {
				$node->link = 'index.php?option=com_content&amp;task=view&amp;id='.$item->slug;
			} else {
				$node->link = 'index.php?option=com_content&amp;task=view&amp;id='.$item->id;
			}

			$content[] = $node;
	    }
	    return $content;
	}

	/***************************************************/
	/* copied from /components/com_content/content.php */
	/***************************************************/

	/** convert a menuitem's params field to an array */
	function _paramsToArray( &$params ) {
		$tmp = explode("\n", $params);
		$res = array();
		foreach($tmp AS $a) {
			@list($key, $val) = explode('=', $a, 2);
			$res[$key] = $val;
		}
		return $res;
	}
	/** Translate Joomla datestring to timestamp */
	function _toTimestamp( &$date ) {
		if ( $date && ereg( "([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ) ) {
			return mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return FALSE;
	}

	/** translate primary order parameter to sort field */
	function _orderby_pri( $orderby ) {
		switch ( $orderby ) {
			case 'alpha':
				$orderby = 'cc.title, ';
				break;
	
			case 'ralpha':
				$orderby = 'cc.title DESC, ';
				break;
	
			case 'order':
				$orderby = 'cc.ordering, ';
				break;
	
			default:
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/** translate secondary order parameter to sort field */
	function _orderby_sec( $orderby ) {
		switch ( $orderby ) {
			case 'date':
				$orderby = 'a.created';
				break;
	
			case 'rdate':
				$orderby = 'a.created DESC';
				break;
	
			case 'alpha':
				$orderby = 'a.title';
				break;
	
			case 'ralpha':
				$orderby = 'a.title DESC';
				break;
	
			case 'hits':
				$orderby = 'a.hits';
				break;
	
			case 'rhits':
				$orderby = 'a.hits DESC';
				break;
	
			case 'order':
				$orderby = 'a.ordering';
				break;
	
			case 'author':
				$orderby = 'a.created_by_alias, u.name';
				break;
	
			case 'rauthor':
				$orderby = 'a.created_by_alias DESC, u.name DESC';
				break;
	
			case 'front':
				$orderby = 'f.ordering';
				break;
	
			default:
				$orderby = 'a.ordering';
				break;
		}

		return $orderby;
	}
	/** @param int 0 = Archives, 1 = Section, 2 = Category */
	function _where( $type=1, &$access, &$noauth, $gid, $id, $now=NULL, $year=NULL, $month=NULL ) {
		global $database;
		
		$nullDate = $database->getNullDate();
		$where = array();
	
		// normal
		if ( $type > 0) {
			$where[] = "a.state = '1'";
			if ( !$access->canEdit ) {
				$where[] = "( a.publish_up = '$nullDate' OR a.publish_up <= '$now' )";
				$where[] = "( a.publish_down = '$nullDate' OR a.publish_down >= '$now' )";
			}
			if ( $noauth ) {
				$where[] = "a.access <= $gid";
			}
			if ( $id > 0 ) {
				if ( $type == 1 ) {
					$where[] = "a.sectionid IN ( $id ) ";
				} else if ( $type == 2 ) {
					$where[] = "a.catid IN ( $id ) ";
				}
			}
		}

		// archive
		if ( $type < 0 ) {
			$where[] = "a.state='-1'";
			if ( $year ) {
				$where[] = "YEAR( a.created ) = '$year'";
			}
			if ( $month ) {
				$where[] = "MONTH( a.created ) = '$month'";
			}
			if ( $noauth ) {
				$where[] = "a.access <= $gid";
			}
			if ( $id > 0 ) {
				if ( $type == -1 ) {
					$where[] = "a.sectionid = $id";
				} else if ( $type == -2) {
					$where[] = "a.catid = $id";
				}
			}
		}

		return $where;
	}
}
