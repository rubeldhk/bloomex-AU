<?php
/**
* $Id: protector.class.php 16 2007-04-15 12:18:46Z eaxs $
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


class HydraProtector
{
	var $valid;
	
	// registered areas
	var $valid_areas;
	
	// registered commands
	var $valid_commands;
	
	// required usertype for the commands
	var $usertype_access;
	
	// current area
	var $current_area;
	
	// current command
	var $current_command;
	
	// the users hydra-id
	var $my_id;
	
	// groups to which the user has access
	var $my_groups;
	
	// projects to which the user has access
	var $my_projects;
	
	// permissions 
	var $my_perms;
	
	// usertype
	var $my_usertype;
	
	// user environment
	var $my_userspace;
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 14:24:58 CET 2007 )
	* @name    HydraProtector
	* @version 1.4
	* @param   $hydra_sess
	* @param   $database
	* @return  void
	* @desc    constructor
	**/
	function HydraProtector(&$hydra_sess, &$database)
	{
		global $hydra_cfg, $hydra_debug;
		
		
		$hydra_debug->logNotice('Including File: ['.__FILE__.']');
		
		
		$this->valid = true;
		
		$this->current_area    = mosGetParam($_REQUEST, 'area', 'controlpanel');
		$this->current_command = mosGetParam($_REQUEST, 'cmd', '');

		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Loading Registry...');
		
		
		// load the registry
		$this->loadRegistry($database);
		
		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Registry has been loaded'); 
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Loading Permissions...');
		
		
		// load permissions for the current user
		$this->loadUserEnvironment($database, $hydra_sess->hid);
		
		
        $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Permissions have been loaded...');
        $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Validating...');
        
        
		// validate user
		$this->validateUser($hydra_sess);
		
		
		// validate area
		switch ($this->perm('*'.$this->current_area))
		{
			case false:
				$this->valid = false;
				$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to access the area: "'.htmlentities($this->current_area).'" !');
				$this->current_area = 'controlpanel';	
				break;	
		}

		
		// validate command
		if ($this->current_command != '' AND (!$this->perm($this->current_command))) {
			
		  $this->valid = false;	
		  $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to perform action: "'.htmlentities($this->current_command).'" !');
		  $this->current_command = '';
		  	
		}
	
		 
		switch ($this->valid)
		{
			case false:
				$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - ACCESS DENIED...shutting down!');

				if ( $hydra_debug->enabled) { $hydra_debug->dieLog(); }
				
				switch (stristr($_SERVER['PHP_SELF'], 'administrator'))
				{
					case true:
						mosRedirect('index2.php', _NOT_AUTH);
						exit(); 
						break;
						
					case false:
						$Itemid = intval(mosGetParam($_REQUEST, 'Itemid', 0));
						mosRedirect(sefRelToAbs('index.php?option=com_login&Itemid='.$Itemid));
						exit();
						break;	
				}
				break;
		}

		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - ...basic validation passed without trouble');
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Validating Area...');
		
		
		switch ($this->current_area)
		{
			default:
				$this->protectControlpanel();
				break;
			
			case 'controlpanel':
				$this->protectControlpanel();
				break;
						
			case 'projects':
				$this->protectProjects();
				break;
				
			case 'files':
				$this->protectFiles();
				break;
				
			case 'calendar':
				$this->protectCalendar();
				break;			
		}
		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - ...access granted');
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 14:09:18 CET 2007 )
	* @name    validateUser
	* @version 1.0
	* @param   object $hydra_sess
	* @return  void
	* @desc    validates the current user
	**/
	function validateUser(&$hydra_sess)
	{
		global $hydra_debug;
		
		
		// make sure the user is logged in and has a valid joomla-id
		switch ($hydra_sess->jid < 1)
		{
			case true:
			    $this->valid = false;
			    $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - User is not logged in'); 
			    break; 	
		}

		
		// make sure the user has a hydra-id
		switch ($hydra_sess->hid < 1)
		{
			case true:
				$this->valid = false;
				$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - User has no Hydra-Account'); 
				break;
		}
  
		
		// make sure the user is at least part of one group!    
		if (count($this->my_groups) < 1 AND ($this->my_usertype < 3)) {
			
			$this->valid = false; 
			$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - User is not part of a usergroup!');
			
		}
		
		
		// make sure we have permissions
		if (count($this->my_perms) < 1 AND ($this->my_usertype < 3))  { 
			
			$this->valid = false; 
			$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - User has no permissions!');
			
		}
		
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 14:27:16 CET 2007 )
	* @name    loadRegistry
	* @version 1.3
	* @param   $database
	* @return  void
	* @desc    loads the registry from the database
	**/
	function loadRegistry(&$database)
	{
		global $hydra_debug;
		
		$registry = '';
		$reg      = '';
		$tmp      = array();
		
		$this->valid_areas     = array();
		$this->valid_commands  = array();
		$this->usertype_access = array();
		
		
		$query = "SELECT area, command, user_type FROM #__hydra_registry";
		       $database->setQuery($query);
		       $registry = $database->loadObjectList();

		       
		// is the registry empty?       
		switch (count($registry) < 1)
		{
			case true:
				$hydra_debug->logError(__CLASS__.'::'.__FUNCTION__.' - Table #__hydra_registry does not exist or is empty!');
				break;
		}

		
		
		for ($i = 0, $n = count($registry); $i < $n; $i++)
		{
			$reg = $registry[$i];
			
			if (strlen($reg->area) <> 56 AND(!in_array($reg->area, $tmp))) { 
				
				$this->valid_areas[] = $reg->area;
				$tmp[] = $reg->area; 
				
			}
			
			if (strlen($reg->command) > 1 AND (!strlen($reg->area) <> 56)) {
				
				$this->valid_commands[$reg->area][]   = $reg->command;
				$this->usertype_access[$reg->command] = $reg->user_type;
				
			}

		}
		
		return;       
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 14:44:45 CET 2007 )
	* @name    loadUserEnvironment - previously "loadPerms"
	* @version 1.3
	* @param   $database
	* @param   int $id
	* @return  boolean true or false
	* @desc    
	**/
	function loadUserEnvironment(&$database, &$id)
	{
		global $hydra_debug;
		
		
		$this->my_id        = 0;
		$this->my_groups    = array();
		$this->my_perms     = array();
		$this->my_projects  = array();
		$this->my_userspace = array();
		$tmp_groups1        = array();

		
		// no user-id? then return
		switch ($id < 1)
		{
			case true:
				return false;
				break;
		}
		
		
		// load usergroups
		$query = "SELECT gid FROM #__hydra_group_members WHERE '$id' = uid";
		       $database->setQuery($query);
		       $groups = $database->loadResultArray();
         
        if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); } 
        
        
		// return false if the user is not in a group        
		switch (count($groups) < 1)
		{
			case true:
				return false;
				break;
		}


		$tmp_groups = implode(',', $groups);
		
		
		// get the user-type
		$query = "SELECT user_type FROM #__hydra_users WHERE id = '$id'";
		       $database->setQuery($query);
		       $this->my_usertype = intval($database->loadResult());

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); } 

		 
		// load group permissions       
		$query = "SELECT area, command FROM #__hydra_perms WHERE gid IN($tmp_groups) ORDER BY area ASC";
		       $database->setQuery($query);
		       $perms = $database->loadObjectList();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); } 

		 
		// return false if we have no permissions       
		if (count($perms) < 1 AND ($this->my_usertype < 3)) {
			return false;
		}
		
		
		for ($i = 0; $i < count($perms); $i++)
		{
			$perm = $perms[$i];
			
			$this->my_perms[$perm->area][] = $perm->command;			
		}
			
		foreach ($this->valid_areas AS $key => $area)
        {
        	if (!isset($this->my_perms[$area])) { $this->my_perms[$area] = array(); }
        }
        
        	
		$this->my_id     = $id;
		$this->my_groups = $groups;
		
		
		
		// get the projects		
		$query = "SELECT pid FROM #__hydra_project_groups WHERE gid IN ($tmp_groups)";
		       $database->setQuery($query);
		       $this->my_projects = $database->loadResultArray();
		
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); } 
		
		
		// make sure it's an array to avoid problems later
		switch (@is_array($this->my_projects))
		{
			case false:
				$this->my_projects = array();
				break;
		}
		
		
		// get the userspace
		$tmp_projects = implode(",", $this->my_projects);
		
		$this->my_userspace[] = $this->my_id;
		
		$query = "SELECT uid FROM #__hydra_group_members WHERE gid IN($tmp_groups) AND uid != '$this->my_id' AND uid != '0'";
		       $database->setQuery($query);
		       $result = $database->loadResultArray();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); } 

		 
		// make sure it's an array to avoid problems later
		switch (@is_array($result))
		{
			case false:
				$result = array();
				break;
		}   
 
		$this->my_userspace = @array_merge($this->my_userspace, $result);

		$tmp_userspace = implode(',', $this->my_userspace);

		if (count($this->my_projects)) {	
		   $query = "SELECT m.uid FROM #__hydra_project_groups AS g, #__hydra_group_members AS m" 
		          . "\n WHERE g.pid IN($tmp_projects)"
		          . "\n AND g.gid = m.gid AND m.uid NOT IN($tmp_userspace) AND m.uid != '0'"
		          . "\n GROUP BY m.uid";
		          $database->setQuery($query); 
                  $tmp_userspace = $database->loadResultArray();
        
           if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); } 
		}
               
        // make sure it's an array to avoid problems later
		switch (@is_array($tmp_userspace))
		{
			case false:
				$tmp_userspace = array();
				break;
		}
         
		     
		$this->my_userspace = @array_merge($this->my_userspace, $tmp_userspace);
		       
		return true;		
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Wed Dec 13 22:44:51 CET 2006 )
	* @name    perm
	* @version 1.2
	* @param   string $perm
	* @param   unknown $return
	* @return  unknown
	* @desc    checks user-permissions
	**/
	function perm($perm = '', $return = '')
	{	
      global $hydra_debug;
       
      
		// are we an admin?
		switch ($this->my_usertype)
		{
			case 3:
				
				switch (empty($return))
				{
					case false:
					    $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Checking Permission "'.htmlentities($perm).'"...Admin identified...access granted');
				        return $return;	
				        break;
				    
				            
					case true:
						$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Checking Permission "'.htmlentities($perm).'"...Admin identified...access granted');
				        return true;
				        break;
						    
				}
				
				break;
		}

		
		
		// check an area only
		if (stristr($perm, '*')) {
			
			$perm = str_replace('*', '', $perm);
			
			$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Checking Area Permission "'.htmlentities($perm).'"...');
			
			
			switch (empty($this->my_perms[$perm]))
			{
				
				// user don't has access
				case true:
					$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to perform action: "'.htmlentities($perm).'" !');
					
					switch (empty($return))
				    {
					    case true:
						    // added in 0.6.5 - allow access to controlpanel if all the user has a valid account
					        if ( $perm == 'controlpanel' AND( !$this->current_command ) AND( $this->my_id ) ) {
			                    return true;
			                }
			                return false;
						    break;
						
					    case false:
						    // added in 0.6.5 - allow access to controlpanel if all the user has a valid account
			  	            if ( $perm == 'controlpanel' AND( !$this->current_command ) AND( $this->my_id ) ) {
			                   return $return;
			                }
			                return '';
						    break;	
				    }
				    
					break;
				
					
				// 	user has access	
				case false:
					$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - ...access granted');
					
				    switch (empty($return))
				    {
					    case true:
						    return true;
						    break;
						
					    case false:
						    return $return;
						    break;	
				    }
				    
					break;	
			}
			
			
		}
		
		
		
		// check command in explicit area
		if (stristr($perm, '->')) {
			
			$perm = explode ('->', $perm);
			$area = $perm[0];
			$cmd  = $perm[1];
			
			$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Checking Permission "'.htmlentities($cmd).'"...');
			
			if (in_array($cmd, $this->my_perms[$area]) AND ($this->usertype_access[$cmd] <= $this->my_usertype) AND (isset($this->usertype_access[$cmd]))) {
				
			  $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - ...access granted');	
			  
			  switch (empty($return))
			  {
			  	 case true:
			  	 	return true;
			  	 	break;
			  	 	
			  	 case false:
			  	 	return $return;
			  	 	break;	
			  }
			  
			}
			else {
			  $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to perform action: "'.htmlentities($cmd).'" !');
			  
			  switch (empty($return))
			  {
			  	 case true:
			  		 return false;
			  		 break;
			  		
			  	 case false:
			  		 return '';
			  		 break;	
			  }
			  	
			}
		}
		
		
		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Checking Permission "'.htmlentities($perm).'"...');
		
		
		// check a command only
		if (in_array($perm, $this->my_perms[$this->current_area])  AND ($this->usertype_access[$perm] <= $this->my_usertype) AND (isset($this->usertype_access[$perm]))) {
			
            $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - ...access granted');	
         
			switch (empty($return))
			{
			  	case true:
			  	   return true;
			  	   break;
			  	 	
			  	case false:
			  	   return $return;
			  	   break;	
			}
						
		}
		else {
			
			$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to perform action: "'.htmlentities($perm).'" !');	
			
			switch (empty($return))
			{
			   case true:
			  		return false;
			  		break;
			  		
			  	case false:
			  		return '';
			  		break;	
			 }
						
		}
		
		
		$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Unknown error...access denied');
		
		// return false if something went wrong
		return false;
	}
	
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 14:51:27 CET 2007 )
	* @name    showUserTypeName
	* @version 1.1
	* @param   int $type
	* @return  contant
	* @desc    returns the name of a usertype
	**/
	function showUserTypeName($type)
	{
		$types = array(HL_USER_TYPE_CLIENT, HL_USER_TYPE_MEMBER, HL_USER_TYPE_GROUPLEADER, HL_USER_TYPE_ADMINISTRATOR);
		$types = @$types[$type];
		
		return $types;
	}
	
	
	
	
	/**
	* @author  Tobias Kuhn ( Wed Dec 13 23:10:14 CET 2006 )
	* @name    protectControlpanel
	* @version 1.2
	* @param   void
	* @return  void
	* @desc    
	**/
	function protectControlpanel()
	{
		global $hydra_debug;
		 
		$valid = true;
		
		$id = intval(mosGetParam($_REQUEST, 'id', 0));
		
		// protect usergroup edit-page
		if ($this->current_command == 'new_usergroup' AND ($id) AND($this->my_usertype != 3) AND(!in_array($id, $this->my_groups))) {
			$valid = false;
			$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - User is not allowed to edit this Usergroup...access denied');
		}
		
		// protect usergroup editing
		if ($this->current_command == 'create_usergroup' AND ($id) AND($this->my_usertype != 3) AND(!in_array($id, $this->my_groups))) {
			$valid = false;
			$hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - User is not allowed to edit this Usergroup...access denied');
		}
		
		
		// redirect and exit if we had no permission
		if (!$valid) { 
			
			if ($hydra_debug->enabled) {
				$hydra_debug->dieLog();
			}
			
			// added in 0.6.1
			// backend
			if (stristr($_SERVER['PHP_SELF'], 'administrator')) {
				
				if ($this->perm('*controlpanel')) {
					mosRedirect('index2.php&option=com_hydra', _NOT_AUTH);
					exit();
				}
				else {
					mosRedirect('index2.php', _NOT_AUTH);
					exit();
				}
			    
			   
		   }
		   // frontend
		   else {
			
		   	$Itemid = intval(mosGetParam($_REQUEST, 'Itemid', 0));
		   	
		   	if ($this->perm('*controlpanel')) {
					mosRedirect(sefRelToAbs('index.php?option=com_hydra&Itemid='.$Itemid));
					exit();
				}
				else {
					mosRedirect(sefRelToAbs('index.php?option=com_login&Itemid='.$Itemid));
					exit();
				}

		   }
		
		}
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Wed Dec 13 23:10:04 CET 2006 )
	* @name    protectProjects
	* @version 1.2 
	* @param   void
	* @return  void
	* @desc    
	**/
	function protectProjects()
	{
		global $database, $hydra_debug;
		
		$valid = true;

		$id  = intval(mosGetParam($_REQUEST, 'id', 0));
		$pid = intval(mosGetParam($_REQUEST, 'pid', 0));
		
        switch ($this->current_command)
        {
        	default:
        		
        		// protect project-sheet access
		        if ($id) {
			       if (!in_array($id, $this->my_projects) AND ($this->my_usertype != 3)) {
				       $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to see project details ('.$id.')...access denied');
				       $valid = false;
			       }
		        }
		        
        		break;
        	
        				
        	case 'show_tasks':
        		
        		// make sure we can't create a new task if there's no project
        		if (!count($this->my_projects)) { 
        			$valid = false;
        		}
        		
        		
        		// protect task-sheet access
		        if ($id) {
			       $query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			       $database->setQuery($query);
			       
			       if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				      $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to see Task ('.$id.')...access denied');
				      $valid = false;
			       }
		        }
		        		
        		break;
        	
        			
        	case 'new_task':
        		
        		// make sure we can't create a new task if there's no project
        		if (!count($this->my_projects)) { 
        			$valid = false;
        		}
        		
        		// protect task editing-page
		        if($id) {
			       $query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			       $database->setQuery($query);
			       
			       if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				      $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to edit Task ('.$id.')...access denied');
				      $valid = false;
			       }
		        }
		        
        		break;
        	
        			
        	case 'new_project':
        		
        		// protect project-editing
		        if($id) {
			        if (!in_array($id, $this->my_projects) AND ($this->my_usertype != 3)) {
				       $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to edit project('.$id.')...access denied');
				       $valid = false;
			        }
		        }
        		
        		break;
        	
        			
        	case 'create_task':
        		
        		// protect task editing
		        if ($this->current_command == 'create_task' AND ($id)) {
			       $query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			              $database->setQuery($query);
			       
			       if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				      $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to edit Task ('.$id.')...access denied');
				      $valid = false;
			       }
		        }
        		
        		break;
        		
        		
        	case 'task_notification':
        		
        		$query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			           $database->setQuery($query);
			       
			    if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				   $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to edit notification settings for Task ('.$id.')...access denied');
				   $valid = false;
			    }
			    
        		break;
        		
        		
        	case 'update_progress':
        		
        		// protect task prgress update
		        if ($id) {
		        	
		           $query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			           $database->setQuery($query);
			           	
			       if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				       $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to update task progress ('.$id.')...access denied');
				       $valid = false;
			       }
		        }
        		
        		break;
        		
        		
        	case 'view_comments':
        		
        		$query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			           $database->setQuery($query);
			           	
			    if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				    $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to view comment of task ('.$id.')...access denied');
				    $valid = false;
			    }
        		
        		break;
        	
        			
        	case 'new_comment':
        		
        		$task_id = intval(mosGetParam($_REQUEST, 'task_id', 0));
        		
        		$query = "SELECT project FROM #__hydra_task WHERE task_id = '$task_id'";
			           $database->setQuery($query);
			           	
			    if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				    $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to add/edit comment for task ('.$task_id.')...access denied');
				    $valid = false;
			    }
        		
        		break;
        	
        			
        	case 'del_comment':
        		
        		$task_id = intval(mosGetParam($_POST, 'task_id', 0));
        		
        		$query = "SELECT project FROM #__hydra_task WHERE task_id = '$task_id'";
			           $database->setQuery($query);
			           	
			    if (!in_array($database->loadResult(), $this->my_projects) AND ($this->my_usertype != 3)) {
				    $hydra_debug->logViolation(__CLASS__.'::'.__FUNCTION__.' - Not allowed to delete comment of task ('.$task_id.')...access denied');
				    $valid = false;
			    }
        		
        		break;			
        								
        }
		
		// redirect and exit if we had no access
		if (!$valid) { 
			
			if ($hydra_debug->enabled) {
				$hydra_debug->dieLog();
			}
			
			// added in 0.6.1
			// backend
			if (stristr($_SERVER['PHP_SELF'], 'administrator')) { 
				
				if ($this->perm('*projects')) {
					mosRedirect('index2.php&option=com_hydra&area=projects', _NOT_AUTH); 
					exit();
				}
				else {
					mosRedirect('index2.php&option=com_hydra', _NOT_AUTH); 
					exit();
				}
			    
		   }
		   // frontend
		   else {
			
		   	$Itemid = intval(mosGetParam($_REQUEST, 'Itemid', 0));
		   	
		   	if ($this->perm('*projects')) {
		   		mosRedirect(sefRelToAbs('index.php?option=com_hydra&area=projects&Itemid='.$Itemid));
					exit();
				}
				else {
					mosRedirect(sefRelToAbs('index.php?option=com_hydra&Itemid='.$Itemid));
					exit();
				}

		   } // else...
		   
		} // if...
		
	}
	
	
	/**
	* @author  Tobias Kuhn ( Wed Jan 10 19:23:59 CET 2007 )
	* @name    protectFiles
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    
	**/
	function protectFiles()
	{
		global $database, $hydra_debug;
		
		$valid = true;
		
		$id        = intval(mosGetParam($_REQUEST, 'id', 0));
		$folder    = intval(mosGetParam($_REQUEST, 'folder', 0));
		$data_type = mosGetParam($_REQUEST, 'data_type', '');
		
		
		switch($this->current_command)
		{
			default:
				
				break;
				
				
			case 'new_folder':
			case 'create_folder':
				
				// protect folder edit-page
		        if ($id AND ($this->my_usertype != 3)) {
			
			       $query = "SELECT folder_type, folder_project, uid, creator, folder_access FROM #__hydra_folders "
			              . "\n WHERE folder_id  = '$id'";
			              $database->setQuery($query);
			              $database->loadObject($properties);


			       // check usertype 
			       if ($properties->folder_access > $this->my_usertype) {
				      $valid = false;
			       }
			
			       // protect project
			       if ($properties->folder_type == '1') {
				      if (!in_array($properties->project, $this->my_projects)) {
					     $valid = false;
				      }
			       }
			         
			       // protect private         
			       if ($properties->folder_type == '2') {
				      if ($properties->creator != $this->my_id OR($properties->uid != $this->my_id)) {
					     $valid = false;
				      }
			       }
			
		        }
							
				break;
				
			
			case 'new_document':
				
				if ($id AND ($this->my_usertype != 3)) {
			
			       $query = "SELECT doc_type, project, uid, creator, doc_access FROM #__hydra_documents "
			              . "\n WHERE doc_id  = '$id'";
			              $database->setQuery($query);
			              $database->loadObject($properties);


			       // check usertype 
			       if ($properties->doc_access > $this->my_usertype AND($id)) {
				      $valid = false;
			       } 
			                
			       // protect project
			       if ($properties->doc_type == '1') {
				      if (!in_array($properties->project, $this->my_projects)) {
					     $valid = false;
				      }
			       }
			          
			       if ($properties->doc_type == '2') {
				      if ($properties->creator != $this->my_id OR($properties->uid != $this->my_id)) {
					     $valid = false;
				      }
			       }
			       
		        }
					
				break;
				
				
			case 'read_data':
				
				// protect document read action
		        if ($id AND ($this->my_usertype != 3) AND($data_type == "document")) {

			       $query = "SELECT doc_type, project, uid, creator, doc_access FROM #__hydra_documents "
			              . "\n WHERE doc_id  = '$id'";
			              $database->setQuery($query);
			              $database->loadObject($properties);
			       
			              
			       // check usertype 
			       if ($properties->doc_access > $this->my_usertype) {
				      $valid = false;
			       } 
			                
			       // protect project
			       if ($properties->doc_type == '1') {
				      if (!in_array($properties->project, $this->my_projects)) {
					     $valid = false;
				      }
			       }
			          
			       if ($properties->doc_type == '2') {
				      if ($properties->creator != $this->my_id) {
					     $valid = false;
					     if ($properties->uid == $this->my_id) {
				      	    $valid = true;
				         }
				      }
			       }       
			       
		        }
		        
		        // protect file download
		        if ($id AND ($this->my_usertype != 3) AND($data_type == "data")) {
			
			       $query = "SELECT file_type, project, uid, creator, file_access FROM #__hydra_files"
			              . "\n WHERE file_id  = '$id'";
			              $database->setQuery($query);
			              $database->loadObject($properties);
			       
			              
			       // check usertype 
			       if ($properties->file_access > $this->my_usertype) {
				       $valid = false;
			       }    
			       
			             
			       // protect project
			       if ($properties->file_type == '1') {
				 
				       if (!in_array($properties->project, $this->my_projects)) {
					      $valid = false;
				       }
			       }
			         
			       
			       // protect private file 
			       if ($properties->file_type == '2') {
				
				      if ($properties->creator != $this->my_id) {
					    $valid = false;
					     if ($properties->uid == $this->my_id) {
				      	    $valid = true;
				         }
				     }
				
			       }       
			       
		        }
		
				break;
				

			case 'upload_file':
				
				// protect file edit-page
		        if ($this->current_command == 'upload_file' AND ($id) AND ($this->my_usertype != 3)) {
			
			       $query = "SELECT file_type, project, uid, creator FROM #__hydra_files"
			              . "\n WHERE file_id  = '$id'";
			              $database->setQuery($query);
			              $database->loadObject($properties);

			       
			        // protect project
			        if ($properties->file_type == '1') {
				       if (!in_array($properties->project, $this->my_projects)) {
				       	
					      $valid = false;
				       }
			        }

			        // protect private file 
			        if ($properties->file_type == '2') {
				       if($properties->creator != $this->my_id OR($properties->uid != $this->my_id)) {
					       $valid = false;
				       }
			        }
			        
		        }
		        
				break;
				
				
			case 'create_files':

		        if ($id AND ($this->my_usertype != 3)) {
			       $query = "SELECT file_type, project, uid, creator FROM #__hydra_files"
			              . "\n WHERE file_id  = '$id'";
			              $database->setQuery($query);
			              $database->loadObject($properties);

			       
			       // protect project
			       if ($properties->file_type == '1') {
				
				      if (!in_array($properties->project, $this->my_projects)) {
					     $valid = false;
				      }
			       }

			       // protect private file 
			       if ($properties->file_type == '2') {
				
				      if ($properties->creator != $this->my_id OR($properties->uid != $this->my_id)) {
					     $valid = false;
				      }
			       }
		        }
				
				break;			
			
		}
		
		// if we had no access, redirect and exit
		if (!$valid) { 
			
			if ($hydra_debug->enabled) {
				$hydra_debug->dieLog();
			}
			
			// added in 0.6.1
			// backend
			if (stristr($_SERVER['PHP_SELF'], 'administrator')) {
				
				if ($this->perm('*files')) {
					mosRedirect('index2.php&option=com_hydra&area=files&folder='.$folder, _NOT_AUTH); 
					exit();
				}
				else {
					mosRedirect('index2.php&option=com_hydra', _NOT_AUTH); 
			      exit();
				}
			   
		   }
		   // frontend
		   else {
		   	
		   	$Itemid = intval(mosGetParam($_REQUEST, 'Itemid', 0));
		   	
		   	if ($this->perm('*files')) {
					mosRedirect(sefRelToAbs('index.php&option=com_hydra&area=files&folder='.$folder)); 
			        exit();
				}
				else {
					mosRedirect(sefRelToAbs('index.php&option=com_hydra&Itemid='.$Itemid));
		   	        exit();
				}
		   	
		   }
		
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 08 14:45:56 CEST 2006 )
	* @name    protectCalendar
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    
	**/
	function protectCalendar()
	{
		global $hydra_debug;
		
		
		$valid = true;
		
		if (!$valid) { 
			
			if ($hydra_debug->enabled) {
				$hydra_debug->dieLog();
			}
			
			die(); 
		}
	}
}
?>