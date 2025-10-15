<?php
/**
* $Id: controlpanel.class.php 16 2007-04-15 12:18:46Z eaxs $
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

class Controlpanel
{
	var $usergroups;
	var $group_perms;
	var $group_members;
	var $hydra_users;
	var $joomla_users;
	
	
	/**
	* @author  Tobias Kuhn ( Fri Sep 29 22:38:56 CEST 2006 )
	* @name    getUsergroups
	* @version 1.1 
	* @param   int $limit
	* @param   int $limitstart
	* @return  array
	* @desc    loads the usergroups from the database
	**/
	function getUsergroups($limit = 0, $limitstart = 0)
	{
		global $database, $protect;
		
		$where = "\n";
		$my_groups = '';
		
		if ($protect->my_usertype < 3) {
			$my_groups = implode(',', $protect->my_groups);
			
			$where = "\n WHERE group_id IN ($my_groups)";
		}
		
		$query = "SELECT COUNT(group_id) FROM #__hydra_groups $where";
		       $database->setQuery($query);
		       $total = $database->loadResult();

		$query_limit =  "\n LIMIT $limitstart, $limit";

		if ($limit < 1 AND ($limitstart < 1)) { $query_limit = "\n"; }
		        
		$query = "SELECT group_id, group_name, group_description FROM #__hydra_groups $where"
		       . $query_limit;
		       $database->setQuery($query);
		       $this->usergroups = $database->loadAssocList();
		       
		return array('total' => $total, 'groups' => $this->usergroups);       
	}	
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:46:54 CEST 2006 )
	* @name    getGroupPerms
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    loads the permissions from the registry
	**/
	function getGroupPerms()
	{
		global $database;
		
		// get the areas first
		$query = "SELECT area, area_label FROM #__hydra_registry"
		       . "\n WHERE command = ''"
		       . "\n ORDER BY area ASC";
		       $database->setQuery($query);
		       $group_perms = $database->loadAssocList();

		// then the commands
		foreach ($group_perms AS $key => $area)
		{
			$area_name = $area['area'];
			$query = "SELECT command, user_type, command_label FROM #__hydra_registry"
			       . "\n WHERE area = '$area_name'"
			       . "\n AND command != ''"
			       . "\n AND inherit = ''"
			       . "\n ORDER BY area, id ASC";
			       $database->setQuery($query);
		          $group_perms[$area_name][] = $database->loadAssocList();
		}
		
		$this->group_perms = $group_perms;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:47:32 CEST 2006 )
	* @name    loadGroup
	* @version 1.0 
	* @param   int $id
	* @return  array $group
	* @desc    loads all group and group-perms from the database
	**/
	function loadGroup($id)
	{
		 global $database;
		 
		 $query = "SELECT group_name, group_description FROM #__hydra_groups"
		        . "\n WHERE group_id = '$id'";
		        $database->setQuery($query);
		        $group = $database->loadAssocList();
		        
		 $query = "SELECT area, command FROM #__hydra_perms"
		        . "\n WHERE gid = '$id'"
		        . "\n ORDER BY area ASC";
		        $database->setQuery($query);
		        $group_perms = $database->loadAssocList();

		 $query = "SELECT uid FROM #__hydra_group_members WHERE gid = '$id'";
		        $database->setQuery($query);
		        $users = $database->loadAssocList();
		                 
		 foreach ($group_perms AS $key => $val)
		 { 
          $area_name = $val['area'];
          $group['perms'][$area_name][] = $val['command'];
		 }
		 
		 foreach ($users AS $key => $val)
		 {
		 	$group['users'][] = $val['uid'];
		 }
		 	   
		 return $group;       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 08 15:00:06 CEST 2006 )
	* @name    createUsergroup
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    creates/updates a usergroup
	**/
	function createUsergroup()
	{
		global $database, $protect;
		
		$name    = mosGetParam($_POST, 'group_name');
		$desc    = addslashes($_POST['group_desc']);
		$perms   = mosGetParam($_POST, 'perms');
		$id      = intval(mosGetParam($_POST, 'id'));
		 
		$members = mosGetParam($_POST, 'group_members');
      
		$query = "INSERT INTO #__hydra_groups VALUES ('', '$name', '$desc')";

		// we have an id so update instead of creating a new group 
		if ($id > 0) {
			$query = "DELETE FROM #__hydra_perms WHERE gid = '$id'";
			       $database->setQuery($query);
			       $database->query();
			       
			$query = "DELETE FROM #__hydra_group_members WHERE gid = '$id'";       
			       $database->setQuery($query);
			       $database->query();
			        
			$query = "UPDATE #__hydra_groups SET group_name = '$name',"
			       . "\n group_description = '$desc'"
			       . "\n WHERE group_id = '$id'";
		}
		$database->setQuery($query);
		$database->query();
      
		if ($id > 0) { 
			$group_id = $id; 
		}
		else {
			$group_id = mysql_insert_id();
		}
		
		// add perms
		foreach ($perms AS $area => $command)
		{
			foreach ($command AS $k => $v)
			{
				
				if ($protect->my_usertype < 3) {
					if ($protect->usertype_access[$v] > $protect->my_usertype) {
						continue;
					}
				}
				
				$query = "INSERT INTO #__hydra_perms VALUES ('$group_id', '$area', '".$v."')";
			       $database->setQuery($query);
			       $database->query($query);
			       
			   $query = "SELECT command FROM #__hydra_registry"
			          . "\n WHERE command != ''"
			          . "\n AND inherit = '$v'"
			          . "\n AND area = '$area'";
			          $database->setQuery($query);
			          $inherited_perms = $database->loadAssocList();
			       
			   foreach ($inherited_perms AS $k2 => $v2)
			   {
				   $query = "INSERT INTO #__hydra_perms VALUES ('$group_id', '$area', '".$v2['command']."')";
			             $database->setQuery($query);
			             $database->query($query);
			   }
			}
		}
		
		// add members
		foreach ($members AS $k => $v)
		{
			$query = "INSERT INTO #__hydra_group_members VALUES ('$group_id', '$v')";
			       $database->setQuery($query);
			       $database->query($query);
		}
		
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_usergroups');
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 04 13:35:31 CET 2006 )
	* @name    deleteUsergroup
	* @version 1.1 
	* @param   void 
	* @return  void
	* @desc    deletes a usergroup
	**/
	function deleteUsergroup()
	{
		global $database, $protect;

		$cid  = mosGetParam($_REQUEST, 'cid');
		$cids = implode(',', $cid);
		
		if (count($cids) < 1) { 
			echo "<script type='text/javascript' language='javascript'>alert('".HL_GROUP_DELETE_WARN."');history.back();</script>";
			return;
		}
		
		
		foreach ($cid AS $k => $v)
		{
		   // ensure we cannot delete groups we don't have access to
		   if ($protect->my_usertype != 3) {
			    if (!in_array($v, $protect->my_groups)) { die(); }
		   }
		   
		   // make sure we cannot delete ourselves from the last group we are in
		   if (count($protect->my_groups) == 1 AND (in_array($v, $protect->my_groups))) {
		   	hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_usergroups');    
		   }
		}
		  
		
		// delete groups
		$query = "DELETE FROM #__hydra_groups WHERE group_id IN ($cids)";
		       $database->setQuery($query); 
		       $database->query(); 

		// delete user-references        
		$query = "DELETE FROM #__hydra_group_members WHERE gid IN ($cids)";
		       $database->setQuery($query); 
		       $database->query();       

		// delete group-perms         
		$query = "DELETE FROM #__hydra_perms WHERE gid IN ($cids)";
		       $database->setQuery($query);
		       $database->query();
		       
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_usergroups');               
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 08 12:42:49 CEST 2006 )
	* @name    deleteUsers
	* @version 1.2 
	* @param   void
	* @return  void
	* @desc    deletes a user-record
	**/
	function deleteUsers()
	{
		global $database, $protect;

		$cid = mosGetParam($_REQUEST, 'cid');
		$cids = implode(',', $cid);
		
		if (count($cids) < 1) { 
			echo "<script type='text/javascript' language='javascript'>alert('".HL_DELETE_USERS_WARN."');history.back();</script>";
			return;
		}
		
		$my_groups = implode(',', $protect->my_groups);
		
		if ($protect->my_usertype != 3) {
			
			// if we are not an admin, we can only delete members which are in our group(s)
			$query = "SELECT m.uid u.user_type FROM #__hydra_group_members AS m, #__hydra_users AS u"
			       . "\n WHERE m.gid IN ($my_groups)";
			       $database->setQuery($query);
			       $uids = $database->loadObjectList();
			       
			for($i = 0; $i < count($uids); $i++)
			{
				$u = $uids[$i];
				
				if (!in_array($u->uid, $protect->my_groups)) { die(); }
				if ($u->user_type > $protect->my_usertype) { die(); }
			}
		}
		
		// delete user	
		$query = "DELETE FROM #__hydra_users WHERE id IN ($cids)";
		       $database->setQuery($query); 
		       $database->query(); 

		// delete profile      
		$query = "DELETE FROM #__hydra_profile WHERE user_id IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		// delete from groups     
		$query = "DELETE FROM #__hydra_group_members WHERE uid IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		// change the owner of all files and tasks to avoid conflicts        
		$new_owner = $protect->my_id;

		// if we are not an admin, get the first we can find and make him the new owner
		if ($protect->my_usertype != 3) {
			$query = "SELECT id FROM #__hydra_users WHERE user_type = '3' LIMIT 1";
			       $database->setQuery($query);
			       $new_owner = $database->loadResult();
		}
		
		// update folders
		$query = "UPDATE #__hydra_folders SET creator = '$new_owner' WHERE creator IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		// update files
		$query = "UPDATE #__hydra_files SET creator = '$new_owner' WHERE creator IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		// update documents          
		$query = "UPDATE #__hydra_documents SET creator = '$new_owner' WHERE creator IN ($cids)";
		       $database->setQuery($query);
		       $database->query();
		       
		// update tasks          
		$query = "UPDATE #__hydra_task SET creator = '$new_owner' WHERE creator IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		// delete comments - I don't think we need them anymore 
		$query = "DELETE FROM #__hydra_comments WHERE creator IN ($cids)";
		       $database->setQuery($query);
		       $database->query(); 

		// same goes for calendar entries
		$query = "DELETE FROM #__hydra_calendar_entry WHERE creator IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		       
		// delete notifications
		$query = "DELETE FROM #__hydra_task_notify WHERE uid IN ($cids)";
		       $database->setQuery($query);
		       $database->query();               

		         
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_users');               
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 04 13:31:29 CET 2006 )
	* @name    getHydraUsers
	* @version 1.2 
	* @param   int $limit
	* @param   int $limitstart
	* @return  array
	* @desc    loads all hydra-users from the database
	**/
	function getHydraUsers($limit = 0, $limitstart = 0)
	{
		global $database, $protect;
		
		$userspace = implode(',', $protect->my_userspace);
		
		$where = " WHERE id IN($userspace)";
		
		if ($protect->my_usertype == 3) { $where = ""; }
		
		$query = "SELECT COUNT(id) FROM #__hydra_users".$where;
		       $database->setQuery($query);
		       $total = $database->loadResult();

		$query_limit =  "\n LIMIT $limitstart, $limit";

		if ($limit < 1 AND ($limitstart < 1)) { $query_limit = "\n"; }
		
		$where = "\n WHERE h.id IN($userspace)";
		
		if ($protect->my_usertype == 3) { $where = "\n "; }
		
		$query = "SELECT h.id, h.user_type, j.name, j.id as jid, j.email, a.aro_id, m.group_id"
		       . "\n FROM #__hydra_users as h"
		       . "\n INNER JOIN #__users AS j"
		       . "\n ON h.jid = j.id"
		       . "\n INNER JOIN #__core_acl_aro AS a"
		       . "\n ON j.id = a.value"
		       . "\n INNER JOIN #__core_acl_groups_aro_map AS m"
		       . "\n ON a.aro_id = m.aro_id"
		       . $where
		       . "\n ORDER BY j.name ASC"
		       . $query_limit;
		       $database->setQuery($query);
		       $this->hydra_users = $database->loadAssocList();

		return array('total' => $total, 'users' => $this->hydra_users);              
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:50:25 CEST 2006 )
	* @name    getJoomlaUsers
	* @version 1.0 
	* @param   int $limit
	* @param   int $limitstart
	* @return  array
	* @desc    loads all joomla-users from the database
	**/
	function getJoomlaUsers($limit = 0, $limitstart = 0)
	{
		global $database;
		
		$hydra_users= array('0' => '0');

		$query = "SELECT jid FROM #__hydra_users";
		       $database->setQuery($query);
		       $hydra_users = $database->loadAssocList();
               $total       = count($hydra_users);
             
		$query_limit = "\n LIMIT $limitstart, $limit";

		if ($limit < 1 AND ($limitstart < 1)) { $query_limit = "\n"; }
		        
		foreach ($hydra_users AS $k => $v) { $hydra_list[] = $v['jid']; }
		
		$hydra_list = implode(',', $hydra_list);
		
		if ($total < 1) { $hydra_list = 0; }
		 
		$query = "SELECT COUNT(id) FROM #__users"
		       . "\n WHERE id NOT IN ($hydra_list)";
		       $database->setQuery($query);
		       $total = $database->loadResult();
     
		$query = "SELECT id, name, username, email FROM #__users"
		       . "\n WHERE id NOT IN ($hydra_list)"
		       . "\n ORDER BY name ASC"
		       . $query_limit;
		       $database->setQuery($query);
		       $this->joomla_users = $database->loadAssocList();
          
		return array('total' => $total, 'users' => $this->joomla_users);             
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:51:02 CEST 2006 )
	* @name    getImportData
	* @version 1.0 
	* @param   array $cid
	* @return  array
	* @desc    
	**/
	function getImportData($cid)
	{
		global $database;
		
		$cids = implode(',', $cid);
		
		$query = "SELECT id, name FROM #__users WHERE id IN ($cids)"
		       . "\n ORDER BY name ASC";
		       $database->setQuery($query);
		       
		return $database->loadAssocList();       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 08 12:31:36 CEST 2006 )
	* @name    importUsers
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    imports joomla-users to hydra
	**/
	function importUsers()
	{
		global $database, $protect;
		
		$users = mosGetParam($_POST, 'user');
		
		foreach ($users AS $k => $v)
		{
			$id = '';
			
			// check the usertype
			if ($v['type'] > $protect->my_usertype) {
				die();
			}
			
			// check the group
			if (!in_array($v['group'], $protect->my_groups)) {
				if ($protect->my_usertype != 3) {
					die();
				}
			}
			
			$query = "INSERT INTO #__hydra_users VALUES ('', '".$v['id']."', '".$v['type']."')";
			       $database->setQuery($query);
			       $database->query();
			       $id = mysql_insert_id();
			       
			$query = "INSERT INTO #__hydra_profile VALUES ('', '$id', 'language', '".$v['language']."')";
			       $database->setQuery($query);
			       $database->query();
			       
			$query = "INSERT INTO #__hydra_profile VALUES ('', '$id', 'theme', '".$v['theme']."')";
			       $database->setQuery($query);
			       $database->query();

			$query = "INSERT INTO #__hydra_group_members VALUES('".$v['group']."', '$id')";
			       $database->setQuery($query);
			       $database->query();         
		}
		
		hydraRedirect('index2.php?option=com_hydra&area=controlpanel&cmd=show_users');
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:52:01 CEST 2006 )
	* @name    saveSettings
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    updates the hydra-settings
	**/
	function saveSettings()
	{
		global $database;
		
		$upload_path = mosGetParam($_POST, 'upload_path', '');
		$debugger    = intval(mosGetParam($_POST, 'debugger', 0));
		$raw_output  = intval(mosGetParam($_POST, 'raw_output', 0));
		
		
		$query = "UPDATE #__hydra_settings SET upload_path = '$upload_path', debugger = '$debugger',"
		       . "\n raw_output = '$raw_output'";
		       $database->setQuery($query);
		       $database->query();
		       
		hydraRedirect('index2.php?option=com_hydra');       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 29 14:40:49 CET 2006 )
	* @name    updateProfile
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    
	**/
	function updateProfile()
	{
		global $hydra_sess;
		
		$hydra_sess->setProfile('time_offset', mosGetParam($_POST, 'time_offset'));
		$hydra_sess->setProfile('time_format', mosGetParam($_POST, 'time_format'));
		$hydra_sess->setProfile('language', mosGetParam($_POST, 'language', 'english'));
		$hydra_sess->setProfile('theme', mosGetParam($_POST, 'theme', 'default'));

		$hydra_sess->setProfile('cp_quickpanel', intval(mosGetParam($_POST, 'cp_quickpanel', 0)));
        $hydra_sess->setProfile('cp_tasks', intval(mosGetParam($_POST, 'cp_tasks', 0)));
        $hydra_sess->setProfile('cp_events', intval(mosGetParam($_POST, 'cp_events', 0)));
        $hydra_sess->setProfile('tasks_highlight', intval(mosGetParam($_POST, 'tasks_highlight', 0)));
      
        $hydra_sess->setProfile('nav_controlpanel', intval(mosGetParam($_POST, 'nav_controlpanel', 0)));
        $hydra_sess->setProfile('nav_projects', intval(mosGetParam($_POST, 'nav_projects')));
        $hydra_sess->setProfile('nav_tasks', intval(mosGetParam($_POST, 'nav_tasks', 0)));
        $hydra_sess->setProfile('nav_files', intval(mosGetParam($_POST, 'nav_files', 0)));
        $hydra_sess->setProfile('nav_calendar', intval(mosGetParam($_POST, 'nav_calendar', 0)));
        $hydra_sess->setProfile('nav_profile', intval(mosGetParam($_POST, 'nav_profile', 0)));
        $hydra_sess->setProfile('nav_usergroups', intval(mosGetParam($_POST, 'nav_usergroups', 0)));
      
      
		hydraRedirect('index2.php?option=com_hydra&cmd=profile', HL_MSG_PROFILE_UPDATED);
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 21:47:54 CET 2007 )
	* @name    getLatestTasks
	* @version 1.1
	* @param   void
	* @return  array $tasks
	* @desc    loads some tasks
	**/
	function getLatestTasks()
	{
		global $database, $protect;
		
		$projects = implode(',', $protect->my_projects);
		$filter   = "\n AND t.project IN ($projects)";
		
		if ($protect->my_usertype == 3) {
			$filter = "\n ";
		}
		
		$query = "SELECT t.task_id, t.project, t.uid, t.task_name, t.task_description, t.start_date,"
		       . "\n t.end_date, t.task_status, t.task_cstatus, t.priority, t.creator, t.last_changed, p.project_id, p.project_name,"
		       . "\n j.name FROM #__hydra_task AS t,"
               . "\n #__hydra_project AS p, #__hydra_users AS u, #__users AS j"
               . "\n WHERE t.project = p.project_id"
               . $filter
               . "\n AND t.uid = u.id"
               . "\n AND u.jid = j.id"
               . "\n ORDER BY t.last_changed DESC LIMIT 10";
               $database->setQuery($query);
               $tasks = $database->loadObjectList();
             
		return $tasks;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 04 12:03:21 CET 2006 )
	* @name    getUpcomingEvents()
	* @version 1.0 
	* @param   void
	* @return  $events
	* @desc    loads 10 upcoming events
	**/
	function getUpcomingEvents()
	{
		global $database, $protect;
		
		$now = time();
		
		$query = "SELECT event_id, title, start_date, end_date FROM #__hydra_events"
		       . "\n WHERE creator = '$protect->my_id' AND start_date > $now"
		       . "\n ORDER BY start_date ASC LIMIT 10";
		       $database->setQuery($query);
		       $events = $database->loadObjectList();
		       
		return $events;       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 03:16:15 CET 2007 )
	* @name    changeUsertype
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    changes the user-type of a user
	**/
	function changeUsertype()
	{
		global $database, $protect;
		
		$new_usertype = intval(mosGetParam($_POST, 'user_type', 0));
		$user = intval(mosGetParam($_POST, 'id', 0));
		
		$query = "SELECT user_type FROM #__hydra_users WHERE id = '$user'";
		       $database->setQuery($query);
		       $old_usertype = $database->loadResult();
		    
		          
		if ($protect->my_id == $user) {
			return false;
		}
		
		if ($protect->my_usertype != 3) {
			
		   if (!in_array($user, $protect->my_userspace)) {
			  return false;
		   }
		
		   if ($protect->my_usertype < $old_usertype) {
			  return false;
		   }
		   
		}   
		
		$query = "UPDATE #__hydra_users SET user_type = '$new_usertype' WHERE id = '$user'";
		       $database->setQuery($query);
		       $database->query();
		       
		hydraRedirect('index2.php?option=com_hydra&cmd=show_users');       
	}
	
}
?>