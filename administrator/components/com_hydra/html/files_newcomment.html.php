<?php
/**
* $Id: files_newcomment.html.php 17 2007-04-15 12:35:17Z eaxs $
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

global $hydra_template, $database, $protect;

$filebrowser = new FileBrowser;

$edit    = array();
$id      = intval(mosGetParam($_POST, 'id', 0));
$folder  = intval(mosGetParam($_POST, 'folder', 0));
$comment = intval(mosGetParam($_POST, 'comment', 0));
$type    = mosGetParam($_POST, 'data_type', '');

if ($comment >= 1) {
	$edit = $filebrowser->loadComment($comment, $type, $id);
}

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
        <td><?php editorArea( 'editor1', @stripslashes($edit[0]['text']), 'text', '100%;', '350', '30', '20' ) ;?></td>
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
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'files');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_comment');
echo $hydra_template->drawInput('hidden', 'folder', $folder);
echo $hydra_template->drawInput('hidden', 'id', $id);
echo $hydra_template->drawInput('hidden', 'data_type', $type);
echo $hydra_template->drawInput('hidden', 'comment', $comment);
?>

<script type="text/javascript" language="javascript">
function submitCreate()
{
	<?php getEditorContents('editor1', 'text'); ?>
	document.adminForm.submit();
}
</script>