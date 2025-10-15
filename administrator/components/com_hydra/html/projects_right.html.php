<?php
/**
* $Id: projects_right.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $hydra_template, $protect;


switch($protect->current_command)
{
	default:
		rightDefault($hydra, $hydra_template, $protect);
		break;

		
	case 'new_project':
		rightNewProject($hydra, $hydra_template, $protect);
		break;

		
	case 'new_task':
		rightNewTask($hydra, $hydra_template, $protect);
		break;

		
	case 'show_tasks':
	   rightShowTasks($hydra, $hydra_template, $protect);
	   break;

	   
	case 'new_comment':
		rightNewComment($hydra, $hydra_template, $protect);
		break;

		
	case 'view_comments':
		rightViewComment($hydra, $hydra_template, $protect);
		break;   			
}


function rightDefault($hydra, $hydra_template, $protect)
{
	$id    = intval(mosGetParam($_REQUEST, 'id'));
	$sheet = mosGetParam($_REQUEST, 'sheet', '');
	
	$body = '';
	$body .= $protect->perm('new_project', $hydra_template->drawIcon(HL_NEW_PROJECT, '32_projects_newproject.gif', 'area=projects&cmd=new_project'));
    $body .= $protect->perm('del_project', $hydra_template->drawIcon(HL_DEL_PROJECT, '32_projects_delproject.gif', '', 'validateDelete()'));
   
    if (count($protect->my_projects) >= 1) {
       $body .= $protect->perm('show_tasks', $hydra_template->drawIcon(HL_SHOW_TASKS, '32_projects_task.gif', 'area=projects&cmd=show_tasks'));
       $body .= $protect->perm('new_task', $hydra_template->drawIcon(HL_NEW_TASK, '32_projects_newtask.gif', 'area=projects&cmd=new_task'));
    }
      
    $body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel');
      

    if ($sheet == 'true') {
       $body = '';
       $body .= $protect->perm('*projects', $hydra_template->drawIcon(HL_PRINT, '32_print.gif', '', "printProject($id)"));	
       $body .= $protect->perm('new_project', $hydra_template->drawIcon(HL_EDIT, '32_projects_newproject.gif', 'area=projects&cmd=new_project&id='.$id));	
       $body .= $protect->perm('del_project', $hydra_template->drawIcon(HL_DEL_PROJECT,'32_projects_delproject.gif', '', 'validateDelete()'));
	   $body .= $protect->perm('*projects', $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=projects'));	
    }
	
    if ($protect->perm('*projects')) {
	   echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }  
}


function rightNewProject($hydra, $hydra_template, $protect)
{
	$body = '';
	$lang = HL_CREATE_PROJECT;
	$id   = intval(mosGetParam($_REQUEST, 'id'));
	
	if ($id >= 1) { $lang = HL_EDIT_PROJECT; }
	   $body .= $protect->perm('create_project', $hydra_template->drawIcon($lang, '32_submit.gif', '', 'validateCreate()'));
       $body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=projects');
	
    if ($protect->perm('new_project')) {
	   echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }  
}


function rightNewTask($hydra, $hydra_template, $protect)
{
	$lang = HL_CREATE_TASK;
	
	$id = intval(mosGetParam($_REQUEST, 'id', 0));
	
	if ($id >= 1) { $lang = HL_EDIT_TASK; }
	$body = '';
	$body .= $protect->perm('create_task', $hydra_template->drawIcon($lang, '32_submit.gif', '', 'validateCreate()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=projects&cmd=show_tasks');
	
	if ($protect->perm('new_task')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}  
}


function rightShowTasks($hydra, $hydra_template, $protect)
{
	$id    = intval(mosGetParam($_REQUEST, 'id', 0));
	$sheet = mosGetParam($_REQUEST, 'sheet', '');
	
	
	$body = '';
	$body .= $protect->perm('new_task', $hydra_template->drawIcon(HL_NEW_TASK, '32_projects_newtask.gif', 'area=projects&cmd=new_task'));
	$body .= $protect->perm('del_task', $hydra_template->drawIcon(HL_DELETE_TASK, '32_projects_deltask.gif', '', 'validateDelete()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=projects');
	
	if ($sheet == 'true') {
		$body = '';
		$body .= $protect->perm('*projects', $hydra_template->drawIcon(HL_PRINT, '32_print.gif', '', "printTask($id)"));
		$body .= $protect->perm('new_task', $hydra_template->drawIcon(HL_EDIT, '32_projects_newtask.gif', 'area=projects&cmd=new_task&id='.$id));
		$body .= $protect->perm('new_comment', $hydra_template->drawIcon(HL_NEW_COMMENT, '32_comment.gif', 'area=projects&cmd=new_comment&task_id='.$id ));
		$body .= $protect->perm('del_task', $hydra_template->drawIcon(HL_DELETE_TASK, '32_projects_deltask.gif', '', 'validateDelete()'));
		$body .= $hydra_template->drawIcon(HL_GO_BACK,'32_back.gif', 'area=projects&cmd=show_tasks');
	}
	
	if ($protect->perm('show_tasks')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}
}


function rightNewComment($hydra, $hydra_template, $protect)
{
	$id   = intval(mosGetParam($_REQUEST, 'id', 0));
	$task = intval(mosGetParam($_REQUEST, 'id', 0));
	
	$body = '';
	
	$body .= $protect->perm('create_comment', $hydra_template->drawIcon(HL_CREATE_COMMENT, '32_comment.gif', '', 'document.adminForm.submit()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK,'32_back.gif', 'area=projects&cmd=show_tasks');
	
	if ($protect->perm('create_comment')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}
}


function rightViewComment($hydra, $hydra_template, $protect)
{
	$task = intval(mosGetParam($_REQUEST, 'id', 0));
	
	$body = '';
	
	$body .= $protect->perm('new_comment', $hydra_template->drawIcon(HL_NEW_COMMENT, '32_comment.gif', 'area=projects&cmd=new_comment&task_id='.$task ));
	$body .= $hydra_template->drawIcon(HL_GO_BACK,'32_back.gif', 'area=projects&cmd=show_tasks');
	
	if ($protect->perm('view_comments')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}
}
?>