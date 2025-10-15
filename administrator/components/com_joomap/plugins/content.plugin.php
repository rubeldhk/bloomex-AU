<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php

/**
* @author Daniel Grothe
* @package Plugins
*/

// Register with the Plugin Manager
$tmp = new Joomap_content;
JoomapPlugins::addPlugin( $tmp );

/** Handles standard Joomla Content */
class Joomap_content {

	function isOfType(&$joomap, &$parent) {
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
	function &getTree(&$joomap, &$parent) {
		
		$result = null;
		
		switch( $parent->type ) {
		case 'content_blog_category':
		case 'content_category':
			if( $joomap->config->expand_category ) {
				$params = $this->_paramsToArray( $parent->params );
				$result = $this->getContentCategory($joomap, $parent, $parent->componentid, $params);
			}
			break;
		case 'content_section':
			if( $joomap->config->expand_section ) {
				$params = $this->_paramsToArray( $parent->params );
				$result = $this->getContentSection($joomap, $parent, $parent->componentid, $params);
			}
			break;
		case 'content_blog_section':
			if( $joomap->config->expand_section ) {
				$params = $this->_paramsToArray( $parent->params );
				$result = $this->getContentBlogSection($joomap, $parent, $parent->componentid, $params);
			}
			break;
		case 'content_typed':
			global $database;
			$database->setQuery("SELECT modified, created FROM #__content WHERE id=". $parent->componentid);
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
	function &getContentCategory(&$joomap, &$parent, $catid, &$params) {
		global $database;

		$orderby = isset($params['orderby']) && !empty($params['orderby']) ? $params['orderby'] : 'rdate';
		$orderby = $this->_orderby_sec( $orderby );

		$query =
		  "SELECT a.id, a.title, a.modified, a.created"
		. "\n FROM #__content AS a"
		. "\n WHERE a.catid='".$catid."'"
		. "\n AND a.state='1'"
		. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '". $joomap->now ."' )"
		. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '". $joomap->now ."' )"
		. ( $joomap->noauth ? '' : "\n AND a.access<='". $joomap->gid ."'" )	// authentication required ?
		. "\n ORDER BY ". $orderby .""
		;
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
			
			$node->link = 'index.php?option=com_content&amp;task=view&amp;id='.$item->id;

			$content[] = $node;													// add this content item as a node to the list
	    }
	    return $content;
	}

	/** Get all Categories within a Section.
	 * Also call getCategory() for each Category to include it's items */
	function &getContentSection(&$joomap, &$parent, $secid, &$params ) {
		global $database;

		$orderby = isset($params['orderby']) ? $params['orderby'] : '';
		$orderby = $this->_orderby_sec( $orderby );

		$query =
		  "SELECT a.id, a.title, a.name, a.params"
		. "\n FROM #__categories AS a"
		. "\n LEFT JOIN #__content AS b ON b.catid = a.id "
		. "\n AND b.state = '1'"
		. "\n AND ( b.publish_up = '0000-00-00 00:00:00' OR b.publish_up <= '". $joomap->now ."' )"
		. "\n AND ( b.publish_down = '0000-00-00 00:00:00' OR b.publish_down >= '". $joomap->now ."' )"
		. ( $joomap->noauth ? '' : "\n AND b.access <= ". $joomap->gid )		// authentication required ?
		. "\n WHERE a.section = '". $secid ."'"
		. "\n AND a.published = '1'"
		. ( $joomap->noauth ? '' : "\n AND a.access <= ". $joomap->gid )		// authentication required ?
		. "\n GROUP BY a.id"
		. ( @$params['empty_cat'] ? '' : "\n HAVING COUNT( b.id ) > 0" )	// hide empty categories ?
		. "\n ORDER BY ". $orderby
		;
		$database->setQuery( $query );
		$items = $database->loadObjectList();

		$content = array();
		foreach($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->name = $item->name;
			$node->browserNav = $parent->browserNav;
			$node->link = 'index.php?option=com_content&amp;task=category&amp;sectionid='.$secid.'&amp;id='.$item->id;
			if( $joomap->config->expand_category )
				$node->tree = $this->getContentCategory($joomap, $parent, $item->id, $params);
				
			$content[] = $node;
	    }
	    return $content;
	}

	/** Return an array with all Items in a Section */
	function &getContentBlogSection(&$joomap, &$parent, $secid, &$params ) {
		global $database;

		$order_pri = isset($params['orderby_pri']) ? $params['orderby_pri'] : '';
		$order_sec = isset($params['orderby_sec']) && !empty($params['orderby_sec']) ? $params['orderby_sec'] : 'rdate';
		$order_pri	= $this->_orderby_pri( $order_pri );
		$order_sec	= $this->_orderby_sec( $order_sec );
		$where		= $this->_where( 1, $joomap->access, $joomap->noauth, $joomap->gid, $secid, $joomap->now );
		
		$query =
		  "SELECT a.id, a.title, a.modified, a.created"
		. "\n FROM #__content AS a"
		. "\n INNER JOIN #__categories AS cc ON cc.id = a.catid"
		. "\n LEFT JOIN #__users AS u ON u.id = a.created_by"
		. "\n LEFT JOIN #__content_rating AS v ON a.id = v.content_id"
		. "\n LEFT JOIN #__sections AS s ON a.sectionid = s.id"
		. "\n LEFT JOIN #__groups AS g ON a.access = g.id"
		. "\n WHERE ". implode( "\n AND ", $where )
		. "\n AND s.access <= ".$joomap->gid
		. "\n AND cc.access <= ".$joomap->gid
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
			
			$node->link = 'index.php?option=com_content&amp;task=view&amp;id='.$item->id;

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
		if ( $date && preg_match( "/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs ) ) {
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
?>
