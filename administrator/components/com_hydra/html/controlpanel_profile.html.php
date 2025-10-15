<?php
/**
* $Id: controlpanel_profile.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $hydra_sess, $protect;

?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_SETTINGS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_LANG);?></td>
    <td width="80%" align="left"><?php echo $hydra_template->dropLanguages('language', $hydra_sess->profile('language'));?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_THEME);?></td>
    <td width="30%" align="left"><?php echo $hydra_template->dropThemes('theme', $hydra_sess->profile('theme'));?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_HYDRA_TIMEOFFSET);?></td>
    <td width="30%" align="left"><?php echo $hydra_template->dropTimeOffset('time_offset', $hydra_sess->profile('time_offset'));?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_HYDRA_TIMEFORMAT);?></td>
    <td width="30%" align="left"><?php echo $hydra_template->dropTimeFormat('time_format', $hydra_sess->profile('time_format'));?></td>
  </tr>
</table>
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_SETTINGS_FOR.": ".HL_NAV_BOX;?></legend>
<table class="formTable" width="100%">
  <?php if ($protect->perm('*controlpanel')) { ?> 
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_CONTROLPANEL);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_controlpanel" <?php if ($hydra_sess->profile('nav_controlpanel') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('*projects')) { ?> 
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_PROJECTS);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_projects" <?php if ($hydra_sess->profile('nav_projects') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('projects->show_tasks')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_TASKS);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_tasks" <?php if ($hydra_sess->profile('nav_tasks') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('*files')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_FILES);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_files" <?php if ($hydra_sess->profile('nav_files') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('*calendar')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_CALENDAR);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_calendar" <?php if ($hydra_sess->profile('nav_calendar') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('profile')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_MY_PROFILE);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_profile" <?php if ($hydra_sess->profile('nav_profile') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('show_usergroups')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW." ".HL_USER_GROUPS);?></td>
    <td align="left"><input type="checkbox" value="1" name="nav_usergroups" <?php if ($hydra_sess->profile('nav_usergroups') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } ?>
</table>
</fieldset>


<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_SETTINGS_FOR.": ".HL_CONTROLPANEL;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW_QUICKPANEL);?></td>
    <td align="left"><input type="checkbox" value="1" name="cp_quickpanel" <?php if ($hydra_sess->profile('cp_quickpanel') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php if ($protect->perm('*projects')) { ?> 
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW_LATEST_TASKS);?></td>
    <td align="left"><input type="checkbox" value="1" name="cp_tasks" <?php if ($hydra_sess->profile('cp_tasks') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } if ($protect->perm('*calendar')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_SHOW_UPCOMING_EVENTS);?></td>
    <td align="left"><input type="checkbox" value="1" name="cp_events" <?php if ($hydra_sess->profile('cp_events') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } ?>
</table>
</fieldset>

<?php if ($protect->perm('*projects')) { ?>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_SETTINGS_FOR.": ".HL_PROJECTS." & ".HL_TASKS;?></legend>
<table class="formTable" width="100%">
  <?php if ($protect->perm('projects->show_tasks')) { ?>
  <tr>
    <td width="30%"><?php echo $hydra_template->drawLabel(HL_HIGHLIGHT_LATE_TASKS);?></td>
    <td align="left"><input type="checkbox" value="1" name="tasks_highlight" <?php if ($hydra_sess->profile('tasks_highlight') == 1) echo "checked='checked'";?>/></td>
  </tr>
  <?php } ?>
</table>
</fieldset>

<?php } ?>

<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'controlpanel');
echo $hydra_template->drawInput('hidden', 'cmd', 'update_profile');
?>