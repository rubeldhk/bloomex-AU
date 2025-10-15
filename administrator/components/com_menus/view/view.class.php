<?php
/**
* @version $Id: components.class.php 4542 2006-08-15 13:49:12Z predator $
* @package Joomla
* @subpackage Menus
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Menus
*/
class view_menu {
	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function edit( $uid, $menutype, $option ) {
		global $database, $my, $mainframe,$mosConfig_live_site;

		$menu = new mosMenu( $database );
		$menu->load( (int)$uid );

		$row = new mosComponent( $database );
		// load the row from the db table
		$row->load( (int)$menu->componentid );

		// fail if checked out not by 'me'
		if ( $menu->checked_out && $menu->checked_out != $my->id ) {
			mosErrorAlert( "The module ".$menu->title." is currently being edited by another administrator" );	
		}

		if ( $uid ) {
			// do stuff for existing item
			$menu->checkout( $my->id );
		} else {
			// do stuff for new item
			$menu->type 		= 'view';
			$menu->menutype 	= $menutype;
			$menu->browserNav 	= 0;
			$menu->ordering 	= 9999;
			$menu->parent 		= intval( mosGetParam( $_POST, 'parent', 0 ) );
			$menu->published 	= 1;
		}
                
                $lists['target'] = mosAdminMenus::Target( $menu );

                /*
		$query = "SELECT c.id AS value, c.name AS text, c.link"
		. "\n FROM #__components AS c"
		. "\n WHERE c.link != ''"
		. "\n ORDER BY c.name"
		;
		$database->setQuery( $query );
		$components = $database->loadObjectList( );

		// build the html select list for section
		$lists['componentid'] 	= mosAdminMenus::Component( $menu, $uid );
                */
		$lists['componentid'] 	= mosAdminMenus::Component( $menu, $uid );
                
                $types = array();
                $types[] = (object) array('key' => 'blog_section', 'value' => 'Blog section', 'id' => 'Section ID');
                $types[] = (object) array('key' => 'blog_category', 'value' => 'Blog category', 'id' => 'Category ID');
                $types[] = (object) array('key' => 'blog_item', 'value' => 'Blog item', 'id' => 'Item ID');
                $types[] = (object) array('key' => 'vm_category', 'value' => 'VM category', 'id' => 'Category ID');
                $types[] = (object) array('key' => 'component', 'value' => 'component', 'id' => 'Option');
                $types[] = (object) array('key' => 'url', 'value' => 'URL', 'id' => 'URL');

                $lists['componentid'] = mosHTML::selectList($types, 'new_type', 'class="inputbox" size="1"', 'key', 'value', $menu->new_type);
		// componentname
		$lists['componentname'] = mosAdminMenus::ComponentName( $menu, $uid );
		// build the html select list for ordering
		$lists['ordering'] 		= mosAdminMenus::Ordering( $menu, $uid );
		// build the html select list for the group access
		$lists['access'] 		= mosAdminMenus::Access( $menu );
		// build the html select list for paraent item
		$lists['parent'] 		= mosAdminMenus::Parent( $menu );
		// build published button option
		$lists['published'] 	= mosAdminMenus::Published( $menu );
                $lists['show'] 	= mosAdminMenus::Show( $menu );
                $lists['nofollow'] 	= mosAdminMenus::Nofollow( $menu );
		// build the url link output
		$lists['link'] 			= mosAdminMenus::Link( $menu, $uid );

		// get params definitions
		$params = new mosParameters( $menu->params, $mainframe->getPath( 'com_xml', $row->option ), 'component' );
                $components = array();


        $sql_changes = "(SELECT *,  'stage' AS `where` FROM  `jos_menu_history_stage` 
                                WHERE `menu_id`=".$uid.")
                                UNION
                                (SELECT *,  'live' AS `where` FROM `jos_menu_history_live` 
                                WHERE `menu_id`=".$uid.")
                                ORDER BY `id` DESC";
        $database->setQuery($sql_changes);
        $changes_history_obj = $database->loadObjectList();
        $changes_history='';
        foreach ($changes_history_obj as $history)
        {
           $changes_history.='<tr>
                <td>'.$history->name.'</td>
                <td>'.$history->old.'</td>
                <td>'.$history->new.'</td>
                <td>'.$history->username.'</td>
                <td>'.$history->date.'</td>
                <td>'.$history->where.'</td>
            </tr>';
        }

		view_menu_html::edit( $menu, $components, $lists, $params, $option,$changes_history );
	}
}
?>