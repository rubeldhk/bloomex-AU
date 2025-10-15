<?php
/**
* $Id: projects_newcomment.html.php 17 2007-04-15 12:35:17Z eaxs $
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
        <td><?php editorArea( 'editor1', @stripslashes($edit->text), 'text', '100%;', '350', '30', '20' ) ;?></td>
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
echo $hydra_template->drawInput('hidden', 'area', 'projects');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_comment');
echo $hydra_template->drawInput('hidden', 'task_id', $task);
echo $hydra_template->drawInput('hidden', 'id', $id);
?>

<script type="text/javascript" language="javascript">
function submitCreate()
{
	<?php getEditorContents('editor1', 'text'); ?>
	document.adminForm.submit();
}
</script>