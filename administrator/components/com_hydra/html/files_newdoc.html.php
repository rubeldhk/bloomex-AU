<?php
/**
* $Id: files_newdoc.html.php 18 2007-04-15 16:20:00Z eaxs $
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

$filebrowser = new FileBrowser;
$properties = $filebrowser->getFolderProperties();

$browserHTML = new browserHTML();

$edit          = array();
$id            = intval(mosGetParam($_POST, 'id', 0));
$display_none  = "style='display:none'";
$display_block = "style='display:block'";

if ($id >= 1) { $edit = $filebrowser->loadDocument($id); }

?>
<div align="center" style="width:100%;">
<table class="sheet" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td class="sheet_tl"></td>
    <td class="sheet_tc"></td>
    <td class="sheet_tr"></td>
  </tr>
  <tr>
    <td class="sheet_cl"></td>
    <td class="sheet_cc">
    
    <table>
      <tr>
        <td><?php echo $hydra_template->drawLabel(HL_TITLE, 'doc_title_lbl');?><input type="text" name="doc_title" class="formInput" size="40" value="<?php echo @htmlspecialchars($edit->doc_title, ENT_QUOTES);?>"/></td>
      </tr>
      <tr>
        <td><?php editorArea( 'editor1', @stripslashes($edit->doc_text), 'doc_text', '100%;', '350', '30', '20' ) ;?></td>
      </tr>
    </table>
    
    
    </td>
    <td class="sheet_cr"></td>
  </tr>
  <tr>
    <td class="sheet_bl"></td>
    <td class="sheet_bc"></td>
    <td class="sheet_br"></td>
  </tr>
</table>
</div>

<?php @$browserHTML->dataTypeSettings($id, $edit->doc_type, $edit->project, $edit->uid, $edit->doc_active, $edit->doc_access, $properties); ?>

<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'files');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_document');
echo $hydra_template->drawInput('hidden', 'folder', intval(mosGetParam($_POST, 'folder', 0)));
echo $hydra_template->drawInput('hidden', 'id', $id);
?>
<script type="text/javascript" language="javascript">

function validateCreate()
{
	var name = document.adminForm.doc_title;
	
	if (name.value == '') {
		document.getElementById('doc_title_lbl').style.color = '#FFFFFF';
		document.getElementById('doc_title_lbl').style.background = '#cc0000';
	}
	else {
		<?php getEditorContents('editor1', 'doc_text'); ?>
		document.adminForm.submit();
	}
}
</script>
