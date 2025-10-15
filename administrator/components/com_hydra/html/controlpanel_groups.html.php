<?php
/**
* $Id: controlpanel_groups.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $database, $hydra, $mainframe, $mosConfig_list_limit, $protect;

$limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
// fixed by Giller
$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$protect->current_area}{$protect->current_command}limitstart", 'limitstart', 0 ) );

if ($limit == 0) { $limit = $mosConfig_list_limit; }

$controlpanel = new Controlpanel;

$usergroups = $controlpanel->getUsergroups($limit, $limitstart);
$total      = $usergroups['total'];
$usergroups = $usergroups['groups'];

require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
$pageNav = new mosPageNav( $total, $limitstart, $limit );

?>
<div class="tableContainer">
<div class="tableContainer_header"><?php echo HL_USER_GROUPS;?></div>
<div class="tableContainer_body">
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th align="center">#</th>
    <th align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $total; ?>);" /></th>
    <th align="left" width="100%"><?php echo HL_NAME;?></th>
    <th align="center"><?php echo $hydra->load('img', '16_projects.gif', "alt='".HL_PROJECTS."' title='".HL_PROJECTS."'");?></th>
    <th align="center"><?php echo $hydra->load('img', '16_user.gif', "alt='".HL_GROUP_MEMBERS."' title='".HL_GROUP_MEMBERS."'");?></th>
    <th align="center"><?php echo $hydra->load('img', '16_edit.gif', "alt='".HL_EDIT."' title='".HL_EDIT."'");?></th>
  </tr>
  <?php
  if ($total < 1) { 
  	 echo "<tr class='row0'><td colspan='5'>".$hydra_template->drawInfo(HL_NO_USERGROUPS)."</td></tr>"; 
    $usergroups = array();
  }
  
  $k = 0;
  $i = 0;
  $listed = array();
  foreach ($usergroups AS $k => $v)
  {
  	  if (in_array($v['group_id'], $listed)) {
  	  	  continue;
  	  }
  	  $listed[] = $v['group_id'];
  	  
  	  $memberlist = "";
  	  $query = "SELECT j.name, h.user_type FROM #__hydra_users AS h, #__users AS j, #__hydra_group_members AS m"
  	         . "\n WHERE m.gid = '".$v['group_id']."'"
  	         . "\n AND h.id = m.uid"
  	         . "\n AND j.id = h.jid";
  	         $database->setQuery($query);
  	         $members = $database->loadAssocList();
  	  
  	  foreach ($members AS $k2 => $v2)
  	  {
  	  	  $memberlist .= "<tr><td>".$v2['name']."</td><td> [ ".$hydra->formatUserType($v2['user_type'])." ]</td></tr>";
  	  }
  	  $memberlist = "\n <a href='#' onmouseover=\"return overlib('<table>".$memberlist."</table>', CAPTION, '".HL_GROUP_MEMBERS."', BELOW, RIGHT);\" onmouseout=\"return nd();\">"
  	              . $hydra->load('img', '16_user.gif', "alt='".HL_GROUP_MEMBERS."' title='".HL_GROUP_MEMBERS."'")
  	              . "\n </a>";
  	              
  	  if (count($members) < 1) { $memberlist = $hydra->load('img', 'user_inactive');	}
  	  
  	  $edit = "<a href='".$hydra->link('section=controlpanel&cmd=new_usergroup&id='.$v['group_id'])."'>".$hydra->load('img', '16_edit.gif', "alt='".HL_EDIT."' title='".HL_EDIT."'")."</a>";

  	  // get the projects
  	  $involved_projects = "";
  	  $cond = "";
  	  
  	  if ($protect->my_usertype != 3) {
  	  	  $my_projects = implode(',', $protect->my_projects);
  	  	  $cond = "\n AND g.pid IN($my_projects)";
  	  }
  	  
  	  $query = "SELECT p.project_name FROM #__hydra_project AS p, #__hydra_project_groups AS g"
  	         . "\n WHERE g.pid = p.project_id"
  	         . "\n AND g.gid = '".$v['group_id']."'"
  	         . $cond
  	         . "\n GROUP BY p.project_id";
  	         $database->setQuery($query);
  	         $project_list = $database->loadResultArray();

  	  if (is_array($project_list)) {            
  	     foreach ($project_list AS $k => $p)
  	     {
  	  	     $involved_projects .= $p."<br/>";
  	     }
  	  }   

  	  if (count($project_list) < 1) { $involved_projects = HL_NO_PROJECTS; }
  	  
  	  $involved_projects = "<a href='#' onmouseover=\"return overlib('<table>".$involved_projects."</table>', CAPTION, '".HL_PROJECTS."', BELOW, RIGHT);\" onmouseout=\"return nd();\">".$hydra->load('img', '16_projects.gif', "alt='".HL_PROJECTS."' title='".HL_PROJECTS."'")."</a>"; 
  	  
  	     
  	  if (!$protect->perm('new_usergroup')) {$edit = $hydra->load('img', '16_edit_2.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'"); }
  	  ?>
  	  <tr class="row<?php echo $k;?>">
  	    <td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
  	    <td align="center"><?php echo mosHTML::idBox( $i, $v['group_id'] ); ?></td>
  	    <td align="left" width="100%"><?php echo $v['group_name'];?></td>
  	    <td align="center"><?php echo $involved_projects;?></td>
  	    <td align="center"><?php echo $memberlist;?></td>
  	    <td align="center"><?php echo $edit;?></td>
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
echo $hydra_template->drawInput('hidden', 'cmd', 'show_usergroups');
echo $hydra_template->drawInput('hidden', 'boxchecked', '');
// fixed by Giller
echo $hydra_template->drawInput('hidden', 'limitstart', $limitstart);
?>
<script type="text/javascript" language="javascript">
function validateDelete()
{
	if (document.adminForm.boxchecked.value == '') {
	  alert ('<?php echo HL_GROUP_DELETE_WARN;?>');	
	}
	else {
	  document.adminForm.cmd.value = 'del_usergroup';	
	  document.adminForm.submit();	
	}
}
</script>
