<?php
/**
* $Id: projects.class.php 27 2007-04-16 18:50:09Z eaxs $
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

class Projects
{
	var $usergroups;

	var $projects;

	var $total_projects;
	
	var $tasks;
	
	var $total_tasks;
	
	var $task_notifications;
	
	var $task_comments;


	/**
	* @author  Tobias Kuhn ( Tue Sep 26 14:01:22 CEST 2006 )
	* @name    getUserGroups
	* @version 1.0
	* @param   void
	* @return  void
	* @desc    loads all usergroups
	**/
	function getUserGroups()
	{
		global $database, $hydra_debug, $protect;

        if($protect->my_usertype == 3) {
            $filter = "";
        }
        else {
            $filter = "\n WHERE group_id IN(".implode(',',$protect->my_groups).")";
        }
        
        
        
		$query = "SELECT group_id, group_name FROM #__hydra_groups"
		       . "\n $filter"
               . "\n GROUP BY group_id" 
		       . "\n ORDER BY group_name ASC";
		       $database->setQuery($query);
		       $this->usergroups = $database->loadAssocList();
		       
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }       
	}


	/**
	* @author  Tobias Kuhn ( Wed Jan 31 18:08:07 CET 2007 )
	* @name    createProject
	* @version 1.2
	* @param   void
	* @return  void
	* @desc    creates a project
	**/
	function createProject()
	{
		global $database, $protect, $hydra_debug;

		// get data
		$name   = mosGetParam($_POST, 'project_name');
		$desc   = addslashes($_POST['project_desc']);
		$start  = strtotime(mosGetParam($_POST, 'start_date'));
		$end    = strtotime(mosGetParam($_POST, 'end_date'));
		$groups = mosGetParam($_POST, 'project_groups', array());
		$id     = intval(mosGetParam($_POST, 'id', 0));

		$msg    = HL_MSG_PROJECT_CREATED.": ".$name;

		
		// make there is a group
		if (!count($groups)) { $groups[] = $protect->my_groups[0]; }

		
		// make sure we have a name
		if (strlen($name) < 1) { hydraRedirect('index2.php?option=com_hydra&area=projects', HL_FORM_ALERT); }

		
		// do the query
		$query = "INSERT INTO #__hydra_project VALUES ('', '$name', '$desc', '$start', '$end', '".time()."')";

		if ($id >= 1) {

			$msg    = HL_MSG_PROJECT_MODIFIED.": ".$name;

			$query = "UPDATE #__hydra_project SET project_name = '$name', project_description = '$desc',"
			       . "\n start_date = '$start', end_date = '$end', mdate = '".time()."'"
			       . "\n WHERE project_id = '$id'";

		}
		$database->setQuery($query);
		$database->query();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }       
		
		
		if ($id < 1) { $id = mysql_insert_id(); }

		
		
		$query = "DELETE FROM #__hydra_project_groups WHERE pid = '$id'";
		       $database->setQuery($query);
		       $database->query();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		 
		foreach ($groups AS $k => $v)
		{
			$query = "INSERT INTO #__hydra_project_groups VALUES ('$id', '$v')";
			       $database->setQuery($query);
		           $database->query();
		           
		    if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }             
		}

		
		hydraRedirect('index2.php?option=com_hydra&area=projects', $msg);
	}


	/**
	* @author  Tobias Kuhn ( Tue Sep 26 14:02:11 CEST 2006 )
	* @name    getProjects
	* @version 1.0
	* @param   int $limit
	* @param   int $limitstart
	* @param   bool $filter
	* @return  int $total
	* @desc    loads all project information and returns total
	**/
	function getProjects($limit = 0, $limitstart = 0)
	{
		global $database, $protect, $hydra_sess, $hydra_debug;

		// get data
		$my_projects = implode(',',$protect->my_projects);
		$query_limit = "\n LIMIT $limitstart, $limit";
		$order_by    = mosGetParam($_POST, 'order_by', $hydra_sess->profile('project_table_order_by', 'project_name', true));
		$order_dir   = mosGetParam($_POST, 'order_dir', $hydra_sess->profile('project_table_order_dir', 'ASC', true));
		$pid         = intval(mosGetParam($_REQUEST, 'pid', 0));
		$filter      = '';

		if ($limit < 1 AND ($limitstart < 1)) { $query_limit = "\n"; }
		
		// filter ?
		if ($pid) {
			$filter = "\n AND project_id = '$pid'";
			if ($protect->my_usertype == 3) { $filter = "\n WHERE project_id = '$pid'"; }
		}
		
		$where = "\n WHERE project_id IN ($my_projects)";
		if ($protect->my_usertype == 3) { $where = "\n"; }

		
		// get total
		$query = "SELECT COUNT(project_id) FROM #__hydra_project"
		       . $where.$filter;
		       $database->setQuery($query);
		       $this->total_projects = $database->loadResult();
		       
        if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }
        
        
        // second filter
		$where = "\n WHERE p.project_id IN ($my_projects)";
		if ($protect->my_usertype == 3) { $where = "\n"; }

		
		// get projects
		/*
		$query = "SELECT project_id, project_name, project_description, start_date, end_date, mdate"
		       . "\n FROM #__hydra_project"
		       . $where.$filter
		       . "\n ORDER BY $order_by $order_dir"
		       . $query_limit;*/
		
		$query = "SELECT p.project_id, p.project_name, p.project_description, p.start_date, p.end_date, p.mdate,"
		       . "\n COUNT(t.task_id) AS total_tasks, SUM(t.task_status) AS project_status"
		       . "\n FROM #__hydra_project AS p"
		       . "\n LEFT JOIN #__hydra_task AS t ON t.project = p.project_id"
		       . $where
		       . "\n GROUP BY p.project_id"
		       . "\n ORDER BY p.$order_by $order_dir"
		       . $query_limit; 
		       $database->setQuery($query);
		       $this->projects = $database->loadObjectList();
  
        if( !is_array($this->projects) ) { $this->projects = array(); }
               
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }       
	}


	/**
	* @author  Tobias Kuhn ( Wed Jan 31 18:11:37 CET 2007 )
	* @name    loadProject
	* @version 1.1
	* @param   int $id
	* @return  array
	* @desc    loads a project
	**/
	function loadProject($id)
	{
		global $database, $protect, $hydra_debug;

		$my_projects = implode(',',$protect->my_projects);

		$and = "\n AND project_id IN ($my_projects)";
		if ($protect->my_usertype == 3) { $and = "\n"; }

		
		$query = "SELECT project_id, project_name, project_description, start_date, end_date"
		       . "\n FROM #__hydra_project"
		       . "\n WHERE project_id = '$id'"
		       . $and;
		       $database->setQuery($query);

		       
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }       

		 
		return $database->loadAssocList();
	}


	
	/**
	* @author  Tobias Kuhn ( Wed Jan 31 18:12:34 CET 2007 )
	* @name    loadTask
	* @version 1.1
	* @param   int $id
	* @return  array $task
	* @desc    loads a task
	**/
	function loadTask($id)
	{
		global $database, $protect, $hydra_debug;

		$projects = implode(',', $protect->my_projects);

		$and = "\n AND t.project IN ($projects)";
		if ($protect->my_usertype == 3) { $and = "\n"; }

		
		// load Task information
		$query = "SELECT t.task_id, t.project, t.uid, t.task_name, t.task_description, t.start_date,"
		       . "\n t.end_date, t.task_status, t.task_cstatus, t.priority, m.parent_task"
		       . "\n FROM #__hydra_task AS t, #__hydra_task_map AS m"
		       . "\n WHERE t.task_id = '$id'"
		       . "\n AND t.task_id = m.task"
		       . $and;
		       $database->setQuery($query);
		       $database->loadObject($task);

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }       

		 
		return $task;
	}


	/**
	* @author  Tobias Kuhn ( Thu Feb 01 22:35:17 CET 2007 )
	* @name    formatProjectStatus
	* @version 1.4
	* @param   int $id
	* @param   int $start_date
	* @param   int $end_date
	* @param   int $total_tasks
	* @param   int $project_status
	* @return  string $format
	* @desc    returns project status bar with image and details
	**/
	function formatProjectStatus($id, $start_date, $end_date, $total_tasks = 0, $total_status = 0)
	{
		global $database, $hydra;

		$start_date = hydraTime($start_date);
		$end_date   = hydraTime($end_date);

		
		// get the overall progress
		switch ($total_tasks < 1)
		{
			case true:
				$status = 0;
				break;
				
			case false:
				@$status = $total_status / $total_tasks;
				$status  = @round($status / 5, 0) * 5;
				break;	
		}

		// project completed ?
		switch ($status)
		{
			// yes
			case 100:
				
				$overlib = "<tr>"
				         . "<td nowrap><strong>".HL_PROJECT_START_TIME."</strong></td>"
		                 . "<td nowrap>".hydraDate($start_date)."</td>"
		                 . "</tr>"
		                 . "<tr>"
		                 . "<td nowrap><strong>".HL_PROJECT_END_TIME."</strong></td>"
		                 . "<td nowrap>".hydraDate($end_date)."</td>"
		                 . "</tr>"
		                 . "<tr>"
		                 . "<td nowrap><strong>".HL_TIME_TOTAL."</strong></td>"
		                 . "<td nowrap>".round(($end_date - $start_date) / 86400,0)." ".HL_DAYS."</td>"
		                 . "</tr>"
		                 . "<tr>"
		                 . "<td nowrap><strong>".HL_STATUS."</strong></td>"
		                 . "<td nowrap><font color=darkgreen><strong>".HL_STATUS_COMPLETE."</strong></font></td>"
		                 . "</tr>";
		                 
				break;
			
			// no		
			default:
				
				$timeleft = round(($end_date - time()) / 86400,0);
				
				$overlib = "<tr>"
				         . "<td nowrap><strong>".HL_PROJECT_START_TIME."</strong></td>"
		                 . "<td nowrap>".hydraDate($start_date)."</td>"
		                 . "</tr>"
		                 . "<tr>"
		                 . "<td nowrap><strong>".HL_PROJECT_END_TIME."</strong></td>"
		                 . "<td nowrap>".hydraDate($end_date)."</td>"
		                 . "</tr>"
		                 . "<tr>"
		                 . "<td nowrap><strong>".HL_TIME_TOTAL."</strong></td>"
		                 . "<td nowrap>".round(($end_date - $start_date) / 86400,0)." ".HL_DAYS."</td>"
		                 . "</tr>";
		         
		                         
		        // behind schedule?
		        switch ($timeleft < 1)
		        {
		        	case true:
		        		
		        		$timeleft = str_replace('-', '', $timeleft);

		   	            $overlib .= "<tr>";
		                $overlib .= "<td nowrap><strong>".HL_TIME_LEFT."</strong></td>";
		                $overlib .= "<td nowrap><font color=darkred><strong>".$timeleft." ".HL_BEHIND_SCHEDULE."</strong></font></td>";
		                $overlib .= "</tr>";
		        		
		        		break;
		        		
		        	case false:
		        		
		        		$overlib .= "<tr>";
		                $overlib .= "<td nowrap><strong>".HL_TIME_LEFT."</strong></td>";
		                $overlib .= "<td nowrap>".$timeleft." ".HL_DAYS."</td>";
		                $overlib .= "</tr>";
		        		
		        		break;	
		        }
		        
		        // show total tasks
		        $overlib .= "</tr>";
		        $overlib .= "<td nowrap><strong>".HL_TOTAL_TASKS."</strong></td>";
		        $overlib .= "<td nowrap>".$total_tasks."</td>";
		        $overlib .= "</tr>";
		        
				break;	
		}

		
		// progress image
		$image = 'progress_'.$status.'.gif';

		
		// put it all together
		$format = "<a onmouseover=\"return overlib('<table>".$overlib."</table>', CAPTION, '".HL_STATUS."', BELOW, RIGHT);\" onmouseout=\"return nd();\">"
		        . $hydra->load('img', $image, 'alt="" title=""')
		        . "</a>";

		        
		return $format;
	}


	/**
	* @author  Tobias Kuhn ( Fri Dec 29 14:05:30 CET 2006 )
	* @name    formatTaskStatus
	* @version 1.3
	* @param   int $status
	* @param   int $start_date
	* @param   int $end_date
	* @return  string $format
	* @desc    returns the a formatted status with image etc.
	**/
	function formatTaskStatus($status, $start_date, $end_date, $id = 0)
	{
		global $hydra, $protect;

		$start_date = hydraTime($start_date);
		$end_date   = hydraTime($end_date);

		if (intval($status) == 100) {
			$overlib = "<tr>"
		             . "<td nowrap><strong>".HL_TASK_START."</strong></td>"
		             . "<td nowrap>".hydraDate($start_date)."</td>"
		             . "</tr>"
		             . "<tr>"
		             . "<td nowrap><strong>".HL_TASK_END."</strong></td>"
		             . "<td nowrap>".hydraDate($end_date)."</td>"
		             . "</tr>"
		             . "<tr>"
		             . "<td nowrap><strong>".HL_STATUS."</strong></td>"
		             . "<td nowrap><font color=darkgreen><strong>".HL_STATUS_COMPLETE."</strong></font></td>"
		             . "</tr>";
		}
		else {
			$timeleft = round(($end_date - time()) / 86400,0);

			$overlib = "<tr>"
		             . "<td nowrap><strong>".HL_TASK_START."</strong></td>"
		             . "<td nowrap>".hydraDate($start_date)."</td>"
		             . "</tr>"
		             . "<tr>"
		             . "<td nowrap><strong>".HL_TASK_END."</strong></td>"
		             . "<td nowrap>".hydraDate($end_date)."</td>"
		             . "</tr>"
		             . "<tr>"
		             . "<td nowrap><strong>".HL_TIME_TOTAL."</strong></td>"
		             . "<td nowrap>".round(($end_date - $start_date) / 86400,0)." ".HL_DAYS."</td>"
		             . "</tr>";
		   if ($timeleft >= 0) {
		     $overlib .= "<tr>";
		     $overlib .= "<td nowrap><strong>".HL_TIME_LEFT."</strong></td>";
		     $overlib .= "<td nowrap>".$timeleft." ".HL_DAYS."</td>";
		     $overlib .= "</tr>";
		   }
		   else {
		   	 $timeleft = str_replace('-', '', $timeleft);

		     $overlib .= "<tr>";
		     $overlib .= "<td nowrap><strong>".HL_TIME_LEFT."</strong></td>";
		     $overlib .= "<td nowrap><font color=darkred><strong>".$timeleft." ".HL_BEHIND_SCHEDULE."</strong></font></td>";
		     $overlib .= "</tr>";
		   }
		}


		$image = 'progress_'.$status.'.gif';

		$format = "<a onmouseover=\"return overlib('<table>".$overlib."</table>', CAPTION, '".HL_STATUS."', BELOW, RIGHT);\" onmouseout=\"return nd();\">"
		        . $hydra->load('img', $image, 'alt="" title=""')
		        . "</a>";

		// menu for progress-update
		if ($protect->current_area == 'projects' AND ($protect->perm('new_task')) AND ($id)) {
			$menu = 'progress_'.$id;

			$format = "<a onclick=\"return buttonClick(event, '$menu');\" onmouseover=\"return overlib('<table>".$overlib."</table>', CAPTION, '".HL_STATUS."', BELOW, RIGHT);\" onmouseout=\"return nd();\" style='cursor:pointer !important'>"
		           . $hydra->load('img', $image, 'alt="'.HL_UPDATE_PROGRESS.'" title="'.HL_UPDATE_PROGRESS.'"')
		           . "</a>";

		   $prog_array = array('0', '5', '10', '15', '20', '25', '30', '35', '40', '45', '50',
		                       '55', '60', '65', '70', '75', '80', '85', '90', '95', '100');

			$format .= HydraMenu::menu($menu);

			foreach ($prog_array AS $k => $v)
			{
				$img = false;

				$new_status = $v;

				if ($v == $status) { $img = '16_tick.gif'; }

				$format .= HydraMenu::item($new_status."%", $img, '', "updateProgress($id,$v)");
			}

			$format .= HydraMenu::menu();
		}

		return $format;
	}


	/**
	* @author  Tobias Kuhn ( Thu Feb 01 22:54:02 CET 2007 )
	* @name    deleteProject
	* @version 1.3
	* @param   void
	* @return  void
	* @desc    deletes a task
	**/
	function deleteProject()
	{
		global $database, $protect, $hydra_debug;

		$cid  = mosGetParam($_POST, 'cid');
		$cids = implode(',', $cid);
        $msg  = HL_MSG_PROJECTS_DELETED;
        
        
		// make sure we have projects selected
		if (count($cid) < 1) { hydraRedirect('index2.php?option=com_hydra&area=projects', HL_DEL_PROJECT_WARN); }

		foreach ($cid AS $k => $v)
		{
			// make sure we can delete the project if we are not an admin
			if ($protect->my_usertype < 3) {
				
			   if (!in_array($v, $protect->my_projects)) {
			   	  hydraRedirect('index2.php?option=com_hydra&area=projects', HL_MSG_HACKER." ".HL_MSG_HACKER_PROJECT);
			   }
			   
			}
		}

		
		// update all files and folders which are associates with the project
		$query = "UPDATE #__hydra_folders SET folder_type = '0', project = '0' WHERE project IN ($cids) AND folder_type = '1'";
			    $database->setQuery($query);
			    $database->query();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }  

		  
		$query = "UPDATE #__hydra_files SET file_type = '0', project = '0' WHERE project IN ($cids) AND file_type = '1'" ;
			    $database->setQuery($query);
			    $database->query();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		 
		$query = "UPDATE #__hydra_documents SET doc_type = '0', project = '0' WHERE project IN ($cids) AND doc_type = '1'";
		       $database->setQuery($query);
		       $database->query();

		       
		// delete
		$query = "DELETE FROM #__hydra_project WHERE project_id IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		 
		// delete project-groups
		$query = "DELETE FROM #__hydra_project_groups WHERE pid IN ($cids)";
		       $database->setQuery($query);
		       $database->query();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		$query = "SELECT task_id FROM #__hydra_task WHERE project IN($cids)";
		       $database->setQuery($query);
		       $tasks = $database->loadResultArray();

		if(!is_array($tasks)) { $tasks = array(); }

		$total_tasks = count($tasks);
		$tasks = implode(',', $tasks);
		 
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		
		if ($total_tasks) {
			
			$query = "DELETE FROM #__hydra_task WHERE project IN ($cids)";
		           $database->setQuery($query);
		           $database->query();

		    if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
			
		    
			$query = "DELETE FROM #__hydra_task_notify WHERE task IN($tasks)";
		           $database->setQuery($query);
		           $database->query();
		    
		    if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		         
		    $query = "DELETE FROM #__hydra_task_map WHERE task IN($tasks)";
		           $database->setQuery($query);
		           $database->query();
		     
		    if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		         
		    $query = "DELETE FROM #__hydra_comments WHERE task IN($tasks)";
		           $database->setQuery($query);
		           $database->query();               
		       
		    if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
		}
		
		        
		hydraRedirect('index2.php?option=com_hydra&area=projects', $msg);
	}


	
	/**
	* @author  Tobias Kuhn ( Mon Dec 25 15:08:19 CET 2006 )
	* @name    createTask
	* @version 1.3
	* @param   void
	* @return  void
	* @desc    creates/updates a task
	**/
	function createTask()
	{
		global $database, $hydra_sess, $protect, $hydra_debug, $hydra;

		$name      = mosGetParam($_POST, 'task_name');
		$desc      = addslashes ($_POST['task_desc']);
		$start     = strtotime(mosGetParam($_POST, 'start_date'));
		$end       = strtotime(mosGetParam($_POST, 'end_date'));
		$status    = mosGetParam($_POST, 'status');
		$project   = mosGetParam($_POST, 'task_project');
		$user      = mosGetParam($_POST, 'task_user');
		$creator   = $hydra_sess->hid;
		$now       = time();
        $id        = intval(mosGetParam($_POST, 'id', 0));
        $parent    = intval(mosGetParam($_POST, 'parent', 0));
        $priority  = intval(mosGetParam($_POST, 'priority', 0));
        $cstatus   = trim(mosGetParam($_POST, 'custom_status', ''));
        $notify_user = intval(mosGetParam($_POST, 'notify_user', 0));
        
        $msg = HL_MSG_TASK_CREATED.': '.$name;

        $my_projects = implode(",", $protect->my_projects);

        
        // force project
        if ($parent) {
      	   $query = "SELECT p.project_id FROM #__hydra_project AS p, #__hydra_task AS t"
      	          . "\n WHERE p.project_id = t.project AND t.task_id = '$parent'";
      	          if ($protect->my_usertype != 3) { $query .= "\n AND p.project_id IN($my_projects)"; }
      	          $database->setQuery($query);
      	          $project = $database->loadResult();
      	          
      	   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }        
        }

        
        // make sure the task has a name
		if (strlen($name) < 1) {
		   hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=new_task', HL_FORM_ALERT);
		}

		
		// make sure we have access to the select project
		if ($protect->my_usertype != 3) {
		   if (!in_array($project, $protect->my_projects)) {
		   	  hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=new_task',HL_MSG_HACKER.' '.HL_MSG_HACKER_PROJECT);
		   }
		}

		
		// make sure the end date of the task is not later than the project end
		$query = "SELECT end_date FROM #__hydra_project WHERE project_id = '$project'";
		       $database->setQuery($query);
		       $project_end = intval($database->loadResult());

		if (intval($end) > $project_end) { $end = $project_end; }

		
		// insert task
		$query = "INSERT INTO #__hydra_task VALUES ('', '$project', '$user', '$name', '$desc', '$start', '$end',"
		       . "\n '$status', '$cstatus', '$creator', '$now', '$priority')";


		if ($id) {
			$msg = HL_MSG_TASK_MODIFIED.': '.$name;

			// make sure we have access to the select task
			if ($protect->my_usertype != 3) {
			   $query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
			          $database->setQuery($query);

			   if (!in_array($database->loadResult(), $protect->my_projects)) {
			   	  hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=new_task',HL_MSG_HACKER.' '.HL_MSG_HACKER_PROJECT);
			   }
			}

			
			// update task child<->parent relation
			$query = "UPDATE #__hydra_task_map SET parent_task = '$parent' WHERE task = '$id'";
			       $database->setQuery($query);
			       $database->query();
			       
            if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
            
             
			// update project of subtasks
			$query = "UPDATE #__hydra_task AS t, #__hydra_task_map AS m SET t.project = '$project'"
			       . "\n WHERE m.parent_task = '$id' AND m.task = t.task_id";
			       $database->setQuery($query);
			       $database->query();
			       
            if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
		    
		    
			// update task
			$query = "UPDATE #__hydra_task SET project='$project',uid='$user',task_name='$name',"
			       . "\n task_description='$desc',start_date='$start',end_date='$end',task_status='$status',task_cstatus ='$cstatus',"
			       . "\n last_changed='$now',priority='$priority' WHERE task_id='$id'";
 
		}
		$database->setQuery($query);
		$database->query();

        if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
        
        
		// store task parent<->child relation
		if (!$id) {
			$id = mysql_insert_id();

			$query = "INSERT INTO #__hydra_task_map VALUES('$id', '$parent')";
			       $database->setQuery($query);
			       $database->query();
			       
			if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

			
			if ($notify_user) {
				
			   // notify users
		       $subject   = HL_NOTIFICATION_TASK_ASSIGNED_EMAIL_SUBJECT." ".$name;
		       $user_info = $hydra->getUserDetails($protect->my_id);
		       $subject   = str_replace('{user}', $user_info->name, $subject);
		 
		       $this->sendTaskNotification($id, $subject, $user);
			}
			     
		}
		else {
			
			// notify users
		    $subject   = HL_NOTIFICATION_TASK_UPDATE_EMAIL_SUBJECT." ".$name;
		    $user_info = $hydra->getUserDetails($protect->my_id);
		    $subject   = str_replace('{user}', $user_info->name, $subject);
		 
		    $this->sendTaskNotification($id, $subject);
		}

		hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks', $msg);
	}


	/**
	* @author  Tobias Kuhn, FallenClient ( Tue Jan 23 21:55:57 CET 2007 )
	* @name    getTasks
	* @version 1.3
	* @param   int $limit
	* @param   int $limitstart
	* @return  void
	* @desc    loads all tasks
	**/
	function getTasks($limit = 0, $limitstart = 0, $level = 0)
	{
		global $database, $protect, $hydra_sess, $hydra_debug;

		$projects    = implode(',', $protect->my_projects);
		$query_limit = "\n LIMIT $limitstart, $limit";

		if ($limit < 1 AND ($limitstart < 1)) { $query_limit = "\n"; }
		
		
		// table sorting
		$order_by    = mosGetParam($_POST, 'order_by', $hydra_sess->profile('task_table_order_by', 'parent, name', true));
		$order_dir   = mosGetParam($_POST, 'order_dir', $hydra_sess->profile('task_table_order_dir', 'ASC', true));
		

		// Filter settings
		$filter_task     = intval(mosGetParam($_REQUEST, 'filter_task', $hydra_sess->profile('task_filter_task')));
		$filter_assigned = intval(mosGetParam($_REQUEST, 'filter_task_assigned', $hydra_sess->profile('filter_task_assigned')));
		$filter_project  = intval(mosGetParam($_REQUEST, 'pid', $hydra_sess->profile('task_filter_project')));
        
        // added by Fallenclient
        $filter_creator = intval(mosGetParam($_REQUEST, 'filter_task_creator', $hydra_sess->profile('filter_task_creator')));
        
        
        if (!in_array($filter_assigned, $protect->my_userspace) AND($filter_assigned != $protect->my_id)) { $filter_assigned = 0; }
		
		// Filter for query
		$filter = "\n WHERE t.project IN ($projects)" ;

		if ($protect->my_usertype == 3) { $filter = "\n WHERE t.project = t.project"; }

        // added by FallenClient
        switch ( $filter_creator > 0 )
		{
			case true:
				$filter .="\n AND t.creator = '$filter_creator'";
				break;
		}
		
		// filter task status
		switch ( $filter_task )
		{
			case 1:
				$filter .= "\n AND t.task_status != '100'";
				break;
				
			case 2:
				$filter .= "\n AND t.task_status = '100'";
				break;	
		}
		
		
		// filter assigned to
		switch ( $filter_assigned > 0 )
		{
			case true:
				$filter .= "\n AND t.uid = '$filter_assigned'";
				break;
		}
		
		
		// filter project
		switch ( $filter_project > 0 )
		{
			case true:
				$filter .= "\n AND t.project = '$filter_project'";
				break;
		}
		
		
		// do the query
		$query = "SELECT t.task_id as id, t.project, t.uid, t.task_name as name,"
		       . "\n t.task_description, t.start_date, t.end_date, t.task_status,"
		       . "\n t.task_cstatus, t.creator, t.last_changed, t.priority,"
		       . "\n m.parent_task as parent,"
		       . "\n p.project_id, p.project_name,"
		       . "\n j.name as username, COUNT(c.comment_id) AS comments"
		       . "\n FROM #__hydra_task AS t"
		       . "\n LEFT JOIN #__hydra_task_map AS m ON m.task = t.task_id"
		       . "\n LEFT JOIN #__hydra_project AS p ON p.project_id = t.project"
		       . "\n LEFT JOIN #__hydra_users AS u ON u.id = t.uid"
		       . "\n LEFT JOIN #__users AS j ON j.id = u.jid"
		       . "\n LEFT JOIN #__hydra_comments AS c ON c.task = t.task_id"
		       . $filter
		       . "\n GROUP BY t.task_id"
		       . "\n ORDER BY $order_by $order_dir";
               $database->setQuery($query);
               $this->tasks = $database->loadObjectList();

         if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }   

                 
         // total tasks            
         $this->total_tasks = count ($this->tasks);
           
          
         // setup parent-child relation      
         $children = array();

         $task_ids = array();
         if(!is_array($this->tasks)) { $this->tasks = array(); }
         
         foreach ($this->tasks as $v ) 
         {
		     $parent = $v->parent;
		     $list   = @$children[$parent] ? $children[$parent] : array();
		
		     array_push( $list, $v );
		
		     $children[$parent] = $list;
		     
		     $task_ids[] = $v->id;
	     } 
         
	     
	     // get task notifications
	     if ($this->total_tasks) {
	        $task_ids = implode(',', $task_ids);
	     
	        $query = "SELECT task FROM #__hydra_task_notify WHERE task IN($task_ids) AND uid = '$protect->my_id'";
	               $database->setQuery($query);
	               $this->task_notifications = $database->loadResultArray();
	               
	        if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }
	               
	     }
	     else {
	     	$this->task_notifications = array();
	     }

	            
	     // build the tree            
	     $list = mosTreeRecurse( 0, '', array(), $children, 1, 0, 0 );
	  
	     if(!is_array($list)) { $list = array(); }
	     
	     // apply filter limits
	     $this->tasks = array_slice( $list, $limitstart, $limit );
	}
	
	

	/**
	* @author  Tobias Kuhn ( Sun Dec 10 17:16:31 CET 2006 )
	* @name    loadProjectSheet
	* @version 1.4
	* @param   int $id
	* @return  array $data
	* @desc    returns information about a task
	**/
	function loadProjectSheet($id)
	{
		global $database, $protect, $hydra_debug;

		$data = array();
		
		// filter ?
		switch(intval($protect->my_usertype))
		{
			case 3:
				$filter = "\n #__hydra_project_groups AS g"
				        . "\n WHERE g.pid = '$id'";
				break;
				
			default:
				$my_groups = implode(",",$protect->my_groups);
				
				$filter = "\n #__hydra_project_groups AS g"
				        . "\n WHERE g.gid IN ($my_groups)"
				        . "\n AND g.pid = '$id'";
				break;	
		}
		

		
		// get the project main-information
		$query = "SELECT p.project_id, p.project_name, p.project_description, p.start_date,"
		       . "\n p.end_date FROM #__hydra_project AS p,"
		       . $filter
		       . "\n AND project_id = '$id'";
		       $database->setQuery($query);
		       $database->loadObject($project);

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }   
		       
		$data['project'] = $project;

		
		// get the groups that are involved in the project
		$query = "SELECT group_name FROM #__hydra_groups, #__hydra_project_groups"
		       . "\n WHERE #__hydra_project_groups.pid = '".$project->project_id."'"
		       . "\n AND #__hydra_project_groups.gid = #__hydra_groups.group_id"
		       . "\n GROUP BY group_name";
		       $database->setQuery($query);
		       $data['groups'] = $database->loadObjectList();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }   

		 
		// get the task of this project
		$query = "SELECT task_name, task_description, task_status, start_date, end_date FROM #__hydra_task WHERE project = '".$project->project_id."'";
		       $database->setQuery($query);
		       $data['tasks'] = $database->loadObjectList();

		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }   

		 
		// get all project folders
		$query = "SELECT folder_id, folder_name FROM #__hydra_folders WHERE project = '".$project->project_id."' "
		       . "\n AND folder_access <= '$protect->my_usertype' ORDER by folder_name ASC";
		       $database->setQuery($query);
		       $data['folders'] = $database->loadObjectList();
     
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }

		 
		// get project documents
		$query = "SELECT doc_id, doc_title FROM #__hydra_documents WHERE project = '$project->project_id'"
		       . "\n AND doc_access <= '$protect->my_usertype' ORDER BY doc_title ASC";
		       $database->setQuery($query);
		       $data['docs'] = $database->loadObjectList();

        if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }   
        
        
		// get project files
		$query = "SELECT file_id, file_name FROM #__hydra_files WHERE project = '$project->project_id'"
		       . "\n AND file_access <= '$protect->my_usertype' ORDER BY file_name ASC";
		       $database->setQuery($query);
		       $data['files'] = $database->loadObjectList();

        if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }   
        
        
		return $data;
	}


	/**
	* @author  Tobias Kuhn ( Tue Sep 26 14:05:12 CEST 2006 )
	* @name    loadTaskSheet
	* @version 1.0
	* @param   int $id
	* @return  array $data
	* @desc    loads task information
	**/
	function loadTaskSheet($id)
	{
		global $database;

		$query = "SELECT t.task_id, t.task_name, t.task_description, t.start_date, t.end_date, t.task_status, t.task_cstatus, t.creator,"
		       . "\n p.project_name, p.project_id, h.jid, u.name "
		       . "\n FROM #__hydra_task AS t, #__hydra_project AS p, #__hydra_users AS h, #__users AS u"
		       . "\n WHERE t.task_id = '$id'"
		       . "\n AND p.project_id = t.project"
		       . "\n AND h.id = t.creator"
		       . "\n AND h.jid = u.id";
		       $database->setQuery($query);
		       $database->loadObject($data);

		return $data;
	}


	/**
	* @author  Tobias Kuhn ( Sun Dec 31 16:10:09 CET 2006 )
	* @name    deleteTask
	* @version 1.1
	* @param   void
	* @return  void
	* @desc    deletes a task
	**/
	function deleteTask()
	{
		global $database, $protect;

		$cid         = mosGetParam($_POST, 'cid', array());
		$cids        = implode(',', $cid);
		$my_projects = implode(',', $protect->my_projects);
		$msg = HL_MSG_TASK_DELETED;

		// security check - makes sure that we can only delete tasks we have access to
		if ($protect->my_usertype != 3) {

		   $query = "SELECT t.task_id FROM #__hydra_task AS t"
		          . "\n INNER JOIN #__hydra_project AS p ON p.project_id = t.project"
		          . "\n WHERE t.project IN($my_projects)"
		          . "\n AND t.task_id IN($cids)";
		          $database->setQuery($query);
		          $cids = implode(',', $database->loadResultArray());
 
		   if ($database->_errorNum) { $msg = $database->_errorMsg; }
		}

		// make sure we have something selected
		if (count($cid) < 1) {
			hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks', HL_DEL_PROJECT_WARN);
			return;
		}

		// delete tasks
		$query = "DELETE FROM #__hydra_task WHERE task_id IN($cids)";
		       $database->setQuery($query);
		       $database->query();

		if ($database->_errorNum) { $msg = $database->_errorMsg; }

		// delete relation
		$query = "DELETE FROM #__hydra_task_map WHERE task IN($cids)";
		       $database->setQuery($query);
		       $database->query();

		if ($database->_errorNum) { $msg = $database->_errorMsg; }

		
		// delete comments
		$query = "DELETE FROM #__hydra_comments WHERE task IN($cids)";
		       $database->setQuery($query);
		       $database->query();
		       
		if ($database->_errorNum) { $msg = $database->_errorMsg; }

		 
		// delete child tasks too!
		$query = "SELECT task FROM #__hydra_task_map WHERE parent_task IN($cids)";
		       $database->setQuery($query);
		       $sub_tasks = @implode(',', $database->loadResultArray());

		if ($database->_errorNum) { $msg = $database->_errorMsg; }

		
		// delete relation of childs
		$query = "DELETE FROM #__hydra_task_map WHERE task IN($sub_tasks)";
		       $database->setQuery($query);
		       $database->query();

		if (strlen($sub_tasks) >= 1 AND ($database->_errorNum)) {
		   $msg = $database->_errorMsg;
		}

		 
		$query = "DELETE FROM #__hydra_comments WHERE task IN($sub_tasks)";
		       $database->setQuery($query);
		       $database->query();
		               
		if (strlen($sub_tasks) >= 1 AND ($database->_errorNum)) {
		   $msg = $database->_errorMsg;
		}


		$query = "DELETE FROM #__hydra_task WHERE task_id IN($sub_tasks)";
		       $database->setQuery($query);
		       $database->query();

        if (strlen($sub_tasks) >= 1 AND ($database->_errorNum)) { $msg = $database->_errorMsg; }


	    hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks', $msg);
	}


	
	/**
	* @author  Tobias Kuhn ( Sat Nov 11 13:29:48 CET 2006 )
	* @name    updateProgress
	* @version 1.1
	* @param   void
	* @return  void
	* @desc    quick-updates the progress of a task
	**/
	function updateProgress()
	{
		global $database, $protect, $hydra;

		$id = intval(mosGetParam($_POST, 'id', 0));

		if (!$id) {
			hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks');
		}

		$progress = intval(mosGetParam($_POST, 'progress', 0));

		// make sure this task belongs to our projects
		if ($protect->my_usertype != 3) {

		   $query = "SELECT project FROM #__hydra_task WHERE task_id = '$id'";
		          $database->setQuery($query);
		          $project = $database->loadResult();

		   if (!in_array($project, $protect->my_projects)) {
		   	  hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks', HL_MSG_HACKER." ".HL_MSG_HACKER_TASK);
		   }
		}

		
		// update the progress
		$query = "UPDATE #__hydra_task SET task_status = '$progress', last_changed = '".time()."' WHERE task_id = '$id'";
		       $database->setQuery($query);
		       $database->query();

		$msg = HL_MSG_PROGRESS_UPDATED;

		if ($database->_errorNum) {
			$msg = HL_MSG_PROGRESS_UPDATED_FAILED;
		}
        
		
		// notify users
		$subject = HL_NOTIFICATION_TASK_UPDATE_EMAIL_SUBJECT;
		
		$query = "SELECT task_name FROM #__hydra_task WHERE task_id = '$id'";
		       $database->setQuery($query);
		       $subject .= " ".$database->loadResult(); 

		$user_info = $hydra->getUserDetails($protect->my_id);

		$subject = str_replace('{user}', $user_info->name, $subject);
		 
		$this->sendTaskNotification($id, $subject);
		
		
		
		hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks', $msg);
	}
	
	
	
	
	/**
	* @author  Tobias Kuhn ( Wed Jan 31 20:29:05 CET 2007 )
	* @name    setTaskNotification
	* @version 1.1
	* @param   int $id
	* @param   int $enable
	* @return  void
	* @desc    enable/disable task notification
	**/
	function setTaskNotification($id, $enable=0)
	{
		global $database, $protect, $hydra_debug;
		
		$limitstart = intval(mosGetParam($_POST, 'limitstart', 0));
		$limit      = intval(mosGetParam($_POST, 'limit', 25));
		
		// get the task name
		$query = "SELECT task_name FROM #__hydra_task WHERE task_id = '$id'";
		       $database->setQuery($query);
		       $task = $database->loadResult();
		       

		// enable or disable?        
		switch ($enable)
		{
			case 0:
				$msg   = $task.": ".HL_MSG_NOTIFICATION_REMOVED;
				$query = "DELETE FROM #__hydra_task_notify WHERE task='$id' AND uid='$protect->my_id'";
				break;
				
			case 1:
				$msg   = $task.": ".HL_MSG_NOTIFICATION_ADD;
				$query = "INSERT INTO #__hydra_task_notify VALUES('$id', '$protect->my_id')";
				break;	
		}
		
		$database->setQuery($query);
		$database->query();
		
		if (intval($database->_errorNum)) { $msg = $database->_errorMsg; }
		
		// redirect
		hydraRedirect("index2.php?option=com_hydra&area=projects&cmd=show_tasks&limitstart=".$limitstart."&limit=".$limit, $msg);
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Thu Feb 01 18:47:14 CET 2007 )
	* @name    sendTaskNotification
	* @version 1.0
	* @param   int $id
	* @param   string $subject
	* @param   int $user
	* @return  void
	* @desc    sends a notification email
	**/
	function sendTaskNotification($id, $subject, $user = 0)
	{
		global $database, $mosConfig_sitename, $mosConfig_mailfrom, $protect, $hydra;
		
		
		// get the email addresses
		switch ($user < 1) {
			case true:
				$query = "SELECT u.email FROM #__users AS u"
		               . "\n INNER JOIN #__hydra_users AS h ON h.jid = u.id"
		               . "\n INNER JOIN #__hydra_task_notify AS n ON n.uid = h.id"
		               . "\n WHERE n.task = '$id'"
		               . "\n AND n.uid != '$protect->my_id'";
		               $database->setQuery($query);
		               $addresses = $database->loadResultArray();
				break;
		
			case false:
				$addresses = $hydra->getUserDetails($user);
				$addresses = array($addresses->email);
				break;
		}
		
		       
        
		// get the task information
		$task = $this->loadTask($id);

		
		// custom status
		$custom_status = '';
		if ($task->task_cstatus) { $custom_status = "[$task->task_cstatus]"; }
		
		
		// time left
		$timeleft = round(($task->end_date - time()) / 86400,0);
		
		
		// get the project name
		$query = "SELECT project_name FROM #__hydra_project WHERE project_id = '$task->project'";
		       $database->setQuery($query);
		       $project_name = $database->loadResult();

		        
		foreach ($addresses AS $key => $address)
		{
			// create the message
			$message = "<strong>".HL_NAME.":</strong> $custom_status $task->task_name <br/>"
			         . "<strong>".HL_PROJECT.":</strong> $project_name <br/>"
			         . "<strong>".HL_PROGRESS.":</strong> $task->task_status% <br/>"
			         . "<strong>".HL_START_DATE.":</strong> ".hydraDate($task->start_date)." <br/>"
			         . "<strong>".HL_END_DATE.":</strong> ".hydraDate($task->end_date)." <br/>"
			         . "<strong>".HL_TIME_LEFT.":</strong> ".$timeleft." ".HL_DAYS." <br/>"
			         . "<br/><strong>".HL_DETAILS.":</strong><br/><br/>"
			         . $task->task_description;
			
			         
			// send email             
			mosmail($mosConfig_mailfrom, $mosConfig_fromname, $address, $subject, $message, 1);
		}

		return;  
	}
	
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 12:02:20 CET 2007 )
	* @name    loadTaskComments
	* @version 1.0
	* @param   int $task_id
	* @return  void
	* @desc    loads the comments from the dabase
	**/
	function loadTaskComments($task_id)
	{
		global $database, $hydra_debug;
		
		$query = "SELECT c.comment_id, c.text, c.task, c.creator, c.cdate, c.mdate, u.name FROM #__hydra_comments AS c"
		       . "\n INNER JOIN #__hydra_users AS h ON h.id = c.creator"
		       . "\n INNER JOIN #__users AS u ON u.id = h.jid"
		       . "\n WHERE area = 'projects'"
		       . "\n AND task = '$task_id'"
		       . "\n ORDER by cdate ASC";
		       $database->setQuery($query);
		       $this->task_comments = $database->loadObjectList();
		       
		if (intval($database->_errorNum)) { $hydra_debug->logError($database->_errorMsg); }       
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 14:23:55 CET 2007 )
	* @name    loadTaskComment
	* @version 1.0
	* @param   int $id
	* @return  object $comment
	* @desc    loads a single comment
	**/
	function loadTaskComment($id)
	{
		global $database, $hydra_debug;
		
		
		$query = "SELECT text FROM #__hydra_comments WHERE comment_id ='$id'";
		       $database->setQuery($query);
		       $database->loadObject($comment);

		if (intval($database->_errorNum)) { $hydra_debug->logError($database->_errorMsg); }    

		 
		return $comment;
	}
	
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 14:23:35 CET 2007 )
	* @name    createComment
	* @version 1.0
	* @param   void
	* @return  void
	* @desc    creates a commenet
	**/
	function createComment()
	{
		global $database, $protect;
		
		$txt  = addslashes ($_POST['text']);
		$task = intval(mosGetParam($_POST, 'task_id', 0));
		$id   = intval(mosGetParam($_POST, 'id', 0));
		$time = time();
		
		switch ($id < 1)
		{
			case true:
				$msg = HL_MSG_COMMENT_CREATED;
				
				$query = "INSERT INTO #__hydra_comments"
		               . "\n VALUES ('','projects','$txt','$protect->my_id','$task','','','$time','$time')";
				break;
				
			case false:
				$msg = HL_MSG_COMMENT_MODIFIED;
				
				$query = "UPDATE #__hydra_comments SET text = '$txt', mdate = '$time'"
				       . "\n WHERE comment_id = '$id'"; 
				break;	
		}
		
		$database->setQuery($query);
		$database->query();
		
		if (intval($database->_errorNum)) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }    
		
		hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=view_comments&id='.$task, $msg);
	}
	
	
	
	function deleteComment()
	{
		global $database, $hydra_debug;
		
		$msg = HL_MSG_COMMENT_DELETED;
		$id  = intval(mosGetParam($_POST, 'id', 0));
		
		
		$query = "DELETE FROM #__hydra_comments WHERE comment_id = '$id'";
		       $database->setQuery($query);
		       $database->query();
		       
		       
		if (intval($database->_errorNum)) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }


		hydraRedirect('index2.php?option=com_hydra&area=projects&cmd=show_tasks', $msg); 
	}
	
	
	
}



