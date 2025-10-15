<?php
/**
* $Id: controlpanel_new_group.html.php 18 2007-04-15 16:20:00Z eaxs $
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

global $hydra_template,$protect, $hydra;

$group        = array(0);
$controlpanel = new Controlpanel;

$controlpanel->getHydraUsers();
$controlpanel->getGroupPerms();

$id = intval(mosGetParam($_REQUEST, 'id', 0));

if ($id > 0) { $group = $controlpanel->loadGroup($id); }

?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_INFO;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_GROUP_NAME." *", 'group_name_lbl');?></td>
    <td width="30%"><input type="text" name="group_name" class="formInput" size="30" value="<?php echo htmlspecialchars($group[0]['group_name'], ENT_QUOTES);?>"/></td>
    <td width="50%"><?php echo $hydra_template->drawDesc(HL_GROUP_NAME_DESC);?></td>
  </tr>
</table>
&nbsp;
<table class="formTable" width="100%">
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_GROUP_DESC);?></td>
    <td width="80%"><?php editorArea( 'editor1', stripslashes($group[0]['group_description']), 'group_desc', '100%;', '250', '30', '20' ) ;?></td>
  </tr>
</table>
</fieldset>
&nbsp;
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GROUP_MEMBERS_AND_PERMS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_GROUP_MEMBERS);?></td>
    <td width="80%">
    <select name="group_members[]" multiple size="5">
    <?php
    foreach ($controlpanel->hydra_users AS $key=>$user)
    {
    	 $selected = '';
    	 if (in_array($user['id'], $group['users'])) { $selected = "selected='selected'"; }
    	 echo "\n <option value='".$user['id']."' $selected>".$user['name']." [ ".$hydra->formatUserType($user['user_type'])." ]</option>";
    }
    ?>
    </select>
    </td>
  </tr>
</table>
<?php 
foreach ($controlpanel->group_perms AS $k => $v)
{
	if (@$v['area_label']) {
	?>
	&nbsp;
	<fieldset class='formFieldset'>
	<legend class='formLegend'><?php echo HL_PERMS_FOR." : ".constant($v['area_label']);?></legend>
   <table class="formTable" width="100%">
   <tr>
     <th width="10%" align="center"><?php echo HL_GRANT;?></th>
     <th width="55%" align="left"><?php echo HL_PERMISSION;?></th>
     <th width="30%" align="left"><?php echo HL_REQUIRED_TYPE;?></th>
   </tr>
   <?php
     $area_name = $v['area'];
     foreach ($controlpanel->group_perms[$area_name] AS $k2 => $v2)
     {
     	  $i = 0;

     	  foreach ($v2 AS $k3 => $v3)
     	  {
     	  	 $ch = "";
     	  	 // show only perms we already have!
     	  	 if (!is_array($protect->my_perms[$area_name])) { $protect->my_perms[$area_name] = array(); }
     	  	 
     	  	 if ($protect->my_usertype == 3 OR (in_array($v3['command'], $protect->my_perms[$area_name]))) {
     	  	 	
     	  	    if (@is_array($group['perms'][$area_name])) {
     	  	    	
     	  	       if (in_array($v3['command'], $group['perms'][$area_name])) { $ch = "checked='checked'"; }
     	  	       
     	  	    }
     	  	     
     	  	    echo "<tr>"; 
     	       ?>     	    
     	         <td width="10%" align="center"><input type="checkbox" name="perms[<?php echo $area_name;?>][<?php echo $i;?>]" value="<?php echo $v3['command'];?>" <?php echo $ch;?>/></td>
     	         <td width="55%" align="left"><?php echo constant($v3['command_label']);?></td>
     	         <td width="30%" align="left"><?php echo $protect->showUserTypeName($v3['user_type'])." ".HL_OR_HIGHER;?></td>
     	       <?php
     	       echo "</tr>";  
     	       $i++;
     	  	 }  
     	  }
     }
   ?>
   </table>
   </fieldset>
   <?php
	}
}
?>
</fieldset>
<script type="text/javascript" language="javascript">
function validateGroup()
{
	var valid = true;
	
	if (document.adminForm.group_name.value == '') {
		valid = false;
	}
	
	if (valid == true) {
     <?php getEditorContents( 'editor1', 'group_desc' ) ; ?>
     document.adminForm.submit();
	}
	else {
		document.getElementById('group_name_lbl').style.color = '#FFFFFF';
		document.getElementById('group_name_lbl').style.background = '#cc0000';
	}
}
</script>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_usergroup');
echo $hydra_template->drawInput('hidden', 'id', $id);
?>
