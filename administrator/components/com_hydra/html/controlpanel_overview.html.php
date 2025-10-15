<?php
/**
* $Id: controlpanel_overview.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $protect, $hydra, $hydra_sess;

$controlpanel = new Controlpanel;
$user = $hydra->getUserDetails($protect->my_id);
?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo $hydra->load('img', '16_controlpanel.gif', "alt='".HL_WELCOME."' align='left'")."&nbsp;&nbsp;".HL_WELCOME." ".$user->name;?></legend>
<table class="formTable" width="100%">
  <?php if (intval( $hydra_sess->profile('cp_quickpanel', 1, true) ) ) { ?>
  <tr>
    <?php
       if ($protect->perm('projects->new_project') ) { ?>
       <td width="20%"><?php echo $hydra_template->drawIcon(HL_NEW_PROJECT, '32_projects_newproject.gif', 'area=projects&cmd=new_project'); ?></td>
       <?php }  if ($protect->perm('projects->new_task') AND (count($protect->my_projects))) { ?>
    	 <td width="20%"><?php echo $hydra_template->drawIcon(HL_NEW_TASK, '32_projects_newtask.gif', 'area=projects&cmd=new_task');?></td>
    	 <?php } if ($protect->perm('files->new_folder')) { ?>
    	 <td width="20%"><?php echo $hydra_template->drawIcon(HL_NEW_FOLDER,'32_files_newfolder.gif', 'area=files&cmd=new_folder&folder=0');?></td>
    	 <?php } if ($protect->perm('files->new_document')) { ?>
    	 <td width="20%"><?php echo $hydra_template->drawIcon(HL_NEW_DOCUMENT, '32_files_newdocument.gif', 'area=files&cmd=new_document&folder=0');?></td>
    	 <?php } if ($protect->perm('files->create_files')) { ?>
    	 <td width="20%"><?php echo $hydra_template->drawIcon(HL_NEW_UPLOAD, '32_files_upload.gif', 'area=files&cmd=create_files&folder=0');?></td>
    	 <?php } ?>
  </tr>
  <?php } ?>
</table>
<?php
if ( $protect->perm('projects->show_tasks') AND ( intval($hydra_sess->profile('cp_tasks', 1, true)) ) ) {
   $latest_tasks = $controlpanel->getLatestTasks();
   
   require_once($hydra->load('class', 'projects'));
   ?>
   <div class="tableContainer">
   <div class="tableContainer_header"><?php echo HL_HYDRA_LATEST_TASKS;?></div>
   <div class="tableContainer_body">
   <table class="listTable" width="100%" cellpadding="0" cellspacing="0">
     <tr>
       <th>#</th>
       <th width="40%" align="left"  valign="top"><?php echo HL_NAME;?></th>
       <th align="center" valign="middle"><?php echo $hydra->load('img', '16_priority_0.gif', "alt='".HL_TASK_PRIORITY."' title='".HL_TASK_PRIORITY."'");?></th>
       <th align="center"  valign="top"><?php echo HL_PROGRESS;?></th>
       <th width="30%" align="left"  valign="top"><?php echo HL_PROJECT;?></th>
       <th width="15%" align="left" valign="top"><?php echo HL_TIME_LEFT;?></th>
       <th width="15%" align="left"  valign="top"><?php echo HL_CHANGE_DATE;?></th>
     </tr>
     <?php
     if (count($latest_tasks) < 1) {
  	      echo "<tr class='row0'><td colspan='7'>".$hydra_template->drawInfo(HL_NO_TASKS)."</td></tr>";
     }
     $k = 0;
     for($i = 0, $n = count($latest_tasks); $i < $n; $i++)
     {
     	   $v = $latest_tasks[$i];
     	   
     	   // custom status
     	   $custom_task_status = "";
     	   
     	   if ($v->task_cstatus) {
     	   	 $custom_task_status = "<span class='custom_status'>[".$v->task_cstatus."]</span> ";
     	   }

     	   // remaining time
     	   $timeleft = round(($v->end_date - time()) / 86400,0);
     	   
     	   // remaining time
  	       switch ($v->task_status == '100')
  	       {
  	  	        case true:
  	  		        $remaining_time = HL_STATUS_COMPLETE;
  	  		        break;
  	  		
  	  	        case false:
  	  		        $remaining_time = $timeleft." ".HL_DAYS;
  	  		        break;	
  	       }
  	       
  	       
  	       // priority
  	       $priority_title = array(HL_TASK_PRIORITY_LOW, HL_TASK_PRIORITY_MED, HL_TASK_PRIORITY_HI);
           $priority_title = $priority_title[(intval($v->priority))];
  	       $priority       = $hydra->load('img', "16_priority_".intval($v->priority).".gif", "alt='$priority_title' title='$priority_title'");
     	   ?>
     	   <tr class="row<?php echo $k;?>">
     	     <td><?php echo ($i+1);?></td>
     	     <td width="40%" align="left" valign="top"><a href="<?php echo $hydra->link('area=projects&cmd=show_tasks&sheet=true&id='.$v->task_id);?>"><?php echo $custom_task_status.$v->task_name;?></a></td>
     	     <td align="center" valign="top"><?php echo $priority;?></td>
     	     <td align="center"  valign="top"><?php echo Projects::formatTaskStatus($v->task_status, $v->start_date, $v->end_date);?></td>
     	     <td width="30%" align="left" valign="top"><a href="<?php echo $hydra->link('area=projects&id='.$v->project_id.'&sheet=true');?>"><?php echo $v->project_name;?></a></td>
     	     <td width="15%" align="left" valign="top"><?php echo $remaining_time;?></td>
     	     <td width="15%" align="left"  valign="top"><?php echo hydraDate($v->last_changed);?></td>
     	   </tr>
     	   <?php
     	   $k = 1 - $k;
     }
     ?>
   </table>
   </div>
   <div class="tableContainer_footer" align="center">&nbsp;</div>
   </div>
   &nbsp;
<?php
}
if ($protect->perm('*calendar')  AND ( intval($hydra_sess->profile('cp_events', 1, true)) ) ) {
	$events = $controlpanel->getUpcomingEvents();
	?>
	<div class="tableContainer">
   <div class="tableContainer_header"><?php echo HL_UPCOMING_EVENTS;?></div>
   <div class="tableContainer_body">
   <table class="listTable" width="100%" cellpadding="0" cellspacing="0">
     <tr>
       <th>#</th>
       <th width="60%" align="left"><?php echo HL_TITLE;?></th>
       <th width="20%" align="left"><?php echo HL_START_DATE;?></th>
       <th width="20%" align="left"><?php echo HL_END_DATE;?></th>
     </tr>
     <?php
     if (count($events) < 1) {
  	      echo "<tr class='row0'><td colspan='4'>".$hydra_template->drawInfo(HL_NO_UPCOMING_EVENTS)."</td></tr>";
     }
     $k = 0;
     for($i = 0, $n = count($events); $i < $n; $i++)
     {
     	   $v = $events[$i];
     	   ?>
     	   <tr class="row<?php echo $k;?>">
     	     <td><?php echo ($i+1);?></td>
     	     <td width="60%" align="left"><a href="<?php echo $hydra->link('area=calendar');?>"><?php echo $v->title;?></a></td>
     	     <td width="20%" align="left"><?php echo hydraDate($v->start_date);?></td>
     	     <td width="20%" align="left"><?php echo hydraDate($v->end_date);?></td>
     	   </tr>
     	   <?php
     	   $k = 1 - $k;
     }
     ?>
   </table>
   </div>
   <div class="tableContainer_footer" align="center">&nbsp;</div>
   </div>
	<?php
}
?>
</fieldset>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'controlpanel');
echo $hydra_template->drawInput('hidden', 'cmd', '');
?>