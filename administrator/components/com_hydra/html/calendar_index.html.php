<?php
/**
* $Id: calendar_index.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $protect;

echo $hydra->load('js', 'calendar');

require_once($hydra->load('class', 'calendar'));

switch ($protect->current_command)
{
	default:
		displayCal();
		break;
		
	case 'new_entry':
		require_once($hydra->load('html', 'calendar_newentry'));
		break;
		
	case 'create_entry':
		$cal = new HydraCalendar();
		$cal->createEvent();
		break;

	case 'del_entry':
		$cal = new HydraCalendar();
		$cal->deleteEvent();
		break;		
}


function displayCal()
{
	global $hydra, $hydra_sess;
	
	$display = mosGetParam($_REQUEST, 'display');
	
	if (isset($display)) {
	   $hydra_sess->setProfile('cal_display', intval(mosGetParam($_REQUEST, 'display',0)));
	}
	
	$display = intval($hydra_sess->profile('cal_display'));
	
	switch ($display)
	{
		case '0':
			$hydra_sess->setProfile('cal_display', '0');
			require_once($hydra->load('html', 'calendar_month'));
			break;
			
		case '1':
			$hydra_sess->setProfile('cal_display', '1');
			require_once($hydra->load('html', 'calendar_week'));
			break;
			
		case '2':
			$hydra_sess->setProfile('cal_display', '2');
			require_once($hydra->load('html', 'calendar_day'));
			break;		
	}
	
}
?>