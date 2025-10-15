<?php
/**
* $Id: configuration.class.php 16 2007-04-15 12:18:46Z eaxs $
* @package   Project Fork
* @copyright Copyright (C) 2006-2007 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Project Fork is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

defined ( '_VALID_MOS' ) OR DIE( 'Direct access is not allowed' );


class HydraConfiguration
{
	var $hydra_url;
	
	var $hydra_path;
	
	var $site_url;
	
	var $site_path;
	
	var $settings;
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:44:47 CEST 2006 )
	* @name    HydraConfiguration
	* @version 1.0 
	* @param   string $mosConfig_live_site
	* @param   string $mosConfig_absolute_path
	* @return  void
	* @desc    constructor
	**/
	function HydraConfiguration($mosConfig_live_site, $mosConfig_absolute_path)
	{
		global $hydra_debug, $database;
		
		$hydra_debug->logNotice('Including File: ['.__FILE__.']');
		
		$this->hydra_url  = $mosConfig_live_site.'/administrator/components/com_hydra';
		
		$this->hydra_path = $mosConfig_absolute_path.'/administrator/components/com_hydra';
		
		$this->site_url   = $mosConfig_live_site;
		
		$this->site_path  = $mosConfig_absolute_path;
		
		$query = "SELECT * FROM #__hydra_settings LIMIT 1";
		       $database->setQuery($query);
		       $database->loadObject($this->settings); 
	}
}
?>