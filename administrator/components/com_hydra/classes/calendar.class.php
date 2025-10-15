<?php
/**
* $Id: calendar.class.php 16 2007-04-15 12:18:46Z eaxs $
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

class HydraCalendar
{
	var $year;
	var $month;
	var $weeks;
	var $days;
	var $month_start;
	var $today;
	var $day;
	var $display;
	var $events;
	var $sharing_users;
	var $shared;
	
	
	
	/**
	* @author  Tobias Kuhn ( Thu Nov 23 20:38:21 CET 2006 )
	* @name    HydraCalendar
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function HydraCalendar()
	{
		global $hydra_sess, $protect;
		
		$this->year  = intval(mosGetParam($_REQUEST, 'year', date('Y', hydraTime())));
		$this->month = intval(mosGetParam($_REQUEST, 'month', date('n', hydraTime())));
		$this->days  = date('t', mktime(0, 0, 0, $this->month, 1, $this->year));
		$this->weeks = round($this->days / 7);
		
		$this->month_start = date('w',hydraTime(mktime(0, 0, 0, $this->month, 1, $this->year))) - 1;
		$this->today       = date('j', hydraTime());
		$this->day         = intval(mosGetParam($_REQUEST, 'day', $this->today)); 
		
		if ($this->month_start == -1) { $this->month_start = 6; }
		
		$this->display = intval($hydra_sess->profile('cal_display'));
		
		$this->sharing_users = $this->getSharingUsers();
		$this->shared        = intval(mosGetParam($_REQUEST, 'shared', 0));
		
		if (!in_array($this->shared, $protect->my_userspace) AND ($protect->my_usertype != 3)) {
			$this->shared = 0;
		}
		
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Oct 03 12:44:15 CEST 2006 )
	* @name    dropMonths
	* @version 1.0 
	* @param   string $name
	* @param   int $isset
	* @return  string $html
	* @desc    dropdownlist containing all months
	**/
	function dropMonths($name, $isset = '')
	{
		$months = array('1'  => HL_MONTH_JANUARY,
		               '2'  => HL_MONTH_FEBRUARY,
		               '3'  => HL_MONTH_MARCH,
		               '4'  => HL_MONTH_APRIL,
		               '5'  => HL_MONTH_MAY,
		               '6'  => HL_MONTH_JUNE,
		               '7'  => HL_MONTH_JULY,
		               '8'  => HL_MONTH_AUGUST,
		               '9'  => HL_MONTH_SEPTEMBER,
		               '10' => HL_MONTH_OCTOBER,
		               '11' => HL_MONTH_NOVEMBER,
		               '12' => HL_MONTH_DECEMBER
		               );
		               
		$html = "\n <select name='$name' size='1'>";

		if (!$isset) { $isset = $this->month; }
		
		foreach ($months AS $k => $month)
		{
			$selected = "";
			
			if ($isset == $k) { $selected = "selected='selected'"; }
			
			$html .= "\n <option value='$k' $selected>$month</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Oct 03 13:11:56 CEST 2006 )
	* @name    dropDisplay
	* @version 1.0 
	* @param   string $name
	* @return  string $html
	* @desc    dropdownlist to switch display-mode (0 = month, 1 = week, 2 = day)
	**/
	function dropDisplay($name)
	{
		$html = "\n <select name='$name' size='1'>";
		
		$display = array (0 => HL_MONTH, 1 => '', 2 => HL_DAY);
		
		foreach ($display AS $k => $mode)
		{
			$selected = "";
			
			if ($this->display == $k) { $selected = "selected='selected'"; }
			
			if ($mode)
			$html .= "\n <option value='$k' $selected>$mode</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 07 10:54:30 CEST 2006 )
	* @name    dropHours
	* @version 1.0 
	* @param   string $name
	* @param   int $isset
	* @return  string $html
	* @desc    returns a dropdownlist with all hours
	**/
	function dropHours($name, $isset=0)
	{
		$id = intval(mosGetParam($_REQUEST, 'id',0));
		
		$hours = array('00','01','02','03','04','05','06','07','08','09','10','11','12',
		               '13','14','15','16','17','18','19','20','21','22','23');
		               
		$html = "\n <select name='$name' size='1'>";

		if (!$id) { $isset = 6; }
		
		foreach ($hours AS $k => $hour)
		{
			$selected = "";
			
			if ($isset == $k) { $selected = "selected='selected'"; }
			$html .= "\n <option value='$k' $selected>$hour</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 07 10:54:30 CEST 2006 )
	* @name    dropMinutes
	* @version 1.0 
	* @param   string $name
	* @param   int $isset
	* @return  string $html
	* @desc    returns a dropdownlist with all minutes
	**/
	function dropMinutes($name, $isset=0)
	{
		$id = intval(mosGetParam($_REQUEST, 'id',0));
		
		$minutes = array('00','05','10','15','20','25','30','35','40','45','50','55');
		
		$html = "\n <select name='$name' size='1'>";

		if (!$id) { $isset = 6; }
		
		foreach ($minutes AS $k => $minute)
		{
			$selected = "";
			
			if ($isset == $minute) { $selected = "selected='selected'"; }
			$html .= "\n <option value='$minute' $selected>$minute</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	function dropColors($name, $isset = 'ffffff')
	{
		$colors = array('ffffff' => HL_COLOR_WHITE,
		                'ffff99' => HL_COLOR_YELLOW,
		                'ffcc33' => HL_COLOR_ORANGE,
		                'ff6633' => HL_COLOR_RED,
		                '66cc00' => HL_COLOR_GREEN,
		                '3399ff' => HL_COLOR_BLUE,
		                'ff33ff' => HL_COLOR_PURPLE,
		                'ffccff' => HL_COLOR_PINK
		               );
		               
		$html = "\n <select name='$name' size='1'>";

		foreach ($colors AS $k => $name)
		{
			$selected = "";
			
			if ($isset == $k) { $selected = "selected='selected'"; }
			$html .= "\n <option value='$k' $selected>$name</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;               
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 07 18:44:51 CEST 2006 )
	* @name    createEvent
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    creates a new event in the calendar
	**/
	function createEvent()
	{
		global $database, $protect;
		
		$title      = mosGetParam($_POST, 'title', '');
		$details    = addslashes ($_POST['details']);
		$start_date = mosGetParam($_POST, 'start_date','');
		$start_h    = intval(mosGetParam($_POST, 'start_hour', 0));
		$start_m    = intval(mosGetParam($_POST, 'start_minute', 0));
		$end_date   = mosGetParam($_POST, 'end_date','');
		$end_h      = mosGetParam($_POST, 'end_hour', '');
		$end_m      = mosGetParam($_POST, 'end_minute', '');
		$shared     = intval(mosGetParam($_POST, 'shared', 0));
		$color      = mosGetParam($_POST, 'color', 'ffffff');
		$id         = intval(mosGetParam($_POST, 'id', 0));
		 
		if (empty($title) OR (empty($start_date)) OR (empty($end_date))) {
			echo "<script type='text/javascript' language='javascript'>alert('".HL_FORM_ALERT."');history.back();</script>";
			return;
		}
		
		// convert to timestamp
		$start_date = explode('-', $start_date);
		$start_date = mktime($start_h,$start_m,0,$start_date[1],$start_date[2],$start_date[0]);
		
		$end_date = explode('-', $end_date);
		$end_date = mktime($end_h,$end_m,0,$end_date[1],$end_date[2],$end_date[0]);
		
		if ($start_date > $end_date) {
			echo "<script type='text/javascript' language='javascript'>alert(\"".HL_ENDDATE_CONFLICT."\");history.back();</script>";
			return;
		}
		
		$query = "INSERT INTO #__hydra_events VALUES("
		       . "\n '', '$title', '$details','$start_date', '$end_date', '$protect->my_id', '$shared', '$color')";
		       
		if ($id) {
			$query = "UPDATE #__hydra_events SET title = '$title', details = '$details', start_date = '$start_date',"
			       . "\n end_date = '$end_date', shared = '$shared', color = '$color' WHERE event_id = '$id'";
		}
		$database->setQuery($query);
		$database->query();
		       
		hydraRedirect('index2.php?option=com_hydra&area=calendar&day='.$this->day);       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 07 22:47:33 CEST 2006 )
	* @name    countEvents
	* @version 1.0 
	* @param   int $day
	* @return  string $html
	* @desc    shows the amount of events a specific day
	**/
	function countEvents($day)
	{
		global $database, $protect;
		
		$start_time = mktime(0,0,0,$this->month,$day,$this->year);
		$end_time   = mktime(23,55,0,$this->month,$day,$this->year);
		
		$where = "\n WHERE creator = '$protect->my_id'";
		
		if ($this->shared) {
			$where = "\n WHERE creator = '$this->shared' AND shared = '1'";
		}
		
		$query = "SELECT COUNT(event_id) FROM #__hydra_events"
		       . $where
		       . "\n AND start_date BETWEEN $start_time AND $end_time";
		       $database->setQuery($query);
		       $events = intval($database->loadResult());
		       
		if ($events >= 1) {
			if ($events == 1) { return $events." ".HL_EVENT; }
			return $events." ".HL_EVENTS;
		}
		
		return '';
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 15 18:04:33 CEST 2006 )
	* @name    loadEvents
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    loads the events
	**/
	function loadEvents()
	{
		global $database, $protect;
		
		$start_time = mktime(0,0,0,$this->month,$this->day,$this->year);
		$end_time   = mktime(23,55,0,$this->month,$this->day,$this->year);
		
		$where = "\n WHERE creator = '$protect->my_id'";
		
		if ($this->shared) {
			$where = "\n WHERE creator = '$this->shared' AND shared = '1'";
		}
		
		$query = "SELECT event_id, title, details, start_date, end_date, creator, shared, color FROM #__hydra_events"
		       . $where
		       . "\n AND start_date BETWEEN $start_time AND $end_time"
		       . "\n OR end_date BETWEEN $start_time AND $end_time"
		       . "\n GROUP BY event_id";
		       $database->setQuery($query);
		       $this->events = $database->loadObjectList();
	}
	
	
	/**
	* @author  Tobias Kuhn ( Mon Nov 27 21:15:55 CET 2006 )
	* @name    showEvent
	* @version 1.1 
	* @param   int $hour
	* @return  void
	* @desc    shows an event on the timetable
	**/
	function showEvent($hour)
	{		
		global $protect;
		
		$minutes   = array('00','05','10','15','20','25','30','35','40','45','50','55');
		
        echo "\n <tr>"; 
        $html  = '';
        $class = "event_row";
        
		for($i = 0, $n = count($this->events); $i < $n; $i++)
		{
			$event = $this->events[$i];

			$date1 = mktime($hour,0,0,$this->month,$this->day,$this->year);
            $menu  = '';
            $node  = '';
            
			foreach ($minutes AS $k2 => $minute) 
			{
				$date2 = mktime($hour,$minute,0,$this->month,$this->day,$this->year);
                
				if ($event->start_date == $date2) {
					
					$from = date('i', $event->start_date);
					
					// if the event ends today
					if (date('d', $event->end_date) == date('d', mktime(0,0,0,$this->month,$this->day,$this->year))) {
						
					  $to = date('H', $event->end_date).":".date('i', $event->end_date);
					  
					}
					else {
						// if the event lasts longer than today
						$to = date('d m, H:i',$event->end_date);
					}

					
					
					// create context-menu
					$menu .= hydraMenu::init2($event->title, 'menu_'.$hour.$minute);
					$menu .= hydraMenu::menu('menu_'.$hour.$minute);
					if ($this->shared AND ($protect->my_usertype != 3)) {
					   $menu .= hydraMenu::item2('Edit', '16_edit.gif', '');
					   $menu .= hydraMenu::item2('Delete', '16_delete.gif');
					}
					else {
						$menu .= hydraMenu::item(HL_EDIT, '16_edit.gif', '', "editEvent($event->event_id, $this->day)");
					   $menu .= hydraMenu::item(HL_DELETE, '16_delete.gif', '', 'deleteEvent('.$event->event_id.');');
					}
					if ($event->details) { 
					   $menu .= hydraMenu::itemNode(HL_DETAILS, 'menu_dt_'.$hour.$minute, '16_details.gif');
					}
					else {
						$menu .= hydraMenu::item2(HL_DETAILS, '16_details_2.gif');
					}
					$menu .= hydraMenu::menu();
					if ($event->details) {
					   $menu .= hydraMenu::menu('menu_dt_'.$hour.$minute);
					   $menu .= hydraMenu::item3(stripslashes($event->details));
					   $menu .= hydraMenu::menu();
					}   
					
					$html .= "\n <div class='event'>";
					$html .= "\n <div class='minute'>".$from." - ".$to."</div>";
					$html .= "\n <div class='event_title' style='background:#$event->color'>".$menu."</div>";
					$html .= "\n </div>";
					$html .= "\n <br style='clear:both;'/>";
					
                    $class = "event";
				}
				elseif ($event->end_date == $date2) {
                    $class = "event";
				}
			}
			
			if ($event->start_date < $date1 AND ($event->end_date > $date1)) {
				$class = "event";
			}
		}
		echo "\n <td width='5%' class=\"hour\" align=\"center\" valign='top'>$hour</td>";
		echo "\n <td width='95%' class='$class' valign='top'>$html</td>";
		echo "\n </tr>"; 
	}
	
	
	function deleteEvent()
	{
		global $database;
		
		$id = intval(mosGetParam($_POST, 'id', 0));
		
		$query = "DELETE FROM #__hydra_events WHERE event_id = '$id'";
		       $database->setQuery($query);
		       $database->query();
		       
		hydraRedirect('index2.php?option=com_hydra&area=calendar&day='.$this->day);     
	}
	
	
	/**
	* @author  Tobias Kuhn ( Wed Nov 01 11:52:20 CET 2006 )
	* @name    loadEvent
	* @version 1.0 
	* @param   int $id
	* @return  object
	* @desc    loads a single Event
	**/
	function loadEvent($id)
	{
		global $database, $protect;
		
		$where = "\n AND creator = '$protect->my_id'";
		$event = null;
		
		if ($protect->my_usertype == 3) { $where = ''; }
		
		$query = "SELECT * FROM #__hydra_events WHERE event_id = '$id'"
		       . $where;
		       $database->setQuery($query);
		       $database->loadObject($event);  
		            
		return $event;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Wed Nov 08 21:03:05 CET 2006 )
	* @name    getSharingUsers
	* @version 1.0 
	* @param   void
	* @return  array
	* @desc    returns all users which have shared events and are in the users user-space
	**/
	function getSharingUsers()
	{
		global $database, $protect;
		
		$userspace = implode(',',$protect->my_userspace);
		$userspace = "\n AND creator IN($userspace)";
		
		if ($protect->my_usertype == 3) { $userspace = "\n "; }
		
		$query = "SELECT creator FROM #__hydra_events"
		       . "\n WHERE shared = '1'"
		       . $userspace
		       . "\n AND creator != '$protect->my_id'"
		       . "\n GROUP BY creator";
		       $database->setQuery($query);
		       
		return $database->loadResultArray();        
	}
	
	
	/**
	* @author  Tobias Kuhn ( Wed Nov 08 21:20:51 CET 2006 )
	* @name    showSharedList
	* @version 1.0 
	* @param   string $name
	* @param   int $isset (optional)
	* @return  string
	* @desc    returns a select-list with all sharing users
	**/
	function showSharedList($name, $isset = 0)
	{
		global $hydra;
		
		if (count($this->sharing_users) < 1) {
			return "&nbsp;";
		}
		
		$html = HL_VIEW_SHARED."<select name='$name' size='1'>";
		$html .= "\n <option value='0'>".HL_NO_USER_SELECTED."</option>";
		foreach ($this->sharing_users AS $key => $id)
		{
			$user = $hydra->getUserDetails($id);
			
			$selected = "";
			
			if ($isset == $id) { $selected = "selected='selected'"; }
			
			$html .= "\n <option value='$id' $selected>$user->name</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
}
?>