<?php
/**
* $Id: projects_projectsheet.html.php 16 2007-04-15 12:18:46Z eaxs $
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

<table class="sheet" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td class="sheet_tl"></td>
    <td class="sheet_tc"></td>
    <td class="sheet_tr"></td>
  </tr>
  <tr>
    <td class="sheet_cl"></td>
    <td class="sheet_cc">
    
     <!-- Start Content -->
     
     
    <table cellpadding="0" cellspacing="2" width="100%">
      <tr>
        <td class="sheet_title" align="left" nowrap valign="top" width="70%" rowspan="2"><?php echo stripslashes($project->project_name);?></td>
        <td align="right" width="15%" nowrap><?php echo HL_PROJECT_START_TIME;?></td>
        <td align="right" width="15%" nowrap><?php echo hydraDate($project->start_date);?></td>
      </tr>
      
      <tr>
        <td align="right" width="70%" nowrap><?php echo HL_PROJECT_END_TIME;?></td>
        <td align="right" width="30%" nowrap><?php echo hydraDate($project->end_date);?></td>
      </tr>
      
      <tr>
        <td class="sheet_heading" colspan="3"><?php echo HL_PROJECT_DESC;?><br/></td>
      </tr>
      
      <?php if (strlen($project->project_description) >= 1) { ?>
      <tr>
        <td class="sheet_text" colspan="3"><?php echo stripslashes($project->project_description);?></td>
      </tr>
      <?php } ?>
      
      <tr>
        <td class="sheet_heading" colspan="3"><?php echo HL_PROJECT_GROUPS;?><br/></td>
      </tr>
      
      <tr>
        <td class="sheet_text" colspan="3">
        <ul>
        <?php 
        for($i = 0, $n = count($groups); $i < $n; $i++)
        {
        	   $group = $groups[$i];
        	   
          	echo "\n <li>".$group->group_name."</li>";
        } 
        ?>
        </ul>  
        </td>
      </tr>
    </table>  
   
    <?php if (!intval(mosGetParam($_REQUEST, 'print_page', 0))) { ?>
    
    <?php $tabs->startPane('sheet'); ?>
    
    <!-- tasks tab start -->
    <?php $tabs->startTab(HL_TASKS."(".count($tasks).")", 'task_tab'); ?>
    
    <table cellpadding="0" cellspacing="1" width="100%">
    
    <?php for($i = 0, $n = count($tasks); $i < $n; $i++) { $task = $tasks[$i]; ?>
      <tr>
        <td class="sheet_text" width="100%" valign="top" align="left"><?php echo "<strong>".$task->task_name."</strong>" ?></td>
      </tr>
      <tr>
        <td idth="100%" valign="top" align="left"><?php echo $projects->formatTaskStatus($task->task_status, $task->start_date, $task->end_date);?></td>
      </tr>
      <tr>
        <td colspan="2" class="sheet_text"  width="100%" valign="top" align="left"><?php echo $task->task_description;?></td>
      </tr>
      <tr>
        <td colspan="2" class="sheet_text"  width="100%" valign="top" align="left"><hr/></td>
      </tr>
      
      <?php } ?>  
        
    </table>
    
    <?php $tabs->endTab();?>
    <!-- tasks tab end -->
    
    <?php if ($protect->perm("*files")) { ?>
    
    <!-- directories tab start -->
    <?php $tabs->startTab(HL_PROJECT_DIRECTORIES."(".count($dirs).")", 'dir_tab'); ?>
    
    <table cellpadding="0" cellspacing="1" width="100%">
    
    <?php for($i = 0, $n = count($dirs); $i < $n; $i++) { $dir = $dirs[$i]; ?>
    <tr>
      <td><?php echo $hydra->load('img', '16_files_folder.gif', "alt='".HL_OPEN_FOLDER."' title='".HL_OPEN_FOLDER."'");?></td>
      <td class="sheet_text" width="100%" valign="top" align="left"><a class="project_uptodate" href="javascript:browse(<?php echo $dir->folder_id;?>);"><?php echo $dir->folder_name; ?></a></td>
    </tr>
    <?php } ?> 
    
    </table>
    
    <?php $tabs->endTab();?>
    <!-- directories tab end -->
    
    <?php if ($protect->perm("files->read_data")) { ?>
    
    <!-- documents tab start -->
    <?php $tabs->startTab(HL_PROJECT_DOCUMENTS."(".count($docs).")", 'docs_tab'); ?>
    
    <table cellpadding="0" cellspacing="1" width="100%">
    
    <?php for($i = 0, $n = count($docs); $i < $n; $i++) { $doc = $docs[$i]; ?>
    <tr>
      <td><?php echo $hydra->load('img', '16_files_doc.gif', "alt='".HL_OPEN_DOC."' title='".HL_OPEN_DOC."'");?></td>
      <td class="sheet_text" width="100%" valign="top" align="left"><a class="project_uptodate" href="javascript:readDoc(<?php echo $doc->doc_id;?>);"><?php echo $doc->doc_title; ?></a></td>
    </tr>
    <?php } ?> 
    
    </table>
    
    <?php $tabs->endTab();?>
    <!-- documents tab end -->
    
    
    <!-- files tab start -->
    <?php $tabs->startTab(HL_PROJECT_FILES."(".count($files).")", 'files_tab'); ?>
    
    <table cellpadding="0" cellspacing="1" width="100%">
    
    <?php for($i = 0, $n = count($files); $i < $n; $i++) { $file = $files[$i]; ?>
    <tr>
      <td><?php echo $hydra->load('img', '16_files_file.gif', "alt='".HL_DOWNLOAD_FILE."' title='".HL_DOWNLOAD_FILE."'");?></td>
      <td class="sheet_text" width="100%" valign="top" align="left"><a class="project_uptodate" href="javascript:readFile(<?php echo $file->file_id;?>);"><?php echo $file->file_name; ?></a></td>
    </tr>
    <?php } ?> 
    
    </table>
    
    <?php $tabs->endTab();?>
    <!-- files tab end -->

    <?php } ?>
    
    
    <?php } ?> 
    
    <?php $tabs->endPane(); ?>
    
    <?php } else { ?>
    
    <table cellpadding="0" cellspacing="1" width="100%">
    
    <?php for($i = 0, $n = count($tasks); $i < $n; $i++) { $task = $tasks[$i]; ?>
      <tr>
        <td class="sheet_heading" width="100%" valign="top" align="left"><?php echo $task->task_name ?></td>
      </tr>
      <tr>
        <td idth="100%" valign="top" align="left"><?php echo $projects->formatTaskStatus($task->task_status, $task->start_date, $task->end_date);?></td>
      </tr>
      <tr>
        <td colspan="2" class="sheet_text"  width="100%" valign="top" align="left"><?php echo $task->task_description;?></td>
      </tr>

      
      <?php } ?>  
        
    </table>
    
    <?php } ?>
    <!-- End Content -->
    
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
echo HydraTemplate::drawInput('hidden', 'option', 'com_hydra');
echo HydraTemplate::drawInput('hidden', 'area', 'projects');
echo HydraTemplate::drawInput('hidden', 'cmd', '');
echo HydraTemplate::drawInput('hidden', 'cid[]', $id);
echo HydraTemplate::drawInput('hidden', 'folder', '0');
echo HydraTemplate::drawInput('hidden', 'browse_mode', '2');
echo HydraTemplate::drawInput('hidden', 'data_type', '');
echo HydraTemplate::drawInput('hidden', 'id', '0');
?>
<script type="text/javascript" language="javascript">
<?php if (intval(mosGetParam($_REQUEST, 'print_page', 0))) { ?>
window.print();
//window.close();
<?php }?>

function validateDelete()
{
	if (confirm("<?php echo HL_CONFIRM_PROJECT_DELETE;?>")) {
	  document.adminForm.cmd.value = 'del_project';	
	  document.adminForm.submit();
	}  
}

function printProject()
{
	<?php if ($hydra->backend) { ?>
	window.open('index3.php?option=com_hydra&area=projects&sheet=true&print_page=1&id=<?php echo $id;?>', 'win2', 'width=595,height=665,location=no,menubar=no,resizable=yes,status=no,toolbar=no,scrollbars=yes');
	<?php } else { ?>
	window.open('index2.php?option=com_hydra&area=projects&sheet=true&print_page=1&id=<?php echo $id;?>', 'win2', 'width=595,height=665,location=no,menubar=no,resizable=yes,status=no,toolbar=no,scrollbars=yes');
	<?php } ?>
}
</script>
