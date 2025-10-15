<?php
/**
* $Id: update.php 22 2007-04-16 13:39:55Z eaxs $
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

defined ('_VALID_MOS') OR die();


class HydraUpdate
{
	var $version = '0.6.7';
	
	
	function install($current_version)
	{
		global $database, $mosConfig_live_site, $mosConfig_absolute_path;

		$queries = array();
		$errors  = array();
		
		// update 0.6.0
		if ($current_version == '0.6.0') {
			
			// get all files
	        $query = "SELECT  file_id, file_location FROM #__hydra_files";
	               $database->setQuery($query);
	               $files = $database->loadObjectList();

	        if ( intval($database->_errorNum)) { $errors[] = $database->_errorMsg; } 
	          
	         
	        // update file location         
	        for($i = 0, $n = count($files); $i < $n; $i++)
	        {
		        $f = $files[$i];
		
		        $new_location = $mosConfig_absolute_path."/".$f->file_location;
		        $id = $f->file_id;
		
		        $query = "UPDATE #__hydra_files SET file_location = '$new_location' WHERE file_id = '$id'";
		               $database->setQuery($query);
		               $database->query();
		       
		        if (intval($database->_errorNum)) { $errors[] = $database->_errorMsg; }       
	         }
	         
	
	         $query = "SELECT upload_path FROM #__hydra_settings";
	                $database->setQuery($query);
	                $upload_path = $database->loadResult();
	           
	                if (intval($database->_errorNum)) { $errors[] = $database->_errorMsg; } 	

	                
	         // set new upload path
	         $upload_path = $mosConfig_absolute_path."/".$upload_path;
	  
	         
	         // update hydra version and upload path
	         $query = "UPDATE #__hydra_settings SET version = '0.6.1', upload_path = '$upload_path'";
	                $database->setQuery($query);
		            $database->query();
		    
	                if (intval($database->_errorNum)) { $errors[] = $database->_errorMsg; }
			
			
			 $current_version = '0.6.1';
			
		}  // if version
		
		
		
		// update 0.6.1
		if ($current_version == '0.6.1') {
			
		
			
		   // add table to save task relationship (parent-child task)
		   $queries[] = "CREATE TABLE `#__hydra_task_map` (`task` INT( 11 ) NOT NULL ,`parent_task` INT( 11 ) NOT NULL ,INDEX ( `parent_task` )) TYPE = MYISAM ;";
			
		
		   // add table to save notifications
		   $queries[] = "CREATE TABLE `#__hydra_task_notify` (`task` INT( 11 ) NOT NULL ,`uid` INT( 11 ) NOT NULL ,INDEX ( `task` )) TYPE = MYISAM ;";
		
		
		   // reset languages
		   $queries[] = "UPDATE #__hydra_profile SET value = 'english' WHERE parameter = 'language'";
		
		
		   // reset theme
		   $queries[] = "UPDATE #__hydra_profile SET value = 'default' WHERE parameter = 'theme'";
		
		
		   // delete old permission
		   $queries[] = "DELETE FROM `#__hydra_registry` WHERE command = 'view_list'";
		
		
		   // add field for the debugger
		   $queries[] = "ALTER TABLE `#__hydra_settings` ADD `debugger` INT( 1 ) NOT NULL ;";
		
		
		   // add field for raw output settings
		   $queries[] = "ALTER TABLE `#__hydra_settings` ADD `raw_output` INT( 1 ) NOT NULL ;"; 
		
		
		   // added new fields to restrict file-access
		   $queries[] = "ALTER TABLE `#__hydra_folders` ADD `folder_access` INT( 1 ) NOT NULL ;"; 
		   $queries[] = "ALTER TABLE `#__hydra_documents` ADD `doc_access` INT( 1 ) NOT NULL ;"; 
		   $queries[] = "ALTER TABLE `#__hydra_files` ADD `file_access` INT( 1 ) NOT NULL ;"; 
		
		
		   // added field for task priority
		   $queries[] = "ALTER TABLE `#__hydra_task` ADD `priority` INT( 1 ) NOT NULL ;";
		
		
		   // added field for custom task status
		   $queries[] = "ALTER TABLE `#__hydra_task` ADD `task_cstatus` VARCHAR( 24 ) NOT NULL AFTER `task_status` ;";
		
		
		   foreach ($queries AS $k => $query)
		   {
			   $database->setQuery($query);
			   $database->query();
			
			   if ($database->_errorNum) { $errors[] = $database->_errorMsg; }
		   }
		
		
		   // update existing tasks
		   $query = "SELECT task_id FROM #__hydra_task";
		          $database->setQuery($query);
		          $rows = $database->loadObjectList();
		       

		   for ($i=0,$n=count($rows); $i<$n; $i++)
		   {
			   $row = $rows[$i];
			
			   $database->setQuery("INSERT INTO #__hydra_task_map VALUES('$row->task_id', '0')");
			   $database->query();
			
			   if ($database->_errorNum) { $errors[] = $database->_errorMsg; }
		   }
		
           $current_version = '0.6.5'; 
		
		
		} // if version
		
		
		
		// update 0.6.5
		if ($current_version == '0.6.5') {
		    
		    // change joomla menu name
		    $query = "UPDATE #__components SET name='Project Fork', admin_menu_alt='Project Fork' WHERE name='hydra project manager'";
		           $database->setQuery($query);
		           $database->query();
		           
		    if ($database->_errorNum) { $errors[] = $database->_errorMsg; }  
               
            // fix that  HL_CMD_VIEW_LIST problem            
		    $query = "DELETE FROM #__hydra_registry WHERE area='calendar' AND command='view_list'";
                   $database->setQuery($query);
		           $database->query();       
		           
		    if ($database->_errorNum) { $errors[] = $database->_errorMsg; }   
            
            $query = "DELETE FROM #__hydra_perms WHERE area='calendar' AND command='view_list'"; 
                   $database->setQuery($query);
		           $database->query();       
		           
		    if ($database->_errorNum) { $errors[] = $database->_errorMsg; }  
            
		    $current_version = '0.6.6';   
		}   // if version
         
        
		if( $current_version == '0.6.6') {
			
			$query = "DELETE FROM #__hydra_registry WHERE command = 'view_list'";
			       $database->setQuery($query);
			       $database->query();
			       
			if ($database->_errorNum) { $errors[] = $database->_errorMsg; }         
			       
			$query = "DELETE FROM #__hydra_perms WHERE command = 'view_list'";
			       $database->setQuery($query);
			       $database->query();  
			       
			if ($database->_errorNum) { $errors[] = $database->_errorMsg; }  

			  
			$current_version = '0.6.7';
		}
         
		if(count($errors)) {
			
			echo "SOMETHING WENT WRONG! <br/><br/>";
			
			foreach ($errors AS $k => $error)
			{
				echo $error."<br/>";
			}
			
			
		}
		else {
			
			$query = "UPDATE #__hydra_settings SET version = '$this->version'";
			       $database->setQuery($query);
			       $database->query();
			       
			mosRedirect($mosConfig_live_site.'/administrator/index2.php?option=com_hydra', 'Welcome to Project Fork '.$this->version.'!');
		}
		
	}
}
?>