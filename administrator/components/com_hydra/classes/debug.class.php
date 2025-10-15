<?php
/**
* $Id: debug.class.php 16 2007-04-15 12:18:46Z eaxs $
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


class HydraDebugger
{
	var $enabled;
	
	var $log;
	
	var $runtime;
	
	var $notices;
	
	var $warnings;
	
	var $errors;
	
	var $violations;
	
	var $ignore;
	

	/**
	* @author  Tobias Kuhn ( Wed Dec 13 20:28:35 CET 2006 )
	* @name    HydraDebugger
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function HydraDebugger()
	{
		global $database;
		
		$database->setQuery("SELECT debugger FROM #__hydra_settings LIMIT 1");
		       
		$this->enabled    = intval($database->loadResult());
		$this->runtime    = mosProfiler::getmicrotime(); 
		$this->log        = array();
		$this->notices    = array();
		$this->warnings   = array();
		$this->errors     = array();
		$this->violations = array();
		$this->ignore     = array();
		
		switch (mosGetParam($_POST, 'cmd', '') == 'store_log')
		{
			case true:
				$this->dieLog();
				break;
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 13:52:48 CET 2007 )
	* @name    logNotice
	* @version 1.1 
	* @param   string $message
	* @return  void
	* @desc    
	**/
	function logNotice($message) 
	{
		
		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
		
		$runtime = (mosProfiler::getmicrotime() - $this->runtime);
		
		switch (in_array($message, $this->ignore))
		{
			case false:
				$this->log[]     = $runtime.' - '.$message;
		        $this->notices[] = $runtime.' - '.$message;
		        $this->ignore[]  = $message;
				break;
		}  
		 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 13:51:22 CET 2007 )
	* @name    logError
	* @version 1.1
	* @param   $message
	* @return  void
	* @desc    
	**/
	function logError($message)
	{
				
		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
		
		$runtime = (mosProfiler::getmicrotime() - $this->runtime);

		switch (in_array($message, $this->ignore))
		{
			case false:
				$this->log[]    = $runtime.' - '.$message;
		        $this->errors[] = $runtime.' - '.$message;
		        $this->ignore[] = $message;
				break;
		}
		  
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 13:50:36 CET 2007 )
	* @name    logWarning
	* @version 1.1 
	* @param   $message
	* @return  void
	* @desc    
	**/
	function logWarning($message)
	{

		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
		
		$runtime = (mosProfiler::getmicrotime() - $this->runtime);

		switch (in_array($message, $this->ignore))
		{
			case false:
				$this->log[]      = $runtime.' - '.$message;
		        $this->warnings[] = $runtime.' - '.$message;
		        $this->ignore[]   = $message;
				break;
		}  
		
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Jan 27 13:49:46 CET 2007 )
	* @name    logViolation
	* @version 1.1 
	* @param   $message
	* @return  void
	* @desc    
	**/
	function logViolation($message)
	{
				
		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
		
		$runtime = (mosProfiler::getmicrotime() - $this->runtime);

		switch (in_array($message, $this->ignore))
		{
			case false:
				$this->log[]        = $runtime.' - '.$message;
		        $this->violations[] = $runtime.' - '.$message;
		        $this->ignore[]     = $message;
				break;
		} 
		 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 15 22:09:30 CET 2006 )
	* @name    getRuntime
	* @version 1.0 
	* @param   void
	* @return  
	* @desc    returns the current runtime
	**/
	function getRuntime()
	{
				
		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
		
		list($usec, $sec) = explode(" ",microtime());
		
		$rt = ((float)$usec + (float)$sec);
		
		return $rt;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 15 23:40:35 CET 2006 )
	* @name    printLine
	* @version 1.1
	* @param   int $i
	* @param   string $message
	* @param   string $class
	* @return  string $line
	* @desc    format a log message
	**/
	function printLine($i, $message, $class)
	{
		global $hydra_cfg; 
				
		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
		
		$line = $i+1;
		
		$message = str_replace($hydra_cfg->site_path, '', $message);
		$message = str_replace($hydra_cfg->site_url, '', $message);
		
		$line = "\n <tr><td class='debug_line' align='left' valign='top' >".$line."</td ><td class='debug_$class' align='left' valign='top' nowrap>$message</td></tr>";
		
		return $line;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Feb 03 23:43:48 CET 2007 )
	* @name    printLog
	* @version 1.3
	* @param   void
	* @return  void
	* @desc    
	**/
	function printLog($return = false)
	{
		global $hydra_cfg, $hydra_sess, $protect, $hydra, $database, $mosConfig_sef, $_VERSION;
		
		error_reporting(0);
			
		switch ($this->enabled)
		{
			case false:
				return;
				break;
		}
	
		
		$tmp_hidden_fields = array();
		$hidden_fields = '';
		
		$output_notices    = '';
		$output_warnings   = '';
		$output_errors     = '';
		$output_violations = '';
		
		$tmp_hidden_fields['runtime'] = $runtime = (mosProfiler::getmicrotime() - $this->runtime);
		 
		$tmp_hidden_fields['total_notices']    = $total_notices     = count($this->notices);
		$tmp_hidden_fields['total_warnings']   = $total_warnings    = count($this->warnings);
		$tmp_hidden_fields['total_errors']     = $total_errors      = count($this->errors);
		$tmp_hidden_fields['total_violations'] = $total_violations  = count($this->violations);
		
		$tmp_hidden_fields['jid']      = $jid      = $hydra_sess->jid;
		$tmp_hidden_fields['hid']      = $hid      = $hydra_sess->hid;
		
		
		if (class_exists('HydraSystem')) {
		   $tmp_hidden_fields['usertype'] = $usertype = $hydra->formatUserType($protect->my_usertype);
		}
		else {
		   $tmp_hidden_fields['usertype'] = $usertype = "undefined";	
		}
		
		
		$tmp_hidden_fields['browser'] = $browser = mosGetBrowser($_SERVER['HTTP_USER_AGENT']);
		$tmp_hidden_fields['os']      = $os      = mosGetOS($_SERVER['HTTP_USER_AGENT']);
	
		
		$my_perms = '';
		$my_total_perms = 0;
		$total_perms    = 0;
		$profile        = '';
		
		
		// SEO
		if ($mosConfig_sef == '1') { 
			$seo = 'Yes';
		}
		else {
			$seo = 'No';
		}
		$tmp_hidden_fields['seo'] = $seo;
		
		
		// get version
		$query = "SELECT version FROM #__hydra_settings";
		       $database->setQuery($query);
		       $tmp_hidden_fields['hydra_version'] = $hydra_version = $database->loadResult(); 

		$tmp_hidden_fields['joomla_version'] = $joomla_version = $_VERSION->RELEASE.'.'.$_VERSION->DEV_LEVEL;
		$tmp_hidden_fields['server'] = $_SERVER['SERVER_SOFTWARE'];

		 
		// format profile
		$c = 0;
		$k = 0;
		foreach($hydra_sess->profile AS $param => $val)
		{
			$profile .= "\n <tr class='row$k'>";
			$profile .= "\n <td width='10%' valign='top' align='left'>".($c+1)."</td>";
			$profile .= "\n <td width='40%' valign='top' align='left'>".htmlentities($param)."</td>";
			$profile .= "\n <td width='50%' valign='top' align='left'>".htmlentities($val)."</td>";
			$profile .= "\n </tr>";
			
			$tmp_hidden_fields['profile'][] = $param.' = '.$val;
			
			$c++;
			$k = 1 - $k;
		}
		
		
		// count my permissions
		foreach ($protect->valid_areas AS $k => $area)
		{
			$my_total_perms = $my_total_perms + count($protect->my_perms[$area]);
		}
		
        $tmp_hidden_fields['my_total_perms'] = $my_total_perms;
      
        
        // count registry perms
        foreach ($protect->valid_areas AS $k => $area)
		{
			$total_perms = $total_perms + count($protect->valid_commands[$area]);
            $tmp_hidden_fields['total_perms'] = $total_perms;
          
			foreach($protect->my_perms[$area] AS $k2 => $cmd) 
			{
			   $my_perms .= "\n <tr class='row0'>";
      	       $my_perms .= "\n <td width='40%' align='left' valign='top'>$area</td>";
      	       $my_perms .= "\n <td width='60%' align='left' valign='top'>$cmd</td>";
      	       $my_perms .= "\n </tr>";
      	   
      	       $tmp_hidden_fields['perms'][] = $area.' -> '.$cmd;
			}
			
		}
      
		
		// format notices
		for($i = 0; $i < $total_notices; $i++)
        {
	      $output_notices .= $this->printLine($i, $this->notices[$i], 'notice');
	      
	      $tmp_hidden_fields['notices'][] = $this->notices[$i];
        }
        
        $output_notices = "<div class='debug_log'><table cellspacing='0' cellpadding='0' width='100%'>$output_notices</table></div>";
      
        
        // format warnings
        for($i = 0; $i < $total_warnings; $i++)
        {
	        $output_warnings .= $this->printLine($i, $this->warnings[$i], 'warning');
	        $tmp_hidden_fields['warnings'][] = $this->warnings[$i];
        }
        
        $output_warnings = "<div class='debug_log'><table cellspacing='0' cellpadding='0' width='100%'>$output_warnings</table></div>";
      
        // format errors
        for($i = 0; $i < $total_errors; $i++)
        {
	        $output_errors .= $this->printLine($i, $this->errors[$i], 'error');
	        $tmp_hidden_fields['errors'][] = $this->errors[$i];
        }
        
        $output_errors = "<div class='debug_log'><table cellspacing='0' cellpadding='0' width='100%'>$output_errors</table></div>";
      
        // format violations
        for($i = 0; $i < $total_violations; $i++)
        {
	        $output_violations .= $this->printLine($i, $this->violations[$i], 'violation');
	        $tmp_hidden_fields['violations'][] = $this->violations[$i];
        }
        
        $output_violations = "<div class='debug_log'><table cellspacing='0' cellpadding='0' width='100%'>$output_violations</table></div>";
      
        // get usergroups
        if (count($protect->my_groups)) {
        	
        
           $groups = @implode(',', $protect->my_groups);
      
           $query = "SELECT group_id, group_name FROM #__hydra_groups WHERE group_id IN($groups)";
                  $database->setQuery($query);
                  $rows = @$database->loadObjectList();
       
               
           $groups = '';
           $k = 0;             
           for($i = 0, $n = count($rows); $i < $n; $i++)
           {
      	      $row = $rows[$i];
      	
      	      $groups .= "\n <tr class='row$k'>";
      	      $groups .= "\n <td width='10%' align='left' valign='top'>$row->group_id</td>";
      	      $groups .= "\n <td width='90%' align='left' valign='top'>$row->group_name</td>";
      	      $groups .= "\n </tr>";
      	
      	      $tmp_hidden_fields['groups'][] = "($row->group_id) $row->group_name";
      	
      	      $k = 1 - $k;
           }
      }
      else {
      	 $tmp_hidden_fields['groups'][] = "undefined";
      }
      
      // get users
      $users = '';
      
      $k = 0;
      for($i = 0, $n = count($protect->my_userspace); $i < $n; $i++)
      {
      	$user_id = $protect->my_userspace[$i];
      	
      	if (class_exists('HydraSystem')) {
      	   $user_info = $hydra->getUserDetails($user_id);
      	}
      	else {
      	   $user_info = new stdClass();
      	   $user_info->name = "undefined";
      	}
      	
      	$users .= "\n <tr class='row$k'>";
      	$users .= "\n <td width='10%' align='left' valign='top'>$user_id</td>";
      	$users .= "\n <td width='90%' align='left' valign='top'>$user_info->name</td>";
      	$users .= "\n </tr>";
      	
      	$tmp_hidden_fields['users'][] = "($user_id) $user_info->name";
      	
      	$k = 1 - $k;
      }
      
      // get Projects
      $projects = @implode(',', $protect->my_projects);
      $rows     = null;
      
      if (count($protect->my_projects)) {
         $query = "SELECT project_id, project_name FROM #__hydra_project WHERE project_id IN($projects)";
                $database->setQuery($query);
                $rows = $database->loadObjectList();
      }          
             
      $projects = '';       
      
      $k = 0;
      for($i = 0, $n = count($rows); $i < $n; $i++)
      {
      	$row = $rows[$i];
      	
      	$projects .= "\n <tr class='row$k'>";
      	$projects .= "\n <td width='10%' align='left' valign='top'>$row->project_id</td>";
      	$projects .= "\n <td width='90%' align='left' valign='top'>$row->project_name</td>";
      	$projects .= "\n </tr>";
      	
      	$tmp_hidden_fields['projects'][] = "($row->project_id) $row->project_name";
      	
      	$k = 1 - $k;
      }
      
      $return_fields = array();
      
      foreach($tmp_hidden_fields AS $name => $value)
      {
      	
      	if (is_array($value)) {
      		
      		foreach ($value AS $name2 => $value2)
      		{
      			$return_fields[$name][] = $value2;
      			
      			$hidden_fields .= "\n <input type='hidden' name='debug[$name][]' value='$value2' />";
      		}
      		
      	}
      	else {
      		
      		$return_fields[$name] = $value;
      	    $hidden_fields .= "\n <input type='hidden' name='debug[$name]' value='$value' />";
      	}
      	   
      }
      
      if ($return) {
      	return $return_fields;
      }
      // include debug_template
      require_once($hydra_cfg->hydra_path.'/html/system_console.html.php');
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Feb 03 23:43:29 CET 2007 )
	* @name    dieLog
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    
	**/
	function dieLog()
	{
		error_reporting(0);
		 
		$fields = mosGetParam($_POST, 'debug', array());
		$date   = date('d m Y, H:i');
		
		if (empty($fields)) {
			$fields = $this->printLog(true);
		}
		
		$notices = '';
		
		foreach ($fields['notices'] AS $k => $v)
		{
			switch (strlen($k+1))
			{
				case '1':
				  $row = ($k+1)."    |";
				  break;
				  
				case '2':
					$row = ($k+1)."   |";
					break;
					
				case '3':
					$row = ($k+1)."  |";
					break;
					
				case '4':
					$row = ($k+1)." |";
					break;		  
			}

			$notices .= $row." ".stripslashes($v)." \r\n";
		}

		$warnings = '';
		
		foreach ($fields['warnings'] AS $k => $v)
		{
			switch (strlen($k+1))
			{
				case '1':
				  $row = ($k+1)."    |";
				  break;
				  
				case '2':
					$row = ($k+1)."   |";
					break;
					
				case '3':
					$row = ($k+1)."  |";
					break;
					
				case '4':
					$row = ($k+1)." |";
					break;		  
			}
			
			$warnings .= $row." ".stripslashes($v)." \r\n";
		}
		
		
		$errors = '';
		
		foreach ($fields['errors'] AS $k => $v)
		{
			switch (strlen($k+1))
			{
				case '1':
				  $row = ($k+1)."    |";
				  break;
				  
				case '2':
					$row = ($k+1)."   |";
					break;
					
				case '3':
					$row = ($k+1)."  |";
					break;
					
				case '4':
					$row = ($k+1)." |";
					break;		  
			}
			
			$errors .= $row." ".stripslashes($v)." \r\n";
		}
		
		$violations = '';
		
		foreach ($fields['violations'] AS $k => $v)
		{
			switch (strlen($k+1))
			{
				case '1':
				  $row = ($k+1)."    |";
				  break;
				  
				case '2':
					$row = ($k+1)."   |";
					break;
					
				case '3':
					$row = ($k+1)."  |";
					break;
					
				case '4':
					$row = ($k+1)." |";
					break;		  
			}
			
			$violations .= $row." ".stripslashes($v)." \r\n";
		}
		
		$profile = '';
		
		foreach ($fields['profile'] AS $k => $v)
		{
			
			$profile .= stripslashes($v)." \r\n";
		}
		
		$groups = '';
		
		foreach ($fields['groups'] AS $k => $v)
		{
			
			$groups .= stripslashes($v)." \r\n";
		}
		
		$users = '';
		
		foreach ($fields['users'] AS $k => $v)
		{
			
			$users .= stripslashes($v)." \r\n";
		}
		
		$perms = '';
		
		foreach ($fields['perms'] AS $k => $v)
		{
			
			$perms .= stripslashes($v)." \r\n";
		}
		
		$file = "================================================================================== \r\n"
		      . "HYDRA LOGFILE - $date \r\n"
		      . "================================================================================== \r\n"
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "SYSTEM \r\n" 
		      . "============================================= \r\n"
		      . "\r\n"
		      . "::::::::: General information \r\n"
		      . "\r\n"
		      . "Hydra Version  = ".$fields['hydra_version']."  \r\n"
		      . "Joomla Version = ".$fields['joomla_version']."  \r\n"
		      . "SEO            = ".$fields['seo']."  \r\n"
		      . "Server         = ".$fields['server']."  \r\n"
		      . "\r\n"
		      . "::::::::: Performance \r\n"
		      . "\r\n"
		      . "Runtime          = ".$fields['runtime']."  \r\n"
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "USER \r\n" 
		      . "============================================= \r\n"
		      . "\r\n"
		      . "::::::::: General information \r\n"
		      . "\r\n"
		      . "Joomla ID         = ".$fields['jid']."  \r\n"
		      . "Hydra ID          = ".$fields['hid']."  \r\n"
		      . "Usertype          = ".$fields['usertype']."  \r\n"
		      . "Operating System  = ".$fields['os']."  \r\n"
		      . "Browser           = ".$fields['browser']."  \r\n"
		      . "\r\n"
		      . "::::::::: Profile \r\n"
		      . $profile
		      . "\r\n"
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "ENVIRONMENT \r\n" 
		      . "============================================= \r\n"
		      . "\r\n"
		      . "::::::::: My usergroups \r\n"
		      . "\r\n"
		      . $groups
		      . "\r\n"
		      . "::::::::: Known users \r\n"
		      . "\r\n"
		      . $users
		      . "\r\n"
		      . "::::::::: My projects \r\n"
		      . $projects
		      . "\r\n"
		      . "::::::::: My permissions \r\n"
		      . $perms
		      . "\r\n"
		      . "\r\n"
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "$fields[total_notices] NOTICES \r\n" 
		      . "============================================= \r\n"
		      . $notices
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "$fields[total_warnings] WARNINGS \r\n" 
		      . "============================================= \r\n"
		      . $warnings
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "$fields[total_errors] ERRORS \r\n" 
		      . "============================================= \r\n"
		      . $errors
		      . "\r\n"
		      . "\r\n"
		      . "============================================= \r\n"
		      . "$fields[total_violations] VIOLATIONS \r\n" 
		      . "============================================= \r\n"
		      . $violations;
		      
		     
		if (!empty($fields) AND ($this->enabled)) {
			
			header("Content-Type: text/plain");
            header("Content-Disposition: attachment; filename=hydra_".time().".txt");
         
            echo $file;
            exit();
		}
	}
	
	
	
}
?>