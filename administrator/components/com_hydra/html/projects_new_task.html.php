<?php
/**
* $Id: projects_new_task.html.php 18 2007-04-15 16:20:00Z eaxs $
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


mosCommonHTML::loadCalendar();

global $hydra_template, $hydra;

$id       = intval( mosGetParam($_REQUEST, 'id', 0));
$projects = new Projects;

$projects->getProjects();
$projectlist = $projects->projects;

require_once($hydra->load('class', 'controlpanel'));

$controlpanel = new Controlpanel;
$controlpanel->getHydraUsers();
$userlist = $controlpanel->hydra_users;


$start_date = date('Y-m-d', time());
$end_date   = date('Y-m-d', time());


$edit = array();
if ($id >= 1) {
	$edit = $projects->loadTask($id);
	$start_date = @date('Y-m-d', $edit->start_date);
	$end_date   = @date('Y-m-d', $edit->end_date);
}

?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_INFO;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_NAME." *", 'task_name_lbl');?></td>
    <td width="30%"><input type="text" name="task_name" class="formInput" size="30" value="<?php echo @htmlspecialchars($edit->task_name, ENT_QUOTES);?>"/></td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_TASK_NAME_DESC);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_CUSTOM_STATUS);?></td>
    <td width="30%"><input type="text" name="custom_status" maxlength="24" size="30" value="<?php echo @htmlspecialchars($edit->task_cstatus, ENT_QUOTES);?>"/></td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_CUSTOM_STATUS_DESC);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_PARENT_TASK);?></td>
    <td width="50%" colspan="2" align="left"><?php echo HydraProjectHTML::dropParentTasks('parent', @$edit->parent_task, @$id);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_TASK_PRIORITY);?></td>
    <td width="30%"><?php echo HydraProjectHTML::dropPriority(@$edit->priority);?></td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_TASK_PRIORITY_DESC);?></td>
  </tr>
</table>
&nbsp;
<table class="formTable" width="100%">
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_TASK_TASK_DESC);?></td>
    <td width="80%"><?php editorArea( 'editor1', @stripslashes($edit->task_description), 'task_desc', '100%;', '250', '30', '20' ) ;?></td>
  </tr>
</table>
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_TIME_LIMIT_AND_PROGRESS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_TASK_START);?></td>
    <td width="30%">
    <input class="formInput" type="text" name="start_date" id="start_date" size="25" maxlength="19" value="<?php echo $start_date;?>" />
	 <input type="reset" class="button" value="..." onclick="return showCalendar('start_date', 'dd-mm-y');" />
	 </td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_TASK_START_DESC);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_TASK_END);?></td>
    <td width="30%">
    <input class="formInput" type="text" name="end_date" id="end_date" size="25" maxlength="19" value="<?php echo $end_date;?>" />
	 <input type="reset" class="button" value="..." onclick="return showCalendar('end_date', 'dd-mm-y');" />
    </td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_TASK_END_DESC);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_PROGRESS);?></td>
    <td width="30%"><?php echo $hydra_template->dropProgress('status', @$edit->task_status);?></td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_TASK_PROGRESS_DESC);?></td>
  </tr>
</table>
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_TASK_LINKS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20" valign="top"><?php echo $hydra_template->drawLabel(HL_PROJECT);?></td>
    <td width="30" valign="top">
    <select name="task_project" size="1">
    <?php
    for($i = 0; $i < count($projectlist); $i++)
    {
    	 $val = $projectlist[$i];
    	 $selected = '';

    	 if (@$val->project_id == $edit->project) { $selected = "selected='selected'"; }

    	 echo "\n <option value='".$val->project_id."' $selected>".$val->project_name."</option>";
    }
    ?>
    </select>
    </td>
    <td width="20" valign="top"><?php echo $hydra_template->drawLabel(HL_USER);?></td>
    <td width="30" valign="top">
    <select name="task_user" size="1">
    <?php
    foreach ($userlist AS $key => $val)
    {
    	 $selected = '';

    	 if ($val['id'] == @$edit->uid) { $selected = "selected='selected'"; }

    	 echo "\n <option value='".$val['id']."' $selected>".$val['name']." [ ".$hydra->formatUserType($val['user_type'])." ]</option>";
    }
    ?>
    </select>
    </td>
  </tr>
  <?php if (!$id) { ?>
  <tr>
    <td width="20" valign="top">&nbsp;</td>
    <td width="30" valign="top">&nbsp;</td>
    <td width="20" valign="top"><input type="checkbox" name="notify_user" value="1" /></td>
    <td width="30" valign="top"><?php echo $hydra_template->drawLabel(HL_NOTIFY_USER);?></td>
  </tr>
  <?php } ?>
</table>
</fieldset>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'projects');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_task');
echo $hydra_template->drawInput('hidden', 'id', $id);
?>
<script type="text/javascript" language="javascript">
function validateCreate()
{
	var valid = true;

	if (document.adminForm.task_name.value == '') {
		valid = false;
	}

	if (valid == true) {
     <?php getEditorContents( 'editor1', 'task_desc' ) ; ?>
     document.adminForm.submit();
	}
	else {
		document.getElementById('task_name_lbl').style.color = '#FFFFFF';
		document.getElementById('task_name_lbl').style.background = '#cc0000';
	}
}
</script>