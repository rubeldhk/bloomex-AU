<?php
/**
* $Id: system.class.php 26 2007-04-16 17:19:37Z eaxs $
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

class HydraSystem
{
	
	var $backend;
	
	var $_message;
	
	var $raw_output;
	
	
	
	/**
	* @author  Tobias Kuhn ( Mon Dec 25 15:00:51 CET 2006 )
	* @name    HydraSystem
	* @version 1.2 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function HydraSystem()
	{
		global $hydra_debug;
		
		
		$hydra_debug->logNotice('Including File: ['.__FILE__.']');
		
		
		switch (stristr($_SERVER['PHP_SELF'], 'administrator'))
		{
			case true:
				$this->backend = true; 
				break;
				
			case false:
				$this->backend = false;
				break;	
		}

		
		$this->forceRawOutput();
		
		$this->_message = urldecode(mosGetParam($_REQUEST, 'hydra_msg', ''));
		
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 29 14:54:58 CET 2006 )
	* @name    forceRawOutput
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    forces raw component output if necessary
	**/
	function forceRawOutput()
	{
		global $Itemid, $hydra_cfg;
		
		$this->raw_output = intval($hydra_cfg->settings->raw_output);
		
		$is_raw = strpos($_SERVER['PHP_SELF'], 'index2.php');
		
		if (!$this->backend AND ($this->raw_output) AND (!$is_raw)) {
			hydraRedirect('index2.php?option=com_hydra');
		}
		
		
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 15:59:08 CET 2007 )
	* @name    load
	* @version 1.3 
	* @param   string $type
	* @param   string $name
	* @param   string $parameter
	* @return  unknown
	* @desc    returns a file if it exists
	**/
	function load($type, $name, $parameter = '')
	{
		global $hydra_cfg, $hydra_sess, $hydra_debug;
		
		$file = '';
		 
		switch ($type)
		{
			// load html
			case 'html':
			    $file = $hydra_cfg->hydra_path.'/html/'.$name.'.html.php';
			  
			    switch (file_exists($file))
			    {
			  	    case true:
			  	  	  $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Including file: ['.$file.']');
			  	      return $file;
			  	  	  break;
			  	  	
			  	    case false:
			  	  	  $hydra_debug->logError(__CLASS__.'::'.__FUNCTION__.' - File does not exists: ['.$file.']');
			  	  	  return false;
			  	  	  break;	
			    }
			  
			    break;
			  
			
			  
			// load class
			case 'class':
				$file = $hydra_cfg->hydra_path.'/classes/'.$name.'.class.php';
				
				
				switch (file_exists($file))
				{
					case true:
						$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Including file: ['.$file.']');
						return $file;
						break;
						
					case false:
						$hydra_debug->logError(__CLASS__.'::'.__FUNCTION__.' - File does not exists: ['.$file.']');
						return false;
						break;	
				}

			    break; 
			   

			    
			// image
			case 'img':
				$tmp_theme = $hydra_sess->profile('theme');
			    
			    if (!$tmp_theme) { $tmp_theme = 'default'; }
			    
			    $file  = $hydra_cfg->hydra_path.'/themes/'.$tmp_theme.'/images/'.$name;
			    
			    switch (file_exists($file))
			    {
			    	case true:
			    		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Loading image: ['.$file.']');
			    		$file = $hydra_cfg->hydra_url.'/themes/'.$tmp_theme.'/images/'.$name;
			  	        return "\n <img src='$file' border='0' $parameter />";
			    		break;
			    		
			    	case false:
			    		$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Image does not exists: ['.$file.']');
			    		return '';
			    		break;
			    }
			  
                break;
         
             
            // image url      
			case 'img_url':
				
				$tmp_theme = $hydra_sess->profile('theme');
			    
			    if (!$tmp_theme) { $tmp_theme = 'default'; }
			    
			    $file = $hydra_cfg->hydra_path.'/themes/'.$tmp_theme.'/images/'.$name;
			  
			    switch (file_exists($file))
			    {
			    	case true:
			    		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Loading image: ['.$file.']');
			    		$file = $hydra_cfg->hydra_url.'/themes/'.$tmp_theme.'/images/'.$name;
			    		return $file;
			    		break;
			    		
			    	case false:
			    		$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Image does not exists: ['.$file.']');
			    		return '';
			    		break;	
			    }
			  
			    break;
			

			// javascript      
			case 'js':
			    $file = $hydra_cfg->hydra_path.'/js/'.$name.'.js';
			    
			    switch (file_exists($file))
			    {
			    	case true:
			    		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Loading javascript: ['.$file.']');
			    		$file = $hydra_cfg->hydra_url.'/js/'.$name.'.js';
			    		$js   = "\n <script type='text/javascript' language='javascript' src='$file'></script>";
			    		return $js;	 
			    		break;
			    		
			    	case false:
			    		$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Javascript does not exist: ['.$file.']');
			    		return false;
			    		break;	
			    }
			  
			    break;
			
			 
			// theme       
			case 'theme':
			    $file = $hydra_cfg->hydra_path.'/themes/'.$name.'/index.php';
			    
			    switch (file_exists($file))
			    {
			    	case true:
			    		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Including theme: ['.$file.']');
			    		return $file;  
			    		break;
			    		
			    	case false:
			    		$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Theme does not exist: ['.$file.']');
			    		$file = $hydra_cfg->hydra_path.'/themes/default/index.php';
			    		
			    		switch (file_exists($file))
			    		{
			    			case true:
			    				$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Including theme: ['.$file.']');
			    				return $file;
			    				break;
			    				
			    			case false:
			    				$hydra_debug->logError(__CLASS__.'::'.__FUNCTION__.' - Default theme does not exist: ['.$file.']');
			    				
			    				if ($hydra_debug->enabled) {
			     	                $hydra_debug->dieLog();
			     	            }
			     	            die();
			    				break;	
			    		}
			    		
			    		break;	
			    }
			  
			    break;	  
			
			    
			// language file      
			case 'language':
			    $file = $hydra_cfg->hydra_path.'/language/'.$name.'.php';
			    
			    switch (file_exists($file))
			    {
			    	case true:
			    		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Including language: ['.$file.']');
			    		return $file; 
			    		break;
			    		
			    	case false:
			    		$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Language does not exist: ['.$file.']');
			    		$file = $hydra_cfg->hydra_path.'/language/english.php';
			    		
			    		switch (file_exists($file))
			    		{
			    			case true:
			    				$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Including language: ['.$file.']');
			    				return $file;
			    				break;
			    				
			    			case false:
			    				$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Language does not exist: ['.$file.']');
			    				
			    				if ($hydra_debug->enabled) {
			     	                $hydra_debug->dieLog();
			     	            }
			     	            die();
			    				break;	
			    		}
			    		
			    		break;	
			    }
			  
			    break;	  
		}
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 16:10:34 CET 2007 )
	* @name    link
	* @version 1.2 
	* @param   string $link
	* @param   bool $index3
	* @return  string $url
	* @desc    this is for backend/frontend compatibility
	**/
	function link($link, $index3 = false, $amp = true)
	{
		global $hydra_cfg, $Itemid;
		
		if ($amp) {
		   $link   = str_replace("&", "&amp;", $link);
	    }   
		$Itemid = intval($Itemid);
		
		switch ($index3)
		{
			case true:
				$url = "index3.php?option=com_hydra&amp;".$link;
				
				switch ($this->backend)
				{
					case false:
						$url = sefRelToAbs("index2.php?option=com_hydra&amp;".$link."&amp;Itemid=".$Itemid);	
						break;
				}
				
				break;
			
					
			case false:
				$url = "index2.php?option=com_hydra&amp;".$link;
				
				switch ($this->backend)
				{
					case false:
						
						switch ($this->raw_output)
						{
							case true:
								$url = sefRelToAbs("index2.php?option=com_hydra&amp;".$link."&amp;Itemid=".$Itemid);
								break;
								
							case false:
								$url = sefRelToAbs("index.php?option=com_hydra&amp;".$link."&amp;Itemid=".$Itemid);
								break;	
						}
						
						break;
				}
				
				break;	
		}
		
		return $url; 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 29 18:44:33 CET 2006 )
	* @name    loadSettings
	* @version 1.2
	* @param   void
	* @return  array $settings
	* @desc    returns the system-settings
	**/
	function loadSettings()
	{
		global $hydra_cfg;

		return $hydra_cfg->settings;
	}
	
	
	
    /**
    * @author  Tobias Kuhn ( Sat Oct 14 13:11:29 CEST 2006 )
    * @name    getUserDetails
	* @version 1.1 
	* @param   int $id
	* @return  array $user
	* @desc    gets name, username and email from the joomla user-table
	**/
	function getUserDetails($id)
	{
		global $database;
		
		$user = null;
		
		$query = "SELECT j.username, j.name, j.email FROM #__users AS j, #__hydra_users AS h"
		       . "\n WHERE h.id = '$id'"
		       . "\n AND h.jid = j.id" ;
		       $database->setQuery($query);
		       $database->loadObject($user);
		
		return $user;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 03:14:57 CET 2007 )
	* @name    formatUserType
	* @version 1.1 
	* @param   int $type
	* @return  string 
	* @desc    shows the User-Type label instead of the ID
	**/
	function formatUserType($type = '0', $edit = false, $id = false)
	{
		global $protect;
		
		$types = array('0' => HL_USER_TYPE_CLIENT, 
		               '1' => HL_USER_TYPE_MEMBER, 
		               '2' => HL_USER_TYPE_GROUPLEADER, 
		               '3' => HL_USER_TYPE_ADMINISTRATOR
		               );
		
		$user_type = intval($type);               
		$type = $types[$type];

		if ($edit AND $protect->perm('controlpanel->change_usertype')  AND ($protect->my_usertype >= $user_type)) {
			
			$type = HydraMenu::init2($type, $edit).HydraMenu::menu($edit);
			
			switch ($id == $protect->my_id)
			{
				case true:
					if ($protect->my_usertype >= 0) { $type .= HydraMenu::item2(HL_USER_TYPE_CLIENT); }
			        if ($protect->my_usertype >= 1) { $type .= HydraMenu::item2(HL_USER_TYPE_MEMBER); }
			        if ($protect->my_usertype >= 2) { $type .= HydraMenu::item2(HL_USER_TYPE_GROUPLEADER); }   
			        if ($protect->my_usertype >= 3) { $type .= HydraMenu::item2(HL_USER_TYPE_ADMINISTRATOR); }
					break;
					
				case false:
					if ($protect->my_usertype >= 0) { $type .= HydraMenu::item(HL_USER_TYPE_CLIENT, false, '', "changeType($id, 0)"); }
			        if ($protect->my_usertype >= 1) { $type .= HydraMenu::item(HL_USER_TYPE_MEMBER, false, '', "changeType($id, 1)"); }
			        if ($protect->my_usertype >= 2) { $type .= HydraMenu::item(HL_USER_TYPE_GROUPLEADER, false, '', "changeType($id, 2)"); }   
			        if ($protect->my_usertype >= 3) { $type .= HydraMenu::item(HL_USER_TYPE_ADMINISTRATOR, false, '', "changeType($id, 3)"); }
					break;	
			}

			
			$type .= HydraMenu::menu();
			
		}
		
		return $type;               
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 11 14:23:24 CET 2006 )
	* @name    loadRawRegistry
	* @version 1.0 
	* @param   void
	* @return  
	* @desc    loads the registry as it is
	**/
	function loadRawRegistry()
	{
		global $database;
		
		$query = "SELECT * FROM #__hydra_registry ORDER BY area, command ASC";
		       $database->setQuery($query);
		       
		return $database->loadObjectList();       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 11 15:51:34 CET 2006 )
	* @name    updateRegistry
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    updates the registry
	**/
	function updateRegistry()
	{
		global $database;
		
		$registry  = mosGetParam($_POST, 'reg', array());
		
		for($i = 0; $i < count($registry); $i++)
		{
			$id         = $registry[$i]['id'];
			$area       = $registry[$i]['area'];
			$cmd        = $registry[$i]['command'];
			$usertype   = $registry[$i]['user_type'];
			$area_label = $registry[$i]['area_label'];
			$cmd_label  = $registry[$i]['command_label'];
			$inherit    = $registry[$i]['inherit'];
			
			$query = "UPDATE #__hydra_registry SET area = '$area', command = '$cmd', user_type = '$usertype',"
			       . "\n area_label = '$area_label', command_label = '$cmd_label', inherit = '$inherit'"
			       . "\n WHERE id = '$id'";
			       $database->setQuery($query);
			       $database->query(); 
			         
		}
		
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=edit_registry');
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 11 17:14:25 CET 2006 )
	* @name    addRegistry
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    adds a new entry to the registry
	**/
	function addRegistry()
	{
		global $database;
		
		$reg = mosGetParam($_POST, 'new_reg', array());
		
		$query = "INSERT INTO #__hydra_registry VALUES('', '".$reg['area']."', '".$reg['command']."', '".$reg['user_type']."',"
		       . "\n '".$reg['area_label']."', '".$reg['command_label']."', '".$reg['inherit']."')";
		       $database->setQuery($query);
		       $database->query();
		       
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=edit_registry');       
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 11 17:26:21 CET 2006 )
	* @name    delRegistry
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    deletes records from the registry
	**/
	function delRegistry()
	{
		global $database;
		
		$cid = mosGetParam($_POST, 'cid', array());
		
		if (count($cid) < 1) {
			echo "<script type='text/javascript' language='javascript'>alert('".HL_REG_DEL_ENTRIES_WARN."');history.back();</script>";
			return;
		}
		
		$cids = implode(',', $cid);
		
		$query = "DELETE FROM #__hydra_registry WHERE id IN($cids)";
		       $database->setQuery($query);
		       $database->query();
		        
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=edit_registry');              
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Nov 19 14:05:37 CET 2006 )
	* @name    loadLanguageList
	* @version 1.0 
	* @param   void
	* @return  $list
	* @desc    returns a list of all languages
	**/
	function loadLanguageList()
	{
		global $database;
		
		$query = "SELECT id, name, label FROM #__hydra_language ORDER BY label ASC";
		       $database->setQuery($query);
		       $list = $database->loadObjectList();
		       
		return $list;        
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Nov 19 14:13:16 CET 2006 )
	* @name    loadThemeList
	* @version 1.0 
	* @param   void
	* @return  $list
	* @desc    returns a list of all themes
	**/
	function loadThemeList()
	{
		global $database;
		
		$query = "SELECT theme_id, name, label FROM #__hydra_theme ORDER BY label ASC";
		       $database->setQuery($query);
		       $list = $database->loadObjectList();
		       
		return $list; 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Nov 19 14:49:09 CET 2006 )
	* @name    delLanguage
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    deletes a language
	**/
	function delLanguage()
	{
		global $database, $hydra_cfg;
		
		$id = intval(mosGetParam($_POST, 'id', 0));
		
		if (!$id) { 
			hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_settings');  
		}	
		
		$query = "SELECT name FROM #__hydra_language WHERE id = '$id'";
		       $database->setQuery($query);
		       $lang = $database->loadResult();

		if ($lang == 'english') { 
          hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_settings');
		}
			  
		$query = "UPDATE #__hydra_profile SET language = 'english' WHERE language = '$lang'";
		       $database->setQuery($query);
		       $database->query();
		       
		@unlink($hydra_cfg->hydra_path."/language/".$lang.".php");

		$query = "DELETE FROM #__hydra_language WHERE id = '$id'";
		       $database->setQuery($query);
		       $database->query(); 
		
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_settings');     
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Nov 19 14:53:20 CET 2006 )
	* @name    delTheme
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    deletes a theme
	**/
	function delTheme()
	{
		global $hydra_cfg, $database;
		
		$id = intval(mosGetParam($_POST, 'id', 0));
		
		$query = "SELECT name FROM #__hydra_theme WHERE theme_id = '$id'";
		       $database->setQuery($query);
		       $theme = $database->loadResult();
		       
		if ($theme == 'default') {
			 hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_settings');     
		}
		
		$query = "UPDATE #__hydra_profile SET theme = 'default' WHERE theme = '$theme'";
		       $database->setQuery($query);
		       $database->query();
		       
		@unlink($hydra_cfg->hydra_path."/themes/".$theme);

		$query = "DELETE FROM #__hydra_theme WHERE theme_id = '$id'";
		       $database->setQuery($query);
		       $database->query(); 
		       
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_settings');                   
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Mon Dec 25 14:53:33 CET 2006 )
	* @name    systemMessage
	* @version 1.0 
	* @param   void
	* @return  string $msg
	* @desc    returns a message
	**/
	function systemMessage()
	{
		$msg = htmlentities(stripslashes($this->_message));
		
		return $msg;
	}
	
}




