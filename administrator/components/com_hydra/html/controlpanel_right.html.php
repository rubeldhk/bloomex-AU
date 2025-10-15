<?php
/**
* $Id: controlpanel_right.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $hydra, $protect;

switch($protect->current_command)
{
	default:
		rightDefault($hydra_template, $hydra, $protect);
		break;
		
	case 'show_usergroups':
		rightShowUsergroups($hydra_template, $hydra, $protect);
		break;
		
	case 'new_usergroup':
		rightNewUserGroup($hydra_template, $hydra, $protect);
		break;	
		
	case 'show_users':
		rightShowUsers($hydra_template, $hydra, $protect);
		break;
		
	case 'show_joomlausers':
		rightShowJoomlaUsers($hydra_template, $hydra, $protect);
		break;

	case 'setup_import':
		rightSetupImport($hydra_template, $hydra, $protect);
		break;
		
	case 'show_settings':
		rightShowSettings($hydra_template, $hydra, $protect);
		break;

	case 'profile':
		rightProfile($hydra_template, $hydra, $protect);
		break;

	case 'edit_registry':
		rightRegistry($hydra_template, $hydra, $protect);
		break;		
}

function rightDefault($hydra_template, $hydra, $protect)
{
   $body  = $protect->perm('profile', $hydra_template->drawIcon(HL_MY_PROFILE, '32_controlpanel_profile.gif', 'area=controlpanel&cmd=profile'));
   $body .= $protect->perm('show_settings', $hydra_template->drawIcon(HL_SYSTEM_SETTINGS, '32_controlpanel_settings.gif', 'area=controlpanel&cmd=show_settings'));
	
   
   if ($protect->perm('profile') OR ($protect->perm('show_settings'))) {
      echo $hydra_template->drawBox(HL_SETTINGS_BOX, $body);
   }
   	
   
   $body = '';
	
   $body .= $protect->perm('show_usergroups', $hydra_template->drawIcon(HL_USER_GROUPS,'32_groups.gif', 'area=controlpanel&cmd=show_usergroups'));
   $body .= $protect->perm('show_users', $hydra_template->drawIcon(HL_USERS, '32_user.gif', 'area=controlpanel&cmd=show_users'));
   
   
   if ($protect->perm('show_usergroups') OR ($protect->perm('show_users'))) {
     echo $hydra_template->drawBox(HL_USERS_MANAGEMENT, $body);
   }  	
  
}

function rightShowUsergroups($hydra_template, $hydra, $protect)
{
	$body = '';
   $body .= $protect->perm('new_usergroup', $hydra_template->drawIcon(HL_NEW_USERGROUP, '32_controlpanel_newgroup.gif', 'area=controlpanel&cmd=new_usergroup'));
   $body .= $protect->perm('del_usergroup', $hydra_template->drawIcon(HL_DELETE_USERGROUPS, '32_controlpanel_delgroup.gif', '', 'validateDelete()'));
   $body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel');
   
	
   if ($protect->perm('show_usergroups')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
   }  	
}

function rightNewUserGroup($hydra_template, $hydra, $protect)
{
	$id = intval(mosGetParam($_REQUEST, 'id', 0));	
	$lang = HL_CREATE_USERGROUP;
	
	if ($id) { $lang = HL_EDIT_USERGROUP; }
	
	$body = '';
   $body .= $hydra_template->drawIcon($lang, '32_submit.gif', '', 'validateGroup()');
   $body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel&cmd=show_usergroups');
	
   if ($protect->perm('new_usergroup')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
   }  	
}


function rightShowUsers($hydra_template, $hydra, $protect)
{
	$body = '';
	$body .= $protect->perm('show_joomlausers', $hydra_template->drawIcon(HL_IMPORT_JOOMLA_USERS, '32_controlpanel_import.gif', 'area=controlpanel&cmd=show_joomlausers'));
	$body .= $protect->perm('del_users',$hydra_template->drawIcon(HL_DELETE_USERS, '32_controlpanel_deluser.gif', '', 'validateDelete()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel');
	
	if ($protect->perm('show_users')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}  
}


function rightShowJoomlaUsers($hydra_template, $hydra, $protect)
{
	$body = '';
	$body .= $protect->perm('setup_import', $hydra_template->drawIcon(HL_IMPORT_SELECTED,'32_controlpanel_import.gif', '', 'validateImport()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel&cmd=show_users');
	
	if ($protect->perm('show_joomlausers')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}  
}

function rightSetupImport($hydra_template, $hydra, $protect)
{
	$body = '';
	$body .= $protect->perm('import_users', $hydra_template->drawIcon(HL_IMPORT, '32_controlpanel_import.gif', '', 'document.adminForm.submit()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel&cmd=show_joomlausers');
	
	if ($protect->perm('setup_import')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}  
}


function rightShowSettings($hydra_template, $hydra, $protect)
{
	$body = '';
	$body .= $protect->perm('edit_settings', $hydra_template->drawIcon(HL_SAVE_SETTINGS, '32_submit.gif', '', 'document.adminForm.submit()'));
	$body .= $protect->perm('edit_registry', $hydra_template->drawIcon(HL_EDIT_REGISTRY, '32_registry.gif', '', 'showRegistry()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel');
	
	if ($protect->perm('show_settings')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	} 
}


function rightProfile($hydra_template, $hydra, $protect)
{
	$body = '';
	$body .= $protect->perm('update_profile', $hydra_template->drawIcon(HL_SAVE_SETTINGS, '32_submit.gif', '', 'document.adminForm.submit()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel');
	
	if ($protect->perm('profile')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}
}


function rightRegistry($hydra_template, $hydra, $protect)
{
	$body = '';
	$body .= $protect->perm('update_registry', $hydra_template->drawIcon(HL_UPDATE_REGISTRY, '32_submit.gif', '', 'updateRegistry()'));
	$body .= $protect->perm('update_registry', $hydra_template->drawIcon(HL_REG_DEL_ENTRIES, '32_delete.gif', '', 'deleteRegistry()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK,'32_back.gif', 'area=controlpanel&cmd=show_settings');
	
	if ($protect->perm('edit_registry')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
	}
}
?>