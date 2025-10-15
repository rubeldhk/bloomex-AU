<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
	/**
	 * The Joomap Plugin Manager
	 * @author Daniel Grothe
	 * @package Joomap
	 */

/** Wraps all plugin functions for Joomap */
class JoomapPlugins {
	
	/** list all plugin files found in the plugins directory */
	function listPlugins( ) {
		$list = array();

		$pathname = $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/plugins/';
		if ( is_dir($pathname) ) {
			$dir_handle = opendir($pathname);
			if( $dir_handle ) {
				while (($filename = readdir($dir_handle)) !== false) {
					if( substr( $filename, -11 ) == '.plugin.php' ) {
						$list[] = $filename;
					}
				}
				closedir($dir_handle);
			}
		}
		return $list;
	}
	
	/** include() all plugin files */
	function loadPlugins() {
		$pathname = $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/plugins/';
		$plugins = JoomapPlugins::listPlugins();
		foreach( $plugins as $plugin ) {
			include_once( $pathname.$plugin );
		}
	}
	
	/** Add class to list of registered plugins */
	function addPlugin( &$plugin ) {
		global $joomap_plugins;
		
		if( !is_object($plugin) )
			return;								// check if parameter is valid

		if( !method_exists($plugin, 'isOfType')	|| !method_exists($plugin, 'getTree') )
			return;								// check if object implements required functions
		
		$joomap_plugins[] = $plugin;
	}

	/** Determine which plugin-object handles this content and let it generate a tree */
	function &getTree( &$joomap, &$parent ) {
		global $joomap_plugins;

		$result = null;
		foreach( $joomap_plugins as $plugin) {
			if( $plugin->isOfType($joomap, $parent) ) {							// check if plugin handles this kind of content
				$result = $plugin->getTree($joomap, $parent);					// call plugin's handler function
				break;
			}
		}
		return $result;
	}
};
?>