class HydraProjectHTML
{

	function dropParentTasks($name, $isset = 0, $id = 0)
	{
		global $database, $protect;

		$my_projects = implode(',', $protect->my_projects);

		$html = "<select name='$name' size='1'><option value='0'>".HL_NO_PARENT."</option>";

		$query = "SELECT t.task_id, t.task_name FROM #__hydra_task AS t, #__hydra_task_map AS m"
		       . "\n WHERE m.parent_task = '0'"
		       . "\n AND m.task = t.task_id"
		       . "\n AND m.task != '$id'"
		       . "\n AND t.task_id != '$id'";
             if ($protect->my_usertype != 3) { $query .= "\n AND t.project IN($my_projects)"; }
		       $query .= "\n ORDER BY t.task_name ASC";
		       $database->setQuery($query);
		       $tasks = $database->loadObjectList();

		for($i = 0, $n = count($tasks); $i < $n; $i++)
		{
			$task = $tasks[$i];

			$selected = '';

			if ($task->task_id == $isset) { $selected = "selected='selected'"; }

			$html .= "<option value='$task->task_id' $selected>$task->task_name</option>";
		}

		return $html;
	}


	function dropProjectFilter()
	{
        global $hydra, $database, $protect, $hydra_sess;

        $my_projects = implode(',',$protect->my_projects);
        $where       = "\n WHERE project_id IN ($my_projects)";
	    $isset       = intval(mosGetParam($_REQUEST, 'pid', $hydra_sess->profile('task_filter_project')));

	    if (isset($isset)) {  $hydra_sess->setProfile('task_filter_project', $isset); }

	    if ($protect->my_usertype == 3) { $where = "\n"; }

	    $query = "SELECT project_id, project_name FROM #__hydra_project"
		       . $where
		       . "\n ORDER BY project_name ASC";
		       $database->setQuery($query);
		       $projectlist = $database->loadObjectList();

	    $list = "\n <select name='pid' size='1'>"
	          . "\n <option value='0'>--".HL_PROJECT."--</option>";

	    for ($i = 0; $i < count($projectlist); $i++)
	    {
		    $p = $projectlist[$i];
		    $selected = '';

		    if ($isset == $p->project_id) { $selected = "selected='selected'"; }

		    $list .= "\n <option value='$p->project_id' $selected>$p->project_name</option>";
	    }

	    $list .= "\n </select>";

	    return $list;
	}


