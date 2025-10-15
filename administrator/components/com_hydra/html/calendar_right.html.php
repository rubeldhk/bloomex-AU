<?php
/**
* $Id: calendar_right.html.php 16 2007-04-15 12:18:46Z eaxs $
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
		
	case 'new_entry':
		rightNewEntry($hydra_template, $hydra, $protect);
		break;	
		
}

function rightDefault($hydra_template, $hydra, $protect)
{  
	 $body = "";
	 
	 $body .= $protect->perm('new_entry', $hydra_template->drawIcon(HL_NEW_EVENT,'32_calendar_newentry.gif', 'area=calendar&cmd=new_entry'));
	 $body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=controlpanel');
   
    echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body); 
}


function rightNewEntry($hydra_template, $hydra, $protect)
{
	 $id = intval(mosGetParam($_REQUEST, 'id', 0));
	 
	 $body = "";
	 
	 if (!$id) { 
	    $body .= $protect->perm('create_entry', $hydra_template->drawIcon(HL_CREATE_EVENT,'32_submit.gif', '', 'validateCreate()'));
	 }
	 else {
	 	$body .= $protect->perm('create_entry', $hydra_template->drawIcon(HL_UPDATE_EVENT, '32_submit.gif', '', 'validateCreate()'));
	 }
	 
	 $body .= $hydra_template->drawIcon(HL_ABORT,'32_abort.gif', 'area=calendar');
   
    echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
}
?>