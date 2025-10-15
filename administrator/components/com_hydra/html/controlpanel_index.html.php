<?php
/**
* $Id: controlpanel_index.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra;

require_once($hydra->load('class', 'controlpanel'));

switch($protect->current_command)
{
	default:
		require_once($hydra->load('html', 'controlpanel_overview'));
		break;
		
	case 'show_usergroups':		
	  require_once($hydra->load('html', 'controlpanel_groups'));
	  break; 

	case 'new_usergroup':
		require_once($hydra->load('html', 'controlpanel_new_group'));
		break;

	case 'create_usergroup':
		$controlpanel = new Controlpanel;
		$controlpanel->createUsergroup();
		break;

	case 'del_usergroup':
		$controlpanel = new Controlpanel;
		$controlpanel->deleteUsergroup();
		break;

	case 'show_users':
	  require_once($hydra->load('html', 'controlpanel_users'));
	  break;	
	  
	case 'del_users':
	  $controlpanel = new Controlpanel;
	  $controlpanel->deleteUsers();
	  break;

	case 'show_joomlausers':
		require_once($hydra->load('html', 'controlpanel_joomlausers'));
		break;

	case 'setup_import':
	   require_once($hydra->load('html', 'controlpanel_importsetup'));
	   break;
	   
	case 'import_users':
	   $controlpanel = new Controlpanel;
		$controlpanel->importUsers();
		break;

	case 'show_settings':
		require_once($hydra->load('html', 'controlpanel_settings'));
	   break;	
	   
	case 'edit_settings':
		$controlpanel = new Controlpanel;
		$controlpanel->saveSettings();
		break;

	case 'profile':
		require_once($hydra->load('html','controlpanel_profile'));
		break;
		
	case 'update_profile':
		$controlpanel = new Controlpanel;
		$controlpanel->updateProfile();
		break;

	case 'change_usertype':
		$controlpanel = new Controlpanel;
		$controlpanel->changeUsertype();
		break;

	case 'edit_registry':
		require_once($hydra->load('html', 'controlpanel_registry'));
		break;	
		
	case 'update_registry':
		$hydra->updateRegistry();
		break;	
		
	case 'add_registry':
		$hydra->addRegistry();
		break;

	case 'del_registry':
		$hydra->delRegistry();
		break;
		
	case 'del_lang':
		$hydra->delLanguage();
		break;

	case 'del_theme':
		$hydra->delTheme();
		break;		
}
?>