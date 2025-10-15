<?php
/**
* $Id: projects_index.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $database, $hydra, $mainframe, $mosConfig_list_limit, $protect, $hydra_sess;

$sheet = mosGetParam($_REQUEST, 'sheet', '');

require_once($hydra->load('class', 'projects'));


switch($protect->current_command)
{
	
	default:
		
		switch ($sheet)
		{
			// load project details
			case 'true':
				
				echo $hydra->load('js', 'filebrowser');
				mosCommonHTML::loadOverlib();
				$projects = new Projects;
				
				$id      = intval(mosGetParam($_REQUEST, 'id'));
                $data    = $projects->loadProjectSheet($id);
                $project = $data['project'];
                $groups  = $data['groups'];
                $tasks   = $data['tasks'];
                $dirs    = $data['folders'];
                $docs    = $data['docs'];
                $files   = $data['files'];
                $tabs    = new mosTabs(1);
                
		        require_once($hydra->load('html', 'projects_projectsheet'));
		        
				break;
			
				
			// load project list
			default:
				
				mosCommonHTML::loadOverlib();

				// query limit
                $limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
                $limitstart = intval( $mainframe->getUserStateFromRequest( "view{$protect->current_area}{$protect->current_command}limitstart", 'limitstart', 0 ) );

                if ($limit == 0) { $limit = $mosConfig_list_limit; }

                
                // get the project list
                $projects = new Projects;
                $projects->getProjects($limit, $limitstart);
                
                
                // pagination 
                require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
                $pageNav = new mosPageNav( $projects->total_projects, $limitstart, $limit );

                // table sorting
                $th_names  = array (HL_NAME, HL_CHANGE_DATE);
                $querys    = array ('project_name', 'mdate');
                $order_by  = mosGetParam($_POST, 'order_by', $hydra_sess->profile('project_table_order_by', 'project_name', true));
                $order_dir = mosGetParam($_POST, 'order_dir', $hydra_sess->profile('project_table_order_dir', 'ASC', true));

                $hydra_template->initTableOrdering($th_names, $querys, $order_by, $order_dir);
                
                
                // permissions
                $show_tasks  = $protect->perm('show_tasks');
                $new_project = $protect->perm('new_project');
                $my_groups   = implode (',',$protect->my_groups);
                
                
				require_once($hydra->load('html', 'projects_projectlist'));
				
				break;	
		}
 
		break;

		
		
	case 'new_project':
		
		require_once($hydra->load('html', 'projects_new_project'));
		
		break;

		
		
	case 'create_project':
		
		$projects = new Projects;
		$projects->createProject();
		
		break;

		
		
	case 'del_project':
		
	   $projects = new Projects;
		$projects->deleteProject();
		
		break;

		
		
	case 'new_task':
		
	    require_once($hydra->load('html', 'projects_new_task'));
	  
	    break;	

	  
	  
	case 'create_task':
		
		$projects = new Projects;
		$projects->createTask();
		
		break; 

		
		
	case 'show_tasks':
		
		mosCommonHTML::loadOverlib();
		
		switch ($sheet)
		{
			// load the details page
			case 'true':
				require_once($hydra->load('html', 'projects_tasksheet'));
				break;
			
				
			// load the task list		
			default:
				
				$limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
                $limitstart = intval( $mainframe->getUserStateFromRequest( "view{$protect->current_area}{$protect->current_command}limitstart", 'limitstart', 0 ) );
                if ($limit == 0) { $limit = $mosConfig_list_limit; }

                $projects  = new Projects;
                $projects->getTasks($limit, $limitstart, true);
                
                $total         = $projects->total_tasks;
                $notifications = $projects->task_notifications;
                $list          = $projects->tasks;


                // Table ordering
                $th_names = array (HL_NAME, HL_PROGRESS, HL_PROJECT, HL_ASSIGNED_TO);
                $querys   = array ('parent, name', 'parent, t.task_status', 'parent, p.project_name', 'parent, t.uid');

                $order_by  = mosGetParam($_POST, 'order_by', $hydra_sess->profile('task_table_order_by', 'parent, name', true));
                $order_dir = mosGetParam($_POST, 'order_dir', $hydra_sess->profile('task_table_order_dir', 'ASC', true));

                $hydra_template->initTableOrdering($th_names, $querys, $order_by, $order_dir);

                
                // pagination
                require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
                $pageNav = new mosPageNav( $total, $limitstart, $limit );
                
                
                // permissions
                $perm_notification = $protect->perm('task_notification');
                $perm_viewcomments = $protect->perm('view_comments'); 
                $perm_addcomments  = $protect->perm('new_comment');
                
                 
                // load the html file
				require_once($hydra->load('html', 'projects_show_tasks'));
				
				break;	
		}
		 
	    break;

	   
	    
	case 'del_task':
		
		$projects = new Projects;
		$projects->deleteTask();
		 
		break; 

		
		
	case 'update_progress':
		
		$projects = new Projects;
		$projects->updateProgress();
		
		break;	
		
		
	case 'task_notification':

		$id     = intval(mosGetParam($_POST, 'id', 0));
		$enable = intval(mosGetParam($_POST, 'enable', 0));
		
		$projects = new Projects;
		$projects->setTaskNotification($id, $enable);
		
		break;
		
		
	case 'new_comment':
		

         $edit = null;
         $id   = intval(mosGetParam($_REQUEST, 'id', 0));
         $task = intval(mosGetParam($_REQUEST, 'task_id', 0));

         
        if ($id) {
        	$projects = new Projects;
	        $edit = $projects->loadTaskComment($id);
        }
		
		// load the html file
		require_once($hydra->load('html', 'projects_newcomment'));
		
		break;

		
	case 'view_comments':

		$task = intval(mosGetParam($_REQUEST, 'id', 0));
		
		
		// load the comments
		$projects = new Projects();
		$projects->loadTaskComments($task);
		
		
		// permissions
		$perm_delcomment = $protect->perm('del_comment');
		
		
		// load the html file
		require_once($hydra->load('html', 'projects_commentlist'));
		
		break;
		
		
	case 'create_comment':
		
		$projects = new Projects();
		$projects->createComment();
		
		break;
		

	case 'del_comment':
		
		$projects = new Projects();
		$projects->deleteComment();
		
		break;	
			
}
?>