    function dropTaskCreatedByFilter()
	{
		global $hydra, $database, $hydra_sess, $database, $protect;

		$isset = intval(mosGetParam($_REQUEST, 'filter_task_creator', $hydra_sess->profile('filter_task_creator')));
		
	    if (isset($isset)) {  $hydra_sess->setProfile('filter_task_creator', $isset); }

	    
	    $filter = array("--".HL_CREATED_BY."--");

	    // get userlist
	    $userspace = implode(',', $protect->my_userspace);
	    $qfilter   = "\n WHERE h.id IN($userspace)";
	    
	    if( $protect->my_usertype == 3 ) {
            $qfilter = "";
        }
        
        $query = "SELECT u.name, h.id FROM #__users AS u"
               . "\n INNER JOIN #__hydra_users AS h ON h.jid = u.id"
               . $qfilter;
               $database->setQuery($query);
               $userlist = $database->loadObjectList();


        foreach ($userlist AS $user)
        {
        	$filter[$user->id] = $user->name;
        }
          
        
        // build the dropdown list     
	    $list = "\n <select name='filter_task_creator' size='1'>";

         foreach ($filter AS $k => $v)
         {
         	$selected = '';

         	if ($isset == $k) { $selected = "selected='selected'"; }

         	$list .= "\n <option value='$k' $selected>$v</option>";
         }

         $list .= "\n </select>";

         
         return $list;
	}
	
	
	function dropTaskProgressFilter()
	{
         global $hydra, $database, $protect, $hydra_sess;

         $isset = intval(mosGetParam($_REQUEST, 'filter_task', $hydra_sess->profile('task_filter_task')));
	     if (isset($isset)) {  $hydra_sess->setProfile('task_filter_task', $isset); }

         $filter = array("--".HL_PROGRESS."--", HL_FILTER_COMPLETED_TASKS, HL_FILTER_UNCOMPLETED_TASKS);

         $list = "\n <select name='filter_task' size='1'>";

         foreach ($filter AS $k => $v)
         {
         	$selected = '';

         	if ($isset == $k) { $selected = "selected='selected'"; }

         	$list .= "\n <option value='$k' $selected>$v</option>";
         }

         $list .= "\n </select>";

         return $list;
	}


