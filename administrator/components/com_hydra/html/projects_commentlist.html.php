<?php
/**
* $Id: projects_commentlist.html.php 16 2007-04-15 12:18:46Z eaxs $
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

foreach ($projects->task_comments AS $k => $v)
{
	
	// edit button
	$edit = $hydra->load('img', '16_edit_2.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'");
	
	if ($v->creator == $protect->my_id OR ($protect->my_usertype == 3)) {
		$edit = "<a href='javascript:editComment(".$v->comment_id.");'>".$hydra->load('img', '16_edit.gif', "alt='".HL_EDIT."' title='".HL_EDIT."'")."</a>";
	}
	
	
	// delete button
	$delete = $hydra->load('img', '16_delete_2.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'");
	
	if ($perm_delcomment AND ($v->creator == $protect->my_id)) {
		$delete = "<a href='javascript:deleteComment(".$v->comment_id.");' >".$hydra->load('img', '16_delete.gif', "alt='".HL_DELETE_DATA."' title='".HL_DELETE_DATA."'")."</a>";
	}
	elseif($protect->my_usertype == 3) {
		$delete = "<a href='javascript:deleteComment(".$v->comment_id.");' >".$hydra->load('img', '16_delete.gif', "alt='".HL_DELETE_DATA."' title='".HL_DELETE_DATA."'")."</a>";
	}
	?>
	<table class="formTable" width="100%" cellpadding="0" cellspacing="0">
	  <tr>
	    <td width="10%" align="left" nowrap><?php echo $hydra_template->drawLabel(HL_CREATED_BY);?></td>
	    <td width="90%" align="left" nowrap><?php echo $v->name." :: ".hydraDate($v->cdate);?></td>
	    <td align="right" nowrap><?php echo $edit;?></td>
	    <td align="right" nowrap><?php echo $delete;?></td>
	  </tr>
	  <tr>
	    <td colspan="4">&nbsp;</td>
	  </tr>  
	  <tr>
	    <td colspan="4" align="left" valign="top"><?php echo stripslashes($v->text);?></td>
	  </tr>
	</table>
	&nbsp;
	<?php
}
$cmd = mosGetParam($_REQUEST, 'cmd', '');

if( $cmd != 'show_tasks') {
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'projects');
echo $hydra_template->drawInput('hidden', 'cmd', '');
}
echo $hydra_template->drawInput('hidden', 'id', 0);
echo $hydra_template->drawInput('hidden', 'task_id', $task);
?>
<script type="text/javascript" language="javascript">
<?php if ($protect->perm('new_comment')) { ?>
function newComment()
{
	document.adminForm.cmd.value = 'new_comment';
	document.adminForm.submit();
}
<?php } ?>
<?php if ($protect->perm('new_comment')) { ?>
function editComment(cid)
{
	document.adminForm.id.value = cid;
	document.adminForm.cmd.value = 'new_comment';
	document.adminForm.submit();
}
<?php } ?>
<?php if ($protect->perm('del_comment')) { ?>
function deleteComment(cid)
{
	document.adminForm.id.value  = cid;
	document.adminForm.cmd.value = 'del_comment';
	document.adminForm.submit();
}
<?php } ?>
</script>