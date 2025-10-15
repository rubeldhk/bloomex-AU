<?php
/**
* $Id: files_upload.html.php 16 2007-04-15 12:18:46Z eaxs $
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


global $hydra_template, $database, $hydra_cfg;

$filebrowser = new FileBrowser;
$properties  = $filebrowser->getFolderProperties();
$edit        = array();
$id          = intval(mosGetParam($_POST, 'id', 0));

$browserHTML = new browserHTML();

if ($id >= 1) {
	$edit = $filebrowser->loadFile($id);
}

?>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_INFO;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_FILE_NAME, 'file_name_lbl');?></td>
    <td width="30%"><input type="text" name="file_name" class="formInput" size="30" value="<?php echo @stripslashes($edit->file_name);?>"/></td>
    <td width="50%"><?php echo HL_FILE_NAME_DESC;?></td>
  </tr>
  <?php if (is_writable($hydra_cfg->settings->upload_path)) { ?>
  <tr>
    <td width="20%"><?php if ($id >= 1) { echo $hydra_template->drawLabel(HL_UPDATE_FILE);} else { echo $hydra_template->drawLabel(HL_FILE_SOURCE." *", 'source_lbl'); }?></td>
    <td width="30%"><input id="file_source" type="file" name="file" class="formInput" size="30"/></td>
    <td width="50%"><?php if ($id >= 1) { echo HL_UPDATE_FILE_DESC;} else { echo HL_FILE_SOURCE_DESC; }?></td>
  </tr>
  <?php } else { ?>
  <tr>
    <td width="20%"><?php if ($id >= 1) { echo $hydra_template->drawLabel(HL_UPDATE_FILE);} else { echo $hydra_template->drawLabel(HL_FILE_SOURCE." *", 'source_lbl'); }?></td>
    <td width="30%"><?php echo $hydra_template->drawInfo(HL_FILES_UPLOADPATH_NOT_WRITABLE);?><input type="hidden" id="file_source" name="file"/></td>
    <td width="50%"><?php if ($id >= 1) { echo HL_UPDATE_FILE_DESC;} else { echo HL_FILE_SOURCE_DESC; }?></td>
  </tr>
  <?php } ?>
</table>
</fieldset>

<?php @$browserHTML->dataTypeSettings($id, $edit->file_type, $edit->project, $edit->uid, $edit->file_active, $edit->file_access, $properties); ?>

<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'files');
echo $hydra_template->drawInput('hidden', 'cmd', 'upload_file');
echo $hydra_template->drawInput('hidden', 'folder', intval(mosGetParam($_POST, 'folder', 0)));
echo $hydra_template->drawInput('hidden', 'id', $id);

?>
<script type="text/javascript" language="javascript">

function validateCreate()
{
<?php if (!$id) { ?>	
	var source = document.adminForm.file_source;
	
	if (source.value == '') {
		document.getElementById('source_lbl').style.color = '#FFFFFF';
		document.getElementById('source_lbl').style.background = '#cc0000';
	}
	else {
<?php } ?>		
		document.adminForm.submit();
<?php if (!$id) { ?>		
	}
<?php } ?>	
}
</script>