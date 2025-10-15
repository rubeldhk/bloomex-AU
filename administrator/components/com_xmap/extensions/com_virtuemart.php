<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_virtuemart.php 83 2008-02-03 23:21:17Z root $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Virtuemart component
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for Phpshop and Virtuemart categories to Xmap */
class xmap_com_virtuemart {

	/** Get the content tree for this kind of content */
	function getTree( &$xmap, &$parent, &$params ) {
		global $mosConfig_absolute_path;

		$tree = array();

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = mosGetParam($link_vars,'category_id',0);
		$prodid = mosGetParam($link_vars,'product_id',0);

		if ( $prodid )
			return $tree;

		$include_products = mosGetParam($params,'include_products',1);
		$include_products = ( $include_products == 1
                                  || ( $include_products == 2 && $xmap->view == 'xml') 
                                  || ( $include_products == 3 && $xmap->view == 'html'));
		$params['include_products'] = $include_products;

		$priority = mosGetParam($params,'cat_priority',$parent->priority);
		$changefreq = mosGetParam($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = mosGetParam($params,'prod_priority',$parent->priority);
		$changefreq = mosGetParam($params,'prod_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['prod_priority'] = $priority;
		$params['prod_changefreq'] = $changefreq;


		require_once ($mosConfig_absolute_path. '/administrator/components/com_virtuemart/virtuemart.cfg.php');
		xmap_com_virtuemart::getCategoryTree($xmap, $parent, $params, $catid);
		return true;
	}

	/** Virtuemart support */
	function &getCategoryTree( &$xmap, &$parent,&$params, $catid=0 ) {
		global $database,$mosConfig_absolute_path;
		$list = array();


		$query  = 
		 "SELECT a.category_id, a.category_name, a.mdate,a.category_flypage, b.category_parent_id AS pid "
		."\n FROM #__vm_category AS a, #__vm_category_xref AS b "
		."\n WHERE a.category_publish='Y' "
		."\n AND b.category_parent_id = $catid "
		."\n AND a.category_id=b.category_child_id "
		."\n ORDER BY a.list_order ASC, a.category_name ASC";

		$database->setQuery( $query );

		$rows = $database->loadObjectList();

		$xmap->changeLevel(1);
		foreach($rows as $row) {
			$node = new stdclass;

			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$row->category_id;
			$node->browserNav = $parent->browserNav;
		    	$node->name = $row->category_name;
			$node->modified = intval($row->mdate);
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = $parent->link.'&amp;page=shop.browse&amp;category_id='.$row->category_id;
			$node->pid = $row->pid;									// parent id
		    	$xmap->printNode($node);
			xmap_com_virtuemart::getCategoryTree( $xmap, $parent, $params, $row->category_id);
	    	}
		$xmap->changeLevel(-1);

		if ( $params['include_products'] ) {
			$query  = 
		 	"SELECT a.product_id, a.product_name,a.mdate, b.category_id,c.manufacturer_id,d.category_flypage "
			."\n FROM #__vm_product AS a, #__vm_product_category_xref AS b, #__vm_product_mf_xref AS c,#__vm_category d"
			."\n WHERE a.product_publish='Y'"
			."\n AND b.category_id=$catid "
			."\n AND a.product_id=b.product_id "
			."\n AND a.product_id=c.product_id "
			."\n AND b.category_id=d.category_id "
			."\n ORDER BY a.product_name";

			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			$xmap->changeLevel(1);
			foreach ( $rows as $row ) {
				$node = new stdclass;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'c'.$row->category_id.'p'.$row->product_id;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['prod_priority'];
				$node->changefreq = $params['prod_changefreq'];
				$node->name = $row->product_name;
				$node->modified = intval($row->mdate);
				$node->link = 'index.php?option=com_virtuemart&amp;page=shop.product_details&amp;flypage='.($row->category_flypage? $row->category_flypage : FLYPAGE).'&amp;category_id='.$row->category_id . '&amp;product_id=' . $row->product_id . '&amp;manufacturer_id='.$row->manufacturer_id;						// parent id
		    		$xmap->printNode($node);
	    		}
			$xmap->changeLevel(-1);
		}

		return $list;
	}

	/************************************************************************************************************
	 * pshop category handling taken from /administrator/components/com_phpshop/classes/ps_product_category.php *
	 * ps_product_category::get_category_tree                                                                   *
	 ************************************************************************************************************/
	/** Get an array with all 1st level Categories in PhpShop */
	function &getPhpShop( &$xmap, &$parent ) {
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
