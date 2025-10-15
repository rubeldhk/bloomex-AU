<?php
/**
* $Id: installer.class.php 16 2007-04-15 12:18:46Z eaxs $
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


class HydraInstaller
{
	var $setup_file;
	
	
	function HydraInstaller()
	{
		global $mosConfig_absolute_path;
		
		$this->setup_file = $mosConfig_absolute_path.'/administrator/components/com_hydra/setup.php';
		
		
		if (file_exists($this->setup_file)) {
			require_once($this->setup_file);
		}
		
	}
}




class HydraUpdater
{
	var $is_current;
	
	var $c_version;
	
	var $u_version;
	
    var $hydra_is_installed;
    
    var $update_file;
    
    var $update;
    
     
	/**
	* @author  Tobias Kuhn ( Fri Dec 29 12:26:54 CET 2006 )
	* @name    HydraUpdater
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function HydraUpdater()
	{
		global $database, $mosConfig_absolute_path;
		
		$this->hydra_is_installed = true;
		
		
		// get the current version
		$query = "SELECT version FROM #__hydra_settings";
		       $database->setQuery($query);
		       $this->c_version = $database->loadResult();

		       
		// no version? then install       
		if (!$this->c_version) {
			
			$this->hydra_is_installed = false;
			
			$this->_install();
		}
		
		
		// check for update
		if ( $this->hydra_is_installed ) {
			
			$this->checkUpdate();
			
			if ( $this->c_version != $this->u_version ) {
				
				$this->hydra_is_installed = false;
				
				$this->installUpdate();
			}
		}
	}
	
	
	function _install()
	{
		$installer = new HydraInstaller();
	}
	
	
	function checkUpdate()
	{
		global $mosConfig_absolute_path;
		
		$this->update_file = $mosConfig_absolute_path.'/administrator/components/com_hydra/update.php';
		
		if ( file_exists( $this->update_file ) ) {
			require_once( $this->update_file );
			
			if ( class_exists('HydraUpdate') ) {
				
				$this->update = new HydraUpdate();
				
				$this->u_version = $this->update->version;
				
			}
			else {
				$this->u_version = $this->c_version;
			}
		}
		else {
			$this->u_version = $this->c_version;
		}
		
		
	}
	
	
	
	function installUpdate()
	{
		$this->update->install($this->c_version);
	}
	
	
}




class ThemeInstaller
{
	// todo
}



class LanguageInstaller
{
	// todo
}
?>