<?php
/**
* $Id: projects_projectlist.html.php 16 2007-04-15 12:18:46Z eaxs $
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
<div class="tableContainer_header"><?php echo HL_PROJECTS;?></div>
<div class="tableContainer_body">

<table class="listTable" width="100%" cellpadding="0" cellspacing="0">

  <tr>
  
    <th align="center">#</th>
    
    <th align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $projects->total_projects; ?>);" /></th>
    
    <th align="center"><?php echo $hydra->load('img', '16_menu.gif', "alt='".HL_OPEN_MENU."' title='".HL_OPEN_MENU."'");?></th>
    
    <th align="left" width="60%"><?php echo $hydra_template->tableOrdering(0);?></th>
    
    <th align="center" width="20%"><?php echo HL_PROGRESS;?></th>
    
    <th align="center" width="20%"><?php echo $hydra_template->tableOrdering(1);?></th>
    
  </tr>
  
  <?php
  
  if ($projects->projects < 1) { 
  	
  	 echo "<tr class='row0'><td colspan='7'>".$hydra_template->drawInfo(HL_NO_PROJECTS)."</td></tr>"; 
     $list = array();
     
  }

  $k = 0;
  $i = 0;
  foreach ($projects->projects AS $v)
  {
  	  // build context menu
  	  $menu = hydraMenu::init('16_menu.gif', 'p_'.$i);
  	  $menu .= hydraMenu::menu('p_'.$i);
  	  $menu .= hydraMenu::item(HL_VIEW_PROJECT_DETAILS, '16_details.gif', $hydra->link('area=projects&sheet=true&id='.$v->project_id));
  	  
  	  
  	  switch ( $new_project )
  	  {
  	  	case true:
  	  		$menu .= hydraMenu::item(HL_SHOW_TASKS, '16_tasks.gif', $hydra->link('area=projects&cmd=show_tasks&pid='.$v->project_id));
  	  		break;
  	  }
 
  	  
  	  switch ( $show_tasks )
  	  {
  	  	case true:
  	  		$menu .= hydraMenu::item(HL_EDIT, '16_edit.gif', $hydra->link('area=projects&cmd=new_project&id='.$v->project_id));
  	  		break;
  	  }
  	    
  	  $menu .= hydraMenu::menu(); 
  	  ?>
  	  <tr class="row<?php echo $k;?>">
  	  
  	     <td align="center" align="top"><?php echo $pageNav->rowNumber( $i ); ?></td>
  	     
  	     <td align="center" align="top"><?php echo mosHTML::idBox( $i, $v->project_id); ?></td>
  	     
  	     <td align="center" align="top"><?php echo $menu;?></td>
  	     
  	     <td align="left" width="60%" align="top" <?php echo $hydra_template->OrderClass(0);?>><a href='<?php echo $hydra->link('area=projects&sheet=true&id='.$v->project_id);?>'><?php echo $v->project_name;?></a></td>
  	     
  	     <td align="center" align="top" width="20%"><?php echo $projects->formatProjectStatus($v->project_id, $v->start_date, $v->end_date, $v->total_tasks, $v->project_status);?></td>
  	     
  	     <td align="center" align="top" width="20%" <?php echo $hydra_template->OrderClass(1);?>><?php echo hydraDate($v->mdate);?></td>
  	     
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
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'projects');
echo $hydra_template->drawInput('hidden', 'cmd', '');
echo $hydra_template->drawInput('hidden', 'boxchecked', '');
echo $hydra_template->drawInput('hidden', 'order_by', $order_by);
echo $hydra_template->drawInput('hidden', 'order_dir', $order_dir);
echo $hydra_template->drawInput('hidden', 'limitstart', $limitstart);
?>
<script type="text/javascript" language="javascript">
function validateDelete()
{
	if (document.adminForm.boxchecked.value == '') {
	  alert ('<?php echo HL_DEL_PROJECT_WARN;?>');	
	}
	else {
	  if (confirm("<?php echo HL_CONFIRM_PROJECT_DELETE;?>")) {	
	     document.adminForm.cmd.value = 'del_project';	
	     document.adminForm.submit();
	  }   	
	}
}
</script>