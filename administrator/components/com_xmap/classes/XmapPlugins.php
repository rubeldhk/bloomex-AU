<?php 
/**
 * $Id: XmapPlugins.php 137 2008-04-05 02:30:21Z root $
 * $LastChangedDate: 2008-04-04 20:30:21 -0600 (vie, 04 abr 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path. '/administrator/components/com_xmap/classes/XmapPlugin.php');

/** Wraps all extension functions for Xmap */
class XmapPlugins {

	/** list all extension files found in the extensions directory */
	function &loadAvailablePlugins( ) {
		global $database,$mosConfig_absolute_path;
		$list = array();

		$query="select * from `#__xmap_ext` where `published`=1 and extension not like '%.bak'";
		$database->setQuery($query);
		$rows = $database->loadAssocList();
		foreach ($rows as $row) {
			$extension = new XmapPlugin($database);
			$extension->bind($row);
			require_once($mosConfig_absolute_path . '/administrator/components/com_xmap/extensions/'. $extension->extension.'.php');
			$list[$extension->extension] = $extension;
		}
		return $list;
	}

	/** Determine which extension-object handles this content and let it generate a tree */
	function &printTree( &$xmap, &$parent, &$cache, &$extensions ) {
		$result = null;

		$matches=array();
		if ( preg_match('#^/?index.php.*option=(com_[^&]+)#',$parent->link,$matches) ) {
			$option = $matches[1];
			if ( !empty($extensions[$option]) ) {
				$parent->uid = "plug".$extensions[$option]->id;
				$className = 'xmap_'.$option;
				$result = call_user_func_array(array($className, 'getTree'),array(&$xmap,&$parent,$extensions[$option]->getParams()));
			}
		}
		return $result;
	}
}
