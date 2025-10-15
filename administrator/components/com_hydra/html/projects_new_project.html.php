<?php
/**
* $Id: projects_new_project.html.php 18 2007-04-15 16:20:00Z eaxs $
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

global $hydra_template, $database;

$projects = new Projects;
$projects->getUserGroups();

$id = intval(mosGetParam($_REQUEST, 'id'));

$edit_project = array();
$sel_groups   = array(0);
$tmp          = array();

if ($id >= 1) { $edit_project = $projects->loadProject($id); }

$start_date = @date('Y-m-d', $edit_project[0]['start_date']);
if (empty($edit_project[0]['start_date'])) { $start_date = date('Y-m-d', time()); }

$end_date   = @date('Y-m-d', $edit_project[0]['end_date']);
if (empty($edit_project[0]['end_date'])) { $end_date = date('Y-m-d', time()); }

if (!empty($edit_project[0]['project_id'])) {
  $query = "SELECT gid FROM #__hydra_project_groups WHERE pid = '".$edit_project[0]['project_id']."'";
         $database->setQuery($query);
         $sel_groups = $database->loadAssocList();
         
  foreach ($sel_groups AS $k => $v) { $tmp[] = $v['gid']; }
  $sel_groups = $tmp;       
}                 
           

?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_INFO;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_PROJECT_NAME." *", 'project_name_lbl');?></td>
    <td width="30%"><input type="text" name="project_name" class="formInput" size="30" value="<?php echo @htmlspecialchars($edit_project[0]['project_name'], ENT_QUOTES);?>"/></td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_PROJECT_NAME_DESC);?></td>
  </tr>
</table>
&nbsp;
<table class="formTable" width="100%">
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_PROJECT_DESC);?></td>
    <td width="80%"><?php editorArea( 'editor1', @stripslashes($edit_project[0]['project_description']), 'project_desc', '100%;', '250', '30', '20' ) ;?></td>
  </tr>
</table>
&nbsp;
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_PROJECT_TIME_LIMIT;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_PROJECT_START_TIME);?></td>
    <td width="30%">
    <input class="formInput" type="text" name="start_date" id="start_date" size="25" maxlength="19" value="<?php echo $start_date;?>" />
	 <input type="reset" class="button" value="..." onclick="return showCalendar('start_date', 'dd-mm-y');" />
	 </td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_PROJECT_START_TIME_DESC);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_PROJECT_END_TIME);?></td>
    <td width="30%">
    <input class="formInput" type="text" name="end_date" id="end_date" size="25" maxlength="19" value="<?php echo $end_date;?>" />
	 <input type="reset" class="button" value="..." onclick="return showCalendar('end_date', 'dd-mm-y');" />
    </td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_PROJECT_END_TIME_DESC);?></td>
  </tr>
</table>
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_PROJECT_ACCESS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_PROJECT_GROUPS);?></td>
    <td width="80%">
    <select name="project_groups[]" size="5" multiple>
    <?php
    foreach ($projects->usergroups AS $k => $v)
    {
    	 $selected = "";
    	 
    	 if (in_array($v['group_id'], $sel_groups)) { $selected = "selected='selected'"; }
    	 
    	 echo "\n <option value='".$v['group_id']."' $selected>".$v['group_name']."</option>";
    }
    ?>
    </select>
    </td>
  </tr>
</table>
</fieldset>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'projects');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_project');
echo $hydra_template->drawInput('hidden', 'id', $id);
?>
<script type="text/javascript" language="javascript">
function validateCreate()
{
	var valid = true;
	
	if (document.adminForm.project_name.value == '') {
		valid = false;
	}
	
	if (valid == true) {
     <?php getEditorContents( 'editor1', 'project_desc' ) ; ?>
     document.adminForm.submit();
	}
	else {
		document.getElementById('project_name_lbl').style.color = '#FFFFFF';
		document.getElementById('project_name_lbl').style.background = '#cc0000';
	}
}
</script>
