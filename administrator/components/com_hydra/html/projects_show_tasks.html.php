<?php
/**
* $Id: projects_show_tasks.html.php 27 2007-04-16 18:50:09Z eaxs $
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

<div class="tableContainer">
<div class="tableContainer_header"><div style="display:block;float:left"><?php echo HL_TASKS;?></div><div style="display:block;float:right"><?php echo $pageNav->getLimitBox($hydra->link('area=projects&task=show_tasks'));?></div></div>
<div class="tableContainer_body">

<!-- Filter start -->

<table width="100%" cellpadding="0" cellspacing="0" class="formTable">
  <tr>
    <td nowrap><?php echo HydraProjectHTML::dropProjectFilter();?></td>
    <td>&nbsp;</td>
    <td nowrap><?php echo HydraProjectHTML::dropTaskCreatedByFilter();?></td>
    <td>&nbsp;</td>
    <td nowrap><?php echo HydraProjectHTML::dropTaskFilter();?></td>
    <td>&nbsp;</td>
    <td nowrap><?php echo HydraProjectHTML::dropTaskProgressFilter();?></td>
    <td>&nbsp;</td>
    <td width="20%" nowrap><a href='javascript:document.adminForm.submit();' class='boxButton' ><?php echo $hydra->load('img', '16_tick.gif', "alt='".HL_APPLY_FILTER."' align='left'")."&nbsp;&nbsp;".HL_APPLY_FILTER;?></a></td>
    <td width="80%">&nbsp;</td>
  </tr>
</table>

<!-- Filter end -->


<!-- List start -->

<table class="listTable" width="100%" cellpadding="0" cellspacing="0">

  <tr>
  
    <th align="center">#</th>
    
    <th align="center" valign="top"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $total; ?>);" /></th>
    
    <?php if ($perm_notification) {?><th align="center" valign="middle"><?php echo $hydra->load('img', '16_notification.gif', "alt='".HL_NOTIFICATION."' title='".HL_NOTIFICATION."'");?></th><?php } ?>
    
    <?php if ($perm_viewcomments) {?><th align="center" valign="middle"><?php echo $hydra->load('img', '16_comment.gif', "alt='".HL_COMMENTS."' title='".HL_COMMENTS."'");?></th><?php } ?>
    
    <th align="center" valign="middle"><?php echo $hydra->load('img', '16_menu.gif', "alt='".HL_OPEN_MENU."' title='".HL_OPEN_MENU."'");?></th>
    
    <th align="left" valign="top" width="40%"><?php echo $hydra_template->tableOrdering(0);?></th>
    
    <th align="left" valign="top" width="15%"><?php echo $hydra_template->tableOrdering(3);?></th>
    
    <th align="center" valign="middle"><?php echo $hydra->load('img', '16_priority_0.gif', "alt='".HL_TASK_PRIORITY."' title='".HL_TASK_PRIORITY."'");?></th>
    
    <th align="center" valign="top" nowrap><?php echo $hydra_template->tableOrdering(1);?></th>
    
    <th align="left" valign="top" width="15%"><?php echo HL_TIME_LEFT;?></th>
    
    <th align="left" valign="top" width="30%"><?php echo $hydra_template->tableOrdering(2);?></th>
    
  </tr>
  <?php
  
  if ($total < 1) {
  	
  	 echo "<tr class='row0'><td colspan='11'>".$hydra_template->drawInfo(HL_NO_TASKS)."</td></tr>";
     $list = array();
     
  }

  $k = 0;
  $i = 0;
  foreach($list AS $v)
  {

  	  $id = $v->id;
  	  
  	  // priority
  	  $priority_title = array(HL_TASK_PRIORITY_LOW, HL_TASK_PRIORITY_MED, HL_TASK_PRIORITY_HI);
      $priority_title = $priority_title[(intval($v->priority))];
  	  $priority       = $hydra->load('img', "16_priority_".intval($v->priority).".gif", "alt='$priority_title' title='$priority_title'");

  	  
  	  // highlight task?
  	  $timeleft = round(($v->end_date - time()) / 86400,0);

  	  if ($timeleft >= 0 OR ($v->task_status == '100')) {
  	     $class = "row$k";
  	  }
  	  else {
  	  	 
  	  	switch (intval($hydra_sess->profile('tasks_highlight')))
  	  	{
  	  		case 1:
  	  			$class = "row_alert";
  	  			break;
  	  			
  	  		case 0:
  	  			$class = "row$k";
  	  			break;	
  	  	}

  	  }
  	  
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
  	  
  	  

      // build context menu
  	  $menu  = hydraMenu::init('16_menu.gif', 't_'.$i);
  	  $menu .= hydraMenu::menu('t_'.$i);
  	  $menu .= hydraMenu::item(HL_VIEW_TASK_DETAILS, '16_details.gif', $hydra->link('area=projects&cmd=show_tasks&sheet=true&id='.$id));
  	  switch ($protect->perm('new_task'))
  	  {
  	  	case true:
  	  		$menu .= hydraMenu::item(HL_EDIT, '16_edit.gif', $hydra->link('area=projects&cmd=new_task&id='.$id));
  	  		break;
  	  }
  	  
  	  // notification ?
  	  if ($perm_notification) {
  	  	
  	  	  switch (in_array($v->id, $projects->task_notifications))
  	      {
  	  	      case true:
  	  	      	  $menu .= hydraMenu::item(HL_NOTIFICATION_DISABLE, '16_notification.gif', '', "removeTaskNotification($v->id)");
  	  		      $notification = "<a href='javascript:removeTaskNotification($v->id);'>".$hydra->load('img', '16_notification.gif', "alt='".HL_NOTIFICATION_ENABLED."' title='".HL_NOTIFICATION_ENABLED."'")."</a>";
  	  		      break;
  	  		
  	  	      case false:
  	  	      	  $menu .= hydraMenu::item(HL_NOTIFICATION_ENABLE, '16_notification_2.gif', '', "addTaskNotification($v->id)");
  	  		      $notification = "<a href='javascript:addTaskNotification($v->id);'>".$hydra->load('img', '16_notification_2.gif', "alt='".HL_NOTIFICATION_DISABLED."' title='".HL_NOTIFICATION_DISABLED."'")."</a>";
  	  		      break;	
  	      }
  	      
  	  }
  	  $menu .= hydraMenu::menu();

  	  
      // custom status ? 
  	  switch ($v->task_cstatus)
  	  {
  	  	case true:
  	  		$custom_status = "<span class='custom_status'>[$v->task_cstatus]</span>";
  	  		break;
  	  		
  	  	case false:
  	  		$custom_status = '';
  	  		break;	
  	  }
  	  
  	  
  	  // subtask?
  	  switch ($v->parent > 0) 
  	  { 
  	  	case true: 
  	  	    $sub = "&nbsp; &nbsp; &nbsp; &#x2514;";
  	  	    break; 
  	  	  
  	  	case false:
  	  		$sub = '';
  	  		break; 
  	  }
  	  
  	  
  	  // comments?
  	  if ($perm_viewcomments) {
  	  	
  	  	switch ($v->comments < 1)
  	  	{
  	  		case true:
  	  			$comments = $v->comments;
  	  			
  	  			if ($perm_addcomments) {
  	  				$comments = "<a href='".$hydra->link('area=projects&cmd=new_comment&task_id='.$v->id)."' style='font-weight:bold'>$v->comments</a>";
  	  			}
  	  			break;
  	  			
  	  		case false:
  	  			$comments = "<a style='font-weight:bold' href='".$hydra->link('area=projects&cmd=view_comments&id='.$v->id)."'>$v->comments</a>";
  	  			break;	
  	  	}
  	  	
  	  }
  	  ?>
  	  <tr class="<?php echo  $class;?>">
  	  
  	    <td align="center"  valign="top"><?php echo $pageNav->rowNumber( $i ); ?></td>
  	    
  	    <td align="center"  valign="top"><?php echo mosHTML::idBox($i, $id); ?></td>
  	    
  	    <?php if ($perm_notification) {?><td align="center" valign="top"><?php echo $notification;?></td><?php } ?>
  	    
  	    <?php if ($perm_viewcomments) {?><td align="center" valign="top" style="text-align:center"><?php echo $comments;?></td><?php } ?>
  	    
  	    <td align="center"  valign="top"><?php echo $menu;?></td>
  	    
  	    <td align="left" width="40%" <?php echo $hydra_template->OrderClass(0);?>  valign="top">
  	    <a href="<?php echo $hydra->link('area=projects&cmd=show_tasks&sheet=true&id='.$v->id);?>"><?php echo $sub.$custom_status.$v->name; ?></a>
  	    </td>
  	    
  	    <td align="left" width="15%" <?php echo $hydra_template->OrderClass(3);?>  valign="top"><?php echo $v->username;?></td>
  	    
  	    <td align="center" valign="top"><?php echo $priority;?></td>
  	    
  	    <td  valign="top" align="center" <?php echo $hydra_template->OrderClass(1);?>><?php echo $projects->formatTaskStatus($v->task_status, $v->start_date, $v->end_date, $v->id);?></td>
  	    
  	    <td  valign="top" align="left" width="15%"><?php echo $remaining_time;?></td>
  	    
  	    <td align="left" width="30%" <?php echo $hydra_template->OrderClass(2);?>  valign="top"><?php echo $v->project_name;?></td>
  	    
  	  </tr>
  	  <?php
  	  $k = 1 - $k;
  	  $i++;
  }
  ?>
</table>
</div>
<div class="tableContainer_footer" align="center"><?php echo $pageNav->getPagesLinks(); ?></div>
</div>

<!-- List end -->


<!-- hidden fields start -->

<input type="hidden" name="option" value="com_hydra" />
<input type="hidden" name="area" value="projects" />
<input type="hidden" name="cmd" value="show_tasks" />
<input type="hidden" name="boxchecked" value="" />
<input type="hidden" name="order_by" value="<?php echo $order_by;?>" />
<input type="hidden" name="order_dir" value="<?php echo $order_dir;?>" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="progress" value="" />
<input type="hidden" name="enable" value="0" />

<!-- hidden fields end -->


<!-- js start -->

<script type="text/javascript" language="javascript">
function validateDelete()
{
	if (document.adminForm.boxchecked.value == '') {
	  alert ('<?php echo HL_DEL_PROJECT_WARN;?>');
	}
	else {
		if (confirm("<?php echo HL_CONFIRM_TASK_DELETE;?>")) {
	      document.adminForm.cmd.value = 'del_task';
	      document.adminForm.submit();
		}
	}
}

function updateProgress(tid, prog)
{
	document.adminForm.cmd.value = 'update_progress';
	document.adminForm.id.value = tid;
	document.adminForm.progress.value = prog;
	document.adminForm.submit();
}

function addTaskNotification(task_id)
{
	document.adminForm.cmd.value    = "task_notification";
	document.adminForm.enable.value = "1";
	document.adminForm.id.value     = task_id;
	document.adminForm.submit();
}

function removeTaskNotification(task_id)
{
	document.adminForm.cmd.value    = "task_notification";
	document.adminForm.enable.value = "0";
	document.adminForm.id.value     = task_id;
	document.adminForm.submit();
}
</script>

<!-- js end -->