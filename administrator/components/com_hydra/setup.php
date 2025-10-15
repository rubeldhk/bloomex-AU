<?php
/**
* $Id: setup.php 16 2007-04-15 12:18:46Z eaxs $
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

defined ('_VALID_MOS') OR DIE();

global $my, $mosConfig_live_site, $mosConfig_offset_user, $database, $mosConfig_absolute_path;

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
	

if (!$_POST['submit']) {
?>
<form name="adminForm" method="post" action="index2.php">
<table width="640" cellpadding="0" cellspacing="0" align="center" style="border:1px solid #cccccc">
  <tr>
    <td align="left" valign="top" style="height:45px;padding:2px" nowrap>
     <span style="color:666666;font-family: Arial, Verdana, sans-serif; font-size:16px">Project</span>
     <span style="color:#349800;font-family: Arial, Verdana, sans-serif; font-size:16px">Fork</span>
    </td>
    <td width="100%" align="right" valign="middle"><span style="height:45px;color:#349800;font-weight:bold;font-size:16px;padding:2px">
    Installation</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background:#f4f4f4;padding:10px;border-top:2px solid #349800" align="left" valign="top">
    
    <!-- INTRO START -->
    <br />
    <strong>
    Welcome to the Project Fork Installation!<br />
    Before you can start using Project Fork, please take a moment to fill out the following information.
    </strong>
    <br /> <br />
    <!-- INTRO END -->
    
    <!-- PROFILE START -->
    <table cellpadding="1" cellspacing="1" width="100%" align="left" style="background:#FFFFFF;margin-bottom:5px;border:1px solid #349800;">
      <tr>
        <td valign="top" align="left"><img src="<?php echo $mosConfig_live_site;?>/administrator/components/com_hydra/themes/default/images/32_controlpanel_profile.gif" align="left" alt="Your Profile"/></td>
        <td width="100%" valign="middle" style="color:#349800;font-weight:bold" align="left" colspan="2">Your Profile</td>
      </tr>
      
      <tr>
        <td >&nbsp;</td>
        <td width="20%" align="left"><strong>Language</strong></td>
        <td width="80%" align="left">
        <select name="language">
        <option value="english">English</option>
        <option value="german">Deutsch</option>
        </select>
        </td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td width="20%" align="left"><strong>Timezone</strong></td>
        <td width="80%" align="left">
        <?php echo dropTimeOffset('time_offset');?>
        </td>
      </tr>
    </table>
    <!-- PROFILE END -->
    
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background:#f4f4f4;padding:10px;" align="left" valign="top">

    <!-- USERGROUP START -->
    <table cellpadding="1" cellspacing="1" width="100%" align="left" style="background:#FFFFFF;margin-bottom:5px;border:1px solid #349800;">
      <tr>
        <td valign="top" align="left"><img src="<?php echo $mosConfig_live_site;?>/administrator/components/com_hydra/themes/default/images/32_groups.gif" align="left" alt="Your Usergroup"/></td>
        <td width="100%" valign="middle" style="color:#349800;font-weight:bold" align="left" colspan="2">Your usergroup</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td width="20%" align="left"><strong>Name</strong></td>
        <td width="80%" align="left">
        <input type="text" name="group_name" value="My Group" maxlength="124" size="40"/>
        </td>
      </tr>
    </table>
    <!-- USERGROUP END -->
    
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background:#f4f4f4;padding:10px;" align="left" valign="top">
    
    <!-- SYSTEM START -->
    <table cellpadding="1" cellspacing="1" width="100%" align="left" style="background:#FFFFFF;margin-bottom:5px;border:1px solid #349800">
      <tr>
        <td valign="top" align="left"><img src="<?php echo $mosConfig_live_site;?>/administrator/components/com_hydra/themes/default/images/32_controlpanel_settings.gif" align="left" alt="System"/></td>
        <td width="100%" valign="middle" style="color:#349800;font-weight:bold" align="left" colspan="2">System</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td width="20%" align="left"><strong>Project Fork version</strong></td>
        <td width="80%" align="left" valign="top">
        0.6.7
        </td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td width="20%" align="left"><strong>Upload path</strong></td>
        <td width="80%" align="left" valign="top">
        <span style="font-size:12px"></span><input type="text" name="upload_path" value="<?php echo $mosConfig_absolute_path;?>/documents" size="40"/>
        </td>
      </tr>
    </table>
    <!-- SYSTEM END -->
    
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background:#f4f4f4;padding:10px;" align="left" valign="top">

    <!-- SUBMIT START -->
    <table cellpadding="1" cellspacing="1" width="100%" align="left" style="background:#FFFFFF;margin-bottom:5px;border:1px solid #349800">
      <tr>
        <td align="center"><input type="submit" name="submit" value="Install" /></td>
      </tr>
    </table>
    <!-- SYSTEM END -->
    
    </td>
  </tr>
</table>

<input type="hidden" name="option" value="com_hydra" />
<input type="hidden" name="task" value="install" />
<input type="hidden" name="hydra_id" value="<?php echo rand(1, 100);?>" />
<input type="hidden" name="user_id" value="<?php echo $my->id;?>" />
<input type="hidden" name="group_id" value="<?php echo rand(1, 100);?>" />
<input type="hidden" name="version" value="0.6.7" />
</form>
<?php 
}
else {
	
	$errors = array();
	
	$language    = mosGetParam($_POST, 'language', 'english');
	$time        = mosGetParam($_POST, 'time_offset', $mosConfig_offset_user);
	$group_name  = mosGetParam($_POST, 'group_name', 'My Group');
	$upload_path = mosGetParam($_POST, 'upload_path', 'documents');
	$group_id    = intval(mosGetParam($_POST, 'group_id', 1));
	$user_id     = intval(mosGetParam($_POST, 'user_id', $my->id));
	$hydra_id    = intval(mosGetParam($_POST, 'hydra_id', 1));
	$version     = mosGetParam($_POST, 'version', '0.6.6');
	
	
	// hydra settings
	$query = "INSERT INTO #__hydra_settings VALUES('$version', '$upload_path', '0', '0')";
	       $database->setQuery($query);
	       $database->query();
	       
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	}
	
	
	// add user
	$query = "INSERT INTO #__hydra_users VALUES('$hydra_id', '$user_id', '3')";
	       $database->setQuery($query);
	       $database->query(); 
	
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	}

	// add profile
	$query = "INSERT INTO #__hydra_profile VALUES('', '$hydra_id', 'language', '$language')";
	       $database->setQuery($query);
	       $database->query();
	       
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	}

	$query = "INSERT INTO #__hydra_profile VALUES('', '$hydra_id', 'theme', 'default')";
	       $database->setQuery($query);
	       $database->query();
	       
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	}
	
	
	$query = "INSERT INTO #__hydra_profile VALUES('', '$hydra_id', 'time_offset', '$time')";
	       $database->setQuery($query);
	       $database->query();
	       
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	}

	
	// add group
	$query = "INSERT INTO #__hydra_groups VALUES('$group_id', '$group_name', '')";
	       $database->setQuery($query);
	       $database->query();
	       
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	}

	
	// add user to group
	$query = "INSERT INTO #__hydra_group_members VALUES('$group_id', '$hydra_id')";
	       $database->setQuery($query);
	       $database->query();
	       
	if (intval($database->_errorNum)) {
		$errors[] = $database->_errorMsg; 
	} 

	$reg_queries = array("INSERT INTO `#__hydra_registry` VALUES (1, 'controlpanel', '', 0, 'HL_CONTROLPANEL', '', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (2, 'controlpanel', 'show_usergroups', 1, '', 'HL_CMD_SHOW_USERGROUPS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (3, 'controlpanel', 'new_usergroup', 1, '', 'HL_CMD_NEW_USERGROUPS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (4, 'controlpanel', 'del_usergroup', 1, '', 'HL_CMD_DEL_USERGROUPS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (5, 'controlpanel', 'create_usergroup', 1, '', '', 'new_usergroup');",
                         "INSERT INTO `#__hydra_registry` VALUES (6, 'controlpanel', 'show_users', 1, '', 'HL_CMD_SHOW_USERS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (7, 'controlpanel', 'show_joomlausers', 1, '', 'HL_CMD_SHOW_JOOMLAUSERS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (8, 'controlpanel', 'del_users', 1, '', 'HL_CMD_DEL_USERS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (9, 'controlpanel', 'setup_import', 1, '', 'HL_CMD_IMPORTUSERS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (10, 'controlpanel', 'import_users', 1, '', '', 'setup_import');",
                         "INSERT INTO `#__hydra_registry` VALUES (11, 'projects', '', 0, 'HL_PROJECTS', '', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (12, 'projects', 'new_project', 1, '', 'HL_CMD_NEW_PROJECT', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (13, 'projects', 'create_project', 1, '', '', 'new_project');",
                         "INSERT INTO `#__hydra_registry` VALUES (14, 'projects', 'del_project', 1, '', 'HL_CMD_DEL_PROJECT', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (15, 'projects', 'show_tasks', 0, '', 'HL_CMD_TASKS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (16, 'projects', 'new_task', 0, '', 'HL_CMD_NEW_TASK', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (17, 'projects', 'create_task', 0, '', '', 'new_task');",
                         "INSERT INTO `#__hydra_registry` VALUES (18, 'projects', 'del_task', 1, '', 'HL_CMD_DEL_TASK', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (19, 'files', '', 0, 'HL_FILES', '', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (20, 'files', 'create_files', 0, '', 'HL_CMD_CREATE_FILE', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (21, 'files', 'del_data', 1, '', 'HL_CMD_DEL_DATA', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (22, 'files', 'new_folder', 0, '', 'HL_CMD_NEW_FOLDER', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (23, 'files', 'create_folder', 0, '', '', 'new_folder');",
                         "INSERT INTO `#__hydra_registry` VALUES (24, 'files', 'move_data', 0, '', 'HL_CMD_MOVE_DATA', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (25, 'files', 'new_document', 0, '', 'HL_CMD_NEW_DOCUMENT', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (26, 'files', 'create_document', 0, '', '', 'new_document');",
                         "INSERT INTO `#__hydra_registry` VALUES (27, 'files', 'read_data', 0, '', 'HL_CMD_READ_DATA', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (28, 'files', 'move_confirm', 0, '', '', 'move_data');",
                         "INSERT INTO `#__hydra_registry` VALUES (29, 'files', 'view_comments', 0, '', 'HL_CMD_VIEW_COMMENTS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (30, 'files', 'new_comment', 0, '', 'HL_CMD_NEW_COMMENT', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (31, 'files', 'create_comment', 0, '', '', 'new_comment');",
                         "INSERT INTO `#__hydra_registry` VALUES (32, 'files', 'upload_file', 0, '', '', 'create_files');",
                         "INSERT INTO `#__hydra_registry` VALUES (33, 'controlpanel', 'show_settings', 1, '', 'HL_CMD_SHOW_SETTINGS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (34, 'controlpanel', 'edit_settings', 1, '', 'HL_CMD_EDIT_SETTINGS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (35, 'calendar', '', 1, 'HL_CALENDAR', '', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (36, 'calendar', 'view_groupcal', 1, '', 'HL_CMD_VIEW_GROUP_CAL', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (37, 'calendar', 'new_entry', 1, '', 'HL_CMD_NEW_CAL_ENTRY', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (38, 'calendar', 'create_entry', 1, '', '', 'new_entry');",
                         "INSERT INTO `#__hydra_registry` VALUES (39, 'controlpanel', 'profile', 0, '', 'HL_CMD_SHOW_PROFILE', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (40, 'controlpanel', 'update_profile', 0, '', '', 'profile');",
                         "INSERT INTO `#__hydra_registry` VALUES (41, 'files', 'del_comment', 1, '', '', 'new_comment');",
                         "INSERT INTO `#__hydra_registry` VALUES (42, 'calendar', 'del_entry', 1, '', '', 'new_entry');",
                         "INSERT INTO `#__hydra_registry` VALUES (43, 'controlpanel', 'change_usertype', 1, '', 'HL_CMD_CHANGE_USERTYPE', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (44, 'projects', 'update_progress', 1, '', '', 'new_task');",
                         "INSERT INTO `#__hydra_registry` VALUES (45, 'controlpanel', 'edit_registry', 3, '', 'HL_CMD_EDIT_REGISTRY', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (46, 'controlpanel', 'update_registry', 3, '', '', 'edit_registry');",
                         "INSERT INTO `#__hydra_registry` VALUES (47, 'controlpanel', 'add_registry', 3, '', '', 'edit_registry');",
                         "INSERT INTO `#__hydra_registry` VALUES (48, 'controlpanel', 'del_lang', 2, '', 'HL_CMD_DEL_LANG', '');",
                         "INSERT INTO `#__hydra_registry` VALUES (49, 'controlpanel', 'del_registry', 3, '', '', 'edit_registry');",
                         "INSERT INTO `#__hydra_registry` VALUES (50, 'controlpanel', 'del_theme', 2, '', 'HL_CMD_DEL_THEME', '');",
                         "INSERT INTO `#__hydra_registry` VALUES ('51', 'projects', 'task_notification', '0', '', 'HL_CMD_TASK_NOTIFICATION', '');",
                         "INSERT INTO `#__hydra_registry` VALUES ('52', 'projects', 'view_comments', '0', '', 'HL_CMD_TASK_VIEWCOMMENTS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES ('53', 'projects', 'new_comment', '0', '', 'HL_CMD_TASK_ADDCOMMENTS', '');",
                         "INSERT INTO `#__hydra_registry` VALUES ('54', 'projects', 'create_comment', '0', '', '', 'new_comment');",
                         "INSERT INTO `#__hydra_registry` VALUES ('55', 'projects', 'del_comment', '0', '', 'HL_CMD_TASK_DELCOMMENTS', '');"
                       );

    // fill registry                       
    foreach ($reg_queries AS $k => $query)
    {
    	 $database->setQuery($query);
    	 $database->query();
    	 
    	 if (intval($database->_errorNum)) {
		    $errors[] = $database->_errorMsg; 
	    }
    }
    
    // grant permissions
    $query = "SELECT area, command FROM #__hydra_registry";
           $database->setQuery($query);
           $permissions = $database->loadObjectList();
           
    for($i = 0, $n = count($permissions); $i < $n; $i++)
    {
    	  $p = $permissions[$i];
    	  
    	  $query = "INSERT INTO #__hydra_perms VALUES ('$group_id', '$p->area', '$p->command')";
    	         $database->setQuery($query);
    	         $database->query(); 
    	         
    	  if (intval($database->_errorNum)) {
		    $errors[] = $database->_errorMsg; 
	     }       
    }
?>
<table width="640" cellpadding="0" cellspacing="0" align="center" style="border:1px solid #cccccc">
  <tr>
    <td align="left" valign="top" style="height:45px;padding:2px" nowrap>
    <span style="color:666666;font-family: Arial, Verdana, sans-serif; font-size:16px">Project</span>
     <span style="color:#349800;font-family: Arial, Verdana, sans-serif; font-size:16px">Fork</span>
    </td>
    <td width="100%" align="right" valign="middle"><span style="height:45px;color:#349800;font-weight:bold;font-size:16px;padding:2px">
    Installation</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background:#f4f4f4;padding:10px;border-top:2px solid #349800" align="left" valign="top">
    
    <br/>
    <strong>Error Log</strong><br/>
    <div style="background:#FFFFFF;border:1px solid #349800;display:block; width:500px; height:100px;overflow:auto;">
    <?php
    if (count($errors)) {
    	
    	 foreach ($errors AS $number => $errorMessage)
    	 {
    		 echo "<small>$errorMessage</small><br/><br/>";
    	 }
    	 
    }
    else {
    	
    	 echo "No errors, Congratulations!";
    	 
    }
    ?>
    </div>
    <br/>
    
    <table cellpadding="1" cellspacing="1" width="100%" align="left" style="background:#FFFFFF;border:1px solid #349800;margin-bottom:5px;">
      <?php if (count($errors)) { ?>
      <tr>
        <td align="center" width="100%" style="color:darkred">
        <strong>Unfortunately the installation was not successful!</strong><br/>
        Please copy the error-log and post it on our <a target="_blank" href="http://forum.projectfork.net">Forums</a> for further help!<br/><br/>
        </td>
      </tr>
      <?php } else { ?>
      <tr>
        <td align="center" width="100%" style="color:#349800">
        <strong>The Installation is Complete!</strong><br/><br/>
        </td>
      </tr>
      <?php } ?>
      <tr>
        <td align="center"><a href="index2.php?option=com_hydra">Go to your Controlpanel</a></td>
      </tr>
    </table>
    
    
    </td>
  </tr>
</table>   
<?php
}
