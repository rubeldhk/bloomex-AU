<?php
/**
 * $Id: XmapCache.php 31 2007-09-30 17:14:38Z root $
 * $LastChangedDate: 2007-09-30 11:14:38 -0600 (dom, 30 sep 2007) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class XmapCache {
	/**
	* @return object A function cache object
	*/
	function &getCache( &$sitemap ) {
		global $mosConfig_absolute_path, $mosConfig_cachepath, $mosConfig_cachetime;

		if (class_exists('JFactory')) {
			$cache = &JFactory::getCache('com_xmap_'.$sitemap->id);
			$cache->setCaching($sitemap->usecache);
			$cache->setLifeTime($sitemap->cachelifetime);
		} else {
			$options = array (
				'cacheDir'		=> $mosConfig_cachepath . '/',
				'caching'		=> $sitemap->usecache,
				'defaultGroup'		=> 'com_xmap_'.$sitemap->id,
				'lifeTime'		=> $sitemap->cachelifetime
			);
			if (file_exists($mosConfig_absolute_path . '/includes/joomla.cache.php')) {
				require_once( $mosConfig_absolute_path . '/includes/joomla.cache.php' );
				$cache = new JCache_Lite_Function( $options );
			} else {
				require_once( $mosConfig_absolute_path . '/includes/Cache/Lite.php' );
				require_once( $mosConfig_absolute_path . '/includes/Cache/Lite/Function.php' );
				$cache = new Cache_Lite_Function( $options );
			}
			$cache->_group = $options['defaultGroup'];
		}
		return $cache;
	}
	/**
	* Cleans the cache
	*/
	function cleanCache( &$sitemap ) {
		$cache =&XmapCache::getCache( $sitemap );
		if (class_exists('JFactory')) {
			return $cache->clean();
		} else {
			return $cache->clean( $cache->_group );
		}
	}
}
