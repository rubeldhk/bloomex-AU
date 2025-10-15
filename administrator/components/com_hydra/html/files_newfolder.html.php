<?php
/**
* $Id: files_newfolder.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $database;

$filebrowser   = new FileBrowser();
$edit          = array();
$id            = intval(mosGetParam($_POST, 'id', 0));

$properties = $filebrowser->getFolderProperties();

$browserHTML = new browserHTML();

if ($id >= 1) { $edit = $filebrowser->loadFolder($id); }

?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_INFO;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_FOLDER_NAME." *", 'folder_name_lbl');?></td>
    <td width="30%"><input type="text" name="folder_name" class="formInput" size="30" value="<?php echo @stripslashes($edit->folder_name);?>"/></td>
    <td width="50%"></td>
  </tr>
</table>
</fieldset>
&nbsp;
<?php @$browserHTML->dataTypeSettings($id, $edit->folder_type, $edit->project, $edit->uid, $edit->folder_active, $edit->folder_access, $properties); ?>

<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'files');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_folder');
echo $hydra_template->drawInput('hidden', 'folder', intval(mosGetParam($_POST, 'folder', 0)));
echo $hydra_template->drawInput('hidden', 'id', $id);
?>

<script type="text/javascript" language="javascript">

function validateCreate()
{
	var name = document.adminForm.folder_name;
	
	if (name.value == '') {
		document.getElementById('folder_name_lbl').style.color = '#FFFFFF';
		document.getElementById('folder_name_lbl').style.background = '#cc0000';
	}
	else {
		document.adminForm.submit();
	}
}
</script>