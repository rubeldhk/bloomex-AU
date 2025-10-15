<?php
/**
* $Id: projects_tasksheet.html.php 16 2007-04-15 12:18:46Z eaxs $
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


mosCommonHTML::loadOverlib();

global $protect;

$projects = new Projects;
$id       = intval(mosGetParam($_REQUEST, 'id'));
$data     = $projects->loadTaskSheet($id);

global $protect, $project;

switch (strlen($data->task_cstatus) > 0)
{
	case true:
		$data->task_cstatus = "[$data->task_cstatus] ";
		break;
}

if (!in_array($data->project_id, $protect->my_projects)) { die ($data->project_id); }

?>
<table class="sheet" cellpadding="0" cellspacing="0" width="100%" align="left">
  <tr>
    <td class="sheet_tl"></td>
    <td class="sheet_tc"></td>
    <td class="sheet_tr"></td>
  </tr>
  <tr>
    <td class="sheet_cl"></td>
    <td class="sheet_cc" valign="top" align="left">
    
    <!-- START INFO -->
    <table cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td class="sheet_title" width="60%" valign="top"><?php echo $data->project_name;?> - <?php echo stripslashes($data->task_cstatus.$data->task_name);?></td>
        <td width="40%" align="right" valign="top">
        <?php 
        echo HL_CREATED_BY.": ".$data->name."<br/>"
        . HL_TASK_START." :".hydraDate($data->start_date)."<br/>"
        . HL_TASK_END." :".hydraDate($data->end_date);
        ?>
        </td>
      </tr>
      <tr>
        <td class="sheet_text"  colspan="2" valign="top"><?php echo stripslashes($data->task_description);?></td>
      </tr>
    </table>
    
    <!-- END INFO -->
    
    </td>
    <td class="sheet_cr"></td>
  </tr>
  <tr>
    <td class="sheet_bl"></td>
    <td class="sheet_bc"></td>
    <td class="sheet_br"></td>
  </tr>
</table>
<?php
echo HydraTemplate::drawInput('hidden', 'cid[]', $id);
echo HydraTemplate::drawInput('hidden', 'option', 'hydra');
echo HydraTemplate::drawInput('hidden', 'area', 'projects');
echo HydraTemplate::drawInput('hidden', 'cmd');
echo "<br style='clear:both'/>";
?>
<?php if($protect->perm('view_comments') ) {
	$projects->loadTaskComments($id);
	
	// permissions
	$perm_delcomment = $protect->perm('del_comment');

	// load the html file
	require_once($hydra->load('html', 'projects_commentlist'));
}
?>
<script type="text/javascript" language="javascript">

<?php if (intval(mosGetParam($_REQUEST, 'print_page', 0))) { ?>
window.print();
window.close();
<?php }?>
function validateDelete()
{
	if (confirm("<?php echo HL_CONFIRM_TASK_DELETE;?>")) {
	  document.adminForm.cmd.value = 'del_task';	
	  document.adminForm.submit();
	}  
}


function printTask(tid)
{
	<?php if ($hydra->backend) { ?>
	window.open('index3.php?option=com_hydra&area=projects&cmd=show_tasks&sheet=true&print_page=1&id=<?php echo $id;?>', 'win2', 'width=595,height=665,location=no,menubar=no,resizable=yes,status=no,toolbar=no,scrollbars=yes');
	<?php } else { ?>
	window.open('index2.php?option=com_hydra&area=projects&cmd=show_tasks&sheet=true&print_page=1&id=<?php echo $id;?>', 'win2', 'width=595,height=665,location=no,menubar=no,resizable=yes,status=no,toolbar=no,scrollbars=yes');
	<?php } ?>
}
</script>