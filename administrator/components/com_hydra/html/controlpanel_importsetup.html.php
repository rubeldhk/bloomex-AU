<?php
/**
* $Id: controlpanel_importsetup.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $protect;

$cid = mosGetParam($_POST, 'cid');

$controlpanel = new Controlpanel;

$controlpanel->getUsergroups();

$users = $controlpanel->getImportData($cid);

$options = '';

foreach ($controlpanel->usergroups AS $k => $v)
{
	if ($protect->my_usertype == 3 OR (in_array($v['group_id'], $protect->my_groups))) {
	   $options .= "\n <option value='".$v['group_id']."'>".$v['group_name']."</option>";
	}   
}

$i = 0;
foreach ($users AS $k => $v)
{
	$grouplist = "\n <select name='user[$i][group]' size='1'>".$options."\n </select>";
	
	?>
	<fieldset class='formFieldset'>
   <legend class='formLegend'><?php echo HL_SETTINGS_FOR." : ".$v['name'];?></legend>
   <table class="formTable" width="100%">
     <tr>
       <td width="10%"><?php echo $hydra_template->drawLabel(HL_LANG);?></td>
       <td width="40%" align="left"><?php echo $hydra_template->dropLanguages("user[$i][language]");?></td>
       <td width="10%"><?php echo $hydra_template->drawLabel(HL_THEME);?></td>
       <td width="40%" align="left"><?php echo $hydra_template->dropThemes("user[$i][theme]");?></td>
     </tr>
     <tr>
       <td width="10%"><?php echo $hydra_template->drawLabel(HL_USER_GROUP);?></td>
       <td width="40%" align="left"><?php echo $grouplist;?></td>
       <td width="10%"><?php echo $hydra_template->drawLabel(HL_USER_TYPE);?></td>
       <td width="40%" align="left"><?php echo $hydra_template->dropUsertypes("user[$i][type]",0,$protect->my_usertype);?></td>
     </tr>
   </table>
   </fieldset>
	<?php
	echo $hydra_template->drawInput('hidden', "user[$i][id]", $v['id']);
	
	$i++;
}

echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'cmd', 'import_users');
?>
