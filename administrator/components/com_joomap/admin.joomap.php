<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
	/**
	 * Joomap by Daniel Grothe
	 * a sitemap component for Joomla! CMS (http://www.joomla.org)
	 * Author Website: http://www.ko-ca.com
	 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 * Additional work by mic (http://www.mgfi.info)
	 * @version $Id: admin.joomap.php,v 0.1 2006/03/18 15:12:12 mic Exp $
	 */

// DEBUG: dump POST input
// echo '<pre style="text-align:left">'.print_r($_POST,true).'</pre>';

// check access permissions (only superadmins & admins)
if ( !( $acl->acl_check('administration', 'config', 'users', $my->usertype) ) 
	||  $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_joomap') ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

// load language file
if( file_exists( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/language/' . $GLOBALS['mosConfig_lang'] . '.php') ) {
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/language/' . $GLOBALS['mosConfig_lang'] . '.php' );
} else {
	echo 'Language file [ '. $GLOBALS['mosConfig_lang'] .' ] not found, using default language: english<br />';
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/language/english.php' );
}

// load settings from database
require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/classes/JoomapConfig.php' );
$config = new JoomapConfig;
if( !$config->load() ) {
	$text = _JOOMAP_ERR_NO_SETTINGS."<br />\n";
	$link = 'index2.php?option=com_joomap&task=create';
	echo sprintf( $text, $link );
}

$cid 		= mosGetParam( $_POST, 'cid', array(0) );
$task 		= mosGetParam( $_REQUEST, 'task', '' );

$admin = new JoomapAdmin;
$admin->show( $config, $task, $cid );

class JoomapAdmin {
	
	var $config = null;
	
	/** Parses input parameters and calls appropriate function */
	function show( &$config, &$task, &$cid ) {
		$this->config = &$config;
		
		switch ($task) {
			
			case 'save':
				$this->saveOptions( );
				break;
			
			case 'cancel':
				mosRedirect( 'index2.php' );
				break;
			
			case 'publish':
				$this->toggleMenu( $cid[0], true );
				break;
			
			case 'unpublish':
				$this->toggleMenu( $cid[0], false );
				break;
			
			case 'orderup':
				$this->orderMenu( $cid[0], -1 );
				break;
			
			case 'orderdown':
				$this->orderMenu( $cid[0], 1 );
				break;
			
			case 'create':
				$config->create();
				$this->showSettingsDialog();
				break;
			
			case 'restore':
				if( $config->restore() ){
					echo _JOOMAP_MSG_SET_RESTORED . "<br />\n";
				}
				$this->showSettingsDialog();
				break;
			
			case 'backup':
				if( $config->backup() ){
					echo _JOOMAP_MSG_SET_BACKEDUP . "<br />\n";
				}
				$this->showSettingsDialog();
				break;
			
			default:
				$this->showSettingsDialog();
				break;
			
		}
	}
	
	/** Show settings dialog
	  * @param integer  configuration save success
	  */
	function showSettingsDialog( $success = 0 ) {
		global $mainframe, $mosConfig_list_limit;
		global $database;
		
		$config = &$this->config;
	
		$limit 		= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
		$limitstart = $mainframe->getUserStateFromRequest( "viewlimitstart", 'limitstart', 0 );
	
		$menus = $this->getMenus();
		$this->sortMenus( $menus );
	
		$total = count($menus);
		require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
		// images for 'external link' tagging
		$javascript = 'onchange="changeDisplayImage();"';
	    $directory = '/components/com_joomap/images';
	    $lists['imageurl'] = mosAdminMenus::Images( 'imageurl', $config->ext_image, $javascript, $directory );
	
	    // success messages
	    switch( $success ) {
	    	case 1:
	    		$lists['msg_success'] = _JOOMAP_MSG_SET_BACKEDUP;
	    		break;
	    	case 2:
	    		$lists['msg_success'] = _JOOMAP_ERR_CONF_SAVE;
	    		break;
	    	default:
	    		$lists['msg_success'] =  _JOOMAP_CFG_COM_TITLE;
	    		break;
	    }
	    
	    // column count selection
	    $columns = array(
			mosHTML::makeOption( 1, 1 ),
			mosHTML::makeOption( 2, 2 ),
			mosHTML::makeOption( 3, 3 ),
			mosHTML::makeOption( 4, 4 )
		);
		$lists['columns'] = mosHTML::selectList( $columns, 'columns', 'id="columns" class="inputbox" size="1"', 'value', 'text',  $config->columns );
	
		// get list of menu entries in all menus
		$query = "SELECT id AS value, name AS text, CONCAT( id, ' - ', name ) AS menu"
		. "\n FROM #__menu"
		. "\n WHERE published != -2"
		. "\n ORDER BY menutype, parent, ordering"
		;
		$database->setQuery( $query );
		$exclmenus = $database->loadObjectList();
		$lists['exclmenus'] = mosHTML::selectList( $exclmenus, 'excl_menus', 'class="inputbox" size="1"', 'value', 'menu', NULL );
	
		require_once( $mainframe->getPath( 'admin_html' ) );
		JoomapAdminHtml::show( $config, $menus, $pageNav, $lists );
	}

	/** Save settings handed via POST */
	function saveOptions( ) {
		$config = &$this->config;
	
		$config->classname 			= mosGetParam( $_POST, 'classname', $config->classname );
		$config->expand_category 	= intval( mosGetParam( $_POST, 'expand_category', 0 ));
		$config->expand_section 	= intval( mosGetParam( $_POST, 'expand_section', 0 ));
		$config->show_menutitle 	= intval( mosGetParam( $_POST, 'show_menutitle', 0 ));
		$config->columns 			= intval( mosGetParam( $_POST, 'columns', $config->columns ));
		$config->exlinks 			= intval( mosGetParam( $_POST, 'exlinks', 0 ));
		$config->includelink		= intval( mosGetParam( $_POST, 'includelink', 0 ));
		$config->ext_image 			= mosGetParam( $_POST, 'imageurl' );
		$config->exclmenus			= mosGetParam( $_POST, 'exclmenus', $config->exclmenus );
		
		$config->exclmenus 			= str_replace( ' ', '', $config->exclmenus ); 	// eliminate blanks
	
		$menus 						= $this->getMenus();
		$menutypes					= mosAdminMenus::menutypes();
	
		$order						= mosGetParam( $_POST, 'order', '0' );			// key = menu id, value = menu ordering
		$csscontent	 				= mosGetParam( $_POST, 'csscontent', '', _MOS_ALLOWHTML );	// CSS
	
		foreach($order as $key => $value) {
			$menutype = $menutypes[$key];
			$menus[ $menutype ]->ordering = $value;
		}
		$this->sortMenus( $menus );
	
		$config->setMenus( $menus );
	
		$success = $config->save() ? 1 : 2;
		
		// save css
		$file 			= $GLOBALS['mosConfig_absolute_path'] . '/components/com_joomap/css/joomap.css';
		$enable_write	= mosGetParam( $_POST, 'enable_write', 0 );
		$oldperms		= fileperms($file);
	
		if( $enable_write ){
			@chmod( $file, $oldperms | 0222 );
		}
	
		clearstatcache();
		
		if( $fp = @fopen( $file, 'w' )) {
			fputs( $fp, stripslashes( $csscontent ) );
			fclose( $fp );
			if( $enable_write ) {
				@chmod( $file, $oldperms );
			}else{
				if( mosGetParam( $_POST, 'disable_write', 0 )){
					@chmod($file, $oldperms & 0777555);
				}
			}
		} else {
			if( $enable_write ){
				@chmod( $file, $oldperms );
			}
		}
		// end CSS
	
		$this->showSettingsDialog( $success );
	}

	/** Move the display order of a record */
	function orderMenu( $uid, $inc ) {
		$config = &$this->config;
	
		$menus		= $this->getMenus();
		$menutypes  = mosAdminMenus::menutypes();
		$menutype	= $menutypes[$uid];
	
		$menus[$menutype]->ordering += $inc;									// move position up/down
	
		foreach( $menus as $type => $menu ) {									// swap position of previous entry at that position
			if( $type != $menutype
				&& $menu->ordering == $menus[$menutype]->ordering )
				$menus[$type]->ordering -= $inc;
		}
	
		$this->sortMenus( $menus );
	
		$config->setMenus( $menus );
	
		if( !$config->save() ) {
			echo _JOOMAP_ERR_CONF_SAVE;
			return;
		}
	
		$this->showSettingsDialog();
	}

	/** Toggle a menu's show attribute */
	function toggleMenu( $uid, $show ) {
		$config = &$this->config;
	
		$menus		= $this->getMenus();
		$menutypes  = mosAdminMenus::menutypes();
		$menutype	= $menutypes[$uid];
	
		$menus[$menutype]->show = !$menus[$menutype]->show;
	
		$config->setMenus( $menus );
	
		if( !$config->save() ) {
			echo _JOOMAP_ERR_CONF_SAVE;
			return;
		}
	
		$this->showSettingsDialog();
	}

//------------------------------ MISC FUNCTIONS ------------------------------//

	/** uasort function that compares element ordering */
	function sort_ordering( &$a, &$b) {
		if( $a->ordering == $b->ordering) {
			return 0;
		}
		return $a->ordering < $b->ordering ? -1 : 1;
	}

	/** make menu ordering continuous*/
	function sortMenus( &$menus ) {
		uasort( $menus, array('JoomapAdmin','sort_ordering') );
		$i = 0;
		foreach( $menus as $key => $menu)
			$menus[$key]->ordering = $i++;
	}

	/** get the complete list of menus in joomla */
	function &getMenus() {
		$config = &$this->config;
	
		$menus = $config->getMenus();
		$menutypes  = mosAdminMenus::menutypes();
	
		$allmenus = array();
		foreach( $menutypes as $index => $menutype ) {
			if( isset($menus[$menutype]) ) {
				$allmenus[$menutype] = $menus[$menutype];
			} else {
				$allmenus[$menutype] = new stdclass;
				$allmenus[$menutype]->ordering = $index;
				$allmenus[$menutype]->show = false;
			}
			$allmenus[$menutype]->id = $index;
			$allmenus[$menutype]->type = $menutype;
		}
	
		return $allmenus;
	}
};
?>
