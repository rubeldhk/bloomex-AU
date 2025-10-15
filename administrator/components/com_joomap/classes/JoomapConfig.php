<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
	/**
	 * The Joomap Config
	 * @author Daniel Grothe
	 * @package Joomap
	 */

/** Wraps all configuration functions for Joomap */
class JoomapConfig {
	var $version 			= '2.0';
	var $classname 			= 'sitemap';
	var $expand_category 	= 1;
	var $expand_section 	= 1;
	var $show_menutitle 	= 1;
	var $columns 			= 1;
	var $exlinks 			= 1;
	var $ext_image 			= 'img_grey.gif';
	var $menus 				= "mainmenu,0,1\nothermenu,1,1";
	var $exclmenus			= '';
	var $includelink		= 1;

	/** Return $menus as an associative array */
	function &getMenus() {
		$lines = explode("\n", $this->menus);

		$menus = array();
		foreach( $lines as $line ) {
			list( $menutype, $ordering, $show ) = explode(',', $line);
			$menu = new stdclass;
			$menu->ordering 	= $ordering;
			$menu->show  		= $show;
			$menus[$menutype] 	= $menu;
		}
		return $menus;
	}

	/** Set $menus from an associoative array of menu objects */
	function setMenus( &$menus ) {
		$lines = array();
		foreach( $menus as $menutype => $menu ) {
			$show = $menu->show ? '1' : '0';
			$lines[] = $menutype.','.$menu->ordering.','.$show;
		}
		$this->menus = implode("\n", $lines);
	}

	/** Create the settings table for Joomap and add initial default values */
	function create() {
		global $database;

		$fields = array();

		$vars = get_class_vars('JoomapConfig');
		foreach($vars as $name => $value) {
			switch( gettype( $value ) ) {
			case 'integer':
				$fields[] = "`$name` INTEGER NULL";
				break;
			case 'string':
				if( $name == 'menus')
					$fields[] = "`$name` TEXT NULL";
				else
					$fields[] = "`$name` VARCHAR(255) NULL";
				break;
			}
		}

		$query = "CREATE TABLE #__joomap (". implode(', ', $fields) .")";
		$database->setQuery( $query );
		if( $database->query() === FALSE ) {
			echo _JOOMAP_ERR_NO_CREATE . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		echo _JOOMAP_MSG_SET_DB_CREATED . "<br />\n";

		// Insert default Settings

		$fields = array();
		foreach($vars as $name => $value) {
			$fields[] = "`$name`='$value'";
		}

		$query = "INSERT INTO #__joomap SET ". implode(', ', $fields);
		$database->setQuery( $query );
		if( $database->query() === FALSE ) {
			echo _JOOMAP_ERR_NO_DEFAULT_SET . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		echo _JOOMAP_MSG_SET_DEF_INSERT . "<br />\n";
		return true;
	}

	/** Create a backup of the settings */
	function backup() {
		global $database;
		$query = "DROP TABLE IF EXISTS #__joomap_backup";						// remove old backup
		$database->setQuery( $query );
		if( $database->query() === FALSE ) {
			echo _JOOMAP_ERR_NO_PREV_BU . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
		}

		$query = "CREATE TABLE #__joomap_backup SELECT * FROM #__joomap";		// backup current settings
		$database->setQuery( $query );
		if( $database->query() === FALSE ) {
			echo _JOOMAP_ERR_NO_BACKUP . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		return true;
	}

	/** Restore backup settings */
	function restore() {
		global $database;
		
		$query = "SELECT * FROM #__joomap_backup";								// restore backup settings
		$database->setQuery( $query );
		if( $database->loadObject( $backup ) === FALSE ) {
			return false;
		}

		if( isset( $this ) && is_object( $this ) ) {
			$config = &$this;
		} else {
			$config = new JoomapConfig;
		}
		
		$vars = get_class_vars('JoomapConfig');									// assign current settings
		foreach( $vars as $var => $value ) {
			if( isset($backup->$var) )
				$config->$var = $backup->$var;
		}

		return $config->save();													// save current settings
	}

	/** Remove the settings table */
	function remove() {
		global $database;
		$query = "DROP TABLE IF EXISTS #__joomap";
		$database->setQuery( $query );
		if( $database->query() === FALSE ) {
			echo _JOOMAP_ERR_NO_DROP_DB . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		echo _JOOMAP_MSG_SET_DB_DROPPED . "<br />\n";
	}

	/** Load settings from the database into this instance */
	function load() {
		global $database;

		$query = "SELECT * FROM #__joomap";
		$database->setQuery( $query );
		if( $database->loadObject( $this ) === FALSE ) {
			return false;														// defaults are still set, though
		}
		return true;
	}

	/** Save current settings to the database */
	function save() {
		global $database;

		$fields = array();

		$vars = get_object_vars( $this );
		foreach($vars as $name => $value) {
			$fields[] = "`{$name}`='{$value}'";
		}

		$query = "UPDATE #__joomap SET ". implode(', ', $fields);
		$database->setQuery( $query );
		if( $database->query() === FALSE ) {
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		return true;
	}
	
	/** Debug output of current settings */
	function dump() {
		$vars = get_object_vars( $this );
		echo '<pre style="text-align:left">';
		foreach( $vars as $name => $value ) {
			echo $name.': '.$value."\n";
		}
		echo '</pre>';
	}
	
};

?>
