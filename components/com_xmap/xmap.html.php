<?php
/**
 * $Id: xmap.html.php 140 2008-04-05 18:52:30Z root $
 * $LastChangedDate: 2008-04-05 12:52:30 -0600 (sáb, 05 abr 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * A Sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/** Wraps HTML output */
class XmapHtml extends Xmap {
	var $level = -1;
	var $_openList = '';
	var $_closeList = '';
	var $_closeItem = '';
	var $_childs;
	var $_width;

        function XmapHtml (&$config, &$sitemap) {
                $this->view = 'html';
                Xmap::Xmap($config, $sitemap);
        }

	/** 
	 * Print one node of the sitemap
	 */
	function printNode( &$node ) {
		global $Itemid,$mosConfig_live_site;

		$out = '';
	
		$out .= $this->_closeItem;
		$out .= $this->_openList;
		$this->_openList = "";

		if ( $Itemid == $node->id )
			$out .= '<li class="active">';
		else
			$out .= '<li>';

		$link = Xmap::getItemLink($node);;

		if( !isset($node->browserNav) )
			$node->browserNav = 0;

		$node->name = htmlspecialchars($node->name);
		switch( $node->browserNav ) {
			case 1:		// open url in new window
				$ext_image = '';
				if ( $this->sitemap->exlinks ) {
					$ext_image = '&nbsp;<img src="'. $mosConfig_live_site .'/components/com_xmap/images/'. $this->sitemap->ext_image .'" alt="' . _XMAP_SHOW_AS_EXTERN_ALT . '" title="' . _XMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
				}
				$out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank">'. $node->name . $ext_image .'</a>';
				break;

			case 2:		// open url in javascript popup window
				$ext_image = '';
				if( $this->sitemap->exlinks ) {
					$ext_image = '&nbsp;<img src="'. $mosConfig_live_site .'/components/com_xmap/images/'. $this->sitemap->ext_image .'" alt="' . _XMAP_SHOW_AS_EXTERN_ALT . '" title="' . _XMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
				}
				$out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank" '. "onClick=\"javascript: window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false;\">". $node->name . $ext_image."</a>";
				break;

			case 3:		// no link
				$out .= '<span>'. $node->name .'</span>';
				break;

			default:	// open url in parent window
				$out .= '<a href="'. $link .'" title="'. $node->name .'">'. $node->name .'</a>';
				break;
		}

		$this->_closeItem = "</li>\n";
		$this->_childs[$this->level]++;
		echo $out;
		$this->count++;
	}

	/**
	* Moves sitemap level up or down
	*/
	function changeLevel( $level ) {
		if ( $level > 0 ) {
			# We do not print start ul here to avoid empty list, it's printed at the first child
			$this->level += $level;
			$this->_childs[$this->level]=0;
                        $this->_openList = "\n<ul class=\"level_".$this->level."\">\n";
			$this->_closeItem = '';
		} else {
			if ($this->_childs[$this->level]){
				echo $this->_closeItem."</ul>\n";
			}
			$this->_closeItem ='</li>';
			$this->_openList = '';
			$this->level += $level;
		}
	}

	/** Print component heading, etc. Then call getHtmlList() to print list */
	function startOutput(&$menus,&$config) {
		global $database, $Itemid;
		$sitemap = &$this->sitemap;

		$menu = new mosMenu( $database );
		$menu->load( $Itemid );			// Load params for the Xmap menu-item
		$title = $menu->name;
		
		$exlink[0] = $sitemap->exlinks;		// image to mark popup links
		$exlink[1] = $sitemap->ext_image;

		if( $sitemap->columns > 1 ) {		// calculate column widths
			$total = count($menus);
			$columns = $total < $sitemap->columns ? $total : $sitemap->columns;
			$this->_width	= (100 / $columns) - 1;
		}
		echo '<div class="'. $sitemap->classname .'">';
		echo '<div class="componentheading">'.$title.'</div>';
		echo '<div class="contentpaneopen"'. ($sitemap->columns > 1 ? ' style="float:left;width:100%;"' : '') .'>';


	}

	/** Print component heading, etc. Then call getHtmlList() to print list */
	function endOutput(&$menus) {
		global $database, $Itemid;
		$sitemap = &$this->sitemap;

		echo '<div style="clear:left"></div>';
		//BEGIN: Advertisement
		if( $sitemap->includelink ) {
			echo "<div style=\"text-align:center;\"><a href=\"http://joomla.vargas.co.cr\" style=\"font-size:10px;\">Powered by Xmap!</a></div>";
		}
		//END: Advertisement

		echo "</div>";
		echo "</div>\n";
	}

	function startMenu(&$menu) {
		$sitemap=&$this->sitemap;
		if( $sitemap->columns > 1 )			// use columns
			echo '<div style="float:left;width:'.$this->_width.'%;">';
		if( $sitemap->show_menutitle )			// show menu titles
			echo '<h2 class="menutitle">'.$menu->name.'</h2>';
	}

	function endMenu(&$menu) {
		$sitemap=&$this->sitemap;
		$this->_closeItem='';
		if( $sitemap->show_menutitle || $sitemap->columns > 1 ) {		// each menu gets a separate list
			if( $sitemap->columns > 1 ) {
				echo "</div>\n";
			}

		}
	}
}
