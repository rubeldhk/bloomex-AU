<?php
/**
* $Id: controlpanel_settings.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $hydra_template, $hydra_cfg, $protect;

$settings  = $hydra->loadSettings();
$languages = $hydra->loadLanguageList();
$themes    = $hydra->loadThemeList();
?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_SETTINGS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_HYDRA_VERSION);?></td>
    <td width="25%"><?php echo $settings->version;?></td>
    <td width="55%"><a class="boxButton" href="http://www.projectfork.net" target="_blank"><?php echo $hydra->load('img', '16_hydra.gif', "alt='".HL_CHECK_HOMEPAGE_UPDATES."' align='left'")."&nbsp;&nbsp;".HL_CHECK_HOMEPAGE_UPDATES;?></a></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_DEBUG);?></td>
    <td width="25%" align="left"><input type="checkbox" value="1" name="debugger" <?php if ($settings->debugger) { echo "checked='checked'"; }?>/></td>
    <td width="55%"><?php echo $hydra_template->drawDesc(HL_DEBUG_DESC);?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_RAW_OUTPUT);?></td>
    <td width="25%" align="left"><input type="checkbox" value="1" name="raw_output" <?php if ($settings->raw_output) { echo "checked='checked'"; }?>/></td>
    <td width="55%"><?php echo $hydra_template->drawDesc(HL_RAW_OUTPUT_DESC);?></td>
  </tr>
</table>
</fieldset>
&nbsp;
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_UPLOAD_SETTINGS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_UPLOAD_STORAGE_PATH);?></td>
    <td width="60%"><input class="formInput" type="text" name="upload_path" value="<?php echo $settings->upload_path;?>" size="40"/></td>
    <td width="20%"><?php if (is_writable($settings->upload_path)) { echo "<font color='darkgreen'>".HL_IS_WRITEABLE."</font>"; } else { echo "<font color='darkred'>".HL_NOT_WRITEABLE."</font>"; };?></td>
  </tr>
</table>
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_INSTALLED_LANGUAGES;?></legend>
<?php echo $hydra_template->drawInfo(HL_DELETE_LANG_INFO);?>
&nbsp;
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th>#</th>
    <th width="90%" align="left"><?php echo HL_NAME;?></th>
    <th width="10%" align="center"><?php echo $hydra->load('img', '16_delete.gif', "alt='".HL_DELETE_LANG."' title='".HL_DELETE_LANG."'");?></th>
  </tr>
  <?php 
  $k = 0;
  for ($i = 0, $n = count($languages); $i < $n; $i++)
  {
  	  $delete = "<a href=\"javascript:deleteLanguage(".$languages[$i]->id.", '".HL_CONFIRM."');\" style='cursor:pointer'>".$hydra->load('img', '16_delete.gif', "alt='".HL_DELETE_LANG."' title='".HL_DELETE_LANG."'")."</a>";
  	  
  	  if (!$protect->perm('del_lang') OR ($languages[$i]->name == 'english')) {
  	     $delete = $hydra->load('img', '16_delete_2.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'");
  	  }
  	  ?>
  	  <tr class="row<?php echo $k;?>">
       <td><?php echo ($i+1);?></td>
       <td width="90%" align="left"><?php echo $languages[$i]->label;?></td>
       <td width="10%" align="center"><?php echo $delete;?></td>
     </tr>
     <?php
     $k = 1 - $k; 
  }
  ?>
</table>

</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_INSTALLED_THEMES;?></legend>
<?php echo $hydra_template->drawInfo(HL_DELETE_THEME_INFO);?>
&nbsp;
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th>#</th>
    <th width="90%" align="left"><?php echo HL_NAME;?></th>
    <th width="10%" align="center"><?php echo $hydra->load('img', '16_delete.gif', "alt='".HL_DELETE_LANG."' title='".HL_DELETE_THEME."'");?></th>
  </tr>
  <?php 
  $k = 0;
  for ($i = 0, $n = count($themes); $i < $n; $i++)
  {
  	  
  	  $delete = "<a href=\"javascript:deleteTheme(".$themes[$i]->theme_id.", '".HL_CONFIRM."');\" style='cursor:pointer'>".$hydra->load('img', '16_delete.gif', "alt='".HL_DELETE_THEME."' title='".HL_DELETE_THEME."'")."</a>";
  	  
  	  if (!$protect->perm('del_theme') OR ($themes[$i]->name == 'default')) {
  	     $delete = $hydra->load('img', '16_delete_2.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'");
  	  }
  	     
  	  ?>
  	  <tr class="row<?php echo $k;?>">
       <td><?php echo ($i+1);?></td>
       <td width="90%" align="left"><?php echo $themes[$i]->label;?></td>
       <td width="10%" align="center"><?php echo $delete;?></td>
     </tr>
     <?php
     $k = 1 - $k; 
  }
  ?>
</table>
</fieldset>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'cmd', 'edit_settings');
echo $hydra_template->drawInput('hidden', 'element', '');
echo $hydra_template->drawInput('hidden', 'id', '0');
?>
<script type="text/javascript" language="javascript">
function showRegistry()
{
	document.adminForm.cmd.value = 'edit_registry';
	document.adminForm.submit();
}
</script>
