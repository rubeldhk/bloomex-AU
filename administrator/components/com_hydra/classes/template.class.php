<?php
/**
* $Id: template.class.php 26 2007-04-16 17:19:37Z eaxs $
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

class HydraTemplate
{	
	var $table_order_names;
	
	var $table_order_querys;
	
	var $table_order_by;
	
	var $table_order_dir;
	
	var $print_page;
	
	/**
	* @author  Tobias Kuhn ( Wed Dec 13 23:14:27 CET 2006 )
	* @name    HydraTemplate
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    constructor - not much to see there :D
	**/
	function HydraTemplate()
	{
      global $hydra_debug;
      
      $this->print_page = intval( mosGetParam($_REQUEST, 'print_page', 0) );
      
      $hydra_debug->logNotice('Including File: ['.__FILE__.']');
	}
	
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Dec 17 15:18:26 CET 2006 )
	* @name    drawForm
	* @version 1.1 
	* @param   string $name
	* @param   string $action
	* @param   bool $end 
	* @return  string $form
	* @desc    
	**/
	function drawForm($name = '', $action = 'index2.php', $end = false)
	{
	   global $hydra, $protect, $hydra_cfg;
	   	
	   if ($end) { return "\n </form>"; }
	   
	   if (!$hydra->backend) {
	   	
	   	  // wrapped $action into sefRelToAbs to fix openSEF problems
	   	  if (!$hydra_cfg->settings->raw_output) {
	   	     $action = sefRelToAbs(str_replace('index2.php', 'index.php', $action));
	   	  } 
	   	    
	   	  $action = sefRelToAbs(str_replace('index3.php', 'index2.php', $action));
	   	
	   }
	   
	   $enctype = "";
	   
	   if ($protect->current_command == 'create_files' AND ($protect->current_area == 'files')) {
	   	  $enctype = "enctype='multipart/form-data'";
	   }
	   
	   if ($protect->current_command == 'show_settings' AND ($protect->current_area == 'controlpanel')) {
	   	  $enctype = "enctype='multipart/form-data'";
	   }
	   
	   $form = "\n <form name='$name' action='$action' method='post' $enctype>";
	   
	   return $form;
	}
	

	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 12:23:11 CEST 2006 )
	* @name    drawInput
	* @version 1.0 
	* @param   string $type
	* @param   string $name
	* @param   string $value
	* @param   string $id
	* @param   string $params
	* @return  string $input
	* @desc    creates an input-field
	**/
	function drawInput($type, $name, $value = '', $id = '', $params = '')
	{
		if ($id) { $id = "id='$id'"; }
		
		$input = "\n <input type='$type' name='$name' value='$value' $id class='formInput' $params/>";
		
		return $input;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 12:22:40 CEST 2006 )
	* @name    drawLabel
	* @version 1.0 
	* @param   string $text
	* @param   string $id
	* @return  string $label
	* @desc    
	**/
	function drawLabel($text, $id = false)
	{
		if ($id) { $id = "id='$id'"; }
		
		$label = "\n <span class='formLabel' $id>$text</span>";
		
		return $label;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 12:22:04 CEST 2006 )
	* @name    drawDesc
	* @version 1.0 
	* @param   string $text
	* @param   string $id
	* @return  string $label
	* @desc    
	**/
	function drawDesc($text, $id = false)
	{
		if ($id) { $id = "id='$id'"; }
		
		$label = "\n <span class='formDesc' $id>$text</span>";
		
		return $label;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Mon Dec 25 14:51:31 CET 2006 )
	* @name    drawInterface
	* @version 1.3 
	* @param   string $position
	* @return  unknown
	* @desc    includes the main-files of an area
	**/
	function drawInterface($position = 'index')
	{
		global $protect, $hydra_cfg, $hydra_debug, $hydra, $hydra_sess;
		
		$file = $hydra_cfg->hydra_path.'/html/'.$protect->current_area.'_'.$position.'.html.php';
		
		// load the print css if necessary
		if ($this->print_page) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$hydra_cfg->hydra_url/themes/".$hydra_sess->profile['theme']."/css/print.css\" />";
		}
		
		
		if (file_exists($file)) {

		  	 $hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Included file: ['.$file.']');
		  	 require_once($file);

		    return true;
		}
		
		if ($position == 'hydra_msg') {
			
			if ($hydra->_message) {
				echo "<div class='hydra_msg-new'>".$hydra->systemMessage()."</div>";
			}
			else {
				echo "<div class='hydra_msg'>".$hydra->systemMessage()."</div>";
			}
			
			
			return true;
		}
		
		
		$hydra_debug->logError(__CLASS__.'::'.__FUNCTION__.' - File does not exists: ['.$file.']');
		
		return false;  	
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Thu Nov 23 20:11:22 CET 2006 )
	* @name    drawBox
	* @version 1.1
	* @param   string $head_content (optional)
	* @param   string $body_content (optional)
	* @param   string $footer_content (optional)
	* @param   string $class (optional)
	* @return  string $container
	* @desc    returns multiple div-boxes - comparable to a "module"
	**/
	function drawBox($head_content = '', $body_content = '', $footer_content = '', $style = '1', $class='')
	{
		switch ($style) 
		{
			case '1':
			   $head      = "\n <div class='box_header$class'>".$head_content."</div>";
		       $body      = "\n <div class='box_body$class'>".$body_content."</div>";
		       $footer    = "\n <div class='box_footer$class'>".$footer_content."</div>";
		       $container = "\n <div class='box$class'>".$head.$body.$footer." \n </div>";
		       break;
		      
			case '2':
               $container = "\n <table class='box$class' cellpadding='0' cellspacing='0' border='0'>"
                          . "\n <tr>"
                          . $body_content
                          . "\n </tr>"
                          . "\n </table>";
				break;  	
		}
		   
		
		return $container;
	}
	
	
	/**
	* @author  Tobias Kuhn, Giller ( Thu Nov 23 21:09:45 CET 2006 )
	* @name    drawIcon
	* @version 1.1
	* @param   string $name (optional)
	* @param   string $img (optional)
	* @param   string $link (optional)
	* @param   string $js (optional)
	* @return  string $icon
	* @desc    returns a button/icon
	**/
	function drawIcon($name = '', $img = '', $link = '', $js = '')
	{
		global $hydra;

		if ($img) {
		  $img = "\n <img alt='".$name."' title='".$name."' src='".$hydra->load('img_url', $img)."' border='0'/>";	
		}
		
		if ($link) {
			$link = "\n <a title='".$name."' href='".$hydra->link($link)."'>".$img."<br/>".$name."</a>";
			$js = '';
		}
		
		if ($js) {
			$link = "\n <a title='".$name."' href='javascript:".$js.";' style='cursor:pointer'>".$img."<br/>".$name."</a>";
			$prop['link'] = '';
		}
		
		$icon = "\n <div class='icon' align='center'>".$link."\n </div>";
		
		return $icon;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 12:18:33 CEST 2006 )
	* @name    drawInfo
	* @version 1.0 
	* @param   string $text
	* @return  string $info
	* @desc    
	**/
	function drawInfo($text)
	{
		$info = "\n <span class='info'>$text</span>";
		
		return $info;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Dec 31 20:40:23 CET 2006 )
	* @name    drawNavigation
	* @version 1.2 
	* @param   void
	* @return  string
	* @desc    returns a nav-box
	**/
	function drawNavigation($style = '1', $class='')
	{
		global $protect, $hydra, $hydra_sess, $hydra_cfg;
		
		
		$body = '';
      
		if ($hydra_sess->profile('nav_controlpanel', 1, true)) { 
			
	      $body .= $protect->perm('*controlpanel', "\n <a href='".$hydra->link('area=controlpanel')."' class='boxButton$class'>".$hydra->load('img', '16_controlpanel.gif', "alt='".HL_CONTROLPANEL."' align='left'")."&nbsp;&nbsp;".HL_CONTROLPANEL."</a>");
	      
		}
		
		
		if ($hydra_sess->profile('nav_projects', 1, true)) {
			
	      $body .= $protect->perm('*projects', "\n <a href='".$hydra->link('area=projects')."' class='boxButton$class'>".$hydra->load('img', '16_projects.gif', "alt='".HL_PROJECTS."' align='left'")."&nbsp;&nbsp;".HL_PROJECTS."</a>");
	      
		}
		
		
		if ($hydra_sess->profile('nav_tasks', 1, true) AND(count($protect->my_projects) >= 1)) { 
			  
	      $body .= $protect->perm('projects->show_tasks', "\n <a href='".$hydra->link("area=projects&cmd=show_tasks")."' class='boxButton$class'>".$hydra->load('img', '16_tasks.gif', "alt='".HL_TASKS."' align='left'")."&nbsp;&nbsp;".HL_TASKS."</a>");
	      
		}
		
		
		if ($hydra_sess->profile('nav_files', 1, true)) { 
	      $body .= $protect->perm('*files', "\n <a href='".$hydra->link('area=files')."' class='boxButton$class'>".$hydra->load('img', '16_filemanager.gif', "alt='".HL_FILES."' align='left'")."&nbsp;&nbsp;".HL_FILES."</a>");
		}
		
		
		if ($hydra_sess->profile('nav_calendar', 1, true)) {  
	      $body .= $protect->perm('*calendar', "\n <a href='".$hydra->link('area=calendar')."' class='boxButton$class'>".$hydra->load('img', '16_calendar.gif', "alt='".HL_CALENDAR."' align='left'")."&nbsp;&nbsp;".HL_CALENDAR."</a>");
		}
		
		
	    if ($protect->current_area != 'controlpanel') {
	   	
	   	   if ($hydra_sess->profile('nav_profile', 1, true)) {
              $body .= $protect->perm('controlpanel->profile', "\n <a href='".$hydra->link('area=controlpanel&cmd=profile')."' class='boxButton$class'>".$hydra->load('img', '16_profile.gif', "alt='".HL_MY_PROFILE."' align='left'")."&nbsp;&nbsp;".HL_MY_PROFILE."</a>");
	   	   }
	   	   
	   	   if ($hydra_sess->profile('nav_usergroups', 1, true)) {  
              $body .= $protect->perm('controlpanel->show_usergroups', "\n <a href='".$hydra->link('area=controlpanel&cmd=show_usergroups')."' class='boxButton$class'>".$hydra->load('img', '16_group.gif', "alt='".HL_USER_GROUPS."' align='left'")."&nbsp;&nbsp;".HL_USER_GROUPS."</a>");
	   	   }
	   	      
	    }
	    
	    if ($hydra->raw_output AND(!$hydra->backend)) {
	    	$body .= "\n <a href='".sefRelToAbs($hydra_cfg->site_url)."' class='boxButton$class'>".$hydra->load('img', '16_home.gif', "alt='".HL_HOMEPAGE."' align='left'")."&nbsp;&nbsp;".HL_HOMEPAGE."</a>";
	    }
	      
	    if ($style == '2') {
	   	
	   	   $body = str_replace('<a', '<td><a', $body);
	   	   $body = str_replace('a>', 'a></td>', $body);
	   	   
	    }
	   
	    return $this->drawBox(HL_NAV_BOX, $body, '', '2', $class);
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 16:29:03 CET 2007 )
	* @name    dropLanguages
	* @version 1.1
	* @param   string $name
	* @param   string $isset
	* @return  string $html
	* @desc    returns a dropdown-list with all registered languages
	**/
	function dropLanguages($name, $isset = '')
	{
		global $database;
		
		$query = "SELECT name, label FROM #__hydra_language";
		       $database->setQuery($query);
		       $languages = $database->loadAssocList();
		       
		$html = "\n <select name='$name'>"; 

		foreach ($languages AS $k => $v)
		{
			$selected = '';
			
			switch ($isset == $v['name'])
			{
				case true:
					$selected = "selected='selected'";
					break;
					
				case false:
					$selected = '';
					break;	
			}
			
			$html .= "\n <option value='".$v['name']."' $selected>".$v['label']."</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 16:28:54 CET 2007 )
	* @name    dropThemes
	* @version 1.1
	* @param   string $name
	* @param   string $isset
	* @return  string $html
	* @desc    returns a dropdown-list with all registered themes
	**/
	function dropThemes($name, $isset = '')
	{
		global $database;
		
		$query = "SELECT name, label FROM #__hydra_theme";
		       $database->setQuery($query);
		       $themes = $database->loadAssocList();
		       
		$html = "\n <select name='$name'>"; 

		foreach ($themes AS $k => $v)
		{
			
			switch ($isset == $v['name'])
			{
				case true:
					$selected = "selected='selected'";
					break;
					
				case false:
					$selected = '';
					break;	
			}
			
			$html .= "\n <option value='".$v['name']."' $selected>".$v['label']."</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 16:29:50 CET 2007 )
	* @name    dropProgress
	* @version 1.1
	* @param   string $name
	* @param   string $isset
	* @return  string $html
	* @desc    
	**/
	function dropProgress($name, $isset = '')
	{		
		$array = array('0', '5', '10', '15', '20', '25', '30', '35', '40', '45', '50', 
		               '55', '60', '65', '70', '75', '80', '85', '90', '95', '100');
		               
		$html = "\n <select name='$name'>"; 

		foreach ($array AS $k => $v)
		{
			
			switch ($isset == $v)
			{
				case true:
					$selected = "selected='selected'";
					break;
					
				case false:
					$selected = '';
					break;	
			}

			$html .= "\n <option value='".$v."' $selected>".$v." %</option>";
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Sep 30 14:30:26 CEST 2006 )
	* @name    dropTimeOffset
	* @version 1.0 
	* @param   string $name
	* @param   string $isset
	* @return  string $html
	* @desc    returns a list of all time-zones
	**/
	function dropTimeOffset($name, $isset = false)
	{
	   global $mosConfig_offset_user;
	   
	   if (!$isset) { $isset = $mosConfig_offset_user; }	
	
		$list = array('-12'  => '(UTC -12:00) International Date Line West',
		              '-11'  => '(UTC -11:00) Midway Island, Samoa',
		              '-10'  => '(UTC -10:00) Hawaii',
		              '-9.5' => '(UTC -09:30) Taiohae, Marquesas Islands',
		              '-9'   => '(UTC -09:00) Alaska',
		              '-8'   => '(UTC -08:00) Pacific Time (US &amp; Canada)',
		              '-7'   => '(UTC -07:00) Mountain Time (US &amp; Canada)',
		              '-6'   => '(UTC -06:00) Central Time (US &amp; Canada), Mexico City',
		              '-5'   => '(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima',
		              '-4'   => '(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz',
		              '-3.5' => '(UTC -03:30) St. John`s, Newfoundland and Labrador',
		              '-3'   => '(UTC -03:00) Brazil, Buenos Aires, Georgetown',
		              '-2'   => '(UTC -02:00) Mid-Atlantic',
		              '-1'   => '(UTC -01:00 hour) Azores, Cape Verde Islands',
		              '0'    => '(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca',
		              '1'    => '(UTC +01:00 hour) Berlin, Brussels, Copenhagen, Madrid, Paris',
		              '2'    => '(UTC +02:00) Kaliningrad, South Africa',
		              '3'    => '(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg',
		              '3.5'  => '(UTC +03:30) Tehran',
		              '4'    => '(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi',
		              '4.5'  => '(UTC +04:30) Kabul',
		              '5.0'  => '(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
		              '5.5'  => '(UTC +05:30) Bombay, Calcutta, Madras, New Delhi',
		              '6'    => '(UTC +06:00) Almaty, Dhaka, Colombo',
		              '6.30' => '(UTC +06:30) Yagoon',
		              '7'    => '(UTC +07:00) Bangkok, Hanoi, Jakarta',
		              '8'    => '(UTC +08:00) Beijing, Perth, Singapore, Hong Kong',
		              '8.75' => '(UTC +08:00) Western Australia',
		              '9'    => '(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
		              '9.5'  => '(UTC +09:30) Adelaide, Darwin, Yakutsk',
		              '10'   => '(UTC +10:00) Eastern Australia, Guam, Vladivostok',
		              '10.5' => '(UTC +10:30) Lord Howe Island (Australia)',
		              '11'   => '(UTC +11:00) Magadan, Solomon Islands, New Caledonia',
		              '11.30'=> '(UTC +11:30) Norfolk Island',
		              '12'   => '(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka',
		              '12.75'=> '(UTC +12:45) Chatham Island',
		              '13'   => '(UTC +13:00) Tonga',
		              '14'   => '(UTC +14:00) Kiribati'
		             );
	
	   $html = "\n <select name='".$name."' size='1'>";

	   foreach ($list AS $offset => $name)
	   {
		   $selected = '';
		
		   if ($offset == $isset) { $selected = "selected='selected'"; }
		
		   $html .= "\n <option value='$offset' $selected>$name</option>";
	   }
	
	   $html .= "\n </select>";
	
	   return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 08 12:04:30 CEST 2006 )
	* @name    dropUsertypes
	* @version 1.0 
	* @param   string $name
	* @param   int $isset
	* @param   int $limit
	* @return  string $html
	* @desc    returns a dropdownlist containing all usertypes
	**/
	function dropUsertypes($name, $isset = 0, $limit = 3)
	{
		$types = array(HL_USER_TYPE_CLIENT, HL_USER_TYPE_MEMBER, HL_USER_TYPE_GROUPLEADER, HL_USER_TYPE_ADMINISTRATOR);
		
		$html = "\n <select name='$name' size='1'>";
		
		foreach ($types AS $k => $v)
		{
			if($k <= $limit) {
			   $selected = '';
			
			   if ($isset == $k) { $selected = "selected='selected'"; }
			
			   $html .= "\n <option value='$k' $selected>$v</option>";
			}   
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 29 13:38:19 CET 2006 )
	* @name    dropTimeFormat
	* @version 1.0 
	* @param   string $name
	* @param   int $isset (optional)
	* @return  string $html
	* @desc    
	**/
	function dropTimeFormat($name, $isset = 0)
	{
		$formats = array('d m Y, H:i', 'm d Y, g:i a', 'd m Y, g:i a', 'd m, H:i', 'd m, g:i a', 'd m Y', 'd m');
		
		$html = "\n <select name='$name' size='1'>";
		
		foreach ($formats AS $k => $v)
		{
			$selected = '';
			
			if ($isset == $k) { $selected = "selected='selected'"; }
			
			$html .= "\n <option value='$k' $selected>".date($v)."</option>";  
		}
		
		$html .= "\n </select>";
		
		return $html;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 12:28:34 CEST 2006 )
	* @name    initTableOrdering
	* @version 1.0 
	* @param   array $order_names
	* @param   array $order_querys
	* @param   string $default_order_by
	* @param   string $default_order_dir  
	* @return  void
	* @desc    initiates the table-order
	**/
	function initTableOrdering($order_names, $order_querys, $default_order_by, $default_order_dir)
	{
	   $this->table_order_names   = $order_names;
	   $this->table_order_querys  = $order_querys;
	   $this->table_order_by      = mosGetParam($_REQUEST, 'order_by', $default_order_by);
	   $this->table_order_dir     = mosGetParam($_REQUEST, 'order_dir', $default_order_dir);
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 12:28:59 CEST 2006 )
	* @name    tableOrdering
	* @version 1.0 
	* @param   int $key
	* @return  $html
	* @desc    
	**/
	function tableOrdering($key)
	{
		global $hydra, $protect;
		
		$new_dir = 'ASC';
		$sec_function = '';
		
		if ($protect->current_area == 'files') { $sec_function = '2'; }
		
		if ($this->table_order_dir == 'ASC') { $new_dir = 'DESC'; }
		
		if ($this->table_order_by == $this->table_order_querys[$key]) {
			if ($this->table_order_dir == 'ASC') {
				$order_dir = $hydra->load('img', '12_uparrow.gif', "alt='".HL_SORT_ASC."' title='".HL_SORT_ASC."'");
			}
			else {
			  $order_dir = $hydra->load('img', '12_downarrow.gif', "alt='".HL_SORT_DESC."' title='".HL_SORT_DESC."'");	
			}
		}		
		else {
			if ($this->table_order_dir == 'ASC') {
				$order_dir = $hydra->load('img', '12_downarrow_2.gif', "alt='".HL_SORT_ASC."' title='".HL_SORT_ASC."'");
			}
			else {
			  $order_dir = $hydra->load('img', '12_uparrow_2.gif', "alt='".HL_SORT_DESC."' title='".HL_SORT_DESC."'");	
			}
		}
		
		$html = "<a href=\"javascript:reorderTable$sec_function('".$this->table_order_querys[$key]."','".$new_dir."');\" >".$this->table_order_names[$key].$order_dir."</a>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 16:31:39 CET 2007 )
	* @name    OrderClass
	* @version 1.1 
	* @param   int $key
	* @return  string $class
	* @desc    
	**/
	function OrderClass($key)
	{
		
		switch ($this->table_order_by == $this->table_order_querys[$key])
		{
			case true:
				$class = "class='active_sorting'";
				break;
				
			case false:
				$class = '';
				break;	
		}
		
		return $class;
	}
	
}




class HydraMenu
{
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:25:22 CEST 2006 )
	* @name    HydraMenu
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function HydraMenu()
	{
		
	}	
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:26:18 CEST 2006 )
	* @name    init
	* @version 1.0 
	* @param   string $icon
	* @param   string $menu
	* @return  string $html
	* @desc    creates a node which refers to a menu
	**/
	function init($icon, $menu)
	{
		$html = "\n <div class=\"hydra_menuBar\">"
		      . "\n <a class=\"hydra_menuButton\" onclick=\"return buttonClick(event, '$menu');\" onmouseover=\"buttonMouseover(event, '$menu');\">".HydraSystem::load('img', $icon, "alt='".HL_OPEN_MENU."' title='".HL_OPEN_MENU."'")."</a></div>";
		      
		return $html;      
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:26:18 CEST 2006 )
	* @name    init2
	* @version 1.0 
	* @param   string $node
	* @param   string $menu
	* @return  string $html
	* @desc    creates a node which refers to a menu
	**/
	function init2($node, $menu)
	{
		$html = "\n <div class=\"hydra_menuBar\">"
		      . "\n <a class=\"hydra_menuButton\" onclick=\"return buttonClick(event, '$menu');\" onmouseover=\"buttonMouseover(event, '$menu');\">".$node."</a></div>";
		      
		return $html;      
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 16:32:49 CET 2007 )
	* @name    menu
	* @version 1.1
	* @param   string $id
	* @return  string $html
	* @desc    creates/closes a new menu
	**/
	function menu($id = false)
	{
		
		switch ($id)
		{
			case true:
				$html = "\n <table cellpadding='0' cellspacing='0' id=\"$id\" class=\"hydra_menu\" style=\"display:none\" onmouseover=\"menuMouseover(event)\">";
				break;
				
			case false:
				$html = "\n </table>";
				break;	
		}
		 
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:27:40 CEST 2006 )
	* @name    spacer
	* @version 1.0 
	* @param   void
	* @return  string $html
	* @desc    creates a menu-spacer
	**/
	function spacer()
	{
		$html = "\n <tr><td colspan='2'><div class=\"hydra_menuItemSep\"></div></td></tr>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:28:19 CEST 2006 )
	* @name    item
	* @version 1.0 
	* @param   string $name
	* @param   string $href (optional)
	* @param   string $js (optional)
	* @return  string $html
	* @desc    creates a menu-item
	**/
	function item($name, $img = false, $href= '', $js = '')
	{
		if ($href) { $href = "href='$href'"; }
		if ($js)   { $js = "onclick=\"$js\""; }
		if ($img)  { $img = HydraSystem::load('img', $img, "alt='' style='position:relative'"); }
		
		$html = "\n <tr>"
		      . "\n <td class='hydra_itemspace'>$img</td>"
		      . "\n <td class='hydra_itemContainer' onmouseover='itemHover(this);' onmouseout='itemReset(this);' nowrap>"
		      . "\n <a class=\"hydra_menuItem\" $href $js>$name</a>"
		      . "\n </td>"
		      . "\n </tr>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:32:04 CEST 2006 )
	* @name    itemNode
	* @version 1.0 
	* @param   string $name
	* @param   string $menu
	* @return  string $html
	* @desc    creates a item-node which shows a submenu $menu
	**/
	function itemNode($name, $menu, $img = '')
	{
		if ($img)  { $img = HydraSystem::load('img', $img, "alt='".$name."'"); }
		
		$html = "\n <tr>"
		      . "\n <td class='hydra_itemspace'>$img</td>"
		      . "\n <td class='hydra_itemContainer' onmouseover='itemHover(this);' onmouseout='itemReset(this);' nowrap>"
		      . "\n <span class=\"hydra_menuItemText\">"
		      . "\n <a class=\"hydra_menuItem\" onclick=\"return false;\" onmouseover=\"menuItemMouseover(event, '$menu');\" >$name<span class=\"hydra_menuItemArrow\">&#9654;</span></a></span>"
		      . "\n </td>"
		      . "\n </tr>";
		      
		return $html;      
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:28:19 CEST 2006 )
	* @name    item
	* @version 1.0 
	* @param   string $name
	* @param   string $href (optional)
	* @param   string $js (optional)
	* @return  string $html
	* @desc    creates a menu-item
	**/
	function item2($name, $img = false)
	{
		if ($img)  { $img = HydraSystem::load('img', $img, "alt='".$name."' style='position:relative'"); }
		
		$html = "\n <tr>"
		      . "\n <td class='hydra_itemspace'>$img</td>"
		      . "\n <td class='hydra_itemContainer' onmouseover='itemHover(this);' onmouseout='itemReset(this);' nowrap>"
		      . "\n <a class=\"hydra_menuItem2\">$name</a>"
		      . "\n </td>"
		      . "\n </tr>";
		
		return $html;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Oct 14 22:28:19 CEST 2006 )
	* @name    item
	* @version 1.0 
	* @param   string $name
	* @param   string $href (optional)
	* @param   string $js (optional)
	* @return  string $html
	* @desc    creates a menu-item
	**/
	function item3($name, $img = false)
	{
		if ($img)  { $img = HydraSystem::load('img', $img, "alt='".$name."' style='position:relative'"); }
		
		$html = "\n <tr>"
		      . "\n <td class='hydra_itemspace'>$img</td>"
		      . "\n <td class='hydra_itemContainer'>"
		      . "\n <div class=\"hydra_menuItem3\">$name</div>"
		      . "\n </td>"
		      . "\n </tr>";
		
		return $html;
	}
}
?>