/**
* @author  Tobias Kuhn ( Mon Dec 25 15:06:33 CET 2006 )
* @name    hydraRedirect
* @version 1.3 
* @param   string $url
* @param   string $msg
* @return  void
* @desc    used to create proper redirect when switching frontend/backend
**/
function hydraRedirect($url, $msg = '')
{
	global $Itemid, $hydra_cfg;
	
	$Itemid = intval($Itemid);
	
	
	switch (strlen($msg) > 1)
	{
		case true:
			
			switch (class_exists('HydraSystem'))
			{
				case true:
					$url = $url."&hydra_msg=".urlencode($msg);
					break;
					
				case false:
					$url = $url."&mosmsg=".$msg;
					break;	
			}
			
			break;
				
	}
	
	
	switch (stristr($_SERVER['PHP_SELF'], 'administrator'))
	{	
		case false:
			
			if (!$hydra_cfg->settings->raw_output) {
		       $url = str_replace('index2.php', 'index.php', $url);
	        }
	        
	        $url = str_replace('index3.php', 'index2.php', $url);
		    $url = sefRelToAbs($url."&Itemid=".$Itemid);
		
			break;	
	}
	 
	
    // redirect
    mosRedirect($url);
    exit();
}


/**
* @author  Tobias Kuhn ( Fri Dec 29 14:22:48 CET 2006 )
* @name    hydraTime
* @version 1.1
* @param   void
* @return  int $time
* @desc    returns a localized timestamp
**/
function hydraTime($timestamp = false)
{
	global $hydra_sess, $mosConfig_offset;
	
	$server_offset = date( 'O' ) / 100;
	
	$time = $timestamp;
	
	if (!$time) { $time = time(); }
	
	if( $server_offset == ($hydra_sess->profile('time_offset')) ) {
        $offset = $server_offset;
    }
    else{
        $offset = intval(($hydra_sess->profile('time_offset') ) - $server_offset + date('I'));
    }
	
    
	$time   = $time + ($offset*60*60);
	
	return $time;
}


function hydraDate($timestamp = '')
{
	global $hydra_sess;
	
	$my_format = intval($hydra_sess->profile('time_format'));
	
	$format = array('d m Y, H:i', 'm d Y, g:i a', 'd m Y, g:i a', 'd m, H:i', 'd m, g:i a', 'd m Y', 'd m');
	$format = $format[$my_format];
	
	if (!$timestamp) { $timestamp = time(); }
	
	$timestamp = hydraTime($timestamp);
	
	return date($format, $timestamp);
}
?>