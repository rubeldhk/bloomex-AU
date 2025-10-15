<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php

/**
* @author Daniel Grothe
* @module Joomap
*/

$tmp = new Joomap_virtuemart;
JoomapPlugins::addPlugin( $tmp );

/** Adds support for Phpshop and Virtuemart categories to Joomap */
class Joomap_virtuemart {
	
	/** Return true if this plugin handles this content */
	function isOfType( &$joomap, &$parent ) {
		if( $parent->type == 'components') {
			switch( $parent->component ) {
				case 'mambo-phpShop':
				case 'VirtueMart':
					return true;
			}
		}
		return false;
	}
	
	/** Get the content tree for this kind of content */
	function &getTree( &$joomap, &$parent ) {
		$tree = null;
		switch($parent->component) {
			case 'mambo-phpShop':
				$tree = $this->getPhpShop($joomap, $parent);
				break;
			case 'VirtueMart':
				$tree = $this->getVirtueMart($joomap, $parent);
				break;
		}
		return $tree;
	}

	/** Virtuemart support */
	function &getVirtueMart( &$joomap, &$parent ) {
		global $database;

		$query  = 
		 "SELECT a.category_id, a.category_name, a.mdate, b.category_parent_id AS pid "
		."\n FROM #__vm_category AS a, #__vm_category_xref AS b "
		."\n WHERE a.category_publish='Y' "
		."\n AND a.category_id=b.category_child_id "
		."\n ORDER BY a.list_order ASC, a.category_name ASC";

		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		$list = array();
		foreach($rows as $row) {
			$node = new stdclass;
			
			$node->id = $parent->id;
			$node->browserNav = $parent->browserNav;
		    $node->name = $row->category_name;
			$node->modified = intval($row->mdate);
			$node->link = $parent->link.'&amp;page=shop.browse&amp;category_id='.$row->category_id;
			$node->pid = $row->pid;												// parent id
			
		    $list[$row->category_id] = $node;
	    }
		
		foreach( $list as $id => $cat ) {										// move children into their parent nodes
			if( $cat->pid > 0  && isset($list[$cat->pid]) ) {
				$list[ $cat->pid ]->tree[] = &$list[$id];
			}
		}
		
		foreach( $list as $id => $cat ) {										// remove children from top tree
			if( $cat->pid > 0 ) {
				unset( $list[$id] );
			}
		}
		
		return $list;
	}
	
	
	/************************************************************************************************************
	 * pshop category handling taken from /administrator/components/com_phpshop/classes/ps_product_category.php *
	 * ps_product_category::get_category_tree                                                                   *
	 ************************************************************************************************************/
	/** Get an array with all 1st level Categories in PhpShop */
	function &getPhpShop( &$joomap, &$parent ) {
		global $database;

		// Show only top level categories that are published
	    $query =
		 "SELECT * FROM #__pshop_category AS a, #__pshop_category_xref AS b"
		."\n WHERE a.category_publish='Y'"
		."\n AND (b.category_parent_id='' OR b.category_parent_id='0')"
		."\n AND a.category_id=b.category_child_id"
		."\n ORDER BY a.list_order ASC, a.category_name ASC";

		$database->setQuery( $query );
		$items = $database->loadObjectList();

		$cats = array();
		foreach($items as $item) {
			$node = new stdclass;
			$node->id = $parent->id;
			$node->browserNav = $parent->browserNav;
		    $node->name = $item->category_name;
			$node->modified = intval($item->mdate);
			$node->link = $parent->link.'&amp;page=shop.browse&amp;category_id='.$item->category_id;

		    $cats[] = $node;
	    }
		return $cats;
	}
}

?>