	function dropTaskFilter()
	{
		global $hydra, $database, $hydra_sess, $database, $protect;

		$isset = intval(mosGetParam($_REQUEST, 'filter_task_assigned', $hydra_sess->profile('filter_task_assigned')));
		
	    if (isset($isset)) {  $hydra_sess->setProfile('filter_task_assigned', $isset); }

	    
	    $filter = array("--".HL_TASK_ASSIGNED_TO."--");

	    // get userlist
	    $userspace = implode(',', $protect->my_userspace);
	    $qfilter   = "\n WHERE h.id IN($userspace)";
	    
	    if( $protect->my_usertype == 3) {
            $qfilter = "";
        }
        
        $query = "SELECT u.name, h.id FROM #__users AS u"
               . "\n INNER JOIN #__hydra_users AS h ON h.jid = u.id"
               . $qfilter;
               $database->setQuery($query);
               $userlist = $database->loadObjectList();


        foreach ($userlist AS $user)
        {
        	$filter[$user->id] = $user->name;
        }
          
        
        // build the dropdown list     
	    $list = "\n <select name='filter_task_assigned' size='1'>";

         foreach ($filter AS $k => $v)
         {
         	$selected = '';

         	if ($isset == $k) { $selected = "selected='selected'"; }

         	$list .= "\n <option value='$k' $selected>$v</option>";
         }

         $list .= "\n </select>";

         
         return $list;
	}


	function dropPriority($isset = 0)
	{

       $options = array(HL_TASK_PRIORITY_LOW, HL_TASK_PRIORITY_MED, HL_TASK_PRIORITY_HI);

       $list = "<select name=\"priority\">";

       foreach ($options AS $k => $v)
       {
       	   $selected = "";

       	   if ($isset == $k) { $selected = "selected='selected'"; }

       	   $list .= "\n <option value='$k' $selected>$v</option>";
       }

       return $list;
	}
}
?>